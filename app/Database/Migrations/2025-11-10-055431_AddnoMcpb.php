<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddnoMcpb extends Migration
{
    public function up()
    {
        $this->forge->addColumn('perbaikan_area', [
            'no_mc' => [
                'type' => 'varchar',
                'constraint' => 100,
                'null' => true,
                'after' => 'no_box'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('perbaikan_area', 'no_mc');
    }
}
