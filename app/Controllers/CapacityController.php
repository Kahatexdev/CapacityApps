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
            'active6' => '',
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
        // Menginisialisasi kalender tahunan
        $yearly_calendar = [];

        // Loop untuk setiap bulan dalam tahun
        for ($month = 1; $month <= 12; $month++) {
            // Mendapatkan daftar hari libur dari database untuk bulan tertentu
            $holidayModel = new LiburModel();
            $holidays = $holidayModel->where('YEAR(tanggal)', $year)
                                     ->where('MONTH(tanggal)', $month)
                                     ->findAll();

            // Mendapatkan jumlah hari dalam bulan
            $days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);

            // Menginisialisasi kalender bulanan
            $monthly_calendar = [];

            // Menghitung hari libur dalam bulan
            $holidays_in_month = count($holidays);

            // Menyusun tanggal per minggu
            for ($week = 1; $week <= 5; $week++) { // Maksimum 5 minggu dalam sebulan
                $week_start = ($week - 1) * 7 + 1;
                $week_end = min($week_start + 6, $days_in_month);
                $week_days = $week_end - $week_start + 1; // Jumlah hari dalam seminggu

                // Mengurangi hari libur dari jumlah hari dalam minggu
                foreach ($holidays as $holiday) {
                    $holiday_day = date('j', strtotime($holiday['tanggal']));
                    if ($holiday_day >= $week_start && $holiday_day <= $week_end) {
                        $week_days--;
                    }
                }

                // Menambahkan minggu ke kalender bulanan
                $monthly_calendar[] = [
                    'week_start' => $week_start,
                    'week_end' => $week_end,
                    'week_days' => $week_days
                ];
            }

            // Menambahkan kalender bulanan ke kalender tahunan
            $yearly_calendar[$month] = [
                'days_in_month' => $days_in_month,
                'holidays_in_month' => $holidays_in_month,
                'monthly_calendar' => $monthly_calendar
            ];
        }

        return $yearly_calendar;
    }


    // 
}
