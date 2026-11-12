<?php
declare(strict_types=1);

namespace Hardness\Observability\Collectors\Mysql;

/**
 * Amostra pontual do "gravador de voo" MySQL.
 * Todas as instruções SQL são fixas, somente leitura e têm resultado limitado.
 */
final class MysqlRecorderCollector
{
    private const LOCK_RESULT_LIMIT = 100;
    private const TRANSACTION_RESULT_LIMIT = 500;
    private const LONG_QUERY_RESULT_LIMIT = 100;
    private const SERVER_DIGEST_RESULT_LIMIT = 2000;
    private const LOCKS_SQL = "SELECT waiting_thread.PROCESSLIST_ID AS waiting_process_id, blocking_thread.PROCESSLIST_ID AS blocking_process_id, requested_lock.OBJECT_SCHEMA AS object_schema, requested_lock.OBJECT_NAME AS object_name, requested_lock.INDEX_NAME AS index_name, requested_lock.LOCK_TYPE AS lock_type, requested_lock.LOCK_MODE AS requested_lock_mode, blocking_lock.LOCK_MODE AS blocking_lock_mode, waiting_thread.PROCESSLIST_USER AS waiting_user, waiting_thread.PROCESSLIST_HOST AS waiting_host, waiting_thread.PROCESSLIST_DB AS waiting_database, waiting_thread.PROCESSLIST_TIME AS waiting_seconds, waiting_thread.PROCESSLIST_STATE AS waiting_state, waiting_thread.PROCESSLIST_INFO AS waiting_sql, blocking_thread.PROCESSLIST_USER AS blocking_user, blocking_thread.PROCESSLIST_HOST AS blocking_host, blocking_thread.PROCESSLIST_DB AS blocking_database, blocking_thread.PROCESSLIST_TIME AS blocking_seconds, blocking_thread.PROCESSLIST_STATE AS blocking_state, blocking_thread.PROCESSLIST_INFO AS blocking_sql FROM performance_schema.data_lock_waits AS waits INNER JOIN performance_schema.data_locks AS requested_lock ON requested_lock.ENGINE = waits.ENGINE AND requested_lock.ENGINE_LOCK_ID = waits.REQUESTING_ENGINE_LOCK_ID INNER JOIN performance_schema.data_locks AS blocking_lock ON blocking_lock.ENGINE = waits.ENGINE AND blocking_lock.ENGINE_LOCK_ID = waits.BLOCKING_ENGINE_LOCK_ID INNER JOIN performance_schema.threads AS waiting_thread ON waiting_thread.THREAD_ID = waits.REQUESTING_THREAD_ID INNER JOIN performance_schema.threads AS blocking_thread ON blocking_thread.THREAD_ID = waits.BLOCKING_THREAD_ID LIMIT 100";
    private const TRANSACTIONS_SQL = "SELECT trx.TRX_ID AS transaction_id, trx.TRX_STATE AS transaction_state, trx.TRX_STARTED AS started_at, TIMESTAMPDIFF(SECOND, trx.TRX_STARTED, NOW()) AS age_seconds, trx.TRX_WAIT_STARTED AS wait_started_at, trx.TRX_ROWS_LOCKED AS rows_locked, trx.TRX_ROWS_MODIFIED AS rows_modified, trx.TRX_TABLES_LOCKED AS tables_locked, trx.TRX_MYSQL_THREAD_ID AS process_id, thread.PROCESSLIST_USER AS user_name, thread.PROCESSLIST_HOST AS host_name, thread.PROCESSLIST_DB AS database_name, thread.PROCESSLIST_STATE AS process_state, COALESCE(trx.TRX_QUERY, thread.PROCESSLIST_INFO) AS transaction_sql FROM information_schema.INNODB_TRX AS trx LEFT JOIN performance_schema.threads AS thread ON thread.PROCESSLIST_ID = trx.TRX_MYSQL_THREAD_ID WHERE thread.PROCESSLIST_DB = ? ORDER BY trx.TRX_STARTED ASC LIMIT 100";
    private const SERVER_TRANSACTIONS_SQL = "SELECT trx.TRX_ID AS transaction_id, trx.TRX_STATE AS transaction_state, trx.TRX_STARTED AS started_at, TIMESTAMPDIFF(SECOND, trx.TRX_STARTED, NOW()) AS age_seconds, trx.TRX_WAIT_STARTED AS wait_started_at, trx.TRX_ROWS_LOCKED AS rows_locked, trx.TRX_ROWS_MODIFIED AS rows_modified, trx.TRX_TABLES_LOCKED AS tables_locked, trx.TRX_MYSQL_THREAD_ID AS process_id, thread.PROCESSLIST_USER AS user_name, thread.PROCESSLIST_HOST AS host_name, thread.PROCESSLIST_DB AS database_name, thread.PROCESSLIST_STATE AS process_state, COALESCE(trx.TRX_QUERY, thread.PROCESSLIST_INFO) AS transaction_sql FROM information_schema.INNODB_TRX AS trx LEFT JOIN performance_schema.threads AS thread ON thread.PROCESSLIST_ID = trx.TRX_MYSQL_THREAD_ID WHERE thread.PROCESSLIST_DB IS NOT NULL ORDER BY trx.TRX_STARTED ASC LIMIT 500";
    private const DIGESTS_SQL = "SELECT DIGEST AS digest, DIGEST_TEXT AS digest_text, COUNT_STAR AS executions, SUM_TIMER_WAIT AS total_wait_picoseconds, AVG_TIMER_WAIT AS average_wait_picoseconds, MAX_TIMER_WAIT AS maximum_wait_picoseconds, SUM_LOCK_TIME AS lock_time_picoseconds, SUM_ROWS_EXAMINED AS rows_examined, SUM_ROWS_SENT AS rows_sent, SUM_ROWS_AFFECTED AS rows_affected, SUM_ERRORS AS errors, SUM_WARNINGS AS warnings, SUM_CREATED_TMP_TABLES AS temporary_tables, SUM_CREATED_TMP_DISK_TABLES AS temporary_disk_tables, SUM_NO_INDEX_USED AS no_index_used, SUM_NO_GOOD_INDEX_USED AS no_good_index_used, FIRST_SEEN AS first_seen, LAST_SEEN AS last_seen FROM performance_schema.events_statements_summary_by_digest WHERE SCHEMA_NAME = ? AND DIGEST IS NOT NULL ORDER BY SUM_TIMER_WAIT DESC, DIGEST ASC LIMIT 101";
    private const SERVER_DIGESTS_SQL = "WITH ranked_digests AS (SELECT SCHEMA_NAME AS database_name, DIGEST AS digest, DIGEST_TEXT AS digest_text, COUNT_STAR AS executions, SUM_TIMER_WAIT AS total_wait_picoseconds, AVG_TIMER_WAIT AS average_wait_picoseconds, MAX_TIMER_WAIT AS maximum_wait_picoseconds, SUM_LOCK_TIME AS lock_time_picoseconds, SUM_ROWS_EXAMINED AS rows_examined, SUM_ROWS_SENT AS rows_sent, SUM_ROWS_AFFECTED AS rows_affected, SUM_ERRORS AS errors, SUM_WARNINGS AS warnings, SUM_CREATED_TMP_TABLES AS temporary_tables, SUM_CREATED_TMP_DISK_TABLES AS temporary_disk_tables, SUM_NO_INDEX_USED AS no_index_used, SUM_NO_GOOD_INDEX_USED AS no_good_index_used, FIRST_SEEN AS first_seen, LAST_SEEN AS last_seen, COUNT(*) OVER() AS total_digest_rows, ROW_NUMBER() OVER (PARTITION BY SCHEMA_NAME ORDER BY SUM_TIMER_WAIT DESC, DIGEST ASC) AS schema_rank, ROW_NUMBER() OVER (ORDER BY SUM_TIMER_WAIT DESC, DIGEST ASC) AS total_time_rank, ROW_NUMBER() OVER (ORDER BY COUNT_STAR DESC, DIGEST ASC) AS execution_rank, ROW_NUMBER() OVER (ORDER BY SUM_ROWS_EXAMINED DESC, DIGEST ASC) AS rows_rank, ROW_NUMBER() OVER (ORDER BY SUM_LOCK_TIME DESC, DIGEST ASC) AS lock_rank, ROW_NUMBER() OVER (ORDER BY (SUM_ERRORS + SUM_CREATED_TMP_DISK_TABLES + SUM_NO_INDEX_USED + SUM_NO_GOOD_INDEX_USED) DESC, DIGEST ASC) AS issue_rank FROM performance_schema.events_statements_summary_by_digest WHERE SCHEMA_NAME IS NOT NULL AND DIGEST IS NOT NULL) SELECT database_name, digest, digest_text, executions, total_wait_picoseconds, average_wait_picoseconds, maximum_wait_picoseconds, lock_time_picoseconds, rows_examined, rows_sent, rows_affected, errors, warnings, temporary_tables, temporary_disk_tables, no_index_used, no_good_index_used, first_seen, last_seen, total_digest_rows FROM ranked_digests WHERE schema_rank <= 20 OR total_time_rank <= 200 OR execution_rank <= 200 OR rows_rank <= 200 OR lock_rank <= 200 OR issue_rank <= 200 ORDER BY schema_rank ASC, total_time_rank ASC, digest ASC LIMIT 2001";
    private const ERRORS_SQL = "SELECT ERROR_NUMBER AS error_number, ERROR_NAME AS error_name, SQL_STATE AS sql_state, SUM_ERROR_RAISED AS raised, SUM_ERROR_HANDLED AS handled, FIRST_SEEN AS first_seen, LAST_SEEN AS last_seen FROM performance_schema.events_errors_summary_global_by_error WHERE ERROR_NUMBER IN (1040, 1045, 1158, 1159, 1160, 1161, 1205, 1213, 2006, 2013, 3024, 4031) AND SUM_ERROR_RAISED > 0 ORDER BY SUM_ERROR_RAISED DESC";
    private const STATUS_SQL = "SHOW GLOBAL STATUS WHERE Variable_name IN ('Aborted_clients','Aborted_connects','Connection_errors_internal','Connection_errors_max_connections','Connection_errors_peer_address','Connections','Created_tmp_disk_tables','Created_tmp_tables','Performance_schema_digest_lost','Questions','Slow_queries','Threads_connected','Threads_running','Uptime')";

