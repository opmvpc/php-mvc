<?php

declare(strict_types=1);

namespace Framework\Database;

class DBConfig
{
    private string $host;
    private string $port;
    private string $database;
    private string $user;
    private string $password;
    private string $dsn;

    public function __construct(string $host, string $port, string $database, string $user, string $password)
    {
        $this->host = $host;
        $this->port = $port;
        $this->database = $database;
        $this->user = $user;
        $this->password = $password;

        $this->dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";
    }

    public function dsn(): string
    {
        return $this->dsn;
    }

    public function user(): string
    {
        return $this->user;
    }

    public function password(): string
    {
        return $this->password;
    }

    public function database(): string
    {
        return $this->database;
    }

    public function host(): string
    {
        return $this->host;
    }

    public function port(): string
    {
        return $this->port;
    }
}
