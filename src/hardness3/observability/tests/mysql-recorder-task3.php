<?php
declare(strict_types=1);

if (!defined('HARDNESS_OBSERVABILITY_ROOT')) { require dirname(__DIR__) . '/bootstrap/bootstrap.php'; }
if (!function_exists('observationAssert')) {
    function observationAssert(bool $condition, string $message): void { if (!$condition) { throw new RuntimeException($message); } }
}

use Hardness\Observability\Collectors\Mysql\MysqlRecorderHistoryRepository;
use Hardness\Observability\Collectors\Mysql\MysqlRecorderHistoryService;
use Hardness\Observability\Collectors\Mysql\MysqlRecorderScopeResolver;
use Hardness\Observability\Collectors\Mysql\MysqlRecorderStorage;
use Hardness\Observability\Config\SiteTenantProvider;
use Hardness\Observability\Context\ObservationContext;
use Hardness\Observability\Context\ObservationContextProvider;
use Hardness\Observability\Http\PrototypeController;
use Hardness\Observability\Http\Request;

$task3Root = sys_get_temp_dir() . '/observability-recorder-task3-' . getmypid() . '-' . bin2hex(random_bytes(4));
@mkdir($task3Root, 0700, true);
putenv('OBSERVABILITY_TEST_MODE=1');
putenv('OBSERVABILITY_TEST_MYSQL_RECORDER_PATH=' . $task3Root . '/history');
$task3Storage = new MysqlRecorderStorage();

$task3Event = function (string $at, string $schema, int $id, bool $partial = false): array {
    return array('schema_version'=>2, 'sample_type'=>'events', 'collected_at'=>$at, 'database'=>$schema,
        'locks'=>array(array('waiting_process_id'=>$id, 'waiting_seconds'=>12, 'waiting_user'=>'private-user', 'waiting_host'=>'10.0.0.1', 'waiting_query'=>'UPDATE T SET A=?', 'waiting_fingerprint'=>'wait-fp', 'blocking_query'=>'SELECT * FROM T WHERE A=?', 'blocking_fingerprint'=>'block-fp')),
        'long_queries'=>array(array('process_id'=>$id, 'seconds'=>20, 'user'=>'private-user', 'host'=>'10.0.0.2', 'query'=>'SELECT * FROM T WHERE SECRET=?', 'fingerprint'=>'query-fp')),
        'long_transactions'=>array(array('transaction_id'=>'trx-' . $id, 'process_id'=>$id, 'age_seconds'=>30, 'user'=>'private-user', 'host'=>'10.0.0.3', 'query'=>'UPDATE T SET SECRET=?', 'fingerprint'=>'trx-fp')),
        'collection'=>array('partial'=>$partial, 'failures'=>$partial ? array(array('section'=>'locks', 'message'=>'password=do-not-expose /internal/path')) : array()));
};
$task3Storage->append('events', $task3Event('2026-10-12T23:59:00-03:00', 'tenant_a', 10), '2026-10-12');
$task3Storage->append('events', $task3Event('2026-10-13T10:00:00-03:00', 'tenant_a', 11, true), '2026-10-13');
$task3Storage->append('events', $task3Event('2026-10-13T10:01:00-03:00', 'tenant_b', 12), '2026-10-13');
file_put_contents($task3Root . '/history/events/2026-10-13.jsonl', '{"last":', FILE_APPEND);

