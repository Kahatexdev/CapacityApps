<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddColoumnPlusPacking extends Migration
{
    public function up()
    {
        $this->forge->addColumn('produksi', [
            'plus_packing' => [
                'type' => 'int',
                'constraint' => 100,
                'null' => true,
                'default' => 0,
                'after' => 'storage_akhir'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('produksi', 'plus_packing');
    }
}
