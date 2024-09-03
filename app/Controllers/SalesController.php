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
use App\Models\CylinderModel;
// 
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\{Border, Alignment, Fill};
use PhpParser\Node\Stmt\Foreach_;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class SalesController extends BaseController
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
        $orderJalan = $this->bookingModel->getOrderJalan();
        $terimaBooking = $this->bookingModel->getBookingMasuk();
        $mcJalan = $this->jarumModel->mcJalan();
        $totalMc = $this->jarumModel->totalMc();
        $dataJarum = $this->jarumModel->getAliasJrm(); // data all jarum
        $aliasjarum = $this->request->getGet('aliasjarum'); // data filter jarum
        $dataMesin = $this->jarumModel->getAllBrand($aliasjarum); // data mesin per jarum
        $jarum = !empty($dataMesin) ? $dataMesin[0]['jarum'] : null; // get jenis jarum dari dataMesin
        $dataTarget = $this->productModel->getKonversi($jarum); // data target per jarum
        $dataMesinByJarum = $this->jarumModel->getTotalMesinCjByJarum($jarum, $aliasjarum); // data target per jarum
        // 
        $startDate = new \DateTime(); // Tanggal hari ini
        $startDate->modify('Monday this week'); // Memastikan start date dimulai dari hari Senin minggu ini
        $endDate = new \DateTime('+1 year'); // Tanggal satu tahun ke depan
        // 
        $jumlahHari = $endDate->diff($startDate)->days + 1; // Hitung jumlah hari bulan ini
        $LiburModel = new LiburModel();
        $holidays = $LiburModel->findAll();
        // 
        $currentMonth = $startDate->format('F');
        $range = ceil($jumlahHari / 7); // Pembulatan ke atas untuk rentang minggu
        $weekCount = 1; // Inisialisasi minggu
        $monthlyData = [];
        $exess = 0;
        // 
        while ($startDate <= $endDate) {
            $startOfWeek = clone $startDate;
            $startOfWeek->modify('Monday this week');
            $endOfWeek = clone $startOfWeek;
            $endOfWeek->modify('Sunday this week');
            $numberOfDays = $startOfWeek->diff($endOfWeek)->days + 1;

            if ($endOfWeek > $endDate) {
                $endOfWeek = clone $endDate;
            }

            $holidaysCount = 0;
            $weekHolidays = [];
            foreach ($holidays as $holiday) {
                $holidayDate = new \DateTime($holiday['tanggal']);
                if ($holidayDate >= $startOfWeek && $holidayDate <= $endOfWeek) {
                    $weekHolidays[] = [
                        'nama' => $holiday['nama'],
                        'tanggal' => $holidayDate->format('d-F'),
                    ];
                    $holidaysCount++;
                    $numberOfDays--;
                }
            }

            $currentMonthOfYear = $startOfWeek->format('F-Y');
            if (!isset($monthlyData[$currentMonthOfYear])) {
                $monthlyData[$currentMonthOfYear] = [
                    'monthlySummary' => [
                        'totalMaxCapacity' => 0,
                        'totalSisaBooking' => 0,
                        'totalConfirmOrder' => 0,
                        'totalSisaConfirmOrder' => 0,
                        'totalExess' => 0,
                    ],
                    'weeks' => []
                ];
            }

            $startOfWeekFormatted = $startOfWeek->format('d-m');
            $endOfWeekFormatted = $endOfWeek->format('d-m');
            $start = $startOfWeek->format('Y-m-d');
            $end = $endOfWeek->format('Y-m-d');
            $cek = [
                'aliasjarum' => $aliasjarum,
                'jarum' => $jarum,
                'start' => $start,
                'end' => $end,
            ];
            $dataBookingByJarum = $this->bookingModel->getTotalBookingByJarum($cek); // Data booking per jarum
            $dataOrderWeekByJarum = $this->ApsPerstyleModel->getTotalOrderWeek($cek); // Data order per jarum

            $ConfirmOrder = 0;
            $sisaConfirmOrder = 0;
            foreach ($dataOrderWeekByJarum as $order) {
                $ConfirmOrder = !empty($order['qty']) ? $order['qty'] / 24 : 0;
                $sisaConfirmOrder = !empty($order['sisa']) ? $order['sisa'] / 24 : 0;
            }

            $totalBooking = 0;
            $sisaBooking = 0;
            foreach ($dataBookingByJarum as $booking) {
                $totalBooking = !empty($booking['total_booking']) ? $booking['total_booking'] / 24 : 0;
                $sisaBooking = !empty($booking['sisa_booking']) ? $booking['sisa_booking'] / 24 : 0;
            }

            $jumlahMcByJrm = 0;
            $totalHari = $numberOfDays;
            $maxCapacity = 0;
            foreach ($dataMesinByJarum as $mc) {
                $jumlahMcByJrm = $mc['total'];
                foreach ($dataTarget as $target) {
                    $konversi = !empty($target['konversi']) ? $target['konversi'] : 0;
                    if ($jumlahMcByJrm > 0) {
                        $maxCapacity = ($jumlahMcByJrm * $konversi * $totalHari) / 24;
                    }
                }
            }
            // $exess = $maxCapacity - $
            // var_dump($jumlahMcByJrm);
            // exit;
            $monthlyData[$currentMonthOfYear]['weeks'][] = [
                'countWeek' => $weekCount,
                'start_date' => $startOfWeekFormatted,
                'end_date' => $endOfWeekFormatted,
                'number_of_days' => $totalHari,
                'holidays' => $weekHolidays,
                'maxCapacity' => $maxCapacity,
                'totalBooking' => $totalBooking,
                'sisaBooking' => $sisaBooking,
                'ConfirmOrder' => $ConfirmOrder,
                'sisaConfirmOrder' => $sisaConfirmOrder,
                'exess' => -$maxCapacity + $sisaBooking + $sisaConfirmOrder,
            ];

            $monthlyData[$currentMonthOfYear]['monthlySummary']['totalMaxCapacity'] += $maxCapacity;
            $monthlyData[$currentMonthOfYear]['monthlySummary']['totalSisaBooking'] += $sisaBooking;
            $monthlyData[$currentMonthOfYear]['monthlySummary']['totalConfirmOrder'] += $ConfirmOrder;
            $monthlyData[$currentMonthOfYear]['monthlySummary']['totalSisaConfirmOrder'] += $sisaConfirmOrder;
            $monthlyData[$currentMonthOfYear]['monthlySummary']['totalExess'] += -$maxCapacity + $sisaBooking + $sisaConfirmOrder;

            $startDate->modify('next week');
            $weekCount++;
        }

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
            'jalan' => $orderJalan,
            'TerimaBooking' => $terimaBooking,
            'mcJalan' => $mcJalan,
            'totalMc' => $totalMc,
            'dataJarum' => $dataJarum,
            'dataMesin' => $dataMesin,
            'dataTarget' => $dataTarget,
            'weeklyRanges' => $monthlyData,
        ];

        return view('capacity/Sales/index', $data);
    }
    // untuk menentukan next kolom di looping
    function getNextColumn($column)
    {
        $column = strtoupper($column);
        $nextColumn = '';

        // Loop untuk menambahkan kolom berikutnya
        while (strlen($column) > 0) {
            $lastChar = substr($column, -1);
            $column = substr($column, 0, -1);

            if ($lastChar === 'Z') {
                $nextColumn = 'A' . $nextColumn;
            } else {
                $nextColumn = chr(ord($lastChar) + 1) . $nextColumn;
                break;
            }
        }

        // Jika seluruh kolom terdiri dari 'Z', tambahkan 'A' di depan
        if (strlen($column) > 0) {
            $nextColumn = 'A' . $nextColumn;
        }

        return $nextColumn;
    }

    public function exportExcelByJarum($aliasjarum)
    {
        $dataMesin = $this->jarumModel->getAllBrand($aliasjarum); // data mesin per jarum
        $jarum = !empty($dataMesin) ? $dataMesin[0]['jarum'] : null; // get jenis jarum dari dataMesin
        $dataTarget = $this->productModel->getKonversi($jarum); // data target per jarum
        $dataMesinByJarum = $this->jarumModel->getTotalMesinCjByJarum($jarum, $aliasjarum); // data target per jarum
        // 
        $startDate = new \DateTime(); // Tanggal hari ini
        $startDate->modify('Monday this week'); // Memastikan start date dimulai dari hari Senin minggu ini
        $endDate = new \DateTime('+3 month'); // Tanggal satu tahun ke depan
        // 
        $jumlahHari = $endDate->diff($startDate)->days + 1; // Hitung jumlah hari bulan ini
        $LiburModel = new LiburModel();
        $holidays = $LiburModel->findAll();
        // 
        $currentMonth = $startDate->format('F');
        $range = ceil($jumlahHari / 7); // Pembulatan ke atas untuk rentang minggu
        $weekCount = 1; // Inisialisasi minggu
        $monthlyData = [];
        $exess = 0;
        // 
        while ($startDate <= $endDate) {
            $startOfWeek = clone $startDate;
            $startOfWeek->modify('Monday this week');
            $endOfWeek = clone $startOfWeek;
            $endOfWeek->modify('Sunday this week');
            $numberOfDays = $startOfWeek->diff($endOfWeek)->days + 1;

            if ($endOfWeek > $endDate) {
                $endOfWeek = clone $endDate;
            }

            $holidaysCount = 0;
            $weekHolidays = [];
            foreach ($holidays as $holiday) {
                $holidayDate = new \DateTime($holiday['tanggal']);
                if ($holidayDate >= $startOfWeek && $holidayDate <= $endOfWeek) {
                    $weekHolidays[] = [
                        'nama' => $holiday['nama'],
                        'tanggal' => $holidayDate->format('d-F'),
                    ];
                    $holidaysCount++;
                    $numberOfDays--;
                }
            }

            $currentMonthOfYear = $startOfWeek->format('F-Y');
            if (!isset($monthlyData[$currentMonthOfYear])) {
                $monthlyData[$currentMonthOfYear] = [
                    'monthlySummary' => [
                        'totalMaxCapacity' => 0,
                        'totalSisaBooking' => 0,
                        'totalConfirmOrder' => 0,
                        'totalSisaConfirmOrder' => 0,
                        'totalExess' => 0,
                    ],
                    'weeks' => []
                ];
            }

            $startOfWeekFormatted = $startOfWeek->format('d-m');
            $endOfWeekFormatted = $endOfWeek->format('d-m');
            $start = $startOfWeek->format('Y-m-d');
            $end = $endOfWeek->format('Y-m-d');
            $cek = [
                'aliasjarum' => $aliasjarum,
                'jarum' => $jarum,
                'start' => $start,
                'end' => $end,
            ];
            $dataBookingByJarum = $this->bookingModel->getTotalBookingByJarum($cek); // Data booking per jarum
            $dataOrderWeekByJarum = $this->ApsPerstyleModel->getTotalOrderWeek($cek); // Data order per jarum

            $ConfirmOrder = 0;
            $sisaConfirmOrder = 0;
            foreach ($dataOrderWeekByJarum as $order) {
                $ConfirmOrder = !empty($order['qty']) ? $order['qty'] / 24 : 0;
                $sisaConfirmOrder = !empty($order['sisa']) ? $order['sisa'] / 24 : 0;
            }

            $totalBooking = 0;
            $sisaBooking = 0;
            foreach ($dataBookingByJarum as $booking) {
                $totalBooking = !empty($booking['total_booking']) ? $booking['total_booking'] / 24 : 0;
                $sisaBooking = !empty($booking['sisa_booking']) ? $booking['sisa_booking'] / 24 : 0;
            }

            $jumlahMcByJrm = 0;
            $totalHari = $numberOfDays;
            $maxCapacity = 0;
            foreach ($dataMesinByJarum as $mc) {
                $jumlahMcByJrm = $mc['total'];
                foreach ($dataTarget as $target) {
                    $konversi = !empty($target['konversi']) ? $target['konversi'] : 0;
                    if ($jumlahMcByJrm > 0) {
                        $maxCapacity = ($jumlahMcByJrm * $konversi * $totalHari) / 24;
                    }
                }
            }
            // $exess = $maxCapacity - $
            // var_dump($jumlahMcByJrm);
            // exit;
            $monthlyData[$currentMonthOfYear]['weeks'][] = [
                'countWeek' => $weekCount,
                'start_date' => $startOfWeekFormatted,
                'end_date' => $endOfWeekFormatted,
                'number_of_days' => $totalHari,
                'holidays' => $weekHolidays,
                'maxCapacity' => $maxCapacity,
                'totalBooking' => $totalBooking,
                'sisaBooking' => $sisaBooking,
                'ConfirmOrder' => $ConfirmOrder,
                'sisaConfirmOrder' => $sisaConfirmOrder,
                'exess' => -$maxCapacity + $sisaBooking + $sisaConfirmOrder,
            ];

            $monthlyData[$currentMonthOfYear]['monthlySummary']['totalMaxCapacity'] += $maxCapacity;
            $monthlyData[$currentMonthOfYear]['monthlySummary']['totalSisaBooking'] += $sisaBooking;
            $monthlyData[$currentMonthOfYear]['monthlySummary']['totalConfirmOrder'] += $ConfirmOrder;
            $monthlyData[$currentMonthOfYear]['monthlySummary']['totalSisaConfirmOrder'] += $sisaConfirmOrder;
            $monthlyData[$currentMonthOfYear]['monthlySummary']['totalExess'] += -$maxCapacity + $sisaBooking + $sisaConfirmOrder;

            $startDate->modify('next week');
            $weekCount++;
        }

        // Buat spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // border
        $styleHeader = [
            'font' => [
                'bold' => true, // Tebalkan teks
                'size' => 14,
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
                'bold' => true, // Tebalkan teks
                'size' => 11,
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

        // Autosize kolom
        foreach (range('A', 'BC') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Judul
        $sheet->setCellValue('A1', 'SALES POSITION ' . $aliasjarum);
        $sheet->mergeCells('A1:J1');
        // Mengatur teks menjadi rata tengah dan huruf tebal
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $rowHeader = 3;
        $endMergHeader = 6;
        // Header
        $sheet->setCellValue('A' . $rowHeader, 'Machine')
            ->mergeCells('A' . $rowHeader . ':A' . $endMergHeader)
            ->getStyle('A' . $rowHeader . ':A' . $endMergHeader)
            ->applyFromArray($styleHeader);

        $sheet->setCellValue('B' . $rowHeader, 'Jumlah')
            ->mergeCells('B' . $rowHeader . ':B' . $endMergHeader)
            ->getStyle('B' . $rowHeader . ':B' . $endMergHeader)
            ->applyFromArray($styleHeader);

        $sheet->setCellValue('C' . $rowHeader, 'Running')
            ->mergeCells('C' . $rowHeader . ':C' . $endMergHeader)
            ->getStyle('C' . $rowHeader . ':C' . $endMergHeader)
            ->applyFromArray($styleHeader);

        $sheet->setCellValue('D' . $rowHeader, 'Stock Cylinder')
            ->mergeCells('D' . $rowHeader . ':D' . $endMergHeader)
            ->getStyle('D' . $rowHeader . ':D' . $endMergHeader)
            ->applyFromArray([
                'font' => [
                    'bold' => true, // Tebalkan teks
                    'size' => 14,
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER, // Alignment rata tengah
                    'vertical' => Alignment::VERTICAL_CENTER, // Alignment rata tengah
                    'wrapText' => true,
                ],
                'borders' => [
                    'outline' => [
                        'borderStyle' => Border::BORDER_THIN, // Gaya garis tipis
                        'color' => ['argb' => 'FF000000'],    // Warna garis hitam
                    ],
                ],
            ]);

        $sheet->setCellValue('E' . $rowHeader, 'CJ')
            ->mergeCells('E' . $rowHeader . ':E' . $endMergHeader)
            ->getStyle('E' . $rowHeader . ':E' . $endMergHeader)
            ->applyFromArray($styleHeader);

        $sheet->setCellValue('F' . $rowHeader, 'MJ')
            ->mergeCells('F' . $rowHeader . ':F' . $endMergHeader)
            ->getStyle('F' . $rowHeader . ':F' . $endMergHeader)
            ->applyFromArray($styleHeader);

        $column = 'G'; // Kolom awal untuk bulan
        $column2 = 'G'; // Kolom awal untuk week
        $col_index = Coordinate::columnIndexFromString($column); // Konversi huruf kolom ke nomor indeks kolom
        $col_index2 = Coordinate::columnIndexFromString($column2); // Konversi huruf kolom ke nomor indeks kolom
        foreach ($monthlyData as $month => $data) :
            // month
            $weekCount = count($data['weeks']); // Hitung jumlah minggu dalam bulan
            $startColumn = $column;
            $endCol_index = $col_index + ($weekCount * 5) - 1; // Dikurangi 1 karena kolom awal tidak terhitung
            // Konversi kembali dari nomor indeks kolom ke huruf kolom
            $endColumn = Coordinate::stringFromColumnIndex($endCol_index);
            // Merge cells untuk bulan ini sesuai dengan jumlah minggu
            $sheet->setCellValue($startColumn . $rowHeader, $month);
            if ($startColumn !== $endColumn) {
                $sheet->mergeCells($startColumn . $rowHeader . ':' . $endColumn . $rowHeader)
                    ->getStyle($startColumn . $rowHeader . ':' . $endColumn . $rowHeader)
                    ->applyFromArray($styleHeader);
            } else {
                $sheet->getStyle($startColumn . $rowHeader)
                    ->getStyle($startColumn . $rowHeader)
                    ->applyFromArray($styleHeader);
            }
            // Pindah ke kolom berikutnya setelah melakukan merge
            $col_index = $endCol_index + 1; // Update ke indeks kolom berikutnya
            $column = Coordinate::stringFromColumnIndex($col_index); // Konversi kembali ke huruf kolom

            // week
            $rowWeek = $rowHeader; // Baris awal untuk week
            $row_index = Coordinate::columnIndexFromString($rowWeek); // Konversi huruf kolom ke nomor indeks kolom
            foreach ($data['weeks'] as $week) :
                $startDate = $week['start_date'];
                $endDate = $week['end_date'];
                $countWeek = $week['countWeek'];
                $tgl = $startDate . '-' . $endDate . ' (' . $countWeek . ')';
                $rowWeek++;
                $endRow_index = $rowWeek + 4;
                // Konversi kembali dari nomor indeks kolom ke huruf kolom
                $endRow = Coordinate::stringFromColumnIndex($endRow_index);
                //   
                $sheet->setCellValue($column2 . $rowWeek, $tgl)
                    ->mergeCells($startColumn . $rowWeek . ':' . $endRow . $rowHeader)
                    ->getStyle($column2 . $rowWeek)
                    ->applyFromArray($styleHeader2);
                // 
                $rowWeek++;
                // 
                $sheet->setCellValue($column2 . $rowHeader + 2, $week['number_of_days'] . ' hari')
                    ->getStyle($column2 . $rowHeader + 2)
                    ->applyFromArray($styleHeader2);

                $col_index2 = $col_index2 + 1; // Update ke indeks kolom berikutnya
                $column2 = Coordinate::stringFromColumnIndex($col_index2); // Konversi kembali ke huruf kolom
            endforeach;
        endforeach;

        // Body
        $total_mc = 0;
        $total_running = 0;
        $total_cj = 0;
        $total_mj = 0;
        $totalProd28 = 0;
        $rowBody = 7;
        // 
        foreach ($dataMesin as $key => $id) {
            // Hitung total untuk setiap kolom
            $total_mc += $id['total_mc'];
            $total_running += $id['mesin_jalan'];
            $total_cj += $id['cj'];
            $total_mj += $id['mj'];
            // 
            $sheet->setCellValue('A' . $rowBody, $id['brand']);
            $sheet->setCellValue('B' . $rowBody, $id['total_mc']);
            $sheet->setCellValue('C' . $rowBody, $id['mesin_jalan']);
            $sheet->setCellValue('D' . $rowBody, '0');
            $sheet->setCellValue('E' . $rowBody, $id['cj']);
            $sheet->setCellValue('F' . $rowBody, $id['mj']);
            // style untuk body
            $sheet->getStyle('A' . $rowBody)->applyFromArray([
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT, // Alignment rata tengah
                ],
                'borders' => [
                    'outline' => [
                        'borderStyle' => Border::BORDER_THIN, // Gaya garis tipis
                        'color' => ['argb' => 'FF000000'],    // Warna garis hitam
                    ],
                ],
            ]);
            $sheet->getStyle('B' . $rowBody)->applyFromArray($styleBody);
            $sheet->getStyle('C' . $rowBody)->applyFromArray($styleBody);
            $sheet->getStyle('D' . $rowBody)->applyFromArray($styleBody);
            $sheet->getStyle('E' . $rowBody)->applyFromArray($styleBody);
            $sheet->getStyle('F' . $rowBody)->applyFromArray($styleBody);
            // $sheet->getStyle('G' . $rowBody)->applyFromArray($styleBody);
            $rowBody + 4;
        }

        // Set judul file dan header untuk download
        $filename = 'Sales Position ' . $aliasjarum . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // Tulis file excel ke output
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function generateExcel()
    {
        $dataJarum = $this->jarumModel->getAliasJrm();
        // foreach ($dataJarum as $key => $jrm) {
        //     $gloves = $jrm['aliasjarum'] == "Baby 10G (84N)" or $jrm['aliasjarum'] == " Baby 10G (92N)" or $jrm['aliasjarum'] == "Child/Ladies 10G (106N)";
        //     if ($gloves) {
        //     }
        //     dd($dataJarum);
        // }
        // Buat spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // border
        $styleHeader = [
            'font' => [
                'bold' => true, // Tebalkan teks
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
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
        $sheet->setCellValue('A1', 'SALES POSITION');
        $sheet->mergeCells('A1:J1');
        // Mengatur teks menjadi rata tengah dan huruf tebal
        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $rowHeader = 3;
        // Header
        $sheet->setCellValue('A' . $rowHeader, 'KIND OF MACHINE')
            ->mergeCells('A' . $rowHeader . ':A5')
            ->getStyle('A' . $rowHeader . ':A5')
            ->applyFromArray($styleHeader);

        // Body
        $rowBody = 6;
        foreach ($dataJarum as $key => $id) {
            $sheet->setCellValue('A' . $rowBody, $id['aliasjarum']);
            // style untuk body
            $sheet->getStyle('A' . $rowBody)->applyFromArray([
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_RIGHT, // Alignment rata tengah
                ],
                'borders' => [
                    'outline' => [
                        'borderStyle' => Border::BORDER_THIN, // Gaya garis tipis
                        'color' => ['argb' => 'FF000000'],    // Warna garis hitam
                    ],
                ],
            ]);
            $rowBody++;
        }

        // Set judul file dan header untuk download
        $filename = 'Sales Position.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // Tulis file excel ke output
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
