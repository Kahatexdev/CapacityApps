<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SummaryJarum extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_sj' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'id_karyawan' => [
                'type' => 'int',
                'constraint' => '7',
            ],
            'nama_karyawan' => [
                'type' => 'VARCHAR',
                'constraint' => '25',
            ],
            'qty_jarum' => [
                'type' => 'DOUBLE',
            ],
            'tanggal' => [
                'type' => 'DATE',
            ],
            'area' => [
                'type' => 'VARCHAR',
                'constraint' => '12',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addPrimaryKey('id_sj');
        $this->forge->createTable('penggunaan_jarum');
    }

    public function down()
    {
        $this->forge->dropTable('penggunaan_jarum');
    }
}
