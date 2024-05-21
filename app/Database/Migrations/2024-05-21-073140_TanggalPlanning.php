<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TanggalPlanning extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'id_detail_pln' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'start_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'stop_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'Est_qty' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'hari' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('tanggal_planning');
    }

    public function down()
    {
        $this->forge->dropTable('areas');
    }
}
