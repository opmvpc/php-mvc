<?php

it('should register a GET route', function () {
    $router = new Framework\Routing\Router();
    $router->get('/', fn () => print 'coucou');

    expect($router->paths())->toHaveCount(1);
    expect($router->paths()['/']->method())->toBe(Framework\Routing\HttpVerb::GET);
    expect($router->paths()['/']->action())->toBeInstanceOf(Closure::class);
});

it('should register a POST route', function () {
    $router = new Framework\Routing\Router();
    $router->post('/', fn () => print 'coucou');

    expect($router->paths())->toHaveCount(1);
    expect($router->paths()['/']->method())->toBe(Framework\Routing\HttpVerb::POST);
    expect($router->paths()['/']->action())->toBeInstanceOf(Closure::class);
});

it('should register a PUT route', function () {
    $router = new Framework\Routing\Router();
    $router->put('/', fn () => print 'coucou');

    expect($router->paths())->toHaveCount(1);
    expect($router->paths()['/']->method())->toBe(Framework\Routing\HttpVerb::PUT);
    expect($router->paths()['/']->action())->toBeInstanceOf(Closure::class);
});

it('should register a DELETE route', function () {
    $router = new Framework\Routing\Router();
    $router->delete('/', fn () => print 'coucou');

    expect($router->paths())->toHaveCount(1);
    expect($router->paths()['/']->method())->toBe(Framework\Routing\HttpVerb::DELETE);
    expect($router->paths()['/']->action())->toBeInstanceOf(Closure::class);
});

it('should register multiple routes', function () {
    $router = new Framework\Routing\Router();
    $router->get('/', fn () => print 'coucou');
    $router->post('/articles', fn () => print 'coucou');

    expect($router->paths())->toHaveCount(2);
    expect($router->paths()['/']->method())->toBe(Framework\Routing\HttpVerb::GET);
    expect($router->paths()['/']->action())->toBeInstanceOf(Closure::class);
    expect($router->paths()['/articles']->method())->toBe(Framework\Routing\HttpVerb::POST);
    expect($router->paths()['/articles']->action())->toBeInstanceOf(Closure::class);
});

it('should return a 404 not found response when no route is found', function () {
    $router = new Framework\Routing\Router();
    $router->get('/', fn () => print 'coucou');

    $response = $router->dispatch('GET', '/articles');
});
