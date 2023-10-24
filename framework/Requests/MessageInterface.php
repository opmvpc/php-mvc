<?php

namespace Framework\Requests;

interface MessageInterface
{
    /**
     * @return array<string, string>
     */
    public function getHeaders(): array;

    public function getBody(): string;

    public function hasHeader(string $name): bool;

    public function getHeader(string $name): string;

    public function withHeader(string $name, string $value): MessageInterface;

    public function withBody(string $body): MessageInterface;
}
