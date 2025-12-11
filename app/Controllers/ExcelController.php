<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Services\orderServices;
use CodeIgniter\HTTP\RequestInterface;
use LengthException;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Week;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\{Border, Alignment, Fill, NumberFormat};
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use DateTime;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\Layout;


class ExcelController extends BaseController
{




    public function __construct()
    {

        if ($this->filters   = ['role' => [session()->get('role') . '']] != session()->get('role')) {
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
        return view('capacity/Sales/index', $data);
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
        // TEST
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
        $totalCC120Row = 12 + $rowCC120;
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

            $sheet->setCellValue('A' . $rowBC, $key)
                ->setCellValue('C' . $rowBC, $item['dakong'])
                ->setCellValue('D' . $rowBC, $item['rosso'])
                ->setCellValue('E' . $rowBC, $item['mekanik'])
                ->setCellValue('F' . $rowBC, $item['lonati'])
                ->setCellValue('B' . $rowBC, "0")

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
                //WEEK & Capasity Produksi
                $sheet
                    ->setCellValue('V' . $totalBCRow, $week)
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
        $weekCol = [];
        $week = [];
        for ($i = 24; $i <= 36; $i++) {
            $weekCol[] = 'V' . $i;
            $week[] = 'WEEK ' . ($i - 23);
        }

        // Atur nilai sel dan gaya dalam satu loop
        foreach ($weekCol as $index => $cellCoordinate) {
            $sheet->setCellValue($cellCoordinate, $week[$index])
                ->getStyle($cellCoordinate)->applyFromArray([
                    'borders' => [
                        'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                    ],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
        }

        //LADY COMP
        // foreach ($ladyComp as $key => $item) {
        //     $totalMc = $item['dakong'] + $item['rosso'] + $item['mekanik'] + $item['lonati'];
        //     $totalDakong  += $item['dakong'];
        //     $totalThs  += $item['mekanik'];
        //     $totalRosso  += $item['rosso'];
        //     $totalLon  += $item['lonati'];
        //     $totalDc  += $totalMc;

        //     $totalMcDcRun = $item['dakongRun'] + $item['rossoRun'] + $item['mekanikRun'] + $item['lonatiRun'];
        //     $totalDakongRun  += $item['dakongRun'];
        //     $totalThsRun  += $item['mekanikRun'];
        //     $totalRossoRun  += $item['rossoRun'];
        //     $totalLonRun  += $item['lonatiRun'];
        //     $totalDcRun += $totalMcDcRun;

        //     $totalStockCylDk += $item['stockCylDk'];
        //     $totalStockCylThs += $item['stockCylThs'];
        //     $totalStockCylRosso += $item['stockCylRosso'];

        //     $totalCjRun += $item['cjRun'];
        //     $totalMjRun +=  $item['mjRun'];
        //     $ttlMcDcRunperPU = $item['cjRun'] + $item['mjRun'];
        //     $totalMcDcRunperPU +=  $ttlMcDcRunperPU;

        //     $mergerow = $rowLC + 4;
        //     $sheet //->mergeCells('A' . $rowLC . ':A' . $mergerow)
        //         ->setCellValue('A' . $rowLC, $key)
        //         ->setCellValue('C' . $rowLC, $item['dakong'])
        //         //  ->mergeCells('C' . $rowLC . ':C' . $mergerow - 1)
        //         ->setCellValue('D' . $rowLC, $item['rosso'])
        //         //->mergeCells('D' . $rowLC . ':D' . $mergerow)
        //         ->setCellValue('E' . $rowLC, $item['mekanik'])
        //         //->mergeCells('E' . $rowLC . ':E' . $mergerow)
        //         ->setCellValue('F' . $rowLC, $item['lonati'])
        //         //->mergeCells('F ' . $rowLC . ':F ' . $mergerow)
        //         //  ->mergeCells('B' . $rowLC . ':B' . $mergerow)
        //         ->setCellValue('B' . $rowLC, "0")
        //         //->mergeCells('G' . $rowLC . ':G' . $mergerow)
        //         ->setCellValue('G' . $rowLC, $totalMc);
        //     $cellCoordinates = ['A' . $rowLC, 'C' . $rowLC, 'D' . $rowLC, 'F' . $rowLC, 'G' . $rowLC, 'B' . $rowLC, 'E' . $rowLC,];
        //     foreach ($cellCoordinates as $cellCoordinate) {
        //         $sheet->getStyle($cellCoordinate)->applyFromArray([
        //             'borders' => [
        //                 'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
        //             ],
        //             'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        //         ]);
        //     }
        //     // total mesin BC
        //     $sheet->setCellValue('A' . $totalLCRow, 'TOTAL CHILD COMP N 120 + N120 SP')
        //         ->setCellValue('C' . $totalLCRow, $totalDakong)
        //         ->setCellValue('D' . $totalLCRow, $totalRosso)
        //         ->setCellValue('E' . $totalLCRow, $totalThs)
        //         ->setCellValue('G' . $totalLCRow, $totalDc)
        //         ->setCellValue('F' . $totalLCRow, $totalLon)
        //         ->setCellValue('H' . $totalLCRow, $totalDakongRun)
        //         ->setCellValue('I' . $totalLCRow, $totalRossoRun)
        //         ->setCellValue('J' . $totalLCRow, $totalThsRun)
        //         ->setCellValue('K' . $totalLCRow, $totalLonRun)
        //         ->setCellValue('M' . $totalLCRow, $totalDcRun);
        //     $boldStyle = ['A' . $totalLCRow, 'C' . $totalLCRow, 'B' . $totalLCRow, 'D' . $totalLCRow, 'E' . $totalLCRow, 'F' . $totalLCRow,  'H' . $totalLCRow, 'I' . $totalLCRow, 'J' . $totalLCRow, 'K' . $totalLCRow, 'M' . $totalLCRow, 'L' . $totalLCRow, 'N' . $totalLCRow, 'O' . $totalLCRow, 'P' . $totalLCRow, 'Q' . $totalLCRow,  'R' . $totalLCRow, 'S' . $totalLCRow, 'T' . $totalLCRow, 'V' . $totalLCRow, 'W' . $totalLCRow, 'X' . $totalLCRow,];
        //     foreach ($boldStyle as $bs) {
        //         $sheet->getStyle($bs)->applyFromArray([
        //             'font' => ['bold' => true],
        //             'borders' => [
        //                 'outline' => ['borderStyle' => Border::BORDER_THICK, 'color' => ['rgb' => '000000']],
        //             ],
        //             'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        //         ]);
        //     }
        //     // totalRunningMc
        //     $sheet
        //         ->setCellValue('H' . $rowLC, $item['dakongRun'])
        //         ->setCellValue('I' . $rowLC, $item['rossoRun'])
        //         ->setCellValue('J' . $rowLC, $item['mekanikRun'])
        //         ->setCellValue('K' . $rowLC, $item['lonatiRun'])
        //         ->setCellValue('M' . $rowLC, $totalMcDcRun);
        //     $cellCoordinates = ['H' . $rowLC, 'I' . $rowLC, 'J' . $rowLC, 'K' . $rowLC,  'N' . $rowLC, 'L' . $rowLC,  'O' . $rowLC,  'P' . $rowLC,  'Q' . $rowLC,];
        //     foreach ($cellCoordinates as $cellCoordinate) {
        //         $sheet->getStyle($cellCoordinate)->applyFromArray([
        //             'borders' => [
        //                 'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
        //             ],
        //             'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],

        //         ]);
        //         // stockCylinderBc
        //         $sheet
        //             ->setCellValue('P' . $rowLC, $item['stockCylDk'])
        //             ->setCellValue('Q' . $rowLC, $item['stockCylThs'])
        //             ->setCellValue('R' . $rowLC, $item['stockCylRosso'])
        //             ->setCellValue('P' . $totalLCRow, $totalStockCylDk)
        //             ->setCellValue('Q' . $totalLCRow, $totalStockCylThs)
        //             ->setCellValue('R' . $totalLCRow, $totalStockCylRosso);

        //         $cellCoordinates = ['P' . $rowLC, 'Q' . $rowLC, 'J' . $rowLC, 'R' . $rowLC,];
        //         foreach ($cellCoordinates as $cellCoordinate) {
        //             $sheet->getStyle($cellCoordinate)->applyFromArray([
        //                 'borders' => [
        //                     'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
        //                 ],
        //                 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],

        //             ]);
        //         }
        //         // totalRunningMc PU &stockCYl
        //         $sheet
        //             ->setCellValue('S' . $totalLCRow, $totalCjRun)
        //             ->setCellValue('T' . $totalLCRow, $totalMjRun)
        //             ->setCellValue('U' . $totalLCRow, $totalMcDcRunperPU)
        //             ->setCellValue('S' . $rowLC, $item['cjRun'])
        //             ->setCellValue('T' . $rowLC, $item['mjRun'])
        //             ->setCellValue('U' . $rowLC, $ttlMcDcRunperPU);
        //         $cellCoordinates = ['S' . $rowLC, 'T' . $rowLC, 'U' . $rowLC,];
        //         foreach ($cellCoordinates as $cellCoordinate) {
        //             $sheet->getStyle($cellCoordinate)->applyFromArray([
        //                 'borders' => [
        //                     'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
        //                 ],
        //                 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],

        //             ]);
        //         }
        //         // WEEK & Capasity Produksi
        //         $sheet
        //             ->setCellValue('V' . $totalLCRow, $week)
        //             ->setCellValue('W' . $totalLCRow, $minus3)
        //             ->setCellValue('X' . $totalLCRow, $po28hari)
        //             ->setCellValue('V' . $rowLC, "0")
        //             ->setCellValue('W' . $rowLC, "0")
        //             ->setCellValue('X' . $rowLC, "0");
        //         $cellCoordinates = ['V' . $rowLC, 'W' . $rowLC, 'X' . $rowLC,];
        //         foreach ($cellCoordinates as $cellCoordinate) {
        //             $sheet->getStyle($cellCoordinate)->applyFromArray([
        //                 'borders' => [
        //                     'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
        //                 ],
        //                 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],

        //             ]);
        //         }

        //         $totalStyle = ['U' . $rowLC, 'M' . $rowLC, 'G' . $rowLC, 'G' . $totalLCRow, 'M' . $totalLCRow, 'U' . $totalLCRow,];
        //         foreach ($totalStyle  as $styleTotal) {
        //             $sheet->getStyle($styleTotal)->applyFromArray([
        //                 'borders' => [
        //                     'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
        //                 ],
        //                 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        //                 'fill' => [
        //                     'fillType' => Fill::FILL_SOLID,
        //                     'startColor' => ['rgb' => 'FFA500'], // Orange color
        //                 ],
        //             ]);
        //         }
        //     }

        //     $rowLC++;
        // }

        // //MENS COMP N168
        // foreach ($mensComp as $key => $item) {
        //     $totalMc = $item['dakong'] + $item['rosso'] + $item['mekanik'] + $item['lonati'];
        //     $totalDakong  += $item['dakong'];
        //     $totalThs  += $item['mekanik'];
        //     $totalRosso  += $item['rosso'];
        //     $totalLon  += $item['lonati'];
        //     $totalDc  += $totalMc;

        //     $totalMcDcRun = $item['dakongRun'] + $item['rossoRun'] + $item['mekanikRun'] + $item['lonatiRun'];
        //     $totalDakongRun  += $item['dakongRun'];
        //     $totalThsRun  += $item['mekanikRun'];
        //     $totalRossoRun  += $item['rossoRun'];
        //     $totalLonRun  += $item['lonatiRun'];
        //     $totalDcRun += $totalMcDcRun;

        //     $totalStockCylDk += $item['stockCylDk'];
        //     $totalStockCylThs += $item['stockCylThs'];
        //     $totalStockCylRosso += $item['stockCylRosso'];

        //     $totalCjRun += $item['cjRun'];
        //     $totalMjRun +=  $item['mjRun'];
        //     $ttlMcDcRunperPU = $item['cjRun'] + $item['mjRun'];
        //     $totalMcDcRunperPU +=  $ttlMcDcRunperPU;


        //     $sheet->setCellValue('A' . $rowMC168, $key)
        //         ->setCellValue('C' . $rowMC168, $item['dakong'])
        //         ->setCellValue('D' . $rowMC168, $item['rosso'])
        //         ->setCellValue('E' . $rowMC168, $item['mekanik'])
        //         ->setCellValue('F' . $rowMC168, $item['lonati'])
        //         ->setCellValue('B' . $rowMC168, "0")
        //         ->setCellValue('G' . $rowMC168, $totalMc);
        //     $cellCoordinates = ['A' . $rowMC168, 'C' . $rowMC168, 'D' . $rowMC168, 'F' . $rowMC168, 'G' . $rowMC168, 'B' . $rowMC168, 'E' . $rowMC168,];
        //     foreach ($cellCoordinates as $cellCoordinate) {
        //         $sheet->getStyle($cellCoordinate)->applyFromArray([
        //             'borders' => [
        //                 'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
        //             ],
        //             'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        //         ]);
        //     }
        //     // total mesin BC
        //     $sheet->setCellValue('A' . $totalMC168Row, 'TOTAL MENS COMP N168 + N168 SP')
        //         ->setCellValue('C' . $totalMC168Row, $totalDakong)
        //         ->setCellValue('D' . $totalMC168Row, $totalRosso)
        //         ->setCellValue('E' . $totalMC168Row, $totalThs)
        //         ->setCellValue('G' . $totalMC168Row, $totalDc)
        //         ->setCellValue('F' . $totalMC168Row, $totalLon)
        //         ->setCellValue('H' . $totalMC168Row, $totalDakongRun)
        //         ->setCellValue('I' . $totalMC168Row, $totalRossoRun)
        //         ->setCellValue('J' . $totalMC168Row, $totalThsRun)
        //         ->setCellValue('K' . $totalMC168Row, $totalLonRun)
        //         ->setCellValue('M' . $totalMC168Row, $totalDcRun);
        //     $boldStyle = ['A' . $totalMC168Row, 'C' . $totalMC168Row, 'B' . $totalMC168Row, 'D' . $totalMC168Row, 'E' . $totalMC168Row, 'F' . $totalMC168Row,  'H' . $totalMC168Row, 'I' . $totalMC168Row, 'J' . $totalMC168Row, 'K' . $totalMC168Row, 'M' . $totalMC168Row, 'L' . $totalMC168Row, 'N' . $totalMC168Row, 'O' . $totalMC168Row, 'P' . $totalMC168Row, 'Q' . $totalMC168Row,  'R' . $totalMC168Row, 'S' . $totalMC168Row, 'T' . $totalMC168Row, 'V' . $totalMC168Row, 'W' . $totalMC168Row, 'X' . $totalMC168Row,];
        //     foreach ($boldStyle as $bs) {
        //         $sheet->getStyle($bs)->applyFromArray([
        //             'font' => ['bold' => true],
        //             'borders' => [
        //                 'outline' => ['borderStyle' => Border::BORDER_THICK, 'color' => ['rgb' => '000000']],
        //             ],
        //             'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        //         ]);
        //     }
        //     // totalRunningMc
        //     $sheet
        //         ->setCellValue('H' . $rowMC168, $item['dakongRun'])
        //         ->setCellValue('I' . $rowMC168, $item['rossoRun'])
        //         ->setCellValue('J' . $rowMC168, $item['mekanikRun'])
        //         ->setCellValue('K' . $rowMC168, $item['lonatiRun'])
        //         ->setCellValue('M' . $rowMC168, $totalMcDcRun);
        //     $cellCoordinates = ['H' . $rowMC168, 'I' . $rowMC168, 'J' . $rowMC168, 'K' . $rowMC168,  'N' . $rowMC168, 'L' . $rowMC168,  'O' . $rowMC168,  'P' . $rowMC168,  'Q' . $rowMC168,];
        //     foreach ($cellCoordinates as $cellCoordinate) {
        //         $sheet->getStyle($cellCoordinate)->applyFromArray([
        //             'borders' => [
        //                 'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
        //             ],
        //             'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],

        //         ]);
        //         // stockCylinderBc
        //         $sheet
        //             ->setCellValue('P' . $rowMC168, $item['stockCylDk'])
        //             ->setCellValue('Q' . $rowMC168, $item['stockCylThs'])
        //             ->setCellValue('R' . $rowMC168, $item['stockCylRosso'])
        //             ->setCellValue('P' . $totalMC168Row, $totalStockCylDk)
        //             ->setCellValue('Q' . $totalMC168Row, $totalStockCylThs)
        //             ->setCellValue('R' . $totalMC168Row, $totalStockCylRosso);

        //         $cellCoordinates = ['P' . $rowMC168, 'Q' . $rowMC168, 'J' . $rowMC168, 'R' . $rowMC168,];
        //         foreach ($cellCoordinates as $cellCoordinate) {
        //             $sheet->getStyle($cellCoordinate)->applyFromArray([
        //                 'borders' => [
        //                     'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
        //                 ],
        //                 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],

        //             ]);
        //         }
        //         // totalRunningMc PU &stockCYl
        //         $sheet
        //             ->setCellValue('S' . $totalMC168Row, $totalCjRun)
        //             ->setCellValue('T' . $totalMC168Row, $totalMjRun)
        //             ->setCellValue('U' . $totalMC168Row, $totalMcDcRunperPU)
        //             ->setCellValue('S' . $rowMC168, $item['cjRun'])
        //             ->setCellValue('T' . $rowMC168, $item['mjRun'])
        //             ->setCellValue('U' . $rowMC168, $ttlMcDcRunperPU);
        //         $cellCoordinates = ['S' . $rowMC168, 'T' . $rowMC168, 'U' . $rowMC168,];
        //         foreach ($cellCoordinates as $cellCoordinate) {
        //             $sheet->getStyle($cellCoordinate)->applyFromArray([
        //                 'borders' => [
        //                     'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
        //                 ],
        //                 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],

        //             ]);
        //         }
        //         // WEEK & Capasity Produksi
        //         $sheet
        //             ->setCellValue('V' . $totalMC168Row, $week)
        //             ->setCellValue('W' . $totalMC168Row, $minus3)
        //             ->setCellValue('X' . $totalMC168Row, $po28hari)
        //             ->setCellValue('V' . $rowMC168, "0")
        //             ->setCellValue('W' . $rowMC168, "0")
        //             ->setCellValue('X' . $rowMC168, "0");
        //         $cellCoordinates = ['V' . $rowMC168, 'W' . $rowMC168, 'X' . $rowMC168,];
        //         foreach ($cellCoordinates as $cellCoordinate) {
        //             $sheet->getStyle($cellCoordinate)->applyFromArray([
        //                 'borders' => [
        //                     'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
        //                 ],
        //                 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],

        //             ]);
        //         }

        //         $totalStyle = ['U' . $rowMC168, 'M' . $rowMC168, 'G' . $rowMC168, 'G' . $totalMC168Row, 'M' . $totalMC168Row, 'U' . $totalMC168Row,];
        //         foreach ($totalStyle  as $styleTotal) {
        //             $sheet->getStyle($styleTotal)->applyFromArray([
        //                 'borders' => [
        //                     'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
        //                 ],
        //                 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        //                 'fill' => [
        //                     'fillType' => Fill::FILL_SOLID,
        //                     'startColor' => ['rgb' => 'FFA500'], // Orange color
        //                 ],
        //             ]);
        //         }
        //     }

        //     $rowMC168++;
        // }

        // //MENS COMP N200
        // foreach ($mensComp200 as $key => $item) {
        //     $totalMc = $item['dakong'] + $item['rosso'] + $item['mekanik'] + $item['lonati'];
        //     $totalDakong  += $item['dakong'];
        //     $totalThs  += $item['mekanik'];
        //     $totalRosso  += $item['rosso'];
        //     $totalLon  += $item['lonati'];
        //     $totalDc  += $totalMc;

        //     $totalMcDcRun = $item['dakongRun'] + $item['rossoRun'] + $item['mekanikRun'] + $item['lonatiRun'];
        //     $totalDakongRun  += $item['dakongRun'];
        //     $totalThsRun  += $item['mekanikRun'];
        //     $totalRossoRun  += $item['rossoRun'];
        //     $totalLonRun  += $item['lonatiRun'];
        //     $totalDcRun += $totalMcDcRun;

        //     $totalStockCylDk += $item['stockCylDk'];
        //     $totalStockCylThs += $item['stockCylThs'];
        //     $totalStockCylRosso += $item['stockCylRosso'];

        //     $totalCjRun += $item['cjRun'];
        //     $totalMjRun +=  $item['mjRun'];
        //     $ttlMcDcRunperPU = $item['cjRun'] + $item['mjRun'];
        //     $totalMcDcRunperPU +=  $ttlMcDcRunperPU;


        //     $sheet->setCellValue('A' . $rowMC200, $key)
        //         ->setCellValue('C' . $rowMC200, $item['dakong'])
        //         ->setCellValue('D' . $rowMC200, $item['rosso'])
        //         ->setCellValue('E' . $rowMC200, $item['mekanik'])
        //         ->setCellValue('F' . $rowMC200, $item['lonati'])
        //         ->setCellValue('B' . $rowMC200, "0")
        //         ->setCellValue('G' . $rowMC200, $totalMc);
        //     $cellCoordinates = ['A' . $rowMC200, 'C' . $rowMC200, 'D' . $rowMC200, 'F' . $rowMC200, 'G' . $rowMC200, 'B' . $rowMC200, 'E' . $rowMC200,];
        //     foreach ($cellCoordinates as $cellCoordinate) {
        //         $sheet->getStyle($cellCoordinate)->applyFromArray([
        //             'borders' => [
        //                 'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
        //             ],
        //             'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        //         ]);
        //     }

        //     // totalRunningMc
        //     $sheet
        //         ->setCellValue('H' . $rowMC200, $item['dakongRun'])
        //         ->setCellValue('I' . $rowMC200, $item['rossoRun'])
        //         ->setCellValue('J' . $rowMC200, $item['mekanikRun'])
        //         ->setCellValue('K' . $rowMC200, $item['lonatiRun'])
        //         ->setCellValue('M' . $rowMC200, $totalMcDcRun);
        //     $cellCoordinates = ['H' . $rowMC200, 'I' . $rowMC200, 'J' . $rowMC200, 'K' . $rowMC200,  'N' . $rowMC200, 'L' . $rowMC200,  'O' . $rowMC200,  'P' . $rowMC200,  'Q' . $rowMC200,];
        //     foreach ($cellCoordinates as $cellCoordinate) {
        //         $sheet->getStyle($cellCoordinate)->applyFromArray([
        //             'borders' => [
        //                 'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
        //             ],
        //             'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],

        //         ]);
        //         // stockCylinderBc
        //         $sheet
        //             ->setCellValue('P' . $rowMC200, $item['stockCylDk'])
        //             ->setCellValue('Q' . $rowMC200, $item['stockCylThs'])
        //             ->setCellValue('R' . $rowMC200, $item['stockCylRosso']);

        //         $cellCoordinates = ['P' . $rowMC200, 'Q' . $rowMC200, 'J' . $rowMC200, 'R' . $rowMC200,];
        //         foreach ($cellCoordinates as $cellCoordinate) {
        //             $sheet->getStyle($cellCoordinate)->applyFromArray([
        //                 'borders' => [
        //                     'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
        //                 ],
        //                 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],

        //             ]);
        //         }
        //         // totalRunningMc PU &stockCYl
        //         $sheet
        //             ->setCellValue('S' . $rowMC200, $item['cjRun'])
        //             ->setCellValue('T' . $rowMC200, $item['mjRun'])
        //             ->setCellValue('U' . $rowMC200, $ttlMcDcRunperPU);
        //         $cellCoordinates = ['S' . $rowMC200, 'T' . $rowMC200, 'U' . $rowMC200,];
        //         foreach ($cellCoordinates as $cellCoordinate) {
        //             $sheet->getStyle($cellCoordinate)->applyFromArray([
        //                 'borders' => [
        //                     'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
        //                 ],
        //                 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],

        //             ]);
        //         }
        //         // WEEK & Capasity Produksi
        //         $sheet
        //             ->setCellValue('V' . $rowMC200, "0")
        //             ->setCellValue('W' . $rowMC200, "0")
        //             ->setCellValue('X' . $rowMC200, "0");
        //         $cellCoordinates = ['V' . $rowMC200, 'W' . $rowMC200, 'X' . $rowMC200,];
        //         foreach ($cellCoordinates as $cellCoordinate) {
        //             $sheet->getStyle($cellCoordinate)->applyFromArray([
        //                 'borders' => [
        //                     'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
        //                 ],
        //                 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],

        //             ]);
        //         }

        //         $totalStyle = ['U' . $rowMC200, 'M' . $rowMC200, 'G' . $rowMC200,];
        //         foreach ($totalStyle  as $styleTotal) {
        //             $sheet->getStyle($styleTotal)->applyFromArray([
        //                 'borders' => [
        //                     'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
        //                 ],
        //                 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        //                 'fill' => [
        //                     'fillType' => Fill::FILL_SOLID,
        //                     'startColor' => ['rgb' => 'FFA500'], // Orange color
        //                 ],
        //             ]);
        //         }
        //     }

        //     $rowMC200++;
        // }

        // //CHILD COMP N132
        // foreach ($childComp132 as $key => $item) {
        //     $totalMc = $item['dakong'] + $item['rosso'] + $item['mekanik'] + $item['lonati'];
        //     $totalDakong  += $item['dakong'];
        //     $totalThs  += $item['mekanik'];
        //     $totalRosso  += $item['rosso'];
        //     $totalLon  += $item['lonati'];
        //     $totalDc  += $totalMc;

        //     $totalMcDcRun = $item['dakongRun'] + $item['rossoRun'] + $item['mekanikRun'] + $item['lonatiRun'];
        //     $totalDakongRun  += $item['dakongRun'];
        //     $totalThsRun  += $item['mekanikRun'];
        //     $totalRossoRun  += $item['rossoRun'];
        //     $totalLonRun  += $item['lonatiRun'];
        //     $totalDcRun += $totalMcDcRun;

        //     $totalStockCylDk += $item['stockCylDk'];
        //     $totalStockCylThs += $item['stockCylThs'];
        //     $totalStockCylRosso += $item['stockCylRosso'];

        //     $totalCjRun += $item['cjRun'];
        //     $totalMjRun +=  $item['mjRun'];
        //     $ttlMcDcRunperPU = $item['cjRun'] + $item['mjRun'];
        //     $totalMcDcRunperPU +=  $ttlMcDcRunperPU;


        //     $sheet->setCellValue('A' . $rowCC132, $key)
        //         ->setCellValue('C' . $rowCC132, $item['dakong'])
        //         ->setCellValue('D' . $rowCC132, $item['rosso'])
        //         ->setCellValue('E' . $rowCC132, $item['mekanik'])
        //         ->setCellValue('F' . $rowCC132, $item['lonati'])
        //         ->setCellValue('B' . $rowCC132, "0")
        //         ->setCellValue('G' . $rowCC132, $totalMc);
        //     $cellCoordinates = ['A' . $rowCC132, 'C' . $rowCC132, 'D' . $rowCC132, 'F' . $rowCC132, 'G' . $rowCC132, 'B' . $rowCC132, 'E' . $rowCC132,];
        //     foreach ($cellCoordinates as $cellCoordinate) {
        //         $sheet->getStyle($cellCoordinate)->applyFromArray([
        //             'borders' => [
        //                 'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
        //             ],
        //             'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        //         ]);
        //     }

        //     // totalRunningMc
        //     $sheet
        //         ->setCellValue('H' . $rowCC132, $item['dakongRun'])
        //         ->setCellValue('I' . $rowCC132, $item['rossoRun'])
        //         ->setCellValue('J' . $rowCC132, $item['mekanikRun'])
        //         ->setCellValue('K' . $rowCC132, $item['lonatiRun'])
        //         ->setCellValue('M' . $rowCC132, $totalMcDcRun);
        //     $cellCoordinates = ['H' . $rowCC132, 'I' . $rowCC132, 'J' . $rowCC132, 'K' . $rowCC132,  'N' . $rowCC132, 'L' . $rowCC132,  'O' . $rowCC132,  'P' . $rowCC132,  'Q' . $rowCC132,];
        //     foreach ($cellCoordinates as $cellCoordinate) {
        //         $sheet->getStyle($cellCoordinate)->applyFromArray([
        //             'borders' => [
        //                 'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
        //             ],
        //             'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],

        //         ]);
        //         // stockCylinderBc
        //         $sheet
        //             ->setCellValue('P' . $rowCC132, $item['stockCylDk'])
        //             ->setCellValue('Q' . $rowCC132, $item['stockCylThs'])
        //             ->setCellValue('R' . $rowCC132, $item['stockCylRosso']);

        //         $cellCoordinates = ['P' . $rowCC132, 'Q' . $rowCC132, 'J' . $rowCC132, 'R' . $rowCC132,];
        //         foreach ($cellCoordinates as $cellCoordinate) {
        //             $sheet->getStyle($cellCoordinate)->applyFromArray([
        //                 'borders' => [
        //                     'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
        //                 ],
        //                 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],

        //             ]);
        //         }
        //         // totalRunningMc PU &stockCYl
        //         $sheet
        //             ->setCellValue('S' . $rowCC132, $item['cjRun'])
        //             ->setCellValue('T' . $rowCC132, $item['mjRun'])
        //             ->setCellValue('U' . $rowCC132, $ttlMcDcRunperPU);
        //         $cellCoordinates = ['S' . $rowCC132, 'T' . $rowCC132, 'U' . $rowCC132,];
        //         foreach ($cellCoordinates as $cellCoordinate) {
        //             $sheet->getStyle($cellCoordinate)->applyFromArray([
        //                 'borders' => [
        //                     'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
        //                 ],
        //                 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],

        //             ]);
        //         }
        //         // WEEK & Capasity Produksi
        //         $sheet
        //             ->setCellValue('V' . $rowCC132, "0")
        //             ->setCellValue('W' . $rowCC132, "0")
        //             ->setCellValue('X' . $rowCC132, "0");
        //         $cellCoordinates = ['V' . $rowCC132, 'W' . $rowCC132, 'X' . $rowCC132,];
        //         foreach ($cellCoordinates as $cellCoordinate) {
        //             $sheet->getStyle($cellCoordinate)->applyFromArray([
        //                 'borders' => [
        //                     'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
        //                 ],
        //                 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],

        //             ]);
        //         }

        //         $totalStyle = ['U' . $rowCC132, 'M' . $rowCC132, 'G' . $rowCC132,];
        //         foreach ($totalStyle  as $styleTotal) {
        //             $sheet->getStyle($styleTotal)->applyFromArray([
        //                 'borders' => [
        //                     'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
        //                 ],
        //                 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        //                 'fill' => [
        //                     'fillType' => Fill::FILL_SOLID,
        //                     'startColor' => ['rgb' => 'FFA500'], // Orange color
        //                 ],
        //             ]);
        //         }
        //     }

        //     $rowCC132++;
        // }

        // //MENS COMP N156
        // foreach ($mensComp156 as $key => $item) {
        //     $totalMc = $item['dakong'] + $item['rosso'] + $item['mekanik'] + $item['lonati'];
        //     $totalDakong  += $item['dakong'];
        //     $totalThs  += $item['mekanik'];
        //     $totalRosso  += $item['rosso'];
        //     $totalLon  += $item['lonati'];
        //     $totalDc  += $totalMc;

        //     $totalMcDcRun = $item['dakongRun'] + $item['rossoRun'] + $item['mekanikRun'] + $item['lonatiRun'];
        //     $totalDakongRun  += $item['dakongRun'];
        //     $totalThsRun  += $item['mekanikRun'];
        //     $totalRossoRun  += $item['rossoRun'];
        //     $totalLonRun  += $item['lonatiRun'];
        //     $totalDcRun += $totalMcDcRun;

        //     $totalStockCylDk += $item['stockCylDk'];
        //     $totalStockCylThs += $item['stockCylThs'];
        //     $totalStockCylRosso += $item['stockCylRosso'];

        //     $totalCjRun += $item['cjRun'];
        //     $totalMjRun +=  $item['mjRun'];
        //     $ttlMcDcRunperPU = $item['cjRun'] + $item['mjRun'];
        //     $totalMcDcRunperPU +=  $ttlMcDcRunperPU;


        //     $sheet->setCellValue('A' . $rowMC156, $key)
        //         ->setCellValue('C' . $rowMC156, $item['dakong'])
        //         ->setCellValue('D' . $rowMC156, $item['rosso'])
        //         ->setCellValue('E' . $rowMC156, $item['mekanik'])
        //         ->setCellValue('F' . $rowMC156, $item['lonati'])
        //         ->setCellValue('B' . $rowMC156, "0")
        //         ->setCellValue('G' . $rowMC156, $totalMc);
        //     $cellCoordinates = ['A' . $rowMC156, 'C' . $rowMC156, 'D' . $rowMC156, 'F' . $rowMC156, 'G' . $rowMC156, 'B' . $rowMC156, 'E' . $rowMC156,];
        //     foreach ($cellCoordinates as $cellCoordinate) {
        //         $sheet->getStyle($cellCoordinate)->applyFromArray([
        //             'borders' => [
        //                 'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
        //             ],
        //             'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        //         ]);
        //     }

        //     // totalRunningMc
        //     $sheet
        //         ->setCellValue('H' . $rowMC156, $item['dakongRun'])
        //         ->setCellValue('I' . $rowMC156, $item['rossoRun'])
        //         ->setCellValue('J' . $rowMC156, $item['mekanikRun'])
        //         ->setCellValue('K' . $rowMC156, $item['lonatiRun'])
        //         ->setCellValue('M' . $rowMC156, $totalMcDcRun);
        //     $cellCoordinates = ['H' . $rowMC156, 'I' . $rowMC156, 'J' . $rowMC156, 'K' . $rowMC156,  'N' . $rowMC156, 'L' . $rowMC156,  'O' . $rowMC156,  'P' . $rowMC156,  'Q' . $rowMC156,];
        //     foreach ($cellCoordinates as $cellCoordinate) {
        //         $sheet->getStyle($cellCoordinate)->applyFromArray([
        //             'borders' => [
        //                 'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
        //             ],
        //             'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],

        //         ]);
        //         // stockCylinderBc
        //         $sheet
        //             ->setCellValue('P' . $rowMC156, $item['stockCylDk'])
        //             ->setCellValue('Q' . $rowMC156, $item['stockCylThs'])
        //             ->setCellValue('R' . $rowMC156, $item['stockCylRosso']);

        //         $cellCoordinates = ['P' . $rowMC156, 'Q' . $rowMC156, 'J' . $rowMC156, 'R' . $rowMC156,];
        //         foreach ($cellCoordinates as $cellCoordinate) {
        //             $sheet->getStyle($cellCoordinate)->applyFromArray([
        //                 'borders' => [
        //                     'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
        //                 ],
        //                 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],

        //             ]);
        //         }
        //         // totalRunningMc PU &stockCYl
        //         $sheet
        //             ->setCellValue('S' . $rowMC156, $item['cjRun'])
        //             ->setCellValue('T' . $rowMC156, $item['mjRun'])
        //             ->setCellValue('U' . $rowMC156, $ttlMcDcRunperPU);
        //         $cellCoordinates = ['S' . $rowMC156, 'T' . $rowMC156, 'U' . $rowMC156,];
        //         foreach ($cellCoordinates as $cellCoordinate) {
        //             $sheet->getStyle($cellCoordinate)->applyFromArray([
        //                 'borders' => [
        //                     'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
        //                 ],
        //                 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],

        //             ]);
        //         }
        //         // WEEK & Capasity Produksi
        //         $sheet
        //             ->setCellValue('V' . $rowMC156, "0")
        //             ->setCellValue('W' . $rowMC156, "0")
        //             ->setCellValue('X' . $rowMC156, "0");
        //         $cellCoordinates = ['V' . $rowMC156, 'W' . $rowMC156, 'X' . $rowMC156,];
        //         foreach ($cellCoordinates as $cellCoordinate) {
        //             $sheet->getStyle($cellCoordinate)->applyFromArray([
        //                 'borders' => [
        //                     'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
        //                 ],
        //                 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],

        //             ]);
        //         }

        //         $totalStyle = ['U' . $rowMC156, 'M' . $rowMC156, 'G' . $rowMC156,];
        //         foreach ($totalStyle  as $styleTotal) {
        //             $sheet->getStyle($styleTotal)->applyFromArray([
        //                 'borders' => [
        //                     'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
        //                 ],
        //                 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        //                 'fill' => [
        //                     'fillType' => Fill::FILL_SOLID,
        //                     'startColor' => ['rgb' => 'FFA500'], // Orange color
        //                 ],
        //             ]);
        //         }
        //     }

        //     $rowMC156++;
        // }
        // week1-5bc N84


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

    public function excelPlnMc($id)
    {
        $detailplan = $this->DetailPlanningModel->getDataPlanning($id);
        // dd($detailplan);
        $judul = $this->request->getGet('judul');
        $area = $this->request->getGet('area');
        $jarum = $this->request->getGet('jarum');
        $mesinarea = $this->jarumModel->getMesinByArea($area, $jarum);

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

        // Tambahkan header
        $sheet->setCellValue('A3', 'Model');
        $sheet->setCellValue('B3', 'Delivery');
        $sheet->setCellValue('C3', 'Qty');
        $sheet->setCellValue('D3', 'Remaining Qty');
        $sheet->setCellValue('E3', 'Qty Planned');
        $sheet->setCellValue('F3', 'Target 100%');
        $sheet->setCellValue('G3', 'Start');
        $sheet->setCellValue('H3', 'Stop');
        $sheet->setCellValue('I3', 'Machine');
        $sheet->setCellValue('J3', 'Days');
        $sheet->getStyle('A3')->applyFromArray($styleHeader);
        $sheet->getStyle('B3')->applyFromArray($styleHeader);
        $sheet->getStyle('C3')->applyFromArray($styleHeader);
        $sheet->getStyle('D3')->applyFromArray($styleHeader);
        $sheet->getStyle('E3')->applyFromArray($styleHeader);
        $sheet->getStyle('F3')->applyFromArray($styleHeader);
        $sheet->getStyle('G3')->applyFromArray($styleHeader);
        $sheet->getStyle('H3')->applyFromArray($styleHeader);
        $sheet->getStyle('I3')->applyFromArray($styleHeader);
        $sheet->getStyle('J3')->applyFromArray($styleHeader);

        $row = 4;
        foreach ($detailplan as $order) :
            //Mengisi sheet excel
            $sheet->setCellValue("A$row", $order['model']);
            $sheet->setCellValue("B$row", date('d-M-Y', strtotime($order['delivery'])));
            $sheet->setCellValue("C$row", number_format($order['qty'], 0, '.', ','));
            $sheet->setCellValue("D$row", number_format($order['sisa'], 0, '.', ','));
            $sheet->setCellValue("E$row", number_format($order['est_qty'], 0, '.', ','));
            $sheet->setCellValue("F$row", number_format(3600 / $order['smv'], 2, '.', ','));
            $sheet->setCellValue("G$row", !empty($order['start_date']) ? date('d-M-Y', strtotime($order['start_date'])) : 'No Start Date');
            $sheet->setCellValue("H$row", !empty($order['stop_date']) ? date('d-M-Y', strtotime($order['stop_date'])) : 'No Stop Date');
            $sheet->setCellValue("I$row", $order['mesin'] ? htmlspecialchars($order['mesin']) . ' Mc' : '');
            $sheet->setCellValue("J$row", $order['hari']);
            $sheet->getStyle("A$row")->applyFromArray($styleBody);
            $sheet->getStyle("B$row")->applyFromArray($styleBody);
            $sheet->getStyle("C$row")->applyFromArray($styleBody);
            $sheet->getStyle("D$row")->applyFromArray($styleBody);
            $sheet->getStyle("E$row")->applyFromArray($styleBody);
            $sheet->getStyle("F$row")->applyFromArray($styleBody);
            $sheet->getStyle("G$row")->applyFromArray($styleBody);
            $sheet->getStyle("H$row")->applyFromArray($styleBody);
            $sheet->getStyle("I$row")->applyFromArray($styleBody);
            $sheet->getStyle("J$row")->applyFromArray($styleBody);
            $row++;
        endforeach;

        // Set sheet pertama sebagai active sheet
        $spreadsheet->setActiveSheetIndex(0);

        // Export file ke Excel
        $writer = new Xlsx($spreadsheet);
        $filename = 'Planning MC ' . $judul . '.xlsx';

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    public function excelSisaOrderBuyer($buyer)
    {
        $role = session()->get('role');
        $month = $this->request->getPost('months');
        $yearss = $this->request->getPost('years');
        $weekCount = 0;

        // Atur tanggal berdasarkan input bulan dan tahun dari POST
        $bulan = date('Y-m-01', strtotime("$yearss-$month-01"));

        $years = [];
        $currentYear = date('Y');
        $endYear = $currentYear + 10;

        // Loop dari tahun ini sampai 10 tahun ke depan
        for ($year = $currentYear; $year <= $endYear; $year++) {
            $months = [];

            // Loop untuk setiap bulan dalam setahun
            for ($i = 1; $i <= 12; $i++) {
                $monthName = date('F', mktime(0, 0, 0, $i, 1)); // Nama bulan
                $months[] = $monthName;
            }

            // Simpan data tahun dengan bulan-bulannya
            $years[$year] = array_unique($months); // array_unique memastikan bulan unik meskipun tidak perlu dalam kasus ini
        }

        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthName = date('F', mktime(0, 0, 0, $i, 1)); // Nama bulan
            $months[] = $monthName;
        }
        $months = array_unique($months);

        // Ambil data dari model
        $startDate = new \DateTime($bulan); // Awal bulan
        $startDate->setTime(0, 0, 0);
        $endDate = (clone $startDate)->modify('last day of this month');   // Akhir bulan

        // Loop data
        $data = $this->ApsPerstyleModel->getBuyerOrder($buyer, $bulan);
        // dd($data);
        $allData = [];
        $week = [];
        $totalPerWeek = [];

        foreach ($data as $id) {
            $mastermodel = $id['mastermodel'];
            $machinetypeid = $id['machinetypeid'];
            $factory = $id['factory'];

            // Ambil data qty, sisa, dan produksi
            $qty = $id['qty'];
            $sisa = $id['sisa'];
            $produksi = $qty - $sisa;
            $deliveryDate = new \DateTime($id['delivery']); // Asumsikan ada field delivery

            // Loop untuk membagi data ke dalam minggu
            $weekCount = 1;
            $currentStartDate = clone $startDate;

            for ($weekCount = 1; $currentStartDate <= $endDate; $weekCount++) {
                $endOfWeek = (clone $currentStartDate)->modify('Sunday this week');
                $endOfWeek = min($endOfWeek, $endDate);
                $dateWeek = $currentStartDate->format('d') . " - " . $endOfWeek->format('d');
                $week[$weekCount] = $dateWeek;
                // dd($currentStartDate);
                // Periksa apakah tanggal pengiriman berada dalam minggu ini
                if ($deliveryDate >= $currentStartDate && $deliveryDate <= $endOfWeek) {

                    // Ambil total jl_mc untuk minggu ini dan jumlahkan jika sudah ada data sebelumnya
                    $dataOrder = [
                        'model' => $mastermodel,
                        'jarum' => $machinetypeid,
                        'area' => $factory,
                        'delivery' => $id['delivery'],
                    ];
                    $jlMc = 0;
                    $jlMcData = $this->produksiModel->getJlMc($dataOrder);

                    // Pastikan data jl_mc ada
                    if ($jlMcData) {
                        // Loop untuk menjumlahkan jl_mc
                        foreach ($jlMcData as $mc) {
                            $jlMc += $mc['jl_mc'];
                        }
                    }
                    $allData[$mastermodel][$machinetypeid][$factory][$weekCount][] = json_encode([
                        'del' => $id['delivery'],
                        'qty' => $qty,
                        'prod' => $produksi,
                        'sisa' => $sisa,
                        'jlMc' => $jlMc,
                    ]);

                    // Hitung total per minggu
                    if (!isset($totalPerWeek[$weekCount])) {
                        $totalPerWeek[$weekCount] = [
                            'totalQty' => 0,
                            'totalProd' => 0,
                            'totalSisa' => 0,
                            'totalJlMc' => 0,
                        ];
                    }
                    $totalPerWeek[$weekCount]['totalQty'] += $qty;
                    $totalPerWeek[$weekCount]['totalProd'] += $produksi;
                    $totalPerWeek[$weekCount]['totalSisa'] += $sisa;
                    $totalPerWeek[$weekCount]['totalJlMc'] += $jlMc;
                }

                // Pindahkan ke minggu berikutnya
                $currentStartDate = (clone $endOfWeek)->modify('+1 day');
            }
        }

        // Proses data per jarum
        $dataPerjarum = $this->ApsPerstyleModel->getBuyerOrderPejarum($buyer, $bulan);
        $allDataPerjarum = [];
        $totalPerWeekJrm = [];
        foreach ($dataPerjarum as $id2) {
            $machinetypeid = $id2['machinetypeid'];
            $delivery = $id2['delivery'];
            $qty = $id2['qty'];
            $sisa = $id2['sisa'];
            $produksi = $qty - $sisa;
            $deliveryDate = new \DateTime($delivery); // Tanggal pengiriman

            $weekCount = 1;
            $currentStartDate = clone $startDate;
            for ($weekCount = 1; $currentStartDate <= $endDate; $weekCount++) {
                $endOfWeek = (clone $currentStartDate)->modify('Sunday this week');
                $endOfWeek = min($endOfWeek, $endDate);

                // Periksa apakah tanggal pengiriman berada dalam minggu ini
                if ($deliveryDate >= $currentStartDate && $deliveryDate <= $endOfWeek) {
                    $jlMcJrm = 0;
                    $dataOrder2 = [
                        'buyer' => $buyer,
                        'jarum' => $machinetypeid,
                        'delivery' => $delivery,
                    ];
                    $jlMcJrmData = $this->produksiModel->getJlMcJrm($dataOrder2);
                    if ($jlMcJrmData) {
                        foreach ($jlMcJrmData as $mcJrm) {
                            $jlMcJrm += $mcJrm['jl_mc'];
                        }
                    }

                    // Pastikan array utama memiliki key jarum
                    if (!isset($allDataPerjarum[$machinetypeid])) {
                        $allDataPerjarum[$machinetypeid] = [];
                    }
                    // Pastikan minggu tersedia
                    if (!isset($allDataPerjarum[$machinetypeid][$weekCount])) {
                        $allDataPerjarum[$machinetypeid][$weekCount] = [
                            'qtyJrm' => 0,
                            'prodJrm' => 0,
                            'sisaJrm' => 0,
                            'jlMcJrm' => 0,
                        ];
                    }

                    // Tambahkan data minggu
                    $allDataPerjarum[$machinetypeid][$weekCount]['qtyJrm'] += $qty;
                    $allDataPerjarum[$machinetypeid][$weekCount]['prodJrm'] += $produksi;
                    $allDataPerjarum[$machinetypeid][$weekCount]['sisaJrm'] += $sisa;
                    $allDataPerjarum[$machinetypeid][$weekCount]['jlMcJrm'] += $jlMcJrm;

                    // Hitung total per minggu
                    if (!isset($totalPerWeekJrm[$weekCount])) {
                        $totalPerWeekJrm[$weekCount] = [
                            'totalQty' => 0,
                            'totalProd' => 0,
                            'totalSisa' => 0,
                            'totalJlMc' => 0,
                        ];
                    }
                    $totalPerWeekJrm[$weekCount]['totalQty'] += $qty;
                    $totalPerWeekJrm[$weekCount]['totalProd'] += $produksi;
                    $totalPerWeekJrm[$weekCount]['totalSisa'] += $sisa;
                    $totalPerWeekJrm[$weekCount]['totalJlMc'] += $jlMcJrm;
                }

                // Pindahkan ke minggu berikutnya
                $currentStartDate = (clone $endOfWeek)->modify('+1 day');
            }
        }
        // dd($allDataPerjarum);
        $maxWeek = $weekCount - 1;

        // Generate Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet()->setTitle("Report Sisa Order");

        $styleTitle = [
            'font' => [
                'bold' => true, // Tebalkan teks
                'color' => ['argb' => 'FF000000'],
                'size' => 30
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER, // Alignment rata tengah
            ],
        ];

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

        // Judul
        $sheet->setCellValue('A1', 'SISA PRODUKSI ' . $buyer . ' Bulan ' . date('F-Y', strtotime($bulan)));

        $row_header = 3;
        $row_header2 = 4;

        // Tambahkan header
        $sheet->setCellValue('A' . $row_header, 'BUYER');
        $sheet->setCellValue('B' . $row_header, 'PDK');
        $sheet->setCellValue('C' . $row_header, 'JARUM');
        $sheet->setCellValue('D' . $row_header, 'AREAL');
        $sheet->mergeCells('A' . $row_header . ':' . 'A' . $row_header2);
        $sheet->getStyle('A' . $row_header . ':' . 'A' . $row_header2)->applyFromArray($styleHeader);
        $sheet->mergeCells('B' . $row_header . ':' . 'B' . $row_header2);
        $sheet->getStyle('B' . $row_header . ':' . 'B' . $row_header2)->applyFromArray($styleHeader);
        $sheet->mergeCells('C' . $row_header . ':' . 'C' . $row_header2);
        $sheet->getStyle('C' . $row_header . ':' . 'C' . $row_header2)->applyFromArray($styleHeader);
        $sheet->mergeCells('D' . $row_header . ':' . 'D' . $row_header2);
        $sheet->getStyle('D' . $row_header . ':' . 'D' . $row_header2)->applyFromArray($styleHeader);

        // looping week
        $col = 'E'; // Kolom awal week
        $col2 = 'I'; // Kolom akhir week

        // Konversi huruf kolom ke nomor indeks kolom
        $col_index = Coordinate::columnIndexFromString($col);
        $col2_index = Coordinate::columnIndexFromString($col2);

        for ($i = 1; $i <= $maxWeek; $i++) {
            $sheet->setCellValue($col . $row_header, 'WEEK ' . $i . '(' . $week[$i] . ')');
            $sheet->mergeCells($col . $row_header . ':' . $col2 . $row_header); // Merge sel antara kolom $col dan $col2
            $sheet->getStyle($col . $row_header . ':' . $col2 . $row_header)->applyFromArray($styleHeader);


            // Tambahkan 2 pada indeks kolom
            $col_index += 5;
            $col2_index = $col_index + 4; // Tambahkan 1 pada indeks kedua kolom

            // Konversi kembali dari nomor indeks kolom ke huruf kolom
            $col = Coordinate::stringFromColumnIndex($col_index);
            $col2 = Coordinate::stringFromColumnIndex($col2_index);
        }
        $col3 = $col;
        $sheet->setCellValue($col3 . $row_header, 'KETERANGAN');
        $sheet->mergeCells($col3 . $row_header . ':' . $col3 . $row_header2);
        $sheet->getStyle($col3 . $row_header . ':' . $col3 . $row_header2)->applyFromArray($styleHeader);


        // Menghitung kolom terakhir untuk menggabungkan judul
        $col_last_index = Coordinate::columnIndexFromString($col3); // Indeks kolom KETERANGAN
        $col_last = Coordinate::stringFromColumnIndex($col_last_index); // Mengonversi indeks kolom ke huruf kolom

        // Style Judul
        $sheet->mergeCells('A1:' . $col_last . '1');
        $sheet->getStyle('A1')->applyFromArray($styleTitle);

        $col4 = 'E';
        // Tambahkan header dinamis untuk tanggal produksi
        for ($i = 1; $i <= $maxWeek; $i++) {
            $sheet->setCellValue($col4 . $row_header2, 'DEL');
            $sheet->getStyle($col4 . $row_header2)->applyFromArray($styleHeader);
            $col4++;
            $sheet->setCellValue($col4 . $row_header2, 'QTY');
            $sheet->getStyle($col4 . $row_header2)->applyFromArray($styleHeader);
            $col4++;
            $sheet->setCellValue($col4 . $row_header2, 'PROD');
            $sheet->getStyle($col4 . $row_header2)->applyFromArray($styleHeader);
            $col4++;
            $sheet->setCellValue($col4 . $row_header2, 'SISA');
            $sheet->getStyle($col4 . $row_header2)->applyFromArray($styleHeader);
            $col4++;
            $sheet->setCellValue($col4 . $row_header2, 'JLN MC');
            $sheet->getStyle($col4 . $row_header2)->applyFromArray($styleHeader);
            $col4++;
        }

        $row = 5;
        $rowsModel = 0;
        foreach ($allData as $noModel => $id) {
            $rowsModel = count($id);
            foreach ($id as $jarum => $rowJarum) {
                $rowsJarums = count($rowJarum);
                if ($rowsJarums > 1) {
                    $rowsModel += $rowsJarums - 1;
                }
                $rowsArea = 0;
                foreach ($rowJarum as $area2 => $rowArea) {
                    for ($i = 1; $i <= $maxWeek; $i++) {
                        if (isset($rowArea[$i])) {
                            $rowsArea = count($rowArea[$i]);
                            $rowDelivery = count($rowArea[$i]);
                            if ($rowDelivery > 1) {
                                $rowsModel += $rowDelivery - 1;
                                $rowsJarums += $rowDelivery - 1;
                            }
                        }
                    }
                }
            }
            $mergeModel = $row + $rowsModel - 1;

            $sheet->setCellValue('A' . $row, $buyer);
            $sheet->setCellValue('B' . $row, $noModel);
            $sheet->mergeCells('A' . $row . ':A' . $mergeModel);
            $sheet->getStyle('A' . $row . ':A' . $mergeModel)->applyFromArray($styleBody);
            $sheet->mergeCells('B' . $row . ':B' . $mergeModel);
            $sheet->getStyle('B' . $row . ':B' . $mergeModel)->applyFromArray($styleBody);

            foreach ($id as $jarum => $id2) {
                $rowsJarum = count($id2);
                if ($rowsJarum > 1) {
                    $rowsModel += $rowsJarum - 1;
                }
                $rowsArea = 0;
                foreach ($id2 as $area2 => $rowArea) {
                    for ($i = 1; $i <= $maxWeek; $i++) {
                        if (isset($rowArea[$i])) {
                            $rowsArea = count($rowArea[$i]);
                            $rowDelivery = count($rowArea[$i]);
                            if ($rowDelivery > 1) {
                                $rowsModel += $rowDelivery - 1;
                                $rowsJarum += $rowDelivery - 1;
                            }
                        }
                    }
                }
                $mergeJarum = $row + $rowsJarum - 1;

                $sheet->setCellValue('C' . $row, $jarum);
                $sheet->mergeCells('C' . $row . ':C' . $mergeJarum);
                $sheet->getStyle('C' . $row . ':C' . $mergeJarum)->applyFromArray($styleBody);

                foreach ($id2 as $area => $id3) {
                    $mergeArea = $row + $rowsArea - 1;
                    $sheet->setCellValue('D' . $row, $area);
                    $sheet->mergeCells('D' . $row . ':D' . $mergeArea);
                    $sheet->getStyle('D' . $row . ':D' . $mergeArea)->applyFromArray($styleBody);

                    $col5 = 'E';
                    for ($i = 1; $i <= $maxWeek; $i++) {
                        if (isset($id3[$i])) {
                            $numRows = count($id3[$i]);
                            $numRows2 = 1;
                            foreach ($id3[$i] as $index => $data) {
                                $parsedData = json_decode($data, true);
                                if ($parsedData) {
                                    $del = $parsedData['del'] ?? 0;
                                    $qty = $parsedData['qty'] ?? 0;
                                    $prod = $parsedData['prod'] ?? 0;
                                    $sisa = $parsedData['sisa'] ?? 0;
                                    $jlMc = $parsedData['jlMc'] ?? 0;


                                    $sheet->setCellValue($col5 . $row, $del);
                                    $sheet->getStyle($col5 . $row)->applyFromArray($styleBody);
                                    $col5++;
                                    $sheet->setCellValue($col5 . $row, $qty);
                                    $sheet->getStyle($col5 . $row)->applyFromArray($styleBody);
                                    $col5++;
                                    $sheet->setCellValue($col5 . $row, $prod !== 0 ? $prod : '-');
                                    $sheet->getStyle($col5 . $row)->applyFromArray($styleBody);
                                    $col5++;
                                    $sheet->setCellValue($col5 . $row, $sisa !== 0 ? $sisa : '-');
                                    $sheet->getStyle($col5 . $row)->applyFromArray($styleBody);
                                    $col5++;
                                    $sheet->setCellValue($col5 . $row, $jlMc !== 0 ? $jlMc : '-');
                                    $sheet->getStyle($col5 . $row)->applyFromArray($styleBody);
                                    $col5++;
                                    // 
                                    $colsEnd = $maxWeek - $i;
                                    $colsStart = $i - 1;
                                    if ($numRows > 1 && $numRows2 < $numRows) {
                                        // untuk kolom setelah week yg terisi
                                        for ($i = 1; $i <= $colsEnd; $i++) {
                                            $sheet->setCellValue($col5 . $row, '');
                                            $sheet->getStyle($col5 . $row)->applyFromArray($styleBody);
                                            $col5++;
                                            $sheet->setCellValue($col5 . $row, '');
                                            $sheet->getStyle($col5 . $row)->applyFromArray($styleBody);
                                            $col5++;
                                            $sheet->setCellValue($col5 . $row, '');
                                            $sheet->getStyle($col5 . $row)->applyFromArray($styleBody);
                                            $col5++;
                                            $sheet->setCellValue($col5 . $row, '');
                                            $sheet->getStyle($col5 . $row)->applyFromArray($styleBody);
                                            $col5++;
                                            $sheet->setCellValue($col5 . $row, '');
                                            $sheet->getStyle($col5 . $row)->applyFromArray($styleBody);
                                            $col5++;
                                            if ($i == $colsEnd) {
                                                $sheet->setCellValue($col5 . $row, '');
                                                $sheet->getStyle($col5 . $row)->applyFromArray($styleBody);
                                                $col5++;
                                            }
                                        }
                                        $row++;
                                        // untuk kolom sebelum week yg terisi
                                        $col_index2 = Coordinate::columnIndexFromString($col5);
                                        $colNext = $col_index2 - (5 * $maxWeek) - 1;
                                        // dd($maxWeek);    
                                        $col5 = Coordinate::stringFromColumnIndex($colNext);
                                        for ($i = 1; $i <= $colsStart; $i++) {
                                            // 
                                            $sheet->setCellValue($col5 . $row, '');
                                            $sheet->getStyle($col5 . $row)->applyFromArray($styleBody);
                                            $col5++;
                                            $sheet->setCellValue($col5 . $row, '');
                                            $sheet->getStyle($col5 . $row)->applyFromArray($styleBody);
                                            $col5++;
                                            $sheet->setCellValue($col5 . $row, '');
                                            $sheet->getStyle($col5 . $row)->applyFromArray($styleBody);
                                            $col5++;
                                            $sheet->setCellValue($col5 . $row, '');
                                            $sheet->getStyle($col5 . $row)->applyFromArray($styleBody);
                                            $col5++;
                                            $sheet->setCellValue($col5 . $row, '');
                                            $sheet->getStyle($col5 . $row)->applyFromArray($styleBody);
                                            $col5++;
                                        }
                                        // dd($col_index2, $colNext, $noModel, $col5);
                                        // dd($col5);
                                    }
                                    $numRows2++;
                                }
                            }
                        } else {
                            $sheet->setCellValue($col5 . $row, '');
                            $sheet->getStyle($col5 . $row)->applyFromArray($styleBody);
                            $col5++;
                            $sheet->setCellValue($col5 . $row, '');
                            $sheet->getStyle($col5 . $row)->applyFromArray($styleBody);
                            $col5++;
                            $sheet->setCellValue($col5 . $row, '');
                            $sheet->getStyle($col5 . $row)->applyFromArray($styleBody);
                            $col5++;
                            $sheet->setCellValue($col5 . $row, '');
                            $sheet->getStyle($col5 . $row)->applyFromArray($styleBody);
                            $col5++;
                            $sheet->setCellValue($col5 . $row, '');
                            $sheet->getStyle($col5 . $row)->applyFromArray($styleBody);
                            $col5++;
                        }
                        $col6 = $col;
                        $sheet->setCellValue($col6 . $row, '');
                        $sheet->getStyle($col6 . $row)->applyFromArray($styleBody);
                    }
                    $row++;
                }
                $row = $mergeJarum + 1;
            }
            $row = $mergeModel + 1;
        }
        // TOTAL

        $sheet->setCellValue('A' . $row, 'TOTAL');
        $sheet->setCellValue('B' . $row, '');
        $sheet->setCellValue('C' . $row, '');
        $sheet->setCellValue('D' . $row, '');
        $sheet->getStyle('A' . $row)->applyFromArray($styleHeader);
        $sheet->getStyle('B' . $row)->applyFromArray($styleHeader);
        $sheet->getStyle('C' . $row)->applyFromArray($styleHeader);
        $sheet->getStyle('D' . $row)->applyFromArray($styleHeader);
        $col6 = 'E';
        for ($i = 1; $i <= $maxWeek; $i++) {
            $sheet->setCellValue($col6 . $row, '');
            $sheet->getStyle($col6 . $row)->applyFromArray($styleHeader);
            $col6++;
            $sheet->setCellValue($col6 . $row, isset($totalPerWeek[$i]['totalQty']) && $totalPerWeek[$i]['totalQty'] != 0 ? $totalPerWeek[$i]['totalQty'] : '-');
            $sheet->getStyle($col6 . $row)->applyFromArray($styleHeader);
            $col6++;
            $sheet->setCellValue($col6 . $row, isset($totalPerWeek[$i]['totalProd']) && $totalPerWeek[$i]['totalProd'] != 0 ? $totalPerWeek[$i]['totalProd'] : '-');
            $sheet->getStyle($col6 . $row)->applyFromArray($styleHeader);
            $col6++;
            $sheet->setCellValue($col6 . $row, isset($totalPerWeek[$i]['totalSisa']) && $totalPerWeek[$i]['totalSisa'] != 0 ? $totalPerWeek[$i]['totalSisa'] : '-');
            $sheet->getStyle($col6 . $row)->applyFromArray($styleHeader);
            $col6++;
            $sheet->setCellValue($col6 . $row, isset($totalPerWeek[$i]['totalJlMc']) && $totalPerWeek[$i]['totalJlMc'] != 0 ? $totalPerWeek[$i]['totalJlMc'] : '-');
            $sheet->getStyle($col6 . $row)->applyFromArray($styleHeader);
            $col6++;
        }
        $col7 = $col6;
        $sheet->setCellValue($col7 . $row, '');
        $sheet->getStyle($col7 . $row)->applyFromArray($styleHeader);

        $rowJudul = $row + 3;
        $row_header3 = $rowJudul + 2;
        $row_header4 = $row_header3 + 1;

        // Judul
        $sheet->setCellValue('A' . $rowJudul, 'SISA PRODUKSI PERJARUM');

        $sheet->setCellValue('A' . $row_header3, 'JARUM');
        $sheet->mergeCells('A' . $row_header3 . ':' . 'A' . $row_header4);
        $sheet->getStyle('A' . $row_header3 . ':' . 'A' . $row_header4)->applyFromArray($styleHeader);

        // Kolom awal untuk looping week
        $col_idx = Coordinate::columnIndexFromString('B');
        $col2_idx = $col_idx + 3;

        for ($i = 1; $i <= $maxWeek; $i++) {
            // Set kolom untuk week ke-i
            $colJrm = Coordinate::stringFromColumnIndex($col_idx);
            $colJrm2 = Coordinate::stringFromColumnIndex($col2_idx);

            $sheet->setCellValue($colJrm . $row_header3, 'WEEK ' . $i);
            $sheet->mergeCells($colJrm . $row_header3 . ':' . $colJrm2 . $row_header3); // Merge sel antara kolom $col dan $col2
            $sheet->getStyle($colJrm . $row_header3 . ':' . $colJrm2 . $row_header3)->applyFromArray($styleHeader);


            // Tambahkan 2 pada indeks kolom
            $col_idx += 4;
            $col2_idx = $col_idx + 3; // Tambahkan 1 pada indeks kedua kolom
        }

        $colEnd = Coordinate::stringFromColumnIndex($col_idx - 1);
        // Style Judul
        $sheet->mergeCells('A' . $rowJudul . ':' . $colEnd . $rowJudul);
        $sheet->getStyle('A' . $rowJudul . ':' . $colEnd . $rowJudul)->applyFromArray($styleTitle);

        $col8 = 'B';
        // Tambahkan header dinamis untuk tanggal produksi
        for ($i = 1; $i <= $maxWeek; $i++) {
            $sheet->setCellValue($col8 . $row_header4, 'QTY');
            $sheet->getStyle($col8 . $row_header4)->applyFromArray($styleHeader);
            $col8++;
            $sheet->setCellValue($col8 . $row_header4, 'PROD');
            $sheet->getStyle($col8 . $row_header4)->applyFromArray($styleHeader);
            $col8++;
            $sheet->setCellValue($col8 . $row_header4, 'SISA');
            $sheet->getStyle($col8 . $row_header4)->applyFromArray($styleHeader);
            $col8++;
            $sheet->setCellValue($col8 . $row_header4, 'JLN MC');
            $sheet->getStyle($col8 . $row_header4)->applyFromArray($styleHeader);
            $col8++;
        }

        $row = $row_header4 + 1;
        foreach ($allDataPerjarum as $jarum => $idJrm) {
            $sheet->setCellValue('A' . $row, $jarum);
            $sheet->getStyle('A' . $row)->applyFromArray($styleBody);
            $col5 = 'B';
            for ($i = 1; $i <= $maxWeek; $i++) {
                // Mengecek apakah week ada di data
                if (isset($idJrm[$i])) {
                    // Ambil data per week
                    $qtyJrm = $idJrm[$i]['qtyJrm'] ?? 0;
                    $prodJrm = $idJrm[$i]['prodJrm'] ?? 0;
                    $sisaJrm = $idJrm[$i]['sisaJrm'] ?? 0;
                    $jlMcJrm = $idJrm[$i]['jlMcJrm'] ?? 0;

                    $sheet->setCellValue($col5 . $row, $qtyJrm !== 0 ? $qtyJrm : '-');
                    $sheet->getStyle($col5 . $row)->applyFromArray($styleBody);
                    $col5++;
                    $sheet->setCellValue($col5 . $row, $prodJrm !== 0 ? $prodJrm : '-');
                    $sheet->getStyle($col5 . $row)->applyFromArray($styleBody);
                    $col5++;
                    $sheet->setCellValue($col5 . $row, $sisaJrm !== 0 ? $sisaJrm : '-');
                    $sheet->getStyle($col5 . $row)->applyFromArray($styleBody);
                    $col5++;
                    $sheet->setCellValue($col5 . $row, $jlMcJrm !== 0 ? $jlMcJrm : '-');
                    $sheet->getStyle($col5 . $row)->applyFromArray($styleBody);
                    $col5++;
                } else {
                    $sheet->setCellValue($col5 . $row, '');
                    $sheet->getStyle($col5 . $row)->applyFromArray($styleBody);
                    $col5++;
                    $sheet->setCellValue($col5 . $row, '');
                    $sheet->getStyle($col5 . $row)->applyFromArray($styleBody);
                    $col5++;
                    $sheet->setCellValue($col5 . $row, '');
                    $sheet->getStyle($col5 . $row)->applyFromArray($styleBody);
                    $col5++;
                    $sheet->setCellValue($col5 . $row, '');
                    $sheet->getStyle($col5 . $row)->applyFromArray($styleBody);
                    $col5++;
                }
            }
            $row++;
        }
        // TOTAL

        $sheet->setCellValue('A' . $row, 'TOTAL');
        $sheet->getStyle('A' . $row)->applyFromArray($styleHeader);

        $col6 = 'B';
        for ($i = 1; $i <= $maxWeek; $i++) {
            $sheet->setCellValue($col6 . $row, isset($totalPerWeekJrm[$i]['totalQty']) && $totalPerWeekJrm[$i]['totalQty'] != 0 ? $totalPerWeekJrm[$i]['totalQty'] : '-');
            $sheet->getStyle($col6 . $row)->applyFromArray($styleHeader);
            $col6++;
            $sheet->setCellValue($col6 . $row, isset($totalPerWeekJrm[$i]['totalProd']) && $totalPerWeekJrm[$i]['totalProd'] != 0 ? $totalPerWeekJrm[$i]['totalProd'] : '-');
            $sheet->getStyle($col6 . $row)->applyFromArray($styleHeader);
            $col6++;
            $sheet->setCellValue($col6 . $row, isset($totalPerWeekJrm[$i]['totalSisa']) && $totalPerWeekJrm[$i]['totalSisa'] != 0 ? $totalPerWeekJrm[$i]['totalSisa'] : '-');
            $sheet->getStyle($col6 . $row)->applyFromArray($styleHeader);
            $col6++;
            $sheet->setCellValue($col6 . $row, isset($totalPerWeekJrm[$i]['totalJlMc']) && $totalPerWeekJrm[$i]['totalJlMc'] != 0 ? $totalPerWeekJrm[$i]['totalJlMc'] : '-');
            $sheet->getStyle($col6 . $row)->applyFromArray($styleHeader);
            $col6++;
        }

        // Set sheet pertama sebagai active sheet
        $spreadsheet->setActiveSheetIndex(0);

        // Export file ke Excel
        $writer = new Xlsx($spreadsheet);
        $filename = 'Sisa Produksi ' . $buyer . ' Bulan ' . date('F-Y', strtotime($bulan)) . '.xlsx';

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    public function excelSisaOrderArea()
    {
        $ar = $this->request->getPost('area') ?: "";
        $role = session()->get('role');
        $month = $this->request->getPost('months');
        $yearss = $this->request->getPost('years');

        // Jika bulan atau tahun tidak diisi, gunakan bulan dan tahun ini
        if (empty($month) || empty($yearss)) {
            $bulan = date('Y-m-01', strtotime('this month')); // Bulan ini
        } else {
            // Atur tanggal berdasarkan input bulan dan tahun dari POST
            $bulan = date('Y-m-01', strtotime("$yearss-$month-01"));
        }

        $role = session()->get('role');
        $data = $this->ApsPerstyleModel->getAreaOrder($ar, $bulan);

        // Ambil tanggal awal dan akhir bulan
        $startDate = new \DateTime($bulan); // Awal bulan
        $startDate->setTime(0, 0, 0);
        $endDate = (clone $startDate)->modify('last day of this month');   // Akhir bulan

        $allData = [];
        $totalPerWeek = []; // Untuk menyimpan total produksi per minggu

        foreach ($data as $id) {
            $buyer = $id['kd_buyer_order'];
            $mastermodel = $id['mastermodel'];
            $machinetypeid = $id['machinetypeid'];
            $factory = $id['factory'];

            // Ambil data qty, sisa, dan produksi
            $qty = $id['qty'];
            $sisa = $id['sisa'];
            $produksi = $qty - $sisa;
            $deliveryDate = new \DateTime($id['delivery']); // Asumsikan ada field delivery

            // Loop untuk membagi data ke dalam minggu
            $weekCount = 1;
            $currentStartDate = clone $startDate;

            for ($weekCount = 1; $currentStartDate <= $endDate; $weekCount++) {
                $endOfWeek = (clone $currentStartDate)->modify('Sunday this week');
                $endOfWeek = min($endOfWeek, $endDate);
                $dateWeek = $currentStartDate->format('d') . " - " . $endOfWeek->format('d');
                $week[$weekCount] = $dateWeek;

                // Periksa apakah tanggal pengiriman berada dalam minggu ini
                if ($deliveryDate >= $currentStartDate && $deliveryDate <= $endOfWeek) {
                    // Ambil total jl_mc untuk minggu ini dan jumlahkan jika sudah ada data sebelumnya
                    $dataOrder = [
                        'model' => $mastermodel,
                        'jarum' => $machinetypeid,
                        'area' => $factory,
                        'delivery' => $id['delivery'],
                    ];
                    $jlMc = 0;
                    $jlMcPlan = 0;
                    $jlMcData = $this->produksiModel->getJlMc($dataOrder);
                    $jlMcPlanning = $this->KebutuhanAreaModel->getJlMcPlanning($dataOrder);

                    // Pastikan data jl_mc ada
                    if ($jlMcData) {
                        // Loop untuk menjumlahkan jl_mc
                        foreach ($jlMcData as $mc) {
                            $jlMc += $mc['jl_mc'];
                        }
                    }
                    if ($jlMcPlanning) {
                        // Loop untuk menjumlahkan jl_mc
                        foreach ($jlMcPlanning as $mcPlan) {
                            $jlMcPlan += $mcPlan['mesin'];
                        }
                    }

                    $allData[$mastermodel][$machinetypeid][$factory][$weekCount][] = json_encode([
                        'del'       => $id['delivery'],
                        'qty'       => $qty,
                        'prod'      => $produksi,
                        'sisa'      => $sisa,
                        'jlMc'      => $jlMc,
                        'jlMcPlan'  => $jlMcPlan,
                        'buyer'     => $buyer,
                    ]);

                    // Hitung total per minggu
                    if (!isset($totalPerWeek[$weekCount])) {
                        $totalPerWeek[$weekCount] = [
                            'totalQty' => 0,
                            'totalProd' => 0,
                            'totalSisa' => 0,
                            'totalJlMc' => 0,
                            'totalJlMcPlan' => 0,
                        ];
                    }
                    $totalPerWeek[$weekCount]['totalQty'] += $qty;
                    $totalPerWeek[$weekCount]['totalProd'] += $produksi;
                    $totalPerWeek[$weekCount]['totalSisa'] += $sisa;
                    $totalPerWeek[$weekCount]['totalJlMc'] += $jlMc;
                    $totalPerWeek[$weekCount]['totalJlMcPlan'] += $jlMcPlan;
                }

                // Pindahkan ke minggu berikutnya
                $currentStartDate = (clone $endOfWeek)->modify('+1 day');
            }
        }

        $allDataPerjarum = [];
        $totalPerWeekJrm = []; // Total per minggu
        $dataPerjarum = $this->ApsPerstyleModel->getAreaOrderPejarum($ar, $bulan);

        foreach ($dataPerjarum as $id2) {
            $machinetypeid = $id2['machinetypeid'];
            $delivery = $id2['delivery'];
            $qty = $id2['qty'];
            $sisa = $id2['sisa'];
            $produksi = $qty - $sisa;
            $deliveryDate = new \DateTime($delivery); // Tanggal pengiriman

            $weekCount = 1;
            $currentStartDate = clone $startDate;
            for ($weekCount = 1; $currentStartDate <= $endDate; $weekCount++) {
                $endOfWeek = (clone $currentStartDate)->modify('Sunday this week');
                $endOfWeek = min($endOfWeek, $endDate);

                // Periksa apakah tanggal pengiriman berada dalam minggu ini
                if ($deliveryDate >= $currentStartDate && $deliveryDate <= $endOfWeek) {
                    $jlMcJrm = 0;
                    $dataOrder2 = [
                        'area' => $ar,
                        'jarum' => $machinetypeid,
                        'delivery' => $delivery,
                    ];
                    $jlMcJrmData = $this->produksiModel->getJlMcJrmArea($dataOrder2);
                    if ($jlMcJrmData) {
                        foreach ($jlMcJrmData as $mcJrm) {
                            $jlMcJrm += $mcJrm['jl_mc'];
                        }
                    }

                    // Pastikan array utama memiliki key jarum
                    if (!isset($allDataPerjarum[$machinetypeid])) {
                        $allDataPerjarum[$machinetypeid] = [];
                    }
                    // Pastikan minggu tersedia
                    if (!isset($allDataPerjarum[$machinetypeid][$weekCount])) {
                        $allDataPerjarum[$machinetypeid][$weekCount] = [
                            'qtyJrm' => 0,
                            'prodJrm' => 0,
                            'sisaJrm' => 0,
                            'jlMcJrm' => 0,
                        ];
                    }

                    // Tambahkan data minggu
                    $allDataPerjarum[$machinetypeid][$weekCount]['qtyJrm'] += $qty;
                    $allDataPerjarum[$machinetypeid][$weekCount]['prodJrm'] += $produksi;
                    $allDataPerjarum[$machinetypeid][$weekCount]['sisaJrm'] += $sisa;
                    $allDataPerjarum[$machinetypeid][$weekCount]['jlMcJrm'] += $jlMcJrm;

                    // Hitung total per minggu
                    if (!isset($totalPerWeekJrm[$weekCount])) {
                        $totalPerWeekJrm[$weekCount] = [
                            'totalQty' => 0,
                            'totalProd' => 0,
                            'totalSisa' => 0,
                            'totalJlMc' => 0,
                        ];
                    }
                    $totalPerWeekJrm[$weekCount]['totalQty'] += $qty;
                    $totalPerWeekJrm[$weekCount]['totalProd'] += $produksi;
                    $totalPerWeekJrm[$weekCount]['totalSisa'] += $sisa;
                    $totalPerWeekJrm[$weekCount]['totalJlMc'] += $jlMcJrm;
                }

                // Pindahkan ke minggu berikutnya
                $currentStartDate = (clone $endOfWeek)->modify('+1 day');
            }
        }
        $maxWeek = $weekCount - 1;

        // Generate Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet()->setTitle("Report Sisa Order");

        $styleTitle = [
            'font' => [
                'bold' => true, // Tebalkan teks
                'color' => ['argb' => 'FF000000'],
                'size' => 30
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER, // Alignment rata tengah
            ],
        ];

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

        // Judul
        $sheet->setCellValue('A1', 'SISA PRODUKSI ' . $ar . ' Bulan ' . date('F', strtotime($bulan)));

        $row_header = 3;
        $row_header2 = 4;

        // Tambahkan header
        $sheet->setCellValue('A' . $row_header, 'BUYER');
        $sheet->setCellValue('B' . $row_header, 'PDK');
        $sheet->setCellValue('C' . $row_header, 'JARUM');
        $sheet->setCellValue('D' . $row_header, 'AREAL');
        $sheet->mergeCells('A' . $row_header . ':' . 'A' . $row_header2);
        $sheet->getStyle('A' . $row_header . ':' . 'A' . $row_header2)->applyFromArray($styleHeader);
        $sheet->mergeCells('B' . $row_header . ':' . 'B' . $row_header2);
        $sheet->getStyle('B' . $row_header . ':' . 'B' . $row_header2)->applyFromArray($styleHeader);
        $sheet->mergeCells('C' . $row_header . ':' . 'C' . $row_header2);
        $sheet->getStyle('C' . $row_header . ':' . 'C' . $row_header2)->applyFromArray($styleHeader);
        $sheet->mergeCells('D' . $row_header . ':' . 'D' . $row_header2);
        $sheet->getStyle('D' . $row_header . ':' . 'D' . $row_header2)->applyFromArray($styleHeader);

        // looping week
        $col = 'E'; // Kolom awal week
        $col2 = 'J'; // Kolom akhir week

        // Konversi huruf kolom ke nomor indeks kolom
        $col_index = Coordinate::columnIndexFromString($col);
        $col2_index = Coordinate::columnIndexFromString($col2);

        for ($i = 1; $i <= $maxWeek; $i++) {
            $sheet->setCellValue($col . $row_header, 'WEEK ' . $i . ' (' . $week[$i] . ')');
            $sheet->mergeCells($col . $row_header . ':' . $col2 . $row_header); // Merge sel antara kolom $col dan $col2
            $sheet->getStyle($col . $row_header . ':' . $col2 . $row_header)->applyFromArray($styleHeader);


            // Tambahkan 2 pada indeks kolom
            $col_index += 6;
            $col2_index = $col_index + 5; // Tambahkan 1 pada indeks kedua kolom

            // Konversi kembali dari nomor indeks kolom ke huruf kolom
            $col = Coordinate::stringFromColumnIndex($col_index);
            $col2 = Coordinate::stringFromColumnIndex($col2_index);
        }
        $col3 = $col;
        $sheet->setCellValue($col3 . $row_header, 'KETERANGAN');
        $sheet->mergeCells($col3 . $row_header . ':' . $col3 . $row_header2);
        $sheet->getStyle($col3 . $row_header . ':' . $col3 . $row_header2)->applyFromArray($styleHeader);


        // Menghitung kolom terakhir untuk menggabungkan judul
        $col_last_index = Coordinate::columnIndexFromString($col3); // Indeks kolom KETERANGAN
        $col_last = Coordinate::stringFromColumnIndex($col_last_index); // Mengonversi indeks kolom ke huruf kolom

        // Style Judul
        $sheet->mergeCells('A1:' . $col_last . '1');
        $sheet->getStyle('A1')->applyFromArray($styleTitle);

        $col4 = 'E';
        // Tambahkan header dinamis untuk tanggal produksi
        for ($i = 1; $i <= $maxWeek; $i++) {
            $sheet->setCellValue($col4 . $row_header2, 'DEL');
            $sheet->getStyle($col4 . $row_header2)->applyFromArray($styleHeader);
            $col4++;
            $sheet->setCellValue($col4 . $row_header2, 'QTY');
            $sheet->getStyle($col4 . $row_header2)->applyFromArray($styleHeader);
            $col4++;
            $sheet->setCellValue($col4 . $row_header2, 'PROD');
            $sheet->getStyle($col4 . $row_header2)->applyFromArray($styleHeader);
            $col4++;
            $sheet->setCellValue($col4 . $row_header2, 'SISA');
            $sheet->getStyle($col4 . $row_header2)->applyFromArray($styleHeader);
            $col4++;
            $sheet->setCellValue($col4 . $row_header2, 'JLN MC ACT');
            $sheet->getStyle($col4 . $row_header2)->applyFromArray($styleHeader);
            $col4++;
            $sheet->setCellValue($col4 . $row_header2, 'JLN MC PLAN');
            $sheet->getStyle($col4 . $row_header2)->applyFromArray($styleHeader);
            $col4++;
        }

        // dd($allData);
        $row = 5;
        $rowsModel = 0;
        foreach ($allData as $noModel => $id) {
            $rowsModel = count($id);
            $rowsJarum = 0;
            foreach ($id as $jarum => $rowJarum) {
                $rowsJarum = count($rowJarum);
                if ($rowsJarum > 1) {
                    $rowsModel += $rowsJarum - 1;
                }
                $rowsArea = 0;
                foreach ($rowJarum as $area => $rowArea) {
                    for ($i = 1; $i <= $maxWeek; $i++) {
                        if (isset($rowArea[$i])) {
                            $rowsArea = count($rowArea[$i]);
                            $rowDelivery = count($rowArea[$i]);
                            if ($rowDelivery > 1) {
                                $rowsModel += $rowDelivery - 1;
                                $rowsJarum += $rowDelivery - 1;
                            }
                            // Extract buyer data from the rowArea
                            foreach ($rowArea[$i] as $entry) {
                                // Decode the JSON to get the original array
                                $entryData = json_decode($entry, true);
                                if (isset($entryData['buyer'])) {
                                    $buyer = $entryData['buyer'];  // Get the buyer data
                                    break; // If you only need the buyer from the first entry
                                }
                            }
                        }
                    }
                }
            }
            $mergeModel = $row + $rowsModel - 1;
            $sheet->setCellValue('A' . $row, $buyer);
            $sheet->setCellValue('B' . $row, $noModel);
            $sheet->mergeCells('A' . $row . ':A' . $mergeModel);
            $sheet->getStyle('A' . $row . ':A' . $mergeModel)->applyFromArray($styleBody);
            $sheet->mergeCells('B' . $row . ':B' . $mergeModel);
            $sheet->getStyle('B' . $row . ':B' . $mergeModel)->applyFromArray($styleBody);

            foreach ($id as $jarum => $id2) {

                $mergeJarum = $row + $rowsJarum - 1;

                $sheet->setCellValue('C' . $row, $jarum);
                $sheet->mergeCells('C' . $row . ':C' . $mergeJarum);
                $sheet->getStyle('C' . $row . ':C' . $mergeJarum)->applyFromArray($styleBody);

                foreach ($id2 as $area => $id3) {
                    $mergeArea = $row + $rowsArea - 1;
                    $sheet->setCellValue('D' . $row, $area);
                    $sheet->mergeCells('D' . $row . ':D' . $mergeJarum);
                    $sheet->getStyle('D' . $row . ':D' . $mergeJarum)->applyFromArray($styleBody);

                    $col5 = 'E';
                    for ($i = 1; $i <= $maxWeek; $i++) {
                        if (isset($id3[$i])) {
                            $numRows = count($id3[$i]);
                            $numRows2 = 1;
                            foreach ($id3[$i] as $index => $data) {
                                $parsedData = json_decode($data, true);
                                if ($parsedData) {
                                    // Ambil data per week
                                    $del = $parsedData['del'] ?? 0;
                                    $qty = $parsedData['qty'] ?? 0;
                                    $prod = $parsedData['prod'] ?? 0;
                                    $sisa = $parsedData['sisa'] ?? 0;
                                    $jlMc = $parsedData['jlMc'] ?? 0;
                                    $jlMcPlan = $parsedData['jlMcPlan'] ?? 0;

                                    $sheet->setCellValue($col5 . $row, $del);
                                    $sheet->getStyle($col5 . $row)->applyFromArray($styleBody);
                                    $col5++;
                                    $sheet->setCellValue($col5 . $row, $qty);
                                    $sheet->getStyle($col5 . $row)->applyFromArray($styleBody);
                                    $col5++;
                                    $sheet->setCellValue($col5 . $row, $prod !== 0 ? $prod : '-');
                                    $sheet->getStyle($col5 . $row)->applyFromArray($styleBody);
                                    $col5++;
                                    $sheet->setCellValue($col5 . $row, $sisa !== 0 ? $sisa : '-');
                                    $sheet->getStyle($col5 . $row)->applyFromArray($styleBody);
                                    $col5++;
                                    $sheet->setCellValue($col5 . $row, $jlMc !== 0 ? $jlMc : '-');
                                    $sheet->getStyle($col5 . $row)->applyFromArray($styleBody);
                                    $col5++;
                                    $sheet->setCellValue($col5 . $row, $jlMcPlan !== 0 ? $jlMcPlan : '-');
                                    $sheet->getStyle($col5 . $row)->applyFromArray($styleBody);
                                    $col5++;
                                    // 
                                    $colsEnd = $maxWeek - $i;
                                    $colsStart = $i - 1;
                                    if ($numRows > 1 && $numRows2 < $numRows) {
                                        // Konversi huruf kolom ke nomor indeks kolom
                                        for ($i = 1; $i <= $colsEnd; $i++) {
                                            $sheet->setCellValue($col5 . $row, '');
                                            $sheet->getStyle($col5 . $row)->applyFromArray($styleBody);
                                            $col5++;
                                            $sheet->setCellValue($col5 . $row, '');
                                            $sheet->getStyle($col5 . $row)->applyFromArray($styleBody);
                                            $col5++;
                                            $sheet->setCellValue($col5 . $row, '');
                                            $sheet->getStyle($col5 . $row)->applyFromArray($styleBody);
                                            $col5++;
                                            $sheet->setCellValue($col5 . $row, '');
                                            $sheet->getStyle($col5 . $row)->applyFromArray($styleBody);
                                            $col5++;
                                            $sheet->setCellValue($col5 . $row, '');
                                            $sheet->getStyle($col5 . $row)->applyFromArray($styleBody);
                                            $col5++;
                                            $sheet->setCellValue($col5 . $row, '');
                                            $sheet->getStyle($col5 . $row)->applyFromArray($styleBody);
                                            $col5++;
                                            if ($i == $colsEnd) {
                                                $sheet->setCellValue($col5 . $row, '');
                                                $sheet->getStyle($col5 . $row)->applyFromArray($styleBody);
                                                $col5++;
                                            }
                                        }
                                        $row++;
                                        for ($i = 1; $i <= $colsStart; $i++) {
                                            $col_index2 = Coordinate::columnIndexFromString($col5);
                                            $colNext = $col_index2 - (5 * $maxWeek) + 1;
                                            $col5 = Coordinate::stringFromColumnIndex($colNext);
                                            $sheet->setCellValue($col5 . $row, '');
                                            $sheet->getStyle($col5 . $row)->applyFromArray($styleBody);
                                            $col5++;
                                            $sheet->setCellValue($col5 . $row, '');
                                            $sheet->getStyle($col5 . $row)->applyFromArray($styleBody);
                                            $col5++;
                                            $sheet->setCellValue($col5 . $row, '');
                                            $sheet->getStyle($col5 . $row)->applyFromArray($styleBody);
                                            $col5++;
                                            $sheet->setCellValue($col5 . $row, '');
                                            $sheet->getStyle($col5 . $row)->applyFromArray($styleBody);
                                            $col5++;
                                            $sheet->setCellValue($col5 . $row, '');
                                            $sheet->getStyle($col5 . $row)->applyFromArray($styleBody);
                                            $col5++;
                                            $sheet->setCellValue($col5 . $row, '');
                                            $sheet->getStyle($col5 . $row)->applyFromArray($styleBody);
                                            $col5++;
                                        }
                                        // dd($col5);
                                    }
                                    $numRows2++;
                                }
                            }
                        } else {
                            $sheet->setCellValue($col5 . $row, '');
                            $sheet->getStyle($col5 . $row)->applyFromArray($styleBody);
                            $col5++;
                            $sheet->setCellValue($col5 . $row, '');
                            $sheet->getStyle($col5 . $row)->applyFromArray($styleBody);
                            $col5++;
                            $sheet->setCellValue($col5 . $row, '');
                            $sheet->getStyle($col5 . $row)->applyFromArray($styleBody);
                            $col5++;
                            $sheet->setCellValue($col5 . $row, '');
                            $sheet->getStyle($col5 . $row)->applyFromArray($styleBody);
                            $col5++;
                            $sheet->setCellValue($col5 . $row, '');
                            $sheet->getStyle($col5 . $row)->applyFromArray($styleBody);
                            $col5++;
                            $sheet->setCellValue($col5 . $row, '');
                            $sheet->getStyle($col5 . $row)->applyFromArray($styleBody);
                            $col5++;
                        }
                        $col6 = $col;
                        $sheet->setCellValue($col6 . $row, '');
                        $sheet->getStyle($col6 . $row)->applyFromArray($styleBody);
                    }
                    $row++;
                }
                $row = $mergeJarum + 1;
            }
            $row = $mergeModel + 1;
        }
        // TOTAL

        $sheet->setCellValue('A' . $row, 'TOTAL');
        $sheet->setCellValue('B' . $row, '');
        $sheet->setCellValue('C' . $row, '');
        $sheet->setCellValue('D' . $row, '');
        $sheet->getStyle('A' . $row)->applyFromArray($styleHeader);
        $sheet->getStyle('B' . $row)->applyFromArray($styleHeader);
        $sheet->getStyle('C' . $row)->applyFromArray($styleHeader);
        $sheet->getStyle('D' . $row)->applyFromArray($styleHeader);
        $col6 = 'E';
        for ($i = 1; $i <= $maxWeek; $i++) {
            $sheet->setCellValue($col6 . $row, '');
            $sheet->getStyle($col6 . $row)->applyFromArray($styleHeader);
            $col6++;
            $sheet->setCellValue($col6 . $row, isset($totalPerWeek[$i]['totalQty']) && $totalPerWeek[$i]['totalQty'] != 0 ? $totalPerWeek[$i]['totalQty'] : '-');
            $sheet->getStyle($col6 . $row)->applyFromArray($styleHeader);
            $col6++;
            $sheet->setCellValue($col6 . $row, isset($totalPerWeek[$i]['totalProd']) && $totalPerWeek[$i]['totalProd'] != 0 ? $totalPerWeek[$i]['totalProd'] : '-');
            $sheet->getStyle($col6 . $row)->applyFromArray($styleHeader);
            $col6++;
            $sheet->setCellValue($col6 . $row, isset($totalPerWeek[$i]['totalSisa']) && $totalPerWeek[$i]['totalSisa'] != 0 ? $totalPerWeek[$i]['totalSisa'] : '-');
            $sheet->getStyle($col6 . $row)->applyFromArray($styleHeader);
            $col6++;
            $sheet->setCellValue($col6 . $row, isset($totalPerWeek[$i]['totalJlMc']) && $totalPerWeek[$i]['totalJlMc'] != 0 ? $totalPerWeek[$i]['totalJlMc'] : '-');
            $sheet->getStyle($col6 . $row)->applyFromArray($styleHeader);
            $col6++;
            $sheet->setCellValue($col6 . $row, isset($totalPerWeek[$i]['totalJlMcPlan']) && $totalPerWeek[$i]['totalJlMcPlan'] != 0 ? $totalPerWeek[$i]['totalJlMcPlan'] : '-');
            $sheet->getStyle($col6 . $row)->applyFromArray($styleHeader);
            $col6++;
        }
        $col7 = $col6;
        $sheet->setCellValue($col7 . $row, '');
        $sheet->getStyle($col7 . $row)->applyFromArray($styleHeader);

        $rowJudul = $row + 3;
        $row_header3 = $rowJudul + 2;
        $row_header4 = $row_header3 + 1;

        // Judul
        $sheet->setCellValue('A' . $rowJudul, 'SISA PRODUKSI PERJARUM');

        $sheet->setCellValue('A' . $row_header3, 'JARUM');
        $sheet->mergeCells('A' . $row_header3 . ':' . 'A' . $row_header4);
        $sheet->getStyle('A' . $row_header3 . ':' . 'A' . $row_header4)->applyFromArray($styleHeader);

        // Kolom awal untuk looping week
        $col_idx = Coordinate::columnIndexFromString('B');
        $col2_idx = $col_idx + 3;

        for ($i = 1; $i <= $maxWeek; $i++) {
            // Set kolom untuk week ke-i
            $colJrm = Coordinate::stringFromColumnIndex($col_idx);
            $colJrm2 = Coordinate::stringFromColumnIndex($col2_idx);

            $sheet->setCellValue($colJrm . $row_header3, 'WEEK ' . $i . ' (' . $week[$i] . ')');
            $sheet->mergeCells($colJrm . $row_header3 . ':' . $colJrm2 . $row_header3); // Merge sel antara kolom $col dan $col2
            $sheet->getStyle($colJrm . $row_header3 . ':' . $colJrm2 . $row_header3)->applyFromArray($styleHeader);


            // Tambahkan 2 pada indeks kolom
            $col_idx += 4;
            $col2_idx = $col_idx + 3; // Tambahkan 1 pada indeks kedua kolom
        }

        $colEnd = Coordinate::stringFromColumnIndex($col_idx - 1);
        // Style Judul
        $sheet->mergeCells('A' . $rowJudul . ':' . $colEnd . $rowJudul);
        $sheet->getStyle('A' . $rowJudul . ':' . $colEnd . $rowJudul)->applyFromArray($styleTitle);

        $col8 = 'B';
        // Tambahkan header dinamis untuk tanggal produksi
        for ($i = 1; $i <= $maxWeek; $i++) {
            $sheet->setCellValue($col8 . $row_header4, 'QTY');
            $sheet->getStyle($col8 . $row_header4)->applyFromArray($styleHeader);
            $col8++;
            $sheet->setCellValue($col8 . $row_header4, 'PROD');
            $sheet->getStyle($col8 . $row_header4)->applyFromArray($styleHeader);
            $col8++;
            $sheet->setCellValue($col8 . $row_header4, 'SISA');
            $sheet->getStyle($col8 . $row_header4)->applyFromArray($styleHeader);
            $col8++;
            $sheet->setCellValue($col8 . $row_header4, 'JLN MC');
            $sheet->getStyle($col8 . $row_header4)->applyFromArray($styleHeader);
            $col8++;
        }

        $row = $row_header4 + 1;
        foreach ($allDataPerjarum as $jarum => $idJrm) {
            $sheet->setCellValue('A' . $row, $jarum);
            $sheet->getStyle('A' . $row)->applyFromArray($styleBody);
            $col5 = 'B';
            for ($i = 1; $i <= $maxWeek; $i++) {
                // Mengecek apakah week ada di data
                if (isset($idJrm[$i])) {
                    // Ambil data per week
                    $qtyJrm = $idJrm[$i]['qtyJrm'] ?? 0;
                    $prodJrm = $idJrm[$i]['prodJrm'] ?? 0;
                    $sisaJrm = $idJrm[$i]['sisaJrm'] ?? 0;
                    $jlMcJrm = $idJrm[$i]['jlMcJrm'] ?? 0;

                    $sheet->setCellValue($col5 . $row, $qtyJrm !== 0 ? $qtyJrm : '-');
                    $sheet->getStyle($col5 . $row)->applyFromArray($styleBody);
                    $col5++;
                    $sheet->setCellValue($col5 . $row, $prodJrm !== 0 ? $prodJrm : '-');
                    $sheet->getStyle($col5 . $row)->applyFromArray($styleBody);
                    $col5++;
                    $sheet->setCellValue($col5 . $row, $sisaJrm !== 0 ? $sisaJrm : '-');
                    $sheet->getStyle($col5 . $row)->applyFromArray($styleBody);
                    $col5++;
                    $sheet->setCellValue($col5 . $row, $jlMcJrm !== 0 ? $jlMcJrm : '-');
                    $sheet->getStyle($col5 . $row)->applyFromArray($styleBody);
                    $col5++;
                } else {
                    $sheet->setCellValue($col5 . $row, '');
                    $sheet->getStyle($col5 . $row)->applyFromArray($styleBody);
                    $col5++;
                    $sheet->setCellValue($col5 . $row, '');
                    $sheet->getStyle($col5 . $row)->applyFromArray($styleBody);
                    $col5++;
                    $sheet->setCellValue($col5 . $row, '');
                    $sheet->getStyle($col5 . $row)->applyFromArray($styleBody);
                    $col5++;
                    $sheet->setCellValue($col5 . $row, '');
                    $sheet->getStyle($col5 . $row)->applyFromArray($styleBody);
                    $col5++;
                }
            }
            $row++;
        }
        // TOTAL

        $sheet->setCellValue('A' . $row, 'TOTAL');
        $sheet->getStyle('A' . $row)->applyFromArray($styleHeader);

        $col6 = 'B';
        for ($i = 1; $i <= $maxWeek; $i++) {
            $sheet->setCellValue($col6 . $row, isset($totalPerWeekJrm[$i]['totalQty']) && $totalPerWeekJrm[$i]['totalQty'] != 0 ? $totalPerWeekJrm[$i]['totalQty'] : '-');
            $sheet->getStyle($col6 . $row)->applyFromArray($styleHeader);
            $col6++;
            $sheet->setCellValue($col6 . $row, isset($totalPerWeekJrm[$i]['totalProd']) && $totalPerWeekJrm[$i]['totalProd'] != 0 ? $totalPerWeekJrm[$i]['totalProd'] : '-');
            $sheet->getStyle($col6 . $row)->applyFromArray($styleHeader);
            $col6++;
            $sheet->setCellValue($col6 . $row, isset($totalPerWeekJrm[$i]['totalSisa']) && $totalPerWeekJrm[$i]['totalSisa'] != 0 ? $totalPerWeekJrm[$i]['totalSisa'] : '-');
            $sheet->getStyle($col6 . $row)->applyFromArray($styleHeader);
            $col6++;
            $sheet->setCellValue($col6 . $row, isset($totalPerWeekJrm[$i]['totalJlMc']) && $totalPerWeekJrm[$i]['totalJlMc'] != 0 ? $totalPerWeekJrm[$i]['totalJlMc'] : '-');
            $sheet->getStyle($col6 . $row)->applyFromArray($styleHeader);
            $col6++;
        }

        // Set sheet pertama sebagai active sheet
        $spreadsheet->setActiveSheetIndex(0);

        // Export file ke Excel
        $writer = new Xlsx($spreadsheet);
        $filename = 'Sisa Produksi ' . $ar . ' Bulan ' . date('F', strtotime($bulan)) . '.xlsx';

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }
    public function exportEstimasispk()
    {
        $rows = $this->request->getPost('data');

        if (!empty($rows)) {
            $allData = [];

            foreach ($rows as $row) {
                // Validasi minimal field penting
                if (!isset($row['model'], $row['size'], $row['area'])) {
                    continue;
                }

                // Siapkan data untuk query ke model
                $data = [
                    'model'    => $row['model'],
                    'size'     => $row['size'],
                    'area'     => $row['area'],
                    'po_plus'  => (int)$row['poplus'] ?? 0,
                    'estimasi' => (int)$row['estimasi'] ?? 0,
                ];

                // Ambil data estimasi dari model
                $result = $this->ApsPerstyleModel->exportDataEstimasi($data);

                if (!$result) {
                    continue; // Lewati jika tidak ada hasil
                }

                // Ambil data produksi
                $dataProd = $this->produksiModel->getProdByPdkSize($result['mastermodel'], $result['size']);
                $bs       = (int)($dataProd['bs'] ?? 0);
                $ttlProd  = (int)($dataProd['prod'] ?? 0);
                $qty      = (int)($result['qty'] ?? 0);
                $sisa     = (int)($result['sisa'] ?? 0);
                $poplus   = (int)($row['poplus'] ?? 0);
                $estimasi = (int)($row['estimasi'] ?? 0);

                if ($ttlProd <= 0 || $qty <= 0) {
                    continue; // Lewati jika tidak valid
                }

                $percentage = round(($ttlProd / $qty) * 100);
                $estimasiQty = round($estimasi); // Perhitungan sesuai kebutuhan

                $allData[] = [
                    'model'      => $result['mastermodel'],
                    'inisial'    => $result['inisial'],
                    'size'       => $result['size'],
                    'sisa'       => $sisa,
                    'qty'        => $qty,
                    'ttlProd'    => $ttlProd,
                    'percentage' => $percentage,
                    'bs'         => $bs,
                    'poplus'     => $poplus,
                    'jarum'      => $result['machinetypeid'],
                    'estimasi'   => $estimasiQty,
                ];

                // Simpan ke tabel estimasi SPK
                $this->estspk->insert([
                    'model'  => $result['mastermodel'],
                    'style'  => $result['size'],
                    'area'   => $row['area'],
                    'qty'    => $estimasiQty,
                    'status' => 'sudah'
                ]);
            }
            // var_dump($allData);
            // dd($allData);
            // Export Excel
            // Buat file Excel
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $styleTitle = [
                'font' => [
                    'bold' => true, // Tebalkan teks
                    'color' => ['argb' => 'FF000000'],
                    'size' => 15
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER, // Alignment rata tengah
                ],
            ];

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

            $sheet->setCellValue('A1', 'ESTIMASI QTY SPK 2');
            $sheet->mergeCells('A1:C1');
            $sheet->getStyle('A1:C1')->applyFromArray($styleTitle);
            // Tulis header
            $sheet->setCellValue('A3', 'NO MODEL');
            $sheet->setCellValue('B3', 'STYLE');
            $sheet->setCellValue('C3', 'QTY SPK 2');
            $sheet->getStyle('A3')->applyFromArray($styleHeader);
            $sheet->getStyle('B3')->applyFromArray($styleHeader);
            $sheet->getStyle('C3')->applyFromArray($styleHeader);

            // Tulis data mulai dari baris 2
            $row = 4;
            foreach ($allData as $item) {
                $sheet->setCellValue('A' . $row, $item['model']);
                $sheet->setCellValue('B' . $row, $item['size']);
                $sheet->setCellValue('C' . $row, $item['estimasi']);
                $sheet->getStyle('A' . $row)->applyFromArray($styleBody);
                $sheet->getStyle('B' . $row)->applyFromArray($styleBody);
                $sheet->getStyle('C' . $row)->applyFromArray($styleBody);
                $row++;
            }

            // Buat writer dan output file Excel
            $writer = new Xlsx($spreadsheet);
            $fileName = 'Export Estimasi SPK.xlsx';

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $fileName . '"');
            header('Cache-Control: max-age=0');

            $writer->save('php://output');
            exit;
        } else {
            return redirect()->back()->with('error', 'Tidak ada data yang dipilih.');
        }
    }
    public function exportExcelRetur($area)
    {

        if (empty($area)) {
            $area = $this->request->getGet('area') ?? '';
        }
        $tglRetur = $this->request->getGet('tgl_retur') ?? '';
        // dd($area, $tglRetur);
        $list = [];

        // Kalau area dipilih, baru ambil data listRetur
        if (!empty($area)) {
            $listReturUrl = api_url('material') . 'listRetur/' . $area . '?tglBuat=' . $tglRetur;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $listReturUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            if ($httpCode === 200 && $response !== false) {
                $list = json_decode($response, true);
            } else {
                log_message('error', "API listRetur gagal. URL: $listReturUrl | HTTP Code: $httpCode | Error: $error");
                $list = []; // supaya tidak error di view
            }
        }

        if (!empty($list)) {
            $listRetur = $list['listRetur'] ?? [];
            $material  = $list['material'] ?? [];
            $kirim     = $list['kirim'] ?? [];
            $poPlus    = $list['poPlus'] ?? [];

            if (!empty($material)) {
                // 🔹 Ambil semua key untuk query massal
                $noModels = array_unique(array_column($material, 'no_model'));
                $sizes = array_unique(array_column($material, 'style_size'));

                // 🔹 Query massal (1x per jenis data)
                $qtyOrderList = $this->ApsPerstyleModel->getAllSisaPerSize($area, $noModels, $sizes);
                $bsMesinList  = $this->bsMesinModel->getAllBsMesin($area, $noModels, $sizes);
                $idApsList    = $this->ApsPerstyleModel->getAllIdForBs($area, $noModels, $sizes);

                // ambil semua id aps
                $allIds = [];
                foreach ($idApsList as $arr) {
                    foreach ($arr as $id) $allIds[] = $id;
                }

                $bsSettingAll = $this->bsModel->getAllTotalBsSet($allIds);
                $prodAll      = $this->produksiModel->getAllProd($allIds);

                // 🔹 Siapkan hasil kalkulasi
                $materialIndex = [];
                foreach ($material as $item) {
                    $noModel = $item['no_model'];
                    $style   = $item['style_size'];
                    $keyBase = $noModel . '|' . $item['item_type'] . '|' . $item['kode_warna'];
                    $keyQty  = $noModel . '|' . $style;

                    $composition = (float)($item['composition'] ?? 0);
                    $gw = (float)($item['gw'] ?? 0);
                    $loss = (float)($item['loss'] ?? 0);

                    // --- ambil hasil dari array index ---
                    $qty_order = $qtyOrderList[$keyQty] ?? 0;
                    $bsGram = $bsMesinList[$keyQty] ?? 0;
                    $idaps = $idApsList[$keyQty] ?? [];

                    // BS Setting
                    $bsSettingPcs = 0;
                    $prodPcs = 0;
                    foreach ($idaps as $id) {
                        $bsSettingPcs += $bsSettingAll[$id] ?? 0;
                        $prodPcs += $prodAll[$id] ?? 0;
                    }

                    // hitung konversi
                    $kgPo = ($qty_order * $composition * $gw / 100 / 1000) * (1 + ($loss / 100));
                    $prodKg = ($prodPcs * $composition * $gw / 100 / 1000);
                    $bsSettingKg = ($bsSettingPcs * $composition * $gw / 100 / 1000);
                    $bsMesinKg = $bsGram / 1000 * $composition / 100;

                    $materialIndex[$keyBase][] = [
                        'style_size'   => $style,
                        'composition'  => $composition,
                        'gw'           => $gw,
                        'gw_aktual'    => $item['gw_aktual'],
                        'loss'         => $loss,
                        'qty_order'    => $qty_order,
                        'prod_kg'        => $prodKg,
                        'kg_po'        => $kgPo,
                        'bs_mesin_kg'  => $bsMesinKg,
                        'bs_setting_kg' => $bsSettingKg
                    ];
                }

                // 🔹 Index data kirim & po tambahan
                $kirimIndex = [];
                foreach ($kirim as $krm) {
                    $key = $krm['no_model'] . '|' . $krm['item_type'] . '|' . $krm['kode_warna'];
                    $kirimIndex[$key] = $krm['total_kgs_out'];
                }

                $poPlusIndex = [];
                foreach ($poPlus as $plus) {
                    $key = $plus['no_model'] . '|' . $plus['item_type'] . '|' . $plus['kode_warna'];
                    $poPlusIndex[$key] = $plus['ttl_tambahan_kg'];
                }

                // 🔹 Gabungkan semua ke listRetur
                foreach ($listRetur as &$retur) {
                    $noModel   = $retur['no_model'] ?? '';
                    $itemType  = $retur['item_type'] ?? '';
                    $kodeWarna = $retur['kode_warna'] ?? '';

                    $keyMaterial = $noModel . '|' . $itemType . '|' . $kodeWarna;

                    $retur['detail'] = $materialIndex[$keyMaterial] ?? [];
                    $retur['total_qty_order'] = 0;
                    $retur['total_kg_po'] = 0;
                    $retur['total_bs_mc_kg'] = 0;
                    $retur['total_bs_st_kg'] = 0;
                    $retur['total_prod_kg'] = 0;

                    foreach ($retur['detail'] as $d) {
                        $retur['total_qty_order'] += $d['qty_order'] ?? 0;
                        $retur['total_kg_po']     += $d['kg_po'] ?? 0;
                        $retur['total_bs_mc_kg']  += $d['bs_mesin_kg'] ?? 0;
                        $retur['total_bs_st_kg']  += $d['bs_setting_kg'] ?? 0;
                        $retur['total_prod_kg']  += $d['prod_kg'] ?? 0;
                    }

                    $retur['total_kgs_out'] = $kirimIndex[$keyMaterial] ?? 0;
                    $retur['total_po_plus']  = $poPlusIndex[$keyMaterial] ?? 0;
                }
                unset($retur);

                log_message('debug', '=== HASIL OPTIMIZED listRetur === ' . print_r($listRetur, true));
            }
        } else {
            log_message('warning', "⚠️ Tidak ada data listRetur untuk area: $area dan tanggal: $tglRetur");
            $listRetur = [];
        }
        if ($listRetur === null) {
            log_message('error', "Gagal mendecode data dari API: $listReturUrl");
            // return $this->response->setJSON(["error" => "Gagal mengolah data dari API"]);
        } else {
            // Buat file Excel
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $styleTitle = [
                'font' => [
                    'bold' => true, // Tebalkan teks
                    'color' => ['argb' => 'FF000000'],
                    'size' => 15
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER, // Alignment rata tengah
                ],
            ];

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

            $sheet->setCellValue('A1', 'LIST RETUR ' . $area);
            $sheet->mergeCells('A1:O1');
            $sheet->getStyle('A1:O1')->applyFromArray($styleTitle);
            // Tulis header
            $sheet->setCellValue('A3', 'NO');
            $sheet->setCellValue('B3', 'TANGGAL RETUR');
            $sheet->setCellValue('C3', 'NO MODEL');
            $sheet->setCellValue('D3', 'ITEM TYPE');
            $sheet->setCellValue('E3', 'KODE WARNA');
            $sheet->setCellValue('F3', 'WARNA');
            $sheet->setCellValue('G3', 'PO (KG)');
            $sheet->setCellValue('H3', 'PO+ (KG)');
            $sheet->setCellValue('I3', 'BS (KG)');
            $sheet->setCellValue('J3', 'KIRIM (KG)');
            $sheet->setCellValue('K3', 'PAKAI (KG)');
            $sheet->setCellValue('L3', 'LOT RETUR');
            $sheet->setCellValue('M3', 'KG RETUR');
            $sheet->setCellValue('N3', 'KATEGORI');
            $sheet->setCellValue('O3', 'KETERANGAN GBN');
            $sheet->getStyle('A3')->applyFromArray($styleHeader);
            $sheet->getStyle('B3')->applyFromArray($styleHeader);
            $sheet->getStyle('C3')->applyFromArray($styleHeader);
            $sheet->getStyle('D3')->applyFromArray($styleHeader);
            $sheet->getStyle('E3')->applyFromArray($styleHeader);
            $sheet->getStyle('F3')->applyFromArray($styleHeader);
            $sheet->getStyle('G3')->applyFromArray($styleHeader);
            $sheet->getStyle('H3')->applyFromArray($styleHeader);
            $sheet->getStyle('I3')->applyFromArray($styleHeader);
            $sheet->getStyle('J3')->applyFromArray($styleHeader);
            $sheet->getStyle('K3')->applyFromArray($styleHeader);
            $sheet->getStyle('L3')->applyFromArray($styleHeader);
            $sheet->getStyle('M3')->applyFromArray($styleHeader);
            $sheet->getStyle('N3')->applyFromArray($styleHeader);
            $sheet->getStyle('O3')->applyFromArray($styleHeader);

            // Tulis data mulai dari baris 2
            $row = 4;
            $no = 1;

            foreach ($listRetur as $item) {
                $sheet->setCellValue('A' . $row, $no++);
                $sheet->setCellValue('B' . $row, $item['tgl_retur']);
                $sheet->setCellValue('C' . $row, $item['no_model']);
                $sheet->setCellValue('D' . $row, $item['item_type']);
                $sheet->setCellValue('E' . $row, $item['kode_warna']);
                $sheet->setCellValue('F' . $row, $item['warna']);
                $sheet->setCellValue('G' . $row, round($item['total_kg_po'] ?? 0, 2));
                $sheet->setCellValue('H' . $row, round($item['ttl_tambahan_kg'] ?? 0, 2));
                $sheet->setCellValue('I' . $row, round(($item['total_bs_mc_kg'] ?? 0) + ($item['total_bs_st_kg'] ?? 0), 2));
                $sheet->setCellValue('J' . $row, round($item['total_kgs_out'] ?? 0, 2));
                $sheet->setCellValue('K' . $row, round(($item['total_prod_kg'] ?? 0) + ($item['total_bs_mc_kg'] ?? 0), 2));
                $sheet->setCellValue('L' . $row, $item['lot_retur']);
                $sheet->setCellValue('M' . $row, $item['kgs_retur']);
                $sheet->setCellValue('N' . $row, $item['kategori']);
                $sheet->setCellValue('O' . $row, $item['keterangan_gbn']);
                $sheet->getStyle('A' . $row)->applyFromArray($styleBody);
                $sheet->getStyle('B' . $row)->applyFromArray($styleBody);
                $sheet->getStyle('C' . $row)->applyFromArray($styleBody);
                $sheet->getStyle('D' . $row)->applyFromArray($styleBody);
                $sheet->getStyle('E' . $row)->applyFromArray($styleBody);
                $sheet->getStyle('F' . $row)->applyFromArray($styleBody);
                $sheet->getStyle('G' . $row)->applyFromArray($styleBody);
                $sheet->getStyle('H' . $row)->applyFromArray($styleBody);
                $sheet->getStyle('I' . $row)->applyFromArray($styleBody);
                $sheet->getStyle('J' . $row)->applyFromArray($styleBody);
                $sheet->getStyle('K' . $row)->applyFromArray($styleBody);
                $sheet->getStyle('L' . $row)->applyFromArray($styleBody);
                $sheet->getStyle('M' . $row)->applyFromArray($styleBody);
                $sheet->getStyle('N' . $row)->applyFromArray($styleBody);
                $sheet->getStyle('O' . $row)->applyFromArray($styleBody);
                $row++;
            }

            // Set lebar kolom agar menyesuaikan isi
            foreach (range('A', 'O') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Buat writer dan output file Excel
            $writer = new Xlsx($spreadsheet);
            $fileName = 'Export Retur ' . $area . '.xlsx';

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $fileName . '"');
            header('Cache-Control: max-age=0');

            $writer->save('php://output');
            exit;
        }
    }
    public function exportDataOrder()
    {
        $buyer = $this->request->getPost('buyer');
        $area = $this->request->getPost('area');
        $jarum = $this->request->getPost('jarum');
        $pdk = $this->request->getPost('pdk');
        $seam = $this->request->getPost('seam');
        $processRoutes = $this->request->getPost('process_routes');
        $tglTurun = $this->request->getPost('tgl_turun_order');
        $tglTurunAkhir = $this->request->getPost('tgl_turun_order_akhir') ?? '';
        $awal = $this->request->getPost('awal');
        $akhir = $this->request->getPost('akhir');
        $yesterday = date('Y-m-d', strtotime('-2 day')); // DUA HARI KE BELAKANG

        $validate = [
            'buyer' => $buyer,
            'area' => $area,
            'jarum' => $jarum,
            'pdk' => $pdk,
            'seam' => $seam,
            'process_routes' => $processRoutes,
            'tglTurun' => $tglTurun,
            'tglTurunAkhir' => $tglTurunAkhir,
            'awal' => $awal,
            'akhir' => $akhir,
        ];

        $data = $this->ApsPerstyleModel->getDataOrder($validate); // ambil data semua order sesuai yg di filter

        // ✅ 2. Kelompokkan berdasarkan model + size dan kumpulkan idaps
        $groupData = [];
        foreach ($data as $row) {
            $groupKey = $row['mastermodel'] . '-' . $row['size'];
            $groupData[$groupKey]['idaps'][] = $row['idapsperstyle'];
            $groupData[$groupKey]['info'] = [
                'model' => $row['mastermodel'],
                'size'  => $row['size'],
            ];
        }

        // ✅ 3. Ambil total produksi untuk semua grup (1x query)
        $produksiRows = $this->produksiModel->getTotalProduksiGroup($area);

        // mapping hasil produksi ke array model-size
        $mapProduksi = [];
        foreach ($produksiRows as $p) {
            $mapProduksi[$p['model'] . '-' . $p['size']] = $p['total_qty'];
        }

        // dd($mapProduksi);

        // ✅ 4. Ambil total BS untuk semua idaps sekaligus (1x query)
        $bsRows = $this->bsModel->getTotalBsGroup($area);

        // hitung total bs per grup model-size
        $mapBs = [];
        foreach ($bsRows as $b) {
            $mapBs[$b['model'] . '-' . $b['size']] = $b['total_bs'];
        }

        // ✅ 5. Ambil total tambahan packing
        $bsRows = $this->ApsPerstyleModel->getPoPlusPacking($area);

        // hitung total po plus per grup model-size
        $mapPoPlus = [];
        foreach ($bsRows as $p) {
            $mapPoPlus[$p['model'] . '-' . $p['size']] = $p['total_po_plus'];
        }
        // dd($mapPoPlus);


        // ✅ 6. Gabungkan ke data utama, tampilkan hanya 1x per grup
        $seen = [];
        foreach ($data as &$id) {
            $groupKey = $id['mastermodel'] . '-' . $id['size'];
            if (!isset($seen[$groupKey])) {
                $bruto = $mapProduksi[$groupKey] ?? 0;
                $bs    = $mapBs[$groupKey] ?? 0;

                $id['bruto_prod'] = $mapProduksi[$groupKey] ?? 0;
                $id['bs_setting'] = $mapBs[$groupKey] ?? 0;
                $id['netto_prod'] = $bruto - $bs;
                $id['po_plus']    = $mapPoPlus[$groupKey] ?? 0;
                $seen[$groupKey]  = true;
            } else {
                $id['bruto_prod'] = '';
                $id['bs_setting'] = '';
                $id['netto_prod'] = '';
                $id['po_plus']    = '';
            }
        }
        unset($id);

        foreach ($data as &$id) {
            $key = [
                'model' => $id['mastermodel'],
                'size' => $id['size'],
                'delivery' => $id['delivery'],
                'machinetypeid' => $id['machinetypeid'],
                'area' => $id['factory'],
                'yesterday' => $yesterday
            ];
            // get data jl mc
            $mc = $this->produksiModel->getJlMcByModel($key);

            $id['jl_mc'] = $mc['jl_mc'] ?? '';
            $id['qty_produksi'] = $mc['qty_produksi'] ?? '';
        }
        // dd($data);
        // Buat file Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $styleTitle = [
            'font' => [
                'bold' => true, // Tebalkan teks
                'color' => ['argb' => 'FF000000'],
                'size' => 15
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER, // Alignment rata tengah
            ],
        ];

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

        $sheet->setCellValue('A1', 'DATA ORDER ' . $area);
        $sheet->mergeCells('A1:M1');
        $sheet->getStyle('A1:M1')->applyFromArray($styleTitle);
        // Tulis header
        $sheet->setCellValue('A3', 'TGL TURUN PDK');
        $sheet->setCellValue('B3', 'NO');
        $sheet->setCellValue('C3', 'KET REPEAT');
        $sheet->setCellValue('D3', 'NO MODEL');
        $sheet->setCellValue('E3', 'PRODUCT');
        $sheet->setCellValue('F3', 'TYPE');
        $sheet->setCellValue('G3', 'NO ORDER');
        $sheet->setCellValue('H3', 'BUYER');
        $sheet->setCellValue('I3', 'SEAM');
        $sheet->setCellValue('J3', 'PROCESS ROUTES');
        $sheet->setCellValue('K3', 'SMV');
        $sheet->setCellValue('L3', 'PRODUCTION UNIT');
        $sheet->setCellValue('M3', 'AREA');
        $sheet->setCellValue('N3', 'JARUM');
        $sheet->setCellValue('O3', 'INISIAL');
        $sheet->setCellValue('P3', 'STYLE SIZE');
        $sheet->setCellValue('Q3', 'DELIVERY');
        $sheet->setCellValue('R3', 'QTY');
        $sheet->setCellValue('S3', 'SISA');
        $sheet->setCellValue('T3', 'PRODUKSI (Bruto)');
        $sheet->setCellValue('U3', 'PRODUKSI (Netto)');
        $sheet->setCellValue('V3', 'BS SETTING');
        $sheet->setCellValue('W3', 'PO PLUS PACKING');
        $sheet->setCellValue('X3', 'COLOR');
        $sheet->setCellValue('Y3', 'DESCRIPTION');
        $sheet->setCellValue('Z3', 'Actual JL MC');
        $sheet->getStyle('A3')->applyFromArray($styleHeader);
        $sheet->getStyle('B3')->applyFromArray($styleHeader);
        $sheet->getStyle('C3')->applyFromArray($styleHeader);
        $sheet->getStyle('D3')->applyFromArray($styleHeader);
        $sheet->getStyle('E3')->applyFromArray($styleHeader);
        $sheet->getStyle('F3')->applyFromArray($styleHeader);
        $sheet->getStyle('G3')->applyFromArray($styleHeader);
        $sheet->getStyle('H3')->applyFromArray($styleHeader);
        $sheet->getStyle('I3')->applyFromArray($styleHeader);
        $sheet->getStyle('J3')->applyFromArray($styleHeader);
        $sheet->getStyle('K3')->applyFromArray($styleHeader);
        $sheet->getStyle('L3')->applyFromArray($styleHeader);
        $sheet->getStyle('M3')->applyFromArray($styleHeader);
        $sheet->getStyle('N3')->applyFromArray($styleHeader);
        $sheet->getStyle('O3')->applyFromArray($styleHeader);
        $sheet->getStyle('P3')->applyFromArray($styleHeader);
        $sheet->getStyle('Q3')->applyFromArray($styleHeader);
        $sheet->getStyle('R3')->applyFromArray($styleHeader);
        $sheet->getStyle('S3')->applyFromArray($styleHeader);
        $sheet->getStyle('T3')->applyFromArray($styleHeader);
        $sheet->getStyle('U3')->applyFromArray($styleHeader);
        $sheet->getStyle('V3')->applyFromArray($styleHeader);
        $sheet->getStyle('W3')->applyFromArray($styleHeader);
        $sheet->getStyle('X3')->applyFromArray($styleHeader);
        $sheet->getStyle('Y3')->applyFromArray($styleHeader);
        $sheet->getStyle('Z3')->applyFromArray($styleHeader);

        // Tulis data mulai dari baris 2
        $row = 4;
        $no = 1;

        foreach ($data as $item) {

            $kode = $item['product_type'] ?? '';

            $pecah = explode('-', $kode);

            $product = $pecah[0] ?? '';
            $type    = $pecah[1] ?? '';

            $sheet->setCellValue('A' . $row, $item['created_at']);
            $sheet->setCellValue('B' . $row, $no++);
            $sheet->setCellValue('C' . $row, $item['repeat_from']);
            $sheet->setCellValue('D' . $row, $item['mastermodel']);
            $sheet->setCellValue('E' . $row, $product);
            $sheet->setCellValue('F' . $row, $type);
            $sheet->setCellValue('G' . $row, $item['no_order']);
            $sheet->setCellValue('H' . $row, $item['kd_buyer_order']);
            $sheet->setCellValue('I' . $row, $item['seam']);
            $sheet->setCellValue('J' . $row, $item['process_routes']);
            $sheet->setCellValue('K' . $row, $item['smv']);
            $sheet->setCellValue('L' . $row, $item['production_unit']);
            $sheet->setCellValue('M' . $row, $item['factory']);
            $sheet->setCellValue('N' . $row, $item['machinetypeid']);
            $sheet->setCellValue('O' . $row, $item['inisial']);
            $sheet->setCellValue('P' . $row, $item['size']);
            $sheet->setCellValue('Q' . $row, $item['delivery']);
            $sheet->setCellValue('R' . $row, $item['qty_pcs']);
            $sheet->setCellValue('S' . $row, $item['sisa_pcs']);
            $sheet->setCellValue('T' . $row, $item['bruto_prod']);
            $sheet->setCellValue('U' . $row, $item['netto_prod']);
            $sheet->setCellValue('V' . $row, $item['bs_setting']);
            $sheet->setCellValue('W' . $row, $item['po_plus']);
            $sheet->setCellValue('X' . $row, $item['color']);
            $sheet->setCellValue('Y' . $row, $item['description']);
            $sheet->setCellValue('Z' . $row, $item['jl_mc']);
            // 
            $sheet->getStyle('A' . $row)->applyFromArray($styleBody);
            $sheet->getStyle('B' . $row)->applyFromArray($styleBody);
            $sheet->getStyle('C' . $row)->applyFromArray($styleBody);
            $sheet->getStyle('D' . $row)->applyFromArray($styleBody);
            $sheet->getStyle('E' . $row)->applyFromArray($styleBody);
            $sheet->getStyle('F' . $row)->applyFromArray($styleBody);
            $sheet->getStyle('G' . $row)->applyFromArray($styleBody);
            $sheet->getStyle('H' . $row)->applyFromArray($styleBody);
            $sheet->getStyle('I' . $row)->applyFromArray($styleBody);
            $sheet->getStyle('J' . $row)->applyFromArray($styleBody);
            $sheet->getStyle('K' . $row)->applyFromArray($styleBody);
            $sheet->getStyle('L' . $row)->applyFromArray($styleBody);
            $sheet->getStyle('M' . $row)->applyFromArray($styleBody);
            $sheet->getStyle('N' . $row)->applyFromArray($styleBody);
            $sheet->getStyle('O' . $row)->applyFromArray($styleBody);
            $sheet->getStyle('P' . $row)->applyFromArray($styleBody);
            $sheet->getStyle('Q' . $row)->applyFromArray($styleBody);
            $sheet->getStyle('R' . $row)->applyFromArray($styleBody);
            $sheet->getStyle('S' . $row)->applyFromArray($styleBody);
            $sheet->getStyle('T' . $row)->applyFromArray($styleBody);
            $sheet->getStyle('U' . $row)->applyFromArray($styleBody);
            $sheet->getStyle('V' . $row)->applyFromArray($styleBody);
            $sheet->getStyle('W' . $row)->applyFromArray($styleBody);
            $sheet->getStyle('X' . $row)->applyFromArray($styleBody);
            $sheet->getStyle('Y' . $row)->applyFromArray($styleBody);
            $sheet->getStyle('Z' . $row)->applyFromArray($styleBody);
            $row++;
        }

        // Set lebar kolom agar menyesuaikan isi
        foreach (range('A', 'Y') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Buat writer dan output file Excel
        $writer = new Xlsx($spreadsheet);
        $fileName = 'Data Order.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
    public function generatePoTambahan()
    {
        $area = $this->request->getGet('area');
        $noModel = $this->request->getGet('model');
        $tglBuat = $this->request->getGet('tgl_buat');

        // Ambil data berdasarkan area dan model
        $apiUrl = api_url('material') . "filterPoTambahan"
            . "?area=" . urlencode($area)
            . "&model=" . urlencode($noModel)
            . "&tglBuat=" . urlencode($tglBuat);

        $ch = curl_init($apiUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
        ]);

        $response = curl_exec($ch);
        $error    = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($response === false) {
            return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Curl error: ' . $error]);
        }

        $result = json_decode($response, true);
        if (!is_array($result)) {
            return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Data tidak valid dari API']);
        }

        $dataPoTambahan = $result['dataPoTambahan'];
        $dataRetur = $result['dataRetur'];

        $returIndex = [];
        foreach ($dataRetur as $retur) {
            $key = $retur['no_model'] . '|' . $retur['item_type'] . '|' . $retur['kode_warna'] . '|' . $retur['color'];

            // Kalau kombinasi belum ada, buat baru
            if (!isset($returIndex[$key])) {
                $returIndex[$key] = [
                    'kgs_retur' => (float)$retur['kgs_retur'],
                    'cns_retur' => (float)$retur['cns_retur'],
                    'krg_retur' => (float)$retur['krg_retur'],
                    'lot_retur' => $retur['lot_retur']
                ];
            } else {
                // Jika ada lebih dari 1 retur untuk kombinasi yang sama → tambahkan nilainya
                $returIndex[$key]['kgs_retur'] += (float)$retur['kgs_retur'];
                $returIndex[$key]['cns_retur'] += (float)$retur['cns_retur'];
                $returIndex[$key]['krg_retur'] += (float)$retur['krg_retur'];

                // Gabungkan lot_retur (hindari duplikat sederhana)
                if (strpos($returIndex[$key]['lot_retur'], $retur['lot_retur']) === false) {
                    $returIndex[$key]['lot_retur'] .= ', ' . $retur['lot_retur'];
                }
            }
        }

        $qtyOrderList = [];

        foreach ($dataPoTambahan as $item) {
            $style = $item['style_size'];
            $noModel = $item['no_model'];  // ambil langsung dari API
            $area = $item['admin'];        // atau sesuai kolom factory di DB

            // Ambil qty dari DB lokal
            $qty = $this->ApsPerstyleModel->getSisaPerSize($area, $noModel, [$style]);
            $qtyOrderList[$style] = is_array($qty) ? ($qty['qty'] ?? 0) : ($qty->qty ?? 0);
        }

        // Gabungkan ke response
        foreach ($dataPoTambahan as $i => $row) {
            $style = $row['style_size'];
            $qty_order = isset($qtyOrderList[$style]) ? (float)$qtyOrderList[$style] : 0;
            $composition = (float)$row['composition'] ?? 0;
            $gw = (float)$row['gw'] ?? 0;
            $loss = (float)$row['loss'] ?? 0;

            $dataPoTambahan[$i]['qty_order'] = $qty_order;
            $dataPoTambahan[$i]['kg_po'] = ($qty_order * $composition * $gw / 100 / 1000) * (1 + ($loss / 100));

            // --- Cari dan masukkan data retur berdasarkan 4 key ---
            $key = $row['no_model'] . '|' . $row['item_type'] . '|' . $row['kode_warna'] . '|' . $row['color'];
            if (isset($returIndex[$key])) {
                $dataPoTambahan[$i]['kgs_retur'] = $returIndex[$key]['kgs_retur'];
                $dataPoTambahan[$i]['cns_retur'] = $returIndex[$key]['cns_retur'];
                $dataPoTambahan[$i]['krg_retur'] = $returIndex[$key]['krg_retur'];
                $dataPoTambahan[$i]['lot_retur'] = $returIndex[$key]['lot_retur'];
            } else {
                // Default 0 / kosong kalau tidak ada retur
                $dataPoTambahan[$i]['kgs_retur'] = 0;
                $dataPoTambahan[$i]['cns_retur'] = 0;
                $dataPoTambahan[$i]['krg_retur'] = 0;
                $dataPoTambahan[$i]['lot_retur'] = '';
            }
        }

        $delivery = $dataPoTambahan[0]['delivery_akhir'] ?? '';
        // dd($dataPoTambahan, $dataRetur);
        // Buat Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $firstSheet = true;

        if ($firstSheet) {
            $sheet = $spreadsheet->getActiveSheet();
            $firstSheet = false;
        } else {
            $sheet = $spreadsheet->createSheet();
        }
        $sheet->setTitle('Form Retur');
        // Style untuk header tabel (border, center, bold)
        $styleHeader = [
            'font' => [
                'bold' => false,
                'size' => 10, // <--- Ukuran font diatur di sini
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ];

        $styleBody = [
            'font' => [
                'size' => 10, // Ukuran font isi tabel
            ],
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];

        $spreadsheet->getDefaultStyle()->getFont()->setName('Arial');
        // $spreadsheet->getDefaultStyle()->getFont()->setSize(16);

        // 1. Atur ukuran kertas jadi A4
        $sheet->getPageSetup()
            ->setPaperSize(PageSetup::PAPERSIZE_A4);

        // 2. Atur orientasi jadi landscape
        $sheet->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        // 3. (Opsional) Atur scaling, agar muat ke 1 halaman
        $sheet->getPageSetup()
            ->setFitToWidth(1)
            ->setFitToHeight(0)    // 0 artinya auto height
            ->setFitToPage(true); // aktifkan fitting

        // 4. (Opsional) Atur margin supaya tidak terlalu sempit
        $sheet->getPageMargins()->setTop(0.4)
            ->setBottom(0.4)
            ->setLeft(0.4)
            ->setRight(0.2);

        //Border Thin
        $sheet->getStyle('D1:D3')->applyFromArray([
            'borders' => [
                'right' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
        $sheet->getStyle('D4')->applyFromArray([
            'borders' => [
                'right' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
        $sheet->getStyle('S4:Z4')->applyFromArray([
            'borders' => [
                'left' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
                'right' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
        $sheet->getStyle('S5:S5')->applyFromArray([
            'borders' => [
                'left' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
                'right' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Double border baris 4 dan 5
        $sheet->getStyle('A4:AD4')->applyFromArray([
            'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
                'bottom' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
        $sheet->getStyle('A5:AD5')->applyFromArray([
            'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
                'bottom' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Border kiri
        $sheet->getStyle('AA5:AD5')->applyFromArray([
            'borders' => [
                'left' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => '000000'],
                ],
            ],
        ]);

        $thinInside = [
            'borders' => [
                // border antar kolom (vertical lines) di dalam range
                'vertical' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
                // border antar baris (horizontal lines) di dalam range
                'horizontal' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];

        $thinInside = [
            'borders' => [
                'vertical' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
                'horizontal' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];
        $sheet->getStyle('A11:AD28')->applyFromArray($thinInside);

        // 2) Border tipis atas untuk baris header tabel (A11:Q11)
        $sheet->getStyle('A11:AD11')->applyFromArray([
            'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // 3) Border tipis bawah untuk baris total (A28:Q28)
        $sheet->getStyle('A28:AD28')->applyFromArray([
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Lebar kolom (dalam pt) dan tinggi baris (dalam pt)
        $columnWidths = [
            'A' => 20,
            'B' => 20,
            'C' => 50,
            'D' => 80,
            'E' => 70,
            'F' => 70,
            'G' => 45,
            'H' => 20,
            'I' => 40,
            'J' => 20,
            'K' => 40,
            'L' => 30,
            'M' => 30,
            'N' => 33,
            'O' => 40,
            'P' => 30,
            'Q' => 30,
            'R' => 30,
            'S' => 33,
            'T' => 30,
            'U' => 30,
            'V' => 30,
            'W' => 30,
            'X' => 30,
            'Y' => 33,
            'Z' => 30,
            'AA' => 33,
            'AB' => 30,
            'AC' => 33,
            'AD' => 90,
        ];

        $rowHeightsPt = array_fill_keys(range(11, 33), 36);
        $rowHeightsPt[11] = 50;
        $rowHeightsPt[12] = 50;

        // Atur tinggi baris
        foreach ($rowHeightsPt as $row => $height) {
            $sheet->getRowDimension($row)->setRowHeight($height);
        }

        // Atur lebar kolom
        foreach ($columnWidths as $col => $pt) {
            $sheet->getColumnDimension($col)->setWidth(round($pt / 5.25, 2));
        }

        // Header Form
        $sheet->mergeCells('A1:D2');
        $sheet->getRowDimension(1)->setRowHeight(30);

        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo Perusahaan');
        $drawing->setPath('assets/img/logo-kahatex.png');
        $drawing->setCoordinates('C1');
        $drawing->setHeight(50);
        $drawing->setOffsetX(37);
        $drawing->setOffsetY(10);
        $drawing->setWorksheet($sheet);
        $sheet->mergeCells('A3:D3');
        $sheet->setCellValue('A3', 'PT. KAHATEX');
        $sheet->getStyle('A3')->getFont()->setSize(11)->setBold(true);
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('E1', 'FORMULIR');
        $sheet->getStyle('E1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('E1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $sheet->getStyle('E1')->getFill()->getStartColor()->setRGB('99FFFF');
        $sheet->mergeCells('E1:AD1');
        $sheet->getStyle('E1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('E2:AD2');
        $sheet->setCellValue('E2', 'DEPARTEMEN KAOS KAKI');
        $sheet->getStyle('E2')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('E2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('E3:AD3');
        $sheet->setCellValue('E3', 'PO TAMBAHAN DAN RETURAN BAHAN BAKU MESIN KE GUDANG BENANG');
        $sheet->getStyle('E3')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('E3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A4:C4');
        $sheet->setCellValue('A4', 'No. Dokumen');
        $sheet->setCellValue('D4', 'FOR-KK-034/REV_05/HAL_1/1');

        $sheet->mergeCells('S4:Z4');
        $sheet->setCellValue('S4', 'Tanggal Revisi');
        $sheet->mergeCells('AA4:AD4');
        $sheet->setCellValue('AA4', '17 Maret 2025');
        $sheet->getStyle('AA4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('S5:Z5');
        $sheet->setCellValue('S5', 'Klasifikasi');
        $sheet->mergeCells('AA5:AD5');
        $sheet->setCellValue('AA5', 'Internal');
        $sheet->getStyle('AA5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A5:R5');
        $sheet->getStyle('A4:AD5')->getFont()->setBold(true)->setSize(11);

        $sheet->setCellValue('A6', 'Area: ' . $area);
        $sheet->getStyle('A6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)
            ->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A6')->getFont()->setBold(true)->setSize(10);

        $sheet->setCellValue('G6', 'Loss F.Up');
        $sheet->getStyle('G6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)
            ->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('G6')->getFont()->setBold(true)->setSize(10);

        $lossValue = isset($dataPoTambahan[0]['loss']) ? $dataPoTambahan[0]['loss'] . '%' : '';
        $sheet->setCellValue('I6', ': ' . $lossValue);
        $sheet->getStyle('I6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)
            ->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('I6')->getFont()->setBold(true)->setSize(10);

        $sheet->setCellValue('P6', 'Tanggal Buat');
        $sheet->getStyle('P6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)
            ->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('P6')->getFont()->setBold(true)->setSize(10);

        $sheet->setCellValue('S6', ': ' . $tglBuat);
        $sheet->getStyle('S6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)
            ->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('S6')->getFont()->setBold(true)->setSize(10);

        $sheet->setCellValue('P7', 'Tanggal Export');
        $sheet->getStyle('P7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)
            ->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('P7')->getFont()->setBold(true)->setSize(10);

        $sheet->setCellValue('S7', ': ' . $delivery);
        $sheet->getStyle('S7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)
            ->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('S7')->getFont()->setBold(true)->setSize(10);

        // Header utama dan sub-header
        $sheet->setCellValue('A8', 'Model');
        $sheet->mergeCells('A8:B10');
        $sheet->getStyle('A8:B10')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('C8', 'Warna');
        $sheet->mergeCells('C8:C10');
        $sheet->getStyle('C8:C10')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('D8', 'Item Type');
        $sheet->mergeCells('D8:D10');
        $sheet->getStyle('D8:D10')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('E8', 'Kode Warna');
        $sheet->mergeCells('E8:E10');
        $sheet->getStyle('E8:E10')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('F8', 'Style / Size');
        $sheet->mergeCells('F8:F10');
        $sheet->getStyle('F8:F10')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('G8', 'Komposisi ( % )');
        $sheet->mergeCells('G8:G10');
        $sheet->getStyle('G8:G10')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('H8', 'GW / Pcs');
        $sheet->mergeCells('H8:H10');
        $sheet->getStyle('H8:H10')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('I8', 'Qty / Pcs');
        $sheet->mergeCells('I8:I10');
        $sheet->getStyle('I8:I10')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('J8', 'Loss');
        $sheet->mergeCells('J8:J10');
        $sheet->getStyle('J8:J10')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('K8', 'Pesanan Kgs');
        $sheet->mergeCells('K8:K10');
        $sheet->getStyle('K8:K10')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('L8', 'Terima');
        $sheet->mergeCells('L8:N9');
        $sheet->getStyle('L8:N9')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('L10', 'Kg');
        $sheet->getStyle('L10')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('M10', '+ / -');
        $sheet->getStyle('M10')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('N10', '%');
        $sheet->getStyle('N10')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('O8', 'Sisa Benang di mesin');
        $sheet->mergeCells('O8:O9');
        $sheet->getStyle('O8:O9')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('O10', 'Kg');
        $sheet->getStyle('O10')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('P8', 'Tambahan I (mesin)');
        $sheet->mergeCells('P8:S8');
        $sheet->getStyle('P8:S8')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('P9', 'Pcs');
        $sheet->mergeCells('P9:P10');
        $sheet->getStyle('P9:P10')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('Q9', 'Benang');
        $sheet->mergeCells('Q9:R9');
        $sheet->getStyle('Q9:R9')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('Q10', 'Kg');
        $sheet->getStyle('Q10')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('R10', 'Cones');
        $sheet->getStyle('R10')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('S9', '%');
        $sheet->mergeCells('S9:S10');
        $sheet->getStyle('S9:S10')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('T8', 'Tambahan II (Packing)');
        $sheet->mergeCells('T8:W8');
        $sheet->getStyle('T8:W8')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('T9', 'Pcs');
        $sheet->mergeCells('T9:T10');
        $sheet->getStyle('T9:T10')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('U9', 'Benang');
        $sheet->mergeCells('U9:V9');
        $sheet->getStyle('U9:V9')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('U10', 'Kg');
        $sheet->getStyle('U10')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('V10', 'Cones');
        $sheet->getStyle('V10')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('W9', '%');
        $sheet->mergeCells('W9:W10');
        $sheet->getStyle('W9:W10')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('X8', 'Total lebih pakai benang');
        $sheet->mergeCells('X8:Y9');
        $sheet->getStyle('X8:Y9')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('X10', 'Kg');
        $sheet->getStyle('X10')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('Y10', '%');
        $sheet->getStyle('Y10')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('Z8', 'RETURAN');
        $sheet->mergeCells('Z8:AC8');
        $sheet->getStyle('Z8:AC8')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('Z9', 'Kg');
        $sheet->mergeCells('Z9:Z10');
        $sheet->getStyle('Z9:Z10')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('AA9', '% dari PSN');
        $sheet->mergeCells('AA9:AA10');
        $sheet->getStyle('AA9:AA10')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('AB9', 'Kg');
        $sheet->mergeCells('AB9:AB10');
        $sheet->getStyle('AB9:AB10')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('AC9', '% dari PO(+)');
        $sheet->mergeCells('AC9:AC10');
        $sheet->getStyle('AC9:AC10')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('AD8', 'Keterangan');
        $sheet->mergeCells('AD8:AD10');
        $sheet->getStyle('AD8:AD10')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        // Terapkan style ke seluruh area header (baris 8–10)
        $sheet->getStyle('A8:AD10')->applyFromArray($styleHeader);
        // Border kiri double untuk kolom A pada header
        $sheet->getStyle('A8:A10')->applyFromArray([
            'borders' => [
                'left' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Border kanan double untuk kolom AD pada header
        $sheet->getStyle('AD8:AD10')->applyFromArray([
            'borders' => [
                'right' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Isi tabel
        $rowNum = 11;
        $sheet->getRowDimension($rowNum)->setRowHeight(18);

        $prevModel = null;
        $prevKode = null;
        $prevItemType = null;
        $totalPcsPo = 0;
        $totalKgPo = 0;
        $totalTerimaKg = 0;
        $totalSisaBBMc = 0;
        $totalTambahanMcPcs = 0;
        $totalTambahanMcKg = 0;
        $totalTambahanMcCns = 0;
        $totalTambahanPckPcs = 0;
        $totalTambahanPckKg = 0;
        $totalTambahanPckCns = 0;
        $totalTambahanKg = 0;
        $lastKgsRetur = 0;

        $groupStartRow = $rowNum;

        foreach ($dataPoTambahan as $row) {
            $sheet->mergeCells("A{$rowNum}:B{$rowNum}");

            $currentModel = $row['no_model'];
            $currentItem  = $row['item_type'];
            $currentKode  = $row['kode_warna'];

            $tambahanMcKg       = (float)$row['poplus_mc_kg']  ?? 0;
            $tambahanPckKg      = (float)$row['plus_pck_kg']  ?? 0;
            $pcs_po             = (float)($row['qty_order'] ?? 0);
            $kg_po              = (float)($row['kg_po'] ?? 0);
            $terimaKg           = (float)($row['ttl_terima_kg'] ?? 0);
            $sisaBBMc           = (float)($row['ttl_sisa_bb_dimc'] ?? 0);
            $tambahanMcPcs      = (float)($row['sisa_order_pcs'] ?? 0);
            $tambahanMcKg       = (float)($row['poplus_mc_kg'] ?? 0);
            $tambahanMcCns      = (float)($row['poplus_mc_cns'] ?? 0);
            $totalTambahanRow   = (float)($row['ttl_tambahan_kg'] ?? 0);
            $tambahanPckPcs     = (float)($row['plus_pck_pcs'] ?? 0);
            $tambahanPckKg      = (float)($row['plus_pck_kg'] ?? 0);
            $tambahanPckCns     = (float)($row['plus_pck_cns'] ?? 0);
            $kgsRetur           = (float)($row['kgs_retur'] ?? 0);

            // persentase per baris
            $persenPoplus  = ($kg_po > 0) ? round(($tambahanMcKg / $kg_po) * 100, 2) . '%' : '0%';
            $persenPlusPck = ($kg_po > 0) ? round(($tambahanPckKg / $kg_po) * 100, 2) . '%' : '0%';

            // 🚨 Cek apakah sudah ganti no_model, item_type, atau kode_warna
            if ($prevModel !== null && ($currentModel !== $prevModel || $currentKode !== $prevKode || $currentItem !== $prevItemType)) {
                $sheet->mergeCells("AD{$groupStartRow}:AD" . ($rowNum - 1));

                // Tulis subtotal ke bawah baris sebelumnya
                // $sheet->mergeCells("I{$rowNum}:J{$rowNum}");
                // $sheet->setCellValue("I{$rowNum}", "TOTAL");
                $sheet->setCellValue("I{$rowNum}", number_format($totalPcsPo, 2));
                $sheet->setCellValue("K{$rowNum}", number_format($totalKgPo, 2));
                $sheet->setCellValue("L{$rowNum}", number_format($totalTerimaKg, 2));
                $sheet->setCellValue("M{$rowNum}", number_format($totalTerimaKg - $totalKgPo, 2));
                $sheet->setCellValue("N{$rowNum}", ($totalKgPo > 0) ? round(($totalTerimaKg / $totalKgPo) * 100, 0) . '%' : '');
                $sheet->setCellValue("O{$rowNum}", number_format($totalSisaBBMc, 2));
                $sheet->setCellValue("P{$rowNum}", $totalTambahanMcPcs);
                $sheet->setCellValue("Q{$rowNum}", number_format($totalTambahanMcKg, 2));
                $sheet->setCellValue("R{$rowNum}", $totalTambahanMcCns);
                $sheet->setCellValue("S{$rowNum}", ($totalKgPo > 0) ? round(($totalTambahanMcKg / $totalKgPo) * 100, 2) . '%' : '');
                $sheet->setCellValue("T{$rowNum}", $totalTambahanPckPcs);
                $sheet->setCellValue("U{$rowNum}", number_format($totalTambahanPckKg, 2));
                $sheet->setCellValue("V{$rowNum}", $totalTambahanPckCns);
                $sheet->setCellValue("W{$rowNum}", ($totalKgPo > 0) ? round(($totalTambahanPckKg / $totalKgPo) * 100, 2) . '%' : '');
                $sheet->setCellValue("X{$rowNum}", $totalTambahanKg);
                $sheet->setCellValue("Y{$rowNum}", ($totalKgPo > 0) ? round(($totalTambahanKg / $totalKgPo) * 100, 2) . '%' : '');
                $sheet->setCellValue("Z{$rowNum}", number_format($lastKgsRetur, 2));
                $sheet->setCellValue("AA{$rowNum}", ($totalKgPo > 0) ? round(($lastKgsRetur / $totalKgPo) * 100, 2) . '%' : '');

                // Bold & style subtotal
                $sheet->getStyle("I{$rowNum}:AD{$rowNum}")->getFont()->setBold(true);
                $sheet->getStyle("I{$rowNum}:AD{$rowNum}")
                    ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $sheet->getRowDimension($rowNum)->setRowHeight(20);
                // 🎯 Tambahin cek nilai kolom L–S di baris TOTAL
                $columns = range('L', 'Z'); // L–Z
                $columns = array_merge($columns, ['AA', 'AB', 'AC']); // tambah kolom > Z

                foreach ($columns as $col) {
                    $cell = $col . $rowNum;
                    $rawValue = $sheet->getCell($cell)->getValue();
                    $numericValue = (float)str_replace('%', '', $rawValue);

                    if ($numericValue <= 0) {
                        $sheet->getStyle($cell)
                            ->getFont()
                            ->getColor()
                            ->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
                    }
                }
                $rowNum++; // pindah baris setelah subtotal
                $groupStartRow = $rowNum;

                // reset subtotal
                $totalPcsPo = 0;
                $totalKgPo = 0;
                $totalTerimaKg = 0;
                $totalSisaBBMc = 0;
                $totalTambahanMcPcs = 0;
                $totalTambahanMcKg = 0;
                $totalTambahanMcCns = 0;
                $totalTambahanPckPcs = 0;
                $totalTambahanPckKg = 0;
                $totalTambahanPckCns = 0;
                $totalTambahanKg = 0;
            }
            $sheet->mergeCells("A{$rowNum}:B{$rowNum}");
            $sheet->fromArray([
                $row['no_model'] ?? '',
                '',
                $row['color'],
                $row['item_type'],
                $row['kode_warna'],
                $row['style_size'],
                $row['composition'],
                $row['gw'],
                $row['qty_order'],
                $row['loss'],
                number_format($kg_po, 2),
                '', //terima
                '', // plus atau minus
                '', // terima
                '', // sisa mesin
                $row['sisa_order_pcs'] == 0 ? '' : $row['sisa_order_pcs'],
                $row['poplus_mc_kg'] == 0 ? '' : number_format($row['poplus_mc_kg'], 2),
                '',
                $persenPoplus,
                $row['plus_pck_pcs'] == 0 ? '' : number_format($row['plus_pck_pcs'], 2),
                $row['plus_pck_kg'] == 0 ? '' : number_format($row['plus_pck_kg'], 2),
                '',
                $persenPlusPck,
                '',
                '',
                '',    // Z
                '',    // AA
                '',    // AB
                '',
                $row['ket_area'],
            ], null, 'A' . $rowNum);

            // 🎯 Loop cek kolom L sampai S (ASCII 76 = L, 83 = S)
            $columns = range('L', 'Z'); // L–Z
            $columns = array_merge($columns, ['AA', 'AB', 'AC']); // tambah kolom > Z

            foreach ($columns as $col) {
                $cell = $col . $rowNum;
                $rawValue = $sheet->getCell($cell)->getValue();
                $numericValue = (float)str_replace('%', '', $rawValue);

                if ($numericValue <= 0) {
                    $sheet->getStyle($cell)
                        ->getFont()
                        ->getColor()
                        ->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
                }
            }

            $sheet->getRowDimension($rowNum)->setRowHeight(-1);

            // akumulasi subtotal per grup
            $totalPcsPo                 += $pcs_po;
            $totalKgPo                  += $kg_po;
            $totalTambahanMcPcs         += $tambahanMcPcs;
            $totalTambahanMcKg          += $tambahanMcKg;
            $totalTambahanMcCns         += $tambahanMcCns;
            $totalTambahanPckPcs        += $tambahanPckPcs;
            $totalTambahanPckKg         += $tambahanPckKg;
            $totalTambahanPckCns        += $tambahanPckCns;
            $totalTerimaKg               = $terimaKg;
            $totalSisaBBMc               = $sisaBBMc;
            $totalTambahanKg             = $totalTambahanRow;
            $lastKgsRetur                = $kgsRetur;

            $prevModel = $currentModel;
            $prevKode = $currentKode;
            $prevItemType = $currentItem;

            $rowNum++;
        }
        // ✅ Merge grup terakhir
        $sheet->mergeCells("AD{$groupStartRow}:AD" . ($rowNum - 1));

        $lastRow = $rowNum - 1;
        // Aktifkan wrap text di A11:Q28
        $sheet->getStyle("A11:AD{$lastRow}")->getAlignment()->setWrapText(true);
        // Style semua data
        $sheet->getStyle("A11:AD" . ($rowNum - 1))->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Rata tengah semua isi tabel
        $sheet->getStyle("A11:AD{$lastRow}")->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        // Atur font: bold + size 10
        $sheet->getStyle("A11:AD{$lastRow}")->getFont()
            ->setSize(10);

        $startRow = 11;               // data mulai dari row 11
        $contentLastRow = $rowNum - 1; // baris terakhir yang terisi saat loop selesai

        $sheet->mergeCells("A{$rowNum}:B{$rowNum}");
        // 🚨 Setelah looping selesai, jangan lupa subtotal terakhir
        if ($totalKgPo > 0) {
            // $sheet->mergeCells("I{$rowNum}:J{$rowNum}");
            // $sheet->setCellValue("I{$rowNum}", "TOTAL");
            $sheet->setCellValue("I{$rowNum}", number_format($totalPcsPo, 2));
            $sheet->setCellValue("K{$rowNum}", number_format($totalKgPo, 2));
            $sheet->setCellValue("L{$rowNum}", number_format($totalTerimaKg, 2));
            $sheet->setCellValue("M{$rowNum}", number_format($totalTerimaKg - $totalKgPo, 2));
            $sheet->setCellValue("N{$rowNum}", ($totalKgPo > 0) ? round((round($totalTerimaKg, 2) / round($totalKgPo, 2)) * 100, 2) . '%' : '');
            $sheet->setCellValue("O{$rowNum}", number_format($totalSisaBBMc, 2));
            $sheet->setCellValue("P{$rowNum}", $totalTambahanMcPcs);
            $sheet->setCellValue("Q{$rowNum}", number_format($totalTambahanMcKg, 2));
            $sheet->setCellValue("R{$rowNum}", $totalTambahanMcCns);
            $sheet->setCellValue("S{$rowNum}", ($totalKgPo > 0) ? round((round($totalTambahanMcKg, 2) / round($totalKgPo, 2)) * 100, 2) . '%' : '');
            $sheet->setCellValue("T{$rowNum}", $totalTambahanPckPcs);
            $sheet->setCellValue("U{$rowNum}", number_format($totalTambahanPckKg, 2));
            $sheet->setCellValue("V{$rowNum}", $totalTambahanPckCns);
            $sheet->setCellValue("W{$rowNum}", ($totalKgPo > 0) ? round((round($totalTambahanPckKg, 2) / round($totalKgPo, 2)) * 100, 2) . '%' : '');
            $sheet->setCellValue("X{$rowNum}", $totalTambahanKg);
            $sheet->setCellValue("Y{$rowNum}", ($totalKgPo > 0) ? round((round($totalTambahanKg, 2) / round($totalKgPo, 2)) * 100, 2) . '%' : '');
            $sheet->setCellValue("Z{$rowNum}", number_format($lastKgsRetur, 2));
            $sheet->setCellValue("AA{$rowNum}", ($totalKgPo > 0) ? round((round($lastKgsRetur, 2) / round($totalKgPo, 2)) * 100, 2) . '%' : '');

            // Style untuk baris TOTAL terakhir
            $sheet->getStyle("A{$rowNum}:AD{$rowNum}")->applyFromArray([
                'font' => ['bold' => true, 'size' => 10],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ]);
            $sheet->getRowDimension($rowNum)->setRowHeight(20);
            // 🎯 Tambahin cek nilai kolom L–S di baris TOTAL
            $columns = range('L', 'Z'); // L–Z
            $columns = array_merge($columns, ['AA', 'AB', 'AC']); // tambah kolom > Z

            foreach ($columns as $col) {
                $cell = $col . $rowNum;
                $rawValue = $sheet->getCell($cell)->getValue();
                $numericValue = (float)str_replace('%', '', $rawValue);

                if ($numericValue <= 0) {
                    $sheet->getStyle($cell)
                        ->getFont()
                        ->getColor()
                        ->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
                }
            }
            $lastRow = $rowNum;
        }

        // // Atur baris kosong setelah data sampai baris 28
        // for ($i = $rowNum; $i <= 28; $i++) {
        //     $sheet->mergeCells("A{$i}:B{$i}");
        //     $sheet->getRowDimension($i)->setRowHeight(18); // Tetapkan tinggi tetap
        // }

        // Hitung jumlah baris isi tabel
        $totalIsi = $lastRow - 10; // data mulai dari row 11

        if ($totalIsi < 18) {
            $ttdRow = 30;           // tanda tangan fix di baris 30
            $doubleBorderRow = 33;  // double border sampai baris 33
        } else {
            $ttdRow = $lastRow + 2;      // default perhitungan
            $doubleBorderRow = $ttdRow + 3;
        }

        //Tanda Tangan
        $sheet->mergeCells("A{$ttdRow}:D{$ttdRow}");
        $sheet->setCellValue("A{$ttdRow}", 'MANAJEMEN NS');
        $sheet->getStyle("A{$ttdRow}:D{$ttdRow}")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells("E{$ttdRow}:F{$ttdRow}");
        $sheet->setCellValue("E{$ttdRow}", 'KEPALA AREA');
        $sheet->getStyle("E{$ttdRow}:F{$ttdRow}")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells("G{$ttdRow}:H{$ttdRow}");
        $sheet->setCellValue("G{$ttdRow}", 'IE TEKNISI');
        $sheet->getStyle("G{$ttdRow}:H{$ttdRow}")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells("I{$ttdRow}:K{$ttdRow}");
        $sheet->setCellValue("I{$ttdRow}", 'PIC PACKING');
        $sheet->getStyle("I{$ttdRow}:K{$ttdRow}")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells("L{$ttdRow}:N{$ttdRow}");
        $sheet->setCellValue("L{$ttdRow}", 'PPC');
        $sheet->getStyle("L{$ttdRow}:N{$ttdRow}")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells("O{$ttdRow}:Q{$ttdRow}");
        $sheet->setCellValue("O{$ttdRow}", 'PPC');
        $sheet->getStyle("O{$ttdRow}:Q{$ttdRow}")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells("R{$ttdRow}:T{$ttdRow}");
        $sheet->setCellValue("R{$ttdRow}", 'PPC');
        $sheet->getStyle("R{$ttdRow}:T{$ttdRow}")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells("U{$ttdRow}:W{$ttdRow}");
        $sheet->setCellValue("U{$ttdRow}", 'GD BENANG');
        $sheet->getStyle("U{$ttdRow}:W{$ttdRow}")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells("X{$ttdRow}:AA{$ttdRow}");
        $sheet->setCellValue("X{$ttdRow}", 'MENGETAHUI');
        $sheet->getStyle("X{$ttdRow}:AA{$ttdRow}")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells("AB{$ttdRow}:AD{$ttdRow}");
        $sheet->setCellValue("AB{$ttdRow}", 'MENGETAHUI');
        $sheet->getStyle("AB{$ttdRow}:AD{$ttdRow}")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Atur tinggi baris tanda tangan (5 baris ke bawah biar ada space untuk tanda tangan)
        for ($i = $ttdRow; $i <= $ttdRow + 4; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(18);
        }

        //Outline Border
        // 1. Top double border dari A1 ke Q1
        $sheet->getStyle('A1:AD1')->applyFromArray([
            'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // 2. Right double border dari Q1 ke Q50
        $sheet->getStyle("Q1:AD{$doubleBorderRow}")->applyFromArray([
            'borders' => [
                'right' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // 3. Bottom double border dari A50 ke Q50
        $sheet->getStyle("A{$doubleBorderRow}:AD{$doubleBorderRow}")->applyFromArray([
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // 4. Left double border dari A1 ke A50
        $sheet->getStyle("A1:A{$doubleBorderRow}")->applyFromArray([
            'borders' => [
                'left' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Set judul file dan header untuk download
        $filename = 'Po Tambahan Area ' . $area . ' Tgl ' . $tglBuat . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // Tulis file excel ke output
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
    function excelSisaOrderAllArea()
    {
        $ar = $this->request->getPost('area') ?: "";
        $role = session()->get('role');
        $month = $this->request->getPost('months');
        $yearss = $this->request->getPost('years');

        // Jika bulan atau tahun tidak diisi, gunakan bulan dan tahun ini
        if (empty($month) || empty($yearss)) {
            $bulan = date('Y-m-01', strtotime('this month')); // Bulan ini
        } else {
            // Atur tanggal berdasarkan input bulan dan tahun dari POST
            $bulan = date('Y-m-01', strtotime("$yearss-$month-01"));
        }

        $role = session()->get('role');
        $data = $this->ApsPerstyleModel->getAreaOrder($ar, $bulan);
        // Ambil tanggal awal dan akhir bulan
        $startDate = new \DateTime($bulan); // Awal bulan
        $startDate->setTime(0, 0, 0);
        $endDate = (clone $startDate)->modify('last day of this month');   // Akhir bulan


        $allData = [];
        $totalPerWeek = []; // Untuk menyimpan total produksi per minggu
        foreach ($data as $id) {
            $buyer = $id['kd_buyer_order'];
            $seam = $id['seam'];
            $repeat = $id['repeat'];
            $mastermodel = $id['mastermodel'];
            $machinetypeid = $id['machinetypeid'];
            $factory = $id['factory'];

            // Ambil data qty, sisa, dan produksi
            $qty = $id['qty'];
            $sisa = $id['sisa'];
            $produksi = $qty - $sisa;
            $deliveryDate = new \DateTime($id['delivery']); // Asumsikan ada field delivery

            // Loop untuk membagi data ke dalam minggu
            $weekCount = 1;
            $currentStartDate = clone $startDate;

            for ($weekCount = 1; $currentStartDate <= $endDate; $weekCount++) {
                $endOfWeek = (clone $currentStartDate)->modify('Sunday this week');
                $endOfWeek = min($endOfWeek, $endDate);
                $dateWeek = $currentStartDate->format('d') . " - " . $endOfWeek->format('d');
                $week[$weekCount] = $dateWeek;

                // Periksa apakah tanggal pengiriman berada dalam minggu ini
                if ($deliveryDate >= $currentStartDate && $deliveryDate <= $endOfWeek) {
                    // Ambil total jl_mc untuk minggu ini dan jumlahkan jika sudah ada data sebelumnya
                    $dataOrder = [
                        'model' => $mastermodel,
                        'jarum' => $machinetypeid,
                        'area' => $factory,
                        'delivery' => $id['delivery'],
                    ];
                    $jlMc = 0;
                    $jlMcPlan = 0;
                    $jlMcData = $this->produksiModel->getJlMc($dataOrder);
                    $jlMcPlanning = $this->KebutuhanAreaModel->getJlMcPlanning($dataOrder);

                    // Pastikan data jl_mc ada
                    if ($jlMcData) {
                        // Loop untuk menjumlahkan jl_mc
                        foreach ($jlMcData as $mc) {
                            $jlMc += $mc['jl_mc'];
                        }
                    }
                    if ($jlMcPlanning) {
                        // Loop untuk menjumlahkan jl_mc
                        foreach ($jlMcPlanning as $mcPlan) {
                            $jlMcPlan += $mcPlan['mesin'];
                        }
                    }

                    $allData[$factory][$mastermodel][$machinetypeid][$weekCount][] = json_encode([
                        'del' => $id['delivery'],
                        'qty' => $qty,
                        'prod' => $produksi,
                        'sisa' => $sisa,
                        'jlMc' => $jlMc,
                        'jlMcPlan'  => $jlMcPlan,
                        'buyer' => $buyer,
                        'seam' => $seam,
                        'repeat' => $repeat,
                    ]);

                    // Hitung total per minggu
                    if (!isset($totalPerWeek[$weekCount])) {
                        $totalPerWeek[$weekCount] = [
                            'totalQty' => 0,
                            'totalProd' => 0,
                            'totalSisa' => 0,
                            'totalJlMc' => 0,
                            'totalJlMcPlan' => 0,
                        ];
                    }
                    $totalPerWeek[$weekCount]['totalQty'] += $qty;
                    $totalPerWeek[$weekCount]['totalProd'] += $produksi;
                    $totalPerWeek[$weekCount]['totalSisa'] += $sisa;
                    $totalPerWeek[$weekCount]['totalJlMc'] += $jlMc;
                    $totalPerWeek[$weekCount]['totalJlMcPlan'] += $jlMcPlan;
                }

                // Pindahkan ke minggu berikutnya
                $currentStartDate = (clone $endOfWeek)->modify('+1 day');
            }
        }
        ksort($allData);

        // DATA BY JARUM
        $allDataPerjarum = [];
        $totalPerWeekJrm = []; // Total per minggu
        $dataPerjarum = $this->ApsPerstyleModel->getAreaOrderPejarum($ar, $bulan);

        foreach ($dataPerjarum as $id2) {
            $machinetypeid = $id2['machinetypeid'];
            $delivery = $id2['delivery'];
            $qty = $id2['qty'];
            $sisa = $id2['sisa'];
            $produksi = $qty - $sisa;
            $deliveryDate = new \DateTime($delivery); // Tanggal pengiriman

            $weekCount = 1;
            $currentStartDate = clone $startDate;
            for ($weekCount = 1; $currentStartDate <= $endDate; $weekCount++) {
                $endOfWeek = (clone $currentStartDate)->modify('Sunday this week');
                $endOfWeek = min($endOfWeek, $endDate);

                // Periksa apakah tanggal pengiriman berada dalam minggu ini
                if ($deliveryDate >= $currentStartDate && $deliveryDate <= $endOfWeek) {
                    $jlMcJrm = 0;
                    $dataOrder2 = [
                        'area' => $ar,
                        'jarum' => $machinetypeid,
                        'delivery' => $delivery,
                    ];
                    $jlMcJrmData = $this->produksiModel->getJlMcJrmArea($dataOrder2);
                    if ($jlMcJrmData) {
                        foreach ($jlMcJrmData as $mcJrm) {
                            $jlMcJrm += $mcJrm['jl_mc'];
                        }
                    }

                    // Pastikan array utama memiliki key jarum
                    if (!isset($allDataPerjarum[$machinetypeid])) {
                        $allDataPerjarum[$machinetypeid] = [];
                    }
                    // Pastikan minggu tersedia
                    if (!isset($allDataPerjarum[$machinetypeid][$weekCount])) {
                        $allDataPerjarum[$machinetypeid][$weekCount] = [
                            'qtyJrm' => 0,
                            'prodJrm' => 0,
                            'sisaJrm' => 0,
                            'jlMcJrm' => 0,
                        ];
                    }

                    // Tambahkan data minggu
                    $allDataPerjarum[$machinetypeid][$weekCount]['qtyJrm'] += $qty;
                    $allDataPerjarum[$machinetypeid][$weekCount]['prodJrm'] += $produksi;
                    $allDataPerjarum[$machinetypeid][$weekCount]['sisaJrm'] += $sisa;
                    $allDataPerjarum[$machinetypeid][$weekCount]['jlMcJrm'] += $jlMcJrm;

                    // Hitung total per minggu
                    if (!isset($totalPerWeekJrm[$weekCount])) {
                        $totalPerWeekJrm[$weekCount] = [
                            'totalQty' => 0,
                            'totalProd' => 0,
                            'totalSisa' => 0,
                            'totalJlMc' => 0,
                        ];
                    }
                    $totalPerWeekJrm[$weekCount]['totalQty'] += $qty;
                    $totalPerWeekJrm[$weekCount]['totalProd'] += $produksi;
                    $totalPerWeekJrm[$weekCount]['totalSisa'] += $sisa;
                    $totalPerWeekJrm[$weekCount]['totalJlMc'] += $jlMcJrm;
                }

                // Pindahkan ke minggu berikutnya
                $currentStartDate = (clone $endOfWeek)->modify('+1 day');
            }
        }
        $maxWeek = $weekCount - 1;

        // Generate Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet()->setTitle("Report Sisa Order");

        $styleTitle = [
            'font' => [
                'bold' => true, // Tebalkan teks
                'color' => ['argb' => 'FF000000'],
                'size' => 30
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER, // Alignment rata tengah
            ],
        ];

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
                'vertical' => Alignment::VERTICAL_CENTER
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

        // Judul
        $sheet->setCellValue('A1', 'SISA PRODUKSI ' . $ar . ' Bulan ' . date('F', strtotime($bulan)));

        $row_header = 3;
        $row_header2 = 4;

        // Tambahkan header
        $sheet->setCellValue('A' . $row_header, 'BUYER');
        $sheet->setCellValue('B' . $row_header, 'AREAL');
        $sheet->setCellValue('C' . $row_header, 'PDK');
        $sheet->setCellValue('D' . $row_header, 'SEAM');
        $sheet->setCellValue('E' . $row_header, 'REPEAT');
        $sheet->setCellValue('F' . $row_header, 'JARUM');
        $sheet->mergeCells('A' . $row_header . ':' . 'A' . $row_header2);
        $sheet->getStyle('A' . $row_header . ':' . 'A' . $row_header2)->applyFromArray($styleHeader);
        $sheet->mergeCells('B' . $row_header . ':' . 'B' . $row_header2);
        $sheet->getStyle('B' . $row_header . ':' . 'B' . $row_header2)->applyFromArray($styleHeader);
        $sheet->mergeCells('C' . $row_header . ':' . 'C' . $row_header2);
        $sheet->getStyle('C' . $row_header . ':' . 'C' . $row_header2)->applyFromArray($styleHeader);
        $sheet->mergeCells('D' . $row_header . ':' . 'D' . $row_header2);
        $sheet->getStyle('D' . $row_header . ':' . 'D' . $row_header2)->applyFromArray($styleHeader);
        $sheet->mergeCells('E' . $row_header . ':' . 'E' . $row_header2);
        $sheet->getStyle('E' . $row_header . ':' . 'E' . $row_header2)->applyFromArray($styleHeader);
        $sheet->mergeCells('F' . $row_header . ':' . 'F' . $row_header2);
        $sheet->getStyle('F' . $row_header . ':' . 'F' . $row_header2)->applyFromArray($styleHeader);

        // looping week
        $col = 'G'; // Kolom awal week
        $col2 = 'L'; // Kolom akhir week

        // Konversi huruf kolom ke nomor indeks kolom
        $col_index = Coordinate::columnIndexFromString($col);
        $col2_index = Coordinate::columnIndexFromString($col2);

        for ($i = 1; $i <= $maxWeek; $i++) {
            $sheet->setCellValue($col . $row_header, 'WEEK ' . $i . ' (' . $week[$i] . ')');
            $sheet->mergeCells($col . $row_header . ':' . $col2 . $row_header); // Merge sel antara kolom $col dan $col2
            $sheet->getStyle($col . $row_header . ':' . $col2 . $row_header)->applyFromArray($styleHeader);


            // Tambahkan 2 pada indeks kolom
            $col_index += 6;
            $col2_index = $col_index + 5; // Tambahkan 1 pada indeks kedua kolom

            // Konversi kembali dari nomor indeks kolom ke huruf kolom
            $col = Coordinate::stringFromColumnIndex($col_index);
            $col2 = Coordinate::stringFromColumnIndex($col2_index);
        }
        $col3 = $col;
        $sheet->setCellValue($col3 . $row_header, 'KETERANGAN');
        $sheet->mergeCells($col3 . $row_header . ':' . $col3 . $row_header2);
        $sheet->getStyle($col3 . $row_header . ':' . $col3 . $row_header2)->applyFromArray($styleHeader);


        // Menghitung kolom terakhir untuk menggabungkan judul
        $col_last_index = Coordinate::columnIndexFromString($col3); // Indeks kolom KETERANGAN
        $col_last = Coordinate::stringFromColumnIndex($col_last_index); // Mengonversi indeks kolom ke huruf kolom

        // Style Judul
        $sheet->mergeCells('A1:' . $col_last . '1');
        $sheet->getStyle('A1')->applyFromArray($styleTitle);

        $col4 = 'G';
        // Tambahkan header dinamis untuk tanggal produksi
        for ($i = 1; $i <= $maxWeek; $i++) {
            $sheet->setCellValue($col4 . $row_header2, 'DEL');
            $sheet->getStyle($col4 . $row_header2)->applyFromArray($styleHeader);
            $col4++;
            $sheet->setCellValue($col4 . $row_header2, 'QTY');
            $sheet->getStyle($col4 . $row_header2)->applyFromArray($styleHeader);
            $col4++;
            $sheet->setCellValue($col4 . $row_header2, 'PROD');
            $sheet->getStyle($col4 . $row_header2)->applyFromArray($styleHeader);
            $col4++;
            $sheet->setCellValue($col4 . $row_header2, 'SISA');
            $sheet->getStyle($col4 . $row_header2)->applyFromArray($styleHeader);
            $col4++;
            $sheet->setCellValue($col4 . $row_header2, 'JLN MC ACT');
            $sheet->getStyle($col4 . $row_header2)->applyFromArray($styleHeader);
            $col4++;
            $sheet->setCellValue($col4 . $row_header2, 'JLN MC PLAN');
            $sheet->getStyle($col4 . $row_header2)->applyFromArray($styleHeader);
            $col4++;
        }

        // dd($allData);

        // Mulai di baris 5
        $row = 5;
        foreach ($allData as $area => $models) {
            // Hitung total baris untuk AREA
            $rowsArea = 0;
            foreach ($models as $model => $jarums) {
                $countjarums = count($jarums);
                $rowsArea += $countjarums;
                foreach ($jarums as $jarum => $weeks) {
                    $maxCountWeek = 0;
                    foreach ($weeks as $weekEntries) {
                        $countWeek = count($weekEntries);
                        if ($countWeek > $maxCountWeek) {
                            $maxCountWeek = $countWeek - 1; // simpan yang paling besar
                        }
                    }
                    $rowsArea += $maxCountWeek; // tambah ke total per model
                }
            }
            // dd($rowsArea);
            // if ($rowsArea === 0) {
            //     continue;
            // }
            $startRowArea = $row;
            $endRowArea = $startRowArea + $rowsArea - 1;

            // dd($endRowArea);
            // Merge & tulis kolom B (area)
            $sheet->setCellValue('B' . $startRowArea, $area);
            $sheet->mergeCells('B' . $startRowArea . ':B' . $endRowArea);
            $sheet->getStyle('B' . $startRowArea . ':B' . $endRowArea)->applyFromArray($styleBody);
            // dd($endRowArea);

            // 2. Loop per model
            foreach ($models as $model => $jarums) {
                $rowsModel = 0;
                $counjarums = count($jarums);
                $rowsModel += $counjarums;
                // Hitung rowsModel
                foreach ($jarums as $jarum => $weeks) {
                    $maxCountWeek = 0;
                    foreach ($weeks as $weekEntries) {
                        $countWeek = count($weekEntries);
                        if ($countWeek > $maxCountWeek) {
                            $maxCountWeek = $countWeek - 1; // simpan yang paling besar
                        }
                    }
                    $rowsModel += $maxCountWeek; // tambah ke total per model
                }
                $startRowModel = $row;
                $endRowModel = $startRowModel + $rowsModel - 1;

                // === Ambil buyer (dan seam jika ingin) untuk seluruh model ini ===
                $buyer = '';
                $seam = '';
                $repeat = '';
                // scan semua jarum & minggu di model untuk cari first non-empty entry
                foreach ($jarums as $jarumScan => $weeksScan) {
                    $found = false;
                    foreach ($weeksScan as $weekEntriesScan) {
                        if (!empty($weekEntriesScan)) {
                            $entryData = json_decode($weekEntriesScan[0], true);
                            if (isset($entryData['buyer'])) {
                                $buyer = $entryData['buyer'];
                            }
                            if (isset($entryData['seam'])) {
                                $seam = $entryData['seam'];
                            }
                            if (isset($entryData['repeat'])) {
                                $repeat = $entryData['repeat'];
                            }
                            $found = true;
                            break;
                        }
                    }
                    if ($found) {
                        break;
                    }
                }
                // Merge & tulis kolom A (buyer) untuk model ini
                $sheet->setCellValue('A' . $startRowModel, $buyer);
                $sheet->mergeCells('A' . $startRowModel . ':A' . $endRowModel);
                $sheet->getStyle('A' . $startRowModel . ':A' . $endRowModel)->applyFromArray($styleBody);

                // Merge & tulis kolom C (No Model)
                $sheet->setCellValue('C' . $startRowModel, $model);
                $sheet->mergeCells('C' . $startRowModel . ':C' . $endRowModel);
                $sheet->getStyle('C' . $startRowModel . ':C' . $endRowModel)->applyFromArray($styleBody);

                $sheet->setCellValue('D' . $startRowModel, $seam);
                $sheet->mergeCells('D' . $startRowModel . ':D' . $endRowModel);
                $sheet->getStyle('D' . $startRowModel . ':D' . $endRowModel)->applyFromArray($styleBody);

                $sheet->setCellValue('E' . $startRowModel, $repeat);
                $sheet->mergeCells('E' . $startRowModel . ':E' . $endRowModel);
                $sheet->getStyle('E' . $startRowModel . ':E' . $endRowModel)->applyFromArray($styleBody);

                // 3. Loop per jarum
                foreach ($jarums as $jarum => $weeks) {
                    // Hitung rowsJarum
                    $rowsJarum = 1;
                    foreach ($weeks as $weekEntries) {
                        $countWeek = count($weekEntries);
                        if ($countWeek > $rowsJarum) {
                            $rowsJarum = $countWeek;
                        }
                    }
                    $startRowJarum = $row;
                    $endRowJarum = $startRowJarum + $rowsJarum - 1;
                    // dd($endRowJarum);

                    // Merge & tulis kolom D (jarum)
                    $sheet->setCellValue('F' . $startRowJarum, $jarum);
                    $sheet->mergeCells('F' . $startRowJarum . ':F' . $endRowJarum);
                    $sheet->getStyle('F' . $startRowJarum . ':F' . $endRowJarum)->applyFromArray($styleBody);

                    // Pastikan setiap minggu ada key
                    for ($w = 1; $w <= $maxWeek; $w++) {
                        if (!isset($weeks[$w])) {
                            $weeks[$w] = [];
                        }
                    }
                    // Siapkan pointer per minggu dengan key 1..$maxWeek
                    $pointers = [];
                    for ($w = 1; $w <= $maxWeek; $w++) {
                        $pointers[$w] = 0;
                    }
                    $totalRows = $rowsJarum;

                    // Loop menulis baris untuk jarum ini
                    for ($offset = 0; $offset < $totalRows; $offset++) {
                        for ($weekNum = 1; $weekNum <= $maxWeek; $weekNum++) {
                            $baseColIndex = Coordinate::columnIndexFromString('G') + ($weekNum - 1) * 6;
                            $cols = [];
                            for ($k = 0; $k < 6; $k++) {
                                $cols[] = Coordinate::stringFromColumnIndex($baseColIndex + $k);
                            }
                            if ($pointers[$weekNum] < count($weeks[$weekNum])) {
                                $json = $weeks[$weekNum][$pointers[$weekNum]];
                                $data = json_decode($json, true) ?: [];
                                $del  = $data['del']  ?? '';
                                $qty  = $data['qty']  ?? '';
                                $prod = $data['prod'] ?? '';
                                $sisa = $data['sisa'] ?? '';
                                $jlMc = $data['jlMc'] ?? '';
                                $jlMcPlan = $data['jlMcPlan'] ?? '';
                                $sheet->setCellValue($cols[0] . $row, $del !== 0 ? $del : ($del === 0 ? 0 : ''));
                                $sheet->getStyle($cols[0] . $row)->applyFromArray($styleBody);
                                $sheet->setCellValue($cols[1] . $row, $qty !== 0 ? $qty : ($qty === 0 ? 0 : ''));
                                $sheet->getStyle($cols[1] . $row)->applyFromArray($styleBody);
                                $sheet->setCellValue($cols[2] . $row, $prod !== 0 ? $prod : '-');
                                $sheet->getStyle($cols[2] . $row)->applyFromArray($styleBody);
                                $sheet->setCellValue($cols[3] . $row, $sisa !== 0 ? $sisa : '-');
                                $sheet->getStyle($cols[3] . $row)->applyFromArray($styleBody);
                                $sheet->setCellValue($cols[4] . $row, $jlMc !== 0 ? $jlMc : '-');
                                $sheet->getStyle($cols[4] . $row)->applyFromArray($styleBody);
                                $sheet->setCellValue($cols[5] . $row, $jlMcPlan !== 0 ? $jlMcPlan : '-');
                                $sheet->getStyle($cols[5] . $row)->applyFromArray($styleBody);
                                $pointers[$weekNum]++;
                            } else {
                                foreach ($cols as $colLetter) {
                                    $sheet->setCellValue($colLetter . $row, '');
                                    $sheet->getStyle($colLetter . $row)->applyFromArray($styleBody);
                                }
                            }
                            $sheet->setCellValue($col3 . $row, '');
                            $sheet->getStyle($col3 . $row)->applyFromArray($styleBody);
                        }
                        $row++;
                    }
                    // Setelah selesai jarum, $row == $endRowJarum + 1
                }
                // Setelah selesai semua jarum di model, $row == $endRowModel + 1
            }
            // Setelah selesai semua model di area, $row == $endRowArea + 1
        }
        // dd($allData);


        // TOTAL

        $sheet->setCellValue('A' . $row, 'TOTAL');
        $sheet->setCellValue('B' . $row, '');
        $sheet->setCellValue('C' . $row, '');
        $sheet->setCellValue('D' . $row, '');
        $sheet->setCellValue('E' . $row, '');
        $sheet->setCellValue('F' . $row, '');
        $sheet->getStyle('A' . $row)->applyFromArray($styleHeader);
        $sheet->getStyle('B' . $row)->applyFromArray($styleHeader);
        $sheet->getStyle('C' . $row)->applyFromArray($styleHeader);
        $sheet->getStyle('D' . $row)->applyFromArray($styleHeader);
        $sheet->getStyle('E' . $row)->applyFromArray($styleHeader);
        $sheet->getStyle('F' . $row)->applyFromArray($styleHeader);
        $col6 = 'G';
        for ($i = 1; $i <= $maxWeek; $i++) {
            $sheet->setCellValue($col6 . $row, '');
            $sheet->getStyle($col6 . $row)->applyFromArray($styleHeader);
            $col6++;
            $sheet->setCellValue($col6 . $row, isset($totalPerWeek[$i]['totalQty']) && $totalPerWeek[$i]['totalQty'] != 0 ? $totalPerWeek[$i]['totalQty'] : '-');
            $sheet->getStyle($col6 . $row)->applyFromArray($styleHeader);
            $col6++;
            $sheet->setCellValue($col6 . $row, isset($totalPerWeek[$i]['totalProd']) && $totalPerWeek[$i]['totalProd'] != 0 ? $totalPerWeek[$i]['totalProd'] : '-');
            $sheet->getStyle($col6 . $row)->applyFromArray($styleHeader);
            $col6++;
            $sheet->setCellValue($col6 . $row, isset($totalPerWeek[$i]['totalSisa']) && $totalPerWeek[$i]['totalSisa'] != 0 ? $totalPerWeek[$i]['totalSisa'] : '-');
            $sheet->getStyle($col6 . $row)->applyFromArray($styleHeader);
            $col6++;
            $sheet->setCellValue($col6 . $row, isset($totalPerWeek[$i]['totalJlMc']) && $totalPerWeek[$i]['totalJlMc'] != 0 ? $totalPerWeek[$i]['totalJlMc'] : '-');
            $sheet->getStyle($col6 . $row)->applyFromArray($styleHeader);
            $col6++;
            $sheet->setCellValue($col6 . $row, isset($totalPerWeek[$i]['totalJlMcPlan']) && $totalPerWeek[$i]['totalJlMcPlan'] != 0 ? $totalPerWeek[$i]['totalJlMcPlan'] : '-');
            $sheet->getStyle($col6 . $row)->applyFromArray($styleHeader);
            $col6++;
        }
        $col7 = $col6;
        $sheet->setCellValue($col7 . $row, '');
        $sheet->getStyle($col7 . $row)->applyFromArray($styleHeader);

        $rowJudul = $row + 3;
        $row_header3 = $rowJudul + 2;
        $row_header4 = $row_header3 + 1;

        // Judul
        $sheet->setCellValue('A' . $rowJudul, 'SISA PRODUKSI PERJARUM');

        $sheet->setCellValue('A' . $row_header3, 'JARUM');
        $sheet->mergeCells('A' . $row_header3 . ':' . 'A' . $row_header4);
        $sheet->getStyle('A' . $row_header3 . ':' . 'A' . $row_header4)->applyFromArray($styleHeader);

        // Kolom awal untuk looping week
        $col_idx = Coordinate::columnIndexFromString('B');
        $col2_idx = $col_idx + 3;

        for ($i = 1; $i <= $maxWeek; $i++) {
            // Set kolom untuk week ke-i
            $colJrm = Coordinate::stringFromColumnIndex($col_idx);
            $colJrm2 = Coordinate::stringFromColumnIndex($col2_idx);

            $sheet->setCellValue($colJrm . $row_header3, 'WEEK ' . $i . ' (' . $week[$i] . ')');
            $sheet->mergeCells($colJrm . $row_header3 . ':' . $colJrm2 . $row_header3); // Merge sel antara kolom $col dan $col2
            $sheet->getStyle($colJrm . $row_header3 . ':' . $colJrm2 . $row_header3)->applyFromArray($styleHeader);


            // Tambahkan 2 pada indeks kolom
            $col_idx += 4;
            $col2_idx = $col_idx + 3; // Tambahkan 1 pada indeks kedua kolom
        }

        $colEnd = Coordinate::stringFromColumnIndex($col_idx - 1);
        // Style Judul
        $sheet->mergeCells('A' . $rowJudul . ':' . $colEnd . $rowJudul);
        $sheet->getStyle('A' . $rowJudul . ':' . $colEnd . $rowJudul)->applyFromArray($styleTitle);

        $col8 = 'B';
        // Tambahkan header dinamis untuk tanggal produksi
        for ($i = 1; $i <= $maxWeek; $i++) {
            $sheet->setCellValue($col8 . $row_header4, 'QTY');
            $sheet->getStyle($col8 . $row_header4)->applyFromArray($styleHeader);
            $col8++;
            $sheet->setCellValue($col8 . $row_header4, 'PROD');
            $sheet->getStyle($col8 . $row_header4)->applyFromArray($styleHeader);
            $col8++;
            $sheet->setCellValue($col8 . $row_header4, 'SISA');
            $sheet->getStyle($col8 . $row_header4)->applyFromArray($styleHeader);
            $col8++;
            $sheet->setCellValue($col8 . $row_header4, 'JLN MC');
            $sheet->getStyle($col8 . $row_header4)->applyFromArray($styleHeader);
            $col8++;
        }

        $row = $row_header4 + 1;
        foreach ($allDataPerjarum as $jarum => $idJrm) {
            $sheet->setCellValue('A' . $row, $jarum);
            $sheet->getStyle('A' . $row)->applyFromArray($styleBody);
            $col5 = 'B';
            for ($i = 1; $i <= $maxWeek; $i++) {
                // Mengecek apakah week ada di data
                if (isset($idJrm[$i])) {
                    // Ambil data per week
                    $qtyJrm = $idJrm[$i]['qtyJrm'] ?? 0;
                    $prodJrm = $idJrm[$i]['prodJrm'] ?? 0;
                    $sisaJrm = $idJrm[$i]['sisaJrm'] ?? 0;
                    $jlMcJrm = $idJrm[$i]['jlMcJrm'] ?? 0;

                    $sheet->setCellValue($col5 . $row, $qtyJrm !== 0 ? $qtyJrm : '-');
                    $sheet->getStyle($col5 . $row)->applyFromArray($styleBody);
                    $col5++;
                    $sheet->setCellValue($col5 . $row, $prodJrm !== 0 ? $prodJrm : '-');
                    $sheet->getStyle($col5 . $row)->applyFromArray($styleBody);
                    $col5++;
                    $sheet->setCellValue($col5 . $row, $sisaJrm !== 0 ? $sisaJrm : '-');
                    $sheet->getStyle($col5 . $row)->applyFromArray($styleBody);
                    $col5++;
                    $sheet->setCellValue($col5 . $row, $jlMcJrm !== 0 ? $jlMcJrm : '-');
                    $sheet->getStyle($col5 . $row)->applyFromArray($styleBody);
                    $col5++;
                } else {
                    $sheet->setCellValue($col5 . $row, '');
                    $sheet->getStyle($col5 . $row)->applyFromArray($styleBody);
                    $col5++;
                    $sheet->setCellValue($col5 . $row, '');
                    $sheet->getStyle($col5 . $row)->applyFromArray($styleBody);
                    $col5++;
                    $sheet->setCellValue($col5 . $row, '');
                    $sheet->getStyle($col5 . $row)->applyFromArray($styleBody);
                    $col5++;
                    $sheet->setCellValue($col5 . $row, '');
                    $sheet->getStyle($col5 . $row)->applyFromArray($styleBody);
                    $col5++;
                }
            }
            $row++;
        }
        // TOTAL

        $sheet->setCellValue('A' . $row, 'TOTAL');
        $sheet->getStyle('A' . $row)->applyFromArray($styleHeader);

        $col6 = 'B';
        for ($i = 1; $i <= $maxWeek; $i++) {
            $sheet->setCellValue($col6 . $row, isset($totalPerWeekJrm[$i]['totalQty']) && $totalPerWeekJrm[$i]['totalQty'] != 0 ? $totalPerWeekJrm[$i]['totalQty'] : '-');
            $sheet->getStyle($col6 . $row)->applyFromArray($styleHeader);
            $col6++;
            $sheet->setCellValue($col6 . $row, isset($totalPerWeekJrm[$i]['totalProd']) && $totalPerWeekJrm[$i]['totalProd'] != 0 ? $totalPerWeekJrm[$i]['totalProd'] : '-');
            $sheet->getStyle($col6 . $row)->applyFromArray($styleHeader);
            $col6++;
            $sheet->setCellValue($col6 . $row, isset($totalPerWeekJrm[$i]['totalSisa']) && $totalPerWeekJrm[$i]['totalSisa'] != 0 ? $totalPerWeekJrm[$i]['totalSisa'] : '-');
            $sheet->getStyle($col6 . $row)->applyFromArray($styleHeader);
            $col6++;
            $sheet->setCellValue($col6 . $row, isset($totalPerWeekJrm[$i]['totalJlMc']) && $totalPerWeekJrm[$i]['totalJlMc'] != 0 ? $totalPerWeekJrm[$i]['totalJlMc'] : '-');
            $sheet->getStyle($col6 . $row)->applyFromArray($styleHeader);
            $col6++;
        }

        // Set sheet pertama sebagai active sheet
        $spreadsheet->setActiveSheetIndex(0);

        // Export file ke Excel
        $writer = new Xlsx($spreadsheet);
        $filename = 'Sisa Produksi ' . $ar . ' Bulan ' . date('F', strtotime($bulan)) . '.xlsx';

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }
    public function exportDataOrderArea()
    {
        $selected = $this->request->getPost('searchModel');
        list($pdk, $factory) = explode('|', $selected);

        $data = $this->ApsPerstyleModel->getDataModel($factory, $pdk);
        // dd($factory, $pdk, $data);
        // Buat file Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $styleTitle = [
            'font' => [
                'bold' => true, // Tebalkan teks
                'color' => ['argb' => 'FF000000'],
                'size' => 15
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER, // Alignment rata tengah
            ],
        ];

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

        $sheet->setCellValue('A1', 'DATA ORDER ' . $pdk);
        $sheet->mergeCells('A1:G1');
        $sheet->getStyle('A1:G1')->applyFromArray($styleTitle);
        // Tulis header
        $sheet->setCellValue('A3', 'JO');
        $sheet->setCellValue('B3', 'Delivery Date');
        $sheet->setCellValue('C3', 'Qty');
        $sheet->setCellValue('D3', 'Customer');
        $sheet->setCellValue('E3', 'No Order');
        $sheet->setCellValue('F3', 'Style');
        $sheet->setCellValue('G3', 'Product Type');
        $sheet->setCellValue('H3', 'Std. Time(s)');

        $sheet->getStyle('A3')->applyFromArray($styleHeader);
        $sheet->getStyle('B3')->applyFromArray($styleHeader);
        $sheet->getStyle('C3')->applyFromArray($styleHeader);
        $sheet->getStyle('D3')->applyFromArray($styleHeader);
        $sheet->getStyle('E3')->applyFromArray($styleHeader);
        $sheet->getStyle('F3')->applyFromArray($styleHeader);
        $sheet->getStyle('G3')->applyFromArray($styleHeader);
        $sheet->getStyle('H3')->applyFromArray($styleHeader);

        // Tulis data mulai dari baris 4
        $row = 4;
        $deliveryOrderMap = [];

        // 1. Kumpulkan dan sort tanggal delivery per no_model
        foreach ($data as $item) {
            $no_model = $item['no_model'];
            $delivery = $item['delivery'];

            if (!isset($deliveryOrderMap[$no_model])) {
                $deliveryOrderMap[$no_model] = [];
            }

            if (!in_array($delivery, $deliveryOrderMap[$no_model])) {
                $deliveryOrderMap[$no_model][] = $delivery;
            }
        }

        // 2. Sort delivery date ASCENDING untuk setiap no_model
        foreach ($deliveryOrderMap as $no_model => &$dates) {
            usort($dates, function ($a, $b) {
                return strtotime($a) <=> strtotime($b);
            });
        }
        // un-reference
        unset($dates);

        // 3. Buat mapping delivery → nomor urut
        $deliveryIndexMap = [];
        foreach ($deliveryOrderMap as $no_model => $dates) {
            foreach ($dates as $i => $date) {
                $deliveryIndexMap[$no_model][$date] = $i + 1; // mulai dari 1
            }
        }

        // 4. Loop data asli tanpa diurutkan, tapi ambil urutan delivery dari mapping
        foreach ($data as $item) {
            $no_model = $item['no_model'];
            $delivery = $item['delivery'];
            $urutan = $deliveryIndexMap[$no_model][$delivery] ?? 1;

            $sheet->setCellValue('A' . $row, $no_model . '/' . $urutan . ' ' . $item['machinetypeid']);
            $sheet->setCellValue('B' . $row, $item['delivery']);
            $sheet->setCellValue('C' . $row, $item['qty']);
            $sheet->setCellValue('D' . $row, $item['kd_buyer_order']);
            $sheet->setCellValue('E' . $row, $item['no_order']);
            $sheet->setCellValue('F' . $row, $item['size']);
            $sheet->setCellValue('G' . $row, $item['product_type']);
            $sheet->setCellValue('H' . $row, $item['smv']);

            foreach (range('A', 'H') as $col) {
                $sheet->getStyle($col . $row)->applyFromArray($styleBody);
            }

            $row++;
        }

        // Set lebar kolom agar menyesuaikan isi
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Buat writer dan output file Excel
        $writer = new Xlsx($spreadsheet);
        $fileName = 'Data Order' . $pdk . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function exportProd()
    {
        $bulan = $this->request->getGet('bulan');
        $tahun = $this->request->getGet('tahun');
        $area = $this->request->getGet('area');
        // dd($bulan, $tahun, $area);
        // Ambil data berdasarkan filter
        $data = $this->produksiModel->getProductionStats($bulan, $tahun, $area);
        // dd ($data);
        // Buat file Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Tulis data ke dalam sheet
        $this->writeDataToSheet($sheet, $data, $tahun, $bulan, $area);

        // Buat writer dan output file Excel
        $writer = new Xlsx($spreadsheet);
        $fileName = 'Report MC Area ' . $area . ' (' . strtoupper(date('F Y', strtotime("$tahun-$bulan-01"))) . ').xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    private function writeDataToSheet($sheet, $data, $tahun, $bulan, $area)
    {
        // ... [style definitions remain unchanged] ...
        $styleSubHeader = [
            'font' => [
                'bold' => true,
                'size' => 12,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];
        // Set header style
        $styleHeader = [
            'font' => [
                'bold' => true,
                'size' => 12,
            ],
            // 'alignment' => [
            //     'horizontal' => Alignment::HORIZONTAL_CENTER,
            //     'vertical'   => Alignment::VERTICAL_CENTER,
            // ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];
        $styleBody = [
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];
        // Get unique dates
        $dates = array_unique(array_column($data, 'tgl_produksi'));
        sort($dates);
        $dateCount = count($dates);

        // Calculate total columns (4 base columns + 6 columns per date)
        $totalColumns = 4 + (6 * $dateCount);

        // Get last column letter correctly
        $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($totalColumns);

        // 1. Area header (merged)
        $sheet->setCellValue('A1', 'Area: ' . $area);
        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray($styleHeader);

        // 2. Month header (merged)
        $sheet->setCellValue('A2', 'Bulan: ' . strtoupper(date('F Y', strtotime("$tahun-$bulan-01"))));
        $sheet->mergeCells("A2:{$lastCol}2");
        $sheet->getStyle("A2:{$lastCol}2")->applyFromArray($styleHeader);

        // 3. Date headers
        $baseHeaders = ['Buyer', 'PDK', 'Style-size', 'Jarum'];
        $col = 'A';

        // Write base headers
        foreach ($baseHeaders as $h) {
            $sheet->setCellValue($col . '3', $h);
            $sheet->mergeCells("{$col}3:{$col}4");
            $sheet->getStyle($col . '3:' . $col . '4')->applyFromArray($styleSubHeader);
            $col++;
        }

        // Write date headers (merged per 6 columns)
        foreach ($dates as $tgl) {
            $tglLabel = date('d M Y', strtotime($tgl));
            $startCol = $col;
            $endCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(
                \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($col) + 5
            );

            $sheet->setCellValue($startCol . '3', $tglLabel);
            $sheet->mergeCells("{$startCol}3:{$endCol}3");
            $sheet->getStyle("{$startCol}3:{$endCol}3")->applyFromArray($styleSubHeader);

            // Move to next group
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(
                \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($endCol) + 1
            );
        }

        // 4. Sub-headers
        $subHeaders = ['Prod', 'Jl MC', 'Prod/mc', 'Target', 'Productivity', 'Loss'];
        $col = 'E'; // Start after base headers

        foreach ($dates as $tgl) {
            foreach ($subHeaders as $sh) {
                $sheet->setCellValue($col . '4', $sh);
                $sheet->getStyle($col . '4')->applyFromArray($styleSubHeader);
                $col++;
            }
        }

        // 5. Data rows
        $row = 5;
        foreach ($data as $item) {
            $sheet->setCellValue("A{$row}", $item['buyer']);
            $sheet->setCellValue("B{$row}", $item['mastermodel']);
            $sheet->setCellValue("C{$row}", $item['size']);
            $sheet->setCellValue("D{$row}", $item['machinetypeid']);

            $col = 'E'; // Start after base headers
            foreach ($dates as $tgl) {
                $val = $item['perDate'][$tgl] ?? [
                    'prod' => $item['prod'] ?? 0,
                    'jl_mc' => $item['jl_mc'] ?? 0,
                    'prodmc' => $item['prodmc'] ?? 0,
                    'target' => $item['target'] ?? 0,
                    'productivity' => $item['productivity'] ?? '0%',
                    'loss' => $item['loss'] ?? '0%'
                ];
                // dd ($val);
                $sheet->setCellValue($col . $row, number_format($val['prod'], 2, '.', ','));
                $col++;
                $sheet->setCellValue($col . $row, $val['jl_mc']);
                $col++;
                $sheet->setCellValue($col . $row, number_format($val['prodmc'], 2, '.', ','));
                $col++;
                $sheet->setCellValue($col . $row, number_format($val['target'], 2, '.', ','));
                $col++;
                $sheet->setCellValue($col . $row, number_format($val['productivity'], 2, '.', ',') . '%');
                $col++;
                $sheet->setCellValue($col . $row, number_format($val['loss'], 2, '.', ',') . '%');
                $col++;
            }

            $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray($styleBody);
            $row++;
        }

        // 6. Auto-size columns
        for ($i = 1; $i <= $totalColumns; $i++) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    public function exportExcelJatahNoModel()
    {
        $noModel = $this->request->getGet('no_model');
        $pdk = $this->ApsPerstyleModel->getPembagianModel($noModel);

        $grouped = [];

        foreach ($pdk as $row) {
            $key = $row['size'] . '|' . $row['machinetypeid'] . '|' . $row['color'];

            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'size' => $row['size'],
                    'machinetypeid' => $row['machinetypeid'],
                    'color' => $row['color'],
                    'total_qty' => 0,
                    'areas' => []
                ];
            }

            $grouped[$key]['total_qty'] += (int)$row['qty'];
            $grouped[$key]['areas'][$row['factory']] =
                ($grouped[$key]['areas'][$row['factory']] ?? 0) + (int)$row['qty'];
        }

        // Inisialisasi data default
        $order          = [];
        $headerRow      = [];
        $result         = [];
        $areas          = [];
        $totalPo        = 0;
        $models         = [];
        $totalAllDelivery = [];

        if ($noModel) {
            //
            // 1) Ambil headerRow & hitung totalQty per delivery (untuk $order)
            //
            $order = $this->ApsPerstyleModel->getQtyArea($noModel) ?: [];

            $apiUrl = api_url('material') . 'pph?model=' . urlencode($noModel);
            $material = @file_get_contents($apiUrl);

            // $models = [];
            if ($material !== FALSE) {
                $models = json_decode($material, true);
            }

            // Ambil semua area unik dari $order

            foreach ($order as $ord) {
                if (!in_array($ord['area'], $areas)) {
                    $areas[] = $ord['area'];
                }
            }
            sort($areas);

            // Kelompokkan order berdasarkan style_size, lalu delivery dan area
            $groupedOrders = [];
            foreach ($order as $ord) {
                $style_size = $ord['size'];
                $delivery = $ord['delivery'];
                $area = $ord['area'];
                $qty = $ord['qty'];
                $sisa = $ord['sisa'];

                if (!isset($groupedOrders[$style_size][$delivery][$area])) {
                    $groupedOrders[$style_size][$delivery][$area] = [
                        'qty' => 0,
                        'sisa' => 0,
                    ];
                }

                $groupedOrders[$style_size][$delivery][$area]['qty'] += $qty;
                $groupedOrders[$style_size][$delivery][$area]['sisa'] += $sisa;
            }

            // Hitung total per kombinasi delivery, item_type, kode_warna, dan area
            foreach ($models as $mat) {
                $style_size = $mat['style_size'];
                $item_type = $mat['item_type'];
                $kode_warna = $mat['kode_warna'];
                $warna = $mat['color']; // warna
                $comp = floatval($mat['composition']);
                $gw = floatval($mat['gw']);
                $loss = floatval($mat['loss']);

                if (!isset($groupedOrders[$style_size])) {
                    continue;
                }

                foreach ($groupedOrders[$style_size] as $delivery => $areaData) {
                    foreach ($areaData as $area => $values) {
                        $qty = $values['qty'];
                        $sisa = $values['sisa'];

                        $jatah = ($qty * $comp * $gw / 100 / 1000) * (1 + ($loss / 100));
                        $sisaVal = ($sisa * $comp * $gw / 100 / 1000) * (1 + ($loss / 100));

                        if (!isset($result[$delivery][$item_type][$kode_warna])) {
                            $result[$delivery][$item_type][$kode_warna] = [];
                            foreach ($areas as $a) {
                                $result[$delivery][$item_type][$kode_warna][$a] = ['jatah' => 0, 'sisa' => 0];
                            }
                            $result[$delivery][$item_type][$kode_warna]['Grand Total Jatah'] = 0;
                            $result[$delivery][$item_type][$kode_warna]['Grand Total Sisa'] = 0;
                        }

                        $result[$delivery][$item_type][$kode_warna][$area]['jatah'] += $jatah;
                        $result[$delivery][$item_type][$kode_warna][$area]['sisa'] += $sisaVal;

                        // Total hanya berdasarkan jatah (tanpa sisa), tapi bisa ditambah sisa jika perlu
                        $result[$delivery][$item_type][$kode_warna]['Grand Total Jatah'] += $jatah;
                        $result[$delivery][$item_type][$kode_warna]['Grand Total Sisa'] += $sisaVal;
                    }
                }
            }

            // Akumulasi total semua delivery
            foreach ($result as $delivery => $itemTypes) {
                foreach ($itemTypes as $item_type => $colors) {
                    foreach ($colors as $kode_warna => $areaData) {
                        if (!isset($totalAllDelivery[$item_type][$kode_warna])) {
                            foreach ($areas as $a) {
                                $totalAllDelivery[$item_type][$kode_warna][$a] = ['jatah' => 0, 'sisa' => 0];
                            }
                            $totalAllDelivery[$item_type][$kode_warna]['Grand Total Jatah'] = 0;
                            $totalAllDelivery[$item_type][$kode_warna]['Grand Total Sisa'] = 0;
                        }

                        foreach ($areas as $area) {
                            $totalAllDelivery[$item_type][$kode_warna][$area]['jatah'] += $areaData[$area]['jatah'] ?? 0;
                            $totalAllDelivery[$item_type][$kode_warna][$area]['sisa'] += $areaData[$area]['sisa'] ?? 0;
                        }

                        $totalAllDelivery[$item_type][$kode_warna]['Grand Total Jatah'] += $areaData['Grand Total Jatah'] ?? 0;
                        $totalAllDelivery[$item_type][$kode_warna]['Grand Total Sisa'] += $areaData['Grand Total Sisa'] ?? 0;
                    }
                }
            }
            //
            $totalPo = $this->ApsPerstyleModel->totalPo($noModel)['totalPo'] ?? 0;
        }

        // Buat file Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $styleTitle = [
            'font' => [
                'bold' => true, // Tebalkan teks
                'color' => ['argb' => 'FF000000'],
                'size' => 15
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER, // Alignment rata tengah
            ],
        ];

        // border
        $styleHeader = [
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FF000000'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
            // 'fill' => [
            //     'fillType' => Fill::FILL_SOLID,
            //     'startColor' => ['argb' => 'FF67748E'],
            // ],
        ];
        $styleBody = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000']
                ]
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
                'horizontal' => Alignment::HORIZONTAL_CENTER
            ]
        ];

        //TABEL PEMBAGIAN QTY
        $totalCols = 2 + 2 + (count($areas) * 2) + 1;
        $lastCol = Coordinate::stringFromColumnIndex($totalCols);

        $sheet->setCellValue('A1', 'RINCIAN PEMBAGIAN QTY PDK ' . $noModel);
        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray($styleHeader);

        // Tulis header statis
        $sheet->setCellValue('A2', 'STYLE');
        $sheet->mergeCells('A2:A3');
        $sheet->setCellValue('B2', 'NEEDLE');
        $sheet->mergeCells('B2:B3');
        $sheet->setCellValue('C2', 'QTY PO');
        $sheet->mergeCells('C2:D2');
        $sheet->setCellValue('C3', 'QTY (Pcs)');
        $sheet->setCellValue('D3', 'QTY (Dz)');

        // Header area dinamis
        $colIndex = 5; // Kolom E
        foreach ($areas as $area) {
            $col1 = Coordinate::stringFromColumnIndex($colIndex);
            $col2 = Coordinate::stringFromColumnIndex($colIndex + 1);

            // Merge area di baris ke-2
            $sheet->mergeCells("{$col1}2:{$col2}2");
            $sheet->setCellValue("{$col1}2", 'QTY ' . strtoupper($area));
            $sheet->setCellValue("{$col1}3", 'QTY (Pcs)');
            $sheet->setCellValue("{$col2}3", 'QTY (Dz)');

            $colIndex += 2;
        }
        $colColor = Coordinate::stringFromColumnIndex($colIndex);
        $sheet->setCellValue("{$colColor}2", 'COLOR');
        $sheet->mergeCells("{$colColor}2:{$colColor}3");
        // $sheet->getColumnDimension($colColor)->setAutoSize(true);

        // ✅ APPLY $styleHeader ke seluruh header dari A2 sampai kolom terakhir
        $lastHeaderCol = $colColor;
        $sheet->getStyle("A2:{$lastHeaderCol}3")->applyFromArray($styleHeader);

        // Tulis data mulai dari baris 4
        $row = 4;
        foreach ($grouped as $data) {
            $sheet->setCellValue("A{$row}", $data['size']);
            $sheet->setCellValue("B{$row}", $data['machinetypeid']);
            $sheet->setCellValue("C{$row}", $data['total_qty']);
            $sheet->setCellValue("D{$row}", number_format($data['total_qty'] / 24, 2));

            $colIndex = 5;
            foreach ($areas as $area) {
                $pcsCol = Coordinate::stringFromColumnIndex($colIndex);
                $dzCol  = Coordinate::stringFromColumnIndex($colIndex + 1);

                $qtyArea = $data['areas'][$area] ?? 0;

                $sheet->setCellValue("{$pcsCol}{$row}", $qtyArea);
                $sheet->setCellValue("{$dzCol}{$row}", number_format($qtyArea / 24, 2));

                $colIndex += 2;
            }

            $sheet->setCellValue("{$colColor}{$row}", $data['color']);
            $row++;
        }

        $dataStartRow = 4;
        $dataEndRow = $row - 1; // Karena $row sudah ditambah 1 setelah loop
        $sheet->getStyle("A{$dataStartRow}:{$lastCol}{$dataEndRow}")->applyFromArray($styleBody);

        // Tambah baris total
        $sheet->setCellValue("A{$row}", 'TOTAL');
        $sheet->mergeCells("A{$row}:B{$row}");
        $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray($styleHeader);

        // Kolom C dan D (QTY PO)
        $sheet->setCellValue("C{$row}", "=SUM(C{$dataStartRow}:C{$dataEndRow})");
        $sheet->setCellValue("D{$row}", "=SUM(D{$dataStartRow}:D{$dataEndRow})");

        // QTY area
        $colIndex = 5;
        foreach ($areas as $area) {
            $pcsCol = Coordinate::stringFromColumnIndex($colIndex);
            $dzCol  = Coordinate::stringFromColumnIndex($colIndex + 1);

            $sheet->setCellValue("{$pcsCol}{$row}", "=SUM({$pcsCol}{$dataStartRow}:{$pcsCol}{$dataEndRow})");
            $sheet->setCellValue("{$dzCol}{$row}", "=SUM({$dzCol}{$dataStartRow}:{$dzCol}{$dataEndRow})");

            $colIndex += 2;
        }

        // Set lebar kolom agar menyesuaikan isi
        for ($i = 1; $i <= $totalCols; $i++) {
            $colLetter = Coordinate::stringFromColumnIndex($i);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }

        // TABEL PEMBAGIAN BAHAN BAKU
        $row = $row + 2;
        $totalCols = 4 + count($areas);
        $lastCol = Coordinate::stringFromColumnIndex($totalCols);
        $sheet->setCellValue("A{$row}", 'PEMBAGIAN KEBUTUHAN BAHAN BAKU');
        $sheet->mergeCells("A{$row}:{$lastCol}{$row}");
        $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray($styleHeader);

        // Header baris ke-1 dan ke-2
        $row++;
        $headerRow1 = $row;
        $headerRow2 = $row + 1;

        // Tulis header statis
        $sheet->setCellValue("A{$headerRow1}", 'Color');
        $sheet->mergeCells("A{$headerRow1}:A{$headerRow2}");
        $sheet->setCellValue("B{$headerRow1}", 'Item Type');
        $sheet->mergeCells("B{$headerRow1}:B{$headerRow2}");
        $sheet->setCellValue("C{$headerRow1}", 'Kode Warna');
        $sheet->mergeCells("C{$headerRow1}:C{$headerRow2}");
        $sheet->setCellValue("D{$headerRow1}", 'Pesanan');
        $sheet->setCellValue("D{$headerRow2}", 'Kgs');

        // Header area dinamis (mulai dari kolom E)
        $colIndex = 5;
        foreach ($areas as $area) {
            $col = Coordinate::stringFromColumnIndex($colIndex);
            $sheet->setCellValue("{$col}{$headerRow1}", 'Keb ' . strtoupper($area));
            $sheet->setCellValue("{$col}{$headerRow2}", 'Kgs');
            $colIndex++;
        }

        // Grand Total Jatah & Sisa
        $lastCol = Coordinate::stringFromColumnIndex($colIndex - 1);

        // Apply style ke header keseluruhan
        $sheet->getStyle("A{$headerRow1}:{$lastCol}{$headerRow2}")->applyFromArray($styleHeader);

        $warnaMap = [];
        foreach ($models ?? [] as $m) {
            if (isset($m['kode_warna']) && isset($m['color'])) {
                $warnaMap[$m['kode_warna']] = $m['color'];
            }
        }

        // Data Rows
        $row = $headerRow2 + 1;
        foreach ($totalAllDelivery as $item_type => $colors) {
            foreach ($colors as $kode_warna => $areaData) {
                $sheet->setCellValue("A{$row}", $warnaMap[$kode_warna] ?? '-');
                $sheet->setCellValue("B{$row}", $item_type);
                $sheet->setCellValue("C{$row}", $kode_warna);
                $sheet->setCellValue("D{$row}", number_format($areaData['Grand Total Jatah'] ?? 0, 2));

                $colIndex = 5;
                foreach ($areas as $area) {
                    $jatah = $areaData[$area]['jatah'] ?? 0;
                    $col = Coordinate::stringFromColumnIndex($colIndex);
                    $sheet->setCellValue("{$col}{$row}", number_format($jatah, 2));
                    $colIndex++;
                }
                $row++;
            }
        }

        // Apply style ke body
        $dataStartRow = $headerRow2 + 1;
        $dataEndRow = $row - 1;
        $sheet->getStyle("A{$dataStartRow}:{$lastCol}{$dataEndRow}")->applyFromArray($styleBody);

        // Buat writer dan output file Excel
        $writer = new Xlsx($spreadsheet);
        $fileName = 'Pembagian Jatah Area ' . $noModel . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    // public function generateFormRetur($area)
    // {
    //     $noModel = $this->request->getGet('model');
    //     $tglBuat = $this->request->getGet('tglBuat');

    //     // Ambil data berdasarkan area dan model
    //     $apiUrl = api_url('material') . "listExportRetur/"
    //         . $area
    //         . "?noModel=" . urlencode($noModel)
    //         . "&tglBuat=" . urlencode($tglBuat);

    //     $ch = curl_init($apiUrl);
    //     curl_setopt_array($ch, [
    //         CURLOPT_RETURNTRANSFER => true,
    //         CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
    //     ]);

    //     $response = curl_exec($ch);
    //     $error    = curl_error($ch);
    //     $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    //     curl_close($ch);
    //     if ($response === false) {
    //         return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Curl error: ' . $error]);
    //     }

    //     $result = json_decode($response, true);
    //     // dd($result);
    //     if (!is_array($result)) {
    //         return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Data tidak valid dari API']);
    //     }

    //     $dataRetur = $result['dataRetur'];
    //     $material = $result['material'];

    //     // Buat index material per style
    //     $materialIndex = [];
    //     foreach ($material as $item) {
    //         $key = $item['no_model'] . '|' . $item['item_type'] . '|' . $item['kode_warna'] . '|' . $item['style_size'];
    //         $materialIndex[$key] = $item;
    //     }

    //     // log_message('info', 'Material Index: ' . print_r($materialIndex, true));

    //     // Ambil qty per style dari material
    //     $qtyOrderList = [];
    //     $kgPoList = [];
    //     foreach ($material as $item) {
    //         $keyStyle = $item['no_model'] . '|' . $item['item_type'] . '|' . $item['kode_warna'] . '|' . $item['style_size'];

    //         $qty = $this->ApsPerstyleModel->getSisaPerSize($area, $item['no_model'], [$item['style_size']]);
    //         $qty_order = is_array($qty) ? ($qty['qty'] ?? 0) : ($qty->qty ?? 0);
    //         $qtyOrderList[$keyStyle] = $qty_order;

    //         $composition = (float)$item['composition'] ?? 0;
    //         $gw = (float)$item['gw'] ?? 0;
    //         $loss = (float)$item['loss'] ?? 0;

    //         // Hitung kg_po
    //         $kgPoList[$keyStyle] = ($qty_order * $composition * $gw / 100 / 1000) * (1 + ($loss / 100));
    //     }

    //     // Group per no_model|item_type|kode_warna
    //     $grouped = [];
    //     foreach ($dataRetur as $row) {
    //         $keyGroup = $row['no_model'] . '|' . $row['item_type'] . '|' . $row['kode_warna'];

    //         // Cari semua style yang match group ini
    //         $matchingKeys = [];
    //         foreach ($materialIndex as $k => $m) {
    //             if (
    //                 $m['no_model'] === $row['no_model']
    //                 && $m['item_type'] === $row['item_type']
    //                 && $m['kode_warna'] === $row['kode_warna']
    //             ) {
    //                 $matchingKeys[] = $k;
    //             }
    //         }

    //         // Sum qty_order dan kg_po untuk group ini
    //         $qty_order_sum = 0;
    //         $kg_po_sum = 0;
    //         $loss_value = 0;
    //         foreach ($matchingKeys as $index => $k) {
    //             $qty_order_sum += $qtyOrderList[$k] ?? 0;
    //             $kg_po_sum += $kgPoList[$k] ?? 0;
    //             if ($index === 0) {
    //                 $loss_value = (float)($materialIndex[$k]['loss'] ?? 0);
    //             }
    //         }

    //         // Masukkan ke $row
    //         $row['qty_order'] = $qty_order_sum;
    //         $row['kg_po'] = $kg_po_sum;
    //         $row['loss'] = $loss_value;

    //         // Simpan di array final
    //         $grouped[$keyGroup] = $row;
    //     }

    //     // Gunakan $dataReturGrouped untuk loop di Excel
    //     $dataReturGrouped = array_values($grouped);

    //     $delivery = $dataReturGrouped[0]['delivery_akhir'] ?? '';
    //     // Buat Excel
    //     $spreadsheet = new Spreadsheet();
    //     $sheet = $spreadsheet->getActiveSheet();
    //     $firstSheet = true;

    //     if ($firstSheet) {
    //         $sheet = $spreadsheet->getActiveSheet();
    //         $firstSheet = false;
    //     } else {
    //         $sheet = $spreadsheet->createSheet();
    //     }
    //     $sheet->setTitle('Form Retur');
    //     // Style untuk header tabel (border, center, bold)
    //     $styleHeader = [
    //         'font' => [
    //             'bold' => false,
    //             'size' => 10, // <--- Ukuran font diatur di sini
    //         ],
    //         'borders' => [
    //             'allBorders' => [
    //                 'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
    //             ],
    //         ],
    //         'alignment' => [
    //             'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
    //             'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
    //             'wrapText' => true,
    //         ],
    //     ];

    //     $styleBody = [
    //         'font' => [
    //             'size' => 10, // Ukuran font isi tabel
    //         ],
    //         'alignment' => [
    //             'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
    //             'wrapText' => true,
    //         ],
    //         'borders' => [
    //             'allBorders' => [
    //                 'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
    //             ],
    //         ],
    //     ];

    //     $spreadsheet->getDefaultStyle()->getFont()->setName('Arial');
    //     // $spreadsheet->getDefaultStyle()->getFont()->setSize(16);

    //     // 1. Atur ukuran kertas jadi A4
    //     $sheet->getPageSetup()
    //         ->setPaperSize(PageSetup::PAPERSIZE_A4);

    //     // 2. Atur orientasi jadi landscape
    //     $sheet->getPageSetup()
    //         ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
    //     // 3. (Opsional) Atur scaling, agar muat ke 1 halaman
    //     $sheet->getPageSetup()
    //         ->setFitToWidth(1)
    //         ->setFitToHeight(0)    // 0 artinya auto height
    //         ->setFitToPage(true); // aktifkan fitting

    //     // 4. (Opsional) Atur margin supaya tidak terlalu sempit
    //     $sheet->getPageMargins()->setTop(0.4)
    //         ->setBottom(0.4)
    //         ->setLeft(0.4)
    //         ->setRight(0.2);
    //     //Outline Border
    //     // 1. Top double border dari A1 ke Q1
    //     $sheet->getStyle('A1:AD1')->applyFromArray([
    //         'borders' => [
    //             'top' => [
    //                 'borderStyle' => Border::BORDER_DOUBLE,
    //                 'color' => ['rgb' => '000000'],
    //             ],
    //         ],
    //     ]);

    //     // 2. Right double border dari Q1 ke Q50
    //     $sheet->getStyle('Q1:AD33')->applyFromArray([
    //         'borders' => [
    //             'right' => [
    //                 'borderStyle' => Border::BORDER_DOUBLE,
    //                 'color' => ['rgb' => '000000'],
    //             ],
    //         ],
    //     ]);

    //     // 3. Bottom double border dari A50 ke Q50
    //     $sheet->getStyle('A33:AD33')->applyFromArray([
    //         'borders' => [
    //             'bottom' => [
    //                 'borderStyle' => Border::BORDER_DOUBLE,
    //                 'color' => ['rgb' => '000000'],
    //             ],
    //         ],
    //     ]);

    //     // 4. Left double border dari A1 ke A50
    //     $sheet->getStyle('A1:A33')->applyFromArray([
    //         'borders' => [
    //             'left' => [
    //                 'borderStyle' => Border::BORDER_DOUBLE,
    //                 'color' => ['rgb' => '000000'],
    //             ],
    //         ],
    //     ]);

    //     //Border Thin
    //     $sheet->getStyle('D1:D3')->applyFromArray([
    //         'borders' => [
    //             'right' => [
    //                 'borderStyle' => Border::BORDER_THIN,
    //                 'color' => ['rgb' => '000000'],
    //             ],
    //         ],
    //     ]);
    //     $sheet->getStyle('D4')->applyFromArray([
    //         'borders' => [
    //             'right' => [
    //                 'borderStyle' => Border::BORDER_THIN,
    //                 'color' => ['rgb' => '000000'],
    //             ],
    //         ],
    //     ]);
    //     $sheet->getStyle('S4:Z4')->applyFromArray([
    //         'borders' => [
    //             'left' => [
    //                 'borderStyle' => Border::BORDER_THIN,
    //                 'color' => ['rgb' => '000000'],
    //             ],
    //             'right' => [
    //                 'borderStyle' => Border::BORDER_THIN,
    //                 'color' => ['rgb' => '000000'],
    //             ],
    //         ],
    //     ]);
    //     $sheet->getStyle('S5:S5')->applyFromArray([
    //         'borders' => [
    //             'left' => [
    //                 'borderStyle' => Border::BORDER_THIN,
    //                 'color' => ['rgb' => '000000'],
    //             ],
    //             'right' => [
    //                 'borderStyle' => Border::BORDER_THIN,
    //                 'color' => ['rgb' => '000000'],
    //             ],
    //         ],
    //     ]);

    //     // Double border baris 4 dan 5
    //     $sheet->getStyle('A4:AD4')->applyFromArray([
    //         'borders' => [
    //             'top' => [
    //                 'borderStyle' => Border::BORDER_DOUBLE,
    //                 'color' => ['rgb' => '000000'],
    //             ],
    //             'bottom' => [
    //                 'borderStyle' => Border::BORDER_DOUBLE,
    //                 'color' => ['rgb' => '000000'],
    //             ],
    //         ],
    //     ]);
    //     $sheet->getStyle('A5:AD5')->applyFromArray([
    //         'borders' => [
    //             'top' => [
    //                 'borderStyle' => Border::BORDER_DOUBLE,
    //                 'color' => ['rgb' => '000000'],
    //             ],
    //             'bottom' => [
    //                 'borderStyle' => Border::BORDER_DOUBLE,
    //                 'color' => ['rgb' => '000000'],
    //             ],
    //         ],
    //     ]);

    //     // Border kiri
    //     $sheet->getStyle('AA5:AD5')->applyFromArray([
    //         'borders' => [
    //             'left' => [
    //                 'borderStyle' => Border::BORDER_THIN,
    //                 'color'       => ['rgb' => '000000'],
    //             ],
    //         ],
    //     ]);

    //     $thinInside = [
    //         'borders' => [
    //             // border antar kolom (vertical lines) di dalam range
    //             'vertical' => [
    //                 'borderStyle' => Border::BORDER_THIN,
    //                 'color' => ['rgb' => '000000'],
    //             ],
    //             // border antar baris (horizontal lines) di dalam range
    //             'horizontal' => [
    //                 'borderStyle' => Border::BORDER_THIN,
    //                 'color' => ['rgb' => '000000'],
    //             ],
    //         ],
    //     ];

    //     $thinInside = [
    //         'borders' => [
    //             'vertical' => [
    //                 'borderStyle' => Border::BORDER_THIN,
    //                 'color' => ['rgb' => '000000'],
    //             ],
    //             'horizontal' => [
    //                 'borderStyle' => Border::BORDER_THIN,
    //                 'color' => ['rgb' => '000000'],
    //             ],
    //         ],
    //     ];
    //     $sheet->getStyle('A11:AD28')->applyFromArray($thinInside);

    //     // 2) Border tipis atas untuk baris header tabel (A11:Q11)
    //     $sheet->getStyle('A11:AD11')->applyFromArray([
    //         'borders' => [
    //             'top' => [
    //                 'borderStyle' => Border::BORDER_THIN,
    //                 'color' => ['rgb' => '000000'],
    //             ],
    //         ],
    //     ]);

    //     // 3) Border tipis bawah untuk baris total (A28:Q28)
    //     $sheet->getStyle('A28:AD28')->applyFromArray([
    //         'borders' => [
    //             'bottom' => [
    //                 'borderStyle' => Border::BORDER_THIN,
    //                 'color' => ['rgb' => '000000'],
    //             ],
    //         ],
    //     ]);

    //     // Aktifkan wrap text di A11:Q28
    //     $sheet->getStyle('A11:AD28')->getAlignment()->setWrapText(true);

    //     // Lebar kolom (dalam pt) dan tinggi baris (dalam pt)
    //     $columnWidths = [
    //         'A' => 20,
    //         'B' => 20,
    //         'C' => 40,
    //         'D' => 50,
    //         'E' => 50,
    //         'F' => 50,
    //         'G' => 50,
    //         'H' => 20,
    //         'I' => 20,
    //         'J' => 20,
    //         'K' => 40,
    //         'L' => 20,
    //         'M' => 20,
    //         'N' => 20,
    //         'O' => 40,
    //         'P' => 20,
    //         'Q' => 20,
    //         'R' => 30,
    //         'S' => 20,
    //         'T' => 20,
    //         'U' => 20,
    //         'V' => 30,
    //         'W' => 20,
    //         'X' => 20,
    //         'Y' => 20,
    //         'Z' => 20,
    //         'AA' => 30,
    //         'AB' => 20,
    //         'AC' => 30,
    //         'AD' => 40,
    //     ];

    //     $rowHeightsPt = array_fill_keys(range(11, 33), 36);
    //     $rowHeightsPt[11] = 50;
    //     $rowHeightsPt[12] = 50;

    //     // Atur tinggi baris
    //     foreach ($rowHeightsPt as $row => $height) {
    //         $sheet->getRowDimension($row)->setRowHeight($height);
    //     }

    //     // Atur lebar kolom
    //     foreach ($columnWidths as $col => $pt) {
    //         $sheet->getColumnDimension($col)->setWidth(round($pt / 5.25, 2));
    //     }

    //     // Header Form
    //     $sheet->mergeCells('A1:D2');
    //     $sheet->getRowDimension(1)->setRowHeight(30);

    //     $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
    //     $drawing->setName('Logo');
    //     $drawing->setDescription('Logo Perusahaan');
    //     $drawing->setPath('assets/img/logo-kahatex.png');
    //     $drawing->setCoordinates('B1');
    //     $drawing->setHeight(50);
    //     $drawing->setOffsetX(60);
    //     $drawing->setOffsetY(10);
    //     $drawing->setWorksheet($sheet);
    //     $sheet->mergeCells('A3:D3');
    //     $sheet->setCellValue('A3', 'PT. KAHATEX');
    //     $sheet->getStyle('A3')->getFont()->setSize(11)->setBold(true);
    //     $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    //     $sheet->setCellValue('E1', 'FORMULIR');
    //     $sheet->getStyle('E1')->getFont()->setBold(true)->setSize(16);
    //     $sheet->getStyle('E1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
    //     $sheet->getStyle('E1')->getFill()->getStartColor()->setRGB('99FFFF');
    //     $sheet->mergeCells('E1:AD1');
    //     $sheet->getStyle('E1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    //     $sheet->mergeCells('E2:AD2');
    //     $sheet->setCellValue('E2', 'DEPARTEMEN KAOS KAKI');
    //     $sheet->getStyle('E2')->getFont()->setBold(true)->setSize(12);
    //     $sheet->getStyle('E2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    //     $sheet->mergeCells('E3:AD3');
    //     $sheet->setCellValue('E3', 'PO TAMBAHAN DAN RETURAN BAHAN BAKU MESIN KE GUDANG BENANG');
    //     $sheet->getStyle('E3')->getFont()->setBold(true)->setSize(12);
    //     $sheet->getStyle('E3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    //     $sheet->mergeCells('A4:C4');
    //     $sheet->setCellValue('A4', 'No. Dokumen');
    //     $sheet->setCellValue('D4', 'FOR-KK-034/REV_05/HAL_1/1');

    //     $sheet->mergeCells('S4:Z4');
    //     $sheet->setCellValue('S4', 'Tanggal Revisi');
    //     $sheet->mergeCells('AA4:AD4');
    //     $sheet->setCellValue('AA4', '17 Maret 2025');
    //     $sheet->getStyle('AA4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    //     $sheet->mergeCells('S5:Z5');
    //     $sheet->setCellValue('S5', 'Klasifikasi');
    //     $sheet->mergeCells('AA5:AD5');
    //     $sheet->setCellValue('AA5', 'Internal');
    //     $sheet->getStyle('AA5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    //     $sheet->mergeCells('A5:R5');
    //     $sheet->getStyle('A4:AD5')->getFont()->setBold(true)->setSize(11);

    //     $sheet->setCellValue('A6', 'Area: ' . $area);
    //     $sheet->getStyle('A6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)
    //         ->setVertical(Alignment::VERTICAL_CENTER);
    //     $sheet->getStyle('A6')->getFont()->setBold(true)->setSize(10);

    //     $sheet->setCellValue('G6', 'Loss F.Up');
    //     $sheet->getStyle('G6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)
    //         ->setVertical(Alignment::VERTICAL_CENTER);
    //     $sheet->getStyle('G6')->getFont()->setBold(true)->setSize(10);

    //     $lossValue = isset($result[0]['loss']) ? $result[0]['loss'] . '%' : '';
    //     $sheet->setCellValue('I6', ': ' . $lossValue);
    //     $sheet->getStyle('I6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)
    //         ->setVertical(Alignment::VERTICAL_CENTER);
    //     $sheet->getStyle('I6')->getFont()->setBold(true)->setSize(10);

    //     $sheet->setCellValue('P6', 'Tanggal Buat');
    //     $sheet->getStyle('P6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)
    //         ->setVertical(Alignment::VERTICAL_CENTER);
    //     $sheet->getStyle('P6')->getFont()->setBold(true)->setSize(10);

    //     $sheet->setCellValue('S6', ': ' . $tglBuat);
    //     $sheet->getStyle('S6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)
    //         ->setVertical(Alignment::VERTICAL_CENTER);
    //     $sheet->getStyle('S6')->getFont()->setBold(true)->setSize(10);

    //     $sheet->setCellValue('P7', 'Tanggal Export');
    //     $sheet->getStyle('P7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)
    //         ->setVertical(Alignment::VERTICAL_CENTER);
    //     $sheet->getStyle('P7')->getFont()->setBold(true)->setSize(10);

    //     $sheet->setCellValue('S7', ': ' . $delivery);
    //     $sheet->getStyle('S7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)
    //         ->setVertical(Alignment::VERTICAL_CENTER);
    //     $sheet->getStyle('S7')->getFont()->setBold(true)->setSize(10);

    //     // Header utama dan sub-header
    //     $sheet->setCellValue('A8', 'Model');
    //     $sheet->mergeCells('A8:B10');
    //     $sheet->getStyle('A8:B10')->getAlignment()
    //         ->setHorizontal(Alignment::HORIZONTAL_CENTER)
    //         ->setVertical(Alignment::VERTICAL_CENTER);

    //     $sheet->setCellValue('C8', 'Warna');
    //     $sheet->mergeCells('C8:C10');
    //     $sheet->getStyle('C8:C10')->getAlignment()
    //         ->setHorizontal(Alignment::HORIZONTAL_CENTER)
    //         ->setVertical(Alignment::VERTICAL_CENTER);

    //     $sheet->setCellValue('D8', 'Item Type');
    //     $sheet->mergeCells('D8:D10');
    //     $sheet->getStyle('D8:D10')->getAlignment()
    //         ->setHorizontal(Alignment::HORIZONTAL_CENTER)
    //         ->setVertical(Alignment::VERTICAL_CENTER);

    //     $sheet->setCellValue('E8', 'Kode Warna');
    //     $sheet->mergeCells('E8:E10');
    //     $sheet->getStyle('E8:E10')->getAlignment()
    //         ->setHorizontal(Alignment::HORIZONTAL_CENTER)
    //         ->setVertical(Alignment::VERTICAL_CENTER);

    //     $sheet->setCellValue('F8', 'Style / Size');
    //     $sheet->mergeCells('F8:F10');
    //     $sheet->getStyle('F8:F10')->getAlignment()
    //         ->setHorizontal(Alignment::HORIZONTAL_CENTER)
    //         ->setVertical(Alignment::VERTICAL_CENTER);

    //     $sheet->setCellValue('G8', 'Komposisi ( % )');
    //     $sheet->mergeCells('G8:G10');
    //     $sheet->getStyle('G8:G10')->getAlignment()
    //         ->setHorizontal(Alignment::HORIZONTAL_CENTER)
    //         ->setVertical(Alignment::VERTICAL_CENTER);

    //     $sheet->setCellValue('H8', 'GW / Pcs');
    //     $sheet->mergeCells('H8:H10');
    //     $sheet->getStyle('H8:H10')->getAlignment()
    //         ->setHorizontal(Alignment::HORIZONTAL_CENTER)
    //         ->setVertical(Alignment::VERTICAL_CENTER);

    //     $sheet->setCellValue('I8', 'Qty / Pcs');
    //     $sheet->mergeCells('I8:I10');
    //     $sheet->getStyle('I8:I10')->getAlignment()
    //         ->setHorizontal(Alignment::HORIZONTAL_CENTER)
    //         ->setVertical(Alignment::VERTICAL_CENTER);

    //     $sheet->setCellValue('J8', 'Loss');
    //     $sheet->mergeCells('J8:J10');
    //     $sheet->getStyle('J8:J10')->getAlignment()
    //         ->setHorizontal(Alignment::HORIZONTAL_CENTER)
    //         ->setVertical(Alignment::VERTICAL_CENTER);

    //     $sheet->setCellValue('K8', 'Pesanan Kgs');
    //     $sheet->mergeCells('K8:K10');
    //     $sheet->getStyle('K8:K10')->getAlignment()
    //         ->setHorizontal(Alignment::HORIZONTAL_CENTER)
    //         ->setVertical(Alignment::VERTICAL_CENTER);

    //     $sheet->setCellValue('L8', 'Terima');
    //     $sheet->mergeCells('L8:N9');
    //     $sheet->getStyle('L8:N9')->getAlignment()
    //         ->setHorizontal(Alignment::HORIZONTAL_CENTER)
    //         ->setVertical(Alignment::VERTICAL_CENTER);

    //     $sheet->setCellValue('L10', 'Kg');
    //     $sheet->getStyle('L10')->getAlignment()
    //         ->setHorizontal(Alignment::HORIZONTAL_CENTER)
    //         ->setVertical(Alignment::VERTICAL_CENTER);

    //     $sheet->setCellValue('M10', '+ / -');
    //     $sheet->getStyle('M10')->getAlignment()
    //         ->setHorizontal(Alignment::HORIZONTAL_CENTER)
    //         ->setVertical(Alignment::VERTICAL_CENTER);

    //     $sheet->setCellValue('N10', '%');
    //     $sheet->getStyle('N10')->getAlignment()
    //         ->setHorizontal(Alignment::HORIZONTAL_CENTER)
    //         ->setVertical(Alignment::VERTICAL_CENTER);

    //     $sheet->setCellValue('O8', 'Sisa Benang di mesin');
    //     $sheet->mergeCells('O8:O9');
    //     $sheet->getStyle('O8:O9')->getAlignment()
    //         ->setHorizontal(Alignment::HORIZONTAL_CENTER)
    //         ->setVertical(Alignment::VERTICAL_CENTER);

    //     $sheet->setCellValue('O10', 'Kg');
    //     $sheet->getStyle('O10')->getAlignment()
    //         ->setHorizontal(Alignment::HORIZONTAL_CENTER)
    //         ->setVertical(Alignment::VERTICAL_CENTER);

    //     $sheet->setCellValue('P8', 'Tambahan I (mesin)');
    //     $sheet->mergeCells('P8:S8');
    //     $sheet->getStyle('P8:S8')->getAlignment()
    //         ->setHorizontal(Alignment::HORIZONTAL_CENTER)
    //         ->setVertical(Alignment::VERTICAL_CENTER);

    //     $sheet->setCellValue('P9', 'Pcs');
    //     $sheet->mergeCells('P9:P10');
    //     $sheet->getStyle('P9:P10')->getAlignment()
    //         ->setHorizontal(Alignment::HORIZONTAL_CENTER)
    //         ->setVertical(Alignment::VERTICAL_CENTER);

    //     $sheet->setCellValue('Q9', 'Benang');
    //     $sheet->mergeCells('Q9:R9');
    //     $sheet->getStyle('Q9:R9')->getAlignment()
    //         ->setHorizontal(Alignment::HORIZONTAL_CENTER)
    //         ->setVertical(Alignment::VERTICAL_CENTER);

    //     $sheet->setCellValue('Q10', 'Kg');
    //     $sheet->getStyle('Q10')->getAlignment()
    //         ->setHorizontal(Alignment::HORIZONTAL_CENTER)
    //         ->setVertical(Alignment::VERTICAL_CENTER);

    //     $sheet->setCellValue('R10', 'Cones');
    //     $sheet->getStyle('R10')->getAlignment()
    //         ->setHorizontal(Alignment::HORIZONTAL_CENTER)
    //         ->setVertical(Alignment::VERTICAL_CENTER);

    //     $sheet->setCellValue('S9', '%');
    //     $sheet->mergeCells('S9:S10');
    //     $sheet->getStyle('S9:S10')->getAlignment()
    //         ->setHorizontal(Alignment::HORIZONTAL_CENTER)
    //         ->setVertical(Alignment::VERTICAL_CENTER);

    //     $sheet->setCellValue('T8', 'Tambahan II (Packing)');
    //     $sheet->mergeCells('T8:W8');
    //     $sheet->getStyle('T8:W8')->getAlignment()
    //         ->setHorizontal(Alignment::HORIZONTAL_CENTER)
    //         ->setVertical(Alignment::VERTICAL_CENTER);

    //     $sheet->setCellValue('T9', 'Pcs');
    //     $sheet->mergeCells('T9:T10');
    //     $sheet->getStyle('T9:T10')->getAlignment()
    //         ->setHorizontal(Alignment::HORIZONTAL_CENTER)
    //         ->setVertical(Alignment::VERTICAL_CENTER);

    //     $sheet->setCellValue('U9', 'Benang');
    //     $sheet->mergeCells('U9:V9');
    //     $sheet->getStyle('U9:V9')->getAlignment()
    //         ->setHorizontal(Alignment::HORIZONTAL_CENTER)
    //         ->setVertical(Alignment::VERTICAL_CENTER);

    //     $sheet->setCellValue('U10', 'Kg');
    //     $sheet->getStyle('U10')->getAlignment()
    //         ->setHorizontal(Alignment::HORIZONTAL_CENTER)
    //         ->setVertical(Alignment::VERTICAL_CENTER);

    //     $sheet->setCellValue('V10', 'Cones');
    //     $sheet->getStyle('V10')->getAlignment()
    //         ->setHorizontal(Alignment::HORIZONTAL_CENTER)
    //         ->setVertical(Alignment::VERTICAL_CENTER);

    //     $sheet->setCellValue('W9', '%');
    //     $sheet->mergeCells('W9:W10');
    //     $sheet->getStyle('W9:W10')->getAlignment()
    //         ->setHorizontal(Alignment::HORIZONTAL_CENTER)
    //         ->setVertical(Alignment::VERTICAL_CENTER);

    //     $sheet->setCellValue('X8', 'Total lebih pakai benang');
    //     $sheet->mergeCells('X8:Y9');
    //     $sheet->getStyle('X8:Y9')->getAlignment()
    //         ->setHorizontal(Alignment::HORIZONTAL_CENTER)
    //         ->setVertical(Alignment::VERTICAL_CENTER);

    //     $sheet->setCellValue('X10', 'Kg');
    //     $sheet->getStyle('X10')->getAlignment()
    //         ->setHorizontal(Alignment::HORIZONTAL_CENTER)
    //         ->setVertical(Alignment::VERTICAL_CENTER);

    //     $sheet->setCellValue('Y10', '%');
    //     $sheet->getStyle('Y10')->getAlignment()
    //         ->setHorizontal(Alignment::HORIZONTAL_CENTER)
    //         ->setVertical(Alignment::VERTICAL_CENTER);

    //     $sheet->setCellValue('Z8', 'RETURAN');
    //     $sheet->mergeCells('Z8:AC8');
    //     $sheet->getStyle('Z8:AC8')->getAlignment()
    //         ->setHorizontal(Alignment::HORIZONTAL_CENTER)
    //         ->setVertical(Alignment::VERTICAL_CENTER);

    //     $sheet->setCellValue('Z9', 'Kg');
    //     $sheet->mergeCells('Z9:Z10');
    //     $sheet->getStyle('Z9:Z10')->getAlignment()
    //         ->setHorizontal(Alignment::HORIZONTAL_CENTER)
    //         ->setVertical(Alignment::VERTICAL_CENTER);

    //     $sheet->setCellValue('AA9', '% dari PSN');
    //     $sheet->mergeCells('AA9:AA10');
    //     $sheet->getStyle('AA9:AA10')->getAlignment()
    //         ->setHorizontal(Alignment::HORIZONTAL_CENTER)
    //         ->setVertical(Alignment::VERTICAL_CENTER);

    //     $sheet->setCellValue('AB9', 'Kg');
    //     $sheet->mergeCells('AB9:AB10');
    //     $sheet->getStyle('AB9:AB10')->getAlignment()
    //         ->setHorizontal(Alignment::HORIZONTAL_CENTER)
    //         ->setVertical(Alignment::VERTICAL_CENTER);

    //     $sheet->setCellValue('AC9', '% dari PO(+)');
    //     $sheet->mergeCells('AC9:AC10');
    //     $sheet->getStyle('AC9:AC10')->getAlignment()
    //         ->setHorizontal(Alignment::HORIZONTAL_CENTER)
    //         ->setVertical(Alignment::VERTICAL_CENTER);

    //     $sheet->setCellValue('AD8', 'Keterangan');
    //     $sheet->mergeCells('AD8:AD10');
    //     $sheet->getStyle('AD8:AD10')->getAlignment()
    //         ->setHorizontal(Alignment::HORIZONTAL_CENTER)
    //         ->setVertical(Alignment::VERTICAL_CENTER);

    //     // Terapkan style ke seluruh area header (baris 8–10)
    //     $sheet->getStyle('A8:AD10')->applyFromArray($styleHeader);
    //     // Border kiri double untuk kolom A pada header
    //     $sheet->getStyle('A8:A10')->applyFromArray([
    //         'borders' => [
    //             'left' => [
    //                 'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE,
    //                 'color' => ['rgb' => '000000'],
    //             ],
    //         ],
    //     ]);

    //     // Border kanan double untuk kolom AD pada header
    //     $sheet->getStyle('AD8:AD10')->applyFromArray([
    //         'borders' => [
    //             'right' => [
    //                 'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE,
    //                 'color' => ['rgb' => '000000'],
    //             ],
    //         ],
    //     ]);

    //     // Isi tabel
    //     $rowNum = 11;
    //     $no = 1;
    //     $firstRow = true;
    //     $sheet->getRowDimension($rowNum)->setRowHeight(18);

    //     foreach ($dataReturGrouped as $row) {
    //         $sheet->mergeCells("A{$rowNum}:B{$rowNum}");
    //         $retur_kg_psn = '';
    //         $retur_kg_po = '';
    //         $retur_persen_psn = '';
    //         $retur_persen_po = '';

    //         $kgs = (float)$row['kg_po'];
    //         $retur = (float)$row['kgs_retur'];
    //         $poplus_mc_kg = (float)$row['poplus_mc_kg'];
    //         $plus_pck_kg = (float)$row['plus_pck_kg'];

    //         // Cek logika penempatan
    //         if ($poplus_mc_kg == 0 && $plus_pck_kg == 0) {
    //             $retur_kg_psn = number_format($retur, 2);
    //             if ($kgs != 0) {
    //                 $retur_persen_psn = number_format(($retur / $kgs) * 100, 2) . '%';
    //             }
    //         } else {
    //             $retur_kg_po = number_format($retur, 2);
    //             $totalPO = $poplus_mc_kg + $plus_pck_kg;
    //             if ($totalPO != 0) {
    //                 $retur_persen_po = number_format(($retur / $totalPO) * 100, 2) . '%';
    //             }
    //         }

    //         $sheet->fromArray([
    //             $row['no_model'] ?? '',
    //             '',
    //             $row['color'],
    //             $row['item_type'],
    //             $row['kode_warna'],
    //             '',
    //             $row['composition'] ?? '',       // G
    //             $row['gw'] ?? '',                // H
    //             $row['qty_order'] ?? 0,          // I -> Qty / Pcs
    //             $row['loss'] ?? '',              // J
    //             $row['kg_po'] ?? 0,
    //             number_format($row['terima_kg'], 2),
    //             number_format($row['terima_kg'] - $row['kg_po'], 2),
    //             number_format($row['terima_kg'] / $row['kg_po'], 2) * 100 . '%', // terima
    //             number_format($row['sisa_bb_mc'], 2), // sisa mesin
    //             $row['sisa_order_pcs'] == 0 ? '' : $row['sisa_order_pcs'],
    //             $row['poplus_mc_kg'] == 0 ? '' : number_format($row['poplus_mc_kg'], 2),
    //             $row['poplus_mc_cns'] == 0 ? '' : $row['poplus_mc_cns'],
    //             ($row['poplus_mc_kg'] / $row['kg_po']) == 0 ? '' : number_format($row['poplus_mc_kg'] / $row['kg_po'], 2) * 100 . '%',
    //             $row['plus_pck_pcs'] == 0 ? '' : number_format($row['plus_pck_pcs'], 2),
    //             $row['plus_pck_kg'] == 0 ? '' : number_format($row['plus_pck_kg'], 2),
    //             $row['plus_pck_cns'] == 0 ? '' : $row['plus_pck_cns'],
    //             ($row['plus_pck_kg'] / $row['kg_po']) == 0 ? '' : number_format($row['plus_pck_kg'] / $row['kg_po'], 2) * 100 . '%',
    //             $row['ttl_tambahan_kg'] == 0 ? '' : number_format($row['ttl_tambahan_kg'], 2),
    //             ($row['ttl_tambahan_kg'] / $row['kg_po']) == 0 ? '' : number_format($row['ttl_tambahan_kg'] / $row['kg_po'], 2) * 100 . '%',
    //             $retur_kg_psn,        // Z
    //             $retur_persen_psn,    // AA
    //             $retur_kg_po,         // AB
    //             $retur_persen_po,
    //             $row['kategori'],
    //         ], null, 'A' . $rowNum);

    //         $sheet->getRowDimension($rowNum)->setRowHeight(-1);
    //         $rowNum++;
    //     }

    //     $lastRow = $rowNum - 1;

    //     // Rata tengah semua isi tabel
    //     $sheet->getStyle("A11:AD{$lastRow}")->getAlignment()
    //         ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
    //         ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
    //     // Atur font: bold + size 10
    //     $sheet->getStyle("A11:AD{$lastRow}")->getFont()
    //         ->setSize(10);

    //     // Tambahkan border kiri double untuk kolom A
    //     $sheet->getStyle("A11:A{$lastRow}")->applyFromArray([
    //         'borders' => [
    //             'left' => [
    //                 'borderStyle' => Border::BORDER_DOUBLE,
    //                 'color' => ['rgb' => '000000'],
    //             ],
    //         ],
    //     ]);

    //     // Tambahkan border kanan double untuk kolom AD
    //     $sheet->getStyle("AD11:AD{$lastRow}")->applyFromArray([
    //         'borders' => [
    //             'right' => [
    //                 'borderStyle' => Border::BORDER_DOUBLE,
    //                 'color' => ['rgb' => '000000'],
    //             ],
    //         ],
    //     ]);

    //     // Atur baris kosong setelah data sampai baris 28
    //     for ($i = $rowNum; $i <= 28; $i++) {
    //         $sheet->mergeCells("A{$i}:B{$i}");
    //         $sheet->getRowDimension($i)->setRowHeight(18); // Tetapkan tinggi tetap
    //     }

    //     //Tanda Tangan
    //     $sheet->mergeCells('A30:D30');
    //     $sheet->setCellValue('A30', 'MANAJEMEN NS');
    //     $sheet->getStyle('A30:D30')->getAlignment()
    //         ->setHorizontal(Alignment::HORIZONTAL_CENTER);
    //     $sheet->mergeCells('E30:F30');
    //     $sheet->setCellValue('E30', 'KEPALA AREA');
    //     $sheet->getStyle('E30:F30')->getAlignment()
    //         ->setHorizontal(Alignment::HORIZONTAL_CENTER);
    //     $sheet->mergeCells('G30:H30');
    //     $sheet->setCellValue('G30', 'IE TEKNISI');
    //     $sheet->getStyle('G30:H30')->getAlignment()
    //         ->setHorizontal(Alignment::HORIZONTAL_CENTER);
    //     $sheet->mergeCells('I30:K30');
    //     $sheet->setCellValue('I30', 'PIC PACKING');
    //     $sheet->getStyle('I30:K30')->getAlignment()
    //         ->setHorizontal(Alignment::HORIZONTAL_CENTER);
    //     $sheet->mergeCells('L30:N30');
    //     $sheet->setCellValue('L30', 'PPC');
    //     $sheet->getStyle('L30:N30')->getAlignment()
    //         ->setHorizontal(Alignment::HORIZONTAL_CENTER);
    //     $sheet->mergeCells('O30:Q30');
    //     $sheet->setCellValue('O30', 'PPC');
    //     $sheet->getStyle('O30:Q30')->getAlignment()
    //         ->setHorizontal(Alignment::HORIZONTAL_CENTER);
    //     $sheet->mergeCells('R30:T30');
    //     $sheet->setCellValue('R30', 'PPC');
    //     $sheet->getStyle('R30:T30')->getAlignment()
    //         ->setHorizontal(Alignment::HORIZONTAL_CENTER);
    //     $sheet->mergeCells('U30:W30');
    //     $sheet->setCellValue('U30', 'GD BENANG');
    //     $sheet->getStyle('U30:W30')->getAlignment()
    //         ->setHorizontal(Alignment::HORIZONTAL_CENTER);
    //     $sheet->mergeCells('X30:AA30');
    //     $sheet->setCellValue('X30', 'MENGETAHUI');
    //     $sheet->getStyle('X30:AA30')->getAlignment()
    //         ->setHorizontal(Alignment::HORIZONTAL_CENTER);
    //     $sheet->mergeCells('AB30:AD30');
    //     $sheet->setCellValue('AB30', 'MENGETAHUI');
    //     $sheet->getStyle('AB30:AD30')->getAlignment()
    //         ->setHorizontal(Alignment::HORIZONTAL_CENTER);

    //     for ($i = 29; $i <= 33; $i++) {
    //         $sheet->getRowDimension($i)->setRowHeight(18);
    //     }

    //     $sheet->getStyle("A11:AD28")->getAlignment()
    //         ->setHorizontal(Alignment::HORIZONTAL_CENTER)
    //         ->setVertical(Alignment::VERTICAL_CENTER);

    //     // Output Excel
    //     $filename = 'FORM RETUR ' . $area . '.xlsx';
    //     $writer = new Xlsx($spreadsheet);
    //     header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    //     header("Content-Disposition: attachment; filename=\"$filename\"");
    //     $writer->save('php://output');
    //     exit;
    // }

    // VERSI YG STYLE NYA DITAMPILIN
    public function generateFormRetur($area)
    {
        $noModel = $this->request->getGet('model');
        $tglBuat = $this->request->getGet('tglBuat');

        // Ambil data berdasarkan area dan model
        $apiUrl = api_url('material') . "listExportRetur/"
            . $area
            . "?noModel=" . urlencode($noModel)
            . "&tglBuat=" . urlencode($tglBuat);

        $ch = curl_init($apiUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
        ]);

        $response = curl_exec($ch);
        $error    = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($response === false) {
            return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Curl error: ' . $error]);
        }

        $result = json_decode($response, true);
        // dd($result);
        if (!is_array($result)) {
            return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Data tidak valid dari API']);
        }

        $dataRetur = $result['dataRetur'];
        $dataPoTambahan = $result['dataPoTambahan'];
        $material = $result['material'];

        // Buat index material per style
        $materialIndex = [];
        foreach ($material as $item) {
            $key = $item['no_model'] . '|' . $item['item_type'] . '|' . $item['kode_warna'] . '|' . $item['style_size'];
            $materialIndex[$key] = $item;
        }

        // Buat index $dataPoTambahan per key style
        $poTambahanIndex = [];
        foreach ($dataPoTambahan as $item) {
            $key = $item['no_model'] . '|' . $item['item_type'] . '|' . $item['kode_warna'] . '|' . $item['color'] . '|' . ($item['style_size'] ?? '');
            $poTambahanIndex[$key] = $item;
        }

        // Ambil qty, kg_po, bs_mesin, bs_setting per style
        $qtyOrderList = [];
        $kgPoList = [];
        $bsMesinGrList = [];
        $bsMesinDzList = [];
        $bsMesinKgList = [];
        $bsSettingList = [];
        $bsSettingKgList = [];

        foreach ($material as $item) {
            $keyStyle = $item['no_model'] . '|' . $item['item_type'] . '|' . $item['kode_warna'] . '|' . $item['style_size'];
            $style = $item['style_size'];
            $noModel = $item['no_model'];

            // --- Qty Order ---
            $qty = $this->ApsPerstyleModel->getSisaPerSize($area, $noModel, [$style]);
            $qty_order = is_array($qty) ? ($qty['qty'] ?? 0) : ($qty->qty ?? 0);
            $qtyOrderList[$keyStyle] = $qty_order;

            // --- Kg PO ---
            $composition = (float)($item['composition'] ?? 0);
            $gw = (float)($item['gw'] ?? 0);
            $loss = (float)($item['loss'] ?? 0);
            $kgPoList[$keyStyle] = ($qty_order * $composition * $gw / 100 / 1000) * (1 + ($loss / 100));

            // --- BS MESIN ---
            $bs = $this->bsMesinModel->getBsMesin($area, $noModel, [$style]);
            $bsGram = is_array($bs) ? ($bs['bs_gram'] ?? 0) : ($bs->bs_gram ?? 0);
            $bsMesinGrList[$keyStyle] = (float)$bsGram;
            $bsMesinDzList[$keyStyle] = (float)$bsGram / $gw / 24;
            $bsMesinKgList[$keyStyle] = (float)$bsGram / 1000;

            // --- BS SETTING ---
            $validate = [
                'area' => $area,
                'style' => $style,
                'no_model' => $noModel
            ];
            $idaps = $this->ApsPerstyleModel->getIdForBs($validate);
            if (!is_array($idaps) || empty($idaps)) {
                $bsSettingList[$keyStyle] = 0;
            } else {
                $bsSetting = $this->bsModel->getTotalBsSet($idaps);
                $bsPcs = is_array($bsSetting) ? ($bsSetting['qty'] ?? 0) : ($bsSetting->qty ?? 0);
                $bsSettingList[$keyStyle] = (float)$bsPcs;
                $bsSettingKgList[$keyStyle] = ($bsPcs * $composition * $gw / 100 / 1000);
            }
        }

        // --- Group per no_model|item_type|kode_warna ---
        $grouped = [];

        foreach ($dataRetur as $row) {
            $keyGroup = $row['no_model'] . '|' . $row['item_type'] . '|' . $row['kode_warna'];

            // === INISIALISASI GRUP HANYA SEKALI ===
            if (!isset($grouped[$keyGroup])) {
                // Cari semua style yang match group ini
                $matchingKeys = [];
                foreach ($materialIndex as $k => $m) {
                    if (
                        $m['no_model'] === $row['no_model'] &&
                        $m['item_type'] === $row['item_type'] &&
                        $m['kode_warna'] === $row['kode_warna']
                    ) {
                        $matchingKeys[] = $k;
                    }
                }

                // Kumpulkan detail per style_size
                $details = [];
                $total_bs_mesin_dz = 0;
                $total_bs_mesin_kg = 0;
                $total_bs_setting_dz = 0;
                $total_bs_setting_kg = 0;

                // --- detail po plus per style_size ---
                $total_sisa_order_pcs = 0;
                $total_poplus_mc_kg = 0;
                $total_poplus_mc_cns = 0;
                $total_plus_pck_pcs = 0;
                $total_plus_pck_kg = 0;
                $total_plus_pck_cns = 0;
                $total_ttl_sisa_bb_dimc = 0;
                $total_ttl_tambahan_kg = 0;
                $total_ttl_tambahan_cns = 0;

                foreach ($matchingKeys as $k) {
                    $mat = $materialIndex[$k];
                    $qty_order = $qtyOrderList[$k] ?? 0;
                    $kg_po = $kgPoList[$k] ?? 0;
                    $loss_value = (float)($mat['loss'] ?? 0);

                    $bs_mesin_gr = $bsMesinGrList[$k] ?? 0;
                    $bs_mesin_dz = $bsMesinDzList[$k] ?? 0;
                    $bs_mesin_kg = $bsMesinKgList[$k] ?? 0;
                    $bs_setting = $bsSettingList[$k] ?? 0;
                    $bs_setting_kg = $bsSettingKgList[$k] ?? 0;

                    // Akumulasi total per kode warna
                    $total_bs_mesin_dz += (float)$bs_mesin_dz;
                    $total_bs_mesin_kg += (float)$bs_mesin_kg;
                    $total_bs_setting_dz += (float)$bs_setting / 24;
                    $total_bs_setting_kg += (float)$bs_setting_kg;

                    // Merge PO tambahan per style_size
                    $keyStyle = $mat['no_model'] . '|' . $mat['item_type'] . '|' . $mat['kode_warna'] . '|' . $row['color'] . '|' . $mat['style_size'];
                    $po = $poTambahanIndex[$keyStyle] ?? [];

                    $sisa_order_pcs   = (float)($po['sisa_order_pcs'] ?? 0);
                    $poplus_mc_kg     = (float)($po['poplus_mc_kg'] ?? 0);
                    $poplus_mc_cns    = (float)($po['poplus_mc_cns'] ?? 0);
                    $plus_pck_pcs     = (float)($po['plus_pck_pcs'] ?? 0);
                    $plus_pck_kg      = (float)($po['plus_pck_kg'] ?? 0);
                    $plus_pck_cns     = (float)($po['plus_pck_cns'] ?? 0);
                    $ttl_sisa_bb_dimc = (float)($po['ttl_sisa_bb_dimc'] ?? 0);
                    $ttl_tambahan_kg  = (float)($po['ttl_tambahan_kg'] ?? 0);
                    $ttl_tambahan_cns = (float)($po['ttl_tambahan_cns'] ?? 0);

                    // akumulasi total group
                    $total_sisa_order_pcs   += $sisa_order_pcs;
                    $total_poplus_mc_kg     += $poplus_mc_kg;
                    $total_poplus_mc_cns    += $poplus_mc_cns;
                    $total_plus_pck_pcs     += $plus_pck_pcs;
                    $total_plus_pck_kg      += $plus_pck_kg;
                    $total_plus_pck_cns     += $plus_pck_cns;
                    $total_ttl_sisa_bb_dimc += $ttl_sisa_bb_dimc;
                    $total_ttl_tambahan_kg  += $ttl_tambahan_kg;
                    $total_ttl_tambahan_cns += $ttl_tambahan_cns;

                    $details[] = [
                        'style_size' => $mat['style_size'] ?? '',
                        'composition' => (float)($mat['composition'] ?? 0),
                        'gw' => (float)($mat['gw'] ?? 0),
                        'qty_order' => $qty_order,
                        'kg_po' => $kg_po,
                        'loss' => $loss_value,
                        'bs_mesin_gr' => $bs_mesin_gr,
                        'bs_mesin_dz' => $bs_mesin_dz,
                        'bs_mesin_kg' => $bs_mesin_kg,
                        'bs_setting' => $bs_setting,
                        'bs_setting_kg' => $bs_setting_kg,
                        'sisa_order_pcs' => $sisa_order_pcs,
                        'poplus_mc_kg' => $poplus_mc_kg,
                        'poplus_mc_cns' => $poplus_mc_cns,
                        'plus_pck_pcs' => $plus_pck_pcs,
                        'plus_pck_kg' => $plus_pck_kg,
                        'plus_pck_cns' => $plus_pck_cns,
                        'ttl_sisa_bb_dimc' => $ttl_sisa_bb_dimc,
                        'ttl_tambahan_kg' => $ttl_tambahan_kg,
                        'ttl_tambahan_cns' => $ttl_tambahan_cns
                    ];
                }

                // --- Simpan grup utama ---
                $grouped[$keyGroup] = [
                    'no_model'       => $row['no_model'],
                    'item_type'      => $row['item_type'],
                    'kode_warna'     => $row['kode_warna'],
                    'color'          => $row['color'] ?? '',
                    'kategori'       => $row['kategori'] ?? '',
                    'terima_kg'      => (float)($row['terima_kg'] ?? 0),
                    'sisa_bb_mc'     => (float)($row['sisa_bb_mc'] ?? 0),
                    'poplus_mc_kg'   => (float)($row['poplus_mc_kg'] ?? 0),
                    'plus_pck_kg'    => (float)($row['plus_pck_kg'] ?? 0),
                    'ttl_tambahan_kg' => (float)($row['ttl_tambahan_kg'] ?? 0),
                    'total_kgs_retur' => 0,
                    'total_cns_retur' => 0,
                    'total_krg_retur' => 0,
                    'total_bs_mesin_dz'   => $total_bs_mesin_dz,
                    'total_bs_mesin_kg'   => $total_bs_mesin_kg,
                    'total_bs_setting_dz' => $total_bs_setting_dz,
                    'total_bs_setting_kg' => $total_bs_setting_kg,
                    'details'        => $details,
                    'detail_lot'     => []
                ];
            }

            // === AKUMULASI TOTAL RETUR ===
            $grouped[$keyGroup]['total_kgs_retur'] += (float)($row['kgs_retur'] ?? 0);
            $grouped[$keyGroup]['total_cns_retur'] += (float)($row['cns_retur'] ?? 0);
            $grouped[$keyGroup]['total_krg_retur'] += (float)($row['krg_retur'] ?? 0);

            // === DETAIL LOT ===
            $lotKey = trim($row['lot_retur'] ?? '');
            if (!isset($grouped[$keyGroup]['detail_lot'][$lotKey])) {
                $grouped[$keyGroup]['detail_lot'][$lotKey] = [
                    'lot_retur' => $lotKey,
                    'kgs_retur' => 0,
                    'cns_retur' => 0,
                    'krg_retur' => 0,
                ];
            }

            $grouped[$keyGroup]['detail_lot'][$lotKey]['kgs_retur'] += (float)$row['kgs_retur'];
            $grouped[$keyGroup]['detail_lot'][$lotKey]['cns_retur'] += (float)$row['cns_retur'];
            $grouped[$keyGroup]['detail_lot'][$lotKey]['krg_retur'] += (float)$row['krg_retur'];
        }

        // Biar rapi, ubah detail_lot jadi array numerik
        foreach ($grouped as &$g) {
            $g['detail_lot'] = array_values($g['detail_lot']);
        }

        // Gunakan $dataReturGrouped untuk loop di Excel
        $dataReturGrouped = array_values($grouped);
        // dd($dataRetur, $dataPoTambahan, $dataReturGrouped, $kgPoList);
        $delivery = $dataReturGrouped[0]['delivery_akhir'] ?? '';
        // Buat Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $firstSheet = true;

        if ($firstSheet) {
            $sheet = $spreadsheet->getActiveSheet();
            $firstSheet = false;
        } else {
            $sheet = $spreadsheet->createSheet();
        }
        $sheet->setTitle('Form Retur');
        // Style untuk header tabel (border, center, bold)
        $styleHeader = [
            'font' => [
                'bold' => false,
                'size' => 10, // <--- Ukuran font diatur di sini
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ];

        $styleBody = [
            'font' => [
                'size' => 10, // Ukuran font isi tabel
            ],
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];

        $spreadsheet->getDefaultStyle()->getFont()->setName('Arial');
        // $spreadsheet->getDefaultStyle()->getFont()->setSize(16);

        // 1. Atur ukuran kertas jadi A4
        $sheet->getPageSetup()
            ->setPaperSize(PageSetup::PAPERSIZE_A4);

        // 2. Atur orientasi jadi landscape
        $sheet->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        // 3. (Opsional) Atur scaling, agar muat ke 1 halaman
        $sheet->getPageSetup()
            ->setFitToWidth(1)
            ->setFitToHeight(0)    // 0 artinya auto height
            ->setFitToPage(true); // aktifkan fitting

        // 4. (Opsional) Atur margin supaya tidak terlalu sempit
        $sheet->getPageMargins()->setTop(0.2)
            ->setBottom(0.2)
            ->setLeft(0.2)
            ->setRight(0.2);

        //Border Thin
        $sheet->getStyle('D1:D3')->applyFromArray([
            'borders' => [
                'right' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
        $sheet->getStyle('D4')->applyFromArray([
            'borders' => [
                'right' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
        $sheet->getStyle('S4:Z4')->applyFromArray([
            'borders' => [
                'left' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
                'right' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
        $sheet->getStyle('S5:S5')->applyFromArray([
            'borders' => [
                'left' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
                'right' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // // Double border baris 4 dan 5
        $sheet->getStyle('A4:AD4')->applyFromArray([
            'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
                'bottom' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
        $sheet->getStyle('A5:AD5')->applyFromArray([
            'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
                'bottom' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Border kiri
        $sheet->getStyle('AA5:AD5')->applyFromArray([
            'borders' => [
                'left' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => '000000'],
                ],
            ],
        ]);

        $thinInside = [
            'borders' => [
                // border antar kolom (vertical lines) di dalam range
                'vertical' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
                // border antar baris (horizontal lines) di dalam range
                'horizontal' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];

        // 2) Border tipis atas untuk baris header tabel (A11:Q11)
        $sheet->getStyle('A11:AD11')->applyFromArray([
            'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Lebar kolom (dalam pt) dan tinggi baris (dalam pt)
        $columnWidths = [
            'A' => 20,
            'B' => 20,
            'C' => 50,
            'D' => 80,
            'E' => 70,
            'F' => 70,
            'G' => 45,
            'H' => 20,
            'I' => 40,
            'J' => 20,
            'K' => 40,
            'L' => 30,
            'M' => 30,
            'N' => 30,
            'O' => 40,
            'P' => 30,
            'Q' => 30,
            'R' => 30,
            'S' => 30,
            'T' => 30,
            'U' => 30,
            'V' => 30,
            'W' => 30,
            'X' => 30,
            'Y' => 30,
            'Z' => 30,
            'AA' => 33,
            'AB' => 30,
            'AC' => 33,
            'AD' => 90,
        ];

        $rowHeightsPt = array_fill_keys(range(11, 33), 36);
        $rowHeightsPt[11] = 50;
        $rowHeightsPt[12] = 50;

        // Atur tinggi baris
        foreach ($rowHeightsPt as $row => $height) {
            $sheet->getRowDimension($row)->setRowHeight($height);
        }

        // Atur lebar kolom
        foreach ($columnWidths as $col => $pt) {
            $sheet->getColumnDimension($col)->setWidth(round($pt / 5.25, 2));
        }

        // Tulis Header Form
        $this->renderHeaderFormRetur($sheet, $area, $tglBuat, $delivery, $dataReturGrouped, $styleHeader);

        // Isi tabel Start
        $rowNum = 11;
        $maxRowsPerPage = 25;
        $currentPageRowCount = 0;
        $sheet->getRowDimension($rowNum)->setRowHeight(18);

        foreach ($dataReturGrouped as $group) {
            $no_model   = $group['no_model'] ?? '';
            $item_type  = $group['item_type'] ?? '';
            $kode_warna = $group['kode_warna'] ?? '';
            $color      = $group['color'] ?? '';

            // Akumulasi total per group
            $total_qty_order = 0;
            $total_kg_po = 0;
            $total_poplus_mc_pcs = 0;
            $total_poplus_mc_kg = 0;
            $total_plus_pck_kg = 0;
            $total_ttl_tambahan_kg = 0;

            $terima_kg   = (float)($group['terima_kg'] ?? 0);
            $sisa_bb_mc  = (float)($group['sisa_bb_mc'] ?? 0);
            $ttl_tambahan_kg = (float)($group['ttl_tambahan_kg'] ?? 0);
            $total_kgs_retur = (float)($group['total_kgs_retur'] ?? 0);

            foreach ($group['details'] as $detail) {
                $style_size  = $detail['style_size'] ?? '';
                $composition = $detail['composition'] ?? 0;
                $gw          = $detail['gw'] ?? 0;
                $qty_order   = (float)($detail['qty_order'] ?? 0);
                $loss        = (float)($detail['loss'] ?? 0);
                $kg_po       = (float)($detail['kg_po'] ?? 0);

                // PO Tambahan
                $sisa_order_pcs   = (float)($detail['sisa_order_pcs'] ?? 0);
                $poplus_mc_kg     = (float)($detail['poplus_mc_kg'] ?? 0);
                $poplus_mc_cns    = (float)($detail['poplus_mc_cns'] ?? 0);
                $plus_pck_pcs     = (float)($detail['plus_pck_pcs'] ?? 0);
                $plus_pck_kg      = (float)($detail['plus_pck_kg'] ?? 0);
                $plus_pck_cns     = (float)($detail['plus_pck_cns'] ?? 0);
                $ttl_sisa_bb_dimc = (float)($detail['ttl_sisa_bb_dimc'] ?? 0);
                $ttl_tambahan_kg  = (float)($detail['ttl_tambahan_kg'] ?? 0);
                $ttl_tambahan_cns = (float)($detail['ttl_tambahan_cns'] ?? 0);

                // Tambahkan ke total
                $total_qty_order     += $qty_order;
                $total_kg_po         += $kg_po;
                $total_poplus_mc_pcs  += $sisa_order_pcs;
                $total_poplus_mc_kg  += $poplus_mc_kg;
                $total_plus_pck_kg   += $plus_pck_kg;
                $total_ttl_tambahan_kg = $ttl_tambahan_kg;

                // Format hanya jika > 0
                $fmt = fn($v, $dec = 2) => ($v && $v != 0) ? number_format($v, $dec) : '';

                // Cek apakah baris sudah melebihi batas halaman
                if ($currentPageRowCount >= $maxRowsPerPage) {
                    // buat page break
                    $sheet->setBreak('A' . ($rowNum - 1), \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);

                    // tambahkan header form baru di halaman berikutnya
                    $rowNum += 2; // jarak sedikit
                    $this->renderHeaderFormRetur($sheet, $area, $tglBuat, $delivery, $dataReturGrouped, $$styleHeader);

                    $rowNum = 11; // header form kamu mengisi baris 1–10
                    $currentPageRowCount = 0;
                }

                // Tulis baris detail
                $sheet->fromArray([
                    $no_model, // A
                    '', // B
                    $color, // C
                    $item_type, // D
                    $kode_warna, // E
                    $style_size, // F
                    $fmt($composition, 2), // G
                    $fmt($gw, 2), // H
                    $fmt($qty_order, 0), // I
                    $fmt($loss, 2), // J
                    $fmt($kg_po, 2), // K
                    '', // L
                    '', // M
                    '', // N
                    '', // O
                    $fmt($sisa_order_pcs, 0), // P
                    $fmt($poplus_mc_kg, 2), // Q
                    '', // R
                    $fmt($poplus_mc_kg / $kg_po * 100, 2) . '%', // S
                    $fmt($plus_pck_pcs, 0), // T
                    $fmt($plus_pck_kg, 2), // U
                    '', // V
                    $fmt($plus_pck_kg / $kg_po * 100, 2) . '%', // W
                    '', // X
                    '', // Y
                    '', // Z
                    '', // AA
                    '', // AB
                    '', // AC
                    '', // AD
                ], null, 'A' . $rowNum);

                // 🔹 Merge kolom A dan B
                $sheet->mergeCells("A{$rowNum}:B{$rowNum}");

                $sheet->getRowDimension($rowNum)->setRowHeight(-1);
                $rowNum++;
            }

            // Hitung selisih
            $selisih = $terima_kg - $total_kg_po;
            $persen_terima = $total_kg_po > 0 ? (round($terima_kg, 2) / round($total_kg_po, 2)) * 100 : 0;
            $persen_tambahan = $total_kg_po > 0 ? (round($total_ttl_tambahan_kg, 2) / round($total_kg_po, 2)) * 100 : 0;

            // $fmt = fn($v, $dec = 2) => ($v && $v != 0) ? number_format($v, $dec) : '';
            $fmt = fn($v, $dec = 2) => number_format((float)$v, $dec);

            $retur_kg_psn = $retur_persen_psn = $retur_kg_po = $retur_persen_po = 0;
            // Cek logika penempatan kgs_retur dan persennya
            if (($total_ttl_tambahan_kg) == 0) {
                // Jika tidak ada tambahan (mesin & packing), maka hitung % dari PSN
                if ($total_kgs_retur > 0) {
                    $retur_kg_psn = round($total_kgs_retur, 2);
                    if ($total_kg_po > 0) {
                        $retur_persen_psn = round((round($total_kgs_retur, 2) / round($total_kg_po, 2)) * 100, 2) . '%';
                    }
                }
            } else {
                // Jika ada tambahan, maka hitung % dari PO(+)
                if ($total_kgs_retur > 0) {
                    $retur_kg_po = round($total_kgs_retur, 2);
                    $totalPO = $total_kg_po + $total_ttl_tambahan_kg;
                    if ($totalPO > 0) {
                        $retur_persen_po = round((round($total_kgs_retur, 2) / round($totalPO, 2)) * 100, 2) . '%';
                    }
                }
            }

            // Baris total (subtotal per warna)
            $sheet->fromArray([
                '', // A
                '', // B
                '', // C
                '', // D
                '', // E
                '', // F
                '', // G
                '', // H
                $fmt($total_qty_order, 0), // I
                '', // J
                $fmt($total_kg_po, 2), // K
                $fmt($terima_kg, 2), // L
                $fmt($selisih, 2), // M
                $persen_terima ? $fmt($persen_terima, 0) . '%' : '', // N
                $fmt($sisa_bb_mc, 2), // O
                $fmt($total_poplus_mc_pcs, 0), // P
                $fmt($total_poplus_mc_kg, 2), // Q
                '', // R
                ($total_kg_po > 0) ? $fmt((round($total_poplus_mc_kg, 2) / round($total_kg_po, 2)) * 100) . '%' : '', // S
                $fmt($total_plus_pck_pcs, 2), // T
                $fmt($total_plus_pck_kg, 2), // U
                '', // V
                ($total_kg_po > 0) ? $fmt((round($total_plus_pck_kg, 2) / round($total_kg_po, 2)) * 100) . '%' : '', // W
                $fmt($total_ttl_tambahan_kg, 2), // X
                $persen_tambahan ? $fmt($persen_tambahan, 0) . '%' : '', // Y
                $retur_kg_psn,      // Z
                $retur_persen_psn,  // AA
                $retur_kg_po,       // AB
                $retur_persen_po,   // AC
                '', // AD
            ], null, 'A' . $rowNum);

            // 🔹 Merge kolom A dan B untuk baris subtotal
            $sheet->mergeCells("A{$rowNum}:B{$rowNum}");

            // Styling total
            $sheet->getStyle("A{$rowNum}:AD{$rowNum}")->applyFromArray([
                'font' => ['bold' => true],
                'borders' => [
                    'top' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
                    'bottom' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
                ],
            ]);
            $sheet->getRowDimension($rowNum)->setRowHeight(20);

            // === Merge kolom kategori (misal kolom AD) ===
            $kategori = $group['kategori'] ?? '';
            $detailCount = count($group['details']);

            // baris pertama data detail group
            $mergeStart = $rowNum - $detailCount;
            // baris terakhir (subtotal terakhir group ini)
            $mergeEnd = $rowNum - 1;

            $sheet->mergeCells("AD{$mergeStart}:AD{$mergeEnd}");
            // === Gabungkan keterangan lama dengan detail lot retur ===
            $keteranganText = $kategori . '. (BS MC= ' . round($total_bs_mesin_dz, 2) .
                'DZ / ' . round($total_bs_mesin_kg, 2) .
                'KG, BS ST= ' . round($total_bs_setting_dz, 2) .
                'DZ / ' . round($total_bs_setting_kg, 2) . 'KG)';

            // 🔹 Tambahkan data lot retur (kalau ada)
            if (!empty($group['detail_lot'])) {
                $lotDetailsText = [];
                foreach ($group['detail_lot'] as $lot) {
                    $lotName = $lot['lot_retur'] ?? '';
                    $kgsLot  = (float)($lot['kgs_retur'] ?? 0);
                    if ($lotName !== '' && $kgsLot > 0) {
                        $lotDetailsText[] = "{$lotName}=" . round($kgsLot, 2) . "KG";
                    }
                }

                if (!empty($lotDetailsText)) {
                    $keteranganText .= ', ' . implode(', ', $lotDetailsText);
                }
            }

            // Set ke kolom AD (gabungkan tanpa menghapus keterangan lama)
            $sheet->setCellValue("AD{$mergeStart}", $keteranganText);

            // Rata tengah
            $sheet->getStyle("AD{$mergeStart}:AD{$mergeEnd}")
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);


            $rowNum++;
        }

        $lastRow = $rowNum - 1;

        // Rata tengah semua isi tabel
        $sheet->getStyle("A11:AD{$lastRow}")->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        // Atur font: bold + size 10
        $sheet->getStyle("A11:AD{$lastRow}")->getFont()
            ->setSize(10);

        // Tambahkan border kiri double untuk kolom A
        $sheet->getStyle("A11:A{$lastRow}")->applyFromArray([
            'borders' => [
                'left' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Tambahkan border kanan double untuk kolom AD
        $sheet->getStyle("AD11:AD{$lastRow}")->applyFromArray([
            'borders' => [
                'right' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $thinInside = [
            'borders' => [
                'vertical' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
                'horizontal' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];
        $sheet->getStyle("A11:AD{$lastRow}")->applyFromArray($thinInside);

        // 3) Border tipis bawah untuk baris total (A28:Q28)
        $sheet->getStyle("A28:AD{$lastRow}")->applyFromArray([
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Aktifkan wrap text di A11:Q28
        $sheet->getStyle("A11:AD{$lastRow}")->getAlignment()->setWrapText(true);
        // Isi tabel End

        //Tanda Tangan
        $startTT = $lastRow + 2; // beri jarak 1 baris kosong
        $sheet->getRowDimension($startTT)->setRowHeight(10);

        // Susunan tanda tangan dalam 1 baris
        $sheet->mergeCells("A{$startTT}:D{$startTT}");
        $sheet->setCellValue("A{$startTT}", 'MANAJEMEN NS');

        $sheet->mergeCells("E{$startTT}:F{$startTT}");
        $sheet->setCellValue("E{$startTT}", 'KEPALA AREA');

        $sheet->mergeCells("G{$startTT}:H{$startTT}");
        $sheet->setCellValue("G{$startTT}", 'IE TEKNISI');

        $sheet->mergeCells("I{$startTT}:K{$startTT}");
        $sheet->setCellValue("I{$startTT}", 'PIC PACKING');

        $sheet->mergeCells("L{$startTT}:N{$startTT}");
        $sheet->setCellValue("L{$startTT}", 'PPC');

        $sheet->mergeCells("O{$startTT}:Q{$startTT}");
        $sheet->setCellValue("O{$startTT}", 'PPC');

        $sheet->mergeCells("R{$startTT}:T{$startTT}");
        $sheet->setCellValue("R{$startTT}", 'PPC');

        $sheet->mergeCells("U{$startTT}:W{$startTT}");
        $sheet->setCellValue("U{$startTT}", 'GD BENANG');

        $sheet->mergeCells("X{$startTT}:AA{$startTT}");
        $sheet->setCellValue("X{$startTT}", 'MENGETAHUI');

        $sheet->mergeCells("AB{$startTT}:AD{$startTT}");
        $sheet->setCellValue("AB{$startTT}", 'MENGETAHUI');

        // Rata tengah semua kolom tanda tangan
        $sheet->getStyle("A{$startTT}:AD{$startTT}")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        //Outline Border
        // 🩶 Hitung batas data terakhir
        $lastTT = $startTT + 4; // baris terakhir dari data kamu

        // 🩶 Terapkan double border hanya di sisi luar (sesuai margin)
        $borderStyleOuter = [
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];

        $sheet->getStyle("A1:AD{$lastTT}")->applyFromArray($borderStyleOuter);

        for ($r = $startTT; $r <= $startTT + 4; $r++) {
            $sheet->getRowDimension($r)->setRowHeight(20);
        }

        // Terapkan border luar — sesuai margin (atas: 1, bawah: lastRow)

        $sheet->getStyle("A11:AD{$lastTT}")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $startKet = $lastTT + 2;

        // BARIS KETERANGAN BS
        // $sheet->mergeCells("A{$startKet}:C{$startKet}");
        // $sheet->setCellValue("A{$startKet}", 'BS SETTING(KG)');

        // $sheet->setCellValue("D{$startKet}", ':');

        // $sheet->setCellValue("E{$startKet}", 'BS MESIN(KG)');

        // $sheet->setCellValue("F{$startKet}", ':');

        // $sheet->mergeCells("G{$startKet}:H{$startKet}");
        // $sheet->setCellValue("G{$startKet}", 'TOTAL BS(%)');

        // $sheet->setCellValue("I{$startKet}", ':');

        // $sheet->setCellValue("K{$startKet}", 'RETUR(KG)');

        // $sheet->mergeCells("L{$startKet}:M{$startKet}");
        // $sheet->setCellValue("L{$startKet}", ':');

        // $sheet->mergeCells("O{$startKet}:P{$startKet}");
        // $sheet->setCellValue("O{$startKet}", 'RETUR(%)');

        // $sheet->mergeCells("Q{$startKet}:R{$startKet}");
        // $sheet->setCellValue("Q{$startKet}", ':');

        // Output Excel
        $filename = 'FORM RETUR ' . $area . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        setcookie("downloadComplete", "true", time() + 60, "/");
        $writer->save('php://output');
        exit;
    }

    private function renderHeaderFormRetur($sheet, $area, $tglBuat, $delivery, $dataReturGrouped, $styleHeader)
    {
        // Header Form Start
        $sheet->mergeCells('A1:D2');
        $sheet->getRowDimension(1)->setRowHeight(30);

        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo Perusahaan');
        $drawing->setPath('assets/img/logo-kahatex.png');
        $drawing->setCoordinates('C1');
        $drawing->setHeight(50);
        $drawing->setOffsetX(37);
        $drawing->setOffsetY(10);
        $drawing->setWorksheet($sheet);
        $sheet->mergeCells('A3:D3');
        $sheet->setCellValue('A3', 'PT. KAHATEX');
        $sheet->getStyle('A3')->getFont()->setSize(11)->setBold(true);
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('E1', 'FORMULIR');
        $sheet->getStyle('E1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('E1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $sheet->getStyle('E1')->getFill()->getStartColor()->setRGB('99FFFF');
        $sheet->mergeCells('E1:AD1');
        $sheet->getStyle('E1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('E2:AD2');
        $sheet->setCellValue('E2', 'DEPARTEMEN KAOS KAKI');
        $sheet->getStyle('E2')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('E2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('E3:AD3');
        $sheet->setCellValue('E3', 'PO TAMBAHAN DAN RETURAN BAHAN BAKU MESIN KE GUDANG BENANG');
        $sheet->getStyle('E3')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('E3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A4:C4');
        $sheet->setCellValue('A4', 'No. Dokumen');
        $sheet->setCellValue('D4', 'FOR-KK-034/REV_05/HAL_1/1');

        $sheet->mergeCells('S4:Z4');
        $sheet->setCellValue('S4', 'Tanggal Revisi');
        $sheet->mergeCells('AA4:AD4');
        $sheet->setCellValue('AA4', '17 Maret 2025');
        $sheet->getStyle('AA4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('S5:Z5');
        $sheet->setCellValue('S5', 'Klasifikasi');
        $sheet->mergeCells('AA5:AD5');
        $sheet->setCellValue('AA5', 'Internal');
        $sheet->getStyle('AA5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A5:R5');
        $sheet->getStyle('A4:AD5')->getFont()->setBold(true)->setSize(11);

        $sheet->setCellValue('A6', 'Area: ' . $area);
        $sheet->getStyle('A6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)
            ->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A6')->getFont()->setBold(true)->setSize(10);

        $sheet->setCellValue('G6', 'Loss F.Up');
        $sheet->getStyle('G6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)
            ->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('G6')->getFont()->setBold(true)->setSize(10);

        $lossValue = isset($dataReturGrouped[0]['details'][0]['loss'])
            ? $dataReturGrouped[0]['details'][0]['loss'] . '%'
            : '';
        $sheet->setCellValue('I6', ': ' . $lossValue);
        $sheet->getStyle('I6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)
            ->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('I6')->getFont()->setBold(true)->setSize(10);

        $sheet->setCellValue('P6', 'Tanggal Buat');
        $sheet->getStyle('P6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)
            ->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('P6')->getFont()->setBold(true)->setSize(10);

        $sheet->setCellValue('S6', ': ' . $tglBuat);
        $sheet->getStyle('S6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)
            ->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('S6')->getFont()->setBold(true)->setSize(10);

        $sheet->setCellValue('P7', 'Tanggal Export');
        $sheet->getStyle('P7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)
            ->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('P7')->getFont()->setBold(true)->setSize(10);

        $sheet->setCellValue('S7', ': ' . $delivery);
        $sheet->getStyle('S7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)
            ->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('S7')->getFont()->setBold(true)->setSize(10);

        // Header utama dan sub-header
        $sheet->setCellValue('A8', 'Model');
        $sheet->mergeCells('A8:B10');
        $sheet->getStyle('A8:B10')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('C8', 'Warna');
        $sheet->mergeCells('C8:C10');
        $sheet->getStyle('C8:C10')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('D8', 'Item Type');
        $sheet->mergeCells('D8:D10');
        $sheet->getStyle('D8:D10')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('E8', 'Kode Warna');
        $sheet->mergeCells('E8:E10');
        $sheet->getStyle('E8:E10')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('F8', 'Style / Size');
        $sheet->mergeCells('F8:F10');
        $sheet->getStyle('F8:F10')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('G8', 'Komposisi ( % )');
        $sheet->mergeCells('G8:G10');
        $sheet->getStyle('G8:G10')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('H8', 'GW / Pcs');
        $sheet->mergeCells('H8:H10');
        $sheet->getStyle('H8:H10')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('I8', 'Qty / Pcs');
        $sheet->mergeCells('I8:I10');
        $sheet->getStyle('I8:I10')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('J8', 'Loss');
        $sheet->mergeCells('J8:J10');
        $sheet->getStyle('J8:J10')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('K8', 'Pesanan Kgs');
        $sheet->mergeCells('K8:K10');
        $sheet->getStyle('K8:K10')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('L8', 'Terima');
        $sheet->mergeCells('L8:N9');
        $sheet->getStyle('L8:N9')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('L10', 'Kg');
        $sheet->getStyle('L10')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('M10', '+ / -');
        $sheet->getStyle('M10')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('N10', '%');
        $sheet->getStyle('N10')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('O8', 'Sisa Benang di mesin');
        $sheet->mergeCells('O8:O9');
        $sheet->getStyle('O8:O9')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('O10', 'Kg');
        $sheet->getStyle('O10')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('P8', 'Tambahan I (mesin)');
        $sheet->mergeCells('P8:S8');
        $sheet->getStyle('P8:S8')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('P9', 'Pcs');
        $sheet->mergeCells('P9:P10');
        $sheet->getStyle('P9:P10')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('Q9', 'Benang');
        $sheet->mergeCells('Q9:R9');
        $sheet->getStyle('Q9:R9')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('Q10', 'Kg');
        $sheet->getStyle('Q10')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('R10', 'Cones');
        $sheet->getStyle('R10')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('S9', '%');
        $sheet->mergeCells('S9:S10');
        $sheet->getStyle('S9:S10')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('T8', 'Tambahan II (Packing)');
        $sheet->mergeCells('T8:W8');
        $sheet->getStyle('T8:W8')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('T9', 'Pcs');
        $sheet->mergeCells('T9:T10');
        $sheet->getStyle('T9:T10')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('U9', 'Benang');
        $sheet->mergeCells('U9:V9');
        $sheet->getStyle('U9:V9')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('U10', 'Kg');
        $sheet->getStyle('U10')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('V10', 'Cones');
        $sheet->getStyle('V10')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('W9', '%');
        $sheet->mergeCells('W9:W10');
        $sheet->getStyle('W9:W10')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('X8', 'Total lebih pakai benang');
        $sheet->mergeCells('X8:Y9');
        $sheet->getStyle('X8:Y9')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('X10', 'Kg');
        $sheet->getStyle('X10')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('Y10', '%');
        $sheet->getStyle('Y10')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('Z8', 'RETURAN');
        $sheet->mergeCells('Z8:AC8');
        $sheet->getStyle('Z8:AC8')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('Z9', 'Kg');
        $sheet->mergeCells('Z9:Z10');
        $sheet->getStyle('Z9:Z10')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('AA9', '% dari PSN');
        $sheet->mergeCells('AA9:AA10');
        $sheet->getStyle('AA9:AA10')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('AB9', 'Kg');
        $sheet->mergeCells('AB9:AB10');
        $sheet->getStyle('AB9:AB10')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('AC9', '% dari PO(+)');
        $sheet->mergeCells('AC9:AC10');
        $sheet->getStyle('AC9:AC10')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('AD8', 'Keterangan');
        $sheet->mergeCells('AD8:AD10');
        $sheet->getStyle('AD8:AD10')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        // Terapkan style ke seluruh area header (baris 8–10)
        $sheet->getStyle('A8:AD10')->applyFromArray($styleHeader);
        // Border kiri double untuk kolom A pada header
        $sheet->getStyle('A8:A10')->applyFromArray([
            'borders' => [
                'left' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Border kanan double untuk kolom AD pada header
        $sheet->getStyle('AD8:AD10')->applyFromArray([
            'borders' => [
                'right' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
        // Header Form End
    }

    public function exportDatangBenang()
    {
        $key = $this->request->getGet('key');
        $tanggalAwal = $this->request->getGet('tanggal_awal');
        $tanggalAkhir = $this->request->getGet('tanggal_akhir');
        $poPlus = $this->request->getGet('po_plus');

        $apiUrl = api_url('material') . 'filterDatangBenang?key=' . urlencode($key) . '&tanggal_awal=' . $tanggalAwal . '&tanggal_akhir=' . $tanggalAkhir . '&po_plus=' . $poPlus;

        $material = @file_get_contents($apiUrl);

        // $models = [];
        if ($material !== FALSE) {
            $data = json_decode($material, true);
        }

        // $data = $this->pemasukanModel->getFilterDatangBenang($key, $tanggal_awal, $tanggal_akhir);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Judul
        $sheet->setCellValue('A1', 'Datang Benang');
        $sheet->mergeCells('A1:V1'); // Menggabungkan sel untuk judul
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Header
        $header = ["No", "Foll Up", "No Model", "No Order", "Buyer", "Delivery Awal", "Delivery Akhir", "Order Type", "Item Type", "Kode Warna", "Warna", "KG Pesan", "Tanggal Datang", "Kgs Datang", "Cones Datang", "LOT Datang", "No Surat Jalan", "LMD", "GW", "Harga", "Nama Cluster", "Po Tambahan"];
        $sheet->fromArray([$header], NULL, 'A3');

        // Styling Header
        $sheet->getStyle('A3:V3')->getFont()->setBold(true);
        $sheet->getStyle('A3:V3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3:V3')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        // Data
        $row = 4;
        foreach ($data as $index => $item) {
            $getPoPlus = $item['po_plus'];
            if ($getPoPlus == 1) {
                $poPlus = 'YA';
            } else {
                $poPlus = '';
            }
            $sheet->fromArray([
                [
                    $index + 1,
                    $item['foll_up'],
                    $item['no_model'],
                    $item['no_order'],
                    $item['buyer'],
                    $item['delivery_awal'],
                    $item['delivery_akhir'],
                    $item['unit'],
                    $item['item_type'],
                    $item['kode_warna'],
                    $item['warna'],
                    number_format($item['kgs_material'], 2),
                    $item['tgl_datang'],
                    number_format($item['kgs_kirim'], 2),
                    $item['cones_kirim'],
                    $item['lot_kirim'],
                    $item['no_surat_jalan'],
                    $item['l_m_d'],
                    number_format($item['gw_kirim'], 2),
                    number_format($item['harga'], 2),
                    $item['nama_cluster'],
                    $poPlus,
                ]
            ], NULL, 'A' . $row);
            $row++;
        }

        // Atur border untuk seluruh tabel
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
        ];
        $sheet->getStyle('A3:V' . ($row - 1))->applyFromArray($styleArray);

        // Set auto width untuk setiap kolom
        foreach (range('A', 'V') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Set isi tabel agar rata tengah
        $sheet->getStyle('A4:V' . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A4:V' . ($row - 1))->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        $writer = new Xlsx($spreadsheet);
        $fileName = 'Report_Datang_Benang_' . date('Y-m-d') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
    public function exportPoBenang()
    {
        $key = $this->request->getGet('key');

        $apiUrl = api_url('material') . 'filterPoBenang?key=' . urlencode($key);
        $material = @file_get_contents($apiUrl);

        // $models = [];
        if ($material !== FALSE) {
            $data = json_decode($material, true);
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Judul
        $sheet->setCellValue('A1', 'Report PO Benang');
        $sheet->mergeCells('A1:P1'); // Menggabungkan sel untuk judul
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Header
        $header = ["No", "Waktu Input", "Tanggal PO", "Foll Up", "No Model", "No Order", "Keterangan", "Buyer", "Delivery Awal", "Delivery Akhir", "Order Type", "Item Type", "Jenis", "Kode Warna", "Warna", "KG Pesan"];
        $sheet->fromArray([$header], NULL, 'A3');

        // Styling Header
        $sheet->getStyle('A3:P3')->getFont()->setBold(true);
        $sheet->getStyle('A3:P3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3:P3')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        // Data
        $row = 4;
        foreach ($data as $index => $item) {
            $sheet->fromArray([
                [
                    $index + 1,
                    $item['tgl_input'],
                    $item['foll_up'],
                    $item['no_model'],
                    $item['no_order'],
                    $item['memo'],
                    $item['buyer'],
                    $item['delivery_awal'],
                    $item['delivery_akhir'],
                    $item['unit'],
                    $item['item_type'],
                    $item['jenis'],
                    $item['kode_warna'],
                    $item['color'],
                    $item['kg_po'],
                ]
            ], NULL, 'A' . $row);
            $row++;
        }

        // Atur border untuk seluruh tabel
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
        ];
        $sheet->getStyle('A3:P' . ($row - 1))->applyFromArray($styleArray);

        // Set auto width untuk setiap kolom
        foreach (range('A', 'P') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Set isi tabel agar rata tengah
        $sheet->getStyle('A4:P' . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A4:P' . ($row - 1))->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        $writer = new Xlsx($spreadsheet);
        $fileName = 'Report_Po_Benang_' . date('Y-m-d') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
    public function exportPengiriman()
    {
        $key = $this->request->getGet('key');
        $tanggalAwal = $this->request->getGet('tanggal_awal');
        $tanggalAkhir = $this->request->getGet('tanggal_akhir');

        $apiUrl = api_url('material') . 'filterPengiriman?key=' . urlencode($key) . '&tanggal_awal=' . urlencode($tanggalAwal) . '&tanggal_akhir=' . urlencode($tanggalAkhir);
        $material = @file_get_contents($apiUrl);

        // $models = [];
        if ($material !== FALSE) {
            $data = json_decode($material, true);
        }

        // Grouping per jenis
        $grouped = [];
        foreach ($data as $item) {
            $jenis = $item['jenis'] ?? 'Undefined';
            $grouped[$jenis][] = $item;
        }

        // Inisialisasi Spreadsheet
        $spreadsheet = new Spreadsheet();

        // Header kolom
        $headers = [
            'NO',
            'TGL PO',
            'FOLL UP',
            'NO MODEL',
            'DELIVERY AWAL',
            'DELIVERY AKHIR',
            'NO ORDER',
            'JENIS',
            'WARNA',
            'KODE BENANG',
            'KGS PESAN',
            'LOSS',
            'QTY PO(+) GBN',
            'QTY STOCK AWAL',
            'LOT AWAL',
            'QTY STOCK OPNAME',
            'LOT OPNAME',
            'AREA',
            'TANGGAL PAKAI',
            'KGS PAKAI',
            'CONES PAKAI',
            'LOT PAKAI',
            'KET GBN PAKAI',
            'ADMIN',
        ];

        // Teks footer
        $footerText = 'FOR-KK-151/TGL_REV_15_03_21/REV_00/HAL_/_';

        $sheetIndex = 0;
        foreach ($grouped as $jenis => $rows) {
            // Pilih atau buat sheet
            if ($sheetIndex === 0) {
                $sheet = $spreadsheet->getActiveSheet();
            } else {
                $sheet = $spreadsheet->createSheet();
            }

            // Setup Page
            $ps = $sheet->getPageSetup();
            $ps->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)
                ->setPaperSize(PageSetup::PAPERSIZE_A4)
                ->setFitToWidth(1)
                ->setFitToHeight(0)
                ->setFitToPage(true);

            // Print area
            // nanti setelah data terisi kita override print area kembali

            // Repeat header row
            $ps->setRowsToRepeatAtTopByStartAndEnd(3, 3);

            // Center saat print
            $ps->setHorizontalCentered(true)
                ->setVerticalCentered(false);

            // Footer
            $hf = $sheet->getHeaderFooter();
            $hf->setOddFooter('&C&"Arial,Bold"' . $footerText);
            $hf->setEvenFooter('&C&"Arial,Bold"' . $footerText);

            // Margins
            $m = $sheet->getPageMargins();
            $m->setTop(0.75)->setBottom(0.75)->setLeft(0.7)->setRight(0.7);

            // Ganti judul sheet (maks 31 char)
            $sheet->setTitle(substr($jenis, 0, 31));

            // Judul di A1:X1
            $sheet->mergeCells('A1:X1');
            $sheet->setCellValue('A1', "DATA PEMAKAIAN AREA {$jenis} {$key}");
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle('A1')
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            // Header kolom di baris 3
            $col = 'A';
            foreach ($headers as $h) {
                $sheet->setCellValue($col . '3', $h);
                $sheet->getStyle($col . '3')->getFont()->setBold(true);
                $col++;
            }

            // Isi data mulai row 4
            $rowNum = 4;
            $no = 1;
            foreach ($rows as $item) {
                $sheet->setCellValue("A{$rowNum}", $no++);
                $sheet->setCellValue("B{$rowNum}", $item['tgl_po'] ?? '-');
                $sheet->setCellValue("C{$rowNum}", $item['foll_up'] ?? '-');
                $sheet->setCellValue("D{$rowNum}", $item['no_model'] ?? '-');
                $sheet->setCellValue("E{$rowNum}", $item['delivery_awal'] ?? '-');
                $sheet->setCellValue("F{$rowNum}", $item['delivery_akhir'] ?? '-');
                $sheet->setCellValue("G{$rowNum}", $item['no_order'] ?? '-');
                $sheet->setCellValue("H{$rowNum}", $item['item_type'] ?? '-');
                $sheet->setCellValue("I{$rowNum}", $item['color'] ?? '-');
                $sheet->setCellValue("J{$rowNum}", $item['kode_warna'] ?? '-');
                $sheet->setCellValue("K{$rowNum}", $item['kgs_pesan'] ?? '0');
                $sheet->setCellValue("L{$rowNum}", $item['loss'] ?? '0');
                $sheet->setCellValue("M{$rowNum}", $item['qty_po_plus'] ?? '0');
                $sheet->setCellValue("N{$rowNum}", $item['kgs_stock_awal'] ?? '0');
                $sheet->setCellValue("O{$rowNum}", $item['lot_awal'] ?? '-');
                $sheet->setCellValue("P{$rowNum}", $item['kgs_in_out'] ?? '0');
                $sheet->setCellValue("Q{$rowNum}", $item['lot_stock'] ?? '-');
                $sheet->setCellValue("R{$rowNum}", $item['area_out'] ?? '-');
                $sheet->setCellValue("S{$rowNum}", $item['tgl_pakai'] ?? '-');
                $sheet->setCellValue("T{$rowNum}", $item['kgs_pakai'] ?? '0');
                $sheet->setCellValue("U{$rowNum}", $item['cones_pakai'] ?? '0');
                $sheet->setCellValue("V{$rowNum}", $item['lot_pakai'] ?? '-');
                $sheet->setCellValue("W{$rowNum}", $item['keterangan_gbn'] ?? '-');
                $sheet->setCellValue("X{$rowNum}", $item['admin'] ?? '-');
                $rowNum++;
            }

            // Tentukan print area sekarang data sudah terisi
            $lastRow = $rowNum - 1;
            $ps->setPrintArea("A1:X{$lastRow}");

            // Border + alignment
            $sheet->getStyle("A3:X{$lastRow}")
                ->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color'       => ['argb' => 'FF000000'],
                        ],
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                ]);

            // Auto–size kolom
            foreach (range('A', 'X') as $c) {
                $sheet->getColumnDimension($c)->setAutoSize(true);
            }

            $sheetIndex++;
        }

        // Kembali ke sheet pertama
        $spreadsheet->setActiveSheetIndex(0);

        // Output file
        $filename = 'PEMAKAIAN_AREA_BENANG.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"{$filename}\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
    public function exportGlobalReport()
    {
        $key = $this->request->getGet('key');
        $jenis = $this->request->getGet('jenis');
        $base  = api_url('material') . '';

        // FUNGSINYA: ambil API sekali panggil
        $getApi = function ($endpoint) use ($base, $key, $jenis) {
            $url  = $base . $endpoint . '?key=' . $key . '&jenis=' . $jenis;
            $resp = @file_get_contents($url);
            return json_decode($resp, true);
        };

        // Semua endpoint lu masuk sini aja
        $data = [
            'material'            => $getApi('filterReportGlobal'),
            'stockAwal'           => $getApi('getDataStockAwal'),
            'datangSolid'         => $getApi('getDataDatangSolid'),
            'datangSolidPlus'     => $getApi('getDataDatangSolidPlus'),
            'gantiRetur'          => $getApi('getDataGantiRetur'),
            'datangLurex'         => $getApi('getDataDatangLurex'),
            'datangLurexPlus'     => $getApi('getDataDatangLurexPlus'),
            'returGbn'            => $getApi('getDataReturGbn'),
            'returArea'           => $getApi('getDataReturArea'),
            'pakaiArea'           => $getApi('getDataPakaiArea'),
            'pakaiLain'           => $getApi('getDataPakaiLain'),
            'returStock'          => $getApi('getDataReturStock'),
            'returTitip'          => $getApi('getDataReturTitip'),
            'dipinjam'            => $getApi('getDataDipinjam'),
            'dipindah'            => $getApi('getDataDipindah'),
        ];

        $delivery = $this->ApsPerstyleModel->getDeliv($key);
        $totalDel  = count($delivery);

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('GLOBAL ALL ' . $key);

        // Judul
        $sheet->mergeCells('A1:AA1');
        $sheet->setCellValue('A1', 'REPORT GLOBAL ' . $key);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Header
        $headers = ['No', 'Buyer', 'No Model', 'Delivery', 'Area', 'Item Type', 'Kode Warna', 'Warna', 'Loss', 'Qty PO', 'Qty PO(+)', 'Stock Awal', 'Stock Opname', 'Datang Solid', '(+) Datang Solid', 'Ganti Retur', 'Datang Lurex', '(+)Datang Lurex', 'Datang PB GBN', 'Retur PB Area', 'Pakai Area', 'Pakai Lain-Lain', 'Retur Stock', 'Retur Titip', 'Dipinjam', 'Pindah Order', 'Pindah Ke Stock Mati', 'Stock Akhir', 'Tagihan GBN', 'Jatah Area'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '3', $header);
            $sheet->getStyle($col . '3')->getFont()->setBold(true);
            $col++;
        }

        // Data
        $row = 4;
        $no = 1;
        $delIndex = 0;
        foreach ($data['material'] as $item) {
            // Format setiap nilai untuk memastikan nilai 0 dan angka dengan dua desimal
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $item['buyer'] ?: '-'); // BUYER
            $sheet->setCellValue('C' . $row, $item['no_model'] ?: '-'); // no model
            if ($delIndex < $totalDel) {
                $sheet->setCellValue('D' . $row, $delivery[$delIndex]['delivery']);
                $delIndex++;
            } else {
                $sheet->setCellValue('D' . $row, '');  // atau '-' sesuai preferensi
            }
            $sheet->setCellValue('E' . $row, $item['area'] ?: '-');
            $sheet->setCellValue('F' . $row, $item['item_type'] ?: '-'); // item type
            $sheet->setCellValue('G' . $row, $item['kode_warna'] ?: '-'); //kode warna
            $sheet->setCellValue('H' . $row, $item['color'] ?: '-'); // color
            $sheet->setCellValue('I' . $row, isset($item['loss']) ? number_format($item['loss'], 2, '.', '') : 0); // loss
            $sheet->setCellValue('J' . $row, isset($item['kgs']) ? number_format($item['kgs'], 2, '.', '') : 0); // qty po
            $sheet->setCellValue('K' . $row, isset($item['qty_poplus']) ? number_format($item['qty_poplus'], 2, '.', '') : 0); // qty po (+)
            $sheet->setCellValue('L' . $row, isset($item['stock_awal']) ? number_format($item['stock_awal'], 2, '.', '') : 0); // stock awal
            $sheet->setCellValue('M' . $row, '-'); // stock opname
            $sheet->setCellValue('N' . $row, isset($item['datang_solid']) ? number_format($item['datang_solid'], 2, '.', '') : 0); // datan solid
            $sheet->setCellValue('O' . $row, isset($item['plus_datang_solid']) ? number_format($item['plus_datang_solid'], 2, '.', '') : 0); // (+) datang solid
            $sheet->setCellValue('P' . $row, isset($item['ganti_retur']) ? number_format($item['ganti_retur'], 2, '.', '') : 0); // ganti retur
            $sheet->setCellValue('Q' . $row, isset($item['datang_lurex']) ? number_format($item['datang_lurex'], 2, '.', '') : 0); // datang lurex
            $sheet->setCellValue('R' . $row, isset($item['plud_datang_lurex']) ? number_format($item['plud_datang_lurex'], 2, '.', '') : 0); // (+) datang lurex
            $sheet->setCellValue('S' . $row, isset($item['retur_pb_gbn']) ? number_format($item['retur_pb_gbn'], 2, '.', '') : 0); // retur pb gbn
            $sheet->setCellValue('T' . $row, isset($item['retur_pb_area']) ? number_format($item['retur_pb_area'], 2, '.', '') : 0); // retur bp area
            $sheet->setCellValue('U' . $row, isset($item['pakai_area']) ? number_format($item['pakai_area'], 2, '.', '') : 0); // pakai area
            $sheet->setCellValue('V' . $row, isset($item['kgs_other_out']) ? number_format($item['kgs_other_out'], 2, '.', '') : 0); // pakai lain-lain
            $sheet->setCellValue('W' . $row, '-'); // retur stock
            $sheet->setCellValue('X' . $row, '-'); // retur titip
            $sheet->setCellValue('Y' . $row, '-'); // dipinjam
            $sheet->setCellValue('Z' . $row, '-'); // pindah order
            $sheet->setCellValue('AA' . $row, '-'); // pindah ke stock mati
            $sheet->setCellValue('AB' . $row, isset($item['kgs_in_out']) ? number_format($item['kgs_in_out'], 2, '.', '') : 0); // stock akhir

            // Tagihan GBN dan Jatah Area perhitungan
            $tagihanGbn = isset($item['kgs']) ? $item['kgs'] + $item['qty_poplus'] - ($item['datang_solid'] + $item['plus_datang_solid'] + $item['stock_awal']) : 0;
            $jatahArea = isset($item['kgs']) ? $item['kgs'] + $item['qty_poplus'] - $item['pakai_area'] : 0;

            // Format Tagihan GBN dan Jatah Area
            $sheet->setCellValue('AC' . $row, number_format($tagihanGbn, 2, '.', '')); // tagihan gbn
            $sheet->setCellValue('AD' . $row, number_format($jatahArea, 2, '.', '')); // jatah area
            $row++;
        }

        // Border
        $lastRow = $row - 1;
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];
        $sheet->getStyle("A3:AD{$lastRow}")->applyFromArray($styleArray);

        // Auto-size
        foreach (range('A', 'AD') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Tambahkan sheet kosong lainnya
        $sheetNames = [
            'STOCK AWAL ' . $key,
            'DATANG SOLID ' . $key,
            '(+) DATANG SOLID ' . $key,
            'GANTI RETUR ' . $key,
            'DATANG LUREX ' . $key,
            '(+) DATANG LUREX ' . $key,
            'RETUR PERBAIKAN GBN ' . $key,
            'RETUR PERBAIKAN AREA ' . $key,
            'PAKAI AREA ' . $key,
            'PAKAI LAIN-LAIN ' . $key,
            'RETUR STOCK ' . $key,
            'RETUR TITIP ' . $key,
            'ORDER ' . $key . ' DIPINJAM',
            'PINDAH ORDER ' . $key
        ];

        foreach ($sheetNames as $name) {
            $newSheet = $spreadsheet->createSheet();
            $newSheet->setTitle($name);

            // Hanya atur judul dan header jika nama sheet mengandung 'STOCK AWAL'
            if ($name === 'STOCK AWAL ' . $key) {
                $newSheet->mergeCells('A1:K1');
                $newSheet->setCellValue('A1', 'REPORT HISTORY PINDAH ORDER KE ' . $key);
                $newSheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $newSheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Header
                $headerStockAwal = ['NO', 'NO MODEL', 'DELIVERY', 'ITEM TYPE', 'KODE WARNA', 'WARNA', 'QTY', 'CONES', 'LOT', 'CLUSTER', 'KETERANGAN'];
                $col = 'A';
                foreach ($headerStockAwal as $header) {
                    $newSheet->setCellValue($col . '3', $header);
                    $newSheet->getStyle($col . '3')->getFont()->setBold(true);
                    $col++;
                }
                $newSheet->getStyle('A3:K3')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);

                // Data
                $row = 4;
                $no = 1;
                foreach ($data['stockAwal'] as $item) {
                    $newSheet->setCellValue('A' . $row, $no++);
                    $newSheet->setCellValue('B' . $row, $item['no_model_old'] ?: '-');
                    $newSheet->setCellValue('C' . $row, '-');
                    $newSheet->setCellValue('D' . $row, $item['item_type'] ?: '-');
                    $newSheet->setCellValue('E' . $row, $item['kode_warna'] ?: '-');
                    $newSheet->setCellValue('F' . $row, $item['warna'] ?: '-');
                    $newSheet->setCellValue('G' . $row, isset($item['kgs']) ? number_format($item['kgs'], 2, '.', '') : 0);
                    $newSheet->setCellValue('H' . $row, isset($item['cns']) ? number_format($item['cns'], 2, '.', '') : 0);
                    $newSheet->setCellValue('I' . $row, $item['lot'] ?: '-');
                    $newSheet->setCellValue('J' . $row, $item['nama_cluster'] ?: '-');
                    $keterangan = $item['tgl_pindah'] . ' ' . $item['keterangan'] . ' ke ' . $item['no_model_new'] . ' kode ' . $item['kode_warna'] . ' (' . $item['admin'] . ')';
                    $newSheet->setCellValue('K' . $row, $keterangan ?: '-');
                    $row++;
                }

                $lastRow = $row - 1;
                $newSheet->getStyle("A3:K{$lastRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ]);

                foreach (range('A', 'Z') as $col) {
                    $newSheet->getColumnDimension($col)->setAutoSize(true);
                }

                $footerRow = $lastRow + 2;
                $newSheet->mergeCells("A{$footerRow}:K{$footerRow}");
                $newSheet->setCellValue("A{$footerRow}", 'REPORT HISTORY PINDAH ORDER');
                $newSheet->getStyle("A{$footerRow}")->getFont()->setBold(true)->setSize(10);
                $newSheet->getStyle("A{$footerRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }

            // Hanya atur judul dan header jika nama sheet mengandung 'DATANG SOLID'
            if (strpos($name, 'DATANG SOLID') !== false) {
                // Judul
                $newSheet->mergeCells('A1:O1');
                $newSheet->setCellValue('A1', 'REPORT DATANG SOLID ' . $key);
                $newSheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $newSheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Header
                $headerStockAwal = ['No', 'No Model', 'Item Type', 'Kode Warna', 'Warna', 'Tgl Datang', 'Nama Cluster', 'Qty Datang', 'Cones Datang', 'Lot Datang', 'Tgl Penerimaan', 'No SJ', 'L/M/D', 'Ket Datang', 'Admin'];
                $col = 'A';
                foreach ($headerStockAwal as $header) {
                    $newSheet->setCellValue($col . '3', $header);
                    $newSheet->getStyle($col . '3')->getFont()->setBold(true);
                    $col++;
                }

                // Tambahkan border untuk header A3:K3
                $newSheet->getStyle('A3:O3')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);

                $row = 4;
                $no = 1;
                foreach ($data['datangSolid'] as $item) {
                    $newSheet->setCellValue('A' . $row, $no++);
                    $newSheet->setCellValue('B' . $row, $item['no_model'] ?: '-');
                    $newSheet->setCellValue('C' . $row, $item['item_type'] ?: '-');
                    $newSheet->setCellValue('D' . $row, $item['kode_warna'] ?: '-');
                    $newSheet->setCellValue('E' . $row, $item['warna'] ?: '-');
                    $newSheet->setCellValue('F' . $row, $item['tgl_datang']);
                    $newSheet->setCellValue('G' . $row, $item['nama_cluster']);
                    $newSheet->setCellValue('H' . $row, isset($item['qty_datang']) ? number_format($item['qty_datang'], 2, '.', '') : 0);
                    $newSheet->setCellValue('I' . $row, isset($item['cns_datang']) ? number_format($item['cns_datang'], 2, '.', '') : 0);
                    $newSheet->setCellValue('J' . $row, $item['lot_datang'] ?: '-');
                    $newSheet->setCellValue('K' . $row, $item['tgl_terima'] ?: '-');
                    $newSheet->setCellValue('L' . $row, $item['no_surat_jalan'] ?: '-');
                    $newSheet->setCellValue('M' . $row, $item['l_m_d'] ?: '-');
                    $newSheet->setCellValue('N' . $row, $item['keterangan']);
                    $newSheet->setCellValue('O' . $row, $item['admin'] ?: '-');
                    $row++;
                }

                $lastRow = $row - 1;
                $newSheet->getStyle("A3:O{$lastRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ]);

                foreach (range('A', 'Z') as $col) {
                    $newSheet->getColumnDimension($col)->setAutoSize(true);
                }

                $footerDatangSolid = $lastRow + 2;
                $newSheet->mergeCells("A{$footerDatangSolid}:O{$footerDatangSolid}");
                $newSheet->setCellValue("A{$footerDatangSolid}", 'REPORT DATANG SOLID');
                $newSheet->getStyle("A{$footerDatangSolid}")->getFont()->setBold(true)->setSize(10);
                $newSheet->getStyle("A{$footerDatangSolid}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }

            // Hanya atur judul dan header jika nama sheet mengandung '(+) DATANG SOLID'
            if (strpos($name, '(+) DATANG SOLID') !== false) {
                // Judul
                $newSheet->mergeCells('A1:P1');
                $newSheet->setCellValue('A1', 'REPORT TAMBAHAN DATANG SOLID ' . $key);
                $newSheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $newSheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Header
                $headerStockAwal = ['No', 'No Model', 'Item Type', 'Kode Warna', 'Warna', 'PO (+)', 'Tgl Datang', 'Nama Cluster', 'Qty Datang', 'Cones Datang', 'Lot Datang', 'Tgl Penerimaan', 'No SJ', 'L/M/D', 'Ket Datang', 'Admin'];
                $col = 'A';
                foreach ($headerStockAwal as $header) {
                    $newSheet->setCellValue($col . '3', $header);
                    $newSheet->getStyle($col . '3')->getFont()->setBold(true);
                    $col++;
                }

                // Tambahkan border untuk header A3:K3
                $newSheet->getStyle('A3:P3')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);

                $row = 4;
                $no = 1;
                foreach ($data['datangSolidPlus'] as $item) {
                    $newSheet->setCellValue('A' . $row, $no++);
                    $newSheet->setCellValue('B' . $row, $item['no_model'] ?: '-');
                    $newSheet->setCellValue('C' . $row, $item['item_type'] ?: '-');
                    $newSheet->setCellValue('D' . $row, $item['kode_warna'] ?: '-');
                    $newSheet->setCellValue('E' . $row, $item['warna'] ?: '-');
                    $newSheet->setCellValue('F' . $row, $item['qty_poplus']);
                    $newSheet->setCellValue('G' . $row, $item['tgl_datang'] ?: '-');
                    $newSheet->setCellValue('H' . $row, $item['nama_cluster']);
                    $newSheet->setCellValue('I' . $row, isset($item['qty_datang']) ? number_format($item['qty_datang'], 2, '.', '') : 0);
                    $newSheet->setCellValue('J' . $row, isset($item['cns_datang']) ? number_format($item['cns_datang'], 2, '.', '') : 0);
                    $newSheet->setCellValue('K' . $row, $item['lot_datang'] ?: '-');
                    $newSheet->setCellValue('L' . $row, $item['tgl_terima'] ?: '-');
                    $newSheet->setCellValue('M' . $row, $item['no_surat_jalan'] ?: '-');
                    $newSheet->setCellValue('N' . $row, $item['l_m_d'] ?: '-');
                    $newSheet->setCellValue('O' . $row, $item['keterangan'] ?: '-');
                    $newSheet->setCellValue('P' . $row, $item['admin'] ?: '-');
                    $row++;
                }

                $lastRow = $row - 1;
                $newSheet->getStyle("A3:P{$lastRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ]);

                foreach (range('A', 'Z') as $col) {
                    $newSheet->getColumnDimension($col)->setAutoSize(true);
                }

                $footerPlusDatangSolid = $lastRow + 2;
                $newSheet->mergeCells("A{$footerPlusDatangSolid}:P{$footerPlusDatangSolid}");
                $newSheet->setCellValue("A{$footerPlusDatangSolid}", 'REPORT (+)DATANG SOLID');
                $newSheet->getStyle("A{$footerPlusDatangSolid}")->getFont()->setBold(true)->setSize(10);
                $newSheet->getStyle("A{$footerPlusDatangSolid}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }

            // Hanya atur judul dan header jika nama sheet mengandung 'GANTI RETUR'
            if (strpos($name, 'GANTI RETUR') !== false) {
                // Judul
                $newSheet->mergeCells('A1:Q1');
                $newSheet->setCellValue('A1', 'REPORT DATANG GANTI RETUR ' . $key);
                $newSheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $newSheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Header
                $headerStockAwal = ['No', 'No Model', 'Item Type', 'Kode Warna', 'Warna', 'PO (+)', 'Tgl Datang', 'Nama Cluster', 'Qty Datang', 'Cones Datang', 'Lot Datang', 'Tgl Penerimaan', 'No SJ', 'L/M/D', 'Ket Datang', 'Admin', 'Ganti Retur'];
                $col = 'A';
                foreach ($headerStockAwal as $header) {
                    $newSheet->setCellValue($col . '3', $header);
                    $newSheet->getStyle($col . '3')->getFont()->setBold(true);
                    $col++;
                }

                // Tambahkan border untuk header A3:K3
                $newSheet->getStyle('A3:Q3')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);

                $row = 4;
                $no = 1;
                foreach ($data['gantiRetur'] as $item) {
                    $newSheet->setCellValue('A' . $row, $no++);
                    $newSheet->setCellValue('B' . $row, $item['no_model'] ?: '-');
                    $newSheet->setCellValue('C' . $row, $item['item_type'] ?: '-');
                    $newSheet->setCellValue('D' . $row, $item['kode_warna'] ?: '-');
                    $newSheet->setCellValue('E' . $row, $item['warna'] ?: '-');
                    $newSheet->setCellValue('F' . $row, $item['qty_poplus']);
                    $newSheet->setCellValue('G' . $row, $item['tgl_datang'] ?: '-');
                    $newSheet->setCellValue('H' . $row, $item['nama_cluster']);
                    $newSheet->setCellValue('I' . $row, isset($item['qty_datang']) ? number_format($item['qty_datang'], 2, '.', '') : 0);
                    $newSheet->setCellValue('J' . $row, isset($item['cns_datang']) ? number_format($item['cns_datang'], 2, '.', '') : 0);
                    $newSheet->setCellValue('K' . $row, $item['lot_datang'] ?: '-');
                    $newSheet->setCellValue('L' . $row, $item['tgl_terima'] ?: '-');
                    $newSheet->setCellValue('M' . $row, $item['no_surat_jalan'] ?: '-');
                    $newSheet->setCellValue('N' . $row, $item['l_m_d'] ?: '-');
                    $newSheet->setCellValue('O' . $row, $item['keterangan'] ?: '-');
                    $newSheet->setCellValue('P' . $row, $item['admin'] ?: '-');
                    $newSheet->setCellValue('Q' . $row, 'GANTI RETUR');
                    $row++;
                }

                $lastRow = $row - 1;
                $newSheet->getStyle("A3:Q{$lastRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ]);

                foreach (range('A', 'Z') as $col) {
                    $newSheet->getColumnDimension($col)->setAutoSize(true);
                }

                $footerRow = $lastRow + 2;
                $newSheet->mergeCells("A{$footerRow}:P{$footerRow}");
                $newSheet->setCellValue("A{$footerRow}", 'REPORT GANTI RETUR');
                $newSheet->getStyle("A{$footerRow}")->getFont()->setBold(true)->setSize(10);
                $newSheet->getStyle("A{$footerRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }

            // Hanya atur judul dan header jika nama sheet mengandung 'DATANG LUREX'
            if (strpos($name, 'DATANG LUREX') !== false) {
                // Judul
                $newSheet->mergeCells('A1:O1');
                $newSheet->setCellValue('A1', 'REPORT DATANG LUREX ' . $key);
                $newSheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $newSheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Header
                $headerStockAwal = ['No', 'No Model', 'Item Type', 'Kode Warna', 'Warna', 'Tgl Datang', 'Nama Cluster', 'Qty Datang', 'Cones Datang', 'Lot Datang', 'Tgl Penerimaan', 'No SJ', 'L/M/D', 'Ket Datang', 'Admin'];
                $col = 'A';
                foreach ($headerStockAwal as $header) {
                    $newSheet->setCellValue($col . '3', $header);
                    $newSheet->getStyle($col . '3')->getFont()->setBold(true);
                    $col++;
                }

                // Tambahkan border untuk header A3:K3
                $newSheet->getStyle('A3:O3')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);

                $row = 4;
                $no = 1;
                foreach ($data['datangLurex'] as $item) {
                    $newSheet->setCellValue('A' . $row, $no++);
                    $newSheet->setCellValue('B' . $row, $item['no_model'] ?: '-');
                    $newSheet->setCellValue('C' . $row, $item['item_type'] ?: '-');
                    $newSheet->setCellValue('D' . $row, $item['kode_warna'] ?: '-');
                    $newSheet->setCellValue('E' . $row, $item['warna'] ?: '-');
                    $newSheet->setCellValue('F' . $row, $item['tgl_datang']);
                    $newSheet->setCellValue('G' . $row, $item['nama_cluster']);
                    $newSheet->setCellValue('H' . $row, isset($item['qty_datang']) ? number_format($item['qty_datang'], 2, '.', '') : 0);
                    $newSheet->setCellValue('I' . $row, isset($item['cns_datang']) ? number_format($item['cns_datang'], 2, '.', '') : 0);
                    $newSheet->setCellValue('J' . $row, $item['lot_datang'] ?: '-');
                    $newSheet->setCellValue('K' . $row, $item['tgl_terima'] ?: '-');
                    $newSheet->setCellValue('L' . $row, $item['no_surat_jalan'] ?: '-');
                    $newSheet->setCellValue('M' . $row, $item['l_m_d'] ?: '-');
                    $newSheet->setCellValue('N' . $row, $item['keterangan']);
                    $newSheet->setCellValue('O' . $row, $item['admin'] ?: '-');
                    $row++;
                }

                $lastRow = $row - 1;
                $newSheet->getStyle("A3:O{$lastRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ]);

                foreach (range('A', 'Z') as $col) {
                    $newSheet->getColumnDimension($col)->setAutoSize(true);
                }

                $footerRow = $lastRow + 2;
                $newSheet->mergeCells("A{$footerRow}:O{$footerRow}");
                $newSheet->setCellValue("A{$footerRow}", 'REPORT DATANG LUREX');
                $newSheet->getStyle("A{$footerRow}")->getFont()->setBold(true)->setSize(10);
                $newSheet->getStyle("A{$footerRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }

            // Hanya atur judul dan header jika nama sheet mengandung '(+) DATANG LUREX'
            if (strpos($name, '(+) DATANG LUREX') !== false) {
                // Judul
                $newSheet->mergeCells('A1:P1');
                $newSheet->setCellValue('A1', 'REPORT TAMBAHAN DATANG LUREX ' . $key);
                $newSheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $newSheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Header
                $headerStockAwal = ['No', 'No Model', 'Item Type', 'Kode Warna', 'Warna', 'PO (+)', 'Tgl Datang', 'Nama Cluster', 'Qty Datang', 'Cones Datang', 'Lot Datang', 'Tgl Penerimaan', 'No SJ', 'L/M/D', 'Ket Datang', 'Admin'];
                $col = 'A';
                foreach ($headerStockAwal as $header) {
                    $newSheet->setCellValue($col . '3', $header);
                    $newSheet->getStyle($col . '3')->getFont()->setBold(true);
                    $col++;
                }

                // Tambahkan border untuk header A3:K3
                $newSheet->getStyle('A3:P3')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);

                $row = 4;
                $no = 1;
                foreach ($data['datangLurexPlus'] as $item) {
                    $newSheet->setCellValue('A' . $row, $no++);
                    $newSheet->setCellValue('B' . $row, $item['no_model'] ?: '-');
                    $newSheet->setCellValue('C' . $row, $item['item_type'] ?: '-');
                    $newSheet->setCellValue('D' . $row, $item['kode_warna'] ?: '-');
                    $newSheet->setCellValue('E' . $row, $item['warna'] ?: '-');
                    $newSheet->setCellValue('F' . $row, $item['qty_poplus']);
                    $newSheet->setCellValue('G' . $row, $item['tgl_datang'] ?: '-');
                    $newSheet->setCellValue('H' . $row, $item['nama_cluster']);
                    $newSheet->setCellValue('I' . $row, isset($item['qty_datang']) ? number_format($item['qty_datang'], 2, '.', '') : 0);
                    $newSheet->setCellValue('J' . $row, isset($item['cns_datang']) ? number_format($item['cns_datang'], 2, '.', '') : 0);
                    $newSheet->setCellValue('K' . $row, $item['lot_datang'] ?: '-');
                    $newSheet->setCellValue('L' . $row, $item['tgl_terima'] ?: '-');
                    $newSheet->setCellValue('M' . $row, $item['no_surat_jalan'] ?: '-');
                    $newSheet->setCellValue('N' . $row, $item['l_m_d'] ?: '-');
                    $newSheet->setCellValue('O' . $row, $item['keterangan'] ?: '-');
                    $newSheet->setCellValue('P' . $row, $item['admin'] ?: '-');
                    $row++;
                }

                $lastRow = $row - 1;
                $newSheet->getStyle("A3:P{$lastRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ]);

                foreach (range('A', 'Z') as $col) {
                    $newSheet->getColumnDimension($col)->setAutoSize(true);
                }

                $footerRow = $lastRow + 2;
                $newSheet->mergeCells("A{$footerRow}:P{$footerRow}");
                $newSheet->setCellValue("A{$footerRow}", 'REPORT (+) DATANG LUREX');
                $newSheet->getStyle("A{$footerRow}")->getFont()->setBold(true)->setSize(10);
                $newSheet->getStyle("A{$footerRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }

            // Hanya atur judul dan header jika nama sheet mengandung 'RETUR PERBAIKAN GBN'
            if (strpos($name, 'RETUR PERBAIKAN GBN') !== false) {
                // Judul
                $newSheet->mergeCells('A1:P1');
                $newSheet->setCellValue('A1', 'REPORT RETUR PERBAIKAN GBN ' . $key);
                $newSheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $newSheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Header
                $headerStockAwal = ['No', 'No Model', 'Item Type', 'Kode Warna', 'Warna', 'Area', 'Tgl Retur', 'Nama Cluster', 'Qty Retur', 'Cones Retur', 'Krg / Pack Retur', 'Lot Retur', 'Kategori', 'Ket Area', 'Ket GBN', 'Note'];
                $col = 'A';
                foreach ($headerStockAwal as $header) {
                    $newSheet->setCellValue($col . '3', $header);
                    $newSheet->getStyle($col . '3')->getFont()->setBold(true);
                    $col++;
                }

                // Tambahkan border untuk header A3:K3
                $newSheet->getStyle('A3:P3')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);

                $row = 4;
                $no = 1;
                foreach ($data['returGbn'] as $item) {
                    // dd($data['returGbn']);
                    $newSheet->setCellValue('A' . $row, $no++);
                    $newSheet->setCellValue('B' . $row, $item['no_model'] ?? '-');
                    $newSheet->setCellValue('C' . $row, $item['item_type'] ?? '-');
                    $newSheet->setCellValue('D' . $row, $item['kode_warna'] ?? '-');
                    $newSheet->setCellValue('E' . $row, $item['warna'] ?? '-');
                    $newSheet->setCellValue('F' . $row, 'GUDANG BENANG');
                    $newSheet->setCellValue('G' . $row, $item['tgl_retur'] ?? '-');
                    $newSheet->setCellValue('H' . $row, $item['cluster_old']);
                    $newSheet->setCellValue('I' . $row, isset($item['kgs']) ? number_format($item['kgs'], 2, '.', '') : 0);
                    $newSheet->setCellValue('J' . $row, $item['cns'] ?? 0);
                    $newSheet->setCellValue('K' . $row, $item['krg'] ?? 0);
                    $newSheet->setCellValue('L' . $row, $item['lot'] ?? '-');
                    $newSheet->setCellValue('M' . $row, $item['nama_kategori'] ?? '-');
                    $newSheet->setCellValue('N' . $row, $item['keterangan_area'] ?? '-');
                    $newSheet->setCellValue('O' . $row, $item['keterangan_gbn'] ?? '-');
                    $row++;
                }

                $lastRow = $row - 1;
                $newSheet->getStyle("A3:O{$lastRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ]);

                foreach (range('A', 'Z') as $col) {
                    $newSheet->getColumnDimension($col)->setAutoSize(true);
                }

                $footerRow = $lastRow + 2;
                $newSheet->mergeCells("A{$footerRow}:O{$footerRow}");
                $newSheet->setCellValue("A{$footerRow}", 'REPORT RETUR PERBAIKAN GBN');
                $newSheet->getStyle("A{$footerRow}")->getFont()->setBold(true)->setSize(10);
                $newSheet->getStyle("A{$footerRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }

            if ($name === 'RETUR PERBAIKAN AREA ' . $key) {
                // Judul
                $newSheet->mergeCells('A1:O1');
                $newSheet->setCellValue('A1', 'REPORT RETUR PERBAIKAN AREA ' . $key);
                $newSheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $newSheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Header
                $headerReturArea = ['NO', 'NO MODEL', 'ITEM TYPE', 'KODE WARNA', 'WARNA', 'AREA', 'TGL RETUR', 'NAMA CLUSTER', 'QTY RETUR', 'CONES RETUR', 'KRG / PACK RETUR', 'LOT RETUR', 'KATEGORI', 'KET AREA', 'KET GBN'];
                $col = 'A';
                foreach ($headerReturArea as $header) {
                    $newSheet->setCellValue($col . '3', $header);
                    $newSheet->getStyle($col . '3')->getFont()->setBold(true);
                    $col++;
                }

                $newSheet->getStyle('A3:O3')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);

                $row = 4;
                $no = 1;
                foreach ($data['returArea'] as $item) {
                    $newSheet->setCellValue('A' . $row, $no++);
                    $newSheet->setCellValue('B' . $row, $item['no_model'] ?: '-');
                    $newSheet->setCellValue('C' . $row, $item['item_type'] ?: '-');
                    $newSheet->setCellValue('D' . $row, $item['kode_warna'] ?: '-');
                    $newSheet->setCellValue('E' . $row, $item['warna'] ?: '-');
                    $newSheet->setCellValue('F' . $row, $item['area_retur']);
                    $newSheet->setCellValue('G' . $row, $item['tgl_retur'] ?: '-');
                    $newSheet->setCellValue('H' . $row, isset($item['nama_cluster']) ? $item['nama_cluster'] : '-');
                    $newSheet->setCellValue('I' . $row, isset($item['kgs_retur']) ? number_format($item['kgs_retur'], 2, '.', '') : 0);
                    $newSheet->setCellValue('J' . $row, $item['cns_retur'] ?: 0);
                    $newSheet->setCellValue('K' . $row, $item['krg_retur'] ?: 0);
                    $newSheet->setCellValue('L' . $row, $item['lot_retur'] ?: '-');
                    $newSheet->setCellValue('M' . $row, $item['kategori'] ?: '-');
                    $newSheet->setCellValue('N' . $row, $item['keterangan_area'] ?: '-');
                    $newSheet->setCellValue('O' . $row, $item['keterangan_gbn'] ?: '-');
                    $row++;
                }

                $lastRow = $row - 1;
                $newSheet->getStyle("A3:O{$lastRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ]);

                foreach (range('A', 'Z') as $col) {
                    $newSheet->getColumnDimension($col)->setAutoSize(true);
                }

                $footerRow = $lastRow + 2;
                $newSheet->mergeCells("A{$footerRow}:O{$footerRow}");
                $newSheet->setCellValue("A{$footerRow}", 'REPORT RETUR PERBAIKAN AREA');
                $newSheet->getStyle("A{$footerRow}")->getFont()->setBold(true)->setSize(10);
                $newSheet->getStyle("A{$footerRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }

            if ($name === 'PAKAI AREA ' . $key) {
                // Judul
                $newSheet->mergeCells('A1:N1');
                $newSheet->setCellValue('A1', 'REPORT ORDER PAKAI AREA ' . $key);
                $newSheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $newSheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Header
                $headerPakaiArea = ['NO', 'NO MODEL', 'ITEM TYPE', 'KODE WARNA', 'WARNA', 'AREA', 'TGL PAKAI', 'TAMBAHAN', 'NAMA CLUSTER', 'QTY PAKAI', 'CONES PAKAI', 'LOT PAKAI', 'KET GBN', 'ADMIN'];
                $col = 'A';
                foreach ($headerPakaiArea as $header) {
                    $newSheet->setCellValue($col . '3', $header);
                    $newSheet->getStyle($col . '3')->getFont()->setBold(true);
                    $col++;
                }

                $newSheet->getStyle('A3:N3')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);

                $row = 4;
                $no = 1;
                foreach ($data['pakaiArea'] as $item) {
                    $newSheet->setCellValue('A' . $row, $no++);
                    $newSheet->setCellValue('B' . $row, $item['no_model'] ?: '-');
                    $newSheet->setCellValue('C' . $row, $item['item_type'] ?: '-');
                    $newSheet->setCellValue('D' . $row, $item['kode_warna'] ?: '-');
                    $newSheet->setCellValue('E' . $row, $item['warna'] ?: '-');
                    $newSheet->setCellValue('F' . $row, $item['area_out']);
                    $newSheet->setCellValue('G' . $row, $item['tgl_out'] ?: '-');
                    $newSheet->setCellValue('H' . $row, $item['po_tambahan'] == '1' ? 'YA' : '');
                    $newSheet->setCellValue('I' . $row, $item['nama_cluster']);
                    $newSheet->setCellValue('J' . $row, isset($item['kgs_out']) ? number_format($item['kgs_out'], 2, '.', '') : 0);
                    $newSheet->setCellValue('K' . $row, $item['cns_out'] ?: 0);
                    $newSheet->setCellValue('L' . $row, $item['krg_out'] ?: 0);
                    $newSheet->setCellValue('M' . $row, $item['lot_out'] ?: '-');
                    $newSheet->setCellValue('N' . $row, $item['admin'] ?: '-');
                    $row++;
                }

                $lastRow = $row - 1;
                $newSheet->getStyle("A3:N{$lastRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ]);

                foreach (range('A', 'Z') as $col) {
                    $newSheet->getColumnDimension($col)->setAutoSize(true);
                }

                $footerRow = $lastRow + 2;
                $newSheet->mergeCells("A{$footerRow}:N{$footerRow}");
                $newSheet->setCellValue("A{$footerRow}", 'REPORT ORDER PAKAI AREA');
                $newSheet->getStyle("A{$footerRow}")->getFont()->setBold(true)->setSize(10);
                $newSheet->getStyle("A{$footerRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }

            if ($name === 'PAKAI LAIN-LAIN ' . $key) {
                // Judul
                $newSheet->mergeCells('A1:N1');
                $newSheet->setCellValue('A1', 'REPORT ORDER PAKAI LAIN-LAIN ' . $key);
                $newSheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $newSheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Header
                $headerPakaiLain = ['NO', 'NO MODEL', 'ITEM TYPE', 'KODE WARNA', 'WARNA', 'AREA', 'TGL PAKAI', 'TAMBAHAN', 'NAMA CLUSTER', 'QTY PAKAI', 'CONES PAKAI', 'LOT PAKAI', 'KET GBN', 'ADMIN'];
                $col = 'A';
                foreach ($headerPakaiLain as $header) {
                    $newSheet->setCellValue($col . '3', $header);
                    $newSheet->getStyle($col . '3')->getFont()->setBold(true);
                    $col++;
                }

                $newSheet->getStyle('A3:N3')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);

                $row = 4;
                $no = 1;
                foreach ($data['pakaiLain'] as $item) {
                    $newSheet->setCellValue('A' . $row, $no++);
                    $newSheet->setCellValue('B' . $row, $item['no_model'] ?: '-');
                    $newSheet->setCellValue('C' . $row, $item['item_type'] ?: '-');
                    $newSheet->setCellValue('D' . $row, $item['kode_warna'] ?: '-');
                    $newSheet->setCellValue('E' . $row, $item['warna'] ?: '-');
                    $newSheet->setCellValue('F' . $row, '-');
                    $newSheet->setCellValue('G' . $row, $item['tgl_other_out'] ?: '-');
                    $newSheet->setCellValue('H' . $row, $item['po_plus'] == '1' ? 'YA' : '');
                    $newSheet->setCellValue('I' . $row, $item['nama_cluster']);
                    $newSheet->setCellValue('J' . $row, isset($item['kgs_other_out']) ? number_format($item['kgs_other_out'], 2, '.', '') : 0);
                    $newSheet->setCellValue('K' . $row, $item['cns_other_out'] ?: 0);
                    $newSheet->setCellValue('L' . $row, $item['krg_other_out'] ?: 0);
                    $newSheet->setCellValue('M' . $row, $item['lot_other_out'] ?: '-');
                    $newSheet->setCellValue('N' . $row, $item['admin'] ?: '-');
                    $row++;
                }

                $lastRow = $row - 1;
                $newSheet->getStyle("A3:N{$lastRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ]);

                foreach (range('A', 'Z') as $col) {
                    $newSheet->getColumnDimension($col)->setAutoSize(true);
                }

                $footerRow = $lastRow + 2;
                $newSheet->mergeCells("A{$footerRow}:N{$footerRow}");
                $newSheet->setCellValue("A{$footerRow}", 'REPORT ORDER PAKAI LAIN-LAIN');
                $newSheet->getStyle("A{$footerRow}")->getFont()->setBold(true)->setSize(10);
                $newSheet->getStyle("A{$footerRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }

            if ($name === 'RETUR STOCK ' . $key) {
                // Judul
                $newSheet->mergeCells('A1:O1');
                $newSheet->setCellValue('A1', 'REPORT RETUR STOCK ' . $key);
                $newSheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $newSheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Header
                $headerReturStock = ['NO', 'NO MODEL', 'ITEM TYPE', 'KODE WARNA', 'WARNA', 'AREA', 'TGL RETUR', 'NAMA CLUSTER', 'QTY RETUR', 'CONES RETUR', 'KRG/PACK RETUR', 'LOT RETUR', 'KATEGORI', 'KET AREA', 'KET GBN'];
                $col = 'A';
                foreach ($headerReturStock as $header) {
                    $newSheet->setCellValue($col . '3', $header);
                    $newSheet->getStyle($col . '3')->getFont()->setBold(true);
                    $col++;
                }

                $newSheet->getStyle('A3:O3')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);

                $row = 4;
                $no = 1;
                foreach ($data['returStock'] as $item) {
                    $newSheet->setCellValue('A' . $row, $no++);
                    $newSheet->setCellValue('B' . $row, $item['no_model'] ?: '-');
                    $newSheet->setCellValue('C' . $row, $item['item_type'] ?: '-');
                    $newSheet->setCellValue('D' . $row, $item['kode_warna'] ?: '-');
                    $newSheet->setCellValue('E' . $row, $item['warna'] ?: '-');
                    $newSheet->setCellValue('F' . $row, $item['area_retur']);
                    $newSheet->setCellValue('G' . $row, $item['tgl_retur'] ?: '-');
                    $newSheet->setCellValue('H' . $row, isset($item['nama_cluster']) ? $item['nama_cluster'] : '-');
                    $newSheet->setCellValue('I' . $row, isset($item['kgs_retur']) ? number_format($item['kgs_retur'], 2, '.', '') : 0);
                    $newSheet->setCellValue('J' . $row, $item['cns_retur'] ?: 0);
                    $newSheet->setCellValue('K' . $row, $item['krg_retur'] ?: 0);
                    $newSheet->setCellValue('L' . $row, $item['lot_retur'] ?: '-');
                    $newSheet->setCellValue('M' . $row, $item['kategori'] ?: '-');
                    $newSheet->setCellValue('N' . $row, $item['keterangan_area'] ?: '-');
                    $newSheet->setCellValue('O' . $row, $item['keterangan_gbn'] ?: '-');
                    $row++;
                }

                $lastRow = $row - 1;
                $newSheet->getStyle("A3:O{$lastRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ]);

                foreach (range('A', 'Z') as $col) {
                    $newSheet->getColumnDimension($col)->setAutoSize(true);
                }

                $footerRow = $lastRow + 2;
                $newSheet->mergeCells("A{$footerRow}:O{$footerRow}");
                $newSheet->setCellValue("A{$footerRow}", 'REPORT RETUR STOCK');
                $newSheet->getStyle("A{$footerRow}")->getFont()->setBold(true)->setSize(10);
                $newSheet->getStyle("A{$footerRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }

            if ($name === 'RETUR TITIP ' . $key) {
                // Judul
                $newSheet->mergeCells('A1:O1');
                $newSheet->setCellValue('A1', 'REPORT RETUR TITIP ' . $key);
                $newSheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $newSheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Header
                $headerReturStock = ['NO', 'NO MODEL', 'ITEM TYPE', 'KODE WARNA', 'WARNA', 'AREA', 'TGL RETUR', 'NAMA CLUSTER', 'QTY RETUR', 'CONES RETUR', 'KRG/PACK RETUR', 'LOT RETUR', 'KATEGORI', 'KET AREA', 'KET GBN'];
                $col = 'A';
                foreach ($headerReturStock as $header) {
                    $newSheet->setCellValue($col . '3', $header);
                    $newSheet->getStyle($col . '3')->getFont()->setBold(true);
                    $col++;
                }

                $newSheet->getStyle('A3:O3')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);

                $row = 4;
                $no = 1;
                foreach ($data['returTitip'] as $item) {
                    $newSheet->setCellValue('A' . $row, $no++);
                    $newSheet->setCellValue('B' . $row, $item['no_model'] ?: '-');
                    $newSheet->setCellValue('C' . $row, $item['item_type'] ?: '-');
                    $newSheet->setCellValue('D' . $row, $item['kode_warna'] ?: '-');
                    $newSheet->setCellValue('E' . $row, $item['warna'] ?: '-');
                    $newSheet->setCellValue('F' . $row, $item['area_retur']);
                    $newSheet->setCellValue('G' . $row, $item['tgl_retur'] ?: '-');
                    $newSheet->setCellValue('H' . $row, $item['nama_cluster']);
                    $newSheet->setCellValue('I' . $row, isset($item['kgs_retur']) ? number_format($item['kgs_retur'], 2, '.', '') : 0);
                    $newSheet->setCellValue('J' . $row, $item['cns_retur'] ?: 0);
                    $newSheet->setCellValue('K' . $row, $item['krg_retur'] ?: 0);
                    $newSheet->setCellValue('L' . $row, $item['lot_retur'] ?: '-');
                    $newSheet->setCellValue('M' . $row, $item['kategori'] ?: '-');
                    $newSheet->setCellValue('N' . $row, $item['keterangan_area'] ?: '-');
                    $newSheet->setCellValue('O' . $row, $item['keterangan_gbn'] ?: '-');
                    $row++;
                }

                $lastRow = $row - 1;
                $newSheet->getStyle("A3:O{$lastRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ]);

                foreach (range('A', 'Z') as $col) {
                    $newSheet->getColumnDimension($col)->setAutoSize(true);
                }

                $footerRow = $lastRow + 2;
                $newSheet->mergeCells("A{$footerRow}:O{$footerRow}");
                $newSheet->setCellValue("A{$footerRow}", 'REPORT RETUR TITIP');
                $newSheet->getStyle("A{$footerRow}")->getFont()->setBold(true)->setSize(10);
                $newSheet->getStyle("A{$footerRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }

            if ($name === 'ORDER ' . $key . ' DIPINJAM') {
                // Judul
                $newSheet->mergeCells('A1:O1');
                $newSheet->setCellValue('A1', 'REPORT ORDER ' . $key . ' DIPINJAM OLEH ORDER LAIN');
                $newSheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $newSheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Header
                $headerReturStock = ['NO', 'NO MODEL', 'ITEM TYPE', 'KODE WARNA', 'WARNA', 'AREA', 'TGL PAKAI', 'TAMBAHAN', 'NAMA CLUSTER', 'QTY PAKAI', 'CONES PAKAI', 'LOT PAKAI', 'KET GBN', 'ADMIN', 'NOTE'];
                $col = 'A';
                foreach ($headerReturStock as $header) {
                    $newSheet->setCellValue($col . '3', $header);
                    $newSheet->getStyle($col . '3')->getFont()->setBold(true);
                    $col++;
                }

                $newSheet->getStyle('A3:O3')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);

                $row = 4;
                $no = 1;
                foreach ($data['dipinjam'] as $item) {
                    $newSheet->setCellValue('A' . $row, $no++);
                    $newSheet->setCellValue('B' . $row, $item['no_model_new'] ?: '-');
                    $newSheet->setCellValue('C' . $row, $item['item_type'] ?: '-');
                    $newSheet->setCellValue('D' . $row, $item['kode_warna'] ?: '-');
                    $newSheet->setCellValue('E' . $row, $item['warna'] ?: '-');
                    $newSheet->setCellValue('F' . $row, $item['area_out']);
                    $newSheet->setCellValue('G' . $row, $item['tgl_pakai'] ?: '-');
                    $newSheet->setCellValue('H' . $row, $item['po_tambahan'] == '1' ? 'YA' : '');
                    $newSheet->setCellValue('I' . $row, $item['nama_cluster']);
                    $newSheet->setCellValue('J' . $row, isset($item['kgs_out']) ? number_format($item['kgs_out'], 2, '.', '') : 0);
                    $newSheet->setCellValue('K' . $row, $item['cns_out'] ?: 0);
                    $newSheet->setCellValue('L' . $row, $item['lot_out'] ?: 0);
                    $newSheet->setCellValue('M' . $row, 'Pinjem dari order ' . $item['no_model_old'] . ' kode ' . $item['kode_warna'] . ' ' . $item['kgs_out'] . ' kg');
                    $newSheet->setCellValue('N' . $row, $item['admin'] ?: '-');
                    $newSheet->setCellValue('O' . $row, $item['keterangan'] . ' dari ' . $item['no_model_old'] . ' kode ' . $item['kode_warna'] . ' untuk ' . $item['no_model_new']);
                    $row++;
                }

                $lastRow = $row - 1;
                $newSheet->getStyle("A3:O{$lastRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ]);

                foreach (range('A', 'Z') as $col) {
                    $newSheet->getColumnDimension($col)->setAutoSize(true);
                }

                $footerRow = $lastRow + 2;
                $newSheet->mergeCells("A{$footerRow}:O{$footerRow}");
                $newSheet->setCellValue("A{$footerRow}", 'REPORT ORDER DIPINJAM');
                $newSheet->getStyle("A{$footerRow}")->getFont()->setBold(true)->setSize(10);
                $newSheet->getStyle("A{$footerRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }

            // ========================= PINDAH ORDER =========================
            if ($name === 'PINDAH ORDER ' . $key) {
                // Judul
                $newSheet->mergeCells('A1:K1');
                $newSheet->setCellValue('A1', 'REPORT PINDAH ORDER ' . $key . ' KE ORDER LAIN');
                $newSheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $newSheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Header
                $headerReturStock = ['NO', 'NO MODEL', 'DELIVERY', 'ITEM TYPE', 'KODE WARNA', 'WARNA', 'QTY', 'CONES', 'LOT', 'CLUSTER', 'KETERANGAN'];
                $col = 'A';
                foreach ($headerReturStock as $header) {
                    $newSheet->setCellValue($col . '3', $header);
                    $newSheet->getStyle($col . '3')->getFont()->setBold(true);
                    $col++;
                }

                $newSheet->getStyle('A3:K3')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);

                $row = 4;
                $no = 1;
                foreach ($data['dipindah'] as $item) {
                    $newSheet->setCellValue('A' . $row, $no++);
                    $newSheet->setCellValue('B' . $row, $item['no_model_old'] ?? '-');
                    $newSheet->setCellValue('C' . $row, $delivery[0]['delivery'] ?? '-');
                    $newSheet->setCellValue('D' . $row, $item['item_type'] ?? '-');
                    $newSheet->setCellValue('E' . $row, $item['kode_warna'] ?? '-');
                    $newSheet->setCellValue('F' . $row, $item['warna'] ?? '-');
                    $newSheet->setCellValue('G' . $row, $item['kgs']);
                    $newSheet->setCellValue('H' . $row, $item['cns']);
                    $newSheet->setCellValue('I' . $row, $item['lot']);
                    $newSheet->setCellValue('J' . $row, $item['nama_cluster']);
                    $newSheet->setCellValue('K' . $row, $item['keterangan'] . ' dari ' . $item['no_model_old'] . ' kode ' . $item['kode_warna'] . ' untuk ' . $item['no_model_new']);
                    $row++;
                }

                $lastRow = $row - 1;
                $newSheet->getStyle("A3:K{$lastRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ]);

                foreach (range('A', 'K') as $col) {
                    $newSheet->getColumnDimension($col)->setAutoSize(true);
                }

                $footerRow = $lastRow + 2;
                $newSheet->mergeCells("A{$footerRow}:O{$footerRow}");
                $newSheet->setCellValue("A{$footerRow}", 'REPORT HISTORY PINDAH ORDER');
                $newSheet->getStyle("A{$footerRow}")->getFont()->setBold(true)->setSize(10);
                $newSheet->getStyle("A{$footerRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
        }

        // Kembali ke sheet pertama sebelum menyimpan
        $spreadsheet->setActiveSheetIndex(0);

        // Download
        $filename = 'Report_Global_' . $key . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
    public function exportReportSisaPakai()
    {
        $delivery = $this->request->getGet('delivery');
        $noModel = $this->request->getGet('no_model');
        $kodeWarna = $this->request->getGet('kode_warna');
        $jenis = $this->request->getGet('jenis');
        $bulanMap = [
            'Januari' => 1,
            'Februari' => 2,
            'Maret' => 3,
            'April' => 4,
            'Mei' => 5,
            'Juni' => 6,
            'Juli' => 7,
            'Agustus' => 8,
            'September' => 9,
            'Oktober' => 10,
            'November' => 11,
            'Desember' => 12
        ];
        $bulan = $bulanMap[$delivery] ?? null;
        $apiUrl = api_url('material') . 'filterSisaPakai?bulan=' . urlencode($bulan) . '&no_model=' . urlencode($noModel) . '&kode_warna=' . urlencode($kodeWarna) . '&jenis=' . urlencode($jenis);
        $material = @file_get_contents($apiUrl);

        if ($material !== FALSE) {
            $data = json_decode($material, true);
        }

        // dd($data);

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->mergeCells('A1:Z1');
        $sheet->setCellValue('A1', 'REPORT SISA PAKAI ' . $jenis);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Buat header dengan sub-header
        $sheet->mergeCells('A3:A4');  // NO
        $sheet->mergeCells('B3:B4');  // TANGGAL PO
        $sheet->mergeCells('C3:C4');  // FOLL UP
        $sheet->mergeCells('D3:D4');  // NO MODEL
        $sheet->mergeCells('E3:E4');  // NO ORDER
        $sheet->mergeCells('F3:F4');  // AREA
        $sheet->mergeCells('G3:G4');  // BUYER
        $sheet->mergeCells('H3:H4');  // START MC
        $sheet->mergeCells('I3:I4');  // DELIVERY AWAL
        $sheet->mergeCells('J3:J4');  // DELIVERY AKHIR
        $sheet->mergeCells('K3:K4');  // ORDER TYPE
        $sheet->mergeCells('L3:L4');  // ITEM TYPE
        $sheet->mergeCells('M3:M4');  // KODE WARNA
        $sheet->mergeCells('N3:N4');  // WARNA
        $sheet->mergeCells('Q3:Q4');  // PESAN KG

        $sheet->setCellValue('A3', 'NO');
        $sheet->setCellValue('B3', 'TANGGAL PO');
        $sheet->setCellValue('C3', 'FOLL UP');
        $sheet->setCellValue('D3', 'NO MODEL');
        $sheet->setCellValue('E3', 'NO ORDER');
        $sheet->setCellValue('F3', 'AREA');
        $sheet->setCellValue('G3', 'BUYER');
        $sheet->setCellValue('H3', 'START MC');
        $sheet->setCellValue('I3', 'DELIVERY AWAL');
        $sheet->setCellValue('J3', 'DELIVERY AKHIR');
        $sheet->setCellValue('K3', 'ORDER TYPE');
        $sheet->setCellValue('L3', 'ITEM TYPE');
        $sheet->setCellValue('M3', 'KODE WARNA');
        $sheet->setCellValue('N3', 'WARNA');
        $sheet->setCellValue('Q3', 'PESAN KG');

        // Stock Awal: Header + Sub-header
        $sheet->mergeCells('O3:P3'); // STOCK AWAL
        $sheet->setCellValue('O3', 'STOCK AWAL');
        $sheet->setCellValue('O4', 'KG');
        $sheet->setCellValue('P4', 'LOT');

        // Po Tambahan Gbn: Header + Sub-header
        $sheet->mergeCells('R3:U3');
        $sheet->setCellValue('R3', 'PO TAMBAHAN GBN');
        $sheet->setCellValue('R4', 'TGL TERIMA PO(+) GBN');
        $sheet->setCellValue('S4', 'TGL PO(+) AREA');
        $sheet->setCellValue('T4', 'DELIVERY PO(+)');
        $sheet->setCellValue('U4', 'KG PO (+)');

        // Pakai
        $sheet->mergeCells('V3:V4');
        $sheet->setCellValue('V3', 'PAKAI');

        // (+) Pakai
        $sheet->mergeCells('W3:W4');
        $sheet->setCellValue('W3', '(+) PAKAI');

        // Retur: Header + Sub-header
        $sheet->mergeCells('X3:Y3');
        $sheet->setCellValue('X3', 'RETUR');
        $sheet->setCellValue('X4', 'KGS');
        $sheet->setCellValue('Y4', 'LOT');

        // Sisa
        $sheet->mergeCells('Z3:Z4');
        $sheet->setCellValue('Z3', 'SISA');

        // Format semua header
        $sheet->getStyle('A3:Z4')->getFont()->setBold(true);
        $sheet->getStyle('A3:Z4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3:Z4')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A3:Z4')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Data
        $row = 5;
        $no = 1;
        foreach ($data as $item) {
            // $sisa = (($item['kgs_out'] ?? 0 + 0) - $item['kgs_retur'] - ($item['kg_po'] + 0));
            $sisa = number_format((($item['kgs_out'] ?? 0) + ($item['kgs_out_plus'] ?? 0)) - ($item['kgs_retur'] ?? 0) - (($item['kg_pesan'] ?? 0) + ($item['kg_po_plus'] ?? 0)), 2, '.', '');

            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $item['lco_date']);
            $sheet->setCellValue('C' . $row, $item['foll_up']);
            $sheet->setCellValue('D' . $row, $item['no_model']);
            $sheet->setCellValue('E' . $row, $item['no_order']);
            $sheet->setCellValue('F' . $row, $item['area_out']);
            $sheet->setCellValue('G' . $row, $item['buyer']);
            $sheet->setCellValue('H' . $row, $item['start_mc'] ?? '');
            $sheet->setCellValue('I' . $row, $item['delivery_awal']);
            $sheet->setCellValue('J' . $row, $item['delivery_akhir']);
            $sheet->setCellValue('K' . $row, $item['unit']);
            $sheet->setCellValue('L' . $row, $item['item_type']);
            $sheet->setCellValue('M' . $row, $item['kode_warna']);
            $sheet->setCellValue('N' . $row, $item['color']);
            $sheet->setCellValue('O' . $row, number_format($item['kgs_stock_awal'], 2, '.', ''));
            $sheet->setCellValue('P' . $row, $item['lot_awal']);
            $sheet->setCellValue('Q' . $row, number_format($item['kg_pesan'] ?? 0, 2, '.', ''));
            $sheet->setCellValue('R' . $row, $item['tgl_terima_po_plus_gbn'] ?? '');
            $sheet->setCellValue('S' . $row, $item['tgl_po_plus_area'] ?? '');
            $sheet->setCellValue('T' . $row, $item['delivery_awal_plus'] ?? '');
            $sheet->setCellValue('U' . $row, number_format($item['kg_po_plus'] ?? 0, 2, '.', ''));
            if (in_array(strtoupper($jenis), ['BENANG', 'NYLON'])) {
                $sheet->setCellValue('V' . $row, number_format($item['kgs_out'] ?? 0, 2, '.', ''));
                $sheet->setCellValue('W' . $row, number_format($item['kgs_out_plus'] ?? 0, 2, '.', ''));
            } else {
                // Untuk SPANDEX/KARET
                $sheet->setCellValue('V' . $row, number_format($item['kgs_out_spandex_karet'] ?? 0, 2, '.', ''));
                $sheet->setCellValue('W' . $row, number_format($item['kgs_out_spandex_karet_plus'] ?? 0, 2, '.', ''));
            }
            $sheet->setCellValue('X' . $row, number_format($item['kgs_retur'] ?? 0, 2, '.', ''));
            $sheet->setCellValue('Y' . $row, $item['lot_retur'] ?? '');
            $sheet->setCellValue('Z' . $row, $sisa ?? 0);
            $row++;
        }

        // Border
        $lastRow = $row - 1;
        $sheet->getStyle("A5:Y{$lastRow}")
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->getStyle("A5:Y{$lastRow}")
            ->getAlignment()
            ->setVertical(Alignment::VERTICAL_CENTER);

        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];
        $sheet->getStyle("A3:Z{$lastRow}")->applyFromArray($styleArray);

        // Auto-size
        foreach (range('A', 'Z') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Download
        $filename = 'Report Sisa Pakai ' . $jenis . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
    public function exportHistoryPindahOrder()
    {
        $noModelOld   = $this->request->getGet('model_old') ?? '';
        $noModelNew   = $this->request->getGet('model_new') ?? '';
        $kodeWarna = $this->request->getGet('kode_warna') ?? '';

        // 1) Ambil data
        $apiUrl = api_url('material') . 'historyPindahOrder'
            . '?no_model_old=' . urlencode($noModelOld)
            . '&no_model_new=' . urlencode($noModelNew)
            . '&kode_warna='   . urlencode($kodeWarna);

        $material = @file_get_contents($apiUrl);

        if ($material !== FALSE) {
            $dataPindah = json_decode($material, true);
        }

        // 2) Loop dan merge API result
        foreach ($dataPindah as &$row) {
            try {
                $delivery = $this->ApsPerstyleModel->getDeliveryAwalAkhir($row['no_model_new']);
                $row['delivery_awal']  = $delivery['delivery_awal']  ?? '-';
                $row['delivery_akhir'] = $delivery['delivery_akhir'] ?? '-';
            } catch (\Exception $e) {
                $row['delivery_awal']  = '-';
                $row['delivery_akhir'] = '-';
            }
        }
        unset($row);

        // Buat spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('REPORT HISTORY PINDAH ORDER');

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

        $dataFilter = '';

        if (!empty($noModel) && !empty($kodeWarna)) {
            $dataFilter = ' NOMOR MODEL ' . $noModel . ' KODE WARNA ' . $kodeWarna;
        } elseif (!empty($noModel)) {
            $dataFilter = ' NOMOR MODEL ' . $noModel;
        } elseif (!empty($kodeWarna)) {
            $dataFilter = ' KODE WARNA ' . $kodeWarna;
        }

        // Judul
        $sheet->setCellValue('A1', 'REPORT HISTORY PINDAH ORDER' . $dataFilter);
        $sheet->mergeCells('A1:L1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $row_header = 3;

        $headers = [
            'A' => 'NO',
            'B' => 'NO MODEL',
            'C' => 'DELIVERY AWAL',
            'D' => 'DELIVERY AKHIR',
            'E' => 'ITEM TYPE',
            'F' => 'KODE WARNA',
            'G' => 'WARNA',
            'H' => 'QTY',
            'I' => 'CONES',
            'J' => 'LOT',
            'K' => 'CLUSTER',
            'L' => 'KETERANGAN'
        ];

        foreach ($headers as $col => $title) {
            $sheet->setCellValue($col . $row_header, $title);
            $sheet->getStyle($col . $row_header)->applyFromArray($styleHeader);
        }


        // Isi data
        $row = 4;
        $no = 1;

        foreach ($dataPindah as $key => $data) {
            if (!is_array($data)) {
                continue; // Lewati nilai akumulasi di $result
            }

            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $data['no_model_old']);
            $sheet->setCellValue('C' . $row, $data['delivery_awal']);
            $sheet->setCellValue('D' . $row, $data['delivery_akhir']);
            $sheet->setCellValue('E' . $row, $data['item_type']);
            $sheet->setCellValue('F' . $row, $data['kode_warna_old']);
            $sheet->setCellValue('G' . $row, $data['warna_old']);
            $sheet->setCellValue('H' . $row, $data['kgs']);
            $sheet->setCellValue('I' . $row, $data['cns']);
            $sheet->setCellValue('J' . $row, $data['lot']);
            $sheet->setCellValue('K' . $row, $data['cluster_old']);
            $sheet->setCellValue('L' . $row, $data['created_at'] . ' ' . $data['keterangan'] . ' KE ' . $data['no_model_new'] . ' KODE ' . $data['kode_warna_new']);

            // style body
            $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L'];

            foreach ($columns as $column) {
                $sheet->getStyle($column . $row)->applyFromArray($styleBody);
            }

            $row++;
        }

        foreach (['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L'] as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Set judul file dan header untuk download
        $filename = 'REPORT HISTORY PINDAH ORDER' . $dataFilter . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // Tulis file excel ke output
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
    public function exportReportBenang()
    {
        $tglAwal = $this->request->getGet('tanggal_awal');
        $tglAkhir = $this->request->getGet('tanggal_akhir');
        if (empty($tglAwal) && empty($tglAkhir)) {
            $bulan = $this->request->getGet('bulan');
            if (empty($bulan) || !preg_match('/^\d{4}\-\d{2}$/', $bulan)) {
                return $this->response
                    ->setStatusCode(400)
                    ->setJSON(['error' => 'Parameter “bulan” harus dalam format YYYY-MM']);
            }

            $timestamp     = strtotime($bulan . '-01');
            $tglAwal   = date('Y-m-01', $timestamp);
            $tglAkhir  = date('Y-m-t', $timestamp);
        }
        $apiUrl = api_url('material') . 'filterBenangMingguan?tanggal_awal=' . urlencode($tglAwal) . '&tanggal_akhir=' . urlencode($tglAkhir);
        $material = @file_get_contents($apiUrl);

        if ($material !== FALSE) {
            $data = json_decode($material, true);
        }

        // $data = $this->pemasukanModel->getFilterBenang($tglAwal, $tglAkhir);
        $tanggal = $data[0]['tgl_input'];
        $date = new DateTime($tanggal);
        $angkaBulan = (int) $date->format('m');
        $angkaTahun = (int) $date->format('Y');

        $namaBulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];

        $bulan = $namaBulan[$angkaBulan] . ' ' . $angkaTahun;

        $groups = [
            'COTTON' => [],
            'ACRYLIC' => [],
            'SPUN POLYESTER'   => [],
            'COTTON X LUREX'   => [],
            'ACRYLIC X LUREX'   => [],
            'Surat Jalan Tidak Masuk'   => [], //Misty
        ];

        foreach ($data as $row) {
            $it = strtoupper($row['item_type']);        // bahan_baku.jenis
            $sj = strtoupper($row['no_surat_jalan']);   // datang.no_suratjalan

            // 1) Surat Jalan Tidak Masuk
            // jenis BUKAN Acrylic, BUKAN Lurex; SJ tidak diawali KWS, bukan '', bukan SL*, bukan SC*
            if (
                stripos($it, 'ACRYLIC') === false
                && stripos($it, 'LUREX') === false
                && stripos($sj, 'KWS') !== 0
                && $sj !== ''
                && stripos($sj, 'SL') !== 0
                && stripos($sj, 'SC') !== 0
            ) {
                $groups['Surat Jalan Tidak Masuk'][] = $row;
                continue;
            }

            // 2) Cotton
            // bukan Lurex, bukan Polyester, bukan Spun, bukan ACR; SJ diawali KWS atau ''
            if (
                stripos($it, 'LUREX') === false
                && stripos($it, 'POLYESTER') === false
                && stripos($it, 'SPUN') === false
                && stripos($it, 'ACR') === false
                && (stripos($sj, 'KWS') === 0 || $sj === '')
            ) {
                $groups['COTTON'][] = $row;
                continue;
            }

            // 3) Acrylic
            // mengandung ACR; bukan Spun, bukan Polyester, bukan pola Lurex-Acr/Lurex-Acrylic
            if (
                stripos($it, 'ACR') !== false
                && stripos($sj, 'KWS') !== false
                && stripos($it, 'SPUN') === false
                && stripos($it, 'POLYESTER') === false
                && stripos($it, 'LUREX ACR') === false
                && stripos($it, 'ACRYLIC LUREX') === false
                && stripos($it, 'ACR LUREX') === false
            ) {
                $groups['ACRYLIC'][] = $row;
                continue;
            }

            // 4) Spun Polyester
            // bukan ACR, bukan Lurex; mengandung SPUN atau POLYESTER; SJ diawali KWS atau ''
            if (
                stripos($it, 'ACR') === false
                && stripos($it, 'LUREX') === false
                && (stripos($it, 'SPUN') !== false || stripos($it, 'POLYESTER') !== false)
                && (stripos($sj, 'KWS') === 0 || $sj === '')
            ) {
                $groups['SPUN POLYESTER'][] = $row;
                continue;
            }

            // 5) Cotton X Lurex
            // bukan ACR; mengandung LUREX
            if (
                stripos($it, 'ACR') === false
                && stripos($it, 'LUREX') !== false
            ) {
                $groups['COTTON X LUREX'][] = $row;
                continue;
            }

            // 6) Acrylic X Lurex
            // mengandung "ACRYLIC LUREX" atau "LUREX ACR"; SJ diawali KWS atau '' atau mengandung LRX
            if (
                (stripos($it, 'LUREX') !== false
                    || stripos($it, 'LUREX ACR') !== false)
                && (
                    stripos($sj, 'KWS') === 0
                    || $sj === ''
                    || stripos($sj, 'LRX') !== false
                )
            ) {
                $groups['ACRYLIC X LUREX'][] = $row;
                continue;
            }

            // kalau tidak masuk salah satu, bisa skip atau taruh di default
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $firstSheet = true;
        foreach ($groups as $title => $rows) {
            if ($firstSheet) {
                $sheet = $spreadsheet->getActiveSheet();
                $firstSheet = false;
            } else {
                $sheet = $spreadsheet->createSheet();
            }
            $sheet->setTitle($title);
            $spreadsheet->getDefaultStyle()->getFont()->setName('Arial');
            $spreadsheet->getDefaultStyle()->getFont()->setSize(11);

            // Header Form
            $sheet->mergeCells('A1:D2');
            $sheet->getColumnDimension('A')->setWidth(10);
            $sheet->getColumnDimension('D')->setWidth(15);

            // $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
            // $drawing->setName('Logo');
            // $drawing->setDescription('Logo Perusahaan');
            // $drawing->setPath('assets/img/logo-kahatex.png');
            // $drawing->setCoordinates('C1');
            // $drawing->setHeight(25);
            // $drawing->setOffsetX(55);
            // $drawing->setOffsetY(10);
            // $drawing->setWorksheet($sheet);
            $sheet->mergeCells('A3:D3');
            $sheet->setCellValue('A3', 'PT. KAHATEX');
            $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            //Merge
            $sheet->mergeCells('E1:O1');
            $sheet->mergeCells('E2:O2');
            $sheet->mergeCells('E3:O3');
            $sheet->mergeCells('E4:O4');
            $sheet->mergeCells('A4:D4');
            $sheet->mergeCells('B5:O5');

            $sheet->setCellValue('E1', 'FORMULIR');
            $sheet->setCellValue('E2', 'DEPARTEMENT KAOS KAKI');
            $sheet->setCellValue('E3', 'REKAP PENERIMAAN BENANG KAOS KAKI DARI DEPARTEMEN (KELOS WARNA)');
            $sheet->setCellValue('A4', 'No. Dokumen');
            $sheet->setCellValue('A5', 'Bulan : ');
            $sheet->setCellValue('B5', $bulan);

            // Buat header dengan sub-header
            $sheet->mergeCells('A6:A7');  // NO
            $sheet->mergeCells('B6:B7');  // NO SJ
            $sheet->mergeCells('C6:C7');  // TANGGAL SJ
            $sheet->mergeCells('D6:D7');  // TANGGAL PENERIMAAN
            $sheet->mergeCells('E6:E7');  // JENIS BARANG
            $sheet->mergeCells('F6:F7');  // KODE BENANG
            $sheet->mergeCells('G6:G7');  // WARNA
            $sheet->mergeCells('H6:H7');  // KODE WARNA
            $sheet->mergeCells('I6:I7');  // L/M/D
            $sheet->mergeCells('J6:J7');  // CONES
            $sheet->mergeCells('M6:M7');  // HARGA PER KG (USD)
            $sheet->mergeCells('N6:N7');  // TOTAL (USD)
            $sheet->mergeCells('O6:O7');  // KETERANGAN
            $sheet->mergeCells('P6:P7');  // DETAIL SJ
            $sheet->mergeCells('Q6:Q7');  // KELOMPOK
            $sheet->mergeCells('R6:R7');  // UKURAN
            $sheet->mergeCells('S6:S7');  // WARNA DASAR
            $sheet->mergeCells('T6:T7');  // NW

            $sheet->setCellValue('A6', 'NO');
            $sheet->setCellValue('B6', 'NO SJ');
            $sheet->setCellValue('C6', 'TANGGAL SJ');
            $sheet->setCellValue('D6', 'TANGGAL PENERIMAAN');
            $sheet->setCellValue('E6', 'JENIS BARANG');
            $sheet->setCellValue('F6', 'KODE BENANG');
            $sheet->setCellValue('G6', 'WARNA');
            $sheet->setCellValue('H6', 'KODE WARNA');
            $sheet->setCellValue('I6', 'L/M/D');
            $sheet->setCellValue('J6', 'CONES');
            $sheet->setCellValue('M6', 'HARGA PER KG (USD)');
            $sheet->setCellValue('N6', 'TOTAL (USD)');
            $sheet->setCellValue('O6', 'KETERANGAN');
            $sheet->setCellValue('P6', 'DETAIL SJ');
            $sheet->setCellValue('Q6', 'KELOMPOK');
            $sheet->setCellValue('R6', 'UKURAN');
            $sheet->setCellValue('S6', 'WARNA DASAR');
            $sheet->setCellValue('T6', 'NW');

            // Stock Awal: Header + Sub-header
            $sheet->mergeCells('K6:L6'); // STOCK AWAL
            $sheet->setCellValue('K6', 'QTY (KG)');
            $sheet->setCellValue('K7', 'GW');
            $sheet->setCellValue('L7', 'NW');

            // Format semua header
            $sheet->getStyle('A6:O6')->getFont()->setBold(true);
            $sheet->getStyle('A6:O6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('A6:O6')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle('A6:O6')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle('A6:O6')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

            $styleArray = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
            ];
            $sheet->getStyle("A1:O5")->applyFromArray($styleArray);
            $sheet->getStyle('E1:O3')->getFont()->setSize(16);

            $sheet->getStyle('A1:O4')->applyFromArray([
                'font' => [
                    'bold' => true,
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);

            // Data
            $row = 8;
            $no = 1;
            $groupTanggal = [];
            foreach ($rows as $item) {
                $tgl = $item['tgl_datang'];
                $groupTanggal[$tgl][] = $item;
            }

            foreach ($groupTanggal as $tgl => $items) {
                $subtotal = ['cones' => 0, 'gw' => 0, 'kgs_kirim' => 0, 'usd' => 0];

                foreach ($items as $item) {
                    $kgsKirim = $item['kgs_kirim'];
                    $harga = $item['harga'];
                    $totalUsd = $kgsKirim * $harga;

                    $tgl = $item['tgl_datang'];
                    $cones = (float)$item['cones'];
                    $gw    = (float)$item['gw'];
                    $kgs_kirim    = (float)$item['kgs_kirim'];
                    $usd   = $kgs_kirim * (float)$item['harga'];
                    $warnaDasar = $item['warna_dasar'] ?? null;

                    $sheet->setCellValue('A' . $row, $no++);
                    $sheet->setCellValue('B' . $row, $item['no_surat_jalan']);
                    $sheet->setCellValue('C' . $row, $item['tgl_datang']);
                    $sheet->setCellValue('D' . $row, $item['tgl_input']);
                    $sheet->setCellValue('E' . $row, $item['item_type']);
                    $sheet->setCellValue('F' . $row, $item['ukuran']);
                    if ($title === 'COTTON') {
                        $sheet->setCellValue('G' . $row, $item['warna'] . ' ' . ($item['kode_warna'] ?? ''));
                    } else {
                        $sheet->setCellValue('G' . $row, $item['warna']);
                    }
                    $sheet->setCellValue('H' . $row, $item['kode_warna'] ?? '');
                    $sheet->setCellValue('I' . $row, $item['l_m_d']);
                    $sheet->setCellValue('J' . $row, $item['cones'] ?? 0);
                    $sheet->setCellValue('K' . $row, number_format($item['gw'], 2));
                    $sheet->setCellValue('L' . $row, number_format($kgsKirim, 2));
                    $sheet->setCellValue('M' . $row, number_format($harga, 2));
                    $sheet->setCellValue('N' . $row, number_format($totalUsd, 2));
                    $sheet->setCellValue('O' . $row, ''); // Keterangan
                    $sheet->setCellValue('P' . $row, $item['detail_sj']);
                    $sheet->setCellValue('Q' . $row, $item['jenis']);
                    $sheet->setCellValue('R' . $row, $item['ukuran'] ?? '');
                    if ($warnaDasar === null || $warnaDasar === 'Kode warna belum ada di database') {
                        $sheet->setCellValue('S' . $row, 'Kode Warna Tidak Ada di Database');
                        $sheet->getStyle('S' . $row)->getFont()->getColor()->setARGB(Color::COLOR_RED);
                    } else {
                        $sheet->setCellValue('S' . $row, $warnaDasar);
                    }
                    $sheet->setCellValue('T' . $row, $kgsKirim ?? 0);
                    $row++;

                    // Hitung subtotal
                    $subtotal['cones'] += $cones;
                    $subtotal['gw'] += $gw;
                    $subtotal['kgs_kirim'] += $kgs_kirim;
                    $subtotal['usd'] += $usd;
                }

                // Tulis total setelah data tanggal itu
                $sheet->mergeCells("A{$row}:H{$row}");
                $sheet->setCellValue("A{$row}", "TOTAL");
                $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->setCellValue("J{$row}", $subtotal['cones']);
                $sheet->setCellValue("K{$row}", number_format($subtotal['gw'], 2));
                $sheet->setCellValue("L{$row}", number_format($subtotal['kgs_kirim'], 2));
                $sheet->setCellValue("N{$row}", number_format($subtotal['usd'], 2));
                $row++;
            }

            // Border
            $lastRow = $row - 1;
            $sheet->getStyle("A6:T{$lastRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->getStyle("A6:T{$lastRow}")
                ->getAlignment()
                ->setVertical(Alignment::VERTICAL_CENTER);

            $sheet->getStyle("A6:O{$lastRow}")->applyFromArray($styleArray);

            // Auto-size
            foreach (range('A', 'T') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
        }

        // Download
        if ($this->request->getGet('tanggal_awal') && $this->request->getGet('tanggal_akhir')) {
            $filename = 'Report Benang Mingguan ' . $tglAwal . ' - ' . $tglAkhir . '.xlsx';
        } else {
            $filename = 'Report Benang Bulan ' . $bulan . '.xlsx';
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function exportReportSisaDatangBenang()
    {
        $delivery = $this->request->getGet('delivery');
        $noModel = $this->request->getGet('no_model');
        $kodeWarna = $this->request->getGet('kode_warna');
        $bulanMap = [
            'Januari' => 1,
            'Februari' => 2,
            'Maret' => 3,
            'April' => 4,
            'Mei' => 5,
            'Juni' => 6,
            'Juli' => 7,
            'Agustus' => 8,
            'September' => 9,
            'Oktober' => 10,
            'November' => 11,
            'Desember' => 12
        ];
        $bulan = $bulanMap[$delivery] ?? null;
        $apiUrl = api_url('material') . 'reportSisaDatangBenang?delivery=' . urlencode($bulan) . '&no_model=' . urlencode($noModel) . '&kode_warna=' . urlencode($kodeWarna);
        $material = @file_get_contents($apiUrl);

        if ($material !== FALSE) {
            $data = json_decode($material, true);
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->mergeCells('A1:Z1');
        $sheet->setCellValue('A1', 'REPORT SISA DATANG BENANG');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Buat header dengan sub-header
        $sheet->mergeCells('A3:A4');  // NO
        $sheet->mergeCells('B3:B4');  // TANGGAL PO
        $sheet->mergeCells('C3:C4');  // FOLL UP
        $sheet->mergeCells('D3:D4');  // NO MODEL
        $sheet->mergeCells('E3:E4');  // NO ORDER
        $sheet->mergeCells('F3:F4');  // AREA
        $sheet->mergeCells('G3:G4');  // BUYER
        $sheet->mergeCells('H3:H4');  // START MC
        $sheet->mergeCells('I3:I4');  // DELIVERY AWAL
        $sheet->mergeCells('J3:J4');  // DELIVERY AKHIR
        $sheet->mergeCells('K3:K4');  // ORDER TYPE
        $sheet->mergeCells('L3:L4');  // ITEM TYPE
        $sheet->mergeCells('M3:M4');  // KODE WARNA
        $sheet->mergeCells('N3:N4');  // WARNA
        $sheet->mergeCells('Q3:Q4');  // PESAN KG

        $sheet->setCellValue('A3', 'NO');
        $sheet->setCellValue('B3', 'TANGGAL PO');
        $sheet->setCellValue('C3', 'FOLL UP');
        $sheet->setCellValue('D3', 'NO MODEL');
        $sheet->setCellValue('E3', 'NO ORDER');
        $sheet->setCellValue('F3', 'AREA');
        $sheet->setCellValue('G3', 'BUYER');
        $sheet->setCellValue('H3', 'START MC');
        $sheet->setCellValue('I3', 'DELIVERY AWAL');
        $sheet->setCellValue('J3', 'DELIVERY AKHIR');
        $sheet->setCellValue('K3', 'ORDER TYPE');
        $sheet->setCellValue('L3', 'ITEM TYPE');
        $sheet->setCellValue('M3', 'KODE WARNA');
        $sheet->setCellValue('N3', 'WARNA');
        $sheet->setCellValue('Q3', 'PESAN KG');

        // Stock Awal: Header + Sub-header
        $sheet->mergeCells('O3:P3'); // STOCK AWAL
        $sheet->setCellValue('O3', 'STOCK AWAL');
        $sheet->setCellValue('O4', 'KG');
        $sheet->setCellValue('P4', 'LOT');

        // Po Tambahan Gbn: Header + Sub-header
        $sheet->mergeCells('R3:U3');
        $sheet->setCellValue('R3', 'PO TAMBAHAN GBN');
        $sheet->setCellValue('R4', 'TGL TERIMA PO(+) GBN');
        $sheet->setCellValue('S4', 'TGL PO(+) AREA');
        $sheet->setCellValue('T4', 'DELIVERY PO(+)');
        $sheet->setCellValue('U4', 'KG PO (+)');

        // DATANG
        $sheet->mergeCells('V3:V4');
        $sheet->setCellValue('V3', 'DATANG');

        // (+) DATANG
        $sheet->mergeCells('W3:W4');
        $sheet->setCellValue('W3', '(+) DATANG');

        // Retur: Header + Sub-header
        $sheet->mergeCells('X3:X4');
        $sheet->setCellValue('X3', 'GANTI RETUR');

        $sheet->mergeCells('Y3:Y4');
        $sheet->setCellValue('Y3', 'RETUR');

        // Sisa
        $sheet->mergeCells('Z3:Z4');
        $sheet->setCellValue('Z3', 'SISA');

        // Format semua header
        $sheet->getStyle('A3:Z4')->getFont()->setBold(true);
        $sheet->getStyle('A3:Z4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3:Z4')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A3:Z4')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Data
        $row = 5;
        $no = 1;
        foreach ($data as $item) {
            $kgsAwal        = $item['kgs_stock_awal']  ?? 0;
            $kgsDatang      = $item['kgs_datang']      ?? 0;
            $kgsDatangPlus  = $item['kgs_datang_plus'] ?? 0;
            $kgsRetur       = $item['kgs_retur']       ?? 0;
            $kgPo           = $item['kg_po']           ?? 0;
            $kgPoPlus       = $item['kg_po_plus']      ?? 0;
            $qtyRetur       = $item['qty_retur']       ?? 0;

            if ($kgsRetur > 0) {
                $sisa = (($kgsAwal + $kgsDatang + $kgsDatangPlus + $kgsRetur) - ($kgPo - $kgPoPlus - $qtyRetur));
            } else {
                $sisa = (($kgsAwal + $kgsDatang + $kgsDatangPlus + $kgsRetur) - ($kgPo - $kgPoPlus));
            }

            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $item['lco_date']);
            $sheet->setCellValue('C' . $row, $item['foll_up']);
            $sheet->setCellValue('D' . $row, $item['no_model']);
            $sheet->setCellValue('E' . $row, $item['no_order']);
            $sheet->setCellValue('F' . $row, $item['area']);
            $sheet->setCellValue('G' . $row, $item['buyer']);
            $sheet->setCellValue('H' . $row, $item['start_mc'] ?? '');
            $sheet->setCellValue('I' . $row, $item['delivery_awal']);
            $sheet->setCellValue('J' . $row, $item['delivery_akhir']);
            $sheet->setCellValue('K' . $row, $item['unit']);
            $sheet->setCellValue('L' . $row, $item['item_type']);
            $sheet->setCellValue('M' . $row, $item['kode_warna']);
            $sheet->setCellValue('N' . $row, $item['color']);
            $sheet->setCellValue('O' . $row, number_format($item['kgs_stock_awal'], 2));
            $sheet->setCellValue('P' . $row, $item['lot_awal']);
            $sheet->setCellValue('Q' . $row, number_format($item['kg_po'], 2));
            $sheet->setCellValue('R' . $row, $item['tgl_terima_po_plus_gbn'] ?? '');
            $sheet->setCellValue('S' . $row, $item['tgl_po_plus_area'] ?? '');
            $sheet->setCellValue('T' . $row, $item['delivery_awal_plus'] ?? '');
            $sheet->setCellValue('U' . $row, number_format($item['kg_po_plus'] ?? 0, 2));
            $sheet->setCellValue('V' . $row, number_format($item['kgs_datang'] ?? 0, 2));
            $sheet->setCellValue('W' . $row, number_format($item['kgs_datang_plus'] ?? 0, 2));
            $sheet->setCellValue('X' . $row, number_format($item['kgs_retur'] ?? 0, 2));
            $sheet->setCellValue('Y' . $row, number_format($item['qty_retur'] ?? 0, 2));
            $sheet->setCellValue('Z' . $row, number_format($sisa ?? 0, 2));
            $row++;
        }

        // Border
        $lastRow = $row - 1;
        $sheet->getStyle("A5:Z{$lastRow}")
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->getStyle("A5:Z{$lastRow}")
            ->getAlignment()
            ->setVertical(Alignment::VERTICAL_CENTER);

        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];
        $sheet->getStyle("A3:Z{$lastRow}")->applyFromArray($styleArray);

        // Auto-size
        foreach (range('A', 'Z') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Download
        $filename = 'Report Sisa Datang Benang' . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function exportReportSisaDatangNylon()
    {
        $delivery = $this->request->getGet('delivery');
        $noModel = $this->request->getGet('no_model');
        $kodeWarna = $this->request->getGet('kode_warna');
        $bulanMap = [
            'Januari' => 1,
            'Februari' => 2,
            'Maret' => 3,
            'April' => 4,
            'Mei' => 5,
            'Juni' => 6,
            'Juli' => 7,
            'Agustus' => 8,
            'September' => 9,
            'Oktober' => 10,
            'November' => 11,
            'Desember' => 12
        ];
        $bulan = $bulanMap[$delivery] ?? null;
        $apiUrl = api_url('material') . 'reportSisaDatangNylon?delivery=' . urlencode($bulan) . '&no_model=' . urlencode($noModel) . '&kode_warna=' . urlencode($kodeWarna);
        $material = @file_get_contents($apiUrl);

        if ($material !== FALSE) {
            $data = json_decode($material, true);
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->mergeCells('A1:Z1');
        $sheet->setCellValue('A1', 'REPORT SISA DATANG NYLON');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Buat header dengan sub-header
        $sheet->mergeCells('A3:A4');  // NO
        $sheet->mergeCells('B3:B4');  // TANGGAL PO
        $sheet->mergeCells('C3:C4');  // FOLL UP
        $sheet->mergeCells('D3:D4');  // NO MODEL
        $sheet->mergeCells('E3:E4');  // NO ORDER
        $sheet->mergeCells('F3:F4');  // AREA
        $sheet->mergeCells('G3:G4');  // BUYER
        $sheet->mergeCells('H3:H4');  // START MC
        $sheet->mergeCells('I3:I4');  // DELIVERY AWAL
        $sheet->mergeCells('J3:J4');  // DELIVERY AKHIR
        $sheet->mergeCells('K3:K4');  // ORDER TYPE
        $sheet->mergeCells('L3:L4');  // ITEM TYPE
        $sheet->mergeCells('M3:M4');  // KODE WARNA
        $sheet->mergeCells('N3:N4');  // WARNA
        $sheet->mergeCells('Q3:Q4');  // PESAN KG

        $sheet->setCellValue('A3', 'NO');
        $sheet->setCellValue('B3', 'TANGGAL PO');
        $sheet->setCellValue('C3', 'FOLL UP');
        $sheet->setCellValue('D3', 'NO MODEL');
        $sheet->setCellValue('E3', 'NO ORDER');
        $sheet->setCellValue('F3', 'AREA');
        $sheet->setCellValue('G3', 'BUYER');
        $sheet->setCellValue('H3', 'START MC');
        $sheet->setCellValue('I3', 'DELIVERY AWAL');
        $sheet->setCellValue('J3', 'DELIVERY AKHIR');
        $sheet->setCellValue('K3', 'ORDER TYPE');
        $sheet->setCellValue('L3', 'ITEM TYPE');
        $sheet->setCellValue('M3', 'KODE WARNA');
        $sheet->setCellValue('N3', 'WARNA');
        $sheet->setCellValue('Q3', 'PESAN KG');

        // Stock Awal: Header + Sub-header
        $sheet->mergeCells('O3:P3'); // STOCK AWAL
        $sheet->setCellValue('O3', 'STOCK AWAL');
        $sheet->setCellValue('O4', 'KG');
        $sheet->setCellValue('P4', 'LOT');

        // Po Tambahan Gbn: Header + Sub-header
        $sheet->mergeCells('R3:U3');
        $sheet->setCellValue('R3', 'PO TAMBAHAN GBN');
        $sheet->setCellValue('R4', 'TGL TERIMA PO(+) GBN');
        $sheet->setCellValue('S4', 'TGL PO(+) AREA');
        $sheet->setCellValue('T4', 'DELIVERY PO(+)');
        $sheet->setCellValue('U4', 'KG PO (+)');

        // DATANG
        $sheet->mergeCells('V3:V4');
        $sheet->setCellValue('V3', 'DATANG');

        // (+) DATANG
        $sheet->mergeCells('W3:W4');
        $sheet->setCellValue('W3', '(+) DATANG');

        // Retur: Header + Sub-header
        $sheet->mergeCells('X3:X4');
        $sheet->setCellValue('X3', 'GANTI RETUR');

        $sheet->mergeCells('Y3:Y4');
        $sheet->setCellValue('Y3', 'RETUR');

        // Sisa
        $sheet->mergeCells('Z3:Z4');
        $sheet->setCellValue('Z3', 'SISA');

        // Format semua header
        $sheet->getStyle('A3:Z4')->getFont()->setBold(true);
        $sheet->getStyle('A3:Z4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3:Z4')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A3:Z4')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Data
        $row = 5;
        $no = 1;
        foreach ($data as $item) {
            $kgsAwal        = $item['kgs_stock_awal']  ?? 0;
            $kgsDatang      = $item['kgs_datang']      ?? 0;
            $kgsDatangPlus  = $item['kgs_datang_plus'] ?? 0;
            $kgsRetur       = $item['kgs_retur']       ?? 0;
            $kgPo           = $item['kg_po']           ?? 0;
            $kgPoPlus       = $item['kg_po_plus']      ?? 0;
            $qtyRetur       = $item['qty_retur']       ?? 0;

            if ($kgsRetur > 0) {
                $sisa = (($kgsAwal + $kgsDatang + $kgsDatangPlus + $kgsRetur) - ($kgPo - $kgPoPlus - $qtyRetur));
            } else {
                $sisa = (($kgsAwal + $kgsDatang + $kgsDatangPlus + $kgsRetur) - ($kgPo - $kgPoPlus));
            }

            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $item['lco_date']);
            $sheet->setCellValue('C' . $row, $item['foll_up']);
            $sheet->setCellValue('D' . $row, $item['no_model']);
            $sheet->setCellValue('E' . $row, $item['no_order']);
            $sheet->setCellValue('F' . $row, $item['area']);
            $sheet->setCellValue('G' . $row, $item['buyer']);
            $sheet->setCellValue('H' . $row, $item['start_mc'] ?? '');
            $sheet->setCellValue('I' . $row, $item['delivery_awal']);
            $sheet->setCellValue('J' . $row, $item['delivery_akhir']);
            $sheet->setCellValue('K' . $row, $item['unit']);
            $sheet->setCellValue('L' . $row, $item['item_type']);
            $sheet->setCellValue('M' . $row, $item['kode_warna']);
            $sheet->setCellValue('N' . $row, $item['color']);
            $sheet->setCellValue('O' . $row, number_format($item['kgs_stock_awal'], 2));
            $sheet->setCellValue('P' . $row, $item['lot_awal']);
            $sheet->setCellValue('Q' . $row, number_format($item['kg_po'], 2));
            $sheet->setCellValue('R' . $row, $item['tgl_terima_po_plus_gbn'] ?? '');
            $sheet->setCellValue('S' . $row, $item['tgl_po_plus_area'] ?? '');
            $sheet->setCellValue('T' . $row, $item['delivery_awal_plus'] ?? '');
            $sheet->setCellValue('U' . $row, number_format($item['kg_po_plus'] ?? 0, 2));
            $sheet->setCellValue('V' . $row, number_format($item['kgs_datang'] ?? 0, 2));
            $sheet->setCellValue('W' . $row, number_format($item['kgs_datang_plus'] ?? 0, 2));
            $sheet->setCellValue('X' . $row, number_format($item['kgs_retur'] ?? 0, 2));
            $sheet->setCellValue('Y' . $row, number_format($item['qty_retur'] ?? 0, 2));
            $sheet->setCellValue('Z' . $row, number_format($sisa ?? 0, 2));
            $row++;
        }

        // Border
        $lastRow = $row - 1;
        $sheet->getStyle("A5:Z{$lastRow}")
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->getStyle("A5:Z{$lastRow}")
            ->getAlignment()
            ->setVertical(Alignment::VERTICAL_CENTER);

        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];
        $sheet->getStyle("A3:Z{$lastRow}")->applyFromArray($styleArray);

        // Auto-size
        foreach (range('A', 'Z') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Download
        $filename = 'Report Sisa Datang Nylon' . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function exportReportSisaDatangSpandex()
    {
        $delivery = $this->request->getGet('delivery');
        $noModel = $this->request->getGet('no_model');
        $kodeWarna = $this->request->getGet('kode_warna');
        $bulanMap = [
            'Januari' => 1,
            'Februari' => 2,
            'Maret' => 3,
            'April' => 4,
            'Mei' => 5,
            'Juni' => 6,
            'Juli' => 7,
            'Agustus' => 8,
            'September' => 9,
            'Oktober' => 10,
            'November' => 11,
            'Desember' => 12
        ];
        $bulan = $bulanMap[$delivery] ?? null;
        $apiUrl = api_url('material') . 'reportSisaDatangSpandex?delivery=' . urlencode($bulan) . '&no_model=' . urlencode($noModel) . '&kode_warna=' . urlencode($kodeWarna);
        $material = @file_get_contents($apiUrl);

        if ($material !== FALSE) {
            $data = json_decode($material, true);
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->mergeCells('A1:Z1');
        $sheet->setCellValue('A1', 'REPORT SISA DATANG SPANDEX');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Buat header dengan sub-header
        $sheet->mergeCells('A3:A4');  // NO
        $sheet->mergeCells('B3:B4');  // TANGGAL PO
        $sheet->mergeCells('C3:C4');  // FOLL UP
        $sheet->mergeCells('D3:D4');  // NO MODEL
        $sheet->mergeCells('E3:E4');  // NO ORDER
        $sheet->mergeCells('F3:F4');  // AREA
        $sheet->mergeCells('G3:G4');  // BUYER
        $sheet->mergeCells('H3:H4');  // START MC
        $sheet->mergeCells('I3:I4');  // DELIVERY AWAL
        $sheet->mergeCells('J3:J4');  // DELIVERY AKHIR
        $sheet->mergeCells('K3:K4');  // ORDER TYPE
        $sheet->mergeCells('L3:L4');  // ITEM TYPE
        $sheet->mergeCells('M3:M4');  // KODE WARNA
        $sheet->mergeCells('N3:N4');  // WARNA
        $sheet->mergeCells('Q3:Q4');  // PESAN KG

        $sheet->setCellValue('A3', 'NO');
        $sheet->setCellValue('B3', 'TANGGAL PO');
        $sheet->setCellValue('C3', 'FOLL UP');
        $sheet->setCellValue('D3', 'NO MODEL');
        $sheet->setCellValue('E3', 'NO ORDER');
        $sheet->setCellValue('F3', 'AREA');
        $sheet->setCellValue('G3', 'BUYER');
        $sheet->setCellValue('H3', 'START MC');
        $sheet->setCellValue('I3', 'DELIVERY AWAL');
        $sheet->setCellValue('J3', 'DELIVERY AKHIR');
        $sheet->setCellValue('K3', 'ORDER TYPE');
        $sheet->setCellValue('L3', 'ITEM TYPE');
        $sheet->setCellValue('M3', 'KODE WARNA');
        $sheet->setCellValue('N3', 'WARNA');
        $sheet->setCellValue('Q3', 'PESAN KG');

        // Stock Awal: Header + Sub-header
        $sheet->mergeCells('O3:P3'); // STOCK AWAL
        $sheet->setCellValue('O3', 'STOCK AWAL');
        $sheet->setCellValue('O4', 'KG');
        $sheet->setCellValue('P4', 'LOT');

        // Po Tambahan Gbn: Header + Sub-header
        $sheet->mergeCells('R3:U3');
        $sheet->setCellValue('R3', 'PO TAMBAHAN GBN');
        $sheet->setCellValue('R4', 'TGL TERIMA PO(+) GBN');
        $sheet->setCellValue('S4', 'TGL PO(+) AREA');
        $sheet->setCellValue('T4', 'DELIVERY PO(+)');
        $sheet->setCellValue('U4', 'KG PO (+)');

        // DATANG
        $sheet->mergeCells('V3:V4');
        $sheet->setCellValue('V3', 'DATANG');

        // (+) DATANG
        $sheet->mergeCells('W3:W4');
        $sheet->setCellValue('W3', '(+) DATANG');

        // Retur: Header + Sub-header
        $sheet->mergeCells('X3:X4');
        $sheet->setCellValue('X3', 'GANTI RETUR');

        $sheet->mergeCells('Y3:Y4');
        $sheet->setCellValue('Y3', 'RETUR');

        // Sisa
        $sheet->mergeCells('Z3:Z4');
        $sheet->setCellValue('Z3', 'SISA');

        // Format semua header
        $sheet->getStyle('A3:Z4')->getFont()->setBold(true);
        $sheet->getStyle('A3:Z4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3:Z4')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A3:Z4')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Data
        $row = 5;
        $no = 1;
        foreach ($data as $item) {
            $kgsAwal        = $item['kgs_stock_awal']  ?? 0;
            $kgsDatang      = $item['kgs_datang']      ?? 0;
            $kgsDatangPlus  = $item['kgs_datang_plus'] ?? 0;
            $kgsRetur       = $item['kgs_retur']       ?? 0;
            $kgPo           = $item['kg_po']           ?? 0;
            $kgPoPlus       = $item['kg_po_plus']      ?? 0;
            $qtyRetur       = $item['qty_retur']       ?? 0;

            if ($kgsRetur > 0) {
                $sisa = (($kgsAwal + $kgsDatang + $kgsDatangPlus + $kgsRetur) - ($kgPo - $kgPoPlus - $qtyRetur));
            } else {
                $sisa = (($kgsAwal + $kgsDatang + $kgsDatangPlus + $kgsRetur) - ($kgPo - $kgPoPlus));
            }

            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $item['lco_date']);
            $sheet->setCellValue('C' . $row, $item['foll_up']);
            $sheet->setCellValue('D' . $row, $item['no_model']);
            $sheet->setCellValue('E' . $row, $item['no_order']);
            $sheet->setCellValue('F' . $row, $item['area']);
            $sheet->setCellValue('G' . $row, $item['buyer']);
            $sheet->setCellValue('H' . $row, $item['start_mc'] ?? '');
            $sheet->setCellValue('I' . $row, $item['delivery_awal']);
            $sheet->setCellValue('J' . $row, $item['delivery_akhir']);
            $sheet->setCellValue('K' . $row, $item['unit']);
            $sheet->setCellValue('L' . $row, $item['item_type']);
            $sheet->setCellValue('M' . $row, $item['kode_warna']);
            $sheet->setCellValue('N' . $row, $item['color']);
            $sheet->setCellValue('O' . $row, number_format($item['kgs_stock_awal'], 2));
            $sheet->setCellValue('P' . $row, $item['lot_awal']);
            $sheet->setCellValue('Q' . $row, number_format($item['kg_po'], 2));
            $sheet->setCellValue('R' . $row, $item['tgl_terima_po_plus_gbn'] ?? '');
            $sheet->setCellValue('S' . $row, $item['tgl_po_plus_area'] ?? '');
            $sheet->setCellValue('T' . $row, $item['delivery_awal_plus'] ?? '');
            $sheet->setCellValue('U' . $row, number_format($item['kg_po_plus'] ?? 0, 2));
            $sheet->setCellValue('V' . $row, number_format($item['kgs_datang'] ?? 0, 2));
            $sheet->setCellValue('W' . $row, number_format($item['kgs_datang_plus'] ?? 0, 2));
            $sheet->setCellValue('X' . $row, number_format($item['kgs_retur'] ?? 0, 2));
            $sheet->setCellValue('Y' . $row, number_format($item['qty_retur'] ?? 0, 2));
            $sheet->setCellValue('Z' . $row, number_format($sisa ?? 0, 2));
            $row++;
        }

        // Border
        $lastRow = $row - 1;
        $sheet->getStyle("A5:Z{$lastRow}")
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->getStyle("A5:Z{$lastRow}")
            ->getAlignment()
            ->setVertical(Alignment::VERTICAL_CENTER);

        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];
        $sheet->getStyle("A3:Z{$lastRow}")->applyFromArray($styleArray);

        // Auto-size
        foreach (range('A', 'Z') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Download
        $filename = 'Report Sisa Datang Spandex' . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function exportReportSisaDatangKaret()
    {
        $delivery = $this->request->getGet('delivery');
        $noModel = $this->request->getGet('no_model');
        $kodeWarna = $this->request->getGet('kode_warna');
        $bulanMap = [
            'Januari' => 1,
            'Februari' => 2,
            'Maret' => 3,
            'April' => 4,
            'Mei' => 5,
            'Juni' => 6,
            'Juli' => 7,
            'Agustus' => 8,
            'September' => 9,
            'Oktober' => 10,
            'November' => 11,
            'Desember' => 12
        ];
        $bulan = $bulanMap[$delivery] ?? null;
        $apiUrl = api_url('material') . 'reportSisaDatangKaret?delivery=' . urlencode($bulan) . '&no_model=' . urlencode($noModel) . '&kode_warna=' . urlencode($kodeWarna);
        $material = @file_get_contents($apiUrl);

        if ($material !== FALSE) {
            $data = json_decode($material, true);
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->mergeCells('A1:Z1');
        $sheet->setCellValue('A1', 'REPORT SISA DATANG KARET');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Buat header dengan sub-header
        $sheet->mergeCells('A3:A4');  // NO
        $sheet->mergeCells('B3:B4');  // TANGGAL PO
        $sheet->mergeCells('C3:C4');  // FOLL UP
        $sheet->mergeCells('D3:D4');  // NO MODEL
        $sheet->mergeCells('E3:E4');  // NO ORDER
        $sheet->mergeCells('F3:F4');  // AREA
        $sheet->mergeCells('G3:G4');  // BUYER
        $sheet->mergeCells('H3:H4');  // START MC
        $sheet->mergeCells('I3:I4');  // DELIVERY AWAL
        $sheet->mergeCells('J3:J4');  // DELIVERY AKHIR
        $sheet->mergeCells('K3:K4');  // ORDER TYPE
        $sheet->mergeCells('L3:L4');  // ITEM TYPE
        $sheet->mergeCells('M3:M4');  // KODE WARNA
        $sheet->mergeCells('N3:N4');  // WARNA
        $sheet->mergeCells('Q3:Q4');  // PESAN KG

        $sheet->setCellValue('A3', 'NO');
        $sheet->setCellValue('B3', 'TANGGAL PO');
        $sheet->setCellValue('C3', 'FOLL UP');
        $sheet->setCellValue('D3', 'NO MODEL');
        $sheet->setCellValue('E3', 'NO ORDER');
        $sheet->setCellValue('F3', 'AREA');
        $sheet->setCellValue('G3', 'BUYER');
        $sheet->setCellValue('H3', 'START MC');
        $sheet->setCellValue('I3', 'DELIVERY AWAL');
        $sheet->setCellValue('J3', 'DELIVERY AKHIR');
        $sheet->setCellValue('K3', 'ORDER TYPE');
        $sheet->setCellValue('L3', 'ITEM TYPE');
        $sheet->setCellValue('M3', 'KODE WARNA');
        $sheet->setCellValue('N3', 'WARNA');
        $sheet->setCellValue('Q3', 'PESAN KG');

        // Stock Awal: Header + Sub-header
        $sheet->mergeCells('O3:P3'); // STOCK AWAL
        $sheet->setCellValue('O3', 'STOCK AWAL');
        $sheet->setCellValue('O4', 'KG');
        $sheet->setCellValue('P4', 'LOT');

        // Po Tambahan Gbn: Header + Sub-header
        $sheet->mergeCells('R3:U3');
        $sheet->setCellValue('R3', 'PO TAMBAHAN GBN');
        $sheet->setCellValue('R4', 'TGL TERIMA PO(+) GBN');
        $sheet->setCellValue('S4', 'TGL PO(+) AREA');
        $sheet->setCellValue('T4', 'DELIVERY PO(+)');
        $sheet->setCellValue('U4', 'KG PO (+)');

        // DATANG
        $sheet->mergeCells('V3:V4');
        $sheet->setCellValue('V3', 'DATANG');

        // (+) DATANG
        $sheet->mergeCells('W3:W4');
        $sheet->setCellValue('W3', '(+) DATANG');

        // Retur: Header + Sub-header
        $sheet->mergeCells('X3:X4');
        $sheet->setCellValue('X3', 'GANTI RETUR');

        $sheet->mergeCells('Y3:Y4');
        $sheet->setCellValue('Y3', 'RETUR');

        // Sisa
        $sheet->mergeCells('Z3:Z4');
        $sheet->setCellValue('Z3', 'SISA');

        // Format semua header
        $sheet->getStyle('A3:Z4')->getFont()->setBold(true);
        $sheet->getStyle('A3:Z4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3:Z4')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A3:Z4')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Data
        $row = 5;
        $no = 1;
        foreach ($data as $item) {
            $kgsAwal        = $item['kgs_stock_awal']  ?? 0;
            $kgsDatang      = $item['kgs_datang']      ?? 0;
            $kgsDatangPlus  = $item['kgs_datang_plus'] ?? 0;
            $kgsRetur       = $item['kgs_retur']       ?? 0;
            $kgPo           = $item['kg_po']           ?? 0;
            $kgPoPlus       = $item['kg_po_plus']      ?? 0;
            $qtyRetur       = $item['qty_retur']       ?? 0;

            if ($kgsRetur > 0) {
                $sisa = (($kgsAwal + $kgsDatang + $kgsDatangPlus + $kgsRetur) - ($kgPo - $kgPoPlus - $qtyRetur));
            } else {
                $sisa = (($kgsAwal + $kgsDatang + $kgsDatangPlus + $kgsRetur) - ($kgPo - $kgPoPlus));
            }

            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $item['lco_date']);
            $sheet->setCellValue('C' . $row, $item['foll_up']);
            $sheet->setCellValue('D' . $row, $item['no_model']);
            $sheet->setCellValue('E' . $row, $item['no_order']);
            $sheet->setCellValue('F' . $row, $item['area']);
            $sheet->setCellValue('G' . $row, $item['buyer']);
            $sheet->setCellValue('H' . $row, $item['start_mc'] ?? '');
            $sheet->setCellValue('I' . $row, $item['delivery_awal']);
            $sheet->setCellValue('J' . $row, $item['delivery_akhir']);
            $sheet->setCellValue('K' . $row, $item['unit']);
            $sheet->setCellValue('L' . $row, $item['item_type']);
            $sheet->setCellValue('M' . $row, $item['kode_warna']);
            $sheet->setCellValue('N' . $row, $item['color']);
            $sheet->setCellValue('O' . $row, number_format($item['kgs_stock_awal'], 2));
            $sheet->setCellValue('P' . $row, $item['lot_awal']);
            $sheet->setCellValue('Q' . $row, number_format($item['kg_po'], 2));
            $sheet->setCellValue('R' . $row, $item['tgl_terima_po_plus_gbn'] ?? '');
            $sheet->setCellValue('S' . $row, $item['tgl_po_plus_area'] ?? '');
            $sheet->setCellValue('T' . $row, $item['delivery_awal_plus'] ?? '');
            $sheet->setCellValue('U' . $row, number_format($item['kg_po_plus'] ?? 0, 2));
            $sheet->setCellValue('V' . $row, number_format($item['kgs_datang'] ?? 0, 2));
            $sheet->setCellValue('W' . $row, number_format($item['kgs_datang_plus'] ?? 0, 2));
            $sheet->setCellValue('X' . $row, number_format($item['kgs_retur'] ?? 0, 2));
            $sheet->setCellValue('Y' . $row, number_format($item['qty_retur'] ?? 0, 2));
            $sheet->setCellValue('Z' . $row, number_format($sisa ?? 0, 2));
            $row++;
        }

        // Border
        $lastRow = $row - 1;
        $sheet->getStyle("A5:Z{$lastRow}")
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->getStyle("A5:Z{$lastRow}")
            ->getAlignment()
            ->setVertical(Alignment::VERTICAL_CENTER);

        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];
        $sheet->getStyle("A3:Z{$lastRow}")->applyFromArray($styleArray);

        // Auto-size
        foreach (range('A', 'Z') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Download
        $filename = 'Report Sisa Datang Karet' . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
    public function exportDataBooking()
    {
        $buyer = $this->request->getPost('buyer');
        $area = $this->request->getPost('area');
        $jarum = $this->request->getPost('jarum');
        $tglTurun = $this->request->getPost('tgl_booking');
        $tglTurunAkhir = $this->request->getPost('tgl_booking_akhir') ?? '';
        $awal = $this->request->getPost('awal');
        $akhir = $this->request->getPost('akhir');
        $yesterday = date('Y-m-d', strtotime('-2 day')); // DUA HARI KE BELAKANG

        $validate = [
            'buyer' => $buyer,
            'area' => $area,
            'jarum' => $jarum,
            'tgl_booking' => $tglTurun,
            'tgl_booking_akhir' => $tglTurunAkhir,
            'awal' => $awal,
            'akhir' => $akhir,
        ];
        $data = $this->bookingModel->getDataBooking($validate);
        // dd($data);
        // Buat file Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $styleTitle = [
            'font' => [
                'bold' => true, // Tebalkan teks
                'color' => ['argb' => 'FF000000'],
                'size' => 15
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER, // Alignment rata tengah
            ],
        ];

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

        $sheet->setCellValue('A1', 'DATA BOOKING ');
        $sheet->mergeCells('A1:M1');
        $sheet->getStyle('A1:M1')->applyFromArray($styleTitle);
        // Tulis header
        $sheet->setCellValue('A3', 'TGL BOOKING');
        $sheet->setCellValue('B3', 'NO BOOKING');
        $sheet->setCellValue('C3', 'PRODUCT');
        $sheet->setCellValue('D3', 'TYPE');
        $sheet->setCellValue('E3', 'NO ORDER');
        $sheet->setCellValue('F3', 'BUYER');
        $sheet->setCellValue('G3', 'SEAM');
        $sheet->setCellValue('H3', 'PRODUCTION UNIT');
        $sheet->setCellValue('I3', 'AREA');
        $sheet->setCellValue('J3', 'JARUM');
        $sheet->setCellValue('K3', 'DELIVERY');
        $sheet->setCellValue('L3', 'OPD');
        $sheet->setCellValue('M3', 'QTY');
        $sheet->setCellValue('N3', 'SISA');
        $sheet->setCellValue('O3', 'DESCRIPTION');
        $sheet->setCellValue('P3', 'PROSES ROUTE');
        $sheet->setCellValue('Q3', 'KETERANGAN');
        $sheet->getStyle('A3')->applyFromArray($styleHeader);
        $sheet->getStyle('B3')->applyFromArray($styleHeader);
        $sheet->getStyle('C3')->applyFromArray($styleHeader);
        $sheet->getStyle('D3')->applyFromArray($styleHeader);
        $sheet->getStyle('E3')->applyFromArray($styleHeader);
        $sheet->getStyle('F3')->applyFromArray($styleHeader);
        $sheet->getStyle('G3')->applyFromArray($styleHeader);
        $sheet->getStyle('H3')->applyFromArray($styleHeader);
        $sheet->getStyle('I3')->applyFromArray($styleHeader);
        $sheet->getStyle('J3')->applyFromArray($styleHeader);
        $sheet->getStyle('K3')->applyFromArray($styleHeader);
        $sheet->getStyle('L3')->applyFromArray($styleHeader);
        $sheet->getStyle('M3')->applyFromArray($styleHeader);
        $sheet->getStyle('N3')->applyFromArray($styleHeader);
        $sheet->getStyle('O3')->applyFromArray($styleHeader);
        $sheet->getStyle('P3')->applyFromArray($styleHeader);
        $sheet->getStyle('Q3')->applyFromArray($styleHeader);


        // Tulis data mulai dari baris 2
        $row = 4;
        $no = 1;

        foreach ($data as $item) {

            $kode = $item['product_type'] ?? '';

            $pecah = explode('-', $kode);

            $product = $pecah[0] ?? '';
            $type    = $pecah[1] ?? '';

            $sheet->setCellValue('A' . $row, $item['tgl_terima_booking']);
            $sheet->setCellValue('B' . $row, $item['no_booking']);
            $sheet->setCellValue('C' . $row, $product);
            $sheet->setCellValue('D' . $row, $type);
            $sheet->setCellValue('E' . $row, $item['no_order']);
            $sheet->setCellValue('F' . $row, $item['kd_buyer_booking']);
            $sheet->setCellValue('G' . $row, $item['seam']);
            $sheet->setCellValue('H' . $row, 'BOOKING');
            $sheet->setCellValue('I' . $row, 'BOOKING');
            $sheet->setCellValue('J' . $row, $item['needle']);
            $sheet->setCellValue('K' . $row, $item['delivery']);
            $sheet->setCellValue('L' . $row, $item['opd']);
            $sheet->setCellValue('M' . $row, number_format($item['qty_booking'] / 24, 2, '.', ''));
            $sheet->setCellValue('N' . $row, number_format($item['sisa_booking'] / 24, 2, '.', ''));
            $sheet->setCellValue('O' . $row, $item['desc']);
            $sheet->setCellValue('P' . $row, $item['proses_route']);
            $sheet->setCellValue('Q' . $row, $item['keterangan']);
            // 
            $sheet->getStyle('A' . $row)->applyFromArray($styleBody);
            $sheet->getStyle('B' . $row)->applyFromArray($styleBody);
            $sheet->getStyle('C' . $row)->applyFromArray($styleBody);
            $sheet->getStyle('D' . $row)->applyFromArray($styleBody);
            $sheet->getStyle('E' . $row)->applyFromArray($styleBody);
            $sheet->getStyle('F' . $row)->applyFromArray($styleBody);
            $sheet->getStyle('G' . $row)->applyFromArray($styleBody);
            $sheet->getStyle('H' . $row)->applyFromArray($styleBody);
            $sheet->getStyle('I' . $row)->applyFromArray($styleBody);
            $sheet->getStyle('J' . $row)->applyFromArray($styleBody);
            $sheet->getStyle('K' . $row)->applyFromArray($styleBody);
            $sheet->getStyle('L' . $row)->applyFromArray($styleBody);
            $sheet->getStyle('M' . $row)->applyFromArray($styleBody);
            $sheet->getStyle('N' . $row)->applyFromArray($styleBody);
            $sheet->getStyle('O' . $row)->applyFromArray($styleBody);
            $sheet->getStyle('P' . $row)->applyFromArray($styleBody);
            $sheet->getStyle('Q' . $row)->applyFromArray($styleBody);

            $row++;
        }

        // Set lebar kolom agar menyesuaikan isi
        foreach (range('A', 'Q') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Buat writer dan output file Excel
        $writer = new Xlsx($spreadsheet);
        $fileName = 'Data Booking.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
    public function formatImportInisial()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $styleTitle = [
            'font' => [
                'bold' => true, // Tebalkan teks
                'color' => ['argb' => 'FF000000'],
                'size' => 15
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER, // Alignment rata tengah
            ],
        ];
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

        $sheet->setCellValue('A1', 'Format Import Inisial');
        $sheet->mergeCells('A1:C1');
        $sheet->getStyle('A1:C1')->applyFromArray($styleTitle);
        // Tulis header
        $sheet->setCellValue('A3', 'PDK');
        $sheet->setCellValue('B3', 'Style Size');
        $sheet->setCellValue('C3', 'Inisial');
        $sheet->getStyle('A3')->applyFromArray($styleHeader);
        $sheet->getStyle('B3')->applyFromArray($styleHeader);
        $sheet->getStyle('C3')->applyFromArray($styleHeader);

        // Buat writer dan output file Excel
        $writer = new Xlsx($spreadsheet);
        $fileName = 'Format Import Inisial.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function exportDetailProduksi($area)
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

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $title = 'Data Produksi ' . $area;
        $sheet->mergeCells('A1:I1');
        $sheet->setCellValue('A1', $title);

        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // === Header Kolom di Baris 3 === //
        $sheet->setCellValue('A3', 'No');
        $sheet->setCellValue('B3', 'Tgl Produksi');
        $sheet->setCellValue('C3', 'No Model');
        $sheet->setCellValue('D3', 'Style Size');
        $sheet->setCellValue('E3', 'Delivery');
        $sheet->setCellValue('F3', 'No Mc');
        $sheet->setCellValue('G3', 'No Box');
        $sheet->setCellValue('H3', 'No Label');
        $sheet->setCellValue('I3', 'Qty Produksi (Pcs)');

        // === Isi Data mulai dari baris ke-3 === //
        $row = 4;
        $no = 1;
        foreach ($produksi as $data) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $data['tgl_produksi']);
            $sheet->setCellValue('C' . $row, $data['mastermodel']);
            $sheet->setCellValue('D' . $row, $data['size']);
            $sheet->setCellValue('E' . $row, $data['delivery']);
            $sheet->setCellValue('F' . $row, $data['no_mesin']);
            $sheet->setCellValue('G' . $row, $data['no_box']);
            $sheet->setCellValue('H' . $row, $data['no_label']);
            $sheet->setCellValue('I' . $row, $data['qty_produksi']);
            $row++;
        }

        // === Auto Size Kolom A - M === //
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // === Tambahkan Border (A2:M[row - 1]) === //
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];

        $lastDataRow = $row - 1;

        $sheet->getStyle("A3:I{$lastDataRow}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A3:I{$lastDataRow}")->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getStyle("A3:I{$lastDataRow}")->applyFromArray($styleArray);

        // Styling Header
        $headerRange = 'A3:I3';
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle($headerRange)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $filename = 'Data_Produksi_' . $area . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function exportPengajuanSPK2()
    {
        // Ambil filter dari input GET
        $tgl = $this->request->getGet('tgl_buat');
        $noModel = $this->request->getGet('no_model');

        $estimasiSpk = $this->estspk->getData($tgl, $noModel);
        foreach ($estimasiSpk as &$spk) {
            $noModel = $spk['model'];
            $styleSize = $spk['style'];
            $area = $spk['area'];
            $idaps = $this->ApsPerstyleModel->getIdApsForPph($area, $noModel, $styleSize);
            $idapsList = array_column($idaps, 'idapsperstyle');
            $spk['qty_order'] = $this->ApsPerstyleModel->getQtyOrder($noModel, $styleSize, $area)['qty'] ?? '-';
            $spk['plus_packing'] = $this->ApsPerstyleModel->getQtyOrder($noModel, $styleSize, $area)['po_plus'] ?? '0';
            $spk['deffect'] =  $this->bsModel->getBsPph($idapsList)['bs_setting'] ?? '-';
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $title = 'Data Pengajuan SPK2';
        $sheet->mergeCells('A1:J1');
        $sheet->setCellValue('A1', $title);

        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // === Header Kolom di Baris 3 === //
        $sheet->setCellValue('A3', 'No');
        $sheet->setCellValue('B3', 'Tgl Dibuat');
        $sheet->setCellValue('C3', 'Jam');
        $sheet->setCellValue('D3', 'No Model');
        $sheet->setCellValue('E3', 'Style');
        $sheet->setCellValue('F3', 'Area');
        $sheet->setCellValue('G3', 'Qty Order (Pcs)');
        $sheet->setCellValue('H3', 'BS Stocklot');
        $sheet->setCellValue('I3', 'Qty (+)Packing (Pcs)');
        $sheet->setCellValue('J3', 'Qty Minta (Pcs)');

        // === Isi Data mulai dari baris ke-3 === //
        $row = 4;
        $no = 1;
        foreach ($estimasiSpk as $data) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $data['tgl_buat']);
            $sheet->setCellValue('C' . $row, $data['jam']);
            $sheet->setCellValue('D' . $row, $data['model']);
            $sheet->setCellValue('E' . $row, $data['style']);
            $sheet->setCellValue('F' . $row, $data['area']);
            $sheet->setCellValue('G' . $row, $data['qty_order']);
            $sheet->setCellValue('H' . $row, $data['deffect']);
            $sheet->setCellValue('I' . $row, $data['plus_packing']);
            $sheet->setCellValue('J' . $row, $data['qty']);
            $row++;
        }

        // === Auto Size Kolom A - M === //
        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // === Tambahkan Border (A2:M[row - 1]) === //
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];

        $lastDataRow = $row - 1;

        $sheet->getStyle("A3:J{$lastDataRow}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A3:J{$lastDataRow}")->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getStyle("A3:J{$lastDataRow}")->applyFromArray($styleArray);

        // Styling Header
        $headerRange = 'A3:J3';
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle($headerRange)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $filename = 'Data_Pengajuan_SPK2.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    // REPORT NYA DIBAGI 3 BLOK
    // public function dataProduksi()
    // {
    //     $area = $this->request->getGet('area');
    //     $tglProduksi = $this->request->getGet('tgl_produksi');

    //     $dataProduksi = $this->produksiModel->getDataProduksi($area, $tglProduksi);
    //     // Ambil semua mastermodel unik yang tidak null
    //     $masterModels = [];
    //     $sizes = [];
    //     if (!empty($dataProduksi)) {
    //         foreach ($dataProduksi as $row) {
    //             if (!empty($row['mastermodel'])) {
    //                 $masterModels[$row['mastermodel']] = true; // key unik
    //             }
    //             if (!empty($row['size'])) { // pakai field 'size' sesuai select
    //                 $sizes[$row['size']] = true;
    //             }
    //         }
    //     }
    //     // Konversi key menjadi array
    //     $masterModels = array_keys($masterModels);
    //     $sizes = array_keys($sizes);

    //     $dataSmv = [];
    //     if (!empty($masterModels)) {
    //         // Ambil data SMV untuk semua mastermodel unik
    //         $dataSmv = $this->ApsPerstyleModel->getDataSmv($masterModels, $sizes);
    //     }

    //     // 1️⃣ Kelompokkan SMV per mastermodel + machinetypeid
    //     $smvPerMachineModel = [];
    //     foreach ($dataSmv as $row) {
    //         $key = $row['mastermodel'] . '|' . $row['machinetypeid'];
    //         if (!empty($row['smv']) && $row['smv'] > 0) {
    //             $smvPerMachineModel[$key][] = floatval($row['smv']);
    //         }
    //     }

    //     // 2️⃣ Hitung rata-rata SMV per kombinasi mastermodel|machinetypeid
    //     $avgSmvPerMachineModel = [];
    //     foreach ($smvPerMachineModel as $key => $values) {
    //         $avgSmvPerMachineModel[$key] = array_sum($values) / count($values);
    //     }

    //     // 3️⃣ Hitung jumlah mesin unik per mastermodel|machinetypeid dari data produksi
    //     $machineCount = [];
    //     foreach ($dataProduksi as $row) {
    //         $key = $row['mastermodel'] . '|' . $row['machinetypeid'];
    //         $noMesin = $row['no_mesin'];
    //         if (!empty($noMesin)) {
    //             $machineCount[$key][$noMesin] = true;
    //         }
    //     }

    //     // Konversi ke jumlah unik (count)
    //     foreach ($machineCount as $key => $listMesin) {
    //         $machineCount[$key] = count($listMesin);
    //     }

    //     // 4️⃣ Hitung target final
    //     $finalTarget = [];
    //     foreach ($avgSmvPerMachineModel as $key => $avgSmv) {
    //         // rumus target dasar: (86400 / smv) * 0.85 / 24
    //         $targetPerMachine = round((86400 / $avgSmv) * 0.85 / 24);
    //         // $targetPerMachine = 14.13;

    //         // kalikan dengan jumlah mesin
    //         // $countMesin = isset($machineCount[$key]) ? count($machineCount[$key]) : 1;
    //         $countMesin = isset($machineCount[$key]) ? $machineCount[$key] : 1;

    //         $finalTarget[$key] = $targetPerMachine * $countMesin;
    //     }

    //     // 5️⃣ DATA TOTAL PER JARUM (MACHINE TYPE) → PER MASTER MODEL
    //     // ===================== DATA TOTAL PER JARUM (MACHINE TYPE) → PER MASTER MODEL ======================
    //     $dataPerJarumPerModel = [];

    //     // Loop data produksi
    //     foreach ($dataProduksi as $row) {
    //         $machineType = $row['machinetypeid'] ?? null;
    //         $model = $row['mastermodel'] ?? null;
    //         $qtyProduksi = !empty($row['qty_produksi']) ? intval($row['qty_produksi']) / 24 : 0;
    //         $noMesin = $row['no_mesin'] ?? null;

    //         if (empty($machineType) || empty($model)) continue;

    //         // Inisialisasi level machine type
    //         if (!isset($dataPerJarumPerModel[$machineType])) {
    //             $dataPerJarumPerModel[$machineType] = [];
    //         }

    //         // Inisialisasi level mastermodel
    //         if (!isset($dataPerJarumPerModel[$machineType][$model])) {
    //             $keyTarget = $model . '|' . $machineType; // untuk ambil target
    //             $dataPerJarumPerModel[$machineType][$model] = [
    //                 'machinetypeid' => $machineType,
    //                 'mastermodel' => $model,
    //                 'total_produksi' => 0,
    //                 'machineCount' => 0,
    //                 'target' => $finalTarget[$keyTarget] ?? 0,
    //                 'mesin' => [], // catat mesin unik
    //             ];
    //         }

    //         // Tambah total produksi
    //         $dataPerJarumPerModel[$machineType][$model]['total_produksi'] += $qtyProduksi;

    //         // Catat mesin unik
    //         if (!empty($noMesin)) {
    //             $dataPerJarumPerModel[$machineType][$model]['mesin'][$noMesin] = true;
    //         }
    //     }

    //     // Hitung machineCount, rata per mesin, dan produktivitas
    //     foreach ($dataPerJarumPerModel as $machineType => &$models) {
    //         foreach ($models as $model => &$info) {
    //             $info['machineCount'] = count($info['mesin']);
    //             $info['rata_per_mesin'] = $info['machineCount'] > 0
    //                 ? round($info['total_produksi'] / $info['machineCount'], 2)
    //                 : 0;
    //             $info['productivity'] = $info['target'] > 0
    //                 ? round(($info['total_produksi'] / $info['target']) * 100, 2)
    //                 : 0;

    //             // Hapus mesin unik agar rapi
    //             unset($info['mesin']);
    //         }
    //         unset($models);
    //     }
    //     unset($info);

    //     // ============= HITUNG TOTAL ALL ===============
    //     $ttlMcArray = $this->areaMcModel->getTotalMc($area);
    //     $ttlMc = isset($ttlMcArray['total_mc']) ? intval($ttlMcArray['total_mc']) : 0;

    //     // Inisialisasi total keseluruhan
    //     // $totalMcOn = 0;
    //     $totalProduksi = 0;
    //     $totalTarget = 0;

    //     // Loop seluruh kombinasi machineType -> model
    //     foreach ($dataPerJarumPerModel as $machineType => $models) {
    //         foreach ($models as $info) {
    //             // $totalMcOn += $info['machineCount'];         // jumlah mesin aktif
    //             $totalProduksi += $info['total_produksi'];   // sum produksi
    //             $totalTarget += $info['target'];             // sum target
    //         }
    //     }

    //     $uniqueMesin = [];

    //     foreach ($dataProduksi as $row) {
    //         $noMesin = $row['no_mesin'] ?? null;
    //         if (!empty($noMesin)) {
    //             $uniqueMesin[$noMesin] = true; // pakai associative biar otomatis unik
    //         }
    //     }

    //     $totalMcOn = count($uniqueMesin);

    //     // Hitung Mc Off
    //     $totalMcOff = $ttlMc - $totalMcOn;

    //     // Hitung Efficiency / Productivity Keseluruhan
    //     $totalEfficiency = ($totalTarget > 0)
    //         ? round(($totalProduksi / $totalTarget) * 100, 2)
    //         : 0;

    //     // Buat array totalAll
    //     $totalAll = [
    //         'ttlMcAll'   => $ttlMc,         // semua mesin area
    //         'ttlMcOn'    => $totalMcOn,     // mesin aktif
    //         'ttlMcOff'   => $totalMcOff,    // mesin tidak aktif
    //         'totalProd'  => $totalProduksi,
    //         'totalTarget' => $totalTarget,
    //         'efficiency' => $totalEfficiency
    //     ];
    //     // dd($dataPerJarumPerModel, $totalAll);
    //     $spreadsheet = new Spreadsheet();
    //     $sheet = $spreadsheet->getActiveSheet();

    //     // ===== HEADER UTAMA (hanya di page pertama) =====
    //     $title = 'PRODUKSI MC ' . $area;
    //     $sheet->mergeCells('A1:G1');
    //     $sheet->setCellValue('A1', $title);
    //     $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
    //     $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

    //     $tanggal = 'TANGGAL ' . strtoupper(date('d F Y', strtotime($tglProduksi)));
    //     $sheet->mergeCells('P1:W1');
    //     $sheet->setCellValue('P1', $tanggal);
    //     $sheet->getStyle('P1')->getFont()->setBold(true)->setSize(14);
    //     $sheet->getStyle('P1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

    //     // ===== GROUP DATA BERDASAR MASTER MODEL =====
    //     $grouped = [];
    //     foreach ($dataProduksi as $row) {
    //         $model = $row['mastermodel'];
    //         $grouped[$model][] = $row;
    //     }

    //     // ===== CONFIG & VAR =====
    //     $startColumns = ['A', 'I', 'Q']; // blok 1,2,3
    //     $rowsPerBlockFirstPage = 49;
    //     $rowsPerBlockOtherPages = 51;
    //     $headers = ['JRM', 'IN', 'NO MC', 'A', 'B', 'C', 'TOTAL'];

    //     $currentRow = 3;

    //     // ===== HELPER FUNCTION UNTUK HEADER BLOK =====
    //     $writeHeaderBlock = function ($sheet, $colStart, $row, $headers) {
    //         foreach ($headers as $i => $header) {
    //             $sheet->setCellValue(chr(ord($colStart) + $i) . $row, $header);
    //         }

    //         $colEnd = chr(ord($colStart) + count($headers) - 1);
    //         $range = $colStart . $row . ':' . $colEnd . $row;

    //         $sheet->getStyle($range)->applyFromArray([
    //             'borders' => [
    //                 'allBorders' => [
    //                     'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
    //                     'color' => ['argb' => 'FF000000'],
    //                 ],
    //             ],
    //             'alignment' => [
    //                 'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
    //                 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
    //             ],
    //             'font' => [
    //                 'bold' => true,
    //             ],
    //         ]);

    //         $sheet->getRowDimension($row)->setRowHeight(43);
    //     };

    //     // ===== VAR INISIAL =====
    //     $baseRow = $currentRow;
    //     $page = 1;
    //     $currentBlock = 0;
    //     $rowInBlock = [0, 0, 0];
    //     $blockStartRow = [null, null, null];
    //     $blockEndRow = [null, null, null];

    //     // ===== HELPER UNTUK BORDER BLOK =====
    //     $applyBorders = function ($sheet, $startCol, $endCol, $startRow, $endRow) {
    //         if ($startRow === null || $endRow === null) return;
    //         if ($endRow < $startRow) return;
    //         $range = $startCol . $startRow . ':' . $endCol . $endRow;
    //         $sheet->getStyle($range)->getBorders()->getAllBorders()
    //             ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
    //     };

    //     // 🆕 Tambahkan untuk menyimpan posisi semua no_mesin
    //     $noMesinMap = [];

    //     // ================ START ISI DATA PER MODEL ================
    //     foreach ($grouped as $model => $items) {
    //         $currentLimit = ($page === 1) ? $rowsPerBlockFirstPage : $rowsPerBlockOtherPages;
    //         $colStart = $startColumns[$currentBlock];

    //         // Jika blok belum digunakan, tulis header dulu
    //         if ($blockStartRow[$currentBlock] === null) {
    //             $writeHeaderBlock($sheet, $colStart, $baseRow, $headers);
    //             $blockStartRow[$currentBlock] = $baseRow + 1; // baris pertama data
    //         }

    //         // ===== MASTER MODEL ROW =====
    //         $rowModel = $blockStartRow[$currentBlock] + $rowInBlock[$currentBlock];
    //         $colEnd = chr(ord($colStart) + 6);
    //         $sheet->mergeCells($colStart . $rowModel . ':' . $colEnd . $rowModel);
    //         $sheet->setCellValue($colStart . $rowModel, $model);

    //         $sheet->getStyle($colStart . $rowModel . ':' . $colEnd . $rowModel)->applyFromArray([
    //             'font' => ['bold' => true],
    //             'alignment' => [
    //                 'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
    //                 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
    //             ],
    //             'fill' => [
    //                 'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
    //                 'startColor' => ['argb' => 'FFD9D9D9'],
    //             ],
    //             'borders' => [
    //                 'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
    //             ],
    //         ]);
    //         $sheet->getRowDimension($rowModel)->setRowHeight(29);
    //         $rowInBlock[$currentBlock]++;

    //         // ===== ISI ITEM =====
    //         foreach ($items as $item) {
    //             if ($rowInBlock[$currentBlock] >= $currentLimit) {
    //                 // Tutup blok dan pindah
    //                 $sCol = $startColumns[$currentBlock];
    //                 $eCol = chr(ord($sCol) + 6);
    //                 $blockEndRow[$currentBlock] = $blockStartRow[$currentBlock] + $rowInBlock[$currentBlock] - 1;
    //                 $applyBorders($sheet, $sCol, $eCol, $blockStartRow[$currentBlock], $blockEndRow[$currentBlock]);

    //                 $currentBlock++;

    //                 if ($currentBlock > 2) {
    //                     // Page break
    //                     $lastFilled = max($blockEndRow);
    //                     $sheet->setBreak('A' . $lastFilled, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);

    //                     $page++;
    //                     $currentLimit = ($page === 1) ? $rowsPerBlockFirstPage : $rowsPerBlockOtherPages;

    //                     $currentBlock = 0;
    //                     $rowInBlock = [0, 0, 0];
    //                     $blockStartRow = [null, null, null];
    //                     $blockEndRow = [null, null, null];
    //                     $baseRow = $lastFilled + 2;
    //                 }

    //                 // Header blok baru
    //                 $colStart = $startColumns[$currentBlock];
    //                 if ($blockStartRow[$currentBlock] === null) {
    //                     $writeHeaderBlock($sheet, $colStart, $baseRow, $headers);
    //                     $blockStartRow[$currentBlock] = $baseRow + 1;
    //                 }
    //             }

    //             // Tulis data
    //             $rowNow = $blockStartRow[$currentBlock] + $rowInBlock[$currentBlock];
    //             $sheet->setCellValue($colStart . $rowNow, $item['machinetypeid']);
    //             $sheet->setCellValue(chr(ord($colStart) + 1) . $rowNow, $item['inisial'] ?? '-');
    //             $sheet->setCellValue(chr(ord($colStart) + 2) . $rowNow, $item['no_mesin']);
    //             $sheet->setCellValue(chr(ord($colStart) + 3) . $rowNow, $item['shift_a']);
    //             $sheet->setCellValue(chr(ord($colStart) + 4) . $rowNow, $item['shift_b']);
    //             $sheet->setCellValue(chr(ord($colStart) + 5) . $rowNow, $item['shift_c']);
    //             $sheet->setCellValue(chr(ord($colStart) + 6) . $rowNow, $item['qty_produksi']);
    //             $sheet->getRowDimension($rowNow)->setRowHeight(29);

    //             // 🆕 Catat posisi cell "NO MC"
    //             $noMcValue = $item['no_mesin'];
    //             if (!isset($noMesinMap[$noMcValue])) {
    //                 $noMesinMap[$noMcValue] = [];
    //             }
    //             $noMesinMap[$noMcValue][] = [
    //                 'col' => chr(ord($colStart) + 2), // kolom NO MC
    //                 'row' => $rowNow
    //             ];

    //             // Style rata tengah + border untuk isi tabel
    //             $sheet->getStyle($colStart . $rowNow . ':' . chr(ord($colStart) + 6) . $rowNow)->applyFromArray([
    //                 'alignment' => [
    //                     'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
    //                     'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
    //                 ],
    //                 'borders' => [
    //                     'allBorders' => [
    //                         'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
    //                         'color' => ['argb' => 'FF000000'],
    //                     ],
    //                 ],
    //             ]);

    //             $rowInBlock[$currentBlock]++;
    //         }

    //         // Jika sisa baris kurang dari 2, pindah blok dulu
    //         if ($rowInBlock[$currentBlock] + 2 > $currentLimit) {

    //             // Tutup blok sekarang
    //             $sCol = $startColumns[$currentBlock];
    //             $eCol = chr(ord($sCol) + 6);
    //             $blockEndRow[$currentBlock] = $blockStartRow[$currentBlock] + $rowInBlock[$currentBlock] - 1;
    //             $applyBorders($sheet, $sCol, $eCol, $blockStartRow[$currentBlock], $blockEndRow[$currentBlock]);

    //             $currentBlock++;

    //             if ($currentBlock > 2) {
    //                 // Page break
    //                 $lastFilled = max($blockEndRow);
    //                 $sheet->setBreak('A' . $lastFilled, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);

    //                 $page++;
    //                 $currentLimit = ($page === 1) ? $rowsPerBlockFirstPage : $rowsPerBlockOtherPages;

    //                 $currentBlock = 0;
    //                 $rowInBlock = [0, 0, 0];
    //                 $blockStartRow = [null, null, null];
    //                 $blockEndRow = [null, null, null];
    //                 $baseRow = $lastFilled + 2;
    //             }

    //             // Header blok baru
    //             $colStart = $startColumns[$currentBlock];
    //             if ($blockStartRow[$currentBlock] === null) {
    //                 $writeHeaderBlock($sheet, $colStart, $baseRow, $headers);
    //                 $blockStartRow[$currentBlock] = $baseRow + 1;
    //             }
    //         }

    //         // === Tambah Baris TOTAL & RATA-RATA ===
    //         $totalRow = $blockStartRow[$currentBlock] + $rowInBlock[$currentBlock];

    //         // Hitung sum kolom dan count mesin
    //         $sumA = $sumB = $sumC = $sumTotal = 0;
    //         $uniqueMc = [];

    //         foreach ($items as $it) {
    //             $sumA += $it['shift_a'];
    //             $sumB += $it['shift_b'];
    //             $sumC += $it['shift_c'];
    //             $sumTotal += $it['qty_produksi'];
    //             $uniqueMc[$it['no_mesin']] = true;
    //         }

    //         $countMc = count($uniqueMc);

    //         // Tulis baris TOTAL
    //         $sheet->setCellValue($colStart . $totalRow, "TOTAL");
    //         $sheet->mergeCells($colStart . $totalRow . ':' . chr(ord($colStart) + 1) . $totalRow);

    //         $sheet->setCellValue(chr(ord($colStart) + 2) . $totalRow, $countMc);
    //         $sheet->setCellValue(chr(ord($colStart) + 3) . $totalRow, $sumA);
    //         $sheet->setCellValue(chr(ord($colStart) + 4) . $totalRow, $sumB);
    //         $sheet->setCellValue(chr(ord($colStart) + 5) . $totalRow, $sumC);
    //         $sheet->setCellValue(chr(ord($colStart) + 6) . $totalRow, $sumTotal);
    //         $sheet->getRowDimension($totalRow)->setRowHeight(29);

    //         $sheet->getStyle($colStart . $totalRow . ':' . chr(ord($colStart) + 6) . $totalRow)->applyFromArray([
    //             'font' => ['bold' => true],
    //             'alignment' => [
    //                 'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
    //                 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
    //             ],
    //             'borders' => [
    //                 'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
    //             ],
    //         ]);

    //         $rowInBlock[$currentBlock]++;

    //         // Tambah baris RATA-RATA
    //         $avgRow = $blockStartRow[$currentBlock] + $rowInBlock[$currentBlock];
    //         $avg = $countMc > 0 ? round($sumTotal / $countMc, 2) : 0;

    //         $sheet->setCellValue($colStart . $avgRow, "RATA-RATA");
    //         $sheet->mergeCells($colStart . $avgRow . ':' . chr(ord($colStart) + 5) . $avgRow);
    //         $sheet->setCellValue(chr(ord($colStart) + 6) . $avgRow, $avg);
    //         $sheet->getRowDimension($avgRow)->setRowHeight(29);

    //         $sheet->getStyle($colStart . $avgRow . ':' . chr(ord($colStart) + 6) . $avgRow)->applyFromArray([
    //             'font' => ['italic' => true],
    //             'alignment' => [
    //                 'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
    //                 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
    //             ],
    //             'borders' => [
    //                 'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
    //             ],
    //         ]);

    //         $rowInBlock[$currentBlock]++;

    //         $blockEndRow[$currentBlock] = $blockStartRow[$currentBlock] + $rowInBlock[$currentBlock] - 1;
    //     }

    //     // ===== PASANG BORDER AKHIR =====
    //     for ($i = 0; $i < 3; $i++) {
    //         if ($blockStartRow[$i] !== null && $blockEndRow[$i] !== null) {
    //             $sCol = $startColumns[$i];
    //             $eCol = chr(ord($sCol) + 6);
    //             $applyBorders($sheet, $sCol, $eCol, $blockStartRow[$i], $blockEndRow[$i]);
    //         }
    //     }

    //     // 🆕 Setelah semua data ditulis, buat border miring untuk no_mesin yang sama
    //     foreach ($noMesinMap as $noMc => $cells) {
    //         if (count($cells) > 1 && !empty($noMc)) {
    //             foreach ($cells as $cell) {
    //                 $sheet->getStyle($cell['col'] . $cell['row'])->applyFromArray([
    //                     'borders' => [
    //                         // 'diagonalDirection' => \PhpOffice\PhpSpreadsheet\Style\Border::DIAGONAL_DOWN,
    //                         'diagonalDirection' => 1, // 1 = up, 2 = down
    //                         'diagonal' => [
    //                             'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
    //                             'color' => ['argb' => 'FF000000'],
    //                         ],
    //                     ],
    //                 ]);
    //             }
    //         }
    //     }

    //     // ===================== TOTAL DATA PER JARUM PER MODEL ======================
    //     // Spasi dulu biar gak nempel ke tabel sebelumnya
    //     $currentRow = $baseRow + max($rowInBlock) + 3;

    //     $headerTotal = ['JRM', 'PDK', 'TARGET', 'PROD', 'MC', 'RATA2', 'PRODUCTIVITY%'];

    //     // Style dasar header
    //     $styleHeaderTotal = [
    //         'font' => ['bold' => true],
    //         'alignment' => [
    //             'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
    //             'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
    //         ],
    //         'borders' => [
    //             'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
    //         ],
    //         'fill' => [
    //             'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
    //             'startColor' => ['argb' => 'FFD9E1F2'],
    //         ],
    //     ];

    //     // Style isi tabel total
    //     $styleIsiTotal = [
    //         'alignment' => [
    //             'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
    //             'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
    //         ],
    //         'borders' => [
    //             'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
    //         ],
    //     ];

    //     foreach ($dataPerJarumPerModel as $machinetypeid => $models) {

    //         // HEADER TABEL
    //         $sheet->fromArray(['JRM', 'PDK', 'TARGET', '', 'PROD', '', 'MC', '', 'RATA2', 'PRODUCTIVITY%', ''], null, "A{$currentRow}");

    //         $sheet->mergeCells("C{$currentRow}:D{$currentRow}"); // TARGET
    //         $sheet->mergeCells("E{$currentRow}:F{$currentRow}"); // PROD
    //         $sheet->mergeCells("G{$currentRow}:H{$currentRow}"); // MC
    //         $sheet->mergeCells("J{$currentRow}:K{$currentRow}"); // PRODUCTIVITY%

    //         $sheet->getStyle("A{$currentRow}:K{$currentRow}")->applyFromArray($styleHeaderTotal);
    //         $sheet->getRowDimension($currentRow)->setRowHeight(26);
    //         $currentRow++;

    //         $startDataRow = $currentRow;

    //         // ISI DATA
    //         foreach ($models as $model => $info) {
    //             $sheet->setCellValue("A{$currentRow}", $machinetypeid);
    //             $sheet->setCellValue("B{$currentRow}", $model);

    //             $sheet->setCellValue("C{$currentRow}", $info['target']);
    //             $sheet->mergeCells("C{$currentRow}:D{$currentRow}");

    //             $sheet->setCellValue("E{$currentRow}", $info['total_produksi']);
    //             $sheet->mergeCells("E{$currentRow}:F{$currentRow}");

    //             $sheet->setCellValue("G{$currentRow}", $info['machineCount']);
    //             $sheet->mergeCells("G{$currentRow}:H{$currentRow}");

    //             $sheet->setCellValue("I{$currentRow}", $info['rata_per_mesin']);

    //             $sheet->setCellValue("J{$currentRow}", $info['productivity']);
    //             $sheet->mergeCells("J{$currentRow}:K{$currentRow}");

    //             $sheet->getStyle("A{$currentRow}:K{$currentRow}")->applyFromArray($styleIsiTotal);
    //             $sheet->getRowDimension($currentRow)->setRowHeight(26);

    //             $currentRow++;
    //         }

    //         $endDataRow = $currentRow - 1;

    //         // TOTAL BARIS
    //         $sheet->setCellValue("A{$currentRow}", "TOTAL");
    //         $sheet->mergeCells("A{$currentRow}:B{$currentRow}");

    //         $sheet->setCellValue("C{$currentRow}", "=SUM(C{$startDataRow}:C{$endDataRow})");
    //         $sheet->mergeCells("C{$currentRow}:D{$currentRow}");

    //         $sheet->setCellValue("E{$currentRow}", "=SUM(E{$startDataRow}:E{$endDataRow})");
    //         $sheet->mergeCells("E{$currentRow}:F{$currentRow}");

    //         $sheet->setCellValue("G{$currentRow}", "=SUM(G{$startDataRow}:G{$endDataRow})");
    //         $sheet->mergeCells("G{$currentRow}:H{$currentRow}");

    //         $sheet->setCellValue("I{$currentRow}", "=AVERAGE(I{$startDataRow}:I{$endDataRow})");

    //         $sheet->setCellValue("J{$currentRow}", "=AVERAGE(J{$startDataRow}:J{$endDataRow})");
    //         $sheet->mergeCells("J{$currentRow}:K{$currentRow}");

    //         $sheet->getStyle("A{$currentRow}:K{$currentRow}")->applyFromArray([
    //             'font' => ['bold' => true],
    //             'alignment' => [
    //                 'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
    //                 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
    //             ],
    //             'borders' => [
    //                 'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
    //             ],
    //             'fill' => [
    //                 'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
    //                 'startColor' => ['argb' => 'FFD9D9D9'],
    //             ],
    //         ]);

    //         $sheet->getRowDimension($currentRow)->setRowHeight(26);

    //         $currentRow += 3;
    //     }

    //     $currentRow += 3;

    //     // =================== TOTAL SEMUANYA =================
    //     $startRow = $currentRow; // simpan awal posisi box

    //     // TTL MC ON
    //     $sheet->mergeCells("I{$currentRow}:J{$currentRow}");
    //     $sheet->setCellValue("I{$currentRow}", "TTL MC ON");
    //     $sheet->mergeCells("K{$currentRow}:L{$currentRow}");
    //     $sheet->setCellValue("K{$currentRow}", ":" . $totalAll['ttlMcOn']);
    //     $currentRow++;

    //     // TTL MC OFF
    //     $sheet->mergeCells("I{$currentRow}:J{$currentRow}");
    //     $sheet->setCellValue("I{$currentRow}", "TTL MC OFF");
    //     $sheet->mergeCells("K{$currentRow}:L{$currentRow}");
    //     $sheet->setCellValue("K{$currentRow}", ":" . $totalAll['ttlMcOff']);
    //     $currentRow++;

    //     // PRODUKSI
    //     $sheet->mergeCells("I{$currentRow}:J{$currentRow}");
    //     $sheet->setCellValue("I{$currentRow}", "PRODUKSI");
    //     $sheet->mergeCells("K{$currentRow}:L{$currentRow}");
    //     $sheet->setCellValue("K{$currentRow}", ":" . $totalAll['totalProd']);
    //     $currentRow++;

    //     // TARGET
    //     $sheet->mergeCells("I{$currentRow}:J{$currentRow}");
    //     $sheet->setCellValue("I{$currentRow}", "TARGET");
    //     $sheet->mergeCells("K{$currentRow}:L{$currentRow}");
    //     $sheet->setCellValue("K{$currentRow}", ":" . $totalAll['totalTarget']);
    //     $currentRow++;

    //     // EFF
    //     $sheet->mergeCells("I{$currentRow}:J{$currentRow}");
    //     $sheet->setCellValue("I{$currentRow}", "EFF");
    //     $sheet->mergeCells("K{$currentRow}:L{$currentRow}");
    //     $sheet->setCellValue("K{$currentRow}", ":" . $totalAll['efficiency'] . "%");
    //     $currentRow++;

    //     // Terapkan border hanya di pinggir kotak
    //     $sheet->getStyle("I{$startRow}:L" . ($currentRow - 1))->applyFromArray([
    //         'borders' => [
    //             'outline' => [
    //                 'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
    //                 'color' => ['argb' => '000000'],
    //             ],
    //         ],
    //         'alignment' => [
    //             'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
    //             'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
    //         ],
    //         'font' => [
    //             'bold' => true,
    //         ],
    //     ]);

    //     // LEBAR KOLOM
    //     $columnWidths = [
    //         'A' => 9,
    //         'B' => 7,
    //         'C' => 9,
    //         'D' => 7,
    //         'E' => 7,
    //         'F' => 7,
    //         'G' => 9,
    //         'H' => 3,
    //         'I' => 9,
    //         'J' => 7,
    //         'K' => 9,
    //         'L' => 7,
    //         'M' => 7,
    //         'N' => 7,
    //         'O' => 9,
    //         'P' => 3,
    //         'Q' => 9,
    //         'R' => 7,
    //         'S' => 9,
    //         'T' => 7,
    //         'T' => 7,
    //         'T' => 7,
    //         'T' => 9,
    //     ];
    //     foreach ($columnWidths as $c => $w) {
    //         $sheet->getColumnDimension($c)->setWidth($w);
    //     }

    //     // ===== PAGE SETUP A4 =====
    //     $sheet->getPageSetup()
    //         ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4)
    //         ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT)
    //         ->setFitToWidth(1)
    //         ->setFitToHeight(0);

    //     // ===== OUTPUT =====
    //     $filename = 'Data_Produksi_' . date('Ymd', strtotime($tglProduksi)) . '.xlsx';
    //     header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    //     header("Content-Disposition: attachment; filename=\"$filename\"");
    //     header('Cache-Control: max-age=0');

    //     $writer = new Xlsx($spreadsheet);
    //     $writer->save('php://output');
    //     exit;
    // }

    public function dataProduksi()
    {
        $area = $this->request->getGet('area');
        $tglProduksi = $this->request->getGet('tgl_produksi');

        $dataProduksi = $this->produksiModel->getDataProduksi($area, $tglProduksi);
        // Ambil semua mastermodel unik yang tidak null
        $masterModels = [];
        $sizes = [];
        if (!empty($dataProduksi)) {
            foreach ($dataProduksi as $row) {
                if (!empty($row['mastermodel'])) {
                    $masterModels[$row['mastermodel']] = true; // key unik
                }
                if (!empty($row['size'])) { // pakai field 'size' sesuai select
                    $sizes[$row['size']] = true;
                }
            }
        }
        // Konversi key menjadi array
        $masterModels = array_keys($masterModels);
        $sizes = array_keys($sizes);

        $dataSmv = [];
        if (!empty($masterModels)) {
            // Ambil data SMV untuk semua mastermodel unik
            $dataSmv = $this->ApsPerstyleModel->getDataSmv($masterModels, $sizes);
        }

        // 1️⃣ Kelompokkan SMV per mastermodel + machinetypeid
        $smvPerMachineModel = [];
        foreach ($dataSmv as $row) {
            $key = $row['mastermodel'] . '|' . $row['machinetypeid'];
            if (!empty($row['smv']) && $row['smv'] > 0) {
                $smvPerMachineModel[$key][] = floatval($row['smv']);
            }
        }

        // 2️⃣ Hitung rata-rata SMV per kombinasi mastermodel|machinetypeid
        $avgSmvPerMachineModel = [];
        foreach ($smvPerMachineModel as $key => $values) {
            $avgSmvPerMachineModel[$key] = array_sum($values) / count($values);
        }

        // 3️⃣ Hitung jumlah mesin unik per mastermodel|machinetypeid dari data produksi
        $machineCount = [];
        foreach ($dataProduksi as $row) {
            $key = $row['mastermodel'] . '|' . $row['machinetypeid'];
            $noMesin = $row['no_mesin'];
            if (!empty($noMesin)) {
                $machineCount[$key][$noMesin] = true;
            }
        }

        // Konversi ke jumlah unik (count)
        foreach ($machineCount as $key => $listMesin) {
            $machineCount[$key] = count($listMesin);
        }

        // 4️⃣ Hitung target final
        $finalTarget = [];
        foreach ($avgSmvPerMachineModel as $key => $avgSmv) {
            // rumus target dasar: (86400 / smv) * 0.85 / 24
            $targetPerMachine = round((86400 / $avgSmv) * 0.85 / 24);
            // $targetPerMachine = 14.13;

            // kalikan dengan jumlah mesin
            // $countMesin = isset($machineCount[$key]) ? count($machineCount[$key]) : 1;
            $countMesin = isset($machineCount[$key]) ? $machineCount[$key] : 1;

            $finalTarget[$key] = $targetPerMachine * $countMesin;
        }

        // 5️⃣ DATA TOTAL PER JARUM (MACHINE TYPE) → PER MASTER MODEL
        // ===================== DATA TOTAL PER JARUM (MACHINE TYPE) → PER MASTER MODEL ======================
        $dataPerJarumPerModel = [];

        // Loop data produksi
        foreach ($dataProduksi as $row) {
            $machineType = $row['machinetypeid'] ?? null;
            $model = $row['mastermodel'] ?? null;
            $qtyProduksi = !empty($row['qty_produksi']) ? intval($row['qty_produksi']) / 24 : 0;
            $noMesin = $row['no_mesin'] ?? null;

            if (empty($machineType) || empty($model)) continue;

            // Inisialisasi level machine type
            if (!isset($dataPerJarumPerModel[$machineType])) {
                $dataPerJarumPerModel[$machineType] = [];
            }

            // Inisialisasi level mastermodel
            if (!isset($dataPerJarumPerModel[$machineType][$model])) {
                $keyTarget = $model . '|' . $machineType; // untuk ambil target
                $dataPerJarumPerModel[$machineType][$model] = [
                    'machinetypeid' => $machineType,
                    'mastermodel' => $model,
                    'total_produksi' => 0,
                    'machineCount' => 0,
                    'target' => $finalTarget[$keyTarget] ?? 0,
                    'mesin' => [], // catat mesin unik
                ];
            }

            // Tambah total produksi
            $dataPerJarumPerModel[$machineType][$model]['total_produksi'] += $qtyProduksi;

            // Catat mesin unik
            if (!empty($noMesin)) {
                $dataPerJarumPerModel[$machineType][$model]['mesin'][$noMesin] = true;
            }
        }

        // Hitung machineCount, rata per mesin, dan produktivitas
        foreach ($dataPerJarumPerModel as $machineType => &$models) {
            foreach ($models as $model => &$info) {
                $info['machineCount'] = count($info['mesin']);
                $info['rata_per_mesin'] = $info['machineCount'] > 0
                    ? round($info['total_produksi'] / $info['machineCount'], 2)
                    : 0;
                $info['productivity'] = $info['target'] > 0
                    ? round(($info['total_produksi'] / $info['target']) * 100, 2)
                    : 0;

                // Hapus mesin unik agar rapi
                unset($info['mesin']);
            }
            unset($models);
        }
        unset($info);

        // ============= HITUNG TOTAL ALL ===============
        $ttlMcArray = $this->areaMcModel->getTotalMc($area);
        $ttlMc = isset($ttlMcArray['total_mc']) ? intval($ttlMcArray['total_mc']) : 0;

        // Inisialisasi total keseluruhan
        // $totalMcOn = 0;
        $totalProduksi = 0;
        $totalTarget = 0;

        // Loop seluruh kombinasi machineType -> model
        foreach ($dataPerJarumPerModel as $machineType => $models) {
            foreach ($models as $info) {
                // $totalMcOn += $info['machineCount'];         // jumlah mesin aktif
                $totalProduksi += $info['total_produksi'];   // sum produksi
                $totalTarget += $info['target'];             // sum target
            }
        }

        $uniqueMesin = [];

        foreach ($dataProduksi as $row) {
            $noMesin = $row['no_mesin'] ?? null;
            if (!empty($noMesin)) {
                $uniqueMesin[$noMesin] = true; // pakai associative biar otomatis unik
            }
        }

        $totalMcOn = count($uniqueMesin);

        // Hitung Mc Off
        $totalMcOff = $ttlMc - $totalMcOn;

        // Hitung Efficiency / Productivity Keseluruhan
        $totalEfficiency = ($totalTarget > 0)
            ? round(($totalProduksi / $totalTarget) * 100, 2)
            : 0;

        // Buat array totalAll
        $totalAll = [
            'ttlMcAll'   => $ttlMc,         // semua mesin area
            'ttlMcOn'    => $totalMcOn,     // mesin aktif
            'ttlMcOff'   => $totalMcOff,    // mesin tidak aktif
            'totalProd'  => $totalProduksi,
            'totalTarget' => $totalTarget,
            'efficiency' => $totalEfficiency
        ];
        // dd($dataPerJarumPerModel, $totalAll);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // ===== HEADER UTAMA (hanya di page pertama) =====
        $title = 'PRODUKSI MC ' . $area . ' TANGGAL ' . strtoupper(date('d F Y', strtotime($tglProduksi)));
        // $sheet->mergeCells('A1:G1');
        $sheet->setCellValue('A1', $title);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

        // ===== GROUP DATA BERDASAR MASTER MODEL =====
        $grouped = [];
        foreach ($dataProduksi as $row) {
            $model = $row['mastermodel'];
            $grouped[$model][] = $row;
        }

        // ===== CONFIG & VAR =====
        $rowsPerBlockFirstPage = 49;
        $rowsPerBlockOtherPages = 49;
        $headers = ['JRM', 'IN', 'NO MC', 'A', 'B', 'C', 'TOTAL'];

        $currentRow = 3;

        // ===== HELPER FUNCTION UNTUK HEADER BLOK =====
        $writeHeaderBlock = function ($sheet, $colStart, $row, $headers) {
            foreach ($headers as $i => $header) {
                $sheet->setCellValue(chr(ord($colStart) + $i) . $row, $header);
            }

            $colEnd = chr(ord($colStart) + count($headers) - 1);
            $range = $colStart . $row . ':' . $colEnd . $row;

            $sheet->getStyle($range)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
                'font' => [
                    'bold' => true,
                ],
            ]);

            $sheet->getRowDimension($row)->setRowHeight(43);
        };

        // ===== VAR INISIAL =====
        $baseRow = $currentRow;
        $page = 1;
        $currentBlock = 0;
        $rowInBlock = [0, 0, 0];
        $blockStartRow = [null, null, null];
        $blockEndRow = [null, null, null];

        // ===== HELPER UNTUK BORDER BLOK =====
        $applyBorders = function ($sheet, $startCol, $endCol, $startRow, $endRow) {
            if ($startRow === null || $endRow === null) return;
            if ($endRow < $startRow) return;
            $range = $startCol . $startRow . ':' . $endCol . $endRow;
            $sheet->getStyle($range)->getBorders()->getAllBorders()
                ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        };

        // 🆕 Tambahkan untuk menyimpan posisi semua no_mesin
        $noMesinMap = [];

        // ================ START ISI DATA PER MODEL (1 BLOK SAJA) ================
        $currentLimit = ($page === 1) ? $rowsPerBlockFirstPage : $rowsPerBlockOtherPages;

        // Tulis header pertama kali
        $writeHeaderBlock($sheet, 'A', $baseRow, $headers);
        $blockStartRow = $baseRow + 1;
        $rowInBlock = 0;

        foreach ($grouped as $model => $items) {

            // === MODEL HEADER ===
            if ($rowInBlock >= $currentLimit) {
                // page break & reset
                $lastRow = $blockStartRow + $rowInBlock - 1;
                $sheet->setBreak('A' . $lastRow, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);

                $page++;
                $currentLimit = ($page === 1) ? $rowsPerBlockFirstPage : $rowsPerBlockOtherPages;
                $baseRow = $lastRow + 2;
                $writeHeaderBlock($sheet, 'A', $baseRow, $headers);
                $blockStartRow = $baseRow + 1;
                $rowInBlock = 0;
            }

            $rowModel = $blockStartRow + $rowInBlock;
            $sheet->mergeCells('A' . $rowModel . ':G' . $rowModel);
            $sheet->setCellValue('A' . $rowModel, $model);
            $sheet->getStyle('A' . $rowModel . ':G' . $rowModel)->applyFromArray([
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FFD9D9D9']],
                'borders' => ['allBorders' => ['borderStyle' => 'thin']]
            ]);
            $sheet->getRowDimension($rowModel)->setRowHeight(29);
            $rowInBlock++;

            // === ISI ITEM ===
            foreach ($items as $item) {
                if ($rowInBlock >= $currentLimit) {
                    $lastRow = $blockStartRow + $rowInBlock - 1;
                    $sheet->setBreak('A' . $lastRow, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);

                    $page++;
                    $currentLimit = ($page === 1) ? $rowsPerBlockFirstPage : $rowsPerBlockOtherPages;
                    $baseRow = $lastRow + 2;
                    $writeHeaderBlock($sheet, 'A', $baseRow, $headers);
                    $blockStartRow = $baseRow + 1;
                    $rowInBlock = 0;
                }

                $rowNow = $blockStartRow + $rowInBlock;
                $sheet->setCellValue('A' . $rowNow, $item['machinetypeid']);
                $sheet->setCellValue('B' . $rowNow, $item['inisial'] ?? '-');
                $sheet->setCellValue('C' . $rowNow, $item['no_mesin']);
                // SIMPAN POSISI CELL UNTUK CEK DUPLIKAT
                $noMc = $item['no_mesin'];
                $mcType = $item['machinetypeid'];
                if (!empty($noMc) && !empty($mcType)) {
                    // Key gabungan contoh: "MESIN-123|TYPE-A"
                    $key = $noMc . '|' . $mcType;

                    $noMesinMap[$key][] = [
                        'col' => 'C',
                        'row' => $rowNow,
                        'no_mesin' => $noMc,
                        'machinetypeid' => $mcType
                    ];
                }
                $sheet->setCellValue('D' . $rowNow, number_format($item['shift_a'] / 24, 2));
                $sheet->setCellValue('E' . $rowNow, number_format($item['shift_b'] / 24, 2));
                $sheet->setCellValue('F' . $rowNow, number_format($item['shift_c'] / 24, 2));
                $sheet->setCellValue('G' . $rowNow, number_format($item['qty_produksi'] / 24, 2));
                $sheet->setCellValue('G' . $rowNow, number_format($item['qty_produksi'] / 24, 2));
                $sheet->getRowDimension($rowNow)->setRowHeight(29);
                $sheet->getStyle('A' . $rowNow . ':G' . $rowNow)->applyFromArray([
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                    'borders' => ['allBorders' => ['borderStyle' => 'thin']]
                ]);

                $rowInBlock++;
            }

            // === TOTAL & RATA2 ===
            $sumA = $sumB = $sumC = $sumTotal = 0;
            $uniqueMc = [];
            foreach ($items as $it) {
                $sumA += $it['shift_a'] / 24;
                $sumB += $it['shift_b'] / 24;
                $sumC += $it['shift_c'] / 24;
                $sumTotal += $it['qty_produksi'] / 24;
                $uniqueMc[$it['no_mesin']] = true;
            }
            $countMc = count($uniqueMc);

            // TOTAL ROW
            if ($rowInBlock >= $currentLimit) {
                $lastRow = $blockStartRow + $rowInBlock - 1;
                $sheet->setBreak('A' . $lastRow, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);

                $page++;
                $currentLimit = ($page === 1) ? $rowsPerBlockFirstPage : $rowsPerBlockOtherPages;
                $baseRow = $lastRow + 2;
                $writeHeaderBlock($sheet, 'A', $baseRow, $headers);
                $blockStartRow = $baseRow + 1;
                $rowInBlock = 0;
            }

            $totalRow = $blockStartRow + $rowInBlock;
            $sheet->setCellValue('A' . $totalRow, "TOTAL");
            $sheet->mergeCells('A' . $totalRow . ':B' . $totalRow);
            $sheet->setCellValue('C' . $totalRow, $countMc);
            $sheet->setCellValue('D' . $totalRow, number_format($sumA, 2));
            $sheet->setCellValue('E' . $totalRow, number_format($sumB, 2));
            $sheet->setCellValue('F' . $totalRow, number_format($sumC, 2));
            $sheet->setCellValue('G' . $totalRow, number_format($sumTotal, 2));
            $sheet->getStyle('A' . $totalRow . ':G' . $totalRow)->applyFromArray([
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                'borders' => ['allBorders' => ['borderStyle' => 'thin']]
            ]);
            $sheet->getRowDimension($totalRow)->setRowHeight(29);
            $rowInBlock++;

            // RATA2 ROW
            if ($rowInBlock >= $currentLimit) {
                $lastRow = $blockStartRow + $rowInBlock - 1;
                $sheet->setBreak('A' . $lastRow, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);

                $page++;
                $currentLimit = ($page === 1) ? $rowsPerBlockFirstPage : $rowsPerBlockOtherPages;
                $baseRow = $lastRow + 2;
                $writeHeaderBlock($sheet, 'A', $baseRow, $headers);
                $blockStartRow = $baseRow + 1;
                $rowInBlock = 0;
            }

            $avgRow = $blockStartRow + $rowInBlock;
            $avg = $countMc > 0 ? round($sumTotal / $countMc, 2) : 0;
            $sheet->setCellValue('A' . $avgRow, "RATA-RATA");
            $sheet->mergeCells('A' . $avgRow . ':F' . $avgRow);
            $sheet->setCellValue('G' . $avgRow, $avg);
            $sheet->getStyle('A' . $avgRow . ':G' . $avgRow)->applyFromArray([
                'font' => ['italic' => true],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                'borders' => ['allBorders' => ['borderStyle' => 'thin']]
            ]);
            $sheet->getRowDimension($avgRow)->setRowHeight(29);
            $rowInBlock++;
        }

        // 🆕 Setelah semua data ditulis, buat border miring untuk no_mesin yang sama
        foreach ($noMesinMap as $key => $cells) {

            // Jika baris lebih dari 1 untuk kombinasi yang sama → beri garis miring
            if (count($cells) > 1) {

                foreach ($cells as $cell) {
                    $sheet->getStyle($cell['col'] . $cell['row'])->applyFromArray([
                        'borders' => [
                            'diagonalDirection' => 1, // 1 = up-right ke bawah kiri
                            'diagonal' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                'color' => ['argb' => 'FF000000'],
                            ],
                        ],
                    ]);
                }
            }
        }

        // ===================== TOTAL DATA PER JARUM PER MODEL ======================
        // Spasi dulu biar gak nempel ke tabel sebelumnya
        $lastRowMain = $blockStartRow + $rowInBlock - 1;
        $currentRow = $lastRowMain + 3;

        $headerTotal = ['JRM', 'PDK', 'TARGET', 'PROD', 'MC', 'RATA2', 'PRODUCTIVITY%'];

        // Style dasar header
        $styleHeaderTotal = [
            'font' => ['bold' => true],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFD9E1F2'],
            ],
        ];

        // Style isi tabel total
        $styleIsiTotal = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
            ],
        ];

        foreach ($dataPerJarumPerModel as $machinetypeid => $models) {

            // HEADER TABEL
            $sheet->fromArray(['JRM', 'PDK', 'TARGET', '', 'PROD', '', 'MC', '', 'RATA2', 'PRODUCTIVITY%', ''], null, "A{$currentRow}");

            $sheet->mergeCells("C{$currentRow}:D{$currentRow}"); // TARGET
            $sheet->mergeCells("E{$currentRow}:F{$currentRow}"); // PROD
            $sheet->mergeCells("G{$currentRow}:H{$currentRow}"); // MC
            $sheet->mergeCells("J{$currentRow}:K{$currentRow}"); // PRODUCTIVITY%

            $sheet->getStyle("A{$currentRow}:K{$currentRow}")->applyFromArray($styleHeaderTotal);
            $sheet->getRowDimension($currentRow)->setRowHeight(26);
            $currentRow++;

            $startDataRow = $currentRow;

            // ISI DATA
            foreach ($models as $model => $info) {
                $sheet->setCellValue("A{$currentRow}", $machinetypeid);
                $sheet->setCellValue("B{$currentRow}", $model);

                $sheet->setCellValue("C{$currentRow}", $info['target']);
                $sheet->mergeCells("C{$currentRow}:D{$currentRow}");

                $sheet->setCellValue("E{$currentRow}", $info['total_produksi']);
                $sheet->mergeCells("E{$currentRow}:F{$currentRow}");

                $sheet->setCellValue("G{$currentRow}", $info['machineCount']);
                $sheet->mergeCells("G{$currentRow}:H{$currentRow}");

                $sheet->setCellValue("I{$currentRow}", $info['rata_per_mesin']);

                $sheet->setCellValue("J{$currentRow}", $info['productivity']);
                $sheet->mergeCells("J{$currentRow}:K{$currentRow}");

                $sheet->getStyle("A{$currentRow}:K{$currentRow}")->applyFromArray($styleIsiTotal);
                $sheet->getRowDimension($currentRow)->setRowHeight(26);

                $currentRow++;
            }

            $endDataRow = $currentRow - 1;

            // TOTAL BARIS
            $sheet->setCellValue("A{$currentRow}", "TOTAL");
            $sheet->mergeCells("A{$currentRow}:B{$currentRow}");

            $sheet->setCellValue("C{$currentRow}", "=SUM(C{$startDataRow}:C{$endDataRow})");
            $sheet->mergeCells("C{$currentRow}:D{$currentRow}");

            $sheet->setCellValue("E{$currentRow}", "=SUM(E{$startDataRow}:E{$endDataRow})");
            $sheet->mergeCells("E{$currentRow}:F{$currentRow}");

            $sheet->setCellValue("G{$currentRow}", "=SUM(G{$startDataRow}:G{$endDataRow})");
            $sheet->mergeCells("G{$currentRow}:H{$currentRow}");

            $sheet->setCellValue("I{$currentRow}", "=AVERAGE(I{$startDataRow}:I{$endDataRow})");

            $sheet->setCellValue("J{$currentRow}", "=AVERAGE(J{$startDataRow}:J{$endDataRow})");
            $sheet->mergeCells("J{$currentRow}:K{$currentRow}");

            $sheet->getStyle("A{$currentRow}:K{$currentRow}")->applyFromArray([
                'font' => ['bold' => true],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFD9D9D9'],
                ],
            ]);

            $sheet->getRowDimension($currentRow)->setRowHeight(26);

            $currentRow += 3;
        }

        $currentRow += 3;

        // =================== TOTAL SEMUANYA =================
        $startRow = $currentRow; // simpan awal posisi box

        // TTL MC ON
        $sheet->mergeCells("I{$currentRow}:J{$currentRow}");
        $sheet->setCellValue("I{$currentRow}", "TTL MC ON");
        $sheet->mergeCells("K{$currentRow}:L{$currentRow}");
        $sheet->setCellValue("K{$currentRow}", ":" . $totalAll['ttlMcOn']);
        $currentRow++;

        // TTL MC OFF
        $sheet->mergeCells("I{$currentRow}:J{$currentRow}");
        $sheet->setCellValue("I{$currentRow}", "TTL MC OFF");
        $sheet->mergeCells("K{$currentRow}:L{$currentRow}");
        $sheet->setCellValue("K{$currentRow}", ":" . $totalAll['ttlMcOff']);
        $currentRow++;

        // PRODUKSI
        $sheet->mergeCells("I{$currentRow}:J{$currentRow}");
        $sheet->setCellValue("I{$currentRow}", "PRODUKSI");
        $sheet->mergeCells("K{$currentRow}:L{$currentRow}");
        $sheet->setCellValue("K{$currentRow}", ":" . $totalAll['totalProd']);
        $currentRow++;

        // TARGET
        $sheet->mergeCells("I{$currentRow}:J{$currentRow}");
        $sheet->setCellValue("I{$currentRow}", "TARGET");
        $sheet->mergeCells("K{$currentRow}:L{$currentRow}");
        $sheet->setCellValue("K{$currentRow}", ":" . $totalAll['totalTarget']);
        $currentRow++;

        // EFF
        $sheet->mergeCells("I{$currentRow}:J{$currentRow}");
        $sheet->setCellValue("I{$currentRow}", "EFF");
        $sheet->mergeCells("K{$currentRow}:L{$currentRow}");
        $sheet->setCellValue("K{$currentRow}", ":" . $totalAll['efficiency'] . "%");
        $currentRow++;

        // Terapkan border hanya di pinggir kotak
        $sheet->getStyle("I{$startRow}:L" . ($currentRow - 1))->applyFromArray([
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'font' => [
                'bold' => true,
            ],
        ]);

        // LEBAR KOLOM
        $columnWidths = [
            'A' => 9,
            'B' => 7,
            'C' => 9,
            'D' => 7,
            'E' => 7,
            'F' => 7,
            'G' => 9,
            'H' => 3,
            'I' => 9,
            'J' => 7,
            'K' => 9,
            'L' => 7,
            'M' => 7,
            'N' => 7,
            'O' => 9,
            'P' => 3,
            'Q' => 9,
            'R' => 7,
            'S' => 9,
            'T' => 7,
            'T' => 7,
            'T' => 7,
            'T' => 9,
        ];
        foreach ($columnWidths as $c => $w) {
            $sheet->getColumnDimension($c)->setWidth($w);
        }

        // ===== PAGE SETUP A4 =====
        $sheet->getPageSetup()
            ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4)
            ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT)
            ->setFitToWidth(1)
            ->setFitToHeight(0);

        // ===== OUTPUT =====
        $filename = 'Data_Produksi_' . date('Ymd', strtotime($tglProduksi)) . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
    public function exportPps($area)
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle("PPS Area " . strtoupper($area));

        // Header row
        $headers = [
            'No.',
            'No. Model',
            'Buyer',
            'Category',
            'Style & Size',
            'Seam',
            'P.T',
            'Material Status',
            'Priority',
            'Mechanic',
            'Coor PPS',
            'Start Production',
            'Planning to knitting PPS',
            'Target to finish PPS',
            'Actual Start PPS',
            'Actual Finish PPS',
            'PPS Status',
            'Approval QAD',
            'Approval MR',
            'Approval FUP',
            'Notes / Revise'
        ];

        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getStyle($col . '1')->getFont()->setBold(true);
            $sheet->getColumnDimension($col)->setAutoSize(true);
            $col++;
        }
        if (!is_array($area)) {
            $area = [$area];
        }

        $getPdk = $this->DetailPlanningModel->getPpsData($area);
        $row = 2;
        $no = 1;

        foreach ($getPdk as $pdk) {
            $modelData = $this->orderModel->getModelData($pdk);
            $ppsData   = $this->ApsPerstyleModel->getPpsData($pdk['model'], $pdk['area']);
            foreach ($ppsData as $pps) {
                $sheet->setCellValue("A{$row}", $no++);
                $sheet->setCellValue("B{$row}", $modelData['no_model'] ?? '');
                $sheet->setCellValue("C{$row}", $modelData['buyer'] ?? '');
                $sheet->setCellValue("D{$row}", $modelData['product_type'] ?? '');
                $sheet->setCellValue("E{$row}", $pps['size'] ?? '');
                $sheet->setCellValue("F{$row}", $modelData['seam'] ?? '');
                $sheet->setCellValue("G{$row}", $pps['inisial'] ?? '');
                $sheet->setCellValue("H{$row}", ucfirst(strtolower($pps['material_status'] ?? '')));
                $sheet->setCellValue("I{$row}", ucfirst(strtolower($pps['priority'] ?? '')));
                $sheet->setCellValue("J{$row}", $pps['mechanic'] ?? '');
                $sheet->setCellValue("K{$row}", $pps['coor'] ?? '');
                $sheet->setCellValue("L{$row}", $this->formatDate($pps['start_mc'] ?? null));
                $sheet->setCellValue("M{$row}",  $this->formatDate($pps['start_pps_plan'] ?? null));
                $sheet->setCellValue("N{$row}",  $this->formatDate($pps['stop_pps_plan'] ?? null));
                $sheet->setCellValue("O{$row}",  $this->formatDate($pps['start_pps_act'] ?? null));
                $sheet->setCellValue("P{$row}",  $this->formatDate($pps['stop_pps_act'] ?? null));
                $sheet->setCellValue("Q{$row}", ucfirst(strtolower($pps['pps_status'] ?? '')));
                $sheet->setCellValue("R{$row}",  $this->formatDate($pps['acc_qad'] ?? null));
                $sheet->setCellValue("S{$row}",  $this->formatDate($pps['acc_mr'] ?? null));
                $sheet->setCellValue("T{$row}",  $this->formatDate($pps['acc_fu'] ?? null));
                $sheet->setCellValue("U{$row}", $pps['history'] ?? '');

                // 🟩 Highlighting logic
                if (strtolower($pps['material_status'] ?? '') === 'complete') {
                    $sheet->getStyle("H{$row}")->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()->setARGB('90EE90'); // light green
                }
                if (strtolower($pps['pps_status'] ?? '') === 'approved') {
                    $sheet->getStyle("Q{$row}")->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()->setARGB('90EE90');
                }

                $row++;
            }
        }

        $filename = "PPS_Export_"  . date('Ymd_His') . ".xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    function formatDate($date)
    {
        return !empty($date) ? date('j-M-y', strtotime($date)) : '';
    }

    public function excelStockMaterial()
    {
        $noModel = $this->request->getGet('no_model');
        $warna = $this->request->getGet('warna');

        $apiUrl = api_url('material') . "searchStock"
            . "?no_model=" . urlencode($noModel)
            . "&warna=" . urlencode($warna);

        $ch = curl_init($apiUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
        ]);

        $response = curl_exec($ch);
        $error    = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($response === false) {
            return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Curl error: ' . $error]);
        }

        $result = json_decode($response);
        if (!is_array($result)) {
            return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Data tidak valid dari API']);
        }

        // Buat Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $title = 'DATA STOCK MATERIAL';
        $sheet->mergeCells('A1:M1');
        $sheet->setCellValue('A1', $title);

        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // === Header Kolom di Baris 2 === //
        $sheet->setCellValue('A3', 'Nama Cluster');
        $sheet->setCellValue('B3', 'No Model');
        $sheet->setCellValue('C3', 'Kode Warna');
        $sheet->setCellValue('D3', 'Warna');
        $sheet->setCellValue('E3', 'Item Type');
        $sheet->setCellValue('F3', 'Kapasitas');
        $sheet->setCellValue('G3', 'Kgs');
        $sheet->setCellValue('H3', 'Krg');
        $sheet->setCellValue('I3', 'Cns');
        $sheet->setCellValue('J3', 'Kgs Stock Awal');
        $sheet->setCellValue('K3', 'Krg Stock Awal');
        $sheet->setCellValue('L3', 'Cns Stock Awal');
        $sheet->setCellValue('M3', 'Lot Stock');
        $sheet->setCellValue('N3', 'Lot Awal');


        // === Isi Data mulai dari baris ke-3 === //
        $row = 4;
        foreach ($result as $data) {
            if ($data->Kgs != 0 || $data->KgsStockAwal != 0) {
                $sheet->setCellValue('A' . $row, $data->nama_cluster);
                $sheet->setCellValue('B' . $row, $data->no_model);
                $sheet->setCellValue('C' . $row, $data->kode_warna);
                $sheet->setCellValue('D' . $row, $data->warna);
                $sheet->setCellValue('E' . $row, $data->item_type);
                $sheet->setCellValue('F' . $row, $data->kapasitas);
                $sheet->setCellValue('G' . $row, number_format($data->Kgs, 2));
                $sheet->setCellValue('H' . $row, $data->Krg);
                $sheet->setCellValue('I' . $row, $data->Cns);
                $sheet->setCellValue('J' . $row, $data->KgsStockAwal);
                $sheet->setCellValue('K' . $row, $data->KrgStockAwal);
                $sheet->setCellValue('L' . $row, $data->CnsStockAwal);
                $sheet->setCellValue('M' . $row, $data->lot_stock);
                $sheet->setCellValue('N' . $row, $data->lot_awal);
                $row++;
            }
        }

        // === Auto Size Kolom A - M === //
        foreach (range('A', 'N') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // === Tambahkan Border (A2:M[row - 1]) === //
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];

        $lastDataRow = $row - 1;
        $sheet->getStyle("A3:N{$lastDataRow}")->applyFromArray($styleArray);

        $filename = 'Data_Stock_' . date('YmdHis') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function exportMaterialPDK()
    {
        $noModel = $this->request->getGet('model') ?? null;
        $search  = $this->request->getGet('search') ?? null;

        if (!empty($noModel)) {

            $master = $this->orderModel->getStartMc($noModel);
        } else {
            $master = [
                'kd_buyer_order' => '-',
                'no_model'       => '-',
                'delivery_awal'  => '-',  // MIN dari apsperstyle.delivery
                'delivery_akhir' => '-',  // MAX dari apsperstyle.delivery
                'start_mc'       => '-' // MIN dari tanggal_planning.start_mesin
            ];
        }
        // 1. Ambil data dari API
        $params = [
            'model'  => $noModel ?? '',
            'search' => $search ?? ''
        ];

        $apiUrl = api_url('material') . 'statusbahanbaku/?' . http_build_query($params);
        $json   = @file_get_contents($apiUrl);

        if ($json === false) {
            return redirect()->back()->with('error', 'Gagal mengambil data dari API.');
        }

        $report = json_decode($json, true) ?? [];
        $master = $master ?? [];
        $status = $report ?? [];
        // dd($report,$master,$status);
        // 2. Buat Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Material PDK ' . $noModel);

        // Set default font
        $spreadsheet->getDefaultStyle()->getFont()
            ->setName('Calibri')
            ->setSize(10);

        $row = 1;

        // 3. Judul besar
        $sheet->setCellValue('A' . $row, 'LAPORAN STATUS BAHAN BAKU - MATERIAL PDK ' . $report[0]['no_model']);
        $sheet->mergeCells('A' . $row . ':N' . $row);
        $sheet->getStyle('A' . $row . ':N' . $row)->getFont()
            ->setBold(true)
            ->setSize(14);
        $sheet->getStyle('A' . $row . ':N' . $row)->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension($row)->setRowHeight(24);

        $row++;

        // 3a. Info cetak
        $sheet->setCellValue('A' . $row, 'Tanggal Cetak');
        $sheet->setCellValue('B' . $row, date('d-m-Y H:i'));
        $row += 2; // jarak ke blok master

        // 4. Header utama (info master)
        $startInfoRow = $row;

        $sheet->setCellValue('A' . $row, 'Buyer');
        $sheet->setCellValue('B' . $row, $master['kd_buyer_order'] ?? '');
        $row++;

        $sheet->setCellValue('A' . $row, 'No Model');
        $sheet->setCellValue('B' . $row, $master['no_model'] ?? '');
        $row++;

        $sheet->setCellValue('A' . $row, 'Delivery Awal');
        $sheet->setCellValue('B' . $row, $master['delivery_awal'] ?? '');
        $row++;

        $sheet->setCellValue('A' . $row, 'Delivery Akhir');
        $sheet->setCellValue('B' . $row, $master['delivery_akhir'] ?? '');
        $row++;

        $sheet->setCellValue('A' . $row, 'Start MC');
        $sheet->setCellValue('B' . $row, $master['start_mc'] ?? '');
        $row += 2; // jarak 1 baris ke tabel detail

        // Styling blok info master
        $sheet->getStyle('A' . $startInfoRow . ':A' . ($row - 2))->getFont()->setBold(true);
        $sheet->getStyle('A' . $startInfoRow . ':A' . ($row - 2))->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getColumnDimension('A')->setWidth(16);
        $sheet->getColumnDimension('B')->setWidth(30);

        // 5. Header tabel detail
        $headerRow = $row;

        $sheet->setCellValue('A' . $headerRow, 'Item Type');
        $sheet->setCellValue('B' . $headerRow, 'Kode Warna');
        $sheet->setCellValue('C' . $headerRow, 'Color');
        $sheet->setCellValue('D' . $headerRow, 'Jenis');
        $sheet->setCellValue('E' . $headerRow, 'Qty PO (Kg)');
        $sheet->setCellValue('F' . $headerRow, 'Total PO Tambahan');
        $sheet->setCellValue('G' . $headerRow, 'Kg Celup');
        $sheet->setCellValue('H' . $headerRow, 'Kg Stock');
        $sheet->setCellValue('I' . $headerRow, 'Lot Urut');
        $sheet->setCellValue('J' . $headerRow, 'Lot Celup');
        $sheet->setCellValue('K' . $headerRow, 'Tgl Schedule');
        $sheet->setCellValue('L' . $headerRow, 'Last Status');
        $sheet->setCellValue('M' . $headerRow, 'Keterangan');
        $sheet->setCellValue('N' . $headerRow, 'Admin');

        // Styling header tabel
        $headerRange = 'A' . $headerRow . ':N' . $headerRow;
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setWrapText(true);
        $sheet->getRowDimension($headerRow)->setRowHeight(22);
        $sheet->getStyle($headerRange)->getFill()->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE2EFDA'); // hijau muda ala Excel

        $row = $headerRow + 1;

        // 6. Isi data detail
        foreach ($status as $item) {
            $sheet->setCellValue('A' . $row, $item['item_type'] ?? '');
            $sheet->setCellValue('B' . $row, $item['kode_warna'] ?? '');
            $sheet->setCellValue('C' . $row, $item['color'] ?? '');
            $sheet->setCellValue('D' . $row, $item['jenis'] ?? '');

            // qty_po, kg_celup, kg_stock, total_po_tambahan → numeric 2 desimal
            $sheet->setCellValue('E' . $row, (float) ($item['qty_po'] ?? 0));
            $sheet->setCellValue('F' . $row, (float) ($item['total_po_tambahan'] ?? 0));
            $sheet->setCellValue('G' . $row, (float) ($item['kg_celup'] ?? 0));
            $sheet->setCellValue('H' . $row, (float) ($item['kg_stock'] ?? 0));
            $sheet->setCellValue('I' . $row, $item['lot_urut'] ?? '');
            $sheet->setCellValue('J' . $row, $item['lot_celup'] ?? '');
            $sheet->setCellValue('K' . $row, $item['tanggal_schedule'] ?? '');
            $sheet->setCellValue('L' . $row, $item['last_status'] ?? '');
            $sheet->setCellValue('M' . $row, $item['keterangan'] ?? '');
            $sheet->setCellValue('N' . $row, $item['admin'] ?? '');

            $row++;
        }

        $lastRow = max($row - 1, $headerRow);

        // 7. Auto-size kolom
        foreach (range('A', 'N') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // 8. Format angka 2 desimal untuk kolom KG
        if ($lastRow > $headerRow) {
            $sheet->getStyle('E' . ($headerRow + 1) . ':E' . $lastRow)
                ->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
            $sheet->getStyle('F' . ($headerRow + 1) . ':F' . $lastRow)
                ->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
            $sheet->getStyle('G' . ($headerRow + 1) . ':G' . $lastRow)
                ->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
            $sheet->getStyle('H' . ($headerRow + 1) . ':H' . $lastRow)
                ->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
        }

        // 9. Border di area tabel
        $tableRange = 'A' . $headerRow . ':N' . $lastRow;
        $sheet->getStyle($tableRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // 10. Freeze header tabel
        $sheet->freezePane('A' . ($headerRow + 1));

        // 11. Output sebagai download
        $fileName = 'Material_PDK_' . ($noModel ?? '-') . '.xlsx';

        return $this->response
            ->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->setHeader('Content-Disposition', 'attachment;filename="' . $fileName . '"')
            ->setHeader('Cache-Control', 'max-age=0')
            ->setBody((function () use ($spreadsheet) {
                ob_start();
                $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
                $writer->save('php://output');
                return ob_get_clean();
            })());
    }
    public function exportExcelPerbaikan()
    {
        $awal  = $this->request->getPost('awal') ?? '';
        $akhir = $this->request->getPost('akhir') ?? '';
        $pdk   = $this->request->getPost('pdk') ?? '';
        $area  = $this->request->getPost('area') ?? '';
        $buyer = $this->request->getPost('buyer') ?? '';

        $theData = [
            'awal' => $awal,
            'akhir' => $akhir,
            'pdk' => $pdk,
            'area' => $area,
            'buyer' => $buyer,
        ];
        if (!$theData) {
            return redirect()->back()->with('error', 'Tidak ada data untuk diexport!');
        }

        $getData = $this->perbaikanAreaModel->getDataPerbaikanFilter($theData);

        // Ubah kolom 'area' jadi kapital semua
        foreach ($getData as &$upper) {
            if (isset($upper['area'])) {
                $upper['area'] = strtoupper($upper['area']);
            }
        }
        unset($upper); // good practice


        // 1️⃣ GROUP BY AREA, KODE DEFFECT, DAN TANGGAL
        $groupedData = [];

        // Loop data mentah
        foreach ($getData as $row) {
            $area        = $row['area'];
            $kode        = $row['kode_deffect'];
            $tgl         = $row['tgl_perbaikan'];
            $keterangan  = $row['Keterangan'];
            $qty         = (int)$row['qty'];

            // Inisialisasi array bertingkat
            if (!isset($groupedData[$area])) {
                $groupedData[$area] = [];
            }

            if (!isset($groupedData[$area][$kode])) {
                $groupedData[$area][$kode] = [
                    'Keterangan' => $keterangan,
                    'Tanggal'    => []
                ];
            }

            if (!isset($groupedData[$area][$kode]['Tanggal'][$tgl])) {
                $groupedData[$area][$kode]['Tanggal'][$tgl] = 0;
            }

            // Tambahkan qty ke tanggal yang sesuai
            $groupedData[$area][$kode]['Tanggal'][$tgl] += $qty;
        }
        // Setelah semua data terkumpul
        ksort($groupedData); // urutkan area A-Z

        // Kalau mau juga urutkan kode di dalam tiap area:
        foreach ($groupedData as &$kodeList) {
            ksort($kodeList);
        }
        unset($kodeList); // hapus referensi

        // 2️⃣ KUMPULKAN SEMUA TANGGAL UNIK (buat header tabel nanti)
        $tanggalList = [];
        foreach ($getData as $row) {
            $tanggalList[$row['tgl_perbaikan']] = true;
        }
        $tanggalList = array_keys($tanggalList);
        sort($tanggalList); // biar tanggal urut

        // dd($theData, $groupedData);


        // excel dimulai
        $spreadsheet = new Spreadsheet();

        $sheetIndex = 0;
        foreach ($groupedData as $areaName => $defects) {

            // Buat sheet baru per area (kecuali sheet pertama)
            if ($sheetIndex > 0) {
                $spreadsheet->createSheet();
            }
            $sheet = $spreadsheet->setActiveSheetIndex($sheetIndex);
            $sheet->setTitle(substr($areaName, 0, 31)); // Excel limit 31 char

            // === Judul ===
            $lastCol = Coordinate::stringFromColumnIndex(2 + count($tanggalList));
            $sheet->mergeCells("A1:B1");
            $sheet->setCellValue('A1', 'LAPORAN SUMMARY REWORK ' . strtoupper($areaName));
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // === Header ===
            $sheet->setCellValue('A3', 'Kode Deffect');
            $sheet->setCellValue('B3', 'Keterangan');

            $col = 'C';
            foreach ($tanggalList as $tgl) {
                $sheet->setCellValue($col . '3', date('d-m-Y', strtotime($tgl)));
                $col++;
            }

            // Tambah kolom Grand Total
            $sheet->setCellValue($col . '3', 'Grand Total');
            $lastCol = Coordinate::stringFromColumnIndex(3 + count($tanggalList));

            // Header Style
            $headerRange = "A3:{$lastCol}3";
            $sheet->getStyle($headerRange)->getFont()->setBold(true);
            // $sheet->getStyle($headerRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('4472C4'); // biru
            $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // === Isi Data ===
            $rowNum = 4;
            $totalPerTanggal = array_fill_keys($tanggalList, 0); // Untuk total bawah
            $grandTotalSemua = 0;

            foreach ($defects as $kode => $defData) {
                $sheet->setCellValue('A' . $rowNum, $kode);
                $sheet->setCellValue('B' . $rowNum, $defData['Keterangan']);

                $col = 'C';
                $totalPerKode = 0;
                foreach ($tanggalList as $tgl) {
                    $qty = $defData['Tanggal'][$tgl] ?? '';
                    $sheet->setCellValue($col . $rowNum, $qty);
                    // 
                    $qtyRaw = $defData['Tanggal'][$tgl] ?? 0;
                    $qty = is_numeric($qtyRaw) ? (int)$qtyRaw : 0;
                    $totalPerTanggal[$tgl] += $qty;
                    $totalPerKode += $qty;

                    $col++;
                }
                // Kolom terakhir = Grand Total per kode defect
                $sheet->setCellValue($col . $rowNum, $totalPerKode);
                $sheet->getStyle($col . $rowNum)->getFont()->setBold(true);

                $grandTotalSemua += $totalPerKode;
                $rowNum++;
            }

            // === Tambahkan Baris Grand Total di Bawah ===
            $sheet->setCellValue('A' . $rowNum, 'Grand Total');
            $sheet->mergeCells("A{$rowNum}:B{$rowNum}");
            $col = 'C';
            foreach ($tanggalList as $tgl) {
                $sheet->setCellValue($col . $rowNum, $totalPerTanggal[$tgl]);
                $col++;
            }
            $sheet->setCellValue($col . $rowNum, $grandTotalSemua);


            // Bold & Center Grand Total row
            $grandRange = "A{$rowNum}:{$lastCol}{$rowNum}";
            $sheet->getStyle($grandRange)->getFont()->setBold(true);
            $sheet->getStyle($grandRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // === BORDER SEMUA KOLOM ===
            $dataRange = "A3:{$lastCol}{$rowNum}";
            $styleArray = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => '000000'],
                    ],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                ],
            ];
            $sheet->getStyle($dataRange)->applyFromArray($styleArray);

            // === AUTO SIZE ===
            foreach (range('A', $lastCol) as $columnID) {
                $sheet->getColumnDimension($columnID)->setAutoSize(true);
            }

            // === LAPORAN REWORK SECTION ===
            $laporanStart = $rowNum + 2;

            // Judul "LAPORAN REWORK"
            $sheet->setCellValue("A{$laporanStart}", 'LAPORAN REWORK');
            $sheet->mergeCells("A{$laporanStart}:C{$laporanStart}");
            $sheet->getStyle("A{$laporanStart}")->getFont()->setBold(true)->setUnderline(true)->setSize(12);

            // Header kecil PCS & DZ
            $sheet->setCellValue("B" . ($laporanStart + 1), 'PCS');
            $sheet->setCellValue("C" . ($laporanStart + 1), 'DZ');

            // TOTAL PER BULAN
            $sheet->setCellValue("A" . ($laporanStart + 2), 'TOTAL PER BULAN');
            $sheet->setCellValue("B" . ($laporanStart + 2), $grandTotalSemua); // kosong untuk PCS
            $sheet->setCellValue("C" . ($laporanStart + 2), round($grandTotalSemua / 24, 2));  // bisa isi hitungan nanti

            // RATA RATA PER HARI
            $avgPerHari = $grandTotalSemua / count($tanggalList);
            $sheet->setCellValue("A" . ($laporanStart + 3), 'RATA RATA PER HARI');
            $sheet->setCellValue("B" . ($laporanStart + 3), $avgPerHari);
            $sheet->setCellValue("C" . ($laporanStart + 3), round($avgPerHari / 24, 2));

            // Styling border & rata tengah
            $sheet->getStyle("A{$laporanStart}:C" . ($laporanStart + 3))
                ->getBorders()
                ->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN);

            $sheet->getStyle("A{$laporanStart}:C" . ($laporanStart + 3))
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);

            // Bold header baris pertama
            $sheet->getStyle("A{$laporanStart}")->getFont()->setBold(true);


            // === TOP 10 CHART DI BAWAH DATA ===
            $chartStartRow = $laporanStart + 6;

            // Judul bagian
            $sheet->setCellValue("G{$chartStartRow}", 'TOP 10 KODE REWORK');
            $sheet->mergeCells("G{$chartStartRow}:I{$chartStartRow}");
            $sheet->getStyle("G{$chartStartRow}")->getFont()->setBold(true)->setSize(12);
            $sheet->getStyle("G{$chartStartRow}")->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);

            // Header tabel di kanan
            $sheet->setCellValue("G" . ($chartStartRow + 1), 'Kode');
            $sheet->setCellValue("H" . ($chartStartRow + 1), 'Keterangan');
            $sheet->setCellValue("I" . ($chartStartRow + 1), 'Total Qty');

            // Hitung total per kode
            $topData = [];
            foreach ($defects as $kode => $val) {
                $totalQty = array_sum($val['Tanggal']);
                $topData[$kode] = [
                    'Kode' => $kode,
                    'Keterangan' => $val['Keterangan'],
                    'Total' => $totalQty
                ];
            }

            // Urutkan besar ke kecil, ambil 10
            usort($topData, fn($a, $b) => $b['Total'] <=> $a['Total']);
            $top10 = array_slice($topData, 0, 10);

            // Isi tabel mulai kolom G ke kanan
            $r = $chartStartRow + 2;
            foreach ($top10 as $val) {
                $sheet->setCellValue("G{$r}", $val['Kode']);
                $sheet->setCellValue("H{$r}", $val['Keterangan']);
                $sheet->setCellValue("I{$r}", $val['Total']);
                $r++;
            }

            // Range label & value (untuk chart)
            $labelRangeDonut = [new DataSeriesValues('String', "{$sheet->getTitle()}!H" . ($chartStartRow + 2) . ":H" . ($r - 1))];
            $valueRangeDonut = [new DataSeriesValues('Number', "{$sheet->getTitle()}!I" . ($chartStartRow + 2) . ":I" . ($r - 1))];

            $seriesDonut = new DataSeries(
                DataSeries::TYPE_DONUTCHART,
                null,
                range(0, count($valueRangeDonut) - 1),
                [],
                $labelRangeDonut,
                $valueRangeDonut
            );

            $layoutDonut = new Layout();
            $layoutDonut->setShowVal(true);
            $layoutDonut->setShowPercent(false);

            $plotAreaDonut = new PlotArea($layoutDonut, [$seriesDonut]);
            $legendDonut = new Legend(Legend::POSITION_RIGHT, null, false);
            $titleDonut = new Title('TOP 10 KODE REWORK');

            $chartDonut = new Chart('chart1', $titleDonut, $legendDonut, $plotAreaDonut);

            // === POSISI CHART DI KIRI ===
            $chartDonut->setTopLeftPosition('A' . ($chartStartRow + 1));
            $chartDonut->setBottomRightPosition('E' . ($chartStartRow + 20));
            $sheet->addChart($chartDonut);
            // // hitung baris akhir
            $endRow = $r - 1;

            // // Tambahkan chart
            // === BORDER + AUTOSIZE ===
            $sheet->getStyle("G{$chartStartRow}:I{$endRow}")
                ->getBorders()
                ->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN);

            foreach (range('G', 'H') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }


            // === CHART REPORT DATA BS PERHARI (PCS) ===
            // === MULAI DARI SINI ===
            $barStart = $chartStartRow + 22;

            // Judul tabel
            $sheet->setCellValue("G{$barStart}", "REPORT DATA BS PERHARI (PCS)");
            $sheet->mergeCells("G{$barStart}:H{$barStart}");
            $sheet->getStyle("G{$barStart}")->getFont()->setBold(true)->setSize(12);
            $sheet->getStyle("G{$barStart}")->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);

            // Header tabel
            $sheet->setCellValue("G" . ($barStart + 1), "Tanggal");
            $sheet->setCellValue("H" . ($barStart + 1), "Total Qty");

            // Isi data total per tanggal
            $r = $barStart + 2;
            foreach ($tanggalList as $tgl) {
                $sheet->setCellValueExplicit("G{$r}", $tgl, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $sheet->setCellValue("H{$r}", $totalPerTanggal[$tgl]);
                $r++;
            }

            // Hitung rata-rata (hanya di kolom H)
            $avgRow = $r;
            $sheet->setCellValue("G{$avgRow}", "AVERAGE");
            $sheet->setCellValue("H{$avgRow}", "=AVERAGE(H" . ($barStart + 2) . ":H" . ($r - 1) . ")");
            $sheet->getStyle("G{$avgRow}:H{$avgRow}")->getFont()->setBold(true);

            $barEnd = $r - 1; // baris terakhir data

            // === RANGE DATA ===
            // Kategori (vertikal axis) = tanggal
            $labelRange = [
                new DataSeriesValues(
                    DataSeriesValues::DATASERIES_TYPE_STRING, // <-- label teks (tanggal)
                    "'{$sheet->getTitle()}'!G" . ($barStart + 2) . ":G{$barEnd}"
                )
            ];

            // Nilai (horizontal axis) = qty
            $valueRange = [
                new DataSeriesValues(
                    DataSeriesValues::DATASERIES_TYPE_NUMBER, // <-- nilai numerik
                    "'{$sheet->getTitle()}'!H" . ($barStart + 2) . ":H{$barEnd}"
                )
            ];

            // === BAR CHART (horizontal, tanggal di vertikal, qty di horizontal) ===
            $seriesBar = new DataSeries(
                DataSeries::TYPE_BARCHART,              // 2D bar chart
                DataSeries::GROUPING_CLUSTERED,         // Kelompokkan bar
                range(0, count($valueRange) - 1),       // urutan seri
                [new DataSeriesValues('String', "'{$sheet->getTitle()}'!H" . ($barStart + 1))], // legend
                $labelRange,                            // kategori = tanggal (vertikal)
                $valueRange                             // nilai = qty (horizontal)
            );
            $seriesBar->setPlotDirection(DataSeries::DIRECTION_COL); // horizontal bar ✅

            // === LINE CHART untuk rata-rata ===
            $valueAvgRange = [
                new DataSeriesValues(
                    'Number',
                    "'{$sheet->getTitle()}'!H{$avgRow}",
                    null,
                    1
                )
            ];

            $seriesAvg = new DataSeries(
                DataSeries::TYPE_LINECHART,
                null,
                range(0, count($valueAvgRange) - 1),
                [new DataSeriesValues('String', '"Average"')],
                $labelRange,
                $valueAvgRange
            );

            // === PLOT AREA ===
            $layout = new Layout();
            $layout->setShowVal(true);
            $plotArea = new PlotArea($layout, [$seriesBar, $seriesAvg]);

            // === TITLE & LEGEND ===
            $title = new Title("REPORT DATA BS PERHARI (PCS)");
            $legend = new Legend(Legend::POSITION_BOTTOM, null, false);

            $chartBar = new Chart('chart2', $title, $legend, $plotArea);
            $chartBar->setTopLeftPosition('A' . ($barStart + 1));
            $chartBar->setBottomRightPosition('E' . ($barStart + 25));
            $sheet->addChart($chartBar);

            // === BORDER + AUTOSIZE ===
            $sheet->getStyle("G{$barStart}:H{$avgRow}")
                ->getBorders()
                ->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN);

            foreach (range('G', 'H') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
            $sheetIndex++;
        }

        // === Output ke Excel ===
        $writer = new Xlsx($spreadsheet);
        $writer->setIncludeCharts(true); // wajib supaya chart muncul
        $filename = 'Data Perbaikan ' . date('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }
    public function summaryGlobalPbArea()
    {
        $area  = $this->request->getPost('area') ?? '';
        $bulan = $this->request->getPost('bulan') ?? '';
        // 
        [$tahun, $bulanAngka] = explode('-', $bulan);
        $bulanText = date('F Y', strtotime($bulan . '-01'));
        $jumlahHari = cal_days_in_month(CAL_GREGORIAN, (int)$bulanAngka, (int)$tahun);

        $theData = [
            'area' => $area,
            'bulan' => $bulan,
        ];
        if (!$theData) {
            return redirect()->back()->with('error', 'Tidak ada data untuk diexport!');
        }

        $getData = $this->perbaikanAreaModel->getSummaryGlobalPerbaikan($theData);
        $getJlmc = $this->produksiModel->getJlmcByMonth($theData);

        // dd($getData);

        $groupedDataTotal = [];
        // 1️⃣ Hitung total qty per area
        foreach ($getData as $row) {
            $area = $row['area'];
            $qty  = (int)$row['qty'];

            if (!isset($groupedDataTotal[$area])) {
                $groupedDataTotal[$area] = [
                    'total_bs'        => 0,
                    'avg_mesin'       => 0,
                    'total_produksi'  => 0,
                ];
            }

            $groupedDataTotal[$area]['total_bs'] += $qty;
        }

        // 2️⃣ Gabungkan data jlmc ke dalam hasil
        foreach ($getJlmc as $row) {
            $area = $row['area'];
            $jlmc    = (int)($row['total_mc'] ?? 0);
            $qtyProd = (int)($row['qty_produksi'] ?? 0);

            if (!isset($groupedDataTotal[$area])) {
                $groupedDataTotal[$area] = [
                    'total_bs'        => 0,
                    'avg_mesin'       => 0,
                    'total_produksi'  => 0,
                ];
            }

            // Rata-rata jalan mesin per hari
            $groupedDataTotal[$area]['avg_mesin'] = $jlmc > 0 ? round($jlmc / $jumlahHari) : 0;
            $groupedDataTotal[$area]['total_produksi'] = $qtyProd > 0 ? round($qtyProd / 24) : 0; // jumlah mc dibagi total hari = rata rata jl mc
        }
        // 3️⃣ Hitung turunan tambahan
        foreach ($groupedDataTotal as $area => &$val) {
            $totalBs       = $val['total_bs'];
            $totalMc       = $val['avg_mesin'];
            $totalProduksi = $val['total_produksi'];

            // Total BS dalam DZ
            $val['bs_dz'] = $totalBs > 0 ? round($totalBs / 24) : 0;

            // Persentase BS terhadap produksi
            $val['percent_bs_dz'] = $val['bs_dz']  > 0 ? ($val['bs_dz'] / $totalProduksi) : 0;

            // Rata-rata BS per mesin (PCS)
            $val['avg_bs_bymc'] = $totalBs  > 0 || $totalMc > 0 ? round($totalBs / $totalMc) : 0;

            // Rata-rata BS per hari per mesin (PCS)
            $val['avg_bymc_day'] = $val['avg_bs_bymc']  > 0 ? round($val['avg_bs_bymc'] / $jumlahHari) : 0;
        }
        unset($val);

        // Urutkan berdasarkan nama area (key)
        ksort($groupedDataTotal);

        // 4️⃣ Hitung rata-rata keseluruhan
        $totalArea = 0;
        $totalAvgByMesin = 0;
        $totalAvgByMcDay = 0;

        foreach ($groupedDataTotal as $val) {
            if ($val['avg_bs_bymc'] > 0) {
                $totalAvgByMesin += $val['avg_bs_bymc'];
                $totalArea++;
            }
            if ($val['avg_bymc_day'] > 0) {
                $totalAvgByMcDay += $val['avg_bymc_day'];
            }
        }

        // Hindari pembagian nol
        $overallAvgByMesin = $totalArea > 0 ? round($totalAvgByMesin / $totalArea) : 0;
        $overallAvgByMcDay = $totalArea > 0 ? round($totalAvgByMcDay / $totalArea) : 0;


        // SUMMARY PER AREA PER TANGGAL
        $pivot = [];
        $areas = [];

        // --- 1. Susun data jadi per tanggal ---
        foreach ($getData as $row) {
            $tgl  = date('d.m.Y', strtotime($row['tgl_perbaikan']));
            $area = $row['area'];
            $qty  = (int) $row['qty'];

            // Simpan daftar area unik
            $areas[$area] = true;

            // Inisialisasi baris tanggal
            if (!isset($pivot[$tgl])) {
                $pivot[$tgl] = [];
            }

            // Isi qty per area
            $pivot[$tgl][$area] = ($pivot[$tgl][$area] ?? 0) + $qty;
        }

        // --- 2. Urutkan tanggal ---
        ksort($pivot);
        $areas = array_keys($areas);
        sort($areas);

        // --- 3. Hitung Grand Total per baris ---
        foreach ($pivot as $tgl => &$areasData) {
            $grandTotal = 0;
            foreach ($areas as $a) {
                $grandTotal += $areasData[$a] ?? 0;
            }
            $areasData['Grand Total'] = $grandTotal;
        }
        unset($areasData);

        // --- 4. Tambahkan baris total keseluruhan (Grand Total bawah) ---
        $bottomTotal = array_fill_keys($areas, 0);
        $bottomGrand = 0;

        foreach ($pivot as $tgl => $areasData) {
            foreach ($areas as $a) {
                $bottomTotal[$a] += $areasData[$a] ?? 0;
            }
            $bottomGrand += $areasData['Grand Total'];
        }

        $bottomTotal['Grand Total'] = $bottomGrand;
        $pivot['Grand Total'] = $bottomTotal;


        // dd($theData, $getData, $getJlmc, $groupedDataTotal, $jumlahHari);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Summary Global');

        $sheet->mergeCells("A1:H1");
        $sheet->setCellValue('A1', 'LAPORAN RESUME PERBAIKAN AREA');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells("A2:H2");
        $sheet->setCellValue('A2', 'Periode ' . $bulanText);
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // --- Header ---
        $row = 5;
        $headers = [
            'A' => 'AREA',
            'B' => 'RATA2 JALAN MC',
            'C' => 'TOTAL PRODUKSI PER BULAN (PCS)',
            'D' => 'TOTAL BS PER BULAN (DZ)',
            'E' => '% BS PER BULAN (DZ)',
            'F' => 'TOTAL BS PER BULAN (PCS)',
            'G' => 'RATA2 BS PER BULAN PER MESIN (PCS)',
            'H' => 'RATA2 BS PER HARI PER MESIN (PCS)',
        ];

        // Set header text
        foreach ($headers as $col => $text) {
            $sheet->setCellValue("{$col}{$row}", $text);
        }

        // --- Style header ---
        $headerRange = "A{$row}:H{$row}";

        // Bold font & center alignment
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true, // bungkus teks agar tidak kepanjangan
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

        // --- Auto-size semua kolom ---
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setWidth(12);
        }

        // --- Optional: tinggi baris biar teks wrap lebih rapi ---
        $sheet->getRowDimension($row)->setRowHeight(60);

        // --- BODY (DATA) ---
        $row++;
        foreach ($groupedDataTotal as $area => $val) {
            $sheet->setCellValue("A{$row}", $area);
            $sheet->setCellValue("B{$row}", $val['avg_mesin']);
            $sheet->setCellValue("C{$row}", $val['total_produksi']);
            $sheet->setCellValue("D{$row}", $val['bs_dz']);
            $sheet->setCellValue("E{$row}", $val['percent_bs_dz']);
            $sheet->setCellValue("F{$row}", $val['total_bs']);
            $sheet->setCellValue("G{$row}", $val['avg_bs_bymc']);
            $sheet->setCellValue("H{$row}", $val['avg_bymc_day']);
            $row++;
        }
        // --- STYLE BODY ---
        $bodyRange = "A6:H" . ($row - 1);
        $sheet->getStyle($bodyRange)->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);
        // --- Set tinggi baris 40 untuk semua baris body ---
        for ($r = 6; $r < $row; $r++) {
            $sheet->getRowDimension($r)->setRowHeight(40);
        }


        // Format angka kolom B sampai H jadi rata kanan
        $sheet->getStyle("B6:H" . ($row - 1))
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // --- OPSIONAL: SET FORMAT PERSENTASE untuk kolom E ---
        $sheet->getStyle("E6:E" . ($row - 1))
            ->getNumberFormat()
            ->setFormatCode('0%');


        // Tambahkan baris TOTAL
        $sheet->mergeCells("A{$row}:F{$row}");
        $sheet->setCellValue("A{$row}", 'RATA RATA');

        // Isi kolom berikutnya (G sampai M misalnya)
        $sheet->setCellValue("G{$row}", $overallAvgByMesin); // total_bs
        $sheet->setCellValue("H{$row}", $overallAvgByMcDay); // avg_mesin

        // Buat semua kolom dari A sampai M jadi tebal
        $sheet->getStyle("A{$row}:H{$row}")->getFont()->setBold(true);

        // Rata tengah semua cell biar rapi
        $sheet->getStyle("A{$row}:H{$row}")
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        // (Opsional) kasih border biar jelas
        $sheet->getStyle("A{$row}:H{$row}")
            ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        // --- Optional: tinggi baris biar teks wrap lebih rapi ---
        $sheet->getRowDimension($row)->setRowHeight(40);



        // CHART 1 
        // Tentukan range data
        $dataStartRow = 6;              // baris pertama data
        $dataEndRow   = $row - 1;       // baris terakhir data

        // Label di atas batang (judul kolom G)
        $dataSeriesLabels = [
            new DataSeriesValues('String', "'Summary Global'!\$G\$5", null, 1),
        ];

        // Kategori sumbu X (nama AREA)
        $xAxisTickValues = [
            new DataSeriesValues('String', "'Summary Global'!\$A\${$dataStartRow}:\$A\${$dataEndRow}", null, ($dataEndRow - $dataStartRow + 1)),
        ];

        // Nilai batang (RATA2 BS PER MESIN)
        $dataSeriesValues = [
            new DataSeriesValues('Number', "'Summary Global'!\$G\${$dataStartRow}:\$G\${$dataEndRow}", null, ($dataEndRow - $dataStartRow + 1)),
        ];

        // Definisikan jenis chart: Bar Chart (vertikal)
        $series = new DataSeries(
            DataSeries::TYPE_BARCHART,
            DataSeries::GROUPING_CLUSTERED,
            range(0, count($dataSeriesValues) - 1),
            $dataSeriesLabels,
            $xAxisTickValues,
            $dataSeriesValues
        );
        $series->setPlotDirection(DataSeries::DIRECTION_COL);

        // Buat plot area
        $layout = new Layout();
        $layout->setShowVal(true);
        $plotArea = new PlotArea($layout, [$series]);

        // Judul chart
        $title = new Title('RATA-RATA PERBAIKAN PER AREA PER MC (PCS)');
        // $legend = new Legend(Legend::POSITION_BOTTOM, null, false);

        // Buat chart
        $chart = new Chart(
            'chart1', // ID
            $title,
            null,
            $plotArea,
            true,
            0,
            null,
            null
        );

        // Posisi chart di sheet
        $chart->setTopLeftPosition('J5');
        $chart->setBottomRightPosition('R10');

        // Tambahkan ke sheet
        $sheet->addChart($chart);

        // CHAR KE 2
        // === DATA RANGE ===
        $dataStartRow = 6;              // baris pertama data
        $dataEndRow   = $row - 1;       // baris terakhir data

        // Label legend (judul kolom F)
        $dataSeriesLabels = [
            new DataSeriesValues('String', "'Summary Global'!\$F\$5", null, 1),
        ];

        // Kategori sumbu X (nama AREA)
        $xAxisTickValues = [
            new DataSeriesValues(
                'String',
                "'Summary Global'!\$A\${$dataStartRow}:\$A\${$dataEndRow}",
                null,
                ($dataEndRow - $dataStartRow + 1)
            ),
        ];

        // Nilai batang (TOTAL BS PER BULAN - PCS)
        $dataSeriesValues = [
            new DataSeriesValues(
                'Number',
                "'Summary Global'!\$F\${$dataStartRow}:\$F\${$dataEndRow}",
                null,
                ($dataEndRow - $dataStartRow + 1)
            ),
        ];

        // === BAR CHART ===
        $series = new DataSeries(
            DataSeries::TYPE_BARCHART,          // tipe bar chart
            DataSeries::GROUPING_CLUSTERED,     // grup batang sejajar
            range(0, count($dataSeriesValues) - 1),
            $dataSeriesLabels,                  // legend (judul kolom)
            $xAxisTickValues,                   // kategori (area)
            $dataSeriesValues                   // nilai batang
        );

        // Arah batang: vertikal
        $series->setPlotDirection(DataSeries::DIRECTION_COL);

        // --- Layout agar ada angka di atas batang ---
        $layout = new Layout();
        $layout->setShowVal(true);

        // --- Plot area ---
        $plotArea = new PlotArea($layout, [$series]);

        // --- Judul chart ---
        $title = new Title('TOTAL PERBAIKAN PER BULAN (PCS)');
        // $legend = new Legend(Legend::POSITION_BOTTOM, null, false);

        // --- Buat chart ---
        $chart2 = new Chart(
            'chart_total_bs',     // ID unik
            $title,
            null,
            $plotArea,
            true,
            0,
            null,
            null
        );

        // Posisi chart di sheet (atur sesuai sel kosong)
        $chart2->setTopLeftPosition('J11');  // posisi kiri atas
        $chart2->setBottomRightPosition('R17'); // posisi kanan bawah

        // Tambahkan ke sheet
        $sheet->addChart($chart2);


        // SUMMARY PER AREA PER TANGGAL
        $row2 = $row + 4;
        $sheet->mergeCells("A{$row2}:H{$row2}");
        $sheet->setCellValue("A{$row2}", 'SUMMARY DATA PERBAIKAN PER AREA PER TANGGAL');
        $sheet->getStyle("A{$row2}")->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle("A{$row2}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $row2++;

        $sheet->mergeCells("A{$row2}:H{$row2}");
        $sheet->setCellValue("A{$row2}", 'Periode ' . $bulanText);
        $sheet->getStyle("A{$row2}")->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle("A{$row2}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $row2++;

        $firstRow = $row2;
        // --- Header berdasarkan $areas + "Date" + "Grand Total" ---
        $sheet->setCellValue("A{$row2}", "Date");
        $colIndex = 1;
        foreach ($areas as $a) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1);
            $sheet->setCellValue("{$col}{$row2}", $a);
            $colIndex++;
        }
        $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($areas) + 2);
        $sheet->setCellValue("{$lastCol}{$row2}", "Grand Total");

        // --- Style header ---
        $sheet->getStyle("A{$row2}:{$lastCol}{$row2}")->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        // --- Auto-width kolom ---
        foreach (range('A', $lastCol) as $col) {
            $sheet->getColumnDimension($col)->setWidth(12);
        }

        // --- Body Data ---
        $row2++;
        foreach ($pivot as $tgl => $areasData) {
            $sheet->setCellValue("A{$row2}", $tgl);
            $colIndex = 1;
            foreach ($areas as $a) {
                $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1);
                $sheet->setCellValue("{$col}{$row2}", $areasData[$a] ?? '');
                $colIndex++;
            }
            $sheet->setCellValue("{$lastCol}{$row2}", $areasData['Grand Total'] ?? '');
            $row2++;
        }

        // --- Style body ---
        $sheet->getStyle("A{$firstRow}:{$lastCol}" . ($row2 - 1))->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        // --- Rata kanan untuk kolom data (B sampai lastCol) ---
        $sheet->getStyle("B4:{$lastCol}" . ($row2 - 1))
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // --- Bold semua kolom Grand Total ---
        $sheet->getStyle("{$lastCol}" . ($firstRow + 1) . ":{$lastCol}" . ($row2 - 1))
            ->getFont()
            ->setBold(true);
        $sheet->getStyle("A" . ($row2 - 1) . ":{$lastCol}" . ($row2 - 1))
            ->getFont()
            ->setBold(true);

        // === Output ke Excel ===
        $writer = new Xlsx($spreadsheet);
        $writer->setIncludeCharts(true); // wajib supaya chart muncul
        $filename = 'Summary Global Perbaikan ' . date('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }
    public function exportExcelDeffect()
    {
        $awal  = $this->request->getPost('awal') ?? '';
        $akhir = $this->request->getPost('akhir') ?? '';
        $pdk   = $this->request->getPost('pdk') ?? '';
        $area  = $this->request->getPost('area') ?? '';
        $buyer = $this->request->getPost('buyer') ?? '';

        $theData = [
            'awal' => $awal,
            'akhir' => $akhir,
            'pdk' => $pdk,
            'area' => $area,
            'buyer' => $buyer,
        ];
        if (!$theData) {
            return redirect()->back()->with('error', 'Tidak ada data untuk diexport!');
        }

        $getData = $this->bsModel->getDataBsFilter($theData);
        // Ubah kolom 'area' jadi kapital semua
        foreach ($getData as &$upper) {
            if (isset($upper['area'])) {
                $upper['area'] = strtoupper($upper['area']);
            }
        }
        unset($upper); // good practice

        // 1️⃣ GROUP BY AREA, KODE DEFFECT, DAN TANGGAL
        $groupedData = [];

        // Loop data mentah
        foreach ($getData as $row) {
            $area        = $row['area'];
            $kode        = $row['kode_deffect'];
            $tgl         = $row['tgl_instocklot'];
            $keterangan  = $row['Keterangan'];
            $qty         = (int)$row['qty'];

            // Inisialisasi array bertingkat
            if (!isset($groupedData[$area])) {
                $groupedData[$area] = [];
            }

            if (!isset($groupedData[$area][$kode])) {
                $groupedData[$area][$kode] = [
                    'Keterangan' => $keterangan,
                    'Tanggal'    => []
                ];
            }

            if (!isset($groupedData[$area][$kode]['Tanggal'][$tgl])) {
                $groupedData[$area][$kode]['Tanggal'][$tgl] = 0;
            }

            // Tambahkan qty ke tanggal yang sesuai
            $groupedData[$area][$kode]['Tanggal'][$tgl] += $qty;
        }
        // Setelah semua data terkumpul
        ksort($groupedData); // urutkan area A-Z

        // Kalau mau juga urutkan kode di dalam tiap area:
        foreach ($groupedData as &$kodeList) {
            ksort($kodeList);
        }
        unset($kodeList); // hapus referensi

        // 2️⃣ KUMPULKAN SEMUA TANGGAL UNIK (buat header tabel nanti)
        $tanggalList = [];
        foreach ($getData as $row) {
            $tanggalList[$row['tgl_instocklot']] = true;
        }
        $tanggalList = array_keys($tanggalList);
        sort($tanggalList); // biar tanggal urut

        // dd($theData, $groupedData);


        // excel dimulai
        $spreadsheet = new Spreadsheet();

        $sheetIndex = 0;
        foreach ($groupedData as $areaName => $defects) {

            // Buat sheet baru per area (kecuali sheet pertama)
            if ($sheetIndex > 0) {
                $spreadsheet->createSheet();
            }
            $sheet = $spreadsheet->setActiveSheetIndex($sheetIndex);
            $sheet->setTitle(substr($areaName, 0, 31)); // Excel limit 31 char

            // === Judul ===
            $lastCol = Coordinate::stringFromColumnIndex(2 + count($tanggalList));
            $sheet->mergeCells("A1:B1");
            $sheet->setCellValue('A1', 'LAPORAN SUMMARY REJECT AREA ' . strtoupper($areaName));
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // === Header ===
            $sheet->setCellValue('A3', 'Kode Deffect');
            $sheet->setCellValue('B3', 'Keterangan');

            $col = 'C';
            foreach ($tanggalList as $tgl) {
                $sheet->setCellValue($col . '3', date('d-m-Y', strtotime($tgl)));
                $col++;
            }

            // Tambah kolom Grand Total
            $sheet->setCellValue($col . '3', 'Grand Total');
            $lastCol = Coordinate::stringFromColumnIndex(3 + count($tanggalList));

            // Header Style
            $headerRange = "A3:{$lastCol}3";
            $sheet->getStyle($headerRange)->getFont()->setBold(true);
            // $sheet->getStyle($headerRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('4472C4'); // biru
            $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // === Isi Data ===
            $rowNum = 4;
            $totalPerTanggal = array_fill_keys($tanggalList, 0); // Untuk total bawah
            $grandTotalSemua = 0;

            foreach ($defects as $kode => $defData) {
                $sheet->setCellValue('A' . $rowNum, $kode);
                $sheet->setCellValue('B' . $rowNum, $defData['Keterangan']);

                $col = 'C';
                $totalPerKode = 0;
                foreach ($tanggalList as $tgl) {
                    $qty = $defData['Tanggal'][$tgl] ?? '';
                    $sheet->setCellValue($col . $rowNum, $qty);
                    // 
                    $qtyRaw = $defData['Tanggal'][$tgl] ?? 0;
                    $qty = is_numeric($qtyRaw) ? (int)$qtyRaw : 0;
                    $totalPerTanggal[$tgl] += $qty;
                    $totalPerKode += $qty;

                    $col++;
                }
                // Kolom terakhir = Grand Total per kode defect
                $sheet->setCellValue($col . $rowNum, $totalPerKode);
                $sheet->getStyle($col . $rowNum)->getFont()->setBold(true);

                $grandTotalSemua += $totalPerKode;
                $rowNum++;
            }
            // === Tambahkan Baris Grand Total di Bawah ===
            $sheet->setCellValue('A' . $rowNum, 'Grand Total');
            $sheet->mergeCells("A{$rowNum}:B{$rowNum}");
            $col = 'C';
            foreach ($tanggalList as $tgl) {
                $sheet->setCellValue($col . $rowNum, $totalPerTanggal[$tgl]);
                $col++;
            }
            $sheet->setCellValue($col . $rowNum, $grandTotalSemua);


            // Bold & Center Grand Total row
            $grandRange = "A{$rowNum}:{$lastCol}{$rowNum}";
            $sheet->getStyle($grandRange)->getFont()->setBold(true);
            $sheet->getStyle($grandRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // === BORDER SEMUA KOLOM ===
            $dataRange = "A3:{$lastCol}{$rowNum}";
            $styleArray = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => '000000'],
                    ],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                ],
            ];
            $sheet->getStyle($dataRange)->applyFromArray($styleArray);

            // === AUTO SIZE ===
            foreach (range('A', $lastCol) as $columnID) {
                $sheet->getColumnDimension($columnID)->setAutoSize(true);
            }

            // === LAPORAN DEFFECT SECTION ===
            $laporanStart = $rowNum + 2;

            // Judul "LAPORAN DEFFECT"
            $sheet->setCellValue("A{$laporanStart}", 'LAPORAN REJECT');
            $sheet->mergeCells("A{$laporanStart}:C{$laporanStart}");
            $sheet->getStyle("A{$laporanStart}")->getFont()->setBold(true)->setUnderline(true)->setSize(12);

            // Header kecil PCS & DZ
            $sheet->setCellValue("B" . ($laporanStart + 1), 'PCS');
            $sheet->setCellValue("C" . ($laporanStart + 1), 'DZ');

            // TOTAL PER BULAN
            $sheet->setCellValue("A" . ($laporanStart + 2), 'TOTAL PER BULAN');
            $sheet->setCellValue("B" . ($laporanStart + 2), $grandTotalSemua); // kosong untuk PCS
            $sheet->setCellValue("C" . ($laporanStart + 2), round($grandTotalSemua / 24, 2));  // bisa isi hitungan nanti

            // RATA RATA PER HARI
            $avgPerHari = $grandTotalSemua / count($tanggalList);
            $sheet->setCellValue("A" . ($laporanStart + 3), 'RATA RATA PER HARI');
            $sheet->setCellValue("B" . ($laporanStart + 3), $avgPerHari);
            $sheet->setCellValue("C" . ($laporanStart + 3), round($avgPerHari / 24, 2));

            // Styling border & rata tengah
            $sheet->getStyle("A{$laporanStart}:C" . ($laporanStart + 3))
                ->getBorders()
                ->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN);

            $sheet->getStyle("A{$laporanStart}:C" . ($laporanStart + 3))
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);

            // Bold header baris pertama
            $sheet->getStyle("A{$laporanStart}")->getFont()->setBold(true);


            // === TOP 10 CHART DI BAWAH DATA ===
            $chartStartRow = $laporanStart + 6;

            // Judul bagian
            $sheet->setCellValue("G{$chartStartRow}", 'TOP 10 KODE REJECT');
            $sheet->mergeCells("G{$chartStartRow}:I{$chartStartRow}");
            $sheet->getStyle("G{$chartStartRow}")->getFont()->setBold(true)->setSize(12);
            $sheet->getStyle("G{$chartStartRow}")->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);

            // Header tabel di kanan
            $sheet->setCellValue("G" . ($chartStartRow + 1), 'Kode');
            $sheet->setCellValue("H" . ($chartStartRow + 1), 'Keterangan');
            $sheet->setCellValue("I" . ($chartStartRow + 1), 'Total Qty');

            // Hitung total per kode
            $topData = [];
            foreach ($defects as $kode => $val) {
                $totalQty = array_sum($val['Tanggal']);
                $topData[$kode] = [
                    'Kode' => $kode,
                    'Keterangan' => $val['Keterangan'],
                    'Total' => $totalQty
                ];
            }

            // Urutkan besar ke kecil, ambil 10
            usort($topData, fn($a, $b) => $b['Total'] <=> $a['Total']);
            $top10 = array_slice($topData, 0, 10);

            // Isi tabel mulai kolom G ke kanan
            $r = $chartStartRow + 2;
            foreach ($top10 as $val) {
                $sheet->setCellValue("G{$r}", $val['Kode']);
                $sheet->setCellValue("H{$r}", $val['Keterangan']);
                $sheet->setCellValue("I{$r}", $val['Total']);
                $r++;
            }

            // Range label & value (untuk chart)
            $labelRange = [new DataSeriesValues('String', "{$sheet->getTitle()}!H" . ($chartStartRow + 2) . ":H" . ($r - 1))];
            $valueRange = [new DataSeriesValues('Number', "{$sheet->getTitle()}!I" . ($chartStartRow + 2) . ":I" . ($r - 1))];

            $series = new DataSeries(
                DataSeries::TYPE_DONUTCHART,
                null,
                range(0, count($valueRange) - 1),
                [],
                $labelRange,
                $valueRange
            );

            $layout = new Layout();
            $layout->setShowVal(true);
            $layout->setShowPercent(true);

            $plotArea = new PlotArea($layout, [$series]);
            $legend = new Legend(Legend::POSITION_RIGHT, null, false);
            $title = new Title('TOP 10 KODE REJECT');

            $chart = new Chart(
                'chart1',
                $title,
                $legend,
                $plotArea
            );

            // === POSISI CHART DI KIRI ===
            $chart->setTopLeftPosition('A' . ($chartStartRow + 1));
            $chart->setBottomRightPosition('E' . ($chartStartRow + 20));
            // hitung baris akhir
            $endRow = $r - 1;

            // Tambahkan chart
            $sheet->addChart($chart);
            // === BORDER + AUTOSIZE ===
            $sheet->getStyle("G{$chartStartRow}:I{$endRow}")
                ->getBorders()
                ->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN);

            foreach (range('G', 'H') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }


            // === CHART REPORT DATA BS PERHARI (PCS) ===
            // === MULAI DARI SINI ===
            $barStart = $chartStartRow + 22;

            // Judul tabel
            $sheet->setCellValue("G{$barStart}", "REPORT DATA BS PERHARI (PCS)");
            $sheet->mergeCells("G{$barStart}:H{$barStart}");
            $sheet->getStyle("G{$barStart}")->getFont()->setBold(true)->setSize(12);
            $sheet->getStyle("G{$barStart}")->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);

            // Header tabel
            $sheet->setCellValue("G" . ($barStart + 1), "Tanggal");
            $sheet->setCellValue("H" . ($barStart + 1), "Total Qty");

            // Isi data total per tanggal
            $r = $barStart + 2;
            foreach ($tanggalList as $tgl) {
                $sheet->setCellValueExplicit("G{$r}", $tgl, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $sheet->setCellValue("H{$r}", $totalPerTanggal[$tgl]);
                $r++;
            }

            // Hitung rata-rata (hanya di kolom H)
            $avgRow = $r;
            $sheet->setCellValue("G{$avgRow}", "AVERAGE");
            $sheet->setCellValue("H{$avgRow}", "=AVERAGE(H" . ($barStart + 2) . ":H" . ($r - 1) . ")");
            $sheet->getStyle("G{$avgRow}:H{$avgRow}")->getFont()->setBold(true);

            $barEnd = $r - 1; // baris terakhir data

            // === RANGE DATA ===
            // Kategori (vertikal axis) = tanggal
            $labelRange = [
                new DataSeriesValues(
                    DataSeriesValues::DATASERIES_TYPE_STRING, // <-- label teks (tanggal)
                    "'{$sheet->getTitle()}'!G" . ($barStart + 2) . ":G{$barEnd}"
                )
            ];

            // Nilai (horizontal axis) = qty
            $valueRange = [
                new DataSeriesValues(
                    DataSeriesValues::DATASERIES_TYPE_NUMBER, // <-- nilai numerik
                    "'{$sheet->getTitle()}'!H" . ($barStart + 2) . ":H{$barEnd}"
                )
            ];

            // === BAR CHART (horizontal, tanggal di vertikal, qty di horizontal) ===
            $seriesBar = new DataSeries(
                DataSeries::TYPE_BARCHART,              // 2D bar chart
                DataSeries::GROUPING_CLUSTERED,         // Kelompokkan bar
                range(0, count($valueRange) - 1),       // urutan seri
                [new DataSeriesValues('String', "'{$sheet->getTitle()}'!H" . ($barStart + 1))], // legend
                $labelRange,                            // kategori = tanggal (vertikal)
                $valueRange                             // nilai = qty (horizontal)
            );
            $seriesBar->setPlotDirection(DataSeries::DIRECTION_COL); // horizontal bar ✅

            // === LINE CHART untuk rata-rata ===
            $valueAvgRange = [
                new DataSeriesValues(
                    'Number',
                    "'{$sheet->getTitle()}'!H{$avgRow}",
                    null,
                    1
                )
            ];

            $seriesAvg = new DataSeries(
                DataSeries::TYPE_LINECHART,
                null,
                range(0, count($valueAvgRange) - 1),
                [new DataSeriesValues('String', '"Average"')],
                $labelRange,
                $valueAvgRange
            );

            // === PLOT AREA ===
            $layout = new Layout();
            $layout->setShowVal(true);
            $plotArea = new PlotArea($layout, [$seriesBar, $seriesAvg]);

            // === TITLE & LEGEND ===
            $title = new Title("REPORT DATA BS PERHARI (PCS)");
            $legend = new Legend(Legend::POSITION_BOTTOM, null, false);

            // === CHART ===
            $chart = new Chart(
                'chart2',
                $title,
                $legend,
                $plotArea
            );

            // Posisi chart di kiri (A–E)
            $chart->setTopLeftPosition('A' . ($barStart + 1));
            $chart->setBottomRightPosition('E' . ($barStart + 25));

            $sheet->addChart($chart);

            // === BORDER + AUTOSIZE ===
            $sheet->getStyle("G{$barStart}:H{$avgRow}")
                ->getBorders()
                ->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN);

            foreach (range('G', 'H') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
            $sheetIndex++;
        }

        // === Output ke Excel ===
        $writer = new Xlsx($spreadsheet);
        $writer->setIncludeCharts(true); // wajib supaya chart muncul
        $filename = 'Data Reject ' . date('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }
    public function summaryGlobalDeffectArea()
    {
        $area  = $this->request->getPost('area') ?? '';
        $bulan = $this->request->getPost('bulan') ?? '';
        // 
        [$tahun, $bulanAngka] = explode('-', $bulan);
        $bulanText = date('F Y', strtotime($bulan . '-01'));
        $jumlahHari = cal_days_in_month(CAL_GREGORIAN, (int)$bulanAngka, (int)$tahun);

        $theData = [
            'area' => $area,
            'bulan' => $bulan,
        ];
        if (!$theData) {
            return redirect()->back()->with('error', 'Tidak ada data untuk diexport!');
        }

        $getData = $this->bsModel->getSummaryGlobalDeffect($theData);
        $getJlmc = $this->produksiModel->getJlmcByMonth($theData);

        // dd($getData);

        $groupedDataTotal = [];
        // 1️⃣ Hitung total qty per area
        foreach ($getData as $row) {
            $area = $row['area'];
            $qty  = (int)$row['qty'];

            if (!isset($groupedDataTotal[$area])) {
                $groupedDataTotal[$area] = [
                    'total_bs'        => 0,
                    'avg_mesin'       => 0,
                    'total_produksi'  => 0,
                ];
            }

            $groupedDataTotal[$area]['total_bs'] += $qty;
        }

        // 2️⃣ Gabungkan data jlmc ke dalam hasil
        foreach ($getJlmc as $row) {
            $area = $row['area'];
            $jlmc    = (int)($row['total_mc'] ?? 0);
            $qtyProd = (int)($row['qty_produksi'] ?? 0);

            if (!isset($groupedDataTotal[$area])) {
                $groupedDataTotal[$area] = [
                    'total_bs'        => 0,
                    'avg_mesin'       => 0,
                    'total_produksi'  => 0,
                ];
            }

            // Rata-rata jalan mesin per hari
            $groupedDataTotal[$area]['avg_mesin'] = $jlmc > 0 ? round($jlmc / $jumlahHari) : 0;
            $groupedDataTotal[$area]['total_produksi'] = $qtyProd > 0 ? round($qtyProd / 24) : 0; // jumlah mc dibagi total hari = rata rata jl mc
        }
        // 3️⃣ Hitung turunan tambahan
        foreach ($groupedDataTotal as $area => &$val) {
            $totalBs       = $val['total_bs'];
            $totalMc       = $val['avg_mesin'];
            $totalProduksi = $val['total_produksi'];

            // Total BS dalam DZ
            $val['bs_dz'] = $totalBs > 0 ? round($totalBs / 24) : 0;

            // Persentase BS terhadap produksi
            $val['percent_bs_dz'] = $val['bs_dz']  > 0 ? ($val['bs_dz'] / $totalProduksi) : 0;

            // Rata-rata BS per mesin (PCS)
            $val['avg_bs_bymc'] = $totalBs  > 0 || $totalMc > 0 ? round($totalBs / $totalMc) : 0;

            // Rata-rata BS per hari per mesin (PCS)
            $val['avg_bymc_day'] = $val['avg_bs_bymc']  > 0 ? round($val['avg_bs_bymc'] / $jumlahHari) : 0;
        }
        unset($val);

        // Urutkan berdasarkan nama area (key)
        ksort($groupedDataTotal);

        // 4️⃣ Hitung rata-rata keseluruhan
        $totalArea = 0;
        $totalAvgByMesin = 0;
        $totalAvgByMcDay = 0;

        foreach ($groupedDataTotal as $val) {
            if ($val['avg_bs_bymc'] > 0) {
                $totalAvgByMesin += $val['avg_bs_bymc'];
                $totalArea++;
            }
            if ($val['avg_bymc_day'] > 0) {
                $totalAvgByMcDay += $val['avg_bymc_day'];
            }
        }

        // Hindari pembagian nol
        $overallAvgByMesin = $totalArea > 0 ? round($totalAvgByMesin / $totalArea) : 0;
        $overallAvgByMcDay = $totalArea > 0 ? round($totalAvgByMcDay / $totalArea) : 0;


        // SUMMARY PER AREA PER TANGGAL
        $pivot = [];
        $areas = [];

        // --- 1. Susun data jadi per tanggal ---
        foreach ($getData as $row) {
            $tgl  = date('d.m.Y', strtotime($row['tgl_instocklot']));
            $area = $row['area'];
            $qty  = (int) $row['qty'];

            // Simpan daftar area unik
            $areas[$area] = true;

            // Inisialisasi baris tanggal
            if (!isset($pivot[$tgl])) {
                $pivot[$tgl] = [];
            }

            // Isi qty per area
            $pivot[$tgl][$area] = ($pivot[$tgl][$area] ?? 0) + $qty;
        }

        // --- 2. Urutkan tanggal ---
        ksort($pivot);
        $areas = array_keys($areas);
        sort($areas);

        // --- 3. Hitung Grand Total per baris ---
        foreach ($pivot as $tgl => &$areasData) {
            $grandTotal = 0;
            foreach ($areas as $a) {
                $grandTotal += $areasData[$a] ?? 0;
            }
            $areasData['Grand Total'] = $grandTotal;
        }
        unset($areasData);

        // --- 4. Tambahkan baris total keseluruhan (Grand Total bawah) ---
        $bottomTotal = array_fill_keys($areas, 0);
        $bottomGrand = 0;

        foreach ($pivot as $tgl => $areasData) {
            foreach ($areas as $a) {
                $bottomTotal[$a] += $areasData[$a] ?? 0;
            }
            $bottomGrand += $areasData['Grand Total'];
        }

        $bottomTotal['Grand Total'] = $bottomGrand;
        $pivot['Grand Total'] = $bottomTotal;


        // dd($theData, $getData, $getJlmc, $groupedDataTotal, $jumlahHari);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Summary Global');

        $sheet->mergeCells("A1:H1");
        $sheet->setCellValue('A1', 'LAPORAN RESUME BS AFTER KNITTIN');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells("A2:H2");
        $sheet->setCellValue('A2', 'Periode ' . $bulanText);
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // --- Header ---
        $row = 5;
        $headers = [
            'A' => 'AREA',
            'B' => 'RATA2 JALAN MC',
            'C' => 'TOTAL PRODUKSI PER BULAN (PCS)',
            'D' => 'TOTAL BS PER BULAN (DZ)',
            'E' => '% BS PER BULAN (DZ)',
            'F' => 'TOTAL BS PER BULAN (PCS)',
            'G' => 'RATA2 BS PER BULAN PER MESIN (PCS)',
            'H' => 'RATA2 BS PER HARI PER MESIN (PCS)',
        ];

        // Set header text
        foreach ($headers as $col => $text) {
            $sheet->setCellValue("{$col}{$row}", $text);
        }

        // --- Style header ---
        $headerRange = "A{$row}:H{$row}";

        // Bold font & center alignment
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true, // bungkus teks agar tidak kepanjangan
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

        // --- Auto-size semua kolom ---
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setWidth(12);
        }

        // --- Optional: tinggi baris biar teks wrap lebih rapi ---
        $sheet->getRowDimension($row)->setRowHeight(60);

        // --- BODY (DATA) ---
        $row++;
        foreach ($groupedDataTotal as $area => $val) {
            $sheet->setCellValue("A{$row}", $area);
            $sheet->setCellValue("B{$row}", $val['avg_mesin']);
            $sheet->setCellValue("C{$row}", $val['total_produksi']);
            $sheet->setCellValue("D{$row}", $val['bs_dz']);
            $sheet->setCellValue("E{$row}", $val['percent_bs_dz']);
            $sheet->setCellValue("F{$row}", $val['total_bs']);
            $sheet->setCellValue("G{$row}", $val['avg_bs_bymc']);
            $sheet->setCellValue("H{$row}", $val['avg_bymc_day']);
            $row++;
        }
        // --- STYLE BODY ---
        $bodyRange = "A6:H" . ($row - 1);
        $sheet->getStyle($bodyRange)->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);
        // --- Set tinggi baris 40 untuk semua baris body ---
        for ($r = 6; $r < $row; $r++) {
            $sheet->getRowDimension($r)->setRowHeight(40);
        }


        // Format angka kolom B sampai H jadi rata kanan
        $sheet->getStyle("B6:H" . ($row - 1))
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // --- OPSIONAL: SET FORMAT PERSENTASE untuk kolom E ---
        $sheet->getStyle("E6:E" . ($row - 1))
            ->getNumberFormat()
            ->setFormatCode('0%');


        // Tambahkan baris TOTAL
        $sheet->mergeCells("A{$row}:F{$row}");
        $sheet->setCellValue("A{$row}", 'RATA RATA');

        // Isi kolom berikutnya (G sampai M misalnya)
        $sheet->setCellValue("G{$row}", $overallAvgByMesin); // total_bs
        $sheet->setCellValue("H{$row}", $overallAvgByMcDay); // avg_mesin

        // Buat semua kolom dari A sampai M jadi tebal
        $sheet->getStyle("A{$row}:H{$row}")->getFont()->setBold(true);

        // Rata tengah semua cell biar rapi
        $sheet->getStyle("A{$row}:H{$row}")
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        // (Opsional) kasih border biar jelas
        $sheet->getStyle("A{$row}:H{$row}")
            ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        // --- Optional: tinggi baris biar teks wrap lebih rapi ---
        $sheet->getRowDimension($row)->setRowHeight(40);



        // CHART 1 
        // Tentukan range data
        $dataStartRow = 6;              // baris pertama data
        $dataEndRow   = $row - 1;       // baris terakhir data

        // Label di atas batang (judul kolom G)
        $dataSeriesLabels = [
            new DataSeriesValues('String', "'Summary Global'!\$G\$5", null, 1),
        ];

        // Kategori sumbu X (nama AREA)
        $xAxisTickValues = [
            new DataSeriesValues('String', "'Summary Global'!\$A\${$dataStartRow}:\$A\${$dataEndRow}", null, ($dataEndRow - $dataStartRow + 1)),
        ];

        // Nilai batang (RATA2 BS PER MESIN)
        $dataSeriesValues = [
            new DataSeriesValues('Number', "'Summary Global'!\$G\${$dataStartRow}:\$G\${$dataEndRow}", null, ($dataEndRow - $dataStartRow + 1)),
        ];

        // Definisikan jenis chart: Bar Chart (vertikal)
        $series = new DataSeries(
            DataSeries::TYPE_BARCHART,
            DataSeries::GROUPING_CLUSTERED,
            range(0, count($dataSeriesValues) - 1),
            $dataSeriesLabels,
            $xAxisTickValues,
            $dataSeriesValues
        );
        $series->setPlotDirection(DataSeries::DIRECTION_COL);

        // Buat plot area
        $layout = new Layout();
        $layout->setShowVal(true);
        $plotArea = new PlotArea($layout, [$series]);

        // Judul chart
        $title = new Title('RATA-RATA BS PER BULAN PER MESIN (PCS)');
        // $legend = new Legend(Legend::POSITION_BOTTOM, null, false);

        // Buat chart
        $chart = new Chart(
            'chart1', // ID
            $title,
            null,
            $plotArea,
            true,
            0,
            null,
            null
        );

        // Posisi chart di sheet
        $chart->setTopLeftPosition('J5');
        $chart->setBottomRightPosition('R10');

        // Tambahkan ke sheet
        $sheet->addChart($chart);

        // CHAR KE 2
        // === DATA RANGE ===
        $dataStartRow = 6;              // baris pertama data
        $dataEndRow   = $row - 1;       // baris terakhir data

        // Label legend (judul kolom F)
        $dataSeriesLabels = [
            new DataSeriesValues('String', "'Summary Global'!\$F\$5", null, 1),
        ];

        // Kategori sumbu X (nama AREA)
        $xAxisTickValues = [
            new DataSeriesValues(
                'String',
                "'Summary Global'!\$A\${$dataStartRow}:\$A\${$dataEndRow}",
                null,
                ($dataEndRow - $dataStartRow + 1)
            ),
        ];

        // Nilai batang (TOTAL BS PER BULAN - PCS)
        $dataSeriesValues = [
            new DataSeriesValues(
                'Number',
                "'Summary Global'!\$F\${$dataStartRow}:\$F\${$dataEndRow}",
                null,
                ($dataEndRow - $dataStartRow + 1)
            ),
        ];

        // === BAR CHART ===
        $series = new DataSeries(
            DataSeries::TYPE_BARCHART,          // tipe bar chart
            DataSeries::GROUPING_CLUSTERED,     // grup batang sejajar
            range(0, count($dataSeriesValues) - 1),
            $dataSeriesLabels,                  // legend (judul kolom)
            $xAxisTickValues,                   // kategori (area)
            $dataSeriesValues                   // nilai batang
        );

        // Arah batang: vertikal
        $series->setPlotDirection(DataSeries::DIRECTION_COL);

        // --- Layout agar ada angka di atas batang ---
        $layout = new Layout();
        $layout->setShowVal(true);

        // --- Plot area ---
        $plotArea = new PlotArea($layout, [$series]);

        // --- Judul chart ---
        $title = new Title('TOTAL BS PER BULAN (PCS)');
        // $legend = new Legend(Legend::POSITION_BOTTOM, null, false);

        // --- Buat chart ---
        $chart2 = new Chart(
            'chart_total_bs',     // ID unik
            $title,
            null,
            $plotArea,
            true,
            0,
            null,
            null
        );

        // Posisi chart di sheet (atur sesuai sel kosong)
        $chart2->setTopLeftPosition('J11');  // posisi kiri atas
        $chart2->setBottomRightPosition('R17'); // posisi kanan bawah

        // Tambahkan ke sheet
        $sheet->addChart($chart2);


        // SUMMARY PER AREA PER TANGGAL
        $row2 = $row + 4;
        $sheet->mergeCells("A{$row2}:H{$row2}");
        $sheet->setCellValue("A{$row2}", 'SUMMARY DATA REJECT PER AREA PER TANGGAL');
        $sheet->getStyle("A{$row2}")->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle("A{$row2}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $row2++;

        $sheet->mergeCells("A{$row2}:H{$row2}");
        $sheet->setCellValue("A{$row2}", 'Periode ' . $bulanText);
        $sheet->getStyle("A{$row2}")->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle("A{$row2}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $row2++;

        $firstRow = $row2;
        // --- Header berdasarkan $areas + "Date" + "Grand Total" ---
        $sheet->setCellValue("A{$row2}", "Date");
        $colIndex = 1;
        foreach ($areas as $a) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1);
            $sheet->setCellValue("{$col}{$row2}", $a);
            $colIndex++;
        }
        $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($areas) + 2);
        $sheet->setCellValue("{$lastCol}{$row2}", "Grand Total");

        // --- Style header ---
        $sheet->getStyle("A{$row2}:{$lastCol}{$row2}")->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        // --- Auto-width kolom ---
        foreach (range('A', $lastCol) as $col) {
            $sheet->getColumnDimension($col)->setWidth(12);
        }

        // --- Body Data ---
        $row2++;
        foreach ($pivot as $tgl => $areasData) {
            $sheet->setCellValue("A{$row2}", $tgl);
            $colIndex = 1;
            foreach ($areas as $a) {
                $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1);
                $sheet->setCellValue("{$col}{$row2}", $areasData[$a] ?? '');
                $colIndex++;
            }
            $sheet->setCellValue("{$lastCol}{$row2}", $areasData['Grand Total'] ?? '');
            $row2++;
        }

        // --- Style body ---
        $sheet->getStyle("A{$firstRow}:{$lastCol}" . ($row2 - 1))->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        // --- Rata kanan untuk kolom data (B sampai lastCol) ---
        $sheet->getStyle("B4:{$lastCol}" . ($row2 - 1))
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // --- Bold semua kolom Grand Total ---
        $sheet->getStyle("{$lastCol}" . ($firstRow + 1) . ":{$lastCol}" . ($row2 - 1))
            ->getFont()
            ->setBold(true);
        $sheet->getStyle("A" . ($row2 - 1) . ":{$lastCol}" . ($row2 - 1))
            ->getFont()
            ->setBold(true);

        // === Output ke Excel ===
        $writer = new Xlsx($spreadsheet);
        $writer->setIncludeCharts(true); // wajib supaya chart muncul
        $filename = 'Summary Global Reject ' . date('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }
    public function excelGlobalProduksi($no_model)
    {
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
                    ->select('SUM(produksi.qty_produksi) AS qtyProd, produksi.area, apsperstyle.size, MAX(tgl_produksi) AS tglProd')
                    ->join('apsperstyle', 'apsperstyle.idapsperstyle=produksi.idapsperstyle')
                    ->where('apsperstyle.mastermodel', $no_model)
                    ->groupBy('produksi.area, apsperstyle.size')
                    ->findAll();

                foreach ($allProd as $row) {
                    $prodMap[$row['area']][$row['size']] = [
                        'prod' => $row['qtyProd'],
                        'tglProd' => $row['tglProd'],
                    ];
                }

                // ===========================
                // 3. BS MESIN (1 QUERY)
                // ===========================
                $allBsMc = $this->bsMesinModel
                    ->select('area, size, SUM(qty_gram) AS bs_gram, SUM(qty_pcs) AS qty_pcs, MAX(tanggal_produksi) AS tglBsMc')
                    ->where('no_model', $no_model)
                    ->groupBy('area, size')
                    ->findAll();

                foreach ($allBsMc as $row) {
                    $bsMcMap[$row['area']][$row['size']] = [
                        'qty_pcs' => $row['qty_pcs'],
                        'bs_gram' => $row['bs_gram'],
                        'tglBsMc' => $row['tglBsMc'],
                    ];
                }

                // ===========================
                // 4. PERBAIKAN AREA (1 QUERY)
                // ===========================
                $allPb = $this->perbaikanAreaModel
                    ->select('perbaikan_area.area, apsperstyle.size, SUM(perbaikan_area.qty) AS qtyPb, MAX(tgl_perbaikan) AS tglPbArea')
                    ->join('apsperstyle', 'apsperstyle.idapsperstyle = perbaikan_area.idapsperstyle')
                    ->where('apsperstyle.mastermodel', $no_model)
                    ->where('apsperstyle.qty > 0')
                    ->groupBy('perbaikan_area.area, apsperstyle.size')
                    ->findAll();

                foreach ($allPb as $row) {
                    $pbMap[$row['area']][$row['size']] = [
                        'qtyPb' => $row['qtyPb'],
                        'tglPbArea' => $row['tglPbArea'],
                    ];
                }

                // ===========================
                // 5. BS STOCKLOT (1 QUERY)
                // ===========================
                $allBsStocklot = $this->bsModel
                    ->select('data_bs.area, apsperstyle.size, SUM(data_bs.qty) AS qtyBs, MAX(data_bs.tgl_instocklot) AS tglBs')
                    ->join('apsperstyle', 'apsperstyle.idapsperstyle = data_bs.idapsperstyle')
                    ->where('apsperstyle.mastermodel', $no_model)
                    ->groupBy('data_bs.area, apsperstyle.size')
                    ->findAll();

                foreach ($allBsStocklot as $row) {
                    $bsStocklotMap[$row['area']][$row['size']] = [
                        'qtyBs' => $row['qtyBs'],
                        'tglBs' => $row['tglBs']
                    ];
                }
                // ========================================
                // 6. LOOP UTAMA (TANPA QUERY LAGI)
                // ========================================
                foreach ($allData as $key => $id) {

                    $area = $id['factory'];   // field dari geQtyByModel
                    $size = $id['size'];

                    // PRODUKSI
                    $allData[$key]['prodPcs'] = $prodMap[$area][$size]['prod'] ?? 0;
                    $allData[$key]['prodDz'] = round($prodMap[$area][$size]['prod'] / 24) ?? 0;
                    $allData[$key]['tglProd'] = $prodMap[$area][$size]['tglProd'] ?? '';

                    // BS MESIN
                    $allData[$key]['bsMcPcs']  = $bsMcMap[$area][$size]['qty_pcs'] ?? 0;
                    $allData[$key]['bsMcGram'] = $bsMcMap[$area][$size]['bs_gram'] ?? 0;
                    $bsMcPercen = $bsMcMap[$area][$size]['qty_pcs'] / ($prodMap[$area][$size]['prod'] + $bsMcMap[$area][$size]['qty_pcs']) * 100;
                    $allData[$key]['bsMcPercen'] = round($bsMcPercen) ?? 0;
                    $allData[$key]['tglBsMc']  = $bsMcMap[$area][$size]['tglBsMc'] ?? '';

                    // PERBAIKAN
                    $allData[$key]['pbAreaPcs'] = $pbMap[$area][$size]['qtyPb'] ?? 0;
                    $allData[$key]['pbAreaDz'] = round($pbMap[$area][$size]['qtyPb'] / 24) ?? 0;
                    $pbAreaPercen = $pbMap[$area][$size]['qtyPb'] / $prodMap[$area][$size]['prod'] * 100;
                    $allData[$key]['pbAreaPercen'] = round($pbAreaPercen) ?? 0;
                    $allData[$key]['tglPbArea'] = $pbMap[$area][$size]['tglPbArea'] ?? '';

                    // BS STOCKLOT
                    $allData[$key]['bsStocklotPcs'] = $bsStocklotMap[$area][$size]['qtyBs'] ?? 0;
                    $allData[$key]['bsStocklotDz'] = round($bsStocklotMap[$area][$size]['qtyBs'] / 24) ?? 0;
                    $bsStocklotPercen = $bsStocklotMap[$area][$size]['qtyBs'] / $prodMap[$area][$size]['prod'] * 100;
                    $allData[$key]['bsStocklotPercen'] = round($bsStocklotPercen) ?? 0;
                    $allData[$key]['tglBs'] = $bsStocklotMap[$area][$size]['tglBs'] ?? '';
                }
            }

            foreach ($allData as $row) {
                $area = $row['factory'];
                $grouped[$area][] = $row;
            }
        }

        $maxDates = [];

        foreach ($grouped as $area => $rows) {
            $maxDates[$area] = [
                'tglProd'   => date('d-m-Y', strtotime(max(array_column($rows, 'tglProd')))),
                'tglBsMc'   => date('d-m-Y', strtotime(max(array_column($rows, 'tglBsMc')))),
                'tglPbArea' => date('d-m-Y', strtotime(max(array_column($rows, 'tglPbArea')))),
                'tglBs'     => date('d-m-Y', strtotime(max(array_column($rows, 'tglBs')))),
            ];
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Title
        $sheet->setCellValue('A2', "Data Produksi, BS Mesin, Perbaikan & Stocklot $no_model");
        $sheet->mergeCells('A2:Q2');
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(14);

        $row = 4;

        foreach ($grouped as $area => $rows) {
            $start = $row;
            $rHeader = $row + 1;
            $rHeader2 = $row + 2;
            $sheet->setCellValue("A{$start}", "Area");
            $sheet->mergeCells("A{$start}:A{$rHeader2}");

            $sheet->setCellValue("B{$start}", "Needle");
            $sheet->mergeCells("B{$start}:B{$rHeader2}");

            $sheet->setCellValue("C{$start}", "No Model");
            $sheet->mergeCells("C{$start}:C{$rHeader2}");

            $sheet->setCellValue("D{$start}", "Inisial");
            $sheet->mergeCells("D{$start}:D{$rHeader2}");

            $sheet->setCellValue("E{$start}", "Style Size");
            $sheet->mergeCells("E{$start}:E{$rHeader2}");

            $sheet->setCellValue("F{$start}", "TOTAL GLOBAL");
            $sheet->mergeCells("F{$start}:Q{$start}");

            $sheet->setCellValue("F{$rHeader}", "Qty Po (Dz)");
            $sheet->mergeCells("F{$rHeader}:F{$rHeader2}");

            $sheet->setCellValue("G{$rHeader}", "Produksi");
            $sheet->mergeCells("G{$rHeader}:H{$rHeader}");
            $sheet->setCellValue("G{$rHeader2}", "Dz");
            $sheet->setCellValue("H{$rHeader2}", "Pcs");

            $sheet->setCellValue("I{$rHeader}", "BS MC");
            $sheet->mergeCells("I{$rHeader}:K{$rHeader}");
            $sheet->setCellValue("I{$rHeader2}", "Gram");
            $sheet->setCellValue("J{$rHeader2}", "Pcs");
            $sheet->setCellValue("K{$rHeader2}", "%");

            $sheet->setCellValue("L{$rHeader}", "IN PB");
            $sheet->mergeCells("L{$rHeader}:N{$rHeader}");
            $sheet->setCellValue("L{$rHeader2}", "Dz");
            $sheet->setCellValue("M{$rHeader2}", "Pcs");
            $sheet->setCellValue("N{$rHeader2}", "%");

            $sheet->setCellValue("O{$rHeader}", "IN STOCKLOT");
            $sheet->mergeCells("O{$rHeader}:Q{$rHeader}");
            $sheet->setCellValue("O{$rHeader2}", "Dz");
            $sheet->setCellValue("P{$rHeader2}", "Pcs");
            $sheet->setCellValue("Q{$rHeader2}", "%");

            $sheet->getStyle("A{$start}:Q{$rHeader2}")->getFont()->setBold(true);
            $sheet->getStyle("A{$start}:Q{$rHeader2}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'wrapText'   => true,
                ],
                'font' => [
                    'bold' => true
                ]
            ]);
            $row = $rHeader2;
            $row++;

            // ==== Data Rows ====
            foreach ($rows as $id) {
                $sheet->fromArray([
                    $area,
                    $id['machinetypeid'],
                    $id['mastermodel'],
                    $id['inisial'],
                    $id['size'],
                    round($id['qty'], 2),
                    $id['prodDz'],
                    $id['prodPcs'],
                    $id['bsMcGram'],
                    $id['bsMcPcs'],
                    $id['bsMcPercen'] . '%',
                    $id['pbAreaDz'],
                    $id['pbAreaPcs'],
                    $id['pbAreaPercen'] . '%',
                    $id['bsStocklotDz'],
                    $id['bsStocklotPcs'],
                    $id['bsStocklotPercen'] . '%',
                ], null, "A{$row}");
                $sheet->getStyle("A{$row}:Q{$row}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['rgb' => '000000']
                        ]
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ]

                ]);
                $row++;
            }

            $row = $row + 2; // spacing antar area
        }

        // DATA NOTE
        $sheet->setCellValue("A{$row}", "Note :");

        foreach ($maxDates as $area => $tgl) {
            $sheet->setCellValue("B{$row}", "Produksi {$area} sampai tanggal {$tgl['tglProd']}");
            $sheet->mergeCells("B{$row}:E{$row}");
            $row++;

            $sheet->setCellValue("B{$row}", "BS Mc {$area} sampai tanggal {$tgl['tglBsMc']}");
            $sheet->mergeCells("B{$row}:E{$row}");
            $row++;

            $sheet->setCellValue("B{$row}", "In Perbaikan {$area} sampai tanggal {$tgl['tglPbArea']}");
            $sheet->mergeCells("B{$row}:E{$row}");
            $row++;

            $sheet->setCellValue("B{$row}", "In Stocklot {$area} sampai tanggal {$tgl['tglBs']}");
            $sheet->mergeCells("B{$row}:E{$row}");
            $row++;
        }



        // ==== Auto-size columns ====
        foreach (range('A', 'Q') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // ==== Output ====
        $filename = "Produlsi Global {$no_model}.xlsx";

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
