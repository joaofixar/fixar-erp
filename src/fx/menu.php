<?php
namespace hardness;

// A Loja de apps fica em Configuração para o usuário 63 ou o usuário de suporte.
// O flag de sessão do gVerificaRoot não entra: um Diretor não herda o root.
// O item de criar app sai. O grupo Apps entra depois de Gerencial.
// Cada atalho abre o endereço de hoje numa aba nova do navegador.

function fxEhRoot(array $acesso): bool
{
    $usuario = isset($acesso['usuario']) ? (string) $acesso['usuario'] : '';
    $suporte = isset($acesso['suporte']) ? (string) $acesso['suporte'] : '';
    return $usuario === '63' || $suporte === 'S';
}

function fxTemPermissao(array $acesso, int $id): bool
{
    $ids = isset($acesso['permissoes']) && is_array($acesso['permissoes']) ? $acesso['permissoes'] : array();
    return !empty($ids[$id]) || !empty($ids[(string) $id]);
}

function fxAbrirFora(string $url): string
{
    $segura = str_replace(array('\\', "'"), array('\\\\', "\\'"), $url);
    return "window.open('{$segura}', '_blank', 'noopener,noreferrer');";
}

function fxAcaoDaLoja(): string
{
    return "abrirJanela(event, 'Loja Apps', '', menuId('lojaAppsCliente'), 'Loja Apps', '.: Loja Apps :.', '/sistema/personalizacaoLowCode/content/appsListarCliente/', '', 'full', false, 'topleft', false);";
}

function fxEhCriarApp($nome, $valor): bool
{
    if ((string) $nome === 'Loja Apps') {
        return true;
    }
    $texto = is_array($valor) ? implode(' ', array_map('strval', $valor)) : (string) $valor;
    return strpos($texto, "content/appsListar/'") !== false
        || strpos($texto, 'content/appsListar/",') !== false;
}

function fxItensApps(array $acesso): array
{
    $usuario = isset($acesso['usuario']) ? (int) $acesso['usuario'] : 0;
    if ($usuario <= 0) {
        return array();
    }
    $empresa = isset($acesso['empresa']) ? (int) $acesso['empresa'] : 0;
    $nivel = isset($acesso['nivel']) ? (int) $acesso['nivel'] : 0;
    $itens = array();
    if (fxTemPermissao($acesso, 304)) {
        $itens['Integrações'] = fxAbrirFora('/hardness3/integradores/');
    }
    if (fxTemPermissao($acesso, 306) && $nivel === 4) {
        $itens['Conexões MCP'] = fxAbrirFora('/gerencial/mcp/content/McpAdminStandalone/?ajax=true');
    }
    if (fxTemPermissao($acesso, 307)) {
        $itens['Webhooks'] = fxAbrirFora('/gerencial/webhooks/content/WebhooksAdminContent/?ajax=true');
    }
    if (fxTemPermissao($acesso, 203) && fxTemPermissao($acesso, 308)) {
        $itens['Apontamento PCP'] = fxAbrirFora('/apontamento/');
    }
    if (fxTemPermissao($acesso, 310)) {
        $itens['GNRE'] = fxAbrirFora(
            '/hardness3/aplicacoes/fiscal/gnre/public/?view=list&gnre_company=' . $empresa
        );
    }
    $itens['Reforma Tributária'] = fxAbrirFora('/hardness3/aplicacoes/fiscal/reformaTributaria/public/');
    $dashboard = isset($acesso['dashboard']) ? (string) $acesso['dashboard'] : '';
    if ($dashboard !== '') {
        $itens['Dashboard'] = fxAbrirFora($dashboard);
    }
    $painel = isset($acesso['painel']) ? (string) $acesso['painel'] : '';
    if ($painel !== '') {
        $itens['Painel TV'] = fxAbrirFora($painel);
    }
    return $itens;
}

function fxGrupoDepois(array $menu, string $depois, string $antes, string $nome, array $itens): array
{
    unset($menu[$nome]);
    $novo = array();
    $posto = false;
    $temDepois = isset($menu[$depois]);
    foreach ($menu as $grupo => $entradas) {
        if (!$posto && !$temDepois && $grupo === $antes) {
            $novo[$nome] = $itens;
            $posto = true;
        }
        $novo[$grupo] = $entradas;
        if ($grupo === $depois) {
            $novo[$nome] = $itens;
            $posto = true;
        }
    }
    if (!$posto) {
        $novo[$nome] = $itens;
    }
    return $novo;
}

