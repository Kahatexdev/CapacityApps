<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeed extends Seeder
{
    public function run()
    {
        $data = [
            [
                'username' => 'capacity',
                'password' => 'capacity',
                'role' => 'capacity'
            ],
            [
                'username' => 'planning',
                'password' => 'planning',
                'role' => 'planning'
            ],
            [
                'username' => 'aps',
                'password' => 'aps',
                'role' => 'aps'
            ],
        ];
        $this->db->table('user')->insertBatch($data);
        $this->call('DataMesin');
        $this->call('ProductType');
        $this->call('DataBooking');
        $this->call('DataModel');
        $this->call('DataOrder');
    }
}
