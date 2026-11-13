<?php
namespace hardness;

require_once __DIR__ . '/session.php';
require_once __DIR__ . '/../bibliotecas/confUsuario.php';

function dashboardCanalLogoResponder($codigo)
{
    http_response_code($codigo);
    exit;
}

$autorizacao = dashboardPersonalizadoLerAutorizacao($confUsuario);
if ($autorizacao === false) {
    dashboardCanalLogoResponder(403);
}

$canalHexadecimal = isset($_GET['canalHex']) ? (string) $_GET['canalHex'] : '';
if (!preg_match('/^[A-Fa-f0-9]{2,400}$/', $canalHexadecimal) || strlen($canalHexadecimal) % 2 !== 0) {
    dashboardCanalLogoResponder(400);
}

$canal = trim(hex2bin($canalHexadecimal));
if ($canal === '' || strlen($canal) > 200) {
    dashboardCanalLogoResponder(400);
}

$conexao = @mysqli_connect($confUsuario['dbHost'], $confUsuario['dbUser'], $confUsuario['dbPass'], $confUsuario['dbDatabase']);
if (!$conexao) {
    dashboardCanalLogoResponder(503);
}
mysqli_set_charset($conexao, 'utf8');

$consulta = mysqli_prepare($conexao, 'SELECT GMP015_Id FROM GMP015 WHERE TRIM(GMP015_Canal) = ? LIMIT 1');
if (!$consulta) {
    mysqli_close($conexao);
    dashboardCanalLogoResponder(404);
}
mysqli_stmt_bind_param($consulta, 's', $canal);
mysqli_stmt_execute($consulta);
$canalId = 0;
mysqli_stmt_bind_result($consulta, $canalId);
mysqli_stmt_fetch($consulta);
mysqli_stmt_close($consulta);
mysqli_close($conexao);

$canalId = (int) $canalId;
if ($canalId <= 0) {
    dashboardCanalLogoResponder(404);
}

$base = realpath(__DIR__ . '/../dados_usuarios/' . basename($confUsuario['dbDatabase']) . '/logos_marketplace');
$diretorio = $base ? realpath($base . DIRECTORY_SEPARATOR . $canalId) : false;
if ($diretorio === false || strpos($diretorio, $base . DIRECTORY_SEPARATOR) !== 0 || !is_dir($diretorio)) {
    dashboardCanalLogoResponder(404);
}

$arquivoImagem = false;
foreach ((array) scandir($diretorio) as $nomeArquivo) {
    if ($nomeArquivo === '.' || $nomeArquivo === '..') {
        continue;
    }
    $candidato = realpath($diretorio . DIRECTORY_SEPARATOR . $nomeArquivo);
    if ($candidato === false || strpos($candidato, $diretorio . DIRECTORY_SEPARATOR) !== 0 || !is_file($candidato)) {
        continue;
    }
    $imagem = @getimagesize($candidato);
    if (is_array($imagem) && isset($imagem['mime']) && strpos($imagem['mime'], 'image/') === 0) {
        $arquivoImagem = $candidato;
        break;
    }
}

if ($arquivoImagem === false) {
    dashboardCanalLogoResponder(404);
}

$imagem = getimagesize($arquivoImagem);
header('Content-Type: ' . $imagem['mime']);
header('Content-Length: ' . filesize($arquivoImagem));
header('Cache-Control: private, max-age=3600');
readfile($arquivoImagem);
