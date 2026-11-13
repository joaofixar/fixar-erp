<?php
// Fixtures locais. Não carrega credenciais nem conecta à base de cliente.
namespace hardness;
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/access.php';
require_once dirname(__DIR__) . '/custom-indicator-engine.php';
function conferir($condicao, $mensagem) { if (!$condicao) throw new \RuntimeException($mensagem); }
// O PHP CLI mínimo deste ambiente não possui mbstring; as fixtures são UTF-8.
function mb_substr($texto, $inicio, $tamanho, $encoding) { return implode('', array_slice(preg_split('//u', $texto, -1, PREG_SPLIT_NO_EMPTY), $inicio, $tamanho)); }
$arquivo = __DIR__ . '/fixtures/desempenho-comercial.json';
foreach ($argv as $arg) if (strpos($arg, '--config=') === 0) $arquivo = substr($arg, 9);
$config = json_decode(file_get_contents($arquivo), true);
$candidatos = array_values(array_filter($config['indicadores'], function ($i) {
    return !empty($i['abas'][0]['desempenhoComercial']['habilitado']);
}));
conferir(count($candidatos) === 1, 'Selecionar um único indicador de desempenho comercial');
$indicador = $candidatos[0];
conferir(dashboardPersonalizadoValidarConfiguracao($config, $erros, true), implode(' ', $erros));
foreach ($indicador['abas'] as $aba) {
    foreach (array($aba['sqlBase'], $aba['desempenhoComercial']['orcamentos'], $aba['desempenhoComercial']['pedidos']) as $sql) {
        conferir(dashboardIndicadorValidarSql(implode("\n", $sql), $erro), $erro);
    }
}
$erro = '';
foreach (array(array('2026-03-01', '2026-02-28'), array('2025-01-01', '2026-01-01'), array('', ''), array('2026-02-30', '2026-03-01')) as $p) {
    conferir(dashboardComercialPeriodo(array('dataInicio' => $p[0], 'dataFim' => $p[1]), $erro) === false, 'Rejeitar período inválido');
}
function linha($clienteId, $responsavelId, $mes, $valor, $quantidade, $convertidos = 0) {
    return array('clienteId' => $clienteId, 'responsavelId' => $responsavelId, 'cliente' => 'Nome repetido', 'responsavel' => 'Nome repetido', 'mes' => $mes, 'valor' => $valor, 'quantidade' => $quantidade, 'convertidos' => $convertidos);
}
$fontes = array('orcamentos' => array(linha('cad:1', 7, '2026-04', 1000, 2, 1), linha('cad:1', 7, '2026-03', 2000, 3, 2)),
    'pedidos' => array(linha('cad:1', 7, '2026-04', 800, 2)),
    'notas' => array(linha('cad:1', 7, '2026-04', 600, 2), linha('cad:1', 8, '2026-04', 200, 1), linha('cad:2', 7, '2026-03', 300, 1)));
$periodo = array('inicio' => '2026-01-01', 'fim' => '2026-04-15');
$r = dashboardComercialConsolidar($fontes, $periodo, '2026-04-15');
conferir(count($r['linhas']) === 3, 'Agrupar por IDs, preservando homônimos e responsáveis diferentes');
conferir($r['totais'] === array('orcamentos' => 3000.0, 'pedidos' => 800.0, 'notas' => 1100.0), 'Não somar etapas diferentes como vendas');
conferir($r['resumo']['faturamento'] === 800.0 && $r['resumo']['projecao'] === 1600.0, 'Projeção por dias corridos do mês de referência');
conferir($r['resumo']['valorOrcado'] === 3000.0, 'Valor orçado soma somente orçamentos de todo o período filtrado');
conferir($r['resumo']['quantidadeOrcamentos'] === 5 && $r['resumo']['orcamentosConvertidos'] === 3, 'Numerador e denominador cobrem o mesmo período');
conferir($r['resumo']['conversao'] === 60 && $r['resumo']['clientesAtendidos'] === 1, 'Conversão por quantidade de orçamentos, não por valores ou quantidade de pedidos');
conferir($r['resumo']['ticketMedio'] === 400.0, 'Ticket por pedido usa a quantidade de pedidos, independente das notas');
conferir($r['resumo']['ticketMedioOrcamento'] === 500.0, 'Ticket por orçamento usa valor e quantidade do mês final, não o acumulado do período');
$ponderadas = $fontes;
$ponderadas['orcamentos'][] = linha('cad:2', 8, '2026-04', 900, 1);
$ponderado = dashboardComercialConsolidar($ponderadas, $periodo, '2026-04-15');
conferir(abs($ponderado['resumo']['ticketMedioOrcamento'] - 1900 / 3) < 0.00001, 'Ticket consolidado divide a soma pela quantidade, sem média simples dos clientes');
conferir($r['totaisMeses']['2026-02']['notas'] === 0.0 && count($r['meses']) === 12, 'Matriz anual contém meses vazios');
conferir($r['resumo']['meta'] === null && $r['resumo']['atingimentoMeta'] === null, 'Não inventar meta');
$zero = dashboardComercialConsolidar(array('orcamentos' => array(), 'pedidos' => array(), 'notas' => array()), $periodo, '2026-04-15');
conferir($zero['resumo']['conversao'] === null && $zero['resumo']['ticketMedio'] === null && $zero['resumo']['ticketMedioOrcamento'] === null, 'Divisões sem denominador');
$semValor = dashboardComercialConsolidar(array('orcamentos' => array(linha('cad:1', 7, '2026-04', 0, 2)), 'pedidos' => array(), 'notas' => array()), $periodo, '2026-04-15');
conferir($semValor['resumo']['ticketMedioOrcamento'] === 0.0 && $semValor['resumo']['ticketMedio'] === null, 'Orçamentos de valor zero têm ticket zero, sem criar ticket de pedido');
$parcial = dashboardComercialConsolidar($fontes, array('inicio' => '2026-04-10', 'fim' => '2026-04-15'), '2026-04-15');
conferir($parcial['resumo']['projecao'] === null, 'Sem projeção com começo do mês excluído');
$futuro = dashboardComercialConsolidar($fontes, $periodo, '2026-04-01');
conferir($futuro['resumo']['projecao'] === null, 'Sem projeção com data futura');
conferir(count(dashboardComercialResumoExportacao($r)) === 11, 'Resumo exportável com os dois tickets');
$resumoExportado = dashboardComercialResumoExportacao($r);
conferir($resumoExportado[4] === array('Ticket médio por pedido', 400.0, 'moeda') && $resumoExportado[5] === array('Ticket médio por orçamento', 500.0, 'moeda'), 'CSV/PDF incluem os mesmos tickets calculados na tela');
conferir(dashboardComercialResumoExportacao($zero)[5] === array('Ticket médio por orçamento', 'Não aplicável', 'texto'), 'Exportar ticket sem orçamento como não aplicável');
conferir($resumoExportado[6] === array('Valor orçado no período filtrado', 3000.0, 'moeda'), 'Exportação usa valor orçado do período');
conferir($resumoExportado[7] === array('Conversão dos orçamentos do período em pedidos', 60, 'percentual'), 'Exportação usa conversão do período');
$historico = dashboardComercialConsolidar(array('orcamentos' => array(linha('cad:1', 7, '2026-03', 2000, 3, 3)), 'pedidos' => array(), 'notas' => array()), $periodo, '2026-04-15');
conferir($historico['resumo']['valorOrcado'] === 2000.0 && $historico['resumo']['conversao'] === 100, 'Orçamentos anteriores contam mesmo sem movimento no mês final');
conferir($historico['resumo']['ticketMedioOrcamento'] === null, 'Não reutilizar orçamentos de outro mês no ticket do mês de referência');
$naoConvertidos = dashboardComercialConsolidar(array('orcamentos' => array(linha('cad:1', 7, '2026-03', 2000, 3, 0)), 'pedidos' => array(), 'notas' => array()), $periodo, '2026-04-15');
conferir($naoConvertidos['resumo']['conversao'] === 0, 'Exibir zero quando nenhum orçamento do período gerou pedido');

