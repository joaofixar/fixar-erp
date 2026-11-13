<?php
namespace hardness;
require_once __DIR__ . '/analise-comercial.php';

// Estas abas compartilham filtros e fontes; só a apresentação da carteira muda.
function dashboardPersonalizadoAbasComerciaisLocais($indicador)
{
    if (empty($indicador['desempenhoComercial']['habilitado'])
        || empty($indicador['desempenhoComercial']['carteiraRepresentantes'])
        || empty($indicador['abas']) || !in_array(count($indicador['abas']), array(2, 3, 4), true)) return false;
    $ids = array();
    foreach ($indicador['abas'] as $aba) {
        // Não liberar uma segunda consulta independente para o vendedor restrito.
        if (array_diff(array_keys($aba), array('id', 'titulo'))) return false;
        $ids[] = isset($aba['id']) ? $aba['id'] : '';
    }
    if (count(array_unique($ids)) !== count($ids)) return false;
    sort($ids);
    if (in_array('pedidos', $ids, true)) {
        if (empty($indicador['desempenhoComercial']['pedidosListagem']['habilitado'])) return false;
        $ids = array_values(array_diff($ids, array('pedidos')));
    }
    if ($ids !== array('representantes', 'vendedores')
        && ($ids !== array('regioes', 'representantes', 'vendedores') || empty($indicador['desempenhoComercial']['carteiraRegioes']))) return false;
    foreach ((array) (isset($indicador['filtros']) ? $indicador['filtros'] : array()) as $filtro) {
        if (isset($filtro['id'], $filtro['tipo']) && $filtro['id'] === 'vendedor' && $filtro['tipo'] === 'selecao') return true;
    }
    return false;
}


// Ordena antes da paginação, inclusive cada nível expandido da carteira.
// Não soma etapas do mesmo documento para produzir uma pontuação artificial.
function dashboardComercialOrdenarValores(&$linhas, $nome, $id)
{
    usort($linhas, function ($a, $b) use ($nome, $id) {
        foreach (array('orcamentos', 'pedidos') as $tipo) {
            $va = (float) $a['totais'][$tipo]; $vb = (float) $b['totais'][$tipo];
            if ($va !== $vb) return $va > $vb ? -1 : 1;
        }
        $ordem = strcasecmp((string) $a[$nome], (string) $b[$nome]);
        return $ordem !== 0 ? $ordem : strcmp((string) $a[$id], (string) $b[$id]);
    });
}

/* As três etapas são agregadas separadamente: vínculos 1:N não multiplicam valores. */
function dashboardComercialPeriodo($valores, &$erro, $ampliado = false)
{
    $inicio = isset($valores['dataInicio']) ? (string) $valores['dataInicio'] : '';
    $fim = isset($valores['dataFim']) ? (string) $valores['dataFim'] : '';
    $dataInicio = \DateTime::createFromFormat('!Y-m-d', $inicio);
    $dataFim = \DateTime::createFromFormat('!Y-m-d', $fim);
    if (!$dataInicio || !$dataFim || $dataInicio->format('Y-m-d') !== $inicio
        || $dataFim->format('Y-m-d') !== $fim || $inicio > $fim
        || (!$ampliado && $dataInicio->format('Y') !== $dataFim->format('Y'))
        || ($ampliado && $inicio < (clone $dataFim)->modify('-2 years')->format('Y-m-d'))) {
        $erro = $ampliado ? 'Selecione um período válido de até 2 anos.' : 'Selecione um período válido dentro do mesmo ano para o desempenho comercial.';
        return false;
    }
    return array('inicio' => $inicio, 'fim' => $fim);
}

function dashboardComercialConsolidar($fontes, $periodo, $hoje)
{
    $tipos = array('orcamentos', 'pedidos', 'notas');
    $zerados = array_fill_keys($tipos, 0.0);
    $meses = array();
    for ($ano = (int) substr($periodo['inicio'], 0, 4); $ano <= (int) substr($periodo['fim'], 0, 4); $ano++)
        for ($mes = 1; $mes <= 12; $mes++) $meses[] = $ano . '-' . str_pad($mes, 2, '0', STR_PAD_LEFT);
    if (substr($periodo['inicio'],0,4)!==substr($periodo['fim'],0,4)) $meses=array_values(array_filter($meses,function($mes)use($periodo){return $mes>=substr($periodo['inicio'],0,7) && $mes<=substr($periodo['fim'],0,7);}));
    $totaisMeses = array_fill_keys($meses, $zerados);
    $totais = $zerados;
    $quantidades = array_fill_keys($tipos, 0);
    $mesReferencia = substr($periodo['fim'], 0, 7);
    $quantidadesMes = $quantidades;
    $clientesMes = array_fill_keys($tipos, array());
    $orcamentosConvertidos = 0;
    $grupos = array();
    foreach ($tipos as $tipo) {
        foreach ($fontes[$tipo] as $linha) {
            $mes = (string) $linha['mes'];
            $clienteId = (string) $linha['clienteId'];
            $responsavelId = (string) $linha['responsavelId'];
            // Orçamentos avulsos sem cadastro não são fundidos apenas pelo nome.
            $chave = json_encode(array($responsavelId, $clienteId));
            if (!isset($grupos[$chave])) {
                $grupos[$chave] = array('cliente' => $linha['cliente'], 'responsavel' => $linha['responsavel'],
                    'clienteId' => $clienteId, 'responsavelId' => $responsavelId,
                    'meses' => array(), 'totais' => $zerados);
            }
            $valor = (float) $linha['valor'];
            if (!isset($grupos[$chave]['meses'][$mes])) $grupos[$chave]['meses'][$mes] = $zerados;
            $quantidade = (int) $linha['quantidade'];
            $grupos[$chave]['meses'][$mes][$tipo] += $valor;
            $grupos[$chave]['totais'][$tipo] += $valor;
            $totaisMeses[$mes][$tipo] += $valor;
            $totais[$tipo] += $valor;
            $quantidades[$tipo] += $quantidade;
            if ($tipo === 'orcamentos') $orcamentosConvertidos += (int) $linha['convertidos'];
            if ($mes === $mesReferencia) {
                $quantidadesMes[$tipo] += $quantidade;
                // Contadores de clientes consideram somente cadastros identificados.
                if (strpos($clienteId, 'cad:') === 0) $clientesMes[$tipo][$clienteId] = true;
            }
        }
    }
    $linhas = array_values($grupos);
    dashboardComercialOrdenarValores($linhas, 'cliente', 'clienteId');
    $mensal = $totaisMeses[$mesReferencia];
    $dataFim = new \DateTime($periodo['fim']);
    $projecao = null;
    // Não extrapola um mês cuja primeira parte foi excluída pelo filtro nem datas futuras.
    if ($periodo['inicio'] <= $mesReferencia . '-01' && $periodo['fim'] <= $hoje) {
        $projecao = $mensal['notas'] / (int) $dataFim->format('j') * (int) $dataFim->format('t');
    }
    return array('meses' => $meses, 'mesReferencia' => $mesReferencia, 'periodo' => $periodo,
        'linhas' => $linhas, 'totais' => $totais, 'totaisMeses' => $totaisMeses,
        'quantidades' => $quantidades, 'resumo' => array(
            'faturamento' => $mensal['notas'], 'meta' => null, 'atingimentoMeta' => null,
            'projecao' => $projecao, 'ticketMedio' => $quantidadesMes['pedidos'] > 0 ? $mensal['pedidos'] / $quantidadesMes['pedidos'] : null,
            'ticketMedioOrcamento' => $quantidadesMes['orcamentos'] > 0 ? $mensal['orcamentos'] / $quantidadesMes['orcamentos'] : null,
            'valorOrcado' => $totais['orcamentos'], 'valorPedidos' => $mensal['pedidos'],
            'conversao' => $quantidades['orcamentos'] > 0 ? 100 * $orcamentosConvertidos / $quantidades['orcamentos'] : null,
            'orcamentosConvertidos' => $orcamentosConvertidos, 'quantidadeOrcamentos' => $quantidades['orcamentos'],
            'quantidades' => $quantidadesMes,
            'clientesAtendidos' => count($clientesMes['notas']), 'clientesOrcados' => count($clientesMes['orcamentos'])
        ));
}

