<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TambahSMVTabelDetailPlanning extends Migration
{
    public function up()
    {
        $this->forge->addColumn('detail_planning', [
            'smv' => [
                'type' => 'INT',
                'constraint' => 12,
                'null' => true,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('detail_planning', 'smv');
    }
}