foreach (array(
    array('2026-10-01','2026-10-14','2026-10-01','2026-09-01','2026-09-14'),
    array('2026-10-05','2026-10-14','2026-10-05','2026-09-05','2026-09-14'),
    array('2026-01-01','2026-10-14','2026-10-01','2026-09-01','2026-09-14'),
    array('2026-01-01','2026-01-14','2026-01-01','2025-12-01','2025-12-14'),
    array('2026-03-01','2026-03-31','2026-03-01','2026-02-01','2026-02-28'),
    array('2024-03-01','2024-03-31','2024-03-01','2024-02-01','2024-02-29'),
    array('2026-03-30','2026-03-31','2026-03-30','2026-02-28','2026-02-28'),
    array('2026-02-01','2026-02-28','2026-02-01','2026-01-01','2026-01-28')
) as $caso) {
    $p=dashboardComercialPeriodosComparacao(array('inicio'=>$caso[0],'fim'=>$caso[1]));
    conferir($p===array('atual'=>array('inicio'=>$caso[2],'fim'=>$caso[1]),'anterior'=>array('inicio'=>$caso[3],'fim'=>$caso[4])),'Comparar os mesmos dias sem transbordar fevereiro ou a virada de ano');
}
foreach (array(array(120,100,20,'alta'),array(80,100,-20,'baixa'),array(100,100,0,'estavel'),
    array(0,0,0,'estavel'),array(100,0,null,'alta'),array(-100,0,null,'baixa'),array(0,100,-100,'baixa'),
    array(-80,-100,20,'alta'),array(-120,-100,-20,'baixa'),array(null,100,null,'sem_base'),array(100,null,null,'sem_base')) as $caso) {
    $v=dashboardComercialVariacao($caso[0],$caso[1]);
    conferir($v['percentual']===$caso[2] || ($v['percentual']!==null && $caso[2]!==null && abs($v['percentual']-$caso[2])<0.001),'Percentual com base zero, negativa ou ausente');
    conferir($v['tendencia']===$caso[3],'Direção da variação');
}

