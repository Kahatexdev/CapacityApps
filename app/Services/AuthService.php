<?php
namespace App\Services;

use App\Models\UserModel;
use App\Models\LoginAttemptModel;

class AuthService
{
    protected $userModel;
    protected $attemptModel;

    const MAX_ATTEMPT = 3;
    const LOCK_MINUTE = 15;

    public function __construct()
    {
        $this->userModel    = new UserModel();
        $this->attemptModel = new LoginAttemptModel();
    }

    public function attemptLogin(string $username, string $password, string $ip): array
    {
        $client = service('curlrequest');

        $response = $client->post(
            'http://172.23.39.117/ComplaintSystem/public/api/getDataUserByName',
            [
                'http_errors' => false, // 🔥 PENTING
                'form_params' => [
                    'username' => $username,
                ]
            ]
        );

        $data = json_decode($response->getBody(), true);
        $user = $data['data'];

        // ❌ user tidak ditemukan
        if (!$user) {
            return [
                'status' => false,
                'locked' => false,
                'failed' => 0,
                'message'=> 'Invalid username or password'
            ];
        }

        $attempt = $this->attemptModel->getAttempt($user['id_user'], $ip);

        // 🔒 akun terkunci
        if (
            $attempt &&
            $attempt['locked_until'] &&
            strtotime($attempt['locked_until']) > time()
        ) {
            return [
                'status'       => false,
                'locked'       => true,
                'failed'       => $attempt['failed_attempt'],
                'locked_until' => $attempt['locked_until'],
                'message'      => 'Account locked'
            ];
        }

        // ❌ password salah
        if (!password_verify($password, $user['password'])) {

            if ($attempt) {
                $failed = $attempt['failed_attempt'] + 1;

                $update = ['failed_attempt' => $failed];

                if ($failed >= self::MAX_ATTEMPT) {
                    $update['locked_until'] = date(
                        'Y-m-d H:i:s',
                        strtotime('+' . self::LOCK_MINUTE . ' minutes')
                    );
                }

                $this->attemptModel->update($attempt['id'], $update);
            } else {
                $failed = 1;
                $this->attemptModel->insert([
                    'user_id'        => $user['id_user'],
                    'ip_address'     => $ip,
                    'failed_attempt' => 1
                ]);
            }

            return [
                'status' => false,
                'locked' => $failed >= self::MAX_ATTEMPT,
                'failed' => $failed,
                'message'=> 'Invalid username or password'
            ];
        }

        // ✅ LOGIN SUKSES
        if ($attempt) {
            $this->attemptModel->delete($attempt['id']);
        }

        return [
            'status' => true,
            'locked' => false,
            'user'   => $user,
            'message'=> 'Login success'
        ];
    }

    /**
     * ADMIN UNLOCK
     */
    public function unlockUser(int $userId)
    {
        return $this->attemptModel
            ->where('user_id', $userId)
            ->delete();
    }
}
