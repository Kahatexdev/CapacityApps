<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class BsMesin extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_bsmc' => [
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
            'shift' => [
                'type' => 'VARCHAR',
                'constraint' => '2',
            ],
            'area' => [
                'type' => 'VARCHAR',
                'constraint' => '6',
            ],
            'no_model' => [
                'type' => 'VARCHAR',
                'constraint' => '8',
            ],
            'size' => [
                'type' => 'VARCHAR',
                'constraint' => '22',
            ],
            'inisial' => [
                'type' => 'VARCHAR',
                'constraint' => '22',
            ],
            'no_mesin' => [
                'type' => 'int',
                'constraint' => '8',
            ],
            'qty_pcs' => [
                'type' => 'DOUBLE',
            ],
            'qty_gram' => [
                'type' => 'DOUBLE',
            ],
            'tanggal_produksi' => [
                'type' => 'DATE',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'update_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addPrimaryKey('id_bsmc');
        $this->forge->createTable('bs_mesin');
    }

    public function down()
    {
        $this->forge->dropTable('bs_mesin');
    }
}