function dashboardComercialPeriodosComparacao($periodo)
{
    $mes = substr($periodo['fim'], 0, 7) . '-01';
    $atual = array('inicio' => max($periodo['inicio'], $mes), 'fim' => $periodo['fim']);
    $anterior = (new \DateTime($mes))->modify('-1 month');
    $limite = (int) $anterior->format('t');
    $deslocar = function ($data) use ($anterior, $limite) {
        return $anterior->format('Y-m-') . str_pad(min((int) substr($data, 8, 2), $limite), 2, '0', STR_PAD_LEFT);
    };
    return array('atual' => $atual, 'anterior' => array('inicio' => $deslocar($atual['inicio']), 'fim' => $deslocar($atual['fim'])));
}

function dashboardComercialVariacao($atual, $anterior)
{
    $atual = $atual === null ? null : round((float) $atual, 2);
    $anterior = $anterior === null ? null : round((float) $anterior, 2);
    $resultado = array('atual' => $atual, 'anterior' => $anterior, 'percentual' => null, 'tendencia' => 'sem_base');
    if ($atual === null || $anterior === null) return $resultado;
    $diferenca = round($atual - $anterior, 2);
    $resultado['tendencia'] = $diferenca > 0 ? 'alta' : ($diferenca < 0 ? 'baixa' : 'estavel');
    // Base zero não permite percentual; valores negativos usam o módulo da base.
    $resultado['percentual'] = $anterior != 0 ? 100 * $diferenca / abs($anterior) : ($diferenca == 0 ? 0.0 : null);
    return $resultado;
}

function dashboardComercialComparar($conexao, $sqls, $parametros, $comercial, &$erro)
{
    $periodos = dashboardComercialPeriodosComparacao($comercial['periodo']);
    $parametros['dataInicio'] = $periodos['anterior']['inicio'];
    $parametros['dataFim'] = $periodos['anterior']['fim'];
    $anteriores = array();
    foreach (array('orcamentos', 'pedidos', 'notas') as $tipo) {
        // Mesmas fontes e restrições; apenas as datas mudam. Não altera a matriz atual.
        $sql = 'SELECT COALESCE(SUM(valor), 0) AS valor, COUNT(*) AS quantidade FROM (' . $sqls[$tipo] . ') AS comercial_comparativo';
        $linhas = dashboardIndicadorConsultar($conexao, $sql, $parametros, $erro);
        if ($linhas === false) return false;
        if (count($linhas) !== 1 || !isset($linhas[0]['valor'], $linhas[0]['quantidade'])) {
            $erro = 'Não foi possível obter a base do comparativo mensal.'; return false;
        }
        $anteriores[$tipo] = $linhas[0];
    }
    $r = $comercial['resumo'];
    $notas = $anteriores['notas'];
    $pedidos = $anteriores['pedidos'];
    $orcamentos = $anteriores['orcamentos'];
    return array('periodos' => $periodos, 'metricas' => array(
        'faturamento' => dashboardComercialVariacao($r['faturamento'], $notas['valor']),
        'valorPedidos' => dashboardComercialVariacao($r['valorPedidos'], $pedidos['valor']),
        'ticketMedio' => dashboardComercialVariacao($r['ticketMedio'], (int) $pedidos['quantidade'] > 0 ? (float) $pedidos['valor'] / (int) $pedidos['quantidade'] : null),
        'ticketMedioOrcamento' => dashboardComercialVariacao($r['ticketMedioOrcamento'], (int) $orcamentos['quantidade'] > 0 ? (float) $orcamentos['valor'] / (int) $orcamentos['quantidade'] : null),
        'valorOrcado' => dashboardComercialVariacao($comercial['totaisMeses'][$comercial['mesReferencia']]['orcamentos'], $anteriores['orcamentos']['valor'])
    ));
}

function dashboardComercialAgruparCarteira($cadastros, $comercial)
{
    $zerados = array('orcamentos' => 0.0, 'pedidos' => 0.0, 'notas' => 0.0);
    $meses = array_fill_keys($comercial['meses'], $zerados);
    $movimentos = array();
    foreach ($comercial['linhas'] as $linha) {
        $movimentos[json_encode(array($linha['responsavelId'], $linha['clienteId']))] = $linha;
    }
    $grupos = array(); $vistos = array(); $totais = $zerados; $totaisMeses = $meses;
    foreach ($cadastros as $cadastro) {
        $clienteId = (string) $cadastro['clienteId'];
        // Um cadastro possui um único vendedor principal e representante.
        if (isset($vistos[$clienteId])) continue;
        $vistos[$clienteId] = true;
        $id = (string) $cadastro['representanteId'];
        if (!isset($grupos[$id])) $grupos[$id] = array('representanteId' => $id,
            'representante' => $cadastro['representante'], 'clientes' => array(), 'meses' => $meses, 'totais' => $zerados);
        $chave = json_encode(array((string) $cadastro['vendedorId'], $clienteId));
        $movimento = isset($movimentos[$chave]) ? $movimentos[$chave] : array('meses' => array(), 'totais' => $zerados);
        // Carteiras consolidadas têm muitos clientes sem movimento: não repetir 12 meses zerados para cada cadastro.
        $mesesCliente = array_filter($movimento['meses'], function ($valores) {
            return $valores['orcamentos'] != 0 || $valores['pedidos'] != 0 || $valores['notas'] != 0;
        });
        $cliente = array_merge($cadastro, array('meses' => $mesesCliente, 'totais' => $movimento['totais']));
        $grupos[$id]['clientes'][] = $cliente;
        foreach ($zerados as $tipo => $zero) {
            $grupos[$id]['totais'][$tipo] += $cliente['totais'][$tipo];
            $totais[$tipo] += $cliente['totais'][$tipo];
            foreach ($cliente['meses'] as $mes => $valores) {
                $grupos[$id]['meses'][$mes][$tipo] += $cliente['meses'][$mes][$tipo];
                $totaisMeses[$mes][$tipo] += $cliente['meses'][$mes][$tipo];
            }
        }
    }
    $grupos = array_values($grupos);
    dashboardComercialOrdenarValores($grupos, 'representante', 'representanteId');
    foreach ($grupos as &$grupo) dashboardComercialOrdenarValores($grupo['clientes'], 'cliente', 'clienteId');
    unset($grupo);
    return array('linhas' => $grupos, 'totais' => $totais, 'totaisMeses' => $totaisMeses);
}

