<?php
declare(strict_types=1);

namespace Hardness\Observability\Collectors\Mysql;

/** Executa amostras rápidas e agenda categorias cumulativas em frequências distintas. */
final class MysqlRecorderRunner
{
    public const STATUS_INTERVAL_SECONDS = 60;
    public const DIGEST_INTERVAL_SECONDS = 300;
    private $collector;
    private $storage;
    private $deltas;
    private $clock;
    private $sleeper;

    public function __construct(MysqlRecorderCollector $collector, MysqlRecorderStorage $storage, ?MysqlRecorderDeltaCalculator $deltas = null, ?callable $clock = null, ?callable $sleeper = null)
    {
        $this->collector = $collector; $this->storage = $storage; $this->deltas = $deltas ?? new MysqlRecorderDeltaCalculator();
        $this->clock = $clock ?? function (): float { return microtime(true); };
        $this->sleeper = $sleeper ?? function (float $seconds): void { if ($seconds > 0) { usleep((int) round($seconds * 1000000)); } };
    }

    public function run(string $database, bool $once = false, int $durationSeconds = 50, int $intervalSeconds = 10, int $longQuerySeconds = 15, int $longTransactionSeconds = 15, int $retentionDays = 7): array
    {
        if (preg_match('/^[A-Za-z0-9_]+$/', $database) !== 1) { throw new \InvalidArgumentException('invalid_database_name'); }
        $durationSeconds = max(1, min(300, $durationSeconds));
        $intervalSeconds = max(1, min(60, $intervalSeconds));
        $retentionDays = max(1, min(90, $retentionDays));
        $lock = new MysqlRecorderRunLock();
        if (!$lock->acquire($this->storage->lockPath($database))) {
            return array('active' => false, 'reason' => 'another_collection_is_active', 'samples' => 0, 'partial' => false, 'failures' => array());
        }

        $started = $this->now(); $deadline = $started + $durationSeconds; $samples = 0; $failures = array(); $limits = array();
        try {
            try {
                $retention = $this->storage->retain($retentionDays, (int) $started);
                if ($retention['failures'] !== array()) { $failures[] = array('section' => 'retention', 'message' => 'mysql_recorder_retention_partial'); }
            }
            catch (\Throwable $exception) { $retention = array('removed' => array(), 'failures' => array('retention_failed'), 'retention_days' => $retentionDays); $failures[] = $this->failure('retention', $exception); }

            do {
                $sampleStarted = $this->now();
                $state = $this->storage->loadState($database) ?? array('schema_version' => 1, 'database' => $database);
                $fast = $this->collector->collectFast($database, $longQuerySeconds, $longTransactionSeconds);
                $fast['runner'] = array('started_at_epoch' => $sampleStarted, 'success' => !$fast['collection']['partial']);
                $this->persist('events', $fast, $limits, $failures);

                $restart = false;
                if ($this->due($state['status_collected_at'] ?? null, $sampleStarted, self::STATUS_INTERVAL_SECONDS)) {
                    $status = $this->collector->collectStatus();
                    $status['database'] = $database;
                    $statusFailed = $this->collectionFailed($status['collection'], 'status');
                    $errorsFailed = $this->collectionFailed($status['collection'], 'errors');
                    $statusValues = $statusFailed ? ($state['status_absolute'] ?? array()) : $status['status'];
                    $errorValues = $errorsFailed ? array() : $status['errors'];
                    $calculated = $this->deltas->status($statusValues, $errorValues, $state['status_absolute'] ?? null, $state['errors_absolute'] ?? null);
                    if ($statusFailed) {
                        $calculated['mysql_restart_detected'] = false; $calculated['baseline'] = true;
                        $calculated['baseline_reason'] = 'collection_failed'; $calculated['absolute'] = null; $calculated['delta'] = null;
                    }
                    if ($errorsFailed) { $calculated['errors'] = array(); $calculated['errors_unavailable'] = true; }
                    $restart = !$statusFailed && $calculated['mysql_restart_detected'];
                    $status['counters'] = $calculated;
                    unset($status['status'], $status['errors']);
                    $this->persist('status', $status, $limits, $failures);
                    $state['status_collected_at'] = $sampleStarted;
                    if (!$statusFailed) { $state['status_absolute'] = $statusValues; }
                    /* State keeps the collector shape, not the persisted absolute/delta envelope. */
                    if (!$errorsFailed) { $state['errors_absolute'] = $this->rawErrors($calculated['errors']); }
                    if ($restart) { $state['mysql_restart_at'] = $sampleStarted; }
                }

                if ($this->due($state['digests_collected_at'] ?? null, $sampleStarted, self::DIGEST_INTERVAL_SECONDS)) {
                    $digests = $this->collector->collectDigests($database);
                    $restartSinceLastDigest = $restart || (isset($state['mysql_restart_at']) && (!isset($state['digests_collected_at']) || (float) $state['mysql_restart_at'] > (float) $state['digests_collected_at']));
                    $digestsFailed = $this->collectionFailed($digests['collection'], 'digests');
                    $calculated = $digestsFailed
                        ? array('items' => array(), 'disappeared_digests' => array(), 'unavailable' => true)
                        : $this->deltas->digests($digests['digests'], $state['digests_absolute'] ?? null, $restartSinceLastDigest);
                    $digestCoverageLimited=!empty($digests['collection']['coverage_limited']);
                    if($digestCoverageLimited){$calculated['disappeared_digests']=array();}
                    $calculated['coverage_limited']=$digestCoverageLimited;
                    if (!$digestsFailed) { $state['digests_absolute'] = $this->deltas->compactDigestsForState($digests['digests']); }
                    $digests['counters'] = $this->deltas->compactDigestsForHistory($calculated);
                    $digests['collection']['counts']['observed_digests'] = $digests['collection']['counts']['digests'] ?? 0;
                    $digests['collection']['counts']['digests'] = count($digests['counters']['items']);
                    unset($digests['digests']);
                    if ($this->hasDigestHistory($digests['counters'])) { $this->persist('digests', $digests, $limits, $failures); }
                    $state['digests_collected_at'] = $sampleStarted;
                }

                $state['schema_version'] = 1; $state['database'] = $database; $state['updated_at'] = date('c', (int) $sampleStarted);
                try { $this->storage->saveState($database, $state); } catch (\Throwable $exception) { $failures[] = $this->failure('state', $exception); }
                $samples++;
                if ($once) { break; }
                $next = min($deadline, $sampleStarted + $intervalSeconds);
                $remaining = $next - $this->now();
                if ($remaining > 0) { ($this->sleeper)($remaining); }
            } while ($this->now() < $deadline);

            if ($limits !== array() || $failures !== array()) { $this->persistHealth($database, $limits, $failures); }
            return array(
                'active' => true, 'samples' => $samples, 'duration_ms' => round(($this->now() - $started) * 1000, 2),
                'partial' => $failures !== array() || $limits !== array(), 'failures' => $failures, 'file_limits' => array_values(array_unique($limits)), 'retention' => $retention,
            );
        } finally { $lock->release(); }
    }

