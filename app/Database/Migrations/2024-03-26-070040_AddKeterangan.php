<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKeterangan extends Migration
{
    public function up()
    {
        $this->forge->addColumn('master_product_type', [
            'keterangan' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                // Other column options
            ],
        ]);
    }

    public function down()
    {
        //
    }
}
