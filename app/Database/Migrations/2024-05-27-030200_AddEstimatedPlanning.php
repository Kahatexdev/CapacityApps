<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEstimatedPlanning extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_est_qty' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'id_detail_pln' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'Est_qty' => [
                'type' => 'INT',
                'constraint' => 25,
            ],
            'hari' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'target' => [
                'type' => 'Float',
                'constraint' => 11,
            ],
            'precentage_target' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
        ]);
        $this->forge->addKey('id_est_qty', true);
        $this->forge->createTable('estimated_planning');
    }

    public function down()
    {
        $this->forge->dropTable('estimated_planning');
    }
}
