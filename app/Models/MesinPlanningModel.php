<?php

namespace App\Models;

use CodeIgniter\Model;

class MesinPlanningModel extends Model
{
    protected $table            = 'mesin_planning';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id','area','jarum','brand','mc_nyala','status','id_kebutuhan_mesin'];

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

    public function saveMachine($id)
    {
        $request = service('request');
        $dataToSave = json_decode($request->getPost('dataToSave'), true);

        // Check if there is data to save
        if (!empty($dataToSave)) {
            // Insert each row of data
            foreach ($dataToSave as $row) {
                // Map numerical keys to column names
                $mappedRow = [
                    'area' => $row[0],
                    'jarum' => $row[1],
                    'brand' => $row[2],
                    'mc_nyala' => $row[3],
                    'status' => $row[4] ?? 'Active', // If column5 is missing in $row, provide a default value
                    'id_kebutuhan_mesin' => $id,
                ];

                // Insert the mapped row into the database
                $this->insert($mappedRow);
            }

            return true; // Data inserted successfully
        } else {
            return false; // No data to insert
        }
    }
    public function getDataPlanning($id){
        return $this->select('mesin_planning.*, data_mesin.*')
            ->join('data_mesin', 'mesin_planning.jarum = data_mesin.jarum AND mesin_planning.brand = data_mesin.brand AND mesin_planning.area = data_mesin.area')
            ->where('mesin_planning.id_kebutuhan_mesin', $id)
            ->findAll();
    }
    public function getMesinByArea($area,$jarum){
        $a = $this->select('sum(mc_nyala)')
        ->where('area',$area)
        ->where('jarum',$jarum)
        ->first();

        $mesin = reset($a);
        return $mesin;
    }
}
