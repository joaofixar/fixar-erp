<?php
namespace hardness;
if (realpath(isset($_SERVER['SCRIPT_FILENAME']) ? $_SERVER['SCRIPT_FILENAME'] : '') === __FILE__) { http_response_code(404); exit; }

require_once __DIR__ . '/../dashboard-personalizado/portal-representantes.php';

// A visualização pertence à sessão interna do ERP, nunca ao login do representante.
function portalRepresentanteGestorContexto($conf, $sessao, $token, $agora = null)
{
    $agora = $agora === null ? time() : $agora;
    if (!is_string($token) || !preg_match('/^[a-f0-9]{64}$/D', $token)
        || empty($sessao['portalGestor'][$token])) return false;
    $contexto = $sessao['portalGestor'][$token];
    $autorizacao = dashboardPersonalizadoValidarAutorizacao($contexto['autorizacao'], $conf);
    if (!$autorizacao || empty($contexto['indicador']) || !dashboardPortalRepresentantesPodeGerenciar($autorizacao, $sessao, $contexto['indicador'])
        || empty($sessao['Usuario_Atual']) || empty($sessao['Empresa_Atual'])
        || (int) $sessao['Usuario_Atual'] !== $autorizacao['usuario'] || (int) $sessao['Empresa_Atual'] !== $autorizacao['empresa']
        || $contexto['gestor'] !== $autorizacao['usuario'] || $contexto['empresa'] !== $autorizacao['empresa']
        || !hash_equals(portalRepresentanteTenant($conf), $contexto['tenant'])
        || $contexto['inicio'] + 28800 <= $agora || $contexto['ultimo'] + 1800 <= $agora) return false;
    return $contexto;
}

function portalRepresentanteGestorConta($conf, $contexto, $registro)
{
    if (!$contexto) throw new RepresentantePortalErro('A visualização do gestor expirou. Feche esta janela e abra o portal novamente pelo dashboard.', 403);
    if (!is_array($registro) || !isset($registro['D021_Id'], $registro['D021_Ativo']) || $registro['D021_Ativo'] !== 'S'
        || (int) $registro['D021_Id'] !== $contexto['representante'])
        throw new RepresentantePortalErro('Este representante não está mais ativo. Feche a visualização e confira o cadastro no dashboard.', 403);
    return array('empresa' => $contexto['empresa'], 'representante' => $contexto['representante'], 'nome' => (string) $registro['D021_Nome_Empresa']);
}

function portalRepresentanteGestorSomenteComerciais($contexto)
{
    if (empty($contexto['empresa']) || empty($contexto['indicador'])) return false;
    $configuracao = dashboardPersonalizadoConfiguracao($contexto['empresa'], $erros);
    if ($configuracao === false) throw new RepresentantePortalErro('Não foi possível carregar a configuração do dashboard. Feche esta janela e abra o portal novamente.', 403);
    foreach ($configuracao['indicadores'] as $indicador) {
        if ($indicador['id'] !== $contexto['indicador']) continue;
        $visoes = array($indicador);
        foreach (isset($indicador['abas']) ? $indicador['abas'] : array() as $aba) $visoes[] = array_merge($indicador, $aba);
        foreach ($visoes as $visao) {
            if (dashboardPortalRepresentantesHabilitado($visao)
                && isset($visao['portalRepresentantes']['somenteComerciais'])
                && $visao['portalRepresentantes']['somenteComerciais'] === true) return true;
        }
        return false;
    }
    throw new RepresentantePortalErro('O indicador não está mais disponível. Feche esta janela e abra o portal novamente pelo dashboard.', 403);
}

function portalRepresentanteGestorRepresentantes($conexao, $contexto = array())
{
    // Usa a configuração atual do indicador que abriu a visualização, inclusive
    // quando o portal está em uma aba. D021A só existe nas instalações optantes.
    $filtroComercial = portalRepresentanteGestorSomenteComerciais($contexto)
        ? " AND EXISTS (SELECT 1 FROM D021A WHERE D021A_D021_Id = D021_Id AND D021A_Flag_Comercial = 'C')" : '';
    $linhas = portalRepresentanteConsultar($conexao, 'SELECT D021_Id AS id,D021_Nome_Empresa AS nome FROM D021
        WHERE D021_Ativo=\'S\'' . $filtroComercial . ' ORDER BY D021_Nome_Empresa,D021_Id LIMIT 5001');
    if (count($linhas) > 5000) throw new RepresentantePortalErro('O cadastro excede o limite desta visão. Contate o suporte para ampliar a consulta.', 422);
    return $linhas;
}

function portalRepresentanteGestorAbrir($conf, &$sessao, $autorizacao, $registro, $assinatura, $indicadorId)
{
    if (!is_array($registro) || empty($registro['D021_Id']) || !isset($registro['D021_Ativo']) || $registro['D021_Ativo'] !== 'S')
        throw new RepresentantePortalErro('Selecione um representante ativo no cadastro.', 422);
    // A consulta interna é autorizada pelo gestor, independentemente do convite externo.
    $contexto = array('empresa' => (int) $autorizacao['empresa'], 'representante' => (int) $registro['D021_Id'],
        'gestor' => (int) $autorizacao['usuario'], 'indicador' => $indicadorId, 'tenant' => portalRepresentanteTenant($conf), 'inicio' => time(), 'ultimo' => time());
    portalRepresentanteEstado($conf, function (&$estado) use ($contexto) {
        $evento = array('usuario' => $contexto['gestor'], 'empresa' => $contexto['empresa'], 'representante' => $contexto['representante'], 'em' => time());
        if (!isset($estado['visualizacoesGestor'])) $estado['visualizacoesGestor'] = array();
        $estado['visualizacoesGestor'][] = $evento;
        $estado['visualizacoesGestor'] = array_slice($estado['visualizacoesGestor'], -100);
        foreach ($estado['acessos'] as &$acesso) {
            if (!empty($acesso['ativo']) && (int) $acesso['empresa'] === $contexto['empresa'] && (int) $acesso['representante'] === $contexto['representante'])
                $acesso['ultimaVisualizacaoGestor'] = array('usuario' => $contexto['gestor'], 'em' => $evento['em']);
        }
    });
    if (!isset($sessao['portalGestor'])) $sessao['portalGestor'] = array();
    foreach ($sessao['portalGestor'] as $id => $anterior) {
        if ($anterior['inicio'] + 28800 <= time() || $anterior['ultimo'] + 1800 <= time()) unset($sessao['portalGestor'][$id]);
    }
    while (count($sessao['portalGestor']) >= 10) array_shift($sessao['portalGestor']);
    $token = bin2hex(random_bytes(32));
    $contexto['csrf'] = bin2hex(random_bytes(32));
    $contexto['autorizacao'] = $assinatura;
    $sessao['portalGestor'][$token] = $contexto;
    return array('contexto' => $token, 'url' => '../portal-representante/?gestor=' . $token,
        'representante' => array('id' => $contexto['representante'], 'nome' => (string) $registro['D021_Nome_Empresa']));
}
