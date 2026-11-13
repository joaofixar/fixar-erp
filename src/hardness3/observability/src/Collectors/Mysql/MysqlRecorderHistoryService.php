<?php
declare(strict_types=1);

namespace Hardness\Observability\Collectors\Mysql;

/** Agrega históricos persistidos em um contrato de API estável e sem estado interno. */
final class MysqlRecorderHistoryService
{
    public const MAX_PAGE_SIZE = 200;
    public const MAX_SERIES_RESULTS = 1000;
    private $repository;

    public function __construct(?MysqlRecorderHistoryRepository $repository = null) { $this->repository = $repository ?? new MysqlRecorderHistoryRepository(); }

    public function history(array $schemas, array $capabilities, int $from, int $to, int $page = 1, int $limit = 100): array
    {
        if ($to < $from || $to - $from > MysqlRecorderHistoryRepository::MAX_PERIOD_SECONDS) { throw new \InvalidArgumentException('mysql_history_invalid_period'); }
        $page = max(1, $page); $limit = max(1, min(self::MAX_PAGE_SIZE, $limit));
        $sources = array();
        foreach (array('events', 'status', 'digests') as $category) {
            try { $sources[$category] = $this->repository->read($category, $schemas, $from, $to); }
            catch (\Throwable $exception) { $sources[$category] = array('category'=>$category, 'records'=>array(), 'files_seen'=>0, 'bytes_read'=>0, 'lines_read'=>0, 'invalid_lines'=>0, 'truncated_last_lines'=>0, 'available'=>false, 'unavailable_reasons'=>array('source_unavailable'), 'limits_reached'=>array()); }
        }

        $timeline = $this->consolidateTimeline($this->timeline($sources['events']['records'], $sources['status']['records'], $capabilities));
        usort($timeline, function (array $left, array $right): int { return $right['timestamp'] <=> $left['timestamp']; });
        $offset = ($page - 1) * $limit; $pageItems = array_slice($timeline, $offset, $limit);
        $status = $this->statusSeries($sources['status']['records'], $capabilities);
        $digests = $this->digestRanking($sources['digests']['records'], $capabilities);
        $unavailable = array(); $limits = array(); $partial = false; $noData = array();
        foreach ($sources as $category => $source) {
            if ($source['files_seen'] === 0) { $noData[] = $category; }
            if (!$source['available']) { $partial = true; $unavailable[] = array('category'=>$category, 'reasons'=>$source['unavailable_reasons']); }
            if ($source['limits_reached'] !== array()) { $partial = true; $limits[$category] = $source['limits_reached']; }
            if ($source['invalid_lines'] > 0 || $source['truncated_last_lines'] > 0) { $partial = true; }
        }
        if ($status['limits_reached'] !== array()) { $partial = true; $limits['aggregation'] = $status['limits_reached']; }
        $flags = $this->flags($sources);
        if ($flags['partial_collections'] > 0 || $flags['file_limit_reached'] || $flags['digest_coverage_limited'] || $flags['lock_coverage_limited'] || $flags['long_query_coverage_limited'] || $flags['transaction_coverage_limited'] || $flags['digest_entries_lost'] > 0) { $partial = true; }
        $metadata = array('baseline_present'=>$flags['baselines'] > 0, 'partial'=>$partial, 'synthetic'=>!empty($flags['synthetic_data']), 'unavailable_sources'=>$unavailable, 'no_data_categories'=>$noData, 'limits_reached'=>$limits, 'read'=>array_map(function (array $source): array { return array('category'=>$source['category'], 'files'=>$source['files_seen'], 'bytes'=>$source['bytes_read'], 'lines'=>$source['lines_read'], 'invalid_lines'=>$source['invalid_lines'], 'truncated_last_lines'=>$source['truncated_last_lines']); }, array_values($sources)));
        $period = array('from'=>date('c', $from), 'to'=>date('c', $to), 'max_seconds'=>MysqlRecorderHistoryRepository::MAX_PERIOD_SECONDS);
        $diagnostics = (new MysqlRecorderDiagnosticEngine())->diagnose($timeline, $status['status'], $status['errors'], $digests, $metadata, $period);
        return array(
            'contract_version'=>'mysql-history.v1',
            'generated_at'=>date('c'),
            'period'=>$period,
            'scope'=>array('schema_count'=>count(array_filter($schemas,function ($schema): bool { return $schema !== 'server'; })), 'server_metrics'=>in_array('server',$schemas,true)),
            'pagination'=>array('page'=>$page, 'limit'=>$limit, 'total'=>count($timeline), 'has_more'=>$offset + count($pageItems) < count($timeline)),
            'timeline'=>$pageItems,
            'diagnostics'=>$diagnostics, 'status_series'=>$status['status'], 'error_series'=>$status['errors'], 'digest_ranking'=>$digests,
            'signals'=>$flags,
            'metadata'=>$metadata,
        );
    }

