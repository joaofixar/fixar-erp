<?php
namespace hardness;

require_once __DIR__ . '/clientes-inativos.php';

function dashboardIndicadorIdentificador($valor, $padrao = '')
{
    $valor = (string) $valor;
    return preg_match('/^[a-zA-Z_][a-zA-Z0-9_]{0,63}$/', $valor) ? $valor : $padrao;
}

function dashboardIndicadorValidarSql($sql, &$erro, $permitirMultiEmpresa = false, $permitirUnionAll = false)
{
    $erro = '';
    $sql = trim((string) $sql);
    if ($sql === '' || !preg_match('/^SELECT\b/i', $sql)) {
        $erro = 'A consulta deve começar com SELECT.';
        return false;
    }
    if (strpos($sql, ';') !== false || preg_match('/(--|#|\/\*)/', $sql)) {
        $erro = 'Não são permitidos múltiplos comandos ou comentários SQL.';
        return false;
    }

    $bloqueados = array(
        'INSERT', 'UPDATE', 'DELETE', 'REPLACE', 'DROP', 'ALTER', 'CREATE', 'TRUNCATE',
        'RENAME', 'GRANT', 'REVOKE', 'CALL', 'EXECUTE', 'PREPARE', 'HANDLER', 'LOAD',
        'OUTFILE', 'DUMPFILE', 'LOCK', 'UNLOCK', 'SLEEP', 'BENCHMARK',
        'GET_LOCK', 'RELEASE_LOCK', 'INFORMATION_SCHEMA', 'PERFORMANCE_SCHEMA', 'MYSQL', 'SYS'
    );
    if (preg_match('/\b(' . implode('|', $bloqueados) . ')\b/i', $sql, $encontrado)) {
        $erro = 'Elemento SQL não permitido: ' . strtoupper($encontrado[1]) . '.';
        return false;
    }
    if (preg_match('/\bUNION\b/i', $sql)) {
        if (!$permitirUnionAll || preg_match('/\bUNION\b(?!\s+ALL\s+SELECT\b)/i', $sql)) {
            $erro = 'Somente UNION ALL SELECT é permitido neste painel.';
            return false;
        }
    }
    if (preg_match('/\bSELECT\s+(?:DISTINCT\s+)?(?:[a-zA-Z0-9_]+\.)?\*/i', $sql)) {
        $erro = 'Informe explicitamente as colunas; SELECT * não é permitido.';
        return false;
    }
    if (preg_match('/\b[a-zA-Z0-9_]*(?:senha|password|secret|token)[a-zA-Z0-9_]*\b/i', $sql)) {
        $erro = 'A consulta referencia um campo sensível.';
        return false;
    }
    if (!$permitirMultiEmpresa && (strpos($sql, ':empresa') === false || !preg_match('/\b[a-zA-Z0-9_]*C004_Id\s*=\s*:empresa\b/i', $sql))) {
        $erro = 'A consulta deve restringir a empresa usando um campo C004_Id = :empresa.';
        return false;
    }
    if ($permitirMultiEmpresa && !preg_match('/\b[a-zA-Z0-9_]*C004_Id\b/i', $sql)) {
        $erro = 'A consulta multiempresa deve referenciar um campo C004_Id.';
        return false;
    }

    preg_match_all('/\b(?:FROM|JOIN)\s+`?([a-zA-Z][a-zA-Z0-9_]*)`?/i', $sql, $tabelas);
    foreach ($tabelas[1] as $tabela) {
        if (!preg_match('/^(?:T|D|C|R|N)[0-9]{3}[A-Z]?$/i', $tabela)) {
            $erro = 'Tabela não permitida no dashboard: ' . $tabela . '.';
            return false;
        }
    }

    return true;
}

function dashboardIndicadorValorPadrao($filtro)
{
    $padrao = isset($filtro['padrao']) ? $filtro['padrao'] : '';
    if ($padrao === 'hoje') {
        return date('Y-m-d');
    }
    if ($padrao === 'inicio_mes') {
        return date('Y-m-01');
    }
    if ($padrao === 'inicio_ano') {
        return date('Y-01-01');
    }
    return $padrao;
}

function dashboardIndicadorFiltros($indicador, $entrada, &$erros)
{
    $erros = array();
    $valores = array();
    $publicos = array();
    $filtros = isset($indicador['filtros']) && is_array($indicador['filtros']) ? $indicador['filtros'] : array();

    foreach ($filtros as $filtro) {
        $id = dashboardIndicadorIdentificador(isset($filtro['id']) ? $filtro['id'] : '');
        if ($id === '') {
            continue;
        }
        $tipo = isset($filtro['tipo']) ? $filtro['tipo'] : 'texto';
        $valor = isset($entrada['f_' . $id]) ? $entrada['f_' . $id] : dashboardIndicadorValorPadrao($filtro);
        $valor = is_array($valor) ? '' : trim((string) $valor);

        if ($tipo === 'data' && $valor !== '') {
            $partes = explode('-', $valor);
            if (count($partes) !== 3 || !checkdate((int) $partes[1], (int) $partes[2], (int) $partes[0])) {
                $erros[] = 'Data inválida no filtro ' . $id . '.';
                continue;
            }
        } elseif ($tipo === 'numero') {
            if ($valor !== '' && !is_numeric(str_replace(',', '.', $valor))) {
                $erros[] = 'Número inválido no filtro ' . $id . '.';
                continue;
            }
            $valor = $valor === '' ? 0 : (float) str_replace(',', '.', $valor);
        } elseif ($tipo === 'selecao') {
            $opcoes = isset($filtro['opcoes']) && is_array($filtro['opcoes']) ? $filtro['opcoes'] : array();
            $permitidos = array('');
            foreach ($opcoes as $opcao) {
                if (is_array($opcao) && isset($opcao['valor'])) {
                    $permitidos[] = (string) $opcao['valor'];
                }
            }
            if (!in_array((string) $valor, $permitidos, true)) {
                $erros[] = 'Opção inválida no filtro ' . $id . '.';
                continue;
            }
        } else {
            $valor = mb_substr($valor, 0, 200, 'UTF-8');
        }

        if (!empty($filtro['obrigatorio']) && $valor === '') {
            $erros[] = 'Preencha o filtro ' . $id . '.';
        }

        $valores[$id] = $valor;
        $filtroPublico = $filtro;
        $filtroPublico['valor'] = $valor;
        $publicos[] = $filtroPublico;
    }

    return array('valores' => $valores, 'publicos' => $publicos);
}

function dashboardIndicadorPrepararSql($sql, $parametros, &$valores, &$tipos, &$erro)
{
    $valores = array();
    $tipos = '';
    $erro = '';
    return preg_replace_callback('/:([a-z][a-zA-Z0-9_]*)/', function ($match) use ($parametros, &$valores, &$tipos, &$erro) {
        $nome = $match[1];
        if (!array_key_exists($nome, $parametros)) {
            $erro = 'Parâmetro não configurado: :' . $nome . '.';
            return '?';
        }
        $valor = $parametros[$nome];
        $valores[] = $valor;
        $tipos .= is_int($valor) ? 'i' : (is_float($valor) ? 'd' : 's');
        return '?';
    }, $sql);
}

function dashboardIndicadorBind($stmt, $tipos, &$valores)
{
    if ($tipos === '') {
        return true;
    }
    $argumentos = array($tipos);
    foreach ($valores as $indice => $valor) {
        $argumentos[] = &$valores[$indice];
    }
    return call_user_func_array(array($stmt, 'bind_param'), $argumentos);
}

function dashboardIndicadorConsultar($conexao, $sql, $parametros, &$erro)
{
    $valores = array();
    $tipos = '';
    $sqlPreparado = dashboardIndicadorPrepararSql($sql, $parametros, $valores, $tipos, $erro);
    if ($erro !== '') {
        return false;
    }

    $stmt = mysqli_prepare($conexao, $sqlPreparado);
    if (!$stmt || !dashboardIndicadorBind($stmt, $tipos, $valores) || !mysqli_stmt_execute($stmt)) {
        $erroBanco = $stmt ? mysqli_stmt_error($stmt) : mysqli_error($conexao);
        error_log('Dashboard personalizado: ' . $erroBanco);
        $erro = 'Não foi possível executar a consulta configurada.';
        if ($stmt) {
            mysqli_stmt_close($stmt);
        }
        return false;
    }

    $resultado = mysqli_stmt_get_result($stmt);
    $linhas = $resultado ? mysqli_fetch_all($resultado, MYSQLI_ASSOC) : array();
    mysqli_stmt_close($stmt);
    return $linhas;
}

function dashboardIndicadorCarregarOpcoesFiltros($conexao, $filtros, $empresa, &$erro)
{
    $erro = '';
    foreach ($filtros as &$filtro) {
        if (is_array($filtro) && isset($filtro['opcoesCadastro']) && $filtro['opcoesCadastro'] === 'representantesAtivos') {
            $linhas = dashboardIndicadorConsultar(
                $conexao,
                "SELECT D021_Id AS valor, D021_Nome_Empresa AS titulo FROM D021 WHERE D021_Ativo = 'S' AND NULLIF(D021_Nome_Empresa, '') IS NOT NULL ORDER BY D021_Nome_Empresa",
                array(),
                $erro
            );
            if ($linhas === false) {
                return false;
            }
            $filtro['opcoes'] = $linhas;
            unset($filtro['opcoesCadastro']);
            continue;
        }
        if (!is_array($filtro) || (isset($filtro['tipo']) ? $filtro['tipo'] : '') !== 'selecao' || empty($filtro['opcoesSql'])) {
            continue;
        }

        $sql = dashboardPersonalizadoSqlTexto($filtro['opcoesSql']);
        if (!dashboardIndicadorValidarSql($sql, $erro)) {
            return false;
        }
        preg_match_all('/:([a-z][a-zA-Z0-9_]*)/', $sql, $encontrados);
        foreach (array_unique($encontrados[1]) as $parametro) {
            if ($parametro !== 'empresa') {
                $erro = 'As opções dinâmicas do filtro só podem usar o parâmetro :empresa.';
                return false;
            }
        }

        $linhas = dashboardIndicadorConsultar($conexao, $sql, array('empresa' => (int) $empresa), $erro);
        if ($linhas === false) {
            return false;
        }
        $filtro['opcoes'] = array();
        foreach ($linhas as $linha) {
            if (!array_key_exists('valor', $linha) || !array_key_exists('titulo', $linha)) {
                $erro = 'As opções dinâmicas devem retornar as colunas valor e titulo.';
                return false;
            }
            $filtro['opcoes'][] = array('valor' => (string) $linha['valor'], 'titulo' => (string) $linha['titulo']);
        }
        unset($filtro['opcoesSql']);
    }
    unset($filtro);

    return $filtros;
}

