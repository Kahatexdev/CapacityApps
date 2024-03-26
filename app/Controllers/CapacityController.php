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
use PhpOffice\PhpSpreadsheet\IOFactory;

class CapacityController extends BaseController
{
    protected $filters;
    protected $jarumModel;
    protected $productModel;
    protected $produksiModel;
    protected $bookingModel;
    protected $orderModel;
    protected $ApsPerstyleModel;

    public function __construct()
    {
        $this->jarumModel = new DataMesinModel();
        $this->bookingModel = new BookingModel();
        $this->productModel = new ProductTypeModel();
        $this->produksiModel = new ProduksiModel();
        $this->orderModel = new OrderModel();
        $this->ApsPerstyleModel = new ApsPerstyleModel();
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
        $bulan = date('m');
        $year = date('Y');

        // Mendapatkan kalender tahunan
        $yearly_calendar = $this->generateYearlyCalendar($year);
        $data = [
            'title' => 'Capacity System',
            'active1' => 'active',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'jalan' => $orderJalan,
            'TerimaBooking' => $terimaBooking,
            'mcJalan' => $mcJalan,
            'totalMc' => $totalMc,
            'order' => $this->ApsPerstyleModel->getTurunOrder($bulan),
            'Capacity'=>$yearly_calendar


        ];
        return view('Capacity/index', $data);
    }
   

    private function generateYearlyCalendar($year)
    {
        // Inisialisasi array untuk kalender tahunan
        $yearly_calendar = [];

        // Loop untuk setiap bulan dalam tahun
        for ($month = 1; $month <= 12; $month++) {
            // Mendapatkan data kalender bulanan
            $monthly_calendar = $this->generateMonthlyCalendar($year, $month);

            // Menambahkan data kalender bulanan ke kalender tahunan
            $yearly_calendar[$month] = $monthly_calendar;
        }

        return $yearly_calendar;
    }

    private function generateMonthlyCalendar($year, $month)
    {
        // Mendapatkan daftar hari libur dari database untuk bulan dan tahun tertentu
        $holidayModel = new LiburModel();
        $holidays = $holidayModel->where('YEAR(tanggal)', $year)
                                 ->where('MONTH(tanggal)', $month)
                                 ->findAll();

        // Mendapatkan jumlah hari dalam bulan
        $days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        // Inisialisasi array untuk kalender bulanan
        $monthly_calendar = [
            'days_in_month' => $days_in_month,
            'holidays_in_month' => count($holidays),
            'monthly_calendar' => []
        ];

        // Menentukan tanggal awal bulan dan tanggal akhir bulan
        $first_day_of_month = date('Y-m-d', strtotime("{$year}-{$month}-01"));
        $last_day_of_month = date('Y-m-d', strtotime("last day of {$year}-{$month}"));

        // Menentukan tanggal akhir minggu terakhir dalam bulan
        $last_week_end = date('Y-m-d', strtotime("last sunday", strtotime($last_day_of_month)));

        // Menyusun tanggal per minggu
        $week_start = $first_day_of_month;
        $week = 1;
        $current_date = $first_day_of_month;
        while (strtotime($current_date) <= strtotime($last_week_end)) {
            // Tentukan tanggal akhir minggu
            $week_end = date('Y-m-d', strtotime("next sunday", strtotime($current_date)));
            if (strtotime($week_end) > strtotime($last_day_of_month)) {
                $week_end = $last_day_of_month; // Sesuaikan tanggal akhir minggu terakhir dengan tanggal akhir bulan
            }
            // Hitung jumlah hari dalam minggu
            $week_days = $this->countWeekDays($week_start, $week_end, $holidays);

            // Hitung jumlah hari libur dalam minggu
            $holidays_in_week = $this->countHolidaysInWeek($week_start, $week_end, $holidays);

            // Menambahkan minggu ke kalender bulanan
            $monthly_calendar['monthly_calendar'][$week] = [
                'week_start' => $week_start,
                'week_end' => $week_end,
                'week_days' => $week_days,
                'holidays_in_week' => $holidays_in_week
            ];

            // Persiapkan untuk minggu berikutnya
            $week++;
            $week_start = date('Y-m-d', strtotime("next monday", strtotime($week_end)));
            $current_date = $week_start;
        }

        return $monthly_calendar;
    }

    private function countWeekDays($start_date, $end_date, $holidays)
    {
        $week_days = 0;
        $current_date = $start_date;
        while (strtotime($current_date) <= strtotime($end_date)) {
            $day_of_week = date('N', strtotime($current_date));
            if ($day_of_week >= 1 && $day_of_week <= 5) {
                $week_days++;
            }
            $current_date = date('Y-m-d', strtotime("+1 day", strtotime($current_date)));
        }

        return $week_days;
    }

    private function countHolidaysInWeek($start_date, $end_date, $holidays)
    {
        $holidays_in_week = 0;
        foreach ($holidays as $holiday) {
            $holiday_date = date('Y-m-d', strtotime($holiday['tanggal']));
            if (strtotime($holiday_date) >= strtotime($start_date) && strtotime($holiday_date) <= strtotime($end_date)) {
                $holidays_in_week++;
            }
        }

        return $holidays_in_week;
    }
}
