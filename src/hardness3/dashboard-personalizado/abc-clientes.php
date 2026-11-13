<?php
namespace hardness;

// Opt-in: the generic SQL executor and all other indicators keep their contract.
function dashboardAbcClientesHabilitado($indicador)
{
    return isset($indicador['abcClientes']['habilitado']) && $indicador['abcClientes']['habilitado'] === true;
}

function dashboardAbcClientesLotesHabilitado($indicador)
{
    return dashboardAbcClientesHabilitado($indicador)
        && isset($indicador['abcClientes']['leituraEmLotes']) && $indicador['abcClientes']['leituraEmLotes'] === true;
}

// Exact decimal arithmetic without requiring BCMath. Floats are used only when
// formatting percentages; ordering, sums and the 80/95 cutoffs remain exact.
function dashboardAbcClientesDecimal($valor)
{
    if ($valor === null) return '0';
    if (!is_scalar($valor) || !preg_match('/^([+-]?)([0-9]+)(?:\.([0-9]*))?(?:[eE]([+-]?[0-9]+))?$/D', (string) $valor, $m)) {
        throw new \RuntimeException('ABC de clientes: valor monetário inválido na consulta de vendas.');
    }
    $fracao = isset($m[3]) ? $m[3] : '';
    $expoente = isset($m[4]) ? (int) $m[4] : 0;
    if (strlen($m[2] . $fracao) > 100 || abs($expoente) > 100) throw new \RuntimeException('ABC de clientes: valor monetário fora do limite.');
    $digitos = $m[2] . $fracao;
    $ponto = strlen($m[2]) + $expoente;
    if ($ponto <= 0) $numero = '0.' . str_repeat('0', -$ponto) . $digitos;
    elseif ($ponto >= strlen($digitos)) $numero = $digitos . str_repeat('0', $ponto - strlen($digitos));
    else $numero = substr($digitos, 0, $ponto) . '.' . substr($digitos, $ponto);
    $partes = explode('.', $numero, 2);
    $inteiro = ltrim($partes[0], '0');
    $fracao = isset($partes[1]) ? rtrim($partes[1], '0') : '';
    $numero = ($inteiro === '' ? '0' : $inteiro) . ($fracao === '' ? '' : '.' . $fracao);
    return $numero !== '0' && $m[1] === '-' ? '-' . $numero : $numero;
}

function dashboardAbcClientesComparar($a, $b)
{
    if ($a === $b) return 0;
    $negA = $a[0] === '-'; $negB = $b[0] === '-';
    if ($negA !== $negB) return $negA ? -1 : 1;
    $a = ltrim($a, '-'); $b = ltrim($b, '-');
    $ordem = strlen($a) === strlen($b) ? strcmp($a, $b) : (strlen($a) < strlen($b) ? -1 : 1);
    return $negA ? -$ordem : $ordem;
}

function dashboardAbcClientesSomar($a, $b)
{
    if ($a === '0') return $b;
    if ($b === '0') return $a;
    if (PHP_INT_SIZE >= 8 && strlen(ltrim($a, '-')) <= 17 && strlen(ltrim($b, '-')) <= 17) return (string) ((int) $a + (int) $b);
    $negA = $a[0] === '-'; $negB = $b[0] === '-';
    $a = ltrim($a, '-'); $b = ltrim($b, '-');
    $subtrair = $negA !== $negB;
    $negativo = $negA;
    if ($subtrair && dashboardAbcClientesComparar($a, $b) < 0) {
        $temp = $a; $a = $b; $b = $temp; $negativo = $negB;
    }
    $saida = ''; $vai = 0; $j = strlen($b) - 1;
    for ($i = strlen($a) - 1; $i >= 0 || (!$subtrair && $j >= 0); $i--, $j--) {
        $x = $i >= 0 ? (int) $a[$i] : 0; $y = $j >= 0 ? (int) $b[$j] : 0;
        $n = $subtrair ? $x - $y - $vai : $x + $y + $vai;
        if ($subtrair) { $vai = $n < 0 ? 1 : 0; if ($n < 0) $n += 10; }
        else { $vai = intdiv($n, 10); $n %= 10; }
        $saida = (string) $n . $saida;
    }
    if (!$subtrair && $vai) $saida = (string) $vai . $saida;
    $saida = ltrim($saida, '0');
    return $saida === '' ? '0' : ($negativo ? '-' : '') . $saida;
}

function dashboardAbcClientesMultiplicar($a, $fator)
{
    if ($a === '0') return '0';
    $saida = ''; $vai = 0;
    for ($i = strlen($a) - 1; $i >= 0; $i--) {
        $n = (int) $a[$i] * $fator + $vai;
        $saida = (string) ($n % 10) . $saida; $vai = intdiv($n, 10);
    }
    return ($vai ? (string) $vai : '') . $saida;
}

