<?php

namespace Framework\Middleware;

use Framework\Requests\MessageInterface;
use Framework\Routing\Context;

abstract class AbstractMiddlewaresManager
{
    /**
     * @var array<string, AbstractMiddleware>
     */
    protected array $routeMiddlewares;

    /**
     * @var array<string, AbstractMiddleware>
     */
    protected array $requestMiddlewares;

    public function __construct()
    {
        $this->routeMiddlewares = [];
        $this->requestMiddlewares = [];
    }

    abstract public function register(): void;

    /**
     * @param array<string> $middlewares
     */
    public function handle(Context $context, $middlewares): Context|MessageInterface
    {
        $this->register();

        $middlewares = \array_map(function (string $middleware): AbstractMiddleware {
            if (!isset($this->routeMiddlewares[$middleware])) {
                throw new \InvalidArgumentException("Middleware {$middleware} is not registered");
            }

            return $this->routeMiddlewares[$middleware];
        }, $middlewares);

        $middlewares = \array_merge($middlewares, $this->requestMiddlewares);

        foreach ($middlewares as $middleware) {
            $context = $middleware->handle($context);
            if ($context instanceof MessageInterface) {
                return $context;
            }
        }

        return $context;
    }
}
