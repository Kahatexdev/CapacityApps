<?php

namespace App\Models;

use CodeIgniter\Model;

class PpsModel extends Model
{
    protected $table            = 'pps';
    protected $primaryKey       = 'id_pps';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields = [
        'id_mesin_perinisial',
        'material_status',
        'pps_status',
        'priority',
        'mechanic',
        'notes',
        'history',
        'coor',
        'start_mc',
        'start_pps_plan',
        'stop_pps_plan',
        'start_pps_act',
        'stop_pps_act',
        'acc_mr',
        'acc_qad',
        'acc_fu',
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
}
