<?php
namespace hardness;
if (realpath(isset($_SERVER['SCRIPT_FILENAME']) ? $_SERVER['SCRIPT_FILENAME'] : '') === __FILE__) { http_response_code(404); exit; }

function portalRepresentanteDemonstracaoPermitida($conf, $conta, $servidor)
{
    if (!is_array($conta) || empty($conta['empresa']) || empty($conta['representante']) || empty($conta['nome'])) return false;
    $opcoes = portalRepresentanteConfig($conf);
    $config = isset($opcoes['demonstracaoLocal']) ? $opcoes['demonstracaoLocal'] : null;
    if ($config === null) {
        $arquivo = dirname(__DIR__) . '/dados_usuarios/portal-representante-demo-local.php';
        if (is_file($arquivo)) $config = require $arquivo;
    }
    if (!is_array($config) || empty($config['habilitado']) || !isset($config['representante'], $config['nome']) || !is_string($config['nome'])
        || (int) $config['representante'] !== (int) $conta['representante']
        || strcasecmp(preg_replace('/\s+/u', ' ', trim($config['nome'])), preg_replace('/\s+/u', ' ', trim($conta['nome']))) !== 0) return false;
    // A configuração explícita e a URL canônica precisam apontar para o ambiente local.
    $locais = array('localhost', '127.0.0.1', '[::1]');
    $raiz = isset($conf['urlRaiz']) && is_string($conf['urlRaiz']) ? parse_url($conf['urlRaiz']) : false;
    $host = isset($servidor['HTTP_HOST']) && is_string($servidor['HTTP_HOST']) ? parse_url('http://' . $servidor['HTTP_HOST'], PHP_URL_HOST) : '';
    return $raiz && isset($raiz['host']) && in_array(strtolower($raiz['host']), $locais, true) && in_array(strtolower((string) $host), $locais, true);
}

function portalRepresentanteDemonstracaoAtiva($conf, $conta, &$sessao, $servidor)
{
    if (!portalRepresentanteDemonstracaoPermitida($conf, $conta, $servidor)) return false;
    $chave = portalRepresentanteTenant($conf) . ':' . (int) $conta['empresa'] . ':' . (int) $conta['representante'];
    if (!isset($sessao['portalDemonstracao'][$chave])) $sessao['portalDemonstracao'][$chave] = true;
    return $sessao['portalDemonstracao'][$chave] === true;
}

function portalRepresentanteDemonstracaoAlternar($conf, $conta, &$sessao, $servidor, $ativo)
{
    if (!portalRepresentanteDemonstracaoPermitida($conf, $conta, $servidor)) throw new RepresentantePortalErro('Demonstração indisponível para este acesso.', 403);
    if (!is_string($ativo) || !in_array($ativo, array('0', '1'), true)) throw new RepresentantePortalErro('Opção inválida.');
    $chave = portalRepresentanteTenant($conf) . ':' . (int) $conta['empresa'] . ':' . (int) $conta['representante'];
    $sessao['portalDemonstracao'][$chave] = $ativo === '1';
    return array('ok' => true, 'demonstracao' => $ativo === '1');
}

