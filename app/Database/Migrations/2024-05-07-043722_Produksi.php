<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Produksi extends Migration
{
    public function up()
    {
        $this->forge->addField(
            [
                'id_produksi' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'auto_increment' => true,
                ],
                'idapsperstyle' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                ],
                'tgl_produksi' => [
                    'type' => 'Date',
                ],
                'qty_produksi' => [
                    'type' => 'INT',
                    'constraint' => 11
                ],
                'tgl_erp' => [
                    'type' => 'DATE',
                ],
                'bagian' => [
                    'type' => 'VARCHAR',
                    'constraint' => 25,
                ],
                'storage_awal' => [
                    'type' => 'VARCHAR',
                    'constraint' => 25,
                ],
                'storage_akhir' => [
                    'type' => 'VARCHAR',
                    'constraint' => 25,
                ],
                'qty_prod' => [
                    'type' => 'DOUBLE',
                ],
                'bs_prod' => [
                    'type' => 'DOUBLE',
                ],
                'kategori_bs' => [
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                ],
                'no_box' => [
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                ],
                'no_label' => [
                    'type' => 'INT',
                ],
                'no_mesin' => [
                    'type' => 'INT',
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                ],
                'updated_at' => [
                    'type' => 'DATETIME',
                ],
                'admin' => [
                    'type' => 'VARCHAR',
                    'constraint' => 20,
                ],
                'kode_shipment' => [
                    'type' => 'INT',
                    'unsigned' => true,
                ]
            ]
        );

        $this->forge->addKey('id_produksi', true);
        $this->forge->addForeignKey('idapsperstyle', 'apsperstyle', 'idapsperstyle', 'CASCADE', 'CASCADE');
        $this->forge->createTable('produksi');
    }

    public function down()
    {
        //
    }
}
