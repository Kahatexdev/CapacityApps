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
    $routes->post('importpecahbooking/(:any)', 'BookingController::importpecahbooking/$1');
    $routes->get('cancelBooking', 'BookingController::getCancelBooking');
    $routes->get('detailcancelbooking/(:any)/(:any)', 'BookingController::detailcancelbooking/$1/$2');
    $routes->post('uncancelbooking/(:any)', 'BookingController::uncancelbooking/$1');
    $routes->get('allbooking', 'BookingController::allbooking');
    $routes->post('transferQty', 'BookingController::transferQty');

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
    $routes->post('inputOrderManual', 'OrderController::inputOrderManual');
    $routes->post('importModel', 'OrderController::importModel');
    $routes->get('turunOrder', 'OrderController::getTurunOrder');
    $routes->get('detailturunorder/(:any)/(:any)', 'OrderController::detailturunorder/$1/$2');
    $routes->post('tampilPerdelivery', 'OrderController::tampilPerdelivery');
    $routes->get('detailPdk/(:any)/(:any)', 'OrderController::detailPdk/$1/$2');


    // produksi
    $routes->get('dataproduksi', 'ProduksiController::viewProduksi');
    $routes->get('dataprogress/:(any)', 'ProduksiController::progressData/$1');
    $routes->get('produksiareachart', 'ProduksiController::produksiAreaChart');
    $routes->get('detailproduksi/(:any)', 'ProduksiController::produksiPerArea/$1');
    $routes->post('importproduksi', 'ProduksiController::importproduksi');


    //summary
    $routes->post('summaryproduksi', 'ProduksiController::summaryProduksi');
    $routes->post('exportSummaryPerTod', 'SummaryController::excelSummaryPerTod');


    //summary produksi
    $routes->post('summaryProdPerTanggal', 'ProduksiController::summaryProdPerTanggal');
    $routes->post('exportSummaryPerTgl', 'SummaryController::excelSummaryPerTgl');

    //timter produksi
    $routes->post('timterProduksi', 'ProduksiController::timterProduksi');
    $routes->post('exportTimter', 'TimterController::excelTimter');

    // deffect
    $routes->get('datadeffect', 'DeffectController::datadeffect');
    $routes->post('inputKode', 'DeffectController::inputKode');
    $routes->post('viewDataBs', 'DeffectController::viewDataBs');


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

    $routes->post('getTypebyJarum', 'BookingController::getTypebyJarum');

    $routes->get('sales', 'SalesController::index');
    $routes->post('sales/position', 'SalesController::index');
    // $routes->get('exportsales', 'ExcelController::export');
    $routes->post('exportsales', 'SalesController::exportExcelByJarum');
    $routes->get('generatesales', 'SalesController::generateExcel');

    //target
    $routes->get('datatarget', 'BookingController::target');
    $routes->get('datatargetjarum/(:any)', 'BookingController::targetjarum/$1');
    $routes->post('edittarget', 'BookingController::edittarget');
    $routes->post('addtarget', 'BookingController::addtarget');
    $routes->post('deletetarget', 'BookingController::deletetarget');
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
    $routes->get('statusOrder', 'OrderController::statusOrder');
    $routes->get('statusOrder/(:any)', 'OrderController::statusOrder/$1');
    $routes->get('detailPdk/(:any)/(:any)', 'OrderController::detailPdk/$1/$2');
    $routes->get('detailModelPlanning/(:any)/(:any)', 'OrderController::detailModelPlanning/$1/$2');
    $routes->get('detailmodeljarum/(:any)/(:any)/(:any)', 'OrderController::detailmodeljarumPlan/$1/$2/$3');
    $routes->get('semuaOrder', 'OrderController::semuaOrderPlan');
    $routes->get('dataorderperjarum/(:any)', 'OrderController::DetailOrderPerJarumPlan/$1');
    $routes->get('dataorderperarea/(:any)', 'OrderController::DetailOrderPerAreaPlan/$1');
    $routes->post('updatedetailorder/(:any)', 'OrderController::updateorder/$1');
    $routes->post('updatedetailjarum/(:any)', 'OrderController::updateorderjarumplan/$1');
    $routes->post('deletedetailstyle/(:any)', 'OrderController::deletedetailstyle/$1');
    $routes->post('deletedetailorder/(:any)', 'OrderController::deletedetailorder/$1');
    $routes->post('deletedetailjarum/(:any)', 'OrderController::deletedetailmodeljarumplan/$1');
    $routes->post('assignareal', 'PlanningController::assignareal');
    $routes->post('splitarea', 'PlanningController::splitarea');
    $routes->post('editarea', 'PlanningController::editarea');
    $routes->post('assignarealall', 'PlanningController::assignarealall');
    $routes->post('recomendationarea', 'MesinController::recomendationarea');

    // mesin
    $routes->get('datamesin', 'MesinController::indexPlan');
    $routes->get('mesinPerJarum/(:any)', 'MesinController::mesinPerJarumPlan/$1');
    $routes->get('mesinperarea/(:any)', 'MesinController::mesinperareaPlan/$1');
    $routes->get('stockcylinder', 'MesinController::stockcylinderPlan');
    $routes->get('datamesinperjarum/(:any)/(:any)', 'MesinController::DetailMesinPerJarumPlan/$1/$2');
    $routes->get('datamesinperarea/(:any)', 'MesinController::DetailMesinPerAreaPlan/$1');
    $routes->post('capacityperarea/(:any)', 'MesinController::capacityperarea/$1');
    $routes->post('deletemesinareal/(:any)', 'MesinController::deletemesinarealPlan/$1');
    $routes->post('updatemesinperjarum/(:any)', 'MesinController::updatemesinperjarumPlan/$1');
    $routes->post('tambahmesinperarea', 'MesinController::inputmesinperareaPlan');
    $routes->post('tambahmesinperjarum', 'MesinController::inputmesinperjarumPlan');
    $routes->post('addcylinder', 'MesinController::inputcylinderPlan');
    $routes->post('editcylinder/(:any)', 'MesinController::editcylinderPlan/$1');
    $routes->post('deletecylinder/(:any)', 'MesinController::deletecylinderPlan/$1');
    $routes->get('allmachine', 'MesinController::allmachinePlan');

    //planning
    $routes->get('dataplanning', 'PlanningController::listplanning');
    $routes->get('detaillistplanning/(:any)', 'PlanningController::detaillistplanning/$1');
    $routes->post('pickmachine/(:any)', 'PlanningController::pickmachine/$1');
    $routes->post('Savemesin/(:any)', 'PlanningController::savemachine/$1');
    $routes->post('viewdetail/(:any)', 'PlanningController::viewdetail/$1');

    $routes->get('jalanmesin', 'PlanningController::jalanmesin');
    // $routes->get('jalanmesin/(:any)', 'PlanningController::jalanmesindetail/$1');
    $routes->get('jalanmesin/(:any)', 'PlanningController::monthlyMachine/$1');
    $routes->get('viewPlan/(:any)', 'PlanningJalanMcController::viewPlan/$1');

    $routes->post('exportPlanningJlMc/(:any)', 'PlanningJalanMcController::excelPlanningJlMc/$1');
    $routes->post('saveMonthlyMc', 'PlanningJalanMcController::saveMonthlyMc');


    // produksi
    $routes->get('dataproduksi', 'ProduksiController::viewProduksi');
    $routes->get('dataprogress', 'ProduksiController::progressData');
    $routes->get('produksiareachart', 'ProduksiController::produksiAreaChart');
    $routes->get('dataproduksi/(:any)', 'ProduksiController::produksiPerArea/$1');
    $routes->post('importproduksi', 'ProduksiController::importproduksi');
    $routes->get('produksi', 'ProduksiController::produksi');
    $routes->get('detailproduksi/(:any)', 'ProduksiController::produksiPerArea/$1');


    //summary
    $routes->post('summaryproduksi', 'ProduksiController::summaryProduksi');
    $routes->post('exportSummaryPerTod', 'SummaryController::excelSummaryPerTod');

    //summary produksi
    $routes->post('summaryProdPerTanggal', 'ProduksiController::summaryProdPerTanggal');
    $routes->post('exportSummaryPerTgl', 'SummaryController::excelSummaryPerTgl');

    //timter produksi
    $routes->post('timterProduksi', 'ProduksiController::timterProduksi');
    $routes->post('exportTimter', 'TimterController::excelTimter');

    // deffect
    $routes->get('datadeffect', 'DeffectController::datadeffect');
    $routes->post('inputKode', 'DeffectController::inputKode');
    $routes->post('viewDataBs', 'DeffectController::viewDataBs');
});


