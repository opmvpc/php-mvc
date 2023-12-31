<?php

use Framework\Exceptions\MethodNotAllowedException;
use Framework\Requests\JsonResponse;
use Framework\Requests\ResponseInterface;
use Framework\Requests\ViewResponse;
use Framework\Routing\HttpVerb;
use Framework\Routing\Router;

describe('Router Tests', function () {
    beforeEach(function () {
        $this->router = Router::getInstance();
    });

    afterEach(function () {
        $this->router->__destruct();
        unset($this->router);
    });

    it('should register routes from route file', function () {
        $this->router->__destruct();
        unset($this->router);
        $this->router = Router::getInstance();
        $registerRoutes = require __DIR__.'/fixtures/simple_routes.php';
        $registerRoutes($this->router);
        expect($this->router->routes())->toBeArray();
        expect($this->router->routes())->toHaveLength(5);

        expect($this->router->routes()[0]->path())->toBe('/');
        expect($this->router->routes()[0]->method())->toBe(HttpVerb::GET);
        expect($this->router->routes()[0]->action())->toBeArray();

        expect($this->router->routes()[1]->path())->toBe('/articles');
        expect($this->router->routes()[1]->method())->toBe(HttpVerb::GET);
        expect($this->router->routes()[1]->action())->toBeInstanceOf(Closure::class);

        expect($this->router->routes()[2]->path())->toBe('/articles/{id?}');
        expect($this->router->routes()[2]->method())->toBe(HttpVerb::GET);
        expect($this->router->routes()[2]->action())->toBeInstanceOf(Closure::class);

        expect($this->router->routes()[3]->path())->toBe('/error');
        expect($this->router->routes()[3]->method())->toBe(HttpVerb::GET);
        expect($this->router->routes()[3]->action())->toBeInstanceOf(Closure::class);

        expect($this->router->routes()[4]->path())->toBe('/post');
        expect($this->router->routes()[4]->method())->toBe(HttpVerb::POST);
        expect($this->router->routes()[4]->action())->toBeInstanceOf(Closure::class);
    });

    it('should register a POST route', function (string $uri, Closure $action) {
        $this->router->post($uri, $action);

        expect($this->router->routes())->toBeArray();
        expect($this->router->routes())->toHaveLength(1);

        expect($this->router->routes()[0]->method())->toBe(HttpVerb::POST);
        expect($this->router->routes()[0]->action())->toBeInstanceOf(Closure::class);
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
        expect($this->router->routes()[0]->method())->toBe(HttpVerb::PUT);
        expect($this->router->routes()[0]->action())->toBeInstanceOf(Closure::class);
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
        expect($this->router->routes()[0]->method())->toBe(HttpVerb::DELETE);
        expect($this->router->routes()[0]->action())->toBeInstanceOf(Closure::class);
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

        expect($this->router->routes())->toBeArray();
        expect($this->router->routes())->toHaveLength(2);

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

    it('should match a route with a parameter', function (string $uri, Closure $action) {
        $this->router->get($uri, $action);

        $route = $this->router->match(HttpVerb::GET, '/articles/1');
        expect($route['route']->path())->toBe('/articles/{id}');
        expect($route['route']->params())->toBe(['id' => '1']);
    })->with([
        [
            '/articles/{id}', fn () => 'coucou',
        ],
    ]);

    it('should match a route with 2 parameters', function (string $uri, Closure $action) {
        $this->router->get($uri, $action);

        $route = $this->router->match(HttpVerb::GET, '/articles/1/comments/2');
        expect($route['route']->path())->toBe('/articles/{articleId}/comments/{commentId}');
        expect($route['route']->params())->toBe(['articleId' => '1', 'commentId' => '2']);
    })->with([
        [
            '/articles/{articleId}/comments/{commentId}', fn () => 'coucou',
        ],
    ]);

    it('should match a route with an optional parameter', function (string $uri, Closure $action) {
        $this->router->get($uri, $action);

        $route = $this->router->match(HttpVerb::GET, '/articles/1');
        expect($route['route']->path())->toBe('/articles/{id?}');
        expect($route['route']->params())->toBe(['id' => '1']);

        $route = $this->router->match(HttpVerb::GET, '/articles');
        expect($route['route']->path())->toBe('/articles/{id?}');
        expect($route['route']->params())->toBe(['id' => null]);
    })->with([
        [
            '/articles/{id?}', fn () => 'coucou',
        ],
    ]);

    it('should match a route with an optional and a required parameters', function (string $uri, Closure $action) {
        $this->router->get($uri, $action);

        $route = $this->router->match(HttpVerb::GET, '/articles/1/comments');
        expect($route['route']->path())->toBe('/articles/{articleId}/comments/{commentId?}');
        expect($route['route']->params())->toBe(['articleId' => '1', 'commentId' => null]);

        $route = $this->router->match(HttpVerb::GET, '/articles/1/comments/2');
        expect($route['route']->path())->toBe('/articles/{articleId}/comments/{commentId?}');
        expect($route['route']->params())->toBe(['articleId' => '1', 'commentId' => '2']);
    })->with([
        [
            '/articles/{articleId}/comments/{commentId?}', fn () => 'coucou',
        ],
    ]);

    it('should match a route with 2 optional parameters', function (string $uri, Closure $action) {
        $this->router->get($uri, $action);

        $route = $this->router->match(HttpVerb::GET, '/articles/1/comments');
        expect($route['route']->path())->toBe('/articles/{articleId}/comments/{commentId?}');
        expect($route['route']->params())->toBe(['articleId' => '1', 'commentId' => null]);

        $route = $this->router->match(HttpVerb::GET, '/articles/1/comments/2');
        expect($route['route']->path())->toBe('/articles/{articleId}/comments/{commentId?}');
        expect($route['route']->params())->toBe(['articleId' => '1', 'commentId' => '2']);
    })->with([
        [
            '/articles/{articleId}/comments/{commentId?}', fn () => 'coucou',
        ],
    ]);

    it('should match a route with multiple parametres', function (string $uri, Closure $action) {
        $this->router->get($uri, $action);

        $route = $this->router->match(HttpVerb::GET, '/categories/1/articles/2/recommendations/3/comments/4');
        expect($route['route']->path())->toBe('/categories/{categoryId}/articles/{articleId}/recommendations/{recommendationId?}/comments/{commentId?}');
        expect($route['route']->params())->toBe(['categoryId' => '1', 'articleId' => '2', 'recommendationId' => '3', 'commentId' => '4']);

        $route = $this->router->match(HttpVerb::GET, '/categories/1/articles/2/recommendations/3/comments');
        expect($route['route']->path())->toBe('/categories/{categoryId}/articles/{articleId}/recommendations/{recommendationId?}/comments/{commentId?}');
        expect($route['route']->params())->toBe(['categoryId' => '1', 'articleId' => '2', 'recommendationId' => '3', 'commentId' => null]);

        $route = $this->router->match(HttpVerb::GET, '/categories/1/articles/2/recommendations/3/comments/');
        expect($route['route']->path())->toBe('/categories/{categoryId}/articles/{articleId}/recommendations/{recommendationId?}/comments/{commentId?}');
        expect($route['route']->params())->toBe(['categoryId' => '1', 'articleId' => '2', 'recommendationId' => '3', 'commentId' => null]);

        $route = $this->router->match(HttpVerb::GET, '/categories/1/articles/2/recommendations/comments/');
        expect($route['route']->path())->toBe('/categories/{categoryId}/articles/{articleId}/recommendations/{recommendationId?}/comments/{commentId?}');
        expect($route['route']->params())->toBe(['categoryId' => '1', 'articleId' => '2', 'recommendationId' => null, 'commentId' => null]);
    })->with([
        [
            '/categories/{categoryId}/articles/{articleId}/recommendations/{recommendationId?}/comments/{commentId?}', fn () => 'coucou',
        ],
    ]);

    it('should work with a index and show route with parameter', function (array $routes) {
        foreach ($routes as $route) {
            $this->router->add($route['uri'], $route['method'], $route['action']);
        }

        expect($this->router->routes())->toBeArray();
        expect($this->router->routes())->toHaveLength(2);

        $response = $this->router->match(HttpVerb::GET, '/articles');
        expect($response['route']->path())->toBe('/articles');
        expect($response['route']->params())->toBe([]);

        $response = $this->router->match(HttpVerb::GET, '/articles/1');
        expect($response['route']->path())->toBe('/articles/{id}');
        expect($response['route']->params())->toBe(['id' => '1']);
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

        expect($this->router->routes())->toBeArray();
        expect($this->router->routes())->toHaveLength(2);

        $response = $this->router->match(HttpVerb::GET, '/articles/1');
        expect($response['route']->path())->toBe('/articles/{id}');
        expect($response['route']->params())->toBe(['id' => '1']);

        $response = $this->router->match(HttpVerb::GET, '/test');
        expect($response['route']->path())->toBe('/test');
        expect($response['route']->params())->toBe([]);
    })->with([
        'routes' => [
            [
                ['uri' => '/articles/{id}', 'method' => HttpVerb::GET, 'action' => fn () => 'show'],
                ['uri' => '/test', 'method' => HttpVerb::GET, 'action' => fn () => 'test'],
            ],
        ],
    ]);

    it('should dispatch a view response', function () {
        $this->router->get('/', fn () => view('basic'));
        $response = $this->router->dispatch();
        expect($response)->toBeInstanceOf(ResponseInterface::class);
        expect($response)->toBeInstanceOf(ViewResponse::class);
        expect($response->getBody())->toContain('<h1>Hello</h1>');
        expect($response->getStatusCode())->toBe(200);
    });

    it('should dispatch a redirect response', function () {
        $this->router->get('/', fn () => Router::redirect('/test'));
        $this->router->get('/test', fn () => 'test');
        $response = $this->router->dispatch();
        expect($response)->toBeInstanceOf(ResponseInterface::class);
        expect($response->getStatusCode())->toBe(302);
        expect($response->getHeaders())->toBeArray();
        expect($response->getHeaders()['Location'])->toBe('/test');
    });

    it('should dispatch a redirect response with a route name', function () {
        $this->router->get('/', fn () => Router::redirect('test.test'));
        $this->router->get('/test', fn () => 'test')->withName('test.test');
        $response = $this->router->dispatch();
        expect($response)->toBeInstanceOf(ResponseInterface::class);
        expect($response->getStatusCode())->toBe(302);
        expect($response->getHeaders())->toBeArray();
        expect($response->getHeaders()['Location'])->toBe('/test');
    });

    it('should dispatch a Json response', function () {
        $this->router->get('/', fn () => ['test' => 'test']);
        $response = $this->router->dispatch();
        expect($response)->toBeInstanceOf(ResponseInterface::class);
        expect($response)->toBeInstanceOf(JsonResponse::class);
        expect($response->getBody())->toBeJson(
            <<<'JSON'
            {
                "test": "test"
            }
            JSON
        );
        expect($response->getStatusCode())->toBe(200);
        expect($response->getHeaders())->toBeArray();
        expect($response->getHeaders()['Content-Type'])->toBe('application/json');
    });

    it('should return the right response when multiple routes are registered with namespaced names', function (array $routes) {
        foreach ($routes as $route) {
            $this->router->add($route['uri'], $route['method'], $route['action']);
        }

        expect($this->router->routes())->toBeArray();
        expect($this->router->routes())->toHaveLength(3);

        $response = $this->router->dispatch();

        expect($response->getBody())->toContain('coucou');
        expect($response->getStatusCode())->toBe(200);

        $response = $this->router->dispatch('/articles/1');

        expect($response->getBody())->toContain('show');
        expect($response->getStatusCode())->toBe(200);

        $response = $this->router->dispatch('/admin/articles/1');

        expect($response->getBody())->toContain('admin');
        expect($response->getStatusCode())->toBe(200);
    })->with([
        'routes' => [
            [
                ['uri' => '/', 'method' => HttpVerb::GET, 'action' => fn () => 'coucou'],
                ['uri' => '/articles/{articleId}', 'method' => HttpVerb::GET, 'action' => fn () => 'show'],
                ['uri' => '/admin/articles/{articleId}', 'method' => HttpVerb::GET, 'action' => fn () => 'admin'],
            ],
        ],
    ]);

    it('should return the right response when multiple routes are registered with same name and different methods', function (array $routes) {
        foreach ($routes as $route) {
            $this->router->add($route['uri'], $route['method'], $route['action']);
        }

        expect($this->router->routes())->toBeArray();
        expect($this->router->routes())->toHaveLength(5);

        $response = $this->router->dispatch('/articles', HttpVerb::GET);
        expect($response->getBody())->toContain('index');

        $response = $this->router->dispatch('/articles', HttpVerb::POST);
        expect($response->getBody())->toContain('store');

        $response = $this->router->dispatch('/articles/1', HttpVerb::GET);
        expect($response->getBody())->toContain('show');

        $response = $this->router->dispatch('/articles/1', HttpVerb::PUT);
        expect($response->getBody())->toContain('update');

        $response = $this->router->dispatch('/articles/1', HttpVerb::DELETE);
        expect($response->getBody())->toContain('destroy');
    })->with([
        'routes' => [
            [
                ['uri' => '/articles', 'method' => HttpVerb::GET, 'action' => fn () => 'index'],
                ['uri' => '/articles', 'method' => HttpVerb::POST, 'action' => fn () => 'store'],
                ['uri' => '/articles/{articleId}', 'method' => HttpVerb::GET, 'action' => fn () => 'show'],
                ['uri' => '/articles/{articleId}', 'method' => HttpVerb::PUT, 'action' => fn () => 'update'],
                ['uri' => '/articles/{articleId}', 'method' => HttpVerb::DELETE, 'action' => fn () => 'destroy'],
            ],
        ],
    ]);

    it('should throw a NotAllowedExeption if verb does not match', function (array $routes) {
        foreach ($routes as $route) {
            $this->router->add($route['uri'], $route['method'], $route['action']);
        }

        expect($this->router->routes())->toBeArray();
        expect($this->router->routes())->toHaveLength(2);

        expect(fn () => $this->router->dispatch('/articles', HttpVerb::PUT))->toThrow(new MethodNotAllowedException('Method PUT is not allowed for the URI /articles. Allowed methods: GET, POST'));
        expect(fn () => $this->router->dispatch('/articles', HttpVerb::DELETE))->toThrow(new MethodNotAllowedException('Method DELETE is not allowed for the URI /articles. Allowed methods: GET, POST'));
    })->with([
        'routes' => [
            [
                ['uri' => '/articles', 'method' => HttpVerb::GET, 'action' => fn () => 'index'],
                ['uri' => '/articles', 'method' => HttpVerb::POST, 'action' => fn () => 'store'],
            ],
        ],
    ]);
});
