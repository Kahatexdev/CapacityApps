<?php

namespace App\Models;

use CodeIgniter\Cache\Handlers\WincacheHandler;
use CodeIgniter\Model;
use LDAP\Result;
use DateTime;

class ProduksiModel extends Model
{
    protected $table            = 'produksi';
    protected $primaryKey       = 'id_produksi';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id_produksi', 'idapsperstyle', 'tgl_produksi', 'qty_produksi', 'bagian', 'storage_awal', 'storage_akhir', 'no_box', 'no_label', 'no_mesin', 'created_at', 'updated_at', 'admin', 'kode_shipment', 'delivery', 'shift', 'area', 'shift_a', 'shift_b', 'shift_c'];

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

    public function getProduksi($area = null, $bulan, $tglProduksi = null, $tglProduksiSampai = null, $noModel = null, $size = null, $noBox = null, $noLabel = null)
    {
        // dd($tglProduksi, $tglProduksiSampai);
        // Mulai query
        $query = $this->select('tgl_produksi, produksi.*, apsperstyle.mastermodel, apsperstyle.size, sisa')
            ->join('apsperstyle', 'apsperstyle.idapsperstyle = produksi.idapsperstyle');


        // Tambahkan filter hanya jika parameter tidak null
        if ($tglProduksi && $tglProduksiSampai) {
            $query->where("produksi.tgl_produksi BETWEEN '$tglProduksi' AND '$tglProduksiSampai'");
        } elseif ($tglProduksi) {
            $query->where('produksi.tgl_produksi', $tglProduksi);
        }
        if ($area) {
            $query->where('produksi.area', $area);
        }
        if ($noModel) {
            $query->where('apsperstyle.mastermodel', $noModel);
        }

        if ($size) {
            $query->where('apsperstyle.size', $size);
        }

        if ($noBox) {
            $query->where('produksi.no_box', $noBox);
        }

        if ($noLabel) {
            $query->where('produksi.no_label', $noLabel);
        }

        // Eksekusi dan kembalikan hasil
        return $query->orderBy('produksi.tgl_produksi')->findAll();
    }
    public function existingData($insert)
    {

        return $this->select('id_produksi,qty_produksi')
            ->where('idapsperstyle', $insert['idapsperstyle'])
            ->where('tgl_produksi', $insert['tgl_produksi'])
            ->where('qty_produksi', $insert['qty_produksi'])
            ->where('no_box', $insert['no_box'])
            ->where('no_label', $insert['no_label'])
            ->where('no_mesin', $insert['no_mesin'])
            ->first();
    }
    public function getProduksiPerhari($bulan, $year)
    {
        $results = $this->select('DATE(tgl_produksi) as tgl_produksi, SUM(qty_produksi) as qty_produksi')
            ->where('MONTH(tgl_produksi)', $bulan)
            ->where('YEAR(tgl_produksi)', $year)
            ->like('storage_akhir', '-')
            ->groupBy('DATE(tgl_produksi)')
            ->orderBy('tgl_produksi')
            ->findAll();

        // Format tanggal ke d-F
        return array_map(function ($result) {
            $result['tgl_produksi'] = date('d/m', strtotime($result['tgl_produksi']));
            return $result;
        }, $results);
    }
    public function getProduksiPerArea($area, $bulan, $year)
    {
        $result = $this->select('DATE(tgl_produksi) as tgl_produksi, SUM(qty_produksi) as qty_produksi')
            ->where('MONTH(tgl_produksi)', $bulan)
            ->where('YEAR(tgl_produksi)', $year)
            ->where('area', $area)
            ->groupBy('DATE(tgl_produksi)')
            ->orderBy('tgl_produksi')
            ->findAll();

        if (!$result) {
            return [
                'tgl_produksi' => 0,
                'qty_produksi' => 0,
            ];
        } else {
            return array_map(function ($item) {
                $item['tgl_produksi'] = date('d/m', strtotime($item['tgl_produksi']));
                return $item;
            }, $result);
        }
    }
    public function getProduksiHarian()
    {
        return $this->select('apsperstyle.mastermodel, DATE(produksi.created_at) as tgl_upload, produksi.tgl_produksi, produksi.admin, SUM(produksi.qty_produksi) as qty, SUM(apsperstyle.sisa) as sisa, SUM(apsperstyle.qty) as qty_order')
            ->join('apsperstyle', 'produksi.idapsperstyle = apsperstyle.idapsperstyle')
            ->groupBy('apsperstyle.mastermodel, DATE(produksi.created_at)')
            ->findAll();
    }
    public function getProduksiHarianArea()
    {
        return $this->select(' DATE(produksi.created_at) as tgl_upload, produksi.tgl_produksi, produksi.admin, SUM(produksi.qty_produksi) as qty')
            ->join('apsperstyle', 'produksi.idapsperstyle = apsperstyle.idapsperstyle')
            ->like('produksi.admin', 'KK')
            ->groupBy('DATE(produksi.tgl_produksi), produksi.admin')
            ->orderBy('DATE(produksi.tgl_produksi)', 'DESC')
            ->findAll();
    }

