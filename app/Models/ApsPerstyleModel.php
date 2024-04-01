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
    public function getPerArea($area)
    {
        return $this->where('factory', $area)->findAll();
    }
    public function getId($validate)
    {
        return $this->select('idapsperstyle')->where('mastermodel', $validate['no_model'])->where('delivery', $validate['delivery'])->where('size', $validate['style'])->first();
    }
    public function getPlanJarum($cek)
    {
        $results = $this->join('data_model', 'data_model.no_model = apsperstyle.mastermodel')
            ->join('master_product_type', 'master_product_type.id_product_type = data_model.id_product_type')
            ->groupBy('apsperstyle.delivery, master_product_type.keterangan')
            ->select('apsperstyle.delivery,apsperstyle.mastermodel, master_product_type.keterangan, SUM(apsperstyle.qty) AS total_qty')
            ->where('apsperstyle.machinetypeid', $cek['jarum'])
            ->where('apsperstyle.machinetypeid', $cek['jarum'])
            ->where('apsperstyle.delivery >=', $cek['start'])
            ->where('apsperstyle.delivery <=', $cek['end'])
            ->get()
            ->getResultArray();

        // Inisialisasi array untuk menyimpan hasil yang dikelompokkan berdasarkan keterangan
        $groupedResults = [];

        // Mengelompokkan hasil berdasarkan keterangan
        foreach ($results as $result) {
            $keterangan = $result['keterangan'];
            $groupedResults[$keterangan] = [

                'total_qty' => $result['total_qty'],
            ];
        }

        return $groupedResults;
    }
    public function getPlanJarumNs($cek)
    {
        $results = $this->join('data_model', 'data_model.no_model = apsperstyle.mastermodel')
            ->join('master_product_type', 'master_product_type.id_product_type = data_model.id_product_type')
            ->groupBy('apsperstyle.delivery, master_product_type.keterangan')
            ->select('apsperstyle.delivery,apsperstyle.mastermodel, master_product_type.keterangan, SUM(apsperstyle.sisa) AS total_qty')
            ->where('apsperstyle.machinetypeid', $cek['jarum'])
            ->where('apsperstyle.delivery >=', $cek['start'])
            ->where('apsperstyle.delivery <=', $cek['end'])
            ->where('master_product_type.keterangan', "Normal Sock")
            ->get()
            ->getResultArray();

        // Inisialisasi array untuk menyimpan hasil yang dikelompokkan berdasarkan keterangan
        $total_qty = 0;

        // Menghitung total_qty dari hasil query
        foreach ($results as $result) {
            $total_qty += $result['total_qty'] ?? 0;
        }


        return $total_qty;
    }

    public function getPlanJarumSs($cek)
    {
        $results = $this->join('data_model', 'data_model.no_model = apsperstyle.mastermodel')
            ->join('master_product_type', 'master_product_type.id_product_type = data_model.id_product_type')
            ->groupBy('apsperstyle.delivery, master_product_type.keterangan')
            ->select('apsperstyle.delivery,apsperstyle.mastermodel, master_product_type.keterangan, SUM(apsperstyle.sisa) AS total_qty')
            ->where('apsperstyle.machinetypeid', $cek['jarum'])
            ->where('apsperstyle.delivery >=', $cek['start'])
            ->where('apsperstyle.delivery <=', $cek['end'])
            ->where('master_product_type.keterangan', "Sneaker")
            ->get()
            ->getResultArray();
        // Inisialisasi array untuk menyimpan hasil yang dikelompokkan berdasarkan keterangan
        $total_qty = 0;
        // Menghitung total_qty dari hasil query
        foreach ($results as $result) {
            $total_qty += $result['total_qty'] ?? 0;
        }
        return $total_qty;
    }
    public function getPlanJarumKh($cek)
    {
        $results = $this->join('data_model', 'data_model.no_model = apsperstyle.mastermodel')
            ->join('master_product_type', 'master_product_type.id_product_type = data_model.id_product_type')
            ->groupBy('apsperstyle.delivery, master_product_type.keterangan')
            ->select('apsperstyle.delivery,apsperstyle.mastermodel, master_product_type.keterangan, SUM(apsperstyle.sisa) AS total_qty')
            ->where('apsperstyle.machinetypeid', $cek['jarum'])
            ->where('apsperstyle.delivery >=', $cek['start'])
            ->where('apsperstyle.delivery <=', $cek['end'])
            ->where('master_product_type.keterangan', "Knee High")
            ->get()
            ->getResultArray();
        // Inisialisasi array untuk menyimpan hasil yang dikelompokkan berdasarkan keterangan
        $total_qty = 0;
        // Menghitung total_qty dari hasil query
        foreach ($results as $result) {
            $total_qty += $result['total_qty'] ?? 0;
        }
        return $total_qty;
    }
    public function getPlanJarumFs($cek)
    {
        $results = $this->join('data_model', 'data_model.no_model = apsperstyle.mastermodel')
            ->join('master_product_type', 'master_product_type.id_product_type = data_model.id_product_type')
            ->groupBy('apsperstyle.delivery, master_product_type.keterangan')
            ->select('apsperstyle.delivery,apsperstyle.mastermodel, master_product_type.keterangan, SUM(apsperstyle.sisa) AS total_qty')
            ->where('apsperstyle.machinetypeid', $cek['jarum'])
            ->where('apsperstyle.delivery >=', $cek['start'])
            ->where('apsperstyle.delivery <=', $cek['end'])
            ->where('master_product_type.keterangan', "Footies")
            ->get()
            ->getResultArray();
        // Inisialisasi array untuk menyimpan hasil yang dikelompokkan berdasarkan keterangan
        $total_qty = 0;
        // Menghitung total_qty dari hasil query
        foreach ($results as $result) {
            $total_qty += $result['total_qty'] ?? 0;
        }
        return $total_qty;
    }
    public function getPlanJarumT($cek)
    {
        $results = $this->join('data_model', 'data_model.no_model = apsperstyle.mastermodel')
            ->join('master_product_type', 'master_product_type.id_product_type = data_model.id_product_type')
            ->groupBy('apsperstyle.delivery, master_product_type.keterangan')
            ->select('apsperstyle.delivery,apsperstyle.mastermodel, master_product_type.keterangan, SUM(apsperstyle.sisa) AS total_qty')
            ->where('apsperstyle.machinetypeid', $cek['jarum'])
            ->where('apsperstyle.delivery >=', $cek['start'])
            ->where('apsperstyle.delivery <=', $cek['end'])
            ->where('master_product_type.keterangan', "Tight")
            ->get()
            ->getResultArray();
        // Inisialisasi array untuk menyimpan hasil yang dikelompokkan berdasarkan keterangan
        $total_qty = 0;
        // Menghitung total_qty dari hasil query
        foreach ($results as $result) {
            $total_qty += $result['total_qty'] ?? 0;
        }
        return $total_qty;
    }
    public function asignareal($data)
    {
        $this->set('factory', $data['area'])
            ->where('mastermodel', $data['mastermodel'])
            ->where('machinetypeid', $data['jarum'])
            ->update();

        return $this->affectedRows();
    }
    // // Fungsi untuk mendapatkan data berdasarkan kondisi
    // public function getDataByCondition($condition)
    // {
    //     return $this->where($condition)->findAll(); // Mengembalikan data berdasarkan kondisi
    // }

}
