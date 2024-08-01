<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Kodedeffect extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'kode_deffect' => [
                'type' => 'VARCHAR',
                'constraint' => 11,


            ],
            'Keterangan' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],


        ]);
        $this->forge->addKey('kode_deffect', true);
        $this->forge->createTable('master_deffect');
    }

    public function down()
    {
        $this->forge->dropTable('mater_deffect');
    }
}