function dashboardComercialAgruparRegioes($cadastros, $comercial)
{
    $ufs = array(); $vistos = array(); $movimentos = array();
    $zerados = array('orcamentos' => 0.0, 'pedidos' => 0.0);
    $totais = $zerados;
    foreach ($comercial['linhas'] as $linha) $movimentos[json_encode(array($linha['responsavelId'], $linha['clienteId']))] = $linha;
    $validas = explode(',', 'AC,AL,AP,AM,BA,CE,DF,ES,GO,MA,MT,MS,MG,PA,PB,PR,PE,PI,RJ,RN,RS,RO,RR,SC,SP,SE,TO');
    foreach ($cadastros as $cadastro) {
        $id = (string) $cadastro['clienteId'];
        if (isset($vistos[$id])) continue;
        $vistos[$id] = true;
        $uf = strtoupper(trim((string) $cadastro['uf']));
        if (!in_array($uf, $validas, true)) $uf = '';
        $cidadeId = (string) $cadastro['cidadeId'];
        if (!isset($ufs[$uf])) $ufs[$uf] = array('uf' => $uf, 'cidades' => array(), 'quantidade' => 0, 'totais' => $zerados);
        if (!isset($ufs[$uf]['cidades'][$cidadeId])) $ufs[$uf]['cidades'][$cidadeId] = array('cidadeId' => $cidadeId,
            'cidade' => trim((string) $cadastro['cidade']) !== '' ? $cadastro['cidade'] : 'Cidade não informada', 'clientes' => array(), 'quantidade' => 0, 'totais' => $zerados);
        if (array_key_exists('regiaoId', $cadastro)) {
            $ufs[$uf]['cidades'][$cidadeId]['regiaoId'] = (string) $cadastro['regiaoId'];
            $ufs[$uf]['cidades'][$cidadeId]['regiao'] = (string) $cadastro['regiao'];
            $ufs[$uf]['cidades'][$cidadeId]['cidadeIbge'] = (string) $cadastro['cidadeIbge'];
        }
        $chave = json_encode(array((string) $cadastro['vendedorId'], $id));
        $movimento = isset($movimentos[$chave]) ? $movimentos[$chave] : array('totais' => $zerados, 'meses' => array());
        $valores = $movimento['totais'];
        $mesesCliente = array();
        foreach ($movimento['meses'] as $mes => $mensal) $mesesCliente[$mes] = array_intersect_key($mensal, $zerados);
        $cliente = array_merge($cadastro, array('uf' => $uf, 'totais' => array_intersect_key($valores, $zerados), 'meses' => $mesesCliente));
        $ufs[$uf]['cidades'][$cidadeId]['clientes'][] = $cliente;
        $ufs[$uf]['quantidade']++; $ufs[$uf]['cidades'][$cidadeId]['quantidade']++;
        foreach ($zerados as $tipo => $zero) {
            $ufs[$uf]['totais'][$tipo] += $valores[$tipo];
            $ufs[$uf]['cidades'][$cidadeId]['totais'][$tipo] += $valores[$tipo];
            $totais[$tipo] += $valores[$tipo];
        }
    }
    $ufs = array_values($ufs);
    dashboardComercialOrdenarValores($ufs, 'uf', 'uf');
    foreach ($ufs as &$uf) {
        $uf['cidades'] = array_values($uf['cidades']);
        dashboardComercialOrdenarValores($uf['cidades'], 'cidade', 'cidadeId');
        foreach ($uf['cidades'] as &$cidade) dashboardComercialOrdenarValores($cidade['clientes'], 'cliente', 'clienteId');
        unset($cidade);
        $regioes = array();
        foreach ($uf['cidades'] as $cidade) {
            if (!isset($cidade['regiaoId'])) continue;
            $id = $cidade['regiaoId'];
            if (!isset($regioes[$id])) $regioes[$id] = array('regiaoId' => $id, 'regiao' => $cidade['regiao'], 'quantidade' => 0, 'totais' => $zerados);
            $regioes[$id]['quantidade'] += $cidade['quantidade'];
            foreach ($zerados as $tipo => $zero) $regioes[$id]['totais'][$tipo] += $cidade['totais'][$tipo];
        }
        if ($regioes) {
            $uf['regioes'] = array_values($regioes);
            dashboardComercialOrdenarValores($uf['regioes'], 'regiao', 'regiaoId');
        }
    }
    unset($uf);
    return array('ufs' => array_values($ufs), 'quantidade' => count($vistos), 'totais' => $totais,
        'meses' => $comercial['meses'], 'periodo' => $comercial['periodo']);
}

// Registros mensais do ERP: não mistura metas de outros meses nem multiplica
// por clientes/empresas. Cadastros repetidos do mesmo vendedor são ambíguos.
function dashboardComercialMetaRegistrada($registros, $mes, &$erro)
{
    $meta = 0.0; $atingido = 0.0; $percentualPonderado = 0.0; $comAtingimento = true; $vistos = array();
    $inicio = $mes . '-01'; $fim = (new \DateTime($inicio))->format('Y-m-t');
    foreach ($registros as $registro) {
        if ($registro['inicio'] !== $inicio || $registro['fim'] !== $fim) continue;
        $id = (string) $registro['vendedorId'];
        if (isset($vistos[$id])) {
            $erro = 'Há mais de uma meta mensal para o mesmo vendedor. Revise o Acompanhamento Meta Vendedor.';
            return false;
        }
        $vistos[$id] = true;
        if (!is_numeric($registro['meta']) || !is_finite((float) $registro['meta']) || (float) $registro['meta'] <= 0) continue;
        $meta += (float) $registro['meta'];
        if ($registro['atingido'] === null || !is_numeric($registro['atingido']) || !is_finite((float) $registro['atingido'])
            || $registro['percentual'] === null || !is_numeric($registro['percentual']) || !is_finite((float) $registro['percentual'])) $comAtingimento = false;
        else {
            $atingido += (float) $registro['atingido'];
            $percentualPonderado += (float) $registro['percentual'] * (float) $registro['meta'];
        }
    }
    return array('meta' => $meta > 0 ? $meta : null, 'valorMetaAtingido' => $meta > 0 && $comAtingimento ? $atingido : null,
        'atingimentoMeta' => $meta > 0 && $comAtingimento ? $percentualPonderado / $meta : null);
}

