<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TambahFieldShift extends Migration
{
    public function up()
    {
        $this->forge->addColumn('produksi', [
            'shift' => [
                'type' => 'VARCHAR',
                'constraint' => 12,
                'null' => true,
                'after' => 'delivery',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('produksi', 'shift');
    }
}
