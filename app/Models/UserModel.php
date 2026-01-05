<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends BaseAuditModel
{
    protected $table            = 'user';
    protected $primaryKey       = 'id_user';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields = [
        'username',
        'password',
        'role'
    ];


    protected string $refType = 'USER';

    

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
