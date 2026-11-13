<?php
namespace hardness;

require_once __DIR__ . '/clientes-inativos.php';
require_once __DIR__ . '/desempenho-comercial.php';
require_once __DIR__ . '/painel-cotacoes.php';
require_once __DIR__ . '/conferencia.php';
require_once __DIR__ . '/performance.php';
require_once __DIR__ . '/previsao-caixa.php';
require_once __DIR__ . '/saldo-caixa.php';
require_once __DIR__ . '/comparativo-periodos.php';
require_once __DIR__ . '/listagem-personalizada.php';
require_once __DIR__ . '/impressao-marcadas.php';
require_once __DIR__ . '/abc-clientes.php';
require_once __DIR__ . '/analise-sql.php';
require_once __DIR__ . '/controle-carteira-bancos.php';
require_once __DIR__ . '/barras-empilhadas.php';
require_once __DIR__ . '/fonte-compartilhada.php';

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
        // Material, distritos de clientes, metas e planos de corte da Inecel.
        if (!preg_match('/^(?:T|D|C|R|N)[0-9]{3}[A-Z]?$/i', $tabela) && !in_array(strtoupper($tabela), array('SMT029', 'SMT030', 'SMT063', 'SMT071', 'SMT072', 'SMT078', 'SMT094', 'SMT095', 'SMT120', 'SMT130', 'SMT134'), true)) {
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
    if ($padrao === 'fim_mes') {
        return date('Y-m-t');
    }
    if ($padrao === 'fim_ano') return date('Y-12-31');
    if ($padrao === 'ano_atual') return date('Y');
    if ($padrao === 'mes_atual') return date('n');
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
            if (!empty($filtro['inteiro']) && (floor($valor) != $valor
                || (isset($filtro['minimo']) && $valor < (float) $filtro['minimo'])
                || (isset($filtro['maximo']) && $valor > (float) $filtro['maximo']))) {
                $erros[] = 'Valor inválido no filtro ' . $id . '.';
                continue;
            }
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
            $valor = $id === 'contasCaixa' && dashboardSaldoCaixaHabilitado($indicador) ? substr($valor, 0, 6000) : mb_substr($valor, 0, 200, 'UTF-8');
        }

        if (!empty($filtro['obrigatorio']) && $valor === '') {
            $erros[] = 'Preencha o filtro ' . $id . '.';
        }

        $valores[$id] = $valor;
        $filtroPublico = $filtro;
        $filtroPublico['valor'] = $valor;
        $publicos[] = $filtroPublico;
    }

    // O horizonte é validado antes de qualquer consulta, inclusive no detalhe/exportação.
    if (!empty($indicador['grafico']['saldoAcumulado']['habilitado'])) {
        $dias = isset($valores['diasProjecao']) ? $valores['diasProjecao'] : 45;
        $saldo = isset($valores['saldoInicial']) ? $valores['saldoInicial'] : 0;
        $inicio = isset($valores['dataInicio']) ? (string) $valores['dataInicio'] : '';
        $data = \DateTime::createFromFormat('!Y-m-d', $inicio);
        if (!is_numeric($dias) || !is_finite((float) $dias) || $dias < 1 || $dias > 366 || floor($dias) != $dias) {
            $erros[] = 'Informe um prazo inteiro entre 1 e 366 dias.';
        } elseif (!$data || $data->format('Y-m-d') !== $inicio) {
            $erros[] = 'Informe a data inicial do fluxo de caixa.';
        } elseif (!is_numeric($saldo) || !is_finite((float) $saldo) || abs($saldo) > 1000000000000) {
            $erros[] = 'Informe um saldo inicial válido de até 1 trilhão.';
        } else {
            $valores['dataFim'] = $data->modify('+' . ((int) $dias - 1) . ' days')->format('Y-m-d');
            $valores['saldoInicial'] = round((float) $saldo, 2);
        }
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

require_once __DIR__ . '/carregamento-progressivo.php';
function dashboardIndicadorConsultar($conexao, $sql, $parametros, &$erro)
{
    if (!empty($GLOBALS['dashboardConsultasCompartilhadas'])) {
        $chaveCompartilhada = dashboardConsultasCompartilhadasChave($sql, $parametros);
        if (array_key_exists($chaveCompartilhada, $GLOBALS['dashboardConsultasCompartilhadas'])) {
            $erro = '';
            return $GLOBALS['dashboardConsultasCompartilhadas'][$chaveCompartilhada];
        }
    }
    return dashboardCargaAtiva() ? dashboardCargaConsultar($conexao,$sql,$parametros,$erro) : dashboardIndicadorConsultarDireto($conexao,$sql,$parametros,$erro);
}
function dashboardIndicadorErroLeitura($stmt)
{
    return function_exists('mysqli_stmt_errno') || function_exists(__NAMESPACE__ . '\\mysqli_stmt_errno') ? (int) mysqli_stmt_errno($stmt) : 0;
}

function dashboardIndicadorConsultarDireto($conexao, $sql, $parametros, &$erro)
{
    if (empty($GLOBALS['dashboardSql'])) return dashboardIndicadorConsultarDiretoInterno($conexao, $sql, $parametros, $erro);
    $inicio = microtime(true);
    unset($GLOBALS['dashboardSqlErroBanco']);
    $origem = array();
    foreach (debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 6) as $frame) {
        $origem[] = array('funcao' => isset($frame['function']) ? $frame['function'] : '',
            'arquivo' => isset($frame['file']) ? basename($frame['file']) : '',
            'linha' => isset($frame['line']) ? $frame['line'] : 0);
    }
    $linhas = false;
    $GLOBALS['dashboardSql']['emExecucao'] = array('sql' => $sql, 'parametros' => $parametros, 'inicio' => $inicio, 'origem' => $origem);
    dashboardSqlEtapa('prepararParametros');
    try {
        return $linhas = dashboardIndicadorConsultarDiretoInterno($conexao, $sql, $parametros, $erro);
    } catch (\Throwable $e) {
        $erro = 'Falha durante a execução da consulta: ' . get_class($e);
        throw $e;
    } finally {
        dashboardSqlRegistrar($sql, $parametros, $inicio, $linhas, $erro, $origem);
        unset($GLOBALS['dashboardSql']['emExecucao']);
    }
}

