<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Apsperstyle extends Migration
{
    public function up()
    { $this->forge->addField([
        'idapsperstyle' => [
            'type' => 'INT',
            'constraint' => 11,
            'unsigned' => true,
            'auto_increment' => true,
        ],
        'machinetypeid' => [
            'type' => 'VARCHAR',
            'constraint' => 10,
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
        'qty' => [
            'type' => 'int',
            'constraint' => 50,
        ],
        'sisa' => [
            'type' => 'INT',
            'constraint' => 50,
        ],
        'seam' => [
            'type' => 'VARCHAR',
            'constraint' => 100,
        ],        
        'factory' => [
            'type' => 'varchar',
            'constraint' => 100,
        ],
        'created_at' => [
            'type' => 'Date',
        ],
        'updated_at' => [
            'type' => 'Date',
        ],

    ]);
    $this->forge->addKey('idapsperstyle', true);
    // Tambahkan kunci asing ke sisa tabel referensi di sini
    // $this->forge->addForeignKey('id_booking', 'data_booking', 'id_booking', 'CASCADE', 'CASCADE');
    // $this->forge->addForeignKey('id_product_type', 'master_product_type', 'id_product_type');
    $this->forge->createTable('apsperstyle');
}

    public function down()
    {
        //
    }
}
