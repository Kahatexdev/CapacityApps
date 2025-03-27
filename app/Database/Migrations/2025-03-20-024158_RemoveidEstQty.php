<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveidEstQty extends Migration
{
    public function up()
    {
        $this->forge->dropColumn('mesin_perinisial', 'id_est_qty');
    }

    public function down()
    {
        $this->forge->addColumn('mesin_perinisial', [
            'id_est_qty' => [
                'type' => 'int',
                'constraint' => 10, // Panjang maksimal string

                // Other column options
            ]
        ]);
    }
}
