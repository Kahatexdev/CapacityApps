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
        if ($this->filters   = ['role' => ['capacity'], 'role' => ['user'], 'role' => ['planning']] != session()->get('role')) {
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
        // Set maximum execution time and memory limit
        ini_set('memory_limit', '512M');
        set_time_limit(180);

        $file = $this->request->getFile('excel_file');
        if ($file->isValid() && !$file->hasMoved()) {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
            $worksheet = $spreadsheet->getActiveSheet();

            $startRow = 18; // Ganti dengan nomor baris mulai
            $batchSize = 100; // Ukuran batch
            $batchData = [];
            $failedRows = []; // Array untuk menyimpan informasi baris yang gagal
            $db = \Config\Database::connect();

            foreach ($worksheet->getRowIterator($startRow) as $row) {
                $rowIndex = $row->getRowIndex();
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                $data = [];
                foreach ($cellIterator as $cell) {
                    $data[] = $cell->getValue();
                }

                if (!empty($data)) {
                    $batchData[] = ['rowIndex' => $rowIndex, 'data' => $data];
                    // Process batch
                    if (count($batchData) >= $batchSize) {
                        $this->processBatch($batchData, $db, $failedRows);
                        $batchData = []; // Reset batch data
                    }
                }
            }

            // Process any remaining data
            if (!empty($batchData)) {
                $this->processBatch($batchData, $db, $failedRows);
            }

            // Prepare notification message for failed rows
            if (!empty($failedRows)) {
                $failedRowsStr = implode(', ', $failedRows);
                $errorMessage = "Baris berikut gagal diimpor: $failedRowsStr";
                return redirect()->to(base_url('/user/produksi'))->with('error', $errorMessage);
            }

            return redirect()->to(base_url('/user/produksi'))->withInput()->with('success', 'Data Berhasil di Import');
        } else {
            return redirect()->to(base_url('/user/produksi'))->with('error', 'No data found in the Excel file');
        }
    }

    private function processBatch($batchData, $db, &$failedRows)
    {
        $db->transStart();
        foreach ($batchData as $batchItem) {
            $rowIndex = $batchItem['rowIndex'];
            $data = $batchItem['data'];

            try {
                $no_model = $data[21];
                $style = $data[4];
                $validate = [
                    'no_model' => $no_model,
                    'style' => $style
                ];
                $idAps = $this->ApsPerstyleModel->getIdProd($validate);
                if (!$idAps) {
                    if ($data[0] == null) {
                        continue; // Skip empty rows
                    } else {
                        $idMinus = $this->ApsPerstyleModel->getIdMinus($validate);
                        if ($idMinus) {
                            $idnext = $idMinus['idapsperstyle'];
                            $qtysisa = $idMinus['sisa'];
                            $deliv = $idMinus['delivery'];
                            $sisa = $qtysisa - $data[12];
                            $this->ApsPerstyleModel->update($idnext, ['sisa' => $sisa]);

                            $tglprod = $data[1];
                            $strReplace = str_replace('.', '-', $tglprod);
                            $dateTime = \DateTime::createFromFormat('d-m-Y', $strReplace);
                            $tgl_produksi = $dateTime->format('Y-m-d');
                            $bagian = $data[2];
                            $storage1 = $data[2];
                            $storage2 = $data[10] ?? '-';
                            $qtyerp = $data[12];
                            $qty = str_replace('-', '', $qtyerp);
                            $kategoriBs = $data[29] ?? '-';
                            $no_mesin = $data[25];
                            $shift = $data[30];
                            $no_box = $data[23];
                            $no_label = $data[22];
                            $area = $data[26];
                            $admin = session()->get('username');
                            $dataInsert = [
                                'tgl_produksi' => $tgl_produksi,
                                'idapsperstyle' => $idMinus['idapsperstyle'],
                                'bagian' => $bagian,
                                'storage_awal' => $storage1,
                                'storage_akhir' => $storage2,
                                'qty_produksi' => $qty,
                                'bs_prod' => 0,
                                'kategori_bs' => $kategoriBs,
                                'no_box' => $no_box,
                                'no_label' => $no_label,
                                'admin' => $admin,
                                'shift' => $shift,
                                'no_mesin' => $no_mesin,
                                'delivery' => $deliv,
                                'area' => $area
                            ];
                            $existingProduction = $this->produksiModel->existingData($dataInsert);
                            if (!$existingProduction) {
                                $this->produksiModel->insert($dataInsert);
                            } else {
                                $failedRows[] = $rowIndex; // Add to failed rows if production data already exists
                            }
                        } else {
                            $failedRows[] = $rowIndex;
                            continue;
                        }
                    }
                } else {
                    $id = $idAps['idapsperstyle'];
                    $sisaOrder = $idAps['sisa'];
                    $delivery = $idAps['delivery'];

                    $tglprod = $data[1];
                    $strReplace = str_replace('.', '-', $tglprod);
                    $dateTime = \DateTime::createFromFormat('d-m-Y', $strReplace);
                    $tgl_produksi = $dateTime->format('Y-m-d');
                    $bagian = $data[2];
                    $storage1 = $data[2];
                    $storage2 = $data[10] ?? '-';
                    $qtyerp = $data[12];
                    $qty = str_replace('-', '', $qtyerp);
                    $sisaQty = $sisaOrder - $qty;
                    if ($sisaQty < 0) {
                        $minus = $sisaQty;
                        $second = [
                            'no_model' => $no_model,
                            'style' => $style,
                            'sisa' => $sisaOrder
                        ];
                        $nextid = $this->ApsPerstyleModel->getIdBawahnya($second);
                        if ($nextid) {
                            $idnext = $nextid['idapsperstyle'];
                            $qtysisa = $nextid['sisa'];
                            $sisa = $qtysisa + $minus;
                            $this->ApsPerstyleModel->update($idnext, ['sisa' => $sisa]);

                            $sisaQty = 0;
                        } else {
                            $sisaQty = $minus;
                        }
                    }
                    $kategoriBs = $data[29] ?? '-';
                    $no_mesin = $data[25];
                    $shift = $data[30];
                    $no_box = $data[23];
                    $no_label = $data[22];
                    $area = $data[26];
                    $admin = session()->get('username');
                    $dataInsert = [
                        'tgl_produksi' => $tgl_produksi,
                        'idapsperstyle' => $id,
                        'bagian' => $bagian,
                        'storage_awal' => $storage1,
                        'storage_akhir' => $storage2,
                        'qty_produksi' => $qty,
                        'bs_prod' => 0,
                        'kategori_bs' => $kategoriBs,
                        'no_box' => $no_box,
                        'no_label' => $no_label,
                        'admin' => $admin,
                        'shift' => $shift,
                        'no_mesin' => $no_mesin,
                        'delivery' => $delivery,
                        'area' => $area
                    ];
                    $existingProduction = $this->produksiModel->existingData($dataInsert);
                    if (!$existingProduction) {
                        $this->produksiModel->insert($dataInsert);
                        $this->ApsPerstyleModel->update($id, ['sisa' => $sisaQty]);
                    } else {
                        $idexist = $existingProduction['id_produksi'];
                        $sumqty = $existingProduction['qty_produksi'] + $qty;
                        $this->produksiModel->update($idexist, ['qty_produksi' => $sumqty]);
                        $this->ApsPerstyleModel->update($id, ['sisa' => $sisaQty]);

                        // $failedRows[] = $rowIndex; // Add to failed rows if production data already exists
                    }
                }
            } catch (\Exception $e) {
                $failedRows[] = $rowIndex;
            }
        }
        $db->transComplete();
    }

    public function viewProduksi()
    {
        $bulan = date('m');
        $month = date('F');
        $totalMesin = $this->jarumModel->getArea();
        $dataProduksi = $this->produksiModel->getProduksiPerhari($bulan);

        $produksiPerArea = [];
        foreach ($totalMesin as $area) {
            $produksiPerArea[$area] = $this->produksiModel->getProduksiPerArea($bulan, $area);
        }

        $data = [
            'title' => 'Data Produksi',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => 'active',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'produksiArea' => $produksiPerArea,
            'Area' => $totalMesin,
            'Produksi' => $dataProduksi,
            'bulan' => $month,

        ];
        return view('Capacity/Produksi/produksi', $data);
    }
    public function progressData($noModel)
    {
        $pdkProgress = $this->ApsPerstyleModel->getProgress($noModel);
        return json_encode($pdkProgress);
    }
    public function produksiAreaChart()
    {
        $bulan = date('m');
        $month = date('F');
        $totalMesin = $this->jarumModel->getArea();
        $produksiPerArea = [];
        foreach ($totalMesin as $area) {
            $produksiPerArea[$area] = $this->produksiModel->getProduksiPerArea($bulan, $area);
        }
        return json_encode($produksiPerArea);
    }
    public function viewProduksiPlan()
    {
        $bulan = date('m');
        $month = date('F');
        $totalMesin = $this->jarumModel->getArea();
        $dataProduksi = $this->produksiModel->getProduksiPerhari($bulan);
        // $pdkProgress = $this->ApsPerstyleModel->getProgress($noModel);
        $produksiPerArea = [];
        foreach ($totalMesin as $area) {
            $produksiPerArea[$area] = $this->produksiModel->getProduksiPerArea($bulan, $area);
        }

        $data = [
            'title' => 'Data Produksi',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => 'active',
            'active7' => '',
            'produksiArea' => $produksiPerArea,
            'Area' => $totalMesin,
            'Produksi' => $dataProduksi,
            'bulan' => $month,
            // 'progress' => $pdkProgress
        ];
        return view('Planning/Produksi/produksi', $data);
    }
}
