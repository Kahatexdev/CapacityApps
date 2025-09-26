<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MesinPernomor extends Migration
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
            'id_mesin' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ],
            'id_detail_plan' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ],
            'idapsperstyle' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ],
            'start_mesin' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'stop_mesin' => [
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
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('mesin_pernomor');
    }

    public function down()
    {
        $this->forge->dropTable('mesin_pernomor');
    }
}
