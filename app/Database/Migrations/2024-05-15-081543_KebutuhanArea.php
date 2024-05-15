<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class KebutuhanArea extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_pln_mc' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'judul' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                // 'unsigned' => true, // VARCHAR type cannot have 'unsigned'
            ],
            'jarum' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'area' => [
                'type' => 'VARCHAR',
                'constraint' => 13,
            ],
            'created_at' => [
                'type' => 'DATETIME', // Correcting to DATETIME for better precision
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME', // Correcting to DATETIME for better precision
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id_pln_mc', true);
        $this->forge->createTable('kebutuhan_area');
    }

    public function down()
    {
        $this->forge->dropTable('kebutuhan_area');
    }
}
