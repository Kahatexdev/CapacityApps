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
use App\Models\BsMesinModel;
use App\Models\PenggunaanJarum;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpParser\Node\Stmt\Return_;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\{Border, Alignment, Fill};
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;


class UserController extends BaseController
{
    protected $filters;
    protected $jarumModel;
    protected $productModel;
    protected $produksiModel;
    protected $bookingModel;
    protected $orderModel;
    protected $ApsPerstyleModel;
    protected $liburModel;
    protected $BsMesinModel;
    protected $PenggunaanJarumModel;
    public function __construct()
    {


        $this->jarumModel = new DataMesinModel();
        $this->bookingModel = new BookingModel();
        $this->productModel = new ProductTypeModel();
        $this->produksiModel = new ProduksiModel();
        $this->orderModel = new OrderModel();
        $this->ApsPerstyleModel = new ApsPerstyleModel();
        $this->BsMesinModel = new BsMesinModel();
        $this->PenggunaanJarumModel = new PenggunaanJarum();
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
        $area = $this->jarumModel->getArea();
        $role = session()->get('role');
        $bulan = date('m');
        $month = date('F');
        $year = date('Y');
        $dataProduksi = $this->produksiModel->getProduksiPerhari($bulan, $year);
        $totalMesin = $this->jarumModel->getArea();
        $model = $this->ApsPerstyleModel->getPdkProduksi();


        $dataBuyer = $this->orderModel->getBuyer();
        $dataArea = $this->jarumModel->getArea();
        $dataJarum = $this->jarumModel->getJarum();
        $data = [
            'role' => session()->get('role'),
            'title' => 'Dashboard',
            'active1' => 'active',
            'active2' => '',
            'active3' => '',
            'targetProd' => 0,
            'produksiBulan' => 0,
            'produksiHari' => 0,
            'area' => $area,
            'bulan' => $month,
            'buyer' => $dataBuyer,
            'area' => $dataArea,
            'jarum' => $dataJarum,
            'models' => $model,

        ];
        return view(session()->get('role') . '/index', $data);
    }
    public function produksi()
    {
        $dataPdk = $this->ApsPerstyleModel->getPdkProduksi();
        $produksi = $this->produksiModel->getProduksiHarianArea();
        $dataBuyer = $this->orderModel->getBuyer();
        $dataArea = $this->jarumModel->getArea();
        $dataJarum = $this->jarumModel->getJarum();
        $model = $this->ApsPerstyleModel->getPdkProduksi();
        $data = [
            'role' => session()->get('role'),
            'title' => 'Dashboard',
            'active1' => '',
            'active3' => '',
            'active2' => 'active',
            'targetProd' => 0,
            'produksiBulan' => 0,
            'produksiHari' => 0,
            'pdk' => $dataPdk,
            'produksi' => $produksi,
            'buyer' => $dataBuyer,
            'area' => $dataArea,
            'jarum' => $dataJarum,
            'models' => $model,
        ];
        return view(session()->get('role') . '/produksi', $data);
    }
    public function bssetting()
    {

        $data = [
            'role' => session()->get('role'),
            'title' => 'Dashboard',
            'active1' => '',
            'active3' => '',
            'active2' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'targetProd' => 0,


        ];
        return view(session()->get('role') . '/bssetting', $data);
    }
    public function perbaikanView()
    {

        $data = [
            'role' => session()->get('role'),
            'title' => 'Dashboard',
            'active1' => '',
            'active3' => '',
            'active2' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'targetProd' => 0,


        ];
        return view(session()->get('role') . '/perbaikanarea', $data);
    }
    public function bsmesin()
    {
        $dataPdk = $this->ApsPerstyleModel->getPdkProduksi();
        $produksi = $this->produksiModel->getProduksiHarianArea();
        $dataBuyer = $this->orderModel->getBuyer();
        $dataArea = $this->jarumModel->getArea();
        $dataJarum = $this->jarumModel->getJarum();
        $area = session()->get('username');
        $month = [];
        for ($i = -3; $i <= 12; $i++) {
            $month[] = date('F-Y', strtotime("first day of $i month"));
        }

        $apiUrl = 'http://172.23.44.14/HumanResourceSystem/public/api/area/' . $area;

        try {
            // Attempt to fetch the API response
            $json = @file_get_contents($apiUrl);

            // Check if the response is valid
            if ($json === false) {
                throw new \Exception('API request failed.');
            }

            // Decode the JSON response
            $karyawan = json_decode($json, true);

            // Validate if decoding was successful
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON response.');
            }
        } catch (\Exception $e) {
            // Set default values in case of an error
            $karyawan[] = [
                'id_karyawan' => '-',
                'nama_karyawan' => 'No Karyawan Data Found',
            ];

            // Log the error for debugging purposes (optional)
            log_message('error', 'Error fetching API data: ' . $e->getMessage());
        }

