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

// chart
$routes->get('chart/getProductionData', 'ProduksiController::getProductionData');
$routes->get('chart/getBsData', 'ProduksiController::getBsData');
$routes->get('chart/getBsMesin', 'DeffectController::getBsMesin');
$routes->get('chart/dashboardData', 'GodController::dashboardData');
$routes->get('chart/getDailyProd', 'GodController::getDailyProd');
$routes->get('exportProd', 'ExcelController::exportProd');


// API ROUTES
$routes->group(
    '/api',
    function ($routes) {
        $routes->get('bsKaryawan/(:any)', 'ApiController::bsKaryawan/$1');
        $routes->get('bsPeriode/(:any)', 'ApiController::bsPeriode/$1');
        $routes->get('bsDaily/(:any)', 'ApiController::bsDaily/$1');

        $routes->get('prodBsDaily/(:any)/(:any)', 'ApiController::prodBsDaily/$1/$2');

        // material
        $routes->get('orderMaterial/(:any)/(:any)', 'ApiController::orderMaterial/$1/$2');
        $routes->get('reqstartmc/(:any)', 'ApiController::reqstartmc/$1');
        $routes->get('getDataForPPH/(:any)/(:any)', 'ApiController::getDataForPPH/$1/$2');
        $routes->get('getDataPerinisial/(:any)/(:any)/(:any)', 'ApiController::getDataPerinisial/$1/$2/$3');
        $routes->get('getDataArea', 'ApiController::getArea');
        $routes->get('getPPhPerhari/(:any)/(:any)', 'ApiController::getPPhPerhari/$1/$2');
        $routes->get('getHariLibur', 'ApiController::getHariLibur');

        $routes->get('getPlanMesin', 'ApiController::getPlanMesin');
        $routes->get('exportPlanningJlMc/(:any)', 'PlanningJalanMcController::excelPlanningJlMc/$1');
        $routes->get('filterRetur/(:any)', 'ReturController::dataRetur/$1');
        $routes->get('retur/(:any)', 'ReturController::index/$1');
        $routes->get('filterRetur/(:any)', 'ReturController::dataRetur/$1');
        $routes->post('pengajuanRetur/(:any)', 'ReturController::pengajuanRetur/$1');
        $routes->get('getKategoriRetur', 'ReturController::getKategoriRetur');
        $routes->get('getKodeWarnaWarnaByItemType', 'ReturController::getKodeWarnaWarnaByItemType');
        $routes->get('getApsPerStyles', 'ApiController::getApsPerStyles');
        $routes->get('getApsPerStyle/(:any)/(:any)/(:any)', 'ApiController::getApsPerStyle/$1/$2/$3');
        $routes->get('getApsPerStyleById/(:any)', 'ApiController::getApsPerStyleById/$1');
        $routes->get('getQtyPcsByAreaByStyle/(:any)', 'ApiController::getQtyPcsByAreaByStyle/$1');
        $routes->get('getMasterModel', 'ApiController::getMasterModel');
        $routes->get('getInisialByModel', 'ApiController::getInisialByModel');
        $routes->get('getIdApsByModelInisial', 'ApiController::getApsPerStyleByInisial');
        $routes->get('getDeliv/(:any)', 'ApiController::getDeliv/$1');

        $routes->get('getQtyOrder', 'ApiController::getQtyOrder');
        $routes->get('getDataBuyer', 'ApiController::getDataBuyer');
        $routes->get('getDeliveryAwalAkhir', 'ApiController::getDeliveryAwalAkhir');
        $routes->get('searchApsPerStyleByMastermodel', 'ApiController::searchApsPerStyleByMastermodel');
        $routes->get('getStartMc/(:any)', 'ApiController::getStartMc/$1');
        $routes->get('getPlanStyle', 'ApsController::getPlanStyle/$1');

        // pengaduan
        $routes->get('pengaduan/(:any)/(:any)', 'PengaduanController::Apipengaduan/$1/$2');
        $routes->post('pengaduan/create', 'PengaduanController::Apicreate');
        $routes->post('pengaduan/reply/(:num)', 'PengaduanController::Apireply/$1');
        $routes->get('getNotif/(:any)', 'ApiController::getNotifAduan/$1');
    }
);


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
    $routes->post('exportDataBooking', 'ExcelController::exportDataBooking');

    // order
    $routes->get('dataorder', 'OrderController::order');
    $routes->get('detailmodel/(:any)/(:any)', 'OrderController::detailModelCapacity/$1/$2');
    $routes->get('detailmodeljarum/(:any)/(:any)/(:any)', 'OrderController::detailmodeljarum/$1/$2/$3');
    $routes->get('semuaOrder', 'OrderController::semuaOrder');
    $routes->get('orderPerjarum', 'OrderController::OrderPerJarum');
    $routes->get('orderPerjarumBln', 'OrderController::orderPerJarumBln');
    $routes->get('belumImport', 'OrderController::belumImport');
    $routes->get('statusOrder', 'OrderController::statusOrder');
    $routes->get('statusorder/(:any)', 'OrderController::statusOrderArea/$1');
    $routes->post('statusorder/(:any)', 'OrderController::statusOrderArea/$1');
    $routes->get('progressdetail/(:any)/(:any)', 'ApsController::progressdetail/$1/$2');
    $routes->get('dataorderperjarum/(:any)', 'OrderController::DetailOrderPerJarum/$1');
    $routes->get('dataorderperjarumbln/(:any)', 'OrderController::DetailOrderPerJarumBln/$1');
    $routes->get('dataorderperjarumblndetail/(:any)/(:any)/(:any)', 'OrderController::orderPerMonth/$1/$2/$3');
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
    $routes->post('reviseorder', 'OrderController::reviseorder');
    $routes->post('assignareal', 'PlanningController::assignareal');
    $routes->post('cancelorder/(:any)', 'OrderController::cancelOrder/$1');
    $routes->get('cancelOrder', 'OrderController::getCancelOrder');
    $routes->get('detailCancelOrder/(:any)', 'OrderController::detailCancelOrder/$1');
    $routes->get('sisaOrder', 'OrderController::sisaOrder');
    $routes->get('sisaOrder/(:any)', 'OrderController::sisaOrderBuyer/$1');
    $routes->post('sisaOrder/(:any)', 'OrderController::sisaOrderBuyer/$1');
    $routes->post('excelSisaOrderBuyer/(:any)', 'ExcelController::excelSisaOrderBuyer/$1');
    $routes->get('sisaOrderArea', 'OrderController::sisaOrderArea');
    $routes->get('sisaOrderArea/(:any)', 'OrderController::detailSisaOrderArea/$1');
    $routes->post('sisaOrderArea/(:any)', 'OrderController::detailSisaOrderArea/$1');
    $routes->post('excelSisaOrderArea', 'ExcelController::excelSisaOrderArea');
    $routes->post('inputhistoryrevise/(:any)', 'OrderController::inputHistory/$1');
    $routes->post('saveRepeat', 'orderController::saveRepeat');
    $routes->post('excelSisaOrderAllArea', 'ExcelController::excelSisaOrderAllArea');
    $routes->post('importStokOrder', 'OrderController::importStokOrder');


    // produksi
    $routes->get('dataproduksi', 'ProduksiController::viewProduksi');
    $routes->get('dataprogress/:(any)', 'ProduksiController::progressData/$1');
    $routes->get('produksiareachart', 'ProduksiController::produksiAreaChart');
    $routes->get('detailproduksi/(:any)', 'ProduksiController::produksiPerArea/$1');
    $routes->post('importproduksi', 'ProduksiController::importproduksi');
    // planning

    $routes->get('planningmesin', 'ApsController::planningmesin');
    $routes->post('fetch_jarum', 'ApsController::fetch_jarum');
    $routes->post('SimpanJudul', 'ApsController::saveplanningmesin');
    $routes->get('detailplnmc/(:any)', 'ApsController::detailplanmc/$1');
    $routes->post('excelplnmc/(:any)', 'ExcelController::excelPlnMc/$1');
    $routes->get('fetchdetailorderarea', 'ApsController::fetchdetailorderarea');
    $routes->get('planningpage/(:any)/(:any)', 'ApsController::planningpage/$1/$2');
    $routes->post('getDataLibur', 'ApsController::getDataLibur');
    $routes->post('saveplanning', 'ApsController::saveplanning');
    $routes->get('getMesinByDate/(:any)', 'ApsController::getMesinByDate/$1');
    $routes->get('kalenderMesin/(:any)', 'ApsController::kalenderMesin/$1');
    $routes->post('deleteplanmesin', 'ApsController::deleteplanmesin');
    $routes->post('stopPlanning/(:any)', 'ApsController::stopPlanning/$1');
    $routes->get('detailplanstop/(:any)', 'ApsController::detailplanstop/$1');
    $routes->post('activePlanning/(:any)', 'ApsController::activePlanning/$1');
    $routes->get('getPlanStyle', 'ApsController::getPlanStyle');
    $routes->post('savePlanStyle', 'ApsController::savePlanStyle');
    $routes->get('cekBahanBaku/(:num)/(:num)', 'MaterialController::cekBahanBaku/$1/$2');
    $routes->get('cekStok', 'MaterialController::cekStok');
    $routes->get('cekStokStyle', 'MaterialController::cekStokStyle');
    $routes->get('summaryPlanner/(:any)', 'SummaryController::summaryPlanner/$1');
    $routes->get('summaryStopPlanner/(:any)', 'SummaryController::summaryStopPlanner/$1');

    //summary
    $routes->post('summaryproduksi', 'ProduksiController::summaryProduksi');
    $routes->post('exportSummaryPerTod', 'SummaryController::excelSummaryPerTod');


    //summary produksi
    $routes->post('summaryProdPerTanggal', 'ProduksiController::summaryProdPerTanggal');
    $routes->post('exportSummaryPerTgl', 'SummaryController::excelSummaryPerTgl');

    //timter produksi
    $routes->post('timterProduksi', 'ProduksiController::timterProduksi');
    $routes->post('exportTimter', 'TimterController::excelTimter');

    //summary bs mc
    $routes->post('exportSummaryBs', 'SummaryController::excelSummaryBs');

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

    $routes->get('sales', 'SalesController::index2');
    // $routes->get('sales2', 'SalesController::index2');
    $routes->post('updateQtyExport', 'SalesController::updateQtyActualExport');
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

    // pengaduan
    $routes->get('pengaduan', 'PengaduanController::index');
    $routes->post('pengaduan/create', 'PengaduanController::create');
    $routes->post('pengaduan/reply/(:num)', 'PengaduanController::reply/$1');
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
    $routes->get('startStopMcByPdk', 'PlanningController::startStopMcByPdk');
    $routes->get('blmAdaArea', 'OrderController::orderBlmAdaAreal');
    $routes->get('orderPerjarum', 'OrderController::OrderPerJarumPlan');
    $routes->get('orderPerArea', 'OrderController::orderPerAreaPlan');
    $routes->get('statusOrder', 'OrderController::statusOrder');
    $routes->get('statusorder/(:any)', 'OrderController::statusOrderArea/$1');
    $routes->post('statusorder/(:any)', 'OrderController::statusOrderArea/$1');
    $routes->get('progressdetail/(:any)/(:any)', 'ApsController::progressdetail/$1/$2');
    $routes->get('detailPdk/(:any)/(:any)', 'OrderController::detailPdk/$1/$2');
    $routes->get('detailModelPlanning/(:any)/(:any)', 'OrderController::detailModelPlanning/$1/$2');
    $routes->get('detailmodeljarum/(:any)/(:any)/(:any)', 'OrderController::detailmodeljarumPlan/$1/$2/$3');
    $routes->get('semuaOrder', 'OrderController::semuaOrder');
    $routes->post('exportDataOrder', 'ExcelController::exportDataOrder');
    $routes->post('exportDataBooking', 'ExcelController::exportDataBooking');
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
    $routes->post('editqtyarea', 'PlanningController::editqtyarea');
    $routes->post('assignarealall', 'PlanningController::assignarealall');
    $routes->post('recomendationarea', 'MesinController::recomendationarea');
    $routes->post('tampilPerdelivery', 'OrderController::tampilPerdelivery');
    $routes->get('orderPerbulan', 'OrderController::orderPerbulan');
    $routes->get('orderPerMonth/(:any)/(:any)', 'OrderController::orderPerMonth/$1/$2');
    $routes->post('inputhistoryrevise/(:any)', 'OrderController::inputHistory/$1');
    $routes->get('pengajuanspk2', 'OrderController::spk2');
    $routes->post('approveSpk2', 'OrderController::approveSpk2');
    $routes->post('rejectSpk2', 'OrderController::rejectSpk2');
    $routes->get('sisaOrder', 'OrderController::sisaOrder');
    $routes->get('sisaOrder/(:any)', 'OrderController::sisaOrderBuyer/$1');
    $routes->post('sisaOrder/(:any)', 'OrderController::sisaOrderBuyer/$1');
    $routes->post('excelSisaOrderBuyer/(:any)', 'ExcelController::excelSisaOrderBuyer/$1');
    $routes->get('sisaOrderArea', 'OrderController::sisaOrderArea');
    $routes->get('sisaOrderArea/(:any)', 'OrderController::detailSisaOrderArea/$1');
    $routes->post('sisaOrderArea/(:any)', 'OrderController::detailSisaOrderArea/$1');
    $routes->post('excelSisaOrderArea', 'ExcelController::excelSisaOrderArea');
    $routes->post('excelSisaOrderAllArea', 'ExcelController::excelSisaOrderAllArea');
    $routes->post('saveRepeat', 'OrderController::saveRepeat');
    $routes->get('flowProses', 'OrderController::flowProses');
    $routes->post('semuaOrder/importFlowproses', 'OrderController::importFlowproses');

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
    $routes->post('updateMonthlyMc', 'PlanningJalanMcController::updateMonthlyMc');
    $routes->get('planningmesin', 'ApsController::planningmesin');
    $routes->get('detailplnmc/(:any)', 'ApsController::detailplanmc/$1');
    $routes->get('kalenderMesin/(:any)', 'ApsController::kalenderMesin/$1');
    $routes->get('planningpage/(:any)/(:any)', 'ApsController::planningpage/$1/$2');





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
    $routes->get('summaryPlanner/(:any)', 'SummaryController::summaryPlanner/$1');

    //summary bs mc
    $routes->post('exportSummaryBs', 'SummaryController::excelSummaryBs');

    // deffect
    $routes->get('datadeffect', 'DeffectController::datadeffect');
    $routes->post('inputKode', 'DeffectController::inputKode');
    $routes->post('viewDataBs', 'DeffectController::viewDataBs');


    //bahanbakyu
    $routes->get('stockbb', 'MaterialController::stockbb');
    $routes->get('filterstockbahanbaku', 'MaterialController::filterStockBahanBaku');
    $routes->get('statusbahanbaku', 'MaterialController::statusbahanbaku');
    $routes->get('filterstatusbahanbaku/(:any)', 'MaterialController::filterstatusbahanbaku/$1');
    $routes->get('warehouse/reportPoBenang', 'MaterialController::reportPoBenang');
    $routes->get('warehouse/filterPoBenang', 'MaterialController::filterPoBenang');
    $routes->get('warehouse/exportPoBenang', 'ExcelController::exportPoBenang');
    $routes->get('warehouse/reportDatangBenang', 'MaterialController::reportDatangBenang');
    $routes->get('warehouse/filterDatangBenang', 'MaterialController::filterDatangBenang');
    $routes->get('warehouse/exportDatangBenang', 'ExcelController::exportDatangBenang');
    $routes->get('warehouse/exportExcel', 'ExcelController::excelStockMaterial');
    $routes->get('warehouse/reportPengiriman', 'MaterialController::reportPengiriman');
    $routes->get('warehouse/filterPengiriman', 'MaterialController::filterPengiriman');
    $routes->get('warehouse/exportPengiriman', 'ExcelController::exportPengiriman');
    $routes->get('warehouse/reportGlobal', 'MaterialController::reportGlobal');
    $routes->get('warehouse/filterReportGlobal', 'MaterialController::filterReportGlobal');
    $routes->get('warehouse/reportGlobalNylon', 'MaterialController::reportGlobalNylon');
    $routes->get('warehouse/filterReportNylon', 'MaterialController::filterReportGlobal');
    $routes->get('warehouse/exportGlobalReport', 'ExcelController::exportGlobalReport');
    $routes->get('warehouse/reportGlobalStockBenang', 'MaterialController::reportGlobalStockBenang');
    $routes->get('warehouse/filterReportGlobalBenang', 'MaterialController::filterReportGlobal');
    $routes->get('warehouse/exportReportGlobalBenang', 'ExcelController::exportReportGlobalBenang');
    $routes->get('warehouse/reportSisaPakaiBenang', 'MaterialController::reportSisaPakaiBenang');
    $routes->get('warehouse/filterSisaPakaiBenang', 'MaterialController::filterSisaPakai');
    $routes->get('warehouse/exportReportSisaPakaiBenang', 'ExcelController::exportReportSisaPakai');
    $routes->get('warehouse/reportSisaPakaiNylon', 'MaterialController::reportSisaPakaiNylon');
    $routes->get('warehouse/filterSisaPakaiNylon', 'MaterialController::filterSisaPakai');
    $routes->get('warehouse/exportReportSisaPakaiNylon', 'ExcelController::exportReportSisaPakai');
    $routes->get('warehouse/reportSisaPakaiSpandex', 'MaterialController::reportSisaPakaiSpandex');
    $routes->get('warehouse/filterSisaPakaiSpandex', 'MaterialController::filterSisaPakai');
    $routes->get('warehouse/exportReportSisaPakaiSpandex', 'ExcelController::exportReportSisaPakai');
    $routes->get('warehouse/reportSisaPakaiKaret', 'MaterialController::reportSisaPakaiKaret');
    $routes->get('warehouse/filterSisaPakaiKaret', 'MaterialController::filterSisaPakai');
    $routes->get('warehouse/exportReportSisaPakaiKaret', 'ExcelController::exportReportSisaPakai');
    $routes->get('warehouse/historyPindahOrder', 'MaterialController::historyPindahOrder');
    $routes->get('warehouse/exportHistoryPindahOrder', 'ExcelController::exportHistoryPindahOrder');
    $routes->get('pemesanan/historyPinjamOrder', 'MaterialController::HistoryPinjamOrder');
    $routes->get('pemesanan/exportHistoryPinjamOrder', 'ExcelController::exportHistoryPinjamOrder');
    $routes->get('warehouse/reportSisaDatangBenang', 'MaterialController::reportSisaDatangBenang');
    $routes->get('warehouse/exportReportSisaDatangBenang', 'ExcelController::exportReportSisaDatangBenang');
    $routes->get('warehouse/reportSisaDatangNylon', 'MaterialController::reportSisaDatangNylon');
    $routes->get('warehouse/exportReportSisaDatangNylon', 'ExcelController::exportReportSisaDatangNylon');
    $routes->get('warehouse/reportSisaDatangSpandex', 'MaterialController::reportSisaDatangSpandex');
    $routes->get('warehouse/exportReportSisaDatangSpandex', 'ExcelController::exportReportSisaDatangSpandex');
    $routes->get('warehouse/reportSisaDatangKaret', 'MaterialController::reportSisaDatangKaret');
    $routes->get('warehouse/exportReportSisaDatangKaret', 'ExcelController::exportReportSisaDatangKaret');
    $routes->get('warehouse/reportBenangMingguan', 'MaterialController::reportBenangMingguan');
    $routes->get('warehouse/filterBenangMingguan', 'MaterialController::filterBenangMingguan');
    $routes->get('warehouse/exportReportBenangMingguan', 'ExcelController::exportReportBenang');
    $routes->get('warehouse/reportBenangBulanan', 'MaterialController::reportBenangBulanan');
    $routes->get('warehouse/filterBenangBulanan', 'MaterialController::filterBenangBulanan');
    $routes->get('warehouse/exportReportBenangBulanan', 'ExcelController::exportReportBenang');

    //pph
    $routes->get('pph/(:any)', 'MaterialController::pph/$1');
    $routes->get('filterpph/(:any)', 'MaterialController::filterPph/$1');
    $routes->get('tampilPerStyle/(:any)', 'MaterialController::tampilPerStyle/$1');
    $routes->get('pphinisial/(:any)', 'MaterialController::pphinisial/$1');
    $routes->get('pphPerhari/(:any)', 'MaterialController::pphPerhari/$1');
    $routes->get('getDataPerhari/(:any)', 'MaterialController::getDataPerhari/$1');
    $routes->get('excelPPHNomodel/(:any)/(:any)', 'ExcelPPHController::excelPPHNomodel/$1/$2');
    $routes->get('excelPPHInisial/(:any)/(:any)', 'ExcelPPHController::excelPPHInisial/$1/$2');
    $routes->get('excelPPHDays/(:any)/(:any)', 'ExcelPPHController::excelPPHDays/$1/$2');

    //retur
    $routes->get('retur', 'ReturController::listdataReturArea');
    $routes->get('exportExcelRetur/(:any)', 'ExcelController::exportExcelRetur/$1');

    //po tambahan
    $routes->get('po_tambahan/(:any)', 'MaterialController::poTambahan/$1');
    $routes->get('poTambahanDetail/(:any)/(:any)', 'MaterialController::poTambahanDetail/$1/$2');
    $routes->get('filter_list_potambahan/(:any)', 'MaterialController::filterPoTambahan/$1');
    $routes->get('generate_po_tambahan', 'PdfController::generatePoTambahan');
    $routes->get('generate_excel_po_tambahan', 'ExcelController::generatePoTambahan');

    //summary pakai
    $routes->get('jatah_bahan_baku', 'MaterialController::jatahBahanBaku');

    // pengaduan
    $routes->get('pengaduan', 'PengaduanController::index');
    $routes->post('pengaduan/create', 'PengaduanController::create');
    $routes->post('pengaduan/reply/(:num)', 'PengaduanController::reply/$1');
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
    $routes->get('dataorder', 'ApsController::orderPerAreaAps');
    $routes->get('blmAdaArea', 'ApsController::orderBlmAdaAreal');
    $routes->get('orderPerjarum', 'ApsController::OrderPerJarum');
    $routes->get('orderPerArea', 'ApsController::orderPerArea');
    $routes->get('detailmodel/(:any)/(:any)', 'ApsController::detailModel/$1/$2');
    $routes->get('detailmodeljarum/(:any)/(:any)/(:any)', 'ApsController::detailmodeljarum/$1/$2/$3');
    $routes->get('semuaOrder', 'ApsController::semuaOrder');
    $routes->get('dataorderperjarum/(:any)', 'ApsController::DetailOrderPerJarum/$1');
    $routes->get('dataorderperarea/(:any)', 'ApsController::DetailOrderPerAreaAps/$1');
    $routes->post('updatedetailorder/(:any)', 'OrderController::updateorder/$1');
    $routes->post('updatedetailjarum/(:any)', 'OrderController::updateorderjarum/$1');
    $routes->get('orderPerjarumBln', 'ApsController::orderPerJarumBln');
    $routes->get('detailPdkAps/(:any)/(:any)', 'OrderController::detailPdkAps/$1/$2');
    $routes->get('estimasispk/(:any)', 'OrderController::estimasispk/$1');
    $routes->post('exportEstimasispk', 'ExcelController::exportEstimasispk');
    $routes->post('exportDataOrderArea', 'ExcelController::exportDataOrderArea');
    $routes->post('tampilPerdelivery', 'OrderController::tampilPerdelivery');
    $routes->post('dataOrderSearch', 'OrderController::dataOrderSearch');
    $routes->get('flowProses', 'OrderController::flowProses');
    $routes->get('statusorder/(:any)', 'OrderController::statusOrderArea/$1');


    // mesin
    $routes->get('datamesin', 'MesinController::mesinperareaAps');
    $routes->get('mesinPerJarum/(:any)', 'MesinController::mesinPerJarumPlan/$1');
    $routes->get('mesinperarea', 'MesinController::mesinperareaAps');
    $routes->get('stockcylinder', 'MesinController::stockcylinderPlan');
    $routes->get('datamesinperjarum/(:any)/(:any)', 'MesinController::DetailMesinPerJarumPlan/$1/$2');
    $routes->get('datamesinperarea/(:any)', 'MesinController::DetailMesinPerAreaPlan/$1');
    $routes->post('deletemesinareal/(:any)', 'MesinController::deletemesinarealPlan/$1');
    $routes->post('deletemesinpernomor/(:any)', 'MesinController::deletemesinpernomor/$1');
    $routes->post('updatemesinperjarum/(:any)', 'MesinController::updatemesinperjarumPlan/$1');
    $routes->post('updatemesinpernomor/(:any)', 'MesinController::updatemesinpernomor/$1');
    $routes->get('mesinPernomor/(:any)/(:any)', 'MesinController::mesinPernomor/$1/$2');
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
    $routes->get('getModelData', 'PlanningController::getModelData');
    $routes->post('pindahjarum/(:any)', 'PlanningController::pindahjarum/$1');
    $routes->get('denah/(:any)', 'PlanningController::denahMesin/$1');
    $routes->get('detailDenah', 'PlanningController::detailDenah');



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

    //summary bs mc
    $routes->post('exportSummaryBs', 'SummaryController::excelSummaryBs');

    $routes->get('planningmesin', 'ApsController::planningmesin');
    $routes->post('fetch_jarum', 'ApsController::fetch_jarum');
    $routes->post('SimpanJudul', 'ApsController::saveplanningmesin');
    $routes->get('detailplnmc/(:any)', 'ApsController::detailplanmc/$1');
    $routes->post('excelplnmc/(:any)', 'ExcelController::excelPlnMc/$1');
    $routes->get('fetchdetailorderarea', 'ApsController::fetchdetailorderarea');
    $routes->get('planningpage/(:any)/(:any)', 'ApsController::planningpage/$1/$2');
    $routes->post('getDataLibur', 'ApsController::getDataLibur');
    $routes->post('saveplanning', 'ApsController::saveplanning');
    $routes->get('getMesinByDate/(:any)', 'ApsController::getMesinByDate/$1');
    $routes->get('kalenderMesin/(:any)', 'ApsController::kalenderMesin/$1');
    $routes->post('deleteplanmesin', 'ApsController::deleteplanmesin');
    $routes->post('stopPlanning/(:any)', 'ApsController::stopPlanning/$1');
    $routes->get('detailplanstop/(:any)', 'ApsController::detailplanstop/$1');
    $routes->post('activePlanning/(:any)', 'ApsController::activePlanning/$1');
    $routes->get('getPlanStyle', 'ApsController::getPlanStyle');
    $routes->post('savePlanStyle', 'ApsController::savePlanStyle');
    $routes->get('cekBahanBaku/(:num)/(:num)', 'MaterialController::cekBahanBaku/$1/$2');
    $routes->get('cekStok', 'MaterialController::cekStok');
    $routes->get('cekStokStyle', 'MaterialController::cekStokStyle');
    $routes->get('summaryPlanner/(:any)', 'SummaryController::summaryPlanner/$1');
    $routes->get('summaryStopPlanner/(:any)', 'SummaryController::summaryStopPlanner/$1');
    $routes->post('deletePlanPdk', 'ApsController::deletePlanPdk');
    $routes->post('deletePlanAll', 'ApsController::deletePlanAll');
    $routes->get('getListMesinplan', 'ApsController::getListMesinplan');

    // deffect
    $routes->get('datadeffect', 'DeffectController::datadeffect');
    $routes->post('inputKode', 'DeffectController::inputKode');
    $routes->post('viewDataBs', 'DeffectController::viewDataBs');

    //bahanbakyu
    $routes->get('stockbb', 'MaterialController::stockbb');
    $routes->get('filterstockbahanbaku', 'MaterialController::filterStockBahanBaku');
    $routes->get('statusbahanbaku', 'MaterialController::statusbahanbaku');
    $routes->get('filterstatusbahanbaku/(:any)', 'MaterialController::filterstatusbahanbaku/$1');
    $routes->get('exportDatangBenang', 'ExcelController::exportDatangBenang');
    $routes->get('warehouse/reportPoBenang', 'MaterialController::reportPoBenang');
    $routes->get('warehouse/filterPoBenang', 'MaterialController::filterPoBenang');
    $routes->get('warehouse/exportPoBenang', 'ExcelController::exportPoBenang');
    $routes->get('warehouse/reportDatangBenang', 'MaterialController::reportDatangBenang');
    $routes->get('warehouse/filterDatangBenang', 'MaterialController::filterDatangBenang');
    $routes->get('warehouse/exportDatangBenang', 'ExcelController::exportDatangBenang');
    $routes->get('warehouse/exportExcel', 'ExcelController::excelStockMaterial');
    $routes->get('warehouse/reportPengiriman', 'MaterialController::reportPengiriman');
    $routes->get('warehouse/filterPengiriman', 'MaterialController::filterPengiriman');
    $routes->get('warehouse/exportPengiriman', 'ExcelController::exportPengiriman');
    $routes->get('warehouse/reportGlobal', 'MaterialController::reportGlobal');
    $routes->get('warehouse/filterReportGlobal', 'MaterialController::filterReportGlobal');
    $routes->get('warehouse/reportGlobalNylon', 'MaterialController::reportGlobalNylon');
    $routes->get('warehouse/filterReportNylon', 'MaterialController::filterReportGlobal');
    $routes->get('warehouse/exportGlobalReport', 'ExcelController::exportGlobalReport');
    $routes->get('warehouse/reportGlobalStockBenang', 'MaterialController::reportGlobalStockBenang');
    $routes->get('warehouse/filterReportGlobalBenang', 'MaterialController::filterReportGlobal');
    $routes->get('warehouse/exportReportGlobalBenang', 'ExcelController::exportReportGlobalBenang');
    $routes->get('warehouse/reportSisaPakaiBenang', 'MaterialController::reportSisaPakaiBenang');
    $routes->get('warehouse/filterSisaPakaiBenang', 'MaterialController::filterSisaPakai');
    $routes->get('warehouse/exportReportSisaPakaiBenang', 'ExcelController::exportReportSisaPakai');
    $routes->get('warehouse/reportSisaPakaiNylon', 'MaterialController::reportSisaPakaiNylon');
    $routes->get('warehouse/filterSisaPakaiNylon', 'MaterialController::filterSisaPakai');
    $routes->get('warehouse/exportReportSisaPakaiNylon', 'ExcelController::exportReportSisaPakai');
    $routes->get('warehouse/reportSisaPakaiSpandex', 'MaterialController::reportSisaPakaiSpandex');
    $routes->get('warehouse/filterSisaPakaiSpandex', 'MaterialController::filterSisaPakai');
    $routes->get('warehouse/exportReportSisaPakaiSpandex', 'ExcelController::exportReportSisaPakai');
    $routes->get('warehouse/reportSisaPakaiKaret', 'MaterialController::reportSisaPakaiKaret');
    $routes->get('warehouse/filterSisaPakaiKaret', 'MaterialController::filterSisaPakai');
    $routes->get('warehouse/exportReportSisaPakaiKaret', 'ExcelController::exportReportSisaPakai');
    $routes->get('warehouse/historyPindahOrder', 'MaterialController::historyPindahOrder');
    $routes->get('warehouse/exportHistoryPindahOrder', 'ExcelController::exportHistoryPindahOrder');
    $routes->get('pemesanan/historyPinjamOrder', 'MaterialController::HistoryPinjamOrder');
    $routes->get('pemesanan/exportHistoryPinjamOrder', 'ExcelController::exportHistoryPinjamOrder');
    $routes->get('warehouse/reportSisaDatangBenang', 'MaterialController::reportSisaDatangBenang');
    $routes->get('warehouse/exportReportSisaDatangBenang', 'ExcelController::exportReportSisaDatangBenang');
    $routes->get('warehouse/reportSisaDatangNylon', 'MaterialController::reportSisaDatangNylon');
    $routes->get('warehouse/exportReportSisaDatangNylon', 'ExcelController::exportReportSisaDatangNylon');
    $routes->get('warehouse/reportSisaDatangSpandex', 'MaterialController::reportSisaDatangSpandex');
    $routes->get('warehouse/exportReportSisaDatangSpandex', 'ExcelController::exportReportSisaDatangSpandex');
    $routes->get('warehouse/reportSisaDatangKaret', 'MaterialController::reportSisaDatangKaret');
    $routes->get('warehouse/exportReportSisaDatangKaret', 'ExcelController::exportReportSisaDatangKaret');
    $routes->get('warehouse/reportBenangMingguan', 'MaterialController::reportBenangMingguan');
    $routes->get('warehouse/filterBenangMingguan', 'MaterialController::filterBenangMingguan');
    $routes->get('warehouse/exportReportBenangMingguan', 'ExcelController::exportReportBenang');
    $routes->get('warehouse/reportBenangBulanan', 'MaterialController::reportBenangBulanan');
    $routes->get('warehouse/filterBenangBulanan', 'MaterialController::filterBenangBulanan');
    $routes->get('warehouse/exportReportBenangBulanan', 'ExcelController::exportReportBenang');

    //pph
    $routes->get('pph/(:any)', 'MaterialController::pph/$1');
    $routes->get('filterpph/(:any)', 'MaterialController::filterPph/$1');
    $routes->get('tampilPerStyle/(:any)', 'MaterialController::tampilPerStyle/$1');
    $routes->get('pphinisial/(:any)', 'MaterialController::pphinisial/$1');
    $routes->get('pphPerhari/(:any)', 'MaterialController::pphPerhari/$1');
    $routes->get('getDataPerhari/(:any)', 'MaterialController::getDataPerhari/$1');
    $routes->get('excelPPHNomodel/(:any)/(:any)', 'ExcelPPHController::excelPPHNomodel/$1/$2');
    $routes->get('excelPPHInisial/(:any)/(:any)', 'ExcelPPHController::excelPPHInisial/$1/$2');
    $routes->get('excelPPHDays/(:any)/(:any)', 'ExcelPPHController::excelPPHDays/$1/$2');

    //retur
    $routes->get('retur', 'ReturController::listdataReturArea');
    $routes->get('exportExcelRetur/(:any)', 'ExcelController::exportExcelRetur/$1');

    //po tambahan
    $routes->get('po_tambahan/(:any)', 'MaterialController::poTambahan/$1');
    $routes->get('poTambahanDetail/(:any)/(:any)', 'MaterialController::poTambahanDetail/$1/$2');
    $routes->get('filter_list_potambahan/(:any)', 'MaterialController::filterPoTambahan/$1');
    $routes->get('generate_po_tambahan', 'PdfController::generatePoTambahan');
    $routes->get('generate_excel_po_tambahan', 'ExcelController::generatePoTambahan');

    $routes->get('jatah_bahan_baku', 'MaterialController::jatahBahanBaku');
    $routes->get('export_excel_jatah_bb', 'ExcelController::exportExcelJatahNoModel');

    // pengaduan
    $routes->get('pengaduan', 'PengaduanController::index');
    $routes->post('pengaduan/create', 'PengaduanController::create');
    $routes->post('pengaduan/reply/(:num)', 'PengaduanController::reply/$1');
});

