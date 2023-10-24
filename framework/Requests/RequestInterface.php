<?php

namespace Framework\Requests;

interface RequestInterface extends MessageInterface
{
    public function getMethod(): string;

    public function getUri(): string;

    public function withMethod(string $method): RequestInterface;

    public function withUri(string $uri): RequestInterface;
}
