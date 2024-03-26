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
    public function __construct()
    {
        $this->jarumModel = new DataMesinModel();
        $this->bookingModel = new BookingModel();
        $this->productModel = new ProductTypeModel();
        $this->produksiModel = new ProduksiModel();
        $this->orderModel = new OrderModel();
        $this->ApsPerstyleModel = new ApsPerstyleModel();
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
        //
    }
    public function produksi()
    {
        $totalMesin = $this->jarumModel->getArea();
        $data = [
            'title' => 'Data Produksi',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => 'active',
            'active5' => '',
            'active6' => '',
            'Area' => $totalMesin
        ];
        return view('Capacity/Produksi/produksi', $data);
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
            $startRow = 2; // Ganti dengan nomor baris mulai
            foreach ($spreadsheet->getActiveSheet()->getRowIterator($startRow) as $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                $data = [];
                foreach ($cellIterator as $cell) {
                    $data[] = $cell->getValue();
                }
                if (!empty($data)) {
                    $no_model = $data[1];
                    $del = $data[5];
                    $unixTime = ($del - 25569) * 86400;
                    $delivery = date('Y-m-d', $unixTime);
                    $style = $data[7];
                    $validate = [
                        'no_model' =>  $no_model,
                        'delivery' => $delivery,
                        'style' => $style
                    ];
                    $idAps = $this->ApsPerstyleModel->getId($validate);
                    $id = $idAps['idapsperstyle'];

                    if ($data[0] == null) {
                        break;
                    } else {
                        $tglprod = $data[23];
                        $unixProd = ($tglprod - 25569) * 86400;
                        $tgl_produksi = date('Y-m-d', $unixProd);
                        $qtyProd = $data[15];
                        $sisaQty = $data[12];
                        $insert = [
                            'idapsperstyle' => $id,
                            'tgl_produksi' => $tgl_produksi,
                            'qty_produksi' => $qtyProd
                        ];
                        $existingProduction = $this->produksiModel->existingData($insert);
                        if (!$existingProduction) {
                            $this->produksiModel->insert($insert);
                            $this->ApsPerstyleModel->update($id, ['sisa' => $sisaQty]);
                        }
                    }
                }
            }
            return redirect()->to(base_url('/capacity/dataproduksi'))->withInput()->with('success', 'Data Berhasil di Import');
        } else {
            return redirect()->to(base_url('/capacity/dataproduksi'))->with('error', 'No data found in the Excel file');
        }
    }
}
