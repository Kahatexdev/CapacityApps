<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\KebutuhanMesinModel;



class KebutuhanMesin extends BaseController

{
    protected $kebMC;
    public function __construct()
    {
        $this->kebMC = new KebutuhanMesinModel();
    }
    public function inputMesinOrder()
    {
        $data = [
            'judul' => $this->request->getPost("judul"),
            'jarum' => $this->request->getPost("jarum"),
            'mesin' => $this->request->getPost("totalMc"),
            'jumlah_hari' => $this->request->getPost("hari"),
            'tanggal_awal' => $this->request->getPost("tgl_awal"),
            'tanggal_akhir' => $this->request->getPost("tgl_akhir"),
            'deskripsi' => 'ORDER'
        ];
        $insert = $this->kebMC->insert($data);

        if ($insert) {
            return redirect()->to(base_url('/capacity/planningorder'))->withInput()->with('success', 'Data Berhasil Di input');
        } else {
            return redirect()->to(base_url('/capacity/planningorder'))->withInput()->with('error', 'Data Gagal Di input');
        }
    }
}