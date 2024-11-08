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
use App\Services\orderServices;
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
    protected $orderService;

    public function __construct()
    {
        $this->orderService = new orderServices();
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
        $this->orderServices = new orderServices();
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

    public function excelPlanningJlMc($judul)
    {
        $role = session()->get('role');

        // Log the input for debugging
        log_message('info', 'Received bulan value: ' . $judul);

        // Parse the $bulan string to a DateTime object
        $date = \DateTime::createFromFormat('F-Y', $judul);
        $awalBulan = $date->format('Y-m-01');
        if (!$date) {
            throw new \Exception("Invalid date format: '{$judul}'. Please use 'F-Y' format.");
        }

        $bulanIni = $date->format('F-Y');
        $startDate = new \DateTime($date->format('Y-m-01')); // First day of the given month

        $idJudul = $this->globalModel->getData($judul);
        $id = $idJudul['id_monthly_mc'];
        $areas = $this->areaMcModel->getData($id);
        // $jarums = $this->detailAreaMc->getData($areas);

        $monthlyData = [];
        if (!isset($monthlyData)) {
            $monthlyData = [
                'areas' => [],
                'monthlySummary' => [
                    'totalMc' => 0,
                    'PlanningMc' => 0,
                    'outputDz' => 0,
                ],
            ];
        }

        $jarums = $this->jarumModel->getJarum();

        foreach ($areas as $ar) {
            $area = $ar['area'] ?? 0;
            $totalMc = $ar['total_mc'] ?? 0;
            $planningMc = $ar['planning_mc'] ?? 0;
            $outputDz = isset($ar['output']) ? $ar['output'] : 0;

            // Siapkan struktur untuk satu area
            $areaData = [
                'total_mc' => $totalMc,
                'planning_mc' => $planningMc,
                'outputDz' => $outputDz,
                'jarums' => [] // Menampung data jarum di dalam area
            ];

            $details = $this->detailAreaMc->getData($ar['id_area_machine']);

            foreach ($details as $data) {
                $jarum = $data['jarum'] ?? null;
                $planning = $data['planning_mc'] ?? 0;

                // Menyimpan data jarum dalam area
                $areaData['jarums'][] = [
                    'jarum' => $jarum,
                    'planning_mc' => $planning
                ];
            }

            $monthlyData['areas'][] = [
                'area' => $area,
                'data' => $areaData,
            ];
        }
        // dd($monthlyData);

        $statusOrder = $this->orderService->statusOrder($judul);


        // Generate Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // border
        $styleHeader = [
            'font' => [
                'bold' => true, // Tebalkan teks
                'color' => ['argb' => 'FFFFFFFF']
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
                'horizontal' => Alignment::HORIZONTAL_CENTER, // Alignment rata tengah
            ],
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THIN, // Gaya garis tipis
                    'color' => ['argb' => 'FF000000'],    // Warna garis hitam
                ],
            ],
        ];

        // Set lebar kolom secara manual
        $sheet->getColumnDimension('A')->setWidth(16); // Lebar kolom A diatur menjadi 20
        $sheet->getColumnDimension('B')->setWidth(12); // Lebar kolom B diatur menjadi 25
        $sheet->getColumnDimension('C')->setWidth(12); // Lebar kolom C diatur menjadi 30
        $sheet->getColumnDimension('D')->setWidth(12); // Lebar kolom C diatur menjadi 30
        $sheet->getColumnDimension('E')->setWidth(12); // Lebar kolom C diatur menjadi 30
        $sheet->getColumnDimension('F')->setWidth(12); // Lebar kolom C diatur menjadi 30
        $sheet->getColumnDimension('G')->setWidth(12); // Lebar kolom C diatur menjadi 30
        $sheet->getColumnDimension('H')->setWidth(12); // Lebar kolom C diatur menjadi 30
        $sheet->getColumnDimension('I')->setWidth(12); // Lebar kolom C diatur menjadi 30
        $col = 'D';
        foreach ($jarums as $jrm) {
            $col++;
        }
        $sheet->setCellValue('A1', 'Planning Jalan MC ' . $bulanIni);
        $sheet->mergeCells("A1:{$col}1");
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
        foreach ($jarums as $jrm) {
            $sheet->setCellValue($col . '3', $jrm);
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
        $totalPlanMcJrmSocks = [];
        $totalPlanMcJrmGloves = [];
        $outputDzSocks = 0;
        $outputDzGloves = 0;
        $totalOutputDz = 0;
        $row = 4;
        foreach ($monthlyData['areas'] as $areaData) {
            $area = $areaData['area'];
            $data = $areaData['data'];

            if ($area != 'KK8J') {
                $totalMcSocks += $data['total_mc'];
                $planMcSocks += $data['planning_mc'];
                $planMcSocks == 0 ?? '';
                $outputDzSocks += $data['outputDz'];
            } else if ($area == 'KK8J') {
                $totalMcGloves = $data['total_mc'];
                $planMcGloves = $data['planning_mc'];
                $planMcGloves
                    == 0 ?? '';
                $outputDzGloves += $data['outputDz'];
            }

            $totalMcAll = $totalMcSocks + $totalMcGloves;
            $planMcAll = $planMcSocks + $planMcGloves;
            $totalOutputDz = $outputDzSocks + $outputDzGloves;

            //Mengisi sheet excel
            $sheet->setCellValue("A$row", $area);
            $sheet->setCellValue("B$row", $data['total_mc'] ?? ' ');
            $sheet->setCellValue("C$row", $data['planning_mc'] ?? ' ');
            $sheet->getStyle("A$row")->applyFromArray($styleBody);
            $sheet->getStyle("B$row")->applyFromArray($styleBody);
            $sheet->getStyle("C$row")->applyFromArray($styleBody);

            //Menambahkan planning mc perarea perjarum
            $col = 'D';
            foreach ($jarums as $jarum) {
                $planningMc = 0;

                if (!isset($totalPlanMcJrmSocks[$jarum])) {
                    $totalPlanMcJrmSocks[$jarum] = 0;
                }
                if (!isset($totalPlanMcJrmGloves[$jarum])) {
                    $totalPlanMcJrmGloves[$jarum] = 0;
                }

                // Loop melalui detail jarum dari area untuk mencari kecocokan
                foreach ($data['jarums'] as $jrm) {
                    if ($jrm['jarum'] == $jarum) {
                        $planningMc = $jrm['planning_mc'] ?? 0; // Dapatkan nilai planning_mc
                        break; // Keluar dari loop karena sudah ditemukan jarum yang cocok
                    }
                }
                if ($area != 'KK8J') {
                    $totalPlanMcJrmSocks[$jarum] += $planningMc;
                    $totalPlanMcJrmSocks[$jarum] == 0 ?? '';
                } else {
                    $totalPlanMcJrmGloves[$jarum] += $planningMc;
                    $totalPlanMcJrmGloves[$jarum]
                        == 0 ?? '';
                }
                if ($planningMc == 0) {
                    $planningMc = ' ';
                }
                $sheet->setCellValue($col . $row, $planningMc);
                $sheet->getStyle($col . $row)->applyFromArray($styleBody);
                $col++;
            }

            $sheet->setCellValue($col . $row, $data['outputDz']); // Output (dz)
            $sheet->getStyle($col . $row)->applyFromArray($styleBody);

            $row++;
        }

        // Add total socs rows
        $sheet->setCellValue("A$row", 'Total MC Socks');
        $sheet->setCellValue("B$row", $totalMcSocks);
        $sheet->setCellValue("C$row", $planMcSocks);
        $sheet->getStyle("A$row")->applyFromArray($styleTotal);
        $sheet->getStyle("B$row")->applyFromArray($styleTotal);
        $sheet->getStyle("C$row")->applyFromArray($styleTotal);

        //Total perjarum
        $col = 'D';
        foreach ($jarums as $jrm) {
            $sheet->setCellValue($col . $row, $totalPlanMcJrmSocks[$jrm] ?? ' ');
            $sheet->getStyle("{$col}{$row}:{$col}{$row}")->applyFromArray($styleTotal);
            $col++;
        }
        $sheet->setCellValue($col . $row, $outputDzSocks);
        $sheet->getStyle("A$row:{$col}{$row}")->applyFromArray($styleTotal);
        $row++;

        //Persentase Total Socks
        $sheet->setCellValue("A$row", '% Sock');
        $sheet->setCellValue("C$row", number_format(($planMcSocks / $totalMcSocks) * 100, 2) . '%');
        $sheet->getStyle("A$row")->applyFromArray($styleTotal);
        $sheet->getStyle("B$row")->applyFromArray($styleTotal);
        $sheet->getStyle("C$row")->applyFromArray($styleTotal);

        $col = 'D';
        foreach ($jarums as $jrm) {
            $sheet->getStyle("{$col}{$row}:{$col}{$row}")->applyFromArray($styleTotal);
            $col++;
        }
        $sheet->getStyle("A$row:{$col}{$row}")->applyFromArray($styleTotal);
        $row++;

        // Add total gloves rows
        $sheet->setCellValue("A$row", 'Total MC Gloves');
        $sheet->setCellValue("B$row", $totalMcGloves);
        $sheet->setCellValue("C$row", $planMcGloves);
        $sheet->getStyle("A$row")->applyFromArray($styleTotal);
        $sheet->getStyle("B$row")->applyFromArray($styleTotal);
        $sheet->getStyle("C$row")->applyFromArray($styleTotal);
        $col = 'D';
        foreach ($jarums as $jrm) {
            $sheet->setCellValue($col . $row, $totalPlanMcJrmGloves[$jrm] ?? ' ');
            $sheet->getStyle("{$col}{$row}:{$col}{$row}")->applyFromArray($styleTotal);
            $col++;
        }
        $sheet->setCellValue($col . $row, $outputDzGloves);
        $sheet->getStyle("A$row:{$col}{$row}")->applyFromArray($styleTotal);
        $row++;

        //Persentase Total Gloves
        $sheet->setCellValue("A$row", '% Gloves');
        $sheet->setCellValue("C$row", number_format(($planMcGloves / $totalMcGloves) * 100, 2) . '%');
        $sheet->getStyle("A$row")->applyFromArray($styleTotal);
        $sheet->getStyle("B$row")->applyFromArray($styleTotal);
        $sheet->getStyle("C$row")->applyFromArray($styleTotal);

        $col = 'D';
        foreach ($jarums as $jrm) {
            $sheet->getStyle("{$col}{$row}:{$col}{$row}")->applyFromArray($styleTotal);
            $col++;
        }
        $sheet->getStyle("A$row:{$col}{$row}")->applyFromArray($styleTotal);
        $row++;

        // Add total rows
        $sheet->setCellValue("A$row", 'Total');
        $sheet->setCellValue("B$row", $totalMcAll);
        $sheet->setCellValue("C$row", $planMcAll);
        $sheet->getStyle("A$row")->applyFromArray($styleTotal);
        $sheet->getStyle("B$row")->applyFromArray($styleTotal);
        $sheet->getStyle("C$row")->applyFromArray($styleTotal);
        $col = 'D';
        foreach ($jarums as $jrm) {
            $sheet->setCellValue($col . $row, $totalPlanMcJrmSocks[$jrm] + $totalPlanMcJrmGloves[$jrm] ?? ' ');
            $sheet->getStyle("{$col}{$row}:{$col}{$row}")->applyFromArray($styleTotal);
            $col++;
        }

        $sheet->setCellValue($col . $row, $totalOutputDz);
        $sheet->getStyle("A$row:{$col}{$row}")->applyFromArray($styleTotal);
        $row++;

        $sheet->setCellValue("A$row", '% Total MC');
        $sheet->setCellValue("C$row", number_format(($planMcAll / $totalMcAll) * 100, 2) . '%');
        $sheet->getStyle("A$row")->applyFromArray($styleTotal);
        $sheet->getStyle("B$row")->applyFromArray($styleTotal);
        $sheet->getStyle("C$row")->applyFromArray($styleTotal);
        $col = 'D';
        foreach ($jarums as $jrm) {
            $sheet->getStyle("{$col}{$row}:{$col}{$row}")->applyFromArray($styleTotal);
            $col++;
        }
        $sheet->getStyle("A$row:{$col}{$row}")->applyFromArray($styleTotal);


        //STATUS ORDER
        // Set starting row after the first loop
        $endrow = $row;
        // Inisialisasi baris awal dan kolom awal
        $row2 = $endrow + 3; // Mulai di baris 2 setelah $endrow
        $startColumn = 'B'; // Mulai di kolom B untuk bulan pertama

        // Function untuk mendapatkan kolom selanjutnya berdasarkan huruf
        function getNextColumn($column, $step = 1)
        {
            $nextColumn = '';
            $columnLength = strlen($column);

            for ($i = $columnLength - 1; $i >= 0; $i--) {
                $currentChar = ord($column[$i]);
                $newChar = chr($currentChar + $step);

                if ($newChar > 'Z') {
                    $newChar = 'A';
                    $step = 1;
                } else {
                    $step = 0;
                }

                $nextColumn = $newChar . $nextColumn;
            }

            if ($step > 0) {
                $nextColumn = 'A' . $nextColumn;
            }

            return $nextColumn;
        }

        // Set header untuk Status Order dengan rowspan 2 baris
        $sheet->setCellValue("A$row2", 'STATUS ORDER');
        $sheet->mergeCells("A$row2:A" . ($row2 + 1)); // Merge 2 baris dari $row2 ke $row2+1
        $sheet->getStyle("A$row2:A" . ($row2 + 1))->applyFromArray($styleHeader); // Apply style

        // Set header untuk KAOS KAKI dan SARUNG TANGAN di baris yang sesuai
        $sheet->setCellValue("A" . ($row2 + 2), 'KAOS KAKI');
        $sheet->setCellValue("A" . ($row2 + 3), 'SARUNG TANGAN');
        $sheet->getStyle("A" . ($row2 + 2))->applyFromArray($styleBody); // Apply style
        $sheet->getStyle("A" . ($row2 + 3))->applyFromArray($styleBody); // Apply style


        // Looping untuk setiap bulan
        $currentColumn = $startColumn;
        foreach ($statusOrder as $month => $data) {
            if (is_array($data)) {
                // Set header bulan
                $sheet->setCellValue($currentColumn . $row2, $month);
                $sheet->mergeCells($currentColumn . $row2 . ':' . getNextColumn($currentColumn) . $row2); // Merge 2 kolom untuk header bulan
                $sheet->getStyle($currentColumn . $row2 . ':' . getNextColumn($currentColumn) . $row2)->applyFromArray($styleHeader); // Apply style


                // Set header untuk Qty dan Sisa Order
                $sheet->setCellValue($currentColumn . ($row2 + 1), 'QTY ORDER');
                $sheet->setCellValue(getNextColumn($currentColumn) . ($row2 + 1), 'SISA ORDER');
                $sheet->getStyle($currentColumn . ($row2 + 1))->applyFromArray($styleHeader); // Apply style
                $sheet->getStyle(getNextColumn($currentColumn) . ($row2 + 1))->applyFromArray($styleHeader); // Apply style


                // Set isi data untuk KAOSKAKI
                $sheet->setCellValue($currentColumn . ($row2 + 2), number_format($data['socks']['qty']));
                $sheet->setCellValue(getNextColumn($currentColumn) . ($row2 + 2), number_format($data['socks']['sisa']));
                $sheet->getStyle($currentColumn . ($row2 + 2))->applyFromArray($styleBody); // Apply style
                $sheet->getStyle(getNextColumn($currentColumn) . ($row2 + 2))->applyFromArray($styleBody); // Apply style

                // Set isi data untuk SARUNG TANGAN
                $sheet->setCellValue($currentColumn . ($row2 + 3), number_format($data['gloves']['qty']));
                $sheet->setCellValue(getNextColumn($currentColumn) . ($row2 + 3), number_format($data['gloves']['sisa']));
                $sheet->getStyle($currentColumn . ($row2 + 3))->applyFromArray($styleBody); // Apply style
                $sheet->getStyle(getNextColumn($currentColumn) . ($row2 + 3))->applyFromArray($styleBody); // Apply style

                // Pindahkan kolom untuk bulan berikutnya (2 kolom)
                $currentColumn = getNextColumn($currentColumn, 2);
            }
        }

        // Setelah looping bulan, tambahkan kolom total
        $sheet->setCellValue($currentColumn . $row2, 'TOTAL');
        $sheet->mergeCells($currentColumn . $row2 . ':' . getNextColumn($currentColumn) . $row2); // Merge 2 kolom untuk header Total
        $sheet->getStyle($currentColumn . $row2 . ':' . getNextColumn($currentColumn) . $row2)->applyFromArray($styleHeader); // Apply style

        // Set header untuk Qty dan Sisa di kolom Total
        $sheet->setCellValue($currentColumn . ($row2 + 1), 'QTY ORDER');
        $sheet->setCellValue(getNextColumn($currentColumn) . ($row2 + 1), 'SISA ORDER');
        $sheet->getStyle($currentColumn . ($row2 + 1))->applyFromArray($styleHeader); // Apply style
        $sheet->getStyle(getNextColumn($currentColumn) . ($row2 + 1))->applyFromArray($styleHeader); // Apply style

        // Isi data total untuk KAOSKAKI di kolom Total
        $sheet->setCellValue($currentColumn . ($row2 + 2), number_format($statusOrder['totalOrderSocks']));
        $sheet->setCellValue(getNextColumn($currentColumn) . ($row2 + 2), number_format($statusOrder['totalSisaSocks']));
        $sheet->getStyle($currentColumn . ($row2 + 2))->applyFromArray($styleBody); // Apply style
        $sheet->getStyle(getNextColumn($currentColumn) . ($row2 + 2))->applyFromArray($styleBody); // Apply style

        // Isi data total untuk SARUNG TANGAN di kolom Total
        $sheet->setCellValue($currentColumn . ($row2 + 3), number_format($statusOrder['totalOrderGloves']));
        $sheet->setCellValue(getNextColumn($currentColumn) . ($row2 + 3), number_format($statusOrder['totalSisaGloves']));
        $sheet->getStyle($currentColumn . ($row2 + 3))->applyFromArray($styleBody); // Apply style
        $sheet->getStyle(getNextColumn($currentColumn) . ($row2 + 3))->applyFromArray($styleBody); // Apply style

        // Isi Grand Total di baris terakhir
        $sheet->setCellValue($currentColumn . ($row2 + 4), number_format($statusOrder['grandTotalOrder']));
        $sheet->setCellValue(getNextColumn($currentColumn) . ($row2 + 4), number_format($statusOrder['grandTotalSisa']));
        $sheet->getStyle($currentColumn . ($row2 + 4))->applyFromArray($styleBody); // Apply style
        $sheet->getStyle(getNextColumn($currentColumn) . ($row2 + 4))->applyFromArray($styleBody); // Apply style

        // Set total di akhir
        $sheet->setCellValue("A" . ($row2 + 4), 'TOTAL');
        $sheet->getStyle("A" . ($row2 + 4))->applyFromArray($styleTotal); // Apply style
        $currentColumn = $startColumn;
        foreach ($statusOrder as $month => $data) {
            if (is_array($data)) {
                $sheet->setCellValue($currentColumn . ($row2 + 4), number_format($data['qty']));
                $sheet->setCellValue(getNextColumn($currentColumn) . ($row2 + 4), number_format($data['sisa']));
                $sheet->getStyle($currentColumn . ($row2 + 4))->applyFromArray($styleTotal); // Apply style
                $sheet->getStyle(getNextColumn($currentColumn) . ($row2 + 4))->applyFromArray($styleTotal); // Apply style

                // Pindahkan kolom untuk total berikutnya
                $currentColumn = getNextColumn($currentColumn, 2);
            }
        }
        $sheet->setCellValue($currentColumn . ($row2 + 4), number_format($statusOrder['grandTotalOrder']));
        $sheet->setCellValue(getNextColumn($currentColumn) . ($row2 + 4), number_format($statusOrder['grandTotalSisa']));
        $sheet->getStyle($currentColumn . ($row2 + 4))->applyFromArray($styleTotal); // Apply style
        $sheet->getStyle(getNextColumn($currentColumn) . ($row2 + 4))->applyFromArray($styleTotal); // Apply style

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

    // public function excelPlanningJlMc($bulan)
    // {
    //     $role = session()->get('role');
    //     $date = DateTime::createFromFormat('F-Y', $bulan);
    //     $bulanIni = $date->format('F-Y');
    //     $awalBulan = $date->format('Y-m-01');
    //     $statusOrder = $this->orderService->statusOrder($bulan);
    //     $filteredArea = $this->jarumModel->getArea();
    //     $area = array_filter($filteredArea, function ($item) {
    //         return strpos($item, 'Gedung') === false;
    //     });
    //     $area = array_values($area);
    //     $monthlyData = [];
    //     foreach ($area as $ar) {
    //         $mesin = $this->jarumModel->areaMc($ar);
    //         $totalMesin = 0;
    //         $planningMc = 0;
    //         $outputDz = 0;
    //         foreach ($mesin as $jarum) {
    //             $sisaOrder = $this->ApsPerstyleModel->ambilSisaOrder($ar, $awalBulan, $jarum['jarum']);
    //             $monthlyData[$ar][$jarum['jarum']]['kebutuhanMesin'] = $sisaOrder['totalKebMesin'];
    //             $monthlyData[$ar][$jarum['jarum']]['output'] = $sisaOrder['outputDz'];
    //             $monthlyData[$ar][$jarum['jarum']]['jr'] = $jarum['jarum'];
    //             $totalMesin += $jarum['total'];
    //             $planningMc += $sisaOrder['totalKebMesin'];
    //             $outputDz += $sisaOrder['outputDz'];
    //         }
    //         $monthlyData[$ar]['totalMesin'] = $totalMesin;
    //         $monthlyData[$ar]['planningMc'] = $planningMc;
    //         $monthlyData[$ar]['outputDz'] = $outputDz;
    //     }
    //     $totalAllMesin = 0;

    //     $totalOutput = 0;
    //     $totalMcPlanning = 0;

    //     foreach ($monthlyData as $data) {
    //         $totalAllMesin += $data['totalMesin'];
    //         $totalOutput += $data['outputDz'];
    //         $totalMcPlanning += $data['planningMc'];
    //     }
    //     $totalKebGloves = 0; // Initialize outside the loop
    //     foreach ($monthlyData['KK8J'] as $data) {
    //         if (is_array($data) && isset($data['kebutuhanMesin'])) {
    //             $totalKebGloves += $data['kebutuhanMesin'];
    //         }
    //     }

    //     $totalKebSock = $totalMcPlanning - $totalKebGloves;
    //     $totalMcSocks = $this->jarumModel->totalMcSock();
    //     $totalMcSocks = intval($totalMcSocks['total']);
    //     $totalMcGloves = $totalAllMesin - $totalMcSocks;

    //     $persenSocks = round(($totalKebSock / $totalMcSocks) * 100);
    //     $persenGloves = round(($totalKebGloves / $totalMcGloves) * 100);
    //     $persenTotal = round(($totalMcPlanning / $totalAllMesin) * 100);
    //     $summary = [
    //         'totalMc' => $totalAllMesin,
    //         'OutputTotal' => $totalOutput,
    //         'totalPlanning' => $totalMcPlanning,
    //         'totalPersen' => $persenTotal,

    //         'mcSocks' => $totalMcSocks,
    //         'planMcSocks' => $totalKebSock,
    //         'persenSocks' => $persenSocks,

    //         'mcGloves' => $totalMcGloves,
    //         'planMcGloves' => $totalKebGloves,
    //         'persenGloves' => $persenGloves
    //     ];

    //     // Generate Excel
    //     $spreadsheet = new Spreadsheet();
    //     $sheet = $spreadsheet->getActiveSheet();
    //     $sheet->setTitle("Planning Mesin");

    //     $styleGlobal = [
    //         'font' => [
    //             'bold' => true, // Tebalkan teks
    //         ],
    //         'alignment' => [
    //             'horizontal' => Alignment::HORIZONTAL_LEFT, // Alignment rata tengah
    //         ],
    //     ];

    //     $styleGlobalBody = [
    //         'alignment' => [
    //             'horizontal' => Alignment::HORIZONTAL_LEFT, // Alignment rata tengah
    //         ],
    //     ];

    //     // border
    //     $styleHeader = [
    //         'font' => [
    //             'bold' => true, // Tebalkan teks
    //         ],
    //         'alignment' => [
    //             'horizontal' => Alignment::HORIZONTAL_CENTER, // Alignment rata tengah
    //         ],
    //         'borders' => [
    //             'outline' => [
    //                 'borderStyle' => Border::BORDER_THIN, // Gaya garis tipis
    //                 'color' => ['argb' => 'FF000000'],    // Warna garis hitam
    //             ],
    //         ],
    //         'fill' => [
    //             'fillType' => Fill::FILL_SOLID, // Jenis pengisian solid
    //             'startColor' => ['argb' => 'FF67748e'], // Warna latar belakang biru tua (HEX)
    //         ],
    //     ];
    //     $styleBody = [
    //         'alignment' => [
    //             'horizontal' => Alignment::HORIZONTAL_CENTER, // Alignment rata tengah
    //         ],
    //         'borders' => [
    //             'outline' => [
    //                 'borderStyle' => Border::BORDER_THIN, // Gaya garis tipis
    //                 'color' => ['argb' => 'FF000000'],    // Warna garis hitam
    //             ],
    //         ],
    //     ];
    //     $styleTotal = [
    //         'font' => [
    //             'bold' => true, // Tebalkan teks
    //         ],
    //         'alignment' => [
    //             'horizontal' => Alignment::HORIZONTAL_RIGHT, // Alignment rata tengah
    //         ],
    //         'borders' => [
    //             'outline' => [
    //                 'borderStyle' => Border::BORDER_THIN, // Gaya garis tipis
    //                 'color' => ['argb' => 'FF000000'],    // Warna garis hitam
    //             ],
    //         ],
    //     ];

    //     // Set lebar kolom secara manual
    //     $sheet->getColumnDimension('A')->setWidth(25); // Lebar kolom A diatur menjadi 20
    //     $sheet->getColumnDimension('B')->setWidth(20); // Lebar kolom B diatur menjadi 25
    //     $sheet->getColumnDimension('C')->setWidth(20); // Lebar kolom C diatur menjadi 30
    //     $sheet->getColumnDimension('D')->setWidth(20); // Lebar kolom C diatur menjadi 30
    //     $sheet->getColumnDimension('E')->setWidth(20); // Lebar kolom C diatur menjadi 30
    //     $sheet->getColumnDimension('F')->setWidth(20); // Lebar kolom C diatur menjadi 30
    //     $sheet->getColumnDimension('G')->setWidth(20); // Lebar kolom C diatur menjadi 30
    //     $sheet->getColumnDimension('H')->setWidth(20); // Lebar kolom C diatur menjadi 30
    //     $sheet->getColumnDimension('I')->setWidth(20); // Lebar kolom C diatur menjadi 30

    //     // Set header untuk global data
    //     $sheet->setCellValue('A1', 'Planning Jalan Mesin ' . $bulan);
    //     $sheet->mergeCells('A1:C1');
    //     $sheet->getStyle('A1')->getFont()->setBold(true);
    //     $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    //     // Global
    //     $sheet->setCellValue('A3', 'Global');
    //     $sheet->setCellValue('A4', 'Total Mesin : ' . $summary['totalMc']);
    //     $sheet->setCellValue('A5', 'Total Planning : ' . $summary['totalPlanning']);
    //     $sheet->setCellValue('A6', 'Persentase :' . $summary['totalPersen'] . '%');
    //     $sheet->setCellValue('A7', 'Total Output : ' . $summary['OutputTotal']);
    //     $sheet->getStyle('A3')->applyFromArray($styleGlobal);
    //     $sheet->getStyle('A4')->applyFromArray($styleGlobalBody);
    //     $sheet->getStyle('A5')->applyFromArray($styleGlobalBody);
    //     $sheet->getStyle('A6')->applyFromArray($styleGlobalBody);
    //     $sheet->getStyle('A7')->applyFromArray($styleGlobalBody);

    //     $sheet->setCellValue('B3', 'Socks');
    //     $sheet->setCellValue('B4', 'Total Mesin : ' . $summary['mcSocks']);
    //     $sheet->setCellValue('B5', 'Total Planning : ' . $summary['planMcSocks']);
    //     $sheet->setCellValue('B6', 'Persentase : ' . $summary['persenSocks'] . '%');
    //     $sheet->getStyle('B3')->applyFromArray($styleGlobal);
    //     $sheet->getStyle('B4')->applyFromArray($styleGlobalBody);
    //     $sheet->getStyle('B5')->applyFromArray($styleGlobalBody);
    //     $sheet->getStyle('B6')->applyFromArray($styleGlobalBody);


    //     $sheet->setCellValue('C3', 'Gloves');
    //     $sheet->setCellValue('C4', 'Total Mesin : ' . $summary['mcGloves']);
    //     $sheet->setCellValue('C5', 'Total Planning : ' . $summary['planMcGloves']);
    //     $sheet->setCellValue('C6', 'Persentase : ' . $summary['persenGloves'] . '%');
    //     $sheet->getStyle('C3')->applyFromArray($styleGlobal);
    //     $sheet->getStyle('C4')->applyFromArray($styleGlobalBody);
    //     $sheet->getStyle('C5')->applyFromArray($styleGlobalBody);
    //     $sheet->getStyle('C6')->applyFromArray($styleGlobalBody);

    //     // Looping untuk menampilkan setiap area dan jarum
    //     $row = 9; // Start row header area

    //     $areaCounter = 0; // Counter untuk menghitung area
    //     foreach ($monthlyData as $area => $jarum) {

    //         // Header per area
    //         $sheet->setCellValue("A$row", $area);
    //         $sheet->getStyle("A$row")->applyFromArray($styleGlobal);
    //         $row++;

    //         // Total mesin, planning, dan output per area
    //         $sheet->setCellValue("A$row", 'Total Mesin: ' . $jarum['totalMesin']);
    //         $sheet->setCellValue("B$row", 'Planning Mesin: ' . $jarum['planningMc']);
    //         $sheet->setCellValue("C$row", 'Output (dz): ' . $jarum['outputDz']);
    //         $sheet->getStyle("A$row:C$row")->applyFromArray($styleGlobalBody);
    //         $row++;

    //         // Set header untuk tabel jarum di bawah setiap area
    //         $sheet->setCellValue("A$row", 'Jarum');
    //         $sheet->setCellValue("B$row", 'Kebutuhan Mesin');
    //         $sheet->mergeCells("B$row:C$row"); // Merge kolom B dan C untuk header
    //         $sheet->getStyle("A$row:C$row")->applyFromArray($styleHeader);
    //         $row++;

    //         // Looping data jarum dalam setiap area
    //         foreach ($jarum as $jr) {
    //             if (is_array($jr)) { // Pastikan yang di-loop hanya array jarum
    //                 $sheet->setCellValue("A$row", $jr['jr']);
    //                 $sheet->setCellValue("B$row", $jr['kebutuhanMesin']);
    //                 $sheet->mergeCells("B$row:C$row"); // Merge kolom B dan C untuk data kebutuhan mesin
    //                 $sheet->getStyle("A$row:C$row")->applyFromArray($styleBody);
    //                 $row++;
    //             }
    //         }

    //         // Spacer antar area: tambahkan beberapa baris kosong
    //         $row += 1; // Tambah 1 baris kosong
    //     }

    //     // Set starting row after the first loop
    //     $endrow = $row;
    //     // Inisialisasi baris awal dan kolom awal
    //     $row2 = $endrow + 1; // Mulai di baris 2 setelah $endrow
    //     $startColumn = 'B'; // Mulai di kolom B untuk bulan pertama

    //     // Function untuk mendapatkan kolom selanjutnya berdasarkan huruf
    //     function getNextColumn($column, $step = 1)
    //     {
    //         $nextColumn = '';
    //         $columnLength = strlen($column);

    //         for ($i = $columnLength - 1; $i >= 0; $i--) {
    //             $currentChar = ord($column[$i]);
    //             $newChar = chr($currentChar + $step);

    //             if ($newChar > 'Z') {
    //                 $newChar = 'A';
    //                 $step = 1;
    //             } else {
    //                 $step = 0;
    //             }

    //             $nextColumn = $newChar . $nextColumn;
    //         }

    //         if ($step > 0) {
    //             $nextColumn = 'A' . $nextColumn;
    //         }

    //         return $nextColumn;
    //     }

    //     // Set header untuk Status Order dengan rowspan 2 baris
    //     $sheet->setCellValue("A$row2", 'STATUS ORDER');
    //     $sheet->mergeCells("A$row2:A" . ($row2 + 1)); // Merge 2 baris dari $row2 ke $row2+1
    //     $sheet->getStyle("A$row2:A" . ($row2 + 1))->applyFromArray($styleHeader); // Apply style

    //     // Set header untuk KAOS KAKI dan SARUNG TANGAN di baris yang sesuai
    //     $sheet->setCellValue("A" . ($row2 + 2), 'KAOS KAKI');
    //     $sheet->setCellValue("A" . ($row2 + 3), 'SARUNG TANGAN');
    //     $sheet->getStyle("A" . ($row2 + 2))->applyFromArray($styleBody); // Apply style
    //     $sheet->getStyle("A" . ($row2 + 3))->applyFromArray($styleBody); // Apply style


    //     // Looping untuk setiap bulan
    //     $currentColumn = $startColumn;
    //     foreach ($statusOrder as $month => $data) {
    //         if (is_array($data)) {
    //             // Set header bulan
    //             $sheet->setCellValue($currentColumn . $row2, $month);
    //             $sheet->mergeCells($currentColumn . $row2 . ':' . getNextColumn($currentColumn) . $row2); // Merge 2 kolom untuk header bulan
    //             $sheet->getStyle($currentColumn . $row2 . ':' . getNextColumn($currentColumn) . $row2)->applyFromArray($styleHeader); // Apply style


    //             // Set header untuk Qty dan Sisa Order
    //             $sheet->setCellValue($currentColumn . ($row2 + 1), 'QTY ORDER');
    //             $sheet->setCellValue(getNextColumn($currentColumn) . ($row2 + 1), 'SISA ORDER');
    //             $sheet->getStyle($currentColumn . ($row2 + 1))->applyFromArray($styleHeader); // Apply style
    //             $sheet->getStyle(getNextColumn($currentColumn) . ($row2 + 1))->applyFromArray($styleHeader); // Apply style


    //             // Set isi data untuk KAOSKAKI
    //             $sheet->setCellValue($currentColumn . ($row2 + 2), number_format($data['socks']['qty']));
    //             $sheet->setCellValue(getNextColumn($currentColumn) . ($row2 + 2), number_format($data['socks']['sisa']));
    //             $sheet->getStyle($currentColumn . ($row2 + 2))->applyFromArray($styleBody); // Apply style
    //             $sheet->getStyle(getNextColumn($currentColumn) . ($row2 + 2))->applyFromArray($styleBody); // Apply style

    //             // Set isi data untuk SARUNG TANGAN
    //             $sheet->setCellValue($currentColumn . ($row2 + 3), number_format($data['gloves']['qty']));
    //             $sheet->setCellValue(getNextColumn($currentColumn) . ($row2 + 3), number_format($data['gloves']['sisa']));
    //             $sheet->getStyle($currentColumn . ($row2 + 3))->applyFromArray($styleBody); // Apply style
    //             $sheet->getStyle(getNextColumn($currentColumn) . ($row2 + 3))->applyFromArray($styleBody); // Apply style

    //             // Pindahkan kolom untuk bulan berikutnya (2 kolom)
    //             $currentColumn = getNextColumn($currentColumn, 2);
    //         }
    //     }

    //     // Setelah looping bulan, tambahkan kolom total
    //     $sheet->setCellValue($currentColumn . $row2, 'TOTAL');
    //     $sheet->mergeCells($currentColumn . $row2 . ':' . getNextColumn($currentColumn) . $row2); // Merge 2 kolom untuk header Total
    //     $sheet->getStyle($currentColumn . $row2 . ':' . getNextColumn($currentColumn) . $row2)->applyFromArray($styleHeader); // Apply style

    //     // Set header untuk Qty dan Sisa di kolom Total
    //     $sheet->setCellValue($currentColumn . ($row2 + 1), 'QTY ORDER');
    //     $sheet->setCellValue(getNextColumn($currentColumn) . ($row2 + 1), 'SISA ORDER');
    //     $sheet->getStyle($currentColumn . ($row2 + 1))->applyFromArray($styleHeader); // Apply style
    //     $sheet->getStyle(getNextColumn($currentColumn) . ($row2 + 1))->applyFromArray($styleHeader); // Apply style

    //     // Isi data total untuk KAOSKAKI di kolom Total
    //     $sheet->setCellValue($currentColumn . ($row2 + 2), number_format($statusOrder['totalOrderSocks']));
    //     $sheet->setCellValue(getNextColumn($currentColumn) . ($row2 + 2), number_format($statusOrder['totalSisaSocks']));
    //     $sheet->getStyle($currentColumn . ($row2 + 2))->applyFromArray($styleBody); // Apply style
    //     $sheet->getStyle(getNextColumn($currentColumn) . ($row2 + 2))->applyFromArray($styleBody); // Apply style

    //     // Isi data total untuk SARUNG TANGAN di kolom Total
    //     $sheet->setCellValue($currentColumn . ($row2 + 3), number_format($statusOrder['totalOrderGloves']));
    //     $sheet->setCellValue(getNextColumn($currentColumn) . ($row2 + 3), number_format($statusOrder['totalSisaGloves']));
    //     $sheet->getStyle($currentColumn . ($row2 + 3))->applyFromArray($styleBody); // Apply style
    //     $sheet->getStyle(getNextColumn($currentColumn) . ($row2 + 3))->applyFromArray($styleBody); // Apply style

    //     // Isi Grand Total di baris terakhir
    //     $sheet->setCellValue($currentColumn . ($row2 + 4), number_format($statusOrder['grandTotalOrder']));
    //     $sheet->setCellValue(getNextColumn($currentColumn) . ($row2 + 4), number_format($statusOrder['grandTotalSisa']));
    //     $sheet->getStyle($currentColumn . ($row2 + 4))->applyFromArray($styleBody); // Apply style
    //     $sheet->getStyle(getNextColumn($currentColumn) . ($row2 + 4))->applyFromArray($styleBody); // Apply style

    //     // Set total di akhir
    //     $sheet->setCellValue("A" . ($row2 + 4), 'TOTAL');
    //     $sheet->getStyle("A" . ($row2 + 4))->applyFromArray($styleBody); // Apply style
    //     $currentColumn = $startColumn;
    //     foreach ($statusOrder as $month => $data) {
    //         if (is_array($data)) {
    //             $sheet->setCellValue($currentColumn . ($row2 + 4), number_format($data['qty']));
    //             $sheet->setCellValue(getNextColumn($currentColumn) . ($row2 + 4), number_format($data['sisa']));
    //             $sheet->getStyle($currentColumn . ($row2 + 4))->applyFromArray($styleBody); // Apply style
    //             $sheet->getStyle(getNextColumn($currentColumn) . ($row2 + 4))->applyFromArray($styleBody); // Apply style

    //             // Pindahkan kolom untuk total berikutnya
    //             $currentColumn = getNextColumn($currentColumn, 2);
    //         }
    //     }
    //     $sheet->setCellValue($currentColumn . ($row2 + 4), number_format($statusOrder['grandTotalOrder']));
    //     $sheet->setCellValue(getNextColumn($currentColumn) . ($row2 + 4), number_format($statusOrder['grandTotalSisa']));
    //     $sheet->getStyle($currentColumn . ($row2 + 4))->applyFromArray($styleBody); // Apply style
    //     $sheet->getStyle(getNextColumn($currentColumn) . ($row2 + 4))->applyFromArray($styleBody); // Apply style


    //     // Set sheet pertama sebagai active sheet
    //     $spreadsheet->setActiveSheetIndex(0);

    //     // Export file ke Excel
    //     $writer = new Xlsx($spreadsheet);
    //     $filename = 'Planning Jalan MC ' . $bulanIni . '.xlsx';

    //     header('Content-Type: application/vnd.ms-excel');
    //     header('Content-Disposition: attachment;filename="' . $filename . '"');
    //     header('Cache-Control: max-age=0');
    //     $writer->save('php://output');
    //     exit;
    // }

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
            $validate = [
                'id_monthly_mc' => $idGlobal,
                'area' => $area['area'],
            ];

            $cekDataArea = $this->areaMcModel->existData($validate);
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
                        'planning_mc' => $detail['kebMesin'],
                        'target' => $detail['target'],
                        'output' => $detail['output']
                    ];
                    $cekvalid = [
                        'id_area_machine' => $idArea,
                        'jarum' => $detail['jarum'],
                    ];
                    $cekDataDetail = $this->detailAreaMc->cekData($cekvalid);
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
            $monthlyData[$area['area']]['idarea'] = $area['id_area_machine'];
            $monthlyData[$area['area']]['totalMesin'] = $area['total_mc'];
            $monthlyData[$area['area']]['planningMc'] = $area['planning_mc'];
            $monthlyData[$area['area']]['outputDz'] = $area['output'];
            $monthlyData[$area['area']]['jarum'] = $this->detailAreaMc->getData($idAreaMc);
        }
        $statusOrder = $this->orderServices->statusOrder($judul);

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
            'data' => $monthlyData,
            'statusOrder' => $statusOrder
        ];
        return view($role . '/Planning/viewPlanMonth', $data);
    }

    public function updateMonthlyMc()
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
        $idGlobal = $global['idglobal'];
        $globalData = [
            'total_mc' => $global['globalMc'],
            'planning_mc' => $global['globalPlan'],
            'total_output' => $global['globalOutput'],
            'mc_socks' => $global['ttlMcSocks'],
            'plan_mc_socks' => $global['ttlPlanSocks'],
            'mc_gloves' => $global['ttlMcGloves'],
            'plan_mc_gloves' => $global['ttlPlanGloves'],
        ];

        // Cek apakah sudah ada data global dengan judul yang sama
        $this->globalModel->update($idGlobal, $globalData);


        // Loop untuk insert area ke tabel area_mc
        foreach ($areaData as $key => $area) {
            $areaInsert = [
                'id_monthly_mc' => $idGlobal,
                'area' => $area['area'],
                'total_mc' => $area['ttlMc'],
                'planning_mc' => $area['planMc'],
                'output' => $area['outputDz']
            ];
            $validate = [
                'id_monthly_mc' => $idGlobal,
                'area' => $area['area'],
            ];

            $cekDataArea = $this->areaMcModel->existData($validate);
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
                        'planning_mc' => $detail['kebMesin'],
                        'target' => $detail['target'],
                        'output' => $detail['output']
                    ];
                    $cekvalid = [
                        'id_area_machine' => $idArea,
                        'jarum' => $detail['jarum'],
                    ];
                    $cekDataDetail = $this->detailAreaMc->cekData($cekvalid);
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
}
