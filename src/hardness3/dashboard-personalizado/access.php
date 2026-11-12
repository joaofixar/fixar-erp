<?php
namespace hardness;

/*
 * As permissões vivem fora do JSON que monta os indicadores. Dessa forma o
 * diretor pode administrar acessos sem risco de alterar uma personalização.
 */
function dashboardPersonalizadoArquivoAcessos($empresaId)
{
    $empresaArquivo = (int) $empresaId;
    $erros = array();
    $configuracao = dashboardPersonalizadoConfiguracao($empresaId, $erros);
    if (is_array($configuracao) && !empty($configuracao['_empresaConfiguracao'])) {
        $empresaArquivo = (int) $configuracao['_empresaConfiguracao'];
    }

    $arquivoConfiguracao = dashboardPersonalizadoArquivo($empresaArquivo);
    if ($arquivoConfiguracao === '') {
        return '';
    }

    return dirname($arquivoConfiguracao) . '/' . (int) $empresaId . 'DASHBOARD PERSONALIZAVEL-acessos.json';
}

function dashboardPersonalizadoAcessosPadrao()
{
    return array('perfisGestores' => array(), 'perfis' => array(), 'usuarios' => array());
}

function dashboardPersonalizadoNormalizarIndicadores($indicadores, $indicadoresValidos)
{
    $resultado = array();
    foreach ((array) $indicadores as $indicador) {
        $indicador = (string) $indicador;
        if (isset($indicadoresValidos[$indicador])) {
            $resultado[$indicador] = true;
        }
    }

    return array_keys($resultado);
}

function dashboardPersonalizadoLerAcessos($empresaId, $indicadoresValidos = array())
{
    $acessos = dashboardPersonalizadoAcessosPadrao();
    $arquivo = dashboardPersonalizadoArquivoAcessos($empresaId);
    if ($arquivo === '' || !is_readable($arquivo)) {
        return $acessos;
    }

    $conteudo = json_decode(file_get_contents($arquivo), true);
    if (!is_array($conteudo)) {
        return $acessos;
    }

    foreach (array('perfisGestores', 'perfis', 'usuarios') as $chave) {
        if (isset($conteudo[$chave]) && is_array($conteudo[$chave])) {
            $acessos[$chave] = $conteudo[$chave];
        }
    }

    $acessos['perfisGestores'] = array_values(array_unique(array_filter(array_map('intval', $acessos['perfisGestores']))));
    foreach (array('perfis') as $tipo) {
        $normalizado = array();
        foreach ($acessos[$tipo] as $id => $indicadores) {
            $id = (int) $id;
            if ($id > 0) {
                $normalizado[$id] = dashboardPersonalizadoNormalizarIndicadores($indicadores, $indicadoresValidos);
            }
        }
        $acessos[$tipo] = $normalizado;
    }

    $usuariosNormalizados = array();
    foreach ((array) $acessos['usuarios'] as $id => $regra) {
        $id = (int) $id;
        if ($id <= 0 || !is_array($regra)) {
            continue;
        }

        $indicadores = dashboardPersonalizadoNormalizarIndicadores(
            isset($regra['indicadores']) ? $regra['indicadores'] : array(),
            $indicadoresValidos
        );
        $restricoes = array();
        foreach ((array) (isset($regra['restricoes']) ? $regra['restricoes'] : array()) as $indicadorId => $restricao) {
            if (!isset($indicadoresValidos[$indicadorId]) || !is_array($restricao)) {
                continue;
            }
            $vendedorId = isset($restricao['vendedorId']) ? (int) $restricao['vendedorId'] : 0;
            $vendedorNome = isset($restricao['vendedorNome']) ? trim((string) $restricao['vendedorNome']) : '';
            if ($vendedorId > 0 && $vendedorNome !== '') {
                $restricoes[$indicadorId] = array('vendedorId' => $vendedorId, 'vendedorNome' => $vendedorNome);
                if (!in_array($indicadorId, $indicadores, true)) {
                    $indicadores[] = $indicadorId;
                }
            }
        }
        if (!empty($indicadores) || !empty($restricoes)) {
            $usuariosNormalizados[$id] = array('indicadores' => array_values($indicadores), 'restricoes' => $restricoes);
        }
    }
    $acessos['usuarios'] = $usuariosNormalizados;

    return $acessos;
}

