<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductTypeModel extends Model
{
    protected $table            = 'master_product_type';
    protected $primaryKey       = 'id_product_type';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id_product_type', 'konversi', 'product_type', 'keterangan'];

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

    public function getId($getIdProd)
    {
        $query = $this->select('id_product_type')
            ->where('product_type', $getIdProd['prodtype'])
            ->where('jarum', $getIdProd['jarum'])
            ->first();
        return $query;
    }
    public function getKategori()
    {
        return $this->distinct()->select('keterangan')->findAll();
    }
    public function getJarum($jarum)
    {
        return $this->select('product_type')
            ->where('jarum', $jarum)
            ->findAll();
    }
    public function getDataisi()
    {
        return $this->select('*')
            ->where('konversi', 10)
            ->findAll();
    }
    public function getProductTypesByJarum($jarum)
    {
        $query = $this->select('product_type')->where('jarum', $jarum)->get();
        return array_column($query->getResultArray(), 'product_type');
    }
}
