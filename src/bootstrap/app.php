<?php

use Core\Kernel;
use Core\Logger;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->load();

// Logger::enableSystemLogs();
// $logger = Logger::getInstance();

try {
    
    $routes = require  __DIR__ . '/../routes.php';

    return new Kernel;

} catch (\Throwable $th) {
    $logger->error($th->getMessage(), ['trace' => $th->getTraceAsString()]);
    dd($th);
}
