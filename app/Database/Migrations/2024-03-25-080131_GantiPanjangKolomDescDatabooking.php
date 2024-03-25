<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class GantiPanjangKolomDescDatabooking extends Migration
{
    public function up()
    {
        // Change length of the name column in the users table
        $this->forge->modifyColumn('data_booking', [
            'desc' => ['type' => 'VARCHAR', 'constraint' => 150] // Change 100 to the desired length
        ]);
    }

    public function down()
    {
        //
    }
}
