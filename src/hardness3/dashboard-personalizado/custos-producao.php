<?php
namespace hardness;

// Componente opt-in. As consultas e os vínculos específicos do cliente ficam
// na personalização; usa a conexão somente-leitura e o timeout do dashboard.
function dashboardCustosProducaoFontes($indicador, $origem, &$erro)
{
    $config = $indicador['custosProducao'];
    if (!isset($config['visao']) || !in_array($config['visao'], array('gerencial', 'detalhado'), true)) {
        $erro = 'Visão de custos inválida.'; return false;
    }
    $nomes = array('itens', 'materiais', 'consumos', 'processos', 'movimentos');
    $nomes = array_merge($nomes, $origem === 'pcp' ? array('participantes', 'eventos') : array('historico'));
    $fontes = array();
    foreach ($nomes as $nome) {
        $sql = dashboardPersonalizadoSqlTexto(isset($config['fontes'][$nome]) ? $config['fontes'][$nome] : '');
        if (!dashboardIndicadorValidarSql($sql, $erro, false, false, true)) return false;
        // Toda leitura tem o mesmo pedido e a mesma empresa. Os parâmetros
        // são vinculados pelo executor existente, nunca interpolados.
        if (strpos($sql, ':pedido') === false || preg_match('/\bLIMIT\b/i', $sql)) {
            $erro = 'A fonte de custos deve filtrar :pedido e deixar o limite para o executor.'; return false;
        }
        preg_match_all('/:([a-z][a-zA-Z0-9_]*)/', $sql, $params);
        if (array_diff($params[1], array('empresa', 'pedido', 'op'))) {
            $erro = 'Parâmetro inválido na fonte de custos.'; return false;
        }
        $fontes[$nome] = $sql;
    }
    return $fontes;
}

function dashboardCustosProducaoExecutar($conexao, $indicador, $empresa, $entrada, &$erro)
{
    $erro = '';
    $semPedido = !isset($entrada['f_pedido']) || $entrada['f_pedido'] === '';
    $configFiltros = $indicador;
    if ($semPedido) foreach ($configFiltros['filtros'] as &$filtro) {
        if ($filtro['id'] === 'pedido') $filtro['obrigatorio'] = false;
    }
    unset($filtro);
    $filtros = dashboardIndicadorFiltros($configFiltros, $entrada, $erros);
    if ($erros) { $erro = implode(' ', $erros); return false; }
    if ($semPedido) {
        $resultado = dashboardCustosProducaoResultado($indicador, $filtros['publicos'], array(), 'pcp', time());
        $resultado['indicador']['subtitulo'] = 'Informe o pedido para consultar os custos e as ordens de produção.';
        return $resultado;
    }
    $pedido = isset($filtros['valores']['pedido']) ? $filtros['valores']['pedido'] : '';
    $op = isset($filtros['valores']['op']) ? $filtros['valores']['op'] : '';
    $origem = isset($filtros['valores']['origem']) ? $filtros['valores']['origem'] : 'pcp';
    foreach (array($pedido, $op) as $n => $id) {
        if (($n === 0 || $id !== '') && (!preg_match('/^[1-9][0-9]{0,9}$/D', (string) $id) || (float) $id > 4294967295)) {
            $erro = 'Informe um número inteiro positivo para ' . ($n === 0 ? 'o pedido.' : 'a OP.'); return false;
        }
    }
    if (!in_array($origem, array('pcp', 'historico'), true)) { $erro = 'Origem de apontamentos inválida.'; return false; }
    $fontes = dashboardCustosProducaoFontes($indicador, $origem, $erro);
    if ($fontes === false) return false;
    $dados = array();
    $params = array('empresa' => (int) $empresa, 'pedido' => $pedido, 'op' => $op);
    foreach ($fontes as $nome => $sql) {
        $limite = $nome === 'itens' ? 500 : 10000;
        $linhas = dashboardIndicadorConsultar($conexao, $sql . ' LIMIT ' . ($limite + 1), $params, $erro);
        if ($linhas === false) return false;
        if (count($linhas) > $limite) { $erro = 'O pedido excedeu o limite de registros do MVP. Filtre uma OP.'; return false; }
        $dados[$nome] = $linhas;
        // Pedido inexistente, de outra empresa ou sem OP: não lê outras fontes.
        if ($nome === 'itens' && !$linhas) break;
    }
    // O relógio e as datas vêm da mesma sessão SQL. Interpretar todos no
    // mesmo fuso mantém as durações mesmo com outro timezone no servidor SQL.
    $dataInstante = !empty($dados['participantes'][0]['instante']) ? $dados['participantes'][0]['instante']
        : (!empty($dados['historico'][0]['instante']) ? $dados['historico'][0]['instante'] : null);
    $instante = $dataInstante ? (new \DateTimeImmutable($dataInstante, new \DateTimeZone('America/Sao_Paulo')))->getTimestamp() : time();
    $resultado = dashboardCustosProducaoResultado($indicador, $filtros['publicos'], $dados, $origem, $instante);
    foreach ($resultado['secoes'] as $secao) if (count($secao['linhas']) > 2000) {
        $erro = 'O detalhamento excedeu 2.000 linhas. Filtre uma OP.'; return false;
    }
    return $resultado;
}

