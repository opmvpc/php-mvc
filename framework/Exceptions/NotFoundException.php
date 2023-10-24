<?php

namespace Framework\Exceptions;

class NotFoundException extends \Exception implements ExceptionInterface
{
    public function render(): string
    {
        return <<<'HTML'
        <h1>Not Found</h1>
        <p>The requested URL was not found on this server.</p>
        HTML;
    }
}