    private $source;

    public function __construct(RecorderQuerySource $source)
    {
        $this->source = $source;
    }

    public function collect(string $database, int $longQuerySeconds = 15, int $longTransactionSeconds = 15): array
    {
        $fast = $this->collectFast($database, $longQuerySeconds, $longTransactionSeconds);
        $digests = $this->collectDigests($database);
        $status = $this->collectStatus();

        $failures = array_merge($fast['collection']['failures'], $digests['collection']['failures'], $status['collection']['failures']);
        return array(
            'schema_version' => 1,
            'collected_at' => $fast['collected_at'],
            'database' => $database,
            'thresholds' => $fast['thresholds'],
            'locks' => $fast['locks'],
            'long_queries' => $fast['long_queries'],
            'long_transactions' => $fast['long_transactions'],
            'digests' => $digests['digests'],
            'status' => $status['status'],
            'errors' => $status['errors'],
            'collection' => array(
                'duration_ms' => round($fast['collection']['duration_ms'] + $digests['collection']['duration_ms'] + $status['collection']['duration_ms'], 2),
                'success' => $failures === array(),
                'partial' => $failures !== array(),
                'failures' => $failures,
                'counts' => array(
                    'locks' => count($fast['locks']),
                    'long_queries' => count($fast['long_queries']),
                    'long_transactions' => count($fast['long_transactions']),
                    'digests' => count($digests['digests']),
                    'errors' => count($status['errors']),
                ),
            ),
        );
    }

