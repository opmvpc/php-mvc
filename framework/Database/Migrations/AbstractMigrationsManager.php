<?php

namespace Framework\Database\Migrations;

use Framework\Database\DB;

abstract class AbstractMigrationsManager
{
    /**
     * @var array<AbstractMigration>
     */
    protected array $migrations = [];

    /**
     * Register migrations.
     */
    abstract public function register(): void;

    public function migrate(): void
    {
        $this->register();

        foreach ($this->migrations as $migration) {
            $migration->createTable();
        }
    }

    public function fresh(): void
    {
        // Get the list of all tables.
        $tables = DB::query('SHOW TABLES')->fetchAll(\PDO::FETCH_COLUMN);

        // Drop each table.
        foreach ($tables as $table) {
            DB::query("DROP TABLE {$table}");
        }

        // Run the migrations.
        $this->migrate();
    }
}
