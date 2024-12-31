<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveDelivDetailPlan extends Migration
{
    public function up()
    {
        $this->forge->dropColumn('detail_planning', 'delivery');
    }

    public function down()
    {
        $this->forge->addColumn('detail_planning', [
            'delivery' => [
                'type' => 'date',

            ],
        ]);
    }
}
