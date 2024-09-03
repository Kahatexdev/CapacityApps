<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddColoumnSisainPlanMesin extends Migration
{
    public function up()
    {
        $this->forge->addColumn('kebutuhan_mesin', [
            'sisa' => [
                'type' => 'int',
                'constraint' => 12,
                // Other column options
            ],
        ]);
    }

    public function down()
    {
        //
    }
}
