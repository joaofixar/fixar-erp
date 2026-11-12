<?php
declare(strict_types=1);

namespace Hardness\Observability\Collectors\Mysql;

use Hardness\Observability\Severity\Severity;
use Hardness\Observability\Severity\SeverityEvaluator;

/** Consultas fixas, individuais e exclusivamente de leitura para MySQL 5.7/8.x. */
final class MysqlObservationCollector
{
    private $severity;
    private $thresholds;

    public function __construct(SeverityEvaluator $severity, array $thresholds)
    {
        $this->severity = $severity;
        $this->thresholds = $thresholds;
    }

    public function collect(array $tenant, bool $restrictToTenant = false): array
    {
        $connection = $this->connect($tenant);
        if ($connection === null) { return array('available' => false, 'label' => (string) $tenant['tenant_label'], 'processes' => array()); }
        try {
            $version = $this->scalar($connection, 'SELECT VERSION()');
            $status = $this->status($connection);
            $maxConnections = (int) $this->scalar($connection, 'SELECT @@max_connections');
            // No modo central, somar todos os schemas pode levar dezenas de
            // segundos em servidores grandes; não bloqueia o snapshot por isso.
            $size = !empty($tenant['central']) ? null : $this->scalar($connection, 'SELECT COALESCE(SUM(data_length + index_length), 0) FROM information_schema.tables WHERE table_schema = DATABASE()');
            $ownId = (int) $this->scalar($connection, 'SELECT CONNECTION_ID()');
            $processes = $this->processes($connection, $ownId, (string) $tenant['tenant_label'], $restrictToTenant ? (string) $tenant['database']['database'] : null, $this->lockRelations($connection, (string) $version));
            $tenantConnections = count($processes);
            $tenantRunning = count(array_filter($processes, function (array $process): bool { return $process['category'] === 'query'; }));
            return array(
                'available' => true,
                'label' => (string) $tenant['tenant_label'],
                'version' => is_string($version) ? $version : 'Indisponível',
                'connections' => $restrictToTenant ? $tenantConnections : (int) ($status['Threads_connected'] ?? 0),
                'running' => $restrictToTenant ? $tenantRunning : (int) ($status['Threads_running'] ?? 0),
                'max_connections' => $restrictToTenant ? 0 : $maxConnections,
                'connection_percent' => $restrictToTenant ? null : ($maxConnections > 0 ? 100.0 * ((int) ($status['Threads_connected'] ?? 0) / $maxConnections) : null),
                'uptime' => (int) ($status['Uptime'] ?? 0),
                'slow_queries' => $restrictToTenant ? null : (int) ($status['Slow_queries'] ?? 0),
                'size_bytes' => is_numeric($size) ? (float) $size : null,
                'processes' => $processes,
            );
        } finally { $connection->close(); }
    }

    private function connect(array $tenant): ?\mysqli
    {
        $db = isset($tenant['database']) && is_array($tenant['database']) ? $tenant['database'] : array();
        if (!isset($db['host'], $db['username'], $db['password'], $db['database'])) { return null; }
        $connection = @mysqli_init();
        if (!$connection) { return null; }
        // Um tenant indisponível não pode atrasar a coleta de todos os clientes.
        if (defined('MYSQLI_OPT_CONNECT_TIMEOUT')) { $connection->options(MYSQLI_OPT_CONNECT_TIMEOUT, 1); }
        if (defined('MYSQLI_OPT_READ_TIMEOUT')) { $connection->options(MYSQLI_OPT_READ_TIMEOUT, 2); }
        if (!@$connection->real_connect((string) $db['host'], (string) $db['username'], (string) $db['password'], (string) $db['database'], isset($db['port']) ? (int) $db['port'] : 3306)) { return null; }
        $connection->set_charset('utf8mb4');
        return $connection;
    }

