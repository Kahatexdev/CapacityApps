<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DeletQtyProd extends Migration
{
    public function up()
    {
        $this->forge->dropColumn('produksi', 'qty_prod');
    }

    public function down()
    {
        //
    }
}
