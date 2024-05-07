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
    protected $allowedFields    = ['id', 'judul', 'jarum', 'mesin', 'jumlah_hari', 'tanggal_awal', 'tanggal_akhir', 'start_mesin', 'stop_mesin', 'created_at', 'updated_at', 'deskripsi'];

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
    public function listPlan()
    {
        return $query = $this->select('created_at,judul,count(jarum) as jarum,sum(mesin) as mesin,max(jumlah_hari) as jumlah_hari')
            ->groupBy('judul,jarum')
            ->findAll();
    }
    public function jarumPlan($judul)
    {
        return $query = $this->select('created_at,id,jarum,mesin,jumlah_hari,tanggal_awal,tanggal_akhir,deskripsi')
            ->where('judul', $judul)
            ->groupBy('judul')
            ->findAll();
    }
    public function listmachine($id, $jarum)
    {
        return $query = $this->select('data_mesin.area,data_mesin.jarum,sum(data_mesin.total_mc) as total_mc,data_mesin.brand,sum(data_mesin.mesin_jalan) as mesin_jalan,pu,kebutuhan_mesin.id,kebutuhan_mesin.mesin as keb_mc,kebutuhan_mesin.deskripsi')
            ->from('data_mesin')
            ->where('data_mesin.jarum', $jarum)
            ->groupBy('data_mesin.brand,data_mesin.jarum,data_mesin.area,data_mesin.pu')
            ->orderBy('total_mc', 'Desc')
            ->findAll();
    }
}
