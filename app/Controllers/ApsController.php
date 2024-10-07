<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;


use App\Models\DataMesinModel;
use App\Models\OrderModel;
use App\Models\BookingModel;
use App\Models\ProductTypeModel;
use App\Models\ApsPerstyleModel;
use App\Models\ProduksiModel;
use App\Models\LiburModel;
use App\Models\KebutuhanMesinModel;
use App\Models\KebutuhanAreaModel;
use App\Models\MesinPlanningModel;
use App\Models\DetailPlanningModel;
use App\Models\TanggalPlanningModel;
use App\Models\EstimatedPlanningModel;
use App\Models\AksesModel;/*  */
use PhpOffice\PhpSpreadsheet\IOFactory;
use CodeIgniter\HTTP\RequestInterface;


class ApsController extends BaseController
{
    protected $filters;
    protected $jarumModel;
    protected $productModel;
    protected $produksiModel;
    protected $bookingModel;
    protected $orderModel;
    protected $ApsPerstyleModel;
    protected $liburModel;
    protected $KebutuhanMesinModel;
    protected $KebutuhanAreaModel;
    protected $MesinPlanningModel;
    protected $aksesModel;
    protected $DetailPlanningModel;
    protected $TanggalPlanningModel;
    protected $EstimatedPlanningModel;

    public function __construct()
    {
        $this->jarumModel = new DataMesinModel();
        $this->bookingModel = new BookingModel();
        $this->productModel = new ProductTypeModel();
        $this->produksiModel = new ProduksiModel();
        $this->orderModel = new OrderModel();
        $this->ApsPerstyleModel = new ApsPerstyleModel();
        $this->liburModel = new LiburModel();
        $this->KebutuhanMesinModel = new KebutuhanMesinModel();
        $this->KebutuhanAreaModel = new KebutuhanAreaModel();
        $this->MesinPlanningModel = new MesinPlanningModel();
        $this->aksesModel = new AksesModel();
        $this->DetailPlanningModel = new DetailPlanningModel();
        $this->TanggalPlanningModel = new TanggalPlanningModel();
        $this->EstimatedPlanningModel = new EstimatedPlanningModel();
        if ($this->filters   = ['role' => [session()->get('role') . '']] != session()->get('role')) {
            return redirect()->to(base_url('/login'));
        }
        $this->isLogedin();
    }
    protected function isLogedin()
    {
        if (!session()->get('id_user')) {
            return redirect()->to(base_url('/login'));
        }
    }
    public function index()
    {
        $idUser = session()->get('id_user');
        $aksesArea = $this->aksesModel->aksesData($idUser);
        $pdk = [];
        foreach ($aksesArea as $ar) {
            $pdk[$ar] = $this->ApsPerstyleModel->getProgressperArea($ar);
        }
        $orderJalan = $this->bookingModel->getOrderJalan();
        $terimaBooking = $this->bookingModel->getBookingMasuk();
        $mcJalan = $this->jarumModel->mcJalan();
        $totalMc = $this->jarumModel->totalMc();
        $bulan = date('m');

        $area = session()->get('username');
        $data = [
            'role' => session()->get('role'),
            'title' => 'Capacity System',
            'active1' => 'active',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'jalan' => $orderJalan,
            'TerimaBooking' => $terimaBooking,
            'mcJalan' => $mcJalan,
            'totalMc' => $totalMc,
            'order' => $this->ApsPerstyleModel->getTurunOrder($bulan),
            'area' => $area,
            'progress' => $pdk

        ];
        return view(session()->get('role') . '/index', $data);
    }
    public function booking()
    {
        $dataJarum = $this->jarumModel->getJarum();
        $totalMesin = $this->jarumModel->getTotalMesinByJarum();

        $data = [
            'role' => session()->get('role'),
            'title' => 'Data Booking',
            'active1' => '',
            'active2' => 'active',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'Jarum' => $dataJarum,
            'TotalMesin' => $totalMesin,
        ];
        return view(session()->get('role') . '/Booking/booking', $data);
    }
    public function bookingPerJarum($jarum)
    {
        $product = $this->productModel->getJarum($jarum);
        $booking = $this->bookingModel->getDataPerjarum($jarum);

        $data = [
            'role' => session()->get('role'),
            'title' => 'Data Booking',
            'active1' => '',
            'active2' => 'active',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'jarum' => $jarum,
            'product' => $product,
            'booking' => $booking

        ];
        return view(session()->get('role') . '/Booking/jarum', $data);
    }

    public function bookingPerBulanJarum($jarum)
    {
        $bulan = $this->bookingModel->getbulan($jarum);
        $data = [
            'role' => session()->get('role'),
            'title' => 'Data Booking',
            'active1' => '',
            'active2' => 'active',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'bulan' => $bulan,
            'jarum' => $jarum,
        ];
        return view(session()->get('role') . '/Booking/bookingbulan', $data);
    }

