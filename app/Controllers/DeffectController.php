<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\DataMesinModel;
use App\Models\OrderModel;
use App\Models\BookingModel;
use App\Models\ProductTypeModel;
use App\Models\ApsPerstyleModel;
use App\Models\ProduksiModel;
use App\Models\LiburModel;
use App\Models\DeffectModel;
use App\Models\BsModel;
use App\Models\BsMesinModel;

use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Week;
use PhpOffice\PhpSpreadsheet\IOFactory;


class DeffectController extends BaseController
{
    protected $filters;
    protected $jarumModel;
    protected $productModel;
    protected $produksiModel;
    protected $bookingModel;
    protected $orderModel;
    protected $ApsPerstyleModel;
    protected $liburModel;
    protected $deffectModel;
    protected $BsModel;
    protected $BsMesinModel;
    protected $db;


    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->jarumModel = new DataMesinModel();
        $this->bookingModel = new BookingModel();
        $this->productModel = new ProductTypeModel();
        $this->produksiModel = new ProduksiModel();
        $this->orderModel = new OrderModel();
        $this->ApsPerstyleModel = new ApsPerstyleModel();
        $this->deffectModel = new DeffectModel();
        $this->liburModel = new LiburModel();
        $this->BsModel = new BsModel();
        $this->BsMesinModel = new BsMesinModel();

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

    public function datadeffect()
    {

        $master = $this->deffectModel->findAll();
        //$databs = $this->BsModel->getDataBs();
        $dataBuyer = $this->orderModel->getBuyer();


        $data = [
            'role' => session()->get('role'),
            'title' => session()->get('role') . ' System',
            'active1' => 'active',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'kode' => $master,
            'dataBuyer' => $dataBuyer,

            //'databs' => $databs
        ];
        return view(session()->get('role') . '/Deffect/databs', $data);
    }
    public function inputKode()
    {

        $kode = $this->request->getPost('kode');
        $keterangan = $this->request->getPost('keterangan');
        $data = [
            'kode_deffect' => $kode,
            'Keterangan' => $keterangan
        ];
        try {
            $input = $this->deffectModel->insert($data); // Assuming save() is a better method name
            if ($input == false) {
                return redirect()->to(session()->get('role') . '/datadeffect')->with('success', 'Data Deffect Berhasil Di input');
            } else {
                return redirect()->to(session()->get('role') . '/datadeffect')->with('error', 'Data Deffect Gagal Di input');
            }
        } catch (\Exception $e) {
            return redirect()->to(session()->get('role') . '/datadeffect')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function viewDataBs()
    {
        $master = $this->deffectModel->findAll();
        $dataBuyer = $this->orderModel->getBuyer();

        $awal = $this->request->getPost('awal');
        $akhir = $this->request->getPost('akhir');
        $pdk = $this->request->getPost('pdk');
        $area = $this->request->getPost('area');
        $buyer = $this->request->getPost('buyer');
        // dd($buyer);
        $theData = [
            'awal' => $awal,
            'akhir' => $akhir,
            'pdk' => $pdk,
            'area' => $area,
            'buyer' => $buyer,
        ];
        $getData = $this->BsModel->getDataBsFilter($theData);
        $total = $this->BsModel->totalBs($theData);
        $chartData = $this->BsModel->chartData($theData);
        $data = [
            'role' => session()->get('role'),
            'title' => ' Data BS',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'kode' => $master,
            'databs' => $getData,
            'awal' => $awal,
            'akhir' => $akhir,
            'pdk' => $pdk,
            'area' => $area,
            'totalbs' => $total,
            'chart' => $chartData,
            'dataBuyer' => $dataBuyer,
        ];

        return view(session()->get('role') . '/Deffect/bstabel', $data);
    }

    public function resetbs()
    {
        $pdk = $this->request->getPost('pdk');

        $idaps = $this->ApsPerstyleModel->getIdAps($pdk);
        // Cek apakah $idaps tidak kosong
        if (!empty($idaps)) {
            $qtyBs = $this->BsModel->getTotalBs($idaps);

            foreach ($qtyBs as $idap) {
                $bs = $idap['qty'];
                $sisa = $this->ApsPerstyleModel->getSisaOrder($idap['idapsperstyle']);
                $newSisa = $sisa - $bs;
                $this->ApsPerstyleModel->update($idap['idapsperstyle'], ['sisa' => $newSisa]);
            }

            $this->BsModel->deleteSesuai($idaps);
        } else {
            // Jika $idaps kosong, lakukan penanganan lain, misalnya logging atau redirect dengan pesan error
            return redirect()->to(base_url(session()->get('role') . '/datadeffect'))->withInput()->with('error', 'Tidak ada data yang ditemukan untuk di-reset.');
        }
        return redirect()->to(base_url(session()->get('role') . '/datadeffect'))->withInput()->with('success', 'Data Berhasil di reset');
    }

    public function resetbsarea()
    {
        $area = $this->request->getPost('area');
        $awal = $this->request->getPost('awal');
        $akhir = $this->request->getPost('akhir');

        $idaps = $this->BsModel->getDataForReset($area, $awal, $akhir);
        if (!empty($idaps)) {
            // Memulai transaksi
            $this->db->transBegin();

            $failedIds = []; // Array untuk menyimpan ID yang gagal

            foreach ($idaps as $data) {
                $qtyBs = intval($data['qty']);
                $idbs = $data['idbs'];
                $id = $data['idapsperstyle'];
                $dataOrder = $this->ApsPerstyleModel->getSisaOrder($id);
                $newOrder = $dataOrder - $qtyBs;

                $updateOrder = $this->ApsPerstyleModel->update($id, ['sisa' => $newOrder]);
                $this->BsModel->delete($idbs);
            }

            if ($this->db->transStatus() === FALSE || !empty($failedIds)) {
                // Rollback jika ada yang gagal
                $this->db->transRollback();

                // Menyusun pesan kesalahan
                $errorMsg = 'Gagal melakukan reset pada ID: ' . implode(', ', $failedIds);
                return redirect()->to(base_url(session()->get('role') . '/datadeffect'))->withInput()->with('error', $errorMsg);
            } else {
                // Commit transaksi jika semua sukses
                $this->db->transCommit();
                return redirect()->to(base_url(session()->get('role') . '/datadeffect'))->withInput()->with('success', 'Data Berhasil di-reset');
            }
        } else {
            // Jika $idaps kosong
            return redirect()->to(base_url(session()->get('role') . '/datadeffect'))->withInput()->with('error', 'Tidak ada data yang ditemukan untuk di-reset.');
        }
    }
    public function getBsMesin()
    {
        $bulan = $this->request->getGet('bulan');
        $tahun = $this->request->getGet('tahun');
        $area  = $this->request->getGet('area');

        if (!$bulan || !$tahun) {
            return $this->response->setJSON(['error' => 'Bulan dan Tahun wajib diisi']);
        }

        try {
            $filters = [
                'bulan' => $bulan,
                'tahun' => $tahun,
                'area'  => $area
            ];
            $data = $this->BsMesinModel->getbsMesinDaily($filters);

            return $this->response->setJSON($data);
        } catch (\Exception $e) {
            return $this->response->setJSON(['error' => $e->getMessage()]);
        }
    }
}
