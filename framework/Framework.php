<?php

declare(strict_types=1);

namespace Framework;

use Dotenv\Dotenv;
use Framework\Routing\Router;
use Framework\Support\Session;
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
        $this->setBasePath();
        $this->loadConfig();

        // Start session if we are not in CLI mode
        if ('cli' !== \php_sapi_name()) {
            Session::start();
        }

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

        Session::stop();
    }

    public function basePath(): string
    {
        return $this->basePath;
    }

    public function setupDB(): void
    {
        $host = $this->config('db.host') ?? 'localhost';
        $port = $this->config('db.port') ?? '3306';
        $user = $this->config('db.user') ?? 'root';
        $password = $this->config('db.password') ?? '';
        $database = $this->config('db.database') ?? 'microvel';

        if (!\is_string($host)) {
            throw new \Exception('DB host is not a string');
        }
        if (!\is_string($port)) {
            throw new \Exception('DB port is not an string');
        }
        if (!\is_string($user)) {
            throw new \Exception('DB user is not a string');
        }
        if (!\is_string($password)) {
            throw new \Exception('DB password is not a string');
        }
        if (!\is_string($database)) {
            throw new \Exception('DB database is not a string');
        }

        $dbConfig = new Database\DBConfig(
            $host,
            $port,
            $database,
            $user,
            $password,
        );

        Database\DB::get($dbConfig);
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
}
