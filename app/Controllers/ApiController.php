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
use App\Models\DetailPlanningModel;
use App\Models\AreaModel;
use App\Models\BsModel;
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
    protected $DetailPlanningModel;
    protected $areaModel;
    protected $bsModel;
    protected $format = 'json';
    public function __construct()
    {
        $this->jarumModel = new DataMesinModel();
        $this->bookingModel = new BookingModel();
        $this->productModel = new ProductTypeModel();
        $this->produksiModel = new ProduksiModel();
        $this->orderModel = new OrderModel();
        $this->ApsPerstyleModel = new ApsPerstyleModel();
        $this->DetailPlanningModel = new DetailPlanningModel();
        $this->BsMesinModel = new BsMesinModel();
        $this->areaModel = new AreaModel();
        $this->bsModel = new BsModel();
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

    public function orderMaterial($model, $size)
    {
        // Call the model method and get the result
        $dataOrder = $this->ApsPerstyleModel->orderMaterial($model, $size);

        // Check if there's an error in the result (e.g., 'error' key)
        if (isset($dataOrder['error'])) {
            // Return 404 if error exists
            return $this->respond(['message' => $dataOrder['error']], 404);
        }

        // Return the result with a 200 status if everything is okay
        return $this->respond($dataOrder, 200);
    }
    public function reqstartmc($model)
    {
        $startMc = $this->DetailPlanningModel->reqstartmc($model);
        if (isset($startMc['error'])) {
            return $this->respond(['message' => $startMc['error']], 404);
        }

        return $this->respond($startMc, 200);
    }

    public function getDataPerinisial($area, $model, $size)
    {
        if (!$area || !$model || !$size) {
            return $this->response->setJSON([
                "error" => "Parameter tidak lengkap",
                "received" => [
                    "area" => $area,
                    "nomodel" => $model,
                    "size" => $size,
                ]
            ])->setStatusCode(400);
        }
        $prod = $this->orderModel->getDataPph($area, $model, $size);
        $idaps = $this->ApsPerstyleModel->getIdApsForPph($area, $model, $size);
        $idapsList = array_column($idaps, 'idapsperstyle');
        $bsSettingData = $this->bsModel->getBsPph($idapsList) ?? 0;
        $bsMesinData = $this->BsMesinModel->getBsMesinPph($area, $model, $size) ?? 0;
        $bsMesin = $bsMesinData['bs_gram'];
        $result = [
            "machinetypeid" => $prod["machinetypeid"],
            "area" => $area,
            "no_model" => $model,
            "size" => $size,
            "inisial" =>  $prod["inisial"] ?? null,
            "qty" => $prod["qty"],
            "sisa" =>    $prod["sisa"],
            "po_plus" => $prod["po_plus"],
            "bruto" => $prod["bruto"],
            "bs_setting" => $bsSettingData['bs_setting'],
            "bs_mesin" => $bsMesin,
        ];
        return $this->response->setJSON($result);
    }
}
