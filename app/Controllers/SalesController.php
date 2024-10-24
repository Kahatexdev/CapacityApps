<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Database\Migrations\TargetExport;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\DataMesinModel;
use App\Models\OrderModel;
use App\Models\BookingModel;
use App\Models\ProductTypeModel;
use App\Models\ApsPerstyleModel;
use App\Models\ProduksiModel;
use App\Models\LiburModel;
use App\Models\CylinderModel;
use App\Models\TargetExportModel;
// 
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\{Border, Alignment, Fill};
use PhpParser\Node\Stmt\Foreach_;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpParser\Node\Stmt\Echo_;

class SalesController extends BaseController
{
    protected $filters;
    protected $jarumModel;
    protected $cylinderModel;
    protected $productModel;
    protected $produksiModel;
    protected $bookingModel;
    protected $orderModel;
    protected $ApsPerstyleModel;
    protected $liburModel;
    protected $targetExportModel;

    public function __construct()
    {
        $this->jarumModel = new DataMesinModel();
        $this->cylinderModel = new cylinderModel();
        $this->bookingModel = new BookingModel();
        $this->productModel = new ProductTypeModel();
        $this->produksiModel = new ProduksiModel();
        $this->orderModel = new OrderModel();
        $this->ApsPerstyleModel = new ApsPerstyleModel();
        $this->liburModel = new LiburModel();
        $this->targetExportModel = new TargetExportModel();

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
        $dataJarum = $this->jarumModel->getAliasJrm(); // data all jarum
        $aliasjarum = $this->request->getPost('aliasjarum') ?? '';

        $brands = ['Dakong', 'Rosso', 'Mechanic', 'Lonati'];
        $brandData = [];
        $totals = [
            'totalMc' => 0,
            'totalRunning' => 0,
            'totalCj' => 0,
            'totalMj' => 0,
            'totalMj' => 0,
            'totalProd' => 0 // Untuk menyimpan total $prod
        ];

        // Ambil data kapasitas per brand dan hitung total
        foreach ($brands as $brand) {
            $data = $this->jarumModel->getTotalMcPerBrand($brand, $aliasjarum);

            // Inisialisasi total per brand
            $brandTotals = [
                'totalMc' => 0,
                'totalRunning' => 0,
                'totalCj' => 0,
                'totalMj' => 0,
                'target' => 0,
                'totalProd' => 0
            ];

            // Hitung total untuk setiap brand
            foreach ($data as $item) {
                // Pastikan $item adalah array dan memiliki kunci yang dibutuhkan
                if (is_array($item)) {
                    $prod = $item['running'] * $item['target'] * 28;

                    // Tambahkan hasil perhitungan ke total per brand
                    $brandTotals['totalMc'] += $item['total_mc'] ?? 0;
                    $brandTotals['totalRunning'] += $item['running'] ?? 0;
                    $brandTotals['totalCj'] += $item['cj'] ?? 0;
                    // $brandTotals['totalMj'] += $item['mj'] ?? 0;
                    $brandTotals['target'] = $item['target'] ?? 0;
                    $brandTotals['totalProd'] += ceil($prod);
                }
            }

            // Simpan total per brand ke dalam $brandData
            $brandData[$brand] = $brandTotals;

            // Tambahkan ke total keseluruhan
            $totals['totalMc'] += $brandTotals['totalMc'];
            $totals['totalRunning'] += $brandTotals['totalRunning'];
            $totals['totalCj'] += $brandTotals['totalCj'];
            // $totals['totalMj'] += $brandTotals['totalMj'];
            $totals['totalProd'] += $brandTotals['totalProd'];
        }

        // // Ambil total kapasitas dan jenis jarum per alias
        // $dataTtlMc = $this->jarumModel->getTotalMcPerAliasJrm($aliasjarum);
        $jarum = $this->jarumModel->getJrmByAliasjrm($aliasjarum); // get jenis jarum per aliasjarum

        $startDate = new \DateTime(); // Tanggal hari ini
        // $startDate->modify('Monday this week'); // Memastikan start date dimulai dari hari Senin minggu ini
        $endDate = new \DateTime('+1 years'); // Tanggal satu tahun ke depan

        $LiburModel = new LiburModel();
        $holidays = $LiburModel->findAll();
        // 
        $weekCount = 1; // Inisialisasi minggu
        $monthlyData = [];

        // $exess =  $exessPercentage = $totalMonthlyCapacity = $totalMonthlyAvailable = $totalMonthlyMachine = 0;
        while ($startDate <= $endDate) {
            $endOfWeek = (clone $startDate)->modify('Sunday this week');

            // Tentukan akhir bulan dari tanggal awal saat ini
            $endOfMonth = new \DateTime($startDate->format('Y-m-t')); // Akhir bulan saat ini

            // Jika akhir minggu melebihi akhir bulan, batasi hingga akhir bulan
            if ($endOfWeek > $endOfMonth) {
                $endOfWeek = clone $endOfMonth; // Akhiri minggu di akhir bulan
            }

            $numberOfDays = $startDate->diff($endOfWeek)->days + 1; //hitung jumlah hari week ini

            // Hitung libur minggu ini
            $weekHolidays = array_filter($holidays, function ($holiday) use ($startDate, $endOfWeek) {
                $holidayDate = new \DateTime($holiday['tanggal']);
                return $holidayDate >= $startDate && $holidayDate <= $endOfWeek;
            });

            $holidaysCount = count($weekHolidays);
            $numberOfDays -= $holidaysCount;

            $currentMonthOfYear = $startDate->format('F-Y');
            if (!isset($monthlyData[$currentMonthOfYear])) {
                $monthlyData[$currentMonthOfYear] = [
                    'monthlySummary' => [
                        'totalMaxCapacity' => 0,
                        'totalSisaBooking' => 0,
                        'totalConfirmOrder' => 0,
                        'totalSisaConfirmOrder' => 0,
                        'totalExess' => 0,
                        'totalExessPercentage' => 0,
                    ],
                    'weeks' => []
                ];
            }

            // Ambil data booking dan order per minggu
            $cek = [
                'jarum' => $jarum,
                'start' => $startDate->format('Y-m-d'),
                'end' => $endOfWeek->format('Y-m-d'),
            ];

            $dataBookingByJarum = $this->bookingModel->getTotalBookingByJarum($cek); // Data booking per jarum
            $dataOrderWeekByJarum = $this->ApsPerstyleModel->getTotalOrderWeek($cek); // Data order per jarum

            $ConfirmOrder = array_reduce($dataOrderWeekByJarum, function ($carry, $order) {
                $qty = !empty($order['qty']) ? ceil($order['qty'] / 24) : 0;
                $carry += $qty;
                return $carry;
            }, 0);

            $sisaConfirmOrder = array_reduce($dataOrderWeekByJarum, function ($carry, $order) {
                $sisa = !empty($order['sisa']) ? ceil($order['sisa'] / 24) : 0;
                $carry += $sisa;
                return $carry;
            }, 0);

            $totalBooking = array_reduce($dataBookingByJarum, function ($carry, $booking) {
                $total = !empty($booking['total_booking']) ? ceil($booking['total_booking'] / 24) : 0;
                $carry += $total;
                return $carry;
            }, 0);

            $sisaBooking = array_reduce($dataBookingByJarum, function ($carry, $booking) {
                $sisa = !empty($booking['sisa_booking']) ? ceil($booking['sisa_booking'] / 24) : 0;
                $carry += $sisa;
                return $carry;
            }, 0);

            $maxCapacity = array_reduce($brands, function ($carry, $brand) use ($brandData, $numberOfDays) {
                $brandCapacity = 0;
                // Periksa apakah $brandData[$brand] adalah array
                if (isset($brandData[$brand]) && is_array($brandData[$brand])) {
                    // Pastikan $brandData[$brand] ada dan berisi array
                    if (isset($brandData[$brand])) {
                        $jumlahMc = $brandData[$brand]['totalCj'] ?? 0;
                        $target = $brandData[$brand]['target'] ?? 0; // Pastikan target juga ada di $brandData
                        if ($jumlahMc > 0) {
                            $brandCapacity = ceil($jumlahMc * $target * $numberOfDays);
                        }
                    }
                }
                $carry += $brandCapacity;
                return $carry;
            }, 0);

            $countExess = -$maxCapacity + $sisaBooking + $sisaConfirmOrder;
            $exess = ($countExess > 0) ? ceil($countExess) : floor($countExess);

            $countExessPercentage = ($maxCapacity > 0) ? ($exess / $maxCapacity) * 100 : 0;
            $exessPercentage = ($countExessPercentage > 0) ? ceil($countExessPercentage) : floor($countExessPercentage);

            $monthlyData[$currentMonthOfYear]['weeks'][] = [
                'countWeek' => $weekCount,
                'start_date' => $startDate->format('d-m'),
                'end_date' => $endOfWeek->format('d-m'),
                'number_of_days' => $numberOfDays,
                'holidays' => array_map(function ($holiday) {
                    return [
                        'nama' => $holiday['nama'],
                        'tanggal' => (new \DateTime($holiday['tanggal']))->format('d-F'),
                    ];
                }, $weekHolidays),
                'maxCapacity' => $maxCapacity,
                'totalBooking' => $totalBooking,
                'sisaBooking' => $sisaBooking,
                'ConfirmOrder' => $ConfirmOrder,
                'sisaConfirmOrder' => $sisaConfirmOrder,
                'exess' => $exess,
                'exessPercentage' => $exessPercentage,
            ];

            // Update data bulanan
            $monthlyData[$currentMonthOfYear]['monthlySummary']['totalMaxCapacity'] += $maxCapacity;
            $monthlyData[$currentMonthOfYear]['monthlySummary']['totalSisaBooking'] += $sisaBooking;
            $monthlyData[$currentMonthOfYear]['monthlySummary']['totalConfirmOrder'] += $ConfirmOrder;
            $monthlyData[$currentMonthOfYear]['monthlySummary']['totalSisaConfirmOrder'] += $sisaConfirmOrder;
            $monthlyData[$currentMonthOfYear]['monthlySummary']['totalExess'] += $exess;

            // Ambil total max kapasitas dan exess setelah penjumlahan
            $totalMaxCapacity = $monthlyData[$currentMonthOfYear]['monthlySummary']['totalMaxCapacity'];
            $totalExess = $monthlyData[$currentMonthOfYear]['monthlySummary']['totalExess'];
            // Hitung persentase setelah penjumlahan
            $exessPercentage = ($totalMaxCapacity > 0) ? (($totalExess / $totalMaxCapacity) * 100) : 0;
            $monthlyData[$currentMonthOfYear]['monthlySummary']['totalExessPercentage'] = $exessPercentage;

            // Perbarui tanggal awal untuk minggu berikutnya
            if ($endOfWeek == $endOfMonth) {
                // Mulai dari hari berikutnya setelah akhir bulan
                $startDate = (clone $endOfMonth)->modify('+1 day');
            } else {
                // Lanjutkan dari hari berikutnya setelah akhir minggu
                $startDate = (clone $endOfWeek)->modify('+1 day');
            }

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
            'dataJarum' => $dataJarum,
            'aliasjarum' => $aliasjarum,
            'dakong' => $brandData['Dakong']  ?? [],
            'rosso' => $brandData['Rosso'] ?? [],
            'ths' => $brandData['Mechanic'] ?? [],
            'lonati' => $brandData['Lonati'] ?? [],
            'ttlMc' => $totals,
            'weeklyRanges' => $monthlyData,
        ];

        return view('capacity/Sales/index', $data);
    }

