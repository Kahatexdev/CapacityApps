<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PengaduanTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'parent_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
                'null'     => true,
            ],
            'user_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => false,
            ],
            'target_role' => [
                'type'       => 'ENUM',
                'constraint' => ['capacity', 'planning', 'aps', 'user', 'rosso', 'gbn', 'celup', 'sudo', 'monitoring', 'covering'],
                'null'       => false,
            ],
            'isi' => [
                'type' => 'TEXT',
                'null' => false,
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => false,

            ],
            'updated_at' => [
                'type'    => 'DATETIME',
                'null'    => false,

            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('parent_id');
        $this->forge->addKey('user_id');
        $this->forge->addKey('target_role');

        $this->forge->addForeignKey('parent_id', 'pengaduan', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'user', 'id_user', 'CASCADE', 'CASCADE');

        $this->forge->createTable('pengaduan', true);
    }

    public function down()
    {
        $this->forge->dropTable('pengaduan', true);
    }
}
