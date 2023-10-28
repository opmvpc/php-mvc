<?php

namespace Framework\Requests;

use Framework\View\View;

class ViewResponse extends Response
{
    public function __construct(View $view, int $statusCode = 200)
    {
        parent::__construct(
            $view->__toString(),
            $statusCode,
            [
                'Content-Type' => 'text/html',
            ]
        );
    }
}
