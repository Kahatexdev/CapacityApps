<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKeteranganEstPlan extends Migration
{
    public function up()
    {

        $this->forge->addColumn('estimated_planning', [
            'keterangan' => [
                'type' => 'varchar',
                'constraint' => 32, // Panjang maksimal string

                // Other column options
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('estimated_planning', 'keterangan');
    }
}
