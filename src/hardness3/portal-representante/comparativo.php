<?php
namespace hardness;
if (realpath(isset($_SERVER['SCRIPT_FILENAME']) ? $_SERVER['SCRIPT_FILENAME'] : '') === __FILE__) { http_response_code(404); exit; }
require_once __DIR__ . '/dados.php';

function portalRepresentanteComparativoPeriodoAnterior($filtros)
{
    $anterior = $filtros;
    foreach (array('inicio','fim') as $campo) {
        list($ano,$mes,$dia) = array_map('intval', explode('-', $filtros[$campo]));
        $ano--;
        if ($ano < 1000) throw new RepresentantePortalErro('Selecione um período a partir do ano 1001 para comparar.');
        if (!checkdate($mes,$dia,$ano)) $dia = 28;
        $anterior[$campo] = sprintf('%04d-%02d-%02d',$ano,$mes,$dia);
    }
    $anterior['limiteFim'] = (new \DateTimeImmutable($anterior['fim']))->modify('+1 day')->format('Y-m-d');
    return $anterior;
}

function portalRepresentanteComparativoSql($tipo, $conta, $filtros)
{
    if (!in_array($tipo,array('orcamentos','pedidos','notas'),true)) throw new RepresentantePortalErro('Selecione orçamentos, pedidos ou faturamento.');
    $filtros['etapa']=''; $filtros['status']=''; $filtros['numero']='';
    $campos=array('id','data','cliente','clienteId','grupoClienteId','grupoCliente','valor','excluido');
    if ($tipo==='orcamentos') $campos=array_merge($campos,array('clienteNormal','clienteProposta'));
    $consultas = array();
    foreach (array('atual'=>$filtros,'anterior'=>portalRepresentanteComparativoPeriodoAnterior($filtros)) as $periodo=>$selecao) {
        // Reutiliza as fontes e os escopos autenticados do painel, sem consultar
        // cadastros/empresas/representantes recebidos do navegador.
        $fonte = portalRepresentanteFonte($tipo,$conta,$selecao,0,null,$campos);
        $grupo = "COALESCE(grupoClienteId,0)>0 AND NULLIF(TRIM(grupoCliente),'') IS NOT NULL";
        $clienteId = "CASE WHEN $grupo THEN 0 ELSE clienteId END";
        $grupoId = "CASE WHEN $grupo THEN grupoClienteId ELSE 0 END";
        $nome = "CASE WHEN $grupo THEN grupoCliente ELSE cliente END";
        $base = 'SELECT '.$fonte['select'].' FROM '.$fonte['from'].' WHERE '.$fonte['where'];
        $sql = "SELECT $clienteId AS clienteId,$grupoId AS grupoClienteId,$nome AS cliente,COUNT(*) AS quantidade,SUM(valor) AS valor
            FROM ($base) comparativo_portal WHERE excluido=0
            GROUP BY $clienteId,$grupoId,$nome ORDER BY valor DESC,clienteId,grupoClienteId LIMIT 1001";
        $consultas[$periodo] = array('sql'=>$sql,'params'=>$fonte['params'],'inicio'=>$selecao['inicio'],'fim'=>$selecao['fim']);
    }
    return $consultas;
}

function portalRepresentanteComparativoResposta($tipo, $consultas, $registros)
{
    $clientes = array();
    foreach (array('anterior','atual') as $periodo) {
        if (count($registros[$periodo])>1000) throw new RepresentantePortalErro('Mais de 1.000 clientes na comparação. Filtre o cliente ou reduza o período.',422);
        foreach ($registros[$periodo] as $linha) {
            $id = (int)$linha['clienteId']; $grupo = (int)$linha['grupoClienteId'];
            $chave = $grupo>0 ? 'grupo:'.$grupo : ($id>0 ? 'cliente:'.$id : 'nome:'.$linha['cliente']);
            if (!isset($clientes[$chave])) $clientes[$chave] = array('clienteId'=>$id,'grupoClienteId'=>$grupo,'cliente'=>$linha['cliente'],
                'valorAtual'=>0.0,'valorAnterior'=>0.0,'quantidadeAtual'=>0,'quantidadeAnterior'=>0);
            $clientes[$chave]['cliente'] = $linha['cliente'];
            $clientes[$chave][$periodo==='atual'?'valorAtual':'valorAnterior'] += (float)$linha['valor'];
            $clientes[$chave][$periodo==='atual'?'quantidadeAtual':'quantidadeAnterior'] += (int)$linha['quantidade'];
        }
    }
    if (count($clientes)>1000) throw new RepresentantePortalErro('Mais de 1.000 clientes na comparação. Filtre o cliente ou reduza o período.',422);
    $atual=0.0; $anterior=0.0;
    foreach ($clientes as &$cliente) {
        $base=$cliente['valorAnterior']; $valor=$cliente['valorAtual'];
        $cliente['diferenca']=$valor-$base;
        $cliente['variacao']=$base>0 ? ($valor-$base)/$base*100 : null;
        $atual+=$valor; $anterior+=$base;
    }
    unset($cliente);
    uasort($clientes,function($a,$b) { return $b['valorAtual']<=>$a['valorAtual'] ?: strcmp($a['cliente'],$b['cliente']); });
    return array('tipo'=>$tipo,'periodos'=>array('atual'=>array_intersect_key($consultas['atual'],array_flip(array('inicio','fim'))),
        'anterior'=>array_intersect_key($consultas['anterior'],array_flip(array('inicio','fim')))),
        'linhas'=>array_values($clientes),'totais'=>array('valorAtual'=>$atual,'valorAnterior'=>$anterior,'diferenca'=>$atual-$anterior,
        'variacao'=>$anterior>0 ? ($atual-$anterior)/$anterior*100 : null),'atualizado'=>date('c'));
}

function portalRepresentanteComparativoCarregar($conexao, $tipo, $conta, $filtros, $demonstracao=false)
{
    $consultas=portalRepresentanteComparativoSql($tipo,$conta,$filtros); $registros=array();
    foreach ($consultas as $periodo=>$consulta) {
        if (!$demonstracao) { $registros[$periodo]=portalRepresentanteConsultar($conexao,$consulta['sql'],$consulta['params']); continue; }
        $dados=portalRepresentanteDemonstracaoDados(); $registros[$periodo]=array();
        foreach ($dados[$tipo] as $linha) {
            if (!empty($linha['excluido']) || $linha['data']<$consulta['inicio'] || $linha['data']>$consulta['fim']) continue;
            if ($filtros['cliente']!=='' && stripos($linha['cliente'],$filtros['cliente'])===false) continue;
            if ($filtros['clienteId'] && (int)$linha['clienteId']!==$filtros['clienteId']) continue;
            $registros[$periodo][]=array('clienteId'=>$linha['clienteId'],'grupoClienteId'=>0,'cliente'=>$linha['cliente'],'valor'=>$linha['valor'],'quantidade'=>1);
        }
    }
    $resultado=portalRepresentanteComparativoResposta($tipo,$consultas,$registros);
    if ($demonstracao) $resultado['demonstracao']=true;
    return $resultado;
}
