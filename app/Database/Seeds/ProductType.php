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
                'keterangan' => 'Normal Sock',
            ],
            [
                'konversi' => 0,
                'product_type' => 'NS-FS',                
                'keterangan' => 'Normal Sock',
            ],
            [
                'konversi' => 0,
                'product_type' => 'NS-MP',                
                'keterangan' => 'Normal Sock',
            ],
            [
                'konversi' => 0,
                'product_type' => 'S-PS',
                'keterangan' => 'Sneaker',
            ],
            [
                'konversi' => 0,
                'product_type' => 'S-FS',
                'keterangan' => 'Sneaker',
            ],
            [
                'konversi' => 0,
                'product_type' => 'S-MP',
                'keterangan' => 'Sneaker',
            ],
            [
                'konversi' => 0,
                'product_type' => 'F-PS',
                'keterangan' => 'Footies',
            ],
            [
                'konversi' => 0,
                'product_type' => 'F-FS',
                'keterangan' => 'Footies',
            ],
            [
                'konversi' => 0,
                'product_type' => 'F-MP',
                'keterangan' => 'Footies',
            ],
            [
                'konversi' => 0,
                'product_type' => 'KH-PS',
                'keterangan' => 'Knee High',
            ],
            [
                'konversi' => 0,
                'product_type' => 'KH-FS',
                'keterangan' => 'Knee High',
            ],
            [
                'konversi' => 0,
                'product_type' => 'KH-MP',
                'keterangan' => 'Knee High',
            ],
            [
                'konversi' => 0,
                'product_type' => 'TG-PS',
                'keterangan' => 'Tight',
            ],
            [
                'konversi' => 0,
                'product_type' => 'TG-FS',
                'keterangan' => 'Tight',
            ],
            [
                'konversi' => 0,
                'product_type' => 'TG-MP',
                'keterangan' => 'Tight',
            ],
            [
                'konversi' => 0,
                'product_type' => 'SS-PS',
                'keterangan' => 'Short Shaft',
            ],
            [
                'konversi' => 0,
                'product_type' => 'SS-FS',
                'keterangan' => 'Short Shaft',
            ],
            [
                'konversi' => 0,
                'product_type' => 'SS-MP',
                'keterangan' => 'Short Shaft',
            ],
            [
                'konversi' => 0,
                'product_type' => 'GL-PS',
                'keterangan' => 'Gloves',
            ],
        ];
        $this->db->table('master_product_type')->insertBatch($data);
    }
}