//aps
$routes->group('/aps', ['filter' => 'aps'], function ($routes) {

    // booking
    $routes->get('databooking', 'ApsController::booking');
    $routes->get('databooking/(:any)', 'ApsController::bookingPerJarum/$1');
    $routes->get('databookingbulan/(:any)', 'ApsController::bookingPerBulanJarum/$1');
    $routes->get('databookingbulantampil/(:any)/(:any)/(:any)', 'ApsController::bookingPerBulanJarumTampil/$1/$2/$3');
    $routes->get('detailbooking/(:any)', 'ApsController::detailbooking/$1');

    $routes->get('progressdetail/(:any)/(:any)', 'ApsController::progressdetail/$1/$2');
    //order
    $routes->get('', 'ApsController::index');
    $routes->get('dataorder', 'ApsController::orderPerArea');
    $routes->get('blmAdaArea', 'ApsController::orderBlmAdaAreal');
    $routes->get('orderPerjarum', 'ApsController::OrderPerJarum');
    $routes->get('orderPerArea', 'ApsController::orderPerArea');
    $routes->get('detailmodel/(:any)/(:any)', 'ApsController::detailModel/$1/$2');
    $routes->get('detailmodeljarum/(:any)/(:any)/(:any)', 'ApsController::detailmodeljarum/$1/$2/$3');
    $routes->get('semuaOrder', 'ApsController::semuaOrder');
    $routes->get('dataorderperjarum/(:any)', 'ApsController::DetailOrderPerJarum/$1');
    $routes->get('dataorderperarea/(:any)', 'ApsController::DetailOrderPerArea/$1');
    $routes->post('updatedetailorder/(:any)', 'OrderController::updateorder/$1');
    $routes->post('updatedetailjarum/(:any)', 'OrderController::updateorderjarum/$1');
    $routes->get('orderPerjarumBln', 'ApsController::orderPerJarumBln');



    // mesin
    $routes->get('datamesin', 'MesinController::mesinperareaAps');
    $routes->get('mesinPerJarum/(:any)', 'MesinController::mesinPerJarumPlan/$1');
    $routes->get('mesinperarea', 'MesinController::mesinperareaAps');
    $routes->get('stockcylinder', 'MesinController::stockcylinderPlan');
    $routes->get('datamesinperjarum/(:any)/(:any)', 'MesinController::DetailMesinPerJarumPlan/$1/$2');
    $routes->get('datamesinperarea/(:any)', 'MesinController::DetailMesinPerAreaPlan/$1');
    $routes->post('deletemesinareal/(:any)', 'MesinController::deletemesinarealPlan/$1');
    $routes->post('updatemesinperjarum/(:any)', 'MesinController::updatemesinperjarumPlan/$1');
    $routes->post('tambahmesinperarea', 'MesinController::inputmesinperareaPlan');
    $routes->post('addcylinder', 'MesinController::inputcylinderPlan');
    $routes->post('editcylinder/(:any)', 'MesinController::editcylinderPlan/$1');
    $routes->post('deletecylinder/(:any)', 'MesinController::deletecylinderPlan/$1');
    $routes->get('allmachine', 'MesinController::allmachinePlan');
    $routes->post('capacityperarea/(:any)', 'MesinController::capacityperarea/$1');

    //planning
    $routes->get('dataplanning', 'PlanningController::listplanningAps');
    $routes->get('detaillistplanning/(:any)', 'PlanningController::detaillistplanningAps/$1');
    $routes->post('pickmachine/(:any)', 'PlanningController::pickmachine/$1');
    $routes->post('Savemesin/(:any)', 'PlanningController::savemachine/$1');
    $routes->post('viewdetail/(:any)', 'PlanningController::viewdetailAps/$1');


    // produksi
    $routes->get('dataproduksi', 'ProduksiController::viewProduksi');
    $routes->get('dataprogress', 'ProduksiController::progressData');
    $routes->get('produksiareachart', 'ProduksiController::produksiAreaChart');
    $routes->get('dataproduksi/(:any)', 'ProduksiController::produksiPerArea/$1');
    $routes->post('importproduksi', 'ProduksiController::importproduksi');
    $routes->get('produksi', 'ProduksiController::produksi');
    $routes->get('detailproduksi/(:any)', 'ProduksiController::produksiPerArea/$1');

    //summary
    $routes->post('summaryproduksi', 'ProduksiController::summaryProduksi');
    $routes->post('exportSummaryPerTod', 'SummaryController::excelSummaryPerTod');

    //summary produksi
    $routes->post('summaryProdPerTanggal', 'ProduksiController::summaryProdPerTanggal');
    $routes->post('exportSummaryPerTgl', 'SummaryController::excelSummaryPerTgl');

    //timter produksi
    $routes->post('timterProduksi', 'ProduksiController::timterProduksi');
    $routes->post('exportTimter', 'TimterController::excelTimter');

    $routes->get('planningmesin', 'ApsController::planningmesin');
    $routes->post('fetch_jarum', 'ApsController::fetch_jarum');
    $routes->post('SimpanJudul', 'ApsController::saveplanningmesin');
    $routes->get('detailplnmc/(:any)', 'ApsController::detailplanmc/$1');
    $routes->get('fetchdetailorderarea', 'ApsController::fetchdetailorderarea');
    $routes->get('planningpage/(:any)', 'ApsController::planningpage/$1');
    $routes->post('getDataLibur', 'ApsController::getDataLibur');
    $routes->post('saveplanning', 'ApsController::saveplanning');
    $routes->get('getMesinByDate/(:any)', 'ApsController::getMesinByDate/$1');

    // deffect
    $routes->get('datadeffect', 'DeffectController::datadeffect');
    $routes->post('inputKode', 'DeffectController::inputKode');
    $routes->post('viewDataBs', 'DeffectController::viewDataBs');
});

