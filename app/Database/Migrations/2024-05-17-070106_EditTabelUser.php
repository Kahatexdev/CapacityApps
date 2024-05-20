<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EditTabelUser extends Migration
{
    public function up()
    {

        $this->forge->addColumn('user', [
            'role' => [
                'type' => 'ENUM',
                'constraint' => ['capacity', 'planning', 'aps', 'user', 'god'],
                'default' => 'user',
            ],
        ]);
    }

    public function down()
    {
    }
}
