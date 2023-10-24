<?php

namespace Framework\Exceptions;

interface ExceptionInterface extends \Throwable
{
    public function render(): string;
}
