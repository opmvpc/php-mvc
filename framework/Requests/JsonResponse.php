<?php

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
            ]
        );
    }
}
