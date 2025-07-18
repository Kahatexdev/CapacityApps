<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Database\Migrations\BsMesin;
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
use App\Models\AreaModel;
use App\Models\MesinPerStyle;
use App\Services\orderServices;
use PhpOffice\PhpSpreadsheet\IOFactory;
use CodeIgniter\HTTP\RequestInterface;
use DateTime;


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
        $logged_in = true;
        // $noModel = $this->DetailPlanningModel->getNoModelAktif($area);
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
                        'po_tambahan'    => $row['po_tambahan'] ?? 0,
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
            'title' => 'Bahan Baku',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            // 'noModel' => $noModel,
            'groupedData' => $groupedData

        ];

        return view(session()->get('role') . '/Material/index', $data);
    }
    public function statusbahanbaku()
    {
        // Ambil nilai search dari query string


        // dd($status);
        $data = [
            'role' => session()->get('role'),
            'title' => 'Status Bahan Baku',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'targetProd' => 0,
        ];

        return view(session()->get('role') . '/Material/statusbahanbaku', $data);
    }
    public function filterstatusbahanbaku($model)
    {
        // Mengambil data master
        $master = $this->orderModel->getStartMc($model);

        // Mengambil nilai 'search' yang dikirim oleh frontend
        $search = $this->request->getGet('search');
        // Jika search ada, panggil API eksternal dengan query parameter 'search'
        $apiUrl = 'http://172.23.44.14/MaterialSystem/public/api/statusbahanbaku/' . $model . '?search=' . urlencode($search);

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
        // Gabungkan data master dan status dalam satu array
        $responseData = [
            'master' => $master, // Data master dari getStartMc
            'status' => $status // Data status yang sudah difilter (gunakan array_values untuk mereset indeks array)
        ];

        // Kembalikan data yang sudah difilter ke frontend
        return $this->response->setJSON($responseData);
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
    public function cekStokStyle()
    {
        $model = $this->request->getGet('model');
        $styleSize = $this->request->getGet('style');
        $style = urlencode($styleSize);
        $apiUrl = 'http://172.23.44.14/MaterialSystem/public/api/cekStok/' . $model . '/' . $style;
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
        // Ambil No Model, Style & Area Size dari permintaan AJAX
        $noModel = $this->request->getPost('no_model');
        $styleSize = $this->request->getPost('style_size');
        $area = $this->request->getPost('area');

        // Query data Jalan MC berdasarkan No Model dan Style Size
        $jalanMc = $this->MesinPerStyleModel->getJalanMc($noModel, $styleSize, $area); // Sesuaikan dengan model Anda

        // Kembalikan data dalam format JSON
        return $this->response->setJSON($jalanMc);
    }
    public function getMU($model, $styleSize, $area, $qtyOrder)
    {
        $styleSize = urlencode($styleSize);  // Encode styleSize
        $apiUrl = 'http://172.23.44.14/MaterialSystem/public/api/getMU/' . $model . '/' . $styleSize . '/' . $area;
        $response = file_get_contents($apiUrl);  // Mendapatkan response dari API
        // if ($response === FALSE) {
        //     die('Error occurred while fetching data.');
        // }
        if ($response === FALSE) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Error occurred while fetching data.',
            ]);
        }

        $data = json_decode($response, true);  // Decode JSON response dari API

        if ($data === null) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Invalid data received from API',
            ]);
        }

        // Pastikan respons berupa array
        if (!is_array($data)) {
            $data = (array)$data;  // Paksa konversi ke array
        }

        // Jika respons berupa array tetapi kosong
        if (empty($data)) {
            return $this->response->setJSON([
                'status'  => 'empty',
                'message' => 'Material Usage belum ada, hubungi Gbn',
            ]);
        }

        // Hitung ttl_kebutuhan
        foreach ($data as $key => $item) {
            if (isset($qtyOrder, $item['composition'], $item['gw'], $item['loss'])) {
                // Hitung ttl_keb untuk setiap item
                $ttl_keb = $qtyOrder * $item['gw'] * ($item['composition'] / 100) * (1 + ($item['loss'] / 100)) / 1000;

                // Tambahkan ttl_keb ke elemen saat ini
                $data[$key]['ttl_keb'] = number_format($ttl_keb, 2);
            } else {
                // Jika data tidak valid, tambahkan ttl_keb sebagai null
                $data[$key]['ttl_keb'] = null;
            }
        }

        // Return data sebagai JSON
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

        // Inisialisasi array untuk menyimpan hasil filter
        $filteredData = [];

        // Iterasi setiap elemen pada `$newData`
        foreach ($newData as $rowKey => $rows) {
            foreach ($rows as $index => $item) {
                // Periksa apakah nilai 'ttl' tidak sama dengan '0'
                if (isset($item['ttl']) && floatval($item['ttl']) > 0) {
                    // Tentukan tgl_pakai berdasarkan jenis item
                    if (isset($item['jenis'])) {
                        $jenisBenang = strtolower($item['jenis']);
                        if (in_array($jenisBenang, ['benang', 'nylon'])) {
                            $item['tgl_pakai'] = $this->request->getPost('tgl_pakai_benang_nylon');
                        } else {
                            $item['tgl_pakai'] = $this->request->getPost('tgl_pakai_spandex_karet');
                        }
                    } else {
                        // Jika 'jenis' tidak ada, berikan default
                        $item['tgl_pakai'] = "";
                    }
                    // Masukkan data yang valid ke array hasil filter
                    $filteredData[$rowKey][$index] = $item;
                }
            }
        }
        // Variabel untuk menyimpan data valid
        $validData = [];

        // Loop melalui data baru
        foreach ($filteredData as $group) {
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
    public function deletePemesananSession()
    {
        // Ambil data dari input POST (array `selected`)
        // $selected = $this->request->getPost('selected') ?? []; // Pastikan default adalah array kosong
        $selected = $this->request->getJSON(true)['selected'] ?? [];
        log_message('debug', 'Isi selected: ' . json_encode($selected));
        $pemesananBb = session()->get('pemesananBb') ?? []; // Ambil data session asli
        $found = false; // Variabel untuk melacak apakah data ditemukan

        // Loop melalui data `selected`
        foreach ($selected as $selectedItem) {
            // Pecah `selectedItem` menjadi `id_material` dan `tgl_pakai`
            list($id_material, $tgl_pakai) = explode(',', $selectedItem);

            // Loop melalui session data untuk menemukan dan menghapus
            foreach ($pemesananBb as $groupKey => $group) {
                foreach ($group as $itemKey => $item) {
                    if ($item['id_material'] === $id_material && $item['tgl_pakai'] === $tgl_pakai) {
                        unset($pemesananBb[$groupKey][$itemKey]); // Hapus elemen
                        $pemesananBb[$groupKey] = array_values($pemesananBb[$groupKey]); // Rapi indeks
                        $found = true;
                    }
                }
            }
        }

        if ($found) {
            session()->set('pemesananBb', $pemesananBb);
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Data berhasil dihapus',
                'updated_session' => $pemesananBb
            ]);
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Tidak ada data yang ditemukan atau dihapus'
        ]);
    }
    public function listPemesanan($area)
    {
        function fetchApiData($url)
        {
            try {
                $response = file_get_contents($url);
                if ($response === false) {
                    throw new \Exception("Error fetching data from $url");
                }
                $data = json_decode($response, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \Exception("Invalid JSON response from $url");
                }
                return $data;
            } catch (\Exception $e) {
                error_log($e->getMessage());
                return null;
            }
        }

        $dataList = fetchApiData("http://172.23.44.14/MaterialSystem/public/api/listPemesanan/$area");
        if (!is_array($dataList)) {
            die('Error: Invalid response format for listPemesanan API.');
        }

        foreach ($dataList as $key => $order) {
            $dataList[$key]['ttl_kebutuhan_bb'] = 0;
            if (isset($order['no_model'], $order['item_type'], $order['kode_warna'])) {
                $styleApiUrl = 'http://172.23.44.14/MaterialSystem/public/api/getStyleSizeByBb?no_model='
                    . $order['no_model'] . '&item_type=' . urlencode($order['item_type']) . '&kode_warna=' . urlencode($order['kode_warna']);
                $styleList = fetchApiData($styleApiUrl);

                if ($styleList) {
                    $totalRequirement = 0;
                    foreach ($styleList as $style) {
                        if (isset($style['no_model'], $style['style_size'], $style['gw'], $style['composition'], $style['loss'])) {
                            $orderQty = $this->ApsPerstyleModel->getQtyOrder($style['no_model'], $style['style_size'], $area);
                            $tambahanApiUrl = 'http://172.23.44.14/MaterialSystem/public/api/getKgTambahan?no_model='
                                . $order['no_model'] . '&item_type=' . urlencode($order['item_type']) . '&kode_warna=' . urlencode($order['kode_warna']) . '&style_size=' . urlencode($style['style_size']) . '&area=' . $area;
                            $tambahan = fetchApiData($tambahanApiUrl);
                            $kgPoTambahan = $tambahan['ttl_keb_potambahan'] ?? 0;
                            log_message('info', 'inii :' . $kgPoTambahan);
                            if (isset($orderQty['qty'])) {
                                $requirement = ($orderQty['qty'] * $style['gw'] * ($style['composition'] / 100) * (1 + ($style['loss'] / 100)) / 1000) + $kgPoTambahan;
                                $totalRequirement += $requirement;
                                $dataList[$key]['qty'] = $orderQty['qty'];
                            }
                        }
                    }
                    $dataList[$key]['ttl_kebutuhan_bb'] = $totalRequirement;
                }

                $pengirimanApiUrl = 'http://172.23.44.14/MaterialSystem/public/api/getTotalPengiriman?area=' . $area . '&no_model='
                    . $order['no_model'] . '&item_type=' . urlencode($order['item_type']) . '&kode_warna=' . urlencode($order['kode_warna']);
                $pengiriman = fetchApiData($pengirimanApiUrl);
                $dataList[$key]['ttl_pengiriman'] = $pengiriman['kgs_out'] ?? 0;

                // Hitung sisa jatah
                $dataList[$key]['sisa_jatah'] = $dataList[$key]['ttl_kebutuhan_bb'] - $dataList[$key]['ttl_pengiriman'];
            }
        }

        // dd($dataList);

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
        $threeDays = getNextNonHoliday($initialthreeDay, $liburDates);

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
            'title' => 'Bahan Baku',
            'dataList' => $dataList,
            'day' => $day,
            'tomorrow' => $tomorrow,
            'twoDays' => $twoDays,
            'threeDays' => $threeDays,
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
    public function getTanggalPakai()
    {
        $jenis = $this->request->getPost('jenis');
        $tomorrow = $this->request->getPost('tomorrow');
        $twoDays = $this->request->getPost('twoDays');
        $threeDays = $this->request->getPost('threeDays');
        $day = $this->request->getPost('day');

        $html = '<label for="tanggal">Tanggal Pakai:</label>';

        switch ($day) {
            case 'Thursday':
                if (in_array($jenis, ['BENANG', 'NYLON'])) {
                    $html .= '<input type="date" id="tanggal_pakai" name="tanggal_pakai" class="form-control" value="' . $tomorrow . '" required readonly>';
                } elseif (in_array($jenis, ['SPANDEX', 'KARET'])) {
                    $html .= '<select id="tanggal_pakai" name="tanggal_pakai" class="form-select" required>';
                    $html .= '<option value="' . $twoDays . '">' . $twoDays . '</option>';
                    $html .= '<option value="' . $threeDays . '">' . $threeDays . '</option>';
                    $html .= '</select>';
                }
                break;

            case 'Friday':
                if ($jenis == 'BENANG') {
                    $html .= '<select id="tanggal_pakai" name="tanggal_pakai" class="form-select" required>';
                    $html .= '<option value="' . $tomorrow . '">' . $tomorrow . '</option>';
                    $html .= '<option value="' . $twoDays . '">' . $twoDays . '</option>';
                    $html .= '</select>';
                } elseif ($jenis == 'NYLON') {
                    $html .= '<input type="date" id="tanggal_pakai" name="tanggal_pakai" class="form-control" value="' . $tomorrow . '" required readonly>';
                } elseif (in_array($jenis, ['SPANDEX', 'KARET'])) {
                    $html .= '<input type="date" id="tanggal_pakai" name="tanggal_pakai" class="form-control" value="' . $threeDays . '" required readonly>';
                }
                break;

            case 'Saturday':
                if ($jenis == 'BENANG') {
                    $html .= '<input type="date" id="tanggal_pakai" name="tanggal_pakai" class="form-control" value="' . $twoDays . '" required readonly>';
                } elseif ($jenis == 'NYLON') {
                    $html .= '<select id="tanggal_pakai" name="tanggal_pakai" class="form-select" required>';
                    $html .= '<option value="' . $tomorrow . '">' . $tomorrow . '</option>';
                    $html .= '<option value="' . $twoDays . '">' . $twoDays . '</option>';
                    $html .= '</select>';
                } elseif (in_array($jenis, ['SPANDEX', 'KARET'])) {
                    $html .= '<input type="date" id="tanggal_pakai" name="tanggal_pakai" class="form-control" value="' . $threeDays . '" required readonly>';
                }
                break;

            default:
                if (in_array($jenis, ['BENANG', 'NYLON'])) {
                    $html .= '<input type="date" id="tanggal_pakai" name="tanggal_pakai" class="form-control" value="' . $tomorrow . '" required readonly>';
                } elseif (in_array($jenis, ['SPANDEX', 'KARET'])) {
                    $html .= '<input type="date" id="tanggal_pakai" name="tanggal_pakai" class="form-control" value="' . $twoDays . '" required readonly>';
                }
                break;
        }

        echo $html;
    }
    public function requestAdditionalTime()
    {
        $area = $this->request->getPost('area');
        $jenis = $this->request->getPost('jenis');
        $tanggal_pakai = $this->request->getPost('tanggal_pakai');

        // Jika search ada, panggil API eksternal dengan query parameter 'search'
        $apiUrl = 'http://172.23.44.14/MaterialSystem/public/api/requestAdditionalTime/' . $area . '?jenis=' . urlencode($jenis) . '&tanggal_pakai=' . urlencode($tanggal_pakai);

        try {
            // Mengambil respon dari API eksternal
            $response = file_get_contents($apiUrl);
            $additionalTime = json_decode($response, true);

            if (isset($additionalTime['status']) && $additionalTime['status']) {
                session()->setFlashdata('success', $additionalTime['message'] ?? 'Update berhasil dilakukan.');
            } else {
                session()->setFlashdata('error', $additionalTime['message'] ?? 'Update gagal dilakukan');
            }
        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Gagal menghubungi API eksternal. Silakan coba lagi.');
        }

        // Redirect kembali ke halaman sebelumnya
        return redirect()->to(previous_url());
    }
    public function pph($area)
    {
        $areas = $this->areaModel->getArea();
        // Filter agar 'name' yang mengandung 'Gedung' tidak ikut
        $filteredArea = array_filter($areas, function ($item) {
            return stripos($item['name'], 'Gedung') === false; // Cek jika 'Gedung' tidak ada di 'name'
        });

        // Ambil hanya field 'name'
        $result = array_column($filteredArea, 'name');

        $data = [
            'role' => session()->get('role'),
            'title' => 'PPH',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'targetProd' => 0,
            'produksiBulan' => 0,
            'produksiHari' => 0,
            'area' => $area, // khusus untuk akun area
            'areas' => $result,
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

        $areas = $this->areaModel->getArea();
        // Filter agar 'name' yang mengandung 'Gedung' tidak ikut
        $filteredArea = array_filter($areas, function ($item) {
            return stripos($item['name'], 'Gedung') === false; // Cek jika 'Gedung' tidak ada di 'name'
        });

        // Ambil hanya field 'name'
        $result = array_column($filteredArea, 'name');

        return view($role . '/Material/pphPerStyle', [
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'title' => 'PPH',
            'role' => $role,
            'area' => $area,
            'areas' => $result,
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

        $areas = $this->areaModel->getArea();
        // Filter agar 'name' yang mengandung 'Gedung' tidak ikut
        $filteredArea = array_filter($areas, function ($item) {
            return stripos($item['name'], 'Gedung') === false; // Cek jika 'Gedung' tidak ada di 'name'
        });

        // Ambil hanya field 'name'
        $result = array_column($filteredArea, 'name');

        return view($role . '/Material/pphPerDays', [
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'title'      => 'PPH',
            'role'       => $role,
            'area'       => $area,
            'areas'       => $result,
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
            $bsMesinData = $this->BsMesinModel->getBsMesinHarian($mastermodels, $sizes, $tanggal, $area);
            log_message('debug', 'Hasil bsMesinData: ' . print_r($bsMesinData, true));

            // Create a lookup table for fast matching
            $bsMesinMap = [];
            foreach ($bsMesinData as $bs) {
                $key = $bs['no_model'] . '_' . $bs['size'];
                $bsMesinMap[$key] = $bs['bs_mesin'];
            }
            log_message('debug', 'Mapping bsMesinMap: ' . print_r($bsMesinMap, true));

            // Assign bs_mesin to production data
            foreach ($data as &$prod) {
                $key = $prod['mastermodel'] . '_' . $prod['size'];
                $prod['bs_mesin'] = $bsMesinMap[$key] ?? 0; // Default to null if not found
                log_message('debug', 'Assign bs_mesin untuk ' . $key . ': ' . $prod['bs_mesin']);
            }
        }

        $result = [];
        $pphInisial = [];

        foreach ($data as $prod) {
            $key = $prod['mastermodel'] . '-' . $prod['size'];

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
        $dataToSort = array_filter($result, 'is_array');

        usort($dataToSort, function ($a, $b) {
            if ($a['mastermodel'] !== $b['mastermodel']) {
                return $a['mastermodel'] <=> $b['mastermodel'];
            }
            if ($a['item_type'] !== $b['item_type']) {
                return $a['item_type'] <=> $b['item_type'];
            }
            return $a['kode_warna'] <=> $b['kode_warna'];
        });
        return $this->response->setJSON($dataToSort);
    }
    public function getQtyByModelSize()
    {
        // Ambil No Model, Style & Area Size dari permintaan AJAX
        $noModel = $this->request->getPost('no_model');
        $styleSize = $this->request->getPost('style_size');
        $area = $this->request->getPost('area');

        // Query data Jalan MC berdasarkan No Model dan Style Size
        $qty = $this->ApsPerstyleModel->getQtyOrder($noModel, $styleSize, $area); // Sesuaikan dengan model Anda

        // Kembalikan data dalam format JSON
        return $this->response->setJSON($qty);
    }
    public function stockbb()
    {
        $role = session()->get('role');
        return view($role . '/Material/stockbb', [
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'title'      => 'Stock Gudang Benang',
            'role'       => $role,
        ]);
    }
    public function poTambahan($area)
    {
        $areas = $this->areaModel->getArea();
        // Filter agar 'name' yang mengandung 'Gedung' tidak ikut
        $filteredArea = array_filter($areas, function ($item) {
            return stripos($item['name'], 'Gedung') === false; // Cek jika 'Gedung' tidak ada di 'name'
        });

        // Ambil hanya field 'name'
        $result = array_column($filteredArea, 'name');

        $data = [
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'title' => 'Po Tambahan',
            'area' => $area,
            'areas' => $result,
            'role' => session()->get('role'),
        ];
        return view(session()->get('role') . '/Material/po_tambahan', $data);
    }
    public function formPoTambahan($area)
    {
        $model = $this->ApsPerstyleModel->getPerArea($area);
        $noModel = array_unique(array_column($model, 'mastermodel'));
        $data = [
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'noModel' => $noModel,
            'title' => 'Po Tambahan',
            'area' => $area,
            'role' => session()->get('role'),
        ];
        return view(session()->get('role') . '/Material/form_po_tambahan', $data);
    }
    public function getStyleSize($area, $noModel)
    {
        // 1) Panggil API eksternal atau query db untuk style size
        $size = $this->ApsPerstyleModel->getSizesByNoModelAndArea($noModel, $area);

        // 2) Asumsikan API mengembalikan ["style_sizes"=>["S","M","L",...]]
        return $this->response
            ->setJSON($size);
    }
    public function poTambahanDetail($noModel, $area)
    {
        $detail = [];
        $apiUrl = 'http://172.23.44.14/MaterialSystem/public/api/poTambahanDetail/' . $noModel . '/' . $area;

        // Mengambil data dari API eksternal
        $response = @file_get_contents($apiUrl);

        log_message('debug', 'API response: ' . $response);

        if ($response === false) {
            log_message('error', 'Gagal mengambil data dari API');
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Tidak dapat mengambil data dari API']);
        }

        $materialData = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            log_message('error', 'JSON tidak valid: ' . json_last_error_msg());
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Data JSON tidak valid']);
        }

        // Ambil item_type saja (key dari level pertama JSON)
        $itemTypes = [];
        foreach ($materialData as $key => $value) {
            if (isset($value['item_type'])) {
                $itemTypes[] = [
                    'item_type' => $value['item_type']
                ];
            }
        }

        // Ambil semua style_size
        $styleSize = [];
        foreach ($materialData as $itemTypeData) {
            if (isset($itemTypeData['kode_warna']) && is_array($itemTypeData['kode_warna'])) {
                foreach ($itemTypeData['kode_warna'] as $kodeWarnaData) {
                    if (isset($kodeWarnaData['style_size']) && is_array($kodeWarnaData['style_size'])) {
                        foreach ($kodeWarnaData['style_size'] as $style) {
                            if (isset($style['style_size'])) {
                                $styleSize[] = $style['style_size'];
                            }
                        }
                    }
                }
            }
        }

        $styleSize = array_unique($styleSize);

        // Ambil SISA per style_size
        $sisaOrderList = [];
        foreach ($styleSize as $style) {
            $sisa = $this->ApsPerstyleModel->getSisaPerSize($area, $noModel, [$style]);
            $sisaPcs = is_array($sisa) ? $sisa['sisa'] ?? 0 : ($sisa->sisa ?? 0);
            $sisaOrderList[$style] = (float)$sisaPcs;
        }

        // Ambil BS MESIN per style_size
        $bsMesinList = [];
        foreach ($styleSize as $style) {
            $bs = $this->BsMesinModel->getBsMesin($area, $noModel, [$style]);
            $bsGram = is_array($bs) ? $bs['bs_gram'] ?? 0 : ($bs->bs_gram ?? 0);
            $bsMesinList[$style] = (float)$bsGram;
        }

        // Ambil BS SETTING per style_size
        $bsSettingList = [];
        foreach ($styleSize as $style) {
            $validate = [
                'area' => $area,
                'style' => $style,
                'no_model' => $noModel
            ];
            $idaps = $this->ApsPerstyleModel->getIdForBs($validate);
            $bsSetting = $this->bsModel->getTotalBsSet($idaps);
            $bsSettingList[$style] = isset($bsSetting['qty']) ? (int)$bsSetting['qty'] : 0;
        }

        $brutoList = [];
        foreach ($materialData as $itemType => $itemData) {
            foreach ($itemData['kode_warna'] as $kodeWarna => $warnaData) {
                foreach ($warnaData['style_size'] as $style) {
                    $styleSize = $style['style_size'] ?? '';

                    // ambil data produksi per style
                    $prod = $this->orderModel->getDataPph($area, $noModel, $styleSize);
                    $prod = is_array($prod) ? $prod : [];
                    $bruto = $prod['bruto'] ?? 0;

                    $brutoList[$styleSize] = $bruto;
                }
            }
        }

        log_message('debug', 'PPH: ' . print_r($brutoList, true));

        return $this->response->setJSON([
            'item_types' => $itemTypes,
            'material' => $materialData,
            'sisa_order' => $sisaOrderList,
            'bs_mesin' => $bsMesinList,
            'bs_setting' => $bsSettingList,
            'bruto' => $brutoList
        ]);
    }
    public function savePoTambahan($area)
    {
        $json = $this->request->getJSON(true);

        if (empty($json) || !isset($json[0])) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON(['status' => 'error', 'message' => 'Payload invalid.']);
        }

        // Siapkan items
        $items = array_map(function ($item) use ($area) {
            return [
                'area'              => $area,
                'no_model'          => $item['no_model'] ?? '',
                'item_type'         => $item['item_type'] ?? '',
                'kode_warna'        => $item['kode_warna'] ?? '',
                'color'             => $item['color'] ?? '',
                'style_size'        => $item['style_size'] ?? '',
                'terima_kg'         => (float) ($item['terima_kg'] ?? 0),
                'sisa_bb_mc'        => (float) ($item['sisa_bb_mc'] ?? 0),
                'sisa_order_pcs'    => (float) ($item['sisa_order_pcs'] ?? 0),
                'bs_mesin_kg'       => (float) ($item['bs_mesin_kg'] ?? 0),
                'bs_st_pcs'         => (float) ($item['bs_st_pcs'] ?? 0),
                'poplus_mc_kg'      => (float) ($item['poplus_mc_kg'] ?? 0),
                'poplus_mc_cns'     => (float) ($item['poplus_mc_cns'] ?? 0),
                'plus_pck_pcs'      => (float) ($item['plus_pck_pcs'] ?? 0),
                'plus_pck_kg'       => (float) ($item['plus_pck_kg'] ?? 0),
                'plus_pck_cns'      => (float) ($item['plus_pck_cns'] ?? 0),
                'lebih_pakai_kg'    => (float) ($item['lebih_pakai_kg'] ?? 0),
                'keterangan'        => $item['keterangan'] ?? '',
                'admin'             => session()->get('username'),
                'created_at'        => date('Y-m-d H:i:s'),
            ];
        }, $json);

        // Log isi items ke log file
        log_message('debug', 'ITEMS untuk dikirim ke API: ' . json_encode($items));

        $payload = ['items' => $items];
        $apiUrl = 'http://172.23.44.14/MaterialSystem/public/api/savePoTambahan';

        $ch = curl_init($apiUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
        ]);

        $response = curl_exec($ch);
        $error    = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false) {
            return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Curl error: ' . $error]);
        }

        $result = json_decode($response, true);

        return $this->response->setStatusCode($httpCode)->setJSON($result);
    }
    public function filterPoTambahan($area)
    {
        $noModel = $this->request->getGet('model');
        $tglBuat = $this->request->getGet('tglBuat');
        $apiUrl = "http://172.23.44.14/MaterialSystem/public/api/filterPoTambahan"
            . "?area=" . urlencode($area)
            . "&tglBuat=" . urlencode($tglBuat)
            . "&model=" . urlencode($noModel);

        $ch = curl_init($apiUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
        ]);

        $response = curl_exec($ch);
        $error    = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($response === false) {
            return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Curl error: ' . $error]);
        }

        $result = json_decode($response, true);

        return $this->response->setStatusCode($httpCode)->setJSON($result);
    }
    public function filterTglPakai($area)
    {
        $tgl_awal = $this->request->getPost('tgl_awal');
        $tgl_akhir = $this->request->getPost('tgl_akhir');

        // Ambil data dari model sesuai range tanggal
        $apiUrl = "http://172.23.44.14/MaterialSystem/public/api/filterTglPakai/"
            . $area
            . "?awal=" . urlencode($tgl_awal)
            . "&akhir=" . urlencode($tgl_akhir);

        $client = \Config\Services::curlrequest();
        $response = $client->get($apiUrl);

        $rows = json_decode($response->getBody(), true) ?? [];

        log_message('debug', 'filterTglPakai rows: ' . print_r($rows, true));

        return $this->response->setJSON($rows);
    }
    public function reportPemesanan($area)
    {
        function fetchApiData($url)
        {
            try {
                $response = file_get_contents($url);
                if ($response === false) {
                    throw new \Exception("Error fetching data from $url");
                }
                $data = json_decode($response, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \Exception("Invalid JSON response from $url");
                }
                return $data;
            } catch (\Exception $e) {
                error_log($e->getMessage());
                return null;
            }
        }

        $dataList = fetchApiData("http://172.23.44.14/MaterialSystem/public/api/listPemesanan/$area");
        if (!is_array($dataList)) {
            die('Error: Invalid response format for listPemesanan API.');
        }

        foreach ($dataList as $key => $order) {
            $dataList[$key]['ttl_kebutuhan_bb'] = 0;
            if (isset($order['no_model'], $order['item_type'], $order['kode_warna'])) {
                $styleApiUrl = 'http://172.23.44.14/MaterialSystem/public/api/getStyleSizeByBb?no_model='
                    . $order['no_model'] . '&item_type=' . urlencode($order['item_type']) . '&kode_warna=' . urlencode($order['kode_warna']);
                $styleList = fetchApiData($styleApiUrl);

                if ($styleList) {
                    $totalRequirement = 0;
                    foreach ($styleList as $style) {
                        if (isset($style['no_model'], $style['style_size'], $style['gw'], $style['composition'], $style['loss'])) {
                            $orderQty = $this->ApsPerstyleModel->getQtyOrder($style['no_model'], $style['style_size'], $area);
                            if (isset($orderQty['qty'])) {
                                $requirement = $orderQty['qty'] * $style['gw'] * ($style['composition'] / 100) * (1 + ($style['loss'] / 100)) / 1000;
                                $totalRequirement += $requirement;
                                $dataList[$key]['qty'] = $orderQty['qty'];
                            }
                        }
                    }
                    $dataList[$key]['ttl_kebutuhan_bb'] = $totalRequirement;
                }

                $pengirimanApiUrl = 'http://172.23.44.14/MaterialSystem/public/api/getTotalPengiriman?area=' . $area . '&no_model='
                    . $order['no_model'] . '&item_type=' . urlencode($order['item_type']) . '&kode_warna=' . urlencode($order['kode_warna']);
                $pengiriman = fetchApiData($pengirimanApiUrl);
                $dataList[$key]['ttl_pengiriman'] = $pengiriman['kgs_out'] ?? 0;

                // Hitung sisa jatah
                $dataList[$key]['sisa_jatah'] = $dataList[$key]['ttl_kebutuhan_bb'] - $dataList[$key]['ttl_pengiriman'];
            }
        }

        // dd($dataList);

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
        $threeDays = getNextNonHoliday($initialthreeDay, $liburDates);

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
            'title' => "Bahan Baku",
            'dataList' => $dataList,
            'day' => $day,
            'tomorrow' => $tomorrow,
            'twoDays' => $twoDays,
            'threeDays' => $threeDays,
        ];

        return view(session()->get('role') . '/Material/reportPemesanan', $data);
    }
    public function getNomodel()
    {
        $poTambahan = $this->request->getGet('po_tambahan'); // Ambil parameter PO Tambahan
        $area = $this->request->getGet('area'); // Ambil parameter PO Tambahan

        // Logika untuk menentukan data berdasarkan status PO Tambahan
        if ($poTambahan == 1) {
            // Jika search ada, panggil API eksternal dengan query parameter 'search'
            $apiUrl = 'http://172.23.44.14/MaterialSystem/public/api/getNoModelByPoTambahan?area=' . $area;

            try {
                $response = file_get_contents($apiUrl);
                if ($response === false) {
                    throw new \Exception("Failed to fetch data from API: $apiUrl");
                }

                $data = json_decode($response, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \Exception("Invalid JSON response from API: $apiUrl");
                }
            } catch (\Exception $e) {
                // Tangani error
                return $this->response->setJSON(['error' => $e->getMessage()])->setStatusCode(500);
            }
        } else {
            $data = $this->DetailPlanningModel->getNoModelAktif($area);
        }

        return $this->response->setJSON($data)->setStatusCode(200);
    }
    public function getStyleSizeByNoModelPemesanan()
    {
        // Ambil No Model dari permintaan AJAX
        $noModel = $this->request->getGet('no_model');
        $area = $this->request->getGet('area');
        $poTambahan = $this->request->getGet('po_tambahan');

        // Logika untuk menentukan data berdasarkan status PO Tambahan
        if ($poTambahan == 1) {
            // Jika search ada, panggil API eksternal dengan query parameter 'search'
            $apiUrl = 'http://172.23.44.14/MaterialSystem/public/api/getStyleSizeByPoTambahan?no_model=' . $noModel . '&area=' . $area;

            try {
                $response = file_get_contents($apiUrl);
                if ($response === false) {
                    throw new \Exception("Failed to fetch data from API: $apiUrl");
                }

                $data = json_decode($response, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \Exception("Invalid JSON response from API: $apiUrl");
                }
            } catch (\Exception $e) {
                // Tangani error
                return $this->response->setJSON(['error' => $e->getMessage()])->setStatusCode(500);
            }
        } else {
            $data = $this->ApsPerstyleModel->getStyleSize($noModel); // Sesuaikan dengan model Anda
        }

        // Kembalikan data dalam format JSON
        return $this->response->setJSON($data)->setStatusCode(200);
    }
    public function sisaKebutuhanArea($area)
    {
        $noModel = $this->request->getGet('filter_model') ?? '';

        // Initialize dataPemesanan as empty by default
        $dataPemesanan = [];
        $dataRetur = [];

        if (!empty($area) && !empty($noModel)) {

            $pemesananUrl  = 'http://172.23.44.14/MaterialSystem/public/api/getPemesananByAreaModel?area=' . $area . '&no_model=' . urlencode($noModel);
            $pemesananResponse = file_get_contents($pemesananUrl);
            $dataPemesanan   = json_decode($pemesananResponse, true);

            $returUrl  = 'http://172.23.44.14/MaterialSystem/public/api/getReturByAreaModel?area=' . $area . '&no_model=' . urlencode($noModel);
            $returResponse = file_get_contents($returUrl);
            $dataRetur     = json_decode($returResponse, true);
        }
        // dd($noModel);

        $mergedData = [];
        $kebutuhan = [];

        // Tambahkan semua data pemesanan ke mergedData
        foreach ($dataPemesanan as $key => $pemesanan) {
            // ambil data styleSize by bb
            $urlStyle = 'http://172.23.44.14/MaterialSystem/public/api/getStyleSizeByBb?no_model=' . $pemesanan['no_model']
                . '&item_type=' . urlencode($pemesanan['item_type'])
                . '&kode_warna=' . urlencode($pemesanan['kode_warna']);

            $styleResponse = file_get_contents($urlStyle);
            $getStyle     = json_decode($styleResponse, true);

            $ttlKeb = 0;
            $ttlQty = 0;

            foreach ($getStyle as $i => $data) {
                // Ambil qty
                $qtyData = $this->ApsPerstyleModel->getQtyOrder($noModel, $data['style_size'], $area);
                $qty         = (intval($qtyData['qty']) ?? 0);

                // Ambil kg po tambahan
                $PoPlus = 'http://172.23.44.14/MaterialSystem/public/api/getKgPoTambahan?no_model=' . $pemesanan['no_model']
                    . '&item_type=' . urlencode($pemesanan['item_type'])
                    . '&area=' . $area;

                $poPlusResponse = file_get_contents($PoPlus);
                $getPoPlus     = json_decode($poPlusResponse, true);


                $kgPoTambahan = floatval(
                    $getPoPlus['ttl_keb_potambahan'] ?? 0
                );
                if ($qty > 0) {
                    $kebutuhan = (($qty * $data['gw'] * ($data['composition'] / 100)) * (1 + ($data['loss'] / 100)) / 1000) + $kgPoTambahan;
                    $pemesanan['ttl_keb'] = $ttlKeb;
                }
                $ttlKeb += $kebutuhan;
                $ttlQty += $qty;
            }
            $pemesanan['qty']     = $ttlQty; // ttl qty pcs
            $pemesanan['ttl_keb'] = $ttlKeb; // ttl kebutuhan bb


            $mergedData[] = [
                'no_model'           => $pemesanan['no_model'],
                'item_type'          => $pemesanan['item_type'],
                'kode_warna'         => $pemesanan['kode_warna'],
                'color'              => $pemesanan['color'],
                'max_loss'           => $pemesanan['max_loss'],
                'tgl_pakai'          => $pemesanan['tgl_pakai'],
                'id_total_pemesanan' => $pemesanan['id_total_pemesanan'],
                'ttl_jl_mc'          => $pemesanan['ttl_jl_mc'],
                'ttl_kg'             => number_format($pemesanan['ttl_kg'], 2),
                'po_tambahan'        => $pemesanan['po_tambahan'],
                'ttl_keb'            => number_format($pemesanan['ttl_keb'], 2),
                'kg_out'             => number_format($pemesanan['kgs_out'], 2),
                'lot_out'            => $pemesanan['lot_out'],
                // field retur kosong
                'tgl_retur'          => null,
                'kgs_retur'          => null,
                'lot_retur'          => null,
                'ket_gbn'            => null,
            ];
            $kebutuhanDipakai[$key] = true;
        }

        // Tambahkan semua data retur ke mergedData (data pemesanan diset null)
        foreach ($dataRetur as $retur) {
            // ambil data styleSize by bb
            $urlStyle = 'http://172.23.44.14/MaterialSystem/public/api/getStyleSizeByBb?no_model=' . $retur['no_model']
                . '&item_type=' . urlencode($retur['item_type'])
                . '&kode_warna=' . urlencode($retur['kode_warna']);

            $styleResponse = file_get_contents($urlStyle);
            $getStyle      = json_decode($styleResponse, true);

            $ttlKeb = 0;
            $ttlQty = 0;

            foreach ($getStyle as $i => $data) {
                // Ambil qty
                $qtyData = $this->ApsPerstyleModel->getQtyOrder($noModel, $data['style_size'], $area);
                $qty     = (intval($qtyData['qty']) ?? 0);

                // Ambil kg po tambahan
                $PoPlus = 'http://172.23.44.14/MaterialSystem/public/api/getKgPoTambahan?no_model=' . $retur['no_model']
                    . '&item_type=' . urlencode($retur['item_type'])
                    . '&area=' . $area;

                $poPlusResponse = file_get_contents($PoPlus);
                $getPoPlus     = json_decode($poPlusResponse, true);


                $kgPoTambahan = floatval(
                    $getPoPlus['ttl_keb_potambahan'] ?? 0
                );

                if ($qty > 0) {
                    $kebutuhan = (($qty * $data['gw'] * ($data['composition'] / 100)) * (1 + ($data['loss'] / 100)) / 1000) + $kgPoTambahan;
                    $retur['ttl_keb'] = $ttlKeb;
                }
                $ttlKeb += $kebutuhan;
                $ttlQty += $qty;
            }
            $retur['qty']     = $ttlQty; // ttl qty pcs
            $retur['ttl_keb'] = $ttlKeb; // ttl kebutuhan bb


            $mergedData[] = [
                'no_model'           => $retur['no_model'],
                'item_type'          => $retur['item_type'],
                'kode_warna'         => $retur['kode_warna'],
                'color'              => $retur['warna'],
                'max_loss'           => 0,
                'tgl_pakai'          => null,
                'id_total_pemesanan' => null,
                'ttl_jl_mc'          => null,
                'ttl_kg'             => null,
                'po_tambahan'        => null,
                'ttl_keb'            => number_format($retur['ttl_keb'], 2),
                'kg_out'             => null,
                'lot_out'            => null,
                'tgl_retur'          => $retur['tgl_retur'],
                'kgs_retur'          => number_format($retur['kgs_retur'], 2),
                'lot_retur'          => $retur['lot_retur'],
                'ket_gbn'            => $retur['keterangan_gbn'],
            ];
        }

        if ($mergedData) {
            usort($mergedData, function ($a, $b) {
                // Bandingkan item_type (ASC)
                $cmpItem = strcmp($a['item_type'], $b['item_type']);
                if ($cmpItem !== 0) {
                    return $cmpItem;
                }

                // Bandingkan kode_warna (ASC)
                $cmpWarna = strcmp($a['kode_warna'], $b['kode_warna']);
                if ($cmpWarna !== 0) {
                    return $cmpWarna;
                }

                // Ambil tanggal (prioritas tgl_pakai, fallback ke tgl_retur)
                $tanggalA = $a['tgl_pakai'] ?: $a['tgl_retur'];
                $tanggalB = $b['tgl_pakai'] ?: $b['tgl_retur'];

                // Handle tanggal kosong supaya selalu di bawah
                if (empty($tanggalA) && !empty($tanggalB)) return 1;
                if (!empty($tanggalA) && empty($tanggalB)) return -1;

                // Bandingkan tanggal (DESC)
                return strtotime($tanggalB) <=> strtotime($tanggalA);
            });
        }

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
            'noModel' => $noModel,
            'title' => "Bahan Baku",
            'dataPemesanan' => $mergedData,
        ];

        return view(session()->get('role') . '/Material/reportSisaKebutuhan', $data);
    }
    public function jatahBahanBaku()
    {
        $noModel = $this->request->getGet('no_model');

        // Inisialisasi data default
        $order          = [];
        $headerRow      = [];
        $result         = [];
        $areas          = [];
        $totalPo        = 0;
        $models         = [];


        if ($noModel) {
            //
            // 1) Ambil headerRow & hitung totalQty per delivery (untuk $order)
            //
            $order = $this->ApsPerstyleModel->getSisaDeliv($noModel) ?: [];

            $apiUrl = 'http://172.23.44.14/MaterialSystem/public/api/pph?model=' . urlencode($noModel);
            $material = @file_get_contents($apiUrl);

            // $models = [];
            if ($material !== FALSE) {
                $models = json_decode($material, true);
            }

            // Ambil semua area unik dari $order

            foreach ($order as $ord) {
                if (!in_array($ord['area'], $areas)) {
                    $areas[] = $ord['area'];
                }
            }
            sort($areas);

            // Kelompokkan order berdasarkan style_size, lalu delivery dan area
            $groupedOrders = [];
            foreach ($order as $ord) {
                $style_size = $ord['size'];
                $delivery = $ord['delivery'];
                $area = $ord['area'];
                $qty = $ord['qty'];

                if (!isset($groupedOrders[$style_size][$delivery][$area])) {
                    $groupedOrders[$style_size][$delivery][$area] = 0;
                }

                $groupedOrders[$style_size][$delivery][$area] += $qty;
            }



            // Hitung total per kombinasi delivery, item_type, kode_warna, dan area
            foreach ($models as $mat) {
                $style_size = $mat['style_size'];
                $item_type = $mat['item_type'];
                $kode_warna = $mat['kode_warna'];
                $warna = $mat['color']; // warna
                $comp = floatval($mat['composition']);
                $gw = floatval($mat['gw']);
                $loss = floatval($mat['loss']);

                if (!isset($groupedOrders[$style_size])) {
                    continue;
                }

                foreach ($groupedOrders[$style_size] as $delivery => $areaData) {
                    if (!isset($result[$delivery][$item_type][$kode_warna])) {
                        $result[$delivery][$item_type][$kode_warna] = array_fill_keys($areas, 0);
                        $result[$delivery][$item_type][$kode_warna]['Grand Total'] = 0;
                    }

                    foreach ($areaData as $area => $qty) {
                        $total = ($qty * $comp * $gw / 100 / 1000) * (1 + ($loss / 100));
                        $result[$delivery][$item_type][$kode_warna][$area] += $total;
                        $result[$delivery][$item_type][$kode_warna]['Grand Total'] += $total;
                    }
                }
            }


            //
            $totalPo = $this->ApsPerstyleModel->totalPo($noModel)['totalPo'] ?? 0;
        }

        // Render full page—AJAX akan mengambil ulang #table-container saja
        return view(session()->get('role') . '/Material/jatahBahanBaku', [
            'role'            => session()->get('role'),
            'title'           => 'Sisa Jatah Area',
            'active1'         => '',
            'active2'         => '',
            'active3'         => '',
            'active4'         => '',
            'active5'         => '',
            'active6'         => 'active',
            'active7'         => '',
            'noModel'         => $noModel,
            'totalPo'         => $totalPo,
            'result'          => $result,
            'areas'           => $areas,
            'models'          => $models,
        ]);
    }
}
