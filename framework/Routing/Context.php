<?php

namespace Framework\Routing;

use Framework\Requests\Request;

class Context
{
    private Route $route;
    private Request $request;

    public function __construct(Route $route, Request $request)
    {
        $this->route = $route;
        $this->request = $request;
    }

    public function route(): Route
    {
        return $this->route;
    }

    public function request(): Request
    {
        return $this->request;
    }
}
