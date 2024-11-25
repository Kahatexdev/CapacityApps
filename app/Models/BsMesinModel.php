<?php

namespace App\Models;

use CodeIgniter\Model;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Sum;

class BsMesinModel extends Model
{
    protected $table            = 'bs_mesin';
    protected $primaryKey       = 'id_bsmc';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id_karyawan', 'nama_karyawan', 'shift', 'area', 'no_model', 'size', 'inisial', 'no_mesin', 'qty_pcs', 'qty_gram', 'tanggal_produksi', 'created_at', 'update_at'];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'update_at';
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


    public function bsDataKaryawan($id)
    {
        return $this->select('id_karyawan, nama_karyawan, sum(qty_gram), sum(qty_pcs)')
            ->where('id_karyawan', $id)
            ->groupBy('id_karyawan')
            ->findAll();
    }
    public function bsPeriode($start, $stop)
    {
        return $this->select('id_karyawan, nama_karyawan, sum(qty_gram), sum(qty_pcs)')
            ->where('tg', $id)
            ->groupBy('id_karyawan')
            ->findAll();
    }
}
