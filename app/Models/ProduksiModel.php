<?php

namespace App\Models;

use CodeIgniter\Model;
use LDAP\Result;

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

        $result = $this->select('produksi.tgl_produksi, apsperstyle.machinetypeid, apsperstyle.factory, apsperstyle.delivery, COUNT(DISTINCT produksi.no_mesin) AS jl_mc')
            ->join('apsperstyle', 'produksi.idapsperstyle = apsperstyle.idapsperstyle', 'left')
            ->where('apsperstyle.production_unit !=', 'MJ')
            ->where('apsperstyle.factory', $data['area'])
            ->where('apsperstyle.machinetypeid', $data['jarum'])
            ->where('apsperstyle.delivery', $data['delivery'])
            ->where('produksi.tgl_produksi', $yesterday)
            ->groupBy('apsperstyle.machinetypeid, apsperstyle.delivery, apsperstyle.factory')
            ->orderBy('apsperstyle.machinetypeid')
            ->findAll();
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
        $prod = $this->select('SUM(produksi.qty_produksi) AS produksi')
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
        return $this->select('COUNT(DISTINCT produksi.no_mesin) AS jl_mc')
            ->join('apsperstyle', 'apsperstyle.idapsperstyle = produksi.idapsperstyle', 'left')
            ->join('data_model', 'data_model.no_model = apsperstyle.mastermodel', 'left')
            ->where('produksi.no_mesin !=', 'STOK PAKING')
            ->where('produksi.tgl_produksi IS NOT NULL')
            ->where('data_model.no_model', $data['model'])
            ->where('apsperstyle.size', $data['size'])
            ->where('apsperstyle.delivery', $data['delivery'])
            ->where('produksi.tgl_produksi', $data['yesterday'])
            ->groupBy('apsperstyle.machinetypeid, data_model.no_model, apsperstyle.size, apsperstyle.delivery')
            ->first();
    }
}
