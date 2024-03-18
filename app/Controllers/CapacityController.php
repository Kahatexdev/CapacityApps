<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\DataMesinModel;

class CapacityController extends BaseController
{
    protected $filters;
    protected $jarumModel;

    public function __construct()
    {
        $this->jarumModel = new DataMesinModel();
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
            'title' => 'Capacity System',
            'active1' => 'active',
            'active2' => '',
            'active3' => '',
            'active4' => '',

        ];
        return view('Capacity/index', $data);
    }


    public function booking()
    {
        $dataJarum = $this->jarumModel->getJarum();
        $totalMesin = $this->jarumModel->getTotalMesinByJarum();
        $data = [
            'title' => 'Data Booking',
            'active1' => '',
            'active2' => 'active',
            'active3' => '',
            'active4' => '',
            'Jarum' => $dataJarum,
            'TotalMesin' => $totalMesin,
        ];
        return view('Capacity/Booking/booking', $data);
    }
    public function bookingPerJarum($jarum)
    {

        $data = [
            'title' => 'Data Booking',
            'active1' => '',
            'active2' => 'active',
            'active3' => '',
            'active4' => '',
            'jarum' => $jarum

        ];
        return view('Capacity/Booking/jarum', $data);
    }
    public function inputbooking()
    {
        $tglbk = $this->request->getPost("tgl_booking");
        $no_order = $this->request->getPost("no_order");
        $no_pdk = $this->request->getPost("no_pdk");
        $desc = $this->request->getPost("desc");
        $seam = $this->request->getPost("seam");
        $opd = $this->request->getPost("opd");
        $shipment = $this->request->getPost("shipment");
        $qty = $this->request->getPost("qty");

        $validate = [
            'no_order' => $no_order,
            'no_pdk' => $no_pdk
        ];


        $input = [
            'tgl_booking' => $tglbk,

        ];
    }
}
