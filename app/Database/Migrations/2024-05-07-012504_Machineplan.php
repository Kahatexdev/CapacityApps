<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Machineplan extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true
            ],
            'area' => [
                'type'       => 'VARCHAR',
                'constraint' => 255
            ],
            'jarum' => [
                'type'       => 'VARCHAR',
                'constraint' => 255
            ],
            'brand' => [
                'type'       => 'VARCHAR',
                'constraint' => 255
            ],
            'mc_nyala' => [
                'type'       => 'INT',
                'constraint' => 11
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 255
            ],
            'id_kebutuhan_mesin' => [
                'type'       => 'INT',
                'constraint' => 11
            ]
        ]);
        $this->forge->addKey('id', true); // Setting 'id' as the primary key
        $this->forge->createTable('mesin_planning'); // Create the table with the name 'data_cylinder'
    }

    public function down()
    {
        $this->forge->dropTable('mesin_planning');
    }
}
