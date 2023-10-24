<?php

namespace Framework\Requests;

abstract class Message implements MessageInterface
{
    /**
     * @var array<string, string>
     */
    protected array $headers = [];
    protected string $body = '';

    /**
     * @param array<string, string> $headers
     */
    public function __construct(string $body = '', array $headers = [])
    {
        $this->headers = $headers;
        $this->body = $body;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getHeader(string $name): string
    {
        return $this->headers[$name] ?? '';
    }

    public function hasHeader(string $name): bool
    {
        return isset($this->headers[$name]);
    }

    public function withHeader(string $name, string $value): MessageInterface
    {
        $this->headers[$name] = $value;

        return $this;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function withBody(string $body): MessageInterface
    {
        $this->body = $body;

        return $this;
    }
}
