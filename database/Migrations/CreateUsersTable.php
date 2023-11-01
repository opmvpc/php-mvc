<?php

namespace Database\Migrations;

use Framework\Database\DB;
use Framework\Database\Migrations\AbstractMigration;

class CreateUsersTable extends AbstractMigration
{
    public function createTable(): void
    {
        DB::query(
            <<<'SQL'
            CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
            SQL
        );
    }
}
