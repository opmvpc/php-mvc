<?php

namespace Framework\Exceptions;

class ServerError extends \Exception implements ExceptionInterface
{
    public function render(): string
    {
        return <<<HTML
        <h1>Server Error</h1>
        <p>An internal server error occurred while processing your request.</p>
        <h2>Details</h2>
        <h3>Message</h3>
        <p>{$this->getMessage()}</p>
        <h3>Code</h3>
        <p>{$this->getCode()}</p>
        <h3>File</h3>
        <p>{$this->getFile()}:{$this->getLine()}</p>
        <h3>Trace</h3>
        <pre>{$this->getTraceAsString()}</pre>
        HTML;
    }
}
