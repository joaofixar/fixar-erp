<?php
declare(strict_types=1);

namespace Hardness\Observability\Collectors\Mysql;

/** Calcula atividade do período sem confundir contadores cumulativos com deltas. */
final class MysqlRecorderDeltaCalculator
{
    private const DIGEST_COUNTERS = array(
        'executions', 'total_time_ms', 'lock_time_ms', 'rows_examined', 'rows_sent', 'rows_affected',
        'errors', 'warnings', 'temporary_tables', 'temporary_disk_tables', 'no_index_used', 'no_good_index_used',
    );
    private const STATUS_COUNTERS = array(
        'Aborted_clients', 'Aborted_connects', 'Connection_errors_internal', 'Connection_errors_max_connections',
        'Connection_errors_peer_address', 'Connections', 'Created_tmp_disk_tables', 'Created_tmp_tables', 'Questions', 'Slow_queries',
        'Performance_schema_digest_lost',
    );

    public function digests(array $current, ?array $previous, bool $forceBaseline = false): array
    {
        $old = array();
        foreach ($previous ?? array() as $digest) {
            if (isset($digest['digest'])) { $old[(string) $digest['digest']] = $digest; }
        }
        $records = array(); $seen = array();
        foreach ($current as $digest) {
            $id = (string) ($digest['digest'] ?? '');
            if ($id === '') { continue; }
            $seen[$id] = true;
            $prior = $old[$id] ?? null;
            $reason = $forceBaseline ? 'mysql_restart' : ($prior === null ? 'new_digest' : $this->resetReason($digest, $prior, self::DIGEST_COUNTERS));
            $records[] = array(
                'digest' => $id,
                'query' => (string) ($digest['query'] ?? ''),
                'first_seen' => (string) ($digest['first_seen'] ?? ''),
                'last_seen' => (string) ($digest['last_seen'] ?? ''),
                'average_time_ms' => (float) ($digest['average_time_ms'] ?? 0),
                'maximum_time_ms' => (float) ($digest['maximum_time_ms'] ?? 0),
                'baseline' => $reason !== null,
                'baseline_reason' => $reason,
                'absolute' => $this->only($digest, self::DIGEST_COUNTERS),
                'delta' => $reason === null ? $this->subtract($digest, $prior, self::DIGEST_COUNTERS) : null,
            );
        }
        $disappeared = array();
        foreach ($old as $id => $ignored) { if (!isset($seen[$id])) { $disappeared[] = $id; } }
        return array('items' => $records, 'disappeared_digests' => $disappeared);
    }

    public function status(array $currentStatus, array $currentErrors, ?array $previousStatus, ?array $previousErrors): array
    {
        $restart = $previousStatus !== null && isset($previousStatus['Uptime'], $currentStatus['Uptime']) && (float) $currentStatus['Uptime'] < (float) $previousStatus['Uptime'];
        $reason = $restart ? 'mysql_restart' : ($previousStatus === null ? 'no_previous_sample' : $this->resetReason($currentStatus, $previousStatus, self::STATUS_COUNTERS));
        $oldErrors = array();
        foreach ($previousErrors ?? array() as $error) { $oldErrors[(string) ($error['number'] ?? '')] = $error; }
        $errors = array();
        foreach ($currentErrors as $error) {
            $prior = $oldErrors[(string) ($error['number'] ?? '')] ?? null;
            $errorReason = $restart ? 'mysql_restart' : ($prior === null ? 'new_error_counter' : $this->resetReason($error, $prior, array('raised', 'handled')));
            $errors[] = array(
                'number' => (int) ($error['number'] ?? 0), 'name' => (string) ($error['name'] ?? ''), 'sql_state' => (string) ($error['sql_state'] ?? ''),
                'first_seen' => (string) ($error['first_seen'] ?? ''), 'last_seen' => (string) ($error['last_seen'] ?? ''),
                'baseline' => $errorReason !== null, 'baseline_reason' => $errorReason,
                'absolute' => $this->only($error, array('raised', 'handled')),
                'delta' => $errorReason === null ? $this->subtract($error, $prior, array('raised', 'handled')) : null,
            );
        }
        return array(
            'mysql_restart_detected' => $restart,
            'baseline' => $reason !== null,
            'baseline_reason' => $reason,
            'absolute' => $currentStatus,
            'delta' => $reason === null ? $this->subtract($currentStatus, $previousStatus, self::STATUS_COUNTERS) : null,
            'errors' => $errors,
        );
    }

    /** Remove acumulados do histórico e conserva somente baselines ou atividade real. */
    public function compactDigestsForHistory(array $calculated): array
    {
        $items = array();
        foreach ($calculated['items'] ?? array() as $item) {
            if (!is_array($item)) { continue; }
            $active = !empty($item['baseline']);
            foreach (is_array($item['delta'] ?? null) ? $item['delta'] : array() as $value) {
                if (is_numeric($value) && (float) $value > 0) { $active = true; break; }
            }
            if (!$active) { continue; }
            unset($item['absolute']);
            $items[] = $item;
        }
        $calculated['items'] = $items;
        return $calculated;
    }

    /** Estado precisa somente da identidade e dos acumulados usados no próximo delta. */
    public function compactDigestsForState(array $digests): array
    {
        $result = array();
        foreach ($digests as $digest) {
            $id = (string) ($digest['digest'] ?? ''); if ($id === '') { continue; }
            $result[] = array_merge(array('digest'=>$id), $this->only($digest, self::DIGEST_COUNTERS));
        }
        return $result;
    }

    private function resetReason(array $current, array $previous, array $keys): ?string
    {
        foreach ($keys as $key) {
            if ((float) ($current[$key] ?? 0) < (float) ($previous[$key] ?? 0)) { return 'counter_reset'; }
        }
        return null;
    }

    private function subtract(array $current, array $previous, array $keys): array
    {
        $result = array();
        foreach ($keys as $key) { $result[$key] = max(0, (float) ($current[$key] ?? 0) - (float) ($previous[$key] ?? 0)); }
        return $result;
    }

    private function only(array $values, array $keys): array
    {
        $result = array();
        foreach ($keys as $key) { $result[$key] = $values[$key] ?? 0; }
        return $result;
    }
}
