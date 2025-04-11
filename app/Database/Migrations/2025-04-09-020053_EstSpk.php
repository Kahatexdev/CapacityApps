<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EstSpk extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'         => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'model'      => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
            ],
            'style'      => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
            ],
            'area'      => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
            ],
            'qty'        => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'status'     => [
                'type'       => 'ENUM',
                'constraint' => ['belum', 'sudah'],
                'default'    => 'belum',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('estimasi_spk');
    }

    public function down()
    {
        $this->forge->dropTable('estimasi_spk');
    }
}
