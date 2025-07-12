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
        $tglTurun = $this->request->getPost('tgl_turun_order');
        $awal = $this->request->getPost('awal');
        $akhir = $this->request->getPost('akhir');

        $validate = [
            'buyer' => $buyer,
            'area' => $area,
            'jarum' => $jarum,
            'pdk' => $pdk,
            'tglTurun' => $tglTurun,
            'awal' => $awal,
            'akhir' => $akhir,
        ];

        $data = $this->ApsPerstyleModel->getDataOrder($validate);
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
        $sheet->setCellValue('Q3', 'SISA');
        $sheet->setCellValue('R3', 'JLN MC');
        $sheet->setCellValue('S3', 'DESCRIPTION');
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

        // Tulis data mulai dari baris 2
        $row = 4;
        $no = 1;

        foreach ($data as $item) {
            $sheet->setCellValue('A' . $row, $item['created_at']);
            $sheet->setCellValue('B' . $row, $no++);
            $sheet->setCellValue('C' . $row, $item['repeat_from']);
            $sheet->setCellValue('D' . $row, $item['mastermodel']);
            $sheet->setCellValue('E' . $row, '');
            $sheet->setCellValue('F' . $row, '');
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
            $sheet->setCellValue('Q' . $row, $item['sisa_pcs']);
            $sheet->setCellValue('R' . $row, '');
            $sheet->setCellValue('S' . $row, $item['description']);
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
}