function dashboardCustosProducaoSomar($valores)
{
    $soma = 0.0;
    foreach ($valores as $valor) {
        if ($valor === null || !is_numeric($valor) || !is_finite((float) $valor)) return null;
        $soma += (float) $valor;
    }
    return round($soma, 2);
}

function dashboardCustosProducaoTempo($segundos)
{
    if ($segundos === null) return null;
    $segundos = max(0, (int) round($segundos));
    return sprintf('%02d:%02d:%02d', intdiv($segundos, 3600), intdiv($segundos % 3600, 60), $segundos % 60);
}

// Intervalos por participante: pausas não são trabalho; eventos inativos já
// são excluídos no SQL. Usa um instante único para todas as atividades abertas.
function dashboardCustosProducaoIntervalos($inicio, $fim, $eventos, $agora)
{
    $tz = new \DateTimeZone('America/Sao_Paulo');
    $timestamp = function ($data) use ($tz) {
        if (!$data || substr($data, 0, 4) === '0000') return null;
        try { return (new \DateTimeImmutable($data, $tz))->getTimestamp(); }
        catch (\Exception $e) { return null; }
    };
    $a = $timestamp($inicio); $b = $fim ? $timestamp($fim) : $agora;
    if ($a === null || $b === null || $b < $a || $a > $agora) return null;
    $b = min($b, $agora);
    usort($eventos, function ($x, $y) {
        $cmp = strcmp($x['dataHora'], $y['dataHora']);
        return $cmp ?: ((int) $x['id'] <=> (int) $y['id']);
    });
    $intervalos = array(); $cursor = $a; $ativo = true;
    foreach ($eventos as $e) {
        $t = $timestamp($e['dataHora']);
        if ($t === null || $t < $a || $t > $b) continue;
        if ($ativo && $t > $cursor) $intervalos[] = array($cursor, $t);
        if ($e['tipo'] === 'P' || $e['tipo'] === 'F') $ativo = false;
        if ($e['tipo'] === 'I' || $e['tipo'] === 'R') $ativo = true;
        $cursor = $t;
    }
    if ($ativo && $cursor < $b) $intervalos[] = array($cursor, $b);
    return $intervalos;
}

function dashboardCustosProducaoDuracao($intervalos, $unir = false)
{
    if ($intervalos === null) return null;
    if (!$intervalos) return 0;
    if (!$unir) return array_sum(array_map(function ($i) { return $i[1] - $i[0]; }, $intervalos));
    usort($intervalos, function ($a, $b) { return $a[0] <=> $b[0]; });
    $total = 0; $atual = array_shift($intervalos);
    foreach ($intervalos as $i) {
        if ($i[0] <= $atual[1]) $atual[1] = max($atual[1], $i[1]);
        else { $total += $atual[1] - $atual[0]; $atual = $i; }
    }
    return $total + $atual[1] - $atual[0];
}

function dashboardCustosProducaoColuna($campo, $titulo, $formato = 'texto')
{
    return array('campo' => $campo, 'titulo' => $titulo, 'formato' => $formato, 'textoNulo' => 'Não apurado');
}

function dashboardCustosProducaoSecao($id, $titulo, $subtitulo, $colunas, $linhas)
{
    return array('id' => $id, 'titulo' => $titulo, 'subtitulo' => $subtitulo,
        'larguraTotal' => true, 'colunas' => $colunas, 'linhas' => array_values($linhas));
}

