<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddColoumnInMonthlyMachine extends Migration
{
    public function up()
    {
        $this->forge->addColumn('monthly_mc', [
            'mc_socks' => [
                'type' => 'int',
                'constraint' => 12,
                // Other column options
            ],
            'plan_mc_socks' => [
                'type' => 'int',
                'constraint' => 12,
                // Other column options
            ],
            'mc_gloves' => [
                'type' => 'int',
                'constraint' => 12,
                // Other column options
            ],
            'plan_mc_gloves' => [
                'type' => 'int',
                'constraint' => 12,
                // Other column options
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('monthly_mc', 'mc_socks');
        $this->forge->dropColumn('monthly_mc', 'plan_mc_socks');
        $this->forge->dropColumn('monthly_mc', 'mc_gloves');
        $this->forge->dropColumn('monthly_mc', 'plan_mc_gloves');
    }
}