    private function scalar(\mysqli $connection, string $sql)
    {
        $result = $connection->query($sql);
        if (!$result instanceof \mysqli_result) { return null; }
        $row = $result->fetch_row(); $result->free();
        return is_array($row) && array_key_exists(0, $row) ? $row[0] : null;
    }

    private function status(\mysqli $connection): array
    {
        $result = $connection->query("SHOW GLOBAL STATUS WHERE Variable_name IN ('Threads_connected', 'Threads_running', 'Uptime', 'Slow_queries')");
        if (!$result instanceof \mysqli_result) { return array(); }
        $values = array();
        while ($row = $result->fetch_assoc()) { $values[(string) $row['Variable_name']] = (string) $row['Value']; }
        $result->free(); return $values;
    }

    private function processes(\mysqli $connection, int $ownId, string $tenantLabel, ?string $database, array $lockRelations): array
    {
        $result = $connection->query('SHOW FULL PROCESSLIST');
        if (!$result instanceof \mysqli_result) { return array(); }
        $rows = array();
        while ($row = $result->fetch_assoc()) {
            if ((int) $row['Id'] === $ownId) { continue; }
            if ($database !== null && (!isset($row['db']) || $row['db'] === null || !hash_equals($database, (string) $row['db']))) { continue; }
            $state = isset($row['State']) && $row['State'] !== null ? (string) $row['State'] : '—';
            $seconds = isset($row['Time']) ? (int) $row['Time'] : 0;
            $command = (string) $row['Command'];
            $category = $command === 'Query' ? 'query' : ($command === 'Sleep' ? 'sleep' : 'other');
            $waitingForLock = $category === 'query' && stripos($state, 'lock') !== false;
            $sql = isset($row['Info']) && $row['Info'] !== null ? trim((string) $row['Info']) : '';
            $rows[] = array(
                'id' => (int) $row['Id'], 'tenant' => $tenantLabel, 'user' => (string) $row['User'],
                'host' => (string) $row['Host'], 'database' => isset($row['db']) && $row['db'] !== null ? (string) $row['db'] : '—',
                'time' => $seconds, 'command' => $command, 'category' => $category, 'state' => $state,
                'lock' => $waitingForLock, 'summary' => $this->summary($sql, $command), 'sql' => $sql,
                'tables' => $this->tables($sql), 'lock_details' => $lockRelations[(int) $row['Id']] ?? array(), 'lock_candidates' => array(),
                'severity' => $category === 'query' ? $this->severity->process($seconds, $waitingForLock, $this->thresholds) : Severity::NORMAL,
            );
        }
        $result->free();
        $rows = $this->withLockCandidates($rows);
        usort($rows, function (array $left, array $right): int { return $right['time'] <=> $left['time']; });
        return $rows;
    }

