<?php
declare(strict_types=1);

namespace Hardness\Observability\Http;

use Hardness\Observability\Context\ObservationContextProvider;
use Hardness\Observability\Support\IncidentSnapshotArchive;
use Hardness\Observability\Support\ErrorLogCollector;
use Hardness\Observability\Collectors\Mysql\MysqlRecorderHistoryService;
use Hardness\Observability\Collectors\Mysql\MysqlRecorderScopeResolver;

final class PrototypeController
{
    private $contexts;
    private $data;
    private $archive;
    private $mysqlHistory;
    private $mysqlScope;

    public function __construct(ObservationContextProvider $contexts, $data, ?IncidentSnapshotArchive $archive = null, ?MysqlRecorderHistoryService $mysqlHistory = null, ?MysqlRecorderScopeResolver $mysqlScope = null)
    {
        $this->contexts = $contexts;
        $this->data = $data;
        $this->archive = $archive ?: new IncidentSnapshotArchive();
        $this->mysqlHistory = $mysqlHistory ?: new MysqlRecorderHistoryService();
        $this->mysqlScope = $mysqlScope;
    }

    public function page(string $requestedPath = '/'): Response
    {
        $context = $this->contexts->current()->toArray();
        $scope = htmlspecialchars($context['scopeLabel'], ENT_QUOTES, 'UTF-8');
        $view = $context['role'] === 'hardness_internal' ? 'Visão Hardness' : 'Visão do cliente';
        $base = preg_match('#^(.*?/observability)(?:/|$)#', $requestedPath, $match) === 1 ? $match[1] . '/' : '/';
        $baseAttribute = htmlspecialchars($base, ENT_QUOTES, 'UTF-8');
        $assetBase = rtrim($base, '/');
        $logout = method_exists($this->contexts, 'csrf') ? '<form method="post" action="' . $assetBase . '/auth/logout"><input type="hidden" name="csrf" value="' . htmlspecialchars($this->contexts->csrf(), ENT_QUOTES, 'UTF-8') . '"><button class="button button-secondary" type="submit">Sair</button></form>' : '';

        $csrf = method_exists($this->contexts, 'csrf') ? htmlspecialchars($this->contexts->csrf(), ENT_QUOTES, 'UTF-8') : '';
        $captureButton = $context['role'] === 'hardness_internal' ? '<button class="button button-secondary" id="capture-button" type="button">Salvar captura</button>' : '';
        $html = '<!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Observability — Hardness</title><link rel="stylesheet" href="' . $assetBase . '/assets/css/observability.css"></head><body>'
            . '<main class="app-shell" id="app" data-base-path="' . $baseAttribute . '" data-view-label="' . htmlspecialchars($view, ENT_QUOTES, 'UTF-8') . '" data-csrf="' . $csrf . '">'
            . '<aside class="sidebar" id="sidebar"><a class="brand" href="' . $baseAttribute . '" aria-label="Observability Hardness"><img class="brand-logo" src="' . $assetBase . '/assets/img/logo_hardness.png" alt="Hardness"></a><div class="sidebar-heading"><span>Observability</span></div><nav id="main-navigation" aria-label="Navegação principal"></nav><div class="sidebar-foot"><span class="status-dot"></span><span class="mock-note">Coleta somente leitura</span></div></aside>'
            . '<section class="workspace"><header class="topbar"><div class="topbar-title"><button class="menu-toggle" id="menu-toggle" type="button" aria-label="Abrir menu">☰</button><div><p class="eyebrow" id="screen-kicker">ESTADO ATUAL</p><h1 id="screen-title">Observabilidade</h1><p class="scope">' . $scope . '</p></div></div><div class="topbar-actions"><span class="role-badge">' . htmlspecialchars($view, ENT_QUOTES, 'UTF-8') . '</span><span class="severity overall-badge" id="overall-status">Carregando</span><span class="refresh-state" id="refresh-state">Atualização automática</span><button class="button button-secondary" id="pause-button" type="button">Pausar</button>' . $captureButton . '<button class="button" id="refresh-button" type="button">Atualizar</button>' . $logout . '</div></header>'
            . '<section id="screen-content" aria-live="polite"></section></section></main>'
            . '<aside class="drawer" id="sql-drawer" aria-hidden="true"><div class="drawer-backdrop" data-close-drawer></div><section class="drawer-panel" role="dialog" aria-modal="true" aria-labelledby="drawer-title"><button class="drawer-close" type="button" data-close-drawer aria-label="Fechar detalhes">×</button><p class="eyebrow">DETALHES DA CONSULTA</p><h2 id="drawer-title">Processo</h2><div class="drawer-meta" id="drawer-meta"></div><div class="sql-block"><div class="sql-heading"><span>SQL</span><button type="button" class="copy-button" id="copy-sql">Copiar</button></div><pre id="sql-content"></pre></div><div class="drawer-section"><h3>Tabelas detectadas</h3><div id="drawer-tables" class="table-tags"></div></div><p class="drawer-note">Observações são informativas e não representam diagnóstico conclusivo.</p></section></aside>'
            . '<aside class="drawer" id="server-process-drawer" aria-hidden="true"><div class="drawer-backdrop" data-close-server-drawer></div><section class="drawer-panel" role="dialog" aria-modal="true" aria-labelledby="server-process-title"><button class="drawer-close" type="button" data-close-server-drawer aria-label="Fechar detalhes">×</button><p class="eyebrow">DETALHES DO PROCESSO</p><h2 id="server-process-title">Processo do servidor</h2><div class="drawer-meta" id="server-process-meta"></div><div class="drawer-section"><h3>Comando em execução</h3><pre class="process-command" id="server-process-command"></pre></div><div class="drawer-section"><h3>Executável e diretório de trabalho</h3><pre class="process-command" id="server-process-location"></pre></div><div class="drawer-section"><h3>Comando do processo pai</h3><pre class="process-command" id="server-process-parent-command"></pre></div><div class="drawer-section"><h3>Sinais para investigar</h3><ul class="process-signals" id="server-process-signals"></ul></div><div class="drawer-section"><h3>Como interpretar</h3><p class="drawer-note" id="server-process-note"></p></div></section></aside>'
            . '<aside class="drawer history-detail-drawer" id="mysql-history-drawer" aria-hidden="true"><div class="drawer-backdrop" data-close-history-drawer></div><section class="drawer-panel" role="dialog" aria-modal="true" aria-labelledby="mysql-history-detail-title"><button class="drawer-close" type="button" data-close-history-drawer aria-label="Fechar detalhes">×</button><p class="eyebrow" id="mysql-history-detail-kicker">DETALHES DO HISTÓRICO</p><h2 id="mysql-history-detail-title">Evidências</h2><div id="mysql-history-detail-body"></div></section></aside>'
            . '<script src="' . $assetBase . '/assets/js/observability.js"></script></body></html>';
        return Response::html($html);
    }

