<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

use App\Models\UserModel;

class AuthController extends BaseController
{

    public function index()
    {

        return view('Auth/index');
    }
    // public function login()
    // {
    //     helper('audit');

    //     // $authService = service('authService');

    //     $username = $this->request->getPost('username');
    //     $password = $this->request->getPost('password');
    //     $ip       = $this->request->getIPAddress();

    //     $result = $this->authService->attemptLogin($username, $password, $ip);

    //     if (!$result['status']) {

    //         log_audit([
    //             'module'=> 'AUTH',
    //             'action'=> ($result['locked'] ?? false) ? 'LOGIN_BLOCKED' : 'LOGIN_FAIL',
    //             'ref_type'=> 'USER',
    //             'ref_id'=> $username,
    //             'message'=> 'Login gagal',
    //             'old'=> null,
    //             'new'=> [
    //                 'username' => $username,
    //                 'ip' => $ip,
    //                 'detail' => $result
    //             ]
    //         ]);

       
    //         // pesan default
    //         $errorMessage = 'Invalid username or password';

    //         if (($result['locked'] ?? false) === true) {
    //             $errorMessage = 'Akun terkunci sementara';
    //         }

    //         return redirect()->back()
    //             ->withInput()
    //             ->with('error', 'Akun terkunci sementara')
    //             ->with('login_info', [
    //                 'locked' => $result['locked'] ?? false,
    //                 'locked_until' => $result['locked_until'] ?? null,
    //                 'failed' => $result['failed'] ?? 0,
    //                 'max' => $result['max_attempt'] ?? 3,
    //             ]);
    //     }

    //     $user = $result['user'];

    //     session()->set([
    //         'id_user'  => $user['id_user'],
    //         'username' => $user['username'],
    //         'role'     => $user['role'],
    //     ]);

    //     log_audit([
    //         'module'=> 'AUTH',
    //         'action'=> 'LOGIN',
    //         'ref_type'=> 'USER',
    //         'ref_id'=> $user['id_user'],
    //         'message'=> 'Login berhasil',
    //         'old'=> null,
    //         'new'=> [
    //             'username' => $user['username'],
    //             'ip' => $ip
    //         ]
    //     ]);

    //     return redirect()->to(base_url('/' . $user['role']));
    // }
    // public function logout()
    // {
    //     session()->destroy();
    //     return redirect()->to(base_url('/login'));
    // }

    public function login()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        $ip       = $this->request->getIPAddress();

        $authService = service('authService');
        $client = service('curlrequest');

        $response = $client->post(
            'http://192.168.1.5/ComplaintSystem/public/api/login',
            [
                'http_errors' => false, // 🔥 PENTING
                'form_params' => [
                    'username' => $username,
                    'password' => $password
                ]
            ]
        );

        $data = json_decode($response->getBody(), true);

        if (!$data || $data['success'] !== true) {
            $result = $this->authService->attemptLogin($username, $password, $ip);

            if (!$result['status']) {
                // pesan default
                $errorMessage = 'Invalid username or password';

                if (($result['locked'] ?? false) === true) {
                    $errorMessage = 'Akun terkunci sementara';
                }

                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Akun terkunci sementara')
                    ->with('login_info', [
                        'locked' => $result['locked'] ?? false,
                        'locked_until' => $result['locked_until'] ?? null,
                        'failed' => $result['failed'] ?? 0,
                        'max' => $result['max_attempt'] ?? 3,
                    ]);
            }
        }


        session()->set([
            'auth_token' => $data['token'],
            'id_user'    => $data['user']['id_user'],
            'username'   => $data['user']['username'],
            'role'       => $data['user']['role'],
            'logged_in'  => true
        ]);

        return redirect()->to(base_url('/' . session()->get('role')));
    }

    public function logout()
    {
        $token = session()->get('auth_token');

        // 🔥 Revoke token ke ComplaintSystem (optional tapi recommended)
        if ($token) {
            try {
                $client = service('curlrequest');
                $client->post(
                    'http://192.168.1.5/ComplaintSystem/public/api/logout',
                    [
                        'headers' => [
                            'Authorization' => 'Bearer ' . $token
                        ]
                    ]
                );
            } catch (\Throwable $e) {
                // kalau auth server down, tetap lanjut logout lokal
                log_message('error', 'Logout revoke failed: ' . $e->getMessage());
            }
        }

        // 🔥 HAPUS SESSION LOKAL
        session()->destroy();

        return redirect()->to('/login');
    }

    public function lockedUsers()
    {
        $client = service('curlrequest');

        $response = $client->get(
            'http://192.168.1.5/ComplaintSystem/public/api/CS/user',
            ['http_errors' => false]
        );

        $userdata = json_decode($response->getBody(), true);

        if (!isset($userdata['userData'])) {
            dd('Data user dari API tidak valid', $userdata);
        }

        // Ambil login attempt yang terkunci
        $loginAttempts = $this->db->table('login_attempts')
            ->where('locked_until IS NOT NULL')
            ->orderBy('locked_until', 'DESC')
            ->get()
            ->getResultArray();

        // Map login_attempt berdasarkan user_id
        $loginMap = [];
        foreach ($loginAttempts as $la) {
            $loginMap[$la['user_id']] = $la;
        }

        // Gabungkan data user + login_attempt
        $lockedUsers = [];
        foreach ($userdata['userData'] as $user) {
            if (isset($loginMap[$user['id_user']])) {
                $lockedUsers[] = array_merge($user, [
                    'failed_attempt' => $loginMap[$user['id_user']]['failed_attempt'],
                    'locked_until'   => $loginMap[$user['id_user']]['locked_until'],
                    'ip_address'     => $loginMap[$user['id_user']]['ip_address'],
                ]);
            }
        }

        $data = [
            'active'   => $this->active,
            'active1'   => $this->active,
            'title'    => 'Monitoring',
            'role'     => $this->role,
            'dataUser' => $lockedUsers,
        ];

        return view($this->role . '/Account/locked_users', $data);
    }


    public function unlockUser($idUser)
    {
        $ip       = $this->request->getIPAddress();
        $user = $this->userModel->find($idUser);
        $this->authService->unlockUser($idUser);

        // log_audit([
        //     'module'=> 'AUTH',
        //     'action'=> 'UNLOCK_USER',
        //     'ref_type'=> 'USER',
        //     'ref_id'=> $idUser,
        //     'message'=> 'Admin unlock akun',
        //     'old'=> null,
        //     'new'=> [
        //         'username' => $user['username'],
        //         'ip' => $ip
        //     ]
        // ]);

        return redirect()->back()->with('success', 'Akun berhasil di-unlock');
    }
}
