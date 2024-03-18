<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ProductType extends Seeder
{
    public function run()
    {
        $data = [
            [
                'konversi' => 0,
                'product_type' => 'NS-PS',
            ],
            [
                'konversi' => 0,
                'product_type' => 'NS-FS',
            ],
            [
                'konversi' => 0,
                'product_type' => 'NS-MP',
            ],
        ];
        $this->db->table('user')->insertBatch($data);
    }
}
