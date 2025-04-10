<?php

namespace App\Models;

use CodeIgniter\Model;

class AreaMachineModel extends Model
{
    protected $table      = 'area_machine';
    protected $primaryKey = 'id_area_machine';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields = ['id_monthly_mc', 'area', 'total_mc', 'planning_mc', 'output', 'operator', 'montir', 'inline', 'wly'];
    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
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

    public function existData($data)
    {
        return $this->select('id_area_machine')->where($data)->first();
    }
    public function getData($idGloblal)
    {
        return $this->where('id_monthly_mc', $idGloblal)->findAll();
    }
}
