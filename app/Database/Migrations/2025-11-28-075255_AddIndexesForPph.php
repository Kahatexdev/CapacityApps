<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIndexesForPph extends Migration
{
    public function up()
    {
         // APSPERSTYLE
        $this->db->query("
            CREATE INDEX idx_aps_master_factory_size
            ON apsperstyle (mastermodel, factory, size)
        ");
        
        $this->db->query("
            CREATE INDEX idx_bs_idapsperstyle
            ON data_bs (idapsperstyle)
        ");

        $this->db->query("
            CREATE INDEX idx_bs_mesin_area_model_size
            ON bs_mesin (area, no_model, size)
        ");
    }

    public function down()
    {
        $this->db->query("DROP INDEX idx_aps_master_factory_size ON apsperstyle");
        $this->db->query("DROP INDEX idx_bs_idapsperstyle ON data_bs");
        $this->db->query("DROP INDEX idx_bs_mesin_area_model_size ON bs_mesin");
    }
}
