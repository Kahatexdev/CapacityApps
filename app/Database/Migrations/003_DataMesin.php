<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DataMesin extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_data_mesin' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'area' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
            ],
            'jarum' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
            ],
            'total_mc' => [
                'type' => 'INT',
                'constraint' => 10,
            ],
            'brand' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'mesin_jalan' => [
                'type' => 'INT',
                'constraint' => 10,
            ],

        ]);
        $this->forge->addKey('id_data_mesin', true);
        $this->forge->createTable('data_mesin');
    }

    public function down()
    {
        $this->forge->dropTable('data_mesin');
    }
}
