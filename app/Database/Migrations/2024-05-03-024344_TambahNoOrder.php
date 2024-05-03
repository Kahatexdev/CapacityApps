<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TambahNoOrder extends Migration
{
    public function up()
    {
        $this->forge->addColumn('apsperstyle', [
            'no_order' => [
                'type' => 'VARCHAR',
                'constraint' => 35,
                'null' => true,
                'after' => 'mastermodel',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('apsperstyle', 'no_order');
    }
}
