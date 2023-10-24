<?php

namespace App\Http\Controllers;

class HomeController extends BaseController
{
    public function index(): string
    {
        return '<h1>Hello</h1>';
    }
}
