<?php

declare(strict_types=1);

namespace Framework\Routing;

use Framework\Support\Session;

class Csrf
{
    public static function token(): string
    {
        $token = bin2hex(random_bytes(32));
        Session::set('csrf_token', $token);

        return $token;
    }

    public static function validate(string $token): bool
    {
        if (false === \hash_equals(Session::get('csrf_token', ''), $token)) {
            throw new \Exception('CSRF token mismatch');
        }

        return true;
    }
}
