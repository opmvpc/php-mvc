<?php

use App\Http\Controllers\HomeController;
use Framework\Routing\Context;
use Framework\Routing\Router;

return $registerRoutes = function (Router $router) {
    $router->get('/', [HomeController::class, 'index']);

    $router->get('/articles', fn () => 'article');

    $router->get('/error', fn () => throw new Exception('Error'));

    $router->get('/articles/{id}', fn (Context $context) => $context->route()->params()['id']);
};
