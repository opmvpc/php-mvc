<?php

namespace Framework\Routing;

use Framework\Exceptions\NotFoundException;
use Framework\Exceptions\ServerError;
use Framework\Requests\Response;
use Framework\Requests\ResponseInterface;

class Router
{
    /**
     * @var array<string, Route>
     */
    protected array $paths;

    protected ?Route $current;

    public function __construct()
    {
        $this->paths = [];
        $this->current = null;
    }

    /**
     * Register a GET route.
     *
     * @param callable|list{0: class-string, 1: callable-string} $action
     */
    public function add(string $path, HttpVerb $method, mixed $action): void
    {
        $this->paths[$path] = Route::add($path, $method, $action);
    }

    /**
     * Register a GET route.
     *
     * @param callable|list{0: class-string, 1: callable-string} $action
     */
    public function get(string $path, mixed $action): void
    {
        $this->paths[$path] = Route::get($path, $action);
    }

    /**
     * Register a POST route.
     *
     * @param callable|list{0: class-string, 1: callable-string} $action
     */
    public function post(string $path, mixed $action): void
    {
        $this->paths[$path] = Route::post($path, $action);
    }

    /**
     * Register a PUT route.
     *
     * @param callable|list{0: class-string, 1: callable-string} $action
     */
    public function put(string $path, mixed $action): void
    {
        $this->paths[$path] = Route::put($path, $action);
    }

    /**
     * Register a DELETE route.
     *
     * @param callable|list{0: class-string, 1: callable-string} $action
     */
    public function delete(string $path, mixed $action): void
    {
        $this->paths[$path] = Route::delete($path, $action);
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
            // if an error occurs, show it to the user
            try {
                $response = $matching->run();
            } catch (\Throwable $th) {
                $response = $this->dispatchError($th);
            }
        } else {
            // no matching route has been found
            $response = $this->dispatchNotFound();
        }

        return $response;
    }

    public function redirect(string $path): void
    {
        \header('Location: '.$path, true, 301);

        exit;
    }

    /**
     * @return array<string, Route>
     */
    public function paths(): array
    {
        return $this->paths;
    }

    /**
     * Match an URL from registered routes
     * Exact match implementation.
     */
    private function match(HttpVerb $requestMethod, string $requestPath): ?Route
    {
        if (!\array_key_exists($requestPath, $this->paths)) {
            return null;
        }

        $route = $this->paths[$requestPath];

        if ($route->method() !== $requestMethod) {
            throw new \Exception('Method not allowed');
        }

        return $route;
    }

    /**
     * Dispatch a Server Error (code 500)
     * and show related error message.
     */
    private function dispatchError(\Throwable $th): ResponseInterface
    {
        return Response::fromException(new ServerError($th->getMessage(), 500, $th));
    }

    /**
     * Dispatch a Not Found Error (code 404).
     */
    private function dispatchNotFound(): ResponseInterface
    {
        return Response::fromException(new NotFoundException(), 404);
    }
}
