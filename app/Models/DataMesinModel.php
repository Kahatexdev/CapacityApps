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
    protected $allowedFields    = ['id_data_mesin', 'area', 'jarum', 'total_mc', 'brand', 'mesin_jalan'];

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
    $query = $this->select('*')->where('area',$area)->findAll();

    return $query;
    }

    public function getTotalMesinByJarum()
    {
    $customOrder = [
        'JC120' => 3,
        'JC144' => 2,
        'JC168' => 1
    ];

    // Get the keys of the custom order
    $customOrderKeys = array_keys($customOrder);

    // Generate the CASE statement for custom ordering
    $caseStatement = "CASE jarum ";
    foreach ($customOrderKeys as $index => $jarum) {
        $caseStatement .= "WHEN '$jarum' THEN $index ";
    }
    $caseStatement .= "END";

    $query = $this->select('jarum, SUM(total_mc) as total')
                  ->groupBy('jarum')
                  ->orderBy("FIELD(jarum, 'JC120', 'JC144', 'JC168') DESC, jarum");

    $result = $query->findAll();

    return $result;
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
                ->join('apsperstyle', 'data_mesin.jarum = apsperstyle.machinetypeid','left')
                ->where('apsperstyle.mastermodel', $noModel)
                ->get()
                ->getResult();
    }

    
}
