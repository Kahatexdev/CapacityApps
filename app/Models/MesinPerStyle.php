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
    protected $allowedFields    = [
        'id_mesin_perinisial',
        'idapsperstyle',
        'mesin',
        'id_pps',
        'keterangan',
        'material_status',
        'priority',
        'start_pps_plan',
        'stop_pps_plan',
        'admin',
        'repeat_from'
    ];

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
        return $this->where('mesin_perinisial.idapsperstyle', $idAps)
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
    public function reqstartmc($model)
    {
        $dm = $this->select('MIN(start_pps_plan) AS start_mc')
            ->join('apsperstyle', 'apsperstyle.idapsperstyle=mesin_perinisial.idapsperstyle')
            ->where('apsperstyle.mastermodel', $model)
            ->first();
        if (empty($dm['start_mc'])) {
            return $this->db->table('data_model')
                ->select('start_mc')->where('no_model', $model)->get()
                ->getRowArray();
        } else {
            return [
                'start_mc' => date('Y-m-d', strtotime($dm['start_mc']))
            ];
        }
    }

    public function reqstartmcBulk(array $models): array
    {
        if (empty($models)) {
            return [];
        }

        /*
        * 1. Ambil dari mesin_perinisial + apsperstyle
        */
        $rows = $this->select('apsperstyle.mastermodel, MIN(start_pps_plan) AS start_mc')
            ->join(
                'apsperstyle',
                'apsperstyle.idapsperstyle = mesin_perinisial.idapsperstyle'
            )
            ->whereIn('apsperstyle.mastermodel', $models)
            ->groupBy('apsperstyle.mastermodel')
            ->findAll();

        $result = [];

        foreach ($rows as $row) {
            if (!empty($row['start_mc'])) {
                $result[$row['mastermodel']] =
                    date('Y-m-d', strtotime($row['start_mc']));
            }
        }

        /*
        * 2. Fallback ke data_model kalau belum ketemu
        */
        $missingModels = array_diff($models, array_keys($result));

        if (!empty($missingModels)) {
            $fallback = $this->db->table('data_model')
                ->select('no_model, start_mc')
                ->whereIn('no_model', $missingModels)
                ->get()
                ->getResultArray();

            foreach ($fallback as $fb) {
                if (!empty($fb['start_mc'])) {
                    $result[$fb['no_model']] = $fb['start_mc'];
                }
            }
        }

        return $result;
    }

}
