<?php

declare(strict_types=1);

namespace Framework\Requests;

use App\App;

class Redirect extends Response
{
    public function __construct(string $uri = '/')
    {
        parent::__construct(
            '',
            302,
            [
                'Location' => $uri,
            ]
        );
    }

    public function back(): MessageInterface
    {
        return $this->withHeader('Location', $_SERVER['HTTP_REFERER']);
    }

    /**
     * @param array<string, string> $params
     */
    public function route(string $name, array $params = []): string
    {
        return App::get()->router()->route($name, $params);
    }
}
