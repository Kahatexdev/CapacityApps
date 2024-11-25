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

    public function getProduksi($area, $bulan)
    {
        $today = date('Y-m-d');
        $threeDaysAgo = date('Y-m-d', strtotime('-20 days'));
        return $this
            ->select('tgl_produksi,produksi.*, sisa')
            ->join('apsperstyle', 'apsperstyle.idapsperstyle= produksi.idapsperstyle')
            ->where('produksi.area', $area)
            ->where('tgl_produksi >=', $threeDaysAgo)
            ->where('tgl_produksi <=', $today)
            ->where('Month(tgl_produksi)', $bulan)
            ->orderBy('produksi.tgl_produksi')
            ->findAll();
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
    public function getProduksiPerhari($bulan)
    {
        $results = $this->select('DATE(tgl_produksi) as tgl_produksi, SUM(qty_produksi) as qty_produksi')
            ->where('MONTH(tgl_produksi)', $bulan)
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
    public function getProduksiPerArea($bulan, $area)
    {
        $result = $this->select('DATE(tgl_produksi) as tgl_produksi, SUM(qty_produksi) as qty_produksi')
            ->where('MONTH(tgl_produksi)', $bulan)
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

    public function getdataSummaryPertgl($data)
    {
        $this->select('apsperstyle.machinetypeid, apsperstyle.mastermodel, apsperstyle.size, SUM(apsperstyle.qty) AS qty, COUNT(DISTINCT produksi.tgl_produksi) AS running, SUM(produksi.qty_produksi) AS qty_produksi, COUNT(DISTINCT produksi.no_mesin) AS jl_mc, produksi.tgl_produksi')
            ->join('apsperstyle', 'produksi.idapsperstyle = apsperstyle.idapsperstyle')
            ->join('data_model', 'apsperstyle.mastermodel = data_model.no_model');

        if (!empty($data['buyer'])) {
            $this->where('data_model.kd_buyer_order', $data['buyer']);
        }
        if (!empty($data['area'])) {
            $this->where('produksi.admin', $data['area']);
        }
        if (!empty($data['jarum'])) {
            $this->where('apsperstyle.machinetypeid', $data['jarum']);
        }
        if (!empty($data['pdk'])) {
            $this->where('apsperstyle.mastermodel', $data['pdk']);
        }
        if (!empty($data['awal'])) {
            $this->where('produksi.tgl_produksi >=', $data['awal']);
        }
        if (!empty($data['akhir'])) {
            $this->where('produksi.tgl_produksi <=', $data['akhir']);
        }

        return $this->groupBy('apsperstyle.machinetypeid, apsperstyle.mastermodel, apsperstyle.size, produksi.tgl_produksi')
            ->orderBy('apsperstyle.machinetypeid, apsperstyle.mastermodel, apsperstyle.size, produksi.tgl_produksi', 'ASC')
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
        $yesterday = date('Y-m-d', strtotime('-24 day'));

        $result = $this->select('produksi.tgl_produksi, apsperstyle.machinetypeid, apsperstyle.factory, apsperstyle.delivery, COUNT(DISTINCT produksi.no_mesin) AS jl_mc')
            ->join('apsperstyle', 'produksi.idapsperstyle = apsperstyle.idapsperstyle', 'left')
            ->join('data_model', 'apsperstyle.mastermodel = data_model.no_model', 'left')
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
        $yesterday = date('Y-m-d', strtotime('-24 day'));

        $result = $this->select('produksi.tgl_produksi, apsperstyle.machinetypeid, apsperstyle.factory, apsperstyle.delivery, WEEK(apsperstyle.delivery, 1) as delivery_week, MONTH(apsperstyle.delivery) as delivery_month, YEAR(apsperstyle.delivery) as delivery_year, COUNT(DISTINCT produksi.no_mesin) AS jl_mc')
            ->join('apsperstyle', 'produksi.idapsperstyle = apsperstyle.idapsperstyle', 'left')
            ->join('data_model', 'apsperstyle.mastermodel = data_model.no_model', 'left')
            ->where('apsperstyle.production_unit !=', 'MJ')
            ->where('apsperstyle.machinetypeid', $data['jarum'])
            ->where('apsperstyle.delivery', $data['delivery'])
            ->where('produksi.tgl_produksi', $yesterday)
            ->groupBy('apsperstyle.machinetypeid')
            ->groupBy('delivery_week')
            ->orderBy('apsperstyle.machinetypeid')
            ->orderBy('delivery_week')
            ->findAll();
        return $result;
    }

    public function getJlMcArea($ar, $bulan)
    {
        $yesterday = date('Y-m-d', strtotime('-13 day'));

        $result = $this->select('apsperstyle.mastermodel, apsperstyle.machinetypeid, apsperstyle.factory, apsperstyle.delivery, WEEK(apsperstyle.delivery, 1) as delivery_week, MONTH(apsperstyle.delivery) as delivery_month, YEAR(apsperstyle.delivery) as delivery_year, COUNT(DISTINCT produksi.no_mesin) AS jl_mc')
            ->join('apsperstyle', 'produksi.idapsperstyle = apsperstyle.idapsperstyle', 'left')
            ->where('apsperstyle.factory', $ar)
            ->where('apsperstyle.production_unit !=', 'MJ')
            ->where('MONTH(apsperstyle.delivery)', date('m', strtotime($bulan))) // Filter bulan
            ->where('YEAR(apsperstyle.delivery)', date('Y', strtotime($bulan)))
            ->where('produksi.tgl_produksi', $yesterday)
            ->groupBy('apsperstyle.mastermodel')
            ->groupBy('apsperstyle.machinetypeid')
            ->groupBy('apsperstyle.factory')
            ->groupBy('apsperstyle.delivery')
            ->orderBy('apsperstyle.mastermodel')
            ->orderBy('apsperstyle.machinetypeid')
            ->orderBy('apsperstyle.factory')
            ->orderBy('apsperstyle.delivery')
            ->findAll();
        return $result;
    }

    public function getJlMcJrmArea($ar, $bulan)
    {
        $yesterday = date('Y-m-d', strtotime('-13 day'));

        $result = $this->select('produksi.tgl_produksi, apsperstyle.machinetypeid, apsperstyle.factory, apsperstyle.delivery, WEEK(apsperstyle.delivery, 1) as delivery_week, MONTH(apsperstyle.delivery) as delivery_month, YEAR(apsperstyle.delivery) as delivery_year, COUNT(DISTINCT produksi.no_mesin) AS jl_mc')
            ->join('apsperstyle', 'produksi.idapsperstyle = apsperstyle.idapsperstyle', 'left')
            ->where('apsperstyle.factory', $ar)
            ->where('apsperstyle.production_unit !=', 'MJ')
            ->where('MONTH(apsperstyle.delivery)', date('m', strtotime($bulan))) // Filter bulan
            ->where('YEAR(apsperstyle.delivery)', date('Y', strtotime($bulan)))
            ->where('produksi.tgl_produksi', $yesterday)
            ->groupBy('apsperstyle.machinetypeid')
            ->groupBy('delivery_week')
            ->orderBy('apsperstyle.machinetypeid')
            ->orderBy('delivery_week')
            ->findAll();
        return $result;
    }

    public function getActualMcByModel($data)
    {
        $yesterday = date('Y-m-d', strtotime('-13 day'));
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
}
