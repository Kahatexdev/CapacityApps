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
use PhpOffice\PhpSpreadsheet\IOFactory;

class CalendarController extends BaseController
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

public function generateCalendar($year, $month)
{
    // Get the current day, month, and year
    $currentDay = date('j');
    $currentMonth = date('n');
    $currentYear = date('Y');
    
    $days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $first_day = mktime(0, 0, 0, $month, 1, $year);
    $first_day_of_week = date('N', $first_day); // 1 (Monday) to 7 (Sunday)

    $calendar = '<table>';
    $calendar .= '<tr>';
    $calendar .= '<th>Sen</th>';
    $calendar .= '<th>Sel</th>';
    $calendar .= '<th>Rab</th>';
    $calendar .= '<th>Kam</th>';
    $calendar .= '<th>Jum</th>';
    $calendar .= '<th>Sam</th>';
    $calendar .= '<th>Min</th>';
    $calendar .= '</tr>';
    $calendar .= '<tr>';

    // Fill in blank cells until the first day of the week
    for ($i = 1; $i < $first_day_of_week; $i++) {
        $calendar .= '<td></td>';
    }

    // Fill in the days of the month
    for ($day = 1; $day <= $days_in_month; $day++) {
        $class = ($day == $currentDay && $month == $currentMonth && $year == $currentYear) ? 'highlight' : '';
        $calendar .= '<td class="' . $class . '">' . $day . '</td>';

        // Start a new row if it's the end of the week
        if (date('N', mktime(0, 0, 0, $month, $day, $year)) == 7) {
            $calendar .= '</tr>';
            // If it's not the last day of the month, start a new row
            if ($day != $days_in_month) {
                $calendar .= '<tr>';
            }
        }
    }

    // Fill in remaining empty cells until the end of the week
    while (date('N', mktime(0, 0, 0, $month, $day, $year)) != 1) {
        $calendar .= '<td></td>';
        $day++;
    }

    $calendar .= '</tr>';
    $calendar .= '</table>';

    return $calendar;
    }

    public function generateYearCalendar($year)
    {
        $calendar = '<h2>' . $year . '</h2>';
        
        $calendar .= '<div class="row">';
        for ($month = 1; $month <= 12; $month++) {
            $calendar .= '<div class="col">';
            $calendar .= '<h3>' . date('F', mktime(0, 0, 0, $month, 1, $year)) . '</h3>';
            $calendar .= $this->generateCalendar($year, $month);
            $calendar .= '</div>';
            // Add a new row after every 4 months
            if ($month % 4 == 0 && $month != 12) {
                $calendar .= '</div><div class="row">';
            }
        }
        $calendar .= '</div>';
    
        return $calendar;
    }
    


    public function index()
    {
        helper('calendar');
        $year = date('Y');
        $month = date('m');
        $calendar = $this->generateYearCalendar($year);
        $data = [
            'title' => 'Data Libur Calendar',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => 'active',
            'calendar' => $calendar,
        ];
        return view('Capacity/Calendar/MasterCalendar', $data);
    }

}