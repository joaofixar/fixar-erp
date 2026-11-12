#!/usr/bin/env php
<?php
declare(strict_types=1);

use Hardness\Observability\Collectors\Mysql\MysqlRecorderRunLock;
use Hardness\Observability\Collectors\Mysql\MysqlRecorderSchemaProvider;
use Hardness\Observability\Collectors\Mysql\MysqlRecorderStorage;
use Hardness\Observability\Config\ShowprocessConnectionProvider;

require dirname(__DIR__) . '/bootstrap/bootstrap.php';

if (PHP_SAPI !== 'cli') { fwrite(STDERR, "Disponível somente no terminal.\n"); exit(2); }

$root = dirname(HARDNESS_OBSERVABILITY_ROOT);
$profiles = $root . '/configuracoes/local/ambientes.php';
$storagePath = $root . '/dados_usuarios/observability/mysql';
$profileStat = @lstat($profiles);
if ($profileStat === false || is_link($profiles) || !is_file($profiles) || realpath($profiles) !== $profiles) {
    fwrite(STDERR, "Demonstração recusada: inventário local indisponível.\n"); exit(3);
}
if (file_exists($storagePath) || is_link($storagePath)) {
    fwrite(STDERR, "Demonstração recusada: o armazenamento MySQL já existe.\n"); exit(4);
}

$tenant = (new ShowprocessConnectionProvider())->tenant();
$host = strtolower((string) ($tenant['database']['host'] ?? ''));
if (!in_array($host, array('127.0.0.1', 'localhost', '::1'), true)) {
    fwrite(STDERR, "Demonstração recusada: conexão central não local.\n"); exit(3);
}

$schemas = (new MysqlRecorderSchemaProvider())->forServer($tenant);
$required = array('e998', 'e229', 'e043');
foreach ($required as $schema) {
    if (!in_array($schema, $schemas, true)) { fwrite(STDERR, "Demonstração recusada: perfil local necessário ausente.\n"); exit(3); }
}

$storage = new MysqlRecorderStorage();
$lock = new MysqlRecorderRunLock();
if (!$lock->acquire($storage->lockPath('server'))) { fwrite(STDERR, "Outra coleta está ativa.\n"); exit(5); }

$now = time();
$at = static function (int $offset) use ($now): string { return date('c', $now + $offset); };
$collection = static function (array $counts, bool $partial = false, array $failures = array()): array {
    return array('duration_ms'=>4.2, 'success'=>!$partial, 'partial'=>$partial, 'failures'=>$failures, 'counts'=>$counts);
};

