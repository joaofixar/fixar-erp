<?php
namespace hardness;
// Apenas fixtures e captura: não carrega credenciais nem abre conexão MySQL.
require dirname(__DIR__) . '/carregamento-progressivo.php';
function conferirLotes($valor, $mensagem) { if (!$valor) throw new \RuntimeException($mensagem); }
$GLOBALS['fixtureLotes'] = array('consultas' => array(), 'roles' => 0, 'timeout' => false, 'encerrarTempo' => false, 'falhar' => false);
$GLOBALS['fixtureRoles'] = array(array('usuarioId' => 7, 'supervisorId' => 79, 'empresaId' => 1));
function dashboardIndicadorConsultarDireto($conexao, $sql, $params, &$erro) {
    if (strpos($sql, 'SELECT v.C007_Id AS usuarioId') === 0) {
        $GLOBALS['fixtureLotes']['roles']++;
        return $GLOBALS['fixtureRoles'];
    }
    $GLOBALS['fixtureLotes']['consultas'][] = array('sql' => $sql, 'parametros' => $params);
    $GLOBALS['dashboardConsultaErroNumero'] = 0;
    if ($GLOBALS['fixtureLotes']['encerrarTempo']) $GLOBALS['dashboardCarga']['lotesAgrupados']['inicio'] = microtime(true) - 2;
    if ($GLOBALS['fixtureLotes']['falhar']) { $erro = 'Erro artificial'; return false; }
    if (isset($params['cenario']) && $params['cenario'] === 'paginacao') {
        preg_match('/OFFSET (\d+)$/', $sql, $match);
        $offset = (int) $match[1];
        if ($GLOBALS['fixtureLotes']['timeout'] && $params['loteInicio'] === '2026-10-01' && $params['loteFim'] === '2026-10-31' && $offset === 2000) {
            $GLOBALS['dashboardConsultaErroNumero'] = 3024; $erro = 'Timeout artificial'; return false;
        }
        $rows = array();
        for ($id = 1; $id <= 5003; $id++) {
            $data = '2026-10-' . str_pad(($id % 31) + 1, 2, '0', STR_PAD_LEFT);
            if ($data >= $params['loteInicio'] && $data <= $params['loteFim']) $rows[] = array('id' => $id, 'data' => $data, 'valor' => $id);
        }
        return array_slice($rows, $offset, 2000);
    }
    if (strpos($sql, 'WHERE 1=0') !== false) return array();
    return array(array('id' => 1, 'data' => isset($params['loteInicio']) ? $params['loteInicio'] : null,
        'valor' => 123.45, 'fonte' => isset($params['fonte']) ? $params['fonte'] : 'auxiliar'));
}
function indicadorLotes($agrupar) {
    return array('id' => 'teste_lotes_comerciais', 'desempenhoComercial' => array('habilitado' => true, 'carregamentoProgressivo' => $agrupar));
}
function iniciarLotes($token, $agrupar = true, $leitura = false, $indicador = null) {
    $entrada = array('f_dataInicio' => '2026-01-01', 'f_dataFim' => '2026-11-13');
    if ($token !== '') $entrada['cargaToken'] = $token;
    dashboardCargaInicializar(null, array('usuario' => 7, 'empresa' => 1), $indicador === null ? indicadorLotes($agrupar) : $indicador,
        $entrada, 'tenant-artificial', $leitura);
    return $GLOBALS['dashboardCarga']['token'];
}
function limparLotes($token) {
    $dir = dashboardCargaDiretorio() . '/' . $token;
    foreach (glob($dir . '/*') as $file) unlink($file);
    rmdir($dir);
}
function executarCargaLotes($agrupar, $fontes) {
    $token = ''; $resposta = array(); $erro = ''; $chamadas = array();
    $rolesAntes = $GLOBALS['fixtureLotes']['roles'];
    $sqlAntes = count($GLOBALS['fixtureLotes']['consultas']);
    for ($requisicao = 1; $requisicao <= 100; $requisicao++) {
        $token = iniciarLotes($token, $agrupar);
        $antes = count($GLOBALS['fixtureLotes']['consultas']);
        try {
            foreach ($fontes as $nome => $fonte) {
                $linhas = dashboardCargaConsultar(null, $fonte['sql'], $fonte['parametros'], $erro);
                conferirLotes($linhas !== false, $nome . ': ' . $erro);
                $resposta[$nome] = $linhas;
            }
            $chamadas[] = count($GLOBALS['fixtureLotes']['consultas']) - $antes;
            dashboardCargaFinalizar(true);
            break;
        } catch (DashboardCargaPendente $e) {
            $progresso = $GLOBALS['dashboardCarga']['progresso'];
            conferirLotes(isset($progresso['token'], $progresso['intervalo'], $progresso['registros']), 'Todo 202 continua com progresso válido');
            $chamadas[] = count($GLOBALS['fixtureLotes']['consultas']) - $antes;
            dashboardCargaFinalizar();
        }
    }
    conferirLotes($requisicao <= 100 && count($resposta) === count($fontes), 'Concluir todas as fontes');
    conferirLotes(max($chamadas) <= ($agrupar ? 6 : 1), 'Limite de consultas respeitado por requisição');
    conferirLotes($GLOBALS['fixtureLotes']['roles'] - $rolesAntes === $requisicao, 'Revalidar permissões em toda requisição HTTP');
    $trace = array_slice($GLOBALS['fixtureLotes']['consultas'], $sqlAntes);
    // Uma exportação concluída relê o snapshot, sem executar fontes novamente.
    $antes = count($GLOBALS['fixtureLotes']['consultas']);
    iniciarLotes($token, $agrupar, true);
    foreach ($fontes as $nome => $fonte) conferirLotes(dashboardCargaConsultar(null, $fonte['sql'], $fonte['parametros'], $erro) === $resposta[$nome], 'Exportação preserva ' . $nome);
    dashboardCargaFinalizar();
    conferirLotes(count($GLOBALS['fixtureLotes']['consultas']) === $antes, 'Exportação não reconsulta fontes');
    limparLotes($token);
    return array('linhas' => $resposta, 'requisicoes' => $requisicao, 'trace' => $trace, 'consultasPorRequisicao' => $chamadas);
}

