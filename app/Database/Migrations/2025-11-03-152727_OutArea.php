<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class OutArea extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_out_area' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_stock_area' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'null'       => true,
            ],
            'kg_out' => [
                'type' => 'FLOAT',
            ],
            'cns_out' => [
                'type' => 'FLOAT',
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
        $this->forge->addKey('id_out_area', true);

        // Create table
        $this->forge->createTable('out_area');
    }

    public function down()
    {
        $this->forge->dropTable('out_area');
    }
}
