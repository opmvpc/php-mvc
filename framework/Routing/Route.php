<?php

namespace Framework\Routing;

use Framework\Exceptions\ServerError;
use Framework\Requests\Response;
use Framework\Requests\ResponseInterface;

class Route
{
    protected string $path;

    protected HttpVerb $method;

    /**
     * @var callable|list{0: class-string, 1: callable-string}
     */
    protected mixed $action;

    /**
     * @param callable|list{0: class-string, 1: callable-string} $action
     */
    private function __construct(string $path, HttpVerb $method, array|callable $action)
    {
        $this->path = $path;
        $this->method = $method;
        $this->action = $action;
    }

    /**
     * Register a new GET route with the router.
     *
     * @param callable|list{0: class-string, 1: callable-string} $action
     */
    public static function get(string $path, array|callable $action): self
    {
        return new Route($path, HttpVerb::GET, $action);
    }

    /**
     * Register a new POST route with the router.
     *
     * @param callable|list{0: class-string, 1: callable-string} $action
     */
    public static function post(string $path, array|callable $action): self
    {
        return new Route($path, HttpVerb::POST, $action);
    }

    /**
     * Register a new PUT route with the router.
     *
     * @param callable|list{0: class-string, 1: callable-string} $action
     */
    public static function put(string $path, array|callable $action): self
    {
        return new Route($path, HttpVerb::PUT, $action);
    }

    /**
     * Register a new DELETE route with the router.
     *
     * @param callable|list{0: class-string, 1: callable-string} $action
     */
    public static function delete(string $path, array|callable $action): self
    {
        return new Route($path, HttpVerb::DELETE, $action);
    }

    public function run(): ResponseInterface
    {
        $res = null;
        if (\is_array($this->action)) {
            [$controllerName, $methodName] = $this->action;

            $res = (new $controllerName())->{$methodName}();
        } else {
            $res = ($this->action)();
        }

        if ($res instanceof ResponseInterface) {
            return $res;
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
}
