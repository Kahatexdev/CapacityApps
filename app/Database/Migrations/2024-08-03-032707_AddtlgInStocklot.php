<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddtlgInStocklot extends Migration
{
    public function up()
    {
        $this->forge->addColumn('data_bs', [
            'tgl_instocklot' => [
                'type' => 'date',
                'null' => true,
                'after' => 'idapsperstyle'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('data_bs', 'tgl_instocklot');
    }
}
