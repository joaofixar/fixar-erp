<?php
declare(strict_types=1);

$root = dirname(__DIR__);
require $root . '/bootstrap/bootstrap.php';

use Hardness\Observability\Context\MockObservationContextProvider;
use Hardness\Observability\Http\PrototypeController;
use Hardness\Observability\Http\Request;
use Hardness\Observability\Http\Router;
use Hardness\Observability\Mock\MockObservationDataProvider;
use Hardness\Observability\Severity\Severity;
use Hardness\Observability\Severity\SeverityEvaluator;
use Hardness\Observability\Auth\AuthenticationService;
use Hardness\Observability\Auth\ClientAccessPolicyLoader;
use Hardness\Observability\Config\SiteTenantProvider;
use Hardness\Observability\Auth\MasterPasswordProvider;
use Hardness\Observability\Legacy\LegacyCredentialValidator;
use Hardness\Observability\Security\LoginRateLimiter;
use Hardness\Observability\Security\TrustedHostResolver;
use Hardness\Observability\Security\ObservationSession;
use Hardness\Observability\Collectors\Linux\LinuxServerCollector;
use Hardness\Observability\Collectors\Apache\ApachePhpCollector;
use Hardness\Observability\Diagnostics\DiagnosticEngine;
use Hardness\Observability\Support\IncidentSnapshotArchive;
use Hardness\Observability\Collectors\Mysql\MysqlRecorderCollector;
use Hardness\Observability\Collectors\Mysql\MysqlRecorderDeltaCalculator;
use Hardness\Observability\Collectors\Mysql\MysqlRecorderHistoryRepository;
use Hardness\Observability\Collectors\Mysql\MysqlRecorderStorage;
use Hardness\Observability\Collectors\Mysql\MysqlRecorderRunLock;
use Hardness\Observability\Collectors\Mysql\MysqlRecorderRunner;
use Hardness\Observability\Collectors\Mysql\RecorderQuerySource;

