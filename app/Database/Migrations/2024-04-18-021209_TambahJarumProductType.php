<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TambahJarumProductType extends Migration
{
    public function up()
    {
        $fields = [
            'jarum' => [
                'type' => 'TEXT',
                'null' => true, // Sesuaikan apakah kolom bisa null atau tidak
            ],
        ];
        $this->forge->addColumn('master_product_type', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('master_product_type', 'jarum');
    }
}
