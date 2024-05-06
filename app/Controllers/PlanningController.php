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
use PhpOffice\PhpSpreadsheet\IOFactory;


class PlanningController extends BaseController
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
        if ($this->filters   = ['role' => ['planning']] != session()->get('role')) {
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
            'DaftarLibur' => $holidays


        ];
        return view('Planning/index', $data);
    }
    public function order()
    {
        $totalMesin = $this->jarumModel->getTotalMesinByJarum();
        $data = [
            'title' => 'Data Order',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => 'active',
            'acive7' => '',

            'TotalMesin' => $totalMesin,
        ];
        return view('Planning/Order/ordermaster', $data);
    }

    public function assignareal()
    {
        $data = [
            'mastermodel' => $this->request->getPost("no_model"),
            'jarum' => $this->request->getPost("jarum"),
            'area' => $this->request->getPost("area"),
        ];
        $assign = $this->ApsPerstyleModel->asignAreal($data);
        if ($assign) {
            return redirect()->to(base_url('planning/dataorder/'))->withInput()->with('success', 'Berhasil Assign Area');
        } else {
            return redirect()->to(base_url('planning/dataorder/'))->withInput()->with('error', 'Gagal Assign Area');
        }
    }
    public function assignarealall()
    {
        $data = [
            'mastermodel' => $this->request->getPost("no_model"),
            'area' => $this->request->getPost("area"),
        ];
        $assign = $this->ApsPerstyleModel->asignArealall($data);
        if ($assign) {
            return redirect()->to(base_url('planning/dataorder/'))->withInput()->with('success', 'Berhasil Assign Area');
        } else {
            return redirect()->to(base_url('planning/dataorder/'))->withInput()->with('error', 'Gagal Assign Area');
        }
    }
    public function listplanning(){
        $dataBooking = $this->KebutuhanMesinModel->listPlan();
        $data = [
            'title' => 'List Planning From Capacity',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => 'active',
            'active6' => '',
            'active7' => '',
            'data' => $dataBooking
        ];
        return view('Planning/Planning/listPlanning', $data);
    }

    public function detaillistplanning($judul){
        $dataplan = $this->KebutuhanMesinModel->jarumPlan($judul);
        $data = [
            'title' => 'List Planning From Capacity',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => 'active',
            'active6' => '',
            'active7' => '',
            'data' => $dataplan,
            'judul' => $judul,
        ];
        return view('Planning/Planning/detailPlanning', $data);

    }

    public function pickmachine($id,$jarum){
        $datamc = $this->KebutuhanMesinModel->listmachine($id,$jarum);
        $mesin = $this->request->getPost('mesin');
        $status = $this->request->getPost('deskripsi');
        $data = [
            'title' => 'Pick Machine',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => 'active',
            'active6' => '',
            'active7' => '',
            'datamc' => $datamc,
            'mesin' => $mesin,
            'status' => $status,
            'id' => $id,
            'jarum' => $jarum,
        ];
        return view('Planning/Planning/pilihMesin',$data);

    }
}
