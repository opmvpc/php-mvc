<?php

declare(strict_types=1);

namespace Framework\Routing;

use Framework\Support\Session;

class Csrf
{
    protected null|string $token;

    protected static null|Csrf $instance = null;

    private function __construct()
    {
        $this->token = \bin2hex(random_bytes(32));
        Session::set('_csrf_token', $this->token);
    }

    public static function token(): string
    {
        if (\is_null(self::$instance)) {
            self::$instance = new Csrf();
        }

        return self::$instance->getToken();
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public static function validate(mixed $token): bool
    {
        if (!\is_string($token)) {
            throw new \Exception('CSRF token must be a string');
        }

        $sessionToken = Session::get('_old_csrf_token', '');

        if (!is_string($sessionToken)) {
            throw new \Exception('CSRF token not found in session');
        }

        if (false === \hash_equals($sessionToken, $token)) {
            throw new \Exception('CSRF token mismatch');
        }

        return true;
    }
}
