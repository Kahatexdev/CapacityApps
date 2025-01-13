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
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class TimterController extends BaseController
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
    public function excelTimter()
    {
        $role = session()->get('role');
        $area = $this->request->getPost('area');
        $jarum = $this->request->getPost('jarum');
        $pdk = $this->request->getPost('pdk');
        $awal = $this->request->getPost('awal');

        $data = [
            'area' => $area,
            'jarum' => $jarum,
            'pdk' => $pdk,
            'awal' => $awal,
        ];

        $dataTimter = $this->orderModel->getDataTimter($data);
        $prodTimter = $this->orderModel->getDetailProdTimter($data);
        $jlMC = $this->orderModel->getprodSummaryPertgl($data);

        $uniqueData = [];

        // Iterasi untuk mendapatkan data yang relevan
        foreach ($dataTimter as $sizeItem) {
            foreach ($prodTimter as $item) {
                // Membuat key berdasarkan kombinasi informasi dari produksi dan size
                $key = $sizeItem['machinetypeid'] . '-' . $sizeItem['mastermodel'] . '-' . $sizeItem['size'];

                // Pastikan key belum ada, jika belum maka tambahkan data
                if (!isset($uniqueData[$key])) {
                    // Menambahkan data ke $uniqueData
                    $uniqueData[$key] = [
                        'seam' => $sizeItem['seam'],
                        'kd_buyer_order' => $sizeItem['kd_buyer_order'],
                        'area' => $sizeItem['factory'],
                        'no_order' => $sizeItem['no_order'],
                        'machinetypeid' => $sizeItem['machinetypeid'],
                        'mastermodel' => $sizeItem['mastermodel'],
                        'inisial' => $sizeItem['inisial'],  // Mengambil data inisial dari $sizeItem
                        'size' => $sizeItem['size'],        // Mengambil size dari $sizeItem
                        'color' => $sizeItem['color'],
                        'smv' => $sizeItem['smv'],
                        'delivery' => $item['delivery'],
                        'sisa' => $sizeItem['sisa'],
                        'qty_prod' => $sizeItem['qty'] - $sizeItem['sisa'],
                        'qty' => $sizeItem['qty'],
                        'ttl_dz' => 0,
                        'no_mesin' => [],  // Menyimpan array untuk no_mesin 
                    ];
                }

                // Menambahkan 'no_mesin' jika machinetypeid, mastermodel, dan size cocok
                if (
                    $sizeItem['machinetypeid'] === $item['machinetypeid'] &&
                    $sizeItem['mastermodel'] === $item['mastermodel'] &&
                    $sizeItem['size'] === $item['size']
                ) {
                    // Pastikan 'no_mesin' sudah ada di array 'no_mesin' sebelum mengaksesnya
                    if (!array_key_exists($item['no_mesin'], $uniqueData[$key]['no_mesin'])) {
                        // Inisialisasi jika 'no_mesin' belum ada
                        $uniqueData[$key]['no_mesin'][$item['no_mesin']] = [
                            'shift_a' => 0,
                            'shift_b' => 0,
                            'shift_c' => 0,
                            'total_shift' => 0,
                            'pa' => 0,
                        ];
                    }

                    // Update qty dan running untuk no_mesin
                    $uniqueData[$key]['no_mesin'][$item['no_mesin']]['shift_a'] += $item['shift_a'];
                    $uniqueData[$key]['no_mesin'][$item['no_mesin']]['shift_b'] += $item['shift_b'];
                    $uniqueData[$key]['no_mesin'][$item['no_mesin']]['shift_c'] += $item['shift_c'];
                    $uniqueData[$key]['no_mesin'][$item['no_mesin']]['pa'] += $item['pa'];

                    // Hitung total shift
                    $total_shift = $uniqueData[$key]['no_mesin'][$item['no_mesin']]['shift_a'] +
                        $uniqueData[$key]['no_mesin'][$item['no_mesin']]['shift_b'] +
                        $uniqueData[$key]['no_mesin'][$item['no_mesin']]['shift_c'] +
                        $uniqueData[$key]['no_mesin'][$item['no_mesin']]['pa'];

                    // Update total_shift
                    $uniqueData[$key]['no_mesin'][$item['no_mesin']]['total_shift'] = $total_shift;
                }

                // **Menjumlahkan total_shift untuk semua no_mesin dalam $key**
                $uniqueData[$key]['ttl_dz'] = 0; // Reset ttl_dz sebelum menjumlahkan

                // Loop untuk menjumlahkan total_shift untuk semua no_mesin dalam kombinasi $key
                foreach ($uniqueData[$key]['no_mesin'] as $noMesinData) {
                    // Menjumlahkan total shift dari setiap no_mesin
                    $uniqueData[$key]['ttl_dz'] += $noMesinData['total_shift'];
                }
            }
        }

        // Buat spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Style
        $styleHeader = [
            'font' => [
                'size' => 12,
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
                'size' => 11,
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
                'size' => 11,
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
        foreach ($columns as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

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
        $drawing->setOffsetY(5);
        $drawing->setOffsetX(70);

        $drawing->setWorksheet($sheet);

        $sheet->mergeCells('A1:A3')->getStyle('A1:A3')->applyFromArray([
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

        // mengatur lebar kolom A
        $sheet->getColumnDimension('A')->setWidth(221);
        // mengatur tinggi baris 1
        $heightInCm = 0.98;
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

        $sheet->setCellValue('B1', 'FORMULIR');
        $sheet->mergeCells('B1:AF1')->getStyle('B1:AF1')->applyFromArray([
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

        // mengatur tinggi baris 2 & 3
        $heightInCm2 = 0.56;
        $heightInPoints2 = $heightInCm2 / 0.0352778;
        $sheet->getRowDimension('2')->setRowHeight($heightInPoints2);
        $sheet->getRowDimension('3')->setRowHeight($heightInPoints2);

        $sheet->setCellValue('B2', 'DEPARTEMEN KAOSKAKI');
        $sheet->mergeCells('B2:AF2')->getStyle('B2:AF2')->applyFromArray([
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

        $sheet->setCellValue('B3', 'TIMBANG TERIMA');
        $sheet->mergeCells('B3:AF3')->getStyle('B3:AF3')->applyFromArray([
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
        $sheet->getStyle('A4')->applyFromArray([
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

        $sheet->setCellValue('B4', ' FOR–KK–025/REV-01/HAL_/_');
        $sheet->mergeCells('B4:W4')->getStyle('B4:W4')->applyFromArray([
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

        $sheet->setCellValue('X4', 'Tanggal Revisi ');
        $sheet->mergeCells('X4:AA4')->getStyle('X4:AA4')->applyFromArray([
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

        $sheet->setCellValue('AB4', '31 Desember 2018');
        $sheet->mergeCells('AB4:AF4')->getStyle('AB4:AF4')->applyFromArray([
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

        $sheet->setCellValue('A5', ' AREA : ' . $area);
        $sheet->mergeCells('A5:W5')->getStyle('A5:W5')->applyFromArray([
            'font' => [
                'size' => 12,
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

        $sheet->setCellValue('X5', 'Tanggal : ' . $awal);
        $sheet->mergeCells('X5:AF5')->getStyle('U5:AF5')->applyFromArray([
            'font' => [
                'size' => 12,
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
        // header ISO end

        // header tabel timter
        $sheet->setCellValue('A6', 'SEAM');
        $sheet->mergeCells('A6:A7')->getStyle('A6:A7')->applyFromArray([
            'font' => [
                'size' => 12,
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
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
                'right' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->setCellValue('B6', 'BUYER');
        $sheet->mergeCells('B6:B7')->getStyle('B6:B7')->applyFromArray($styleHeader);

        $sheet->setCellValue('C6', 'NO ORDER');
        $sheet->mergeCells('C6:C7')->getStyle('C6:C7')->applyFromArray($styleHeader);

        $sheet->setCellValue('D6', 'JRM');
        $sheet->mergeCells('D6:D7')->getStyle('D6:D7')->applyFromArray($styleHeader);

        $sheet->setCellValue('E6', 'PDK');
        $sheet->mergeCells('E6:E7')->getStyle('E6:E7')->applyFromArray($styleHeader);

        $sheet->setCellValue('F6', 'IN');
        $sheet->mergeCells('F6:F7')->getStyle('F6:F7')->applyFromArray($styleHeader);

        $sheet->setCellValue('G6', 'STYLE');
        $sheet->mergeCells('G6:G7')->getStyle('G6:G7')->applyFromArray($styleHeader);

        $sheet->setCellValue('H6', 'COLOR');
        $sheet->mergeCells('H6:H7')->getStyle('H6:H7')->applyFromArray($styleHeader);

        $sheet->setCellValue('I6', 'SMV');
        $sheet->mergeCells('I6:I7')->getStyle('I6:I7')->applyFromArray($styleHeader);

        $sheet->setCellValue('J6', 'DELIVERY');
        $sheet->mergeCells('J6:J7')->getStyle('J6:J7')->applyFromArray($styleHeader);

        $sheet->setCellValue('K6', 'TARGET');
        $sheet->mergeCells('K6:K7')->getStyle('K6:K7')->applyFromArray($styleHeader);

        $sheet->setCellValue('L6', 'JML MC');
        $sheet->mergeCells('L6:L7')->getStyle('L6:L7')->applyFromArray($styleHeader);

        $sheet->setCellValue('M6', 'NO MC');
        $sheet->mergeCells('M6:M7')->getStyle('M6:M7')->applyFromArray($styleHeader);

        $sheet->setCellValue('N6', 'A');
        $sheet->mergeCells('N6:O6')->getStyle('N6:O6')->applyFromArray($styleHeader);
        $sheet->setCellValue('N7', 'DZ');
        $sheet->getStyle('N7')->applyFromArray($styleHeader2);
        $sheet->setCellValue('O7', 'PCS');
        $sheet->getStyle('O7')->applyFromArray($styleHeader2);

        $sheet->setCellValue('P6', 'B');
        $sheet->mergeCells('P6:Q6')->getStyle('P6:Q6')->applyFromArray($styleHeader);
        $sheet->setCellValue('P7', 'DZ');
        $sheet->getStyle('P7')->applyFromArray($styleHeader2);
        $sheet->setCellValue('Q7', 'PCS');
        $sheet->getStyle('Q7')->applyFromArray($styleHeader2);

        $sheet->setCellValue('R6', 'C');
        $sheet->mergeCells('R6:S6')->getStyle('R6:S6')->applyFromArray($styleHeader);
        $sheet->setCellValue('R7', 'DZ');
        $sheet->getStyle('R7')->applyFromArray($styleHeader2);
        $sheet->setCellValue('S7', 'PCS');
        $sheet->getStyle('S7')->applyFromArray($styleHeader2);

        $sheet->setCellValue('T6', 'PA');
        $sheet->mergeCells('T6:U6')->getStyle('T6:U6')->applyFromArray($styleHeader);
        $sheet->setCellValue('T7', 'DZ');
        $sheet->getStyle('T7')->applyFromArray($styleHeader2);
        $sheet->setCellValue('U7', 'PCS');
        $sheet->getStyle('U7')->applyFromArray($styleHeader2);

        $sheet->setCellValue('V6', 'TOTAL');
        $sheet->mergeCells('V6:W6')->getStyle('V6:W6')->applyFromArray($styleHeader);
        $sheet->setCellValue('V7', 'DZ');
        $sheet->getStyle('V7')->applyFromArray($styleHeader2);
        $sheet->setCellValue('W7', 'PCS');
        $sheet->getStyle('W7')->applyFromArray($styleHeader2);

        $sheet->setCellValue('X6', 'PRODUKSI');
        $sheet->mergeCells('X6:Y6')->getStyle('X6:Y6')->applyFromArray($styleHeader);
        $sheet->setCellValue('X7', 'DZ');
        $sheet->getStyle('X7')->applyFromArray($styleHeader2);
        $sheet->setCellValue('Y7', 'PCS');
        $sheet->getStyle('Y7')->applyFromArray($styleHeader2);

        $sheet->setCellValue('Z6', 'QTY DELIVERY');
        $sheet->mergeCells('Z6:AA6')->getStyle('Z6:AA6')->applyFromArray($styleHeader);
        $sheet->setCellValue('Z7', 'DZ');
        $sheet->getStyle('Z7')->applyFromArray($styleHeader2);
        $sheet->setCellValue('AA7', 'PCS');
        $sheet->getStyle('AA7')->applyFromArray($styleHeader2);

        $sheet->setCellValue('AB6', 'TOTAL PRODUKSI');
        $sheet->mergeCells('AB6:AC6')->getStyle('AB6:AC6')->applyFromArray($styleHeader);
        $sheet->setCellValue('AB7', 'DZ');
        $sheet->getStyle('AB7')->applyFromArray($styleHeader2);
        $sheet->setCellValue('AC7', 'PCS');
        $sheet->getStyle('AC7')->applyFromArray($styleHeader2);

        $sheet->setCellValue('AD6', 'SISA PRODUKSI');
        $sheet->mergeCells('AD6:AE6')->getStyle('AD6:AE6')->applyFromArray($styleHeader);
        $sheet->setCellValue('AD7', 'DZ');
        $sheet->getStyle('AD7')->applyFromArray($styleHeader2);
        $sheet->setCellValue('AE7', 'PCS');
        $sheet->getStyle('AE7')->applyFromArray($styleHeader2);

        $sheet->setCellValue('AF6', 'KETERANGAN');
        $sheet->mergeCells('AF6:AF7')->getStyle('AF6:AF7')->applyFromArray([
            'font' => [
                'size' => 12,
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
        $sheet->getColumnDimension('AF')->setAutoSize(true);
        // end Header Timter

        // body start
        $row = 8; // baris awal isi tabel

        foreach ($uniqueData as $key => $id) :
            if (count($id['no_mesin']) > 0) {
                $rowspan = count($id['no_mesin']);
            } else {
                $rowspan = 0;
            }

            $smv = $id['smv'];
            $target = 0;
            if (!empty($smv)) {
                $target = 86400 / floatval($smv) * 0.8 / 24;
            }

            // Ambil countjlMc untuk ukuran dan model tertentu
            $countjlMc = 0;
            foreach ($jlMC as $jl) {
                if ($jl['mastermodel'] == $id['mastermodel'] && $jl['size'] == $id['size']) {
                    $countjlMc = $jl['jl_mc'];
                    break; // Hentikan loop setelah menemukan count
                }
            }
            // Jika tidak ada countjlMc, set ke 1 untuk menampilkan setidaknya satu baris
            $countjlMc = $countjlMc ?: 0;

            // Gabungkan sel untuk rowspan
            if ($rowspan > 1) {
                $sheet->mergeCells('A' . $row . ':A' . ($row + $rowspan - 1));
                $sheet->mergeCells('B' . $row . ':B' . ($row + $rowspan - 1));
                $sheet->mergeCells('C' . $row . ':C' . ($row + $rowspan - 1));
                $sheet->mergeCells('D' . $row . ':D' . ($row + $rowspan - 1));
                $sheet->mergeCells('E' . $row . ':E' . ($row + $rowspan - 1));
                $sheet->mergeCells('F' . $row . ':F' . ($row + $rowspan - 1));
                $sheet->mergeCells('G' . $row . ':G' . ($row + $rowspan - 1));
                $sheet->mergeCells('H' . $row . ':H' . ($row + $rowspan - 1));
                $sheet->mergeCells('I' . $row . ':I' . ($row + $rowspan - 1));
                $sheet->mergeCells('J' . $row . ':J' . ($row + $rowspan - 1));
                $sheet->mergeCells('K' . $row . ':K' . ($row + $rowspan - 1));
                $sheet->mergeCells('L' . $row . ':L' . ($row + $rowspan - 1));
            }

            // Isi data untuk baris pertama (merged cells)
            $sheet->setCellValue('A' . $row, $id['seam']);
            $sheet->setCellValue('B' . $row, $id['kd_buyer_order']);
            $sheet->setCellValue('C' . $row, $id['no_order']);
            $sheet->setCellValue('D' . $row, $id['machinetypeid']);
            $sheet->setCellValue('E' . $row, $id['mastermodel']);
            $sheet->setCellValue('F' . $row, $id['inisial']);
            $sheet->setCellValue('G' . $row, $id['size']);
            $sheet->setCellValue('H' . $row, $id['color']);
            $sheet->setCellValue('I' . $row, $id['smv']);
            $sheet->setCellValue('J' . $row, $id['delivery']);
            $sheet->setCellValue('K' . $row, number_format($target, 0));
            $sheet->setCellValue('L' . $row, $countjlMc);

            // style body
            $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L'];

            foreach ($columns as $column) {
                if ($rowspan > 1) {
                    $sheet->getStyle($column . $row)->applyFromArray($styleBody);
                } else {
                    $sheet->getStyle($column . $row)->applyFromArray($styleBody);
                }
            }

            // Pengisian untuk baris yang sudah di-merge
            if (empty($id['no_mesin'])) {
                $sheet->setCellValue('X' . $row, floor($id['ttl_dz'] / 24));
                $sheet->setCellValue('Y' . $row, floor($id['ttl_dz'] % 24));
                $sheet->setCellValue('Z' . $row, floor($id['qty'] / 24));
                $sheet->setCellValue('AA' . $row, floor($id['qty'] % 24));
                $sheet->setCellValue('AB' . $row, floor($id['qty_prod'] / 24));
                $sheet->setCellValue('AC' . $row, floor($id['qty_prod'] % 24));
                $sheet->setCellValue('AD' . $row, floor($id['sisa'] / 24));
                $sheet->setCellValue('AE' . $row, floor($id['sisa'] % 24));
                $sheet->setCellValue('AF' . $row, '');

                // style body
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
                $sheet->getStyle('AA' . $row)->applyFromArray($styleBody);
                $sheet->getStyle('AB' . $row)->applyFromArray($styleBody);
                $sheet->getStyle('AC' . $row)->applyFromArray($styleBody);
                $sheet->getStyle('AD' . $row)->applyFromArray($styleBody);
                $sheet->getStyle('AE' . $row)->applyFromArray($styleBody);
                $sheet->getStyle('AF' . $row)->applyFromArray($styleBody);
                $row++;
            } else {
                // Proses untuk 'no_mesin' ada
                $loopIndex = 0;
                foreach ($id['no_mesin'] as $noMc => $rows) {
                    // Inisialisasi variabel 
                    $shift_a = $shift_b = $shift_c = $pa = 0;
                    $pcs_a = $pcs_b = $pcs_c = $pcs_pa = 0;

                    // Menggunakan null coalescing untuk mengatur nilai default 0 jika null
                    $no_mesin = $rows['no_mesin'] ?? 0;
                    $shift_a = $rows['shift_a'] ?? 0;
                    $shift_b = $rows['shift_b'] ?? 0;
                    $shift_c = $rows['shift_c'] ?? 0;
                    $pa = $rows['pa'] ?? 0;

                    // Hitung pcs
                    $pcs_a = $shift_a % 24;
                    $pcs_b = $shift_b % 24;
                    $pcs_c = $shift_c % 24;
                    $pcs_pa = $pa % 24;

                    // Hitung total dz & pcs
                    $total_dz = $rows['total_shift'];
                    $total_pcs = $rows['total_shift'] % 24;

                    $sheet->setCellValue('M' . $row, $noMc);
                    $sheet->setCellValue('N' . $row, floor($shift_a / 24));
                    $sheet->setCellValue('O' . $row, $pcs_a);
                    $sheet->setCellValue('P' . $row, floor($shift_b / 24));
                    $sheet->setCellValue('Q' . $row, $pcs_b);
                    $sheet->setCellValue('R' . $row, floor($shift_c / 24));
                    $sheet->setCellValue('S' . $row, $pcs_c);
                    $sheet->setCellValue('T' . $row, floor($pa / 24));
                    $sheet->setCellValue('U' . $row, $pcs_pa);
                    $sheet->setCellValue('V' . $row, floor($total_dz / 24));
                    $sheet->setCellValue('W' . $row, $total_pcs);

                    // style body
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
                    $sheet->getStyle('AA' . $row)->applyFromArray($styleBody);
                    $sheet->getStyle('AB' . $row)->applyFromArray($styleBody);
                    $sheet->getStyle('AC' . $row)->applyFromArray($styleBody);
                    $sheet->getStyle('AD' . $row)->applyFromArray($styleBody);
                    $sheet->getStyle('AE' . $row)->applyFromArray($styleBody);
                    $sheet->getStyle('AF' . $row)->applyFromArray($styleBody);


                    if ($loopIndex == 0) {
                        $sheet->setCellValue('X' . $row, floor($id['ttl_dz'] / 24));
                        $sheet->setCellValue('Y' . $row, floor($id['ttl_dz'] % 24));
                        $sheet->setCellValue('Z' . $row, floor($id['qty'] / 24));
                        $sheet->setCellValue('AA' . $row, floor($id['qty'] % 24));
                        $sheet->setCellValue('AB' . $row, floor($id['qty_prod'] / 24));
                        $sheet->setCellValue('AC' . $row, floor($id['qty_prod'] % 24));
                        $sheet->setCellValue('AD' . $row, floor($id['sisa'] / 24));
                        $sheet->setCellValue('AE' . $row, floor($id['sisa'] % 24));
                        $sheet->setCellValue('AF' . $row, '');

                        $sheet->getStyle('X' . $row)->applyFromArray($styleBody);
                        $sheet->getStyle('Y' . $row)->applyFromArray($styleBody);
                        $sheet->getStyle('Z' . $row)->applyFromArray($styleBody);
                        $sheet->getStyle('AA' . $row)->applyFromArray($styleBody);
                        $sheet->getStyle('AB' . $row)->applyFromArray($styleBody);
                        $sheet->getStyle('AC' . $row)->applyFromArray($styleBody);
                        $sheet->getStyle('AD' . $row)->applyFromArray($styleBody);
                        $sheet->getStyle('AE' . $row)->applyFromArray($styleBody);
                        $sheet->getStyle('AF' . $row)->applyFromArray($styleBody);
                    }
                    $row++;
                    $loopIndex++;
                }
            }

        endforeach;


        // Set judul file dan header untuk download
        $filename = 'TIMTER ' . $area . ' ' . $jarum . ' ' . $awal . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // Tulis file excel ke output
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
