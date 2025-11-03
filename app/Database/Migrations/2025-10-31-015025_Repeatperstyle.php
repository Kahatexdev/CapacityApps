<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Repeatperstyle extends Migration
{
    public function up()
    {
        $fields = [
            'repeat_from' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
        ];
        $this->forge->addColumn('mesin_perinisial', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('mesin_perinisial', 'repeat_from');
    }
}
