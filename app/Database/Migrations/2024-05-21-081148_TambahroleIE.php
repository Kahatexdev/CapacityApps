<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TambahroleIE extends Migration
{
    public function up()
    {

        $this->forge->modifyColumn('user', [
            'role' => [
                'type' => 'ENUM',
                'constraint' => ['capacity', 'planning', 'aps', 'user', 'god', 'ie'],
                'default' => 'user',
            ],
        ]);
    }

    public function down()
    {
        //
    }
}
