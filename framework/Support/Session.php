<?php

declare(strict_types=1);

namespace Framework\Support;

class Session
{
    private static string $id;

    public static function start(): void
    {
        static::$id = Str::uuid();
        session_start();
    }

    public static function stop(): void
    {
        static::destroyFlash();
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function delete(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function destroy(): void
    {
        session_destroy();
    }

    public static function flash(string $key, mixed $value): void
    {
        static::set($key, $value);
        $flash = static::get('_flash', []);
        $flash[static::$id][$key] = true;
        static::set('_flash', $flash);
    }

    public static function has(string $key): bool
    {
        return array_key_exists($key, $_SESSION);
    }

    public static function destroyFlash(): void
    {
        $flash = static::get('_flash', []);

        $oldFlash = array_filter($flash, fn ($key) => $key !== static::$id, ARRAY_FILTER_USE_KEY);
        foreach ($oldFlash as $sessions) {
            foreach (\array_keys($sessions) as $sessionKey) {
                static::delete($sessionKey);
            }
        }

        $flash = array_filter($flash, fn ($key) => $key === static::$id, ARRAY_FILTER_USE_KEY);
        static::set('_flash', $flash);
    }
}
