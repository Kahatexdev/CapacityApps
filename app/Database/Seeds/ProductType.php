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
                'product_type' => 'NS-FP',                
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
                'product_type' => 'S-FP',
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
                'product_type' => 'F-FP',
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
                'product_type' => 'KH-FP',
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
                'product_type' => 'TG-FP',
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
                'product_type' => 'SS-FP',
                'keterangan' => 'Short Shaft',
            ],
            [
                'konversi' => 0,
                'product_type' => 'SS-MP',
                'keterangan' => 'Short Shaft',
            ],
            [
                'konversi' => 0,
                'product_type' => 'GL-PL',
                'keterangan' => 'Gloves Plain',
            ],
            [
                'konversi' => 0,
                'product_type' => 'GL-ST',
                'keterangan' => 'Gloves Stripe',
            ],
            [
                'konversi' => 0,
                'product_type' => 'GL-FL',
                'keterangan' => 'Gloves Fingerless',
            ],
            [
                'konversi' => 0,
                'product_type' => 'GL-MT',
                'keterangan' => 'Gloves Mitten',
            ],
            [
                'konversi' => 0,
                'product_type' => 'HT-PL',
                'keterangan' => 'Hat Plain',
            ],
            [
                'konversi' => 0,
                'product_type' => 'HT-ST',
                'keterangan' => 'Hat Stripe',
            ],

        ];
        $this->db->table('master_product_type')->insertBatch($data);
    }
}
