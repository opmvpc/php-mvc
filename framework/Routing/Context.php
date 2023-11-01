<?php

declare(strict_types=1);

namespace Framework\Routing;

use Framework\Requests\RequestInterface;

class Context
{
    private Route $route;
    private RequestInterface $request;

    /**
     * @var array<string, string>
     */
    private array $queryParams;

    /**
     * @var array<string, mixed>
     */
    private array $jsonParams;

    /**
     * @var array<string, mixed>
     */
    private array $postParams;

    public function __construct(Route $route, RequestInterface $request)
    {
        $this->route = $route;
        $this->request = $request;
        $this->queryParams = $this->getQueryParams();
        $this->jsonParams = $this->getJsonParams();
        $this->postParams = $this->getPostParams();
    }

    public function route(): Route
    {
        return $this->route;
    }

    public function request(): RequestInterface
    {
        return $this->request;
    }

    public function queryParams(null|string $key = null): mixed
    {
        if (null === $key) {
            return $this->queryParams;
        }

        return $this->queryParams[$key] ?? throw new \Exception('Query param not found');
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonParams(): array
    {
        return $this->jsonParams;
    }

    public function postParams(null|string $key = null): mixed
    {
        if (null === $key) {
            return $this->postParams;
        }

        return $this->postParams[$key] ?? throw new \Exception('Post param not found');
    }

    public function routeParams(null|string $key = null): mixed
    {
        if (null === $key) {
            return $this->route->params();
        }

        return $this->route->params()[$key] ?? throw new \Exception('Route param not found');
    }

    /**
     * @return array<string, string>
     */
    private function getQueryParams(): array
    {
        return $_GET;
    }

    /**
     * @return array<string, mixed>
     */
    private function getJsonParams(): array
    {
        $params = json_decode($this->request->getBody(), true);

        if (false === $params || null === $params) {
            return [];
        }

        if (!\is_array($params)) {
            throw new \Exception('Invalid JSON body');
        }

        return $params;
    }

    /**
     * @return array<string, mixed>
     */
    private function getPostParams(): array
    {
        return $_POST;
    }
}
