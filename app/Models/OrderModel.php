<?php

namespace App\Models;

use CodeIgniter\Model;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Sum;

class OrderModel extends Model
{
    protected $table            = 'data_model';
    protected $primaryKey       = 'id_model';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id_model', 'id_booking', 'no_model', 'kd_buyer_order', 'id_product_type', 'seam', 'leadtime', 'description', 'created at', 'updated_at'];

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


    // Fungsi untuk mendapatkan semua data dari tabel
    public function getOrder()
    {
        return $this->findAll(); // Mengembalikan seluruh data
    }
    public function checkExist($no_model)
    {
        return $this->where('no_model', $no_model)->first();
    }
    public function tampilPerdelivery()
    {
        $builder = $this->db->table('data_model');

        $builder->select('data_model.*, mastermodel,no_order, machinetypeid, ROUND(SUM(QTy), 0) AS qty, ROUND(SUM(sisa), 0) AS sisa, factory, delivery, product_type');
        $builder->join('apsperstyle', 'data_model.no_model = apsperstyle.mastermodel', 'left');
        $builder->join('master_product_type', 'data_model.id_product_type = master_product_type.id_product_type', 'left');
        $builder->orderby('created_at', 'desc');
        $builder->orderby('no_model', 'asc');
        $builder->orderby('delivery', 'asc');
        $builder->groupBy('delivery,machinetypeid');
        $builder->groupBy('data_model.no_model');

        return $builder->get()->getResult();
    }
    public function tampilBelumImport()
    {
    $builder = $this->db->table('data_model');

    $builder->select('data_model.*, mastermodel,no_order, machinetypeid, ROUND(SUM(QTy), 0) AS qty, ROUND(SUM(sisa), 0) AS sisa, factory, delivery, product_type');
    $builder->join('apsperstyle', 'data_model.no_model = apsperstyle.mastermodel', 'left');
    $builder->join('master_product_type', 'data_model.id_product_type = master_product_type.id_product_type', 'left');
    $builder->where('qty IS NULL'); // Add this line to filter records where qty is null
    $builder->orderby('created_at', 'desc');
    $builder->orderby('no_model', 'asc');
    $builder->orderby('delivery', 'asc');
    $builder->groupBy('delivery');
    $builder->groupBy('data_model.no_model');

    return $builder->get()->getResult();
    }
    public function tampilPerModelBlmAdaArea()
    {
        $builder = $this->db->table('data_model');

        $builder->select('data_model.*, mastermodel,no_order, machinetypeid, ROUND(SUM(QTy), 0) AS qty, ROUND(SUM(sisa), 0) AS sisa, factory, delivery, product_type');
        $builder->join('apsperstyle', 'data_model.no_model = apsperstyle.mastermodel', 'left');
        $builder->join('master_product_type', 'data_model.id_product_type = master_product_type.id_product_type', 'left');
        $builder->where('factory', "Belum Ada Area");
        $builder->orderby('created_at', 'desc');
        $builder->orderby('no_model', 'asc');
        $builder->orderby('delivery', 'asc');
        $builder->groupBy('delivery');
        $builder->groupBy('data_model.no_model');

        return $builder->get()->getResult();
    }
    public function tampilPerjarum($jarum)
    {
        $builder = $this->db->table('data_model');

        $builder->select('data_model.*, mastermodel, no_order, machinetypeid, ROUND(SUM(QTy), 0) AS qty, ROUND(SUM(sisa), 0) AS sisa, factory, delivery, product_type');
        $builder->select('CONCAT(mastermodel, "/", machinetypeid) AS model_machine');
        $builder->join('apsperstyle', 'data_model.no_model = apsperstyle.mastermodel', 'left');
        $builder->join('master_product_type', 'data_model.id_product_type = master_product_type.id_product_type', 'left');
        $builder->where('machinetypeid', $jarum);
        $builder->orderby('created_at', 'desc');
        $builder->orderby('no_model', 'asc');
        $builder->orderby('delivery', 'asc');

        $results = $builder->groupBy('delivery')
                        ->groupBy('data_model.no_model')
                        ->groupBy('machinetypeid')
                        ->get()->getResult();

        $previous_model_machine = null;
        $counter = 1;

        foreach ($results as $result) {
            $current_model_machine = $result->model_machine;

            if ($current_model_machine != $previous_model_machine) {
                $counter = 1;
                $previous_model_machine = $current_model_machine;
            }

            // Append counter after the "/" with a space before machinetypeid
            $parts = explode('/', $current_model_machine);
            $result->model_machine = $parts[0] . '/' . $counter . ' ' . $parts[1];

            $counter++;
        }

        return $results;
    }