$baselineStatus = array('schema_version'=>2, 'sample_type'=>'status', 'collected_at'=>'2026-10-13T09:00:00-03:00', 'database'=>'tenant_a', 'synthetic'=>true, 'collection'=>array('partial'=>false), 'counters'=>array('baseline'=>true, 'baseline_reason'=>'no_previous_sample', 'mysql_restart_detected'=>false, 'absolute'=>array('Connections'=>100,'Threads_connected'=>3,'Threads_running'=>1), 'delta'=>null, 'errors'=>array(array('number'=>1205,'name'=>'ER_LOCK_WAIT_TIMEOUT','sql_state'=>'HY000','baseline'=>true,'baseline_reason'=>'new_error_counter','absolute'=>array('raised'=>5,'handled'=>0),'delta'=>null))));
$deltaStatus = array('schema_version'=>2, 'sample_type'=>'status', 'collected_at'=>'2026-10-13T09:01:00-03:00', 'database'=>'tenant_a', 'collection'=>array('partial'=>false), 'counters'=>array('baseline'=>false, 'baseline_reason'=>null, 'mysql_restart_detected'=>false, 'absolute'=>array('Connections'=>107,'Threads_connected'=>4,'Threads_running'=>2), 'delta'=>array('Connections'=>7), 'errors'=>array(array('number'=>1205,'name'=>'ER_LOCK_WAIT_TIMEOUT','sql_state'=>'HY000','baseline'=>false,'baseline_reason'=>null,'absolute'=>array('raised'=>7,'handled'=>0),'delta'=>array('raised'=>2,'handled'=>0)))));
$resetStatus = array('schema_version'=>2, 'sample_type'=>'status', 'collected_at'=>'2026-10-13T09:02:00-03:00', 'database'=>'tenant_a', 'collection'=>array('partial'=>false), 'counters'=>array('baseline'=>true, 'baseline_reason'=>'counter_reset', 'mysql_restart_detected'=>false, 'absolute'=>array('Connections'=>1), 'delta'=>null, 'errors'=>array()));
$restartStatus = array('schema_version'=>2, 'sample_type'=>'status', 'collected_at'=>'2026-10-13T09:03:00-03:00', 'database'=>'tenant_a', 'collection'=>array('partial'=>false), 'counters'=>array('baseline'=>true, 'baseline_reason'=>'mysql_restart', 'mysql_restart_detected'=>true, 'absolute'=>array('Connections'=>2), 'delta'=>null, 'errors'=>array()));
$task3Storage->append('status', $baselineStatus, '2026-10-13');
file_put_contents($task3Root . '/history/status/2026-10-13.jsonl', "not-json\n", FILE_APPEND);
$task3Storage->append('status', $deltaStatus, '2026-10-13');
$task3Storage->append('status', $resetStatus, '2026-10-13');
$task3Storage->append('status', $restartStatus, '2026-10-13');
$task3Storage->append('status', array('schema_version'=>2,'sample_type'=>'collector_health','collected_at'=>'2026-10-13T09:04:00-03:00','database'=>'tenant_a','conditions'=>array('daily_file_limit_reached','partial_failure'),'categories'=>array('events'),'failures'=>array(array('section'=>'events','message'=>'credential=secret /private/path'))), '2026-10-13');

$digestBaseline = array('schema_version'=>2,'sample_type'=>'digests','collected_at'=>'2026-10-13T08:00:00-03:00','database'=>'tenant_a','collection'=>array('partial'=>false),'counters'=>array('items'=>array(array('digest'=>'digest-a','query'=>'SELECT * FROM A WHERE ID = ?','baseline'=>true,'baseline_reason'=>'new_digest','absolute'=>array('executions'=>1000,'total_time_ms'=>9000),'delta'=>null)), 'disappeared_digests'=>array()));
$digestDelta = array('schema_version'=>2,'sample_type'=>'digests','collected_at'=>'2026-10-13T08:05:00-03:00','database'=>'tenant_a','collection'=>array('partial'=>true),'counters'=>array('items'=>array(array('digest'=>'digest-a','query'=>'SELECT * FROM A WHERE ID = ?','baseline'=>false,'baseline_reason'=>null,'absolute'=>array('executions'=>1005,'total_time_ms'=>9030),'delta'=>array('executions'=>5,'total_time_ms'=>30,'errors'=>1)),array('digest'=>'digest-b','query'=>'SELECT * FROM B','baseline'=>false,'baseline_reason'=>null,'absolute'=>array('executions'=>500,'total_time_ms'=>9200),'delta'=>array('executions'=>2,'total_time_ms'=>200,'errors'=>0))), 'disappeared_digests'=>array('digest-gone')));
$task3Storage->append('digests', $digestBaseline, '2026-10-13');
$task3Storage->append('digests', $digestDelta, '2026-10-13');
$outside = $task3Root . '/outside.jsonl'; file_put_contents($outside, "{}\n"); symlink($outside, $task3Root . '/history/digests/2026-10-12.jsonl');

$from = strtotime('2026-10-12 00:00:00'); $to = strtotime('2026-10-13 23:59:59');
$repository = new MysqlRecorderHistoryRepository($task3Root . '/history');
$eventsRead = $repository->read('events', array('tenant_a'), $from, $to);
observationAssert(count($eventsRead['records']) === 2 && $eventsRead['files_seen'] === 2, 'Task 3 must read normal JSONL across multiple daily files and filter unauthorized schemas');
observationAssert($eventsRead['truncated_last_lines'] === 1 && $eventsRead['records'][1]['database'] === 'tenant_a', 'Task 3 must tolerate a truncated final line without losing prior records');
$statusRead = $repository->read('status', array('tenant_a'), $from, $to);
observationAssert(count($statusRead['records']) === 5 && $statusRead['invalid_lines'] === 1 && $statusRead['available'] === false, 'Task 3 must retain valid records around an invalid intermediate line and mark the source unavailable');
$digestsRead = $repository->read('digests', array('tenant_a'), $from, $to);
observationAssert(count($digestsRead['records']) === 2 && in_array('unsafe_file_refused', $digestsRead['unavailable_reasons'], true), 'Task 3 must refuse symlink history files without losing safe daily files');

