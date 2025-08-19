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
        return $this->select('id_pln_mc,jarum')
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
    public function getJlMcPlanning($data)
    {
        return $this->select('
                kebutuhan_area.id_pln_mc, 
                detail_planning.id_detail_pln, 
                estimated_planning.id_est_qty, 
                tanggal_planning.id, 
                kebutuhan_area.area, 
                kebutuhan_area.jarum, 
                detail_planning.model, 
                estimated_planning.delivery, 
                tanggal_planning.mesin
            ')
            ->join('detail_planning', 'detail_planning.id_pln_mc = kebutuhan_area.id_pln_mc', 'left')
            ->join('estimated_planning', 'estimated_planning.id_detail_pln = detail_planning.id_detail_pln', 'left')
            ->join('tanggal_planning', 'tanggal_planning.id_est_qty = estimated_planning.id_est_qty', 'left')
            ->where([
                'detail_planning.model'     => $data['model'],
                'kebutuhan_area.jarum'      => $data['jarum'],
                'kebutuhan_area.area'       => $data['area'],
                'estimated_planning.delivery' => $data['delivery'],
            ])
            ->groupBy('estimated_planning.id_est_qty, tanggal_planning.mesin')
            ->get()
            ->getResultArray();
    }
}
