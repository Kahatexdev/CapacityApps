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
        ];
        // $dataList = $this->response->setJSON($data);


        return view(session()->get('role') . '/Material/listPemesanan', $data);
    }
}
