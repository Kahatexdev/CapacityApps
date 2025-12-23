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
            $startRow = 7;
            $area = $worksheet->getCell('B2')->getValue();
            $tanggal = $worksheet->getCell('B3')->getValue();

            dd($tanggal);
            $grouped = [];
            foreach ($excelRows as $row) {

                $key = implode('|', [
                    $row['area'],
                    $row['tanggal'],
                    $row['jarum'],
                    $row['no_mc']
                ]);

                if (!isset($grouped[$key])) {
                    $grouped[$key] = [
                        'area'           => $row['area'],
                        'tanggal'        => $row['tanggal'],
                        'jarum'          => $row['jarum'],
                        'no_mc'          => $row['no_mc'],
                        'total_time'     => 0,
                        'total_mt'       => 0,
                        'keterangan_arr' => [],
                        'breakdown_arr'  => []
                    ];
                }

                $menit = (int) $row['menit'];
                $ket   = strtoupper(trim($row['keterangan']));
                $bd    = $row['breakdown'] ?? null;

                $grouped[$key]['keterangan_arr'][] = "{$ket}({$menit})";

                if (!empty($bd)) {
                    $grouped[$key]['breakdown_arr'][] = "{$bd}({$menit})";
                }

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

            foreach ($grouped as $g) {

                $loadingTime   = 1440 - $g['total_mt'];
                $operatingTime = $loadingTime - $g['total_time'];

                if ($loadingTime < 0 || $operatingTime < 0) {
                    throw new \Exception('Waktu hasil perhitungan tidak valid');
                }

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
                    'created_at'     => date('Y-m-d H:i:s')
                ];
            }

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
}
