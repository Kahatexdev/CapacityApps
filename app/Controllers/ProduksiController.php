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
use App\Models\BsMesinModel;
use CodeIgniter\Controller;
use PhpParser\Node\Stmt\Else_;
use PhpOffice\PhpSpreadsheet\Shared\Date;

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
    protected $bsMesinModel;
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
        $this->bsMesinModel = new BsMesinModel();

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
        $year = date('Y');
        $totalMesin = $this->jarumModel->getArea();
        $dataProduksi = $this->produksiModel->getProduksiPerhari($bulan, $year);
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
        $bulan = $this->request->getGet('bulan');
        $tglProduksi = $this->request->getGet('tgl_produksi') ?? null;
        $tglProduksiSampai = $this->request->getGet('tgl_produksi_sampai') ?? null;
        $noModel = $this->request->getGet('no_model') ?? null;
        $size = $this->request->getGet('size') ?? null;
        $noBox = $this->request->getGet('no_box') ?? null;
        $noLabel = $this->request->getGet('no_label') ?? null;

        $produksi = [];



        if ($bulan || $tglProduksi || $noModel || $size) {
            $produksi = $this->produksiModel->getProduksi($area, $bulan, $tglProduksi, $tglProduksiSampai, $noModel, $size, $noBox, $noLabel);
            // dd($produksi);
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
            'bulan' => date('M-Y'),
            'produksi' => $produksi,
            'area' => $area
        ];
        return view(session()->get('role') . '/Produksi/detail', $data);
    }
    public function produksiGlobal()
    {
        $bulan = $this->request->getGet('bulan');
        $tglProduksi = $this->request->getGet('tgl_produksi') ?? null;
        $area = $this->request->getGet('area') ?? null;
        $tglProduksiSampai = $this->request->getGet('tgl_produksi_sampai') ?? null;
        $noModel = $this->request->getGet('no_model') ?? null;
        $size = $this->request->getGet('size') ?? null;
        $noBox = $this->request->getGet('no_box') ?? null;
        $noLabel = $this->request->getGet('no_label') ?? null;
        $listArea = $this->jarumModel->getArea();

        $produksi = [];
        if ($bulan || $tglProduksi || $noModel || $size) {
            $produksi = $this->produksiModel->getProduksi($area, $bulan, $tglProduksi, $tglProduksiSampai, $noModel, $size, $noBox, $noLabel);
            // dd($produksi);
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
            'bulan' => date('M-Y'),
            'produksi' => $produksi,
            'area' => $area,
            'listArea' => $listArea
        ];
        return view(session()->get('role') . '/Produksi/detailGlobal', $data);
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
        $year = date('Y');
        $dataProduksi = $this->produksiModel->getProduksiPerhari($bulan, $year);
        $totalMesin = $this->jarumModel->getArea();
        $model = $this->ApsPerstyleModel->getPdkProduksi();

        $produksiPerArea = [];
        foreach ($totalMesin as $area) {
            $produksiPerArea[$area] = $this->produksiModel->getProduksiPerArea($area, $bulan, $year);
        }
        $dataBuyer = $this->orderModel->getBuyer();
        $dataArea = $this->jarumModel->getArea();
        $dataJarum = $this->jarumModel->getJarum();

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
            'models' => $model,
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
        $bulan = $this->request->getGet('bulan') ? $this->request->getGet('bulan') : date('m');
        $tahun = $this->request->getGet('tahun') ? $this->request->getGet('tahun') : date('Y');
        $totalMesin = $this->jarumModel->getArea();
        $produksiPerArea = [];
        foreach ($totalMesin as $area) {
            $produksiPerArea[$area] = $this->produksiModel->getProduksiPerArea($area, $bulan, $tahun);
        }
        return json_encode($produksiPerArea);
    }
    public function viewProduksiPlan()
    {
        $bulan = date('m');
        $month = date('F');
        $totalMesin = $this->jarumModel->getArea();
        $year = date('Y');
        $dataProduksi = $this->produksiModel->getProduksiPerhari($bulan, $year);
        // $pdkProgress = $this->ApsPerstyleModel->getProgress($noModel);
        $produksiPerArea = [];
        foreach ($totalMesin as $area) {
            $produksiPerArea[$area] = $this->produksiModel->getProduksiPerArea($area, $bulan, $year);
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
                    'area' => $area
                ];

                // Fetch data based on model, style, and size
                $dataMaster = $this->ApsPerstyleModel->getAllForModelStyleAndSize($validate);
                $remainingQty = str_replace('-', '', $data[14]); // Production quantity
                $lastId = null;
                $delivery = null;

                if ($dataMaster && count($dataMaster) > 0) {
                    log_message('debug', 'Data Master found: ' . json_encode($dataMaster));
                    log_message('debug', 'Original remainingQty: ' . $remainingQty);

                    // Sort delivery ascending if not sorted from query
                    usort($dataMaster, fn($a, $b) => strtotime($a['delivery']) <=> strtotime($b['delivery']));
                    log_message('debug', 'Sorted Data: ' . json_encode($dataMaster));

                    $remaining = $remainingQty;
                    $lastDeliveryRow = end($dataMaster);
                    reset($dataMaster);

                    foreach ($dataMaster as $item) {
                        if ($remaining <= 0) break;

                        $id       = $item['idapsperstyle'];
                        $sisa     = (float) $item['sisa'];
                        $delivery = $item['delivery'];

                        log_message('debug', "Processing ID: $id | Delivery: $delivery | Sisa: $sisa | Remaining: $remaining");

                        if ($sisa >= $remaining) {
                            $newSisa  = $sisa - $remaining;
                            $remaining = 0;
                        } else {
                            $remaining -= $sisa;
                            $newSisa = 0;
                        }

                        $this->ApsPerstyleModel->update($id, ['sisa' => $newSisa]);
                        log_message('debug', "Updated ID: $id | New Sisa: $newSisa");
                    }

                    // Overproduction → simpan ke delivery terakhir
                    if ($remaining > 0 && $lastDeliveryRow) {
                        $lastId = $lastDeliveryRow['idapsperstyle'];
                        $this->ApsPerstyleModel->update($lastId, ['sisa' => -1 * $remaining]);
                        log_message('debug', "Overproduction: updated ID $lastId with negative sisa: -" . $remaining);
                    }

                    // Setup tanggal produksi = tgl input - 1 hari
                    $tglInputProduksi = $data[0];
                    $tglProduksi = (new DateTime($tglInputProduksi))->modify('-1 day')->format('Y-m-d');

                    // Siapkan data insert produksi
                    $dataInsert = [
                        'tgl_produksi'   => $tglProduksi,
                        'idapsperstyle'  => $lastDeliveryRow['idapsperstyle'],
                        'bagian'         => "-",
                        'storage_awal'   => "-",
                        'storage_akhir'  => "-",
                        'qty_produksi'   => abs((int) $data[14]),
                        'bs_prod'        => 0,
                        'kategori_bs'    => "-",
                        'no_box'         => $data[12] ?? 0,
                        'no_label'       => $data[13],
                        'admin'          => session()->get('username'),
                        'shift'          => "-",
                        'shift_a'        => $data[9] ?? 0,
                        'shift_b'        => $data[10] ?? 0,
                        'shift_c'        => $data[11] ?? 0,
                        'no_mesin'       => $data[8] ?? 0,
                        'delivery'       => $lastDeliveryRow['delivery'],
                        'area'           => $area,
                        'size'           => $style,
                    ];

                    // Cek duplikat sebelum insert
                    if (!$this->produksiModel->existingData($dataInsert)) {
                        $this->produksiModel->insert($dataInsert);
                        log_message('debug', "Inserted production data for row: $rowIndex");
                    } else {
                        $failedRows[] = "$rowIndex duplikat";
                    }
                } else {
                    $failedRows[] = "Style tidak ditemukan $rowIndex";
                    log_message('debug', "Style not found for row: $rowIndex");
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
                    'inisial' => $item['inisial'],
                    'size' => $item['size'],
                    'qty_produksi' => $item['qty_produksi'],
                    'max_delivery' => $item['max_delivery'],
                    'sisa' => $item['sisa'],
                    'qty' => 0,
                    'plus_packing' => 0,
                    'running' => 0,
                    'ttl_prod' => 0,
                    'ttl_jlmc' => 0,
                ];
            }
            $uniqueData[$key]['qty'] += $item['qty'];
            $uniqueData[$key]['plus_packing'] += $item['plus_packing'];
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
                    'inisial' => $item['inisial'],
                    'size' => $item['size'],
                    'color' => $item['color'],
                    'delivery' => $item['delivery'],
                    'sisa' => $item['sisa'],
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
        $prodTimter = $this->orderModel->getDetailProdTimter($data);
        $jlMC = $this->produksiModel->getJlMcTimter($data);

        $uniqueData = [];

        // Iterasi untuk mendapatkan data yang relevan
        foreach ($dataTimter as $sizeItem) {
            foreach ($prodTimter as $item) {
                // Membuat key berdasarkan kombinasi informasi dari produksi dan size
                $key = $sizeItem['machinetypeid'] . '-' . $sizeItem['mastermodel'] . '-' . $sizeItem['size'];

                // Pastikan key belum ada, jika belum maka tambahkan data
                if (!isset($uniqueData[$key])) {
                    // Menambahkan data ke $uniqueData
                    $uniqueData[$key] = [
                        'seam' => $sizeItem['seam'],
                        'kd_buyer_order' => $sizeItem['kd_buyer_order'],
                        'area' => $sizeItem['factory'],
                        'no_order' => $sizeItem['no_order'],
                        'machinetypeid' => $sizeItem['machinetypeid'],
                        'mastermodel' => $sizeItem['mastermodel'],
                        'inisial' => $sizeItem['inisial'],  // Mengambil data inisial dari $sizeItem
                        'size' => $sizeItem['size'],        // Mengambil size dari $sizeItem
                        'color' => $sizeItem['color'],
                        'smv' => $sizeItem['smv'],
                        'delivery' => $sizeItem['delivery'],
                        'qty' => $sizeItem['qty'],
                        'sisa' => $sizeItem['sisa'],
                        'qty_prod' => $sizeItem['qty'] - $sizeItem['sisa'],
                        'ttl_dz' => 0,
                        'no_mesin' => [],  // Menyimpan array untuk no_mesin 
                    ];
                }

                // Menambahkan 'no_mesin' jika machinetypeid, mastermodel, dan size cocok
                if (
                    $sizeItem['machinetypeid'] === $item['machinetypeid'] &&
                    $sizeItem['mastermodel'] === $item['mastermodel'] &&
                    $sizeItem['size'] === $item['size']
                ) {
                    // Pastikan 'no_mesin' sudah ada di array 'no_mesin' sebelum mengaksesnya
                    if (!array_key_exists($item['no_mesin'], $uniqueData[$key]['no_mesin'])) {
                        // Inisialisasi jika 'no_mesin' belum ada
                        $uniqueData[$key]['no_mesin'][$item['no_mesin']] = [
                            'shift_a' => 0,
                            'shift_b' => 0,
                            'shift_c' => 0,
                            'total_shift' => 0,
                            'pa' => 0,
                        ];
                    }

                    // Update qty dan running untuk no_mesin
                    $uniqueData[$key]['no_mesin'][$item['no_mesin']]['shift_a'] += $item['shift_a'];
                    $uniqueData[$key]['no_mesin'][$item['no_mesin']]['shift_b'] += $item['shift_b'];
                    $uniqueData[$key]['no_mesin'][$item['no_mesin']]['shift_c'] += $item['shift_c'];
                    $uniqueData[$key]['no_mesin'][$item['no_mesin']]['pa'] += $item['pa'];

                    // Hitung total shift
                    $total_shift = $uniqueData[$key]['no_mesin'][$item['no_mesin']]['shift_a'] +
                        $uniqueData[$key]['no_mesin'][$item['no_mesin']]['shift_b'] +
                        $uniqueData[$key]['no_mesin'][$item['no_mesin']]['shift_c'] +
                        $uniqueData[$key]['no_mesin'][$item['no_mesin']]['pa'];

                    // Update total_shift
                    $uniqueData[$key]['no_mesin'][$item['no_mesin']]['total_shift'] = $total_shift;
                }

                // **Menjumlahkan total_shift untuk semua no_mesin dalam $key**
                $uniqueData[$key]['ttl_dz'] = 0; // Reset ttl_dz sebelum menjumlahkan

                // Loop untuk menjumlahkan total_shift untuk semua no_mesin dalam kombinasi $key
                foreach ($uniqueData[$key]['no_mesin'] as $noMesinData) {
                    // Menjumlahkan total shift dari setiap no_mesin
                    $uniqueData[$key]['ttl_dz'] += $noMesinData['total_shift'];
                }
            }
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
        $shiftA = $this->request->getPost('shift_a');
        $shiftB = $this->request->getPost('shift_b');
        $shiftC = $this->request->getPost('shift_c');
        $realqty = $sisa + $curr;
        $updateqty = $realqty - $qtynow;
        $updateSisaAps = $this->ApsPerstyleModel->update($idaps, ['sisa' => $updateqty]);
        if ($updateSisaAps) {
            $update = [
                'no_mesin' => $this->request->getPost('no_mc'),
                'no_label' => $this->request->getPost('no_label'),
                'no_box' => $this->request->getPost('no_box'),
                'qty_produksi' => $qtynow,
                'shift_a' => $shiftA,
                'shift_b' => $shiftB,
                'shift_c' => $shiftC,
                'tgl_produksi' => $this->request->getPost('tgl_prod'),
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
                return redirect()->to(base_url(session()->get('role') . '/bssetting'))->with('error', $errorMessage);
            }

            return redirect()->to(base_url(session()->get('role') . '/bssetting'))->withInput()->with('success', 'Data Berhasil di Import');
        } else {
            return redirect()->to(base_url(session()->get('role') . '/bssetting'))->with('error', 'No data found in the Excel file');
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
                        break; // Skip empty rows
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


                    $datainsert = [
                        'tgl_instocklot' => $tglprod,
                        'idapsperstyle' => $id,
                        'area' => $area,
                        'no_label' => $data[22],
                        'no_box' => $data[23],
                        'qty' => $qty,
                        'kode_deffect' => $kodeDeffect,

                    ];
                    $insert = $this->BsModel->insert($datainsert);
                    if ($insert) {
                        $updateBs = $this->ApsPerstyleModel->update($id, ['sisa' => $sisaQty]);
                        if (!$updateBs) {
                            $failedRows[] = "baris" . $rowIndex . "gagal Update Data BS";
                            continue;
                        }
                    } else {

                        $failedRows[] = "baris " . $rowIndex . "gagal Insert data, ada kolom yang kosong";
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
            $poPlus = $sd['po_plus'];
        }
        // dd($styleData);
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

        $this->db->transStart();

        try {
            foreach ($ids as $key => $id) {
                $po = isset($pos[$key]) ? $pos[$key] : 0;

                // Pastikan nilai PO valid
                if (!is_numeric($po) || $po < 0) {
                    throw new \Exception('Nilai PO tidak valid.');
                }

                // Update kolom po_plus pada tabel apsperstyle
                $updatePoPlus = $this->ApsPerstyleModel->set('po_plus', $po, false)
                    ->where('idapsperstyle', $id)
                    ->update();
                if (!$updatePoPlus) {
                    throw new \Exception('Gagal mengupdate po tambahan untuk ID: ' . $id);
                }

                // Update kolom sisa pada tabel apsperstyle
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
    public function updateproduksi()
    {
        $role = session()->get('role');
        $data = $this->produksiModel->updateProduksi();
        $gagal = [];
        foreach ($data as $dt) {
            $id = $dt['id_produksi'];
            $model = $dt['mastermodel'];
            $size = $dt['size'];

            // Lakukan update dan simpan error jika gagal
            $update = $this->produksiModel->update($id, ['no_model' => $model, 'size' => $size]);
            if (!$update) {
                $gagal[] = "Gagal update model: $model, size: $size (ID: $id)";
            }
        }

        // Redirect dengan pesan sukses atau error
        if (!empty($gagal)) {
            return redirect()->to('/sudo')->with('error', 'Beberapa data gagal diupdate: ' . implode(', ', $gagal));
        }

        return redirect()->to('/sudo')->with('success', 'Data berhasil diupdate');
    }
    public function updatebs()
    {
        $role = session()->get('role');
        $data = $this->BsModel->updateBs();
        $gagal = [];
        foreach ($data as $dt) {
            $id = $dt['idbs'];
            $model = $dt['mastermodel'];
            $size = $dt['size'];
            $deliv = $dt['delivery'];

            // Lakukan update dan simpan error jika gagal
            $update = $this->BsModel->update($id, ['no_model' => $model, 'size' => $size, 'delivery' => $deliv]);
            if (!$update) {
                $gagal[] = "Gagal update model: $model, size: $size (ID: $id)";
            }
        }

        // Redirect dengan pesan sukses atau error
        if (!empty($gagal)) {
            return redirect()->to('/sudo')->with('error', 'Beberapa data gagal diupdate: ' . implode(', ', $gagal));
        }

        return redirect()->to('/sudo')->with('success', 'Data berhasil diupdate');
    }
    public function getProductionData()
    {
        $bulan = $this->request->getGet('bulan');
        $tahun = $this->request->getGet('tahun');
        $area  = $this->request->getGet('area');

        if (!$bulan || !$tahun) {
            return $this->response->setJSON(['error' => 'Bulan dan Tahun wajib diisi']);
        }

        try {
            $builder = $this->produksiModel
                ->select("DATE_FORMAT(tgl_produksi, '%d-%b') as tgl_produksi, SUM(qty_produksi) as qty_produksi")
                ->where('MONTH(tgl_produksi)', $bulan)
                ->where('YEAR(tgl_produksi)', $tahun);

            if (!empty($area)) {
                $builder->where('area', $area);
            }

            $data = $builder
                ->groupBy('tgl_produksi')
                ->orderBy('tgl_produksi', 'ASC')
                ->findAll();

            return $this->response->setJSON($data);
        } catch (\Exception $e) {
            return $this->response->setJSON(['error' => $e->getMessage()]);
        }
    }

    public function getBsData()
    {
        $bulan = $this->request->getGet('bulan');
        $tahun = $this->request->getGet('tahun');
        $area  = $this->request->getGet('area');

        if (!$bulan || !$tahun) {
            return $this->response->setJSON(['error' => 'Bulan dan Tahun wajib diisi']);
        }

        try {
            $data = $this->BsModel->getBsPerhari($bulan, $tahun, $area);
            return $this->response->setJSON($data);
        } catch (\Exception $e) {
            return $this->response->setJSON(['error' => $e->getMessage()]);
        }
    }



    public function getArea()
    {
        $nomodel = $this->request->getPost('nomodel');
        $data = $this->ApsPerstyleModel->getAreasByNoModel($nomodel);
        return $this->response->setJSON($data);
    }
    public function getSize()
    {
        $nomodel = $this->request->getPost('nomodel');
        $area = $this->request->getPost('area');
        $data = $this->ApsPerstyleModel->getSizesByNoModelAndArea($nomodel, $area);
        return $this->response->setJSON($data);
    }
    public function inputProduksiManual()
    {
        $tglProduksi = $this->request->getPost('tgl_produksi');
        $noModel = $this->request->getPost('nomodel');
        $area = $this->request->getPost('area');
        $size = $this->request->getPost('size');
        $noBox = $this->request->getPost('box');
        $noLabel = $this->request->getPost('label');
        $noMesin = $this->request->getPost('no_mesin');
        $shiftA = $this->request->getPost('shift_a');
        $shiftB = $this->request->getPost('shift_b');
        $shiftC = $this->request->getPost('shift_c');
        $qtyProduksi = $shiftA + $shiftB + $shiftC;
        $admin = session()->get('username');

        $validate = [
            'no_model' => $noModel,
            'style' => $size
        ];
        $idAps = $this->ApsPerstyleModel->getIdProd($validate);

        if (!$idAps) {
            $idMinus = $this->ApsPerstyleModel->getIdMinus($validate);
            if ($idMinus) {
                $idnext = $idMinus['idapsperstyle'];
                $qtysisa = $idMinus['sisa'];
                $deliv = $idMinus['delivery'];
                // dd($qtyProduksi);
                $sisa = $qtysisa - $qtyProduksi;
                $this->ApsPerstyleModel->update($idnext, ['sisa' => $sisa]);

                $dataInsert = [
                    'tgl_produksi' => $tglProduksi,
                    'idapsperstyle' => $idnext,
                    'qty_produksi' => $qtyProduksi,
                    'no_box' => $noBox,
                    'no_label' => $noLabel,
                    'no_mesin' => $noMesin,
                    'delivery' => $deliv,
                    'area' => $area,
                    'admin' => $admin,
                    'shift_a' => $shiftA,
                    'shift_b' => $shiftB,
                    'shift_c' => $shiftC,
                ];
                // dd($dataInsert);
                $existingProduction = $this->produksiModel->existingData($dataInsert);
                if (!$existingProduction) {
                    $this->produksiModel->insert($dataInsert);
                    return redirect()->to('/' . session()->get('role'))->with('success', 'Berhasil input data');
                } else {
                    return redirect()->to('/' . session()->get('role'))->with('error', 'Data gagal diinput');
                }
            } else {
                return redirect()->to('/' . session()->get('role'))->with('error', 'Id tidak ditemukan');
            }
        } else {
            $id = $idAps['idapsperstyle'];
            $sisaOrder = $idAps['sisa'];
            $delivery = $idAps['delivery'];

            $sisaQty = $sisaOrder - $qtyProduksi;

            if ($sisaQty < 0) {
                $minus = $sisaQty;
                $second = [
                    'no_model' => $noModel,
                    'style' => $size,
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
            $dataInsert = [
                'tgl_produksi' => $tglProduksi,
                'idapsperstyle' => $id,
                'qty_produksi' => $qtyProduksi,
                'no_box' => $noBox,
                'no_label' => $noLabel,
                'no_mesin' => $noMesin,
                'delivery' => $delivery,
                'area' => $area,
                'admin' => $admin,
                'shift_a' => $shiftA,
                'shift_b' => $shiftB,
                'shift_c' => $shiftC,
            ];
            $existingProduction = $this->produksiModel->existingData($dataInsert);
            if (!$existingProduction) {
                $insert =  $this->produksiModel->insert($dataInsert);
                if ($insert) {
                    $update = $this->ApsPerstyleModel->update($id, ['sisa' => $sisaQty]);
                    if ($update) {
                        return redirect()->to('/' . session()->get('role'))->with('success', 'Berhasil input data');
                    }
                } else {
                    return redirect()->to('/' . session()->get('role'))->with('error', 'Data gagal diinput');
                }
            } else {
                $idexist = $existingProduction['id_produksi'];
                $sumqty = $existingProduction['qty_produksi'] + $qtyProduksi;
                $this->produksiModel->update($idexist, ['qty_produksi' => $sumqty]);
                $this->ApsPerstyleModel->update($id, ['sisa' => $sisaQty]);

                return redirect()->to('/' . session()->get('role'))->with('error', 'Berhasil input data');
            }
        }
    }
    public function deleteProduksi($id)
    {
        $idaps = $this->request->getGet('idaps');
        $qty = $this->request->getGet('qty');
        $sisa = $this->request->getGet('sisa');
        $area = $this->request->getGet('area');
        // dd($idaps, $area, $sisa, $qty);
        $delete = $this->produksiModel->where('id_produksi', $id)->delete();
        if ($delete) {
            $sisaQty = $sisa + $qty;
            $update = $this->ApsPerstyleModel->update($idaps, ['sisa' => $sisaQty]);
            if ($update) {
                return redirect()->to(base_url(session()->get('role') . '/detailproduksi/' . $area))->withInput()->with('success', 'Data Berhasil di hapus');
            }
        } else {
            return redirect()->to(base_url(session()->get('role') . '/detailproduksi/' . $area))->withInput()->with('error', 'Data Gagal di hapus ❗');
        }
    }
    public function importbsmc()
    {
        $area = session()->get('username');

        // Set execution time and memory limit
        ini_set('memory_limit', '512M');
        set_time_limit(180);

        $file = $this->request->getFile('excel_file');
        if (!$file->isValid() || $file->hasMoved()) {
            return redirect()->to(base_url(session()->get('role') . '/bsmesin'))
                ->with('error', 'No data found in the Excel file');
        }

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
        $worksheet   = $spreadsheet->getActiveSheet();

        // Ambil nilai dari sel B1 dan hilangkan spasi ekstra
        $tglProduksiRaw = trim($worksheet->getCell('B1')->getValue());
        if (is_numeric($tglProduksiRaw)) {
            // Konversi serial date Excel ke objek DateTime
            $dateTime = Date::excelToDateTimeObject($tglProduksiRaw);
            $tgl_produksi = $dateTime->format('Y-m-d');
        } else {
            // Jika bukan serial number, kita lakukan parsing seperti sebelumnya
            $tglProduksiFormatted = str_replace('.', '-', $tglProduksiRaw);
            $dateTime = \DateTime::createFromFormat('d-m-Y', $tglProduksiFormatted);
            $tgl_produksi = $dateTime ? $dateTime->format('Y-m-d') : null;
        }

        $startRow   = 6;
        $batchSize  = 100;
        $batchData  = [];
        $allBatchData = [];
        $allOperators = [];
        $failedRows = [];
        $db         = \Config\Database::connect();

        // Ambil semua data operator dari kolom F, G, H
        foreach ($worksheet->getRowIterator($startRow) as $row) {
            $rowIndex = $row->getRowIndex();
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            $rowData = ['role' => session()->get('role')];
            foreach ($cellIterator as $cell) {
                $rowData[] = $cell->getCalculatedValue();
            }

            // Ambil operator F (index 5), G (6), H (7)
            for ($i = 5; $i <= 7; $i++) {
                $operatorName = trim($rowData[$i] ?? '');
                if (!empty($operatorName)) {
                    $allOperators[] = $operatorName;
                }
            }

            if (!empty($rowData)) {
                $batchData[] = ['rowIndex' => $rowIndex, 'data' => $rowData];
            }

            if (count($batchData) >= $batchSize) {
                $allBatchData[] = $batchData;
                $batchData = [];
            }
        }

        if (!empty($batchData)) {
            $allBatchData[] = $batchData;
        }

        // Ambil data operator unik
        $uniqueOperators = array_unique($allOperators);
        $operatorDataMap = [];

        // Ambil data karyawan dari API
        foreach ($uniqueOperators as $operatorName) {
            $operatorEncoded = urlencode($operatorName);
            $apiUrl = "http://172.23.44.14/SkillMapping/public/api/getdataforbs/{$area}/{$operatorEncoded}";
            $response = @file_get_contents($apiUrl);
            log_message('debug', "Response API untuk operator '{$operatorName}': {$response}");

            if ($response === false) {
                log_message('error', "Gagal mengakses API: {$apiUrl}");
                $operatorDataMap[$operatorName] = null;
            } else {
                // Mengubah response JSON menjadi array
                $decodedResponse = json_decode($response, true);

                if (isset($decodedResponse[0]['nama_karyawan']) && isset($decodedResponse[0]['id_karyawan'])) {
                    $operatorDataMap[$operatorName] = $decodedResponse[0];
                } else {
                    $operatorDataMap[$operatorName] = null;
                }
            }
        }

        // Proses semua batch dengan operator yang sudah ada datanya
        foreach ($allBatchData as $batchData) {
            $this->processBatchBsMc($batchData, $db, $failedRows, $area, $tgl_produksi, $operatorDataMap);
        }

        // Jika ada baris gagal, tampilkan pesan error
        if (!empty($failedRows)) {
            $shiftToColumn = [
                'A' => 'Kolom F (Operator Shift A)',
                'B' => 'Kolom G (Operator Shift B)',
                'C' => 'Kolom H (Operator Shift C)'
            ];

            $failedDetails = array_map(function ($errorInfo) use ($shiftToColumn) {
                $shiftColumn = $shiftToColumn[$errorInfo['error']] ?? $errorInfo['error'];
                $message = "{$shiftColumn} baris ke {$errorInfo['row']}";
                if (!empty($errorInfo['error'])) {
                    $message .= " - " . $errorInfo['row'];
                }
                return $message;
            }, $failedRows);

            return redirect()->to(base_url(session()->get('role') . '/bsmesin'))
                ->with('error', 'Beberapa data gagal diimpor:')
                ->with('error_list', $failedDetails);
        }

        return redirect()->to(base_url(session()->get('role') . '/bsmesin'))
            ->withInput()->with('success', 'Data Bs Mesin Berhasil di Import');
    }

    private function processBatchBsMc($batchData, $db, &$failedRows, $area, $tgl_produksi, $operatorDataMap)
    {
        $db->transStart();
        $today = date('Y-m-d H:i:s');

        // Mapping shift dan kolom qty berdasarkan indeks dalam array data
        $shiftMapping   = [0 => 'A', 1 => 'B', 2 => 'C'];
        $qtyPcsMapping  = [0 => 9, 1 => 12, 2 => 15];
        $qtyGramMapping = [0 => 17, 1 => 18, 2 => 19];

        // Loop untuk batch data
        foreach ($batchData as $batchItem) {
            $rowIndex = $batchItem['rowIndex'];
            $data = $batchItem['data'];

            // Iterasi berdasarkan operator (shift A, B, C)
            for ($index = 0; $index < 3; $index++) {
                $operatorIndex = 5 + $index;
                $operatorName = trim($data[$operatorIndex] ?? '');
                if (empty($operatorName)) {
                    continue;
                }

                // Mencocokkan nama operator dengan nama_karyawan dari API
                $id_karyawan = null;
                if (isset($operatorDataMap[$operatorName])) {
                    $operatorData = $operatorDataMap[$operatorName];
                    if ($operatorData && isset($operatorData['id_karyawan'])) {
                        $id_karyawan = $operatorData['id_karyawan'];
                    }
                }

                // Jika tidak ditemukan, id_karyawan tetap null
                // Bisa juga menambahkan log jika diperlukan
                if (is_null($id_karyawan)) {
                    log_message('info', "Operator {$operatorName} tidak ditemukan di API, ID Karyawan di-set null");
                }

                // Ambil shift yang sedang diproses
                $shift = $shiftMapping[$index];
                $qtyPcs = $data[$qtyPcsMapping[$index]] ?? 0;
                $qtyGram = $data[$qtyGramMapping[$index]] ?? 0;

                // Lewati proses insert jika qty tidak valid (nol atau kosong)
                // if (((empty($qtyPcs) || $qtyPcs == 0) && (empty($qtyGram) || $qtyGram == 0))) {
                //     continue;
                // }

                $noModel = $data[2];
                $inisial = $data[3];
                $sz = $this->ApsPerstyleModel->getSizes($noModel, $inisial);

                // Jika model tidak ditemukan
                if (empty($sz) || !isset($sz['size']) || empty($sz['size'])) {
                    $failedRows[] = [
                        'row'      => $rowIndex,
                        'no_model' => $noModel,
                        'inisial'  => $inisial,
                        'error'    => "No Model atau Inisial tidak ditemukan untuk model: '{$noModel}', inisial: '{$inisial}'"
                    ];
                    continue;
                }

                $size = $sz['size'];
                $noMesin = $data[1];

                // Prepare data insert
                $dataInsert = [
                    'tanggal_produksi' => $tgl_produksi,
                    'id_karyawan' => $id_karyawan,
                    'nama_karyawan' => $operatorName,
                    'shift' => $shift,
                    'area' => $area,
                    'no_model' => $noModel,
                    'size' => $size,
                    'inisial' => $inisial,
                    'no_mesin' => $noMesin,
                    'qty_pcs' => $qtyPcs,
                    'qty_gram' => $qtyGram,
                    'created_at' => $today,
                ];

                // Cek apakah data sudah ada
                $existingBs = $this->bsMesinModel->existingData($dataInsert);
                if (!$existingBs) {
                    $result = $this->bsMesinModel->insert($dataInsert);
                    if (!$result) {
                        log_message('error', "Gagal insert data pada row {$rowIndex} untuk shift {$shift}");
                        $failedRows[] = [
                            'row'      => $rowIndex,
                            'operator' => $operatorName,
                            'shift'    => $shift,
                            'error'    => 'Insert gagal'
                        ];
                    }
                } else {
                    $idbs = $existingBs['id_bsmc'];
                    $result = $this->bsMesinModel->update($idbs, ['qty_pcs' => $dataInsert['qty_pcs'], 'qty_gram' => $dataInsert['qty_gram']]);
                    if (!$result) {
                        log_message('error', "Gagal update data pada row {$rowIndex} untuk shift {$shift}");
                        $failedRows[] = [
                            'row'      => $rowIndex,
                            'operator' => $operatorName,
                            'shift'    => $shift,
                            'error'    => 'update gagal'
                        ];
                    }
                }
            }
        }

        $db->transComplete();
    }
}
