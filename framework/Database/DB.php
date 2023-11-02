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
    public static function get(): DB
    {
        if (null === self::$instance) {
            throw new \Exception('Database connection not initialized, call DB::init() first');
        }

        return self::$instance;
    }

    /**
     * Initialize the database connection.
     */
    public static function init(DBConfig $dbConfig): void
    {
        self::$instance = new DB($dbConfig);
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
    public static function lastInsertId(): int
    {
        $db = self::get();

        return \intval($db->pdo->lastInsertId());
    }
}