function portalRepresentanteDemonstracaoDados($hoje = null)
{
    $agora = new \DateTimeImmutable($hoje === null ? date('Y-m-d') : $hoje);
    $dia = function ($data, $dias) { return (new \DateTimeImmutable($data))->modify(($dias >= 0 ? '+' : '') . $dias . ' days')->format('Y-m-d'); };
    $hoje = $agora->format('Y-m-d');
    $clientes = array('Indústria Horizonte — Exemplo', 'Metalúrgica Aurora — Exemplo', 'Comercial Vale Verde — Exemplo', 'Distribuidora Atlântica — Exemplo', 'Mecânica Paraná — Exemplo', 'Construções Pioneira — Exemplo', 'Equipamentos Sul — Exemplo');
    $dados = array('orcamentos' => array(), 'pedidos' => array(), 'notas' => array(), 'boletos' => array());
    for ($i = 0; $i < 90; $i++) {
        $emissao = $agora->modify('-' . (89 - $i) . ' days')->format('Y-m-d');
        $ultimo = $i === 89; $clienteId = 700 + $i % count($clientes); $cliente = $clientes[$i % count($clientes)];
        $centavos = 245000 + ($i * 173117) % 1850000;
        $cancelado = !$ultimo && $i % 17 === 0;
        $criarPedido = !$cancelado && ($ultimo || $i % 10 < 6) && ($ultimo || $dia($emissao, 1) <= $hoje);
        $pedidoCancelado = $criarPedido && !$ultimo && $i % 13 === 0;
        $convertido = $criarPedido && !$pedidoCancelado;
        $categoria = $cancelado ? 'cancelado' : ($convertido ? 'convertido' : ($i % 5 < 2 ? 'pendente' : ($i % 5 === 2 ? 'aprovado' : ($i % 5 === 3 ? 'vencido' : 'negado'))));
        if ($categoria === 'pendente' && $i % 2) $categoria = 'analise';
        if (!$cancelado && !$convertido && $i % 11 === 0) $categoria = 'finalizado';
        $rotulos = array('cancelado' => 'Cancelado', 'convertido' => 'Finalizado', 'pendente' => 'Pendente', 'analise' => 'Análise', 'finalizado' => 'Finalizado', 'aprovado' => 'Aprovado', 'vencido' => 'Vencido', 'negado' => 'Negado');
        $orcamento = array('id' => 700100 + $i, 'numero' => 'DEMO-ORC-' . (1000 + $i), 'clienteId' => $clienteId, 'cliente' => $cliente,
            'data' => $emissao, 'valor' => $centavos / 100, 'status' => $rotulos[$categoria], 'categoria' => $categoria, 'excluido' => $cancelado ? 1 : 0, 'convertido' => $convertido ? 1 : 0, 'origem' => null);
        $dados['orcamentos'][] = $orcamento;
        if (!$criarPedido) continue;
        $dataPedido = $ultimo ? $hoje : $dia($emissao, 1);
        $valorPedido = (int) round($centavos * .96);
        $dataNota = $ultimo ? $hoje : $dia($dataPedido, 2);
        $comNota = !$pedidoCancelado && ($ultimo || $i % 4 !== 0) && $dataNota <= $hoje;
        $entrega = $dia($dataPedido, 7);
        $catPedido = $pedidoCancelado ? 'cancelado' : ($comNota ? 'com_nota' : 'expedicao');
        $pedido = array('id' => 710100 + $i, 'numero' => 'DEMO-PED-' . (2000 + $i), 'clienteId' => $clienteId, 'cliente' => $cliente,
            'data' => $dataPedido, 'valor' => $valorPedido / 100, 'status' => $pedidoCancelado ? 'Cancelado' : ($comNota ? 'Aguard.coleta' : 'Separando'),
            'categoria' => $catPedido, 'excluido' => $pedidoCancelado ? 1 : 0, 'origem' => $orcamento['id'], 'comNota' => $comNota ? 1 : 0, 'entrega' => $entrega,
            'pendenteDireto' => 0, 'prazoVencido' => !$pedidoCancelado && !$comNota && $entrega < $hoje ? 1 : 0);
        $dados['pedidos'][] = $pedido;
        if (!$comNota) continue;
        $parcial = $i % 4 === 1;
        $valoresNotas = $parcial ? array((int) round($valorPedido * .6)) : array($valorPedido);
        if ($parcial && !$ultimo && $dia($dataNota, 10) <= $hoje && $i % 3 !== 0) $valoresNotas[] = $valorPedido - $valoresNotas[0];
        foreach ($valoresNotas as $parte => $valorNota) {
            $idNota = 720100 + $i * 2 + $parte;
            $dataNF = $dia($dataNota, $parte * 10); $parcelas = array();
            // A nota da prévia enviada pelo usuário apresenta os quatro cenários de títulos.
            $exemploTitulos = $i === 81 && $parte === 0;
            $quantidadeParcelas = $exemploTitulos ? 4 : 3;
            if ($ultimo || $i % 9 !== 0 || $exemploTitulos) for ($p = 0; $p < $quantidadeParcelas; $p++) {
                $valorParcela = $p === $quantidadeParcelas - 1 ? $valorNota - ($quantidadeParcelas - 1) * intdiv($valorNota, $quantidadeParcelas) : intdiv($valorNota, $quantidadeParcelas);
                $vencimento = $exemploTitulos ? $dia($hoje, array(-4,3,-1,14)[$p]) : ($ultimo && $p === 0 ? $hoje : $dia($dataNF, 7 + 14 * $p));
                $semente = ($i + $parte + $p) % 5;
                $recebido = $vencimento > $hoje || ($ultimo && $p === 0) ? 0 : ($semente < 2 ? $valorParcela : ($semente === 2 ? (int) round($valorParcela * .4) : 0));
                if ($exemploTitulos) $recebido = $p === 0 ? $valorParcela : ($p === 1 ? (int) round($valorParcela * .4) : 0);
                $saldo = $valorParcela - $recebido;
                $situacao = $saldo <= 0 ? 'pago' : ($vencimento < $hoje ? 'vencido' : ($recebido > 0 ? 'parcial' : 'aberto'));
                $parcela = array('id' => $exemploTitulos ? 749900 + $p : 730100 + $i * 6 + $parte * 3 + $p, 'numero' => $exemploTitulos ? 'DEMO-BOL-4162-0' . ($p+1) : 'DEMO-BOL-' . (3000 + $i * 6 + $parte * 3 + $p),
                    'clienteId' => $clienteId, 'cliente' => $cliente, 'data' => $dataNF, 'vencimento' => $vencimento,
                    'valor' => $valorParcela / 100, 'recebido' => $recebido / 100, 'saldo' => $saldo / 100,
                    'status' => $situacao, 'categoria' => $situacao, 'excluido' => 0, 'pagamento' => $recebido > 0 ? ($exemploTitulos ? $dia($hoje, $p === 0 ? -4 : -1) : min($hoje, $dia($vencimento, 1))) : '0000-00-00',
                    'nota' => 'DEMO-NF-' . (4000 + $i * 2 + $parte), 'notaId' => $idNota, 'nossoNumero' => $exemploTitulos ? 'DEMO4162P0' . ($p+1) : 'DEMONSTRAÇÃO', 'tipoCobranca' => 'Boleto de exemplo');
                $parcelas[] = $parcela; $dados['boletos'][] = $parcela;
            }
            $recebidoNota = round(array_sum(array_column($parcelas, 'recebido')), 2); $saldoNota = round(array_sum(array_column($parcelas, 'saldo')), 2);
            $dados['notas'][] = array('id' => $idNota, 'numero' => 'DEMO-NF-' . (4000 + $i * 2 + $parte), 'clienteId' => $clienteId, 'cliente' => $cliente,
                'data' => $dataNF, 'valor' => $valorNota / 100, 'recebido' => $recebidoNota, 'saldo' => $saldoNota,
                'cobrancas' => count($parcelas), 'status' => 'Autorizada', 'categoria' => $saldoNota > 0 ? 'em_aberto' : ($parcelas ? 'quitada' : 'sem_cobranca'), 'excluido' => 0, 'origem' => $pedido['id']);
        }
    }
    // Pedidos incluídos diretamente, ainda no status Pendente do ERP.
    foreach (array(3,8,13) as $i => $dias) {
        $emissao = $dia($hoje, -$dias); $entrega = $dia($emissao, 7);
        $dados['pedidos'][] = array('id' => 719900+$i, 'numero' => 'DEMO-PED-D' . (900+$i), 'clienteId' => 700+$i, 'cliente' => $clientes[$i],
            'data' => $emissao, 'valor' => array(5800,12450,7920)[$i], 'status' => 'Pendente', 'categoria' => 'pendente', 'excluido' => 0,
            'origem' => null, 'comNota' => 0, 'entrega' => $entrega, 'pendenteDireto' => 1, 'prazoVencido' => $entrega < $hoje ? 1 : 0);
    }
    return $dados;
}

