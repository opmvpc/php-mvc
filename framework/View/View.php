<?php

namespace Framework\View;

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

    protected string $baseViewPath = 'resources/views';

    /**
     * @param array<string, mixed> $data
     */
    public function __construct(string $path, array $data = [])
    {
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

    public function withBaseViewPath(string $path): View
    {
        $this->baseViewPath = $path;

        return $this;
    }

    public function getFullPath(string $path): string
    {
        $realPath = __DIR__.'/../../'.$this->baseViewPath.'/'.$path.'.php';
        $realPath = \realpath($realPath);

        if (false === $realPath) {
            throw new \Exception('View not found with path: '.$path);
        }

        return $realPath;
    }
}
