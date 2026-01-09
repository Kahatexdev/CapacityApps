<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Week;
use PhpOffice\PhpSpreadsheet\IOFactory;


class DeffectController extends BaseController
{


    public function __construct()
    {

        if ($this->filters   = ['role' => ['capaciity']] != session()->get('role')) {
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

    public function datadeffect()
    {

        $master = $this->deffectModel->findAll();
        //$databs = $this->bsModel->getDataBs();
        $dataBuyer = $this->orderModel->getBuyer();

        $currentMonth = (int) date('n');
        $currentYear  = (int) date('Y');

        $namaBulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];

        $bulanList = [];
        for ($month = 1; $month <= 12; $month++) {
            $bulanList[] = [
                'value' => sprintf('%04d-%02d', $currentYear, $month), // contoh: 2025-01
                'label' => $namaBulan[$month] . ' ' . $currentYear
            ];
        }



        $data = [
            'role' => session()->get('role'),
            'title' => session()->get('role') . ' System',
            'active1' => 'active',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'kode' => $master,
            'dataBuyer' => $dataBuyer,
            'filterBulan' => $bulanList,

            //'databs' => $databs
        ];
        return view(session()->get('role') . '/Deffect/databs', $data);
    }
    public function perbaikanArea()
    {

        $master = $this->deffectModel->findAll();
        //$databs = $this->bsModel->getDataBs();
        $dataBuyer = $this->orderModel->getBuyer();

        $currentMonth = (int) date('n');
        $currentYear  = (int) date('Y');

        $namaBulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];

        $bulanList = [];
        for ($month = 1; $month <= 12; $month++) {
            $bulanList[] = [
                'value' => sprintf('%04d-%02d', $currentYear, $month), // contoh: 2025-01
                'label' => $namaBulan[$month] . ' ' . $currentYear
            ];
        }


        $data = [
            'role' => session()->get('role'),
            'username' => session()->get('username'),
            'title' => session()->get('role') . ' System',
            'active1' => 'active',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'kode' => $master,
            'dataBuyer' => $dataBuyer,
            'filterBulan' => $bulanList,

            //'databs' => $databs
        ];
        return view(session()->get('role') . '/Perbaikan/perbaikan', $data);
    }
    public function inputKode()
    {

        $kode = $this->request->getPost('kode');
        $keterangan = $this->request->getPost('keterangan');
        $data = [
            'kode_deffect' => $kode,
            'Keterangan' => $keterangan
        ];
        try {
            $input = $this->deffectModel->insert($data); // Assuming save() is a better method name
            if ($input == false) {
                return redirect()->to(session()->get('role') . '/datadeffect')->with('success', 'Data Deffect Berhasil Di input');
            } else {
                return redirect()->to(session()->get('role') . '/datadeffect')->with('error', 'Data Deffect Gagal Di input');
            }
        } catch (\Exception $e) {
            return redirect()->to(session()->get('role') . '/datadeffect')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function viewDataBs()
    {
        $master = $this->deffectModel->findAll();
        $dataBuyer = $this->orderModel->getBuyer();

        $awal = $this->request->getPost('awal');
        $akhir = $this->request->getPost('akhir');
        $pdk = $this->request->getPost('pdk');
        $area = $this->request->getPost('area');
        $buyer = $this->request->getPost('buyer');
        // dd($buyer);
        $theData = [
            'awal' => $awal,
            'akhir' => $akhir,
            'pdk' => $pdk,
            'area' => $area,
            'buyer' => $buyer,
        ];
        $getData = $this->bsModel->getDataBsFilter($theData);
        $total = $this->bsModel->totalBs($theData);
        $chartData = $this->bsModel->chartData($theData);

        $currentMonth = (int) date('n');
        $currentYear  = (int) date('Y');

        $namaBulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];

        $bulanList = [];
        for ($month = 1; $month <= 12; $month++) {
            $bulanList[] = [
                'value' => sprintf('%04d-%02d', $currentYear, $month), // contoh: 2025-01
                'label' => $namaBulan[$month] . ' ' . $currentYear
            ];
        }

        $data = [
            'role' => session()->get('role'),
            'title' => ' Data BS',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'kode' => $master,
            'databs' => $getData,
            'awal' => $awal,
            'akhir' => $akhir,
            'pdk' => $pdk,
            'area' => $area,
            'totalbs' => $total,
            'chart' => $chartData,
            'dataBuyer' => $dataBuyer,
            'filter' => $theData,
            'filterBulan' => $bulanList,
        ];

        return view(session()->get('role') . '/Deffect/bstabel', $data);
    }

    public function resetbs()
    {
        $pdk = $this->request->getPost('pdk');

        $idaps = $this->ApsPerstyleModel->getIdAps($pdk);
        // Cek apakah $idaps tidak kosong
        if (!empty($idaps)) {
            $qtyBs = $this->bsModel->getTotalBs($idaps);

            foreach ($qtyBs as $idap) {
                $bs = $idap['qty'];
                $sisa = $this->ApsPerstyleModel->getSisaOrder($idap['idapsperstyle']);
                $newSisa = $sisa - $bs;
                $this->ApsPerstyleModel->update($idap['idapsperstyle'], ['sisa' => $newSisa]);
            }

            $this->bsModel->deleteSesuai($idaps);
        } else {
            // Jika $idaps kosong, lakukan penanganan lain, misalnya logging atau redirect dengan pesan error
            return redirect()->to(base_url(session()->get('role') . '/datadeffect'))->withInput()->with('error', 'Tidak ada data yang ditemukan untuk di-reset.');
        }
        return redirect()->to(base_url(session()->get('role') . '/datadeffect'))->withInput()->with('success', 'Data Berhasil di reset');
    }

    public function resetbsarea()
    {
        $area  = $this->request->getPost('area') ?? null;
        $awal  = $this->request->getPost('awal');
        $akhir = $this->request->getPost('akhir');

        $rows = $this->bsModel->getDataForReset($awal, $akhir, $area);
        if (empty($rows)) {
            return redirect()
                ->to(base_url(session()->get('role') . '/datadeffect'))
                ->with('error', 'Tidak ada data yang ditemukan untuk di-reset.');
        }

        $db = $this->db;

        // ===============================
        // CONTAINER
        // ===============================
        $apsReduceMap = []; // idaps => totalQtyBS
        $bsIds        = []; // idbs list

        foreach ($rows as $row) {
            $idAps = (int) $row['idapsperstyle'];
            $qty   = (int) $row['qty'];

            $apsReduceMap[$idAps] = ($apsReduceMap[$idAps] ?? 0) + $qty;
            $bsIds[] = (int) $row['idbs'];
        }

        if (empty($apsReduceMap) || empty($bsIds)) {
            return redirect()
                ->to(base_url(session()->get('role') . '/datadeffect'))
                ->with('error', 'Data tidak valid untuk di-reset.');
        }

        $db->transStart();

        /**
         * UPDATE APS (per ID, tapi sudah teragregasi)
         */
        foreach ($apsReduceMap as $idAps => $totalQty) {
            $this->ApsPerstyleModel
                ->set('sisa', "sisa - {$totalQty}", false) // atomic
                ->where('idapsperstyle', $idAps)
                ->update();
        }

        /**
         * DELETE BS (BATCH)
         */
        $this->bsModel
            ->whereIn('idbs', $bsIds)
            ->delete();

        $db->transComplete();

        // ===============================
        // TRANSACTION CHECK
        // ===============================
        if ($db->transStatus() === false) {
            return redirect()
                ->to(base_url(session()->get('role') . '/datadeffect'))
                ->with('error', 'Gagal melakukan reset data BS.');
        }

        return redirect()
            ->to(base_url(session()->get('role') . '/datadeffect'))
            ->with('success', 'Data BS berhasil di-reset.');
    }

    public function getBsMesin()
    {
        $bulan = $this->request->getGet('bulan');
        $tahun = $this->request->getGet('tahun');
        $area  = $this->request->getGet('area');

        if (!$bulan || !$tahun) {
            return $this->response->setJSON(['error' => 'Bulan dan Tahun wajib diisi']);
        }

        try {
            $filters = [
                'bulan' => $bulan,
                'tahun' => $tahun,
                'area'  => $area
            ];
            $data = $this->bsMesinModel->getbsMesinDaily($filters);

            return $this->response->setJSON($data);
        } catch (\Exception $e) {
            return $this->response->setJSON(['error' => $e->getMessage()]);
        }
    }
    public function viewPerbaikan()
    {
        $master = $this->deffectModel->findAll();
        $dataBuyer = $this->orderModel->getBuyer();

        $awal = $this->request->getPost('awal');
        $akhir = $this->request->getPost('akhir');
        $pdk = $this->request->getPost('pdk');
        $area = $this->request->getPost('area');
        $buyer = $this->request->getPost('buyer');
        // dd($buyer);
        $theData = [
            'awal' => $awal,
            'akhir' => $akhir,
            'pdk' => $pdk,
            'area' => $area,
            'buyer' => $buyer,
        ];
        $getData = $this->perbaikanModel->getDataPerbaikanFilter($theData);
        $total = $this->perbaikanModel->totalPerbaikan($theData);
        $chartData = $this->perbaikanModel->chartData($theData);

        // filter bulan sumamry global
        $currentMonth = (int) date('n');
        $currentYear  = (int) date('Y');

        $namaBulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];

        $bulanList = [];
        for ($month = 1; $month <= 12; $month++) {
            $bulanList[] = [
                'value' => sprintf('%04d-%02d', $currentYear, $month), // contoh: 2025-01
                'label' => $namaBulan[$month] . ' ' . $currentYear
            ];
        }

        $data = [
            'role' => session()->get('role'),
            'username' => session()->get('username'),
            'title' => ' Data Perbaikan',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'kode' => $master,
            'databs' => $getData,
            'awal' => $awal,
            'akhir' => $akhir,
            'pdk' => $pdk,
            'area' => $area,
            'totalbs' => $total,
            'chart' => $chartData,
            'dataBuyer' => $dataBuyer,
            'filter' => $theData,
            'filterBulan' => $bulanList,
        ];

        return view(session()->get('role') . '/Perbaikan/tabelperbaikan', $data);
    }
    public function summaryGlobalPbArea()
    {
        $master = $this->deffectModel->findAll();
        $dataBuyer = $this->orderModel->getBuyer();

        $data = [
            'role' => session()->get('role'),
            'username' => session()->get('username'),
            'title' => session()->get('role') . ' System',
            'active1' => 'active',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'kode' => $master,
            'dataBuyer' => $dataBuyer,

            //'databs' => $databs
        ];
        return view(session()->get('role') . '/Perbaikan/summaryGlobal', $data);
    }
    public function getPerbaikan()
    {
        $bulan = $this->request->getGet('bulan');
        $tahun = $this->request->getGet('tahun');
        $area  = $this->request->getGet('area');

        if (!$bulan || !$tahun) {
            return $this->response->setJSON(['error' => 'Bulan dan Tahun wajib diisi']);
        }

        try {
            $data = $this->perbaikanModel->chartDataByMonth($bulan, $tahun, $area);
            return $this->response->setJSON($data);
        } catch (\Exception $e) {
            return $this->response->setJSON(['error' => $e->getMessage()]);
        }
    }
}
