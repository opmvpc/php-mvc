<?php

declare(strict_types=1);

namespace App\Models;

use Framework\Database\DB;
use Framework\Database\Repository\AbstractModel;

class Article extends AbstractModel
{
    public function __construct(
        private null|int $id,
        private string $title,
        private string $content,
    ) {}

    public function id(): null|int
    {
        return $this->id;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function content(): string
    {
        return $this->content;
    }

    public static function findOrFail(mixed $id): Article
    {
        if (null === $id) {
            throw new \Exception('Article id is required');
        }

        if (\is_string($id)) {
            $id = (int) $id;
        }

        if (!\is_int($id)) {
            throw new \Exception('Article id must be an integer');
        }

        $article = DB::query(
            <<<'SQL'
            SELECT id, title, content
            FROM articles
            WHERE id = :id
            SQL,
            ['id' => $id]
        )->fetch();

        if (null !== $article) {
            if (!\is_array($article)) {
                throw new \Exception('Article must be an array');
            }

            return self::fromRow($article);
        }

        throw new \Exception('Article not found');
    }

    /**
     * @return array<Article>
     */
    public static function all(): array
    {
        $articles = DB::query(
            <<<'SQL'
            SELECT id, title, content
            FROM articles
            SQL
        )->fetchAll();

        return \array_map(
            fn (array $article): Article => self::fromRow($article),
            $articles
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
        ];
    }

    public function save(): Article
    {
        if (null === $this->id) {
            $this->id = $this->insert();
        } else {
            $this->update();
        }

        return $this;
    }

    public function delete(): void
    {
        if (null === $this->id) {
            throw new \Exception('Article id is required');
        }

        DB::query(
            <<<'SQL'
            DELETE FROM articles
            WHERE id = :id
            SQL,
            ['id' => $this->id]
        );
    }

    /**
     * @param array<string, mixed> $row
     */
    private static function fromRow(array $row): Article
    {
        if (!\is_int($row['id'])) {
            throw new \Exception('Article id must be an integer');
        }

        if (!\is_string($row['title'])) {
            throw new \Exception('Article title must be a string');
        }

        if (!\is_string($row['content'])) {
            throw new \Exception('Article content must be a string');
        }

        return new Article(
            $row['id'],
            $row['title'],
            $row['content'],
        );
    }

    private function insert(): int
    {
        DB::query(
            <<<'SQL'
            INSERT INTO articles (title, content)
            VALUES (:title, :content)
            SQL,
            [
                'title' => $this->title,
                'content' => $this->content,
            ]
        );

        return DB::lastInsertId();
    }

    private function update(): void
    {
        DB::query(
            <<<'SQL'
            UPDATE articles
            SET title = :title, content = :content
            WHERE id = :id
            SQL,
            [
                'id' => $this->id,
                'title' => $this->title,
                'content' => $this->content,
            ]
        );
    }
}
