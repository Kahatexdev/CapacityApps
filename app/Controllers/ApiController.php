<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\DataMesinModel;
use App\Models\OrderModel;
use App\Models\BookingModel;
use App\Models\ProductTypeModel;
use App\Models\ApsPerstyleModel;
use App\Models\ProduksiModel;
use App\Models\BsMesinModel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ApiController extends ResourceController
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
    protected $format = 'json';
    public function __construct()
    {
        $this->jarumModel = new DataMesinModel();
        $this->bookingModel = new BookingModel();
        $this->productModel = new ProductTypeModel();
        $this->produksiModel = new ProduksiModel();
        $this->orderModel = new OrderModel();
        $this->ApsPerstyleModel = new ApsPerstyleModel();
        $this->BsMesinModel = new BsMesinModel();
        $this->validation = \Config\Services::validation();
    }
    public function index()
    {
        //
    }
    public function bsKaryawan($id)
    {
        $bsData = $this->BsMesinModel->bsDataKaryawan($id);
        return $this->respond($bsData, 200);
    }
    public function bsPeriode($start, $stop)
    {
        $bsData = $this->BsMesinModel->bsPeriode($start, $stop);
        return $this->respond($bsData, 200);
    }
    public function bsDaily($start, $stop)
    {
        $bsData = $this->BsMesinModel->bsDaily($start, $stop);
        return $this->respond($bsData, 200);
    }
}