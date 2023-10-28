<?php

namespace Framework\Requests;

use Framework\Routing\HttpVerb;

interface RequestInterface extends MessageInterface
{
    public function getMethod(): HttpVerb;

    public function getUri(): string;

    public function withMethod(HttpVerb $method): RequestInterface;

    public function withUri(string $uri): RequestInterface;

    public function isJson(): bool;

    public static function fromGlobals(): RequestInterface;
}
