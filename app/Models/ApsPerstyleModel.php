<?php

namespace App\Models;

use CodeIgniter\Model;
use PHPUnit\TextUI\XmlConfiguration\Group;

class ApsPerstyleModel extends Model
{
    protected $table            = 'apsperstyle';
    protected $primaryKey       = 'idapsperstyle';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['idapsperstyle', 'machinetypeid', 'mastermodel', 'size', 'delivery', 'qty', 'sisa', 'seam', 'factory', 'production_unit', 'smv', 'no_order', 'country'];

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
            ->where('mastermodel', $validate['mastermodel'])
            ->where('size', $validate['size'])
            ->where('country', $validate['country'])
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
    public function getTurunOrderPerbulan()
    {
        $hasil = $this->join('data_model', 'data_model.no_model = apsperstyle.mastermodel')
            ->select('data_model.created_at, apsperstyle.qty')
            ->orderBy('data_model.created_at')
            ->findAll();

        $dataPerBulan = [];
        foreach ($hasil as $row) {
            $bulanTahun = date('Y-m', strtotime($row['created_at']));
            if (!isset($dataPerBulan[$bulanTahun])) {
                $dataPerBulan[$bulanTahun] = ['total_qty' => 0, 'details' => []];
            }
            $dataPerBulan[$bulanTahun]['total_qty'] += $row['qty'];
            array_push($dataPerBulan[$bulanTahun]['details'], $row);
        }

        return $dataPerBulan;
    }
    public function getPerArea($area)
    {
        return $this->where('factory', $area)->findAll();
    }
    public function getId($validate)
    {
        return $this->select('idapsperstyle')->where('mastermodel', $validate['no_model'])->where('delivery', $validate['delivery'])->where('size', $validate['style'])->first();
    }
    public function getPlanJarum($cek, $type)
    {
        $results = $this->join('data_model', 'data_model.no_model = apsperstyle.mastermodel')
            ->join('master_product_type', 'master_product_type.id_product_type = data_model.id_product_type')
            ->groupBy('apsperstyle.delivery, master_product_type.keterangan')
            ->select('apsperstyle.delivery,apsperstyle.mastermodel, master_product_type.keterangan, SUM(apsperstyle.sisa) AS total_qty')
            ->where('apsperstyle.machinetypeid', $cek['jarum'])
            ->where('apsperstyle.delivery >=', $cek['start'])
            ->where('apsperstyle.delivery <=', $cek['end'])
            ->where('master_product_type.product_type', $type)
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
    public function asignarealall($data)
    {
        $this->set('factory', $data['area'])
            ->where('mastermodel', $data['mastermodel'])
            ->update();

        return $this->affectedRows();
    }
    public function getBulan($jarum)
    {
        return $this->select("MONTHNAME(delivery) as bulan, YEAR(delivery) as tahun")
            ->where('machinetypeid', $jarum)
            ->groupBy('MONTH(delivery), YEAR(delivery)')
            ->findAll();
    }

    public function hitungMesin($cek, $type)
    {
        $this->select('delivery,smv, SUM(sisa) AS sisa, 
       DATEDIFF(delivery, CURDATE()) - 
       (SELECT COUNT(tanggal) FROM data_libur WHERE tanggal BETWEEN CURDATE() AND apsperstyle.delivery)-3 AS totalhari,
       master_product_type.product_type');
        $this->join('data_model', 'apsperstyle.mastermodel = data_model.no_model', 'left');
        $this->join('master_product_type', 'data_model.id_product_type = master_product_type.id_product_type', 'left');
        $this->where('apsperstyle.machinetypeid', $cek['jarum'])
            ->where('apsperstyle.delivery >=', $cek['start'])
            ->where('apsperstyle.delivery <=', $cek['end'])
            ->where('master_product_type.product_type', $type);
        $this->where('apsperstyle.sisa >', 0);
        $this->groupBy('apsperstyle.delivery, master_product_type.product_type');

        return $this->get()->getResultArray();
    }

    public function getProgress($noModel)
    {
        $res = $this->select('mastermodel, SUM(qty) as target, SUM(sisa) as remain')
            ->where('mastermodel', $noModel)
            ->groupBy('mastermodel')
            ->get()
            ->getResultArray();

        $reset = [];
        foreach ($res as $data) {
            $produksi = ($data['target'] - $data['remain']) / 24;
            $target = $data['target'] / 24;

            $persen = ($produksi / $target) * 100;
            $formated = round($persen);
            $data['persen'] = $formated;
            $reset[] = [
                'mastermodel' => $data['mastermodel'],
                'target' => round($target),
                'persen' => $formated,
                'remain' => round($produksi)
            ];
        }
        return $reset;
    }
    public function getIdMinus($validate)
    {
        return $this->select('idapsperstyle, delivery, sisa')
            ->where('mastermodel', $validate['no_model'])
            ->where('sisa <=', 0)
            ->where('size', $validate['style'])
            ->first();
    }

    public function getIdProd($validate)
    {
        return $this->select('idapsperstyle, delivery, sisa')
            ->where('mastermodel', $validate['no_model'])
            ->where('sisa >', 0)
            ->where('size', $validate['style'])
            ->first();
    }

    public function getIdBawahnya($validate)
    {
        return $this->select('idapsperstyle, delivery, sisa')
            ->where('mastermodel', $validate['no_model'])
            ->where('sisa >', $validate['sisa'])
            ->where('size', $validate['style'])
            ->first();
    }
    public function getDetailPlanning($area, $jarum) // funtion ieu kudu diganti where na kade ulah poho
    {
        return $this->select('mastermodel AS model, delivery, SUM(qty)/24 AS qty, SUM(sisa)/24 AS sisa, AVG(smv) AS smv')
            ->where('factory', $area)
            ->where('machinetypeid', $jarum)
            ->where('sisa >', 0)
            ->where('delivery > DATE_ADD(NOW(), INTERVAL 7 DAY)', null, false) // Add 7 days to current date
            ->groupBy(['delivery', 'mastermodel'])
            ->findAll();
    }

    public function getSmv()
    {
        return $this->select('idapsperstyle,mastermodel,size,smv')
            ->groupBy('size', 'mastermodel')
            ->findAll();
    }
    public function getPdkProduksi()
    {
        return $this->select('mastermodel, sum(qty) as totalqty, sum(sisa) as totalsisa, sum(qty)-sum(sisa) as totalproduksi')
            ->groupBy('mastermodel')
            ->findAll();
    }
}
