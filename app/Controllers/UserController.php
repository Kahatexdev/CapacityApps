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
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpParser\Node\Stmt\Return_;

class UserController extends BaseController
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
    public function __construct()
    {


        $this->jarumModel = new DataMesinModel();
        $this->bookingModel = new BookingModel();
        $this->productModel = new ProductTypeModel();
        $this->produksiModel = new ProduksiModel();
        $this->orderModel = new OrderModel();
        $this->ApsPerstyleModel = new ApsPerstyleModel();
        $this->BsMesinModel = new BsMesinModel();
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
        $data = [
            'role' => session()->get('role'),
            'title' => 'Dashboard',
            'active1' => 'active',
            'active2' => '',
            'active3' => '',
            'targetProd' => 0,
            'produksiBulan' => 0,
            'produksiHari' => 0

        ];
        return view(session()->get('role') . '/index', $data);
    }
    public function produksi()
    {
        $dataPdk = $this->ApsPerstyleModel->getPdkProduksi();
        $produksi = $this->produksiModel->getProduksiHarianArea();
        $dataBuyer = $this->orderModel->getBuyer();
        $dataArea = $this->jarumModel->getArea();
        $dataJarum = $this->jarumModel->getJarum();
        $data = [
            'role' => session()->get('role'),
            'title' => 'Dashboard',
            'active1' => '',
            'active3' => '',
            'active2' => 'active',
            'targetProd' => 0,
            'produksiBulan' => 0,
            'produksiHari' => 0,
            'pdk' => $dataPdk,
            'produksi' => $produksi,
            'buyer' => $dataBuyer,
            'area' => $dataArea,
            'jarum' => $dataJarum,

        ];
        return view(session()->get('role') . '/produksi', $data);
    }
    public function bssetting()
    {

        $data = [
            'role' => session()->get('role'),
            'title' => 'Dashboard',
            'active1' => '',
            'active3' => '',
            'active2' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'targetProd' => 0,


        ];
        return view(session()->get('role') . '/bssetting', $data);
    }
    public function bsmesin()
    {
        $area = session()->get('username');

        $apiUrl = 'http://172.23.44.14/SkillMapping/public/api/area/' . $area;

        try {
            // Attempt to fetch the API response
            $json = @file_get_contents($apiUrl);

            // Check if the response is valid
            if ($json === false) {
                throw new \Exception('API request failed.');
            }

            // Decode the JSON response
            $karyawan = json_decode($json, true);

            // Validate if decoding was successful
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON response.');
            }
        } catch (\Exception $e) {
            // Set default values in case of an error
            $karyawan[] = [
                'id_karyawan' => '-',
                'nama_karyawan' => 'No Karyawan Data Found',
            ];

            // Log the error for debugging purposes (optional)
            log_message('error', 'Error fetching API data: ' . $e->getMessage());
        }

        // Prepare data for the view
        $data = [
            'role' => session()->get('role'),
            'title' => 'BS Mesin',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'targetProd' => 0,
            'karyawan' => $karyawan,
        ];
        return view(session()->get('role') . '/bsmesin', $data);
    }
    public function saveBsMesin()
    {
        $request = $this->request;

        // Data input utama
        $id = $request->getPost('nama');
        $namaKaryawan = $request->getPost('namakar');
        $kodeKartu    = $request->getPost('kode_kartu');
        $shift        = $request->getPost('shift');
        $tglProd      = $request->getPost('tgl_prod');
        $area         = session()->get('username');

        // Data detail
        $noMesin = $request->getPost('no_mesin');
        $inisial = $request->getPost('inisial');
        $noModel = $request->getPost('no_model');
        $size    = $request->getPost('size');
        $gram    = $request->getPost('gram');
        $pcs     = $request->getPost('pcs');

        // Pastikan data detail valid
        if (empty($noMesin) || !is_array($noMesin)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data detail mesin tidak valid.',
            ]);
        }

        // Iterasi data detail dan siapkan untuk batch insert
        $details = [];
        foreach ($noMesin as $index => $value) {
            $details[] = [
                'id_karyawan'   => $id, // atau field lain sebagai ID
                'nama_karyawan' => $namaKaryawan,
                'shift'         => $shift,
                'area'          => $area,
                'tanggal_produksi'   => $tglProd,
                'no_mesin'      => $noMesin[$index],
                'inisial'       => $inisial[$index],
                'no_model'      => $noModel[$index],
                'size'          => $size[$index],
                'qty_gram'      => $gram[$index],
                'qty_pcs'       => $pcs[$index],
            ];
        }

        // Batch insert data ke database
        if ($this->BsMesinModel->insertBatch($details)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data berhasil disimpan.',
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menyimpan data.',
            ]);
        }
    }
}
