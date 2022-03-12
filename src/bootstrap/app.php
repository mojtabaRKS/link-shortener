<?php

use Core\Kernel;
use Dotenv\Dotenv;
use Core\Logging\Logger;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\UrlMatcher;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->load();

Logger::enableSystemLogs();
$logger = Logger::getInstance();

try {

    $app = new Kernel;

    $routes = require  __DIR__ . '/../routes.php';

    $context = new RequestContext();
    $request = Request::createFromGlobals();
    $context = $context->fromRequest($request);

    // Init UrlMatcher object
    $matcher = new UrlMatcher($routes, $context);
 
    // Find the current route
    $parameters = $matcher->match($context->getPathInfo());

    $controller = $parameters['controller'];
    $action = $parameters['method'];
    
    if (isset($parameters['id'])) {
        $id = $parameters['id'];
        return (new $controller)->$action($id, $request);
    } else {
        return (new $controller)->$action($request);
    }

} catch (\Throwable $th) {
    $logger->error($th->getMessage(), ['trace' => $th->getTraceAsString()]);

    dd($th, __FILE__, __LINE__);
}
