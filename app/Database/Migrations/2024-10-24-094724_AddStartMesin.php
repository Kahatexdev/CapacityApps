<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStartMesin extends Migration
{
    public function up()
    {
        $this->forge->addColumn('tanggal_planning', [
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
        $this->forge->dropColumn('tanggal_planning', 'start_mesin');
        $this->forge->dropColumn('tanggal_planning', 'stop_mesin');
    }
}
