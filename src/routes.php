<?php

use Symfony\Component\Routing\RouteCollection;

$collection = new RouteCollection();

$collection->add('links.index', newRoute(
    'DELETE',
    '/links',
    'App\Controllers\LinkController@index',
));

$collection->add('links.store', newRoute(
    'POST',
    '/links',
    'App\Controllers\LinkController@store',
));

$collection->add('links.show', newRoute(
    'GET',
    '/links/{id}',
    'App\Controllers\LinkController@show',
    ['id' => '[0-9]+']
));

$collection->add('links.update', newRoute(
    'PUT',
    '/links/{id}',
    'App\Controllers\LinkController@update',
    ['id' => '[0-9]+']
));

$collection->add('links.delete', newRoute(
    'DELETE',
    '/links/{id}',
    'App\Controllers\LinkController@delete',
    ['id' => '[0-9]+']
));

$collection->add('links.redirect', newRoute(
    'GET',
    '/{code}',
    'App\Controllers\LinkController@redirect',
));

$collection->add('auth.login', newRoute(
    'POST',
    '/',
    'App\Controllers\AuthController@login',
));

$collection->add('auth.logout', newRoute(
    'POST',
    '/',
    'App\Controllers\AuthController@logout',
));

return $collection;