try {
    $events = array(
        array(
            'schema_version'=>3, 'sample_type'=>'events', 'scope'=>'schema', 'synthetic'=>true, 'collected_at'=>$at(-150), 'database'=>'e998',
            'thresholds'=>array('long_query_seconds'=>15, 'long_transaction_seconds'=>15),
            'locks'=>array(),
            'long_queries'=>array(array('process_id'=>4101, 'seconds'=>48, 'state'=>'Sending data', 'waiting_for_lock'=>false, 'user'=>'demo_app', 'host'=>'127.0.0.1', 'query'=>'SELECT * FROM PED001 WHERE PED001_Status = ? ORDER BY PED001_Data DESC', 'fingerprint'=>hash('sha256', 'demo-query-e998'), 'tables'=>array('PED001'))),
            'long_transactions'=>array(),
            'collection'=>$collection(array('locks'=>0, 'long_queries'=>1, 'long_transactions'=>0)),
        ),
        array(
            'schema_version'=>3, 'sample_type'=>'events', 'scope'=>'schema', 'synthetic'=>true, 'collected_at'=>$at(-90), 'database'=>'e229',
            'thresholds'=>array('long_query_seconds'=>15, 'long_transaction_seconds'=>15),
            'locks'=>array(array('type'=>'InnoDB', 'confirmed'=>true, 'waiting_process_id'=>4202, 'blocking_process_id'=>4201, 'object'=>'e229.T004', 'index'=>'PRIMARY', 'lock_type'=>'RECORD', 'requested_mode'=>'X', 'blocking_mode'=>'X', 'waiting_seconds'=>31, 'waiting_state'=>'Waiting for row lock', 'waiting_user'=>'demo_app', 'waiting_host'=>'127.0.0.1', 'waiting_query'=>'UPDATE T004 SET T004_Quantidade = ? WHERE T004_Id = ?', 'waiting_fingerprint'=>hash('sha256', 'demo-wait-e229'), 'blocking_seconds'=>74, 'blocking_state'=>'executing', 'blocking_user'=>'demo_worker', 'blocking_host'=>'127.0.0.1', 'blocking_query'=>'UPDATE T004 SET T004_Reserva = ? WHERE T004_Id = ?', 'blocking_fingerprint'=>hash('sha256', 'demo-block-e229'))),
            'long_queries'=>array(), 'long_transactions'=>array(),
            'collection'=>$collection(array('locks'=>1, 'long_queries'=>0, 'long_transactions'=>0)),
        ),
        array(
            'schema_version'=>3, 'sample_type'=>'events', 'scope'=>'schema', 'synthetic'=>true, 'collected_at'=>$at(-45), 'database'=>'e043',
            'thresholds'=>array('long_query_seconds'=>15, 'long_transaction_seconds'=>15), 'locks'=>array(), 'long_queries'=>array(),
            'long_transactions'=>array(array('transaction_id'=>'demo-trx-4301', 'process_id'=>4301, 'state'=>'RUNNING', 'started_at'=>date('Y-m-d H:i:s', $now - 185), 'age_seconds'=>185, 'wait_started_at'=>'', 'rows_locked'=>12, 'rows_modified'=>4, 'tables_locked'=>2, 'user'=>'demo_batch', 'host'=>'127.0.0.1', 'process_state'=>'updating', 'query'=>'UPDATE EST001 SET EST001_Saldo = ? WHERE EST001_Id = ?', 'fingerprint'=>hash('sha256', 'demo-trx-e043'))),
            'collection'=>$collection(array('locks'=>0, 'long_queries'=>0, 'long_transactions'=>1)),
        ),
    );
    foreach ($events as $record) { $storage->append('events', $record); }

    $statusBaseline = array(
        'schema_version'=>3, 'sample_type'=>'status', 'scope'=>'server', 'synthetic'=>true, 'collected_at'=>$at(-300), 'database'=>'server',
        'collection'=>$collection(array('status'=>13, 'errors'=>1)),
        'counters'=>array('mysql_restart_detected'=>false, 'baseline'=>true, 'baseline_reason'=>'no_previous_sample', 'absolute'=>array('Threads_connected'=>18, 'Threads_running'=>3), 'delta'=>null, 'errors'=>array(array('number'=>1205, 'name'=>'ER_LOCK_WAIT_TIMEOUT', 'sql_state'=>'HY000', 'baseline'=>true, 'baseline_reason'=>'new_error_counter', 'absolute'=>array('raised'=>4, 'handled'=>0), 'delta'=>null))),
    );
    $statusDelta = array(
        'schema_version'=>3, 'sample_type'=>'status', 'scope'=>'server', 'synthetic'=>true, 'collected_at'=>$at(-30), 'database'=>'server',
        'collection'=>$collection(array('status'=>13, 'errors'=>1)),
        'counters'=>array('mysql_restart_detected'=>false, 'baseline'=>false, 'baseline_reason'=>null, 'absolute'=>array('Threads_connected'=>23, 'Threads_running'=>7), 'delta'=>array('Connections'=>42, 'Questions'=>1860, 'Slow_queries'=>3, 'Created_tmp_tables'=>34, 'Created_tmp_disk_tables'=>6), 'errors'=>array(array('number'=>1205, 'name'=>'ER_LOCK_WAIT_TIMEOUT', 'sql_state'=>'HY000', 'baseline'=>false, 'baseline_reason'=>null, 'absolute'=>array('raised'=>6, 'handled'=>0), 'delta'=>array('raised'=>2, 'handled'=>0)))),
    );
    $health = array('schema_version'=>3, 'sample_type'=>'collector_health', 'scope'=>'server', 'synthetic'=>true, 'collected_at'=>$at(-15), 'database'=>'server', 'healthy'=>false, 'conditions'=>array('partial_failure'), 'categories'=>array(), 'performance_budget_overruns'=>0, 'collection'=>$collection(array(), true, array(array('section'=>'locks', 'message'=>'synthetic_source_unavailable'))), 'failures'=>array(array('section'=>'locks', 'message'=>'synthetic_source_unavailable')));
    $storage->append('status', $statusBaseline); $storage->append('status', $statusDelta); $storage->append('status', $health);

    $digestDefinitions = array(
        'e998'=>array('digest'=>'demo-digest-e998', 'query'=>'SELECT * FROM PED001 WHERE PED001_Status = ?', 'executions'=>array(120, 148), 'time'=>array(840.0, 1036.0)),
        'e229'=>array('digest'=>'demo-digest-e229', 'query'=>'UPDATE T004 SET T004_Quantidade = ? WHERE T004_Id = ?', 'executions'=>array(35, 43), 'time'=>array(510.0, 690.0)),
        'e043'=>array('digest'=>'demo-digest-e043', 'query'=>'SELECT * FROM EST001 WHERE EST001_Produto = ?', 'executions'=>array(82, 101), 'time'=>array(320.0, 401.0)),
    );
    foreach ($digestDefinitions as $schema => $definition) {
        $baseItem = array('digest'=>$definition['digest'], 'query'=>$definition['query'], 'first_seen'=>$at(-3600), 'last_seen'=>$at(-300), 'average_time_ms'=>7.0, 'maximum_time_ms'=>42.0, 'baseline'=>true, 'baseline_reason'=>'new_digest', 'delta'=>null);
        $deltaItem = array('digest'=>$definition['digest'], 'query'=>$definition['query'], 'first_seen'=>$at(-3600), 'last_seen'=>$at(-30), 'average_time_ms'=>7.4, 'maximum_time_ms'=>44.0, 'baseline'=>false, 'baseline_reason'=>null, 'delta'=>array('executions'=>$definition['executions'][1]-$definition['executions'][0], 'total_time_ms'=>$definition['time'][1]-$definition['time'][0], 'lock_time_ms'=>12.0, 'rows_examined'=>56000.0, 'rows_sent'=>28.0, 'rows_affected'=>$schema==='e229'?8.0:0.0, 'errors'=>0.0, 'warnings'=>1.0, 'temporary_tables'=>3.0, 'temporary_disk_tables'=>1.0, 'no_index_used'=>2.0, 'no_good_index_used'=>0.0));
        foreach (array(array($at(-300), $baseItem), array($at(-30), $deltaItem)) as $sample) {
            $storage->append('digests', array('schema_version'=>3, 'sample_type'=>'digests', 'scope'=>'schema', 'synthetic'=>true, 'collected_at'=>$sample[0], 'database'=>$schema, 'collection'=>$collection(array('digests'=>1, 'observed_digests'=>1)), 'counters'=>array('items'=>array($sample[1]), 'disappeared_digests'=>array(), 'coverage_limited'=>false)));
        }
    }

    $storage->saveState('server', array('schema_version'=>2, 'scope'=>'server', 'synthetic'=>true, 'databases'=>$schemas, 'updated_at'=>$at(0)));
    fwrite(STDOUT, json_encode(array('synthetic'=>true, 'schemas'=>$schemas, 'records'=>array('events'=>count($events), 'status'=>3, 'digests'=>count($digestDefinitions) * 2), 'state'=>'server'), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL);
} finally {
    $lock->release();
}
