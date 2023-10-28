<?php

declare(strict_types=1);

namespace App\Models;

class Article
{
    public function __construct(
        private int $id,
        private string $title,
        private string $content,
    ) {}

    public function id(): int
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

    public static function findOrFail(mixed $id): ?Article
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

        foreach (static::articles() as $article) {
            if ($article->id() === $id) {
                return $article;
            }
        }

        throw new \Exception('Article not found');
    }

    /**
     * @return array<Article>
     */
    public static function all(): array
    {
        return static::articles();
    }

    /**
     * @return array<Article>
     */
    public static function articles(): array
    {
        return [
            new Article(
                1,
                'Article 1',
                'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed vitae l
                ectus nisl. Nulla facilisi. Nullam euismod, nisl eget aliquam aliquet,
                nunc nisl aliquet nunc, vitae aliquam nisl nunc vitae nisl. Nulla facil
                isi. Nullam euismod, nisl eget aliquam aliquet, nunc nisl aliquet nunc,
                vitae aliquam nisl nunc vitae nisl.'
            ),
            new Article(
                2,
                'Article 2',
                'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed vitae l
                ectus nisl. Nulla facilisi. Nullam euismod, nisl eget aliquam aliquet,
                nunc nisl aliquet nunc, vitae aliquam nisl nunc vitae nisl. Nulla facil
                isi. Nullam euismod, nisl eget aliquam aliquet, nunc nisl aliquet nunc,
                vitae aliquam nisl nunc vitae nisl.'
            ),
        ];
    }
}
