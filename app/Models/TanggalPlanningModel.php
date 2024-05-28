<?php

namespace App\Models;

use CodeIgniter\Model;

class TanggalPlanningModel extends Model
{
    protected $table            = 'tanggal_planning';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id','id_detail_pln','id_est_qty','date','mesin'];

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

    public function getMesinByDate($id){
        return $this->select('tanggal_planning.DATE, SUM(tanggal_planning.mesin) AS mesin')
        ->join('detail_planning', 'tanggal_planning.id_detail_pln = detail_planning.id_detail_pln', 'left')
        ->where('detail_planning.id_pln_mc', 3)
        ->groupBy('tanggal_planning.DATE')
        ->get();
    }
}
