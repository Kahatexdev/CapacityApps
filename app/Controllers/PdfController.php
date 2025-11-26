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

class PDF extends FPDF
{
    /** Hitung jumlah baris yang akan dipakai MultiCell pada lebar tertentu. */
    public function nbLines(float $w, string $txt): int
    {
        // akses metrik font yang sedang aktif (legal di subclass)
        $cw = $this->CurrentFont['cw'];

        if ($w == 0) {
            $w = $this->w - $this->rMargin - $this->x;
        }
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;

        $s  = str_replace("\r", '', (string) $txt);
        $nb = strlen($s);
        if ($nb > 0 && $s[$nb - 1] == "\n") $nb--;

        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if ($c == ' ') $sep = $i;
            $l += $cw[$c] ?? 0;
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j) $i++;
                } else {
                    $i = $sep + 1;
                }
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else {
                $i++;
            }
        }
        return $nl;
    }

    /** MultiCell tapi pointer X maju ke kolom berikutnya. */
    public function multiCellFit(float $w, float $h, string $txt, $border = 1, string $align = 'C'): void
    {
        $x = $this->GetX();
        $y = $this->GetY();
        $this->MultiCell($w, $h, $txt, $border, $align);
        $this->SetXY($x + $w, $y);
    }

    /** Gambar satu sel wrap namun tetap menjaga tinggi baris konsisten. */
    public function cellWrap(float $w, float $h, string $txt, int $border = 1, string $align = 'C', float $rowHeight = null): void
    {
        $x = $this->GetX();
        $y = $this->GetY();
        // bingkai sel dengan tinggi baris total, lalu tulis teks dengan MultiCell tanpa border (agar rapi)
        if ($border) $this->Rect($x, $y, $w, $rowHeight ?? $h);
        $this->MultiCell($w, $h, $txt, 0, $align);
        $this->SetXY($x + $w, $y);
    }

    /** Ulang header tabel (dipanggil di awal halaman & saat page break). */
    public function renderHeaderRow(array $cols, float $lineHeight, array $widths): void
    {
        $this->SetFont('Arial', 'B', 7);
        // hitung tinggi maksimum header berdasar teks header & width
        $maxLines = 1;
        foreach ($cols as $key => $col) {
            $hdrText = $col['header'];
            $w       = $widths[$key];
            $n       = $this->nbLines($w, $hdrText);
            if ($n > $maxLines) $maxLines = $n;
        }
        $rowH = $maxLines * $lineHeight;

        foreach ($cols as $key => $col) {
            $this->cellWrap($widths[$key], $lineHeight, $col['header'], 1, 'C', $rowH);
        }
        $this->Ln($rowH);
        $this->SetFont('Arial', '', 7);
    }

    public function getCellMargin(): float
    {
        return $this->cMargin; // aman karena masih di dalam subclass
    }

    /** Ambil ambang page break yang dipakai FPDF. */
    public function getPageBreakTrigger(): float
    {
        return $this->PageBreakTrigger; // legal karena masih di dalam subclass
    }

    /** Cek apakah menulis blok setinggi $h akan melewati page break. */
    public function willExceedPage(float $h): bool
    {
        return ($this->GetY() + $h) > $this->PageBreakTrigger;
    }
}

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
        $apiUrl = $this->urlMaterial . "filterPoTambahan"
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
        MultiCellFit($pdf, 10, 6, "Kompo\nsisi(%)");
        MultiCellFit($pdf, 7, 6, "GW/\nPcs");
        MultiCellFit($pdf, 9, 6, "Qty/\nPcs");
        $pdf->Cell(7, 12, 'Loss', 1, 0, 'C');
        MultiCellFit($pdf, 12, 6, "Pesanan\nKgs");
        $pdf->Cell(23, 9, 'Terima', 1, 0, 'C');
        MultiCellFit($pdf, 11, 3, "Sisa Benang\ndi Mesin");
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
        $pdf->Cell(8, 3, 'Kg', 1, 0, 'C');
        $pdf->Cell(7, 3, '+ / -', 1, 0, 'C');
        $pdf->Cell(8, 3, '%', 1, 0, 'C');
        $pdf->Cell(11, 3, '', 0, 0, 'C');
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
        $pdf->SetFont('Arial', '', 7);
        $no = 1;
        $yLimit = 170;
        $lineHeight = 4;

        $prevKey    = null;
        $totals     = [
            'qty_pcs' => 0,
            'kgs'      => 0,
            'terima_kg'      => 0,
            'sisa_benang'      => 0,
            'plus_mc_pcs'      => 0,
            'plus_mc_kg'      => 0,
            'plus_mc_cns'      => 0,
            'plus_pck_pcs'      => 0,
            'plus_pck_kg'      => 0,
            'plus_pck_cns'      => 0,
            'lebih_pakai'      => 0,
            'kgs_retur'      => 0,
        ];


        foreach ($data as $row) {
            // bangun “key” unik untuk group
            $currentKey = implode('|', [
                $row['no_model'],
                $row['color'],
                $row['item_type'],
                $row['kode_warna'],
            ]);

            // 1) kalau pindah group (bukan pertama)
            if ($prevKey !== null && $currentKey !== $prevKey) {
                // cetak subtotal untuk group sebelumnya
                $this->printSubtotalRow($pdf, $totals);

                // reset accumulator
                foreach ($totals as $k => $v) {
                    $totals[$k] = 0;
                }
            }

            // Cek dulu apakah nambah baris ini bakal lewat batas
            if ($pdf->GetY() + $lineHeight > $yLimit) {
                $this->renderFooterPage($pdf);
                $pdf->AddPage();
                $this->renderHeaderPage($pdf, $area, $tglBuat, $deliveryAkhir);

                // Gambar ulang margin & header halaman
                $pdf->SetDrawColor(0, 0, 0);
                $pdf->SetLineWidth(0.4);
                $pdf->Rect(9, 9, 279, 192);
                $pdf->SetLineWidth(0.2);
                $pdf->Rect(10, 10, 277, 190);

                // (Kalau header halaman lain ada lagi, panggil disini...)

                // Gambar ulang header tabel
                $this->renderTableHeader($pdf);
            }

            $startX = $pdf->GetX();
            $startY = $pdf->GetY();

            // 1. SIMULASI: Hitung tinggi maksimum yang dibutuhkan
            $heights = [];
            $tempX = $startX;

            $pdf->SetTextColor(255, 255, 255); // putih agar tidak terlihat

            $multiCellData = [
                ['w' => 12, 'text' => $row['no_model']],
                ['w' => 12, 'text' => $row['color']],
                ['w' => 19, 'text' => $row['item_type']],
                ['w' => 16, 'text' => $row['kode_warna']],
                ['w' => 15, 'text' => $row['style_size']],
                ['w' => 14, 'text' => $row['keterangan']],
            ];

            foreach ($multiCellData as $data) {
                $pdf->SetXY($tempX, $startY);
                $y0 = $pdf->GetY();
                $pdf->MultiCell($data['w'], $lineHeight, $data['text'], 0, 'C');
                $heights[] = $pdf->GetY() - $y0;
                $tempX += $data['w'];
            }

            $pdf->SetTextColor(0, 0, 0); // kembali ke hitam
            $maxHeight = max($heights);

            // 2. RENDER: Gambar semua cell dengan tinggi yang sama
            $pdf->SetXY($startX, $startY);

            // Buat semua cell dengan border dan tinggi yang sama, tapi isi kosong dulu
            $cellWidths = [12, 12, 19, 16, 15, 10, 7, 9, 7, 12, 8, 7, 8, 11, 7, 7, 8, 7, 7, 7, 8, 7, 7, 7, 6, 12, 6, 14, 14];
            $cellData = [
                '',
                '',
                '',
                '',
                '', // 5 kolom multiCell akan diisi kemudian
                $row['composition'],
                $row['gw'],
                $row['qty_pcs'],
                $row['loss'],
                $row['kgs'],
                number_format($row['terima_kg'], 2),
                number_format($row['terima_kg'] - $row['kgs'], 2),
                number_format($row['terima_kg'] / $row['kgs'], 2) * 100 . '%', // terima
                number_format($row['sisa_bb_mc'], 2), // sisa mesin
                $row['sisa_order_pcs'],
                number_format($row['poplus_mc_kg'], 2),
                $row['poplus_mc_cns'],
                number_format($row['poplus_mc_kg'] / $row['kgs'], 2) * 100 . '%',
                number_format($row['plus_pck_pcs'], 2),
                number_format($row['plus_pck_kg'], 2),
                $row['plus_pck_cns'],
                number_format($row['plus_pck_kg'] / $row['kgs'], 2) * 100 . '%',
                number_format($row['lebih_pakai_kg'], 2),
                number_format($row['lebih_pakai_kg'] / $row['kgs'], 2) * 100 . '%',
                '',
                '',
                '',
                '',
                '' // keterangan akan diisi kemudian
            ];

            // Gambar semua cell dengan border
            for ($i = 0; $i < count($cellWidths); $i++) {
                $pdf->Cell($cellWidths[$i], $maxHeight, $cellData[$i], 1, 0, 'C');
            }

            // 3. ISI TEXT untuk kolom multiCell (overlay tanpa border)
            $currentX = $startX;

            // No Model
            $textCenterY = $startY + ($maxHeight / 2) - ($lineHeight / 2);
            $pdf->SetXY($currentX, $textCenterY);
            $pdf->Cell(12, $lineHeight, $row['no_model'], 0, 0, 'C');
            $currentX += 12;

            // Color
            $pdf->SetXY($currentX, $startY);
            $pdf->SetTextColor(255, 255, 255);
            $y0 = $pdf->GetY();
            $pdf->MultiCell(12, $lineHeight, $row['color'], 0, 'C');
            $textHeight = $pdf->GetY() - $y0;
            $pdf->SetTextColor(0, 0, 0);

            $centerY = $startY + ($maxHeight - $textHeight) / 2;
            $pdf->SetXY($currentX, $centerY);
            $pdf->MultiCell(12, $lineHeight, $row['color'], 0, 'C');
            $currentX += 12;

            // Item Type (mungkin multiline)
            $pdf->SetXY($currentX, $startY);
            $pdf->SetTextColor(255, 255, 255);
            $y0 = $pdf->GetY();
            $pdf->MultiCell(19, $lineHeight, $row['item_type'], 0, 'C');
            $textHeight = $pdf->GetY() - $y0;
            $pdf->SetTextColor(0, 0, 0);

            $centerY = $startY + ($maxHeight - $textHeight) / 2;
            $pdf->SetXY($currentX, $centerY);
            $pdf->MultiCell(19, $lineHeight, $row['item_type'], 0, 'C');
            $currentX += 19;

            // Kode Warna (mungkin multiline)
            $pdf->SetXY($currentX, $startY);
            $pdf->SetTextColor(255, 255, 255);
            $y0 = $pdf->GetY();
            $pdf->MultiCell(16, $lineHeight, $row['kode_warna'], 0, 'C');
            $textHeight = $pdf->GetY() - $y0;
            $pdf->SetTextColor(0, 0, 0);

            $centerY = $startY + ($maxHeight - $textHeight) / 2;
            $pdf->SetXY($currentX, $centerY);
            $pdf->MultiCell(16, $lineHeight, $row['kode_warna'], 0, 'C');
            $currentX += 16;

            // Style Size
            $centerY = $startY + ($maxHeight - $textHeight) / 2;
            $pdf->SetXY($currentX, $centerY);
            $pdf->MultiCell(15, $lineHeight, $row['style_size'], 0, 'C');
            $currentX += 15;

            // Skip kolom yang sudah terisi dengan Cell biasa
            $currentX += 10 + 7 + 9 + 7 + 12 + 8 + 7 + 8 + 11 + 7 + 7 + 8 + 7 + 7 + 7 + 8 + 7 + 7 + 7 + 6 + 12 + 6 + 14;

            // Keterangan (mungkin multiline)
            $pdf->SetXY($currentX, $startY);
            $pdf->SetTextColor(255, 255, 255);
            $y0 = $pdf->GetY();
            $pdf->MultiCell(14, $lineHeight, $row['keterangan'], 0, 'C');
            $textHeight = $pdf->GetY() - $y0;
            $pdf->SetTextColor(0, 0, 0);

            $centerY = $startY + ($maxHeight - $textHeight) / 2;
            $pdf->SetXY($currentX, $centerY);
            $pdf->MultiCell(14, $lineHeight, $row['keterangan'], 0, 'C');

            // Pindah ke baris berikutnya
            $pdf->SetY($startY + $maxHeight);

            $no++;

            // 3) akumulasi nilai-nilai numeric
            $totals['qty_pcs']          += $row['qty_pcs'];
            $totals['kgs']              += $row['kgs'];
            $totals['terima_kg']        += $row['terima_kg'];
            $totals['sisa_benang']      += $row['sisa_bb_mc'];
            $totals['plus_mc_pcs']      += $row['sisa_order_pcs'];
            $totals['plus_mc_kg']       += $row['poplus_mc_kg'];
            $totals['plus_mc_cns']      += $row['poplus_mc_cns'];
            $totals['plus_pck_pcs']     += $row['plus_pck_pcs'];
            $totals['plus_pck_kg']      += $row['plus_pck_kg'];
            $totals['plus_pck_cns']     += $row['plus_pck_cns'];
            $totals['lebih_pakai']      += $row['lebih_pakai_kg'];
            // dst.

            // update prevKey
            $prevKey = $currentKey;
        }

        // Setelah loop, cetak subtotal untuk group terakhir
        if ($prevKey !== null) {
            $this->printSubtotalRow($pdf, $totals);
        }

        $currentY = $pdf->GetY();
        $footerY = 170; // batas sebelum footer (tergantung desain kamu)

        // Tinggi standar baris kosong (bisa sesuaikan ke $maxHeight rata-rata atau tetap 6 misal)
        $emptyRowHeight = 5;

        // Selama posisi Y masih di atas footer, tambahkan baris kosong
        while ($currentY + $emptyRowHeight < $footerY) {
            $startX = $pdf->GetX();
            $pdf->SetXY($startX, $currentY);

            // Gambar semua cell border kosong
            $cellWidths = [12, 12, 19, 16, 15, 10, 7, 9, 7, 12, 8, 7, 8, 11, 7, 7, 8, 7, 7, 7, 8, 7, 7, 7, 6, 12, 6, 14, 14];
            foreach ($cellWidths as $width) {
                $pdf->Cell($width, $emptyRowHeight, '', 1, 0, 'C');
            }
            $pdf->Ln($emptyRowHeight);

            $currentY = $pdf->GetY();
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

    private function renderHeaderPage($pdf, $area, $tglBuat, $deliveryAkhir)
    {
        // Inisialisasi FPDF
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
    }

    private function renderTableHeader($pdf)
    {
        // Tabel Header Baris Pertama
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(12, 12, 'Model', 1, 0, 'C');
        $pdf->Cell(12, 12, 'Warna', 1, 0, 'C');
        $pdf->Cell(19, 12, 'Item Type', 1, 0, 'C');
        $pdf->Cell(16, 12, 'Kode Warna', 1, 0, 'C');
        $pdf->Cell(15, 12, 'Style / Size', 1, 0, 'C');
        MultiCellFit($pdf, 10, 6, "Kompo\nsisi(%)");
        MultiCellFit($pdf, 7, 6, "GW/\nPcs");
        MultiCellFit($pdf, 9, 6, "Qty/\nPcs");
        $pdf->Cell(7, 12, 'Loss', 1, 0, 'C');
        MultiCellFit($pdf, 12, 6, "Pesanan\nKgs");
        $pdf->Cell(23, 9, 'Terima', 1, 0, 'C');
        MultiCellFit($pdf, 11, 3, "Sisa Benang\ndi Mesin");
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
        $pdf->Cell(8, 3, 'Kg', 1, 0, 'C');
        $pdf->Cell(7, 3, '+ / -', 1, 0, 'C');
        $pdf->Cell(8, 3, '%', 1, 0, 'C');
        $pdf->Cell(11, 3, '', 0, 0, 'C');
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
    }

    private function renderFooterPage($pdf)
    {
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
    }

    private function printSubtotalRow($pdf, array $totals)
    {
        // kolom label “Subtotal”
        $pdf->Cell(91 /*atau lebar yg sesuai*/, 6, 'Subtotal', 'LTR', 0, 'R');

        // kolom-kolom numeric, sesuaikan urutan & lebar
        $pdf->Cell(9, 6, number_format($totals['qty_pcs']), 'LTR', 0, 'C');
        $pdf->Cell(7, 6, '', 'LTR', 0, 'C');
        $pdf->Cell(12, 6, number_format($totals['kgs'], 2), 'LTR', 0, 'C');
        $pdf->Cell(8, 6, number_format($totals['terima_kg'], 2), 'LTR', 0, 'C');
        $pdf->Cell(7, 6, '', 'LTR', 0, 'C');
        $pdf->Cell(8, 6, '', 'LTR', 0, 'C');
        $pdf->Cell(11, 6, number_format($totals['sisa_benang'], 2), 'LTR', 0, 'C');
        $pdf->Cell(7, 6, $totals['plus_mc_pcs'], 'LTR', 0, 'C');
        $pdf->Cell(7, 6, number_format($totals['plus_mc_kg'], 2), 'LTR', 0, 'C');
        $pdf->Cell(8, 6, $totals['plus_mc_cns'], 'LTR', 0, 'C');
        $pdf->Cell(7, 6, '', 'LTR', 0, 'C');
        $pdf->Cell(7, 6, $totals['plus_pck_pcs'], 'LTR', 0, 'C');
        $pdf->Cell(7, 6, number_format($totals['plus_pck_kg'], 2), 'LTR', 0, 'C');
        $pdf->Cell(8, 6, $totals['plus_pck_cns'], 'LTR', 0, 'C');
        $pdf->Cell(7, 6, '', 'LTR', 0, 'C');
        $pdf->Cell(7, 6, number_format($totals['lebih_pakai'], 2), 'LTR', 0, 'C');
        $pdf->Cell(7, 6, '', 'LTR', 0, 'C');
        $pdf->Cell(6, 6, number_format($totals['kgs_retur'], 2), 'LTR', 0, 'C');
        $pdf->Cell(12, 6, '', 'LTR', 0, 'C');
        $pdf->Cell(6, 6, '', 'LTR', 0, 'C');
        $pdf->Cell(14, 6, '', 'LTR', 0, 'C');
        $pdf->Cell(14, 6, '', 'LTR', 1, 'C');

        // border bawah
        // (atau jika mau garis ganda, kamu bisa ulang loop Cell dengan border 'LBR')
    }

    public function exportPemesanan($jenis, $area, $tgl_pakai)
    {
        // --- Ambil data dari API ---
        $apiUrl = $this->urlMaterial . "dataPemesananArea"
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
        $error    = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false) {
            return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Curl error: ' . $error]);
        }
        $data = json_decode($response, true);
        if (!is_array($data)) {
            return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Response tidak valid']);
        }

        // --- Definisi kolom (kunci data, label header, tipe) ---
        // tipe: text | number (number kita format ringkas)
        $columns = [
            'tgl_pesan'  => ['header' => 'Tgl Pesan',   'type' => 'text',   'min' => 16, 'max' => 30],
            'no_model'   => ['header' => 'No Model',    'type' => 'text',   'min' => 14, 'max' => 26],
            'item_type'  => ['header' => 'Item Type',   'type' => 'text',   'min' => 24, 'max' => 42],
            'color'      => ['header' => 'Warna',       'type' => 'text',   'min' => 16, 'max' => 26],
            'kode_warna' => ['header' => 'Kode Warna',  'type' => 'text',   'min' => 18, 'max' => 30],
            'jl_mc'      => ['header' => 'Jl MC',       'type' => 'number', 'min' => 10, 'max' => 14],
            'qty_pesan'  => ['header' => "Kgs\nPesan",  'type' => 'number', 'min' => 14, 'max' => 20],
            'cns_pesan'  => ['header' => "Cones\nPesan", 'type' => 'number', 'min' => 12, 'max' => 18],
            'lot_pesan'  => ['header' => 'Lot Pesan',   'type' => 'text',   'min' => 16, 'max' => 28],
            'kgs_out'    => ['header' => "Kgs\nKirim",  'type' => 'number', 'min' => 14, 'max' => 20],
            'cns_out'    => ['header' => "Cones\nKirim", 'type' => 'number', 'min' => 12, 'max' => 18],
            'krg_out'    => ['header' => "Karung\nKirim", 'type' => 'number', 'min' => 12, 'max' => 18],
            'lot_out'    => ['header' => 'Lot Kirim',   'type' => 'text',   'min' => 16, 'max' => 28],
            'ket'        => ['header' => 'Keterangan',  'type' => 'text',   'min' => 20, 'max' => 80], // akan menyerap sisa
        ];

        // --- Siapkan data final utk tabel (gabungkan field ket_area + ket_gbn + po_tambahan) ---
        $rows = [];
        $totalKgsPesan = 0;
        $totalConesPesan = 0;
        $totalKgsKirim = 0;
        $totalConesKirim = 0;
        $totalKarungKirim = 0;

        foreach ($data as $r) {
            $poTambahan = !empty($r['po_tambahan']) && (int)$r['po_tambahan'] === 1 ? '(+)' : '';
            $ketGbn     = !empty($r['ket_gbn']) ? (' / ' . $r['ket_gbn']) : '';
            $ketText    = trim((string)($r['ket_area'] ?? '') . $ketGbn);

            $final = [
                'tgl_pesan'  => (string)($r['tgl_pesan'] ?? ''),
                'no_model'   => $poTambahan . (string)($r['no_model'] ?? ''),
                'item_type'  => (string)($r['item_type'] ?? ''),
                'color'      => (string)($r['color'] ?? ''),
                'kode_warna' => (string)($r['kode_warna'] ?? ''),
                'jl_mc'      => (string)($r['jl_mc'] ?? ''),
                'qty_pesan'  => is_numeric($r['qty_pesan'] ?? null) ? number_format((float)$r['qty_pesan'], 2) : (string)($r['qty_pesan'] ?? ''),
                'cns_pesan'  => is_numeric($r['cns_pesan'] ?? null) ? number_format((float)$r['cns_pesan']) : (string)($r['cns_pesan'] ?? ''),
                'lot_pesan'  => (string)($r['lot_pesan'] ?? ''),
                'kgs_out'    => ($r['kgs_out'] ?? 0) > 0 ? number_format((float)$r['kgs_out'], 2) : '',
                'cns_out'    => ($r['cns_out'] ?? 0) > 0 ? (string)(int)$r['cns_out'] : '',
                'krg_out'    => ($r['krg_out'] ?? 0) > 0 ? (string)(int)$r['krg_out'] : '',
                'lot_out'    => (string)($r['lot_out'] ?? ''),
                'ket'        => $ketText,
            ];
            $rows[] = $final;

            // hitung total numeric pakai nilai asli
            $totalKgsPesan    += (float)($r['qty_pesan'] ?? 0);
            $totalConesPesan  += (float)($r['cns_pesan'] ?? 0);
            $totalKgsKirim    += (float)($r['kgs_out']   ?? 0);
            $totalConesKirim  += (float)($r['cns_out']   ?? 0);
            $totalKarungKirim += (float)($r['krg_out']   ?? 0);
        }

        // --- Inisialisasi PDF ---
        $pdf = new PDF('L', 'mm', 'A4');
        $pdf->AliasNbPages();
        $pdf->SetAutoPageBreak(true, 8); // margin bawah 8 mm
        $pdf->AddPage();

        // nomor halaman custom (kalau kamu punya method lain, panggil di sini)
        if (method_exists($this, 'generatePageNumber')) {
            $this->generatePageNumber($pdf);
        }

        // --- Judul & Info Laporan ---
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 8, 'REPORT PEMESANAN BAHAN BAKU', 0, 1, 'C');

        $pdf->SetFont('Arial', 'B', 7);
        $pdf->Cell(40, 6, 'JENIS BAHAN BAKU', 0, 0, 'L');
        $pdf->Cell(80, 6, ': ' . $jenis, 0, 0, 'L');
        $pdf->Cell(15, 6, 'AREA', 0, 0, 'L');
        $pdf->Cell(80, 6, ': ' . $area, 0, 0, 'L');
        $pdf->Cell(22, 6, 'TANGGAL PAKAI', 0, 0, 'L');
        $pdf->Cell(0, 6, ': ' . $tgl_pakai, 0, 1, 'L');
        $pdf->Ln(1);

        // --- Hitung lebar kolom otomatis berbasis konten ---
        $tableTotalWidth = 297 - 10 - 10; // A4 landscape, margin 10mm kiri/kanan
        $lineHeight      = 4.5;           // tinggi tiap baris wrap
        $pdf->SetFont('Arial', '', 7);

        // 1) hitung "natural width" per kolom dari header & data (pakai GetStringWidth + padding)
        $natural = [];
        foreach ($columns as $key => $meta) {
            $pdf->SetFont('Arial', 'B', 7);
            $pad = 2 * $pdf->getCellMargin() + 2;   // padding kiri+kanan + napas ekstra 2mm

            $hdrW = $pdf->GetStringWidth(str_replace("\n", " ", $meta['header'])) + $pad;
            $pdf->SetFont('Arial', '', 7);

            $maxDataW = 0;
            foreach ($rows as $row) {
                $txt = (string) ($row[$key] ?? '');
                $w    = $pdf->GetStringWidth($txt) + $pad;
                if ($w > $maxDataW) $maxDataW = $w;
            }

            // ambil yang terbesar lalu clamp ke min/max
            $nw = max($meta['min'], min($maxDataW, $meta['max']));
            // pastikan header muat minimal
            $nw = max($nw, min($meta['max'], $hdrW, max($meta['min'], $hdrW)));
            $natural[$key] = $nw;
        }

        // 2) skala agar total = tableTotalWidth (kolom Keterangan boleh menyerap sisa)
        $sumNatural = array_sum($natural);
        $widths     = $natural;

        if ($sumNatural != 0) {
            $scale = $tableTotalWidth / $sumNatural;
            foreach ($widths as $k => $w) {
                $widths[$k] = round($w * $scale, 2);
            }
        }

        // 3) pastikan total tepat (lempar selisih ke 'ket')
        $delta = $tableTotalWidth - array_sum($widths);
        if (abs($delta) >= 0.01 && isset($widths['ket'])) {
            $widths['ket'] = max($columns['ket']['min'], min($columns['ket']['max'], $widths['ket'] + $delta));
        }

        // --- Render header tabel ---
        // ganti label header yang multiline (supaya wrap) – sudah dipasang \n
        $pdf->renderHeaderRow($columns, $lineHeight, $widths);

        // helper untuk cek page break per baris
        $renderHeaderAgain = function () use ($pdf, $columns, $lineHeight, $widths) {
            // panggil header ulang
            $pdf->renderHeaderRow($columns, $lineHeight, $widths);
        };

        // --- Render isi tabel (auto-wrap & auto-height) ---
        foreach ($rows as $row) {
            // hitung max lines per kolom untuk tentukan tinggi baris
            $maxLines = 1;
            foreach ($columns as $key => $meta) {
                $txt = (string) ($row[$key] ?? '');
                $n   = $pdf->nbLines($widths[$key], $txt);
                if ($n > $maxLines) $maxLines = $n;
            }
            $rowHeight = $maxLines * $lineHeight;

            // kalau bakal melewati page trigger, add page & header ulang
            if ($pdf->willExceedPage($rowHeight)) {
                // footer custom sebelum addPage?
                if (method_exists($this, 'footerPemesanan')) {
                    $this->footerPemesanan($pdf);
                }
                $pdf->AddPage();
                if (method_exists($this, 'generatePageNumber')) {
                    $this->generatePageNumber($pdf);
                }
                $renderHeaderAgain();
            }

            // tulis baris
            foreach ($columns as $key => $meta) {
                $txt = (string) ($row[$key] ?? '');
                $pdf->cellWrap($widths[$key], $lineHeight, $txt, 1, ($meta['type'] === 'number' ? 'C' : 'C'), $rowHeight);
            }
            $pdf->Ln($rowHeight);
        }

        // --- Grand Total ---
        $pdf->SetFont('Arial', 'B', 7);

        // hitung tinggi baris total (pakai 1 baris saja)
        $gtHeight = $lineHeight * 2; // sedikit lebih tinggi biar menonjol

        // tulis label "GRAND TOTAL" di kolom paling kiri sampai sebelum kolom Kgs Kirim (sesuai kolom kita)
        // kita akan isi per kolom sesuai urutan: qty_pesan, cns_pesan, (lot_pesan kosong), kgs_out, cns_out, krg_out, (lot_out kosong), (ket kosong)
        // jadi: kolom yang diisi angka total: qty_pesan, cns_pesan, kgs_out, cns_out, krg_out
        $sumBefore = 0.0;
        $order     = array_keys($columns);
        // tulis label yang membentang dari kolom pertama sampai sebelum 'qty_pesan'
        $stopKey   = 'qty_pesan';

        foreach ($order as $k) {
            if ($k === $stopKey) break;
            $sumBefore += $widths[$k];
        }
        // label GRAND TOTAL
        $pdf->cellWrap($sumBefore, $lineHeight, 'GRAND TOTAL', 1, 'C', $gtHeight);

        // lalu isi kolom berikutnya satu per satu
        $writeCell = function ($key, $text = '', $bold = true) use ($pdf, $widths, $lineHeight, $gtHeight) {
            if ($bold) $pdf->SetFont('Arial', 'B', 7);
            $pdf->cellWrap($widths[$key], $lineHeight, $text, 1, 'C', $gtHeight);
        };

        $writeCell('qty_pesan', number_format($totalKgsPesan, 2));
        $writeCell('cns_pesan', number_format($totalConesPesan));
        $writeCell('lot_pesan', '');
        $writeCell('kgs_out',   number_format($totalKgsKirim, 2));
        $writeCell('cns_out',   number_format($totalConesKirim));
        $writeCell('krg_out',   number_format($totalKarungKirim));
        $writeCell('lot_out',   '');
        $writeCell('ket',       '');

        $pdf->Ln($gtHeight);

        // --- Footer terakhir (jika ada) ---
        if (method_exists($this, 'footerPemesanan')) {
            $this->footerPemesanan($pdf);
        }

        // --- Output PDF ---
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
        // simpan posisi cursor sekarang
        $curX = $pdf->GetX();
        $curY = $pdf->GetY();

        // posisi vertikal di dekat header
        $pdf->SetY(10);
        $pdf->SetFont('Arial', 'I', 7);

        // lebar 0 = otomatis sampai right margin, 'R' = rata kanan
        $text = 'Page ' . $pdf->PageNo() . '/{nb}';
        $pdf->Cell(0, 5, $text, 0, 0, 'R');

        // kembalikan posisi cursor ke semula
        $pdf->SetXY($curX, $curY);
    }

    private function footerPemesanan($pdf)
    {
        // 15 mm dari bawah, lalu Cell lebar 0 = center di area print
        $pdf->SetY(-18);
        $pdf->SetFont('Arial', 'I', 7);

        // {nb} akan terisi jika kamu sudah memanggil $pdf->AliasNbPages() setelah instansiasi
        $text = 'FOR_KK_369/TGL_REV_13_07_20/REV_02/HAL ' . $pdf->PageNo() . '/{nb}';
        $pdf->Cell(0, 10, $text, 0, 0, 'C');
    }


    public function exportPdfRetur($area)
    {
        $noModel = $this->request->getGet('noModel');
        $tglBuat = $this->request->getGet('tglBuat');

        // Ambil data berdasarkan area dan model
        $apiUrl = $this->urlMaterial . "listExportRetur/"
            . $area
            . "?noModel=" . urlencode($noModel)
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

        $lossValue = isset($data[0]['loss']) ? $data[0]['loss'] . '%' : '';
        $pdf->Cell(20, 5, 'Loss F.Up', 0, 0, 'L');
        $pdf->Cell(75, 5, ': ' . $lossValue, 0, 0, 'L');

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
        MultiCellFit($pdf, 10, 6, "Kompo\nsisi(%)");
        MultiCellFit($pdf, 7, 6, "GW/\nPcs");
        MultiCellFit($pdf, 9, 6, "Qty/\nPcs");
        $pdf->Cell(7, 12, 'Loss', 1, 0, 'C');
        MultiCellFit($pdf, 12, 6, "Pesanan\nKgs");
        $pdf->Cell(23, 9, 'Terima', 1, 0, 'C');
        MultiCellFit($pdf, 11, 3, "Sisa Benang\ndi Mesin");
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
        $pdf->Cell(8, 3, 'Kg', 1, 0, 'C');
        $pdf->Cell(7, 3, '+ / -', 1, 0, 'C');
        $pdf->Cell(8, 3, '%', 1, 0, 'C');
        $pdf->Cell(11, 3, '', 0, 0, 'C');
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
        $pdf->SetFont('Arial', '', 7);
        $no = 1;
        $yLimit = 170;
        $lineHeight = 4;

        $prevKey    = null;
        $totals     = [
            'qty_pcs' => 0,
            'kgs'      => 0,
            'terima_kg'      => 0,
            'sisa_benang'      => 0,
            'plus_mc_pcs'      => 0,
            'plus_mc_kg'      => 0,
            'plus_mc_cns'      => 0,
            'plus_pck_pcs'      => 0,
            'plus_pck_kg'      => 0,
            'plus_pck_cns'      => 0,
            'lebih_pakai'      => 0,
            'kgs_retur'      => 0,
        ];


        foreach ($data as $row) {
            // bangun “key” unik untuk group
            $currentKey = implode('|', [
                $row['no_model'],
                $row['color'],
                $row['item_type'],
                $row['kode_warna'],
            ]);

            // 1) kalau pindah group (bukan pertama)
            if ($prevKey !== null && $currentKey !== $prevKey) {
                // cetak subtotal untuk group sebelumnya
                $this->printSubtotalRow($pdf, $totals);

                // reset accumulator
                foreach ($totals as $k => $v) {
                    $totals[$k] = 0;
                }
            }

            // Cek dulu apakah nambah baris ini bakal lewat batas
            if ($pdf->GetY() + $lineHeight > $yLimit) {
                $this->renderFooterPage($pdf);
                $pdf->AddPage();
                $this->renderHeaderPage($pdf, $area, $tglBuat, $deliveryAkhir);

                // Gambar ulang margin & header halaman
                $pdf->SetDrawColor(0, 0, 0);
                $pdf->SetLineWidth(0.4);
                $pdf->Rect(9, 9, 279, 192);
                $pdf->SetLineWidth(0.2);
                $pdf->Rect(10, 10, 277, 190);

                // (Kalau header halaman lain ada lagi, panggil disini...)

                // Gambar ulang header tabel
                $this->renderTableHeader($pdf);
            }

            $startX = $pdf->GetX();
            $startY = $pdf->GetY();

            // 1. SIMULASI: Hitung tinggi maksimum yang dibutuhkan
            $heights = [];
            $tempX = $startX;

            $pdf->SetTextColor(255, 255, 255); // putih agar tidak terlihat

            $multiCellData = [
                ['w' => 12, 'text' => $row['no_model']],
                ['w' => 12, 'text' => $row['color']],
                ['w' => 19, 'text' => $row['item_type']],
                ['w' => 16, 'text' => $row['kode_warna']],
                ['w' => 15, 'text' => ''],
                ['w' => 14, 'text' => $row['kategori']],
            ];

            foreach ($multiCellData as $data) {
                $pdf->SetXY($tempX, $startY);
                $y0 = $pdf->GetY();
                $pdf->MultiCell($data['w'], $lineHeight, $data['text'], 0, 'C');
                $heights[] = $pdf->GetY() - $y0;
                $tempX += $data['w'];
            }

            $pdf->SetTextColor(0, 0, 0); // kembali ke hitam
            $maxHeight = max($heights);

            // 2. RENDER: Gambar semua cell dengan tinggi yang sama
            $pdf->SetXY($startX, $startY);

            // Buat semua cell dengan border dan tinggi yang sama, tapi isi kosong dulu
            $cellWidths = [12, 12, 19, 16, 15, 10, 7, 9, 7, 12, 8, 7, 8, 11, 7, 7, 8, 7, 7, 7, 8, 7, 7, 7, 6, 12, 6, 14, 14];
            $cellData = [
                '',
                '',
                '',
                '',
                '', // 5 kolom multiCell akan diisi kemudian
                $row['composition'],
                $row['gw'],
                $row['qty_pcs'],
                $row['loss'],
                $row['kgs'],
                number_format($row['terima_kg'], 2),
                number_format($row['terima_kg'] - $row['kgs'], 2),
                number_format($row['terima_kg'] / $row['kgs'], 2) * 100 . '%', // terima
                number_format($row['sisa_bb_mc'], 2), // sisa mesin
                $row['sisa_order_pcs'] == 0 ? '' : $row['sisa_order_pcs'],
                $row['poplus_mc_kg'] == 0 ? '' : number_format($row['poplus_mc_kg'], 2),
                $row['poplus_mc_cns'] == 0 ? '' : $row['poplus_mc_cns'],
                ($row['poplus_mc_kg'] / $row['kgs']) == 0 ? '' : number_format($row['poplus_mc_kg'] / $row['kgs'], 2) * 100 . '%',
                $row['plus_pck_pcs'] == 0 ? '' : number_format($row['plus_pck_pcs'], 2),
                $row['plus_pck_kg'] == 0 ? '' : number_format($row['plus_pck_kg'], 2),
                $row['plus_pck_cns'] == 0 ? '' : $row['plus_pck_cns'],
                ($row['plus_pck_kg'] / $row['kgs']) == 0 ? '' : number_format($row['plus_pck_kg'] / $row['kgs'], 2) * 100 . '%',
                $row['lebih_pakai_kg'] == 0 ? '' : number_format($row['lebih_pakai_kg'], 2),
                ($row['lebih_pakai_kg'] / $row['kgs']) == 0 ? '' : number_format($row['lebih_pakai_kg'] / $row['kgs'], 2) * 100 . '%',
                number_format($row['kgs_retur'], 2),
                '',
                '',
                '',
                '' // keterangan akan diisi kemudian
            ];

            // Gambar semua cell dengan border
            for ($i = 0; $i < count($cellWidths); $i++) {
                $pdf->Cell($cellWidths[$i], $maxHeight, $cellData[$i], 1, 0, 'C');
            }

            // 3. ISI TEXT untuk kolom multiCell (overlay tanpa border)
            $currentX = $startX;

            // No Model
            $textCenterY = $startY + ($maxHeight / 2) - ($lineHeight / 2);
            $pdf->SetXY($currentX, $textCenterY);
            $pdf->Cell(12, $lineHeight, $row['no_model'], 0, 0, 'C');
            $currentX += 12;

            // Color
            $pdf->SetXY($currentX, $startY);
            $pdf->SetTextColor(255, 255, 255);
            $y0 = $pdf->GetY();
            $pdf->MultiCell(12, $lineHeight, $row['color'], 0, 'C');
            $textHeight = $pdf->GetY() - $y0;
            $pdf->SetTextColor(0, 0, 0);

            $centerY = $startY + ($maxHeight - $textHeight) / 2;
            $pdf->SetXY($currentX, $centerY);
            $pdf->MultiCell(12, $lineHeight, $row['color'], 0, 'C');
            $currentX += 12;

            // Item Type (mungkin multiline)
            $pdf->SetXY($currentX, $startY);
            $pdf->SetTextColor(255, 255, 255);
            $y0 = $pdf->GetY();
            $pdf->MultiCell(19, $lineHeight, $row['item_type'], 0, 'C');
            $textHeight = $pdf->GetY() - $y0;
            $pdf->SetTextColor(0, 0, 0);

            $centerY = $startY + ($maxHeight - $textHeight) / 2;
            $pdf->SetXY($currentX, $centerY);
            $pdf->MultiCell(19, $lineHeight, $row['item_type'], 0, 'C');
            $currentX += 19;

            // Kode Warna (mungkin multiline)
            $pdf->SetXY($currentX, $startY);
            $pdf->SetTextColor(255, 255, 255);
            $y0 = $pdf->GetY();
            $pdf->MultiCell(16, $lineHeight, $row['kode_warna'], 0, 'C');
            $textHeight = $pdf->GetY() - $y0;
            $pdf->SetTextColor(0, 0, 0);

            $centerY = $startY + ($maxHeight - $textHeight) / 2;
            $pdf->SetXY($currentX, $centerY);
            $pdf->MultiCell(16, $lineHeight, $row['kode_warna'], 0, 'C');
            $currentX += 16;

            // Style Size
            $centerY = $startY + ($maxHeight - $textHeight) / 2;
            $pdf->SetXY($currentX, $centerY);
            $pdf->MultiCell(15, $lineHeight, '', 0, 'C');
            $currentX += 15;

            // Skip kolom yang sudah terisi dengan Cell biasa
            $currentX += 10 + 7 + 9 + 7 + 12 + 8 + 7 + 8 + 11 + 7 + 7 + 8 + 7 + 7 + 7 + 8 + 7 + 7 + 7 + 6 + 12 + 6 + 14;

            // Keterangan (mungkin multiline)
            $pdf->SetXY($currentX, $startY);
            $pdf->SetTextColor(255, 255, 255);
            $y0 = $pdf->GetY();
            $pdf->MultiCell(14, $lineHeight, $row['kategori'], 0, 'C');
            $textHeight = $pdf->GetY() - $y0;
            $pdf->SetTextColor(0, 0, 0);

            $centerY = $startY + ($maxHeight - $textHeight) / 2;
            $pdf->SetXY($currentX, $centerY);
            $pdf->MultiCell(14, $lineHeight, $row['kategori'], 0, 'C');

            // Pindah ke baris berikutnya
            $pdf->SetY($startY + $maxHeight);

            $no++;

            // 3) akumulasi nilai-nilai numeric
            // $totals['qty_pcs']          += $row['qty_pcs'];
            // $totals['kgs']              += $row['kgs'];
            // $totals['terima_kg']        += $row['terima_kg'];
            // $totals['sisa_benang']      += $row['sisa_bb_mc'];
            // $totals['plus_mc_pcs']      += $row['sisa_order_pcs'];
            // $totals['plus_mc_kg']       += $row['poplus_mc_kg'];
            // $totals['plus_mc_cns']      += $row['poplus_mc_cns'];
            // $totals['plus_pck_pcs']     += $row['plus_pck_pcs'];
            // $totals['plus_pck_kg']      += $row['plus_pck_kg'];
            // $totals['plus_pck_cns']     += $row['plus_pck_cns'];
            // $totals['lebih_pakai']      += $row['lebih_pakai_kg'];
            $totals['kgs_retur']      += $row['kgs_retur'];
            // dst.

            // update prevKey
            $prevKey = $currentKey;
        }

        // Setelah loop, cetak subtotal untuk group terakhir
        if ($prevKey !== null) {
            $this->printSubtotalRow($pdf, $totals);
        }

        $currentY = $pdf->GetY();
        $footerY = 170; // batas sebelum footer (tergantung desain kamu)

        // Tinggi standar baris kosong (bisa sesuaikan ke $maxHeight rata-rata atau tetap 6 misal)
        $emptyRowHeight = 5;

        // Selama posisi Y masih di atas footer, tambahkan baris kosong
        while ($currentY + $emptyRowHeight < $footerY) {
            $startX = $pdf->GetX();
            $pdf->SetXY($startX, $currentY);

            // Gambar semua cell border kosong
            $cellWidths = [12, 12, 19, 16, 15, 10, 7, 9, 7, 12, 8, 7, 8, 11, 7, 7, 8, 7, 7, 7, 8, 7, 7, 7, 6, 12, 6, 14, 14];
            foreach ($cellWidths as $width) {
                $pdf->Cell($width, $emptyRowHeight, '', 1, 0, 'C');
            }
            $pdf->Ln($emptyRowHeight);

            $currentY = $pdf->GetY();
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
}
