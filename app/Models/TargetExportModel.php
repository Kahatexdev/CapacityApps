<?php

namespace App\Models;

use CodeIgniter\Model;

class TargetExportModel extends Model
{
    protected $table            = 'target_export';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id', 'month', 'qty_target', 'created_at', 'update_at'];

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

    public function getData($thisMonth)
    {
        $results = $this->select('month, qty_target')
            ->where('month', $thisMonth['month'])
            ->first();
        if ($results) {
            return $results;
        } else {
            return null; // Atau bisa mengembalikan nilai default lain sesuai kebutuhan
        }
    }
}
