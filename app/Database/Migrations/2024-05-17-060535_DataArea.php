<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DataArea extends Migration
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
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
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
        $this->forge->createTable('areas');
        $seeder = \Config\Database::seeder();
        $seeder->call('AreaSeeder');
    }

    public function down()
    {
        $this->forge->dropTable('areas');
    }
}