function dashboardAbcClientesEscalar($numero, $escala)
{
    $posicao = strpos($numero, '.');
    $casas = $posicao === false ? 0 : strlen($numero) - $posicao - 1;
    $negativo = $numero[0] === '-';
    $digitos = ltrim(str_replace('.', '', ltrim($numero, '-')) . str_repeat('0', $escala - $casas), '0');
    return $digitos === '' ? '0' : ($negativo ? '-' : '') . $digitos;
}

function dashboardAbcClientesDesescalar($numero, $escala)
{
    $negativo = $numero[0] === '-'; $digitos = ltrim($numero, '-');
    if ($escala) {
        $digitos = str_pad($digitos, $escala + 1, '0', STR_PAD_LEFT);
        $digitos = substr($digitos, 0, -$escala) . '.' . substr($digitos, -$escala);
    }
    return ($negativo ? '-' : '') . $digitos;
}

function dashboardAbcClientesVariacao($atual, $anterior)
{
    if ($anterior === '0') return 'Sem base anterior';
    $oposto = $anterior[0] === '-' ? substr($anterior, 1) : '-' . $anterior;
    $diferenca = dashboardAbcClientesSomar($atual, $oposto);
    return number_format((float) $diferenca / abs((float) $anterior) * 100, 2, ',', '.') . '%';
}

function dashboardAbcClientesPeriodo($semestre, $hoje)
{
    $data = \DateTime::createFromFormat('!Y-m-d', $hoje);
    if (!$data || $data->format('Y-m-d') !== $hoje || !in_array($semestre, array(1, 2), true)) {
        throw new \RuntimeException('ABC de clientes: período inválido.');
    }
    $ano = (int) substr($hoje, 0, 4);
    $inicio = $ano . ($semestre === 1 ? '-01-01' : '-07-01');
    $fim = $hoje < $inicio ? null : min($hoje, $ano . ($semestre === 1 ? '-06-30' : '-12-31'));
    $anoAnterior = function ($dia) {
        if ($dia === null) return null;
        $mes = ((int) substr($dia, 0, 4) - 1) . substr($dia, 4, 3);
        $limite = (int) (new \DateTime($mes . '-01'))->format('t');
        return $mes . '-' . str_pad(min((int) substr($dia, 8, 2), $limite), 2, '0', STR_PAD_LEFT);
    };
    return array('anoAtual' => $ano, 'anoAnterior' => $ano - 1, 'inicioAtual' => $inicio,
        'fimAtual' => $fim, 'inicioAnterior' => $anoAnterior($inicio), 'fimAnterior' => $anoAnterior($fim));
}

function dashboardAbcClientesClasse($valor, $antes, $limiteA, $limiteB)
{
    if ($valor === '0' || $valor[0] === '-') return 'Sem venda';
    $antes = dashboardAbcClientesMultiplicar($antes, 100);
    return dashboardAbcClientesComparar($antes, $limiteA) < 0 ? 'A'
        : (dashboardAbcClientesComparar($antes, $limiteB) < 0 ? 'B' : 'C');
}

