<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRoleTabelUser extends Migration
{
    public function up()
    {

        $this->forge->modifyColumn('user', [
            'role' => [
                'type' => 'ENUM',
                'constraint' => ['capacity', 'planning', 'aps', 'user', 'god', 'sudo', 'ie', 'rosso'],
                'default' => 'user',
            ],
        ]);
    }

    public function down() {}
}