    public function collectFast(string $database, int $longQuerySeconds = 15, int $longTransactionSeconds = 15): array
    {
        $this->validateDatabase($database);
        $longQuerySeconds = max(1, min(3600, $longQuerySeconds));
        $longTransactionSeconds = max(1, min(86400, $longTransactionSeconds));
        $started = microtime(true); $failures = array();

        $ownIdRows = $this->optional('connection', 'SELECT CONNECTION_ID() AS connection_id', array(), $failures);
        $ownId = isset($ownIdRows[0]['connection_id']) ? (int) $ownIdRows[0]['connection_id'] : 0;
        $processes = $this->optional('processlist', 'SHOW FULL PROCESSLIST', array(), $failures);
        $longQueryCoverageLimited = false;
        $longQueries = $this->longQueries($processes, $database, $ownId, $longQuerySeconds, $longQueryCoverageLimited);
        $lockRows = $this->optional('locks', self::LOCKS_SQL, array(), $failures);
        $transactionRows = $this->optional('transactions', self::TRANSACTIONS_SQL, array($database), $failures);
        $locks = $this->locks($lockRows, $database);
        $transactions = $this->transactions($transactionRows, $longTransactionSeconds);
        return array(
            'schema_version' => 2,
            'sample_type' => 'events',
            'collected_at' => date('c'),
            'database' => $database,
            'thresholds' => array('long_query_seconds' => $longQuerySeconds, 'long_transaction_seconds' => $longTransactionSeconds),
            'locks' => $locks,
            'long_queries' => $longQueries,
            'long_transactions' => $transactions,
            'collection' => array(
                'duration_ms' => round((microtime(true) - $started) * 1000, 2),
                'success' => $failures === array(),
                'partial' => $failures !== array(),
                'failures' => $failures,
                'counts' => array('locks' => count($locks), 'long_queries' => count($longQueries), 'long_transactions' => count($transactions)),
                'coverage_limited' => array(
                    'locks' => count($lockRows) >= self::LOCK_RESULT_LIMIT,
                    'long_queries' => $longQueryCoverageLimited,
                    'long_transactions' => count($transactionRows) >= self::LOCK_RESULT_LIMIT,
                ),
            ),
        );
    }

