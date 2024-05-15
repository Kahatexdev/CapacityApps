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
use App\Models\KebutuhanMesinModel;
use App\Models\MesinPlanningModel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use CodeIgniter\HTTP\RequestInterface;


class ApsController extends BaseController
{
    protected $filters;
    protected $jarumModel;
    protected $productModel;
    protected $produksiModel;
    protected $bookingModel;
    protected $orderModel;
    protected $ApsPerstyleModel;
    protected $liburModel;
    protected $KebutuhanMesinModel;
    protected $MesinPlanningModel;

    public function __construct()
    {
        $this->jarumModel = new DataMesinModel();
        $this->bookingModel = new BookingModel();
        $this->productModel = new ProductTypeModel();
        $this->produksiModel = new ProduksiModel();
        $this->orderModel = new OrderModel();
        $this->ApsPerstyleModel = new ApsPerstyleModel();
        $this->liburModel = new LiburModel();
        $this->KebutuhanMesinModel = new KebutuhanMesinModel();
        $this->MesinPlanningModel = new MesinPlanningModel();
        if ($this->filters   = ['role' => ['aps']] != session()->get('role')) {
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

        $startDate = new \DateTime('first day of this month');
        $LiburModel = new LiburModel();
        $holidays = $LiburModel->findAll();
        $currentMonth = $startDate->format('F');
        $weekCount = 1; // Initialize week count for the first week of the month
        $monthlyData = [];

        for ($i = 0; $i < 52; $i++) {
            $startOfWeek = clone $startDate;
            $startOfWeek->modify("+$i week");
            $startOfWeek->modify('Monday this week');

            $endOfWeek = clone $startOfWeek;
            $endOfWeek->modify('Sunday this week');
            $numberOfDays = $startOfWeek->diff($endOfWeek)->days + 1;

            $weekHolidays = [];
            foreach ($holidays as $holiday) {
                $holidayDate = new \DateTime($holiday['tanggal']);
                if ($holidayDate >= $startOfWeek && $holidayDate <= $endOfWeek) {
                    $weekHolidays[] = [
                        'nama' => $holiday['nama'],
                        'tanggal' => $holidayDate->format('d-F'),
                    ];
                    $numberOfDays--;
                }
            }
            $currentMonthOfYear = $startOfWeek->format('F');
            if ($currentMonth !== $currentMonthOfYear) {
                $currentMonth = $currentMonthOfYear;
                $weekCount = 1; // Reset week count
                $monthlyData[$currentMonth] = [];
            }

            $startOfWeekFormatted = $startOfWeek->format('d/m');
            $endOfWeekFormatted = $endOfWeek->format('d/m');

            $monthlyData[$currentMonth][] = [
                'week' => $weekCount,
                'start_date' => $startOfWeekFormatted,
                'end_date' => $endOfWeekFormatted,
                'number_of_days' => $numberOfDays,
                'holidays' => $weekHolidays,
            ];

            $weekCount++;
        }
        $area =session()->get('username');
        $data = [
            'title' => 'Capacity System',
            'active1' => 'active',
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
            'order' => $this->ApsPerstyleModel->getTurunOrder($bulan),
            'weeklyRanges' => $monthlyData,
            'DaftarLibur' => $holidays,
            'area'=>$area


        ];
        return view('Aps/index', $data);
    }
    public function booking()
    {
        $dataJarum = $this->jarumModel->getJarum();
        $totalMesin = $this->jarumModel->getTotalMesinByJarum();

        $data = [
            'title' => 'Data Booking',
            'active1' => '',
            'active2' => 'active',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'Jarum' => $dataJarum,
            'TotalMesin' => $totalMesin,
        ];
        return view('Aps/Booking/booking', $data);
    }
    public function bookingPerJarum($jarum)
    {
        $product = $this->productModel->getJarum($jarum);
        $booking = $this->bookingModel->getDataPerjarum($jarum);

        $data = [
            'title' => 'Data Booking',
            'active1' => '',
            'active2' => 'active',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'jarum' => $jarum,
            'product' => $product,
            'booking' => $booking

        ];
        return view('Aps/Booking/jarum', $data);
    }

    public function bookingPerBulanJarum($jarum)
    {
        $bulan = $this->bookingModel->getbulan($jarum);
        $data = [
            'title' => 'Data Booking',
            'active1' => '',
            'active2' => 'active',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'bulan' => $bulan,
            'jarum' => $jarum,
        ];
        return view('Aps/Booking/bookingbulan', $data);
    }

    public function bookingPerBulanJarumTampil($bulan, $tahun, $jarum)
    {
        $booking = $this->bookingModel->getDataPerjarumbulan($bulan, $tahun, $jarum);
        $product = $this->productModel->getJarum($jarum);
        $data = [
            'title' => 'Data Booking',
            'active1' => '',
            'active2' => 'active',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'booking' => $booking,
            'product' => $product,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'jarum' => $jarum,
        ];
        return view('Aps/Booking/jarumbulan', $data);
    }

    
    public function detailbooking($idBooking)
    {
        $needle = $this->bookingModel->getNeedle($idBooking);
        $product = $this->productModel->findAll();
        $booking = $this->bookingModel->getDataById($idBooking);
        $totalMesin = $this->jarumModel->getTotalMesinByJarum();
        $childOrder = $this->orderModel->getChild($idBooking);
        $childBooking = $this->bookingModel->getChild($idBooking);
        $data = [
            'title' => 'Data Booking',
            'active1' => '',
            'active2' => 'active',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'booking' => $booking,
            'jarum' => $needle,
            'product' => $product,
            'jenisJarum' => $totalMesin,
            'childOrder' => $childOrder,
            'childBooking' => $childBooking

        ];
        return view('Aps/Booking/detail', $data);
    }
    
}
