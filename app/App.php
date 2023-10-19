<?php

namespace App;

use Framework\Routing\Router;

class App
{
    public static function run(): void
    {
        $router = new Router();

        // routes registering
        $registerRoutes = require_once __DIR__.'/routes.php';
        $registerRoutes($router);

        $router->dispatch();
    }
}