    public function deleteSesuai(array $idaps)
    {
        return $this->whereIn('idapsperstyle', $idaps)->delete();
    }
    public function getDataForReset($area, $tanggal, $akhir)
    {
        return $this->select('id_produksi,idapsperstyle,qty_produksi')
            ->where('admin', $area)
            ->where('tgl_produksi >=', $tanggal)
            ->where('tgl_produksi <=', $akhir)
            ->findAll();
    }
    public function getIdForBs($validate)
    {
        return $this->where('no_box', $validate['no_box'])
            ->where('no_label', $validate['no_label'])
            ->where('no_mesin', $validate['no_mesin'])
            ->first();
    }

    public function getJlMc($data)
    {
        $yesterday = date('Y-m-d', strtotime('-2 day'));

        $result = $this->select('produksi.tgl_produksi, apsperstyle.machinetypeid, apsperstyle.factory, apsperstyle.delivery, COUNT(DISTINCT produksi.no_mesin) AS jl_mc')
            ->join('apsperstyle', 'produksi.idapsperstyle = apsperstyle.idapsperstyle', 'left')
            ->where('apsperstyle.production_unit !=', 'MJ')
            ->where('apsperstyle.factory', $data['area'])
            ->where('apsperstyle.mastermodel', $data['model'])
            ->where('apsperstyle.machinetypeid', $data['jarum'])
            ->where('apsperstyle.delivery', $data['delivery'])
            ->where('produksi.tgl_produksi', $yesterday)
            ->groupBy('apsperstyle.mastermodel, apsperstyle.machinetypeid, apsperstyle.factory, apsperstyle.delivery')
            ->orderBy('apsperstyle.mastermodel, apsperstyle.machinetypeid, apsperstyle.factory, apsperstyle.delivery', 'ASC')
            ->findAll();
        return $result;
    }

    public function getJlMcJrm($data)
    {
        // dd($data);
        $yesterday = date('Y-m-d', strtotime('-2 day'));

        $result = $this->select('produksi.tgl_produksi, apsperstyle.machinetypeid, apsperstyle.factory, apsperstyle.delivery, COUNT(DISTINCT produksi.no_mesin) AS jl_mc')
            ->join('apsperstyle', 'produksi.idapsperstyle = apsperstyle.idapsperstyle', 'left')
            ->join('data_model', 'apsperstyle.mastermodel = data_model.no_model', 'left')
            ->where('apsperstyle.production_unit !=', 'MJ')
            ->where('data_model.kd_buyer_order', $data['buyer'])
            ->where('apsperstyle.machinetypeid', $data['jarum'])
            ->where('apsperstyle.delivery', $data['delivery'])
            ->where('produksi.tgl_produksi', $yesterday)
            ->groupBy('apsperstyle.machinetypeid, apsperstyle.delivery, factory')
            ->orderBy('apsperstyle.machinetypeid')
            ->findAll();
        return $result;
    }

    public function getJlMcJrmArea($data)
    {
        $yesterday = date('Y-m-d', strtotime('-2 day'));

        $builder =  $this->select('produksi.tgl_produksi, apsperstyle.machinetypeid, apsperstyle.factory, apsperstyle.delivery, COUNT(DISTINCT produksi.no_mesin) AS jl_mc')
            ->join('apsperstyle', 'produksi.idapsperstyle = apsperstyle.idapsperstyle', 'left')
            ->where('apsperstyle.production_unit !=', 'MJ')
            ->where('apsperstyle.machinetypeid', $data['jarum'])
            ->where('apsperstyle.delivery', $data['delivery'])
            ->where('produksi.tgl_produksi', $yesterday);
        // Filter area hanya jika tidak kosong
        if (!empty($data['area'])) {
            $builder->where('apsperstyle.factory', $data['area']);
        }
        $builder->groupBy('apsperstyle.machinetypeid')
            ->groupBy('apsperstyle.delivery')
            ->groupBy('apsperstyle.factory')
            ->orderBy('apsperstyle.machinetypeid');
        $result = $builder->findAll(); // Eksekusi query
        return $result;
    }

