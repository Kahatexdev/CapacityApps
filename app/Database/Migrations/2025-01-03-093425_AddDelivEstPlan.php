<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDelivEstPlan extends Migration
{
    public function up()
    {

        $this->forge->addColumn('estimated_planning', [
            'delivery' => [
                'type' => 'date',
                // Other column options
            ]
        ]);
    }

    public function down()
    {

        $this->forge->dropColumn('estimated_planning', 'delivery');
    }   
}