function portalRepresentanteDemonstracaoFiltrar($linhas, $tipo, $filtros)
{
    return array_values(array_filter($linhas, function ($linha) use ($tipo, $filtros) {
        $data = $tipo === 'boletos' ? $linha['vencimento'] : $linha['data'];
        if ($data < $filtros['inicio'] || $data >= $filtros['limiteFim']) return false;
        if ($filtros['cliente'] !== '' && stripos($linha['cliente'], $filtros['cliente']) === false) return false;
        if (!empty($filtros['clienteId']) && (int) $linha['clienteId'] !== $filtros['clienteId']) return false;
        if ($filtros['numero'] !== '' && strcasecmp((string) $linha['numero'], $filtros['numero']) !== 0) return false;
        if ($filtros['etapa'] !== '') {
            $etapa = $filtros['etapa'];
            if ($tipo === 'pedidos' && in_array($etapa, array('pendente_direto','atrasado','sem_nota'), true)) {
                if ($etapa === 'pendente_direto' && empty($linha['pendenteDireto'])) return false;
                if ($etapa === 'atrasado' && empty($linha['prazoVencido'])) return false;
                if ($etapa === 'sem_nota' && ($linha['excluido'] || $linha['comNota'])) return false;
            } elseif ($linha['categoria'] !== $etapa) return false;
        }
        return $tipo !== 'boletos' || $filtros['status'] === '' || $linha['status'] === $filtros['status'];
    }));
}

