<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DataModel extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id_booking' => 259,
                'no_model' => 'SI2600',
                'kd_buyer_order' => 'H&M',
                'id_product_type' => 1,
                'seam' => 'ROSSO NORMAL SOCKS',
                'leadtime' => 28,
                'description' => 'SOCKS BOYS KNITTED 5P COSMO CREW BASIC PK 1 WHITES',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'id_booking' => 259,
                'no_model' => 'SI2609',
                'kd_buyer_order' => 'H&M',
                'id_product_type' => 1,
                'seam' => 'ROSSO NORMAL SOCKS',
                'leadtime' => 28,
                'description' => 'SOCKS BOYS KNITTED 5P COSMO CREW BASIC PK 1 WHITES',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'id_booking' => 259,
                'no_model' => 'ER1025',
                'kd_buyer_order' => 'AUCHAN',
                'id_product_type' => 1,
                'seam' => 'ROSSO NORMAL SOCKS',
                'leadtime' => 28,
                'description' => '1X MENS ORG PLAIN NORMAL SOCKS',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'id_booking' => 259,
                'no_model' => 'RZ2407',
                'kd_buyer_order' => 'ADIDAS',
                'id_product_type' => 3,
                'seam' => 'AUTOLINK WITH TUMBLING',
                'leadtime' => 28,
                'description' => '3X HALF TERRY STRIPES CREW SOCKS',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'id_booking' => 259,
                'no_model' => 'RZ2416',
                'kd_buyer_order' => 'ADIDAS',
                'id_product_type' => 6,
                'seam' => 'AUTOLINK WITH TUMBLING',
                'leadtime' => 28,
                'description' => '3X LINEAR LOW CUT SOCKS',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];
        $this->db->table('data_model')->insertBatch($data);
    }
}
