<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MesinPerInisial extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_mesin_perinisial' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'id_est_qty' => [
                'type' => 'int',
                'constraint' => '10', // Menambahkan presisi dan skala untuk tipe data DOUBLE
            ],
            'idapsperstyle' => [
                'type' => 'int',
                'constraint' => 10,
            ],
            'mesin' => [
                'type' => 'int',
                'constraint' => 10,
            ],
            'keterangan' => [
                'type' => 'varchar',
                'constraint' => 64,
            ],
        ]);
        $this->forge->addKey('id_mesin_perinisial', true);
        $this->forge->createTable('mesin_perinisial');
    }

    public function down()
    {
        $this->forge->dropTable('mesin_perinisial');
    }
}
