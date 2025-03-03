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
    protected $allowedFields    = ['id_detail_pln', 'id_pln_mc', 'model', 'smv', 'jarum', 'status'];

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
            ->where('detail_planning.status', 'aktif')
            ->groupBy('detail_planning.id_detail_pln, detail_planning.model')
            ->orderBy('detail_planning.model')
            ->findAll();
    }
    public function getDataPlanning2($id)
    {
        return $this->select('detail_planning.model, ap.delivery, ap.qty, ap.sisa, ap.machinetypeid, detail_planning.id_detail_pln, detail_planning.id_pln_mc, detail_planning.smv, ep.id_est_qty, ep.hari, ep.precentage_target, ep.delivery2, tp.start_date, tp.stop_date')
            // Subquery untuk apsperstyle, dengan SUM untuk qty dan sisa
            ->join('(SELECT mastermodel, delivery, SUM(qty) AS qty, SUM(sisa) AS sisa, machinetypeid FROM apsperstyle GROUP BY mastermodel, machinetypeid, delivery) ap', 'ap.mastermodel = detail_planning.model', 'right')
            // Subquery untuk estimated_planning
            ->join('(SELECT id_detail_pln, id_est_qty, hari, precentage_target, delivery AS delivery2 FROM estimated_planning GROUP BY id_est_qty) ep', 'ep.id_detail_pln = detail_planning.id_detail_pln AND ep.delivery2 = ap.delivery', 'left')
            // Join dengan tanggal_planning
            ->join('(SELECT id_est_qty, MIN(date) AS start_date, MAX(date) AS stop_date FROM tanggal_planning GROUP BY id_est_qty) tp', 'tp.id_est_qty = ep.id_est_qty', 'left')
            // Kondisi WHERE
            ->where('detail_planning.id_pln_mc', $id)
            // Grouping berdasarkan kolom yang relevan
            ->groupBy('detail_planning.model, ap.delivery, detail_planning.id_detail_pln, ep.id_est_qty, ep.hari, ep.precentage_target')
            // Urutkan berdasarkan mastermodel
            ->orderBy('detail_planning.model, ap.delivery')
            // Ambil hasilnya
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
        return $this->select('id_detail_pln')->where('id_pln_mc', $id)
            ->where('status', 'aktif')
            ->findAll();
    }
    public function detailPdk($id)
    {
        return $this->select('model,jarum')
            ->where('id_detail_pln', $id)
            ->first();
    }
    public function getDataPlanningStop($id)
    {
        return $this->select('detail_planning.id_detail_pln, detail_planning.model, detail_planning.smv,detail_planning.jarum, MIN(tp.date) AS start_date, MAX(tp.date) AS stop_date, ep.total_est_qty AS est_qty, ep.max_hari AS hari, ep.precentage_target, ep.delivery')
            ->join('tanggal_planning tp', 'detail_planning.id_detail_pln = tp.id_detail_pln', 'left')
            ->join('(SELECT id_detail_pln, SUM(est_qty) AS total_est_qty, MAX(hari) AS max_hari, precentage_target, delivery FROM estimated_planning GROUP BY id_detail_pln) ep', 'detail_planning.id_detail_pln = ep.id_detail_pln', 'left')
            ->where('detail_planning.id_pln_mc', $id)
            ->where('detail_planning.status', 'stop')
            ->groupBy('detail_planning.id_detail_pln, detail_planning.model')
            ->orderBy('detail_planning.model')
            ->findAll();
    }
    public function getDetailPlanningStop($id)
    {
        return $this->select('detail_planning.model, ap.delivery, ap.qty, ap.sisa, detail_planning.id_detail_pln, detail_planning.id_pln_mc, detail_planning.smv, ep.id_est_qty, ep.hari, ep.precentage_target, ep.delivery2, tp.start_date, tp.stop_date')
            // Subquery untuk apsperstyle, dengan SUM untuk qty dan sisa
            ->join('(SELECT mastermodel, delivery, SUM(qty) AS qty, SUM(sisa) AS sisa FROM apsperstyle GROUP BY mastermodel, delivery) ap', 'ap.mastermodel = detail_planning.model', 'right')
            // Subquery untuk estimated_planning
            ->join('(SELECT id_detail_pln, id_est_qty, hari, precentage_target, delivery AS delivery2 FROM estimated_planning GROUP BY id_est_qty) ep', 'ep.id_detail_pln = detail_planning.id_detail_pln AND ep.delivery2 = ap.delivery', 'left')
            // Join dengan tanggal_planning
            ->join('(SELECT id_est_qty, MIN(date) AS start_date, MAX(date) AS stop_date FROM tanggal_planning GROUP BY id_est_qty) tp', 'tp.id_est_qty = ep.id_est_qty', 'left')
            // Kondisi WHERE
            ->where('detail_planning.id_pln_mc', $id)
            ->where('detail_planning.status', 'stop')
            // Grouping berdasarkan kolom yang relevan
            ->groupBy('detail_planning.model, ap.delivery, detail_planning.id_detail_pln, ep.id_est_qty, ep.hari, ep.precentage_target')
            // Urutkan berdasarkan mastermodel
            ->orderBy('detail_planning.model, ap.delivery')
            // Ambil hasilnya
            ->findAll();
    }
    public function reqstartmc($model)
    {
        return $this->select('tanggal_planning.date as start_mc')
            ->join('tanggal_planning', 'tanggal_planning.id_detail_pln = detail_planning.id_detail_pln')
            ->where('detail_planning.model', $model)
            ->where('detail_planning.status', 'aktif')
            ->first();
    }
    public function getNoModelAktif($area)
    {
        return $this->select('detail_planning.model')
            ->join('kebutuhan_area', 'detail_planning.id_pln_mc=kebutuhan_area.id_pln_mc')
            ->where('detail_planning.status', 'aktif')
            ->where('kebutuhan_area.area', $area)
            ->findAll();
    }
}
