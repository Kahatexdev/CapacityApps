<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EditTypeDataProduksi extends Migration
{
    public function up()
    {

        $this->forge->modifyColumn('produksi', [
            'no_mesin' => [
                'type' => 'VARCHAR',
                'constraint' => 15,
                'default' => NULL,
            ],
        ]);
    }
    public function down()
    {
        //
    }
}
