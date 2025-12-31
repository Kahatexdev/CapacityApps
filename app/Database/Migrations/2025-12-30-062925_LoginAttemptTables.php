<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class LoginAttemptTables extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],

            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],

            'ip_address' => [
                'type'       => 'VARCHAR',
                'constraint' => 45,
            ],

            'failed_attempt' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],

            'locked_until' => [
                'type' => 'DATETIME',
                'null' => true,
            ],

            'updated_at' => [
                'type'    => 'DATETIME',
                'null'    => false,
                'default' => null,
                'on_update' => 'CURRENT_TIMESTAMP',
            ],
        ]);

        $this->forge->addKey('id', true);

        // UNIQUE (user_id + ip)
        $this->forge->addUniqueKey(['user_id', 'ip_address'], 'uniq_user_ip');

        // OPTIONAL: Foreign key (aktifkan kalau tabel users sudah pasti ada)
        // $this->forge->addForeignKey(
        //     'user_id',
        //     'users',
        //     'id',
        //     'CASCADE',
        //     'CASCADE'
        // );

        $this->forge->createTable('login_attempts', true);
    }

    public function down()
    {
        $this->forge->dropTable('login_attempts', true);
    }
}
