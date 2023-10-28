<?php

namespace Framework\Routing;

use Framework\Exceptions\ServerError;
use Framework\Requests\Request;
use Framework\Requests\Response;
use Framework\Requests\ResponseInterface;
use Framework\View\View;

class Route
{
    protected string $path;

    protected HttpVerb $method;

    protected ?string $name;

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

    public function run(): ResponseInterface
    {
        $context = new Context($this, new Request());

        $res = null;
        if (\is_array($this->action)) {
            [$controllerName, $methodName] = $this->action;

            $res = (new $controllerName())->{$methodName}($context);
        } else {
            $res = ($this->action)($context);
        }

        if ($res instanceof ResponseInterface) {
            return $res;
        }

        if ($res instanceof View) {
            return new Response($res->__toString(), 200, ['Content-Type' => 'text/html']);
        }

        if (\is_string($res)) {
            return new Response($res);
        }

        $json = json_encode($res, JSON_PRETTY_PRINT);

        if (false !== $json) {
            return new Response($json, 200, ['Content-Type' => 'application/json']);
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
        // literal match
        if ($this->path === $path && $this->method === $method) {
            return true;
        }

        $paramNames = [];
        $requiredParams = [];

        $pattern = $this->normalizePath($this->path);

        /**
         * Remplace les paramètres de type {id} par des regex
         * - {id} => ([^/]+)
         * - {id?} => ([^/]*).
         */
        $pattern = \preg_replace_callback('/\{([^}]+)\}\//', function ($matches) use (&$paramNames, &$requiredParams) {
            $paramNames[] = \rtrim($matches[1], '?');

            if (str_ends_with($matches[1], '?')) {
                return '([^/]*)(?:/?)';
            }

            $requiredParams[] = $matches[1];

            return '([^/]+)/';
        }, $pattern);

        // si la route ne contient pas de paramètres, on ne fait pas de regex
        if (null !== $pattern && !str_contains($pattern, '+') && !str_contains($pattern, '*')) {
            return false;
        }

        // on ajoute les délimiteurs de regex
        \preg_match_all("#{$pattern}#", $this->normalizePath($path), $matches);

        $paramValues = [];
        // on récupère les valeurs des paramètres
        if (count($matches[1]) > 0) {
            $paramValues = \array_slice($matches, 1);
            $paramValues = \array_map(function ($value) {
                if (count($value) > 0) {
                    return $value[0];
                }

                return null;
            }, $paramValues);
            // replace empty string by null
            $paramValues = \array_map(fn ($value) => '' === $value ? null : $value, $paramValues);
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
