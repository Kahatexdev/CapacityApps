<?php

namespace App\Models;

use DateTime;
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
    protected $allowedFields    = ['idapsperstyle', 'machinetypeid', 'mastermodel', 'size', 'delivery', 'qty', 'sisa', 'seam', 'factory', 'production_unit', 'smv', 'no_order', 'country', 'color'];

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
            ->where('delivery', $validate['delivery'])
            ->first();
        return $result;
    }
    public function detailModel($noModel, $delivery)
    {
        return $this->select('idapsperstyle,mastermodel,no_order,country, smv, sum(sisa) as sisa, sum(qty) as qty, machinetypeid,size,delivery,seam,factory,production_unit')
            ->where('mastermodel', $noModel)
            ->where('delivery', $delivery)
            ->groupby('size, machinetypeid')
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
    public function getPlanJarum($cek)
    {
        $results = $this
            ->select('delivery,mastermodel, SUM(sisa) AS total_qty')
            ->where('machinetypeid', $cek['jarum'])
            ->where('delivery >=', $cek['start'])
            ->where('delivery <=', $cek['end'])
            ->where('sisa >', 0)
            ->groupBy('mastermodel,delivery')
            ->findAll();

        // Inisialisasi array untuk menyimpan hasil yang dikelompokkan berdasarkan keterangan
        $total_qty = 0;

        // Menghitung total_qty dari hasil query
        foreach ($results as $result) {
            $total_qty += $result['total_qty'] ?? 0;
        }

        return $results;
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

    public function hitungMesin($cek)
    {
        $this->select('mastermodel,size, delivery,smv, SUM(sisa) AS sisa,size, 
       DATEDIFF(delivery, CURDATE()) - 
       (SELECT COUNT(tanggal) FROM data_libur WHERE tanggal BETWEEN CURDATE() AND apsperstyle.delivery)-3 AS totalhari,');
        $this->where('machinetypeid', $cek['jarum'])
            ->where('delivery >=', $cek['start'])
            ->where('delivery <=', $cek['end'])
            ->where('production_unit !=', 'MAJALAYA');
        $this->where('sisa >', 0);
        $this->groupBy('smv,mastermodel, delivery');
        $this->orderBy('delivery');

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
            ->where('delivery > NOW()', null, false) // Add 7 days to current date
            // ->where('delivery > DATE_ADD(NOW(), INTERVAL 7 DAY)', null, false) // Add 7 days to current date
            ->groupBy(['delivery', 'mastermodel'])
            ->findAll();
    }

    public function getSmv()
    {
        $monthago = date('Y-m-d', strtotime('10 days ago')); // Menggunakan format tanggal yang benar
        return $this->select('idapsperstyle,mastermodel,size,smv')
            ->where('delivery >', $monthago) // Perbaiki spasi di operator where
            ->groupBy(['size', 'mastermodel']) // Menggunakan array untuk groupBy
            ->findAll();
    }

    public function getPdkProduksi()
    {
        return $this->select('mastermodel, sum(qty) as totalqty, sum(sisa) as totalsisa, sum(qty)-sum(sisa) as totalproduksi')
            ->groupBy('mastermodel')
            ->findAll();
    }

    public function getIdSmv($validate)
    {
        $id = $this->select('idapsperstyle,smv')
            ->where('mastermodel', $validate['mastermodel'])
            ->where('size', $validate['size'])
            ->findAll();

        return $id;
    }

    public function getIdAps($pdk)
    {
        $results = $this->select('idapsperstyle')->where('mastermodel', $pdk)->findAll();
        return array_column($results, 'idapsperstyle');
    }

    public function resetSisa($pdk)
    {
        return $this->db->table($this->table)
            ->set('sisa', 'qty', false)  // 'false' agar 'qty' tidak dianggap sebagai string literal
            ->where('mastermodel', $pdk)
            ->update();
    }
    public function getSisaOrder($idaps)
    {
        $res = $this->select('sisa')->where('idapsperstyle', $idaps)->first();
        $result = reset($res);
        return $result;
    }
    public function resetProduksiArea($pr)
    {
        return $this->db->table($this->table)
            ->set('sisa', 'qty', false)
            ->where('idapsperstyle', $pr['idapsperstyle'])
            ->update();
    }
    public function listOrderArea($area, $jarum)
    {
        $today = date('Y-m-d', strtotime('+1 Days'));
        $maxDeliv = date('Y-m-d', strtotime('+90 Days'));

        return $this->select('mastermodel')
            ->where('factory', $area)
            ->where('machinetypeid', $jarum)
            ->where('sisa >', 0)
            ->where('delivery <', $maxDeliv)
            ->where('delivery >', $today)
            ->groupBy('mastermodel')
            ->findAll();
    }
    public function CapacityArea($pdk, $area, $jarum)
    {
        $oneweek = date('Y-m-d', strtotime('+7 Days'));
        $data = $this->select('mastermodel,sum(sisa)as sisa,delivery,smv')
            ->where('mastermodel', $pdk)
            ->where('factory', $area)
            ->where('machinetypeid', $jarum)
            ->where('sisa >', 0)
            ->where('delivery >', $oneweek)
            ->groupBy('mastermodel,delivery')
            ->get()
            ->getResultArray();

        $sisaArray = array_column($data, 'sisa');
        $maxValue = max($sisaArray);
        $indexMax = array_search($maxValue, $sisaArray);
        $totalQty = 0;
        for ($i = 0; $i <= $indexMax; $i++) {
            $totalQty += $sisaArray[$i];
        }
        $totalQty = round($totalQty / 24);

        $deliveryTerjauh = end($data)['delivery'];
        $today = new DateTime(date('Y-m-d'));
        $deliveryDate = new DateTime($deliveryTerjauh); // Tanggal delivery terjauh
        $diff = $today->diff($deliveryDate);
        $hari = $diff->days - 7;

        $deliveryMax = $data[$indexMax]['delivery'];
        $tglDeliv = new DateTime($deliveryMax); // Tanggal delivery terjauh
        $beda = $today->diff($tglDeliv);
        $hariTarget = $beda->days - 7;
        $smvArray = array_column($data, 'smv');
        $smvArray = array_map('intval', $smvArray);
        $averageSmv = array_sum($smvArray) / count($smvArray);

        $pdk = $data[$indexMax]['mastermodel'];
        $order = [
            'mastermodel' => $pdk,
            'sisa' => $totalQty,
            'delivery' => $deliveryTerjauh,
            'targetHari' => $hariTarget,
            'smv' => $averageSmv
        ];
        return $order;
    }
    public function getIdBs($validate)
    {
        return $this->select('idapsperstyle, delivery, sisa')
            ->where('mastermodel', $validate['no_model'])
            ->where('size', $validate['style'])
            ->first();
    }
    public function getSisaPerJarum($model, $tanggal)
    {
        return $this->select('sum(sisa) as sisa, machinetypeid, mastermodel')
            ->where('delivery', $tanggal)
            ->where('mastermodel', $model)
            ->where('sisa >', 0)
            ->groupby('machinetypeid')
            ->findAll();
    }
    public function getSisaPerDeliv($model, $jarum)
    {
        return $this->select('sum(sisa) as sisa,sum(qty) as qty, delivery, mastermodel,smv')
            ->where('machinetypeid', $jarum)
            ->where('mastermodel', $model)
            ->where('sisa >=', 0)
            ->groupby('delivery')
            ->findAll();
    }
    public function getSisaPerDlv($model, $jarum, $deliv)
    {
        $sisa = $this->select('idapsperstyle,mastermodel,size,sum(qty) as qty,sum(sisa) as sisa,factory, production_unit, delivery,smv')
            ->where('machinetypeid', $jarum)
            ->where('mastermodel', $model)
            ->where('delivery', $deliv)
            ->where('sisa >=', 0)
            ->groupBy('size')
            ->findAll();
        $final = reset($sisa);
        return $sisa;
    }
    public function getSisaOrderforRec($jarum, $start, $stop)
    {
        $maxDeliv = date('Y-m-d', strtotime($start . '+90 Days'));
        return $this->select('idapsperstyle,sum(sisa) as sisa, machinetypeid, mastermodel,factory, delivery')
            ->where('delivery >', $start)
            ->where('machinetypeid', $jarum)
            ->where('sisa >', 0)
            ->where('factory !=', 'Belum Ada Area')
            ->groupby('machinetypeid,factory')
            ->findAll();
    }
    public function getIdForBs($validate)
    {
        $data = $this->select('idapsperstyle, delivery, sisa,qty')
            ->where('mastermodel', $validate['no_model'])
            ->where('size', $validate['style'])
            ->where('factory', $validate['area'])
            ->where('qty != sisa')
            ->orderBy('sisa', 'ASC') // Mengurutkan berdasarkan 'sisa' dari yang terkecil
            ->first(); // Mengambil data pertama (yang terkecil)

        return $data;
    }
    public function getStyle($pdk)
    {
        return $this->select('idapsperstyle, mastermodel, size,sum(qty) as qty')
            ->where('mastermodel', $pdk)
            ->groupBy('size')
            ->findAll();
    }

    public function ambilSisaOrder($ar, $bulan, $jarum)
    {
        $todayDate = new DateTime(); // Current date
        $ld = (clone $todayDate)->modify('+90 days')->format('Y-m-d');

        $data = $this->select('mastermodel, delivery, SUM(sisa) AS sisa, smv, factory, machinetypeid')
            ->where('machinetypeid', $jarum)
            ->where('sisa >', '0')
            ->where('delivery >', $bulan)
            ->where('delivery <', $ld)
            ->where('factory', $ar)
            ->groupBy('smv,delivery,machinetypeid, factory, mastermodel')
            ->findAll();
        $totalKebMesin = 0;
        $outputDz = 0;
        foreach ($data as $dt) {
            $delivDate = new DateTime($dt['delivery']);
            $leadtime = $delivDate->diff($todayDate)->days;
            $smv = intval($dt['smv']) ?? 185;
            $sisa = round($dt['sisa'] / 24);
            if ($leadtime > 0) {
                $target = round((86400 / $smv) * 0.85 / 24); // Simplified target calculation
                $kebMesin = $sisa / $target / $leadtime;

                $kebutuhanMc = ceil($kebMesin);
                $dz = $kebutuhanMc * $target;

                $outputDz += $dz;
                $totalKebMesin += $kebutuhanMc;
            } else {
                continue; // Skip this iteration if either is zero
            }
        }

        return [
            'totalKebMesin' => $totalKebMesin,
            'outputDz' => $outputDz
        ];
    }

    public function getTotalOrderWeek($cek)
    {
        return $this->select('machinetypeid, SUM(qty) AS qty, SUM(sisa) AS sisa, delivery')
            ->where('factory!=', ['MJ'])
            ->where('sisa>', '0')
            ->where('machinetypeid', $cek['jarum'])
            ->where('delivery>=', $cek['start'])
            ->where('delivery<=', $cek['end'])
            ->groupBy('machinetypeid')
            ->findAll();
    }
    public function getIdByDeliv($pdk, $size, $deliv)
    {
        return $this->select('idapsperstyle')
            ->where('mastermodel', $pdk)
            ->where('size', $size)
            ->findAll();
    }
    public function rekomenarea($noModel, $jarum)
    {
        $this->select('size, delivery, smv, SUM(sisa) AS sisa, size, 
        DATEDIFF(DATE_SUB(delivery, INTERVAL 7 DAY), DATE_ADD(CURDATE(), INTERVAL 7 DAY)) - 
        (SELECT COUNT(tanggal) FROM data_libur WHERE tanggal BETWEEN DATE_ADD(CURDATE(), INTERVAL 7 DAY) AND DATE_SUB(apsperstyle.delivery, INTERVAL 7 DAY)) AS totalhari');
        $this->where('machinetypeid', $jarum)
            ->where('mastermodel', $noModel)
            ->where('production_unit !=', 'MAJALAYA');
        $this->where('sisa >', 0);
        $this->groupBy('smv, mastermodel, delivery');
        $this->orderBy('delivery');

        return $this->get()->getResultArray();
    }
    public function resetSisaDlv($update)
    {
        $sisa = 0;
        $this->set('sisa', $sisa)
            ->where('mastermodel', $update['mastermodel'])
            ->where('delivery', $update['delivery'])
            ->where('size', $update['size'])
            ->update();
        return $this->affectedRows();
    }
    public function getIdPerDeliv($update)
    {
        return $this->select('idapsperstyle')
            ->where('mastermodel', $update['mastermodel'])
            ->where('delivery', $update['delivery'])
            ->where('size', $update['size'])
            ->first();
    }
    public function totalPo($model)
    {
        $po = $this->select('sum(qty) as totalPo')
            ->where('mastermodel', $model)
            ->findAll();
        $order = reset($po);
        return $order;
    }
}
