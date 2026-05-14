<?php

namespace App\Services;

class BranchContext
{
    private static ?int $branchId = null;
    private static bool $isGlobal = false;

    public static function set(?int $branchId): void
    {
        self::$branchId = $branchId;
        self::$isGlobal = $branchId === null;
    }

    public static function get(): ?int
    {
        return self::$branchId;
    }

    public static function isGlobal(): bool
    {
        return self::$isGlobal;
    }

    public static function hasBranch(): bool
    {
        return self::$branchId !== null;
    }

    public static function clear(): void
    {
        self::$branchId = null;
        self::$isGlobal = false;
    }
}
