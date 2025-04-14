<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKebInLineDanWly extends Migration
{
    public function up()
    {
        $this->forge->addColumn('area_machine', [
            'inline' => [
                'type' => 'int',
                'constraint' => 12,
                'after'      => 'montir'
            ],
            'wly' => [
                'type' => 'int',
                'constraint' => 12,
                'after'      => 'inline'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('area_machine', 'inline');
        $this->forge->dropColumn('area_machine', 'wly');
    }
}
