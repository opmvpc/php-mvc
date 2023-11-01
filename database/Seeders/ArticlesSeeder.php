<?php

namespace Database\Seeders;

use Framework\Database\DB;
use Framework\Database\Seeders\AbstractSeeder;

class ArticlesSeeder extends AbstractSeeder
{
    public function run(): void
    {
        $articles = [
            [
                'title' => 'Article 1',
                'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla euismod, nisl eget aliquam ultricies, nunc nunc aliquet nunc, vitae aliquam nisl nunc vitae nisl.',
            ],

            [
                'title' => 'Article 2',
                'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla euismod, nisl eget aliquam ultricies, nunc nunc aliquet nunc, vitae aliquam nisl nunc vitae nisl.',
            ],

            [
                'title' => 'Article 3',
                'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla euismod, nisl eget aliquam ultricies, nunc nunc aliquet nunc, vitae aliquam nisl nunc vitae nisl.',
            ],
        ];

        DB::query(
            <<<'SQL'
                INSERT INTO articles (title, content)
                VALUES (:title, :content)
                SQL,
            $articles[0]
        );

        DB::query(
            <<<'SQL'
                INSERT INTO articles (title, content)
                VALUES (:title, :content)
                SQL,
            $articles[1]
        );

        DB::query(
            <<<'SQL'
                INSERT INTO articles (title, content)
                VALUES (:title, :content)
                SQL,
            $articles[2]
        );
    }
}
