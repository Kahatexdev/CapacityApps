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
    protected $allowedFields    = ['id_booking', 'tgl_terima_booking', 'kd_buyer_booking', 'id_product_type', 'no_order', 'no_booking', 'desc', 'opd', 'delivery', 'qty_booking', 'sisa_booking', 'needle', 'seam', 'status', 'lead_time', 'ref_id', 'created_at', 'updated_at', 'keterangan'];

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
            ->where('no_booking ', $validate['no_pdk'])
            ->where('tgl_terima_booking', $validate['tgl_terima_booking'])
            ->where('delivery', $validate['delivery'])
            ->where('keterangan !=', 'Manual Cancel Booking')
            ->first();
        return $query;
    }
    public function getAllData()
    {
        return $this->select('data_booking.*, master_product_type.product_type')
            ->where('data_booking.keterangan !=', 'Manual Cancel Booking')
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
            ->where('data_booking.keterangan !=', 'Manual Cancel Booking')
            ->join('master_product_type', 'master_product_type.id_product_type = data_booking.id_product_type')
            ->groupBy('id_booking')
            ->findAll();
    }
    public function getDataPerjarumbulan($bulan, $tahun, $jarum)
    {
        return $this->select('data_booking.*, master_product_type.product_type')
            ->where('needle', $jarum)
            ->where('monthname(delivery)', $bulan)
            ->where('year(delivery)', $tahun)
            ->where('data_booking.keterangan !=', 'Manual Cancel Booking')
            ->join('master_product_type', 'master_product_type.id_product_type = data_booking.id_product_type')
            ->groupBy('id_booking')
            ->findAll();
    }
    public function getNeedle($idBooking)
    {
        $query = $this->select('needle')->where('id_booking', $idBooking)->first();
        return $query;
    }

    public function existingOrder($no_order)
    {
        return $this->where('no_order', $no_order)->where('keterangan !=', 'Manual Cancel Booking')->first();
    }
    public function getOrderJalan()
    {
        return $this->where('status', 'Aktif')->where('data_booking.keterangan !=', 'Manual Cancel Booking')->countAllResults();
    }
    public function getBookingMasuk()
    {
        $bulan = date('m');
        $year = date('Y');
        return $this->where("MONTH(tgl_terima_booking) =", $bulan)
            ->where("YEAR(tgl_terima_booking) =", $year)
            ->where('data_booking.keterangan !=', 'Manual Cancel Booking')
            ->countAllResults();
    }
    public function getBulan($jarum)
    {
        return $this->select("MONTHNAME(delivery) as bulan, YEAR(delivery) as tahun")
            ->where('needle', $jarum)
            ->groupBy('MONTHNAME(delivery), YEAR(delivery)')
            ->orderBy('tahun', 'ASC')
            ->orderBy('delivery', 'ASC')
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
            ->where('data_booking.keterangan !=', 'Manual Cancel Booking')
            ->get()
            ->getResultArray();

        // Inisialisasi array untuk menyimpan hasil yang dikelompokkan berdasarkan keterangan
        $total_qty = 0;

        // Menghitung total_qty dari hasil query
        foreach ($results as $result) {
            $total_qty += $result['total_qty'] ?? 0;
        }


        return $total_qty / 24;
    }

    public function getCancelBooking()
    {
        $query = $this->select('data_booking.*, SUM(data_cancel.qty_cancel) AS qty')
            ->select("CONCAT(YEAR(data_booking.updated_at), LPAD(WEEK(data_booking.updated_at, 3), 2, '0')) AS week_number", false)
            ->join('data_cancel', 'data_booking.id_booking = data_cancel.id_booking')
            ->groupBy(['data_booking.kd_buyer_booking', 'week_number'])
            ->orderBy('week_number', 'DESC')
            ->findAll();
        return $query;
    }

    public function getDetailCancelBooking($week, $buyer)
    {
        $query = $this->select('data_booking.*, data_cancel.qty_cancel, data_cancel.alasan')
            ->join('data_cancel', 'data_booking.id_booking = data_cancel.id_booking')
            ->where("CONCAT(YEARWEEK(data_booking.updated_at, 3))", $week) // Use YEARWEEK() function
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
            ->groupBy('month_year')
            ->orderBy('month_year', 'ASC')
            ->findAll();

        $totalPerBulan = [];

        foreach ($allResults as $result) {
            // Adding cancellation details to the array based on month
            $monthYear = $result['month_year'];
            $totalPerBulan[$monthYear] = round($result['qty'] / 24);
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
            ->where('data_booking.keterangan !=', 'Manual Cancel Booking')
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
        $result = $this->select('data_booking.delivery, master_product_type.konversi, master_product_type.product_type, sum(data_booking.sisa_booking) as sisa_booking')
            ->select('DATEDIFF(data_booking.delivery, CURDATE()) - (SELECT COUNT(tanggal) FROM data_libur WHERE tanggal BETWEEN CURDATE() AND data_booking.delivery) AS totalhari')
            ->join('master_product_type', 'data_booking.id_product_type = master_product_type.id_product_type')
            ->where('master_product_type.product_type', $type)
            ->where('master_product_type.jarum', $get['jarum'])
            ->where('sisa_booking > ', 0)
            ->where('status != ', 'Cancel Booking')
            ->where("data_booking.delivery > DATE_ADD(CURDATE(), INTERVAL 7 DAY)")
            ->where('data_booking.delivery >=', $get['start'])
            ->where('data_booking.delivery <=', $get['end'])
            ->where('data_booking.keterangan !=', 'Manual Cancel Booking')
            ->get()->getResultArray();


        return $result ?? "tidak Ada Data";
    }

    public function getIdForTransfer($data)
    {
        $result = $this->select('id_booking, qty_booking,sisa_booking')
            ->where('kd_buyer_booking', $data['kd_buyer'])
            ->where('no_booking', $data['no_booking'])
            ->where('no_order', $data['no_order'])
            ->where('delivery', $data['delivery'])
            ->where('data_booking.keterangan !=', 'Manual Cancel Booking')
            ->first();
        return $result;
    }

    public function getTotalBookingByJarum($cek)
    {
        $results = $this->groupBy('needle')
            ->select('needle, delivery, SUM(qty_booking) as total_booking, SUM(sisa_booking) AS sisa_booking')
            ->where('status!=', 'Cancel Booking')
            ->where('needle', $cek['jarum'])
            ->where('delivery >=', $cek['start'])
            ->where('delivery <=', $cek['end'])
            ->where('data_booking.keterangan !=', 'Manual Cancel Booking')
            ->get()
            ->getResultArray();

        return $results;
    }

    public function getTotalBookingByJarum2($cek)
    {
        $groupingProductType = 'CASE
                WHEN master_product_type.product_type LIKE \'SS-%\' THEN \'SS\'
                WHEN master_product_type.product_type LIKE \'S-%\' THEN \'S\'
                WHEN master_product_type.product_type LIKE \'F-%\' THEN \'F\'
                WHEN master_product_type.product_type LIKE \'NS-%\' THEN \'NS\'
                WHEN master_product_type.product_type LIKE \'KH-%\' THEN \'KH\'
                WHEN master_product_type.product_type LIKE \'TG-%\' THEN \'TG\'
                WHEN master_product_type.product_type LIKE \'HT-%\' THEN \'HT\'
                ELSE \'OTHER\'
            END AS product_group';
        $results = $this->select('data_booking.needle, data_booking.delivery, SUM(data_booking.qty_booking) as total_booking, SUM(data_booking.sisa_booking) AS sisa_booking, data_booking.id_product_type,' . $groupingProductType)
            ->join('master_product_type', 'master_product_type.id_product_type=data_booking.id_product_type', 'LEFT')
            ->where('data_booking.status!=', 'Cancel Booking')
            ->where('data_booking.needle', $cek['jarum'])
            ->where('data_booking.delivery >=', $cek['start'])
            ->where('data_booking.delivery <=', $cek['end'])
            ->where('data_booking.keterangan !=', 'Manual Cancel Booking')
            ->groupBy('product_group')
            ->get()
            ->getResultArray();
        return $results;
    }
    public function updateStatusBooking()
    {
        return $this->where('sisa_booking', 0)
            ->where('status !=', 'cancel booking')
            ->where('data_booking.keterangan !=', 'Manual Cancel Booking')
            ->set('status', 'Habis')
            ->update();
    }
    public function getDataBooking($validate)
    {
        $builder = $this->select('data_booking.*,master_product_type.product_type, data_model.seam AS proses_route')
            ->join('master_product_type', 'master_product_type.id_product_type=data_booking.id_product_type', 'left')
            ->join('data_model', 'data_model.id_booking=data_booking.id_booking', 'left')
            ->where('status !=', 'cancel booking')
            ->where('data_booking.keterangan !=', 'Manual Cancel Booking');

        if (!empty($validate['buyer'])) {
            $builder->like('data_booking.kd_buyer_booking', $validate['buyer']);
        }
        if (!empty($validate['jarum'])) {
            $builder->like('data_booking.needle', $validate['jarum']);
        }
        if (!empty($validate['tgl_booking']) && !empty($validate['tgl_booking_akhir'])) {
            $builder->where('data_booking.tgl_terima_booking >=', $validate['tgl_booking']);
            $builder->where('data_booking.tgl_terima_booking <=', $validate['tgl_booking_akhir']);
        }
        if (!empty($validate['tgl_booking']) && empty($validate['tgl_booking_akhir'])) {
            $builder->where('data_booking.tgl_terima_booking', $validate['tgl_booking']);
        }
        if (empty($validate['tgl_booking']) && !empty($validate['tgl_booking_akhir'])) {
            $builder->where('data_booking.tgl_terima_booking', $validate['tgl_booking_akhir']);
        }

        if (!empty($validate['awal'])) {
            $builder->where('data_booking.delivery >=', $validate['awal']);
        }

        if (!empty($validate['akhir'])) {
            $builder->where('data_booking.delivery <=', $validate['akhir']);
        }
        return $builder
            ->orderBy('data_booking.tgl_terima_booking, data_booking.needle, data_booking.delivery', 'ASC')
            ->findAll();
    }
    public function getSisaBookingMonth($month)
    {
        return $this->select('ROUND(SUM(sisa_booking/24)) AS sisa_booking')
            ->where("DATE_FORMAT(delivery, '%Y-%m')", $month)
            ->where('status !=', 'cancel booking')
            ->where('data_booking.keterangan !=', 'Manual Cancel Booking')
            ->first() ?? ['sisa_booking' => 0];
    }
}
