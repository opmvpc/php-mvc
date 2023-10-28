<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Framework\View\View;

class HomeController extends BaseController
{
    public function index(): View
    {
        return new View('index');
    }
}
