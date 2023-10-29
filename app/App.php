<?php

declare(strict_types=1);

namespace App;

use Framework\Framework;

class App extends Framework
{
    public function registerRoutes(): void
    {
        // routes registering
        $registerRoutes = require_once __DIR__.'/routes.php';
        $registerRoutes($this->router());
    }
}
