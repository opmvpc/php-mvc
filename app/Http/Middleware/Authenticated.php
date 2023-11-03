<?php

namespace App\Http\Middleware;

use Framework\Auth\Auth;
use Framework\Middleware\AbstractMiddleware;
use Framework\Requests\MessageInterface;
use Framework\Routing\Context;
use Framework\Routing\Router;

class Authenticated extends AbstractMiddleware
{
    public function handle(Context $context): Context|MessageInterface
    {
        if (!Auth::check()) {
            return Router::redirect('auth.login.show');
        }

        return $context;
    }
}
