<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ColorColoumninAps extends Migration
{
    public function up()
    {
        $this->forge->addColumn('apsperstyle', [
            'color' => [
                'type' => 'varchar',
                'constraint' => 100,
                'null' => true,
                'after' => 'size'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('apsperstyle', 'color');
    }
}
