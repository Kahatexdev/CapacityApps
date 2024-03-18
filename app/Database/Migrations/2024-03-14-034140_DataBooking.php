<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DataBooking extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_booking' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'tgl_terima_booking' => [
                'type' => 'DATE',
            ],
            'kd_buyer_booking' => [
                'type' => 'VARCHAR',
                'constraint' => 30,
            ],
            'id_product_type' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'no_order' => [
                'type' => 'VARCHAR',
                'constraint' => 30,
            ],
            'no_booking' => [
                'type' => 'VARCHAR',
                'constraint' => 30,
            ],
            'desc' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'opd' => [
                'type' => 'VARCHAR',
                'constraint' => 30,
            ],
            'delivery' => [
                'type' => 'DATE',
            ],
            'qty_booking' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'sisa_booking' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'needle' => [
                'type' => 'VARCHAR',
                'constraint' => 30,
            ],
            'seam' => [
                'type' => 'VARCHAR',
                'constraint' => 30,
            ],
        ]);
        $this->forge->addKey('id_booking', true);
        //$this->forge->addForeignKey('id_product_type', 'product_types', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('data_booking');
    }

    public function down()
    {
        $this->forge->dropTable('data_booking');
    }
}
