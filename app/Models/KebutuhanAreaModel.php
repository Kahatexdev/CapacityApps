<?php

namespace App\Models;

use CodeIgniter\Model;

class KebutuhanAreaModel extends Model
{
    protected $table            = 'kebutuhan_area';
    protected $primaryKey       = 'id_pln_mc';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id_pln_mc', 'judul', 'jarum', 'area', 'created_at', 'updated_at'];

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

    public function getDatabyArea($ar)
    {
        return $this->select('*')
            ->where('area', $ar)
            ->findAll();
    }
    public function getDataByAreaGroupJrm($area)
    {
        return $this->select('jarum')
            ->where('area', $area)
            ->groupBy('jarum')
            ->findAll();
    }
    public function getDataByAreaJrm($area, $jarum)
    {
        return $this->select('*')
            ->where('area', $area)
            ->where('jarum', $jarum)
            ->findAll();
    }
}
