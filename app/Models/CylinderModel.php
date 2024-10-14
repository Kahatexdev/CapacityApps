<?php

namespace App\Models;

use CodeIgniter\Model;

class CylinderModel extends Model
{
    protected $table            = 'data_cylinder';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id', 'needle', 'production_unit', 'type_machine', 'qty', 'needle_detail'];

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

    public function getCylinder($jarum)
    {
        // Mengambil nilai unik dari kolom 'jarum'
        $query =  $this->select('SUM(CASE WHEN production_unit = "Cijerah" AND type_machine = "CYLINDER 306" OR type_machine = "CYLINDER 308/316" THEN qty ELSE 0 END) AS qty_dakong, SUM(CASE WHEN production_unit = "Cijerah" AND type_machine = "THS DOUBLE" THEN qty ELSE 0 END) AS qty_ths, SUM(CASE WHEN production_unit = "Cijerah" AND type_machine = "CYLINDER ROSSO" THEN qty ELSE 0 END) AS qty_rosso')
            ->where('needle', $jarum)
            ->groupBy('needle')
            ->orderBy('needle', 'ASC')
            ->get();

        $result = $query->getResultArray(); // Mengambil hasil sebagai array

        // Jika hasil query kosong, kembalikan array kosong
        return !empty($result) ? $result : [];
    }
}