    public function collectDigests(string $database): array
    {
        $this->validateDatabase($database);
        $started = microtime(true); $failures = array();
        $rows=$this->optional('digests', self::DIGESTS_SQL, array($database), $failures);$coverageLimited=count($rows)>100;if($coverageLimited){$rows=array_slice($rows,0,100);}$digests = $this->digests($rows);
        return array(
            'schema_version' => 2, 'sample_type' => 'digests', 'collected_at' => date('c'), 'database' => $database,
            'digests' => $digests,
            'collection' => array('duration_ms' => round((microtime(true) - $started) * 1000, 2), 'success' => $failures === array(), 'partial' => $failures !== array(), 'failures' => $failures, 'coverage_limited'=>$coverageLimited, 'counts' => array('digests' => count($digests))),
        );
    }

    public function collectStatus(): array
    {
        $started = microtime(true); $failures = array();
        $sourceStarted=microtime(true); $status = $this->status($this->optional('status', self::STATUS_SQL, array(), $failures)); $statusDuration=round((microtime(true)-$sourceStarted)*1000,2);
        $sourceStarted=microtime(true); $errors = $this->errors($this->optional('errors', self::ERRORS_SQL, array(), $failures)); $errorsDuration=round((microtime(true)-$sourceStarted)*1000,2);
        return array(
            'schema_version' => 2, 'sample_type' => 'status', 'collected_at' => date('c'),
            'status' => $status, 'errors' => $errors,
            'collection' => array('duration_ms' => round((microtime(true) - $started) * 1000, 2), 'source_duration_ms'=>array('status'=>$statusDuration,'errors'=>$errorsDuration), 'success' => $failures === array(), 'partial' => $failures !== array(), 'failures' => $failures, 'counts' => array('status' => count($status), 'errors' => count($errors))),
        );
    }

