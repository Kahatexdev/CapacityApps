<?php

namespace App\Controllers;
use DateTime;
use App\Controllers\BaseController;
use App\Models\AksesModel;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\DataMesinModel;
use App\Models\OrderModel;
use App\Models\BookingModel;
use App\Models\ProductTypeModel;
use App\Models\ApsPerstyleModel;
use App\Models\ProduksiModel;
use App\Models\CylinderModel;
use PhpOffice\PhpSpreadsheet\IOFactory;


class MesinController extends BaseController
{
    protected $filters;
    protected $jarumModel;
    protected $productModel;
    protected $produksiModel;
    protected $bookingModel;
    protected $orderModel;
    protected $ApsPerstyleModel;
    protected $cylinderModel;
    protected $aksesModel;

    public function __construct()
    {
        $this->aksesModel = new AksesModel();
        $this->jarumModel = new DataMesinModel();
        $this->bookingModel = new BookingModel();
        $this->productModel = new ProductTypeModel();
        $this->produksiModel = new ProduksiModel();
        $this->orderModel = new OrderModel();
        $this->cylinderModel = new cylinderModel();
        $this->ApsPerstyleModel = new ApsPerstyleModel();
        if ($this->filters   = ['role' => ['capacity'], 'god', 'sudo'] != session()->get('role')) {
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
        $data = [
            'role' => session()->get('role'),
            'title' => 'Data Mesin',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => 'active',
            'active6' => '',
            'active7' => '',
        ];
        return view(session()->get('role') . '/Mesin/Mastermesin', $data);
    }
    public function mesinPerJarum($pu)
    {
        $totalMesin = $this->jarumModel->getTotalMesinByJarum2($pu);
        $data = [
            'role' => session()->get('role'),
            'title' => 'Data Mesin',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => 'active',
            'active6' => '',
            'active7' => '',
            'pu' => $pu,
            'TotalMesin' => $totalMesin,
        ];
        return view(session()->get('role') . '/Mesin/mesinjarum', $data);
    }
    public function mesinPerJarumPlan($pu)
    {
        $totalMesin = $this->jarumModel->getTotalMesinByJarum2($pu);
        $data = [
            'role' => session()->get('role'),
            'title' => 'Data Mesin',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => 'active',
            'active6' => '',
            'active7' => '',
            'pu' => $pu,
            'TotalMesin' => $totalMesin,
        ];
        return view(session()->get('role') . '/Mesin/mesinjarum', $data);
    }
    public function stockcylinder()
    {
        $totalCylinder = $this->cylinderModel->findAll();
        $data = [
            'role' => session()->get('role'),
            'title' => 'Data Cylinder',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => 'active',
            'active6' => '',
            'active7' => '',
            'tampildata' => $totalCylinder,
        ];
        return view(session()->get('role') . '/Mesin/dataCylinder', $data);
    }
    public function stockcylinderPlan()
    {
        $totalCylinder = $this->cylinderModel->findAll();
        $data = [
            'role' => session()->get('role'),
            'title' => 'Data Cylinder',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => 'active',
            'active6' => '',
            'active7' => '',
            'tampildata' => $totalCylinder,
        ];
        return view(session()->get('role') . '/Mesin/dataCylinder', $data);
    }
    public function mesinperarea($pu)
    {
        $tampilperarea = $this->jarumModel->getArea2($pu);
        $product = $this->productModel->findAll();
        $data = [
            'role' => session()->get('role'),
            'title' => 'Data Mesin',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => 'active',
            'active6' => '',
            'active7' => '',
            'tampildata' => $tampilperarea,
            'product' => $product,

        ];
        return view(session()->get('role') . '/Mesin/mesinarea', $data);
    }
    public function allmachine()
    {
        $tampilperarea = $this->jarumModel->getAllMachine();
        $data = [
            'role' => session()->get('role'),
            'title' => 'Data Mesin',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => 'active',
            'active6' => '',
            'active7' => '',
            'tampildata' => $tampilperarea,
        ];
        return view(session()->get('role') . '/Mesin/allmesin', $data);
    }
    public function allmachinePlan()
    {
        $tampilperarea = $this->jarumModel->getAllMachine();
        $data = [
            'role' => session()->get('role'),
            'title' => 'Data Mesin',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => 'active',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'tampildata' => $tampilperarea,
        ];
        return view(session()->get('role') . '/Mesin/allmesin', $data);
    }
    public function DetailMesinPerJarum($jarum, $pu)
    {
        $tampilperarea = $this->jarumModel->getMesinPerJarum($jarum, $pu);
        $data = [
            'role' => session()->get('role'),
            'title' => 'Data Mesin',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => 'active',
            'active6' => '',
            'active7' => '',
            'pu' => $pu,
            'jarum' => $jarum,
            'tampildata' => $tampilperarea,
        ];

        return view(session()->get('role') . '/Mesin/detailMesinJarum', $data);
    }
    public function DetailMesinPerArea($area)
    {
        $tampilperarea = $this->jarumModel->getJarumArea($area);
        $getPU = $this->jarumModel->getpu($area);
        $data = [
            'role' => session()->get('role'),
            'title' => 'Data Mesin',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => 'active',
            'active6' => '',
            'active7' => '',
            'area' => $area,
            'pu' => $getPU,
            'tampildata' => $tampilperarea,
        ];

        return view(session()->get('role') . '/Mesin/detailMesinArea', $data);
    }
    public function inputmesinperarea()
    {
        $area = $this->request->getPost("area");
        $jarum = $this->request->getPost("jarum");
        $total_mc = $this->request->getPost("total_mc");
        $brand = $this->request->getPost("brand");
        $mesin_jalan = $this->request->getPost("mesin_jalan");

        $input = [
            'area' => $area,
            'jarum' => $jarum,
            'total_mc' => $total_mc,
            'brand' => $brand,
            'mesin_jalan' => $mesin_jalan,
        ];

        $insert =   $this->jarumModel->insert($input);
        if ($insert) {
            return redirect()->to(base_url(session()->get('role') . '/datamesinperjarum/' . $area))->withInput()->with('success', 'Data Berhasil Di Input');
        } else {
            return redirect()->to(base_url(session()->get('role') . '/datamesinperjarum/' . $area))->withInput()->with('error', 'Data Gagal Di Input');
        }
    }
    public function inputcylinder()
    {
        $area = $this->request->getPost("needle");
        $jarum = $this->request->getPost("production_unit");
        $total_mc = $this->request->getPost("type_machine");
        $brand = $this->request->getPost("qty");
        $mesin_jalan = $this->request->getPost("needle_detail");

        $input = [
            'needle' => $area,
            'production_unit' => $jarum,
            'type_machine' => $total_mc,
            'qty' => $brand,
            'needle_detail' => $mesin_jalan,
        ];

        $insert =   $this->cylinderModel->insert($input);
        if ($insert) {
            return redirect()->to(base_url(session()->get('role') . '/stockcylinder/'))->withInput()->with('success', 'Data Berhasil Di Input');
        } else {
            return redirect()->to(base_url(session()->get('role') . '/stockcylinder/'))->withInput()->with('error', 'Data Gagal Di Input');
        }
    }
    public function editcylinder($idDataCylinder)
    {

        $data = [
            'role' => session()->get('role'),
            'needle' => $this->request->getPost("needle"),
            'production_unit' => $this->request->getPost("production_unit"),
            'type_machine' => $this->request->getPost("type_machine"),
            'qty' => $this->request->getPost("qty"),
            'needle_detail' => $this->request->getPost("needle_detail"),
        ];
        $id = $idDataCylinder;
        $update = $this->cylinderModel->update($id, $data);
        if ($update) {
            return redirect()->to(base_url(session()->get('role') . '/stockcylinder/'))->withInput()->with('success', 'Data Berhasil Di Update');
        } else {
            return redirect()->to(base_url(session()->get('role') . '/stockcylinder/'))->withInput()->with('error', 'Gagal Update Data');
        }
    }
    public function inputcylinderPlan()
    {
        $area = $this->request->getPost("needle");
        $jarum = $this->request->getPost("production_unit");
        $total_mc = $this->request->getPost("type_machine");
        $brand = $this->request->getPost("qty");
        $mesin_jalan = $this->request->getPost("needle_detail");

        $input = [
            'needle' => $area,
            'production_unit' => $jarum,
            'type_machine' => $total_mc,
            'qty' => $brand,
            'needle_detail' => $mesin_jalan,
        ];

        $insert =   $this->cylinderModel->insert($input);
        if ($insert) {
            return redirect()->to(base_url(session()->get('role') . '/stockcylinder/'))->withInput()->with('success', 'Data Berhasil Di Input');
        } else {
            return redirect()->to(base_url(session()->get('role') . '/stockcylinder/'))->withInput()->with('error', 'Data Gagal Di Input');
        }
    }
    public function editcylinderPlan($idDataCylinder)
    {

        $data = [
            'role' => session()->get('role'),
            'needle' => $this->request->getPost("needle"),
            'production_unit' => $this->request->getPost("production_unit"),
            'type_machine' => $this->request->getPost("type_machine"),
            'qty' => $this->request->getPost("qty"),
            'needle_detail' => $this->request->getPost("needle_detail"),
        ];
        $id = $idDataCylinder;
        $update = $this->cylinderModel->update($id, $data);
        if ($update) {
            return redirect()->to(base_url(session()->get('role') . '/stockcylinder/'))->withInput()->with('success', 'Data Berhasil Di Update');
        } else {
            return redirect()->to(base_url(session()->get('role') . '/stockcylinder/'))->withInput()->with('error', 'Gagal Update Data');
        }
    }
    public function updatemesinperjarum($idDataMesin)
    {

        $data = [
            'role' => session()->get('role'),
            'total_mesin' => $this->request->getPost("total_mc"),
            'brand' => $this->request->getPost("brand"),
            'mesin_jalan' => $this->request->getPost("mesin_jalan"),
        ];
        $id = $idDataMesin;
        $update = $this->jarumModel->update($id, $data);
        $area = $this->request->getPost("area");
        if ($update) {
            return redirect()->to(base_url(session()->get('role') . '/datamesinperjarum/' . $area))->withInput()->with('success', 'Data Berhasil Di Update');
        } else {
            return redirect()->to(base_url(session()->get('role') . '/datamesinperjarum/' . $area))->withInput()->with('error', 'Gagal Update Data');
        }
    }
    public function deletemesinareal($idDataMesin)
    {
        $delete = $this->jarumModel->delete($idDataMesin);
        if ($delete) {
            return redirect()->to(base_url(session()->get('role') . '/datamesin'))->withInput()->with('success', 'Data Berhasil Di Hapus');
        } else {
            return redirect()->to(base_url(session()->get('role') . '/datamesin'))->withInput()->with('error', 'Gagal Hapus Data');
        }
    }
    public function deletecylinder($idDataCylinder)
    {
        $delete = $this->cylinderModel->delete($idDataCylinder);
        if ($delete) {
            return redirect()->to(base_url(session()->get('role') . '/stockcylinder'))->withInput()->with('success', 'Data Berhasil Di Hapus');
        } else {
            return redirect()->to(base_url(session()->get('role') . '/stockcylinder'))->withInput()->with('error', 'Gagal Hapus Data');
        }
    }
    public function deletecylinderPlan($idDataCylinder)
    {
        $delete = $this->cylinderModel->delete($idDataCylinder);
        if ($delete) {
            return redirect()->to(base_url(session()->get('role') . '/stockcylinder'))->withInput()->with('success', 'Data Berhasil Di Hapus');
        } else {
            return redirect()->to(base_url(session()->get('role') . '/stockcylinder'))->withInput()->with('error', 'Gagal Hapus Data');
        }
    }

    // planning
    public function indexPlan()
    {
        $data = [
            'role' => session()->get('role'),
            'title' => 'Data Mesin',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => 'active',
            'active5' => '',
            'active6' => '',
            'active7' => '',
        ];
        return view(session()->get('role') . '/Mesin/index', $data);
    }
    public function mesinperareaPlan($pu)
    {
        $tampilperarea = $this->jarumModel->getArea2($pu);
        $product = $this->productModel->findAll();
        $data = [
            'role' => session()->get('role'),
            'title' => 'Data Mesin',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => 'active',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'tampildata' => $tampilperarea,
            'product' => $product,

        ];
        return view(session()->get('role') . '/Mesin/mesinarea', $data);
    }
    public function mesinperareaAps()
    {
        $id = session()->get('id_user');
        $area = $this->aksesModel->getArea($id);
        $product = $this->productModel->findAll();
        $data = [
            'role' => session()->get('role'),
            'title' => 'Data Mesin',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => 'active',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'tampildata' => $area,
            'product' => $product,

        ];
        return view(session()->get('role') . '/Mesin/mesinarea', $data);
    }
    public function DetailMesinPerAreaPlan($area)
    {
        $tampilperarea = $this->jarumModel->getJarumArea($area);
        $getPU = $this->jarumModel->getpu($area);
        $data = [
            'role' => session()->get('role'),
            'title' => 'Data Mesin',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => 'active',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'area' => $area,
            'pu' => $getPU,
            'tampildata' => $tampilperarea,
        ];

        return view(session()->get('role') . '/Mesin/detailMesinArea', $data);
    }
    public function updatemesinperjarumPlan($idDataMesin)
    {


        $data = [
            'role' => session()->get('role'),
            'total_mc' => $this->request->getPost("total_mc"),
            'brand' => $this->request->getPost("brand"),
            'mesin_jalan' => $this->request->getPost("mesin_jalan"),
        ];
        $id = $idDataMesin;
        $update = $this->jarumModel->update($id, $data);
        $area = $this->request->getPost("area");
        if ($update) {
            return redirect()->to(base_url(session()->get('role') . '/datamesinperarea/' . $area))->withInput()->with('success', 'Data Berhasil Di Update');
        } else {
            return redirect()->to(base_url(session()->get('role') . '/datamesinperarea/' . $area))->withInput()->with('error', 'Gagal Update Data');
        }
    }
    public function deletemesinarealPlan($idDataMesin)
    {
        $delete = $this->jarumModel->delete($idDataMesin);
        if ($delete) {
            return redirect()->to(base_url(session()->get('role') . '/datamesinPlan'))->withInput()->with('success', 'Data Berhasil Di Hapus');
        } else {
            return redirect()->to(base_url(session()->get('role') . '/datamesinPlan'))->withInput()->with('error', 'Gagal Hapus Data');
        }
    }
    public function inputmesinperjarumPlan()
    {
        $area = $this->request->getPost("area");
        $jarum = $this->request->getPost("jarum");
        $total_mc = $this->request->getPost("total_mc");
        $brand = $this->request->getPost("brand");
        $mesin_jalan = $this->request->getPost("mesin_jalan");
        $pu = $this->request->getPost("production_unit");
        $input = [
            'area' => $area,
            'jarum' => $jarum,
            'total_mc' => $total_mc,
            'brand' => $brand,
            'mesin_jalan' => $mesin_jalan,
        ];

        $insert =   $this->jarumModel->insert($input);
        if ($insert) {
            return redirect()->to(base_url(session()->get('role') . '/datamesinperjarum/' . $jarum) . '/' . $pu)->withInput()->with('success', 'Data Berhasil Di Input');
        } else {
            return redirect()->to(base_url(session()->get('role') . '/datamesinperjarum/' . $jarum . '/' . $pu))->withInput()->with('error', 'Data Gagal Di Input');
        }
    }
    public function inputmesinperareaPlan()
    {
        $area = $this->request->getPost("area");
        $jarum = $this->request->getPost("jarum");
        $total_mc = $this->request->getPost("total_mc");
        $brand = $this->request->getPost("brand");
        $mesin_jalan = $this->request->getPost("mesin_jalan");
        $pu = $this->request->getPost("production_unit");
        $input = [
            'area' => $area,
            'jarum' => $jarum,
            'total_mc' => $total_mc,
            'brand' => $brand,
            'mesin_jalan' => $mesin_jalan,
        ];

        $insert =   $this->jarumModel->insert($input);
        if ($insert) {
            return redirect()->to(base_url(session()->get('role') . '/datamesinperarea/' . $area))->withInput()->with('success', 'Data Berhasil Di Input');
        } else {
            return redirect()->to(base_url(session()->get('role') . '/datamesinperarea/' . $area))->withInput()->with('error', 'Data Gagal Di Input');
        }
    }
    public function DetailMesinPerJarumPlan($jarum, $pu)
    {
        $tampilperarea = $this->jarumModel->getMesinPerJarum($jarum, $pu);
        $data = [
            'role' => session()->get('role'),
            'title' => 'Data Mesin',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => 'active',
            'active6' => '',
            'active7' => '',
            'pu' => $pu,
            'jarum' => $jarum,
            'tampildata' => $tampilperarea,
        ];

        return view(session()->get('role') . '/Mesin/detailMesinJarum', $data);
    }
    public function capacityperarea($area)
{
    $targetInput = $this->request->getPost('target');
    $today = new DateTime();
    $today->setTime(0, 0);  // Ensuring the time is set to midnight
    $jarum = $this->request->getPost('jarum');
    $listjarum = $this->jarumModel->getJarumByArea($area);
    $maxCapacity = $this->jarumModel->maxCapacity($area, $jarum);
    $capacity = $this->ApsPerstyleModel->CapacityArea($area, $jarum);

    $orderWeek = [];
    $totalProduksi = 0;
    $totalKebMesin = 0;
    $calendar = [];
    $startWeek = 1;

    // Creating the calendar starting from today
    $currentDate = clone $today;
    $firstWeekDays = [];
    $dayOfWeek = (int)$currentDate->format('N'); // Day of the week (1 for Monday, 7 for Sunday)

    // Fill the first week starting from today
    for ($day = $dayOfWeek; $day <= 7; $day++) {
        $firstWeekDays[] = $currentDate->format('Y-m-d');
        $currentDate->modify('+1 day');
    }
    $calendar[$startWeek] = $firstWeekDays;

    // Generate subsequent weeks from Monday to Sunday
    for ($week = $startWeek + 1; $week <= 12; $week++) {
        $weekDays = [];
        for ($day = 1; $day <= 7; $day++) {
            $weekDays[] = $currentDate->format('Y-m-d');
            $currentDate->modify('+1 day');
        }
        $calendar[$week] = $weekDays;
    }

    // Initialize weekly production and machines array
    $weeklyProduction = array_fill($startWeek, 12, 0);
    $weeklyMachines = array_fill($startWeek, 12, 0);

    foreach ($capacity as $row) {
        $smv = $row['smv'];
        $targetPerMesin = ceil((86400 / ($smv * 0.8)) / 24);
        $sisa = ceil($row['sisa']/24);
        $deliveryDate = new DateTime($row['delivery']);
        $time = $today->diff($deliveryDate);
        $leadtime = max($time->days, 1);  // Ensuring leadtime is at least 1 to avoid division by zero

        // Calculate weekly production and machine needs
        $kebMesin = ceil($sisa / ($leadtime * $targetPerMesin));
        $produksi = $targetPerMesin * $kebMesin;

        $totalProduksi += $produksi;
        $totalKebMesin += $kebMesin;

        $produksiHarian = [];
        $sisaOrder = $sisa;

        for ($i = 0; $i < $leadtime; $i++) {
            if ($sisaOrder <= 0) {
                break;
            }
            // $produksiHariIni = min($targetPerMesin, $sisaOrder);
            $produksiHariIni = $produksi;
            $sisaOrder -= $produksiHariIni;
            $produksiHarian[] = [
                'hari' => $i + 1,
                'produksi' => $produksiHariIni,
                'kebMesin' => $kebMesin
            ];

            // Menghitung produksi mingguan
            $currentWeek = floor($i / 7) + $startWeek;
            $weeklyProduction[$currentWeek] += $produksiHariIni;
            $weeklyMachines[$currentWeek] += $kebMesin;
        }

        $orderWeek[] = [
            'PDK' => $row['mastermodel'],
            'sisa' => $sisa,
            'leadtime' => $leadtime,
            'targetPerMesin' => $targetPerMesin,
            'produksi' => $produksi,
            'kebMesin' => $kebMesin,
            'produksiHarian' => $produksiHarian
        ];
    }

    // Calculate max capacity per week
    $maxCapacityPerWeek = $maxCapacity['totalmesin'] * 7 * $targetInput;
    // Calculate available capacity per week
    $availableCapacity = [];
    foreach ($weeklyProduction as $week => $production) {
        $availableCapacity[$week] = $maxCapacityPerWeek - $production;
    }

    // Calculate available machines per week
    $availableMachines = [];
    foreach ($weeklyMachines as $week => $machines) {
        $averageMachinesUsed = $machines / 7;
        $availableMachines[$week] = $maxCapacity['totalmesin'] - $averageMachinesUsed;
    }
        $tampilperarea = $this->jarumModel->getJarumArea($area);
        $getPU = $this->jarumModel->getpu($area);
        $data = [
           'role' => session()->get('role'),
        'title' => 'Data Mesin',
        'active1' => '',
        'active2' => '',
        'active3' => '',
        'active4' => '',
        'active5' => 'active',
        'active6' => '',
        'active7' => '',
        'listjarum' => $listjarum,
        'area' => $area,
        'jarum' => $jarum,
        'max'=>$maxCapacityPerWeek,
        'headerData' => $maxCapacity,
        'orderWeek' => $orderWeek,
        'weeklyProduction' => $weeklyProduction,
        'calendar' => $calendar,
        'availableMachines' => $availableMachines,
        'availableCapacity' => $availableCapacity,
            'pu' => $getPU,
            'jarum' => $jarum,
            'tampildata' => $tampilperarea,
            'startWeek' => $startWeek
        ];
    
        return view(session()->get('role') . '/Mesin/capacityarea', $data);
    }
    
    
}
