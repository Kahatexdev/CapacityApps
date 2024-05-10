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
    $routes->get('cancelBooking', 'BookingController::getCancelBooking');
    $routes->post('detailcancelbooking/(:any)/(:any)', 'BookingController::detailcancelbooking/$1/$2');

    // order
    $routes->get('dataorder', 'OrderController::order');
    $routes->get('detailmodel/(:any)/(:any)', 'OrderController::detailModelCapacity/$1/$2');
    $routes->get('detailmodeljarum/(:any)/(:any)/(:any)', 'OrderController::detailmodeljarum/$1/$2/$3');
    $routes->get('semuaOrder', 'OrderController::semuaOrder');
    $routes->get('orderPerjarum', 'OrderController::OrderPerJarum');
    $routes->get('orderPerjarumBln', 'OrderController::orderPerJarumBln');
    $routes->get('belumImport', 'OrderController::belumImport');
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
    $routes->get('turunOrder', 'OrderController::getTurunOrder');
    $routes->post('detailturunorder/(:any)/(:any)', 'OrderController::detailturunorder/$1/$2');

    // produksi
    $routes->get('dataproduksi', 'ProduksiController::viewProduksi');
    $routes->get('dataproduksi/(:any)', 'ProduksiController::produksiPerArea/$1');
    $routes->post('importproduksi', 'ProduksiController::importproduksi');

    // mesin
    $routes->get('datamesin', 'MesinController::index');
    $routes->get('mesinPerJarum/(:any)', 'MesinController::mesinPerJarum/$1');
    $routes->get('mesinperarea/(:any)', 'MesinController::mesinperarea/$1');
    $routes->get('stockcylinder', 'MesinController::stockcylinder');
    $routes->get('datamesinperjarum/(:any)/(:any)', 'MesinController::DetailMesinPerJarum/$1/$2');
    $routes->get('datamesinperarea/(:any)', 'MesinController::DetailMesinPerArea/$1');
    $routes->post('deletemesinareal/(:any)', 'MesinController::deletemesinareal/$1');
    $routes->post('updatemesinperjarum/(:any)', 'MesinController::updatemesinperjarum/$1');
    $routes->post('tambahmesinperarea', 'MesinController::inputmesinperarea');
    $routes->post('addcylinder', 'MesinController::inputcylinder');
    $routes->post('editcylinder/(:any)', 'MesinController::editcylinder/$1');
    $routes->post('deletecylinder/(:any)', 'MesinController::deletecylinder/$1');
    $routes->get('allmachine', 'MesinController::allmachine');

    //calendar
    $routes->get('planningorder', 'CalendarController::planningorder');
    $routes->get('planningbooking', 'CalendarController::planningbooking');
    $routes->get('test', 'CalendarController::test');
    $routes->post('calendar/(:any)', 'CalendarController::planOrder/$1');
    $routes->post('planningbooking/(:any)', 'CalendarController::planBooking/$1');
    $routes->get('hapusLibur/(:any)', 'CalendarController::hapuslibur/$1');
    $routes->get('checkdate', 'Checkdate::index');
    $routes->post('inputLibur', 'CapacityController::inputLibur');
    $routes->get('detailplan/(:any)', 'CalendarController::detailPlanning/$1');
    $routes->get('detailbook/(:any)', 'CalendarController::detailPlanningbook/$1');
    $routes->get('cek', 'TestController::test');

    $routes->post('kebutuhanMesinBooking', 'KebutuhanMesin::inputMesinBooking');

    $routes->get('sales', 'ExportController::index');
    $routes->get('exportsales', 'ExportController::export');
});


//planning manager
$routes->group('/planning', ['filter' => 'planning'], function ($routes) {

    // booking
    $routes->get('databooking', 'BookingController::bookingPlan');
    $routes->get('databooking/(:any)', 'BookingController::bookingPerJarumPLan/$1');
    $routes->get('databookingbulan/(:any)', 'BookingController::bookingPerBulanJarumPlan/$1');
    $routes->get('databookingbulantampil/(:any)/(:any)/(:any)', 'BookingController::bookingPerBulanJarumTampilPlan/$1/$2/$3');
    $routes->get('detailbooking/(:any)', 'BookingController::detailbookingPlan/$1');

    //order
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

    // mesin
    $routes->get('datamesin', 'MesinController::indexPlan');
    $routes->get('mesinPerJarum/(:any)', 'MesinController::mesinPerJarum/$1');
    $routes->get('mesinperarea/(:any)', 'MesinController::mesinperareaPlan/$1');
    $routes->get('stockcylinder', 'MesinController::stockcylinder');
    $routes->get('datamesinperjarum/(:any)/(:any)', 'MesinController::DetailMesinPerJarumPlan/$1/$2');
    $routes->get('datamesinperarea/(:any)', 'MesinController::DetailMesinPerAreaPlan/$1');
    $routes->post('deletemesinareal/(:any)', 'MesinController::deletemesinarealPlan/$1');
    $routes->post('updatemesinperjarum/(:any)', 'MesinController::updatemesinperjarumPlan/$1');
    $routes->post('tambahmesinperarea', 'MesinController::inputmesinperareaPlan');
    $routes->post('addcylinder', 'MesinController::inputcylinder');
    $routes->post('editcylinder/(:any)', 'MesinController::editcylinder/$1');
    $routes->post('deletecylinder/(:any)', 'MesinController::deletecylinder/$1');
    $routes->get('allmachine', 'MesinController::allmachine');

    //planning
    $routes->get('dataplanning', 'PlanningController::listplanning');
    $routes->get('detaillistplanning/(:any)', 'PlanningController::detaillistplanning/$1');
    $routes->post('pickmachine/(:any)/(:any)', 'PlanningController::pickmachine/$1/$2');
    $routes->post('Savemesin/(:any)', 'PlanningController::savemachine/$1');
    $routes->post('viewdetail/(:any)', 'PlanningController::viewdetail/$1');
});


//aps
$routes->group('/aps', ['filter' => 'aps'], function ($routes) {
});

// user
$routes->group('/user', ['filter' => 'user'], function ($routes) {
    $routes->get('', 'UserController::index');
    $routes->get('produksi', 'ProduksiController::produksi');
    $routes->get('importProduksi', 'ProduksiController::importProduksi');
});
