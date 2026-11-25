<?php
// O menu que a sidebar e a paleta listam: loja só para o root, grupo Apps no lugar.
if (PHP_SAPI !== 'cli') {
    exit(1);
}

require_once dirname(__DIR__) . '/src/fx/menu.php';

function falhar($mensagem)
{
    fwrite(STDERR, "FALHOU: {$mensagem}\n");
    exit(1);
}

function exigir($ok, $mensagem)
{
    if (!$ok) {
        falhar($mensagem);
    }
}

function menuDeHoje()
{
    return array(
        'Cadastros' => array(
            'Cliente/Fornecedor' => "abrirJanela(event, divIdRootAberto(), '', menuId('cad001'), 'Clientes e Fornecedores', '.: Clientes e Fornecedores :.', '/cad/cad001/content/cad001ContentPrincipal/', '', 'auto', false, 'center');",
        ),
        'Gerencial' => array(
            'Relatórios' => "abrirJanela(event, 'relatorios', '', menuId('relatorios'), 'Relatórios', '.: Relatórios :.', '/rel/menu/menu/content/', '', 'full', false, 'topleft', false);",
        ),
        'Outros' => array(
            'Log Erros' => "abrirJanela(event, divIdRootAberto(), '', menuId('LogErros'), 'Log Erros', '.: Log Erros :.', '/sistema/logSistema/outro/contentErros/', '', [1024,600], false, 'full');",
        ),
        'Configuração' => array(
            'Usuários/Perfis' => "abrirJanela(event, divIdRootAberto(), '', menuId('cad030'), 'Usuários', '.: Usuários :.', '/cad/cad030/content/cad030ContentPrincipal/', '', [950,550], false, false, 'center');",
            'Loja Apps' => "abrirJanela(event, 'Loja Apps', '', menuId('lojaApps'), 'Loja Apps', '.: Loja Apps :.', '/sistema/personalizacaoLowCode/content/appsListar/', '', 'full', false, 'topleft', false);",
            'IDE' => "abrirJanela(event, 'IDE', '', menuId('grids'), 'IDE', '.: IDE :.', '/sistema/personalizacaoLowCode/content/ide/', '', 'full', false, 'topleft', false);",
        ),
    );
}

function acesso($usuario, $suporte, array $permissoes, $nivel, $empresa, $dashboard, $painel, $diretor)
{
    return array(
        'usuario' => $usuario,
        'suporte' => $suporte,
        'permissoes' => $permissoes,
        'nivel' => $nivel,
        'empresa' => $empresa,
        'dashboard' => $dashboard,
        'painel' => $painel,
        'diretor' => $diretor,
    );
}

function nomes($menu, $grupo)
{
    if (!isset($menu[$grupo]) || !is_array($menu[$grupo])) {
        return array();
    }
    return array_keys($menu[$grupo]);
}

function acao($menu, $grupo, $item)
{
    return isset($menu[$grupo][$item]) ? (string) $menu[$grupo][$item] : '';
}

function temCriarApp($menu)
{
    foreach ($menu as $itens) {
        if (!is_array($itens)) {
            continue;
        }
        foreach ($itens as $nome => $valor) {
            $texto = is_array($valor) ? implode(' ', $valor) : (string) $valor;
            if ($nome === 'Loja Apps' || strpos($texto, "content/appsListar/'") !== false || strpos($texto, 'content/appsListar/",') !== false) {
                return true;
            }
        }
    }
    return false;
}

$diretor = hardness\fxMontarMenu(
    menuDeHoje(),
    acesso(12, '', array(), 4, 3, '', '', true)
);
exigir(nomes($diretor, 'Configuração') === array('Usuários/Perfis', 'IDE'), 'perfil Diretor não vê a Loja de apps');
exigir(temCriarApp($diretor) === false, 'o item de criar app sai do menu');
exigir(nomes($diretor, 'Apps') === array('Reforma Tributária'), 'quem está logado vê Reforma Tributária e o grupo');
exigir(
    array_keys($diretor) === array('Cadastros', 'Gerencial', 'Apps', 'Outros', 'Configuração'),
    'o grupo Apps fica depois de Gerencial e antes de Outros'
);
exigir(strpos(acao($diretor, 'Apps', 'Reforma Tributária'), 'abrirJanela') === false, 'Reforma Tributária não vira aba do desktop');
exigir(
    acao($diretor, 'Apps', 'Reforma Tributária') === "window.open('/hardness3/aplicacoes/fiscal/reformaTributaria/public/', '_blank', 'noopener,noreferrer');",
    'Reforma Tributária abre o endereço de hoje numa aba nova'
);

