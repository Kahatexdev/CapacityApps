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
use PhpOffice\PhpSpreadsheet\IOFactory;

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
    public function __construct()
    {


        $this->jarumModel = new DataMesinModel();
        $this->bookingModel = new BookingModel();
        $this->productModel = new ProductTypeModel();
        $this->produksiModel = new ProduksiModel();
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
}
