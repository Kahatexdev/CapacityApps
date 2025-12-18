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

    // public function getPengaduan(string $username, string $role)
    // {
    //     // 7 hari terakhir termasuk hari ini
    //     $endDate   = date('Y-m-d 23:59:59');                     // hari ini jam 23:59:59
    //     $startDate = date('Y-m-d 00:00:00', strtotime('-6 days')); // 7 hari lalu jam 00:00:00
    //     // dd($endDate,$startDate);
    //     return $this->Where('created_at >=', $startDate)
    //         ->where('created_at <=', $endDate)
    //         ->where('target_role', $role)
    //         ->orWhere('username', $username)
    //         ->orderBy('updated_at', 'DESC')
    //         ->findAll();
    // }

    public function getPengaduan(string $username, string $role)
{
    $endDate   = date('Y-m-d 23:59:59');
    $startDate = date('Y-m-d 00:00:00', strtotime('-6 days'));

    return $this->where('created_at >=', $startDate)
        ->where('created_at <=', $endDate)
        ->groupStart()
            ->where('target_role', $role)
            ->orWhere('username', $username)
        ->groupEnd()
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

    public function getNewPengaduan(array $opt): array
    {
        $role     = $opt['role'] ?? 'user';
        $username = trim((string)($opt['username'] ?? ''));
        $lastId   = (int)($opt['last_id'] ?? 0);
        $limit    = (int)($opt['limit'] ?? 50);

        $b = $this->builder();
        $b->select('id_pengaduan, username, target_role, isi, created_at, updated_at, replied');
            $b->where('id_pengaduan >', $lastId);

if ($role === 'user') {
    $b->where('username', $username);
} else {
    $b->groupStart()
          ->where('target_role', $role)
          ->orWhere('username', $username)
      ->groupEnd();
}

        return $b->orderBy('id_pengaduan', 'ASC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

    public function getMaxIdByRole(string $role, string $username = ''): int
    {
        $username = trim((string)$username);

        $b = $this->builder();
        $b->selectMax('id_pengaduan', 'max_id');

        if ($role === 'user') {
            $b->where('username', $username ?: '__NO_USER__');
        } else {
            $b->groupStart()
                ->where('target_role', $role)
                ->orWhere('username', $username ?: '__NO_USER__')
                ->groupEnd();
        }

        $row = $b->get()->getRowArray();
        return (int)($row['max_id'] ?? 0);
    }
}
