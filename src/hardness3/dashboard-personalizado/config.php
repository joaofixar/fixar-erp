<?php
namespace hardness;

require_once __DIR__ . '/session.php';

function dashboardPersonalizadoArquivos($empresaId)
{
    global $g, $confUsuario;

    $empresaId = (int) $empresaId;
    if ($empresaId < 0) {
        return array();
    }

    $prefixoEmpresa = $empresaId > 0 ? (string) $empresaId : '0';

    $pathDados = !empty($g['pathDados'])
        ? $g['pathDados']
        : 'dados_usuarios/' . (isset($confUsuario['dbDatabase']) ? $confUsuario['dbDatabase'] : '') . '/';

    if ($pathDados === 'dados_usuarios//') {
        return array();
    }

    if ($pathDados[0] !== '/' && !empty($confUsuario['pathRaiz'])) {
        $pathDados = rtrim($confUsuario['pathRaiz'], '/\\') . '/' . $pathDados;
    }

    $diretorio = rtrim($pathDados, '/\\') . '/personalizacaoConf/';
    return array_values(array_unique(array(
        $diretorio . $prefixoEmpresa . 'DASHBOARD PERSONALIZAVEL-alterarDashboard.json',
        $diretorio . $prefixoEmpresa . 'DASHBOARD+PERSONALIZAVEL-alterarDashboard.json',
        $diretorio . $prefixoEmpresa . 'DASHBOARD-PERSONALIZAVEL-alterarDashboard.json',
    )));
}

/*
 * Prioridade: personalização da empresa, personalização geral do grupo e,
 * somente quando nenhuma delas existir, configuração padrão versionada.
 */
function dashboardPersonalizadoEmpresasCandidatas($empresaId)
{
    $empresaId = (int) $empresaId;
    $empresas = $empresaId > 0 ? array($empresaId) : array();

    if ($empresaId !== 0) {
        $empresas[] = 0;
    }

    return array_values(array_unique($empresas));
}

function dashboardPersonalizadoArquivo($empresaId)
{
    $arquivos = dashboardPersonalizadoArquivos($empresaId);

    return isset($arquivos[0]) ? $arquivos[0] : '';
}

function dashboardPersonalizadoArquivoPadrao()
{
    return dirname(__DIR__) . '/configuracoes/dashboard-personalizavel.json';
}

function dashboardPersonalizadoSqlTexto($sqlBase)
{
    if (is_array($sqlBase)) {
        return trim(implode("\n", array_map('strval', $sqlBase)));
    }

    return is_string($sqlBase) ? trim($sqlBase) : '';
}

function dashboardPersonalizadoValidarConfiguracao($configuracao, &$erros = array(), $exigirHabilitado = false)
{
    $erros = array();
    if (!is_array($configuracao)) {
        $erros[] = 'A configuração deve ser um objeto JSON.';
        return false;
    }

    if (!isset($configuracao['habilitado']) || !is_bool($configuracao['habilitado'])) {
        $erros[] = 'O campo "habilitado" deve ser true ou false.';
    }

    if (array_key_exists('acessoDiretorAutomatico', $configuracao) && !is_bool($configuracao['acessoDiretorAutomatico'])) {
        $erros[] = 'O campo "acessoDiretorAutomatico" deve ser true ou false.';
    }

    if (!isset($configuracao['indicadores']) || !is_array($configuracao['indicadores'])) {
        $erros[] = 'O campo "indicadores" deve ser uma lista.';
        return false;
    }

    $ids = array();
    $ativos = 0;
    foreach ($configuracao['indicadores'] as $indice => $indicador) {
        $prefixo = 'Indicador ' . ($indice + 1) . ': ';
        if (!is_array($indicador)) {
            $erros[] = $prefixo . 'a definição deve ser um objeto.';
            continue;
        }

        $id = isset($indicador['id']) ? (string) $indicador['id'] : '';
        if (!preg_match('/^[a-z][a-zA-Z0-9_]{1,49}$/', $id)) {
            $erros[] = $prefixo . 'id inválido.';
        } elseif (isset($ids[$id])) {
            $erros[] = $prefixo . 'id duplicado.';
        } else {
            $ids[$id] = true;
        }

        $habilitado = !isset($indicador['habilitado']) || $indicador['habilitado'] === true;
        if (!$habilitado) {
            continue;
        }
        $ativos++;

        if (empty($indicador['titulo']) || !is_string($indicador['titulo'])) {
            $erros[] = $prefixo . 'informe o título.';
        }
        $possuiAbas = isset($indicador['abas']) && is_array($indicador['abas']) && !empty($indicador['abas']);
        if (!$possuiAbas && dashboardPersonalizadoSqlTexto(isset($indicador['sqlBase']) ? $indicador['sqlBase'] : '') === '') {
            $erros[] = $prefixo . 'informe o sqlBase.';
        }

        $filtros = isset($indicador['filtros']) ? $indicador['filtros'] : array();
        if (!is_array($filtros)) {
            $erros[] = $prefixo . 'filtros deve ser uma lista.';
            continue;
        }

        $filtrosIds = array();
        foreach ($filtros as $filtroIndice => $filtro) {
            if (!is_array($filtro)) {
                $erros[] = $prefixo . 'filtro ' . ($filtroIndice + 1) . ' inválido.';
                continue;
            }
            $filtroId = isset($filtro['id']) ? (string) $filtro['id'] : '';
            $tipo = isset($filtro['tipo']) ? (string) $filtro['tipo'] : 'texto';
            if (!preg_match('/^[a-z][a-zA-Z0-9_]{1,39}$/', $filtroId) || isset($filtrosIds[$filtroId])) {
                $erros[] = $prefixo . 'filtro com id inválido ou duplicado.';
            }
            if (!in_array($tipo, array('data', 'texto', 'numero', 'selecao'), true)) {
                $erros[] = $prefixo . 'tipo de filtro não suportado: ' . $tipo . '.';
            }
            $filtrosIds[$filtroId] = true;
        }
    }

    if (($exigirHabilitado || !empty($configuracao['habilitado'])) && $ativos === 0) {
        $erros[] = 'Habilite ao menos um indicador.';
    }

    return empty($erros);
}

