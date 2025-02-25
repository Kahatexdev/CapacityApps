<?php

namespace App\Models;

use CodeIgniter\Model;

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

    public function getProduksi($area, $bulan, $tglProduksi = null, $noModel = null, $size = null)
    {
        // Mulai query
        $query = $this->select('tgl_produksi, produksi.*, apsperstyle.mastermodel, apsperstyle.size, sisa')
            ->join('apsperstyle', 'apsperstyle.idapsperstyle = produksi.idapsperstyle')
            ->where('produksi.area', $area);

        // Tambahkan filter hanya jika parameter tidak null
        if ($tglProduksi) {
            $query->where('produksi.tgl_produksi', $tglProduksi);
        }

        if ($noModel) {
            $query->where('apsperstyle.mastermodel', $noModel);
        }

        if ($size) {
            $query->where('apsperstyle.size', $size);
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
            ->like('storage_akhir', '-')
            ->groupBy('DATE(tgl_produksi)')
            ->orderBy('tgl_produksi')
            ->findAll();

        if (!$result) {
            return [
                'tgl_produksi' => 0,
                'qty_produksi' => 0,
            ];
        } else {
            // Format tanggal ke d/m
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
}