/*
 * A configuração inicial precisa apenas da estrutura dos filtros. As opções
 * consultadas no banco são carregadas quando o indicador é efetivamente
 * aberto, evitando executar consultas de indicadores ainda não utilizados.
 */
function dashboardIndicadorFiltrosSemOpcoesDinamicas($filtros)
{
    $filtrosPublicos = array();
    foreach ((array) $filtros as $filtro) {
        if (!is_array($filtro)) {
            continue;
        }
        unset($filtro['opcoesSql'], $filtro['opcoesCadastro']);
        $filtrosPublicos[] = $filtro;
    }

    return $filtrosPublicos;
}

function dashboardIndicadorFormatoPermitido($formato)
{
    return in_array($formato, array('moeda', 'numero', 'percentual', 'texto', 'data'), true) ? $formato : 'numero';
}

function dashboardIndicadorNormalizarNome($nome)
{
    $nome = trim((string) $nome);
    if (function_exists('iconv')) {
        $convertido = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $nome);
        if ($convertido !== false) {
            $nome = $convertido;
        }
    }
    $nome = function_exists('mb_strtolower') ? mb_strtolower($nome, 'UTF-8') : strtolower($nome);
    return preg_replace('/[^a-z0-9]+/', '', $nome);
}

function dashboardIndicadorComparativoRepresentantes($conexao, $configuracao, $sqlBase, $parametros, &$erro)
{
    if (empty($configuracao) || !is_array($configuracao)) {
        return array();
    }

    if (isset($configuracao['modo']) && $configuracao['modo'] === 'mensal') {
        $anoBase = isset($configuracao['anoBase']) ? (int) $configuracao['anoBase'] : ((int) date('Y') - 1);
        $anoAtual = isset($configuracao['anoAtual']) ? (int) $configuracao['anoAtual'] : (int) date('Y');
        $sqlBaseAnoBase = dashboardPersonalizadoSqlTexto(isset($configuracao['sqlBaseAnoBase']) ? $configuracao['sqlBaseAnoBase'] : '');
        if (!dashboardIndicadorValidarSql($sqlBaseAnoBase, $erro)) {
            return false;
        }
        $meses = array();
        for ($numeroMes = 1; $numeroMes <= 12; $numeroMes++) {
            $meses[] = str_pad((string) $numeroMes, 2, '0', STR_PAD_LEFT);
        }

        $representantes = dashboardIndicadorConsultar(
            $conexao,
            "SELECT D021_Id AS representanteId, D021_Nome_Empresa AS representante FROM D021 WHERE D021_Ativo = 'S' AND NULLIF(D021_Nome_Empresa, '') IS NOT NULL ORDER BY D021_Nome_Empresa",
            array(),
            $erro
        );
        if ($representantes === false) {
            return false;
        }

        $representanteSelecionado = isset($parametros['representante']) ? trim((string) $parametros['representante']) : '';
        if ($representanteSelecionado !== '') {
            $representantes = array_values(array_filter($representantes, function ($representante) use ($representanteSelecionado) {
                return (string) $representante['representanteId'] === $representanteSelecionado;
            }));
        }

        $agruparValoresMensais = function ($sql) use ($conexao, $parametros, &$erro) {
            $resultado = dashboardIndicadorConsultar(
                $conexao,
                "SELECT representanteId, DATE_FORMAT(data, '%m') AS mes, COALESCE(SUM(valor), 0) AS valor FROM (" . $sql . ") AS dashboard_base GROUP BY representanteId, mes",
                $parametros,
                $erro
            );
            if ($resultado === false) {
                return false;
            }
            $porRepresentante = array();
            foreach ($resultado as $linha) {
                $id = (string) $linha['representanteId'];
                $mes = (string) $linha['mes'];
                if (!isset($porRepresentante[$id])) {
                    $porRepresentante[$id] = array();
                }
                $porRepresentante[$id][$mes] = (float) $linha['valor'];
            }
            return $porRepresentante;
        };

        $valoresPorRepresentante = $agruparValoresMensais($sqlBase);
        if ($valoresPorRepresentante === false) {
            return false;
        }
        $valoresBasePorRepresentante = $agruparValoresMensais($sqlBaseAnoBase);
        if ($valoresBasePorRepresentante === false) {
            return false;
        }

        $linhas = array();
        $totaisMeses = array_fill_keys($meses, 0);
        $totaisMesesBase = array_fill_keys($meses, 0);
        $totalAtual = 0;
        $totalBase = 0;
        foreach ($representantes as $representante) {
            $id = (string) $representante['representanteId'];
            $valores = array();
            $valoresBase = array();
            $variacoes = array();
            $totalRepresentante = 0;
            $totalRepresentanteBase = 0;
            foreach ($meses as $mes) {
                $valor = isset($valoresPorRepresentante[$id][$mes]) ? $valoresPorRepresentante[$id][$mes] : 0;
                $valorBase = isset($valoresBasePorRepresentante[$id][$mes]) ? $valoresBasePorRepresentante[$id][$mes] : 0;
                $valores[$mes] = $valor;
                $valoresBase[$mes] = $valorBase;
                $variacoes[$mes] = $valorBase > 0 ? (($valor - $valorBase) / $valorBase) * 100 : null;
                $totaisMeses[$mes] += $valor;
                $totaisMesesBase[$mes] += $valorBase;
                $totalRepresentante += $valor;
                $totalRepresentanteBase += $valorBase;
            }
            $linhas[] = array(
                'representante' => (string) $representante['representante'],
                'meses' => $valores,
                'mesesBase' => $valoresBase,
                'variacoes' => $variacoes,
                'total' => $totalRepresentante,
                'totalBase' => $totalRepresentanteBase,
                'variacaoTotal' => $totalRepresentanteBase > 0 ? (($totalRepresentante - $totalRepresentanteBase) / $totalRepresentanteBase) * 100 : null,
            );
            $totalAtual += $totalRepresentante;
            $totalBase += $totalRepresentanteBase;
        }

        $variacoesTotaisMeses = array();
        foreach ($meses as $mes) $variacoesTotaisMeses[$mes] = $totaisMesesBase[$mes] > 0 ? (($totaisMeses[$mes] - $totaisMesesBase[$mes]) / $totaisMesesBase[$mes]) * 100 : null;

        return array(
            'modo' => 'mensal',
            'anoBase' => $anoBase,
            'anoAtual' => $anoAtual,
            'meses' => $meses,
            'linhas' => $linhas,
            'totaisMeses' => $totaisMeses,
            'totaisMesesBase' => $totaisMesesBase,
            'variacoesTotaisMeses' => $variacoesTotaisMeses,
            'totalBase' => $totalBase,
            'totalAtual' => $totalAtual,
            'variacaoTotal' => $totalBase > 0 ? (($totalAtual - $totalBase) / $totalBase) * 100 : null,
        );
    }

    if (isset($configuracao['modo']) && $configuracao['modo'] === 'cadastroAtivo') {
        $anoBase = isset($configuracao['anoBase']) ? (int) $configuracao['anoBase'] : ((int) date('Y') - 1);
        $anoAtual = isset($configuracao['anoAtual']) ? (int) $configuracao['anoAtual'] : (int) date('Y');
        $sqlBaseAnoBase = dashboardPersonalizadoSqlTexto(isset($configuracao['sqlBaseAnoBase']) ? $configuracao['sqlBaseAnoBase'] : '');
        if (!dashboardIndicadorValidarSql($sqlBaseAnoBase, $erro)) {
            return false;
        }

        $representantes = dashboardIndicadorConsultar(
            $conexao,
            "SELECT D021_Id AS representanteId, D021_Nome_Empresa AS representante FROM D021 WHERE D021_Ativo = 'S' AND NULLIF(D021_Nome_Empresa, '') IS NOT NULL ORDER BY D021_Nome_Empresa",
            array(),
            $erro
        );
        if ($representantes === false) {
            return false;
        }
        $representanteSelecionado = isset($parametros['representante']) ? trim((string) $parametros['representante']) : '';
        if ($representanteSelecionado !== '') {
            $representantes = array_values(array_filter($representantes, function ($representante) use ($representanteSelecionado) {
                return (string) $representante['representanteId'] === $representanteSelecionado;
            }));
        }

        $agruparValores = function ($sql) use ($conexao, $parametros, &$erro) {
            $resultado = dashboardIndicadorConsultar($conexao, 'SELECT representanteId, COALESCE(SUM(valor), 0) AS valor FROM (' . $sql . ') AS dashboard_base GROUP BY representanteId', $parametros, $erro);
            if ($resultado === false) {
                return false;
            }
            $porRepresentante = array();
            foreach ($resultado as $linha) {
                $porRepresentante[(string) $linha['representanteId']] = (float) $linha['valor'];
            }
            return $porRepresentante;
        };

        $valoresAtuais = $agruparValores($sqlBase);
        if ($valoresAtuais === false) {
            return false;
        }
        $valoresBase = $agruparValores($sqlBaseAnoBase);
        if ($valoresBase === false) {
            return false;
        }

        $linhas = array();
        $totalBase = 0;
        $totalAtual = 0;
        foreach ($representantes as $representante) {
            $id = (string) $representante['representanteId'];
            $valorBase = isset($valoresBase[$id]) ? $valoresBase[$id] : 0;
            $valorAtual = isset($valoresAtuais[$id]) ? $valoresAtuais[$id] : 0;
            $linhas[] = array(
                'representante' => (string) $representante['representante'],
                'quantidade' => 0,
                'valorBase' => $valorBase,
                'valorAtual' => $valorAtual,
                'variacao' => $valorBase > 0 ? (($valorAtual - $valorBase) / $valorBase) * 100 : null,
            );
            $totalBase += $valorBase;
            $totalAtual += $valorAtual;
        }

        return array(
            'anoBase' => $anoBase,
            'anoAtual' => $anoAtual,
            'linhas' => $linhas,
            'totalBase' => $totalBase,
            'totalAtual' => $totalAtual,
            'variacaoTotal' => $totalBase > 0 ? (($totalAtual - $totalBase) / $totalBase) * 100 : null,
        );
    }

    $nomes = isset($configuracao['incluirNomes']) && is_array($configuracao['incluirNomes']) ? $configuracao['incluirNomes'] : array();
    if (empty($nomes)) {
        $erro = 'Informe os representantes do comparativo.';
        return false;
    }

    $referencia = isset($configuracao['referencia2025']) && is_array($configuracao['referencia2025']) ? $configuracao['referencia2025'] : array();
    $referenciaPorNome = array();
    foreach ($referencia as $nome => $valor) {
        $referenciaPorNome[dashboardIndicadorNormalizarNome($nome)] = (float) $valor;
    }

    $sql = 'SELECT COALESCE(NULLIF(`descricao`, \'\'), \'Não informado\') AS representante, COUNT(*) AS quantidade, COALESCE(SUM(`valor`), 0) AS valorAtual FROM (' . $sqlBase . ') AS dashboard_base GROUP BY representante';
    $resultado = dashboardIndicadorConsultar($conexao, $sql, $parametros, $erro);
    if ($resultado === false) {
        return false;
    }

    $atualPorNome = array();
    foreach ($resultado as $linha) {
        $chave = dashboardIndicadorNormalizarNome(isset($linha['representante']) ? $linha['representante'] : '');
        if ($chave === '') {
            continue;
        }
        if (!isset($atualPorNome[$chave])) {
            $atualPorNome[$chave] = array('quantidade' => 0, 'valor' => 0);
        }
        $atualPorNome[$chave]['quantidade'] += (int) $linha['quantidade'];
        $atualPorNome[$chave]['valor'] += (float) $linha['valorAtual'];
    }

    $linhas = array();
    $totalBase = 0;
    $totalAtual = 0;
    foreach ($nomes as $nome) {
        $nome = trim((string) $nome);
        if ($nome === '') {
            continue;
        }
        $chave = dashboardIndicadorNormalizarNome($nome);
        $valorBase = isset($referenciaPorNome[$chave]) ? $referenciaPorNome[$chave] : 0;
        $atual = isset($atualPorNome[$chave]) ? $atualPorNome[$chave] : array('quantidade' => 0, 'valor' => 0);
        $valorAtual = (float) $atual['valor'];
        $linhas[] = array(
            'representante' => $nome,
            'quantidade' => (int) $atual['quantidade'],
            'valorBase' => $valorBase,
            'valorAtual' => $valorAtual,
            'variacao' => $valorBase > 0 ? (($valorAtual - $valorBase) / $valorBase) * 100 : null,
        );
        $totalBase += $valorBase;
        $totalAtual += $valorAtual;
    }

    return array(
        'anoBase' => isset($configuracao['anoBase']) ? (int) $configuracao['anoBase'] : 2025,
        'anoAtual' => isset($configuracao['anoAtual']) ? (int) $configuracao['anoAtual'] : (int) date('Y'),
        'linhas' => $linhas,
        'totalBase' => $totalBase,
        'totalAtual' => $totalAtual,
        'variacaoTotal' => $totalBase > 0 ? (($totalAtual - $totalBase) / $totalBase) * 100 : null,
    );
}

