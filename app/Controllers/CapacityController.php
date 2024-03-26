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

      // Set the start date to the first day of the current month
      $startDate = new \DateTime('first day of this month');

      // Load the HolidayModel
      $LiburModel = new LiburModel();
  
      // Get all holidays from the database
      $holidays = $LiburModel->findAll();
  
      // Initialize variables to keep track of the current month and week count
      $currentMonth = $startDate->format('F');
      $weekCount = 1; // Initialize week count for the first week of the month
  
      // Initialize array to store weekly ranges
      $weeklyRanges = [];
  
      // Loop for 26 weeks (1 year)
      for ($i = 0; $i < 26; $i++) {
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
  
          // Reset the week count and update the current month if it's a new month
          if ($currentMonth !== $currentMonthOfYear) {
              $currentMonth = $currentMonthOfYear;
              $weekCount = 1; // Reset week count
          }
  
          // Format dates to the desired format
          $startOfWeekFormatted = $startOfWeek->format('Y-m-d');
          $endOfWeekFormatted = $endOfWeek->format('Y-m-d');
  
          // Append the weekly range to the array
          $weeklyRanges[$currentMonth][] = [
              'week' => $weekCount,
              'start_date' => $startOfWeekFormatted,
              'end_date' => $endOfWeekFormatted,
              'number_of_days' => $numberOfDays,
          ];
  
          $weekCount++;
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

