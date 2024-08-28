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
        $jarum = !empty($dataMesin) ? $dataMesin[0]['jarum'] : null;
        $dataTarget = $this->productModel->getKonversi($jarum); // data target per jarum
        $dataMesinByJarum = $this->jarumModel->getTotalMesinCjByJarum($jarum, $aliasjarum); // data target per jarum
        $month = date('F-Y');
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
                        'totalAvailable' => 0,
                        'totalSisaBooking' => 0,
                        'totalConfirmOrder' => 0,
                        'totalSisaConfirmOrder' => 0,
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
            // var_dump($jumlahMcByJrm);
            // exit;
            $monthlyData[$currentMonthOfYear]['weeks'][] = [
                'countWeek' => $weekCount,
                'start_date' => $startOfWeekFormatted,
                'end_date' => $endOfWeekFormatted,
                'number_of_days' => $totalHari,
                'holidays' => $weekHolidays,
                'maxCapacity' => $maxCapacity,
                'available' => $maxCapacity - $sisaBooking - $sisaConfirmOrder,
                'totalBooking' => $totalBooking,
                'sisaBooking' => $sisaBooking,
                'ConfirmOrder' => $ConfirmOrder,
                'sisaConfirmOrder' => $sisaConfirmOrder,
            ];

            $monthlyData[$currentMonthOfYear]['monthlySummary']['totalMaxCapacity'] += $maxCapacity;
            $monthlyData[$currentMonthOfYear]['monthlySummary']['totalAvailable'] += $maxCapacity - $sisaBooking - $sisaConfirmOrder;
            $monthlyData[$currentMonthOfYear]['monthlySummary']['totalSisaBooking'] += $sisaBooking;
            $monthlyData[$currentMonthOfYear]['monthlySummary']['totalConfirmOrder'] += $ConfirmOrder;
            $monthlyData[$currentMonthOfYear]['monthlySummary']['totalSisaConfirmOrder'] += $sisaConfirmOrder;

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
            'weeklyRanges' => $monthlyData,
        ];

        return view('capacity/Sales/index', $data);
    }
}
