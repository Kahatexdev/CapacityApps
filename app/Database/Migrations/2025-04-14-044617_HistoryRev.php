<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class HistoryRev extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_history_rev' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'tanggal_rev' => [
                'type' => 'DATETIME'
            ],
            'model' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
            ],
            'keterangan' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
            ],
        ]);
        $this->forge->addKey('id_history_rev', true);
        $this->forge->createTable('history_revisi');
    }

    public function down()
    {
        $this->forge->dropTable('history_revisi');
    }
}
