<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TambahKebutuhanArea extends Migration
{
    public function up()
    {
        $this->forge->addColumn('detail_planning', [
            'delivery' => [
                'type' => 'date',
                'after' => 'model',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('detail_planning', 'delivery');
    }
}
