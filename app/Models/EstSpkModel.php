<?php

namespace App\Models;

use CodeIgniter\Model;

class EstSpkModel extends Model
{
    protected $table            = 'estimasi_spk';
    protected $primaryKey       =
    'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['model', 'style', 'area', 'qty', 'status'];

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


    public function cekStatus($model, $style, $area)
    {
        return $this
            ->where('model', $model)
            ->where('style', $style)
            ->where('area', $area)
            ->first();
    }

    public function getData()
    {
        return $this->select('estimasi_spk.*, DATE(created_at) AS tgl_buat')
            ->where('status', 'sudah')
            ->orderBy('created_at', 'ASC')
            ->findAll();
    }
    public function getHistory($area, $lastmonth)
    {
        return $this->where('area', $area)
            ->where('updated_at>', $lastmonth)
            ->findAll();
    }
}
