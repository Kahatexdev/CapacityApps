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
                    'area' => $item['area'],
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
        $sheet->mergeCells('A1:J1');
        // Mengatur teks menjadi rata tengah dan huruf tebal
        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $row_header = 3;
        $row_header2 = 4;
        // Isi header
        $sheet->setCellValue('A' . $row_header , 'Tanggal');
        $sheet->mergeCells('A'.$row_header.':J'.$row_header);
        $sheet->getStyle('A'.$row_header.':J'.$row_header)->applyFromArray($styleHeader);

        // looping tgl produksi
        $col = 'K'; // Kolom awal tanggal produksi
        $col2 = 'L'; // Kolom kedua untuk mergeCells

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
        $sheet->setCellValue('A'.$row_header2, 'Area');
        $sheet->setCellValue('B'.$row_header2, 'Needle');
        $sheet->setCellValue('C'.$row_header2, 'No Model');
        $sheet->setCellValue('D'.$row_header2, 'Style Size');
        $sheet->setCellValue('E'.$row_header2, 'Qty PO (dz)');
        $sheet->setCellValue('F'.$row_header2, 'Total Prod');
        $sheet->setCellValue('G'.$row_header2, 'Sisa (dz)');
        $sheet->setCellValue('H'.$row_header2, 'Rata-Rata Jl Mc');
        $sheet->setCellValue('I'.$row_header2, 'Running');
        $sheet->setCellValue('J'.$row_header2, 'Day Stop');
        // style untuk header
        $sheet->getStyle('A'.$row_header2)->applyFromArray($styleHeader);
        $sheet->getStyle('B'.$row_header2)->applyFromArray($styleHeader);
        $sheet->getStyle('C'.$row_header2)->applyFromArray($styleHeader);
        $sheet->getStyle('D'.$row_header2)->applyFromArray($styleHeader);
        $sheet->getStyle('E'.$row_header2)->applyFromArray($styleHeader);
        $sheet->getStyle('F'.$row_header2)->applyFromArray($styleHeader);
        $sheet->getStyle('G'.$row_header2)->applyFromArray($styleHeader);
        $sheet->getStyle('H'.$row_header2)->applyFromArray($styleHeader);
        $sheet->getStyle('I'.$row_header2)->applyFromArray($styleHeader);
        $sheet->getStyle('J'.$row_header2)->applyFromArray($styleHeader);

        // Tambahkan header dinamis untuk tanggal produksi
        $col3 = 'K';
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
        $ttl_qty = 0;
        $ttl_prod = 0;
        $ttl_jlmc = 0;
        $ttl_sisa = 0;
        $ttl_rata2 = 0;
        $totalProdPerModel = array_fill_keys($tgl_produksi, 0);
        $totalJlMcPerModel = array_fill_keys($tgl_produksi, 0);
        foreach ($uniqueData as $key => $id) {
            $ttl_qty += $id['qty'];
            $ttl_prod += $id['ttl_prod'];
            $ttl_jlmc += $id['ttl_jlmc'];
            // Pastikan $id['running'] tidak bernilai nol sebelum dibagi
            $rata2 = ($id['running'] != 0) ? $id['ttl_jlmc'] / $id['running'] : 0;
            // Pastikan $id['ttl_prod'] tidak bernilai nol sebelum dibagi
            $sisa = ($id['ttl_prod'] != 0) ? $id['qty'] / $id['ttl_prod'] : 0;
            $target_normal_socks = 14;
            $hitung_day_stop = ($rata2 != 0) ? $sisa / ($rata2 * $target_normal_socks) : 0;
            $today = date('Y-m-d');

            $ttl_sisa += $sisa;
            $ttl_rata2 += $rata2;

            $sheet->setCellValue('A' . $row, $id['area']);
            $sheet->setCellValue('B' . $row, $id['machinetypeid']);
            $sheet->setCellValue('C' . $row, $id['mastermodel']);
            $sheet->setCellValue('D' . $row, $id['size']);
            $sheet->setCellValue('E' . $row, number_format($id['qty']/24, 2));
            $sheet->setCellValue('F' . $row, number_format($id['ttl_prod']/24, 2));
            $sheet->setCellValue('G' . $row, number_format($sisa, 2));
            $sheet->setCellValue('H' . $row, number_format($rata2, 1));
            $sheet->setCellValue('I' . $row, $id['running'] . ' days');
            $sheet->setCellValue('J' . $row, date('Y-m-d', strtotime($today . ' + ' . round($hitung_day_stop) . ' days')));
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

            // looping kolom qty produksi & jl mc pertanggal
            $col4 = "K";
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
                $sheet->mergeCells('A' . $rowTotal . ':D' . $rowTotal);
                $sheet->getStyle('A' . $rowTotal . ':D' . $rowTotal)->applyFromArray($styleSubTotal);

                $sheet->setCellValue('E' . $rowTotal, number_format($ttl_qty/24, 2));
                $sheet->getStyle('E' . $rowTotal)->applyFromArray($styleHeader);

                $sheet->setCellValue('F' . $rowTotal, number_format($ttl_prod/24, 2));
                $sheet->getStyle('F' . $rowTotal)->applyFromArray($styleHeader);

                $sheet->setCellValue('G' . $rowTotal, number_format($ttl_sisa, 2));
                $sheet->getStyle('G' . $rowTotal)->applyFromArray($styleHeader);

                $sheet->setCellValue('H' . $rowTotal, number_format($ttl_rata2, 0));
                $sheet->getStyle('H' . $rowTotal)->applyFromArray($styleHeader);

                $sheet->setCellValue('I' . $rowTotal, '');
                $sheet->getStyle('I' . $rowTotal)->applyFromArray($styleHeader);

                $sheet->setCellValue('J' . $rowTotal, '');
                $sheet->getStyle('J' . $rowTotal)->applyFromArray($styleHeader);

                $colTotal = 'K'; // Kolom awal tanggal produksi
                $colTotal2 = 'L'; // Kolom kedua untuk mergeCells

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
        $filename = 'SUMMARY PRODUKSI PER TANGGAL.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // Tulis file excel ke output
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function excelSummaryPerTod()
    {
        $buyer = $this->request->getPost('buyer');
        $area = $this->request->getPost('area');
        $jarum = $this->request->getPost('jarum');
        $pdk = $this->request->getPost('pdk');

        $data = [
            'buyer' => $buyer,
            'area' => $area,
            'jarum' => $jarum,
            'pdk' => $pdk,
        ];

        $dataSummary = $this->orderModel->getProdSummary($data);
        $totalShip = $this->orderModel->getTotalShipment($data);
        
        // Debugging to check if $totalShip is an array
        if (!is_array($totalShip)) {
            echo "Error: totalShip is not an array!";
            print_r($totalShip);
            exit; // Stop execution to debug
        }

        $uniqueData = [];
        foreach ($dataSummary as $item) {
            $key = $item['machinetypeid'] . '-' . $item['mastermodel'] . '-' . $item['size'] . '-' . $item['delivery'];
            if (!isset($uniqueData[$key])) {
                $uniqueData[$key] = [
                    'seam' => $item['seam'],
                    'buyer' => $item['kd_buyer_order'],
                    'no_order' => $item['no_order'],
                    'machinetypeid' => $item['machinetypeid'],
                    'mastermodel' => $item['mastermodel'],
                    'size' => $item['size'],
                    'delivery' => $item['delivery'],
                    'qty_deliv' => 0,
                    'running' => 0,
                    'bruto' => 0,
                    'ttl_jlmc' => 0,
                ];
            }
            $uniqueData[$key]['qty_deliv'] += $item['qty_deliv'];
            $uniqueData[$key]['running'] += $item['running'];
            $uniqueData[$key]['bruto'] += $item['bruto'];
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

        // Judul
        $sheet->setCellValue('A1', 'SUMMARY PRODUKSI PER TOD');
        $sheet->mergeCells('A1:O1');
        // Mengatur teks menjadi rata tengah dan huruf tebal
        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $row_header = 3;
        // Isi header
        $sheet->setCellValue('A'.$row_header, 'Seam');
        $sheet->setCellValue('B'.$row_header, 'Delivery');
        $sheet->setCellValue('C'.$row_header, 'Sisa Hari');
        $sheet->setCellValue('D'.$row_header, 'Jln Order');
        $sheet->setCellValue('E'.$row_header, 'Buyer');
        $sheet->setCellValue('F'.$row_header, 'No Order');
        $sheet->setCellValue('G'.$row_header, 'Jarum');
        $sheet->setCellValue('H'.$row_header, 'No Model');
        $sheet->setCellValue('I'.$row_header, 'Style');
        $sheet->setCellValue('J'.$row_header, 'Qty Shipment');
        $sheet->setCellValue('K'.$row_header, 'Total Shipment');
        $sheet->setCellValue('L'.$row_header, 'Prod (bruto)');
        $sheet->setCellValue('M'.$row_header, 'Prod (netto)');
        $sheet->setCellValue('N'.$row_header, 'Sisa Prod');
        $sheet->setCellValue('O'.$row_header, 'Sisa Shipment');
        $sheet->setCellValue('P'.$row_header, '% Prod');
        $sheet->setCellValue('Q'.$row_header, 'BS Setting');
        $sheet->setCellValue('R'.$row_header, 'Running');
        // style untuk header
        $sheet->getStyle('A'.$row_header)->applyFromArray($styleHeader);
        $sheet->getStyle('B'.$row_header)->applyFromArray($styleHeader);
        $sheet->getStyle('C'.$row_header)->applyFromArray($styleHeader);
        $sheet->getStyle('D'.$row_header)->applyFromArray($styleHeader);
        $sheet->getStyle('E'.$row_header)->applyFromArray($styleHeader);
        $sheet->getStyle('F'.$row_header)->applyFromArray($styleHeader);
        $sheet->getStyle('G'.$row_header)->applyFromArray($styleHeader);
        $sheet->getStyle('H'.$row_header)->applyFromArray($styleHeader);
        $sheet->getStyle('I'.$row_header)->applyFromArray($styleHeader);
        $sheet->getStyle('J'.$row_header)->applyFromArray($styleHeader);
        $sheet->getStyle('K'.$row_header)->applyFromArray($styleHeader);
        $sheet->getStyle('L'.$row_header)->applyFromArray($styleHeader);
        $sheet->getStyle('M'.$row_header)->applyFromArray($styleHeader);
        $sheet->getStyle('N'.$row_header)->applyFromArray($styleHeader);
        $sheet->getStyle('O'.$row_header)->applyFromArray($styleHeader);
        $sheet->getStyle('P'.$row_header)->applyFromArray($styleHeader);
        $sheet->getStyle('Q'.$row_header)->applyFromArray($styleHeader);
        $sheet->getStyle('R'.$row_header)->applyFromArray($styleHeader);

        // Isi data
        $row = 4;
        $prevSize = null;
        foreach ($uniqueData as $key => $id) {
            $today = date('Y-m-d'); // Get current date in yyyy-mm-dd format
            $delivery_date = $id['delivery']; // Assuming $id['delivery'] is also in yyyy-mm-dd format
    
            // Calculate remaining days
            $sisa_hari = (strtotime($delivery_date) - strtotime($today)) / (60 * 60 * 24);

            if ($id['size'] != $prevSize) {
                $sheet->setCellValue('A' . $row, $id['seam']);
                $sheet->setCellValue('E' . $row, $id['buyer']);
                $sheet->setCellValue('F' . $row, $id['no_order']);
                $total_ship_found = false;
                foreach ($totalShip as $ts) {
                    if ($ts['mastermodel'] == $id['mastermodel'] && $ts['size'] == $id['size']) {
                        $total_ship_found = true;
                        $sheet->setCellValue('K' . $row, number_format($ts['ttl_ship']/24, 2));
                        break;
                    }
                }
                if (!$total_ship_found) {
                    $sheet->setCellValue('K' . $row, '0');
                }
                $sheet->setCellValue('L' . $row, number_format($id['bruto']/24, 2));
                $prevSize = $id['size'];
            } else {
                $sheet->setCellValue('A' . $row, '');
                $sheet->setCellValue('E' . $row, '');
                $sheet->setCellValue('F' . $row, '');
                $sheet->setCellValue('K' . $row, '');
                $sheet->setCellValue('L' . $row, '');
            }
            $sheet->setCellValue('B' . $row, $id['delivery']);
            $sheet->setCellValue('C' . $row, $sisa_hari .' days');
            $sheet->setCellValue('D' . $row, $id['running'].' days');
            $sheet->setCellValue('G' . $row, $id['machinetypeid']);
            $sheet->setCellValue('H' . $row, $id['mastermodel']);
            $sheet->setCellValue('I' . $row, $id['size']);
            $sheet->setCellValue('J' . $row, number_format($id['qty_deliv']/24, 2));
            $sheet->setCellValue('M' . $row, '');
            $sheet->setCellValue('N' . $row, '');
            $sheet->setCellValue('O' . $row, '');
            $sheet->setCellValue('P' . $row, '');
            $sheet->setCellValue('Q' . $row, '');
            $sheet->setCellValue('R' . $row, $id['ttl_jlmc'].' mc');

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

            $row++;
        }

        // Set judul file dan header untuk download
        $filename = 'SUMMARY PRODUKSI.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // Tulis file excel ke output
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

}