function dashboardIndicadorConsultarDiretoInterno($conexao, $sql, $parametros, &$erro)
{
    $GLOBALS['dashboardConsultaErroNumero'] = 0;
    $valores = array();
    $tipos = '';
    $sqlPreparado = dashboardIndicadorPrepararSql($sql, $parametros, $valores, $tipos, $erro);
    if ($erro !== '') {
        return false;
    }

    try {
    dashboardSqlEtapa('prepararBanco');
    $stmt = mysqli_prepare($conexao, $sqlPreparado);
    $vinculado = false; $executado = false;
    if ($stmt) {
        dashboardSqlEtapa('vincularParametros');
        $vinculado = dashboardIndicadorBind($stmt, $tipos, $valores);
        if ($vinculado) {
            dashboardSqlEtapa('executarBanco');
            $executado = mysqli_stmt_execute($stmt);
        }
    }
    if (!$stmt || !$vinculado || !$executado) {
        if (function_exists('mysqli_stmt_errno') || function_exists(__NAMESPACE__ . '\\mysqli_stmt_errno'))
            $GLOBALS['dashboardConsultaErroNumero'] = $stmt ? mysqli_stmt_errno($stmt) : mysqli_errno($conexao);
        $erroBanco = $stmt ? mysqli_stmt_error($stmt) : mysqli_error($conexao);
        dashboardSqlErroBanco($GLOBALS['dashboardConsultaErroNumero'], $erroBanco);
        error_log('Dashboard personalizado: ' . $erroBanco);
        $erro = 'Não foi possível executar a consulta configurada.';
        if ($stmt) {
            mysqli_stmt_close($stmt);
        }
        return false;
    }

    dashboardSqlEtapa('receberResultado');
    $resultado = mysqli_stmt_get_result($stmt);
    if ($resultado === false) {
        $GLOBALS['dashboardConsultaErroNumero'] = dashboardIndicadorErroLeitura($stmt);
        dashboardSqlErroBanco($GLOBALS['dashboardConsultaErroNumero'], mysqli_stmt_error($stmt));
        error_log('Dashboard personalizado: ' . mysqli_stmt_error($stmt));
        $erro = in_array($GLOBALS['dashboardConsultaErroNumero'], array(3024, 1969, 1317), true)
            ? 'A consulta ultrapassou o tempo limite. Reduza o período ou selecione menos empresas.'
            : 'Não foi possível ler o resultado da consulta configurada.';
        mysqli_stmt_close($stmt);
        return false;
    }
    if (dashboardCargaGeralAtiva() && dashboardCargaGeralLimitesObrigatorios() && mysqli_num_rows($resultado) > 20000) {
        mysqli_free_result($resultado);
        mysqli_stmt_close($stmt);
        $erro = 'Uma fonte excedeu 20.000 linhas. Reduza o período ou os filtros.';
        return false;
    }
    dashboardSqlEtapa('materializarLinhas');
    $linhas = mysqli_fetch_all($resultado, MYSQLI_ASSOC);
    if (!is_array($linhas) || dashboardIndicadorErroLeitura($stmt)) {
        $GLOBALS['dashboardConsultaErroNumero'] = dashboardIndicadorErroLeitura($stmt);
        dashboardSqlErroBanco($GLOBALS['dashboardConsultaErroNumero'], mysqli_stmt_error($stmt));
        error_log('Dashboard personalizado: ' . mysqli_stmt_error($stmt));
        $erro = 'Não foi possível concluir a leitura da consulta configurada.';
        mysqli_stmt_close($stmt);
        return false;
    }
    mysqli_stmt_close($stmt);
    return $linhas;
    } catch (\mysqli_sql_exception $e) {
        $GLOBALS['dashboardConsultaErroNumero'] = (int) $e->getCode();
        dashboardSqlErroBanco($e->getCode(), $e->getMessage(), method_exists($e, 'getSqlState') ? $e->getSqlState() : '');
        $erro = in_array((int) $e->getCode(), array(3024, 1969, 1317), true)
            ? 'A consulta ultrapassou o tempo limite. Reduza o período ou selecione menos empresas.'
            : 'Não foi possível executar a consulta configurada.';
        if (!empty($stmt)) mysqli_stmt_close($stmt);
        return false;
    }
}

// D021A é uma personalização: só consulta a tabela quando o JSON solicita.
function dashboardIndicadorRepresentantesCondicaoComercial($configuracao)
{
    return isset($configuracao['somenteComerciais']) && $configuracao['somenteComerciais'] === true
        ? " AND EXISTS (SELECT 1 FROM D021A WHERE D021A_D021_Id = D021_Id AND D021A_Flag_Comercial = 'C')"
        : '';
}

