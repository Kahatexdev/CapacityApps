<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRoleFuTabelUser extends Migration
{
    public function up()
    {

        $this->forge->modifyColumn('user', [
            'role' => [
                'type' => 'ENUM',
                'constraint' => ['capacity', 'planning', 'aps', 'user', 'god', 'sudo', 'ie', 'rosso', 'followup'],
                'default' => 'user',
            ],
        ]);
    }

    public function down() {}
}
