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
            [
                'konversi' => 0,
                'product_type' => 'S-PS',
            ],
            [
                'konversi' => 0,
                'product_type' => 'S-FS',
            ],
            [
                'konversi' => 0,
                'product_type' => 'S-MP',
            ],
            [
                'konversi' => 0,
                'product_type' => 'F-PS',
            ],
            [
                'konversi' => 0,
                'product_type' => 'F-FS',
            ],
            [
                'konversi' => 0,
                'product_type' => 'F-MP',
            ],
            [
                'konversi' => 0,
                'product_type' => 'KH-PS',
            ],
            [
                'konversi' => 0,
                'product_type' => 'KH-FS',
            ],
            [
                'konversi' => 0,
                'product_type' => 'KH-MP',
            ],
            [
                'konversi' => 0,
                'product_type' => 'TG-PS',
            ],
            [
                'konversi' => 0,
                'product_type' => 'TG-FS',
            ],
            [
                'konversi' => 0,
                'product_type' => 'TG-MP',
            ],
            [
                'konversi' => 0,
                'product_type' => 'SS-PS',
            ],
            [
                'konversi' => 0,
                'product_type' => 'SS-FS',
            ],
            [
                'konversi' => 0,
                'product_type' => 'SS-MP',
            ],
            [
                'konversi' => 0,
                'product_type' => 'GL-PS',
            ],
        ];
        $this->db->table('master_product_type')->insertBatch($data);
    }
}
