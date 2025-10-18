<?php

namespace App\Models;

use CodeIgniter\Model;

class MesinPerStyle extends Model
{
    protected $table            = 'mesin_perinisial';
    protected $primaryKey       = 'id_mesin_perinisial';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id_mesin_perinisial', 'idapsperstyle', 'mesin', 'pps', 'keterangan'];

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



    public function getMesin($idAps)
    {
        return $this->select('mesin,keterangan,pps')
            ->where('idapsperstyle', $idAps)
            ->first();
    }
    public function getJalanMc($noModel, $styleSize, $area)
    {
        return $this->select('SUM(mesin_perinisial.mesin) AS jalan_mc')
            ->join('apsperstyle', 'apsperstyle.idapsperstyle=mesin_perinisial.idapsperstyle')
            ->where('apsperstyle.mastermodel', $noModel)
            ->where('apsperstyle.size', $styleSize)
            ->where('apsperstyle.factory', $area)
            ->first();
    }
}
