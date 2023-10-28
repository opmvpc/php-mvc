<?php

declare(strict_types=1);

namespace Framework\View;

use App\App;
use Framework\View\Engine\EngineInterface;
use Framework\View\Engine\PhpEngine;

class View
{
    protected EngineInterface $engine;
    protected string $path;

    /**
     * @var array<string, mixed>
     */
    protected array $data;

    protected string $baseViewPath;

    /**
     * @param array<string, mixed> $data
     */
    public function __construct(string $path, array $data = [], string $baseViewPath = 'resources/views')
    {
        $this->baseViewPath = $baseViewPath;
        $this->engine = new PhpEngine();
        $this->path = $this->getFullPath($path);
        $this->data = $data;
    }

    public function __toString(): string
    {
        return $this->engine->render($this);
    }

    /**
     * @return array<string, mixed>
     */
    public function data(): array
    {
        return $this->data;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function getFullPath(string $path): string
    {
        $realPath = App::get()->basePath().'/'.$this->baseViewPath.'/'.$path.'.php';
        $realPath = \realpath($realPath);

        if (false === $realPath) {
            throw new \Exception('View not found with path: '.$path);
        }

        return $realPath;
    }

    public function baseViewPath(): string
    {
        return $this->baseViewPath;
    }
}
