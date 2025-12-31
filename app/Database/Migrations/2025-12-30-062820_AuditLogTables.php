<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AuditLogTables extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_log' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],

            'log_time' => ['type' => 'DATETIME'],

            'actor_name' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'actor_role' => ['type' => 'VARCHAR', 'constraint' => 50,  'null' => true],

            // Aksi & modul
            'action' => ['type' => 'VARCHAR', 'constraint' => 50], // CREATE/UPDATE/DELETE/LOGIN/APPROVE/REJECT/EXPORT
            'module' => ['type' => 'VARCHAR', 'constraint' => 50], // SPAREPART/STOCK/TX/REQUEST/MASTER/SETTINGS

            // Referensi data (misal id_sparepart / id_tx / tx_code / request_code)
            'ref_type' => ['type' => 'VARCHAR', 'constraint' => 50,  'null' => true], // SPAREPART/TX/REQUEST/etc
            'ref_id'   => ['type' => 'VARCHAR', 'constraint' => 50,  'null' => true],

            // Keterangan
            'message'     => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'payload_old' => ['type' => 'LONGTEXT', 'null' => true], // JSON (sebelum)
            'payload_new' => ['type' => 'LONGTEXT', 'null' => true], // JSON (sesudah)

            // Info request
            'ip_address' => ['type' => 'VARCHAR', 'constraint' => 45,  'null' => true],
            'user_agent' => ['type' => 'VARCHAR', 'constraint' => 200, 'null' => true],
        ]);

        $this->forge->addKey('id_log', true);
        $this->forge->addKey('log_time');
        $this->forge->addKey(['module', 'action']);
        $this->forge->addKey(['ref_type', 'ref_id']);

        $this->forge->createTable('audit_logs', true, ['ENGINE' => 'InnoDB']);
    }

    public function down()
    {
        $this->forge->dropTable('audit_logs', true);
    }
}
