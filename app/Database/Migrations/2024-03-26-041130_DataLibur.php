<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DataLibur extends Migration
{
    public function up()
    {
        // Membuat tabel holidays
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'tanggal' => [
                'type' => 'DATE',
            ],
            'nama' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            // Tambahkan kolom lain sesuai kebutuhan Anda, misalnya 'description'
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('data_libur');
    }

    public function down()
    {
        // Menghapus tabel holidays
        $this->forge->dropTable('data_libur');
    }
}