    public function snapshot(): Response
    {
        $snapshot = $this->sanitizedSnapshot($this->data->snapshot());
        return Response::json($snapshot);
    }

    public function capture(string $note): Response
    {
        $context = $this->contexts->current()->toArray();
        if (($context['role'] ?? '') !== 'hardness_internal') { return Response::json(array('error' => 'Capturas estão disponíveis somente na Visão Hardness.'), 403); }
        try { $item = $this->archive->save($this->sanitizedSnapshot($this->data->snapshot()), $context, $note); }
        catch (\Throwable $exception) { return Response::json(array('error' => 'Não foi possível gravar a captura. Verifique a escrita em dados_usuarios/observability.'), 503); }
        return Response::json(array('item' => $item), 201);
    }

    public function captures(): Response
    {
        $context = $this->contexts->current()->toArray();
        if (($context['role'] ?? '') !== 'hardness_internal') { return Response::json(array('error' => 'Capturas estão disponíveis somente na Visão Hardness.'), 403); }
        return Response::json($this->archive->list($context));
    }

    public function captureById(string $id): Response
    {
        $context = $this->contexts->current()->toArray();
        if (($context['role'] ?? '') !== 'hardness_internal') { return Response::json(array('error' => 'Capturas estão disponíveis somente na Visão Hardness.'), 403); }
        $record = $this->archive->get($context, $id);
        return $record === null ? Response::json(array('error' => 'Captura não encontrada.'), 404) : Response::json($record);
    }

