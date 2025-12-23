<?php

namespace App\Models;

use CodeIgniter\Model;

class PengaduanReply extends Model
{
    protected $table            = 'pengaduan_reply';
    protected $primaryKey       = 'id_reply';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields = ['id_pengaduan', 'username', 'isi', 'created_at'];

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

    public function getRepliesByPengaduan($id_pengaduan)
    {
        return $this->where('id_pengaduan', $id_pengaduan)
            ->orderBy('created_at', 'ASC')
            ->findAll();
    }
    public function deleteReplyLama($week)
    {
        return $this->where('created_at <=', $week)
            ->delete();
    }

    public function getNewRepliesByRole(array $opt): array
    {
        $role        = $opt['role'] ?? 'user';
        $username    = trim((string)($opt['username'] ?? ''));
        $lastReplyId = (int)($opt['last_reply_id'] ?? 0);
        $limit       = (int)($opt['limit'] ?? 100);

        $db = \Config\Database::connect();
        $b  = $db->table($this->table . ' r');

        $b->select('r.id_reply, r.id_pengaduan, r.username, r.isi, r.created_at')
            ->join('pengaduan p', 'p.id_pengaduan = r.id_pengaduan', 'inner');
            $b->where('r.id_reply >', $lastReplyId);

if ($role === 'user') {
    $b->where('p.username', $username);
} else {
    $b->groupStart()
          ->where('p.target_role', $role)
          ->orWhere('p.username', $username)
      ->groupEnd();
}


        return $b->orderBy('r.id_reply', 'ASC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }


    public function getMaxReplyIdByRole(string $role, string $username = ''): int
    {
        $username = trim((string)$username);

        $db = \Config\Database::connect();
        $b  = $db->table($this->table . ' r');

        $b->selectMax('r.id_reply', 'max_reply_id')
            ->join('pengaduan p', 'p.id_pengaduan = r.id_pengaduan', 'inner');

        if ($role === 'user') {
            $b->where('p.username', $username ?: '__NO_USER__');
        } else {
            $b->groupStart()
                ->where('p.target_role', $role)
                ->orWhere('p.username', $username ?: '__NO_USER__')
                ->groupEnd();
        }

        $row = $b->get()->getRowArray();
        return (int)($row['max_reply_id'] ?? 0);
    }
}
