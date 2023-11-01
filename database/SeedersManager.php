<?php

namespace Database;

use Database\Seeders\ArticlesSeeder;
use Database\Seeders\UsersSeeder;
use Framework\Database\Seeders\AbstractSeedersManager;

class SeedersManager extends AbstractSeedersManager
{
    public function register(): void
    {
        $this->seeders = [
            new UsersSeeder(),
            new ArticlesSeeder(),
        ];
    }
}
