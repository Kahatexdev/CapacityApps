<?php

namespace App\Models;

use CodeIgniter\Model;

class DetailAreaMachineModel extends Model
{
    protected $table      = 'detail_area_machine';
    protected $primaryKey = 'id_detail_area_machine';

    protected $allowedFields = ['id_area_machine', 'jarum', 'planning_mc', 'target', 'output'];
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

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

    public function cekData($data)
    {
        return $this->select('id_detail_area_machine')->where($data)->first();
    }
    public function getData($id)
    {
        return $this->select('jarum,planning_mc')->where('id_area_machine', $id)->findAll();
    }
}
