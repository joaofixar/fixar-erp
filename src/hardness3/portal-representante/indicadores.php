<?php
namespace hardness;
if (realpath(isset($_SERVER['SCRIPT_FILENAME']) ? $_SERVER['SCRIPT_FILENAME'] : '') === __FILE__) { http_response_code(404); exit; }

function portalRepresentanteIndicadoresSql($tipo, $conta, $filtros, $hoje = null)
{
    // Indicadores, séries e documentos usam a mesma seleção do painel.
    $fonte = portalRepresentanteFonte($tipo, $conta, $filtros, 0, $hoje);
    $base = 'SELECT ' . $fonte['select'] . ' FROM ' . $fonte['from'] . ' WHERE ' . $fonte['where'];
    $validos = 'excluido=0';
    $campos = "COALESCE(SUM(CASE WHEN $validos THEN 1 ELSE 0 END),0) AS quantidade,
        COALESCE(SUM(CASE WHEN $validos THEN valor ELSE 0 END),0) AS valor,
        COUNT(DISTINCT CASE WHEN $validos AND clienteId>0 THEN clienteId ELSE NULL END) AS clientes";
    if ($tipo === 'orcamentos') $campos .= ",COALESCE(SUM(CASE WHEN $validos AND convertido=1 THEN 1 ELSE 0 END),0) AS convertidos,
        COALESCE(SUM(CASE WHEN categoria='pendente' THEN 1 ELSE 0 END),0) AS pendentes,
        COALESCE(SUM(CASE WHEN categoria='pendente' THEN valor ELSE 0 END),0) AS valorPendente,
        COALESCE(SUM(CASE WHEN cancelamentoReal=1 THEN 1 ELSE 0 END),0) AS cancelados,
        COALESCE(SUM(CASE WHEN cancelamentoReal=1 THEN valor ELSE 0 END),0) AS valorCancelado";
    elseif ($tipo === 'pedidos') $campos .= ",COALESCE(SUM(CASE WHEN $validos AND comNota=1 THEN 1 ELSE 0 END),0) AS comNota,
        COALESCE(SUM(CASE WHEN $validos AND categoria<>'negado' AND comNota=0 THEN 1 ELSE 0 END),0) AS semNota,
        COALESCE(SUM(prazoVencido),0) AS atrasados,
        COALESCE(SUM(pendenteDireto),0) AS pendentesDiretos,
        COALESCE(SUM(CASE WHEN pendenteDireto=1 THEN valor ELSE 0 END),0) AS valorPendentesDiretos,
        COALESCE(SUM(CASE WHEN categoria='expedicao' THEN 1 ELSE 0 END),0) AS naExpedicao,
        COALESCE(SUM(CASE WHEN $validos AND origem>0 THEN 1 ELSE 0 END),0) AS comOrcamento";
    else {
        $campos .= ',COALESCE(SUM(recebido),0) AS recebido,COALESCE(SUM(saldo),0) AS saldo';
        if ($tipo === 'notas') $campos .= ",COALESCE(SUM(CASE WHEN saldo>0 THEN 1 ELSE 0 END),0) AS notasAbertas,
            COALESCE(SUM(CASE WHEN cobrancas=0 THEN 1 ELSE 0 END),0) AS semCobranca";
        else {
            $fonte['params']['hoje'] = $hoje === null ? date('Y-m-d') : $hoje;
            $fonte['params']['proximos7'] = (new \DateTimeImmutable($fonte['params']['hoje']))->modify('+7 days')->format('Y-m-d');
            $campos .= ",COALESCE(SUM(CASE WHEN status='vencido' THEN saldo ELSE 0 END),0) AS vencido,
                COALESCE(SUM(CASE WHEN vencimento>=:hoje AND vencimento<=:proximos7 THEN saldo ELSE 0 END),0) AS proximos7,
                COALESCE(SUM(CASE WHEN status='pago' THEN 1 ELSE 0 END),0) AS pagos";
        }
    }
    $dia = $fonte['campoEvolucao'];
    $evolucao = "SELECT DATE($dia) AS data,SUM(valor) AS valor,COUNT(*) AS quantidade";
    if ($tipo === 'boletos') $evolucao .= ',SUM(recebido) AS recebido';
    // O grupo consolida o ranking; os documentos e o mapa preservam o cadastro
    // original. Configurações sem grupo mantêm o agrupamento por cliente.
    $temGrupo = "COALESCE(grupoClienteId,0)>0 AND NULLIF(TRIM(grupoCliente),'') IS NOT NULL";
    $clienteRanking = "CASE WHEN $temGrupo THEN 0 ELSE clienteId END";
    $grupoRanking = "CASE WHEN $temGrupo THEN grupoClienteId ELSE 0 END";
    $nomeRanking = "CASE WHEN $temGrupo THEN grupoCliente ELSE cliente END";
    $queries = array(
        'resumo' => "SELECT $campos FROM ($base) indicadores_portal",
        'evolucao' => "$evolucao FROM ($base) evolucao_portal WHERE $validos GROUP BY DATE($dia) ORDER BY data LIMIT 366",
        'situacoes' => "SELECT categoria,COUNT(*) AS quantidade,SUM(valor) AS valor FROM ($base) situacoes_portal GROUP BY categoria ORDER BY quantidade DESC,categoria LIMIT 30",
        'clientes' => "SELECT $clienteRanking AS clienteId,$grupoRanking AS grupoClienteId,$nomeRanking AS cliente,
            COUNT(*) AS quantidade,SUM(valor) AS valor FROM ($base) clientes_portal WHERE $validos
            GROUP BY $clienteRanking,$grupoRanking,$nomeRanking ORDER BY valor DESC,clienteId,grupoClienteId LIMIT 5"
    );
    $config = isset($conta['painel']) ? $conta['painel'] : portalRepresentanteLerConfiguracao($conta['empresa']);
    if ($tipo === 'orcamentos' && isset($config['fontes'][$tipo]['campos']['cancelamentoReal']))
        $queries['situacoes'] = "SELECT categoria,COUNT(*) AS quantidade,SUM(valor) AS valor FROM ($base) situacoes_portal
            WHERE categoria<>'cancelado' OR cancelamentoReal=1 GROUP BY categoria ORDER BY quantidade DESC,categoria LIMIT 30";
    if (isset($config['fontes'][$tipo]['campos']['segmento'])) $queries['segmentos'] = "SELECT COALESCE(NULLIF(TRIM(segmento),''),'Não informado') AS segmento,
        COUNT(*) AS quantidade,SUM(valor) AS valor FROM ($base) segmentos_portal WHERE $validos
        GROUP BY COALESCE(NULLIF(TRIM(segmento),''),'Não informado') ORDER BY valor DESC,segmento LIMIT 30";
    if ($tipo === 'orcamentos' && isset($config['fontes'][$tipo]['campos']['cancelamentoReal'])) $queries['cancelamentos'] = "SELECT motivoCancelamento AS motivo,
        COUNT(*) AS quantidade,SUM(valor) AS valor FROM ($base) cancelamentos_portal WHERE cancelamentoReal=1
        GROUP BY motivoCancelamento ORDER BY quantidade DESC,motivoCancelamento LIMIT 30";
    if ($tipo === 'boletos') $queries['atrasos'] = "SELECT CASE WHEN DATEDIFF(:hoje,vencimento)<=7 THEN '1–7 dias'
        WHEN DATEDIFF(:hoje,vencimento)<=30 THEN '8–30 dias' WHEN DATEDIFF(:hoje,vencimento)<=60 THEN '31–60 dias' ELSE 'Mais de 60 dias' END AS faixa,
        MIN(DATEDIFF(:hoje,vencimento)) AS ordem,COUNT(*) AS quantidade,SUM(saldo) AS valor
        FROM ($base) atrasos_portal WHERE status='vencido' GROUP BY faixa ORDER BY ordem LIMIT 4";
    return array('queries' => $queries, 'params' => $fonte['params']);
}
