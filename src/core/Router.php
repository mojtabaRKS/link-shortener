<?php

namespace Core;

use Core\Request;
use Core\Response;

class Router
{
    public function get($app_route, $app_callback)
    {
        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'GET') !== 0) {
            return;
        }

        $this->on($app_route, $app_callback);
    }

    public function post($app_route, $app_callback)
    {
        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') !== 0) {
            return;
        }

        $this->on($app_route, $app_callback);
    }

    public function put($app_route, $app_callback)
    {
        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'PUT') !== 0) {
            return;
        }

        $this->on($app_route, $app_callback);
    }

    public function delete($app_route, $app_callback)
    {
        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'DELETE') !== 0) {
            return;
        }

        $this->on($app_route, $app_callback);
    }

    public function on($exprr, $call_back)
    {
        $parameters = $_SERVER['REQUEST_URI'];
        $parameters = (stripos($parameters, "/") !== 0) ? "/" . $parameters : $parameters;
        $exprr = str_replace('/', '\/', $exprr);
        $matched = preg_match('/^\/' . ($exprr) . '$/', $parameters, $is_matched, PREG_OFFSET_CAPTURE);

        if ($matched) {
            // first value is normally the route, lets remove it
            array_shift($is_matched);
            // Get the matches as parameters
            $parameters = array_map(function ($parameter) {
                return $parameter[0];
            }, $is_matched);

            $controller = new $call_back[0];
            $action = $call_back[1];
            $controller->$action(new Request($parameters), new Response());
        }
    }
}