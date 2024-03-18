<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MasterProduct extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_product_type' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'konversi' => [
                'type' => 'DOUBLE',
                'constraint' => '10,2', // Menambahkan presisi dan skala untuk tipe data DOUBLE
            ],
            'product_type' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
            ],
        ]);
        $this->forge->addKey('id_product_type', true);
        $this->forge->createTable('master_product_type');
    }

    public function down()
    {
        $this->forge->dropTable('master_product_type');
    }
}
