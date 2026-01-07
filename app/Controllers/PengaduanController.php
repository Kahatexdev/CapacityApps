<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;


class PengaduanController extends BaseController
{
    public function __construct() {}

    public function getpengaduan()
    {
        $role   = session()->get('role');
        $idUser = session()->get('id_user');

        $url = api_url('complaint') . 'chat/messages/' . $idUser. '?role=' . $role;
        $url2 = api_url('complaint') . 'getRole';
        // dd($url);
        try {
            $context = stream_context_create([
                'http' => [
                    'timeout' => 5
                ]
            ]);

            $json = @file_get_contents($url, false, $context);
            $jsonRole = @file_get_contents($url2, false, $context);
            if ($json === false) {
                throw new \Exception('Gagal mengambil data dari API');
            }

            $threads = json_decode($json, true);
            $roles = json_decode($jsonRole, true);
            if (!is_array($threads)) {
                throw new \Exception('Format response API tidak valid');
            }
            return view($role . '/pengaduan/index', [
                'threads' => $threads, // ✅ tetap sama
                'roles' => $roles,
                'role'    => $role,
                'title'   => 'Pengaduan',
                'active'  => $this->active,
                'active1'  => $this->active,
            ]);

        } catch (\Throwable $e) {
            log_message('error', 'API pengaduan error: ' . $e->getMessage());

            return view($role . '/pengaduan/index', [
                'threads' => [],
                'roles' => [],
                'error'   => 'Tidak dapat memuat pengaduan',
                'role'    => $role,
                'title'   => 'Pengaduan',
                'active'  => $this->active,
                'active1'  => $this->active
            ]);
        }
    }

    public function index()
    {
        // Misalnya user info disimpan di session saat login
        $username = session()->get('username');
        $role     = session()->get('role');
        // $week = date('Y-m-d', strtotime('-7 days'));

        $pengaduan = $this->pengaduanModel->getPengaduan($username, $role);
        // $this->pengaduanModel->deleteAduanLama($week);
        // $this->replyModel->deleteReplyLama($week);
        // Ambil semua reply per pengaduan
        $reply = [];
        foreach ($pengaduan as $p) {
            $reply[$p['id_pengaduan']] = $this->replyModel->getRepliesByPengaduan($p['id_pengaduan']) ?? null;
        }
        $data = [
            'role' => $role,
            'title' => 'Capacity System',
            'active1' => '',
            'active2' => '',
            'active3' => '',
            'active4' => '',
            'active5' => '',
            'active6' => '',
            'active7' => '',
            'pengaduan' => $pengaduan,
            'replies' => $reply
        ];

        return view($role . '/pengaduan/index', $data);
    }

    public function create()
    {
        $username   = session()->get('username');
        $targetRole = $this->request->getPost('target_role');
        $isi        = $this->request->getPost('isi');
        $role = session()->get('role');
        $this->pengaduanModel->save([
            'username'    => $username,
            'target_role' => $targetRole,
            'isi'         => $isi
        ]);
        return redirect()->to($role . '/pengaduan')->withInput()->with('success', 'Aduan Berhasil Di kirim');
    }

    public function reply($id_pengaduan)
    {
        $username = session()->get('username');
        $isi      = $this->request->getPost('isi');
        $role = session()->get('role');

        $this->replyModel->save([
            'id_pengaduan' => $id_pengaduan,
            'username'     => $username,
            'isi'          => $isi,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        $setStat = $this->pengaduanModel->update($id_pengaduan, ['replied' => 1, 'updated_at' => date('Y-m-d H:i:s')]);


        return redirect()->to($role . '/pengaduan')->withInput()->with('success', 'Balasan berhasil dikirin');
    }
    public function Apicreate()
    {
        $username   = urldecode($this->request->getPost('username'));
        $targetRole = $this->request->getPost('target_role');
        $isi        = $this->request->getPost('isi');

        $this->pengaduanModel->save([
            'username'    => $username,
            'target_role' => $targetRole,
            'isi'         => $isi
        ]);

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Berhasil terkirim'
        ]);
    }

