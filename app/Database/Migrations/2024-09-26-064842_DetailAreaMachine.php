<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DetailAreaMachine extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_detail_area_machine' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'auto_increment' => true,
            ],
            'id_area_machine' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'jarum' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'planning_mc' => [
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
        $this->forge->addKey('id_detail_area_machine', true);
        $this->forge->addForeignKey('id_area_machine', 'area_machine', 'id_area_machine', 'CASCADE', 'CASCADE');
        $this->forge->createTable('detail_area_machine');
    }

    public function down()
    {
        $this->forge->dropTable('detail_area_machine');
    }
}
