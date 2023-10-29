<?php

declare(strict_types=1);

namespace Framework\Support;

use Ramsey\Uuid\Uuid;

class Str
{
    public static function __(string $key): string
    {
        return Translation::translate($key);
    }

    public static function slug(string $string): string
    {
        return strtolower(
            preg_replace(
                '/[^a-zA-Z0-9]/',
                '-',
                trim($string)
            ) ?? ''
        );
    }

    public static function translate(string $key): string
    {
        return Translation::translate($key);
    }

    public static function escape(string $string): string
    {
        return \htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    public static function uuid(): string
    {
        return Uuid::uuid4()->toString();
    }
}
