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
use DateTime;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\BsModel;
use CodeIgniter\Controller;
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
    protected $BsModel;
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
        $this->BsModel = new BsModel();

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
        $produksi = $this->produksiModel->getProduksiHarianArea();
        $data = [
            'role' => session()->get('role'),
            'title' => 'Data Produksi',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'pdk' => $dataPdk,
            'Area' => $totalMesin,
            'Produksi' => $dataProduksi,
            'bulan' => $month,
            'produksi' => $produksi
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
        $role = session()->get('role');
        $bulan = date('m');
        $month = date('F');
        $totalMesin = $this->jarumModel->getArea();
        $dataProduksi = $this->produksiModel->getProduksiPerhari($bulan);

        $dataBuyer = $this->orderModel->getBuyer();
        $dataArea = $this->jarumModel->getArea();
        $dataJarum = $this->jarumModel->getJarum();

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
            'active6' => '',
            'active7' => '',
            'produksiArea' => $produksiPerArea,
            'Area' => $totalMesin,
            'Produksi' => $dataProduksi,
            'bulan' => $month,
            'buyer' => $dataBuyer,
            'area' => $dataArea,
            'jarum' => $dataJarum,
        ];
        if ($role == 'user') {
            return view(session()->get('role') . '/produksi', $data);
        } else {
            return view(session()->get('role') . '/Produksi/produksi', $data);
        }
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
            'active6' => '',
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
        set_time_limit(500);

        $file = $this->request->getFile('excel_file');
        if ($file->isValid() && !$file->hasMoved()) {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
            $worksheet = $spreadsheet->getActiveSheet();

            $startRow = 10; // Ganti dengan nomor baris mulai
            $batchSize = 100; // Ukuran batch
            $batchData = [];
            $failedRows = []; // Array untuk menyimpan informasi baris yang gagal
            $db = \Config\Database::connect();
            $areal = $worksheet->getCell('A3')->getValue();
            $parts = explode(' ', $areal);
            $area = $parts[1];
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
                        $this->processBatchnew($batchData, $db, $failedRows, $area);
                        $batchData = []; // Reset batch data
                    }
                }
            }

            // Process any remaining data
            if (!empty($batchData)) {
                $this->processBatchnew($batchData, $db, $failedRows, $area);
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
    private function processBatchnew($batchData, $db, &$failedRows, $area)
    {
        $db->transStart();
        foreach ($batchData as $batchItem) {
            $rowIndex = $batchItem['rowIndex'];
            $data = $batchItem['data'];
            try {
                log_message('debug', 'Processing row: ' . $rowIndex);

                $no_model = $data[2];
                $style = $data[3];
                $size = $data[3];
                $validate = [
                    'no_model' => $no_model,
                    'style' => $style,
                ];

                // Fetch data based on model, style, and size
                $dataMaster = $this->ApsPerstyleModel->getAllForModelStyleAndSize($validate);
                $remainingQty = str_replace('-', '', $data[14]); // Production quantity
                $lastId = null;
                $delivery = null;

                if ($dataMaster) {
                    log_message('debug', 'Data Master found: ' . json_encode($dataMaster));
                    log_message('debug', 'Original remainingQty: ' . $remainingQty);

                    // Sorting logic
                    if (count($dataMaster) > 1) {
                        // Sort by delivery date if multiple deliveries
                        usort($dataMaster, function ($a, $b) {
                            return strtotime($a['delivery']) - strtotime($b['delivery']);
                        });
                        log_message('debug', 'Data sorted by delivery date: ' . json_encode($dataMaster));
                    } else {
                        // Sort by qty descending if only one delivery
                        usort($dataMaster, function ($a, $b) {
                            return $b['sisa'] - $a['sisa'];
                        });
                        log_message('debug', 'Data sorted by qty descending: ' . json_encode($dataMaster));
                    }

                    // Process each row in the master data for the given model/style
                    foreach ($dataMaster as $item) {
                        $currentQty = $item['sisa'];
                        $id = $item['idapsperstyle'];
                        $delivery = $item['delivery'];

                        log_message('debug', 'Processing id: ' . $id . ' | Size: ' . $size . ' | Current qty: ' . $currentQty . ' | Remaining qty: ' . $remainingQty);

                        if ($remainingQty <= 0) break; // Stop when no remaining qty to subtract

                        if ($currentQty >= $remainingQty) {
                            $newQty = $currentQty - $remainingQty;
                            $remainingQty = 0; // Production quantity used up
                        } else {
                            $remainingQty -= $currentQty;
                            $newQty = 0; // This delivery's qty used up
                        }

                        log_message('debug', 'Updating id: ' . $id . ' | Size: ' . $size . ' | New qty: ' . $newQty . ' | Remaining after update: ' . $remainingQty);
                        $this->ApsPerstyleModel->update($id, ['sisa' => $newQty, 'factory' => $area]);
                        log_message('debug', 'Updated id: ' . $id . ' with new sisa: ' . $newQty);
                        $lastId = $id;
                    }

                    // If there's remaining qty after processing all deliveries
                    if ($remainingQty > 0 && $lastId !== null) {
                        log_message('debug', 'Final remaining qty after processing all rows: ' . $remainingQty);
                        $this->ApsPerstyleModel->update($lastId, ['sisa' => -1 * $remainingQty, 'factory' => $area]);
                        log_message('debug', 'Final update for lastId: ' . $lastId . ' | Remaining qty: ' . $remainingQty);
                    }

                    // Insert production data
                    $tglInputProduksi = $data[0];
                    $date = new DateTime($tglInputProduksi);
                    $date->modify('-1 day');
                    $tglprod = $date->format('Y-m-d');

                    if ($lastId !== null) {
                        $dataInsert = [
                            'tgl_produksi' => $tglprod,
                            'idapsperstyle' => $lastId,
                            'bagian' => "-",
                            'storage_awal' => "-",
                            'storage_akhir' => "-",
                            'qty_produksi' => str_replace('-', '', $data[14]),
                            'bs_prod' => 0,
                            'kategori_bs' => "-",
                            'no_box' => $data[12] ?? 0,
                            'no_label' => $data[13],
                            'admin' => session()->get('username'),
                            'shift' => "-",
                            'shift_a' => $data[9] ?? 0,
                            'shift_b' => $data[10] ?? 0,
                            'shift_c' => $data[11] ?? 0,
                            'no_mesin' => $data[8] ?? 0,
                            'delivery' => $delivery,
                            'area' => $area,
                            'size' => $style
                        ];

                        $existingProduction = $this->produksiModel->existingData($dataInsert);
                        if (!$existingProduction) {
                            $this->produksiModel->insert($dataInsert);
                            log_message('debug', 'Inserted production data for row: ' . $rowIndex);
                        } else {
                            $failedRows[] = $rowIndex . " duplikat";
                        }
                    } else {
                        $failedRows[] = 'Failed to insert production data for row: ' . $rowIndex . ' due to missing lastId';
                    }
                } else {
                    $failedRows[] = "Style tidak ditemukan " . $rowIndex;
                    log_message('debug', 'Style not found for row: ' . $rowIndex);
                }
            } catch (\Exception $e) {
                log_message('error', 'Error in row ' . $rowIndex . ': ' . $e->getMessage());
                $failedRows[] = 'Error on row ' . $rowIndex . ': ' . $e->getMessage();
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
    public function resetproduksiarea()
    {
        $area = $this->request->getPost('area');
        $awal = $this->request->getPost('awal');
        $akhir = $this->request->getPost('akhir');

        $produksi = $this->produksiModel->getDataForReset($area, $awal, $akhir);
        $errorMessages = [];
        $totalProcessed = 0;
        $totalErrors = 0;

        // Set batch size
        $batchSize = 100;
        $batchCounter = 0;

        foreach ($produksi as $pr) {
            try {
                $idProduksi = $pr['id_produksi'];
                $qtyproduksi = $pr['qty_produksi'];
                $idaps = $pr['idapsperstyle'];
                $sisaOrder = $this->ApsPerstyleModel->getSisaOrder($idaps);
                $setSisa = $qtyproduksi + $sisaOrder;

                // Update 'sisa' di tabel ApsPerstyle
                $this->ApsPerstyleModel->update($idaps, ['sisa' => $setSisa]);

                // Hapus data produksi
                $this->produksiModel->delete($idProduksi);

                $totalProcessed++;
            } catch (\Exception $e) {
                // Simpan pesan error jika terjadi
                $errorMessages[] = "Error processing ID: $idProduksi - " . $e->getMessage();
                $totalErrors++;
            }

            $batchCounter++;

            // Kalau sudah mencapai batch size, simpan checkpoint dan lanjut ke batch berikutnya
            if ($batchCounter >= $batchSize) {
                // Reset batch counter
                $batchCounter = 0;
                // Di sini lo bisa simpan ke log atau lakukan tindakan lain kalau perlu
            }
        }

        // Redirect dengan pesan sukses atau error
        if ($totalErrors > 0) {
            return redirect()->to(base_url(session()->get('role') . '/dataproduksi'))->withInput()->with('error', "Data berhasil di reset sebagian. $totalErrors data tidak terproses.");
        } else {
            return redirect()->to(base_url(session()->get('role') . '/dataproduksi'))->withInput()->with('success', 'Semua data berhasil di reset');
        }
    }
    public function summaryProdPerTanggal()
    {
        $role = session()->get('role');
        $buyer = $this->request->getPost('buyer');
        $area = $this->request->getPost('area');
        $jarum = $this->request->getPost('jarum');
        $pdk = $this->request->getPost('pdk');
        $awal = $this->request->getPost('awal');
        $akhir = $this->request->getPost('akhir');

        $data = [
            'buyer' => $buyer,
            'area' => $area,
            'jarum' => $jarum,
            'pdk' => $pdk,
            'awal' => $awal,
            'akhir' => $akhir,
        ];

        $dataSummaryPertgl = $this->orderModel->getdataSummaryPertgl($data);
        $prodSummaryPertgl = $this->orderModel->getProdSummaryPertgl($data);
        $totalProd = $this->orderModel->getDataTimter($data);

        // agar data tgl produksi menjadi unik
        $tgl_produksi = [];
        foreach ($prodSummaryPertgl as $item) {
            $tgl_produksi[$item['tgl_produksi']] = $item['tgl_produksi'];
        }
        $tgl_produksi = array_values($tgl_produksi);
        // Sort ASC
        sort($tgl_produksi);

        $uniqueData = [];
        foreach ($dataSummaryPertgl as $item) {
            $key = $item['machinetypeid'] . '-' . $item['mastermodel'] . '-' . $item['size'];
            if (!isset($uniqueData[$key])) {
                $uniqueData[$key] = [
                    'area' => $item['area'],
                    'machinetypeid' => $item['machinetypeid'],
                    'mastermodel' => $item['mastermodel'],
                    'size' => $item['size'],
                    'qty_produksi' => $item['qty_produksi'],
                    'max_delivery' => $item['max_delivery'],
                    'qty' => 0,
                    'running' => 0,
                    'ttl_prod' => 0,
                    'ttl_jlmc' => 0,
                ];
            }
            $uniqueData[$key]['qty'] += $item['qty'];
            $uniqueData[$key]['running'] += $item['running'];
            $uniqueData[$key]['ttl_prod'] += $item['qty_produksi'];
            $uniqueData[$key]['ttl_jlmc'] += $item['jl_mc'];
        }
        // Sort ASC
        sort($uniqueData);

        $data2 = [
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'dataSummaryPertgl' => $dataSummaryPertgl,
            'tglProdUnik' => $tgl_produksi,
            'uniqueData' => $uniqueData,
            'prodSummaryPertgl' => $prodSummaryPertgl,
            'total' => $totalProd,
            'role' => $role,
            'title' => 'Summary Produksi Per Tanggal ' . $buyer . ' ' . $area . ' ' . $pdk,
            'dataFilter' => $data,
        ];
        if ($role == 'user') {
            return view(session()->get('role') . '/summaryPertanggal', $data2);
        } else {
            return view(session()->get('role') . '/Produksi/summaryPertanggal', $data2);
        }
    }
    public function summaryProduksi()
    {
        $role = session()->get('role');
        $buyer = $this->request->getPost('buyer');
        $area = $this->request->getPost('area');
        $jarum = $this->request->getPost('jarum');
        $pdk = $this->request->getPost('pdk');

        $data = [
            'buyer' => $buyer,
            'area' => $area,
            'jarum' => $jarum,
            'pdk' => $pdk,
        ];

        $dataSummary = $this->orderModel->getProdSummary($data);
        $totalShip = $this->orderModel->getTotalShipment($data);
        $totalProd = $this->orderModel->getdataSummaryPertgl($data);
        // Debugging to check if $totalShip is an array
        if (!is_array($totalShip)) {
            echo "Error: totalShip is not an array!";
            print_r($totalShip);
            exit; // Stop execution to debug
        }

        $uniqueData = [];
        foreach ($dataSummary as $item) {
            $key = $item['machinetypeid'] . '-' . $item['mastermodel'] . '-' . $item['size'] . '-' . $item['delivery'];
            if (!isset($uniqueData[$key])) {
                $uniqueData[$key] = [
                    'area' => $item['area'],
                    'seam' => $item['seam'],
                    'buyer' => $item['kd_buyer_order'],
                    'no_order' => $item['no_order'],
                    'machinetypeid' => $item['machinetypeid'],
                    'mastermodel' => $item['mastermodel'],
                    'size' => $item['size'],
                    'color' => $item['color'],
                    'delivery' => $item['delivery'],
                    'qty_deliv' => 0,
                    'running' => 0,
                    'bruto' => 0,
                    'ttl_jlmc' => 0,
                ];
            }
            $uniqueData[$key]['qty_deliv'] += $item['qty_deliv'];
            $uniqueData[$key]['running'] += $item['running'];
            $uniqueData[$key]['bruto'] += $item['bruto'];
            $uniqueData[$key]['ttl_jlmc'] += $item['jl_mc'];
        }

        $data2 = [
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'uniqueData' => $uniqueData,
            'total_ship' => $totalShip,
            'total_prod' => $totalProd,
            'role' => $role,
            'title' => 'Summary Produksi ' . $buyer . ' ' . $area . ' ' . $pdk,
            'dataFilter' => $data,
        ];
        if ($role == 'user') {
            return view(session()->get('role') . '/summaryProduksi', $data2);
        } else {
            return view(session()->get('role') . '/Produksi/summaryProduksi', $data2);
        }
    }
    public function timterProduksi()
    {
        $role = session()->get('role');
        $area = $this->request->getPost('area');
        $jarum = $this->request->getPost('jarum');
        $pdk = $this->request->getPost('pdk');
        $awal = $this->request->getPost('awal');

        $data = [
            'area' => $area,
            'jarum' => $jarum,
            'pdk' => $pdk,
            'awal' => $awal,
        ];

        $dataTimter = $this->orderModel->getDataTimter($data);
        $poTimter = $this->orderModel->getQtyPOTimter($data);
        $prodTimter = $this->orderModel->getDetailProdTimter($data);
        $jlMC = $this->orderModel->getprodSummaryPertgl($data);
        // dd($dataTimter);
        $uniqueData = [];
        foreach ($prodTimter as $item) {
            $key = $item['machinetypeid'] . '-' . $item['mastermodel'] . '-' . $item['size'] . '-' . $item['no_mesin'];
            if (!isset($uniqueData[$key])) {
                $uniqueData[$key] = [
                    'seam' => $item['seam'],
                    'kd_buyer_order' => $item['kd_buyer_order'],
                    'area' => $item['area'],
                    'no_order' => $item['no_order'],
                    'machinetypeid' => $item['machinetypeid'],
                    'mastermodel' => $item['mastermodel'],
                    'size' => $item['size'],
                    'color' => $item['color'],
                    'smv' => $item['smv'],
                    'delivery' => $item['delivery'],
                    'qty' => 0,
                    'running' => 0,
                    'ttl_prod' => 0,
                    'ttl_jlmc' => 0,
                    'no_mesin' => $item['no_mesin'],
                ];
            }
            $uniqueData[$key]['qty'] += $item['qty'];
            $uniqueData[$key]['running'] += $item['running'];
            $uniqueData[$key]['ttl_prod'] += $item['qty_produksi'];
            $uniqueData[$key]['ttl_jlmc'] += $item['jl_mc'];
        }
        $data3 = [
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'dataTimter' => $dataTimter,
            'poTimter' => $poTimter,
            'prodTimter' => $prodTimter,
            'jlMC' => $jlMC,
            'uniqueData' => $uniqueData,
            'role' => $role,
            'title' => 'Timter Produksi ' . $area . ' ' . $pdk . ' Tanggal ' . $awal,
            'dataFilter' => $data,
            'header' => $area,
        ];
        if ($role == 'user') {
            return view(session()->get('role') . '/timterProduksi', $data3);
        } else {
            return view(session()->get('role') . '/Produksi/timterProduksi', $data3);
        }
    }

    public function editproduksi()
    {
        $id = $this->request->getPost('id');
        $area = $this->request->getPost('area');
        $idaps = $this->request->getPost('idaps');
        $sisa = $this->request->getPost('sisa');
        $curr = $this->request->getPost('qtycurrent');
        $qtynow = $this->request->getPost('qty_prod');
        $realqty = $sisa + $curr;
        $updateqty = $realqty - $qtynow;
        $updateSisaAps = $this->ApsPerstyleModel->update($idaps, ['sisa' => $updateqty]);
        if ($updateSisaAps) {
            $update = [
                'no_mesin' => $this->request->getPost('no_mc'),
                'no_label' => $this->request->getPost('no_label'),
                'no_box' => $this->request->getPost('no_box'),
                'qty_produksi' => $qtynow,
            ];
            $u = $this->produksiModel->update($id, $update);
            if ($u) {
                return redirect()->to(base_url(session()->get('role') . '/detailproduksi/' . $area))->withInput()->with('success', 'Berhasil Update Data Produksi');
            } else {
                return redirect()->to(base_url(session()->get('role') . '/detailproduksi/' . $area))->withInput()->with('error', 'Gagal Update Data Produksi');
            }
        } else {
            return redirect()->to(base_url(session()->get('role') . '/detailproduksi/' . $area))->withInput()->with('error', 'Gagal Update Sisa Order');
        }
    }

    public function importbssetting()
    {
        $file = $this->request->getFile('excel_file');
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
                        $this->prossesBs($batchData, $db, $failedRows);
                        $batchData = []; // Reset batch data
                    }
                }
            }

            // Process any remaining data
            if (!empty($batchData)) {
                $this->prossesBs($batchData, $db, $failedRows);
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
    private function prossesBs($batchData, $db, &$failedRows)
    {
        $db->transStart();
        foreach ($batchData as $batchItem) {
            $rowIndex = $batchItem['rowIndex'];
            $data = $batchItem['data'];

            try {
                $no_model = $data[21];
                $style = $data[4];
                $area = $data[26];
                $validate = [
                    'no_model' => $no_model,
                    'style' => $style,
                    'area' => $area,
                ];
                $idAps = $this->ApsPerstyleModel->getIdForBs($validate);
                if (!$idAps) {
                    if ($data[0] == null) {
                        continue; // Skip empty rows
                    } else {
                        $failedRows[] = "style tidak ditemukan " . $rowIndex;
                        continue;
                    }
                } else {
                    $id = $idAps['idapsperstyle'];
                    $sisaOrder = $idAps['sisa'];
                    $qtyOrder = $idAps['qty'];
                    $qtyerp = $data[12];
                    $qty = str_replace('-', '', $qtyerp);
                    $sisaQty = $sisaOrder + $qty;
                    $tgl = $data[1];
                    $date = new DateTime($tgl);
                    $tglprod = $date->format('Y-m-d');
                    $kodeDeffect =  substr($data[29] ?? '16A', 0, 3);

                    // $strReplace = str_replace('.', '-', $tglprod);
                    // $dateTime = \DateTime::createFromFormat('d-m-Y', $strReplace);
                    $idProduksi = $this->produksiModel
                        ->where('idapsperstyle', $id)
                        ->where('area', $area)
                        ->where('bs_prod <=', $qtyOrder)
                        ->orderBy('qty_produksi', 'desc')
                        ->orderBy('bs_prod', 'asc')
                        ->findAll();
                    if (!$idProduksi) {
                        $failedRows[] = "style: " . $style . "baris:" . $rowIndex . "idaps:" . $id . "tidak ada di database produksi";
                        continue;
                    }
                    $minBsProd = min(array_column($idProduksi, 'bs_prod'));
                    $idprod = array_filter($idProduksi, function ($prod) use ($minBsProd) {
                        return $prod['bs_prod'] == $minBsProd;
                    });
                    $idprod = reset($idprod);
                    $idprd = $idprod['id_produksi'];
                    $bs = $idprod['bs_prod'] + $qty;

                    $datainsert = [
                        'tgl_instocklot' => $tglprod,
                        'idapsperstyle' => $id,
                        'area' => $area,
                        'no_label' => $data[22],
                        'no_box' => $data[23],
                        'qty' => $qty,
                        'kode_deffect' => $kodeDeffect,
                        'id_produksi' => $idprd

                    ];
                    $insert = $this->BsModel->insert($datainsert);
                    if ($insert) {
                        $updateproduksi = $this->produksiModel->update($idprod['id_produksi'], ['bs_prod' => $bs]);
                        if (!$updateproduksi) {
                            $failedRows[] = "baris " . $rowIndex . "Gagal Update Data Produksi";
                            continue;
                        }
                        $updateBs = $this->ApsPerstyleModel->update($id, ['sisa' => $sisaQty]);
                        if (!$updateBs) {
                            $failedRows[] = "baris" . $rowIndex . "gagal Update Data BS";
                            continue;
                        }
                    } else {
                        $failedRows[] = "baris" . $rowIndex . "gagal Insert data";
                        continue;
                    }
                }
            } catch (\Exception $e) {
                $failedRows[] = $rowIndex;
            }
        }
        $db->transComplete();
    }
    public function plusPacking()
    {
        $role = session()->get('role');
        $data = [
            'role' => $role,
            'title' => 'Data Produksi',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
        ];
        return view($role . '/pluspacking', $data);
    }
    public function viewModelPlusPacking($pdk)
    {

        $styleData = $this->ApsPerstyleModel->getStyle($pdk);
        foreach ($styleData as &$sd) {
            $id = $sd['idapsperstyle'];
            $pluspacking = $this->produksiModel->getPlusPacking($id);
            $sd['pluspacking'] = $pluspacking;
        }
        $role = session()->get('role');
        $data = [
            'role' => $role,
            'title' => 'Data Produksi',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'style' => $styleData,
            'pdk' => $pdk,
        ];
        return view($role . '/detailplus', $data);
    }
    public function updatepo()
    {
        $pdk = $this->request->getPost('pdk');
        $ids = $this->request->getPost('id');
        $pos = $this->request->getPost('po');
        $role = session()->get('role');

        if (count($ids) !== count($pos)) {
            return redirect()->back()->with('error', 'Jumlah ID dan PO tidak sesuai.');
        }

        // Mulai transaction untuk memastikan integritas data
        $this->db->transStart();

        try {
            foreach ($ids as $key => $id) {
                $po = $pos[$key];

                // Pastikan nilai PO valid
                if (!is_numeric($po) || $po < 0) {
                    throw new \Exception('Nilai PO tidak valid.');
                }

                // Temukan produksi yang terkait dengan idapsperstyle
                $produksiList = $this->produksiModel
                    ->where('idapsperstyle', $id)
                    ->orderBy('qty_produksi', 'desc')
                    ->orderBy('bs_prod', 'asc')
                    ->findAll();

                // Cek apakah produksi ditemukan
                if (empty($produksiList)) {
                    throw new \Exception('Data produksi tidak ditemukan untuk ID: ' . $id);
                }

                // Temukan produksi dengan nilai plus_packing terendah
                $minBsProd = min(array_column($produksiList, 'plus_packing'));
                $produksiToUpdate = array_filter($produksiList, function ($prod) use ($minBsProd) {
                    return $prod['plus_packing'] == $minBsProd;
                });
                if (empty($produksiToUpdate)) {
                    throw new \Exception('Tidak dapat menemukan produksi yang cocok untuk ID: ' . $id);
                }

                $produksiToUpdate = reset($produksiToUpdate); // Ambil elemen pertama

                // Pastikan id ada di array
                if (!isset($produksiToUpdate['id_produksi'])) {
                    throw new \Exception('ID tidak ditemukan dalam produksi yang dipilih untuk ID: ' . $id);
                }

                // Update kolom plus_packing pada produksi yang dipilih
                $updateResult = $this->produksiModel->update($produksiToUpdate['id_produksi'], [
                    'plus_packing' => $po
                ]);

                // Cek apakah update berhasil
                if (!$updateResult) {
                    throw new \Exception('Gagal mengupdate data produksi untuk ID: ' . $id);
                }

                // Update kolom sisa pada tabel order
                $updateOrderResult = $this->ApsPerstyleModel->set('sisa', 'sisa + ' . $po, false)
                    ->where('idapsperstyle', $id)
                    ->update();

                if (!$updateOrderResult) {
                    throw new \Exception('Gagal mengupdate data order untuk ID: ' . $id);
                }
            }

            // Selesaikan transaksi
            $this->db->transComplete();

            // Cek apakah transaksi sukses
            if ($this->db->transStatus() === false) {
                throw new \Exception('Terjadi kesalahan saat menyimpan data.');
            }

            return redirect()->back()->with('success', 'Data berhasil diupdate');
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi error
            $this->db->transRollback();

            // Redirect dengan pesan error
            return redirect()->to($role . '/viewModelPlusPacking/' . $pdk)->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
