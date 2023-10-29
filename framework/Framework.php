<?php

declare(strict_types=1);

namespace Framework;

use Dotenv\Dotenv;
use Framework\Routing\Router;
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

    private Router $router;

    final private function __construct() {}

    protected function setup(): void
    {
        $this->registerClassAliases();
        $this->setBasePath();
        $this->loadConfig();

        if ('production' !== $this->config('app.env') && $this->config('app.debug')) {
            $this->setupWhoops();
        }
    }

    abstract public function registerRoutes(): void;

    public function router(): Router
    {
        if (!isset($this->router)) {
            $this->router = new Router();
            $this->registerRoutes();
        }

        return $this->router;
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

    public function run(): void
    {
        $response = $this->router()->dispatch();

        $response->send();
    }

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

    private function loadConfig(): void
    {
        $dotenv = Dotenv::createImmutable($this->basePath);
        $dotenv->load();

        $this->config = [
            'app' => [
                'name' => $_ENV['APP_NAME'] ?? 'APP PHP MVC Framework',
                'env' => $_ENV['APP_ENV'] ?? 'production',
                'debug' => $_ENV['APP_DEBUG'] ?? false,
                'lang' => 'fr',
            ],
            'db' => [
                'host' => $_ENV['DB_HOST'] ?? 'localhost',
                'port' => $_ENV['DB_PORT'] ?? '3306',
                'user' => $_ENV['DB_USER'] ?? 'root',
                'password' => $_ENV['DB_PASSWORD'] ?? '',
                'database' => $_ENV['DB_DATABASE'] ?? 'php-mvc-framework',
            ],
        ];
    }

    private function setBasePath(): void
    {
        $basePath = \realpath(__DIR__.'/../');

        if (false === $basePath) {
            throw new \Exception('Base path not found');
        }

        $this->basePath = $basePath;
    }

    private function registerClassAliases(): void
    {
        spl_autoload_register(function (string $class) {
            if ('Str' === $class) {
                class_alias('Framework\Support\Str', 'Str');
            }

            if ('Session' === $class) {
                class_alias('Framework\Support\Session', 'Session');
            }
        });
    }
}
