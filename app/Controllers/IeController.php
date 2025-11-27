<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;

class IeController extends BaseController
{
    public function __construct()
    {
        if ($this->filters   = ['role' => ['ie']] != session()->get('role')) {
            return redirect()->to(base_url('/login'));
        }
        $this->isLogedin();
    }
    protected function isLogedin()
    {
        if (!session()->get('id_user')) {
            return redirect()->to(base_url('/login'));
        }
    }
    public function index()
    {
        $role = session()->get('role');

        $data = [
            'role' => session()->get('role'),
            'title' => 'Dashboard',
            'active1' => 'active',
            'active2' => '',
            'targetProd' => 0,
            'produksiBulan' => 0,
            'produksiHari' => 0,
        ];
        return view($role . '/index', $data);
    }
    public function updatesmv()
    {
        $role = session()->get('role');
        $data = [
            'role' => session()->get('role'),
            'title' => 'Dashboard',
            'active1' => 'active',
            'active2' => '',

        ];
        return view($role . '/updateview', $data);
    }
    public function inputsmv()
    {

        $insert = [
            'style' => $this->request->getPost('size'),
            'smv_old' => $this->request->getPost('smvold'),
        ];
        $id = $this->request->getPost('id');
        $smv = $this->request->getPost('smv');
        $update = $this->ApsPerstyleModel->update($id, ['smv' => $smv]);
        if ($update) {
            $input = $this->historysmv->insert($insert);
            if ($input) {
                return redirect()->to(base_url('ie/'))->with('success', 'Smv berhasil di update');
            } else {
                return redirect()->to(base_url('ie/'))->with('error', 'Smv gagal di update');
            }
        } else {
            return redirect()->to(base_url('ie/'))->with('error', 'Smv gagal di update');
        }
    }
    public function gethistory()
    {
        $size = $this->request->getPost('size');
        $data = $this->historysmv->getDataSize($size);

        if (empty($data)) {
            $empty = [
                [
                    'smv_old' => "tidak ada data",
                    'created_at' => "tidak ada data"
                ]
            ];
            return json_encode($empty);
        }

        return json_encode($data);
    }
    public function getServerSide()
    {
        $request = service('request');
        $req = $request->getPost();

        // Ambil semua data dari model (tidak filter di SQL)
        $allData = $this->ApsPerstyleModel->getSmv();

        // Ambil parameter datatable
        $start = $req['start'] ?? 0;
        $length = $req['length'] ?? 10;
        $draw = intval($req['draw'] ?? 1);
        $search = $req['search']['value'] ?? '';
        $orderColIndex = $req['order'][0]['column'] ?? 0;
        $orderDir = $req['order'][0]['dir'] ?? 'asc';
        $columns = ['mastermodel', 'size', 'smv']; // Sesuaikan nama kolom view

        // Optional: filter by search (di PHP)
        if ($search) {
            $allData = array_filter($allData, function ($row) use ($search) {
                return stripos($row['mastermodel'], $search) !== false
                    || stripos($row['size'], $search) !== false
                    || stripos($row['smv'], $search) !== false;
            });
        }

        // Optional: sort (di PHP)
        $orderColumn = $columns[$orderColIndex] ?? 'mastermodel';
        usort($allData, function ($a, $b) use ($orderColumn, $orderDir) {
            return $orderDir === 'asc'
                ? strcmp($a[$orderColumn], $b[$orderColumn])
                : strcmp($b[$orderColumn], $a[$orderColumn]);
        });

        // Pagination
        $recordsTotal = count($allData);
        $data = array_slice($allData, $start, $length);

        return $this->response->setJSON([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsTotal,
            'data' => array_values($data)
        ]);
    }
}
