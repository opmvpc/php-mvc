<?php

namespace Framework\Exceptions;

interface RenderableException extends \Throwable
{
    public function render(): string;
}
