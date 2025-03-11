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
        $noModel = $this->DetailPlanningModel->getNoModelAktif($area);
        $pemesananBb = session()->get('pemesananBb');
        // Kita "flatten" data sehingga tiap baris tersimpan sebagai record tunggal
        $flattenData = [];

        if (!empty($pemesananBb)) {
            foreach ($pemesananBb as $group) {
                // Pastikan group adalah array multidimensi (setiap field berupa array)
                if (is_array($group) && isset($group['tgl_pakai']) && is_array($group['tgl_pakai'])) {
                    $jumlahBaris = count($group['tgl_pakai']);
                    for ($i = 0; $i < $jumlahBaris; $i++) {
                        $flattenData[] = [
                            'tgl_pakai'    => $group['tgl_pakai'][$i] ?? '',
                            'no_model'     => $group['no_model'][$i] ?? '',
                            'style_size'   => $group['style_size'][$i] ?? '',
                            'item_type'    => $group['item_type'][$i] ?? '',
                            'kode_warna'   => $group['kode_warna'][$i] ?? '',
                            'warna'        => $group['warna'][$i] ?? '',
                            'jalan_mc'     => $group['jalan_mc'][$i] ?? '',
                            'ttl_cns'      => $group['ttl_cns'][$i] ?? '',
                            'ttl_berat_cns'=> $group['ttl_berat_cns'][$i] ?? '',
                            'id_material'  => $group['id_material'][$i] ?? ''
                        ];
                    }
                } else {
                    // Jika data sudah tersimpan sebagai record tunggal
                    $flattenData[] = $group;
                }
            }
        }
        // Lakukan sorting berdasarkan urutan kolom: tgl_pakai, no_model, style_size, item_type, kode_warna, dan warna
        usort($flattenData, function($a, $b) {
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
            $groupKey = $data['tgl_pakai'] . '|' .$data['no_model'] . '|' . $data['item_type'] . '|' . $data['kode_warna'] . '|' . $data['warna'];
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
    public function getMU($model, $styleSize)
    {
        $styleSize = urlencode($styleSize);  // Encode styleSize
        $apiUrl = 'http://172.23.39.118/MaterialSystem/public/api/getMU/' . $model . '/' . $styleSize;
        $response = file_get_contents($apiUrl);  // Mendapatkan response dari API
        if ($response === FALSE) {
            die('Error occurred while fetching data.');
        }

        $data = json_decode($response, true);  // Decode JSON response dari API


        return $this->response->setJSON($data);
    }
    public function savePemesananSession()
    {
        // Ambil data yang sudah ada atau buat array kosong
        $existingData = session()->get('pemesananBb') ?? [];

        // Ambil data baru dari request POST dengan key 'items'
        $newData = $this->request->getPost('items');

        // Cek apakah ada duplikasi berdasarkan id_material dan tgl_pakai
        foreach ($newData as $record) {
            foreach ($existingData as $existingRecord) {
                if (
                    isset($record['id_material'], $record['tgl_pakai'], $existingRecord['id_material'], $existingRecord['tgl_pakai']) &&
                    $record['id_material'] == $existingRecord['id_material'] &&
                    $record['tgl_pakai'] == $existingRecord['tgl_pakai']
                ) {
                    // Jika duplikasi ditemukan, batalkan penyimpanan dan kembalikan error JSON
                    return $this->response->setJSON([
                        'message' => 'Data dengan id_material dan tgl_pakai yang sama sudah ada.',
                        'title'  => 'Error!',
                        'status'  => 'warning'
                    ]);
                }
            }
        }

        // Jika tidak ada duplikasi, gabungkan data baru dengan data yang sudah ada
        foreach ($newData as $record) {
            $existingData[] = $record;
        }

        // Simpan kembali ke session
        session()->set('pemesananBb', $existingData);

        return $this->response->setJSON([
            'message' => 'Data berhasil disimpan ke session',
            'data'    => $existingData,
            'status'  => 'success',
            'title'  => 'Sukses!',

        ]);
    }
    public function deletePemesananSession($id_material) {
        // Ambil data session yang asli (data flattened)
        $pemesananBb = session()->get('pemesananBb') ?? [];

        $found = false;
        foreach ($pemesananBb as $key => $record) {
            // Pastikan record memiliki id_material dan cocok dengan parameter yang diterima
            if (isset($record['id_material']) && $record['id_material'] == $id_material) {
                unset($pemesananBb[$key]);
                $found = true;
                break;
            }
        }
        
        if ($found) {
            // Re-index array agar indeks kembali berurutan
            $pemesananBb = array_values($pemesananBb);
            session()->set('pemesananBb', $pemesananBb);
            return redirect()->back()->with('success', 'Data berhasil dihapus');
        } else {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }
    }
    public function deleteAllPemesananSession() {
        // Menghapus data session 'pemesananBb'
        session()->remove('pemesananBb');
        
        // Redirect dengan pesan sukses
        return redirect()->back()->with('success', 'Data berhasil dihapus dari session');
    }
    public function saveListPemesanan() {
        $admin = session()->get('username');

        $pemesananBb = session()->get('pemesananBb') ?? [];
        if (empty($pemesananBb)) {
            return redirect()->back()->with('error', 'Tidak ada data list pemesanan');
        }
        dd($pemesananBb);
        foreach ($pemesananBb as $key => $data) {
            $insertData = [
                'id_material' => $data['id_material'],
                'tgl_list' => '',
                'tgl_pakai' => $data['tgl_pakai'],
                'jl_mc' => $data['jalan_mc'],
                'ttl_qty_cones' => $data['ttl_cns'],
                'ttl_berat_cones' => $data['ttl_berat_cns'],
                'admin' => $admin,
                'created_at' => '',
            ];
        }
    }
}