function dashboardComercialHistoricoCliente($conexao, $indicador, $empresa, $entrada, &$erro)
{
    if (empty($indicador['desempenhoComercial']['habilitado'])
        || (isset($indicador['_abaComercial']) && $indicador['_abaComercial'] !== 'vendedores')) {
        $erro = 'Histórico disponível somente na aba Vendedores.';
        return false;
    }
    $cliente = isset($entrada['clienteId']) && is_string($entrada['clienteId']) ? $entrada['clienteId'] : '';
    $responsavel = isset($entrada['responsavelId']) && is_string($entrada['responsavelId']) ? $entrada['responsavelId'] : '';
    if (!preg_match('/^(cad|T003|T005|T007):[0-9]{1,12}$/D', $cliente) || !preg_match('/^[0-9]{1,12}$/D', $responsavel)) {
        $erro = 'Selecione um cliente válido na listagem.';
        return false;
    }
    $opcoes = dashboardIndicadorCarregarOpcoesFiltros($conexao, $indicador['filtros'], $empresa, $erro);
    if ($opcoes === false) return false;
    $indicador['filtros'] = $opcoes;
    $filtros = dashboardIndicadorFiltros($indicador, $entrada, $erros);
    if ($erros) { $erro = implode(' ', $erros); return false; }
    $hoje = new \DateTime('today');
    $inicio = (clone $hoje)->modify('first day of this month')->modify('-5 months');
    $meses = array();
    for ($mes = clone $inicio; $mes <= $hoje; $mes->modify('+1 month')) $meses[] = $mes->format('Y-m');
    $valores = array_fill_keys($meses, array('orcamentos' => 0.0, 'pedidos' => 0.0));
    // Reutiliza as fontes autorizadas, preservando empresa, equipe e vendedor.
    $parametros = array_merge(array('equipe' => ''), $filtros['valores'], array('empresa' => (int) $empresa,
        'dataInicio' => $inicio->format('Y-m-d'), 'dataFim' => $hoje->format('Y-m-d'),
        'historicoCliente' => $cliente, 'historicoResponsavel' => $responsavel));
    foreach (array('orcamentos', 'pedidos') as $tipo) {
        $sql = dashboardPersonalizadoSqlTexto($indicador['desempenhoComercial'][$tipo]);
        if (!dashboardIndicadorValidarSql($sql, $erro)) return false;
        $linhas = dashboardIndicadorConsultar($conexao, "SELECT DATE_FORMAT(data, '%Y-%m') AS mes, SUM(valor) AS valor
            FROM (" . $sql . ") AS comercial_historico
            WHERE clienteId = :historicoCliente AND responsavelId = :historicoResponsavel
            GROUP BY DATE_FORMAT(data, '%Y-%m') LIMIT 7", $parametros, $erro);
        if ($linhas === false) return false;
        foreach ($linhas as $linha) if (isset($valores[$linha['mes']])) $valores[$linha['mes']][$tipo] += (float) $linha['valor'];
    }
    return array('periodo' => array('inicio' => $inicio->format('Y-m-d'), 'fim' => $hoje->format('Y-m-d')),
        'meses' => $meses, 'totaisMeses' => $valores);
}

// Reutiliza a própria condição de equipe do JSON, inclusive as exceções dos
// supervisores. Não mantém uma segunda definição dessas regras no motor.
function dashboardComercialSubconsultasEquipe($sql)
{
    preg_match_all('/\bIN\s*\(\s*SELECT\s+(?:[a-zA-Z_][a-zA-Z0-9_]*\.)?C007_Id\s+FROM\b/i', $sql, $matches, PREG_OFFSET_CAPTURE);
    $result = array(); $literal = ''; $next = 0;
    foreach ($matches[0] as $match) {
        // Um texto que apenas descreve SQL nunca vira uma consulta de equipe.
        for (; $next < $match[1]; $next++) {
            $c = $sql[$next];
            if ($literal !== '') {
                if ($c === '\\') $next++;
                elseif ($c === $literal) { if (isset($sql[$next + 1]) && $sql[$next + 1] === $literal) $next++; else $literal = ''; }
            } elseif ($c === "'" || $c === '"' || $c === '`') $literal = $c;
        }
        if ($literal !== '') continue;
        $open = strpos($sql, '(', $match[1]); $depth = 1; $quote = ''; $length = strlen($sql);
        for ($end = $open + 1; $end < $length; $end++) {
            $c = $sql[$end];
            if ($quote !== '') {
                if ($c === '\\') $end++;
                elseif ($c === $quote) { if ($end + 1 < $length && $sql[$end + 1] === $quote) $end++; else $quote = ''; }
                continue;
            }
            if ($c === "'" || $c === '"' || $c === '`') { $quote = $c; continue; }
            if ($c === '(') $depth++;
            elseif ($c === ')' && --$depth === 0) break;
        }
        if ($depth !== 0) continue;
        $source = trim(substr($sql, $open + 1, $end - $open - 1));
        // Só materializa subconsultas independentes. Uma referência ao SELECT
        // externo permanece no SQL original, sem modificar sua correlação.
        preg_match_all('/\b(?:FROM|JOIN)\s+([a-zA-Z_][a-zA-Z0-9_]*)(?:\s+(?:AS\s+)?([a-zA-Z_][a-zA-Z0-9_]*))?/i', $source, $tables);
        preg_match_all('/\)\s+(?:AS\s+)?([a-zA-Z_][a-zA-Z0-9_]*)/i', $source, $derived);
        preg_match_all('/\b([a-zA-Z_][a-zA-Z0-9_]*)\.[a-zA-Z_]/', $source, $references);
        if (array_diff($references[1], array_merge($tables[1], $tables[2], $derived[1]))) continue;
        if (strpos($source, ':equipe') !== false) $result[] = array('inicio' => $open + 1, 'fim' => $end, 'sql' => $source);
    }
    return $result;
}

function dashboardComercialChaveEquipe($sql)
{
    $normal = ''; $quote = ''; $length = strlen($sql);
    for ($i = 0; $i < $length; $i++) {
        $c = $sql[$i];
        if ($quote !== '') {
            $normal .= $c;
            if ($c === '\\' && $i + 1 < $length) $normal .= $sql[++$i];
            elseif ($c === $quote) { if ($i + 1 < $length && $sql[$i + 1] === $quote) $normal .= $sql[++$i]; else $quote = ''; }
        } elseif ($c === "'" || $c === '"' || $c === '`') { $quote = $c; $normal .= $c; }
        elseif (ctype_space($c)) { if ($normal !== '' && substr($normal, -1) !== ' ') $normal .= ' '; }
        else $normal .= $c;
    }
    return hash('sha256', trim($normal));
}

function dashboardComercialSqlEquipe($conexao, $sql, &$parametros, &$equipes, &$erro)
{
    if (!isset($parametros['equipe']) || $parametros['equipe'] === '') return $sql;
    foreach (array_reverse(dashboardComercialSubconsultasEquipe($sql)) as $source) {
        $key = dashboardComercialChaveEquipe($source['sql']);
        if (!array_key_exists($key, $equipes)) {
            if (!dashboardIndicadorValidarSql($source['sql'], $erro)) return false;
            $rows = dashboardIndicadorConsultar($conexao, 'SELECT equipe.C007_Id AS vendedorId FROM (' . $source['sql'] . ') AS equipe LIMIT 2001', $parametros, $erro);
            if ($rows === false) return false;
            // Uma equipe maior continua no SQL original, sem truncar seu acesso.
            if (count($rows) > 2000) { $equipes[$key] = null; continue; }
            $ids = array();
            foreach ($rows as $row) {
                if (!isset($row['vendedorId']) || !preg_match('/^-?[0-9]+$/D', (string) $row['vendedorId'])) {
                    $erro = 'Não foi possível validar os vendedores da equipe.'; return false;
                }
                $ids[(string) $row['vendedorId']] = (string) $row['vendedorId'];
            }
            if (isset($parametros['vendedor']) && $parametros['vendedor'] !== '' && !isset($ids[(string) $parametros['vendedor']])) {
                $erro = 'O vendedor selecionado não pertence à equipe selecionada. Revise os dois filtros.';
                return false;
            }
            $names = array(); $prefix = 'comercialEquipe' . count($equipes) . 'Id';
            foreach (array_values($ids) as $index => $id) {
                $name = $prefix . $index;
                if (array_key_exists($name, $parametros)) { $erro = 'Parâmetro reservado para a equipe.'; return false; }
                $parametros[$name] = $id; $names[] = ':' . $name;
            }
            $equipes[$key] = $names ? implode(',', $names) : 'SELECT C007_Id FROM C007 WHERE 1=0';
        }
        if ($equipes[$key] !== null) $sql = substr_replace($sql, $equipes[$key], $source['inicio'], $source['fim'] - $source['inicio']);
    }
    return $sql;
}

function dashboardComercialExecutar($conexao, $indicador, $empresa, $filtros, &$erro, $entrada = array())
{
    if (isset($indicador['_abaComercial']) && $indicador['_abaComercial'] === 'pedidos')
        return dashboardComercialPedidosExecutar($conexao, $indicador, $empresa, $filtros, $entrada, $erro);
    $periodo = dashboardComercialPeriodo($filtros['valores'], $erro, !empty($indicador['desempenhoComercial']['carregamentoProgressivo']));
    if ($periodo === false) return false;
    $config = $indicador['desempenhoComercial'];
    $parametros = array_merge(array('equipe' => ''), $filtros['valores'], array('empresa' => (int) $empresa));
    $equipes = array();
    $fontes = array();
    $sqls = array();
    $clientes = array();
    foreach (array('orcamentos', 'pedidos', 'notas') as $tipo) {
        $sql = dashboardPersonalizadoSqlTexto($tipo === 'notas' ? $indicador['sqlBase'] : (isset($config[$tipo]) ? $config[$tipo] : ''));
        if (!dashboardIndicadorValidarSql($sql, $erro)) return false;
        if (!empty($config['equipes'])) {
            $sql = dashboardComercialSqlEquipe($conexao, $sql, $parametros, $equipes, $erro);
            if ($sql === false) return false;
        }
        $sqls[$tipo] = $sql;
        $segmento = !empty($config['segmentos']) ? ', segmento' : '';
        $sql = "SELECT clienteId, responsavelId$segmento, MAX(cliente) AS cliente, MAX(responsavel) AS responsavel,
            DATE_FORMAT(data, '%Y-%m') AS mes, SUM(valor) AS valor, COUNT(*) AS quantidade,
            SUM(convertido) AS convertidos FROM (" . $sql . ") AS comercial_base
            GROUP BY clienteId, responsavelId$segmento, DATE_FORMAT(data, '%Y-%m')
            ORDER BY clienteId, responsavelId, mes$segmento LIMIT 12001";
        $fontes[$tipo] = dashboardIndicadorConsultar($conexao, $sql, $parametros, $erro);
        if ($fontes[$tipo] === false) return false;
        if (!dashboardCargaAtiva() && count($fontes[$tipo]) > 12000) {
            $erro = 'O período possui muitos registros. Filtre um vendedor, representante, cliente ou período menor.';
            return false;
        }
        foreach ($fontes[$tipo] as $linha) {
            $clientes[json_encode(array((string) $linha['responsavelId'], (string) $linha['clienteId']))] = true;
        }
        if (!dashboardCargaAtiva() && count($clientes) > 2000) {
            $erro = 'Há mais de 2.000 vínculos de clientes no período. Selecione um responsável, cliente ou período menor.';
            return false;
        }
    }
    $comercial = dashboardComercialConsolidar($fontes, $periodo, date('Y-m-d'));
    if (!empty($config['segmentos'])) {
        foreach ($fontes as $tipo => $linhas) $comercial['analiseComercial']['segmentos'][$tipo] = dashboardAnaliseAgrupar($linhas, 'segmento');
    }
    if (!empty($config['cancelamentos'])) {
        $sql = dashboardPersonalizadoSqlTexto($config['cancelamentos']);
        if (!dashboardIndicadorValidarSql($sql, $erro)) return false;
        if (!empty($config['equipes'])) {
            $sql = dashboardComercialSqlEquipe($conexao, $sql, $parametros, $equipes, $erro);
            if ($sql === false) return false;
        }
        $linhas = dashboardIndicadorConsultar($conexao, "SELECT motivoCancelamento AS motivo, COUNT(*) AS quantidade, SUM(valor) AS valor
            FROM ($sql) AS comercial_base GROUP BY motivoCancelamento ORDER BY motivoCancelamento LIMIT 2001", $parametros, $erro);
        if ($linhas === false) return false;
        if (!dashboardCargaAtiva() && count($linhas) > 2000) { $erro = 'Há muitos motivos de cancelamento. Reduza o período.'; return false; }
        $comercial['analiseComercial']['cancelamentos'] = dashboardAnaliseAgrupar($linhas, 'motivo');
    }
    if (!empty($config['metasVendedores'])) {
        $sqlMetas = dashboardPersonalizadoSqlTexto($config['metasVendedores']);
        if (!dashboardIndicadorValidarSql($sqlMetas, $erro)) return false;
        if (!empty($config['equipes'])) {
            $sqlMetas = dashboardComercialSqlEquipe($conexao, $sqlMetas, $parametros, $equipes, $erro);
            if ($sqlMetas === false) return false;
        }
        $parametrosMetas = array_merge($parametros, array('metaInicio' => $comercial['mesReferencia'] . '-01',
            'metaFim' => (new \DateTime($comercial['mesReferencia'] . '-01'))->format('Y-m-t')));
        $registrosMetas = dashboardIndicadorConsultar($conexao, 'SELECT vendedorId, inicio, fim, meta, atingido, percentual FROM (' . $sqlMetas . ') AS comercial_metas LIMIT 2001', $parametrosMetas, $erro);
        if ($registrosMetas === false) return false;
        $erroMeta = '';
        if (count($registrosMetas) > 2000) $erroMeta = 'Há mais de 2.000 metas no mês. Selecione um vendedor.';
        $meta = $erroMeta === '' ? dashboardComercialMetaRegistrada($registrosMetas, $comercial['mesReferencia'], $erroMeta) : false;
        if ($meta === false) $comercial['resumo']['metaAviso'] = $erroMeta;
        else $comercial['resumo'] = array_merge($comercial['resumo'], $meta);
    }
    if (!empty($config['comparativoMesAnterior'])) {
        $comercial['comparativo'] = dashboardComercialComparar($conexao, $sqls, $parametros, $comercial, $erro);
        if ($comercial['comparativo'] === false) return false;
    }
    $comercial['rotuloResponsavel'] = isset($config['rotuloResponsavel']) ? $config['rotuloResponsavel'] : 'Vendedor';
    // A consulta define o valor; esta opção identifica sua regra na tela e nas exportações.
    $comercial['valorNotas'] = isset($config['valorNotas']) && $config['valorNotas'] === 'total' ? 'total' : 'liquido';
    $publico = dashboardIndicadorDefinicaoPublica($indicador, $filtros['publicos']);
    $abasLocais = dashboardPersonalizadoAbasComerciaisLocais($indicador);
    // Clientes antigos continuam recebendo as abas completas. O frontend novo
    // solicita somente a carteira da aba ativa, com as mesmas fontes e filtros.
    $sobDemanda = $abasLocais && !empty($config['carregamentoProgressivo'])
        && isset($entrada['abaComercialSobDemanda']) && $entrada['abaComercialSobDemanda'] === '1';
    $abaComercial = isset($indicador['_abaComercial']) ? $indicador['_abaComercial'] : 'vendedores';
    if ($abasLocais) {
        $limiteCarteira = 20000;
        if (!$sobDemanda || $abaComercial === 'representantes') {
            $sqlCarteira = dashboardPersonalizadoSqlTexto($config['carteiraRepresentantes']);
            if (!dashboardIndicadorValidarSql($sqlCarteira, $erro)) return false;
            if (!empty($config['equipes'])) {
                $sqlCarteira = dashboardComercialSqlEquipe($conexao, $sqlCarteira, $parametros, $equipes, $erro);
                if ($sqlCarteira === false) return false;
            }
            $cadastros = dashboardIndicadorConsultar($conexao, 'SELECT clienteId, cliente, vendedorId, vendedor, representanteId, representante FROM (' . $sqlCarteira . ') AS comercial_carteira LIMIT ' . ($limiteCarteira + 1), $parametros, $erro);
            if ($cadastros === false) return false;
            if (count($cadastros) > $limiteCarteira) {
                $erro = 'Há mais de 20.000 clientes na carteira. Selecione um vendedor ou cliente.';
                return false;
            }
            $comercial['carteiraRepresentantes'] = dashboardComercialAgruparCarteira($cadastros, $comercial);
        }
        if (!empty($config['carteiraRegioes']) && (!$sobDemanda || $abaComercial === 'regioes')) {
            $sqlRegioes = dashboardPersonalizadoSqlTexto($config['carteiraRegioes']);
            if (!dashboardIndicadorValidarSql($sqlRegioes, $erro)) return false;
            if (!empty($config['equipes'])) {
                $sqlRegioes = dashboardComercialSqlEquipe($conexao, $sqlRegioes, $parametros, $equipes, $erro);
                if ($sqlRegioes === false) return false;
            }
            $camposRegiao = !empty($config['territoriosRegioes']) ? ', regiaoId, regiao, cidadeIbge' : '';
            $cadastrosRegioes = dashboardIndicadorConsultar($conexao, 'SELECT clienteId, cliente, vendedorId, vendedor, uf, cidadeId, cidade' . $camposRegiao . ' FROM (' . $sqlRegioes . ') AS comercial_regioes LIMIT ' . ($limiteCarteira + 1), $parametros, $erro);
            if ($cadastrosRegioes === false) return false;
            if (count($cadastrosRegioes) > $limiteCarteira) { $erro = 'Há mais de 20.000 clientes na carteira regional. Selecione um vendedor ou cliente.'; return false; }
            $comercial['carteiraRegioes'] = dashboardComercialAgruparRegioes($cadastrosRegioes, $comercial);
            if (!empty($config['territoriosRegioes'])) {
                $sqlTerritorios = dashboardPersonalizadoSqlTexto($config['territoriosRegioes']);
                if (!dashboardIndicadorValidarSql($sqlTerritorios, $erro)) return false;
                if (!empty($config['equipes'])) {
                    $sqlTerritorios = dashboardComercialSqlEquipe($conexao, $sqlTerritorios, $parametros, $equipes, $erro);
                    if ($sqlTerritorios === false) return false;
                }
                $territorios = dashboardIndicadorConsultar($conexao, 'SELECT uf, cidadeIbge, regiaoId, regiao FROM (' . $sqlTerritorios . ') AS comercial_territorios LIMIT 7001', $parametros, $erro);
                if ($territorios === false) return false;
                if (count($territorios) > 7000) { $erro = 'Há mais de 7.000 municípios no cadastro de regiões. Revise os vínculos de cidades.'; return false; }
                $comercial['carteiraRegioes']['territorios'] = $territorios;
            }
        }
    }
    foreach ($filtros['publicos'] as $filtro) {
        if (!$abasLocais && $filtro['id'] === 'vendedor' && !empty($filtro['bloqueado'])) {
            $publico['abas'] = array_values(array_filter($publico['abas'], function ($aba) use ($indicador) {
                return $aba['id'] === $indicador['id'];
            }));
        }
    }
    // Tabela normalizada compartilhada pelas exportações CSV/PDF.
    $publico['listagem'] = array('colunas' => array(
        array('campo' => 'responsavel', 'titulo' => $comercial['rotuloResponsavel']),
        array('campo' => 'cliente', 'titulo' => 'Cliente'), array('campo' => 'mes', 'titulo' => 'Mês'),
        array('campo' => 'orcamentos', 'titulo' => 'Orçamentos', 'formato' => 'moeda'),
        array('campo' => 'pedidos', 'titulo' => 'Pedidos', 'formato' => 'moeda'),
        array('campo' => 'notas', 'titulo' => $comercial['valorNotas'] === 'total' ? 'Notas emitidas (total)' : 'Notas emitidas (líquido)', 'formato' => 'moeda')
    ));
    $linhasTabela = $comercial['linhas'];
    $totaisTabela = $comercial['totais'];
    if ($abasLocais && isset($indicador['_abaComercial']) && $indicador['_abaComercial'] === 'representantes') {
        $publico['listagem']['colunas'][0]['titulo'] = 'Representante do cadastro';
        array_splice($publico['listagem']['colunas'], 1, 0, array(array('campo' => 'vendedor', 'titulo' => 'Vendedor principal')));
        array_pop($publico['listagem']['colunas']); // Carteira exibe/exporta apenas orçamentos e pedidos.
        $linhasTabela = array();
        foreach ($comercial['carteiraRepresentantes']['linhas'] as $grupo) foreach ($grupo['clientes'] as $cliente) {
            $cliente['responsavel'] = $grupo['representante'];
            $linhasTabela[] = $cliente;
        }
        $totaisTabela = $comercial['carteiraRepresentantes']['totais'];
    }
    $listagem = array();
    if (empty($indicador['_comercialSomenteVisual'])) {
        foreach ($linhasTabela as $linha) foreach ($comercial['meses'] as $mes) {
            if ($mes < substr($periodo['inicio'], 0, 7) || $mes > substr($periodo['fim'], 0, 7)) continue;
            $valores = isset($linha['meses'][$mes]) ? $linha['meses'][$mes] : array('orcamentos' => 0.0, 'pedidos' => 0.0, 'notas' => 0.0);
            $listagem[] = array_merge(array('responsavel' => $linha['responsavel'], 'cliente' => $linha['cliente'], 'mes' => substr($mes, 5, 2) . '/' . substr($mes, 0, 4)), isset($linha['vendedor']) ? array('vendedor' => $linha['vendedor']) : array(), $valores);
        }
        $listagem[] = array_merge(array('responsavel' => 'TOTAL DO PERÍODO', 'cliente' => '', 'mes' => ''), $totaisTabela);
    }
    if ($abasLocais && isset($indicador['_abaComercial']) && $indicador['_abaComercial'] === 'regioes') {
        $publico['listagem']['colunas'] = array(
            array('campo' => 'uf', 'titulo' => 'UF'), array('campo' => 'cidade', 'titulo' => 'Cidade'),
            array('campo' => 'vendedor', 'titulo' => 'Vendedor principal'), array('campo' => 'cliente', 'titulo' => 'Cliente'),
            array('campo' => 'orcamentos', 'titulo' => 'Orçamentos', 'formato' => 'moeda'),
            array('campo' => 'pedidos', 'titulo' => 'Pedidos', 'formato' => 'moeda'));
        $listagem = array();
        if (empty($indicador['_comercialSomenteVisual'])) {
            foreach ($comercial['carteiraRegioes']['ufs'] as $uf) foreach ($uf['cidades'] as $cidade) foreach ($cidade['clientes'] as $cliente) {
                $listagem[] = array_merge(array('uf' => $uf['uf'] !== '' ? $uf['uf'] : 'UF não informada',
                    'cidade' => $cidade['cidade'], 'vendedor' => $cliente['vendedor'], 'cliente' => $cliente['cliente']), $cliente['totais']);
            }
            $listagem[] = array_merge(array('uf' => 'TOTAL DO PERÍODO', 'cidade' => '', 'vendedor' => '', 'cliente' => ''), $comercial['carteiraRegioes']['totais']);
        }
    }
    return array('indicador' => $publico, 'desempenhoComercial' => $comercial,
        'card' => array('valor' => $comercial['totais']['notas'], 'quantidade' => $comercial['quantidades']['notas'], 'formato' => 'moeda'),
        'listagem' => $listagem, 'listagemDetalhes' => array(), 'comparativoRepresentantes' => array());
}

function dashboardComercialResumoExportacao($comercial)
{
    $r = $comercial['resumo'];
    $valorTotal = isset($comercial['valorNotas']) && $comercial['valorNotas'] === 'total';
    $linhaMeta = array('Meta atingida', 'Não definida', 'texto');
    if ($r['meta'] !== null) {
        $linhaMeta = $r['atingimentoMeta'] === null
            ? array('Meta do mês', $r['meta'], 'moeda')
            : array('Meta atingida (registro ERP)', $r['atingimentoMeta'], 'percentual');
    }
    $linhas = array(
        array('Mês de referência', substr($comercial['mesReferencia'], 5, 2) . '/' . substr($comercial['mesReferencia'], 0, 4), 'texto'),
        array($valorTotal ? 'Faturamento total no mês' : 'Faturamento líquido no mês', $r['faturamento'], 'moeda'),
        $linhaMeta,
        array('Projeção do faturamento no mês (dias corridos)', $r['projecao'] === null ? 'Não aplicável' : $r['projecao'], $r['projecao'] === null ? 'texto' : 'moeda'),
        array('Ticket médio por pedido', $r['ticketMedio'] === null ? 'Não aplicável' : $r['ticketMedio'], $r['ticketMedio'] === null ? 'texto' : 'moeda'),
        array('Ticket médio por orçamento', $r['ticketMedioOrcamento'] === null ? 'Não aplicável' : $r['ticketMedioOrcamento'], $r['ticketMedioOrcamento'] === null ? 'texto' : 'moeda'),
        array('Valor orçado no período filtrado', $r['valorOrcado'], 'moeda'),
        array('Conversão dos orçamentos do período em pedidos', $r['conversao'] === null ? 'Não aplicável' : $r['conversao'], $r['conversao'] === null ? 'texto' : 'percentual'),
        array('Valor dos pedidos no mês', $r['valorPedidos'], 'moeda'),
        array('Clientes atendidos no mês', $r['clientesAtendidos'], 'numero'),
        array('Clientes orçados no mês', $r['clientesOrcados'], 'numero')
    );
    if ($r['meta'] !== null) {
        $linhas[] = array('Meta mensal cadastrada', $r['meta'], 'moeda');
        $linhas[] = array('Valor atingido registrado no ERP (pedidos)', $r['valorMetaAtingido'] === null ? 'Não calculado' : $r['valorMetaAtingido'], $r['valorMetaAtingido'] === null ? 'texto' : 'moeda');
    }
    if (!empty($r['metaAviso'])) $linhas[] = array('Aviso da meta', $r['metaAviso'], 'texto');
    if (!empty($comercial['comparativo'])) {
        $c = $comercial['comparativo'];
        $formatarPeriodo = function ($p) { return implode('/', array_reverse(explode('-', $p['inicio']))) . ' a ' . implode('/', array_reverse(explode('-', $p['fim']))); };
        $linhas[] = array('Comparativo mensal: atual / anterior', $formatarPeriodo($c['periodos']['atual']) . ' / ' . $formatarPeriodo($c['periodos']['anterior']), 'texto');
        foreach (array('faturamento' => 'Faturamento', 'valorPedidos' => 'Valor dos pedidos', 'ticketMedio' => 'Ticket médio por pedido', 'ticketMedioOrcamento' => 'Ticket médio por orçamento', 'valorOrcado' => 'Valor orçado') as $chave => $titulo) {
            $m = $c['metricas'][$chave];
            foreach (array('atual' => 'mês de referência', 'anterior' => 'mês anterior') as $campo => $rotulo) {
                $linhas[] = array($titulo . ' (' . $rotulo . ', intervalo comparado)', $m[$campo] === null ? 'Sem base' : $m[$campo], $m[$campo] === null ? 'texto' : 'moeda');
            }
            $linhas[] = array($titulo . ': variação mensal', $m['percentual'] === null ? 'Sem base percentual' : $m['percentual'], $m['percentual'] === null ? 'texto' : 'percentual');
        }
    }
    return $linhas;
}

// A listagem usa o mesmo universo dos cartões comerciais e as restrições já
// aplicadas pelo endpoint. Detalhes de separação/carga ficam limitados à página.
function dashboardComercialPedidosFonte($conexao, $indicador, $empresa, $filtros, $entrada, &$erro)
{
    if (empty($indicador['desempenhoComercial']['habilitado']) || empty($indicador['desempenhoComercial']['pedidosListagem']['habilitado'])) {
        $erro = 'A listagem de pedidos não está configurada neste indicador.'; return false;
    }
    $periodo = dashboardComercialPeriodo($filtros['valores'], $erro, !empty($indicador['desempenhoComercial']['carregamentoProgressivo']));
    if ($periodo === false) return false;
    $params = array_merge(array('equipe' => ''), $filtros['valores'], array('empresa' => (int) $empresa));
    $sql = dashboardPersonalizadoSqlTexto($indicador['desempenhoComercial']['pedidos']);
    if (!dashboardIndicadorValidarSql($sql, $erro)) return false;
    if (!empty($indicador['desempenhoComercial']['equipes'])) {
        $equipes = array(); $sql = dashboardComercialSqlEquipe($conexao, $sql, $params, $equipes, $erro);
        if ($sql === false) return false;
    }
    $situacao = isset($entrada['situacaoPedidos']) && is_string($entrada['situacaoPedidos']) ? $entrada['situacaoPedidos'] : '';
    if (!in_array($situacao, array('', 'pendente', 'expedicao', 'com_nota', 'cancelado', 'negado', 'outras_etapas', 'atrasado'), true)) {
        $erro = 'Situação de pedido inválida.'; return false;
    }
    $where = '';
    if ($situacao === 'atrasado') $where = " WHERE COALESCE(comNota,0)=0 AND entrega < CURRENT_DATE() AND entrega > '0000-00-00' AND categoria NOT IN ('cancelado','negado')";
    elseif ($situacao !== '') { $where = ' WHERE categoria=:situacaoPedidos'; $params['situacaoPedidos'] = $situacao; }
    return array('sql' => $sql, 'where' => $where, 'params' => $params, 'periodo' => $periodo, 'situacao' => $situacao);
}

function dashboardComercialPedidosExecutar($conexao, $indicador, $empresa, $filtros, $entrada, &$erro)
{
    $fonte = dashboardComercialPedidosFonte($conexao, $indicador, $empresa, $filtros, $entrada, $erro);
    if ($fonte === false) return false;
    $base = ' FROM (' . $fonte['sql'] . ') AS pedidos' . $fonte['where'];
    $resumos = dashboardIndicadorConsultar($conexao, "SELECT COUNT(*) AS quantidade, COALESCE(SUM(valor),0) AS valor,
        COUNT(DISTINCT clienteId) AS clientes,
        COALESCE(SUM(CASE WHEN comNota>0 THEN valor ELSE 0 END),0) AS valorComNota,
        COALESCE(SUM(CASE WHEN comNota>0 THEN 1 ELSE 0 END),0) AS quantidadeComNota,
        COALESCE(SUM(CASE WHEN comNota=0 THEN valor ELSE 0 END),0) AS valorSemNota,
        COALESCE(SUM(CASE WHEN categoria='expedicao' THEN valor ELSE 0 END),0) AS valorExpedicao,
        COALESCE(SUM(CASE WHEN categoria='expedicao' THEN 1 ELSE 0 END),0) AS quantidadeExpedicao" . $base, $fonte['params'], $erro);
    if ($resumos === false) return false;
    $resumo = $resumos[0];
    foreach (array('quantidade', 'clientes', 'quantidadeComNota', 'quantidadeExpedicao') as $campo) $resumo[$campo] = (int) $resumo[$campo];
    foreach (array('valor', 'valorComNota', 'valorSemNota', 'valorExpedicao') as $campo) $resumo[$campo] = (float) $resumo[$campo];
    $pagina = isset($entrada['paginaPedidos']) ? $entrada['paginaPedidos'] : '1';
    if (!is_scalar($pagina) || !preg_match('/^[1-9][0-9]{0,5}$/D', (string) $pagina)) { $erro = 'Página de pedidos inválida.'; return false; }
    $pagina = min((int) $pagina, max(1, (int) ceil($resumo['quantidade'] / 50)));
    $config = $indicador['desempenhoComercial']['pedidosListagem'];
    $ordenacaoHabilitada = !empty($config['ordenacaoHabilitada']);
    $campoOrdem = isset($entrada['ordemPedidos']) ? $entrada['ordemPedidos'] : 'data';
    $direcaoOrdem = isset($entrada['direcaoPedidos']) ? $entrada['direcaoPedidos'] : 'DESC';
    $camposOrdem = array('numero'=>'CAST(pedidos.numero AS UNSIGNED)', 'cliente'=>'pedidos.cliente',
        'data'=>'pedidos.data', 'valor'=>'pedidos.valor', 'status'=>'pedidos.status',
        'percentualSeparado'=>'detalhesOrdem.percentualSeparado', 'numeroCarga'=>'detalhesOrdem.numeroCarga',
        'origem'=>'CAST(pedidos.origem AS UNSIGNED)', 'faturamento'=>'pedidos.comNota', 'entrega'=>'pedidos.entrega');
    if (!is_string($campoOrdem) || !isset($camposOrdem[$campoOrdem]) || !is_string($direcaoOrdem)
        || !in_array($direcaoOrdem, array('ASC', 'DESC'), true)) {
        $erro = 'Ordenação de pedidos inválida.'; return false;
    }
    if (!$ordenacaoHabilitada) { $campoOrdem = 'data'; $direcaoOrdem = 'DESC'; }
    $basePagina = $base;
    if (in_array($campoOrdem, array('percentualSeparado','numeroCarga'), true)) {
        if (empty($config['detalhes'])) { $erro = 'Detalhes de pedidos não configurados para ordenação.'; return false; }
        $sqlOrdem = dashboardPersonalizadoSqlTexto($config['detalhes']);
        if (!dashboardIndicadorValidarSql($sqlOrdem, $erro)) return false;
        $basePagina = ' FROM (' . $fonte['sql'] . ') AS pedidos LEFT JOIN (' . $sqlOrdem
            . ') AS detalhesOrdem ON detalhesOrdem.id=pedidos.id' . $fonte['where'];
    }
    $offset = ($pagina - 1) * 50;
    // A ordenação dos detalhes precisa ocorrer antes do LIMIT. Não aplicar
    // janelas progressivas: o alias d dos detalhes está em uma subconsulta.
    $consultarPagina = in_array($campoOrdem, array('percentualSeparado','numeroCarga'), true)
        ? __NAMESPACE__ . '\dashboardIndicadorConsultarDireto' : __NAMESPACE__ . '\dashboardIndicadorConsultar';
    $linhas = $consultarPagina($conexao, 'SELECT pedidos.id, pedidos.numero, pedidos.data, pedidos.cliente, pedidos.valor, pedidos.status, pedidos.categoria, pedidos.origem, pedidos.comNota, pedidos.entrega'
        . $basePagina . ' ORDER BY ' . $camposOrdem[$campoOrdem] . ' ' . $direcaoOrdem . ', pedidos.id DESC LIMIT 51 OFFSET ' . $offset, $fonte['params'], $erro);
    if ($linhas === false) return false;
    $temMais = count($linhas) > 50; $linhas = array_slice($linhas, 0, 50);
    if ($linhas && !empty($config['detalhes'])) {
        $sql = dashboardPersonalizadoSqlTexto($config['detalhes']);
        if (!dashboardIndicadorValidarSql($sql, $erro)) return false;
        $params = $fonte['params']; $ids = array();
        foreach ($linhas as $n => $linha) { $ids[] = ':pedidoPagina' . $n; $params['pedidoPagina' . $n] = (int) $linha['id']; }
        // Estes IDs já foram autorizados e paginados. A consulta pontual não
        // recebe janelas de emissão: o alias d pertence à subconsulta interna.
        $detalhes = dashboardIndicadorConsultarDireto($conexao, 'SELECT id, percentualSeparado, numeroCarga FROM (' . $sql . ') AS detalhes
            WHERE id IN (' . implode(',', $ids) . ')', $params, $erro);
        if ($detalhes === false) return false;
        $porId = array(); foreach ($detalhes as $detalhe) $porId[(string) $detalhe['id']] = $detalhe;
        foreach ($linhas as &$linha) if (isset($porId[(string) $linha['id']]))
            $linha = array_merge($linha, array_intersect_key($porId[(string) $linha['id']], array_flip(array('percentualSeparado','numeroCarga'))));
        unset($linha);
    }
    foreach ($linhas as &$linha) {
        $linha['faturamento'] = !empty($linha['comNota']) ? 'Com NF autorizada' : 'Sem NF vinculada';
        $linha['percentualSeparado'] = isset($linha['percentualSeparado']) && (float) $linha['percentualSeparado'] > 0 ? (float) $linha['percentualSeparado'] : null;
        $linha['numeroCarga'] = isset($linha['numeroCarga']) && (int) $linha['numeroCarga'] > 0 ? $linha['numeroCarga'] : null;
        if (empty($linha['origem'])) $linha['origem'] = null;
    }
    unset($linha);
    $publico = dashboardIndicadorDefinicaoPublica($indicador, $filtros['publicos']);
    $colunas = array();
    foreach (array('numero'=>array('Nº do pedido','texto'), 'cliente'=>array('Cliente','texto'), 'data'=>array('Emissão','data'),
        'valor'=>array('Valor','moeda'), 'status'=>array('Situação','texto'), 'percentualSeparado'=>array('% separado','texto'),
        'numeroCarga'=>array('Nº carga','texto'), 'origem'=>array('Orçamento de origem','texto'), 'faturamento'=>array('Faturamento','texto'),
        'entrega'=>array('Entrega prevista','data')) as $campo=>$coluna) $colunas[] = array('campo'=>$campo,'titulo'=>$coluna[0],'formato'=>$coluna[1]);
    $publico['listagem'] = array('habilitada'=>true,'colunas'=>$colunas,
        'observacaoRodape'=>'Pedidos: página ' . $pagina . ', até 50 registros. Total no filtro: ' . $resumo['quantidade'] . '.');
    $publico['card']['titulo'] = 'Valor dos pedidos no período';
    $publico['card']['descricao'] = 'Total dos pedidos nos filtros selecionados.';
    $exportaveis = $linhas;
    foreach ($exportaveis as &$linha) $linha['percentualSeparado'] = $linha['percentualSeparado'] === null ? '—'
        : rtrim(rtrim(number_format($linha['percentualSeparado'], 2, ',', '.'), '0'), ',') . '%';
    unset($linha);
    return array('indicador'=>$publico, 'card'=>array('valor'=>$resumo['valor'],'quantidade'=>$resumo['quantidade'],'formato'=>'moeda'),
        'listagem'=>$exportaveis,'listagemDetalhes'=>array(),'comparativoRepresentantes'=>array(),
        'pedidosComerciais'=>array('periodo'=>$fonte['periodo'],'resumo'=>$resumo,'linhas'=>$linhas,'pagina'=>$pagina,
            'total'=>$resumo['quantidade'],'temMais'=>$temMais,'situacao'=>$fonte['situacao'],
            'impressaoDisponibilidade'=>!empty($config['impressaoDisponibilidade']),
            'ordenacaoHabilitada'=>$ordenacaoHabilitada, 'ordem'=>$campoOrdem, 'direcao'=>$direcaoOrdem,
            'exportarCsv'=>!isset($config['exportarCsv']) || $config['exportarCsv'] !== false,
            'ticketMedioCartao'=>!empty($config['ticketMedioCartao'])));
}

function dashboardComercialPedidoImpressao($conexao, $indicador, $autorizacao, $entrada, &$erro)
{
    $tela = isset($indicador['desempenhoComercial']['pedidosListagem']['impressaoDisponibilidade']) ? $indicador['desempenhoComercial']['pedidosListagem']['impressaoDisponibilidade'] : '';
    $id = isset($entrada['pedidoId']) ? $entrada['pedidoId'] : '';
    if (!is_string($tela) || !preg_match('/^[a-f0-9]{32}$/D', $tela) || !is_scalar($id) || !preg_match('/^[1-9][0-9]{0,9}$/D', (string) $id)) {
        $erro = 'Impressão de disponibilidade não configurada ou pedido inválido.'; return false;
    }
    $opcoes = dashboardIndicadorCarregarOpcoesFiltros($conexao, $indicador['filtros'], $autorizacao['empresa'], $erro);
    if ($opcoes === false) return false;
    $indicador['filtros'] = $opcoes;
    $filtros = dashboardIndicadorFiltros($indicador, $entrada, $erros);
    if ($erros) { $erro = implode(' ', $erros); return false; }
    $fonte = dashboardComercialPedidosFonte($conexao, $indicador, $autorizacao['empresa'], $filtros, $entrada, $erro);
    if ($fonte === false) return false;
    $params = array_merge($fonte['params'], array('pedidoImpressao'=>(int) $id));
    $sql = 'SELECT liberado.id FROM (SELECT id FROM (' . $fonte['sql'] . ') AS pedidos' . $fonte['where'] . ') liberado
        INNER JOIN T005 n ON n.T005_Id=liberado.id AND n.T005_C004_Id=:empresa WHERE liberado.id=:pedidoImpressao LIMIT 1';
    $linhas = dashboardIndicadorConsultar($conexao, $sql, $params, $erro);
    if ($linhas === false) return false;
    if (!$linhas) { $erro = 'Pedido não disponível nos filtros ou no acesso do seu usuário.'; return false; }
    return array('id'=>(int) $linhas[0]['id'],'tela'=>$tela);
}
