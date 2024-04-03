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
    $routes->get('databookingbulan/(:any)', 'BookingController::bookingPerBulanJarum/$1');
    $routes->get('databookingbulantampil/(:any)/(:any)/(:any)', 'BookingController::bookingPerBulanJarumTampil/$1/$2/$3');
    $routes->get('detailbooking/(:any)', 'BookingController::detailbooking/$1');
    $routes->post('inputbooking', 'BookingController::inputbooking');
    $routes->post('updatebooking/(:any)', 'BookingController::updatebooking/$1');
    $routes->post('deletebooking/(:any)', 'BookingController::deletebooking/$1');
    $routes->post('cancelbooking/(:any)', 'BookingController::cancelbooking/$1');
    $routes->post('pecahbooking/(:any)', 'BookingController::pecahbooking/$1');
    $routes->post('importbooking', 'BookingController::importbooking');

    // order
    $routes->get('dataorder', 'OrderController::order');
    $routes->get('detailmodel/(:any)/(:any)', 'OrderController::detailModelCapacity/$1/$2');
    $routes->get('detailmodeljarum/(:any)/(:any)/(:any)', 'OrderController::detailmodeljarum/$1/$2/$3');
    $routes->get('semuaOrder', 'OrderController::semuaOrder');
    $routes->get('orderPerjarum', 'OrderController::OrderPerJarum');
    $routes->get('orderPerjarumBln', 'OrderController::orderPerJarumBln');
    $routes->get('dataorderperjarum/(:any)', 'OrderController::DetailOrderPerJarum/$1');
    $routes->get('dataorderperjarumbln/(:any)', 'OrderController::DetailOrderPerJarumBln/$1');
    $routes->get('dataorderperjarumblndetail/(:any)/(:any)/(:any)', 'OrderController::DetailOrderPerJarumBlnDetail/$1/$2/$3');
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
    $routes->get('Calendar', 'CalendarController::index');
    $routes->get('test', 'CalendarController::test');
    $routes->post('calendar/(:any)', 'CalendarController::calendar/$1');
    $routes->get('hapusLibur/(:any)', 'CalendarController::hapuslibur/$1');
    $routes->get('checkdate', 'Checkdate::index');
    $routes->post('inputLibur', 'CapacityController::inputLibur');
    $routes->post('generate', 'CalendarController::generatePlanning');
    $routes->get('cek', 'TestController::test');
});


//planning manager
$routes->group('/planning', ['filter' => 'planning'], function ($routes) {
    $routes->get('', 'PlanningController::index');
    $routes->get('dataorder', 'PlanningController::order');
    $routes->get('blmAdaArea', 'OrderController::orderBlmAdaAreal');
    $routes->get('orderPerjarum', 'OrderController::OrderPerJarumPlan');
    $routes->get('orderPerArea', 'OrderController::orderPerAreaPlan');
    $routes->get('detailModelPlanning/(:any)/(:any)', 'OrderController::detailModelPlanning/$1/$2');
    $routes->get('detailmodeljarum/(:any)/(:any)/(:any)', 'OrderController::detailmodeljarum/$1/$2/$3');
    $routes->get('semuaOrder', 'OrderController::semuaOrderPlan');
    $routes->get('dataorderperjarum/(:any)', 'OrderController::DetailOrderPerJarumPlan/$1');
    $routes->get('dataorderperarea/(:any)', 'OrderController::DetailOrderPerAreaPlan/$1');
    $routes->post('updatedetailorder/(:any)', 'OrderController::updateorder/$1');
    $routes->post('updatedetailjarum/(:any)', 'OrderController::updateorderjarum/$1');
    $routes->post('deletedetailstyle/(:any)', 'OrderController::deletedetailstyle/$1');
    $routes->post('deletedetailorder/(:any)', 'OrderController::deletedetailorder/$1');
    $routes->post('deletedetailjarum/(:any)', 'OrderController::deletedetailmodeljarum/$1');
    $routes->post('assignareal', 'PlanningController::assignareal');
    $routes->post('assignarealall', 'PlanningController::assignarealall');
});


//aps
$routes->group('/aps', ['filter' => 'aps'], function ($routes) {
});
