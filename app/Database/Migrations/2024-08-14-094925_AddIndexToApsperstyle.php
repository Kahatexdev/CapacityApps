<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIndexToApsperstyle extends Migration
{
    public function up()
    {
        // Add the index to the existing table without recreating it
        $this->db->query("ALTER TABLE apsperstyle ADD INDEX idx_machinetypeid_delivery_sisa_factory (machinetypeid, delivery, sisa, factory)");
    }

    public function down()
    {
        // Remove the index if the migration is rolled back
        $this->db->query("ALTER TABLE apsperstyle DROP INDEX idx_machinetypeid_delivery_sisa_factory");
    }
}
