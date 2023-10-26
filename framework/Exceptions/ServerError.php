<?php

namespace Framework\Exceptions;

class ServerError extends \Exception implements ExceptionInterface
{
    public function render(): string
    {
        $code = $this->getCode();
        $message = $this->getPrevious()?->getMessage() ?? $this->getMessage();
        $file = $this->getPrevious()?->getFile() ?? $this->getFile();
        $line = $this->getPrevious()?->getLine() ?? $this->getLine();
        $trace = $this->getPrevious()?->getTraceAsString() ?? $this->getTraceAsString();

        return <<<HTML
        <h1>Server Error</h1>
        <p>An internal server error occurred while processing your request.</p>
        <h2>Details</h2>
        <h3>Message</h3>
        <p>{$message}</p>
        <h3>Code</h3>
        <p>{$code}</p>
        <h3>File</h3>
        <p>{$file}:{$line}</p>
        <h3>Trace</h3>
        <pre>{$trace}</pre>
        HTML;
    }
}
