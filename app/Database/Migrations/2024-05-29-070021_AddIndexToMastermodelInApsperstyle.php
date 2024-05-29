<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIndexToMastermodelInApsperstyle extends Migration
{
    public function up()
    {
        $this->db->query('CREATE INDEX idx_mastermodel ON apsperstyle(mastermodel)');
        $this->db->query('CREATE INDEX idx_no_model ON data_model(no_model)');
        $this->db->query('CREATE INDEX idx_groupby ON apsperstyle(delivery, machinetypeid, mastermodel)');
        $this->db->query('CREATE INDEX idx_id_product_type ON data_model(id_product_type)');
    }

    public function down()
    {
        $this->db->query('DROP INDEX idx_mastermodel ON apsperstyle');
        $this->db->query('DROP INDEX idx_no_model ON data_model');
        $this->db->query('DROP INDEX idx_groupby ON apsperstyle');
        $this->db->query('DROP INDEX idx_id_product_type ON data_model');
    }
}
