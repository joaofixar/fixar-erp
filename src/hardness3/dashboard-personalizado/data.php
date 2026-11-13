<?php
namespace hardness;

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

require_once __DIR__ . '/session.php';
require_once __DIR__ . '/../bibliotecas/confUsuario.php';
require_once __DIR__ . '/connection.php';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/access.php';
require_once __DIR__ . '/filter-preferences.php';
require_once __DIR__ . '/custom-indicator-engine.php';
require_once __DIR__ . '/agenda-action.php';
require_once __DIR__ . '/cotacoes-documento.php';
require_once __DIR__ . '/titulo-action.php';
require_once __DIR__ . '/portal-representantes.php';
require_once __DIR__ . '/resposta-json.php';

function dashboardResponder($codigo, $dados)
{
    http_response_code($codigo);
    echo dashboardRespostaJson(dashboardSqlResposta($codigo, $dados));
    exit;
}

$autorizacao = dashboardPersonalizadoLerAutorizacao($confUsuario);
if ($autorizacao === false) {
    dashboardResponder(401, array('error' => 'Autorização do dashboard não encontrada ou expirada.'));
}
$usuario = $autorizacao['usuario'];
$empresa = $autorizacao['empresa'];
$acao = isset($_GET['action']) && is_string($_GET['action']) ? $_GET['action'] : 'configuration';
dashboardSqlIniciar($autorizacao, $acao, $_GET);

$errosConfiguracao = array();
$configuracao = dashboardPersonalizadoConfiguracao($empresa, $errosConfiguracao);
if ($configuracao === false) {
    dashboardResponder(403, array('error' => implode(' ', $errosConfiguracao)));
}

$idsIndicadores = array();
foreach ($configuracao['indicadores'] as $indicadorConfigurado) {
    $idsIndicadores[$indicadorConfigurado['id']] = true;
}
$acessos = dashboardPersonalizadoLerAcessos($empresa, $idsIndicadores);
$podeGerenciarAcessos = dashboardPersonalizadoPodeGerenciarAcessos($autorizacao, $acessos);
$indicadoresPermitidos = dashboardPersonalizadoIndicadoresPermitidos($autorizacao, $configuracao, $acessos);

if ($acao === 'configuration') {
    $indicadores = array();
    foreach ($indicadoresPermitidos as $indicador) {
        $entradaRestrita = array();
        $restricao = dashboardPersonalizadoRestricaoUsuarioIndicador($autorizacao, $acessos, $indicador['id']);
        $indicador = dashboardPersonalizadoAplicarRestricaoUsuario($indicador, $restricao, $entradaRestrita, $erroRestricao);
        if ($indicador === false) {
            dashboardResponder(403, array('error' => $erroRestricao));
        }
        $filtros = dashboardIndicadorFiltrosSemOpcoesDinamicas(isset($indicador['filtros']) ? $indicador['filtros'] : array());
        $definicaoPublica = dashboardIndicadorDefinicaoPublica($indicador, $filtros);
        if ($restricao !== false && !empty($definicaoPublica['abas']) && !dashboardPersonalizadoAbasComerciaisLocais($indicador)) {
            $abaPermitida = dashboardPersonalizadoAbaVendedores($indicador);
            $definicaoPublica['abas'] = array_values(array_filter($definicaoPublica['abas'], function ($aba) use ($abaPermitida) {
                return isset($aba['id']) && $aba['id'] === $abaPermitida;
            }));
        }
        $indicadores[] = $definicaoPublica;
    }
    dashboardResponder(200, array(
        'titulo' => isset($configuracao['titulo']) ? $configuracao['titulo'] : 'Dashboard',
        'indicadores' => $indicadores,
        'preferenciasFiltros' => dashboardPersonalizadoLerPreferenciasFiltros($empresa, $usuario),
        'podeGerenciarAcessos' => $podeGerenciarAcessos,
        'podeAnalisarSql' => dashboardPersonalizadoEhRoot($autorizacao),
        'semAcesso' => empty($indicadores),
    ));
}

