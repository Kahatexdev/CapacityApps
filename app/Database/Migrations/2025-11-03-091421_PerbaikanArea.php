<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PerbaikanArea extends Migration
{

    public function up()
    {
        $this->forge->addField([
            'id_perbaikan' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'idapsperstyle' => [
                'type' => 'INT',
                'constraint' => 11,

            ],
            'tgl_perbaikan' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'no_label' => [
                'type' => 'INT',
                'constraint' => 25,
            ],
            'no_box' => [
                'type' => 'VARCHAR',
                'constraint' => 25,
            ],
            'area' => [
                'type' => 'VARCHAR',
                'constraint' => 25,
            ],
            'qty' => [
                'type' => 'INT',
                'constraint' => 25,
            ],
            'kode_deffect' => [
                'type' => 'VARCHAR',
                'constraint' => 25,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id_perbaikan', true);
        $this->forge->createTable('perbaikan_area');
    }

    public function down()
    {
        $this->forge->dropTable('perbaikan_area');
    }
}