    public function tampilPerjarumBulan($bulan, $tahun, $jarum)
    {
        $builder = $this->db->table('data_model');

        $builder->select('data_model.*, mastermodel,no_order, machinetypeid, ROUND(SUM(QTy), 0) AS qty, ROUND(SUM(sisa), 0) AS sisa, factory, delivery, product_type');
        $builder->join('apsperstyle', 'data_model.no_model = apsperstyle.mastermodel', 'left');
        $builder->join('master_product_type', 'data_model.id_product_type = master_product_type.id_product_type', 'left');
        $builder->where('machinetypeid', $jarum);
        $builder->where('monthname(delivery)', $bulan);
        $builder->where('year(delivery)', $tahun);
        $builder->orderby('created_at', 'desc');
        $builder->orderby('no_model', 'asc');
        $builder->orderby('delivery', 'asc');
        $builder->groupBy('delivery');
        $builder->groupBy('data_model.no_model');
        $builder->groupBy('machinetypeid');

        return $builder->get()->getResult();
    }
    public function tampilPerarea($area)
    {
        $builder = $this->db->table('data_model');

        $builder->select('data_model.*, mastermodel,no_order, machinetypeid, ROUND(SUM(QTy), 0) AS qty, ROUND(SUM(sisa), 0) AS sisa, factory, delivery, product_type');
        $builder->join('apsperstyle', 'data_model.no_model = apsperstyle.mastermodel', 'left');
        $builder->join('master_product_type', 'data_model.id_product_type = master_product_type.id_product_type', 'left');
        $builder->where('factory', $area);
        $builder->orderby('created_at', 'desc');
        $builder->orderby('no_model', 'asc');
        $builder->orderby('delivery', 'asc');
        $builder->groupBy('delivery');
        $builder->groupBy('data_model.no_model');
        $builder->groupBy('factory');

        return $builder->get()->getResult();
    }
    public function getId($nomodel)
    {
        return $this->select('id_model')->where('no_model', $nomodel)->first();
    }

    public function getChild($idBooking)
    {
        return  $this->join('apsperstyle', 'data_model.no_model = apsperstyle.mastermodel', 'left')
            ->select('no_model, created_at, kd_buyer_order, SUM(qty) AS qtyOrder')
            ->groupBy('no_model')
            ->where('id_booking', $idBooking)
            ->findAll();
    }

    public function getTurunOrder()
    {
        $query = $this->select('data_model.kd_buyer_order, YEARWEEK(data_model.created_at, 3) as week_number, SUM(apsperstyle.qty) as qty_turun')
            ->join('apsperstyle', 'data_model.no_model = apsperstyle.mastermodel')
            ->groupBy(['week_number', 'kd_buyer_order'])
            ->orderBy('week_number', 'DESC')
            ->findAll();

        return $query;
    }

    public function chartTurun()
    {
        $allResults = $this->select('sum(apsperstyle.qty) as qty_turun')
        ->select("DATE_FORMAT(data_model.created_at, '%M-%Y') AS month_year", false)
        ->join('apsperstyle', 'data_model.no_model = apsperstyle.mastermodel')
        ->groupBy('month_year')
        ->orderBy('month_year', 'ASC')
        ->findAll();

        $totalPerBulan = [];

        foreach ($allResults as $result) {
            // Adding cancellation details to the array based on month
            $monthYear = $result['month_year'];
            $totalPerBulan[$monthYear] = round($result['qty_turun'] / 24);
        }

        return [
            'details' => $result,
            'totals' => $totalPerBulan
        ];
    }

    public function getDetailTurunOrder($week, $buyer)
    {
        $query = $this->select('data_model.*, apsperstyle.machinetypeid, apsperstyle.mastermodel,apsperstyle.delivery, sum(apsperstyle.qty) as qty,apsperstyle.no_order')
            ->join('apsperstyle', 'data_model.no_model = apsperstyle.mastermodel')
            ->where("CONCAT(YEAR(data_model.updated_at), LPAD(WEEK(data_model.updated_at), 2, '0'))", $week)
            ->where('data_model.kd_buyer_order', $buyer)
            ->findAll();

        return $query;
    }
}
