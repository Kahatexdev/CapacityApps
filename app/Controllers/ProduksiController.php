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

    public function __construct()
    {


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

                            // Insert production even if sisa is negative
                            $tglInputProduksi = $data[0];
                            $date = new DateTime($tglInputProduksi);
                            $date->modify('-1 day');
                            $tglprod = $date->format('Y-m-d');
                            $qtyerp = $data[14];
                            $qty = str_replace('-', '', $qtyerp);

                            // Prepare data for insert
                            $dataInsert = [
                                'tgl_produksi' => $tglprod,
                                'idapsperstyle' => $idnext,
                                'qty_produksi' => $qty,
                                'bs_prod' => 0,
                                'no_box' => $data[12] ?? 0,
                                'no_label' => $data[13],
                                'admin' => session()->get('username'),
                                'shift_a' => $data[9] ?? 0,
                                'shift_b' => $data[10] ?? 0,
                                'shift_c' => $data[11] ?? 0,
                                'no_mesin' => $data[8] ?? 0,
                                'delivery' => $deliv,
                                'area' => session()->get('username')
                            ];

                            // Insert the production data
                            $existingProduction = $this->produksiModel->existingData($dataInsert);
                            if (!$existingProduction) {
                                $this->produksiModel->insert($dataInsert);
                            } else {
                                $failedRows[] = $rowIndex . "duplikat";
                            }

                            // Update the sisa value after inserting production
                            $this->ApsPerstyleModel->update($idnext, ['sisa' => $sisa]);
                        } else {
                            $failedRows[] = "style tidak ditemukan" . $rowIndex;
                            continue;
                        }
                    }
                } else {
                    $id = $idAps['idapsperstyle'];
                    $sisaOrder = $idAps['sisa'];
                    $delivery = $idAps['delivery'];

                    $tglInputProduksi = $data[0];
                    $date = new DateTime($tglInputProduksi);
                    $date->modify('-1 day');
                    $tglprod = $date->format('Y-m-d');
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
                            $id = $nextid['idapsperstyle'];
                            $qtysisa = $nextid['sisa'];
                            $sisa = $qtysisa + $minus;

                            // Update sisa qty di id yang ditemukan
                            $this->ApsPerstyleModel->update($id, ['sisa' => $sisa]);

                            // Reset sisaQty ke 0 karena sisa sudah diambil alih
                            $sisaQty = 0;
                        } else {
                            // Tidak ada id berikutnya, tetap pakai id yang terakhir dan insert minus
                            $this->ApsPerstyleModel->update($id, ['sisa' => 0]);
                            $sisaQty = $minus; // Akan tetap minus di produksi
                            dd($id);
                        }
                    }

                    // Prepare data for insert
                    $dataInsert = [
                        'tgl_produksi' => $tglprod,
                        'idapsperstyle' => $id,
                        'qty_produksi' => $qty,
                        'bs_prod' => 0,
                        'no_box' => $data[12] ?? 0,
                        'no_label' => $data[13],
                        'admin' => session()->get('username'),
                        'shift_a' => $data[9] ?? 0,
                        'shift_b' => $data[10] ?? 0,
                        'shift_c' => $data[11] ?? 0,
                        'no_mesin' => $data[8] ?? 0,
                        'delivery' => $delivery,
                        'area' => session()->get('username')
                    ];

                    // Insert the production data
                    $existingProduction = $this->produksiModel->existingData($dataInsert);
                    if (!$existingProduction) {
                        $insert = $this->produksiModel->insert($dataInsert);
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

                        $failedRows[] = $rowIndex;
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
            'role' => session()->get('role'),
            'title' => 'Summary Produksi Per Tanggal ' . $pdk,
            'dataFilter' => $data,
        ];
        return view(session()->get('role') . '/Produksi/summaryPertanggal', $data2);
    }
    public function summaryProduksi()
    {
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
        // Sort ASC
        sort($uniqueData);

        $data2 = [
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => 'active',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'uniqueData' => $uniqueData,
            'total_ship' => $totalShip,
            'role' => session()->get('role'),
            'title' => 'Summary Produksi ' . $pdk,
            'dataFilter' => $data,
        ];
        return view(session()->get('role') . '/Produksi/summaryProduksi', $data2);
    }
    public function timterProduksi()
    {
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
            'active4' => 'active',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'dataTimter' => $dataTimter,
            'prodTimter' => $prodTimter,
            'jlMC' => $jlMC,
            'uniqueData' => $uniqueData,
            'role' => session()->get('role'),
            'title' => 'Timter Produksi ' . $pdk . ' Tanggal ' . $awal,
            'dataFilter' => $data,
            'header' => $area,
        ];
        return view(session()->get('role') . '/Produksi/timterProduksi', $data3);
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
            $batchSize = 15; // Ukuran batch
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
                $validate = [
                    'no_model' => $no_model,
                    'style' => $style
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
                    // $strReplace = str_replace('.', '-', $tglprod);
                    // $dateTime = \DateTime::createFromFormat('d-m-Y', $strReplace);
                    $idProduksi = $this->produksiModel
                        ->where('idapsperstyle', $id)
                        ->where('bs_prod <=', $qtyOrder)
                        ->first();
                    if (!$idProduksi) {
                        $failedRows[] = "style: " . $style . "baris:" . $rowIndex . "idaps:" . $id . "tidak ada di database produksi";
                        continue;
                    }
                    $bs = $idProduksi['bs_prod'] + $qty;

                    $datainsert = [
                        'tgl_instocklot' => $tglprod,
                        'idapsperstyle' => $id,
                        'area' => $data[26],
                        'no_label' => $data[22],
                        'no_box' => $data[23],
                        'qty' => $qty,
                        'kode_deffect' => $data[29]

                    ];
                    $this->BsModel->insert($datainsert);
                    $updateproduksi = $this->produksiModel->update($idProduksi['id_produksi'], ['bs_prod' => $bs]);
                    if (!$updateproduksi) {
                        $failedRows[] = "baris " . $rowIndex . "Gagal Update Data Produksi";
                        continue;
                    }
                    $updateBs = $this->ApsPerstyleModel->update($id, ['sisa' => $sisaQty]);
                    if (!$updateBs) {
                        $failedRows[] = "baris" . $rowIndex . "gagal Update Data BS";
                        continue;
                    }
                }
            } catch (\Exception $e) {
                $failedRows[] = $rowIndex;
            }
        }
        $db->transComplete();
    }
}