function observationAssert(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

function observationRouter(MockObservationContextProvider $contexts): Router
{
    return new Router(new PrototypeController($contexts, new MockObservationDataProvider($contexts)));
}

putenv('OBSERVABILITY_MOCK_CONTEXT');
putenv('OBSERVABILITY_MOCK_SCENARIO');
$client = new MockObservationContextProvider();
$clientContext = $client->current()->toArray();
observationAssert($clientContext['role'] === 'client', 'client must be the default mock context');
observationAssert($clientContext['capabilities']['full_sql'] === false, 'client mock must keep full SQL unavailable');

foreach (array('/', '/server', '/database', '/processes', '/mysql-history', '/diagnostics', '/network', '/captures', '/error-logs', '/observability/processes') as $route) {
    $response = observationRouter($client)->dispatch(Request::fromValues('GET', $route));
    observationAssert($response->status() === 200, 'valid route must return 200: ' . $route);
}
observationAssert(strpos(observationRouter($client)->dispatch(Request::fromValues('GET', '/'))->body(), 'assets/img/logo_hardness.png') !== false, 'sidebar must use the Hardness logo');
observationAssert(observationRouter($client)->dispatch(Request::fromValues('GET', '/api/v1/network-probe'))->status() === 204, 'network probe must be available through the application router');
$emptyHistoryResponse = observationRouter($client)->dispatch(Request::fromValues('GET', '/api/v1/mysql-history'));
observationAssert($emptyHistoryResponse->status() === 200 && json_decode($emptyHistoryResponse->body(), true)['contract_version'] === 'mysql-history.v1', 'MySQL history API must remain stable before data exists');
observationAssert(observationRouter($client)->dispatch(Request::fromValues('GET', '/unknown'))->status() === 404, 'unknown route must return 404');
observationAssert(observationRouter($client)->dispatch(Request::fromValues('POST', '/processes'))->status() === 405, 'invalid method must return 405');

$evaluator = new SeverityEvaluator();
observationAssert($evaluator->percentage(20, 70, 90) === Severity::NORMAL, 'normal threshold must be calculated centrally');
observationAssert($evaluator->percentage(70, 70, 90) === Severity::ATTENTION, 'attention threshold must be calculated centrally');
observationAssert($evaluator->percentage(90, 70, 90) === Severity::CRITICAL, 'critical threshold must be calculated centrally');
observationAssert($evaluator->aggregate(array(Severity::NORMAL, Severity::UNAVAILABLE)) === Severity::NORMAL, 'partial unavailability must not be critical');
observationAssert($evaluator->aggregate(array(Severity::UNAVAILABLE, Severity::UNAVAILABLE)) === Severity::UNAVAILABLE, 'all unavailable must aggregate as unavailable');
observationAssert($evaluator->aggregate(array(Severity::NORMAL, Severity::ATTENTION, Severity::CRITICAL)) === Severity::CRITICAL, 'critical must dominate aggregate');

$engine = new DiagnosticEngine();
$diagnostics = $engine->evaluate(
    array(
        array('label' => 'CPU', 'value' => '94%', 'severity' => Severity::CRITICAL),
        array('label' => 'Memória', 'value' => '82%', 'severity' => Severity::ATTENTION),
        array('label' => 'Swap', 'value' => '4%', 'severity' => Severity::NORMAL),
        array('label' => 'Disco', 'value' => '91%', 'severity' => Severity::CRITICAL),
        array('label' => 'Banco', 'value' => '91 conexões', 'severity' => Severity::ATTENTION)
    ),
    array(array('time' => 18, 'lock' => true, 'severity' => Severity::CRITICAL)),
    array('details' => array(array('label' => 'OPcache', 'value' => 'Indisponível', 'available' => false), array('label' => 'APCu', 'value' => 'Indisponível', 'available' => false))),
    array('severity' => Severity::ATTENTION, 'details' => array('connectionUsage' => '91%', 'slowQueries' => '2'))
);
observationAssert(count($diagnostics) >= 7, 'diagnostic engine must correlate available server, web and database data');
observationAssert($diagnostics[0]['severity'] === Severity::CRITICAL, 'diagnostics must prioritize critical conditions');
observationAssert(strpos($diagnostics[0]['message'], 'Possível causa:') !== false && strpos($diagnostics[0]['message'], 'Verificar:') !== false, 'diagnostics must provide cause and recommendation without changing the API contract');

putenv('OBSERVABILITY_MOCK_CONTEXT=hardness_internal');
$internal = new MockObservationContextProvider();
$internalContext = $internal->current()->toArray();
observationAssert($internalContext['role'] === 'hardness_internal', 'development environment may select only the internal mock context');
observationAssert(count($internalContext['tenantLabels']) === 3, 'internal mock must expose only fictitious tenant labels');
$internalSnapshot = (new MockObservationDataProvider($internal))->snapshot();
observationAssert(isset($internalSnapshot['database']['tenants']), 'internal mock must receive a fictitious tenant summary');
observationAssert($internalSnapshot['processes'][0]['lock_details'][0]['blocking_process_id'] === 402, 'lock fixtures must expose the blocking process relation');

putenv('OBSERVABILITY_MOCK_CONTEXT');
$clientSnapshot = (new MockObservationDataProvider($client))->snapshot();
observationAssert(!isset($clientSnapshot['database']['tenants']), 'client must not receive tenant inventory');

putenv('OBSERVABILITY_MOCK_SCENARIO=normal');
$normalSnapshot = (new MockObservationDataProvider($client))->snapshot();
observationAssert($normalSnapshot['overall']['severity'] === Severity::NORMAL, 'normal scenario must aggregate as normal');
putenv('OBSERVABILITY_MOCK_SCENARIO=cpu_critical');
$criticalSnapshot = (new MockObservationDataProvider($client))->snapshot();
observationAssert($criticalSnapshot['overall']['severity'] === Severity::CRITICAL, 'critical scenario must aggregate as critical');
putenv('OBSERVABILITY_MOCK_SCENARIO=database_unavailable');
$unavailableSnapshot = (new MockObservationDataProvider($client))->snapshot();
observationAssert($unavailableSnapshot['database']['severity'] === Severity::UNAVAILABLE, 'unavailable scenario must expose unavailable state');

$javascript = file_get_contents($root . '/public/assets/js/observability.js');
observationAssert(strpos($javascript, 'pushState') !== false && strpos($javascript, 'popstate') !== false, 'navigation must preserve browser URL/history');
observationAssert(strpos($javascript, 'AbortController') !== false && strpos($javascript, 'inFlight') !== false, 'polling must avoid concurrent work and cancel on navigation');
observationAssert(strpos($javascript, '/api/v1/network-probe') !== false && strpos($javascript, 'state.network.samples.length > 20') !== false && strpos($javascript, 'data-network-test') !== false, 'network screen must use the internal probe, explicit test and a browser-only bounded history');
observationAssert(strpos($javascript, 'networkDiagnostic') !== false && strpos($javascript, 'diagnosisMessage') !== false, 'network and server diagnostics must be presented with structured guidance');
observationAssert(strpos($javascript, '/api/v1/mysql-history') !== false && strpos($javascript, 'digest_ranking') !== false, 'MySQL history screen must consume only the authorized API contract');
observationAssert(strpos($javascript, 'history-synthetic-banner') !== false && strpos($javascript, 'historyMetricList') !== false, 'MySQL history screen must identify synthetic data and present deltas as readable metrics');
observationAssert(strpos($javascript, 'KILL ') === false && strpos($javascript, 'DELETE ') === false, 'prototype must not contain destructive actions');

$forbidden = array('confUsuario', '/proc/', 'apache2ctl', 'server-status', 'mysql_query');
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root . '/src', FilesystemIterator::SKIP_DOTS));
foreach ($iterator as $file) {
    if (!$file->isFile() || substr($file->getFilename(), -4) !== '.php') {
        continue;
    }
        if (strpos($file->getPathname(), '/Legacy/') !== false || strpos($file->getPathname(), '/Collectors/') !== false || strpos($file->getPathname(), '/Config/SiteTenantProvider.php') !== false) {
            continue;
        }
        $contents = file_get_contents($file->getPathname());
    foreach ($forbidden as $token) {
        observationAssert(stripos($contents, $token) === false, 'forbidden real-source token found: ' . $token . ' in ' . $file->getFilename());
    }
}

echo "Observability prototype tests passed\n";

// Primeiros coletores reais: somente fontes locais/read-only e forma estável do contrato da UI.
$linux = (new LinuxServerCollector())->collect();
observationAssert(isset($linux['hostname'], $linux['time'], $linux['uptime_seconds'], $linux['load'], $linux['cpu_percent'], $linux['memory_percent'], $linux['disk_percent']), 'linux collector must provide server summary keys');
observationAssert($linux['cpu_percent'] === null || ($linux['cpu_percent'] >= 0 && $linux['cpu_percent'] <= 100), 'cpu percentage must be bounded');
$apachePhp = (new ApachePhpCollector())->collect();
observationAssert(isset($apachePhp['mode'], $apachePhp['version'], $apachePhp['details']) && count($apachePhp['details']) >= 7, 'apache/php collector must expose the requested runtime details');
$mysqlSource = file_get_contents($root . '/src/Collectors/Mysql/MysqlObservationCollector.php');
foreach (array('INSERT ', 'UPDATE ', 'DELETE ', 'ALTER ', 'DROP ', 'TRUNCATE ', 'KILL ', 'FLUSH ', 'EXPLAIN ', 'SET GLOBAL') as $forbiddenSql) {
    observationAssert(stripos($mysqlSource, $forbiddenSql) === false, 'mysql collector must not contain a destructive statement: ' . $forbiddenSql);
}
observationAssert(strpos($mysqlSource, 'SHOW FULL PROCESSLIST') !== false, 'mysql collector must use the fixed SHOW FULL PROCESSLIST query');
observationAssert(strpos($mysqlSource, 'data_lock_waits') !== false && strpos($mysqlSource, 'innodb_lock_waits') !== false, 'mysql collector must support MySQL 8 and 5.7 lock-wait correlation');
echo "Observability real collector contract tests passed\n";

