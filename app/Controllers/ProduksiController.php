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
use PhpParser\Node\Stmt\Else_;

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
        if ($this->filters   = ['role' => [session()->get('role') . ''], 'role' => ['user'], 'role' => ['planning']] != session()->get('role')) {
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
        $dataPdk = $this->ApsPerstyleModel->getPdkProduksi();

        $data = [
            'role' => session()->get('role'),
            'title' => 'Data Produksi',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => 'active',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'pdk' => $dataPdk,
            'Area' => $totalMesin,
            'Produksi' => $dataProduksi,
            'bulan' => $month
        ];
        return view(session()->get('role') . '/produksi', $data);
    }
    public function produksiPerArea($area)
    {
        $bulan = date('m');
        $produksi = $this->produksiModel->getProduksi($area, $bulan);
        $data = [
            'role' => session()->get('role'),
            'title' => 'Data Produksi',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => 'active',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'bulan' => date('M-Y'),
            'produksi' => $produksi,
            'area' => $area
        ];
        return view(session()->get('role') . '/Produksi/detail', $data);
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
                $data = ['role' => session()->get('role'),];
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
                return redirect()->to(base_url(session()->get('role') . '/produksi'))->with('error', $errorMessage);
            }

            return redirect()->to(base_url(session()->get('role') . '/produksi'))->withInput()->with('success', 'Data Berhasil di Import');
        } else {
            return redirect()->to(base_url(session()->get('role') . '/produksi'))->with('error', 'No data found in the Excel file');
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
                            $no_mesin = $data[25] ?? 0;
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
                            dd($dataInsert);
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
                    $no_mesin = $data[25] ?? 0;
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
                        $insert =  $this->produksiModel->insert($dataInsert);
                        if ($insert) {
                            $this->ApsPerstyleModel->update($id, ['sisa' => $sisaQty]);
                        } else {
                            $failedRows[] = $rowIndex;
                            continue;
                        }
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
            'role' => session()->get('role'),
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
        return view(session()->get('role') . '/Produksi/produksi', $data);
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
            'role' => session()->get('role'),
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

    public function importproduksinew()
    {
        // Set maximum execution time and memory limit
        ini_set('memory_limit', '512M');
        set_time_limit(180);

        $file = $this->request->getFile('excel_file');
        if ($file->isValid() && !$file->hasMoved()) {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
            $worksheet = $spreadsheet->getActiveSheet();

            $startRow = 10; // Ganti dengan nomor baris mulai
            $batchSize = 100; // Ukuran batch
            $batchData = [];
            $failedRows = []; // Array untuk menyimpan informasi baris yang gagal
            $db = \Config\Database::connect();

            foreach ($worksheet->getRowIterator($startRow) as $row) {
                $rowIndex = $row->getRowIndex();
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                $data = ['role' => session()->get('role'),];
                foreach ($cellIterator as $cell) {
                    $data[] = $cell->getValue();
                }

                if (!empty($data)) {
                    $batchData[] = ['rowIndex' => $rowIndex, 'data' => $data];
                    // Process batch
                    if (count($batchData) >= $batchSize) {
                        $this->processBatchnew($batchData, $db, $failedRows);
                        $batchData = []; // Reset batch data
                    }
                }
            }

            // Process any remaining data
            if (!empty($batchData)) {
                $this->processBatchnew($batchData, $db, $failedRows);
            }

            // Prepare notification message for failed rows
            if (!empty($failedRows)) {
                $failedRowsStr = implode(', ', $failedRows);
                $errorMessage = "Baris berikut gagal diimpor: $failedRowsStr";
                return redirect()->to(base_url(session()->get('role') . '/produksi'))->with('error', $errorMessage);
            }

            return redirect()->to(base_url(session()->get('role') . '/produksi'))->withInput()->with('success', 'Data Berhasil di Import');
        } else {
            return redirect()->to(base_url(session()->get('role') . '/produksi'))->with('error', 'No data found in the Excel file');
        }
    }

    private function processBatchnew($batchData, $db, &$failedRows)
    {
        $db->transStart();
        foreach ($batchData as $batchItem) {
            $rowIndex = $batchItem['rowIndex'];
            $data = $batchItem['data'];

            try {
                $no_model = $data[2];
                $style = $data[3];
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
                            $sisa = $qtysisa - $data[14];
                            $this->ApsPerstyleModel->update($idnext, ['sisa' => $sisa]);

                            $tglprod = $data[0];
                            // $strReplace = str_replace('.', '-', $tglprod);
                            // $dateTime = \DateTime::createFromFormat('d-m-Y', $strReplace);
                            // $tgl_produksi = $dateTime->format('Y-m-d');
                            $bagian = "-";
                            $storage1 = "-";
                            $storage2 = "-" ?? '-';
                            $qtyerp = $data[14];
                            $qty = str_replace('-', '', $qtyerp);
                            $kategoriBs = "-" ?? '-';
                            $no_mesin = $data[8] ?? 0;
                            $shift = "-";
                            $no_box = $data[12];
                            $no_label = $data[13];
                            $area = "-";
                            $admin = session()->get('username');
                            $dataInsert = [
                                'tgl_produksi' => $tglprod,
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

                                //$failedRows[] = $rowIndex . "duplikat"; // Add to failed rows if production data already exists
                            }
                        } else {
                            $failedRows[] = "style tidak ditemukan" . $rowIndex;
                            continue;
                        }
                    }
                } else {
                    $id = $idAps['idapsperstyle'];
                    $sisaOrder = $idAps['sisa'];
                    $delivery = $idAps['delivery'];

                    $tglprod = $data[0];
                    // $strReplace = str_replace('.', '-', $tglprod);
                    // $dateTime = \DateTime::createFromFormat('d-m-Y', $strReplace);
                    // $tgl_produksi = $dateTime->format('Y-m-d');
                    $bagian = "-";
                    $storage1 = "-";
                    $storage2 = "-" ?? '-';
                    $qtyerp = $data[14];
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
                    $kategoriBs = "-" ?? '-';
                    $no_mesin = $data[8] ?? 0;
                    $shift = "-";
                    $no_box = $data[12];
                    $no_label = $data[13];
                    $area = "-";
                    $admin = session()->get('username');
                    $dataInsert = [
                        'tgl_produksi' => $tglprod,
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
                        $insert =  $this->produksiModel->insert($dataInsert);
                        if ($insert) {
                            $this->ApsPerstyleModel->update($id, ['sisa' => $sisaQty]);
                        } else {
                            $failedRows[] = $rowIndex;
                            continue;
                        }
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

    public function resetproduksi()
    {
        $pdk = $this->request->getPost('pdk');

        $idaps = $this->ApsPerstyleModel->getIdAps($pdk);
        $this->ApsPerstyleModel->resetSisa($pdk);
        $this->produksiModel->deleteSesuai($idaps);
        return redirect()->to(base_url(session()->get('role') . '/produksi'))->withInput()->with('success', 'Data Berhasil di reset');
    }

    public function summaryProdPerTanggal() {
        $pdk = $this->request->getPost('pdk');
        $awal = $this->request->getPost('awal');
        $akhir = $this->request->getPost('akhir');
        
        $data = [
            'pdk' => $pdk,
            'awal' => $awal,
            'akhir' => $akhir,
        ];

        $dataSummaryPertgl = $this->produksiModel->getdataSummaryPertgl($data);
        // supaya data menjadi unik
        $tgl_produksi = [];
        foreach ($dataSummaryPertgl as $item) {
            $tgl_produksi[$item['tgl_produksi']] = $item['tgl_produksi'];
        }
        $tgl_produksi = array_values($tgl_produksi);
        // Sort ASC
        sort($tgl_produksi);
        
        $uniqueData = [];
        foreach ($dataSummaryPertgl as $item) {
            $key = $item['machinetypeid'] . '-' . $item['mastermodel'] . '-' . $item['size'];
            $qty =+ $item['qty'];
            if (!isset($uniqueData[$key])) {
                $uniqueData[$key] = [
                    'machinetypeid' => $item['machinetypeid'],
                    'mastermodel' => $item['mastermodel'],
                    'size' => $item['size'],
                    'qty' => 0,
                    'running' => 0,
                    'ttl_prod' => 0,
                    'ttl_jlmc' => 0,
                ];
            }
            $uniqueData[$key]['qty'] += $item['qty']/24;
            $uniqueData[$key]['running'] += $item['running'];
            $uniqueData[$key]['ttl_prod'] += $item['qty_produksi']/24;
            $uniqueData[$key]['ttl_jlmc'] += $item['jl_mc'];
        }
        // Sort ASC
        sort($uniqueData);
        // dd($dataSummaryPertgl);
        $data2 = [
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => 'active',
            'active7' => '',
            'dataSummaryPertgl' => $dataSummaryPertgl,
            'tglProdUnik' => $tgl_produksi,
            'uniqueData' => $uniqueData,
            'role' => session()->get('role'),
            'title' => $pdk,
        ];
        return view('Capacity/Produksi/summaryPertanggal', $data2);
    }
}

