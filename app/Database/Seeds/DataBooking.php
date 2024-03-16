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
                'kd_buyer_booking' => 'buyer1',
                'id_product_type' => 1,
                'no_order' => 'ORD001',
                'no_booking' => 'BKG001',
                'opd' => 'OPD1',
                'delivery' => '2024-08-20',
                'qty_booking' => 100,
                'sisa_booking' => 50,
                'needle' => 'needle1',
                'seam' => 'seam1',
            ]
            ];    }
}
