<?php

namespace App\Services;

class AuditContext
{
    protected static array $before = [];

    public static function set(array $data): void
    {
        self::$before = $data;
    }

    public static function get(): array
    {
        return self::$before;
    }

    public static function clear(): void
    {
        self::$before = [];
    }
}
