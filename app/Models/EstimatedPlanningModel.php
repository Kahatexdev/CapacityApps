<?php

namespace App\Models;

use CodeIgniter\Model;

class EstimatedPlanningModel extends Model
{
    protected $table            = 'estimated_planning';
    protected $primaryKey       = 'id_est_qty';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id_est_qty', 'id_detail_pln', 'Est_qty', 'hari', 'target', 'precentage_target', 'delivery', 'keterangan'];

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

    public function getId($id_pln)
    {
        return $this->select('id_est_qty')
            ->where('id_detail_pln', $id_pln)
            ->orderBy('id_est_qty', 'desc')
            ->first();
    }
    public function listPlanning($id)
    {
        return $this->select('estimated_planning.*, MIN(tanggal_planning.date) AS start_date, MAX(tanggal_planning.date) AS stop_date, mesin')
            ->join('tanggal_planning', 'estimated_planning.id_est_qty = tanggal_planning.id_est_qty', 'RIGHT')
            ->where('estimated_planning.id_detail_pln', $id)
            ->groupBy('id_est_qty')
            ->findAll();
    }
    public function deletePlaningan($idPdk)
    {
        return $this->where('id_detail_pln', $idPdk)->delete();
    }
}