    private function timeline(array $events, array $statuses, array $capabilities): array
    {
        $items = array();
        foreach ($events as $record) {
            $common = array('at'=>(string) ($record['collected_at'] ?? ''), 'timestamp'=>(int) $record['_history_timestamp'], 'schema'=>$this->publicSchema($record, $capabilities), 'partial'=>!empty($record['collection']['partial']), 'synthetic'=>!empty($record['synthetic']));
            foreach ($record['locks'] ?? array() as $item) { if (is_array($item)) { $items[] = array_merge($common, array('type'=>'lock', 'duration_seconds'=>max((int) ($item['waiting_seconds'] ?? 0), (int) ($item['blocking_seconds'] ?? 0)), 'details'=>$this->eventDetails($item, $capabilities))); } }
            foreach ($record['long_queries'] ?? array() as $item) { if (is_array($item)) { $items[] = array_merge($common, array('type'=>'long_query', 'duration_seconds'=>(int) ($item['seconds'] ?? 0), 'details'=>$this->eventDetails($item, $capabilities))); } }
            foreach ($record['long_transactions'] ?? array() as $item) { if (is_array($item)) { $items[] = array_merge($common, array('type'=>'long_transaction', 'duration_seconds'=>(int) ($item['age_seconds'] ?? 0), 'details'=>$this->eventDetails($item, $capabilities))); } }
            if (!empty($record['collection']['partial'])) { $items[] = array_merge($common, array('type'=>'collector_health', 'duration_seconds'=>0, 'details'=>array('conditions'=>array('partial_collection'), 'failed_sections'=>$this->failureSections($record['collection']['failures'] ?? array())))); }
        }
        foreach ($statuses as $record) {
            if (($record['sample_type'] ?? '') !== 'collector_health') { continue; }
            $items[] = array('at'=>(string) ($record['collected_at'] ?? ''), 'timestamp'=>(int) $record['_history_timestamp'], 'schema'=>$this->publicSchema($record, $capabilities), 'type'=>'collector_health', 'duration_seconds'=>0, 'partial'=>true, 'details'=>array('conditions'=>array_values(array_filter($record['conditions'] ?? array(), 'is_string')), 'categories'=>array_values(array_filter($record['categories'] ?? array(), 'is_string')), 'failed_sections'=>$this->failureSections($record['failures'] ?? array())));
        }
        return $items;
    }

    private function eventDetails(array $item, array $capabilities): array
    {
        $safe = $item;
        foreach (array('absolute', 'delta', 'password', 'credentials') as $field) { unset($safe[$field]); }
        $queryFields = array('query', 'waiting_query', 'blocking_query');
        foreach ($queryFields as $field) {
            if (!isset($safe[$field])) { continue; }
            $query = (string) $safe[$field]; unset($safe[$field]);
            $fingerprintField = $field === 'query' ? 'fingerprint' : str_replace('_query', '_fingerprint', $field);
            if (!isset($safe[$fingerprintField])) { $safe[$fingerprintField] = hash('sha256', $query); }
            if (!empty($capabilities['full_sql'])) { $safe[$field] = $query; }
            else { $safe[$field . '_summary'] = mb_strlen($query) > 160 ? mb_substr($query, 0, 159) . '…' : $query; }
        }
        if (empty($capabilities['full_sql'])) { unset($safe['tables'], $safe['possible_blockers']); }
        if (empty($capabilities['connection_identity'])) {
            foreach (array('user', 'host', 'database', 'waiting_user', 'waiting_host', 'blocking_user', 'blocking_host') as $field) { unset($safe[$field]); }
            unset($safe['object']);
        }
        return $safe;
    }