// user
$routes->group('/user', ['filter' => 'user'], function ($routes) {
    $routes->get('', 'UserController::index');
    $routes->get('produksi', 'UserController::produksi');
    $routes->get('bssetting', 'UserController::bssetting');
    $routes->get('bsmesin', 'UserController::bsmesin');
    $routes->get('bsMesinPerbulan/(:any)/(:any)', 'UserController::bsMesinPerbulan/$1/$2');
    $routes->post('saveBsMesin', 'UserController::saveBsMesin');
    $routes->get('userController/getInisial/(:any)', 'UserController::getInisial/$1');
    $routes->get('userController/getSize/(:any)', 'UserController::getSize/$1');
    $routes->get('dataproduksi', 'ProduksiController::viewProduksi');
    $routes->get('produksi', 'ProduksiController::produksi');
    $routes->get('dataprogress', 'ProduksiController::progressData');
    $routes->get('produksiareachart', 'ProduksiController::produksiAreaChart');
    $routes->get('dataproduksi/(:any)', 'ProduksiController::produksiPerArea/$1');
    $routes->post('importproduksi', 'ProduksiController::importproduksinew');
    $routes->post('resetproduksi', 'ProduksiController::resetproduksi');
    $routes->post('resetproduksiarea', 'ProduksiController::resetproduksiarea');
    $routes->post('editproduksi', 'ProduksiController::editproduksi');
    $routes->get('updateproduksi', 'ProduksiController::updateproduksi');
    $routes->get('detailproduksi/(:any)', 'ProduksiController::produksiPerArea/$1');
    $routes->post('detailproduksi/(:any)', 'ProduksiController::produksiPerArea/$1');
    $routes->get('updatebs', 'ProduksiController::updatebs');
    $routes->post('get-area', 'ProduksiController::getArea');
    $routes->post('get-size', 'ProduksiController::getSize');
    $routes->post('prosesInputProdManual', 'ProduksiController::inputProduksiManual');
    // data order
    $routes->get('dataorderperarea/(:any)', 'OrderController::DetailOrderPerAreaPlan/$1');
    $routes->get('detailPdk/(:any)/(:any)', 'OrderController::pdkDetail/$1/$2');
    $routes->post('inputinisial', 'UserController::inputinisial');
    $routes->get('statusorder/(:any)', 'OrderController::statusOrderArea/$1');
    $routes->get('estimasispk/(:any)', 'OrderController::estimasispk/$1');
    $routes->post('mintaSpk2', 'OrderController::mintaSpk2');
    $routes->post('spkmanual', 'OrderController::spkmanual');
    $routes->post('exportEstimasispk', 'ExcelController::exportEstimasispk');
    $routes->post('importinisial', 'UserController::importinisial');
    $routes->get('progressdetail/(:any)/(:any)', 'ApsController::progressdetail/$1/$2');


    // $routes->post('importproduksi', 'ProduksiController::importproduksi');
    $routes->post('importproduksi', 'ProduksiController::importproduksinew');
    $routes->post('importbssetting', 'ProduksiController::importbssetting');
    $routes->post('importbsmesin', 'ProduksiController::importbsmc');
    $routes->get('viewModelPlusPacking/(:any)', 'ProduksiController::viewModelPlusPacking/$1');
    $routes->get('pluspacking', 'ProduksiController::pluspacking');
    $routes->post('inputpo', 'ProduksiController::updatepo');
    $routes->post('get-area', 'ProduksiController::getArea');
    $routes->post('get-size', 'ProduksiController::getSize');
    $routes->post('prosesInputProdManual', 'ProduksiController::inputProduksiManual');

    //summary
    $routes->post('summaryproduksi', 'ProduksiController::summaryProduksi');
    $routes->post('exportSummaryPerTod', 'SummaryController::excelSummaryPerTod');

    //summary produksi
    $routes->post('summaryProdPerTanggal', 'ProduksiController::summaryProdPerTanggal');
    $routes->post('exportSummaryPerTgl', 'SummaryController::excelSummaryPerTgl');

    $routes->get('datadeffect', 'DeffectController::datadeffect');
    $routes->post('inputKode', 'DeffectController::inputKode');
    $routes->post('viewDataBs', 'DeffectController::viewDataBs');

    //timter produksi
    $routes->post('timterProduksi', 'ProduksiController::timterProduksi');
    $routes->post('exportTimter', 'TimterController::excelTimter');

    // Jarum
    $routes->get('penggunaanJarum', 'UserController::penggunaanJarum');
    $routes->post('savePenggunaanJarum', 'UserController::savePenggunaanJarum');
    $routes->get('penggunaanPerbulan/(:any)/(:any)', 'UserController::penggunaanPerbulan/$1/$2');
    $routes->get('excelPenggunaanPerbulan/(:any)/(:any)', 'UserController::exportPenggunaanPerbulan/$1/$2');

    //summary bs mc
    $routes->post('deleteBsMc', 'UserController::deleteBsMc');
    $routes->post('exportSummaryBs', 'SummaryController::excelSummaryBs');
    $routes->get('exportBsMesinPerbulan/(:any)/(:any)', 'SummaryController::summaryBsMesinPerbulan/$1/$2');

    // bahanbaku
    $routes->get('bahanBaku', 'MaterialController::index');
    $routes->get('bahanBaku/getNomodel', 'MaterialController::getNomodel');
    $routes->get('statusbahanbaku', 'MaterialController::statusbahanbaku');
    $routes->get('filterstatusbahanbaku/(:any)', 'MaterialController::filterstatusbahanbaku/$1');
    $routes->post('getStyleSizeByNoModel', 'MaterialController::getStyleSizeByNoModel');
    $routes->get('getStyleSizeByNoModelPemesanan', 'MaterialController::getStyleSizeByNoModelPemesanan');
    $routes->post('getJalanMc', 'MaterialController::getJalanMcByModelSize');
    $routes->get('getMU/(:any)/(:any)/(:any)/(:any)', 'MaterialController::getMU/$1/$2/$3/$4');
    $routes->post('bahanBaku/simpanKeSession', 'MaterialController::savePemesananSession');
    $routes->post('bahanBaku/hapusSession', 'MaterialController::deletePemesananSession');
    // $routes->get('bahanBaku/hapusSession/(:any)/(:any)', 'MaterialController::deletePemesananSession/$1/$2');
    // $routes->get('listPemesanan/(:any)', 'MaterialController::listPemesanan/$1');
    $routes->get('listPemesanan/(:any)', 'MaterialController::listPemesanan/$1');
    // $routes->get('listPemesananCoba/(:any)', 'MaterialController::listPemesananCoba/$1');
    $routes->get('report_pemesanan/(:any)', 'MaterialController::reportPemesanan/$1');
    $routes->post('filterTglPakai/(:any)', 'MaterialController::filterTglPakai/$1');
    $routes->get('report/pdf/(:any)/(:any)/(:any)', 'PdfController::exportPemesanan/$1/$2/$3');
    $routes->get('stockbahanbaku/(:any)', 'MaterialController::stockBahanBaku/$1');
    $routes->get('filterstockbahanbaku/(:any)', 'MaterialController::filterStockBahanBaku/$1');

    //pph
    $routes->get('pph/(:any)', 'MaterialController::pph/$1');
    $routes->get('filterpph/(:any)', 'MaterialController::filterPph/$1');
    $routes->get('tampilPerStyle/(:any)', 'MaterialController::tampilPerStyle/$1');
    $routes->get('pphinisial/(:any)', 'MaterialController::pphinisial/$1');
    // $routes->get('tampilPerDays/(:any)', 'MaterialController::tampilPerDays/$1');
    $routes->get('pphPerhari/(:any)', 'MaterialController::pphPerhari/$1');
    $routes->get('getDataPerhari/(:any)', 'MaterialController::getDataPerhari/$1');
    $routes->get('excelPPHNomodel/(:any)/(:any)', 'ExcelPPHController::excelPPHNomodel/$1/$2');
    $routes->get('excelPPHInisial/(:any)/(:any)', 'ExcelPPHController::excelPPHInisial/$1/$2');
    $routes->get('excelPPHDays/(:any)/(:any)', 'ExcelPPHController::excelPPHDays/$1/$2');
    $routes->post('requestAdditionalTime', 'MaterialController::requestAdditionalTime');
    $routes->post('requestAdditionalTime/getTanggalPakai', 'MaterialController::getTanggalPakai');
    $routes->post('getQty', 'MaterialController::getQtyByModelSize');

    //po tambahan
    $routes->get('po_tambahan/(:any)', 'MaterialController::poTambahan/$1');
    $routes->get('form-potambahan/(:any)', 'MaterialController::formPoTambahan/$1');
    $routes->get('getStyleSize/(:any)/(:any)', 'MaterialController::getStyleSize/$1/$2');
    $routes->get('poTambahanDetail/(:any)/(:any)', 'MaterialController::poTambahanDetail/$1/$2');
    $routes->post('savePoTambahan/(:any)', 'MaterialController::savePoTambahan/$1');
    $routes->get('filter_list_potambahan/(:any)', 'MaterialController::filterPoTambahan/$1');
    $routes->get('generate_po_tambahan', 'PdfController::generatePoTambahan');
    $routes->get('generate_excel_po_tambahan', 'ExcelController::generatePoTambahan');

    $routes->get('sisaKebutuhanArea/(:any)', 'MaterialController::sisaKebutuhanArea/$1');

    // retur
    $routes->get('retur/(:any)', 'ReturController::index/$1');
    $routes->get('filterRetur/(:any)', 'ReturController::dataRetur/$1');
    $routes->post('pengajuanRetur/(:any)', 'ReturController::pengajuanRetur/$1');
    $routes->get('getKategoriRetur', 'ReturController::getKategoriRetur');
    $routes->get('getKodeWarnaWarnaByItemType', 'ReturController::getKodeWarnaWarnaByItemType');
    $routes->get('exportExcelRetur/(:any)', 'ExcelController::exportExcelRetur/$1');
    $routes->get('listRetur/(:any)', 'ReturController::listRetur/$1');
    $routes->get('exportPdfRetur/(:any)', 'PdfController::exportPdfRetur/$1');
    $routes->get('generateFormRetur/(:any)', 'ExcelController::generateFormRetur/$1');

    // pengaduan
    $routes->get('pengaduan', 'PengaduanController::index');
    $routes->post('pengaduan/create', 'PengaduanController::create');
    $routes->post('pengaduan/reply/(:num)', 'PengaduanController::reply/$1');
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
    $routes->get('dataorderperjarumblndetail/(:any)/(:any)/(:any)', 'OrderController::orderPerMonth/$1/$2/$3');
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
    $routes->post('sisaOrder/(:any)', 'OrderController::sisaOrderBuyer/$1');
    $routes->post('excelSisaOrderBuyer/(:any)', 'ExcelController::excelSisaOrderBuyer/$1');
    $routes->get('statusOrder', 'OrderController::statusOrder');
    $routes->get('statusorder/(:any)', 'OrderController::statusOrderArea/$1');
    $routes->get('progressdetail/(:any)/(:any)', 'ApsController::progressdetail/$1/$2');
    $routes->get('sisaOrderArea', 'OrderController::sisaOrderArea');
    $routes->get('sisaOrderArea/(:any)', 'OrderController::detailSisaOrderArea/$1');
    $routes->post('sisaOrderArea/(:any)', 'OrderController::detailSisaOrderArea/$1');
    $routes->post('excelSisaOrderAllArea', 'ExcelController::excelSisaOrderAllArea');
    $routes->post('excelSisaOrderArea', 'ExcelController::excelSisaOrderArea');
    $routes->post('inputinisial', 'UserController::inputinisial');
    $routes->post('importinisial', 'UserController::importinisial');
    $routes->get('flowProses', 'OrderController::flowProses');
    $routes->post('exportDataOrder', 'ExcelController::exportDataOrder');


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
    $routes->get('updateproduksi', 'ProduksiController::updateproduksi');
    $routes->get('detailproduksi', 'ProduksiController::produksiGlobal');
    $routes->get('detailproduksi/(:any)', 'ProduksiController::produksiPerArea/$1');
    $routes->post('detailproduksi/(:any)', 'ProduksiController::produksiPerArea/$1');
    $routes->get('updatebs', 'ProduksiController::updatebs');
    $routes->get('hapus-produksi/(:any)', 'ProduksiController::deleteProduksi/$1');

    $routes->post('summaryproduksi', 'ProduksiController::summaryProduksi');
    $routes->get('bssetting', 'UserController::bssetting');
    $routes->post('importbssetting', 'ProduksiController::importbssetting');
    $routes->post('get-area', 'ProduksiController::getArea');
    $routes->post('get-size', 'ProduksiController::getSize');
    $routes->post('prosesInputProdManual', 'ProduksiController::inputProduksiManual');

    //summary
    $routes->post('summaryproduksi', 'ProduksiController::summaryProduksi');
    $routes->post('exportSummaryPerTod', 'SummaryController::excelSummaryPerTod');

    //summary produksi
    $routes->post('summaryProdPerTanggal', 'ProduksiController::summaryProdPerTanggal');
    $routes->post('exportSummaryPerTgl', 'SummaryController::excelSummaryPerTgl');

    //timter produksi
    $routes->post('timterProduksi', 'ProduksiController::timterProduksi');
    $routes->post('exportTimter', 'TimterController::excelTimter');
    //summary bs mc
    $routes->post('exportSummaryBs', 'SummaryController::excelSummaryBs');

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

    //pluspacking
    $routes->get('viewModelPlusPacking/(:any)', 'ProduksiController::viewModelPlusPacking/$1');
    $routes->get('pluspacking', 'ProduksiController::pluspacking');
    $routes->post('inputpo', 'ProduksiController::updatepo');

    // usermanageement
    $routes->get('account', 'GodController::account');
    $routes->post('addaccount', 'GodController::addaccount');
    $routes->post('assignarea', 'GodController::assignarea');
    $routes->post('updateaccount/(:any)', 'GodController::updateaccount/$1');
    $routes->post('deleteaccount/(:any)', 'GodController::deleteaccount/$1');

    // chat
    $routes->get('chat', 'ChatController::pesan');

    // pengaduan
    $routes->get('pengaduan', 'PengaduanController::index');
    $routes->post('pengaduan/create', 'PengaduanController::create');
    $routes->post('pengaduan/reply/(:num)', 'PengaduanController::reply/$1');
});

