<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateIndexDataBs extends Migration
{
    public function up()
    {
        // Add the index to the existing table without recreating it
        $this->db->query("ALTER TABLE apsperstyle ADD INDEX idx_summary2 (po_plus)");
        $this->db->query("ALTER TABLE data_bs ADD INDEX idx_summary (idbs, idapsperstyle, tgl_instocklot, area, qty)");
    }

    public function down()
    {
        $this->db->query("ALTER TABLE apsperstyle DROP INDEX idx_summary2");
        $this->db->query("ALTER TABLE data_bs DROP INDEX idx_summary");
    }
}
