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
use App\Models\StockAreaModel;
use App\Models\SupermarketModel;
use App\Models\OutArea;
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
    protected $stockArea;
    protected $supermarketModel;
    protected $outArea;

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
        $this->stockArea = new StockAreaModel();
        $this->supermarketModel = new SupermarketModel();
        $this->outArea = new OutArea();
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
                        'jalan_mc'       => $row['jalan_mc'] ?? 0,
                        'ttl_cns'        => $row['ttl_cns'] ?? 0,
                        'ttl_berat_cns'  => $row['ttl_berat_cns'] ?? 0,
                        'id_material'    => $row['id_material'] ?? '',
                        'po_tambahan'    => $row['po_tambahan'] ?? '0',
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
            $groupKey = $data['tgl_pakai'] . '|' . $data['no_model'] . '|' . $data['item_type'] . '|' . $data['kode_warna'] . '|' . $data['warna'] . '|' . $data['po_tambahan'];
            $groupedData[$groupKey][] = $data;
        }
        // dd ($groupedData);
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
    public function filterstatusbahanbaku()
    {
        // Mengambil data master
        $model = $this->request->getGet('model');
        $search = $this->request->getGet('search');
        if (!empty($model)) {

            $master = $this->orderModel->getStartMc($model);
        } else {
            $master = [
                'kd_buyer_order' => '-',
                'no_model'       => '-',
                'delivery_awal'  => '-',  // MIN dari apsperstyle.delivery
                'delivery_akhir' => '-',  // MAX dari apsperstyle.delivery
                'start_mc'       => '-' // MIN dari tanggal_planning.start_mesin
            ];
        }
        // Mengambil nilai 'search' yang dikirim oleh frontend
        // Jika search ada, panggil API eksternal dengan query parameter 'search'
        $params = [
            'model'  => $model ?? '',
            'search' => $search ?? ''
        ];

        $apiUrl = 'http://172.23.44.14/MaterialSystem/public/api/statusbahanbaku/?' . http_build_query($params);


        // Mengambil data dari API eksternal
        $response = file_get_contents($apiUrl);
        $status = json_decode($response, true);


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
                if (isset($item['item_type']) && stripos($item['item_type'], 'JHT') !== false) {
                    // Hitung ttl_keb untuk setiap item nylon jht
                    $ttl_keb = $item['kgs'] ?? 0;
                } else {
                    // Hitung ttl_keb untuk setiap item
                    $ttl_keb = $qtyOrder * $item['gw'] * ($item['composition'] / 100) * (1 + ($item['loss'] / 100)) / 1000;
                }

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
        // dd ($existingData);
        // Ambil data baru dari request POST dengan key 'items'
        $newData = $this->request->getPost('items');
        // \var_dump($newData);
        if (!is_array($newData)) {
            return; // Pastikan $newData adalah array sebelum diproses
        }

        // Inisialisasi array untuk menyimpan hasil filter
        $filteredData = [];
        // dd ($newData);
        // Iterasi setiap elemen pada `$newData`
        foreach ($newData as $rowKey => $rows) {
            foreach ($rows as $index => $item) {
                // Periksa apakah nilai 'ttl' tidak sama dengan '0'
                if (isset($item['ttl']) && floatval($item['ttl']) > 0 && isset($item['ttl_cns']) && floatval($item['ttl_cns']) > 0) {
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
                            $record['tgl_pakai'] === $existingRecord['tgl_pakai'] &&
                            $record['po_tambahan'] === $existingRecord['po_tambahan']
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
    private function fetchApiData(string $url)
    {
        try {
            $response = @file_get_contents($url);
            if ($response === false) {
                throw new \Exception("Error fetching data from $url");
            }
            $data = json_decode($response, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception("Invalid JSON response from $url");
            }
            return $data;
        } catch (\Exception $e) {
            log_message('error', $e->getMessage());
            return null;
        }
    }
    private function fetchApiData2(string $url)
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,          // batas waktu 10 detik
            CURLOPT_CONNECTTIMEOUT => 5,    // batas waktu koneksi
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 3,
            CURLOPT_HTTPHEADER => [
                'Accept: application/json'
            ],
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr  = curl_error($ch);
        curl_close($ch);

        if ($response === false || $httpCode !== 200) {
            log_message('error', "fetchApiData gagal: $url (HTTP $httpCode, cURL error: $curlErr)");
            return null;
        }

        $data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            log_message('error', "JSON rusak dari API: $url. Response: $response");
            return null;
        }

        return $data;
    }

    public function listPemesanan($area)
    {
        // ini_set('max_execution_time', 300); // Sets the limit to 300 seconds (5 minutes)

        // ambil filter dari query string (jika ada)
        $tglPakai = $this->request->getGet('tgl_pakai');
        $pdk      = $this->request->getGet('searchPdk');

        // dd($tglPakai, $pdk);

        // kalau dua-duanya kosong, langsung return kosong
        if (empty($tglPakai) && empty($pdk)) {
            $dataList = []; // ga usah load dari API
            $message = 'Silakan filter tanggal pakai atau no model terlebih dahulu.';
        } elseif (!empty($tglPakai) || !empty($pdk)) {
            $message = null;
            $rawList = $this->fetchApiData("http://172.23.44.14/MaterialSystem/public/api/listPemesanan/{$area}?tgl_pakai=" . urlencode($tglPakai) . "&searchPdk=" . urlencode($pdk));
            if (!is_array($rawList)) {
                // handle error dengan baik
                return redirect()->back()->with('error', 'Gagal mengambil data pemesanan.');
            }
            // Filter RAW data dulu (mengurangi jumlah panggilan API untuk enrichment)
            $filteredRaw = array_filter($rawList, function ($order) use ($tglPakai, $pdk) {
                // jika kedua filter diberikan -> cari yang memenuhi KEDUA kondisi
                if ($tglPakai && $pdk) {
                    return (isset($order['tgl_pakai']) && $order['tgl_pakai'] === $tglPakai)
                        && (isset($order['no_model']) && stripos($order['no_model'], $pdk) !== false);
                }
                // jika hanya tglPakai
                if ($tglPakai) {
                    return isset($order['tgl_pakai']) && $order['tgl_pakai'] === $tglPakai;
                }
                // jika hanya pdk/no_model (support partial search)
                if ($pdk) {
                    return isset($order['no_model']) && stripos($order['no_model'], $pdk) !== false;
                }
                // jika tidak ada filter -> tampilkan semua
                return true;
            });

            // reindex array
            $dataList = array_values($filteredRaw);

            foreach ($dataList as $key => $order) {
                $dataList[$key]['ttl_kebutuhan_bb'] = 0;
                if (isset($order['no_model'], $order['item_type'], $order['kode_warna'])) {
                    $styleApiUrl = 'http://172.23.44.14/MaterialSystem/public/api/getStyleSizeByBb?no_model='
                        . $order['no_model'] . '&item_type=' . urlencode($order['item_type']) . '&kode_warna=' . urlencode($order['kode_warna']) . '&warna=' . urlencode($order['color']);
                    $styleList = $this->fetchApiData($styleApiUrl);

                    if ($styleList) {
                        $totalRequirement = 0;
                        foreach ($styleList as $style) {
                            if (isset($style['no_model'], $style['style_size'], $style['gw'], $style['composition'], $style['loss'])) {
                                $orderQty = $this->ApsPerstyleModel->getQtyOrder($style['no_model'], $style['style_size'], $area);
                                if (isset($orderQty['qty'])) {
                                    if (isset($style['item_type']) && stripos($style['item_type'], 'JHT') !== false) {
                                        $requirement = $style['kgs'] ?? 0;
                                    } else {
                                        // $requirement = ($orderQty['qty'] * $style['gw'] * ($style['composition'] / 100) * (1 + ($style['loss'] / 100)) / 1000) + $kgPoTambahan;
                                        $requirement = ($orderQty['qty'] * $style['gw'] * ($style['composition'] / 100) * (1 + ($style['loss'] / 100)) / 1000);
                                    }
                                    $totalRequirement += $requirement;
                                    $dataList[$key]['qty'] = $orderQty['qty'];
                                }
                            }
                        }
                        $tambahanApiUrl = 'http://172.23.44.14/MaterialSystem/public/api/getKgTambahan?no_model='
                            . $order['no_model'] . '&item_type=' . urlencode($order['item_type']) . '&kode_warna=' . urlencode($order['kode_warna']) . '&area=' . $area;
                        $tambahan = $this->fetchApiData($tambahanApiUrl);
                        // dd($tambahan);
                        $kgPoTambahan = $tambahan['ttl_keb_potambahan'] ?? 0;
                        log_message('info', 'inii :' . $kgPoTambahan);

                        // Tambahkan kgPoTambahan ke total kebutuhan
                        $totalRequirement += $kgPoTambahan;
                        $dataList[$key]['ttl_kebutuhan_bb'] = $totalRequirement;
                    }

                    // penerimaan benang
                    $pengirimanApiUrl = 'http://172.23.44.14/MaterialSystem/public/api/getTotalPengiriman?area=' . $area . '&no_model='
                        . $order['no_model'] . '&item_type=' . urlencode($order['item_type']) . '&kode_warna=' . urlencode($order['kode_warna']);
                    $pengiriman = $this->fetchApiData($pengirimanApiUrl);
                    log_message('info', "[API CHECK] URL=$pengirimanApiUrl");
                    log_message('info', "[API RESULT] " . json_encode($pengiriman));
                    $dataList[$key]['ttl_pengiriman'] = $pengiriman['kgs_out'] ?? 0;

                    // retur
                    $returApiUrl = 'http://172.23.44.14/MaterialSystem/public/api/getTotalRetur?area=' . $area . '&no_model='
                        . $order['no_model'] . '&item_type=' . urlencode($order['item_type']) . '&kode_warna=' . urlencode($order['kode_warna']);
                    $retur = $this->fetchApiData($returApiUrl);
                    $dataList[$key]['ttl_retur'] = $retur['kgs_retur'] ?? 0;

                    // Hitung sisa jatah
                    $dataList[$key]['sisa_jatah'] = $dataList[$key]['ttl_kebutuhan_bb'] - $dataList[$key]['ttl_pengiriman'] + $dataList[$key]['ttl_retur'];
                }
            }
        }

        // ambil data libur hari kedepan untuk menentukan jadwal pemesanan
        $today = date('Y-m-d'); // ambil data hari ini
        // $today = '2025-10-25'; // ambil data hari ini
        $dataLibur = $this->liburModel->getDataLiburForPemesanan($today);

        // Ambil data tanggal libur menjadi array sederhana
        $liburDates = array_column($dataLibur, 'tanggal'); // Ambil hanya kolom 'tanggal'

        $day   = date('l', strtotime($today)); // ambil nama hari dari $today

        function getNextNonHoliday($date, $liburDates)
        {
            while (in_array($date, $liburDates)) {
                $date = date('Y-m-d', strtotime($date . ' +1 day'));
            }
            return $date;
        }

        // get range berdasarkan hari
        $masterRangeApiUrl = 'http://172.23.44.14/MaterialSystem/public/api/getMasterRangePemesanan?day=' .    ($day) . '&area=' . urlencode($area);
        $masterRangePemesanan = $this->fetchApiData($masterRangeApiUrl);

        // Simpan hasil
        $result = [
            'benang'  => [],
            'nylon'   => [],
            'spandex' => [],
            'karet'   => [],
        ];

        // Jam awal
        $startTime = "08:30:00";
        // Helper untuk generate jadwal
        function generateRangeDates($today, $range, $liburDates, $startTime, $initialOffset)
        {
            $result = [];
            $currentDate = $today;

            // cek apakah today hari Minggu
            $isSunday = (date('l', strtotime($today)) === 'Sunday');

            for ($i = 1; $i <= $range; $i++) {
                // kalau loop pertama pakai initialOffset (bisa 1 atau 2)
                // setelah itu selalu pakai offset 1
                $offset = ($i === 1) ? $initialOffset : 1;
                for ($j = 0; $j < $offset; $j++) {
                    $currentDate = date('Y-m-d', strtotime($currentDate . " +1 day"));
                    $currentDate = getNextNonHoliday($currentDate, $liburDates);
                }

                // kalau hari ini Minggu â†’ pakai 00:00:00
                if ($isSunday) {
                    $time = "00:00:00";
                } else {
                    // waktu pemesanan bertambah 30 menit tiap looping
                    $time = date("H:i:s", strtotime($startTime . " +" . ($i - 1) * 30 . " minutes"));
                }

                $result[] = [
                    'tgl_pakai'   => $currentDate,
                    'batas_waktu' => $time
                ];
            }
            return $result;
        }

        // Spandex & Karet â†’ cek apakah hari ini Jumat atau Sabtu
        if ($day === 'Sunday') {
            $initialOffsetBenang  = 0;
            $initialOffsetNylon   = 0;
            $initialOffsetSpandex = 0;
            $initialOffsetKaret   = 0;
        } else {
            // JANGAN DI HAPUS
            // apakah hari ini Jumat atau Sabtu 
            // $initialOffsetBenang  = ($day === 'Saturday') ? 2 : 1;
            // $initialOffsetNylon   = ($day === 'Saturday') ? 2 : 1;
            // $initialOffsetSpandex = ($day === 'Friday' || $day === 'Saturday') ? 3 : 2;
            // $initialOffsetKaret   = ($day === 'Friday' || $day === 'Saturday') ? 3 : 2;
            $initialOffsetBenang  = 1;
            $initialOffsetNylon   = 1;
            $initialOffsetSpandex = ($day === 'Friday') ? 3 : (($day === 'Saturday') ? 0 : 2); // untuk libur hari minggu
            $initialOffsetKaret   = ($day === 'Friday') ? 3 : (($day === 'Saturday') ? 0 : 2); // untuk libur hari minggu
        }

        $result['benang']  = generateRangeDates($today, (int)$masterRangePemesanan['range_benang'], $liburDates, $startTime, $initialOffsetBenang);
        $result['nylon']   = generateRangeDates($today, (int)$masterRangePemesanan['range_nylon'], $liburDates, $startTime, $initialOffsetNylon);
        $result['spandex'] = generateRangeDates($today, (int)$masterRangePemesanan['range_spandex'], $liburDates, $startTime, $initialOffsetSpandex);
        $result['karet']   = generateRangeDates($today, (int)$masterRangePemesanan['range_karet'], $liburDates, $startTime, $initialOffsetKaret);

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
            'filter_tgl' => $tglPakai,
            'filter_pdk' => $pdk,
            'result' => $result,
            'message' => $message,
        ];

        return view(session()->get('role') . '/Material/listPemesanan_coba', $data);
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
                    // $html .= '<option value="' . $twoDays . '">' . $twoDays . '</option>';
                    $html .= '</select>';
                } elseif ($jenis == 'NYLON') {
                    $html .= '<input type="date" id="tanggal_pakai" name="tanggal_pakai" class="form-control" value="' . $tomorrow . '" required readonly>';
                } elseif (in_array($jenis, ['SPANDEX', 'KARET'])) {
                    $html .= '<input type="date" id="tanggal_pakai" name="tanggal_pakai" class="form-control" value="' . $threeDays . '" required readonly>';
                }
                break;

            case 'Saturday':
                if ($jenis == 'BENANG') {
                    $html .= '<input type="date" id="tanggal_pakai" name="tanggal_pakai" class="form-control" value="' . $tomorrow . '" required readonly>';
                } elseif ($jenis == 'NYLON') {
                    $html .= '<select id="tanggal_pakai" name="tanggal_pakai" class="form-select" required>';
                    $html .= '<option value="' . $tomorrow . '">' . $tomorrow . '</option>';
                    // $html .= '<option value="' . $twoDays . '">' . $twoDays . '</option>';
                    $html .= '</select>';
                } elseif (in_array($jenis, ['SPANDEX', 'KARET'])) {
                    // $html .= '<input type="date" id="tanggal_pakai" name="tanggal_pakai" class="form-control" value="' . $threeDays . '" required readonly>';
                    $html .= '<input type="date" id="tanggal_pakai" name="tanggal_pakai" class="form-control" value="000-00-00" required readonly>';
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
        $tanggal_pakai = $this->request->getPost('tgl_pakai');
        $alasan = $this->request->getPost('alasan');

        // Jika search ada, panggil API eksternal dengan query parameter 'search'
        $apiUrl = 'http://172.23.44.14/MaterialSystem/public/api/requestAdditionalTime/' . $area . '?jenis=' . urlencode($jenis) . '&tanggal_pakai=' . urlencode($tanggal_pakai) . '&alasan=' . urlencode($alasan);

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
                    // $pph = ((($bruto + ($bsMesin / $gw)) * $comp * $gw) / 100) / 1000;
                    $pph = ((($bruto * $comp * $gw) / 100) / 1000) + ($bsMesin / 1000 * $comp / 100);
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
                    // $pph = ((($bruto + ($bsMesin / $gw)) * $comp * $gw) / 100) / 1000;
                    $pph = ((($bruto * $comp * $gw) / 100) / 1000) + ($bsMesin / 1000 * $comp / 100);
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
                        // $pph = ((($bruto + ($bs_mesin / $gw)) * $comp * $gw) / 100) / 1000;
                        $pph = ((($bruto * $comp * $gw) / 100) / 1000) + ($bs_mesin / 1000 * $comp / 100);
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
            'title'      => 'Gudang Benang',
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
        log_message('info', 'material data : ' . json_encode($materialData));

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

        // Ambil SISA dan QTY PO PLUS per style_size
        $qtyOrderList = [];
        $sisaOrderList = [];
        $poPlusList = [];
        foreach ($styleSize as $style) {
            $sisa = $this->ApsPerstyleModel->getSisaPerSize($area, $noModel, [$style]);
            $qtyPcs = is_array($sisa) ? $sisa['qty'] ?? 0 : ($sisa->qty ?? 0);
            $qtyOrderList[$style] = (float)$qtyPcs;
            $sisaPcs = is_array($sisa) ? $sisa['sisa'] ?? 0 : ($sisa->sisa ?? 0);
            $sisaOrderList[$style] = (float)$sisaPcs;
            $poPlusPcs = is_array($sisa) ? $sisa['po_plus'] ?? 0 : ($sisa->po_plus ?? 0);
            $poPlusList[$style] = (float)$poPlusPcs;
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
            if (!is_array($idaps) || empty($idaps)) {
                $bsSettingList[$style] = 0;
                continue;
            }
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
            'qty_order' => $qtyOrderList,
            'sisa_order' => $sisaOrderList,
            'bs_mesin' => $bsMesinList,
            'bs_setting' => $bsSettingList,
            'bruto' => $brutoList,
            'plusPck' => $poPlusList,
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
                'ttl_terima_kg'     => (float) ($item['terima_kg'] ?? 0),
                'ttl_sisa_jatah'    => (float) ($item['sisa_jatah'] ?? 0),
                'ttl_sisa_bb_dimc'  => (float) ($item['sisa_bb_mc'] ?? 0),
                'sisa_order_pcs'    => (float) ($item['sisa_order_pcs'] ?? 0),
                'bs_mesin_kg'       => (float) ($item['bs_mesin_kg'] ?? 0),
                'bs_st_pcs'         => (float) ($item['bs_st_pcs'] ?? 0),
                'poplus_mc_kg'      => (float) ($item['poplus_mc_kg'] ?? 0),
                'poplus_mc_cns'     => (float) ($item['poplus_mc_cns'] ?? 0),
                'plus_pck_pcs'      => (float) ($item['plus_pck_pcs'] ?? 0),
                'plus_pck_kg'       => (float) ($item['plus_pck_kg'] ?? 0),
                'plus_pck_cns'      => (float) ($item['plus_pck_cns'] ?? 0),
                // 'lebih_pakai_kg'    => (float) ($item['lebih_pakai_kg'] ?? 0),
                'ttl_tambahan_kg'   => (float) ($item['total_kg_po'] ?? 0),
                'ttl_tambahan_cns'  => (float) ($item['total_cns_po'] ?? 0),
                'delivery_po_plus'  => $item['delivery_po_plus'] ?? '',
                'keterangan'        => $item['keterangan'] ?? '',
                'loss_aktual'       => (float) ($item['loss_aktual'] ?? 0),
                'loss_tambahan'     => (float) ($item['loss_tambahan'] ?? 0),
                'admin'             => session()->get('username'),
                'created_at'        => date('Y-m-d H:i:s'),
            ];
        }, $json);

        // Log isi items ke log file
        // log_message('debug', 'ITEMS untuk dikirim ke API: ' . json_encode($items));

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

        // Jika API balas error karena data duplikat
        if (isset($result['status']) && $result['status'] === 'error') {
            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'status'  => 'error',
                    'message' => $result['message'] // kirim balik ke AJAX
                ]);
        }

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

        $dataPoTambahan = $result['dataPoTambahan'];
        $dataRetur = $result['dataRetur'];

        $qtyOrderList = [];

        foreach ($dataPoTambahan as $item) {
            $style = $item['style_size'];
            $noModel = $item['no_model'];  // ambil langsung dari API
            $area = $item['admin'];        // atau sesuai kolom factory di DB

            // Ambil qty dari DB lokal
            $qty = $this->ApsPerstyleModel->getSisaPerSize($area, $noModel, [$style]);
            $qtyOrderList[$style] = is_array($qty) ? ($qty['qty'] ?? 0) : ($qty->qty ?? 0);
        }

        // Gabungkan ke response
        foreach ($dataPoTambahan as $i => $row) {
            $style = $row['style_size'];
            $qty_order = isset($qtyOrderList[$style]) ? (float)$qtyOrderList[$style] : 0;
            $composition = (float)$row['composition'] ?? 0;
            $gw = (float)$row['gw'] ?? 0;
            $loss = (float)$row['loss'] ?? 0;

            $dataPoTambahan[$i]['qty_order'] = $qty_order;
            $dataPoTambahan[$i]['kg_po'] = ($qty_order * $composition * $gw / 100 / 1000) * (1 + ($loss / 100));
        }

        return $this->response->setStatusCode($httpCode)->setJSON($dataPoTambahan);
    }
    public function filterTglPakai($area)
    {
        $area = $this->request->getPost('area') ?? $area;
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
        $areas = $this->areaModel->getArea();
        // Filter agar 'name' yang mengandung 'Gedung' tidak ikut
        $filteredArea = array_filter($areas, function ($item) {
            return stripos($item['name'], 'Gedung') === false; // Cek jika 'Gedung' tidak ada di 'name'
        });

        // Ambil hanya field 'name'
        $result = array_column($filteredArea, 'name');

        // $tgl_pakai = '2025-09-06';
        $tgl_pakai = $this->request->getGet('tgl_pakai') ?? date('Y-m-d');
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

        $dataList = fetchApiData("http://172.23.44.14/MaterialSystem/public/api/listReportPemesanan/" . $area . "/" . urlencode($tgl_pakai));
        if (!is_array($dataList)) {
            die('Error: Invalid response format for listPemesanan API.');
        }

        foreach ($dataList as $key => $order) {
            $dataList[$key]['ttl_kebutuhan_bb'] = 0;
            if (isset($order['no_model'], $order['item_type'], $order['kode_warna'])) {
                $styleApiUrl = 'http://172.23.44.14/MaterialSystem/public/api/getStyleSizeByBb?no_model='
                    . $order['no_model'] . '&item_type=' . urlencode($order['item_type']) . '&kode_warna=' . urlencode($order['kode_warna']) . '&warna=' . urlencode($order['color']);
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
            'areas' => $result,
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
        $area = $this->request->getGet('filter_area') ?? $area;
        $areas = $this->areaModel->getArea();
        // Filter agar 'name' yang mengandung 'Gedung' tidak ikut
        $filteredArea = array_filter($areas, function ($item) {
            return stripos($item['name'], 'Gedung') === false; // Cek jika 'Gedung' tidak ada di 'name'
        });

        // Ambil hanya field 'name'
        $result = array_column($filteredArea, 'name');
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
                . '&kode_warna=' . urlencode($pemesanan['kode_warna'])
                . '&warna=' . urlencode($pemesanan['color']);

            $styleResponse = file_get_contents($urlStyle);
            $getStyle     = json_decode($styleResponse, true);

            $ttlKeb = 0;
            $ttlQty = 0;

            foreach ($getStyle as $i => $data) {
                // Ambil qty
                $qtyData = $this->ApsPerstyleModel->getQtyOrder($noModel, $data['style_size'], $area);
                $qty         = (intval($qtyData['qty']) ?? 0);

                // // Ambil kg po tambahan
                // $PoPlus = 'http://172.23.44.14/MaterialSystem/public/api/getKgPoTambahan?no_model=' . $pemesanan['no_model']
                //     . '&item_type=' . urlencode($pemesanan['item_type'])
                //     . '&area=' . $area;

                // $poPlusResponse = file_get_contents($PoPlus);
                // $getPoPlus     = json_decode($poPlusResponse, true);


                // $kgPoTambahan = floatval($getPoPlus['ttl_keb_potambahan'] ?? 0);

                if ($qty >= 0) {
                    if (isset($pemesanan['item_type']) && stripos($pemesanan['item_type'], 'JHT') !== false) {
                        $kebutuhan = $data['kgs'] ?? 0;
                    } else {
                        // $kebutuhan = (($qty * $data['gw'] * $data['composition'] / 100 / 1000) * (1 + ($data['loss'] / 100))) + $kgPoTambahan;
                        $kebutuhan = (($qty * $data['gw'] * $data['composition'] / 100 / 1000) * (1 + ($data['loss'] / 100)));
                    }
                    $pemesanan['ttl_keb'] = $ttlKeb;
                }

                $ttlKeb += $kebutuhan;
                $ttlQty += $qty;
            }
            // Ambil kg po tambahan
            $PoPlus = 'http://172.23.44.14/MaterialSystem/public/api/getKgTambahan?no_model=' . $pemesanan['no_model']
                . '&item_type=' . urlencode($pemesanan['item_type'])
                . '&kode_warna=' . urlencode($pemesanan['kode_warna'])
                . '&area=' . $area;

            $getPoPlus = $this->fetchApiData($PoPlus);
            // dd($getPoPlus);
            $kgPoTambahan = floatval($getPoPlus['ttl_keb_potambahan'] ?? 0);


            $ttlKeb += $kgPoTambahan;
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
                'ttl_jl_mc'          => (int)($pemesanan['ttl_jl_mc'] ?? 0),
                'ttl_kg'             => (float)($pemesanan['ttl_kg'] ?? 0),   // â† JANGAN number_format di sini
                'po_tambahan'        => (int)($pemesanan['po_tambahan'] ?? 0),
                'ttl_keb'            => (float)$ttlKeb,                       // â† hasil hitung, mentah
                'kg_out'             => (float)($pemesanan['kgs_out'] ?? 0),  // â† mentah
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
                . '&kode_warna=' . urlencode($retur['kode_warna'])
                . '&warna=' . urlencode($retur['warna']);

            $styleResponse = file_get_contents($urlStyle);
            $getStyle      = json_decode($styleResponse, true);

            $ttlKeb = 0;
            $ttlQty = 0;

            foreach ($getStyle as $i => $data) {
                // Ambil qty
                $qtyData = $this->ApsPerstyleModel->getQtyOrder($noModel, $data['style_size'], $area);
                $qty     = (intval($qtyData['qty']) ?? 0);

                // // Ambil kg po tambahan
                // $PoPlus = 'http://172.23.44.14/MaterialSystem/public/api/getKgPoTambahan?no_model=' . $retur['no_model']
                //     . '&item_type=' . urlencode($retur['item_type'])
                //     . '&area=' . $area;

                // $poPlusResponse = file_get_contents($PoPlus);
                // $getPoPlus     = json_decode($poPlusResponse, true);


                // $kgPoTambahan = floatval(
                //     $getPoPlus['ttl_keb_potambahan'] ?? 0
                // );

                if ($qty >= 0) {
                    if (isset($pemesanan['item_type']) && stripos($pemesanan['item_type'], 'JHT') !== false) {
                        $kebutuhan = $data['kgs'] ?? 0;
                    } else {
                        // $kebutuhan = (($qty * $data['gw'] * $data['composition'] / 100 / 1000) * (1 + ($data['loss'] / 100))) + $kgPoTambahan;
                        $kebutuhan = (($qty * $data['gw'] * $data['composition'] / 100 / 1000) * (1 + ($data['loss'] / 100)));
                    }
                    $retur['ttl_keb'] = $ttlKeb;
                }
                $ttlKeb += $kebutuhan;
                $ttlQty += $qty;
            }

            // // Ambil kg po tambahan
            $PoPlus = 'http://172.23.44.14/MaterialSystem/public/api/getKgTambahan?no_model=' . $retur['no_model']
                . '&item_type=' . urlencode($retur['item_type'])
                . '&kode_warna=' . urlencode($retur['kode_warna'])
                . '&area=' . $area;

            $poPlusResponse = file_get_contents($PoPlus);
            $getPoPlus     = json_decode($poPlusResponse, true);

            $kgPoTambahan = floatval($getPoPlus['ttl_keb_potambahan'] ?? 0);

            $ttlKeb += $kgPoTambahan;
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
                'ttl_keb'            => (float)$ttlKeb,                        // â† mentah
                'kg_out'             => 0.0,                                   // â† angka 0
                'lot_out'            => null,
                'tgl_retur'          => $retur['tgl_retur'],
                'kgs_retur'          => (float)($retur['kgs_retur'] ?? 0),     // â† mentah
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
            'areas' => $result,
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
        $totalAllDelivery = [];

        if ($noModel) {
            //
            // 1) Ambil headerRow & hitung totalQty per delivery (untuk $order)
            //
            $order = $this->ApsPerstyleModel->getQtyArea($noModel) ?: [];

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
                $sisa = $ord['sisa'];

                if (!isset($groupedOrders[$style_size][$delivery][$area])) {
                    $groupedOrders[$style_size][$delivery][$area] = [
                        'qty' => 0,
                        'sisa' => 0,
                    ];
                }

                $groupedOrders[$style_size][$delivery][$area]['qty'] += $qty;
                $groupedOrders[$style_size][$delivery][$area]['sisa'] += $sisa;
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
                    foreach ($areaData as $area => $values) {
                        $qty = $values['qty'];
                        $sisa = $values['sisa'];

                        $jatah = ($qty * $comp * $gw / 100 / 1000) * (1 + ($loss / 100));
                        $sisaVal = ($sisa * $comp * $gw / 100 / 1000) * (1 + ($loss / 100));

                        if (!isset($result[$delivery][$item_type][$kode_warna])) {
                            $result[$delivery][$item_type][$kode_warna] = [];
                            foreach ($areas as $a) {
                                $result[$delivery][$item_type][$kode_warna][$a] = ['jatah' => 0, 'sisa' => 0];
                            }
                            $result[$delivery][$item_type][$kode_warna]['Grand Total Jatah'] = 0;
                            $result[$delivery][$item_type][$kode_warna]['Grand Total Sisa'] = 0;
                        }

                        $result[$delivery][$item_type][$kode_warna][$area]['jatah'] += $jatah;
                        $result[$delivery][$item_type][$kode_warna][$area]['sisa'] += $sisaVal;

                        // Total hanya berdasarkan jatah (tanpa sisa), tapi bisa ditambah sisa jika perlu
                        $result[$delivery][$item_type][$kode_warna]['Grand Total Jatah'] += $jatah;
                        $result[$delivery][$item_type][$kode_warna]['Grand Total Sisa'] += $sisaVal;
                    }
                }
            }

            // Akumulasi total semua delivery
            foreach ($result as $delivery => $itemTypes) {
                foreach ($itemTypes as $item_type => $colors) {
                    foreach ($colors as $kode_warna => $areaData) {
                        if (!isset($totalAllDelivery[$item_type][$kode_warna])) {
                            foreach ($areas as $a) {
                                $totalAllDelivery[$item_type][$kode_warna][$a] = ['jatah' => 0, 'sisa' => 0];
                            }
                            $totalAllDelivery[$item_type][$kode_warna]['Grand Total Jatah'] = 0;
                            $totalAllDelivery[$item_type][$kode_warna]['Grand Total Sisa'] = 0;
                        }

                        foreach ($areas as $area) {
                            $totalAllDelivery[$item_type][$kode_warna][$area]['jatah'] += $areaData[$area]['jatah'] ?? 0;
                            $totalAllDelivery[$item_type][$kode_warna][$area]['sisa'] += $areaData[$area]['sisa'] ?? 0;
                        }

                        $totalAllDelivery[$item_type][$kode_warna]['Grand Total Jatah'] += $areaData['Grand Total Jatah'] ?? 0;
                        $totalAllDelivery[$item_type][$kode_warna]['Grand Total Sisa'] += $areaData['Grand Total Sisa'] ?? 0;
                    }
                }
            }
            //
            $totalPo = $this->ApsPerstyleModel->totalPo($noModel)['totalPo'] ?? 0;
        }

        // Render full pageâ€”AJAX akan mengambil ulang #table-container saja
        return view(session()->get('role') . '/Material/jatahBahanBaku', [
            'role'            => session()->get('role'),
            'title'           => 'Jatah Bahan Baku',
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
            'totalAllDelivery'  => $totalAllDelivery,
        ]);
    }
    public function reportDatangBenang()
    {
        $data = [
            'role'            => session()->get('role'),
            'title'           => 'Gudang Benang',
            'active1'         => '',
            'active2'         => '',
            'active3'         => '',
            'active4'         => '',
            'active5'         => '',
            'active6'         => '',
            'active7'         => '',
        ];
        return view(session()->get('role') . '/Material/report-datang-benang', $data);
    }
    public function filterDatangBenang()
    {
        $key = $this->request->getGet('key');
        $tanggalAwal = $this->request->getGet('tanggal_awal');
        $tanggalAkhir = $this->request->getGet('tanggal_akhir');
        $poPlus = $this->request->getGet('po_plus');

        $apiUrl = 'http://172.23.44.14/MaterialSystem/public/api/filterDatangBenang?key=' . urlencode($key) . '&tanggal_awal=' . $tanggalAwal . '&tanggal_akhir=' . $tanggalAkhir . '&po_plus=' . $poPlus;
        $material = @file_get_contents($apiUrl);

        $models = [];
        if ($material !== FALSE) {
            $models = json_decode($material, true);
        }

        return $this->response->setJSON($models);
    }
    public function reportPoBenang()
    {
        $data = [
            'role'            => session()->get('role'),
            'title'           => 'Gudang Benang',
            'active1'         => '',
            'active2'         => '',
            'active3'         => '',
            'active4'         => '',
            'active5'         => '',
            'active6'         => '',
            'active7'         => '',
        ];
        return view(session()->get('role') . '/Material/report-po-benang', $data);
    }
    public function filterPoBenang()
    {
        $key = $this->request->getGet('key');

        $apiUrl = 'http://172.23.44.14/MaterialSystem/public/api/filterPoBenang?key=' . urlencode($key);
        $material = @file_get_contents($apiUrl);

        $models = [];
        if ($material !== FALSE) {
            $models = json_decode($material, true);
        }
        // $data = $this->openPoModel->getFilterPoBenang($key);

        return $this->response->setJSON($models);
    }
    public function reportPengiriman()
    {
        $data = [
            'role'            => session()->get('role'),
            'title'           => 'Gudang Benang',
            'active1'         => '',
            'active2'         => '',
            'active3'         => '',
            'active4'         => '',
            'active5'         => '',
            'active6'         => '',
            'active7'         => '',
        ];
        return view(session()->get('role') . '/Material/report-pengiriman', $data);
    }
    public function filterPengiriman()
    {
        $key = $this->request->getGet('key');
        $tanggalAwal = $this->request->getGet('tanggal_awal');
        $tanggalAkhir = $this->request->getGet('tanggal_akhir');

        $apiUrl = 'http://172.23.44.14/MaterialSystem/public/api/filterPengiriman?key=' . urlencode($key) . '&tanggal_awal=' . urlencode($tanggalAwal) . '&tanggal_akhir=' . urlencode($tanggalAkhir);
        $material = @file_get_contents($apiUrl);

        if ($material !== FALSE) {
            $models = json_decode($material, true);
        }

        // dd($data);
        return $this->response->setJSON($models);
    }
    public function reportGlobal()
    {
        $data = [
            'role'            => session()->get('role'),
            'title'           => 'Gudang Benang',
            'active1'         => '',
            'active2'         => '',
            'active3'         => '',
            'active4'         => '',
            'active5'         => '',
            'active6'         => '',
            'active7'         => '',
        ];
        return view(session()->get('role') . '/Material/report-global', $data);
    }
    public function filterReportGlobal()
    {
        $key = $this->request->getGet('key');
        $jenis = $this->request->getGet('jenis');
        log_message('debug', 'Received key: ' . $key);  // Log key yang diterima
        if (empty($key)) {
            return $this->response->setJSON(['error' => 'Key is missing']);
        }

        $apiUrl = 'http://172.23.44.14/MaterialSystem/public/api/filterReportGlobal?key=' . urlencode($key) . '&jenis=' . urlencode($jenis);
        $material = @file_get_contents($apiUrl);

        if ($material !== FALSE) {
            $models = json_decode($material, true);
        }

        // Log data yang diterima dari model
        log_message('debug', 'Query result: ' . print_r($models, true));

        if (empty($models)) {
            return $this->response->setJSON(['error' => 'No data found']);
        }

        return $this->response->setJSON($models);
    }
    public function reportGlobalStockBenang()
    {
        $data = [
            'role'            => session()->get('role'),
            'title'           => 'Gudang Benang',
            'active1'         => '',
            'active2'         => '',
            'active3'         => '',
            'active4'         => '',
            'active5'         => '',
            'active6'         => '',
            'active7'         => '',
        ];
        return view(session()->get('role') . '/Material/report-global-benang', $data);
    }

    public function reportGlobalNylon()
    {
        $data = [
            'role'            => session()->get('role'),
            'title'           => 'Gudang Benang',
            'active1'         => '',
            'active2'         => '',
            'active3'         => '',
            'active4'         => '',
            'active5'         => '',
            'active6'         => '',
            'active7'         => '',
        ];
        return view(session()->get('role') . '/Material/report-global-nylon', $data);
    }

    public function reportSisaPakaiBenang()
    {
        $data = [
            'role'            => session()->get('role'),
            'title'           => 'Gudang Benang',
            'active1'         => '',
            'active2'         => '',
            'active3'         => '',
            'active4'         => '',
            'active5'         => '',
            'active6'         => '',
            'active7'         => '',
        ];
        return view(session()->get('role') . '/Material/report-sisa-pakai-benang', $data);
    }

    public function filterSisaPakai()
    {
        $delivery = $this->request->getGet('delivery');
        $noModel = $this->request->getGet('no_model');
        $kodeWarna = $this->request->getGet('kode_warna');
        $jenis = $this->request->getGet('jenis');

        $bulanMap = [
            'Januari' => 1,
            'Februari' => 2,
            'Maret' => 3,
            'April' => 4,
            'Mei' => 5,
            'Juni' => 6,
            'Juli' => 7,
            'Agustus' => 8,
            'September' => 9,
            'Oktober' => 10,
            'November' => 11,
            'Desember' => 12
        ];
        $bulan = $bulanMap[$delivery] ?? null;

        $apiUrl = 'http://172.23.44.14/MaterialSystem/public/api/filterSisaPakai?bulan=' . urlencode($bulan) . '&no_model=' . urlencode($noModel) . '&kode_warna=' . urlencode($kodeWarna) . '&jenis=' . urlencode($jenis);
        $material = @file_get_contents($apiUrl);

        if ($material !== FALSE) {
            $data = json_decode($material, true);
        }

        return $this->response->setJSON($data);
    }

    public function reportSisaPakaiNylon()
    {
        $data = [
            'role'            => session()->get('role'),
            'title'           => 'Gudang Benang',
            'active1'         => '',
            'active2'         => '',
            'active3'         => '',
            'active4'         => '',
            'active5'         => '',
            'active6'         => '',
            'active7'         => '',
        ];
        return view(session()->get('role') . '/Material/report-sisa-pakai-nylon', $data);
    }

    public function reportSisaPakaiSpandex()
    {
        $data = [
            'role'            => session()->get('role'),
            'title'           => 'Gudang Benang',
            'active1'         => '',
            'active2'         => '',
            'active3'         => '',
            'active4'         => '',
            'active5'         => '',
            'active6'         => '',
            'active7'         => '',
        ];
        return view(session()->get('role') . '/Material/report-sisa-pakai-spandex', $data);
    }

    public function reportSisaPakaiKaret()
    {
        $data = [
            'role'            => session()->get('role'),
            'title'           => 'Gudang Benang',
            'active1'         => '',
            'active2'         => '',
            'active3'         => '',
            'active4'         => '',
            'active5'         => '',
            'active6'         => '',
            'active7'         => '',
        ];
        return view(session()->get('role') . '/Material/report-sisa-pakai-karet', $data);
    }

    public function historyPindahOrder()
    {
        $noModel   = $this->request->getGet('model')     ?? '';
        $kodeWarna = $this->request->getGet('kode_warna') ?? '';

        // 1) Ambil data
        $apiUrl = 'http://172.23.44.14/MaterialSystem/public/api/historyPindahOrder?model=' . urlencode($noModel) . '&kode_warna=' . urlencode($kodeWarna);
        $material = @file_get_contents($apiUrl);

        if ($material !== FALSE) {
            $dataPindah = json_decode($material, true);
        }

        // 2) Loop dan merge API result
        foreach ($dataPindah as &$row) {
            try {
                $delivery = $this->ApsPerstyleModel->getDeliveryAwalAkhir($row['no_model_new']);
                $row['delivery_awal']  = $delivery['delivery_awal']  ?? '-';
                $row['delivery_akhir'] = $delivery['delivery_akhir'] ?? '-';
            } catch (\Exception $e) {
                $row['delivery_awal']  = '-';
                $row['delivery_akhir'] = '-';
            }
        }
        unset($row);

        // 4) Response
        if ($this->request->isAJAX()) {
            return $this->response->setJSON($dataPindah);
        }

        return view(session()->get('role')  . '/Material/history-pindah-order', [
            'role'            => session()->get('role'),
            'title'           => 'Gudang Benang',
            'active1'         => '',
            'active2'         => '',
            'active3'         => '',
            'active4'         => '',
            'active5'         => '',
            'active6'         => '',
            'active7'         => '',
            'history'         => $dataPindah,
        ]);
    }

    public function reportSisaDatangBenang()
    {
        $delivery = $this->request->getGet('delivery');
        $noModel = $this->request->getGet('no_model');
        $kodeWarna = $this->request->getGet('kode_warna');
        $bulanMap = [
            'Januari' => 1,
            'Februari' => 2,
            'Maret' => 3,
            'April' => 4,
            'Mei' => 5,
            'Juni' => 6,
            'Juli' => 7,
            'Agustus' => 8,
            'September' => 9,
            'Oktober' => 10,
            'November' => 11,
            'Desember' => 12
        ];
        $bulan = $bulanMap[$delivery] ?? null;

        $apiUrl = 'http://172.23.44.14/MaterialSystem/public/api/reportSisaDatangBenang?delivery=' . urlencode($bulan) . '&no_model=' . urlencode($noModel) . '&kode_warna=' . urlencode($kodeWarna);
        $material = @file_get_contents($apiUrl);

        if ($material !== FALSE) {
            $getFilterData = json_decode($material, true);
        }

        if ($this->request->isAJAX()) {
            // set header JSON dan langsung echo data
            return $this->response
                ->setStatusCode(200)
                ->setJSON($getFilterData);
        }

        $data = [
            'role'            => session()->get('role'),
            'title'           => 'Gudang Benang',
            'active1'         => '',
            'active2'         => '',
            'active3'         => '',
            'active4'         => '',
            'active5'         => '',
            'active6'         => '',
            'active7'         => '',
            'getFilterData'   => $getFilterData
        ];

        return view(session()->get('role') . '/Material/report-sisa-datang-benang', $data);
    }

    public function reportSisaDatangNylon()
    {
        $delivery = $this->request->getGet('delivery');
        $noModel = $this->request->getGet('no_model');
        $kodeWarna = $this->request->getGet('kode_warna');
        $bulanMap = [
            'Januari' => 1,
            'Februari' => 2,
            'Maret' => 3,
            'April' => 4,
            'Mei' => 5,
            'Juni' => 6,
            'Juli' => 7,
            'Agustus' => 8,
            'September' => 9,
            'Oktober' => 10,
            'November' => 11,
            'Desember' => 12
        ];
        $bulan = $bulanMap[$delivery] ?? null;
        $apiUrl = 'http://172.23.44.14/MaterialSystem/public/api/reportSisaDatangNylon?delivery=' . urlencode($bulan) . '&no_model=' . urlencode($noModel) . '&kode_warna=' . urlencode($kodeWarna);
        $material = @file_get_contents($apiUrl);

        if ($material !== FALSE) {
            $getFilterData = json_decode($material, true);
        }

        if ($this->request->isAJAX()) {
            // set header JSON dan langsung echo data
            return $this->response
                ->setStatusCode(200)
                ->setJSON($getFilterData);
        }

        $data = [
            'role'            => session()->get('role'),
            'title'           => 'Gudang Benang',
            'active1'         => '',
            'active2'         => '',
            'active3'         => '',
            'active4'         => '',
            'active5'         => '',
            'active6'         => '',
            'active7'         => '',
            'getFilterData'   => $getFilterData
        ];

        return view(session()->get('role') . '/Material/report-sisa-datang-nylon', $data);
    }

    public function reportSisaDatangSpandex()
    {
        $delivery = $this->request->getGet('delivery');
        $noModel = $this->request->getGet('no_model');
        $kodeWarna = $this->request->getGet('kode_warna');
        $bulanMap = [
            'Januari' => 1,
            'Februari' => 2,
            'Maret' => 3,
            'April' => 4,
            'Mei' => 5,
            'Juni' => 6,
            'Juli' => 7,
            'Agustus' => 8,
            'September' => 9,
            'Oktober' => 10,
            'November' => 11,
            'Desember' => 12
        ];
        $bulan = $bulanMap[$delivery] ?? null;

        $apiUrl = 'http://172.23.44.14/MaterialSystem/public/api/reportSisaDatangSpandex?delivery=' . urlencode($bulan) . '&no_model=' . urlencode($noModel) . '&kode_warna=' . urlencode($kodeWarna);
        $material = @file_get_contents($apiUrl);

        if ($material !== FALSE) {
            $getFilterData = json_decode($material, true);
        }

        if ($this->request->isAJAX()) {
            // set header JSON dan langsung echo data
            return $this->response
                ->setStatusCode(200)
                ->setJSON($getFilterData);
        }

        $data = [
            'role'            => session()->get('role'),
            'title'           => 'Gudang Benang',
            'active1'         => '',
            'active2'         => '',
            'active3'         => '',
            'active4'         => '',
            'active5'         => '',
            'active6'         => '',
            'active7'         => '',
            'getFilterData'   => $getFilterData
        ];

        return view(session()->get('role') . '/Material/report-sisa-datang-spandex', $data);
    }

    public function reportSisaDatangKaret()
    {
        $delivery = $this->request->getGet('delivery');
        $noModel = $this->request->getGet('no_model');
        $kodeWarna = $this->request->getGet('kode_warna');
        $bulanMap = [
            'Januari' => 1,
            'Februari' => 2,
            'Maret' => 3,
            'April' => 4,
            'Mei' => 5,
            'Juni' => 6,
            'Juli' => 7,
            'Agustus' => 8,
            'September' => 9,
            'Oktober' => 10,
            'November' => 11,
            'Desember' => 12
        ];
        $bulan = $bulanMap[$delivery] ?? null;

        $apiUrl = 'http://172.23.44.14/MaterialSystem/public/api/reportSisaDatangKaret?delivery=' . urlencode($bulan) . '&no_model=' . urlencode($noModel) . '&kode_warna=' . urlencode($kodeWarna);
        $material = @file_get_contents($apiUrl);

        if ($material !== FALSE) {
            $getFilterData = json_decode($material, true);
        }

        if ($this->request->isAJAX()) {
            // set header JSON dan langsung echo data
            return $this->response
                ->setStatusCode(200)
                ->setJSON($getFilterData);
        }

        $data = [
            'role'            => session()->get('role'),
            'title'           => 'Gudang Benang',
            'active1'         => '',
            'active2'         => '',
            'active3'         => '',
            'active4'         => '',
            'active5'         => '',
            'active6'         => '',
            'active7'         => '',
            'getFilterData'   => $getFilterData
        ];

        return view(session()->get('role') . '/Material/report-sisa-datang-karet', $data);
    }

    public function reportBenangMingguan()
    {
        $data = [
            'role'            => session()->get('role'),
            'title'           => 'Gudang Benang',
            'active1'         => '',
            'active2'         => '',
            'active3'         => '',
            'active4'         => '',
            'active5'         => '',
            'active6'         => '',
            'active7'         => '',
        ];
        return view(session()->get('role') . '/Material/report-benang-mingguan', $data);
    }

    public function filterBenangMingguan()
    {
        $tanggalAwal = $this->request->getGet('tanggal_awal');
        $tanggalAkhir = $this->request->getGet('tanggal_akhir');

        $apiUrl = 'http://172.23.44.14/MaterialSystem/public/api/filterBenangMingguan?tanggal_awal=' . urlencode($tanggalAwal) . '&tanggal_akhir=' . urlencode($tanggalAkhir);
        $material = @file_get_contents($apiUrl);

        if ($material !== FALSE) {
            $data = json_decode($material, true);
        }
        return $this->response->setJSON($data);
    }

    public function reportBenangBulanan()
    {
        $data = [
            'role'            => session()->get('role'),
            'title'           => 'Gudang Benang',
            'active1'         => '',
            'active2'         => '',
            'active3'         => '',
            'active4'         => '',
            'active5'         => '',
            'active6'         => '',
            'active7'         => '',
        ];
        return view(session()->get('role') . '/Material/report-benang-bulanan', $data);
    }

    public function filterBenangBulanan()
    {
        $bulan = $this->request->getGet('bulan');
        if (empty($bulan) || !preg_match('/^\d{4}\-\d{2}$/', $bulan)) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON(['error' => 'Parameter â€œbulanâ€ harus dalam format YYYY-MM']);
        }

        $timestamp     = strtotime($bulan . '-01');
        $tanggalAwal   = date('Y-m-01', $timestamp);
        $tanggalAkhir  = date('Y-m-t', $timestamp);
        // $data = $this->pemasukanModel->getFilterBenang($tanggalAwal, $tanggalAkhir);
        $apiUrl = 'http://172.23.44.14/MaterialSystem/public/api/filterBenangBulanan?tanggal_awal=' . urlencode($tanggalAwal) . '&tanggal_akhir=' . urlencode($tanggalAkhir);
        $material = @file_get_contents($apiUrl);

        if ($material !== FALSE) {
            $data = json_decode($material, true);
        }

        return $this->response->setJSON($data);
    }

    public function reportKebutuhanBahanBaku()
    {
        $data = [
            'role'            => session()->get('role'),
            'title'           => 'Material System',
            'active1'         => '',
            'active2'         => '',
            'active3'         => '',
            'active4'         => '',
            'active5'         => '',
            'active6'         => '',
            'active7'         => '',
        ];
        return view(session()->get('role') . '/Material/report-kebutuhan-bb', $data);
    }
    public function stockarea($area)
    {
        $stock = $this->stockArea->getStock($area);
        $kapasitas = $this->supermarketModel->getKapasitas($area)['kapasitas'] ?? 0;
        $terisi = 0;
        if (!empty($stock)) {
            foreach ($stock as $st) {
                $terisi += (float) ($st['kgs_in_out'] ?? 0);
            }
        }
        $tgl = date('Y-m-d');

        $apiUrl = 'http://172.23.44.14/MaterialSystem/public/api/countKirimArea/' . $area . '/' . $tgl;

        // ambil data dari API
        $response = @file_get_contents($apiUrl);
        $countKirim = 0;

        // kalau responsenya valid JSON dan punya key 'count'
        if ($response !== false) {
            $json = json_decode($response, true);
            $countKirim = isset($json['count']) ? (int) $json['count'] : 0;
        }

        $sisaKapasitas = $kapasitas - $terisi;
        // dd($stock);
        $data = [
            'role' => session()->get('role'),
            'title' => 'Stock Supermarket',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'targetProd' => 0,
            'produksiBulan' => 0,
            'produksiHari' => 0,
            'area' => $area,
            'stock' => $stock,
            'kapasitas' => $kapasitas,
            'terisi' => $terisi,
            'sisaKapasitas' => $sisaKapasitas,
            'notif' => $countKirim
        ];

        return view(session()->get('role') . '/Material/stockarea', $data);
    }
    public function stocksupermarket()
    {
        $uid = session()->get('id_user');
        $area = $this->aksesModel->aksesData($uid);
        $stock = $this->stockArea->dataSupermarket($area);

        // dd($stock);
        $data = [
            'role' => session()->get('role'),
            'title' => 'Stock Supermarket',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'stock' => $stock,

        ];

        return view(session()->get('role') . '/Material/stockarea', $data);
    }
    public function inStock($area)
    {
        $tgl = date('Y-m-d');
        // $tgl = $this->request->getGet('tgl') ?? date('Y-m-d');

        $dataPengirimanGbn = [];

        if ($tgl) {
            $apiUrl = 'http://172.23.44.14/MaterialSystem/public/api/getListKirimArea/' . $area . '/' . $tgl;
            $response = @file_get_contents($apiUrl);
            $decoded = json_decode($response, true);

            // Ambil hanya bagian 'data' kalau status success
            $dataPengirimanGbn = $decoded['data'] ?? [];
        }

        $data = [
            'role' => session()->get('role'),
            'title' => 'Pemasukan Supermarket',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'targetProd' => 0,
            'produksiBulan' => 0,
            'produksiHari' => 0,
            'area' => $area,
            'tgl' => $tgl,
            'dataPengiriman' => $dataPengirimanGbn,
        ];
        return view(session()->get('role') . '/Material/inStockArea', $data);
    }
    public function outStock()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        $idStock = $this->request->getPost('idStock');
        $cnsOut  = (float) $this->request->getPost('cns');
        $kgOut   = (float) $this->request->getPost('kg');

        if (!$idStock || $cnsOut <= 0) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data tidak valid']);
        }

        // ambil stok sekarang untuk validasi
        $row = $this->stockArea->find($idStock);
        if (!$row) {
            return $this->response->setJSON(['success' => false, 'message' => 'Stock tidak ditemukan']);
        }

        // validasi tidak boleh melebihi stok
        $currentCns = (float) ($row['cns_in_out'] ?? 0);
        if ($cnsOut > $currentCns) {
            return $this->response->setJSON(['success' => false, 'message' => 'Jumlah melebihi stok saat ini']);
        }

        // lakukan update dalam transaction
        $db = \Config\Database::connect();
        $db->transStart();
        $dataInsert = [
            'id_stock_area' => $idStock,
            'kg_out' => $kgOut,
            'cns_out' => $cnsOut,
            'admin' => session()->get('username')
        ];
        $insert = $this->outArea->insert($dataInsert);
        if (!$insert) {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal Mengeluarkan barang']);
        }
        $ok = $this->stockArea->decreaseStock($idStock, $cnsOut, $kgOut);

        if ($db->transStatus() === false || !$ok) {
            $db->transRollback();
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal update database']);
        }

        $db->transComplete();

        // ambil data terbaru (sesuaikan area/filternya kalau perlu)
        // asumsikan ada method getStock(area) — sesuaikan argumen area sesuai flow aplikasi
        $area = $this->request->getPost('area') ?? null;
        $stock = $this->stockArea->getStock($area);
        $role = session()->get('role');
        // render partial view sebagai HTML
        $html = view($role . '/Material/partials/stock_result', ['stock' => $stock, 'role' => $role, 'area' => $area]);

        return $this->response->setJSON(['success' => true, 'message' => 'Berhasil', 'html' => $html]);
    }
    public function saveStock()
    {
        $cns = (float) $this->request->getPost('cns');
        $kgs = (float) $this->request->getPost('kg');
        $idPengeluaran = $this->request->getPost('id_pengeluaran');

        // Hindari pembagian 0
        $kgcns = ($cns > 0) ? ($kgs / $cns) : 0;

        $data = [
            'id_pengeluaran' => $idPengeluaran,
            'no_karung'      => $this->request->getPost('no_karung'),
            'area'           => $this->request->getPost('area'),
            'no_model'       => $this->request->getPost('no_model'),
            'item_type'      => $this->request->getPost('item_type'),
            'lot'            => $this->request->getPost('lot_out'),
            'kode_warna'     => $this->request->getPost('kode_warna'),
            'warna'          => $this->request->getPost('warna'),
            'cns_in_out'     => $cns,
            'kgs_in_out'     => $kgs,
            'kg_cns'         => $kgcns,
            'created_at'     => date('Y-m-d H:i:s'),
        ];

        // Simpan ke DB terlebih dahulu
        $this->stockArea->insert($data);

        // Update status terima area di sistem material
        $apiUrl = 'http://172.23.44.14/MaterialSystem/public/api/updateTerimaArea/' . $idPengeluaran;

        // Gunakan CURL biar bisa handle error
        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Kalau API gagal (500 atau tidak ada response)
        if ($httpCode !== 200) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Gagal update status ke sistem MaterialSystem.',
                'api_code' => $httpCode,
            ])->setStatusCode(500);
        }

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Data berhasil diterima area dan status diperbarui.',
        ]);
    }
}
