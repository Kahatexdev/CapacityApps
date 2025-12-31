<?php

namespace App\Models;

use CodeIgniter\Model;

class LoginAttemptModel extends Model
{
    protected $table            = 'login_attempts';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields = [
        'user_id',
        'ip_address',
        'failed_attempt',
        'locked_until'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

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

    public function getAttempt($userId, $ip)
    {
        return $this->where([
            'user_id'    => $userId,
            'ip_address' => $ip
        ])->first();
    }

    public function getDataUser()
    {
        return $this->db->table('login_attempts la')
        ->select('
            u.id_user,
            u.username,
            la.failed_attempt,
            la.locked_until,
            la.ip_address
        ')
        ->join('user u', 'u.id_user = la.user_id')
        ->where('la.locked_until IS NOT NULL')
        ->orderBy('la.locked_until', 'DESC')
        ->get()
        ->getResultArray();
    }
}
