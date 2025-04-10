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
use App\Models\BsModel;
use App\Models\BsMesinModel;
use App\Models\MesinPerStyle;
use App\Services\orderServices;
use PhpOffice\PhpSpreadsheet\IOFactory;
use CodeIgniter\HTTP\RequestInterface;


class MaterialController extends BaseController
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
        $area = session()->get('username');
        $role = session()->get('role');
        $logged_in = true;
        $noModel = $this->DetailPlanningModel->getNoModelAktif($area);
        $pemesananBb = session()->get('pemesananBb');
        // Kita "flatten" data sehingga tiap baris tersimpan sebagai record tunggal
        $flattenData = [];

        if (!empty($pemesananBb)) {
            foreach ($pemesananBb as $group) {
                foreach ($group as $rowKey => $row) {
                    $flattenData[] = [
                        'tgl_pakai'      => $row['tgl_pakai'] ?? '',
                        'no_model'       => $row['no_model'] ?? '',
                        'style_size'     => $row['style_size'] ?? '',
                        'item_type'      => $row['item_type'] ?? '',
                        'kode_warna'     => $row['kode_warna'] ?? '',
                        'warna'          => $row['warna'] ?? '',
                        'jalan_mc'       => $row['jalan_mc'] ?? '',
                        'ttl_cns'        => $row['ttl_cns'] ?? '',
                        'ttl_berat_cns'  => $row['ttl_berat_cns'] ?? '',
                        'id_material'    => $row['id_material'] ?? '',
                    ];
                }
            }
        }
        // Lakukan sorting berdasarkan urutan kolom: tgl_pakai, no_model, style_size, item_type, kode_warna, dan warna
        usort($flattenData, function ($a, $b) {
            // Urutan field yang ingin dijadikan acuan sorting
            $order = ['tgl_pakai', 'no_model', 'item_type', 'kode_warna', 'warna', 'style_size',];
            foreach ($order as $field) {
                // Karena tgl_pakai dalam format YYYY-MM-DD bisa dibandingkan secara string
                $cmp = strcmp($a[$field], $b[$field]);
                if ($cmp !== 0) {
                    return $cmp;
                }
            }
            return 0;
        });
        $groupedData = [];
        foreach ($flattenData as $data) {
            // Gunakan separator untuk membentuk key unik
            $groupKey = $data['tgl_pakai'] . '|' . $data['no_model'] . '|' . $data['item_type'] . '|' . $data['kode_warna'] . '|' . $data['warna'];
            $groupedData[$groupKey][] = $data;
        }

        $data = [
            'role' => session()->get('role'),
            'area' => $area,
            'role' => $role,
            'title' => 'Bahan Baku',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'targetProd' => 0,
            'produksiBulan' => 0,
            'produksiHari' => 0,
            'noModel' => $noModel,
            'groupedData' => $groupedData

        ];

        return view(session()->get('role') . '/Material/index', $data);
    }
    public function statusbahanbaku($area)
    {
        // Ambil nilai search dari query string
        $apiUrl = 'http://172.23.44.14/MaterialSystem/public/api/statusbahanbaku/' . $area;
        // dd($search);

        // Ambil data dari API
        $response = file_get_contents($apiUrl);
        $status = json_decode($response, true);

        // dd($status);
        $data = [
            'role' => session()->get('role'),
            'title' => 'Status Bahan Baku',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'targetProd' => 0,
            'produksiBulan' => 0,
            'produksiHari' => 0,
            'material' => $status,
            'area' => $area

        ];

        return view(session()->get('role') . '/Material/statusbahanbaku', $data);
    }
    public function filterstatusbahanbaku($area)
    {
        // Mengambil nilai 'search' yang dikirim oleh frontend
        $search = $this->request->getGet('search');

        // Jika search ada, panggil API eksternal dengan query parameter 'search'
        $apiUrl = 'http://172.23.44.14/MaterialSystem/public/api/statusbahanbaku/' . $area . '?search=' . urlencode($search);

        // Mengambil data dari API eksternal
        $response = file_get_contents($apiUrl);
        $status = json_decode($response, true);

        // Filter data berdasarkan 'no_model' jika ada keyword 'search'
        if ($search) {
            $status = array_filter($status, function ($item) use ($search) {
                // Cek apakah pencarian ada di no_model terlebih dahulu
                if (isset($item['no_model']) && strpos(strtolower($item['no_model']), strtolower($search)) !== false) {
                    return true;
                }
                // Lanjutkan pencarian ke kode_warna, lot_celup, dan tanggal_schedule jika no_model tidak cocok
                if (isset($item['kode_warna']) && strpos(strtolower($item['kode_warna']), strtolower($search)) !== false) {
                    return true;
                }
                if (isset($item['lot_celup']) && strpos(strtolower($item['lot_celup']), strtolower($search)) !== false) {
                    return true;
                }
                if (isset($item['tanggal_schedule']) && strpos(strtolower($item['tanggal_schedule']), strtolower($search)) !== false) {
                    return true;
                }
                return false;
            });
        }

        // Kembalikan data yang sudah difilter ke frontend
        return $this->response->setJSON($status);
    }

    public function cekBahanBaku($id, $idpln)
    {
        $model = $this->DetailPlanningModel->detailPdk($id);
        $apiUrl = 'http://172.23.44.14/MaterialSystem/public/api/cekBahanBaku/' . $model['model'];
        // Ambil data dari API
        $response = file_get_contents($apiUrl);
        $status = json_decode($response, true);
        // dd($status);
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
            'material' => $status,
            'role' => session()->get('role'),
            'idDetail' => $id,
            'idPln' => $idpln,
            'model' => $model['model']
        ];

        return view(session()->get('role') . '/Material/cekBahanBaku', $data);
    }
    public function cekStok()
    {
        $model = $this->request->getGet('model');
        $apiUrl = 'http://172.23.44.14/MaterialSystem/public/api/cekStok/' . $model;
        $response = file_get_contents($apiUrl);
        $stok = json_decode($response, true);

        return $this->response->setJSON($stok);
    }
    public function getStyleSizeByNoModel()
    {
        // Ambil No Model dari permintaan AJAX
        $noModel = $this->request->getPost('no_model');
        // Query data style size berdasarkan No Model
        $styleSize = $this->ApsPerstyleModel->getStyleSize($noModel); // Sesuaikan dengan model Anda
        // var_dump($noModel);

        // Kembalikan data dalam format JSON
        return $this->response->setJSON($styleSize);
    }
    public function getJalanMcByModelSize()
    {
        // Ambil No Model dan Style Size dari permintaan AJAX
        $noModel = $this->request->getPost('no_model');
        $styleSize = $this->request->getPost('style_size');

        // Query data Jalan MC berdasarkan No Model dan Style Size
        $jalanMc = $this->MesinPerStyleModel->getJalanMc($noModel, $styleSize); // Sesuaikan dengan model Anda

        // Kembalikan data dalam format JSON
        return $this->response->setJSON($jalanMc);
    }
    public function getMU($model, $styleSize, $area)
    {
        $styleSize = urlencode($styleSize);  // Encode styleSize
        $apiUrl = 'http://172.23.44.14/MaterialSystem/public/api/getMU/' . $model . '/' . $styleSize . '/' . $area;
        $response = file_get_contents($apiUrl);  // Mendapatkan response dari API
        if ($response === FALSE) {
            die('Error occurred while fetching data.');
        }

        $data = json_decode($response, true);  // Decode JSON response dari API


        return $this->response->setJSON($data);
    }
    public function savePemesananSession()
    {
        $existingData = session()->get('pemesananBb') ?? [];

        // Ambil data baru dari request POST dengan key 'items'
        $newData = $this->request->getPost('items');

        if (!is_array($newData)) {
            return; // Pastikan $newData adalah array sebelum diproses
        }

        // Variabel untuk menyimpan data valid
        $validData = [];

        // Loop melalui data baru
        foreach ($newData as $group) {
            if (!is_array($group)) {
                continue; // Pastikan $group adalah array sebelum diproses
            }

            foreach ($group as $record) {
                $isDuplicate = false;

                // Cek ke existingData untuk duplikasi
                foreach ($existingData as $existingGroup) {
                    foreach ($existingGroup as $existingRecord) {
                        if (
                            isset($record['id_material'], $record['tgl_pakai'], $existingRecord['id_material'], $existingRecord['tgl_pakai']) &&
                            $record['id_material'] === $existingRecord['id_material'] &&
                            $record['tgl_pakai'] === $existingRecord['tgl_pakai']
                        ) {
                            // Tandai data sebagai duplikat
                            $isDuplicate = true;

                            // Log pesan error atau berikan respon status warning
                            log_message('error', 'Duplikasi ditemukan: ' . json_encode($record));
                            break 2; // Keluar dari loop jika duplikat ditemukan
                        }
                    }
                }

                // Jika tidak ada duplikasi, tambahkan ke data valid
                if (!$isDuplicate) {
                    $validData[] = $record;
                }
            }
        }

        // Update session dengan data valid baru
        if (!empty($validData)) {
            session()->set('pemesananBb', array_merge($existingData, [$validData]));
        } else {
            // Tampilkan respon warning
            return $this->response->setJSON([
                'status' => 'warning',
                'message' => 'Beberapa data tidak disimpan karena duplikasi ditemukan.'
            ]);
        }

        return $this->response->setJSON([
            'message' => 'Data berhasil diupdate & disimpan ke session',
            'data'    => $existingData,
            'status'  => 'success',
            'title'  => 'Sukses!',

        ]);
    }
    public function deletePemesananSession($id_material, $tgl_pakai)
    {
        // Ambil data session yang asli (data flattened)
        $pemesananBb = session()->get('pemesananBb') ?? [];
        $found = false; // Variabel untuk melacak apakah data ditemukan
        // dd($id_material);
        // Loop melalui data untuk menemukan dan menghapus elemen
        foreach ($pemesananBb as $groupKey => $group) {
            foreach ($group as $itemKey => $item) {

                // Cek apakah id_material cocok
                if ($item['id_material'] === $id_material && $item['tgl_pakai'] === $tgl_pakai) {
                    unset($pemesananBb[$groupKey][$itemKey]); // Hapus elemen
                    $pemesananBb[$groupKey] = array_values($pemesananBb[$groupKey]); // Rapi indeks
                    $found = true;
                    break 2; // Hentikan loop setelah menemukan dan menghapus
                }
            }
        }

        // Perbarui session jika ada perubahan
        if ($found) {
            session()->set('pemesananBb', $pemesananBb); // Gunakan set() untuk menyimpan data ke session
            return redirect()->back()->with('success', 'Data berhasil dihapus');
        }

        // Jika data tidak ditemukan
        return redirect()->back()->with('error', 'Data tidak ditemukan');
    }
    public function deleteAllPemesananSession()
    {
        // Menghapus data session 'pemesananBb'
        session()->remove('pemesananBb');

        // Redirect dengan pesan sukses
        return redirect()->back()->with('success', 'Data berhasil dihapus dari session');
    }
    public function listPemesanan($area)
    {
        $apiUrl = 'http://172.23.44.14/MaterialSystem/public/api/listPemesanan/' . $area;
        $response = file_get_contents($apiUrl);  // Mendapatkan response dari API
        if ($response === FALSE) {
            die('Error occurred while fetching data.');
        }
        $dataList = json_decode($response, true);  // Decode JSON response dari API

        // ambil data libur hari kedepan untuk menentukan jadwal pemesanan
        $today = date('Y-m-d'); // ambil data hari ini
        $dataLibur = $this->liburModel->getDataLiburForPemesanan($today);
        // Ambil data tanggal libur menjadi array sederhana
        $liburDates = array_column($dataLibur, 'tanggal'); // Ambil hanya kolom 'tanggal'

        $day = date('l'); // ambil data hari ini
        function getNextNonHoliday($date, $liburDates)
        {
            while (in_array($date, $liburDates)) {
                // Jika tanggal ada di daftar libur, tambahkan 1 hari
                $date = date('Y-m-d', strtotime($date . ' +1 day'));
            }
            return $date;
        }

        $initialTomorrow = date('Y-m-d', strtotime('+1 day')); // Mulai dari hari besok
        $tomorrow = getNextNonHoliday($initialTomorrow, $liburDates); // Dapatkan tanggal "tomorrow" yang valid (bukan libur)

        // Untuk tanggal berikutnya, kita ambil 1 hari setelah tanggal "tomorrow" dan cek ulang
        $initialTwoDays = date('Y-m-d', strtotime($tomorrow . ' +1 day'));
        $twoDays = getNextNonHoliday($initialTwoDays, $liburDates);

        // Untuk tanggal ketiga, ambil 1 hari setelah tanggal "twoDays" dan cek ulang
        $initialthreeDay = date('Y-m-d', strtotime($twoDays . ' +1 day'));
        $threeDay = getNextNonHoliday($initialthreeDay, $liburDates);

        $data = [
            'role' => session()->get('role'),
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => 'active',
            'active7' => '',
            'area' => $area,
            'title' => "List Pemesanan",
            'dataList' => $dataList,
            'day' => $day,
            'tomorrow' => $tomorrow,
            'twoDays' => $twoDays,
            'threeDay' => $threeDay,
        ];

        return view(session()->get('role') . '/Material/listPemesanan', $data);
    }
    public function stockBahanBaku($area)
    {
        $data = [
            'role' => session()->get('role'),
            'title' => 'Stock Bahan Baku',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'targetProd' => 0,
            'produksiBulan' => 0,
            'produksiHari' => 0,
            'area' => $area
        ];

        return view(session()->get('role') . '/Material/stockbahanbaku', $data);
    }
    public function filterStockBahanBaku($area)
    {
        // Mengambil nilai 'search' yang dikirim oleh frontend
        $noModel = $this->request->getGet('noModel');
        $warna = $this->request->getGet('warna');

        // Jika search ada, panggil API eksternal dengan query parameter 'search'
        $apiUrl = 'http://172.23.44.14/MaterialSystem/public/api/stockbahanbaku/' . $area . '?noModel=' . urlencode($noModel) . '&warna=' . urlencode($warna);

        // Mengambil data dari API eksternal
        $response = file_get_contents($apiUrl);
        $stock = json_decode($response, true);

        // Kembalikan data yang sudah difilter ke frontend
        return $this->response->setJSON($stock);
    }
    public function pph($area)
    {
        $data = [
            'role' => session()->get('role'),
            'title' => 'PPH',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'targetProd' => 0,
            'produksiBulan' => 0,
            'produksiHari' => 0,
            'area' => $area
        ];

        return view(session()->get('role') . '/Material/pph', $data);
    }
    public function filterPPH($area)
    {
        // Mengambil nilai 'search' yang dikirim oleh frontend
        $noModel = $this->request->getGet('model') ?? '';

        // Jika search ada, panggil API eksternal dengan query parameter 'search'
        $apiUrl = 'http://172.23.44.14/MaterialSystem/public/api/pph?model=' . urlencode($noModel);

        // Mengambil data dari API eksternal
        $response = file_get_contents($apiUrl);
        if ($response === FALSE) {
            log_message('error', "API tidak bisa diakses: $apiUrl");
            return $this->response->setJSON(["error" => "Gagal mengambil data dari API"]);
        } else {
            $models = json_decode($response, true);

            $pphInisial = [];
            foreach ($models as $items) {
                $styleSize = $items['style_size'];
                $gw = $items['gw'];
                $comp = $items['composition'];
                $loss = $items['loss'];
                $gwpcs = ($gw * $comp) / 100;
                $prod = $this->orderModel->getDataPph($area, $noModel, $styleSize);
                $prod = is_array($prod) ? $prod : [];
                $idaps = $this->ApsPerstyleModel->getIdApsForPph($area, $noModel, $styleSize);
                $idapsList = array_column($idaps, 'idapsperstyle');
                if (!empty($idapsList)) {
                    $bsSettingData = $this->bsModel->getBsPph($idapsList);
                } else {
                    $bsSettingData = ['bs_setting' => 0]; // default kalau data kosong
                }
                $bsMesinData = $this->BsMesinModel->getBsMesinPph($area, $noModel, $styleSize);
                $bsMesin = $bsMesinData['bs_gram'] ?? 0;
                $bruto = $prod['bruto'] ?? 0;
                if ($gw == 0) {
                    $pph = 0;
                } else {

                    $pph = ((($bruto + ($bsMesin / $gw)) * $comp * $gw) / 100) / 1000;
                }

                $ttl_kebutuhan = ($prod['qty'] * $comp * $gw / 100 / 1000) + ($loss / 100 * ($prod['qty'] * $comp * $gw / 100 / 1000));

                $pphInisial[] = [
                    'area'  => $items['area'],
                    'style_size'  => $items['style_size'],
                    'inisial'  => $prod['inisial'],
                    'item_type'  => $items['item_type'],
                    'kode_warna'      => $items['kode_warna'],
                    'color'      => $items['color'],
                    'gw'         => $items['gw'],
                    'composition' => $items['composition'],
                    'kgs'  => $ttl_kebutuhan,
                    'jarum'      => $prod['machinetypeid'] ?? null,
                    'bruto'      => $bruto,
                    'qty'        => $prod['qty'] ?? 0,
                    'sisa'       => $prod['sisa'] ?? 0,
                    'po_plus'    => $prod['po_plus'] ?? 0,
                    'bs_setting' => $bsSettingData['bs_setting'] ?? 0,
                    'bs_mesin'   => $bsMesin,
                    'pph'        => $pph
                ];
            }
        }
        $result = [
            'qty' => 0,
            'sisa' => 0,
            'bruto' => 0,
            'bs_setting' => 0,
            'bs_mesin' => 0
        ];

        $processedStyleSizes = []; // Untuk memastikan style_size tidak dihitung lebih dari sekali
        $temporaryData = [];
        foreach ($pphInisial as $item) {
            $key = $item['item_type'] . '-' . $item['kode_warna'];
            $styleSizeKey = $item['style_size'];

            // Jika style_size sudah ada, jangan tambahkan lagi
            if (!isset($processedStyleSizes[$styleSizeKey])) {
                $temporaryData[] = [
                    'qty' => $item['qty'],
                    'sisa' => $item['sisa'],
                    'bruto' => $item['bruto'],
                    'bs_setting' => $item['bs_setting'],
                    'bs_mesin' => $item['bs_mesin']
                ];
                $processedStyleSizes[$styleSizeKey] = true;
            }

            if (!isset($result[$key])) {
                $result[$key] = [
                    'item_type' => $item['item_type'],
                    'kode_warna' => $item['kode_warna'],
                    'warna' => $item['color'],
                    'kgs' => 0,
                    'pph' => 0,
                    'jarum' => $item['jarum'],
                    'area' => $item['area']
                ];
            }

            // Akumulasi data berdasarkan item_type-kode_warna
            $result[$key]['kgs'] += $item['kgs'];
            $result[$key]['pph'] += $item['pph'];
        }

        // Menambahkan total dari style_size yang unik ke dalam result
        foreach ($temporaryData as $res) {
            $result['qty'] += $res['qty'];
            $result['sisa'] += $res['sisa'];
            $result['bruto'] += $res['bruto'];
            $result['bs_setting'] += $res['bs_setting'];
            $result['bs_mesin'] += $res['bs_mesin'];
        }

        // Hapus semua elemen dengan format style_size dari $result
        foreach (array_keys($result) as $key) {
            if (preg_match('/^\w+\s*\d+[Xx]\d+$/', $key)) {
                unset($result[$key]);
            }
        }
        log_message('debug', "Final Result: " . json_encode($result));

        // Kembalikan data yang sudah difilter ke frontend
        return $this->response->setJSON($result);
    }
    public function tampilPerStyle($area)
    {
        $role = session()->get('role');
        return view($role . '/Material/pphPerStyle', [
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'title' => 'PPH',
            'role' => $role,
            'area' => $area,
            'dataPph' => []
        ]);
    }
    public function pphinisial($area)
    {
        // Mengambil nilai 'search' yang dikirim oleh frontend
        $noModel = $this->request->getGet('model') ?? '';

        // Jika search ada, panggil API eksternal dengan query parameter 'search'
        $apiUrl = 'http://172.23.44.14/MaterialSystem/public/api/pph?model=' . urlencode($noModel);

        // Mengambil data dari API eksternal
        $response = file_get_contents($apiUrl);
        if ($response === FALSE) {
            log_message('error', "API tidak bisa diakses: $apiUrl");
            return $this->response->setJSON(["error" => "Gagal mengambil data dari API"]);
        } else {
            $models = json_decode($response, true);

            $pphInisial = [];
            foreach ($models as $items) {
                $styleSize = $items['style_size'];
                $gw = $items['gw'];
                $comp = $items['composition'];
                $loss = $items['loss'];
                $gwpcs = ($gw * $comp) / 100;
                $prod = $this->orderModel->getDataPph($area, $noModel, $styleSize);
                $prod = is_array($prod) ? $prod : [];
                $idaps = $this->ApsPerstyleModel->getIdApsForPph($area, $noModel, $styleSize);
                $idapsList = array_column($idaps, 'idapsperstyle');
                if (!empty($idapsList)) {
                    $bsSettingData = $this->bsModel->getBsPph($idapsList);
                } else {
                    $bsSettingData = ['bs_setting' => 0]; // default kalau data kosong
                }
                $bsMesinData = $this->BsMesinModel->getBsMesinPph($area, $noModel, $styleSize);
                $bsMesin = $bsMesinData['bs_gram'] ?? 0;
                $bruto = $prod['bruto'] ?? 0;
                if ($gw == 0) {
                    $pph = 0;
                } else {
                    $pph = ((($bruto + ($bsMesin / $gw)) * $comp * $gw) / 100) / 1000;
                }
                $ttl_kebutuhan = ($prod['qty'] * $comp * $gw / 100 / 1000) + ($loss / 100 * ($prod['qty'] * $comp * $gw / 100 / 1000));

                $pphInisial[] = [
                    'area'  => $items['area'],
                    'style_size'  => $items['style_size'],
                    'inisial'  => $prod['inisial'],
                    'item_type'  => $items['item_type'],
                    'kode_warna'      => $items['kode_warna'],
                    'color'      => $items['color'],
                    'gw'         => $items['gw'],
                    'loss'         => $items['loss'],
                    'composition' => $items['composition'],
                    'kgs'  => $ttl_kebutuhan,
                    'jarum'      => $prod['machinetypeid'] ?? null,
                    'bruto'      => $bruto,
                    'netto'      => $bruto - $bsSettingData['bs_setting'] ?? 0,
                    'qty'        => $prod['qty'] ?? 0,
                    'sisa'       => $prod['sisa'] ?? 0,
                    'po_plus'    => $prod['po_plus'] ?? 0,
                    'bs_setting' => $bsSettingData['bs_setting'] ?? 0,
                    'bs_mesin'   => $bsMesin,
                    'pph'        => $pph,
                    'pph_persen' => ($ttl_kebutuhan != 0) ? ($pph / $ttl_kebutuhan) * 100 : 0,
                ];
            }
        }

        $dataToSort = array_filter($pphInisial, 'is_array');

        usort($dataToSort, function ($a, $b) {
            return $a['inisial'] <=> $b['inisial']
                ?: $a['item_type'] <=> $b['item_type']
                ?: $a['kode_warna'] <=> $b['kode_warna'];
        });

        return $this->response->setJSON($dataToSort);
    }
    public function pphPerhari($area)
    {
        $role = session()->get('role');
        return view($role . '/Material/pphPerDays', [
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'title'      => 'PPH',
            'role'       => $role,
            'area'       => $area,
            'mergedData' => [] // Tidak ada data sampai search diisi
        ]);
    }
    public function getDataPerhari($area)
    {
        $tanggal = $this->request->getGet('tanggal');
        // $tanggal = '2025-03-10';
        $data = $this->produksiModel->getProduksiPerStyle($area, $tanggal);
        if (!empty($data)) {
            // Extract all mastermodel and size values for batch query
            $mastermodels = array_column($data, 'mastermodel');
            $sizes = array_column($data, 'size');

            // Fetch all bs_mesin data in one query
            $bsMesinData = $this->BsMesinModel->getBsMesinHarian($mastermodels, $sizes, $tanggal);

            // Create a lookup table for fast matching
            $bsMesinMap = [];
            foreach ($bsMesinData as $bs) {
                $key = $bs['no_model'] . '_' . $bs['size'];
                $bsMesinMap[$key] = $bs['bs_mesin'];
            }

            // Assign bs_mesin to production data
            foreach ($data as $prod) {
                $key = $prod['mastermodel'] . '_' . $prod['size'];
                $prod['bs_mesin'] = $bsMesinMap[$key] ?? 0; // Default to null if not found
            }
        }

        $result = [];
        $pphInisial = [];

        foreach ($data as $prod) {
            $key = $prod['mastermodel'] . '-' . $prod['size'];
            // $mastermodelStr = implode(',', $mastermodels);
            // $sizeStr = implode(',', $sizes);

            $apiUrl = 'http://172.23.44.14/MaterialSystem/public/api/pphperhari?model=' . urlencode($prod['mastermodel']) . '&size=' . urlencode($prod['size']);

            // Mengambil data dari API eksternal
            $response = @file_get_contents($apiUrl);
            log_message('debug', 'Response dari API: ' . $response);
            if ($response === FALSE) {
                log_message('error', "API tidak bisa diakses: $apiUrl");
                log_message('debug', 'URL yang dikirim ke API: ' . $apiUrl);
                return $this->response->setJSON(["error" => "Gagal mengambil data dari API"]);
            } else {
                log_message('debug', 'Response dari API: ' . $response);
                $material = json_decode($response, true);
            }
            // $material = $this->materialModel->getMU($prod['mastermodel'], $prod['size']);

            if (empty($material)) {
                $result[$prod['mastermodel']] = [
                    'mastermodel' => $prod['mastermodel'],
                    'item_type' => null,
                    'kode_warna' => null,
                    'warna' => null,
                    'pph' => 0,
                    'bruto' => $prod['prod'],
                    'bs_mesin' => $prod['bs_mesin'] ?? 0,
                ];
            } else {
                foreach ($material as $mtr) {
                    $gw = $mtr['gw'];
                    $comp = $mtr['composition'];
                    $gwpcs = ($gw * $comp) / 100;

                    $bruto = $prod['prod'] ?? 0;
                    $bs_mesin = $prod['bs_mesin'] ?? 0;
                    if ($gw == 0) {
                        $pph = 0;
                    } else {

                        $pph = ((($bruto + ($bs_mesin / $gw)) * $comp * $gw) / 100) / 1000;
                    }

                    $pphInisial[] = [
                        'mastermodel'    => $prod['mastermodel'],
                        'style_size'  => $prod['size'],
                        'item_type'   => $mtr['item_type'] ?? null,
                        'kode_warna'  => $mtr['kode_warna'] ?? null,
                        'color'       => $mtr['color'] ?? null,
                        'gw'          => $gw,
                        'composition' => $comp,
                        'bruto'       => $bruto,
                        'qty'         => $prod['qty'] ?? 0,
                        'sisa'        => $prod['sisa'] ?? 0,
                        'bs_mesin'    => $bs_mesin,
                        'pph'         => $pph
                    ];
                }
            }
        }

        // Grouping & Summing Data
        foreach ($pphInisial as $item) {
            $key = $item['mastermodel'] . '-' . $item['item_type'] . '-' . $item['kode_warna'];

            if (!isset($result[$key])) {
                $result[$key] = [
                    'mastermodel' => $item['mastermodel'],
                    'item_type'   => $item['item_type'],
                    'kode_warna'  => $item['kode_warna'],
                    'warna'       => $item['color'],
                    'pph'         => 0,
                    'bruto'       => 0,
                    'bs_mesin'    => 0,
                ];
            }

            // Accumulate values correctly

            $result[$key]['bruto'] += $item['bruto'];
            $result[$key]['bs_mesin'] += $item['bs_mesin'];
            $result[$key]['pph'] += $item['pph'];
        }
        return $this->response->setJSON($result);
    }
}
