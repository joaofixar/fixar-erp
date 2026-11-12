<?php
namespace hardness;

require_once __DIR__ . '/session.php';
require_once __DIR__ . '/../bibliotecas/confUsuario.php';

function dashboardImagemProdutoResponder($codigo)
{
    http_response_code($codigo);
    exit;
}

if (dashboardPersonalizadoLerAutorizacao($confUsuario) === false) {
    dashboardImagemProdutoResponder(401);
}

$produtoId = isset($_GET['produto']) ? (string) $_GET['produto'] : '';
$fotoId = isset($_GET['foto']) ? (string) $_GET['foto'] : '';
if (!ctype_digit($produtoId) || !ctype_digit($fotoId) || (int) $produtoId <= 0 || (int) $fotoId <= 0) {
    dashboardImagemProdutoResponder(400);
}

$diretorio = realpath(__DIR__ . '/../dados_usuarios/' . $confUsuario['dbDatabase'] . '/produtos/' . $produtoId . '/fotos');
if ($diretorio === false || !is_dir($diretorio)) {
    dashboardImagemProdutoResponder(404);
}

$arquivos = glob($diretorio . DIRECTORY_SEPARATOR . $fotoId . '.*');
$arquivo = !empty($arquivos) ? realpath($arquivos[0]) : false;
if ($arquivo === false || strpos($arquivo, $diretorio . DIRECTORY_SEPARATOR) !== 0 || !is_file($arquivo)) {
    dashboardImagemProdutoResponder(404);
}

$imagem = @getimagesize($arquivo);
$mime = is_array($imagem) && isset($imagem['mime']) ? $imagem['mime'] : '';
if (strpos($mime, 'image/') !== 0) {
    dashboardImagemProdutoResponder(404);
}

header('Content-Type: ' . $mime);
header('Content-Length: ' . filesize($arquivo));
header('Cache-Control: private, max-age=3600');
readfile($arquivo);
