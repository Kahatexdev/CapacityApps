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
use App\Models\LiburModel;
use App\Models\BsModel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\MonthlyMcModel;

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
    protected $globalModel;

    protected $validation;
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
        $this->globalModel = new MonthlyMcModel();
        $this->liburModel = new LiburModel();
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
        if (!$model || !$size) {
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
        $bsSettingData = $this->bsModel->getBsPph($idapsList);
        $bsMesinData = $this->BsMesinModel->getBsMesinPph($area, $model, $size);
        $bsMesin = $bsMesinData['bs_gram'] ?? 0;
        $result = [
            "machinetypeid" => $prod["machinetypeid"],
            "area" => $prod['factory'],
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

    public function getArea()
    {
        $area = $this->areaModel->getArea();

        // Filter agar 'name' yang mengandung 'Gedung' tidak ikut
        $filteredArea = array_filter($area, function ($item) {
            return stripos($item['name'], 'Gedung') === false; // Cek jika 'Gedung' tidak ada di 'name'
        });

        // Ambil hanya field 'name'
        $result = array_column($filteredArea, 'name');
        return $this->response->setJSON($result);
    }
    public function getPPhPerhari($area, $tanggal)
    {
        $produksi = $this->produksiModel->getProduksiPerStyle($area, $tanggal);

        if (!empty($produksi)) {
            // Extract all mastermodel and size values for batch query
            $mastermodels = array_column($produksi, 'mastermodel');
            $sizes = array_column($produksi, 'size');

            // Fetch all bs_mesin data in one query
            $bsMesinData = $this->BsMesinModel->getBsMesinHarian($mastermodels, $sizes, $tanggal, $area);

            // Create a lookup table for fast matching
            $bsMesinMap = [];
            foreach ($bsMesinData as $bs) {
                $key = $bs['no_model'] . '_' . $bs['size'];
                $bsMesinMap[$key] = $bs['bs_mesin'];
            }

            // Assign bs_mesin to production data
            foreach ($produksi as &$prod) {
                $key = $prod['mastermodel'] . '_' . $prod['size'];
                $prod['bs_mesin'] = $bsMesinMap[$key] ?? 0; // Default to null if not found
            }
        }

        return $this->response->setJSON($produksi);
    }
    public function getHariLibur()
    {
        $data = $this->liburModel->findAll();
        return $this->response->setJSON($data);
    }
    public function getPlanMesin()
    {
        $dataPlan = $this->globalModel->getPlan();
        return $this->response->setJSON($dataPlan);
    }
    public function prodBsDaily($area, $tanggal)
    {

        $bsdata = $this->BsMesinModel->bsKary($area, $tanggal);
        return $this->response->setJSON($bsdata);
    }

    public function getApsPerStyles()
    {
        // Get the parameters from the request
        $mastermodel = $this->request->getGet('mastermodel');
        $size = $this->request->getGet('size');
        $factory = $this->request->getGet('factory');
        // Validate the input parameters
        if (!$mastermodel || !$size) {
            return $this->response->setJSON([
                "error" => "Parameter tidak lengkap",
                "received" => [
                    "mastermodel" => $mastermodel,
                    "size" => $size,
                    "factory" => $factory,
                ]
            ])->setStatusCode(400);
        }

        // Fetch data from the model
        $apsData = $this->ApsPerstyleModel->getApsPerStyle($mastermodel, $size, $factory);

        // Check if data is found
        if (empty($apsData)) {
            return $this->respond(['message' => 'Data tidak ditemukan'], ResponseInterface::HTTP_NOT_FOUND);
        }

        // Return the data with a 200 status
        return $this->respond($apsData, ResponseInterface::HTTP_OK);
    }

    public function getApsPerStyle($mastermodel, $size, $factory)
    {
        // Validate the input parameters
        if (!$mastermodel || !$size) {
            return $this->response->setJSON([
                "error" => "Parameter tidak lengkap",
                "received" => [
                    "mastermodel" => $mastermodel,
                    "size" => $size,
                    "factory" => $factory,
                ]
            ])->setStatusCode(400);
        }

        // Fetch data from the model
        $apsData = $this->ApsPerstyleModel->getApsPerStyle($mastermodel, $size, $factory);

        // Check if data is found
        if (empty($apsData)) {
            return $this->respond(['message' => 'Data tidak ditemukan'], ResponseInterface::HTTP_NOT_FOUND);
        }

        // Return the data with a 200 status
        return $this->respond($apsData, ResponseInterface::HTTP_OK);
    }

    public function getApsPerStyleById($id)
    {
        // Validate the input parameter
        if (!$id) {
            return $this->response->setJSON([
                "error" => "Parameter tidak lengkap",
                "received" => [
                    "idapsperstyle" => $id,
                ]
            ])->setStatusCode(400);
        }

        // Fetch data from the model
        $apsData = $this->ApsPerstyleModel->getApsPerStyleById($id);

        // Check if data is found
        if (empty($apsData)) {
            return $this->respond(['message' => 'Data tidak ditemukan'], ResponseInterface::HTTP_NOT_FOUND);
        }

        // Return the data with a 200 status
        return $this->respond($apsData, ResponseInterface::HTTP_OK);
    }
    public function getQtyPcsByAreaByStyle($area)
    {
        $noModel = $this->request->getGet('no_model');
        $styleSize = $this->request->getGet('style_size');

        $data = [
            'area' => $area,
            'noModel' => $noModel,
            'styleSize' => $styleSize
        ];

        $getQty = $this->ApsPerstyleModel->getQtyPcsByAreaByStyle($data);

        // log_message('info', ': ' . json_encode($getQty));
        return $this->response->setJSON($getQty['qty']);
    }

    public function getMasterModel()
    {
        // Fetch data from the model
        $masterModels = $this->ApsPerstyleModel->getMasterModel();

        // Check if data is found
        if (empty($masterModels)) {
            return $this->respond(['message' => 'Data tidak ditemukan'], ResponseInterface::HTTP_NOT_FOUND);
        }

        // Return the data with a 200 status
        return $this->respond($masterModels, ResponseInterface::HTTP_OK);
    }

    public function getInisialByModel()
    {
        // Get the mastermodel parameter from the request
        $mastermodel = $this->request->getGet('mastermodel');
        // Validate the input parameter
        if (!$mastermodel) {
            return $this->response->setJSON([
                "error" => "Parameter tidak lengkap",
                "received" => [
                    "mastermodel" => $mastermodel,
                ]
            ])->setStatusCode(400);
        }

        // Fetch data from the model
        $inisialData = $this->ApsPerstyleModel->getInisialByModel($mastermodel);

        // Check if data is found
        if (empty($inisialData)) {
            return $this->respond(['message' => 'Data tidak ditemukan'], ResponseInterface::HTTP_NOT_FOUND);
        }

        // Return the data with a 200 status
        return $this->respond($inisialData, ResponseInterface::HTTP_OK);
    }

    public function getIdApsByModelInisial()
    {
        // Get the parameters from the request
        $mastermodel = $this->request->getGet('mastermodel');
        $inisial = $this->request->getGet('inisial');
        // Validate the input parameters
        if (!$mastermodel || !$inisial) {
            return $this->response->setJSON([
                "error" => "Parameter tidak lengkap",
                "received" => [
                    "mastermodel" => $mastermodel,
                    "inisial" => $inisial,
                ]
            ])->setStatusCode(400);
        }

        // Fetch data from the model
        $idApsData = $this->ApsPerstyleModel->getIdApsByModelInisial($mastermodel, $inisial);

        // Check if data is found
        if (empty($idApsData)) {
            return $this->respond(['message' => 'Data tidak ditemukan'], ResponseInterface::HTTP_NOT_FOUND);
        }

        // Return the data with a 200 status
        return $this->respond($idApsData, ResponseInterface::HTTP_OK);
    }
    public function getDeliv($model)
    {
        $deliv = $this->ApsPerstyleModel->getDeliv($model);
        if (empty($deliv)) {
            return $this->respond(['message' => 'Data tidak ditemukan'], ResponseInterface::HTTP_NOT_FOUND);
        }

        // Return the data with a 200 status
        return $this->respond($deliv, ResponseInterface::HTTP_OK);
    }
    public function getQtyOrder()
    {
        $noModel = $this->request->getGet('no_model');
        $styleSize = $this->request->getGet('style_size');
        $area = $this->request->getGet('area');

        // Debug ke log CI
        log_message('debug', 'API getQtyOrder params => no_model: ' . $noModel . ', style_size: ' . $styleSize . ', area: ' . $area);

        $orderQty = $this->ApsPerstyleModel->getQtyOrder($noModel, $styleSize, $area);

        // Check if data is found
        if (empty($orderQty)) {
            return $this->respond(['message' => 'Data tidak ditemukan'], ResponseInterface::HTTP_NOT_FOUND);
        }

        // Return the data with a 200 status
        return $this->respond($orderQty, ResponseInterface::HTTP_OK);
    }
    public function getDataBuyer()
    {
        $noModel = $this->request->getGet('no_model');

        $buyer = $this->orderModel->getDataBuyer($noModel);

        // Check if data is found
        if (empty($buyer)) {
            return $this->respond(['message' => 'Data tidak ditemukan'], ResponseInterface::HTTP_NOT_FOUND);
        }

        // Return the data with a 200 status
        return $this->respond($buyer, ResponseInterface::HTTP_OK);
    }
}