$root = hardness\fxMontarMenu(
    menuDeHoje(),
    acesso(63, '', array(304 => 1, 306 => 1, 307 => 1, 203 => 1, 308 => 1, 310 => 1), 4, 9, '/hardness3/dashboard-personalizado/#session=abc', '/hardness3/painel.php#session=def', false)
);
exigir(in_array('Loja de apps', nomes($root, 'Configuração'), true), 'usuário 63 vê a Loja de apps em Configuração');
exigir(temCriarApp($root) === false, 'o root também não vê o item de criar app');
exigir(
    acao($root, 'Configuração', 'Loja de apps') === "abrirJanela(event, 'Loja Apps', '', menuId('lojaAppsCliente'), 'Loja Apps', '.: Loja Apps :.', '/sistema/personalizacaoLowCode/content/appsListarCliente/', '', 'full', false, 'topleft', false);",
    'a Loja de apps abre o catálogo de produção, com Obter, Instalar e Atualizar'
);
exigir(
    nomes($root, 'Apps') === array(
        'Integrações',
        'Conexões MCP',
        'Webhooks',
        'Apontamento PCP',
        'GNRE',
        'Reforma Tributária',
        'Dashboard',
        'Painel TV',
    ),
    'a ordem do grupo é a combinada'
);
exigir(
    acao($root, 'Apps', 'Integrações') === "window.open('/hardness3/integradores/', '_blank', 'noopener,noreferrer');",
    'Integrações abre o painel de hoje'
);
exigir(
    acao($root, 'Apps', 'Conexões MCP') === "window.open('/gerencial/mcp/content/McpAdminStandalone/?ajax=true', '_blank', 'noopener,noreferrer');",
    'Conexões MCP abre a administração de hoje'
);
exigir(
    acao($root, 'Apps', 'Webhooks') === "window.open('/gerencial/webhooks/content/WebhooksAdminContent/?ajax=true', '_blank', 'noopener,noreferrer');",
    'Webhooks abre a administração de hoje'
);
exigir(
    acao($root, 'Apps', 'Apontamento PCP') === "window.open('/apontamento/', '_blank', 'noopener,noreferrer');",
    'Apontamento PCP abre o endereço de hoje'
);
exigir(
    acao($root, 'Apps', 'GNRE') === "window.open('/hardness3/aplicacoes/fiscal/gnre/public/?view=list&gnre_company=9', '_blank', 'noopener,noreferrer');",
    'GNRE abre o endereço de hoje da empresa'
);
exigir(
    acao($root, 'Apps', 'Dashboard') === "window.open('/hardness3/dashboard-personalizado/#session=abc', '_blank', 'noopener,noreferrer');",
    'Dashboard abre o endereço de hoje'
);
exigir(
    acao($root, 'Apps', 'Painel TV') === "window.open('/hardness3/painel.php#session=def', '_blank', 'noopener,noreferrer');",
    'Painel TV abre o endereço de hoje'
);
foreach (nomes($root, 'Apps') as $item) {
    exigir(strpos(acao($root, 'Apps', $item), 'abrirJanela') === false, $item . ' não vira aba do desktop');
}

$suporte = hardness\fxMontarMenu(
    menuDeHoje(),
    acesso(8, 'S', array(), 1, 3, '', '', true)
);
exigir(in_array('Loja de apps', nomes($suporte, 'Configuração'), true), 'usuário de suporte vê a Loja de apps');
exigir(temCriarApp($suporte) === false, 'o suporte não vê o item de criar app');

$semMcp = hardness\fxMontarMenu(
    menuDeHoje(),
    acesso(4, '', array(306 => 1, 203 => 1), 2, 3, '', '', false)
);
exigir(
    nomes($semMcp, 'Apps') === array('Reforma Tributária'),
    'sem a permissão de hoje o item não entra, e a alçada abaixo de 4 não vê Conexões MCP'
);

$semPainel = hardness\fxMontarMenu(
    menuDeHoje(),
    acesso(4, '', array(304 => 1, 307 => 1, 308 => 1, 310 => 1), 4, 3, '', '', false)
);
exigir(
    nomes($semPainel, 'Apps') === array('Integrações', 'Webhooks', 'GNRE', 'Reforma Tributária'),
    'Apontamento PCP pede as duas permissões, e Dashboard e Painel TV só entram com o endereço de hoje'
);

$visitante = hardness\fxMontarMenu(menuDeHoje(), acesso(0, '', array(), 0, 0, '', '', false));
exigir(!isset($visitante['Apps']), 'sem login não há grupo Apps');
exigir(temCriarApp($visitante) === false, 'sem login o item de criar app também sai');

$gDiretor = array(
    'usuarioAtual' => 12,
    'empresaAtual' => 3,
    'perfilUsuario' => 4,
    'nivelAlcada' => 4,
    'c029Ids' => array(),
    'C007' => array(
        'C007_Flag_Vendedor' => 'D',
        'C007_Usuario_Suporte' => 'N',
    ),
);
$trocado = hardness\fxMenuDoShell(menuDeHoje(), $gDiretor);
exigir(
    nomes($trocado, 'Configuração') === array('Usuários/Perfis', 'IDE'),
    'perfil Diretor não vê a Loja de apps, mesmo com alçada 4'
);

echo "A loja fica em Configuração para o root, e o grupo Apps abre os atalhos de hoje.\n";
