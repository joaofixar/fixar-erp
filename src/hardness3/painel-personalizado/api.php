<?php
namespace hardness;

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/../bibliotecas/confUsuario.php';

function painelPersonalizadoResponder($codigo, $dados)
{
    http_response_code($codigo);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($dados, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}

$acao = isset($_GET['acao']) && is_string($_GET['acao']) ? $_GET['acao'] : '';
if ($acao === 'autorizar') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') painelPersonalizadoResponder(405, array('erro' => 'Método não permitido.'));
    if (!painelPersonalizadoAutorizarToken(isset($_POST['token']) ? $_POST['token'] : '', $confUsuario)) {
        painelPersonalizadoResponder(401, array('erro' => 'Acesso expirado. Reabra o PAINEL pela tela inicial do sistema.'));
    }
    painelPersonalizadoResponder(200, array('ok' => true));
}
if ($_SERVER['REQUEST_METHOD'] !== 'GET') painelPersonalizadoResponder(405, array('erro' => 'Método não permitido.'));
$contexto = painelPersonalizadoLerAutorizacao($confUsuario);
if ($contexto === false) painelPersonalizadoResponder(401, array('erro' => 'Acesso expirado. Reabra o PAINEL pela tela inicial do sistema.'));
$identidade = hash('sha256', json_encode($contexto));
if (isset($_GET['contexto']) && (!is_string($_GET['contexto']) || !hash_equals($identidade, $_GET['contexto']))) {
    painelPersonalizadoResponder(403, array('erro' => 'A empresa ou o usuário mudou em outra aba. Reabra este PAINEL pela tela inicial do sistema.'));
}
$config = painelPersonalizadoConfiguracao($contexto['empresa']);
$paineis = painelPersonalizadoPermitidos($config, $contexto);
if (!$paineis) painelPersonalizadoResponder(403, array('erro' => 'Nenhum painel habilitado para seu usuário nesta empresa.'));

if ($acao === 'logo-empresa') {
    require __DIR__ . '/company-logo.php';
    exit;
}

if ($acao === 'configuracao') {
    $publicos = array();
    foreach ($paineis as $painel) {
        $publicos[] = painelPersonalizadoDefinicaoPublica($painel);
    }
    painelPersonalizadoResponder(200, array(
        'contexto' => $identidade,
        'titulo' => $config['titulo'],
        'atualizacaoSegundos' => isset($config['atualizacaoSegundos']) ? $config['atualizacaoSegundos'] : 60,
        'alternanciaSegundos' => isset($config['alternanciaSegundos']) ? $config['alternanciaSegundos'] : 10,
        'linhasPorPagina' => isset($config['linhasPorPagina']) ? $config['linhasPorPagina'] : 8,
        'paineis' => $publicos,
    ));
}
if ($acao !== 'dados') painelPersonalizadoResponder(404, array('erro' => 'Ação não encontrada.'));
$selecionado = null;
foreach ($paineis as $painel) {
    if (isset($_GET['id']) && $_GET['id'] === $painel['id']) $selecionado = $painel;
}
if ($selecionado === null) painelPersonalizadoResponder(404, array('erro' => 'Painel indisponível para este usuário.'));
if (!isset($selecionado['tipo']) || $selecionado['tipo'] === 'mensagem') {
    painelPersonalizadoResponder(200, array('linhas' => array(), 'atualizadoEm' => date('c'), 'limitado' => false));
}
if (in_array($selecionado['tipo'], array('grafico', 'comercial'), true) && isset($selecionado['dados'])) {
    $campos = array_flip(painelPersonalizadoCamposDados($selecionado));
    $linhas = array_map(function ($linha) use ($campos) { return array_intersect_key($linha, $campos); }, $selecionado['dados']);
    painelPersonalizadoResponder(200, array('linhas' => $linhas, 'atualizadoEm' => date('c'), 'limitado' => false));
}
$erros = array();
$filtros = dashboardIndicadorFiltros($selecionado, array(), $erros);
if ($erros) painelPersonalizadoResponder(422, array('erro' => 'Revise os filtros configurados para este painel.'));
// O navegador não define empresa, filtros nem SQL. A configuração determina o recorte.
$parametros = array_merge($filtros['valores'], array('empresa' => (int) $contexto['empresa']));
$colunas = array();
foreach (painelPersonalizadoCamposDados($selecionado) as $campo) $colunas[] = 'painel_dados.`' . $campo . '`';
$sql = 'SELECT ' . implode(', ', $colunas) . ' FROM (' . painelPersonalizadoSql($selecionado) . ') painel_dados LIMIT 201';
if (isset($selecionado['ordenacao'])) {
    $sql = 'SELECT ' . implode(', ', $colunas) . ' FROM (' . painelPersonalizadoSql($selecionado) . ') painel_dados ORDER BY painel_dados.`' . $selecionado['ordenacao']['campo'] . '` ' . $selecionado['ordenacao']['direcao'] . ' LIMIT 201';
}
$conexao = null;
$linhas = false;
try {
    $conexao = mysqli_init();
    mysqli_options($conexao, MYSQLI_OPT_CONNECT_TIMEOUT, 5);
    if (defined('MYSQLI_OPT_READ_TIMEOUT')) mysqli_options($conexao, MYSQLI_OPT_READ_TIMEOUT, 8);
    if (!@mysqli_real_connect($conexao, $confUsuario['dbHost'], $confUsuario['dbUser'], $confUsuario['dbPass'], $confUsuario['dbDatabase'])) throw new \RuntimeException();
    mysqli_set_charset($conexao, 'utf8');
    @mysqli_query($conexao, 'SET SESSION MAX_EXECUTION_TIME=5000');
    if (!@mysqli_query($conexao, 'START TRANSACTION READ ONLY')) throw new \RuntimeException();
    $erro = '';
    $linhas = dashboardIndicadorConsultar($conexao, $sql, $parametros, $erro);
    @mysqli_rollback($conexao);
    mysqli_close($conexao);
    $conexao = null;
} catch (\Exception $e) {
    if ($conexao) @mysqli_close($conexao);
    $linhas = false;
}
if ($linhas === false) painelPersonalizadoResponder(503, array('erro' => 'Não foi possível atualizar os dados. Uma nova tentativa será feita automaticamente.'));
if ($selecionado['tipo'] === 'comercial') {
    $errosComercial = array();
    painelComercialValidarDados($linhas, $errosComercial);
    if ($errosComercial) painelPersonalizadoResponder(422, array('erro' => 'Dados comerciais incompletos ou inconsistentes. Revise os totais por documento e situação.'));
}
$limitado = count($linhas) > 200;
painelPersonalizadoResponder(200, array('linhas' => array_slice($linhas, 0, 200), 'limitado' => $limitado, 'atualizadoEm' => date('c')));
