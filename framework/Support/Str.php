<?php

namespace Framework\Support;

use App\App;

class Str
{
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
        $lang = App::get()->config('app.lang');
        if (!\is_string($lang)) {
            throw new \Exception('Invalid lang config');
        }

        $basePath = App::get()->basePath();

        // explode only once
        [$file, $key] = explode('.', $key, 2);
        $filePath = "{$basePath}/resources/lang/{$lang}/{$file}.php";

        if (!file_exists($filePath) && 'en' !== $lang) {
            throw new \Exception("Translation file {$file} not found (lang: {$lang})");
        }

        $translations = include $filePath;

        return $translations[$key] ?? $key;
    }
}