function dashboardAbcClientesConsolidar($cadastros, $vendas, $classeFiltro, $semestre)
{
    if (count($cadastros) > 100000 || count($vendas) > 100001) throw new \RuntimeException('ABC de clientes: a carteira excede o limite de 100 mil clientes.');
    $clientes = array();
    foreach ($cadastros as $linha) {
        foreach (array('clienteId', 'descricao', 'uf', 'cidade', 'filtroCliente', 'filtroUf') as $campo) {
            if (!isset($linha[$campo]) || !is_scalar($linha[$campo])) throw new \RuntimeException('ABC de clientes: consulta de cadastros incompleta.');
        }
        $id = (string) $linha['clienteId'];
        if (!preg_match('/^-?[0-9]+$/D', $id) || isset($clientes[$id])) throw new \RuntimeException('ABC de clientes: código de cliente inválido ou duplicado.');
        if (!in_array((string) $linha['filtroCliente'], array('0', '1'), true) || !in_array((string) $linha['filtroUf'], array('0', '1'), true)) {
            throw new \RuntimeException('ABC de clientes: resultado dos filtros inválido.');
        }
        $clientes[$id] = $linha;
    }
    unset($cadastros);
    $movimentos = array(); $escala = 0; $hoje = null;
    foreach ($vendas as $linha) {
        if (!isset($linha['dataReferencia']) || !is_string($linha['dataReferencia']) || ($hoje !== null && $hoje !== $linha['dataReferencia'])) {
            throw new \RuntimeException('ABC de clientes: data do servidor ausente ou inconsistente.');
        }
        $hoje = $linha['dataReferencia'];
        if (!isset($linha['clienteId'])) continue; // The outer company row also supplies the date when no sales exist.
        $id = (string) $linha['clienteId'];
        if (!isset($clientes[$id])) continue;
        if (isset($movimentos[$id])) throw new \RuntimeException('ABC de clientes: a consulta deve somar uma linha por cliente.');
        foreach (array('valor', 'valorAnterior', 'documentos', 'documentosAnterior') as $campo) {
            if (!array_key_exists($campo, $linha)) throw new \RuntimeException('ABC de clientes: consulta de vendas incompleta.');
        }
        $atual = dashboardAbcClientesDecimal($linha['valor']);
        $anterior = dashboardAbcClientesDecimal($linha['valorAnterior']);
        foreach (array($atual, $anterior) as $numero) {
            $ponto = strpos($numero, '.');
            if ($ponto !== false) $escala = max($escala, strlen($numero) - $ponto - 1);
        }
        foreach (array('documentos', 'documentosAnterior') as $campo) {
            if (!is_scalar($linha[$campo]) || !preg_match('/^[0-9]+$/D', (string) $linha[$campo])) throw new \RuntimeException('ABC de clientes: quantidade de documentos inválida.');
        }
        $movimentos[$id] = array($atual, $anterior, (int) $linha['documentos'], (int) $linha['documentosAnterior']);
    }
    unset($vendas);
    if ($hoje === null) throw new \RuntimeException('ABC de clientes: a consulta de vendas deve retornar a data do servidor, mesmo sem movimento.');
    $periodo = dashboardAbcClientesPeriodo($semestre, $hoje);
    $totalAtual = '0'; $totalAnterior = '0'; $positivosAnteriores = array();
    foreach ($movimentos as $id => &$movimento) {
        $movimento[0] = dashboardAbcClientesEscalar($movimento[0], $escala);
        $movimento[1] = dashboardAbcClientesEscalar($movimento[1], $escala);
        if ($movimento[0] !== '0' && $movimento[0][0] !== '-') $totalAtual = dashboardAbcClientesSomar($totalAtual, $movimento[0]);
        if ($movimento[1] !== '0' && $movimento[1][0] !== '-') {
            $totalAnterior = dashboardAbcClientesSomar($totalAnterior, $movimento[1]);
            $positivosAnteriores[] = $id;
        }
    }
    unset($movimento);
    $ordenar = function (&$ids, $campo) use (&$movimentos) {
        usort($ids, function ($a, $b) use (&$movimentos, $campo) {
            $va = isset($movimentos[$a]) ? $movimentos[$a][$campo] : '0';
            $vb = isset($movimentos[$b]) ? $movimentos[$b][$campo] : '0';
            $ordem = dashboardAbcClientesComparar($vb, $va);
            return $ordem !== 0 ? $ordem : strcmp((string) $a, (string) $b);
        });
    };
    $limiteAAnterior = dashboardAbcClientesMultiplicar($totalAnterior, 80);
    $limiteBAnterior = dashboardAbcClientesMultiplicar($totalAnterior, 95);
    $ordenar($positivosAnteriores, 1); $antes = '0'; $classesAnteriores = array();
    foreach ($positivosAnteriores as $id) {
        $valor = $movimentos[$id][1];
        $classesAnteriores[$id] = dashboardAbcClientesClasse($valor, $antes, $limiteAAnterior, $limiteBAnterior);
        $antes = dashboardAbcClientesSomar($antes, $valor);
    }
    unset($positivosAnteriores);
    $ids = array_keys($clientes); $ordenar($ids, 0);
    $limiteA = dashboardAbcClientesMultiplicar($totalAtual, 80); $limiteB = dashboardAbcClientesMultiplicar($totalAtual, 95);
    $grupos = array();
    foreach (array('A', 'B', 'C', 'Sem venda') as $classe) $grupos[$classe] = array('clientes' => 0, 'atual' => '0', 'anterior' => '0');
    $lista = array(); $quantidade = 0; $somaAtual = '0'; $somaAnterior = '0'; $antes = '0'; $posicao = 0;
    foreach ($ids as $id) {
        $posicao++;
        $m = isset($movimentos[$id]) ? $movimentos[$id] : array('0', '0', 0, 0);
        $classe = dashboardAbcClientesClasse($m[0], $antes, $limiteA, $limiteB);
        if ($m[0] !== '0' && $m[0][0] !== '-') $antes = dashboardAbcClientesSomar($antes, $m[0]);
        $cliente = $clientes[$id];
        if (!(int) $cliente['filtroCliente'] || !(int) $cliente['filtroUf'] || ($classeFiltro !== '' && $classe !== $classeFiltro)) continue;
        $quantidade++;
        $somaAtual = dashboardAbcClientesSomar($somaAtual, $m[0]); $somaAnterior = dashboardAbcClientesSomar($somaAnterior, $m[1]);
        $grupos[$classe]['clientes']++;
        $grupos[$classe]['atual'] = dashboardAbcClientesSomar($grupos[$classe]['atual'], $m[0]);
        $grupos[$classe]['anterior'] = dashboardAbcClientesSomar($grupos[$classe]['anterior'], $m[1]);
        if (count($lista) >= 500) continue;
        $oposto = $m[1] === '0' ? '0' : ($m[1][0] === '-' ? substr($m[1], 1) : '-' . $m[1]);
        $positivo = $m[0] !== '0' && $m[0][0] !== '-';
        $lista[] = array('id' => 'cad:' . $id, 'codigo' => (string) $id, 'descricao' => $cliente['descricao'],
            'uf' => $cliente['uf'], 'cidade' => $cliente['cidade'],
            'valor' => dashboardAbcClientesDesescalar($m[0], $escala), 'valorAnterior' => dashboardAbcClientesDesescalar($m[1], $escala),
            'documentos' => $m[2], 'documentosAnterior' => $m[3], 'posicao' => $posicao,
            'participacao' => $positivo && $totalAtual !== '0' ? (float) $m[0] / (float) $totalAtual * 100 : 0,
            'percentualAcumulado' => $totalAtual === '0' ? 0 : ($positivo ? (float) $antes / (float) $totalAtual * 100 : 100),
            'classificacao' => $classe, 'classificacaoAnterior' => isset($classesAnteriores[$id]) ? $classesAnteriores[$id] : 'Sem venda',
            'diferenca' => dashboardAbcClientesDesescalar(dashboardAbcClientesSomar($m[0], $oposto), $escala),
            'variacao' => dashboardAbcClientesVariacao($m[0], $m[1]), 'clienteComVenda' => $positivo ? 1 : 0, 'clienteExibido' => 1);
    }
    $categorias = array(); $composicao = array();
    foreach ($grupos as $classe => $grupo) {
        $composicao[$classe] = $grupo['clientes'];
        if (!$grupo['clientes']) continue;
        $oposto = $grupo['anterior'] === '0' ? '0' : ($grupo['anterior'][0] === '-' ? substr($grupo['anterior'], 1) : '-' . $grupo['anterior']);
        $categorias[] = array('categoria' => $classe, 'clientes' => $grupo['clientes'],
            'atual' => $classe === 'Sem venda' && $grupo['atual'] === '0' ? null : dashboardAbcClientesDesescalar($grupo['atual'], $escala),
            'anterior' => dashboardAbcClientesDesescalar($grupo['anterior'], $escala),
            'diferenca' => dashboardAbcClientesDesescalar(dashboardAbcClientesSomar($grupo['atual'], $oposto), $escala),
            'variacao' => dashboardAbcClientesVariacao($grupo['atual'], $grupo['anterior']));
    }
    $oposto = $somaAnterior === '0' ? '0' : ($somaAnterior[0] === '-' ? substr($somaAnterior, 1) : '-' . $somaAnterior);
    $resumo = array('clientes' => $quantidade, 'atual' => dashboardAbcClientesDesescalar($somaAtual, $escala),
        'anterior' => dashboardAbcClientesDesescalar($somaAnterior, $escala),
        'diferenca' => dashboardAbcClientesDesescalar(dashboardAbcClientesSomar($somaAtual, $oposto), $escala),
        'variacao' => $periodo['fimAtual'] === null ? 'Ainda não iniciado' : dashboardAbcClientesVariacao($somaAtual, $somaAnterior));
    return array('listagem' => $lista, 'quantidade' => $quantidade, 'valor' => $resumo['atual'],
        'composicao' => $composicao, 'categorias' => $categorias, 'resumo' => $resumo, 'periodo' => $periodo);
}