// user
$routes->group('/user', ['filter' => 'user'], function ($routes) {
    $routes->get('', 'UserController::produksi');
    $routes->get('produksi', 'UserController::produksi');
    $routes->get('bssetting', 'UserController::bssetting');
    // $routes->post('importproduksi', 'ProduksiController::importproduksi');
    $routes->post('importproduksi', 'ProduksiController::importproduksinew');
    $routes->post('importbssetting', 'ProduksiController::importbssetting');
    $routes->get('viewModelPlusPacking/(:any)', 'ProduksiController::viewModelPlusPacking/$1');
    $routes->get('pluspacking', 'ProduksiController::pluspacking');
    $routes->post('inputpo', 'ProduksiController::updatepo');

    //summary
    $routes->post('summaryproduksi', 'ProduksiController::summaryProduksi');
    $routes->post('exportSummaryPerTod', 'SummaryController::excelSummaryPerTod');

    //summary produksi
    $routes->post('summaryProdPerTanggal', 'ProduksiController::summaryProdPerTanggal');
    $routes->post('exportSummaryPerTgl', 'SummaryController::excelSummaryPerTgl');

    //timter produksi
    $routes->post('timterProduksi', 'ProduksiController::timterProduksi');
    $routes->post('exportTimter', 'TimterController::excelTimter');
});

