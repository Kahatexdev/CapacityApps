<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddProcessRoutes extends Migration
{
    public function up()
    {
        $fields = [
            'process_routes' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after'=>'seam'
            ],
        ];
        $this->forge->addColumn('apsperstyle', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('apsperstyle', 'process_routes');
    }
}
