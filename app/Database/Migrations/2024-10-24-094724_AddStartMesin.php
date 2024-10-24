<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStartMesin extends Migration
{
    public function up()
    {
        $this->forge->addColumn('estimated_planning', [
            'start_mesin' => [
                'type' => 'date',

            ],
            'stop_mesin' => [
                'type' => 'date',

            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('estimated_planning', ['start_mesin,stop_mesin']);
    }
}