// Gravador MySQL Task 1: fonte simulada, sem conexão ou escrita externa.
$recorderSource = new class implements RecorderQuerySource {
    public $closed = false;
    public function rows(string $sql, array $parameters = array()): array
    {
        if (strpos($sql, 'CONNECTION_ID()') !== false) { return array(array('connection_id' => 999)); }
        if (strpos($sql, 'SHOW FULL PROCESSLIST') !== false) { return array(
            array('Id'=>10,'User'=>'app','Host'=>'10.0.0.1','db'=>'e229','Command'=>'Query','Time'=>25,'State'=>'executing','Info'=>"SELECT * FROM D009 WHERE id = 123 AND token = 'secret'"),
            array('Id'=>11,'User'=>'app','Host'=>'10.0.0.2','db'=>'e229','Command'=>'Query','Time'=>12,'State'=>'Waiting for table level lock','Info'=>'UPDATE T004 SET quantidade = 10 WHERE id = 20'),
            array('Id'=>12,'User'=>'app','Host'=>'10.0.0.3','db'=>'other','Command'=>'Query','Time'=>90,'State'=>'executing','Info'=>'SELECT * FROM hidden'),
        ); }
        if (strpos($sql, 'data_lock_waits') !== false) { return array(array('waiting_process_id'=>11,'blocking_process_id'=>10,'object_schema'=>'e229','object_name'=>'D009','index_name'=>'PRIMARY','lock_type'=>'RECORD','requested_lock_mode'=>'X','blocking_lock_mode'=>'X','waiting_user'=>'app','waiting_host'=>'10.0.0.2','waiting_database'=>'e229','waiting_seconds'=>12,'waiting_state'=>'lock wait','waiting_sql'=>'UPDATE D009 SET value = 55','blocking_user'=>'app','blocking_host'=>'10.0.0.1','blocking_database'=>'e229','blocking_seconds'=>25,'blocking_state'=>'executing','blocking_sql'=>"SELECT * FROM D009 WHERE token='private'")); }
        if (strpos($sql, 'INNODB_TRX') !== false) { observationAssert($parameters === array('e229'), 'transaction query must bind the selected database'); return array(array('transaction_id'=>'trx-1','transaction_state'=>'RUNNING','started_at'=>'2026-10-13 10:00:00','age_seconds'=>30,'wait_started_at'=>null,'rows_locked'=>2,'rows_modified'=>1,'tables_locked'=>1,'process_id'=>10,'user_name'=>'app','host_name'=>'10.0.0.1','database_name'=>'e229','process_state'=>'executing','transaction_sql'=>'UPDATE D009 SET value=99')); }
        if (strpos($sql, 'events_statements_summary_by_digest') !== false) { observationAssert($parameters === array('e229'), 'digest query must bind the selected database'); return array(array('digest'=>'abc','digest_text'=>'SELECT * FROM D009 WHERE id = ?','executions'=>20,'total_wait_picoseconds'=>20000000000,'average_wait_picoseconds'=>1000000000,'maximum_wait_picoseconds'=>5000000000,'lock_time_picoseconds'=>3000000000,'rows_examined'=>4000,'rows_sent'=>20,'rows_affected'=>0,'errors'=>1,'warnings'=>2,'temporary_tables'=>3,'temporary_disk_tables'=>1,'no_index_used'=>4,'no_good_index_used'=>0,'first_seen'=>'2026-10-13 09:00:00','last_seen'=>'2026-10-13 10:00:00')); }
        if (strpos($sql, 'SHOW GLOBAL STATUS') !== false) { return array(array('Variable_name'=>'Questions','Value'=>'1000'),array('Variable_name'=>'Threads_running','Value'=>'6')); }
        if (strpos($sql, 'events_errors_summary_global_by_error') !== false) { return array(array('error_number'=>1205,'error_name'=>'ER_LOCK_WAIT_TIMEOUT','sql_state'=>'HY000','raised'=>3,'handled'=>0,'first_seen'=>'2026-10-13 09:00:00','last_seen'=>'2026-10-13 10:41:34')); }
        throw new RuntimeException('unexpected test query');
    }
    public function close(): void { $this->closed = true; }
};
$recorderResult = (new MysqlRecorderCollector($recorderSource))->collect('e229', 15, 15);
observationAssert($recorderResult['collection']['partial'] === false, 'recorder fixture must collect every supported section');
observationAssert(count($recorderResult['long_queries']) === 2 && $recorderResult['long_queries'][0]['process_id'] === 10, 'recorder must retain long and lock-wait queries only for the selected database');
observationAssert(strpos($recorderResult['long_queries'][0]['query'], 'secret') === false && strpos($recorderResult['long_queries'][0]['query'], '123') === false, 'recorder must remove literal values from active SQL');
observationAssert($recorderResult['locks'][0]['blocking_process_id'] === 10 && $recorderResult['locks'][0]['confirmed'] === true, 'recorder must preserve confirmed InnoDB blocker relation');
observationAssert(strpos($recorderResult['locks'][0]['blocking_query'], 'private') === false, 'recorder must sanitize lock SQL');
observationAssert($recorderResult['long_transactions'][0]['age_seconds'] === 30, 'recorder must retain old transactions');
observationAssert($recorderResult['digests'][0]['total_time_ms'] === 20.0 && $recorderResult['digests'][0]['lock_time_ms'] === 3.0, 'recorder must convert Performance Schema picoseconds to milliseconds');
observationAssert($recorderResult['errors'][0]['number'] === 1205 && $recorderResult['status']['Threads_running'] === 6, 'recorder must expose relevant errors and status counters');
$invalidRecorderDatabase = false; try { (new MysqlRecorderCollector($recorderSource))->collect('../e229'); } catch (InvalidArgumentException $exception) { $invalidRecorderDatabase = true; }
observationAssert($invalidRecorderDatabase, 'recorder must reject invalid database identifiers');
$partialRecorderSource = new class implements RecorderQuerySource {
    public function rows(string $sql, array $parameters = array()): array { if (strpos($sql, 'CONNECTION_ID()') !== false) { return array(array('connection_id'=>1)); } throw new RuntimeException('permission denied'); }
    public function close(): void {}
};
$partialRecorderResult = (new MysqlRecorderCollector($partialRecorderSource))->collect('e229');
observationAssert($partialRecorderResult['collection']['partial'] === true && count($partialRecorderResult['collection']['failures']) === 6, 'recorder must report unavailable sources without discarding the sample');
$recorderSourceCode = file_get_contents($root . '/src/Collectors/Mysql/MysqlRecorderCollector.php');
foreach (array('INSERT ', 'UPDATE ', 'DELETE ', 'ALTER ', 'DROP ', 'TRUNCATE ', 'KILL ', 'FLUSH ', 'EXPLAIN ', 'SET GLOBAL') as $forbiddenRecorderSql) {
    observationAssert(stripos($recorderSourceCode, $forbiddenRecorderSql) === false, 'recorder collector must not contain a mutating statement: ' . $forbiddenRecorderSql);
}
echo "Observability MySQL recorder Task 1 fixtures passed\n";

