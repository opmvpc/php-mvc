<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use Framework\Routing\Router;

return $registerAuthRoutes = function (Router $router) {
    $router->get('/login', [LoginController::class, 'showLoginForm'])->withName('auth.login.show');
    $router->post('/login/authenticate', [LoginController::class, 'login'])->withName('auth.login.auth');

    $router->get('/register', [RegisterController::class, 'showRegisterForm'])->withName('auth.register.show');
    $router->post('/register/store', [RegisterController::class, 'register'])->withName('auth.register.store');

    $router->post('/logout', [LoginController::class, 'logout'])->withName('auth.logout');
};
