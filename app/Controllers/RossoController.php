<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\OrderModel;
use App\Models\BookingModel;
use App\Models\ProductTypeModel;
use App\Models\ApsPerstyleModel;
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
    public function __construct()
    {
        $this->bookingModel = new BookingModel();
        $this->productModel = new ProductTypeModel();
        $this->orderModel = new OrderModel();
        $this->ApsPerstyleModel = new ApsPerstyleModel();
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
}
