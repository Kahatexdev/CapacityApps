<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PengaduanTable extends Migration
{
    public function up()
    {
        // Tabel pengaduan utama
        $this->forge->addField([
            'id_pengaduan' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true
            ],
            'username' => [ // identifier dari user pengadu
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'target_role' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'isi' => [
                'type' => 'TEXT',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true
            ],
            'replied' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'null'       => false,
            ],
        ]);
        $this->forge->addKey('id_pengaduan', true);
        $this->forge->createTable('pengaduan');

        // Tabel reply
        $this->forge->addField([
            'id_reply' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true
            ],
            'id_pengaduan' => [
                'type' => 'INT',
                'unsigned' => true,
            ],
            'username' => [ // identifier dari user yang reply
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'isi' => [
                'type' => 'TEXT',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true
            ],
        ]);
        $this->forge->addKey('id_reply', true);
        $this->forge->createTable('pengaduan_reply');
    }

    public function down()
    {
        $this->forge->dropTable('pengaduan_reply');
        $this->forge->dropTable('pengaduan');
    }
}
