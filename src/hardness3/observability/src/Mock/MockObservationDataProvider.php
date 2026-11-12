<?php
declare(strict_types=1);

namespace Hardness\Observability\Mock;

use Hardness\Observability\Context\ObservationContextProvider;
use Hardness\Observability\Diagnostics\DiagnosticEngine;
use Hardness\Observability\Severity\PrototypeThresholds;
use Hardness\Observability\Severity\Severity;
use Hardness\Observability\Severity\SeverityEvaluator;

/** Fonte única da Etapa 3.1: cenários fictícios, sem fallback ou coleta real. */
final class MockObservationDataProvider
{
    private $contexts;
    private $severity;
    private $diagnostics;

    public function __construct(ObservationContextProvider $contexts, ?SeverityEvaluator $severity = null)
    {
        $this->contexts = $contexts;
        $this->severity = $severity ?: new SeverityEvaluator();
        $this->diagnostics = new DiagnosticEngine();
    }

    public function snapshot(): array
    {
        $context = $this->contexts->current()->toArray();
        $scenario = $this->scenario();
        $metrics = $this->metrics($scenario);
        $processes = $this->processes($context, $scenario);
        $server = $this->server($metrics);
        $apachePhp = $this->apachePhp($scenario);
        $database = $this->database($context, $scenario);
        $diagnostics = $this->diagnostics->evaluate($metrics, $processes, $apachePhp, $database);
        $severities = array_map(function (array $item): string { return $item['severity']; }, $metrics);
        foreach ($processes as $process) {
            $severities[] = $process['severity'];
        }

        return array(
            'prototype' => true,
            'scenario' => $scenario,
            'generatedAt' => gmdate('c'),
            'context' => $context,
            'thresholds' => array(
                'cpu' => array('attention' => PrototypeThresholds::CPU_ATTENTION_PERCENT, 'critical' => PrototypeThresholds::CPU_CRITICAL_PERCENT),
                'memory' => array('attention' => PrototypeThresholds::MEMORY_ATTENTION_PERCENT, 'critical' => PrototypeThresholds::MEMORY_CRITICAL_PERCENT),
                'disk' => array('attention' => PrototypeThresholds::DISK_ATTENTION_PERCENT, 'critical' => PrototypeThresholds::DISK_CRITICAL_PERCENT),
                'processSeconds' => array('attention' => PrototypeThresholds::PROCESS_ATTENTION_SECONDS, 'critical' => PrototypeThresholds::PROCESS_CRITICAL_SECONDS),
            ),
            'overall' => array('severity' => $this->severity->aggregate($severities), 'counts' => $this->severity->counts($severities)),
            'navigation' => $this->navigation($metrics, $processes, $diagnostics),
            'overview' => array('metrics' => $metrics, 'processSummary' => $this->processSummary($processes), 'diagnostics' => array_slice($diagnostics, 0, 3)),
            'server' => $server,
            'apachePhp' => $apachePhp,
            'database' => $database,
            'processes' => $processes,
            'diagnostics' => $diagnostics,
            // A qualidade de rede é calculada no navegador, a partir da sonda HTTP interna.
            'network' => array('severity' => Severity::UNKNOWN, 'timings' => array(), 'note' => 'Medições realizadas pelo navegador nesta sessão.'),
        );
    }

    private function scenario(): string
    {
        $allowed = array('normal', 'memory_attention', 'cpu_critical', 'lock_critical', 'apache_unavailable', 'database_unavailable', 'no_queries', 'temporary_error');
        $value = getenv('OBSERVABILITY_MOCK_SCENARIO');
        return in_array($value, $allowed, true) ? $value : 'lock_critical';
    }

    private function metrics(string $scenario): array
    {
        $cpu = $scenario === 'cpu_critical' ? 94 : 34;
        $memory = $scenario === 'memory_attention' ? 76 : 58;
        $apacheSeverity = $scenario === 'apache_unavailable' ? Severity::UNAVAILABLE : Severity::NORMAL;
        $databaseSeverity = ($scenario === 'database_unavailable' || $scenario === 'temporary_error') ? Severity::UNAVAILABLE : Severity::NORMAL;
        return array(
            $this->metric('CPU', $cpu . '%', $this->severity->percentage($cpu, PrototypeThresholds::CPU_ATTENTION_PERCENT, PrototypeThresholds::CPU_CRITICAL_PERCENT), 'Uso simulado do processador', '/server'),
            $this->metric('Memória', $memory . '%', $this->severity->percentage($memory, PrototypeThresholds::MEMORY_ATTENTION_PERCENT, PrototypeThresholds::MEMORY_CRITICAL_PERCENT), 'Uso simulado de memória', '/server'),
            $this->metric('Disco', '42%', $this->severity->percentage(42, PrototypeThresholds::DISK_ATTENTION_PERCENT, PrototypeThresholds::DISK_CRITICAL_PERCENT), 'Uso simulado do volume', '/server'),
            $this->metric('Apache', $apacheSeverity === Severity::UNAVAILABLE ? 'Indisponível' : '14 workers', $apacheSeverity, $apacheSeverity === Severity::UNAVAILABLE ? 'Coletor simulado indisponível' : 'Workers simulados disponíveis', '/apache-php'),
            $this->metric('PHP', '12 processos', Severity::NORMAL, 'Execução simulada', '/apache-php'),
            $this->metric('Banco', $databaseSeverity === Severity::UNAVAILABLE ? 'Indisponível' : '19 conexões', $databaseSeverity, $databaseSeverity === Severity::UNAVAILABLE ? 'Coletor simulado indisponível' : 'Conexões simuladas', '/database'),
        );
    }

