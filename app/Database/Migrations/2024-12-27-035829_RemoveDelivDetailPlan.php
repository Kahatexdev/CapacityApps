<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveDelivDetailPlan extends Migration
{
    public function up()
    {
        $this->forge->dropColumn('detail_planning', 'delviery');

        $this->forge->addColumn('detail_planning', [
            'jarum' => [
                'type' => 'varchar',
                'constraint' => 10,
                // Other column options
            ]
        ]);
    }

    public function down()
    {

        $this->forge->addColumn('detail_planning', [
            'delivery' => [
                'type' => 'date',
                // Other column options
            ]
        ]);
        $this->forge->dropColumn('detail_planning', 'jarum');
    }
}