    /** Uma única leitura global, separada em memória pelos schemas autorizados. */
    public function collectFastServer(array $databases, int $longQuerySeconds = 15, int $longTransactionSeconds = 15): array
    {
        $databases = $this->validateDatabases($databases);
        $longQuerySeconds = max(1, min(3600, $longQuerySeconds)); $longTransactionSeconds = max(1, min(86400, $longTransactionSeconds));
        $started = microtime(true); $failures = array();
        $durations=array(); $sourceStarted=microtime(true); $ownIdRows = $this->optional('connection', 'SELECT CONNECTION_ID() AS connection_id', array(), $failures); $durations['connection']=round((microtime(true)-$sourceStarted)*1000,2);
        $ownId = isset($ownIdRows[0]['connection_id']) ? (int) $ownIdRows[0]['connection_id'] : 0;
        $sourceStarted=microtime(true); $processes = $this->optional('processlist', 'SHOW FULL PROCESSLIST', array(), $failures); $durations['processlist']=round((microtime(true)-$sourceStarted)*1000,2);
        $sourceStarted=microtime(true); $locks = $this->optional('locks', self::LOCKS_SQL, array(), $failures); $durations['locks']=round((microtime(true)-$sourceStarted)*1000,2);
        $sourceStarted=microtime(true); $transactions = $this->optional('transactions', self::SERVER_TRANSACTIONS_SQL, array(), $failures); $durations['transactions']=round((microtime(true)-$sourceStarted)*1000,2);
        $records = array(); $collectedAt = date('c');
        foreach ($databases as $database) {
            $longQueryCoverageLimited = false;
            $queriesForSchema = $this->longQueries($processes, $database, $ownId, $longQuerySeconds, $longQueryCoverageLimited);
            $lockRows = array_values(array_filter($locks, function (array $row) use ($database): bool { $owner=(string)($row['object_schema']??'');if($owner===''){$owner=(string)($row['waiting_database']??'');}if($owner===''){$owner=(string)($row['blocking_database']??'');}return $owner===$database; }));
            $locksForSchema = $this->locks($lockRows, $database);
            $transactionRows = array_values(array_filter($transactions, function (array $row) use ($database): bool { return (string) ($row['database_name'] ?? '') === $database; }));
            $transactionsForSchema = $this->transactions($transactionRows, $longTransactionSeconds);
            $records[$database] = array('schema_version'=>3, 'sample_type'=>'events', 'scope'=>'schema', 'collected_at'=>$collectedAt, 'database'=>$database, 'thresholds'=>array('long_query_seconds'=>$longQuerySeconds,'long_transaction_seconds'=>$longTransactionSeconds), 'locks'=>$locksForSchema, 'long_queries'=>$queriesForSchema, 'long_transactions'=>$transactionsForSchema, 'collection'=>array('duration_ms'=>0.0, 'success'=>$failures===array(), 'partial'=>$failures!==array(), 'failures'=>$failures, 'counts'=>array('locks'=>count($locksForSchema),'long_queries'=>count($queriesForSchema),'long_transactions'=>count($transactionsForSchema)), 'coverage_limited'=>array('locks'=>count($locks)>=self::LOCK_RESULT_LIMIT,'long_queries'=>$longQueryCoverageLimited,'long_transactions'=>count($transactions)>=self::TRANSACTION_RESULT_LIMIT)));
        }
        $duration = round((microtime(true) - $started) * 1000, 2);
        foreach ($records as &$record) { $record['collection']['duration_ms'] = $duration; } unset($record);
        return array('schema_version'=>3, 'sample_type'=>'events_server_batch', 'collected_at'=>$collectedAt, 'schemas'=>$databases, 'records'=>$records, 'collection'=>array('duration_ms'=>$duration,'source_duration_ms'=>$durations,'success'=>$failures===array(),'partial'=>$failures!==array(),'failures'=>$failures,'source_queries'=>4));
    }

