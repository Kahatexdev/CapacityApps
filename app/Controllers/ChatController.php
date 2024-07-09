<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class ChatController extends BaseController
{
    public function pesan()
    {
        $role = $this->session->userdata('role') ?? 'default';
        $data['role'] = $role;
        return view('sudo/chat/index', $data);
    }
}
