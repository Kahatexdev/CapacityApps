<?php

namespace App\Models;

use CodeIgniter\Model;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Sum;

class KebutuhanMesinModel extends Model
{
    protected $table            = 'kebutuhan_mesin';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id', 'judul', 'jarum', 'mesin', 'jumlah_hari', 'tanggal_awal', 'tanggal_akhir', 'created_at', 'updated_at'];

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

    public function getJudul()
    {
        $result = $this->select('*, SUM(mesin) as total')->groupBy('judul')->get()->getResultArray();
        return $result;
    }

    public function getData($judul)
    {
        $result = $this->where('judul', $judul)->distinct('judul')->orderBy('jarum', 'asc')->findAll();
        return $result;
    }
    public function tglPlan($judul)
    {
        $result = $this->select('created_at')->where('judul', $judul)->first();
        if ($result) {
            // Ubah format tanggal dari Y-m-d menjadi d-m-Y
            $created_at_formatted = date('d-F-Y', strtotime($result['created_at']));

            return $created_at_formatted;
        } else {
            return null;
        }
    }
    public function range($judul)
    {
        $result = $this->select('tanggal_awal, tanggal_akhir')->where('judul', $judul)->first();
        if ($result) {
            // Ubah format tanggal dari Y-m-d menjadi d-m-Y
            $range = [
                'awal' => date('d F Y', strtotime($result['tanggal_awal'])),
                'akhir' => date('d F Y', strtotime($result['tanggal_akhir']))
            ];

            return $range;
        } else {
            return null;
        }
    }
}