    public function downloadCapture(string $id): Response
    {
        $context = $this->contexts->current()->toArray();
        if (($context['role'] ?? '') !== 'hardness_internal') { return Response::json(array('error' => 'Capturas estão disponíveis somente na Visão Hardness.'), 403); }
        $record = $this->archive->get($context, $id);
        return $record === null ? Response::json(array('error' => 'Captura não encontrada.'), 404) : Response::downloadJson($record, 'hardness-observability-' . $id . '.json');
    }
    public function deleteCapture(string $id): Response
    {
        $context = $this->contexts->current()->toArray();
        if (($context['role'] ?? '') !== 'hardness_internal') { return Response::json(array('error' => 'Capturas estão disponíveis somente na Visão Hardness.'), 403); }
        return $this->archive->delete($context, $id) ? Response::json(array('deleted' => true)) : Response::json(array('error' => 'Captura não encontrada ou não pôde ser excluída.'), 404);
    }
    public function errorLogs(): Response
    {
        $context=$this->contexts->current()->toArray();
        if (($context['role'] ?? '') !== 'hardness_internal') { return Response::json(array('error'=>'Logs disponíveis somente na Visão Hardness.'),403); }
        return Response::json((new ErrorLogCollector())->collect());
    }

    public function mysqlHistory(Request $request): Response
    {
        try {
            $scope = $this->mysqlScope ? $this->mysqlScope->resolve() : array('schemas'=>array(), 'capabilities'=>$this->contexts->current()->toArray()['capabilities'] ?? array());
            $now = time(); $from = $now - 86400; $to = $now;
            $fromInput = $request->query('from'); $toInput = $request->query('to');
            if (($fromInput === '') !== ($toInput === '')) { throw new \InvalidArgumentException('mysql_history_invalid_period'); }
            if ($fromInput !== '') { $from = $this->historyTimestamp($fromInput); $to = $this->historyTimestamp($toInput); }
            $page = $this->positiveInteger($request->query('page'), 1);
            $limit = $this->positiveInteger($request->query('limit'), 100);
            return Response::json($this->mysqlHistory->history($scope['schemas'], $scope['capabilities'], $from, $to, $page, $limit));
        } catch (\InvalidArgumentException $exception) {
            return Response::json(array('error'=>'Período ou paginação inválidos.', 'contract_version'=>'mysql-history.v1'), 400);
        } catch (\Throwable $exception) {
            return Response::json(array('error'=>'Histórico MySQL indisponível.', 'contract_version'=>'mysql-history.v1'), 503);
        }
    }

    private function historyTimestamp(string $value): int
    {
        if (strlen($value) > 64) { throw new \InvalidArgumentException('mysql_history_invalid_period'); }
        $date = \DateTimeImmutable::createFromFormat(DATE_ATOM, $value);
        $errors = \DateTimeImmutable::getLastErrors();
        if ($date === false || (is_array($errors) && ($errors['warning_count'] > 0 || $errors['error_count'] > 0))) { throw new \InvalidArgumentException('mysql_history_invalid_period'); }
        return $date->getTimestamp();
    }

    private function positiveInteger(string $value, int $default): int
    {
        if ($value === '') { return $default; }
        if (preg_match('/^[1-9]\d{0,5}$/D', $value) !== 1) { throw new \InvalidArgumentException('mysql_history_invalid_pagination'); }
        return (int) $value;
    }

    private function sanitizedSnapshot(array $snapshot): array
    {
        $capabilities = isset($snapshot['context']['capabilities']) ? $snapshot['context']['capabilities'] : array();
        foreach ($snapshot['processes'] as $index => $process) {
            if (empty($capabilities['full_sql'])) {
                $snapshot['processes'][$index]['sql'] = '';
                $snapshot['processes'][$index]['summary'] = $process['command'] === 'Sleep' ? 'Conexão ociosa' : 'SQL oculto';
                $snapshot['processes'][$index]['tables'] = array();
            }
            if (empty($capabilities['connection_identity'])) {
                $snapshot['processes'][$index]['user'] = 'Oculto';
                $snapshot['processes'][$index]['host'] = 'Oculto';
                $snapshot['processes'][$index]['database'] = 'Oculto';
            }
        }
        return $snapshot;
    }

    /**
     * Sonda deliberadamente vazia: a duração da requisição é medida pelo navegador.
     * Não expõe dados do servidor e é protegida pelo mesmo router/sessão da aplicação.
     */
    public function networkProbe(): Response
    {
        return Response::empty();
    }
}
