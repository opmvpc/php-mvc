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
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            ]
        );
    }

    /**
     * Get an instance of this class or creates it if it doesn't exist.
     */
    public static function get(null|DBConfig $dbConfig = null): DB
    {
        if (null === self::$instance) {
            if (null === $dbConfig) {
                throw new \Exception('DBConfig is required');
            }

            self::$instance = new DB($dbConfig);
        }

        return self::$instance;
    }

    /**
     * Prepares and executes a SQL query.
     *
     * @param array<string, mixed> $prepareParams
     */
    public function run(string $sql, array $prepareParams = []): \PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($prepareParams);

        return $stmt;
    }

    /**
     * Prepares and executes a SQL query.
     *
     * @param array<string, mixed> $prepareParams
     */
    public static function query(string $sql, array $prepareParams = []): \PDOStatement
    {
        $db = self::get();

        return $db->run($sql, $prepareParams);
    }

    /**
     * Get last inserted id.
     */
    public function lastInsertId(): int
    {
        return \intval($this->pdo->lastInsertId());
    }
}
