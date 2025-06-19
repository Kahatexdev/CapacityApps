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
        $tglBuat = $this->request->getGet('tgl_buat');

        // Ambil data berdasarkan area dan model
        $apiUrl = "http://172.23.44.14/MaterialSystem/public/api/filterPoTambahan"
            . "?area=" . urlencode($area)
            . "&model=" . urlencode($noModel)
            . "&tglBuat=" . urlencode($tglBuat);

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
        if (!is_array($data)) {
            return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Data tidak valid dari API']);
        }

        // Inisialisasi FPDF
        $pdf = new FPDF('L', 'mm', 'A4');
        $pdf->AddPage();
        $pdf->SetAutoPageBreak(false);

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

        $pdf->SetFont('Arial', 'B', 6);
        $pdf->Cell(43, 5, '', 0, 0, 'L'); // Tetap di baris yang sama
        $pdf->Cell(234, 5, 'DEPARTMEN KAOS KAKI', 0, 1, 'C'); // Pindah ke baris berikutnya setelah ini

        $pdf->SetFont('Arial', 'B', 6);
        $pdf->Cell(43, 4, 'PT KAHATEX', 0, 0, 'C'); // Tetap di baris yang sama
        $pdf->Cell(234, 4, 'PO TAMBAHAN DAN RETURAN BAHAN BAKU MESIN KE GUDANG BENANG', 0, 1, 'C'); // Pindah ke baris berikutnya setelah ini

        // Tabel Header Atas
        $pdf->SetFont('Arial', 'B', 5);
        $pdf->Cell(43, 4, 'No. Dokumen', 1, 0, 'L');
        $pdf->Cell(162, 4, 'FOR-KK-034/REV_05/HAL_1/1', 1, 0, 'L');
        $pdf->Cell(31, 4, 'Tanggal Revisi', 1, 0, 'L');
        $pdf->Cell(41, 4, '30 Januari 2025', 1, 1, 'L');

        $pdf->Cell(205, 4, '', 1, 0, 'L');
        $pdf->Cell(31, 4, 'Klasifikasi', 1, 0, 'L');
        $pdf->Cell(41, 4, 'Sensitif', 1, 1, 'L');

        $pdf->SetFont('Arial', 'B', 6);

        $pdf->Cell(10, 5, 'Area', 0, 0, 'L');
        $pdf->Cell(75, 5, ': ' . $area, 0, 0, 'L');

        $pdf->Cell(20, 5, 'Loss F.Up', 0, 0, 'L');
        $pdf->Cell(75, 5, ': ' . '', 0, 0, 'L');

        $pdf->Cell(24, 5, 'Tanggal Buat', 0, 0, 'L');
        $pdf->Cell(30, 5, ': ' . $tglBuat, 0, 1, 'L');

        $firstItem = $data[0] ?? null;
        $deliveryAkhir = $firstItem['delivery_akhir'] ?? '-';

        $pdf->Cell(180, 5, '', 0, 0, 'L');
        $pdf->Cell(24, 5, 'Tgl. Export', 0, 0, 'L');
        $pdf->Cell(40, 5, ': ' . $deliveryAkhir, 0, 1, 'L');

        //Simpan posisi awal Season & MaterialType
        function MultiCellFit($pdf, $w, $h, $txt, $border = 1, $align = 'C')
        {
            // Simpan posisi awal
            $x = $pdf->GetX();
            $y = $pdf->GetY();

            // Simulasikan MultiCell tetapi tetap pakai tinggi tetap (12)
            $pdf->MultiCell($w, $h, $txt, $border, $align);

            // Kembalikan ke kanan cell agar sejajar
            $pdf->SetXY($x + $w, $y);
        }

        // Tabel Header Baris Pertama
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(12, 12, 'Model', 1, 0, 'C');
        $pdf->Cell(12, 12, 'Warna', 1, 0, 'C');
        $pdf->Cell(19, 12, 'Item Type', 1, 0, 'C');
        $pdf->Cell(16, 12, 'Kode Warna', 1, 0, 'C');
        $pdf->Cell(15, 12, 'Style / Size', 1, 0, 'C');
        MultiCellFit($pdf, 12, 6, "Komposi\nsi (%)");
        MultiCellFit($pdf, 7, 6, "GW/\nPcs");
        MultiCellFit($pdf, 7, 6, "Qty/\nPcs");
        $pdf->Cell(7, 12, 'Loss', 1, 0, 'C');
        MultiCellFit($pdf, 12, 6, "Pesanan\nKgs");
        $pdf->Cell(21, 9, 'Terima', 1, 0, 'C');
        MultiCellFit($pdf, 13, 3, "Sisa Benang\ndi Mesin");
        $pdf->Cell(29, 6, 'Tambahan I (mesin)', 1, 0, 'C');
        $pdf->Cell(29, 6, 'Tamabahan II (Packing)', 1, 0, 'C');
        MultiCellFit($pdf, 14, 3, "Total lebih\npakai benang");
        $pdf->Cell(38, 6, 'RETURAN', 1, 0, 'C');
        $pdf->Cell(14, 12, 'Keterangan', 1, 1, 'C');

        // Tabel Header Baris Kedua
        $pdf->Cell(153, -6, '', 0, 0);
        $pdf->Cell(7, -6, 'Pcs', 1, 1, 'C');
        $pdf->Cell(160, -6, '', 0, 0);
        $pdf->Cell(15, 3, 'Benang', 1, 0, 'C');
        $pdf->Cell(7, 6, '%', 1, 0, 'C');
        $pdf->Cell(7, 6, 'Pcs', 1, 0, 'C');
        $pdf->Cell(15, 3, 'Benang', 1, 0, 'C');
        $pdf->Cell(7, 6, '%', 1, 0, 'C');
        $pdf->Cell(14, -6, '', 0, 0);
        $pdf->Cell(6, 6, 'Kg', 1, 0, 'C');
        MultiCellFit($pdf, 12, 3, "%\ndari PSN");
        $pdf->Cell(6, 6, 'Kg', 1, 0, 'C');
        MultiCellFit($pdf, 14, 3, "%\ndari PO(+)");

        $pdf->Ln(3);

        // Tabel Header Baris Ketiga
        $pdf->Cell(119);
        $pdf->Cell(7, 3, 'Kg', 1, 0, 'C');
        $pdf->Cell(7, 3, '+ / -', 1, 0, 'C');
        $pdf->Cell(7, 3, '%', 1, 0, 'C');
        $pdf->Cell(13, 3, 'Kg', 1, 0, 'C');
        $pdf->Cell(7, -3, '', 0, 0, 'C');
        $pdf->Cell(7, 3, 'Kg', 1, 0, 'C');
        $pdf->Cell(8, 3, 'Cones', 1, 0, 'C');
        $pdf->Cell(14, -3, '', 0, 0, 'C');
        $pdf->Cell(7, 3, 'Kg', 1, 0, 'C');
        $pdf->Cell(8, 3, 'Cones', 1, 0, 'C');
        $pdf->Cell(7, -3, '', 0, 0, 'C');
        $pdf->Cell(7, 3, 'Kg', 1, 0, 'C');
        $pdf->Cell(7, 3, '%', 1, 0, 'C');
        $pdf->Ln(3);

        //Isi Tabel
        $rowHeight = 6;
        $lineHeight = 3;
        $pdf->SetFont('Arial', '', 7);
        $no = 1;
        $yLimit = 180;

        foreach ($data as $row) {
            // Cek jika sudah mendekati batas bawah halaman, buat halaman baru
            if ($pdf->GetY() > $yLimit) {
                $pdf->AddPage();
                // Panggil lagi header jika perlu
            }

            $startX = $pdf->GetX();
            $startY = $pdf->GetY();

            // Simpan tinggi maksimum dari semua MultiCell di baris ini
            $maxHeight = 0;

            $pdf->Cell(12, $rowHeight, $row['no_model'], 1, 0, 'C'); //no model
            // Simpan posisi awal X untuk MultiCell berikutnya
            $xBefore = $pdf->GetX();
            $yBefore = $pdf->GetY();

            // Kolom 2: color
            $pdf->MultiCell(12, $lineHeight, $row['color'], 1, 'C');
            $h1 = $pdf->GetY() - $yBefore;
            $maxHeight = max($maxHeight, $h1);
            $pdf->SetXY($xBefore + 12, $yBefore);
            // Kolom 3: item_type
            $xBefore = $pdf->GetX();
            $pdf->MultiCell(19, $lineHeight, $row['item_type'], 1, 'C');
            $h2 = $pdf->GetY() - $yBefore;
            $maxHeight = max($maxHeight, $h2);
            $pdf->SetXY($xBefore + 19, $yBefore);

            // Kolom 4: kode_warna
            $xBefore = $pdf->GetX();
            $pdf->MultiCell(16, $lineHeight, $row['kode_warna'], 1, 'C');
            $h3 = $pdf->GetY() - $yBefore;
            $maxHeight = max($maxHeight, $h3);
            $pdf->SetXY($xBefore + 16, $yBefore);

            // Kolom 5: style_size
            $xBefore = $pdf->GetX();
            $pdf->MultiCell(15, $lineHeight, $row['style_size'], 1, 'C');
            $h4 = $pdf->GetY() - $yBefore;
            $maxHeight = max($maxHeight, $h4);
            $pdf->SetXY($xBefore + 15, $yBefore);

            // Kolom-kolom berikut: isi seperti biasa, tapi tingginya harus `maxHeight`
            $pdf->Cell(12, $maxHeight, $row['composition'], 1, 0, 'C');
            $pdf->Cell(7, $maxHeight, $row['gw'], 1, 0, 'C');
            $pdf->Cell(7, $maxHeight, $row['qty_pcs'], 1, 0, 'C');
            $pdf->Cell(7, $maxHeight, $row['loss'], 1, 0, 'C');
            $pdf->Cell(12, $maxHeight, $row['kgs'], 1, 0, 'C');

            // Terima: terdiri dari 3 kolom
            $pdf->Cell(7, $rowHeight, number_format($row['terima_kg'], 2), 1, 0, 'C'); //terima kg
            $pdf->Cell(7, $rowHeight, '', 1, 0, 'C'); //terima +/-
            $pdf->Cell(7, $rowHeight, '', 1, 0, 'C'); //terima %

            $pdf->Cell(13, $rowHeight, number_format($row['sisa_bb_mc'], 2), 1, 0, 'C'); //sisa mesin

            // Lanjutkan isi kolom lainnya sesuai dengan struktur header

            $pdf->Cell(7, $maxHeight, $row['sisa_order_pcs'], 1, 0, 'C'); // tambahan I pcs
            $pdf->Cell(7, $maxHeight, number_format($row['poplus_mc_kg'], 2), 1, 0, 'C'); // tambahan I benang kg
            $pdf->Cell(8, $maxHeight, $row['poplus_mc_cns'], 1, 0, 'C'); // tambahan I benang cones
            $pdf->Cell(7, $maxHeight, '', 1, 0, 'C'); // tambahan I %

            $pdf->Cell(7, $maxHeight, number_format($row['plus_pck_pcs'], 2), 1, 0, 'C'); // tambahan II pcs
            $pdf->Cell(7, $maxHeight, number_format($row['plus_pck_kg'], 2), 1, 0, 'C'); // tambahan II benang kg
            $pdf->Cell(8, $maxHeight, $row['plus_pck_cns'], 1, 0, 'C'); // tambahan II benang cones
            $pdf->Cell(7, $maxHeight, '', 1, 0, 'C'); // tambahan II %

            $pdf->Cell(7, $maxHeight, number_format($row['lebih_pakai_kg'], 2), 1, 0, 'C'); //lebih pakai kg
            $pdf->Cell(7, $maxHeight, '', 1, 0, 'C'); //lebih pakai %

            $pdf->Cell(6, $maxHeight, '', 1, 0, 'C'); //returan kg
            $pdf->Cell(12, $maxHeight, '', 1, 0, 'C'); //returan % dari PSN
            $pdf->Cell(6, $maxHeight, '', 1, 0, 'C'); //returan kg
            $pdf->Cell(14, $maxHeight, '', 1, 0, 'C'); //returan % dari PO(+)

            $xBefore = $pdf->GetX();
            $pdf->MultiCell(14, $lineHeight, $row['keterangan'], 1, 'C');
            $h5 = $pdf->GetY() - $startY;
            $maxHeight = max($maxHeight, $h5);
            $pdf->SetY($startY + $maxHeight); //keterangan
        }

        // FOOTER
        // Posisi 55 mm dari bawah
        $pdf->SetY(-40);
        $pdf->SetFont('Arial', '', 7);

        // Baris kosong
        $pdf->Cell(277, 5, '', 0, 1, 'C');
        // Judul kolom tanda tangan
        $pdf->Cell(27, 5, 'MANAJEMEN NS', 0, 0, 'C');
        $pdf->Cell(27, 5, 'KEPALA AREA', 0, 0, 'C');
        $pdf->Cell(27, 5, 'IE TEKNISI', 0, 0, 'C');
        $pdf->Cell(27, 5, 'PIC PACKING', 0, 0, 'C');
        $pdf->Cell(27, 5, 'PPC', 0, 0, 'C');
        $pdf->Cell(27, 5, 'PPC', 0, 0, 'C');
        $pdf->Cell(27, 5, 'PPC', 0, 0, 'C');
        $pdf->Cell(27, 5, 'GD BENANG', 0, 0, 'C');
        $pdf->Cell(27, 5, 'MENGETAHUI', 0, 0, 'C');
        $pdf->Cell(27, 5, 'MENGETAHUI', 0, 1, 'C');

        // Pindah ke bawah sedikit (tetap aman)
        $pdf->SetY(194); // sekitar 18 mm dari bawah halaman (bukan dari margin)
        $pdf->SetFont('Arial', '', 7);

        // Garis tanda tangan
        for ($i = 0; $i < 10; $i++) {
            $pdf->Cell(27, 5, '(________________)', 0, 0, 'C');
        }
        $pdf->Ln();

        // Output PDF
        $pdfContent = $pdf->Output('S');
        return $this->response
            ->setStatusCode(200)
            ->setContentType('application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="Report_Model_' . $noModel . '.pdf"')
            ->setBody($pdfContent);
    }
    public function exportPemesanan($jenis, $area, $tgl_pakai)
    {
        // Ambil data berdasarkan area dan model
        $apiUrl = "http://172.23.44.14/MaterialSystem/public/api/dataPemesananArea"
            . "?jenis=" . urlencode($jenis)
            . "&area=" . urlencode($area)
            . "&tgl_pakai=" . urlencode($tgl_pakai);

        log_message('debug', 'API Url: ' . $apiUrl);

        $ch = curl_init($apiUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
        ]);

        $response = curl_exec($ch);
        log_message('debug', 'Raw API response: ' . $response);

        $error    = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($response === false) {
            return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Curl error: ' . $error]);
        }

        $data = json_decode($response, true);

        // Inisialisasi FPDF
        $pdf = new FPDF('L', 'mm', 'A4');
        $pdf->AliasNbPages();          // <-- untuk {nb}
        $pdf->SetAutoPageBreak(true, 5); // Atur margin bawah saat halaman penuh
        $pdf->AddPage();
        $this->generatePageNumber($pdf);   // cetak “Page X/{nb}”

        // Garis margin luar (lebih tebal)
        // $pdf->SetDrawColor(0, 0, 0); // Warna hitam
        // $pdf->SetLineWidth(0.4); // Lebih tebal
        // $pdf->Rect(9, 9, 279, 192); // Sedikit lebih besar dari margin dalam

        // // Garis margin dalam (lebih tipis)
        // $pdf->SetLineWidth(0.2); // Lebih tipis
        // $pdf->Rect(10, 10, 277, 190); // Ukuran aslinya

        $pdf->SetFont('Arial', 'B', 14);
        $pdf->SetFillColor(255, 255, 255); // Atur warna latar belakang menjadi putih
        $pdf->Cell(279, 8, 'REPORT PEMESANAN BAHAN BAKU', 0, 1, 'C');

        $pdf->SetFont('Arial', 'B', 7);
        $pdf->Cell(40, 8, 'JENIS BAHAN BAKU', 0, 0, 'L');
        $pdf->Cell(92, 8, ': ' . $jenis, 0, 0, 'L');
        $pdf->Cell(15, 8, 'AREA', 0, 0, 'L');
        $pdf->Cell(92, 8, ': ' . $area, 0, 0, 'L');
        $pdf->Cell(22, 8, 'TANGGAL PAKAI' . '', 0, 0, 'L');
        $pdf->Cell(48, 8, ': ' . $tgl_pakai, 0, 1, 'L');

        //Simpan posisi awal Season & MaterialType
        function MultiCellFit($pdf, $w, $h, $txt, $border = 1, $align = 'C')
        {
            // Simpan posisi awal
            $x = $pdf->GetX();
            $y = $pdf->GetY();

            // Simulasikan MultiCell tetapi tetap pakai tinggi tetap (12)
            $pdf->MultiCell($w, $h, $txt, $border, $align);

            // Kembalikan ke kanan cell agar sejajar
            $pdf->SetXY($x + $w, $y);
        }

        // Tabel Header Baris Pertama
        $pdf->SetFont('Arial', 'B', 7);
        $pdf->Cell(25, 8, 'Tgl Pesan', 1, 0, 'C');
        $pdf->Cell(15, 8, 'No Model', 1, 0, 'C');
        $pdf->Cell(37, 8, 'Item Type', 1, 0, 'C');
        $pdf->Cell(20, 8, 'Warna', 1, 0, 'C');
        $pdf->Cell(25, 8, 'Kode Warna', 1, 0, 'C');
        $pdf->Cell(12, 8, 'Jl MC', 1, 0, 'C');
        $pdf->Cell(17, 8, 'Kgs Pesan', 1, 0, 'C');
        MultiCellFit($pdf, 11, 4, "Cones\n Pesan");
        $pdf->Cell(18, 8, 'Lot Pesan', 1, 0, 'C');
        $pdf->Cell(17, 8, 'Kgs Kirim', 1, 0, 'C');
        MultiCellFit($pdf, 12, 4, "Cones\n Kirim");
        MultiCellFit($pdf, 12, 4, "Karung\n Kirim");
        $pdf->Cell(19, 8, 'Lot Kirim', 1, 0, 'C');
        $pdf->Cell(37, 8, 'Keterangan', 1, 1, 'C');


        //Isi Tabel
        $lineHeight = 3;
        $pdf->SetFont('Arial', '', 7);
        $no = 1;
        $yLimit = 180;

        $totalKgsPesan = 0;
        $totalConesPesan = 0;
        $totalKgsKirim = 0;
        $totalConesKirim = 0;
        $totalKarungKirim = 0;

        foreach ($data as $row) {
            $totalKgsPesan    += floatval($row['qty_pesan']);
            $totalConesPesan  += floatval($row['cns_pesan']);
            $totalKgsKirim    += floatval($row['kgs_out']);
            $totalConesKirim  += floatval($row['cns_out']);
            $totalKarungKirim += floatval($row['krg_out']);

            $rowHeight = 5;
            $heights = [];

            // hitung jumlah baris per kolom
            $heights = [
                'item_type'     => ceil($pdf->GetStringWidth($row['item_type']) / 37) * $rowHeight,
                'color'         => ceil($pdf->GetStringWidth($row['color']) / 20) * $rowHeight,
                'kode_warna'    => ceil($pdf->GetStringWidth($row['kode_warna']) / 25) * $rowHeight,
                'lot_pesan'     => ceil($pdf->GetStringWidth($row['lot_pesan']) / 18) * $rowHeight,
                'lot_out'       => ceil($pdf->GetStringWidth($row['lot_out']) / 19) * $rowHeight,
                'ket_area'      => ceil($pdf->GetStringWidth($row['ket_area']) / 37) * $rowHeight,
            ];

            $rowHeight = max($heights);

            // Cek jika sudah mendekati batas bawah halaman, buat halaman baru
            if ($pdf->GetY() + $rowHeight > $yLimit) {
                $this->footerPemesanan($pdf);
                $pdf->AddPage();
            }

            $yStart = $pdf->GetY(); // posisi awal Y
            $xStart = $pdf->GetX(); // posisi awal X

            // Kolom 
            $pdf->SetXY($xStart, $yStart);

            $pdf->Cell(25, $rowHeight, $row['tgl_pesan'], 1, 0, 'C'); // tgl pesan
            $pdf->Cell(15, $rowHeight, $row['no_model'], 1, 0, 'C'); // no model

            $xNow = $pdf->GetX();
            $rowItem = $heights['item_type'] / 5 > 1 ? 5 : $rowHeight;
            $pdf->MultiCell(37, $rowItem, $row['item_type'], 1, 'C'); // item type
            $pdf->SetXY($xNow + 37, $yStart);

            $xNow = $pdf->GetX();
            $rowColor = $heights['color'] / 5 > 1 ? 5 : $rowHeight;
            $pdf->MultiCell(20, $rowColor, $row['color'], 1, 'C'); // warna
            $pdf->SetXY($xNow + 20, $yStart);

            $xNow = $pdf->GetX();
            $rowKode = $heights['kode_warna'] / 5 > 1 ? 5 : $rowHeight;
            $pdf->MultiCell(25, $rowKode, $row['kode_warna'], 1, 'C'); // kode warna
            $pdf->SetXY($xNow + 25, $yStart);

            $pdf->Cell(12, $rowHeight, $row['jl_mc'], 1, 0, 'C'); // jl mc
            $pdf->Cell(17, $rowHeight, $row['qty_pesan'], 1, 0, 'C'); // kg pesan
            $pdf->Cell(11, $rowHeight, $row['cns_pesan'], 1, 0, 'C'); // cns pcs

            $xNow = $pdf->GetX();
            $rowLotP = $heights['lot_pesan'] / 5 > 1 ? 5 : $rowHeight;
            $pdf->MultiCell(18, $rowLotP, $row['lot_pesan'], 1, 'C'); // lot
            $pdf->SetXY($xNow + 18, $yStart);

            // Kgs Kirim: kosong kalau 0, format 2 desimal kalau >0
            $valKgs = $row['kgs_out'] ?? 0;
            $textKgs = $valKgs > 0 ? number_format($valKgs, 2) : '';
            $pdf->Cell(17, $rowHeight, $textKgs, 1, 0, 'C');

            // Cones Kirim: kosong kalau 0
            $valCns = $row['cns_out'] ?? 0;
            $textCns = $valCns > 0 ? $valCns : '';
            $pdf->Cell(12, $rowHeight, $textCns, 1, 0, 'C');

            // Karung Kirim: kosong kalau 0
            $valKrg = $row['krg_out'] ?? 0;
            $textKrg = $valKrg > 0 ? $valKrg : '';
            $pdf->Cell(12, $rowHeight, $textKrg, 1, 0, 'C');


            $xNow = $pdf->GetX();
            $rowLotO = $heights['lot_out'] / 5 > 1 ? 5 : $rowHeight;
            $pdf->MultiCell(19, $rowLotO, $row['lot_out'], 1, 'C'); // lot kirim
            $pdf->SetXY($xNow + 19, $yStart);

            $xNow = $pdf->GetX();
            $rowKet = $heights['ket_area'] / 5 > 1 ? 5 : $rowHeight;
            $pdf->MultiCell(37, $rowKet, $row['ket_area'], 1, 'C'); // keterangan
            $pdf->SetXY($xNow + 37, $yStart);

            $pdf->Ln($rowHeight); // Pindah ke baris berikutnya
        }

        // Grand Total
        $pdf->SetFont('Arial', 'B', 7);
        $pdf->Cell(134, 6, 'GRAND TOTAL', 1, 0, 'C'); // Span 134 mm (dari awal sampai sebelum kolom-kolom jumlah)
        $pdf->Cell(17, 6, number_format($totalKgsPesan, 2), 1, 0, 'C');
        $pdf->Cell(11, 6, number_format($totalConesPesan), 1, 0, 'C');
        $pdf->Cell(18, 6, '', 1, 0); // Lot Pesan kosong
        $pdf->Cell(17, 6, number_format($totalKgsKirim, 2), 1, 0, 'C');
        $pdf->Cell(12, 6, number_format($totalConesKirim), 1, 0, 'C');
        $pdf->Cell(12, 6, number_format($totalKarungKirim), 1, 0, 'C');
        $pdf->Cell(19, 6, '', 1, 0); // Lot Kirim kosong
        $pdf->Cell(37, 6, '', 1, 1); // Keterangan kosong

        $this->footerPemesanan($pdf);

        // Output PDF
        $pdfContent = $pdf->Output('S');
        return $this->response
            ->setStatusCode(200)
            ->setContentType('application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="Report_Pemesanan_' . $jenis . '_Area_' . $area . '_' . $tgl_pakai . '.pdf"')
            ->setBody($pdfContent);
    }

    /**
     * Cetak page number di pojok kanan atas
     * @param FPDF $pdf
     */
    private function generatePageNumber($pdf)
    {
        // X = -30mm dari kanan, Y = 10mm dari atas
        $pdf->SetXY(-30, 10);
        $pdf->SetFont('Arial', 'I', 7);
        // 'Page 1/5'
        $text = 'Page ' . $pdf->PageNo() . '/{nb}';
        // lebar 30mm, tinggi 5mm, no border, ln=0, align right
        $pdf->Cell(30, 5, $text, 0, 0, 'R');
        // kembalikan posisi ke margin kiri (jika perlu)
        $pdf->SetXY(10, 10);
    }

    /**
     * Cetak footer di posisi bawah halaman
     * @param FPDF $pdf
     */
    private function footerPemesanan($pdf)
    {
        // 15 mm dari bawah
        $pdf->SetY(-15);
        $pdf->SetFont('Arial', 'I', 7);
        // PageNo() mengembalikan halaman sekarang, {nb} diganti total halaman
        $text = 'FOR_KK_369/TGL_REV_13_07_20/REV_02/HAL '
            . $pdf->PageNo() . '/{nb}';
        $pdf->Cell(0, 10, $text, 0, 0, 'C');
    }
}
