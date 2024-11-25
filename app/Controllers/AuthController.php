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
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        $UserModel = new Usermodel;
        $userData = $UserModel->login($username, $password);
        if (!$userData) {
            return redirect()->to(base_url('/login'))->withInput()->with('error', 'Invalid username or password');
        }
        session()->set('id_user', $userData['id']);
        session()->set('username', $userData['username']);
        session()->set('role', $userData['role']);
        switch ($userData['role']) {
            case 'capacity':
                return redirect()->to(base_url('/capacity'));
                break;
            case 'planning':
                return redirect()->to(base_url('/planning'));
                break;
            case 'aps':
                return redirect()->to(base_url('/aps'));
                break;
            case 'user':
                return redirect()->to(base_url('/user'));
                break;
            case 'god':
                return redirect()->to(base_url('/sudo'));
                break;
            case 'sudo':
                return redirect()->to(base_url('/sudo'));
                break;
            case 'ie':
                return redirect()->to(base_url('/ie'));
                break;

            default:
                return redirect()->to(base_url('/login'))->withInput()->with('error', 'Invalid username or password');
                break;
        }
    }
    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url('/login'));
    }
}
