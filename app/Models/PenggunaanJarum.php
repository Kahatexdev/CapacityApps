<?php

namespace App\Models;

use CodeIgniter\Model;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Month;
use DateTime;

class PenggunaanJarum extends Model
{
    protected $table            = 'penggunaan_jarum';
    protected $primaryKey       = 'id_sj';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id_karyawan', 'nama_karyawan', 'qty_jarum', 'area', 'tanggal', 'created_at', 'update_at'];

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

    public function jarumPerbulan($area, $bulan)
    {
        $bulanDateTime = DateTime::createFromFormat('F-Y', $bulan);
        $tahun = $bulanDateTime->format('Y'); // 2024
        $bulanNumber = $bulanDateTime->format('m'); // 12
        return $this->select('nama_karyawan, SUM(qty_jarum) AS total_jarum, tanggal,area')
            ->where('MONTH(tanggal)', $bulanNumber) // Filter bulan
            ->where('YEAR(tanggal)', $tahun) // Filter bulan
            ->where('area', $area) // Filter area
            ->groupBy('nama_karyawan, tanggal') // Kelompokkan berdasarkan karyawan dan tanggal
            ->findAll();
    }
}
