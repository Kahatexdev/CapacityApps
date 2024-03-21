<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class APSOrderReport extends Migration
{
    public function up()
    { $this->forge->addField([
        'recordid' => [
            'type' => 'INT',
            'constraint' => 11,
            'unsigned' => true,
            'auto_increment' => true,
        ],
        'articleNo' => [
            'type' => 'VARCHAR',
            'constraint' => 40,
        ],
        'size' => [
            'type' => 'VARCHAR',
            'constraint' => 35,
        ],
        'qty' => [
            'type' => 'int',
            'constraint' => 50,
        ],
        'color' => [
            'type' => 'VARCHAR',
            'constraint' => 150,
        ],
        'delivery' => [
            'type' => 'date',
        ],
        'country' => [
            'type' => 'varchar',
            'constraint' => 100,
        ],
        'smv' => [
            'type' => 'int',
            'constraint' => 10,
        ],
        'machinetypeid' => [
            'type' => 'Varchar',
            'constraint' => 10,
        ],
        'processroute' => [
            'type' => 'VARCHAR',
            'constraint' => 100,
        ],  
        'no_model' => [
            'type' => 'VARCHAR',
            'constraint' => 30,
        ],        
        'lcoDate' => [
            'type' => 'date',
        ],
    ]);
    $this->forge->addKey('recordid', true);
    // Tambahkan kunci asing ke sisa tabel referensi di sini
    // $this->forge->addForeignKey('id_booking', 'data_booking', 'id_booking', 'CASCADE', 'CASCADE');
    // $this->forge->addForeignKey('id_product_type', 'master_product_type', 'id_product_type');
    $this->forge->createTable('aps_order_report');
}

    public function down()
    {
        //
    }
}
