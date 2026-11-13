<?php
declare(strict_types=1);

namespace Hardness\Observability\Severity;

final class Severity
{
    public const NORMAL = 'normal';
    public const ATTENTION = 'attention';
    public const CRITICAL = 'critical';
    public const UNAVAILABLE = 'unavailable';
    public const UNKNOWN = 'unknown';

    public static function all(): array
    {
        return array(self::NORMAL, self::ATTENTION, self::CRITICAL, self::UNAVAILABLE, self::UNKNOWN);
    }

    public static function isValid(string $severity): bool
    {
        return in_array($severity, self::all(), true);
    }
}
