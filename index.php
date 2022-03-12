<?php

use Core\Kernel;

try {
    require_once __DIR__ . '/vendor/autoload.php';
    
    $app = require __DIR__ . '/src/bootstrap/app.php';

    $app->run();
    
} catch (\Throwable $th) {
    dd($th);
}