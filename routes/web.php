<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\ArticleController as AdminArticleController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\HomeController;
use Framework\Routing\Router;

return $registerWebRoutes = function (Router $router) {
    $router->get('/', [HomeController::class, 'index'])
        ->withName('home')
    ;

    $router->get('/articles', [ArticleController::class, 'index'])->withName('articles.index');
    $router->get('/articles/{articleId}', [ArticleController::class, 'show'])->withName('articles.show');

    // Admin routes
    $router->get('/admin/dashboard', [DashboardController::class, 'index'])->withName('admin.dashboard')->withMiddlewares(['auth']);

    $router->get('/admin/articles', [AdminArticleController::class, 'index'])->withName('admin.articles.index')->withMiddlewares(['auth']);
    $router->get('/admin/articles/create', [AdminArticleController::class, 'create'])->withName('admin.articles.create')->withMiddlewares(['auth']);
    $router->post('/admin/articles/store', [AdminArticleController::class, 'store'])->withName('admin.articles.store')->withMiddlewares(['auth']);
    $router->post('/admin/articles/{articleId}/destroy', [AdminArticleController::class, 'destroy'])->withName('admin.articles.destroy')->withMiddlewares(['auth']);
    $router->get('/admin/articles/{articleId}/show', [AdminArticleController::class, 'show'])->withName('admin.articles.show')->withMiddlewares(['auth']);
};
