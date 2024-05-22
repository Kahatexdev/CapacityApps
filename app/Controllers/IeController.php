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
use App\Models\HistorySmvModel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class IeController extends BaseController
{
    protected $filters;
    protected $jarumModel;
    protected $productModel;
    protected $produksiModel;
    protected $bookingModel;
    protected $orderModel;
    protected $ApsPerstyleModel;
    protected $liburModel;
    protected $historysmv;
    public function __construct()
    {


        $this->jarumModel = new DataMesinModel();
        $this->bookingModel = new BookingModel();
        $this->productModel = new ProductTypeModel();
        $this->produksiModel = new ProduksiModel();
        $this->orderModel = new OrderModel();
        $this->ApsPerstyleModel = new ApsPerstyleModel();
        $this->historysmv = new HistorySmvModel();
        if ($this->filters   = ['role' => ['ie']] != session()->get('role')) {
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
        $orders = $this->ApsPerstyleModel->getSmv();
        $data = [
            'title' => 'Dashboard',
            'active1' => 'active',
            'active2' => '',
            'targetProd' => 0,
            'produksiBulan' => 0,
            'produksiHari' => 0,
            'order' => $orders
        ];
        return view('Ie/index', $data);
    }
    public function inputsmv()
    {

        $insert = [
            'style' => $this->request->getPost('size'),
            'smv_old' => $this->request->getPost('smvold'),
        ];
        $id = $this->request->getPost('id');
        $smv = $this->request->getPost('smv');
        $update = $this->ApsPerstyleModel->update($id, ['smv' => $smv]);
        if ($update) {
            $input = $this->historysmv->insert($insert);
            if ($input) {
                return redirect()->to(base_url('ie/'))->with('success', 'Smv berhasil di update');
            } else {
                return redirect()->to(base_url('ie/'))->with('error', 'Smv gagal di update');
            }
        } else {
            return redirect()->to(base_url('ie/'))->with('error', 'Smv gagal di update');
        }
    }
    public function gethistory()
    {
        $size = $this->request->getPost('size');
        $data = $this->historysmv->getDataSize($size);

        if (empty($data)) {
            $empty = [
                [
                    'smv_old' => "tidak ada data",
                    'created_at' => "tidak ada data"
                ]
            ];
            return json_encode($empty);
        }

        return json_encode($data);
    }
}
