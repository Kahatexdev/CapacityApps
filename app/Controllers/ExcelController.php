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
use App\Models\KebutuhanMesinModel;
use App\Models\KebutuhanAreaModel;
use App\Models\MesinPlanningModel;
use App\Models\DetailPlanningModel;
use App\Models\TanggalPlanningModel;
use App\Models\EstimatedPlanningModel;
use App\Models\AksesModel;/*  */
use App\Services\orderServices;
use CodeIgniter\HTTP\RequestInterface;
use LengthException;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Week;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\{Border, Alignment, Fill};
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use App\Models\EstSpkModel;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;


class ExcelController extends BaseController
{

    protected $filters;
    protected $jarumModel;
    protected $productModel;
    protected $produksiModel;
    protected $bookingModel;
    protected $orderModel;
    protected $ApsPerstyleModel;
    protected $liburModel;
    protected $KebutuhanMesinModel;
    protected $KebutuhanAreaModel;
    protected $MesinPlanningModel;
    protected $aksesModel;
    protected $DetailPlanningModel;
    protected $TanggalPlanningModel;
    protected $EstimatedPlanningModel;
    protected $orderServices;
    protected $estspk;


    public function __construct()
    {
        $this->jarumModel = new DataMesinModel();
        $this->bookingModel = new BookingModel();
        $this->productModel = new ProductTypeModel();
        $this->produksiModel = new ProduksiModel();
        $this->orderModel = new OrderModel();
        $this->ApsPerstyleModel = new ApsPerstyleModel();
        $this->liburModel = new LiburModel();
        $this->KebutuhanMesinModel = new KebutuhanMesinModel();
        $this->KebutuhanAreaModel = new KebutuhanAreaModel();
        $this->MesinPlanningModel = new MesinPlanningModel();
        $this->aksesModel = new AksesModel();
        $this->DetailPlanningModel = new DetailPlanningModel();
        $this->TanggalPlanningModel = new TanggalPlanningModel();
        $this->EstimatedPlanningModel = new EstimatedPlanningModel();
        $this->orderServices = new orderServices();
        $this->estspk = new EstSpkModel();

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
                        'buyer' => $buyer,
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
        $col2 = 'I'; // Kolom akhir week

        // Konversi huruf kolom ke nomor indeks kolom
        $col_index = Coordinate::columnIndexFromString($col);
        $col2_index = Coordinate::columnIndexFromString($col2);

        for ($i = 1; $i <= $maxWeek; $i++) {
            $sheet->setCellValue($col . $row_header, 'WEEK ' . $i . ' (' . $week[$i] . ')');
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

        $url = 'http://172.23.44.14/MaterialSystem/public/api/listRetur/' . $area;

        $response = file_get_contents($url);
        log_message('debug', "API Response: " . $response);
        if ($response === FALSE) {
            // log_message('error', "API tidak bisa diakses: $url");
            return $this->response->setJSON(["error" => "Gagal mengambil data dari API"]);
        }
        $data = json_decode($response, true);
        if ($data === null) {
            log_message('error', "Gagal mendecode data dari API: $url");
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
            $sheet->mergeCells('A1:H1');
            $sheet->getStyle('A1:H1')->applyFromArray($styleTitle);
            // Tulis header
            $sheet->setCellValue('A3', 'NO');
            $sheet->setCellValue('B3', 'TANGGAL RETUR');
            $sheet->setCellValue('C3', 'NO MODEL');
            $sheet->setCellValue('D3', 'ITEM TYPE');
            $sheet->setCellValue('E3', 'KODE WARNA');
            $sheet->setCellValue('F3', 'WARNA');
            $sheet->setCellValue('G3', 'LOT RETUR');
            $sheet->setCellValue('H3', 'KG RETUR');
            $sheet->setCellValue('I3', 'KATEGORI');
            $sheet->setCellValue('J3', 'KETERANGAN GBN');
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

            // Tulis data mulai dari baris 2
            $row = 4;
            $no = 1;

            foreach ($data as $item) {
                $sheet->setCellValue('A' . $row, $no++);
                $sheet->setCellValue('B' . $row, $item['tgl_retur']);
                $sheet->setCellValue('C' . $row, $item['no_model']);
                $sheet->setCellValue('D' . $row, $item['item_type']);
                $sheet->setCellValue('E' . $row, $item['kode_warna']);
                $sheet->setCellValue('F' . $row, $item['warna']);
                $sheet->setCellValue('G' . $row, $item['lot_retur']);
                $sheet->setCellValue('H' . $row, $item['kgs_retur']);
                $sheet->setCellValue('I' . $row, $item['kategori']);
                $sheet->setCellValue('J' . $row, $item['keterangan_gbn']);
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
                $row++;
            }

            // Set lebar kolom agar menyesuaikan isi
            foreach (range('A', 'J') as $col) {
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
            'tglTurun' => $tglTurun,
            'tglTurunAkhir' => $tglTurunAkhir,
            'awal' => $awal,
            'akhir' => $akhir,
        ];
        $data = $this->ApsPerstyleModel->getDataOrder($validate);
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
        $sheet->setCellValue('J3', 'PRODUCTION UNIT');
        $sheet->setCellValue('K3', 'AREA');
        $sheet->setCellValue('L3', 'JARUM');
        $sheet->setCellValue('M3', 'INISIAL');
        $sheet->setCellValue('N3', 'STYLE SIZE');
        $sheet->setCellValue('O3', 'DELIVERY');
        $sheet->setCellValue('P3', 'QTY');
        $sheet->setCellValue('Q3', 'PRODUKSI');
        $sheet->setCellValue('R3', 'SISA');
        $sheet->setCellValue('S3', 'JLN MC');
        $sheet->setCellValue('T3', 'DESCRIPTION');
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
            $sheet->setCellValue('J' . $row, $item['production_unit']);
            $sheet->setCellValue('K' . $row, $item['factory']);
            $sheet->setCellValue('L' . $row, $item['machinetypeid']);
            $sheet->setCellValue('M' . $row, $item['inisial']);
            $sheet->setCellValue('N' . $row, $item['size']);
            $sheet->setCellValue('O' . $row, $item['delivery']);
            $sheet->setCellValue('P' . $row, $item['qty_pcs']);
            $sheet->setCellValue('Q' . $row, $item['qty_produksi']);
            $sheet->setCellValue('R' . $row, $item['sisa_pcs']);
            $sheet->setCellValue('S' . $row, $item['jl_mc']);
            $sheet->setCellValue('T' . $row, $item['description']);
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
            $row++;
        }

        // Set lebar kolom agar menyesuaikan isi
        foreach (range('A', 'S') as $col) {
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
        $apiUrl = "http://172.23.44.14/MaterialSystem/public/api/filterPoTambahan"
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

        $data = json_decode($response, true);
        if (!is_array($data)) {
            return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Data tidak valid dari API']);
        }

        // Buat spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Style
        $styleHeader = [
            'font' => [
                'size' => 10,
                'bold' => true,
                'name' => 'Arial',
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER, // Alignment rata tengah
                'vertical' => Alignment::VERTICAL_CENTER, // Alignment rata tengah
            ],
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THIN, // Gaya garis tipis
                    'color' => ['argb' => 'FF000000'],    // Warna garis hitam
                ],
            ],
        ];
        $styleHeader2 = [
            'font' => [
                'size' => 10,
                'bold' => true,
                'name' => 'Arial',
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER, // Alignment rata tengah
                'vertical' => Alignment::VERTICAL_CENTER, // Alignment rata tengah
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
                'size' => 10,
                'name' => 'Arial',
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER, // Alignment rata tengah
                'vertical' => Alignment::VERTICAL_CENTER, // Alignment rata tengah
            ],
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THIN, // Gaya garis tipis
                    'color' => ['argb' => 'FF000000'],    // Warna garis hitam
                ],
            ],
        ];
        // mengatur tinggi semua baris
        $sheet->getDefaultRowDimension()->setRowHeight(19);
        // Mendefinisikan kolom B sampai AD
        $columns = range('B', 'AF');

        // Mengatur autosize untuk setiap kolom dalam range B sampai AD
        // foreach ($columns as $columnID) {
        //     $sheet->getColumnDimension($columnID)->setAutoSize(true);
        // }

        // Header ISO
        $drawing =  new Drawing();
        $drawing->setName('Sample Image');
        $drawing->setDescription('Sample Image');
        $pathToImage = FCPATH . 'assets/img/logo-kahatex.png';

        $drawing->setPath($pathToImage);
        $drawing->setCoordinates('A1');
        // Set ukuran gambar
        $sizeInCm = 1.25;
        $sizeInPixels = $sizeInCm * 37.7952755906;
        $drawing->setHeight($sizeInPixels);
        $drawing->setWidth($sizeInPixels);
        $drawing->setOffsetY(50);
        $drawing->setOffsetX(45);

        $drawing->setWorksheet($sheet);

        $sheet->mergeCells('A1:D3')->getStyle('A1:D3')->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_TOP
            ],
            'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
                'bottom' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
                'left' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
                'right' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // mengatur lebar kolom
        $sheet->getColumnDimension('A')->setWidth(19);
        $sheet->getColumnDimension('B')->setWidth(11);
        $sheet->getColumnDimension('C')->setWidth(14);
        $sheet->getColumnDimension('D')->setWidth(7);
        $sheet->getColumnDimension('E')->setWidth(9);
        $sheet->getColumnDimension('F')->setWidth(6);
        $sheet->getColumnDimension('G')->setWidth(12);
        $sheet->getColumnDimension('H')->setWidth(35);
        $sheet->getColumnDimension('I')->setWidth(8);
        $sheet->getColumnDimension('J')->setWidth(12);
        $sheet->getColumnDimension('K')->setWidth(9);
        $sheet->getColumnDimension('L')->setWidth(8);
        $sheet->getColumnDimension('M')->setWidth(8);
        $sheet->getColumnDimension('N')->setWidth(8);
        $sheet->getColumnDimension('O')->setWidth(8);
        $sheet->getColumnDimension('P')->setWidth(8);
        $sheet->getColumnDimension('Q')->setWidth(8);
        $sheet->getColumnDimension('R')->setWidth(8);
        $sheet->getColumnDimension('S')->setWidth(8);
        $sheet->getColumnDimension('T')->setWidth(8);
        $sheet->getColumnDimension('U')->setWidth(8);
        $sheet->getColumnDimension('V')->setWidth(8);
        $sheet->getColumnDimension('W')->setWidth(8);
        $sheet->getColumnDimension('X')->setWidth(8);
        $sheet->getColumnDimension('Y')->setWidth(8);
        $sheet->getColumnDimension('Z')->setWidth(8);
        $sheet->getColumnDimension('AA')->setWidth(8);
        $sheet->getColumnDimension('AB')->setWidth(8);
        $sheet->getColumnDimension('AC')->setWidth(8);
        $sheet->getColumnDimension('AD')->setWidth(8);
        $sheet->getColumnDimension('AE')->setWidth(8);
        $sheet->getColumnDimension('AF')->setWidth(35);


        // mengatur tinggi baris 1
        $heightInCm = 1.25;
        $heightInPoints = $heightInCm / 0.0352778;
        $sheet->getRowDimension('1')->setRowHeight($heightInPoints);

        $sheet->setCellValue('A1', 'PT. KAHATEX');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 11,
                'name' => 'Arial',
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_BOTTOM,
            ],
        ]);

        $sheet->setCellValue('E1', 'FORMULIR');
        $sheet->mergeCells('E1:AD1')->getStyle('E1:AD1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16,
                'name' => 'Arial',
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
                'bottom' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
                'left' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
                'right' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => '99FFFF', // Warna dengan red 153, green 255, dan blue 255 dalam format RGB
                ],
            ],
        ]);

        // mengatur tinggi baris 2 - 7
        $heightInCm2 = 1.25;
        $heightInPoints2 = $heightInCm2 / 0.0352778;
        $sheet->getRowDimension('2')->setRowHeight($heightInPoints2);
        $sheet->getRowDimension('3')->setRowHeight($heightInPoints2);
        $sheet->getRowDimension('4')->setRowHeight($heightInPoints2);
        $sheet->getRowDimension('5')->setRowHeight($heightInPoints2);
        $sheet->getRowDimension('6')->setRowHeight($heightInPoints2);
        $sheet->getRowDimension('7')->setRowHeight($heightInPoints2);

        $sheet->setCellValue('E2', 'DEPARTEMEN KAOSKAKI');
        $sheet->mergeCells('E2:AD2')->getStyle('E2:AD2')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'name' => 'Arial',
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
                'bottom' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
                'left' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
                'right' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->setCellValue('E3', 'PO TAMBAHAN DAN RETURAN BAHAN BAKU MESIN KE GUDANG BENANG');
        $sheet->mergeCells('E3:AD3')->getStyle('E3:AD3')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'name' => 'Arial',
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
                'bottom' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
                'left' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
                'right' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->setCellValue('A4', 'No. Dokumen');
        $sheet->mergeCells('A4:D4')->getStyle('A4:D4')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 11,
                'name' => 'Arial',
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
                'bottom' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
                'left' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
                'right' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->setCellValue('E4', 'FOR-KK-034/REV_05/HAL_1/1');
        $sheet->mergeCells('E4:R4')->getStyle('E4:R4')->applyFromArray([
            'font' => [
                'size' => 11,
                'bold' => true,
                'name' => 'Arial',
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
                'bottom' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
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

        $sheet->setCellValue('S4', 'Tanggal Revisi ');
        $sheet->mergeCells('S4:Z4')->getStyle('S4:Z4')->applyFromArray([
            'font' => [
                'size' => 11,
                'bold' => true,
                'name' => 'Arial',
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
                'bottom' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
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

        $sheet->setCellValue('AA4', '30 Januari 2025');
        $sheet->mergeCells('AA4:AD4')->getStyle('AA4:AD4')->applyFromArray([
            'font' => [
                'size' => 11,
                'bold' => true,
                'name' => 'Arial',
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
                'bottom' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
                'left' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
                'right' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->getStyle('A5:R5')->applyFromArray([
            'font' => [
                'size' => 11,
                'bold' => true,
                'name' => 'Arial',
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
                'bottom' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
                'left' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
                'right' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->setCellValue('S5', 'Klasifikasi');
        $sheet->mergeCells('S5:Z5')->getStyle('S5:Z5')->applyFromArray([
            'font' => [
                'size' => 11,
                'bold' => true,
                'name' => 'Arial',
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
                'bottom' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
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

        $sheet->setCellValue('AA5', 'Sensitif');
        $sheet->mergeCells('AA5:AD5')->getStyle('AA5:AD5')->applyFromArray([
            'font' => [
                'size' => 11,
                'bold' => true,
                'name' => 'Arial',
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
                'bottom' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
                'left' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
                'right' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->setCellValue('A6', ' Area :');
        $sheet->mergeCells('A6:B6')->getStyle('A6:B6')->applyFromArray([
            'font' => [
                'size' => 10,
                'bold' => true,
                'name' => 'Arial',
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_NONE,
                    'color' => ['rgb' => '000000'],
                ],
                'bottom' => [
                    'borderStyle' => Border::BORDER_NONE,
                    'color' => ['rgb' => '000000'],
                ],
                'left' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
                'right' => [
                    'borderStyle' => Border::BORDER_NONE,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->setCellValue('C6', $area);
        $sheet->getStyle('C6')->applyFromArray([
            'font' => [
                'size' => 10,
                'bold' => false,
                'name' => 'Arial',
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_NONE,
                    'color' => ['rgb' => '000000'],
                ],
                'bottom' => [
                    'borderStyle' => Border::BORDER_NONE,
                    'color' => ['rgb' => '000000'],
                ],
                'left' => [
                    'borderStyle' => Border::BORDER_NONE,
                    'color' => ['rgb' => '000000'],
                ],
                'right' => [
                    'borderStyle' => Border::BORDER_NONE,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->setCellValue('G6', 'Loss F.Up');
        $sheet->getStyle('G6')->applyFromArray([
            'font' => [
                'size' => 10,
                'bold' => true,
                'name' => 'Arial',
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_NONE,
                    'color' => ['rgb' => '000000'],
                ],
                'bottom' => [
                    'borderStyle' => Border::BORDER_NONE,
                    'color' => ['rgb' => '000000'],
                ],
                'left' => [
                    'borderStyle' => Border::BORDER_NONE,
                    'color' => ['rgb' => '000000'],
                ],
                'right' => [
                    'borderStyle' => Border::BORDER_NONE,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->setCellValue('I6', ': ');
        $sheet->getStyle('I6')->applyFromArray([
            'font' => [
                'size' => 10,
                'bold' => false,
                'name' => 'Arial',
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_NONE,
                    'color' => ['rgb' => '000000'],
                ],
                'bottom' => [
                    'borderStyle' => Border::BORDER_NONE,
                    'color' => ['rgb' => '000000'],
                ],
                'left' => [
                    'borderStyle' => Border::BORDER_NONE,
                    'color' => ['rgb' => '000000'],
                ],
                'right' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->setCellValue('P6', 'Tanggal Buat');
        $sheet->mergeCells('P6:R6')->getStyle('P6:R6')->applyFromArray([
            'font' => [
                'size' => 10,
                'bold' => true,
                'name' => 'Arial',
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_NONE,
                    'color' => ['rgb' => '000000'],
                ],
                'bottom' => [
                    'borderStyle' => Border::BORDER_NONE,
                    'color' => ['rgb' => '000000'],
                ],
                'left' => [
                    'borderStyle' => Border::BORDER_NONE,
                    'color' => ['rgb' => '000000'],
                ],
                'right' => [
                    'borderStyle' => Border::BORDER_NONE,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->setCellValue('S6', ':' . $tglBuat);
        $sheet->mergeCells('S6:X6')->getStyle('S6:X6')->applyFromArray([
            'font' => [
                'size' => 10,
                'bold' => false,
                'name' => 'Arial',
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_NONE,
                    'color' => ['rgb' => '000000'],
                ],
                'bottom' => [
                    'borderStyle' => Border::BORDER_NONE,
                    'color' => ['rgb' => '000000'],
                ],
                'left' => [
                    'borderStyle' => Border::BORDER_NONE,
                    'color' => ['rgb' => '000000'],
                ],
                'right' => [
                    'borderStyle' => Border::BORDER_NONE,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->setCellValue('P7', 'Tanggal Export');
        $sheet->mergeCells('P7:R7')->getStyle('P7:R7')->applyFromArray([
            'font' => [
                'size' => 10,
                'bold' => true,
                'name' => 'Arial',
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_NONE,
                    'color' => ['rgb' => '000000'],
                ],
                'bottom' => [
                    'borderStyle' => Border::BORDER_NONE,
                    'color' => ['rgb' => '000000'],
                ],
                'left' => [
                    'borderStyle' => Border::BORDER_NONE,
                    'color' => ['rgb' => '000000'],
                ],
                'right' => [
                    'borderStyle' => Border::BORDER_NONE,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->setCellValue('S7', ':');
        $sheet->mergeCells('S7:X7')->getStyle('S7:X7')->applyFromArray([
            'font' => [
                'size' => 10,
                'bold' => false,
                'name' => 'Arial',
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_NONE,
                    'color' => ['rgb' => '000000'],
                ],
                'bottom' => [
                    'borderStyle' => Border::BORDER_NONE,
                    'color' => ['rgb' => '000000'],
                ],
                'left' => [
                    'borderStyle' => Border::BORDER_NONE,
                    'color' => ['rgb' => '000000'],
                ],
                'right' => [
                    'borderStyle' => Border::BORDER_NONE,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
        // header ISO end

        // Header Table
        $sheet->setCellValue('A8', 'Model');
        $sheet->mergeCells('A8:B10')->getStyle('A8:B10')->applyFromArray($styleHeader);

        $sheet->setCellValue('C8', 'Warna');
        $sheet->mergeCells('C8:C10')->getStyle('C8:C10')->applyFromArray($styleHeader);

        $sheet->setCellValue('D8', 'Item Type');
        $sheet->mergeCells('D8:D10')->getStyle('D8:D10')->applyFromArray($styleHeader);

        $sheet->setCellValue('E8', 'Kode Warna');
        $sheet->mergeCells('E8:E10')->getStyle('E8:E10')->applyFromArray($styleHeader);

        $sheet->setCellValue('F8', 'Style / Size');
        $sheet->mergeCells('F8:F10')->getStyle('F8:F10')->applyFromArray($styleHeader);

        $sheet->setCellValue('G8', 'Komposisi (%)');
        $sheet->mergeCells('G8:G10')->getStyle('G8:G10')->applyFromArray($styleHeader);

        $sheet->setCellValue('H8', 'Gw / Pcs');
        $sheet->mergeCells('H8:H10')->getStyle('H8:H10')->applyFromArray($styleHeader);

        $sheet->setCellValue('I8', 'Qty / Pcs');
        $sheet->mergeCells('I8:I10')->getStyle('I8:I10')->applyFromArray($styleHeader);

        $sheet->setCellValue('J8', 'Loss');
        $sheet->mergeCells('J8:J10')->getStyle('J8:J10')->applyFromArray($styleHeader);

        $sheet->setCellValue('K8', 'Pesanan Kgs');
        $sheet->mergeCells('K8:K10')->getStyle('K8:K10')->applyFromArray($styleHeader);

        $sheet->setCellValue('L8', 'Terima');
        $sheet->mergeCells('L8:N9')->getStyle('L8:N9')->applyFromArray($styleHeader);

        $sheet->setCellValue('L10', 'Kg');
        $sheet->getStyle('L10')->applyFromArray($styleHeader);

        $sheet->setCellValue('M10', '+ / -');
        $sheet->getStyle('M10')->applyFromArray($styleHeader);

        $sheet->setCellValue('N10', '%');
        $sheet->getStyle('N10')->applyFromArray($styleHeader);

        $sheet->setCellValue('O8', 'Sisa Benang di mesin');
        $sheet->mergeCells('O8:O9')->getStyle('O8:O9')->applyFromArray($styleHeader);

        $sheet->setCellValue('O10', 'Kg');
        $sheet->getStyle('O10')->applyFromArray($styleHeader);

        $sheet->setCellValue('P8', 'Tambahan I (mesin)');
        $sheet->mergeCells('P8:S8')->getStyle('P8:S8')->applyFromArray($styleHeader);

        $sheet->setCellValue('P9', 'Pcs');
        $sheet->mergeCells('P9:P10')->getStyle('P9:P10')->applyFromArray($styleHeader);

        $sheet->setCellValue('Q9', 'Benang');
        $sheet->mergeCells('Q9:R9')->getStyle('Q9:R9')->applyFromArray($styleHeader);

        $sheet->setCellValue('Q10', 'Kg');
        $sheet->getStyle('Q10')->applyFromArray($styleHeader);

        $sheet->setCellValue('R10', 'Cones');
        $sheet->getStyle('R10')->applyFromArray($styleHeader);

        $sheet->setCellValue('S9', '%');
        $sheet->mergeCells('S9:S10')->getStyle('S9:S10')->applyFromArray($styleHeader);

        $sheet->setCellValue('T8', 'Tambahan II (Packing)');
        $sheet->mergeCells('T8:W8')->getStyle('T8:W8')->applyFromArray($styleHeader);

        $sheet->setCellValue('T9', 'Pcs');
        $sheet->mergeCells('T9:T10')->getStyle('T9:T10')->applyFromArray($styleHeader);

        $sheet->setCellValue('U9', 'Benang');
        $sheet->mergeCells('U9:P9')->getStyle('U9:P9')->applyFromArray($styleHeader);

        $sheet->setCellValue('U10', 'Kg');
        $sheet->getStyle('U10')->applyFromArray($styleHeader);

        $sheet->setCellValue('V10', 'Cones');
        $sheet->getStyle('V10')->applyFromArray($styleHeader);

        $sheet->setCellValue('W9', '%');
        $sheet->mergeCells('W9:W10')->getStyle('W9:W10')->applyFromArray($styleHeader);

        $sheet->setCellValue('X8', 'Total lebih pakai benang');
        $sheet->mergeCells('X8:Y9')->getStyle('X8:Y9')->applyFromArray($styleHeader);

        $sheet->setCellValue('X10', 'Kg');
        $sheet->getStyle('X10')->applyFromArray($styleHeader);

        $sheet->setCellValue('Y10', '%');
        $sheet->getStyle('Y10')->applyFromArray($styleHeader);

        $sheet->setCellValue('Z8', 'RETURAN');
        $sheet->mergeCells('Z8:AC8')->getStyle('Z8:AC8')->applyFromArray($styleHeader);

        $sheet->setCellValue('Z9', 'Kg');
        $sheet->mergeCells('Z9:Z10')->getStyle('Z9:Z10')->applyFromArray($styleHeader);

        $sheet->setCellValue('AA9', '%');
        $sheet->getStyle('AA9')->applyFromArray($styleHeader);

        $sheet->setCellValue('AA10', 'dari PSN');
        $sheet->getStyle('AA10')->applyFromArray($styleHeader);

        $sheet->setCellValue('AB9', 'Kg');
        $sheet->mergeCells('AB9:AB10')->getStyle('AB9:AB10')->applyFromArray($styleHeader);

        $sheet->setCellValue('AC9', '%');
        $sheet->getStyle('AC9')->applyFromArray($styleHeader);

        $sheet->setCellValue('AC10', 'dari PSN');
        $sheet->getStyle('AC10')->applyFromArray($styleHeader);

        $sheet->setCellValue('AD8', 'Keterangan');
        $sheet->mergeCells('AD8:AD10')->getStyle('AD8:AD10')->applyFromArray($styleHeader);

        // Terapkan border double bawah pada setiap kolom di baris terakhir
        // foreach ($columns as $column) {
        //     $sheet->getStyle($column . $lastRow)->getBorders()->getBottom()->setBorderStyle(Border::BORDER_DOUBLE);
        // }


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
                    $jlMcData = $this->produksiModel->getJlMc($dataOrder);

                    // Pastikan data jl_mc ada
                    if ($jlMcData) {
                        // Loop untuk menjumlahkan jl_mc
                        foreach ($jlMcData as $mc) {
                            $jlMc += $mc['jl_mc'];
                        }
                    }

                    $allData[$factory][$mastermodel][$machinetypeid][$weekCount][] = json_encode([
                        'del' => $id['delivery'],
                        'qty' => $qty,
                        'prod' => $produksi,
                        'sisa' => $sisa,
                        'jlMc' => $jlMc,
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
        $col2 = 'K'; // Kolom akhir week

        // Konversi huruf kolom ke nomor indeks kolom
        $col_index = Coordinate::columnIndexFromString($col);
        $col2_index = Coordinate::columnIndexFromString($col2);

        for ($i = 1; $i <= $maxWeek; $i++) {
            $sheet->setCellValue($col . $row_header, 'WEEK ' . $i . ' (' . $week[$i] . ')');
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
            $sheet->setCellValue($col4 . $row_header2, 'JLN MC');
            $sheet->getStyle($col4 . $row_header2)->applyFromArray($styleHeader);
            $col4++;
        }

        // dd($allData);

        // Mulai di baris 5
        $row = 5;
        foreach ($allData as $area => $models) {
            // 1. Hitung total baris di area
            $rowsArea = 0;
            foreach ($models as $model => $jarums) {
                foreach ($jarums as $jarum => $weeks) {
                    foreach ($weeks as $weekEntries) {
                        $rowsArea += count($weekEntries);
                    }
                }
            }
            if ($rowsArea === 0) {
                continue;
            }
            $startRowArea = $row;
            $endRowArea = $startRowArea + $rowsArea - 1;

            // Merge & tulis kolom B (area)
            // $sheet->setCellValue('A' . $startRowArea, $buyer);
            // $sheet->getStyle('A' . $startRowArea);
            $sheet->setCellValue('B' . $startRowArea, $area);
            // $sheet->mergeCells('A' . $startRowArea . ':A' . $endRowArea);
            // $sheet->getStyle('A' . $startRowArea . ':A' . $endRowArea)->applyFromArray($styleBody);
            $sheet->mergeCells('B' . $startRowArea . ':B' . $endRowArea);
            $sheet->getStyle('B' . $startRowArea . ':B' . $endRowArea)->applyFromArray($styleBody);

            // 2. Loop per model
            foreach ($models as $model => $jarums) {
                // Hitung rowsModel
                $rowsModel = 0;
                foreach ($jarums as $jarum => $weeks) {
                    foreach ($weeks as $weekEntries) {
                        $rowsModel += count($weekEntries);
                    }
                }
                if ($rowsModel === 0) {
                    continue;
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
                    $rowsJarum = 0;
                    foreach ($weeks as $weekEntries) {
                        $rowsJarum += count($weekEntries);
                    }
                    if ($rowsJarum === 0) {
                        continue;
                    }
                    $startRowJarum = $row;
                    $endRowJarum = $startRowJarum + $rowsJarum - 1;

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
                            $baseColIndex = Coordinate::columnIndexFromString('G') + ($weekNum - 1) * 5;
                            $cols = [];
                            for ($k = 0; $k < 5; $k++) {
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
        // dd($area, $pdk, $data);
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

            $apiUrl = 'http://172.23.44.14/MaterialSystem/public/api/pph?model=' . urlencode($noModel);
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

    public function generateFormRetur($area)
    {
        $noModel = $this->request->getGet('noModel');
        $tglBuat = $this->request->getGet('tglBuat');

        // Ambil data berdasarkan area dan model
        $apiUrl = "http://172.23.44.14/MaterialSystem/public/api/listExportRetur/"
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
        if (!is_array($result)) {
            return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Data tidak valid dari API']);
        }

        $delivery = $result[0]['delivery_akhir'] ?? '';
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
        $sheet->getStyle('Q1:AD33')->applyFromArray([
            'borders' => [
                'right' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // 3. Bottom double border dari A50 ke Q50
        $sheet->getStyle('A33:AD33')->applyFromArray([
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // 4. Left double border dari A1 ke A50
        $sheet->getStyle('A1:A33')->applyFromArray([
            'borders' => [
                'left' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

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

        // Aktifkan wrap text di A11:Q28
        $sheet->getStyle('A11:AD28')->getAlignment()->setWrapText(true);

        // Lebar kolom (dalam pt) dan tinggi baris (dalam pt)
        $columnWidths = [
            'A' => 20,
            'B' => 20,
            'C' => 40,
            'D' => 50,
            'E' => 50,
            'F' => 50,
            'G' => 50,
            'H' => 20,
            'I' => 20,
            'J' => 20,
            'K' => 40,
            'L' => 20,
            'M' => 20,
            'N' => 20,
            'O' => 40,
            'P' => 20,
            'Q' => 20,
            'R' => 30,
            'S' => 20,
            'T' => 20,
            'U' => 20,
            'V' => 30,
            'W' => 20,
            'X' => 20,
            'Y' => 20,
            'Z' => 20,
            'AA' => 30,
            'AB' => 20,
            'AC' => 30,
            'AD' => 40,
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
        $drawing->setCoordinates('B1');
        $drawing->setHeight(50);
        $drawing->setOffsetX(60);
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

        $lossValue = isset($result[0]['loss']) ? $result[0]['loss'] . '%' : '';
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
        $no = 1;
        $firstRow = true;
        $sheet->getRowDimension($rowNum)->setRowHeight(18);

        foreach ($result as $row) {
            $sheet->mergeCells("A{$rowNum}:B{$rowNum}");
            $retur_kg_psn = '';
            $retur_kg_po = '';
            $retur_persen_psn = '';
            $retur_persen_po = '';

            $kgs = (float)$row['kgs'];
            $retur = (float)$row['kgs_retur'];
            $poplus_mc_kg = (float)$row['poplus_mc_kg'];
            $plus_pck_kg = (float)$row['plus_pck_kg'];

            // Cek logika penempatan
            if ($poplus_mc_kg == 0 && $plus_pck_kg == 0) {
                $retur_kg_psn = number_format($retur, 2);
                if ($kgs != 0) {
                    $retur_persen_psn = number_format(($retur / $kgs) * 100, 2) . '%';
                }
            } else {
                $retur_kg_po = number_format($retur, 2);
                $totalPO = $poplus_mc_kg + $plus_pck_kg;
                if ($totalPO != 0) {
                    $retur_persen_po = number_format(($retur / $totalPO) * 100, 2) . '%';
                }
            }

            $sheet->fromArray([
                $row['no_model'] ?? '',
                '',
                $row['color'],
                $row['item_type'],
                $row['kode_warna'],
                '',
                $row['composition'],
                $row['gw'],
                $row['qty_pcs'],
                $row['loss'],
                $row['kgs'],
                number_format($row['terima_kg'], 2),
                number_format($row['terima_kg'] - $row['kgs'], 2),
                number_format($row['terima_kg'] / $row['kgs'], 2) * 100 . '%', // terima
                number_format($row['sisa_bb_mc'], 2), // sisa mesin
                $row['sisa_order_pcs'] == 0 ? '' : $row['sisa_order_pcs'],
                $row['poplus_mc_kg'] == 0 ? '' : number_format($row['poplus_mc_kg'], 2),
                $row['poplus_mc_cns'] == 0 ? '' : $row['poplus_mc_cns'],
                ($row['poplus_mc_kg'] / $row['kgs']) == 0 ? '' : number_format($row['poplus_mc_kg'] / $row['kgs'], 2) * 100 . '%',
                $row['plus_pck_pcs'] == 0 ? '' : number_format($row['plus_pck_pcs'], 2),
                $row['plus_pck_kg'] == 0 ? '' : number_format($row['plus_pck_kg'], 2),
                $row['plus_pck_cns'] == 0 ? '' : $row['plus_pck_cns'],
                ($row['plus_pck_kg'] / $row['kgs']) == 0 ? '' : number_format($row['plus_pck_kg'] / $row['kgs'], 2) * 100 . '%',
                $row['lebih_pakai_kg'] == 0 ? '' : number_format($row['lebih_pakai_kg'], 2),
                ($row['lebih_pakai_kg'] / $row['kgs']) == 0 ? '' : number_format($row['lebih_pakai_kg'] / $row['kgs'], 2) * 100 . '%',
                $retur_kg_psn,        // Z
                $retur_persen_psn,    // AA
                $retur_kg_po,         // AB
                $retur_persen_po,
                $row['kategori'],
            ], null, 'A' . $rowNum);

            $sheet->getRowDimension($rowNum)->setRowHeight(-1);
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

        // Atur baris kosong setelah data sampai baris 28
        for ($i = $rowNum; $i <= 28; $i++) {
            $sheet->mergeCells("A{$i}:B{$i}");
            $sheet->getRowDimension($i)->setRowHeight(18); // Tetapkan tinggi tetap
        }

        //Tanda Tangan
        $sheet->mergeCells('A30:D30');
        $sheet->setCellValue('A30', 'MANAJEMEN NS');
        $sheet->getStyle('A30:D30')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->mergeCells('E30:F30');
        $sheet->setCellValue('E30', 'KEPALA AREA');
        $sheet->getStyle('E30:F30')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->mergeCells('G30:H30');
        $sheet->setCellValue('G30', 'IE TEKNISI');
        $sheet->getStyle('G30:H30')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->mergeCells('I30:K30');
        $sheet->setCellValue('I30', 'PIC PACKING');
        $sheet->getStyle('I30:K30')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->mergeCells('L30:N30');
        $sheet->setCellValue('L30', 'PPC');
        $sheet->getStyle('L30:N30')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->mergeCells('O30:Q30');
        $sheet->setCellValue('O30', 'PPC');
        $sheet->getStyle('O30:Q30')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->mergeCells('R30:T30');
        $sheet->setCellValue('R30', 'PPC');
        $sheet->getStyle('R30:T30')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->mergeCells('U30:W30');
        $sheet->setCellValue('U30', 'GD BENANG');
        $sheet->getStyle('U30:W30')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->mergeCells('X30:AA30');
        $sheet->setCellValue('X30', 'MENGETAHUI');
        $sheet->getStyle('X30:AA30')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->mergeCells('AB30:AD30');
        $sheet->setCellValue('AB30', 'MENGETAHUI');
        $sheet->getStyle('AB30:AD30')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        for ($i = 29; $i <= 33; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(18);
        }

        $sheet->getStyle("A11:AD28")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        // Output Excel
        $filename = 'FORM RETUR ' . $area . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        $writer->save('php://output');
        exit;
    }
    public function exportDatangBenang()
    {
        $key = $this->request->getGet('key');
        $tanggalAwal = $this->request->getGet('tanggal_awal');
        $tanggalAkhir = $this->request->getGet('tanggal_akhir');

        $apiUrl = 'http://172.23.44.14/MaterialSystem/public/api/filterDatangBenang?key=' . urlencode($key) . '&tanggal_awal=' . $tanggalAwal . '&tanggal_akhir=' . $tanggalAkhir;
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
        $sheet->mergeCells('A1:U1'); // Menggabungkan sel untuk judul
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Header
        $header = ["No", "Foll Up", "No Model", "No Order", "Buyer", "Delivery Awal", "Delivery Akhir", "Order Type", "Item Type", "Kode Warna", "Warna", "KG Pesan", "Tanggal Datang", "Kgs Datang", "Cones Datang", "LOT Datang", "No Surat Jalan", "LMD", "GW", "Harga", "Nama Cluster"];
        $sheet->fromArray([$header], NULL, 'A3');

        // Styling Header
        $sheet->getStyle('A3:U3')->getFont()->setBold(true);
        $sheet->getStyle('A3:U3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3:U3')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        // Data
        $row = 4;
        foreach ($data as $index => $item) {
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
                    $item['tgl_masuk'],
                    number_format($item['kgs_kirim'], 2),
                    $item['cones_kirim'],
                    $item['lot_kirim'],
                    $item['no_surat_jalan'],
                    $item['l_m_d'],
                    number_format($item['gw_kirim'], 2),
                    number_format($item['harga'], 2),
                    $item['nama_cluster']
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
        $sheet->getStyle('A3:U' . ($row - 1))->applyFromArray($styleArray);

        // Set auto width untuk setiap kolom
        foreach (range('A', 'U') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Set isi tabel agar rata tengah
        $sheet->getStyle('A4:U' . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A4:U' . ($row - 1))->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

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

        $apiUrl = 'http://172.23.44.14/MaterialSystem/public/api/filterPoBenang?key=' . urlencode($key);
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
                    $item['created_at'],
                    $item['tgl_po'],
                    $item['foll_up'],
                    $item['no_model'],
                    $item['no_order'],
                    $item['keterangan'],
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

        $apiUrl = 'http://172.23.44.14/MaterialSystem/public/api/filterPengiriman?key=' . urlencode($key) . '&tanggal_awal=' . urlencode($tanggalAwal) . '&tanggal_akhir=' . urlencode($tanggalAkhir);
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
        $apiUrl = 'http://172.23.44.14/MaterialSystem/public/api/filterReportGlobal?key=' . urlencode($key);
        $material = @file_get_contents($apiUrl);

        if ($material !== FALSE) {
            $data = json_decode($material, true);
        }

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
        foreach ($data as $item) {
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
            $sheet->setCellValue('K' . $row, '-'); // qty po (+)
            $sheet->setCellValue('L' . $row, isset($item['kgs_stock_awal']) ? number_format($item['kgs_stock_awal'], 2, '.', '') : 0); // stock awal
            $sheet->setCellValue('M' . $row, '-'); // stock opname
            $sheet->setCellValue('N' . $row, isset($item['kgs_kirim']) ? number_format($item['kgs_kirim'], 2, '.', '') : 0); // datan solid
            $sheet->setCellValue('O' . $row, '-'); // (+) datang solid
            $sheet->setCellValue('P' . $row, '-'); // ganti retur
            $sheet->setCellValue('Q' . $row, '-'); // datang lurex
            $sheet->setCellValue('R' . $row, '-'); // (+) datang lurex
            $sheet->setCellValue('S' . $row, '-'); // retur pb gbn
            $sheet->setCellValue('T' . $row, isset($item['kgs_retur']) ? number_format($item['kgs_retur'], 2, '.', '') : 0); // retur bp area
            $sheet->setCellValue('U' . $row, isset($item['kgs_out']) ? number_format($item['kgs_out'], 2, '.', '') : 0); // pakai area
            $sheet->setCellValue('V' . $row, '-'); // pakai lain-lain
            $sheet->setCellValue('W' . $row, '-'); // retur stock
            $sheet->setCellValue('X' . $row, '-'); // retur titip
            $sheet->setCellValue('Y' . $row, '-'); // dipinjam
            $sheet->setCellValue('Z' . $row, '-'); // pindah order
            $sheet->setCellValue('AA' . $row, '-'); // pindah ke stock mati
            $sheet->setCellValue('AB' . $row, isset($item['kgs_in_out']) ? number_format($item['kgs_in_out'], 2, '.', '') : 0); // stock akhir

            // Tagihan GBN dan Jatah Area perhitungan
            $tagihanGbn = isset($item['kgs']) ? $item['kgs'] - ($item['kgs_kirim'] + $item['kgs_stock_awal']) : 0;
            $jatahArea = isset($item['kgs']) ? $item['kgs'] - $item['kgs_out'] : 0;

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
            if (strpos($name, 'STOCK AWAL') !== false) {
                // Judul
                $newSheet->mergeCells('A1:K1');
                $newSheet->setCellValue('A1', 'REPORT STOCK AWAL ' . $key);
                $newSheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $newSheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Header
                $headerStockAwal = ['No', 'No Model', 'Delivery', 'Item Type', 'Kode Warna', 'Warna', 'Qty', 'Cones', 'Lot', 'Cluster', 'Keterangan'];
                $col = 'A';
                foreach ($headerStockAwal as $header) {
                    $newSheet->setCellValue($col . '3', $header);
                    $newSheet->getStyle($col . '3')->getFont()->setBold(true);
                    $col++;
                }

                // Tambahkan border untuk header A3:K3
                $newSheet->getStyle('A3:K3')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);
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
}
