<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateSMVbyIE extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'style' => [
                'type' => 'varchar',
                'constraint' => 50,
            ],
            'smv_old' => [
                'type' => 'INT',
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
        $this->forge->addKey('id', true);
        $this->forge->createTable('history_smv');
    }

    public function down()
    {
        $this->forge->dropTable('history_smv');
    }
}
