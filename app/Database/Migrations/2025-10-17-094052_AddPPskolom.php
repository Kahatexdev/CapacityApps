<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPPskolom extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_pps' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'auto_increment' => true,
            ],
            'id_mesin_perinisial' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'pps_status' => [
                'type'       => 'ENUM',
                'constraint' => ['planning', 'process', 'hold', 'declined', 'approved', 'perbaikan'],
                'default'    => 'planning',
            ],
            'mechanic' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'notes' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'history' => [
                'type'       => 'TEXT',
                'null'       => true,
            ],
            'coor' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],

            'start_pps_act' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'stop_pps_act' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'acc_qad' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'acc_mr' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'acc_fu' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'admin' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
        ]);

        $this->forge->addKey('id_pps', true);
        $this->forge->createTable('pps');
    }

    public function down()
    {
        $this->forge->dropTable('pps');
    }
}
