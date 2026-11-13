<?php
declare(strict_types=1);

namespace Hardness\Observability\Diagnostics;

use Hardness\Observability\Severity\Severity;

/**
 * Transforma apenas o snapshot já coletado em orientações de suporte.
 * O contrato público continua sendo title/message/origin/evidence/link.
 */
final class DiagnosticEngine
{
    public function evaluate(array $metrics, array $processes, array $apachePhp, array $database): array
    {
        $items = array();
        $byLabel = array();
        foreach ($metrics as $metric) {
            $byLabel[$metric['label']] = $metric;
        }

        $this->metricRule($items, isset($byLabel['CPU']) ? $byLabel['CPU'] : null, 'Possível gargalo de processamento', 'Uso elevado de CPU pode indicar processos concorrendo por processamento.', 'Respostas da aplicação podem ficar lentas ou enfileiradas.', 'Verificar os processos com maior consumo de CPU.', '/server');
        $memory = isset($byLabel['Memória']) ? $byLabel['Memória'] : null;
        if ($memory && $this->needsAttention($memory['severity'])) {
            $swap = isset($byLabel['Swap']) ? $byLabel['Swap']['value'] : '';
            $cause = $this->number($swap) > 0 ? 'Memória elevada com swap em uso indica possível pressão de memória.' : 'Uso elevado de memória pode indicar pressão nos processos da aplicação.';
            $this->add($items, $memory['severity'], 'Possível pressão de memória', $cause, 'A aplicação pode responder mais lentamente sob carga.', 'Verificar o consumo de memória por processos e a disponibilidade de RAM.', 'Coletor do servidor', $memory['value'], '/server');
        }
        $this->metricRule($items, isset($byLabel['Disco']) ? $byLabel['Disco'] : null, 'Espaço em disco reduzido', 'A utilização do volume está elevada.', 'Logs, temporários e operações do banco podem falhar ao faltar espaço.', 'Liberar espaço antes que o volume atinja o limite.', '/server');

        $this->apacheRules($items, $apachePhp);
        $this->databaseRules($items, $database, $processes, isset($byLabel['Banco']) ? $byLabel['Banco'] : null);

        usort($items, function (array $left, array $right): int {
            return $this->weight($right['severity']) <=> $this->weight($left['severity']);
        });
        return $items ?: array($this->item(Severity::NORMAL, 'Sem condições que exijam atenção', 'As métricas disponíveis estão dentro dos limites configurados.', 'Não há impacto esperado nesta coleta.', 'Continuar acompanhando as próximas atualizações.', 'Visão geral', 'Coleta atual', '/'));
    }

    private function metricRule(array &$items, ?array $metric, string $title, string $cause, string $impact, string $recommendation, string $link): void
    {
        if (!$metric || !$this->needsAttention($metric['severity'])) {
            return;
        }
        $this->add($items, $metric['severity'], $title, $cause, $impact, $recommendation, 'Coletor do servidor', $metric['value'], $link);
    }

    private function apacheRules(array &$items, array $apachePhp): void
    {
        foreach (isset($apachePhp['details']) ? $apachePhp['details'] : array() as $detail) {
            if ($detail['label'] === 'OPcache' && empty($detail['available'])) {
                $this->add($items, Severity::ATTENTION, 'Cache de bytecode indisponível', 'OPcache está indisponível ou desativado.', 'O PHP pode precisar recompilar código com mais frequência.', 'Verificar a configuração e a disponibilidade do OPcache.', 'Apache/PHP', $detail['value'], '/apache-php');
            }
            if ($detail['label'] === 'APCu' && empty($detail['available'])) {
                $this->add($items, Severity::ATTENTION, 'Cache de aplicação indisponível', 'A extensão APCu não está disponível para a aplicação.', 'Operações que dependem desse cache podem ficar mais lentas.', 'Validar a extensão APCu e sua configuração.', 'Apache/PHP', $detail['value'], '/apache-php');
            }
        }
    }