// sudo

$routes->group('/sudo', ['filter' => 'sudo', 'god'], function ($routes) {
    $routes->get('', 'GodController::index');
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
    $routes->post('importpecahbooking/(:any)', 'BookingController::importpecahbooking/$1');
    $routes->get('cancelBooking', 'BookingController::getCancelBooking');
    $routes->post('detailcancelbooking/(:any)/(:any)', 'BookingController::detailcancelbooking/$1/$2');
    $routes->post('uncancelbooking/(:any)', 'BookingController::uncancelbooking/$1');

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
    $routes->post('tampilPerdelivery', 'OrderController::tampilPerdelivery');
    $routes->get('smvimport', 'OrderController::smvimport');
    $routes->post('importsmv', 'OrderController::importsmv');
    $routes->get('sisa', 'OrderController::smvimport');
    $routes->post('updateSisa', 'GodController::updateSisa');
    $routes->get('detailPdk/(:any)/(:any)', 'OrderController::detailPdk/$1/$2');
    $routes->get('sisaOrder', 'OrderController::sisaOrder');
    $routes->get('sisaOrder/(:any)', 'OrderController::sisaOrderBuyer/$1');






    // produksi
    $routes->get('dataproduksi', 'ProduksiController::viewProduksi');
    $routes->get('produksi', 'ProduksiController::produksi');
    $routes->get('dataprogress', 'ProduksiController::progressData');
    $routes->get('produksiareachart', 'ProduksiController::produksiAreaChart');
    $routes->get('dataproduksi/(:any)', 'ProduksiController::produksiPerArea/$1');
    $routes->post('importproduksi', 'ProduksiController::importproduksinew');
    $routes->post('resetproduksi', 'ProduksiController::resetproduksi');
    $routes->post('resetproduksiarea', 'ProduksiController::resetproduksiarea');
    $routes->post('editproduksi', 'ProduksiController::editproduksi');
    $routes->get('detailproduksi/(:any)', 'ProduksiController::produksiPerArea/$1');

    $routes->post('summaryproduksi', 'ProduksiController::summaryProduksi');


    //summary
    $routes->post('summaryproduksi', 'ProduksiController::summaryProduksi');
    $routes->post('exportSummaryPerTod', 'SummaryController::excelSummaryPerTod');

    //summary produksi
    $routes->post('summaryProdPerTanggal', 'ProduksiController::summaryProdPerTanggal');
    $routes->post('exportSummaryPerTgl', 'SummaryController::excelSummaryPerTgl');

    //timter produksi
    $routes->post('timterProduksi', 'ProduksiController::timterProduksi');
    $routes->post('exportTimter', 'TimterController::excelTimter');


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

    $routes->post('kebutuhanMesinBooking', 'KebutuhanMesin::inputMesinBooking');

    $routes->post('getTypebyJarum', 'GodController::getTypebyJarum');

    $routes->get('sales', 'ExcelController::index');
    $routes->get('exportsales', 'ExcelController::export');

    //target
    $routes->get('datatarget', 'GodController::target');
    $routes->get('datatargetjarum/(:any)', 'GodController::targetjarum/$1');
    $routes->post('edittarget', 'GodController::edittarget');

    // deffect
    $routes->get('datadeffect', 'DeffectController::datadeffect');
    $routes->post('inputKode', 'DeffectController::inputKode');
    $routes->post('viewDataBs', 'DeffectController::viewDataBs');
    $routes->post('resetbspdk', 'DeffectController::resetbs');
    $routes->post('resetbsarea', 'DeffectController::resetbsarea');

    // usermanageement
    $routes->get('account', 'GodController::account');
    $routes->post('addaccount', 'GodController::addaccount');
    $routes->post('assignarea', 'GodController::assignarea');
    $routes->post('updateaccount/(:any)', 'GodController::updateaccount/$1');
    $routes->post('deleteaccount/(:any)', 'GodController::deleteaccount/$1');

    // chat
    $routes->get('chat', 'ChatController::pesan');
});

