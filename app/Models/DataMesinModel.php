<?php

namespace App\Models;

use CodeIgniter\Model;

class DataMesinModel extends Model
{
    protected $table            = 'data_mesin';
    protected $primaryKey       = 'id_data_mesin';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id_data_mesin', 'area', 'jarum', 'total_mc', 'brand', 'mesin_jalan', 'aliasjarum'];

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


    public function getJarum()
    {
        // Mengambil nilai unik dari kolom 'jarum'
        $query = $this->distinct()->select('jarum')->orderBy('jarum', 'ASC')->findAll();

        // Mengubah hasil query menjadi array dengan nilai 'jarum' saja
        $uniqueJarums = array_column($query, 'jarum');
        return $uniqueJarums;
    }

    public function getArea()
    {
        // Mengambil nilai unik dari kolom 'jarum'
        $query = $this->distinct()->select('area')->orderBy('id_data_mesin', 'ASC')->findAll();

        // Mengubah hasil query menjadi array dengan nilai 'jarum' saja
        $uniqueArea = array_column($query, 'area');
        return $uniqueArea;
    }

    public function getJarumArea($area)
    {
        $query = $this->select('*')->where('area', $area)->findAll();

        return $query;
    }
    public function getMesinPerJarum($jarum)
    {
        $query = $this->select('*')->where('jarum', $jarum)->findAll();
        return $query;
    }

    public function getTotalMesinByJarum()
    {
        $customOrder = [
            'JC120' => 1,
            'TJ120' => 2,
            'JC144' => 3,
            'TJ144' => 4,
            'JC168' => 5,
            'TJ168' => 6
        ];

        // Generate the CASE statement for custom ordering
        $caseStatement = "CASE ";
        foreach ($customOrder as $jarum => $index) {
            $caseStatement .= "WHEN jarum = '$jarum' THEN $index ";
        }
        // For '10G', '13G', '240N', and 'POM-POM' entries, set the index to a very large number to move them to the end
        $caseStatement .= "WHEN jarum LIKE '10G%' THEN 1000 ";
        $caseStatement .= "WHEN jarum = '13G' THEN 1001 ";
        $caseStatement .= "WHEN jarum = '240N' THEN 1002 ";
        $caseStatement .= "WHEN jarum = 'POM-POM' THEN 1003 ";
        $caseStatement .= "ELSE " . (count($customOrder) + 1) . " END";

        return $this->select('jarum, SUM(total_mc) as total')
            ->groupBy('jarum')
            ->orderBy($caseStatement . ', jarum')
            ->findAll();
    }



    public function mcJalan()
    {
        return $this->selectSum('mesin_jalan')->get()->getRow()->mesin_jalan;
    }
    public function totalMc()
    {
        return $this->selectSum('total_mc')->get()->getRow()->total_mc;
    }
    public function getAreaModel($noModel)
    {
        return $this->select('data_mesin.*')
            ->join('apsperstyle', 'data_mesin.jarum = apsperstyle.machinetypeid', 'left')
            ->where('apsperstyle.mastermodel', $noModel)
            ->get()
            ->getResult();
    }
    public function getDC()
    {
        return $this->select('aliasjarum')->like('jarum', 'DC')->findAll();
    }
    public function getBrand($jarum, $brand)
    {
        $result = $this->selectSum('total_mc')->where('jarum', $jarum)->like('brand', $brand)->get()->getRow();

        // Periksa apakah hasilnya ada sebelum mengembalikannya
        return $result ? $result->total_mc : 0; // Mengembalikan total_mc jika ada, jika tidak, kembalikan 0 atau nilai default lainnya
    }
    public function getRunningMc($jarum, $brand)
    {
        $result = $this->selectSum('mesin_jalan')->where('jarum', $jarum)->like('brand', $brand)->get()->getRow();

        // Periksa apakah hasilnya ada sebelum mengembalikannya
        return $result ? $result->mesin_jalan : 0; // Mengembalikan total_mc jika ada, jika tidak, kembalikan 0 atau nilai default lainnya

    }
}