    /** Agrupa amostras consecutivas do mesmo evento sem confundi-las com incidentes distintos. */
    private function consolidateTimeline(array $items): array
    {
        usort($items, function (array $left, array $right): int { return $left['timestamp'] <=> $right['timestamp']; });
        $result = array(); $active = array();
        foreach ($items as $item) {
            $key = $this->timelineIdentity($item);
            if ($key === null) { $item['sample_count']=1; $result[]=$item; continue; }
            $previousIndex = $active[$key] ?? null;
            if ($previousIndex === null || (int)$item['timestamp'] - (int)$result[$previousIndex]['timestamp'] > 30) {
                $item['first_observed_at']=$item['at']; $item['last_observed_at']=$item['at']; $item['sample_count']=1;
                $result[]=$item; $active[$key]=count($result)-1; continue;
            }
            $previous=&$result[$previousIndex];
            $previous['at']=$item['at']; $previous['timestamp']=$item['timestamp']; $previous['last_observed_at']=$item['at'];
            $previous['sample_count']=(int)($previous['sample_count']??1)+1;
            $previous['duration_seconds']=max((int)($previous['duration_seconds']??0),(int)($item['duration_seconds']??0));
            $previous['partial']=!empty($previous['partial'])||!empty($item['partial']);
            $details=is_array($item['details']??null)?$item['details']:array();$priorDetails=is_array($previous['details']??null)?$previous['details']:array();
            foreach(array('seconds','age_seconds','waiting_seconds','blocking_seconds','rows_locked','rows_modified','tables_locked') as $field){if(isset($priorDetails[$field])||isset($details[$field])){$details[$field]=max((int)($priorDetails[$field]??0),(int)($details[$field]??0));}}
            $previous['details']=$details; unset($previous);
        }
        return $result;
    }

    private function timelineIdentity(array $item): ?string
    {
        $type=(string)($item['type']??''); if(!in_array($type,array('lock','long_query','long_transaction'),true)){return null;}
        $details=is_array($item['details']??null)?$item['details']:array();
        if($type==='lock'){$identity=array($details['waiting_process_id']??0,$details['blocking_process_id']??0,$details['waiting_fingerprint']??'',$details['blocking_fingerprint']??'',$details['object']??'');}
        elseif($type==='long_transaction'){$identity=array($details['transaction_id']??'',$details['process_id']??0,$details['fingerprint']??'');}
        else{$identity=array($details['process_id']??0,$details['fingerprint']??'');}
        return hash('sha256',$type.'|'.(string)($item['schema']??'').'|'.json_encode($identity));
    }

    private function statusSeries(array $records, array $capabilities): array
    {
        $status = array(); $errors = array();
        foreach ($records as $record) {
            if (($record['sample_type'] ?? '') === 'collector_health') { continue; }
            $counters = is_array($record['counters'] ?? null) ? $record['counters'] : array();
            $baseline = !empty($counters['baseline']);
            $absolute = is_array($counters['absolute'] ?? null) ? $counters['absolute'] : array();
            $status[] = array('at'=>(string) ($record['collected_at'] ?? ''), 'schema'=>$this->publicSchema($record, $capabilities), 'synthetic'=>!empty($record['synthetic']), 'baseline'=>$baseline, 'baseline_reason'=>$counters['baseline_reason'] ?? null, 'reset'=>($counters['baseline_reason'] ?? null) === 'counter_reset', 'mysql_restart'=>!empty($counters['mysql_restart_detected']), 'gauges'=>array('Threads_connected'=>$absolute['Threads_connected'] ?? null, 'Threads_running'=>$absolute['Threads_running'] ?? null), 'delta'=>$baseline || !is_array($counters['delta'] ?? null) ? null : $counters['delta'], 'partial'=>!empty($record['collection']['partial']));
            foreach ($counters['errors'] ?? array() as $error) {
                if (!is_array($error)) { continue; } $errorBaseline = !empty($error['baseline']);
                $errors[] = array('at'=>(string) ($record['collected_at'] ?? ''), 'schema'=>$this->publicSchema($record, $capabilities), 'synthetic'=>!empty($record['synthetic']), 'number'=>(int) ($error['number'] ?? 0), 'name'=>(string) ($error['name'] ?? ''), 'sql_state'=>(string) ($error['sql_state'] ?? ''), 'baseline'=>$errorBaseline, 'baseline_reason'=>$error['baseline_reason'] ?? null, 'delta'=>$errorBaseline || !is_array($error['delta'] ?? null) ? null : $error['delta']);
            }
        }
        $limits = array();
        if (count($status) > self::MAX_SERIES_RESULTS) { $status = array_slice($status, -self::MAX_SERIES_RESULTS); $limits[] = 'status_series'; }
        if (count($errors) > self::MAX_SERIES_RESULTS) { $errors = array_slice($errors, -self::MAX_SERIES_RESULTS); $limits[] = 'error_series'; }
        return array('status'=>$status, 'errors'=>$errors, 'limits_reached'=>$limits);
    }

