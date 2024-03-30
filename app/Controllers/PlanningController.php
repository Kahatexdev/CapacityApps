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
use PhpOffice\PhpSpreadsheet\IOFactory;


class PlanningController extends BaseController
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
        $this->liburModel = new LiburModel();
        if ($this->filters   = ['role' => ['planning']] != session()->get('role')) {
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
            'title' => 'Capacity System',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => 'active',

        ];
        return view('Planning/index', $data);
    }
    public function order()
    {
        $totalMesin = $this->jarumModel->getTotalMesinByJarum();
        $data = [
            'title' => 'Data Order',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => 'active',
            'TotalMesin' => $totalMesin,
        ];
        return view('Planning/Order/ordermaster', $data);
    }

    public function assignareal()
    {
        $data = [
            'mastermodel' => $this->request->getPost("no_model"),
            'jarum' => $this->request->getPost("jarum"),
            'area' => $this->request->getPost("area"),
        ];
        $assign = $this->ApsPerstyleModel->asignAreal($data);
        if ($assign) {
            return redirect()->to(base_url('planning/dataorder/'))->withInput()->with('success', 'Berhasil Assign Area');
        } else {
            return redirect()->to(base_url('planning/dataorder/'))->withInput()->with('error', 'Gagal Assign Area');
        }
    }
}