function dashboardPersonalizadoConfiguracao($empresaId, &$erros = array())
{
    $errosArquivos = array();
    $configuracao = false;
    foreach (dashboardPersonalizadoEmpresasCandidatas($empresaId) as $empresaCandidata) {
        $arquivos = array_values(array_filter(dashboardPersonalizadoArquivos($empresaCandidata), function ($arquivo) {
            return is_file($arquivo) && is_readable($arquivo);
        }));
        usort($arquivos, function ($arquivoA, $arquivoB) {
            return (int) @filemtime($arquivoB) - (int) @filemtime($arquivoA);
        });

        foreach ($arquivos as $arquivo) {
            $configuracaoCandidata = json_decode(file_get_contents($arquivo), true);
            if (!is_array($configuracaoCandidata) || json_last_error() !== JSON_ERROR_NONE) {
                $errosArquivos[] = 'Existe um JSON personalizado inválido.';
                continue;
            }

            $errosCandidata = array();
            if (!dashboardPersonalizadoValidarConfiguracao($configuracaoCandidata, $errosCandidata, false)) {
                $errosArquivos = array_merge($errosArquivos, $errosCandidata);
                continue;
            }
            if (empty($configuracaoCandidata['habilitado'])) {
                $erros = array('O dashboard personalizado está desabilitado.');
                return false;
            }

            $configuracao = $configuracaoCandidata;
            $configuracao['_empresaConfiguracao'] = (int) $empresaCandidata;
            $configuracao['_origemConfiguracao'] = (int) $empresaCandidata === (int) $empresaId ? 'empresa' : 'geral';
            break 2;
        }

        if (!empty($arquivos) && $configuracao === false) {
            $erros = !empty($errosArquivos) ? array_values(array_unique($errosArquivos)) : array('A personalização do dashboard não pôde ser carregada.');
            return false;
        }
    }

    if ($configuracao === false) {
        $arquivoPadrao = dashboardPersonalizadoArquivoPadrao();
        if (is_file($arquivoPadrao) && is_readable($arquivoPadrao)) {
            $configuracaoPadrao = json_decode(file_get_contents($arquivoPadrao), true);
            $errosPadrao = array();
            if (is_array($configuracaoPadrao)
                && json_last_error() === JSON_ERROR_NONE
                && dashboardPersonalizadoValidarConfiguracao($configuracaoPadrao, $errosPadrao, true)
                && !empty($configuracaoPadrao['habilitado'])) {
                $configuracao = $configuracaoPadrao;
                $configuracao['_empresaConfiguracao'] = null;
                $configuracao['_origemConfiguracao'] = 'padrao';
            } else {
                $errosArquivos = array_merge($errosArquivos, $errosPadrao);
            }
        }
    }

    if ($configuracao === false) {
        $erros = !empty($errosArquivos) ? array_values(array_unique($errosArquivos)) : array('O dashboard personalizado não está habilitado.');
        return false;
    }

    usort($configuracao['indicadores'], function ($a, $b) {
        return (int) (isset($a['ordem']) ? $a['ordem'] : 999) - (int) (isset($b['ordem']) ? $b['ordem'] : 999);
    });

    $configuracao['indicadores'] = array_values(array_filter($configuracao['indicadores'], function ($indicador) {
        return !isset($indicador['habilitado']) || $indicador['habilitado'] === true;
    }));

    return $configuracao;
}

function dashboardPersonalizadoHabilitado($empresaId)
{
    $erros = array();
    return dashboardPersonalizadoConfiguracao($empresaId, $erros) !== false;
}
