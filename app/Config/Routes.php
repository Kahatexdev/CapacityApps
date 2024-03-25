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
    $routes->get('detailmodel/(:any)/(:any)', 'CapacityController::detailmodel/$1/$2');  
    $routes->get('detailmodeljarum/(:any)/(:any)/(:any)', 'CapacityController::detailmodeljarum/$1/$2/$3');  
    $routes->get('semuaOrder', 'CapacityController::semuaOrder');
    $routes->get('orderPerjarum', 'CapacityController::OrderPerJarum');
    $routes->get('dataorderperjarum/(:any)', 'CapacityController::DetailOrderPerJarum/$1');
    $routes->post('updatedetailorder/(:any)', 'CapacityController::updateorder/$1');
    $routes->post('updatedetailjarum/(:any)', 'CapacityController::updateorderjarum/$1');
    $routes->post('deletedetailstyle/(:any)', 'CapacityController::deletedetailstyle/$1');
    $routes->post('deletedetailorder/(:any)', 'CapacityController::deletedetailorder/$1');
    $routes->post('deletedetailjarum/(:any)', 'CapacityController::deletedetailmodeljarum/$1');
    $routes->post('inputOrder', 'CapacityController::inputOrder');
    $routes->post('importModel', 'CapacityController::importModel');

    // produksi
    $routes->get('dataproduksi', 'CapacityController::produksi');

    // mesin
    $routes->get('datamesin', 'CapacityController::datamesin');
    $routes->get('mesinPerJarum', 'CapacityController::mesinPerJarum');
    $routes->get('mesinperarea', 'CapacityController::mesinperarea');
    $routes->get('datamesinperjarum/(:any)', 'CapacityController::DetailMesinPerJarum/$1');
    $routes->post('deletemesinareal/(:any)', 'CapacityController::deletemesinareal/$1');
    $routes->post('updatemesinperjarum/(:any)', 'CapacityController::updatemesinperjarum/$1');
    $routes->post('tambahmesinperarea', 'CapacityController::inputmesinperarea');

});


//planning manager
$routes->group('/planning', ['filter' => 'planning'], function ($routes) {
});


//aps
$routes->group('/aps', ['filter' => 'aps'], function ($routes) {
});
