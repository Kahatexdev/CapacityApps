<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TargetExport extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'month' => [
                'type' => 'VARCHAR',
                'constraint' => '25',
            ],
            'qty_target' => [
                'type' => 'DOUBLE',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'update_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('target_export');
    }

    public function down()
    {
        $this->forge->dropTable('target_export');
    }
}
