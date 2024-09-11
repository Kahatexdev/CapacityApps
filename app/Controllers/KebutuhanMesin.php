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
    public function inputMesinBooking()
    {
        $desk = $this->request->getPost("deskripsi");
        $cek = [
            'desk' => $desk,
            'judul' => $this->request->getPost("judul"),
            'jarum' => $this->request->getPost("jarum"),
        ];


        $data = [
            'role' => session()->get('role'),
            'judul' => $this->request->getPost("judul"),
            'jarum' => $this->request->getPost("jarum"),
            'mesin' => $this->request->getPost("totalMc"),
            'jumlah_hari' => $this->request->getPost("hari"),
            'tanggal_awal' => $this->request->getPost("tgl_awal"),
            'tanggal_akhir' => $this->request->getPost("tgl_akhir"),
            'start_mesin' => $this->request->getPost("startMc"),
            'stop_mesin' => $this->request->getPost("stopMc"),
            'deskripsi' => $this->request->getPost("deskripsi"),
            'sisa' => $this->request->getPost("sisa")
        ];
        $exist = $this->kebMC->cekData($cek);
        if ($exist) {
            $this->kebMC->update($exist['id'], $data);
            return redirect()->to(base_url(session()->get('role') . ''))->withInput()->with('success', 'Data ' . $this->request->getPost('judul') . ' Berhasil Di Perbarui');
        } else {
            $insert = $this->kebMC->insert($data);

            if ($insert) {
                return redirect()->to(base_url(session()->get('role') . ''))->withInput()->with('success', 'Data Berhasil Di input');
            } else {
                return redirect()->to(base_url(session()->get('role') . ''))->withInput()->with('error', 'Data Gagal Di input');
            }
        }
    }
}