// Gravador MySQL Task 2: deltas, persistência, retenção, trava e frequências, sem banco real.
$deltaCalculator = new MysqlRecorderDeltaCalculator();
$previousDigest = array(array('digest'=>'kept','query'=>'SELECT ?','executions'=>10,'total_time_ms'=>100.0,'lock_time_ms'=>10.0,'rows_examined'=>50,'rows_sent'=>10,'rows_affected'=>0,'errors'=>1,'warnings'=>2,'temporary_tables'=>3,'temporary_disk_tables'=>1,'no_index_used'=>4,'no_good_index_used'=>1));
$currentDigest = array(
    array('digest'=>'kept','query'=>'SELECT ?','executions'=>15,'total_time_ms'=>160.0,'lock_time_ms'=>14.0,'rows_examined'=>80,'rows_sent'=>15,'rows_affected'=>0,'errors'=>2,'warnings'=>4,'temporary_tables'=>4,'temporary_disk_tables'=>2,'no_index_used'=>6,'no_good_index_used'=>1),
    array('digest'=>'new','query'=>'UPDATE T SET A=?','executions'=>2,'total_time_ms'=>8.0,'lock_time_ms'=>1.0,'rows_examined'=>2,'rows_sent'=>0,'rows_affected'=>2,'errors'=>0,'warnings'=>0,'temporary_tables'=>0,'temporary_disk_tables'=>0,'no_index_used'=>0,'no_good_index_used'=>0),
);
$digestDelta = $deltaCalculator->digests($currentDigest, array_merge($previousDigest, array(array('digest'=>'gone','executions'=>4))));
observationAssert($digestDelta['items'][0]['baseline'] === false && $digestDelta['items'][0]['delta']['executions'] === 5.0 && $digestDelta['items'][0]['delta']['total_time_ms'] === 60.0, 'normal digest deltas must be calculated from cumulative counters');
observationAssert($digestDelta['items'][1]['baseline'] === true && $digestDelta['items'][1]['delta'] === null && $digestDelta['items'][1]['baseline_reason'] === 'new_digest', 'new digests must start as baselines');
observationAssert($digestDelta['disappeared_digests'] === array('gone'), 'disappeared digests must be reported');
$firstDigest = $deltaCalculator->digests($currentDigest, null);
observationAssert($firstDigest['items'][0]['baseline'] === true && $firstDigest['items'][0]['delta'] === null, 'first digest sample must be a baseline');
$compactDigest = $deltaCalculator->compactDigestsForHistory($deltaCalculator->digests($currentDigest, $currentDigest));
observationAssert($compactDigest['items'] === array(), 'unchanged digests must not be duplicated in history');
$compactActiveDigest = $deltaCalculator->compactDigestsForHistory($digestDelta);
observationAssert(count($compactActiveDigest['items']) === 2 && !array_key_exists('absolute', $compactActiveDigest['items'][0]), 'digest history must retain activity and baselines without absolute counters');
$resetDigest = $deltaCalculator->digests(array(array_merge($currentDigest[0], array('executions'=>1))), $currentDigest);
observationAssert($resetDigest['items'][0]['baseline_reason'] === 'counter_reset' && $resetDigest['items'][0]['delta'] === null, 'reset digest counters must create a baseline without negative deltas');
$previousStatus = array('Uptime'=>1000,'Connections'=>100,'Aborted_connects'=>4,'Threads_connected'=>8,'Threads_running'=>2);
$currentStatus = array('Uptime'=>1060,'Connections'=>112,'Aborted_connects'=>5,'Threads_connected'=>9,'Threads_running'=>3);
$statusDelta = $deltaCalculator->status($currentStatus, array(array('number'=>1205,'name'=>'timeout','raised'=>7,'handled'=>1)), $previousStatus, array(array('number'=>1205,'name'=>'timeout','raised'=>5,'handled'=>1)));
observationAssert($statusDelta['baseline'] === false && $statusDelta['delta']['Connections'] === 12.0 && $statusDelta['errors'][0]['delta']['raised'] === 2.0, 'status and global error deltas must be calculated normally');
$firstStatus = $deltaCalculator->status($currentStatus, array(), null, null);
observationAssert($firstStatus['baseline_reason'] === 'no_previous_sample' && $firstStatus['delta'] === null, 'first status sample must be a baseline');
$restartStatus = $deltaCalculator->status(array('Uptime'=>2,'Connections'=>1), array(), $previousStatus, array());
observationAssert($restartStatus['mysql_restart_detected'] === true && $restartStatus['baseline_reason'] === 'mysql_restart', 'lower uptime must identify a MySQL restart and create a baseline');

