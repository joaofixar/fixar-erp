<?php
namespace hardness;

require_once __DIR__ . '/session.php';
require_once __DIR__ . '/../bibliotecas/confUsuario.php';
require_once __DIR__ . '/access.php';

function dashboardLogoEmpresaResponder($codigo)
{
    http_response_code($codigo);
    exit;
}

$autorizacao = dashboardPersonalizadoLerAutorizacao($confUsuario);
if ($autorizacao === false) {
    dashboardLogoEmpresaResponder(403);
}

$empresa = isset($autorizacao['empresa']) ? (int) $autorizacao['empresa'] : 0;
if ($empresa <= 0) {
    dashboardLogoEmpresaResponder(404);
}

$conexao = @mysqli_connect($confUsuario['dbHost'], $confUsuario['dbUser'], $confUsuario['dbPass'], $confUsuario['dbDatabase']);
if (!$conexao) {
    dashboardLogoEmpresaResponder(503);
}
mysqli_set_charset($conexao, 'utf8');
$consulta = mysqli_prepare($conexao, 'SELECT C004_Logo_Marca FROM C004 WHERE C004_Id = ? LIMIT 1');
if (!$consulta) {
    mysqli_close($conexao);
    dashboardLogoEmpresaResponder(503);
}
mysqli_stmt_bind_param($consulta, 'i', $empresa);
mysqli_stmt_execute($consulta);
mysqli_stmt_bind_result($consulta, $nomeArquivo);
$encontrouLogo = mysqli_stmt_fetch($consulta);
mysqli_stmt_close($consulta);
mysqli_close($conexao);
$nomeArquivo = $encontrouLogo ? basename((string) $nomeArquivo) : '';
if ($nomeArquivo === '') {
    dashboardLogoEmpresaResponder(404);
}

// No cadastro, a extensão é preservada em C004_Logo_Marca, mas o arquivo
// físico é gravado com o código da empresa: imagens/logo/<C004_Id>.<extensão>.
$extensao = strtolower((string) pathinfo($nomeArquivo, PATHINFO_EXTENSION));
if (!preg_match('/^[a-z0-9]{1,10}$/', $extensao)) {
    dashboardLogoEmpresaResponder(400);
}
$nomeArquivo = $empresa . '.' . $extensao;

$diretorio = realpath(__DIR__ . '/../dados_usuarios/' . $confUsuario['dbDatabase'] . '/imagens/logo');
$arquivo = $diretorio ? realpath($diretorio . DIRECTORY_SEPARATOR . $nomeArquivo) : false;
if ($arquivo === false || strpos($arquivo, $diretorio . DIRECTORY_SEPARATOR) !== 0 || !is_file($arquivo)) {
    dashboardLogoEmpresaResponder(404);
}

$imagem = @getimagesize($arquivo);
$mime = is_array($imagem) && isset($imagem['mime']) ? $imagem['mime'] : '';
if (strpos($mime, 'image/') !== 0) {
    dashboardLogoEmpresaResponder(404);
}

header('Content-Type: ' . $mime);
header('Content-Length: ' . filesize($arquivo));
header('Cache-Control: private, max-age=3600');
readfile($arquivo);
