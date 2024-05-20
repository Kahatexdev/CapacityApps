<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AreaAkses extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'area_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
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
        $this->forge->addKey(['user_id', 'area_id'], true);
        $this->forge->addForeignKey('user_id', 'user', 'id_user', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('area_id', 'areas', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('user_areas');
    }

    public function down()
    {
        $this->forge->dropTable('user_areas');
    }
}
