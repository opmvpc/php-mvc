<?php

namespace Framework\Requests;

class Request extends Message implements RequestInterface
{
    private string $method;
    private string $uri;

    /**
     * @param array<string, string> $headers
     */
    public function __construct(string $body = '', string $method = 'GET', string $uri = '/', array $headers = [])
    {
        parent::__construct($body, $headers);
        $this->method = $method;
        $this->uri = $uri;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function withMethod(string $method): RequestInterface
    {
        $this->method = $method;

        return $this;
    }

    public function withUri(string $uri): RequestInterface
    {
        $this->uri = $uri;

        return $this;
    }
}