function portalRepresentanteDemonstracaoResumo($tipo, $linhas, $hoje)
{
    $resumo = array_fill_keys(array('quantidade', 'valor', 'clientes', 'convertidos', 'pendentes', 'valorPendente', 'comNota', 'semNota', 'atrasados', 'pendentesDiretos', 'valorPendentesDiretos', 'naExpedicao', 'comOrcamento', 'recebido', 'saldo', 'notasAbertas', 'semCobranca', 'vencido', 'proximos7', 'pagos'), 0);
    $evolucao = array(); $situacoes = array(); $clientes = array(); $atrasos = array();
    $fim7 = (new \DateTimeImmutable($hoje))->modify('+7 days')->format('Y-m-d');
    foreach ($linhas as $linha) {
        $categoria = $linha['categoria'];
        if (!isset($situacoes[$categoria])) $situacoes[$categoria] = array('categoria' => $categoria, 'quantidade' => 0, 'valor' => 0);
        $situacoes[$categoria]['quantidade']++; $situacoes[$categoria]['valor'] += $linha['valor'];
        if ($linha['excluido']) continue;
        $resumo['quantidade']++; $resumo['valor'] += $linha['valor'];
        $idCliente = $linha['clienteId'];
        if (!isset($clientes[$idCliente])) $clientes[$idCliente] = array('clienteId' => $idCliente, 'cliente' => $linha['cliente'], 'quantidade' => 0, 'valor' => 0);
        $clientes[$idCliente]['quantidade']++; $clientes[$idCliente]['valor'] += $linha['valor'];
        $data = $tipo === 'boletos' ? $linha['vencimento'] : $linha['data'];
        if (!isset($evolucao[$data])) $evolucao[$data] = array('data' => $data, 'quantidade' => 0, 'valor' => 0, 'recebido' => 0);
        $evolucao[$data]['quantidade']++; $evolucao[$data]['valor'] += $linha['valor'];
        if ($tipo === 'orcamentos') {
            $resumo['convertidos'] += $linha['convertido'];
            if ($categoria === 'pendente') { $resumo['pendentes']++; $resumo['valorPendente'] += $linha['valor']; }
        } elseif ($tipo === 'pedidos') {
            $resumo['comNota'] += $linha['comNota'];
            if (!$linha['comNota']) $resumo['semNota']++;
            $resumo['atrasados'] += $linha['prazoVencido'];
            $resumo['pendentesDiretos'] += $linha['pendenteDireto'];
            if ($linha['pendenteDireto']) $resumo['valorPendentesDiretos'] += $linha['valor'];
            if ($categoria === 'expedicao') $resumo['naExpedicao']++;
            if ($linha['origem'] > 0) $resumo['comOrcamento']++;
        } else {
            $resumo['recebido'] += $linha['recebido']; $resumo['saldo'] += $linha['saldo'];
            if ($tipo === 'notas') { if ($linha['saldo'] > 0) $resumo['notasAbertas']++; if (!$linha['cobrancas']) $resumo['semCobranca']++; }
            else {
                $evolucao[$data]['recebido'] += $linha['recebido'];
                if ($linha['status'] === 'pago') $resumo['pagos']++;
                if ($linha['vencimento'] >= $hoje && $linha['vencimento'] <= $fim7) $resumo['proximos7'] += $linha['saldo'];
                if ($linha['status'] === 'vencido') {
                    $resumo['vencido'] += $linha['saldo'];
                    $dias = (int) (new \DateTimeImmutable($linha['vencimento']))->diff(new \DateTimeImmutable($hoje))->days;
                    $faixa = $dias <= 7 ? '1–7 dias' : ($dias <= 30 ? '8–30 dias' : ($dias <= 60 ? '31–60 dias' : 'Mais de 60 dias'));
                    if (!isset($atrasos[$faixa])) $atrasos[$faixa] = array('faixa' => $faixa, 'ordem' => $dias, 'quantidade' => 0, 'valor' => 0);
                    $atrasos[$faixa]['ordem'] = min($atrasos[$faixa]['ordem'], $dias); $atrasos[$faixa]['quantidade']++; $atrasos[$faixa]['valor'] += $linha['saldo'];
                }
            }
        }
    }
    $resumo['clientes'] = count($clientes);
    foreach ($resumo as $chave => $valor) $resumo[$chave] = round($valor, 2);
    ksort($evolucao);
    $clientes = array_values($clientes); usort($clientes, function ($a, $b) { return $b['valor'] <=> $a['valor']; });
    $atrasos = array_values($atrasos); usort($atrasos, function ($a, $b) { return $a['ordem'] <=> $b['ordem']; });
    return array('demonstracao' => true, 'tipo' => $tipo, 'resumo' => $resumo, 'evolucao' => array_values($evolucao),
        'situacoes' => array_values($situacoes), 'clientes' => array_slice($clientes, 0, 5), 'atrasos' => $atrasos, 'atualizado' => date('c'));
}

