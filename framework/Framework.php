<?php

namespace Framework;

use Dotenv\Dotenv;
use Whoops\Handler\JsonResponseHandler;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;
use Whoops\Util\Misc;

abstract class Framework
{
    /**
     * @var array<string, array<string, mixed>>
     */
    protected array $config;

    private static Framework $instance;

    private string $basePath;

    final private function __construct() {}

    protected function setup(): void
    {
        $this->setBasePath();

        $dotenv = Dotenv::createImmutable($this->basePath);
        $dotenv->load();

        $this->config = [
            'app' => [
                'name' => $_ENV['APP_NAME'] ?? 'APP PHP MVC Framework',
                'env' => $_ENV['APP_ENV'] ?? 'production',
                'debug' => $_ENV['APP_DEBUG'] ?? false,
            ],
            'db' => [
                'host' => $_ENV['DB_HOST'] ?? 'localhost',
                'port' => $_ENV['DB_PORT'] ?? '3306',
                'user' => $_ENV['DB_USER'] ?? 'root',
                'password' => $_ENV['DB_PASSWORD'] ?? '',
                'database' => $_ENV['DB_DATABASE'] ?? 'php-mvc-framework',
            ],
        ];

        if ('production' !== $this->config('app.env') && $this->config('app.debug')) {
            $this->setupWhoops();
        }
    }

    public static function get(): Framework
    {
        if (!isset(self::$instance)) {
            $instance = self::$instance = new static();
            $instance->setup();
        }

        return self::$instance;
    }

    public function config(string $key): mixed
    {
        $keys = explode('.', $key);
        $config = $this->config;

        foreach ($keys as $key) {
            if (\is_array($config)) {
                if (!array_key_exists($key, $config)) {
                    throw new \Exception("Key {$key} not found in config");
                }
                $config = $config[$key];
            }
        }

        return $config;
    }

    abstract public function run(): void;

    public function basePath(): string
    {
        return $this->basePath;
    }

    private function setupWhoops(): void
    {
        $run = new Run();

        if (Misc::isAjaxRequest()) {
            $jsonHandler = new JsonResponseHandler();
            $jsonHandler->setJsonApi(true);
            $run->pushHandler($jsonHandler);
        } else {
            $prettyPageHandler = new PrettyPageHandler();
            $prettyPageHandler->setPageTitle('Whoops! There was a problem.');
            $run->pushHandler($prettyPageHandler);
        }

        $run->register();
    }

    private function setBasePath(): void
    {
        $basePath = \realpath(__DIR__.'/../');

        if (false === $basePath) {
            throw new \Exception('Base path not found');
        }

        $this->basePath = $basePath;
    }
}