function dashboardIndicadorDefinicaoPublica($indicador, $filtros)
{
    return array(
        'id' => $indicador['id'],
        'titulo' => $indicador['titulo'],
        'subtitulo' => isset($indicador['subtitulo']) ? $indicador['subtitulo'] : '',
        'icone' => isset($indicador['icone']) ? $indicador['icone'] : 'chart',
        'cor' => isset($indicador['cor']) ? $indicador['cor'] : 'blue',
        'filtros' => $filtros,
        'card' => isset($indicador['card']) ? $indicador['card'] : array(),
        'geografia' => isset($indicador['geografia']) ? $indicador['geografia'] : array(),
        'composicao' => isset($indicador['composicao']) ? $indicador['composicao'] : array(),
        'grafico' => isset($indicador['grafico']) ? $indicador['grafico'] : array(),
        'graficosPorGrupo' => isset($indicador['graficosPorGrupo']) ? $indicador['graficosPorGrupo'] : array(),
        'calendarios' => isset($indicador['calendarios']) && is_array($indicador['calendarios']) ? $indicador['calendarios'] : array(),
        'listagem' => isset($indicador['listagem']) ? $indicador['listagem'] : array(),
        'comparativoRepresentantes' => isset($indicador['comparativoRepresentantes']) ? $indicador['comparativoRepresentantes'] : array(),
        'abas' => isset($indicador['abas']) && is_array($indicador['abas']) ? array_map(function ($aba) {
            return array(
                'id' => isset($aba['id']) ? $aba['id'] : '',
                'titulo' => isset($aba['titulo']) ? $aba['titulo'] : '',
                'filtros' => dashboardIndicadorFiltrosSemOpcoesDinamicas(isset($aba['filtros']) ? $aba['filtros'] : array()),
            );
        }, $indicador['abas']) : array(),
    );
}

function dashboardIndicadorFeriadosNacionais($ano)
{
    static $porAno = array();
    $ano = (int) $ano;
    if (isset($porAno[$ano])) {
        return $porAno[$ano];
    }

    $feriados = array();
    foreach (array('01-01', '04-21', '05-01', '09-07', '10-12', '11-02', '11-15', '12-25') as $mesDia) {
        $feriados[sprintf('%04d-%s', $ano, $mesDia)] = true;
    }
    // A primeira ocorrência nacional de 20/11 após a Lei 14.759/2023 é em 2024.
    if ($ano >= 2024) {
        $feriados[sprintf('%04d-11-20', $ano)] = true;
    }

    // Páscoa gregoriana (Meeus/Jones/Butcher), sem depender da extensão calendar.
    $a = $ano % 19;
    $b = intdiv($ano, 100);
    $c = $ano % 100;
    $d = intdiv($b, 4);
    $e = $b % 4;
    $f = intdiv($b + 8, 25);
    $g = intdiv($b - $f + 1, 3);
    $h = (19 * $a + $b - $d - $g + 15) % 30;
    $i = intdiv($c, 4);
    $k = $c % 4;
    $l = (32 + 2 * $e + 2 * $i - $h - $k) % 7;
    $m = intdiv($a + 11 * $h + 22 * $l, 451);
    $mes = intdiv($h + $l - 7 * $m + 114, 31);
    $dia = ($h + $l - 7 * $m + 114) % 31 + 1;
    $paixao = \DateTime::createFromFormat('!Y-m-d', sprintf('%04d-%02d-%02d', $ano, $mes, $dia));
    if ($paixao) {
        $paixao->modify('-2 days');
        $feriados[$paixao->format('Y-m-d')] = true;
    }

    $porAno[$ano] = $feriados;
    return $feriados;
}

function dashboardIndicadorProximoDiaUtil($data, $feriados, $diasUteisAdicionais, $considerarFeriadosNacionais = false)
{
    $dataOriginal = substr((string) $data, 0, 10);
    $dataUtil = \DateTime::createFromFormat('!Y-m-d', $dataOriginal);
    if (!$dataUtil || $dataUtil->format('Y-m-d') !== $dataOriginal) {
        return '';
    }
    $ehDiaUtil = function ($dia) use ($feriados, $considerarFeriadosNacionais) {
        $diaSemana = (int) $dia->format('N');
        $dataDia = $dia->format('Y-m-d');
        if ($diaSemana > 5 || isset($feriados[$dataDia])) {
            return false;
        }
        $nacionais = $considerarFeriadosNacionais ? dashboardIndicadorFeriadosNacionais($dia->format('Y')) : array();
        return !isset($nacionais[$dataDia]);
    };
    $limite = 0;
    while (!$ehDiaUtil($dataUtil) && $limite++ < 31) {
        $dataUtil->modify('+1 day');
    }
    $adicionados = 0;
    $diasUteisAdicionais = max(0, min(15, (int) $diasUteisAdicionais));
    while ($adicionados < $diasUteisAdicionais && $limite++ < 62) {
        $dataUtil->modify('+1 day');
        if ($ehDiaUtil($dataUtil)) {
            $adicionados++;
        }
    }
    return $dataUtil->format('Y-m-d');
}

