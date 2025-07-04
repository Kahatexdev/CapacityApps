<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRepeatField extends Migration
{
    public function up()
    {
        $this->forge->addColumn('data_model', [
            'repeat_from' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'description'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('data_model', 'repeat_from');
    }
}