// Quatro fontes mensais + nove fontes auxiliares = 53 consultas úteis.
$fontes = array();
foreach (array('orcamentos', 'pedidos', 'notas', 'cancelamentos') as $nome) $fontes[$nome] = array(
    'sql' => "SELECT clienteId, responsavelId, SUM(valor) AS valor FROM (SELECT T003_Id AS clienteId, 7 AS responsavelId, T003_Data_Emissao AS data, T003_Valor_Total AS valor FROM T003) AS comercial_base GROUP BY clienteId, responsavelId LIMIT 12001",
    'parametros' => array('empresa' => 1, 'dataInicio' => '2026-01-01', 'dataFim' => '2026-11-13', 'fonte' => $nome));
for ($n = 0; $n < 9; $n++) $fontes['aux' . $n] = array('sql' => 'SELECT C007_Id AS id FROM C007 WHERE C007_Id=:empresa', 'parametros' => array('empresa' => 1, 'fonte' => 'aux' . $n));
$anterior = executarCargaLotes(false, $fontes);
$otimizado = executarCargaLotes(true, $fontes);
conferirLotes($anterior['requisicoes'] === 54 && $otimizado['requisicoes'] === 9, 'Fixture anual: reduzir 54 requisições para nove');
conferirLotes($anterior['trace'] === $otimizado['trace'], 'Mesmas consultas, SQL, filtros, datas e sequência de lotes');
conferirLotes($anterior['linhas'] === $otimizado['linhas'], 'Mesmos dados e totais para Todas as equipes/vendedores');
echo "OK: fixture anual, 54 para 9 requisições, 53 consultas idênticas, permissões em cada HTTP e exportação do snapshot.\n";

// Timeout depois de gravar uma página: descarta somente a janela e subdivide.
$GLOBALS['fixtureLotes']['timeout'] = true;
$pagina = array('paginacao' => array('sql' => 'SELECT d.T003_Id AS id FROM T003 d WHERE d.T003_C004_Id=:empresa ORDER BY d.T003_Id LIMIT 12001',
    'parametros' => array('empresa' => 1, 'dataInicio' => '2026-10-01', 'dataFim' => '2026-11-30', 'cenario' => 'paginacao')));
