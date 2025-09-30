<?php

namespace App\Models;

use CodeIgniter\Model;

class MesinPernomor extends Model
{
    protected $table            = 'mesin_pernomor';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_mesin',
        'id_detail_plan',
        'idapsperstyle',
        'start_mesin',
        'stop_mesin',
        'created_at',
        'updated_at'
    ];

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

    public function getListPlan($idAps, $idplan)
    {
        return $this->select('mesin_pernomor.id, machines.no_mc,start_mesin,stop_mesin')
            ->join('machines', 'machines.id = mesin_pernomor.id_mesin', 'left')
            ->where('idapsperstyle', $idAps)->where('id_detail_plan', $idplan)->findAll();
    }
    public function hitung($idaps, $idplan)
    {
        return $this->selectCount('id_mesin', 'jumlah')
            ->where('id_detail_plan', $idplan)
            ->where('idapsperstyle', $idaps)
            ->first();
    }
}
