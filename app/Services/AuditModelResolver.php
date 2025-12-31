<?php

namespace App\Services;

use App\Models\BaseAuditModel;

class AuditModelResolver
{
    public static function resolve(string $table): ?BaseAuditModel
    {
        $class = 'App\\Models\\' . ucfirst($table) . 'Model';

        if (!class_exists($class)) {
            return null;
        }

        $model = new $class;

        return ($model instanceof BaseAuditModel) ? $model : null;
    }
}