if ($acao === 'saveFilters') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        dashboardResponder(405, array('error' => 'Método não permitido.'));
    }
    $recebido = json_decode(file_get_contents('php://input'), true);
    if (!is_array($recebido)) {
        dashboardResponder(422, array('error' => 'Preferências de filtros inválidas.'));
    }
    $indicadorIdPreferencia = isset($recebido['id']) ? (string) $recebido['id'] : '';
    $abaIdPreferencia = isset($recebido['tab']) ? (string) $recebido['tab'] : '';
    if (!preg_match('/^[a-z][a-zA-Z0-9_]{1,49}$/', $indicadorIdPreferencia)
        || ($abaIdPreferencia !== '' && !preg_match('/^[a-zA-Z0-9_-]{1,80}$/', $abaIdPreferencia))) {
        dashboardResponder(422, array('error' => 'Indicador ou aba inválidos para salvar os filtros.'));
    }
    $indicadorPreferencia = null;
    foreach ($indicadoresPermitidos as $indicadorPermitido) {
        if (isset($indicadorPermitido['id']) && $indicadorPermitido['id'] === $indicadorIdPreferencia) {
            $indicadorPreferencia = $indicadorPermitido;
            break;
        }
    }
    if ($indicadorPreferencia === null) {
        dashboardResponder(404, array('error' => 'Indicador não encontrado para salvar os filtros.'));
    }
    if (!empty($indicadorPreferencia['abas']) && is_array($indicadorPreferencia['abas'])) {
        $abaEncontradaPreferencia = false;
        foreach ($indicadorPreferencia['abas'] as $abaPreferencia) {
            if (isset($abaPreferencia['id']) && (string) $abaPreferencia['id'] === $abaIdPreferencia) {
                $indicadorPreferencia = array_merge($indicadorPreferencia, $abaPreferencia);
                $abaEncontradaPreferencia = true;
                break;
            }
        }
        if (!$abaEncontradaPreferencia) {
            dashboardResponder(422, array('error' => 'Aba inválida para salvar os filtros.'));
        }
    } elseif ($abaIdPreferencia !== '') {
        dashboardResponder(422, array('error' => 'Este indicador não possui abas para salvar os filtros.'));
    }
    $entradaPreferencia = array();
    $restricaoPreferencia = dashboardPersonalizadoRestricaoUsuarioIndicador($autorizacao, $acessos, $indicadorIdPreferencia);
    $indicadorPreferencia = dashboardPersonalizadoAplicarRestricaoUsuario($indicadorPreferencia, $restricaoPreferencia, $entradaPreferencia, $erroPreferencia);
    if ($indicadorPreferencia === false) {
        dashboardResponder(403, array('error' => $erroPreferencia));
    }
    $filtrosRecebidos = isset($recebido['filters']) && is_array($recebido['filters']) ? $recebido['filters'] : array();
    $filtrosPermitidos = array();
    foreach ((array) (isset($indicadorPreferencia['filtros']) ? $indicadorPreferencia['filtros'] : array()) as $filtroPreferencia) {
        if (!is_array($filtroPreferencia) || !empty($filtroPreferencia['bloqueado']) || (isset($filtroPreferencia['persistir']) && $filtroPreferencia['persistir'] === false)) continue;
        $filtroIdPreferencia = isset($filtroPreferencia['id']) ? (string) $filtroPreferencia['id'] : '';
        $chaveFiltroPreferencia = 'f_' . $filtroIdPreferencia;
        if ($filtroIdPreferencia === '' || !array_key_exists($chaveFiltroPreferencia, $filtrosRecebidos) || is_array($filtrosRecebidos[$chaveFiltroPreferencia])) continue;
        $valorPreferencia = trim((string) $filtrosRecebidos[$chaveFiltroPreferencia]);
        if ((function_exists('mb_strlen') ? mb_strlen($valorPreferencia, 'UTF-8') : strlen($valorPreferencia)) <= 500) {
            $filtrosPermitidos[$chaveFiltroPreferencia] = $valorPreferencia;
        }
    }
    if (!dashboardPersonalizadoAtualizarPreferenciasFiltros($empresa, $usuario, $indicadorIdPreferencia, $abaIdPreferencia, $filtrosPermitidos)) {
        dashboardResponder(500, array('error' => 'Não foi possível salvar as preferências de filtros.'));
    }
    dashboardResponder(200, array('ok' => true, 'filters' => $filtrosPermitidos));
}

