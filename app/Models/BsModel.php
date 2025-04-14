<?php

namespace App\Models;

use CodeIgniter\Model;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Month;

use function PHPUnit\Framework\isEmpty;

class BsModel extends Model
{
    protected $table            = 'data_bs';
    protected $primaryKey       = 'idbs';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['idapsperstyle', 'no_label', 'area', 'tgl_instocklot', 'no_box', 'qty', 'kode_deffect', 'created_at', 'updated_at', 'id_produksi', 'no_model', 'size', 'delivery'];

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


    public function getDataBs()
    {
        return $this->select('no_label, no_box, area, apsperstyle.mastermodel, apsperstyle.size')
            ->join('apsperstyle', 'apsperstyle.idapsperstyle = data_bs.idapsperstyle')
            ->groupBy('area')
            ->findAll();
    }
    public function getDataBsFilter($theData)
    {
        $this->select('apsperstyle.idapsperstyle, data_model.kd_buyer_order, apsperstyle.mastermodel, apsperstyle.size, data_bs.*,sum(data_bs.qty) as qty, master_deffect.Keterangan')
            ->join('apsperstyle', 'apsperstyle.idapsperstyle = data_bs.idapsperstyle')
            ->join('master_deffect', 'master_deffect.kode_deffect = data_bs.kode_deffect')
            ->join('data_model', 'data_model.no_model = apsperstyle.mastermodel')
            ->where('tgl_instocklot >=', $theData['awal'])
            ->where('tgl_instocklot <=', $theData['akhir']);

        if (!empty($theData['pdk'])) {
            $this->where('apsperstyle.mastermodel', $theData['pdk']);
        }
        if (!empty($theData['area'])) {
            $this->where('data_bs.area', $theData['area']);
        }
        if (!empty($theData['buyer'])) {
            $this->where('data_model.kd_buyer_order', $theData['buyer']);
        }

        $data = $this->groupBy('tgl_instocklot, apsperstyle.size, data_bs.kode_deffect')->findAll();
        return $data;
    }