function dashboardAbcClientesConsultar($conexao, $sql, $parametros, $etapa, &$erro)
{
    if (!dashboardAbcClientesValidarFonte($sql, $parametros, $erro)) return false;
    $inicio = microtime(true);
    $linhas = !empty($GLOBALS['dashboardCarga']['abcClientes'])
        ? dashboardIndicadorConsultarDireto($conexao, $sql, $parametros, $erro)
        : dashboardIndicadorConsultar($conexao, $sql, $parametros, $erro);
    if ($linhas === false) {
        $numero = isset($GLOBALS['dashboardConsultaErroNumero']) ? (int) $GLOBALS['dashboardConsultaErroNumero'] : 0;
        error_log('Dashboard personalizado ABC: etapa=' . $etapa . ' mysql=' . $numero
            . ' segundos=' . number_format(microtime(true) - $inicio, 3, '.', '') . ' consulta=' . substr(hash('sha256', $sql), 0, 16));
        $erro = 'ABC de clientes: falha na consulta de ' . $etapa . ($numero ? ' (MySQL ' . $numero . ')' : '') . '.';
    }
    return $linhas;
}

function dashboardAbcClientesValidarFonte($sql, $parametros, &$erro)
{
    if (!dashboardIndicadorValidarSql($sql, $erro)) return false;
    preg_match_all('/:([a-z][a-zA-Z0-9_]*)/', $sql, $nomes);
    foreach (array_unique($nomes[1]) as $nome) {
        if (!array_key_exists($nome, $parametros)) { $erro = 'ABC de clientes: parâmetro não configurado: ' . $nome . '.'; return false; }
    }
    return true;
}

