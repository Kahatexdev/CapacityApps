<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SetFloatKgCns extends Migration
{
    public function up()
    {
        $fields = [
            'kg_cns' => [
                'type'       => 'FLOAT',
                'null'       => true,
                'default'    => null,
            ],
            'kgs_in_out' => [
                'type'       => 'FLOAT',
                'null'       => true,
                'default'    => null,
            ],
            'cns_in_out' => [
                'type'       => 'FLOAT',
                'null'       => true,
                'default'    => null,
            ],
        ];

        $this->forge->modifyColumn('stock_area', $fields);
    }

    public function down()
    {
        // Optional: rollback ke tipe sebelumnya (misal DECIMAL atau INT)
        $fields = [
            'kg_cns' => [
                'type' => 'INT',
                'null' => true,
            ],
            'kgs_in_out' => [
                'type' => 'INT',
                'null' => true,
            ],
            'cns_in_out' => [
                'type' => 'INT',
                'null' => true,
            ],
        ];

        $this->forge->modifyColumn('stock_area', $fields);
    }
}
