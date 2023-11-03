<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use Framework\Support\Session;
use Framework\View\View;

class DashboardController extends BaseController
{
    public function index(): View
    {
        $userName = Session::get('user')->name();
        $message = 'Hello, '.$userName.'!';

        return new View('admin/dashboard', [
            'message' => $message,
        ]);
    }
}
