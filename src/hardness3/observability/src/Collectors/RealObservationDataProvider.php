<?php
declare(strict_types=1);

namespace Hardness\Observability\Collectors;

use Hardness\Observability\Collectors\Apache\ApachePhpCollector;
use Hardness\Observability\Collectors\Linux\LinuxServerCollector;
use Hardness\Observability\Collectors\Linux\LinuxProcessCollector;
use Hardness\Observability\Collectors\Mysql\MysqlObservationCollector;
use Hardness\Observability\Config\SiteTenantProvider;
use Hardness\Observability\Config\ShowprocessConnectionProvider;
use Hardness\Observability\Support\DiagnosticHistory;
use Hardness\Observability\Context\ObservationContextProvider;
use Hardness\Observability\Diagnostics\DiagnosticEngine;
use Hardness\Observability\Severity\Severity;
use Hardness\Observability\Severity\SeverityEvaluator;
use Hardness\Observability\Support\SnapshotCache;

final class RealObservationDataProvider
{
    private $contexts;
    private $tenants;
    private $severity;
    private $thresholds;
    private $linux;
    private $linuxProcesses;
    private $apachePhp;
    private $mysql;
    private $diagnostics;
    private $cache;
    private $showprocessConnection;
    private $diagnosticHistory;

    public function __construct(ObservationContextProvider $contexts, SiteTenantProvider $tenants)
    {
        $this->contexts = $contexts; $this->tenants = $tenants; $this->severity = new SeverityEvaluator();
        $this->thresholds = CollectorThresholds::load();
        $this->linux = new LinuxServerCollector(); $this->linuxProcesses = new LinuxProcessCollector(); $this->apachePhp = new ApachePhpCollector();
        $this->mysql = new MysqlObservationCollector($this->severity, $this->thresholds);
        $this->diagnostics = new DiagnosticEngine();
        $this->cache = new SnapshotCache();
        $this->showprocessConnection = new ShowprocessConnectionProvider(); $this->diagnosticHistory = new DiagnosticHistory();
    }

