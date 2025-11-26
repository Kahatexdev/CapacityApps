<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Database\Migrations\DataCancelOrder;
use App\Database\Migrations\EstSpk;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\DataMesinModel;
use App\Models\OrderModel;
use App\Models\BookingModel;
use App\Models\ProductTypeModel;
use App\Models\ApsPerstyleModel;
use App\Models\AreaModel;
use App\Models\ProduksiModel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use DateTime;
use App\Models\HistorySmvModel;
use App\Models\StockPdk;
use App\Models\DataCancelOrderModel;
use App\Models\EstSpkModel;
use App\Models\HistoryRevisiModel;
use App\Models\BsModel;
use App\Models\DetailPlanningModel;
use CodeIgniter\HTTP\Exceptions\HTTPException;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\CURLRequest;

class OrderController extends BaseController
{
    protected $filters;
    protected $jarumModel;
    protected $productModel;
    protected $produksiModel;
    protected $bookingModel;
    protected $orderModel;
    protected $ApsPerstyleModel;
    protected $liburModel;
    protected $roleSession;
    protected $historysmv;
    protected $areaModel;
    protected $cancelOrder;
    protected $estspk;
    protected $historyRev;
    protected $bsModel;
    protected $DetailPlanningModel;
    protected $stokPdk;
    protected $curl;

