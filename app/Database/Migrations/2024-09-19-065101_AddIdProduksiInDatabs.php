<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIdProduksiInDatabs extends Migration
{
    public function up()
    {
        $this->forge->addColumn('data_bs', [
            'id_produksi' => [
                'type' => 'int',
                'constraint' => 12,
                // Other column options
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('data_bs', 'id_produksi');
    }
}
