<?php
namespace hardness;

/*
 * As permissões vivem fora do JSON que monta os indicadores. Dessa forma o
 * gestor autorizado pode administrar acessos sem alterar uma personalização.
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
                if (isset($restricao['escopo']) && $restricao['escopo'] === 'equipe') $restricoes[$indicadorId]['escopo'] = 'equipe';
                if (!in_array($indicadorId, $indicadores, true)) {
                    $indicadores[] = $indicadorId;
                }
            }
        }
        if (!empty($indicadores) || !empty($restricoes) || !empty($regra['gestor'])) {
            $usuariosNormalizados[$id] = array('indicadores' => array_values($indicadores), 'restricoes' => $restricoes, 'gestor' => isset($regra['gestor']) && $regra['gestor'] === true);
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

function dashboardPersonalizadoGerenciaTodosAcessos($autorizacao, $acessos)
{
    return dashboardPersonalizadoEhRoot($autorizacao)
        || in_array((int) $autorizacao['perfil'], (array) $acessos['perfisGestores'], true);
}

function dashboardPersonalizadoPodeGerenciarAcessos($autorizacao, $acessos)
{
    return dashboardPersonalizadoGerenciaTodosAcessos($autorizacao, $acessos)
        || !empty($acessos['usuarios'][(int) $autorizacao['usuario']]['gestor']);
}

function dashboardPersonalizadoIndicadoresGerenciaveis($autorizacao, $configuracao, $acessos)
{
    if (dashboardPersonalizadoGerenciaTodosAcessos($autorizacao, $acessos)) return $configuracao['indicadores'];
    if (!dashboardPersonalizadoPodeGerenciarAcessos($autorizacao, $acessos)) return array();
    $ids = (array) $acessos['usuarios'][(int) $autorizacao['usuario']]['indicadores'];
    return array_values(array_filter($configuracao['indicadores'], function ($i) use ($ids) {
        return in_array($i['id'], $ids, true);
    }));
}

function dashboardPersonalizadoAcessosVisiveis($acessos, $indicadores)
{
    $ids = array_fill_keys(array_column($indicadores, 'id'), true);
    $resultado = dashboardPersonalizadoAcessosPadrao();
    foreach ($acessos['perfis'] as $id => $lista) $resultado['perfis'][$id] = dashboardPersonalizadoNormalizarIndicadores($lista, $ids);
    foreach ($acessos['usuarios'] as $id => $regra) $resultado['usuarios'][$id] = array(
        'indicadores' => dashboardPersonalizadoNormalizarIndicadores($regra['indicadores'], $ids),
        'restricoes' => array_intersect_key($regra['restricoes'], $ids),
        'gestor' => !empty($regra['gestor'])
    );
    return $resultado;
}

// O gestor individual administra somente seus indicadores, preservando todos
// os demais acessos e sem poder alterar sua própria autorização ou delegar
// uma visão de dados mais ampla do que a que recebeu.
function dashboardPersonalizadoMesclarAcessosGestor($autorizacao, $configuracao, $atuais, $novo, &$erro)
{
    if (dashboardPersonalizadoGerenciaTodosAcessos($autorizacao, $atuais)) {
        if (!dashboardPersonalizadoEhRoot($autorizacao)) {
            foreach ($novo['usuarios'] as $id => &$regra) $regra['gestor'] = !empty($atuais['usuarios'][$id]['gestor']);
            unset($regra);
            foreach ($atuais['usuarios'] as $id => $regra) if (!empty($regra['gestor']) && !isset($novo['usuarios'][$id])) {
                $novo['usuarios'][$id] = array('indicadores' => array(), 'restricoes' => array(), 'gestor' => true);
            }
        }
        return $novo;
    }
    $ids = array_fill_keys(array_column(dashboardPersonalizadoIndicadoresGerenciaveis($autorizacao, $configuracao, $atuais), 'id'), true);
    $resultado = $atuais;
    $ator = (int) $autorizacao['usuario'];
    $limites = isset($atuais['usuarios'][$ator]['restricoes']) ? $atuais['usuarios'][$ator]['restricoes'] : array();
    foreach ($novo['perfis'] as $id => $lista) {
        foreach ($lista as $indicador) if (!isset($ids[$indicador])) { $erro = 'Você pode distribuir apenas os indicadores liberados para você.'; return false; }
        $anterior = isset($atuais['perfis'][$id]) ? $atuais['perfis'][$id] : array();
        foreach ($lista as $indicador) if (isset($limites[$indicador]) && !in_array($indicador, $anterior, true)) { $erro = 'Um indicador com visão individual deve ser liberado por usuário, com a mesma restrição.'; return false; }
        $resultado['perfis'][$id] = array_values(array_unique(array_merge(array_values(array_filter($anterior, function ($i) use ($ids) { return !isset($ids[$i]); })), $lista)));
    }
    foreach ($novo['usuarios'] as $id => $regra) {
        foreach (array_merge($regra['indicadores'], array_keys($regra['restricoes'])) as $indicador) if (!isset($ids[$indicador])) { $erro = 'Você pode distribuir apenas os indicadores liberados para você.'; return false; }
        if ((int) $id === $ator) continue;
        foreach ($regra['indicadores'] as $indicador) if (isset($limites[$indicador])) {
            $r = isset($regra['restricoes'][$indicador]) ? $regra['restricoes'][$indicador] : array();
            if (empty($r['vendedorId']) || (int) $r['vendedorId'] !== (int) $limites[$indicador]['vendedorId'] || (isset($r['escopo']) ? $r['escopo'] : '') !== (isset($limites[$indicador]['escopo']) ? $limites[$indicador]['escopo'] : '')) {
                $erro = 'Você não pode liberar uma visão mais ampla que a sua.'; return false;
            }
        }
        $anterior = isset($atuais['usuarios'][$id]) ? $atuais['usuarios'][$id] : array('indicadores' => array(), 'restricoes' => array());
        $lista = array_values(array_filter($anterior['indicadores'], function ($i) use ($ids) { return !isset($ids[$i]); }));
        $resultado['usuarios'][$id] = array('indicadores' => array_values(array_unique(array_merge($lista, $regra['indicadores']))),
            'restricoes' => array_merge(array_diff_key($anterior['restricoes'], $ids), $regra['restricoes']), 'gestor' => !empty($anterior['gestor']));
    }
    return $resultado;
}

function dashboardPersonalizadoIndicadoresPermitidos($autorizacao, $configuracao, $acessos)
{
    // JSONs legados podem conter acessoDiretorAutomatico=true. Esse campo não
    // concede mais acesso: somente ROOT e as liberações explícitas da ACL.
    if (dashboardPersonalizadoGerenciaTodosAcessos($autorizacao, $acessos)) {
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
    if (dashboardPersonalizadoGerenciaTodosAcessos($autorizacao, $acessos)) {
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
    if (!empty($indicador['painelCotacoes']['habilitado'])) {
        // O painel novo tem abas e datas livres; somente o universo de vendedores é restrito.
        $indicador['_cotacoesRestricao'] = $restricao;
        return $indicador;
    }
    if (isset($restricao['escopo']) && $restricao['escopo'] === 'equipe') {
        $erro = 'O acesso por equipe exige um indicador compatível.';
        return false;
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
    $datasLivres = !empty($indicador['desempenhoComercial']['carregamentoProgressivo']);
    if (!$datasLivres) unset($entrada['f_dataInicio'], $entrada['f_dataFim']);
    if (!empty($indicador['desempenhoComercial']['equipes'])) {
        // Gestores sem restrição mantêm o filtro; o acesso individual ignora
        // também valores forjados ou preferências antigas de equipe.
        unset($entrada['f_equipe']);
        $indicador['filtros'] = array_values(array_filter($indicador['filtros'], function ($filtro) {
            return !isset($filtro['id']) || $filtro['id'] !== 'equipe';
        }));
    }
    $bloquearFiltros = function (&$definicao) use ($filtroVendedor, $restricao, $datasLivres) {
        if (empty($definicao['filtros']) || !is_array($definicao['filtros'])) {
            return;
        }
        foreach ($definicao['filtros'] as &$filtro) {
            $id = isset($filtro['id']) ? (string) $filtro['id'] : '';
            if ((!$datasLivres && ($id === 'dataInicio' || $id === 'dataFim')) || $id === $filtroVendedor['id']) {
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
