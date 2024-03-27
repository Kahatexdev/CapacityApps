<?php

namespace App\Controllers;

use App\Models\TestModel;
use App\Controllers\BaseController;

class Checkdate extends BaseController
{
    public function index()
    {
        helper('form');
        
        // Buat instance model
        $testModel = new TestModel();

        // Ambil data dari database
        $optionsData = $testModel->findAll();

        // Array untuk menyimpan opsi-opsi
        $options = [];

        // Loop untuk membuat opsi-opsi dan menghitung kumulatif
        $qtyTotal = 0;
        $hariTotal = 0;
        foreach ($optionsData as $key => $option) {
            $qty1 = $option['qty'];
            $hari1 = $option['hari'];

            // Akumulasi nilai-nilai
            $qtyTotal += $qty1;
            $hariTotal += $hari1;

            $options["OPT " . chr(65 + $key)] = [
                'Qty1' => $qty1,
                'Jumlah Hari1' => $hari1,
                'QtyTotal' => $qtyTotal,
                'Jumlah HariTotal' => $hariTotal,
            ];
        }

        // Bandingkan nilai kumulatif dengan masing-masing opsi
        $this->compareOptions($options);
    }

    // Method untuk membandingkan kumulatif nilai dengan masing-masing opsi
    private function compareOptions($options)
    {
        // Total nilai untuk setiap kategori
        $totalQty = 0;
        $totalHari = 0;
        // Inisialisasi variabel untuk nilai maksimum perbedaan Qty
        $maxQtyDiff = 0;
        $maxQtyDiffOption = null; // Tambahkan inisialisasi di sini

        // Iterasi melalui setiap opsi
        foreach ($options as $opt => $data) {
            // Tampilkan total sampai opsi saat ini
            echo "Total sampai $opt: <br>";
            echo "Qty Total: " . $data['QtyTotal'] . "<br>";
            echo "Jumlah Hari Total: " . $data['Jumlah HariTotal'] . "<br>";

            // Perbedaan antara kumulatif dan nilai opsi saat ini
            $qtyDiff = $totalQty - $data['QtyTotal'];
            $hariDiff = $totalHari - $data['Jumlah HariTotal'];

            // Tampilkan hasil perbandingan
            echo "Perbedaan Qty: " . abs($qtyDiff) . "<br>";
            echo "Perbedaan Jumlah Hari: " . abs($hariDiff) . "<br><br>";

            // Update total untuk kategori berikutnya
            $totalQty = $data['QtyTotal'];
            $totalHari = $data['Jumlah HariTotal'];

            // Hitung nilai maksimum perbedaan Qty
            if (abs($qtyDiff) > $maxQtyDiff) {
                $maxQtyDiff = abs($qtyDiff);
                $maxQtyDiffOption = $opt;
            }
        }

        // Tampilkan nilai terbesar dari perbedaan Qty
        echo "Nilai terbesar dari perbedaan Qty: " . $maxQtyDiff . "<br>";

        // Opsi dengan perbedaan qty terbesar
        echo "Opsi dengan perbedaan qty terbesar: ";
        echo $maxQtyDiffOption !== null ? $maxQtyDiffOption : "Tidak ada opsi";

        // Tampilkan qty total dan jumlah hari total sesuai dengan opsi terpilih
        if ($maxQtyDiffOption !== null) {
            echo "<br>Qty Total untuk opsi terpilih: " . $options[$maxQtyDiffOption]['QtyTotal'] . "<br>";
            echo "Jumlah Hari Total untuk opsi terpilih: " . $options[$maxQtyDiffOption]['Jumlah HariTotal'] . "<br>";
            echo "Kebutuhan Mesin: " . ceil($options[$maxQtyDiffOption]['QtyTotal']/($options[$maxQtyDiffOption]['Jumlah HariTotal']-3)/16);
        }
    }
}
