<?php

namespace Framework\Exceptions;

class ValidationException extends \Exception
{
    /**
     * @var array<string, list<string>>
     */
    private array $errorBag;

    /**
     * @param array<string, list<string>> $errorBag
     */
    public function __construct(array $errorBag, string $message = 'Error validating data provided', int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->errorBag = $errorBag;
    }

    /**
     * @return array<string, list<string>>
     */
    public function errorBag(): array
    {
        return $this->errorBag;
    }
}
