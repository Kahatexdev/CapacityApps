<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPPskolom extends Migration
{
    public function up()
    {
        $fields = [
            'pps' => [
                'type' => 'date',
                'null' => true,
                'after' => 'mesin'
            ],
        ];
        $this->forge->addColumn('mesin_perinisial', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('mesin_perinisial', 'pps');
    }
}
