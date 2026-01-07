<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKeteranganSpk2 extends Migration
{
    public function up()
    {
        $this->forge->addColumn('estimasi_spk', [
            'keterangan' => [
                'type' => 'varchar',
                'constraint' => 100,
                // Other column options
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('estimasi_spk', 'keterangan');
    }
}
