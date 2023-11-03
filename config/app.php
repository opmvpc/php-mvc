<?php

return [
    'name' => $_ENV['APP_NAME'] ?? 'APP PHP MVC Framework',
    'env' => $_ENV['APP_ENV'] ?? 'production',
    'debug' => $_ENV['APP_DEBUG'] ?? false,
    'lang' => 'fr',
];
