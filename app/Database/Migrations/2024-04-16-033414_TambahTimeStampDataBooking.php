<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TambahTimestampsDataBooking extends Migration
{
    public function up()
    {
        $fields = [
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ];
        $this->forge->addColumn('data_booking', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('data_booking', 'created_at');
        $this->forge->dropColumn('data_booking', 'updated_at');
    }
}
