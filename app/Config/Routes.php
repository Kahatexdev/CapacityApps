<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
//auth
$routes->get('/', 'AuthController::index');
$routes->get('/login', 'AuthController::index');
$routes->post('authverify', 'AuthController::login');



$routes->group('/capacity', ['filter' => 'capacity'], function ($routes) {
    $routes->get('', 'CapacityController::index');
    $routes->get('databooking', 'CapacityController::booking');
});


//planning manager
$routes->group('/planning', ['filter' => 'planning'], function ($routes) {
});


//aps
$routes->group('/aps', ['filter' => 'aps'], function ($routes) {
});
