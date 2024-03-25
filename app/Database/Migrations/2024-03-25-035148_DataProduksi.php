<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DataProduksi extends Migration
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
                ]
            ]
        );
        $this->forge->addKey('id_produksi', true);
        $this->forge->addForeignKey('idapsperstyle', 'apsperstyle', 'idapsperstyle', 'CASCADE', 'CASCADE');
        $this->forge->createTable('data_produksi');
    }

    public function down()
    {
        $this->forge->dropTable('data_produksi');
    }
}
