<?php

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

        expect($this->router->paths()['/']->method())->toBe(HttpVerb::GET);
        expect($this->router->paths()['/']->action())->toBeArray();
        expect($this->router->paths()['/articles']->method())->toBe(HttpVerb::GET);
        expect($this->router->paths()['/articles']->action())->toBeInstanceOf(Closure::class);
        expect($this->router->paths()['/error']->method())->toBe(HttpVerb::GET);
        expect($this->router->paths()['/error']->action())->toBeInstanceOf(Closure::class);
        expect($this->router->paths()['/post']->method())->toBe(HttpVerb::POST);
        expect($this->router->paths()['/post']->action())->toBeInstanceOf(Closure::class);
    });

    it('should register a POST route', function (string $uri, Closure $action) {
        $this->router->post($uri, $action);
        expect($this->router->paths()[$uri]->method())->toBe(HttpVerb::POST);
        expect($this->router->paths()[$uri]->action())->toBeInstanceOf(Closure::class);
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
        expect($this->router->paths()[$uri]->method())->toBe(HttpVerb::PUT);
        expect($this->router->paths()[$uri]->action())->toBeInstanceOf(Closure::class);
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
        expect($this->router->paths()[$uri]->method())->toBe(HttpVerb::DELETE);
        expect($this->router->paths()[$uri]->action())->toBeInstanceOf(Closure::class);
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
        $response = $this->router->dispatch();
        expect($response->getBody())->toContain('Server Error');
        expect($response->getBody())->toContain('An error');
        expect($response->getStatusCode())->toBe(500);
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
});
