<?php

use App\Http\Controllers\HomeController;
use Framework\Routing\Router;

return $registerRoutes = function (Router $router) {
    $router->get('/', [HomeController::class, 'index']);

    $router->get('/articles', fn () => print 'article');
};
