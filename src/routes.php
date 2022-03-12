<?php

use Core\Router;
use App\Controllers\LinkController;

$router = new Router();

$router->get('links', [LinkController::class, 'index']);
$router->post('links', [LinkController::class, 'store']);
$router->put('links/([0-9]*)', [LinkController::class, 'update']);
$router->delete('links/([0-9]*)', [LinkController::class, 'destroy']);

// $router->post('login', ['App\Controllers\AuthController', 'login']);
