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
use Framework\Validator\Rules\Unique;
use Framework\Validator\Validator;
use Framework\View\View;

class RegisterController extends BaseController
{
    public function showRegisterForm(): View
    {
        return new View('auth/register');
    }

    public function register(Context $context): Response
    {
        $validated = (new Validator([
            'name' => [
                new Required(),
                new Min(3),
                new Max(100),
            ],
            'email' => [
                new Required(),
                new Email(),
                new Unique('users'),
                new Min(3),
                new Max(100),
            ],
            'password' => [
                new Required(),
                new Min(8),
                new Max(50),
            ],
            'password_confirmation' => [
                new Required(),
                new Min(8),
                new Max(50),
            ],
        ]))->validate($context->postParams());

        if (!\is_string($validated['name']) || !\is_string($validated['email']) || !\is_string($validated['password']) || !\is_string($validated['password_confirmation'])) {
            throw new \Exception('User name, email and password must be strings');
        }

        Auth::register(
            name: $validated['name'],
            email: $validated['email'],
            password: $validated['password'],
            password_confirmation: $validated['password_confirmation'],
            context: $context,
        );

        return Router::redirect('admin.dashboard');
    }
}
