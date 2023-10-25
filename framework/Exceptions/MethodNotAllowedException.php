<?php

namespace Framework\Exceptions;

class MethodNotAllowedException extends \Exception implements ExceptionInterface
{
    public function render(): string
    {
        return <<<'HTML'
        <h1>Method Not Allowed</h1>
        <p>The requested URL does not support the specified HTTP method.</p>
        HTML;
    }
}
