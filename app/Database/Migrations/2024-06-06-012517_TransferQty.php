<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TransferQty extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_transfer' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'from_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
            ],
            'qty_transfer' => [
                'type'           => 'INT',
                'constraint'     => 11,
            ],
            'to_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
            ],
            'created_at' => [
                'type'           => 'DATETIME',
                'null'           => true,
            ],
            'updated_at' => [
                'type'           => 'DATETIME',
                'null'           => true,
            ],
        ]);

        // Define the primary key
        $this->forge->addKey('id_transfer', true);

        // Create the table
        $this->forge->createTable('transfers');
    }

    public function down()
    {
        // Drop the table if exists
        $this->forge->dropTable('transfers');
    }
}