    /**
     * Correlaciona esperas de lock InnoDB com a thread que as bloqueia.
     * O performance_schema pode não estar disponível ou o usuário pode não ter
     * permissão: nesses casos a coleta principal continua normalmente.
     */
    private function lockRelations(\mysqli $connection, string $version): array
    {
        $sql = preg_match('/^(?:8\.|9\.)/', $version) === 1
            ? "SELECT waiting_thread.PROCESSLIST_ID AS waiting_process_id, blocking_thread.PROCESSLIST_ID AS blocking_process_id, CONCAT(IFNULL(requested_lock.OBJECT_SCHEMA, ''), IF(requested_lock.OBJECT_SCHEMA IS NULL OR requested_lock.OBJECT_SCHEMA = '', '', '.'), IFNULL(requested_lock.OBJECT_NAME, '')) AS locked_object, IFNULL(requested_lock.INDEX_NAME, '') AS locked_index FROM performance_schema.data_lock_waits AS waits INNER JOIN performance_schema.threads AS waiting_thread ON waiting_thread.THREAD_ID = waits.REQUESTING_THREAD_ID INNER JOIN performance_schema.threads AS blocking_thread ON blocking_thread.THREAD_ID = waits.BLOCKING_THREAD_ID LEFT JOIN performance_schema.data_locks AS requested_lock ON requested_lock.ENGINE_LOCK_ID = waits.REQUESTING_ENGINE_LOCK_ID AND requested_lock.ENGINE = waits.ENGINE"
            : "SELECT waiting_transaction.trx_mysql_thread_id AS waiting_process_id, blocking_transaction.trx_mysql_thread_id AS blocking_process_id, REPLACE(IFNULL(requested_lock.lock_table, ''), '`', '') AS locked_object, IFNULL(requested_lock.lock_index, '') AS locked_index FROM information_schema.innodb_lock_waits AS waits INNER JOIN information_schema.innodb_trx AS waiting_transaction ON waiting_transaction.trx_id = waits.requesting_trx_id INNER JOIN information_schema.innodb_trx AS blocking_transaction ON blocking_transaction.trx_id = waits.blocking_trx_id LEFT JOIN information_schema.innodb_locks AS requested_lock ON requested_lock.lock_id = waits.requested_lock_id";
        $result = @$connection->query($sql);
        if (!$result instanceof \mysqli_result) { return array(); }
        $relations = array();
        while ($row = $result->fetch_assoc()) {
            $waiting = (int) ($row['waiting_process_id'] ?? 0);
            $blocking = (int) ($row['blocking_process_id'] ?? 0);
            if ($waiting <= 0 || $blocking <= 0) { continue; }
            $relations[$waiting][] = array(
                'blocking_process_id' => $blocking,
                'type' => 'InnoDB',
                'object' => (string) ($row['locked_object'] ?? ''),
                'index' => (string) ($row['locked_index'] ?? ''),
            );
        }
        $result->free();
        return $relations;
    }

    /**
     * SHOW PROCESSLIST não vincula locks de tabela (por exemplo MyISAM) ao
     * detentor. Para acelerar a triagem, sugere somente sessões ativas que
     * estavam há mais tempo em uma das mesmas tabelas. Não é uma confirmação.
     */
    private function withLockCandidates(array $processes): array
    {
        foreach ($processes as $index => $waiting) {
            if (empty($waiting['lock']) || !empty($waiting['lock_details']) || empty($waiting['tables'])) { continue; }
            $candidates = array();
            foreach ($processes as $candidate) {
                if ($candidate['id'] === $waiting['id'] || $candidate['category'] !== 'query' || !empty($candidate['lock']) || $candidate['time'] < $waiting['time']) { continue; }
                $shared = array_values(array_intersect($waiting['tables'], $candidate['tables']));
                if (!$shared) { continue; }
                $candidates[] = array('process_id' => $candidate['id'], 'tables' => $shared, 'time' => $candidate['time'], 'reason' => 'Query ativa há mais tempo na mesma tabela');
            }
            usort($candidates, function (array $left, array $right): int { return $right['time'] <=> $left['time']; });
            $processes[$index]['lock_candidates'] = array_slice($candidates, 0, 3);
        }
        return $processes;
    }

    private function tables(string $sql): array
    {
        if ($sql === '') { return array(); }
        if (preg_match_all('/\\b(?:FROM|JOIN|UPDATE|INTO|TABLE)\\s+([`a-zA-Z0-9_.$]+)/i', $sql, $matches) < 1) { return array(); }
        $tables = array();
        foreach ($matches[1] as $name) {
            $name = trim(str_replace('`', '', (string) $name));
            if ($name !== '' && !in_array($name, $tables, true)) { $tables[] = $name; }
        }
        return $tables;
    }

    private function summary(string $sql, string $command): string
    {
        if ($sql === '') { return $command === 'Sleep' ? 'Conexão ociosa' : 'Sem SQL em execução'; }
        $flat = preg_replace('/\s+/', ' ', $sql);
        return mb_strlen($flat) > 140 ? mb_substr($flat, 0, 137) . '…' : $flat;
    }
}