    public function getActualMcByModel($data)
    {
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $result = $this->select('apsperstyle.mastermodel, apsperstyle.machinetypeid, apsperstyle.factory, apsperstyle.delivery, COUNT(DISTINCT produksi.no_mesin) AS jl_mc, produksi.tgl_produksi, produksi.idapsperstyle')
            ->join('apsperstyle', 'produksi.idapsperstyle = apsperstyle.idapsperstyle', 'left')
            ->join('data_model', 'apsperstyle.mastermodel = data_model.no_model', 'left')
            ->where('apsperstyle.production_unit !=', 'MJ')
            ->where('apsperstyle.factory', $data['area'])
            ->where('apsperstyle.mastermodel', $data['model'])
            ->where('apsperstyle.machinetypeid', $data['jarum'])
            ->where('apsperstyle.delivery', $data['delivery'])
            ->where('produksi.tgl_produksi', $yesterday)
            ->groupBy('produksi.tgl_produksi, apsperstyle.mastermodel,apsperstyle.delivery')
            ->orderBy('produksi.tgl_produksi', 'DESC')
            ->orderBy('apsperstyle.mastermodel', 'ASC')
            ->first();
        return $result;
    }
    public function updateProduksi()
    {
        $builder = $this->select('produksi.id_produksi,produksi.idapsperstyle, apsperstyle.mastermodel as mastermodel, apsperstyle.size as size')
            ->join('apsperstyle', 'produksi.idapsperstyle = apsperstyle.idapsperstyle', 'left')
            ->where('produksi.no_model IS NULL')
            ->where('produksi.size IS NULL');

        return $builder->get(10000)->getResultArray();
    }
    public function getJlMcTimter($data)
    {
        $query = $this->select('apsperstyle.mastermodel, apsperstyle.machinetypeid, apsperstyle.size, produksi.tgl_produksi, COUNT(DISTINCT produksi.no_mesin) AS jl_mc')
            ->join('apsperstyle', 'apsperstyle.idapsperstyle = produksi.idapsperstyle', 'left')
            ->join('data_model', 'data_model.no_model = apsperstyle.mastermodel', 'left')
            ->where('produksi.no_mesin !=', 'STOK PAKING')
            ->where('produksi.tgl_produksi IS NOT NULL');

        if (!empty($data['area'])) {
            $this->where('produksi.area', $data['area']);
        }
        if (!empty($data['jarum'])) {
            $this->like('apsperstyle.machinetypeid', $data['jarum']);
        }
        if (!empty($data['pdk'])) {
            $this->where('data_model.no_model', $data['pdk']);
        }
        if (!empty($data['awal'])) {
            $this->where('produksi.tgl_produksi =', $data['awal']);
        }

        return $query->groupBy('apsperstyle.machinetypeid, data_model.no_model, apsperstyle.size, produksi.tgl_produksi')
            ->orderBy('apsperstyle.machinetypeid, data_model.no_model, apsperstyle.size, produksi.tgl_produksi', 'ASC')
            ->findAll();
    }
    public function getProdByPdkSize($model, $size)
    {
        $tabel = 'apsperstyle'; // Perbaikan: Harus dalam string
        $idaps = $this->db->table($tabel) // Perbaikan: Gunakan query builder dari DB
            ->select('idapsperstyle')
            ->where('mastermodel', $model)
            ->where('size', $size) // Perbaikan: Harusnya `size`, bukan `sizee`
            ->get()
            ->getResultArray(); // Perbaikan: Ambil sebagai array

        // Jika tidak ada hasil, langsung return 0 untuk menghindari error
        if (empty($idaps)) {
            return 0;
        }

        // Ambil nilai idapsperstyle sebagai array untuk whereIn
        $idapsArray = array_column($idaps, 'idapsperstyle');

        $prod = $this->db->table('produksi') // Perbaikan: Tambahkan table produksi
            ->select('SUM(qty_produksi) AS ttl_prod')
            ->whereIn('idapsperstyle', $idapsArray)
            ->get()
            ->getRowArray(); // Perbaikan: Ambil satu baris
        $bs = $this->db->table('data_bs') // Perbaikan: Tambahkan table produksi
            ->select('SUM(qty) AS bs')
            ->whereIn('idapsperstyle', $idapsArray)
            ->get()
            ->getRowArray(); // Perbaikan: Ambil satu baris 
        $result = [
            'prod' => $prod['ttl_prod'] ?? 0,
            'bs' => $bs['bs'] ?? 0
        ];
        return $result;
    }
    public function getProduksiByModelDelivery($data)
    {
        $prod = $this->select('SUM(produksi.qty_produksi/24) AS produksi')
            ->join('apsperstyle', 'produksi.idapsperstyle = apsperstyle.idapsperstyle', 'left')
            ->where('apsperstyle.production_unit !=', 'MJ')
            ->where('apsperstyle.factory', $data['area'])
            ->where('apsperstyle.mastermodel', $data['model'])
            ->where('apsperstyle.machinetypeid', $data['jarum'])
            ->where('apsperstyle.delivery', $data['delivery'])
            ->groupBy('apsperstyle.mastermodel, apsperstyle.delivery, machinetypeid')
            ->first();
        if (empty($prod)) {
            $qty = 0;
        } else {
            $qty = $prod['produksi'];
        }
        return $qty;
    }
    public function monthlyProd($filters)
    {
        $builder = $this->db->table('produksi');
        $builder->select('produksi.idapsperstyle, apsperstyle.mastermodel, apsperstyle.size, SUM(produksi.qty_produksi) as prod');
        $builder->join('apsperstyle', 'apsperstyle.idapsperstyle = produksi.idapsperstyle', 'left');

        if (!empty($filters['bulan'])) {
            $builder->where('MONTH(produksi.tgl_produksi)', $filters['bulan']);
        }

        if (!empty($filters['tahun'])) {
            $builder->where('YEAR(produksi.tgl_produksi)', $filters['tahun']);
        }

        if (!empty($filters['area'])) {
            $builder->where('produksi.area', $filters['area']);
        }

        // Pastikan group by hanya model & size untuk aggr per style
        return $builder->groupBy(['produksi.idapsperstyle', 'apsperstyle.mastermodel', 'apsperstyle.size'])->get()->getResultArray();
    }

