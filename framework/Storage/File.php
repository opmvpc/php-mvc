<?php

declare(strict_types=1);

namespace Framework\Storage;

class File
{
    private string $path;
    private string $contents;
    private string $mimeType;
    private int $size;
    private \DateTime $lastModified;
    private string $visibility;

    public function __construct(
        string $path,
        string $contents,
        string $mimeType,
        int $size,
        int $lastModified,
        string $visibility
    ) {
        $this->path = $path;
        $this->contents = $contents;
        $this->mimeType = $mimeType;
        $this->size = $size;
        $this->lastModified = new \DateTime('@'.$lastModified);
        $this->visibility = $visibility;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function contents(): string
    {
        return $this->contents;
    }

    public function mimeType(): string
    {
        return $this->mimeType;
    }

    public function size(): int
    {
        return $this->size;
    }

    public function lastModified(): \DateTime
    {
        return $this->lastModified;
    }

    public function visibility(): string
    {
        return $this->visibility;
    }
}