$recorderFixture = sys_get_temp_dir() . '/observability-recorder-task2-' . getmypid() . '-' . bin2hex(random_bytes(4));
@mkdir($recorderFixture, 0700, true);
$linkedUsersTarget = $recorderFixture . '/volume/dados_usuarios';
@mkdir($linkedUsersTarget . '/observability', 0700, true);
symlink($linkedUsersTarget, $recorderFixture . '/dados_usuarios');
putenv('OBSERVABILITY_TEST_MODE=1'); putenv('OBSERVABILITY_TEST_MYSQL_RECORDER_PATH=' . $recorderFixture . '/dados_usuarios/observability/mysql');
$linkedStorage = new MysqlRecorderStorage();
$linkedStorage->append('events', array('database'=>'e229','collected_at'=>'2026-10-13T10:00:00-03:00'), '2026-10-13');
$linkedHistory = (new MysqlRecorderHistoryRepository($recorderFixture . '/dados_usuarios/observability/mysql'))->read('events', array('e229'), strtotime('2026-10-13 09:00:00'), strtotime('2026-10-13 11:00:00'));
observationAssert($linkedStorage->baseDirectory() === $linkedUsersTarget . '/observability/mysql' && count($linkedHistory['records']) === 1, 'recorder must resolve an application-controlled parent symlink and keep writer/reader on the canonical volume path');
$linkedStorageTarget = $linkedUsersTarget . '/outside-storage'; @mkdir($linkedStorageTarget, 0700, true);
symlink($linkedStorageTarget, $linkedUsersTarget . '/observability/mysql-link');
putenv('OBSERVABILITY_TEST_MYSQL_RECORDER_PATH=' . $recorderFixture . '/dados_usuarios/observability/mysql-link');
$linkedStorageRootRejected = false; try { new MysqlRecorderStorage(); } catch (RuntimeException $exception) { $linkedStorageRootRejected = $exception->getMessage() === 'mysql_recorder_symlink_refused'; }
observationAssert($linkedStorageRootRejected, 'recorder must still reject a symlink used as its own mysql storage directory');
putenv('OBSERVABILITY_TEST_MODE=1'); putenv('OBSERVABILITY_TEST_MYSQL_RECORDER_PATH=' . $recorderFixture . '/storage');
$recorderStorage = new MysqlRecorderStorage();
$jsonlResult = $recorderStorage->append('events', array('kind'=>'fixture','valid'=>true), '2026-10-13');
$jsonlLines = file($recorderFixture . '/storage/events/2026-10-13.jsonl', FILE_IGNORE_NEW_LINES);
observationAssert($jsonlResult['written'] === true && count($jsonlLines) === 1 && json_decode($jsonlLines[0], true)['valid'] === true, 'JSONL history must contain one complete JSON object per line');
$recorderStorage->saveState('e229', array('counter'=>10));
$savedState = $recorderStorage->loadState('e229');
observationAssert($savedState['counter'] === 10 && glob($recorderFixture . '/storage/state/e229.json.tmp.*') === array(), 'state writes must atomically replace the target and remove temporary files');
file_put_contents($recorderFixture . '/storage/state/e229.json', '{corrupt');
observationAssert($recorderStorage->loadState('e229') === null, 'corrupt state must be treated as absent without stopping collection');
$symlinkTarget = $recorderFixture . '/outside.jsonl'; file_put_contents($symlinkTarget, "untouched\n");
symlink($symlinkTarget, $recorderFixture . '/storage/digests/2026-10-13.jsonl');
$symlinkRejected = false; try { $recorderStorage->append('digests', array('unsafe'=>true), '2026-10-13'); } catch (RuntimeException $exception) { $symlinkRejected = true; }
observationAssert($symlinkRejected && file_get_contents($symlinkTarget) === "untouched\n", 'history writer must reject symlink targets');
putenv('OBSERVABILITY_TEST_MYSQL_RECORDER_PATH=' . $recorderFixture . '/limited');
$limitedStorage = new MysqlRecorderStorage(256);
$limitResult = $limitedStorage->append('events', array('payload'=>str_repeat('x', 400)), '2026-10-13');
observationAssert($limitResult['limit_reached'] === true && filesize($recorderFixture . '/limited/events/2026-10-13.jsonl') === 0, 'daily size limit must stop growth before writing a partial line');
$recorderStorage->append('status', array('old'=>true), '2020-01-01');
file_put_contents($recorderFixture . '/storage/status/keep.txt', 'keep');
$retentionResult = $recorderStorage->retain(7, strtotime('2026-10-13 12:00:00'));
observationAssert(!file_exists($recorderFixture . '/storage/status/2020-01-01.jsonl') && file_exists($recorderFixture . '/storage/status/keep.txt') && in_array('status/2020-01-01.jsonl', $retentionResult['removed'], true), 'retention must remove only recognized old recorder files');
$firstLock = new MysqlRecorderRunLock(); $secondLock = new MysqlRecorderRunLock();
observationAssert($firstLock->acquire($recorderStorage->lockPath('e229')) === true && $secondLock->acquire($recorderStorage->lockPath('e229')) === false, 'a second recorder instance must fail its non-blocking lock');
$firstLock->release(); $thirdLock = new MysqlRecorderRunLock();
observationAssert($thirdLock->acquire($recorderStorage->lockPath('e229')) === true, 'recorder lock must be released after normal completion'); $thirdLock->release();

