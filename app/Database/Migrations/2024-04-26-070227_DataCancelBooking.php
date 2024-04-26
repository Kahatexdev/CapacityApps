<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DataCancelBooking extends Migration
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
            'id_booking' => [
                'type'       => 'VARCHAR',
                'constraint' => 50
            ],
            'qty_cancel' => [
                'type'       => 'INT',
                'constraint' => 50
            ],
            'alasan' => [
                'type'       => 'TEXT',
            ]
        ]);
        $this->forge->addKey('id', true); // Setting 'id' as the primary key
        $this->forge->createTable('data_cancel'); // Create the table with the name 'data_cylinder'
    }

    public function down()
    {
        $this->forge->dropTable('data_cancel');
    }
}
