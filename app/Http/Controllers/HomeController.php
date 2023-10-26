<?php

namespace App\Http\Controllers;

use Framework\View\View;

class HomeController extends BaseController
{
    public function index(): View
    {
        return new View('index');
    }
}
