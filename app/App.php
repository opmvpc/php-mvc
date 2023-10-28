<?php

namespace App;

use Framework\Framework;

class App extends Framework
{
    public function run(): void
    {
        $response = $this->router()->dispatch();

        $response->send();
    }

    public function registerRoutes(): void
    {
        // routes registering
        $registerRoutes = require_once __DIR__.'/routes.php';
        $registerRoutes($this->router());
    }
}
