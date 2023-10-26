<?php

namespace App;

use Framework\Framework;
use Framework\Routing\Router;

class App extends Framework
{
    public function run(): void
    {
        $router = new Router();

        // routes registering
        $registerRoutes = require_once __DIR__.'/routes.php';
        $registerRoutes($router);

        $hasError = false;
        $response = null;

        $response = $router->dispatch();

        $response->send();
    }
}
