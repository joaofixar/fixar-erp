<?php
namespace hardness;

require_once __DIR__ . '/logo-image.php';

// Incluído pela API somente após validar a sessão e o acesso ao PAINEL.
$empresaLogo = (int) $contexto['empresa'];
$conexaoLogo = null;
$nomeLogo = '';
try {
    $conexaoLogo = mysqli_init();
    mysqli_options($conexaoLogo, MYSQLI_OPT_CONNECT_TIMEOUT, 5);
    if (defined('MYSQLI_OPT_READ_TIMEOUT')) mysqli_options($conexaoLogo, MYSQLI_OPT_READ_TIMEOUT, 8);
    if (!@mysqli_real_connect($conexaoLogo, $confUsuario['dbHost'], $confUsuario['dbUser'], $confUsuario['dbPass'], $confUsuario['dbDatabase'])) throw new \RuntimeException();
    $consultaLogo = mysqli_prepare($conexaoLogo, 'SELECT C004_Logo_Marca FROM C004 WHERE C004_Id = ? LIMIT 1');
    if (!$consultaLogo) throw new \RuntimeException();
    mysqli_stmt_bind_param($consultaLogo, 'i', $empresaLogo);
    if (!mysqli_stmt_execute($consultaLogo)) throw new \RuntimeException();
    mysqli_stmt_bind_result($consultaLogo, $nomeLogo);
    mysqli_stmt_fetch($consultaLogo);
    mysqli_stmt_close($consultaLogo);
    mysqli_close($conexaoLogo);
    $conexaoLogo = null;
} catch (\Throwable $erroLogo) {
    if ($conexaoLogo) @mysqli_close($conexaoLogo);
    http_response_code(503);
    exit;
}

// Mesmo cadastro e convenção de arquivos utilizados pelo dashboard.
$extensaoLogo = strtolower(pathinfo(basename((string) $nomeLogo), PATHINFO_EXTENSION));
if (!preg_match('/^[a-z0-9]{1,10}$/', $extensaoLogo)) {
    http_response_code(404);
    exit;
}
$diretorioLogo = realpath(__DIR__ . '/../dados_usuarios/' . $confUsuario['dbDatabase'] . '/imagens/logo');
$arquivoLogo = $diretorioLogo ? realpath($diretorioLogo . DIRECTORY_SEPARATOR . $empresaLogo . '.' . $extensaoLogo) : false;
if (!$arquivoLogo || strpos($arquivoLogo, $diretorioLogo . DIRECTORY_SEPARATOR) !== 0 || !is_file($arquivoLogo)) {
    http_response_code(404);
    exit;
}
$mimeLogo = painelPersonalizadoLogoMime($arquivoLogo);
if ($mimeLogo === '') {
    http_response_code(404);
    exit;
}
header('Content-Type: ' . $mimeLogo);
header('Content-Length: ' . filesize($arquivoLogo));
readfile($arquivoLogo);
exit;
