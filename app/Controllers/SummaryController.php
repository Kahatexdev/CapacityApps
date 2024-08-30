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
        $totalProd = $this->orderModel->getDataTimter($data);

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
                    'qty_produksi' => $item['qty_produksi'],
                    'max_delivery' => $item['max_delivery'],
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
        $sheet->setCellValue('A1', 'SUMMARY PRODUKSI ' . $area);
        $sheet->mergeCells('A1:J1');
        // Mengatur teks menjadi rata tengah dan huruf tebal
        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $row_header = 3;
        $row_header2 = 4;
        // Isi header
        $sheet->setCellValue('A' . $row_header, 'Tanggal');
        $sheet->mergeCells('A' . $row_header . ':J' . $row_header);
        $sheet->getStyle('A' . $row_header . ':J' . $row_header)->applyFromArray($styleHeader);

        // looping tgl produksi
        $col = 'K'; // Kolom awal tanggal produksi
        $col2 = 'L'; // Kolom kedua untuk mergeCells

        // Konversi huruf kolom ke nomor indeks kolom
        $col_index = Coordinate::columnIndexFromString($col);
        $col2_index = Coordinate::columnIndexFromString($col2);

        foreach ($tgl_produksi as $tgl_prod) {
            $sheet->setCellValue($col . $row_header, $tgl_prod);
            $sheet->mergeCells($col . $row_header . ':' . $col2 . $row_header); // Merge sel antara kolom $col dan $col2
            $sheet->getStyle($col . $row_header . ':' . $col2 . $row_header)->applyFromArray($styleHeader);


            // Tambahkan 2 pada indeks kolom
            $col_index += 2;
            $col2_index = $col_index + 1; // Tambahkan 1 pada indeks kedua kolom

            // Konversi kembali dari nomor indeks kolom ke huruf kolom
            $col = Coordinate::stringFromColumnIndex($col_index);
            $col2 = Coordinate::stringFromColumnIndex($col2_index);
        }
        $sheet->setCellValue('A' . $row_header2, 'Area');
        $sheet->setCellValue('B' . $row_header2, 'Needle');
        $sheet->setCellValue('C' . $row_header2, 'No Model');
        $sheet->setCellValue('D' . $row_header2, 'Style Size');
        $sheet->setCellValue('E' . $row_header2, 'Qty PO (dz)');
        $sheet->setCellValue('F' . $row_header2, 'Total Prod');
        $sheet->setCellValue('G' . $row_header2, 'Sisa (dz)');
        $sheet->setCellValue('H' . $row_header2, 'Rata-Rata Jl Mc');
        $sheet->setCellValue('I' . $row_header2, 'Running (days)');
        $sheet->setCellValue('J' . $row_header2, 'Day Stop');
        // style untuk header
        $sheet->getStyle('A' . $row_header2)->applyFromArray($styleHeader);
        $sheet->getStyle('B' . $row_header2)->applyFromArray($styleHeader);
        $sheet->getStyle('C' . $row_header2)->applyFromArray($styleHeader);
        $sheet->getStyle('D' . $row_header2)->applyFromArray($styleHeader);
        $sheet->getStyle('E' . $row_header2)->applyFromArray($styleHeader);
        $sheet->getStyle('F' . $row_header2)->applyFromArray($styleHeader);
        $sheet->getStyle('G' . $row_header2)->applyFromArray($styleHeader);
        $sheet->getStyle('H' . $row_header2)->applyFromArray($styleHeader);
        $sheet->getStyle('I' . $row_header2)->applyFromArray($styleHeader);
        $sheet->getStyle('J' . $row_header2)->applyFromArray($styleHeader);

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
        // 
        $sisa = 0;
        $ttl_qty = 0;
        $ttl_prod = 0;
        $ttl_jlmc = 0;
        $ttl_sisa = 0;
        $ttl_rata2 = 0;
        $totalProdPerModel = array_fill_keys($tgl_produksi, 0);
        $totalJlMcPerModel = array_fill_keys($tgl_produksi, 0);
        foreach ($uniqueData as $key => $id) {
            $today = date('Y-m-d');
            // 
            $ttl_qty += $id['qty'];
            $ttl_prod += $id['ttl_prod'];
            $ttl_jlmc += $id['ttl_jlmc'];
            // Pastikan $id['running'] tidak bernilai nol sebelum dibagi
            $rata2 = ($id['running'] != 0) ? number_format($id['ttl_jlmc'] / $id['running'], 0) : 0;
            $target_normal_socks = 14;
            $sisa = $id['qty'] - $id['qty_produksi'];
            $ttl_sisa += $sisa;
            $hitung_day_stop = ($rata2 != 0) ? ($sisa / 24) / ($rata2 * $target_normal_socks) : 0;
            $day_stop = ($id['max_delivery'] > $today && $sisa > 0 && $rata2 != 0) ? date('Y-m-d', strtotime($today . ' + ' . round($hitung_day_stop) . ' days')) : '';

            $ttl_rata2 += $rata2;

            $sheet->setCellValue('A' . $row, $id['area']);
            $sheet->setCellValue('B' . $row, $id['machinetypeid']);
            $sheet->setCellValue('C' . $row, $id['mastermodel']);
            $sheet->setCellValue('D' . $row, $id['size']);
            $sheet->setCellValue('E' . $row, number_format($id['qty'] / 24, 2));
            $sheet->setCellValue('F' . $row, number_format($id['qty_produksi'] / 24, 2));
            $sheet->setCellValue('G' . $row, number_format($sisa / 24, 2));
            $sheet->setCellValue('H' . $row, number_format($rata2, 0));
            $sheet->setCellValue('I' . $row, $id['running']);
            $sheet->setCellValue('J' . $row, $day_stop);
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
                    if (
                        $id['machinetypeid'] == $prod['machinetypeid'] && $id['mastermodel'] == $prod['mastermodel']
                        && $id['size'] == $prod['size'] && $tgl_prod2 == $prod['tgl_produksi']
                    ) {
                        $qty_produksi = $prod['qty_produksi'];
                        $jl_mc = $prod['jl_mc'];
                        break;
                    }
                }
                // Update total production and machine count per model
                $totalProdPerModel[$tgl_prod2] += $qty_produksi;
                $totalJlMcPerModel[$tgl_prod2] += $jl_mc;

                $sheet->setCellValue($col4 . $row, number_format($qty_produksi / 24, 2));
                $sheet->getStyle($col4 . $row)->applyFromArray($styleBody);
                $col4++;

                $sheet->setCellValue($col4 . $row, $jl_mc);
                $sheet->getStyle($col4 . $row)->applyFromArray($styleBody);
                $col4++;
            }
            $row++;

            $rowTotal = $row;
            if (!isset($uniqueData[$key + 1]) || (isset($uniqueData[$key + 1]) && $uniqueData[$key + 1]['mastermodel'] != $id['mastermodel'])) {
                $sheet->setCellValue('A' . $rowTotal, "TOTAL " . $id['mastermodel'] . ':');
                $sheet->mergeCells('A' . $rowTotal . ':D' . $rowTotal);
                $sheet->getStyle('A' . $rowTotal . ':D' . $rowTotal)->applyFromArray($styleSubTotal);

                $sheet->setCellValue('E' . $rowTotal, number_format($ttl_qty / 24, 2));
                $sheet->getStyle('E' . $rowTotal)->applyFromArray($styleHeader);

                $sheet->setCellValue('F' . $rowTotal, number_format($ttl_prod / 24, 2));
                $sheet->getStyle('F' . $rowTotal)->applyFromArray($styleHeader);

                $sheet->setCellValue('G' . $rowTotal, number_format($ttl_sisa / 24, 2));
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
                    $sheet->setCellValue($colTotal . $rowTotal, number_format($totalProdPerModel[$tgl_prod] / 24, 2));
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
                $ttl_qty = 0;
                $ttl_prod = 0;
                $ttl_jlmc = 0;
                $ttl_rata2 = 0;
                $ttl_sisa = 0;
                $totalProdPerModel = array_fill_keys($tgl_produksi, 0);
                $totalJlMcPerModel = array_fill_keys($tgl_produksi, 0);
                $row++;
            }
        }

        // kolom total per no model

        // Set judul file dan header untuk download
        $filename = 'SUMMARY PRODUKSI PER TANGGAL ' . $buyer . ' ' . $area . ' ' . $jarum . ' ' . $pdk . ' ' . $awal . '-' . $akhir . '.xlsx';
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
        $totalProd = $this->orderModel->getdataSummaryPertgl($data);

        if (!is_array($totalShip)) {
            echo "Error: totalShip is not an array!";
            print_r($totalShip);
            exit;
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
                    'color' => $item['color'],
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

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $styleHeader = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];

        $styleBody = [
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];

        $sheet->setCellValue('A1', 'SUMMARY PRODUKSI PER TOD');
        $sheet->mergeCells('A1:T1');
        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $row_header = 3;
        $sheet->setCellValue('A' . $row_header, 'Seam');
        $sheet->setCellValue('B' . $row_header, 'Delivery');
        $sheet->setCellValue('C' . $row_header, 'Sisa Hari');
        $sheet->setCellValue('D' . $row_header, 'Jln Order');
        $sheet->setCellValue('E' . $row_header, 'Running (days)');
        $sheet->setCellValue('F' . $row_header, 'Buyer');
        $sheet->setCellValue('G' . $row_header, 'No Order');
        $sheet->setCellValue('H' . $row_header, 'Jarum');
        $sheet->setCellValue('I' . $row_header, 'No Model');
        $sheet->setCellValue('J' . $row_header, 'Style');
        $sheet->setCellValue('K' . $row_header, 'Color');
        $sheet->setCellValue('L' . $row_header, 'Qty Shipment');
        $sheet->setCellValue('M' . $row_header, 'Total Shipment');
        $sheet->setCellValue('N' . $row_header, 'Prod (bruto)');
        $sheet->setCellValue('O' . $row_header, 'Prod (netto)');
        $sheet->setCellValue('P' . $row_header, 'Sisa Prod');
        $sheet->setCellValue('Q' . $row_header, 'Sisa Shipment');
        $sheet->setCellValue('R' . $row_header, '% Prod');
        $sheet->setCellValue('S' . $row_header, 'BS Setting');
        $sheet->setCellValue('T' . $row_header, 'Qty (+)Pck');
        $sheet->setCellValue('E' . $row_header, 'Running (days)');
        $sheet->setCellValue('F' . $row_header, 'Buyer');
        $sheet->setCellValue('G' . $row_header, 'No Order');
        $sheet->setCellValue('H' . $row_header, 'Jarum');
        $sheet->setCellValue('I' . $row_header, 'No Model');
        $sheet->setCellValue('J' . $row_header, 'Style');
        $sheet->setCellValue('K' . $row_header, 'Color');
        $sheet->setCellValue('L' . $row_header, 'Qty Shipment');
        $sheet->setCellValue('M' . $row_header, 'Total Shipment');
        $sheet->setCellValue('N' . $row_header, 'Prod (bruto)');
        $sheet->setCellValue('O' . $row_header, 'Prod (netto)');
        $sheet->setCellValue('P' . $row_header, 'Sisa Prod');
        $sheet->setCellValue('Q' . $row_header, 'Sisa Shipment');
        $sheet->setCellValue('R' . $row_header, '% Prod');
        $sheet->setCellValue('S' . $row_header, 'BS Setting');
        $sheet->setCellValue('T' . $row_header, 'Qty (+)Pck');

        $sheet->getStyle('A' . $row_header)->applyFromArray($styleHeader);
        $sheet->getStyle('B' . $row_header)->applyFromArray($styleHeader);
        $sheet->getStyle('C' . $row_header)->applyFromArray($styleHeader);
        $sheet->getStyle('D' . $row_header)->applyFromArray($styleHeader);
        $sheet->getStyle('E' . $row_header)->applyFromArray($styleHeader);
        $sheet->getStyle('F' . $row_header)->applyFromArray($styleHeader);
        $sheet->getStyle('G' . $row_header)->applyFromArray($styleHeader);
        $sheet->getStyle('H' . $row_header)->applyFromArray($styleHeader);
        $sheet->getStyle('I' . $row_header)->applyFromArray($styleHeader);
        $sheet->getStyle('J' . $row_header)->applyFromArray($styleHeader);
        $sheet->getStyle('K' . $row_header)->applyFromArray($styleHeader);
        $sheet->getStyle('L' . $row_header)->applyFromArray($styleHeader);
        $sheet->getStyle('M' . $row_header)->applyFromArray($styleHeader);
        $sheet->getStyle('N' . $row_header)->applyFromArray($styleHeader);
        $sheet->getStyle('O' . $row_header)->applyFromArray($styleHeader);
        $sheet->getStyle('P' . $row_header)->applyFromArray($styleHeader);
        $sheet->getStyle('Q' . $row_header)->applyFromArray($styleHeader);
        $sheet->getStyle('R' . $row_header)->applyFromArray($styleHeader);
        $sheet->getStyle('S' . $row_header)->applyFromArray($styleHeader);
        $sheet->getStyle('T' . $row_header)->applyFromArray($styleHeader);

        $row = 4;
        $prevSize = null;
        $sisa_ship_prev = [];

        foreach ($uniqueData as $key => $id) {
            $today = date('Y-m-d');
            $delivery_date = $id['delivery'];
            $sisa_hari = (strtotime($delivery_date) - strtotime($today)) / (60 * 60 * 24);
            $group_key = $id['machinetypeid'] . '_' . $id['mastermodel'] . '_' . $id['size'];

            if (!isset($sisa_ship_prev[$group_key])) {
                $sisa_ship_prev[$group_key] = null;
            }
            $total_ship_found = false;

            foreach ($totalShip as $ts) {
                if ($ts['mastermodel'] == $id['mastermodel'] && $ts['size'] == $id['size']) {
                    $total_ship_found = true;
                    $sheet->setCellValue('K' . $row, number_format($ts['ttl_ship'] / 24, 2));
                    $sheet->setCellValue('M' . $row, ($id['mastermodel'] . $id['size'] != $prevSize) ? number_format($ts['ttl_ship'] / 24, 2) : '');
                    break;
                }
            }
            if (!$total_ship_found) {
                $sheet->setCellValue('K' . $row, '0');
            }
            foreach ($totalProd as $pr) {
                if ($pr['mastermodel'] == $id['mastermodel'] && $pr['size'] == $id['size']) {
                    $bruto = $pr['qty_produksi'] ?? 0;
                    $bs_st = $pr['bs_prod'] ?? 0;
                    $netto = $bruto - $bs_st ?? 0;

                    //sisa per inisial
                    $sisa = $ts['ttl_ship'] - $netto ?? 0;
                    if ($sisa > 0) {
                        $sisa;
                    } else {
                        $sisa = 0;
                    }

                    // Initialize sisa_ship for the first calculation
                    if ($sisa_ship_prev[$group_key] === null) {
                        $sisa_ship = $id['qty_deliv'] - $netto;
                    } else {
                        // Calculate sisa for each shipment based on previous sisa_ship
                        if ($sisa_ship_prev[$group_key] < 0) {
                            $sisa_ship = $id['qty_deliv'] + $sisa_ship_prev[$group_key];
                        } else {
                            $sisa_ship = $id['qty_deliv'];
                        }
                    }

                    // Calculate percentage
                    $persentase = ($ts['ttl_ship'] != 0) ? ($netto / $ts['ttl_ship']) * 100 : 0;

                    // Update sisa_ship_prev for the next iteration
                    $sisa_ship_prev[$group_key] = $sisa_ship;
                    $sheet->setCellValue('D' . $row, ($id['mastermodel'] . $id['size'] != $prevSize) ? $pr['start_mc'] : '');
                    $sheet->setCellValue('N' . $row, ($id['mastermodel'] . $id['size'] != $prevSize) ? number_format($bruto / 24, 2) : '');
                    $sheet->setCellValue('O' . $row, ($id['mastermodel'] . $id['size'] != $prevSize) ? number_format($netto / 24, 2) : '');
                    $sheet->setCellValue('P' . $row, ($id['mastermodel'] . $id['size'] != $prevSize) ? $sisa > 0 ? number_format($sisa / 24, 2) : '0.00' : '');
                    $sheet->setCellValue('Q' . $row, $sisa_ship > 0 ? number_format($sisa_ship / 24, 2) : '0.00');
                    $sheet->setCellValue('R' . $row, ($id['mastermodel'] . $id['size'] != $prevSize) ? number_format($persentase, 2) . '%' : '');
                    $sheet->setCellValue('S' . $row, ($id['mastermodel'] . $id['size'] != $prevSize) ? number_format($bs_st / 24, 2) : '');
                    $sheet->setCellValue('T' . $row, ($id['mastermodel'] . $id['size'] != $prevSize) ? $pr['plus_packing'] : '');
                    break;
                }
            }

            $sheet->setCellValue('A' . $row, ($id['mastermodel'] . $id['size'] != $prevSize) ? $id['seam'] : '');
            $sheet->setCellValue('B' . $row, $id['delivery']);
            $sheet->setCellValue('C' . $row, $sisa_hari);
            $sheet->setCellValue('E' . $row, ($id['mastermodel'] . $id['size'] != $prevSize) ? $id['running'] : '');
            $sheet->setCellValue('F' . $row, ($id['mastermodel'] . $id['size'] != $prevSize) ? $id['buyer'] : '');
            $sheet->setCellValue('G' . $row, ($id['mastermodel'] . $id['size'] != $prevSize) ? $id['no_order'] : '');
            $sheet->setCellValue('H' . $row, $id['machinetypeid']);
            $sheet->setCellValue('I' . $row, $id['mastermodel']);
            $sheet->setCellValue('J' . $row, $id['size']);
            $sheet->setCellValue('K' . $row, $id['color']);
            $sheet->setCellValue('L' . $row, number_format($id['qty_deliv'] / 24, 2));

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
            $prevSize = $id['mastermodel'] . $id['size'];
        }

        $filename = 'SUMMARY PRODUKSI ' . $buyer . ' ' . $area . ' ' . $jarum . ' ' . $pdk . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
