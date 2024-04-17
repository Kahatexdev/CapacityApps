<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TambahDeskripsiKebMesin extends Migration
{
    public function up()
    {
        $fields = [
            'deskripsi' => [
                'type' => 'TEXT',
                'null' => true, // Sesuaikan apakah kolom bisa null atau tidak
            ],
        ];
        $this->forge->addColumn('kebutuhan_mesin', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('kebutuhan_mesin', 'deskripsi');
    }
}
