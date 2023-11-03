<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseController;
use Framework\Requests\Response;
use Framework\View\View;

class LoginController extends BaseController
{
    public function showLoginForm(): View
    {
        return new View('auth/login');
    }

    public function login(): Response {}
}