    /** Uma consulta limitada de digests para todo o servidor, depois particionada por schema. */
    public function collectDigestsServer(array $databases): array
    {
        $databases = $this->validateDatabases($databases); $allowed = array_fill_keys($databases, true);
        $started = microtime(true); $failures = array(); $rows = $this->optional('digests', self::SERVER_DIGESTS_SQL, array(), $failures); $selectedDigestRows=count($rows);$totalDigestRows=0;foreach($rows as $row){$totalDigestRows=max($totalDigestRows,(int)($row['total_digest_rows']??0));}$resultLimitReached=$selectedDigestRows>self::SERVER_DIGEST_RESULT_LIMIT;if($resultLimitReached){$rows=array_slice($rows,0,self::SERVER_DIGEST_RESULT_LIMIT);}$coverageLimited=$resultLimitReached||$totalDigestRows>count($rows); $grouped = array();
        foreach ($databases as $database) { $grouped[$database] = array(); }
        foreach ($rows as $row) { $database = (string) ($row['database_name'] ?? ''); if (isset($allowed[$database])) { $grouped[$database][] = $row; } }
        $records = array(); $collectedAt = date('c'); $duration = round((microtime(true) - $started) * 1000, 2);
        foreach ($grouped as $database => $schemaRows) { $digests = $this->digests($schemaRows); $records[$database] = array('schema_version'=>3,'sample_type'=>'digests','scope'=>'schema','collected_at'=>$collectedAt,'database'=>$database,'digests'=>$digests,'collection'=>array('duration_ms'=>$duration,'success'=>$failures===array(),'partial'=>$failures!==array(),'failures'=>$failures,'counts'=>array('digests'=>count($digests)))); }
        return array('schema_version'=>3,'sample_type'=>'digests_server_batch','collected_at'=>$collectedAt,'schemas'=>$databases,'records'=>$records,'collection'=>array('duration_ms'=>$duration,'source_duration_ms'=>array('digests'=>$duration),'success'=>$failures===array(),'partial'=>$failures!==array(),'failures'=>$failures,'source_queries'=>1,'coverage_limited'=>$coverageLimited,'result_limit_reached'=>$resultLimitReached,'total_digest_rows'=>$totalDigestRows,'selected_digest_rows'=>min($selectedDigestRows,self::SERVER_DIGEST_RESULT_LIMIT),'selection'=>array('per_schema'=>20,'total_time'=>200,'executions'=>200,'rows_examined'=>200,'lock_time'=>200,'issues'=>200,'result_limit'=>self::SERVER_DIGEST_RESULT_LIMIT)));
    }

    private function validateDatabase(string $database): void
    {
        if (preg_match('/^[A-Za-z0-9_]+$/', $database) !== 1) { throw new \InvalidArgumentException('invalid_database_name'); }
    }

    private function validateDatabases(array $databases): array
    {
        $valid = array();
        foreach ($databases as $database) { if (!is_string($database)) { continue; } $this->validateDatabase($database); $valid[$database] = true; }
        if ($valid === array() || count($valid) > 100) { throw new \InvalidArgumentException('invalid_server_database_scope'); }
        $result = array_keys($valid); sort($result); return $result;
    }

    private function optional(string $section, string $sql, array $parameters, array &$failures): array
    {
        try { return $this->source->rows($sql, $parameters); }
        catch (\Throwable $exception) {
            $failures[] = array('section' => $section, 'message' => $this->safeError($exception->getMessage()));
            return array();
        }
    }

    private function longQueries(array $rows, string $database, int $ownId, int $minimumSeconds, ?bool &$coverageLimited = null): array
    {
        $queries = array();
        foreach ($rows as $row) {
            $id = (int) ($row['Id'] ?? 0); $seconds = (int) ($row['Time'] ?? 0);
            $state = (string) ($row['State'] ?? ''); $isLock = stripos($state, 'lock') !== false;
            if ($id === $ownId || (string) ($row['db'] ?? '') !== $database || (string) ($row['Command'] ?? '') !== 'Query' || ($seconds < $minimumSeconds && !$isLock)) { continue; }
            $sql = $this->normalizeSql((string) ($row['Info'] ?? ''));
            $queries[] = array(
                'process_id' => $id, 'seconds' => $seconds, 'state' => $state, 'waiting_for_lock' => $isLock,
                'user' => (string) ($row['User'] ?? ''), 'host' => (string) ($row['Host'] ?? ''),
                'query' => $sql, 'fingerprint' => hash('sha256', $sql), 'tables' => $this->tables($sql),
            );
        }
        usort($queries, function (array $left, array $right): int { return $right['seconds'] <=> $left['seconds']; });
        $coverageLimited = count($queries) > self::LONG_QUERY_RESULT_LIMIT;
        return array_slice($this->withMyisamCandidates($queries), 0, self::LONG_QUERY_RESULT_LIMIT);
    }

