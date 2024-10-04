<?php

namespace App\Services;

use DateTime;
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
use App\Models\MonthlyMcModel;

class orderServices
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
    protected $globalModel;


    public function __construct()
    {
        $this->globalModel = new MonthlyMcModel();
        $this->jarumModel = new DataMesinModel();
        $this->bookingModel = new BookingModel();
        $this->productModel = new ProductTypeModel();
        $this->produksiModel = new ProduksiModel();
        $this->orderModel = new OrderModel();
        $this->ApsPerstyleModel = new ApsPerstyleModel();
        $this->liburModel = new LiburModel();
        $this->KebutuhanMesinModel = new KebutuhanMesinModel();
        $this->MesinPlanningModel = new MesinPlanningModel();
    }
    public function statusOrder($bulan)
    {
        $date = DateTime::createFromFormat('F-Y', $bulan);
        $bulanIni = $date->format('F-Y');
        $awalBulan = $date->format('Y-m-01');
        $akhirBulan = date('Y-m-t', strtotime('+2 months'));
        // Inisialisasi total order dan sisa
        $statusOrder = [];

        // Inisialisasi total order dan sisa
        $statusOrder['totalOrderSocks'] = 0;
        $statusOrder['totalSisaSocks'] = 0;
        $statusOrder['totalOrderGloves'] = 0;
        $statusOrder['totalSisaGloves'] = 0;

        $statusOrderSocks = $this->ApsPerstyleModel->statusOrderSock($awalBulan, $akhirBulan);
        $statusOrderGloves = $this->ApsPerstyleModel->statusOrderGloves($awalBulan, $akhirBulan);

        // Gabungkan data socks
        foreach ($statusOrderSocks as $order) {
            $month = $order['month']; // Nama bulan
            // Cek jika bulan sudah ada di $statusOrder
            if (!isset($statusOrder[$month])) {
                $statusOrder[$month] = [
                    'qty' => 0,
                    'sisa' => 0,
                    'socks' => [
                        'qty' => 0,
                        'sisa' => 0,
                    ],
                    'gloves' => [
                        'qty' => 0,
                        'sisa' => 0,
                    ],
                ];
            }
            // Update qty dan sisa untuk socks
            $statusOrder[$month]['socks']['qty'] += $order['qty'];
            $statusOrder[$month]['socks']['sisa'] += $order['sisa'];
            // Tambahkan total order dan sisa socks
            $statusOrder['totalOrderSocks'] += $order['qty'];
            $statusOrder['totalSisaSocks'] += $order['sisa'];
        }

        // Gabungkan data gloves
        foreach ($statusOrderGloves as $order) {
            $month = $order['month'];
            // Cek jika bulan sudah ada di $statusOrder
            if (!isset($statusOrder[$month])) {
                $statusOrder[$month] = [
                    'qty' => 0,
                    'sisa' => 0,
                    'socks' => [
                        'qty' => 0,
                        'sisa' => 0,
                    ],
                    'gloves' => [
                        'qty' => 0,
                        'sisa' => 0,
                    ],
                ];
            }
            // Update qty dan sisa untuk gloves
            $statusOrder[$month]['gloves']['qty'] += $order['qty'];
            $statusOrder[$month]['gloves']['sisa'] += $order['sisa'];
            // Tambahkan total order dan sisa gloves
            $statusOrder['totalOrderGloves'] += $order['qty'];
            $statusOrder['totalSisaGloves'] += $order['sisa'];
        }

        // Menghitung total per bulan (gabungan socks dan gloves)
        foreach ($statusOrder as $month => $data) {
            if (is_array($data)) {
                $statusOrder[$month]['qty'] = $data['socks']['qty'] + $data['gloves']['qty'];
                $statusOrder[$month]['sisa'] = $data['socks']['sisa'] + $data['gloves']['sisa'];
            }
        }

        // Hitung Grand Total
        $grandTotalOrder = $statusOrder['totalOrderSocks'] + $statusOrder['totalOrderGloves'];
        $grandTotalSisa = $statusOrder['totalSisaSocks'] + $statusOrder['totalSisaGloves'];

        // Tambahkan ke array statusOrder
        $statusOrder['grandTotalOrder'] = $grandTotalOrder;
        $statusOrder['grandTotalSisa'] = $grandTotalSisa;
        return $statusOrder;
    }
}
