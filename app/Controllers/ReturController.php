<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\HTTP\CURLRequest;

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
use App\Models\BsModel;
use App\Models\BsMesinModel;
use App\Models\MesinPerStyle;
use App\Models\AreaModel;
use App\Services\orderServices;
use PhpOffice\PhpSpreadsheet\IOFactory;
use CodeIgniter\HTTP\RequestInterface;

class ReturController extends BaseController
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
    protected $MesinPerStyleModel;
    protected $orderServices;
    protected $bsModel;
    protected $BsMesinModel;
    protected $areaModel;

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
        $this->MesinPerStyleModel = new MesinPerStyle();
        $this->orderServices = new orderServices();
        $this->bsModel = new BsModel();
        $this->BsMesinModel = new BsMesinModel();
        $this->areaModel = new AreaModel();

        if ($this->filters   = ['role' => [session()->get('role') . ''], 'role' => ['user'], 'role' => ['planning']] != session()->get('role')) {
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
    public function index($area)
    {
        $areas = $this->areaModel->getArea();
        // Filter agar 'name' yang mengandung 'Gedung' tidak ikut
        $filteredArea = array_filter($areas, function ($item) {
            return stripos($item['name'], 'Gedung') === false; // Cek jika 'Gedung' tidak ada di 'name'
        });

        // Ambil hanya field 'name'
        $result = array_column($filteredArea, 'name');

        $url = 'http://172.23.44.14/MaterialSystem/public/api/getKategoriRetur';

        $response = file_get_contents($url);
        log_message('debug', "API Response: " . $response);
        if ($response === FALSE) {
            // log_message('error', "API tidak bisa diakses: $url");
            return $this->response->setJSON(["error" => "Gagal mengambil data dari API"]);
        }
        $data = json_decode($response, true);
        if ($data === null) {
            log_message('error', "Gagal mendecode data dari API: $url");
            // return $this->response->setJSON(["error" => "Gagal mengolah data dari API"]);
        }
        // Ambil data kategori retur
        $kategoriRetur = [];
        foreach ($data as $item) {
            $kategoriRetur[] = [
                'nama_kategori' => $item['nama_kategori'],
                'tipe_kategori' => $item['tipe_kategori']
            ];
        }
        $listRetur = 'http://172.23.44.14/MaterialSystem/public/api/listRetur/' . $area;
        $res = file_get_contents($listRetur);
        $list = json_decode($res, true);
        $data = [
            'role' => session()->get('role'),
            'title' => 'Retur Bahan Baku',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'active8' => '',
            'area' => $area,
            'areas' => $result,
            'kategori' => $kategoriRetur,
            'list' => $list
        ];
        return view(session()->get('role') . '/retur', $data);
    }

    public function dataRetur($area)
    {
        // Ambil nilai 'model' dari query parameter
        $noModel = $this->request->getGet('model') ?? '';


        $apiUrlPph = 'http://172.23.44.14/MaterialSystem/public/api/pph?model=' . urlencode($noModel);
        $apiUrlPengiriman = 'http://172.23.44.14/MaterialSystem/public/api/getPengirimanArea?noModel=' . urlencode($noModel);

        // Ambil data dari API PPH
        $responsePph = file_get_contents($apiUrlPph);
        if ($responsePph === FALSE) {
            // log_message('error', "API PPH tidak bisa diakses: $apiUrlPph");
            return $this->response->setJSON(["error" => "Gagal mengambil data dari API PPH"]);
        }

        $models = json_decode($responsePph, true);
        if ($models === null) {
            // log_message('error', "Gagal mendecode data PPH dari: $apiUrlPph");
            return $this->response->setJSON(["error" => "Gagal mengolah data dari API PPH"]);
        }
        // Ambil data dari API Pengiriman
        $responsePengiriman = file_get_contents($apiUrlPengiriman);
        // log_message('debug', "API Response Pengiriman: " . $responsePengiriman);
        $pengirimanData = json_decode($responsePengiriman, true);
        if ($pengirimanData === null) {
            // log_message('error', "Gagal mendecode data Pengiriman dari: $apiUrlPengiriman");
            $pengirimanData = [];
        }

        // Buat mapping data Pengiriman dengan key: no_model-kode_warna-item_type-area_out
        $pengirimanMap = [];
        foreach ($pengirimanData as $pengiriman) {
            // Key disesuaikan: menggunakan noModel dari parameter, kode_warna, item_type dan area_out
            $keyPengiriman = $noModel . '-' . $pengiriman['kode_warna'] . '-' . $pengiriman['item_type'] . '-' . $pengiriman['area_out'];
            $pengirimanMap[$keyPengiriman] = $pengiriman;
        }

        $pphInisial = [];
        foreach ($models as $items) {
            $styleSize = $items['style_size'];
            $gw = $items['gw'];
            $comp = $items['composition'];
            $loss = $items['loss'];

            // Dapatkan data produksi (sesuai fungsi model)
            $prod = $this->orderModel->getDataPph($area, $noModel, $styleSize);
            $prod = is_array($prod) ? $prod : ['qty' => 0];

            // Ambil data dari model APS dan BS Setting
            $idaps = $this->ApsPerstyleModel->getIdApsForPph($area, $noModel, $styleSize);
            $idapsList = array_column($idaps, 'idapsperstyle');
            if (!empty($idapsList)) {
                $bsSettingData = $this->bsModel->getBsPph($idapsList);
            } else {
                $bsSettingData = ['bs_setting' => 0];
            }
            $bsMesinData = $this->BsMesinModel->getBsMesinPph($area, $noModel, $styleSize);
            $bsMesin = $bsMesinData['bs_gram'] ?? 0;

            // Hitung bruto dan PPH
            $bruto = $prod['bruto'] ?? 0;
            if ($gw == 0) {
                $pph = 0;
            } else {
                $pph = ((($bruto + ($bsMesin / $gw)) * $comp * $gw) / 100) / 1000;
            }

            // Hitung total kebutuhan (ttl_kebutuhan) berdasarkan qty dan loss
            $ttl_kebutuhan = ($prod['qty'] * $comp * $gw / 100 / 1000) + ($loss / 100 * ($prod['qty'] * $comp * $gw / 100 / 1000));

            // Buat key untuk mencocokkan data pengiriman
            $keyPengiriman = $noModel . '-' . $items['kode_warna'] . '-' . $items['item_type'] . '-' . $items['area'];

            // Default field pengiriman
            $pengirimanDefaults = [
                'area_out'     => '-',
                'tgl_out'      => '-',
                'kgs_out'      => '-',
                'cns_out'      => '-',
                'krg_out'      => '-',
                'lot_out'      => '-',
                'nama_cluster' => '-',
                'status'       => '-',
                'admin'        => '-'
            ];

            // Cari data pengiriman yang match
            $pengirimanExtra = isset($pengirimanMap[$keyPengiriman])
                ? $pengirimanMap[$keyPengiriman]
                : $pengirimanDefaults;

            // Tambahkan semua field yang diperlukan ke dalam array pphInisial
            $pphInisial[] = [
                'no_model'     => $noModel,
                'area'         => $items['area'],
                'style_size'   => $items['style_size'],
                'inisial'      => $prod['inisial'] ?? null,
                'item_type'    => $items['item_type'],
                'kode_warna'   => $items['kode_warna'],
                'color'        => $items['color'],
                'gw'           => $items['gw'],
                'composition'  => $items['composition'],
                'ttl_kebutuhan' => $ttl_kebutuhan,  // tambahan untuk JS
                'jarum'        => $prod['machinetypeid'] ?? null,
                'bruto'        => $bruto,
                'qty'          => $prod['qty'] ?? 0,
                'sisa'         => $prod['sisa'] ?? 0,
                'po_plus'      => $prod['po_plus'] ?? 0,
                'bs_setting'   => $bsSettingData['bs_setting'] ?? 0,
                'bs_mesin'     => $bsMesin,
                'pph'          => $pph,
                // Field tambahan dari API Pengiriman
                'area_out'     => $pengirimanExtra['area_out'] ?? '0',
                'tgl_out'      => $pengirimanExtra['tgl_out'] ?? '-',
                'kgs_out'      => $pengirimanExtra['kgs_out'] ?? '0',  // KGS out dari API Pengiriman
                'cns_out'      => $pengirimanExtra['cns_out'] ?? '0',
                'krg_out'      => $pengirimanExtra['krg_out'] ?? '0',
                'lot_out'      => $pengirimanExtra['lot_out'] ?? '-',
                'nama_cluster' => $pengirimanExtra['nama_cluster'] ?? '-',
                'status'       => $pengirimanExtra['status'] ?? '-',
                'admin'        =>  '-'
            ];
        }

        // Proses akumulasi total data untuk key khusus
        $result = [
            'qty'       => 0,
            'sisa'      => 0,
            'bruto'     => 0,
            'bs_setting' => 0,
            'bs_mesin'  => 0
        ];
        $processedStyleSizes = [];
        $temporaryData = [];
        foreach ($pphInisial as $item) {
            $key = $item['item_type'] . '-' . $item['kode_warna'];
            $styleSizeKey = $item['style_size'];

            if (!isset($processedStyleSizes[$styleSizeKey])) {
                $temporaryData[] = [
                    'qty'       => $item['qty'],
                    'sisa'      => $item['sisa'],
                    'bruto'     => $item['bruto'],
                    'bs_setting' => $item['bs_setting'],
                    'bs_mesin'  => $item['bs_mesin']
                ];
                $processedStyleSizes[$styleSizeKey] = true;
            }

            if (!isset($result[$key])) {
                $result[$key] = [
                    'no_model'  => $item['no_model'],
                    'area'      => $item['area'],
                    'item_type' => $item['item_type'],
                    'kode_warna' => $item['kode_warna'],
                    'warna'     => $item['color'],
                    'ttl_kebutuhan' => 0,
                    'pph'       => 0,
                    'jarum'     => $item['jarum'],
                    'kgs_out' => $item['kgs_out'],
                    'cns_out' => $item['cns_out'],
                    'krg_out' => $item['krg_out'],
                    'lot_out' => $item['lot_out'],
                    'nama_cluster' => $item['nama_cluster']
                ];
            }
            // Akumulasi data berdasarkan item_type-kode_warna
            $result[$key]['ttl_kebutuhan'] += $item['ttl_kebutuhan'];
            $result[$key]['pph'] += $item['pph'];
        }

        foreach ($temporaryData as $res) {
            $result['qty']        += $res['qty'];
            $result['sisa']       += $res['sisa'];
            $result['bruto']      += $res['bruto'];
            $result['bs_setting'] += $res['bs_setting'];
            $result['bs_mesin']   += $res['bs_mesin'];
        }

        log_message('debug', "Final Result: " . json_encode($result));

        return $this->response->setJSON($result);
    }

    public function pengajuanRetur($area)
    {
        $postData = $this->request->getPost();

        if (!$postData) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Data tidak ditemukan.'
            ]);
        }

        // Ambil array kgs/cones/karung
        $kgsArr   = isset($postData['kgs']) ? (array)$postData['kgs'] : [];
        $conesArr = isset($postData['cones']) ? (array)$postData['cones'] : [];
        $krgArr   = isset($postData['karung']) ? (array)$postData['karung'] : [];

        // count berdasarkan jumlah karung
        $count = count($krgArr);

        if ($count === 0) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Tidak ada data karung untuk diproses.'
            ]);
        }

        /** @var CURLRequest $client */
        $client = \Config\Services::curlrequest();

        // Ambil data material sekali
        try {
            $materialUrl = 'http://172.23.44.14/MaterialSystem/public/api/cekMaterial/' . $postData['material'];
            $materialResponse = $client->get($materialUrl, ['headers' => ['Accept' => 'application/json']]);
            $materialData = json_decode($materialResponse->getBody(), true);
            if (!$materialData || !isset($materialData['item_type'])) {
                throw new \Exception('Data material tidak ditemukan.');
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal mengambil data material.'
            ]);
        }

        $errors = [];
        $success = 0;

        for ($i = 0; $i < $count; $i++) {
            // Ambil nilai per index, fallback ke 0 atau empty jika tidak ada
            $kgsVal   = isset($kgsArr[$i]) ? (float)$kgsArr[$i] : 0;
            $conesVal = isset($conesArr[$i]) ? (int)$conesArr[$i] : 0;
            $krgVal   = $krgArr[$i] ?? null; // ini adalah identifier/nomor karung

            if ($krgVal === null) {
                $errors[] = "Index $i: no karung kosong, dilewatkan.";
                continue;
            }

            $payload = [
                'no_model'        =>  isset($postData['model']) ? strtoupper($postData['model']) : '',
                'area_retur'      => $postData['area'] ?? $area,
                'item_type'       => $materialData['item_type'],
                'kode_warna'      => $materialData['kode_warna'] ?? null,
                'warna'           => $materialData['color'] ?? null,
                'tgl_retur'       => date('Y-m-d'),
                'kgs_retur'       => $kgsVal,
                'cns_retur'       => $conesVal,
                'krg_retur'       => $krgVal,
                'lot_retur'       => $postData['lotRetur'] ?? null,
                'kategori'        => $postData['kategori_retur'] ?? null,
                'keterangan_area' => $postData['keterangan'] ?? ''
            ];

            try {
                $resp = $client->post('http://172.23.44.14/MaterialSystem/public/api/saveRetur', [
                    'headers' => [
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json'
                    ],
                    'json' => $payload
                ]);
                $code = $resp->getStatusCode();
                if (in_array($code, [200, 201])) {
                    $success++;
                } else {
                    $body = json_decode($resp->getBody(), true);
                    $errors[] = "Index $i: API kembalikan kode $code - " . json_encode($body);
                }
            } catch (\Exception $e) {
                log_message('error', "Error kirim item index $i: " . $e->getMessage());
                $errors[] = "Index $i: " . $e->getMessage();
            }
        }

        if ($success > 0 && empty($errors)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => "Berhasil mengirim $success item retur."
            ]);
        } elseif ($success > 0) {
            return $this->response->setJSON([
                'status' => 'partial',
                'message' => "Berhasil: $success item. Namun ada error pada beberapa item.",
                'errors' => $errors
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal mengirim semua item retur.',
                'errors' => $errors
            ]);
        }
    }

    public function getKodeWarnaWarnaByItemType()
    {
        $itemType = $this->request->getGet('item_type');

        // Ambil data berdasarkan item_type dari database
        $model = new \App\Models\ReturModel(); // Ganti sesuai model kamu
        $result = $model->where('item_type', $itemType)->groupBy(['kode_warna', 'warna'])->findAll();

        return $this->response->setJSON($result);
    }

    public function listdataReturArea()
    {
        $area = $this->request->getGet('area') ?? '';

        $areas = $this->areaModel->getArea();
        // Filter agar 'name' yang mengandung 'Gedung' tidak ikut
        $filteredArea = array_filter($areas, function ($item) {
            return stripos($item['name'], 'Gedung') === false; // Cek jika 'Gedung' tidak ada di 'name'
        });

        // Ambil hanya field 'name'
        $result = array_column($filteredArea, 'name');

        $list = [];

        // Kalau area dipilih, baru ambil data listRetur
        if (!empty($area)) {
            $listRetur = 'http://172.23.44.14/MaterialSystem/public/api/listRetur/' . $area;
            $res = file_get_contents($listRetur);
            if ($res !== false) {
                $list = json_decode($res, true);
            }
        }
        $data = [
            'role' => session()->get('role'),
            'title' => 'Retur Bahan Baku',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'active8' => '',
            'area' => $area,
            'areas' => $result,
            'list' => $list
        ];
        return view(session()->get('role') . '/retur', $data);
    }

    public function listRetur($area)
    {
        $noModel = $this->request->getGet('noModel');
        $tglBuat = $this->request->getGet('tglBuat');

        $listRetur = 'http://172.23.44.14/MaterialSystem/public/api/listRetur/' . $area . '?noModel=' . $noModel . '&tglBuat=' . $tglBuat;
        $res = file_get_contents($listRetur);
        $list = json_decode($res, true);
        // Kalau request-nya AJAX, _return_ JSON langsung
        if ($this->request->isAJAX()) {
            return $this->response->setJSON($list);
        }

        $data = [
            'role' => session()->get('role'),
            'title' => 'Retur Bahan Baku',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'active8' => '',
            'area' => $area,
            'list' => $list
        ];
        return view(session()->get('role') . '/listRetur', $data);
    }
}
