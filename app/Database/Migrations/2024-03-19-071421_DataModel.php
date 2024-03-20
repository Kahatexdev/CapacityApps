<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DataModel extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_model' => [
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
            'no_model' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
            ],
            'kd_buyer_order' => [
                'type' => 'VARCHAR',
                'constraint' => 15,
            ],
            'id_product_type' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'seam' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'leadtime' => [
                'type' => 'INT',
                'constraint' => 10,
            ],
            'description' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'created_at' => [
                'type' => 'Date',
            ],
            'updated_at' => [
                'type' => 'Date',
            ],

        ]);
        $this->forge->addKey('id_model', true);
        // Tambahkan kunci asing ke sisa tabel referensi di sini
        // $this->forge->addForeignKey('id_booking', 'data_booking', 'id_booking', 'CASCADE', 'CASCADE');
        // $this->forge->addForeignKey('id_product_type', 'master_product_type', 'id_product_type');
        $this->forge->createTable('data_model');
    }

    public function down()
    {
        $this->forge->dropTable('data_model');
    }
}
