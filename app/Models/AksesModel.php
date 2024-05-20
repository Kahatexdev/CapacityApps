<?php

namespace App\Models;

use CodeIgniter\Model;

class AksesModel extends Model
{
    protected $table            = 'user_areas';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields = ['user_id', 'area_id', 'created_at', 'updated_at'];

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

    public function getArea($id)
    {
        $area = $this->select('areas.name')
            ->join('areas', 'areas.id = user_areas.area_id')
            ->where('user_areas.user_id', $id)
            ->get()
            ->getResultArray();
        $dta =  array_column($area, 'name');
        return $dta;
    }
    public function allArea()
    {
        $area = $this->select('areas.name')
            ->join('areas', 'areas.id = user_areas.area_id')
            ->get()
            ->getResultArray();
        $dta =  array_column($area, 'name');
        return $dta;
    }
}
