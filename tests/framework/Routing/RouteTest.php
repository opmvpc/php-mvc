<?php

use Framework\Routing\Router;

describe('Route generation', function () {
    beforeEach(function () {
        $this->router = Router::getInstance();
    });

    afterEach(function () {
        $this->router->__destruct();
        unset($this->router);
    });

    it('should generate a route', function (string $uri, string $name, Closure $action) {
        $this->router->get($uri, $action)->withName($name);
        $routeUri = $this->router->route($name);

        expect($routeUri)->toBe($uri);
    })->with([
        [
            '/', 'home', fn () => 'coucou',
        ],
    ]);

    it('should generate a route with parameters', function (string $uri, string $name, Closure $action) {
        $this->router->get($uri, $action)->withName($name);
        $routeUri = $this->router->route($name, ['articleId' => 1]);

        expect($routeUri)->toBe('/articles/1');
    })->with([
        [
            '/articles/{articleId}', 'articles.show', fn () => 'coucou',
        ],
    ]);

    it('should generate a route with parameters and multiple routes registered', function (array $routes) {
        foreach ($routes as $route) {
            $this->router->get($route[0], $route[2])->withName($route[1]);
        }

        $routeUri = $this->router->route('articles.show', ['articleId' => 1]);

        expect($routeUri)->toBe('/articles/1');

        $routeUri = $this->router->route('articles.index');

        expect($routeUri)->toBe('/articles');

        $routeUri = $this->router->route('articles.destroy', ['articleId' => 1]);

        expect($routeUri)->toBe('/articles/1/destroy');
    })->with([
        'routes' => [
            [
                [
                    '/articles/{articleId}', 'articles.show', fn () => 'coucou',
                ],
                [
                    '/articles', 'articles.index', fn () => 'coucou',
                ],
                [
                    '/articles/{articleId}/destroy', 'articles.destroy', fn () => 'coucou',
                ],
            ],
        ],
    ]);

    it('should generate a route with parameters and query parameters', function (string $uri, string $name, Closure $action) {
        $this->router->get($uri, $action)->withName($name);
        $routeUri = $this->router->route($name, ['articleId' => 1], ['page' => 2]);

        expect($routeUri)->toBe('/articles/1?page=2');
    })->with([
        [
            '/articles/{articleId}', 'articles.show', fn () => 'coucou',
        ],
    ]);
});