function dashboardIndicadorSqlPareto($sqlBase, $grafico, $quantidadeProdutos)
{
    $campos = array();
    foreach (array('Posicao' => 'posicao', 'Codigo' => 'codigo', 'Descricao' => 'descricao', 'Valor' => 'valor', 'Acumulado' => 'percentualAcumulado', 'Classe' => 'classificacao') as $opcao => $padrao) {
        $campos[$opcao] = '`' . dashboardIndicadorIdentificador(isset($grafico['campo' . $opcao]) ? $grafico['campo' . $opcao] : $padrao, $padrao) . '`';
    }
    $maxPontos = isset($grafico['maxPontos']) ? max(10, min(100, (int) $grafico['maxPontos'])) : 60;
    // Cada barra precisa caber inteira na listagem ao ser selecionada.
    $tamanhoGrupo = min(500, max(1, (int) ceil($quantidadeProdutos / $maxPontos)));
    // Agrupa posições consecutivas, sem limitar a população usada pela curva.
    $sql = 'SELECT FLOOR((' . $campos['Posicao'] . ' - 1) / ' . $tamanhoGrupo . ') AS grupo, '
        . 'MIN(' . $campos['Posicao'] . ') AS posicaoInicio, MAX(' . $campos['Posicao'] . ') AS posicaoFim, '
        . 'COUNT(*) AS produtos, MAX(' . $campos['Codigo'] . ') AS codigo, MAX(' . $campos['Descricao'] . ') AS descricao, '
        . 'SUM(' . $campos['Valor'] . ') AS valor, MAX(' . $campos['Acumulado'] . ') AS acumulado';
    foreach (array('A', 'B', 'C') as $classe) {
        $condicao = $campos['Classe'] . " = '" . $classe . "'";
        $sql .= ', SUM(CASE WHEN ' . $condicao . ' THEN ' . $campos['Valor'] . ' ELSE 0 END) AS valor' . $classe;
        $sql .= ', SUM(CASE WHEN ' . $condicao . ' THEN 1 ELSE 0 END) AS produtos' . $classe;
    }
    return $sql . ' FROM (' . $sqlBase . ') AS dashboard_pareto GROUP BY grupo ORDER BY grupo';
}

function dashboardIndicadorFaixaPareto($entrada, $grafico, &$erro)
{
    $erro = '';
    if (!is_array($grafico) || !isset($grafico['tipo']) || $grafico['tipo'] !== 'pareto') {
        return null;
    }

    $temInicio = array_key_exists('paretoInicio', $entrada);
    $temFim = array_key_exists('paretoFim', $entrada);
    if (!$temInicio && !$temFim) {
        return null;
    }
    if (!$temInicio || !$temFim || is_array($entrada['paretoInicio']) || is_array($entrada['paretoFim'])) {
        $erro = 'A faixa selecionada no gráfico é inválida.';
        return false;
    }

    $inicio = trim((string) $entrada['paretoInicio']);
    $fim = trim((string) $entrada['paretoFim']);
    if (!preg_match('/^[1-9][0-9]{0,8}$/', $inicio) || !preg_match('/^[1-9][0-9]{0,8}$/', $fim) || (int) $fim < (int) $inicio) {
        $erro = 'A faixa selecionada no gráfico é inválida.';
        return false;
    }

    return array(
        'inicio' => (int) $inicio,
        'fim' => (int) $fim,
        'campo' => dashboardIndicadorIdentificador(isset($grafico['campoPosicao']) ? $grafico['campoPosicao'] : 'posicao', 'posicao'),
    );
}

function dashboardIndicadorClassePareto($entrada, $grafico, &$erro)
{
    $erro = '';
    if (!is_array($grafico) || !isset($grafico['tipo']) || $grafico['tipo'] !== 'pareto' || !array_key_exists('paretoClasse', $entrada)) {
        return null;
    }
    if (is_array($entrada['paretoClasse'])) {
        $erro = 'A classe selecionada no gráfico é inválida.';
        return false;
    }
    $classe = strtoupper(trim((string) $entrada['paretoClasse']));
    if (!in_array($classe, array('A', 'B', 'C'), true)) {
        $erro = 'A classe selecionada no gráfico é inválida.';
        return false;
    }
    return $classe;
}

