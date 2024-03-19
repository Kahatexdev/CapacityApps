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
    $routes->get('dataorder', 'CapacityController::order');
    $routes->get('dataproduksi', 'CapacityController::produksi');
    $routes->get('databooking/(:any)', 'CapacityController::bookingPerJarum/$1');
    $routes->get('dataorder/(:any)', 'CapacityController::orderPerJarum/$1');
    $routes->get('detailbooking/(:any)', 'CapacityController::detailbooking/$1');
    $routes->post('inputbooking', 'CapacityController::inputbooking');
    $routes->post('inputOrder', 'CapacityController::inputOrder');
});


//planning manager
$routes->group('/planning', ['filter' => 'planning'], function ($routes) {
});


//aps
$routes->group('/aps', ['filter' => 'aps'], function ($routes) {
});
