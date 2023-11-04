<?php

namespace App\Http\Middleware;

use Framework\Middleware\AbstractMiddlewaresManager;
use Framework\Middleware\Authenticated;
use Framework\Middleware\CsrfValidation;

class MiddlewaresManager extends AbstractMiddlewaresManager
{
    public function register(): void
    {
        // Middlewares that will be applied to specific routes
        $this->routeMiddlewares = [
            'auth' => new Authenticated(),
        ];

        // Middlewares that will be applied to all routes
        $this->requestMiddlewares = [
            'csrf' => new CsrfValidation(),
        ];
    }
}