function dashboardIndicadorExecutar($conexao, $indicador, $empresa, $entrada, &$erro)
{
    $erro = '';
    if (dashboardClientesInativosHabilitado($indicador)) {
        $indicador = dashboardClientesInativosConfigurar($indicador, $entrada);
    }
    $sqlBase = dashboardPersonalizadoSqlTexto(isset($indicador['sqlBase']) ? $indicador['sqlBase'] : '');
    if (!dashboardIndicadorValidarSql($sqlBase, $erro, !empty($indicador['multiempresa']))) {
        return false;
    }

    $filtrosConfigurados = dashboardIndicadorCarregarOpcoesFiltros($conexao, isset($indicador['filtros']) ? $indicador['filtros'] : array(), $empresa, $erro);
    if ($filtrosConfigurados === false) {
        return false;
    }
    $indicador['filtros'] = $filtrosConfigurados;

    $errosFiltros = array();
    $filtros = dashboardIndicadorFiltros($indicador, $entrada, $errosFiltros);
    if (!empty($errosFiltros)) {
        $erro = implode(' ', $errosFiltros);
        return false;
    }
    if (dashboardClientesInativosHabilitado($indicador) && $filtros['valores']['situacao'] === 'reativados') {
        return dashboardClientesInativosHistorico($conexao, $indicador, $empresa, $filtros, $erro);
    }
    $detalheGrupo = isset($entrada['detalheGrupo']) && !is_array($entrada['detalheGrupo']) ? trim((string) $entrada['detalheGrupo']) : '';
    if (function_exists('mb_strlen') ? mb_strlen($detalheGrupo, 'UTF-8') > 200 : strlen($detalheGrupo) > 200) {
        $erro = 'O vendedor selecionado para o detalhamento é inválido.';
        return false;
    }
    $detalheCalendarioId = isset($entrada['detalheCalendarioId']) && !is_array($entrada['detalheCalendarioId']) ? trim((string) $entrada['detalheCalendarioId']) : '';
    $detalheCalendarioData = isset($entrada['detalheCalendarioData']) && !is_array($entrada['detalheCalendarioData']) ? trim((string) $entrada['detalheCalendarioData']) : '';
    if (($detalheCalendarioId === '') !== ($detalheCalendarioData === '')) {
        $erro = 'Informe o calendário e a data para carregar o detalhamento.';
        return false;
    }
    if ($detalheCalendarioId !== '' && !preg_match('/^[a-zA-Z0-9_-]{1,80}$/', $detalheCalendarioId)) {
        $erro = 'O calendário selecionado para o detalhamento é inválido.';
        return false;
    }
    if ($detalheCalendarioData !== '') {
        $dataCalendario = \DateTime::createFromFormat('!Y-m-d', $detalheCalendarioData);
        if (!$dataCalendario || $dataCalendario->format('Y-m-d') !== $detalheCalendarioData) {
            $erro = 'A data selecionada para o detalhamento é inválida.';
            return false;
        }
    }
    $detalheGraficoSerie = isset($entrada['detalheGraficoSerie']) && !is_array($entrada['detalheGraficoSerie']) ? trim((string) $entrada['detalheGraficoSerie']) : '';
    $detalheGraficoData = isset($entrada['detalheGraficoData']) && !is_array($entrada['detalheGraficoData']) ? trim((string) $entrada['detalheGraficoData']) : '';
    $detalheGraficoDataInicio = isset($entrada['detalheGraficoDataInicio']) && !is_array($entrada['detalheGraficoDataInicio']) ? trim((string) $entrada['detalheGraficoDataInicio']) : $detalheGraficoData;
    $detalheGraficoDataFim = isset($entrada['detalheGraficoDataFim']) && !is_array($entrada['detalheGraficoDataFim']) ? trim((string) $entrada['detalheGraficoDataFim']) : $detalheGraficoData;
    if (($detalheGraficoSerie === '') !== ($detalheGraficoDataInicio === '' && $detalheGraficoDataFim === '')) {
        $erro = 'Informe a série e o período para carregar o detalhamento do gráfico.';
        return false;
    }
    if (($detalheGraficoDataInicio === '') !== ($detalheGraficoDataFim === '')) {
        $erro = 'Informe as datas inicial e final do detalhamento do gráfico.';
        return false;
    }
    if ($detalheGraficoSerie !== '' && !preg_match('/^[a-zA-Z0-9_-]{1,80}$/', $detalheGraficoSerie)) {
        $erro = 'A série selecionada para o detalhamento é inválida.';
        return false;
    }
    if ($detalheGraficoDataInicio !== '') {
        $dataGraficoInicio = \DateTime::createFromFormat('!Y-m-d', $detalheGraficoDataInicio);
        $dataGraficoFim = \DateTime::createFromFormat('!Y-m-d', $detalheGraficoDataFim);
        if (!$dataGraficoInicio || $dataGraficoInicio->format('Y-m-d') !== $detalheGraficoDataInicio || !$dataGraficoFim || $dataGraficoFim->format('Y-m-d') !== $detalheGraficoDataFim || $detalheGraficoDataInicio > $detalheGraficoDataFim) {
            $erro = 'O período selecionado para o detalhamento do gráfico é inválido.';
            return false;
        }
    }
    $parametros = array_merge(array('empresa' => (int) $empresa, 'detalheGrupo' => $detalheGrupo), $filtros['valores']);
    $periodoProjecao = null;
    if (!empty($indicador['grafico']['limitarAoPeriodoFiltrado'])) {
        $inicioProjecao = isset($parametros['dataInicio']) ? (string) $parametros['dataInicio'] : '';
        $fimProjecao = isset($parametros['dataFim']) ? (string) $parametros['dataFim'] : '';
        $dataInicioProjecao = \DateTime::createFromFormat('!Y-m-d', $inicioProjecao);
        $dataFimProjecao = \DateTime::createFromFormat('!Y-m-d', $fimProjecao);
        if (!$dataInicioProjecao || !$dataFimProjecao || $dataInicioProjecao->format('Y-m-d') !== $inicioProjecao || $dataFimProjecao->format('Y-m-d') !== $fimProjecao || $inicioProjecao > $fimProjecao) {
            $erro = 'Informe um período inicial e final válido para limitar a projeção de caixa.';
            return false;
        }
        $periodoProjecao = array('dataInicio' => $inicioProjecao, 'dataFim' => $fimProjecao);
        $indicador['grafico']['periodoFiltrado'] = $periodoProjecao;
    }
    $faixaPareto = dashboardIndicadorFaixaPareto($entrada, isset($indicador['grafico']) ? $indicador['grafico'] : array(), $erro);
    if ($faixaPareto === false) {
        return false;
    }
    $classePareto = dashboardIndicadorClassePareto($entrada, isset($indicador['grafico']) ? $indicador['grafico'] : array(), $erro);
    if ($classePareto === false) {
        return false;
    }

    // A geografia permanece baseada nos filtros regulares para que o mapa e os
    // tooltips continuem disponíveis para todas as UFs com vendas. A UF clicada
    // restringe apenas os demais resultados do indicador.
    $sqlBaseMapa = $sqlBase;
    $ufMapa = isset($entrada['ufMapa']) && !is_array($entrada['ufMapa']) ? strtoupper(trim((string) $entrada['ufMapa'])) : '';
    if ($ufMapa !== '') {
        if (!preg_match('/^[A-Z]{2}$/', $ufMapa)) {
            $erro = 'UF selecionada no mapa é inválida.';
            return false;
        }
        $parametros['ufMapa'] = $ufMapa;
        $sqlBase = 'SELECT dashboard_base_mapa.* FROM (' . $sqlBase . ') AS dashboard_base_mapa WHERE dashboard_base_mapa.`uf` = :ufMapa';
    }

    preg_match_all('/:([a-z][a-zA-Z0-9_]*)/', $sqlBase, $encontrados);
    foreach (array_unique($encontrados[1]) as $parametro) {
        if (!array_key_exists($parametro, $parametros)) {
            $erro = 'O SQL usa o parâmetro :' . $parametro . ', mas não existe um filtro com esse id.';
            return false;
        }
    }

    $card = isset($indicador['card']) && is_array($indicador['card']) ? $indicador['card'] : array();
    $calculo = isset($card['calculo']) ? $card['calculo'] : 'soma';
    $campoCard = dashboardIndicadorIdentificador(isset($card['campo']) ? $card['campo'] : 'valor', 'valor');
    $agregacao = $calculo === 'contagem' ? 'COUNT(*)' : ($calculo === 'media' ? 'AVG(`' . $campoCard . '`)' : 'SUM(`' . $campoCard . '`)');
    $sqlCard = 'SELECT COUNT(*) AS quantidade, COALESCE(' . $agregacao . ', 0) AS valor FROM (' . $sqlBase . ') AS dashboard_base';
    $linhasCard = dashboardIndicadorConsultar($conexao, $sqlCard, $parametros, $erro);
    if ($linhasCard === false) {
        return false;
    }
    $resultadoCard = isset($linhasCard[0]) ? $linhasCard[0] : array('quantidade' => 0, 'valor' => 0);

    $comparativoRepresentantes = dashboardIndicadorComparativoRepresentantes(
        $conexao,
        isset($indicador['comparativoRepresentantes']) ? $indicador['comparativoRepresentantes'] : array(),
        $sqlBase,
        $parametros,
        $erro
    );
    if ($comparativoRepresentantes === false) {
        return false;
    }

    $geografiaResultado = array();
    $geografia = isset($indicador['geografia']) && is_array($indicador['geografia']) ? $indicador['geografia'] : array();
    if (!empty($geografia) && (!isset($geografia['habilitada']) || $geografia['habilitada'])) {
        $tipoGeografia = isset($geografia['tipo']) ? strtolower((string) $geografia['tipo']) : 'brasil';
        if ($tipoGeografia === 'mundo') {
            $campoPais = dashboardIndicadorIdentificador(isset($geografia['campoPais']) ? $geografia['campoPais'] : 'paisCodigo', 'paisCodigo');
            $campoNomePais = dashboardIndicadorIdentificador(isset($geografia['campoNomePais']) ? $geografia['campoNomePais'] : 'pais', 'pais');
            $campoValorPais = dashboardIndicadorIdentificador(isset($geografia['campoValor']) ? $geografia['campoValor'] : $campoCard, $campoCard);
            $campoFornecedorPais = dashboardIndicadorIdentificador(isset($geografia['campoFornecedor']) ? $geografia['campoFornecedor'] : 'fornecedor', 'fornecedor');
            $sqlPaises = "SELECT UPPER(COALESCE(NULLIF(`{$campoPais}`, ''), '')) AS codigo, MAX(COALESCE(NULLIF(`{$campoNomePais}`, ''), NULLIF(`{$campoPais}`, ''), 'Não informado')) AS pais, COALESCE(SUM(`{$campoValorPais}`), 0) AS valor, COALESCE(AVG(`{$campoValorPais}`), 0) AS ticketMedio, COUNT(*) AS quantidade, COUNT(DISTINCT NULLIF(`{$campoFornecedorPais}`, '')) AS fornecedores FROM ({$sqlBaseMapa}) AS dashboard_base GROUP BY codigo HAVING codigo <> '' ORDER BY valor DESC";
            $geografiaResultado = dashboardIndicadorConsultar($conexao, $sqlPaises, $parametros, $erro);
            if ($geografiaResultado === false) return false;
            $totalGeografia = 0;
            foreach ($geografiaResultado as $linhaPais) $totalGeografia += (float) $linhaPais['valor'];
            foreach ($geografiaResultado as &$linhaPais) $linhaPais['percentual'] = $totalGeografia > 0 ? ((float) $linhaPais['valor'] / $totalGeografia) * 100 : 0;
            unset($linhaPais);
        } else {
            $campoUf = dashboardIndicadorIdentificador(isset($geografia['campoUf']) ? $geografia['campoUf'] : 'uf', 'uf');
            $campoValorUf = dashboardIndicadorIdentificador(isset($geografia['campoValor']) ? $geografia['campoValor'] : $campoCard, $campoCard);
            $campoClienteUf = dashboardIndicadorIdentificador(isset($geografia['campoCliente']) ? $geografia['campoCliente'] : 'descricao', 'descricao');
            $sqlUf = "SELECT COALESCE(NULLIF(`{$campoUf}`, ''), 'Não informado') AS uf, COALESCE(SUM(`{$campoValorUf}`), 0) AS valor, COALESCE(AVG(`{$campoValorUf}`), 0) AS ticketMedio, COUNT(DISTINCT NULLIF(`{$campoClienteUf}`, '')) AS clientes FROM ({$sqlBaseMapa}) AS dashboard_base GROUP BY uf ORDER BY valor DESC";
            $geografiaResultado = dashboardIndicadorConsultar($conexao, $sqlUf, $parametros, $erro);
            if ($geografiaResultado === false) return false;
            $totalGeografia = 0;
            foreach ($geografiaResultado as $linhaUf) $totalGeografia += (float) $linhaUf['valor'];
            foreach ($geografiaResultado as &$linhaUf) $linhaUf['percentual'] = $totalGeografia > 0 ? ((float) $linhaUf['valor'] / $totalGeografia) * 100 : 0;
            unset($linhaUf);

            if (!empty($geografia['campoCidade'])) {
                $campoCidade = dashboardIndicadorIdentificador($geografia['campoCidade'], 'cidade');
                $sqlCidades = "SELECT COALESCE(NULLIF(`{$campoUf}`, ''), 'Não informado') AS uf, COALESCE(NULLIF(`{$campoCidade}`, ''), 'Não informado') AS cidade, COALESCE(SUM(`{$campoValorUf}`), 0) AS valor FROM ({$sqlBaseMapa}) AS dashboard_base GROUP BY uf, cidade HAVING valor > 0 ORDER BY uf, valor DESC";
                $cidadesResultado = dashboardIndicadorConsultar($conexao, $sqlCidades, $parametros, $erro);
                if ($cidadesResultado === false) return false;
                $cidadesPorUf = array();
                foreach ($cidadesResultado as $cidade) {
                    $uf = (string) $cidade['uf'];
                    if (!isset($cidadesPorUf[$uf])) $cidadesPorUf[$uf] = array();
                    if (count($cidadesPorUf[$uf]) < 3) {
                        $cidadesPorUf[$uf][] = array(
                            'cidade' => (string) $cidade['cidade'],
                            'valor' => (float) $cidade['valor']
                        );
                    }
                }
                foreach ($geografiaResultado as &$linhaUf) {
                    $uf = (string) $linhaUf['uf'];
                    if (isset($cidadesPorUf[$uf]) && !empty($cidadesPorUf[$uf])) {
                        $linhaUf['cidades'] = $cidadesPorUf[$uf];
                        // Mantém os campos existentes para o resumo geral do mapa.
                        $linhaUf['cidade'] = $cidadesPorUf[$uf][0]['cidade'];
                        $linhaUf['cidadeValor'] = $cidadesPorUf[$uf][0]['valor'];
                    }
                }
                unset($linhaUf);
            }
        }
    }

    $composicaoResultado = array();
    $composicao = isset($indicador['composicao']) && is_array($indicador['composicao']) ? $indicador['composicao'] : array();
    if (!empty($composicao) && (!isset($composicao['habilitada']) || $composicao['habilitada'])) {
        $campoComposicao = dashboardIndicadorIdentificador(isset($composicao['campo']) ? $composicao['campo'] : 'situacao', 'situacao');
        $campoValorComposicao = dashboardIndicadorIdentificador(isset($composicao['campoValor']) ? $composicao['campoValor'] : $campoCard, $campoCard);
        $calculoComposicao = isset($composicao['calculo']) ? $composicao['calculo'] : 'soma';
        $sqlComposicaoBase = dashboardPersonalizadoSqlTexto(isset($composicao['sqlBase']) ? $composicao['sqlBase'] : '');
        if ($sqlComposicaoBase !== '') {
            if (!dashboardIndicadorValidarSql($sqlComposicaoBase, $erro)) return false;
            preg_match_all('/:([a-z][a-zA-Z0-9_]*)/', $sqlComposicaoBase, $parametrosComposicao);
            foreach (array_unique($parametrosComposicao[1]) as $parametroComposicao) {
                if (!array_key_exists($parametroComposicao, $parametros)) {
                    $erro = 'A composição usa o parâmetro :' . $parametroComposicao . ', mas ele não está configurado.';
                    return false;
                }
            }
            $sqlComposicao = 'SELECT `' . $campoComposicao . '` AS chave, COALESCE(`' . $campoValorComposicao . '`, 0) AS valor FROM (' . $sqlComposicaoBase . ') AS dashboard_composicao';
        } else {
            $valorComposicao = $calculoComposicao === 'contagem' ? 'COUNT(*)' : ($calculoComposicao === 'media' ? 'AVG(`' . $campoValorComposicao . '`)' : 'SUM(`' . $campoValorComposicao . '`)');
            $sqlComposicao = 'SELECT `' . $campoComposicao . '` AS chave, COALESCE(' . $valorComposicao . ', 0) AS valor FROM (' . $sqlBase . ') AS dashboard_base GROUP BY `' . $campoComposicao . '`';
        }
        $composicaoResultado = dashboardIndicadorConsultar($conexao, $sqlComposicao, $parametros, $erro);
        if ($composicaoResultado === false) return false;
        $porChave = array();
        foreach ($composicaoResultado as $item) $porChave[(string) $item['chave']] = array('chave' => (string) $item['chave'], 'rotulo' => (string) $item['chave'], 'valor' => (float) $item['valor']);
        if (!empty($composicao['itens']) && is_array($composicao['itens'])) {
            $composicaoResultado = array();
            foreach ($composicao['itens'] as $item) {
                $chave = isset($item['chave']) ? (string) $item['chave'] : '';
                if ($chave === '') continue;
                $composicaoResultado[] = isset($porChave[$chave]) ? array_merge($porChave[$chave], array('rotulo' => isset($item['rotulo']) ? $item['rotulo'] : $chave)) : array('chave' => $chave, 'rotulo' => isset($item['rotulo']) ? $item['rotulo'] : $chave, 'valor' => 0);
            }
        } else $composicaoResultado = array_values($porChave);
    }

    $graficoResultado = array();
    $graficoDetalhes = array();
    $grafico = isset($indicador['grafico']) && is_array($indicador['grafico']) ? $indicador['grafico'] : array();
    if (!empty($grafico) && (!isset($grafico['habilitado']) || $grafico['habilitado']) && isset($grafico['tipo']) && $grafico['tipo'] === 'pareto') {
        $sqlGrafico = dashboardIndicadorSqlPareto($sqlBase, $grafico, (int) $resultadoCard['quantidade']);
        $graficoResultado = dashboardIndicadorConsultar($conexao, $sqlGrafico, $parametros, $erro);
        if ($graficoResultado === false) {
            return false;
        }
    } elseif (!empty($grafico) && (!isset($grafico['habilitado']) || $grafico['habilitado']) && isset($grafico['tipo']) && $grafico['tipo'] === 'barras_comparativas') {
        $consultasGrafico = isset($grafico['sqlSeries']) && is_array($grafico['sqlSeries']) ? $grafico['sqlSeries'] : array();
        if (empty($consultasGrafico) && isset($grafico['sqlBase'])) {
            $consultasGrafico[] = array('chave' => '', 'sqlBase' => $grafico['sqlBase']);
        }
        if (empty($consultasGrafico)) {
            $erro = 'O gráfico de barras comparativas precisa de ao menos uma consulta de série.';
            return false;
        }
        $linhasGrafico = array();
        foreach ($consultasGrafico as $consultaGrafico) {
            $chaveSerieGrafico = isset($consultaGrafico['chave']) ? trim((string) $consultaGrafico['chave']) : '';
            $sqlGraficoBase = dashboardPersonalizadoSqlTexto(isset($consultaGrafico['sqlBase']) ? $consultaGrafico['sqlBase'] : '');
            if ($sqlGraficoBase === '' || !dashboardIndicadorValidarSql($sqlGraficoBase, $erro)) {
                if ($erro === '') $erro = 'Uma das séries do gráfico não possui um SQL base válido.';
                return false;
            }
            preg_match_all('/:([a-z][a-zA-Z0-9_]*)/', $sqlGraficoBase, $parametrosGrafico);
            foreach (array_unique($parametrosGrafico[1]) as $parametroGrafico) {
                if (!array_key_exists($parametroGrafico, $parametros)) {
                    $erro = 'O gráfico usa o parâmetro :' . $parametroGrafico . ', mas ele não está configurado.';
                    return false;
                }
            }
            $linhasSerieGrafico = dashboardIndicadorConsultar($conexao, $sqlGraficoBase, $parametros, $erro);
            if ($linhasSerieGrafico === false) return false;
            foreach ($linhasSerieGrafico as $linhaSerieGrafico) {
                if ($chaveSerieGrafico !== '') $linhaSerieGrafico['serie'] = $chaveSerieGrafico;
                $linhasGrafico[] = $linhaSerieGrafico;
            }
        }

        $feriados = array();
        $considerarFeriados = !isset($grafico['considerarFeriados']) || $grafico['considerarFeriados'];
        $considerarFeriadosNacionais = $considerarFeriados && !empty($grafico['considerarFeriadosNacionais']);
        if ($considerarFeriados) {
            $linhasFeriados = dashboardIndicadorConsultar($conexao, "SELECT DISTINCT D207_Data AS data FROM D207 LEFT JOIN D206 ON D206_Id = D207_D206_Id WHERE D206_Flag_Ativo = 'S' AND D207_Data IS NOT NULL", array(), $erro);
            if ($linhasFeriados === false) return false;
            foreach ($linhasFeriados as $linhaFeriado) {
                if (!empty($linhaFeriado['data'])) $feriados[substr((string) $linhaFeriado['data'], 0, 10)] = true;
            }
        }

        $campoDataGrafico = dashboardIndicadorIdentificador(isset($grafico['campoData']) ? $grafico['campoData'] : 'data', 'data');
        $campoSerieGrafico = dashboardIndicadorIdentificador(isset($grafico['campoSerie']) ? $grafico['campoSerie'] : 'serie', 'serie');
        $campoValorGrafico = dashboardIndicadorIdentificador(isset($grafico['campoValor']) ? $grafico['campoValor'] : 'valor', 'valor');
        $campoDiasUteis = dashboardIndicadorIdentificador(isset($grafico['campoDiasUteisAdicionais']) ? $grafico['campoDiasUteisAdicionais'] : 'diasUteisAdicionais', 'diasUteisAdicionais');
        $detalhamentoGrafico = isset($grafico['detalhamento']) && is_array($grafico['detalhamento']) ? $grafico['detalhamento'] : array();
        $detalhamentoGraficoHabilitado = !empty($detalhamentoGrafico) && (!isset($detalhamentoGrafico['habilitado']) || $detalhamentoGrafico['habilitado']);
        $limiteDetalhamentoGrafico = isset($detalhamentoGrafico['limite']) ? max(1, min(5000, (int) $detalhamentoGrafico['limite'])) : 1000;
        $agrupadoGrafico = array();
        foreach ($linhasGrafico as $linhaGrafico) {
            $dataProjetada = dashboardIndicadorProximoDiaUtil(
                isset($linhaGrafico[$campoDataGrafico]) ? $linhaGrafico[$campoDataGrafico] : '',
                $feriados,
                isset($linhaGrafico[$campoDiasUteis]) ? $linhaGrafico[$campoDiasUteis] : 0,
                $considerarFeriadosNacionais
            );
            $serieGrafico = isset($linhaGrafico[$campoSerieGrafico]) ? trim((string) $linhaGrafico[$campoSerieGrafico]) : '';
            if ($dataProjetada === '' || $serieGrafico === '') continue;
            if ($periodoProjecao !== null && ($dataProjetada < $periodoProjecao['dataInicio'] || $dataProjetada > $periodoProjecao['dataFim'])) continue;
            if ($detalhamentoGraficoHabilitado && $detalheGraficoSerie !== '' && $serieGrafico === $detalheGraficoSerie && $dataProjetada >= $detalheGraficoDataInicio && $dataProjetada <= $detalheGraficoDataFim && count($graficoDetalhes) < $limiteDetalhamentoGrafico) {
                $linhaGrafico['dataProjetada'] = $dataProjetada;
                $graficoDetalhes[] = $linhaGrafico;
            }
            $chaveGrafico = $dataProjetada . "\n" . $serieGrafico;
            if (!isset($agrupadoGrafico[$chaveGrafico])) {
                $agrupadoGrafico[$chaveGrafico] = array('periodo' => $dataProjetada, 'rotulo' => date('d/m', strtotime($dataProjetada)), 'serie' => $serieGrafico, 'valor' => 0);
            }
            $agrupadoGrafico[$chaveGrafico]['valor'] += (float) (isset($linhaGrafico[$campoValorGrafico]) ? $linhaGrafico[$campoValorGrafico] : 0);
        }
        $graficoResultado = array_values($agrupadoGrafico);
        usort($graficoResultado, function ($a, $b) {
            $comparacaoData = strcmp($a['periodo'], $b['periodo']);
            return $comparacaoData !== 0 ? $comparacaoData : strcmp($a['serie'], $b['serie']);
        });
    } elseif (!empty($grafico) && (!isset($grafico['habilitado']) || $grafico['habilitado'])) {
        $campoData = dashboardIndicadorIdentificador(isset($grafico['campoData']) ? $grafico['campoData'] : 'data', 'data');
        $campoValor = dashboardIndicadorIdentificador(isset($grafico['campoValor']) ? $grafico['campoValor'] : $campoCard, $campoCard);
        $calculoGrafico = isset($grafico['calculo']) ? $grafico['calculo'] : 'soma';
        $valorGrafico = $calculoGrafico === 'contagem' ? 'COUNT(*)' : ($calculoGrafico === 'media' ? 'AVG(`' . $campoValor . '`)' : 'SUM(`' . $campoValor . '`)');
        $mesInicio = isset($filtros['valores']['dataInicio']) ? substr((string) $filtros['valores']['dataInicio'], 0, 7) : '';
        $mesFim = isset($filtros['valores']['dataFim']) ? substr((string) $filtros['valores']['dataFim'], 0, 7) : '';
        $porDia = $mesInicio !== '' && $mesInicio === $mesFim;
        $formatoPeriodo = $porDia ? '%Y-%m-%d' : '%Y-%m';
        $formatoRotulo = $porDia ? '%d/%m' : '%m/%Y';
        $sqlGrafico = "SELECT DATE_FORMAT(`{$campoData}`, '{$formatoPeriodo}') AS periodo, DATE_FORMAT(`{$campoData}`, '{$formatoRotulo}') AS rotulo, COALESCE({$valorGrafico}, 0) AS valor FROM ({$sqlBase}) AS dashboard_base GROUP BY periodo, rotulo ORDER BY periodo";
        $graficoResultado = dashboardIndicadorConsultar($conexao, $sqlGrafico, $parametros, $erro);
        if ($graficoResultado === false) {
            return false;
        }
    }

    $quantidadeClassePareto = null;
    if ($classePareto !== null) {
        $quantidadeClassePareto = 0;
        foreach ($graficoResultado as $pontoPareto) {
            $quantidadeClassePareto += isset($pontoPareto['produtos' . $classePareto]) ? (int) $pontoPareto['produtos' . $classePareto] : 0;
        }
    }

    $graficosPorGrupoResultado = array();
    $graficosPorGrupo = isset($indicador['graficosPorGrupo']) && is_array($indicador['graficosPorGrupo']) ? $indicador['graficosPorGrupo'] : array();
    if (!empty($graficosPorGrupo) && (!isset($graficosPorGrupo['habilitado']) || $graficosPorGrupo['habilitado'])) {
        $campoGrupo = dashboardIndicadorIdentificador(isset($graficosPorGrupo['campoGrupo']) ? $graficosPorGrupo['campoGrupo'] : 'grupo', 'grupo');
        $campoDataGrupo = dashboardIndicadorIdentificador(isset($graficosPorGrupo['campoData']) ? $graficosPorGrupo['campoData'] : 'data', 'data');
        $campoValorGrupo = dashboardIndicadorIdentificador(isset($graficosPorGrupo['campoValor']) ? $graficosPorGrupo['campoValor'] : $campoCard, $campoCard);
        $calculoGrupo = isset($graficosPorGrupo['calculo']) ? $graficosPorGrupo['calculo'] : 'soma';
        $valorGrupo = $calculoGrupo === 'contagem' ? 'COUNT(*)' : ($calculoGrupo === 'media' ? 'AVG(`' . $campoValorGrupo . '`)' : 'SUM(`' . $campoValorGrupo . '`)');
        $limiteGrupos = isset($graficosPorGrupo['limite']) ? max(1, min(30, (int) $graficosPorGrupo['limite'])) : 16;
        $sqlGraficosPorGrupo = "SELECT COALESCE(NULLIF(`{$campoGrupo}`, ''), 'Não informado') AS grupo, DATE(`{$campoDataGrupo}`) AS data, COALESCE({$valorGrupo}, 0) AS valor FROM ({$sqlBase}) AS dashboard_base WHERE `{$campoDataGrupo}` IS NOT NULL GROUP BY grupo, DATE(`{$campoDataGrupo}`) ORDER BY grupo, data";
        $linhasGraficosPorGrupo = dashboardIndicadorConsultar($conexao, $sqlGraficosPorGrupo, $parametros, $erro);
        if ($linhasGraficosPorGrupo === false) return false;
        $grupos = array();
        foreach ($linhasGraficosPorGrupo as $linhaGrupo) {
            $grupo = isset($linhaGrupo['grupo']) ? (string) $linhaGrupo['grupo'] : 'Não informado';
            if (!isset($grupos[$grupo])) $grupos[$grupo] = array('grupo' => $grupo, 'total' => 0, 'serie' => array());
            $valor = isset($linhaGrupo['valor']) ? (float) $linhaGrupo['valor'] : 0;
            $grupos[$grupo]['total'] += $valor;
            $grupos[$grupo]['serie'][] = array('data' => isset($linhaGrupo['data']) ? $linhaGrupo['data'] : '', 'valor' => $valor);
        }
        uasort($grupos, function ($a, $b) { return $b['total'] <=> $a['total']; });
        $graficosPorGrupoResultado = array_slice(array_values($grupos), 0, $limiteGrupos);
    }

    $calendariosResultado = array();
    $calendarioDetalhes = array();
    $calendarios = isset($indicador['calendarios']) && is_array($indicador['calendarios']) ? $indicador['calendarios'] : array();
    foreach ($calendarios as $calendario) {
        if (!is_array($calendario) || (isset($calendario['habilitado']) && !$calendario['habilitado'])) {
            $calendariosResultado[] = array();
            continue;
        }
        $campoDataCalendario = dashboardIndicadorIdentificador(isset($calendario['campoData']) ? $calendario['campoData'] : 'data', 'data');
        $campoValorCalendario = dashboardIndicadorIdentificador(isset($calendario['campoValor']) ? $calendario['campoValor'] : $campoCard, $campoCard);
        $calculoCalendario = isset($calendario['calculo']) ? $calendario['calculo'] : 'soma';
        $valorCalendario = $calculoCalendario === 'contagem' ? 'COUNT(*)' : ($calculoCalendario === 'media' ? 'AVG(`' . $campoValorCalendario . '`)' : 'SUM(`' . $campoValorCalendario . '`)');
        $sqlCalendario = "SELECT DATE(`{$campoDataCalendario}`) AS data, COALESCE({$valorCalendario}, 0) AS valor FROM ({$sqlBase}) AS dashboard_base WHERE `{$campoDataCalendario}` IS NOT NULL GROUP BY DATE(`{$campoDataCalendario}`) ORDER BY data";
        $parametrosCalendario = $parametros;
        if (isset($calendario['periodo']) && $calendario['periodo'] === 'ultimos_30_dias') {
            $parametrosCalendario['dataInicio'] = date('Y-m-d', strtotime('-29 days'));
            $parametrosCalendario['dataFim'] = date('Y-m-d');
        } elseif (isset($calendario['periodo']) && $calendario['periodo'] === 'proximos_30_dias') {
            $parametrosCalendario['dataInicio'] = date('Y-m-d');
            $parametrosCalendario['dataFim'] = date('Y-m-d', strtotime('+29 days'));
        }
        $resultadoCalendario = dashboardIndicadorConsultar($conexao, $sqlCalendario, $parametrosCalendario, $erro);
        if ($resultadoCalendario === false) {
            return false;
        }
        $calendariosResultado[] = $resultadoCalendario;

        $calendarioId = isset($calendario['id']) ? (string) $calendario['id'] : '';
        $detalhamentoCalendario = isset($calendario['detalhamento']) && is_array($calendario['detalhamento']) ? $calendario['detalhamento'] : array();
        if ($detalheCalendarioId !== '' && $calendarioId === $detalheCalendarioId && !empty($detalhamentoCalendario) && (!isset($detalhamentoCalendario['habilitado']) || $detalhamentoCalendario['habilitado'])) {
            $limiteDetalhamentoCalendario = isset($detalhamentoCalendario['limite']) ? max(1, min(5000, (int) $detalhamentoCalendario['limite'])) : 1000;
            $parametrosCalendario['detalheCalendarioData'] = $detalheCalendarioData;
            $sqlDetalhamentoCalendario = "SELECT * FROM ({$sqlBase}) AS dashboard_calendario_detalhamento WHERE `{$campoDataCalendario}` IS NOT NULL AND DATE(`{$campoDataCalendario}`) = :detalheCalendarioData AND COALESCE(`{$campoValorCalendario}`, 0) > 0 ORDER BY `{$campoDataCalendario}` LIMIT {$limiteDetalhamentoCalendario}";
            $calendarioDetalhes = dashboardIndicadorConsultar($conexao, $sqlDetalhamentoCalendario, $parametrosCalendario, $erro);
            if ($calendarioDetalhes === false) {
                return false;
            }
        }
    }

    $listagemResultado = array();
    $listagemDetalhes = array();
    $listagem = isset($indicador['listagem']) && is_array($indicador['listagem']) ? $indicador['listagem'] : array();
    if (!empty($listagem) && (!isset($listagem['habilitada']) || $listagem['habilitada'])) {
        $limite = isset($listagem['limite']) ? max(1, min(500, (int) $listagem['limite'])) : 200;
        if ($quantidadeClassePareto !== null) {
            $limite = min(2000, max($limite, $quantidadeClassePareto));
        }
        $agrupamento = isset($listagem['agrupamento']) && is_array($listagem['agrupamento']) ? $listagem['agrupamento'] : array();
        $ordenacao = isset($listagem['ordenacao']) && is_array($listagem['ordenacao']) ? $listagem['ordenacao'] : array();
        $ordemSql = '';
        if (!empty($ordenacao)) {
            $campoOrdem = dashboardIndicadorIdentificador(isset($ordenacao['campo']) ? $ordenacao['campo'] : 'valor', 'valor');
            $direcaoOrdem = strtoupper(isset($ordenacao['direcao']) ? $ordenacao['direcao'] : 'DESC');
            $direcaoOrdem = $direcaoOrdem === 'ASC' ? 'ASC' : 'DESC';
            $ordemSql = ' ORDER BY `' . $campoOrdem . '` ' . $direcaoOrdem;
        }
        $filtrosPareto = array();
        if ($faixaPareto !== null && empty($agrupamento)) {
            $parametros['paretoInicio'] = $faixaPareto['inicio'];
            $parametros['paretoFim'] = $faixaPareto['fim'];
            $filtrosPareto[] = '`' . $faixaPareto['campo'] . '` BETWEEN :paretoInicio AND :paretoFim';
        }
        if ($classePareto !== null && empty($agrupamento)) {
            $parametros['paretoClasse'] = $classePareto;
            $campoClassePareto = dashboardIndicadorIdentificador(isset($indicador['grafico']['campoClasse']) ? $indicador['grafico']['campoClasse'] : 'classificacao', 'classificacao');
            $filtrosPareto[] = '`' . $campoClassePareto . '` = :paretoClasse';
        }
        $filtroPareto = empty($filtrosPareto) ? '' : ' WHERE ' . implode(' AND ', $filtrosPareto);
        if (!empty($agrupamento)) {
            $campoAgrupamento = dashboardIndicadorIdentificador(isset($agrupamento['campo']) ? $agrupamento['campo'] : 'descricao', 'descricao');
            $campoValorAgrupamento = dashboardIndicadorIdentificador(isset($agrupamento['campoValor']) ? $agrupamento['campoValor'] : $campoCard, $campoCard);
            $campoContagem = dashboardIndicadorIdentificador(isset($agrupamento['campoContagem']) ? $agrupamento['campoContagem'] : '', '');
            $camposExtras = isset($agrupamento['campos']) && is_array($agrupamento['campos']) ? $agrupamento['campos'] : array();
            $camposExtrasSql = '';
            $camposExtrasIncluidos = array();
            $camposSoma = isset($agrupamento['camposSoma']) && is_array($agrupamento['camposSoma']) ? $agrupamento['camposSoma'] : array();
            foreach ($camposSoma as $campoSoma) {
                if (!is_string($campoSoma)) {
                    continue;
                }
                $campoSoma = dashboardIndicadorIdentificador($campoSoma, '');
                if ($campoSoma === '' || $campoSoma === $campoAgrupamento || in_array($campoSoma, array('quantidade', 'valor', 'percentual'), true) || isset($camposExtrasIncluidos[$campoSoma])) {
                    continue;
                }
                $camposExtrasIncluidos[$campoSoma] = true;
                $camposExtrasSql .= ', COALESCE(SUM(`' . $campoSoma . '`), 0) AS `' . $campoSoma . '`';
            }
            foreach ($camposExtras as $campoExtra) {
                $campoExtra = dashboardIndicadorIdentificador($campoExtra, '');
                if ($campoExtra === '' || $campoExtra === $campoAgrupamento || isset($camposExtrasIncluidos[$campoExtra])) {
                    continue;
                }
                $camposExtrasIncluidos[$campoExtra] = true;
                $camposExtrasSql .= ', MAX(`' . $campoExtra . '`) AS `' . $campoExtra . '`';
            }
            // Converte os campos para texto antes de comparar com string vazia.
            // Em modo SQL estrito, NULLIF(data, '') falha com "Incorrect DATE value".
            $campoAgrupamentoSql = "COALESCE(NULLIF(CAST(`{$campoAgrupamento}` AS CHAR), ''), 'Não informado')";
            $contagemSql = $campoContagem !== '' ? "COUNT(DISTINCT NULLIF(CAST(`{$campoContagem}` AS CHAR), ''))" : 'COUNT(*)';
            $ordemAgrupamentoSql = $ordemSql !== '' ? $ordemSql : ' ORDER BY valor DESC';
            $sqlListagem = "SELECT {$campoAgrupamentoSql} AS `{$campoAgrupamento}`, {$contagemSql} AS quantidade, COALESCE(SUM(`{$campoValorAgrupamento}`), 0) AS valor{$camposExtrasSql} FROM (" . $sqlBase . ") AS dashboard_base GROUP BY `{$campoAgrupamento}`" . $ordemAgrupamentoSql . " LIMIT " . $limite;
        } else $sqlListagem = 'SELECT * FROM (' . $sqlBase . ') AS dashboard_base' . $filtroPareto . $ordemSql . ' LIMIT ' . $limite;
        $listagemResultado = dashboardIndicadorConsultar($conexao, $sqlListagem, $parametros, $erro);
        if ($listagemResultado === false) {
            return false;
        }
        if (!empty($agrupamento)) foreach ($listagemResultado as &$linha) $linha['percentual'] = (float) $resultadoCard['valor'] > 0 ? ((float) $linha['valor'] / (float) $resultadoCard['valor']) * 100 : 0;

        $detalhamento = isset($listagem['detalhamento']) && is_array($listagem['detalhamento']) ? $listagem['detalhamento'] : array();
        $sqlDetalhamentoBase = dashboardPersonalizadoSqlTexto(isset($detalhamento['sqlBase']) ? $detalhamento['sqlBase'] : '');
        $detalhamentoModal = !empty($detalhamento['modal']);
        if ($sqlDetalhamentoBase !== '' && (!isset($detalhamento['habilitado']) || $detalhamento['habilitado']) && (!$detalhamentoModal || $detalheGrupo !== '')) {
            if (!dashboardIndicadorValidarSql($sqlDetalhamentoBase, $erro)) return false;
            preg_match_all('/:([a-z][a-zA-Z0-9_]*)/', $sqlDetalhamentoBase, $parametrosDetalhamento);
            foreach (array_unique($parametrosDetalhamento[1]) as $parametroDetalhamento) {
                if (!array_key_exists($parametroDetalhamento, $parametros)) {
                    $erro = 'O detalhamento usa o parâmetro :' . $parametroDetalhamento . ', mas ele não está configurado.';
                    return false;
                }
            }
            $campoGrupoDetalhamento = dashboardIndicadorIdentificador(isset($detalhamento['campoGrupo']) ? $detalhamento['campoGrupo'] : $campoAgrupamento, $campoAgrupamento);
            // Em janelas modais, a consulta já é restrita a um único grupo. O
            // limite maior evita que notas antigas desapareçam por um corte global.
            $limiteDetalhamento = isset($detalhamento['limite']) ? max(1, min(5000, (int) $detalhamento['limite'])) : $limite;
            $sqlDetalhamento = 'SELECT * FROM (' . $sqlDetalhamentoBase . ') AS dashboard_detalhamento';
            if ($detalhamentoModal) {
                $sqlDetalhamento .= " WHERE COALESCE(NULLIF(`{$campoGrupoDetalhamento}`, ''), 'Não informado') = :detalheGrupo";
            }
            $sqlDetalhamento .= ' LIMIT ' . $limiteDetalhamento;
            $linhasDetalhamento = dashboardIndicadorConsultar($conexao, $sqlDetalhamento, $parametros, $erro);
            if ($linhasDetalhamento === false) return false;
            foreach ($linhasDetalhamento as $linhaDetalhamento) {
                $grupo = isset($linhaDetalhamento[$campoGrupoDetalhamento]) ? trim((string) $linhaDetalhamento[$campoGrupoDetalhamento]) : '';
                $grupo = $grupo === '' ? 'Não informado' : $grupo;
                if (!isset($listagemDetalhes[$grupo])) $listagemDetalhes[$grupo] = array();
                $listagemDetalhes[$grupo][] = $linhaDetalhamento;
            }
        }
    }

    if (dashboardClientesInativosHabilitado($indicador)) {
        $listagemResultado = dashboardClientesInativosEnriquecer($conexao, $empresa, $listagemResultado, $erro);
        if ($listagemResultado === false) return false;
    }

    $definicaoPublica = dashboardIndicadorDefinicaoPublica($indicador, $filtros['publicos']);
    return array(
        'indicador' => $definicaoPublica,
        'card' => array(
            'valor' => isset($resultadoCard['valor']) ? (float) $resultadoCard['valor'] : 0,
            'quantidade' => isset($resultadoCard['quantidade']) ? (int) $resultadoCard['quantidade'] : 0,
            'formato' => dashboardIndicadorFormatoPermitido(isset($card['formato']) ? $card['formato'] : 'numero'),
        ),
        'geografia' => $geografiaResultado,
        'composicao' => $composicaoResultado,
        'grafico' => $graficoResultado,
        'graficoDetalhes' => $graficoDetalhes,
        'graficosPorGrupo' => $graficosPorGrupoResultado,
        'calendarios' => $calendariosResultado,
        'calendarioDetalhes' => $calendarioDetalhes,
        'listagem' => $listagemResultado,
        'paretoClasse' => $classePareto === null ? null : array('classe' => $classePareto, 'quantidade' => $quantidadeClassePareto),
        'listagemDetalhes' => $listagemDetalhes,
        'comparativoRepresentantes' => $comparativoRepresentantes,
    );
}
