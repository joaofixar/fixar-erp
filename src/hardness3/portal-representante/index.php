<?php
namespace hardness;
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/empresa.php';
if (!$portalModoGestor && isset($_GET['convite'])) {
    $conviteEntrada = is_string($_GET['convite']) && preg_match('/^[a-f0-9]{48}$/D', $_GET['convite']) ? $_GET['convite'] : '';
    if (!isset($_SESSION['portalConvite']) || $_SESSION['portalConvite'] !== $conviteEntrada) {
        unset($_SESSION['portal'], $_SESSION['portalDesafio'], $_SESSION['portalCadastro'], $_SESSION['portalTokenSenha'], $_SESSION['portalEmailConfirmado'], $_SESSION['portalSenhaSalva']);
        $_SESSION['portalCsrf'] = bin2hex(random_bytes(32));
    }
    $_SESSION['portalConvite'] = $conviteEntrada;
}
if (!$portalModoGestor && isset($_GET['cadastro'])) {
    unset($_SESSION['portal']);
    $_SESSION['portalTokenSenha'] = is_string($_GET['cadastro']) && preg_match('/^[a-f0-9]{64}$/D', $_GET['cadastro']) ? $_GET['cadastro'] : '';
}
$conta = false; $erroGestor = ''; $representantesGestor = array();
$empresaPortal = array('nome' => 'Empresa', 'logo' => '');
try {
    $sessao = isset($_SESSION['portal']) ? $_SESSION['portal'] : null;
    if ($portalModoGestor && !$portalGestorContexto) throw new RepresentantePortalErro('A visualização do gestor expirou. Feche esta janela e abra o portal novamente pelo dashboard.', 403);
    $acesso = !$portalModoGestor && is_array($sessao) && isset($sessao['convite']) ? portalRepresentanteAcesso($confUsuario, $sessao['convite']) : false;
    if ($portalModoGestor || $acesso) {
        $conexao = portalRepresentanteConectar($confUsuario);
        try {
            $registroPortal = portalRepresentanteLerCadastro($conexao, $portalModoGestor ? $portalGestorContexto['representante'] : $acesso['representante']);
            $conta = $portalModoGestor ? portalRepresentanteGestorConta($confUsuario, $portalGestorContexto, $registroPortal)
                : portalRepresentanteValidarSessao($confUsuario, $sessao, $registroPortal);
            if ($portalModoGestor && $conta) {
                $_SESSION['portalGestor'][$portalGestorToken]['ultimo'] = time();
                $representantesGestor = portalRepresentanteGestorRepresentantes($conexao, $portalGestorContexto);
            }
            if ($conta) {
                try { $empresaPortal = portalRepresentanteEmpresa($conexao, $confUsuario, $conta['empresa']); }
                catch (\Throwable $erroEmpresa) { /* A identidade visual não impede o acompanhamento. */ }
            }
        }
        finally { portalRepresentanteFecharConexao($conexao); }
    }
} catch (\Throwable $erro) {
    $conta = false;
    if ($portalModoGestor) { $erroGestor = $erro instanceof RepresentantePortalErro ? $erro->getMessage() : 'Não foi possível abrir o portal. Feche esta janela e tente novamente pelo dashboard.'; http_response_code($erro instanceof RepresentantePortalErro ? $erro->status : 503); }
}
if (!$portalModoGestor) {
    if (!$conta) unset($_SESSION['portal']);
    require __DIR__ . '/login-estado.php';
    $portalCsrfAtual = $_SESSION['portalCsrf'];
}
$permiteDemonstracao = !$portalModoGestor && $conta && portalRepresentanteDemonstracaoPermitida($confUsuario, $conta, $_SERVER);
$demonstracao = !$portalModoGestor && $conta && portalRepresentanteDemonstracaoAtiva($confUsuario, $conta, $_SESSION, $_SERVER);
$layoutPortal = array(); $erroPainel = '';
$identidadePortal = portalRepresentanteIdentidadePortal();
if ($conta) {
    try {
        $configPortal = portalRepresentanteLerConfiguracao($conta['empresa']);
        $layoutPortal = portalRepresentanteLayoutPublico($configPortal);
        $identidadePortal = portalRepresentanteIdentidadePortal($configPortal);
    }
    catch (RepresentantePortalErro $erro) { $erroPainel = $erro->getMessage(); }
} elseif (!empty($empresaPortalLogin)) {
    try { $identidadePortal = portalRepresentanteIdentidadePortal(portalRepresentanteLerConfiguracao($empresaPortalLogin, $confUsuario, false)); }
    catch (\Throwable $erroIdentidade) { /* A personalização visual não impede a identificação. */ }
}
$tituloPaginaPortal = $identidadePortal['personalizado'] ? $identidadePortal['nome'] . ' | Hardness' : 'Acompanhamento de vendas | Hardness';
$rotuloPortal = $identidadePortal['personalizado'] ? $identidadePortal['nome'] : 'PORTAL DO REPRESENTANTE';
function portalRepresentanteHtml($valor) { return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8'); }
header('Content-Type: text/html; charset=utf-8');
?>
<!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="theme-color" content="#173dc4"><title><?= portalRepresentanteHtml($tituloPaginaPortal) ?></title><link rel="stylesheet" href="../dashboard-personalizado/dashboard.css?v=88"><link rel="stylesheet" href="portal.css?v=44"><script src="personalizacao.js?v=2" defer></script><script src="regioes.js?v=9" defer></script><script src="analise-comercial.js?v=3" defer></script><script src="inicio.js?v=15" defer></script><script src="login.js?v=2" defer></script><script src="comparativo.js?v=2" defer></script><script src="portal.js?v=47" defer></script></head>
<body<?= $layoutPortal ? ' data-layout="' . portalRepresentanteHtml(json_encode($layoutPortal, JSON_UNESCAPED_UNICODE)) . '"' : '' ?> data-csrf="<?= portalRepresentanteHtml($portalCsrfAtual) ?>" data-demonstracao="<?= $demonstracao ? '1' : '0' ?>"<?= $portalModoGestor ? ' data-gestor="' . portalRepresentanteHtml(is_string($portalGestorToken) ? $portalGestorToken : '') . '"' : '' ?>>
<?php if ($erroGestor): ?>
<main class="login-card"><h1>Visualização do gestor</h1><p role="alert"><?= portalRepresentanteHtml($erroGestor) ?></p></main>
<?php elseif (!$conta): ?>
<main class="login-shell">
  <section class="login-intro"><div class="login-brand"><img src="../static/img/logo_hardness.png" alt="Hardness"></div><div><h1><?php if ($identidadePortal['prefixo'] !== ''): ?><span class="login-title-prefix"><?= portalRepresentanteHtml($identidadePortal['prefixo']) ?></span> <?php endif; ?><span><?= portalRepresentanteHtml($identidadePortal['titulo']) ?></span></h1><p class="login-subtitle">Acompanhe suas vendas,<br>onde estiver.</p></div><small>Hardness · Acompanhamento comercial</small></section>
  <section class="login-panel<?= $empresaPortal['logo'] !== '' ? ' login-panel--branded' : '' ?>">
    <?php if ($empresaPortal['logo'] !== ''): ?><div class="login-company"><img src="<?= portalRepresentanteHtml($empresaPortal['logo']) ?>" alt="Logo da empresa representada"></div><?php endif; ?>
    <div class="login-card">
    <?php require __DIR__ . '/login-form.php'; ?>
  </div></section>
</main>
<?php elseif ($erroPainel): ?>
<main class="login-card"><h1><?= portalRepresentanteHtml($identidadePortal['nome']) ?></h1><p role="alert"><?= portalRepresentanteHtml($erroPainel) ?></p><p>Após a correção, atualize esta página.</p></main>
<?php else: ?>
<main class="app-shell" id="representanteApp">
  <aside class="sidebar" aria-label="Acompanhamento comercial"><div class="brand"><img src="../static/img/logo_hardness.png" alt="Hardness"></div><div class="sidebar-heading"><span>Meu acompanhamento</span></div><nav class="indicator-menu" id="portalMenu">
  <?php foreach ($layoutPortal['menu'] as $indice => $tipo): $rotulo = $layoutPortal['visoes'][$tipo]; ?>
    <button type="button" class="menu-item<?= $tipo === 'inicio' ? ' active' : '' ?>" data-tipo="<?= $tipo ?>"<?= $tipo === 'inicio' ? ' aria-current="page"' : '' ?>><span class="menu-icon"><?= str_pad((string) ($indice + 1), 2, '0', STR_PAD_LEFT) ?></span><span class="menu-copy"><b><?= portalRepresentanteHtml($rotulo['titulo']) ?></b><small><?= portalRepresentanteHtml($rotulo['descricao']) ?></small></span><span class="menu-chevron">›</span></button>
  <?php endforeach; ?>
  </nav><div class="sidebar-footer"><span class="status-dot"></span><span><?= portalRepresentanteHtml($conta['nome']) ?></span><button type="button" class="secondary logout" id="logout"><?= $portalModoGestor ? 'Fechar visualização' : 'Sair do portal' ?></button></div></aside>
  <section class="workspace"><header class="topbar portal-topbar">
    <div class="portal-heading"><p class="eyebrow"><?= portalRepresentanteHtml($rotuloPortal) ?></p><h1><?= portalRepresentanteHtml($layoutPortal['titulo']) ?></h1></div>
    <div class="portal-representative"><?php if ($portalModoGestor): ?>
      <label class="portal-representative-picker" for="gestorRepresentante"><small>Representante · visualização do gestor</small><select id="gestorRepresentante" aria-label="Representante da visualização do gestor">
      <?php foreach ($representantesGestor as $representanteGestor): ?><option value="<?= (int) $representanteGestor['id'] ?>"<?= (int) $representanteGestor['id'] === $conta['representante'] ? ' selected' : '' ?>><?= portalRepresentanteHtml($representanteGestor['nome']) ?></option><?php endforeach; ?>
      </select></label>
    <?php else: ?><small>Representante</small><strong><?= portalRepresentanteHtml($conta['nome']) ?></strong><?php endif; ?></div>
    <div class="portal-company"><?php if ($empresaPortal['logo'] !== ''): ?><img src="<?= portalRepresentanteHtml($empresaPortal['logo']) ?>" alt="Logo da empresa representada"><?php endif; ?></div>
    <div class="portal-today"><small>Hoje</small><time id="portalToday" datetime="<?= date('Y-m-d') ?>"><?= date('d/m/Y') ?></time><span id="portalWeekday"></span></div>
    <div class="topbar-actions"><time id="updatedAt"></time><button class="refresh" id="refresh" type="button" aria-label="Atualizar">↻</button></div>
  </header>
    <?php if ($permiteDemonstracao): ?>
    <aside class="demo-banner" role="status"><div><strong><?= $demonstracao ? 'Dados de demonstração' : 'Visualizando dados reais' ?></strong><p><?= $demonstracao ? 'Movimentações de exemplo para testar valores, gráficos e filtros. Esses documentos não foram cadastrados no ERP.' : 'A demonstração está desativada. Os valores abaixo são os documentos reais deste representante.' ?></p></div><button type="button" class="secondary" id="demoToggle"><?= $demonstracao ? 'Voltar aos dados reais' : 'Ver dados de demonstração' ?></button></aside>
    <?php endif; ?>
    <div class="period-toolbar"><div class="period-navigation"><h2 id="viewTitle">Início</h2><div class="period-presets" aria-label="Período de análise" hidden><button type="button" data-period="month">Este mês</button><button type="button" data-period="30">Últimos 30 dias</button><button type="button" data-period="90">Últimos 90 dias</button></div></div><span id="periodLabel"></span></div>
    <section class="month-filter" aria-label="Período por mês"><label for="periodYear">Ano <input type="number" id="periodYear" min="1000" max="<?= date('Y') ?>" value="<?= date('Y') ?>" step="1"></label><div id="monthBlocks" class="month-blocks" role="group" aria-label="Selecionar mês"></div><p id="monthPeriodNote" aria-live="polite"></p></section>
    <form class="filters" id="filters" hidden><label><span>Data inicial</span><input type="date" name="inicio" value="<?= date('Y-01-01') ?>" required></label><label><span>Data final</span><input type="date" name="fim" value="<?= date('Y-m-d') ?>" required></label><label class="client-search"><span>Cliente</span><input type="search" name="cliente" maxlength="100" placeholder="Buscar pelo nome"></label><label><span id="numberLabel">Nº do orçamento</span><input type="search" name="numero" maxlength="40" placeholder="Buscar número"></label><div class="filter-actions"><button type="submit" class="primary">Aplicar filtros</button><button type="button" class="secondary" id="clearFilters">Limpar</button></div><p class="period-note" id="periodNote"></p></form>
    <div id="selectionBar" class="selection-bar" hidden><div><strong id="selectionTitle"></strong><span id="selectionDescription">Indicadores, gráficos e listagens desta seleção.</span></div><button type="button" class="secondary" data-clear-selection>Limpar seleção e ver todos</button></div>
    <p id="portalStatus" class="form-status" role="status" aria-live="polite"></p>
    <section id="homeView" class="home-view" aria-label="Visão geral do representante" aria-busy="true"></section>
    <section hidden id="summary" class="portal-summary" aria-label="Indicadores da área" aria-busy="true"></section>
    <section hidden id="insights" class="portal-insights" aria-label="Destaques do período"></section>
    <section hidden id="analytics" class="portal-analytics" tabindex="-1" aria-label="Análise do período" aria-busy="true">
      <article class="wide-card evolution-card"><header class="analytics-heading"><div><p class="eyebrow">EVOLUÇÃO NO PERÍODO</p><h3 id="chartTitle">Volume de orçamentos</h3><p id="chartDescription"></p></div><div class="chart-actions"><button type="button" class="secondary clear-client" data-clear-selection hidden>Limpar seleção</button><div class="chart-switch" aria-label="Medida do gráfico"><button type="button" data-measure="valor" aria-pressed="true">Valor</button><button type="button" data-measure="quantidade" aria-pressed="false">Quantidade</button></div></div></header><div id="evolutionChart" class="evolution-chart"></div><p class="chart-hint">Selecione um intervalo do gráfico para consultar seus documentos.</p></article>
      <article class="wide-card situation-card"><header class="analytics-heading"><div><p class="eyebrow">DISTRIBUIÇÃO</p><h3 id="situationTitle">Situação dos orçamentos</h3><p>Participação por quantidade de documentos.</p></div></header><div id="situations"></div></article>
      <article class="wide-card customers-card"><header class="analytics-heading"><div><p class="eyebrow">CLIENTES EM DESTAQUE</p><h3 id="customersTitle">Maiores volumes do período</h3><p>Selecione um cliente para consultar seus documentos.</p></div><button type="button" class="secondary clear-client" data-clear-selection hidden>Limpar seleção</button></header><div id="topCustomers"></div></article>
      <article class="wide-card attention-card"><header class="analytics-heading"><div><p class="eyebrow" id="attentionEyebrow">AÇÕES COMERCIAIS</p><h3 id="attentionTitle">Orçamentos que precisam de ação</h3><p id="attentionDescription"></p></div></header><div id="attention"></div></article>
      <article class="wide-card segments-card" id="segmentsCard" hidden><header class="analytics-heading"><div><p class="eyebrow">DIVISÃO DAS VENDAS</p><h3>Vendas por distrito</h3><p>Valor e participação no total do período.</p></div></header><div id="segmentsBreakdown"></div></article>
      <article class="wide-card cancellations-card" id="cancellationsCard" hidden><header class="analytics-heading"><div><p class="eyebrow">CANCELAMENTOS COM MOTIVO</p><h3>Orçamentos cancelados</h3><p>Revisões substituídas não são contabilizadas. Principais motivos do período.</p></div></header><div id="cancellationsBreakdown"></div></article>
    </section>
    <section hidden class="wide-card documents-card" id="documentsCard"><header><div><p class="eyebrow">DOCUMENTOS</p><h3 id="documentsTitle">Orçamentos do período</h3><p id="listCaption"></p></div><button type="button" class="secondary" id="exportCsv" disabled>Exportar página CSV</button></header><div class="list-toolbar"><label id="stageFilter"><span>Situação do painel</span><select name="etapa" form="filters" id="stageSelect"><option value="">Todas as situações</option></select></label><label id="paymentFilter" hidden><span>Situação do painel</span><select name="status" form="filters"><option value="">Todos os pagamentos</option><option value="pago">Pago</option><option value="aberto">Em aberto</option><option value="parcial">Parcial</option><option value="vencido">Vencido</option></select></label><span id="listCount"></span></div><div id="compositionDetail" class="composition-detail" hidden><p id="compositionCaption" role="status"></p><button type="button" class="secondary" id="clearComposition">Ver toda a listagem</button></div><div id="documents" tabindex="-1" class="table-scroll" aria-busy="true"></div><footer class="pagination"><button id="previous" type="button" class="secondary" disabled>Anterior</button><span id="pageLabel">Página 1</span><button id="next" type="button" class="secondary" disabled>Próxima</button></footer></section>
    <section id="comparisonView" hidden aria-label="Comparativo por cliente" aria-busy="false"></section>
    <section id="regionalView" hidden aria-label="Vendas por região">
      <div class="regional-toolbar"><label>Região<select id="regionalRegion"><option value="">Todo o Brasil</option><option>Norte</option><option>Nordeste</option><option>Centro-Oeste</option><option>Sudeste</option><option>Sul</option></select></label><label>Estado<select id="regionalUf"><option value="">Todas as UFs</option></select></label><label>Medida no mapa<select id="regionalMetric"><option value="faturamento">Faturamento autorizado</option><option value="valorPedidos">Valor em pedidos</option><option value="clientes">Quantidade de clientes</option></select></label><label>Exibir clientes<select id="regionalCoverage"><option value="todos">Toda a carteira</option><option value="com_venda">Com faturamento no período</option><option value="sem_venda">Sem faturamento no período</option><option value="sem_localizacao">Sem localização válida</option></select></label></div>
      <p class="regional-quality" id="regionalQuality" role="status"></p>
      <div class="regional-layout">
        <article class="wide-card regional-map-card" id="regionalMapCard" tabindex="-1"><header class="analytics-heading"><div><p class="eyebrow">PRESENÇA COMERCIAL</p><h3>Seu território de vendas</h3><p>Explore as regiões e descubra onde sua carteira está presente.</p></div><span class="regional-live"><i></i> BRASIL</span></header>
          <div class="regional-map-stage">
            <div class="regional-map" id="regionalMap" aria-busy="true"></div>
            <div class="regional-map-hud" aria-hidden="true"><span id="regionalMapMetric">Faturamento autorizado</span><strong id="regionalMapScope">Visão nacional</strong><small id="regionalMapDensity"></small></div>
            <div class="regional-map-tooltip" id="regionalMapTooltip" role="tooltip" hidden></div>
            <div class="regional-map-controls" aria-label="Navegação do mapa"><button type="button" id="regionalZoomIn" aria-label="Ampliar mapa" title="Ampliar">+</button><button type="button" id="regionalZoomOut" aria-label="Reduzir mapa" title="Reduzir">−</button><output id="regionalZoomLevel" aria-label="Ampliação">1×</output><button type="button" id="regionalReset">Ver Brasil</button><button type="button" id="regionalExpand" aria-label="Expandir mapa em tela cheia" title="Expandir mapa"><svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><path d="M8 3H3v5m13-5h5v5M3 16v5h5m13-5v5h-5" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg></button></div>
            <span class="regional-map-gesture">Arraste para explorar · Ctrl + rolagem para aproximar</span>
          </div>
          <div class="regional-legend" id="regionalLegend"></div><p class="regional-map-note" id="regionalMapNote">Limites: IBGE · Posições aproximadas por município. Consulte o endereço completo na lista.</p>
        </article>
        <aside class="wide-card regional-detail"><p class="eyebrow">EXPLORAR A CARTEIRA</p><h3 id="regionalFocus">Brasil</h3><p id="regionalFocusCaption"></p><div id="regionalFocusStats" class="regional-focus-stats"></div><button type="button" class="text-button" id="regionalClearCity" hidden>Ver todo o estado</button><h4 id="regionalRankingTitle">Estados em destaque</h4><p id="regionalRankingCaption" class="regional-ranking-caption" hidden></p><div id="regionalRanking"></div><div class="regional-help"><strong>Encontre oportunidades</strong><p>Use “Sem faturamento no período” para acompanhar clientes da sua carteira que ainda não compraram.</p></div></aside>
      </div>
      <article class="wide-card regional-clients"><header class="analytics-heading"><div><p class="eyebrow">CLIENTES POR LOCALIDADE</p><h3 id="regionalClientsTitle">Carteira do representante</h3><p id="regionalClientsCaption"></p></div><button type="button" class="secondary" id="regionalCsv" disabled>Exportar seleção CSV</button></header><p class="regional-table-hint">Deslize a tabela para ver endereços e valores.</p><div id="regionalClients" class="table-scroll"></div><footer class="pagination"><button type="button" id="regionalPrevious" class="secondary" disabled>Anterior</button><span id="regionalPage">Página 1</span><button type="button" id="regionalNext" class="secondary" disabled>Próxima</button></footer></article>
    </section>
  </section>
  <section class="indicator-loading portal-loading" id="portalLoading" hidden aria-live="polite" aria-atomic="true">
    <div class="indicator-loading-card" role="status"><div><strong>Carregando acompanhamento</strong><small>Consultando e consolidando os dados. Aguarde um momento.</small><span class="indicator-loading-progress" role="progressbar" aria-label="Carregamento em andamento"><i></i></span></div></div>
  </section>
</main><dialog id="chargesDialog" class="charges-dialog"><header><div><p class="eyebrow">PAGAMENTOS</p><h2 id="chargesTitle">Cobranças da nota</h2></div><button type="button" id="closeCharges" class="secondary" aria-label="Fechar">×</button></header><p id="chargesDescription">Todas as parcelas da nota, independentemente do vencimento.</p><div id="chargesContent" class="table-scroll"></div></dialog>
<dialog id="regionalClientsDialog" class="regional-clients-dialog" aria-labelledby="regionalDialogTitle" aria-describedby="regionalDialogCaption">
  <header><div><p class="eyebrow">CLIENTES DO MUNICÍPIO</p><h2 id="regionalDialogTitle">Clientes da cidade</h2></div><button type="button" class="secondary" id="regionalCloseClients" autofocus>Fechar ×</button></header>
  <p id="regionalDialogCaption" class="regional-dialog-caption"></p>
  <p class="regional-dialog-hint">Endereço principal do cadastro. Selecione um cliente para voltar ao município no mapa.</p>
  <div id="regionalDialogContent" class="table-scroll"></div>
  <footer><button type="button" class="secondary" id="regionalLocateCity">Ver município no mapa</button><div class="pagination"><button type="button" class="secondary" id="regionalDialogPrevious">Anterior</button><span id="regionalDialogPage"></span><button type="button" class="secondary" id="regionalDialogNext">Próxima</button></div></footer>
</dialog>
<?php endif; ?>
</body></html>
