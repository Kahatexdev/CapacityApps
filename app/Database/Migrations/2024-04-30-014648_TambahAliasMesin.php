<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TambahAliasMesin extends Migration
{
    public function up()
    {
        $this->forge->addColumn('data_mesin', [
            'aliasjarum' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'pu' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'Default' => 'CJ',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('data_mesin', 'aliasjarum');
    }
}
