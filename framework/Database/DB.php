<?php

declare(strict_types=1);

namespace Framework\Database;

/**
 * Database connection singleton using PDO.
 */
class DB
{
    private \PDO $pdo;

    private static ?self $instance = null;

    private function __construct(DBConfig $dbConfig)
    {
        $this->pdo = new \PDO(
            $dbConfig->dsn(),
            $dbConfig->user(),
            $dbConfig->password(),
            [
            ]
        );
    }

    /**
     * Get an instance of this class or creates it if it doesn't exist.
     *
     * @return static
     */
    public static function get(null|DBConfig $dbConfig = null): self
    {
        if (null === static::$instance) {
            if (null === $dbConfig) {
                throw new \Exception('DBConfig is required');
            }

            static::$instance = new static($dbConfig);
        }

        return static::$instance;
    }

    /**
     * Prepares and executes a SQL query.
     */
    public function run(string $sql, array $prepareParams = []): \PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($prepareParams);

        return $stmt;
    }

    /**
     * Prepares and executes a SQL query.
     */
    public static function query(string $sql, array $prepareParams = []): \PDOStatement
    {
        $db = static::get();
        $stmt = $db->run($sql, $prepareParams);

        return $stmt;
    }

    /**
     * Get last inserted id.
     */
    public function lastInsertId(): int
    {
        return \intval($this->pdo->lastInsertId());
    }
}
