<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TambahStartAndStopMC extends Migration
{
    public function up()
    {
        $this->forge->addColumn('kebutuhan_mesin', [
            'start_mesin' => [
                'type' => 'date',
                'null' => true,
                'after' => 'tanggal_akhir',
            ],
            'stop_mesin' => [
                'type' => 'date',
                'null' => true,
                'after' => 'start_mesin',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('kebutuhan_mesin', 'start_mesin', 'stop_mesin');
    }
}
