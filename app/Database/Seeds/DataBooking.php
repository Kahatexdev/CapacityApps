<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DataBooking extends Seeder
{
    // public function run()
    // {

    //     $data = [
    //         [
    //             'tgl_terima_booking' => '2024-03-25',
    //             'kd_buyer_booking' => 'H&M S76',
    //             'id_product_type' => 1,
    //             'no_order' => '80000-3276',
    //             'no_booking' => '36O',
    //             'desc' => '5p Harper Normal Sock',
    //             'opd' => '2024-04-20',
    //             'delivery' => '2024-08-20',
    //             'qty_booking' => 6449784,
    //             'sisa_booking' => 6449784,                
    //             'lead_time' => 40,
    //             'needle' => 'JC144',
    //             'seam' => 'ROSSO NORMAL SOCK',
    //             'status' => 'Booking Baru',
    //             'ref_id' => 0               
    //         ],
    //         [
    //             'tgl_terima_booking' => '2024-08-18',
    //             'kd_buyer_booking' => 'H&M S76',
    //             'id_product_type' => 1,
    //             'no_order' => '7P MARIE PACK 1 UNICORN KG SIZE OL I42',
    //             'no_booking' => '36O',
    //             'desc' => '7P MARIE PACK 1 UNICORN KG SIZE OL',
    //             'opd' => '2024-04-20',
    //             'delivery' => '2024-08-20',
    //             'qty_booking' => 246378,
    //             'sisa_booking' => 246378,                
    //             'lead_time' => 28,
    //             'needle' => 'JC144',
    //             'seam' => 'ROSSO NORMAL SOCK',
    //             'status' => 'Booking Baru',
    //             'ref_id' => 0               
    //         ],
    //         [
    //             'tgl_terima_booking' => '2024-03-19',
    //             'kd_buyer_booking' => 'H&M S76',
    //             'id_product_type' => 1,
    //             'no_order' => '7P BASIC SHAFTLESS PINK (PD) SS',
    //             'no_booking' => '14P                ',
    //             'desc' => '7P BASIC SHAFTLESS PINK (PD) SS I43',
    //             'opd' => '2024-07-24',
    //             'delivery' => '2024-08-02',
    //             'qty_booking' => 281574,
    //             'sisa_booking' => 281574,                
    //             'lead_time' => 28,
    //             'needle' => 'JC144',
    //             'seam' => 'ROSSO NORMAL SOCK',
    //             'status' => 'Booking Baru',
    //             'ref_id' => 0               
    //         ],
    //         [
    //             'tgl_terima_booking' => '2024-03-10',
    //             'kd_buyer_booking' => 'H&M S76',
    //             'id_product_type' => 1,
    //             'no_order' => '7P PATTERN SHAFTLESS NATURAL RIB (PD) SS',
    //             'no_booking' => '18D',
    //             'desc' => '7P PATTERN SHAFTLESS NATURAL RIB (PD) SS I43',
    //             'opd' => '2024-07-24',
    //             'delivery' => '2024-08-02',
    //             'qty_booking' => 800000,
    //             'sisa_booking' => 800000,                
    //             'lead_time' => 28,
    //             'needle' => 'JC144',
    //             'seam' => 'ROSSO NORMAL SOCK',
    //             'status' => 'Booking Baru',
    //             'ref_id' => 0               
    //         ],
    //         [
    //             'tgl_terima_booking' => '2024-03-20',
    //             'kd_buyer_booking' => 'H&M S76',
    //             'id_product_type' => 1,
    //             'no_order' => '5P SCALLOP BLACK RCY POLYESTER (PD) SS',
    //             'no_booking' => '4Q',
    //             'desc' => '5P SCALLOP BLACK RCY POLYESTER (PD) SS I51',
    //             'opd' => '2024-07-24',
    //             'delivery' => '2024-08-02',
    //             'qty_booking' => 800000,
    //             'sisa_booking' => 800000,                
    //             'lead_time' => 28,
    //             'needle' => 'JC144',
    //             'seam' => 'ROSSO NORMAL SOCK',
    //             'status' => 'Booking Baru',
    //             'ref_id' => 0               
    //         ],
    //         [
    //             'tgl_terima_booking' => '2024-03-25',
    //             'kd_buyer_booking' => 'H&M S76',
    //             'id_product_type' => 1,
    //             'no_order' => '80000-3276',
    //             'no_booking' => '36O',
    //             'desc' => '5p Harper Normal Sock',
    //             'opd' => '2024-04-20',
    //             'delivery' => '2024-08-20',
    //             'qty_booking' => 6449784,
    //             'sisa_booking' => 6449784,                
    //             'lead_time' => 40,
    //             'needle' => 'JC144',
    //             'seam' => 'ROSSO NORMAL SOCK',
    //             'status' => 'Booking Baru',
    //             'ref_id' => 0               
    //         ],
    //         [
    //             'tgl_terima_booking' => '2024-08-18',
    //             'kd_buyer_booking' => 'H&M S76',
    //             'id_product_type' => 1,
    //             'no_order' => '7P MARIE PACK 1 UNICORN KG SIZE OL I42',
    //             'no_booking' => '36O',
    //             'desc' => '7P MARIE PACK 1 UNICORN KG SIZE OL',
    //             'opd' => '2024-04-20',
    //             'delivery' => '2024-08-20',
    //             'qty_booking' => 246378,
    //             'sisa_booking' => 246378,                
    //             'lead_time' => 28,
    //             'needle' => 'JC120',
    //             'seam' => 'ROSSO NORMAL SOCK',
    //             'status' => 'Booking Baru',
    //             'ref_id' => 0               
    //         ],
    //         [
    //             'tgl_terima_booking' => '2024-03-19',
    //             'kd_buyer_booking' => 'H&M S76',
    //             'id_product_type' => 1,
    //             'no_order' => '7P BASIC SHAFTLESS PINK (PD) SS',
    //             'no_booking' => '14P                ',
    //             'desc' => '7P BASIC SHAFTLESS PINK (PD) SS I43',
    //             'opd' => '2024-07-24',
    //             'delivery' => '2024-08-02',
    //             'qty_booking' => 281574,
    //             'sisa_booking' => 281574,                
    //             'lead_time' => 28,
    //             'needle' => 'JC120',
    //             'seam' => 'ROSSO NORMAL SOCK',
    //             'status' => 'Booking Baru',
    //             'ref_id' => 0               
    //         ],
    //         [
    //             'tgl_terima_booking' => '2024-03-10',
    //             'kd_buyer_booking' => 'H&M S76',
    //             'id_product_type' => 1,
    //             'no_order' => '7P PATTERN SHAFTLESS NATURAL RIB (PD) SS',
    //             'no_booking' => '18D',
    //             'desc' => '7P PATTERN SHAFTLESS NATURAL RIB (PD) SS I43',
    //             'opd' => '2024-07-24',
    //             'delivery' => '2024-08-02',
    //             'qty_booking' => 800000,
    //             'sisa_booking' => 800000,                
    //             'lead_time' => 28,
    //             'needle' => 'JC120',
    //             'seam' => 'ROSSO NORMAL SOCK',
    //             'status' => 'Booking Baru',
    //             'ref_id' => 0               
    //         ],
    //         [
    //             'tgl_terima_booking' => '2024-03-20',
    //             'kd_buyer_booking' => 'H&M S76',
    //             'id_product_type' => 1,
    //             'no_order' => '5P SCALLOP BLACK RCY POLYESTER (PD) SS',
    //             'no_booking' => '4Q',
    //             'desc' => '5P SCALLOP BLACK RCY POLYESTER (PD) SS I51',
    //             'opd' => '2024-07-24',
    //             'delivery' => '2024-08-02',
    //             'qty_booking' => 800000,
    //             'sisa_booking' => 800000,                
    //             'lead_time' => 28,
    //             'needle' => 'JC120',
    //             'seam' => 'ROSSO NORMAL SOCK',
    //             'status' => 'Booking Baru',
    //             'ref_id' => 0               
    //         ],

    //         ];   
    //         $this->db->table('data_booking')->insertBatch($data);        
    // }
}