    private function locks(array $rows, string $database): array
    {
        $locks = array();
        foreach ($rows as $row) {
            if ((string) ($row['object_schema'] ?? '') !== $database && (string) ($row['waiting_database'] ?? '') !== $database && (string) ($row['blocking_database'] ?? '') !== $database) { continue; }
            $waitingSql = $this->normalizeSql((string) ($row['waiting_sql'] ?? ''));
            $blockingSql = $this->normalizeSql((string) ($row['blocking_sql'] ?? ''));
            $locks[] = array(
                'type' => 'InnoDB', 'confirmed' => true,
                'waiting_process_id' => (int) ($row['waiting_process_id'] ?? 0), 'blocking_process_id' => (int) ($row['blocking_process_id'] ?? 0),
                'object' => trim((string) ($row['object_schema'] ?? '') . '.' . (string) ($row['object_name'] ?? ''), '.'),
                'index' => (string) ($row['index_name'] ?? ''), 'lock_type' => (string) ($row['lock_type'] ?? ''),
                'requested_mode' => (string) ($row['requested_lock_mode'] ?? ''), 'blocking_mode' => (string) ($row['blocking_lock_mode'] ?? ''),
                'waiting_seconds' => (int) ($row['waiting_seconds'] ?? 0), 'waiting_state' => (string) ($row['waiting_state'] ?? ''),
                'waiting_user' => (string) ($row['waiting_user'] ?? ''), 'waiting_host' => (string) ($row['waiting_host'] ?? ''),
                'waiting_query' => $waitingSql, 'waiting_fingerprint' => hash('sha256', $waitingSql),
                'blocking_seconds' => (int) ($row['blocking_seconds'] ?? 0), 'blocking_state' => (string) ($row['blocking_state'] ?? ''),
                'blocking_user' => (string) ($row['blocking_user'] ?? ''), 'blocking_host' => (string) ($row['blocking_host'] ?? ''),
                'blocking_query' => $blockingSql, 'blocking_fingerprint' => hash('sha256', $blockingSql),
            );
        }
        return $locks;
    }

    private function transactions(array $rows, int $minimumSeconds): array
    {
        $transactions = array();
        foreach ($rows as $row) {
            $age = (int) ($row['age_seconds'] ?? 0);
            if ($age < $minimumSeconds && (string) ($row['transaction_state'] ?? '') !== 'LOCK WAIT') { continue; }
            $sql = $this->normalizeSql((string) ($row['transaction_sql'] ?? ''));
            $transactions[] = array(
                'transaction_id' => (string) ($row['transaction_id'] ?? ''), 'process_id' => (int) ($row['process_id'] ?? 0),
                'state' => (string) ($row['transaction_state'] ?? ''), 'started_at' => (string) ($row['started_at'] ?? ''), 'age_seconds' => $age,
                'wait_started_at' => (string) ($row['wait_started_at'] ?? ''), 'rows_locked' => (int) ($row['rows_locked'] ?? 0),
                'rows_modified' => (int) ($row['rows_modified'] ?? 0), 'tables_locked' => (int) ($row['tables_locked'] ?? 0),
                'user' => (string) ($row['user_name'] ?? ''), 'host' => (string) ($row['host_name'] ?? ''), 'process_state' => (string) ($row['process_state'] ?? ''),
                'query' => $sql, 'fingerprint' => hash('sha256', $sql),
            );
        }
        return $transactions;
    }

    private function digests(array $rows): array
    {
        $digests = array();
        foreach ($rows as $row) {
            $digests[] = array(
                'digest' => (string) ($row['digest'] ?? ''), 'query' => $this->limit((string) ($row['digest_text'] ?? ''), 2000),
                'executions' => (int) ($row['executions'] ?? 0),
                'total_time_ms' => $this->picosecondsToMilliseconds($row['total_wait_picoseconds'] ?? 0),
                'average_time_ms' => $this->picosecondsToMilliseconds($row['average_wait_picoseconds'] ?? 0),
                'maximum_time_ms' => $this->picosecondsToMilliseconds($row['maximum_wait_picoseconds'] ?? 0),
                'lock_time_ms' => $this->picosecondsToMilliseconds($row['lock_time_picoseconds'] ?? 0),
                'rows_examined' => (int) ($row['rows_examined'] ?? 0), 'rows_sent' => (int) ($row['rows_sent'] ?? 0), 'rows_affected' => (int) ($row['rows_affected'] ?? 0),
                'errors' => (int) ($row['errors'] ?? 0), 'warnings' => (int) ($row['warnings'] ?? 0),
                'temporary_tables' => (int) ($row['temporary_tables'] ?? 0), 'temporary_disk_tables' => (int) ($row['temporary_disk_tables'] ?? 0),
                'no_index_used' => (int) ($row['no_index_used'] ?? 0), 'no_good_index_used' => (int) ($row['no_good_index_used'] ?? 0),
                'first_seen' => (string) ($row['first_seen'] ?? ''), 'last_seen' => (string) ($row['last_seen'] ?? ''),
            );
        }
        return $digests;
    }

