<?php

namespace App\Models;

use CodeIgniter\Model;

class BsModel extends Model
{
    protected $table            = 'data_bs';
    protected $primaryKey       = 'idbs';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['idapsperstyle', 'no_label', 'area', 'tgl_instocklot', 'no_box', 'qty', 'kode_deffect', 'created_at', 'updated_at'];

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
        $this->select('apsperstyle.idapsperstyle, apsperstyle.mastermodel, apsperstyle.size, data_bs.*,sum(data_bs.qty) as qty, master_deffect.Keterangan')
            ->join('apsperstyle', 'apsperstyle.idapsperstyle = data_bs.idapsperstyle')
            ->join('master_deffect', 'master_deffect.kode_deffect = data_bs.kode_deffect')
            ->where('tgl_instocklot >=', $theData['awal'])
            ->where('tgl_instocklot <=', $theData['akhir']);

        if (!empty($theData['pdk'])) {
            $this->where('apsperstyle.mastermodel', $theData['pdk']);
        }
        if (!empty($theData['area'])) {
            $this->where('data_bs.area', $theData['area']);
        }

        $data = $this->groupBy('tgl_instocklot, apsperstyle.size, data_bs.kode_deffect')->findAll();
        return $data;
    }

    public function  totalBs($theData)
    {

        $this->select('sum(data_bs.qty) as qty')
            ->join('apsperstyle', 'apsperstyle.idapsperstyle = data_bs.idapsperstyle')
            ->join('master_deffect', 'master_deffect.kode_deffect = data_bs.kode_deffect')
            ->where('tgl_instocklot >=', $theData['awal'])
            ->where('tgl_instocklot <=', $theData['akhir']);

        if (!empty($theData['pdk'])) {
            $this->where('apsperstyle.mastermodel', $theData['pdk']);
        }
        if (!empty($theData['area'])) {
            $this->where('area', $theData['area']);
        }

        $result = $this->first();
        return $result['qty'] ?? '0';
    }
    public function chartData($theData)
    {
        $this->select('master_deffect.Keterangan, sum(data_bs.qty) as qty, ')
            ->join('apsperstyle', 'apsperstyle.idapsperstyle = data_bs.idapsperstyle')
            ->join('master_deffect', 'master_deffect.kode_deffect = data_bs.kode_deffect')
            ->where('tgl_instocklot >=', $theData['awal'])
            ->where('tgl_instocklot <=', $theData['akhir']);

        if (!empty($theData['pdk'])) {
            $this->where('apsperstyle.mastermodel', $theData['pdk']);
        }
        if (!empty($theData['area'])) {
            $this->where('area', $theData['area']);
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
        $results = $this->select('idapsperstyle')
            ->where('area', $area)
            ->where('tgl_instocklot >=', $awal)
            ->where('tgl_instocklot <=', $akhir)
            ->findAll();
        return array_column($results, 'idapsperstyle');
    }
    public function deleteBSArea($area, $awal, $akhir)
    {
        return $this->where('area', $area)
            ->where('tgl_instocklot >=', $awal)
            ->where('tgl_instocklot <=', $akhir)
            ->delete();
    }
}
