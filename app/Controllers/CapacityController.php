<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Week;
use PhpOffice\PhpSpreadsheet\IOFactory;

class CapacityController extends BaseController
{


    public function __construct()
    {
        if ($this->filters   = ['role' => ['capaciity']] != session()->get('role')) {
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
        $orderJalan = $this->bookingModel->getOrderJalan();
        $terimaBooking = $this->bookingModel->getBookingMasuk();
        $mcJalan = $this->jarumModel->getJalanMesinPerArea();
        $totalMc = $this->jarumModel->totalMc();
        $bulan = date('m');
        $totalMesin = $this->jarumModel->getJalanMesinPerArea();
        $jarum = $this->jarumModel->jarumPerArea();
        $area = $this->jarumModel->getArea();

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
            'jalan' => $orderJalan,
            'TerimaBooking' => $terimaBooking,
            'mcJalan' => $mcJalan,
            'totalMc' => $totalMc,
            'Area' => $totalMesin,
            'order' => $this->ApsPerstyleModel->getTurunOrder($bulan),
            'jarum' => $jarum,
            'area' => $area,

        ];
        return view(session()->get('role') . '/index', $data);
    }

    public function inputLibur()
    {
        $tanggal = $this->request->getPost('tgl_libur');
        $nama = $this->request->getPost('nama');
        $data = [
            'role' => session()->get('role'),
            'tanggal' => $tanggal,
            'nama' => $nama
        ];
        $insert = $this->liburModel->insert($data);
        if ($insert) {
            return redirect()->to(base_url(session()->get('role') . ''))->withInput()->with('success', 'Tanggal Berhasil Di Input');
        } else {
            return redirect()->to(base_url(session()->get('role') . ''))->withInput()->with('error', 'Gagal Input Tanggal');
        }
    }
}
