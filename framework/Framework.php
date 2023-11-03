<?php

declare(strict_types=1);

namespace Framework;

use Framework\Config\Config;
use Framework\Routing\Router;
use Framework\Support\Session;
use Whoops\Handler\JsonResponseHandler;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;
use Whoops\Util\Misc;

abstract class Framework
{
    protected Config $config;

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
            $lifetime = $this->config('session.lifetime');
            if (!\is_int($lifetime)) {
                throw new \Exception('Session lifetime must be an integer');
            }
            Session::start($lifetime);
        }

        if ('production' !== $this->config('app.env') && $this->config('app.debug')) {
            $this->setupWhoops();
        }
    }

    abstract public function registerRoutes(): void;

    public function router(): Router
    {
        if (!isset($this->router)) {
            $this->router = Router::getInstance();
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
        return $this->config->get($key);
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
        $dbConfig = $this->config->getDatabaseConfig();

        Database\DB::init($dbConfig);
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
        $this->config = new Config($this->basePath);
    }

    private function setBasePath(): void
    {
        $separator = \DIRECTORY_SEPARATOR;
        $basePath = \realpath(__DIR__.$separator.'..'.$separator);

        if (false === $basePath) {
            throw new \Exception('Base path not found');
        }

        $this->basePath = $basePath;
    }
}
