<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStatusDetailPlannign extends Migration
{
    public function up()
    {
        $this->forge->addColumn('detail_planning', [
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 10, // Panjang maksimal string
                'default' => 'aktif',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('detail_planning', 'status');
    }
}
