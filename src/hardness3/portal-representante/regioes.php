<?php
namespace hardness;
if (realpath(isset($_SERVER['SCRIPT_FILENAME']) ? $_SERVER['SCRIPT_FILENAME'] : '') === __FILE__) { http_response_code(404); exit; }

function portalRepresentanteRegioesSql($conta, $filtros)
{
    $carteira = portalRepresentanteBaseConfigurada('clientes', $conta, $filtros);
    $semSelecao = array_merge($filtros, array('cliente' => '', 'clienteId' => 0, 'grupoClienteId' => 0, 'numero' => '', 'etapa' => '', 'status' => ''));
    $fontes = array();
    foreach (array('notas','pedidos') as $tipo) {
        $f = portalRepresentanteBaseConfigurada($tipo, $conta, $semSelecao, null, array('clienteId','valor','excluido','data'));
        $fontes[$tipo] = 'SELECT clienteId,valor FROM (' . $f['sql'] . ') movimentos WHERE data>=:inicio AND data<:fim AND excluido=0';
    }
    $params = $carteira['params']; $params['cliente'] = $filtros['cliente'];
    $clienteExato = '';
    if ($filtros['clienteId']) { $clienteExato = ' AND c.id=:clienteId'; $params['clienteId'] = $filtros['clienteId']; }
    if (!empty($filtros['grupoClienteId'])) { $clienteExato .= ' AND c.grupoClienteId=:grupoClienteId'; $params['grupoClienteId'] = $filtros['grupoClienteId']; }
    $sql = 'SELECT /* carteira_regional_portal */ c.' . implode(',c.', portalRepresentanteCamposFonte('clientes')) . ",
        COALESCE(v.faturamento,0) AS faturamento,COALESCE(v.notas,0) AS notas,
        COALESCE(p.pedidos,0) AS pedidos,COALESCE(p.valorPedidos,0) AS valorPedidos
        FROM (" . $carteira['sql'] . ") c
        LEFT JOIN (SELECT clienteId,SUM(valor) AS faturamento,COUNT(*) AS notas FROM (" . $fontes['notas'] . ") notas_mapa GROUP BY clienteId) v ON v.clienteId=c.id
        LEFT JOIN (SELECT clienteId,SUM(valor) AS valorPedidos,COUNT(*) AS pedidos FROM (" . $fontes['pedidos'] . ") pedidos_mapa GROUP BY clienteId) p ON p.clienteId=c.id
        WHERE (:cliente='' OR LOCATE(:cliente,c.cliente)>0 OR LOCATE(:cliente,c.grupoCliente)>0)$clienteExato ORDER BY c.id LIMIT 20001";
    return array('sql' => $sql, 'params' => $params);
}

function portalRepresentanteRegioesResposta($linhas, $demonstracao = false)
{
    if (count($linhas) > 20000) throw new RepresentantePortalErro('Mais de 20.000 clientes nesta seleção. Filtre pelo nome do cliente para consultar a carteira.', 422);
    foreach ($linhas as &$linha) {
        foreach (array('id','notas','pedidos') as $campo) $linha[$campo] = (int) $linha[$campo];
        foreach (array('faturamento','valorPedidos') as $campo) $linha[$campo] = round((float) $linha[$campo], 2);
        foreach (array('cliente','ativo','uf','ufMunicipio','ibge','cidade','endereco','numero','bairro','cep') as $campo) $linha[$campo] = trim((string) $linha[$campo]);
    }
    unset($linha);
    return array('clientes' => $linhas, 'demonstracao' => $demonstracao, 'atualizado' => date('c'));
}

function portalRepresentanteRegioesDemonstracao($filtros, $hoje = null)
{
    $cidades = array(
        array('PR','4106902','Curitiba'),array('SC','4209102','Joinville'),array('SP','3550308','São Paulo'),
        array('MG','3106200','Belo Horizonte'),array('GO','5208707','Goiânia'),array('BA','2927408','Salvador'),array('AM','1302603','Manaus'),
        array('PE','2611606','Recife'),array('CE','2304400','Fortaleza'),array('PR','4106902','Curitiba'),array('','','')
    );
    $movimentos = portalRepresentanteDemonstracaoDados($hoje); $clientes = array();
    foreach ($cidades as $i => $cidade) {
        $id = 700 + $i; $nome = $i < 7 ? $movimentos['orcamentos'][$i]['cliente'] : array(7 => 'Comercial Recife — Exemplo', 8 => 'Indústria Fortaleza — Exemplo', 9 => 'Peças Curitiba — Exemplo', 10 => 'Cliente sem município — Exemplo')[$i];
        $linha = array('id' => $id, 'cliente' => $nome, 'ativo' => 'S', 'uf' => $cidade[0], 'ufMunicipio' => $cidade[0], 'ibge' => $cidade[1], 'cidade' => $cidade[2],
            'endereco' => $cidade[0] ? 'Rua de demonstração' : '', 'numero' => $cidade[0] ? (string) (100 + 35*$i) : '', 'bairro' => $cidade[0] ? 'Bairro de exemplo' : '', 'cep' => '', 'faturamento' => 0, 'notas' => 0, 'pedidos' => 0, 'valorPedidos' => 0);
        foreach (array('notas','pedidos') as $tipo) foreach (portalRepresentanteDemonstracaoFiltrar($movimentos[$tipo], $tipo, array_merge($filtros, array('etapa' => '', 'status' => '', 'numero' => '', 'cliente' => ''))) as $documento) {
            if ($documento['clienteId'] !== $id || $documento['excluido'] || $documento['categoria'] === 'negado') continue;
            $linha[$tipo]++; $linha[$tipo === 'notas' ? 'faturamento' : 'valorPedidos'] += $documento['valor'];
        }
        if ((!$filtros['clienteId'] || $filtros['clienteId'] === $id) && ($filtros['cliente'] === '' || stripos($nome, $filtros['cliente']) !== false)) $clientes[] = $linha;
    }
    return portalRepresentanteRegioesResposta($clientes, true);
}
