<?php

use Core\Kernel;
use Dotenv\Dotenv;
use Core\Logging\Logger;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\HttpFoundation\Response;
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

} catch (\Throwable $exception) {

    $logger->error($exception->getMessage(), ['trace' => $exception->getTraceAsString()]);
    $code = $exception->getCode() ?? Response::HTTP_BAD_REQUEST;
    return new Response(
        json_encode([
            'status' => false,
            'code' => $code,
            'message' => $exception->getMessage(),
            'data' => $_ENV['APP_DEBUG'] ? $exception->getTrace() : [],
        ]),
        $code,
        ['content-type' => 'text/json']
    );
}
