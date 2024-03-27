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

class CalendarController extends BaseController
{
    public function index()
    {
        // Mendapatkan bulan dan tahun saat ini
        $year = date('Y');
        $month = date('n');

        // Mendapatkan kalender per minggu
        $weekly_calendar = $this->generateWeeklyCalendar($year, $month);

        // Menampilkan halaman dengan data kalender
        return view('calendar_view', ['weekly_calendar' => $weekly_calendar]);
    }

    private function generateWeeklyCalendar($year, $month)
    {
        // Mendapatkan daftar hari libur dari database
        $holidayModel = new LiburModel();
        $holidays = $holidayModel->where('YEAR(tanggal)', $year)
            ->where('MONTH(tanggal)', $month)
            ->findAll();

        // Mendapatkan jumlah hari dalam bulan
        $days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        // Mendapatkan tanggal pertama dan terakhir dari bulan
        $first_day = date("N", strtotime("$year-$month-01"));
        $last_day = date("N", strtotime("$year-$month-$days_in_month"));

        // Mendapatkan jumlah minggu dalam bulan
        $num_weeks = ($first_day == 7) ? ceil(($days_in_month + $first_day - 1) / 7) : ceil(($days_in_month + $first_day - 1) / 7) + 1;

        // Menginisialisasi kalender per minggu
        $weekly_calendar = [];

        // Menyusun tanggal per minggu
        for ($i = 0; $i < $num_weeks; $i++) {
            $week_start = $i * 7 - $first_day + 2;
            $week_end = min($week_start + 6, $days_in_month);
            $week_days = 7; // Jumlah hari dalam seminggu
            foreach ($holidays as $holiday) {
                $holiday_date = date("j", strtotime($holiday['tanggal']));
                if ($holiday_date >= $week_start && $holiday_date <= $week_end) {
                    $week_days--;
                }
            }
            $weekly_calendar[] = [
                'start' => $week_start,
                'end' => $week_end,
                'days_in_week' => $week_days
            ];
        }

        return [
            'weekly_calendar' => $weekly_calendar,
            'days_in_month' => $days_in_month,
        ];
    }

}
