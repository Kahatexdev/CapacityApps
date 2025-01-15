<?php

namespace App\Models;

use CodeIgniter\Model;

class DetailPlanningModel extends Model
{
    protected $table            = 'detail_planning';
    protected $primaryKey       = 'id_detail_pln';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id_detail_pln', 'id_pln_mc', 'model', 'smv', 'jarum'];

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

    public function getDataPlanning($id)
    {
        return $this->select('detail_planning.id_detail_pln, detail_planning.model, detail_planning.smv,detail_planning.jarum, MIN(tp.date) AS start_date, MAX(tp.date) AS stop_date, ep.total_est_qty AS est_qty, ep.max_hari AS hari, ep.precentage_target, ep.delivery')
            ->join('tanggal_planning tp', 'detail_planning.id_detail_pln = tp.id_detail_pln', 'left')
            ->join('(SELECT id_detail_pln, SUM(est_qty) AS total_est_qty, MAX(hari) AS max_hari, precentage_target, delivery FROM estimated_planning GROUP BY id_detail_pln) ep', 'detail_planning.id_detail_pln = ep.id_detail_pln', 'left')
            ->where('detail_planning.id_pln_mc', $id)
            ->groupBy('detail_planning.id_detail_pln, detail_planning.model')
            ->orderBy('detail_planning.model')
            ->findAll();
    }
    public function getDetailPlanning($id)
    {
        return $this->select('detail_planning.id_detail_pln,model,qty,sisa,smv,MIN(date) as start_date,MAX(date) as stop_date,MAX(mesin) as mesin,sum(est_qty) as est_qty,max(hari) as hari')
            ->join('tanggal_planning', 'detail_planning.id_detail_pln = tanggal_planning.id_detail_pln', 'LEFT')
            ->join('estimated_planning', 'estimated_planning.id_detail_pln = detail_planning.id_detail_pln', 'LEFT')
            ->where('detail_planning.id_detail_pln', $id)
            ->groupby('model')
            ->findAll();
    }
    public function cekPlanning($validate)
    {
        return $this->select('id_detail_pln')
            ->where('id_pln_mc', $validate['id'])
            ->where('model', $validate['model'])
            ->first();
    }
    public function pdkList($id)
    {
        return $this->select('id_detail_pln')->where('id_pln_mc', $id)->findAll();
    }
    public function detailPdk($id)
    {
        return $this->select('model,jarum')
            ->where('id_detail_pln', $id)
            ->first();
    }
}