    public function bookingPerBulanJarumTampil($bulan, $tahun, $jarum)
    {
        $booking = $this->bookingModel->getDataPerjarumbulan($bulan, $tahun, $jarum);
        $product = $this->productModel->getJarum($jarum);
        $data = [
            'role' => session()->get('role'),
            'title' => 'Data Booking',
            'active1' => '',
            'active2' => 'active',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'booking' => $booking,
            'product' => $product,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'jarum' => $jarum,
        ];
        return view(session()->get('role') . '/Booking/jarumbulan', $data);
    }


    public function detailbooking($idBooking)
    {
        $needle = $this->bookingModel->getNeedle($idBooking);
        $product = $this->productModel->findAll();
        $booking = $this->bookingModel->getDataById($idBooking);
        $totalMesin = $this->jarumModel->getTotalMesinByJarum();
        $childOrder = $this->orderModel->getChild($idBooking);
        $childBooking = $this->bookingModel->getChild($idBooking);
        $data = [
            'role' => session()->get('role'),
            'title' => 'Data Booking',
            'active1' => '',
            'active2' => 'active',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'booking' => $booking,
            'jarum' => $needle,
            'product' => $product,
            'jenisJarum' => $totalMesin,
            'childOrder' => $childOrder,
            'childBooking' => $childBooking

        ];
        return view(session()->get('role') . '/Booking/detail', $data);
    }
    public function order()
    {
        $totalMesin = $this->jarumModel->getTotalMesinByJarum();
        $data = [
            'role' => session()->get('role'),
            'title' => 'Data Order',
            'active1' => '',
            'active2' => '',
            'active3' => 'active',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'TotalMesin' => $totalMesin,
        ];
        return view(session()->get('role') . '/Order/ordermaster', $data);
    }
    public function semuaOrder()
    {
        $tampilperdelivery = $this->orderModel->tampilPerdelivery();
        $product = $this->productModel->findAll();
        $data = [
            'role' => session()->get('role'),
            'title' => 'Data Order',
            'active1' => '',
            'active2' => '',
            'active3' => 'active',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'tampildata' => $tampilperdelivery,
            'product' => $product,


        ];
        return view(session()->get('role') . '/Order/semuaorder', $data);
    }
    public function detailModel($noModel, $delivery)
    {
        $dataApsPerstyle = $this->ApsPerstyleModel->detailModel($noModel, $delivery);
        $dataMc = $this->jarumModel->getAreaModel($noModel);
        $data = [
            'role' => session()->get('role'),
            'title' => 'Data Order',
            'active1' => '',
            'active2' => '',
            'active3' => 'active',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'dataAps' => $dataApsPerstyle,
            'noModel' => $noModel,
            'delivery' => $delivery,
            'dataMc' => $dataMc,
        ];
        return view(session()->get('role') . '/Order/detailOrder', $data);
    }
    public function orderPerJarum()
    {
        $totalMesin = $this->jarumModel->getTotalMesinByJarum();
        $data = [
            'role' => session()->get('role'),
            'title' => 'Data Order',
            'active1' => '',
            'active2' => '',
            'active3' => 'active',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'TotalMesin' => $totalMesin,
        ];
        return view(session()->get('role') . '/Order/orderjarum', $data);
    }
    public function DetailOrderPerJarum($jarum)
    {
        $tampilperdelivery = $this->orderModel->tampilPerjarum($jarum);
        $product = $this->productModel->findAll();
        $booking = $data = [
            'role' => session()->get('role'),
            'title' => 'Data Order',
            'active1' => '',
            'active2' => '',
            'active3' => 'active',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'jarum' => $jarum,
            'tampildata' => $tampilperdelivery,
            'product' => $product,

        ];
        return view(session()->get('role') . '/Order/semuaorderjarum', $data);
    }
    public function detailmodeljarum($noModel, $delivery, $jarum)
    {
        $apsPerstyleModel = new ApsPerstyleModel(); // Create an instance of the model
        $dataApsPerstyle = $apsPerstyleModel->detailModelJarum($noModel, $delivery, $jarum); // Call the model method
        $data = [
            'role' => session()->get('role'),
            'title' => 'Data Order',
            'active1' => '',
            'active2' => '',
            'active3' => 'active',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'jarum' => $jarum,
            'dataAps' => $dataApsPerstyle,
            'noModel' => $noModel,
            'delivery' => $delivery,
        ];

        return view(session()->get('role') . '/Order/detailModelJarum', $data);
    }
    public function planningmesin()
    {
        $id = session()->get('id_user');
        $planarea = $this->KebutuhanAreaModel->findAll();
        $area = $this->aksesModel->getArea($id);
        $data = [
            'role' => session()->get('role'),
            'title' => 'Data Planning Area',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => 'active',
            'active7' => '',
            'planarea' => $planarea,
            'area' => $area,
        ];
        return view(session()->get('role') . '/Planning/PilihJudulArea', $data);
    }

