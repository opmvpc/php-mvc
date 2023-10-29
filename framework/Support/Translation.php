<?php

namespace Framework\Support;

use App\App;

class Translation
{
    /**
     * @var array<string, array<string, string>>
     */
    private static null|array $translations = null;

    private function __construct() {}

    public static function translate(string $key): string
    {
        if (null === self::$translations) {
            static::addTranslations();
        }

        // explode only once
        $explodedKey = explode('.', $key, 2);
        $fileName = null;
        if (2 === \count($explodedKey)) {
            [$fileName, $key] = $explodedKey;
        }

        if (null === $fileName) {
            // search in all files
            foreach (static::$translations as $translations) {
                if (\array_key_exists($key, $translations)) {
                    return $translations[$key];
                }
            }

            return $key;
        }

        return static::$translations[$fileName][$key] ?? $key;
    }

    private static function addTranslations(): void
    {
        $basePath = App::get()->basePath();
        $lang = App::get()->config('app.lang');

        if (!\is_string($lang)) {
            throw new \Exception('Invalid lang config');
        }

        // get all files from resources/lang
        $files = scandir("{$basePath}/resources/lang/{$lang}");

        foreach ($files as $file) {
            if ('.' === $file || '..' === $file) {
                continue;
            }

            $translations = include "{$basePath}/resources/lang/{$lang}/{$file}";

            if (!\is_array($translations)) {
                throw new \Exception('Invalid translation file');
            }

            $fileName = explode('.', $file)[0];

            static::$translations[$fileName] = $translations;
        }
    }
}
