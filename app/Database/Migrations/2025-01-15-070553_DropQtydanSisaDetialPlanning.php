<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DropQtydanSisaDetialPlanning extends Migration
{
    public function up()
    {
        $this->forge->dropColumn('detail_planning', 'qty,sisa');
    }

    public function down()
    {
        $this->forge->addColumn('detail_planning', [
            'qty' => [
                'type' => 'int',
                'constraint' => 10,
                // Other column options
            ],
            [
                'sisa' => [
                    'type' => 'int',
                    'constraint' => 10,
                    // Other column options
                ]
            ]
        ]);
    }
}
