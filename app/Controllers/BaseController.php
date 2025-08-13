<?php

namespace App\Controllers;


use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

use App\Models\DataMesinModel;
use App\Models\EstSpkModel;
use App\Models\MonthlyMcModel;
use App\Models\PengaduanModel;
use App\Models\UserModel;
use App\Models\PengaduanReply;



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
    protected $jarumModel;
    protected $estspk;
    protected $globalModel;
    protected $pengaduanModel;
    protected $role;
    protected $userModel;
    protected $replyModel;
    protected $countNotif;
    public function __construct()
    {
        $this->jarumModel = new DataMesinModel();
        $this->estspk = new EstSpkModel();
        $this->globalModel = new MonthlyMcModel();
        $this->pengaduanModel = new PengaduanModel();
        $this->userModel = new UserModel();
        $this->role = session()->get('role');
        $this->replyModel = new PengaduanReply();
        $this->countNotif = $this->pengaduanModel->countNotif($this->role);
    }
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
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.

        // E.g.: $this->session = \Config\Services::session();
    }
}
