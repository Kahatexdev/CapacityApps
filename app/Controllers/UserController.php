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
        $data = [
            'role' => session()->get('role'),
            'title' => 'Dashboard',
            'active1' => 'active',
            'active2' => '',
            'active3' => '',
            'targetProd' => 0,
            'produksiBulan' => 0,
            'produksiHari' => 0

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
    public function bsmesin()
    {
        $dataPdk = $this->ApsPerstyleModel->getPdkProduksi();
        $produksi = $this->produksiModel->getProduksiHarianArea();
        $dataBuyer = $this->orderModel->getBuyer();
        $dataArea = $this->jarumModel->getArea();
        $dataJarum = $this->jarumModel->getJarum();
        $area = session()->get('username');
        $month = [];
        for ($i = -1; $i <= 12; $i++) {
            $month[] = date('F-Y', strtotime("first day of $i month"));
        }

        $apiUrl = 'http://172.23.44.14/SkillMapping/public/api/area/' . $area;

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
            'karyawan' => $karyawan,
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
    public function getSize($noModel)
    {
        $pdk = $this->ApsPerstyleModel->getSizeProduksi();

        // Filter data berdasarkan no_model
        $filteredData = array_filter($pdk, function ($item) use ($noModel) {
            return $item['mastermodel'] === $noModel;
        });

        // Ambil semua data inisial dan idapsperstyle
        $sizeList = array_map(function ($item) {
            return [
                'size' => $item['size'],
            ];
        }, $filteredData);

        // Kembalikan daftar inisial sebagai JSON
        return $this->response->setJSON(['size' => $sizeList]);
    }
    public function getInisial($size)
    {
        $inisial = $this->ApsPerstyleModel->getInProduksi();

        // Cari data inisial berdasarkan size
        $inisialData = array_filter($inisial, function ($item) use ($size) {
            return $item['size'] === $size;
        });

        // Ambil data pertama yang sesuai
        $inisialData = reset($inisialData);

        // Jika data ditemukan, kembalikan inisial
        return $this->response->setJSON(['inisial' => $inisialData['inisial'] ?? '']);
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

        $apiUrl = 'http://172.23.44.14/SkillMapping/public/api/area/' . $area;

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
        $bsPerbulan = $this->BsMesinModel->bsMesinPerbulan($area, $bulan);
        $totalBsGram = $this->BsMesinModel->totalGramPerbulan($area, $bulan);
        $totalBsPcs = $this->BsMesinModel->totalPcsPerbulan($area, $bulan);
        $chartData = $this->BsMesinModel->ChartPdk($area, $bulan);
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
            'dataBs' => $bsPerbulan,
            'month' => $bulan,
            'totalbsgram' => $totalBsGram,
            'totalbspcs' => $totalBsPcs,
            'chart' => $chartData
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
}