// ie
$routes->group('/ie', ['filter' => 'ie'], function ($routes) {
    $routes->get('', 'IeController::index');
    $routes->get('historysmv', 'IeController::historysmv');
    $routes->post('getServerSide', 'IeController::getServerSide');
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
    // pengaduan
    $routes->get('pengaduan', 'PengaduanController::index');
    $routes->post('pengaduan/create', 'PengaduanController::create');
    $routes->post('pengaduan/reply/(:num)', 'PengaduanController::reply/$1');
});

// rosso
$routes->group(
    '/rosso',
    ['filter' => 'rosso'],
    function ($routes) {
        //pemesanan
        $routes->get('', 'RossoController::index');
        $routes->get('pemesanan', 'RossoController::index');
        $routes->get('bahanBaku/getNomodel', 'RossoController::getNomodel');
        $routes->get('getStyleSizeByNoModelPemesanan', 'RossoController::getStyleSizeByNoModelPemesanan');
        $routes->post('getQty', 'MaterialController::getQtyByModelSize');
        $routes->get('getMU/(:any)/(:any)/(:any)/(:any)', 'RossoController::getMU/$1/$2/$3/$4');
        $routes->post('bahanBaku/simpanKeSession', 'RossoController::savePemesananSession');
        $routes->post('bahanBaku/hapusSession', 'RossoController::deletePemesananSession');
        $routes->get('listPemesanan/(:any)', 'RossoController::listPemesanan/$1');
        $routes->post('requestAdditionalTime', 'MaterialController::requestAdditionalTime');
        $routes->post('requestAdditionalTime/getTanggalPakai', 'RossoController::getTanggalPakai');
        $routes->get('report_pemesanan/(:any)', 'RossoController::reportPemesanan/$1');
        $routes->post('filterTglPakai/(:any)', 'RossoController::filterTglPakai/$1');
        $routes->get('report/pdf/(:any)/(:any)/(:any)', 'PdfController::exportPemesanan/$1/$2/$3');
        $routes->get('sisaKebutuhanArea/(:any)', 'RossoController::sisaKebutuhanArea/$1');

        // retur
        $routes->get('retur/(:any)', 'ReturController::index/$1');
        $routes->get('filterRetur/(:any)', 'ReturController::dataRetur/$1');
        $routes->post('pengajuanRetur/(:any)', 'ReturController::pengajuanRetur/$1');
        $routes->get('getKategoriRetur', 'ReturController::getKategoriRetur');
        $routes->get('getKodeWarnaWarnaByItemType', 'ReturController::getKodeWarnaWarnaByItemType');
        $routes->get('exportExcelRetur/(:any)', 'ExcelController::exportExcelRetur/$1');
        $routes->get('listRetur/(:any)', 'ReturController::listRetur/$1');
        $routes->get('exportPdfRetur/(:any)', 'PdfController::exportPdfRetur/$1');
        $routes->get('generateFormRetur/(:any)', 'ExcelController::generateFormRetur/$1');
        // pengaduan
        $routes->get('pengaduan', 'PengaduanController::index');
        $routes->post('pengaduan/create', 'PengaduanController::create');
        $routes->post('pengaduan/reply/(:num)', 'PengaduanController::reply/$1');
    }
);

