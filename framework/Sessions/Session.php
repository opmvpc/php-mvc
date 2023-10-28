<?php

namespace Framework\Sessions;

class Session
{
    public static function get(string $key, mixed $default = null): mixed
    {
        if (!array_key_exists($key, $_SESSION)) {
            throw new \Exception("Session key {$key} not found");
        }

        return $_SESSION[$key] ?? $default;
    }

    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function delete(string $key): void
    {
        if (!array_key_exists($key, $_SESSION)) {
            throw new \Exception("Session key {$key} not found");
        }

        unset($_SESSION[$key]);
    }

    public static function destroy(): void
    {
        session_destroy();
    }
}
