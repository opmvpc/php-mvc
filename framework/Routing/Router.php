<?php

namespace Framework\Routing;

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
    public function dispatch(): void
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'] ?? HttpVerb::GET;
        $requestPath = $_SERVER['REQUEST_URI'] ?? '/';

        $matching = $this->match($requestMethod, $requestPath);
        if ($matching) {
            // if an error occurs, show it to the user
            try {
                $matching->run();
            } catch (\Throwable $th) {
                $this->dispatchError($th);
            }
        } else {
            // no matching route has been found
            $this->dispatchNotFound();
        }
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
    private function dispatchError(\Throwable $th): void
    {
        \http_response_code(500);
        echo 'Server Error: '.$th->getMessage();
    }

    /**
     * Dispatch a Not Found Error (code 404).
     */
    private function dispatchNotFound(): void
    {
        \http_response_code(404);
        echo '404 Error: Not found';
    }
}
