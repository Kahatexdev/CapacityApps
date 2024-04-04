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
use PhpParser\Node\Expr\Cast\String_;

class CalendarController extends BaseController
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
        $holidays = $this->liburModel->findAll();
        $dataJarum = $this->jarumModel->getJarum();
        $totalMesin = $this->jarumModel->getTotalMesinByJarum();
        $data = [
            'title' => 'Data Booking',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => 'active',
            'Jarum' => $dataJarum,
            'TotalMesin' => $totalMesin,
            'DaftarLibur' => $holidays,
        ];
        return view('Capacity/Calendar/index', $data);
    }
    public function calendar($jarum)
    {
        $awal = strval($this->request->getPost("awal"));
        $akhir = strval($this->request->getPost("akhir"));

        $tgl_awal = strtotime($awal);
        $tgl_akhir = strtotime($akhir);

        $jumlahHari = ($tgl_akhir - $tgl_awal) / (60 * 60 * 24);
        
        $startDate = new \DateTime('first day of this month');
        $LiburModel = new LiburModel();
        $holidays = $LiburModel->findAll();
        $currentMonth = $startDate->format('F');
        $weekCount = 1; // Initialize week count for the first week of the month
        $monthlyData = [];
        $range = $jumlahHari / 7;
        $value = []; // Initialize $value array

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

        $normalTotal = 0;
        $sneakerTotal = 0;
        $kneeTotal = 0;
        $footiesTotal = 0;
        $tightTotal = 0;
        $hariTotal = 0;
        $tNormal = 1;
        $tSneaker = 1;
        $tFooties = 1;
        $tKnee = 1;
        $tTight = 1;
        foreach ($monthlyData as $data) {
          
           $normal = $data['normal'];
           $hari = $data['number_of_days'];
            $normalTotal += $normal;
            $sneakerTotal += $sneaker;
            $kneeTotal += $knee;
            $footiesTotal += $footies;
            $tightTotal += $tight;
            $hariTotal += $hari;

            switch ($jarum) {
                case 'JC120':
                    $tNormal = 17;
                    $tSneaker = 20.4;
                    $tFooties = 21.3;
                    $tKnee = 11.3;
                    $tTight = 8.2;
                    break;
                case 'JC168':
                    $tNormal = 12;
                    $tSneaker = 15.6;
                    $tFooties = 16.9;
                    $tKnee = 8.7;
                    $tTight = 4.3;
                    break;
                default:
                    $tNormal = 1;
                    $tSneaker = 1;
                    $tFooties = 1;
                    $tKnee = 1;
                    $tTight = 1;
                    break;
            }
        }
        $value[] = [
            'Jumlah Hari1' => $hariTotal,
            'totalNormal' => ceil($normalTotal / $tNormal / $hariTotal),
            'totalsneaker' => ceil($sneakerTotal / $tSneaker / $hariTotal),
            'totalFooties' => ceil($footiesTotal / $tFooties / $hariTotal),
            'totalKnee' => ceil($kneeTotal / $tKnee / $hariTotal),
            'totalTight' => ceil($tightTotal / $tTight / $hariTotal),
            'Jumlah HariTotal' => $hariTotal,
        ];

        $maxTotalNormal = max(array_column($value, 'totalNormal'));
        $maxTotalSneaker = max(array_column($value, 'totalsneaker'));
        $maxTotalFooties = max(array_column($value, 'totalFooties'));
        $maxTotalKnee = max(array_column($value, 'totalKnee'));
        $maxTotalTight = max(array_column($value, 'totalTight'));
        $maxHariTotal = max(array_column($value, 'Jumlah Hari1'));

        $TotalKebutuhanMesin = $maxTotalNormal + $maxTotalSneaker + $maxTotalFooties + $maxTotalKnee + $maxTotalTight;
        $kebutuhanMc = [
            'Normal Socks' => $maxTotalNormal,
            'Sneakers' => $maxTotalSneaker,
            'Footies' => $maxTotalFooties,
            'Knee' => $maxTotalKnee,
            'Tight' => $maxTotalTight,
            'Total Kebutuhan Mesin' => $TotalKebutuhanMesin,
            'Hari' => $hariTotal
        ];
        // Di sini Anda mungkin perlu memanggil model lain untuk mendapatkan data lain yang diperlukan
        $kategori = $this->productModel->getKategori();

        $data = [
            'title' => 'Capacity System',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => 'active',
            'KebutuhanMC'=>$kebutuhanMc,
            'weeklyRanges' => $monthlyData,
            'DaftarLibur' => $holidays,
            'kategoriProduk' => $kategori
        ];
        dd($data);

        return view('Capacity/Calendar/calendar', $data);
    }



    public function inputLibur()
    {
        $tanggal = $this->request->getPost('tgl_libur');
        $nama = $this->request->getPost('nama');
        $data = [
            'tanggal' => $tanggal,
            'nama' => $nama
        ];
        $insert = $this->liburModel->insert($data);
        if ($insert) {
            return redirect()->to(base_url('/capacity/calendar'))->withInput()->with('success', 'Tanggal Berhasil Di Input');
        } else {
            return redirect()->to(base_url('/capacity/calendar'))->withInput()->with('error', 'Gagal Input Tanggal');
        }
    }

    public function generatePlanning()
    {
        $tgl_awal = $this->request->getPost("tgl_awal");
        $tgl_akhir = $this->request->getPost("tgl_akhir");


        $dataJarum = $this->jarumModel->getJarum();
        $totalMesin = $this->jarumModel->getTotalMesinByJarum();

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
            $currentMonthOfYear = $startOfWeek->format('F');
            if ($currentMonth !== $currentMonthOfYear) {
                $currentMonth = $currentMonthOfYear;
                $weekCount = 1; // Reset week count
                $monthlyData[$currentMonth] = [];
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

            $monthlyData[$currentMonth][] = [
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
        $data = [
            'title' => 'Data Booking',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => 'active',
            'Jarum' => $dataJarum,
            'TotalMesin' => $totalMesin,
            'DaftarLibur' => $holidays,
            'weeklyRanges' => $monthlyData,
            'DaftarLibur' => $holidays,

        ];
        return view('Capacity/Calendar/generate', $data);
    }
}
