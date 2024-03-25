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

class MesinController extends BaseController
{
    protected $filters;
    protected $jarumModel;
    protected $productModel;
    protected $produksiModel;
    protected $bookingModel;
    protected $orderModel;
    protected $ApsPerstyleModel;

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
            'title' => 'Data Mesin',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => 'active',
        ];
        return view('Capacity/Mesin/Mastermesin', $data);
    }
    public function mesinPerJarum()
    {
        $totalMesin = $this->jarumModel->getTotalMesinByJarum();
        $data = [
            'title' => 'Data Order',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => 'active',
            'TotalMesin' => $totalMesin,
        ];
        return view('Capacity/Mesin/mesinjarum', $data);
    }
    public function mesinperarea()
    {
        $tampilperarea = $this->jarumModel->getArea();
        $product = $this->productModel->findAll();
        $data = [
            'title' => 'Data Order',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => 'active',
            'tampildata' => $tampilperarea,
            'product' => $product,

        ];
        return view('Capacity/Mesin/mesinarea', $data);
    }
    public function DetailMesinPerJarum($area)
    {
        $tampilperarea = $this->jarumModel->getJarumArea($area);
        $data = [
            'title' => 'Data Order',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => 'active',
            'area' => $area,
            'tampildata' => $tampilperarea,

        ];
        return view('Capacity/Mesin/detailMesinJarum', $data);
    }
    public function inputmesinperarea()
    {
        $area = $this->request->getPost("area");
        $jarum = $this->request->getPost("jarum");
        $total_mc = $this->request->getPost("total_mc");
        $brand = $this->request->getPost("brand");
        $mesin_jalan = $this->request->getPost("mesin_jalan");

        $input = [
            'area' => $area,
            'jarum' => $jarum,
            'total_mc' => $total_mc,
            'brand' => $brand,
            'mesin_jalan' => $mesin_jalan,
        ];

        $insert =   $this->jarumModel->insert($input);
        if ($insert) {
            return redirect()->to(base_url('/capacity/datamesinperjarum/' . $area))->withInput()->with('success', 'Data Berhasil Di Input');
        } else {
            return redirect()->to(base_url('/capacity/datamesinperjarum/' . $area))->withInput()->with('error', 'Data Gagal Di Input');
        }
    }
    public function updatemesinperjarum($idDataMesin)
    {

        $data = [
            'total_mesin' => $this->request->getPost("total_mc"),
            'brand' => $this->request->getPost("brand"),
            'mesin_jalan' => $this->request->getPost("mesin_jalan"),
        ];
        $id = $idDataMesin;
        $update = $this->jarumModel->update($id, $data);
        $area = $this->request->getPost("area");
        if ($update) {
            return redirect()->to(base_url('capacity/datamesinperjarum/' . $area))->withInput()->with('success', 'Data Berhasil Di Update');
        } else {
            return redirect()->to(base_url('capacity/datamesinperjarum/' . $area))->withInput()->with('error', 'Gagal Update Data');
        }
    }
    public function deletemesinareal($idDataMesin)
    {
        $delete = $this->jarumModel->delete($idDataMesin);
        if ($delete) {
            return redirect()->to(base_url('capacity/datamesin'))->withInput()->with('success', 'Data Berhasil Di Hapus');
        } else {
            return redirect()->to(base_url('capacity/datamesin'))->withInput()->with('error', 'Gagal Hapus Data');
        }
    }
    

}
