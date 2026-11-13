<?php
declare(strict_types=1);

namespace Hardness\Observability\Severity;

/** Thresholds exclusivamente simulados; serão substituíveis na etapa de integração. */
final class PrototypeThresholds
{
    public const CPU_ATTENTION_PERCENT = 70;
    public const CPU_CRITICAL_PERCENT = 90;
    public const MEMORY_ATTENTION_PERCENT = 70;
    public const MEMORY_CRITICAL_PERCENT = 85;
    public const DISK_ATTENTION_PERCENT = 80;
    public const DISK_CRITICAL_PERCENT = 90;
    public const PROCESS_ATTENTION_SECONDS = 5;
    public const PROCESS_CRITICAL_SECONDS = 15;
    public const LOCK_ATTENTION_SECONDS = 3;
    public const LOCK_CRITICAL_SECONDS = 10;
}
