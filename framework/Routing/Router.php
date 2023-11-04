<?php

declare(strict_types=1);

namespace Framework\Routing;

use Framework\Exceptions\MethodNotAllowedException;
use Framework\Exceptions\NotFoundException;
use Framework\Requests\MessageInterface;
use Framework\Requests\Redirect;
use Framework\Requests\Request;
use Framework\Requests\Response;
use Framework\Requests\ResponseInterface;

class Router
{
    /**
     * @var array<Route>
     */
    protected array $routes;

    protected static null|Router $instance = null;

    protected ?Route $current;

    private function __construct()
    {
        $this->routes = [];
        $this->current = null;
    }

    public function __destruct()
    {
        self::$instance = null;
    }

    public static function getInstance(): Router
    {
        if (null === self::$instance) {
            self::$instance = new Router();
        }

        return self::$instance;
    }

    /**
     * Register a GET route.
     *
     * @param callable|list{0: class-string, 1: callable-string} $action
     */
    public function add(string $path, HttpVerb $method, mixed $action): Route
    {
        return $this->routes[] = Route::add($path, $method, $action);
    }

    /**
     * Register a GET route.
     *
     * @param callable|list{0: class-string, 1: callable-string} $action
     */
    public function get(string $path, mixed $action): Route
    {
        return $this->routes[] = Route::get($path, $action);
    }

    /**
     * Register a POST route.
     *
     * @param callable|list{0: class-string, 1: callable-string} $action
     */
    public function post(string $path, mixed $action): Route
    {
        return $this->routes[] = Route::post($path, $action);
    }

    /**
     * Register a PUT route.
     *
     * @param callable|list{0: class-string, 1: callable-string} $action
     */
    public function put(string $path, mixed $action): Route
    {
        return $this->routes[] = Route::put($path, $action);
    }

    /**
     * Register a DELETE route.
     *
     * @param callable|list{0: class-string, 1: callable-string} $action
     */
    public function delete(string $path, mixed $action): Route
    {
        return $this->routes[] = Route::delete($path, $action);
    }

    /**
     * Dispatch a request url to the right handler.
     */
    public function dispatch(string $uri = '/', HttpVerb $method = HttpVerb::GET): MessageInterface
    {
        $request = Request::fromGlobals($uri, $method);

        $matching = $this->match($request->getMethod(), $request->getUri());

        if ($matching) {
            $this->current = $matching;

            // if an error occurs, show it to the user
            return $matching->run($request);
        }

        if (in_array($request->getMethod(), HttpVerb::cases())) {
            throw new MethodNotAllowedException("Method {$request->getMethod()->value} not allowed for {$request->getUri()}");
        }

        // no matching route has been found
        return $this->dispatchNotFound();
    }

    public static function redirect(string $path): Response
    {
        // try with named routes first
        $router = self::getInstance();

        try {
            $path = $router->route($path);
        } catch (\Exception $e) {
            // do nothing
        }

        return new Redirect($path);
    }

    /**
     * @return array<string, Route>
     */
    public function routes(): array
    {
        return $this->routes;
    }

    /**
     * Generate an URL from a route name.
     *
     * @param array<string, string> $params
     * @param array<string, string> $getParams
     */
    public function route(string $name, array $params = [], array $getParams = []): string
    {
        foreach ($this->routes as $route) {
            if ($route->name() === $name) {
                $finds = [];
                $replaces = [];

                foreach ($params as $key => $value) {
                    $finds[] = "{{$key}}";
                    $replaces[] = $value;

                    $finds[] = "{{$key}?}";
                    $replaces[] = $value;
                }

                $path = str_replace($finds, $replaces, $route->path());

                // remove optional params
                $path = preg_replace('/\{([^}]+)\}\//', '', $path);

                // throw an exception if there are still params
                if (null === $path || str_contains($path, '{')) {
                    throw new \Exception("Route {$name} has missing params");
                }

                // add get params
                if (!empty($getParams)) {
                    $path .= '?'.http_build_query($getParams);
                }

                return $path;
            }
        }

        throw new \Exception("Route {$name} not found");
    }

    /**
     * Match an URL from registered routes.
     */
    public function match(HttpVerb $requestMethod, string $requestPath): ?Route
    {
        foreach ($this->routes as $route) {
            if ($route->matches($requestMethod, $requestPath)) {
                return $route;
            }
        }

        return null;
    }

    /**
     * Dispatch a Not Found Error (code 404).
     */
    private function dispatchNotFound(): ResponseInterface
    {
        return Response::fromException(new NotFoundException(), 404);
    }
}
