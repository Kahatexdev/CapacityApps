<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DataOrder extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_order' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'id_booking' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'tgl_terima_order' => [
                'type' => 'DATE',
            ],
            'kd_buyer_order' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'id_product_type' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'qty_order' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'sisa_order' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'description' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],

        ]);
        $this->forge->addKey('id_order', true);
        // Tambahkan kunci asing ke sisa tabel referensi di sini
        $this->forge->addForeignKey('id_booking', 'data_booking', 'id_booking', 'CASCADE', 'CASCADE');
        $this->forge->createTable('data_order');
    }

    public function down()
    {
        $this->forge->dropTable('data_order');
    }
}
