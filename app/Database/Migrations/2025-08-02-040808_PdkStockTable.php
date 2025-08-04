<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PdkStockTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_stok_pdk' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'mastermodel' => [
                'type' => 'VARCHAR',
                'constraint' => 35,
            ],
            'size' => [
                'type' => 'VARCHAR',
                'constraint' => 35,
            ],
            'delivery' => [
                'type' => 'date',
            ],
            'qty_asli' => [
                'type' => 'int',
                'constraint' => 50,
            ],
            'stok' => [
                'type' => 'int',
                'constraint' => 50,
            ],
            'qty_akhir' => [
                'type' => 'int',
                'constraint' => 50,
            ],
            'created_at' => [
                'type' => 'DATETIME',
            ],
            'updated_at' => [
                'type' => 'DATETIME',
            ],

        ]);
        $this->forge->addKey('id_stok_pdk', true);
        // Tambahkan kunci asing ke sisa tabel referensi di sini
        // $this->forge->addForeignKey('id_booking', 'data_booking', 'id_booking', 'CASCADE', 'CASCADE');
        // $this->forge->addForeignKey('id_product_type', 'master_product_type', 'id_product_type');
        $this->forge->createTable('stok_pdk');
    }

    public function down()
    {
        $this->forge->dropTable('stok_pdk');
    }
}
