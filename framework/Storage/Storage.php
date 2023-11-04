<?php

declare(strict_types=1);

namespace Framework\Storage;

use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;

class Storage
{
    protected string $path;

    protected FileSystem $fileSystem;

    protected static ?Storage $instance = null;

    private function __construct(string $path)
    {
        $this->path = $path;

        $adapter = new LocalFilesystemAdapter(
            $path,
        );

        $this->fileSystem = new Filesystem(
            $adapter,
            ['public_url' => '/storage/']
        );
    }

    public static function init(string $path): Storage
    {
        self::$instance = new Storage($path);

        return self::$instance;
    }

    /**
     * get a file from storage.
     */
    public static function get(string $path): File
    {
        $storage = self::getInstance();

        if (!$storage->fileSystem->fileExists($path)) {
            throw new \Exception('File not found');
        }

        return $storage->createFileObject($path);
    }

    public static function exists(string $path): bool
    {
        $storage = self::getInstance();

        return $storage->fileSystem->has($path);
    }

    /**
     * @param array<string, string> $config
     */
    public static function put(string $path, string $content, array $config = []): void
    {
        $storage = self::getInstance();

        $storage->fileSystem->write($path, $content, $config);
    }

    public static function delete(string $path): void
    {
        $storage = self::getInstance();

        $storage->fileSystem->delete($path);
    }

    public static function url(string $path): string
    {
        $storage = self::getInstance();

        return $storage->fileSystem->publicUrl($path);
    }

    public static function copy(string $from, string $to): void
    {
        $storage = self::getInstance();

        $storage->fileSystem->copy($from, $to);
    }

    public static function move(string $from, string $to): void
    {
        $storage = self::getInstance();

        $storage->fileSystem->move($from, $to);
    }

    /**
     * @return File[]
     */
    public static function files(string $path = '/'): array
    {
        $storage = self::getInstance();

        return $storage->fileSystem->listContents($path, false)->filter(
            fn ($file) => $file->isFile()
        )
            ->sortByPath()
            ->map(
                fn ($file) => $storage->createFileObject($file->path())
            )->toArray()
        ;
    }

    /**
     * @return string[]
     */
    public static function directories(string $path = '/'): array
    {
        $storage = self::getInstance();

        return $storage->fileSystem->listContents($path, true)->filter(
            fn ($file) => $file->isDir()
        )->sortByPath()
            ->map(
                fn ($file) => $file->path()
                )
            ->toArray()
        ;
    }

    /**
     * @param array<string, string> $config
     */
    public static function createDirectory(string $path, array $config = []): void
    {
        $storage = self::getInstance();

        $storage->fileSystem->createDirectory($path, $config);
    }

    public static function deleteDirectory(string $path): void
    {
        $storage = self::getInstance();

        $storage->fileSystem->deleteDirectory($path);
    }

    /**
     * Create symlink from private directory to public directory.
     */
    public static function link(string $privateDir = '/public', string $publicDir = '/public'): void
    {
        $storage = self::getInstance();

        if (!file_exists($privateDir)) {
            throw new \Exception('private directory not found');
        }

        if (!file_exists($publicDir)) {
            throw new \Exception('public directory not found');
        }

        symlink(
            $storage->path.$privateDir,
            $_SERVER['DOCUMENT_ROOT'].$publicDir
        );
    }

    private static function getInstance(): Storage
    {
        if (is_null(self::$instance)) {
            throw new \Exception('Storage not initialized');
        }

        return self::$instance;
    }

    private function createFileObject(string $path): File
    {
        $contents = $this->fileSystem->read($path);
        $mimeType = $this->fileSystem->mimeType($path);
        $size = $this->fileSystem->fileSize($path);
        $lastModified = $this->fileSystem->lastModified($path);
        $visibility = $this->fileSystem->visibility($path);

        return new File(
            $path,
            $contents,
            $mimeType,
            $size,
            $lastModified,
            $visibility
        );
    }
}
