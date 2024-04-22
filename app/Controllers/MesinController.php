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
use App\Models\CylinderModel;
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
        $this->cylinderModel = new cylinderModel();
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
            'active6' => '',
            'active7' => '',
        ];
        return view('Capacity/Mesin/Mastermesin', $data);
    }
    public function mesinPerJarum()
    {
        $totalMesin = $this->jarumModel->getTotalMesinByJarum();
        $data = [
            'title' => 'Data Mesin',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => 'active',
            'active6' => '',
            'active7' => '',
            'TotalMesin' => $totalMesin,
        ];
        return view('Capacity/Mesin/mesinjarum', $data);
    }
    public function stockcylinder()
    {
        $totalCylinder = $this->cylinderModel->findAll();
        $data = [
            'title' => 'Data Cylinder',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => 'active',
            'active6' => '',
            'active7' => '',
            'tampildata' => $totalCylinder,
        ];
        return view('Capacity/Mesin/dataCylinder', $data);
    }
    public function mesinperarea()
    {
        $tampilperarea = $this->jarumModel->getArea();
        $product = $this->productModel->findAll();
        $data = [
            'title' => 'Data Mesin',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => 'active',
            'active6' => '',
            'active7' => '',
            'tampildata' => $tampilperarea,
            'product' => $product,

        ];
        return view('Capacity/Mesin/mesinarea', $data);
    }
    public function DetailMesinPerJarum($jarum)
    {
        $tampilperarea = $this->jarumModel->getMesinPerJarum($jarum);
        $data = [
            'title' => 'Data Mesin',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => 'active',
            'active6' => '',
            'active7' => '',
            'jarum' => $jarum,
            'tampildata' => $tampilperarea,
        ];

        return view('Capacity/Mesin/detailMesinJarum', $data);
    }
    public function DetailMesinPerArea($area)
    {
        $tampilperarea = $this->jarumModel->getJarumArea($area);
        $data = [
            'title' => 'Data Mesin',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => 'active',
            'active6' => '',
            'active7' => '',
            'area' => $area,
            'tampildata' => $tampilperarea,
        ];

        return view('Capacity/Mesin/detailMesinArea', $data);
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
    public function inputcylinder()
    {
        $area = $this->request->getPost("needle");
        $jarum = $this->request->getPost("production_unit");
        $total_mc = $this->request->getPost("type_machine");
        $brand = $this->request->getPost("qty");
        $mesin_jalan = $this->request->getPost("needle_detail");

        $input = [
            'needle' => $area,
            'production_unit' => $jarum,
            'type_machine' => $total_mc,
            'qty' => $brand,
            'needle_detail' => $mesin_jalan,
        ];

        $insert =   $this->cylinderModel->insert($input);
        if ($insert) {
            return redirect()->to(base_url('/capacity/stockcylinder/'))->withInput()->with('success', 'Data Berhasil Di Input');
        } else {
            return redirect()->to(base_url('/capacity/stockcylinder/'))->withInput()->with('error', 'Data Gagal Di Input');
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
