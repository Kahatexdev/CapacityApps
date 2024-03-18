<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DataMesin extends Seeder
{
    public function run()
    {
        $data = [
            [
                'area' => 'KK1A',
                'jarum' => 'JC120',
                'total_mc' => 2,
                'brand' => 'Lonati 120 OPENTOE',
                'mesin_jalan' => 0,
            ],
            [
                'area' => 'KK1A',
                'jarum' => 'TJ120',
                'total_mc' => 27,
                'brand' => 'Lonati 120 OPENTOE',
                'mesin_jalan' => 17,
            ],
            [
                'area' => 'KK1A',
                'jarum' => 'JC120',
                'total_mc' => 4,
                'brand' => 'Lonati 120 AUTOLINK',
                'mesin_jalan' => 0,
            ],
            [
                'area' => 'KK1A',
                'jarum' => 'TJ120',
                'total_mc' => 15,
                'brand' => 'Lonati 120 AUTOLINK',
                'mesin_jalan' => 12,
            ],
            [
                'area' => 'KK1A',
                'jarum' => 'JC144',
                'total_mc' => 59,
                'brand' => 'Lonati 144 OPENTOE',
                'mesin_jalan' => 53,
            ],
            [
                'area' => 'KK1A',
                'jarum' => 'TJ144',
                'total_mc' => 30,
                'brand' => 'Lonati 144 OPENTOE',
                'mesin_jalan' => 29,
            ],
            [
                'area' => 'KK1A',
                'jarum' => 'JC144',
                'total_mc' => 17,
                'brand' => 'Lonati 144 AUTOLINK',
                'mesin_jalan' => 13,
            ],
            [
                'area' => 'KK1A',
                'jarum' => 'TJ144',
                'total_mc' => 67,
                'brand' => 'Lonati 144 AUTOLINK',
                'mesin_jalan' => 57,
            ],
            [
                'area' => 'KK1A',
                'jarum' => 'JC168',
                'total_mc' => 154,
                'brand' => 'Lonati 168 AUTOLINK',
                'mesin_jalan' => 112,
            ],
            [
                'area' => 'KK1A',
                'jarum' => 'TJ168',
                'total_mc' => 9,
                'brand' => 'Lonati 168 AUTOLINK',
                'mesin_jalan' => 5,
            ],
            [
                'area' => 'KK1B',
                'jarum' => 'JC120',
                'total_mc' => 152,
                'brand' => 'ROSSO',
                'mesin_jalan' => 146,
            ],
            [
                'area' => 'KK1B',
                'jarum' => 'TJ120',
                'total_mc' => 0,
                'brand' => 'ROSSO',
                'mesin_jalan' => 0,
            ],
            [
                'area' => 'KK1B',
                'jarum' => 'JC144',
                'total_mc' => 172,
                'brand' => 'ROSSO',
                'mesin_jalan' => 162,
            ],
            [
                'area' => 'KK1B',
                'jarum' => 'TJ144',
                'total_mc' => 0,
                'brand' => 'ROSSO',
                'mesin_jalan' => 0,
            ],
            [
                'area' => 'KK1B',
                'jarum' => 'JC168',
                'total_mc' => 0,
                'brand' => 'ROSSO',
                'mesin_jalan' => 0,
            ],
        ];
        $this->db->table('data_mesin')->insertBatch($data);
    }
}
