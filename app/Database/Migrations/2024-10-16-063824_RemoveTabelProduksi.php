<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveTabelProduksi extends Migration
{
    public function up()
    {
        $this->forge->dropColumn('produksi', ['plus_packing', 'bs_prod', 'kategori_bs']);
        $this->forge->dropColumn('data_bs', ['id_produksi']);
        $this->forge->addColumn('apsperstyle', [
            'po_plus' => [
                'type' => 'int',
                'constraint' => 12,
                'null' => false,
                'default' => 0,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->addColumn('produksi', [
            'plus_packing' => [
                'type' => 'int',
                'constraint' => 100,
                'null' => true,
                'default' => 0,
                'after' => 'storage_akhir'
            ],
            'bs_prod' => [
                'type' => 'DOUBLE',
            ],
            'kategori_bs' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
        ]);
        $this->forge->addColumn('data_bs', [
            'id_produksi' => [
                'type' => 'int',
                'constraint' => 12,
            ],
        ]);
        $this->forge->dropColumn('apsperstyle', 'po_plus');
    }
}
