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
use App\Models\BsMesinModel;
use App\Models\PenggunaanJarum;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpParser\Node\Stmt\Return_;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\{Border, Alignment, Fill};
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class RossoController extends BaseController
{
    protected $filters;
    protected $jarumModel;
    protected $productModel;
    protected $produksiModel;
    protected $bookingModel;
    protected $orderModel;
    protected $ApsPerstyleModel;
    protected $liburModel;
    protected $BsMesinModel;
    protected $PenggunaanJarumModel;
    public function __construct()
    {


        $this->jarumModel = new DataMesinModel();
        $this->bookingModel = new BookingModel();
        $this->productModel = new ProductTypeModel();
        $this->produksiModel = new ProduksiModel();
        $this->orderModel = new OrderModel();
        $this->ApsPerstyleModel = new ApsPerstyleModel();
        $this->BsMesinModel = new BsMesinModel();
        $this->PenggunaanJarumModel = new PenggunaanJarum();
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
            $data = $this->ApsPerstyleModel->getNoModel();
        }

        return $this->response->setJSON($data)->setStatusCode(200);
    }
}
