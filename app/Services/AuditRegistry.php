<?php

namespace App\Services;

class AuditRegistry
{
    protected array $data = [];

    public function set(array $data): void
    {
        $this->data = $data;
    }

    public function get(): array
    {
        return $this->data;
    }
}