function fxMontarMenu(array $menu, array $acesso): array
{
    foreach ($menu as $grupo => $itens) {
        if (!is_array($itens)) {
            continue;
        }
        foreach ($itens as $nome => $valor) {
            if (fxEhCriarApp($nome, $valor)) {
                unset($menu[$grupo][$nome]);
            }
        }
    }
    if (fxEhRoot($acesso)) {
        if (!isset($menu['Configuração']) || !is_array($menu['Configuração'])) {
            $menu['Configuração'] = array();
        }
        $menu['Configuração']['Loja de apps'] = fxAcaoDaLoja();
    }
    $apps = fxItensApps($acesso);
    if ($apps) {
        return fxGrupoDepois($menu, 'Gerencial', 'Outros', 'Apps', $apps);
    }
    unset($menu['Apps']);
    return $menu;
}

function fxLinhaUsuario($g): array
{
    if (isset($g['C007']) && is_array($g['C007'])) {
        return $g['C007'];
    }
    if (isset($g['c007']) && is_array($g['c007'])) {
        return $g['c007'];
    }
    return array();
}

function fxCarregarAtalhos(): void
{
    $raiz = dirname(__DIR__) . '/hardness3';
    $arquivos = array(
        $raiz . '/dashboard-personalizado/config.php',
        $raiz . '/dashboard-personalizado/access.php',
        $raiz . '/painel-personalizado/config.php',
        $raiz . '/painel-personalizado/session.php',
    );
    foreach ($arquivos as $arquivo) {
        if (is_file($arquivo)) {
            require_once $arquivo;
        }
    }
}

function fxUrlDashboard($g): string
{
    fxCarregarAtalhos();
    if (!function_exists('dashboardPersonalizadoHabilitado')
        || !function_exists('dashboardPersonalizadoPodeAcessar')
        || !function_exists('dashboardPersonalizadoCriarTokenSessao')) {
        return '';
    }
    $empresa = isset($g['empresaAtual']) ? (int) $g['empresaAtual'] : 0;
    $linha = fxLinhaUsuario($g);
    $flag = isset($linha['C007_Flag_Vendedor']) ? (string) $linha['C007_Flag_Vendedor'] : '';
    $autorizacao = array(
        'usuario' => isset($g['usuarioAtual']) ? (int) $g['usuarioAtual'] : 0,
        'perfil' => isset($g['perfilUsuario']) ? (int) $g['perfilUsuario'] : 0,
        'root' => !empty($g['isRoot']) || (isset($g['usuarioAtual']) && (int) $g['usuarioAtual'] === 63),
        'diretor' => (isset($g['nivelAlcada']) && (int) $g['nivelAlcada'] === 4) || $flag === 'D',
    );
    if (!dashboardPersonalizadoHabilitado($empresa) || !dashboardPersonalizadoPodeAcessar($autorizacao, $empresa)) {
        return '';
    }
    $token = dashboardPersonalizadoCriarTokenSessao();
    if (!is_string($token) || $token === '') {
        return '';
    }
    return '/hardness3/dashboard-personalizado/#session=' . $token;
}

function fxUrlPainel(): string
{
    fxCarregarAtalhos();
    if (!function_exists('painelPersonalizadoContexto')
        || !function_exists('painelPersonalizadoConfiguracao')
        || !function_exists('painelPersonalizadoPermitidos')
        || !function_exists('painelPersonalizadoCriarTokenSessao')) {
        return '';
    }
    $contexto = painelPersonalizadoContexto();
    $config = painelPersonalizadoConfiguracao(isset($contexto['empresa']) ? $contexto['empresa'] : 0);
    if (!painelPersonalizadoPermitidos($config, $contexto)) {
        return '';
    }
    $token = painelPersonalizadoCriarTokenSessao();
    if (!is_string($token) || $token === '') {
        return '';
    }
    return '/hardness3/painel.php#session=' . $token;
}

function fxAcessoDoShell($g): array
{
    $linha = fxLinhaUsuario($g);
    return array(
        'usuario' => isset($g['usuarioAtual']) ? $g['usuarioAtual'] : 0,
        'suporte' => isset($linha['C007_Usuario_Suporte']) ? $linha['C007_Usuario_Suporte'] : '',
        'permissoes' => isset($g['c029Ids']) && is_array($g['c029Ids']) ? $g['c029Ids'] : array(),
        'nivel' => isset($g['nivelAlcada']) ? $g['nivelAlcada'] : 0,
        'empresa' => isset($g['empresaAtual']) ? $g['empresaAtual'] : 0,
    );
}

function fxMenuDoShell($menu, $g): array
{
    if (!is_array($menu)) {
        $menu = array();
    }
    if (!is_array($g)) {
        $g = array();
    }
    $acesso = fxAcessoDoShell($g);
    $acesso['dashboard'] = fxUrlDashboard($g);
    $acesso['painel'] = fxUrlPainel();
    return fxMontarMenu($menu, $acesso);
}
