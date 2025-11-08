<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTanggalInputPlan extends Migration
{
    public function up()
    {
        $this->forge->addColumn('detail_planning', [
            'create_plan' => [
                'type' => 'DATETIME',
            ],
            'updated_at' => [
                'type' => 'DATETIME',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('detail_planning', 'create_plan');
        $this->forge->dropColumn('detail_planning', 'updated_at');
    }
}
