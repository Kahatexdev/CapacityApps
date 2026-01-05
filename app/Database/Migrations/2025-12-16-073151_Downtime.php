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
            'jarum' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            'no_mc' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'total_time' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
                'comment'    => 'Changeover time in minutes',
            ],
            'loading_time' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
                'comment'    => 'Changeover time in minutes',
            ],
            'operating_time' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
                'comment'    => 'Changeover time in minutes',
            ],
            'breakdown' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'keterangan' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'oee' => [
                'type'       => 'int',
                'constraint' => 11,
                'default'    => 0,
                'comment'    => 'OEE',
            ],
            'quality' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'performance' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'availability' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,

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
        $this->forge->createTable('downtime', true);
    }

    public function down()
    {
        $this->forge->dropTable('downtime', true);
    }
}
