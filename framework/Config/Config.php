<?php

namespace Framework\Config;

use Dotenv\Dotenv;
use Framework\Database\DBConfig;

class Config
{
    private string $basePath;

    /**
     * @var array<string, mixed>
     */
    private array $config;

    public function __construct(string $basePath)
    {
        $this->basePath = $basePath;
        $this->loadConfig();
    }

    /**
     * Get config value by key.
     *
     * @example $config->get('app.env')
     */
    public function get(string $key): mixed
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

    /**
     * Get an instance of DBConfig.
     */
    public function getDatabaseConfig(): DBConfig
    {
        $host = $this->get('database.host') ?? 'localhost';
        $port = $this->get('database.port') ?? '3306';
        $user = $this->get('database.user') ?? 'root';
        $password = $this->get('database.password') ?? '';
        $database = $this->get('database.database') ?? 'microvel';

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

        return new DBConfig(
            $host,
            $port,
            $database,
            $user,
            $password,
        );
    }

    private function loadConfig(): void
    {
        $dotenv = Dotenv::createImmutable($this->basePath);
        $dotenv->load();

        $this->config = [
            'app' => require $this->basePath.'/config/app.php',
            'database' => require $this->basePath.'/config/database.php',
            'session' => require $this->basePath.'/config/session.php',
            'storage' => require $this->basePath.'/config/storage.php',
        ];
    }
}
