<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class PengaduanController extends BaseController
{
    public function index()
    {
        $role = $this->role;
        $userId = session()->get('id_user');
        $list = $this->pengaduanModel->getPengaduan($userId, $role) ?? null;
        $listRole = $this->userModel->getListRole();
        // dd($listRole);
        $data = [
            'role' => session()->get('role'),
            'title' => 'Capacity System',
            'active1' => 'active',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'pengaduan' => $list
        ];
        return view($role . '/pengaduan/index', $data);
    }
}
