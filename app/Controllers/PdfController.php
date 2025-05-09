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
use App\Models\KebutuhanAreaModel;
use App\Models\MesinPlanningModel;
use App\Models\DetailPlanningModel;
use App\Models\TanggalPlanningModel;
use App\Models\EstimatedPlanningModel;
use App\Models\AksesModel;/*  */
use App\Models\BsModel;
use App\Models\BsMesinModel;
use App\Models\MesinPerStyle;
use App\Services\orderServices;
use PhpOffice\PhpSpreadsheet\IOFactory;
use CodeIgniter\HTTP\RequestInterface;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Week;
use FPDF;

class PdfController extends BaseController
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
    protected $KebutuhanAreaModel;
    protected $MesinPlanningModel;
    protected $aksesModel;
    protected $DetailPlanningModel;
    protected $TanggalPlanningModel;
    protected $EstimatedPlanningModel;
    protected $MesinPerStyleModel;
    protected $orderServices;
    protected $bsModel;
    protected $BsMesinModel;

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
        $this->KebutuhanAreaModel = new KebutuhanAreaModel();
        $this->MesinPlanningModel = new MesinPlanningModel();
        $this->aksesModel = new AksesModel();
        $this->DetailPlanningModel = new DetailPlanningModel();
        $this->TanggalPlanningModel = new TanggalPlanningModel();
        $this->EstimatedPlanningModel = new EstimatedPlanningModel();
        $this->MesinPerStyleModel = new MesinPerStyle();
        $this->orderServices = new orderServices();
        $this->bsModel = new BsModel();
        $this->BsMesinModel = new BsMesinModel();
        if ($this->filters   = ['role' => [session()->get('role') . '']] != session()->get('role')) {
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
    public function generatePoTambahan()
    {
        $area = $this->request->getGet('area');
        $noModel = $this->request->getGet('model');

        // Ambil data berdasarkan area dan model
        $apiUrl = "http://172.23.39.114/MaterialSystem/public/api/filterPoTambahan"
            . "?area=" . urlencode($area)
            . "&model=" . urlencode($noModel);

        $ch = curl_init($apiUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
        ]);

        $response = curl_exec($ch);
        $error    = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($response === false) {
            return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Curl error: ' . $error]);
        }

        $data = json_decode($response, true);

        // Inisialisasi FPDF
        $pdf = new FPDF('L', 'mm', 'A4');
        $pdf->AddPage();

        // Garis margin luar (lebih tebal)
        $pdf->SetDrawColor(0, 0, 0); // Warna hitam
        $pdf->SetLineWidth(0.4); // Lebih tebal
        $pdf->Rect(9, 9, 279, 192); // Sedikit lebih besar dari margin dalam

        // Garis margin dalam (lebih tipis)
        $pdf->SetLineWidth(0.2); // Lebih tipis
        $pdf->Rect(10, 10, 277, 190); // Ukuran aslinya

        // Masukkan gambar di dalam kolom
        $x = $pdf->GetX(); // Simpan posisi X saat ini
        $y = $pdf->GetY(); // Simpan posisi Y saat ini

        // Menambahkan gambar
        $pdf->Image('assets/img/logo-kahatex.png', $x + 16, $y + 1, 10, 8); // Lokasi X, Y, lebar, tinggi

        // Header
        $pdf->SetFont('Arial', 'B', 7);
        $pdf->Cell(43, 13, '', 1, 0, 'C'); // Tetap di baris yang sama
        // Set warna latar belakang menjadi biru telur asin (RGB: 170, 255, 255)
        $pdf->SetFillColor(170, 255, 255);
        $pdf->Cell(234, 4, 'FORMULIR', 1, 1, 'C', 1); // Pindah ke baris berikutnya setelah ini

        $pdf->SetFont('Arial', '', 6);
        $pdf->Cell(43, 5, '', 0, 0, 'L'); // Tetap di baris yang sama
        $pdf->Cell(234, 5, 'DEPARTMEN CELUP CONES', 0, 1, 'C'); // Pindah ke baris berikutnya setelah ini

        $pdf->SetFont('Arial', '', 5);
        $pdf->Cell(43, 4, 'PT KAHATEX', 0, 0, 'C'); // Tetap di baris yang sama
        $pdf->Cell(234, 4, 'FORMULIR PO', 0, 1, 'C'); // Pindah ke baris berikutnya setelah ini

        // Tabel Header Atas
        $pdf->SetFont('Arial', '', 5);
        $pdf->Cell(43, 4, 'No. Dokumen', 1, 0, 'L');
        $pdf->Cell(162, 4, 'FOR-CC-087/REV_01/HAL_1/1', 1, 0, 'L');
        $pdf->Cell(31, 4, 'Tanggal Revisi', 1, 0, 'L');
        $pdf->Cell(41, 4, '04 Desember 2019', 1, 1, 'L');

        $pdf->Cell(205, 4, '', 1, 0, 'L');
        $pdf->Cell(31, 4, 'Klasifikasi', 1, 0, 'L');
        $pdf->Cell(41, 4, 'Internal', 1, 1, 'L');

        $pdf->SetFont('Arial', '', 7);

        $pdf->Cell(10, 5, 'Area', 0, 0, 'L');
        $pdf->Cell(75, 5, ': ' . '', 0, 0, 'L');

        $pdf->Cell(20, 5, 'Loss F.Up', 0, 0, 'L');
        $pdf->Cell(75, 5, ': ' . '', 0, 0, 'L');

        $pdf->Cell(24, 5, 'Tanggal Buat', 0, 0, 'L');
        $pdf->Cell(30, 5, ': ' . '', 0, 1, 'L');

        $pdf->Cell(197, 5, 'Tanggal Buat', 0, 0, 'R');
        $pdf->Cell(10, 5, ': ' . '', 0, 1, 'R');

        //Simpan posisi awal Season & MaterialType
        $x = $pdf->GetX();
        $y = $pdf->GetY();

        // Tabel Header Baris Pertama
        $pdf->SetFont('Arial', '', 7);
        // Merge cells untuk kolom No, Bentuk Celup, Warna, Kode Warna, Buyer, Nomor Order, Delivery, Untuk Produksi, Contoh Warna, Keterangan Celup
        $pdf->Cell(12, 12, 'Model', 1, 0, 'C'); // Merge 2 baris
        $pdf->Cell(12, 12, 'Warna', 1, 0, 'C'); // Merge 2 kolom ke samping untuk baris pertama
        $pdf->Cell(15, 12, 'Item Type', 1, 0, 'C'); // Merge 2 baris
        $pdf->Cell(17, 12, 'Kode Warna', 1, 0, 'C'); // Merge 2 baris
        $pdf->Cell(16, 12, 'Style / Size', 1, 0, 'C'); // Merge 2 baris
        $pdf->Cell(16, 12, 'Komposisi (%)', 1, 0, 'C'); // Merge 2 baris
        $pdf->Cell(7, 12, 'GW / Pcs', 1, 0, 'C'); // Merge 2 baris
        $pdf->Cell(7, 12, 'Qty / Pcs', 1, 0, 'C'); // Merge 2 baris
        $pdf->Cell(7, 12, 'Loss', 1, 0, 'C'); // Merge 2 baris
        $pdf->Cell(12, 12, 'Pesanan Kgs', 1, 0, 'C'); // Merge 4 kolom
        $pdf->Cell(21, 9, 'Terima', 1, 0, 'C'); // Merge 4 kolom
        $pdf->Cell(15, 9, 'Sisa Benang di Mesin', 1, 0, 'C'); // Merge 2 baris
        $pdf->Cell(28, 6, 'Tambahan I (mesin)', 1, 0, 'C'); // Merge 2 baris
        $pdf->Cell(28, 6, 'Tamabahan II (Packing)', 1, 0, 'C'); // Merge 2 baris
        $pdf->Cell(13, 9, 'Total lebih pakai benang', 1, 0, 'C'); // Merge 2 baris
        $pdf->Cell(34, 6, 'RETURAN', 1, 0, 'C'); // Merge 2 baris
        $pdf->Cell(17, 12, 'Keterangan', 1, 0, 'C'); // Merge 2 baris


        // Output PDF
        $pdfContent = $pdf->Output('S');
        return $this->response
            ->setStatusCode(200)
            ->setContentType('application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="Report_Model_' . $noModel . '.pdf"')
            ->setBody($pdfContent);
    }
}
