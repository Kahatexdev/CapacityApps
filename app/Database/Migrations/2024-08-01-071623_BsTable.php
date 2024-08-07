<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class BsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'idbs' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'idapsperstyle' => [
                'type' => 'INT',
                'constraint' => 11,

            ],
            'no_label' => [
                'type' => 'INT',
                'constraint' => 25,
            ],
            'no_box' => [
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
        $this->forge->addKey('idbs', true);
        $this->forge->createTable('data_bs');
    }

    public function down()
    {
        //
    }
}
