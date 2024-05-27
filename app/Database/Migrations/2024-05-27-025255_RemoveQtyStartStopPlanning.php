<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveQtyStartStopPlanning extends Migration
{
    public function up()
    {
        $this->forge->dropColumn('tanggal_planning', ['hari', 'Est_qty','start_date','stop_date']);
        $this->forge->addColumn('tanggal_planning', [
            'date' => [
                'type' => 'DATE',
                'null' => true,
                'after' => 'id_detail_pln'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->addColumn('tanggal_planning', [
            'hari' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'Est_qty' => [
                'type' => 'INT',
                'null' => true,
            ],
            'start_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'stop_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
        ]);
    }
}