    private function metric(string $label, string $value, string $severity, string $detail, string $link): array
    {
        $actions = array(
            '/server' => 'Abrir servidor',
            '/apache-php' => 'Abrir Apache e PHP',
            '/database' => 'Abrir banco de dados',
        );
        return array('label' => $label, 'value' => $value, 'severity' => $severity, 'detail' => $detail, 'available' => $severity !== Severity::UNAVAILABLE, 'link' => $link, 'actionLabel' => isset($actions[$link]) ? $actions[$link] : '');
    }

    private function processes(array $context, string $scenario): array
    {
        if ($scenario === 'no_queries' || $scenario === 'temporary_error') {
            return array();
        }
        $rows = array(
            array('id' => 401, 'tenant' => 'Ambiente A', 'user' => 'mock_user', 'time' => $scenario === 'lock_critical' ? 18 : 2, 'command' => 'Query', 'state' => $scenario === 'lock_critical' ? 'Waiting for lock' : 'Executing', 'lock' => $scenario === 'lock_critical', 'summary' => 'SELECT … WHERE item_key = ?', 'sql' => "SELECT item_id, item_state\nFROM mock_items\nWHERE item_key = ?;", 'tables' => array('mock_items'), 'lock_details' => $scenario === 'lock_critical' ? array(array('blocking_process_id' => 402, 'type' => 'InnoDB', 'object' => 'mock.mock_items', 'index' => 'PRIMARY')) : array(), 'lock_candidates' => array()),
            array('id' => 402, 'tenant' => 'Ambiente B', 'user' => 'mock_worker', 'time' => $scenario === 'normal' ? 2 : 6, 'command' => 'Query', 'state' => 'Sending data', 'lock' => false, 'summary' => 'SELECT … FROM mock_events', 'sql' => "SELECT event_id, event_label\nFROM mock_events\nWHERE active = ?;", 'tables' => array('mock_events')),
            array('id' => 403, 'tenant' => 'Ambiente C', 'user' => 'mock_service', 'time' => 1, 'command' => 'Sleep', 'state' => 'Idle', 'lock' => false, 'summary' => 'Conexão ociosa', 'sql' => '', 'tables' => array()),
        );
        foreach ($rows as $index => $row) {
            $rows[$index]['severity'] = $this->severity->process($row['time'], $row['lock']);
            if ($context['role'] !== 'hardness_internal') {
                $rows[$index]['tenant'] = 'Ambiente atual simulado';
            }
        }
        return $rows;
    }

    private function processSummary(array $processes): array
    {
        $running = count($processes);
        $locks = count(array_filter($processes, function (array $row): bool { return $row['lock']; }));
        $longest = 0;
        foreach ($processes as $row) { $longest = max($longest, $row['time']); }
        return array('activeProcesses' => $running + 4, 'runningQueries' => $running, 'locks' => $locks, 'longestSeconds' => $longest);
    }

    private function serverMetrics(array $metrics): array
    {
        return array_merge($metrics, array(
            $this->metric('Load average', '0,74', Severity::NORMAL, 'Média simulada', '/server'),
            $this->metric('Swap', '2%', Severity::NORMAL, 'Uso simulado', '/server'),
            $this->metric('Inodes', '29%', Severity::NORMAL, 'Uso simulado', '/server'),
            $this->metric('Uptime', '12 dias', Severity::NORMAL, 'Tempo simulado', '/server'),
            $this->metric('Rede', '2,4 MB/s', Severity::NORMAL, 'Entrada e saída simuladas', '/network'),
            $this->metric('TCP', '31 conexões', Severity::NORMAL, 'Estado simulado', '/network'),
        ));
    }

