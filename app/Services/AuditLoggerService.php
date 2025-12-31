<?php

namespace App\Services;

use App\Models\AuditLogModel;

class AuditLoggerService
{
    protected AuditLogModel $model;

    public function __construct()
    {
        $this->model = new AuditLogModel();
    }

    public function insert(array $data): bool
    {
        return $this->model->insert([
            'log_time'    => date('Y-m-d H:i:s'),
            'actor_name'  => session('username'),
            'actor_role'  => session('role'),
            'action'      => $data['action'],
            'module'      => $data['module'],
            'ref_type'    => $data['ref_type'],
            'ref_id'      => $data['ref_id'],
            'message'     => $data['message'] ?? null,
            'payload_old' => $data['old'] ?? null,
            'payload_new' => isset($data['new'])
                ? json_encode($this->sanitize($data['new']))
                : null,
            'ip_address'  => service('request')->getIPAddress(),
            'user_agent'  => service('request')->getUserAgent()->getAgentString(),
        ]);
    }

    protected function sanitize(array $payload): array
    {
        unset($payload['password']); // masking
        return $payload;
    }

    // public function logUpdate(array $data): void
    // {
    //     $data['action'] = 'UPDATE';
    //     $this->log($data);
    // }

    // public function logCreate(array $data): void
    // {
    //     $data['action'] = 'CREATE';
    //     $this->log($data);
    // }

    // public function logDelete(array $data): void
    // {
    //     $data['action'] = 'DELETE';
    //     $this->log($data);
    // }
}
