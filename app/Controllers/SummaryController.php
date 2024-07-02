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
use LengthException;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Week;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\{Border, Alignment, Fill};

class SummaryController extends BaseController
{
    protected $filters;
    protected $jarumModel;
    protected $productModel;
    protected $produksiModel;
    protected $bookingModel;
    protected $orderModel;
    protected $ApsPerstyleModel;
    protected $liburModel;

    public function __construct() {
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

    public function excelSummaryPerTgl()
    {
        // Ambil data dari request
        $buyer = $this->request->getPost('buyer');
        $area = $this->request->getPost('area');
        $jarum = $this->request->getPost('jarum');
        $pdk = $this->request->getPost('pdk');
        $awal = $this->request->getPost('awal');
        $akhir = $this->request->getPost('akhir');

        // Ambil data summary per tanggal dari model
        $data = [
            'buyer' => $buyer,
            'area' => $area,
            'jarum' => $jarum,
            'pdk' => $pdk,
            'awal' => $awal,
            'akhir' => $akhir,
        ];
        $dataSummaryPertgl = $this->produksiModel->getdataSummaryPertgl($data);
        dd($dataSummaryPertgl);
        // Ambil tanggal produksi unik
        $tgl_produksi = [];
        foreach ($dataSummaryPertgl as $item) {
            $tgl_produksi[$item['tgl_produksi']] = $item['tgl_produksi'];
        }
        $tgl_produksi = array_values($tgl_produksi);
        // Sort ASC
        sort($tgl_produksi);

        // Buat spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Isi header
        $sheet->setCellValue('A1', 'Tanggal');
        $sheet->mergeCells('A1:G1');
        // looping tgl produksi
        $col = "H";
        foreach ($tgl_produksi as $tgl_prod) {
            $sheet->setCellValue($col . '1', $tgl_prod);
            $col++;
        }
        $sheet->setCellValue('A2', 'Needle');
        $sheet->setCellValue('B2', 'No Model');
        $sheet->setCellValue('C2', 'Style Size');
        $sheet->setCellValue('D2', 'Qty PO (dz)');
        $sheet->setCellValue('E2', 'Running');
        $sheet->setCellValue('F2', 'Total Prod');
        $sheet->setCellValue('G2', 'Total Jl Mc');

        // Tambahkan header dinamis untuk tanggal produksi
        $col = 'H';
        foreach ($tgl_produksi as $tgl) {
            $sheet->setCellValue($col . '2', 'Prod (dz)');
            $col++;
            $sheet->setCellValue($col . '2', 'Jl Mc');
            $col++;
        }

        // // Isi data
        // $row = 2;
        // foreach ($dataSummaryPertgl as $item) {
        //     $sheet->setCellValue('A' . $row, $item['machinetypeid']);
        //     $sheet->setCellValue('B' . $row, $item['mastermodel']);
        //     $sheet->setCellValue('C' . $row, $item['size']);
        //     $sheet->setCellValue('D' . $row, number_format($item['qty'], 2));
        //     $sheet->setCellValue('E' . $row, $item['running'] . ' days');
        //     $sheet->setCellValue('F' . $row, isset($item['ttl_prod']) ? number_format($item['ttl_prod'], 2) : '');
        //     $sheet->setCellValue('G' . $row, isset($item['ttl_jlmc']) ? $item['ttl_jlmc'] : '');

        //     // Isi data tanggal produksi
        //     $col = 'H';
        //     foreach ($tgl_produksi as $tgl) {
        //         $key = $item['machinetypeid'] . '-' . $item['mastermodel'] . '-' . $item['size'] . '-' . $tgl;
        //         $qty_produksi = isset($dataSummaryPertgl[$key]) ? $dataSummaryPertgl[$key]['qty'] : 0;
        //         $jl_mc = isset($dataSummaryPertgl[$key]) ? $dataSummaryPertgl[$key]['ttl_jlmc'] : 0;
        //         $sheet->setCellValue($col . $row, number_format($qty_produksi, 2));
        //         $sheet->setCellValue($col . ($row + 1), $jl_mc);
        //         $col++;
        //     }

        //     $row += 2; // Naik 2 baris untuk mengakomodasi Prod (dz) dan Jl Mc

        //     // Limit memory usage, check every 100 rows
        //     if ($row % 100 == 0) {
        //         if (memory_get_usage(true) >= 536870912) { // Check if memory limit reached (512 MB in bytes)
        //             break;
        //         }
        //     }
        // }

        // Set judul file dan header untuk download
        $filename = 'report.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // Tulis file excel ke output
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
