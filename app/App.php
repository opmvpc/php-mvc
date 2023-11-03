<?php

declare(strict_types=1);

namespace App;

use Framework\Framework;

class App extends Framework
{
    public function registerRoutes(): void
    {
        $registerWebRoutes = require_once $this->basePath().'/routes/web.php';
        $registerWebRoutes($this->router());

        $registerAuthRoutes = require_once $this->basePath().'/routes/auth.php';
        $registerAuthRoutes($this->router());
    }

    public function run(): void
    {
        $this->setupDB();
        parent::run();
    }
}
