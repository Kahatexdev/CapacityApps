<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIndexToApsperstyleInSummary extends Migration
{
    public function up()
    {
        // Add the index to the existing table without recreating it
        $this->db->query("ALTER TABLE apsperstyle ADD INDEX idx_summary (machinetypeid, no_order, mastermodel, size, color, delivery, qty, sisa, seam, factory, smv)");
        $this->db->query("ALTER TABLE produksi ADD INDEX idx_summary (tgl_produksi, qty_produksi, plus_packing, bs_prod, area)");
        $this->db->query("ALTER TABLE produksi ADD INDEX idx_timter (tgl_produksi, qty_produksi, plus_packing, bs_prod, no_mesin, area, no_label, shift_a, shift_b, shift_c)");
    }

    public function down()
    {
        // Remove the index if the migration is rolled back
        $this->db->query("ALTER TABLE apsperstyle DROP INDEX idx_summary");
        $this->db->query("ALTER TABLE produksi DROP INDEX idx_summary");
        $this->db->query("ALTER TABLE produksi DROP INDEX idx_timter");
    }
}
