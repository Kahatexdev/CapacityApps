<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEstQtyTglPlanning extends Migration
{
    public function up()
    {
        $this->forge->addColumn('tanggal_planning', [
            'id_est_qty' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'after' => 'id'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('tanggal_planning', 'id_est_qty');
    }
}
