<?php

namespace Framework\Auth;

use Framework\Exceptions\ValidationException;
use Framework\Routing\Context;
use Framework\Support\Session;
use Framework\Support\Str;

class Auth
{
    public static function login(string $email, string $password): bool
    {
        try {
            $user = Authenticatable::findByEmail($email);
        } catch (\Exception $exception) {
            throw new ValidationException(
                [
                    'email' => [Str::translate('validation.wrong_email')],
                ],
                [
                    'email' => $email,
                ]
            );
        }

        if (!password_verify($password, $user->password())) {
            throw new ValidationException(
                [
                    'password' => [Str::translate('validation.wrong_password')],
                ],
                [
                    'email' => $email,
                ]
            );
        }

        $user->setPassword('');

        Session::set('_user', $user);

        return true;
    }

    public static function register(string $name, string $email, string $password, string $password_confirmation, Context $context): bool
    {
        if ($password !== $password_confirmation) {
            throw new ValidationException(
                [
                    'password_confirmation' => [Str::translate('validation.password_confirmation')],
                ],
                $context->postParams()
            );
        }

        $user = new Authenticatable(
            id: null,
            name: $name,
            email: $email,
            password: password_hash($password, PASSWORD_DEFAULT),
        );

        Session::set('_user', $user);

        $user->save();

        return true;
    }

    public static function logout(): void
    {
        Session::delete('_user');
    }

    public static function user(): ?Authenticatable
    {
        $user = Session::get('_user');

        if ($user instanceof Authenticatable) {
            return $user;
        }

        throw new \Exception('No authenticated user');
    }

    public static function check(): bool
    {
        return null !== self::user();
    }

    public static function guest(): bool
    {
        return !self::check();
    }

    public static function id(): ?int
    {
        $user = self::user();

        if (null === $user) {
            return null;
        }

        return $user->id();
    }
}
