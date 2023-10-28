<?php

declare(strict_types=1);

namespace Framework\Requests;

use Framework\Exceptions\RenderableException;

interface ResponseInterface extends MessageInterface
{
    public function getStatusCode(): int;

    public function getReasonPhrase(): string;

    public function withStatus(int $code, string $reasonPhrase = ''): ResponseInterface;

    public static function fromException(RenderableException $e, int $code = 500): ResponseInterface;
}