    private function status(array $rows): array
    {
        $status = array();
        foreach ($rows as $row) {
            if (!isset($row['Variable_name'])) { continue; }
            $status[(string) $row['Variable_name']] = is_numeric($row['Value'] ?? null) ? (int) $row['Value'] : (string) ($row['Value'] ?? '');
        }
        return $status;
    }

    private function errors(array $rows): array
    {
        return array_map(function (array $row): array {
            return array('number' => (int) ($row['error_number'] ?? 0), 'name' => (string) ($row['error_name'] ?? ''), 'sql_state' => (string) ($row['sql_state'] ?? ''), 'raised' => (int) ($row['raised'] ?? 0), 'handled' => (int) ($row['handled'] ?? 0), 'first_seen' => (string) ($row['first_seen'] ?? ''), 'last_seen' => (string) ($row['last_seen'] ?? ''));
        }, $rows);
    }

    private function withMyisamCandidates(array $queries): array
    {
        foreach ($queries as $index => $waiting) {
            if (!$waiting['waiting_for_lock'] || stripos($waiting['state'], 'table level lock') === false || $waiting['tables'] === array()) { continue; }
            $candidates = array();
            foreach ($queries as $candidate) {
                if ($candidate['process_id'] === $waiting['process_id'] || $candidate['waiting_for_lock'] || $candidate['seconds'] < $waiting['seconds']) { continue; }
                $shared = array_values(array_intersect($waiting['tables'], $candidate['tables']));
                if ($shared !== array()) { $candidates[] = array('process_id' => $candidate['process_id'], 'tables' => $shared, 'reason' => 'Consulta ativa há mais tempo na mesma tabela; candidato não confirmado'); }
            }
            $queries[$index]['possible_blockers'] = array_slice($candidates, 0, 3);
        }
        return $queries;
    }

    private function normalizeSql(string $sql): string
    {
        $sql = preg_replace("/'(?:''|\\\\.|[^'])*'/s", '?', $sql);
        $sql = preg_replace('/"(?:""|\\\\.|[^"])*"/s', '?', (string) $sql);
        $sql = preg_replace('/\b0x[0-9a-f]+\b/i', '?', (string) $sql);
        $sql = preg_replace('/\b\d+(?:\.\d+)?\b/', '?', (string) $sql);
        $sql = trim((string) preg_replace('/\s+/', ' ', (string) $sql));
        return $this->limit($sql, 2000);
    }

    private function tables(string $sql): array
    {
        if (preg_match_all('/\b(?:FROM|JOIN|UPDATE|INTO|TABLE)\s+([`a-zA-Z0-9_.$]+)/i', $sql, $matches) < 1) { return array(); }
        $tables = array();
        foreach ($matches[1] as $table) {
            $table = trim(str_replace('`', '', (string) $table));
            if ($table !== '' && !in_array($table, $tables, true)) { $tables[] = $table; }
        }
        return $tables;
    }

    private function picosecondsToMilliseconds($value): float { return round((float) $value / 1000000000, 3); }
    private function limit(string $value, int $length): string { return mb_strlen($value) > $length ? mb_substr($value, 0, $length - 1) . '…' : $value; }
    private function safeError(string $message): string { return $this->limit((string) preg_replace('/(?:password|passwd|pwd)\s*[=:]\s*\S+/i', 'credential=[hidden]', $message), 300); }
}
