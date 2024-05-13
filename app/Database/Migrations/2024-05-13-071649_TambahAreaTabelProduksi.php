<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TambahAreaTabelProduksi extends Migration
{
    public function up()
    {
        $this->forge->addColumn('produksi', [
            'area' => [
                'type' => 'VARCHAR',
                'constraint' => 12,
                'null' => true,
                'after' => 'delivery',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('produksi', 'area');
    }
}
