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

        // Log the input for debugging
        log_message('info', 'Received bulan value: ' . $bulan);

        // Parse the $bulan string to a DateTime object
        $date = \DateTime::createFromFormat('F-Y', $bulan);
        if (!$date) {
            throw new \Exception("Invalid date format: '{$bulan}'. Please use 'F-Y' format.");
        }

        $bulanIni = $date->format('F-Y');
        $startDate = new \DateTime($date->format('Y-m-01')); // First day of the given month
        $monthlyData = [];
        for ($i = 0; $i < 5; $i++) {
            $startOfWeek = clone $startDate;
            $startOfWeek->modify("+$i week");

            // Ensure we start on Monday and stay within the month
            $startOfWeek->modify($startOfWeek->format('N') === '1' ? 'this Monday' : 'next Monday');
            if ($startOfWeek->format('m') !== $startDate->format('m')) break;

            $endOfWeek = clone $startOfWeek;
            $endOfWeek->modify('Sunday this week');

            $monthlyData[] = [
                'week' => $i + 1,
                'start_date' => $startOfWeek->format('Y-m-d'),
                'end_date' => $endOfWeek->format('Y-m-d'),
                'number_of_days' => $startOfWeek->diff($endOfWeek)->days + 1,
            ];
        }

        $jarum = $this->jarumModel->getAreaAndJarum();
        $kebutuhanMesin = [];
        $outputDz = []; // Initialize outputDz array

        $areas = $this->jarumModel->getArea();
        $totalArea = [];
        foreach ($areas as $ar) {
            $totalArea[$ar] = $this->jarumModel->totalMcArea($ar);
        }
        // Fetch sisa orders efficiently
        foreach ($monthlyData as $wk) {
            foreach ($areas as $ar) {
                $outputDz[$wk['week']][$ar] = 0; // Initialize the outputDz for each week and area
                foreach ($jarum as $jr) {
                    $weekNumber = $wk['week'];
                    $sisaOrder = $this->ApsPerstyleModel->ambilSisaOrder($ar, $wk['start_date'], $jr['jarum']);

                    $kebutuhanMesin[$weekNumber][$ar][$jr['jarum']] = $sisaOrder['totalKebMesin'] ?? 0;
                    $outputDz[$weekNumber][$ar] += $sisaOrder['outputDz'] ?? 0; // Summing outputDz per week per area
                }
            }
        }

        // Generate Excel
        $spreadsheet = new Spreadsheet();

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

        for ($i = 1; $i <= count($monthlyData); $i++) {
            if (!empty($kebutuhanMesin[$i])) {
                $sheet = $spreadsheet->createSheet();
                $sheet->setTitle("Week-$i");

                $sheet->setCellValue('A1', 'REKOMENDASI PLANNING JALAN MC');
                $sheet->mergeCells('A1:AE1');
                $sheet->getStyle('A1')->getFont()->setBold(true);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Tambahkan header
                $sheet->setCellValue('A3', 'Area');
                $sheet->setCellValue('B3', 'Jumlah MC');
                $sheet->setCellValue('C3', 'Planning MC');
                $sheet->getStyle('A3')->applyFromArray($styleHeader);
                $sheet->getStyle('B3')->applyFromArray($styleHeader);
                $sheet->getStyle('C3')->applyFromArray($styleHeader);

                // Tambahkan header untuk setiap jenis jarum
                $col = 'D';
                foreach ($jarum as $jrm) {
                    $sheet->setCellValue($col . '3', $jrm['jarum']);
                    $sheet->getStyle($col . '3')->applyFromArray($styleHeader);
                    $col++;
                }

                $sheet->setCellValue($col . '3', 'Output (dz)');
                $sheet->getStyle($col . '3')->applyFromArray($styleHeader);

                // Tambahkan data per area
                $totalMcSocks = 0;
                $planMcSocks = 0;
                $totalMcGloves = 0;
                $planMcGloves = 0;
                $totalMcAll = 0;
                $planMcAll = 0;
                $totalPlanMcJrm = [];
                $outputDzSocks = 0;
                $outputDzGloves = 0;
                $totalOutputDz = 0;
                $row = 4;
                foreach ($kebutuhanMesin[$i] as $area => $jarums) {
                    $planMcArea = 0;
                    $outputDz = 0;
                    foreach ($jarum as $jrm) {
                        $planMcJrm = $jarums[$jrm['jarum']] ?? 0; // Planning MC untuk jenis jarum tertentu
                        $planMcArea += $planMcJrm;
                        $dz = $planMcJrm * 14; // Menjumlahkan Planning MC per jarum untuk total area
                        $outputDz += $dz; // Menjumlahkan Planning MC per jarum untuk total area
                        // Menambahkan nilai ke total per jarum
                        if (!isset($totalPlanMcJrm[$jrm['jarum']])) {
                            $totalPlanMcJrm[$jrm['jarum']] = 0;
                        }
                        $totalPlanMcJrm[$jrm['jarum']] += $planMcJrm;
                    }
                    // Memastikan totalMc sudah diisi sebelumnya
                    if (isset($totalArea[$area]['Total'])) {
                        // Menambahkan total MC per area ke variabel total seluruh area
                        if ($area != 'KK8J') {
                            $totalMcSocks += $totalArea[$area]['Total'];  // Perbaikan ini memastikan totalMcSocks dihitung dengan benar
                            $planMcSocks += $planMcArea;
                            $outputDzSocks += $outputDz;
                        } else if ($area == 'KK8J') {
                            $totalMcGloves = $totalArea[$area]['Total'];
                            $planMcGloves = $planMcArea;
                            $outputDzGloves += $outputDz;
                        }
                    }
                    $totalMcAll = $totalMcSocks + $totalMcGloves;
                    $planMcAll = $planMcSocks + $planMcGloves;
                    $totalOutputDz = $outputDzSocks + $outputDzGloves;

                    $sheet->setCellValue("A$row", $area);
                    $sheet->setCellValue("B$row", $totalArea[$area]['Total'] ?? 0);
                    $sheet->setCellValue("C$row", array_sum($jarums));
                    $sheet->getStyle("A$row")->applyFromArray($styleBody);
                    $sheet->getStyle("B$row")->applyFromArray($styleBody);
                    $sheet->getStyle("C$row")->applyFromArray($styleBody);

                    $col = 'D';
                    foreach ($jarum as $jrm) {
                        $sheet->setCellValue($col . $row, $jarums[$jrm['jarum']] ?? 0);
                        $sheet->getStyle($col . $row)->applyFromArray($styleBody);
                        $col++;
                    }

                    $sheet->setCellValue($col . $row, array_sum($jarums) * 14); // Output (dz)
                    $sheet->getStyle($col . $row)->applyFromArray($styleBody);
                    $row++;
                }

                // Add total rows
                $sheet->setCellValue("A$row", 'Total MC Socks');
                $sheet->setCellValue("B$row", $totalMcSocks);
                $sheet->setCellValue("C$row", $planMcSocks);
                $sheet->getStyle("A$row")->applyFromArray($styleTotal);
                $sheet->getStyle("B$row")->applyFromArray($styleTotal);
                $sheet->getStyle("C$row")->applyFromArray($styleTotal);
                $col = 'D';
                foreach ($jarum as $jrm) {
                    $sheet->setCellValue($col . $row, $totalPlanMcJrm[$jrm['jarum']] ?? 0);
                    $sheet->getStyle($col . $row)->applyFromArray($styleTotal);

                    $col++;
                }
                $sheet->setCellValue($col . $row, $outputDzSocks);
                $sheet->getStyle("A$row:{$col}{$row}")->applyFromArray($styleTotal);
                $row++;

                $sheet->setCellValue("A$row", '% Sock');
                $sheet->setCellValue("C$row", number_format(($totalMcSocks / $planMcSocks) * 100, 2) . '%');
                $sheet->getStyle("A$row:AE$row")->applyFromArray($styleTotal);
                $row++;

                $sheet->setCellValue("A$row", 'Total MC Gloves');
                $sheet->setCellValue("B$row", $totalMcGloves);
                $sheet->setCellValue("C$row", $planMcGloves);
                $sheet->getStyle("A$row:AE$row")->applyFromArray($styleTotal);
                $col = 'D';
                foreach ($jarum as $jrm) {
                    $col++;
                }
                $sheet->setCellValue($col . $row, $outputDzGloves);
                $sheet->getStyle("A$row:{$col}{$row}")->applyFromArray($styleTotal);
                $row++;

                $sheet->setCellValue("A$row", 'Total');
                $sheet->setCellValue("B$row", $totalMcAll);
                $sheet->setCellValue("C$row", $planMcAll);
                $sheet->getStyle("A$row:AE$row")->applyFromArray($styleTotal);
                $col = 'D';
                foreach ($jarum as $jrm) {
                    $col++;
                }
                $sheet->setCellValue($col . $row, $totalOutputDz);
                $sheet->getStyle("A$row:{$col}{$row}")->applyFromArray($styleTotal);
                $row++;

                $sheet->setCellValue("A$row", '% Total MC');
                $sheet->setCellValue("C$row", number_format(($totalMcAll / $planMcAll) * 100, 2) . '%');
                $sheet->getStyle("A$row:AE$row")->applyFromArray($styleTotal);
            }
        }

        // Set sheet pertama sebagai active sheet
        $spreadsheet->setActiveSheetIndex(0);

        // Export file ke Excel
        $writer = new Xlsx($spreadsheet);
        $filename = 'Rekomendasi Planning Jalan MC ' . $bulanIni . '.xlsx';

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    public function saveMonthlyMc()
    {
        $global = [
            'judul' => $this->request->getPost('judul'),
            'total_mc' => $this->request->getPost('totalMc'),
            'planning_mc' => $this->request->getPost('totalPlanning'),
            'total_output' => $this->request->getPost('OutputTotal'),
            'mc_socks' => $this->request->getPost('mcSocks'),
            'plan_mc_socks' => $this->request->getPost('planMcSocks'),
            'mc_gloves' => $this->request->getPost('mcGloves'),
            'plan_mc_gloves' => $this->request->getPost('planMcGloves'),
        ];
        $jarum          = $this->request->getPost('jarum');         // array
        $kebutuhanMesin = $this->request->getPost('kebutuhanMesin');
        $totalMesin     = $this->request->getPost('totalMesin');
        $planningMc     = $this->request->getPost('planningMc');
        $outputDz     = $this->request->getPost('outputDz');
        $areas = $this->request->getPost('area');
        dd($kebutuhanMesin);


        $chek = $this->globalModel->cekExist($global);
        if (!$chek) {
            $this->globalModel->insert($global);
            $getId = $this->globalModel->cekExist($global);
            $idGlobal = $getId['id_monthly_mc'];
            $areaMcInsert = [];

            foreach ($areas as $key => $area) {
                $areaMcInsert = [
                    'id_monthly_mc' => $idGlobal,
                    'area' => $area,
                    'total_mc' => $totalMesin[$key],
                    'planning_mc' => $planningMc[$key],
                    'output' => $outputDz[$key]
                ];
            }
        } else {
            $idGlobal = $chek['id_monthly_mc'];
            $this->globalModel->update($idGlobal, $global);
            $getId = $this->globalModel->cekExist($global);
            $idGlobal = $getId['id_monthly_mc'];
            $areaMcInsert = [];

            foreach ($areas as $key => $area) {
                $areaMcInsert = [
                    'id_monthly_mc' => $idGlobal,
                    'area' => $area,
                    'total_mc' => $totalMesin[$key],
                    'planning_mc' => $planningMc[$key],
                    'output' => $outputDz[$key]
                ];
                $cekDataArea = $this->areaMcModel->existData($areaMcInsert);
                if (!$cekDataArea) {
                    $this->areaMcModel->insert($areaMcInsert);
                    $getIdArea = $this->areaMcModel->existData($areaMcInsert);
                    $idArea = $getIdArea['id_area_machine'];
                    $childData = [];
                } else {
                    $idArea = $cekDataArea['id_area_machine'];
                }
            }
        }
    }
}
