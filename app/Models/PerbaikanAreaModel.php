<?php

namespace App\Models;

use CodeIgniter\Model;

class PerbaikanAreaModel extends Model
{
    protected $table            = 'perbaikan_area';
    protected $primaryKey       = 'id_perbaikan';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['idapsperstyle', 'no_label', 'area', 'tgl_perbaikan', 'no_box', 'no_mc', 'qty', 'kode_deffect', 'created_at', 'updated_at'];


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

    public function getDataPerbaikanFilter($theData)
    {
        $this->select('
        apsperstyle.idapsperstyle,
        data_model.kd_buyer_order,
        apsperstyle.mastermodel,
        apsperstyle.size,
        perbaikan_area.tgl_perbaikan,
        perbaikan_area.no_box,
        perbaikan_area.no_label,
        perbaikan_area.area,
        perbaikan_area.kode_deffect,
        master_deffect.Keterangan,
        SUM(perbaikan_area.qty) AS qty
    ')
            ->join('apsperstyle', 'apsperstyle.idapsperstyle = perbaikan_area.idapsperstyle')
            ->join('master_deffect', 'master_deffect.kode_deffect = perbaikan_area.kode_deffect')
            ->join('data_model', 'data_model.no_model = apsperstyle.mastermodel')
            ->where('tgl_perbaikan >=', $theData['awal'])
            ->where('tgl_perbaikan <=', $theData['akhir']);

        if (!empty($theData['pdk'])) {
            $this->where('apsperstyle.mastermodel', $theData['pdk']);
        }
        if (!empty($theData['area'])) {
            $this->where('perbaikan_area.area', $theData['area']);
        }
        if (!empty($theData['buyer'])) {
            $this->where('data_model.kd_buyer_order', $theData['buyer']);
        }

        $this->groupBy([
            'perbaikan_area.tgl_perbaikan',
            'perbaikan_area.no_box',
            'perbaikan_area.no_label',
            'perbaikan_area.area',
            'apsperstyle.size',
            'perbaikan_area.kode_deffect'
        ]);

        return $this->findAll();
    }
    public function getSummaryGlobalPerbaikan($theData)
    {
        $this->select('
            perbaikan_area.tgl_perbaikan,
            perbaikan_area.area,
            SUM(perbaikan_area.qty) AS qty
        ')
            ->join('apsperstyle', 'apsperstyle.idapsperstyle = perbaikan_area.idapsperstyle')
            ->join('master_deffect', 'master_deffect.kode_deffect = perbaikan_area.kode_deffect')
            ->join('data_model', 'data_model.no_model = apsperstyle.mastermodel');

        if (!empty($theData['bulan'])) {
            $this->where("DATE_FORMAT(perbaikan_area.tgl_perbaikan, '%Y-%m')", $theData['bulan']);
        }
        if (!empty($theData['area'])) {
            $this->where('perbaikan_area.area', $theData['area']);
        }
        $this->groupBy([
            'perbaikan_area.tgl_perbaikan',
            'perbaikan_area.area'
        ]);
        $this->orderBy('perbaikan_area.tgl_perbaikan', 'perbaikan_area.area');

        return $this->findAll();
    }
    public function  totalPerbaikan($theData)
    {

        $this->select('sum(perbaikan_area.qty) as qty')
            ->join('apsperstyle', 'apsperstyle.idapsperstyle = perbaikan_area.idapsperstyle')
            ->join('master_deffect', 'master_deffect.kode_deffect = perbaikan_area.kode_deffect')
            ->join('data_model', 'data_model.no_model = apsperstyle.mastermodel')
            ->where('tgl_perbaikan >=', $theData['awal'])
            ->where('tgl_perbaikan <=', $theData['akhir']);

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
        $this->select('master_deffect.Keterangan, sum(perbaikan_area.qty) as qty, ')
            ->join('apsperstyle', 'apsperstyle.idapsperstyle = perbaikan_area.idapsperstyle')
            ->join('master_deffect', 'master_deffect.kode_deffect = perbaikan_area.kode_deffect')
            ->join('data_model', 'data_model.no_model = apsperstyle.mastermodel')
            ->where('tgl_perbaikan >=', $theData['awal'])
            ->where('tgl_perbaikan <=', $theData['akhir']);

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
}
