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
    protected $allowedFields    = ['id_model', 'id_booking', 'no_model', 'kd_buyer_order', 'id_product_type', 'seam', 'leadtime', 'description', 'created_at', 'updated_at'];

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


    // Fungsi untuk mendapatkan semua data dari tabel
    public function getOrder()
    {
        return $this->findAll(); // Mengembalikan seluruh data
    }
    public function checkExist($no_model)
    {
        return $this->where('no_model', $no_model)->first();
    }
    public function tampilPerdelivery($searchValue = null)
    {
        $builder = $this->db->table('data_model');

        // Selecting the columns
        $builder->select('data_model.created_at, 
                        data_model.kd_buyer_order, 
                        data_model.no_model, 
                        apsperstyle.no_order, 
                        apsperstyle.machinetypeid, 
                        apsperstyle.factory, 
                        master_product_type.product_type, 
                        data_model.description, 
                        data_model.seam, 
                        data_model.leadtime, 
                        ROUND(SUM(apsperstyle.qty), 0) AS qty, 
                        ROUND(SUM(apsperstyle.sisa), 0) AS sisa, 
                        apsperstyle.delivery')
            // Joining the required tables
            ->join('apsperstyle', 'data_model.no_model = apsperstyle.mastermodel', 'left')
            ->join('master_product_type', 'data_model.id_product_type = master_product_type.id_product_type', 'left')
            // Filtering out records where no_model is not empty
            ->where('no_model !=', '');

        // Apply search filter if search value is provided
        if ($searchValue !== null) {
            $builder->groupStart()
                ->like('data_model.created_at', $searchValue)
                ->orLike('data_model.kd_buyer_order', $searchValue)
                ->orLike('data_model.no_model', $searchValue)
                ->orLike('apsperstyle.no_order', $searchValue)
                ->orLike('apsperstyle.machinetypeid', $searchValue)
                ->orLike('master_product_type.product_type', $searchValue)
                ->orLike('data_model.description', $searchValue)
                ->orLike('data_model.seam', $searchValue)
                ->orLike('data_model.leadtime', $searchValue)
                ->orLike('apsperstyle.delivery', $searchValue)
                ->orLike('apsperstyle.factory', $searchValue)
                ->groupEnd();
        }

        // Grouping and ordering
        $builder->groupBy('apsperstyle.delivery')
            ->groupBy('apsperstyle.machinetypeid')
            ->groupBy('data_model.no_model')
            ->orderBy('data_model.created_at', 'DESC')
            ->orderBy('data_model.no_model', 'ASC')
            ->orderBy('apsperstyle.delivery', 'ASC');

        // Fetching data
        $query = $builder->get();
        return $query->getResult();
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
        $nextweek = date('Y-m-d', strtotime('+7 days'));
        $builder = $this->db->table('data_model');

        $builder->select('data_model.*, mastermodel,no_order, machinetypeid, ROUND(SUM(QTy), 0) AS qty, ROUND(SUM(sisa), 0) AS sisa, factory, delivery, product_type');
        $builder->join('apsperstyle', 'data_model.no_model = apsperstyle.mastermodel', 'left');
        $builder->join('master_product_type', 'data_model.id_product_type = master_product_type.id_product_type', 'left');
        $builder->where('factory', "Belum Ada Area");
        $builder->where('delivery >', $nextweek);
        $builder->groupBy('data_model.no_model, apsperstyle.machinetypeid');
        $builder->orderby('created_at', 'desc');
        $builder->orderby('no_model', 'asc');


        return $builder->get()->getResultArray();
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
    public function tampilPerBulan($bulan, $tahun)
    {
        $builder = $this->db->table('data_model');

        $builder->select('data_model.*, mastermodel,no_order, machinetypeid, ROUND(SUM(QTy), 0) AS qty, ROUND(SUM(sisa), 0) AS sisa, factory, delivery, product_type');
        $builder->join('apsperstyle', 'data_model.no_model = apsperstyle.mastermodel', 'left');
        $builder->join('master_product_type', 'data_model.id_product_type = master_product_type.id_product_type', 'left');
        $builder->where('monthname(delivery)', $bulan);
        $builder->where('year(delivery)', $tahun);
        $builder->where('production_unit !=', 'MJ');
        $builder->orderby('created_at', 'desc');
        $builder->orderby('no_model', 'asc');
        $builder->orderby('delivery', 'asc');
        $builder->groupBy('delivery');
        $builder->groupBy('data_model.no_model');
        $builder->groupBy('machinetypeid');

        return $builder->get()->getResultArray();
    }
    public function tampilPerarea($area)
    {
        $twomonth = date('Y-m-d', strtotime('60 days ago'));

        $builder = $this->db->table('data_model');

        $builder->select('data_model.*, mastermodel,no_order, machinetypeid, ROUND(SUM(QTy), 0) AS qty, ROUND(SUM(sisa), 0) AS sisa, factory, delivery, product_type');
        $builder->join('apsperstyle', 'data_model.no_model = apsperstyle.mastermodel', 'left');
        $builder->join('master_product_type', 'data_model.id_product_type = master_product_type.id_product_type', 'left');
        $builder->where('factory', $area);
        $builder->where('delivery >', $twomonth);
        $builder->orderby('created_at', 'desc');
        $builder->orderby('no_model', 'asc');
        $builder->orderby('delivery', 'asc');
        $builder->groupBy('machinetypeid');
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

    public function getdataSummaryPertgl($data)
    {
        // ambil data qty po
        $builder = $this->db->table('data_model');
        $subquery1 = $builder
            ->select('data_model.kd_buyer_order, apsperstyle.no_order, apsperstyle.smv, apsperstyle.idapsperstyle, apsperstyle.machinetypeid, apsperstyle.inisial, apsperstyle.mastermodel, apsperstyle.size, SUM(apsperstyle.qty) as qty, SUM(apsperstyle.po_plus) AS plus_packing, MAX(apsperstyle.delivery) AS max_delivery, SUM(apsperstyle.sisa) AS sisa')
            ->join('apsperstyle', 'data_model.no_model = apsperstyle.mastermodel');
        if (!empty($data['area'])) {
            $builder->where('apsperstyle.factory', $data['area']);
        }
        $subquery1 = $builder->groupBy('apsperstyle.machinetypeid, apsperstyle.mastermodel, apsperstyle.size')->getCompiledSelect();

        // Subquery untuk data_bs
        $builderBs = $this->db->table('data_bs');
        $subqueryBs = $builderBs
            ->select('apsperstyle.idapsperstyle, SUM(data_bs.qty) AS bs_prod')
            ->join('apsperstyle', 'apsperstyle.idapsperstyle = data_bs.idapsperstyle', 'left')
            ->groupBy('apsperstyle.machinetypeid')
            ->groupBy('apsperstyle.mastermodel')
            ->groupBy('apsperstyle.size')
            ->getCompiledSelect();

        // Subquery untuk produksi dan apsperstyle
        $builder2 = $this->db->table('produksi');
        $builder2->select('apsperstyle.machinetypeid, apsperstyle.mastermodel, apsperstyle.inisial, apsperstyle.size, apsperstyle.idapsperstyle, COUNT(DISTINCT produksi.tgl_produksi) AS running, MIN(produksi.tgl_produksi) AS start_mc, SUM(produksi.qty_produksi) AS qty_produksi, COUNT(produksi.no_mesin) AS jl_mc, GROUP_CONCAT(DISTINCT(produksi.area)) AS area')
            ->join('apsperstyle', 'apsperstyle.idapsperstyle = produksi.idapsperstyle', 'left')
            ->where('produksi.tgl_produksi IS NOT NULL')
            ->where('produksi.tgl_produksi!=', '0000-00-00');
        if (!empty($data['area'])) {
            $builder2->where('produksi.area', $data['area']);
        }
        $subquery2 = $builder2->groupBy('apsperstyle.machinetypeid, apsperstyle.mastermodel, apsperstyle.size')->getCompiledSelect();

        // Main query
        $mainQuery = $this->db->table('(' . $subquery1 . ') AS subquery')
            ->select('subquery.kd_buyer_order, subquery.no_order, subquery.smv, subquery.idapsperstyle, subquery.machinetypeid, subquery.mastermodel, subquery.inisial, subquery.size, subquery.qty, subquery.sisa,  COALESCE(subquery.plus_packing, 0) AS plus_packing, COALESCE(produksi_subquery.running, 0) AS running, produksi_subquery.start_mc, subquery.max_delivery, COALESCE(produksi_subquery.qty_produksi, 0) AS qty_produksi, COALESCE(bs_prod_subquery.bs_prod, 0) AS bs_prod, COALESCE(produksi_subquery.jl_mc, 0) AS jl_mc, produksi_subquery.area')
            ->join('(' . $subquery2 . ') AS produksi_subquery', 'subquery.machinetypeid = produksi_subquery.machinetypeid AND subquery.mastermodel = produksi_subquery.mastermodel AND subquery.size = produksi_subquery.size', 'left')
            ->join('(' . $subqueryBs . ') AS bs_prod_subquery', 'subquery.idapsperstyle = bs_prod_subquery.idapsperstyle', 'left');
        if (!empty($data['buyer'])) {
            $mainQuery->where('subquery.kd_buyer_order', $data['buyer']);
        }
        if (!empty($data['pdk'])) {
            $mainQuery->where('subquery.mastermodel', $data['pdk']);
        }
        if (!empty($data['jarum'])) {
            $mainQuery->like('subquery.machinetypeid', $data['jarum']);
        }
        $mainQuery->groupBy('subquery.machinetypeid, subquery.mastermodel, subquery.size')
            ->orderBy('area, subquery.machinetypeid, subquery.mastermodel, subquery.size', 'ASC');

        return $mainQuery->get()->getResultArray();
    }

    public function getProdSummaryPertgl($data)
    {
        $this->select('apsperstyle.idapsperstyle, apsperstyle.machinetypeid, apsperstyle.mastermodel, apsperstyle.size, SUM(produksi.qty_produksi) as qty_produksi, COUNT(DISTINCT produksi.no_mesin) AS jl_mc, produksi.tgl_produksi, produksi.no_mesin, shift_a, shift_b, shift_c')
            ->join('apsperstyle', 'apsperstyle.mastermodel = data_model.no_model', 'LEFT')
            ->join('produksi', 'produksi.idapsperstyle = apsperstyle.idapsperstyle', 'LEFT')
            ->where('produksi.tgl_produksi IS NOT NULL');

        if (!empty($data['buyer'])) {
            $this->where('data_model.kd_buyer_order', $data['buyer']);
        }
        if (!empty($data['area'])) {
            $this->where('produksi.area', $data['area']);
        }
        if (!empty($data['jarum'])) {
            $this->like('apsperstyle.machinetypeid', $data['jarum']);
        }
        if (!empty($data['pdk'])) {
            $this->where('data_model.no_model', $data['pdk']);
        }
        if (!empty($data['awal'])) {
            $this->where('produksi.tgl_produksi >=', $data['awal']);
        }
        if (!empty($data['akhir'])) {
            $this->where('produksi.tgl_produksi <=', $data['akhir']);
        }

        return $this->groupBy('apsperstyle.machinetypeid, data_model.no_model, apsperstyle.size, produksi.tgl_produksi')
            ->orderBy('apsperstyle.machinetypeid, data_model.no_model, apsperstyle.size, produksi.tgl_produksi', 'ASC')
            ->findAll();
    }

    public function getBuyer()
    {
        return $this->select('kd_buyer_order')->DISTINCT()
            ->where('kd_buyer_order !=', null)
            ->orderBy('kd_buyer_order', 'ASC')
            ->findAll();
    }

    public function getProdSummary($data)
    {
        $builder = $this->db->table('data_model');
        $subquery1 = $builder
            ->select('data_model.seam, apsperstyle.delivery, data_model.kd_buyer_order, apsperstyle.no_order, apsperstyle.machinetypeid, apsperstyle.inisial, apsperstyle.mastermodel, apsperstyle.size, apsperstyle.color, SUM(apsperstyle.qty) AS qty_deliv, SUM(apsperstyle.sisa) AS sisa')
            ->join('apsperstyle', 'data_model.no_model = apsperstyle.mastermodel');
        if (!empty($data['area'])) {
            $builder->where('apsperstyle.factory', $data['area']);
        }
        $subquery1 = $builder->groupBy('apsperstyle.machinetypeid, apsperstyle.mastermodel, apsperstyle.size, apsperstyle.delivery')->getCompiledSelect();

        // Subquery untuk produksi dan apsperstyle
        $builder2 = $this->db->table('produksi');
        $builder2->select('apsperstyle.machinetypeid, apsperstyle.mastermodel, apsperstyle.inisial, apsperstyle.size, apsperstyle.color, apsperstyle.delivery, COUNT(DISTINCT produksi.tgl_produksi) AS running, SUM(produksi.qty_produksi) as bruto, COUNT(produksi.no_mesin) AS jl_mc, GROUP_CONCAT(DISTINCT(produksi.area)) AS area')
            ->join('apsperstyle', 'apsperstyle.idapsperstyle = produksi.idapsperstyle', 'left')
            ->where('produksi.tgl_produksi IS NOT NULL');
        if (!empty($data['area'])) {
            $builder2->where('produksi.area', $data['area']);
        }
        $subquery2 = $builder2->groupBy('apsperstyle.machinetypeid, apsperstyle.mastermodel, apsperstyle.size, apsperstyle.delivery')->getCompiledSelect();

        // Main query
        $mainQuery = $this->db->table('(' . $subquery1 . ') AS subquery')
            ->select('subquery.seam, subquery.delivery, subquery.kd_buyer_order, subquery.no_order, subquery.machinetypeid, subquery.mastermodel, subquery.inisial, subquery.size, subquery.color, subquery.qty_deliv, subquery.sisa, produksi_subquery.running, produksi_subquery.bruto, produksi_subquery.jl_mc, produksi_subquery.area')
            ->join('(' . $subquery2 . ') AS produksi_subquery', 'subquery.machinetypeid = produksi_subquery.machinetypeid AND subquery.mastermodel = produksi_subquery.mastermodel AND subquery.size = produksi_subquery.size AND subquery.delivery = produksi_subquery.delivery', 'left');
        if (!empty($data['buyer'])) {
            $mainQuery->where('subquery.kd_buyer_order', $data['buyer']);
        }
        if (!empty($data['jarum'])) {
            $mainQuery->like('subquery.machinetypeid', $data['jarum']);
        }
        if (!empty($data['pdk'])) {
            $mainQuery->where('subquery.mastermodel', $data['pdk']);
        }
        $mainQuery->groupBy('subquery.machinetypeid, subquery.mastermodel, subquery.size, subquery.delivery')
            ->orderBy('subquery.machinetypeid, subquery.mastermodel, subquery.size, subquery.delivery', 'ASC');
        return $mainQuery->get()->getResultArray();
    }

    public function getTotalShipment($data)
    {
        $this->select('apsperstyle.machinetypeid, apsperstyle.mastermodel, apsperstyle.size, SUM(apsperstyle.qty) AS ttl_ship, SUM(apsperstyle.sisa) AS sisa')
            ->join('apsperstyle', 'apsperstyle.mastermodel = data_model.no_model', 'LEFT');

        if (!empty($data['buyer'])) {
            $this->where('data_model.kd_buyer_order', $data['buyer']);
        }
        if (!empty($data['area'])) {
            $this->where('apsperstyle.factory', $data['area']);
        }
        if (!empty($data['jarum'])) {
            $this->like('apsperstyle.machinetypeid', $data['jarum']);
        }
        if (!empty($data['pdk'])) {
            $this->where('data_model.no_model', $data['pdk']);
        }

        return $this->groupBy('apsperstyle.machinetypeid, data_model.no_model, apsperstyle.size')
            ->orderBy('apsperstyle.machinetypeid, data_model.no_model, apsperstyle.size', 'ASC')
            ->findAll();
    }

    public function getDataTimter($data)
    {
        $batas = date('Y-m-d', strtotime($data['awal'] . ' -30 days'));

        $this->select('data_model.seam, apsperstyle.delivery, data_model.kd_buyer_order, apsperstyle.idapsperstyle, apsperstyle.no_order, apsperstyle.factory, apsperstyle.machinetypeid, apsperstyle.mastermodel, apsperstyle.inisial, apsperstyle.size, apsperstyle.color, apsperstyle.smv, apsperstyle.delivery, SUM(apsperstyle.qty) AS qty, SUM(apsperstyle.sisa) AS sisa')
            ->join('apsperstyle', 'apsperstyle.mastermodel = data_model.no_model', 'LEFT')
            ->having('MAX(apsperstyle.delivery) >', $batas);
        if (!empty($data['area'])) {
            $this->where('apsperstyle.factory', $data['area']);
        }
        if (!empty($data['jarum'])) {
            $this->like('apsperstyle.machinetypeid', $data['jarum']);
        }
        if (!empty($data['pdk'])) {
            $this->where('data_model.no_model', $data['pdk']);
        }

        return $this->groupBy('apsperstyle.machinetypeid, apsperstyle.mastermodel, apsperstyle.size')
            ->orderBy('apsperstyle.machinetypeid, apsperstyle.mastermodel, apsperstyle.size', 'ASC')
            ->findAll();
    }

    public function getQtyPOTimter($data)
    {
        $batas = date('Y-m-d', strtotime($data['awal'] . ' -30 days'));

        $this->select('apsperstyle.idapsperstyle, apsperstyle.machinetypeid, apsperstyle.mastermodel, apsperstyle.inisial, apsperstyle.size, MAX(apsperstyle.delivery) AS delivery, SUM(apsperstyle.qty) AS qty')
            ->join('apsperstyle', 'apsperstyle.mastermodel = data_model.no_model', 'LEFT')
            ->having('MAX(apsperstyle.delivery) >', $batas);
        if (!empty($data['jarum'])) {
            $this->like('apsperstyle.machinetypeid', $data['jarum']);
        }
        if (!empty($data['pdk'])) {
            $this->where('data_model.no_model', $data['pdk']);
        }

        return $this->groupBy('apsperstyle.machinetypeid, data_model.no_model, apsperstyle.size')
            ->orderBy('apsperstyle.machinetypeid, data_model.no_model, apsperstyle.size', 'ASC')
            ->findAll();
    }

    public function getDetailProdTimter($data)
    {
        $this->select('apsperstyle.idapsperstyle, apsperstyle.seam, data_model.kd_buyer_order, apsperstyle.no_order, apsperstyle.machinetypeid, apsperstyle.mastermodel, apsperstyle.inisial, apsperstyle.size, apsperstyle.color, apsperstyle.smv, apsperstyle.delivery, SUM(DISTINCT apsperstyle.qty) AS qty, SUM(DISTINCT apsperstyle.sisa) AS sisa, produksi.area, SUM(COALESCE(produksi.qty_produksi, 0)) AS qty_produksi, COUNT(DISTINCT produksi.tgl_produksi) AS running, COUNT(DISTINCT produksi.no_mesin) AS jl_mc, produksi.tgl_produksi, produksi.no_mesin, SUM(CASE WHEN produksi.no_label > 3000 THEN produksi.qty_produksi ELSE 0 END) AS pa, SUM(CASE WHEN produksi.no_label < 3000 THEN produksi.shift_a ELSE 0 END) AS shift_a, SUM(CASE WHEN produksi.no_label < 3000 THEN produksi.shift_b ELSE 0 END) AS shift_b, SUM(CASE WHEN produksi.no_label < 3000 THEN produksi.shift_c ELSE 0 END) AS shift_c')
            ->join('apsperstyle', 'apsperstyle.mastermodel = data_model.no_model', 'LEFT')
            ->join('produksi', 'produksi.idapsperstyle = apsperstyle.idapsperstyle', 'LEFT')
            ->where('produksi.no_mesin !=', 'STOK PAKING')
        ;

        if (!empty($data['area'])) {
            $this->where('produksi.area', $data['area']);
        }
        if (!empty($data['jarum'])) {
            $this->like('apsperstyle.machinetypeid', $data['jarum']);
        }
        if (!empty($data['pdk'])) {
            $this->where('data_model.no_model', $data['pdk']);
        }
        if (!empty($data['awal'])) {
            $this->where('produksi.tgl_produksi =', $data['awal']);
        }

        return $this->groupBy('apsperstyle.machinetypeid, apsperstyle.mastermodel, apsperstyle.size, apsperstyle.delivery, produksi.tgl_produksi, produksi.no_mesin')
            ->orderBy('apsperstyle.machinetypeid, apsperstyle.mastermodel, apsperstyle.size, produksi.no_mesin', 'ASC')
            ->findAll();
    }

    public function getProductTypeByModel($noModel)
    {
        return $this->select('data_model.kd_buyer_order, master_product_type.product_type')
            ->join('master_product_type', 'master_product_type.id_product_type=data_model.id_product_type')
            ->where('data_model.no_model', $noModel)
            ->first();
    }
    public function getSummaryBsPertgl($data)
    {
        $this->select('apsperstyle.idapsperstyle, apsperstyle.machinetypeid, apsperstyle.mastermodel, apsperstyle.size, (SELECT SUM(qty_pcs) 
         FROM bs_mesin AS b 
         WHERE b.no_model = apsperstyle.mastermodel 
           AND b.area = apsperstyle.factory 
           AND b.size = apsperstyle.size 
           AND b.tanggal_produksi = bs_mesin.tanggal_produksi
        ) AS qty_pcs,
        (SELECT SUM(qty_gram) 
         FROM bs_mesin AS b 
         WHERE b.no_model = apsperstyle.mastermodel 
           AND b.area = apsperstyle.factory 
           AND b.size = apsperstyle.size 
           AND b.tanggal_produksi = bs_mesin.tanggal_produksi
        ) AS qty_gram, COUNT(DISTINCT bs_mesin.no_mesin) AS jl_mc, bs_mesin.tanggal_produksi, bs_mesin.area, bs_mesin.inisial, bs_mesin.no_mesin, shift')
            ->join('apsperstyle', 'apsperstyle.mastermodel = data_model.no_model', 'LEFT')
            ->join(
                'bs_mesin',
                'apsperstyle.factory = bs_mesin.area AND apsperstyle.mastermodel = bs_mesin.no_model AND apsperstyle.size = bs_mesin.size',
                'left'
            )
            ->where('bs_mesin.tanggal_produksi IS NOT NULL');

        if (!empty($data['buyer'])) {
            $this->where('data_model.kd_buyer_order', $data['buyer']);
        }
        if (!empty($data['area'])) {
            $this->where('bs_mesin.area', $data['area']);
        }
        if (!empty($data['jarum'])) {
            $this->like('apsperstyle.machinetypeid', $data['jarum']);
        }
        if (!empty($data['pdk'])) {
            $this->where('data_model.no_model', $data['pdk']);
        }
        if (!empty($data['awal'])) {
            $this->where('bs_mesin.tanggal_produksi >=', $data['awal']);
        }
        if (!empty($data['akhir'])) {
            $this->where('bs_mesin.tanggal_produksi <=', $data['akhir']);
        }

        return $this->groupBy('apsperstyle.machinetypeid, data_model.no_model, apsperstyle.size, bs_mesin.tanggal_produksi')
            ->orderBy('apsperstyle.machinetypeid, data_model.no_model, apsperstyle.size, bs_mesin.tanggal_produksi', 'ASC')
            ->findAll();
    }

    public function getDataPph($area, $nomodel, $size)
    {
        return $this->select('apsperstyle.machinetypeid, apsperstyle.factory, data_model.no_model, apsperstyle.size, apsperstyle.inisial, (SELECT SUM(qty) FROM apsperstyle AS a WHERE a.mastermodel = data_model.no_model AND a.factory = apsperstyle.factory AND a.size = apsperstyle.size) AS qty, 
        (SELECT SUM(sisa) FROM apsperstyle AS a WHERE a.mastermodel = data_model.no_model AND a.factory = apsperstyle.factory AND a.size = apsperstyle.size) AS sisa, (SELECT SUM(po_plus) FROM apsperstyle AS a WHERE a.mastermodel = data_model.no_model AND a.factory = apsperstyle.factory AND a.size = apsperstyle.size) AS po_plus, COALESCE(SUM(produksi.qty_produksi), 0) AS bruto')
            ->join('apsperstyle', 'apsperstyle.mastermodel=data_model.no_model', 'left')
            ->join('produksi', 'apsperstyle.idapsperstyle=produksi.idapsperstyle', 'left')
            ->where('apsperstyle.factory', $area)
            ->where('data_model.no_model', $nomodel)
            ->where('apsperstyle.size', $size)
            ->groupBy('apsperstyle.size')
            ->orderBy('apsperstyle.inisial', 'ASC')
            ->first();
    }


    public function getDataBsSetting() {}

    public function getStartMc($model)
    {
        return $this->select('data_model.kd_buyer_order, data_model.no_model, 
                      MIN(apsperstyle.delivery) AS delivery_awal, 
                      MAX(apsperstyle.delivery) AS delivery_akhir, 
                      COALESCE(MIN(tanggal_planning.start_mesin), NULL) AS start_mc')
            ->join('apsperstyle', 'data_model.no_model = apsperstyle.mastermodel', 'left')
            ->join('detail_planning', 'detail_planning.model = data_model.no_model', 'left')
            ->join('estimated_planning', 'estimated_planning.id_detail_pln = detail_planning.id_detail_pln', 'left')
            ->join('tanggal_planning', 'tanggal_planning.id_est_qty = estimated_planning.id_est_qty', 'left')
            ->where('data_model.no_model', $model)
            ->first();
    }
}