    public function snapshot(): array
    {
        $context = $this->contexts->current()->toArray();
        $cacheKey = $this->cacheKey($context);
        $cached = $this->cache->get($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        $server = $this->linux->collect($this->thresholds['disk_path']); $server['process_data']=$this->linuxProcesses->collect(); $apachePhp = $this->apachePhp->collect();
        $databases = $this->databases($context);
        $database = $this->databaseSummary($databases, $context);
        $processes = array(); foreach ($databases as $item) { $processes = array_merge($processes, $item['processes']); }
        $metrics = $this->overviewMetrics($server, $apachePhp, $database);
        $diagnostics = $this->diagnostics->evaluate($metrics, $processes, $apachePhp, $database);
        $activeDiagnostics = array_values(array_filter($diagnostics, function (array $item): bool { return isset($item['severity']) && $item['severity'] !== Severity::NORMAL; }));
        $diagnosticHistory = $this->diagnosticHistory->update($activeDiagnostics, (string) $context['role'] . '|' . (string) $context['scopeLabel']);
        $levels = array_map(function (array $item): string { return $item['severity']; }, $metrics);
        foreach ($processes as $process) { $levels[] = $process['severity']; }
        $snapshot = array(
            'prototype' => false, 'generatedAt' => date('c'), 'context' => $context,
            'thresholds' => array('cpu'=>array('attention'=>$this->thresholds['cpu_attention_percent'],'critical'=>$this->thresholds['cpu_critical_percent']), 'memory'=>array('attention'=>$this->thresholds['memory_attention_percent'],'critical'=>$this->thresholds['memory_critical_percent']), 'disk'=>array('attention'=>$this->thresholds['disk_attention_percent'],'critical'=>$this->thresholds['disk_critical_percent']), 'processSeconds'=>array('attention'=>$this->thresholds['attention_seconds'],'critical'=>$this->thresholds['critical_seconds'])),
            'overall' => array('severity'=>$this->severity->aggregate($levels), 'counts'=>$this->severity->counts($levels)),
            'navigation' => $this->navigation($metrics, $processes, $diagnostics),
            'overview' => array('metrics'=>$metrics, 'processSummary'=>$this->processSummary($processes, $database), 'diagnostics'=>array_slice($diagnostics,0,3)),
            'server' => $this->server($server, $metrics), 'apachePhp'=>$apachePhp, 'database'=>$database,
            'processes'=>$processes, 'diagnostics'=>$diagnosticHistory['active'], 'diagnosticHistory'=>array('recovering'=>$diagnosticHistory['recovering'], 'resolved'=>$diagnosticHistory['resolved'], 'persistenceAvailable'=>$diagnosticHistory['persistenceAvailable']),
            // Não há coletor de servidor: a tela mede a rota navegador → aplicação.
            'network'=>array('severity'=>Severity::UNKNOWN,'timings'=>array(),'note'=>'Medições realizadas pelo navegador nesta sessão.'),
        );
        // A coleta central usa uma conexão só; mantenha o processlist recente.
        $this->cache->put($cacheKey, $snapshot, 2);
        return $snapshot;
    }

    private function cacheKey(array $context): string
    {
        return 'hardness_observability_snapshot_' . hash('sha256', (string) $context['role'] . '|' . (string) $context['scopeLabel']);
    }

    private function databases(array $context): array
    {
        if ($context['role'] === 'hardness_internal') {
            $centralTenant = $this->showprocessConnection->tenant();
            if ($centralTenant !== null) {
                return array($this->mysql->collect($centralTenant));
            }
            $tenants = $this->tenants->all();
        } else {
            $tenants = array($this->tenantForCurrentHost());
        }
        $unique = array(); $results = array();
        foreach ($tenants as $tenant) {
            if (!is_array($tenant) || !isset($tenant['database'], $tenant['tenant_label'])) { continue; }
            $database = $tenant['database'];
            $key = implode('|', array((string) ($database['host'] ?? ''), (string) ($database['port'] ?? 3306), (string) ($database['database'] ?? ''), (string) ($database['username'] ?? '')));
            if (isset($unique[$key])) { continue; } $unique[$key] = true;
            $results[] = $this->mysql->collect($tenant, $context['role'] !== 'hardness_internal');
        }
        return $results;
    }

    private function tenantForCurrentHost(): array
    {
        return $this->tenants->resolve((string) ($_SERVER['HTTP_HOST'] ?? ''));
    }

    private function overviewMetrics(array $server, array $apachePhp, array $database): array
    {
        $cpu = $this->percentageMetric('CPU', $server['cpu_percent'], 'Uso do processador', '/server', 'cpu_attention_percent', 'cpu_critical_percent');
        $memory = $this->percentageMetric('Memória', $server['memory_percent'], 'Memória efetivamente em uso', '/server', 'memory_attention_percent', 'memory_critical_percent');
        $disk = $this->percentageMetric('Disco', $server['disk_percent'], 'Uso do volume raiz', '/server', 'disk_attention_percent', 'disk_critical_percent');
        return array($cpu, $memory, $disk,
            $this->metric('Apache', (string) $apachePhp['mode'], (string) $apachePhp['severity'], 'Versão e runtime do servidor web', '/apache-php'),
            $this->metric('PHP', PHP_VERSION, Severity::NORMAL, 'Versão e configuração ativa', '/apache-php'),
            $this->metric('Banco', $database['connections'], $database['severity'], 'Conexões abertas no MySQL', '/database')
        );
    }

    private function percentageMetric(string $label, ?float $value, string $detail, string $link, string $attention, string $critical): array
    {
        return $this->metric($label, $value === null ? 'Indisponível' : number_format($value, 1, ',', '') . '%', $value === null ? Severity::UNAVAILABLE : $this->severity->percentage($value, $this->thresholds[$attention], $this->thresholds[$critical]), $detail, $link);
    }

    private function metric(string $label, string $value, string $severity, string $detail, string $link): array
    {
        $actions = array('/server'=>'Abrir servidor','/apache-php'=>'Abrir Apache e PHP','/database'=>'Abrir banco de dados');
        return array('label'=>$label,'value'=>$value,'severity'=>$severity,'detail'=>$detail,'available'=>$severity!==Severity::UNAVAILABLE,'link'=>$link,'actionLabel'=>$actions[$link]);
    }

    private function databaseSummary(array $items, array $context): array
    {
        $available = array_values(array_filter($items, function (array $item): bool { return $item['available']; }));
        $processes = array(); foreach ($available as $item) { $processes=array_merge($processes,$item['processes']); }
        $severity = $available === array() ? Severity::UNAVAILABLE : $this->severity->aggregate(array_map(function (array $item): string { return $this->severity->aggregate(array($this->connectionSeverity($item), $this->processSeverity($item['processes']))); }, $available));
        $first = isset($available[0]) ? $available[0] : null;
        $totalConnections=array_sum(array_column($available,'connections')); $maxConnections=array_sum(array_column($available,'max_connections'));
        $tenantScoped = $context['role'] !== 'hardness_internal';
        $summary = array('severity'=>$severity, 'product'=>$first ? 'MySQL' : 'Indisponível', 'version'=>$first ? $first['version'] : 'Indisponível', 'connections'=>$first ? (string) $totalConnections : 'Indisponível', 'details'=>array('threads'=>$first ? (string) array_sum(array_column($available,'running')) : 'Indisponível','slowQueries'=>$tenantScoped ? 'Indisponível' : ($first ? (string) array_sum(array_column($available,'slow_queries')) : 'Indisponível'),'locks'=>(string) count(array_filter($processes,function(array $p):bool{return $p['lock'];})),'maxConnections'=>$tenantScoped ? 'Indisponível' : ($first ? (string)$maxConnections:'Indisponível'),'connectionUsage'=>$first&&$maxConnections>0&&!$tenantScoped?number_format(100*$totalConnections/$maxConnections,1,',','').'%':'Indisponível','uptime'=>$first ? $this->duration($first['uptime']) : 'Indisponível','size'=>$first ? $this->bytes(array_sum(array_column($available,'size_bytes'))) : 'Indisponível'));
        if ($context['role'] === 'hardness_internal') { $summary['tenants']=array_map(function(array $item):array{return array('label'=>$item['label'],'connections'=>$item['available']?$item['connections']:'—','severity'=>$item['available']?$this->processSeverity($item['processes']):Severity::UNAVAILABLE);},$items); }
        return $summary;
    }

    private function processSeverity(array $processes): string { $active=array_values(array_filter($processes,function(array $p):bool{return $p['category']==='query';})); return $active === array() ? Severity::NORMAL : $this->severity->aggregate(array_map(function(array $p):string{return $p['severity'];},$active)); }
    private function connectionSeverity(array $item): string { return $item['connection_percent'] === null ? Severity::NORMAL : $this->severity->percentage((float)$item['connection_percent'],$this->thresholds['mysql_connections_attention_percent'],$this->thresholds['mysql_connections_critical_percent']); }
    private function processSummary(array $processes,array $database): array { $queries=array_values(array_filter($processes,function(array $p):bool{return $p['category']==='query';}));$longest=null;foreach($queries as $p){$longest=$longest===null?$p['time']:max($longest,$p['time']);}return array('totalConnections'=>$database['connections'],'idleConnections'=>count(array_filter($processes,function(array $p):bool{return $p['category']==='sleep';})),'runningQueries'=>count($queries),'locks'=>count(array_filter($queries,function(array $p):bool{return $p['lock'];})),'longestSeconds'=>$longest); }
    private function server(array $server, array $metrics): array { $p=$server['process_data']; return array('metrics'=>array_merge($metrics,array($this->metric('Hostname',$server['hostname'],Severity::NORMAL,'Identificação do servidor atual','/server'),$this->metric('Uptime',$server['uptime_seconds']===null?'Indisponível':$this->duration($server['uptime_seconds']),$server['uptime_seconds']===null?Severity::UNAVAILABLE:Severity::NORMAL,'Tempo desde a inicialização','/server'),$this->metric('Load average',$server['load']===null?'Indisponível':implode(' · ',array_map(function($v){return number_format($v,2,',','');},$server['load'])),$server['load']===null?Severity::UNAVAILABLE:Severity::NORMAL,'Médias de 1, 5 e 15 minutos','/server'),$this->metric('Horário do servidor',$server['time'],Severity::NORMAL,'Data, hora e fuso configurados','/server'))),'memoryBreakdown'=>array('applications'=>'Não coletado','cache'=>'Não coletado','free'=>$server['memory_total']&&$server['memory_available']?$this->bytes($server['memory_available']):'Indisponível','swap'=>number_format((float)$server['swap_percent'],1,',','').'%'),'io'=>array('read'=>$p['io']['available']?'Contadores disponíveis':'Indisponível','write'=>'Leitura /proc/diskstats','wait'=>$p['pressure']['available']?number_format($p['pressure']['memory_avg10'],2,',','').'% pressão memória':'Indisponível','severity'=>Severity::NORMAL),'processes'=>$p['rows'],'processCounts'=>$p['counts'],'correlations'=>array('Memória em uso = (MemTotal − MemAvailable) / MemTotal.','Swap é exibida separadamente e não compõe a utilização de RAM.','Processos Linux: amostra local de /proc, somente leitura.')); }
    private function navigation(array $metrics,array $processes,array $diagnostics):array { $metricLevels=array_map(function(array $m):string{return $m['severity'];},$metrics);return array('overview'=>$this->severity->aggregate($metricLevels),'server'=>$this->severity->aggregate(array_slice($metricLevels,0,3)),'apache-php'=>$this->severity->aggregate(array_slice($metricLevels,3,2)),'database'=>$metricLevels[5],'processes'=>$this->processSeverity($processes),'diagnostics'=>$this->severity->aggregate(array_map(function(array $d):string{return $d['severity'];},$diagnostics)),'network'=>Severity::UNKNOWN); }
    private function bytes(?float $bytes): string { if($bytes===null){return 'Indisponível';}$units=array('B','KB','MB','GB','TB');$i=0;while($bytes>=1024&&$i<count($units)-1){$bytes/=1024;$i++;}return number_format($bytes,$i===0?0:1,',','.').' '.$units[$i]; }
    private function duration(int $seconds): string { $days=intdiv($seconds,86400);$hours=intdiv($seconds%86400,3600);$minutes=intdiv($seconds%3600,60);return $days>0?$days.' dias '.$hours.' h':($hours>0?$hours.' h '.$minutes.' min':$minutes.' min'); }
}
