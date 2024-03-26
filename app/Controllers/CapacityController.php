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
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Week;
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

             // Specify the start date
    $startDate = new \DateTime('first day of this month');

    // Load the HolidayModel
    $liburModel = new LiburModel();

    // Get all holidays from the database
    $holidays = $liburModel->findAll();

    // Initialize variable to keep track of the current month
    $currentMonth = null;

    // Initialize array to store weekly ranges
    $weeklyRanges = [];

    // Loop for 52 weeks to generate weekly ranges
    for ($i = 0; $i < 52; $i++) {
        // Calculate the start of the week
        $startOfWeek = clone $startDate;
        $startOfWeek->modify("+$i week");
        $startOfWeek->modify('Monday this week');

        // Calculate the end of the week
        $endOfWeek = clone $startOfWeek;
        $endOfWeek->modify('Sunday this week');

        // Calculate the number of days in the week
        $numberOfDays = $startOfWeek->diff($endOfWeek)->days + 1;

        // Check if any holidays fall within this week
        foreach ($holidays as $holiday) {
            $holidayDate = new \DateTime($holiday['tanggal']);
            if ($holidayDate >= $startOfWeek && $holidayDate <= $endOfWeek) {
                // Subtract the holiday from the total number of days
                $numberOfDays--;
            }
        }

        // Get the month of the current week
        $currentMonthOfYear = $startOfWeek->format('F');

        // Format dates to the desired format
        $startOfWeekFormatted = $startOfWeek->format('Y-m-d');
        $endOfWeekFormatted = $endOfWeek->format('Y-m-d');

        // Append the weekly range to the array
        $weeklyRanges[$currentMonthOfYear][] = [
            'start_date' => $startOfWeekFormatted,
            'end_date' => $endOfWeekFormatted,
            'number_of_days' => $numberOfDays,
        ];
    }

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
                'weeklyRanges'=>$weeklyRanges


            ];
            return view('Capacity/index', $data);
     }
  
}

