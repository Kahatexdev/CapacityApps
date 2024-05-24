<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddContryinAps extends Migration
{
    public function up()
    {
        $this->forge->addColumn('apsperstyle', [
            'country' => [
                'type' => 'varchar',
                'constraint' => 12,
                'null' => true,
                'after' => 'size'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('apsperstyle', 'country');
    }
}
