<?php

namespace App\Models;

use CodeIgniter\Model;

class DataOrder extends Model
{
    protected $table            = 'data_order';
    protected $primaryKey       = 'id_order';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id_order', 'id_booking', 'tgl_terima_order', 'kd_buyer_order', 'id_product_type', 'qty_order', 'sisa_order', 'id_hari', 'id_plan'];

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


    // Fungsi untuk mendapatkan semua data dari tabel
    public function getOrder()
    {
        return $this->findAll(); // Mengembalikan seluruh data
    }

    // // Fungsi untuk mendapatkan data berdasarkan kondisi
    // public function getDataByCondition($condition)
    // {
    //     return $this->where($condition)->findAll(); // Mengembalikan data berdasarkan kondisi
    // }
    
}