$frequencySource = new class implements RecorderQuerySource {
    public $fast=0; public $status=0; public $errors=0; public $digests=0; public $failDigests=false;
    public function rows(string $sql, array $parameters = array()): array {
        if (strpos($sql,'CONNECTION_ID()')!==false) { $this->fast++; return array(array('connection_id'=>999)); }
        if (strpos($sql,'SHOW FULL PROCESSLIST')!==false || strpos($sql,'data_lock_waits')!==false || strpos($sql,'INNODB_TRX')!==false) { return array(); }
        if (strpos($sql,'SHOW GLOBAL STATUS')!==false) { $this->status++; return array(array('Variable_name'=>'Uptime','Value'=>(string)(1000+$this->status*60)),array('Variable_name'=>'Connections','Value'=>(string)(100+$this->status))); }
        if (strpos($sql,'events_errors_summary_global_by_error')!==false) { $this->errors++; return array(); }
        if (strpos($sql,'events_statements_summary_by_digest')!==false) { $this->digests++; if ($this->failDigests) { throw new RuntimeException('digest unavailable'); } return array(array('digest'=>'frequency','digest_text'=>'SELECT ?','executions'=>10,'total_wait_picoseconds'=>1000000000)); }
        throw new RuntimeException('unexpected frequency query');
    }
    public function close(): void {}
};
$fakeNow = 1000000.0;
putenv('OBSERVABILITY_TEST_MYSQL_RECORDER_PATH=' . $recorderFixture . '/frequency');
$frequencyStorage = new MysqlRecorderStorage();
$frequencyRunner = new MysqlRecorderRunner(new MysqlRecorderCollector($frequencySource), $frequencyStorage, null, function () use (&$fakeNow): float { return $fakeNow; }, function (float $seconds) use (&$fakeNow): void { $fakeNow += $seconds; });
$frequencyResult = $frequencyRunner->run('e229', false, 130, 10);
observationAssert($frequencyResult['samples'] === 13 && $frequencySource->fast === 13, 'controlled cycle must collect fast categories every interval');
observationAssert($frequencySource->status === 3 && $frequencySource->errors === 3 && $frequencySource->digests === 1, 'status must run at most once per minute and digests at most once per five minutes');
$frequencyRunner->run('e229', true);
observationAssert($frequencySource->fast === 14 && $frequencySource->status === 3 && $frequencySource->errors === 3 && $frequencySource->digests === 1, 'persisted scheduling state must preserve slow-category frequencies across process runs');
$fakeNow += 300; $frequencySource->failDigests = true; $frequencyRunner->run('e229', true);
$frequencyStateAfterFailure = $frequencyStorage->loadState('e229');
$frequencyDigestLines = file($frequencyStorage->baseDirectory() . '/digests/' . date('Y-m-d') . '.jsonl', FILE_IGNORE_NEW_LINES);
$failedDigestRecord = json_decode($frequencyDigestLines[count($frequencyDigestLines) - 1], true);
observationAssert($frequencyStateAfterFailure['digests_absolute'][0]['digest'] === 'frequency' && $failedDigestRecord['counters']['unavailable'] === true && $failedDigestRecord['counters']['disappeared_digests'] === array(), 'failed digest collection must preserve the previous baseline and must not report false disappearance');

$mysqlRecorderFiles = glob($root . '/src/Collectors/Mysql/*Recorder*.php');
foreach ($mysqlRecorderFiles as $mysqlRecorderFile) {
    $mysqlRecorderCode = file_get_contents($mysqlRecorderFile);
    foreach (array('INSERT ', 'UPDATE ', 'DELETE ', 'ALTER ', 'DROP ', 'TRUNCATE ', 'KILL ', 'FLUSH ', 'EXPLAIN ', 'SET GLOBAL') as $forbiddenRecorderSql) {
        observationAssert(stripos($mysqlRecorderCode, $forbiddenRecorderSql) === false, 'Task 2 recorder must remain read-only: ' . basename($mysqlRecorderFile) . ' / ' . $forbiddenRecorderSql);
    }
}
putenv('OBSERVABILITY_TEST_MYSQL_RECORDER_PATH');
echo "Observability MySQL recorder Task 2 fixtures passed\n";

require __DIR__ . '/mysql-recorder-task3.php';
require __DIR__ . '/mysql-recorder-task4.php';
require __DIR__ . '/mysql-recorder-task5.php';
require __DIR__ . '/mysql-recorder-task6.php';
require __DIR__ . '/mysql-recorder-task7.php';
require __DIR__ . '/mysql-recorder-task8.php';
require __DIR__ . '/mysql-recorder-task9.php';

