#!/usr/bin/env php
<?php
declare(strict_types=1);

use Hardness\Observability\Collectors\Mysql\MysqlRecorderCollector;
use Hardness\Observability\Collectors\Mysql\MysqlRecorderRunner;
use Hardness\Observability\Collectors\Mysql\MysqlRecorderServerRunner;
use Hardness\Observability\Collectors\Mysql\MysqlRecorderSchemaProvider;
use Hardness\Observability\Collectors\Mysql\MysqlRecorderStorage;
use Hardness\Observability\Collectors\Mysql\MysqliRecorderQuerySource;
use Hardness\Observability\Config\ShowprocessConnectionProvider;

require dirname(__DIR__) . '/bootstrap/bootstrap.php';

if (PHP_SAPI !== 'cli') { fwrite(STDERR, "Este comando está disponível somente no terminal.\n"); exit(2); }

$options = getopt('', array('database:', 'all-schemas', 'long-query-seconds::', 'long-transaction-seconds::', 'dry-run', 'record', 'once', 'duration::', 'interval::', 'retention-days::', 'help'));
if (isset($options['help'])) {
    fwrite(STDOUT, "Uso:\n  php observability/bin/mysql-recorder.php --all-schemas --dry-run\n  php observability/bin/mysql-recorder.php --all-schemas --record [--once] [--duration=50] [--interval=10] [--retention-days=7]\n  php observability/bin/mysql-recorder.php --database=e229 --dry-run|--record (compatibilidade/piloto restrito)\n");
    exit(0);
}
$dryRun = isset($options['dry-run']); $record = isset($options['record']);
if ($dryRun === $record) { fwrite(STDERR, "Escolha exatamente um modo: --dry-run ou --record.\n"); exit(2); }
$database = isset($options['database']) ? (string) $options['database'] : ''; $allSchemas=isset($options['all-schemas']);
if ($allSchemas === ($database !== '')) { fwrite(STDERR, "Escolha exatamente um escopo: --all-schemas ou --database.\n"); exit(2); }
if (!$allSchemas && preg_match('/^[A-Za-z0-9_]+$/', $database) !== 1) { fwrite(STDERR, "Informe um banco válido com --database.\n"); exit(2); }

$tenant = (new ShowprocessConnectionProvider())->tenant();
if ($tenant === null) { fwrite(STDERR, "Configuração local showprocess/confConnect.php indisponível.\n"); exit(3); }

$source = null;
try {
    $source = MysqliRecorderQuerySource::connect($tenant);
    $collector = new MysqlRecorderCollector($source);
    $longQuerySeconds = isset($options['long-query-seconds']) ? (int) $options['long-query-seconds'] : 15;
    $longTransactionSeconds = isset($options['long-transaction-seconds']) ? (int) $options['long-transaction-seconds'] : 15;
    if ($allSchemas) {
        $databases=(new MysqlRecorderSchemaProvider())->forServer($tenant);
        if ($dryRun) { $events=$collector->collectFastServer($databases,$longQuerySeconds,$longTransactionSeconds);$status=$collector->collectStatus();$digests=$collector->collectDigestsServer($databases);$result=array('scope'=>'server','schemas'=>$databases,'partial'=>!empty($events['collection']['partial'])||!empty($status['collection']['partial'])||!empty($digests['collection']['partial']),'events'=>$events,'status'=>$status,'digests'=>$digests); }
        else { $result=(new MysqlRecorderServerRunner($collector,new MysqlRecorderStorage()))->run($databases,isset($options['once']),isset($options['duration'])?(int)$options['duration']:50,isset($options['interval'])?(int)$options['interval']:10,$longQuerySeconds,$longTransactionSeconds,isset($options['retention-days'])?(int)$options['retention-days']:7); }
    } elseif ($dryRun) {
        $result = $collector->collect($database, $longQuerySeconds, $longTransactionSeconds);
    } else {
        $runner = new MysqlRecorderRunner($collector, new MysqlRecorderStorage());
        $result = $runner->run(
            $database,
            isset($options['once']),
            isset($options['duration']) ? (int) $options['duration'] : 50,
            isset($options['interval']) ? (int) $options['interval'] : 10,
            $longQuerySeconds,
            $longTransactionSeconds,
            isset($options['retention-days']) ? (int) $options['retention-days'] : 7
        );
    }
    $json = json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if (!is_string($json)) { throw new RuntimeException('Não foi possível gerar o JSON da coleta.'); }
    fwrite(STDOUT, $json . PHP_EOL);
    if (isset($result['active']) && $result['active'] === false) { fwrite(STDERR, "Outra coleta deste banco já está ativa.\n"); exit(5); }
    $partial = isset($result['collection']) ? $result['collection']['partial'] : ($result['partial'] ?? false);
    exit($partial ? 4 : 0);
} catch (Throwable $exception) {
    $safeMessage = preg_replace('/(?:password|passwd|pwd)\s*[=:]\s*\S+/i', 'credential=[hidden]', $exception->getMessage());
    fwrite(STDERR, "Falha no gravador MySQL: " . substr((string) $safeMessage, 0, 300) . PHP_EOL);
    exit(3);
} finally {
    if ($source !== null) { $source->close(); }
}
