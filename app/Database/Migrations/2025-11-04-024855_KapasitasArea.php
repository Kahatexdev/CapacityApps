<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class KapasitasArea extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_supermarket' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'area' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
            ],
            'kapasitas' => [
                'type' => 'int',
            ],
        ]);

        // Primary key
        $this->forge->addKey('id_supermarket', true);

        // Create table
        $this->forge->createTable('supermarket_area');
    }

    public function down()
    {
        $this->forge->dropTable('supermarket_area');
    }
}