        // Prepare data for the view
        $data = [
            'role' => session()->get('role'),
            'title' => 'BS Mesin',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'targetProd' => 0,
            // 'karyawan' => $karyawan,
            'month' => $month,
            'areas' => $area,
            'pdk' => $dataPdk,
            'produksi' => $produksi,
            'buyer' => $dataBuyer,
            'area' => $dataArea,
            'jarum' => $dataJarum,
        ];
        return view(session()->get('role') . '/bsmesin', $data);
    }
    public function getSize($model, $inisial)
    {
        $size = $this->ApsPerstyleModel->getSizeProduksi();

        // Cari data berdasarkan model dan inisial
        $sizeData = array_filter($size, function ($item) use ($model, $inisial) {
            return $item['mastermodel'] === $model && $item['inisial'] === $inisial;
        });

        // Ambil data pertama yang sesuai
        $sizeData = reset($sizeData);

        // Jika data ditemukan, kembalikan inisial
        return $this->response->setJSON(['size' => $sizeData['size'] ?? '']);
    }
    public function getInisial($noModel)
    {
        $inisial = $this->ApsPerstyleModel->getInProduksi();

        // Cari data inisial berdasarkan no_model
        $filteredData = array_filter($inisial, function ($item) use ($noModel) {
            return $item['mastermodel'] === $noModel;
        });

        // Ambil data pertama yang sesuai
        $inisialData = array_map(function ($item) {
            return [
                'inisial' => $item['inisial'],
            ];
        }, $filteredData);

        // Jika data ditemukan, kembalikan inisial
        return $this->response->setJSON(['inisial' => $inisialData]);
    }
    public function saveBsMesin()
    {
        $request = $this->request;

        // Data input utama
        $id = $request->getPost('nama');
        $namaKaryawan = $request->getPost('namakar');
        $kodeKartu    = $request->getPost('kode_kartu');
        $shift        = $request->getPost('shift');
        $tglProd      = $request->getPost('tgl_prod');
        $area         = session()->get('username');

        // Data detail
        $noMesin = $request->getPost('no_mesin');
        $inisial = $request->getPost('inisial');
        $noModel = $request->getPost('no_model');
        $size    = $request->getPost('size');
        $gram    = $request->getPost('gram');
        $pcs     = $request->getPost('pcs');

        // Pastikan data detail valid
        if (empty($noMesin) || !is_array($noMesin)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data detail mesin tidak valid.',
            ]);
        }

        // Iterasi data detail dan siapkan untuk batch insert
        $details = [];
        foreach ($noMesin as $index => $value) {


            $details[] = [
                'id_karyawan'   => $id, // atau field lain sebagai ID
                'nama_karyawan' => $namaKaryawan,
                'shift'         => $shift,
                'area'          => $area,
                'tanggal_produksi'   => $tglProd,
                'no_mesin'      => $noMesin[$index],
                'inisial'       => $inisial[$index],
                'no_model'      => $noModel[$index],
                'size'          => $size[$index],
                'qty_gram'      => $gram[$index],
                'qty_pcs'       => $pcs[$index],
            ];
        }

        // Batch insert data ke database
        if ($this->BsMesinModel->insertBatch($details)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data berhasil disimpan.',
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menyimpan data.',
            ]);
        }
    }
    public function penggunaanJarum()
    {
        $area = session()->get('username');
        $month = [];
        for ($i = -1; $i <= 12; $i++) {
            $month[] = date('F-Y', strtotime("first day of $i month"));
        }

        $apiUrl = 'http://172.23.44.14/HumanResourceSystem/public/api/area/' . $area;

        try {
            // Attempt to fetch the API response
            $json = @file_get_contents($apiUrl);

            // Check if the response is valid
            if ($json === false) {
                throw new \Exception('API request failed.');
            }

            // Decode the JSON response
            $karyawan = json_decode($json, true);

            // Validate if decoding was successful
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON response.');
            }
        } catch (\Exception $e) {
            // Set default values in case of an error
            $karyawan[] = [
                'id_karyawan' => '-',
                'nama_karyawan' => 'No Karyawan Data Found',
            ];

            // Log the error for debugging purposes (optional)
            log_message('error', 'Error fetching API data: ' . $e->getMessage());
        }

        // Prepare data for the view
        $data = [
            'role' => session()->get('role'),
            'area' => session()->get('username'),
            'title' => 'Penggunaan Jarum',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'targetProd' => 0,
            'karyawan' => $karyawan,
            'month' => $month,
        ];
        return view(session()->get('role') . '/jarum', $data);
    }
    public function savePenggunaanJarum()
    {
        $role = session()->get('role');
        $area = session()->get('username');
        $data = [
            'id_karyawan' => $this->request->getPost('idkary'),
            'nama_karyawan' => $this->request->getPost('namakar'),
            'kodeKartu' => $this->request->getPost('kode_kartu'),
            'tanggal' => $this->request->getPost('tgl'),
            'qty_jarum' => $this->request->getPost('pcs'),
            'area' => $area,
        ];
        $insert = $this->PenggunaanJarumModel->insert($data);
        if (!$insert) {
            return redirect()->to(base_url($role . '/penggunaanJarum'))->with('error', 'Data Gagal Disimpan');
        } else {
            return redirect()->to(base_url($role . '/penggunaanJarum'))->with('success', 'Data Berhasil Disimpan');
        }
    }
    public function penggunaanPerbulan($area, $bulan)
    {
        $jarumArea = $this->PenggunaanJarumModel->jarumPerbulan($area, $bulan);
        $data = [
            'role' => session()->get('role'),
            'area' => session()->get('username'),
            'title' => 'Penggunaan Jarum',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'jarum' => $jarumArea,
            'month' => $bulan,
        ];

        return view(session()->get('role') . '/jarumPerbulan', $data);
    }
    public function bsMesinPerbulan($area, $bulan)
    {
        // $bsPerbulan = $this->BsMesinModel->bsMesinPerbulan($area, $bulan);
        $bsPerbulan = $this->BsMesinModel->bsMesinPerbulan2($area, $bulan);
        $totalBsGram = $this->BsMesinModel->totalGramPerbulan($area, $bulan);
        $totalBsPcs = $this->BsMesinModel->totalPcsPerbulan($area, $bulan);
        $chartData = $this->BsMesinModel->ChartPdk($area, $bulan);


        // group data semua pdk dan size untuk ambil gw aktual / gw MU
        $dataOrder = [];
        foreach ($bsPerbulan as $item) {
            $key = $item['no_model'] . '|' . $item['size'];
            $dataOrder[$key] = [
                'no_model' => $item['no_model'],
                'size' => $item['size'],
            ];
        }
        // Hapus key-nya, biar jadi array numerik lagi
        $dataOrder = array_values($dataOrder);

        // get data gw aktual / gw MU
        $apiUrl = 'http://172.23.39.118/MaterialSystem/public/api/getAllGw';
        // Kirim data ke API pakai CodeIgniter HTTP client
        $dataGw = service('curlrequest')->post($apiUrl, [
            'json' => $dataOrder
        ]);
        $responseBody = $dataGw->getBody();
        $responseJson = json_decode($responseBody, true); // jadi array asosiatif
        $dataGwList = $responseJson['data'] ?? []; // ambil bagian 'data'

        // Data total START
        $totalProd = $this->produksiModel->produksiPerbulan($area, $bulan, $dataGwList);
        // Data total END

        // Data bs perbulan START
        // ambil gw untuk hitung bs gram ke pcs
        foreach ($bsPerbulan as &$bs) {
            $noModel = $bs['no_model'];
            $size = $bs['size'];

            $gwValue = 0;
            foreach ($dataGwList as $gwItem) {
                if (
                    strtoupper($gwItem['no_model']) === strtoupper($noModel) &&
                    strtoupper($gwItem['size']) === $size
                ) {
                    $gwValue = $gwItem['gw'];
                    break; // langsung keluar loop kalau udah ketemu
                }
            }
            if ($gwValue == 0) {
                log_message('warning', "⚠️ GW tidak ditemukan untuk {$noModel} / {$size}");
            }

            $bsGram = $bs['qty_gram'] > 0 ? round($bs['qty_gram'] / $gwValue) : 0;
            $bsPcs = $bs['qty_pcs'] + $bsGram;
            $totalBsDz = round($bsPcs / 24);

            // tambahkan gw ke data bs
            $bs['totalBsMc'] = $bsPcs;
        }
        unset($bs); // good practice

        // --- 🔽 Sekarang group berdasarkan nama_karyawan ---
        $groupedByKaryawan = [];

        foreach ($bsPerbulan as $row) {
            $nama = $row['nama_karyawan']; // ganti sesuai nama kolom kamu
            if (!isset($groupedByKaryawan[$nama])) {
                $groupedByKaryawan[$nama] = [
                    'nama_karyawan' => $nama,
                    'totalBsMc' => 0,
                    'totalQtyPcs' => 0,
                    'totalQtyGram' => 0,
                ];
            }

            $groupedByKaryawan[$nama]['totalBsMc'] += $row['totalBsMc'];
            $groupedByKaryawan[$nama]['totalQtyPcs'] += $row['qty_pcs'];
            $groupedByKaryawan[$nama]['totalQtyGram'] += $row['qty_gram'];
        }

        // ubah dari associative ke numerik array
        $groupedByKaryawan = array_values($groupedByKaryawan);

        // --- Urutkan dari yang terbanyak ---
        usort($groupedByKaryawan, function ($a, $b) {
            return $b['totalBsMc'] <=> $a['totalBsMc'];
        });
        // Data bs perbulan END

        // Data Chart START
        // Urutkan data berdasarkan totalGram dari besar ke kecil
        usort($chartData, function ($a, $b) {
            return $b['totalGram'] <=> $a['totalGram'];
        });
        // Ambil hanya 10 data teratas
        $chartData = array_slice($chartData, 0, 10);
        // Data Chart END

        $data = [
            'role' => session()->get('role'),
            'area' => session()->get('username'),
            'title' => 'BS Mesin',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'dataBs' => $groupedByKaryawan,
            'month' => $bulan,
            'totalbsgram' => $totalBsGram,
            'totalbspcs' => $totalBsPcs,
            'chart' => $chartData,
            'dataTotal' => $totalProd['final']
        ];
        return view(session()->get('role') . '/bsMesinPerbulan', $data);
    }

    public function exportPenggunaanPerbulan($area, $bulan)
    {
        $jarumArea = $this->PenggunaanJarumModel->jarumPerbulan($area, $bulan);

        // Generate Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet()->setTitle("Report Penggunaan Jarum");

        $styleTitle = [
            'font' => [
                'bold' => true, // Tebalkan teks
                'color' => ['argb' => 'FF000000'],
                'size' => 14
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER, // Alignment rata tengah
            ],
        ];

        $styleHeader = [
            'font' => [
                'bold' => true, // Tebalkan teks
                'color' => ['argb' => 'FF000000'],
                'size' => 12
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER, // Alignment rata tengah
            ],
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THIN, // Gaya garis tipis
                    'color' => ['argb' => 'FF000000'],    // Warna garis hitam
                ],
            ],
        ];
        $styleBody = [
            'font' => [
                'color' => ['argb' => 'FF000000'],
                'size' => 10
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER, // Alignment rata tengah
            ],
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THIN, // Gaya garis tipis
                    'color' => ['argb' => 'FF000000'],    // Warna garis hitam
                ],
            ],
        ];

        // Judul
        $sheet->setCellValue('A1', 'Penggunaan Jarum bulan ' . $bulan);
        $sheet->getStyle('A1')->applyFromArray($styleTitle);

        $rowHeader = 3;
        $sheet->setCellValue('A' . $rowHeader, 'Tanggal');
        $sheet->setCellValue('B' . $rowHeader, 'Nama');
        $sheet->setCellValue('C' . $rowHeader, 'Qty');
        $sheet->setCellValue('D' . $rowHeader, 'Area');
        // style header
        $sheet->getStyle('A' . $rowHeader)->applyFromArray($styleHeader);
        $sheet->getStyle('B' . $rowHeader)->applyFromArray($styleHeader);
        $sheet->getStyle('C' . $rowHeader)->applyFromArray($styleHeader);
        $sheet->getStyle('D' . $rowHeader)->applyFromArray($styleHeader);

        $rowBody = $rowHeader++;

        foreach ($jarumArea as $data) {
            $sheet->setCellValue('A' . $rowBody, $data['tanggal']);
            $sheet->setCellValue('B' . $rowBody, $data['nama_karyawan']);
            $sheet->setCellValue('C' . $rowBody, $data['total_jarum']);
            $sheet->setCellValue('D' . $rowBody, $data['area']);
            //  style body
            $sheet->getStyle('A' . $rowBody)->applyFromArray($styleBody);
            $sheet->getStyle('B' . $rowBody)->applyFromArray($styleBody);
            $sheet->getStyle('C' . $rowBody)->applyFromArray($styleBody);
            $sheet->getStyle('D' . $rowBody)->applyFromArray($styleBody);
        }
        // Export file ke Excel
        $writer = new Xlsx($spreadsheet);
        $filename = 'Penggunaan Jarum.xlsx';

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    public function inputinisial()
    {
        $data = [
            'inisial' => $this->request->getPost('inisial'),
            'pdk' => $this->request->getPost('pdk'),
            'size' => $this->request->getPost('size'),
        ];

        $role = session()->get('role');
        $pdk = $data['pdk'];
        $jarum = $this->request->getPost('jarum');
        $redirectUrl = base_url("$role/detailPdk/$pdk/$jarum");

        $update = $this->ApsPerstyleModel->updateInisial($data);

        if ($update === false) {
            log_message('error', "Update gagal untuk PDK: {$pdk}, Size: {$data['size']}");
            return redirect()->to($redirectUrl)->with('error', 'Terjadi kesalahan saat mengupdate inisial');
        }

        if ($update === 0) {
            return redirect()->to($redirectUrl)->with('warning', 'Tidak ada perubahan pada inisial');
        }

        return redirect()->to($redirectUrl)->with('success', 'Inisial berhasil diubah');
    }
    public function importinisial()
    {
        ini_set('memory_limit', '512M');
        set_time_limit(500);

        $file = $this->request->getFile('excel_file');
        if ($file->isValid() && !$file->hasMoved()) {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
            $worksheet = $spreadsheet->getActiveSheet();

            $startRow = 4; // Ganti dengan nomor baris mulai
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

            return redirect()->to(base_url(session()->get('role') . '/dataorderperarea/' . session()->get('username')))->withInput()->with('success', 'Data Berhasil di Import');
        } else {
            return redirect()->to(base_url(session()->get('role') . '/dataorderperarea/' . session()->get('username')))->with('error', 'No data found in the Excel file');
        }
    }
    private function processBatchnew($batchData, $db, &$failedRows)
    {
        $db->transStart();
        foreach ($batchData as $batchItem) {
            $rowIndex = $batchItem['rowIndex'];
            $data = $batchItem['data'];
            try {
                $no_model = $data[0];
                $style = $data[1];
                $inisial = $data[2];
                $update = [
                    'pdk' => $no_model,
                    'size' => $style,
                    'inisial' => $inisial,
                ];
                $update = $this->ApsPerstyleModel->updateInisial($update);
                if (!$update) {
                    $failedRows[] = 'Error on row ' . $rowIndex . ': Gagal Update ';
                }
            } catch (\Exception $e) {
                log_message('error', 'Error in row ' . $rowIndex . ': ' . $e->getMessage());
                $failedRows[] = 'Error on row ' . $rowIndex . ': ' . $e->getMessage();
            }
        }
        $db->transComplete();
    }
    public function deleteBsMc()
    {
        $area = $this->request->getPost('area');
        $awal = $this->request->getPost('awal');
        $akhir = $this->request->getPost('akhir');

        $delete = $this->BsMesinModel->deleteBsRange($area, $awal, $akhir);

        if ($delete) {
            return redirect()->back()->with('success', 'Data berhasil dihapus.');
        } else {
            return redirect()->back()->with('error', 'Data gagal dihapus.');
        }
    }
}