    private function digestRanking(array $records, array $capabilities): array
    {
        $ranking = array();
        foreach ($records as $record) {
            foreach (($record['counters']['items'] ?? array()) as $item) {
                if (!is_array($item) || !empty($item['baseline']) || !is_array($item['delta'] ?? null)) { continue; }
                $id = (string) ($item['digest'] ?? ''); if ($id === '') { continue; }
                $key = (string) ($record['database'] ?? '') . "\0" . $id;
                if (!isset($ranking[$key])) {
                    $ranking[$key] = array('schema'=>$this->publicSchema($record, $capabilities), 'digest'=>$id, 'fingerprint'=>hash('sha256', (string) ($item['query'] ?? $id)), 'activity'=>array(), 'metrics'=>array(), 'first_seen'=>null, 'last_seen'=>null, 'synthetic'=>!empty($record['synthetic']));
                    if (!empty($capabilities['full_sql'])) { $ranking[$key]['sql'] = (string) ($item['query'] ?? ''); } else { $ranking[$key]['sql_summary'] = mb_substr((string) ($item['query'] ?? ''), 0, 160); }
                }
                foreach ($item['delta'] as $counter => $value) { if (is_numeric($value)) { $ranking[$key]['activity'][$counter] = ($ranking[$key]['activity'][$counter] ?? 0.0) + (float) $value; } }
                $ranking[$key]['synthetic'] = $ranking[$key]['synthetic'] || !empty($record['synthetic']);
                $ranking[$key]['first_seen'] = $this->earliest($ranking[$key]['first_seen'], $item['first_seen'] ?? null);
                $ranking[$key]['last_seen'] = $this->latest($ranking[$key]['last_seen'], $item['last_seen'] ?? null);
                $ranking[$key]['metrics']['maximum_time_ms'] = max((float)($ranking[$key]['metrics']['maximum_time_ms']??0), (float)($item['maximum_time_ms']??0));
            }
        }
        $ranking = array_values($ranking);
        foreach ($ranking as &$entry) {
            $activity=$entry['activity']; $executions=(float)($activity['executions']??0); $examined=(float)($activity['rows_examined']??0); $returned=(float)($activity['rows_sent']??0);
            $entry['metrics']=array(
                'executions'=>$executions, 'total_time_ms'=>(float)($activity['total_time_ms']??0),
                'average_time_ms'=>$executions>0?round((float)($activity['total_time_ms']??0)/$executions,3):null,
                'maximum_time_ms'=>(float)($entry['metrics']['maximum_time_ms']??0), 'maximum_time_scope'=>'accumulated_observation',
                'lock_time_ms'=>(float)($activity['lock_time_ms']??0), 'rows_examined'=>$examined,
                'rows_returned'=>$returned, 'rows_affected'=>(float)($activity['rows_affected']??0),
                'rows_examined_per_execution'=>$executions>0?round($examined/$executions,3):null,
                'examined_to_returned_ratio'=>$returned>0?round($examined/$returned,3):($examined>0?null:0.0),
                'errors'=>(float)($activity['errors']??0), 'warnings'=>(float)($activity['warnings']??0),
                'temporary_tables'=>(float)($activity['temporary_tables']??0), 'temporary_disk_tables'=>(float)($activity['temporary_disk_tables']??0),
                'no_index_used'=>(float)($activity['no_index_used']??0), 'no_good_index_used'=>(float)($activity['no_good_index_used']??0),
            );
        }
        unset($entry);
        usort($ranking, function (array $left, array $right): int { $time = ($right['activity']['total_time_ms'] ?? 0) <=> ($left['activity']['total_time_ms'] ?? 0); return $time !== 0 ? $time : (($right['activity']['executions'] ?? 0) <=> ($left['activity']['executions'] ?? 0)); });
        return array_slice($ranking, 0, 100);
    }

