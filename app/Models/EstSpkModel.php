<?php

namespace App\Models;

use CodeIgniter\Model;

class EstSpkModel extends Model
{
    protected $table            = 'estimasi_spk';
    protected $primaryKey       =
    'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['model', 'style', 'area', 'qty', 'status', 'keterangan'];

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


    public function cekStatus($model, $style, $area)
    {
        return $this
            ->where('model', $model)
            ->where('style', $style)
            ->where('area', $area)
            ->first();
    }

    public function getData($tgl = null, $noModel = null)
    {
        $builder = $this->select('estimasi_spk.*, DATE(created_at) AS tgl_buat, TIME(created_at) as jam')
            ->where('status', 'sudah');
        if (!empty($tgl)) {
            $builder->like('created_at', $tgl);
        }
        if (!empty($noModel)) {
            $builder->where('model', $noModel);
        }
        return $builder
            ->orderBy('created_at', 'ASC')
            ->findAll();
    }

    public function getHistory($area, $lastmonth)
    {
        return $this->where('area', $area)
            ->where('updated_at>', $lastmonth)
            ->findAll();
    }
    // public function getHistorySpk()
    // {
    //     return $this->select('estimasi_spk.*, DATE(updated_at) AS tgl_buat, TIME(created_at) as jam')
    //         ->whereIn('status', ['Ditolak', 'approved'])
    //         ->findAll();
    // }
    public function getStatusBulk(array $keys, $area)
    {
        return $this->where('area', $area)
            ->whereIn('CONCAT(model, "_", style)', $keys)
            ->findAll();
    }

    public function getHistorySpk($start, $length, $search, $orderColumn, $orderDir, $filters)
    {
        $builder = $this->db->table('estimasi_spk');

        // SELECT: boleh SQL function
        $builder->select("
        DATE(created_at) AS tgl_buat,
        TIME(created_at) AS jam,
        model,
        style,
        area,
        qty,
        status,
        keterangan
    ")->whereIn('status', ['Ditolak', 'approved']);

        if ($search) {
            $builder->groupStart()
                ->like('model', $search)
                ->orLike('style', $search)
                ->orLike('area', $search)
                ->orLike('status', $search)
                ->groupEnd();
        }

        if (!empty($filters['tgl'])) {
            $builder->where('DATE(created_at)', $filters['tgl']);
        }

        if (!empty($filters['no_model'])) {
            $builder->like('model', $filters['no_model']);
        }

        if (!empty($filters['style'])) {
            $builder->like('style', $filters['style']);
        }


        $builder->orderBy($orderColumn, $orderDir);
        $builder->limit($length, $start);

        return $builder->get()->getResultArray();
    }

    public function countHistorySpk()
    {
        return $this->db->table('estimasi_spk')->countAll();
    }

    public function countHistorySpkFiltered($search)
    {
        $builder = $this->db->table('estimasi_spk');

        if ($search) {
            $builder->groupStart()
                ->whereIn('status', ['Ditolak', 'approved'])
                ->like('model', $search)
                ->orLike('style', $search)
                ->orLike('area', $search)
                ->orLike('status', $search)
                ->groupEnd();
        }

        return $builder->countAllResults();
    }

    public function getHistorySpkExport($filters)
    {
        $builder = $this->db->table('estimasi_spk');

        // SELECT: boleh SQL function
        $builder->select("
            DATE(created_at) AS tgl_buat,
            TIME(created_at) AS jam,
            model,
            style,
            area,
            qty,
            status,
            keterangan
        ")->whereIn('status', ['Ditolak', 'approved']);
        // data filter
        if (!empty($filters['tgl'])) {
            $builder->where('DATE(created_at)', $filters['tgl']);
        }

        if (!empty($filters['no_model'])) {
            $builder->like('model', $filters['no_model']);
        }

        if (!empty($filters['style'])) {
            $builder->like('style', $filters['style']);
        }

        $builder->orderBy('tgl_buat, model, style');
        return $builder->get()->getResultArray();
    }
}
