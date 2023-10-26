<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\HomeController;
use Framework\Routing\Router;

return $registerRoutes = function (Router $router) {
    $router->get('/', [HomeController::class, 'index']);

    $router->get('/articles', [ArticleController::class, 'index']);

    $router->get('/articles/{articleId}', [ArticleController::class, 'show']);

    $router->get('/error', fn () => throw new Exception('Error'));
};
