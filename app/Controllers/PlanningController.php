<?php

namespace App\Controllers;

use DateTime;
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
use App\Models\DetailPlanningModel;
use App\Services\orderServices;
use App\Models\MonthlyMcModel;




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
    protected $MesinPlanningModel;
    protected $orderServices;
    protected $DetailPlanningModel;
    protected $globalModel;



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
        $this->DetailPlanningModel = new DetailPlanningModel();
        $this->globalModel = new MonthlyMcModel();

        $this->orderServices = new orderServices();
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
        $mcJalan = $this->jarumModel->getJalanMesinPerArea();
        $totalMc = $this->jarumModel->totalMc();
        $bulan = date('m');
        $totalMesin = $this->jarumModel->getJalanMesinPerArea();
        $jarum = $this->jarumModel->jarumPerArea();
        $area = $this->jarumModel->getArea();

        $data = [
            'role' => session()->get('role'),
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
            'Area' => $totalMesin,
            'order' => $this->ApsPerstyleModel->getTurunOrder($bulan),
            'jarum' => $jarum,
            'area' => $area,

        ];
        return view(session()->get('role') . '/index', $data);
    }
    public function order()
    {
        $totalMesin = $this->jarumModel->getTotalMesinByJarum();
        $monthlyData = $this->ApsPerstyleModel->getMonthlyData();
        $data = [
            'role' => session()->get('role'),
            'title' => 'Data Order',
            'active1' => '',
            'active2' => '',
            'active3' => 'active',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'acive7' => '',
            'chartData' => $monthlyData,
            'TotalMesin' => $totalMesin,
        ];
        return view(session()->get('role') . '/Order/ordermaster', $data);
    }

    public function assignareal()
    {
        $deliv = $this->request->getPost('delivery');
        $pdk = $this->request->getPost("no_model");
        $jarum = $this->request->getPost("jarum");
        $area = $this->request->getPost("area");
        if ($area == 'Gedung 1' || $area == 'Gedung 2') {
            $pu = 'MJ';
        } else {
            $pu = 'CJ';
        }

        foreach ($deliv as $del) {

            $data = [
                'role' => session()->get('role'),
                'mastermodel' => $pdk,
                'jarum' => $jarum,
                'area' => $area,
                'delivery' => $del,
                'pu' => $pu
            ];

            $assign = $this->ApsPerstyleModel->asignAreal($data);
            if (!$assign) {
                return redirect()->to(base_url(session()->get('role') . '/detailPdk/' . $pdk . '/' . $jarum))->withInput()->with('error', 'Gagal Assign Area');
            }
        }
        return redirect()->to(base_url(session()->get('role') . '/detailPdk/' . $pdk . '/' . $jarum))->withInput()->with('success', 'Berhasil Assign Area');
    }
    public function assignarealall()
    {
        $model = $this->request->getPost("no_model");
        $area = $this->request->getPost("area");
        $jarum = $this->request->getPost("jarum");
        $pu = 'Pu Belum Di Pilih';
        if ($area == 'Gedung 1' || $area == 'Gedung 2') {
            $pu = 'MJ';
        } else {
            $pu = 'CJ';
        }
        // Simpan ke sistem Capacity dulu
        $data = [
            'role' => session()->get('role'),
            'mastermodel' => $model,
            'area' => $area,
            'pu' => $pu
        ];
        $assign = $this->ApsPerstyleModel->asignarealall($data);

        // Kirim ke API MaterialSystem dengan cURL
        $apiUrl = 'http://172.23.44.14/MaterialSystem/public/api/assignArea';
        $postData = [
            'model' => $model,
            'area' => $area
        ];

        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch); // Tangkap error jika ada
        curl_close($ch);

        $apiResult = json_decode($response, true);


        if ($assign && $httpCode == 200) {
            return redirect()->to(base_url(session()->get('role') . '/detailPdk/' . $model . '/' . $jarum))
                ->with('success', 'Berhasil Assign Area di Capacity dan Material');
        } elseif ($assign && $httpCode == 404) {
            return redirect()->to(base_url(session()->get('role') . '/detailPdk/' . $model . '/' . $jarum))
                ->with('warning', 'Berhasil Assign Area di Capacity, tapi Order belum ada di Material');
        } else {
            return redirect()->to(base_url(session()->get('role') . '/detailPdk/' . $model . '/' . $jarum))
                ->with('error', 'Gagal Assign Area ' . $httpCode);
        }
    }
    public function listplanning()
    {
        $dataBooking = $this->KebutuhanMesinModel->listPlan();
        $data = [
            'role' => session()->get('role'),
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
        return view(session()->get('role') . '/Planning/listPlanning', $data);
    }
    public function listplanningAps()
    {
        $dataBooking = $this->KebutuhanMesinModel->listPlan();
        $data = [
            'role' => session()->get('role'),
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
        return view(session()->get('role') . '/Planning/listPlanning', $data);
    }
    public function splitarea()
    {
        $idaps = $this->request->getPost('idaps');
        $pdk = $this->request->getPost('noModel');
        $deliv = $this->request->getPost('delivery');
        $jarum = $this->request->getPost('jarum');
        $update = [
            'factory' => $this->request->getPost('area1'),
            'qty' => $this->request->getPost('qty1'),
            'sisa' => $this->request->getPost('qty1'),
        ];
        $insert = [
            'machinetypeid' => $this->request->getPost('jarum'),
            'no_order' => $this->request->getPost('order'),
            'size' => $this->request->getPost('style'),
            'country' => $this->request->getPost('country'),
            'mastermodel' => $pdk,
            'delivery' => $deliv,
            'qty' => $this->request->getPost('qty2'),
            'sisa' => $this->request->getPost('qty2'),
            'factory' => $this->request->getPost('area2'),
            'production_unit' => "PU Belum Di Pilih",
            'smv' => $this->request->getPost('smv'),
            'seam' => $this->request->getPost('seam')

        ];
        $u = $this->ApsPerstyleModel->update($idaps, $update);
        if ($u) {
            $this->ApsPerstyleModel->insert($insert);
            return redirect()->to(base_url(session()->get('role') . '/detailPdk/' . $pdk . '/' . $jarum))->withInput()->with('success', 'Berhasil Split Style Area');
        } else {
            return redirect()->to(base_url(session()->get('role') . '/detailModelPlanning/' . $pdk . '/' .  $jarum))->withInput()->with('error', 'Gagal Membagi Style');
        }
    }
    public function detaillistplanning($judul)
    {
        $dataplan = $this->KebutuhanMesinModel->jarumPlan($judul);
        $data = [
            'role' => session()->get('role'),
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
        return view(session()->get('role') . '/Planning/detailPlanning', $data);
    }
    public function detaillistplanningAps($judul)
    {
        $dataplan = $this->KebutuhanMesinModel->jarumPlan($judul);
        $data = [
            'role' => session()->get('role'),
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
        return view(session()->get('role') . '/Planning/detailPlanning', $data);
    }

    public function pickmachine($jarum)
    {
        $datamc = $this->jarumModel->listmachine($jarum);
        $mesin = $this->request->getPost('mesin');
        $status = $this->request->getPost('deskripsi');
        $id = $this->request->getPost('id');
        $data = [
            'role' => session()->get('role'),
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
        return view(session()->get('role') . '/Planning/pilihMesin', $data);
    }

    public function savemachine($id)
    {
        $data = $this->MesinPlanningModel->savemachine($id);
        $judulData = $this->KebutuhanMesinModel->find($id);
        $judul = $judulData ? $judulData['judul'] : null;
        if ($data) {
            return redirect()->to(base_url(session()->get('role') . '/detaillistplanning/' . $judul . '/' . $id))->withInput()->with('success', 'Success Pick Machine Area');
        } else {
            return redirect()->to(base_url(session()->get('role') . '/detaillistplanning/' . $judul . '/' . $id))->withInput()->with('error', 'Error Pick Machine Area');
        }
    }

    public function viewdetail($id)
    {

        $datamc = $this->MesinPlanningModel->getDataPlanning($id);
        $judulData = $this->KebutuhanMesinModel->find($id);
        $judul = $judulData ? $judulData['judul'] : null;
        $mesin = $judulData ? $judulData['mesin'] : null;
        $jarum = $judulData ? $judulData['jarum'] : null;
        $data = [
            'role' => session()->get('role'),
            'title' => 'View Detail Machine Choosen',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => 'active',
            'active6' => '',
            'active7' => '',
            'datamc' => $datamc,
            'judul' => $judul,
            'mesin' => $mesin,
            'jarum' => $jarum,

        ];
        return view(session()->get('role') . '/Planning/mesinSelected', $data);
    }
    public function viewdetailAps($id)
    {

        $datamc = $this->MesinPlanningModel->getDataPlanning($id);
        $judulData = $this->KebutuhanMesinModel->find($id);
        $judul = $judulData ? $judulData['judul'] : null;
        $mesin = $judulData ? $judulData['mesin'] : null;
        $jarum = $judulData ? $judulData['jarum'] : null;
        $data = [
            'role' => session()->get('role'),
            'title' => 'View Detail Machine Choosen',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => 'active',
            'active6' => '',
            'active7' => '',
            'datamc' => $datamc,
            'judul' => $judul,
            'mesin' => $mesin,
            'jarum' => $jarum,

        ];
        return view(session()->get('role') . '/Planning/mesinSelected', $data);
    }
    public function editarea()
    {
        $area = $this->request->getPost('area');
        if ($area == 'Gedung 1' || $area == 'Gedung 2') {
            $pu = 'MJ';
        } else {
            $pu = 'CJ';
        }
        $pdk = $this->request->getPost('pdk');
        $size = $this->request->getPost('size');
        $deliv = $this->request->getPost('deliv');
        $id = $this->ApsPerstyleModel->getIdByDeliv($pdk, $size, $deliv);
        $jarum = $this->request->getPost('jarum');
        foreach ($id as $i) {
            $aps = $i['idapsperstyle'];
            $update = $this->ApsPerstyleModel->update($aps, ['factory' => $area, 'production_unit' => $pu]);
            if (!$update) {
                return redirect()->to(base_url(session()->get('role') . '/detailPdk/' . $pdk . '/' . $jarum))->withInput()->with('error', 'Gagal Mengubah Area');
            }
        }
        return redirect()->to(base_url(session()->get('role') . '/detailPdk/' . $pdk . '/' . $jarum))->withInput()->with('success', 'Berhasil Mengubah Area');
    }
    public function editqtyarea()
    {
        $qty = $this->request->getPost('qty');
        $sisa = $this->request->getPost('sisa');
        $id = $this->request->getPost('id');
        $pdk = $this->request->getPost('pdk');
        $size = $this->request->getPost('size');
        $deliv = $this->request->getPost('deliv');
        $jarum = $this->request->getPost('jarum');

        $update = $this->ApsPerstyleModel->update($id, ['qty' => $qty, 'sisa' => $sisa]);
        if (!$update) {
            return redirect()->to(base_url(session()->get('role') . '/detailPdk/' . $pdk . '/' . $jarum))->withInput()->with('error', 'Gagal Mengubah Qty');
        } else {


            return redirect()->to(base_url(session()->get('role') . '/detailPdk/' . $pdk . '/' . $jarum))->withInput()->with('success', 'Berhasil Mengubah Qty');
        }
    }
    public function jalanmesin()
    {
        $role = session()->get('role');
        $bulanIni = [];
        $currentDate = new DateTime(); // Tanggal sekarang
        $dataPlan = $this->globalModel->getPlan();
        for ($i = 0; $i < 12; $i++) {
            $bulanIni[] = $currentDate->format('F Y'); // Format bulan dan tahun (e.g., "August 2024")
            $currentDate->modify('+1 month'); // Tambah satu bulan
        }
        $data = [
            'role' => session()->get('role'),
            'title' => 'Jalan Mesin',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'bulan' => $bulanIni,
            'plan' => $dataPlan
        ];
        return view($role . '/Planning/jalanmesin', $data);
    }
    public function jalanmesindetail($bulan)
    {
        $role = session()->get('role');

        $areas = $this->jarumModel->getArea();
        $totalArea = [];
        foreach ($areas as $ar) {
            $totalArea[$ar] = $this->jarumModel->totalMcArea($ar);
        }

        // Parse the $bulan string to a DateTime object
        $date = DateTime::createFromFormat('F-Y', $bulan);
        if (!$date) {
            throw new \Exception("Invalid date format. Please use 'F-Y' format.");
        }

        $bulanIni = $date->format('F-Y');
        $startDate = new \DateTime($date->format('Y-m-01')); // First day of the given month
        $endDate = (clone $startDate)->modify('last day of this month'); // Last day of the month

        $monthlyData = [];
        $weekCount = 1; // Inisialisasi minggu
        $LiburModel = new LiburModel();
        $holidays = $LiburModel->findAll(); // Ambil data libur

        while ($startDate <= $endDate) {
            // Tentukan akhir minggu (hari Minggu)
            $endOfWeek = (clone $startDate)->modify('Sunday this week');

            // Tentukan akhir bulan dari tanggal awal saat ini
            $endOfMonth = new \DateTime($startDate->format('Y-m-t')); // Akhir bulan saat ini

            if ($endOfWeek > $endOfMonth) {
                $endOfWeek = clone $endOfMonth; // Akhiri minggu di akhir bulan
            }

            // Hitung jumlah hari di minggu ini
            $numberOfDays = $startDate->diff($endOfWeek)->days + 1;

            // Hitung libur minggu ini
            $weekHolidays = array_filter($holidays, function ($holiday) use ($startDate, $endOfWeek) {
                $holidayDate = new \DateTime($holiday['tanggal']);
                return $holidayDate >= $startDate && $holidayDate <= $endOfWeek;
            });

            $holidaysCount = count($weekHolidays);
            $numberOfDays -= $holidaysCount; // Kurangi jumlah hari dengan jumlah libur

            $monthlyData[] = [
                'week' => $weekCount,
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endOfWeek->format('Y-m-d'),
                'number_of_days' => $numberOfDays,
                'holidays' => array_map(function ($holiday) {
                    return [
                        'nama' => $holiday['nama'],
                        'tanggal' => (new \DateTime($holiday['tanggal']))->format('d-F'),
                    ];
                }, $weekHolidays),
            ];

            // Perbarui tanggal awal untuk minggu berikutnya
            $startDate = (clone $endOfWeek)->modify('+1 day');
            $weekCount++;
        }

        $jarum = $this->jarumModel->getAreaAndJarum();
        $kebutuhanMesin = [];
        $outputDz = []; // Initialize outputDz array

        // Fetch sisa orders efficiently
        foreach ($monthlyData as $wk) {
            foreach ($areas as $ar) {
                $outputDz[$wk['week']][$ar] = 0; // Initialize the outputDz for each week and area
                foreach ($jarum as $jr) {
                    $weekNumber = $wk['week'];
                    $sisaOrder = $this->ApsPerstyleModel->ambilSisaOrder($ar, $wk['start_date'], $jr['jarum']);

                    $kebutuhanMesin[$weekNumber][$ar][$jr['jarum']] = $sisaOrder['totalKebMesin'] ?? 0;
                    $outputDz[$weekNumber][$ar] += $sisaOrder['outputDz'] ?? 0; // Summing outputDz per week per area
                }
            }
        }

        // Debugging output removed for production
        $data = [
            'role' => $role,
            'title' => 'Planning Jalan MC ' . $bulanIni,
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'bulan' => $bulanIni,

        ];

        return view($role . '/Planning/planningjalanMCPerBulan', $data);
    }
    public function monthlyMachine($bulan)
    {
        $role = session()->get('role');
        $date = DateTime::createFromFormat('F-Y', $bulan);
        $bulanIni = $date->format('F-Y');
        $awalBulan = $date->format('Y-m-01');
        $akhirBulan = date('Y-m-t', strtotime('+2 months'));
        $filteredArea = $this->jarumModel->getArea();
        $area = array_filter($filteredArea, function ($item) {
            return strpos($item, 'Gedung') === false &&
                strpos($item, 'SAMPLE') === false &&
                strpos($item, 'WAREHOUSE') === false;
        });
        $area = array_values($area);
        $monthlyData = [];
        foreach ($area as $ar) {
            $mesin = $this->jarumModel->areaMc($ar);
            $totalMesin = 0;
            $planningMc = 0;
            $outputDz = 0;
            $operator = 0;
            $montir = 0;
            $inLine = 0;
            $wly = 0;
            foreach ($mesin as $jarum) {
                $sisaOrder = $this->ApsPerstyleModel->ambilSisaOrder($ar, $awalBulan, $jarum['jarum']);
                $monthlyData[$ar][$jarum['jarum']]['kebutuhanMesin'] = $sisaOrder['totalKebMesin'];
                $monthlyData[$ar][$jarum['jarum']]['output'] = $sisaOrder['totalKebMesin'] * $jarum['target'];
                $monthlyData[$ar][$jarum['jarum']]['target'] = $jarum['target'];
                $monthlyData[$ar][$jarum['jarum']]['jr'] = $jarum['jarum'];
                $totalMesin += $jarum['total'];
                $planningMc += $sisaOrder['totalKebMesin'];
                $outputDz +=   $monthlyData[$ar][$jarum['jarum']]['output'];
            }
            // Perhitungan operator dan montir
            if ($ar == 'KK8J') {
                $operator = ceil((($planningMc / 12) + ($planningMc / 12) / 7) * 3);
            } else {
                $operator = ceil((($planningMc / 20) + ($planningMc / 20) / 7) * 3);
            }
            $montir = ceil((($planningMc / 50) + ($planningMc / 50) / 7) * 3);
            $inLine = (($val = ceil(($planningMc / 80 * 3) + (($planningMc / 80 * 3) / 7))) > 0 && $val <= 4) ? 4 : $val;
            $wly = (($val = ceil(($planningMc / 180 * 3) + (($planningMc / 180 * 3) / 7))) > 0 && $val <= 4) ? 4 : $val;

            $monthlyData[$ar]['totalMesin'] = $totalMesin;
            $monthlyData[$ar]['planningMc'] = $planningMc;
            $monthlyData[$ar]['outputDz'] = $outputDz;
            $monthlyData[$ar]['operator'] = round($operator); // Dibulatkan agar lebih masuk akal
            $monthlyData[$ar]['montir'] = round($montir);
            $monthlyData[$ar]['inLine'] = round($inLine);
            $monthlyData[$ar]['wly'] = round($wly);
        }
        $totalAllMesin = 0;

        $totalOutput = 0;
        $totalMcPlanning = 0;

        foreach ($monthlyData as $data) {
            $totalAllMesin += $data['totalMesin'];
            $totalOutput += $data['outputDz'];
            $totalMcPlanning += $data['planningMc'];
        }
        $totalKebGloves = 0; // Initialize outside the loop
        foreach ($monthlyData['KK8J'] as $data) {
            if (is_array($data) && isset($data['kebutuhanMesin'])) {
                $totalKebGloves += $data['kebutuhanMesin'];
            }
        }
        $totalKebSock = $totalMcPlanning - $totalKebGloves;
        $totalMcSocks = $this->jarumModel->totalMcSock();
        $totalMcSocks = intval($totalMcSocks['total']);
        $totalMcGloves = $totalAllMesin - $totalMcSocks;

        $persenSocks = round(($totalKebSock / $totalMcSocks) * 100);
        $persenGloves = round(($totalKebGloves / $totalMcGloves) * 100);
        $persenTotal = round(($totalMcPlanning / $totalAllMesin) * 100);
        $summary = [
            'totalMc' => $totalAllMesin,
            'OutputTotal' => $totalOutput,
            'totalPlanning' => $totalMcPlanning,
            'totalPersen' => $persenTotal,

            'mcSocks' => $totalMcSocks,
            'planMcSocks' => $totalKebSock,
            'persenSocks' => $persenSocks,

            'mcGloves' => $totalMcGloves,
            'planMcGloves' => $totalKebGloves,
            'persenGloves' => $persenGloves
        ];
        $statusOrder = $this->orderServices->statusOrder($bulan);

        // print(json_encode($statusOrder, JSON_PRETTY_PRINT));
        $data = [
            'role' => $role,
            'title' =>  $bulanIni,
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'bulan' => $bulanIni,
            'data' => $monthlyData,
            'summary' => $summary,
            'statusOrder' => $statusOrder
        ];

        return view($role . '/Planning/monthlyMachine', $data);
    }
    public function getModelData()
    {
        if ($this->request->isAJAX()) { // Pastikan ini adalah permintaan AJAX
            $delivery = $this->request->getGet('delivery'); // Ambil data dari parameter 'delivery'
            $model = $this->request->getGet('model'); // Ambil data dari parameter 'model'
            $jarum = $this->request->getGet('jarum'); // Ambil data dari parameter 'model'
            $area = $this->request->getGet('area'); // Ambil data dari parameter 'model'

            // Validasi input
            if (empty($delivery) || empty($model)) {
                return $this->response->setJSON([
                    'error' => 'Invalid input data.',
                ])->setStatusCode(400); // 400: Bad Request
            }
            $getData = [
                'delivery' => $delivery,
                'model' => $model,
                'jarum' => $jarum,
                'area' => $area,
            ];
            // Ambil data dari database (sesuaikan dengan logika bisnis Anda)
            $result = $this->ApsPerstyleModel->getSisaForPlanning($getData);

            if (!empty($result) && isset($result[0])) {
                $data = $result[0]; // Ambil elemen pertama dari array hasil
                $target = round(3600 / $data['smv']);
                $response = [
                    'qty' => (float) $data['qty'], // Pastikan tipe data sesuai
                    'sisa' => (float) $data['sisa'],
                    'smv' => (float) $target
                ];
                return $this->response->setJSON($response);
            } else {
                // Jika data tidak ditemukan
                return $this->response->setJSON([
                    'error' => 'Data not found.',
                ])->setStatusCode(404); // 404: Not Found
            }
        }

        // Jika bukan permintaan AJAX, tampilkan 403 Forbidden
        return $this->response->setStatusCode(403)->setBody('Forbidden');
    }
    public function pindahjarum($pageid)
    {
        $role = session()->get('role');
        $idPdk = $this->request->getPost('id_detail');
        $idJarum = $this->request->getPost('jarumname');
        $update = $this->DetailPlanningModel->update($idPdk, ['id_pln_mc' => $idJarum]);

        if ($update) {
            return redirect()->to(base_url($role . '/detailplnmc/' . $pageid))->with('success', 'Model berhasil Dipindahkan');
        } else {
            return redirect()->to(base_url($role . '/detailplnmc/' . $pageid))->with('error', 'Model Gagal Dipindahkan');
        }
    }
}
