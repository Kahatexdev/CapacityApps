<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TambahDelivProduksi extends Migration
{
    public function up()
    {
        $this->forge->addColumn('produksi', [
            'delivery' => [
                'type' => 'date',
                'null' => true,
                'after' => 'no_mesin',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('produksi', 'delivery');
    }
}