// Exercita os SQLs preparados do executor com mysqli substituído por fixtures.
if (!defined('MYSQLI_ASSOC')) define('MYSQLI_ASSOC', 1);
class ComercialStatement {
    public $sql; public $conexao; public $valores;
    public function bind_param($tipos, &...$valores) { $this->valores = $valores; return true; }
}
function mysqli_prepare($conexao, $sql) { $s = new ComercialStatement(); $s->conexao = $conexao; $s->sql = $sql; return $s; }
function mysqli_stmt_execute($stmt) { $stmt->conexao->consultas[] = array('sql' => $stmt->sql, 'valores' => $stmt->valores); return true; }
function mysqli_stmt_close($stmt) {}
function mysqli_fetch_all($linhas, $modo) { return $linhas; }
function mysqli_stmt_get_result($s) {
    if (strpos($s->sql, 'SELECT DISTINCT CASE') === 0) return array(array('valor'=>'gian','titulo'=>'Gian'),array('valor'=>'humberto_rafaela','titulo'=>'Humberto / Rafaela'),array('valor'=>'william','titulo'=>'William'));
    if (strpos($s->sql, 'AS comercial_metas') !== false) return $s->conexao->metas;
    if (strpos($s->sql, 'AS comercial_territorios') !== false) return $s->conexao->territorios;
    if (strpos($s->sql, 'AS comercial_regioes') !== false) return $s->conexao->regioes;
    if (strpos($s->sql, 'AS comercial_carteira') !== false) return $s->conexao->carteira;
    if (strpos($s->sql, 'SELECT DISTINCT C007_Id') === 0) return array(array('valor' => '7', 'titulo' => 'Vendedor teste'));
    if (strpos($s->sql, 'SELECT D021_Id') === 0) return array(array('valor' => '31', 'titulo' => 'Representante teste'));
    $tipo = strpos($s->sql, 'FROM T003') !== false ? 'orcamentos' : (strpos($s->sql, 'FROM T005') !== false ? 'pedidos' : 'notas');
    if (strpos($s->sql, 'AS comercial_comparativo') !== false) return isset($s->conexao->anteriores) ? $s->conexao->anteriores[$tipo] : array(array('valor'=>0,'quantidade'=>0));
    return $s->conexao->fontes[$tipo];
}
$auth = array('usuario' => 7, 'perfil' => 4, 'diretor' => false, 'root' => false);
$acessos = array('perfisGestores' => array(), 'perfis' => array(), 'usuarios' => array(7 => array('indicadores' => array($indicador['id']), 'restricoes' => array($indicador['id'] => array('vendedorId' => 7, 'vendedorNome' => 'Vendedor teste')))));
conferir(count(dashboardPersonalizadoIndicadoresPermitidos($auth, $config, $acessos)) === 1, 'Liberação por usuário');
$negado = $auth; $negado['usuario'] = 99;
conferir(count(dashboardPersonalizadoIndicadoresPermitidos($negado, $config, $acessos)) === 0, 'Usuário sem liberação');
$restricao = dashboardPersonalizadoRestricaoUsuarioIndicador($auth, $acessos, $indicador['id']);
conferir(dashboardPersonalizadoAbaVendedores($indicador) === 'vendedores', 'Somente aba de vendedores para acesso individual');
$entrada = array('f_vendedor' => '999', 'f_dataInicio' => '1900-01-01', 'f_dataFim' => '2100-01-01', 'f_empresa' => '999');
$selecionado = array_merge($indicador, $indicador['abas'][0]);
$selecionado = dashboardPersonalizadoAplicarRestricaoUsuario($selecionado, $restricao, $entrada, $erro);
conferir($entrada['f_vendedor'] === '7' && !isset($entrada['f_dataInicio']), 'Servidor ignora vendedor e datas forjados');
$conexao = (object) array('fontes' => array('orcamentos' => array(), 'pedidos' => array(), 'notas' => array()), 'consultas' => array());
$resultado = dashboardIndicadorExecutar($conexao, $selecionado, 1, $entrada, $erro);
conferir($resultado !== false, $erro);
conferir(count($resultado['indicador']['abas']) === 1 && $resultado['indicador']['abas'][0]['id'] === 'vendedores', 'Resposta mantém aba de representantes oculta');
conferir(count($conexao->consultas) === (!empty($selecionado['desempenhoComercial']['comparativoMesAnterior']) ? 7 : 4), 'Sem comparativo: opções e três fontes; com comparativo: três agregações existentes');
foreach (array_slice($conexao->consultas, 1) as $consulta) {
    conferir(!in_array('999', $consulta['valores'], true), 'Nenhum parâmetro forjado executado');
    conferir(in_array('7', $consulta['valores'], true) && in_array(1, $consulta['valores'], true), 'Todas as fontes recebem vendedor e empresa autorizados');
}
// Visão gerencial por usuário: sem restrição, sem tornar o perfil administrador.
$acessos['usuarios'][7]['restricoes'] = array();
conferir(dashboardPersonalizadoRestricaoUsuarioIndicador($auth, $acessos, $indicador['id']) === false, 'Gestor individual tem visão completa');
conferir(!dashboardPersonalizadoPodeGerenciarAcessos($auth, $acessos), 'Gestor comercial não ganha administração de acessos');
foreach ($indicador['abas'] as $aba) {
    $resultado = dashboardIndicadorExecutar($conexao, array_merge($indicador, $aba), 1, array('f_dataInicio'=>'2026-01-01','f_dataFim'=>'2026-04-15'), $erro);
    conferir($resultado !== false && count($resultado['indicador']['abas']) === 2, 'Gestor acessa ambas as abas: ' . $erro);
}
$conexao->fontes = $fontes;
$exportavel = dashboardIndicadorExecutar($conexao, array_merge($indicador, $indicador['abas'][0]), 1, array('f_dataInicio'=>'2026-01-01','f_dataFim'=>'2026-04-15'), $erro);
// Carrega apenas os formatadores de exportação; não executa o endpoint nem lê credenciais.
$codigoExportacao = file_get_contents(dirname(__DIR__) . '/export.php');
foreach (array('dashboardExportarTexto', 'dashboardExportarFormatar', 'dashboardExportarTabela') as $funcao) {
    $inicioFuncao = strpos($codigoExportacao, 'function ' . $funcao . '(');
    $fimFuncao = strpos($codigoExportacao, "\nfunction ", $inicioFuncao + 1);
    eval('namespace hardness; ' . substr($codigoExportacao, $inicioFuncao, $fimFuncao - $inicioFuncao));
}
list($cabecalho, $linhasExportadas) = dashboardExportarTabela($exportavel);
conferir(count($cabecalho) === 6 && count($linhasExportadas) === 13, 'Exportar todos os clientes e meses filtrados, mais total');
$totalExportado = end($linhasExportadas);
conferir($totalExportado[3] === 'R$ 3.000,00' && $totalExportado[4] === 'R$ 800,00' && $totalExportado[5] === 'R$ 1.100,00', 'CSV/PDF preservam totais independentes');
$abaTotal = array_merge($indicador, $indicador['abas'][0]);
$abaTotal['desempenhoComercial']['valorNotas'] = 'total';
$resultadoTotal = dashboardIndicadorExecutar($conexao, $abaTotal, 1, array('f_dataInicio'=>'2026-01-01','f_dataFim'=>'2026-04-15'), $erro);
conferir($resultadoTotal['desempenhoComercial']['valorNotas'] === 'total', 'Propagar regra de valor total');
conferir($resultadoTotal['indicador']['listagem']['colunas'][5]['titulo'] === 'Notas emitidas (total)', 'Coluna exportada usa rótulo total');
$resumoTotal = dashboardComercialResumoExportacao($resultadoTotal['desempenhoComercial']);
conferir($resumoTotal[1][0] === 'Faturamento total no mês', 'Resumo exportado usa rótulo total');
$comComparativo=$abaTotal;$comComparativo['desempenhoComercial']['comparativoMesAnterior']=true;
$conexao->anteriores=array('orcamentos'=>array(array('valor'=>800,'quantidade'=>4)), 'pedidos'=>array(array('valor'=>1000,'quantidade'=>2)), 'notas'=>array(array('valor'=>1000,'quantidade'=>2)));
$conexao->consultas=array();
$comparavel=dashboardIndicadorExecutar($conexao,$comComparativo,1,array('f_dataInicio'=>'2026-01-01','f_dataFim'=>'2026-04-15','f_vendedor'=>'7','f_cliente'=>'Cliente teste'),$erro);
conferir($comparavel!==false,$erro);
$comp=$comparavel['desempenhoComercial']['comparativo'];
conferir(count($conexao->consultas)===7,'Ticket orçamento reutiliza as três agregações existentes, sem consulta adicional');
conferir($comp['metricas']['faturamento']['percentual']==-20 && $comp['metricas']['ticketMedio']['tendencia']==='baixa','Faturamento e ticket com queda');
conferir($comp['metricas']['ticketMedio']['percentual']==-20,'Ticket de pedido anterior calculado pela quantidade de pedidos anterior');
conferir($comp['metricas']['ticketMedioOrcamento']['atual']===500.0 && $comp['metricas']['ticketMedioOrcamento']['anterior']===200.0 && $comp['metricas']['ticketMedioOrcamento']['percentual']==150,'Comparar tickets de orçamento com valores e quantidades dos mesmos intervalos mensais');
conferir($comp['metricas']['valorOrcado']['atual']===1000.0 && $comp['metricas']['valorOrcado']['percentual']==25,'Comparativo orçado é mensal, não o acumulado do filtro');
conferir($comparavel['desempenhoComercial']['resumo']['valorOrcado']===3000.0,'Comparativo não altera o cartão orçado do período');
conferir($comparavel['listagem']===$resultadoTotal['listagem'] && $comparavel['card']===$resultadoTotal['card'],'Período anterior não contamina tabela ou totais exportados');
foreach (array_slice($conexao->consultas,-3) as $consulta) {
    foreach (array('2026-03-01','2026-03-15','7','Cliente teste',1) as $valor) conferir(in_array($valor,$consulta['valores'],true),'Comparativo preserva vendedor, cliente e empresa, mudando apenas as datas');
    conferir(strpos($consulta['sql'],'GROUP BY')===false,'Comparativo retorna apenas um resumo por etapa');
}
$exportComp=dashboardComercialResumoExportacao($comparavel['desempenhoComercial']);
conferir(count($exportComp)===27 && strpos($exportComp[11][1],'01/04/2026 a 15/04/2026 / 01/03/2026 a 15/03/2026')!==false,'CSV/PDF incluem as duas janelas e as cinco variações');
conferir($exportComp[21] === array('Ticket médio por orçamento (mês de referência, intervalo comparado)', 500.0, 'moeda') && $exportComp[23] === array('Ticket médio por orçamento: variação mensal', 150.0, 'percentual'), 'Exportar comparativo de orçamento com o mesmo valor e percentual');
$restritoComp=$selecionado;$restritoComp['desempenhoComercial']['comparativoMesAnterior']=true;
$conexao->consultas=array();
conferir(dashboardIndicadorExecutar($conexao,$restritoComp,1,$entrada,$erro)!==false,$erro);
foreach(array_slice($conexao->consultas,-3) as $consulta) conferir(in_array('7',$consulta['valores'],true) && !in_array('999',$consulta['valores'],true),'Comparativo respeita liberação individual no servidor');
$repComp=array_merge($indicador,$indicador['abas'][1]);$repComp['desempenhoComercial']['comparativoMesAnterior']=true;
$conexao->consultas=array();
conferir(dashboardIndicadorExecutar($conexao,$repComp,1,array('f_dataInicio'=>'2026-04-01','f_dataFim'=>'2026-04-15','f_representante'=>'31','f_cliente'=>'Cliente teste'),$erro)!==false,$erro);
foreach(array_slice($conexao->consultas,-3) as $consulta) conferir(in_array('31',$consulta['valores'],true) && in_array('Cliente teste',$consulta['valores'],true),'Comparativo também mantém representante e cliente');
$semComparativo=$comComparativo;unset($semComparativo['desempenhoComercial']['comparativoMesAnterior']);
$conexao->consultas=array();$sem=dashboardIndicadorExecutar($conexao,$semComparativo,1,array('f_dataInicio'=>'2026-04-01','f_dataFim'=>'2026-04-15'),$erro);
conferir(count($conexao->consultas)===4 && !isset($sem['desempenhoComercial']['comparativo']),'Configuração antiga preservada sem novas consultas');
$conexao->anteriores['notas']=array();
conferir(dashboardIndicadorExecutar($conexao,$comComparativo,1,array('f_dataInicio'=>'2026-04-01','f_dataFim'=>'2026-04-15'),$erro)===false,'Falha no comparativo não produz base zero artificial');
unset($conexao->anteriores);
$conexao->fontes = array('orcamentos' => array(), 'pedidos' => array(), 'notas' => array());
for ($id = 1; $id <= 2001; $id++) $conexao->fontes['orcamentos'][] = linha('cad:' . $id, 7, '2026-04', 100, 1);
conferir(dashboardIndicadorExecutar($conexao, $selecionado, 1, $entrada, $erro) === false, 'Limitar matriz antes de alocar todos os meses');
$conexao->fontes['orcamentos'] = array_fill(0, 12001, linha('cad:1', 7, '2026-04', 100, 1));
conferir(dashboardIndicadorExecutar($conexao, $selecionado, 1, $entrada, $erro) === false, 'Recusar totais truncados por limite');
// Carteira opt-in: mesmas fontes/filtros para as duas abas, inclusive acesso individual.
$carteiraConfig = json_decode(file_get_contents(__DIR__ . '/fixtures/desempenho-comercial-carteira.json'), true);
$carteiraIndicador = $carteiraConfig['indicadores'][0];
conferir(dashboardPersonalizadoAbasComerciaisLocais($carteiraIndicador), 'Habilitar abas locais somente na configuração compartilhada');
conferir(!dashboardPersonalizadoAbasComerciaisLocais($indicador), 'Indicador antigo mantém regras das abas independentes');
$inseguro = $carteiraIndicador; $inseguro['abas'][1]['sqlBase'] = 'SELECT 1';
conferir(!dashboardPersonalizadoAbasComerciaisLocais($inseguro), 'Segunda aba com fonte própria não recebe liberação compartilhada');
conferir(dashboardIndicadorValidarSql(implode("\n", $carteiraIndicador['desempenhoComercial']['carteiraRepresentantes']), $erro), $erro);
$conexao->fontes = $fontes;
$conexao->carteira = array(
    array('clienteId'=>'cad:1','cliente'=>'Cliente com movimento','vendedorId'=>'7','vendedor'=>'Rafaela','representanteId'=>'31','representante'=>'INECEL'),
    array('clienteId'=>'cad:3','cliente'=>'Cliente sem movimento','vendedorId'=>'7','vendedor'=>'Rafaela','representanteId'=>'31','representante'=>'INECEL'),
    array('clienteId'=>'cad:2','cliente'=>'Cliente com movimento','vendedorId'=>'7','vendedor'=>'Rafaela','representanteId'=>'32','representante'=>'Outro representante')
);
$conexao->consultas = array();
$filtrosCarteira = array('f_dataInicio'=>'2026-01-01','f_dataFim'=>'2026-04-15','f_vendedor'=>'7');
$carteiraIndicador['_abaComercial'] = 'vendedores';
$carteiraVendedor = dashboardIndicadorExecutar($conexao, $carteiraIndicador, 1, $filtrosCarteira, $erro);
conferir($carteiraVendedor !== false, $erro);
$carteiraIndicador['_abaComercial'] = 'representantes';
$carteiraRepresentante = dashboardIndicadorExecutar($conexao, $carteiraIndicador, 1, $filtrosCarteira, $erro);
conferir($carteiraRepresentante !== false, $erro);
conferir($carteiraVendedor['desempenhoComercial'] === $carteiraRepresentante['desempenhoComercial'], 'Troca de aba não altera dados, cartões ou comparativos');
$carteira = $carteiraRepresentante['desempenhoComercial']['carteiraRepresentantes'];
conferir(count($carteira['linhas']) === 2 && count($carteira['linhas'][0]['clientes']) === 2, 'Uma linha por representante, expandindo sua carteira');
conferir($carteira['linhas'][0]['clientes'][1]['totais'] === array('orcamentos'=>0.0,'pedidos'=>0.0,'notas'=>0.0), 'Cliente sem movimento permanece na carteira');
conferir($carteira['totais']['notas'] === 900.0, 'Não atribuir os documentos do outro vendedor à carteira de Rafaela');
conferir(count($carteiraRepresentante['indicador']['listagem']['colunas']) === 6 && count($carteiraRepresentante['listagem']) === 13, 'Exportar representante, vendedor principal e orçamentos/pedidos dos clientes sem duplicar grupos');
conferir(!in_array('notas', array_column($carteiraRepresentante['indicador']['listagem']['colunas'], 'campo')), 'Carteira não exporta coluna NF');
conferir(end($carteiraRepresentante['listagem'])['notas'] === 900.0, 'Total da exportação corresponde à carteira');
$fontesSoma = $fontes;
$fontesSoma['orcamentos'][] = linha('cad:2', 7, '2026-04', 250, 1);
$fontesSoma['pedidos'][] = linha('cad:2', 7, '2026-04', 60, 1);
$cadastrosSoma = $conexao->carteira;
$cadastrosSoma[2]['representanteId'] = '31'; $cadastrosSoma[2]['representante'] = 'INECEL';
$soma = dashboardComercialAgruparCarteira($cadastrosSoma, dashboardComercialConsolidar($fontesSoma, $periodo, '2026-04-15'));
conferir(count($soma['linhas']) === 1 && count($soma['linhas'][0]['clientes']) === 3, 'Representante reúne todos os seus clientes, inclusive o sem movimento');
conferir($soma['linhas'][0]['totais']['orcamentos'] === 3250.0 && $soma['linhas'][0]['totais']['pedidos'] === 860.0, 'Totais do representante somam os movimentos de todos os clientes');
$porCliente = array_column($soma['linhas'][0]['clientes'], null, 'clienteId');
conferir($porCliente['cad:1']['totais']['orcamentos'] === 3000.0 && $porCliente['cad:1']['totais']['pedidos'] === 800.0
    && $porCliente['cad:2']['totais']['orcamentos'] === 250.0 && $porCliente['cad:2']['totais']['pedidos'] === 60.0, 'Detalhes preservam os totais individuais por cliente');
