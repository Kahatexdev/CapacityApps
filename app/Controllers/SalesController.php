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
        $dataJarum = $this->jarumModel->getJarum(); // data all jarum
        $jarum = $this->request->getGet('jarum'); // data filter jarum
        $dataMesin = $this->jarumModel->getAllBrand($jarum); // data mesin per jarum
        $dataTarget = $this->productModel->getKonversi($jarum); // data target per jarum
        $dataMesinByJarum = $this->jarumModel->getTotalMesinCjByJarum($jarum); // data target per jarum

        $month = date('F-Y');
        $startDate = new \DateTime(); // Tanggal hari ini
        $startDate->modify('Monday this week'); // Memastikan start date dimulai dari hari Senin minggu ini
        $endDate = new \DateTime('+1 year'); // Tanggal satu tahun ke depan


        $jumlahHari = $endDate->diff($startDate)->days + 1; // Hitung jumlah hari bulan ini
        $LiburModel = new LiburModel();
        $holidays = $LiburModel->findAll();

        $currentMonth = $startDate->format('F');
        $range = ceil($jumlahHari / 7); // Pembulatan ke atas untuk rentang minggu
        $weekCount = 1; // Inisialisasi minggu

        $monthlyData = [];
        $totalMonthlyCapacity = 0;
        $totalMonthlyAvailable = 0;
        $totalMonthlyMachine = 0;
        $totalMonthlyOrder = 0;

        while ($startDate <= $endDate) {
            $startOfWeek = clone $startDate;
            $startOfWeek->modify('Monday this week');
            $endOfWeek = clone $startOfWeek;
            $endOfWeek->modify('Sunday this week');

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
                }
            }

            $currentMonthOfYear = $startOfWeek->format('F-Y');
            if (!isset($monthlyData[$currentMonthOfYear])) {
                $monthlyData[$currentMonthOfYear] = [
                    'monthlySummary' => [
                        'totalCapacity' => 0,
                        'totalAvailable' => 0,
                        'totalMachine' => 0,
                        'totalOrder' => 0,
                    ],
                    'weeks' => []
                ];
            }

            $startOfWeekFormatted = $startOfWeek->format('d-m');
            $endOfWeekFormatted = $endOfWeek->format('d-m');
            $start = $startOfWeek->format('Y-m-d');
            $end = $endOfWeek->format('Y-m-d');
            $cek = [
                'jarum' => $jarum,
                'start' => $start,
                'end' => $end,
            ];
            $dataBookingByJarum = $this->bookingModel->getTotalBookingByJarum($cek); // Data booking per jarum
            $dataOrderWeekByJarum = $this->ApsPerstyleModel->getTotalOrderWeek($cek); // Data order per jarum

            $confirmOrder = 0;
            foreach ($dataOrderWeekByJarum as $order) {
                $confirmOrder = !empty($order['qty']) ? $order['qty'] / 24 : 0;
            }

            $totalBooking = 0;
            $sisaBooking = 0;
            foreach ($dataBookingByJarum as $booking) {
                $totalBooking = !empty($booking['total_booking']) ? $booking['total_booking'] / 24 : 0;
                $sisaBooking = !empty($booking['sisa_booking']) ? $booking['sisa_booking'] / 24 : 0;
            }

            $jumlahMcByJrm = 0;
            $totalHari = $startOfWeek->diff($endOfWeek)->days + 1;
            $maxCapacity = 0;
            foreach ($dataMesinByJarum as $mc) {
                $jumlahMcByJrm = $mc['total'];
                foreach ($dataTarget as $target) {
                    $konversi = !empty($target['konversi']) ? $target['konversi'] : 14;
                    if ($jumlahMcByJrm > 0) {
                        $maxCapacity = ($jumlahMcByJrm * $konversi * $totalHari) / 24;
                    }
                }
            }

            $monthlyData[$currentMonthOfYear]['weeks'][] = [
                'countWeek' => $weekCount,
                'start_date' => $startOfWeekFormatted,
                'end_date' => $endOfWeekFormatted,
                'number_of_days' => $totalHari,
                'holidays' => $weekHolidays,
                'maxCapacity' => $maxCapacity,
                'available' => $maxCapacity - $sisaBooking - $confirmOrder,
                'totalBooking' => $totalBooking,
                'sisaBooking' => $sisaBooking,
                'confirmOrder' => $confirmOrder,
            ];

            $monthlyData[$currentMonthOfYear]['monthlySummary']['totalCapacity'] += $maxCapacity;
            $monthlyData[$currentMonthOfYear]['monthlySummary']['totalAvailable'] += $maxCapacity - $sisaBooking - $confirmOrder;
            // $monthlyData[$currentMonthOfYear]['monthlySummary']['totalMachine'] += $jumlahMcByJrm;
            $monthlyData[$currentMonthOfYear]['monthlySummary']['totalOrder'] += $confirmOrder;

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
            'my' => $month,
            'weeklyRanges' => $monthlyData,
        ];

        return view('capacity/Sales/index', $data);
    }
}
