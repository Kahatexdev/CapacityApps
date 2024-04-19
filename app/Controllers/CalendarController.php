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
use App\Models\KebutuhanMesinModel;
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
    protected $kebMc;

    public function __construct()
    {
        $this->kebMc = new KebutuhanMesinModel();
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
    public function planningorder()
    {
        $holidays = $this->liburModel->findAll();
        $dataJarum = $this->jarumModel->getJarum();
        $totalMesin = $this->jarumModel->getTotalMesinByJarum();
        $kebutuhanMC = $this->kebMc->getOrder();
        $data = [
            'title' => 'Planning Order',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => 'active',
            'active7' => '',
            'Jarum' => $dataJarum,
            'TotalMesin' => $totalMesin,
            'DaftarLibur' => $holidays,
            'kebutuhanMc' => $kebutuhanMC
        ];
        return view('Capacity/Calendar/index', $data);
    }
    public function planningBooking()
    {
        $holidays   = $this->liburModel->findAll();
        $dataJarum  = $this->jarumModel->getJarum();
        $totalMesin = $this->jarumModel->getTotalMesinByJarum();
        $kebutuhanMC = $this->kebMc->getBooking();
        $data = [
            'title' => 'Planning Booking',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => 'active',
            'Jarum' => $dataJarum,
            'TotalMesin' => $totalMesin,
            'DaftarLibur' => $holidays,
            'kebutuhanMc' => $kebutuhanMC
        ];
        return view('Capacity/Calendar/booking', $data);
    }
    public function planOrder($jarum)
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
        $get = [
            'jarum' => $jarum,
            'start' => $awal,
            'end' => $akhir,
        ];

        $normalMC = $this->normalCalc($get);
        $sneakerMC = $this->sneakerCalc($get);
        $kneeMc = $this->kneeCalc($get);
        $footiesMc = $this->footiesCalc($get);
        $shaftlessMc = $this->shaftlessCalc($get);
        $tightMc = $this->tightCalc($get);
        $totalKebutuhan = $normalMC + $sneakerMC + $kneeMc + $footiesMc + $shaftlessMc + $tightMc;

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
            'active7' => '',
            'weeklyRanges' => $monthlyData,
            'DaftarLibur' => $holidays,
            'kategoriProduk' => $kategori,
            'mesinNormal' => $normalMC,
            'mesinSneaker' => $sneakerMC,
            'mesinKnee' => $kneeMc,
            'mesinFooties' => $footiesMc,
            'mesinShaftless' => $shaftlessMc,
            'mesinTight' => $tightMc,
            'totalKebutuhan' => $totalKebutuhan,
            'start' => $awal,
            'end' => $akhir,
            'jarum' => $jarum,
            'jmlHari' => $jumlahHari,
            'title' => 'Planning Order'
        ];

        return view('Capacity/Calendar/calendar', $data);
    }
    public function planBooking($jarum)
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


            $nsmp = $this->bookingModel->getPlanJarumNSMP($cek);
            $nsps = $this->bookingModel->getPlanJarumNSPS($cek);
            $nsfp = $this->bookingModel->getPlanJarumNSFP($cek);
            $sps = $this->bookingModel->getPlanJarumSPS($cek);
            $smp = $this->bookingModel->getPlanJarumSMP($cek);
            $sfp = $this->bookingModel->getPlanJarumSFP($cek);
            $ssps = $this->bookingModel->getPlanJarumSSPS($cek);
            $ssmp = $this->bookingModel->getPlanJarumSSMP($cek);
            $ssfp = $this->bookingModel->getPlanJarumSSFP($cek);
            $khps = $this->bookingModel->getPlanJarumKHPS($cek);
            $khmp = $this->bookingModel->getPlanJarumKHMP($cek);
            $khfp = $this->bookingModel->getPlanJarumKHFP($cek);
            $fps = $this->bookingModel->getPlanJarumFPS($cek);
            $fmp = $this->bookingModel->getPlanJarumFMP($cek);
            $ffp = $this->bookingModel->getPlanJarumFFP($cek);
            $tgps = $this->bookingModel->getPlanJarumTGPS($cek);
            $tgfp = $this->bookingModel->getPlanJarumTGFP($cek);
            $tgmp = $this->bookingModel->getPlanJarumTGMP($cek);
            $glfl = $this->bookingModel->getPlanJarumGLFL($cek);
            $glmt = $this->bookingModel->getPlanJarumGLMT($cek);
            $glpt = $this->bookingModel->getPlanJarumGLPT($cek);
            $glst = $this->bookingModel->getPlanJarumGLST($cek);
            $htst = $this->bookingModel->getPlanJarumHTST($cek);
            $htpl = $this->bookingModel->getPlanJarumHTPL($cek);
            $nsmpQty = $nsmp ?? 0;
            $nspsQty = $nsps ?? 0;
            $nsfpQty = $nsfp ?? 0;
            $spsQty = $sps ?? 0;
            $smpQty = $smp ?? 0;
            $sfpQty = $sfp ?? 0;
            $sspsQty = $ssps ?? 0;
            $ssmpQty = $ssmp ?? 0;
            $ssfpQty = $ssfp ?? 0;
            $khpsQty = $khps ?? 0;
            $khmpQty = $khmp ?? 0;
            $khfpQty = $khfp ?? 0;
            $ffpQty = $ffp ?? 0;
            $fpsQty = $fps ?? 0;
            $fmpQty = $fmp ?? 0;
            $tgpsQty = $tgps ?? 0;
            $tgmpQty = $tgmp ?? 0;
            $tgfpQty = $tgfp ?? 0;
            $glflQty = $glfl ?? 0;
            $glmtQty = $glmt ?? 0;
            $glptQty = $glpt ?? 0;
            $glstQty = $glst ?? 0;
            $htstQty = $htst ?? 0;
            $htplQty = $htpl ?? 0;

            $monthlyData[$currentMonth][] = [
                'week' => $weekCount,
                'start_date' => $startOfWeekFormatted,
                'end_date' => $endOfWeekFormatted,
                'number_of_days' => $numberOfDays,
                'holidays' => $weekHolidays,
                'nsps' => $nspsQty,
                'nsmp' => $nsmpQty,
                'nsfp' => $nsfpQty,
                'sps' => $spsQty,
                'smp' => $smpQty,
                'sfp' => $ssfpQty,
                'ssps' => $sspsQty,
                'ssmp' => $ssmpQty,
                'ssfp' => $sfpQty,
                'khps' => $khpsQty,
                'khmp' => $khmpQty,
                'khfp' => $khfpQty,
                'ffp' => $ffpQty,
                'fmp' => $fmpQty,
                'fps' => $fpsQty,
                'tgps' => $tgpsQty,
                'tgmp' => $tgmpQty,
                'tgfp' => $tgfpQty,
                'glfl' => $glflQty,
                'glmt' => $glmtQty,
                'glpt' => $glptQty,
                'glst' => $glstQty,
                'htst' => $htstQty,
                'htpl' => $htplQty,
            ];

            $weekCount++;
        }
        $get = [
            'jarum' => $jarum,
            'start' => $awal,
            'end' => $akhir,
        ];


        $normalMC = $this->normalCalc($get);
        dd($normalMC);
        $sneakerMC = $this->sneakerCalc($get);
        $kneeMc = $this->kneeCalc($get);
        $footiesMc = $this->footiesCalc($get);
        $shaftlessMc = $this->shaftlessCalc($get);
        $tightMc = $this->tightCalc($get);
        $totalKebutuhan = $normalMC + $sneakerMC + $kneeMc + $footiesMc + $shaftlessMc + $tightMc;

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
            'active7' => '',
            'weeklyRanges' => $monthlyData,
            'DaftarLibur' => $holidays,
            'kategoriProduk' => $kategori,
            'mesinNormal' => $normalMC,
            'mesinSneaker' => $sneakerMC,
            'mesinKnee' => $kneeMc,
            'mesinFooties' => $footiesMc,
            'mesinShaftless' => $shaftlessMc,
            'mesinTight' => $tightMc,
            'totalKebutuhan' => $totalKebutuhan,
            'start' => $awal,
            'end' => $akhir,
            'jarum' => $jarum,
            'jmlHari' => $jumlahHari
        ];

        return view('Capacity/Calendar/calendar', $data);
    }

    private function normalCalc($cek)
    {
        $query = $this->ApsPerstyleModel->normalCalc($cek);
        if (!$query) {
            $result = 0;
            return $result;
        } else {
            $qtyTotal = 0;
            $jarum = $cek['jarum'];
            $target = 0;
            switch ($jarum) {
                case 'JC84':
                    $target = 25 * 24;
                    break;
                case 'JC96':
                    $target = 19 * 24;
                    break;
                case 'JC108':
                    $target = 18 * 24;
                    break;
                case 'JC120':
                    $target = 17 * 24;
                    break;
                case 'JC144':
                    $target = 14 * 24;
                    break;
                case 'JC168':
                    $target = 13 * 24;
                    break;
                case 'JC200':
                    $target = 11 * 24;
                    break;
                case 'TJ96':
                    $target = 16 * 24;
                    break;
                case 'TJ108':
                    $target = 14 * 24;
                    break;
                case 'TJ120':
                    $target = 12 * 24;
                    break;
                case 'TJ144':
                    $target = 11 * 24;
                    break;
                case 'TJ168':
                    $target = 9 * 24;
                    break;

                default:
                    $target = 1;
            }
            $value = [];
            foreach ($query as $key => $data) {
                $qty1 = $data['sisa'];
                $hari1 = $data['totalhari'];
                $deliv = $data['delivery'];
                $qtyTotal += $qty1;

                $value[] = [
                    'kebutuhanMc' => ceil($qtyTotal / $target / $hari1),
                    'JumlahHari' => $hari1,
                    'delivery' => $deliv
                ];
            }
            $kebutuhanMc = max(array_column($value, 'kebutuhanMc'));
            $result = $kebutuhanMc;
            return $result;
        }
    }
    private function sneakerCalc($cek)
    {
        $query = $this->ApsPerstyleModel->sneakerCalc($cek);
        if (!$query) {
            $result = 0;
            return $result;
        } else {
            $qtyTotal = 0;
            $jarum = $cek['jarum'];
            $target = 0;
            switch ($jarum) {
                case 'JC84':
                    $target = 30 * 24;
                    break;
                case 'JC96':
                    $target = 22.8 * 24;
                    break;
                case 'JC108':
                    $target = 21.6 * 24;
                    break;
                case 'JC120':
                    $target = 20.4 * 24;
                    break;
                case 'JC144':
                    $target = 16.8 * 24;
                    break;
                case 'JC168':
                    $target = 15.6 * 24;
                    break;
                case 'JC200':
                    $target = 13.2 * 24;
                    break;
                case 'TJ96':
                    $target = 19.2 * 24;
                    break;
                case 'TJ108':
                    $target = 16.8 * 24;
                    break;
                case 'TJ120':
                    $target = 14.4 * 24;
                    break;
                case 'TJ144':
                    $target = 13.2 * 24;
                    break;
                case 'TJ168':
                    $target = 10.8 * 24;
                    break;

                default:
                    $target = 1;
            }
            $value = [];
            foreach ($query as $key => $data) {
                $qty1 = $data['sisa'];
                $hari1 = $data['totalhari'];
                $deliv = $data['delivery'];
                $qtyTotal += $qty1;

                $value[] = [
                    'kebutuhanMc' => ceil($qtyTotal / $target / $hari1),
                    'JumlahHari' => $hari1,
                    'delivery' => $deliv
                ];
            }
            $kebutuhanMc = max(array_column($value, 'kebutuhanMc'));

            $result = $kebutuhanMc;
            return $result;
        }
    }
    private function kneeCalc($cek)
    {
        $query = $this->ApsPerstyleModel->kneeCalc($cek);
        if (!$query) {
            $result = 0;
            return $result;
        } else {
            $qtyTotal = 0;
            $jarum = $cek['jarum'];
            $target = 0;
            switch ($jarum) {
                case 'JC84':
                    $target = 16.6 * 24;
                    break;
                case 'JC96':
                    $target = 12.8 * 24;
                    break;
                case 'JC108':
                    $target = 12 * 24;
                    break;
                case 'JC120':
                    $target = 11.3 * 24;
                    break;
                case 'JC144':
                    $target = 9.3 * 24;
                    break;
                case 'JC168':
                    $target = 8.6 * 24;
                    break;
                case 'JC200':
                    $target = 7.3 * 24;
                    break;
                case 'TJ96':
                    $target = 10.6 * 24;
                    break;
                case 'TJ108':
                    $target = 9.3 * 24;
                    break;
                case 'TJ120':
                    $target = 8 * 24;
                    break;
                case 'TJ144':
                    $target = 7.3 * 24;
                    break;
                case 'TJ168':
                    $target = 6 * 24;
                    break;
                default:
                    $target = 1;
            }
            $value = [];
            foreach ($query as $key => $data) {
                $qty1 = $data['sisa'];
                $hari1 = $data['totalhari'];
                $deliv = $data['delivery'];
                $qtyTotal += $qty1;

                $value[] = [
                    'kebutuhanMc' => ceil($qtyTotal / $target / $hari1),
                    'JumlahHari' => $hari1,
                    'delivery' => $deliv
                ];
            }
            $kebutuhanMc = max(array_column($value, 'kebutuhanMc'));

            $result = $kebutuhanMc;
            return $result;
        }
    }
    private function footiesCalc($cek)
    {
        $query = $this->ApsPerstyleModel->footiesCalc($cek);
        if (!$query) {
            $result = 0;
            return $result;
        } else {
            $qtyTotal = 0;
            $jarum = $cek['jarum'];
            $target = 0;
            switch ($jarum) {
                case 'JC84':
                    $target = 32.5 * 24;
                    break;
                case 'JC96':
                    $target = 24.7 * 24;
                    break;
                case 'JC108':
                    $target = 23.4 * 24;
                    break;
                case 'JC120':
                    $target = 22.1 * 24;
                    break;
                case 'JC144':
                    $target = 18.2 * 24;
                    break;
                case 'JC168':
                    $target = 16.9 * 24;
                    break;
                case 'JC200':
                    $target = 14.3 * 24;
                    break;
                case 'TJ96':
                    $target = 20.8 * 24;
                    break;
                case 'TJ108':
                    $target = 18.2 * 24;
                    break;
                case 'TJ120':
                    $target = 15.6 * 24;
                    break;
                case 'TJ144':
                    $target = 14.3 * 24;
                    break;
                case 'TJ168':
                    $target = 11.7 * 24;
                    break;
                default:
                    $target = 1;
            }
            $value = [];
            foreach ($query as $key => $data) {
                $qty1 = $data['sisa'];
                $hari1 = $data['totalhari'];
                $deliv = $data['delivery'];
                $qtyTotal += $qty1;

                $value[] = [
                    'kebutuhanMc' => ceil($qtyTotal / $target / $hari1),
                    'JumlahHari' => $hari1,
                    'delivery' => $deliv
                ];
            }
            $kebutuhanMc = max(array_column($value, 'kebutuhanMc'));

            $result = $kebutuhanMc;
            return $result;
        }
    }
    private function shaftlessCalc($cek)
    {
        $query = $this->ApsPerstyleModel->shaftlessCalc($cek);
        if (!$query) {
            $result = 0;
            return $result;
        } else {
            $qtyTotal = 0;
            $jarum = $cek['jarum'];
            $target = 0;
            switch ($jarum) {
                case 'JC84':
                    $target = 32.5 * 24;
                    break;
                case 'JC96':
                    $target = 24.7 * 24;
                    break;
                case 'JC108':
                    $target = 23.4 * 24;
                    break;
                case 'JC120':
                    $target = 22.1 * 24;
                    break;
                case 'JC144':
                    $target = 18.2 * 24;
                    break;
                case 'JC168':
                    $target = 16.9 * 24;
                    break;
                case 'JC200':
                    $target = 14.3 * 24;
                    break;
                case 'TJ96':
                    $target = 20.8 * 24;
                    break;
                case 'TJ108':
                    $target = 18.2 * 24;
                    break;
                case 'TJ120':
                    $target = 15.6 * 24;
                    break;
                case 'TJ144':
                    $target = 14.3 * 24;
                    break;
                case 'TJ168':
                    $target = 11.7 * 24;
                    break;
                default:
                    $target = 1;
            }
            $value = [];
            foreach ($query as $key => $data) {
                $qty1 = $data['sisa'];
                $hari1 = $data['totalhari'];
                $deliv = $data['delivery'];
                $qtyTotal += $qty1;

                $value[] = [
                    'kebutuhanMc' => ceil($qtyTotal / $target / $hari1),
                    'JumlahHari' => $hari1,
                    'delivery' => $deliv
                ];
            }
            $kebutuhanMc = max(array_column($value, 'kebutuhanMc'));

            $result = $kebutuhanMc;
            return $result;
        }
    }
    private function tightCalc($cek)
    {
        $query = $this->ApsPerstyleModel->tightCalc($cek);
        if (!$query) {
            $result = 0;
            return $result;
        } else {
            $qtyTotal = 0;
            $jarum = $cek['jarum'];
            $target = 0;
            switch ($jarum) {
                case 'JC84':
                    $target = 16 * 24;
                    break;
                case 'JC96':
                    $target = 7.6 * 24;
                    break;
                case 'JC108':
                    $target = 7.2 * 24;
                    break;
                case 'JC120':
                    $target = 6.8 * 24;
                    break;
                case 'JC144':
                    $target = 5.6 * 24;
                    break;
                case 'JC168':
                    $target = 5.2 * 24;
                    break;
                case 'JC200':
                    $target = 4.4 * 24;
                    break;
                case 'TJ96':
                    $target = 6.4 * 24;
                    break;
                case 'TJ108':
                    $target = 5.6 * 24;
                    break;
                case 'TJ120':
                    $target = 4.8 * 24;
                    break;
                case 'TJ144':
                    $target = 4.4 * 24;
                    break;
                case 'TJ168':
                    $target = 3.6 * 24;
                    break;
                default:
                    $target = 1;
            }
            $value = [];
            foreach ($query as $key => $data) {
                $qty1 = $data['sisa'];
                $hari1 = $data['totalhari'];
                $deliv = $data['delivery'];
                $qtyTotal += $qty1;

                $value[] = [
                    'kebutuhanMc' => ceil($qtyTotal / $target / $hari1),
                    'JumlahHari' => $hari1,
                    'delivery' => $deliv
                ];
            }
            $kebutuhanMc = max(array_column($value, 'kebutuhanMc'));

            $result = $kebutuhanMc;
            return $result;
        }
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
            return redirect()->to(base_url('/capacity/Calendar'))->withInput()->with('success', 'Tanggal Berhasil Di Input');
        } else {
            return redirect()->to(base_url('/capacity/Calendar'))->withInput()->with('error', 'Gagal Input Tanggal');
        }
    }

    public function detailPlanning($judul)
    {
        $holidays = $this->liburModel->findAll();
        $dataJarum = $this->jarumModel->getJarum();
        $totalMesin = $this->jarumModel->getTotalMesinByJarum();
        $kebutuhanMC = $this->kebMc->getOrder();

        $planning = $this->kebMc->getData($judul);
        $tgl_plan = $this->kebMc->tglPlan($judul);

        $range = $this->kebMc->range($judul);
        $groupedData = [];
        $jumlahMc = 0;
        foreach ($planning as $val) {
            $judul = $val['judul'];
            if (!array_key_exists($judul, $groupedData)) {
                $groupedData[$judul] = [];
            }
            $groupedData[$judul][] = [
                'jarum' => $val['jarum'],
                'mesin' => $val['mesin']
            ];
            $jumlahMc += (int)$val['mesin'];
        }
        $data = [
            'planning' => $groupedData,
            'jumlahMc' => $jumlahMc,
            'judul' => $judul,
            'range' => $range,
            'tglplan' => $tgl_plan,
            'title' => 'Plan Order',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => 'active',
            'active7' => '',
            'Jarum' => $dataJarum,
            'TotalMesin' => $totalMesin,
            'DaftarLibur' => $holidays,
            'kebutuhanMc' => $kebutuhanMC,
            'chartstat' => $planning
        ];
        return view('Capacity/Calendar/detail', $data);
    }
}
