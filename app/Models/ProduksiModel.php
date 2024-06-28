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
    protected $allowedFields    = ['id_produksi', 'idapsperstyle', 'tgl_produksi', 'qty_produksi', 'bagian', 'storage_awal', 'storage_akhir', 'bs_prod', 'katefori_bs', 'no_box', 'no_label', 'no_mesin', 'created_at', 'updated_at', 'admin', 'kode_shipment', 'delivery', 'shift', 'area'];

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
        return $this->join('apsperstyle', 'apsperstyle.idapsperstyle= produksi.idapsperstyle')
            ->select('tgl_produksi,mastermodel,size,produksi.delivery,sum(qty) as qty,  sum(qty_produksi) as qty_produksi')->where('produksi.admin', $area)->orderBy('produksi.tgl_produksi')
            ->groupBy('size')
            ->groupBy('tgl_produksi')
            ->where('Month(tgl_produksi)', $bulan)
            ->findAll();
    }
    public function existingData($insert)
    {
        return $this->select('id_produksi,qty_produksi')
            ->where('idapsperstyle', $insert['idapsperstyle'])->where('tgl_produksi', $insert['tgl_produksi'])->where('qty_produksi', $insert['qty_produksi'])
            ->where('shift', $insert['shift'])
            ->where('no_box', $insert['no_box'])
            ->first();
    }
    public function getProduksiPerhari($bulan)
    {
        return $this->select('DATE(tgl_produksi) as tgl_produksi, SUM(qty_produksi) as qty_produksi')
            ->where('MONTH(tgl_produksi)', $bulan)
            ->like('storage_akhir', '-')
            ->groupBy('DATE(tgl_produksi)')
            ->orderBy('tgl_produksi')
            ->findAll();
    }
    public function getProduksiPerArea($bulan, $area)
    {
        $result = $this->select('DATE(tgl_produksi) as tgl_produksi, SUM(qty_produksi) as qty_produksi')
            ->where('MONTH(tgl_produksi)', $bulan)
            ->where('admin', $area)

            ->like('storage_akhir', '-')
            ->groupBy('DATE(tgl_produksi)')
            ->orderBy('tgl_produksi')
            ->findAll();
        if (!$result) {
            $nul = [
                'tgl_produksi' => 0,
                'qty_produksi' => 0,

            ];
            return $nul;
        } else {
            return $result;
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
            ->groupBy('DATE(produksi.created_at), produksi.admin')
            ->orderBy('DATE(produksi.created_at)', 'DESC')
            ->findAll();
    }

    public function deleteSesuai(array $idaps)
    {
        return $this->whereIn('idapsperstyle', $idaps)->delete();
    }
    public function getDataForReset($area, $tanggal)
    {
        return $this->select('id_produksi,idapsperstyle,qty_produksi')
            ->where('admin', $area)
            ->where('tgl_produksi', $tanggal)
            ->findAll();
    }
}
