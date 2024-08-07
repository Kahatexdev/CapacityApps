<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ColoumnAreaInBS extends Migration
{
    public function up()
    {
        $this->forge->addColumn('data_bs', [
            'area' => [
                'type' => 'varchar',
                'constraint' => 100,
                'null' => true,
                'after' => 'idapsperstyle'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('data_bs', 'area');
    }
}