    public function saveplanningmesin()
    {
        $validation = $this->validate([
            'judul' => 'required',
            'area' => 'required',
            'jarum' => 'required'
        ]);

        if (!$validation) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'All fields are required.'
            ]);
        }

        $data = [
            'role' => session()->get('role'),
            'judul' => $this->request->getPost('judul'),
            'area' => $this->request->getPost('area'),
            'jarum' => $this->request->getPost('jarum')
        ];
        $save = $this->KebutuhanAreaModel->save($data);
        if ($save) {
            $planarea = $this->KebutuhanAreaModel->findAll();
            $dataplan = $this->response->setJSON([
                'status' => 'success',
                'message' => 'Data saved successfully.',
                'planarea' => $planarea
            ]);
            return $dataplan;
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to save data.'
            ]);
        }
    }
    public function fetch_jarum()
    {
        try {
            $area = $this->request->getPost('area');
            $jarumData = $this->jarumModel->getJarumByArea($area);

            return $this->response->setJSON($jarumData);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setBody($e->getMessage());
        }
    }
    public function detailplanmc($id)
    {
        $detailplan = $this->DetailPlanningModel->getDataPlanning($id);
        // dd($detailplan);
        $judul = $this->request->getGet('judul');
        $area = $this->request->getGet('area');
        $jarum = $this->request->getGet('jarum');
        $mesinarea = $this->jarumModel->getMesinByArea($area, $jarum); //mesin yang dipakai semua mesin tanpa melibatkan head planning
        // $mesinplanning = $this->MesinPlanningModel->getMesinByArea($area,$jarum); //mesin yang dipilih oleh head planning di teruskan ke bagian aps
        $data = [
            'role' => session()->get('role'),
            'title' => 'Data Planning',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => 'active',
            'active7' => '',
            'detailplan' => $detailplan,
            'judul' => $judul,
            'area' => $area,
            'jarum' => $jarum,
            'mesin' => $mesinarea,
            'id_pln_mc' => $id,
        ];
        return view(session()->get('role') . '/Planning/fetchDataArea', $data);
    }

    public function orderPerJarumBln()
    {
        $totalMesin = $this->jarumModel->getTotalMesinByJarum();
        $data = [
            'role' => session()->get('role'),
            'title' => 'Data Order',
            'active1' => '',
            'active2' => '',
            'active3' => 'active',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'TotalMesin' => $totalMesin,
        ];
        return view(session()->get('role') . '/Order/orderjarumbln', $data);
    }
    public function orderBlmAdaAreal()
    {
        $tampilperdelivery = $this->orderModel->tampilPerModelBlmAdaArea();
        $product = $this->productModel->findAll();
        $data = [
            'role' => session()->get('role'),
            'title' => 'Data Order',
            'active1' => '',
            'active2' => '',
            'active3' => 'active',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'tampildata' => $tampilperdelivery,
            'product' => $product,

        ];
        return view(session()->get('role') . '/Order/orderBlmAdaArea', $data);
    }
    public function orderPerArea()
    {
        $id = session()->get('id_user');
        $area = $this->aksesModel->getArea($id);
        $totalMesin = $this->jarumModel->getArea();
        $data = [
            'role' => session()->get('role'),
            'title' => 'Data Order',
            'active1' => '',
            'active2' => '',
            'active3' => 'active',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'TotalMesin' => $area,
        ];
        return view(session()->get('role') . '/Order/orderarea', $data);
    }
    public function DetailOrderPerArea($area)
    {
        $tampilperdelivery = $this->orderModel->tampilPerarea($area);
        $product = $this->productModel->findAll();
        $booking = $data = [
            'role' => session()->get('role'),
            'title' => 'Data Order',
            'active1' => '',
            'active2' => '',
            'active3' => 'active',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'area' => $area,
            'tampildata' => $tampilperdelivery,
            'product' => $product,
        ];
        return view(session()->get('role') . '/Order/semuaorderarea', $data);
    }
    public function fetchdetailorderarea()
    {
        $area = $this->request->getGet('area');
        $jarum = $this->request->getGet('jarum');
        $id_pln_mc = $this->request->getGet('id_pln_mc');


        $data = $this->ApsPerstyleModel->getDetailPlanning($area, $jarum);

        foreach ($data as $row) {
            $row['id_pln_mc'] = $id_pln_mc;
            $this->DetailPlanningModel->insert($row);
        }
    }
    public function planningpage($id)
    {
        $area = $this->request->getGet('area');
        $jarum = $this->request->getGet('jarum');
        $mesin = $this->request->getGet('mesin');
        $judul = $this->request->getGet('judul');
        $idutama = $this->request->getGet('id_utama');
        $detailplan = $this->DetailPlanningModel->getDetailPlanning($id); //get data model with detail quantity,model etc.
        $listPlanning = $this->EstimatedPlanningModel->listPlanning($id); //get data planning per page and fetch it into datatable at bottom datatables
        // $mesinpertgl = $this->TanggalPlanningModel->getMesinByDate($idutama);//get data machine per date and return into array
        $data = [
            'role' => session()->get('role'),
            'title' => 'Data Order',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => 'active',
            'active7' => '',
            'area' => $area,
            'jarum' => $jarum,
            'mesin' => $mesin,
            'planning' => $detailplan,
            'listPlanning' => $listPlanning,
            'id_pln' => $idutama,
            'id_save' => $id,
            'judul' => $judul
        ];
        return view(session()->get('role') . '/Planning/operationPlanning', $data);
    }
    public function getDataLibur()
    {
        $startDate = $this->request->getPost('startDate');
        $endDate = $this->request->getPost('endDate');

        // Fetch total number of holidays between the given dates from the model
        $totalLibur = $this->liburModel->getTotalLiburBetweenDates($startDate, $endDate);

        // Return the total number of holidays as a JSON response
        return $this->response->setJSON(['status' => 'success', 'total_libur' => $totalLibur]);
    }

    public function getMesinByDate($id)
    {
        $date = $this->request->getGet('date');
        $machines = $this->TanggalPlanningModel->getMesinByDate($id, $date);

        // Check if machines data is empty
        if (empty($machines)) {
            $availableMachines = 0; // Return 1 if no machines are found
        } else {
            // Extract the sum of mesin
            $availableMachines = array_sum(array_column($machines, 'mesin'));
        }

        return $this->response->setJSON(['status' => 'success', 'available' => $availableMachines]);
    }

    public function saveplanning()
    {
        $model = $this->request->getPost('model');
        $delivery = $this->request->getPost('delivery');
        $qty = $this->request->getPost('qty');
        $sisa = $this->request->getPost('sisa');
        $target = $this->request->getPost('target_akhir');
        preg_match('/^[\d\.]+/', $target, $matches);
        $numericalTarget = $matches[0];
        $persentarget = $this->request->getPost('persen_target');
        $start = $this->request->getPost('start_date');
        $stop = $this->request->getPost('stop_date');
        $hari = $this->request->getPost('days_count');
        $mesin = $this->request->getPost('machine_usage');
        $est = round($this->request->getPost('estimated_qty'));
        $id_save = $this->request->getPost('id_save');
        $id_pln = $this->request->getPost('id_pln');
        $mc = $this->request->getPost('mesin');
        $area = $this->request->getPost('area');
        $jrm = $this->request->getPost('jarum');
        $judul = $this->request->getPost('judul');


        $startDateTime = new \DateTime($start);
        $stopDateTime = new \DateTime($stop);

        $interval = new \DateInterval('P1D'); // 1 day interval
        $datePeriod = new \DatePeriod($startDateTime, $interval, $stopDateTime->modify('+1 day')); // +1 day to include the end date

        $libur = $this->liburModel->findAll();
        $holidayDates = array_column($libur, 'tanggal');

        $dataestqty = [
            'id_detail_pln' => $id_save,
            'Est_qty' => $est,
            'hari' => $hari,
            'target' => $numericalTarget,
            'precentage_target' => $persentarget,
        ];
        $saveest = $this->EstimatedPlanningModel->insert($dataestqty);
        $idOrder = $this->EstimatedPlanningModel->getId($id_save);

        foreach ($datePeriod as $date) {
            $formattedDate = $date->format('Y-m-d');
            if (in_array($formattedDate, $holidayDates)) {
                continue; // Skip this date
            }
            $data = [
                'role' => session()->get('role'),
                'id_detail_pln' => $id_save,
                'id_est_qty' => $idOrder,
                'date' => $date->format('Y-m-d'), // Insert the current date in the range
                'mesin' => $mesin,
            ];
            $this->TanggalPlanningModel->insert($data);
        }



        if ($saveest) {
            return redirect()->to(base_url(session()->get('role') . '/planningpage/' . $id_save . '?id_utama=' . $id_pln . '?mesin=' . $mc . '&area=' . $area . '&jarum=' . $jrm . '&judul=' . $judul))->withInput()->with('success', 'Data Berhasil Disimpan');
        } else {
            return redirect()->to(base_url(session()->get('role') . '/planningpage/' . $id_save . '?id_utama=' . $id_pln . '?mesin=' . $mc . '&area=' . $area . '&jarum=' . $jrm . '&judul=' . $judul))->withInput()->with('error', 'Data Gagal Disimpan');
        }


        dd($dataestqty);
    }
}
