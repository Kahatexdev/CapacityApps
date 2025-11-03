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
use App\Models\MesinPernomor;
use App\Models\MachinesModel;
use App\Models\AksesModel;/*  */
use App\Models\PpsModel;/*  */
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
    protected $MesinPernomor;
    protected $machinesModel;
    protected $ppsModel;

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
        $this->MesinPernomor = new MesinPernomor();
        $this->machinesModel = new MachinesModel();
        $this->ppsModel = new PpsModel();
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


        $orderJalan = $this->bookingModel->getOrderJalan();
        $terimaBooking = $this->bookingModel->getBookingMasuk();
        $mcJalan = $this->jarumModel->mcJalan();
        $totalMc = $this->jarumModel->totalMc();
        $bulan = date('m');
        $area = $this->jarumModel->getArea();
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
        $kebutuhanArea = $this->KebutuhanAreaModel->where('id_pln_mc', $id)->first();
        $judul = $kebutuhanArea['judul'];
        $area = $kebutuhanArea['area'];
        $jarum =  $kebutuhanArea['jarum'];
        $mesinarea = $this->jarumModel->getMesinByArea($area, $jarum); //mesin yang dipakai semua mesin tanpa melibatkan head planning
        // $mesinplanning = $this->MesinPlanningModel->getMesinByArea($area,$jarum); //mesin yang dipilih oleh head planning di teruskan ke bagian aps
        $jarumList = $this->KebutuhanAreaModel->getDataByAreaGroupJrm($area);
        $detailplan = $this->DetailPlanningModel->getDataPlanning($id);
        foreach ($detailplan as &$dp) {
            $iddetail = $dp['id_detail_pln'];
            $qtysisa = $this->ApsPerstyleModel->getSisaPerModel($dp['model'], $dp['jarum'], $area);
            $mesin = $this->TanggalPlanningModel->totalMc($iddetail);
            $maxMesin = 0;
            foreach ($mesin as $mc) {
                if ($mc['mesin'] > $maxMesin) {
                    $maxMesin = $mc['mesin'];
                }
            }
            if (empty($qtysisa)) {
                $this->DetailPlanningModel->delete($iddetail);
                continue;
            } else {
                // Gunakan format Y-m-d untuk sorting (standar format)
                $dp['delivery_raw'] = $qtysisa['delivery']; // untuk sorting
                $dp['delivery'] = date('d-M-y', strtotime($qtysisa['delivery'])); // untuk tampil
                $dp['mesin'] = $maxMesin;
                $dp['qty'] = round($qtysisa['qty']);
                $dp['sisa'] = round($qtysisa['sisa']);
            }
        }

        // Sort pakai field 'delivery_raw'
        // dd($detailplan);
        usort($detailplan, function ($a, $b) {
            return strtotime($a['delivery_raw']) - strtotime($b['delivery_raw']);
        });


        $yesterday = date('Y-m-d', strtotime('-2 days'));
        foreach ($detailplan as &$dp) {
            $val = [
                'area' => $area,
                'jarum' => $jarum,
                'pdk' => $dp['model'],
                'awal' => $yesterday,
            ];
            $jlMC = $this->produksiModel->getJlMcTimter($val) ?? 0;
            $mcJalan = 0;
            foreach ($jlMC as $mc) {
                $mcJalan += $mc['jl_mc'];
            }
            $dp['actualMc'] = $mcJalan;
        }
        // dd($detailplan);
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
    public function orderPerAreaAps()
    {
        $id = session()->get('id_user');
        $area = $this->aksesModel->getArea($id);

        // dd($tampilperdelivery);
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

            'product' => $product,
        ];
        return view(session()->get('role') . '/Order/semuaorderarea', $data);
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
    public function DetailOrderPerAreaAps($area)
    {
        $tampilperdelivery = $this->orderModel->getPDk($area);
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

        // if ($area == 'KK8J') {
        //     $data = $this->ApsPerstyleModel->getDetailPlanningGloves($area, $jarum);
        // } else {
        //     $data = $this->ApsPerstyleModel->getDetailPlanning($area, $jarum);
        // }
        $data = $this->ApsPerstyleModel->getDetailPlanning($area, $jarum);

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
        $startMc = $this->orderModel->startMcBenang($pdk['model']);
        $repeat = $this->orderModel->getRepeat($pdk['model']) ?? '';
        $listDeliv = $this->ApsPerstyleModel->getDetailPerDeliv($pdk, $area);
        $listPlanning = $this->EstimatedPlanningModel->listPlanning($id);
        // dd($listPlanning);
        // $mesinpertgl = $this->TanggalPlanningModel->getMesinByDate($idutama);//get data machine per date and return into array
        $mesin = $this->jarumModel->getMesinByArea($area, $jarum);
        $data = [
            'role' => session()->get('role'),
            'title' => 'Data Planningan',
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
            'judul' => $judul,
            'startMc' => $startMc,
            'repeat' => $repeat,
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

        $cekOrderModel = $this->orderModel->startMcBenang($model);
        if (empty($cekOrderModel)) {
            $this->orderModel->updateStartMc($model, $start);
        }
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
        $idest = $this->request->getPost('idest');
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
        $kebutuhanArea = $this->KebutuhanAreaModel->where('id_pln_mc', $id)->first();
        $judul = $kebutuhanArea['judul'];
        $area = $kebutuhanArea['area'];
        $jarum =  $kebutuhanArea['jarum'];
        foreach ($detailplan as &$dp) {
            // dd($dp);
            $iddetail = $dp['id_detail_pln'];
            $qtysisa = $this->ApsPerstyleModel->getSisaPerModel($dp['model'], $dp['jarum'], $area);
            $mesin = $this->TanggalPlanningModel->totalMc($iddetail);
            $jum = 0;
            foreach ($mesin as $mc) {
                $jum += $mc['mesin'];
            }
            if ($qtysisa != null) {
                $dp['mesin'] = $jum;
                $dp['qty'] = round($qtysisa['qty']);
                $dp['sisa'] =
                    round($qtysisa['sisa']);
            }
        }


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
                $area = $this->request->getGet('area');
                $pdk = $this->request->getGet('pdk');
                $style = $this->ApsPerstyleModel->getPlanStyle($area, $pdk, $jarum);
                $return = [];
                foreach ($style as $jc) {
                    $idAps = $jc['idapsperstyle'];
                    $mesin = $this->MesinPerStyle->getMesin($idAps);
                    // dd($mesin);
                    $return[] = [
                        'idAps' => $idAps,
                        'inisial' => $jc['inisial'] ?? null,
                        'style' => $jc['size'] ?? null,
                        'color' => $jc['color'] ?? null,
                        'qty' => round($jc['qty'] / 24) ?? null,
                        'sisa' => round($jc['sisa'] / 24) ?? null,
                        'mesin' => $mesin['mesin'] ?? null,
                        'keterangan' => $mesin['keterangan'] ?? null,
                        'material_status' => $mesin['material_status'] ?? 'not ready',
                        'priority' => $mesin['priority'] ?? 'low',
                        'start' => $mesin['start_pps_plan'] ?? null,
                        'stop' => $mesin['stop_pps_plan'] ?? null,
                        'repeat' => $mesin['repeat_from'] ?? null,
                    ];
                }
                usort($return, function ($a, $b) {
                    return strcmp($a['inisial'], $b['inisial']);
                });
                $startStop = null;
                if (!empty($return)) {
                    $startStop = [
                        'start' => $return[0]['start'],
                        'stop'  => $return[0]['stop'],
                    ];
                }
                return $this->response->setJSON([
                    'status' => 'success',
                    'data' => $return, // Replace with your data
                    'start_stop' => $startStop
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
        $mesin = $request->getPost('mesin');
        $pps = $request->getPost('pps');
        // dd($pps);
        $keterangan = $request->getPost('keterangan');
        $priority = $request->getPost('priority');
        $material = $request->getPost('material');
        $start = $request->getPost('start_pps');
        $stop  = $request->getPost('stop_pps');
        $repeat  = $request->getPost('repeat');

        // Format biar aman ke DB (datetime)
        $start = (!empty($start) && $start !== '0000-00-00') ? $start . ' 00:00:00' : null;
        $stop  = (!empty($stop) && $stop !== '0000-00-00') ? $stop . ' 00:00:00' : null;
        if (!empty($idAps) && is_array($idAps)) {
            foreach ($idAps as $key => $id_apsperstyle) {

                $jumlah_mesin = $mesin[$key] ?? 0;
                $desc = $keterangan[$key] ?? '';
                $prio = $priority[$key] ?? '';
                $mat = $material[$key] ?? '';
                $rp = $repeat[$key] ?? '';
                // Cek apakah data sudah ada di tabel
                $existing = $this->MesinPerStyle
                    ->where('idapsperstyle', $id_apsperstyle)
                    ->first();
                // dd($existing);
                if ($existing) {
                    // Jika data sudah ada
                    $cekPps = $this->ppsModel
                        ->where('id_mesin_perinisial', $existing['id_mesin_perinisial'])
                        ->first();

                    if ($cekPps) {
                        // Update PPS jika sudah ada
                        $this->ppsModel->update($cekPps['id_pps'], [
                            'pps_status'        => 'planning',
                        ]);
                    } else {
                        // Insert PPS baru jika belum ada
                        // dd($existing['id_mesin_perinisial']);
                        $this->ppsModel->insert([
                            'id_mesin_perinisial' => $existing['id_mesin_perinisial'],
                            'pps_status'        => 'planning',
                        ]);
                    }
                    // Update data mesin_perstyle
                    $this->MesinPerStyle->update($existing['id_mesin_perinisial'], [
                        'mesin'      => $jumlah_mesin,
                        'keterangan' => $desc,
                        'start_pps_plan'    => $start,
                        'stop_pps_plan'    => $stop,
                        'material_status'    => $mat,
                        'priority'    => $prio,
                        'repeat_from'    => $rp,
                        'admin'    => session()->get('username'),
                    ]);
                } else {
                    // Jika belum ada, lakukan insert baru
                    $this->MesinPerStyle->insert([
                        'idapsperstyle' => $id_apsperstyle,
                        'mesin'         => $jumlah_mesin,
                        'keterangan'    => $desc,
                        'start_pps_plan'    => $start,
                        'stop_pps_plan'    => $stop,
                        'material_status'    => $mat,
                        'priority'    => $prio,
                        'repeat_from'    => $rp,
                        'admin'    => session()->get('username'),
                    ]);

                    $getId = $this->MesinPerStyle->getInsertID();

                    // Tambahkan PPS baru
                    $this->ppsModel->insert([
                        'id_mesin_perisinial' => $getId,
                        'pps_status'        => 'planning',
                    ]);
                }
            }
        }

        return redirect()->back()->with('success', 'Data berhasil disimpan.');
    }
    public function deletePlanPdk()
    {
        $idDetail = $this->request->getPost('id');
        $idEst = $this->EstimatedPlanningModel->getIdEst($idDetail) ?? null;
        if (!empty($idEst)) {
            foreach ($idEst as $id) {
                $this->TanggalPlanningModel->hapusData($id['id_est_qty'], $idDetail);
            }
            $delete = $this->EstimatedPlanningModel->deletePlaningan($idDetail);
            if ($delete) {
                return redirect()->back()->with('success', 'Data berhasil dihapus.');
            } else {
                return redirect()->back()->with('error', 'Data Gagal dihapus.');
            }
        } else {
            return redirect()->back()->with('error', 'Data Belum Di planning.');
        }
    }
    public function deletePlanAll()
    {
        $idPlan = $this->request->getPost('id');
        $idDetail = $this->DetailPlanningModel->getIdAktif($idPlan) ?? null;
        if (empty($idDetail)) {
            return redirect()->back()->with('error', 'Belum Ada planningan');
        } else {
            foreach ($idDetail as $detailPln) {
                $idEst = $this->EstimatedPlanningModel->getIdEst($detailPln['id_detail_pln']) ?? null;
                if (!empty($idEst)) {
                    foreach ($idEst as $id) {
                        $this->TanggalPlanningModel->hapusData($id['id_est_qty'], $detailPln['id_detail_pln']);
                    }
                    $this->EstimatedPlanningModel->deletePlaningan($detailPln['id_detail_pln']);
                } else {
                    continue;
                }
            }
            return redirect()->back()->with('success', 'Data berhasil dihapus.');
        }
    }
    public function getListMesinplan()
    {
        if ($this->request->isAJAX()) {
            try {
                $idAps = $this->request->getGet('idAps');
                $idMc  = $this->request->getGet('idplan');

                $mesin = $this->MesinPernomor->getListPlan($idAps, $idMc);
                foreach ($mesin as &$row) {
                    $row['start_mesin'] = date('Y-m-d', strtotime($row['start_mesin']));
                    $row['stop_mesin']  = date('Y-m-d', strtotime($row['stop_mesin']));
                }
                return $this->response->setJSON([
                    'status' => 'success',
                    'data'   => $mesin
                ]);
            } catch (\Exception $e) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => $e->getMessage()
                ]);
            }
        }
    }
    public function saveStartmesinBenang()
    {
        $tgl = $this->request->getPost('startMesin');
        $model = $this->request->getPost('model');
        $update = $this->orderModel->updateStartMc($model, $tgl);
        if ($update) {
            return redirect()->back()->with('success', 'Start mesin disimpan.');
        } else {
            return redirect()->back()->with('error', 'Data Gagal disimpan.');
        }
    }
    public function savePlanningPernomor()
    {
        if ($this->request->isAJAX()) {
            try {
                $area   = $this->request->getPost('area');
                $jarum  = $this->request->getPost('jarum');
                $no_mc  = $this->request->getPost('no_mc'); // array
                $idaps  = $this->request->getPost('idaps');
                $idplan = $this->request->getPost('idplan');
                $start  = $this->request->getPost('start'); // array
                $stop   = $this->request->getPost('stop');  // array
                $id     = $this->request->getPost('id');    // array

                $jumlah = count($no_mc);

                foreach ($no_mc as $i => $mc) {
                    $idmc = $this->machinesModel
                        ->where('no_mc', $mc)
                        ->where('jarum', $jarum)
                        ->where('area', $area)
                        ->first();

                    $data = [
                        'id_mesin'       => $idmc['id'],
                        'id_detail_plan' => $idplan,
                        'idapsperstyle'  => $idaps,
                        'start_mesin'    => $start[$i] ?? null,
                        'stop_mesin'     => $stop[$i] ?? null,
                    ];

                    // ✅ cek id per-baris
                    if (empty($id[$i])) {
                        $this->MesinPernomor->insert($data);
                    } else {
                        $this->MesinPernomor->update($id[$i], $data);
                    }
                }

                // ✅ update jumlah mesin
                $this->MesinPerStyle
                    ->where('idapsperstyle', $idaps)
                    ->set('mesin', $jumlah)
                    ->update();

                return $this->response->setJSON([
                    'status'  => 'success',
                    'message' => 'Data berhasil disimpan'
                ]);
            } catch (\Exception $e) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => $e->getMessage()
                ]);
            }
        }
    }
    public function deleteMesinPernomor()
    {
        $id    = $this->request->getPost('id');
        $idaps = $this->request->getPost('idaps');
        $idplan = $this->request->getPost('idplan');


        if ($this->MesinPernomor->delete($id)) {
            $data = $this->MesinPernomor->hitung($idaps, $idplan);
            $jumlah = $data['jumlah'] ?? 0;
            $this->MesinPerStyle
                ->where('idapsperstyle', $idaps)
                ->set('mesin', $jumlah)
                ->update();
            return $this->response->setJSON(['status' => 'success']);
        }
        return $this->response->setJSON(['status' => 'error']);
    }
    public function checkAvailable()
    {
        $no_mc = $this->request->getPost('no_mc');
        $jarum = $this->request->getPost('jarum');
        $area  = $this->request->getPost('area');

        $exists = $this->machinesModel
            ->where('no_mc', $no_mc)
            ->where('jarum', $jarum)
            ->where('area', $area)
            ->countAllResults();

        return $this->response->setJSON([
            'ada' => $exists == 0
        ]);
    }
    public function pps()
    {
        $userId = session()->get('id_user');
        $area = $this->aksesModel->aksesData($userId);
        $getPdk = $this->DetailPlanningModel->getPpsData($area);
        $result = [];
        foreach ($getPdk as $pdk) {
            $ppsData = $this->ApsPerstyleModel->getPpsData($pdk['model'], $pdk['area']); // array of arrays
            // dd($ppsData);
            $rowNum = count($ppsData);

            if ($rowNum == 0) continue; // skip kalau ga ada data

            // hitung yang approved
            $done = 0;
            foreach ($ppsData as $pps) {
                if (isset($pps['pps_status']) && $pps['pps_status'] === 'approved') {
                    $done++;
                }
            }
            $matDone = 0;
            foreach ($ppsData as $pps) {
                if (isset($pps['material_status']) && $pps['material_status'] === 'complete') {
                    $matDone++;
                }
            }
            $star = 0;
            foreach ($ppsData as $pps) {
                if (isset($pps['priority']) && $pps['priority'] === 'high') {
                    $star++;
                }
            }

            $result[] = [
                'repeat' => $pps['repeat'],
                'pdk' => $pdk['model'],
                'start' => isset($ppsData[0]['start_pps_plan']) ? date('Y-m-d', strtotime($ppsData[0]['start_pps_plan'])) : null,
                'stop'  => isset($ppsData[0]['stop_pps_plan']) ? date('Y-m-d', strtotime($ppsData[0]['stop_pps_plan'])) : null,
                'progress' => $rowNum > 0 ? $done / $rowNum * 100 : 0,
                'material' => $rowNum > 0 ? $matDone / $rowNum * 100 : 0,
                'star' => $rowNum > 0 ? ($star / $rowNum) * 5 : 0,
                'factory' => $pps['factory'],

            ];
        }
        // dd($result);
        // urutkan dari progress terbesar ke terkecil
        usort($result, function ($a, $b) {
            // Urutkan berdasarkan star dulu (descending)
            if ($a['star'] != $b['star']) {
                return $b['star'] <=> $a['star'];
            }

            // Kalau star sama, urutkan berdasarkan material (descending)
            return $b['material'] <=> $a['material'];
        });
        $data = [
            'role' => session()->get('role'),
            'title' => 'Data Order',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'area' => $area,
            'pdk' => $result,
        ];
        return view(session()->get('role') . '/Planning/Listpps', $data);
    }
    public function ppsDetail($pdk, $area)
    {
        $modelData = $this->orderModel->getModelData($pdk);
        $ppsData = $this->ApsPerstyleModel->getPpsData($pdk, $area);

        $data = [
            'role' => session()->get('role'),
            'title' => 'Data Order',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'ppsData' => $ppsData,
            'modelData' => $modelData,
            'role' => session()->get('role')
        ];
        return view(session()->get('role') . '/Planning/ppsDetail', $data);
    }
    public function updatePps()
    {
        $post = $this->request->getPost();
        $ids            = $post['id_pps'] ?? [];
        $imp            = $post['imp'] ?? [];
        $status         = $post['status'] ?? [];
        $mechanic       = $post['mechanic'] ?? [];
        $coor           = $post['coor'] ?? [];
        $start_pps_act  = $post['start_pps_act'] ?? [];
        $stop_pps_act   = $post['stop_pps_act'] ?? [];
        $acc_qad        = $post['acc_qad'] ?? [];
        $acc_mr         = $post['acc_mr'] ?? [];
        $acc_fu         = $post['acc_fu'] ?? [];
        $notes          = $post['notes'] ?? [];
        $admin          = session()->get('username');
        // dd($post);
        $updateData = [];
        $insertData = [];

        foreach ($imp as $i => $impValue) {
            $oldData = null;
            if (!empty($ids[$i])) {
                $oldData = $this->ppsModel->find($ids[$i]);
            }
            // dd($imp);
            $row = [
                'id_mesin_perinisial' => $impValue ?? null,
                'pps_status'           => $status[$i] ?? 'planning',
                'mechanic'             => $mechanic[$i] ?? null,
                'coor'                 => $coor[$i] ?? null,
                'start_pps_act'        => $start_pps_act[$i] ?? null,
                'stop_pps_act'         => $stop_pps_act[$i] ?? null,
                'acc_qad'              => $acc_qad[$i] ?? null,
                'acc_mr'               => $acc_mr[$i] ?? null,
                'acc_fu'               => $acc_fu[$i] ?? null,
                'admin'                => $admin,
            ];
            // dd($row);
            // --- Update existing record ---
            if ($oldData) {
                $newStatus = $status[$i] ?? 'planning';
                $oldStatus = $oldData['pps_status'] ?? 'planning';
                $history = $oldData['history'] ?? '';
                $timeNow = date('Y-m-d H:i:s');

                // Kalau status berubah → tulis ke history
                if ($newStatus !== $oldStatus) {
                    $history .= "\n[{$timeNow}] {$admin}: Status changed from '{$oldStatus}' to '{$newStatus}'";
                }

                // Kalau ada notes → tulis juga ke history
                if (!empty(trim($notes[$i] ?? ''))) {
                    $noteText = trim($notes[$i]);
                    $history .= "\n[{$timeNow}] {$admin} Note: {$noteText} ";
                }

                $row['history'] = trim($history);
                $row['id_pps'] = $ids[$i];
                $updateData[] = $row;

                // --- Insert new record ---
            } else {
                $timeNow = date('Y-m-d H:i:s');
                $history = "[{$timeNow}] Created by {$admin}";
                if (!empty(trim($notes[$i] ?? ''))) {
                    $history .= "\n[{$timeNow}] {$admin} Note: " . trim($notes[$i]);
                }

                $row['history'] = $history;
                $insertData[] = $row;
            }
        }
        if (!empty($updateData)) {
            $this->ppsModel->updateBatch($updateData, 'id_pps');
        }
        // dd($insertData);

        if (!empty($insertData)) {
            $this->ppsModel->insertBatch($insertData);
        }

        return redirect()->back()->with('success', 'Data PPS berhasil disimpan!');
    }
}