    public function directMonthly($filters)
    {
        $builder = $this->select('area, COUNT(DISTINCT no_mesin) as jumlah_mesin');

        if (!empty($filters['bulan'])) {
            $builder->where('MONTH(tgl_produksi)', $filters['bulan']);
        }

        if (!empty($filters['tahun'])) {
            $builder->where('YEAR(tgl_produksi)', $filters['tahun']);
        }
        if (!empty($filters['area'])) {
            $builder->where('area', $filters['area']);
        }

        $mesin = $builder->groupBy('area')->findAll();

        $totalMesin = 0;
        foreach ($mesin as $mc) {
            $totalMesin += $mc['jumlah_mesin'];
        }

        return $totalMesin;
    }

    public function getProduksiPerStyle($area, $tanggal)
    {
        // Mulai query
        $query = $this->select(' tgl_produksi, apsperstyle.mastermodel, apsperstyle.size, apsperstyle.inisial, apsperstyle.qty, sisa,sum(qty_produksi) as prod,')
            ->join('apsperstyle', 'apsperstyle.idapsperstyle = produksi.idapsperstyle')
            ->where('apsperstyle.factory', $area)
            ->where('produksi.tgl_produksi', $tanggal)
            ->groupBy('apsperstyle.mastermodel')
            ->groupBy('apsperstyle.size');


        return $query->orderBy('produksi.tgl_produksi')->findAll();
    }
    public function hariProduksi($filters)
    {
        $builder = $this->select('COUNT(distinct(tgl_produksi)) as hari');

        if (!empty($filters['bulan'])) {
            $builder->where('MONTH(tgl_produksi)', $filters['bulan']);
        }

        if (!empty($filters['tahun'])) {
            $builder->where('YEAR(tgl_produksi)', $filters['tahun']);
        }

        if (!empty($filters['area'])) {
            $builder->where('area', $filters['area']);
        }

        return $builder->first(); // hasilnya ['hari' => jumlah_hari]
    }
    public function dailyProductivity($filters)
    {
        $builder = $this->select(' produksi.tgl_produksi, apsperstyle.mastermodel, apsperstyle.size,SUM(qty_produksi) as prod')
            ->join('apsperstyle', 'apsperstyle.idapsperstyle = produksi.idapsperstyle');

        if (!empty($filters['bulan'])) {
            $builder->where('MONTH(tgl_produksi)', $filters['bulan']);
        }

        if (!empty($filters['tahun'])) {
            $builder->where('YEAR(tgl_produksi)', $filters['tahun']);
        }

        if (!empty($filters['area'])) {
            $builder->where('area', $filters['area']);
        }

        return $builder->groupBy('produksi.idapsperstyle, produksi.tgl_produksi')
            ->groupBy('apsperstyle.size')->findAll();
    }
    public function totalProdBulan($filters)
    {
        $builder = $this->select('SUM(qty_produksi) as prod');

        if (!empty($filters['bulan'])) {
            $builder->where('MONTH(tgl_produksi)', $filters['bulan']);
        }

        if (!empty($filters['tahun'])) {
            $builder->where('YEAR(tgl_produksi)', $filters['tahun']);
        }

        if (!empty($filters['area'])) {
            $builder->where('area', $filters['area']);
        }

        // Ambil hasil query, pastikan ambil baris pertama
        $result = $builder->findAll();

        // Akses nilai 'prod' dari baris pertama
        $prod = isset($result[0]['prod']) ? $result[0]['prod'] : 0; // Default ke 0 jika null

        return $prod;
    }
    public function getJlMcByModel($data)
    {
        return $this->select('COUNT(DISTINCT produksi.no_mesin) AS jl_mc, SUM(qty_produksi) AS qty_produksi')
            ->join('apsperstyle', 'apsperstyle.idapsperstyle = produksi.idapsperstyle', 'left')
            ->join('data_model', 'data_model.no_model = apsperstyle.mastermodel', 'left')
            ->where('produksi.no_mesin !=', 'STOK PAKING')
            ->where('produksi.tgl_produksi IS NOT NULL')
            ->where('data_model.no_model', $data['model'])
            ->where('apsperstyle.size', $data['size'])
            ->where('apsperstyle.delivery', $data['delivery'])
            ->where('produksi.tgl_produksi', $data['yesterday'])
            ->where('produksi.area', $data['area'])
            ->groupBy('apsperstyle.machinetypeid, data_model.no_model, apsperstyle.size, apsperstyle.delivery')
            ->first();
    }
    public function getStartStopMc($noModel)
    {
        return $this->select('produksi.tgl_produksi, data_model.no_model, MIN(produksi.tgl_produksi) AS start_mc, MAX(produksi.tgl_produksi) AS stop_mc')
            ->join('apsperstyle', 'produksi.idapsperstyle = apsperstyle.idapsperstyle', 'left')
            ->join('data_model', 'data_model.no_model = apsperstyle.mastermodel', 'left')
            ->where('apsperstyle.production_unit !=', 'MJ')
            ->where('data_model.no_model', $noModel)
            ->groupBy('data_model.no_model')
            ->orderBy('apsperstyle.machinetypeid')
            ->findAll();
    }