    public function  totalBs($theData)
    {

        $this->select('sum(data_bs.qty) as qty')
            ->join('apsperstyle', 'apsperstyle.idapsperstyle = data_bs.idapsperstyle')
            ->join('master_deffect', 'master_deffect.kode_deffect = data_bs.kode_deffect')
            ->join('data_model', 'data_model.no_model = apsperstyle.mastermodel')
            ->where('tgl_instocklot >=', $theData['awal'])
            ->where('tgl_instocklot <=', $theData['akhir']);

        if (!empty($theData['pdk'])) {
            $this->where('apsperstyle.mastermodel', $theData['pdk']);
        }
        if (!empty($theData['area'])) {
            $this->where('area', $theData['area']);
        }
        if (!empty($theData['buyer'])) {
            $this->where('data_model.kd_buyer_order', $theData['buyer']);
        }

        $result = $this->first();
        return $result['qty'] ?? '0';
    }
    public function chartData($theData)
    {
        $this->select('master_deffect.Keterangan, sum(data_bs.qty) as qty, ')
            ->join('apsperstyle', 'apsperstyle.idapsperstyle = data_bs.idapsperstyle')
            ->join('master_deffect', 'master_deffect.kode_deffect = data_bs.kode_deffect')
            ->join('data_model', 'data_model.no_model = apsperstyle.mastermodel')
            ->where('tgl_instocklot >=', $theData['awal'])
            ->where('tgl_instocklot <=', $theData['akhir']);

        if (!empty($theData['pdk'])) {
            $this->where('apsperstyle.mastermodel', $theData['pdk']);
        }
        if (!empty($theData['area'])) {
            $this->where('area', $theData['area']);
        }
        if (!empty($theData['buyer'])) {
            $this->where('data_model.kd_buyer_order', $theData['buyer']);
        }

        return $this->groupBy('Keterangan')
            ->orderBy('qty', 'DESC')
            ->findAll();
    }
    public function getTotalBs($idaps)
    {
        return $this->select('idapsperstyle, SUM(qty) AS qty')
            ->whereIn('idapsperstyle', $idaps)
            ->groupBy('idapsperstyle')
            ->findAll(); // Ambil satu hasil
    }
    public function getQtyBs($idaps)
    {
        return $this->select('idapsperstyle, qty')
            ->whereIn('idapsperstyle', $idaps)
            ->findAll(); // Ambil satu hasil
    }
    public function deleteSesuai(array $idaps)
    {
        return $this->whereIn('idapsperstyle', $idaps)->delete();
    }
    public function getDataForReset($area, $awal, $akhir)
    {
        return $this->select('idbs,idapsperstyle,qty,area')
            ->where('area', $area)
            ->where('tgl_instocklot >=', $awal)
            ->where('tgl_instocklot <=', $akhir)
            ->findAll();
    }
    public function deleteBSArea($area, $awal, $akhir)
    {
        return $this->where('area', $area)
            ->where('tgl_instocklot >=', $awal)
            ->where('tgl_instocklot <=', $akhir)
            ->delete();
    }
    public function updatebs()
    {
        $builder = $this->select('data_bs.idbs, data_bs.idapsperstyle, apsperstyle.mastermodel as mastermodel, apsperstyle.size as size,apsperstyle.delivery as delivery')
            ->join('apsperstyle', 'data_bs.idapsperstyle = apsperstyle.idapsperstyle', 'left')
            ->where('data_bs.no_model IS NULL')
            ->where('data_bs.size IS NULL');

        return $builder->get(10000)->getResultArray();
    }
    public function getBsPph($idaps)
    {
        $return = $this->select('SUM(data_bs.qty) AS bs_setting')
            ->join('apsperstyle', 'apsperstyle.idapsperstyle = data_bs.idapsperstyle', 'left')
            ->whereIn('data_bs.idapsperstyle', $idaps)
            ->first(); // Ambil satu hasil

        // Pastikan hasil tidak null sebelum dikembalikan
        if (!$return || empty($return->bs_setting)) {
            $return = ['bs_setting' => 0];
        }

        return $return;
    }
    public function bsMonthly($filters)
    {
        $builder = $this->select(' apsperstyle.mastermodel, apsperstyle.size,SUM(data_bs.qty) as bs')
            ->join('apsperstyle', 'apsperstyle.idapsperstyle = data_bs.idapsperstyle');
        if (!empty($filters['bulan'])) {
            $builder->where('MONTH(tgl_instocklot)', $filters['bulan']);
        }

        if (!empty($filters['tahun'])) {
            $builder->where('YEAR(tgl_instocklot)', $filters['tahun']);
        }

        if (!empty($filters['area'])) {
            $builder->where('area', $filters['area']);
        }
        return $builder->groupBy('data_bs.idapsperstyle')
            ->groupBy('apsperstyle.size')->findAll();
    }
    public function getBsPerhari($bulan, $year, $area = null)
    {
        $builder = $this->select('master_deffect.Keterangan, SUM(data_bs.qty) as qty')
            ->join('apsperstyle', 'apsperstyle.idapsperstyle = data_bs.idapsperstyle')
            ->join('master_deffect', 'master_deffect.kode_deffect = data_bs.kode_deffect')
            ->where('MONTH(tgl_instocklot)', $bulan)
            ->where('YEAR(tgl_instocklot)', $year);
        if (!empty($area)) {
            $builder->where('area', $area); // pastiin kolom `area` memang ada di tabel ini
        }

        return $builder->groupBy('master_deffect.Keterangan')
            ->orderBy('qty', 'DESC')
            ->findAll();
    }
    public function getBsPerArea($bulan, $tahun)
    {
        return $this->select('area,sum(qty) as bs')
            ->where('MONTH(tgl_instocklot)', $bulan)
            ->where('YEAR(tgl_instocklot)', $tahun)
            ->groupBy('area')
            ->orderBy('bs', 'DESC')
            ->findAll();
    }
}
