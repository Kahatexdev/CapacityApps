<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

use App\Models\DataMesinModel;
use DateTime;
use PhpOffice\PhpSpreadsheet\IOFactory;
use CodeIgniter\Controller;
use PhpParser\Node\Stmt\Else_;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ProduksiController extends BaseController
{

    protected $db;

    public function __construct()
    {

        $this->db = \Config\Database::connect();

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
                dd($idAps);
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
                    $currentId = $idAps['idapsperstyle'];
                    $sisaOrder = $idAps['sisa'];
                    $currentDeliv = $idAps['delivery'];

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
                            $currentId = $nextid['idapsperstyle'];
                            $currentDeliv = $nextid['delivery'];
                            $qtysisa = $nextid['sisa'];
                            $sisa = $qtysisa + $minus;
                            $this->ApsPerstyleModel->update($currentId, ['sisa' => $sisa]);

                            $sisaQty = 0;
                        } else {

                            $sisaQty = $minus;
                        }
                        $currentId = $nextid['idapsperstyle'];
                        $currentDeliv = $nextid['delivery'];
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
                        'idapsperstyle' => $currentId,
                        'delivery' => $currentDeliv,
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
                        'area' => $area
                    ];
                    $existingProduction = $this->produksiModel->existingData($dataInsert);
                    if (!$existingProduction) {
                        $insert =  $this->produksiModel->insert($dataInsert);
                        if ($insert) {
                            $this->ApsPerstyleModel->update($currentId, ['sisa' => $sisaQty]);
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
        $totalMesin = $this->jarumModel->getArea();
        $model = $this->ApsPerstyleModel->getPdkProduksi();

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
            'Area' => $totalMesin,
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
            $data     = $batchItem['data'];

            try {

                $no_model = $data[2];
                $style    = $data[3];
                $area     = $area;

                $totalProduksi = abs((int) $data[14]);
                if ($totalProduksi <= 0) {
                    continue;
                }

                $validate = [
                    'no_model' => $no_model,
                    'style'    => $style,
                    'area'     => $area
                ];

                $dataMaster = $this->ApsPerstyleModel
                    ->getAllForModelStyleAndSize($validate);

                if (!$dataMaster || count($dataMaster) === 0) {
                    $failedRows[] = "Style tidak ditemukan row $rowIndex";
                    continue;
                }

                // =====================================================
                // SORT SHIPMENT BERURUT (PALING PENTING)
                // =====================================================
                usort(
                    $dataMaster,
                    fn($a, $b) =>
                    strtotime($a['delivery']) <=> strtotime($b['delivery'])
                );

                $tglProduksi = (new DateTime($data[0]))
                    ->modify('-1 day')
                    ->format('Y-m-d');

                $remaining = $totalProduksi;
                $lastAPS   = null;

                // =====================================================
                // LOOP SHIPMENT SATU-SATU (HABISKAN DULU)
                // =====================================================
                foreach ($dataMaster as $item) {

                    if ($remaining <= 0) {
                        break;
                    }

                    $id       = $item['idapsperstyle'];
                    $sisa     = (int) $item['sisa'];
                    $delivery = $item['delivery'];

                    $lastAPS = $item; // selalu update → ujungnya shipment terakhir

                    // shipment ini sudah habis → skip
                    if ($sisa <= 0) {
                        continue;
                    }

                    // tentukan berapa yang dipakai dari shipment ini
                    if ($sisa >= $remaining) {
                        $pakai     = $remaining;
                        $newSisa   = $sisa - $remaining;
                        $remaining = 0;
                    } else {
                        $pakai     = $sisa;
                        $newSisa   = 0;
                        $remaining -= $sisa;
                    }

                    // UPDATE SISA SHIPMENT INI
                    $this->ApsPerstyleModel->update($id, [
                        'sisa' => $newSisa
                    ]);

                    // INSERT PRODUKSI UNTUK SHIPMENT INI
                    $this->produksiModel->insert([
                        'tgl_produksi'  => $tglProduksi,
                        'idapsperstyle' => $id,
                        'qty_produksi'  => $pakai,
                        'bs_prod'       => 0,
                        'kategori_bs'   => '-',
                        'no_box'        => $data[12] ?? 0,
                        'no_label'      => $data[13],
                        'admin'         => session()->get('username'),
                        'shift'         => '-',
                        'shift_a'       => $data[9] ?? 0,
                        'shift_b'       => $data[10] ?? 0,
                        'shift_c'       => $data[11] ?? 0,
                        'no_mesin'      => $data[8] ?? 0,
                        'delivery'      => $delivery,
                        'area'          => $area,
                        'size'          => $style,
                    ]);
                }

                // =====================================================
                // OVERPRODUCTION → MASUK KE SHIPMENT TERAKHIR SAJA
                // =====================================================
                if ($remaining > 0 && $lastAPS) {

                    $lastId   = $lastAPS['idapsperstyle'];
                    $sisaLast = (int) $lastAPS['sisa'];

                    // sisa akhir = sisa awal shipment terakhir - sisa produksi
                    $this->ApsPerstyleModel->update($lastId, [
                        'sisa' => $sisaLast - $remaining
                    ]);

                    // insert produksi sisa ke shipment terakhir
                    $this->produksiModel->insert([
                        'tgl_produksi'  => $tglProduksi,
                        'idapsperstyle' => $lastId,
                        'qty_produksi'  => $remaining,
                        'bs_prod'       => 0,
                        'kategori_bs'   => '-',
                        'no_box'        => $data[12] ?? 0,
                        'no_label'      => $data[13],
                        'admin'         => session()->get('username'),
                        'shift'         => '-',
                        'shift_a'       => $data[9] ?? 0,
                        'shift_b'       => $data[10] ?? 0,
                        'shift_c'       => $data[11] ?? 0,
                        'no_mesin'      => $data[8] ?? 0,
                        'delivery'      => $lastAPS['delivery'],
                        'area'          => $area,
                        'size'          => $style,
                    ]);
                }
            } catch (\Throwable $e) {
                log_message('error', "Row $rowIndex error: " . $e->getMessage());
                $failedRows[] = "Error row $rowIndex";
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
        $area  = $this->request->getPost('area');
        $awal  = $this->request->getPost('awal');
        $akhir = $this->request->getPost('akhir');

        $db = \Config\Database::connect();
        $db->transBegin();

        try {

            /**
             * 1. Ambil total produksi per APS
             */
            $rows = $db->table('produksi p')
                ->select('p.idapsperstyle, SUM(p.qty_produksi) AS total_qty')
                ->where('p.area', $area)
                ->where('p.tgl_produksi >=', $awal)
                ->where('p.tgl_produksi <=', $akhir)
                ->groupBy('p.idapsperstyle')
                ->get()
                ->getResultArray();

            if (empty($rows)) {
                $db->transRollback();
                return redirect()
                    ->back()
                    ->with('warning', 'Tidak ada data produksi untuk di-reset');
            }

            /**
             * 2. Update sisa APS (bulk, loop kecil)
             */
            foreach ($rows as $row) {
                $db->table('apsperstyle')
                    ->set('sisa', 'sisa + ' . (int)$row['total_qty'], false)
                    ->where('idapsperstyle', $row['idapsperstyle'])
                    ->update();
            }

            /**
             * 3. Delete produksi langsung (sekali query)
             */
            $db->table('produksi')
                ->where('area', $area)
                ->where('tgl_produksi >=', $awal)
                ->where('tgl_produksi <=', $akhir)
                ->delete();

            $db->transCommit();

            return redirect()
                ->to(base_url(session()->get('role') . '/dataproduksi'))
                ->with('success', 'Reset produksi berhasil dan konsisten');
        } catch (\Throwable $e) {
            $db->transRollback();

            log_message('error', 'Reset produksi gagal: ' . $e->getMessage());

            return redirect()
                ->back()
                ->with('error', 'Reset produksi gagal. Silakan cek log.');
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
        // dd($totalProd);
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

    // public function editproduksi()
    // {
    //     $id = $this->request->getPost('id');
    //     $area = $this->request->getPost('area');
    //     $idaps = $this->request->getPost('idaps');
    //     $sisa = $this->request->getPost('sisa');
    //     $curr = $this->request->getPost('qtycurrent');
    //     $qtynow = $this->request->getPost('qty_prod');
    //     $shiftA = $this->request->getPost('shift_a');
    //     $shiftB = $this->request->getPost('shift_b');
    //     $shiftC = $this->request->getPost('shift_c');
    //     $realqty = $sisa + $curr;
    //     $updateqty = $realqty - $qtynow;
    //     $updateSisaAps = $this->ApsPerstyleModel->update($idaps, ['sisa' => $updateqty]);
    //     if ($updateSisaAps) {
    //         $update = [
    //             'no_mesin' => $this->request->getPost('no_mc'),
    //             'no_label' => $this->request->getPost('no_label'),
    //             'no_box' => $this->request->getPost('no_box'),
    //             'qty_produksi' => $qtynow,
    //             'shift_a' => $shiftA,
    //             'shift_b' => $shiftB,
    //             'shift_c' => $shiftC,
    //             'tgl_produksi' => $this->request->getPost('tgl_prod'),
    //         ];
    //         $u = $this->produksiModel->update($id, $update);
    //         if ($u) {
    //             return redirect()->to(base_url(session()->get('role') . '/detailproduksi/' . $area))->withInput()->with('success', 'Berhasil Update Data Produksi');
    //         } else {
    //             return redirect()->to(base_url(session()->get('role') . '/detailproduksi/' . $area))->withInput()->with('error', 'Gagal Update Data Produksi');
    //         }
    //     } else {
    //         return redirect()->to(base_url(session()->get('role') . '/detailproduksi/' . $area))->withInput()->with('error', 'Gagal Update Sisa Order');
    //     }
    // }
    public function editproduksi()
    {
        $id = $this->request->getPost('id');
        $area = $this->request->getPost('area');
        $idaps = $this->request->getPost('idaps');
        $sisa = (int) $this->request->getPost('sisa');
        $curr = (int) $this->request->getPost('qtycurrent');
        $qtynow = (int) $this->request->getPost('qty_prod');
        $shiftA = $this->request->getPost('shift_a');
        $shiftB = $this->request->getPost('shift_b');
        $shiftC = $this->request->getPost('shift_c');
        $realqty = $sisa + $curr;
        $updateqty = $realqty - $qtynow;
        $updateSisaAps = $this->ApsPerstyleModel->update($idaps, ['sisa' => $updateqty]);
        if (!$updateSisaAps) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Gagal Update Sisa Order'
            ]);
        }
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
        if (!$u) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Gagal Update Data Produksi'
            ]);
        }
        // ✅ sukses → kirim balik data untuk update view
        return $this->response->setJSON([
            'status' => 'success',
            'data' => [
                'id_produksi'  => $id,
                'tgl_produksi'  => $update['tgl_produksi'],
                'no_mesin'      => $update['no_mesin'],
                'no_box'        => $update['no_box'],
                'no_label'      => $update['no_label'],
                'shift_a'      => $update['shift_a'],
                'shift_b'      => $update['shift_b'],
                'shift_c'      => $update['shift_c'],
                'qty_produksi'  => $qtynow,
            ]
        ]);
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
        $db = $this->db;
        // ===============================
        // CONTAINER
        // ===============================
        $apsCache         = []; // key => aps row | null
        $apsUpdateBuffer  = []; // idaps => sisa baru
        $bsInsertBatch    = [];
        $failedRows       = [];

        // ===============================
        // LOOP EXCEL
        // ===============================
        foreach ($batchData as $batchItem) {
            $rowIndex = $batchItem['rowIndex'];
            $data     = $batchItem['data'];

            try {
                // Skip empty row
                if (empty($data[0])) {
                    continue;
                }

                // Mapping
                $noModel = $data[21] ?? null;
                $style   = $data[4]  ?? null;
                $area    = $data[26] ?? null;

                if (!$noModel || !$style || !$area) {
                    $failedRows[] = "Baris {$rowIndex}: data utama kosong";
                    continue;
                }

                $cacheKey = "{$noModel}|{$style}|{$area}";

                // ===============================
                // APS QUERY (CACHED PER KEY)
                // ===============================
                if (!array_key_exists($cacheKey, $apsCache)) {

                    $aps = $this->ApsPerstyleModel->getIdForBs([
                        'no_model' => $noModel,
                        'style'    => $style,
                        'area'     => $area,
                    ]);

                    // Cache walaupun null (biar ga query ulang)
                    $apsCache[$cacheKey] = $aps ?: null;
                }

                if ($apsCache[$cacheKey] === null) {
                    $failedRows[] = "Baris {$rowIndex}: APS tidak ditemukan";
                    continue;
                }

                $aps = $apsCache[$cacheKey];

                // ===============================
                // VALIDASI QTY
                // ===============================
                $qty = (int) str_replace('-', '', $data[12] ?? 0);
                if ($qty <= 0) {
                    continue;
                }

                // ===============================
                // PARSE TANGGAL
                // ===============================
                try {
                    $tglProd = (new \DateTime($data[1]))->format('Y-m-d');
                } catch (\Exception $e) {
                    $failedRows[] = "Baris {$rowIndex}: tanggal invalid";
                    continue;
                }

                // ===============================
                // SIAPKAN INSERT BS
                // ===============================
                $bsInsertBatch[] = [
                    'tgl_instocklot' => $tglProd,
                    'idapsperstyle'  => $aps['idapsperstyle'],
                    'area'           => $area,
                    'no_label'       => $data[22] ?? null,
                    'no_box'         => $data[23] ?? null,
                    'storage_from'         => $data[10] ?? null,
                    'qty'            => $qty,
                    'kode_deffect'   => substr($data[29] ?? '20A', 0, 3),
                ];

                // ===============================
                // AKUMULASI SISA APS
                // ===============================
                if (!isset($apsUpdateBuffer[$aps['idapsperstyle']])) {
                    $apsUpdateBuffer[$aps['idapsperstyle']] = $aps['sisa'];
                }

                $apsUpdateBuffer[$aps['idapsperstyle']] += $qty;
            } catch (\Throwable $e) {
                $failedRows[] = "Baris {$rowIndex}: {$e->getMessage()}";
            }
        }

        // ===============================
        // EXECUTE DATABASE
        // ===============================
        $db->transStart();

        // INSERT BS (BATCH)
        if (!empty($bsInsertBatch)) {
            $this->bsModel->insertBatch($bsInsertBatch);
        }

        // UPDATE APS (PER ID)
        foreach ($apsUpdateBuffer as $idAps => $sisaBaru) {
            $this->ApsPerstyleModel
                ->set('sisa', $sisaBaru)
                ->where('idapsperstyle', $idAps)
                ->update();
        }

        $db->transComplete();

        // ===============================
        // TRANSACTION CHECK
        // ===============================
        if ($db->transStatus() === false) {
            throw new \RuntimeException('Import gagal, transaction rollback');
        }
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
        $pdk  = $this->request->getPost('pdk');
        $ids  = $this->request->getPost('id');
        $pos  = $this->request->getPost('po');
        $role = session()->get('role');

        $this->db->transStart();

        try {
            foreach ($ids as $key => $id) {
                $newPo = isset($pos[$key]) ? (int)$pos[$key] : 0;

                // Ambil nilai po_plus lama dari database
                $oldData = $this->ApsPerstyleModel
                    ->select('po_plus')
                    ->where('idapsperstyle', $id)
                    ->first();

                $oldPo = isset($oldData['po_plus']) ? (int)$oldData['po_plus'] : 0;

                // 🔹 Skip update jika nilai tidak berubah
                if ($newPo === $oldPo) {
                    continue;
                }

                // 🔹 Update hanya jika ada perubahan
                $updatePoPlus = $this->ApsPerstyleModel
                    ->set('po_plus', $newPo, false)
                    ->where('idapsperstyle', $id)
                    ->update();

                if (!$updatePoPlus) {
                    throw new \Exception('Gagal mengupdate po_plus untuk ID: ' . $id);
                }

                // 🔹 Jika kamu memang mau update sisa juga (sesuai logikamu sebelumnya)
                //    disarankan bukan sisa + po baru, tapi sesuaikan dengan perubahan
                //    (selisih perubahan saja)
                $selisih = $newPo - $oldPo;
                if ($selisih != 0) {
                    $updateSisa = $this->ApsPerstyleModel
                        ->set('sisa', 'sisa + ' . $selisih, false)
                        ->where('idapsperstyle', $id)
                        ->update();

                    if (!$updateSisa) {
                        throw new \Exception('Gagal mengupdate sisa untuk ID: ' . $id);
                    }
                }
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Terjadi kesalahan saat menyimpan data.');
            }

            return redirect()->back()->with('success', 'Data berhasil diupdate');
        } catch (\Exception $e) {
            $this->db->transRollback();
            return redirect()->to($role . '/viewModelPlusPacking/' . $pdk)
                ->with('error', 'Error: ' . $e->getMessage());
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
        $data = $this->bsModel->updateBs();
        $gagal = [];
        foreach ($data as $dt) {
            $id = $dt['idbs'];
            $model = $dt['mastermodel'];
            $size = $dt['size'];
            $deliv = $dt['delivery'];

            // Lakukan update dan simpan error jika gagal
            $update = $this->bsModel->update($id, ['no_model' => $model, 'size' => $size, 'delivery' => $deliv]);
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
            $data = $this->bsModel->getBsPerhari($bulan, $tahun, $area);
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

        ini_set('memory_limit', '512M');
        set_time_limit(180);

        $file = $this->request->getFile('excel_file');
        if (!$file || !$file->isValid() || $file->hasMoved()) {
            return redirect()->to(base_url(session()->get('role') . '/bsmesin'))
                ->with('error', 'File Excel tidak valid / sudah dipindahkan.');
        }
        // dd($file);
        // ===== [A] LOAD EXCEL: read-only + rangeToArray =====
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file->getPathname());
        $reader->setReadDataOnly(true);
        // ReadFilter untuk batasi area baca (hemat CPU)
        $reader->setReadFilter(new class implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter {
            public function readCell(string $columnAddress, int $row, string $worksheetName = ''): bool
            {
                // Baca B1 untuk tanggal, dan baris >=6 hanya sampai kolom T
                if ($row === 1 && $columnAddress === 'B') {
                    return true;
                }
                if ($row >= 6 && $columnAddress <= 'T') {
                    return true;
                }
                return false;
            }
        });


        $spreadsheet = $reader->load($file->getPathname());
        $sheet       = $spreadsheet->getActiveSheet();
        // dd ($sheet->toArray());
        // Tanggal produksi di B1 (raw)
        $tglRaw = trim((string)$sheet->getCell('B1')->getValue());
        // dd($tglRaw);
        if (is_numeric($tglRaw)) {
            $dateTime = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($tglRaw);
            $tgl_produksi = $dateTime ? $dateTime->format('Y-m-d') : null;
        } else {
            $tglRaw = str_replace('.', '-', $tglRaw);
            $dateTime = \DateTime::createFromFormat('d-m-Y', $tglRaw);
            $tgl_produksi = $dateTime ? $dateTime->format('Y-m-d') : null;
        }
        if (!$tgl_produksi) {
            return redirect()->back()->with('error', 'Tanggal produksi (B1) tidak valid.');
        }

        // Range data: A6..T{maxRow}
        $startRow = 6;
        $maxRow   = $sheet->getHighestRow();
        if ($maxRow < $startRow) {
            return redirect()->back()->with('error', 'Tidak ada data pada sheet.');
        }
        // rangeToArray: raw values, tanpa format/rumus
        $rows = $sheet->rangeToArray("A{$startRow}:T{$maxRow}", null, true, false, false);
        // dd ($rows);

        // ===== [B] 1x FETCH KARYAWAN per AREA -> MAP =====
        $operatorMap = []; // nama_karyawan (trim) -> array data karyawan
        try {
            // Disarankan sediakan endpoint area-only (tanpa nama):
            //   GET /api/getdataforbs/{area}
            $apiUrl = api_url('hris') . "getdataforbs/{$area}";
            $resp = @file_get_contents($apiUrl);
            // dd ($resp);
            if ($resp !== false) {
                $list = json_decode($resp, true) ?: [];
                foreach ($list as $k) {
                    $nm = trim((string)($k['nama_karyawan'] ?? ''));
                    if ($nm !== '') {
                        $operatorMap[$nm] = $k;
                    }
                }
            } else {
                log_message('error', "Gagal akses API area-only: {$apiUrl}");
            }
        } catch (\Throwable $e) {
            log_message('error', 'HR API error: ' . $e->getMessage());
        }
        // dd ($operatorMap);
        // Jika endpoint area-only belum ada, fallback partial: biarkan $operatorMap kosong,
        // lalu id_karyawan akan null. (Atau: tambahkan blok fallback fetch-per-nama jika diperlukan.)

        // ===== [C] RAKIT DATA SEKALIAN -> INSERT BATCH / UPSERT =====
        // Indeks kolom dari rangeToArray (A=0, B=1, C=2, ...)
        $IDX_NO_MESIN = 1; // B
        $IDX_NO_MODEL = 2; // C
        $IDX_INISIAL  = 3; // D
        $IDX_OP_A     = 5; // F
        $IDX_OP_B     = 6; // G
        $IDX_OP_C     = 7; // H

        // Qty mapping (sesuaikan dengan struktur sheet)
        $QTY_PCS = [0 => 9,  1 => 12, 2 => 15]; // J, M, P
        $QTY_GRM = [0 => 17, 1 => 18, 2 => 19]; // R, S, T
        $SHIFT   = [0 => 'A', 1 => 'B', 2 => 'C'];
        $OP_COLS = [0 => $IDX_OP_A, 1 => $IDX_OP_B, 2 => $IDX_OP_C];

        $today = date('Y-m-d H:i:s');
        $failedRows = [];
        $inserts = [];     // kumpulan row baru untuk di-upsert
        $sizeCache = [];   // cache getSizes per "no_model|inisial"

        // Loop semua baris sheet
        foreach ($rows as $offset => $cols) {
            $rowIndex = $startRow + $offset; // nomor baris asli di Excel
            $noMesin  = trim((string)($cols[$IDX_NO_MESIN] ?? ''));
            $noModel  = trim((string)($cols[$IDX_NO_MODEL] ?? ''));
            $inisial  = trim((string)($cols[$IDX_INISIAL] ?? ''));

            // Validasi minimal — jika tidak ada model/mesin, lanjut baris berikutnya
            if ($noModel === '' || $noMesin === '') {
                continue;
            }

            // Resolve size (cached)
            $sizeKey = $noModel . '|' . $inisial;
            // dd ($sizeKey);
            if (!array_key_exists($sizeKey, $sizeCache)) {
                $sz = $this->ApsPerstyleModel->getSizes($noModel, $inisial);
                $sizeCache[$sizeKey] = (!empty($sz) && !empty($sz['size'])) ? $sz['size'] : null;
            }
            $size = $sizeCache[$sizeKey];
            // dd ($size);

            if (!$size) {
                $failedRows[] = [
                    'row'     => $rowIndex,
                    'shift'   => null,
                    'reason'  => "No Model/Inisial tidak ditemukan (model='{$noModel}', inisial='{$inisial}')"
                ];
                // tidak perlu stop seluruh proses, lanjut baris lain
                continue;
            }

            // Tiga shift per baris (A/B/C)
            for ($i = 0; $i < 3; $i++) {
                $opName  = trim((string)($cols[$OP_COLS[$i]] ?? ''));
                if ($opName === '') {
                    continue; // tidak ada operator → skip
                }

                // Ambil qty (raw -> cast)
                $qtyPcs  = (int)($cols[$QTY_PCS[$i]] ?? 0);
                $qtyGram = (float)($cols[$QTY_GRM[$i]] ?? 0.0);



                // Ambil id_karyawan dari map (jika tidak ada, tetap null)
                $idKaryawan = null;
                if (isset($operatorMap[$opName]) && isset($operatorMap[$opName]['id_karyawan'])) {
                    $idKaryawan = $operatorMap[$opName]['id_karyawan'];
                }

                $inserts[] = [
                    'tanggal_produksi' => $tgl_produksi,
                    'id_karyawan'      => $idKaryawan,     // bisa null
                    'nama_karyawan'    => $opName,
                    'shift'            => $SHIFT[$i],
                    'area'             => $area,
                    'no_model'         => $noModel,
                    'size'             => $size,
                    'inisial'          => $inisial,
                    'no_mesin'         => $noMesin,
                    'qty_pcs'          => $qtyPcs,
                    'qty_gram'         => $qtyGram,
                    'created_at'       => $today,
                ];
            }
        }
        // dd ($inserts);
        if (empty($inserts)) {
            return redirect()->to(base_url(session()->get('role') . '/bsmesin'))
                ->with('error', 'Tidak ada data valid untuk diimpor.');
        }

        // ===== [E] UPSERT BATCH (chunked) =====
        // Pastikan sudah ada UNIQUE KEY seperti:
        // ALTER TABLE bs_mesin_mc
        // ADD UNIQUE KEY uq_bs (tanggal_produksi, area, no_model, size, inisial, no_mesin, shift);
        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            $chunkSize = 500; // sesuaikan
            for ($i = 0; $i < count($inserts); $i += $chunkSize) {
                $chunk = array_slice($inserts, $i, $chunkSize);

                // Build upsert raw (MySQL)
                // Kolom yang diinsert:
                $cols = [
                    'tanggal_produksi',
                    'id_karyawan',
                    'nama_karyawan',
                    'shift',
                    'area',
                    'no_model',
                    'size',
                    'inisial',
                    'no_mesin',
                    'qty_pcs',
                    'qty_gram',
                    'created_at'
                ];
                $colList = implode(',', array_map(fn($c) => "`{$c}`", $cols));

                // Placeholder values (?, ?, ..., ?)
                $valuesPart = '(' . implode(',', array_fill(0, count($cols), '?')) . ')';
                $valuesAll  = implode(',', array_fill(0, count($chunk), $valuesPart));
                // dd($colList,$valuesPart, $valuesAll, $chunk);
                // ON DUPLICATE KEY UPDATE — hanya update qty & updated_at  
                $sql = "INSERT INTO `bs_mesin` ({$colList}) VALUES {$valuesAll}
                    ON DUPLICATE KEY UPDATE
                      `qty_pcs` = VALUES(`qty_pcs`),
                      `qty_gram`= VALUES(`qty_gram`),
                      `nama_karyawan` = VALUES(`nama_karyawan`),
                      `id_karyawan`   = VALUES(`id_karyawan`),
                      `update_at` = VALUES(`created_at`)";
                // log_message('debug', 'query'.$sql);
                // Flatten bind
                $bind = [];
                foreach ($chunk as $r) {
                    foreach ($cols as $c) {
                        $bind[] = $r[$c] ?? null;
                    }
                }
                // dd($colList, $valuesPart, $valuesAll, $chunk,$bind);

                $db->query($sql, $bind);
            }

            $db->transCommit();
        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', 'Import BS upsert gagal: ' . $e->getMessage());
            return redirect()->to(base_url(session()->get('role') . '/bsmesin'))
                ->with('error', 'Gagal mengimpor data (DB Error).');
        }

        // ===== [F] LAPORAN ERROR (jika ada) =====
        if (!empty($failedRows)) {
            $shiftToColumn = [
                'A' => 'Kolom F (Operator Shift A)',
                'B' => 'Kolom G (Operator Shift B)',
                'C' => 'Kolom H (Operator Shift C)',
            ];
            $details = array_map(function ($e) use ($shiftToColumn) {
                $where = $e['shift'] ? ($shiftToColumn[$e['shift']] ?? "Shift {$e['shift']}") : 'Baris';
                return "{$where} baris ke {$e['row']} - {$e['reason']}";
            }, $failedRows);

            return redirect()->to(base_url(session()->get('role') . '/bsmesin'))
                ->with('error', 'Beberapa data gagal diimpor:')
                ->with('error_list', $details);
        }

        return redirect()->to(base_url(session()->get('role') . '/bsmesin'))
            ->with('success', 'Data BS Mesin berhasil diimpor (optimized).');
    }
    public function importPerbaikanArea()
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
                        $this->prossesPerbaikan($batchData, $db, $failedRows);
                        $batchData = []; // Reset batch data
                    }
                }
            }

            // Process any remaining data
            if (!empty($batchData)) {
                $this->prossesPerbaikan($batchData, $db, $failedRows);
            }

            // Prepare notification message for failed rows
            if (!empty($failedRows)) {
                $failedRowsStr = implode(', ', $failedRows);
                $errorMessage = "Baris berikut gagal diimpor: $failedRowsStr";
                return redirect()->to(base_url(session()->get('role') . '/perbaikanArea'))->with('error', $errorMessage);
            }

            return redirect()->to(base_url(session()->get('role') . '/perbaikanArea'))->withInput()->with('success', 'Data Berhasil di Import');
        } else {
            return redirect()->to(base_url(session()->get('role') . '/perbaikanArea'))->with('error', 'No data found in the Excel file');
        }
    }
    private function prossesPerbaikan($batchData, $db, &$failedRows)
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
                        'tgl_perbaikan' => $tglprod,
                        'idapsperstyle' => $id,
                        'area' => $area,
                        'no_label' => $data[22],
                        'no_box' => $data[23],
                        'no_mc' => $data[25],
                        'qty' => $qty,
                        'kode_deffect' => $kodeDeffect,

                    ];
                    $insert = $this->perbaikanModel->insert($datainsert);
                    if ($insert) {

                        continue;
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
    public function reportGlobalProduksi()
    {
        $no_model = $this->request->getGet('no_model') ?? '';

        // Siapkan default grouped kosong
        $grouped = [];

        // Jika no_model kosong → skip proses tapi tetap kirim view
        if (!empty($no_model)) {

            // data utama
            $allData = $this->ApsPerstyleModel->geQtyByModel($no_model);

            // Jika data utama ada → proses
            if (!empty($allData)) {

                // Siapkan array mapping
                $prodMap = $bsMcMap = $pbMap = $bsStocklotMap = [];

                $allProd = $this->produksiModel
                    ->select('SUM(produksi.qty_produksi) AS qtyProd, UPPER(produksi.area) AS area, apsperstyle.size')
                    ->join('apsperstyle', 'apsperstyle.idapsperstyle=produksi.idapsperstyle')
                    ->where('apsperstyle.mastermodel', $no_model)
                    ->groupBy('produksi.area, apsperstyle.size')
                    ->findAll();

                foreach ($allProd as $row) {
                    $prodMap[$row['area']][$row['size']] = $row['qtyProd'];
                }

                // ===========================
                // 3. BS MESIN (1 QUERY)
                // ===========================
                $allBsMc = $this->bsMesinModel
                    ->select('UPPER(area) AS area, size, sum(qty_gram) AS bs_gram, sum(qty_pcs) AS qty_pcs')
                    ->where('no_model', $no_model)
                    ->groupBy('area, size')
                    ->findAll();

                foreach ($allBsMc as $row) {
                    $bsMcMap[$row['area']][$row['size']] = [
                        'qty_pcs' => $row['qty_pcs'],
                        'bs_gram' => $row['bs_gram'],
                    ];
                }

                // ===========================
                // 4. PERBAIKAN AREA (1 QUERY)
                // ===========================
                $allPb = $this->perbaikanAreaModel
                    ->select('UPPER(perbaikan_area.area) AS area, apsperstyle.size, SUM(perbaikan_area.qty) AS qtyPb')
                    ->join('apsperstyle', 'apsperstyle.idapsperstyle = perbaikan_area.idapsperstyle')
                    ->where('apsperstyle.mastermodel', $no_model)
                    ->where('apsperstyle.qty > 0')
                    ->groupBy('apsperstyle.factory, apsperstyle.size')
                    ->findAll();

                foreach ($allPb as $row) {
                    $pbMap[$row['area']][$row['size']] = $row['qtyPb'];
                }

                // ===========================
                // 5. BS STOCKLOT (1 QUERY)
                // ===========================
                $allBsStocklot = $this->bsModel
                    ->select('UPPER(data_bs.area) AS area, apsperstyle.size, SUM(data_bs.qty) AS qtyBs')
                    ->join('apsperstyle', 'apsperstyle.idapsperstyle = data_bs.idapsperstyle')
                    ->where('apsperstyle.mastermodel', $no_model)
                    ->groupBy('data_bs.area, apsperstyle.size')
                    ->findAll();

                foreach ($allBsStocklot as $row) {
                    $bsStocklotMap[$row['area']][$row['size']] = $row['qtyBs'];
                }

                // ========================================
                // 6. LOOP UTAMA (TANPA QUERY LAGI)
                // ========================================
                // dd($allData);
                foreach ($allData as $key => $id) {

                    $area = $id['factory'];   // field dari geQtyByModel
                    $size = $id['size'];

                    // PRODUKSI
                    $allData[$key]['prodPcs'] = $prodQty = $prodMap[$area][$size] ?? 0;
                    $allData[$key]['prodDz'] = round($prodQty / 24) ?? 0;

                    // BS MESIN
                    $allData[$key]['bsMcPcs']  = $bsMcQty = $bsMcMap[$area][$size]['qty_pcs'] ?? 0;
                    $allData[$key]['bsMcGram'] = $bsMcGram = $bsMcMap[$area][$size]['bs_gram'] ?? 0;
                    $bsMcPercen = $bsMcQty > 0
                        ? $bsMcQty / ($prodQty + $bsMcQty) * 100
                        : 0;
                    $allData[$key]['bsMcPercen'] = round($bsMcPercen) ?? 0;

                    // PERBAIKAN
                    $allData[$key]['pbAreaPcs'] = $pbQty = $pbMap[$area][$size] ?? 0;
                    $allData[$key]['pbAreaDz'] = round($pbQty / 24) ?? 0;
                    $pbAreaPercen = $pbQty > 0
                        ? ($pbQty / $prodQty) * 100
                        : 0;

                    $allData[$key]['pbAreaPercen'] = round($pbAreaPercen) ?? 0;

                    // BS STOCKLOT
                    $allData[$key]['bsStocklotPcs'] = $bsStk = $bsStocklotMap[$area][$size] ?? 0;
                    $allData[$key]['bsStocklotDz'] = round($bsStk / 24) ?? 0;
                    $bsStocklotPercen = $bsStk > 0
                        ? ($bsStk / $prodQty) * 100
                        : 0;
                    $allData[$key]['bsStocklotPercen'] = round($bsStocklotPercen) ?? 0;
                }
            }

            foreach ($allData as $row) {
                $area = $row['factory'];
                $grouped[$area][] = $row;
            }
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
            'noModel' => $no_model,
            'data' => $grouped,
        ];
        // dd($data);

        return view(session()->get('role') . '/Produksi/reportGlobal', $data);
    }
}
