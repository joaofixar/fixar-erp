<?php
namespace hardness;

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/../bibliotecas/confUsuario.php';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/access.php';
require_once __DIR__ . '/custom-indicator-engine.php';

function dashboardAtualizarVendedorResponder($codigo, $dados)
{
    http_response_code($codigo);
    echo json_encode($dados, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    dashboardAtualizarVendedorResponder(405, array('error' => 'Método não permitido.'));
}
$autorizacao = dashboardPersonalizadoLerAutorizacao($confUsuario);
if ($autorizacao === false) dashboardAtualizarVendedorResponder(401, array('error' => 'Sessão expirada. Abra novamente o dashboard.'));
if (!dashboardClientesInativosPodeAtualizar($autorizacao)) {
    dashboardAtualizarVendedorResponder(403, array('error' => 'A atualização exige acesso de diretor ou ROOT.'));
}
$recebido = json_decode(file_get_contents('php://input'), true);
$dados = dashboardClientesInativosValidarAssinatura(isset($recebido['ticket']) ? $recebido['ticket'] : '', $autorizacao, $confUsuario);
if ($dados === false) dashboardAtualizarVendedorResponder(409, array('error' => 'A sugestão expirou ou é inválida. Atualize o indicador.'));

$configuracao = dashboardPersonalizadoConfiguracao($autorizacao['empresa'], $erros);
if ($configuracao === false) dashboardAtualizarVendedorResponder(403, array('error' => 'Indicador indisponível.'));
$ids = array();
foreach ($configuracao['indicadores'] as $indicador) $ids[$indicador['id']] = true;
$acessos = dashboardPersonalizadoLerAcessos($autorizacao['empresa'], $ids);
$permitido = false;
foreach (dashboardPersonalizadoIndicadoresPermitidos($autorizacao, $configuracao, $acessos) as $indicador) {
    if ($indicador['id'] === $dados['indicador'] && dashboardClientesInativosHabilitado($indicador)
        && dashboardPersonalizadoRestricaoUsuarioIndicador($autorizacao, $acessos, $indicador['id']) === false) {
        $permitido = true;
    }
}
if (!$permitido) dashboardAtualizarVendedorResponder(403, array('error' => 'Você não possui acesso à atualização neste indicador.'));

mysqli_report(MYSQLI_REPORT_OFF);
$conexao = @mysqli_connect($confUsuario['dbHost'], $confUsuario['dbUser'], $confUsuario['dbPass'], $confUsuario['dbDatabase']);
if (!$conexao) dashboardAtualizarVendedorResponder(503, array('error' => 'Não foi possível acessar o cadastro.'));
mysqli_set_charset($conexao, 'utf8mb4');
@mysqli_query($conexao, 'SET SESSION MAX_EXECUTION_TIME=5000');
@mysqli_query($conexao, 'SET SESSION innodb_lock_wait_timeout=5');
try {
    $resultado = dashboardClientesInativosAtualizar($conexao, $dados, $autorizacao);
    mysqli_close($conexao);
    dashboardAtualizarVendedorResponder(200, $resultado);
} catch (\Throwable $e) {
    mysqli_close($conexao);
    dashboardAtualizarVendedorResponder(409, array('error' => $e instanceof \RuntimeException ? $e->getMessage() : 'Não foi possível concluir a atualização. Atualize o indicador.'));
}
