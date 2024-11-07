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
    protected $allowedFields    = ['id', 'id_detail_pln', 'id_est_qty', 'date', 'mesin', 'start_mesin', 'stop_mesin'];

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

    public function getMesinByDate($id, $date)
    {
        return $this->select('tanggal_planning.DATE as date, SUM(tanggal_planning.mesin) AS mesin')
            ->join('detail_planning', 'tanggal_planning.id_detail_pln = detail_planning.id_detail_pln', 'left')
            ->where('tanggal_planning.DATE', $date)
            ->where('detail_planning.id_pln_mc', $id)
            ->groupBy('tanggal_planning.DATE')
            ->get()
            ->getResultArray();
    }
    public function hapusData($idest, $iddetail)
    {
        return $this->where('id_est_qty', $idest)
            ->where('id_detail_pln', $iddetail)
            ->delete();
    }
    public function totalMc($iddetail)
    {
        return $this->select('id_est_qty,mesin')->distinct('id_est_qty')->where('id_detail_pln', $iddetail)->findAll();
    }
    public function dailyMachine($id)
    {
        return $this->select('date')
            ->selectSum('mesin')
            ->where('id_detail_pln', $id)
            ->groupBy('date')
            ->findAll();
    }
}
