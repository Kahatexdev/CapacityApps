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
    protected $allowedFields    = ['id', 'judul', 'jarum', 'sisa', 'mesin', 'jumlah_hari', 'tanggal_awal', 'tanggal_akhir', 'start_mesin', 'stop_mesin', 'created_at', 'updated_at', 'deskripsi'];

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

    public function getOrder()
    {
        $result = $this->select('*, SUM(mesin) as total')->where('deskripsi', 'ORDER')->groupBy('judul')->get()->getResultArray();
        return $result;
    }
    public function getBooking()
    {
        $result = $this->select('*, SUM(mesin) as total')->where('deskripsi', 'BOOKING')->groupBy('judul')->get()->getResultArray();
        return $result;
    }

    public function getDataOrder($judul)
    {
        $result = $this->where('judul', $judul)
            ->where('deskripsi', 'ORDER')
            ->distinct('judul')->orderBy('jarum', 'asc')->groupBy('deskripsi,jarum')
            ->findAll();
        return $result;
    }
    public function getDataBooking($judul)
    {
        $result = $this->where('judul', $judul)
            ->where('deskripsi', 'BOOKING')
            ->distinct('judul')->orderBy('jarum', 'asc')->groupBy('deskripsi,jarum')
            ->findAll();
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
    public function listPlan()
    {
        return $this->select('created_at,judul,count(jarum) as jarum,sum(mesin) as mesin,max(jumlah_hari) as jumlah_hari')
            ->groupBy('judul')
            ->findAll();
    }
    public function jarumPlan($judul)
    {
        return $this->select('kebutuhan_mesin.created_at, kebutuhan_mesin.id, kebutuhan_mesin.jarum, kebutuhan_mesin.mesin, kebutuhan_mesin.jumlah_hari, kebutuhan_mesin.tanggal_awal, kebutuhan_mesin.tanggal_akhir, kebutuhan_mesin.deskripsi, sum(mesin_planning.mc_nyala) as mc_nyala')
            ->join('mesin_planning', 'kebutuhan_mesin.id = mesin_planning.id_kebutuhan_mesin', 'left')
            ->where('judul', $judul)
            ->groupBy('judul,jarum,status,deskripsi')
            ->findAll();
    }

    public function listmachine($id, $jarum)
    {
        return $this->select('data_mesin.area,data_mesin.jarum,sum(data_mesin.total_mc) as total_mc,data_mesin.brand,sum(data_mesin.mesin_jalan) as mesin_jalan,pu,kebutuhan_mesin.id,kebutuhan_mesin.mesin as keb_mc,kebutuhan_mesin.deskripsi')
            ->from('data_mesin')
            ->where('data_mesin.jarum', $jarum)
            ->groupBy('data_mesin.brand,data_mesin.jarum,data_mesin.area,data_mesin.pu')
            ->orderBy('total_mc', 'Desc')
            ->findAll();
    }
    public function cekData($cek)
    {
        return $this->where('judul', $cek['judul'])
            ->where('jarum', $cek['jarum'])
            ->where('deskripsi', $cek['desk'])
            ->first();
    }
}
