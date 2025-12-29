<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use Throwable;
use PhpOffice\PhpSpreadsheet\IOFactory;
use DateTime;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class OEEController extends BaseController
{
    public function index()
    {
        $area = $this->jarumModel->getArea();
        $latest = $this->downtimeModel->getLatestData()['tanggal']
            ?? date('Y-m-d');
        $data = [
            'role' => session()->get('role'),
            'title' => 'Capacity System',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'area' => $area,
            'latest' => $latest
        ];
        return view(session()->get('role') . '/Oee/index', $data);
    }
    public function downloadTemplate()
    {
        $filePath = FCPATH . 'templateExcel/format import downtime.xlsx';

        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'Template file not found.');
        }

        $fileName = 'formatImportDowntime.xlsx';

        return $this->response->download($filePath, null)->setFileName($fileName);
    }


    public function import()
    {
        $excelRows = $this->request->getFile('file');
        if (empty($excelRows)) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Data kosong'
            ]);
        }

        $this->db->transBegin();

        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($excelRows);
            $worksheet = $spreadsheet->getActiveSheet();

            $area = $worksheet->getCell('B2')->getValue();
            $excelValue = $worksheet->getCell('B3')->getValue();

            if (Date::isDateTime($worksheet->getCell('B3'))) {
                $tanggal = Date::excelToDateTimeObject($excelValue)
                    ->format('Y-m-d');
            } else {
                $tanggal = null; // atau throw exception
            }
            $startRow   = 7;
            $highestRow = $worksheet->getHighestRow();

            $grouped = [];

            for ($row = $startRow; $row <= $highestRow; $row++) {

                $noMc  = trim($worksheet->getCell("A{$row}")->getValue());
                $jarum = trim($worksheet->getCell("B{$row}")->getValue());
                $menit = (int) $worksheet->getCell("E{$row}")->getValue();
                $bd    = trim($worksheet->getCell("F{$row}")->getValue());
                $ket   = strtoupper(trim($worksheet->getCell("G{$row}")->getValue()));

                // skip row kosong / aneh
                if (!$noMc || $menit <= 0) {
                    continue;
                }

                $key = implode('|', [$area, $tanggal, $jarum, $noMc]);

                if (!isset($grouped[$key])) {
                    $grouped[$key] = [
                        'area'           => $area,
                        'tanggal'        => $tanggal,
                        'jarum'          => $jarum,
                        'no_mc'          => $noMc,
                        'total_time'     => 0,
                        'total_mt'       => 0,
                        'keterangan_arr' => [],
                        'breakdown_arr'  => []
                    ];
                }

                // simpan histori
                if ($ket) {
                    $grouped[$key]['keterangan_arr'][] = "{$ket}({$menit})";
                }

                if ($bd) {
                    $grouped[$key]['breakdown_arr'][] = "{$bd}({$menit})";
                }

                // hitung waktu
                if ($ket === 'MT') {
                    $grouped[$key]['total_mt'] += $menit;
                } else {
                    $grouped[$key]['total_time'] += $menit;
                }
            }

            // -----------------------------
            // 2. FINAL CALCULATION
            // -----------------------------
            $insertData = [];
            $prodBsMap = $this->produksiModel
                ->getProduksiDanBsBulk($area, $tanggal);
            // dd($prodBsMap);
            foreach ($grouped as $g) {

                $loadingTime   = 1440 - $g['total_mt'];
                $operatingTime = $loadingTime - $g['total_time'];

                if ($loadingTime <= 0 || $operatingTime <= 0) {
                    throw new \Exception("Waktu tidak valid MC {$g['no_mc']}");
                }

                $key = implode('|', [
                    $g['area'],
                    $g['tanggal'],
                    $g['no_mc'],
                    $g['jarum']
                ]);

                $prodDefect = $prodBsMap[$key] ?? [
                    'prod' => 0,
                    'bs'   => 0,
                    'smv'  => 0
                ];
                $productionTime =
                    (($prodDefect['bs'] * $prodDefect['smv']) / 60) +
                    (($prodDefect['prod'] * $prodDefect['smv']) / 60);

                $quality = ($prodDefect['prod'] + $prodDefect['bs']) > 0
                    ? $prodDefect['prod'] / ($prodDefect['prod'] + $prodDefect['bs'])
                    : 0;

                $performance = $operatingTime > 0
                    ? $productionTime / $operatingTime
                    : 0;

                $availability = $operatingTime / $loadingTime;
                $oee = $quality * $performance * $availability;

                $insertData[] = [
                    'area'           => $g['area'],
                    'tanggal'        => $g['tanggal'],
                    'jarum'          => $g['jarum'],
                    'no_mc'          => $g['no_mc'],
                    'total_time'     => $g['total_time'],
                    'loading_time'   => $loadingTime,
                    'operating_time' => $operatingTime,
                    'breakdown'      => implode(', ', $g['breakdown_arr']),
                    'keterangan'     => implode(', ', $g['keterangan_arr']),
                    'quality'        => round($quality * 100, 2),
                    'performance'    => round($performance * 100, 2),
                    'availability'   => round($availability * 100, 2),
                    'oee'            => round($oee * 100, 2),
                    'created_at'     => date('Y-m-d H:i:s')
                ];
            }

            // dd($insertData);
            // -----------------------------
            // 3. INSERT

            // -----------------------------
            if (!$this->downtimeModel->insertBatch($insertData)) {
                throw new \Exception('Gagal insert downtime');
            }


            // 🔥 COMMIT
            $this->db->transCommit();

            return $this->response->setJSON([
                'status'  => true,
                'message' => 'Import downtime berhasil',
                'total'   => count($insertData)
            ]);
        } catch (Throwable $e) {

            // ❌ ROLLBACK
            $this->db->transRollback();

            log_message('error', '[IMPORT DOWNTIME] ' . $e->getMessage());

            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Import gagal',
                'error'   => $e->getMessage()
            ]);
        }
    }
    public function fetchData()
    {
        $tanggal = $this->request->getGet('tanggal');
        $area    = $this->request->getGet('area');

        if (!$tanggal || !$area) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Tanggal dan Area wajib diisi'
            ]);
        }

        // ================= SUMMARY =================
        $summary = $this->downtimeModel->getOeeSummary($tanggal, $area);
        $averageMonth = $this->downtimeModel->averageMonth($tanggal, $area);
        // ================= DETAIL TABLE =================
        $detail = $this->downtimeModel->getOeeDetail($tanggal, $area);

        return $this->response->setJSON([
            'status' => true,
            'filter' => [
                'tanggal' => $tanggal,
                'area' => $area
            ],
            'summary' => $summary,
            'detail' => $detail,
            'average' => $averageMonth
        ]);
    }
}