if ($acao === 'access') {
    if (!$podeGerenciarAcessos) {
        dashboardResponder(403, array('error' => 'Você não possui permissão para gerenciar acessos.'));
    }

    $conexaoAcessos = @mysqli_connect($confUsuario['dbHost'], $confUsuario['dbUser'], $confUsuario['dbPass'], $confUsuario['dbDatabase']);
    if (!$conexaoAcessos) {
        dashboardResponder(503, array('error' => 'Não foi possível carregar usuários e perfis.'));
    }
    mysqli_set_charset($conexaoAcessos, 'utf8');
    $perfis = array();
    $resultadoPerfis = mysqli_query($conexaoAcessos, "SELECT C021_Id AS id, C021_Nome_Perfil AS nome FROM C021 WHERE IFNULL(C021_Nome_Perfil, '') != '' ORDER BY nome");
    while ($resultadoPerfis && ($linha = mysqli_fetch_assoc($resultadoPerfis))) {
        $perfis[] = array('id' => (int) $linha['id'], 'nome' => $linha['nome']);
    }
    $usuarios = array();
    $vendedores = array();
    // Mesma origem e mesmo critério da tela Cadastros > Usuários (cad030).
    $resultadoUsuarios = mysqli_query($conexaoAcessos, "SELECT C007_Id AS id, C007_Primeiro_Nome AS nome FROM C007 WHERE C007_Ativo = 'S' ORDER BY C007_Primeiro_Nome");
    if ($resultadoUsuarios === false) {
        mysqli_close($conexaoAcessos);
        dashboardResponder(503, array('error' => 'Não foi possível carregar os usuários ativos do cadastro.'));
    }
    while ($resultadoUsuarios && ($linha = mysqli_fetch_assoc($resultadoUsuarios))) {
        $registro = array('id' => (int) $linha['id'], 'nome' => $linha['nome']);
        $usuarios[] = $registro;
        $vendedores[] = $registro;
    }
    mysqli_close($conexaoAcessos);

    $indicadoresGestao = dashboardPersonalizadoIndicadoresGerenciaveis($autorizacao, $configuracao, $acessos);
    $gestaoLimitada = !dashboardPersonalizadoGerenciaTodosAcessos($autorizacao, $acessos);
    $indicadoresComRestricaoVendedor = array();
    $indicadoresComRestricaoEquipe = array();
    foreach ($indicadoresGestao as $indicador) {
        if (dashboardCotacoesHabilitado($indicador)) $indicadoresComRestricaoEquipe[$indicador['id']] = true;
        if (dashboardPersonalizadoIndicadorSuportaRestricaoVendedor($indicador)) {
            $indicadoresComRestricaoVendedor[$indicador['id']] = true;
        }
    }

    dashboardResponder(200, array(
        'indicadores' => array_map(function ($indicador) { return array('id' => $indicador['id'], 'titulo' => $indicador['titulo']); }, $indicadoresGestao),
        'perfis' => $perfis,
        'usuarios' => $usuarios,
        'vendedores' => $vendedores,
        'indicadoresComRestricaoVendedor' => $indicadoresComRestricaoVendedor,
        'indicadoresComRestricaoEquipe' => $indicadoresComRestricaoEquipe,
        'acessos' => $gestaoLimitada ? dashboardPersonalizadoAcessosVisiveis($acessos, $indicadoresGestao) : $acessos,
        'edicaoLimitada' => $gestaoLimitada,
        'usuarioAtual' => (int) $usuario,
        'root' => dashboardPersonalizadoEhRoot($autorizacao),
    ));
}