$entradaCarteira = array('f_vendedor'=>'999', 'f_representante'=>'999');
$restritoCarteira = dashboardPersonalizadoAplicarRestricaoUsuario($carteiraIndicador, $restricao, $entradaCarteira, $erro);
$conexao->consultas = array();
$resultadoCarteiraRestrita = dashboardIndicadorExecutar($conexao, $restritoCarteira, 1, $entradaCarteira, $erro);
conferir($resultadoCarteiraRestrita !== false && count($resultadoCarteiraRestrita['indicador']['abas']) === 2, 'Vendedor individual pode acessar a aba de sua carteira');
conferir($entradaCarteira['f_vendedor'] === '7', 'Carteira ignora tentativa de selecionar outro vendedor');
$consultaCarteira = end($conexao->consultas);
conferir(in_array('7', $consultaCarteira['valores'], true) && in_array(1, $consultaCarteira['valores'], true) && !in_array('999', $consultaCarteira['valores'], true), 'Carteira recebe vendedor do ACL e empresa da sessão');
$conexao->carteira = array_fill(0, 20001, $conexao->carteira[0]);
conferir(dashboardIndicadorExecutar($conexao, $restritoCarteira, 1, $entradaCarteira, $erro) === false, 'Carteira não trunca clientes silenciosamente');

