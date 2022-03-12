<?php

if (! function_exists('dd')) {
    function dd(...$args)
    {
        foreach ($args as $arg) {
            dump($arg);
        }
        die();
    }
}