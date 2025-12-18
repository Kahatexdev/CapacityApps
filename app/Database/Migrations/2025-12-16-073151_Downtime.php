<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Downtime extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'area' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'tanggal' => [
                'type' => 'DATE',
            ],
            'needle' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            'no_mc' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'available_wh' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
                'comment'    => 'Available working hours in minutes',
            ],
            'downtime' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
                'comment'    => 'Downtime in minutes',
            ],
            'changeover_time' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
                'comment'    => 'Changeover time in minutes',
            ],
            'loading_time' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
                'comment'    => 'Loading time in minutes',
            ],
            'operating_time' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
                'comment'    => 'Operating time in minutes',
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
        $this->forge->addKey(['area', 'tanggal']);
        $this->forge->createTable('downtime', true);
    }

    public function down()
    {
        $this->forge->dropTable('downtime', true);
    }
}
