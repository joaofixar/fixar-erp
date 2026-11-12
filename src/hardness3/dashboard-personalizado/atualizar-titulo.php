<?php
namespace hardness;

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/../bibliotecas/confUsuario.php';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/access.php';
require_once __DIR__ . '/custom-indicator-engine.php';
require_once __DIR__ . '/titulo-action.php';

function dashboardAtualizarTituloResponder($codigo, $dados)
{
    http_response_code($codigo);
    echo json_encode($dados, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') dashboardAtualizarTituloResponder(405, array('error' => 'Método não permitido.'));
if (stripos(isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : '', 'application/json') !== 0) {
    dashboardAtualizarTituloResponder(415, array('error' => 'Envie os dados em JSON.'));
}
$autorizacao = dashboardPersonalizadoLerAutorizacao($confUsuario);
if ($autorizacao === false) dashboardAtualizarTituloResponder(401, array('error' => 'Sessão expirada. Reabra o dashboard pelo ERP.'));
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
if (!dashboardAgendaSessaoValida($autorizacao, $_SESSION)) {
    dashboardAtualizarTituloResponder(403, array('error' => 'A sessão do ERP mudou. Reabra o dashboard.'));
}
$recebido = json_decode(file_get_contents('php://input'), true);
if (!is_array($recebido)) dashboardAtualizarTituloResponder(422, array('error' => 'Dados de edição inválidos.'));
$lote = array_key_exists('tickets', $recebido);
$assinados = $lote ? $recebido['tickets'] : array(isset($recebido['ticket']) ? $recebido['ticket'] : '');
if (!is_array($assinados) || count($assinados) < 1 || count($assinados) > 100) {
    dashboardAtualizarTituloResponder(422, array('error' => 'Selecione de 1 a 100 títulos por atualização.'));
}
$tickets = array();
foreach ($assinados as $assinado) {
    $validado = dashboardTituloValidarTicket($assinado, $autorizacao, $confUsuario);
    if ($validado === false) dashboardAtualizarTituloResponder(409, array('error' => 'A edição expirou ou é inválida. Abra novamente a seleção.'));
    $tickets[] = $validado;
}
try { $tickets = dashboardTitulosValidarLote($tickets); }
catch (\RuntimeException $e) { dashboardAtualizarTituloResponder(422, array('error' => $e->getMessage())); }
$ticket = $tickets[0];
$configuracao = dashboardPersonalizadoConfiguracao($autorizacao['empresa'], $erros);
if ($configuracao === false) dashboardAtualizarTituloResponder(403, array('error' => 'Indicador indisponível.'));
$ids = array();
foreach ($configuracao['indicadores'] as $indicador) $ids[$indicador['id']] = true;
$acessos = dashboardPersonalizadoLerAcessos($autorizacao['empresa'], $ids);
$selecionado = null;
foreach (dashboardPersonalizadoIndicadoresPermitidos($autorizacao, $configuracao, $acessos) as $indicador) {
    if ($indicador['id'] === $ticket['entrada']['id']) { $selecionado = $indicador; break; }
}
if ($selecionado === null) dashboardAtualizarTituloResponder(403, array('error' => 'Você não possui mais acesso ao indicador.'));
$restricao = dashboardPersonalizadoRestricaoUsuarioIndicador($autorizacao, $acessos, $selecionado['id']);
$abaId = isset($ticket['entrada']['tab']) ? $ticket['entrada']['tab'] : '';
if (!empty($selecionado['abas'])) {
    if ($restricao !== false && $abaId !== dashboardPersonalizadoAbaVendedores($selecionado)) {
        dashboardAtualizarTituloResponder(403, array('error' => 'Aba não permitida.'));
    }
    $encontrada = false;
    foreach ($selecionado['abas'] as $aba) {
        if ($abaId === '' || $aba['id'] === $abaId) { $selecionado = array_merge($selecionado, $aba); $encontrada = true; break; }
    }
    if (!$encontrada) dashboardAtualizarTituloResponder(403, array('error' => 'Aba indisponível.'));
}
$entradaRestrita = $ticket['entrada'];
$selecionado = dashboardPersonalizadoAplicarRestricaoUsuario($selecionado, $restricao, $entradaRestrita, $erro);
if ($selecionado === false) dashboardAtualizarTituloResponder(403, array('error' => $erro));
foreach ($tickets as &$item) {
    $tituloId = $item['entrada']['tituloId'];
    $item['entrada'] = $entradaRestrita;
    $item['entrada']['tituloId'] = $tituloId;
}
unset($item);

mysqli_report(MYSQLI_REPORT_OFF);
$conexao = @mysqli_connect($confUsuario['dbHost'], $confUsuario['dbUser'], $confUsuario['dbPass'], $confUsuario['dbDatabase']);
if (!$conexao) dashboardAtualizarTituloResponder(503, array('error' => 'Não foi possível acessar o título.'));
mysqli_set_charset($conexao, 'utf8mb4');
@mysqli_query($conexao, 'SET SESSION MAX_EXECUTION_TIME=5000');
@mysqli_query($conexao, 'SET SESSION innodb_lock_wait_timeout=5');
try {
    $resultado = $lote
        ? dashboardTitulosAtualizar($conexao, $selecionado, $autorizacao, $_SESSION, $tickets, $recebido)
        : dashboardTituloAtualizar($conexao, $selecionado, $autorizacao, $_SESSION, $tickets[0], $recebido);
    mysqli_close($conexao);
    dashboardAtualizarTituloResponder(200, $resultado);
} catch (\Throwable $e) {
    mysqli_close($conexao);
    dashboardAtualizarTituloResponder(409, array('error' => $e instanceof \RuntimeException ? $e->getMessage() : 'Não foi possível concluir a atualização.'));
}
