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
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

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
                    $prod = ($item['cj'] * $item['target'] * 28) / 24;

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
        $endDate = new \DateTime('+1 year'); // Tanggal satu tahun ke depan

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
                            $brandCapacity = ceil(($jumlahMc * $target * $numberOfDays) / 24);
                        }
                    }
                }
                $carry += $brandCapacity;
                return $carry;
            }, 0);

            $countExess = ($maxCapacity > 0) ? -$maxCapacity + $sisaBooking + $sisaConfirmOrder : 0;
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
            $monthlyData[$currentMonthOfYear]['monthlySummary']['totalMaxCapacity'] += ceil($maxCapacity);
            $monthlyData[$currentMonthOfYear]['monthlySummary']['totalSisaBooking'] += $sisaBooking;
            $monthlyData[$currentMonthOfYear]['monthlySummary']['totalConfirmOrder'] += $ConfirmOrder;
            $monthlyData[$currentMonthOfYear]['monthlySummary']['totalSisaConfirmOrder'] += $sisaConfirmOrder;
            $monthlyData[$currentMonthOfYear]['monthlySummary']['totalExess'] += $exess;
            $monthlyData[$currentMonthOfYear]['monthlySummary']['totalExessPercentage'] +=  $exessPercentage;

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
                    $prod = ($item['cj'] * $item['target'] * 28) / 24;

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
        $endDate = new \DateTime('+3 month'); // Tanggal satu tahun ke depan

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
                            $brandCapacity = ceil(($jumlahMc * $target * $numberOfDays) / 24);
                        }
                    }
                }
                $carry += $brandCapacity;
                return $carry;
            }, 0);

            $countExess = ($maxCapacity > 0) ? -$maxCapacity + $sisaBooking + $sisaConfirmOrder : 0;
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
                'ConfirmOrder' => $ConfirmOrder,
                'sisaConfirmOrder' => $sisaConfirmOrder,
                'sisaBooking' => $sisaBooking,
                'totalBooking' => $totalBooking,
                'exess' => $exess,
                'exessPercentage' => $exessPercentage,
            ];

            // Update data bulanan
            $monthlyData[$currentMonthOfYear]['monthlySummary']['totalMaxCapacity'] += ceil($maxCapacity);
            $monthlyData[$currentMonthOfYear]['monthlySummary']['totalSisaBooking'] += $sisaBooking;
            $monthlyData[$currentMonthOfYear]['monthlySummary']['totalConfirmOrder'] += $ConfirmOrder;
            $monthlyData[$currentMonthOfYear]['monthlySummary']['totalSisaConfirmOrder'] += $sisaConfirmOrder;
            $monthlyData[$currentMonthOfYear]['monthlySummary']['totalExess'] += $exess;
            $monthlyData[$currentMonthOfYear]['monthlySummary']['totalExessPercentage'] +=  $exessPercentage;

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


        $column = 'R'; // Kolom awal untuk bulan
        $col_index = Coordinate::columnIndexFromString($column); // Konversi huruf kolom ke nomor indeks kolom
        $column2 = 'R';
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
            ->getStyle('A' . $rowBody)
            ->applyFromArray($styleBody);
        $sheet->setCellValue('B' . $rowBody, isset($brandData['Dakong']['totalMc']) ? $brandData['Dakong']['totalMc'] : 0)
            ->getStyle('B' . $rowBody)
            ->applyFromArray($styleBody);
        $sheet->setCellValue('C' . $rowBody, isset($brandData['Rosso']['totalMc']) ? $brandData['Rosso']['totalMc'] : 0)
            ->getStyle('C' . $rowBody)
            ->applyFromArray($styleBody);
        $sheet->setCellValue('D' . $rowBody, isset($brandData['Mechanic']['totalMc']) ? $brandData['Mechanic']['totalMc'] : 0)
            ->getStyle('D' . $rowBody)
            ->applyFromArray($styleBody);
        $sheet->setCellValue('E' . $rowBody, isset($brandData['Lonati']['totalMc']) ? $brandData['Lonati']['totalMc'] : 0)
            ->getStyle('E' . $rowBody)
            ->applyFromArray($styleBody);
        $sheet->setCellValue('F' . $rowBody, isset($totals['totalMc']) ? $totals['totalMc'] : 0)
            ->getStyle('F' . $rowBody)
            ->applyFromArray($styleBody);
        // running mc by system
        $sheet->setCellValue('G' . $rowBody, isset($brandData['Dakong']['totalRunning']) ? $brandData['Dakong']['totalMc'] : 0)
            ->getStyle('G' . $rowBody)
            ->applyFromArray($styleBody);
        $sheet->setCellValue('H' . $rowBody, isset($brandData['Rosso']['totalRunning']) ? $brandData['Rosso']['totalMc'] : 0)
            ->getStyle('H' . $rowBody)
            ->applyFromArray($styleBody);
        $sheet->setCellValue('I' . $rowBody, isset($brandData['Lonati']['totalRunning']) ? $brandData['Mechanic']['totalMc'] : 0)
            ->getStyle('I' . $rowBody)
            ->applyFromArray($styleBody);
        $sheet->setCellValue('J' . $rowBody, isset($brandData['Mechanic']['totalRunning']) ? $brandData['Lonati']['totalMc'] : 0)
            ->getStyle('J' . $rowBody)
            ->applyFromArray($styleBody);
        $sheet->setCellValue('K' . $rowBody, isset($totals['totalMc']) ? $totals['totalRunning'] : 0)
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


        $columnBody = 'R'; // Kolom awal 
        $colBody_index = Coordinate::columnIndexFromString($columnBody); // Konversi huruf kolom ke nomor indeks kolom
        foreach ($monthlyData as $month => $data) :
            $startColumnBody = $columnBody; // Kolom awal 

            foreach ($data['weeks'] as $week) :
                // dd($data['weeks']);
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
        // dd($writer);
        $writer->save('php://output');
        // return redirect(session()->get('role') . '/sales');
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
