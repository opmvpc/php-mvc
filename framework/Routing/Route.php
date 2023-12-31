<?php

declare(strict_types=1);

namespace Framework\Routing;

use App\Http\Middleware\MiddlewaresManager;
use Framework\Exceptions\ServerError;
use Framework\Exceptions\ValidationException;
use Framework\Requests\JsonResponse;
use Framework\Requests\MessageInterface;
use Framework\Requests\Redirect;
use Framework\Requests\RequestInterface;
use Framework\Requests\Response;
use Framework\Requests\ResponseInterface;
use Framework\Requests\ViewResponse;
use Framework\Support\Session;
use Framework\View\View;

class Route
{
    protected string $path;

    protected HttpVerb $method;

    protected ?string $name;

    /**
     * @var array<string>
     */
    protected array $middlewares;

    /**
     * @var callable|list{0: class-string, 1: callable-string}
     */
    protected mixed $action;

    /**
     * @var array<string, null|string>
     */
    protected array $params;

    /**
     * @param callable|list{0: class-string, 1: callable-string} $action
     */
    private function __construct(string $path, HttpVerb $method, array|callable $action)
    {
        $this->path = $path;
        $this->method = $method;
        $this->action = $action;
        $this->params = [];
        $this->name = null;
        $this->middlewares = [];
    }

    /**
     * Create a new route.
     *
     * @param callable|list{0: class-string, 1: callable-string} $action
     */
    public static function add(string $path, HttpVerb $method, array|callable $action): self
    {
        return new Route($path, $method, $action);
    }

    /**
     * Create a new GET route.
     *
     * @param callable|list{0: class-string, 1: callable-string} $action
     */
    public static function get(string $path, array|callable $action): self
    {
        return new Route($path, HttpVerb::GET, $action);
    }

    /**
     * Create a new POST route.
     *
     * @param callable|list{0: class-string, 1: callable-string} $action
     */
    public static function post(string $path, array|callable $action): self
    {
        return new Route($path, HttpVerb::POST, $action);
    }

    /**
     * Create a new PUT route.
     *
     * @param callable|list{0: class-string, 1: callable-string} $action
     */
    public static function put(string $path, array|callable $action): self
    {
        return new Route($path, HttpVerb::PUT, $action);
    }

    /**
     * Create a new DELETE route.
     *
     * @param callable|list{0: class-string, 1: callable-string} $action
     */
    public static function delete(string $path, array|callable $action): self
    {
        return new Route($path, HttpVerb::DELETE, $action);
    }

    public function run(RequestInterface $request): MessageInterface
    {
        $context = new Context($this, $request);
        $routeMiddlewares = $this->middlewares;
        $middlewareManager = new MiddlewaresManager();
        $context = $middlewareManager->handle($context, $routeMiddlewares);

        if ($context instanceof MessageInterface) {
            return $context;
        }

        return $this->getResponse($context);
    }

    public function getResponse(Context $context): MessageInterface
    {
        $res = null;

        try {
            if (\is_array($this->action)) {
                [$controllerName, $methodName] = $this->action;

                $res = (new $controllerName())->{$methodName}($context);
            } else {
                $res = ($this->action)($context);
            }
        } catch (ValidationException $exception) {
            // check if the request is an ajax request
            if ($context->request()->isJson()) {
                return new JsonResponse($exception->errorBag(), 422);
            }

            // if not, redirect to the previous page with errors in session
            Session::flash('_errors', $exception->errorBag());
            Session::flash('_old_inputs', $exception->input());

            return (new Redirect())->back();
        }

        if ($res instanceof ResponseInterface) {
            return $res;
        }

        if ($res instanceof View) {
            return new ViewResponse($res);
        }

        if (\is_string($res)) {
            return new Response($res);
        }

        $json = json_encode($res, JSON_PRETTY_PRINT);

        if (false !== $json) {
            return new JsonResponse($res);
        }

        throw new ServerError('Unable to encode response');
    }

    public function path(): string
    {
        return $this->path;
    }

    /**
     * @return callable|list{0: class-string, 1: callable-string} $action
     */
    public function action(): array|callable
    {
        return $this->action;
    }

    public function method(): HttpVerb
    {
        return $this->method;
    }

    /**
     * @return array<string, null|string>
     */
    public function params(): array
    {
        return $this->params;
    }

    public function matches(HttpVerb $method, string $path): bool
    {
        if ($this->path === $path) {
            if ($this->method === $method) {
                // Chemin et méthode correspondent parfaitement
                return true;
            }

            // Chemin correspond mais pas la méthode
            return false;
        }

        $paramNames = [];
        $requiredParams = [];

        $pattern = $this->normalizePath($this->path);

        /**
         * Remplace les paramètres de type {id} par des regex
         * - {id} => ([^/]+)
         * - {id?} => ([^/]*).
         */
        $pattern = \preg_replace_callback('/\{([^}]+)\}\//', function (array $matches) use (&$paramNames, &$requiredParams) {
            $paramNames[] = \rtrim($matches[1], '?');

            if (str_ends_with($matches[1], '?')) {
                return '([^/]*)(?:/?)';
            }

            $requiredParams[] = $matches[1];

            return '([^/]+)/';
        }, $pattern);

        // Ajouter des ancres de début et de fin pour s'assurer que la regex correspond à tout le chemin
        $pattern = "^{$pattern}$"; // Modifiez cette ligne

        // si la route ne contient pas de paramètres, on ne fait pas de regex
        if (null !== $pattern && !str_contains($pattern, '+') && !str_contains($pattern, '*')) {
            return false;
        }

        \preg_match_all("#{$pattern}#", $this->normalizePath($path), $matches);

        // si la regex à matché, on check si la méthode est la bonne
        if (count($matches[0]) > 0 && $this->method !== $method) {
            return false;
        }

        $paramValues = [];
        // on récupère les valeurs des paramètres
        if (count($matches[1]) > 0) {
            $paramValues = \array_slice($matches, 1);
            $paramValues = \array_map(function (array $value) {
                if (count($value) > 0) {
                    return $value[0];
                }

                return null;
            }, $paramValues);
            // replace empty string by null
            $paramValues = \array_map(fn (string $value) => '' === $value ? null : $value, $paramValues);
        }
        if (count($paramValues) > 0) {
            // on ajoute les paramètres à la route
            $emptyParamValues = \array_fill(0, count($paramNames), null);
            $paramValues += $emptyParamValues;
            $this->params = \array_combine($paramNames, $paramValues);

            // si un paramètre requis est null, on envoie une erreur
            $missingParams = [];
            foreach ($this->params as $paramName => $paramValue) {
                if (null === $paramValue && \in_array($paramName, $requiredParams)) {
                    $missingParams[] = $paramName;
                }
            }

            return true;
        }

        return false;
    }

    public function name(): ?string
    {
        return $this->name;
    }

    public function withName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param array<string> $middlewares
     */
    public function withMiddlewares(array $middlewares): self
    {
        $this->middlewares = $middlewares;

        return $this;
    }

    /**
     * Normalisation du chemin de la route.
     *
     * Exemples:
     * - '' => '/'
     * - 'articles' => '/articles/'
     * - 'articles/{id}' => '/articles/{id}/'
     */
    protected function normalizePath(string $path): string
    {
        $path = \trim($path, '/');
        $path = "/{$path}/";

        return \preg_replace('/[\/]{2,}/', '/', $path) ?? '/';
    }
}