$regConfig = json_decode(file_get_contents(__DIR__ . '/fixtures/desempenho-comercial-regioes.json'), true);
$regIndicador = $regConfig['indicadores'][0];
conferir(dashboardPersonalizadoAbasComerciaisLocais($regIndicador), 'Terceira aba compartilha as fontes autorizadas');
$semFonteReg = $regIndicador; unset($semFonteReg['desempenhoComercial']['carteiraRegioes']);
conferir(!dashboardPersonalizadoAbasComerciaisLocais($semFonteReg), 'Não liberar aba regional sem fonte própria de carteira');
$conexao->carteira = array();
foreach ($carteira['linhas'] as $grupo) foreach ($grupo['clientes'] as $cliente) $conexao->carteira[] = $cliente;
$conexao->regioes = array(
    array('clienteId'=>'cad:1','cliente'=>'Cliente A','vendedorId'=>'7','vendedor'=>'Rafaela','uf'=>'PR','cidadeId'=>'10','cidade'=>'Curitiba'),
    array('clienteId'=>'cad:2','cliente'=>'Cliente B','vendedorId'=>'7','vendedor'=>'Rafaela','uf'=>'PR','cidadeId'=>'11','cidade'=>'Londrina'),
    array('clienteId'=>'cad:3','cliente'=>'Cliente C sem movimento','vendedorId'=>'7','vendedor'=>'Rafaela','uf'=>'SP','cidadeId'=>'12','cidade'=>'São Paulo'),
    array('clienteId'=>'cad:4','cliente'=>'Cliente sem endereço/representante','vendedorId'=>'7','vendedor'=>'Rafaela','uf'=>'','cidadeId'=>'0','cidade'=>'')
);
$regIndicador['_abaComercial'] = 'vendedores';
$regVendedor = dashboardIndicadorExecutar($conexao, $regIndicador, 1, $filtrosCarteira, $erro);
conferir($regVendedor !== false, $erro);
$regIndicador['_abaComercial'] = 'regioes';
$regResultado = dashboardIndicadorExecutar($conexao, $regIndicador, 1, $filtrosCarteira, $erro);
conferir($regResultado !== false && $regResultado['desempenhoComercial'] === $regVendedor['desempenhoComercial'], 'Regiões não altera cartões nem dados das outras abas');
$geo = $regResultado['desempenhoComercial']['carteiraRegioes'];
conferir($geo['quantidade'] === 4 && count($geo['ufs']) === 3, 'Incluir clientes sem movimento e sem representante/endereço');
$porUf = array_column($geo['ufs'], null, 'uf');
conferir($porUf['PR']['quantidade'] === 2 && count($porUf['PR']['cidades']) === 2 && $porUf['SP']['totais']['orcamentos'] === 0.0, 'Brasil agrega UFs; UFs agregam cidades; carteira sem movimento tem presença no mapa');
conferir($geo['totais']['orcamentos'] === 3000.0 && $porUf['PR']['totais']['pedidos'] === 800.0, 'Totais regionais preservam as fontes do vendedor');
conferir(count($regResultado['listagem']) === 5 && count($regResultado['indicador']['listagem']['colunas']) === 6, 'Exportar UF/cidade/cliente e totais sem coluna NF');
$entradaReg = array('f_vendedor'=>'999');
$regRestrito = dashboardPersonalizadoAplicarRestricaoUsuario($regIndicador, $restricao, $entradaReg, $erro);
$conexao->consultas = array();
conferir(dashboardIndicadorExecutar($conexao, $regRestrito, 1, $entradaReg, $erro) !== false, $erro);
$queryReg = end($conexao->consultas);
conferir(in_array('7', $queryReg['valores'], true) && !in_array('999', $queryReg['valores'], true), 'Regiões mantém o vendedor autorizado em chamadas diretas/exportações');
$conexao->regioes = array_fill(0, 20001, $conexao->regioes[0]);
conferir(dashboardIndicadorExecutar($conexao, $regRestrito, 1, $entradaReg, $erro) === false, 'Recusar carteira regional truncada');

