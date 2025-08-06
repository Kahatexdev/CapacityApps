<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\OrderModel;
use App\Models\BookingModel;
use App\Models\ProductTypeModel;
use App\Models\ApsPerstyleModel;
use App\Models\LiburModel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpParser\Node\Stmt\Return_;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\{Border, Alignment, Fill};
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class RossoController extends BaseController
{
    protected $filters;
    protected $bookingModel;
    protected $productModel;
    protected $orderModel;
    protected $ApsPerstyleModel;
    protected $liburModel;
    public function __construct()
    {
        $this->bookingModel = new BookingModel();
        $this->productModel = new ProductTypeModel();
        $this->orderModel = new OrderModel();
        $this->ApsPerstyleModel = new ApsPerstyleModel();
        $this->liburModel = new LiburModel();
        if ($this->filters   = ['role' => ['capacity']] != session()->get('role')) {
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
        // dd ($pemesananBb);
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
            $groupKey = $data['tgl_pakai'] . '|' . $data['no_model'] . '|' . $data['item_type'] . '|' . $data['kode_warna'] . '|' . $data['warna'];
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

        return view(session()->get('role') . '/index', $data);
    }

    public function getNomodel()
    {
        // $poTambahan = $this->request->getGet('po_tambahan'); // Ambil parameter PO Tambahan
        // $area = $this->request->getGet('area'); // Ambil parameter PO Tambahan

        // Logika untuk menentukan data berdasarkan status PO Tambahan
        // if ($poTambahan == 1) {
        //     // Jika search ada, panggil API eksternal dengan query parameter 'search'
        //     $apiUrl = 'http://172.23.44.14/MaterialSystem/public/api/getNoModelByPoTambahan?area=' . $area;

        //     try {
        //         $response = file_get_contents($apiUrl);
        //         if ($response === false) {
        //             throw new \Exception("Failed to fetch data from API: $apiUrl");
        //         }

        //         $data = json_decode($response, true);
        //         if (json_last_error() !== JSON_ERROR_NONE) {
        //             throw new \Exception("Invalid JSON response from API: $apiUrl");
        //         }
        //     } catch (\Exception $e) {
        //         // Tangani error
        //         return $this->response->setJSON(['error' => $e->getMessage()])->setStatusCode(500);
        //     }
        // } else {
        $data = $this->ApsPerstyleModel->getNoModel();
        // }

        return $this->response->setJSON($data)->setStatusCode(200);
    }

    public function getStyleSizeByNoModelPemesanan()
    {
        // Ambil No Model dari permintaan AJAX
        $noModel = $this->request->getGet('no_model');
        $area = $this->request->getGet('area');

        // $poTambahan = $this->request->getGet('po_tambahan');

        // Logika untuk menentukan data berdasarkan status PO Tambahan
        // if ($poTambahan == 1) {
        //     // Jika search ada, panggil API eksternal dengan query parameter 'search'
        //     $apiUrl = 'http://172.23.44.14/MaterialSystem/public/api/getStyleSizeByPoTambahan?no_model=' . $noModel . '&area=' . $area;

        //     try {
        //         $response = file_get_contents($apiUrl);
        //         if ($response === false) {
        //             throw new \Exception("Failed to fetch data from API: $apiUrl");
        //         }

        //         $data = json_decode($response, true);
        //         if (json_last_error() !== JSON_ERROR_NONE) {
        //             throw new \Exception("Invalid JSON response from API: $apiUrl");
        //         }
        //     } catch (\Exception $e) {
        //         // Tangani error
        //         return $this->response->setJSON(['error' => $e->getMessage()])->setStatusCode(500);
        //     }
        // } else {
        $data = $this->ApsPerstyleModel->getSizesByNoModelAndArea($noModel, $area); // Sesuaikan dengan model Anda
        // }

        // Kembalikan data dalam format JSON
        return $this->response->setJSON($data)->setStatusCode(200);
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

    public function getMU($model, $styleSize, $area, $qtyOrder)
    {
        $styleSize = urlencode($styleSize);  // Encode styleSize
        $apiUrl = 'http://172.23.44.14/MaterialSystem/public/api/getMUForRosso/' . $model . '/' . $styleSize . '/' . $area;
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
                        if (in_array($jenisBenang, ['nylon'])) {
                            $item['tgl_pakai'] = $this->request->getPost('tgl_pakai_nylon');
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
        $isDuplicateFound = false;
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
                            log_message('error', 'Duplikat: ' . json_encode([
                                'new' => $record,
                                'existing' => $existingRecord
                            ]));
                            // Tandai data sebagai duplikat
                            $isDuplicate = true;

                            // Log pesan error atau berikan respon status warning
                            log_message('error', 'Duplikasi ditemukan: ' . json_encode($record));
                            break 3; // Keluar dari loop jika duplikat ditemukan
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
        log_message('debug', 'Session ID: ' . session_id());
        $selectedItems = $this->request->getJSON()->selected ?? [];

        log_message('debug', 'Selected Items: ' . json_encode($selectedItems));
        $pemesananBb = session()->get('pemesananBb');
        log_message('debug', 'Session pemesananBb sebelum hapus: ' . json_encode($pemesananBb));

        if (empty($pemesananBb) || !is_array($pemesananBb)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Session tidak ditemukan atau kosong',
            ]);
        }

        foreach ($selectedItems as $selectedItem) {
            [$id_material, $tgl_pakai] = explode(',', $selectedItem);

            foreach ($pemesananBb as $groupIndex => $group) {
                foreach ($group as $itemIndex => $item) {
                    if ((string)$item['id_material'] === (string)$id_material && $item['tgl_pakai'] === $tgl_pakai) {
                        unset($pemesananBb[$groupIndex][$itemIndex]);
                    }
                }
                // Reset index array agar tidak bolong
                $pemesananBb[$groupIndex] = array_values($pemesananBb[$groupIndex]);
            }
        }

        // Hapus session jika sudah kosong semua
        $allEmpty = true;
        foreach ($pemesananBb as $group) {
            if (!empty($group)) {
                $allEmpty = false;
                break;
            }
        }

        if ($allEmpty) {
            session()->remove('pemesananBb');
        } else {
            session()->set('pemesananBb', $pemesananBb);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Data berhasil dihapus',
            'updated_session' => $pemesananBb
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
                    $totalKgs = 0;
                    foreach ($styleList as $style) {
                        if (isset($style['no_model'], $style['style_size'])) {
                            $requirement = $style['kgs'];
                            $totalRequirement += $requirement;
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

        // Untuk tanggal keempat, ambil 1 hari setelah tanggal "threeDays" dan cek ulang
        $initialFourDays = date('Y-m-d', strtotime($threeDays . ' +1 day'));
        $fourDays        = getNextNonHoliday($initialFourDays, $liburDates);

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
            'fourDays' => $fourDays,
        ];

        return view(session()->get('role') . '/listPemesanan', $data);
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

        return view(session()->get('role') . '/reportPemesanan', $data);
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

                $kebutuhan = $data['kgs'];
                $pemesanan['ttl_keb'] = $ttlKeb;

                $ttlKeb += $kebutuhan;
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
                // $qtyData = $this->ApsPerstyleModel->getQtyOrder($noModel, $data['style_size'], $area);
                // $qty     = (intval($qtyData['qty']) ?? 0);

                // Ambil kg po tambahan
                $PoPlus = 'http://172.23.44.14/MaterialSystem/public/api/getKgPoTambahan?no_model=' . $retur['no_model']
                    . '&item_type=' . urlencode($retur['item_type'])
                    . '&area=' . $area;

                $poPlusResponse = file_get_contents($PoPlus);
                $getPoPlus     = json_decode($poPlusResponse, true);


                $kgPoTambahan = floatval(
                    $getPoPlus['ttl_keb_potambahan'] ?? 0
                );

                $kebutuhan = $data['kgs'] + $kgPoTambahan;
                $retur['ttl_keb'] = $ttlKeb;
                $ttlKeb += $kebutuhan;
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

        return view(session()->get('role') . '/reportSisaKebutuhan', $data);
    }
}
