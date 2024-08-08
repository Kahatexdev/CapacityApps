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

    public function __construct()
    {
        $this->jarumModel = new DataMesinModel();
        $this->bookingModel = new BookingModel();
        $this->productModel = new ProductTypeModel();
        $this->produksiModel = new ProduksiModel();
        $this->orderModel = new OrderModel();
        $this->ApsPerstyleModel = new ApsPerstyleModel();
        $this->deffectModel = new DeffectModel();
        $this->liburModel = new LiburModel();
        $this->BsModel = new BsModel();
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

        $awal = $this->request->getPost('awal');
        $akhir = $this->request->getPost('akhir');
        $pdk = $this->request->getPost('pdk');
        $area = $this->request->getPost('area');
        $theData = [
            'awal' => $awal,
            'akhir' => $akhir,
            'pdk' => $pdk,
            'area' => $area
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
            'chart' => $chartData
        ];

        return view(session()->get('role') . '/Deffect/bstabel', $data);
    }
}
