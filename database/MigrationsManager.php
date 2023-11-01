<?php

namespace Database;

use Database\Migrations\CreateArticlesTable;
use Database\Migrations\CreateUsersTable;
use Framework\Database\Migrations\AbstractMigrationsManager;

class MigrationsManager extends AbstractMigrationsManager
{
    public function register(): void
    {
        $this->migrations = [
            new CreateUsersTable(),
            new CreateArticlesTable(),
        ];
    }
}
