<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DataCylinder extends Migration
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
            'needle' => [
                'type'       => 'VARCHAR',
                'constraint' => 255
            ],
            'production_unit' => [
                'type'       => 'VARCHAR',
                'constraint' => 255
            ],
            'type_machine' => [
                'type'       => 'VARCHAR',
                'constraint' => 255
            ],
            'qty' => [
                'type'       => 'INT',
                'constraint' => 11
            ],
            'needle_detail' => [
                'type'       => 'VARCHAR',
                'constraint' => 255
            ]
        ]);
        $this->forge->addKey('id', true); // Setting 'id' as the primary key
        $this->forge->createTable('data_cylinder'); // Create the table with the name 'data_cylinder'
    }

    public function down()
    {
        $this->forge->dropTable('data_booking');
    }
}
