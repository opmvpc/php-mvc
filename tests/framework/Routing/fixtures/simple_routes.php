<?php

use Framework\Routing\Router;

require_once __DIR__.'/HomeController.php';

return $registerRoutes = function (Router $router) {
    $router->get('/', [HomeController::class, 'index']);

    $router->get('/articles', fn () => 'article');

    $router->get('/articles/{id?}', fn () => 'article x');

    $router->get('/error', fn () => throw new Exception('Error'));

    $router->post('/post', fn () => 'post');
};
