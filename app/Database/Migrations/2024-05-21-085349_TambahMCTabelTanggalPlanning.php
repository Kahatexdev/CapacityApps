<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TambahMCTabelTanggalPlanning extends Migration
{
    public function up()
    {
        $this->forge->addColumn('tanggal_planning', [
            'mesin' => [
                'type' => 'INT',
                'constraint' => 12,
                'null' => true,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('tanggal_planning', 'mesin');
    }
}
