<?php

namespace Framework\Requests;

use Framework\Exceptions\ExceptionInterface;

interface ResponseInterface extends MessageInterface
{
    public function getStatusCode(): int;

    public function getReasonPhrase(): string;

    public function withStatus(int $code, string $reasonPhrase = ''): ResponseInterface;

    public function send(): void;

    public static function fromException(ExceptionInterface $e, int $code = 500): ResponseInterface;
}