    private function databaseRules(array &$items, array $database, array $processes, ?array $metric): void
    {
        if (isset($database['severity']) && $database['severity'] === Severity::UNAVAILABLE) {
            $this->add($items, Severity::UNAVAILABLE, 'Banco de dados indisponível', 'A coleta não conseguiu obter o estado do MySQL.', 'Funções dependentes do banco podem não responder.', 'Verificar a disponibilidade do MySQL e a conectividade da aplicação.', 'Coletor MySQL', 'Indisponível', '/database');
            return;
        }
        $usage = isset($database['details']['connectionUsage']) ? $this->number($database['details']['connectionUsage']) : 0;
        if ($usage >= 70) {
            $severity = $usage >= 90 ? Severity::CRITICAL : Severity::ATTENTION;
            $this->add($items, $severity, 'Número elevado de conexões', 'A utilização do limite de conexões do MySQL está elevada.', 'Novas operações podem aguardar ou falhar ao abrir conexão.', 'Verificar o pool de conexões e os processos ativos.', 'Coletor MySQL', $database['details']['connectionUsage'], '/database');
        } elseif ($metric && $this->needsAttention($metric['severity'])) {
            $this->add($items, $metric['severity'], 'Banco de dados requer atenção', 'A métrica de conexões ou atividade do MySQL atingiu o limite configurado.', 'A aplicação pode apresentar lentidão nas operações que usam o banco.', 'Verificar conexões ativas, processos e capacidade disponível.', 'Coletor MySQL', $metric['value'], '/database');
        }

        $locks = array_values(array_filter($processes, function (array $process): bool { return !empty($process['lock']); }));
        if ($locks) {
            $longest = max(array_column($locks, 'time'));
            $severity = $longest >= 15 ? Severity::CRITICAL : Severity::ATTENTION;
            $this->add($items, $severity, 'Contenção no banco de dados', 'Há sessões aguardando lock no MySQL.', 'Transações dependentes podem ficar bloqueadas ou lentas.', 'Analisar as sessões bloqueadas e a sessão que mantém o lock.', 'SHOW FULL PROCESSLIST', count($locks) . ' lock(s), maior espera: ' . $longest . ' s', '/processes');
        }
        $slowQueries = isset($database['details']['slowQueries']) ? $this->number($database['details']['slowQueries']) : 0;
        if ($slowQueries > 0) {
            $this->add($items, Severity::ATTENTION, 'Consultas lentas identificadas', 'O contador do MySQL registra consultas lentas.', 'Consultas demoradas podem aumentar o tempo de resposta da aplicação.', 'Revisar a execução das consultas e correlacionar com os processos ativos.', 'Coletor MySQL', (int) $slowQueries . ' slow query(s)', '/processes');
        }
        $longRunning = array_values(array_filter($processes, function (array $process): bool { return empty($process['lock']) && isset($process['severity']) && $this->needsAttention($process['severity']); }));
        if ($longRunning) {
            $longest = max(array_column($longRunning, 'time'));
            $severity = $longest >= 15 ? Severity::CRITICAL : Severity::ATTENTION;
            $this->add($items, $severity, 'Consulta acima do tempo esperado', 'Há consultas ativas acima do limite configurado.', 'A carga no banco pode aumentar e afetar operações concorrentes.', 'Revisar a consulta, índices envolvidos e o estado da sessão.', 'SHOW FULL PROCESSLIST', 'Maior duração: ' . $longest . ' s', '/processes');
        }
    }

    private function add(array &$items, string $severity, string $title, string $cause, string $impact, string $recommendation, string $origin, string $evidence, string $link): void
    {
        $items[] = $this->item($severity, $title, $cause, $impact, $recommendation, $origin, $evidence, $link);
    }

    private function item(string $severity, string $title, string $cause, string $impact, string $recommendation, string $origin, string $evidence, string $link): array
    {
        return array('severity' => $severity, 'title' => $title, 'message' => 'Possível causa: ' . $cause . ' Impacto esperado: ' . $impact . ' Verificar: ' . $recommendation, 'origin' => $origin, 'evidence' => $evidence, 'link' => $link);
    }

    private function needsAttention(string $severity): bool { return $severity === Severity::ATTENTION || $severity === Severity::CRITICAL; }
    private function number($value): float { return (float) str_replace(',', '.', preg_replace('/[^0-9,.-]/', '', (string) $value)); }
    private function weight(string $severity): int { return array(Severity::CRITICAL => 3, Severity::ATTENTION => 2, Severity::UNAVAILABLE => 1, Severity::NORMAL => 0)[$severity] ?? 0; }
}