//followup
$routes->group('/followup', ['filter' => 'followup'], function ($routes) {
    // pengaduan
    $routes->get('pengaduan', 'PengaduanController::index');
    $routes->post('pengaduan/create', 'PengaduanController::create');
    $routes->post('pengaduan/reply/(:num)', 'PengaduanController::reply/$1');
    // booking
    $routes->get('databooking', 'BookingController::bookingPlan');
    $routes->get('databooking/(:any)', 'BookingController::bookingPerJarumPLan/$1');
    $routes->get('databookingbulan/(:any)', 'BookingController::bookingPerBulanJarumPlan/$1');
    $routes->get('databookingbulantampil/(:any)/(:any)/(:any)', 'BookingController::bookingPerBulanJarumTampilPlan/$1/$2/$3');
    $routes->get('detailbooking/(:any)', 'BookingController::detailbookingPlan/$1');
    $routes->post('exportDataBooking', 'ExcelController::exportDataBooking');

    //order
    $routes->get('', 'FollowupController::index');
    $routes->get('dataorder', 'FollowupController::order');
    $routes->get('startStopMcByPdk', 'FollowupController::startStopMcByPdk');
    $routes->get('blmAdaArea', 'OrderController::orderBlmAdaAreal');
    $routes->get('orderPerjarum', 'OrderController::OrderPerJarumPlan');
    $routes->get('orderPerArea', 'OrderController::orderPerAreaPlan');
    $routes->get('statusOrder', 'OrderController::statusOrder');
    $routes->get('statusorder/(:any)', 'OrderController::statusOrderArea/$1');
    $routes->post('statusorder/(:any)', 'OrderController::statusOrderArea/$1');
    $routes->get('progressdetail/(:any)/(:any)', 'ApsController::progressdetail/$1/$2');
    $routes->get('detailPdk/(:any)/(:any)', 'OrderController::detailPdk/$1/$2');
    $routes->get('detailModelPlanning/(:any)/(:any)', 'OrderController::detailModelPlanning/$1/$2');
    $routes->get('detailmodeljarum/(:any)/(:any)/(:any)', 'OrderController::detailmodeljarumPlan/$1/$2/$3');
    $routes->get('semuaOrder', 'OrderController::semuaOrder');
    $routes->post('exportDataOrder', 'ExcelController::exportDataOrder');
    $routes->get('dataorderperjarum/(:any)', 'OrderController::DetailOrderPerJarumPlan/$1');
    $routes->get('dataorderperarea/(:any)', 'OrderController::DetailOrderPerAreaPlan/$1');
    $routes->post('updatedetailorder/(:any)', 'OrderController::updateorder/$1');
    $routes->post('updatedetailjarum/(:any)', 'OrderController::updateorderjarumplan/$1');
    $routes->post('deletedetailstyle/(:any)', 'OrderController::deletedetailstyle/$1');
    $routes->post('deletedetailorder/(:any)', 'OrderController::deletedetailorder/$1');
    $routes->post('deletedetailjarum/(:any)', 'OrderController::deletedetailmodeljarumplan/$1');
    $routes->post('assignareal', 'FollowupController::assignareal');
    $routes->post('splitarea', 'FollowupController::splitarea');
    $routes->post('editarea', 'FollowupController::editarea');
    $routes->post('editqtyarea', 'FollowupController::editqtyarea');
    $routes->post('assignarealall', 'FollowupController::assignarealall');
    $routes->post('recomendationarea', 'MesinController::recomendationarea');
    $routes->post('tampilPerdelivery', 'OrderController::tampilPerdelivery');
    $routes->get('orderPerbulan', 'OrderController::orderPerbulan');
    $routes->get('orderPerMonth/(:any)/(:any)', 'OrderController::orderPerMonth/$1/$2');
    $routes->post('inputhistoryrevise/(:any)', 'OrderController::inputHistory/$1');
    $routes->get('pengajuanspk2', 'OrderController::spk2');
    $routes->post('approveSpk2', 'OrderController::approveSpk2');
    $routes->post('rejectSpk2', 'OrderController::rejectSpk2');
    $routes->get('sisaOrder', 'OrderController::sisaOrder');
    $routes->get('sisaOrder/(:any)', 'OrderController::sisaOrderBuyer/$1');
    $routes->post('sisaOrder/(:any)', 'OrderController::sisaOrderBuyer/$1');
    $routes->post('excelSisaOrderBuyer/(:any)', 'ExcelController::excelSisaOrderBuyer/$1');
    $routes->get('sisaOrderArea', 'OrderController::sisaOrderArea');
    $routes->get('sisaOrderArea/(:any)', 'OrderController::detailSisaOrderArea/$1');
    $routes->post('sisaOrderArea/(:any)', 'OrderController::detailSisaOrderArea/$1');
    $routes->post('excelSisaOrderArea', 'ExcelController::excelSisaOrderArea');
    $routes->post('excelSisaOrderAllArea', 'ExcelController::excelSisaOrderAllArea');
    $routes->post('saveRepeat', 'OrderController::saveRepeat');
    $routes->get('flowProses', 'OrderController::flowProses');
    $routes->post('semuaOrder/importFlowproses', 'OrderController::importFlowproses');

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
    $routes->get('summaryPlanner/(:any)', 'SummaryController::summaryPlanner/$1');

    //summary bs mc
    $routes->post('exportSummaryBs', 'SummaryController::excelSummaryBs');

    // deffect
    $routes->get('datadeffect', 'DeffectController::datadeffect');
    $routes->post('inputKode', 'DeffectController::inputKode');
    $routes->post('viewDataBs', 'DeffectController::viewDataBs');

    // bs mesin
    $routes->get('bsmesin', 'FollowupController::bsmesin');
    $routes->post('bsMesinByDate', 'FollowupController::bsMesinByDate');


    //bahanbakyu
    $routes->get('stockbb', 'MaterialController::stockbb');
    $routes->get('filterstockbahanbaku', 'MaterialController::filterStockBahanBaku');
    $routes->get('statusbahanbaku', 'MaterialController::statusbahanbaku');
    $routes->get('filterstatusbahanbaku/(:any)', 'MaterialController::filterstatusbahanbaku/$1');
    $routes->get('warehouse/reportPoBenang', 'MaterialController::reportPoBenang');
    $routes->get('warehouse/filterPoBenang', 'MaterialController::filterPoBenang');
    $routes->get('warehouse/exportPoBenang', 'ExcelController::exportPoBenang');
    $routes->get('warehouse/reportDatangBenang', 'MaterialController::reportDatangBenang');
    $routes->get('warehouse/filterDatangBenang', 'MaterialController::filterDatangBenang');
    $routes->get('warehouse/exportDatangBenang', 'ExcelController::exportDatangBenang');
    $routes->get('warehouse/exportExcel', 'ExcelController::excelStockMaterial');
    $routes->get('warehouse/reportPengiriman', 'MaterialController::reportPengiriman');
    $routes->get('warehouse/filterPengiriman', 'MaterialController::filterPengiriman');
    $routes->get('warehouse/exportPengiriman', 'ExcelController::exportPengiriman');
    $routes->get('warehouse/reportGlobal', 'MaterialController::reportGlobal');
    $routes->get('warehouse/filterReportGlobal', 'MaterialController::filterReportGlobal');
    $routes->get('warehouse/reportGlobalNylon', 'MaterialController::reportGlobalNylon');
    $routes->get('warehouse/filterReportNylon', 'MaterialController::filterReportGlobal');
    $routes->get('warehouse/exportGlobalReport', 'ExcelController::exportGlobalReport');
    $routes->get('warehouse/reportGlobalStockBenang', 'MaterialController::reportGlobalStockBenang');
    $routes->get('warehouse/filterReportGlobalBenang', 'MaterialController::filterReportGlobal');
    $routes->get('warehouse/exportReportGlobalBenang', 'ExcelController::exportReportGlobalBenang');
    $routes->get('warehouse/reportSisaPakaiBenang', 'MaterialController::reportSisaPakaiBenang');
    $routes->get('warehouse/filterSisaPakaiBenang', 'MaterialController::filterSisaPakai');
    $routes->get('warehouse/exportReportSisaPakaiBenang', 'ExcelController::exportReportSisaPakai');
    $routes->get('warehouse/reportSisaPakaiNylon', 'MaterialController::reportSisaPakaiNylon');
    $routes->get('warehouse/filterSisaPakaiNylon', 'MaterialController::filterSisaPakai');
    $routes->get('warehouse/exportReportSisaPakaiNylon', 'ExcelController::exportReportSisaPakai');
    $routes->get('warehouse/reportSisaPakaiSpandex', 'MaterialController::reportSisaPakaiSpandex');
    $routes->get('warehouse/filterSisaPakaiSpandex', 'MaterialController::filterSisaPakai');
    $routes->get('warehouse/exportReportSisaPakaiSpandex', 'ExcelController::exportReportSisaPakai');
    $routes->get('warehouse/reportSisaPakaiKaret', 'MaterialController::reportSisaPakaiKaret');
    $routes->get('warehouse/filterSisaPakaiKaret', 'MaterialController::filterSisaPakai');
    $routes->get('warehouse/exportReportSisaPakaiKaret', 'ExcelController::exportReportSisaPakai');
    $routes->get('warehouse/historyPindahOrder', 'MaterialController::historyPindahOrder');
    $routes->get('warehouse/exportHistoryPindahOrder', 'ExcelController::exportHistoryPindahOrder');
    $routes->get('pemesanan/historyPinjamOrder', 'MaterialController::HistoryPinjamOrder');
    $routes->get('pemesanan/exportHistoryPinjamOrder', 'ExcelController::exportHistoryPinjamOrder');
    $routes->get('warehouse/reportSisaDatangBenang', 'MaterialController::reportSisaDatangBenang');
    $routes->get('warehouse/exportReportSisaDatangBenang', 'ExcelController::exportReportSisaDatangBenang');
    $routes->get('warehouse/reportSisaDatangNylon', 'MaterialController::reportSisaDatangNylon');
    $routes->get('warehouse/exportReportSisaDatangNylon', 'ExcelController::exportReportSisaDatangNylon');
    $routes->get('warehouse/reportSisaDatangSpandex', 'MaterialController::reportSisaDatangSpandex');
    $routes->get('warehouse/exportReportSisaDatangSpandex', 'ExcelController::exportReportSisaDatangSpandex');
    $routes->get('warehouse/reportSisaDatangKaret', 'MaterialController::reportSisaDatangKaret');
    $routes->get('warehouse/exportReportSisaDatangKaret', 'ExcelController::exportReportSisaDatangKaret');
    $routes->get('warehouse/reportBenangMingguan', 'MaterialController::reportBenangMingguan');
    $routes->get('warehouse/filterBenangMingguan', 'MaterialController::filterBenangMingguan');
    $routes->get('warehouse/exportReportBenangMingguan', 'ExcelController::exportReportBenang');
    $routes->get('warehouse/reportBenangBulanan', 'MaterialController::reportBenangBulanan');
    $routes->get('warehouse/filterBenangBulanan', 'MaterialController::filterBenangBulanan');
    $routes->get('warehouse/exportReportBenangBulanan', 'ExcelController::exportReportBenang');

    //pph
    $routes->get('pph/(:any)', 'MaterialController::pph/$1');
    $routes->get('filterpph/(:any)', 'MaterialController::filterPph/$1');
    $routes->get('tampilPerStyle/(:any)', 'MaterialController::tampilPerStyle/$1');
    $routes->get('pphinisial/(:any)', 'MaterialController::pphinisial/$1');
    $routes->get('pphPerhari/(:any)', 'MaterialController::pphPerhari/$1');
    $routes->get('getDataPerhari/(:any)', 'MaterialController::getDataPerhari/$1');
    $routes->get('excelPPHNomodel/(:any)/(:any)', 'ExcelPPHController::excelPPHNomodel/$1/$2');
    $routes->get('excelPPHInisial/(:any)/(:any)', 'ExcelPPHController::excelPPHInisial/$1/$2');
    $routes->get('excelPPHDays/(:any)/(:any)', 'ExcelPPHController::excelPPHDays/$1/$2');

    //sales
    $routes->get('sales', 'SalesController::index2');
    $routes->post('sales/position', 'SalesController::index');
    $routes->post('exportsales', 'SalesController::exportExcelByJarum');
    $routes->get('generatesales', 'SalesController::generateExcel');
});
