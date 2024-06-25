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
use PhpOffice\PhpSpreadsheet\Style\{Border, Alignment, Fill};


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
            'role' => session()->get('role'),
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
            $jarum = $dc['aliasjarum'];
            $needle = $dc['jarum'];
            $doublecyn[$jarum] = [
                'dakong' => $this->jarumModel->getBrand($jarum, 'dakong') ?? 0,
                'mekanik' => $this->jarumModel->getBrand($jarum, 'MECHANIC') ?? 0,
                'rosso' => $this->jarumModel->getBrand($jarum, 'ROSSO') ?? 0,
                'lonati' => $this->jarumModel->getBrand($jarum, 'lonati') ?? 0,
                'dakongRun' => $this->jarumModel->getRunningMc($jarum, 'dakong') ?? 0,
                'mekanikRun' => $this->jarumModel->getRunningMc($jarum, 'MECHANIC') ?? 0,
                'rossoRun' => $this->jarumModel->getRunningMc($jarum, 'ROSSO') ?? 0,
                'lonatiRun' => $this->jarumModel->getRunningMc($jarum, 'lonati') ?? 0,
                'cjRun'  => $this->jarumModel->getRunningMcPU($jarum, 'CJ') ?? 0,
                'mjRun'  => $this->jarumModel->getRunningMcPU($jarum, 'MJ') ?? 0,
                'stockCylDk' => $this->jarumModel->getStockSylDc($needle, "3") ?? 0,
                'stockCylThs' => $this->jarumModel->getStockSylDc($needle, "THS") ?? 0,
                'stockCylRosso' => $this->jarumModel->getStockSylDc($needle, "Rosso") ?? 0,
            ];
        }

        $jarumBabyComp = $this->jarumModel->getBabyComp();
        $babyComp = [];
        foreach ($jarumBabyComp as $bc) {
            $jarum = $bc['aliasjarum'];
            $needle = $bc['jarum'];
            $babyComp[$jarum] = [
                'dakong' => $this->jarumModel->getBrand($jarum, 'dakong') ?? 0,
                'mekanik' => $this->jarumModel->getBrand($jarum, 'MECHANIC') ?? 0,
                'rosso' => $this->jarumModel->getBrand($jarum, 'ROSSO') ?? 0,
                'lonati' => $this->jarumModel->getBrand($jarum, 'lonati') ?? 0,
                'dakongRun' => $this->jarumModel->getRunningMc($jarum, 'dakong') ?? 0,
                'mekanikRun' => $this->jarumModel->getRunningMc($jarum, 'MECHANIC') ?? 0,
                'rossoRun' => $this->jarumModel->getRunningMc($jarum, 'ROSSO') ?? 0,
                'lonatiRun' => $this->jarumModel->getRunningMc($jarum, 'lonati') ?? 0,
                'cjRun'  => $this->jarumModel->getRunningMcPU($jarum, 'CJ') ?? 0,
                'mjRun'  => $this->jarumModel->getRunningMcPU($jarum, 'MJ') ?? 0,
                'stockCylDk' => $this->jarumModel->getStockSylDc($needle, "3") ?? 0,
                'stockCylThs' => $this->jarumModel->getStockSylDc($needle, "THS") ?? 0,
                'stockCylRosso' => $this->jarumModel->getStockSylDc($needle, "Rosso") ?? 0,
            ];
        }

        $jarumBabyComp108 = $this->jarumModel->getBabyComp108();
        $babyComp108 = [];
        foreach ($jarumBabyComp108 as $bc) {
            $jarum = $bc['aliasjarum'];
            $needle = $bc['jarum'];
            $babyComp108[$jarum] = [
                'dakong' => $this->jarumModel->getBrand($jarum, 'dakong') ?? 0,
                'mekanik' => $this->jarumModel->getBrand($jarum, 'MECHANIC') ?? 0,
                'rosso' => $this->jarumModel->getBrand($jarum, 'ROSSO') ?? 0,
                'lonati' => $this->jarumModel->getBrand($jarum, 'lonati') ?? 0,
                'dakongRun' => $this->jarumModel->getRunningMc($jarum, 'dakong') ?? 0,
                'mekanikRun' => $this->jarumModel->getRunningMc($jarum, 'MECHANIC') ?? 0,
                'rossoRun' => $this->jarumModel->getRunningMc($jarum, 'ROSSO') ?? 0,
                'lonatiRun' => $this->jarumModel->getRunningMc($jarum, 'lonati') ?? 0,
                'cjRun'  => $this->jarumModel->getRunningMcPU($jarum, 'CJ') ?? 0,
                'mjRun'  => $this->jarumModel->getRunningMcPU($jarum, 'MJ') ?? 0,
                'stockCylDk' => $this->jarumModel->getStockSylDc($needle, "3") ?? 0,
                'stockCylThs' => $this->jarumModel->getStockSylDc($needle, "THS") ?? 0,
                'stockCylRosso' => $this->jarumModel->getStockSylDc($needle, "Rosso") ?? 0,
            ];
        }

        $jarumChildComp120 = $this->jarumModel->getChildComp120();
        $childComp120 = [];
        foreach ($jarumChildComp120 as $cc) {
            $jarum = $cc['aliasjarum'];
            $needle = $cc['jarum'];
            $childComp120[$jarum] = [
                'dakong' => $this->jarumModel->getBrand($jarum, 'dakong') ?? 0,
                'mekanik' => $this->jarumModel->getBrand($jarum, 'MECHANIC') ?? 0,
                'rosso' => $this->jarumModel->getBrand($jarum, 'ROSSO') ?? 0,
                'lonati' => $this->jarumModel->getBrand($jarum, 'lonati') ?? 0,
                'dakongRun' => $this->jarumModel->getRunningMc($jarum, 'dakong') ?? 0,
                'mekanikRun' => $this->jarumModel->getRunningMc($jarum, 'MECHANIC') ?? 0,
                'rossoRun' => $this->jarumModel->getRunningMc($jarum, 'ROSSO') ?? 0,
                'lonatiRun' => $this->jarumModel->getRunningMc($jarum, 'lonati') ?? 0,
                'cjRun'  => $this->jarumModel->getRunningMcPU($jarum, 'CJ') ?? 0,
                'mjRun'  => $this->jarumModel->getRunningMcPU($jarum, 'MJ') ?? 0,
                'stockCylDk' => $this->jarumModel->getStockSylDc($needle, "3") ?? 0,
                'stockCylThs' => $this->jarumModel->getStockSylDc($needle, "THS") ?? 0,
                'stockCylRosso' => $this->jarumModel->getStockSylDc($needle, "Rosso") ?? 0,
            ];
        }

        $jarumLadyComp = $this->jarumModel->getLadyComp();
        $ladyComp = [];
        foreach ($jarumLadyComp as $lc) {
            $jarum = $lc['aliasjarum'];
            $needle = $lc['jarum'];
            $ladyComp[$jarum] = [
                'dakong' => $this->jarumModel->getBrand($jarum, 'dakong') ?? 0,
                'mekanik' => $this->jarumModel->getBrand($jarum, 'MECHANIC') ?? 0,
                'rosso' => $this->jarumModel->getBrand($jarum, 'ROSSO') ?? 0,
                'lonati' => $this->jarumModel->getBrand($jarum, 'lonati') ?? 0,
                'dakongRun' => $this->jarumModel->getRunningMc($jarum, 'dakong') ?? 0,
                'mekanikRun' => $this->jarumModel->getRunningMc($jarum, 'MECHANIC') ?? 0,
                'rossoRun' => $this->jarumModel->getRunningMc($jarum, 'ROSSO') ?? 0,
                'lonatiRun' => $this->jarumModel->getRunningMc($jarum, 'lonati') ?? 0,
                'cjRun'  => $this->jarumModel->getRunningMcPU($jarum, 'CJ') ?? 0,
                'mjRun'  => $this->jarumModel->getRunningMcPU($jarum, 'MJ') ?? 0,
                'stockCylDk' => $this->jarumModel->getStockSylDc($needle, "3") ?? 0,
                'stockCylThs' => $this->jarumModel->getStockSylDc($needle, "THS") ?? 0,
                'stockCylRosso' => $this->jarumModel->getStockSylDc($needle, "Rosso") ?? 0,
            ];
        }

        $jarumMensComp = $this->jarumModel->getMensComp168();
        $mensComp = [];
        foreach ($jarumMensComp as $mc) {
            $jarum = $mc['aliasjarum'];
            $needle = $mc['jarum'];
            $mensComp[$jarum] = [
                'dakong' => $this->jarumModel->getBrand($jarum, 'dakong') ?? 0,
                'mekanik' => $this->jarumModel->getBrand($jarum, 'MECHANIC') ?? 0,
                'rosso' => $this->jarumModel->getBrand($jarum, 'ROSSO') ?? 0,
                'lonati' => $this->jarumModel->getBrand($jarum, 'lonati') ?? 0,
                'dakongRun' => $this->jarumModel->getRunningMc($jarum, 'dakong') ?? 0,
                'mekanikRun' => $this->jarumModel->getRunningMc($jarum, 'MECHANIC') ?? 0,
                'rossoRun' => $this->jarumModel->getRunningMc($jarum, 'ROSSO') ?? 0,
                'lonatiRun' => $this->jarumModel->getRunningMc($jarum, 'lonati') ?? 0,
                'cjRun'  => $this->jarumModel->getRunningMcPU($jarum, 'CJ') ?? 0,
                'mjRun'  => $this->jarumModel->getRunningMcPU($jarum, 'MJ') ?? 0,
                'stockCylDk' => $this->jarumModel->getStockSylDc($needle, "3") ?? 0,
                'stockCylThs' => $this->jarumModel->getStockSylDc($needle, "THS") ?? 0,
                'stockCylRosso' => $this->jarumModel->getStockSylDc($needle, "Rosso") ?? 0,
            ];
        }

        $jarumMensComp = $this->jarumModel->getMensComp168();
        $mensComp = [];
        foreach ($jarumMensComp as $mc) {
            $jarum = $mc['aliasjarum'];
            $needle = $mc['jarum'];
            $mensComp[$jarum] = [
                'dakong' => $this->jarumModel->getBrand($jarum, 'dakong') ?? 0,
                'mekanik' => $this->jarumModel->getBrand($jarum, 'MECHANIC') ?? 0,
                'rosso' => $this->jarumModel->getBrand($jarum, 'ROSSO') ?? 0,
                'lonati' => $this->jarumModel->getBrand($jarum, 'lonati') ?? 0,
                'dakongRun' => $this->jarumModel->getRunningMc($jarum, 'dakong') ?? 0,
                'mekanikRun' => $this->jarumModel->getRunningMc($jarum, 'MECHANIC') ?? 0,
                'rossoRun' => $this->jarumModel->getRunningMc($jarum, 'ROSSO') ?? 0,
                'lonatiRun' => $this->jarumModel->getRunningMc($jarum, 'lonati') ?? 0,
                'cjRun'  => $this->jarumModel->getRunningMcPU($jarum, 'CJ') ?? 0,
                'mjRun'  => $this->jarumModel->getRunningMcPU($jarum, 'MJ') ?? 0,
                'stockCylDk' => $this->jarumModel->getStockSylDc($needle, "3") ?? 0,
                'stockCylThs' => $this->jarumModel->getStockSylDc($needle, "THS") ?? 0,
                'stockCylRosso' => $this->jarumModel->getStockSylDc($needle, "Rosso") ?? 0,
            ];
        }

        $jarumMensComp200 = $this->jarumModel->getMensComp200();
        $mensComp200 = [];
        foreach ($jarumMensComp200 as $mc) {
            $jarum = $mc['aliasjarum'];
            $needle = $mc['jarum'];
            $mensComp200[$jarum] = [
                'dakong' => $this->jarumModel->getBrand($jarum, 'dakong') ?? 0,
                'mekanik' => $this->jarumModel->getBrand($jarum, 'MECHANIC') ?? 0,
                'rosso' => $this->jarumModel->getBrand($jarum, 'ROSSO') ?? 0,
                'lonati' => $this->jarumModel->getBrand($jarum, 'lonati') ?? 0,
                'dakongRun' => $this->jarumModel->getRunningMc($jarum, 'dakong') ?? 0,
                'mekanikRun' => $this->jarumModel->getRunningMc($jarum, 'MECHANIC') ?? 0,
                'rossoRun' => $this->jarumModel->getRunningMc($jarum, 'ROSSO') ?? 0,
                'lonatiRun' => $this->jarumModel->getRunningMc($jarum, 'lonati') ?? 0,
                'cjRun'  => $this->jarumModel->getRunningMcPU($jarum, 'CJ') ?? 0,
                'mjRun'  => $this->jarumModel->getRunningMcPU($jarum, 'MJ') ?? 0,
                'stockCylDk' => $this->jarumModel->getStockSylDc($needle, "3") ?? 0,
                'stockCylThs' => $this->jarumModel->getStockSylDc($needle, "THS") ?? 0,
                'stockCylRosso' => $this->jarumModel->getStockSylDc($needle, "Rosso") ?? 0,
            ];
        }

        $jarumChildComp132 = $this->jarumModel->getChildComp132();
        $childComp132 = [];
        foreach ($jarumChildComp132 as $cc) {
            $jarum = $cc['aliasjarum'];
            $needle = $cc['jarum'];
            $childComp132[$jarum] = [
                'dakong' => $this->jarumModel->getBrand($jarum, 'dakong') ?? 0,
                'mekanik' => $this->jarumModel->getBrand($jarum, 'MECHANIC') ?? 0,
                'rosso' => $this->jarumModel->getBrand($jarum, 'ROSSO') ?? 0,
                'lonati' => $this->jarumModel->getBrand($jarum, 'lonati') ?? 0,
                'dakongRun' => $this->jarumModel->getRunningMc($jarum, 'dakong') ?? 0,
                'mekanikRun' => $this->jarumModel->getRunningMc($jarum, 'MECHANIC') ?? 0,
                'rossoRun' => $this->jarumModel->getRunningMc($jarum, 'ROSSO') ?? 0,
                'lonatiRun' => $this->jarumModel->getRunningMc($jarum, 'lonati') ?? 0,
                'cjRun'  => $this->jarumModel->getRunningMcPU($jarum, 'CJ') ?? 0,
                'mjRun'  => $this->jarumModel->getRunningMcPU($jarum, 'MJ') ?? 0,
                'stockCylDk' => $this->jarumModel->getStockSylDc($needle, "3") ?? 0,
                'stockCylThs' => $this->jarumModel->getStockSylDc($needle, "THS") ?? 0,
                'stockCylRosso' => $this->jarumModel->getStockSylDc($needle, "Rosso") ?? 0,
            ];
        }

        $jarumMensComp156 = $this->jarumModel->getMensComp156();
        $mensComp156 = [];
        foreach ($jarumMensComp156 as $cc) {
            $jarum = $cc['aliasjarum'];
            $needle = $cc['jarum'];
            $mensComp156[$jarum] = [
                'dakong' => $this->jarumModel->getBrand($jarum, 'dakong') ?? 0,
                'mekanik' => $this->jarumModel->getBrand($jarum, 'MECHANIC') ?? 0,
                'rosso' => $this->jarumModel->getBrand($jarum, 'ROSSO') ?? 0,
                'lonati' => $this->jarumModel->getBrand($jarum, 'lonati') ?? 0,
                'dakongRun' => $this->jarumModel->getRunningMc($jarum, 'dakong') ?? 0,
                'mekanikRun' => $this->jarumModel->getRunningMc($jarum, 'MECHANIC') ?? 0,
                'rossoRun' => $this->jarumModel->getRunningMc($jarum, 'ROSSO') ?? 0,
                'lonatiRun' => $this->jarumModel->getRunningMc($jarum, 'lonati') ?? 0,
                'cjRun'  => $this->jarumModel->getRunningMcPU($jarum, 'CJ') ?? 0,
                'mjRun'  => $this->jarumModel->getRunningMcPU($jarum, 'MJ') ?? 0,
                'stockCylDk' => $this->jarumModel->getStockSylDc($needle, "3") ?? 0,
                'stockCylThs' => $this->jarumModel->getStockSylDc($needle, "THS") ?? 0,
                'stockCylRosso' => $this->jarumModel->getStockSylDc($needle, "Rosso") ?? 0,
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
        $sheet->mergeCells('V4:V6')->setCellValue('V4', 'WEEK')->getStyle('V4:V5')
            ->applyFromArray([
                'borders' => [
                    'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
        $sheet->mergeCells('W4:X5')->setCellValue('W4', 'Capasity Produksi')->getStyle('W4:X5')
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
        $sheet->setCellValue('W6', '-3')->getStyle('W6')
            ->applyFromArray([
                'borders' => [
                    'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
        $sheet->setCellValue('X6', 'Pro 28 hari')->getStyle('X6')
            ->applyFromArray([
                'borders' => [
                    'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);


        // data body machine
        $rowDC = 7;
        $totalDCRow = count($doublecyn) + $rowDC;
        $rowBC = $totalDCRow + 1;
        $rangerowBC = count($babyComp);
        $totalBCRow = count($babyComp) + $rowBC;
        $rowBC108 = $totalBCRow + 1;
        $totalBC108Row = count($babyComp108) + $rowBC108;
        $rowCC120 = $totalBC108Row + 1;
        $totalCC120Row = count($childComp120) + $rowCC120;
        $rowLC = $totalCC120Row + 1;
        $totalLCRow = count($ladyComp) + $rowLC;
        $rowMC168 = $totalLCRow + 1;
        $totalMC168Row = count($mensComp) + $rowMC168;
        $rowMC200 = $totalMC168Row + 1;
        $rowCC132 = $totalMC168Row + $rowMC200 + 1;
        $rowMC156 = $totalMC168Row + $rowMC200 + $rowCC132 + 1;
        $totalSCCompRow = $rowMC156 + 1;
        $sumSCComp = $babyComp + $babyComp108 + $childComp120 + $ladyComp + $mensComp + $mensComp200 + $childComp132 + $mensComp156;

        $totalDakong = 0;
        $totalRosso = 0;
        $totalThs = 0;
        $totalLon = 0;
        $totalDc = 0;
        $totalDakongRun = 0;
        $totalRossoRun = 0;
        $totalThsRun = 0;
        $totalLonRun = 0;
        $totalDcRun = 0;
        $totalStockCylDk = 0;
        $totalStockCylThs = 0;
        $totalStockCylRosso = 0;
        $totalCjRun = 0;
        $totalMjRun = 0;
        $totalMcDcRunperPU = 0;
        $week = 0;
        $minus3 = 0;
        $po28hari = 0;
        //DOUBLE CYLINDER
        foreach ($doublecyn as $jarum => $item) {

            $totalMc = $item['dakong'] + $item['rosso'] + $item['mekanik'] + $item['lonati'];
            $totalDakong  += $item['dakong'];
            $totalThs  += $item['mekanik'];
            $totalRosso  += $item['rosso'];
            $totalLon  += $item['lonati'];
            $totalDc  += $totalMc;

            $totalMcDcRun = $item['dakongRun'] + $item['rossoRun'] + $item['mekanikRun'] + $item['lonatiRun'];
            $totalDakongRun  += $item['dakongRun'];
            $totalThsRun  += $item['mekanikRun'];
            $totalRossoRun  += $item['rossoRun'];
            $totalLonRun  += $item['lonatiRun'];
            $totalDcRun += $totalMcDcRun;

            $totalStockCylDk += $item['stockCylDk'];
            $totalStockCylThs += $item['stockCylThs'];
            $totalStockCylRosso += $item['stockCylRosso'];

            $totalCjRun += $item['cjRun'];
            $totalMjRun +=  $item['mjRun'];
            $ttlMcDcRunperPU = $item['cjRun'] + $item['mjRun'];
            $totalMcDcRunperPU +=  $ttlMcDcRunperPU;


            $sheet->setCellValue('A' . $rowDC, $jarum)
                ->setCellValue('C' . $rowDC, $item['dakong'])
                ->setCellValue('D' . $rowDC, $item['rosso'])
                ->setCellValue('E' . $rowDC, $item['mekanik'])
                ->setCellValue('F' . $rowDC, $item['lonati'])
                ->setCellValue('B' . $rowDC, "0")
                ->setCellValue('G' . $rowDC, $totalMc);
            $cellCoordinates = ['A' . $rowDC, 'C' . $rowDC, 'D' . $rowDC, 'F' . $rowDC, 'G' . $rowDC, 'B' . $rowDC, 'E' . $rowDC,];
            foreach ($cellCoordinates as $cellCoordinate) {
                $sheet->getStyle($cellCoordinate)->applyFromArray([
                    'borders' => [
                        'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                    ],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
            }
            // total mesin DC
            $sheet->setCellValue('A' . $totalDCRow, 'TOTAL DOUBLE CYLINDER')
                ->setCellValue('C' . $totalDCRow, $totalDakong)
                ->setCellValue('D' . $totalDCRow, $totalRosso)
                ->setCellValue('E' . $totalDCRow, $totalThs)
                ->setCellValue('G' . $totalDCRow, $totalDc)
                ->setCellValue('F' . $totalDCRow, $totalLon)
                ->setCellValue('H' . $totalDCRow, $totalDakongRun)
                ->setCellValue('I' . $totalDCRow, $totalRossoRun)
                ->setCellValue('J' . $totalDCRow, $totalThsRun)
                ->setCellValue('K' . $totalDCRow, $totalLonRun)
                ->setCellValue('M' . $totalDCRow, $totalDcRun);
            $boldStyle = ['A' . $totalDCRow, 'C' . $totalDCRow, 'B' . $totalDCRow, 'D' . $totalDCRow, 'E' . $totalDCRow, 'F' . $totalDCRow,  'H' . $totalDCRow, 'I' . $totalDCRow, 'J' . $totalDCRow, 'K' . $totalDCRow, 'M' . $totalDCRow, 'L' . $totalDCRow, 'N' . $totalDCRow, 'O' . $totalDCRow, 'P' . $totalDCRow, 'Q' . $totalDCRow,  'R' . $totalDCRow, 'S' . $totalDCRow, 'T' . $totalDCRow, 'V' . $totalDCRow, 'W' . $totalDCRow, 'X' . $totalDCRow,];
            foreach ($boldStyle as $bs) {
                $sheet->getStyle($bs)->applyFromArray([
                    'font' => ['bold' => true],
                    'borders' => [
                        'outline' => ['borderStyle' => Border::BORDER_THICK, 'color' => ['rgb' => '000000']],
                    ],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
            }
            // totalRunningMc
            $sheet
                ->setCellValue('H' . $rowDC, $item['dakongRun'])
                ->setCellValue('I' . $rowDC, $item['rossoRun'])
                ->setCellValue('J' . $rowDC, $item['mekanikRun'])
                ->setCellValue('K' . $rowDC, $item['lonatiRun'])
                ->setCellValue('M' . $rowDC, $totalMcDcRun);
            $cellCoordinates = ['H' . $rowDC, 'I' . $rowDC, 'J' . $rowDC, 'K' . $rowDC,  'N' . $rowDC, 'L' . $rowDC,  'O' . $rowDC,  'P' . $rowDC,  'Q' . $rowDC,];
            foreach ($cellCoordinates as $cellCoordinate) {
                $sheet->getStyle($cellCoordinate)->applyFromArray([
                    'borders' => [
                        'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                    ],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],

                ]);
                // stockCylinderDc
                $sheet
                    ->setCellValue('P' . $rowDC, $item['stockCylDk'])
                    ->setCellValue('Q' . $rowDC, $item['stockCylThs'])
                    ->setCellValue('R' . $rowDC, $item['stockCylRosso'])
                    ->setCellValue('P' . $totalDCRow, $totalStockCylDk)
                    ->setCellValue('Q' . $totalDCRow, $totalStockCylThs)
                    ->setCellValue('R' . $totalDCRow, $totalStockCylRosso);

                $cellCoordinates = ['P' . $rowDC, 'Q' . $rowDC, 'J' . $rowDC, 'R' . $rowDC,];
                foreach ($cellCoordinates as $cellCoordinate) {
                    $sheet->getStyle($cellCoordinate)->applyFromArray([
                        'borders' => [
                            'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                        ],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],

                    ]);
                }
                // totalRunningMc PU &stockCYl
                $sheet
                    ->setCellValue('S' . $totalDCRow, $totalCjRun)
                    ->setCellValue('T' . $totalDCRow, $totalMjRun)
                    ->setCellValue('U' . $totalDCRow, $totalMcDcRunperPU)
                    ->setCellValue('S' . $rowDC, $item['cjRun'])
                    ->setCellValue('T' . $rowDC, $item['mjRun'])
                    ->setCellValue('U' . $rowDC, $ttlMcDcRunperPU);
                $cellCoordinates = ['S' . $rowDC, 'T' . $rowDC, 'U' . $rowDC,];
                foreach ($cellCoordinates as $cellCoordinate) {
                    $sheet->getStyle($cellCoordinate)->applyFromArray([
                        'borders' => [
                            'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                        ],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],

                    ]);
                }
                // WEEK & Capasity Produksi
                $sheet
                    ->setCellValue('V' . $totalDCRow, $week)
                    ->setCellValue('W' . $totalDCRow, $minus3)
                    ->setCellValue('X' . $totalDCRow, $po28hari)
                    ->setCellValue('V' . $rowDC, "0")
                    ->setCellValue('W' . $rowDC, "0")
                    ->setCellValue('X' . $rowDC, "0");
                $cellCoordinates = ['V' . $rowDC, 'W' . $rowDC, 'X' . $rowDC,];
                foreach ($cellCoordinates as $cellCoordinate) {
                    $sheet->getStyle($cellCoordinate)->applyFromArray([
                        'borders' => [
                            'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                        ],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],

                    ]);
                }

                $totalStyle = ['U' . $rowDC, 'M' . $rowDC, 'G' . $rowDC, 'G' . $totalDCRow, 'M' . $totalDCRow, 'U' . $totalDCRow,];
                foreach ($totalStyle  as $styleTotal) {
                    $sheet->getStyle($styleTotal)->applyFromArray([
                        'borders' => [
                            'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                        ],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'FFA500'], // Orange color
                        ],
                    ]);
                }
            }

            $rowDC++;
        }

        //BABY COMP N 84 & 96
        foreach ($babyComp as $key => $item) {
            $totalMc = $item['dakong'] + $item['rosso'] + $item['mekanik'] + $item['lonati'];
            $totalDakong  += $item['dakong'];
            $totalThs  += $item['mekanik'];
            $totalRosso  += $item['rosso'];
            $totalLon  += $item['lonati'];
            $totalDc  += $totalMc;

            $totalMcDcRun = $item['dakongRun'] + $item['rossoRun'] + $item['mekanikRun'] + $item['lonatiRun'];
            $totalDakongRun  += $item['dakongRun'];
            $totalThsRun  += $item['mekanikRun'];
            $totalRossoRun  += $item['rossoRun'];
            $totalLonRun  += $item['lonatiRun'];
            $totalDcRun += $totalMcDcRun;

            $totalStockCylDk += $item['stockCylDk'];
            $totalStockCylThs += $item['stockCylThs'];
            $totalStockCylRosso += $item['stockCylRosso'];

            $totalCjRun += $item['cjRun'];
            $totalMjRun +=  $item['mjRun'];
            $ttlMcDcRunperPU = $item['cjRun'] + $item['mjRun'];
            $totalMcDcRunperPU +=  $ttlMcDcRunperPU;
            $mergerow = $rowBC + 4;

            $sheet->setCellValue('A' . $rowBC, $key)
                ->setCellValue('C' . $rowBC, $item['dakong'])
                ->setCellValue('D' . $rowBC, $item['rosso'])
                ->setCellValue('E' . $rowBC, $item['mekanik'])
                ->setCellValue('F' . $rowBC, $item['lonati'])
                ->setCellValue('B' . $rowBC, "0")
                ->mergeCells('A' . $rowBC . ':A' . $mergerow)
                ->mergeCells('B' . $rowBC . ':B' . $mergerow)
                ->mergeCells('C' . $rowBC . ':C' . $mergerow)
                ->mergeCells('D' . $rowBC . ':D' . $mergerow)
                ->mergeCells('E' . $rowBC . ':E' . $mergerow)
                ->mergeCells('F' . $rowBC . ':F' . $mergerow)
                ->mergeCells('G' . $rowBC . ':G' . $mergerow)
                ->mergeCells('H' . $rowBC . ':H' . $mergerow)
                ->mergeCells('I' . $rowBC . ':I' . $mergerow)
                ->mergeCells('J' . $rowBC . ':J' . $mergerow)
                ->mergeCells('K' . $rowBC . ':K' . $mergerow)
                ->mergeCells('L' . $rowBC . ':L' . $mergerow)
                ->mergeCells('M' . $rowBC . ':M' . $mergerow)
                ->mergeCells('N' . $rowBC . ':N' . $mergerow)
                ->mergeCells('O' . $rowBC . ':O' . $mergerow)
                ->mergeCells('P' . $rowBC . ':P' . $mergerow)
                ->mergeCells('Q' . $rowBC . ':Q' . $mergerow)
                ->mergeCells('R' . $rowBC . ':R' . $mergerow)
                ->mergeCells('S' . $rowBC . ':S' . $mergerow)
                ->mergeCells('T' . $rowBC . ':T' . $mergerow)
                ->mergeCells('U' . $rowBC . ':U' . $mergerow)
                ->setCellValue('G' . $rowBC, $totalMc);
            $cellCoordinates = ['A' . $rowBC, 'C' . $rowBC, 'D' . $rowBC, 'F' . $rowBC, 'G' . $rowBC, 'B' . $rowBC, 'E' . $rowBC,];
            foreach ($cellCoordinates as $cellCoordinate) {
                $sheet->getStyle($cellCoordinate)->applyFromArray([
                    'borders' => [
                        'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                    ],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
            }
            // total mesin BC
            $sheet->setCellValue('A' . $totalBCRow, 'TOTAL BABY COMP N84 + N96')
                ->setCellValue('C' . $totalBCRow, $totalDakong)
                ->setCellValue('D' . $totalBCRow, $totalRosso)
                ->setCellValue('E' . $totalBCRow, $totalThs)
                ->setCellValue('G' . $totalBCRow, $totalDc)
                ->setCellValue('F' . $totalBCRow, $totalLon)
                ->setCellValue('H' . $totalBCRow, $totalDakongRun)
                ->setCellValue('I' . $totalBCRow, $totalRossoRun)
                ->setCellValue('J' . $totalBCRow, $totalThsRun)
                ->setCellValue('K' . $totalBCRow, $totalLonRun)
                ->setCellValue('M' . $totalBCRow, $totalDcRun);
            $boldStyle = ['A' . $totalBCRow, 'C' . $totalBCRow, 'B' . $totalBCRow, 'D' . $totalBCRow, 'E' . $totalBCRow, 'F' . $totalBCRow,  'H' . $totalBCRow, 'I' . $totalBCRow, 'J' . $totalBCRow, 'K' . $totalBCRow, 'M' . $totalBCRow, 'L' . $totalBCRow, 'N' . $totalBCRow, 'O' . $totalBCRow, 'P' . $totalBCRow, 'Q' . $totalBCRow,  'R' . $totalBCRow, 'S' . $totalBCRow, 'T' . $totalBCRow, 'V' . $totalBCRow, 'W' . $totalBCRow, 'X' . $totalBCRow,];
            foreach ($boldStyle as $bs) {
                $sheet->getStyle($bs)->applyFromArray([
                    'font' => ['bold' => true],
                    'borders' => [
                        'outline' => ['borderStyle' => Border::BORDER_THICK, 'color' => ['rgb' => '000000']],
                    ],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
            }
            // totalRunningMc
            $sheet
                ->setCellValue('H' . $rowBC, $item['dakongRun'])
                ->setCellValue('I' . $rowBC, $item['rossoRun'])
                ->setCellValue('J' . $rowBC, $item['mekanikRun'])
                ->setCellValue('K' . $rowBC, $item['lonatiRun'])
                ->setCellValue('M' . $rowBC, $totalMcDcRun);
            $cellCoordinates = ['H' . $rowBC, 'I' . $rowBC, 'J' . $rowBC, 'K' . $rowBC,  'N' . $rowBC, 'L' . $rowBC,  'O' . $rowBC,  'P' . $rowBC,  'Q' . $rowBC,];
            foreach ($cellCoordinates as $cellCoordinate) {
                $sheet->getStyle($cellCoordinate)->applyFromArray([
                    'borders' => [
                        'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                    ],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],

                ]);
                // stockCylinderBc
                $sheet
                    ->setCellValue('P' . $rowBC, $item['stockCylDk'])
                    ->setCellValue('Q' . $rowBC, $item['stockCylThs'])
                    ->setCellValue('R' . $rowBC, $item['stockCylRosso'])
                    ->setCellValue('P' . $totalBCRow, $totalStockCylDk)
                    ->setCellValue('Q' . $totalBCRow, $totalStockCylThs)
                    ->setCellValue('R' . $totalBCRow, $totalStockCylRosso);

                $cellCoordinates = ['P' . $rowBC, 'Q' . $rowBC, 'J' . $rowBC, 'R' . $rowBC,];
                foreach ($cellCoordinates as $cellCoordinate) {
                    $sheet->getStyle($cellCoordinate)->applyFromArray([
                        'borders' => [
                            'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                        ],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],

                    ]);
                }
                // totalRunningMc PU &stockCYl
                $sheet
                    ->setCellValue('S' . $totalBCRow, $totalCjRun)
                    ->setCellValue('T' . $totalBCRow, $totalMjRun)
                    ->setCellValue('U' . $totalBCRow, $totalMcDcRunperPU)
                    ->setCellValue('S' . $rowBC, $item['cjRun'])
                    ->setCellValue('T' . $rowBC, $item['mjRun'])
                    ->setCellValue('U' . $rowBC, $ttlMcDcRunperPU);
                $cellCoordinates = ['S' . $rowBC, 'T' . $rowBC, 'U' . $rowBC,];
                foreach ($cellCoordinates as $cellCoordinate) {
                    $sheet->getStyle($cellCoordinate)->applyFromArray([
                        'borders' => [
                            'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                        ],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],

                    ]);
                }
                // WEEK & Capasity Produksi
                $sheet
                    ->setCellValue('V' . $totalBCRow, 'week 1')
                    ->setCellValue('W' . $totalBCRow, $minus3)
                    ->setCellValue('X' . $totalBCRow, $po28hari)
                    ->setCellValue('V' . $rowBC, "0")
                    ->setCellValue('W' . $rowBC, "0")
                    ->setCellValue('X' . $rowBC, "0");
                $cellCoordinates = ['V' . $rowBC, 'W' . $rowBC, 'X' . $rowBC,];
                foreach ($cellCoordinates as $cellCoordinate) {
                    $sheet->getStyle($cellCoordinate)->applyFromArray([
                        'borders' => [
                            'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                        ],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],

                    ]);
                }

                $totalStyle = ['U' . $rowBC, 'M' . $rowBC, 'G' . $rowBC, 'G' . $totalBCRow, 'M' . $totalBCRow, 'U' . $totalBCRow,];
                foreach ($totalStyle  as $styleTotal) {
                    $sheet->getStyle($styleTotal)->applyFromArray([
                        'borders' => [
                            'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                        ],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'FFA500'], // Orange color
                        ],
                    ]);
                }
            }

            $rowBC++;
        }

        //BABY COMP N 108
        foreach ($babyComp108 as $key => $item) {
            $totalMc = $item['dakong'] + $item['rosso'] + $item['mekanik'] + $item['lonati'];
            $totalDakong  += $item['dakong'];
            $totalThs  += $item['mekanik'];
            $totalRosso  += $item['rosso'];
            $totalLon  += $item['lonati'];
            $totalDc  += $totalMc;

            $totalMcDcRun = $item['dakongRun'] + $item['rossoRun'] + $item['mekanikRun'] + $item['lonatiRun'];
            $totalDakongRun  += $item['dakongRun'];
            $totalThsRun  += $item['mekanikRun'];
            $totalRossoRun  += $item['rossoRun'];
            $totalLonRun  += $item['lonatiRun'];
            $totalDcRun += $totalMcDcRun;

            $totalStockCylDk += $item['stockCylDk'];
            $totalStockCylThs += $item['stockCylThs'];
            $totalStockCylRosso += $item['stockCylRosso'];

            $totalCjRun += $item['cjRun'];
            $totalMjRun +=  $item['mjRun'];
            $ttlMcDcRunperPU = $item['cjRun'] + $item['mjRun'];
            $totalMcDcRunperPU +=  $ttlMcDcRunperPU;


            $sheet->setCellValue('A' . $rowBC108, $key)
                ->setCellValue('C' . $rowBC108, $item['dakong'])
                ->setCellValue('D' . $rowBC108, $item['rosso'])
                ->setCellValue('E' . $rowBC108, $item['mekanik'])
                ->setCellValue('F' . $rowBC108, $item['lonati'])
                ->setCellValue('B' . $rowBC108, "0")
                ->setCellValue('G' . $rowBC108, $totalMc);
            $cellCoordinates = ['A' . $rowBC108, 'C' . $rowBC108, 'D' . $rowBC108, 'F' . $rowBC108, 'G' . $rowBC108, 'B' . $rowBC108, 'E' . $rowBC108,];
            foreach ($cellCoordinates as $cellCoordinate) {
                $sheet->getStyle($cellCoordinate)->applyFromArray([
                    'borders' => [
                        'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                    ],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
            }
            // total mesin BC N 108
            $sheet->setCellValue('A' . $totalBC108Row, 'TOTAL BABY COMP N108')
                ->setCellValue('C' . $totalBC108Row, $totalDakong)
                ->setCellValue('D' . $totalBC108Row, $totalRosso)
                ->setCellValue('E' . $totalBC108Row, $totalThs)
                ->setCellValue('G' . $totalBC108Row, $totalDc)
                ->setCellValue('F' . $totalBC108Row, $totalLon)
                ->setCellValue('H' . $totalBC108Row, $totalDakongRun)
                ->setCellValue('I' . $totalBC108Row, $totalRossoRun)
                ->setCellValue('J' . $totalBC108Row, $totalThsRun)
                ->setCellValue('K' . $totalBC108Row, $totalLonRun)
                ->setCellValue('M' . $totalBC108Row, $totalDcRun);
            $boldStyle = ['A' . $totalBC108Row, 'C' . $totalBC108Row, 'B' . $totalBC108Row, 'D' . $totalBC108Row, 'E' . $totalBC108Row, 'F' . $totalBC108Row,  'H' . $totalBC108Row, 'I' . $totalBC108Row, 'J' . $totalBC108Row, 'K' . $totalBC108Row, 'M' . $totalBC108Row, 'L' . $totalBC108Row, 'N' . $totalBC108Row, 'O' . $totalBC108Row, 'P' . $totalBC108Row, 'Q' . $totalBC108Row,  'R' . $totalBC108Row, 'S' . $totalBC108Row, 'T' . $totalBC108Row, 'V' . $totalBC108Row, 'W' . $totalBC108Row, 'X' . $totalBC108Row,];
            foreach ($boldStyle as $bs) {
                $sheet->getStyle($bs)->applyFromArray([
                    'font' => ['bold' => true],
                    'borders' => [
                        'outline' => ['borderStyle' => Border::BORDER_THICK, 'color' => ['rgb' => '000000']],
                    ],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
            }
            // totalRunningMc
            $sheet
                ->setCellValue('H' . $rowBC108, $item['dakongRun'])
                ->setCellValue('I' . $rowBC108, $item['rossoRun'])
                ->setCellValue('J' . $rowBC108, $item['mekanikRun'])
                ->setCellValue('K' . $rowBC108, $item['lonatiRun'])
                ->setCellValue('M' . $rowBC108, $totalMcDcRun);
            $cellCoordinates = ['H' . $rowBC108, 'I' . $rowBC108, 'J' . $rowBC108, 'K' . $rowBC108,  'N' . $rowBC108, 'L' . $rowBC108,  'O' . $rowBC108,  'P' . $rowBC108,  'Q' . $rowBC108,];
            foreach ($cellCoordinates as $cellCoordinate) {
                $sheet->getStyle($cellCoordinate)->applyFromArray([
                    'borders' => [
                        'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                    ],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],

                ]);
                // stockCylinderBc
                $sheet
                    ->setCellValue('P' . $rowBC108, $item['stockCylDk'])
                    ->setCellValue('Q' . $rowBC108, $item['stockCylThs'])
                    ->setCellValue('R' . $rowBC108, $item['stockCylRosso'])
                    ->setCellValue('P' . $totalBC108Row, $totalStockCylDk)
                    ->setCellValue('Q' . $totalBC108Row, $totalStockCylThs)
                    ->setCellValue('R' . $totalBC108Row, $totalStockCylRosso);

                $cellCoordinates = ['P' . $rowBC108, 'Q' . $rowBC108, 'J' . $rowBC108, 'R' . $rowBC108,];
                foreach ($cellCoordinates as $cellCoordinate) {
                    $sheet->getStyle($cellCoordinate)->applyFromArray([
                        'borders' => [
                            'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                        ],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],

                    ]);
                }
                // totalRunningMc PU &stockCYl
                $sheet
                    ->setCellValue('S' . $totalBC108Row, $totalCjRun)
                    ->setCellValue('T' . $totalBC108Row, $totalMjRun)
                    ->setCellValue('U' . $totalBC108Row, $totalMcDcRunperPU)
                    ->setCellValue('S' . $rowBC108, $item['cjRun'])
                    ->setCellValue('T' . $rowBC108, $item['mjRun'])
                    ->setCellValue('U' . $rowBC108, $ttlMcDcRunperPU);
                $cellCoordinates = ['S' . $rowBC108, 'T' . $rowBC108, 'U' . $rowBC108,];
                foreach ($cellCoordinates as $cellCoordinate) {
                    $sheet->getStyle($cellCoordinate)->applyFromArray([
                        'borders' => [
                            'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                        ],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],

                    ]);
                }
                // WEEK & Capasity Produksi
                $sheet
                    ->setCellValue('V' . $totalBC108Row, $week)
                    ->setCellValue('W' . $totalBC108Row, $minus3)
                    ->setCellValue('X' . $totalBC108Row, $po28hari)
                    ->setCellValue('V' . $rowBC108, "0")
                    ->setCellValue('W' . $rowBC108, "0")
                    ->setCellValue('X' . $rowBC108, "0");
                $cellCoordinates = ['V' . $rowBC108, 'W' . $rowBC108, 'X' . $rowBC108,];
                foreach ($cellCoordinates as $cellCoordinate) {
                    $sheet->getStyle($cellCoordinate)->applyFromArray([
                        'borders' => [
                            'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                        ],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],

                    ]);
                }

                $totalStyle = ['U' . $rowBC108, 'M' . $rowBC108, 'G' . $rowBC108, 'G' . $totalBC108Row, 'M' . $totalBC108Row, 'U' . $totalBC108Row,];
                foreach ($totalStyle  as $styleTotal) {
                    $sheet->getStyle($styleTotal)->applyFromArray([
                        'borders' => [
                            'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                        ],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'FFA500'], // Orange color
                        ],
                    ]);
                }
            }

            $rowBC108++;
        }

        //CHILD COMP N120
        foreach ($childComp120 as $key => $item) {
            $totalMc = $item['dakong'] + $item['rosso'] + $item['mekanik'] + $item['lonati'];
            $totalDakong  += $item['dakong'];
            $totalThs  += $item['mekanik'];
            $totalRosso  += $item['rosso'];
            $totalLon  += $item['lonati'];
            $totalDc  += $totalMc;

            $totalMcDcRun = $item['dakongRun'] + $item['rossoRun'] + $item['mekanikRun'] + $item['lonatiRun'];
            $totalDakongRun  += $item['dakongRun'];
            $totalThsRun  += $item['mekanikRun'];
            $totalRossoRun  += $item['rossoRun'];
            $totalLonRun  += $item['lonatiRun'];
            $totalDcRun += $totalMcDcRun;

            $totalStockCylDk += $item['stockCylDk'];
            $totalStockCylThs += $item['stockCylThs'];
            $totalStockCylRosso += $item['stockCylRosso'];

            $totalCjRun += $item['cjRun'];
            $totalMjRun +=  $item['mjRun'];
            $ttlMcDcRunperPU = $item['cjRun'] + $item['mjRun'];
            $totalMcDcRunperPU +=  $ttlMcDcRunperPU;


            $sheet->setCellValue('A' . $rowCC120, $key)
                ->setCellValue('C' . $rowCC120, $item['dakong'])
                ->setCellValue('D' . $rowCC120, $item['rosso'])
                ->setCellValue('E' . $rowCC120, $item['mekanik'])
                ->setCellValue('F' . $rowCC120, $item['lonati'])
                ->setCellValue('B' . $rowCC120, "0")
                ->setCellValue('G' . $rowCC120, $totalMc);
            $cellCoordinates = ['A' . $rowCC120, 'C' . $rowCC120, 'D' . $rowCC120, 'F' . $rowCC120, 'G' . $rowCC120, 'B' . $rowCC120, 'E' . $rowCC120,];
            foreach ($cellCoordinates as $cellCoordinate) {
                $sheet->getStyle($cellCoordinate)->applyFromArray([
                    'borders' => [
                        'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                    ],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
            }
            // total mesin BC
            $sheet->setCellValue('A' . $totalCC120Row, 'TOTAL Child COMP N 120 + N120 SP')
                ->setCellValue('C' . $totalCC120Row, $totalDakong)
                ->setCellValue('D' . $totalCC120Row, $totalRosso)
                ->setCellValue('E' . $totalCC120Row, $totalThs)
                ->setCellValue('G' . $totalCC120Row, $totalDc)
                ->setCellValue('F' . $totalCC120Row, $totalLon)
                ->setCellValue('H' . $totalCC120Row, $totalDakongRun)
                ->setCellValue('I' . $totalCC120Row, $totalRossoRun)
                ->setCellValue('J' . $totalCC120Row, $totalThsRun)
                ->setCellValue('K' . $totalCC120Row, $totalLonRun)
                ->setCellValue('M' . $totalCC120Row, $totalDcRun);
            $boldStyle = ['A' . $totalCC120Row, 'C' . $totalCC120Row, 'B' . $totalCC120Row, 'D' . $totalCC120Row, 'E' . $totalCC120Row, 'F' . $totalCC120Row,  'H' . $totalCC120Row, 'I' . $totalCC120Row, 'J' . $totalCC120Row, 'K' . $totalCC120Row, 'M' . $totalCC120Row, 'L' . $totalCC120Row, 'N' . $totalCC120Row, 'O' . $totalCC120Row, 'P' . $totalCC120Row, 'Q' . $totalCC120Row,  'R' . $totalCC120Row, 'S' . $totalCC120Row, 'T' . $totalCC120Row, 'V' . $totalCC120Row, 'W' . $totalCC120Row, 'X' . $totalCC120Row,];
            foreach ($boldStyle as $bs) {
                $sheet->getStyle($bs)->applyFromArray([
                    'font' => ['bold' => true],
                    'borders' => [
                        'outline' => ['borderStyle' => Border::BORDER_THICK, 'color' => ['rgb' => '000000']],
                    ],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
            }
            // totalRunningMc
            $sheet
                ->setCellValue('H' . $rowCC120, $item['dakongRun'])
                ->setCellValue('I' . $rowCC120, $item['rossoRun'])
                ->setCellValue('J' . $rowCC120, $item['mekanikRun'])
                ->setCellValue('K' . $rowCC120, $item['lonatiRun'])
                ->setCellValue('M' . $rowCC120, $totalMcDcRun);
            $cellCoordinates = ['H' . $rowCC120, 'I' . $rowCC120, 'J' . $rowCC120, 'K' . $rowCC120,  'N' . $rowCC120, 'L' . $rowCC120,  'O' . $rowCC120,  'P' . $rowCC120,  'Q' . $rowCC120,];
            foreach ($cellCoordinates as $cellCoordinate) {
                $sheet->getStyle($cellCoordinate)->applyFromArray([
                    'borders' => [
                        'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                    ],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],

                ]);
                // stockCylinderBc
                $sheet
                    ->setCellValue('P' . $rowCC120, $item['stockCylDk'])
                    ->setCellValue('Q' . $rowCC120, $item['stockCylThs'])
                    ->setCellValue('R' . $rowCC120, $item['stockCylRosso'])
                    ->setCellValue('P' . $totalCC120Row, $totalStockCylDk)
                    ->setCellValue('Q' . $totalCC120Row, $totalStockCylThs)
                    ->setCellValue('R' . $totalCC120Row, $totalStockCylRosso);

                $cellCoordinates = ['P' . $rowCC120, 'Q' . $rowCC120, 'J' . $rowCC120, 'R' . $rowCC120,];
                foreach ($cellCoordinates as $cellCoordinate) {
                    $sheet->getStyle($cellCoordinate)->applyFromArray([
                        'borders' => [
                            'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                        ],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],

                    ]);
                }
                // totalRunningMc PU &stockCYl
                $sheet
                    ->setCellValue('S' . $totalCC120Row, $totalCjRun)
                    ->setCellValue('T' . $totalCC120Row, $totalMjRun)
                    ->setCellValue('U' . $totalCC120Row, $totalMcDcRunperPU)
                    ->setCellValue('S' . $rowCC120, $item['cjRun'])
                    ->setCellValue('T' . $rowCC120, $item['mjRun'])
                    ->setCellValue('U' . $rowCC120, $ttlMcDcRunperPU);
                $cellCoordinates = ['S' . $rowCC120, 'T' . $rowCC120, 'U' . $rowCC120,];
                foreach ($cellCoordinates as $cellCoordinate) {
                    $sheet->getStyle($cellCoordinate)->applyFromArray([
                        'borders' => [
                            'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                        ],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],

                    ]);
                }
                // WEEK & Capasity Produksi
                $sheet
                    ->setCellValue('V' . $totalCC120Row, $week)
                    ->setCellValue('W' . $totalCC120Row, $minus3)
                    ->setCellValue('X' . $totalCC120Row, $po28hari)
                    ->setCellValue('V' . $rowCC120, "0")
                    ->setCellValue('W' . $rowCC120, "0")
                    ->setCellValue('X' . $rowCC120, "0");
                $cellCoordinates = ['V' . $rowCC120, 'W' . $rowCC120, 'X' . $rowCC120,];
                foreach ($cellCoordinates as $cellCoordinate) {
                    $sheet->getStyle($cellCoordinate)->applyFromArray([
                        'borders' => [
                            'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                        ],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],

                    ]);
                }

                $totalStyle = ['U' . $rowCC120, 'M' . $rowCC120, 'G' . $rowCC120, 'G' . $totalCC120Row, 'M' . $totalCC120Row, 'U' . $totalCC120Row,];
                foreach ($totalStyle  as $styleTotal) {
                    $sheet->getStyle($styleTotal)->applyFromArray([
                        'borders' => [
                            'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                        ],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'FFA500'], // Orange color
                        ],
                    ]);
                }
            }

            $rowCC120++;
        }

        //LADY COMP
        foreach ($ladyComp as $key => $item) {
            $totalMc = $item['dakong'] + $item['rosso'] + $item['mekanik'] + $item['lonati'];
            $totalDakong  += $item['dakong'];
            $totalThs  += $item['mekanik'];
            $totalRosso  += $item['rosso'];
            $totalLon  += $item['lonati'];
            $totalDc  += $totalMc;

            $totalMcDcRun = $item['dakongRun'] + $item['rossoRun'] + $item['mekanikRun'] + $item['lonatiRun'];
            $totalDakongRun  += $item['dakongRun'];
            $totalThsRun  += $item['mekanikRun'];
            $totalRossoRun  += $item['rossoRun'];
            $totalLonRun  += $item['lonatiRun'];
            $totalDcRun += $totalMcDcRun;

            $totalStockCylDk += $item['stockCylDk'];
            $totalStockCylThs += $item['stockCylThs'];
            $totalStockCylRosso += $item['stockCylRosso'];

            $totalCjRun += $item['cjRun'];
            $totalMjRun +=  $item['mjRun'];
            $ttlMcDcRunperPU = $item['cjRun'] + $item['mjRun'];
            $totalMcDcRunperPU +=  $ttlMcDcRunperPU;

            $mergerow = $rowLC + 4;
            $sheet //->mergeCells('A' . $rowLC . ':A' . $mergerow)
                ->setCellValue('A' . $rowLC, $key)
                ->setCellValue('C' . $rowLC, $item['dakong'])
                //  ->mergeCells('C' . $rowLC . ':C' . $mergerow - 1)
                ->setCellValue('D' . $rowLC, $item['rosso'])
                //->mergeCells('D' . $rowLC . ':D' . $mergerow)
                ->setCellValue('E' . $rowLC, $item['mekanik'])
                //->mergeCells('E' . $rowLC . ':E' . $mergerow)
                ->setCellValue('F' . $rowLC, $item['lonati'])
                //->mergeCells('F ' . $rowLC . ':F ' . $mergerow)
                //  ->mergeCells('B' . $rowLC . ':B' . $mergerow)
                ->setCellValue('B' . $rowLC, "0")
                //->mergeCells('G' . $rowLC . ':G' . $mergerow)
                ->setCellValue('G' . $rowLC, $totalMc);
            $cellCoordinates = ['A' . $rowLC, 'C' . $rowLC, 'D' . $rowLC, 'F' . $rowLC, 'G' . $rowLC, 'B' . $rowLC, 'E' . $rowLC,];
            foreach ($cellCoordinates as $cellCoordinate) {
                $sheet->getStyle($cellCoordinate)->applyFromArray([
                    'borders' => [
                        'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                    ],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
            }
            // total mesin BC
            $sheet->setCellValue('A' . $totalLCRow, 'TOTAL CHILD COMP N 120 + N120 SP')
                ->setCellValue('C' . $totalLCRow, $totalDakong)
                ->setCellValue('D' . $totalLCRow, $totalRosso)
                ->setCellValue('E' . $totalLCRow, $totalThs)
                ->setCellValue('G' . $totalLCRow, $totalDc)
                ->setCellValue('F' . $totalLCRow, $totalLon)
                ->setCellValue('H' . $totalLCRow, $totalDakongRun)
                ->setCellValue('I' . $totalLCRow, $totalRossoRun)
                ->setCellValue('J' . $totalLCRow, $totalThsRun)
                ->setCellValue('K' . $totalLCRow, $totalLonRun)
                ->setCellValue('M' . $totalLCRow, $totalDcRun);
            $boldStyle = ['A' . $totalLCRow, 'C' . $totalLCRow, 'B' . $totalLCRow, 'D' . $totalLCRow, 'E' . $totalLCRow, 'F' . $totalLCRow,  'H' . $totalLCRow, 'I' . $totalLCRow, 'J' . $totalLCRow, 'K' . $totalLCRow, 'M' . $totalLCRow, 'L' . $totalLCRow, 'N' . $totalLCRow, 'O' . $totalLCRow, 'P' . $totalLCRow, 'Q' . $totalLCRow,  'R' . $totalLCRow, 'S' . $totalLCRow, 'T' . $totalLCRow, 'V' . $totalLCRow, 'W' . $totalLCRow, 'X' . $totalLCRow,];
            foreach ($boldStyle as $bs) {
                $sheet->getStyle($bs)->applyFromArray([
                    'font' => ['bold' => true],
                    'borders' => [
                        'outline' => ['borderStyle' => Border::BORDER_THICK, 'color' => ['rgb' => '000000']],
                    ],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
            }
            // totalRunningMc
            $sheet
                ->setCellValue('H' . $rowLC, $item['dakongRun'])
                ->setCellValue('I' . $rowLC, $item['rossoRun'])
                ->setCellValue('J' . $rowLC, $item['mekanikRun'])
                ->setCellValue('K' . $rowLC, $item['lonatiRun'])
                ->setCellValue('M' . $rowLC, $totalMcDcRun);
            $cellCoordinates = ['H' . $rowLC, 'I' . $rowLC, 'J' . $rowLC, 'K' . $rowLC,  'N' . $rowLC, 'L' . $rowLC,  'O' . $rowLC,  'P' . $rowLC,  'Q' . $rowLC,];
            foreach ($cellCoordinates as $cellCoordinate) {
                $sheet->getStyle($cellCoordinate)->applyFromArray([
                    'borders' => [
                        'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                    ],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],

                ]);
                // stockCylinderBc
                $sheet
                    ->setCellValue('P' . $rowLC, $item['stockCylDk'])
                    ->setCellValue('Q' . $rowLC, $item['stockCylThs'])
                    ->setCellValue('R' . $rowLC, $item['stockCylRosso'])
                    ->setCellValue('P' . $totalLCRow, $totalStockCylDk)
                    ->setCellValue('Q' . $totalLCRow, $totalStockCylThs)
                    ->setCellValue('R' . $totalLCRow, $totalStockCylRosso);

                $cellCoordinates = ['P' . $rowLC, 'Q' . $rowLC, 'J' . $rowLC, 'R' . $rowLC,];
                foreach ($cellCoordinates as $cellCoordinate) {
                    $sheet->getStyle($cellCoordinate)->applyFromArray([
                        'borders' => [
                            'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                        ],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],

                    ]);
                }
                // totalRunningMc PU &stockCYl
                $sheet
                    ->setCellValue('S' . $totalLCRow, $totalCjRun)
                    ->setCellValue('T' . $totalLCRow, $totalMjRun)
                    ->setCellValue('U' . $totalLCRow, $totalMcDcRunperPU)
                    ->setCellValue('S' . $rowLC, $item['cjRun'])
                    ->setCellValue('T' . $rowLC, $item['mjRun'])
                    ->setCellValue('U' . $rowLC, $ttlMcDcRunperPU);
                $cellCoordinates = ['S' . $rowLC, 'T' . $rowLC, 'U' . $rowLC,];
                foreach ($cellCoordinates as $cellCoordinate) {
                    $sheet->getStyle($cellCoordinate)->applyFromArray([
                        'borders' => [
                            'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                        ],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],

                    ]);
                }
                // WEEK & Capasity Produksi
                $sheet
                    ->setCellValue('V' . $totalLCRow, $week)
                    ->setCellValue('W' . $totalLCRow, $minus3)
                    ->setCellValue('X' . $totalLCRow, $po28hari)
                    ->setCellValue('V' . $rowLC, "0")
                    ->setCellValue('W' . $rowLC, "0")
                    ->setCellValue('X' . $rowLC, "0");
                $cellCoordinates = ['V' . $rowLC, 'W' . $rowLC, 'X' . $rowLC,];
                foreach ($cellCoordinates as $cellCoordinate) {
                    $sheet->getStyle($cellCoordinate)->applyFromArray([
                        'borders' => [
                            'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                        ],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],

                    ]);
                }

                $totalStyle = ['U' . $rowLC, 'M' . $rowLC, 'G' . $rowLC, 'G' . $totalLCRow, 'M' . $totalLCRow, 'U' . $totalLCRow,];
                foreach ($totalStyle  as $styleTotal) {
                    $sheet->getStyle($styleTotal)->applyFromArray([
                        'borders' => [
                            'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                        ],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'FFA500'], // Orange color
                        ],
                    ]);
                }
            }

            $rowLC++;
        }

        //MENS COMP N168
        foreach ($mensComp as $key => $item) {
            $totalMc = $item['dakong'] + $item['rosso'] + $item['mekanik'] + $item['lonati'];
            $totalDakong  += $item['dakong'];
            $totalThs  += $item['mekanik'];
            $totalRosso  += $item['rosso'];
            $totalLon  += $item['lonati'];
            $totalDc  += $totalMc;

            $totalMcDcRun = $item['dakongRun'] + $item['rossoRun'] + $item['mekanikRun'] + $item['lonatiRun'];
            $totalDakongRun  += $item['dakongRun'];
            $totalThsRun  += $item['mekanikRun'];
            $totalRossoRun  += $item['rossoRun'];
            $totalLonRun  += $item['lonatiRun'];
            $totalDcRun += $totalMcDcRun;

            $totalStockCylDk += $item['stockCylDk'];
            $totalStockCylThs += $item['stockCylThs'];
            $totalStockCylRosso += $item['stockCylRosso'];

            $totalCjRun += $item['cjRun'];
            $totalMjRun +=  $item['mjRun'];
            $ttlMcDcRunperPU = $item['cjRun'] + $item['mjRun'];
            $totalMcDcRunperPU +=  $ttlMcDcRunperPU;


            $sheet->setCellValue('A' . $rowMC168, $key)
                ->setCellValue('C' . $rowMC168, $item['dakong'])
                ->setCellValue('D' . $rowMC168, $item['rosso'])
                ->setCellValue('E' . $rowMC168, $item['mekanik'])
                ->setCellValue('F' . $rowMC168, $item['lonati'])
                ->setCellValue('B' . $rowMC168, "0")
                ->setCellValue('G' . $rowMC168, $totalMc);
            $cellCoordinates = ['A' . $rowMC168, 'C' . $rowMC168, 'D' . $rowMC168, 'F' . $rowMC168, 'G' . $rowMC168, 'B' . $rowMC168, 'E' . $rowMC168,];
            foreach ($cellCoordinates as $cellCoordinate) {
                $sheet->getStyle($cellCoordinate)->applyFromArray([
                    'borders' => [
                        'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                    ],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
            }
            // total mesin BC
            $sheet->setCellValue('A' . $totalMC168Row, 'TOTAL MENS COMP N168 + N168 SP')
                ->setCellValue('C' . $totalMC168Row, $totalDakong)
                ->setCellValue('D' . $totalMC168Row, $totalRosso)
                ->setCellValue('E' . $totalMC168Row, $totalThs)
                ->setCellValue('G' . $totalMC168Row, $totalDc)
                ->setCellValue('F' . $totalMC168Row, $totalLon)
                ->setCellValue('H' . $totalMC168Row, $totalDakongRun)
                ->setCellValue('I' . $totalMC168Row, $totalRossoRun)
                ->setCellValue('J' . $totalMC168Row, $totalThsRun)
                ->setCellValue('K' . $totalMC168Row, $totalLonRun)
                ->setCellValue('M' . $totalMC168Row, $totalDcRun);
            $boldStyle = ['A' . $totalMC168Row, 'C' . $totalMC168Row, 'B' . $totalMC168Row, 'D' . $totalMC168Row, 'E' . $totalMC168Row, 'F' . $totalMC168Row,  'H' . $totalMC168Row, 'I' . $totalMC168Row, 'J' . $totalMC168Row, 'K' . $totalMC168Row, 'M' . $totalMC168Row, 'L' . $totalMC168Row, 'N' . $totalMC168Row, 'O' . $totalMC168Row, 'P' . $totalMC168Row, 'Q' . $totalMC168Row,  'R' . $totalMC168Row, 'S' . $totalMC168Row, 'T' . $totalMC168Row, 'V' . $totalMC168Row, 'W' . $totalMC168Row, 'X' . $totalMC168Row,];
            foreach ($boldStyle as $bs) {
                $sheet->getStyle($bs)->applyFromArray([
                    'font' => ['bold' => true],
                    'borders' => [
                        'outline' => ['borderStyle' => Border::BORDER_THICK, 'color' => ['rgb' => '000000']],
                    ],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
            }
            // totalRunningMc
            $sheet
                ->setCellValue('H' . $rowMC168, $item['dakongRun'])
                ->setCellValue('I' . $rowMC168, $item['rossoRun'])
                ->setCellValue('J' . $rowMC168, $item['mekanikRun'])
                ->setCellValue('K' . $rowMC168, $item['lonatiRun'])
                ->setCellValue('M' . $rowMC168, $totalMcDcRun);
            $cellCoordinates = ['H' . $rowMC168, 'I' . $rowMC168, 'J' . $rowMC168, 'K' . $rowMC168,  'N' . $rowMC168, 'L' . $rowMC168,  'O' . $rowMC168,  'P' . $rowMC168,  'Q' . $rowMC168,];
            foreach ($cellCoordinates as $cellCoordinate) {
                $sheet->getStyle($cellCoordinate)->applyFromArray([
                    'borders' => [
                        'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                    ],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],

                ]);
                // stockCylinderBc
                $sheet
                    ->setCellValue('P' . $rowMC168, $item['stockCylDk'])
                    ->setCellValue('Q' . $rowMC168, $item['stockCylThs'])
                    ->setCellValue('R' . $rowMC168, $item['stockCylRosso'])
                    ->setCellValue('P' . $totalMC168Row, $totalStockCylDk)
                    ->setCellValue('Q' . $totalMC168Row, $totalStockCylThs)
                    ->setCellValue('R' . $totalMC168Row, $totalStockCylRosso);

                $cellCoordinates = ['P' . $rowMC168, 'Q' . $rowMC168, 'J' . $rowMC168, 'R' . $rowMC168,];
                foreach ($cellCoordinates as $cellCoordinate) {
                    $sheet->getStyle($cellCoordinate)->applyFromArray([
                        'borders' => [
                            'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                        ],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],

                    ]);
                }
                // totalRunningMc PU &stockCYl
                $sheet
                    ->setCellValue('S' . $totalMC168Row, $totalCjRun)
                    ->setCellValue('T' . $totalMC168Row, $totalMjRun)
                    ->setCellValue('U' . $totalMC168Row, $totalMcDcRunperPU)
                    ->setCellValue('S' . $rowMC168, $item['cjRun'])
                    ->setCellValue('T' . $rowMC168, $item['mjRun'])
                    ->setCellValue('U' . $rowMC168, $ttlMcDcRunperPU);
                $cellCoordinates = ['S' . $rowMC168, 'T' . $rowMC168, 'U' . $rowMC168,];
                foreach ($cellCoordinates as $cellCoordinate) {
                    $sheet->getStyle($cellCoordinate)->applyFromArray([
                        'borders' => [
                            'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                        ],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],

                    ]);
                }
                // WEEK & Capasity Produksi
                $sheet
                    ->setCellValue('V' . $totalMC168Row, $week)
                    ->setCellValue('W' . $totalMC168Row, $minus3)
                    ->setCellValue('X' . $totalMC168Row, $po28hari)
                    ->setCellValue('V' . $rowMC168, "0")
                    ->setCellValue('W' . $rowMC168, "0")
                    ->setCellValue('X' . $rowMC168, "0");
                $cellCoordinates = ['V' . $rowMC168, 'W' . $rowMC168, 'X' . $rowMC168,];
                foreach ($cellCoordinates as $cellCoordinate) {
                    $sheet->getStyle($cellCoordinate)->applyFromArray([
                        'borders' => [
                            'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                        ],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],

                    ]);
                }

                $totalStyle = ['U' . $rowMC168, 'M' . $rowMC168, 'G' . $rowMC168, 'G' . $totalMC168Row, 'M' . $totalMC168Row, 'U' . $totalMC168Row,];
                foreach ($totalStyle  as $styleTotal) {
                    $sheet->getStyle($styleTotal)->applyFromArray([
                        'borders' => [
                            'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                        ],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'FFA500'], // Orange color
                        ],
                    ]);
                }
            }

            $rowMC168++;
        }

        //MENS COMP N200
        foreach ($mensComp200 as $key => $item) {
            $totalMc = $item['dakong'] + $item['rosso'] + $item['mekanik'] + $item['lonati'];
            $totalDakong  += $item['dakong'];
            $totalThs  += $item['mekanik'];
            $totalRosso  += $item['rosso'];
            $totalLon  += $item['lonati'];
            $totalDc  += $totalMc;

            $totalMcDcRun = $item['dakongRun'] + $item['rossoRun'] + $item['mekanikRun'] + $item['lonatiRun'];
            $totalDakongRun  += $item['dakongRun'];
            $totalThsRun  += $item['mekanikRun'];
            $totalRossoRun  += $item['rossoRun'];
            $totalLonRun  += $item['lonatiRun'];
            $totalDcRun += $totalMcDcRun;

            $totalStockCylDk += $item['stockCylDk'];
            $totalStockCylThs += $item['stockCylThs'];
            $totalStockCylRosso += $item['stockCylRosso'];

            $totalCjRun += $item['cjRun'];
            $totalMjRun +=  $item['mjRun'];
            $ttlMcDcRunperPU = $item['cjRun'] + $item['mjRun'];
            $totalMcDcRunperPU +=  $ttlMcDcRunperPU;


            $sheet->setCellValue('A' . $rowMC200, $key)
                ->setCellValue('C' . $rowMC200, $item['dakong'])
                ->setCellValue('D' . $rowMC200, $item['rosso'])
                ->setCellValue('E' . $rowMC200, $item['mekanik'])
                ->setCellValue('F' . $rowMC200, $item['lonati'])
                ->setCellValue('B' . $rowMC200, "0")
                ->setCellValue('G' . $rowMC200, $totalMc);
            $cellCoordinates = ['A' . $rowMC200, 'C' . $rowMC200, 'D' . $rowMC200, 'F' . $rowMC200, 'G' . $rowMC200, 'B' . $rowMC200, 'E' . $rowMC200,];
            foreach ($cellCoordinates as $cellCoordinate) {
                $sheet->getStyle($cellCoordinate)->applyFromArray([
                    'borders' => [
                        'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                    ],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
            }

            // totalRunningMc
            $sheet
                ->setCellValue('H' . $rowMC200, $item['dakongRun'])
                ->setCellValue('I' . $rowMC200, $item['rossoRun'])
                ->setCellValue('J' . $rowMC200, $item['mekanikRun'])
                ->setCellValue('K' . $rowMC200, $item['lonatiRun'])
                ->setCellValue('M' . $rowMC200, $totalMcDcRun);
            $cellCoordinates = ['H' . $rowMC200, 'I' . $rowMC200, 'J' . $rowMC200, 'K' . $rowMC200,  'N' . $rowMC200, 'L' . $rowMC200,  'O' . $rowMC200,  'P' . $rowMC200,  'Q' . $rowMC200,];
            foreach ($cellCoordinates as $cellCoordinate) {
                $sheet->getStyle($cellCoordinate)->applyFromArray([
                    'borders' => [
                        'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                    ],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],

                ]);
                // stockCylinderBc
                $sheet
                    ->setCellValue('P' . $rowMC200, $item['stockCylDk'])
                    ->setCellValue('Q' . $rowMC200, $item['stockCylThs'])
                    ->setCellValue('R' . $rowMC200, $item['stockCylRosso']);

                $cellCoordinates = ['P' . $rowMC200, 'Q' . $rowMC200, 'J' . $rowMC200, 'R' . $rowMC200,];
                foreach ($cellCoordinates as $cellCoordinate) {
                    $sheet->getStyle($cellCoordinate)->applyFromArray([
                        'borders' => [
                            'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                        ],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],

                    ]);
                }
                // totalRunningMc PU &stockCYl
                $sheet
                    ->setCellValue('S' . $rowMC200, $item['cjRun'])
                    ->setCellValue('T' . $rowMC200, $item['mjRun'])
                    ->setCellValue('U' . $rowMC200, $ttlMcDcRunperPU);
                $cellCoordinates = ['S' . $rowMC200, 'T' . $rowMC200, 'U' . $rowMC200,];
                foreach ($cellCoordinates as $cellCoordinate) {
                    $sheet->getStyle($cellCoordinate)->applyFromArray([
                        'borders' => [
                            'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                        ],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],

                    ]);
                }
                // WEEK & Capasity Produksi
                $sheet
                    ->setCellValue('V' . $rowMC200, "0")
                    ->setCellValue('W' . $rowMC200, "0")
                    ->setCellValue('X' . $rowMC200, "0");
                $cellCoordinates = ['V' . $rowMC200, 'W' . $rowMC200, 'X' . $rowMC200,];
                foreach ($cellCoordinates as $cellCoordinate) {
                    $sheet->getStyle($cellCoordinate)->applyFromArray([
                        'borders' => [
                            'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                        ],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],

                    ]);
                }

                $totalStyle = ['U' . $rowMC200, 'M' . $rowMC200, 'G' . $rowMC200,];
                foreach ($totalStyle  as $styleTotal) {
                    $sheet->getStyle($styleTotal)->applyFromArray([
                        'borders' => [
                            'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                        ],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'FFA500'], // Orange color
                        ],
                    ]);
                }
            }

            $rowMC200++;
        }

        //CHILD COMP N132
        foreach ($childComp132 as $key => $item) {
            $totalMc = $item['dakong'] + $item['rosso'] + $item['mekanik'] + $item['lonati'];
            $totalDakong  += $item['dakong'];
            $totalThs  += $item['mekanik'];
            $totalRosso  += $item['rosso'];
            $totalLon  += $item['lonati'];
            $totalDc  += $totalMc;

            $totalMcDcRun = $item['dakongRun'] + $item['rossoRun'] + $item['mekanikRun'] + $item['lonatiRun'];
            $totalDakongRun  += $item['dakongRun'];
            $totalThsRun  += $item['mekanikRun'];
            $totalRossoRun  += $item['rossoRun'];
            $totalLonRun  += $item['lonatiRun'];
            $totalDcRun += $totalMcDcRun;

            $totalStockCylDk += $item['stockCylDk'];
            $totalStockCylThs += $item['stockCylThs'];
            $totalStockCylRosso += $item['stockCylRosso'];

            $totalCjRun += $item['cjRun'];
            $totalMjRun +=  $item['mjRun'];
            $ttlMcDcRunperPU = $item['cjRun'] + $item['mjRun'];
            $totalMcDcRunperPU +=  $ttlMcDcRunperPU;


            $sheet->setCellValue('A' . $rowCC132, $key)
                ->setCellValue('C' . $rowCC132, $item['dakong'])
                ->setCellValue('D' . $rowCC132, $item['rosso'])
                ->setCellValue('E' . $rowCC132, $item['mekanik'])
                ->setCellValue('F' . $rowCC132, $item['lonati'])
                ->setCellValue('B' . $rowCC132, "0")
                ->setCellValue('G' . $rowCC132, $totalMc);
            $cellCoordinates = ['A' . $rowCC132, 'C' . $rowCC132, 'D' . $rowCC132, 'F' . $rowCC132, 'G' . $rowCC132, 'B' . $rowCC132, 'E' . $rowCC132,];
            foreach ($cellCoordinates as $cellCoordinate) {
                $sheet->getStyle($cellCoordinate)->applyFromArray([
                    'borders' => [
                        'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                    ],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
            }

            // totalRunningMc
            $sheet
                ->setCellValue('H' . $rowCC132, $item['dakongRun'])
                ->setCellValue('I' . $rowCC132, $item['rossoRun'])
                ->setCellValue('J' . $rowCC132, $item['mekanikRun'])
                ->setCellValue('K' . $rowCC132, $item['lonatiRun'])
                ->setCellValue('M' . $rowCC132, $totalMcDcRun);
            $cellCoordinates = ['H' . $rowCC132, 'I' . $rowCC132, 'J' . $rowCC132, 'K' . $rowCC132,  'N' . $rowCC132, 'L' . $rowCC132,  'O' . $rowCC132,  'P' . $rowCC132,  'Q' . $rowCC132,];
            foreach ($cellCoordinates as $cellCoordinate) {
                $sheet->getStyle($cellCoordinate)->applyFromArray([
                    'borders' => [
                        'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                    ],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],

                ]);
                // stockCylinderBc
                $sheet
                    ->setCellValue('P' . $rowCC132, $item['stockCylDk'])
                    ->setCellValue('Q' . $rowCC132, $item['stockCylThs'])
                    ->setCellValue('R' . $rowCC132, $item['stockCylRosso']);

                $cellCoordinates = ['P' . $rowCC132, 'Q' . $rowCC132, 'J' . $rowCC132, 'R' . $rowCC132,];
                foreach ($cellCoordinates as $cellCoordinate) {
                    $sheet->getStyle($cellCoordinate)->applyFromArray([
                        'borders' => [
                            'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                        ],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],

                    ]);
                }
                // totalRunningMc PU &stockCYl
                $sheet
                    ->setCellValue('S' . $rowCC132, $item['cjRun'])
                    ->setCellValue('T' . $rowCC132, $item['mjRun'])
                    ->setCellValue('U' . $rowCC132, $ttlMcDcRunperPU);
                $cellCoordinates = ['S' . $rowCC132, 'T' . $rowCC132, 'U' . $rowCC132,];
                foreach ($cellCoordinates as $cellCoordinate) {
                    $sheet->getStyle($cellCoordinate)->applyFromArray([
                        'borders' => [
                            'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                        ],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],

                    ]);
                }
                // WEEK & Capasity Produksi
                $sheet
                    ->setCellValue('V' . $rowCC132, "0")
                    ->setCellValue('W' . $rowCC132, "0")
                    ->setCellValue('X' . $rowCC132, "0");
                $cellCoordinates = ['V' . $rowCC132, 'W' . $rowCC132, 'X' . $rowCC132,];
                foreach ($cellCoordinates as $cellCoordinate) {
                    $sheet->getStyle($cellCoordinate)->applyFromArray([
                        'borders' => [
                            'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                        ],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],

                    ]);
                }

                $totalStyle = ['U' . $rowCC132, 'M' . $rowCC132, 'G' . $rowCC132,];
                foreach ($totalStyle  as $styleTotal) {
                    $sheet->getStyle($styleTotal)->applyFromArray([
                        'borders' => [
                            'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                        ],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'FFA500'], // Orange color
                        ],
                    ]);
                }
            }

            $rowCC132++;
        }

        //MENS COMP N156
        foreach ($mensComp156 as $key => $item) {
            $totalMc = $item['dakong'] + $item['rosso'] + $item['mekanik'] + $item['lonati'];
            $totalDakong  += $item['dakong'];
            $totalThs  += $item['mekanik'];
            $totalRosso  += $item['rosso'];
            $totalLon  += $item['lonati'];
            $totalDc  += $totalMc;

            $totalMcDcRun = $item['dakongRun'] + $item['rossoRun'] + $item['mekanikRun'] + $item['lonatiRun'];
            $totalDakongRun  += $item['dakongRun'];
            $totalThsRun  += $item['mekanikRun'];
            $totalRossoRun  += $item['rossoRun'];
            $totalLonRun  += $item['lonatiRun'];
            $totalDcRun += $totalMcDcRun;

            $totalStockCylDk += $item['stockCylDk'];
            $totalStockCylThs += $item['stockCylThs'];
            $totalStockCylRosso += $item['stockCylRosso'];

            $totalCjRun += $item['cjRun'];
            $totalMjRun +=  $item['mjRun'];
            $ttlMcDcRunperPU = $item['cjRun'] + $item['mjRun'];
            $totalMcDcRunperPU +=  $ttlMcDcRunperPU;


            $sheet->setCellValue('A' . $rowMC156, $key)
                ->setCellValue('C' . $rowMC156, $item['dakong'])
                ->setCellValue('D' . $rowMC156, $item['rosso'])
                ->setCellValue('E' . $rowMC156, $item['mekanik'])
                ->setCellValue('F' . $rowMC156, $item['lonati'])
                ->setCellValue('B' . $rowMC156, "0")
                ->setCellValue('G' . $rowMC156, $totalMc);
            $cellCoordinates = ['A' . $rowMC156, 'C' . $rowMC156, 'D' . $rowMC156, 'F' . $rowMC156, 'G' . $rowMC156, 'B' . $rowMC156, 'E' . $rowMC156,];
            foreach ($cellCoordinates as $cellCoordinate) {
                $sheet->getStyle($cellCoordinate)->applyFromArray([
                    'borders' => [
                        'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                    ],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
            }

            // totalRunningMc
            $sheet
                ->setCellValue('H' . $rowMC156, $item['dakongRun'])
                ->setCellValue('I' . $rowMC156, $item['rossoRun'])
                ->setCellValue('J' . $rowMC156, $item['mekanikRun'])
                ->setCellValue('K' . $rowMC156, $item['lonatiRun'])
                ->setCellValue('M' . $rowMC156, $totalMcDcRun);
            $cellCoordinates = ['H' . $rowMC156, 'I' . $rowMC156, 'J' . $rowMC156, 'K' . $rowMC156,  'N' . $rowMC156, 'L' . $rowMC156,  'O' . $rowMC156,  'P' . $rowMC156,  'Q' . $rowMC156,];
            foreach ($cellCoordinates as $cellCoordinate) {
                $sheet->getStyle($cellCoordinate)->applyFromArray([
                    'borders' => [
                        'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                    ],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],

                ]);
                // stockCylinderBc
                $sheet
                    ->setCellValue('P' . $rowMC156, $item['stockCylDk'])
                    ->setCellValue('Q' . $rowMC156, $item['stockCylThs'])
                    ->setCellValue('R' . $rowMC156, $item['stockCylRosso']);

                $cellCoordinates = ['P' . $rowMC156, 'Q' . $rowMC156, 'J' . $rowMC156, 'R' . $rowMC156,];
                foreach ($cellCoordinates as $cellCoordinate) {
                    $sheet->getStyle($cellCoordinate)->applyFromArray([
                        'borders' => [
                            'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                        ],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],

                    ]);
                }
                // totalRunningMc PU &stockCYl
                $sheet
                    ->setCellValue('S' . $rowMC156, $item['cjRun'])
                    ->setCellValue('T' . $rowMC156, $item['mjRun'])
                    ->setCellValue('U' . $rowMC156, $ttlMcDcRunperPU);
                $cellCoordinates = ['S' . $rowMC156, 'T' . $rowMC156, 'U' . $rowMC156,];
                foreach ($cellCoordinates as $cellCoordinate) {
                    $sheet->getStyle($cellCoordinate)->applyFromArray([
                        'borders' => [
                            'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                        ],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],

                    ]);
                }
                // WEEK & Capasity Produksi
                $sheet
                    ->setCellValue('V' . $rowMC156, "0")
                    ->setCellValue('W' . $rowMC156, "0")
                    ->setCellValue('X' . $rowMC156, "0");
                $cellCoordinates = ['V' . $rowMC156, 'W' . $rowMC156, 'X' . $rowMC156,];
                foreach ($cellCoordinates as $cellCoordinate) {
                    $sheet->getStyle($cellCoordinate)->applyFromArray([
                        'borders' => [
                            'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                        ],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],

                    ]);
                }

                $totalStyle = ['U' . $rowMC156, 'M' . $rowMC156, 'G' . $rowMC156,];
                foreach ($totalStyle  as $styleTotal) {
                    $sheet->getStyle($styleTotal)->applyFromArray([
                        'borders' => [
                            'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                        ],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'FFA500'], // Orange color
                        ],
                    ]);
                }
            }

            $rowMC156++;
        }

        // total SINGLE CYLINDER COMP
        $sheet->setCellValue('A' . $totalSCCompRow, 'TOTAL SINGLE CYLINDER COMP')
            ->setCellValue('C' . $totalSCCompRow, $totalDakong)
            ->setCellValue('D' . $totalSCCompRow, $totalRosso)
            ->setCellValue('E' . $totalSCCompRow, $totalThs)
            ->setCellValue('G' . $totalSCCompRow, $totalDc)
            ->setCellValue('F' . $totalSCCompRow, $totalLon)
            ->setCellValue('H' . $totalSCCompRow, $totalDakongRun)
            ->setCellValue('I' . $totalSCCompRow, $totalRossoRun)
            ->setCellValue('J' . $totalSCCompRow, $totalThsRun)
            ->setCellValue('K' . $totalSCCompRow, $totalLonRun)
            ->setCellValue('M' . $totalSCCompRow, $totalDcRun);
        $boldStyle = ['A' . $totalSCCompRow, 'C' . $totalSCCompRow, 'B' . $totalSCCompRow, 'D' . $totalSCCompRow, 'E' . $totalSCCompRow, 'F' . $totalSCCompRow,  'H' . $totalSCCompRow, 'I' . $totalSCCompRow, 'J' . $totalSCCompRow, 'K' . $totalSCCompRow, 'M' . $totalSCCompRow, 'L' . $totalSCCompRow, 'N' . $totalSCCompRow, 'O' . $totalSCCompRow, 'P' . $totalSCCompRow, 'Q' . $totalSCCompRow,  'R' . $totalSCCompRow, 'S' . $totalSCCompRow, 'T' . $totalSCCompRow, 'V' . $totalSCCompRow, 'W' . $totalSCCompRow, 'X' . $totalSCCompRow,];
        foreach ($boldStyle as $bs) {
            $sheet->getStyle($bs)->applyFromArray([
                'font' => ['bold' => true],
                'borders' => [
                    'outline' => ['borderStyle' => Border::BORDER_THICK, 'color' => ['rgb' => '000000']],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
        }
        // download Data
        $writer = new Xlsx($spreadsheet);
        $file_path = WRITEPATH . 'uploads/data.xlsx'; // Lokasi penyimpanan file Excel
        $writer->save($file_path);

        // Unduh file
        return $this->response->download($file_path, null)->setFileName('data.xlsx');
    }
}