    private function persist(string $category, array $record, array &$limits, array &$failures): void
    {
        try {
            $result = $this->storage->append($category, $record);
            if ($result['limit_reached']) { $limits[] = $category; }
        } catch (\Throwable $exception) { $failures[] = $this->failure($category, $exception); }
    }

    private function persistHealth(string $database, array $limits, array &$failures): void
    {
        $health = array(
            'schema_version' => 2, 'sample_type' => 'collector_health', 'collected_at' => date('c'), 'database' => $database,
            'healthy' => false,
            'conditions' => array_values(array_filter(array($limits !== array() ? 'daily_file_limit_reached' : null, $failures !== array() ? 'partial_failure' : null))),
            'categories' => array_values(array_unique($limits)), 'failures' => $failures,
        );
        try { $this->storage->append('status', $health); } catch (\Throwable $exception) { $failures[] = $this->failure('health', $exception); }
    }

    private function due($last, float $now, int $frequency): bool { return !is_numeric($last) || $now - (float) $last >= $frequency; }
    private function collectionFailed(array $collection, string $section): bool
    {
        foreach ($collection['failures'] ?? array() as $failure) { if (($failure['section'] ?? '') === $section) { return true; } }
        return false;
    }
    private function now(): float { return (float) ($this->clock)(); }
    private function hasDigestHistory(array $counters): bool { return ($counters['items'] ?? array()) !== array() || ($counters['disappeared_digests'] ?? array()) !== array() || !empty($counters['unavailable']) || !empty($counters['coverage_limited']); }
    private function failure(string $section, \Throwable $exception): array { return array('section' => $section, 'message' => substr((string) preg_replace('/(?:password|passwd|pwd)\s*[=:]\s*\S+/i', 'credential=[hidden]', $exception->getMessage()), 0, 300)); }

    private function rawErrors(array $records): array
    {
        $result = array();
        foreach ($records as $record) {
            $result[] = array_merge(
                array('number' => $record['number'], 'name' => $record['name'], 'sql_state' => $record['sql_state'], 'first_seen' => $record['first_seen'], 'last_seen' => $record['last_seen']),
                $record['absolute']
            );
        }
        return $result;
    }
}
