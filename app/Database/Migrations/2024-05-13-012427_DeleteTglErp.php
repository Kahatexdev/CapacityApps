<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DeleteTglErp extends Migration
{
    public function up()
    {
        $this->forge->dropColumn('produksi', 'tgl_erp');
    }

    public function down()
    {
        //
    }
}