if ($acao === 'saveAccess') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        dashboardResponder(405, array('error' => 'Método não permitido.'));
    }
    if (!$podeGerenciarAcessos) {
        dashboardResponder(403, array('error' => 'Você não possui permissão para gerenciar acessos.'));
    }
    $recebido = json_decode(file_get_contents('php://input'), true);
    if (!is_array($recebido)) {
        dashboardResponder(422, array('error' => 'Dados de acesso inválidos.'));
    }
    $conexaoValidacao = @mysqli_connect($confUsuario['dbHost'], $confUsuario['dbUser'], $confUsuario['dbPass'], $confUsuario['dbDatabase']);
    if (!$conexaoValidacao) {
        dashboardResponder(503, array('error' => 'Não foi possível validar os acessos informados.'));
    }
    $perfisValidos = array();
    $consultaPerfis = mysqli_query($conexaoValidacao, "SELECT C021_Id AS id FROM C021 WHERE IFNULL(C021_Nome_Perfil, '') != ''");
    while ($consultaPerfis && ($linha = mysqli_fetch_assoc($consultaPerfis))) { $perfisValidos[(int) $linha['id']] = true; }
    $usuariosValidos = array();
    $vendedoresValidos = array();
    $consultaUsuarios = mysqli_query($conexaoValidacao, "SELECT C007_Id AS id, C007_Primeiro_Nome AS nome FROM C007 WHERE C007_Ativo = 'S'");
    if ($consultaUsuarios === false) {
        mysqli_close($conexaoValidacao);
        dashboardResponder(503, array('error' => 'Não foi possível validar os usuários ativos do cadastro.'));
    }
    while ($consultaUsuarios && ($linha = mysqli_fetch_assoc($consultaUsuarios))) {
        $usuariosValidos[(int) $linha['id']] = true;
        $vendedoresValidos[(int) $linha['id']] = (string) $linha['nome'];
    }
    mysqli_close($conexaoValidacao);

    $novoAcesso = dashboardPersonalizadoAcessosPadrao();
    $gestoresRecebidos = array_values(array_unique(array_filter(array_map('intval', isset($recebido['perfisGestores']) ? $recebido['perfisGestores'] : array()))));
    $novoAcesso['perfisGestores'] = dashboardPersonalizadoEhRoot($autorizacao) ? array_values(array_filter($gestoresRecebidos, function ($id) use ($perfisValidos) { return isset($perfisValidos[$id]); })) : $acessos['perfisGestores'];
    foreach (array('perfis') as $tipo) {
        foreach ((array) (isset($recebido[$tipo]) ? $recebido[$tipo] : array()) as $id => $indicadores) {
            $id = (int) $id;
            if ($id > 0 && isset($perfisValidos[$id])) {
                $novoAcesso[$tipo][$id] = dashboardPersonalizadoNormalizarIndicadores($indicadores, $idsIndicadores);
            }
        }
    }
    foreach ((array) (isset($recebido['usuarios']) ? $recebido['usuarios'] : array()) as $usuarioId => $regra) {
        $usuarioId = (int) $usuarioId;
        if ($usuarioId <= 0 || !isset($usuariosValidos[$usuarioId]) || !is_array($regra)) {
            continue;
        }
        $indicadoresUsuario = dashboardPersonalizadoNormalizarIndicadores(isset($regra['indicadores']) ? $regra['indicadores'] : array(), $idsIndicadores);
        $restricoesUsuario = array();
        foreach ((array) (isset($regra['restricoes']) ? $regra['restricoes'] : array()) as $indicadorId => $restricao) {
            if (!isset($idsIndicadores[$indicadorId]) || !is_array($restricao)) {
                continue;
            }
            $indicadorConfigurado = null;
            foreach ($configuracao['indicadores'] as $indicador) {
                if ($indicador['id'] === $indicadorId) {
                    $indicadorConfigurado = $indicador;
                    break;
                }
            }
            $vendedorId = isset($restricao['vendedorId']) ? (int) $restricao['vendedorId'] : 0;
            if ($indicadorConfigurado === null || !dashboardPersonalizadoIndicadorSuportaRestricaoVendedor($indicadorConfigurado) || !isset($vendedoresValidos[$vendedorId])) {
                dashboardResponder(422, array('error' => 'Selecione um vendedor válido para a restrição individual.'));
            }
            $restricoesUsuario[$indicadorId] = array('vendedorId' => $vendedorId, 'vendedorNome' => $vendedoresValidos[$vendedorId]);
            if (isset($restricao['escopo']) && $restricao['escopo'] === 'equipe') {
                if (!dashboardCotacoesHabilitado($indicadorConfigurado)) dashboardResponder(422, array('error' => 'Este indicador não permite acesso por equipe.'));
                $restricoesUsuario[$indicadorId]['escopo'] = 'equipe';
            }
            if (!in_array($indicadorId, $indicadoresUsuario, true)) {
                $indicadoresUsuario[] = $indicadorId;
            }
        }
        $gestorUsuario = dashboardPersonalizadoEhRoot($autorizacao)
            ? isset($regra['gestor']) && $regra['gestor'] === true
            : !empty($acessos['usuarios'][$usuarioId]['gestor']);
        if (!empty($indicadoresUsuario) || !empty($restricoesUsuario) || $gestorUsuario || !dashboardPersonalizadoGerenciaTodosAcessos($autorizacao, $acessos)) {
            $novoAcesso['usuarios'][$usuarioId] = array('indicadores' => array_values($indicadoresUsuario), 'restricoes' => $restricoesUsuario, 'gestor' => $gestorUsuario);
        }
    }
    $novoAcesso = dashboardPersonalizadoMesclarAcessosGestor($autorizacao, $configuracao, $acessos, $novoAcesso, $erroGestao);
    if ($novoAcesso === false) dashboardResponder(403, array('error' => $erroGestao));
    if (!dashboardPersonalizadoSalvarAcessos($empresa, $novoAcesso)) {
        dashboardResponder(500, array('error' => 'Não foi possível salvar os acessos.'));
    }
    dashboardResponder(200, array('ok' => true));
}

