<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddShiftandBSinProduksi extends Migration
{
    public function up()
    {
        $this->forge->addColumn('produksi', [
            'shift_a' => [
                'type' => 'int',
                'constraint' => 100,
                'default' => 0,
            ],
            'shift_b' => [
                'type' => 'int',
                'constraint' => 100,
                'default' => 0,
            ],
            'shift_c' => [
                'type' => 'int',
                'constraint' => 100,
                'default' => 0,
            ],
            
        ]);
    }

    public function down()
    {
        //
    }
}
