<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddtargetPerMesinPerjarum extends Migration
{
    public function up()
    {
        $this->forge->addColumn('data_mesin', [
            'target' => [
                'type' => 'int',
                'constraint' => 12,
                // Other column options
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('data_mesin', 'target');
    }
}
