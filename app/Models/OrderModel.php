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

        return $this->select('data_model.created_at, 
                            data_model.kd_buyer_order, 
                            data_model.no_model, 
                            apsperstyle.no_order, 
                            apsperstyle.machinetypeid, 
                            master_product_type.product_type, 
                            data_model.description, 
                            data_model.seam, 
                            data_model.leadtime, 
                            ROUND(SUM(apsperstyle.qty), 0) AS qty, 
                            ROUND(SUM(apsperstyle.sisa), 0) AS sisa, 
                            apsperstyle.delivery')
        ->join('apsperstyle', 'data_model.no_model = apsperstyle.mastermodel', 'left')
        ->join('master_product_type', 'data_model.id_product_type = master_product_type.id_product_type', 'left')
        ->where('no_model !=', '')
        ->groupBy('apsperstyle.delivery')
        ->groupBy('apsperstyle.machinetypeid')
        ->groupBy('data_model.no_model')
        ->orderBy('data_model.created_at', 'DESC')
        ->orderBy('data_model.no_model', 'ASC')
        ->orderBy('apsperstyle.delivery', 'ASC')
        ->findAll();

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
        return $this->select('data_model.kd_buyer_order, YEARWEEK(data_model.updated_at, 3) as week_number, SUM(apsperstyle.qty) as qty_turun')
            ->join('apsperstyle', 'data_model.no_model = apsperstyle.mastermodel')
            ->groupBy(['week_number', 'kd_buyer_order'])
            ->orderBy('week_number', 'DESC')
            ->findAll();
    }

    public function chartTurun()
    {
        $allResults = $this->select('sum(apsperstyle.qty) as qty_turun')
            ->select("CONCAT(
                    'Minggu ke ', CEIL(DAY(data_model.created_at) / 7), ' bulan ', 
                    MONTHNAME(data_model.created_at)
                 ) AS week_month", false)
            ->join('apsperstyle', 'data_model.no_model = apsperstyle.mastermodel')
            ->groupBy('week_month')
            ->orderBy('week_month', 'ASC')
            ->findAll();

        $totalPerBulan = [];

        foreach ($allResults as $result) {
            $week = $result['week_month'];
            $totalPerBulan[$week] = round($result['qty_turun'] / 24);
        }

        return [
            'details' => $allResults,
            'totals' => $totalPerBulan
        ];
    }

    public function getDetailTurunOrder($week, $buyer)
    {
        return $this->select('data_model.*, apsperstyle.machinetypeid, apsperstyle.mastermodel,apsperstyle.delivery, sum(apsperstyle.qty) as qty,apsperstyle.no_order')
            ->join('apsperstyle', 'data_model.no_model = apsperstyle.mastermodel')
            ->where("YEARWEEK(data_model.updated_at, 3)", $week)
            ->where('data_model.kd_buyer_order', $buyer)
            ->groupby('data_model.no_model')
            ->findAll();
    }
}
