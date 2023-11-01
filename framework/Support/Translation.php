<?php

namespace Framework\Support;

use App\App;

class Translation
{
    /**
     * @var null|array<string, array<string, string>>
     */
    private static null|array $translations = null;

    private function __construct() {}

    public static function translate(string $key): string
    {
        if (null === self::$translations) {
            self::addTranslations();
        }

        // explode only once
        $explodedKey = explode('.', $key, 2);
        $fileName = null;
        if (2 === \count($explodedKey)) {
            [$fileName, $key] = $explodedKey;
        }

        if (null === $fileName) {
            $translationsFiles = self::$translations;
            if (null === $translationsFiles) {
                throw new \Exception('Invalid translations');
            }
            // search in all files
            foreach ($translationsFiles as $translations) {
                if (\array_key_exists($key, $translations)) {
                    return $translations[$key];
                }
            }

            return $key;
        }

        return self::$translations[$fileName][$key] ?? $key;
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

        if (false === $files) {
            throw new \Exception('Invalid lang directory');
        }

        foreach ($files as $file) {
            if ('.' === $file || '..' === $file) {
                continue;
            }

            $translations = include "{$basePath}/resources/lang/{$lang}/{$file}";

            if (!\is_array($translations)) {
                throw new \Exception('Invalid translation file');
            }

            $fileName = explode('.', $file)[0];

            self::$translations[$fileName] = $translations;
        }
    }
}
