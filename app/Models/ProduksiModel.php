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
    protected $allowedFields    = ['id_produksi', 'idapsperstyle', 'tgl_produksi', 'qty_produksi', 'bagian', 'storage_awal', 'storage_akhir', 'bs_prod', 'katefori_bs', 'no_box', 'no_label', 'no_mesin', 'created_at', 'updated_at', 'admin', 'kode_shipment', 'delivery', 'shift'];

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

    public function getProduksi($area)
    {
        return $this->join('apsperstyle', 'apsperstyle.idapsperstyle= data_produksi.idapsperstyle')
            ->select('*')->where('apsperstyle.factory', $area)->orderBy('data_produksi.tgl_produksi')->findAll();
    }
    public function existingData($insert)
    {
        return $this->where('idapsperstyle', $insert['idapsperstyle'])->where('tgl_produksi', $insert['tgl_produksi'])->where('qty_produksi', $insert['qty_produksi'])
            ->where('shift', $insert['shift'])
            ->where('no_box', $insert['no_box'])
            ->first();
    }
    public function getProduksiPerhari($bulan)
    {
        return $this->select('*')->where('MONTH(tgl_produksi)', $bulan)->orderBy('tgl_produksi')->findAll();
    }
}
