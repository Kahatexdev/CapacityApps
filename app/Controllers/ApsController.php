<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use Datetime;

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
use App\Models\MesinPerStyle;
use App\Models\AksesModel;/*  */
use App\Services\orderServices;
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
    protected $orderServices;
    protected $MesinPerStyle;

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
        $this->MesinPerStyle = new MesinPerStyle();
        $this->orderServices = new orderServices();
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
            // Dapatkan data progress per area
            $areaProgress = $this->ApsPerstyleModel->getProgressperArea($ar);
            $lastmonth = date('Y-m-d', strtotime('- 1 month'));

            // Grup by mastermodel
            $grouped = [];
            foreach ($areaProgress as $item) {
                $model = $item['mastermodel'];
                if (!isset($grouped[$model])) {
                    $grouped[$model] = [
                        'mastermodel' => $model,
                        'target' => 0,
                        'remain' => 0,
                        'delivery' => $item['delivery'],
                        'percentage' => 0,
                    ];
                }

                // Jumlahkan target dan remain
                $grouped[$model]['target'] += (int)$item['target'];
                $grouped[$model]['remain'] += (int)$item['remain'];
                $produksi = $grouped[$model]['target'] - $grouped[$model]['remain'];

                // Hitung percentage hanya jika produksi > 0
                if ($produksi > 0) {
                    $grouped[$model]['percentage'] = round(($produksi / $grouped[$model]['target']) * 100);
                }

                // Ambil delivery paling akhir
                if ($grouped[$model]['delivery'] < $item['delivery']) {
                    $grouped[$model]['delivery'] = $item['delivery'];
                }
            }

            // Filter yang delivery >= hari ini
            $filtered = array_filter($grouped, function ($item) use ($lastmonth) {
                return $item['delivery'] >= $lastmonth;
            });
            usort($filtered, function ($a, $b) {
                return $a['percentage'] <=> $b['percentage'];
            });

            // Simpan hasil per area
            $pdk[$ar] = $filtered;
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
    public function progressdetail($model, $area)
    {
        $pdk = [];
        $pdkProg = $this->ApsPerstyleModel->getProgressDetail($model, $area);
        $today = date('Y-m-d');

        // Grup by mastermodel dan machinetypeid (jarum)
        $groupedDetail = [];
        foreach ($pdkProg as $item) {
            $model = $item['mastermodel'];
            $jarum = $item['machinetypeid'];  // Mengelompokkan juga berdasarkan jarum
            // Gabungkan mastermodel dan jarum sebagai kunci
            $key = $jarum;

            if (!isset($groupedDetail[$key])) {
                $groupedDetail[$key] = [
                    'mastermodel' => $model,
                    'jarum' => $jarum,
                    'target' => 0,
                    'remain' => 0,
                    'delivery' => $item['delivery'],
                    'percentage' => 0,
                    'detail' => [] // Tambahin array buat detail
                ];
            }

            // Jumlahkan target dan remain dengan tipe float untuk mengakomodasi desimal
            $groupedDetail[$key]['target'] += (float)$item['target'];
            $groupedDetail[$key]['remain'] += (float)$item['remain'];
            $produksi = $groupedDetail[$key]['target'] - $groupedDetail[$key]['remain'];

            // Hitung percentage hanya jika produksi > 0
            if ($produksi > 0) {
                $groupedDetail[$key]['percentage'] = round(($produksi / $groupedDetail[$key]['target']) * 100);
            }

            // Ambil data progress per delivery
            $progresPerDeliv = $this->ApsPerstyleModel->getProgresPerdeliv($model, $area, $jarum);
            foreach ($progresPerDeliv as $dlv) {
                $cek = [
                    'model' => $model,
                    'area' => $area,
                    'jarum' => $jarum,
                    'delivery' => $dlv['delivery']
                ];

                // Ambil detail progress per ukuran (size)
                $sizes = $this->ApsPerstyleModel->progressdetail($cek);

                // Assign size ke dalam detail delivery yang sesuai
                $groupedDetail[$key]['detail'][$dlv['delivery']] = [
                    'mastermodel' => $model,
                    'jarum' => $jarum,
                    'target' => (float)$dlv['target'],
                    'remain' => (float)$dlv['remain'],
                    'delivery' => $dlv['delivery'],
                    'percentage' => round((($dlv['target'] - $dlv['remain']) / $dlv['target']) * 100),
                    'size' => $sizes // Assign detail size ke dalam delivery
                ];
            }
        }


        usort($groupedDetail, function ($a, $b) {
            return $a['percentage'] <=> $b['percentage'];
        });

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
            'area' => $area,
            'model' => $model,
            'perjarum' => $groupedDetail,

        ];
        return view(session()->get('role') . '/Order/progressdetail', $data);
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
        $area = $this->aksesModel->getArea($id);
        $planarea = [];
        foreach ($area as $ar) {
            $planarea[] = $this->KebutuhanAreaModel->getDatabyArea($ar);
        }
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
        foreach ($detailplan as &$dp) {
            $iddetail = $dp['id_detail_pln'];
            $qtysisa = $this->ApsPerstyleModel->getSisaPerModel($dp['model'], $dp['jarum']);
            $mesin = $this->TanggalPlanningModel->totalMc($iddetail);
            $jum = 0;
            foreach ($mesin as $mc) {
                $jum += $mc['mesin'];
            }

            $dp['delivery'] = date('d-M-y', strtotime($qtysisa['delivery']));
            $dp['mesin'] = $jum;
            $dp['qty'] = round($qtysisa['qty']);
            $dp['sisa'] =
                round($qtysisa['sisa']);
        }
        usort($detailplan, function ($a, $b) {
            return strtotime($a['delivery']) - strtotime($b['delivery']);
        });

        $kebutuhanArea = $this->KebutuhanAreaModel->where('id_pln_mc', $id)->first();
        $judul = $kebutuhanArea['judul'];
        $area = $kebutuhanArea['area'];
        $jarum =  $kebutuhanArea['jarum'];
        $mesinarea = $this->jarumModel->getMesinByArea($area, $jarum); //mesin yang dipakai semua mesin tanpa melibatkan head planning
        // $mesinplanning = $this->MesinPlanningModel->getMesinByArea($area,$jarum); //mesin yang dipilih oleh head planning di teruskan ke bagian aps
        $jarumList = $this->KebutuhanAreaModel->getDataByAreaGroupJrm($area);
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
            'jarumList' => $jarumList
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

        if ($area == 'KK8J') {
            $data = $this->ApsPerstyleModel->getDetailPlanningGloves($area, $jarum);
        } else {
            $data = $this->ApsPerstyleModel->getDetailPlanning($area, $jarum);
        }

        // Cek jika data kosong, langsung return atau berikan pesan
        if (empty($data)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'No data found for the given area and needle type.' . $area,
            ]);
        }

        foreach ($data as $row) {
            $row['id_pln_mc'] = $id_pln_mc;
            $model = $row['model'];
            $sisa = $row['sisa'];
            $qty = $row['qty'];
            $validate = [
                'id' => $row['id_pln_mc'],
                'model' => $model,
            ];
            $insert = [
                'id_pln_mc' => $row['id_pln_mc'],
                'model' => $model,
                'smv' => $row['smv'],
                'jarum' => $row['machinetypeid'],
                'status' => 'aktif',
                'delivery' => 'delivery',
            ];
            $cek = $this->DetailPlanningModel->cekPlanning($validate);
            if (!$cek) {
                $this->DetailPlanningModel->insert($insert);
            }
        }
    }
    public function planningpage($id, $idutama)
    {
        $kebutuhanArea = $this->KebutuhanAreaModel->where('id_pln_mc', $idutama)->first();
        $judul = $kebutuhanArea['judul'];
        $area = $kebutuhanArea['area'];
        $jarum =  $kebutuhanArea['jarum'];
        // $detailplan = $this->DetailPlanningModel->getDetailPlanning($id); //get data model with detail quantity,model etc.
        $pdk = $this->DetailPlanningModel->detailPdk($id);
        $listDeliv = $this->ApsPerstyleModel->getDetailPerDeliv($pdk);
        $listPlanning = $this->EstimatedPlanningModel->listPlanning($id);
        // dd($listPlanning);
        // $mesinpertgl = $this->TanggalPlanningModel->getMesinByDate($idutama);//get data machine per date and return into array
        $mesin = $this->jarumModel->getMesinByArea($area, $jarum);
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
            'pdk' => $pdk['model'],
            'listDeliv' => $listDeliv,
            //'planning' => $detailplan,
            'listPlanning' => $listPlanning,
            'mesin' => $mesin,
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
        $id_est = $this->request->getPost('id_est');
        $model = $this->request->getPost('model');
        $delivery = $this->request->getPost('delivery');
        $qty = $this->request->getPost('qty');
        $sisa = $this->request->getPost('sisa');
        $target = $this->request->getPost('target_akhir');
        $keterangan = $this->request->getPost('keterangan');
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
            'delivery' => $delivery,
            'keterangan' => $keterangan
        ];

        // Cek apakah ID ada (proses edit atau save baru)
        if ($id_est) {
            // Update data
            $saveest = $this->EstimatedPlanningModel->update($id_est, $dataestqty);
            $idOrder = $id_est; // ID tetap sama untuk update
        } else {
            // Insert data baru
            $saveest = $this->EstimatedPlanningModel->insert($dataestqty);
            $idOrder = $this->EstimatedPlanningModel->getInsertID(); // Ambil ID setelah insert
        }

        // Query data dari model
        $select = $this->TanggalPlanningModel
            ->where('id_est_qty', $id_est)
            ->where('id_detail_pln', $id_save)
            ->findAll();

        // Cek apakah data ditemukan
        if (!empty($select)) {
            // Jika data ditemukan, hapus data lama
            $delete = $this->TanggalPlanningModel
                ->where('id_est_qty', $id_est)
                ->where('id_detail_pln', $id_save)
                ->delete();

            if ($delete) {
                // Insert ulang data berdasarkan periode tanggal
                foreach ($datePeriod as $date) {
                    $formattedDate = $date->format('Y-m-d');
                    if (in_array($formattedDate, $holidayDates)) {
                        continue; // Lewati tanggal libur
                    }

                    $data = [
                        'role' => session()->get('role'),
                        'id_detail_pln' => $id_save,
                        'id_est_qty' => $idOrder,
                        'date' => $formattedDate, // Masukkan tanggal
                        'mesin' => $mesin,
                        'start_mesin' => $start,
                        'stop_mesin' => $stop,
                    ];
                    $this->TanggalPlanningModel->insert($data);
                }
            }
        } else {
            // Jika data tidak ditemukan, langsung insert data baru
            foreach ($datePeriod as $date) {
                $formattedDate = $date->format('Y-m-d');
                if (in_array($formattedDate, $holidayDates)) {
                    continue; // Lewati tanggal libur
                }

                $data = [
                    'role' => session()->get('role'),
                    'id_detail_pln' => $id_save,
                    'id_est_qty' => $idOrder,
                    'date' => $formattedDate, // Masukkan tanggal
                    'mesin' => $mesin,
                    'start_mesin' => $start,
                    'stop_mesin' => $stop,
                ];
                $this->TanggalPlanningModel->insert($data);
            }
        }

        if ($formattedDate) {
            return redirect()->to(base_url(session()->get('role') . '/planningpage/' . $id_save . '/' . $id_pln))->withInput()->with('success', 'Data Berhasil Disimpan');
        } else {
            return redirect()->to(base_url(session()->get('role') . '/planningpage/' . $id_save . '/' . $id_pln))->withInput()->with('error', 'Data Gagal Disimpan');
        }
    }
    public function kalenderMesin($id)
    {
        $role = session()->get('role');
        $today = date('Y-m-d', strtotime('today'));
        $hariIni = new DateTime($today);
        $kebutuhanArea = $this->KebutuhanAreaModel->where('id_pln_mc', $id)->first();
        $judul = $kebutuhanArea['judul'];
        $area = $kebutuhanArea['area'];
        $jarum =  $kebutuhanArea['jarum'];
        $mesin = $this->jarumModel->getMesinByArea($area, $jarum);
        $pdkList = $this->DetailPlanningModel->pdkList($id);
        $mesinPerDay = [];
        foreach ($pdkList as $pdk) {
            $idPdk = $pdk['id_detail_pln'];
            $mesinPerDay[] = $this->TanggalPlanningModel->dailyMachine($idPdk);
        }
        $libur = $this->liburModel->findAll();
        $merah = [];
        foreach ($libur as $list) {
            $merah[] = [
                'title' => $list['nama'],
                'start' => $list['tanggal'],
                'className' => 'event-holiday'
            ];
        }
        $jadwal = $this->getJadwalMesin($mesinPerDay, $mesin);
        $events = [];
        foreach ($jadwal as $item) {
            $events[] = [
                'title' => "Used: " . $item['mesin'] . "\n Available :" . $item['avail'],
                'start' => $item['date'], // format date harus sesuai dengan format FullCalendar
                'desk' => json_encode($item['deskripsi']),
                'className' => 'event-normal',
            ];
        }


        $data = [
            'role' => session()->get('role'),
            'title' => 'Jalan Mesin',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'area' => $area,
            'jarum' => $jarum,
            'mesin' => $mesin,
            'id' => $id,
            'today' => $today,
            'events' => json_encode($events),
            'libur' => json_encode($merah)

        ];
        return view($role . '/Planning/kalenderMesin', $data);
    }
    function getJadwalMesin($mesinPerDay, $mesin)
    {
        $jadwal = [];

        foreach ($mesinPerDay as $mesinInfo) {
            foreach ($mesinInfo as $entry) {
                $date = $entry['date'];
                $model = $entry['model']; // Ambil model dari entry
                $jumlahMesin = $entry['mesin']; // Ambil jumlah mesin dari entry

                // Jika tanggal sudah ada di array jadwal
                if (isset($jadwal[$date])) {
                    $jadwal[$date]['mesin'] += $jumlahMesin;
                    $jadwal[$date]['avail'] -= $jumlahMesin;

                    // Jika model sudah ada di deskripsi, tambahkan jumlah mesinnya
                    if (isset($jadwal[$date]['deskripsi'][$model])) {
                        $jadwal[$date]['deskripsi'][$model] += $jumlahMesin;
                    } else {
                        // Jika model belum ada, tambahkan entry baru
                        $jadwal[$date]['deskripsi'][$model] = $jumlahMesin;
                    }
                } else {
                    // Jika tanggal belum ada, tambahkan entry baru
                    $jadwal[$date] = [
                        'date' => $date,
                        'mesin' => $jumlahMesin,
                        'avail' => $mesin - $jumlahMesin,
                        'deskripsi' => [
                            $model => $jumlahMesin, // Tambahkan model pertama dengan jumlah mesinnya
                        ],
                    ];
                }
            }
        }

        // Mengubah associative array menjadi indexed array
        return array_values($jadwal);
    }

    public function deleteplanmesin()
    {
        $id = $this->request->getPost('id');
        $idpln = $this->request->getPost('idpl');

        // Fetch estimated planning data
        $est = $this->EstimatedPlanningModel->where('id_detail_pln', $id)->first();
        if (!$est) {
            return redirect()->back()->withInput()->with('error', 'Data Planning tidak ditemukan');
        }

        $idest = $est['id_est_qty'];

        // Delete from TanggalPlanningModel
        $deleteTanggal = $this->TanggalPlanningModel->hapusData($idest, $id);
        if ($deleteTanggal) {
            // Delete from DetailPlanningModel
            $deleteDetail = $this->EstimatedPlanningModel->delete($idest);

            if ($deleteDetail) {
                return redirect()->to(base_url(session()->get('role') . '/planningpage/' . $id . '/' . $idpln))->withInput()->with('success', 'Data Berhasil Dihapus');
            } else {
                return redirect()->to(base_url(session()->get('role') . '/planningpage/' . $id . '/' . $idpln))->withInput()->with('error', 'Data Gagal Dihapus di DetailPlanningModel');
            }
        } else {
            return redirect()->to(base_url(session()->get('role') . '/planningpage/' . $id . '/' . $idpln))->withInput()->with('error', 'Data Gagal Dihapus di TanggalPlanningModel');
        }
    }

    public function stopPlanning($id)
    {
        if ($this->request->isAJAX()) {

            try {
                // Cek apakah data dengan ID tersebut ada
                $planning = $this->DetailPlanningModel->find($id);

                if (!$planning) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Plan not found.'
                    ]);
                }

                // Update status menjadi 'stop'
                $this->DetailPlanningModel->update($id, ['status' => 'stop']);

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Plan stopped successfully.'
                ]);
            } catch (\Exception $e) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to stop the plan.',
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Jika bukan permintaan AJAX, kembalikan halaman 404
        throw new \CodeIgniter\Exceptions\PageNotFoundException();
    }

    public function detailplanstop($id)
    {
        $detailplan = $this->DetailPlanningModel->getDataPlanningStop($id);
        foreach ($detailplan as &$dp) {
            $iddetail = $dp['id_detail_pln'];
            $qtysisa = $this->ApsPerstyleModel->getSisaPerModel($dp['model'], $dp['jarum']);
            $mesin = $this->TanggalPlanningModel->totalMc($iddetail);
            $jum = 0;
            foreach ($mesin as $mc) {
                $jum += $mc['mesin'];
            }
            $dp['mesin'] = $jum;
            $dp['qty'] = round($qtysisa['qty']);
            $dp['sisa'] =
                round($qtysisa['sisa']);
        }
        $kebutuhanArea = $this->KebutuhanAreaModel->where('id_pln_mc', $id)->first();
        $judul = $kebutuhanArea['judul'];
        $area = $kebutuhanArea['area'];
        $jarum =  $kebutuhanArea['jarum'];
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
        return view(session()->get('role') . '/Planning/planMcStopArea', $data);
    }

    public function activePlanning($id)
    {
        if ($this->request->isAJAX()) {

            try {
                // Cek apakah data dengan ID tersebut ada
                $planning = $this->DetailPlanningModel->find($id);

                if (!$planning) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Plan not found.'
                    ]);
                }

                // Update status menjadi 'stop'
                $this->DetailPlanningModel->update($id, ['status' => 'aktif']);

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Plan actived successfully.'
                ]);
            } catch (\Exception $e) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to active the plan.',
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Jika bukan permintaan AJAX, kembalikan halaman 404
        throw new \CodeIgniter\Exceptions\PageNotFoundException();
    }

    public function getPlanStyle()
    {
        if ($this->request->isAJAX()) {
            try {
                $jarum = $this->request->getGet('jarum');
                $model = $this->request->getGet('model');
                $delivery = $this->request->getGet('delivery');
                $style = $this->ApsPerstyleModel->detailModelJarum($model, $delivery, $jarum);
                $return = [];
                foreach ($style as $jc) {
                    $idAps = $jc['idapsperstyle'];
                    $mesin = $this->MesinPerStyle->getMesin($idAps);
                    $return[] = [
                        'idAps' => $idAps,
                        'inisial' => $jc['inisial'] ?? null,
                        'style' => $jc['size'] ?? null,
                        'qty' => round($jc['qty'] / 24) ?? null,
                        'sisa' => round($jc['sisa'] / 24) ?? null,
                        'mesin' => $mesin['mesin'] ?? null,
                        'keterangan' => $mesin['keterangan'] ?? null,
                    ];
                }
                usort($return, function ($a, $b) {
                    return strcmp($a['inisial'], $b['inisial']);
                });
                return $this->response->setJSON([
                    'status' => 'success',
                    'data' => $return // Replace with your data
                ]);
            } catch (\Exception $e) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => $e->getMessage()
                ]);
            }
        }
    }
    public function savePlanStyle()
    {

        $request = $this->request;

        $idAps = $request->getPost('idAps');
        $detailId = $request->getPost('detailId');
        $mesin = $request->getPost('mesin');
        $keterangan = $request->getPost('keterangan');

        if (!empty($idAps) && is_array($idAps)) {
            foreach ($idAps as $key => $id_apsperstyle) {
                $id_est_qty = $detailId[$key] ?? null;
                $jumlah_mesin = $mesin[$key] ?? 0;
                $desc = $keterangan[$key] ?? '';

                // Cek apakah data sudah ada di tabel
                $existing = $this->MesinPerStyle->where('id_est_qty', $id_est_qty)
                    ->where('idapsperstyle', $id_apsperstyle)
                    ->first();

                if ($existing) {
                    // Jika data sudah ada, lakukan update
                    $this->MesinPerStyle->update($existing['id_mesin_perinisial'], [
                        'mesin' => $jumlah_mesin,
                        'keterangan' => $desc
                    ]);
                } else {
                    // Jika belum ada, lakukan insert
                    $this->MesinPerStyle->insert([
                        'id_est_qty' => $id_est_qty,
                        'idapsperstyle' => $id_apsperstyle,
                        'mesin' => $jumlah_mesin,
                        'keterangan' => $desc
                    ]);
                }
            }
        }

        return redirect()->back()->with('success', 'Data berhasil disimpan.');
    }
}
