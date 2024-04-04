<?php

namespace App\Models;

use CodeIgniter\Model;

class TestModel extends Model
{
    protected $table            = 'apsperstyle';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    public function getDeliveryData()
    {
        $builder = $this->db->table($this->table);
        $builder->select('delivery, SUM(sisa) AS sisa, 
            DATEDIFF(delivery, CURDATE()) - 
            (SELECT COUNT(tanggal) FROM data_libur WHERE tanggal BETWEEN CURDATE() AND apsperstyle.delivery)-3 AS totalhari, 
            master_product_type.keterangan');
        $builder->join('data_model', 'apsperstyle.mastermodel = data_model.no_model', 'left');
        $builder->join('master_product_type', 'data_model.id_product_type = master_product_type.id_product_type', 'left');
        $builder->where('apsperstyle.machinetypeid', 'tj144');
        $builder->where('apsperstyle.delivery >', date('Y-m-d'));
        $builder->groupBy('apsperstyle.delivery, master_product_type.keterangan');

        return $builder->get()->getResultArray();
    }
}
