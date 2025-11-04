<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStockArea extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_stock_area' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_pengeluaran' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'null'       => true,
            ],
            'no_model' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
            ],
            'item_type' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'kode_warna' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
            ],
            'warna' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
            ],
            'lot' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
            ],
            'kgs_in_out' => [
                'type' => 'INT',
            ],
            'cns_in_out' => [
                'type' => 'INT',
            ],
            'kg_cns' => [
                'type' => 'INT',
            ],
            'admin' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
            ],
            'created_at' => [
                'type' => 'DATETIME',
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ]
        ]);

        // Primary key
        $this->forge->addKey('id_stock_area', true);

        // Create table
        $this->forge->createTable('stock_area');
    }

    public function down()
    {
        $this->forge->dropTable('stock_area');
    }
}
