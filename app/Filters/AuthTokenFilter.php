<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthTokenFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('logged_in')) {
            $this->revokeToken();
            return redirect()->to('/login');
        }
    }

    private function revokeToken()
    {
        $token = session()->get('auth_token');
        if (!$token) return;

        try {
            service('curlrequest')->post(
                'http://192.168.1.5/ComplaintSystem/public/api/logout',
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $token
                    ]
                ]
            );
        } catch (\Throwable $e) {
            log_message('error', 'Auto revoke failed: ' . $e->getMessage());
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
