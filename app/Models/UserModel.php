<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'user';
    protected $primaryKey       = 'id_user';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id_user', 'username', 'password', 'role'];

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

    public function login($username, $password)
    {
        $user = $this->where(['username' => $username, 'password' => $password])->first();

        if (!$user) {
            return null;
        }

        return [
            'id' => $user['id_user'],
            'role' => $user['role'],
            'username' => $user['username']
        ];
    }
    public function getData()
    {
        $sql = "
            SELECT user.id_user, user.username, user.role,user.password, (
                SELECT GROUP_CONCAT(areas.name SEPARATOR ', ') 
                FROM areas
                JOIN user_areas ON areas.id = user_areas.area_id
                WHERE user_areas.user_id = user.id_user
            ) as area_names 
            FROM user
        ";

        $query = $this->db->query($sql);
        return $query->getResultArray();
    }
    public function getListRole()
    {
        return $this->select('DISTINCT(role) as role')->findAll();
    }
}
