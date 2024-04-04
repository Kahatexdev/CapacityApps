<?php

namespace App\Controllers;

use App\Models\TestModel;
use App\Models\LiburModel;
use App\Controllers\BaseController;

class Checkdate extends BaseController
{
    public function index()
    {
        helper('form');
        
        // Buat instance model
        $testModel = new TestModel();

        // Ambil data dari database
        $optionsData = $testModel->getDeliveryData();

        // Array untuk menyimpan opsi-opsi
        $options = [];

        // Loop untuk membuat opsi-opsi dan menghitung kumulatif
        $qtyTotal = 0;
        $hariTotal = 0;
        foreach ($optionsData as $key => $option) {
            $delivery = $option['delivery'];
            $qty1 = $option['sisa'];            
            $hari1 = $option['totalhari'];
            $keterangan = $option['keterangan'];

            // Akumulasi nilai-nilai
            $qtyTotal += $qty1;
            $hariTotal += $hari1;

            $options["OPT " . chr(65 + $key)] = [
                'Delivery' => $delivery,
                'Qty Di Delivery' => $qty1,
                'QtyTotal' => $qtyTotal,
                'Jumlah HariTotal' => $hari1,
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
        $maxValue = 0;
        $maxValueOption = null;

        // Iterasi melalui setiap opsi
        foreach ($options as $opt => $data) {
            // Tampilkan total sampai opsi saat ini
            echo "Total sampai $opt: <br>";
            echo "Delivery : ".date('d-M-Y', strtotime($data['Delivery']))."<br>";
            echo "Qty di Delivery : " . $data['Qty Di Delivery'] . "<br>";
            echo "Qty Total: " . $data['QtyTotal'] . "<br>";
            echo "Jumlah Hari Total: " . $data['Jumlah HariTotal'] . "<br>";
            $expressionValue = $data['QtyTotal'] / $data['Jumlah HariTotal'] / 14 / 12; //14 ini adalah targetnya

            // Output the value for the current option
            echo "Kebutuhan Mesin Sampai $opt: " . ceil($expressionValue) . "<br>";

            // Perbedaan antara kumulatif dan nilai opsi saat ini
            $qtyDiff = $totalQty - $data['QtyTotal'];
            $hariDiff = $totalHari - $data['Jumlah HariTotal'];

            // Tampilkan hasil perbandingan
            echo "Perbedaan Qty: " . abs($qtyDiff) . "<br>";
            echo "<br><br>";

            // Update total untuk kategori berikutnya
            $totalQty = $data['QtyTotal'];
            $totalHari = $data['Jumlah HariTotal'];
            

            if ($expressionValue > $maxValue) {
                $maxValue = $expressionValue;
                $maxValueOption = $opt;
            }
        }
            $maxValueTotalHari = $options[$maxValueOption]['Jumlah HariTotal'];
            $maxValueTotalQty = $options[$maxValueOption]['QtyTotal'];
            echo "Total Hari Yang dibutuhkan : $maxValueTotalHari  Hari <br>";
            echo "Kebutuhan Mesin: " . ceil($maxValue) . " Mesin<br>";
            echo "Jalan mesin selama ".$kebutuhanhari = ceil($totalQty/$maxValue/12/14);
            echo " Hari<br>";
            $dateStopMesin = date('Y-m-d', strtotime('+' . $kebutuhanhari . ' days'));
            echo "<br>";
        
            $liburModel = new LiburModel();
            $startDate = date('Y-m-d'); // Tanggal hari ini
            $endDate = date('Y-m-d',strtotime($dateStopMesin)); // Tanggal stop mesin
            $totalLibur = $liburModel->getTotalLiburBetweenDates($startDate, $endDate);           
            $estimatedStopDate = date('Y-m-d', strtotime("+$totalLibur days", strtotime($dateStopMesin)));
            echo "Estimasi stop mesin: " . date('d-M-Y',strtotime($estimatedStopDate)) ." Sudah Termasuk Libur $totalLibur Hari";
        }
    }