// Etapa 3.2: fixtures temporárias somente em modo de teste.
$fixture = sys_get_temp_dir() . '/observability-test-' . getmypid();
@mkdir($fixture, 0700, true);
$runtimePath = $fixture . '/runtime.json'; $sysConfigPath=$fixture . '/sysConfig.php'; $accessPath = $fixture . '/access.json'; $legacyAccessPath = $fixture . '/legacy-client-access.json'; $missingAccessPath = $fixture . '/missing-access.json'; $ratePath = $fixture . '/rate.json'; $sitesPath=$fixture . '/sites'; @mkdir($sitesPath,0700,true);
$archive = new IncidentSnapshotArchive($fixture . '/captures'); $savedCapture = $archive->save($internalSnapshot, $internalContext, 'fixture'); observationAssert($archive->get($internalContext, $savedCapture['id']) !== null, 'saved capture must be retrievable in its scope'); observationAssert($archive->delete($internalContext, $savedCapture['id']) === true && $archive->get($internalContext, $savedCapture['id']) === null, 'capture deletion must remove only the requested scoped record'); observationAssert($archive->delete($internalContext, '../invalid') === false, 'capture deletion must reject invalid identifiers');
file_put_contents($sysConfigPath, '<?php $sys=array("senhaRootMaster"=>"'.md5('master-test').'");');
foreach (array('client.test'=>array('fixture','fixture'),'client-a.test'=>array('fixture-a','database_a'),'client-b.test'=>array('fixture-b','database_b')) as $host=>$database) { file_put_contents($sitesPath.'/'.$host.'-confUsuario.php', '<?php $confUsuario='.var_export(array('dbDatabase'=>$database[1],'dbHost'=>$database[0],'dbUser'=>'user','dbPass'=>'password','pathRaiz'=>'/var/www','urlRaiz'=>'https://'.$host.'/'),true).';'); }
file_put_contents($legacyAccessPath, '{"enabled":true,"allowed_users":{"182":["view"]}}'); chmod($legacyAccessPath,0640);
putenv('OBSERVABILITY_TEST_MODE=1'); putenv('OBSERVABILITY_TEST_SYSCONFIG_PATH='.$sysConfigPath); putenv('OBSERVABILITY_TEST_ACCESS_PATH='.$missingAccessPath); putenv('OBSERVABILITY_TEST_RATE_PATH='.$ratePath); putenv('OBSERVABILITY_TEST_SITES_PATH='.$sitesPath);
$master=new MasterPasswordProvider(); $tenants=new SiteTenantProvider(); $repository=new class implements LegacyCredentialValidator { public $calls=0; public function authenticate(array $tenant,string $login,string $password):?array{$this->calls++;if($password!=='normal-test'){return null;}return $login==='client'?array('id'=>182):($login==='analyst'?array('id'=>245):($login==='other'?array('id'=>999):null));} };
observationAssert($tenants->resolve('CLIENT.TEST:443')['database']['database']==='fixture','site tenant provider must normalize a valid host and load only its matching file');
foreach (array('other.test','../client.test','client.test/path') as $invalidHost) { $rejected=false; try{$tenants->resolve($invalidHost);}catch(Throwable $e){$rejected=true;} observationAssert($rejected,'unknown or malformed hosts must fail closed'); }
file_put_contents($sitesPath.'/empty.test-confUsuario.php','<?php $other=array();'); file_put_contents($sitesPath.'/incomplete.test-confUsuario.php','<?php $confUsuario=array("dbDatabase"=>"x");');
foreach (array('empty.test','incomplete.test') as $invalidTenant) { $rejected=false; try{$tenants->resolve($invalidTenant);}catch(Throwable $e){$rejected=true;} observationAssert($rejected,'missing or incomplete confUsuario must fail closed'); }
file_put_contents($sitesPath.'/duplicate.test-confUsuario.php','<?php $confUsuario='.var_export(array('dbDatabase'=>'database_a','dbHost'=>'fixture-a','dbUser'=>'user','dbPass'=>'password','pathRaiz'=>'/var/www','urlRaiz'=>'https://duplicate.test/'),true).';');
observationAssert(count($tenants->all())===3,'root tenant listing must skip invalid entries and deduplicate equal host/database pairs');
$auth=new AuthenticationService($master,new TrustedHostResolver($tenants),$repository,new ClientAccessPolicyLoader());
$tryAuth=function($login,$password)use($auth){try{return $auth->authenticate($login,$password,array('HTTP_HOST'=>'client.test'));}catch(Throwable $e){return null;}};
$masterIdentity=$auth->authenticate('root','master-test',array()); observationAssert($masterIdentity['role']==='hardness_internal' && $repository->calls===0,'root master must not consult C007');
observationAssert($auth->authenticate('root','normal-test',array())===null,'root other password must be denied');
observationAssert($tryAuth('client','normal-test')===null,'without access.json, a valid ERP user must be denied even if the legacy file exists');
file_put_contents($accessPath, '{"enabled":true,"tenants":{"client.test":{"allowed_users":{"182":["view"],"245":["view","full_sql"]}}}}'); chmod($accessPath,0640); putenv('OBSERVABILITY_TEST_ACCESS_PATH='.$accessPath);
observationAssert($auth->authenticate('root','master-test',array())['role']==='hardness_internal','root must remain authorized without being listed in access.json');
$viewIdentity=$tryAuth('client','normal-test'); observationAssert($viewIdentity['role']==='client' && $viewIdentity['capabilities']['full_sql']===false,'view permission must authorize the panel but not full SQL');
observationAssert($tryAuth('other','normal-test')===null,'unlisted ERP users must be denied');
$sqlIdentity=$tryAuth('analyst','normal-test'); observationAssert($sqlIdentity['capabilities']['view']===true && $sqlIdentity['capabilities']['full_sql']===true,'view and full_sql must authorize full SQL');
file_put_contents($accessPath, '{"enabled":false,"tenants":{"client.test":{"allowed_users":{"182":["view"]}}}}'); chmod($accessPath,0640); observationAssert($tryAuth('client','normal-test')===null,'enabled false must deny common users');
foreach (array('', '{', '{"enabled":true}', '{"enabled":true,"allowed_users":{"182":["unknown"]}}', '{"enabled":true,"allowed_users":{"63":["view"]}}') as $invalidJson) { file_put_contents($accessPath, $invalidJson); chmod($accessPath,0640); $rejected=false; try{(new ClientAccessPolicyLoader())->load();}catch(Throwable $e){$rejected=true;} observationAssert($rejected && $tryAuth('client','normal-test')===null,'invalid, empty or unknown access policy must fail closed'); }
$hostRejected=false; try{(new TrustedHostResolver($tenants))->resolve(array('HTTP_HOST'=>'other.test'));}catch(Throwable $e){$hostRejected=true;} observationAssert($hostRejected,'host change must not resolve another tenant');
$multiRepository=new class implements LegacyCredentialValidator { public $databases=array(); public function authenticate(array $tenant,string $login,string $password):?array{$this->databases[]=$tenant['database']['database'];return $login==='same-id'&&$password==='normal-test'?array('id'=>182):null;} };
$multiAuth=new AuthenticationService($master,new TrustedHostResolver($tenants),$multiRepository,new ClientAccessPolicyLoader());
file_put_contents($accessPath, '{"enabled":true,"allowed_users":{"182":["view"]}}'); chmod($accessPath,0640);
observationAssert($multiAuth->authenticate('same-id','normal-test',array('HTTP_HOST'=>'client-a.test'))===null,'legacy global access.json must fail closed when multiple tenants exist');
file_put_contents($accessPath, '{"enabled":true,"tenants":{"client-a.test":{"allowed_users":{"182":["view"]}}}}'); chmod($accessPath,0640);
$tenantAIdentity=$multiAuth->authenticate('same-id','normal-test',array('HTTP_HOST'=>'client-a.test'));
observationAssert($tenantAIdentity['tenant_key']==='client-a.test' && $tenantAIdentity['request_host']==='client-a.test' && !isset($tenantAIdentity['tenant']),'client session identity must retain only tenant and request identifiers, never database credentials');
observationAssert($multiAuth->authenticate('same-id','normal-test',array('HTTP_HOST'=>'client-b.test'))===null,'the same C007 id in another tenant must not inherit client A authorization');
file_put_contents($accessPath, '{"enabled":true,"tenants":{"client-a.test":{"allowed_users":{"182":["view"]}},"client-b.test":{"allowed_users":{"182":["view","full_sql"]}}}}'); chmod($accessPath,0640);
$tenantBIdentity=$multiAuth->authenticate('same-id','normal-test',array('HTTP_HOST'=>'client-b.test'));
observationAssert($tenantBIdentity['tenant_key']==='client-b.test' && $tenantBIdentity['capabilities']['full_sql']===true,'tenant B authorization must be evaluated independently for the same C007 id');
observationAssert(in_array('database_a',$multiRepository->databases,true) && in_array('database_b',$multiRepository->databases,true),'each host must authenticate C007 against its resolved tenant database');
$clientSession=new ObservationSession(new TrustedHostResolver($tenants),new ClientAccessPolicyLoader());
$_SESSION=array('obs_identity'=>$tenantAIdentity,'obs_created'=>time(),'obs_seen'=>time(),'obs_csrf'=>'fixture'); $_SERVER['HTTP_HOST']='client-a.test';
observationAssert($clientSession->current()->toArray()['role']==='client','a client session must remain valid only on its resolved host and tenant');
$_SERVER['HTTP_HOST']='client-b.test'; $sessionHostRejected=false; try{$clientSession->current();}catch(Throwable $e){$sessionHostRejected=true;}
observationAssert($sessionHostRejected && $_SESSION===array(),'a client session reused on another tenant host must be destroyed');
observationAssert(strpos($mysqlSource, 'hash_equals($database, (string) $row[\'db\'])') !== false,'client MySQL process collection must filter rows by the selected schema in the backend');
$limiter=new LoginRateLimiter(); for($i=0;$i<5;$i++){$limiter->failure('127.0.0.1','client');} observationAssert($limiter->blocked('127.0.0.1','client'),'rate limit must block repeated failures');
observationAssert(strpos(file_get_contents($root . '/src/Application.php'),'MockObservationContextProvider') !== false,'mock is retained only behind explicit development switch');
@unlink($runtimePath); observationAssert($master->hash()===md5('master-test'),'root master source must not depend on runtime.json');
observationAssert(strpos(file_get_contents($root . '/src/Auth/AuthenticationService.php'),'master_password_hash')===false && strpos(file_get_contents($root . '/src/Config/RuntimeConfiguration.php'),'master_password_hash')===false,'master password hash must not remain in the observability runtime contract');
putenv('OBSERVABILITY_TEST_SYSCONFIG_PATH='.$fixture.'/missing-sysConfig.php'); $missingMaster=false; try{(new MasterPasswordProvider())->hash();}catch(Throwable $e){$missingMaster=true;} observationAssert($missingMaster,'missing official master config must fail closed');
file_put_contents($fixture.'/invalid-sysConfig.php','<?php $sys=array("senhaRootSistema"=>"x");'); putenv('OBSERVABILITY_TEST_SYSCONFIG_PATH='.$fixture.'/invalid-sysConfig.php'); $invalidMaster=false; try{(new MasterPasswordProvider())->hash();}catch(Throwable $e){$invalidMaster=true;} observationAssert($invalidMaster,'invalid official master config must fail closed');
putenv('OBSERVABILITY_TEST_MODE'); putenv('OBSERVABILITY_TEST_SYSCONFIG_PATH'); putenv('OBSERVABILITY_TEST_ACCESS_PATH'); putenv('OBSERVABILITY_TEST_RATE_PATH'); putenv('OBSERVABILITY_TEST_SITES_PATH');
echo "Observability authentication MVP fixtures passed\n";