function dashboardAbcClientesLotesInicializar($auth, $indicador, $entrada, $tenant, $leitura)
{
    $token = isset($entrada['cargaToken']) ? $entrada['cargaToken'] : '';
    if (!is_string($token) || ($leitura && $token === '')) throw new \RuntimeException('Atualize o indicador antes de consultar suas páginas ou exportar.');
    $filtros = dashboardIndicadorFiltros($indicador, $entrada, $erros);
    if ($erros) throw new \RuntimeException(implode(' ', $erros));
    // Endpoints revalidate authorization/configuration on every continuation.
    // Offset and output format do not change this completed visualization.
    $assinatura = hash('sha256', serialize(array('abc-notas-v1', $tenant, $auth, $indicador, $filtros['valores'], date('Y-m-d'))));
    $dono = hash('sha256', serialize(array($tenant, $auth['usuario'], $auth['empresa'], $indicador['id'])));
    dashboardCargaAbrir($token, $assinatura, $leitura, $dono, false, true);
}

function dashboardAbcClientesArquivoSalvar($nome, $dados)
{
    $ctx = &$GLOBALS['dashboardCarga'];
    $json = json_encode($dados, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($json === false || strlen($json) > 33554432) throw new \RuntimeException('ABC de clientes: dados excederam o limite do carregamento.');
    $arquivo = $ctx['dir'] . '/' . $nome . '.json';
    $temp = @tempnam($ctx['dir'], 'abc-');
    if ($temp === false) throw new \RuntimeException('ABC de clientes: não foi possível preparar os dados.');
    try {
        if (!@chmod($temp, 0600) || @file_put_contents($temp, $json, LOCK_EX) !== strlen($json)
            || is_link($arquivo) || !@rename($temp, $arquivo)) throw new \RuntimeException('ABC de clientes: não foi possível salvar os dados.');
    } finally { if (is_file($temp)) @unlink($temp); }
    $ctx['estado']['abc']['arquivos'][$nome] = hash('sha256', $json);
}

function dashboardAbcClientesArquivoLer($nome)
{
    $ctx = &$GLOBALS['dashboardCarga'];
    $arquivo = $ctx['dir'] . '/' . $nome . '.json';
    if (!isset($ctx['estado']['abc']['arquivos'][$nome]) || is_link($arquivo) || !is_file($arquivo) || filesize($arquivo) > 33554432) {
        throw new \RuntimeException('ABC de clientes: dados indisponíveis. Atualize o indicador.');
    }
    $json = @file_get_contents($arquivo);
    if ($json === false || !hash_equals($ctx['estado']['abc']['arquivos'][$nome], hash('sha256', $json))) {
        throw new \RuntimeException('ABC de clientes: dados inválidos. Atualize o indicador.');
    }
    $dados = json_decode($json, true);
    if (!is_array($dados)) throw new \RuntimeException('ABC de clientes: dados incompletos. Atualize o indicador.');
    return $dados;
}

function dashboardAbcClientesSomarDecimal($a, $b)
{
    $a = dashboardAbcClientesDecimal($a); $b = dashboardAbcClientesDecimal($b);
    $casas = function ($n) { $p = strpos($n, '.'); return $p === false ? 0 : strlen($n) - $p - 1; };
    $escala = max($casas($a), $casas($b));
    return dashboardAbcClientesDecimal(dashboardAbcClientesDesescalar(
        dashboardAbcClientesSomar(dashboardAbcClientesEscalar($a, $escala), dashboardAbcClientesEscalar($b, $escala)), $escala));
}

function dashboardAbcClientesLotesExecutar($conexao, $indicador, $parametros, &$erro)
{
    if (empty($GLOBALS['dashboardCarga']['abcClientes'])) {
        $erro = 'Atualize os arquivos do motor e reabra o indicador para carregar as vendas em lotes.';
        return false;
    }
    $ctx = &$GLOBALS['dashboardCarga']; $config = $indicador['abcClientes'];
    $sqlContexto = dashboardPersonalizadoSqlTexto(isset($config['sqlContexto']) ? $config['sqlContexto'] : '');
    $sqlClientes = dashboardPersonalizadoSqlTexto(isset($config['sqlClientes']) ? $config['sqlClientes'] : '');
    $sqlVendas = dashboardPersonalizadoSqlTexto(isset($indicador['sqlBase']) ? $indicador['sqlBase'] : '');
    $paramsLote = $parametros + array('loteInicio' => '', 'loteFim' => '', 'ultimoId' => '0', 'primeiroLote' => 1);
    foreach (array($sqlContexto, $sqlClientes, $sqlVendas) as $sql) {
        if (!dashboardAbcClientesValidarFonte($sql, $paramsLote, $erro)) return false;
        if (preg_match_all('/\bSELECT\b/i', $sql) !== 1 || preg_match('/\b(?:GROUP\s+BY|OFFSET|UNION|SUM|COUNT)\b/i', $sql)) {
            $erro = 'ABC de clientes em lotes exige consultas simples, sem agregações nem subconsultas.'; return false;
        }
    }
    foreach (array(':loteInicio', ':loteFim', ':ultimoId', ':primeiroLote') as $nome) {
        if (strpos($sqlVendas, $nome) === false) { $erro = 'ABC de clientes: falta o parâmetro de lote ' . $nome . '.'; return false; }
    }
    if (!preg_match('/\bLIMIT\s+2000\s*$/i', $sqlVendas)) { $erro = 'ABC de clientes: configure vendas em lotes de 2.000 notas.'; return false; }
    if (!isset($ctx['estado']['abc'])) $ctx['estado']['abc'] = array('etapa' => 'contexto', 'arquivos' => array(), 'janela' => 0, 'ultimoId' => null, 'documentos' => 0);
    $estado = &$ctx['estado']['abc'];
    if ($estado['etapa'] === 'concluida') return dashboardAbcClientesArquivoLer('resultado-abc');
    if ($ctx['leitura'] || $ctx['estado']['concluida']) throw new \RuntimeException('Atualize o indicador para concluir seu carregamento antes de consultar os resultados.');
    if ($estado['etapa'] === 'contexto') {
        $linhas = dashboardAbcClientesConsultar($conexao, $sqlContexto, $parametros, 'contexto', $erro);
        if ($linhas === false) return false;
        if (count($linhas) !== 1 || empty($linhas[0]['dataReferencia'])) throw new \RuntimeException('ABC de clientes: empresa ou data de referência indisponível.');
        $estado['dataReferencia'] = $linhas[0]['dataReferencia'];
        $periodo = dashboardAbcClientesPeriodo($config['semestre'], $estado['dataReferencia']);
        $estado['janelas'] = array();
        if ($periodo['fimAtual'] !== null) {
            foreach (array('Atual', 'Anterior') as $ano) {
                foreach (dashboardCargaJanelas($periodo['inicio' . $ano], $periodo['fim' . $ano]) as $janela) {
                    $estado['janelas'][] = array('inicio' => $janela[0], 'fim' => (new \DateTime($janela[1]))->modify('+1 day')->format('Y-m-d'), 'anterior' => $ano === 'Anterior');
                }
            }
        }
        $estado['etapa'] = 'cadastros';
    } elseif ($estado['etapa'] === 'cadastros') {
        $linhas = dashboardAbcClientesConsultar($conexao, $sqlClientes, $parametros, 'cadastros', $erro);
        if ($linhas === false) return false;
        if (count($linhas) > 100000) throw new \RuntimeException('ABC de clientes: a carteira excede o limite de 100 mil clientes.');
        dashboardAbcClientesArquivoSalvar('cadastros-abc', $linhas);
        dashboardAbcClientesArquivoSalvar('vendas-abc', array());
        $estado['etapa'] = 'vendas';
    } elseif ($estado['janela'] < count($estado['janelas'])) {
        $janela = $estado['janelas'][$estado['janela']];
        $paramsLote['loteInicio'] = $janela['inicio']; $paramsLote['loteFim'] = $janela['fim'];
        $paramsLote['primeiroLote'] = $estado['ultimoId'] === null ? 1 : 0;
        $paramsLote['ultimoId'] = $estado['ultimoId'] === null ? '0' : $estado['ultimoId'];
        $linhas = dashboardAbcClientesConsultar($conexao, $sqlVendas, $paramsLote, 'vendas-lote', $erro);
        if ($linhas === false) return false;
        if (count($linhas) > 2000 || $estado['documentos'] + count($linhas) > 2000000) throw new \RuntimeException('ABC de clientes: quantidade de notas excedeu o limite do carregamento.');
        $vendas = dashboardAbcClientesArquivoLer('vendas-abc');
        $ultimoId = $estado['ultimoId'];
        foreach ($linhas as $linha) {
            foreach (array('id', 'clienteId', 'valor') as $campo) {
                if (!isset($linha[$campo]) || !is_scalar($linha[$campo])) throw new \RuntimeException('ABC de clientes: nota incompleta no lote.');
            }
            $id = (string) $linha['id']; $cliente = (string) $linha['clienteId'];
            if (!preg_match('/^-?(?:0|[1-9][0-9]*)$/D', $id) || !preg_match('/^-?(?:0|[1-9][0-9]*)$/D', $cliente)
                || ($ultimoId !== null && dashboardAbcClientesComparar($id, $ultimoId) <= 0)) throw new \RuntimeException('ABC de clientes: notas fora de ordem ou duplicadas.');
            $ultimoId = $id; $chave = 'c:' . $cliente;
            if (!isset($vendas[$chave])) $vendas[$chave] = array('clienteId' => $cliente, 'valor' => '0', 'valorAnterior' => '0',
                'documentos' => 0, 'documentosAnterior' => 0, 'dataReferencia' => $estado['dataReferencia']);
            $campoValor = $janela['anterior'] ? 'valorAnterior' : 'valor';
            $campoDocumentos = $janela['anterior'] ? 'documentosAnterior' : 'documentos';
            $vendas[$chave][$campoValor] = dashboardAbcClientesSomarDecimal($vendas[$chave][$campoValor], $linha['valor']);
            $vendas[$chave][$campoDocumentos]++;
        }
        if (count($vendas) > 100000) throw new \RuntimeException('ABC de clientes: a carteira excede o limite de 100 mil clientes.');
        dashboardAbcClientesArquivoSalvar('vendas-abc', $vendas);
        $estado['documentos'] += count($linhas); $estado['ultimoId'] = $ultimoId;
        if (count($linhas) < 2000) { $estado['janela']++; $estado['ultimoId'] = null; }
    } else {
        $vendas = array_values(dashboardAbcClientesArquivoLer('vendas-abc'));
        if (!$vendas) $vendas[] = array('clienteId' => null, 'dataReferencia' => $estado['dataReferencia']);
        $dados = dashboardAbcClientesConsolidar(dashboardAbcClientesArquivoLer('cadastros-abc'), $vendas, $parametros['classe'], $config['semestre']);
        dashboardAbcClientesArquivoSalvar('resultado-abc', $dados);
        $estado['etapa'] = 'concluida';
        return $dados;
    }
    $ctx['progresso'] = array('token' => $ctx['token'], 'intervalo' => min($estado['janela'] + 1, max(1, count($estado['janelas']))),
        'intervalos' => max(1, count($estado['janelas'])), 'registros' => $estado['documentos']);
    throw new DashboardCargaPendente();
}

function dashboardAbcClientesExecutar($conexao, $indicador, $empresa, $entrada, &$erro)
{
    $erro = ''; $config = $indicador['abcClientes'];
    $semestre = isset($config['semestre']) ? $config['semestre'] : 0;
    if (!in_array($semestre, array(1, 2), true)) { $erro = 'ABC de clientes: configure semestre 1 ou 2.'; return false; }
    foreach (array('detalheBloco', 'detalheGrupo', 'detalheCalendarioId', 'detalheGraficoSerie', 'somenteDetalhamento') as $chave) {
        if (!empty($entrada[$chave])) { $erro = 'Este ABC não possui detalhamento adicional.'; return false; }
    }
    $offset = isset($entrada['listaOffset']) ? $entrada['listaOffset'] : '0';
    if (!is_scalar($offset) || !preg_match('/^[0-9]{1,9}$/D', (string) $offset)) { $erro = 'Página da tabela inválida.'; return false; }
    $filtros = dashboardIndicadorFiltros($indicador, $entrada, $erros);
    if ($erros) { $erro = implode(' ', $erros); return false; }
    $valores = array_merge(array('cliente' => '', 'uf' => '', 'classe' => ''), $filtros['valores']);
    if (!in_array($valores['classe'], array('', 'A', 'B', 'C', 'Sem venda'), true)) { $erro = 'Categoria inválida.'; return false; }
    $parametros = array_merge($valores, array('empresa' => (int) $empresa));
    $sqlClientes = dashboardPersonalizadoSqlTexto(isset($config['sqlClientes']) ? $config['sqlClientes'] : '');
    $sqlVendas = dashboardPersonalizadoSqlTexto(isset($indicador['sqlBase']) ? $indicador['sqlBase'] : '');
    if (dashboardAbcClientesLotesHabilitado($indicador)) {
        $dados = dashboardAbcClientesLotesExecutar($conexao, $indicador, $parametros, $erro);
        if ($dados === false) return false;
    } else {
        // Validate both sources before executing either. No client data is truncated.
        if (!dashboardAbcClientesValidarFonte($sqlClientes, $parametros, $erro) || !dashboardAbcClientesValidarFonte($sqlVendas, $parametros, $erro)) return false;
        $cadastros = dashboardAbcClientesConsultar($conexao, $sqlClientes, $parametros, 'cadastros', $erro);
        if ($cadastros === false) return false;
        if (count($cadastros) > 100000) { $erro = 'ABC de clientes: a carteira excede o limite de 100 mil clientes.'; return false; }
        $vendas = dashboardAbcClientesConsultar($conexao, $sqlVendas, $parametros, 'vendas', $erro);
        if ($vendas === false) return false;
        try { $dados = dashboardAbcClientesConsolidar($cadastros, $vendas, $valores['classe'], $semestre); }
        catch (\RuntimeException $e) { $erro = $e->getMessage(); return false; }
        unset($cadastros, $vendas);
    }
    $completa = defined('HARDNESS_DASHBOARD_EXPORTAR_LISTAGEM_COMPLETA') && HARDNESS_DASHBOARD_EXPORTAR_LISTAGEM_COMPLETA;
    $offset = $completa ? 0 : (int) $offset;
    $tamanho = isset($indicador['listagem']['paginacao']['tamanhoPagina']) ? max(1, min(100, (int) $indicador['listagem']['paginacao']['tamanhoPagina'])) : 50;
    $total = count($dados['listagem']);
    $linhas = $completa ? $dados['listagem'] : array_slice($dados['listagem'], $offset, $tamanho);
    $pagina = array('total' => $total, 'offset' => $offset, 'tamanhoPagina' => $tamanho,
        'quantidade' => count($linhas), 'temMais' => $offset + count($linhas) < $total, 'totais' => array());
    $publico = dashboardIndicadorDefinicaoPublica($indicador, $filtros['publicos']);
    unset($publico['composicao']['sqlBase']);
    $resultado = array('indicador' => $publico, 'listagem' => $linhas, 'listagemPaginacao' => $pagina);
    if (isset($entrada['somenteListagem']) && $entrada['somenteListagem'] === '1') return $resultado;
    $composicao = array();
    foreach ((isset($indicador['composicao']['itens']) ? $indicador['composicao']['itens'] : array()) as $item) {
        $chave = $item['chave'];
        $composicao[] = array('chave' => $chave, 'rotulo' => $item['rotulo'],
            'valor' => isset($dados['composicao'][$chave]) ? $dados['composicao'][$chave] : 0);
    }
    $secoes = array();
    foreach ((isset($indicador['secoes']) ? $indicador['secoes'] : array()) as $secao) {
        if ($secao['id'] === 'categorias_clientes_abc') $linhasSecao = $dados['categorias'];
        elseif ($secao['id'] === 'resumo_filtros_abc') $linhasSecao = array($dados['resumo']);
        else { $erro = 'ABC de clientes: bloco não reconhecido.'; return false; }
        $secao['linhas'] = $linhasSecao;
        unset($secao['sqlBase']);
        $secao += array('grafico' => array(), 'conferencia' => array(), 'rodapeTotais' => array());
        $secoes[] = $secao;
    }
    return $resultado + array('resumos' => array(), 'secoes' => $secoes,
        'card' => array('valor' => (float) $dados['valor'], 'quantidade' => $dados['quantidade'], 'formato' => 'moeda'),
        'geografia' => array(), 'composicao' => $composicao, 'grafico' => array(), 'graficoDetalhes' => array(),
        'previsaoCaixa' => null, 'graficosPorGrupo' => array(), 'calendarios' => array(), 'calendarioDetalhes' => array(),
        'paretoClasse' => null, 'listagemDetalhes' => array(), 'comparativoRepresentantes' => array());
}
