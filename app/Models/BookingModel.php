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
    protected $allowedFields    = ['id_booking', 'tgl_terima_booking', 'kd_buyer_booking', 'id_product_type', 'no_order', 'no_booking', 'desc', 'opd', 'delivery', 'qty_booking', 'sisa_booking', 'needle', 'seam', 'status', 'lead_time', 'ref_id', 'created_at', 'updated_at'];

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
    public function getChild($idBooking)
    {
        return $this->where('ref_id', $idBooking)->findAll();
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

    // Plan Jarum NORMAL
    public function getPlanJarum($cek, $productType)
    {
        $results = $this
            ->join('master_product_type', 'master_product_type.id_product_type = data_booking.id_product_type')
            ->groupBy('data_booking.delivery, master_product_type.product_type')
            ->select('data_booking.delivery, master_product_type.product_type, SUM(data_booking.sisa_booking) AS total_qty')
            ->where('data_booking.needle', $cek['jarum'])
            ->where('data_booking.delivery >=', $cek['start'])
            ->where('data_booking.delivery <=', $cek['end'])
            ->where('master_product_type.product_type', $productType)
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



    public function getCancelBooking()
    {
        $allResults = $this
            ->select('data_booking.*, SUM(data_cancel.qty_cancel) AS qty')
            ->select('YEARWEEK(data_booking.updated_at) AS week_number')
            ->join('data_cancel', 'data_booking.id_booking = data_cancel.id_booking')
            ->where('status', 'Cancel Booking')
            ->groupBy('data_booking.kd_buyer_booking, week_number')
            ->orderBy('week_number', 'Desc')
            ->findAll();

        return $allResults;
    }

    public function getDetailCancelBooking($week, $buyer)
    {
        $query = $this->select('data_booking.*, data_cancel.qty_cancel, data_cancel.alasan')
            ->join('data_cancel', 'data_booking.id_booking = data_cancel.id_booking')
            ->where("CONCAT(YEAR(data_booking.updated_at), LPAD(WEEK(data_booking.updated_at), 2, '0'))", $week)
            ->where('data_booking.kd_buyer_booking', $buyer)
            ->findAll();

        return $query;
    }


    public function chartCancel()
    {

        $allResults = $this
            ->select('SUM(data_cancel.qty_cancel) AS qty')
            ->select("DATE_FORMAT(data_booking.updated_at, '%M-%Y') AS month_year", false)
            ->join('data_cancel', 'data_booking.id_booking = data_cancel.id_booking')
            ->where('status', 'Cancel Booking')
            ->groupBy('data_booking.kd_buyer_booking, month_year')
            ->orderBy('month_year', 'ASC')
            ->findAll();

        $totalPerBulan = [];

        foreach ($allResults as $result) {
            // Adding cancellation details to the array based on month
            $monthYear = $result['month_year'];
            $totalPerBulan[$monthYear] = $result['qty'] / 24;
        }

        return [
            'details' => $result,
            'totals' => $totalPerBulan
        ];
    }
    public function getTurunOrder()
    {
        $allResults = $this
            ->select('*')
            ->where('status', 'Aktif')
            ->orderBy('updated_at', 'ASC')
            ->findAll();

        $results = [];
        $totalPerBulan = [];

        foreach ($allResults as $result) {
            $month = date('F', strtotime($result['updated_at']));
            // Menambahkan detail pembatalan ke dalam array berdasarkan bulan
            if (!isset($results[$month])) {
                $results[$month] = [];
                $totalPerBulan[$month] = 0;
            }
            $results[$month][] = $result;
            // Menghitung total pembatalan per bulan
            $totalPerBulan[$month]++;
        }

        return [
            'details' => $results,
            'totals' => $totalPerBulan
        ];
    }
    public function hitungKebutuhanMC($get, $type)
    {
        $result = $this->join('master_product_type', 'data_booking.id_product_type = master_product_type.id_product_type')
            ->where('master_product_type.jarum', $get['jarum'])
            ->where('master_product_type.product_type', $type)
            ->where('data_booking.delivery >=', $get['start'])
            ->where('data_booking.delivery <=', $get['end'])
            ->select('delivery,master_product_type.konversi,master_product_type.product_type, data_booking.sisa_booking,   DATEDIFF(delivery, CURDATE()) - 
            (SELECT COUNT(tanggal) FROM data_libur WHERE tanggal BETWEEN CURDATE() AND data_booking.delivery)-3 AS totalhari')->get()->getResultArray();

        return $result ?? "tidak Ada Data";
    }
}
