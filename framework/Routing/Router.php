<?php

namespace Framework\Routing;

use Framework\Exceptions\MethodNotAllowedException;
use Framework\Exceptions\NotFoundException;
use Framework\Requests\Response;
use Framework\Requests\ResponseInterface;

class Router
{
    /**
     * @var array<string, Route>
     */
    protected array $routes;

    protected ?Route $current;

    public function __construct()
    {
        $this->routes = [];
        $this->current = null;
    }

    /**
     * Register a GET route.
     *
     * @param callable|list{0: class-string, 1: callable-string} $action
     */
    public function add(string $path, HttpVerb $method, mixed $action): Route
    {
        return $this->routes[$path] = Route::add($path, $method, $action);
    }

    /**
     * Register a GET route.
     *
     * @param callable|list{0: class-string, 1: callable-string} $action
     */
    public function get(string $path, mixed $action): Route
    {
        return $this->routes[$path] = Route::get($path, $action);
    }

    /**
     * Register a POST route.
     *
     * @param callable|list{0: class-string, 1: callable-string} $action
     */
    public function post(string $path, mixed $action): Route
    {
        return $this->routes[$path] = Route::post($path, $action);
    }

    /**
     * Register a PUT route.
     *
     * @param callable|list{0: class-string, 1: callable-string} $action
     */
    public function put(string $path, mixed $action): Route
    {
        return $this->routes[$path] = Route::put($path, $action);
    }

    /**
     * Register a DELETE route.
     *
     * @param callable|list{0: class-string, 1: callable-string} $action
     */
    public function delete(string $path, mixed $action): Route
    {
        return $this->routes[$path] = Route::delete($path, $action);
    }

    /**
     * Dispatch a request url to the right handler.
     */
    public function dispatch(string $uri = '/', HttpVerb $method = HttpVerb::GET): ResponseInterface
    {
        if (isset($_SERVER['REQUEST_METHOD'])) {
            $requestMethod = HttpVerb::from($_SERVER['REQUEST_METHOD']);
        } else {
            $requestMethod = $method;
        }

        $requestPath = $_SERVER['REQUEST_URI'] ?? $uri;

        $matching = $this->match($requestMethod, $requestPath);

        if ($matching) {
            $this->current = $matching;

            // if an error occurs, show it to the user
            $response = $matching->run();

            return $response;
        }

        if (\in_array($requestPath, \array_keys($this->routes))) {
            throw new MethodNotAllowedException();
        }

        // no matching route has been found
        return $this->dispatchNotFound();
    }

    public static function redirect(string $path): Response
    {
        return new Response('', 302, ['Location' => $path]);
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
     */
    public function route(string $name, array $params = []): string
    {
        foreach ($this->routes as $route) {
            if ($route->name() === $name) {
                $finds = [];
                $replaces = [];

                foreach ($params as $key => $value) {
                    $finds[] = "{{$value}}";
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
