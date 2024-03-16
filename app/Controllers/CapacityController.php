<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class CapacityController extends BaseController
{
    protected $filters;

    public function __construct()
    {
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
    public function booking(){
        $data = [
            'title' => 'Capacity System',
            'active1' => '',
            'active2' => 'active',
            'active3' => '',
            'active4' => '',

        ];
        return view('Capacity/Booking/booking', $data);
    }
}
