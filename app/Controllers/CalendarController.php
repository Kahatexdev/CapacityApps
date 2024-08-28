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
        if ($this->filters   = ['role' => ['capacity', 'planning', 'aps', 'god']] != session()->get('role')) {
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
        $stopMc = 0;
        $data = [
            'role' => session()->get('role'),
            'title' => session()->get('role') . ' Order',
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
            'stopMc' => $stopMc,
        ];
        return view(session()->get('role') . '/Calendar/index', $data);
    }
    public function planningBooking()
    {
        $holidays   = $this->liburModel->findAll();
        $dataJarum  = $this->jarumModel->getJarum();
        $totalMesin = $this->jarumModel->getTotalMesinByJarum();
        $kebutuhanMC = $this->kebMc->getBooking();
        $data = [
            'role' => session()->get('role'),
            'title' => session()->get('role') . ' Booking',
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
        return view(session()->get('role') . '/Calendar/booking', $data);
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
            $order = $this->ApsPerstyleModel->getPlanJarum($cek);
            foreach ($order as $order) {
                $monthlyData[$currentMonth][] = [
                    'week' => $weekCount,
                    'start_date' => $startOfWeekFormatted,
                    'end_date' => $endOfWeekFormatted,
                    'number_of_days' => $numberOfDays,
                    'holidays' => $weekHolidays,
                    $order['mastermodel'] => $order['total_qty'] != 0 ? number_format($order['total_qty'], 0, ',', '.') : '-',

                ];
            }
            $weekCount++;
        }
        $get = [
            'jarum' => $jarum,
            'start' => $awal,
            'end' => $akhir,
        ];

        $KebMesin =  $this->hitungMcOrder($get);

        $kategori = $this->productModel->getKategori();
        $maxHari = max(array_column($KebMesin, 'JumlahHari'));
        $totalKebutuhanMC = 0;
        foreach ($KebMesin as $kebutuhanMesin) {
            $totalKebutuhanMC += $kebutuhanMesin['kebutuhanMc']; // Adjust this line based on the structure of your data
        }
        $stopmc = max(array_column($KebMesin, 'stopmc'));
        $data = [
            'role' => session()->get('role'),
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
            'title' => session()->get('role') . ' Order',
            'stopmc' => $stopmc,
            'desk' => 'ORDER',
        ];
        return view(session()->get('role') . '/Calendar/calendar', $data);
    }
    public function planBooking($jarum)
    {
        $awal = strval($this->request->getPost("awal"));
        $akhir = strval($this->request->getPost("akhir"));

        $tgl_awal = strtotime($awal);
        $tgl_akhir = strtotime($akhir);

        $jumlahHari = ($tgl_akhir - $tgl_awal) / (60 * 60 * 24);

        $startDate = new \DateTime();
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
                'nsps' => $nsps != 0 ? number_format($nsps, 0, ',', '.') : '-',
                'nsmp' => $nsmp != 0 ? number_format($nsmp, 0, ',', '.') : '-',
                'nsfp' => $nsfp != 0 ? number_format($nsfp, 0, ',', '.') : '-',
                'sps' => $sps != 0 ? number_format($sps, 0, ',', '.') : '-',
                'smp' => $smp != 0 ? number_format($smp, 0, ',', '.') : '-',
                'sfp' => $sfp != 0 ? number_format($sfp, 0, ',', '.') : '-',
                'ssps' => $ssps != 0 ? number_format($ssps, 0, ',', '.') : '-',
                'ssmp' => $ssmp != 0 ? number_format($ssmp, 0, ',', '.') : '-',
                'ssfp' => $ssfp != 0 ? number_format($ssfp, 0, ',', '.') : '-',
                'khps' => $khps != 0 ? number_format($khps, 0, ',', '.') : '-',
                'khmp' => $khmp != 0 ? number_format($khmp, 0, ',', '.') : '-',
                'khfp' => $khfp != 0 ? number_format($khfp, 0, ',', '.') : '-',
                'ffp' => $ffp != 0 ? number_format($ffp, 0, ',', '.') : '-',
                'fmp' => $fmp != 0 ? number_format($fmp, 0, ',', '.') : '-',
                'fps' => $fps != 0 ? number_format($fps, 0, ',', '.') : '-',
                'tgps' => $tgps != 0 ? number_format($tgps, 0, ',', '.') : '-',
                'tgmp' => $tgmp != 0 ? number_format($tgmp, 0, ',', '.') : '-',
                'tgfp' => $tgfp != 0 ? number_format($tgfp, 0, ',', '.') : '-',
                'glfl' => $glfl != 0 ? number_format($glfl, 0, ',', '.') : '-',
                'glmt' => $glmt != 0 ? number_format($glmt, 0, ',', '.') : '-',
                'glpt' => $glpt != 0 ? number_format($glpt, 0, ',', '.') : '-',
                'glst' => $glst != 0 ? number_format($glst, 0, ',', '.') : '-',
                'htst' => $htst != 0 ? number_format($htst, 0, ',', '.') : '-',
                'htpl' => $htpl != 0 ? number_format($htpl, 0, ',', '.') : '-',
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
        $stopmc = max(array_column($KebMesin, 'stopmc'));
        // Di sini Anda mungkin perlu memanggil model lain untuk mendapatkan data lain yang diperlukan
        $kategori = $this->productModel->getKategori();

        $data = [
            'role' => session()->get('role'),
            'title' => session()->get('role') . ' System',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => 'active',
            'weeklyRanges' => $monthlyData,
            'DaftarLibur' => $holidays,
            'kategoriProduk' => $kategori,
            'kebMesin' => $KebMesin,
            'start' => $awal,
            'end' => $akhir,
            'jarum' => $jarum,
            'jmlHari' => $maxHari,
            'totalKebutuhan' => $totalKebutuhanMC,
            'stopmc' => $stopmc,
            'desk' => 'BOOKING',
        ];
        return view(session()->get('role') . '/Calendar/calendar', $data);
    }

    private function hitungKebutuhanMC($get, $type)
    {
        $query = $this->bookingModel->hitungKebutuhanMC($get, $type);
        $value = [];
        $qtyTotal = 0;
        foreach ($query as $key => $data) {
            $qty1 = $data['sisa_booking'];
            $hari1 = intval($data['totalhari']);
            $deliv = $data['delivery'];
            $target = $data['konversi'];
            $type = $data['product_type'];
            $qtyTotal += $qty1;
            if ($hari1 == null) {
                $value =  ['kebutuhanMc' => 0, 'JumlahHari' => 0, 'delivery' => 0, 'type' => $type, 'stopmc' => 0];
                return $value;
            } else {
                $value[] = [
                    'kebutuhanMc' => ceil($qtyTotal / $target / $hari1 / 24),
                    'qty' => ceil($qtyTotal),
                    'target' => ceil($target),
                    'JumlahHari' => $hari1,
                    'delivery' => $deliv,
                    'type' => $type
                ];
            }
        }

        if (!$value) {
            $value =  ['kebutuhanMc' => 0, 'JumlahHari' => 0, 'delivery' => 0, 'type' => $type, 'stopmc' => 0];
            return $value;
        } else {
            $kebutuhanMc = max(array_column($value, 'kebutuhanMc'));
            $smc = max(array_column($value, 'delivery'));

            $result = array_filter($value, function ($val) use ($kebutuhanMc) {
                return $val['kebutuhanMc'] == $kebutuhanMc;
            });

            $hasil = reset($result);
            $hasil['stopmc'] = $smc; // Menyimpan nilai $smc ke dalam $hasil
            return $hasil;
        }
    }
    public function hitungMcOrder($get)
    {
        $query = $this->ApsPerstyleModel->hitungMesin($get);
        $value = [];
        $qtyTotal = 0;
        foreach ($query as $key => $data) {
            $qty1 = $data['sisa'];
            $hari1 = intval($data['totalhari']);
            $deliv = $data['delivery'];
            $target = ((86400 / (intval($data['smv'])  * 0.8)) / 24);

            $type = $data['mastermodel'];
            $qtyTotal += $qty1;

            $value[] = [
                'kebutuhanMc' => ceil(intval($qtyTotal / 24) / $target / $hari1),
                'smv' => $data['smv'],
                'qty' => ceil($qtyTotal),
                'target' => ceil($target),
                'JumlahHari' => $hari1,
                'delivery' => $deliv,
                'type' => $type
            ];
        }
        dd($value);
        if (!$value) {
            $value =  ['kebutuhanMc' => 0, 'JumlahHari' => 0, 'delivery' => 0, 'type' => $type, 'stopmc' => 0];
            return $value;
        } else {
            $kebutuhanMc = max(array_column($value, 'kebutuhanMc'));
            $smc = max(array_column($value, 'delivery'));
            $result = array_filter($value, function ($val) use ($kebutuhanMc) {

                return $val['kebutuhanMc'] == $kebutuhanMc;
            });
            $hasil = reset($result);
            $hasil['stopmc'] = $smc;
            return $hasil;
        }
    }


    public function inputLibur()
    {
        $tanggal = $this->request->getPost('tgl_libur');
        $nama = $this->request->getPost('nama');
        $data = [
            'role' => session()->get('role'),
            'tanggal' => $tanggal,
            'nama' => $nama
        ];
        $insert = $this->liburModel->insert($data);
        if ($insert) {
            return redirect()->to(base_url(session()->get('role') . '/Calendar'))->withInput()->with('success', 'Tanggal Berhasil Di Input');
        } else {
            return redirect()->to(base_url(session()->get('role') . '/Calendar'))->withInput()->with('error', 'Gagal Input Tanggal');
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
                'mesin' => $val['mesin'],
                'startmc' => $val['start_mesin'],
                'stopmc' => $val['stop_mesin']
            ];
            $jumlahMc += (int)$val['mesin'];
        }
        $data = [
            'role' => session()->get('role'),
            session()->get('role') . '' => $groupedData,
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
        return view(session()->get('role') . '/Calendar/detail', $data);
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
                'mesin' => $val['mesin'],
                'startmc' => $val['start_mesin'],
                'stopmc' => $val['stop_mesin']
            ];
            $jumlahMc += (int)$val['mesin'];
        }
        $data = [
            'role' => session()->get('role'),
            session()->get('role') . '' => $groupedData,
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
        return view(session()->get('role') . '/Calendar/detailbook', $data);
    }
}