    private function server(array $metrics): array
    {
        // Variação determinística por janela de polling: demonstra atualização contínua sem fonte real.
        $sample = (int) floor(time() / 5);
        $webCpu = 14 + ($sample % 9);
        $dbCpu = 10 + (($sample * 3) % 8);
        $rows = array(
            array('name' => 'mock-web-worker', 'type' => 'Aplicação', 'cpu' => $webCpu . '%', 'memory' => (8 + ($sample % 3)) . ',2%', 'runtime' => '18 min', 'severity' => Severity::NORMAL),
            array('name' => 'mock-db-worker', 'type' => 'Banco', 'cpu' => $dbCpu . '%', 'memory' => '7,8%', 'runtime' => '2 h 14 min', 'severity' => $dbCpu >= 15 ? Severity::ATTENTION : Severity::NORMAL),
            array('name' => 'mock-job-runner', 'type' => 'Tarefa', 'cpu' => (5 + ($sample % 6)) . '%', 'memory' => '5,1%', 'runtime' => '43 min', 'severity' => Severity::NORMAL),
            array('name' => 'mock-cache-service', 'type' => 'Serviço', 'cpu' => '3%', 'memory' => '4,4%', 'runtime' => '12 dias', 'severity' => Severity::NORMAL),
        );
        if ($sample % 2 === 0) {
            $rows[] = array('name' => 'mock-short-task', 'type' => 'Tarefa', 'cpu' => '6%', 'memory' => '1,6%', 'runtime' => '2 min', 'severity' => Severity::NORMAL);
        }
        usort($rows, function (array $left, array $right): int {
            return (int) rtrim($right['cpu'], '%') <=> (int) rtrim($left['cpu'], '%');
        });
        return array(
            'metrics' => $this->serverMetrics($metrics),
            'processes' => $rows,
            'io' => array('read' => (1 + ($sample % 3)) . ',8 MB/s', 'write' => (600 + (($sample % 4) * 80)) . ' KB/s', 'wait' => (1 + ($sample % 3)) . ',1%', 'severity' => Severity::NORMAL),
            'memoryBreakdown' => array('applications' => (40 + ($sample % 4)) . '%', 'cache' => '15%', 'free' => (41 - ($sample % 4)) . '%', 'swap' => '2%'),
            'correlations' => array('Amostra simulada atualizada automaticamente a cada 5 segundos.', 'Não há pressão de swap neste cenário.', 'O processo mock-db-worker merece acompanhamento por consumo sustentado.'),
        );
    }

    private function apachePhp(string $scenario): array
    {
        $unavailable = $scenario === 'apache_unavailable';
        return array('severity' => $unavailable ? Severity::UNAVAILABLE : Severity::NORMAL, 'mode' => 'mod_php simulado', 'version' => 'Versão simulada', 'details' => array(
            array('label' => 'Workers Apache', 'value' => $unavailable ? 'Indisponível' : '14 de 32', 'severity' => $unavailable ? Severity::UNAVAILABLE : Severity::NORMAL, 'available' => !$unavailable),
            array('label' => 'Fila PHP-FPM', 'value' => 'Não aplicável', 'severity' => Severity::UNAVAILABLE, 'available' => false),
            array('label' => 'Processos PHP', 'value' => '12', 'severity' => Severity::NORMAL, 'available' => true),
            array('label' => 'OPcache', 'value' => $unavailable ? 'Indisponível' : 'Ativo', 'severity' => $unavailable ? Severity::UNAVAILABLE : Severity::NORMAL, 'available' => !$unavailable),
            array('label' => 'APCu', 'value' => $unavailable ? 'Indisponível' : 'Ativo', 'severity' => $unavailable ? Severity::UNAVAILABLE : Severity::NORMAL, 'available' => !$unavailable),
        ));
    }

    private function database(array $context, string $scenario): array
    {
        $unavailable = $scenario === 'database_unavailable';
        $database = array('severity' => $unavailable ? Severity::UNAVAILABLE : Severity::NORMAL, 'product' => $unavailable ? 'Indisponível' : 'Motor simulado', 'version' => $unavailable ? 'Indisponível' : 'Versão simulada', 'connections' => $unavailable ? 'Indisponível' : '19', 'details' => array('threads' => $unavailable ? 'Indisponível' : '8', 'slowQueries' => '2', 'locks' => '1'));
        if ($context['role'] === 'hardness_internal') {
            $database['tenants'] = array(array('label' => 'Ambiente A', 'connections' => 7, 'severity' => Severity::NORMAL), array('label' => 'Ambiente B', 'connections' => 8, 'severity' => Severity::ATTENTION), array('label' => 'Ambiente C', 'connections' => 4, 'severity' => Severity::NORMAL));
        }
        return $database;
    }

    private function navigation(array $metrics, array $processes, array $diagnostics): array
    {
        $processLevels = array_map(function (array $row): string { return $row['severity']; }, $processes);
        $diagnosticLevels = array_map(function (array $row): string { return $row['severity']; }, $diagnostics);
        return array(
            'overview' => $this->severity->aggregate(array_map(function (array $row): string { return $row['severity']; }, $metrics)),
            'server' => $this->severity->aggregate(array($metrics[0]['severity'], $metrics[1]['severity'], $metrics[2]['severity'])),
            'apache-php' => $metrics[3]['severity'],
            'database' => $metrics[5]['severity'],
            'processes' => $this->severity->aggregate($processLevels),
            'diagnostics' => $this->severity->aggregate($diagnosticLevels),
            'network' => Severity::NORMAL,
        );
    }
}
