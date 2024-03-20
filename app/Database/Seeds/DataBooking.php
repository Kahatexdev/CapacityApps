<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DataBooking extends Seeder
{
    public function run()
    {
        
        $data = [
            [
                'tgl_terima_booking' => '2024-03-14',
                'kd_buyer_booking' => 'H&M s76',
                'id_product_type' => 1,
                'no_order' => '80000-3276',
                'no_booking' => 'MF3700',
                'opd' => '2024-04-20',
                'delivery' => '2024-08-20',
                'qty_booking' => 1000000,
                'sisa_booking' => 1000000,
                'needle' => 'JC144',
                'seam' => 'ROSSO NORMAL SOCK',
                'lead_time' => 40,
                'status' => 'Booking Baru',
                'desc' => '5p Harper Normal Sock'
            ]
            ];   
        
    }
}
