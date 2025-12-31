<?php

namespace App\Models;

use CodeIgniter\Model;

class BaseAuditModel extends Model
{
    protected string $refType = '';

    // protected $afterInsert = ['auditInsert'];
    // protected $afterUpdate = ['auditUpdate'];
    // protected $afterDelete = ['auditDelete'];

    // // protected function auditInsert(array $data)
    // // {
    // //     helper('audit');

    // //     if (empty($data['id'])) {
    // //         return $data;
    // //     }

    // //     log_audit([
    // //         'action'   => 'insert',
    // //         'module'   => detectModule(),
    // //         'ref_type' => $this->refType,
    // //         'ref_id'   => $data['id'],
    // //         'message'  => "Insert {$this->refType}",
    // //         'new'      => $data['data'] ?? [],
    // //     ]);

    // //     return $data;
    // // }

    // // protected function auditUpdate(array $data)
    // // {
    // //     helper('audit');

    // //     if (empty($data['id'])) {
    // //         return $data;
    // //     }

    // //     log_audit([
    // //         'action'   => 'update',
    // //         'module'   => detectModule(),
    // //         'ref_type' => $this->refType,
    // //         'ref_id'   => is_array($data['id']) ? $data['id'][0] : $data['id'],
    // //         'message'  => "Update {$this->refType}",
    // //         'old'      => $data['old'] ?? null,
    // //         'new'      => $data['data'] ?? [],
    // //     ]);

    // //     return $data;
    // // }

    // // protected function auditDelete(array $data)
    // // {
    // //     helper('audit');

    // //     if (empty($data['id'])) {
    // //         return $data;
    // //     }

    // //     log_audit([
    // //         'action'   => 'delete',
    // //         'module'   => detectModule(),
    // //         'ref_type' => $this->refType,
    // //         'ref_id'   => $data['id'],
    // //         'message'  => "Delete {$this->refType}",
    // //         'old'      => $data['data'] ?? null,
    // //     ]);

    // //     return $data;
    // // }

}