// Carteira consolidada de gestor: supera 2.000 clientes sem gerar uma matriz anual para cada cadastro vazio.
$conexao->carteira = array(); $conexao->regioes = array();
for ($id = 1; $id <= 20000; $id++) {
    $vendedorId = $id <= 2 || $id % 2 ? '7' : '8';
    $cliente = array('clienteId'=>'cad:'.$id, 'cliente'=>'Cliente '.$id, 'vendedorId'=>$vendedorId, 'vendedor'=>'Vendedor '.$vendedorId);
    $conexao->carteira[] = array_merge($cliente, array('representanteId'=>'31', 'representante'=>'INECEL'));
    $conexao->regioes[] = array_merge($cliente, array('uf'=>'PR','cidadeId'=>'10','cidade'=>'Curitiba'));
}
$gestorTodos = $regIndicador; $gestorTodos['_abaComercial'] = 'vendedores'; $gestorTodos['_comercialSomenteVisual'] = true;
$grande = dashboardIndicadorExecutar($conexao, $gestorTodos, 1, array('f_vendedor'=>'','f_dataInicio'=>'2026-01-01','f_dataFim'=>'2026-04-15'), $erro);
conferir($grande !== false, $erro);
$carteiraGrande = $grande['desempenhoComercial']['carteiraRepresentantes'];
conferir(count($carteiraGrande['linhas'][0]['clientes']) === 20000 && $grande['desempenhoComercial']['carteiraRegioes']['quantidade'] === 20000, 'Gestor pode abrir carteira consolidada com 20.000 clientes sem truncar');
conferir($carteiraGrande['linhas'][0]['clientes'][100]['meses'] === array(), 'Cliente sem movimento não repete matriz anual de zeros');
conferir($grande['listagem'] === array(), 'Resposta visual não duplica a carteira em linhas de exportação');
conferir($grande['desempenhoComercial']['resumo'] === $regVendedor['desempenhoComercial']['resumo'], 'Ampliação da carteira não altera os cartões');
$visualCarteira = $carteiraIndicador; $visualCarteira['_comercialSomenteVisual'] = true;
$conexao->carteira = $cadastrosSoma;
$visual = dashboardIndicadorExecutar($conexao, $visualCarteira, 1, $filtrosCarteira, $erro);
conferir($visual !== false && $visual['listagem'] === array(), 'Carteira de representantes usa resposta visual compacta');
// Divisões cadastradas: agregar regiões dentro da UF sem repetir clientes.
$regionalConfig = json_decode(file_get_contents(__DIR__ . '/fixtures/regioes-cadastradas.json'), true);
$regionalIndicador = $regionalConfig['indicadores'][0];
unset($regionalIndicador['desempenhoComercial']['metasVendedores']);
$conexao->regioes = array(
 array('clienteId'=>'cad:1','cliente'=>'Cliente 1','vendedorId'=>'7','vendedor'=>'Rafaela','uf'=>'PR','cidadeId'=>'10','cidade'=>'Curitiba','cidadeIbge'=>'4106902','regiaoId'=>'101','regiao'=>'PR1 GRANDE CURITIBA'),
 array('clienteId'=>'cad:2','cliente'=>'Cliente 2','vendedorId'=>'7','vendedor'=>'Rafaela','uf'=>'PR','cidadeId'=>'11','cidade'=>'Londrina','cidadeIbge'=>'4113700','regiaoId'=>'102','regiao'=>'PR4 NORTE'),
 array('clienteId'=>'cad:3','cliente'=>'Cliente 3','vendedorId'=>'7','vendedor'=>'Rafaela','uf'=>'PR','cidadeId'=>'12','cidade'=>'Sem região','cidadeIbge'=>'','regiaoId'=>'0','regiao'=>'Sem região cadastrada')
);
$conexao->territorios = array(array('uf'=>'PR','cidadeIbge'=>'4106902','regiaoId'=>'101','regiao'=>'PR1 GRANDE CURITIBA'),array('uf'=>'PR','cidadeIbge'=>'4113700','regiaoId'=>'102','regiao'=>'PR4 NORTE'));
$conexao->consultas = array(); $erro='';
$regionalResultado = dashboardIndicadorExecutar($conexao, $regionalIndicador, 1, $filtrosCarteira, $erro);
conferir($regionalResultado !== false, $erro);
$ufRegional = $regionalResultado['desempenhoComercial']['carteiraRegioes']['ufs'][0];
conferir(count($ufRegional['regioes']) === 3 && $ufRegional['regioes'][0]['regiaoId']==='101', 'Regiões e sem região dentro da UF, ordenadas por valores');
conferir($ufRegional['regioes'][0]['totais']['orcamentos']===3000.0 && $ufRegional['regioes'][0]['quantidade']===1,'Totais da região sem multiplicar etapas');
conferir($regionalResultado['desempenhoComercial']['carteiraRegioes']['territorios']===$conexao->territorios,'Retornar cadastro municipal para desenhar divisões');
$clienteRegional=$ufRegional['cidades'][0]['clientes'][0];
conferir($clienteRegional['meses']['2026-03']['orcamentos']===2000.0 && $clienteRegional['meses']['2026-04']['orcamentos']===1000.0 && $clienteRegional['meses']['2026-04']['pedidos']===800.0,'Mensal regional preserva orçamento/pedido por cliente e vendedor');
conferir(!isset($clienteRegional['meses']['2026-04']['notas']),'Detalhamento regional não inclui NF');
conferir($ufRegional['cidades'][2]['clientes'][0]['meses']===array(),'Cliente sem movimento não gera matriz de zeros no servidor');
foreach($conexao->consultas as $q) if(strpos($q['sql'],'AS comercial_territorios')!==false) conferir(in_array('7',$q['valores'],true) && in_array(1,$q['valores'],true),'Malha cadastral respeita vendedor e empresa');
// Metas são opt-in; usam cadastro mensal e permissões, nunca o faturamento filtrado.
$metasSql = file_get_contents(__DIR__ . '/fixtures/metas-vendedores.sql');
conferir(dashboardIndicadorValidarSql($metasSql, $erro), $erro);
conferir(!dashboardIndicadorValidarSql('SELECT SMT121_Id FROM SMT121 WHERE SMT121_C004_Id = :empresa', $erro), 'Não liberar todas as tabelas personalizadas');
conferir(!dashboardIndicadorValidarSql('SELECT SMT120_Id FROM SMT120', $erro), 'Meta exige escopo de empresa');
$erro = '';
$mensalMeta = array('vendedorId'=>'7','inicio'=>'2026-04-01','fim'=>'2026-04-30','meta'=>'1000','atingido'=>'4900.50','percentual'=>'490.05');
$meta = dashboardComercialMetaRegistrada(array($mensalMeta), '2026-04', $erro);
conferir($meta['meta'] === 1000.0 && $meta['valorMetaAtingido'] === 4900.5 && $meta['atingimentoMeta'] === 490.05, 'Usar atingimento gravado, mesmo acima de 100%');
$outraMeta = array_merge($mensalMeta, array('vendedorId'=>'8','meta'=>'3000','atingido'=>'1200','percentual'=>'40'));
$totalMeta = dashboardComercialMetaRegistrada(array($mensalMeta,$outraMeta), '2026-04', $erro);
conferir($totalMeta['meta'] === 4000.0 && $totalMeta['valorMetaAtingido'] === 6100.5 && abs($totalMeta['atingimentoMeta'] - 152.5125) < 0.0001, 'Consolidado ponderado pelas metas, sem média simples de percentuais');
conferir(dashboardComercialMetaRegistrada(array($mensalMeta), '2026-05', $erro)['meta'] === null, 'Não reutilizar meta de outro mês');
conferir(dashboardComercialMetaRegistrada(array(array_merge($mensalMeta,array('meta'=>'0'))), '2026-04', $erro)['meta'] === null, 'Meta zero não gera divisão');
conferir(dashboardComercialMetaRegistrada(array(array_merge($mensalMeta,array('atingido'=>null))), '2026-04', $erro)['atingimentoMeta'] === null, 'Atingimento ausente não vira zero calculado');
foreach (array(array('atingido'=>null,'percentual'=>null), array('percentual'=>null), array('atingido'=>null)) as $camposPendentes) {
    $metaPendente = dashboardComercialMetaRegistrada(array(array_merge($mensalMeta, $camposPendentes)), '2026-04', $erro);
    conferir($metaPendente['meta'] === 1000.0 && $metaPendente['atingimentoMeta'] === null, 'Meta mensal independe do preenchimento do atingimento e do percentual');
    $comercialPendente = $r;
    $comercialPendente['resumo'] = array_merge($r['resumo'], $metaPendente);
    $exportacaoPendente = dashboardComercialResumoExportacao($comercialPendente);
    conferir($exportacaoPendente[2] === array('Meta do mês', 1000.0, 'moeda'), 'CSV/PDF exibem a meta cadastrada quando o atingimento está pendente');
    conferir(dashboardExportarFormatar($exportacaoPendente[2][1], $exportacaoPendente[2][2]) === 'R$ 1.000,00', 'Exportar meta pendente em moeda, sem inventar percentual');
}
$totalPendente = dashboardComercialMetaRegistrada(array($mensalMeta, array_merge($outraMeta, array('percentual'=>null))), '2026-04', $erro);
$comercialPendente['resumo'] = array_merge($r['resumo'], $totalPendente);
conferir(dashboardComercialResumoExportacao($comercialPendente)[2] === array('Meta do mês', 4000.0, 'moeda'), 'Todos mantém a soma das metas mesmo com atingimento parcialmente pendente');
conferir(dashboardComercialMetaRegistrada(array($mensalMeta,$mensalMeta), '2026-04', $erro) === false, 'Não somar registros mensais duplicados');
$erro = ''; $comMeta = $comComparativo; $comMeta['desempenhoComercial']['metasVendedores'] = $metasSql;
$conexao->metas = array($mensalMeta); $conexao->consultas = array();
$comMeta = dashboardPersonalizadoAplicarRestricaoUsuario($comMeta, $restricao, $entrada, $erro);
$entrada['f_dataInicio']='2026-01-01'; $entrada['f_dataFim']='2026-04-15'; $entrada['f_cliente']='Cliente teste';
$resultadoMeta = dashboardIndicadorExecutar($conexao, $comMeta, 1, $entrada, $erro);
conferir($resultadoMeta !== false, $erro);
conferir($resultadoMeta['desempenhoComercial']['resumo']['atingimentoMeta'] === 490.05, 'Cartão usa percentual ERP, não notas do indicador');
$consultaMeta = array_values(array_filter($conexao->consultas, function($q) { return strpos($q['sql'], 'AS comercial_metas') !== false; }))[0];
conferir(in_array('7', $consultaMeta['valores'], true) && in_array(1, $consultaMeta['valores'], true) && !in_array('999', $consultaMeta['valores'], true), 'Meta recebe vendedor e empresa autorizados');
conferir(in_array('2026-04-01', $consultaMeta['valores'], true) && in_array('2026-04-30', $consultaMeta['valores'], true), 'Meta usa mês inteiro da data final');
conferir(!in_array('Cliente teste', $consultaMeta['valores'], true), 'Filtro cliente não divide a meta mensal do vendedor');
conferir(dashboardComercialResumoExportacao($resultadoMeta['desempenhoComercial'])[2][1] === 490.05, 'Exportar o mesmo percentual ERP');
$conexao->metas = array($mensalMeta,$mensalMeta); $erro = '';
$duplicadoMeta = dashboardIndicadorExecutar($conexao, $comMeta, 1, $entrada, $erro);
conferir($duplicadoMeta !== false && isset($duplicadoMeta['desempenhoComercial']['resumo']['metaAviso']), 'Cadastro ambíguo não bloqueia outros cartões e tabela');
// Equipe é apenas um filtro gerencial; nunca altera o acesso individual.
$teamConfig=json_decode(file_get_contents(__DIR__.'/fixtures/desempenho-comercial-equipes.json'),true);
$teamIndicator=$teamConfig['indicadores'][0];
$teamConnection=(object)array('fontes'=>$fontes,'carteira'=>array(),'regioes'=>array(),'territorios'=>array(),'metas'=>array(),'consultas'=>array());
$teamInput=array('f_equipe'=>'gian','f_vendedor'=>'999');
$teamRestricted=dashboardPersonalizadoAplicarRestricaoUsuario($teamIndicator,$restricao,$teamInput,$erro);
conferir(!in_array('equipe',array_column($teamRestricted['filtros'],'id'),true) && !isset($teamInput['f_equipe']),'Acesso individual não tem filtro de equipe, mesmo forjado');
$teamInput['f_dataInicio']='2026-04-01';$teamInput['f_dataFim']='2026-04-15';
$teamResult=dashboardIndicadorExecutar($teamConnection,$teamRestricted,1,$teamInput,$erro);
conferir($teamResult!==false,$erro);
conferir(!in_array('equipe',array_column($teamResult['indicador']['filtros'],'id'),true),'Equipe não aparece na resposta individual');
foreach($teamConnection->consultas as $q) conferir(!in_array('gian',$q['valores'],true) && !in_array('999',$q['valores'],true),'Nenhuma consulta recebe equipe/vendedor forjados');
$teamManager=dashboardPersonalizadoAplicarRestricaoUsuario($teamIndicator,false,$teamInput,$erro);
$teamResult=dashboardIndicadorExecutar($teamConnection,$teamManager,1,array('f_equipe'=>'william','f_dataInicio'=>'2026-04-01','f_dataFim'=>'2026-04-15'),$erro);
conferir($teamResult!==false,$erro);
conferir(in_array('equipe',array_column($teamResult['indicador']['filtros'],'id'),true),'Root/gestor sem restrição recebe equipe');
foreach(array_slice($teamConnection->consultas,-8) as $q) if(strpos($q['sql'],'AS comercial_')!==false) conferir(in_array('william',$q['valores'],true),'Equipe em documentos, comparativo, metas, carteiras e territórios');
$ampliado=$teamIndicator;
$ampliado['desempenhoComercial']['carregamentoProgressivo']=true;
$ampliadoInput=array('f_vendedor'=>'999','f_equipe'=>'gian','f_dataInicio'=>'2024-10-14','f_dataFim'=>'2026-10-14');
$ampliado=dashboardPersonalizadoAplicarRestricaoUsuario($ampliado,$restricao,$ampliadoInput,$erro);
conferir($ampliadoInput['f_vendedor']==='7' && !isset($ampliadoInput['f_equipe']) && $ampliadoInput['f_dataInicio']==='2024-10-14','Período livre mantém isolamento do vendedor');
foreach($ampliado['filtros'] as $f) if(in_array($f['id'],array('dataInicio','dataFim'),true)) conferir(empty($f['bloqueado']),'Datas liberadas no acesso individual');
$multiano=dashboardComercialConsolidar(array('orcamentos'=>array(linha('cad:1',7,'2024-10',100,1),linha('cad:1',7,'2026-10',200,1)),'pedidos'=>array(),'notas'=>array()),array('inicio'=>'2024-10-14','fim'=>'2026-10-14'),'2026-10-14');
conferir(count($multiano['meses'])===25 && $multiano['meses'][0]==='2024-10' && end($multiano['meses'])==='2026-10','Meses de anos diferentes são separados');
conferir($multiano['totais']['orcamentos']===300.0 && $multiano['totaisMeses']['2024-10']['orcamentos']===100.0,'Totais multiano completos');
// Histórico de seis meses usa agregados, IDs exatos e as fontes com escopo autorizado.
$historyIndicator = $teamManager;
$historyIndicator['_abaComercial'] = 'vendedores';
$historyConnection = (object) array('consultas'=>array(), 'fontes'=>array('orcamentos'=>array(), 'pedidos'=>array()));
$historyInput = array('clienteId'=>'cad:1','responsavelId'=>'7','f_vendedor'=>'7','f_dataInicio'=>'2026-04-01','f_dataFim'=>'2026-04-15');
$history = dashboardComercialHistoricoCliente($historyConnection,$historyIndicator,1,$historyInput,$erro);
conferir($history !== false,$erro);
conferir(count($history['meses']) === 6,'Histórico tem seis meses inclusive na virada do ano');
conferir($history['periodo']['fim'] === date('Y-m-d'),'Histórico termina hoje');
$historyQueries = array_values(array_filter($historyConnection->consultas,function($q){return strpos($q['sql'],'AS comercial_historico')!==false;}));
conferir(count($historyQueries)===2,'Histórico consulta somente orçamentos e pedidos');
foreach ($historyQueries as $q) {
    conferir(in_array('cad:1',$q['valores'],true) && in_array('7',$q['valores'],true),'Cliente e responsável são parâmetros');
    conferir(in_array($history['periodo']['inicio'],$q['valores'],true),'Fonte usa início do histórico, fora do período da tabela');
    conferir(strpos($q['sql'],'clienteId = ? AND responsavelId = ?')!==false,'Identidade exata sem confundir nomes');
}
$historyInput['clienteId'] = "cad:1' OR 1=1";
conferir(dashboardComercialHistoricoCliente($historyConnection,$historyIndicator,1,$historyInput,$erro)===false,'Rejeitar identidade inválida');
$historyIndicator['_abaComercial']='representantes';
conferir(dashboardComercialHistoricoCliente($historyConnection,$historyIndicator,1,array(),$erro)===false,'Histórico somente na aba Vendedores');

if (in_array('--fixture-regioes-cadastradas', $argv, true)) echo json_encode($regionalResultado, JSON_UNESCAPED_UNICODE);
elseif (in_array('--fixture-gestor', $argv, true)) echo json_encode($grande, JSON_UNESCAPED_UNICODE);
elseif (in_array('--fixture-regioes', $argv, true)) echo json_encode($regVendedor, JSON_UNESCAPED_UNICODE);
elseif (in_array('--fixture-carteira', $argv, true)) echo json_encode($carteiraVendedor, JSON_UNESCAPED_UNICODE);
elseif (in_array('--fixture-comparativo', $argv, true)) echo json_encode($comparavel, JSON_UNESCAPED_UNICODE);
elseif (in_array('--fixture', $argv, true)) echo json_encode($r, JSON_UNESCAPED_UNICODE);
else echo "OK: configuração, consolidação, limites, filtros preparados e permissões comerciais.\n";
