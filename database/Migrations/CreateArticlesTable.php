<?php

namespace Database\Migrations;

use Framework\Database\DB;
use Framework\Database\Migrations\AbstractMigration;

class CreateArticlesTable extends AbstractMigration
{
    public function createTable(): void
    {
        DB::query(
            <<<'SQL'
            CREATE TABLE IF NOT EXISTS articles (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            content TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
            SQL
        );
    }
}
