<?php

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

$collection = new RouteCollection();

$collection->add('links.show', newRoute(
    'GET', 
    '/links/{id}', 
    'App\Controllers\LinkController@show', 
    ['id' => '[0-9]+']
));

$collection->add('links.store', newRoute(
    'POST', 
    '/links', 
    'App\Controllers\LinkController@store', 
));

return $collection;
