<?php

use Framework\Routing\HttpVerb;
use Framework\Routing\Router;

it('should register a GET route', function () {
    $router = new Router();
    $router->get('/', fn () => print 'coucou');

    expect($router->paths())->toHaveCount(1);
    expect($router->paths()['/']->method())->toBe(HttpVerb::GET);
    expect($router->paths()['/']->action())->toBeInstanceOf(Closure::class);
});

it('should register a POST route', function () {
    $router = new Router();
    $router->post('/', fn () => print 'coucou');

    expect($router->paths())->toHaveCount(1);
    expect($router->paths()['/']->method())->toBe(HttpVerb::POST);
    expect($router->paths()['/']->action())->toBeInstanceOf(Closure::class);
});

it('should register a PUT route', function () {
    $router = new Router();
    $router->put('/', fn () => print 'coucou');

    expect($router->paths())->toHaveCount(1);
    expect($router->paths()['/']->method())->toBe(HttpVerb::PUT);
    expect($router->paths()['/']->action())->toBeInstanceOf(Closure::class);
});

it('should register a DELETE route', function () {
    $router = new Router();
    $router->delete('/', fn () => print 'coucou');

    expect($router->paths())->toHaveCount(1);
    expect($router->paths()['/']->method())->toBe(HttpVerb::DELETE);
    expect($router->paths()['/']->action())->toBeInstanceOf(Closure::class);
});

it('should register multiple routes', function () {
    $router = new Router();
    $router->get('/', fn () => print 'coucou');
    $router->post('/articles', fn () => print 'coucou');

    expect($router->paths())->toHaveCount(2);
    expect($router->paths()['/']->method())->toBe(HttpVerb::GET);
    expect($router->paths()['/']->action())->toBeInstanceOf(Closure::class);
    expect($router->paths()['/articles']->method())->toBe(HttpVerb::POST);
    expect($router->paths()['/articles']->action())->toBeInstanceOf(Closure::class);
});

it('should return a 404 not found response when no route is found', function () {
    $router = new Router();

    $function = function () {
        echo 'coucou';
    };

    $router->get('/', $function);

    var_dump($router);

    $response = $router->dispatch('GET', '/articles');
    var_dump($response);
    expect($response->getBody())->toContain('Not Found');
    expect($response->getStatusCode())->toBe(404);
});
