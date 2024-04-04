<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class KebutuhanMesin extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'judul' => [
                'type' => 'VARCHAR',
                'constraint' => 255
            ],
            'jarum' => [
                'type' => 'VARCHAR',
                'constraint' => 12,
            ],
            'mesin' => [
                'type' => 'INT',
                'constraint' => 30,
            ],
            'jumlah_hari' => [
                'type' => 'INT',
                'constraint' => 30,
            ],
            'created_at' => [
                'type' => 'date',

            ],
            'updated_at' => [
                'type' => 'date',

            ],
            // Tambahkan kolom lain sesuai kebutuhan Anda, misalnya 'description'
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('kebutuhan_mesin');
    }

    public function down()
    {
        // Menghapus tabel holidays
        $this->forge->dropTable('kebutuhan_mesin');
    }
}
