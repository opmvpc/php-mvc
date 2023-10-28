<?php

use Framework\Exceptions\MethodNotAllowedException;
use Framework\Requests\ResponseInterface;
use Framework\Routing\HttpVerb;
use Framework\Routing\Router;

beforeEach(function () {
    $this->router = new Router();
});

afterEach(function () {
    unset($this->router);
});

describe('Router Tests', function () {
    it('should register routes from route file', function () {
        $registerRoutes = require __DIR__.'/fixtures/simple_routes.php';
        $registerRoutes($this->router);

        expect($this->router->routes()['/']->method())->toBe(HttpVerb::GET);
        expect($this->router->routes()['/']->action())->toBeArray();
        expect($this->router->routes()['/articles']->method())->toBe(HttpVerb::GET);
        expect($this->router->routes()['/articles']->action())->toBeInstanceOf(Closure::class);
        expect($this->router->routes()['/error']->method())->toBe(HttpVerb::GET);
        expect($this->router->routes()['/error']->action())->toBeInstanceOf(Closure::class);
        expect($this->router->routes()['/post']->method())->toBe(HttpVerb::POST);
        expect($this->router->routes()['/post']->action())->toBeInstanceOf(Closure::class);
    });

    it('should register a POST route', function (string $uri, Closure $action) {
        $this->router->post($uri, $action);
        expect($this->router->routes()[$uri]->method())->toBe(HttpVerb::POST);
        expect($this->router->routes()[$uri]->action())->toBeInstanceOf(Closure::class);
    })->with([
        [
            '/post', fn () => 'post',
        ],
        [
            '/articles', fn () => 'post',
        ],
    ]);

    it('should register a PUT route', function (string $uri, Closure $action) {
        $this->router->put($uri, $action);
        expect($this->router->routes()[$uri]->method())->toBe(HttpVerb::PUT);
        expect($this->router->routes()[$uri]->action())->toBeInstanceOf(Closure::class);
    })->with([
        [
            '/put', fn () => 'put',
        ],
        [
            '/articles', fn () => 'put',
        ],
    ]);

    it('should register a DELETE route', function (string $uri, Closure $action) {
        $this->router->delete($uri, $action);
        expect($this->router->routes()[$uri]->method())->toBe(HttpVerb::DELETE);
        expect($this->router->routes()[$uri]->action())->toBeInstanceOf(Closure::class);
    })->with([
        [
            '/delete', fn () => 'delete',
        ],
        [
            '/articles', fn () => 'delete',
        ],
    ]);

    it('should return a response when a route is found', function (string $uri, Closure $action) {
        $this->router->get($uri, $action);
        $response = $this->router->dispatch();
        expect($response)->toBeInstanceOf(ResponseInterface::class);
        expect($response->getBody())->toContain('coucou');
        expect($response->getStatusCode())->toBe(200);
    })->with([
        [
            '/', fn () => 'coucou',
        ],
    ]);

    it('should return the right response when multiple routes are registered', function (array $routes) {
        foreach ($routes as $route) {
            $this->router->add($route['uri'], $route['method'], $route['action']);
        }

        $response = $this->router->dispatch();
        expect($response->getBody())->toContain('coucou');
        expect($response->getStatusCode())->toBe(200);

        $response = $this->router->dispatch('/articles', HttpVerb::POST);
        expect($response->getBody())->toContain('articles');
        expect($response->getStatusCode())->toBe(200);
    })->with([
        'routes' => [
            [
                ['uri' => '/', 'method' => HttpVerb::GET, 'action' => fn () => 'coucou'],
                ['uri' => '/articles', 'method' => HttpVerb::POST, 'action' => fn () => 'articles'],
            ],
        ],
    ]);

    it('should return a 404 not found response when no route is found', function () {
        $response = $this->router->dispatch('/articles', HttpVerb::GET);
        expect($response->getBody())->toContain('Not Found');
        expect($response->getStatusCode())->toBe(404);
    });

    it('should return a 500 internal server error response when an exception is thrown', function (string $uri, Closure $action) {
        $this->router->get($uri, $action);
        expect(fn () => $this->router->dispatch())->toThrow(new Exception('An error'));
    })->with([
        [
            '/', fn () => throw new Exception('An error'),
        ],
    ]);

    it('should execute controller action', function () {
        $registerRoutes = require __DIR__.'/fixtures/simple_routes.php';
        $registerRoutes($this->router);

        $response = $this->router->dispatch();
        expect($response->getBody())->toContain('<h1>Hello</h1>');
        expect($response->getStatusCode())->toBe(200);
    });

    it('should throw a not allowed exception when method is not allowed', function () {
        $registerRoutes = require __DIR__.'/fixtures/simple_routes.php';
        $registerRoutes($this->router);

        expect(fn () => $this->router->dispatch('/articles', HttpVerb::POST))->toThrow(new MethodNotAllowedException());
    });

    it('should match a route with a parameter', function (string $uri, Closure $action) {
        $this->router->get($uri, $action);

        $route = $this->router->match(HttpVerb::GET, '/articles/1');
        expect($route->path())->toBe('/articles/{id}');
        expect($route->params())->toBe(['id' => '1']);
    })->with([
        [
            '/articles/{id}', fn () => 'coucou',
        ],
    ]);

    it('should match a route with 2 parameters', function (string $uri, Closure $action) {
        $this->router->get($uri, $action);

        $route = $this->router->match(HttpVerb::GET, '/articles/1/comments/2');
        expect($route->path())->toBe('/articles/{articleId}/comments/{commentId}');
        expect($route->params())->toBe(['articleId' => '1', 'commentId' => '2']);
    })->with([
        [
            '/articles/{articleId}/comments/{commentId}', fn () => 'coucou',
        ],
    ]);

    it('should match a route with an optional parameter', function (string $uri, Closure $action) {
        $this->router->get($uri, $action);

        $route = $this->router->match(HttpVerb::GET, '/articles/1');
        expect($route->path())->toBe('/articles/{id?}');
        expect($route->params())->toBe(['id' => '1']);

        $route = $this->router->match(HttpVerb::GET, '/articles');
        expect($route->path())->toBe('/articles/{id?}');
        expect($route->params())->toBe(['id' => null]);
    })->with([
        [
            '/articles/{id?}', fn () => 'coucou',
        ],
    ]);

    it('should match a route with an optional and a required parameters', function (string $uri, Closure $action) {
        $this->router->get($uri, $action);

        $route = $this->router->match(HttpVerb::GET, '/articles/1/comments');
        expect($route->path())->toBe('/articles/{articleId}/comments/{commentId?}');
        expect($route->params())->toBe(['articleId' => '1', 'commentId' => null]);

        $route = $this->router->match(HttpVerb::GET, '/articles/1/comments/2');
        expect($route->path())->toBe('/articles/{articleId}/comments/{commentId?}');
        expect($route->params())->toBe(['articleId' => '1', 'commentId' => '2']);
    })->with([
        [
            '/articles/{articleId}/comments/{commentId?}', fn () => 'coucou',
        ],
    ]);

    it('should match a route with 2 optional parameters', function (string $uri, Closure $action) {
        $this->router->get($uri, $action);

        $route = $this->router->match(HttpVerb::GET, '/articles/1/comments');
        expect($route->path())->toBe('/articles/{articleId}/comments/{commentId?}');
        expect($route->params())->toBe(['articleId' => '1', 'commentId' => null]);

        $route = $this->router->match(HttpVerb::GET, '/articles/1/comments/2');
        expect($route->path())->toBe('/articles/{articleId}/comments/{commentId?}');
        expect($route->params())->toBe(['articleId' => '1', 'commentId' => '2']);
    })->with([
        [
            '/articles/{articleId}/comments/{commentId?}', fn () => 'coucou',
        ],
    ]);

    it('should match a route with multiple parametres', function (string $uri, Closure $action) {
        $this->router->get($uri, $action);

        $route = $this->router->match(HttpVerb::GET, '/categories/1/articles/2/recommendations/3/comments/4');
        expect($route->path())->toBe('/categories/{categoryId}/articles/{articleId}/recommendations/{recommendationId?}/comments/{commentId?}');
        expect($route->params())->toBe(['categoryId' => '1', 'articleId' => '2', 'recommendationId' => '3', 'commentId' => '4']);

        $route = $this->router->match(HttpVerb::GET, '/categories/1/articles/2/recommendations/3/comments');
        expect($route->path())->toBe('/categories/{categoryId}/articles/{articleId}/recommendations/{recommendationId?}/comments/{commentId?}');
        expect($route->params())->toBe(['categoryId' => '1', 'articleId' => '2', 'recommendationId' => '3', 'commentId' => null]);

        $route = $this->router->match(HttpVerb::GET, '/categories/1/articles/2/recommendations/3/comments/');
        expect($route->path())->toBe('/categories/{categoryId}/articles/{articleId}/recommendations/{recommendationId?}/comments/{commentId?}');
        expect($route->params())->toBe(['categoryId' => '1', 'articleId' => '2', 'recommendationId' => '3', 'commentId' => null]);

        $route = $this->router->match(HttpVerb::GET, '/categories/1/articles/2/recommendations/comments/');
        expect($route->path())->toBe('/categories/{categoryId}/articles/{articleId}/recommendations/{recommendationId?}/comments/{commentId?}');
        expect($route->params())->toBe(['categoryId' => '1', 'articleId' => '2', 'recommendationId' => null, 'commentId' => null]);
    })->with([
        [
            '/categories/{categoryId}/articles/{articleId}/recommendations/{recommendationId?}/comments/{commentId?}', fn () => 'coucou',
        ],
    ]);

    it('should work with a index and show route with parameter', function (array $routes) {
        foreach ($routes as $route) {
            $this->router->add($route['uri'], $route['method'], $route['action']);
        }

        $response = $this->router->match(HttpVerb::GET, '/articles');
        expect($response->path())->toBe('/articles');
        expect($response->params())->toBe([]);

        $response = $this->router->match(HttpVerb::GET, '/articles/1');
        expect($response->path())->toBe('/articles/{id}');
        expect($response->params())->toBe(['id' => '1']);
    })->with([
        'routes' => [
            [
                ['uri' => '/articles', 'method' => HttpVerb::GET, 'action' => fn () => 'index'],
                ['uri' => '/articles/{id}', 'method' => HttpVerb::GET, 'action' => fn () => 'show'],
            ],
        ],
    ]);

    it('should match a simple route with multiple routes registered', function (array $routes) {
        foreach ($routes as $route) {
            $this->router->add($route['uri'], $route['method'], $route['action']);
        }

        $response = $this->router->match(HttpVerb::GET, '/articles/1');
        expect($response->path())->toBe('/articles/{id}');
        expect($response->params())->toBe(['id' => '1']);

        $response = $this->router->match(HttpVerb::GET, '/test');
        expect($response->path())->toBe('/test');
        expect($response->params())->toBe([]);
    })->with([
        'routes' => [
            [
                ['uri' => '/articles/{id}', 'method' => HttpVerb::GET, 'action' => fn () => 'show'],
                ['uri' => '/test', 'method' => HttpVerb::GET, 'action' => fn () => 'test'],
            ],
        ],
    ]);
});