if ($acao !== 'indicator' && $acao !== 'portalRepresentativeAction' && $acao !== 'commercialClientHistory' && $acao !== 'commercialOrderPdf' && $acao !== 'agendaContext' && $acao !== 'titleContext' && $acao !== 'cotacoesDocumentContext' && $acao !== 'saveCotacoesGoal' && $acao !== 'saveCashForecast' && $acao !== 'saveBankGuarantees') {
    dashboardResponder(404, array('error' => 'Consulta não disponível.'));
}

$indicadorId = isset($_GET['id']) ? (string) $_GET['id'] : '';
$indicadorSelecionado = null;
foreach ($indicadoresPermitidos as $indicador) {
    if (isset($indicador['id']) && $indicador['id'] === $indicadorId) {
        $indicadorSelecionado = $indicador;
        break;
    }
}
if ($indicadorSelecionado === null) {
    dashboardResponder(404, array('error' => 'Indicador não encontrado na personalização da empresa.'));
}

$restricaoUsuario = dashboardPersonalizadoRestricaoUsuarioIndicador($autorizacao, $acessos, $indicadorId);
$entradaIndicador = $_GET;

if ($acao === 'saveCotacoesGoal') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') dashboardResponder(405, array('error' => 'Método não permitido.'));
    if (!dashboardCotacoesHabilitado($indicadorSelecionado)) dashboardResponder(403, array('error' => 'Este indicador não permite metas de projeção.'));
    if (strpos(isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : '', 'application/json') !== 0) dashboardResponder(415, array('error' => 'Formato de envio inválido.'));
    if (!empty($_SERVER['HTTP_ORIGIN'])) {
        if (!dashboardCotacoesMetaOrigemValida($_SERVER['HTTP_ORIGIN'], isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '')) dashboardResponder(403, array('error' => 'Origem do envio inválida.'));
    }
    $metaEntrada = json_decode(file_get_contents('php://input'), true);
    if (!is_array($metaEntrada) || !array_key_exists('valor', $metaEntrada)) dashboardResponder(422, array('error' => 'Informe o valor da meta.'));
    $chaveMeta = dashboardCotacoesMetaChave($metaEntrada, $erroMeta);
    if ($chaveMeta === false) dashboardResponder(422, array('error' => $erroMeta));
    // Empresa e usuário sempre vêm da autorização; nunca do corpo da requisição.
    $metaSalva = dashboardCotacoesMetaSalvar(dashboardCotacoesMetaArquivo($empresa, $usuario, $indicadorId), $chaveMeta, $metaEntrada['valor'], $erroMeta);
    if ($metaSalva === false) dashboardResponder(422, array('error' => $erroMeta));
    dashboardResponder(200, $metaSalva);
}

if (!empty($indicadorSelecionado['abas']) && is_array($indicadorSelecionado['abas'])) {
    $abaId = isset($_GET['tab']) ? (string) $_GET['tab'] : '';
    if ($restricaoUsuario !== false && !dashboardPersonalizadoAbasComerciaisLocais($indicadorSelecionado)) {
        $abaPermitida = dashboardPersonalizadoAbaVendedores($indicadorSelecionado);
        if ($abaPermitida === '' || ($abaId !== '' && $abaId !== $abaPermitida)) {
            dashboardResponder(403, array('error' => 'Esta aba não está liberada para o seu usuário.'));
        }
        $abaId = $abaPermitida;
        $entradaIndicador['tab'] = $abaId;
    }
    $abaEncontrada = false;
    foreach ($indicadorSelecionado['abas'] as $aba) {
        if ($abaId === '' || (isset($aba['id']) && $aba['id'] === $abaId)) {
            if (dashboardPersonalizadoAbasComerciaisLocais($indicadorSelecionado)) {
                $indicadorSelecionado['_abaComercial'] = $aba['id'];
            } else {
                $indicadorSelecionado = array_merge($indicadorSelecionado, $aba);
            }
            $abaEncontrada = true;
            break;
        }
    }
    if (!$abaEncontrada) dashboardResponder(404, array('error' => 'Aba do indicador não encontrada.'));
}

