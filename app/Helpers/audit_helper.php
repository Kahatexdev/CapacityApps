<?php

use CodeIgniter\I18n\Time;
use CodeIgniter\HTTP\RequestInterface;

// audit_helper.php
function audit_set_context(array $ctx)
{
    service('auditContext')->set($ctx);
}



if (!function_exists('log_audit')) {
    function log_audit(array $data)
    {
        $db = \Config\Database::connect();
        $request = service('request');

        $actorName = session('username') ?? 'SYSTEM';
        $actorRole = session('role') ?? 'SYSTEM';

        $insert = [
            'log_time'    => date('Y-m-d H:i:s'),
            'actor_name'  => $actorName,
            'actor_role'  => $actorRole,
            'action'      => strtoupper($data['action']),
            'module'      => strtoupper($data['module']),
            'ref_type'    => strtoupper($data['ref_type']),
            'ref_id'      => $data['ref_id'],
            'message'     => $data['message'] ?? null,
            'payload_old' => isset($data['old']) ? json_encode($data['old']) : null,
            'payload_new' => isset($data['new']) ? json_encode($data['new']) : null,
            'ip_address'  => $request->getIPAddress(),
            'user_agent'  => $request->getUserAgent()?->getAgentString(),
        ];

        // log_message('error', '📝 AUDIT INSERT PAYLOAD: ' . json_encode($insert));

        $db->table('audit_logs')->insert($insert);
    }
}

if (!function_exists('normalize_audit_payload')) {
    function normalize_audit_payload($payload): ?string
    {
        if ($payload === null) return null;

        // kalau sudah string JSON valid
        if (is_string($payload)) {
            $trim = trim($payload);
            if ($trim === '') return null;

            json_decode($trim, true);
            if (json_last_error() === JSON_ERROR_NONE) return $trim;

            // bukan json -> simpan sebagai json string
            return json_encode(['raw' => $trim], JSON_UNESCAPED_UNICODE);
        }

        // array/object
        return json_encode($payload, JSON_UNESCAPED_UNICODE);
    }
}

if (!function_exists('resolveAuditRefs')) {
    function resolveAuditRefs(array $payload = []): array
    {
        $refs = [];

        // 1️⃣ dari payload (POST/PUT)
        foreach ($payload as $key => $value) {
            if (preg_match('/^id_(.+)$/', $key, $m) && is_numeric($value)) {
                $refs[] = [
                    'table' => $m[1],
                    'id'    => (int) $value
                ];
            }
        }

        // 2️⃣ fallback dari URI (/user/5)
        $uri = service('uri');
        $segments = $uri->getSegments();

        foreach ($segments as $seg) {
            if (is_numeric($seg)) {
                $refs[] = [
                    'table' => detectModule(),
                    'id'    => (int) $seg
                ];
                break;
            }
        }

        return $refs;
    }

}