    public function getProductionStats($bulan, $tahun, $area)
    {
        return $this->select(
            'produksi.tgl_produksi,
     produksi.area,
     data_model.kd_buyer_order AS buyer,
     apsperstyle.mastermodel,
     apsperstyle.machinetypeid,
     apsperstyle.size,
     (SUM(produksi.qty_produksi) / 24) AS prod,
     COUNT(DISTINCT produksi.no_mesin) AS jl_mc,
     ((SUM(produksi.qty_produksi) / 24) / COUNT(DISTINCT produksi.no_mesin)) AS prodmc,
     (3600 / apsperstyle.smv) AS target,
     (((SUM(produksi.qty_produksi) / 24) / COUNT(DISTINCT produksi.no_mesin)) / (3600 / apsperstyle.smv)) * 100 AS productivity,
     bs.bsdz / ((SUM(produksi.qty_produksi) / 24) + bs.bsdz) AS loss'
        )

            ->join('apsperstyle', 'produksi.idapsperstyle = apsperstyle.idapsperstyle', 'inner')
            ->join('data_model', 'apsperstyle.mastermodel = data_model.no_model', 'inner')
            ->join(
                '(SELECT no_model, size, area, SUM(qty_pcs) / 24 AS bsdz FROM bs_mesin GROUP BY no_model, size, area) AS bs',
                'apsperstyle.mastermodel = bs.no_model AND apsperstyle.size = bs.size AND apsperstyle.factory = bs.area',
                'inner'
            )
            ->where('MONTH(produksi.tgl_produksi)', $bulan)
            ->where('YEAR(produksi.tgl_produksi)', $tahun)
            ->where('produksi.area', $area)
            ->groupBy(['produksi.tgl_produksi', 'produksi.area', 'apsperstyle.mastermodel', 'apsperstyle.machinetypeid', 'apsperstyle.size', 'apsperstyle.smv', 'bs.bsdz'])
            ->orderBy('produksi.tgl_produksi')
            ->findAll();
    }
    public function getProductionPerJarum($data)
    {
        return $this->select(
            'produksi.area,
     apsperstyle.machinetypeid,
     (SUM(produksi.qty_produksi) / 24) AS prod,
     COUNT(DISTINCT produksi.no_mesin) AS jl_mc,
     ((SUM(produksi.qty_produksi) / 24) / COUNT(DISTINCT produksi.no_mesin)) AS prodmc,
     (3600 / AVG(apsperstyle.smv)) AS target,
     (((SUM(produksi.qty_produksi) / 24) / COUNT(DISTINCT produksi.no_mesin)) / (3600 / AVG(apsperstyle.smv))) * 100 AS productivity'
        )

            ->join('apsperstyle', 'produksi.idapsperstyle = apsperstyle.idapsperstyle', 'inner')
            ->join('data_model', 'apsperstyle.mastermodel = data_model.no_model', 'inner')
            ->where('produksi.tgl_produksi', $data['awal'])
            ->where('produksi.area', $data['area'])
            ->groupBy('apsperstyle.machinetypeid')
            ->orderBy('produksi.tgl_produksi')
            ->findAll();
    }
    // public function getProductionPerJarum($data)
    // {
    //     $query = $this->select('\ apsperstyle.machinetypeid, apsperstyle.size, produksi.tgl_produksi, COUNT(DISTINCT produksi.no_mesin) AS jl_mc')
    //         ->join('apsperstyle', 'apsperstyle.idapsperstyle = produksi.idapsperstyle', 'left')
    //         ->join('data_model', 'data_model.no_model = apsperstyle.mastermodel', 'left')
    //         ->where('produksi.no_mesin !=', 'STOK PAKING')
    //         ->where('produksi.tgl_produksi IS NOT NULL');

