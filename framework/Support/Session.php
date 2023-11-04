<?php

declare(strict_types=1);

namespace Framework\Support;

class Session
{
    private static string $id = '';

    public static function start(int $lifetime = 180): void
    {
        \session_cache_limiter('private');
        \session_cache_expire($lifetime);

        self::$id = Str::uuid();
        \session_start();

        if (self::has('_csrf_token')) {
            self::set('_old_csrf_token', self::get('_csrf_token'));
            self::delete('_csrf_token');
        }
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
        \session_destroy();
    }

    public static function flash(string $key, mixed $value): void
    {
        self::set($key, $value);
        $flash = self::get('_flash', []);

        if (!\is_array($flash)) {
            $flash = [];
        }

        if (!\array_key_exists(self::$id, $flash)) {
            $flash[self::$id] = [];
        }

        $flash[self::$id][$key] = true;
        self::set('_flash', $flash);
    }

    public static function has(string $key): bool
    {
        return \array_key_exists($key, $_SESSION);
    }

    public static function destroyFlash(): void
    {
        $flash = self::get('_flash', []);

        if (!\is_array($flash)) {
            $flash = [];
        }

        $oldFlash = \array_filter($flash, fn (string $key) => $key !== self::$id, ARRAY_FILTER_USE_KEY);
        foreach ($oldFlash as $sessions) {
            foreach (\array_keys($sessions) as $sessionKey) {
                self::delete(\strval($sessionKey));
            }
        }

        $flash = \array_filter($flash, fn (string $key) => $key === self::$id, ARRAY_FILTER_USE_KEY);
        static::set('_flash', $flash);
    }

    public static function id(): string
    {
        return self::$id;
    }
}
