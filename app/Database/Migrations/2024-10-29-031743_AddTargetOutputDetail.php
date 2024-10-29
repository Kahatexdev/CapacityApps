<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTargetOutputDetail extends Migration
{
    public function up()
    {
        $this->forge->addColumn('detail_area_machine', [
            'target' => [
                'type' => 'int',
                'constraint' => 12,


            ],
            'output' => [
                'type' => 'int',
                'constraint' => 12,


            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('detail_area_machine', 'target');
        $this->forge->dropColumn('detail_area_machine', 'output');
    }
}
