<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class AuditLogController extends BaseController
{
    public function index()
    {
        // default tanggal: 7 hari terakhir
        $dateTo   = date('Y-m-d');
        $dateFrom = date('Y-m-d', strtotime('-7 days'));

        $module = $this->auditLogModel
                    ->distinct()
                    ->select('module')
                    ->findAll();

        $action = $this->auditLogModel
                    ->distinct()
                    ->select('action')
                    ->findAll();
        $data = [
            'active' => $this->active,
            'active1' => $this->active,
            'title' => 'Sudo',
            'role' => session()->get('role'),
            'dateFrom' => $dateFrom,
            'dateTo'   => $dateTo,
            'module'   => $module,
            'action'   => $action
        ];
        return view(session()->get('role') . '/Audit/index', $data);
    }

    public function datatables()
    {
        $request = service('request');
        $db      = db_connect();

        // DataTables params
        $draw   = (int) $request->getGet('draw');
        $start  = (int) $request->getGet('start');
        $length = (int) $request->getGet('length');

        // Custom filters
        $dateFrom = $request->getGet('date_from');
        $dateTo   = $request->getGet('date_to');
        $module   = $request->getGet('module');
        $action   = $request->getGet('action');
        $q        = trim((string) $request->getGet('q'));

        // base builder
        $base = $db->table('audit_logs');
        $recordsTotal = (int) (clone $base)->countAllResults();

        $builder = clone $base;

        // filters
        if (!empty($dateFrom)) $builder->where('DATE(log_time) >=', $dateFrom);
        if (!empty($dateTo))   $builder->where('DATE(log_time) <=', $dateTo);
        if (!empty($module))   $builder->where('module', $module);
        if (!empty($action))   $builder->where('action', $action);

        if ($q !== '') {
            $builder->groupStart()
                ->like('actor_name', $q)
                ->orLike('actor_role', $q)
                ->orLike('message', $q)
                ->orLike('ref_type', $q)
                ->orLike('ref_id', $q)
                ->orLike('ip_address', $q)
            ->groupEnd();
        }

        // filtered count (clone)
        $countBuilder   = clone $builder;
        $recordsFiltered = (int) $countBuilder->countAllResults();

        // ordering DataTables
        $orderColIndex = (int) ($request->getGet('order')[0]['column'] ?? 0);
        $orderDir      = $request->getGet('order')[0]['dir'] ?? 'desc';

        // mapping: index => column
        $columnsMap = [
            0 => 'log_time',
            1 => 'actor_name',
            2 => 'module',
            3 => 'action',
            4 => 'ref_id',
            5 => 'message',
        ];
        $orderBy = $columnsMap[$orderColIndex] ?? 'log_time';

        // data
        $rows = $builder
            ->select('id_log, log_time, actor_name, actor_role, action, module, ref_type, ref_id, message, payload_old, payload_new')
            ->orderBy($orderBy, $orderDir === 'asc' ? 'asc' : 'desc')
            ->limit($length, $start)
            ->get()
            ->getResultArray();

        $data = [];
        foreach ($rows as $r) {
            $logTimeFmt = !empty($r['log_time']) ? date('d-m-Y H:i:s', strtotime($r['log_time'])) : '-';
            $actor      = trim(($r['actor_name'] ?? '-') . (!empty($r['actor_role']) ? " <span class='text-muted'>(" . esc($r['actor_role']) . ")</span>" : ''));

            $module = strtoupper($r['module'] ?? '-');
            $action = strtoupper($r['action'] ?? '-');

            $moduleBadge = "<span class='badge bg-dark px-3 py-2'>{$module}</span>";

            $actionBadge = match ($action) {
                'CREATE'  => "<span class='badge bg-success px-3 py-2'>CREATE</span>",
                'UPDATE'  => "<span class='badge bg-info px-3 py-2'>UPDATE</span>",
                'DELETE'  => "<span class='badge bg-danger px-3 py-2'>DELETE</span>",
                'LOGIN'   => "<span class='badge bg-secondary px-3 py-2'>LOGIN</span>",
                'LOGIN_FAIL' => "<span class='badge bg-danger px-3 py-2'>LOGIN_FAIL</span>",
                'LOGOUT'  => "<span class='badge bg-secondary px-3 py-2'>LOGOUT</span>",
                'APPROVE' => "<span class='badge bg-primary px-3 py-2'>APPROVE</span>",
                'REJECT'  => "<span class='badge bg-warning text-dark px-3 py-2'>REJECT</span>",
                'EXPORT'  => "<span class='badge bg-dark px-3 py-2'>EXPORT</span>",
                default   => "<span class='badge bg-light text-dark px-3 py-2'>{$action}</span>",
            };

            $ref = esc(($r['ref_type'] ?? '-') . ' / ' . ($r['ref_id'] ?? '-'));

            // payload json pretty (safe)
            $old = $this->prettyJson($r['payload_old'] ?? null);
            $new = $this->prettyJson($r['payload_new'] ?? null);

            $infoHtml = "
                <div><b>Waktu:</b> " . esc($logTimeFmt) . "</div>
                <div><b>Actor:</b> " . esc($r['actor_name'] ?? '-') . "</div>
                <div><b>Module:</b> " . esc($module) . " | <b>Action:</b> " . esc($action) . "</div>
                <div><b>Ref:</b> " . esc(($r['ref_type'] ?? '-') . ' / ' . ($r['ref_id'] ?? '-')) . "</div>
                <div><b>Message:</b> " . esc($r['message'] ?? '-') . "</div>
            ";

            $btn = "<button
                        type='button'
                        class='btn btn-sm btn-outline-dark mb-0 btn-audit-detail'
                        data-info=\"" . esc($infoHtml) . "\"
                        data-old=\"" . esc($old) . "\"
                        data-new=\"" . esc($new) . "\"
                    >Detail</button>";

            $data[] = [
                'log_time_fmt' => esc($logTimeFmt),
                'actor'        => $actor,
                'module_badge' => $moduleBadge,
                'action_badge' => $actionBadge,
                'ref'          => $ref,
                'message'      => esc($r['message'] ?? '-'),
                'action'       => $btn,

                // ✅ tambahan: payload disimpan di row-data, bukan di attribute HTML
                'payload_old'  => $old,
                'payload_new'  => $new,

                // opsional kalau mau tampil info dari row juga
                'info_html'    => $infoHtml,
            ];
        }

        return $this->response->setJSON([
            'draw'            => $draw,
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data,
        ]);
    }

    private function prettyJson(?string $json): string
    {
        if (empty($json)) return '{}';

        $decoded = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            // kalau bukan json valid, tetap tampilkan raw
            return $json;
        }
        return json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
