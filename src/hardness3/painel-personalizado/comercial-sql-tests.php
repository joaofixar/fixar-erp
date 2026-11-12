<?php
require_once __DIR__ . '/config.php';

$arquivo = __DIR__ . '/../configuracoes/painel-comercial-inecel-root-63.json';
$configuracao = json_decode(file_get_contents($arquivo), true);
$erros = array();
if (!hardness\painelPersonalizadoValidarConfiguracao($configuracao, $erros)) {
    throw new RuntimeException('JSON comercial inválido: ' . implode('; ', $erros));
}

$sql = hardness\painelPersonalizadoSql($configuracao['paineis'][0]);
if (!empty($configuracao['paineis'][0]['demonstracao']) || isset($configuracao['paineis'][0]['dados']) || isset($configuracao['paineis'][0]['referencia']) || $configuracao['usuariosPermitidos'] !== array(63)) {
    throw new RuntimeException('O painel deve usar dados ao vivo e manter o usuário 63 configurado.');
}
if (substr_count($sql, 'UNION ALL') !== 8) {
    throw new RuntimeException('O SQL comercial deve produzir nove situações.');
}

$erro = '';
if (!hardness\dashboardIndicadorValidarSql($sql, $erro, false, true)) {
    throw new RuntimeException('UNION ALL comercial recusado: ' . $erro);
}
if (hardness\dashboardIndicadorValidarSql($sql, $erro)) {
    throw new RuntimeException('UNION ALL ficou disponível fora do painel comercial.');
}
if (hardness\dashboardIndicadorValidarSql(str_replace('UNION ALL', 'UNION', $sql), $erro, false, true)) {
    throw new RuntimeException('UNION sem ALL foi aceito.');
}

echo "SQL comercial e isolamento do validador: OK\n";