$invalidCategory = false; try { $repository->read('state', array('tenant_a'), $from, $to); } catch (InvalidArgumentException $exception) { $invalidCategory = true; }
$invalidPeriod = false; try { $repository->read('events', array('tenant_a'), $from, $from + MysqlRecorderHistoryRepository::MAX_PERIOD_SECONDS + 1); } catch (InvalidArgumentException $exception) { $invalidPeriod = true; }
observationAssert($invalidCategory && $invalidPeriod, 'Task 3 must reject unknown categories and excessive periods');

$service = new MysqlRecorderHistoryService($repository);
$redacted = $service->history(array('tenant_a'), array('view'=>true,'full_sql'=>false,'connection_identity'=>false), $from, $to, 1, 2);
observationAssert($redacted['contract_version'] === 'mysql-history.v1' && $redacted['pagination']['limit'] === 2 && $redacted['pagination']['has_more'] === true, 'Task 3 API contract must be versioned and paginated');
observationAssert(count($redacted['digest_ranking']) === 2 && $redacted['digest_ranking'][0]['digest'] === 'digest-b' && $redacted['digest_ranking'][0]['activity']['total_time_ms'] === 200.0, 'digest ranking must use period delta and exclude the larger absolute baseline');
observationAssert($redacted['signals']['resets'] === 1 && $redacted['signals']['mysql_restarts'] === 1 && $redacted['signals']['disappeared_digests'] === array('digest-gone'), 'reset, restart and disappeared digests must remain explicit');
observationAssert($redacted['signals']['partial_collections'] >= 2 && $redacted['signals']['file_limit_reached'] === true && $redacted['metadata']['partial'] === true, 'partial collection and daily file limit must remain explicit');
observationAssert($redacted['signals']['synthetic_data'] === true && $redacted['metadata']['synthetic'] === true, 'synthetic fixtures must be explicitly marked for the history screen');
observationAssert($redacted['status_series'][0]['delta'] === null && $redacted['status_series'][1]['delta']['Connections'] === 7, 'status baselines must not be counted as deltas');
observationAssert($redacted['error_series'][0]['delta'] === null && $redacted['error_series'][1]['delta']['raised'] === 2, 'error series must contain only valid deltas after baseline');
$redactedJson = json_encode($redacted);
observationAssert(strpos($redactedJson, 'private-user') === false && strpos($redactedJson, '10.0.0.') === false && strpos($redactedJson, 'tenant_a') === false && strpos($redactedJson, '"query":') === false, 'connection_identity and full_sql must redact sensitive history fields');
observationAssert(strpos($redactedJson, $task3Root) === false && strpos($redactedJson, 'do-not-expose') === false && strpos($redactedJson, 'credential=secret') === false && strpos($redactedJson, '"absolute":') === false, 'API must not expose paths, credentials, state or absolute counters');
$privileged = $service->history(array('tenant_a'), array('view'=>true,'full_sql'=>true,'connection_identity'=>true), $from, $to, 1, 200);
$eventDetails = $privileged['timeline'][count($privileged['timeline']) - 1]['details'];
observationAssert(strpos(json_encode($privileged), 'private-user') !== false && (isset($eventDetails['query']) || isset($eventDetails['waiting_query'])), 'full_sql and connection_identity must control their respective sensitive fields');

putenv('OBSERVABILITY_TEST_MYSQL_RECORDER_PATH=' . $task3Root . '/large'); $largeStorage = new MysqlRecorderStorage();
for ($i = 0; $i < 40; $i++) { $largeStorage->append('events', array('schema_version'=>2,'sample_type'=>'events','collected_at'=>'2026-10-13T10:' . str_pad((string) $i, 2, '0', STR_PAD_LEFT) . ':00-03:00','database'=>'tenant_a','payload'=>str_repeat('x', 180),'locks'=>array(),'long_queries'=>array(),'long_transactions'=>array(),'collection'=>array('partial'=>false)), '2026-10-13'); }
$limitedRead = (new MysqlRecorderHistoryRepository($task3Root . '/large'))->read('events', array('tenant_a'), $from, $to, 1024, 4, 3);
observationAssert($limitedRead['limits_reached'] !== array() && $limitedRead['lines_read'] <= 4 && $limitedRead['bytes_read'] <= 1024, 'large history reads must stop at byte, line and result limits');