$legado = executarCargaLotes(false, $pagina);
$novo = executarCargaLotes(true, $pagina);
$linhas = $novo['linhas']['paginacao'];
conferirLotes($legado['trace'] === $novo['trace'] && $legado['linhas'] === $novo['linhas'], 'Timeout e subdivisão conservam consultas e resultados');
conferirLotes(count($linhas) === 5003 && count(array_unique(array_column($linhas, 'id'))) === 5003, '5.003 documentos completos sem duplicação');
conferirLotes(array_sum(array_column($linhas, 'valor')) === 5003 * 5004 / 2, 'Mesmo total após rollback de janela');
$GLOBALS['fixtureLotes']['timeout'] = false;
echo "OK: paginação, timeout na segunda página, rollback parcial, subdivisão e 5.003 documentos preservados.\n";

// Orçamento de tempo interrompe antes da segunda consulta; sem sleep real.
$token = iniciarLotes('');
$GLOBALS['fixtureLotes']['encerrarTempo'] = true;
$antes = count($GLOBALS['fixtureLotes']['consultas']); $pendente = false; $erro = '';
try { dashboardCargaConsultar(null, $fontes['notas']['sql'], $fontes['notas']['parametros'], $erro); }
catch (DashboardCargaPendente $e) { $pendente = true; }
conferirLotes($pendente && count($GLOBALS['fixtureLotes']['consultas']) === $antes + 1, 'Uma consulta lenta devolve progresso antes de iniciar a próxima');
dashboardCargaFinalizar(); $GLOBALS['fixtureLotes']['encerrarTempo'] = false;
// Com orçamento já vencido antes de consultar, ainda obtém um primeiro lote e
// token válido. O browser consegue continuar, sem receber progresso vazio.
iniciarLotes($token); $GLOBALS['dashboardCarga']['lotesAgrupados']['inicio'] = microtime(true) - 2;
$antes = count($GLOBALS['fixtureLotes']['consultas']); $pendente = false;
try { dashboardCargaConsultar(null, $fontes['notas']['sql'], $fontes['notas']['parametros'], $erro); }
catch (DashboardCargaPendente $e) { $pendente = true; }
conferirLotes($pendente && count($GLOBALS['fixtureLotes']['consultas']) === $antes + 1, 'Primeiro lote assegura progresso válido mesmo após replay demorado');
dashboardCargaFinalizar();
// Alterar os vínculos entre chamadas invalida a assinatura do snapshot.
$GLOBALS['fixtureRoles'][] = array('usuarioId' => 7, 'supervisorId' => 89, 'empresaId' => 5);
$negado = false;
try { iniciarLotes($token); dashboardCargaFinalizar(); } catch (\RuntimeException $e) { $negado = true; }
conferirLotes($negado, 'Mudança de permissões invalida a continuação');
array_pop($GLOBALS['fixtureRoles']); limparLotes($token);
echo "OK: orçamento de tempo, continuação válida e invalidação por mudança de permissões.\n";

// Fontes vazias e erros definitivos mantêm seus contratos.
$token = iniciarLotes('');
$vazio = dashboardCargaConsultar(null, 'SELECT C007_Id AS id FROM C007 WHERE 1=0', array(), $erro);
conferirLotes($vazio === array(), 'Fonte vazia retorna lista vazia');
$GLOBALS['fixtureLotes']['falhar'] = true;
$antes = count($GLOBALS['fixtureLotes']['consultas']);
conferirLotes(dashboardCargaConsultar(null, 'SELECT C007_Id AS id FROM C007', array(), $erro) === false && $erro === 'Erro artificial', 'Erro definitivo não é repetido nem ocultado');
conferirLotes(count($GLOBALS['fixtureLotes']['consultas']) === $antes + 1, 'Erro definitivo executado uma única vez');
$GLOBALS['fixtureLotes']['falhar'] = false; dashboardCargaFinalizar(); limparLotes($token);
// Cotações e modo geral não aderem ao agrupamento comercial.
foreach (array(array('id' => 'cotacoes', 'painelCotacoes' => array('habilitado' => true, 'carregamentoProgressivo' => true)), array('id' => 'geral', 'carregamentoProgressivo' => true)) as $indicador) {
    $token = iniciarLotes('', false, false, $indicador);
    conferirLotes(empty($GLOBALS['dashboardCarga']['lotesAgrupados']), 'Agrupamento restrito ao componente comercial');
    dashboardCargaFinalizar(); limparLotes($token);
}
echo "OK: vazios, erros definitivos, Cotações e carregamento geral preservados. Sem conexão externa.\n";
