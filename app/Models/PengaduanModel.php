<?php

namespace App\Models;

use CodeIgniter\Model;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\F;

class PengaduanModel extends Model
{
    protected $table            = 'pengaduan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['parent_id', 'user_id', 'target_role', 'isi', 'created_at', 'updated_at'];

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

    public function getPengaduan($userId, $role)
    {
        // Ambil pengaduan utama
        $pengaduanUtama = $this->select('pengaduan.*, user.username')
            ->join('user', 'user.id_user = pengaduan.user_id', 'left')
            ->groupStart()
            ->where('pengaduan.user_id', $userId)
            ->orWhere('pengaduan.target_role', $role)
            ->groupEnd()
            ->where('parent_id', null)
            ->orderBy('pengaduan.created_at', 'DESC')
            ->findAll();

        // Ambil semua replies dalam 1 query
        $replies = $this->select('pengaduan.*, user.username')
            ->join('user', 'user.id_user = pengaduan.user_id', 'left')
            ->groupStart()
            ->where('pengaduan.user_id', $userId)
            ->orWhere('pengaduan.target_role', $role)
            ->groupEnd()
            ->where('parent_id IS NOT NULL')
            ->orderBy('pengaduan.created_at', 'ASC')
            ->findAll();

        // Group replies berdasarkan parent_id
        $groupedReplies = [];
        foreach ($replies as $reply) {
            $groupedReplies[$reply['parent_id']][] = $reply;
        }

        // Masukkan replies ke pengaduan utama
        foreach ($pengaduanUtama as &$p) {
            $p['replies'] = $groupedReplies[$p['id']] ?? [];
        }

        return $pengaduanUtama;
    }
}
