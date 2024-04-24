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

use function PHPUnit\Framework\returnSelf;

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

            $monthlyData[$currentMonth][] = [
                'week' => $weekCount,
                'start_date' => $startOfWeekFormatted,
                'end_date' => $endOfWeekFormatted,
                'number_of_days' => $numberOfDays,
                'holidays' => $weekHolidays,
                'nsmp' => $this->ApsPerstyleModel->getPlanJarum($cek, "NS-MP") ?? 0,
                'nsfp' => $this->ApsPerstyleModel->getPlanJarum($cek, "NS-FP") ?? 0,
                'nsps' => $this->ApsPerstyleModel->getPlanJarum($cek, "NS-PS") ?? 0,
                'smp' => $this->ApsPerstyleModel->getPlanJarum($cek, "S-MP") ?? 0,
                'sfp' => $this->ApsPerstyleModel->getPlanJarum($cek, "S-FP") ?? 0,
                'sps' => $this->ApsPerstyleModel->getPlanJarum($cek, "S-PS") ?? 0,
                'ssmp' => $this->ApsPerstyleModel->getPlanJarum($cek, "SS-MP") ?? 0,
                'ssfp' => $this->ApsPerstyleModel->getPlanJarum($cek, "SS-FP") ?? 0,
                'ssps' => $this->ApsPerstyleModel->getPlanJarum($cek, "SS-PS") ?? 0,
                'fmp' => $this->ApsPerstyleModel->getPlanJarum($cek, "F-MP") ?? 0,
                'ffp' => $this->ApsPerstyleModel->getPlanJarum($cek, "F-FP") ?? 0,
                'fps' => $this->ApsPerstyleModel->getPlanJarum($cek, "F-PS") ?? 0,
                'khmp' => $this->ApsPerstyleModel->getPlanJarum($cek, "KH-MP") ?? 0,
                'khfp' => $this->ApsPerstyleModel->getPlanJarum($cek, "KH-FP") ?? 0,
                'khps' => $this->ApsPerstyleModel->getPlanJarum($cek, "KH-PS") ?? 0,
                'tgmp' => $this->ApsPerstyleModel->getPlanJarum($cek, "TG-MP") ?? 0,
                'tgfp' => $this->ApsPerstyleModel->getPlanJarum($cek, "TG-FP") ?? 0,
                'tgps' => $this->ApsPerstyleModel->getPlanJarum($cek, "TG-PS") ?? 0,
                'glfl' => $this->ApsPerstyleModel->getPlanJarum($cek, "GL-FL") ?? 0,
                'glmt' => $this->ApsPerstyleModel->getPlanJarum($cek, "GL-MT") ?? 0,
                'glpt' => $this->ApsPerstyleModel->getPlanJarum($cek, "GL-PT") ?? 0,
                'glst' => $this->ApsPerstyleModel->getPlanJarum($cek, "GL-ST") ?? 0,
                'htst' => $this->ApsPerstyleModel->getPlanJarum($cek, "HT-ST") ?? 0,
                'htpl' => $this->ApsPerstyleModel->getPlanJarum($cek, "HT-pl") ?? 0,

            ];

            $weekCount++;
        }
        $get = [
            'jarum' => $jarum,
            'start' => $awal,
            'end' => $akhir,
        ];

        $KebMesin = [
            'fps' =>  $this->hitungMcOrder($get, 'F-PS'),
            'fmp' =>  $this->hitungMcOrder($get, 'F-MP'),
            'ffp' =>  $this->hitungMcOrder($get, 'F-FP'),
            'sps' =>  $this->hitungMcOrder($get, 'S-PS'),
            'smp' =>  $this->hitungMcOrder($get, 'S-MP'),
            'sfp' =>  $this->hitungMcOrder($get, 'S-FP'),
            'ssps' => $this->hitungMcOrder($get, 'SS-PS'),
            'ssmp' => $this->hitungMcOrder($get, 'SS-MP'),
            'ssfp' => $this->hitungMcOrder($get, 'SS-FP'),
            'nsps' => $this->hitungMcOrder($get, 'NS-PS'),
            'nsmp' => $this->hitungMcOrder($get, 'NS-MP'),
            'nsfp' => $this->hitungMcOrder($get, 'NS-FP'),
            'khps' => $this->hitungMcOrder($get, 'KH-PS'),
            'khmp' => $this->hitungMcOrder($get, 'KH-MP'),
            'khfp' => $this->hitungMcOrder($get, 'KH-FP'),
            'tgps' => $this->hitungMcOrder($get, 'TG-PS'),
            'tgmp' => $this->hitungMcOrder($get, 'TG-MP'),
            'tgfp' => $this->hitungMcOrder($get, 'TG-FP'),
            'glfl' => $this->hitungMcOrder($get, 'GL-FL'),
            'glmt' => $this->hitungMcOrder($get, 'GL-MT'),
            'glpt' => $this->hitungMcOrder($get, 'GL-PT'),
            'glst' => $this->hitungMcOrder($get, 'GL-ST'),
            'htst' => $this->hitungMcOrder($get, 'HT-ST'),
            'htpl' => $this->hitungMcOrder($get, 'HT-PL'),
        ];
        $kategori = $this->productModel->getKategori();
        $maxHari = max(array_column($KebMesin, 'JumlahHari'));
        $totalKebutuhanMC = 0;
        foreach ($KebMesin as $kebutuhanMesin) {
            $totalKebutuhanMC += $kebutuhanMesin['kebutuhanMc']; // Adjust this line based on the structure of your data
        }
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
            'kebMesin' => $KebMesin,
            'totalKebutuhan' => $totalKebutuhanMC,
            'start' => $awal,
            'end' => $akhir,
            'jarum' => $jarum,
            'jmlHari' => $maxHari,
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
            $nsps = $this->bookingModel->getPlanJarum($cek, 'NS-PS');
            $nsmp = $this->bookingModel->getPlanJarum($cek, 'NS-MP');
            $nsfp = $this->bookingModel->getPlanJarum($cek, 'NS-FP');
            $sps = $this->bookingModel->getPlanJarum($cek, 'S-PS');
            $smp = $this->bookingModel->getPlanJarum($cek, 'S-MP');
            $sfp = $this->bookingModel->getPlanJarum($cek, 'S-FP');
            $ssps = $this->bookingModel->getPlanJarum($cek, 'SS-PS');
            $ssmp = $this->bookingModel->getPlanJarum($cek, 'SS-MP');
            $ssfp = $this->bookingModel->getPlanJarum($cek, 'SS-FP');
            $khps = $this->bookingModel->getPlanJarum($cek, 'KH-PS');
            $khmp = $this->bookingModel->getPlanJarum($cek, 'KH-MP');
            $khfp = $this->bookingModel->getPlanJarum($cek, 'KH-FP');
            $fps = $this->bookingModel->getPlanJarum($cek, 'F-PS');
            $fmp = $this->bookingModel->getPlanJarum($cek, 'F-MP');
            $ffp = $this->bookingModel->getPlanJarum($cek, 'F-FP');
            $tgps = $this->bookingModel->getPlanJarum($cek, 'TG-PS');
            $tgfp = $this->bookingModel->getPlanJarum($cek, 'TG-MP');
            $tgmp = $this->bookingModel->getPlanJarum($cek, 'TG-FP');
            $glfl = $this->bookingModel->getPlanJarum($cek, 'GL-FL');
            $glmt = $this->bookingModel->getPlanJarum($cek, 'GL-MT');
            $glpt = $this->bookingModel->getPlanJarum($cek, 'GL-PT');
            $glst = $this->bookingModel->getPlanJarum($cek, 'GL-ST');
            $htst = $this->bookingModel->getPlanJarum($cek, 'HT-ST');
            $htpl = $this->bookingModel->getPlanJarum($cek, 'HT-PL');

            $monthlyData[$currentMonth][] = [
                'week' => $weekCount,
                'start_date' => $startOfWeekFormatted,
                'end_date' => $endOfWeekFormatted,
                'number_of_days' => $numberOfDays,
                'holidays' => $weekHolidays,
                'nsps' => $nsps ?? 0,
                'nsmp' => $nsmp ?? 0,
                'nsfp' => $nsfp ?? 0,
                'sps' => $sps ?? 0,
                'smp' => $smp ?? 0,
                'sfp' => $ssfp ?? 0,
                'ssps' => $ssps ?? 0,
                'ssmp' => $ssmp ?? 0,
                'ssfp' => $sfp ?? 0,
                'khps' => $khps ?? 0,
                'khmp' => $khmp ?? 0,
                'khfp' => $khfp ?? 0,
                'ffp' => $ffp ?? 0,
                'fmp' => $fmp ?? 0,
                'fps' => $fps ?? 0,
                'tgps' => $tgps ?? 0,
                'tgmp' => $tgmp ?? 0,
                'tgfp' => $tgfp ?? 0,
                'glfl' => $glfl ?? 0,
                'glmt' => $glmt ?? 0,
                'glpt' => $glpt ?? 0,
                'glst' => $glst ?? 0,
                'htst' => $htst ?? 0,
                'htpl' => $htpl ?? 0,
            ];

            $weekCount++;
        }
        $get = [
            'jarum' => $jarum,
            'start' => $awal,
            'end' => $akhir,
        ];

        $KebMesin = [
            'fps' =>  $this->hitungKebutuhanMC($get, 'F-PS'),
            'fmp' =>  $this->hitungKebutuhanMC($get, 'F-MP'),
            'ffp' =>  $this->hitungKebutuhanMC($get, 'F-FP'),
            'sps' =>  $this->hitungKebutuhanMC($get, 'S-PS'),
            'smp' =>  $this->hitungKebutuhanMC($get, 'S-MP'),
            'sfp' =>  $this->hitungKebutuhanMC($get, 'S-FP'),
            'ssps' => $this->hitungKebutuhanMC($get, 'SS-PS'),
            'ssmp' => $this->hitungKebutuhanMC($get, 'SS-MP'),
            'ssfp' => $this->hitungKebutuhanMC($get, 'SS-FP'),
            'nsps' => $this->hitungKebutuhanMC($get, 'NS-PS'),
            'nsmp' => $this->hitungKebutuhanMC($get, 'NS-MP'),
            'nsfp' => $this->hitungKebutuhanMC($get, 'NS-FP'),
            'khps' => $this->hitungKebutuhanMC($get, 'KH-PS'),
            'khmp' => $this->hitungKebutuhanMC($get, 'KH-MP'),
            'khfp' => $this->hitungKebutuhanMC($get, 'KH-FP'),
            'tgps' => $this->hitungKebutuhanMC($get, 'TG-PS'),
            'tgmp' => $this->hitungKebutuhanMC($get, 'TG-MP'),
            'tgfp' => $this->hitungKebutuhanMC($get, 'TG-FP'),
            'glfl' => $this->hitungKebutuhanMC($get, 'GL-FL'),
            'glmt' => $this->hitungKebutuhanMC($get, 'GL-MT'),
            'glpt' => $this->hitungKebutuhanMC($get, 'GL-PT'),
            'glst' => $this->hitungKebutuhanMC($get, 'GL-ST'),
            'htst' => $this->hitungKebutuhanMC($get, 'HT-ST'),
            'htpl' => $this->hitungKebutuhanMC($get, 'HT-PL'),
        ];
        $maxHari = max(array_column($KebMesin, 'JumlahHari'));
        $totalKebutuhanMC = 0;
        foreach ($KebMesin as $kebutuhanMesin) {
            $totalKebutuhanMC += $kebutuhanMesin['kebutuhanMc']; // Adjust this line based on the structure of your data
        }
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
            'kebMesin' => $KebMesin,
            'start' => $awal,
            'end' => $akhir,
            'jarum' => $jarum,
            'jmlHari' => $maxHari,
            'totalKebutuhan' => $totalKebutuhanMC,
        ];
        return view('Capacity/Calendar/calendar', $data);
    }

    private function hitungKebutuhanMC($get, $type)
    {
        $query = $this->bookingModel->hitungKebutuhanMC($get, $type);
        $value = [];
        $qtyTotal = 0;
        foreach ($query as $key => $data) {
            $qty1 = $data['sisa_booking'];
            $hari1 = $data['totalhari'];
            $deliv = $data['delivery'];
            $target = $data['konversi'];
            $type = $data['product_type'];
            $qtyTotal += $qty1;

            $value[] = [
                'kebutuhanMc' => ceil($qtyTotal / $target / $hari1 / 24),
                'JumlahHari' => $hari1,
                'delivery' => $deliv,
                'type' => $type
            ];
        }
        if (!$value) {
            $value =  ['kebutuhanMc' => 0, 'JumlahHari' => 0, 'delivery' => 0, 'type' => $type];
            return $value;
        } else {
            $kebutuhanMc = max(array_column($value, 'kebutuhanMc'));
            $result = array_filter($value, function ($val) use ($kebutuhanMc) {
                return $val['kebutuhanMc'] == $kebutuhanMc;
            });
            $hasil = reset($result);
            return $hasil;
        }
    }
    public function hitungMcOrder($get, $type)
    {
        $query = $this->ApsPerstyleModel->hitungMesin($get, $type);
        $value = [];
        $qtyTotal = 0;
        foreach ($query as $key => $data) {
            $qty1 = $data['sisa'];
            $hari1 = $data['totalhari'];
            $deliv = $data['delivery'];
            $target = $data['smv'];
            $type = $data['product_type'];
            $qtyTotal += $qty1;

            $value[] = [
                'kebutuhanMc' => ceil($qtyTotal / $target / $hari1 / 24),
                'JumlahHari' => $hari1,
                'delivery' => $deliv,
                'type' => $type
            ];
        }
        if (!$value) {
            $value =  ['kebutuhanMc' => 0, 'JumlahHari' => 0, 'delivery' => 0, 'type' => $type];
            return $value;
        } else {
            $kebutuhanMc = max(array_column($value, 'kebutuhanMc'));
            $result = array_filter($value, function ($val) use ($kebutuhanMc) {
                return $val['kebutuhanMc'] == $kebutuhanMc;
            });
            $hasil = reset($result);
            return $hasil;
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
    public function detailPlanningbook($judul)
    {
        $holidays = $this->liburModel->findAll();
        $dataJarum = $this->jarumModel->getJarum();
        $totalMesin = $this->jarumModel->getTotalMesinByJarum();
        $kebutuhanMC = $this->kebMc->getBooking();
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
            'active6' => '',
            'active7' => 'active',
            'Jarum' => $dataJarum,
            'TotalMesin' => $totalMesin,
            'DaftarLibur' => $holidays,
            'kebutuhanMc' => $kebutuhanMC,
            'chartstat' => $planning
        ];
        return view('Capacity/Calendar/detailbook', $data);
    }
}
