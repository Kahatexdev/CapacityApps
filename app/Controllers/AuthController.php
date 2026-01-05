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
    public function login()
    {
        helper('audit');

        // $authService = service('authService');

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        $ip       = $this->request->getIPAddress();

        $result = $this->authService->attemptLogin($username, $password, $ip);

        if (!$result['status']) {

            log_audit([
                'module'=> 'AUTH',
                'action'=> ($result['locked'] ?? false) ? 'LOGIN_BLOCKED' : 'LOGIN_FAIL',
                'ref_type'=> 'USER',
                'ref_id'=> $username,
                'message'=> 'Login gagal',
                'old'=> null,
                'new'=> [
                    'username' => $username,
                    'ip' => $ip,
                    'detail' => $result
                ]
            ]);

       
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

        $user = $result['user'];

        session()->set([
            'id_user'  => $user['id_user'],
            'username' => $user['username'],
            'role'     => $user['role'],
        ]);

        log_audit([
            'module'=> 'AUTH',
            'action'=> 'LOGIN',
            'ref_type'=> 'USER',
            'ref_id'=> $user['id_user'],
            'message'=> 'Login berhasil',
            'old'=> null,
            'new'=> [
                'username' => $user['username'],
                'ip' => $ip
            ]
        ]);

        return redirect()->to(base_url('/' . $user['role']));
    }
    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url('/login'));
    }

    public function lockedUsers()
    {
        $users = $this->LoginAttemptModel->getDataUser();
        // dd ($users);
        $data = [
            'active' => $this->active,
            'active1' => $this->active,
            'title' => 'Monitoring',
            'role' => $this->role,
            'dataUser' => $users,
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
