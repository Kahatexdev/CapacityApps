<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
//auth
$routes->get('/', 'AuthController::index');
$routes->get('/login', 'AuthController::index');
$routes->post('/logout', 'AuthController::logout');
$routes->post('authverify', 'AuthController::login');



$routes->group('/capacity', ['filter' => 'capacity'], function ($routes) {
    $routes->get('', 'CapacityController::index');
    // booking
    $routes->get('databooking', 'CapacityController::booking');
    $routes->get('databooking/(:any)', 'CapacityController::bookingPerJarum/$1');
    $routes->get('detailbooking/(:any)', 'CapacityController::detailbooking/$1');
    $routes->post('inputbooking', 'CapacityController::inputbooking');
    $routes->post('updatebooking/(:any)', 'CapacityController::updatebooking/$1');
    $routes->post('deletebooking/(:any)', 'CapacityController::deletebooking/$1');
    $routes->post('cancelbooking/(:any)', 'CapacityController::cancelbooking/$1');
    $routes->post('pecahbooking/(:any)', 'CapacityController::pecahbooking/$1');
    $routes->post('importbooking', 'CapacityController::importbooking');

    // order
    $routes->get('dataorder', 'CapacityController::order');
    $routes->get('dataorder/(:any)', 'CapacityController::OrderPerJarum/$1');
    $routes->post('inputOrder', 'CapacityController::inputOrder');
    $routes->post('importModel', 'CapacityController::importModel');

    // produksi
    $routes->get('dataproduksi', 'CapacityController::produksi');
});


//planning manager
$routes->group('/planning', ['filter' => 'planning'], function ($routes) {
});


//aps
$routes->group('/aps', ['filter' => 'aps'], function ($routes) {
});