function dashboardIndicadorCarregarOpcoesFiltros($conexao, $filtros, $empresa, &$erro, $abrangenciaEmpresa = 'atual')
{
    $erro = '';
    if (!in_array($abrangenciaEmpresa, array('atual', 'todas'), true)
        && !preg_match('/^[1-9][0-9]{0,9}$/D', (string) $abrangenciaEmpresa)) {
        $erro = 'Abrangência de empresas inválida.';
        return false;
    }
    foreach ($filtros as &$filtro) {
        if (isset($filtro['opcoesCadastro']) && $filtro['opcoesCadastro'] === 'empresasGrupo') {
            $ids = isset($filtro['empresas']) ? $filtro['empresas'] : array();
            if (!is_array($ids) || !$ids || count($ids) > 30) {
                $erro = 'Configure as empresas do grupo.';
                return false;
            }
            foreach ($ids as $id) {
                if (!is_int($id) || $id <= 0) { $erro = 'Empresa inválida no grupo.'; return false; }
            }
            $linhas = dashboardFiltrosConsultar($conexao,
                "SELECT C004_Id AS valor, COALESCE(NULLIF(C004_Nome_Abreviado, ''), C004_Nome_Empresa) AS titulo
                 FROM C004 WHERE C004_Id IN (" . implode(',', $ids) . ") AND C004_Id <> :empresa
                 ORDER BY titulo, C004_Id LIMIT 30", array('empresa' => (int) $empresa), $erro);
            if ($linhas === false) return false;
            $filtro['opcoes'] = array(array('valor' => 'atual', 'titulo' => 'Empresa atual'));
            foreach ($linhas as $linha) {
                $filtro['opcoes'][] = array('valor' => (string) $linha['valor'], 'titulo' => (string) $linha['titulo']);
            }
            $filtro['opcoes'][] = array('valor' => 'todas', 'titulo' => 'Todas as empresas');
            unset($filtro['opcoesCadastro']);
            continue;
        }
        if (is_array($filtro) && isset($filtro['opcoesCadastro']) && $filtro['opcoesCadastro'] === 'representantesAtivos') {
            $linhas = dashboardFiltrosConsultar(
                $conexao,
                "SELECT D021_Id AS valor, D021_Nome_Empresa AS titulo FROM D021 WHERE D021_Ativo = 'S' AND NULLIF(D021_Nome_Empresa, '') IS NOT NULL"
                    . dashboardIndicadorRepresentantesCondicaoComercial($filtro) . " ORDER BY D021_Nome_Empresa",
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
            if (!in_array($parametro, array('empresa', 'abrangenciaEmpresa'), true)) {
                $erro = 'As opções dinâmicas do filtro só podem usar :empresa e :abrangenciaEmpresa.';
                return false;
            }
        }

        $linhas = dashboardFiltrosConsultar($conexao, $sql, array('empresa' => (int) $empresa, 'abrangenciaEmpresa' => $abrangenciaEmpresa), $erro);
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
            "SELECT D021_Id AS representanteId, D021_Nome_Empresa AS representante FROM D021 WHERE D021_Ativo = 'S' AND NULLIF(D021_Nome_Empresa, '') IS NOT NULL"
                . dashboardIndicadorRepresentantesCondicaoComercial($configuracao) . " ORDER BY D021_Nome_Empresa",
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
            "SELECT D021_Id AS representanteId, D021_Nome_Empresa AS representante FROM D021 WHERE D021_Ativo = 'S' AND NULLIF(D021_Nome_Empresa, '') IS NOT NULL"
                . dashboardIndicadorRepresentantesCondicaoComercial($configuracao) . " ORDER BY D021_Nome_Empresa",
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

function dashboardIndicadorCompararGrupos($conexao, $grupos, $configuracao, $sqlBase, $parametros, &$erro)
{
    $comparativo = $configuracao['comparativo'];
    $filtroAno = dashboardIndicadorIdentificador(isset($comparativo['filtroAno']) ? $comparativo['filtroAno'] : 'ano', 'ano');
    $filtroBase = dashboardIndicadorIdentificador(isset($comparativo['filtroAnoBase']) ? $comparativo['filtroAnoBase'] : 'anoComparativo', 'anoComparativo');
    if (!isset($parametros[$filtroAno], $parametros[$filtroBase])) { $erro = 'Informe os dois anos do comparativo.'; return false; }
    $ano = (int) $parametros[$filtroAno];
    $anoBase = (int) $parametros[$filtroBase];
    if ($ano < 1900 || $ano > 2100 || $anoBase < 1900 || $anoBase > 2100) { $erro = 'Ano inválido no comparativo.'; return false; }
    if (!$grupos) return array();
    $campoGrupo = dashboardIndicadorIdentificador($configuracao['campoGrupo'], 'grupo');
    $campoData = dashboardIndicadorIdentificador($configuracao['campoData'], 'data');
    $campoValor = dashboardIndicadorIdentificador($configuracao['campoValor'], 'valor');
    $parametrosBase = $parametros;
    $parametrosBase[$filtroAno] = $anoBase;
    $chaves = array();
    foreach ($grupos as $indice => &$grupo) {
        $chave = 'comparativoGrupo' . $indice;
        $chaves[] = ':' . $chave;
        $parametrosBase[$chave] = $grupo['grupo'];
        $grupo['anoAtual'] = $ano;
        $grupo['anoBase'] = $anoBase;
        $grupo['totalBase'] = 0.0;
        $grupo['serieBase'] = array();
    }
    unset($grupo);
    // O ranking permanece sendo o do ano principal; o segundo ano só compara esses clientes.
    $sql = "SELECT `{$campoGrupo}` AS grupo, DATE_FORMAT(`{$campoData}`, '%Y-%m-01') AS data, COALESCE(SUM(`{$campoValor}`), 0) AS valor"
        . " FROM ({$sqlBase}) AS dashboard_comparativo WHERE `{$campoGrupo}` IN (" . implode(', ', $chaves) . ')'
        . " GROUP BY `{$campoGrupo}`, DATE_FORMAT(`{$campoData}`, '%Y-%m-01') ORDER BY grupo, data";
    $linhas = dashboardIndicadorConsultar($conexao, $sql, $parametrosBase, $erro);
    if ($linhas === false) return false;
    foreach ($grupos as &$grupo) foreach ($linhas as $linha) {
        if ((string) $linha['grupo'] !== $grupo['grupo']) continue;
        $grupo['totalBase'] += (float) $linha['valor'];
        $grupo['serieBase'][] = array('data' => $linha['data'], 'valor' => (float) $linha['valor']);
    }
    unset($grupo);
    return $grupos;
}

function dashboardIndicadorDefinicaoPublica($indicador, $filtros)
{
    $abasLocais = dashboardPersonalizadoAbasComerciaisLocais($indicador);
    $listaPublica = isset($indicador['listagem']) ? $indicador['listagem'] : array();
    unset($listaPublica['sqlBase'], $listaPublica['permitirUnionAll']);
    unset($listaPublica['_barras']);
    unset($listaPublica['detalhamento']['sqlBase']);
    if (isset($listaPublica['impressaoMarcadas']) && is_array($listaPublica['impressaoMarcadas'])) {
        $listaPublica['impressaoMarcadas'] = array_intersect_key($listaPublica['impressaoMarcadas'], array_flip(array(
            'habilitada', 'campoChave', 'campoNome', 'textoBotao', 'observacao'
        )));
    }
    // Os títulos acompanham os anos aplicados, inclusive no detalhe e na exportação.
    $rotulosFiltros = array();
    foreach ($filtros as $filtro) {
        if (isset($filtro['id'], $filtro['valor']) && in_array($filtro['id'], array('ano', 'anoComparativo'), true)) {
            $rotulosFiltros['{' . $filtro['id'] . '}'] = (string) $filtro['valor'];
        }
    }
    $rotular = function ($valor) use (&$rotular, $rotulosFiltros) {
        if (!is_array($valor)) return $valor;
        foreach ($valor as $chave => &$item) {
            if ($chave === 'titulo' && is_string($item)) $item = strtr($item, $rotulosFiltros);
            elseif (is_array($item)) $item = $rotular($item);
        }
        unset($item);
        return $valor;
    };
    $listaPublica = $rotular($listaPublica);
    if (isset($listaPublica['detalhamento']['itens'])) unset($listaPublica['detalhamento']['itens']['sqlBase']);
    $grafico = isset($indicador['grafico']) ? $indicador['grafico'] : array();
    if (isset($grafico['tipo']) && $grafico['tipo'] === 'linhas_periodos') unset($grafico['sqlBase']);
    return array(
        'id' => $indicador['id'],
        'titulo' => $indicador['titulo'],
        'subtitulo' => isset($indicador['subtitulo']) ? $indicador['subtitulo'] : '',
        'icone' => isset($indicador['icone']) ? $indicador['icone'] : 'chart',
        'cor' => isset($indicador['cor']) ? $indicador['cor'] : 'blue',
        'apresentacao' => array('compacta' => !empty($indicador['apresentacao']['compacta']),
            'criterios' => isset($indicador['apresentacao']['criterios']) && is_string($indicador['apresentacao']['criterios']) ? $indicador['apresentacao']['criterios'] : ''),
        'painelCotacoes' => dashboardCotacoesHabilitado($indicador),
        'portalRepresentantes' => !empty($indicador['portalRepresentantes']['habilitado']),
        'abcClientesEmLotes' => dashboardAbcClientesLotesHabilitado($indicador),
        'carregamentoProgressivo' => dashboardCargaHabilitada($indicador),
        'limitarPeriodoProgressivo' => !dashboardCargaGeralHabilitada($indicador),
        'previsaoCaixa' => dashboardCaixaPrevisaoHabilitada($indicador),
        'controleCarteiraBancos' => dashboardCarteiraBancosHabilitado($indicador),
        'filtros' => $filtros,
        'card' => isset($indicador['card']) ? $indicador['card'] : array(),
        'geografia' => isset($indicador['geografia']) ? $indicador['geografia'] : array(),
        'composicao' => isset($indicador['composicao']) ? $indicador['composicao'] : array(),
        'grafico' => $grafico,
        'graficosPorGrupo' => isset($indicador['graficosPorGrupo']) ? $indicador['graficosPorGrupo'] : array(),
        'calendarios' => isset($indicador['calendarios']) && is_array($indicador['calendarios']) ? $indicador['calendarios'] : array(),
        'listagem' => $listaPublica,
        'comparativoRepresentantes' => isset($indicador['comparativoRepresentantes']) ? $indicador['comparativoRepresentantes'] : array(),
        'abasComerciaisLocais' => $abasLocais,
        'abasComerciaisSobDemanda' => $abasLocais && !empty($indicador['desempenhoComercial']['carregamentoProgressivo']),
        'abas' => isset($indicador['abas']) && is_array($indicador['abas']) ? array_map(function ($aba) use ($abasLocais, $filtros) {
            return array(
                'id' => isset($aba['id']) ? $aba['id'] : '',
                'titulo' => isset($aba['titulo']) ? $aba['titulo'] : '',
                'portalRepresentantes' => !empty($aba['portalRepresentantes']['habilitado']),
                'filtros' => $abasLocais ? $filtros : dashboardIndicadorFiltrosSemOpcoesDinamicas(isset($aba['filtros']) ? $aba['filtros'] : array()),
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

function dashboardIndicadorConsultarGraficoComparativo($conexao, $indicador, $empresa, $parametros, $periodoProjecao, $detalheGraficoSerie, $detalheGraficoDataInicio, $detalheGraficoDataFim, &$erro)
{
    $grafico = $indicador['grafico'];
    $graficoDetalhes = array();
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

    if (dashboardCaixaPrevisaoHabilitada($indicador)) {
        $previsaoCaixa = dashboardCaixaPrevisaoCarregar($conexao, $indicador, $empresa, $parametros, $erro);
        if ($previsaoCaixa === false) return false;
        $linhasGrafico = array_merge($linhasGrafico, $previsaoCaixa['linhas']);
    }
    if (dashboardSaldoCaixaHabilitado($indicador)) {
        $movimentos = dashboardSaldoCaixaMovimentos($conexao, $indicador, $parametros, $erro);
        if ($movimentos === false) return false;
        $linhasGrafico = array_merge($linhasGrafico, $movimentos);
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
        if (dashboardSaldoCaixaHabilitado($indicador)) {
            if (!array_key_exists('contaId', $linhaGrafico)) { $erro = 'Configure contaId nas fontes do fluxo para filtrar bancos e caixas.'; return false; }
            if (!dashboardSaldoCaixaAceita($indicador, $linhaGrafico)) {
                if ($linhaGrafico[$campoSerieGrafico] === 'pedidos' && isset($previsaoCaixa['simulacao'])) $previsaoCaixa['simulacao']['parcelasForaContas']++;
                continue;
            }
        }
        $dataProjetada = !empty($linhaGrafico['_dataBancaria']) ? substr($linhaGrafico['data'], 0, 10) : dashboardIndicadorProximoDiaUtil(
            isset($linhaGrafico[$campoDataGrafico]) ? $linhaGrafico[$campoDataGrafico] : '',
            $feriados,
            isset($linhaGrafico[$campoDiasUteis]) ? $linhaGrafico[$campoDiasUteis] : 0,
            $considerarFeriadosNacionais
        );
        $serieGrafico = isset($linhaGrafico[$campoSerieGrafico]) ? trim((string) $linhaGrafico[$campoSerieGrafico]) : '';
        if ($dataProjetada === '' || $serieGrafico === '') continue;
        if ($periodoProjecao !== null && ($dataProjetada < $periodoProjecao['dataInicio'] || $dataProjetada > $periodoProjecao['dataFim'])) continue;
        if ($serieGrafico === 'pedidos' && isset($previsaoCaixa['simulacao'])) {
            $previsaoCaixa['simulacao']['parcelasNoPeriodo']++;
            $previsaoCaixa['simulacao']['valorNoPeriodo'] += (float) $linhaGrafico[$campoValorGrafico];
        }
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
    return array('grafico' => $graficoResultado, 'graficoDetalhes' => $graficoDetalhes, 'previsaoCaixa' => isset($previsaoCaixa) ? $previsaoCaixa : null);
}

function dashboardIndicadorConsultarSecaoPaginada($conexao, $secao, $parametros, $entrada, &$erro)
{
    if (empty($secao['paginacao']['habilitada']) || empty($secao['somenteGrafico'])) {
        $erro = 'Este bloco não possui gráfico paginado.';
        return false;
    }
    $lista = array(
        'sqlBase' => isset($secao['sqlBase']) ? $secao['sqlBase'] : '',
        'permitirUnionAll' => !empty($secao['permitirUnionAll']),
        'paginacao' => $secao['paginacao'],
        'ordenacao' => isset($secao['ordenacao']) ? $secao['ordenacao'] : array('campo' => 'valor', 'direcao' => 'DESC'),
    );
    $entradaPagina = array('listaOffset' => isset($entrada['secaoOffset']) ? $entrada['secaoOffset'] : '0');
    return dashboardIndicadorConsultarListagemPaginada($conexao, $lista, $parametros, $entradaPagina, $erro);
}

function dashboardIndicadorConfigurarDetalheSecao($indicador, $entrada, &$erro)
{
    if (!isset($entrada['detalheSecao'])) return $indicador;
    $id = is_string($entrada['detalheSecao']) ? dashboardIndicadorIdentificador($entrada['detalheSecao']) : '';
    $grupo = isset($entrada['detalheGrupo']) && is_string($entrada['detalheGrupo']) ? trim($entrada['detalheGrupo']) : '';
    if ($id === '' || $grupo === '' || strlen($grupo) > 200) { $erro = 'Informe um produto e um bloco válidos.'; return false; }
    foreach (isset($indicador['secoes']) ? $indicador['secoes'] : array() as $secao) {
        if (!isset($secao['id']) || $secao['id'] !== $id) continue;
        $grafico = isset($secao['grafico']) ? $secao['grafico'] : array();
        $detalhe = isset($grafico['detalhamento']) ? $grafico['detalhamento'] : array();
        if (empty($grafico['habilitado']) || empty($detalhe['habilitado']) || empty($detalhe['modal'])) break;
        $idsFiltros = array_column(isset($indicador['filtros']) ? $indicador['filtros'] : array(), 'id');
        foreach (isset($detalhe['filtros']) ? $detalhe['filtros'] : array() as $filtro) {
            if (!isset($filtro['id']) || in_array($filtro['id'], $idsFiltros, true)) { $erro = 'Filtro de detalhamento inválido ou duplicado.'; return false; }
            $indicador['filtros'][] = $filtro;
            $idsFiltros[] = $filtro['id'];
        }
        $indicador['listagem'] = array('habilitada' => true, 'detalhamento' => $detalhe);
        $indicador['_detalheSecao'] = $id;
        return $indicador;
    }
    $erro = 'O bloco selecionado não possui detalhamento habilitado.';
    return false;
}

function dashboardIndicadorExecutar($conexao, $indicador, $empresa, $entrada, &$erro)
{
    $anterior = isset($GLOBALS['dashboardConsultasCompartilhadas']) ? $GLOBALS['dashboardConsultasCompartilhadas'] : null;
    unset($GLOBALS['dashboardConsultasCompartilhadas']);
    try { return dashboardIndicadorExecutarInterno($conexao, $indicador, $empresa, $entrada, $erro); }
    finally {
        if ($anterior === null) unset($GLOBALS['dashboardConsultasCompartilhadas']);
        else $GLOBALS['dashboardConsultasCompartilhadas'] = $anterior;
    }
}

function dashboardIndicadorExecutarInterno($conexao, $indicador, $empresa, $entrada, &$erro)
{
    $erro = '';
    $indicador = dashboardIndicadorConfigurarDetalheSecao($indicador, $entrada, $erro);
    if ($indicador === false) return false;
    if (dashboardAbcClientesHabilitado($indicador)) {
        return dashboardAbcClientesExecutar($conexao, $indicador, $empresa, $entrada, $erro);
    }
    if (dashboardCotacoesHabilitado($indicador)) {
        return dashboardCotacoesExecutar($conexao, $indicador, $empresa, $entrada, $erro);
    }
    if (dashboardCarteiraBancosHabilitado($indicador)) {
        return dashboardCarteiraBancosExecutar($conexao, $indicador, $empresa, $entrada, $erro);
    }
    if (dashboardClientesInativosHabilitado($indicador)) {
        $indicador = dashboardClientesInativosConfigurar($indicador, $entrada);
    }
    $sqlBase = dashboardPersonalizadoSqlTexto(isset($indicador['sqlBase']) ? $indicador['sqlBase'] : '');
    if (!dashboardIndicadorValidarSql($sqlBase, $erro, !empty($indicador['multiempresa']), !empty($indicador['permitirUnionAll']))) {
        return false;
    }

    $filtrosOriginais = isset($indicador['filtros']) ? $indicador['filtros'] : array();
    $abrangenciaEmpresa = 'atual';
    foreach ($filtrosOriginais as $indice => $filtro) {
        if (!isset($filtro['id']) || $filtro['id'] !== 'abrangenciaEmpresa') continue;
        if (isset($filtro['opcoesCadastro']) && $filtro['opcoesCadastro'] === 'empresasGrupo') {
            $carregado = dashboardIndicadorCarregarOpcoesFiltros($conexao, array($filtro), $empresa, $erro);
            if ($carregado === false) return false;
            $filtro = $carregado[0];
            $filtrosOriginais[$indice] = $filtro;
        }
        $errosAbrangencia = array();
        $abrangencia = dashboardIndicadorFiltros(array('filtros' => array($filtro)), $entrada, $errosAbrangencia);
        if ($errosAbrangencia) { $erro = implode(' ', $errosAbrangencia); return false; }
        $abrangenciaEmpresa = $abrangencia['valores']['abrangenciaEmpresa'];
    }
    $filtrosConfigurados = dashboardIndicadorCarregarOpcoesFiltros($conexao, $filtrosOriginais, $empresa, $erro, $abrangenciaEmpresa);
    if ($filtrosConfigurados === false) {
        return false;
    }
    $indicador['filtros'] = $filtrosConfigurados;

    $errosFiltros = array();
    $filtros = dashboardIndicadorFiltros($indicador, $entrada, $errosFiltros);
    if (!empty($errosFiltros) && !empty($GLOBALS['dashboardFiltrosCache']['usado'])) {
        // Um cadastro recém-criado pode ser válido, embora ainda não esteja no cache.
        $GLOBALS['dashboardFiltrosCache']['ignorar'] = true;
        try { $novosFiltros = dashboardIndicadorCarregarOpcoesFiltros($conexao, $filtrosOriginais, $empresa, $erro, $abrangenciaEmpresa); }
        finally { $GLOBALS['dashboardFiltrosCache']['ignorar'] = false; }
        if ($novosFiltros === false) return false;
        $indicador['filtros'] = $novosFiltros;
        $filtros = dashboardIndicadorFiltros($indicador, $entrada, $errosFiltros);
    }
    if (!empty($errosFiltros)) {
        $erro = implode(' ', $errosFiltros);
        return false;
    }
    if (dashboardSaldoCaixaHabilitado($indicador) && !dashboardSaldoCaixaPreparar($conexao, $indicador, $empresa, $filtros, $erro)) return false;
    if (!empty($indicador['desempenhoComercial']['habilitado'])) {
        return dashboardComercialExecutar($conexao, $indicador, $empresa, $filtros, $erro, $entrada);
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
    $detalheMes = dashboardIndicadorMesDetalhamento($indicador, $entrada, $erro);
    if ($detalheMes === false) return false;
    $parametros['detalheMes'] = $detalheMes;
    if ($detalheMes > 0) {
        $nomesMeses = array(1 => 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro');
        $filtros['publicos'][] = array('id' => 'detalheMes', 'titulo' => 'Mês das notas', 'tipo' => 'texto', 'valor' => $nomesMeses[$detalheMes]);
    }
    if (isset($entrada['detalheNota'])) {
        $itens = dashboardIndicadorConsultarItensDetalhamento($conexao, $indicador, $entrada, $parametros, $erro);
        if ($itens === false) return false;
        return array('itensNota' => $itens);
    }
    if (!empty($indicador['grafico']['saldoAcumulado']['habilitado'])) {
        $indicador['grafico']['saldoAcumulado']['saldoInicial'] = $parametros['saldoInicial'];
    }
    if (isset($entrada['detalheBloco'])) {
        return dashboardConferenciaExecutar($conexao, $indicador, $parametros, $entrada, $filtros['publicos'], $erro);
    }
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

    $indicador = dashboardBarrasEmpilhadasPreparar($indicador, $entrada, $parametros, $erro);
    if ($indicador === false) return false;

    if (isset($entrada['somenteSecao'])) {
        $idSecao = is_string($entrada['somenteSecao']) ? dashboardIndicadorIdentificador($entrada['somenteSecao']) : '';
        foreach (isset($indicador['secoes']) ? $indicador['secoes'] : array() as $secao) {
            if ($idSecao === '' || !isset($secao['id']) || $secao['id'] !== $idSecao) continue;
            $pagina = dashboardIndicadorConsultarSecaoPaginada($conexao, $secao, $parametros, $entrada, $erro);
            if ($pagina === false) return false;
            return array('secoes' => array(array('id' => $idSecao, 'linhas' => $pagina['linhas'],
                'paginacao' => array_merge($secao['paginacao'], $pagina['paginacao']))));
        }
        $erro = 'Bloco inválido para carregar mais produtos.';
        return false;
    }
    if (isset($entrada['somenteListagem']) && $entrada['somenteListagem'] === '1') {
        if (!dashboardIndicadorListagemPaginadaHabilitada($indicador)) { $erro = 'Este indicador não possui tabela paginada.'; return false; }
        $pagina = dashboardIndicadorConsultarListagemPaginada($conexao, $indicador['listagem'], $parametros, $entrada, $erro);
        if ($pagina === false) return false;
        return array('indicador' => dashboardIndicadorDefinicaoPublica($indicador, $filtros['publicos']),
            'listagem' => $pagina['linhas'], 'listagemPaginacao' => $pagina['paginacao']);
    }
    if (!empty($indicador['_detalheSecao']) || dashboardIndicadorPodeDetalharDireto($indicador, $entrada)) {
        $lista = $indicador['listagem'];
        $campoPadrao = isset($lista['agrupamento']['campo']) ? $lista['agrupamento']['campo'] : 'descricao';
        $limite = isset($lista['limite']) ? max(1, min(500, (int) $lista['limite'])) : 200;
        $detalhes = dashboardIndicadorConsultarDetalhamento($conexao, $lista['detalhamento'], $campoPadrao, $limite, $parametros, $erro);
        if ($detalhes === false) return false;
        $resultado = dashboardIndicadorResultadoDetalhe($indicador, $filtros['publicos']);
        $resultado['listagemDetalhes'] = $detalhes;
        return $resultado;
    }
    $tipoDetalheDireto = dashboardIndicadorDetalheVisualDireto($indicador, $entrada);
    if ($tipoDetalheDireto !== '') {
        $resultado = dashboardIndicadorResultadoDetalhe($indicador, $filtros['publicos']);
        if ($tipoDetalheDireto === 'calendario') {
            foreach ($indicador['calendarios'] as $calendario) {
                if (!isset($calendario['id']) || $calendario['id'] !== $detalheCalendarioId) continue;
                $campoCard = dashboardIndicadorIdentificador(isset($indicador['card']['campo']) ? $indicador['card']['campo'] : 'valor', 'valor');
                $detalhes = dashboardIndicadorConsultarDetalheCalendario($conexao, $sqlBase, $calendario, $campoCard, dashboardIndicadorParametrosCalendario($calendario, $parametros), $detalheCalendarioData, $erro);
                if ($detalhes === false) return false;
                $resultado['calendarioDetalhes'] = $detalhes;
                break;
            }
        } else {
            // As mesmas fontes e o mesmo período preservam projeções e dias úteis.
            $detalhes = dashboardIndicadorConsultarGraficoComparativo($conexao, $indicador, $empresa, $parametros, $periodoProjecao, $detalheGraficoSerie, $detalheGraficoDataInicio, $detalheGraficoDataFim, $erro);
            if ($detalhes === false) return false;
            $resultado['graficoDetalhes'] = $detalhes['graficoDetalhes'];
        }
        return $resultado;
    }
    $card = isset($indicador['card']) && is_array($indicador['card']) ? $indicador['card'] : array();
    $calculo = isset($card['calculo']) ? $card['calculo'] : 'soma';
    $campoCard = dashboardIndicadorIdentificador(isset($card['campo']) ? $card['campo'] : 'valor', 'valor');
    $agregacao = $calculo === 'contagem' ? 'COUNT(*)' : ($calculo === 'media' ? 'AVG(`' . $campoCard . '`)' : 'SUM(`' . $campoCard . '`)');
    $resumos = isset($indicador['resumos']) && is_array($indicador['resumos']) ? array_slice($indicador['resumos'], 0, 8) : array();
    $sqlResumos = '';
    foreach ($resumos as $indice => $resumo) {
        $campoResumo = dashboardIndicadorIdentificador(isset($resumo['campo']) ? $resumo['campo'] : '');
        if ($campoResumo === '') { $erro = 'Campo inválido no resumo do indicador.'; return false; }
        $sqlResumos .= ', ' . (!empty($resumo['preservarNulo']) ? 'SUM(`' . $campoResumo . '`)' : 'COALESCE(SUM(`' . $campoResumo . '`), 0)') . ' AS resumo' . $indice;
    }
    $campoContagemCard = dashboardIndicadorIdentificador(isset($card['campoContagem']) ? $card['campoContagem'] : '', '');
    $contagemCard = $campoContagemCard !== '' ? 'COUNT(DISTINCT `' . $campoContagemCard . '`)' : 'COUNT(*)';
    $sqlCard = 'SELECT ' . $contagemCard . ' AS quantidade, COALESCE(' . $agregacao . ', 0) AS valor' . $sqlResumos . ' FROM (' . $sqlBase . ') AS dashboard_base';
    $fonteCompartilhada = null;
    if ($detalheGrupo === '' && $faixaPareto === null && $classePareto === null && $ufMapa === '') {
        $fonteCompartilhada = dashboardFonteCompartilhadaConsultar($conexao, $indicador, $sqlCard, $parametros, $erro);
        if ($fonteCompartilhada === false) return false;
    }
    if (!empty($indicador['consultasCompartilhadas']) && $detalheGrupo === '' && $faixaPareto === null && $classePareto === null && $ufMapa === '') {
        require_once __DIR__ . '/consultas-compartilhadas.php';
        if (!dashboardConsultasCompartilhadasPreparar($conexao, $indicador, $sqlBase, $sqlCard, $parametros, $erro)) return false;
    }
    $baseCompartilhada = null;
    if (!empty($indicador['baseCompartilhada']['habilitada']) && $detalheGrupo === '' && $faixaPareto === null && $classePareto === null && $ufMapa === '') {
        require_once __DIR__ . '/base-compartilhada.php';
        $baseCompartilhada = dashboardBaseCompartilhadaConsultar($conexao, $indicador, $sqlBase, $sqlCard, $parametros, $erro);
        if ($baseCompartilhada === false) return false;
    }
    $linhasCard = $fonteCompartilhada !== null ? array($fonteCompartilhada['card'])
        : ($baseCompartilhada === null ? dashboardIndicadorConsultar($conexao, $sqlCard, $parametros, $erro) : array($baseCompartilhada['card']));
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
    } elseif (dashboardBarrasEmpilhadasHabilitado($indicador)) {
        $graficoResultado = dashboardBarrasEmpilhadasConsultar($conexao, $sqlBase, $grafico, $parametros, $erro);
        if ($graficoResultado === false) return false;
    } elseif (!empty($grafico) && (!isset($grafico['habilitado']) || $grafico['habilitado']) && isset($grafico['tipo']) && $grafico['tipo'] === 'linhas_periodos') {
        $graficoResultado = dashboardIndicadorConsultarComparativoPeriodos($conexao, $grafico, $parametros, $erro);
        if ($graficoResultado === false) return false;
    } elseif (!empty($grafico) && (!isset($grafico['habilitado']) || $grafico['habilitado']) && isset($grafico['tipo']) && $grafico['tipo'] === 'barras_comparativas') {
        $resultadoGrafico = dashboardIndicadorConsultarGraficoComparativo($conexao, $indicador, $empresa, $parametros, $periodoProjecao, $detalheGraficoSerie, $detalheGraficoDataInicio, $detalheGraficoDataFim, $erro);
        if ($resultadoGrafico === false) return false;
        $graficoResultado = $resultadoGrafico['grafico'];
        $graficoDetalhes = $resultadoGrafico['graficoDetalhes'];
        $previsaoCaixa = $resultadoGrafico['previsaoCaixa'];
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
        if (!empty($graficosPorGrupo['comparativo']['habilitado'])) {
            $graficosPorGrupoResultado = dashboardIndicadorCompararGrupos($conexao, $graficosPorGrupoResultado, $graficosPorGrupo, $sqlBase, $parametros, $erro);
            if ($graficosPorGrupoResultado === false) return false;
        }
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
        $parametrosCalendario = dashboardIndicadorParametrosCalendario($calendario, $parametros);
        $resultadoCalendario = dashboardIndicadorConsultar($conexao, $sqlCalendario, $parametrosCalendario, $erro);
        if ($resultadoCalendario === false) {
            return false;
        }
        $calendariosResultado[] = $resultadoCalendario;

        $calendarioId = isset($calendario['id']) ? (string) $calendario['id'] : '';
        $detalhamentoCalendario = isset($calendario['detalhamento']) && is_array($calendario['detalhamento']) ? $calendario['detalhamento'] : array();
        if ($detalheCalendarioId !== '' && $calendarioId === $detalheCalendarioId && !empty($detalhamentoCalendario) && (!isset($detalhamentoCalendario['habilitado']) || $detalhamentoCalendario['habilitado'])) {
            $calendarioDetalhes = dashboardIndicadorConsultarDetalheCalendario($conexao, $sqlBase, $calendario, $campoCard, $parametrosCalendario, $detalheCalendarioData, $erro);
            if ($calendarioDetalhes === false) {
                return false;
            }
        }
    }

    $listagemResultado = array();
    $listagemDetalhes = array();
    $listagemPaginacao = null;
    $listagem = isset($indicador['listagem']) && is_array($indicador['listagem']) ? $indicador['listagem'] : array();
    $limiteDetalhes = isset($listagem['limite']) ? max(1, min(500, (int) $listagem['limite'])) : 200;
    if (dashboardIndicadorListagemPaginadaHabilitada($indicador)) {
        $completa = defined('HARDNESS_DASHBOARD_EXPORTAR_LISTAGEM_COMPLETA') && HARDNESS_DASHBOARD_EXPORTAR_LISTAGEM_COMPLETA;
        $pagina = dashboardIndicadorConsultarListagemPaginada($conexao, $listagem, $parametros, array('listaOffset' => '0'), $erro, $completa);
        if ($pagina === false) return false;
        $listagemResultado = $pagina['linhas'];
        $listagemPaginacao = $pagina['paginacao'];
    } elseif (!empty($listagem) && (!isset($listagem['habilitada']) || $listagem['habilitada'])) {
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
        $listagemResultado = $baseCompartilhada === null ? dashboardIndicadorConsultar($conexao, $sqlListagem, $parametros, $erro) : $baseCompartilhada['listagem'];
        if ($listagemResultado === false) {
            return false;
        }
        if (!empty($agrupamento)) foreach ($listagemResultado as &$linha) $linha['percentual'] = (float) $resultadoCard['valor'] > 0 ? ((float) $linha['valor'] / (float) $resultadoCard['valor']) * 100 : 0;
        $limiteDetalhes = $limite;
    }
    if (!empty($listagem) && (!isset($listagem['habilitada']) || $listagem['habilitada'])) {
        $detalhamento = isset($listagem['detalhamento']) && is_array($listagem['detalhamento']) ? $listagem['detalhamento'] : array();
        $sqlDetalhamentoBase = dashboardPersonalizadoSqlTexto(isset($detalhamento['sqlBase']) ? $detalhamento['sqlBase'] : '');
        $detalhamentoModal = !empty($detalhamento['modal']);
        if ($sqlDetalhamentoBase !== '' && (!isset($detalhamento['habilitado']) || $detalhamento['habilitado']) && (!$detalhamentoModal || $detalheGrupo !== '')) {
            $listagemDetalhes = dashboardIndicadorConsultarDetalhamento($conexao, $detalhamento, isset($campoAgrupamento) ? $campoAgrupamento : 'descricao', $limiteDetalhes, $parametros, $erro);
            if ($listagemDetalhes === false) return false;
        }
    }

    if (dashboardClientesInativosHabilitado($indicador)) {
        $listagemResultado = dashboardClientesInativosEnriquecer($conexao, $empresa, $listagemResultado, $erro);
        if ($listagemResultado === false) return false;
    }

    $resumosResultado = array();
    foreach ($resumos as $indice => $resumo) {
        $resumosResultado[] = array(
            'titulo' => isset($resumo['titulo']) ? (string) $resumo['titulo'] : '',
            'descricao' => isset($resumo['descricao']) ? (string) $resumo['descricao'] : '',
            'formato' => dashboardIndicadorFormatoPermitido(isset($resumo['formato']) ? $resumo['formato'] : 'numero'),
            'conferencia' => isset($resumo['conferencia']) ? $resumo['conferencia'] : '',
            'textoNulo' => isset($resumo['textoNulo']) ? (string) $resumo['textoNulo'] : '',
            'valor' => !empty($resumo['preservarNulo']) && (!isset($resultadoCard['resumo' . $indice]))
                ? null : (isset($resultadoCard['resumo' . $indice]) ? (float) $resultadoCard['resumo' . $indice] : 0),
        );
    }
    $secoesResultado = array();
    $secoes = isset($indicador['secoes']) && is_array($indicador['secoes']) ? $indicador['secoes'] : array();
    if (count($secoes) > 6) { $erro = 'O indicador aceita até seis blocos.'; return false; }
    if ($detalheGrupo === '') foreach ($secoes as $secao) {
        $sqlSecao = dashboardPersonalizadoSqlTexto(isset($secao['sqlBase']) ? $secao['sqlBase'] : '');
        if (!dashboardIndicadorValidarSql($sqlSecao, $erro, false, !empty($secao['permitirUnionAll']))) return false;
        preg_match_all('/:([a-z][a-zA-Z0-9_]*)/', $sqlSecao, $encontradosSecao);
        foreach (array_unique($encontradosSecao[1]) as $parametroSecao) {
            if (!array_key_exists($parametroSecao, $parametros)) { $erro = 'Filtro não configurado no bloco: ' . $parametroSecao; return false; }
        }
        $colunasSecao = isset($secao['colunas']) && is_array($secao['colunas']) ? $secao['colunas'] : array();
        $camposSecao = array();
        foreach ($colunasSecao as $colunaSecao) {
            $campoSecao = dashboardIndicadorIdentificador(isset($colunaSecao['campo']) ? $colunaSecao['campo'] : '');
            if ($campoSecao === '') { $erro = 'Coluna inválida no bloco.'; return false; }
            $camposSecao[] = '`' . $campoSecao . '`';
        }
        if (empty($camposSecao)) { $erro = 'Informe as colunas do bloco.'; return false; }
        $secaoId = isset($secao['id']) ? $secao['id'] : '';
        $paginacaoSecao = array();
        if (!empty($secao['paginacao']['habilitada'])) {
            $paginaSecao = dashboardIndicadorConsultarSecaoPaginada($conexao, $secao, $parametros, array(), $erro);
            if ($paginaSecao === false) return false;
            $linhasSecao = $paginaSecao['linhas'];
            $paginacaoSecao = array_merge($secao['paginacao'], $paginaSecao['paginacao']);
        } else {
            $linhasSecao = $fonteCompartilhada !== null && isset($fonteCompartilhada['secoes'][$secaoId])
                ? $fonteCompartilhada['secoes'][$secaoId]
                : ($baseCompartilhada !== null && isset($baseCompartilhada['secoes'][$secaoId])
                    ? $baseCompartilhada['secoes'][$secaoId]
                    : dashboardIndicadorConsultar($conexao, 'SELECT ' . implode(', ', $camposSecao) . ' FROM (' . $sqlSecao . ') AS dashboard_bloco LIMIT 501', $parametros, $erro));
        }
        if ($linhasSecao === false) return false;
        if (count($linhasSecao) > 500) { $erro = 'O bloco excedeu 500 linhas. Reduza o período.'; return false; }
        $graficoSecaoPublico = isset($secao['grafico']) && is_array($secao['grafico']) ? $secao['grafico'] : array();
        unset($graficoSecaoPublico['detalhamento']['sqlBase']);
        $secoesResultado[] = array(
            'id' => dashboardIndicadorIdentificador(isset($secao['id']) ? $secao['id'] : ''),
            'titulo' => isset($secao['titulo']) ? (string) $secao['titulo'] : '',
            'subtitulo' => isset($secao['subtitulo']) ? (string) $secao['subtitulo'] : '',
            'somenteGrafico' => !empty($secao['somenteGrafico']),
            'larguraTotal' => !empty($secao['larguraTotal']),
            'grafico' => $graficoSecaoPublico,
            'conferencia' => isset($secao['conferencia']) ? $secao['conferencia'] : array(),
            'colunas' => $colunasSecao,
            'rodapeTotais' => isset($secao['rodapeTotais']) ? $secao['rodapeTotais'] : array(),
            'linhas' => $linhasSecao,
            'paginacao' => $paginacaoSecao,
        );
    }
    $definicaoPublica = dashboardIndicadorDefinicaoPublica($indicador, $filtros['publicos']);
    return array(
        'indicador' => $definicaoPublica,
        'resumos' => $resumosResultado,
        'secoes' => $secoesResultado,
        'card' => array(
            'valor' => isset($resultadoCard['valor']) ? (float) $resultadoCard['valor'] : 0,
            'quantidade' => isset($resultadoCard['quantidade']) ? (int) $resultadoCard['quantidade'] : 0,
            'formato' => dashboardIndicadorFormatoPermitido(isset($card['formato']) ? $card['formato'] : 'numero'),
        ),
        'geografia' => $geografiaResultado,
        'composicao' => $composicaoResultado,
        'grafico' => $graficoResultado,
        'graficoDetalhes' => $graficoDetalhes,
        'previsaoCaixa' => isset($previsaoCaixa) ? array('lancamentos' => $previsaoCaixa['lancamentos'], 'pedidosPendentes' => $previsaoCaixa['pedidosPendentes'], 'simulacao' => array_merge($previsaoCaixa['simulacao'], array('mensagem' => dashboardCaixaPrevisaoMensagem($previsaoCaixa['simulacao'])))) : null,
        'graficosPorGrupo' => $graficosPorGrupoResultado,
        'calendarios' => $calendariosResultado,
        'calendarioDetalhes' => $calendarioDetalhes,
        'listagem' => $listagemResultado,
        'listagemPaginacao' => $listagemPaginacao,
        'paretoClasse' => $classePareto === null ? null : array('classe' => $classePareto, 'quantidade' => $quantidadeClassePareto),
        'listagemDetalhes' => $listagemDetalhes,
        'comparativoRepresentantes' => $comparativoRepresentantes,
    );
}
