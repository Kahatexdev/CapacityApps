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
use App\Models\LiburModel;
use LengthException;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Week;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\{Border, Alignment};


class ExportController extends BaseController
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
        $data = [
            'title' => 'Sales Position',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
        ];
        return view('Capacity/Sales/index', $data);
    }

    public function export()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();


        $rangeReport = "";
        $jarumDc = $this->jarumModel->getDC();
        $doublecyn = [];
        foreach ($jarumDc as $dc) {
            $jarum = $dc['jarum'];
            $doublecyn[$jarum] = [
                'dakong' => $this->jarumModel->getBrand($jarum, 'dakong') ?? 0,
                'mekanik' => $this->jarumModel->getBrand($jarum, 'MECHANIC') ?? 0,
                'rosso' => $this->jarumModel->getBrand($jarum, 'ROSSO') ?? 0,
                'lonati' => $this->jarumModel->getBrand($jarum, 'lonati') ?? 0,

            ];
        }

        function setCellStyle($sheet, $cellRange, $value, $borderStyle = Border::BORDER_THIN)
        {
            $sheet->mergeCells($cellRange)->setCellValue($cellRange, $value);
            $sheet->getStyle($cellRange)->applyFromArray([
                'borders' => [
                    'outline' => ['borderStyle' => $borderStyle, 'color' => ['rgb' => '000000']],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
        }
        // Judul
        $sheet->setCellValue('D1', 'PT KAHATEX (SOCK DIVISION)');
        $sheet->mergeCells('D2:J2');
        $sheet->setCellValue('D2', 'SALES POSITION AS NO :');
        $sheet->setCellValue('K2', $rangeReport);
        $sheet->setCellValue('D3', "no doc : FOR-PPC-048/090218_011216/HAL_1/2");


        // table data HEADER
        $sheet->mergeCells('A4:A6')->setCellValue('A4', 'KIND OF MACHINE')->getStyle('A4:A6')
            ->applyFromArray([
                'borders' => [
                    'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
        $sheet->mergeCells('B4:R4')->setCellValue('B4', 'NO OF MC')->getStyle('B4:R4')
            ->applyFromArray([
                'borders' => [
                    'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
        $sheet->mergeCells('S4:U5')->setCellValue('S4', 'Running MC Actual')->getStyle('S4:U5')
            ->applyFromArray([
                'borders' => [
                    'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
        $sheet->setCellValue('B5', 'orgnl')->getStyle('B5')
            ->applyFromArray([
                'borders' => [
                    'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
        $sheet->mergeCells('C5:G5')->setCellValue('C5', 'MACHINE')->getStyle('C5:G5')
            ->applyFromArray([
                'borders' => [
                    'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
        $sheet->mergeCells('H5:M5')->setCellValue('H5', 'RUNNING')->getStyle('H5:M5')
            ->applyFromArray([
                'borders' => [
                    'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
        $sheet->mergeCells('N5:N6')->setCellValue('N5', 'mc break down')->getStyle('N5:N6')
            ->applyFromArray([
                'borders' => [
                    'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
        $sheet->mergeCells('O5:O6')->setCellValue('O5', 'mc in wh')->getStyle('O5:O6')
            ->applyFromArray([
                'borders' => [
                    'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
        $sheet->mergeCells('P5:R5')->setCellValue('P5', 'Stock Cylinder')->getStyle('P5:R5')
            ->applyFromArray([
                'borders' => [
                    'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
        // original data
        $sheet->setCellValue('B6', '')->getStyle('B6')
            ->applyFromArray([
                'borders' => [
                    'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
        // machine data
        $sheet->setCellValue('C6', 'Dk')->getStyle('C6')
            ->applyFromArray([
                'borders' => [
                    'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
        $sheet->setCellValue('D6', 'Rosso')->getStyle('D6')
            ->applyFromArray([
                'borders' => [
                    'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
        $sheet->setCellValue('E6', 'Ths')->getStyle('E6')
            ->applyFromArray([
                'borders' => [
                    'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
        $sheet->setCellValue('F6', 'Ths')->getStyle('F6')
            ->applyFromArray([
                'borders' => [
                    'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
        $sheet->setCellValue('F6', 'Lon')->getStyle('F6')
            ->applyFromArray([
                'borders' => [
                    'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
        $sheet->setCellValue('G6', 'TTL')->getStyle('G6')
            ->applyFromArray([
                'borders' => [
                    'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
        // runing data
        $sheet->setCellValue('H6', 'Dk')->getStyle('H6')
            ->applyFromArray([
                'borders' => [
                    'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
        $sheet->setCellValue('I6', 'Rosso')->getStyle('I6')
            ->applyFromArray([
                'borders' => [
                    'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
        $sheet->setCellValue('E6', 'Ths')->getStyle('E6')
            ->applyFromArray([
                'borders' => [
                    'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
        $sheet->setCellValue('J6', 'Ths')->getStyle('J6')
            ->applyFromArray([
                'borders' => [
                    'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
        $sheet->setCellValue('K6', 'Lon')->getStyle('K6')
            ->applyFromArray([
                'borders' => [
                    'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
        $sheet->setCellValue('L6', 'Spl')->getStyle('L6')
            ->applyFromArray([
                'borders' => [
                    'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
        $sheet->setCellValue('M6', 'TTL')->getStyle('M6')
            ->applyFromArray([
                'borders' => [
                    'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
        // stock cylinder
        $sheet->setCellValue('P6', 'Dk')->getStyle('P6')
            ->applyFromArray([
                'borders' => [
                    'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
        $sheet->setCellValue('Q6', 'Ths')->getStyle('Q6')
            ->applyFromArray([
                'borders' => [
                    'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
        $sheet->setCellValue('R6', 'Rosso')->getStyle('R6')
            ->applyFromArray([
                'borders' => [
                    'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
        $sheet->setCellValue('S6', 'CJ')->getStyle('S6')
            ->applyFromArray([
                'borders' => [
                    'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
        $sheet->setCellValue('T6', 'MJ')->getStyle('T6')
            ->applyFromArray([
                'borders' => [
                    'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
        $sheet->setCellValue('U6', 'TTL')->getStyle('U6')
            ->applyFromArray([
                'borders' => [
                    'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);



        // data body
        $rowjarum = 7;
        $totalDCRow = count($doublecyn) + $rowjarum;
        $totalDakong = 0;
        $totalRosso = 0;
        $totalThs = 0;
        $totalLon = 0;
        $totalDc = 0;
        foreach ($doublecyn as $jarum => $item) {

            $total = $item['dakong'] + $item['rosso'] + $item['mekanik'] + $item['lonati'];
            $totalDakong  += $item['dakong'];
            $totalThs  += $item['mekanik'];
            $totalRosso  += $item['rosso'];
            $totalLon  += $item['lonati'];
            $totalDc  += $total;


            $sheet->setCellValue('A' . $rowjarum, $jarum)
                ->setCellValue('C' . $rowjarum, $item['dakong'])
                ->setCellValue('D' . $rowjarum, $item['rosso'])
                ->setCellValue('E' . $rowjarum, $item['mekanik'])
                ->setCellValue('F' . $rowjarum, $item['lonati'])
                ->setCellValue('B' . $rowjarum, "0")
                ->setCellValue('G' . $rowjarum, $total);
            $cellCoordinates = ['A' . $rowjarum, 'C' . $rowjarum, 'D' . $rowjarum, 'F' . $rowjarum, 'G' . $rowjarum, 'B' . $rowjarum, 'E' . $rowjarum,];
            // Mengatur gaya sel dengan border untuk setiap kolom dalam baris saat ini
            foreach ($cellCoordinates as $cellCoordinate) {
                $sheet->getStyle($cellCoordinate)->applyFromArray([
                    'borders' => [
                        'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                    ],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
            }
            $sheet->setCellValue('A' . $totalDCRow, 'TOTAL DOUBLE CYLINDER')
                ->setCellValue('C' . $totalDCRow, $totalDakong)
                ->setCellValue('D' . $totalDCRow, $totalRosso)
                ->setCellValue('E' . $totalDCRow, $totalThs)
                ->setCellValue('G' . $totalDCRow, $totalDc)
                ->setCellValue('F' . $totalDCRow, $totalLon);
            $boldStyle = ['A' . $totalDCRow, 'C' . $totalDCRow, 'B' . $totalDCRow, 'D' . $totalDCRow, 'E' . $totalDCRow, 'F' . $totalDCRow, 'G' . $totalDCRow,];
            foreach ($boldStyle as $bs) {
                $sheet->getStyle($bs)->applyFromArray([
                    'borders' => [
                        'outline' => ['borderStyle' => Border::BORDER_THICK, 'color' => ['rgb' => '000000']],
                    ],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
            }
            $rowjarum++;
        }
        // download Data
        $writer = new Xlsx($spreadsheet);
        $file_path = WRITEPATH . 'uploads/data.xlsx'; // Lokasi penyimpanan file Excel
        $writer->save($file_path);

        // Unduh file
        return $this->response->download($file_path, null)->setFileName('data.xlsx');
    }
}
