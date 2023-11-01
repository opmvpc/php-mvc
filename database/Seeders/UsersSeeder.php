<?php

namespace Database\Seeders;

use Framework\Database\DB;
use Framework\Database\Seeders\AbstractSeeder;

class UsersSeeder extends AbstractSeeder
{
    public function run(): void
    {
        $user = [
            'name' => 'John Doe',
            'email' => 'example@test.com',
            'password' => password_hash('secret', PASSWORD_DEFAULT),
        ];

        DB::query(
            <<<'SQL'
            INSERT INTO users (name, email, password)
            VALUES (:name, :email, :password)
            SQL,
            $user
        );
    }
}
