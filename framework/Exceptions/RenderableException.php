<?php

declare(strict_types=1);

namespace Framework\Exceptions;

interface RenderableException extends \Throwable
{
    public function render(): string;
}
