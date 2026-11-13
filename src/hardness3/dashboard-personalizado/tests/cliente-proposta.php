<?php
namespace hardness;
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once dirname(__DIR__) . '/custom-indicator-engine.php';
$config = json_decode(file_get_contents(isset($argv[1]) ? $argv[1] : __DIR__ . '/fixtures/cliente-proposta.json'), true);
$queries = array();
foreach ($config['indicadores'] as $indicator) {
    if ($indicator['id'] === 'desempenho_vendedores') {
        foreach (array($indicator['sqlBase'], $indicator['desempenhoComercial']['orcamentos'], $indicator['desempenhoComercial']['pedidos']) as $sql) {
            if (!dashboardIndicadorValidarSql(implode("\n", $sql), $error)) throw new \RuntimeException($error);
        }
    }
    if ($indicator['id'] === 'inecel_desempenho_comercial') {
        $panel = $indicator['painelCotacoes'];
        // Teste offline: só empresas com ID explicitamente configurado; não resolve CNPJ em banco.
        $panel['_empresas'] = array_values(array_filter($panel['empresasConfiguradas'], function ($e) { return !empty($e['id']); }));
        foreach (array('orcamentos', 'pedidos', 'notas') as $kind) {
            $queries[$kind] = dashboardCotacoesSql($kind, $panel);
            if (!dashboardIndicadorValidarSql($queries[$kind], $error)) throw new \RuntimeException($error);
        }
    }
}
echo json_encode($queries);
