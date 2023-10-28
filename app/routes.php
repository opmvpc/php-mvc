<?php

declare(strict_types=1);

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\HomeController;
use Framework\Routing\Router;

return $registerRoutes = function (Router $router) {
    $router->get('/', [HomeController::class, 'index'])
        ->withName('home')
    ;

    $router->get('/articles', [ArticleController::class, 'index'])->withName('articles.index');
    $router->get('/articles/create', [ArticleController::class, 'create'])->withName('articles.create');
    $router->post('/articles/store', [ArticleController::class, 'store'])->withName('articles.store');
    $router->get('/articles/{articleId}', [ArticleController::class, 'show'])->withName('articles.show');

    $router->get('/error', fn () => throw new Exception('Error'));
};
