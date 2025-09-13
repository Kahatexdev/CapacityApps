<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKolomStartMcDatamodel extends Migration
{
    public function up()
    {
        $this->forge->addColumn('data_model', [
            'start_mc' => [
                'type' => 'date',
                'after' => 'repeat_from'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('data_model', 'start_mc');
    }
}
