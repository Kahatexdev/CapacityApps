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
    protected $allowedFields    = ['id_detail_planning','id_pln_mc','model','delivery','qty','sisa','qty_planned'];

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
        return $this->select('detail_planning.id_detail_pln,model,delivery,qty,sisa,smv,MIN(start_date) as start_date,MAX(stop_date) as stop_date,MAX(mesin) as mesin,SUM(est_qty) as est_qty,max(hari) as hari')
        ->join('tanggal_planning','detail_planning.id_detail_pln = tanggal_planning.id_detail_pln','LEFT')
        ->where('id_pln_mc',$id)
        ->groupby('delivery','model')
        ->findAll();
    }
}
