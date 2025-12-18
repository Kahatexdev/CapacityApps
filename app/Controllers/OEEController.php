<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class OEEController extends BaseController
{
    public function index()
    {

        $data = [
            'role' => session()->get('role'),
            'title' => 'Capacity System',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
        ];
        return view(session()->get('role') . '/Oee/index', $data);
    }
}
