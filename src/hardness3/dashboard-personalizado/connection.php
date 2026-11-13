<?php
namespace hardness;

function dashboardPersonalizadoIndicadorUsaEspelho($indicador)
{
    return empty($indicador['saldoCaixa']['habilitado']) || empty($indicador['saldoCaixa']['usarSaldoAtual']);
}

/*
 * Mantém a configuração principal intacta: ela também identifica o cliente,
 * seus arquivos, caches e assinaturas de autorização do dashboard.
 */
function dashboardPersonalizadoConectarLeitura($confUsuario, $confUsuarioEspelhado = null, $permitirEspelho = true)
{
    $configuracoes = array($confUsuario);
    $espelhoValido = $permitirEspelho && is_array($confUsuarioEspelhado);
    if ($espelhoValido) {
        foreach (array('dbHost', 'dbUser', 'dbPass', 'dbDatabase') as $campo) {
            if (!isset($confUsuarioEspelhado[$campo])
                || !is_string($confUsuarioEspelhado[$campo])
                || ($campo !== 'dbPass' && trim($confUsuarioEspelhado[$campo]) === '')) {
                $espelhoValido = false;
                break;
            }
        }
    }
    // Um espelho deve pertencer à mesma base do cliente autenticado.
    if ($espelhoValido && $confUsuarioEspelhado['dbDatabase'] === $confUsuario['dbDatabase']) {
        array_unshift($configuracoes, $confUsuarioEspelhado);
    }

    foreach ($configuracoes as $configuracao) {
        if (!empty($GLOBALS['dashboardSql'])) {
            $relatorio =& $GLOBALS['dashboardSql']['relatorio'];
            $relatorio['tentativasConexao'] = isset($relatorio['tentativasConexao']) ? $relatorio['tentativasConexao'] + 1 : 1;
        }
        $conexao = false;
        try {
            $conexao = mysqli_init();
            if (!$conexao) continue;
            if (mysqli_options($conexao, MYSQLI_OPT_CONNECT_TIMEOUT, 5)
                && @mysqli_real_connect($conexao, $configuracao['dbHost'], $configuracao['dbUser'], $configuracao['dbPass'], $configuracao['dbDatabase'])) {
                if (!empty($GLOBALS['dashboardSql'])) $GLOBALS['dashboardSql']['relatorio']['conexao'] =
                    $espelhoValido && $configuracao === $confUsuarioEspelhado ? 'espelho' : 'principal';
                if (!empty($GLOBALS['dashboardSql']) && function_exists('mysqli_get_server_info'))
                    $GLOBALS['dashboardSql']['relatorio']['mysqlVersao'] = dashboardSqlTexto(mysqli_get_server_info($conexao), 100);
                return $conexao;
            }
        } catch (\Throwable $e) {
            // Compatível com mysqli que retorna false ou lança exceção.
            // Não expor nem registrar mensagens que possam conter credenciais.
        }
        if ($conexao) {
            try {
                @mysqli_close($conexao);
            } catch (\Throwable $e) {
                // A conexão pode já ter sido fechada pelo driver.
            }
        }
    }

    return false;
}