$indicadorSelecionado = dashboardPersonalizadoAplicarRestricaoUsuario($indicadorSelecionado, $restricaoUsuario, $entradaIndicador, $erroRestricao);
if ($indicadorSelecionado === false) {
    dashboardResponder(403, array('error' => $erroRestricao));
}
dashboardSqlContexto($indicadorSelecionado, $entradaIndicador);
if (dashboardPortalRepresentantesHabilitado($indicadorSelecionado) || $acao === 'portalRepresentativeAction') {
    if (!dashboardPortalRepresentantesHabilitado($indicadorSelecionado) || $restricaoUsuario !== false) dashboardResponder(403, array('error' => 'Gestão do portal indisponível neste indicador.'));
    try {
        if (!dashboardPortalRepresentantesDisponivel()) dashboardResponder(503, array('error' => 'Instale ou atualize o Portal do Representante nesta instalação para gerenciar os acessos.'));
        // A autorização assinada do dashboard não inicia a sessão interna do ERP.
        if (session_status() !== PHP_SESSION_ACTIVE) session_start(array('read_and_close' => true));
        if ($acao === 'portalRepresentativeAction') {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') dashboardResponder(405, array('error' => 'Método não permitido.'));
            if (strpos(isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : '', 'application/json') !== 0) dashboardResponder(415, array('error' => 'Formato de envio inválido.'));
            if (!empty($_SERVER['HTTP_ORIGIN']) && !dashboardCotacoesMetaOrigemValida($_SERVER['HTTP_ORIGIN'], isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '')) dashboardResponder(403, array('error' => 'Origem do envio inválida.'));
            dashboardResponder(200, dashboardPortalRepresentantesAcao($autorizacao, $confUsuario,
                json_decode(file_get_contents('php://input'), true), $_SERVER['REQUEST_METHOD'], $indicadorId));
        }
        if ($acao !== 'indicator') dashboardResponder(404, array('error' => 'Consulta não disponível.'));
        $conexaoPortal = dashboardPersonalizadoConectarLeitura($confUsuario, null, false);
        if (!$conexaoPortal) throw new RepresentantePortalErro('Não foi possível consultar o cadastro.', 503);
        mysqli_set_charset($conexaoPortal, 'utf8');
        @mysqli_query($conexaoPortal, 'SET SESSION MAX_EXECUTION_TIME=5000');
        @mysqli_query($conexaoPortal, 'START TRANSACTION READ ONLY');
        try { $portal = dashboardPortalRepresentantesCarregar($conexaoPortal, $indicadorSelecionado, $autorizacao, $confUsuario, $_SESSION, $indicadorId); }
        finally { mysqli_close($conexaoPortal); }
        dashboardResponder(200, array('indicador' => dashboardIndicadorDefinicaoPublica($indicadorSelecionado, array()),
            'portalRepresentantes' => $portal, 'card' => array('valor' => 0, 'formato' => 'numero'),
            'updatedAt' => (new \DateTimeImmutable('now', new \DateTimeZone('America/Sao_Paulo')))->format('d/m/Y H:i')));
    } catch (RepresentantePortalErro $erroPortal) { dashboardResponder($erroPortal->status, array('error' => $erroPortal->getMessage())); }
    catch (\Throwable $erroPortal) { dashboardResponder(503, array('error' => 'Não foi possível consultar os acessos do portal. Tente novamente.')); }
}
if (dashboardCotacoesHabilitado($indicadorSelecionado)) $indicadorSelecionado['_cotacoesUsuario'] = (int) $usuario;