function dashboardCustosProducaoResultado($indicador, $filtros, $dados, $origem, $agora)
{
    $ops = array(); $itens = array(); $processos = array(); $materiais = array();
    foreach (isset($dados['itens']) ? $dados['itens'] : array() as $i) {
        $id = (string) $i['op'];
        if (isset($itens[$i['id']])) continue;
        $itens[$i['id']] = $i;
        if (!isset($ops[$id])) $ops[$id] = array('op' => $id, 'pedido' => $i['pedido'], 'cliente' => $i['cliente'],
            'situacao' => $i['situacao'], 'produtos' => array(), 'orcados' => array(), 'compartilhada' => !empty($i['compartilhada']),
            'processoOrcado' => null, 'insumoOrcado' => null, 'processoPrevisto' => 0, 'insumoPrevisto' => 0,
            'processoRealizado' => 0, 'insumoRealizado' => 0, 'alertas' => array());
        $ops[$id]['produtos'][$i['codigo'] . ' — ' . $i['produto']] = true;
        $ops[$id]['orcados'][] = $i['custoOrcado'] === null ? null : (float) $i['custoOrcado'] * (float) $i['quantidade'];
    }
    foreach (isset($dados['processos']) ? $dados['processos'] : array() as $p) {
        if (!isset($itens[$p['item']])) continue;
        $i = $itens[$p['item']]; $op = (string) $i['op']; $key = $op . ':' . $p['processo'];
        if (!isset($processos[$key])) $processos[$key] = array('op' => $op, 'processo' => $p['processo'], 'operacao' => $p['descricao'],
            'previstoSeg' => 0, 'previsto' => 0, 'homemSeg' => 0, 'operacaoSeg' => 0,
            'taxas' => array(), 'recursos' => array(), 'intervalos' => array(), 'pendencias' => array());
        $r =& $processos[$key];
        $seg = $p['minutos'] === null ? null : max(0, (float) $p['minutos']) * 60 * (float) $i['quantidade'];
        $valor = $seg === null || $p['operadorMinuto'] === null || $p['maquinaMinuto'] === null
            ? null : $seg / 60 * ((float) $p['operadorMinuto'] + (float) $p['maquinaMinuto']);
        $r['previstoSeg'] = $seg === null || $r['previstoSeg'] === null ? null : $r['previstoSeg'] + $seg;
        $r['previsto'] = $valor === null || $r['previsto'] === null ? null : $r['previsto'] + $valor;
        // Várias linhas com a mesma operação só aceitam taxa única. Taxas
        // divergentes não são rateadas silenciosamente no apontamento novo.
        $r['taxas'][] = array($p['operadorMinuto'], $p['maquinaMinuto']);
        $r['recursos'][isset($p['recurso']) ? $p['recurso'] : 'unico'] = true;
        unset($r);
    }
    foreach (array('materiais', 'consumos') as $fonte) foreach (isset($dados[$fonte]) ? $dados[$fonte] : array() as $m) {
        if (!isset($itens[$m['item']])) continue;
        $i = $itens[$m['item']]; $op = (string) $i['op']; $key = $op . ':' . $m['produtoId'];
        if (!isset($materiais[$key])) $materiais[$key] = array('op' => $op, 'codigo' => $m['codigo'], 'produto' => $m['descricao'],
            'quantidadePrevista' => 0, 'previsto' => 0, 'quantidadeRealizada' => 0, 'realizado' => 0);
        $q = (float) $m['quantidade'] * (float) $i['quantidade'];
        $materiais[$key]['quantidadePrevista'] += $q;
        $v = $m['custoUnitario'] === null ? null : $q * (float) $m['custoUnitario'];
        $materiais[$key]['previsto'] = $v === null || $materiais[$key]['previsto'] === null ? null : $materiais[$key]['previsto'] + $v;
    }
    foreach (isset($dados['movimentos']) ? $dados['movimentos'] : array() as $m) {
        $op = (string) $m['op']; if (!isset($ops[$op])) continue;
        $key = $op . ':' . $m['produtoId'];
        if (!isset($materiais[$key])) $materiais[$key] = array('op' => $op, 'codigo' => $m['codigo'], 'produto' => $m['descricao'],
            'quantidadePrevista' => 0, 'previsto' => 0, 'quantidadeRealizada' => 0, 'realizado' => 0);
        $materiais[$key]['quantidadeRealizada'] += (float) $m['quantidade'];
        $v = $m['custoUnitario'] === null ? null : (float) $m['quantidade'] * (float) $m['custoUnitario'];
        $materiais[$key]['realizado'] = $v === null || $materiais[$key]['realizado'] === null ? null : $materiais[$key]['realizado'] + $v;
    }
    $eventos = array();
    foreach (isset($dados['eventos']) ? $dados['eventos'] : array() as $e) $eventos[$e['participante']][] = $e;
    $apontamentos = $origem === 'pcp' ? (isset($dados['participantes']) ? $dados['participantes'] : array()) : (isset($dados['historico']) ? $dados['historico'] : array());
    foreach ($apontamentos as $a) {
        $op = (string) $a['op']; if (!isset($ops[$op])) continue;
        $key = $op . ':' . $a['processo'];
        if (!isset($processos[$key])) $processos[$key] = array('op' => $op, 'processo' => $a['processo'], 'operacao' => $a['descricao'],
            'previstoSeg' => 0, 'previsto' => 0, 'homemSeg' => 0, 'operacaoSeg' => 0, 'taxas' => array(), 'recursos' => array(), 'intervalos' => array(), 'pendencias' => array());
        $r =& $processos[$key];
        $intervalos = dashboardCustosProducaoIntervalos($a['inicio'], $a['fim'], isset($eventos[$a['id']]) ? $eventos[$a['id']] : array(), $agora);
        if ($intervalos === null) $r['pendencias']['Datas inválidas no apontamento'] = true;
        else {
            $r['homemSeg'] += dashboardCustosProducaoDuracao($intervalos);
            $r['intervalos'] = array_merge($r['intervalos'], $intervalos);
        }
        if (!empty($a['aberto']) || !$a['fim'] || substr($a['fim'], 0, 4) === '0000') $r['pendencias']['Apontamento em andamento'] = true;
        unset($r);
    }
    foreach ($processos as &$r) {
        $r['operacaoSeg'] = dashboardCustosProducaoDuracao($r['intervalos'], true);
        $taxas = array();
        foreach ($r['taxas'] as $t) {
            if ($t[0] === null || $t[1] === null) continue;
            $taxas[(string) (float) $t[0] . ':' . (string) (float) $t[1]] = $t;
        }
        $valida = count($taxas) === 1 && count($r['recursos']) === 1 && count($r['taxas']) > 0 && !in_array(null, array_merge(...$r['taxas']), true);
        $taxa = $valida ? reset($taxas) : null;
        $r['realizado'] = $r['homemSeg'] === 0 ? 0 : ($taxa === null ? null
            : $r['homemSeg'] / 60 * (float) $taxa[0] + $r['operacaoSeg'] / 60 * (float) $taxa[1]);
        if (isset($r['pendencias']['Datas inválidas no apontamento'])) $r['realizado'] = null;
        if ($r['homemSeg'] > 0 && !$valida) $r['pendencias']['Taxa ausente, divergente ou múltiplos recursos na operação'] = true;
        $r['taxaHora'] = $valida ? ((float) $taxa[0] + (float) $taxa[1]) * 60 : null;
        $r['tempoPrevisto'] = dashboardCustosProducaoTempo($r['previstoSeg']);
        $r['tempoRealizado'] = dashboardCustosProducaoTempo($r['operacaoSeg']);
        $r['horasHomem'] = dashboardCustosProducaoTempo($r['homemSeg']);
        $r['percentual'] = $r['previstoSeg'] > 0 ? $r['operacaoSeg'] / $r['previstoSeg'] * 100 : null;
        $r['observacao'] = implode('; ', array_keys($r['pendencias']));
        $ops[$r['op']]['processoPrevisto'] = dashboardCustosProducaoSomar(array($ops[$r['op']]['processoPrevisto'], $r['previsto']));
        $ops[$r['op']]['processoRealizado'] = dashboardCustosProducaoSomar(array($ops[$r['op']]['processoRealizado'], $r['realizado']));
        foreach ($r['pendencias'] as $msg => $unused) $ops[$r['op']]['alertas'][$msg] = true;
        unset($r['taxas'], $r['recursos'], $r['intervalos'], $r['pendencias']);
    }
    unset($r);
    foreach ($materiais as &$m) {
        $op =& $ops[$m['op']];
        $op['insumoPrevisto'] = dashboardCustosProducaoSomar(array($op['insumoPrevisto'], $m['previsto']));
        $op['insumoRealizado'] = dashboardCustosProducaoSomar(array($op['insumoRealizado'], $m['realizado']));
        if ($m['previsto'] === null || $m['realizado'] === null) $op['alertas']['Insumo sem custo informado'] = true;
        $m['diferenca'] = $m['previsto'] === null || $m['realizado'] === null ? null : $m['realizado'] - $m['previsto'];
        unset($op);
    }
    unset($m);
    foreach ($ops as &$op) {
        $op['produto'] = implode(' | ', array_keys($op['produtos']));
        $op['totalOrcado'] = dashboardCustosProducaoSomar($op['orcados']);
        if ($op['totalOrcado'] === null) $op['alertas']['Pedido sem custo orçado informado'] = true;
        if ($op['compartilhada']) {
            // O apontamento e a baixa pertencem à OP inteira. Sem um critério
            // de rateio aprovado, não atribui custos de outro pedido a este.
            $op['processoRealizado'] = null; $op['insumoRealizado'] = null;
            $op['alertas']['OP compartilhada: realizado sem rateio no MVP'] = true;
        }
        $op['totalPrevisto'] = dashboardCustosProducaoSomar(array($op['processoPrevisto'], $op['insumoPrevisto']));
        $op['totalRealizado'] = dashboardCustosProducaoSomar(array($op['processoRealizado'], $op['insumoRealizado']));
        $op['desvio'] = $op['totalRealizado'] === null || $op['totalPrevisto'] === null ? null : $op['totalRealizado'] - $op['totalPrevisto'];
        $op['percentual'] = $op['desvio'] === null || $op['totalPrevisto'] <= 0 ? null : $op['desvio'] / $op['totalPrevisto'] * 100;
        $op['observacao'] = implode('; ', array_keys($op['alertas']));
        unset($op['produtos'], $op['orcados'], $op['alertas']);
    }
    unset($op);
    // Detalhes de OPs compartilhadas também preservam a fronteira do pedido.
    foreach ($materiais as &$m) if ($ops[$m['op']]['compartilhada']) {
        $m['quantidadeRealizada'] = null; $m['realizado'] = null; $m['diferenca'] = null;
    }
    unset($m);
    foreach ($processos as &$r) if ($ops[$r['op']]['compartilhada']) {
        foreach (array('homemSeg', 'operacaoSeg', 'tempoRealizado', 'horasHomem', 'realizado', 'percentual') as $campo) $r[$campo] = null;
        $r['observacao'] = 'OP compartilhada: realizado sem rateio no MVP';
    }
    unset($r);
    $col = __NAMESPACE__ . '\\dashboardCustosProducaoColuna';
    $camposTotais = array('totalOrcado', 'totalPrevisto', 'totalRealizado', 'desvio'); $totais = array();
    foreach ($camposTotais as $campo) $totais[$campo] = $ops ? dashboardCustosProducaoSomar(array_column($ops, $campo)) : null;
    $totais['percentual'] = $totais['desvio'] === null || $totais['totalPrevisto'] <= 0 ? null : $totais['desvio'] / $totais['totalPrevisto'] * 100;
    $resumos = array();
    foreach (array('totalOrcado' => 'Orçado no pedido', 'totalPrevisto' => 'Previsto na OP', 'totalRealizado' => 'Realizado apurado', 'desvio' => 'Desvio × previsto', 'percentual' => 'Desvio percentual') as $campo => $titulo)
        $resumos[] = array('titulo' => $titulo, 'descricao' => $campo === 'totalRealizado' ? 'Inclui a estimativa dos processos pelas taxas atuais da OP.' : '',
            'valor' => $totais[$campo], 'formato' => $campo === 'percentual' ? 'percentual' : 'moeda', 'textoNulo' => 'Não apurado');
    $gerencial = array($col('op', 'OP'), $col('situacao', 'Situação'), $col('produto', 'Produto'),
        $col('processoOrcado', 'Processos · orçado', 'moeda'), $col('processoPrevisto', 'Processos · previsto', 'moeda'), $col('processoRealizado', 'Processos · realizado¹', 'moeda'),
        $col('insumoOrcado', 'Insumos · orçado', 'moeda'), $col('insumoPrevisto', 'Insumos · previsto', 'moeda'), $col('insumoRealizado', 'Insumos · realizado', 'moeda'),
        $col('totalOrcado', 'Total · orçado', 'moeda'), $col('totalPrevisto', 'Total · previsto', 'moeda'), $col('totalRealizado', 'Total · realizado¹', 'moeda'), $col('observacao', 'Conferir'));
    $secoes = array();
    $totalOp = array('op' => 'TOTAL', 'produto' => '', 'situacao' => '', 'observacao' => '');
    foreach (array('processoOrcado', 'processoPrevisto', 'processoRealizado', 'insumoOrcado', 'insumoPrevisto', 'insumoRealizado', 'totalOrcado', 'totalPrevisto', 'totalRealizado') as $campo)
        $totalOp[$campo] = dashboardCustosProducaoSomar(array_column($ops, $campo));
    $linhasOps = array_values($ops); if ($ops) $linhasOps[] = $totalOp;
    if ($indicador['custosProducao']['visao'] === 'gerencial') {
        $secoes[] = dashboardCustosProducaoSecao('gerencial', 'Custo orçado, previsto e realizado por OP',
            'Orçado total: custo unitário registrado no pedido × quantidade da OP. A divisão histórica entre processos e insumos não está disponível no MVP.', $gerencial, $linhasOps);
    } else {
        $secoes[] = dashboardCustosProducaoSecao('insumos', 'Insumos das OPs', 'Previsto: composição e materiais de consumo da OP. Realizado: baixas de estoque vinculadas à OP, líquidas de devoluções.',
            array($col('op', 'OP'), $col('codigo', 'Código'), $col('produto', 'Insumo'), $col('quantidadePrevista', 'Qtd. prevista', 'numero'), $col('previsto', 'Custo previsto', 'moeda'),
                $col('quantidadeRealizada', 'Qtd. consumida', 'numero'), $col('realizado', 'Custo realizado', 'moeda'), $col('diferenca', 'Diferença', 'moeda')), $materiais);
        $secoes[] = dashboardCustosProducaoSecao('apontamentos', 'Resumo dos apontamentos', 'Tempos líquidos de pausas. Mão de obra por hora-homem; máquina pelo tempo de operação sem duplicar participantes simultâneos. ¹ Custo estimado com as taxas atuais da OP.',
            array($col('op', 'OP'), $col('operacao', 'Operação'), $col('taxaHora', 'Taxa base/h', 'moeda'), $col('tempoPrevisto', 'Tempo previsto'), $col('tempoRealizado', 'Tempo de operação'),
                $col('horasHomem', 'Horas-homem'), $col('previsto', 'Custo previsto', 'moeda'), $col('realizado', 'Custo realizado¹', 'moeda'), $col('percentual', '% tempo previsto', 'percentual'), $col('observacao', 'Conferir')), $processos);
        $resumoCols = array($col('op', 'OP'), $col('processoPrevisto', 'Processos · previsto', 'moeda'), $col('processoRealizado', 'Processos · realizado¹', 'moeda'),
            $col('insumoPrevisto', 'Insumos · previsto', 'moeda'), $col('insumoRealizado', 'Insumos · realizado', 'moeda'), $col('totalOrcado', 'Total · orçado', 'moeda'),
            $col('totalPrevisto', 'Total · previsto', 'moeda'), $col('totalRealizado', 'Total · realizado¹', 'moeda'), $col('observacao', 'Conferir'));
        $secoes[] = dashboardCustosProducaoSecao('pedido', 'Resumo dos custos do pedido', 'Totais das OPs selecionadas. Um valor não apurado impede apresentar o total como completo.', $resumoCols, $linhasOps);
    }
    $publico = dashboardIndicadorDefinicaoPublica($indicador, $filtros);
    $cliente = $ops ? reset($ops)['cliente'] : '';
    $publico['subtitulo'] = $ops ? 'Pedido ' . reset($ops)['pedido'] . ' · ' . $cliente : 'Nenhuma OP encontrada para o pedido e os filtros informados.';
    $publico['apresentacao']['criterios'] .= ' Origem: ' . ($origem === 'pcp' ? 'Apontamento PCP (novo).' : 'Histórico SMT009, separado do novo apontamento.');
    return array('indicador' => $publico, 'card' => array('valor' => count($ops), 'quantidade' => count($ops), 'formato' => 'numero'),
        'resumos' => $resumos, 'secoes' => $secoes, 'listagem' => array(), 'listagemDetalhes' => array(),
        'grafico' => array(), 'calendarios' => array(), 'graficosPorGrupo' => array(), 'composicao' => array(), 'geografia' => array());
}
