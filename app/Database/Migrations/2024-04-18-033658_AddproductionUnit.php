<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddProductionUnit extends Migration
{
    public function up()
    {
        $fields = [
            'production_unit' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true
            ],
            'smv' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true
            ],
        ];
        $this->forge->addColumn('apsperstyle', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('apsperstyle', 'production_unit');
        $this->forge->dropColumn('apsperstyle', 'smv');
    }
}
