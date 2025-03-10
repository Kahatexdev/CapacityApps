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
    public function getDataForPPH($area, $nomodel)
    {
        if (!$area || !$nomodel) {
            return $this->response->setJSON([
                "error" => "Parameter tidak lengkap",
                "received" => [
                    "area" => $area,
                    "nomodel" => $nomodel
                ]
            ])->setStatusCode(400);
        }

        $prod = $this->orderModel->getDataPph($area, $nomodel);
        $idaps = $this->ApsPerstyleModel->getIdApsForPph($area, $nomodel);
        $idapsList = array_column($idaps, 'idapsperstyle');
        $bsSettingData = $this->bsModel->getBsPph($idapsList);
        $bsMesinData = $this->BsMesinModel->getBsMesinPph($area, $nomodel);

        // Konversi bsMesin menjadi array asosiatif berdasarkan key
        $bsMesin = [];
        foreach ($bsMesinData as $bs) {
            $key = $bs['factory'] . '-' . $bs['mastermodel'] . '-' . $bs['size'];
            $bsMesin[$key] = $bs['bs_pcs'] ?? 0; // Jika tidak ada, default ke 0
        }

        // Konversi bsSetting menjadi array asosiatif berdasarkan key
        $bsSetting = [];
        foreach ($bsSettingData as $bs) {
            $key = $bs['factory'] . '-' . $bs['mastermodel'] . '-' . $bs['size'];
            $bsSetting[$key] = $bs['bs_setting'] ?? 0; // Jika tidak ada, default ke 0
        }

        $result = [];
        foreach ($prod as $item) {
            $key = $item['factory'] . '-' . $item['no_model'] . '-' . $item['size'];
            if (!isset($result[$key])) {
                $result[$key] = [
                    'area' => $item['factory'],
                    'no_model' => $item['no_model'],
                    'size' => $item['size'],
                    'qty' => $item['qty'],
                    'sisa' => $item['sisa'],
                    'bruto' => $item['bruto'],
                    'bs_pcs' => $bsMesin[$key] ?? 0,    
                    'bs_setting' => $bsSetting[$key] ?? 0
                ];
            }

        }

        return $this->response->setJSON($result ?? []);
    }
}