    public function Apireply($id_pengaduan)
    {
        $username   = urldecode($this->request->getPost('username'));
        $isi      = $this->request->getPost('isi');

        $this->replyModel->save([
            'id_pengaduan' => $id_pengaduan,
            'username'     => $username,
            'isi'          => $isi,
            'created_at'   => date('Y-m-d H:i:s')
        ]);
        $setStat = $this->pengaduanModel->update($id_pengaduan, ['replied' => 1, 'updated_at' => date('Y-m-d H:i:s')]);

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Berhasil terkirim'
        ]);
    }


    public function Apipengaduan($username, $role)
    {
        $usernamedec   = urldecode($username);
        $pengaduan = $this->pengaduanModel->getPengaduan($usernamedec, $role);
        // Ambil semua reply per pengaduan
        $reply = [];
        foreach ($pengaduan as $p) {
            $reply[$p['id_pengaduan']] = $this->replyModel->getRepliesByPengaduan($p['id_pengaduan']) ?? null;
        }
        $data = [
            'pengaduan' => $pengaduan,
            'replies' => $reply
        ];
        // $week = date('Y-m-d', strtotime('-7 days'));
        // $this->pengaduanModel->deleteAduanLama($week);
        // $this->replyModel->deleteReplyLama($week);
        return $this->response->setJSON($data);
    }

    public function fetchNew()
    {
        $lastId      = (int) ($this->request->getGet('last_id') ?? 0);
        $lastReplyId = (int) ($this->request->getGet('last_reply_id') ?? 0);

        // role dari query atau session
        $role = $this->request->getGet('role') ?? session()->get('role') ?? 'user';
        $role = trim((string) $role);

        // username opsional (buat role user biasanya)
        $username = $this->request->getGet('username') ?? '';
        $username = trim((string) $username);

        if ($role === 'user' && $username === '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'username is required for role=user'
            ])->setStatusCode(400);
        }

        // allowlist role biar aman
        $allowed = ['user', 'planning', 'aps', 'sudo', 'monitoring', 'capacity', 'rosso', 'gbn', 'celup', 'covering'];
        if (!in_array($role, $allowed, true)) {
            $role = 'user';
        }

        // ===== 1) Ambil pengaduan baru =====
        // - kalau role != user : filter target_role = role
        // - kalau role == user : ambil pengaduan yg dibuat si username (opsional, sesuai sistem kamu)
        $newPengaduan = $this->pengaduanModel->getNewPengaduan([
            'role'    => $role,
            'username'=> $username,
            'last_id' => $lastId,
            'limit'   => 50,
        ]);

        // ===== 2) Ambil reply baru =====
        // reply diambil dengan filter pengaduan yang relevan untuk role tsb,
        // lalu reply id_reply > last_reply_id
        $newReplies = $this->replyModel->getNewRepliesByRole([
            'role'          => $role,
            'username'      => $username,
            'last_reply_id' => $lastReplyId,
            'limit'         => 100,
        ]);

        // ===== 3) Cursor max terbaru =====
        // max_id harus dihitung berdasarkan filter yang sama biar "maxId gak loncat"
        $maxId      = (int) ($this->pengaduanModel->getMaxIdByRole($role, $username) ?? 0);
        $maxReplyId = (int) ($this->replyModel->getMaxReplyIdByRole($role, $username) ?? 0);

        // ===== 4) Normalize payload buat front-end =====
        // tambahin field helper biar front-end gampang render
        foreach ($newPengaduan as &$p) {
            $p['date_iso']       = date('Y-m-d', strtotime($p['created_at']));
            $p['formatted_time'] = date('d M Y (H:i)', strtotime($p['created_at']));
            $p['pdf_url']        = base_url('api/pengaduan/exportPdf/' . $p['id_pengaduan']);
        }
        unset($p);

        return $this->response->setJSON([
            'success'       => true,
            'data'          => $newPengaduan,
            'replies'       => $newReplies,
            'max_id'        => $maxId,
            'max_reply_id'  => $maxReplyId,
        ]);
    }
}
