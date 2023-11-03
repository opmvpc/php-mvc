<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseController;
use Framework\Auth\Auth;
use Framework\Requests\Response;
use Framework\Routing\Context;
use Framework\Routing\Router;
use Framework\Validator\Rules\Email;
use Framework\Validator\Rules\Max;
use Framework\Validator\Rules\Min;
use Framework\Validator\Rules\Required;
use Framework\Validator\Validator;
use Framework\View\View;

class LoginController extends BaseController
{
    public function showLoginForm(): View
    {
        return new View('auth/login');
    }

    public function login(Context $context): Response
    {
        $validated = (new Validator([
            'email' => [
                new Required(),
                new Email(),
                new Min(3),
                new Max(100),
            ],
            'password' => [
                new Required(),
                new Max(50),
            ],
        ]))->validate($context->postParams());

        if (!is_string($validated['email']) || !is_string($validated['password'])) {
            throw new \Exception('Email and password must be strings');
        }

        Auth::login($validated['email'], $validated['password']);

        return Router::redirect('admin.dashboard');
    }

    public function logout(): Response
    {
        Auth::logout();

        return Router::redirect('home');
    }
}
