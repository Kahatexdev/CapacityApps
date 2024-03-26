<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class HolidayDate extends Migration
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
            'date' => [
                'type' => 'DATE',
            ],
            'description' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('holidays');
    }

    public function down()
    {
        //
    }
}
