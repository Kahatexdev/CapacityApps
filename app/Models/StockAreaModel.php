<?php

namespace App\Models;

use CodeIgniter\Model;

class StockAreaModel extends Model
{
    protected $table            = 'stock_area';
    protected $primaryKey       = 'id_stock_area';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id_pengeluaran', 'no_karung', 'no_model', 'item_type', 'kode_warna', 'warna', 'lot', 'kgs_in_out', 'cns_in_out', 'kg_cns', 'admin', 'area'];

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

    public function getStock($area)
    {
        return $this->where('area', $area)
            ->where('cns_in_out >', 0)
            ->findAll();
    }
    public function decreaseStock($idStock, $cnsOut, $kgOut)
    {
        return $this->db->table($this->table)
            ->where($this->primaryKey, $idStock)
            ->set('cns_in_out', "cns_in_out - " . (float)$cnsOut, false)
            ->set('kgs_in_out', "kgs_in_out - " . (float)$kgOut, false)
            ->update();
    }
}