    private function earliest($current, $candidate): ?string
    {
        if (!is_string($candidate) || $candidate === '') { return is_string($current) ? $current : null; }
        return !is_string($current) || $current === '' || strtotime($candidate) < strtotime($current) ? $candidate : $current;
    }

    private function latest($current, $candidate): ?string
    {
        if (!is_string($candidate) || $candidate === '') { return is_string($current) ? $current : null; }
        return !is_string($current) || $current === '' || strtotime($candidate) > strtotime($current) ? $candidate : $current;
    }

    private function flags(array $sources): array
    {
        $flags = array('baselines'=>0, 'resets'=>0, 'mysql_restarts'=>0, 'disappeared_digests'=>array(), 'partial_collections'=>0, 'file_limit_reached'=>false, 'digest_coverage_limited'=>false, 'lock_coverage_limited'=>false, 'long_query_coverage_limited'=>false, 'transaction_coverage_limited'=>false, 'digest_entries_lost'=>0, 'performance_budget_exceeded'=>false, 'synthetic_data'=>false);
        foreach ($sources['status']['records'] as $record) {
            if (!empty($record['synthetic'])) { $flags['synthetic_data'] = true; }
            $counters = $record['counters'] ?? array(); if (!empty($counters['baseline'])) { $flags['baselines']++; } if (($counters['baseline_reason'] ?? null) === 'counter_reset') { $flags['resets']++; } if (!empty($counters['mysql_restart_detected'])) { $flags['mysql_restarts']++; }
            $digestLost=(int)($counters['absolute']['Performance_schema_digest_lost']??0);if($digestLost>$flags['digest_entries_lost']){$flags['digest_entries_lost']=$digestLost;}
            if (!empty($record['collection']['partial'])) { $flags['partial_collections']++; }
            if (in_array('daily_file_limit_reached', $record['conditions'] ?? array(), true)) { $flags['file_limit_reached'] = true; }
            if (in_array('performance_budget_exceeded', $record['conditions'] ?? array(), true)) { $flags['performance_budget_exceeded'] = true; }
        }
        foreach ($sources['events']['records'] as $record) { if (!empty($record['synthetic'])) { $flags['synthetic_data'] = true; } if (!empty($record['collection']['partial'])) { $flags['partial_collections']++; } $coverage=$record['collection']['coverage_limited']??array();if(!empty($coverage['locks'])){$flags['lock_coverage_limited']=true;}if(!empty($coverage['long_queries'])){$flags['long_query_coverage_limited']=true;}if(!empty($coverage['long_transactions'])){$flags['transaction_coverage_limited']=true;} }
        foreach ($sources['digests']['records'] as $record) {
            if (!empty($record['synthetic'])) { $flags['synthetic_data'] = true; }
            if (!empty($record['counters']['coverage_limited'])) { $flags['digest_coverage_limited'] = true; }
            foreach ($record['counters']['items'] ?? array() as $item) { if (!empty($item['baseline'])) { $flags['baselines']++; if (($item['baseline_reason'] ?? null) === 'counter_reset') { $flags['resets']++; } } }
            foreach ($record['counters']['disappeared_digests'] ?? array() as $digest) { if (is_string($digest)) { $flags['disappeared_digests'][$digest] = true; } }
            if (!empty($record['collection']['partial'])) { $flags['partial_collections']++; }
        }
        $flags['disappeared_digests'] = array_keys($flags['disappeared_digests']); return $flags;
    }

    private function failureSections(array $failures): array
    {
        $sections = array(); foreach ($failures as $failure) { $section = is_array($failure) ? ($failure['section'] ?? null) : null; if (is_string($section) && preg_match('/^[a-z0-9_-]+$/iD', $section) === 1) { $sections[$section] = true; } } return array_keys($sections);
    }

    private function publicSchema(array $record, array $capabilities): string
    {
        return !empty($capabilities['connection_identity']) ? (string) ($record['database'] ?? '') : 'current';
    }
}
