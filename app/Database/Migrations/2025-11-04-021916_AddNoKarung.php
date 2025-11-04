<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNoKarung extends Migration
{
    public function up()
    {
        $this->forge->addColumn('stock_area', [
            'no_karung' => [
                'type' => 'int',
                'constraint' => 12,
                'null' => true,
                'after' => 'id_pengeluaran'
            ],
            'area' => [
                'type' => 'varchar',
                'constraint' => 14,
                'null' => true,
                'after' => 'no_karung'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('stock_area', 'no_karung');
        $this->forge->dropColumn('stock_area', 'area');
    }
}
