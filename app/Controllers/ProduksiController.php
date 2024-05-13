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
use PhpOffice\PhpSpreadsheet\IOFactory;

class ProduksiController extends BaseController
{
    protected $filters;
    protected $jarumModel;
    protected $productModel;
    protected $produksiModel;
    protected $bookingModel;
    protected $orderModel;
    protected $ApsPerstyleModel;
    protected $liburModel;
    public function __construct()
    {


        $this->jarumModel = new DataMesinModel();
        $this->bookingModel = new BookingModel();
        $this->productModel = new ProductTypeModel();
        $this->produksiModel = new ProduksiModel();
        $this->orderModel = new OrderModel();
        $this->ApsPerstyleModel = new ApsPerstyleModel();
        if ($this->filters   = ['role' => ['capacity'], 'role' => ['user']] != session()->get('role')) {
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
        //
    }
    public function produksi()
    {
        $bulan = date('m');
        $month = date('F');
        $totalMesin = $this->jarumModel->getArea();
        $dataProduksi = $this->produksiModel->getProduksiPerhari($bulan);
        $data = [
            'title' => 'Data Produksi',
            'active1' => '',
            'active2' => 'active',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',

            'Area' => $totalMesin,
            'Produksi' => $dataProduksi,
            'bulan' => $month
        ];
        return view('User/produksi', $data);
    }
    public function produksiPerArea($area)
    {
        $produksi = $this->produksiModel->getProduksi($area);
        $data = [
            'title' => 'Data Produksi',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => 'active',
            'active5' => '',
            'active6' => '',
            'active7' => '',

            'produksi' => $produksi,
            'area' => $area
        ];
        return view('Capacity/Produksi/detail', $data);
    }
    public function importproduksi()
    {
        $file = $this->request->getFile('excel_file');
        if ($file->isValid() && !$file->hasMoved()) {
            $spreadsheet = IOFactory::load($file);
            $data = $spreadsheet->getActiveSheet();

            $startRow = 18; // Ganti dengan nomor baris mulai
            foreach ($spreadsheet->getActiveSheet()->getRowIterator($startRow) as $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                $data = [];
                foreach ($cellIterator as $cell) {
                    $data[] = $cell->getValue();
                }
                if (!empty($data)) {
                    $no_model = $data[20];
                    $style = $data[4];
                    $no_order = $data[19];
                    $validate = [
                        'no_model' =>  $no_model,
                        'style' => $style
                    ];
                    $idAps = $this->ApsPerstyleModel->getIdProd($validate);
                    if (!$idAps) {
                        return redirect()->to(base_url('/user/produksi'))->with('error', 'Data Order Tidak Ditemukan');
                    } else {
                        $id = $idAps['idapsperstyle'];
                        $sisaOrder = $idAps['sisa'];
                        $delivery = $idAps['delivery'];
                        if ($data[0] == null) {
                            break;
                        } else {
                            $tglprod = $data[1];
                            $strReplace = str_replace('.', '-', $tglprod);
                            $dateTime   = \DateTime::createFromFormat('d-m-Y', $strReplace);
                            $tgl_produksi =  $dateTime->format('Y-m-d');
                            $bagian     = $data[2];
                            $storage1   = $data[2];
                            $storage2   = $data[10] ?? '-';
                            $qtyerp        = $data[12];
                            $qty = str_replace('-', '', $qtyerp);
                            $sisaQty = $sisaOrder - $qty;
                            $kategoriBs = $data[29] ?? '-';
                            $no_mesin = $data[25];
                            $shift = $data[30];
                            $no_box     = $data[23];
                            $no_label   = $data[22];
                            $area = $data[26];
                            $admin      = session()->get('username');
                            $dataInsert = [
                                'tgl_produksi'            => $tgl_produksi,
                                'idapsperstyle'         => $id,
                                'bagian'                => $bagian,
                                'storage_awal'          => $storage1,
                                'storage_akhir'         => $storage2,
                                'qty_produksi'              => $qty,
                                'bs_prod'               => 0,
                                'kategori_bs'           => $kategoriBs,
                                'no_box'                => $no_box,
                                'no_label'              => $no_label,
                                'admin'                 => $admin,
                                'shift'                 => $shift,
                                'no_mesin'              => $no_mesin,
                                'delivery'              => $delivery,
                                'area' => $area
                            ];
                            // $existingProduction = $this->produksiModel->existingData($dataInsert);
                            // if (!$existingProduction) {
                            $this->produksiModel->insert($dataInsert);
                            $this->ApsPerstyleModel->update($id, ['sisa' => $sisaQty]);
                            //}
                        }
                    }
                }
            }
            return redirect()->to(base_url('/user/produksi'))->withInput()->with('success', 'Data Berhasil di Import');
        } else {
            return redirect()->to(base_url('/user/produksi'))->with('error', 'No data found in the Excel file');
        }
    }

    public function viewProduksi()
    {
        $bulan = date('m');
        $month = date('F');
        $totalMesin = $this->jarumModel->getArea();
        $dataProduksi = $this->produksiModel->getProduksiPerhari($bulan);
        $pdkProgress = $this->ApsPerstyleModel->getProgress();
        $data = [
            'title' => 'Data Produksi',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => 'active',
            'active5' => '',
            'active6' => '',
            'active7' => '',

            'Area' => $totalMesin,
            'Produksi' => $dataProduksi,
            'bulan' => $month,
            'progress' => $pdkProgress
        ];
        return view('Capacity/Produksi/produksi', $data);
    }
    public function progressData()
    {
        $pdkProgress = $this->ApsPerstyleModel->getProgress();
        return json_encode($pdkProgress);
    }
}
