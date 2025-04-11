<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKebOperatorDanMontir extends Migration
{
    public function up()
    {
        $this->forge->addColumn('area_machine', [
            'operator' => [
                'type' => 'int',
                'constraint' => 12,
                'after'      => 'output'
            ],
            'montir' => [
                'type' => 'int',
                'constraint' => 12,
                'after'      => 'operator'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('area_machine', 'operator');
        $this->forge->dropColumn('area_machine', 'montir');
    }
}
