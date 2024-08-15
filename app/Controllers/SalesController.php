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
        $dataJarum = $this->jarumModel->getJarum(); // data all jarum
        $jarum = $this->request->getGet('jarum'); // data filter jarum
        $dataMesin = $this->jarumModel->getAllBrand($jarum); // data mesin per jarum
        $dataTarget = $this->productModel->getKonversi($jarum); // data target per jarum
        $dataMesinByJarum = $this->jarumModel->getTotalMesinCjByJarum($jarum); // data target per jarum

        $month = date('F-Y');
        $startDate = new \DateTime(); // Tanggal hari ini
        $endDate = new \DateTime('+1 year'); // Tanggal satu tahun ke depan

        $jumlahHari = $endDate->diff($startDate)->days + 1; // Hitung jumlah hari bulan ini
        $LiburModel = new LiburModel();
        $holidays = $LiburModel->findAll();

        $currentMonth = $startDate->format('F');
        $range = ceil($jumlahHari / 7); // Pembulatan ke atas untuk rentang minggu
        $weekCount = 1; // Inisialisasi minggu
        $monthlyData = [];
        $totalMonthlyCapacity = 0; // Inisialisasi total kapasitas bulanan

        for ($i = 0; $i < $range; $i++) {
            $startOfWeek = clone $startDate;
            $startOfWeek->modify("+$i week");
            $startOfWeek->modify('Monday this week');


            $endOfWeek = clone $startOfWeek;
            $endOfWeek->modify('Sunday this week');
            $numberOfDays = $startOfWeek->diff($endOfWeek)->days + 1;

            $holidaysCount = 0;
            $weekHolidays = [];
            foreach ($holidays as $holiday) {
                $holidayDate = new \DateTime($holiday['tanggal']);
                if ($holidayDate >= $startOfWeek && $holidayDate <= $endOfWeek) {
                    $weekHolidays[] = [
                        'nama' => $holiday['nama'],
                        'tanggal' => $holidayDate->format('d-F'),
                    ];
                    $holidaysCount++; // Menambah jumlah hari libur
                    $numberOfDays--;
                }
            }
            $currentMonthOfYear = $startOfWeek->format('F');
            if ($currentMonth !== $currentMonthOfYear) {
                $currentMonth = $currentMonthOfYear;
                $monthlyData[$currentMonth] = [];
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
            $dataBookingByJarum = $this->bookingModel->getTotalBookingByJarum($cek); // data booking per jarum
            $totalBooking = 0;

            // Jika dataBookingByJarum adalah array dari hasil, lakukan penjumlahan
            foreach ($dataBookingByJarum as $booking) {
                $totalBooking += !empty($booking['total_booking']) ? $booking['total_booking'] / 24 : 0; // Ganti 'jumlah' dengan kunci yang sesuai
            }
            // Format total booking
            $formattedBooking = $totalBooking != 0 ? number_format($totalBooking, 0, ',', '.') : '-';
            // print_r($formattedBooking) . '<br>';
            // exit;

            // //
            $jumlahMcByJrm = 0;
            $totalHari = $numberOfDays; // Hitung total hari yang tidak libur per minggu
            $maxCapacity = 0; // Reset total kapasitas untuk minggu ini
            // Hitung total mesin di cijerah per jarum
            foreach ($dataMesinByJarum as $mc) {
                $jumlahMcByJrm = $mc['total']; // Total mesin di cijerah per jarum
                // hitung max capacity
                foreach ($dataTarget as $target) {
                    $konversi = !empty($target['konversi']) ? $target['konversi'] : 14;
                    if ($jumlahMcByJrm > 0) {
                        $maxCapacity = ($jumlahMcByJrm * $konversi * $totalHari) / 24;
                    }
                }
                // hitung available
            }
            // var_dump($startOfWeekFormatted, $endOfWeekFormatted);
            // exit;
            // Debugging output untuk memeriksa hasil
            $monthlyData[$currentMonth][] = [
                'week' => $weekCount,
                'start_date' => $startOfWeekFormatted,
                'end_date' => $endOfWeekFormatted,
                'number_of_days' => $numberOfDays,
                'holidays' => $weekHolidays,
                'maxCapacity' => $maxCapacity,
                'totalMonthlyCapacity' => $totalMonthlyCapacity,
                'dataBooking' => $totalBooking,
            ];

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
            'dataMesin' => $dataMesin,
            'my' => $month,
            'weeklyRanges' => $monthlyData,
        ];
        return view('capacity/Sales/index', $data);
    }
}
