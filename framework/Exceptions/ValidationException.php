<?php

declare(strict_types=1);

namespace Framework\Exceptions;

class ValidationException extends \Exception
{
    /**
     * @var array<string, list<string>>
     */
    private array $errorBag;

    /**
     * @var array<string, mixed>
     */
    private array $input;

    /**
     * @param array<string, list<string>> $errorBag
     * @param array<string, mixed>        $input
     */
    public function __construct(array $errorBag, array $input, string $message = 'Error validating data provided', int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->errorBag = $errorBag;
        $this->input = $input;
    }

    /**
     * @return array<string, list<string>>
     */
    public function errorBag(): array
    {
        return $this->errorBag;
    }

    /**
     * @return array<string, mixed>
     */
    public function input(): array
    {
        return $this->input;
    }
}
