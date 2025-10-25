<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKolomMesinPerinisial extends Migration
{
    public function up()
    {
        $fields = [
            'material_status' => [
                'type'       => 'ENUM',
                'constraint' => ['complete', 'not ready'],
                'default'    => 'not ready',
            ],

            'priority' => [
                'type'       => 'ENUM',
                'constraint' => ['low', 'normal', 'high'],
                'default'    => 'normal',
            ],
            'start_pps_plan' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'stop_pps_plan' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'admin' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
        ];
        $this->forge->addColumn('mesin_perinisial', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('mesin_perinisial', 'material_status');
        $this->forge->dropColumn('mesin_perinisial',   'priority');
        $this->forge->dropColumn('mesin_perinisial', 'start_pps_plan');
        $this->forge->dropColumn('mesin_perinisial',  'stop_pps_plan');
        $this->forge->dropColumn('mesin_perinisial',  'admin');
    }
}
