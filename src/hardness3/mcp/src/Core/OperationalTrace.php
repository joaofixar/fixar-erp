<?php

declare(strict_types=1);

namespace HardnessMcp\Core;

/** Request-local diagnostic state. It deliberately contains no credential material. */
final class OperationalTrace
{
    private static string $correlationId = '';
    private static string $stage = 'bootstrap';

    public static function begin(string $correlationId): void { self::$correlationId = $correlationId; self::$stage = 'bootstrap'; }
    public static function reach(string $stage): void { self::$stage = $stage; }
    public static function correlationId(): string { return self::$correlationId; }
    public static function stage(): string { return self::$stage; }
}