    //     if (!empty($data['area'])) {
    //         $this->where('produksi.area', $data['area']);
    //     }
    //     if (!empty($data['awal'])) {
    //         $this->where('produksi.tgl_produksi =', $data['awal']);
    //     }

    //     return $query->groupBy('apsperstyle.machinetypeid')
    //         ->orderBy('apsperstyle.machinetypeid, data_model.no_model, apsperstyle.size, produksi.tgl_produksi', 'ASC')
    //         ->findAll();
    // }
    public function newestDate($area)
    {
        return $this->select('tgl_produksi')->where('area', $area)->orderBy('tgl_produksi', 'desc')->first();
    }

    public function getDetailById($idprod, $idaps)
    {
        // Normalisasi input supaya selalu jadi array
        if (!is_array($idprod)) {
            $idprod = explode(',', str_replace(' ', '', $idprod));
        }
        if (!is_array($idaps)) {
            $idaps = explode(',', str_replace(' ', '', $idaps));
        }

        $builder = $this->select('
            produksi.tgl_produksi,
            produksi.qty_produksi,
            produksi.no_mesin,
            produksi.area,
            apsperstyle.mastermodel,
            apsperstyle.size,
            apsperstyle.inisial
        ')
            ->join('apsperstyle', 'apsperstyle.idapsperstyle = produksi.idapsperstyle', 'left')
            ->whereIn('produksi.id_produksi', $idprod)
            ->whereIn('apsperstyle.idapsperstyle', $idaps);

        return $builder->get()->getResultArray();
    }
    public function cekProduksi($area, $tanggal)
    {
        return $this->where('area', $area)
            ->where('tgl_produksi', $tanggal)
            ->limit(1)
            ->countAllResults() > 0;
    }

    public function getDataProduksi($area, $tglProduksi)
    {
        return $this->select('apsperstyle.mastermodel, apsperstyle.machinetypeid, apsperstyle.size, apsperstyle.inisial, data_model.kd_buyer_order, produksi.area, produksi.tgl_produksi, produksi.no_mesin, produksi.shift_a, produksi.shift_b, produksi.shift_c, produksi.qty_produksi')
            ->join('apsperstyle', 'apsperstyle.idapsperstyle=produksi.idapsperstyle', 'left')
            ->join('data_model', 'apsperstyle.mastermodel=data_model.no_model', 'left')
            ->where('produksi.area', $area)
            ->where('produksi.tgl_produksi', $tglProduksi)
            ->groupBy('data_model.kd_buyer_order')
            ->groupBy('apsperstyle.mastermodel')
            ->groupBy('apsperstyle.machinetypeid')
            ->groupBy('apsperstyle.size')
            ->groupBy('produksi.no_mesin')
            ->orderBy('data_model.kd_buyer_order', 'ASC')
            ->orderBy('apsperstyle.mastermodel', 'ASC')
            ->orderBy('apsperstyle.machinetypeid', 'ASC')
            ->orderBy('apsperstyle.inisial', 'ASC')
            ->orderBy('produksi.no_mesin', 'ASC')
            ->findAll();
    }
    public function getTotalProduksiGroup($area)
    {
        $builder = $this->select('
            apsperstyle.mastermodel AS model,
            apsperstyle.size AS size,
            SUM(produksi.qty_produksi) AS total_qty
        ')
            ->join('apsperstyle', 'apsperstyle.idapsperstyle = produksi.idapsperstyle', 'left')
            ->join('data_model', 'apsperstyle.mastermodel =  data_model.no_model', 'left')
            ->where('apsperstyle.qty != 0')
            ->where('produksi.no_mesin !=', 'STOK PAKING')
            ->where('produksi.tgl_produksi IS NOT NULL');

        if (!empty($params['area'])) {
            $builder->where('apsperstyle.factory', $area);
        }

        // baru group by di akhir
        $builder->groupBy('apsperstyle.mastermodel, apsperstyle.size');

        return $builder->findAll();
    }

    public function getAllProd($idAps)
    {
        if (empty($idAps)) return [];
        $result = $this->select('idapsperstyle, SUM(qty_produksi) as qty_produksi')
            ->whereIn('idapsperstyle', $idAps)
            ->groupBy('idapsperstyle')
            ->findAll();

        $index = [];
        foreach ($result as $r) {
            $index[$r['idapsperstyle']] = $r['qty_produksi'];
        }
        return $index;
    }
    public function getJlmcByMonth($theData)
    {
        $this->select('area, COUNT(DISTINCT no_mesin) AS total_mc, SUM(qty_produksi) AS qty_produksi');
        // Filter berdasarkan bulan (misal "2025-10")
        if (!empty($theData['bulan'])) {
            $this->where("DATE_FORMAT(tgl_produksi, '%Y-%m')", $theData['bulan']);
        }
        // Filter area (kalau tidak kosong)
        if (!empty($theData['area'])) {
            $this->where('area', $theData['area']);
        }
        $this->groupBy('area');
        return $this->findAll();
    }
    public function produksiPerbulan($area, $bulan, $buyer, $dataGwList)
    {
        $db = \Config\Database::connect();

        $buyerFilter = '';
        // kalau buyer tidak kosong, tambahkan filter dan parameter
        if (!empty($buyer)) {
            $buyerFilter = ' AND dm.kd_buyer_order = :buyer:';
            $params['buyer'] = $buyer;
        }

        $bulanDateTime = DateTime::createFromFormat('F-Y', $bulan);
        $tahun = $bulanDateTime->format('Y'); // 2024
        $bulanNumber = $bulanDateTime->format('m'); // 12
        // === 1ï¸âƒ£ PRODUKSI + PERBAIKAN per TANGGAL ===
        $sqlProduksi = "
            SELECT 
                t.tanggal,
                COALESCE(p.qty_prod, 0) AS qty_prod,
                COALESCE(pa.qty_perbaikan, 0) AS qty_perbaikan
            FROM (
                SELECT tgl_produksi AS tanggal FROM produksi
                WHERE area = :area:
                  AND MONTH(tgl_produksi) = :bulan:
                  AND YEAR(tgl_produksi) = :tahun:
                UNION
                SELECT tgl_perbaikan FROM perbaikan_area
                WHERE area = :area:
                  AND MONTH(tgl_perbaikan) = :bulan:
                  AND YEAR(tgl_perbaikan) = :tahun:
            ) AS t
            LEFT JOIN (
                SELECT produksi.tgl_produksi, SUM(produksi.qty_produksi) AS qty_prod
                FROM produksi
                JOIN apsperstyle aps ON aps.idapsperstyle = produksi.idapsperstyle
                JOIN data_model dm ON dm.no_model = aps.mastermodel
                WHERE produksi.area = :area:
                  AND MONTH(produksi.tgl_produksi) = :bulan:
                  AND YEAR(produksi.tgl_produksi) = :tahun:
                  $buyerFilter
                GROUP BY tgl_produksi
            ) AS p ON t.tanggal = p.tgl_produksi
            LEFT JOIN (
                SELECT perbaikan_area.tgl_perbaikan, SUM(perbaikan_area.qty) AS qty_perbaikan
                FROM perbaikan_area
                JOIN apsperstyle aps ON aps.idapsperstyle = perbaikan_area.idapsperstyle
                JOIN data_model dm ON dm.no_model = aps.mastermodel
                WHERE perbaikan_area.area = :area:
                  AND MONTH(perbaikan_area.tgl_perbaikan) = :bulan:
                  AND YEAR(perbaikan_area.tgl_perbaikan) = :tahun:
                  $buyerFilter
                GROUP BY tgl_perbaikan
            ) AS pa ON t.tanggal = pa.tgl_perbaikan
            ORDER BY t.tanggal
        ";

        $dataProduksi = $db->query($sqlProduksi, [
            'area'  => $area,
            'bulan' => $bulanNumber,
            'tahun' => $tahun,
            'buyer' => $buyer
        ])->getResultArray();

        // === 2ï¸âƒ£ DATA BS MESIN per MODEL & SIZE ===
        $sqlBs = "
            SELECT 
                bs_mesin.tanggal_produksi,
                bs_mesin.no_model,
                bs_mesin.size,
                SUM(bs_mesin.qty_pcs) AS qty_pcs,
                SUM(bs_mesin.qty_gram) AS qty_gram
            FROM bs_mesin
            LEFT JOIN data_model dm ON dm.no_model=bs_mesin.no_model
            WHERE bs_mesin.area = :area:
            $buyerFilter
            AND MONTH(bs_mesin.tanggal_produksi) = :bulan:
            AND YEAR(bs_mesin.tanggal_produksi) = :tahun:
            GROUP BY bs_mesin.tanggal_produksi, bs_mesin.no_model, bs_mesin.size
            ORDER BY bs_mesin.tanggal_produksi, bs_mesin.no_model, bs_mesin.size
        ";

        $dataBs = $db->query($sqlBs, [
            'area'  => $area,
            'bulan' => $bulanNumber,
            'tahun' => $tahun,
            'buyer' => $buyer
        ])->getResultArray();

        // 2ï¸âƒ£ Hitung totalBsMc per model+size
        foreach ($dataBs as &$bs) {
            $noModel = $bs['no_model'];
            $size = $bs['size'];

            $gwValue = 0;
            foreach ($dataGwList as $gwItem) {
                if (
                    strtoupper($gwItem['no_model']) === strtoupper($noModel) &&
                    strtoupper($gwItem['size']) === $size
                ) {
                    $gwValue = $gwItem['gw'];
                    break;
                }
            }

            if ($gwValue == 0) {
                log_message('warning', "âš ï¸ GW tidak ditemukan untuk {$noModel} / {$size}");
            } else {
                $bsGram = $bs['qty_gram'] > 0 ? round($bs['qty_gram'] / $gwValue) : 0;
                $bsPcs  = $bs['qty_pcs'] + $bsGram;
                // $totalBsDz = round($bsPcs / 24);
            }
            $bs['totalBsMc'] = $bsPcs;
        }
        unset($bs);

        // 3ï¸âƒ£ Group berdasarkan tanggal_produksi
        $groupedByTanggal = [];

        foreach ($dataBs as $row) {
            $tanggal = $row['tanggal_produksi'];
            if (!isset($groupedByTanggal[$tanggal])) {
                $groupedByTanggal[$tanggal] = [
                    'tanggal_produksi' => $tanggal,
                    'totalBsMc' => 0,
                    'totalQtyPcs' => 0,
                    'totalQtyGram' => 0,
                ];
            }

            $groupedByTanggal[$tanggal]['totalBsMc']  += $row['totalBsMc'];
            $groupedByTanggal[$tanggal]['totalQtyPcs'] += $row['qty_pcs'];
            $groupedByTanggal[$tanggal]['totalQtyGram'] += $row['qty_gram'];
        }

        $bsPerTanggal = array_values($groupedByTanggal);

        $mapBs = [];
        foreach ($bsPerTanggal as $bs) {
            $mapBs[$bs['tanggal_produksi']] = $bs;
        }

        $finalData = [];
        foreach ($dataProduksi as $row) {
            $tanggal = $row['tanggal'];

            // ambil bs kalau ada
            $bs = $mapBs[$tanggal] ?? [
                'totalBsMc' => 0,
                'persentase' => 0,
            ];

            $persentase = ($row['qty_prod'] > 0)
                ? (($bs['totalBsMc'] + $row['qty_perbaikan']) / $row['qty_prod']) * 100
                : 0;

            $finalData[] = [
                'tanggal'        => $tanggal,
                'qty_prod'       => $row['qty_prod'],
                'totalBsMc'      => $bs['totalBsMc'],
                'qty_perbaikan'  => $row['qty_perbaikan'],
                'persentase'     => $persentase,
            ];
        }

        // urutkan hasil akhir berdasarkan tanggal
        usort($finalData, fn($a, $b) => strcmp($a['tanggal'], $b['tanggal']));


        return [
            'final'    => $finalData,   // hasil gabungan produksi + bs + perbaikan per tanggal
            'produksi' => $dataProduksi, // optional: kalau masih mau akses data mentahan
            'bs'       => $groupedByTanggal,       // optional: kalau masih mau akses detail bs per model/size
        ];
    }
    public function getLatestMc($filters)
    {
        $days = $this->select('tgl_produksi')
            ->where('area', $filters['area'])
            ->orderBy('tgl_produksi', 'DESC')
            ->first();
        $mc = $this->select('count(no_mesin) as mc, sum(qty_produksi) as prodYes')
            ->where('tgl_produksi', $days['tgl_produksi'])
            ->where('area', $filters['area'])
            ->first();
        return $mc;
    }
}
