<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKeteranganinDataBooking extends Migration
{
    public function up()
    {
        $fields = [
            'keterangan' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true
            ],
        ];
        $this->forge->addColumn('data_booking', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('data_booking', 'keterangan');
    }
}
