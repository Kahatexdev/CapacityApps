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
    $routes->get('databooking', 'BookingController::booking');
    $routes->get('databooking/(:any)', 'BookingController::bookingPerJarum/$1');
    $routes->get('detailbooking/(:any)', 'BookingController::detailbooking/$1');
    $routes->post('inputbooking', 'BookingController::inputbooking');
    $routes->post('updatebooking/(:any)', 'BookingController::updatebooking/$1');
    $routes->post('deletebooking/(:any)', 'BookingController::deletebooking/$1');
    $routes->post('cancelbooking/(:any)', 'BookingController::cancelbooking/$1');
    $routes->post('pecahbooking/(:any)', 'BookingController::pecahbooking/$1');
    $routes->post('importbooking', 'BookingController::importbooking');

    // order
    $routes->get('dataorder', 'OrderController::order');
    $routes->get('detailmodel/(:any)/(:any)', 'OrderController::detailmodel/$1/$2');
    $routes->get('detailmodeljarum/(:any)/(:any)/(:any)', 'OrderController::detailmodeljarum/$1/$2/$3');
    $routes->get('semuaOrder', 'OrderController::semuaOrder');
    $routes->get('orderPerjarum', 'OrderController::OrderPerJarum');
    $routes->get('dataorderperjarum/(:any)', 'OrderController::DetailOrderPerJarum/$1');
    $routes->post('updatedetailorder/(:any)', 'OrderController::updateorder/$1');
    $routes->post('updatedetailjarum/(:any)', 'OrderController::updateorderjarum/$1');
    $routes->post('deletedetailstyle/(:any)', 'OrderController::deletedetailstyle/$1');
    $routes->post('deletedetailorder/(:any)', 'OrderController::deletedetailorder/$1');
    $routes->post('deletedetailjarum/(:any)', 'OrderController::deletedetailmodeljarum/$1');
    $routes->post('inputOrder', 'OrderController::inputOrder');
    $routes->post('importModel', 'OrderController::importModel');

    // produksi
    $routes->get('dataproduksi', 'ProduksiController::produksi');
    $routes->get('dataproduksi', 'ProduksiController::produksi');
    $routes->get('dataproduksi/(:any)', 'ProduksiController::produksiPerArea/$1');
    $routes->post('importproduksi', 'ProduksiController::importproduksi');

    // mesin
    $routes->get('datamesin', 'MesinController::index');
    $routes->get('mesinPerJarum', 'MesinController::mesinPerJarum');
    $routes->get('mesinperarea', 'MesinController::mesinperarea');
    $routes->get('datamesinperjarum/(:any)', 'MesinController::DetailMesinPerJarum/$1');
    $routes->post('deletemesinareal/(:any)', 'MesinController::deletemesinareal/$1');
    $routes->post('updatemesinperjarum/(:any)', 'MesinController::updatemesinperjarum/$1');
    $routes->post('tambahmesinperarea', 'MesinController::inputmesinperarea');

    //calendar
    $routes->get('checkdate', 'Checkdate::generateWeeklyRanges');
    $routes->get('Calendar', 'CalendarController::index');

});


//planning manager
$routes->group('/planning', ['filter' => 'planning'], function ($routes) {
});


//aps
$routes->group('/aps', ['filter' => 'aps'], function ($routes) {
});