$sites = $task3Root . '/sites'; @mkdir($sites, 0700, true);
foreach (array('client-a.test'=>'tenant_a','client-b.test'=>'tenant_b') as $host=>$schema) { file_put_contents($sites . '/' . $host . '-confUsuario.php', '<?php $confUsuario=' . var_export(array('dbDatabase'=>$schema,'dbHost'=>'fixture','dbUser'=>'user','dbPass'=>'password','pathRaiz'=>'/var/www','urlRaiz'=>'https://' . $host . '/'), true) . ';'); }
$clientContext = new class implements ObservationContextProvider { public function current(): ObservationContext { return new ObservationContext('client','Tenant',array('view'=>true,'full_sql'=>false,'connection_identity'=>false),array('Tenant')); } };
$tenantProvider = new SiteTenantProvider($sites); $_SERVER['HTTP_HOST'] = 'client-a.test';
$scopeA = (new MysqlRecorderScopeResolver($clientContext, $tenantProvider))->resolve(); $_SERVER['HTTP_HOST'] = 'client-b.test'; $scopeB = (new MysqlRecorderScopeResolver($clientContext, $tenantProvider))->resolve();
observationAssert($scopeA['schemas'] === array('tenant_a') && $scopeB['schemas'] === array('tenant_b'), 'the same client identity on different hosts must remain isolated by backend tenant resolution');
$rootContext = new class implements ObservationContextProvider { public function current(): ObservationContext { return new ObservationContext('hardness_internal','Root',array('view'=>true,'full_sql'=>true,'connection_identity'=>true),array()); } };
$rootScope = (new MysqlRecorderScopeResolver($rootContext, $tenantProvider))->resolve(); sort($rootScope['schemas']);
observationAssert($rootScope['schemas'] === array('server','tenant_a','tenant_b'), 'Hardness root scope must include server metrics and every schema from the authorized backend inventory');
putenv('OBSERVABILITY_TEST_MYSQL_RECORDER_PATH=' . $task3Root . '/history'); $_SERVER['HTTP_HOST'] = 'client-a.test';
$controller = new PrototypeController($clientContext, new class { public function snapshot(): array { return array(); } }, null, $service, new MysqlRecorderScopeResolver($clientContext, $tenantProvider));
$response = $controller->mysqlHistory(Request::fromValues('GET', '/api/v1/mysql-history', array('schema'=>'tenant_b','tenant'=>'client-b.test','path'=>'/etc/passwd','page'=>'1','limit'=>'100','from'=>'2026-10-12T00:00:00-03:00','to'=>'2026-10-13T23:59:59-03:00')));
$api = json_decode($response->body(), true);
observationAssert($response->status() === 200 && strpos($response->body(), 'tenant_b') === false && strpos($response->body(), '/etc/passwd') === false && $api['scope']['schema_count'] === 1, 'browser schema, tenant and path parameters must be ignored in favor of backend scope');
$badPeriodResponse = $controller->mysqlHistory(Request::fromValues('GET', '/api/v1/mysql-history', array('from'=>'invalid','to'=>'2026-10-13T23:59:59-03:00')));
observationAssert($badPeriodResponse->status() === 400, 'API must reject invalid periods with a stable client error');
$emptyRoot = $task3Root . '/empty'; @mkdir($emptyRoot . '/events', 0700, true); @mkdir($emptyRoot . '/status', 0700, true); @mkdir($emptyRoot . '/digests', 0700, true);
$emptyHistory = (new MysqlRecorderHistoryService(new MysqlRecorderHistoryRepository($emptyRoot)))->history(array('tenant_a'), array('view'=>true), $from, $to);
observationAssert($emptyHistory['timeline'] === array() && count($emptyHistory['metadata']['no_data_categories']) === 3, 'API must return a stable empty contract before the recorder has data');

$task3Files = array_merge(glob(dirname(__DIR__) . '/src/Collectors/Mysql/*Recorder*.php'), array(dirname(__DIR__) . '/public/assets/js/observability.js'));
foreach ($task3Files as $task3File) {
    $code = file_get_contents($task3File);
    foreach (array('KILL ', 'DELETE ', 'FLUSH ', 'TRUNCATE ', 'ALTER ', 'DROP ', 'SET GLOBAL') as $mutableCommand) { observationAssert(stripos($code, $mutableCommand) === false, 'Task 3 must not add mutable MySQL actions: ' . basename($task3File) . ' / ' . $mutableCommand); }
}
putenv('OBSERVABILITY_TEST_MYSQL_RECORDER_PATH');
echo "Observability MySQL recorder Task 3 fixtures passed\n";
