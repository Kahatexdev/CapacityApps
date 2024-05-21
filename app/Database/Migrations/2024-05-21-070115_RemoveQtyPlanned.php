<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveQtyPlanned extends Migration
{
    public function up()
    {
        $this->forge->dropColumn('detail_planning', 'qty_planned');
    }

    public function down()
    {
        //
    }
}
