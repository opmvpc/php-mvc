<?php

namespace Framework\Requests;

use App\App;
use Framework\Exceptions\ExceptionInterface;

class Response extends Message implements ResponseInterface
{
    private int $statusCode;
    private string $reasonPhrase;

    /**
     * @param array<string, string> $headers
     */
    public function __construct(string $body = '', int $statusCode = 200, array $headers = [], string $reasonPhrase = 'OK')
    {
        parent::__construct($body, $headers);
        $this->statusCode = $statusCode;
        $this->reasonPhrase = $reasonPhrase;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getReasonPhrase(): string
    {
        return $this->reasonPhrase;
    }

    public function withStatus(int $code, string $reasonPhrase = ''): ResponseInterface
    {
        $clone = clone $this;
        $clone->statusCode = $code;
        $clone->reasonPhrase = $reasonPhrase;

        return $clone;
    }

    public function send(): void
    {
        http_response_code($this->statusCode);

        foreach ($this->headers as $name => $values) {
            header(sprintf('%s: %s', $name, $values), false);
        }

        echo $this->body;
    }

    public static function fromException(ExceptionInterface $e, int $code = 500): ResponseInterface
    {
        $response = (new Response())
            ->withStatus($code, 'Internal Server Error')
            ->withHeader('Content-Type', 'text/html')
        ;

        if ('development' === App::get()->config('app.env')) {
            $response = $response->withBody($e->render());
        } else {
            $response = $response->withBody('<h1>Internal Server Error</h1>');
        }

        //  Ignoring this PHPStan error
        // Method Framework\Requests\Response::fromException() should return Framework\Requests\ResponseInterface but returns Framework\Requests\MessageInterface.

        // @phpstan-ignore-next-line
        return $response;
    }
}
