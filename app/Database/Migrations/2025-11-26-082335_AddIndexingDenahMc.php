<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIndexingDenahMc extends Migration
{
    public function up()
    {
        // machines
        $this->db->query("
            ALTER TABLE `machines`
            ADD INDEX `idx_machines_area_id` (`area`, `id`),
            ADD INDEX `idx_machines_no_mc_jarum` (`no_mc`, `jarum`)
        ");

        // produksi
        $this->db->query("
            ALTER TABLE `produksi`
            ADD INDEX `idx_produksi_tgl_area_no_mc_idaps` (
                `tgl_produksi`,
                `area`,
                `no_mesin`,
                `idapsperstyle`
            )
        ");

        // (optional) apsperstyle
        $this->db->query("
            ALTER TABLE `apsperstyle`
            ADD INDEX `idx_aps_machinetypeid` (`machinetypeid`)
        ");
    }

    public function down()
    {
        // machines
        $this->db->query("
            ALTER TABLE `machines`
            DROP INDEX `idx_machines_area_id`,
            DROP INDEX `idx_machines_no_mc_jarum`
        ");

        // produksi
        $this->db->query("
            ALTER TABLE `produksi`
            DROP INDEX `idx_produksi_tgl_area_no_mc_idaps`,
        ");

        // (optional) apsperstyle
        $this->db->query("
            ALTER TABLE `apsperstyle`
            DROP INDEX `idx_aps_machinetypeid`
        ");
    }
}
