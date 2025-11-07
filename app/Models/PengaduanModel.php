<?php

namespace App\Models;

use CodeIgniter\Model;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\F;

class PengaduanModel extends Model
{
    protected $table            = 'pengaduan';
    protected $primaryKey       = 'id_pengaduan';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields = ['username', 'target_role', 'isi', 'created_at', 'updated_at', 'replied'];
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

    public function getPengaduan(string $username, string $role)
    {
        // 7 hari terakhir termasuk hari ini
        $endDate   = date('Y-m-d 23:59:59');                     // hari ini jam 23:59:59
        $startDate = date('Y-m-d 00:00:00', strtotime('-6 days')); // 7 hari lalu jam 00:00:00
        // dd($endDate,$startDate);
        return $this->Where('created_at >=', $startDate)
            ->where('created_at <=', $endDate)
            ->where('target_role', $role)
            ->orWhere('username', $username)
            ->orderBy('updated_at', 'DESC')
            ->findAll();
    }

    public function countNotif($role)
    {
        return $this->where('target_role', $role)
            ->where('replied', 0)
            ->countAllResults();
    }
    public function deleteAduanLama($week)
    {
        return $this->where('created_at <=', $week)
            ->delete();
    }
}
