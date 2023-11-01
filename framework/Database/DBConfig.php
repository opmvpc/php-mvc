<?php

declare(strict_types=1);

namespace Framework\Database;

class DBConfig
{
    private string $host;
    private string $name;
    private string $user;
    private string $password;
    private string $dsn;

    public function __construct(string $host, string $name, string $user, string $password)
    {
        $this->host = $host;
        $this->name = $name;
        $this->user = $user;
        $this->password = $password;

        $this->dsn = 'mysql:dbname='.$this->name.';host='.$this->host;
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

    public function name(): string
    {
        return $this->name;
    }

    public function host(): string
    {
        return $this->host;
    }
}
