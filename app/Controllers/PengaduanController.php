<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class PengaduanController extends BaseController
{
    public function index()
    {
        // Misalnya user info disimpan di session saat login
        $username = session()->get('username');
        $role     = session()->get('role');

        $pengaduan = $this->pengaduanModel->getPengaduan($username, $role);
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
        $role = $this->role;
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
        $role = $this->role;
        $this->replyModel->save([
            'id_pengaduan' => $id_pengaduan,
            'username'     => $username,
            'isi'          => $isi,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to($role . '/pengaduan')->withInput()->with('success', 'Balasan berhasil dikirin');
    }
    public function Apicreate()
    {
        $username   = $this->request->getPost('username');
        $targetRole = $this->request->getPost('target_role');
        $isi        = $this->request->getPost('isi');

        $this->pengaduanModel->save([
            'username'    => $username,
            'target_role' => $targetRole,
            'isi'         => $isi
        ]);

        return $this->respond(['status' => 'success', 'message' => 'Berhasil terkirim']);
    }

    public function Apireply($id_pengaduan)
    {
        $username = $this->request->getPost('username');
        $isi      = $this->request->getPost('isi');

        $this->replyModel->save([
            'id_pengaduan' => $id_pengaduan,
            'username'     => $username,
            'isi'          => $isi,
            'created_at'   => date('Y-m-d H:i:s')
        ]);

        return $this->respond(['status' => 'success', 'message' => 'Balasan terkirim']);
    }


    public function Apipengaduan($username, $role)
    {
        $pengaduan = $this->pengaduanModel->getPengaduan($username, $role);
        // Ambil semua reply per pengaduan
        $reply = [];
        foreach ($pengaduan as $p) {
            $reply[$p['id_pengaduan']] = $this->replyModel->getRepliesByPengaduan($p['id_pengaduan']) ?? null;
        }
        $data = [
            'pengaduan' => $pengaduan,
            'replies' => $reply
        ];
        return $this->response->setJSON($data);
    }
}