    public function exportExcelByJarum()
    {
        $aliasjarum = $this->request->getPost('aliasjarum') ?? '';

        $brands = ['Dakong', 'Rosso', 'Mechanic', 'Lonati'];
        $brandData = [];
        $totals = [
            'totalMc' => 0,
            'totalRunning' => 0,
            'totalCj' => 0,
            'totalMj' => 0,
            'totalMj' => 0,
            'totalProd' => 0 // Untuk menyimpan total $prod
        ];

        // Ambil data kapasitas per brand dan hitung total
        foreach ($brands as $brand) {
            $data = $this->jarumModel->getTotalMcPerBrand($brand, $aliasjarum);

            // Inisialisasi total per brand
            $brandTotals = [
                'totalMc' => 0,
                'totalRunning' => 0,
                'totalCj' => 0,
                'totalMj' => 0,
                'target' => 0,
                'totalProd' => 0
            ];

            // Hitung total untuk setiap brand
            foreach ($data as $item) {
                // Pastikan $item adalah array dan memiliki kunci yang dibutuhkan
                if (is_array($item)) {
                    $prod = $item['running'] * $item['target'] * 28;

                    // Tambahkan hasil perhitungan ke total per brand
                    $brandTotals['totalMc'] += $item['total_mc'] ?? 0;
                    $brandTotals['totalRunning'] += $item['running'] ?? 0;
                    $brandTotals['totalCj'] += $item['cj'] ?? 0;
                    $brandTotals['totalMj'] += $item['mj'] ?? 0;
                    $brandTotals['target'] = $item['target'] ?? 0;
                    $brandTotals['totalProd'] += ceil($prod);
                }
            }

            // Simpan total per brand ke dalam $brandData
            $brandData[$brand] = $brandTotals;

            // Tambahkan ke total keseluruhan
            $totals['totalMc'] += $brandTotals['totalMc'];
            $totals['totalRunning'] += $brandTotals['totalRunning'];
            $totals['totalCj'] += $brandTotals['totalCj'];
            // $totals['totalMj'] += $brandTotals['totalMj'];
            $totals['totalProd'] += $brandTotals['totalProd'];
        }

        // // Ambil total kapasitas dan jenis jarum per alias
        // $dataTtlMc = $this->jarumModel->getTotalMcPerAliasJrm($aliasjarum);
        $jarum = $this->jarumModel->getJrmByAliasjrm($aliasjarum); // get jenis jarum per aliasjarum

        $startDate = new \DateTime(); // Tanggal hari ini
        // $startDate->modify('Monday this week'); // Memastikan start date dimulai dari hari Senin minggu ini
        $endDate = new \DateTime('+1 years'); // Tanggal satu tahun ke depan

        $LiburModel = new LiburModel();
        $holidays = $LiburModel->findAll();
        // 
        $weekCount = 1; // Inisialisasi minggu
        $monthlyData = [];

        // $exess =  $exessPercentage = $totalMonthlyCapacity = $totalMonthlyAvailable = $totalMonthlyMachine = 0;
        while ($startDate <= $endDate) {
            $endOfWeek = (clone $startDate)->modify('Sunday this week');

            // Tentukan akhir bulan dari tanggal awal saat ini
            $endOfMonth = new \DateTime($startDate->format('Y-m-t')); // Akhir bulan saat ini

            // Jika akhir minggu melebihi akhir bulan, batasi hingga akhir bulan
            if ($endOfWeek > $endOfMonth) {
                $endOfWeek = clone $endOfMonth; // Akhiri minggu di akhir bulan
            }

            $numberOfDays = $startDate->diff($endOfWeek)->days + 1; //hitung jumlah hari week ini

            // Hitung libur minggu ini
            $weekHolidays = array_filter($holidays, function ($holiday) use ($startDate, $endOfWeek) {
                $holidayDate = new \DateTime($holiday['tanggal']);
                return $holidayDate >= $startDate && $holidayDate <= $endOfWeek;
            });

            $holidaysCount = count($weekHolidays);
            $numberOfDays -= $holidaysCount;

            $currentMonthOfYear = $startDate->format('F-Y');
            if (!isset($monthlyData[$currentMonthOfYear])) {
                $monthlyData[$currentMonthOfYear] = [
                    'monthlySummary' => [
                        'totalMaxCapacity' => 0,
                        'totalSisaBooking' => 0,
                        'totalConfirmOrder' => 0,
                        'totalSisaConfirmOrder' => 0,
                        'totalExess' => 0,
                        'totalExessPercentage' => 0
                    ],
                    'weeks' => []
                ];
            }

            // Ambil data booking dan order per minggu
            $cek = [
                'jarum' => $jarum,
                'start' => $startDate->format('Y-m-d'),
                'end' => $endOfWeek->format('Y-m-d'),
            ];

            $dataBookingByJarum = $this->bookingModel->getTotalBookingByJarum($cek); // Data booking per jarum
            $dataOrderWeekByJarum = $this->ApsPerstyleModel->getTotalOrderWeek($cek); // Data order per jarum

            $ConfirmOrder = array_reduce($dataOrderWeekByJarum, function ($carry, $order) {
                $qty = !empty($order['qty']) ? ceil($order['qty'] / 24) : 0;
                $carry += $qty;
                return $carry;
            }, 0);

            $sisaConfirmOrder = array_reduce($dataOrderWeekByJarum, function ($carry, $order) {
                $sisa = !empty($order['sisa']) ? ceil($order['sisa'] / 24) : 0;
                $carry += $sisa;
                return $carry;
            }, 0);

            $totalBooking = array_reduce($dataBookingByJarum, function ($carry, $booking) {
                $total = !empty($booking['total_booking']) ? ceil($booking['total_booking'] / 24) : 0;
                $carry += $total;
                return $carry;
            }, 0);

            $sisaBooking = array_reduce($dataBookingByJarum, function ($carry, $booking) {
                $sisa = !empty($booking['sisa_booking']) ? ceil($booking['sisa_booking'] / 24) : 0;
                $carry += $sisa;
                return $carry;
            }, 0);

            $maxCapacity = array_reduce($brands, function ($carry, $brand) use ($brandData, $numberOfDays) {
                $brandCapacity = 0;
                // Periksa apakah $brandData[$brand] adalah array
                if (isset($brandData[$brand]) && is_array($brandData[$brand])) {
                    // Pastikan $brandData[$brand] ada dan berisi array
                    if (isset($brandData[$brand])) {
                        $jumlahMc = $brandData[$brand]['totalCj'] ?? 0;
                        $target = $brandData[$brand]['target'] ?? 0; // Pastikan target juga ada di $brandData
                        if ($jumlahMc > 0) {
                            $brandCapacity = ceil($jumlahMc * $target * $numberOfDays);
                        }
                    }
                }
                $carry += $brandCapacity;
                return $carry;
            }, 0);

            $countExess = -$maxCapacity + $sisaBooking + $sisaConfirmOrder;
            $exess = ($countExess > 0) ? ceil($countExess) : floor($countExess);

            $countExessPercentage = ($maxCapacity > 0) ? ($exess / $maxCapacity) * 100 : 0;
            $exessPercentage = ($countExessPercentage > 0) ? ceil($countExessPercentage) : floor($countExessPercentage);

            $monthlyData[$currentMonthOfYear]['weeks'][] = [
                'countWeek' => $weekCount,
                'start_date' => $startDate->format('d-m'),
                'end_date' => $endOfWeek->format('d-m'),
                'number_of_days' => $numberOfDays,
                'holidays' => array_map(function ($holiday) {
                    return [
                        'nama' => $holiday['nama'],
                        'tanggal' => (new \DateTime($holiday['tanggal']))->format('d-F'),
                    ];
                }, $weekHolidays),
                'maxCapacity' => $maxCapacity,
                'totalBooking' => $totalBooking,
                'sisaBooking' => $sisaBooking,
                'ConfirmOrder' => $ConfirmOrder,
                'sisaConfirmOrder' => $sisaConfirmOrder,
                'exess' => $exess,
                'exessPercentage' => $exessPercentage,
            ];

            // Update data bulanan
            $monthlyData[$currentMonthOfYear]['monthlySummary']['totalMaxCapacity'] += $maxCapacity;
            $monthlyData[$currentMonthOfYear]['monthlySummary']['totalSisaBooking'] += $sisaBooking;
            $monthlyData[$currentMonthOfYear]['monthlySummary']['totalConfirmOrder'] += $ConfirmOrder;
            $monthlyData[$currentMonthOfYear]['monthlySummary']['totalSisaConfirmOrder'] += $sisaConfirmOrder;
            $monthlyData[$currentMonthOfYear]['monthlySummary']['totalExess'] += $exess;

            // Ambil total max kapasitas dan exess setelah penjumlahan
            $totalMaxCapacity = $monthlyData[$currentMonthOfYear]['monthlySummary']['totalMaxCapacity'];
            $totalExess = $monthlyData[$currentMonthOfYear]['monthlySummary']['totalExess'];
            // Hitung persentase setelah penjumlahan
            $exessPercentage = ($totalMaxCapacity > 0) ? (($totalExess / $totalMaxCapacity) * 100) : 0;
            $monthlyData[$currentMonthOfYear]['monthlySummary']['totalExessPercentage'] = $exessPercentage;

            // Perbarui tanggal awal untuk minggu berikutnya
            if ($endOfWeek == $endOfMonth) {
                // Mulai dari hari berikutnya setelah akhir bulan
                $startDate = (clone $endOfMonth)->modify('+1 day');
            } else {
                // Lanjutkan dari hari berikutnya setelah akhir minggu
                $startDate = (clone $endOfWeek)->modify('+1 day');
            }

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
                'size' => 12,
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
        $sheet->setCellValue('A1', 'Sales Position ' . $aliasjarum);
        $sheet->mergeCells('A1:J1');
        // Mengatur teks menjadi rata tengah dan huruf tebal
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $rowHeader = 3;
        $rowHeader2 = 4;
        $rowHeader3 = 5;
        $endMergHeader = 6;
        // Header
        $sheet->setCellValue('A' . $rowHeader, 'Kind Of Machine')
            ->mergeCells('A' . $rowHeader . ':A' . $endMergHeader)
            ->getStyle('A' . $rowHeader . ':A' . $endMergHeader)
            ->applyFromArray($styleHeader);

        $sheet->setCellValue('B' . $rowHeader, 'No Of Mc')
            ->mergeCells('B' . $rowHeader . ':N' . $rowHeader2)
            ->getStyle('B' . $rowHeader . ':N' . $rowHeader2)
            ->applyFromArray($styleHeader);
        // jumlah mesin
        $sheet->setCellValue('B' . $rowHeader3, 'Machine')
            ->mergeCells('B' . $rowHeader3 . ':F' . $rowHeader3)
            ->getStyle('B' . $rowHeader3 . ':F' . $rowHeader3)
            ->applyFromArray($styleHeader2);

        $sheet->setCellValue('B' . $endMergHeader, 'Dakong')
            ->getStyle('B' . $endMergHeader)
            ->applyFromArray($styleHeader2);

        $sheet->setCellValue('C' . $endMergHeader, 'Rosso')
            ->getStyle('C' . $endMergHeader)
            ->applyFromArray($styleHeader2);

        $sheet->setCellValue('D' . $endMergHeader, 'Ths')
            ->getStyle('D' . $endMergHeader)
            ->applyFromArray($styleHeader2);

        $sheet->setCellValue('E' . $endMergHeader, 'Lonati')
            ->getStyle('E' . $endMergHeader)
            ->applyFromArray($styleHeader2);

        $sheet->setCellValue('F' . $endMergHeader, 'Total')
            ->getStyle('F' . $endMergHeader)
            ->applyFromArray($styleHeader2);
        // mesin running
        $sheet->setCellValue('G' . $rowHeader3, 'Running')
            ->mergeCells('G' . $rowHeader3 . ':K' . $rowHeader3)
            ->getStyle('G' . $rowHeader3 . ':K' . $rowHeader3)
            ->applyFromArray($styleHeader2);

        $sheet->setCellValue('G' . $endMergHeader, 'Dakong')
            ->getStyle('G' . $endMergHeader)
            ->applyFromArray($styleHeader2);

        $sheet->setCellValue('H' . $endMergHeader, 'Rosso')
            ->getStyle('H' . $endMergHeader)
            ->applyFromArray($styleHeader2);

        $sheet->setCellValue('I' . $endMergHeader, 'Ths')
            ->getStyle('I' . $endMergHeader)
            ->applyFromArray($styleHeader2);

        $sheet->setCellValue('J' . $endMergHeader, 'Lonati')
            ->getStyle('J' . $endMergHeader)
            ->applyFromArray($styleHeader2);

        $sheet->setCellValue('K' . $endMergHeader, 'Total')
            ->getStyle('K' . $endMergHeader)
            ->applyFromArray($styleHeader2);
        // stock cylinder
        $sheet->setCellValue('L' . $rowHeader3, 'Stock Cylinder')
            ->mergeCells('L' . $rowHeader3 . ':N' . $rowHeader3)
            ->getStyle('L' . $rowHeader3 . ':N' . $rowHeader3)
            ->applyFromArray($styleHeader2);

        $sheet->setCellValue('L' . $endMergHeader, 'Dakong')
            ->getStyle('L' . $endMergHeader)
            ->applyFromArray($styleHeader2);

        $sheet->setCellValue('M' . $endMergHeader, 'Rosso')
            ->getStyle('M' . $endMergHeader)
            ->applyFromArray($styleHeader2);

        $sheet->setCellValue('N' . $endMergHeader, 'Ths')
            ->getStyle('N' . $endMergHeader)
            ->applyFromArray($styleHeader2);
        // actual running mc
        $sheet->setCellValue('O' . $rowHeader, 'Running Mc Actual')
            ->mergeCells('O' . $rowHeader . ':Q' . $rowHeader3)
            ->getStyle('O' . $rowHeader . ':Q' . $rowHeader3)
            ->applyFromArray($styleHeader)
            ->getAlignment()->setWrapText(true); // Menambahkan pengaturan wrap text

        $sheet->setCellValue('O' . $endMergHeader, 'CJ')
            ->getStyle('O' . $endMergHeader)
            ->applyFromArray($styleHeader2);

        $sheet->setCellValue('P' . $endMergHeader, 'MJ')
            ->getStyle('P' . $endMergHeader)
            ->applyFromArray($styleHeader2);

        $sheet->setCellValue('Q' . $endMergHeader, 'Total')
            ->getStyle('Q' . $endMergHeader)
            ->applyFromArray($styleHeader2);

        $sheet->setCellValue('R' . $rowHeader, 'Prod 28days')
            ->mergeCells('R' . $rowHeader . ':R' . $endMergHeader)
            ->getStyle('R' . $rowHeader . ':R' . $endMergHeader)
            ->applyFromArray($styleHeader)
            ->getAlignment()->setWrapText(true); // Menambahkan pengaturan wrap text


        $column = 'S'; // Kolom awal untuk bulan
        $col_index = Coordinate::columnIndexFromString($column); // Konversi huruf kolom ke nomor indeks kolom
        $column2 = 'S';
        $col_index2 = Coordinate::columnIndexFromString($column2);
        foreach ($monthlyData as $month => $data) :
            // month
            $weekCount = count($data['weeks']); // Hitung jumlah minggu dalam bulan
            $startColumn = $column;
            $endCol_index = $col_index + ($weekCount * 6) - 1; // Dikurangi 1 karena kolom awal tidak terhitung
            $endColumn = Coordinate::stringFromColumnIndex($endCol_index); // Konversi kembali dari nomor indeks kolom ke huruf kolom

            // Merge cells untuk bulan ini sesuai dengan jumlah minggu
            $sheet->setCellValue($startColumn . $rowHeader, $month);
            if ($startColumn !== $endColumn) {
                $sheet->mergeCells($startColumn . $rowHeader . ':' . $endColumn . $rowHeader)
                    ->getStyle($startColumn . $rowHeader . ':' . $endColumn . $rowHeader)
                    ->applyFromArray($styleHeader);
            } else {
                $sheet->getStyle($startColumn . $rowHeader)
                    ->applyFromArray($styleHeader);
            }

            // Pastikan $column2 dan $col_index2 berada di luar loop bulan
            $rowWeek = $rowHeader + 1; // Baris awal untuk week

            foreach ($data['weeks'] as $week) :
                $startDate = $week['start_date'];
                $endDate = $week['end_date'];
                $countWeek = $week['countWeek'];
                $tgl = $startDate . '-' . $endDate . ' (' . $countWeek . ')';
                $rowWh = $rowWeek + 1;

                $startColumn2 = $column2;
                $endCol_index2 = $col_index2 + 5; // Misalnya, 4 kolom untuk setiap minggu
                $endColumn2 = Coordinate::stringFromColumnIndex($endCol_index2);

                // Set value dan merge cells untuk minggu
                $sheet->setCellValue($startColumn2 . $rowWeek, $tgl);
                $sheet->mergeCells($startColumn2 . $rowWeek . ':' . $endColumn2 . $rowWeek)
                    ->getStyle($startColumn2 . $rowWeek . ':' . $endColumn2 . $rowWeek)
                    ->applyFromArray($styleHeader2);

                // Set value dan merge cells untuk hari kerja
                $sheet->setCellValue($startColumn2 . $rowWh, $week['number_of_days'] . ' hari');
                $sheet->mergeCells($startColumn2 . $rowWh . ':' . $endColumn2 . $rowWh)
                    ->getStyle($startColumn2 . $rowWh . ':' . $endColumn2 . $rowWh)
                    ->applyFromArray($styleHeader2);

                $rowTitle = $rowWh + 1;
                foreach ($data['weeks'] as $week) :
                    $sheet->setCellValue($startColumn2 . $rowTitle, 'Max Capacity')
                        ->getStyle($startColumn2 . $rowTitle)
                        ->applyFromArray($styleHeader2);
                    // 
                    $max_index = $col_index2 + 1; // Next Column
                    $nextColumn = Coordinate::stringFromColumnIndex($max_index);
                    // 
                    $sheet->setCellValue($nextColumn . $rowTitle, 'Confirm Order')
                        ->getStyle($nextColumn . $rowTitle)
                        ->applyFromArray($styleHeader2);
                    // 
                    $confirm_index = $max_index + 1; // Next Column
                    $nextColumn = Coordinate::stringFromColumnIndex($confirm_index);
                    // 
                    $sheet->setCellValue($nextColumn . $rowTitle, 'Sisa Order')
                        ->getStyle($nextColumn . $rowTitle)
                        ->applyFromArray($styleHeader2);
                    // 
                    $order_index = $confirm_index + 1; // Next Column
                    $nextColumn = Coordinate::stringFromColumnIndex($order_index);
                    // 
                    $sheet->setCellValue($nextColumn . $rowTitle, 'Sisa Booking')
                        ->getStyle($nextColumn . $rowTitle)
                        ->applyFromArray($styleHeader2);
                    // 
                    $booking_index = $order_index + 1; // Next Column
                    $nextColumn = Coordinate::stringFromColumnIndex($booking_index);
                    // 
                    $sheet->setCellValue($nextColumn . $rowTitle, '(+)Exess')
                        ->getStyle($nextColumn . $rowTitle)
                        ->applyFromArray($styleHeader2);
                    // 
                    $exess_index = $booking_index + 1; // Next Column
                    $nextColumn = Coordinate::stringFromColumnIndex($exess_index);
                    // 

                    $sheet->setCellValue($nextColumn . $rowTitle, '%')
                        ->getStyle($nextColumn . $rowTitle)
                        ->applyFromArray($styleHeader2);
                    // 
                    $exessPercentage_index = $booking_index + 1; // Next Column
                    $startColumn2 = Coordinate::stringFromColumnIndex($exessPercentage_index);
                endforeach;
                // Update ke indeks kolom berikutnya
                $col_index2 = $endCol_index2 + 1;
                $column2 = Coordinate::stringFromColumnIndex($col_index2);
            endforeach;
            // Pindah ke kolom berikutnya setelah melakukan merge
            $col_index = $endCol_index + 1; // Update ke indeks kolom berikutnya
            $column = Coordinate::stringFromColumnIndex($col_index); // Konversi kembali ke huruf kolom
        endforeach;

        // Body
        $rowBody = 7;
        // total mc
        $sheet->setCellValue('A' . $rowBody, $aliasjarum)
            ->getStyle('A' . $rowBody);
        $sheet->setCellValue('B' . $rowBody, isset($brandData['Dakong']['totalCj']) ? $brandData['Dakong']['totalCj'] : 0)
            ->getStyle('B' . $rowBody)
            ->applyFromArray($styleBody);
        $sheet->setCellValue('C' . $rowBody, isset($brandData['Rosso']['totalCj']) ? $brandData['Rosso']['totalCj'] : 0)
            ->getStyle('C' . $rowBody)
            ->applyFromArray($styleBody);
        $sheet->setCellValue('D' . $rowBody, isset($brandData['Mechanic']['totalCj']) ? $brandData['Mechanic']['totalCj'] : 0)
            ->getStyle('D' . $rowBody)
            ->applyFromArray($styleBody);
        $sheet->setCellValue('E' . $rowBody, isset($brandData['Lonati']['totalCj']) ? $brandData['Lonati']['totalCj'] : 0)
            ->getStyle('E' . $rowBody)
            ->applyFromArray($styleBody);
        $sheet->setCellValue('F' . $rowBody, isset($totals['totalCj']) ? $totals['totalCj'] : 0)
            ->getStyle('F' . $rowBody)
            ->applyFromArray($styleBody);
        // running mc by system
        $sheet->setCellValue('G' . $rowBody, isset($brandData['Dakong']['totalRunning']) ? $brandData['Dakong']['totalRunning'] : 0)
            ->getStyle('G' . $rowBody)
            ->applyFromArray($styleBody);
        $sheet->setCellValue('H' . $rowBody, isset($brandData['Rosso']['totalRunning']) ? $brandData['Rosso']['totalRunning'] : 0)
            ->getStyle('H' . $rowBody)
            ->applyFromArray($styleBody);
        $sheet->setCellValue('I' . $rowBody, isset($brandData['Mechanic']['totalRunning']) ? $brandData['Mechanic']['totalRunning'] : 0)
            ->getStyle('I' . $rowBody)
            ->applyFromArray($styleBody);
        $sheet->setCellValue('J' . $rowBody, isset($brandData['Lonati']['totalRunning']) ? $brandData['Lonati']['totalRunning'] : 0)
            ->getStyle('J' . $rowBody)
            ->applyFromArray($styleBody);
        $sheet->setCellValue('K' . $rowBody, isset($totals['totalRunning']) ? $totals['totalRunning'] : 0)
            ->getStyle('K' . $rowBody)
            ->applyFromArray($styleBody);
        // stock cylinder
        $sheet->setCellValue('L' . $rowBody, 0)
            ->getStyle('L' . $rowBody)
            ->applyFromArray($styleBody);
        $sheet->setCellValue('M' . $rowBody, 0)
            ->getStyle('M' . $rowBody)
            ->applyFromArray($styleBody);
        $sheet->setCellValue('N' . $rowBody, 0)
            ->getStyle('N' . $rowBody)
            ->applyFromArray($styleBody);
        // running mc actual
        $sheet->setCellValue('O' . $rowBody, 0)
            ->getStyle('O' . $rowBody)
            ->applyFromArray($styleBody);
        $sheet->setCellValue('P' . $rowBody, 0)
            ->getStyle('P' . $rowBody)
            ->applyFromArray($styleBody);
        $sheet->setCellValue('Q' . $rowBody, 0)
            ->getStyle('Q' . $rowBody)
            ->applyFromArray($styleBody);

        $sheet->setCellValue('R' . $rowBody, $totals['totalProd'])
            ->getStyle('R' . $rowBody)
            ->applyFromArray($styleBody);


        $columnBody = 'S'; // Kolom awal 
        $colBody_index = Coordinate::columnIndexFromString($columnBody); // Konversi huruf kolom ke nomor indeks kolom
        foreach ($monthlyData as $month => $data) :
            $startColumnBody = $columnBody; // Kolom awal 

            foreach ($data['weeks'] as $week) :
                $sheet->setCellValue($startColumnBody . $rowBody, isset($week['maxCapacity']) ? $week['maxCapacity'] : 0)
                    ->getStyle($startColumnBody . $rowBody)
                    ->applyFromArray($styleBody);
                $colBody_index++;
                $nextColumnBody = Coordinate::stringFromColumnIndex($colBody_index); // Konversi 

                $sheet->setCellValue($nextColumnBody . $rowBody, isset($week['ConfirmOrder']) ? $week['ConfirmOrder'] : 0)
                    ->getStyle($nextColumnBody . $rowBody)
                    ->applyFromArray($styleBody);
                $colBody_index++;
                $nextColumnBody = Coordinate::stringFromColumnIndex($colBody_index); // Konversi 

                $sheet->setCellValue($nextColumnBody . $rowBody, isset($week['sisaConfirmOrder']) ? $week['sisaConfirmOrder'] : 0)
                    ->getStyle($nextColumnBody . $rowBody)
                    ->applyFromArray($styleBody);
                $colBody_index++;
                $nextColumnBody = Coordinate::stringFromColumnIndex($colBody_index); // Konversi 

                $sheet->setCellValue($nextColumnBody . $rowBody, isset($week['sisaBooking']) ? $week['sisaBooking'] : 0)
                    ->getStyle($nextColumnBody . $rowBody)
                    ->applyFromArray($styleBody);
                $colBody_index++;
                $nextColumnBody = Coordinate::stringFromColumnIndex($colBody_index); // Konversi 

                $sheet->setCellValue($nextColumnBody . $rowBody, isset($week['exess']) ? $week['exess'] : 0)
                    ->getStyle($nextColumnBody . $rowBody)
                    ->applyFromArray($styleBody);
                $colBody_index++;
                $nextColumnBody = Coordinate::stringFromColumnIndex($colBody_index); // Konversi 

                $sheet->setCellValue($nextColumnBody . $rowBody, isset($week['exessPercentage']) ? $week['exessPercentage'] . '%' : 0)
                    ->getStyle($nextColumnBody . $rowBody)
                    ->applyFromArray($styleBody);

                $colBody_index++;
                $startColumnBody = Coordinate::stringFromColumnIndex($colBody_index); // Konversi 
            endforeach;
            $columnBody = Coordinate::stringFromColumnIndex($colBody_index); // Konversi 
        endforeach;

        // style kolom body
        $sheet->getStyle('A' . $rowBody)->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT, // Alignment rata tengah
                'vertical' => Alignment::VERTICAL_CENTER, // Alignment rata tengah
            ],
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THIN, // Gaya garis tipis
                    'color' => ['argb' => 'FF000000'],    // Warna garis hitam
                ],
            ],
        ]);

        // Set judul file dan header untuk download
        $filename = 'Sales Position ' . $aliasjarum . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // Tulis file excel ke output
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        // return redirect(session()->get('role') . '/sales');
        exit;
    }

    public function generateExcel()
    {
        $dataJarum = $this->jarumModel->getAliasJrm(); // data all jarum
        $brands = ['Dakong', 'Rosso', 'Mechanic', 'Lonati'];

        $allData = [];
        $totalExportPerBulan = [];
        $targetExportPerbulan = [];
        $totalLokalPerBulan = [];
        $ttlConfirmPerBulan = [];
        foreach ($dataJarum as $alias) {
            $aliasjarum = $alias['aliasjarum'];
            $jarum = $alias['jarum'];
            $brandData = [];
            $totals = [
                'totalMc' => 0,
                'totalCj' => 0,
                'totalRunning' => 0,
                'totalSample' => 0,
                'totalWarehouse' => 0,
                'totalBreakdown' => 0,
                'totalRunningAct' => 0,
                'totalProd' => 0,
                'totalCylinderDk' => 0,
                'totalCylinderThs' => 0,
                'totalCylinderRs' => 0
            ];
            $dataRunningSplWh = $this->jarumModel->getRunningMcSplAllAlias($aliasjarum); // get data running sample per aliasjarum
            // Ambil data kapasitas per brand dan hitung total
            foreach ($brands as $brand) {
                $data = $this->jarumModel->getTotalMcPerBrand($brand, $aliasjarum); // total mc per brand + sample & total mc running tanpa sample
                $dataCylinder = $this->cylinderModel->getCylinder($jarum); // ambil dara cylinder berdasarkan jarum & brand

                // Inisialisasi total per brand
                $brandTotals = [
                    'totalMc' => 0,
                    'totalCj' => 0,
                    'totalRunning' => 0,
                    'totalRunningAct' => 0,
                    'target' => 0,
                    'totalProd' => 0,
                ];
                // Hitung total untuk setiap brand
                foreach ($data as $item) {
                    // Pastikan $item adalah array dan memiliki kunci yang dibutuhkan
                    if (is_array($item)) {
                        $prod = $item['running'] * $item['target'] * 28;

                        // Tambahkan hasil perhitungan ke total per brand
                        $brandTotals['totalMc'] += $item['total_mc'] ?? 0;
                        $brandTotals['totalCj'] += $item['cj'] ?? 0;
                        $brandTotals['totalRunning'] += $item['running'] ?? 0;
                        $brandTotals['target'] = $item['target'] ?? 0;
                        $brandTotals['totalRunningAct'] += $item['running_act'] ?? 0;
                        $brandTotals['totalProd'] += ceil($prod);
                    }
                }

                // Simpan total per brand ke dalam $brandData
                $brandData[$brand] = $brandTotals;

                // Tambahkan ke total keseluruhan
                $totals['totalMc'] += $brandTotals['totalMc'] ?? 0;
                $totals['totalCj'] += $brandTotals['totalCj'] ?? 0;
                $totals['totalRunning'] += $brandTotals['totalRunning'] ?? 0;
                $totals['totalRunningAct'] += $brandTotals['totalRunningAct'] ?? 0;
                $totals['totalProd'] += $brandTotals['totalProd'] ?? 0;
            }

            foreach ($dataRunningSplWh as $splWh) {
                if (is_array($splWh)) {
                    $totals['totalSample'] += $splWh['running_spl'] ?? 0;
                    $totals['totalWarehouse'] += $splWh['warehouse'] ?? 0;
                    $totals['totalBreakdown'] += $splWh['breakdown'] ?? 0;
                }
            }

            foreach ($dataCylinder as $cylinder) {
                $totals['totalCylinderDk'] = $cylinder['qty_dakong'];
                $totals['totalCylinderThs'] = $cylinder['qty_ths'];
                $totals['totalCylinderRs'] = $cylinder['qty_rosso'];
            }

            $startDate = new \DateTime('first day of this month'); // Awal bulan ini
            $endDate = new \DateTime('+1 years'); // Tanggal satu tahun ke depan

            $LiburModel = new LiburModel();
            $holidays = $LiburModel->findAll();

            $monthlyData = [];
            // Penentuan week
            $currentYear = (new \DateTime())->format('Y'); // Ambil tahun dari tanggal hari ini
            $startWeek = new \DateTime("$currentYear-01-01"); // Buat tanggal untuk hari pertama bulan Januari tahun ini
            $startWeek->modify('first Monday of January'); // ubah ke tgl awal di hari senin
            $endWeek = new \DateTime('+1 years'); // tanggal satu tahun kedepan dari hari ini
            $weekCount = 1; // Inisialisasi minggu
            $weeklyData = []; // Inisialisasi array untuk menyimpan data mingguan
            $currentYearWeeklyData = []; // Menyimpan data untuk tahun tertentu

            while ($startWeek <= $endWeek) {
                // Tanggal akhir tahun
                $endOfYear = new \DateTime("$currentYear-12-31");
                // Hitung akhir minggu di tahun ini
                $endOfYears = (clone $endOfYear)->modify('next Sunday');
                // Hitung akhir minggu (Minggu)
                $endOfWeeks = (clone $startWeek)->modify('Sunday this week');

                // Simpan data minggu
                $currentYearWeeklyData[$weekCount] = [
                    'weekCount' => $weekCount,
                    'start_date' => $startWeek->format('Y-m-d'),
                    'end_date' => $endOfWeeks->format('Y-m-d'),
                ];

                // Update tanggal untuk minggu berikutnya
                $startWeek->modify('+1 week'); // Tambah satu minggu

                // Cek apakah sudah melewati akhir tahun
                if ($startWeek > $endOfYears) {
                    $weeklyData = array_merge($weeklyData, $currentYearWeeklyData); // Simpan data tahun ini ke weeklyData

                    // Reset untuk tahun baru
                    $currentYear++; // Dapatkan tahun baru
                    $currentYearWeeklyData = []; // Reset data untuk tahun baru
                    $weekCount = 1; // Reset weekCount untuk tahun baru
                } else {
                    $weekCount++;
                }
            }

            // Tambahkan data terakhir jika ada
            if (!empty($currentYearWeeklyData)) {
                $weeklyData = array_merge($weeklyData, $currentYearWeeklyData);
            }

            while ($startDate <= $endDate) {
                $endOfWeek = (clone $startDate)->modify('Sunday this week');
                // Tentukan akhir bulan dari tanggal awal saat ini
                $endOfMonth = new \DateTime($startDate->format('Y-m-t')); // Akhir bulan saat ini

                // Jika akhir minggu melebihi akhir bulan, batasi hingga akhir bulan
                if ($endOfWeek > $endOfMonth) {
                    $endOfWeek = clone $endOfMonth; // Akhiri minggu di akhir bulan
                }

                // Inisialisasi variabel untuk menyimpan minggu saat ini
                $currentWeekCount = null;
                foreach ($weeklyData as $weekly) {
                    // Periksa apakah startDate berada dalam rentang minggu
                    if ($startDate->format('Y-m-d') >= $weekly['start_date'] && $endOfWeek->format('Y-m-d') <= $weekly['end_date']) {
                        $currentWeekCount = $weekly['weekCount'];
                        break; // Jika sudah ditemukan, keluar dari loop
                    }
                }

                $numberOfDays = $startDate->diff($endOfWeek)->days + 1; //hitung jumlah hari week ini

                // Hitung libur minggu ini
                $weekHolidays = array_filter($holidays, function ($holiday) use ($startDate, $endOfWeek) {
                    $holidayDate = new \DateTime($holiday['tanggal']);
                    return $holidayDate >= $startDate && $holidayDate <= $endOfWeek;
                });

                $holidaysCount = count($weekHolidays);
                $numberOfDays -= $holidaysCount;

                $currentMonthOfYear = $startDate->format('F-Y');
                if (!isset($monthlyData[$currentMonthOfYear])) {
                    $monthlyData[$currentMonthOfYear] = [
                        'monthlySummary' => [
                            'totalMaxCapacity' => 0,
                            'totalSisaBooking' => 0,
                            'totalConfirmOrder' => 0,
                            'totalSisaConfirmOrder' => 0,
                            'totalExess' => 0,
                            'totalExessPercentage' => 0,
                        ],
                        'weeks' => []
                    ];
                }

                // Ambil data booking dan order per minggu
                $cek = [
                    'jarum' => $jarum,
                    'start' => $startDate->format('Y-m-d'),
                    'end' => $endOfWeek->format('Y-m-d'),
                ];

                $dataBookingByJarum = $this->bookingModel->getTotalBookingByJarum2($cek); // Data booking per jarum
                $dataOrderWeekByJarum = $this->ApsPerstyleModel->getTotalOrderWeek($cek); // Data order per jarum

                $ConfirmOrder = array_reduce($dataOrderWeekByJarum, function ($carry, $order) {
                    $qty = !empty($order['qty']) ? ceil($order['qty'] / 24) : 0;
                    $carry += $qty;
                    return $carry;
                }, 0);

                $sisaConfirmOrder = array_reduce($dataOrderWeekByJarum, function ($carry, $order) {
                    $sisa = !empty($order['sisa']) ? ceil($order['sisa'] / 24) : 0;
                    $carry += $sisa;
                    return $carry;
                }, 0);

                $totalBooking = array_reduce($dataBookingByJarum, function ($carry, $booking) {
                    $total = !empty($booking['total_booking']) ? ceil($booking['total_booking'] / 24) : 0;
                    $carry += $total;
                    return $carry;
                }, 0);

                // $sisaBooking = array_reduce($dataBookingByJarum, function ($carry, $booking) {
                //     $sisa = !empty($booking['sisa_booking']) ? ceil($booking['sisa_booking'] / 24) : 0;
                //     $carry += $sisa;
                //     return $carry;
                // }, 0);
                $sisaBooking = array_reduce($dataBookingByJarum, function ($carry, $booking) {
                    $sisa_booking = $booking['sisa_booking'];
                    switch ($booking['product_group']) {
                        case "SS":
                            $total = !empty($sisa_booking) ? ceil(($sisa_booking - ($sisa_booking * (25 / 100))) / 24) : 0;
                            break;
                        case "S":
                            $total = !empty($sisa_booking) ? ceil(($sisa_booking - ($sisa_booking * (25 / 100))) / 24) : 0;
                            break;
                        case "F":
                            $total = !empty($sisa_booking) ? ceil(($sisa_booking - ($sisa_booking * (30 / 100))) / 24) : 0;
                            break;
                        case "KH":
                            $total = !empty($sisa_booking) ? ceil(($sisa_booking * 1.5 / 24)) : 0;
                            break;
                        case "TG":
                            $total = !empty($sisa_booking) ? ceil(($sisa_booking * 2 / 24)) : 0;
                            break;
                        default:
                            $total = !empty($sisa_booking) ? ceil($sisa_booking / 24) : 0;
                            break;
                    }
                    $carry += $total;
                    return $carry;
                }, 0);

                $maxCapacity = array_reduce($brands, function ($carry, $brand) use ($brandData, $numberOfDays) {
                    $brandCapacity = 0;
                    // Periksa apakah $brandData[$brand] adalah array
                    if (isset($brandData[$brand]) && is_array($brandData[$brand])) {
                        // Pastikan $brandData[$brand] ada dan berisi array
                        if (isset($brandData[$brand])) {
                            $jumlahMc = $brandData[$brand]['totalCj'] ?? 0;
                            $target = $brandData[$brand]['target'] ?? 0; // Pastikan target juga ada di $brandData
                            if ($jumlahMc > 0) {
                                $brandCapacity = ceil($jumlahMc * $target * $numberOfDays);
                            }
                        }
                    }
                    $carry += $brandCapacity;
                    return $carry;
                }, 0);

                $countExess = -$maxCapacity + $sisaBooking + $sisaConfirmOrder;
                $exess = ($countExess > 0) ? ceil($countExess) : floor($countExess);

                $countExessPercentage = ($maxCapacity > 0) ? ($exess / $maxCapacity) * 100 : 0;
                $exessPercentage = ($countExessPercentage > 0) ? ceil($countExessPercentage) : floor($countExessPercentage);

                $monthlyData[$currentMonthOfYear]['weeks'][] = [
                    'countWeek' => $currentWeekCount,
                    'start_date' => $startDate->format('d-m'),
                    'end_date' => $endOfWeek->format('d-m'),
                    'number_of_days' => $numberOfDays,
                    'holidays' => array_map(function ($holiday) {
                        return [
                            'nama' => $holiday['nama'],
                            'tanggal' => (new \DateTime($holiday['tanggal']))->format('d-F'),
                        ];
                    }, $weekHolidays),
                    'maxCapacity' => $maxCapacity,
                    'totalBooking' => $totalBooking,
                    'sisaBooking' => $sisaBooking,
                    'ConfirmOrder' => $ConfirmOrder,
                    'sisaConfirmOrder' => $sisaConfirmOrder,
                    'exess' => $exess,
                    'exessPercentage' => $exessPercentage,
                ];

                // Update data bulanan
                $monthlyData[$currentMonthOfYear]['monthlySummary']['totalMaxCapacity'] += $maxCapacity;
                $monthlyData[$currentMonthOfYear]['monthlySummary']['totalSisaBooking'] += $sisaBooking;
                $monthlyData[$currentMonthOfYear]['monthlySummary']['totalConfirmOrder'] += $ConfirmOrder;
                $monthlyData[$currentMonthOfYear]['monthlySummary']['totalSisaConfirmOrder'] += $sisaConfirmOrder;
                $monthlyData[$currentMonthOfYear]['monthlySummary']['totalExess'] += $exess;

                $thisMonth = [
                    'start' => $startDate->format('Y-m-') . '01',
                    'end' => $startDate->format('Y-m-') . '25',
                    'month' => $startDate->format('F-Y'),
                ];

                // data target export actual yg di inuput
                $dataTargetExport = $this->targetExportModel->getData($thisMonth);
                if (!isset($targetExportPerbulan[$currentMonthOfYear])) {
                    $targetExportPerbulan[$currentMonthOfYear] = 0; // Inisialisasi jika belum ada
                }
                $targetExportPerbulan[$currentMonthOfYear] = !empty($dataTargetExport['qty_target']) ? ceil($dataTargetExport['qty_target'] / 24) : 0;
                // data export, inspect & lokal per bulan
                $dataExport = $this->ApsPerstyleModel->getTotalConfirmByMonth($thisMonth);
                foreach ($dataExport as $dExport) {
                    $export = ($dExport > 0) ? ceil($dExport / 24) : 0;

                    // Tambahkan export ke total per bulan
                    if (!isset($totalExportPerBulan[$currentMonthOfYear])) {
                        $totalExportPerBulan[$currentMonthOfYear] = 0; // Inisialisasi jika belum ada
                    }
                    $totalExportPerBulan[$currentMonthOfYear] = $export; // Tambahkan export
                }
                $dataLokal = $this->ApsPerstyleModel->getTotalConfirmByMontLokal($thisMonth);
                foreach ($dataLokal as $dLokal) {
                    $lokal = ($dLokal > 0) ? ceil($dLokal / 24) : 0;

                    // Tambahkan lokal ke total per bulan
                    $currentMonthOfYear = $startDate->format('F-Y');
                    if (!isset($totalLokalPerBulan[$currentMonthOfYear])) {
                        $totalLokalPerBulan[$currentMonthOfYear] = 0; // Inisialisasi jika belum ada
                    }
                    $totalLokalPerBulan[$currentMonthOfYear] = $lokal; // Tambahkan lokal
                }

                // Ambil total max kapasitas dan exess setelah penjumlahan
                $totalMaxCapacity = $monthlyData[$currentMonthOfYear]['monthlySummary']['totalMaxCapacity'];
                $totalExess = $monthlyData[$currentMonthOfYear]['monthlySummary']['totalExess'];
                // Hitung persentase setelah penjumlahan
                $exessPercentage = ($totalMaxCapacity > 0) ? (($totalExess / $totalMaxCapacity) * 100) : 0;
                $monthlyData[$currentMonthOfYear]['monthlySummary']['totalExessPercentage'] = $exessPercentage;

                // Perbarui tanggal awal untuk minggu berikutnya
                if ($endOfWeek == $endOfMonth) {
                    // Mulai dari hari berikutnya setelah akhir bulan
                    $startDate = (clone $endOfMonth)->modify('+1 day');
                } else {
                    // Lanjutkan dari hari berikutnya setelah akhir minggu
                    $startDate = (clone $endOfWeek)->modify('+1 day');
                }
            }

            // Setelah semua data mingguan diproses, tampilkan hasil per aliasjarum
            $allData[$aliasjarum] = [
                'jarum' => $jarum,
                'brandData' => $brandData,
                'totals' => $totals,
                'monthlyData' => $monthlyData,
            ];
        }
        $allData['total'] = [
            'totalExport' => $totalExportPerBulan,
            'totalLokal' => $totalLokalPerBulan,
            'targetExport' => $targetExportPerbulan,
        ];

        dd($allData);

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
                'size' => 12,
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
        $styleGrandTotal = [
            'font' => [
                'bold' => true, // Tebalkan teks
                'size' => 12,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT, // Alignment rata tengah
                'vertical' => Alignment::VERTICAL_CENTER, // Alignment rata tengah
            ],
        ];

        // Autosize kolom
        foreach (range('A', 'BC') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Judul
        $sheet->setCellValue('A1', 'Sales Position');
        $sheet->mergeCells('A1:J1');
        // Mengatur teks menjadi rata tengah dan huruf tebal
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $rowHeader = 3;
        $rowHeader2 = 4;
        $rowHeader3 = 5;
        $endMergHeader = 6;
        // Header
        $sheet->setCellValue('A' . $rowHeader, 'Kind Of Machine')
            ->mergeCells('A' . $rowHeader . ':A' . $endMergHeader)
            ->getStyle('A' . $rowHeader . ':A' . $endMergHeader)
            ->applyFromArray($styleHeader);

        $sheet->setCellValue('B' . $rowHeader, 'No Of Mc')
            ->mergeCells('B' . $rowHeader . ':Q' . $rowHeader2)
            ->getStyle('B' . $rowHeader . ':Q' . $rowHeader2)
            ->applyFromArray($styleHeader);
        // jumlah mesin
        $sheet->setCellValue('B' . $rowHeader3, 'Machine')
            ->mergeCells('B' . $rowHeader3 . ':F' . $rowHeader3)
            ->getStyle('B' . $rowHeader3 . ':F' . $rowHeader3)
            ->applyFromArray($styleHeader2);

        $sheet->setCellValue('B' . $endMergHeader, 'Dakong')
            ->getStyle('B' . $endMergHeader)
            ->applyFromArray($styleHeader2);

        $sheet->setCellValue('C' . $endMergHeader, 'Rosso')
            ->getStyle('C' . $endMergHeader)
            ->applyFromArray($styleHeader2);

        $sheet->setCellValue('D' . $endMergHeader, 'Ths')
            ->getStyle('D' . $endMergHeader)
            ->applyFromArray($styleHeader2);

        $sheet->setCellValue('E' . $endMergHeader, 'Lonati')
            ->getStyle('E' . $endMergHeader)
            ->applyFromArray($styleHeader2);

        $sheet->setCellValue('F' . $endMergHeader, 'Total')
            ->getStyle('F' . $endMergHeader)
            ->applyFromArray($styleHeader2);
        // mesin running
        $sheet->setCellValue('G' . $rowHeader3, 'Running')
            ->mergeCells('G' . $rowHeader3 . ':K' . $rowHeader3)
            ->getStyle('G' . $rowHeader3 . ':K' . $rowHeader3)
            ->applyFromArray($styleHeader2);

        $sheet->setCellValue('G' . $endMergHeader, 'Dakong')
            ->getStyle('G' . $endMergHeader)
            ->applyFromArray($styleHeader2);

        $sheet->setCellValue('H' . $endMergHeader, 'Rosso')
            ->getStyle('H' . $endMergHeader)
            ->applyFromArray($styleHeader2);

        $sheet->setCellValue('I' . $endMergHeader, 'Ths')
            ->getStyle('I' . $endMergHeader)
            ->applyFromArray($styleHeader2);

        $sheet->setCellValue('J' . $endMergHeader, 'Lonati')
            ->getStyle('J' . $endMergHeader)
            ->applyFromArray($styleHeader2);

        $sheet->setCellValue('K' . $endMergHeader, 'Spl')
            ->getStyle('K' . $endMergHeader)
            ->applyFromArray($styleHeader2);

        $sheet->setCellValue('L' . $endMergHeader, 'Total')
            ->getStyle('L' . $endMergHeader)
            ->applyFromArray($styleHeader2);

        // mesin running
        $sheet->setCellValue('M' . $rowHeader3, 'Mc Break Down')
            ->mergeCells('M' . $rowHeader3 . ':M' . $endMergHeader)
            ->getStyle('M' . $rowHeader3 . ':M' . $endMergHeader)
            ->applyFromArray($styleHeader2)
            ->getAlignment()->setWrapText(true); // Menambahkan pengaturan wrap text

        // mesin running
        $sheet->setCellValue('N' . $rowHeader3, 'Mc in Wh')
            ->mergeCells('N' . $rowHeader3 . ':N' . $endMergHeader)
            ->getStyle('N' . $rowHeader3 . ':N' . $endMergHeader)
            ->applyFromArray($styleHeader2)
            ->getAlignment()->setWrapText(true); // Menambahkan pengaturan wrap text
        // stock cylinder
        $sheet->setCellValue('O' . $rowHeader3, 'Stock Cylinder')
            ->mergeCells('O' . $rowHeader3 . ':Q' . $rowHeader3)
            ->getStyle('O' . $rowHeader3 . ':Q' . $rowHeader3)
            ->applyFromArray($styleHeader2);

        $sheet->setCellValue('O' . $endMergHeader, 'Dakong')
            ->getStyle('O' . $endMergHeader)
            ->applyFromArray($styleHeader2);

        $sheet->setCellValue('P' . $endMergHeader, 'Rosso')
            ->getStyle('P' . $endMergHeader)
            ->applyFromArray($styleHeader2);

        $sheet->setCellValue('Q' . $endMergHeader, 'Ths')
            ->getStyle('Q' . $endMergHeader)
            ->applyFromArray($styleHeader2);
        // actual running mc
        $sheet->setCellValue('R' . $rowHeader, 'Running Mc Actual')
            ->mergeCells('R' . $rowHeader . ':T' . $rowHeader3)
            ->getStyle('R' . $rowHeader . ':T' . $rowHeader3)
            ->applyFromArray($styleHeader)
            ->getAlignment()->setWrapText(true); // Menambahkan pengaturan wrap text

        $sheet->setCellValue('R' . $endMergHeader, 'CJ')
            ->getStyle('R' . $endMergHeader)
            ->applyFromArray($styleHeader2);

        $sheet->setCellValue('S' . $endMergHeader, 'MJ')
            ->getStyle('S' . $endMergHeader)
            ->applyFromArray($styleHeader2);

        $sheet->setCellValue('T' . $endMergHeader, 'Total')
            ->getStyle('T' . $endMergHeader)
            ->applyFromArray($styleHeader2);

        $sheet->setCellValue('U' . $rowHeader, 'Prod 28days')
            ->mergeCells('U' . $rowHeader . ':U' . $endMergHeader)
            ->getStyle('U' . $rowHeader . ':U' . $endMergHeader)
            ->applyFromArray($styleHeader)
            ->getAlignment()->setWrapText(true); // Menambahkan pengaturan wrap text


        $column = 'V'; // Kolom awal untuk bulan
        $col_index = Coordinate::columnIndexFromString($column); // Konversi huruf kolom ke nomor indeks kolom
        $column2 = 'V';
        $col_index2 = Coordinate::columnIndexFromString($column2);
        foreach ($monthlyData as $month => $data) :
            // month
            $weekCount = count($data['weeks']); // Hitung jumlah minggu dalam bulan
            $startColumn = $column;
            $endCol_index = $col_index + ($weekCount * 6) - 1; // Dikurangi 1 karena kolom awal tidak terhitung
            $endColumn = Coordinate::stringFromColumnIndex($endCol_index); // Konversi kembali dari nomor indeks kolom ke huruf kolom

            // Merge cells untuk bulan ini sesuai dengan jumlah minggu
            $sheet->setCellValue($startColumn . $rowHeader, $month);
            if ($startColumn !== $endColumn) {
                $sheet->mergeCells($startColumn . $rowHeader . ':' . $endColumn . $rowHeader)
                    ->getStyle($startColumn . $rowHeader . ':' . $endColumn . $rowHeader)
                    ->applyFromArray($styleHeader);
            } else {
                $sheet->getStyle($startColumn . $rowHeader)
                    ->applyFromArray($styleHeader);
            }

            // Pastikan $column2 dan $col_index2 berada di luar loop bulan
            $rowWeek = $rowHeader + 1; // Baris awal untuk week
            foreach ($data['weeks'] as $week) :
                $startDate = $week['start_date'];
                $endDate = $week['end_date'];
                $countWeek = $week['countWeek'];
                $tgl = $startDate . '-' . $endDate . ' (' . $countWeek . ')';
                $rowWh = $rowWeek + 1;

                $startColumn2 = $column2;
                $endCol_index2 = $col_index2 + 5; // Misalnya, 4 kolom untuk setiap minggu
                $endColumn2 = Coordinate::stringFromColumnIndex($endCol_index2);

                // Set value dan merge cells untuk minggu
                $sheet->setCellValue($startColumn2 . $rowWeek, $tgl);
                $sheet->mergeCells($startColumn2 . $rowWeek . ':' . $endColumn2 . $rowWeek)
                    ->getStyle($startColumn2 . $rowWeek . ':' . $endColumn2 . $rowWeek)
                    ->applyFromArray($styleHeader2);

                // Set value dan merge cells untuk hari kerja
                $sheet->setCellValue($startColumn2 . $rowWh, $week['number_of_days'] . ' hari');
                $sheet->mergeCells($startColumn2 . $rowWh . ':' . $endColumn2 . $rowWh)
                    ->getStyle($startColumn2 . $rowWh . ':' . $endColumn2 . $rowWh)
                    ->applyFromArray($styleHeader2);

                $rowTitle = $rowWh + 1;
                foreach ($data['weeks'] as $week) :
                    $sheet->setCellValue($startColumn2 . $rowTitle, 'Max Capacity')
                        ->getStyle($startColumn2 . $rowTitle)
                        ->applyFromArray($styleHeader2);
                    // 
                    $max_index = $col_index2 + 1; // Next Column
                    $nextColumn = Coordinate::stringFromColumnIndex($max_index);
                    // 
                    $sheet->setCellValue($nextColumn . $rowTitle, 'Confirm Order')
                        ->getStyle($nextColumn . $rowTitle)
                        ->applyFromArray($styleHeader2);
                    // 
                    $confirm_index = $max_index + 1; // Next Column
                    $nextColumn = Coordinate::stringFromColumnIndex($confirm_index);
                    // 
                    $sheet->setCellValue($nextColumn . $rowTitle, 'Sisa Order')
                        ->getStyle($nextColumn . $rowTitle)
                        ->applyFromArray($styleHeader2);
                    // 
                    $order_index = $confirm_index + 1; // Next Column
                    $nextColumn = Coordinate::stringFromColumnIndex($order_index);
                    // 
                    $sheet->setCellValue($nextColumn . $rowTitle, 'Sisa Booking')
                        ->getStyle($nextColumn . $rowTitle)
                        ->applyFromArray($styleHeader2);
                    // 
                    $booking_index = $order_index + 1; // Next Column
                    $nextColumn = Coordinate::stringFromColumnIndex($booking_index);
                    // 
                    $sheet->setCellValue($nextColumn . $rowTitle, '(+)Exess')
                        ->getStyle($nextColumn . $rowTitle)
                        ->applyFromArray($styleHeader2);
                    // 
                    $exess_index = $booking_index + 1; // Next Column
                    $nextColumn = Coordinate::stringFromColumnIndex($exess_index);
                    // 

                    $sheet->setCellValue($nextColumn . $rowTitle, '%')
                        ->getStyle($nextColumn . $rowTitle)
                        ->applyFromArray($styleHeader2);
                    // 
                    $exessPercentage_index = $booking_index + 1; // Next Column
                    $startColumn2 = Coordinate::stringFromColumnIndex($exessPercentage_index);
                endforeach;
                // Update ke indeks kolom berikutnya
                $col_index2 = $endCol_index2 + 1;
                $column2 = Coordinate::stringFromColumnIndex($col_index2);
            endforeach;
            // Pindah ke kolom berikutnya setelah melakukan merge
            $col_index = $endCol_index + 1; // Update ke indeks kolom berikutnya
            $column = Coordinate::stringFromColumnIndex($col_index); // Konversi kembali ke huruf kolom
        endforeach;

        // Body
        $rowBody = 7;
        foreach ($allData as $aliasjarum => $data) {
            $sheet->setCellValue('A' . $rowBody, $aliasjarum)
                ->getStyle('A' . $rowBody);
            $sheet->setCellValue('B' . $rowBody, isset($data['brandData']['Dakong']['totalCj']) ? $data['brandData']['Dakong']['totalCj'] : 0)
                ->getStyle('B' . $rowBody)
                ->applyFromArray($styleBody);
            $sheet->setCellValue('C' . $rowBody, isset($data['brandData']['Rosso']['totalCj']) ? $data['brandData']['Rosso']['totalCj'] : 0)
                ->getStyle('C' . $rowBody)
                ->applyFromArray($styleBody);
            $sheet->setCellValue('D' . $rowBody, isset($data['brandData']['Mechanic']['totalCj']) ? $data['brandData']['Mechanic']['totalCj'] : 0)
                ->getStyle('D' . $rowBody)
                ->applyFromArray($styleBody);
            $sheet->setCellValue('E' . $rowBody, isset($data['brandData']['Lonati']['totalCj']) ? $data['brandData']['Lonati']['totalCj'] : 0)
                ->getStyle('E' . $rowBody)
                ->applyFromArray($styleBody);
            $sheet->setCellValue('F' . $rowBody, isset($data['totals']['totalCj']) ? $data['totals']['totalCj'] : 0)
                ->getStyle('F' . $rowBody)
                ->applyFromArray($styleBody);
            // running mc by system
            $sheet->setCellValue('G' . $rowBody, isset($data['brandData']['Dakong']['totalRunning']) ? $data['brandData']['Dakong']['totalRunning'] : 0)
                ->getStyle('G' . $rowBody)
                ->applyFromArray($styleBody);
            $sheet->setCellValue('H' . $rowBody, isset($data['brandData']['Rosso']['totalRunning']) ? $data['brandData']['Rosso']['totalRunning'] : 0)
                ->getStyle('H' . $rowBody)
                ->applyFromArray($styleBody);
            $sheet->setCellValue('I' . $rowBody, isset($data['brandData']['Mechanic']['totalRunning']) ? $data['brandData']['Mechanic']['totalRunning'] : 0)
                ->getStyle('I' . $rowBody)
                ->applyFromArray($styleBody);
            $sheet->setCellValue('J' . $rowBody, isset($data['brandData']['Lonati']['totalRunning']) ? $data['brandData']['Lonati']['totalRunning'] : 0)
                ->getStyle('J' . $rowBody)
                ->applyFromArray($styleBody);
            $sheet->setCellValue('K' . $rowBody, isset($data['totals']['totalSample']) ? $data['totals']['totalSample'] : 0)
                ->getStyle('K' . $rowBody)
                ->applyFromArray($styleBody);
            $sheet->setCellValue('L' . $rowBody, isset($data['totals']['totalRunning']) && isset($data['totals']['totalSample']) ? $data['totals']['totalRunning'] + $data['totals']['totalSample'] : 0)
                ->getStyle('L' . $rowBody)
                ->applyFromArray($styleBody);
            // mc break down
            $sheet->setCellValue('M' . $rowBody, isset($data['totals']['totalBreakdown']) ? $data['totals']['totalBreakdown'] : 0)
                ->getStyle('M' . $rowBody)
                ->applyFromArray($styleBody);
            // mc in wh
            $sheet->setCellValue('N' . $rowBody, isset($data['totals']['totalWarehouse']) ? $data['totals']['totalWarehouse'] : 0)
                ->getStyle('N' . $rowBody)
                ->applyFromArray($styleBody);
            // stock cylinder
            $sheet->setCellValue('O' . $rowBody, isset($data['totals']['totalCylinderDk']) ? $data['totals']['totalCylinderDk'] : 0)
                ->getStyle('O' . $rowBody)
                ->applyFromArray($styleBody);
            $sheet->setCellValue('P' . $rowBody, isset($data['totals']['totalCylinderThs']) ? $data['totals']['totalCylinderThs'] : 0)
                ->getStyle('P' . $rowBody)
                ->applyFromArray($styleBody);
            $sheet->setCellValue('Q' . $rowBody, isset($data['totals']['totalCylinderRs']) ? $data['totals']['totalCylinderRs'] : 0)
                ->getStyle('Q' . $rowBody)
                ->applyFromArray($styleBody);
            // running mc actual
            $sheet->setCellValue('R' . $rowBody, isset($data['totals']['totalRunningAct']) ? $data['totals']['totalRunningAct'] : 0)
                ->getStyle('R' . $rowBody)
                ->applyFromArray($styleBody);
            $sheet->setCellValue('S' . $rowBody, 0)
                ->getStyle('S' . $rowBody)
                ->applyFromArray($styleBody);
            $sheet->setCellValue('T' . $rowBody, isset($data['totals']['totalRunningAct']) ? $data['totals']['totalRunningAct'] : 0)
                ->getStyle('T' . $rowBody)
                ->applyFromArray($styleBody);
            $sheet->setCellValue('U' . $rowBody, isset($data['totals']['totalProd']) ? $data['totals']['totalProd'] : 0)
                ->getStyle('U' . $rowBody)
                ->applyFromArray($styleBody);

            $columnBody = 'V'; // Kolom awal 
            $colBody_index = Coordinate::columnIndexFromString($columnBody); // Konversi huruf kolom ke nomor indeks kolom
            if (isset($data['monthlyData']) && is_array($data['monthlyData'])) {
                foreach ($data['monthlyData'] as $month => $currentMonth) :
                    $startColumnBody = $columnBody; // Kolom awal 
                    foreach ($currentMonth['weeks'] as $week) :
                        $sheet->setCellValue($startColumnBody . $rowBody, isset($week['maxCapacity']) ? $week['maxCapacity'] : 0)
                            ->getStyle($startColumnBody . $rowBody)
                            ->applyFromArray($styleBody);
                        $colBody_index++;
                        $nextColumnBody = Coordinate::stringFromColumnIndex($colBody_index); // Konversi 

                        $sheet->setCellValue($nextColumnBody . $rowBody, isset($week['ConfirmOrder']) ? $week['ConfirmOrder'] : 0)
                            ->getStyle($nextColumnBody . $rowBody)
                            ->applyFromArray($styleBody);
                        $colBody_index++;
                        $nextColumnBody = Coordinate::stringFromColumnIndex($colBody_index); // Konversi 

                        $sheet->setCellValue($nextColumnBody . $rowBody, isset($week['sisaConfirmOrder']) ? $week['sisaConfirmOrder'] : 0)
                            ->getStyle($nextColumnBody . $rowBody)
                            ->applyFromArray($styleBody);
                        $colBody_index++;
                        $nextColumnBody = Coordinate::stringFromColumnIndex($colBody_index); // Konversi 

                        $sheet->setCellValue($nextColumnBody . $rowBody, isset($week['sisaBooking']) ? $week['sisaBooking'] : 0)
                            ->getStyle($nextColumnBody . $rowBody)
                            ->applyFromArray($styleBody);
                        $colBody_index++;
                        $nextColumnBody = Coordinate::stringFromColumnIndex($colBody_index); // Konversi 

                        $sheet->setCellValue($nextColumnBody . $rowBody, isset($week['exess']) ? $week['exess'] : 0)
                            ->getStyle($nextColumnBody . $rowBody)
                            ->applyFromArray($styleBody);
                        $colBody_index++;
                        $nextColumnBody = Coordinate::stringFromColumnIndex($colBody_index); // Konversi 

                        $sheet->setCellValue($nextColumnBody . $rowBody, isset($week['exessPercentage']) ? $week['exessPercentage'] . '%' : 0)
                            ->getStyle($nextColumnBody . $rowBody)
                            ->applyFromArray($styleBody);

                        $colBody_index++;
                        $startColumnBody = Coordinate::stringFromColumnIndex($colBody_index); // Konversi 
                    endforeach;
                    $columnBody = Coordinate::stringFromColumnIndex($colBody_index); // Konversi 
                    // style kolom body
                    $sheet->getStyle('A' . $rowBody)->applyFromArray([
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_LEFT, // Alignment rata tengah
                            'vertical' => Alignment::VERTICAL_CENTER, // Alignment rata tengah
                        ],
                        'borders' => [
                            'outline' => [
                                'borderStyle' => Border::BORDER_THIN, // Gaya garis tipis
                                'color' => ['argb' => 'FF000000'],    // Warna garis hitam
                            ],
                        ],
                    ]);
                endforeach;
                $rowBody++;
            }
        }
        $rowSubtotal = $rowBody;
        // kolom subtotal di mulai
        $sheet->setCellValue('A' . $rowBody, 'Sub Total')
            ->getStyle('A' . $rowBody . ':A' . $rowBody)
            ->applyFromArray($styleSubTotal);
        $sumRowAwal = $rowTitle + 1;
        $sumRowAkhir = $rowBody - 1;
        // Kolom subtotal mc
        $sheet->setCellValue('B' . $rowSubtotal, "=SUM(B" . $sumRowAwal . ":B" . $sumRowAkhir . ")")
            ->getStyle('B' . $rowSubtotal)
            ->applyFromArray($styleSubTotal);
        $sheet->setCellValue('C' . $rowSubtotal, "=SUM(C" . $sumRowAwal . ":C" . $sumRowAkhir . ")")
            ->getStyle('C' . $rowSubtotal)
            ->applyFromArray($styleSubTotal);
        $sheet->setCellValue('D' . $rowSubtotal, "=SUM(D" . $sumRowAwal . ":D" . $sumRowAkhir . ")")
            ->getStyle('D' . $rowSubtotal)
            ->applyFromArray($styleSubTotal);
        $sheet->setCellValue('E' . $rowSubtotal, "=SUM(E" . $sumRowAwal . ":E" . $sumRowAkhir . ")")
            ->getStyle('E' . $rowSubtotal)
            ->applyFromArray($styleSubTotal);
        $sheet->setCellValue('F' . $rowSubtotal, "=SUM(F" . $sumRowAwal . ":F" . $sumRowAkhir . ")")
            ->getStyle('F' . $rowSubtotal)
            ->applyFromArray($styleSubTotal);
        // Kolom subtotal running
        $sheet->setCellValue('G' . $rowSubtotal, "=SUM(G" . $sumRowAwal . ":G" . $sumRowAkhir . ")")
            ->getStyle('G' . $rowSubtotal)
            ->applyFromArray($styleSubTotal);
        $sheet->setCellValue('H' . $rowSubtotal, "=SUM(H" . $sumRowAwal . ":H" . $sumRowAkhir . ")")
            ->getStyle('H' . $rowSubtotal)
            ->applyFromArray($styleSubTotal);
        $sheet->setCellValue('I' . $rowSubtotal, "=SUM(I" . $sumRowAwal . ":I" . $sumRowAkhir . ")")
            ->getStyle('I' . $rowSubtotal)
            ->applyFromArray($styleSubTotal);
        $sheet->setCellValue('J' . $rowSubtotal, "=SUM(J" . $sumRowAwal . ":J" . $sumRowAkhir . ")")
            ->getStyle('J' . $rowSubtotal)
            ->applyFromArray($styleSubTotal);
        $sheet->setCellValue('K' . $rowSubtotal, "=SUM(K" . $sumRowAwal . ":K" . $sumRowAkhir . ")")
            ->getStyle('K' . $rowSubtotal)
            ->applyFromArray($styleSubTotal);
        $sheet->setCellValue('L' . $rowSubtotal, "=SUM(L" . $sumRowAwal . ":L" . $sumRowAkhir . ")")
            ->getStyle('L' . $rowSubtotal)
            ->applyFromArray($styleSubTotal);
        // Kolom subtotal Mc Breakdown
        $sheet->setCellValue('M' . $rowSubtotal, "=SUM(M" . $sumRowAwal . ":M" . $sumRowAkhir . ")")
            ->getStyle('M' . $rowSubtotal)
            ->applyFromArray($styleSubTotal);
        $sheet->setCellValue('N' . $rowSubtotal, "=SUM(N" . $sumRowAwal . ":N" . $sumRowAkhir . ")")
            ->getStyle('N' . $rowSubtotal)
            ->applyFromArray($styleSubTotal);
        // Kolom subtotal Stock Cylinder
        $sheet->setCellValue('O' . $rowSubtotal, "=SUM(O" . $sumRowAwal . ":O" . $sumRowAkhir . ")")
            ->getStyle('O' . $rowSubtotal)
            ->applyFromArray($styleSubTotal);
        $sheet->setCellValue('P' . $rowSubtotal, "=SUM(P" . $sumRowAwal . ":P" . $sumRowAkhir . ")")
            ->getStyle('P' . $rowSubtotal)
            ->applyFromArray($styleSubTotal);
        $sheet->setCellValue('Q' . $rowSubtotal, "=SUM(Q" . $sumRowAwal . ":Q" . $sumRowAkhir . ")")
            ->getStyle('Q' . $rowSubtotal)
            ->applyFromArray($styleSubTotal);
        // Kolom subtotal Running actual mc
        $sheet->setCellValue('R' . $rowSubtotal, "=SUM(R" . $sumRowAwal . ":R" . $sumRowAkhir . ")")
            ->getStyle('R' . $rowSubtotal)
            ->applyFromArray($styleSubTotal);
        $sheet->setCellValue('S' . $rowSubtotal, "=SUM(S" . $sumRowAwal . ":S" . $sumRowAkhir . ")")
            ->getStyle('S' . $rowSubtotal)
            ->applyFromArray($styleSubTotal);
        $sheet->setCellValue('T' . $rowSubtotal, "=SUM(T" . $sumRowAwal . ":T" . $sumRowAkhir . ")")
            ->getStyle('T' . $rowSubtotal)
            ->applyFromArray($styleSubTotal);
        // Kolom subtotal Prod 28days
        $sheet->setCellValue('U' . $rowSubtotal, "=SUM(U" . $sumRowAwal . ":U" . $sumRowAkhir . ")")
            ->getStyle('U' . $rowSubtotal)
            ->applyFromArray($styleSubTotal);

        foreach ($allData as $aliasjarum => $data) {
            $columnBody = 'V'; // Kolom awal 
            $colBody_index = Coordinate::columnIndexFromString($columnBody); // Konversi huruf kolom ke nomor indeks kolom
            if (isset($data['monthlyData']) && is_array($data['monthlyData'])) {
                foreach ($data['monthlyData'] as $month => $currentMonth) :
                    $startColumnBody = $columnBody; // Kolom awal 
                    foreach ($currentMonth['weeks'] as $week) :
                        // Kolom subtotal Max Capacity
                        $sheet->setCellValue($startColumnBody . $rowSubtotal, "=SUM(" . $startColumnBody . $sumRowAwal . ":" . $startColumnBody . $sumRowAkhir . ")")
                            ->getStyle($startColumnBody . $rowSubtotal)
                            ->applyFromArray($styleSubTotal);
                        $colBody_index++;
                        $nextColumnBody = Coordinate::stringFromColumnIndex($colBody_index); // Konversi 

                        // Kolom subtotal Confirm Order
                        $sheet->setCellValue($nextColumnBody . $rowSubtotal, "=SUM(" . $nextColumnBody . $sumRowAwal . ":" . $nextColumnBody . $sumRowAkhir . ")")
                            ->getStyle($nextColumnBody . $rowSubtotal)
                            ->applyFromArray($styleSubTotal);
                        $colBody_index++;
                        $nextColumnBody = Coordinate::stringFromColumnIndex($colBody_index); // Konversi 
                        // Kolom subtotal Sisa Order
                        $sheet->setCellValue($nextColumnBody . $rowSubtotal, "=SUM(" . $nextColumnBody . $sumRowAwal . ":" . $nextColumnBody . $sumRowAkhir . ")")
                            ->getStyle($nextColumnBody . $rowSubtotal)
                            ->applyFromArray($styleSubTotal);
                        $colBody_index++;
                        $nextColumnBody = Coordinate::stringFromColumnIndex($colBody_index); // Konversi 
                        // Kolom subtotal Sisa Booking
                        $sheet->setCellValue($nextColumnBody . $rowSubtotal, "=SUM(" . $nextColumnBody . $sumRowAwal . ":" . $nextColumnBody . $sumRowAkhir . ")")
                            ->getStyle($nextColumnBody . $rowSubtotal)
                            ->applyFromArray($styleSubTotal);
                        $colBody_index++;
                        $nextColumnBody = Coordinate::stringFromColumnIndex($colBody_index); // Konversi 
                        // Kolom subtotal (+)Exess
                        $sheet->setCellValue($nextColumnBody . $rowSubtotal, "=SUM(" . $nextColumnBody . $sumRowAwal . ":" . $nextColumnBody . $sumRowAkhir . ")")
                            ->getStyle($nextColumnBody . $rowSubtotal)
                            ->applyFromArray($styleSubTotal);
                        $colBody_index++;
                        $nextColumnBody = Coordinate::stringFromColumnIndex($colBody_index); // Konversi 
                        // Kolom subtotal (+)Exess%
                        //ambil kolom total exess dan max capacity
                        $colSubtotalExess_index = $colBody_index - 1;
                        $colSubtotalExess = Coordinate::stringFromColumnIndex($colSubtotalExess_index); // Konversi
                        $colSubtotalMaxCapacity_index = $colBody_index - 5;
                        $colSubtotalMaxCapacity = Coordinate::stringFromColumnIndex($colSubtotalMaxCapacity_index); // Konversi
                        // persentase exess
                        $sheet->setCellValue($nextColumnBody . $rowSubtotal, "=(" . $colSubtotalExess . $rowBody . "/" . $colSubtotalMaxCapacity . $rowBody . ")")
                            ->getStyle($nextColumnBody . $rowSubtotal)
                            ->applyFromArray($styleSubTotal);
                        // Set format kolom menjadi persentase 
                        $sheet->getStyle($nextColumnBody . $rowSubtotal)
                            ->getNumberFormat()
                            ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE);

                        $colBody_index++;
                        $startColumnBody = Coordinate::stringFromColumnIndex($colBody_index); // Konversi 
                    endforeach;
                    $columnBody = Coordinate::stringFromColumnIndex($colBody_index); // Konversi 
                endforeach;
            }
        }
        $rowSubtotal++;

        $rowGrandtotal = $rowSubtotal;
        // baris grand total per bulan
        $column = 'V'; // Kolom awal untuk bulan
        $col_index = Coordinate::columnIndexFromString($column); // Konversi huruf kolom ke nomor indeks kolom
        $columnGrandTotal = 'V'; // Kolom awal untuk bulan
        $colGrandTotalMaxCap = Coordinate::columnIndexFromString($columnGrandTotal); // Konversi huruf kolom ke nomor indeks kolom
        $colGrandTotalConfirm = Coordinate::columnIndexFromString($columnGrandTotal) + 1; // Konversi huruf kolom ke nomor indeks kolom
        $colGrandTotalSisaOrder = Coordinate::columnIndexFromString($columnGrandTotal) + 2; // Konversi huruf kolom ke nomor indeks kolom
        $colGrandTotalSisaBooking = Coordinate::columnIndexFromString($columnGrandTotal) + 3; // Konversi huruf kolom ke nomor indeks kolom
        $colGrandTotalExess = Coordinate::columnIndexFromString($columnGrandTotal) + 4; // Konversi huruf kolom ke nomor indeks kolom
        $colGrandTotalPersentaseExess = Coordinate::columnIndexFromString($columnGrandTotal) + 5; // Konversi huruf kolom ke nomor indeks kolom
        $thisMonthExport = (new \DateTime())->format('F-Y');
        $nextMonthExport = (new \DateTime('first day of next month'))->format('F-Y');

        foreach ($monthlyData as $month => $data) :
            // month
            $weekCount = count($data['weeks']); // Hitung jumlah minggu dalam bulan
            $startColumn = $column;
            $endCol_index = $col_index + ($weekCount * 6) - 1; // Dikurangi 1 karena kolom awal tidak terhitung
            $endColumn = Coordinate::stringFromColumnIndex($endCol_index); // Konversi kembali dari nomor indeks kolom ke huruf kolom
            $endColTitle_index = $col_index + 1; // untuk title grandTotal
            $endColumnTitle = Coordinate::stringFromColumnIndex($endColTitle_index); // Konversi kembali dari nomor indeks kolom ke huruf kolom

            $startColGrandtotal_index = $endColTitle_index + 1; // Konversi kembali dari nomor indeks kolom ke huruf kolom
            $startColumnGrandtotal = Coordinate::stringFromColumnIndex($startColGrandtotal_index); // untuk start isi grandTotal
            $endColGrandtotal_index = $startColGrandtotal_index + 1; // Konversi kembali dari nomor indeks kolom ke huruf kolom
            $endColumnGrandtotal = Coordinate::stringFromColumnIndex($endColGrandtotal_index); // untuk end isi grandTotal

            $startTitleGrandExport_index = $endColGrandtotal_index + 1; // Konversi kembali dari nomor indeks kolom ke huruf kolom
            $startTitleGrandExport = Coordinate::stringFromColumnIndex($startTitleGrandExport_index); // untuk start title grandExport
            $endTitleGrandExport_index = $startTitleGrandExport_index + 1; // Konversi kembali dari nomor indeks kolom ke huruf kolom
            $endTitleGrandExport = Coordinate::stringFromColumnIndex($endTitleGrandExport_index); // untuk end title grandExport

            $startColTitleGrandExport_index = $endTitleGrandExport_index + 1; // Konversi kembali dari nomor indeks kolom ke huruf kolom
            $startColumnTitleGrandExport = Coordinate::stringFromColumnIndex($startColTitleGrandExport_index); // untuk start isi grandExport

            $totalWeeks = 0; // Inisialisasi total minggu
            // untuk menghitung total minggu
            if (isset($data['weeks']) && is_array($data['weeks'])) {
                // Hitung jumlah minggu untuk bulan ini
                $totalWeeks = count($data['weeks']);
            }
            $rowTotal = $rowGrandtotal - 1;

            // Merge cells untuk bulan ini sesuai dengan jumlah minggu
            $sheet->setCellValue($startColumn . $rowGrandtotal, "Total " . $month);
            if ($startColumn !== $endColumn) {
                $sheet->mergeCells($startColumn . $rowGrandtotal . ':' . $endColumn . $rowGrandtotal)
                    ->getStyle($startColumn . $rowGrandtotal . ':' . $endColumn . $rowGrandtotal)
                    ->applyFromArray($styleGrandTotal);
            } else {
                $sheet->getStyle($startColumn . $rowHeader)
                    ->applyFromArray($styleGrandTotal);
            }
            $rowGrandtotal++;

            // Grand total Max Capacity Per bulan
            $sheet->setCellValue($startColumn . $rowGrandtotal, "Max Capacity")
                ->mergeCells($startColumn . $rowGrandtotal . ':' . $endColumnTitle . $rowGrandtotal)
                ->getStyle($startColumn . $rowGrandtotal . ':' . $endColumnTitle . $rowGrandtotal)
                ->applyFromArray($styleGrandTotal);


            // Buat formula berdasarkan total minggu
            $grandTotalMaxCap = "=SUM(";
            $grandTotalPartsMaxCap = []; // Untuk menyimpan tiap bagian formula SUM

            for ($week = 0; $week < $totalWeeks; $week++) {
                // Kolom awal setiap bulan dimulai dari index yang sesuai
                $currentColumnIndex = $colGrandTotalMaxCap + ($week * 6); // Kolom pertama tiap minggu
                $currentColumn = Coordinate::stringFromColumnIndex($currentColumnIndex); // Mengubah indeks kolom jadi string

                // Tambahkan kolom ke formula hanya untuk minggu yang relevan
                $grandTotalPartsMaxCap[] = "{$currentColumn}{$rowTotal}";
            }

            // Gabungkan bagian formula dengan koma
            $grandTotalMaxCap .= implode(', ', $grandTotalPartsMaxCap) . ")";

            // Update kolom untuk bulan berikutnya
            $colGrandTotalMaxCap = $currentColumnIndex + 6; // Kolom berikutnya untuk loop bulan berikutnya

            $sheet->setCellValue($startColumnGrandtotal . $rowGrandtotal, $grandTotalMaxCap);
            $sheet->mergeCells($startColumnGrandtotal . $rowGrandtotal . ':' . $endColumn . $rowGrandtotal)
                ->getStyle($startColumnGrandtotal . $rowGrandtotal . ':' . $endColumn . $rowGrandtotal)
                ->applyFromArray($styleGrandTotal);

            $startColGrandtotal_index = $currentColumnIndex + 6; // Kolom berikutnya untuk loop bulan berikutnya

            $rowGrandtotal++; // Pindah ke baris berikutnya jika ingin menyusun hasil di baris yang berbeda

            // Grand total Confirm Order Per bulan
            $sheet->setCellValue($startColumn . $rowGrandtotal, "Confirm Order")
                ->mergeCells($startColumn . $rowGrandtotal . ':' . $endColumnTitle . $rowGrandtotal)
                ->getStyle($startColumn . $rowGrandtotal . ':' . $endColumnTitle . $rowGrandtotal)
                ->applyFromArray($styleGrandTotal);

            // Buat formula berdasarkan total minggu
            $grandTotalConfirm = "=SUM(";
            $grandTotalPartsConfirm = []; // Untuk menyimpan tiap bagian formula SUM

            for ($week = 0; $week < $totalWeeks; $week++) {
                // Kolom awal setiap bulan dimulai dari index yang sesuai
                $currentColumnIndex = $colGrandTotalConfirm + ($week * 6); // Kolom pertama tiap minggu +1 supaya di mulai dari kolom u
                $currentColumn = Coordinate::stringFromColumnIndex($currentColumnIndex); // Mengubah indeks kolom jadi string

                // Tambahkan kolom ke formula hanya untuk minggu yang relevan
                $grandTotalPartsConfirm[] = "{$currentColumn}{$rowTotal}";
            }

            // Gabungkan bagian formula dengan koma
            $grandTotalConfirm .= implode(', ', $grandTotalPartsConfirm) . ")";

            // Update kolom untuk bulan berikutnya
            $colGrandTotalConfirm = $currentColumnIndex + 6; // Kolom berikutnya untuk loop bulan berikutnya

            $sheet->setCellValue($startColumnGrandtotal . $rowGrandtotal, $grandTotalConfirm);
            $sheet->mergeCells($startColumnGrandtotal . $rowGrandtotal . ':' . $endColumnGrandtotal . $rowGrandtotal)
                ->getStyle($startColumnGrandtotal . $rowGrandtotal . ':' . $endColumnGrandtotal . $rowGrandtotal)
                ->applyFromArray($styleGrandTotal);

            $sheet->setCellValue($startTitleGrandExport . $rowGrandtotal, "Total Export");
            $sheet->mergeCells($startTitleGrandExport . $rowGrandtotal . ':' . $endTitleGrandExport . $rowGrandtotal)
                ->getStyle($startTitleGrandExport . $rowGrandtotal . ':' . $endTitleGrandExport . $rowGrandtotal)
                ->applyFromArray($styleGrandTotal);
            // Cek apakah ini bulan pertama atau kedua
            if ($month == $thisMonthExport || $month == $nextMonthExport) {
                // Kosongkan nilai untuk bulan pertama dan kedua
                $sheet->setCellValue($startColumnTitleGrandExport . $rowGrandtotal, $allData['total']['targetExport'][$month]); // Mengosongkan nilai
            } else {
                // Untuk bulan ketiga dan seterusnya
                $sheet->setCellValue($startColumnTitleGrandExport . $rowGrandtotal, $allData['total']['totalExport'][$month]);
            }
            $sheet->mergeCells($startColumnTitleGrandExport . $rowGrandtotal . ':' . $endColumn . $rowGrandtotal)
                ->getStyle($startColumnTitleGrandExport . $rowGrandtotal . ':' . $endColumn . $rowGrandtotal)
                ->applyFromArray($styleGrandTotal);

            $startColGrandtotal_index = $currentColumnIndex + 6; // Kolom berikutnya untuk loop bulan berikutnya

            $rowGrandtotal++;

            // Grand total Sisa Order perbulan
            $sheet->setCellValue($startColumn . $rowGrandtotal, "Sisa Order")
                ->mergeCells($startColumn . $rowGrandtotal . ':' . $endColumnTitle . $rowGrandtotal)
                ->getStyle($startColumn . $rowGrandtotal . ':' . $endColumnTitle . $rowGrandtotal)
                ->applyFromArray($styleGrandTotal);

            // Buat formula berdasarkan total minggu
            $grandTotalSisaOrder = "=SUM(";
            $grandTotalPartsSisaOrder = []; // Untuk menyimpan tiap bagian formula SUM

            for ($week = 0; $week < $totalWeeks; $week++) {
                // Kolom awal setiap bulan dimulai dari index yang sesuai
                $currentColumnIndex = $colGrandTotalSisaOrder + ($week * 6); // Kolom pertama tiap minggu +1 supaya di mulai dari kolom u
                $currentColumn = Coordinate::stringFromColumnIndex($currentColumnIndex); // Mengubah indeks kolom jadi string

                // Tambahkan kolom ke formula hanya untuk minggu yang relevan
                $grandTotalPartsSisaOrder[] = "{$currentColumn}{$rowTotal}";
            }

            // Gabungkan bagian formula dengan koma
            $grandTotalSisaOrder .= implode(', ', $grandTotalPartsSisaOrder) . ")";

            // Update kolom untuk bulan berikutnya
            $colGrandTotalSisaOrder = $currentColumnIndex + 6; // Kolom berikutnya untuk loop bulan berikutnya

            $sheet->setCellValue($startColumnGrandtotal . $rowGrandtotal, $grandTotalSisaOrder);
            $sheet->mergeCells($startColumnGrandtotal . $rowGrandtotal . ':' . $endColumnGrandtotal . $rowGrandtotal)
                ->getStyle($startColumnGrandtotal . $rowGrandtotal . ':' . $endColumnGrandtotal . $rowGrandtotal)
                ->applyFromArray($styleGrandTotal);

            $sheet->setCellValue($startTitleGrandExport . $rowGrandtotal, "Total Inspect");
            $sheet->mergeCells($startTitleGrandExport . $rowGrandtotal . ':' . $endTitleGrandExport . $rowGrandtotal)
                ->getStyle($startTitleGrandExport . $rowGrandtotal . ':' . $endTitleGrandExport . $rowGrandtotal)
                ->applyFromArray($styleGrandTotal);

            $colConfirm_index = $startColTitleGrandExport_index - 4;
            $colConfirm = Coordinate::stringFromColumnIndex($colConfirm_index);
            $rowConfirm_Export = $rowGrandtotal - 1;
            $rowLokal = $rowGrandtotal + 1;
            $sheet->setCellValue($startColumnTitleGrandExport . $rowGrandtotal, "=" . $colConfirm . $rowConfirm_Export . "-" . $startColumnTitleGrandExport . $rowConfirm_Export . "-" . $startColumnTitleGrandExport . $rowLokal); // Confirm Order - Export -Lokal
            $sheet->mergeCells($startColumnTitleGrandExport . $rowGrandtotal . ':' . $endColumn . $rowGrandtotal)
                ->getStyle($startColumnTitleGrandExport . $rowGrandtotal . ':' . $endColumn . $rowGrandtotal)
                ->applyFromArray($styleGrandTotal);

            $startColGrandtotal_index = $currentColumnIndex + 6; // Kolom berikutnya untuk loop bulan berikutnya

            $rowGrandtotal++;

            // Grand Total Sisa Booking perbulan
            $sheet->setCellValue($startColumn . $rowGrandtotal, "Sisa Booking")
                ->mergeCells($startColumn . $rowGrandtotal . ':' . $endColumnTitle . $rowGrandtotal)
                ->getStyle($startColumn . $rowGrandtotal . ':' . $endColumnTitle . $rowGrandtotal)
                ->applyFromArray($styleGrandTotal);

            // Buat formula berdasarkan total minggu
            $grandTotalSisaBooking = "=SUM(";
            $grandTotalPartsSisaBooking = []; // Untuk menyimpan tiap bagian formula SUM

            for ($week = 0; $week < $totalWeeks; $week++) {
                // Kolom awal setiap bulan dimulai dari index yang sesuai
                $currentColumnIndex = $colGrandTotalSisaBooking + ($week * 6); // Kolom pertama tiap minggu +1 supaya di mulai dari kolom u
                $currentColumn = Coordinate::stringFromColumnIndex($currentColumnIndex); // Mengubah indeks kolom jadi string

                // Tambahkan kolom ke formula hanya untuk minggu yang relevan
                $grandTotalPartsSisaBooking[] = "{$currentColumn}{$rowTotal}";
            }

            // Gabungkan bagian formula dengan koma
            $grandTotalSisaBooking .= implode(', ', $grandTotalPartsSisaBooking) . ")";

            // Update kolom untuk bulan berikutnya
            $colGrandTotalSisaBooking = $currentColumnIndex + 6; // Kolom berikutnya untuk loop bulan berikutnya

            $sheet->setCellValue($startColumnGrandtotal . $rowGrandtotal, $grandTotalSisaBooking);
            $sheet->mergeCells($startColumnGrandtotal . $rowGrandtotal . ':' . $endColumnGrandtotal . $rowGrandtotal)
                ->getStyle($startColumnGrandtotal . $rowGrandtotal . ':' . $endColumnGrandtotal . $rowGrandtotal)
                ->applyFromArray($styleGrandTotal);

            $sheet->setCellValue($startTitleGrandExport . $rowGrandtotal, "Total Lokal");
            $sheet->mergeCells($startTitleGrandExport . $rowGrandtotal . ':' . $endTitleGrandExport . $rowGrandtotal)
                ->getStyle($startTitleGrandExport . $rowGrandtotal . ':' . $endTitleGrandExport . $rowGrandtotal)
                ->applyFromArray($styleGrandTotal);

            $sheet->setCellValue($startColumnTitleGrandExport . $rowGrandtotal, $allData['total']['totalLokal'][$month]);
            $sheet->mergeCells($startColumnTitleGrandExport . $rowGrandtotal . ':' . $endColumn . $rowGrandtotal)
                ->getStyle($startColumnTitleGrandExport . $rowGrandtotal . ':' . $endColumn . $rowGrandtotal)
                ->applyFromArray($styleGrandTotal);


            $startColGrandtotal_index = $currentColumnIndex + 6; // Kolom berikutnya untuk loop bulan berikutnya

            $rowGrandtotal++;

            // Grand Total Exess perbulan
            $sheet->setCellValue($startColumn . $rowGrandtotal, "(+)Exess")
                ->mergeCells($startColumn . $rowGrandtotal . ':' . $endColumnTitle . $rowGrandtotal)
                ->getStyle($startColumn . $rowGrandtotal . ':' . $endColumnTitle . $rowGrandtotal)
                ->applyFromArray($styleGrandTotal);

            // Buat formula berdasarkan total minggu
            $grandTotalExess = "=SUM(";
            $grandTotalPartsExess = []; // Untuk menyimpan tiap bagian formula SUM

            for ($week = 0; $week < $totalWeeks; $week++) {
                // Kolom awal setiap bulan dimulai dari index yang sesuai
                $currentColumnIndex = $colGrandTotalExess + ($week * 6); // Kolom pertama tiap minggu +1 supaya di mulai dari kolom u
                $currentColumn = Coordinate::stringFromColumnIndex($currentColumnIndex); // Mengubah indeks kolom jadi string

                // Tambahkan kolom ke formula hanya untuk minggu yang relevan
                $grandTotalPartsExess[] = "{$currentColumn}{$rowTotal}";
            }

            // Gabungkan bagian formula dengan koma
            $grandTotalExess .= implode(', ', $grandTotalPartsExess) . ")";

            // Update kolom untuk bulan berikutnya
            $colGrandTotalExess = $currentColumnIndex + 6; // Kolom berikutnya untuk loop bulan berikutnya

            $sheet->setCellValue($startColumnGrandtotal . $rowGrandtotal, $grandTotalExess);
            $sheet->mergeCells($startColumnGrandtotal . $rowGrandtotal . ':' . $endColumn . $rowGrandtotal)
                ->getStyle($startColumnGrandtotal . $rowGrandtotal . ':' . $endColumn . $rowGrandtotal)
                ->applyFromArray($styleGrandTotal);

            $startColGrandtotal_index = $currentColumnIndex + 6; // Kolom berikutnya untuk loop bulan berikutnya

            $rowGrandtotal++;

            // Grand Total Exess perbulan
            $sheet->setCellValue($startColumn . $rowGrandtotal, "%")
                ->mergeCells($startColumn . $rowGrandtotal . ':' . $endColumnTitle . $rowGrandtotal)
                ->getStyle($startColumn . $rowGrandtotal . ':' . $endColumnTitle . $rowGrandtotal)
                ->applyFromArray($styleGrandTotal);

            $rowGtMaxCap = $rowGrandtotal - 5;
            $rowGtExess = $rowGrandtotal - 1;
            $sheet->setCellValue($startColumnGrandtotal . $rowGrandtotal, "=(" . $startColumnGrandtotal . $rowGtExess . "/" . $startColumnGrandtotal . $rowGtMaxCap . ")");
            $sheet->mergeCells($startColumnGrandtotal . $rowGrandtotal . ':' . $endColumn . $rowGrandtotal)
                ->getStyle($startColumnGrandtotal . $rowGrandtotal . ':' . $endColumn . $rowGrandtotal)
                ->applyFromArray($styleGrandTotal);
            // Set format kolom menjadi persentase 
            $sheet->getStyle($startColumnGrandtotal . $rowGrandtotal)
                ->getNumberFormat()
                ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE);

            // Pindah ke kolom berikutnya setelah melakukan merge
            $col_index = $endCol_index + 1; // Update ke indeks kolom berikutnya
            $column = Coordinate::stringFromColumnIndex($col_index); // Konversi kembali ke huruf kolom

            // agar baris kembali ke baris pertama grandtotal
            $rowGrandtotal -= 6;
        endforeach;

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

    public function index2()
    {
        $role = session()->get('role');
        $dataJarum = $this->jarumModel->getAliasJrm(); // data all jarum
        $currentMonth = new \DateTime(); // Membuat objek DateTime untuk tanggal saat ini
        $thisMonth = $currentMonth->format('F-Y'); // Mengambil nama bulan dan tahun saat ini
        // $next = clone $currentMonth; // Mengkloning objek DateTime untuk menghindari perubahan pada objek asli
        // $next->modify('+1 month'); // Menambahkan satu bulan
        // $nextMonth = $next->format('F-Y');

        // foreach ($dataJarum as $key => $id) {
        //     $jarum = $id['jarum'];
        //     $start = date('2024-11-01');
        //     $end = date('2024-11-31');
        //     $cek = [
        //         // 'jarum' => $jarum,
        //         'jarum' => 'JC144',
        //         'start' => $start,
        //         'end' => $end,
        //     ];
        //     $dataBookingByJarum = $this->bookingModel->getTotalBookingByJarum2($cek); // Data booking per jarum
        //     // dd($dataBookingByJarum);
        //     $bookingSS = $ttlBookingSS = 0;
        //     $bookingS = $ttlBookingS = 0;
        //     $bookingF = $ttlBookingF = 0;
        //     $bookingKH = $ttlBookingKH = 0;
        //     $bookingTG = $ttlBookingTG = 0;
        //     $booking = $ttlBooking = 0;
        //     $allBooking = 0;
        //     foreach ($dataBookingByJarum as $booking) {
        //         if ($booking['product_group'] == "SS") {
        //             $sisa_bookingSS = $booking['sisa_booking'];
        //             $bookingSS = !empty($sisa_bookingSS) ? $sisa_bookingSS - ($sisa_bookingSS * (25 / 100)) : 0;
        //             $ttlBookingSS += ceil($bookingSS);
        //         } elseif ($booking['product_group'] == "S") {
        //             $sisa_bookingS = $booking['sisa_booking'];
        //             $bookingS = !empty($sisa_bookingS) ? $sisa_bookingS - ($sisa_bookingS * (25 / 100)) : 0;
        //             $ttlBookingS += ceil($bookingS);
        //         } elseif ($booking['product_group'] == "F") {
        //             $sisa_bookingF = $booking['sisa_booking'];
        //             $bookingF = !empty($sisa_bookingF) ? $sisa_bookingF - ($sisa_bookingF * (30 / 100)) : 0;
        //             $ttlBookingF += ceil($bookingF);
        //         } elseif ($booking['product_group'] == "KH") {
        //             $sisa_bookingKH = $booking['sisa_booking'];
        //             $bookingKH = !empty($sisa_bookingKH) ? $sisa_bookingKH  * 1.5 : 0;
        //             $ttlBookingKH += ceil($bookingKH);
        //         } elseif ($booking['product_group'] == "TG") {
        //             $sisa_bookingTG = $booking['sisa_booking'];
        //             $bookingTG = !empty($sisa_bookingTG) ? $sisa_bookingTG  * 2 : 0;
        //             $ttlBookingTG += ceil($bookingTG);
        //         } else {
        //             $sisa_booking = $booking['sisa_booking'];
        //             $booking = !empty($sisa_booking) ? $sisa_booking : 0;
        //             $ttlBooking += ceil($booking);
        //         }
        //     }
        //     $allBooking = $ttlBookingSS + $ttlBookingS + $ttlBookingF + $ttlBookingKH + $ttlBookingTG + $ttlBooking;
        //     dd($allBooking);
        // }

        $data = [
            'role' => $role,
            'title' => 'Sales Position',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'dataJarum' => $dataJarum,
            'thisMonth' => $thisMonth,
            // 'nextMonth' => $nextMonth,
        ];

        return view($role . '/Sales/index2', $data);
    }

    public function updateQtyActualExport()
    {
        $role = session()->get('role');
        // Mengambil data dari input
        $bulan = $this->request->getPost('month');
        $qtyExport = $this->request->getPost('qty_export');

        // Inisialisasi status sukses
        $success = false;

        foreach ($bulan as $key => $month) {
            // Mengambil nilai qtyExport berdasarkan key
            $qtyExportValue = isset($qtyExport[$key]) ? ceil($qtyExport[$key]) : 0;
            var_dump($qtyExportValue);

            if ($qtyExportValue > 0) {
                // Mencari data berdasarkan bulan & tahun
                $existingData = $this->targetExportModel->where('month', $month)->first();
                if ($existingData) {
                    $update = [
                        'qty_target' => $qtyExportValue,
                        'update_at' => date('Y-m-d H:i:s') // Menyisipkan waktu saat ini
                    ];
                    // Jika data sudah ada, lakukan update
                    $this->targetExportModel->where('month', $month)->set($update)->update();
                } else {
                    $data = [
                        'month' => $month,
                        'qty_target' => $qtyExportValue,
                        'created_at' => date('Y-m-d H:i:s') // Menyisipkan waktu saat ini
                    ];
                    // Jika data belum ada, lakukan insert
                    $this->targetExportModel->insert($data);
                }
                // Set status sukses
                $success = true;
            }
        }
        // dd($qtyExport);
        // Mengatur session flash data jika operasi sukses
        if ($success) {
            // Redirect ke halaman yang diinginkan setelah operasi
            return redirect()->to(base_url(session()->get('role') . '/sales'))->with('success', 'Data berhasil disimpan!');
        } else {
            // Redirect ke halaman yang diinginkan setelah operasi
            return redirect()->to(base_url(session()->get('role') . '/sales'))->with('error', 'Data gagal disimpan!');
        }
    }
}
