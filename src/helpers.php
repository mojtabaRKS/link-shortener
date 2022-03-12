<?php

use Symfony\Component\Routing\Route;

if (!function_exists('dd')) {
    function dd(...$args)
    {
        foreach ($args as $arg) {
            dump($arg);
        }
        die();
    }
}

if (!function_exists('newRoute')) {
    function newRoute($verb, $path, $controller , $requirements = [], $defaults = [], $schemes = [])
    {
        return new Route($path, [
            'controller' => explode('@', $controller)[0],
            'method' => explode('@', $controller)[1]
        ], $requirements, $defaults, '', $schemes, [$verb]);
    }
}

if (! function_exists('generateRandomString')) {
    function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
