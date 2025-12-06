<?php

namespace App\Models;

use DateTime;
use CodeIgniter\Model;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Sum;
use PHPUnit\TextUI\XmlConfiguration\Group;

class ApsPerstyleModel extends Model
{
    protected $table            = 'apsperstyle';
    protected $primaryKey       = 'idapsperstyle';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['idapsperstyle', 'machinetypeid', 'mastermodel', 'size', 'delivery', 'qty', 'sisa', 'seam', 'process_routes', 'factory', 'production_unit', 'smv', 'no_order', 'country', 'color', 'po_plus', 'inisial'];

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
        $year = date('Y');
        return $this->join('data_model', 'data_model.no_model = apsperstyle.mastermodel')
            ->select('data_model.created_at, SUM(apsperstyle.qty) as total_produksi')
            ->where('MONTH(data_model.created_at)', $bulan)
            ->where('YEAR(data_model.created_at)', $year)
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
            ->where('sisa >=', 0)
            ->where('qty >', 0)
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
            ->set('production_unit', $data['pu'])
            ->where('delivery', $data['delivery'])
            ->where('mastermodel', $data['mastermodel'])
            ->where('machinetypeid', $data['jarum'])
            ->update();

        return $this->affectedRows();
    }

    public function asignarealall($data)
    {
        $this->set('factory', $data['area'])
            ->set('production_unit', $data['pu'])
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
        $this->select('mastermodel,size, delivery, SUM(sisa) AS sisa,size, round(AVG(smv)) as smv, 
       DATEDIFF(delivery, CURDATE()) - 
       (SELECT COUNT(tanggal) FROM data_libur WHERE tanggal BETWEEN CURDATE() AND apsperstyle.delivery)-3 AS totalhari,');
        $this->where('machinetypeid', $cek['jarum'])
            ->where('delivery >=', $cek['start'])
            ->where('delivery <=', $cek['end'])
            ->where('production_unit !=', 'MAJALAYA');
        $this->where('sisa >', 0);
        $this->where('qty >', 0);
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
    public function getProgressperArea($area)
    {

        // Ambil data dari database, termasuk semua delivery dates yang relevan
        $res = $this->select('mastermodel, delivery, SUM(qty/24) as target, SUM(sisa/24) as remain, factory')
            ->where('factory', $area)
            ->groupBy(['delivery', 'mastermodel']) // Group by mastermodel dan delivery
            ->get()
            ->getResultArray();

        return $res;
    }


    public function getIdMinus($validate)
    {
        return $this->select('idapsperstyle, delivery, sisa')
            ->where('mastermodel', $validate['no_model'])
            ->where('sisa <=', 0)
            ->where('qty >', 0)
            ->where('size', $validate['style'])
            ->first();
    }

    public function getIdProd($validate)
    {
        return $this->select('idapsperstyle, delivery, sisa')
            ->where('mastermodel', $validate['no_model'])
            ->where('sisa >', 0)
            ->where('qty >', 0)
            ->where('size', $validate['style'])
            ->first();
    }

    public function getIdBawahnya($validate)
    {
        return $this->select('idapsperstyle, delivery, sisa')
            ->where('mastermodel', $validate['no_model'])
            ->where('sisa >', $validate['sisa'])
            ->where('qty >', 0)
            ->where('size', $validate['style'])
            ->first();
    }
    public function getDetailPlanning($area, $jarum) // funtion ieu kudu diganti where na kade ulah poho
    {
        return $this->select('mastermodel AS model, SUM(qty/24) AS qty, SUM(sisa/24) AS sisa, AVG(smv) AS smv, machinetypeid')
            ->where('factory', $area)
            ->where('machinetypeid', $jarum)
            ->where('sisa >', 0)
            ->where('qty >', 0)
            ->where('delivery > NOW()', null, false) // Add 7 days to current date
            // ->where('delivery > DATE_ADD(NOW(), INTERVAL 7 DAY)', null, false) // Add 7 days to current date
            ->groupBy('mastermodel')
            ->findAll();
    }
    public function getDetailPlanningGloves($area, $jarum) // funtion ieu kudu diganti where na kade ulah poho
    {
        $rules = [
            'POM-POM' => ['POM-POM'],
            '240N' => ['240N', '240N-PL'],
            '240N-PL' => ['240N-PL'],
            '10G92N-PL' => ['10G92N-PL'],
            '10G92N-MT' => ['10G92N-MT', '10G92N-MTPL'],
            '10G92N-MTPL' => ['10G92N-MTPL'],
            '10G84N-MT' => ['10G84N-MT', '10G84N-MTPL'],
            '10G84N-MTPL' => ['10G84N-MTPL'],
            '10G144N' => ['10G144N'],
            '10G126N' => ['10G126N'],
            '10G126N' => ['10G126N'],
            '10G116N' => ['10G116N', '10G116N-PL'],
            '10G116N-PL' => ['10G116N-PL'],
            '10G116N-FL' => ['10G116N-FL', '10G116N-FLPL'],
            '10G116N-FLPL' => ['10G116N-FLPL'],
            '10G106N' => ['10G106N', '10G106N-PL'],
            '10G106N-PL' => ['10G106N-PL'],
            '10G106N-FL' => ['10G106N-FL', '10G106N-FLPL'],
            '10G106N-FLPL' => ['10G106N-FLPL'],
            '10G106N-MTPL' => ['10G106N-MTPL'],
        ];

        return $this->select('mastermodel AS model, SUM(qty)/24 AS qty, SUM(sisa)/24 AS sisa, AVG(smv) AS smv,machinetypeid')
            ->where('factory', $area)
            ->whereIn('machinetypeid', $rules[$jarum])
            ->where('sisa >', 0)
            ->where('qty >', 0)
            ->where('delivery > NOW()', null, false)
            ->groupBy('mastermodel')
            ->findAll();
    }
    public function getAllForModelStyleAndSize($validate)
    {
        return $this->where('mastermodel', $validate['no_model'])
            ->where('size', $validate['style'])
            ->where('factory', $validate['area'])
            ->where('qty >', 0)
            ->orderBy('delivery', 'ASC') // Optional: sort berdasarkan delivery date, bisa diubah sesuai kebutuhan
            ->findAll();
    }

    public function getSmv()
    {
        $monthago = date('Y-m-d', strtotime('10 days ago')); // Menggunakan format tanggal yang benar
        return $this->select('idapsperstyle,mastermodel,size,smv')
            ->where('delivery >', $monthago) // Perbaiki spasi di operator where
            ->groupBy(['size', 'mastermodel']) // Menggunakan array untuk groupBy
            ->orderBy('delivery')
            ->findAll();
    }

    public function getPdkProduksi()
    {
        return $this->select('mastermodel, inisial, sum(qty) as totalqty, sum(sisa) as totalsisa, sum(qty)-sum(sisa) as totalproduksi')
            ->groupBy('mastermodel')
            ->findAll();
    }

    public function getInProduksi()
    {
        return $this->select('mastermodel, inisial, size, idapsperstyle')
            ->groupBy('mastermodel, inisial')
            ->findAll();
    }

    public function getSizeProduksi()
    {
        return $this->select('idapsperstyle, mastermodel, size, inisial')
            ->groupBy('mastermodel, size')
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
        $res = $this->select('sisa')
            ->where('idapsperstyle', $idaps)
            ->first();

        // kalau gak ada hasil, return 0 atau null sesuai kebutuhan
        if (!$res || !is_array($res)) {
            return 0; // atau bisa null kalau kamu mau
        }

        return $res['sisa'] ?? 0;
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
        $amonth = date('Y-m-d', strtotime('-30 Days'));
        $maxDeliv = date('Y-m-d', strtotime('+150 Days'));

        return $this->select('mastermodel')
            ->where('factory', $area)
            ->where('machinetypeid', $jarum)
            ->where('sisa >', 0)
            ->where('delivery <', $maxDeliv)
            ->where('delivery >', $amonth)
            ->groupBy('mastermodel')
            ->findAll();
    }
    public function CapacityArea($area, $jarum)
    {
        $amonth = date('Y-m-d', strtotime('-30 Days'));
        $maxDeliv = date('Y-m-d', strtotime('+150 Days'));
        $data = $this->select('mastermodel,round(sum(qty/24)) as qty, round(sum(sisa/24 ))as sisa,delivery,smv')
            ->where('factory', $area)
            ->where('machinetypeid', $jarum)
            ->where('sisa >', 0)
            ->where('qty >', 0)
            ->where('delivery <', $maxDeliv)
            ->where('delivery >', $amonth)
            ->groupBy('delivery,mastermodel')
            ->get()
            ->getResultArray();
        // dd($data);
        $order = [];
        foreach ($data as $d) {
            $today = new DateTime(date('Y-m-d'));
            $deliveryDate = new DateTime($d['delivery']); // Tanggal delivery terjauh
            $diff = $today->diff($deliveryDate);
            $hari = $diff->days - 7;

            $tglDeliv = new DateTime($d['delivery']); // Tanggal delivery terjauh
            $beda = $today->diff($tglDeliv);
            $hariTarget = $beda->days;

            $order[] = [
                'mastermodel' => $d['mastermodel'],
                'sisa' => $d['sisa'],
                'qty' => $d['qty'],
                'delivery' => $d['delivery'],
                'targetHari' => $hariTarget,
                'smv' => $d['smv']
            ];
        }
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
            ->where('qty >', 0)
            ->groupby('machinetypeid')
            ->findAll();
    }
    public function getSisaPerDeliv($model, $jarum)
    {
        $result = $this->select('sum(sisa) as sisa,sum(qty) as qty, delivery, mastermodel,smv')
            ->where('machinetypeid', $jarum)
            ->where('mastermodel', $model)
            // ->where('sisa >=', 0)
            ->where('qty >', 0)
            ->groupby('delivery')
            ->findAll();
        return $result;
    }
    public function getSisaPerStyleArea($model, $area)
    {
        $result = $this->select('mastermodel,size,sum(qty) as qty,sum(sisa) as sisa,smv,inisial,color')
            ->where('mastermodel', $model)
            ->where('factory', $area)
            // ->where('sisa >=', 0)
            ->where('qty >', 0)
            ->groupby('size')
            ->findAll();
        return $result;
    }
    public function getSisaPerDlv($model, $jarum, $deliv)
    {
        $sisa = $this->select('idapsperstyle,inisial,mastermodel,size,sum(qty) as qty,sum(sisa) as sisa,factory, production_unit, delivery,smv,seam,country,no_order')
            ->where('machinetypeid', $jarum)
            ->where('mastermodel', $model)
            ->where('delivery', $deliv)
            // ->where('sisa >=', 0)
            ->where('qty >', 0)
            ->groupBy('size,factory')
            ->findAll();
        $final = reset($sisa);
        return $sisa;
    }
    public function getSisaPerStyle($model, $style)
    {
        $sisa = $this->select('idapsperstyle,inisial,mastermodel,machinetypeid,size,sum(qty) as qty,sum(sisa) as sisa,factory, production_unit, delivery,smv,apsperstyle.seam,country,no_order, data_model.kd_buyer_order as buyer, master_product_type.product_type')
            ->join('data_model', 'data_model.no_model = apsperstyle.mastermodel', 'left')
            ->join('master_product_type', 'master_product_type.id_product_type = data_model.id_product_type', 'left')
            ->where('mastermodel', $model)
            ->where('size', $style)
            // ->where('sisa >=', 0)
            ->where('qty >', 0)
            ->groupBy('machinetypeid,delivery')
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
            ->where('qty >', 0)
            ->where('factory !=', 'Belum Ada Area')
            ->groupby('machinetypeid,factory')
            ->findAll();
    }
    public function getIdForBs($validate)
    {
        $builder = $this->select('idapsperstyle, delivery, sisa,qty')
            ->where('mastermodel', $validate['no_model'])
            ->where('size', $validate['style'])
            ->where('factory', $validate['area'])
            ->where('qty >', 0)
            // ->where('qty != sisa')
            ->orderBy('sisa', 'ASC') // Mengurutkan berdasarkan 'sisa' dari yang terkecil
            ->first(); // Mengambil data pertama (yang terkecil)

        return $builder;
    }
    // public function getStyle($pdk)
    // {
    //     return $this->select('idapsperstyle, mastermodel, size,sum(qty) as qty, sum(po_plus) as po_plus, inisial')
    //         ->where('mastermodel', $pdk)
    //         ->groupBy('size')
    //         ->orderBy('inisial')
    //         ->findAll();
    // }

    public function getStyle($pdk)
    {
        // Subquery 1: total qty & po_plus per size (semua delivery)
        $subTotal = $this->db->table('apsperstyle')
            ->select('size, SUM(qty) AS qty, SUM(po_plus) AS po_plus')
            ->where('mastermodel', $pdk)
            ->groupBy('size');

        // Subquery 2: ambil idapsperstyle & delivery tertinggi per size
        $subMax = $this->db->table('apsperstyle')
            ->select('size, MAX(delivery) AS max_delivery')
            ->where('mastermodel', $pdk)
            ->groupBy('size');

        // Join subquery 2 ke tabel utama untuk ambil id dari delivery terakhir
        $latest = $this->db->table('apsperstyle a')
            ->select('a.idapsperstyle, a.mastermodel, a.size, a.inisial, a.delivery')
            ->join("({$subMax->getCompiledSelect()}) m", 'a.size = m.size AND a.delivery = m.max_delivery', 'inner', false)
            ->where('a.mastermodel', $pdk);

        // Join hasil total + hasil latest
        return $this->db->table("({$latest->getCompiledSelect()}) latest")
            ->select('latest.idapsperstyle, latest.mastermodel, latest.size, latest.inisial, latest.delivery, total.qty, total.po_plus')
            ->join("({$subTotal->getCompiledSelect()}) total", 'total.size = latest.size', 'inner', false)
            ->orderBy('latest.inisial')
            ->get()
            ->getResultArray();
    }

    public function ambilSisaOrder($ar, $bulan, $jarum)
    {
        $todayDate = new DateTime(); // Current date
        $ld = (clone $todayDate)->modify('+90 days')->format('Y-m-d');

        $data = $this->select('mastermodel, delivery, SUM(sisa) AS sisa, smv, factory, machinetypeid')
            ->where('machinetypeid', $jarum)
            ->where('sisa >', '0')
            ->where('qty >', '0')
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
        return $this->select('machinetypeid, SUM(qty) AS qty, SUM(IF(sisa > 0, sisa, 0)) AS sisa, delivery')
            ->where('production_unit', 'CJ')
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
            ->where('delivery', $deliv)
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
        $this->where('qty >', 0);
        $this->groupBy('smv, mastermodel, delivery');
        $this->orderBy('delivery');

        return $this->get()->getResultArray();
    }
    public function resetSisaDlv($update)
    {
        $sisa = 0;
        $this->set('sisa', $sisa)
            ->where('factory', $update['factory'])
            ->where('mastermodel', $update['mastermodel'])
            ->where('delivery', $update['delivery'])
            ->where('size', $update['size'])
            ->update();
        return $this->affectedRows();
    }
    public function getIdPerDeliv($update)
    {
        return $this->select('idapsperstyle')
            ->where('factory', $update['factory'])
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
    public function statusOrderSock($startDate, $endDate)
    {
        return $this->select('MONTHNAME(delivery) as month, sum(round(qty/24)) as qty, sum(round(sisa/24)) as sisa')
            ->where('sisa >', 0)
            ->where('qty >', 0)
            ->where('factory !=', 'KK8J')
            ->where('delivery >=', $startDate)
            ->where('delivery <=', $endDate)
            ->groupBy('MONTH(delivery)')
            ->orderBy('delivery', 'ASC')
            ->findAll();
    }
    public function statusOrderGloves($startDate, $endDate)
    {
        return $this->select('MONTHNAME(delivery) as month, sum(round(qty/24)) as qty, sum(round(sisa/24)) as sisa')
            ->where('sisa >', 0)
            ->where('qty >', 0)
            ->where('factory =', 'KK8J')
            ->where('delivery >=', $startDate)
            ->where('delivery <=', $endDate)
            ->groupBy('MONTH(delivery)')
            ->orderBy('delivery', 'ASC')
            ->findAll();
    }
    public function getProgressDetail($model, $area)
    {
        $res = $this->select('mastermodel, delivery, SUM(qty/24) as target, SUM(sisa/24) as remain, factory,machinetypeid')
            ->where('mastermodel', $model)
            ->where('factory', $area)
            ->groupBy(['delivery', 'machinetypeid'])
            ->get()
            ->getResultArray();

        return $res;
    }
    public function getProgresPerdeliv($model, $area, $jarum)
    {
        $res = $this->select('mastermodel, delivery, SUM(qty/24) as target, SUM(sisa/24) as remain, factory,machinetypeid')
            ->where('mastermodel', $model)
            ->where('factory', $area)
            ->where('machinetypeid', $jarum)
            ->groupBy(['delivery'])
            ->get()
            ->getResultArray();
        $result = [];
        foreach ($res as $val) {
            $produksi = $val['target'] - $val['remain'];
            $percent = 0;
            if ($produksi > 0) {
                $percent = round(($produksi / $val['target']) * 100);
            }
            $result[$val['delivery']] = [
                'mastermodel' => $model,
                'jarum' => $val['machinetypeid'],
                'target' => $val['target'],
                'remain' => $val['remain'],
                'delivery' => $val['delivery'],
                'percentage' => $percent,
            ];
        }

        return $result;
    }
    public function progressdetail($data)
    {
        $res = $this->select('mastermodel,size, delivery, SUM(qty/24) as target, SUM(sisa/24) as remain, factory,machinetypeid')
            ->where('mastermodel', $data['model'])
            ->where('factory', $data['area'])
            ->where('machinetypeid', $data['jarum'])
            ->where('delivery', $data['delivery'])
            ->groupBy(['size'])
            ->get()
            ->getResultArray();
        $result = [];
        foreach ($res as $val) {
            $produksi = $val['target'] - $val['remain'];
            $percent = 0;
            if ($produksi > 0) {
                $percent = round(($produksi / $val['target']) * 100);
            }
            $result[$val['size']] = [
                'mastermodel' => $data['model'],
                'jarum' => $val['machinetypeid'],
                'size' => $val['size'],
                'target' => $val['target'],
                'remain' => $val['remain'],
                'delivery' => $val['delivery'],
                'percentage' => $percent,
            ];
        }

        return $result;
    }
    public function getTotalConfirmByMonth($thisMonth)
    {
        return $this->select('SUM(qty) as qty_export')
            ->where('delivery >=', $thisMonth['start'])
            ->where('delivery <=', $thisMonth['end'])
            ->where('production_unit', 'CJ')
            ->first();
    }
    public function getTotalConfirmByMontLokal($thisMonth)
    {
        return $this->select('SUM(apsperstyle.qty) as qty_export')
            ->join('data_model', 'data_model.no_model=apsperstyle.mastermodel')
            ->where('apsperstyle.delivery >=', $thisMonth['start'])
            ->where('apsperstyle.delivery <=', $thisMonth['end'])
            ->where('data_model.kd_buyer_order', 'LOKAL')
            ->where('apsperstyle.production_unit', 'CJ')
            ->first();
    }
    public function getBuyerOrder($buyer, $bulan)
    {
        return $this->select('data_model.kd_buyer_order, idapsperstyle, machinetypeid, mastermodel, delivery, factory, production_unit, round(sum(qty)/24) as qty, round(sum(sisa)/24) as sisa, data_model.kd_buyer_order')
            ->join('data_model', 'data_model.no_model=apsperstyle.mastermodel')
            ->where('data_model.kd_buyer_order', $buyer)
            ->where('apsperstyle.production_unit !=', 'MJ')
            ->where('MONTH(apsperstyle.delivery)', date('m', strtotime($bulan))) // Filter bulan
            ->where('YEAR(apsperstyle.delivery)', date('Y', strtotime($bulan))) // Filter tahun
            ->groupBy('apsperstyle.mastermodel')
            ->groupBy('apsperstyle.machinetypeid')
            ->groupBy('apsperstyle.factory')
            ->groupBy('apsperstyle.delivery')
            ->orderBy('apsperstyle.mastermodel')
            ->orderBy('apsperstyle.machinetypeid')
            ->orderBy('apsperstyle.factory')
            ->orderBy('apsperstyle.delivery', 'ASC')
            ->findAll();
    }
    public function getBuyerOrderPejarum($buyer, $bulan)
    {
        return $this->select('data_model.kd_buyer_order, idapsperstyle, machinetypeid, apsperstyle.delivery, production_unit, round(sum(qty)/24) as qty, round(sum(sisa)/24) as sisa, data_model.kd_buyer_order')
            ->join('data_model', 'data_model.no_model=apsperstyle.mastermodel')
            ->where('data_model.kd_buyer_order', $buyer)
            ->where('apsperstyle.production_unit !=', 'MJ')
            ->where('MONTH(apsperstyle.delivery)', date('m', strtotime($bulan))) // Filter bulan
            ->where('YEAR(apsperstyle.delivery)', date('Y', strtotime($bulan))) // Filter tahun
            ->groupBy('apsperstyle.machinetypeid, delivery')
            ->orderBy('apsperstyle.machinetypeid')

            ->findAll();
    }
    public function getAreaOrder($ar, $bulan)
    {
        $builder = $this->select('data_model.kd_buyer_order, data_model.seam,data_model.repeat_from as repeat, apsperstyle.machinetypeid, apsperstyle.mastermodel, apsperstyle.delivery, apsperstyle.factory, apsperstyle.production_unit, round(sum(apsperstyle.qty)/24) as qty, round(sum(apsperstyle.sisa)/24) as sisa')
            ->join('data_model', 'data_model.no_model=apsperstyle.mastermodel');

        // Tambahkan kondisi WHERE hanya jika $ar tidak kosong
        if (!empty($ar)) {
            $builder->where('apsperstyle.factory', $ar);
        }
        $builder->where('apsperstyle.production_unit !=', 'MJ')
            ->where('apsperstyle.qty > 0')
            // ->where('apsperstyle.mastermodel', 'DA2549')
            ->where('MONTH(apsperstyle.delivery)', date('m', strtotime($bulan))) // Filter bulan
            ->where('YEAR(apsperstyle.delivery)', date('Y', strtotime($bulan))) // Filter tahun
            ->groupBy('apsperstyle.mastermodel')
            ->groupBy('apsperstyle.factory')
            ->groupBy('apsperstyle.machinetypeid')
            ->groupBy('apsperstyle.delivery')
            ->orderBy('apsperstyle.mastermodel')
            ->orderBy('apsperstyle.machinetypeid')
            ->orderBy('apsperstyle.factory')
            ->orderBy('apsperstyle.delivery');
        $result = $builder->findAll(); // Eksekusi query
        return $result;
    }
    public function getAreaOrderPejarum($ar, $bulan)
    {
        $builder = $this->select('data_model.kd_buyer_order, machinetypeid, delivery, production_unit, round(sum(qty)/24) as qty, round(sum(sisa)/24) as sisa, data_model.kd_buyer_order')
            ->join('data_model', 'data_model.no_model=apsperstyle.mastermodel');
        // Tambahkan kondisi WHERE hanya jika $ar tidak kosong
        if (!empty($ar)) {
            $builder->where('apsperstyle.factory', $ar);
        }
        $builder->where('apsperstyle.production_unit !=', 'MJ')
            ->where('MONTH(apsperstyle.delivery)', date('m', strtotime($bulan))) // Filter bulan
            ->where('YEAR(apsperstyle.delivery)', date('Y', strtotime($bulan))) // Filter tahun
            ->groupBy('apsperstyle.machinetypeid')
            ->groupBy('apsperstyle.delivery')
            ->orderBy('apsperstyle.machinetypeid')
            ->orderBy('apsperstyle.delivery');
        $result = $builder->findAll(); // Eksekusi query
        return $result;
    }
    public function setZero($nomodel)
    {
        return $this->where('mastermodel', $nomodel)
            ->set('qty', 0)
            ->update();
    }
    public function setSisaZero($nomodel)
    {
        return $this->where('mastermodel', $nomodel)
            ->where('qty', 0)
            ->set('sisa', 0)
            ->update();
    }
    public function StylePerDelive($model, $jarum)
    {
        // Ambil data sisa dan qty jika sisa >= 0 dan qty > 0
        $data = $this->select('SUM(sisa) as sisa, SUM(qty) as qty, delivery, mastermodel, smv')
            ->where('machinetypeid', $jarum)
            ->where('mastermodel', $model)
            ->where('sisa >=', 0)
            ->where('qty >', 0)
            ->groupBy('delivery')
            ->findAll();

        // Jika tidak ada data, ambil semua data (fallback)
        if (empty($data)) {
            $data = $this->select('SUM(sisa) as sisa, SUM(qty) as qty, delivery, mastermodel, smv')
                ->where('machinetypeid', $jarum)
                ->where('mastermodel', $model)
                ->groupBy('delivery')
                ->findAll();

            // Set nilai sisa dan qty jadi 0 secara eksplisit untuk tiap item
            foreach ($data as &$item) {
                $item['sisa'] = 0;
                $item['qty'] = 0;
            }
        }

        return $data;
    }

    public function StylePerDlv($model, $jarum, $deliv)
    {
        $sisa = $this->select('idapsperstyle,mastermodel,size,sum(qty) as qty,sum(sisa) as sisa,factory, production_unit, delivery,smv,seam,country,no_order,inisial')
            ->where('machinetypeid', $jarum)
            ->where('mastermodel', $model)
            ->where('delivery', $deliv)
            ->where('qty >', 0)
            ->groupBy('size,factory')
            ->findAll();
        $final = reset($sisa);
        return $sisa;
    }
    public function updateInisial($data)
    {
        $result = $this->set('inisial', $data['inisial'])
            ->where('mastermodel', $data['pdk'])
            ->where('size', $data['size'])
            ->update();
        return $result;
    }
    public function
    getDetailPerDeliv($pdk, $area)
    {
        return $this->select('mastermodel, delivery')
            ->where('mastermodel', $pdk['model'])
            ->where('machinetypeid', $pdk['jarum'])
            ->where('factory', $area)
            ->where('qty>', 0)
            ->groupBy('delivery')
            ->findAll();
    }
    public function getSisaForPlanning($getData)
    {
        return $this->select('round(sum(qty/24)) as qty, round(sum(sisa/24)) as sisa, AVG(smv) as smv ')
            ->where('mastermodel', $getData['model'])
            ->where('delivery', $getData['delivery'])
            ->where('factory', $getData['area'])
            ->where('machinetypeid', $getData['jarum'])
            ->where('qty >', 0)
            ->groupBy('machinetypeid')
            ->findAll();
    }
    public function getAllSizes($area, $jarum, $pdk)
    {
        return $this->db->table('apsperstyle')
            ->select('size')
            ->where('factory', $area)
            ->where('machinetypeid', $jarum)
            ->where('mastermodel', $pdk)
            ->groupBy('size')
            ->get()
            ->getResultArray();
    }
    public function orderMaterial($nomodel, $size)
    {
        $qry = $this->select('mastermodel, size, factory, inisial, delivery,production_unit')
            ->where('mastermodel', $nomodel)
            ->where('size', $size)
            ->groupBy('delivery')
            ->findAll();

        if (!$qry) {
            return ['error' => 'Data not found'];  // Return an error message if no data found
        }

        if ($qry[0]['inisial'] === null && $qry[0]['factory'] === null) {
            return ['error' => 'Data not found'];  // Return error if both inisial and factory are null
        }

        // Check if there's only one delivery
        if (count($qry) === 1) {
            $minDeliv = $maxDeliv = $qry[0]['delivery'];
        } else {
            // Extract all 'delivery' values if there are multiple
            $deliveries = array_column($qry, 'delivery');
            $maxDeliv = max($deliveries);
            $minDeliv = min($deliveries);
        }

        $result = [
            'no_model' => $qry[0]['mastermodel'],
            'size' => $qry[0]['size'],
            'inisial' => $qry[0]['inisial'],
            'area' => $qry[0]['factory'],
            'delivery_awal' => $minDeliv,
            'delivery_akhir' => $maxDeliv
        ];

        return $result;
    }
    public function dataEstimasi($area, $lastmonth)
    {
        $model = $this->select('mastermodel, inisial, size')
            ->where('factory', $area)
            ->where('qty >', 0)
            ->where('delivery >', $lastmonth)
            ->groupBy('mastermodel,size')
            ->orderBy('delivery', 'DESC')
            ->findAll();
        $data = [];
        foreach ($model as $pdk) {
            $data[] = $this->select('mastermodel, inisial, size, SUM(sisa) AS sisa, sum(qty) as qty,sum(po_plus) as poplus, delivery,machinetypeid')
                ->where('mastermodel', $pdk['mastermodel'])
                ->where('size', $pdk['size'])
                ->groupBy('mastermodel,size')
                ->orderBy('delivery', 'DESC')
                ->first();
        }
        $result = [];
        foreach ($data as $dt) {
            $qty = $dt['qty'];
            $sisa = $dt['sisa'];
            $prod = $qty - $sisa;
            $percent = $prod / $qty * 100;
            if ($percent > 65 && $percent < 98) {
                $result[] = $dt;
            }
        }

        return $result;
    }


    public function getSisaPerModel($model, $jarum, $area)
    {
        $result = $this->select('sum(qty/24) as qty, sum(sisa/24) as sisa, delivery')
            ->where('mastermodel', $model)
            ->where('machinetypeid', $jarum)
            ->where('factory', $area)
            ->where('qty >', 0)
            ->groupBy('machinetypeid')
            ->orderBy('delivery', 'asc')
            ->first();
        if (!$result) {
            $result = [
                'qty' => 0,
                'sisa' => 0,
                'delivery' => "0000-00-00"
            ];
        }
        return $result;
    }
    public function getQtyCancel($idaps)
    {
        return $this->select('idapsperstyle, qty')
            ->whereIn('idapsperstyle', (array) $idaps)
            ->findAll();
    }
    public function getStyleSize($noModel)
    {
        return $this->select('size,inisial')
            ->where('mastermodel', $noModel)
            ->groupBy('size')
            ->orderBy('size', 'ASC')
            ->findAll();
    }
    public function
    getMonthlyData()
    {
        $db = db_connect();
        $query = $db->query("
            SELECT 
                DATE_FORMAT(delivery, '%M') AS month_name, 
                YEAR(delivery) AS year,
                round(SUM(qty/24)) AS total_qty, 
                round(SUM(sisa/24)) AS total_sisa
            FROM apsperstyle
            WHERE YEAR(delivery) = YEAR(CURDATE()) 
            AND production_unit !='MJ'
            GROUP BY DATE_FORMAT(delivery, '%Y-%m')
            ORDER BY MIN(delivery) ASC
        ");

        $result = $query->getResultArray();

        $data = [];
        foreach ($result as $row) {
            $data[$row['month_name'] . ' ' . $row['year']] = [
                'qty' => (int) $row['total_qty'],
                'sisa' => (int) $row['total_sisa']
            ];
        }

        return $data;
    }
    public function getMonthName()
    {
        $db = db_connect();
        $query = $db->query("
        SELECT 
            DATE_FORMAT(delivery, '%M') AS month_name, 
            YEAR(delivery) AS year
        FROM apsperstyle
        WHERE production_unit != 'MJ'
        AND DATE_FORMAT(delivery, '%Y-%m') >= DATE_FORMAT(CURDATE(), '%Y-%m')
        GROUP BY DATE_FORMAT(delivery, '%Y-%m')
        ORDER BY MIN(delivery) ASC
        ");

        $result = $query->getResultArray();
        $data = [];

        foreach ($result as $row) {
            $data[] = [
                'bulan' => $row['month_name'],
                'tahun' => $row['year']
            ]; // Simpan nilai ke dalam array
        }
        return $data;
    }
    public function exportDataEstimasi($data)
    {
        $data = $this->select('mastermodel, inisial, size, SUM(sisa) AS sisa, sum(qty) as qty,sum(po_plus) as poplus, delivery,machinetypeid')
            ->where('factory', $data['area'])
            ->where('mastermodel', $data['model'])
            ->where('size', $data['size'])
            ->groupBy('mastermodel, size')
            ->orderBy('delivery', 'DESC');

        $query = $data->get()->getFirstRow('array');
        return $query;
    }
    public function getIdApsForPph($area, $no_model, $size)
    {
        $builder = $this->select('idapsperstyle')
            ->where('mastermodel', $no_model)
            ->where('size', $size);

        if (!empty($area)) {
            $builder->where('factory', $area);
        }

        return $builder->groupBy('idapsperstyle')
            ->findAll();
    }

    public function monthlyTarget($filters)
    {
        $builder = $this->select('SUM(qty) as qty, SUM(sisa) as sisa')
            ->where('production_unit !=', 'MJ')
            ->where('qty >', 0);

        if (!empty($filters['bulan'])) {
            $builder->where('MONTH(delivery)', $filters['bulan']);
        }
        if (!empty($filters['area'])) {
            $builder->where('factory', $filters['area']);
        }

        return $builder->first();
    }

    public function getAreasByNoModel($nomodel)
    {
        return $this->select('factory')
            ->where('mastermodel', $nomodel)
            ->groupBy('factory')
            ->findAll();
    }
    public function getSizesByNoModelAndArea($nomodel, $area)
    {
        return $this->select('size, inisial')
            ->where('mastermodel', $nomodel)
            ->where('factory', $area)
            ->groupBy('size')
            ->findAll();
    }
    public function getPlanStyle($area, $pdk, $jarum)
    {
        $builder = $this->select('idapsperstyle,inisial, size, sum(qty) as qty, sum(sisa) as sisa, color')
            ->where('mastermodel', $pdk)
            ->where('machinetypeid', $jarum)
            ->where('qty >', 0);
        if (!empty($area)) {
            $builder->where('factory', $area);
        }
        $builder->groupBy('size,machinetypeid');
        return $builder->findAll();
    }
    public function getSizes($nomodel, $inisial)
    {
        return $this->select('size')
            ->where('mastermodel', $nomodel)
            ->where('inisial', $inisial)
            ->groupBy('size')
            ->first();
    }
    public function getQtyOrder($noModel, $styleSize, $area)
    {
        return $this->select('SUM(qty) AS qty, inisial, sum(po_plus) as po_plus')
            ->where('mastermodel', $noModel)
            ->where('size', $styleSize)
            ->where('factory', $area)
            ->first();
    }
    public function gantiJarum($pdk, $sz, $jarumOld, $jarumnew)
    {
        return $this->where('mastermodel', $pdk)
            ->where('size', $sz)
            ->where('machinetypeid', $jarumOld)
            ->set('machinetypeid', $jarumnew)
            ->update();
    }
    public function getModelArea($area)
    {
        return $this->select('mastermodel')
            ->where('factory', $area)
            ->groupBy('mastermodel')
            ->findAll();
    }
    public function getSisaPerSize($area, $nomodel, $size)
    {
        return $this->select('sum(qty) as qty, sum(sisa) as sisa, sum(po_plus) as po_plus')
            ->where('factory', $area)
            ->where('mastermodel', $nomodel)
            ->whereIn('size', $size)
            ->where('qty >', 0)
            ->first(); // Ambil satu hasil
    }

    public function getApsPerStyle($nomodel, $size, $area)
    {
        return $this->select('idapsperstyle, mastermodel, size, inisial, delivery, factory')
            ->where('mastermodel', $nomodel)
            ->where('size', $size)
            ->where('factory', $area)
            ->findAll();
    }

    public function getApsPerStyleById($id)
    {
        return $this->select('idapsperstyle, mastermodel, size, inisial, delivery, factory')
            ->where('idapsperstyle', $id)
            ->findAll();
    }
    public function getDataOrder($validate)
    {
        $builder = $this->select('apsperstyle.*, SUM(apsperstyle.qty) AS qty_pcs, SUM(apsperstyle.sisa) AS sisa_pcs, data_model.kd_buyer_order, data_model.description, data_model.repeat_from, data_model.created_at, master_product_type.product_type, apsperstyle.smv')
            ->join('data_model', 'apsperstyle.mastermodel =  data_model.no_model', 'left')
            ->join('master_product_type', 'master_product_type.id_product_type =  data_model.id_product_type', 'left')
            ->where('apsperstyle.qty != 0');

        if (!empty($validate['buyer'])) {
            $builder->where('data_model.kd_buyer_order', $validate['buyer']);
        }

        if (!empty($validate['area'])) {
            $builder->where('apsperstyle.factory', $validate['area']);
        }

        if (!empty($validate['jarum'])) {
            $builder->like('apsperstyle.machinetypeid', $validate['jarum']);
        }

        if (!empty($validate['pdk'])) {
            $builder->where('data_model.no_model', $validate['pdk']);
        }

        if (!empty($validate['seam'])) {
            $builder->like('apsperstyle.seam', $validate['seam']);
        }
        if (!empty($validate['process_routes'])) {
            $builder->like('apsperstyle.process_routes', $validate['process_routes']);
        }

        if (!empty($validate['tglTurun']) && !empty($validate['tglTurunAkhir'])) {
            $builder->where('data_model.created_at >=', $validate['tglTurun']);
            $builder->where('data_model.created_at <=', $validate['tglTurunAkhir']);
        }
        if (!empty($validate['tglTurun']) && empty($validate['tglTurunAkhir'])) {
            $builder->where('data_model.created_at', $validate['tglTurun']);
        }
        if (empty($validate['tglTurun']) && !empty($validate['tglTurunAkhir'])) {
            $builder->where('data_model.created_at', $validate['tglTurunAkhir']);
        }

        if (!empty($validate['awal'])) {
            $builder->where('apsperstyle.delivery >=', $validate['awal']);
        }

        if (!empty($validate['akhir'])) {
            $builder->where('apsperstyle.delivery <=', $validate['akhir']);
        }
        return $builder
            ->groupBy('apsperstyle.mastermodel')
            ->groupBy('apsperstyle.machinetypeid')
            ->groupBy('apsperstyle.delivery')
            ->groupBy('apsperstyle.size')
            ->groupBy('apsperstyle.factory')
            ->orderBy('apsperstyle.mastermodel, apsperstyle.size, apsperstyle.machinetypeid, apsperstyle.delivery', 'ASC')
            ->findAll();
    }
    public function getQtyPcsByAreaByStyle($data)
    {
        return $this->select('SUM(qty) AS qty')
            ->where('factory', $data['area'])
            ->where('mastermodel', $data['noModel'])
            ->where('size', $data['styleSize'])
            ->first();
    }

    public function getMasterModel()
    {
        return $this->select('mastermodel')
            ->groupBy('mastermodel')
            // 3 bulan sebelumnya dan 3 bulan ke depan
            ->where('delivery >=', date('Y-m-d', strtotime('-3 months')))
            ->where('delivery <=', date('Y-m-d', strtotime('+3 months')))
            ->findAll();
    }

    public function getInisialByModel($model)
    {
        return $this->select('inisial')
            ->where('mastermodel', $model)
            ->groupBy('inisial')
            ->where('delivery >=', date('Y-m-d', strtotime('-3 months')))
            ->where('delivery <=', date('Y-m-d', strtotime('+3 months')))
            ->findAll();
    }

    public function getIdApsByModelInisial($model, $inisial)
    {
        return $this->select('idapsperstyle')
            ->where('mastermodel', $model)
            ->where('inisial', $inisial)
            ->findAll();
    }
    public function getDeliv($model)
    {
        return $this->select('delivery')
            ->where('mastermodel', $model)
            ->groupBy('delivery')
            ->findAll();
    }
    public function getDeliveryAwalAkhir($model)
    {
        return $this->select('MIN(delivery) AS delivery_awal, MAX(delivery) AS delivery_akhir,production_unit as unit')
            ->where('mastermodel', $model)
            ->first();
    }

    public function searchApsPerStyleByMastermodel($mastermodel)
    {
        return $this->select('idapsperstyle, mastermodel, size, inisial, delivery, factory')
            ->like('mastermodel', $mastermodel)
            ->groupBy('mastermodel, size, inisial, delivery, factory')
            ->findAll();
    }

    public function getQtyArea($model)
    {
        $result = $this->select(' mastermodel,size,factory as area,delivery,sum(qty) as qty,sum(sisa) as sisa')
            ->where('mastermodel', $model)
            ->where('sisa >=', 0)
            ->where('qty >=', 0)
            // ->groupby('size,factory,delivery')
            ->groupby('delivery,factory,size')
            ->findAll();
        return $result;
    }
    public function smvPerSize($pdk, $sz)
    {
        return $this->select('smv')->where('mastermodel', $pdk)->where('size', $sz)->first();
    }

    public function getDataModel($area, $pdk)
    {
        return $this->select('data_model.no_model, data_model.kd_buyer_order, apsperstyle.delivery, SUM(apsperstyle.qty) AS qty, apsperstyle.size, apsperstyle.smv, apsperstyle.machinetypeid,apsperstyle.no_order, master_product_type.product_type')
            ->join('data_model', 'apsperstyle.mastermodel = data_model.no_model')
            ->join('master_product_type', 'data_model.id_product_type = master_product_type.id_product_type')
            ->where('apsperstyle.factory', $area)
            ->where('apsperstyle.qty >', 0)
            ->where('data_model.no_model', $pdk)
            ->groupBy('apsperstyle.size, apsperstyle.delivery')
            ->orderBy('apsperstyle.size, apsperstyle.delivery', 'ASC')
            ->findAll();
    }

    public function getIdApsForFlowProses($noModel, $needle)
    {
        return $this->select('idapsperstyle,mastermodel, size, inisial, factory, delivery, machinetypeid')
            ->where('mastermodel', $noModel)
            ->where('machinetypeid', $needle)
            ->groupBy('size')
            ->findAll();
    }

    public function getPembagianModel($noModel)
    {
        return $this->select('mastermodel, SUM(qty) AS qty, size, machinetypeid, factory, color')
            ->where('qty >', 0)
            ->where('mastermodel', $noModel)
            ->groupBy('factory, size')
            ->orderBy('factory, size', 'ASC')
            ->findAll();
    }

    public function getNoModel()
    {
        return $this->select('mastermodel, factory')
            ->where('qty >', 0)
            ->groupBy('mastermodel, factory')
            ->orderBy('mastermodel, factory')
            ->findAll();
    }

    public function updateQtyStok($data)
    {
        return $this->where('mastermodel', $data['mastermodel'])
            ->where('size', $data['size'])
            ->where('delivery', $data['delivery'])
            ->set('qty', $data['qty_akhir'])
            ->set('sisa', $data['qty_akhir'])
            ->update();
    }
    public function tampilPerjarumBulan($bulan, $tahun, $jarum)
    {
        $month = date('m', strtotime($bulan));
        return $this->select(' round(SUM(apsperstyle.qty/24)) as qty, round(SUM(apsperstyle.sisa/24)) as sisa, apsperstyle.delivery, apsperstyle.no_order, apsperstyle.machinetypeid, ')
            // ->join('data_model', 'data_model.no_model = apsperstyle.mastermodel', 'left')
            // ->join('master_product_type', 'master_product_type.id_product_type = data_model.id_product_type', 'left')
            ->where('YEAR(delivery)', $tahun)
            ->where('MONTH(delivery)', $month)
            ->where('machinetypeid', $jarum)
            ->groupBy('machinetypeid, delivery, mastermodel')
            ->get()->getResult();
    }

    public function getQtyBySizes(string $noModel, string $area, array $sizes): array
    {
        if (empty($sizes)) return [];
        return $this->select('size, SUM(qty) AS qty, SUM(po_plus) AS po_plus, MIN(inisial) AS inisial')
            ->where('mastermodel', $noModel)
            ->where('factory', $area)
            ->whereIn('size', $sizes)
            ->groupBy('size')
            ->findAll();
    }

    public function getQtyAllSizes(string $noModel, string $area): array
    {
        return $this->select('size, SUM(qty) AS qty, SUM(po_plus) AS po_plus, MIN(inisial) AS inisial')
            ->where('mastermodel', $noModel)
            ->where('factory', $area)
            ->groupBy('size')
            ->findAll();
    }
    public function getTotalOrderMonth($month)
    {
        return $this->select('
            SUM(qty/24) AS qty, 
            SUM(CASE WHEN sisa > 0 THEN sisa/24 ELSE 0 END) AS sisa
        ')
            ->where('production_unit !=', 'MJ')
            ->where("DATE_FORMAT(delivery, '%Y-%m')", $month)
            ->first() ?? ['qty' => 0, 'sisa' => 0];
    }
    public function getFilterArea($model)
    {
        return $this->select('factory AS area')
            ->where('mastermodel', $model)
            ->groupBy('factory')
            ->findAll();
    }
    public function getDataSmv($mastermodels, $sizes)
    {
        $data = $this->select('machinetypeid,mastermodel, size, smv')
            ->whereIn('mastermodel', $mastermodels)
            // ->whereIn('size', $sizes)
            ->findAll();

        return $data;
    }
    public function getPpsData($pdk, $area)
    {
        if (empty($pdk)) {
            return [];
        }
        $builder = $this->select('
        apsperstyle.idapsperstyle,
        apsperstyle.machinetypeid,
        apsperstyle.mastermodel,
        apsperstyle.size,
        apsperstyle.inisial,
        apsperstyle.color,
        apsperstyle.qty,
        apsperstyle.sisa,
        apsperstyle.factory,
        mesin_perinisial.*, pps.id_pps,
  pps.pps_status, pps.mechanic, pps.notes,pps.coor,pps.start_pps_act,pps.stop_pps_act,pps.acc_qad,pps.acc_mr,pps.acc_fu,
  pps.history,
        data_model.start_mc,
        data_model.repeat_from as repeat
    ')
            ->join('mesin_perinisial', 'mesin_perinisial.idapsperstyle = apsperstyle.idapsperstyle', 'left')
            ->join('pps', 'pps.id_mesin_perinisial = mesin_perinisial.id_mesin_perinisial', 'left')
            ->join('data_model', 'data_model.no_model = apsperstyle.mastermodel', 'left')
            ->where('apsperstyle.mastermodel', $pdk)
            ->where('apsperstyle.qty >', 0);
        if (!empty($area)) {
            $builder->where('apsperstyle.factory', $area);
        }
        $builder->groupBy('apsperstyle.size')
            ->groupBy('apsperstyle.factory');
        return $builder->findAll();
    }
    public function getPoPlusPacking($area)
    {
        $builder = $this->select('mastermodel AS model, size, SUM(po_plus) AS total_po_plus')
            ->where('apsperstyle.qty != 0');

        if (!empty($params['area'])) {
            $builder->where('apsperstyle.factory', $area);
        }

        // baru group by di akhir
        $builder->groupBy('apsperstyle.mastermodel, apsperstyle.size');

        return $builder->findAll();
    }
    public function getAllSisaPerSize($area, $noModels, $sizes)
    {
        $result = $this->select('factory, mastermodel as no_model, size, SUM(qty) as qty')
            ->where('factory', $area)
            ->whereIn('mastermodel', $noModels)
            ->whereIn('size', $sizes)
            ->where('qty >', 0)
            ->groupBy('factory, mastermodel, size')
            ->findAll();

        // bikin index biar cepat diakses
        $index = [];
        foreach ($result as $r) {
            $index[$r['no_model'] . '|' . $r['size']] = $r['qty'];
        }
        return $index;
    }
    public function getAllIdForBs($area, $noModels, $sizes)
    {
        $result = $this->select('idapsperstyle, mastermodel as no_model, size')
            ->where('factory', $area)
            ->whereIn('mastermodel', $noModels)
            ->whereIn('size', $sizes)
            ->where('qty >', 0)
            ->orderBy('sisa', 'ASC')
            ->findAll();

        $index = [];
        foreach ($result as $r) {
            $index[$r['no_model'] . '|' . $r['size']][] = $r['idapsperstyle'];
        }
        return $index;
    }
    public function getSisaOrderAnomali()
    {
        return $this->select('mastermodel, factory, delivery, sum(qty) as qty, sum(sisa) as sisa')
            ->where('sisa > qty')
            ->where('qty >', 0)
            ->where('factory !=', 'mj')
            ->where('production_unit !=', 'mj')
            ->groupBy('mastermodel,factory,delivery')
            ->findAll();
    }
    public function getAllDataOrder($noModel)
    {
        return $this->select('data_model.kd_buyer_order, data_model.seam, data_model.description, apsperstyle.no_order, data_model.no_model, apsperstyle.factory, MIN(apsperstyle.delivery) AS delivery_awal, GROUP_CONCAT(DISTINCT apsperstyle.factory) AS list_factory, SUM(apsperstyle.qty) AS qty')
            ->join('data_model', 'data_model.no_model=apsperstyle.mastermodel')
            ->whereIn('apsperstyle.mastermodel', $noModel)
            ->where('apsperstyle.qty <>', 0)
            ->groupBy('apsperstyle.mastermodel')
            ->orderBy('data_model.kd_buyer_order, data_model.no_model', 'ASC')
            // ->limit(100)
            ->get()
            ->getResultArray();
    }
    public function getDetailOrder($noModel)
    {
        return $this->select('apsperstyle.machinetypeid, apsperstyle.mastermodel, apsperstyle.inisial, apsperstyle.size, apsperstyle.color, apsperstyle.delivery, apsperstyle.factory, SUM(apsperstyle.qty) AS qty, SUM(apsperstyle.sisa) AS sisa, SUM(apsperstyle.po_plus) AS po_plus')
            ->join('data_model', 'data_model.no_model=apsperstyle.mastermodel')
            ->where('apsperstyle.mastermodel', $noModel)
            ->where('apsperstyle.qty <>', 0)
            ->groupBy('apsperstyle.mastermodel')
            ->groupBy('apsperstyle.factory')
            ->groupBy('apsperstyle.machinetypeid')
            ->groupBy('apsperstyle.size')
            ->groupBy('apsperstyle.delivery')
            ->orderBy(' apsperstyle.delivery, apsperstyle.machinetypeid, apsperstyle.inisial, apsperstyle.factory', 'ASC')
            // ->limit(100)
            ->get()
            ->getResultArray();
    }
    public function getStatusOrder($noModel)
    {
        return $this->select('apsperstyle.idapsperstyle, apsperstyle.machinetypeid, apsperstyle.mastermodel, apsperstyle.inisial, apsperstyle.size, apsperstyle.color, apsperstyle.country, apsperstyle.delivery, apsperstyle.factory, apsperstyle.qty, apsperstyle.sisa, apsperstyle.po_plus')
            ->join('data_model', 'data_model.no_model=apsperstyle.mastermodel')
            ->where('apsperstyle.mastermodel', $noModel)
            ->where('apsperstyle.qty <>', 0)
            ->groupBy('apsperstyle.idapsperstyle')   // PENTING!
            ->orderBy('apsperstyle.delivery, apsperstyle.machinetypeid, apsperstyle.inisial, apsperstyle.factory', 'ASC')
            ->get()
            ->getResultArray();
    }
    public function getDataOrderFetch($listNoModel)
    {
        return $this->db->table('apsperstyle')
            ->select('idapsperstyle, inisial, size, mastermodel')
            ->whereIn('mastermodel', $listNoModel)
            ->get()
            ->getResultArray();
    }
}
