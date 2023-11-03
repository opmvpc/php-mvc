<?php

declare(strict_types=1);

namespace Framework\Requests;

class JsonResponse extends Response
{
    public function __construct(mixed $data, int $statusCode = 200)
    {
        $json = json_encode($data, JSON_PRETTY_PRINT);
        if (false === $json) {
            throw new \Exception('Json encode error');
        }

        parent::__construct(
            $json,
            $statusCode,
            [
                'Content-Type' => 'application/json',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Access-Control-Allow-Origin' => 'SAMEORIGIN',
                'Access-Control-Allow-Methods' => 'GET, POST, OPTIONS',
            ]
        );
    }
}
