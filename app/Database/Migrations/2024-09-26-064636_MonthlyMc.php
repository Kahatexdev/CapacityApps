<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MonthlyMc extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_monthly_mc' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'auto_increment' => true,
            ],
            'judul' => [
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
            'total_output' => [
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
        $this->forge->addKey('id_monthly_mc', true);
        $this->forge->createTable('monthly_mc');
    }

    public function down()
    {
        $this->forge->dropTable('monthly_mc');
    }
}
