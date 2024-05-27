<?php

namespace App\Models;

use CodeIgniter\Model;

class DetailPlanningModel extends Model
{
    protected $table            = 'detail_planning';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id_detail_planning','id_pln_mc','model','delivery','qty','sisa','smv'];

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

    public function getDataPlanning($id){
        return $this->select('detail_planning.id_detail_pln,model,delivery,qty,sisa,smv,MIN(date) as start_date,MAX(date) as stop_date,MAX(mesin) as mesin,sum(est_qty) as est_qty,max(hari) as hari')
        ->join('tanggal_planning','detail_planning.id_detail_pln = tanggal_planning.id_detail_pln','LEFT')
        ->join('estimated_planning','estimated_planning.id_detail_pln = detail_planning.id_detail_pln','LEFT')
        ->where('id_pln_mc',$id)
        ->groupby('delivery','model')
        ->findAll();
    }
    public function getDetailPlanning($id){
        return $this->select('detail_planning.id_detail_pln,model,delivery,qty,sisa,smv,MIN(date) as start_date,MAX(date) as stop_date,MAX(mesin) as mesin,sum(est_qty) as est_qty,max(hari) as hari')
        ->join('tanggal_planning','detail_planning.id_detail_pln = tanggal_planning.id_detail_pln','LEFT')
        ->join('estimated_planning','estimated_planning.id_detail_pln = detail_planning.id_detail_pln','LEFT')
        ->where('detail_planning.id_detail_pln',$id)
        ->groupby('delivery','model')
        ->findAll();
    }
}
