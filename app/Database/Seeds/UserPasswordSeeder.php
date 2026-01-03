<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserPasswordSeeder extends Seeder
{
     public function run()
    {
        $users = $this->db->table('user')->get()->getResultArray();

        foreach ($users as $user) {
            $this->db->table('user')
                ->where('id_user', $user['id_user'])
                ->update([
                    'password' => password_hash($user['password'], PASSWORD_DEFAULT)
                ]);
        }
    }
}
