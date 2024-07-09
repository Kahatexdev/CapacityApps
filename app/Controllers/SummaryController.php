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

class SummaryController extends BaseController
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

    public function excelSummaryPerTgl()
    {
        $buyer = $this->request->getPost('buyer');
        $area = $this->request->getPost('area');
        $jarum = $this->request->getPost('jarum');
        $pdk = $this->request->getPost('pdk');
        $awal = $this->request->getPost('awal');
        $akhir = $this->request->getPost('akhir');
        
        $data = [
            'buyer' => $buyer,
            'area' => $area,
            'jarum' => $jarum,
            'pdk' => $pdk,
            'awal' => $awal,
            'akhir' => $akhir,
        ];

        $dataSummaryPertgl = $this->orderModel->getdataSummaryPertgl($data);
        $prodSummaryPertgl = $this->orderModel->getProdSummaryPertgl($data);

        // agar data tgl produksi menjadi unik
        $tgl_produksi = [];
        foreach ($prodSummaryPertgl as $item) {
            $tgl_produksi[$item['tgl_produksi']] = $item['tgl_produksi'];
        }
        $tgl_produksi = array_values($tgl_produksi);
        // Sort ASC
        sort($tgl_produksi);
        
        $uniqueData = [];
        foreach ($dataSummaryPertgl as $item) {
            $key = $item['machinetypeid'] . '-' . $item['mastermodel'] . '-' . $item['size'];
            if (!isset($uniqueData[$key])) {
                $uniqueData[$key] = [
                    'machinetypeid' => $item['machinetypeid'],
                    'mastermodel' => $item['mastermodel'],
                    'size' => $item['size'],
                    'qty' => 0,
                    'running' => 0,
                    'ttl_prod' => 0,
                    'ttl_jlmc' => 0,
                ];
            }
            $uniqueData[$key]['qty'] += $item['qty'];
            $uniqueData[$key]['running'] += $item['running'];
            $uniqueData[$key]['ttl_prod'] += $item['qty_produksi'];
            $uniqueData[$key]['ttl_jlmc'] += $item['jl_mc'];
        }
        // Sort ASC
        sort($uniqueData);
        
        // Buat spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

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
        $styleSubTotal = [
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

        // Judul
        $sheet->setCellValue('A1', 'SUMMARY PRODUKSI');
        $sheet->mergeCells('A1:G1');
        // Mengatur teks menjadi rata tengah dan huruf tebal
        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $row_header = 3;
        $row_header2 = 4;
        // Isi header
        $sheet->setCellValue('A' . $row_header , 'Tanggal');
        $sheet->mergeCells('A'.$row_header.':G'.$row_header);
        $sheet->getStyle('A'.$row_header.':G'.$row_header)->applyFromArray($styleHeader);

        // looping tgl produksi
        $col = 'H'; // Kolom awal tanggal produksi
        $col2 = 'I'; // Kolom kedua untuk mergeCells

        // Konversi huruf kolom ke nomor indeks kolom
        $col_index = Coordinate::columnIndexFromString($col);
        $col2_index = Coordinate::columnIndexFromString($col2);
        
        foreach ($tgl_produksi as $tgl_prod) {
            $sheet->setCellValue($col . $row_header , $tgl_prod); 
            $sheet->mergeCells($col . $row_header .':' . $col2 . $row_header); // Merge sel antara kolom $col dan $col2
            $sheet->getStyle($col . $row_header .':' . $col2 . $row_header)->applyFromArray($styleHeader);


            // Tambahkan 2 pada indeks kolom
            $col_index += 2;
            $col2_index = $col_index + 1; // Tambahkan 1 pada indeks kedua kolom

            // Konversi kembali dari nomor indeks kolom ke huruf kolom
            $col = Coordinate::stringFromColumnIndex($col_index);
            $col2 = Coordinate::stringFromColumnIndex($col2_index);
        }
        $sheet->setCellValue('A'.$row_header2, 'Needle');
        $sheet->setCellValue('B'.$row_header2, 'No Model');
        $sheet->setCellValue('C'.$row_header2, 'Style Size');
        $sheet->setCellValue('D'.$row_header2, 'Qty PO (dz)');
        $sheet->setCellValue('E'.$row_header2, 'Running');
        $sheet->setCellValue('F'.$row_header2, 'Total Prod');
        $sheet->setCellValue('G'.$row_header2, 'Total Jl Mc');
        // style untuk header
        $sheet->getStyle('A'.$row_header2)->applyFromArray($styleHeader);
        $sheet->getStyle('B'.$row_header2)->applyFromArray($styleHeader);
        $sheet->getStyle('C'.$row_header2)->applyFromArray($styleHeader);
        $sheet->getStyle('D'.$row_header2)->applyFromArray($styleHeader);
        $sheet->getStyle('E'.$row_header2)->applyFromArray($styleHeader);
        $sheet->getStyle('F'.$row_header2)->applyFromArray($styleHeader);
        $sheet->getStyle('G'.$row_header2)->applyFromArray($styleHeader);

        // Tambahkan header dinamis untuk tanggal produksi
        $col3 = 'H';
        foreach ($tgl_produksi as $tgl_prod) {
            $sheet->setCellValue($col3 . $row_header2, 'Prod (dz)');
            $sheet->getStyle($col3 . $row_header2)->applyFromArray($styleHeader);
            $col3++;
            $sheet->setCellValue($col3 . $row_header2, 'Jl Mc');
            $sheet->getStyle($col3 . $row_header2)->applyFromArray($styleHeader);
            $col3++;
        }

        // // Isi data
        $row = 5;
        $ttl_prod = 0;
        $ttl_jlmc = 0;
        $totalProdPerModel = array_fill_keys($tgl_produksi, 0);
        $totalJlMcPerModel = array_fill_keys($tgl_produksi, 0);
        foreach ($uniqueData as $key => $id) {
            $ttl_prod += $id['ttl_prod'];
            $ttl_jlmc += $id['ttl_jlmc'];
            $sheet->setCellValue('A' . $row, $id['machinetypeid']);
            $sheet->setCellValue('B' . $row, $id['mastermodel']);
            $sheet->setCellValue('C' . $row, $id['size']);
            $sheet->setCellValue('D' . $row, number_format($id['qty']/24, 2));
            $sheet->setCellValue('E' . $row, $id['running'] . ' days');
            $sheet->setCellValue('F' . $row, number_format($id['ttl_prod']/24, 2));
            $sheet->setCellValue('G' . $row, $id['ttl_jlmc']);
            // 
            $sheet->getStyle('A' . $row)->applyFromArray($styleBody);
            $sheet->getStyle('B' . $row)->applyFromArray($styleBody);
            $sheet->getStyle('C' . $row)->applyFromArray($styleBody);
            $sheet->getStyle('D' . $row)->applyFromArray($styleBody);
            $sheet->getStyle('E' . $row)->applyFromArray($styleBody);
            $sheet->getStyle('F' . $row)->applyFromArray($styleBody);
            $sheet->getStyle('G' . $row)->applyFromArray($styleBody);

            // looping kolom qty produksi & jl mc pertanggal
            $col4 = "H";
            foreach ($tgl_produksi as $tgl_prod2) {
                $qty_produksi = 0;
                $jl_mc = 0;
                foreach ($prodSummaryPertgl as $prod) {
                    if ($id['machinetypeid'] == $prod['machinetypeid'] && $id['mastermodel'] == $prod['mastermodel']
                    && $id['size'] == $prod['size'] && $tgl_prod2 == $prod['tgl_produksi']) {
                        $qty_produksi = $prod['qty_produksi'];
                        $jl_mc = $prod['jl_mc'];
                        break;
                    }
                }
                // Update total production and machine count per model
                $totalProdPerModel[$tgl_prod2] += $qty_produksi;
                $totalJlMcPerModel[$tgl_prod2] += $jl_mc;

                $sheet->setCellValue($col4 . $row, number_format($qty_produksi/24, 2));
                $sheet->getStyle($col4 . $row)->applyFromArray($styleBody);
                $col4++;
                
                $sheet->setCellValue($col4 . $row, $jl_mc);
                $sheet->getStyle($col4 . $row)->applyFromArray($styleBody);
                $col4++;
            }
            $row++;

            $rowTotal = $row;
            if (!isset($uniqueData[$key+1]) || (isset($uniqueData[$key+1]) && $uniqueData[$key+1]['mastermodel'] != $id['mastermodel'])) {
                $sheet->setCellValue('A' . $rowTotal, "TOTAL " . $id['mastermodel'] . ':');
                $sheet->mergeCells('A' . $rowTotal . ':E' . $rowTotal);
                $sheet->getStyle('A' . $rowTotal . ':E' . $rowTotal)->applyFromArray($styleSubTotal);

                $sheet->setCellValue('F' . $rowTotal, number_format($ttl_prod/24, 2));
                $sheet->getStyle('F' . $rowTotal)->applyFromArray($styleHeader);

                $sheet->setCellValue('G' . $rowTotal, $ttl_jlmc);
                $sheet->getStyle('G' . $rowTotal)->applyFromArray($styleHeader);

                $colTotal = 'H'; // Kolom awal tanggal produksi
                $colTotal2 = 'I'; // Kolom kedua untuk mergeCells

                // Konversi huruf kolom ke nomor indeks kolom
                $colTotal_index = Coordinate::columnIndexFromString($colTotal);
                $colTotal2_index = Coordinate::columnIndexFromString($colTotal2);
                // looping tgl produksi
                foreach ($tgl_produksi as $tgl_prod) {
                    $sheet->setCellValue($colTotal . $rowTotal, number_format($totalProdPerModel[$tgl_prod]/24, 2)); 
                    $sheet->getStyle($colTotal . $rowTotal)->applyFromArray($styleHeader);
                    
                    $sheet->setCellValue($colTotal2 . $rowTotal, $totalJlMcPerModel[$tgl_prod]); 
                    $sheet->getStyle($colTotal2 . $rowTotal)->applyFromArray($styleHeader);


                    // Tambahkan 2 pada indeks kolom
                    $colTotal_index += 2;
                    $colTotal2_index = $colTotal_index + 1; // Tambahkan 1 pada indeks kedua kolom

                    // Konversi kembali dari nomor indeks kolom ke huruf kolom
                    $colTotal = Coordinate::stringFromColumnIndex($colTotal_index);
                    $colTotal2 = Coordinate::stringFromColumnIndex($colTotal2_index);
                }
                $ttl_prod = 0;
                $ttl_jlmc = 0;
                $totalProdPerModel = array_fill_keys($tgl_produksi, 0);
                $totalJlMcPerModel = array_fill_keys($tgl_produksi, 0);
                $row++;
            }
        }
        
        // kolom total per no model

        // Set judul file dan header untuk download
        $filename = 'report.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // Tulis file excel ke output
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}