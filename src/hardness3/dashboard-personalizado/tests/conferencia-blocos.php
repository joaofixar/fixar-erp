<?php
// Fixtures locais; nunca abre conexão com banco ou lê credenciais.
namespace hardness;
require_once dirname(__DIR__) . '/custom-indicator-engine.php';
function dashboardPersonalizadoSqlTexto($sql) { return is_array($sql) ? implode("\n", $sql) : $sql; }
if (!function_exists('mb_substr')) { function mb_substr($s,$a,$b,$encoding) { return substr($s,$a,$b); } }
if (!defined('MYSQLI_ASSOC')) define('MYSQLI_ASSOC', 1);
class ConferenciaStatement {
    public $sql, $values = array();
    function bind_param($tipos, &...$values) { $this->values = $values; return true; }
}
function mysqli_prepare($c, $sql) { $s = new ConferenciaStatement(); $s->sql = $sql; $GLOBALS['confStatements'][] = $s; return $s; }
function mysqli_stmt_execute($s) { return true; }
function mysqli_stmt_get_result($s) {
    if (strpos($s->sql, 'FROM C004 WHERE') !== false) {
        return array_values(array_filter(array(
            array('valor'=>1,'titulo'=>'Matriz'), array('valor'=>2,'titulo'=>'Filial A'),
            array('valor'=>4,'titulo'=>'Filial B'), array('valor'=>5,'titulo'=>'Filial C')
        ), function($r) use ($s) { return $r['valor'] !== $s->values[0]; }));
    }
    return $GLOBALS['confRows'];
}
function mysqli_fetch_all($r, $mode) { return $r; }
function mysqli_stmt_close($s) {}
function check($ok, $message) { if (!$ok) throw new \RuntimeException($message); }
$path = dirname(__DIR__, 2) . '/dashboard-personalizado/indicadores-pcp-realizado.json';
$d = json_decode(file_get_contents($path), true);
$i = array_values(array_filter($d['indicadores'], function ($x) { return $x['id'] === 'inecel_pcp_planejamento'; }))[0];
// As opções dinâmicas não são necessárias à fixture da rota de conferência.
foreach ($i['filtros'] as &$f) {
    unset($f['opcoesSql'], $f['opcoesCadastro']);
    if ($f['id']==='abrangenciaEmpresa') array_splice($f['opcoes'],1,0,array(array('valor'=>'2','titulo'=>'Filial A')));
}
unset($f);
$entrada = array('f_dataInicio'=>'2026-10-01', 'f_dataFim'=>'2026-10-31', 'f_maquina'=>'0', 'f_op'=>'12', 'f_cliente'=>'Cliente');
$GLOBALS['confRows'] = array(
    array('id'=>11,'op'=>12,'data'=>'2026-10-10','maquina'=>'Blank','valor'=>100.25,'um'=>1,'semPeso'=>0,'blank'=>100.25),
    array('id'=>13,'op'=>12,'data'=>'2026-10-10','maquina'=>'Blank','valor'=>200.50,'um'=>1,'semPeso'=>0,'blank'=>200.50),
    array('id'=>14,'op'=>15,'data'=>'2026-10-10','maquina'=>'Blank','valor'=>0,'um'=>1,'semPeso'=>1,'blank'=>0),
);
$GLOBALS['confStatements'] = array();
$erro = '';
$r = dashboardIndicadorExecutar(null, $i, 7, $entrada + array('detalheBloco'=>'realizado_dia','detalheBlocoGrupo'=>'2026-10-10','empresa'=>99), $erro);
check($r !== false, $erro);
check(count($GLOBALS['confStatements'])===1, 'Rota de detalhe consulta apenas os registros selecionados.');
$s = $GLOBALS['confStatements'][0];
check(strpos($s->sql, 'WHERE `data` = ? LIMIT 5001') !== false, 'Grupo vinculado com parâmetro.');
check(in_array(7,$s->values,true) && !in_array(99,$s->values,true), 'Empresa da sessão é preservada.');
check(in_array('2026-10-01',$s->values,true) && in_array('Cliente',$s->values,true) && in_array(12.0,$s->values,true), 'Filtros seguem para a consulta.');
$totais = array_column($r['conferencia']['totais'],'valor','titulo');
check($totais['Realizado (kg)']===300.75 && $totais['OPs consideradas']===2 && $totais['Apontamentos']===3.0 && $totais['Sem peso positivo']===1.0, 'Somas, distintos e apontamentos sem peso.');
check(strpos(json_encode($r),'sqlBase')===false, 'A resposta de conferência não divulga SQL.');
$r = dashboardIndicadorExecutar(null, $i, 7, $entrada + array('f_planoCorte'=>'S', 'detalheBloco'=>'semanal','detalheBlocoGrupo'=>'2026-10 / Semana 2','detalheBlocoDia'=>'quinta'), $erro);
check($r !== false, $erro);
$s = end($GLOBALS['confStatements']);
check(strpos($s->sql, 'WHERE `semanaMes` = ? AND WEEKDAY(`dataInicio`) = ? LIMIT 5001') !== false, 'Célula restringe semana e dia.');
check(in_array(3, $s->values, true) && in_array('S', $s->values, true), 'Dia autorizado e plano de corte parametrizados.');
$before=count($GLOBALS['confStatements']);
foreach (array(array('detalheBloco'=>'semanal','detalheBlocoGrupo'=>'semana','detalheBlocoDia'=>'valor'), array('detalheBloco'=>'semanal','detalheBlocoGrupo'=>'semana','detalheBlocoDia'=>array('quinta')), array('detalheBloco'=>'semanal','detalheBlocoDia'=>'quinta'), array('detalheBloco'=>'realizado_dia','detalheBlocoGrupo'=>'2026-10-10','detalheBlocoDia'=>'quinta'), array('detalheBloco'=>'inexistente'),array('detalheBloco'=>array('fila')),array('detalheBloco'=>'fila','detalheBlocoGrupo'=>array('x')),array('detalheBloco'=>'fila','detalheBlocoGrupo'=>'sem-grupo')) as $bad) {
    check(dashboardIndicadorExecutar(null,$i,7,$entrada+$bad,$erro)===false,'Rejeita entrada inválida.');
}
check(count($GLOBALS['confStatements'])===$before,'Entradas inválidas não consultam os dados.');
$unsafe=$i;$unsafe['conferencia']['fila']['sqlBase']='DELETE FROM T255';
check(dashboardIndicadorExecutar(null,$unsafe,7,$entrada+array('detalheBloco'=>'fila'),$erro)===false,'Validador bloqueia escrita.');
$unsafe=$i;$unsafe['conferencia']['fila']['sqlBase']='SELECT T255_Id AS op FROM T255 WHERE T255_C004_Id = :empresa AND T255_Id = :naoConfigurado';
check(dashboardIndicadorExecutar(null,$unsafe,7,$entrada+array('detalheBloco'=>'fila'),$erro)===false,'Validador bloqueia parâmetro desconhecido.');
$empty=$i;unset($empty['conferencia']);
check(dashboardIndicadorExecutar(null,$empty,7,$entrada+array('detalheBloco'=>'fila'),$erro)===false,'Não habilita em indicadores sem configuração.');
$GLOBALS['confRows']=array_fill(0,5001,array('op'=>1,'valor'=>1));
check(dashboardIndicadorExecutar(null,$i,7,$entrada+array('detalheBloco'=>'realizado_dia'),$erro)===false && strpos($erro,'5.000')!==false,'Nunca devolve somatórios parciais.');
$GLOBALS['confRows']=array();
check(dashboardIndicadorExecutar(null,$i,7,$entrada+array('detalheBloco'=>'realizado_dia'),$erro)['conferencia']['linhas']===array(),'Sem movimento retorna vazio.');
foreach ($i['conferencia'] as $key=>$definition) {
    check(dashboardIndicadorValidarSql(dashboardPersonalizadoSqlTexto($definition['sqlBase']),$erro),$key.': '.$erro);
    $cols=array_column($definition['colunas'],'campo');
    foreach ($definition['totais'] as $t) check(in_array($t['campo'],$cols,true),'Total deve usar campo retornado.');
}
$GLOBALS['confRows'] = array();
check(dashboardIndicadorExecutar(null,$i,7,$entrada+array('f_abrangenciaEmpresa'=>'todas','detalheBloco'=>'realizado_dia'),$erro)!==false,$erro);
$s = end($GLOBALS['confStatements']);
check(in_array('todas',$s->values,true) && strpos($s->sql,'op.T255_C004_Id IN (1, 2, 4, 5)')!==false,'Conferência usa a abrangência selecionada e o grupo configurado.');
$before=count($GLOBALS['confStatements']);
check(dashboardIndicadorExecutar(null,$i,7,$entrada+array('f_abrangenciaEmpresa'=>'invalida'),$erro)===false,'Rejeita abrangência inválida.');
check(count($GLOBALS['confStatements'])===$before,'Abrangência inválida não consulta o banco.');
$original = array_values(array_filter($d['indicadores'],function($x){return $x['id']==='inecel_pcp_planejamento';}))[0];
$GLOBALS['confRows'] = array(array('valor'=>'9','titulo'=>'Material do grupo'));
check(dashboardIndicadorCarregarOpcoesFiltros(null,$original['filtros'],7,$erro,'todas')!==false,$erro);
$s = end($GLOBALS['confStatements']);
check(in_array('todas',$s->values,true) && strpos($s->sql,'D009_C004_Id IN (1, 2, 4, 5)')!==false,'Cadastros dinâmicos usam o mesmo grupo.');
check(dashboardIndicadorCarregarOpcoesFiltros(null,$original['filtros'],7,$erro)!==false,$erro);
check(in_array('atual',end($GLOBALS['confStatements'])->values,true),'Cadastros continuam na empresa atual por padrão.');
$empresas = dashboardIndicadorCarregarOpcoesFiltros(null,array($original['filtros'][0]),1,$erro);
check(array_column($empresas[0]['opcoes'],'valor')===array('atual','2','4','5','todas'),'Empresa atual primeiro, demais separadas e todas por último, sem repetir a atual.');
$GLOBALS['confRows'] = array();
check(dashboardIndicadorExecutar(null,$original,1,$entrada+array('f_abrangenciaEmpresa'=>'2','detalheBloco'=>'realizado_dia'),$erro)!==false,$erro);
check(in_array('2',end($GLOBALS['confStatements'])->values,true),'Seleção individual segue para a conferência.');
check(dashboardIndicadorExecutar(null,$original,1,$entrada+array('f_abrangenciaEmpresa'=>'3','detalheBloco'=>'realizado_dia'),$erro)===false,'Recusa empresa fora do grupo.');
$baseGrafico = array('id'=>'grafico_teste','titulo'=>'Teste','filtros'=>array(),'sqlBase'=>'SELECT D009_Quantidade_Estoque AS valor FROM D009 WHERE D009_C004_Id = :empresa','composicao'=>array('habilitada'=>false),'grafico'=>array('habilitado'=>false),'listagem'=>array('habilitada'=>false),'secoes'=>array());
$secaoGrafico = array('sqlBase'=>'SELECT D015_Nome_Grupo AS acabamento, D009_Quantidade_Estoque AS estoqueKg FROM D009 INNER JOIN D001 ON D001_Id = D009_D001_Id INNER JOIN D015 ON D015_Id = D001_D015_Id WHERE D009_C004_Id = :empresa','colunas'=>array(array('campo'=>'acabamento'),array('campo'=>'estoqueKg')));
$baseGrafico['secoes'] = array($secaoGrafico, $secaoGrafico);
$GLOBALS['confRows'] = array(array('acabamento'=>'PZ','estoqueKg'=>1250));
$before = count($GLOBALS['confStatements']);
$semGrafico = dashboardIndicadorExecutar(null,$baseGrafico,1,array(),$erro);
check($semGrafico!==false,$erro);
$consultasSemGrafico = array_map(function($s){return $s->sql;},array_slice($GLOBALS['confStatements'],$before));
$baseGrafico['secoes'][0]['id'] = 'estoque_acabamento';
$baseGrafico['secoes'][1]['grafico'] = array('habilitado'=>true,'fonteSecao'=>'estoque_acabamento','campoRotulo'=>'acabamento','campoValor'=>'estoqueKg');
$before = count($GLOBALS['confStatements']);
$comGrafico = dashboardIndicadorExecutar(null,$baseGrafico,1,array(),$erro);
check($comGrafico!==false,$erro);
$consultasComGrafico = array_map(function($s){return $s->sql;},array_slice($GLOBALS['confStatements'],$before));
check($consultasComGrafico===$consultasSemGrafico,'Adicionar gráfico não modifica nem acrescenta consultas.');
check($comGrafico['secoes'][0]['id']==='estoque_acabamento' && $comGrafico['secoes'][1]['grafico']['fonteSecao']==='estoque_acabamento','Referência da origem chega ao navegador.');
check($comGrafico['secoes'][0]['linhas']===$semGrafico['secoes'][0]['linhas'],'Gráfico preserva as linhas da tabela.');
echo "OK: rota, empresa da sessão, filtros, agrupamento, totais, segurança, configuração opcional e excesso sem totais parciais.\n";
