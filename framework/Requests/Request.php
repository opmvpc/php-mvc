<?php

declare(strict_types=1);

namespace Framework\Requests;

use Framework\Routing\HttpVerb;

class Request extends Message implements RequestInterface
{
    private HttpVerb $method;
    private string $uri;

    /**
     * @param array<string, string> $headers
     */
    public function __construct(string $body = '', HttpVerb $method = HttpVerb::GET, string $uri = '/', array $headers = [])
    {
        parent::__construct($body, $headers);
        $this->method = $method;
        $this->uri = $uri;
    }

    public function getMethod(): HttpVerb
    {
        return $this->method;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function withMethod(HttpVerb $method): RequestInterface
    {
        $this->method = $method;

        return $this;
    }

    public function withUri(string $uri): RequestInterface
    {
        $this->uri = $uri;

        return $this;
    }

    public static function fromGlobals(
        string $uri = '/',
        HttpVerb $method = HttpVerb::GET,
    ): RequestInterface {
        if (isset($_SERVER['REQUEST_METHOD'])) {
            $requestMethod = HttpVerb::from($_SERVER['REQUEST_METHOD']);
        } else {
            $requestMethod = $method;
        }

        $requestPath = $_SERVER['REQUEST_URI'] ?? $uri;
        // remove query string
        $requestPath = explode('?', $requestPath)[0];

        if (function_exists('getallheaders')) {
            $headers = \getallheaders();
        } else {
            $headers = [];
        }
        $body = \file_get_contents('php://input');

        if (false === $body) {
            $body = '';
        }

        return new Request($body, $requestMethod, $requestPath, $headers);
    }

    public function isJson(): bool
    {
        return 'application/json' === $this->getHeader('Content-Type');
    }
}
