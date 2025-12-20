<?php

namespace App\Controllers;


use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;


use App\Models\DataMesinModel;
use App\Models\OrderModel;
use App\Models\BookingModel;
use App\Models\ProductTypeModel;
use App\Models\ApsPerstyleModel;
use App\Models\ProduksiModel;
use App\Models\LiburModel;
use App\Models\KebutuhanMesinModel;
use App\Models\KebutuhanAreaModel;
use App\Models\MesinPlanningModel;
use App\Models\DetailPlanningModel;
use App\Models\TanggalPlanningModel;
use App\Models\EstimatedPlanningModel;
use App\Models\MesinPerStyle;
use App\Models\MesinPernomor;
use App\Models\MachinesModel;
use App\Models\AksesModel;/*  */
use App\Models\PpsModel;/*  */
use App\Models\PengaduanModel;
use App\Models\PengaduanReply;
use App\Models\CancelModel;
use App\Models\TransferModel;
use App\Models\DeffectModel;
use App\Models\BsModel;
use App\Models\BsMesinModel;
use App\Models\PerbaikanAreaModel;
use App\Models\EstSpkModel;
use App\Models\MonthlyMcModel;
use App\Models\UserModel;
use App\Models\AreaMachineModel;
use App\Models\HistorySmvModel;
use App\Models\AreaModel;
use App\Models\StockAreaModel;
use App\Models\SupermarketModel;
use App\Models\OutArea;
use App\Models\CylinderModel;
use App\Models\StockPdk;
use App\Models\DataCancelOrderModel;
use App\Models\HistoryRevisiModel;
use App\Models\PenggunaanJarum;
use App\Models\DetailAreaMachineModel;
use App\Models\DowntimeModel;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    protected $filters;
    protected $productModel;
    protected $produksiModel;
    protected $bookingModel;
    protected $orderModel;
    protected $ApsPerstyleModel;
    protected $liburModel;
    protected $KebutuhanMesinModel;
    protected $KebutuhanAreaModel;
    protected $MesinPlanningModel;
    protected $aksesModel;
    protected $DetailPlanningModel;
    protected $TanggalPlanningModel;
    protected $EstimatedPlanningModel;
    protected $orderServices;
    protected $MesinPerStyle;
    protected $MesinPernomor;
    protected $machinesModel;
    protected $ppsModel;
    protected $jarumModel;
    protected $estspk;
    protected $globalModel;
    protected $pengaduanModel;
    protected $role;
    protected $userModel;
    protected $cancelModel;
    protected $replyModel;
    protected $countNotif;
    protected $kebMc;
    protected $transferModel;
    protected $deffectModel;
    protected $bsModel;
    protected $bsMesinModel;
    protected $perbaikanModel;
    protected $areaMcModel;
    protected $perbaikanAreaModel;
    protected $historysmv;
    protected $areaModel;
    protected $stockArea;
    protected $supermarketModel;
    protected $outArea;
    protected $MonthlyMcModel;
    protected $cylinderModel;
    protected $historyRev;
    protected $cancelOrder;
    protected $stokPdk;
    protected $PenggunaanJarumModel;
    protected $MesinPerStyleModel;
    protected $downtimeModel;
    protected $detailAreaMc;
    protected $urlMaterial;
    protected $urlHris;
    protected $urlTls;
    protected $db;

    public function __construct() {}
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var array
     */
    protected $helpers = [];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    // protected $session;

    /**
     * @return void
     */
    public function initController(
        RequestInterface $request,
        ResponseInterface $response,
        LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);
        service('renderer')->setVar('materialApiUrl', api_url('material'));
        service('renderer')->setVar('hrisApiUrl', api_url('hris'));
        service('renderer')->setVar('tlsApiUrl', api_url('tls'));
        $this->db = \Config\Database::connect();
        $this->jarumModel = new DataMesinModel();
        $this->bookingModel = new BookingModel();
        $this->productModel = new ProductTypeModel();
        $this->produksiModel = new ProduksiModel();
        $this->orderModel = new OrderModel();
        $this->ApsPerstyleModel = new ApsPerstyleModel();
        $this->liburModel = new LiburModel();
        $this->KebutuhanMesinModel = new KebutuhanMesinModel();
        $this->KebutuhanAreaModel = new KebutuhanAreaModel();
        $this->MesinPlanningModel = new MesinPlanningModel();
        $this->aksesModel = new AksesModel();
        $this->DetailPlanningModel = new DetailPlanningModel();
        $this->TanggalPlanningModel = new TanggalPlanningModel();
        $this->EstimatedPlanningModel = new EstimatedPlanningModel();
        $this->MesinPerStyle = new MesinPerStyle();
        $this->MesinPernomor = new MesinPernomor();
        $this->machinesModel = new MachinesModel();
        $this->ppsModel = new PpsModel();
        $this->cancelModel = new cancelModel();
        $this->kebMc = new KebutuhanMesinModel();
        $this->transferModel = new TransferModel();
        $this->deffectModel = new DeffectModel();
        $this->bsModel = new BsModel();
        $this->bsMesinModel = new BsMesinModel();
        $this->perbaikanModel = new PerbaikanAreaModel();
        $this->areaMcModel = new AreaMachineModel();
        $this->perbaikanAreaModel = new PerbaikanAreaModel();
        $this->estspk = new EstSpkModel();
        $this->globalModel = new MonthlyMcModel();
        $this->userModel = new UserModel();
        $this->historysmv = new HistorySmvModel();
        $this->areaModel = new AreaModel();
        $this->stockArea = new StockAreaModel();
        $this->supermarketModel = new SupermarketModel();
        $this->outArea = new OutArea();
        $this->MonthlyMcModel = new MonthlyMcModel();
        $this->cylinderModel = new cylinderModel();
        $this->cancelOrder = new DataCancelOrderModel();
        $this->historyRev = new HistoryRevisiModel();
        $this->stokPdk = new StockPdk();
        $this->PenggunaanJarumModel = new PenggunaanJarum();
        $this->MesinPerStyleModel = new MesinPerStyle();
        $this->pengaduanModel = new PengaduanModel();
        $this->replyModel = new PengaduanReply();
        $this->detailAreaMc = new DetailAreaMachineModel();
        $this->downtimeModel = new DowntimeModel();
        if ($this->filters   = ['role' => [session()->get('role') . '']] != session()->get('role')) {
            return redirect()->to(base_url('/login'));
        }
        $this->isLogedin();

        $this->role = session()->get('role');
        $this->countNotif = $this->pengaduanModel->countNotif($this->role);
    }
}