function portalRepresentanteDemonstracaoResposta($acao, $tipo, $filtros, $nota = 0, $hoje = null)
{
    $hoje = $hoje === null ? date('Y-m-d') : $hoje;
    $dados = portalRepresentanteDemonstracaoDados($hoje);
    if ($acao === 'cobrancas') {
        $notas = array_column($dados['notas'], 'id');
        $linhas = in_array($nota, $notas, true) ? array_values(array_filter($dados['boletos'], function ($linha) use ($nota) { return $linha['notaId'] === $nota; })) : array();
    } else {
        if (!isset($dados[$tipo])) throw new RepresentantePortalErro('Visão inválida.');
        $linhas = portalRepresentanteDemonstracaoFiltrar($dados[$tipo], $tipo, $filtros);
        if ($acao === 'resumo') return portalRepresentanteDemonstracaoResumo($tipo, $linhas, $hoje);
    }
    if ($acao === 'lista' && $tipo === 'orcamentos' && !empty($filtros['segmento'])) $linhas = array_values(array_filter($linhas, function ($linha) use ($filtros) {
        return empty($linha['excluido']) && isset($linha['segmento']) && $linha['segmento'] === $filtros['segmento'];
    }));
    $composicao = $acao === 'lista' && $tipo === 'boletos' && !empty($filtros['composicao']) ? $filtros['composicao'] : '';
    if ($composicao !== '') $linhas = array_values(array_filter($linhas, function ($linha) use ($composicao, $hoje) {
        if (!empty($linha['excluido'])) return false;
        if ($composicao === 'recebido') return $linha['recebido'] > 0;
        if ($composicao === 'saldo') return $linha['saldo'] > 0;
        if ($composicao === 'vencido') return $linha['status'] === 'vencido' && $linha['saldo'] > 0;
        if ($composicao === 'proximos7') return $linha['saldo'] > 0 && $linha['vencimento'] >= $hoje && $linha['vencimento'] <= (new \DateTimeImmutable($hoje))->modify('+7 days')->format('Y-m-d');
        return true;
    }));
    $campoComposicao = in_array($composicao, array('saldo','vencido','proximos7'), true) ? 'saldo' : ($composicao === 'recebido' ? 'recebido' : 'valor');
    usort($linhas, function ($a, $b) use ($tipo, $composicao, $campoComposicao) { if ($composicao !== '' && $a[$campoComposicao] != $b[$campoComposicao]) return $b[$campoComposicao] <=> $a[$campoComposicao]; $chave = $tipo === 'boletos' || $tipo === 'cobrancas' ? 'vencimento' : 'data'; return strcmp($b[$chave], $a[$chave]) ?: ($b['id'] <=> $a['id']); });
    $inicio = $acao === 'cobrancas' ? 0 : ($filtros['pagina'] - 1) * 50;
    return array('demonstracao' => true, 'linhas' => array_slice($linhas, $inicio, $acao === 'cobrancas' ? 100 : 50), 'mais' => $acao !== 'cobrancas' && count($linhas) > $inicio + 50);
}