if ($acao === 'saveBankGuarantees') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') dashboardResponder(405, array('error' => 'Método não permitido.'));
    if (!dashboardCarteiraBancosHabilitado($indicadorSelecionado) || $restricaoUsuario !== false) dashboardResponder(403, array('error' => 'Edição de garantias indisponível para este acesso.'));
    if (strpos(isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : '', 'application/json') !== 0) dashboardResponder(415, array('error' => 'Formato de envio inválido.'));
    if (!empty($_SERVER['HTTP_ORIGIN']) && !dashboardCotacoesMetaOrigemValida($_SERVER['HTTP_ORIGIN'], isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '')) dashboardResponder(403, array('error' => 'Origem do envio inválida.'));
    $bancosGarantia = dashboardCarteiraBancosConfig($indicadorSelecionado, $erroGarantias);
    if ($bancosGarantia === false) dashboardResponder(422, array('error' => $erroGarantias));
    $garantiasSalvas = dashboardCarteiraBancosSalvar(dashboardCarteiraBancosArquivo($empresa, $indicadorId), $bancosGarantia,
        json_decode(file_get_contents('php://input'), true), $usuario, $erroGarantias);
    if ($garantiasSalvas === false) dashboardResponder(422, array('error' => $erroGarantias));
    dashboardResponder(200, $garantiasSalvas);
}

if ($acao === 'saveCashForecast') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') dashboardResponder(405, array('error' => 'Método não permitido.'));
    if (!dashboardCaixaPrevisaoHabilitada($indicadorSelecionado) || $restricaoUsuario !== false) dashboardResponder(403, array('error' => 'Previsão manual indisponível para este acesso.'));
    if (strpos(isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : '', 'application/json') !== 0) dashboardResponder(415, array('error' => 'Formato de envio inválido.'));
    if (!empty($_SERVER['HTTP_ORIGIN']) && !dashboardCotacoesMetaOrigemValida($_SERVER['HTTP_ORIGIN'], isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '')) dashboardResponder(403, array('error' => 'Origem do envio inválida.'));
    $entradaPrevisao = json_decode(file_get_contents('php://input'), true);
    $salvo = dashboardCaixaPrevisaoSalvar(dashboardCaixaPrevisaoArquivo($empresa, $indicadorId), $entradaPrevisao, $erroPrevisao);
    if ($salvo === false) dashboardResponder(422, array('error' => $erroPrevisao));
    dashboardResponder(200, $salvo);
}

if ($acao === 'indicator') dashboardFiltrosCacheContexto(
    hash('sha256', $confUsuario['dbHost'] . '|' . $confUsuario['dbDatabase']),
    $autorizacao, $acessos, $indicadorSelecionado
);

if ($acao === 'agendaContext' || $acao === 'titleContext' || $acao === 'cotacoesDocumentContext' || $acao === 'commercialClientHistory' || $acao === 'commercialOrderPdf') {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start(array('read_and_close' => true));
    if (!dashboardAgendaSessaoValida($autorizacao, isset($_SESSION) ? $_SESSION : array())) {
        dashboardResponder(403, array('error' => 'A sessão do ERP mudou ou expirou. Reabra o dashboard pela empresa atual do sistema.'));
    }
}

// Abertura de telas e emissão de tickets exigem as permissões atuais do ERP.
$inicioConexao = microtime(true);
$conexao = dashboardPersonalizadoConectarLeitura(
    $confUsuario,
    isset($confUsuarioEspelhado) ? $confUsuarioEspelhado : null,
    ($acao === 'indicator' || $acao === 'commercialClientHistory') && dashboardPersonalizadoIndicadorUsaEspelho($indicadorSelecionado)
);
if (!empty($GLOBALS['dashboardSql'])) $GLOBALS['dashboardSql']['relatorio']['tempoConexaoMs'] = round((microtime(true) - $inicioConexao) * 1000, 2);
if (!$conexao) {
    dashboardResponder(503, array('error' => 'Não foi possível consultar os dados do dashboard.'));
}
mysqli_set_charset($conexao, 'utf8');
@mysqli_query($conexao, 'SET SESSION MAX_EXECUTION_TIME=15000');
@mysqli_query($conexao, 'START TRANSACTION READ ONLY');

