<?php
declare(strict_types=1);

namespace Hardness\Observability\Severity;

final class SeverityEvaluator
{
    public function percentage(float $value, int $attentionAt, int $criticalAt): string
    {
        if ($value >= $criticalAt) {
            return Severity::CRITICAL;
        }
        if ($value >= $attentionAt) {
            return Severity::ATTENTION;
        }
        return Severity::NORMAL;
    }

    public function process(int $seconds, bool $waitingForLock = false, ?array $thresholds = null): string
    {
        $thresholds = $thresholds ?: array(
            'attention_seconds' => PrototypeThresholds::PROCESS_ATTENTION_SECONDS,
            'critical_seconds' => PrototypeThresholds::PROCESS_CRITICAL_SECONDS,
            'lock_attention_seconds' => PrototypeThresholds::LOCK_ATTENTION_SECONDS,
            'lock_critical_seconds' => PrototypeThresholds::LOCK_CRITICAL_SECONDS,
        );
        if ($waitingForLock && $seconds >= $thresholds['lock_critical_seconds']) {
            return Severity::CRITICAL;
        }
        if ($seconds >= $thresholds['critical_seconds']) {
            return Severity::CRITICAL;
        }
        if (($waitingForLock && $seconds >= $thresholds['lock_attention_seconds']) || $seconds >= $thresholds['attention_seconds']) {
            return Severity::ATTENTION;
        }
        return Severity::NORMAL;
    }

    /** @param string[] $severities */
    public function aggregate(array $severities): string
    {
        if (in_array(Severity::CRITICAL, $severities, true)) {
            return Severity::CRITICAL;
        }
        if (in_array(Severity::ATTENTION, $severities, true)) {
            return Severity::ATTENTION;
        }
        $available = array_filter($severities, function (string $severity): bool {
            return $severity !== Severity::UNAVAILABLE && $severity !== Severity::UNKNOWN;
        });
        if (in_array(Severity::UNKNOWN, $severities, true) && $available === array()) {
            return Severity::UNKNOWN;
        }
        if ($severities !== array() && $available === array()) {
            return Severity::UNAVAILABLE;
        }
        return Severity::NORMAL;
    }

    /** @param string[] $severities */
    public function counts(array $severities): array
    {
        $counts = array(Severity::CRITICAL => 0, Severity::ATTENTION => 0, Severity::UNAVAILABLE => 0);
        foreach ($severities as $severity) {
            if (isset($counts[$severity])) {
                $counts[$severity]++;
            }
        }
        return $counts;
    }
}
