<?php
declare(strict_types=1);

if (!defined('HARDNESS_OBSERVABILITY_ROOT')) { require dirname(__DIR__) . '/bootstrap/bootstrap.php'; }
if (!function_exists('observationAssert')) {
    function observationAssert(bool $condition, string $message): void { if (!$condition) { throw new RuntimeException($message); } }
}

use Hardness\Observability\Collectors\Mysql\MysqliRecorderQuerySource;
use Hardness\Observability\Collectors\Mysql\MysqlRecorderSchemaProvider;
use Hardness\Observability\Config\SiteTenantProvider;

$task5Root = dirname(__DIR__);
$task5Cli = file_get_contents($task5Root . '/bin/mysql-recorder.php');
$task5DemoCli = file_get_contents($task5Root . '/bin/mysql-recorder-demo.php');
$task5Source = file_get_contents($task5Root . '/src/Collectors/Mysql/MysqliRecorderQuerySource.php');

observationAssert(
    strpos($task5Cli, "'all-schemas'") !== false
    && strpos($task5Cli, "'dry-run'") !== false
    && strpos($task5Cli, "'record'") !== false,
    'Task 5 CLI must retain explicit server scope and mutually exclusive execution modes'
);
observationAssert(
    strpos($task5Cli, '$_GET') === false
    && strpos($task5Cli, '$_POST') === false
    && strpos($task5Cli, 'OBSERVABILITY_TEST_MYSQL_RECORDER_PATH') === false,
    'Task 5 CLI must not accept browser input or an operational storage path'
);
observationAssert(
    strpos($task5Source, "extension_loaded('mysqli')") !== false
    && strpos($task5Source, 'mysql_recorder_mysqli_extension_unavailable') !== false,
    'Task 5 connection must fail explicitly when mysqli is unavailable'
);
observationAssert(
    strpos($task5Source, 'connect_error') === false,
    'Task 5 connection failures must not echo driver details that can expose connection identity'
);

if (!extension_loaded('mysqli')) {
    $unavailable = false;
    try {
        MysqliRecorderQuerySource::connect(array('database'=>array('host'=>'127.0.0.1','username'=>'fixture','password'=>'fixture')));
    } catch (RuntimeException $exception) {
        $unavailable = $exception->getMessage() === 'mysql_recorder_mysqli_extension_unavailable';
    }
    observationAssert($unavailable, 'missing mysqli must stop before attempting a connection');
}

$task5Fixture = sys_get_temp_dir() . '/observability-recorder-task5-' . getmypid() . '-' . bin2hex(random_bytes(4));
@mkdir($task5Fixture . '/sites', 0700, true);
$task5LocalProfiles = $task5Fixture . '/ambientes.php';
$task5Profiles = array(
    'localhost'=>array('dbDatabase'=>'e998','dbHost'=>'127.0.0.1','dbUser'=>'local','dbPass'=>'fixture','pathRaiz'=>'/www/hardness3/','urlRaiz'=>'http://localhost/'),
    'client.localhost'=>array('dbDatabase'=>'e229','dbHost'=>'127.0.0.1','dbUser'=>'local','dbPass'=>'fixture','pathRaiz'=>'/www/hardness3/','urlRaiz'=>'http://client.localhost/'),
    'duplicate.localhost'=>array('dbDatabase'=>'e998','dbHost'=>'127.0.0.1','dbUser'=>'local','dbPass'=>'fixture','pathRaiz'=>'/www/hardness3/','urlRaiz'=>'http://duplicate.localhost/'),
    'remote.localhost'=>array('dbDatabase'=>'remote','dbHost'=>'database.example.test','dbUser'=>'local','dbPass'=>'fixture','pathRaiz'=>'/www/hardness3/','urlRaiz'=>'http://remote.localhost/'),
);
file_put_contents($task5LocalProfiles, '<?php return ' . var_export($task5Profiles, true) . ';');
putenv('OBSERVABILITY_TEST_MODE=1');
$task5Tenants = new SiteTenantProvider($task5Fixture . '/sites', $task5LocalProfiles);
$task5Schemas = (new MysqlRecorderSchemaProvider($task5Tenants))->forServer(array('database'=>array('host'=>'127.0.0.1','port'=>3306)));
observationAssert($task5Schemas === array('e229', 'e998'), 'local inventory must add only unique loopback schemas for the central endpoint');
observationAssert($task5Tenants->resolve('LOCALHOST:8080')['database']['database'] === 'e998', 'local inventory must resolve exact local hosts without weakening path validation');
$task5Outside = $task5Fixture . '/outside.php'; file_put_contents($task5Outside, '<?php return ' . var_export($task5Profiles, true) . ';');
$task5Symlink = $task5Fixture . '/ambientes-link.php'; symlink($task5Outside, $task5Symlink);
$task5SymlinkTenants = new SiteTenantProvider($task5Fixture . '/sites', $task5Symlink);
observationAssert($task5SymlinkTenants->all() === array(), 'local inventory must refuse symlinks');
putenv('OBSERVABILITY_TEST_MODE');

foreach (array('INSERT ', 'UPDATE ', 'DELETE ', 'ALTER ', 'DROP ', 'TRUNCATE ', 'KILL ', 'FLUSH ', 'SET GLOBAL') as $mutable) {
    observationAssert(stripos($task5Source . $task5Cli, $mutable) === false, 'Task 5 must remain MySQL read-only: ' . $mutable);
}
observationAssert(strpos($task5DemoCli, 'configuracoes/local/ambientes.php') !== false && strpos($task5DemoCli, 'conexão central não local') !== false, 'synthetic preview must fail closed outside the explicit local environment');
observationAssert(strpos($task5DemoCli, "'synthetic'=>true") !== false && strpos($task5DemoCli, "MysqlRecorderStorage") !== false, 'synthetic preview must mark every generated dataset and use the recorder storage safeguards');

echo "Observability MySQL recorder Task 5 fixtures passed\n";
