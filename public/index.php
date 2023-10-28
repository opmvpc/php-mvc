<?php

require __DIR__.'/../vendor/autoload.php';

use App\App;

\session_start();

$app = App::get();
$app->run();
