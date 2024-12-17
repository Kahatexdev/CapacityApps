<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddInisial extends Migration
{
    public function up()
    {
        $this->forge->addColumn('apsperstyle', [
            'inisial' => [
                'type' => 'varchar',
                'constraint' => 12,
                // Other column options
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('apsperstyle', 'inisial');
    }
}
