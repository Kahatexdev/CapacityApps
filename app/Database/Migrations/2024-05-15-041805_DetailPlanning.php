<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DetailPlanning extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_detail_pln' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'id_pln_mc' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'model' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'qty' => [
                'type' => 'INT',
                'constraint' => 15,
                'unsigned' => true,
            ],
            'sisa' => [
                'type' => 'INT',
                'constraint' => 15,
                'unsigned' => true,
            ],
            'qty planned' => [
                'type' => 'INT',
                'constraint' => 15,
                'unsigned' => true,
            ],
        ]);
        $this->forge->addKey('id_detail_pln', true);
        $this->forge->createTable('detail_planning');
    }

    public function down()
    {
        $this->forge->dropTable('kebutuhan_area');
    }
}
