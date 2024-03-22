<?php

namespace App\Models;

use CodeIgniter\Model;

class ApsPerstyleModel extends Model
{
    protected $table            = 'apsperstyle';
    protected $primaryKey       = 'idapsperstyle';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['idapsperstyle', 'machinetypeid', 'mastermodel', 'size', 'delivery', 'qty', 'sisa', 'seam', 'factory'];

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


    // Fungsi untuk mendapatkan semua data dari tabel
    public function getOrder()
    {
        return $this->findAll(); // Mengembalikan seluruh data
    }
    public function checkExist($no_model)
    {
        return $this->where('no_model', $no_model)->first();
    }
    public function checkAps($validate)
    {
        $result = $this->select('*')
            ->where('size', $validate['size'])
            ->where('delivery', $validate['delivery'])
            ->get()
            ->getRow();
        return $result;
    }
    public function detailModel($noModel, $delivery)
    {
        return $this->where('mastermodel', $noModel)
            ->where('delivery', $delivery)
            ->findAll();
    }
    public function detailModelJarum($noModel, $delivery, $jarum)
    {
        return $this->where('mastermodel', $noModel)
            ->where('delivery', $delivery)
            ->where('machinetypeid', $jarum)
            ->findAll();
    }
    public function getTurunOrder($bulan)
    {
        return $this->join('data_model', 'data_model.no_model = apsperstyle.mastermodel')
            ->select('data_model.created_at, SUM(apsperstyle.qty) as total_produksi')
            ->where('MONTH(data_model.created_at)', $bulan)
            ->groupBy('data_model.created_at')
            ->orderBy('data_model.created_at')
            ->findAll();
    }

    // // Fungsi untuk mendapatkan data berdasarkan kondisi
    // public function getDataByCondition($condition)
    // {
    //     return $this->where($condition)->findAll(); // Mengembalikan data berdasarkan kondisi
    // }

}
