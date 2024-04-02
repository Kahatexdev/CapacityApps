<?php

namespace App\Controllers;


use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\DataMesinModel;
use App\Models\OrderModel;
use App\Models\BookingModel;
use App\Models\ProductTypeModel;
use App\Models\ApsPerstyleModel;
use App\Models\LiburModel;
use App\Models\ProduksiModel;
use CodeIgniter\Format\JSONFormatter;
use PhpOffice\PhpSpreadsheet\IOFactory;

class TestController extends BaseController
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

    public function test()
    {
        $tgl_awal = $this->request->getPost("tgl_awal");
        $tgl_akhir = $this->request->getPost("tgl_akhir");

        $startDate = new \DateTime('first day of this month');
        $LiburModel = new LiburModel();
        $holidays = $LiburModel->findAll();
        $currentMonth = $startDate->format('F');
        $weekCount = 1; // Initialize week count for the first week of the month
        $monthlyData = [];
        $range = 12;
        for ($i = 0; $i < $range; $i++) {
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


            $startOfWeekFormatted = $startOfWeek->format('d-m');
            $endOfWeekFormatted = $endOfWeek->format('d-m');
            $start = $startOfWeek->format('Y-m-d');
            $end = $endOfWeek->format('Y-m-d');
            $cek = [
                'jarum' => 'TJ144',
                'start' => $start,
                'end' => $end,
            ];
            $hari = 18;
            $dt = $this->ApsPerstyleModel->getPlanJarum($cek);
            $normalSock = $this->ApsPerstyleModel->getPlanJarumNs($cek);
            $sneaker = $this->ApsPerstyleModel->getPlanJarumSs($cek);
            $knee = $this->ApsPerstyleModel->getPlanJarumKh($cek);
            $footies = $this->ApsPerstyleModel->getPlanJarumFs($cek);
            $tight = $this->ApsPerstyleModel->getPlanJarumT($cek);
            $normalTotalQty = $normalSock ?? 0;
            $sneakerTotalQty = $sneaker ?? 0;
            $kneeTotalQty = $knee ?? 0;
            $footiesTotalQty = $footies ?? 0;
            $tightTotalQty = $tight ?? 0;

            $monthlyData[] = [
                'week' => $weekCount,
                'start_date' => $startOfWeekFormatted,
                'end_date' => $endOfWeekFormatted,
                'number_of_days' => $numberOfDays,
                'holidays' => $weekHolidays,
                'normal' => $normalTotalQty,
                'sneaker' => $sneakerTotalQty,
                'knee' => $kneeTotalQty,
                'footies' => $footiesTotalQty,
                'tight' => $tightTotalQty,
            ];

            $weekCount++;
        }
        dd($monthlyData);
        $data = [
            'title' => 'Data Booking',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => 'active',
            'DaftarLibur' => $holidays,
            'weeklyRanges' => $monthlyData,
            'DaftarLibur' => $holidays,

        ];
        return view('Capacity/Calendar/generate', $data);
    }
}
