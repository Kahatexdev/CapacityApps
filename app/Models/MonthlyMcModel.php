<?php

namespace App\Models;

use CodeIgniter\Model;

class MonthlyMcModel extends Model
{
    protected $table      = 'monthly_mc';
    protected $primaryKey = 'id_monthly_mc';

    protected $allowedFields = ['judul', 'total_mc', 'planning_mc', 'total_output', 'mc_socks', 'plan_mc_socks', 'mc_gloves', 'plan_mc_gloves'];
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

    public function cekExist($data)
    {
        return $this->select('id_monthly_mc')->where('judul', $data['judul'])->first();
    }
    public function getData($judul)
    {
        return $this->where('judul', $judul)->first();
    }
    public function getPlan()
    {
        return $this->select('judul')->findAll();
    }
    public function getTarget($judul)
    {
        return $this->select('total_output, planning_mc as mesin')
            ->where('judul', $judul)
            ->first();
    }
    public function getTargetArea($judul, $area)
    {
        return $this->select('area_machine.output as total_output, area_machine.planning_mc as mesin')
            ->join('area_machine', 'area_machine.id_monthly_mc=monthly_mc.id_monthly_mc', 'left')
            ->where('monthly_mc.judul', $judul)
            ->where('area_machine.area', $area)
            ->first();
    }
}