$erro = '';
if ($acao === 'indicator' && !empty($indicadorSelecionado['desempenhoComercial']['habilitado'])) {
    // CSV/PDF geram sua tabela no endpoint de exportação; a tela usa apenas o componente comercial.
    $indicadorSelecionado['_comercialSomenteVisual'] = true;
}
try {
    if ($acao === 'indicator' && dashboardCargaHabilitada($indicadorSelecionado)
        && (!empty($entradaIndicador['carregarProgressivo']) || (dashboardAbcClientesLotesHabilitado($indicadorSelecionado) && !empty($entradaIndicador['cargaToken'])))) {
        dashboardCargaInicializar($conexao,$autorizacao,$indicadorSelecionado,$entradaIndicador,hash('sha256',$confUsuario['dbHost'].'|'.$confUsuario['dbDatabase']));
    }
if ($acao === 'commercialOrderPdf') {
    $resultado = dashboardComercialPedidoImpressao($conexao, $indicadorSelecionado, $autorizacao, $entradaIndicador, $erro);
} elseif ($acao === 'commercialClientHistory') {
    $resultado = dashboardComercialHistoricoCliente($conexao, $indicadorSelecionado, $empresa, $entradaIndicador, $erro);
} elseif ($acao === 'cotacoesDocumentContext') {
    $resultado = dashboardCotacoesDocumentoContexto($conexao, $indicadorSelecionado, $autorizacao, $_SESSION, $entradaIndicador, $erro);
} elseif ($acao === 'titleContext') {
    $resultado = dashboardTituloContexto($conexao, $indicadorSelecionado, $autorizacao, $_SESSION, $entradaIndicador, $confUsuario, $erro);
} else {
    $resultado = $acao === 'agendaContext'
        ? dashboardAgendaContexto($conexao, $indicadorSelecionado, $autorizacao, $_SESSION, $entradaIndicador, $erro)
        : dashboardIndicadorExecutar($conexao, $indicadorSelecionado, $empresa, $entradaIndicador, $erro);
}
    if (dashboardCargaAtiva()) {
        if ($resultado !== false && empty($GLOBALS['dashboardCarga']['estado']['semSnapshot'])) $resultado['cargaToken'] = $GLOBALS['dashboardCarga']['token'];
        dashboardCargaFinalizar($resultado !== false);
    }
} catch (DashboardCargaPendente $e) {
    $progresso=$GLOBALS['dashboardCarga']['progresso'];dashboardCargaFinalizar();
    @mysqli_rollback($conexao);mysqli_close($conexao);
    dashboardResponder(202,array('carregamento'=>$progresso));
} catch (\RuntimeException $e) {
    dashboardSqlExcecao($e);
    dashboardCargaFinalizar();@mysqli_rollback($conexao);mysqli_close($conexao);
    dashboardResponder(422,array('error'=>$e->getMessage()));
} catch (\Throwable $e) {
    dashboardSqlExcecao($e);
    dashboardCargaFinalizar();@mysqli_rollback($conexao);mysqli_close($conexao);
    dashboardResponder(500, array('error' => 'Não foi possível concluir o indicador.'));
}
@mysqli_rollback($conexao);
mysqli_close($conexao);

if ($resultado === false) {
    dashboardResponder(422, array('error' => $erro));
}

if ($acao === 'commercialOrderPdf') {
    $motorDocumento = dirname(__DIR__) . '/portal-representante/documentos.php';
    if (!is_file($motorDocumento)) dashboardResponder(503, array('error'=>'O motor de impressão precisa ser atualizado.'));
    require_once $motorDocumento;
    if (!function_exists(__NAMESPACE__ . '\\portalRepresentanteDocumentoDisponibilidadePdf'))
        dashboardResponder(503, array('error'=>'O motor de impressão precisa ser atualizado.'));
    try {
        $pdf = portalRepresentanteDocumentoDisponibilidadePdf($confUsuario,
            array('empresa'=>(int) $empresa,'painel'=>array('impressoes'=>array('disponibilidadePedido'=>$resultado['tela']))), $resultado);
    } catch (\Throwable $e) {
        dashboardResponder(503, array('error'=>'Não foi possível preparar a impressão de disponibilidade agora. Tente novamente.'));
    }
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="disponibilidade-pedido-' . (int) $resultado['id'] . '.pdf"');
    header('X-Content-Type-Options: nosniff');
    echo $pdf; exit;
}

$resultado['updatedAt'] = date('d/m/Y H:i');
if (dashboardClientesInativosHabilitado($indicadorSelecionado)) {
    $resultado['indicador']['listagem']['atualizarVendedor'] = dashboardClientesInativosPodeAtualizar($autorizacao)
        && $restricaoUsuario === false;
    if ($resultado['indicador']['listagem']['atualizarVendedor']) {
        foreach ($resultado['listagem'] as &$linhaCliente) {
            if (!empty($linhaCliente['vendedorSugeridoId']) && $linhaCliente['situacao'] === 'Inativo') {
                $linhaCliente['acaoVendedor'] = dashboardClientesInativosAssinar($linhaCliente, $indicadorId, $autorizacao, $confUsuario);
            }
        }
        unset($linhaCliente);
    }
}
dashboardResponder(200, $resultado);
