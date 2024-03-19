<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDetailOrderTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_detail_order' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'id_order' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'no_model' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'no_order' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'style' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'qty_perstyle' => [
                'type' => 'INT',
                'constraint' => 50,
            ],
            'sisa_qty' => [
                'type' => 'INT',
                'constraint' => 50,
            ],
            'delivery' => [
                'type' => 'DATE',
            ],
            'needle' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'seam' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'smv' => [
                'type' => 'INT',
                'constraint'=> 10,
            ],
        ]);
        $this->forge->addKey('id_detail_order', true);
        $this->forge->addForeignKey('id_order', 'data_order', 'id_order');
        $this->forge->createTable('detail_order');
    }

    public function down()
    {
        $this->forge->dropTable('detail_order');
    }
}
