<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SetIdKaryawanNull extends Migration
{
    public function up()
    {
        $fields = [
            'id_karyawan' => [
                'name' => 'id_karyawan',
                'type' => 'INT',
                'constraint' => 7,
                'null' => true, // ubah jadi nullable
            ],
        ];

        $this->forge->modifyColumn('bs_mesin', $fields);
    }

    public function down()
    {
        $fields = [
            'id_karyawan' => [
                'name' => 'id_karyawan',
                'type' => 'INT',
                'constraint' => 7,
                'null' => false, // balik lagi jadi NOT NULL
            ],
        ];

        $this->forge->modifyColumn('bs_mesin', $fields);
    }
}
