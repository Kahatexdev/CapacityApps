<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AreaMachine extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_area_machine' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'auto_increment' => true,
            ],
            'id_monthly_mc' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'area' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'total_mc' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'planning_mc' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'output' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id_area_machine', true);
        $this->forge->addForeignKey('id_monthly_mc', 'monthly_mc', 'id_monthly_mc', 'CASCADE', 'CASCADE');
        $this->forge->createTable('area_machine');
    }

    public function down()
    {
        $this->forge->dropTable('area_machine');
    }
}