    public function __construct()
    {
        session();
        $this->jarumModel = new DataMesinModel();
        $this->bookingModel = new BookingModel();
        $this->productModel = new ProductTypeModel();
        $this->produksiModel = new ProduksiModel();
        $this->orderModel = new OrderModel();
        $this->ApsPerstyleModel = new ApsPerstyleModel();
        $this->historysmv = new HistorySmvModel();
        $this->areaModel = new AreaModel();
        $this->cancelOrder = new DataCancelOrderModel();
        $this->estspk = new EstSpkModel();
        $this->historyRev = new HistoryRevisiModel();
        $this->bsModel = new BsModel();
        $this->DetailPlanningModel = new DetailPlanningModel();
        $this->stokPdk = new StockPdk();

        if ($this->filters   = ['role' => ['capacity',  'planning', 'aps', 'god']] != session()->get('role')) {
            return redirect()->to(base_url('/login'));
        }
        $this->isLogedin();
        $this->roleSession =  session()->get('role');
    }
    protected function isLogedin()
    {
        if (!session()->get('id_user')) {
            return redirect()->to(base_url('/login'));
        }
    }
    public function order()
    {
        $role = session()->get('role');
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
            'jenisJarum' => $totalMesin,
            'role' => $role
        ];
        return view($role . '/Order/ordermaster', $data);
    }


    public function detailModelCapacity($noModel, $delivery)
    {
        $pdkProgress = $this->ApsPerstyleModel->getProgress($noModel);
        $dataApsPerstyle = $this->ApsPerstyleModel->detailModel($noModel, $delivery); // Call the model method
        $role = session()->get('role');

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
            'progress' => $pdkProgress,
            'role' => $role

        ];
        return view(session()->get('role') . '/Order/detailOrder', $data);
    }

    public function semuaOrder()
    {
        $role = session()->get('role');
        $dataBuyer = $this->orderModel->getBuyer();
        $dataArea = $this->jarumModel->getArea();
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
            'product' => $product,
            'role' => $role,
            'buyer' => $dataBuyer,
            'area' => $dataArea,

        ];
        return view($role . '/Order/semuaorder', $data);
    }

    public function tampilPerdelivery()
    {
        $request = service('request');
        $requestData = $request->getPost();
        log_message('debug', 'POST data: ' . print_r($requestData, true));

        $start = $requestData['start'] ?? 0;
        $length = $requestData['length'] ?? 10;
        $orderIndex = $requestData['order'][0]['column'] ?? 0;
        $orderDir = $requestData['order'][0]['dir'] ?? 'asc';
        $orderColumn = $requestData['columns'][$orderIndex]['data'] ?? '';

        // Extract search value from request
        $searchValue = $requestData['search']['value'] ?? '';

        // Fetch data from the model with search filter
        $tampilperdelivery = $this->orderModel->tampilPerdelivery($searchValue);

        $data = array_slice($tampilperdelivery, $start, $length);

        // 🔹 3. Ambil semua no_model unik dari hasil data
        $models = array_unique(array_column($data, 'no_model'));

        // 🔹 4. Hanya panggil API kalau memang ada model
        if (!empty($models)) {
            // 🔹 Siapkan body JSON
            $postData = json_encode(['models' => $models]);

            // 🔹 Siapkan konfigurasi untuk file_get_contents (POST JSON)
            $options = [
                'http' => [
                    'method'  => 'POST',
                    'header'  => "Content-Type: application/json\r\n",
                    'content' => $postData,
                    'timeout' => 10 // batas waktu 10 detik
                ]
            ];

            $context = stream_context_create($options);

            // 🔹 URL API target
            $url = $this->urlMaterial . 'getTglScheduleBulk';

            // 🔹 Eksekusi request ke API
            $response = @file_get_contents($url, false, $context);

            // 🔹 Logging untuk debug
            // log_message('debug', "API Request to: {$url}");
            // log_message('debug', "API Request Body: {$postData}");
            log_message('debug', "API Response: {$response}");

            // 🔹 Decode hasil response jadi array asosiatif
            $scheduleMap = json_decode($response, true);

            // Kalau array numerik, ubah jadi associative pakai no_model
            if (is_array($scheduleMap) && array_is_list($scheduleMap) && isset($scheduleMap[0]['no_model'])) {
                $scheduleMap = array_column($scheduleMap, null, 'no_model');
            }

            log_message('debug', 'scheduleMap isi setelah normalisasi: ' . json_encode($scheduleMap, JSON_PRETTY_PRINT));

            foreach ($data as &$row) {
                $noModel = $row->no_model ?? null;
                $tglSchedule = $scheduleMap[$noModel]['tgl_schedule'] ?? null;
                log_message('debug', "✅ schedule untuk {$noModel}: " . json_encode($tglSchedule));
                $row->tgl_schedule = $tglSchedule;
            }
        }

        $recordsTotal = count($tampilperdelivery);
        $recordsFiltered = $recordsTotal;

        $response = [
            'draw' => $requestData['draw'] ?? 0,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ];

        return $this->response->setJSON($response);
    }
    public function dataOrderSearch()
    {
        $searchTerm = $this->request->getPost('searchTerm');

        $model = new \App\Models\ApsPerstyleModel(); // ganti sesuai model kamu

        $result = $model->select('mastermodel, factory')
            ->groupBy(['mastermodel', 'factory'])
            ->like('mastermodel', $searchTerm)
            ->limit(20)
            ->findAll();

        $data = [];

        foreach ($result as $row) {
            $data[] = [
                'value' => $row['mastermodel'] . '|' . $row['factory'], // akan jadi value di <option>
                'label' => $row['mastermodel']                          // akan jadi label di Select2
            ];
        }

        return $this->response->setJSON($data);
    }

    public function belumImport()
    {
        $role = session()->get('role');

        $tampilperdelivery = $this->orderModel->tampilbelumImport();
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
            'role' => $role


        ];
        return view($role . '/Order/semuaorder2', $data);
    }

    public function orderPerJarum()
    {
        $role = session()->get('role');

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
            'role' => $role

        ];
        return view(session()->get('role') . '/Order/orderjarum', $data);
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
    public function orderPerbulan()
    {
        $bulan = $this->ApsPerstyleModel->getMonthName();
        foreach ($bulan as $key => &$id) {
            $order = $this->orderModel->tampilPerBulanByJarum($id['bulan'], $id['tahun']);
            // all
            $id['qtyAll'] = $order['qty_tj'] + $order['qty_jc'];
            $id['sisaAll'] = $order['sisa_tj'] + $order['sisa_jc'];
            $id['actAll'] = $order['actual_tj'] + $order['actual_jc'];

            // byJarum TJ
            $id['qtyTj'] = $order['qty_tj'];
            $id['sisaTj'] = $order['sisa_tj'];
            $id['actualTj'] = $order['actual_tj'];
            // rosso TJ
            $id['qtyRossoTj'] = $order['qty_rosso_tj'];
            $id['sisaRossoTj'] = $order['sisa_rosso_tj'];
            $id['actualRossoTj'] = $order['actual_rosso_tj'];
            // autolink TJ
            $id['qtyAutolinkTj'] = $order['qty_autolink_tj'];
            $id['sisaAutolinkTj'] = $order['sisa_autolink_tj'];
            $id['actualAutolinkTj'] = $order['actual_autolink_tj'];

            // byJarum JC
            $id['qtyJc'] = $order['qty_jc'];
            $id['sisaJc'] = $order['sisa_jc'];
            $id['actualJc'] = $order['actual_jc'];
            // rosso TJ
            $id['qtyRossoJc'] = $order['qty_rosso_jc'];
            $id['sisaRossoJc'] = $order['sisa_rosso_jc'];
            $id['actualRossoJc'] = $order['actual_rosso_jc'];
            // autolink TJ
            $id['qtyAutolinkJc'] = $order['qty_autolink_jc'];
            $id['sisaAutolinkJc'] = $order['sisa_autolink_jc'];
            $id['actualAutolinkJc'] = $order['actual_autolink_jc'];
        }
        $data = [
            'role' => session()->get('role'),
            'title' => 'Data Order Perbulan',
            'active1' => '',
            'active2' => 'active',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'bulan' => $bulan,
        ];
        return view(session()->get('role') . '/Order/orderbulan', $data);
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
    public function detailmodeljarumPlan($noModel, $delivery, $jarum)
    {
        $apsPerstyleModel = new ApsPerstyleModel(); // Create an instance of the model
        $dataApsPerstyle = $apsPerstyleModel->detailModelJarum($noModel, $delivery, $jarum); // Call the model method
        $area = new AreaModel();
        $dataArea = $area->findALl();
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
            'area' => $dataArea
        ];

        return view(session()->get('role') . '/Order/detailModelJarum', $data);
    }



    public function DetailOrderPerJarum($jarum)
    {
        $tampilperdelivery = $this->orderModel->tampilPerjarum($jarum);
        $product = $this->productModel->findAll();
        $totalMesin = $this->jarumModel->getTotalMesinByJarum();
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
            'jenisJarum' => $totalMesin,

        ];
        return view(session()->get('role') . '/Order/semuaorderjarum', $data);
    }

    public function DetailOrderPerJarumBlnDetail($bulan, $tahun, $jarum)
    {
        $totalMesin = $this->jarumModel->getTotalMesinByJarum();
        $tampilperdelivery = $this->ApsPerstyleModel->tampilPerjarumBulan($bulan, $tahun, $jarum);
        // $tampilperdelivery = $this->orderModel->tampilPerjarumBulan($bulan, $tahun, $jarum);
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
            'jenisJarum' => $totalMesin,

        ];
        return view(session()->get('role') . '/Order/semuaorderjarum', $data);
    }

    public function DetailOrderPerJarumBln($jarum)
    {
        $bulan = $this->ApsPerstyleModel->getBulan($jarum);
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
            'bulan' => $bulan,
            'product' => $product,

        ];
        return view(session()->get('role') . '/Order/orderjarumblngroup', $data);
    }
    public function DetailOrderPerJarumPlan($jarum)
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
    public function DetailOrderPerAreaPlan($area)
    {
        $tampilperdelivery = $this->orderModel->tampilPerarea($area);
        foreach ($tampilperdelivery as &$id) {
            $statusPlan = $this->DetailPlanningModel->getStatusPlanning($area, $id->mastermodel, $id->machinetypeid);
            $id->status_plan = ($statusPlan && $statusPlan['status'] === 'aktif') ? 'Planned' : '';
        }
        $dataBuyer = $this->orderModel->getBuyer();
        $dataArea = $this->jarumModel->getArea();

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
            'buyer' => $dataBuyer,
            'area' => $area,
            'listArea' => $dataArea,
            'tampildata' => $tampilperdelivery,
            'product' => $product,

        ];
        return view(session()->get('role') . '/Order/semuaorderarea', $data);
    }
    public function updateorder($idOrder)
    {

        $data = [
            'role' => session()->get('role'),
            'mastermodel' => $this->request->getPost("no_model"),
            'size' =>  $this->request->getPost("style"),
            'delivery' => $this->request->getPost("delivery"),
            'qty' => $this->request->getPost("qty"),
            'sisa' => $this->request->getPost("sisa"),
            'seam' => $this->request->getPost("seam"),
            'smv' => $this->request->getPost("smv"),
            'factory' => $this->request->getPost("factory"),
            'inisial' => $this->request->getPost("inisial"),
        ];
        $jrm = $this->request->getPost("jarum");
        $id = $idOrder;
        $update = $this->ApsPerstyleModel->update($id, $data);
        $modl = $this->request->getPost("no_model");
        $del = $this->request->getPost("delivery");
        if ($update) {
            return redirect()->to(base_url(session()->get('role') . '/detailPdk/' . $modl . '/' . $jrm))->withInput()->with('success', 'Data Berhasil Di Update');
        } else {
            return redirect()->to(base_url(session()->get('role') . '/detailPdk/' . $modl . '/' . $jrm))->withInput()->with('error', 'Gagal Update Data');
        }
    }
    public function updateorderjarum($idOrder)
    {

        $data = [
            'role' => session()->get('role'),
            'mastermodel' => $this->request->getPost("no_model"),
            'size' =>  $this->request->getPost("style"),
            'delivery' => $this->request->getPost("delivery"),
            'qty' => $this->request->getPost("qty"),
            'sisa' => $this->request->getPost("sisa"),
            'seam' => $this->request->getPost("seam"),
            'factory' => $this->request->getPost("factory"),
        ];
        $id = $idOrder;
        $update = $this->ApsPerstyleModel->update($id, $data);
        $modl = $this->request->getPost("no_model");
        $del = $this->request->getPost("delivery");
        $jrm = $this->request->getPost("jarum");
        if ($update) {
            return redirect()->to(base_url(session()->get('role') . '/detailPdk/' . $modl . '/' . $jrm))->withInput()->with('success', 'Data Berhasil Di Update');
        } else {
            return redirect()->to(base_url(session()->get('role') . '/detailPdk/' . $modl . '/' . $jrm))->withInput()->with('error', 'Gagal Update Data');
        }
    }
    public function updateorderjarumplan($idOrder)
    {

        $data = [
            'role' => session()->get('role'),
            'mastermodel' => $this->request->getPost("no_model"),
            'size' =>  $this->request->getPost("style"),
            'delivery' => $this->request->getPost("delivery"),
            'qty' => $this->request->getPost("qty"),
            'sisa' => $this->request->getPost("sisa"),
            'seam' => $this->request->getPost("seam"),
            'factory' => $this->request->getPost("factory"),
        ];
        $id = $idOrder;
        $update = $this->ApsPerstyleModel->update($id, $data);
        $modl = $this->request->getPost("no_model");
        $del = $this->request->getPost("delivery");
        $jrm = $this->request->getPost("jarum");
        if ($update) {
            return redirect()->to(base_url(session()->get('role') . '/detailmodeljarum/' . $modl . '/' . $del . '/' . $jrm))->withInput()->with('success', 'Data Berhasil Di Update');
        } else {
            return redirect()->to(base_url(session()->get('role') . '/detailmodeljarum/' . $modl . '/' . $del . '/' . $jrm))->withInput()->with('error', 'Gagal Update Data');
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

    public function deletedetailstyle($idOrder)
    {

        $idOrder = $this->request->getPost("idapsperstyle");
        $id = $idOrder;
        $delete = $this->ApsPerstyleModel->delete($id);
        $modl = $this->request->getPost("no_model");
        $del = $this->request->getPost("delivery");
        if ($delete) {
            return redirect()->to(base_url(session()->get('role') . '/detailmodel/' . $modl . '/' . $del))->withInput()->with('success', 'Data Berhasil Di Hapus');
        } else {
            return redirect()->to(base_url(session()->get('role') . '/detailmodel/' . $modl . '/' . $del))->withInput()->with('error', 'Gagal Hapus Data');
        }
    }

    public function deletedetailmodeljarum($idOrder)
    {

        $idOrder = $this->request->getPost("idapsperstyle");
        $id = $idOrder;
        $delete = $this->ApsPerstyleModel->delete($id);
        $modl = $this->request->getPost("no_model");
        $del = $this->request->getPost("delivery");
        $jrm = $this->request->getPost("jarum");
        if ($delete) {
            return redirect()->to(base_url(session()->get('role') . '/detailmodeljarum/' . $modl . '/' . $del . '/' . $jrm))->withInput()->with('success', 'Data Berhasil Di Hapus');
        } else {
            return redirect()->to(base_url(session()->get('role') . '/detailmodeljarum/' . $modl . '/' . $del . '/' . $jrm))->withInput()->with('error', 'Gagal Hapus Data');
        }
    }
    public function deletedetailmodeljarumplan($idOrder)
    {

        $idOrder = $this->request->getPost("idapsperstyle");
        $id = $idOrder;
        $delete = $this->ApsPerstyleModel->delete($id);
        $modl = $this->request->getPost("no_model");
        $del = $this->request->getPost("delivery");
        $jrm = $this->request->getPost("jarum");
        if ($delete) {
            return redirect()->to(base_url(session()->get('role') . '/detailmodeljarum/' . $modl . '/' . $del . '/' . $jrm))->withInput()->with('success', 'Data Berhasil Di Hapus');
        } else {
            return redirect()->to(base_url(session()->get('role') . '/detailmodeljarum/' . $modl . '/' . $del . '/' . $jrm))->withInput()->with('error', 'Gagal Hapus Data');
        }
    }
    public function inputOrder()
    {
        $tgl_turun = $this->request->getPost("tgl_turun");
        $no_model = $this->request->getPost("no_model");
        $no_booking = $this->request->getPost("no_booking");
        $deskripsi = $this->request->getPost("deskripsi");
        $sisa_booking = $this->request->getPost("sisa_booking_akhir");
        $keterangan = $this->request->getPost("keterangan");
        $id_booking = $this->request->getPost("id_booking");
        $jarum = $this->request->getPost("jarum");
        if (empty($tgl_turun)) {
            $tgl_turun = date('Y-m-d');
        }
        $check = $this->orderModel->checkExist($no_model);
        if ($check) {
            $id = $id_booking;
            $status = "";
            if ($sisa_booking == "0") {
                $status = "Habis";
            } else {
                $status = "Aktif";
            }
            $cekKeterangan = $this->bookingModel->select('keterangan')->where('id_booking', $id)->first();
            $oldKeterangan = $cekKeterangan['keterangan'] ?? '';
            $ket = $oldKeterangan . ' | ' . $keterangan;
            $data = [
                'role' => session()->get('role'),
                'sisa_booking' => $sisa_booking,
                'keterangan' => $ket,
                'status' => $status
            ];
            // dd($data);
            $this->bookingModel->update($id, $data);
            return redirect()->to(base_url(session()->get('role') . '/detailbooking/' . $id_booking))->withInput()->with('success', 'Data Berhasil Diinput');
        } else {

            $inputModel = [
                'created_at' => $tgl_turun,
                'no_model' => $no_model,
                'deskripsi' => $deskripsi,
                'id_booking' => $id_booking,
            ];
            $input = $this->orderModel->insert($inputModel);
            if (!$input) {
                return redirect()->to(base_url(session()->get('role') . '/detailbooking/' . $id_booking))->withInput()->with('error', 'Gagal Ambil Order');
            } else {
                $id = $id_booking;
                $status = "";
                if ($sisa_booking == "0") {
                    $status = "Habis";
                } else {
                    $status = "Aktif";
                }
                $cekKeterangan = $this->bookingModel->select('keterangan')->where('id_booking', $id)->first();
                $oldKeterangan = $cekKeterangan['keterangan'] ?? '';
                $ket = $oldKeterangan . ' | ' . $keterangan;
                $data = [
                    'role' => session()->get('role'),
                    'sisa_booking' => $sisa_booking,
                    'keterangan' => $ket,
                    'status' => $status
                ];
                $this->bookingModel->update($id, $data);
                return redirect()->to(base_url(session()->get('role') . '/detailbooking/' . $id_booking))->withInput()->with('success', 'Data Berhasil Diinput');
            }
        }
    }
    public function inputOrderManual()
    {
        $model = $this->request->getPost('no_model');
        $jarum = $this->request->getPost('jarum');
        $prod = $this->request->getPost('productType');
        $id = $this->request->getPost('id');

        $getId = [
            'jarum' => $jarum,
            'prodtype' => $prod
        ];
        $idProdtype = $this->productModel->getId($getId);

        $tgl = $this->request->getPost('tgl_turun');
        $tanggal = date('Y-m-d', strtotime($tgl));
        if (empty($tgl)) {
            $tanggal = date('Y-m-d');
        }
        if ($model) {
            $check = $this->orderModel->checkExist($model);
            if ($check) {
                return redirect()->to(base_url(session()->get('role') . '/dataorderperjarum/' . $id))->withInput()->with('error', 'Data Model Exist');
            } else {
                $insert = $this->orderModel->insert([
                    'no_model' => $model,
                    'id_product_type' => $idProdtype,
                    'created_at' => $tgl
                ]);
            }
            if ($insert) {
                return redirect()->to(base_url(session()->get('role') . '/dataorderperjarum/' . $id))->withInput()->with('success', 'Data Model Inserted');
            } else {
                return redirect()->to(base_url(session()->get('role') . '/dataorderperjarum/' . $id))->withInput()->with('error', 'Data Model Not Insert');
            }
        } else {
            return redirect()->to(base_url(session()->get('role') . '/dataorderperjarum/' . $id))->withInput()->with('error', 'Please Check Model number');
        }
    }

    public function importModel()
    {
        $file = $this->request->getFile('excel_file');
        if ($file->isValid() && !$file->hasMoved()) {
            $spreadsheet = IOFactory::load($file);
            $row = $spreadsheet->getActiveSheet();
            $nomodel = $this->request->getVar('no_model');
            $idModel = $this->orderModel->getId($nomodel);
            $startRow = 4; // Ganti dengan nomor baris mulai
            foreach ($spreadsheet->getActiveSheet()->getRowIterator($startRow) as $row) {
                $rowIndex = $row->getRowIndex();
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                $row = [];
                foreach ($cellIterator as $cell) {
                    $row[] = $cell->getValue();
                }
                if (!empty($row)) {
                    $no_models = $row[29];
                    $firstSpacePosition = strpos($no_models, ' '); // Cari posisi spasi pertama
                    $no_model = substr($no_models, 0, $firstSpacePosition);
                    if ($no_model != $nomodel) {
                        return redirect()->to(base_url(session()->get('role') . '/semuaOrder'))->with('error', 'Nomor Model Tidak Sama. Silahkan periksa kembali' . $rowIndex);
                    } else {
                        if ($row[5] == null) {
                            return redirect()->to(base_url(session()->get('role') . '/semuaOrder'))->with('error', 'GAGAL');
                        } else {
                            $recordID = $row[0];
                            $articleNo = $row[30];
                            $producttype = $row[5];
                            $custCode = $row[7];
                            $description = $row[10];
                            $delivery = $row[11];
                            $rdelivery = str_replace('/', '-', (substr($delivery, -10)));
                            $delivery2 = date('Y-m-d', strtotime($rdelivery));
                            $qty = $row[12];
                            $country = $row[17];
                            $color = $row[18];
                            $size = $row[19];
                            $sam = $row[20];
                            if ($sam == null) {
                                $sam = 185;
                            }
                            $machinetypeid = $row[22];
                            $prodtype = [
                                'jarum' => $machinetypeid,
                                'prodtype' => $producttype
                            ];
                            $idProduct = $this->productModel->getId($prodtype);

                            $seam = $row[23];
                            $leadtime = $row[24];
                            $processRoute = $row[25];
                            $lcoDate = $row[26];
                            $rlcoDate = str_replace('/', '-', (substr($lcoDate, -10)));
                            $lcoDate2 = date('Y-m-d', strtotime($rlcoDate));


                            $simpandata = [
                                'machinetypeid' => $machinetypeid,
                                'size' => $size,
                                'mastermodel' => $nomodel,
                                'no_order' => $articleNo,
                                'delivery' => $delivery2,
                                'qty' => $qty,
                                'sisa' => $qty,
                                'country' => $country,
                                'color' => $color,
                                'seam' => $seam,
                                'process_routes' => $processRoute,
                                'smv' => $sam,
                                'production_unit' => 'PU Belum Dipilih',
                                'factory' => 'Belum Ada Area'
                            ];

                            $updateData = [
                                'kd_buyer_order' => $custCode,
                                'id_product_type' => $idProduct,
                                'seam' => $seam,
                                'leadtime' => $leadtime,
                                'description' => $description
                            ];
                            $validate = [
                                'size' => $size,
                                'delivery' => $delivery2,
                                'mastermodel' => $nomodel,
                                'country' => $country,
                                'qty' => $qty
                            ];

                            $existingAps = $this->ApsPerstyleModel->checkAps($validate);
                            if (!$existingAps) {
                                $this->ApsPerstyleModel->insert($simpandata);
                            } else {
                                $id = $existingAps['idapsperstyle'];
                                $qtyLama = $existingAps['qty'];
                                $qtyBaru = $qty + $qtyLama;
                                $this->ApsPerstyleModel->update($id, ['qty' => $qtyBaru, 'sisa' => $qtyBaru]);
                            }
                            $this->orderModel->update($idModel, $updateData);

                            // }
                        }
                    }
                }
            }
            return redirect()->to(base_url(session()->get('role') . '/belumImport'))->withInput()->with('success', 'Data Berhasil di Import');
        } else {
            return redirect()->to(base_url(session()->get('role') . '/belumImport'))->with('error', 'No data found in the Excel file');
        }
    }
    public function deletedetailorder($idModel)
    {

        $idModel = $this->request->getPost("no_model");
        $id = $idModel;
        $delete = $this->ApsPerstyleModel->where('Mastermodel', $id)->delete();
        if ($delete) {
            return redirect()->to(base_url(session()->get('role') . '/semuaOrder/'))->withInput()->with('success', 'Data Berhasil Di Hapus');
        } else {
            return redirect()->to(base_url(session()->get('role') . '/semuaOrder/'))->withInput()->with('error', 'Gagal Hapus Data');
        }
    }

    // Planning
    public function detailModelPlanning($noModel, $delivery)
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
    public function detailPdk($noModel, $jarum)
    {
        $pdk = $this->ApsPerstyleModel->getSisaPerDeliv($noModel, $jarum);
        $stok = $this->stokPdk->where('mastermodel', $noModel)->findAll() ?? null;
        // dd($stok);
        $history = $this->historyRev->getData($noModel);
        $repeat = $this->orderModel
            ->select('repeat_from')
            ->where('no_model', $noModel)
            ->first()['repeat_from'] ?? null;
        $sisaPerDeliv = [];
        foreach ($pdk as $perdeliv) {
            $deliv = $perdeliv['delivery'];
            $sisaPerDeliv[$deliv] = $this->ApsPerstyleModel->getSisaPerDlv($noModel, $jarum, $deliv);
        }
        foreach ($sisaPerDeliv as $deliv => $list) {
            $totalqty = 0;
            $qty = 0;
            if (is_array($list)) {
                foreach ($list as $val) {
                    if (isset($val['sisa'])) {
                        $qty += $val['qty'];
                        $totalqty = $qty;
                    }
                }
            }
            $sisaPerDeliv[$deliv]['totalQty'] = $totalqty;
        }
        $totalPo = $this->ApsPerstyleModel->totalPo($noModel);
        // ini ngambil jumlah qty
        $sisaArray = array_column($pdk, 'sisa');
        $maxValue = max($sisaArray);
        $indexMax = array_search($maxValue, $sisaArray);
        $totalQty = 0;
        for ($i = 0; $i <= $indexMax; $i++) {
            $totalQty += $sisaArray[$i];
        }

        // ini ngambil jumlah hari
        usort($pdk, function ($a, $b) {
            return strtotime($a['delivery']) - strtotime($b['delivery']);
        });
        $totalQty = round($totalQty / 24);
        $deliveryTerjauh = end($pdk)['delivery'];
        $today = new DateTime(date('Y-m-d'));
        $deliveryDate = new DateTime($deliveryTerjauh); // Tanggal delivery terjauh
        $diff = $today->diff($deliveryDate);
        $hari = $diff->days - 7;

        // ini ngambil delivery bottleneck

        $deliveryMax = $pdk[$indexMax]['delivery'];
        $tglDeliv = new DateTime($deliveryMax); // Tanggal delivery terjauh
        $beda = $today->diff($tglDeliv);
        $hariTarget = $beda->days - 7;
        $hariTarget = ($hariTarget <= 0) ? 1 : $hariTarget;

        // disini ngambil rata rata target.
        $smvArray = array_column($pdk, 'smv');
        $smvArray = array_map('intval', $smvArray);
        $averageSmv = array_sum($smvArray) / count($smvArray);
        $target = round((86400 / (intval($averageSmv))) * 0.85 / 24);

        // ini baru kalkulasi
        $mesin = round($totalQty / $target / $hariTarget);
        $targetPerhari = round($mesin * $target);


        // ini bagian rekomendasi (hard bgt bjir)
        $start = date('Y-m-d', strtotime('+7 days'));
        $rekomen = $this->ApsPerstyleModel->getSisaOrderforRec($jarum, $start, $deliveryTerjauh);
        $rekomendasi = [];
        foreach ($rekomen as $rec) {
            $sisa = round($rec['sisa'] / 24);
            $area = $rec['factory'];
            $mesinPerarea = $this->jarumModel->mesinPerArea($jarum, $area);
            if (!empty($mesinPerarea)) {
                $target = $mesinPerarea[0]['target'];
                $totalMesin = $mesinPerarea[0]['totalMesin'];
                $kapasitasPerhari = ($target * $totalMesin);
                $usedCapacityDaily = round($sisa / $hariTarget);
                $availCapacityDaily = $kapasitasPerhari - $usedCapacityDaily;

                // Tetap simpan area, tapi label avail sebagai 'N/A' jika kapasitas avail kurang dari target
                $avail = ($availCapacityDaily >= $targetPerhari) ? $availCapacityDaily : 'Only ' . $availCapacityDaily;

                $rekomendasi[$area] = [
                    'area' => $area,
                    'max' => $kapasitasPerhari,
                    'used' => $usedCapacityDaily,
                    'avail' => $avail
                ];
            }
        }
        usort($rekomendasi, function ($a, $b) {
            // Handle string "Only" vs angka
            if (is_string($a['avail'])) return 1; // Push "Only" ke belakang
            if (is_string($b['avail'])) return -1; // Push "Only" ke belakang

            // Jika keduanya angka, bandingkan nilai 'avail'
            return $b['avail'] <=> $a['avail'];
        });
        $top3Rekomendasi = array_slice($rekomendasi, 0, 3);
        $dataMc = $this->jarumModel->getAreabyJarum($jarum);
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
            'order' => $sisaPerDeliv,
            'headerRow' => $pdk,
            'noModel' => $noModel,
            'dataMc' => $dataMc,
            'jarum' => $jarum,
            'kebMesin' => $mesin,
            'target' => $targetPerhari,
            'hari' => $hari,
            'rekomendasi' => $top3Rekomendasi,
            'totalPo' => $totalPo,
            'historyRev' => $history,
            'repeat' => $repeat,
            'stok' => $stok
        ];
        return view(session()->get('role') . '/Order/detailPdk', $data);
    }

    public function orderBlmAdaAreal()
    {

        $tampilperdelivery = $this->orderModel->tampilPerModelBlmAdaArea();
        foreach ($tampilperdelivery as &$key) {
            $delivery = new DateTime($key['delivery']);
            $ayeuna = new DateTime(); // Assuming $today is already set as $ayeuna

            $sisahari = $ayeuna->diff($delivery)->days;
            $key['sisahari'] = $sisahari;
        }

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
    public function semuaOrderPlan()
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
        return view(session()->get('role') . '/Order/semuaorder2', $data);
    }
    public function orderPerJarumPlan()
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
    public function orderPerAreaPlan()
    {
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
            'TotalMesin' => $totalMesin,
        ];
        return view(session()->get('role') . '/Order/orderarea', $data);
    }
    public function statusOrder()
    {
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
            'TotalMesin' => $totalMesin,
        ];
        return view(session()->get('role') . '/Order/statusorder', $data);
    }
    public function statusOrderArea($area)
    {
        $areaProgress =  $this->ApsPerstyleModel->getProgressperArea($area);
        $lastmonth = date('Y-m-d', strtotime('- 1 month'));
        // Grup by mastermodel
        $grouped = [];
        foreach ($areaProgress as $item) {
            if ($item['target'] > 0) {

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
        }

        // Filter yang delivery >= hari ini
        $filtered = array_filter($grouped, function ($item) use ($lastmonth) {
            return $item['delivery'] >= $lastmonth;
        });

        usort($filtered, function ($a, $b) {
            return $a['percentage'] <=> $b['percentage'];
        });

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
            'progress' => $filtered,
            'area' => $area
        ];
        return view(session()->get('role') . '/Order/statusorderArea', $data);
    }
    public function getTurunOrder()
    {
        $resultTurunOrder = $this->orderModel->getTurunOrder();
        $charts = $this->orderModel->chartTurun();
        $bulan = array_keys($charts['details']);
        $jumlahTurun = array_values($charts['totals']);
        $data = [
            'role' => session()->get('role'),
            'title' => 'Summary Turun Order',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'active8' => '',
            'details' => $resultTurunOrder,
            'bulan' => $bulan,
            'jumlahPembatalan' => $jumlahTurun,
            'totalChart' => $charts['totals']
        ];
        return view(session()->get('role') . '/Order/turunOrder', $data);
    }

    public function detailturunorder($week, $buyer)
    {
        $resultTurun = $this->orderModel->getDetailTurunOrder($week, $buyer);
        $data = [
            'role' => session()->get('role'),
            'title' => 'Detail Confirm Order',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => 'active',
            'active7' => '',
            'data' => $resultTurun,
        ];
        return view(session()->get('role') . '/Order/detailturunorder', $data);
    }

    public function importsmv()
    {
        $file = $this->request->getFile('excel_file');
        if ($file->isValid() && !$file->hasMoved()) {
            $spreadsheet = IOFactory::load($file);
            $sheet = $spreadsheet->getActiveSheet();
            $startRow = 4; // Ganti dengan nomor baris mulai
            $errorRows = [];

            foreach ($sheet->getRowIterator($startRow) as $rowIndex => $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                $rowData = [];
                foreach ($cellIterator as $cell) {
                    $rowData[] = $cell->getValue();
                }
                if ($rowData[19] == null) {
                    break;
                }
                if (!empty($rowData)) {
                    $no_models = $rowData[29];
                    $firstSpacePosition = strpos($no_models, ' '); // Cari posisi spasi pertama
                    $no_model = substr($no_models, 0, $firstSpacePosition);
                    $size = $rowData[19];
                    $smv = $rowData[20];
                    $validate = [
                        'mastermodel' => $no_model,
                        'size' => $rowData[19],
                        'smv' => $smv
                    ];
                    $id = $this->ApsPerstyleModel->getIdSmv($validate);
                    if ($id === null) {
                        $errorRows[] = "ID not found at row " . ($rowIndex + $startRow);
                        continue;
                    }
                    $Id = $id['idapsperstyle'] ?? 0;

                    $update = $this->ApsPerstyleModel->update($Id, ['smv' => $smv]);

                    if (!$update) {
                        $errorRows[] = "Failed to update row " . ($rowIndex + $startRow);
                    }
                }
            }

            if (!empty($errorRows)) {
                $errorMessage = "Errors occurred:\n" . implode("\n", $errorRows);
                dd($errorMessage);
                return redirect()->to(base_url(session()->get('role') . '/smvimport'))->withInput()->with('error', $errorMessage);
            } else {
                return redirect()->to(base_url(session()->get('role') . '/smvimport'))->withInput()->with('success', 'Data Berhasil di Update');
            }
        } else {
            return redirect()->to(base_url(session()->get('role') . '/smvimport'))->withInput()->with('error', 'No data found in the Excel file');
        }
    }
    public function importupdatesmv()
    {
        $file = $this->request->getFile('excel_file');
        if ($file->isValid() && !$file->hasMoved()) {
            $spreadsheet = IOFactory::load($file);
            $sheet = $spreadsheet->getActiveSheet();
            $startRow = 5; // Ganti dengan nomor baris mulai
            $errorRows = [];

            foreach ($sheet->getRowIterator($startRow) as $rowIndex => $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                $rowData = [];
                foreach ($cellIterator as $cell) {
                    $rowData[] = $cell->getValue();
                }
                if ($rowData[2] == null) {
                    break;
                }
                if (!empty($rowData)) {
                    $no_model = $rowData[1];
                    $size = $rowData[2];
                    $smv = $rowData[3];
                    $validate = [
                        'mastermodel' => $no_model,
                        'size' => $size,
                        'smv' => $smv
                    ];
                    $id = $this->ApsPerstyleModel->getIdSmv($validate);
                    if ($id === null) {
                        $errorRows[] = "ID not found at row " . ($rowIndex + $startRow);
                        continue;
                    }
                    foreach ($id as $Id) {
                        $idaps = $Id['idapsperstyle'];
                        $update = $this->ApsPerstyleModel->update($Id, ['smv' => $smv]);
                        $insert = [
                            'style' => $size,
                            'smv_old' => $Id['smv'],
                        ];
                        if (!$update) {
                            $errorRows[] = "Failed to update row " . ($rowIndex + $startRow);
                        } else {
                            $this->historysmv->insert($insert);
                        }
                    }
                }
            }
            if (!empty($errorRows)) {
                $errorMessage = "Errors occurred:\n" . implode("\n", $errorRows);
                dd($errorMessage);
                return redirect()->to(base_url(session()->get('role') . '/updatesmv'))->withInput()->with('error', $errorMessage);
            } else {
                return redirect()->to(base_url(session()->get('role') . '/updatesmv'))->withInput()->with('success', 'Data Berhasil di Update');
            }
        } else {
            return redirect()->to(base_url(session()->get('role') . '/updatesmv'))->withInput()->with('error', 'No data found in the Excel file');
        }
    }
    public function smvimport()
    {
        return view(session()->get('role') . '/smvimport');
    }
    public function sisaOrder()
    {
        $role = session()->get('role');
        $buyer = $this->orderModel->getBuyer();

        $data = [
            'role' => $role,
            'title' => 'Data Order',
            'active1' => '',
            'active2' => '',
            'active3' => 'active',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'buyer' => $buyer,
            'role' => $role
        ];
        return view($role . '/Order/sisaOrder', $data);
    }
    public function sisaOrderBuyer($buyer)
    {
        $weekCount = 1;
        $role = session()->get('role');
        $month = $this->request->getPost('month');
        $yearss = $this->request->getPost('year');

        // Jika bulan atau tahun tidak diisi, gunakan bulan dan tahun ini
        if (empty($month) || empty($yearss)) {
            $bulan = date('Y-m-01', strtotime('this month')); // Bulan ini
        } else {
            // Atur tanggal berdasarkan input bulan dan tahun dari POST
            $bulan = date('Y-m-01', strtotime("$yearss-$month-01"));
        }

        $years = [];
        $currentYear = date('Y');
        $startYear = $currentYear - 2;
        $endYear = $currentYear + 7;

        // Loop dari tahun ini sampai 10 tahun ke depan
        for ($year = $startYear; $year <= $endYear; $year++) {
            $months = [];

            // Loop untuk setiap bulan dalam setahun
            for ($i = 1; $i <= 12; $i++) {
                $monthName = date('F', mktime(0, 0, 0, $i, 1)); // Nama bulan
                $months[] = $monthName;
            }

            // Simpan data tahun dengan bulan-bulannya
            $years[$year] = array_unique($months); // array_unique memastikan bulan unik meskipun tidak perlu dalam kasus ini
        }

        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthName = date('F', mktime(0, 0, 0, $i, 1)); // Nama bulan
            $months[] = $monthName;
        }
        $months = array_unique($months);

        // Ambil data dari model
        $startDate = new \DateTime($bulan); // Awal bulan
        $startDate->setTime(0, 0, 0);
        $endDate = (clone $startDate)->modify('last day of this month');   // Akhir bulan

        // Loop data
        $data = $this->ApsPerstyleModel->getBuyerOrder($buyer, $bulan);
        // dd($data);
        $allData = [];
        $week = [];
        $totalPerWeek = [];

        foreach ($data as $id) {
            $mastermodel = $id['mastermodel'];
            $machinetypeid = $id['machinetypeid'];
            $factory = $id['factory'];

            // Ambil data qty, sisa, dan produksi
            $qty = $id['qty'];
            $sisa = $id['sisa'];
            $produksi = $qty - $sisa;
            $deliveryDate = new \DateTime($id['delivery']); // Asumsikan ada field delivery

            // Loop untuk membagi data ke dalam minggu
            $weekCount = 1;
            $currentStartDate = clone $startDate;

            for ($weekCount = 1; $currentStartDate <= $endDate; $weekCount++) {
                $endOfWeek = (clone $currentStartDate)->modify('Sunday this week');
                $endOfWeek = min($endOfWeek, $endDate);
                $dateWeek = $currentStartDate->format('d') . " - " . $endOfWeek->format('d');
                $week[$weekCount] = $dateWeek;
                // dd($currentStartDate);
                // Periksa apakah tanggal pengiriman berada dalam minggu ini
                if ($deliveryDate >= $currentStartDate && $deliveryDate <= $endOfWeek) {

                    // Ambil total jl_mc untuk minggu ini dan jumlahkan jika sudah ada data sebelumnya
                    $dataOrder = [
                        'model' => $mastermodel,
                        'jarum' => $machinetypeid,
                        'area' => $factory,
                        'delivery' => $id['delivery'],
                    ];
                    $jlMc = 0;
                    $jlMcData = $this->produksiModel->getJlMc($dataOrder);

                    // Pastikan data jl_mc ada
                    if ($jlMcData) {
                        // Loop untuk menjumlahkan jl_mc
                        foreach ($jlMcData as $mc) {
                            $jlMc += $mc['jl_mc'];
                        }
                    }
                    $allData[$mastermodel][$machinetypeid][$factory][$weekCount][] = json_encode([
                        'del' => $id['delivery'],
                        'qty' => $qty,
                        'prod' => $produksi,
                        'sisa' => $sisa,
                        'jlMc' => $jlMc,
                    ]);

                    // Hitung total per minggu
                    if (!isset($totalPerWeek[$weekCount])) {
                        $totalPerWeek[$weekCount] = [
                            'totalQty' => 0,
                            'totalProd' => 0,
                            'totalSisa' => 0,
                            'totalJlMc' => 0,
                        ];
                    }
                    $totalPerWeek[$weekCount]['totalQty'] += $qty;
                    $totalPerWeek[$weekCount]['totalProd'] += $produksi;
                    $totalPerWeek[$weekCount]['totalSisa'] += $sisa;
                    $totalPerWeek[$weekCount]['totalJlMc'] += $jlMc;
                }

                // Pindahkan ke minggu berikutnya
                $currentStartDate = (clone $endOfWeek)->modify('+1 day');
            }
        }

        // Proses data per jarum
        $dataPerjarum = $this->ApsPerstyleModel->getBuyerOrderPejarum($buyer, $bulan);
        $allDataPerjarum = [];
        $totalPerWeekJrm = [];

        foreach ($dataPerjarum as $id2) {
            $machinetypeid = $id2['machinetypeid'];
            $delivery = $id2['delivery'];
            $qty = $id2['qty'];
            $sisa = $id2['sisa'];
            $produksi = $qty - $sisa;
            $deliveryDate = new \DateTime($delivery); // Tanggal pengiriman

            $weekCount = 1;
            $currentStartDate = clone $startDate;
            for ($weekCount = 1; $currentStartDate <= $endDate; $weekCount++) {
                $endOfWeek = (clone $currentStartDate)->modify('Sunday this week');
                $endOfWeek = min($endOfWeek, $endDate);

                // Periksa apakah tanggal pengiriman berada dalam minggu ini
                if ($deliveryDate >= $currentStartDate && $deliveryDate <= $endOfWeek) {
                    $jlMcJrm = 0;
                    $dataOrder2 = [
                        'buyer' => $buyer,
                        'jarum' => $machinetypeid,
                        'delivery' => $delivery,
                    ];
                    $jlMcJrmData = $this->produksiModel->getJlMcJrm($dataOrder2);
                    if ($jlMcJrmData) {
                        foreach ($jlMcJrmData as $mcJrm) {
                            $jlMcJrm += $mcJrm['jl_mc'];
                        }
                    }

                    // Pastikan array utama memiliki key jarum
                    if (!isset($allDataPerjarum[$machinetypeid])) {
                        $allDataPerjarum[$machinetypeid] = [];
                    }
                    // Pastikan minggu tersedia
                    if (!isset($allDataPerjarum[$machinetypeid][$weekCount])) {
                        $allDataPerjarum[$machinetypeid][$weekCount] = [
                            'qtyJrm' => 0,
                            'prodJrm' => 0,
                            'sisaJrm' => 0,
                            'jlMcJrm' => 0,
                        ];
                    }

                    // Tambahkan data minggu
                    $allDataPerjarum[$machinetypeid][$weekCount]['qtyJrm'] += $qty;
                    $allDataPerjarum[$machinetypeid][$weekCount]['prodJrm'] += $produksi;
                    $allDataPerjarum[$machinetypeid][$weekCount]['sisaJrm'] += $sisa;
                    $allDataPerjarum[$machinetypeid][$weekCount]['jlMcJrm'] += $jlMcJrm;

                    // Hitung total per minggu
                    if (!isset($totalPerWeekJrm[$weekCount])) {
                        $totalPerWeekJrm[$weekCount] = [
                            'totalQty' => 0,
                            'totalProd' => 0,
                            'totalSisa' => 0,
                            'totalJlMc' => 0,
                        ];
                    }
                    $totalPerWeekJrm[$weekCount]['totalQty'] += $qty;
                    $totalPerWeekJrm[$weekCount]['totalProd'] += $produksi;
                    $totalPerWeekJrm[$weekCount]['totalSisa'] += $sisa;
                    $totalPerWeekJrm[$weekCount]['totalJlMc'] += $jlMcJrm;
                }

                // Pindahkan ke minggu berikutnya
                $currentStartDate = (clone $endOfWeek)->modify('+1 day');
            }
        }
        $maxWeekCount = $weekCount - 1;

        $data = [
            'role' => $role,
            'title' => 'Data Sisa Order ' . $buyer,
            'active1' => '',
            'active2' => '',
            'active3' => 'active',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'buyer' => $buyer,
            'bulan' => $bulan,
            'maxWeek' => $maxWeekCount,
            'allData' => $allData,
            'totalData' => $totalPerWeek,
            'allDataJrm' => $allDataPerjarum,
            'totalDataJrm' => $totalPerWeekJrm,
            'years' => $years,
            'months' => $months,
            'week' => $week,
        ];
        // dd($data);
        return view(session()->get('role') . '/Order/detailSisaOrder', $data);
    }
    public function sisaOrderArea()
    {
        $role = session()->get('role');
        $area = $this->areaModel->getArea();

        $month = $this->request->getPost('month');
        $yearss = $this->request->getPost('year');

        // Jika bulan atau tahun tidak diisi, gunakan bulan dan tahun ini
        if (empty($month) || empty($yearss)) {
            $bulan = date('Y-m-01', strtotime('this month')); // Bulan ini
        } else {
            // Atur tanggal berdasarkan input bulan dan tahun dari POST
            $bulan = date('Y-m-01', strtotime("$yearss-$month-01"));
        }

        $years = [];
        $currentYear = date('Y');
        $startYear = $currentYear - 2;
        $endYear = $currentYear + 7;

        // Loop dari tahun ini sampai 10 tahun ke depan
        for ($year = $startYear; $year <= $endYear; $year++) {
            $months = [];

            // Loop untuk setiap bulan dalam setahun
            for ($i = 1; $i <= 12; $i++) {
                $monthName = date('F', mktime(0, 0, 0, $i, 1)); // Nama bulan
                $months[] = $monthName;
            }

            // Simpan data tahun dengan bulan-bulannya
            $years[$year] = array_unique($months); // array_unique memastikan bulan unik meskipun tidak perlu dalam kasus ini
        }

        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthName = date('F', mktime(0, 0, 0, $i, 1)); // Nama bulan
            $months[] = $monthName;
        }
        $months = array_unique($months);

        $data = [
            'role' => $role,
            'title' => 'Data Order',
            'active1' => '',
            'active2' => '',
            'active3' => 'active',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'area' => $area,
            'years' => $years,
            'months' => $months,
            'role' => $role
        ];
        return view($role . '/Order/sisaOrderArea', $data);
    }
    public function detailSisaOrderArea($ar)
    {
        // $ar = $this->request->getPost('area') ?: "";
        $role = session()->get('role');
        $month = $this->request->getPost('month');
        $yearss = $this->request->getPost('year');

        // Jika bulan atau tahun tidak diisi, gunakan bulan dan tahun ini
        if (empty($month) || empty($yearss)) {
            $bulan = date('Y-m-01', strtotime('this month')); // Bulan ini
        } else {
            // Atur tanggal berdasarkan input bulan dan tahun dari POST
            $bulan = date('Y-m-01', strtotime("$yearss-$month-01"));
        }

        $years = [];
        $currentYear = date('Y');
        $startYear = $currentYear - 2;
        $endYear = $currentYear + 7;

        // Loop dari tahun ini sampai 10 tahun ke depan
        for ($year = $startYear; $year <= $endYear; $year++) {
            $months = [];

            // Loop untuk setiap bulan dalam setahun
            for ($i = 1; $i <= 12; $i++) {
                $monthName = date('F', mktime(0, 0, 0, $i, 1)); // Nama bulan
                $months[] = $monthName;
            }

            // Simpan data tahun dengan bulan-bulannya
            $years[$year] = array_unique($months); // array_unique memastikan bulan unik meskipun tidak perlu dalam kasus ini
        }

        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthName = date('F', mktime(0, 0, 0, $i, 1)); // Nama bulan
            $months[] = $monthName;
        }
        $months = array_unique($months);
        // dd($bulan);

        // Ambil tanggal awal dan akhir bulan
        $startDate = new \DateTime($bulan); // Awal bulan
        $startDate->setTime(0, 0, 0);
        $endDate = (clone $startDate)->modify('last day of this month');   // Akhir bulan

        $data = $this->ApsPerstyleModel->getAreaOrder($ar, $bulan);
        $allData = [];
        $totalPerWeek = [];
        $weekCount = 0;
        $week = [];

        foreach ($data as $id) {
            $mastermodel = $id['mastermodel'];
            $machinetypeid = $id['machinetypeid'];
            $factory = $id['factory'];

            // Ambil data qty, sisa, dan produksi
            $qty = $id['qty'];
            $sisa = $id['sisa'];
            $produksi = $qty - $sisa;
            $deliveryDate = new \DateTime($id['delivery']); // Asumsikan ada field delivery

            // Loop untuk membagi data ke dalam minggu
            $weekCount = 1;
            $currentStartDate = clone $startDate;

            for ($weekCount = 1; $currentStartDate <= $endDate; $weekCount++) {
                $endOfWeek = (clone $currentStartDate)->modify('Sunday this week');
                $endOfWeek = min($endOfWeek, $endDate);
                $dateWeek = $currentStartDate->format('d') . " - " . $endOfWeek->format('d');
                $week[$weekCount] = $dateWeek;

                // Periksa apakah tanggal pengiriman berada dalam minggu ini
                if ($deliveryDate >= $currentStartDate && $deliveryDate <= $endOfWeek) {
                    // Ambil total jl_mc untuk minggu ini dan jumlahkan jika sudah ada data sebelumnya
                    $dataOrder = [
                        'model' => $mastermodel,
                        'jarum' => $machinetypeid,
                        'area' => $factory,
                        'delivery' => $id['delivery'],
                    ];
                    $jlMc = 0;
                    $jlMcData = $this->produksiModel->getJlMc($dataOrder);

                    // Pastikan data jl_mc ada
                    if ($jlMcData) {
                        // Loop untuk menjumlahkan jl_mc
                        foreach ($jlMcData as $mc) {
                            $jlMc += $mc['jl_mc'];
                        }
                    }

                    $allData[$mastermodel][$machinetypeid][$factory][$weekCount][] = json_encode([
                        'del' => $id['delivery'],
                        'qty' => $qty,
                        'prod' => $produksi,
                        'sisa' => $sisa,
                        'jlMc' => $jlMc,
                    ]);

                    // Hitung total per minggu
                    if (!isset($totalPerWeek[$weekCount])) {
                        $totalPerWeek[$weekCount] = [
                            'totalQty' => 0,
                            'totalProd' => 0,
                            'totalSisa' => 0,
                            'totalJlMc' => 0,
                        ];
                    }
                    $totalPerWeek[$weekCount]['totalQty'] += $qty;
                    $totalPerWeek[$weekCount]['totalProd'] += $produksi;
                    $totalPerWeek[$weekCount]['totalSisa'] += $sisa;
                    $totalPerWeek[$weekCount]['totalJlMc'] += $jlMc;
                }

                // Pindahkan ke minggu berikutnya
                $currentStartDate = (clone $endOfWeek)->modify('+1 day');
            }
        }

        $allDataPerjarum = [];
        $totalPerWeekJrm = []; // Total per minggu
        $dataPerjarum = $this->ApsPerstyleModel->getAreaOrderPejarum($ar, $bulan);

        foreach ($dataPerjarum as $id2) {
            $machinetypeid = $id2['machinetypeid'];
            $delivery = $id2['delivery'];
            $qty = $id2['qty'];
            $sisa = $id2['sisa'];
            $produksi = $qty - $sisa;
            $deliveryDate = new \DateTime($delivery); // Tanggal pengiriman

            $weekCount = 1;
            $currentStartDate = clone $startDate;
            for ($weekCount = 1; $currentStartDate <= $endDate; $weekCount++) {
                $endOfWeek = (clone $currentStartDate)->modify('Sunday this week');
                $endOfWeek = min($endOfWeek, $endDate);

                // Periksa apakah tanggal pengiriman berada dalam minggu ini
                if ($deliveryDate >= $currentStartDate && $deliveryDate <= $endOfWeek) {
                    $jlMcJrm = 0;
                    $dataOrder2 = [
                        'area' => $ar,
                        'jarum' => $machinetypeid,
                        'delivery' => $delivery,
                    ];
                    $jlMcJrmData = $this->produksiModel->getJlMcJrmArea($dataOrder2);
                    if ($jlMcJrmData) {
                        foreach ($jlMcJrmData as $mcJrm) {
                            $jlMcJrm += $mcJrm['jl_mc'];
                        }
                    }

                    // Pastikan array utama memiliki key jarum
                    if (!isset($allDataPerjarum[$machinetypeid])) {
                        $allDataPerjarum[$machinetypeid] = [];
                    }
                    // Pastikan minggu tersedia
                    if (!isset($allDataPerjarum[$machinetypeid][$weekCount])) {
                        $allDataPerjarum[$machinetypeid][$weekCount] = [
                            'qtyJrm' => 0,
                            'prodJrm' => 0,
                            'sisaJrm' => 0,
                            'jlMcJrm' => 0,
                        ];
                    }

                    // Tambahkan data minggu
                    $allDataPerjarum[$machinetypeid][$weekCount]['qtyJrm'] += $qty;
                    $allDataPerjarum[$machinetypeid][$weekCount]['prodJrm'] += $produksi;
                    $allDataPerjarum[$machinetypeid][$weekCount]['sisaJrm'] += $sisa;
                    $allDataPerjarum[$machinetypeid][$weekCount]['jlMcJrm'] += $jlMcJrm;

                    // Hitung total per minggu
                    if (!isset($totalPerWeekJrm[$weekCount])) {
                        $totalPerWeekJrm[$weekCount] = [
                            'totalQty' => 0,
                            'totalProd' => 0,
                            'totalSisa' => 0,
                            'totalJlMc' => 0,
                        ];
                    }
                    $totalPerWeekJrm[$weekCount]['totalQty'] += $qty;
                    $totalPerWeekJrm[$weekCount]['totalProd'] += $produksi;
                    $totalPerWeekJrm[$weekCount]['totalSisa'] += $sisa;
                    $totalPerWeekJrm[$weekCount]['totalJlMc'] += $jlMcJrm;
                }

                // Pindahkan ke minggu berikutnya
                $currentStartDate = (clone $endOfWeek)->modify('+1 day');
            }
        }

        $maxWeekCount = max(0, $weekCount - 1);

        $data = [
            'role' => $role,
            'title' => 'Data Sisa Order',
            'active1' => '',
            'active2' => '',
            'active3' => 'active',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'area' => $ar,
            'bulan' => $bulan,
            'maxWeek' => $maxWeekCount,
            'allData' => $allData,
            'totalData' => $totalPerWeek,
            'allDataJrm' => $allDataPerjarum,
            'totalDataJrm' => $totalPerWeekJrm,
            'years' => $years,
            'months' => $months,
            'week' => $week,
        ];
        // dd($data);
        return view($role . '/Order/detailSisaOrderArea', $data);
    }


    public function reviseorder()
    {
        $file = $this->request->getFile('excel_file');
        $nomodel = $this->request->getVar('no_model');
        $keterangan = $this->request->getPost('keterangan');
        $jarum = $this->request->getPost('jarum');
        $today = DATE('Y-m-d H:i:s');

        if ($file->isValid() && !$file->hasMoved()) {
            $spreadsheet = IOFactory::load($file);
            $row = $spreadsheet->getActiveSheet();
            $nomodel = $this->request->getVar('no_model');
            $idModel = $this->orderModel->getId($nomodel);
            $this->ApsPerstyleModel->setZero($nomodel);
            $startRow = 4; // Ganti dengan nomor baris mulai
            foreach ($spreadsheet->getActiveSheet()->getRowIterator($startRow) as $row) {
                $rowIndex = $row->getRowIndex();
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                $row = [];
                foreach ($cellIterator as $cell) {
                    $row[] = $cell->getValue();
                }
                if (!empty($row)) {
                    $no_models = $row[29];
                    $firstSpacePosition = strpos($no_models, ' '); // Cari posisi spasi pertama
                    $no_model = substr($no_models, 0, $firstSpacePosition);
                    $machinetypeid = $row[22];
                    if ($machinetypeid == "DC168L") {
                        $machinetypeid = $machinetypeid . "SF";
                    }
                    if (empty($machinetypeid)) {
                        log_message('error', "machinetypeid kosong di baris ke-$rowIndex. Data tidak disimpan.");
                        continue;
                    }
                    // if ($row[5] == null) {
                    //     return redirect()->to(base_url(session()->get('role') . '/detailPdk/' . $nomodel . '/' . $jarum))->withInput()->with('error', 'Data Gagal di revise');
                    // } else {
                    // if ($no_model != $nomodel) {
                    //     return redirect()->to(base_url(session()->get('role') . '/detailPdk/' . $nomodel . '/' . $jarum))->with('error', 'Nomor Model Tidak Sama. Silahkan periksa kembali' . $rowIndex);
                    // } else {
                    $recordID = $row[0];
                    $articleNo = $row[30];
                    $producttype = $row[5];
                    $custCode = $row[7];
                    $description = $row[10];
                    $delivery = $row[11];
                    $rdelivery = str_replace('/', '-', (substr($delivery, -10)));
                    $delivery2 = date('Y-m-d', strtotime($rdelivery));
                    $qty = $row[12];
                    $country = $row[17];
                    $color = $row[18];
                    $size = $row[19];
                    $sam = $row[20];
                    if ($sam == null) {
                        $sam = 185;
                    }

                    $prodtype = [
                        'jarum' => $machinetypeid,
                        'prodtype' => $producttype
                    ];
                    $idProduct = $this->productModel->getId($prodtype);

                    $leadtime = $row[24];
                    $seam = $row[23];
                    $processRoute = $row[25];
                    $lcoDate = $row[26];
                    $rlcoDate = str_replace('/', '-', (substr($lcoDate, -10)));
                    $lcoDate2 = date('Y-m-d', strtotime($rlcoDate));


                    $simpandata = [
                        'machinetypeid' => $machinetypeid,
                        'size' => $size,
                        'mastermodel' => $nomodel,
                        'no_order' => $articleNo,
                        'delivery' => $delivery2,
                        'qty' => $qty,
                        'sisa' => $qty,
                        'country' => $country,
                        'color' => $color,
                        'seam' => $seam,
                        'process_routes' => $processRoute,
                        'smv' => $sam,
                        'production_unit' => 'PU Belum Dipilih',
                        'factory' => 'Belum Ada Area'
                    ];
                    $updateData = [
                        'kd_buyer_order' => $custCode,
                        'id_product_type' => $idProduct,
                        'seam' => $processRoute,
                        'leadtime' => $leadtime,
                        'description' => $description
                    ];
                    $validate = [
                        'size' => $size,
                        'delivery' => $delivery2,
                        'mastermodel' => $nomodel,
                        'qty' => $qty,
                        'country' => $country,
                    ];

                    $existingAps = $this->ApsPerstyleModel->checkAps($validate);
                    if (!$existingAps) {
                        $this->ApsPerstyleModel->insert($simpandata);
                    } else {
                        $id = $existingAps['idapsperstyle'];
                        $qtyLama = $existingAps['qty'];
                        $qtyBaru = $qty + $qtyLama;
                        $update = [
                            'qty' => $qtyBaru,
                            'seam' => $seam,
                            'process_routes' => $processRoute,
                        ];
                        $this->ApsPerstyleModel->update($id, $update);
                    }
                    $this->orderModel->update($idModel, $updateData);
                    // }
                    // }
                }
            }
            $dataInput = [
                'tanggal_rev' => $today,
                'model' => $nomodel,
                'keterangan' => $keterangan
            ];
            $this->historyRev->insert($dataInput);
            $this->ApsPerstyleModel->setSisaZero($nomodel);
            return redirect()->to(base_url(session()->get('role') . '/detailPdk/' . $nomodel . '/' . $jarum))->withInput()->with('success', 'Data Berhasil di revise');
        } else {
            return redirect()->back()->with('error', 'No data found in the Excel file');
        }
    }
    public function pdkDetail($noModel, $jarum)
    {
        $pdk = $this->ApsPerstyleModel->StylePerDelive($noModel, $jarum);
        $history = $this->historyRev->getData($noModel);
        $pdkPerdlv = [];
        foreach ($pdk as $perdeliv) {
            $deliv = $perdeliv['delivery'];
            $pdkPerdlv[$deliv] = $this->ApsPerstyleModel->StylePerDlv($noModel, $jarum, $deliv);
        }
        foreach ($pdkPerdlv as $deliv => $list) {
            $totalqty = 0;
            $qty = 0;
            if (is_array($list)) {
                foreach ($list as $val) {
                    if (isset($val['sisa'])) {
                        $qty += $val['qty'];
                        $totalqty = $qty;
                    }
                }
            }
            $pdkPerdlv[$deliv]['totalQty'] = $totalqty;
        }
        $totalPo = $this->ApsPerstyleModel->totalPo($noModel);
        // ini ngambil jumlah qty
        $sisaArray = array_column($pdk, 'sisa');
        $maxValue = max($sisaArray);
        $indexMax = array_search($maxValue, $sisaArray);
        $totalQty = 0;
        for ($i = 0; $i <= $indexMax; $i++) {
            $totalQty += $sisaArray[$i];
        }



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
            'order' => $pdkPerdlv,
            'headerRow' => $pdk,
            'noModel' => $noModel,
            'jarum' => $jarum,
            'totalPo' => $totalPo,
            'historyRev' => $history
        ];
        return view(session()->get('role') . '/Order/detailPdk', $data);
    }
    public function estimasispk($area)
    {
        $lastmonth = date('Y-m-01', strtotime('-2 month'));
        $data = $this->ApsPerstyleModel->dataEstimasi($area, $lastmonth);
        $pdkArea = $this->ApsPerstyleModel->getModelArea($area);
        $perStyle = [];
        $requeseted = [];
        $history = $this->estspk->getHistory($area, $lastmonth);
        foreach ($data as $id) {

            // get data produksi
            $dataProd = $this->produksiModel->getProdByPdkSize($id['mastermodel'], $id['size']);
            $sudahMinta = $this->estspk->cekStatus($id['mastermodel'], $id['size'], $area);

            if ($sudahMinta) {
                if ($sudahMinta['status'] === 'sudah') {
                    $sudahMinta['status'] = 'Menunggu Acc';
                }
                $requeseted[] = [
                    'model' => $sudahMinta['model'],
                    'style' => $sudahMinta['style'],
                    'area' => $sudahMinta['area'],
                    'qty' => $sudahMinta['qty'],
                    'status' => $sudahMinta['status'],
                    'updated_at' => $sudahMinta['updated_at'],
                ];
            } else {

                // Hitung nilai akumulasi awal
                $bs =  (int)$dataProd['bs'];
                $qty = (int)$id['qty'];
                $sisa = (int)$id['sisa'];
                $poplus = (int)$id['poplus'];
                $produksi = $qty - $sisa;
                $ttlProd = (int)$dataProd['prod'];
                // Periksa apakah produksi valid dan memenuhi kondisi
                if ($ttlProd > 0) {
                    $percentage = round(($ttlProd / $qty) * 100);
                    $ganti = $bs + $poplus;
                    $estimasi = ($ganti / $ttlProd / 100) * $qty;
                    $perStyle[] = [
                        'model' => $id['mastermodel'],
                        'inisial' => $id['inisial'],
                        'size' => $id['size'],
                        'sisa' => $sisa,
                        'qty' => $qty,
                        'ttlProd' => $ttlProd,
                        'percentage' => $percentage,
                        'bs' => $bs,
                        'poplus' => $poplus,
                        'jarum' => $id['machinetypeid'],
                        'estimasi' => round(($estimasi * 100), 1),
                        'status' => 'belum',
                        'waktu' => '-'
                    ];
                }
            }
        }



        $data2 = [
            'role' => session()->get('role'),
            'title' => 'Data Order',
            'active1' => '',
            'active2' => '',
            'active3' => 'active',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'data' => $data,
            'area' => $area,
            'perStyle' => $perStyle,
            'model' => $pdkArea,
            'history' => $history
        ];
        return view(session()->get('role') . '/Order/estimasispk', $data2);
    }
    public function cancelOrder($noModel)
    {
        $alasan = $this->request->getPost("alasan");
        $idaps = $this->ApsPerstyleModel->getIdAps($noModel);
        $qtyData  = $this->ApsPerstyleModel->getQtyCancel($idaps);
        $qtyMap = [];
        foreach ($qtyData as $row) {
            $qtyMap[$row['idapsperstyle']] = $row['qty'];
        }

        // Siapkan data untuk insert
        $dataInsert = [];
        foreach ($idaps as $id) {
            $dataInsert[] = [
                "idapsperstyle" => $id,
                "qty_cancel" => $qtyMap[$id] ?? 0, // Ambil qty sesuai idaps
                "alasan" => $alasan,
            ];
        }

        $insert = $this->cancelOrder->insertBatch($dataInsert);

        if ($insert) {
            $update = $this->ApsPerstyleModel
                ->whereIn('idapsperstyle', $idaps)
                ->set(['qty' => 0, 'sisa' => 0])
                ->update();

            if ($update) {
                return redirect()->to(base_url(session()->get('role') . '/semuaOrder'))->withInput()->with('success', 'Order Berhasil Di Cancel');
            } else {
                return redirect()->to(base_url(session()->get('role') . '/semuaOrder'))->withInput()->with('error', 'Gagal Update Data');
            }
        }
        return redirect()->to(base_url(session()->get('role') . '/semuaOrder'))->with('error', 'Gagal Cancel Order');
    }
    public function getCancelOrder()
    {
        $role = session()->get('role');
        $cancel = $this->cancelOrder->getDataCancel();

        $data = [
            'role' => session()->get('role'),
            'title' => 'Data Cancel Order',
            'active1' => '',
            'active2' => '',
            'active3' => 'active',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'cancel' => $cancel,
            'role' => $role

        ];
        return view($role . '/Order/cancelorder', $data);
    }
    public function detailCancelOrder($pdk)
    {
        $role = session()->get('role');
        $cancel = $this->cancelOrder->getDetailCancel($pdk);

        $data = [
            'role' => session()->get('role'),
            'title' => 'Data Cancel Order',
            'active1' => '',
            'active2' => '',
            'active3' => 'active',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'cancel' => $cancel,
            'role' => $role,
            'pdk' => $pdk,

        ];
        return view($role . '/Order/detailcancelorder', $data);
    }
    public function orderPerMonth($month, $year)
    {
        $order = $this->orderModel->tampilPerBulan($month, $year);
        $qty = 0;
        $sisa = 0;
        foreach ($order as $der) {
            $qty += round($der['qty']);
            $sisa += round($der['sisa']);
        }
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
            'order' => $order,
            'bulan' => $month,
            'tahun' => $year,
            'qty' => $qty,
            'sisa' => $sisa


        ];
        return view(session()->get('role') . '/Order/orderMonthDetail', $data);
    }
    public function inputHistory()
    {
        $jarum = $this->request->getPost('jarum');
        $tanggalInput = $this->request->getPost('tanggal_rev');
        $nomodel = $this->request->getPost('no_model');
        $keterangan = $this->request->getPost('keterangan');

        $tanggal = date('Y-m-d H:i:s', strtotime($tanggalInput));

        $dataInsert = [
            'tanggal_rev' => $tanggal,
            'model' => $nomodel,
            'keterangan' => $keterangan
        ];

        $insert = $this->historyRev->insert($dataInsert);

        if ($insert) {
            return redirect()->to(base_url(session()->get('role') . '/detailPdk/' . $nomodel . '/' . $jarum))->withInput()->with('success', 'Berhasil Input History Revisi');
        } else {
            return redirect()->to(base_url(session()->get('role') . '/detailPdk/' . $nomodel . '/' . $jarum))->withInput()->with('error', 'Gagal Update Data');
        }
    }
    public function spk2()
    {
        // Ambil filter dari input GET
        $tgl = $this->request->getGet('tgl_buat');
        $noModel = $this->request->getGet('no_model');

        $estimasiSpk = $this->estspk->getData($tgl, $noModel);
        foreach ($estimasiSpk as &$spk) {
            $noModel = $spk['model'];
            $styleSize = $spk['style'];
            $area = $spk['area'];
            $idaps = $this->ApsPerstyleModel->getIdApsForPph($area, $noModel, $styleSize);
            $idapsList = array_column($idaps, 'idapsperstyle');
            $spk['qty_order'] = $this->ApsPerstyleModel->getQtyOrder($noModel, $styleSize, $area)['qty'] ?? '-';
            $spk['plus_packing'] = $this->ApsPerstyleModel->getQtyOrder($noModel, $styleSize, $area)['po_plus'] ?? '0';
            $spk['deffect'] =  $this->bsModel->getBsPph($idapsList)['bs_setting'] ?? '-';
        }
        // dd($estimasiSpk);
        $aproved = $this->estspk->getHistorySpk();
        // dd($estimasiSpk);
        $data = [
            'role' => session()->get('role'),
            'title' => 'Pengajuan SPK 2',
            'active1' => '',
            'active2' => '',
            'active3' => 'active',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'data' => $estimasiSpk,
            'history' => $aproved,

        ];
        return view(session()->get('role') . '/Order/pengajuanspk2', $data);
    }
    public function approveSpk2()
    {
        // Ambil array ID yang dikirim via POST
        $ids = $this->request->getPost('data');

        if (empty($ids)) {
            return redirect()
                ->to(base_url(session()->get('role') . '/pengajuanspk2'))
                ->withInput()
                ->with('error', 'Pilih minimal 1 SPK2 untuk di-approve');
        }

        // Update semua baris yang ID-nya ada di array $ids
        $updated = $this->estspk
            ->whereIn('id', $ids)
            ->set('status', 'approved')
            ->update();

        if ($updated) {
            return redirect()
                ->to(base_url(session()->get('role') . '/pengajuanspk2'))
                ->withInput()
                ->with('success', 'Berhasil Approve SPK2');
        } else {
            return redirect()
                ->to(base_url(session()->get('role') . '/pengajuanspk2'))
                ->withInput()
                ->with('error', 'Gagal Approve SPK2');
        }
    }
    public function mintaSpk2()
    {
        $rows = $this->request->getPost('data');

        if (!empty($rows)) {
            $allData = [];

            foreach ($rows as $row) {
                // Validasi minimal field penting
                if (!isset($row['model'], $row['size'], $row['area'])) {
                    continue;
                }

                // Cek apakah sudah ada di database
                $exists = $this->estspk
                    ->where('model', $row['model'])
                    ->where('style', $row['size'])
                    ->where('area', $row['area'])
                    ->where('qty', $row['estimasi'])
                    ->first();

                if (!$exists) {
                    $this->estspk->insert([
                        'model'  => $row['model'],
                        'style'  => $row['size'],
                        'area'   => $row['area'],
                        'qty'    => isset($row['estimasi']) ? (int)$row['estimasi'] : 0,
                        'status' => 'sudah'
                    ]);
                }
            }
            return redirect()->back()->with('sucess', 'Permintaan terikirm');
        } else {
            return redirect()->back()->with('error', 'Tidak ada data yang dipilih.');
        }
    }
    public function spkmanual()
    {
        $items = $this->request->getPost('items');

        if (!$items || !is_array($items)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Data tidak valid.'
            ]);
        }


        foreach ($items as $item) {
            // Cek apakah item sudah ada berdasarkan kombinasi unik
            $exists = $this->estspk
                ->where('model', $item['no_model'])
                ->where('style', $item['size'])
                ->where('area', $item['area'])
                ->where('qty', $item['qty'])
                ->first();

            if (!$exists) {
                // Hanya insert jika belum ada
                $this->estspk->insert([
                    'model' => $item['no_model'],
                    'style' => $item['size'],
                    'area' => $item['area'],
                    'qty'   => $item['qty'],
                    'status' => 'sudah'
                ]);
            }
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Data berhasil disimpan.'
        ]);
    }
    public function saveRepeat()
    {
        $repeat = $this->request->getPost('repeat');
        $model = $this->request->getPost('model');
        $update = $this->orderModel
            ->where('no_model', $model)
            ->set('repeat_from', $repeat)
            ->update();

        if ($update) {
            return redirect()->back()->with('success', 'Berhasil update repeat!');
        } else {
            return redirect()->back()->with('error', 'Gagal update repeat!');
        }
    }
    public function rejectSpk2()
    {
        // Ambil array ID yang dikirim via POST
        $ids = $this->request->getPost('data');

        if (empty($ids)) {
            return redirect()
                ->to(base_url(session()->get('role') . '/pengajuanspk2'))
                ->withInput()
                ->with('error', 'Pilih minimal 1 SPK2 untuk di-reject');
        }

        // Update semua baris yang ID-nya ada di array $ids
        $updated = $this->estspk
            ->whereIn('id', $ids)
            ->set('status', 'Ditolak')
            ->update();

        if ($updated) {
            return redirect()
                ->to(base_url(session()->get('role') . '/pengajuanspk2'))
                ->withInput()
                ->with('success', 'Berhasil reject SPK2');
        } else {
            return redirect()
                ->to(base_url(session()->get('role') . '/pengajuanspk2'))
                ->withInput()
                ->with('error', 'Gagal reject SPK2');
        }
    }

    public function flowProses()
    {
        $model    = $this->request->getGet('mastermodel') ?? '';
        $delivery = $this->request->getGet('delivery')   ?? '';
        // dd($model);
        // Full URL including path:
        $url = $this->urlTls . '/flowproses';

        /** @var \CodeIgniter\HTTP\CURLRequest $client */
        $client = \Config\Services::curlrequest([
            'timeout' => 5,
        ]);

        $response = $client->get($url, [
            'query' => [
                'mastermodel' => $model,
                'delivery'    => $delivery,
            ],
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('API error: ' . $response->getStatusCode());
        }

        $json   = json_decode($response->getBody(), true);
        $flows  = $json['flows'];
        $styles = $json['styles'];

        return $this->response->setJSON([
            'status' => 'success',
            'flows'  => $flows,
            'styles' => $styles,
        ]);
    }

    public function detailPdkAps($noModel, $area)
    {
        $pdk = $this->ApsPerstyleModel->getSisaPerStyleArea($noModel, $area);
        $history = $this->historyRev->getData($noModel);
        $repeat = $this->orderModel
            ->select('repeat_from')
            ->where('no_model', $noModel)
            ->first()['repeat_from'] ?? null;
        $sisaPerStyle = [];
        foreach ($pdk as $perStyle) {
            $style = $perStyle['size'];
            $sisaPerStyle[$perStyle['inisial'] . '|' . $style . '||'  . $perStyle['color']] = $this->ApsPerstyleModel->getSisaPerStyle($noModel, $style);
        }
        foreach ($sisaPerStyle as $style => $list) {
            $totalqty = 0;
            $qty = 0;
            if (is_array($list)) {
                foreach ($list as $val) {
                    if (isset($val['sisa'])) {
                        $qty += $val['qty'];
                        $totalqty = $qty;
                    }
                }
            }
            $sisaPerStyle[$style]['totalQty'] = $totalqty;
        }
        // dd($sisaPerStyle);
        $totalPo = $this->ApsPerstyleModel->totalPo($noModel);
        // ini ngambil jumlah qty
        $sisaArray = array_column($pdk, 'sisa');
        $maxValue = max($sisaArray);
        $indexMax = array_search($maxValue, $sisaArray);
        $totalQty = 0;
        for ($i = 0; $i <= $indexMax; $i++) {
            $totalQty += $sisaArray[$i];
        }


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
            'order' => $sisaPerStyle,
            'headerRow' => $pdk,
            'noModel' => $noModel,
            'historyRev' => $history,
            'repeat' => $repeat,
            'area' => $area
        ];
        return view(session()->get('role') . '/Order/detailPdkAps', $data);
    }

    public function importFlowproses()
    {
        $request      = $this->request;
        $noModel      = $request->getPost('no_model');
        $delivery     = $request->getPost('delivery');
        $needle       = $request->getPost('machinetypeid');
        $tanggalInput = $request->getPost('tanggal_input');

        // 1) Ambil style list dari DB
        $styleList = $this->ApsPerstyleModel->getIdApsForFlowProses($noModel, $needle);

        // 2) Ambil file Excel
        $file = $request->getFile('excel_file');
        if (! $file->isValid()) {
            return redirect()->back()->with('error', 'File tidak valid');
        }

        // 3) Load spreadsheet dan ubah jadi array-assoc per baris
        $spreadsheet = IOFactory::load($file->getTempName());
        $rows        = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
        $header      = array_shift($rows);

        // Inisialisasi hasil
        $insertCount = 0;
        $notMatched  = [];
        $errors      = [];

        // 4) Loop tiap baris data
        foreach ($rows as $idx => $row) {
            $rowData = array_combine($header, $row);
            // Ambil field dasar
            $noModelExcel = $rowData['NO MODEL'] ?? '';
            $areaExcel    = $rowData['AREA']     ?? '';
            $sizeExcel    = $rowData['JC']       ?? '';
            $inisialExcel = $rowData['INISIAL']  ?? '';

            if (empty($noModelExcel) && empty($areaExcel) && empty($sizeExcel)) {
                continue;
            }

            // Jika model tidak cocok, skip
            // if ($noModelExcel !== $noModel) {
            //     continue;
            // }
            // dd ($noModelExcel, $areaExcel, $sizeExcel, $inisialExcel);
            // 5) Extract semua kolom "PROSES *"
            $prosesList = [];
            foreach ($rowData as $colName => $value) {
                if (strpos($colName, 'PROSES') === 0 && trim((string)$value) !== '') {
                    $prosesList[] = trim($value);
                }
            }
            $prosesList = array_values(array_unique($prosesList));
            if (empty($prosesList)) {
                $notMatched[] = ['row' => $idx + 2, 'reason' => 'Tidak ada PROSES'];
                continue;
            }

            // 6) Cari semua idapsperstyle yang cocok
            // dd ($styleList, $prosesList, $sizeExcel, $areaExcel);
            // Gabungkan proses untuk setiap idapsperstyle (1 idaps bisa punya banyak proses)
            $idApsList = [];
            foreach ($styleList as $style) {
                if (
                    $style['mastermodel'] === $noModelExcel
                    && $style['size']    === $sizeExcel
                    && $style['factory'] === $areaExcel
                ) {
                    // Setiap idapsperstyle dapat memiliki banyak proses
                    $idApsList[] = [
                        'idapsperstyle' => $style['idapsperstyle'],
                        'proses'        => $prosesList
                    ];
                    // dd  ($idApsList);
                }
            }
            // dd ($idApsList);
            // Jika tidak ada idapsperstyle yang cocok, catat sebagai not matched
            if (empty($idApsList)) {
                $notMatched[] = [
                    'row'    => $idx + 2,
                    'reason' => 'No Model : ' . $noModelExcel . ' Size : ' . $sizeExcel . ' Area : ' . $areaExcel . ' Tidak Ada'
                ];
                continue;
            }
            // dd ($idApsList);
            // Validasi wajib
            if (empty($areaExcel) || empty($tanggalInput)) {
                $notMatched[] = [
                    'row'    => $idx + 2,
                    'reason' => empty($areaExcel) ? 'Area kosong' : 'Tanggal input kosong'
                ];
                continue;
            }

            // 5) Kirim ke API — hanya kirim array of IDs
            $payload = [
                'idapsperstyle' => $idApsList,
                'tanggal'       => $tanggalInput,
                'area'          => $areaExcel,
                'admin'         => session()->get('username'),
            ];
            // dd ($payload);
            $client = \Config\Services::curlrequest([
                'headers'     => [
                    'Accept'       => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'http_errors' => false,  // penting: CI4 tidak akan throw exception meski status 500
            ]);
            try {
                $response = $client->post($this->urlTls . '/saveFlowProses', [
                    'json'        => $payload,
                    'http_errors' => false,   // supaya tidak otomatis throw
                ]);
                $status = $response->getStatusCode();
                $body   = (string)$response->getBody();
            } catch (\Exception $e) {
                // Log message saja, karena exception-nya tidak menyertakan response
                log_message('error', 'CurlRequest failed: ' . $e->getMessage());
                $errors[] = [
                    'row'    => $idx + 2,
                    'status' => 'Request error: ' . $e->getMessage(),
                ];
                continue;
            }


            $status = $response->getStatusCode();       // misal 200, 422, 500
            $body   = (string) $response->getBody();    // isi JSON error atau success

            if ($status !== 200) {
                $decoded = json_decode($body, true);
                $errorMsg = $decoded['errors']
                    ?? $decoded['error']
                    ?? 'Unknown API error';

                $notMatched[] = [
                    'row'    => $idx + 2,
                    'reason' => $errorMsg,
                ];
                continue;
            }


            $insertCount++;
        }

        // 6) Kembalikan ringkasan
        $summary = [
            'inserted'   => $insertCount,
            'notMatched' => $notMatched,
            'errors'     => $errors,
        ];

        if ($insertCount > 0) {
            $flashType = 'success';
            $message   = 'Flow proses berhasil diimpor';
            $summary['status'] = 'done';
        } else {
            $flashType = 'error';
            // key 'error' nanti bisa dipakai di view untuk alert-danger
            $message   = 'Import Gagal! Tidak ada data yang berhasil di-insert.';
            $summary['status'] = 'fail';
        }

        return redirect()->back()
            ->with($flashType, $message)
            ->with('importSummary', $summary);
    }


    public function importStokOrder()
    {
        $file = $this->request->getFile('excel_file');
        if (!($file && $file->isValid() && !$file->hasMoved())) {
            return redirect()->to(base_url(session()->get('role') . '/dataorder'))
                ->with('error', 'File tidak valid atau tidak ditemukan.');
        }

        try {
            $spreadsheet = IOFactory::load($file);
            $sheet = $spreadsheet->getActiveSheet();
            $startRow = 3; // baris data mulai (header diasumsikan di atas)

            // Mulai transaction agar insert + update konsisten
            $db = \Config\Database::connect();
            $db->transStart();

            foreach ($sheet->getRowIterator($startRow) as $rowObj) {
                $rowIndex = $rowObj->getRowIndex();
                $cellIterator = $rowObj->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);

                $rowData = [];
                foreach ($cellIterator as $cell) {
                    $rowData[] = $cell->getValue();
                }

                // Cek apakah seluruh baris kosong (trim supaya whitespace diabaikan)
                $allEmpty = true;
                foreach ($rowData as $v) {
                    if (is_string($v) && trim($v) !== '') {
                        $allEmpty = false;
                        break;
                    } elseif (!is_string($v) && $v !== null && $v !== '') {
                        $allEmpty = false;
                        break;
                    }
                }
                if ($allEmpty) {
                    continue; // skip baris kosong
                }

                // Ambil field dengan defensif
                $nomodel = isset($rowData[0]) ? trim($rowData[0]) : null;
                $size = isset($rowData[1]) ? trim($rowData[1]) : null;
                $deliveryRaw = $rowData[2] ?? null;
                $qty = isset($rowData[3]) ? $rowData[3] : null;
                $stok = isset($rowData[4]) ? $rowData[4] : null;
                $qty_akhir = isset($rowData[5]) ? $rowData[5] : null;

                if (!$nomodel || !$size) {
                    // skip atau log, karena minimal dua key penting hilang
                    log_message('warning', "Skipping row {$rowIndex}: missing mastermodel or size.");
                    continue;
                }

                // Normalize delivery date (Excel serial or string)
                try {
                    if (is_numeric($deliveryRaw)) {
                        $deliveryDate = (new \DateTimeImmutable('1899-12-30'))
                            ->modify('+' . intval($deliveryRaw) . ' days');
                    } else {
                        $deliveryDate = new \DateTimeImmutable($deliveryRaw);
                    }
                    $delivery2 = $deliveryDate->format('Y-m-d');
                } catch (\Exception $e) {
                    log_message('error', "Invalid delivery date at row {$rowIndex}: " . var_export($deliveryRaw, true));
                    continue; // skip baris dengan tanggal invalid
                }

                $simpandata = [
                    'mastermodel' => $nomodel,
                    'size' => $size,
                    'delivery' => $delivery2,
                    'qty_asli' => $qty,
                    'stok' => $stok,
                    'qty_akhir' => $qty_akhir,
                ];
                $insert = $this->stokPdk->insert($simpandata);
                if ($insert === false) {
                    log_message('error', "Insert stokPdk gagal di row {$rowIndex}: " . json_encode($this->stokPdk->errors()));
                    continue; // lanjut ke baris berikut
                }

                $updateData = [
                    'mastermodel' => $nomodel,
                    'size' => $size,
                    'delivery' => $delivery2,
                    'qty_akhir' => $qty_akhir,
                ];
                $update = $this->ApsPerstyleModel->updateQtyStok($updateData);
                if ($update === false) {
                    log_message('error', "UpdateQtyStok gagal di row {$rowIndex}: " . json_encode($this->ApsPerstyleModel->errors()));
                    // jangan rollback langsung, tergantung policy — bisa catat dan lanjut
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                // rollback terjadi
                return redirect()->to(base_url(session()->get('role') . '/dataorder'))
                    ->with('error', 'Gagal mengimport data, transaksi dibatalkan.');
            }

            return redirect()->to(base_url(session()->get('role') . '/dataorder'))
                ->with('success', 'Data berhasil diimport.');
        } catch (\Throwable $e) {
            log_message('error', 'Exception saat importStokOrder: ' . $e->getMessage());
            return redirect()->to(base_url(session()->get('role') . '/dataorder'))
                ->with('error', 'Terjadi error saat memproses file: ' . $e->getMessage());
        }
    }
    public function sisaOrderAnomali()
    {
        $role = session()->get('role');
        $order = $this->ApsPerstyleModel->getSisaOrderAnomali();
        // dd($order);
        $data = [
            'role' => $role,
            'title' => 'Data Order',
            'active1' => '',
            'active2' => '',
            'active3' => 'active',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'order' => $order,
            'role' => $role
        ];
        return view($role . '/Order/sisaOrderAnomali', $data);
    }
}