function dashboardPersonalizadoSalvarAcessos($empresaId, $acessos)
{
    $arquivo = dashboardPersonalizadoArquivoAcessos($empresaId);
    if ($arquivo === '') {
        return false;
    }

    $diretorio = dirname($arquivo);
    if (!is_dir($diretorio) && !@mkdir($diretorio, 0770, true)) {
        return false;
    }

    $json = json_encode($acessos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($json === false) {
        return false;
    }

    $temporario = $arquivo . '.' . uniqid('tmp-', true);
    if (@file_put_contents($temporario, $json . "\n", LOCK_EX) === false) {
        return false;
    }
    @chmod($temporario, 0660);
    return @rename($temporario, $arquivo);
}

function dashboardPersonalizadoEhRoot($autorizacao)
{
    return !empty($autorizacao['root']) || (isset($autorizacao['usuario']) && (int) $autorizacao['usuario'] === 63);
}

function dashboardPersonalizadoPodeGerenciarAcessos($autorizacao, $acessos)
{
    return dashboardPersonalizadoEhRoot($autorizacao)
        || in_array((int) $autorizacao['perfil'], (array) $acessos['perfisGestores'], true);
}

function dashboardPersonalizadoIndicadoresPermitidos($autorizacao, $configuracao, $acessos)
{
    $acessoDiretorAutomatico = !array_key_exists('acessoDiretorAutomatico', $configuracao)
        || $configuracao['acessoDiretorAutomatico'] === true;
    if (dashboardPersonalizadoPodeGerenciarAcessos($autorizacao, $acessos)
        || (!empty($autorizacao['diretor']) && $acessoDiretorAutomatico)) {
        return $configuracao['indicadores'];
    }

    $permitidos = array();
    $perfil = (int) $autorizacao['perfil'];
    foreach ((array) (isset($acessos['perfis'][$perfil]) ? $acessos['perfis'][$perfil] : array()) as $id) {
        $permitidos[$id] = true;
    }
    $usuario = (int) $autorizacao['usuario'];
    foreach ((array) (isset($acessos['usuarios'][$usuario]['indicadores']) ? $acessos['usuarios'][$usuario]['indicadores'] : array()) as $id) {
        $permitidos[$id] = true;
    }

    return array_values(array_filter($configuracao['indicadores'], function ($indicador) use ($permitidos) {
        return isset($permitidos[$indicador['id']]);
    }));
}

function dashboardPersonalizadoFiltroVendedorDireto($indicador)
{
    foreach ((array) (isset($indicador['filtros']) ? $indicador['filtros'] : array()) as $filtro) {
        if (!is_array($filtro)) {
            continue;
        }
        $id = isset($filtro['id']) ? (string) $filtro['id'] : '';
        if (in_array($id, array('vendedor', 'vendedorId', 'representante', 'representanteId'), true)) {
            return $filtro;
        }
    }
    return false;
}

function dashboardPersonalizadoFiltroVendedor($indicador)
{
    $filtro = dashboardPersonalizadoFiltroVendedorDireto($indicador);
    if ($filtro !== false) {
        return $filtro;
    }
    foreach ((array) (isset($indicador['abas']) ? $indicador['abas'] : array()) as $aba) {
        $filtro = dashboardPersonalizadoFiltroVendedorDireto($aba);
        if ($filtro !== false) {
            return $filtro;
        }
    }
    return false;
}

function dashboardPersonalizadoIndicadorSuportaRestricaoVendedor($indicador)
{
    return dashboardPersonalizadoFiltroVendedor($indicador) !== false;
}

function dashboardPersonalizadoRestricaoUsuarioIndicador($autorizacao, $acessos, $indicadorId)
{
    if (dashboardPersonalizadoPodeGerenciarAcessos($autorizacao, $acessos)) {
        return false;
    }
    $usuario = (int) $autorizacao['usuario'];
    $restricoes = isset($acessos['usuarios'][$usuario]['restricoes']) ? $acessos['usuarios'][$usuario]['restricoes'] : array();
    return isset($restricoes[$indicadorId]) ? $restricoes[$indicadorId] : false;
}

function dashboardPersonalizadoValorBloqueadoVendedor($filtro, $restricao)
{
    return isset($filtro['tipo']) && $filtro['tipo'] === 'selecao'
        ? (string) $restricao['vendedorId']
        : (string) $restricao['vendedorNome'];
}

function dashboardPersonalizadoAbaVendedores($indicador)
{
    $abas = isset($indicador['abas']) && is_array($indicador['abas']) ? $indicador['abas'] : array();
    foreach ($abas as $aba) {
        $texto = (isset($aba['id']) ? $aba['id'] : '') . ' ' . (isset($aba['titulo']) ? $aba['titulo'] : '');
        if (preg_match('/vendedor/i', $texto)) {
            return isset($aba['id']) ? (string) $aba['id'] : '';
        }
    }
    return !empty($abas) && isset($abas[0]['id']) ? (string) $abas[0]['id'] : '';
}

/*
 * A restrição individual é preparada na configuração pública e reaplicada na
 * requisição dos dados. O segundo passo é o que protege exportações e chamadas
 * manuais à API.
 */
function dashboardPersonalizadoAplicarRestricaoUsuario($indicador, $restricao, &$entrada, &$erro)
{
    $erro = '';
    if ($restricao === false) {
        return $indicador;
    }
    if (isset($indicador['clientesInativos']['habilitado']) && $indicador['clientesInativos']['habilitado'] === true) {
        $erro = 'A restrição individual de vendedor não é compatível com este indicador.';
        return false;
    }
    $filtroVendedor = dashboardPersonalizadoFiltroVendedor($indicador);
    if ($filtroVendedor === false) {
        $erro = 'A restrição do usuário não é compatível com este indicador.';
        return false;
    }

    $entrada['f_' . $filtroVendedor['id']] = dashboardPersonalizadoValorBloqueadoVendedor($filtroVendedor, $restricao);
    unset($entrada['f_dataInicio'], $entrada['f_dataFim']);
    $bloquearFiltros = function (&$definicao) use ($filtroVendedor, $restricao) {
        if (empty($definicao['filtros']) || !is_array($definicao['filtros'])) {
            return;
        }
        foreach ($definicao['filtros'] as &$filtro) {
            $id = isset($filtro['id']) ? (string) $filtro['id'] : '';
            if ($id === 'dataInicio' || $id === 'dataFim' || $id === $filtroVendedor['id']) {
                $filtro['bloqueado'] = true;
                if ($id === $filtroVendedor['id']) {
                    $filtro['valor'] = dashboardPersonalizadoValorBloqueadoVendedor($filtro, $restricao);
                }
            }
        }
        unset($filtro);
    };
    $bloquearFiltros($indicador);
    if (!empty($indicador['abas']) && is_array($indicador['abas'])) {
        foreach ($indicador['abas'] as &$aba) {
            $bloquearFiltros($aba);
        }
        unset($aba);
    }
    return $indicador;
}

function dashboardPersonalizadoPodeAcessar($autorizacao, $empresaId)
{
    $erros = array();
    $configuracao = dashboardPersonalizadoConfiguracao($empresaId, $erros);
    if ($configuracao === false) {
        return false;
    }
    $ids = array();
    foreach ($configuracao['indicadores'] as $indicador) {
        $ids[$indicador['id']] = true;
    }
    $acessos = dashboardPersonalizadoLerAcessos($empresaId, $ids);
    return !empty(dashboardPersonalizadoIndicadoresPermitidos($autorizacao, $configuracao, $acessos));
}
