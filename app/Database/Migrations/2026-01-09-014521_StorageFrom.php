<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class StorageFrom extends Migration
{
    public function up()
    {
        $this->forge->addColumn('data_bs', [
            'storage_from' => [
                'type' => 'varchar',
                'constraint' => 25,
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('data_bs', 'storage_from');
    }
}
