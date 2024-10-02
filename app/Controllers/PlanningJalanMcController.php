<?php

namespace App\Controllers;

use DateTime;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\DataMesinModel;
use App\Models\OrderModel;
use App\Models\BookingModel;
use App\Models\ProductTypeModel;
use App\Models\ApsPerstyleModel;
use App\Models\ProduksiModel;
use App\Models\LiburModel;
use App\Models\MonthlyMcModel;
use App\Models\AreaMachineModel;
use App\Models\DetailAreaMachineModel;
use LengthException;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Week;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\{Border, Alignment, Fill};
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class PlanningJalanMcController extends BaseController
{
    protected $filters;
    protected $jarumModel;
    protected $productModel;
    protected $produksiModel;
    protected $bookingModel;
    protected $orderModel;
    protected $ApsPerstyleModel;
    protected $liburModel;
    protected $globalModel;
    protected $areaMcModel;
    protected $detailAreaMc;

    public function __construct()
    {
        $this->detailAreaMc = new DetailAreaMachineModel();
        $this->areaMcModel = new AreaMachineModel();
        $this->globalModel = new MonthlyMcModel();
        $this->jarumModel = new DataMesinModel();
        $this->bookingModel = new BookingModel();
        $this->productModel = new ProductTypeModel();
        $this->produksiModel = new ProduksiModel();
        $this->orderModel = new OrderModel();
        $this->ApsPerstyleModel = new ApsPerstyleModel();
        $this->liburModel = new LiburModel();
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

    public function excelPlanningJlMc($bulan)
    {
        $role = session()->get('role');
        $date = DateTime::createFromFormat('F-Y', $bulan);
        $bulanIni = $date->format('F-Y');
        $awalBulan = $date->format('Y-m-01');

        $filteredArea = $this->jarumModel->getArea();
        $area = array_filter($filteredArea, function ($item) {
            return strpos($item, 'Gedung') === false;
        });
        $area = array_values($area);
        $monthlyData = [];
        foreach ($area as $ar) {
            $mesin = $this->jarumModel->areaMc($ar);
            $totalMesin = 0;
            $planningMc = 0;
            $outputDz = 0;
            foreach ($mesin as $jarum) {
                $sisaOrder = $this->ApsPerstyleModel->ambilSisaOrder($ar, $awalBulan, $jarum['jarum']);
                $monthlyData[$ar][$jarum['jarum']]['kebutuhanMesin'] = $sisaOrder['totalKebMesin'];
                $monthlyData[$ar][$jarum['jarum']]['output'] = $sisaOrder['outputDz'];
                $monthlyData[$ar][$jarum['jarum']]['jr'] = $jarum['jarum'];
                $totalMesin += $jarum['total'];
                $planningMc += $sisaOrder['totalKebMesin'];
                $outputDz += $sisaOrder['outputDz'];
            }
            $monthlyData[$ar]['totalMesin'] = $totalMesin;
            $monthlyData[$ar]['planningMc'] = $planningMc;
            $monthlyData[$ar]['outputDz'] = $outputDz;
        }
        $totalAllMesin = 0;

        $totalOutput = 0;
        $totalMcPlanning = 0;

        foreach ($monthlyData as $data) {
            $totalAllMesin += $data['totalMesin'];
            $totalOutput += $data['outputDz'];
            $totalMcPlanning += $data['planningMc'];
        }
        $totalKebGloves = 0; // Initialize outside the loop
        foreach ($monthlyData['KK8J'] as $data) {
            if (is_array($data) && isset($data['kebutuhanMesin'])) {
                $totalKebGloves += $data['kebutuhanMesin'];
            }
        }

        $totalKebSock = $totalMcPlanning - $totalKebGloves;
        $totalMcSocks = $this->jarumModel->totalMcSock();
        $totalMcSocks = intval($totalMcSocks['total']);
        $totalMcGloves = $totalAllMesin - $totalMcSocks;

        $persenSocks = round(($totalKebSock / $totalMcSocks) * 100);
        $persenGloves = round(($totalKebGloves / $totalMcGloves) * 100);
        $persenTotal = round(($totalMcPlanning / $totalAllMesin) * 100);
        $summary = [
            'totalMc' => $totalAllMesin,
            'OutputTotal' => $totalOutput,
            'totalPlanning' => $totalMcPlanning,
            'totalPersen' => $persenTotal,

            'mcSocks' => $totalMcSocks,
            'planMcSocks' => $totalKebSock,
            'persenSocks' => $persenSocks,

            'mcGloves' => $totalMcGloves,
            'planMcGloves' => $totalKebGloves,
            'persenGloves' => $persenGloves
        ];

        // Generate Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle("Planning Mesin");

        $styleGlobal = [
            'font' => [
                'bold' => true, // Tebalkan teks
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT, // Alignment rata tengah
            ],
        ];

        $styleGlobalBody = [
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT, // Alignment rata tengah
            ],
        ];

        // border
        $styleHeader = [
            'font' => [
                'bold' => true, // Tebalkan teks
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
            'fill' => [
                'fillType' => Fill::FILL_SOLID, // Jenis pengisian solid
                'startColor' => ['argb' => 'FF67748e'], // Warna latar belakang biru tua (HEX)
            ],
        ];
        $styleBody = [
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
        $styleTotal = [
            'font' => [
                'bold' => true, // Tebalkan teks
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_RIGHT, // Alignment rata tengah
            ],
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THIN, // Gaya garis tipis
                    'color' => ['argb' => 'FF000000'],    // Warna garis hitam
                ],
            ],
        ];

        // Set lebar kolom secara manual
        $sheet->getColumnDimension('A')->setWidth(25); // Lebar kolom A diatur menjadi 20
        $sheet->getColumnDimension('B')->setWidth(25); // Lebar kolom B diatur menjadi 25
        $sheet->getColumnDimension('C')->setWidth(25); // Lebar kolom C diatur menjadi 30

        // Set header untuk global data
        $sheet->setCellValue('A1', 'Planning Jalan Mesin ' . $bulan);
        $sheet->mergeCells('A1:C1');
        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Global
        $sheet->setCellValue('A3', 'Global');
        $sheet->setCellValue('A4', 'Total Mesin : ' . $summary['totalMc']);
        $sheet->setCellValue('A5', 'Total Planning : ' . $summary['totalPlanning']);
        $sheet->setCellValue('A6', 'Persentase :' . $summary['totalPersen'] . '%');
        $sheet->setCellValue('A7', 'Total Output : ' . $summary['OutputTotal']);
        $sheet->getStyle('A3')->applyFromArray($styleGlobal);
        $sheet->getStyle('A4')->applyFromArray($styleGlobalBody);
        $sheet->getStyle('A5')->applyFromArray($styleGlobalBody);
        $sheet->getStyle('A6')->applyFromArray($styleGlobalBody);
        $sheet->getStyle('A7')->applyFromArray($styleGlobalBody);

        $sheet->setCellValue('B3', 'Socks');
        $sheet->setCellValue('B4', 'Total Mesin : ' . $summary['mcSocks']);
        $sheet->setCellValue('B5', 'Total Planning : ' . $summary['planMcSocks']);
        $sheet->setCellValue('B6', 'Persentase : ' . $summary['persenSocks'] . '%');
        $sheet->getStyle('B3')->applyFromArray($styleGlobal);
        $sheet->getStyle('B4')->applyFromArray($styleGlobalBody);
        $sheet->getStyle('B5')->applyFromArray($styleGlobalBody);
        $sheet->getStyle('B6')->applyFromArray($styleGlobalBody);


        $sheet->setCellValue('C3', 'Gloves');
        $sheet->setCellValue('C4', 'Total Mesin : ' . $summary['mcGloves']);
        $sheet->setCellValue('C5', 'Total Planning : ' . $summary['planMcGloves']);
        $sheet->setCellValue('C6', 'Persentase : ' . $summary['persenGloves'] . '%');
        $sheet->getStyle('C3')->applyFromArray($styleGlobal);
        $sheet->getStyle('C4')->applyFromArray($styleGlobalBody);
        $sheet->getStyle('C5')->applyFromArray($styleGlobalBody);
        $sheet->getStyle('C6')->applyFromArray($styleGlobalBody);

        // Looping untuk menampilkan setiap area dan jarum
        $row = 9; // Start row header area

        $areaCounter = 0; // Counter untuk menghitung area
        foreach ($monthlyData as $area => $jarum) {

            // Header per area
            $sheet->setCellValue("A$row", $area);
            $sheet->getStyle("A$row")->applyFromArray($styleGlobal);
            $row++;

            // Total mesin, planning, dan output per area
            $sheet->setCellValue("A$row", 'Total Mesin: ' . $jarum['totalMesin']);
            $sheet->setCellValue("B$row", 'Planning Mesin: ' . $jarum['planningMc']);
            $sheet->setCellValue("C$row", 'Output (dz): ' . $jarum['outputDz']);
            $sheet->getStyle("A$row:C$row")->applyFromArray($styleGlobalBody);
            $row++;

            // Set header untuk tabel jarum di bawah setiap area
            $sheet->setCellValue("A$row", 'Jarum');
            $sheet->setCellValue("B$row", 'Kebutuhan Mesin');
            $sheet->mergeCells("B$row:C$row"); // Merge kolom B dan C untuk header
            $sheet->getStyle("A$row:C$row")->applyFromArray($styleHeader);
            $row++;

            // Looping data jarum dalam setiap area
            foreach ($jarum as $jr) {
                if (is_array($jr)) { // Pastikan yang di-loop hanya array jarum
                    $sheet->setCellValue("A$row", $jr['jr']);
                    $sheet->setCellValue("B$row", $jr['kebutuhanMesin']);
                    $sheet->mergeCells("B$row:C$row"); // Merge kolom B dan C untuk data kebutuhan mesin
                    $sheet->getStyle("A$row:C$row")->applyFromArray($styleBody);
                    $row++;
                }
            }

            // Spacer antar area: tambahkan beberapa baris kosong
            $row += 1; // Tambah 1 baris kosong
        }


        // Set sheet pertama sebagai active sheet
        $spreadsheet->setActiveSheetIndex(0);

        // Export file ke Excel
        $writer = new Xlsx($spreadsheet);
        $filename = 'Planning Jalan MC ' . $bulanIni . '.xlsx';

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    public function saveMonthlyMc()
    {
        // Set CORS Headers untuk mengizinkan request dari origin lain
        $this->response->setHeader('Access-Control-Allow-Origin', '*');
        $this->response->setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
        $this->response->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');

        // Tangani preflight request (OPTIONS) untuk CORS
        if ($this->request->getMethod() === 'options') {
            return $this->response->setStatusCode(200); // Tidak perlu lanjut jika preflight request
        }

        // Ambil data JSON dari fetch
        $jsonData = $this->request->getJSON(true);

        // Cek jika JSON tidak valid atau kosong
        if (!$jsonData) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid JSON data'])->setStatusCode(400);
        }

        // Ambil data global, area, dan detail dari JSON
        $global = $jsonData['global'] ?? null;
        $areaData = $jsonData['area'] ?? [];
        $detailData = $jsonData['detail'] ?? [];

        if (!$global || empty($areaData) || empty($detailData)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Incomplete data'])->setStatusCode(400);
        }

        // Insert ke tabel global
        $globalData = [
            'judul' => $global['judulPlan'],
            'total_mc' => $global['globalMc'],
            'planning_mc' => $global['globalPlan'],
            'total_output' => $global['globalOutput'],
            'mc_socks' => $global['ttlMcSocks'],
            'plan_mc_socks' => $global['ttlPlanSocks'],
            'mc_gloves' => $global['ttlMcGloves'],
            'plan_mc_gloves' => $global['ttlPlanGloves'],
        ];

        // Cek apakah sudah ada data global dengan judul yang sama
        $globalCheck = $this->globalModel->cekExist($globalData);
        if (!$globalCheck) {
            $this->globalModel->insert($globalData);
            $idGlobal = $this->globalModel->cekExist($globalData)['id_monthly_mc'];
        } else {
            $idGlobal = $globalCheck['id_monthly_mc'];
            $this->globalModel->update($idGlobal, $globalData);
        }

        // Loop untuk insert area ke tabel area_mc
        foreach ($areaData as $key => $area) {
            $areaInsert = [
                'id_monthly_mc' => $idGlobal,
                'area' => $area['area'],
                'total_mc' => $area['ttlMc'],
                'planning_mc' => $area['planMc'],
                'output' => $area['outputDz']
            ];

            $cekDataArea = $this->areaMcModel->existData($areaInsert);
            if (!$cekDataArea) {
                $this->areaMcModel->insert($areaInsert);
                $idArea = $this->areaMcModel->existData($areaInsert)['id_area_machine'];
            } else {
                $idArea = $cekDataArea['id_area_machine'];
                $this->areaMcModel->update($idArea, $areaInsert);
            }

            // Loop untuk insert detail area (jarum dan kebutuhan mesin)
            foreach ($detailData as $detail) {
                if ($detail['areaDetail'] == $area['area']) {  // Cocokkan area
                    $detailInsert = [
                        'id_area_machine' => $idArea,
                        'jarum' => $detail['jarum'],
                        'planning_mc' => $detail['kebMesin']
                    ];
                    $cekDataDetail = $this->detailAreaMc->cekData($detailInsert);
                    if (!$cekDataDetail) {
                        $this->detailAreaMc->insert($detailInsert);
                        $idDetail = $this->detailAreaMc->cekData($detailInsert)['id_detail_area_machine'];
                    } else {
                        $idDetail = $cekDataDetail['id_detail_area_machine'];
                        $this->detailAreaMc->update($idDetail, $detailInsert);
                    }
                }
            }
        }

        return $this->response->setJSON(['status' => 'success', 'message' => 'Data saved successfully'])->setStatusCode(200);
    }


    public function viewPlan($judul)
    {
        $role = session()->get('role');

        $global = $this->globalModel->getData($judul);
        $idGlobal = $global['id_monthly_mc'];
        $areaMachine = $this->areaMcModel->getData($idGlobal);
        $monthlyData = [];
        foreach ($areaMachine as $area) {
            $idAreaMc = $area['id_area_machine'];
            $monthlyData[$area['area']]['totalMesin'] = $area['total_mc'];
            $monthlyData[$area['area']]['planningMc'] = $area['planning_mc'];
            $monthlyData[$area['area']]['outputDz'] = $area['output'];
            $monthlyData[$area['area']]['jarum'] = $this->detailAreaMc->getData($idAreaMc);
        }

        $data = [
            'role' => $role,
            'title' =>  $judul,
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'summary' => $global,
            'data' => $monthlyData
        ];

        return view($role . '/Planning/viewPlanMonth', $data);
    }
}
