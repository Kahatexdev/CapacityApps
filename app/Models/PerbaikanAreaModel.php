<?php

namespace App\Models;

use CodeIgniter\Model;

class PerbaikanAreaModel extends Model
{
    protected $table            = 'perbaikan_area';
    protected $primaryKey       = 'id_perbaikan';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['idapsperstyle', 'no_label', 'area', 'tgl_perbaikan', 'no_box', 'no_mc', 'qty', 'kode_deffect', 'created_at', 'updated_at'];


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

    public function getDataPerbaikanFilter($theData)
    {
        $this->select('
        apsperstyle.idapsperstyle,
        data_model.kd_buyer_order,
        apsperstyle.mastermodel,
        apsperstyle.size,
        perbaikan_area.tgl_perbaikan,
        perbaikan_area.no_box,
        perbaikan_area.no_label,
        perbaikan_area.area,
        perbaikan_area.kode_deffect,
        master_deffect.Keterangan,
        SUM(perbaikan_area.qty) AS qty
    ')
            ->join('apsperstyle', 'apsperstyle.idapsperstyle = perbaikan_area.idapsperstyle')
            ->join('master_deffect', 'master_deffect.kode_deffect = perbaikan_area.kode_deffect')
            ->join('data_model', 'data_model.no_model = apsperstyle.mastermodel')
            ->where('tgl_perbaikan >=', $theData['awal'])
            ->where('tgl_perbaikan <=', $theData['akhir']);

        if (!empty($theData['pdk'])) {
            $this->where('apsperstyle.mastermodel', $theData['pdk']);
        }
        if (!empty($theData['area'])) {
            $this->where('perbaikan_area.area', $theData['area']);
        }
        if (!empty($theData['buyer'])) {
            $this->where('data_model.kd_buyer_order', $theData['buyer']);
        }

        $this->groupBy([
            'perbaikan_area.tgl_perbaikan',
            'perbaikan_area.no_box',
            'perbaikan_area.no_label',
            'perbaikan_area.area',
            'apsperstyle.size',
            'perbaikan_area.kode_deffect'
        ]);

        return $this->findAll();
    }
    public function getSummaryGlobalPerbaikan($theData)
    {
        $this->select('
            perbaikan_area.tgl_perbaikan,
            perbaikan_area.area,
            SUM(perbaikan_area.qty) AS qty
        ')
            ->join('apsperstyle', 'apsperstyle.idapsperstyle = perbaikan_area.idapsperstyle')
            ->join('master_deffect', 'master_deffect.kode_deffect = perbaikan_area.kode_deffect')
            ->join('data_model', 'data_model.no_model = apsperstyle.mastermodel');

        if (!empty($theData['bulan'])) {
            $this->where("DATE_FORMAT(perbaikan_area.tgl_perbaikan, '%Y-%m')", $theData['bulan']);
        }
        if (!empty($theData['area'])) {
            $this->where('perbaikan_area.area', $theData['area']);
        }
        $this->groupBy([
            'perbaikan_area.tgl_perbaikan',
            'perbaikan_area.area'
        ]);
        $this->orderBy('perbaikan_area.tgl_perbaikan', 'perbaikan_area.area');

        return $this->findAll();
    }
    public function  totalPerbaikan($theData)
    {

        $this->select('sum(perbaikan_area.qty) as qty')
            ->join('apsperstyle', 'apsperstyle.idapsperstyle = perbaikan_area.idapsperstyle')
            ->join('master_deffect', 'master_deffect.kode_deffect = perbaikan_area.kode_deffect')
            ->join('data_model', 'data_model.no_model = apsperstyle.mastermodel')
            ->where('tgl_perbaikan >=', $theData['awal'])
            ->where('tgl_perbaikan <=', $theData['akhir']);

        if (!empty($theData['pdk'])) {
            $this->where('apsperstyle.mastermodel', $theData['pdk']);
        }
        if (!empty($theData['area'])) {
            $this->where('area', $theData['area']);
        }
        if (!empty($theData['buyer'])) {
            $this->where('data_model.kd_buyer_order', $theData['buyer']);
        }

        $result = $this->first();
        return $result['qty'] ?? '0';
    }
    public function chartData($theData)
    {
        $this->select('master_deffect.Keterangan, sum(perbaikan_area.qty) as qty, ')
            ->join('apsperstyle', 'apsperstyle.idapsperstyle = perbaikan_area.idapsperstyle')
            ->join('master_deffect', 'master_deffect.kode_deffect = perbaikan_area.kode_deffect')
            ->join('data_model', 'data_model.no_model = apsperstyle.mastermodel')
            ->where('tgl_perbaikan >=', $theData['awal'])
            ->where('tgl_perbaikan <=', $theData['akhir']);

        if (!empty($theData['pdk'])) {
            $this->where('apsperstyle.mastermodel', $theData['pdk']);
        }
        if (!empty($theData['area'])) {
            $this->where('area', $theData['area']);
        }
        if (!empty($theData['buyer'])) {
            $this->where('data_model.kd_buyer_order', $theData['buyer']);
        }

        return $this->groupBy('Keterangan')
            ->orderBy('qty', 'DESC')
            ->findAll();
    }
    public function chartDataByMonth($bulan, $year, $area = null)
    {
        $builder = $this->select('master_deffect.Keterangan, sum(perbaikan_area.qty) as qty, ')
            ->join('apsperstyle', 'apsperstyle.idapsperstyle = perbaikan_area.idapsperstyle')
            ->join('master_deffect', 'master_deffect.kode_deffect = perbaikan_area.kode_deffect')
            ->join('data_model', 'data_model.no_model = apsperstyle.mastermodel')
            ->where('MONTH(tgl_perbaikan)', $bulan)
            ->where('YEAR(tgl_perbaikan)', $year);

        if (!empty($area)) {
            $builder->where('area', $area); // pastiin kolom `area` memang ada di tabel ini
        }

        return $this->groupBy('Keterangan')
            ->orderBy('qty', 'DESC')
            ->findAll();
    }
    public function getAllPB($idAps)
    {
        if (empty($idAps)) return [];
        $result = $this->select('idapsperstyle, SUM(qty) as qty')
            ->whereIn('idapsperstyle', $idAps)
            ->groupBy('idapsperstyle')
            ->findAll();

        $index = [];
        foreach ($result as $r) {
            $index[$r['idapsperstyle']] = $r['qty'];
        }
        return $index;
    }
    public function totalPerbulan($filters)
    {
        $builder = $this->select('SUM(qty) as qty');

        if (!empty($filters['bulan'])) {
            $builder->where('MONTH(tgl_perbaikan)', $filters['bulan']);
        }

        if (!empty($filters['tahun'])) {
            $builder->where('YEAR(tgl_perbaikan)', $filters['tahun']);
        }

        if (!empty($filters['area'])) {
            $builder->where('area', $filters['area']);
        }

        // Ambil hasil query, pastikan ambil baris pertama
        $result = $builder->findAll();

        // Akses nilai 'prod' dari baris pertama
        $qty = isset($result[0]['qty']) ? $result[0]['qty'] : 0; // Default ke 0 jika null

        return $qty;
    }
    public function getDataSummaryPerbaikan($theData)
    {
        // ambil data produksi dulu sesuai tgl yg di filter
        $prod = $this->db->table('produksi')
            ->select('SUM(produksi.qty_produksi/24) AS qty_prod, produksi.tgl_produksi')
            ->join('apsperstyle', 'produksi.idapsperstyle=apsperstyle.idapsperstyle')
            ->join('data_model', 'data_model.no_model=apsperstyle.mastermodel')
            ->where('produksi.tgl_produksi >=', $theData['awal'])
            ->where('produksi.tgl_produksi <=', $theData['akhir']);

        if (!empty($theData['pdk'])) {
            $prod->where('apsperstyle.mastermodel', $theData['pdk']);
        }
        if (!empty($theData['area'])) {
            $prod->where('produksi.area', $theData['area']);
        }
        if (!empty($theData['buyer'])) {
            $prod->where('data_model.kd_buyer_order', $theData['buyer']);
        }

        $prodduksi = $prod->groupBy('produksi.tgl_produksi')
            ->orderBy('produksi.tgl_produksi', 'ASC')
            ->get()
            ->getResultArray();


        // ambil data perbaikan area sesuai tgl produksi
        $pb = $this->db->table('perbaikan_area')
            ->select('SUM(perbaikan_area.qty/24) AS qty_pb, perbaikan_area.tgl_perbaikan')
            ->join('apsperstyle', 'perbaikan_area.idapsperstyle=apsperstyle.idapsperstyle')
            ->join('data_model', 'data_model.no_model=apsperstyle.mastermodel')
            ->where('perbaikan_area.tgl_perbaikan >=', $theData['awal'])
            ->where('perbaikan_area.tgl_perbaikan <=', $theData['akhir']);

        if (!empty($theData['pdk'])) {
            $pb->where('apsperstyle.mastermodel', $theData['pdk']);
        }
        if (!empty($theData['area'])) {
            $pb->where('perbaikan_area.area', $theData['area']);
        }
        if (!empty($theData['buyer'])) {
            $pb->where('data_model.kd_buyer_order', $theData['buyer']);
        }

        $perbaikan = $pb->groupBy('perbaikan_area.tgl_perbaikan')
            ->orderBy('perbaikan_area.tgl_perbaikan', 'ASC')
            ->get()
            ->getResultArray();

        // ambil data perbaikan area sesuai tgl produksi
        $stc = $this->db->table('data_bs')
            ->select('SUM(data_bs.qty/24) AS qty_stc, data_bs.tgl_instocklot,
            SUM(
                CASE 
                    WHEN data_bs.no_label BETWEEN 3000 AND 3999
                    OR data_bs.no_label BETWEEN 8000 AND 8999
                    THEN data_bs.qty/24
                    ELSE 0
                END
            ) AS qty_stcPb')
            ->join('apsperstyle', 'data_bs.idapsperstyle=apsperstyle.idapsperstyle')
            ->join('data_model', 'data_model.no_model=apsperstyle.mastermodel')
            ->where('data_bs.tgl_instocklot >=', $theData['awal'])
            ->where('data_bs.tgl_instocklot <=', $theData['akhir']);

        if (!empty($theData['pdk'])) {
            $stc->where('apsperstyle.mastermodel', $theData['pdk']);
        }
        if (!empty($theData['area'])) {
            $stc->where('data_bs.area', $theData['area']);
        }
        if (!empty($theData['buyer'])) {
            $stc->where('data_model.kd_buyer_order', $theData['buyer']);
        }

        $stocklot = $stc->groupBy('data_bs.tgl_instocklot')
            ->orderBy('data_bs.tgl_instocklot', 'ASC')
            ->get()
            ->getResultArray();


        foreach ($prodduksi as $row) {
            $tgl = $row['tgl_produksi'];
            $summary[$tgl]['prod'] = round($row['qty_prod'], 2);
        }

        foreach ($perbaikan as $row) {
            $tgl = $row['tgl_perbaikan'];
            $summary[$tgl]['pb'] = round($row['qty_pb'], 2);
        }
        foreach ($stocklot as $row) {
            $tgl = $row['tgl_instocklot'];
            $summary[$tgl]['stc'] = round($row['qty_stc'], 2);
            $summary[$tgl]['stcPb'] = round($row['qty_stcPb'], 2);
        }

        foreach ($summary as $tgl => $row) {
            $prod  = (float)$row['prod'];
            $pb    = (float)$row['pb'];
            $stc   = (float)$row['stc'];
            $stcPb = (float)$row['stcPb'];

            $repair = ($prod > 0 && $pb > 0) ? round(($pb / $prod) * 100) : 0;
            $bsRepair = ($stcPb > 0 && $pb > 0) ? round(($stcPb / $pb) * 100) : 0;
            $goodRepair = ($pb > 0 || $stcPb > 0) ? round((($pb - $stcPb) / $pb) * 100) : 0;
            $pureStc = ($stc > 0 || $stcPb > 0) ? round((($stc - $stcPb) / $prod) * 100) : 0;

            $summary[$tgl]['prod']  = $row['prod']  ?? 0;
            $summary[$tgl]['pb']    = $row['pb']    ?? 0;
            $summary[$tgl]['stc']   = $row['stc']   ?? 0;
            $summary[$tgl]['stcPb'] = $row['stcPb'] ?? 0;
            $summary[$tgl]['repair'] = $repair;
            $summary[$tgl]['bsRepair'] = $bsRepair;
            $summary[$tgl]['goodRepair'] = $goodRepair;
            $summary[$tgl]['pureStc'] = $pureStc;
        }

        // $builder = $this->db->table('produksi p')
        //     ->select("
        //         p.tgl_produksi AS tgl,
        //         SUM(p.qty_produksi/24) AS prod,
        //         COALESCE(pb.pb, 0) AS pb,
        //         COALESCE(stc.stc, 0) AS stc,
        //         COALESCE(stc.stcPb, 0) AS stcPb
        //     ")
        //     ->join('apsperstyle aps', 'p.idapsperstyle=aps.idapsperstyle')
        //     ->join('data_model dm', 'dm.no_model=aps.mastermodel')
        //     ->join(
        //         "(SELECT tgl_perbaikan, SUM(qty/24) pb
        //         FROM perbaikan_area
        //         GROUP BY tgl_perbaikan) pb",
        //         "pb.tgl_perbaikan = p.tgl_produksi",
        //         "left"
        //     )

        //     ->join(
        //         "(SELECT tgl_instocklot, SUM(qty/24) stc, SUM(CASE WHEN no_label BETWEEN 3000 AND 3999 OR no_label BETWEEN 8000 AND 8999 THEN qty/24 ELSE 0 END) stcPb
        //         FROM data_bs
        //         GROUP BY tgl_instocklot) stc",
        //         "stc.tgl_instocklot = p.tgl_produksi",
        //         "left"
        //     )
        //     ->where('p.tgl_produksi >=', $theData['awal'])
        //     ->where('p.tgl_produksi <=', $theData['akhir']);
        // if (!empty($theData['pdk'])) {
        //     $builder->where('aps.mastermodel', $theData['pdk']);
        // }
        // if (!empty($theData['area'])) {
        //     $builder->where('p.area', $theData['area']);
        // }
        // if (!empty($theData['buyer'])) {
        //     $builder->where('dm.kd_buyer_order', $theData['buyer']);
        // }
        // $result = $builder
        //     ->groupBy('p.tgl_produksi')
        //     ->orderBy('p.tgl_produksi', 'ASC')
        //     ->get()
        //     ->getResultArray();

        // dd($result);

        // foreach ($result as &$row) {
        //     $prod  = (float)$row['prod'];
        //     $pb    = (float)$row['pb'];
        //     $stc   = (float)$row['stc'];
        //     $stcPb = (float)$row['stcPb'];

        //     $repair = ($prod > 0 && $pb > 0) ? round(($pb / $prod) * 100) : 0;
        //     $bsRepair = ($stcPb > 0 && $pb > 0) ? round(($stcPb / $pb) * 100) : 0;
        //     $goodRepair = ($pb > 0 || $stcPb > 0) ? round((($pb - $stcPb) / $pb) * 100) : 0;
        //     $pureStc = ($stc > 0 || $stcPb > 0) ? round((($stc - $stcPb) / $prod) * 100) : 0;

        //     $row['repair'] = $repair;
        //     $row['bsRepair'] = $bsRepair;
        //     $row['goodRepair'] = $goodRepair;
        //     $row['pureStc'] = $pureStc;
        // }

        return $summary;
    }
}
