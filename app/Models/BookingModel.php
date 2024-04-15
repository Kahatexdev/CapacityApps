<?php

namespace App\Models;

use CodeIgniter\Model;

class BookingModel extends Model
{
    protected $table            = 'data_booking';
    protected $primaryKey       = 'id_booking';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id_booking', 'tgl_terima_booking', 'kd_buyer_booking', 'id_product_type', 'no_order', 'no_booking', 'desc', 'opd', 'delivery', 'qty_booking', 'sisa_booking', 'needle', 'seam', 'status', 'lead_time', 'ref_id'];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = false;
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

    public function checkExist($validate)
    {
        $query = $this->where('no_order', $validate['no_order'])
            ->where('no_booking ', $validate['no_pdk'])->first();
        return $query;
    }
    public function getAllData()
    {
        return $this->select('data_booking.*, master_product_type.product_type')
            ->join('master_product_type', 'master_product_type.id_product_type = data_booking.id_product_type')
            ->findAll();
    }
    public function getDataById($idBooking)
    {
        return $this->select('data_booking.*, master_product_type.product_type')
            ->where('id_booking', $idBooking)
            ->join('master_product_type', 'master_product_type.id_product_type = data_booking.id_product_type')
            ->first();
    }
    public function getDataPerjarum($jarum)
    {
        return $this->select('data_booking.*, master_product_type.product_type')
            ->where('needle', $jarum)
            ->join('master_product_type', 'master_product_type.id_product_type = data_booking.id_product_type')
            ->findAll();
    }
    public function getDataPerjarumbulan($bulan, $tahun, $jarum)
    {
        return $this->select('data_booking.*, master_product_type.product_type')
            ->where('needle', $jarum)
            ->where('monthname(delivery)', $bulan)
            ->where('year(delivery)', $tahun)
            ->join('master_product_type', 'master_product_type.id_product_type = data_booking.id_product_type')
            ->findAll();
    }
    public function getNeedle($idBooking)
    {
        $query = $this->select('needle')->where('id_booking', $idBooking)->first();
        return $query;
    }

    public function existingOrder($no_order)
    {
        return $this->where('no_order', $no_order)->first();
    }
    public function getOrderJalan()
    {
        return $this->where('status', 'Aktif')->countAllResults();
    }
    public function getBookingMasuk()
    {
        $bulan = date('m');

        return $this->where("MONTH(tgl_terima_booking) =", $bulan)->countAllResults();
    }
    public function getBulan($jarum)
    {
        return $this->select("MONTHNAME(delivery) as bulan, YEAR(delivery) as tahun")
            ->where('needle', $jarum)
            ->groupBy('MONTHNAME(delivery), YEAR(delivery)')
            ->findAll();
    }


    public function getPlanJarumNs($cek)
    {
        $results = $this
            ->join('master_product_type', 'master_product_type.id_product_type = data_booking.id_product_type')
            ->groupBy('data_booking.delivery, master_product_type.keterangan')
            ->select('data_booking.delivery, master_product_type.keterangan, SUM(data_booking.sisa_booking) AS total_qty')
            ->where('data_booking.needle', $cek['jarum'])
            ->where('data_booking.delivery >=', $cek['start'])
            ->where('data_booking.delivery <=', $cek['end'])
            ->where('master_product_type.keterangan', "Normal Sock")
            ->get()
            ->getResultArray();

        // Inisialisasi array untuk menyimpan hasil yang dikelompokkan berdasarkan keterangan
        $total_qty = 0;

        // Menghitung total_qty dari hasil query
        foreach ($results as $result) {
            $total_qty += $result['total_qty'] ?? 0;
        }


        return $total_qty;
    }

    public function getPlanJarumSs($cek)
    {
        $results = $this
            ->join('master_product_type', 'master_product_type.id_product_type = data_booking.id_product_type')
            ->groupBy('data_booking.delivery, master_product_type.keterangan')
            ->select('data_booking.delivery, master_product_type.keterangan, SUM(data_booking.sisa_booking) AS total_qty')
            ->where('data_booking.needle', $cek['jarum'])
            ->where('data_booking.delivery >=', $cek['start'])
            ->where('data_booking.delivery <=', $cek['end'])
            ->where('master_product_type.keterangan', "Sneaker")
            ->get()
            ->getResultArray();
        // Inisialisasi array untuk menyimpan hasil yang dikelompokkan berdasarkan keterangan
        $total_qty = 0;
        // Menghitung total_qty dari hasil query
        foreach ($results as $result) {
            $total_qty += $result['total_qty'] ?? 0;
        }
        return $total_qty;
    }
    public function getPlanJarumKh($cek)
    {
        $results = $this
            ->join('master_product_type', 'master_product_type.id_product_type = data_booking.id_product_type')
            ->groupBy('data_booking.delivery, master_product_type.keterangan')
            ->select('data_booking.delivery, master_product_type.keterangan, SUM(data_booking.sisa_booking) AS total_qty')
            ->where('data_booking.needle', $cek['jarum'])
            ->where('data_booking.delivery >=', $cek['start'])
            ->where('data_booking.delivery <=', $cek['end'])
            ->where('master_product_type.keterangan', "Knee High")
            ->get()
            ->getResultArray();
        // Inisialisasi array untuk menyimpan hasil yang dikelompokkan berdasarkan keterangan
        $total_qty = 0;
        // Menghitung total_qty dari hasil query
        foreach ($results as $result) {
            $total_qty += $result['total_qty'] ?? 0;
        }
        return $total_qty;
    }
    public function getPlanJarumFs($cek)
    {
        $results = $this
            ->join('master_product_type', 'master_product_type.id_product_type = data_booking.id_product_type')
            ->groupBy('data_booking.delivery, master_product_type.keterangan')
            ->select('data_booking.delivery, master_product_type.keterangan, SUM(data_booking.sisa_booking) AS total_qty')
            ->where('data_booking.needle', $cek['jarum'])
            ->where('data_booking.delivery >=', $cek['start'])
            ->where('data_booking.delivery <=', $cek['end'])
            ->where('master_product_type.keterangan', "Footies")
            ->get()
            ->getResultArray();
        // Inisialisasi array untuk menyimpan hasil yang dikelompokkan berdasarkan keterangan
        $total_qty = 0;
        // Menghitung total_qty dari hasil query
        foreach ($results as $result) {
            $total_qty += $result['total_qty'] ?? 0;
        }
        return $total_qty;
    }
    public function getPlanJarumT($cek)
    {
        $results = $this
            ->join('master_product_type', 'master_product_type.id_product_type = data_booking.id_product_type')
            ->groupBy('data_booking.delivery, master_product_type.keterangan')
            ->select('data_booking.delivery, master_product_type.keterangan, SUM(data_booking.sisa_booking) AS total_qty')
            ->where('data_booking.needle', $cek['jarum'])
            ->where('data_booking.delivery >=', $cek['start'])
            ->where('data_booking.delivery <=', $cek['end'])
            ->where('master_product_type.keterangan', "Tight")
            ->get()
            ->getResultArray();
        // Inisialisasi array untuk menyimpan hasil yang dikelompokkan berdasarkan keterangan
        $total_qty = 0;
        // Menghitung total_qty dari hasil query
        foreach ($results as $result) {
            $total_qty += $result['total_qty'] ?? 0;
        }
        return $total_qty;
    }
}