// ie
$routes->group('/ie', ['filter' => 'ie'], function ($routes) {
    $routes->get('', 'IeController::index');
    $routes->get('historysmv', 'IeController::historysmv');
    $routes->post('inputsmv', 'IeController::inputsmv');
    $routes->post('gethistory', 'IeController::gethistory');


    $routes->get('mesinperarea/(:any)', 'MesinController::mesinperareaPlan/$1');
    $routes->get('datamesin', 'MesinController::indexPlan');
    $routes->get('mesinPerJarum/(:any)', 'MesinController::mesinPerJarumPlan/$1');
    $routes->get('mesinperarea/(:any)', 'MesinController::mesinperareaPlan/$1');
    $routes->get('stockcylinder', 'MesinController::stockcylinderPlan');
    $routes->get('datamesinperjarum/(:any)/(:any)', 'MesinController::DetailMesinPerJarumPlan/$1/$2');
    $routes->get('datamesinperarea/(:any)', 'MesinController::DetailMesinPerAreaPlan/$1');
    $routes->post('capacityperarea/(:any)', 'MesinController::capacityperarea/$1');
    $routes->post('deletemesinareal/(:any)', 'MesinController::deletemesinarealPlan/$1');
    $routes->post('updatemesinperjarum/(:any)', 'MesinController::updatemesinperjarumPlan/$1');
    $routes->post('tambahmesinperarea', 'MesinController::inputmesinperareaPlan');
    $routes->post('tambahmesinperjarum', 'MesinController::inputmesinperjarumPlan');
    $routes->post('addcylinder', 'MesinController::inputcylinderPlan');
    $routes->post('editcylinder/(:any)', 'MesinController::editcylinderPlan/$1');
    $routes->post('deletecylinder/(:any)', 'MesinController::deletecylinderPlan/$1');
    $routes->get('allmachine', 'MesinController::allmachinePlan');

    $routes->get('updatesmv', 'IeController::updatesmv');
    $routes->post('importupdate', 'OrderController::importupdatesmv');
});
