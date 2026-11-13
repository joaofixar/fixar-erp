<?php
namespace hardness;

/* Mantém diagnóstico no log do servidor mesmo quando o PHP só devolve HTTP 500. */
if (!defined('INTEGRADORES_DIAGNOSTICO_HTTP_500')) {
    define('INTEGRADORES_DIAGNOSTICO_HTTP_500', true);
    register_shutdown_function(function () {
        $erro = error_get_last();
        if ($erro && in_array($erro['type'], array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR), true)) {
            error_log('Integrações: erro fatal em ' . $erro['file'] . ':' . $erro['line'] . ' - ' . $erro['message']);
            return;
        }
        if (http_response_code() >= 500) {
            error_log('Integrações: resposta HTTP ' . http_response_code() . ' sem erro fatal informado pelo PHP. URI: ' . (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : 'não informada'));
        }
    });
}

require_once dirname(__DIR__) . '/bibliotecas/webservices/iset/iset_logs.php';
require_once dirname(__DIR__) . '/bibliotecas/webservices/iset/iset_permissoes.php';

if (PHP_SAPI !== 'cli' && session_id() === '') {
    session_start();
}

const INTEGRADORES_C029_ACESSAR_PAINEL = 304;
const INTEGRADORES_C029_CONFIGURACOES = 305;

function integradores_usuario_tem_permissao($permissaoId)
{
    return (new ISET_PERMISSOES())->usuarioTemPermissao($permissaoId);
}

function integradores_bloquear_acesso($mensagem)
{
    http_response_code(403);
    integradores_header('Acesso restrito');
    ?>
    <div class="integradores-acesso-dialog" role="dialog" aria-modal="true" aria-labelledby="integradores-acesso-titulo">
        <section class="integradores-acesso-dialog__conteudo">
            <span class="material-symbols-outlined" aria-hidden="true">lock</span>
            <h2 id="integradores-acesso-titulo">Acesso não permitido</h2>
            <p><?php echo integradores_h($mensagem); ?></p>
            <a class="integradores-button" href="/erp">Voltar ao sistema</a>
        </section>
    </div>
    <?php
    integradores_footer();
    exit;
}

function integradores_exigir_permissao($permissaoId, $mensagem)
{
    if (!integradores_usuario_tem_permissao($permissaoId)) {
        integradores_bloquear_acesso($mensagem);
    }
}

function integradores_base_path($path = '')
{
    $scriptName = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '';
    $modulePath = '/integradores';
    $position = strpos($scriptName, $modulePath);
    $prefix = $position === false ? '' : substr($scriptName, 0, $position);
    $base = rtrim($prefix, '/') . $modulePath;

    return $path === '' ? $base : $base . '/' . ltrim($path, '/');
}

function integradores_h($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/** Navegação local do catálogo, sem misturar a configuração de API com campos de produto. */
function integradores_iset_produtos_abas($ativa)
{
    $abas = array(
        'produtos' => array('Produtos Hardness', 'iset/produtos.php', 'inventory_2'),
        'configuracoes' => array('Configurações', 'iset/produtos_configuracoes.php', 'tune'),
        'pendencias' => array('Pendências', 'iset/produtos_pendencias.php', 'pending_actions'),
        'logs' => array('Sincronizações', 'iset/produtos_logs.php', 'sync'),
    );
    ?>
    <section class="iset-produtos-cabecalho">
        <h1>Produtos</h1>
        <nav class="iset-produtos-abas" aria-label="Produtos ISET">
            <?php foreach ($abas as $chave => $aba): ?>
                <a class="<?php echo $chave === $ativa ? 'active' : ''; ?>" href="<?php echo integradores_h(integradores_base_path($aba[1])); ?>"><span class="material-symbols-outlined" aria-hidden="true"><?php echo integradores_h($aba[2]); ?></span><span><?php echo integradores_h($aba[0]); ?></span></a>
            <?php endforeach; ?>
        </nav>
    </section>
    <style>
        .integradores-content{width:min(1280px,calc(100% - 48px));max-width:1280px}
        .iset-produtos-cabecalho{min-height:160px;margin:0 0 18px;padding:25px 26px 18px;border:1px solid #d6e0ef;border-top:4px solid #2860dc;border-radius:17px;background:#fff;box-shadow:0 12px 30px rgba(25,55,100,.07)}
        .iset-produtos-cabecalho~.integradores-card{width:100%;max-width:none}
        .iset-produtos-cabecalho h1{margin:0 0 26px;color:#07224d;font-size:27px;line-height:1.1}
        .iset-produtos-abas{display:flex;flex-wrap:wrap;gap:9px}
        .iset-produtos-abas a{display:inline-flex;align-items:center;gap:9px;min-height:48px;padding:8px 15px;border:1px solid #cbd8ea;border-radius:10px;color:#07224d;background:#fff;font-size:12px;font-weight:700;text-decoration:none;transition:border-color .18s ease,background-color .18s ease,color .18s ease}
        .iset-produtos-abas a:hover{border-color:#2860dc;color:#2860dc;background:#edf3ff}
        .iset-produtos-abas .material-symbols-outlined{font-size:21px}
        .iset-produtos-abas a.active{border-color:#2860dc;color:#fff;background:#2860dc}
        .iset-produtos-abas a.active:hover{color:#fff;background:#1554d1}
        @media(max-width:760px){.integradores-content{width:calc(100% - 24px)}.iset-produtos-cabecalho{min-height:0;padding:21px 18px}.iset-produtos-cabecalho h1{margin-bottom:18px}.iset-produtos-abas a{flex:1 1 190px}}
    </style>
    <?php
}

/**
 * A INT001 usa o horário comercial do ERP: Brasília.
 */
function integradores_formatar_data_hora($data, $hora, $formato = 'd/m/Y H:i:s')
{
    $original = trim((string) $data . ' ' . (string) $hora);
    try {
        $dataHora = new \DateTime($original, new \DateTimeZone('America/Sao_Paulo'));
        return $dataHora->format($formato);
    } catch (\Exception $e) {
        return $original;
    }
}

/**
 * Webhooks antigos têm no JSON o instante original, incluindo o fuso horário.
 * Ele é mais confiável que a coluna legada quando banco e servidor usam fusos distintos.
 */
function integradores_formatar_data_hora_log(array $log, $formato = 'd/m/Y H:i:s')
{
    $dados = isset($log['INT001_Log']) ? json_decode($log['INT001_Log'], true) : null;
    $recebidoEm = is_array($dados) && isset($dados['requisicao']['recebidoEm']) ? $dados['requisicao']['recebidoEm'] : '';
    if ($recebidoEm !== '') {
        try {
            $dataHora = new \DateTime($recebidoEm);
            $dataHora->setTimezone(new \DateTimeZone('America/Sao_Paulo'));
            return $dataHora->format($formato);
        } catch (\Exception $e) {
        }
    }

    return integradores_formatar_data_hora($log['INT001_Data'], $log['INT001_Hora'], $formato);
}

function integradores_header($title, $activePage = '', $integracaoAtual = '')
{
    global $g;
    $items = array(
        'inicio' => array('Início', 'iset/', 'home'),
        'importar' => array('Importar', 'iset/importar.php', 'file_download'),
        'produtos' => array('Produtos', 'iset/produtos.php', 'inventory_2'),
        'configuracoes' => array('Configurações', 'iset/configuracoes.php', 'settings'),
        'logs' => array('Logs', 'iset/logs.php', 'receipt_long'),
        'automacao' => array('Automação', 'iset/automacao.php', 'bolt'),
    );
    $temAcessoPainel = integradores_usuario_tem_permissao(INTEGRADORES_C029_ACESSAR_PAINEL);
    if (!integradores_usuario_tem_permissao(INTEGRADORES_C029_CONFIGURACOES)) {
        unset($items['configuracoes']);
    }
    $empresaAtualNome = isset($g['C004']['C004_Nome_Empresa']) ? trim((string) $g['C004']['C004_Nome_Empresa']) : '';
    if ($empresaAtualNome === '' && isset($g['C004']['C004_Nome_Abreviado'])) $empresaAtualNome = trim((string) $g['C004']['C004_Nome_Abreviado']);
    ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,400,0,0">
    <link rel="icon" type="image/x-icon" href="/hardness3/favicon.ico">
    <?php $browserTitle = $title === 'Integrações' ? $title : $title . ' | Integrações'; ?>
    <title><?php echo integradores_h($browserTitle); ?></title>
    <style>
        :root { --navy: #07224d; --blue: #2860dc; --line: #d6e0ef; --muted: #617495; --surface: #fff; --background: #f3f7fc; --shadow: 0 12px 30px rgba(25, 55, 100, .07); }
        * { box-sizing: border-box; }
        body { margin: 0; color: var(--navy); background: var(--background); font: 14px/1.45 Arial, sans-serif; }
        .integradores-shell { min-height: 100vh; display: grid; grid-template-columns: 270px minmax(0, 1fr); }
        .integradores-sidebar { position: sticky; top: 0; height: 100vh; padding: 26px 14px; background: var(--surface); border-right: 1px solid var(--line); display: flex; flex-direction: column; }
        .integradores-brand { min-height: 82px; margin: -26px -14px 0; padding: 0 24px; display: flex; align-items: center; justify-content: center; background: #184784; }
        .integradores-empresa-atual { display: flex; align-items: center; gap: 10px; margin: 16px 4px 5px; padding: 11px 12px; border: 1px solid #dbe5f4; border-radius: 12px; background: linear-gradient(135deg, #f8fbff, #eef4ff); color: var(--navy); text-align: left; }
        .integradores-empresa-atual__icone { display: grid; flex: 0 0 34px; width: 34px; height: 34px; place-items: center; border-radius: 9px; color: #2860dc; background: #dfeaff; }
        .integradores-empresa-atual__icone .material-symbols-outlined { font-size: 20px; }
        .integradores-empresa-atual__texto { min-width: 0; }
        .integradores-empresa-atual small { display: block; color: #617495; font-size: 10px; font-weight: 800; letter-spacing: .7px; text-transform: uppercase; }
        .integradores-empresa-atual strong { display: block; overflow: hidden; margin-top: 2px; color: var(--navy); font-size: 12px; text-overflow: ellipsis; white-space: nowrap; }
        .integradores-brand img { display: block; width: 136px; height: 34px; object-fit: contain; }
        .integradores-selector { margin-top: 22px; }
        .integradores-selector summary { position: relative; display: flex; align-items: center; justify-content: center; padding: 12px 32px; border: 1px solid var(--line); border-radius: 12px; color: var(--navy); background: #fff; cursor: pointer; font-weight: 800; list-style: none; text-align: center; }
        .integradores-selector summary::-webkit-details-marker { display: none; }
        .integradores-selector summary::after { position: absolute; right: 15px; width: 7px; height: 7px; border-right: 2px solid var(--blue); border-bottom: 2px solid var(--blue); content: ''; transform: rotate(45deg) translateY(-2px); transition: transform .18s ease; }
        .integradores-selector[open] summary::after { transform: rotate(225deg) translate(-2px, -2px); }
        .integradores-selector nav { margin-top: 7px; padding: 6px; border: 1px solid var(--line); border-radius: 12px; background: #fff; box-shadow: var(--shadow); }
        .integradores-selector nav a { display: block; margin: 2px 0; padding: 10px 12px; border: 1px solid transparent; border-radius: 9px; color: var(--navy); font-weight: 700; text-align: center; text-decoration: none; }
        .integradores-nav a { display: flex; align-items: center; justify-content: flex-start; gap: 10px; margin: 5px 0; padding: 8px 14px; border: 1px solid transparent; border-radius: 9px; color: var(--navy); font-weight: 700; text-align: left; text-decoration: none; }
        .integradores-selector nav a:hover, .integradores-nav a:hover { color: var(--blue); }
        .integradores-nav a.active { background: #edf3ff; border-color: #c8d8fa; color: var(--blue); }
        .integradores-menu-icon { display: grid; flex: 0 0 36px; width: 36px; height: 36px; place-items: center; border-radius: 10px; color: var(--blue); background: #eaf0ff; }
        .integradores-menu-icon .material-symbols-outlined { font-size: 22px; }
        .material-symbols-outlined { font-family: 'Material Symbols Outlined'; font-weight: normal; font-style: normal; line-height: 1; letter-spacing: normal; text-transform: none; white-space: nowrap; word-wrap: normal; direction: ltr; -webkit-font-feature-settings: 'liga'; -webkit-font-smoothing: antialiased; font-feature-settings: 'liga'; }
        .integradores-nav a:not(.active) .integradores-menu-icon { color: #4f6b9c; background: #f3f6fb; }
        .integradores-nav { margin-top: 12px; }
        .integradores-disponiveis { margin: 22px 4px 0; }
        .integradores-disponiveis__titulo { margin: 0 0 9px; color: #617495; font-size: 10px; font-weight: 800; letter-spacing: .8px; text-transform: uppercase; }
        .integradores-disponiveis__item { display: flex; align-items: center; gap: 10px; padding: 11px; border: 1px solid #d6e0ef; border-radius: 12px; color: var(--navy); background: #fff; box-shadow: 0 8px 18px rgba(25,55,100,.05); text-decoration: none; }
        .integradores-disponiveis__item:hover { border-color: #aecaff; background: #f4f8ff; }
        .integradores-disponiveis__item .integradores-menu-icon { flex-basis: 38px; width: 38px; height: 38px; }
        .integradores-disponiveis__texto { display: grid; gap: 1px; min-width: 0; }
        .integradores-selector .integradores-disponiveis { margin: 10px 0 0; padding: 0; border: 0; border-radius: 0; background: transparent; box-shadow: none; }
        .integradores-selector .integradores-disponiveis__item { display: flex; margin: 0; padding: 11px; border: 1px solid #d6e0ef; text-align: left; }
        .integradores-disponiveis__texto strong { font-size: 13px; }.integradores-disponiveis__texto small { color: var(--muted); font-size: 11px; }.integradores-disponiveis__seta { margin-left: auto; color: var(--blue); }
        .integradores-content { width: min(1060px, calc(100% - 48px)); margin: 0 auto; padding: 34px 0 70px; }
        .integradores-content h2 { margin: 4px 0 8px; font-size: 25px; }
        .integradores-content h3 { margin: 26px 0 6px; font-size: 17px; }
        .integradores-content p { color: var(--muted); }
        .integradores-card { max-width: 760px; min-height: 245px; padding: 26px; border: 1px solid var(--line); border-top: 4px solid var(--blue); border-radius: 17px; background: var(--surface); box-shadow: var(--shadow); }
        .integradores-acesso-dialog { position: fixed; z-index: 10; inset: 0; display: grid; place-items: center; padding: 24px; background: rgba(7, 34, 77, .28); }
        .integradores-acesso-dialog__conteudo { width: min(100%, 430px); padding: 30px; border-top: 4px solid #e13f5d; border-radius: 16px; background: #fff; box-shadow: 0 18px 50px rgba(7, 34, 77, .25); text-align: center; }
        .integradores-acesso-dialog__conteudo > .material-symbols-outlined { display: inline-grid; width: 46px; height: 46px; place-items: center; border-radius: 50%; color: #b4233d; background: #fff1f3; font-size: 25px; }
        .integradores-acesso-dialog__conteudo h2 { margin-top: 14px; }
        .integradores-acesso-dialog__conteudo p { margin: 0; }
        .integradores-button { display: inline-block; margin-top: 12px; padding: 10px 16px; border: 1px solid var(--blue); border-radius: 9px; background: var(--blue); color: #fff; font-weight: 700; text-decoration: none; transition: background-color .18s ease; }
        .integradores-button:hover { background: #1554d1; }
        .integradores-button--secundario { margin-left: 8px; border-color: #cbd8ea; background: #fff; color: var(--navy); }
        .integradores-button--secundario:hover { background: #edf3ff; }
        .integradores-configuracoes { max-width: 760px; }
        .integradores-formulario { display: grid; gap: 16px; margin-top: 24px; }
        .integradores-formulario label { display: grid; gap: 6px; color: var(--navy); font-size: 12px; font-weight: 700; }
        .integradores-formulario input { width: 100%; height: 42px; padding: 0 12px; border: 1px solid #cbd8ea; border-radius: 9px; color: var(--navy); background: #fff; font: inherit; }
        .integradores-campo-senha { position: relative; display: block; }
        .integradores-campo-senha input { padding-right: 84px; }
        .integradores-campo-senha button { position: absolute; top: 6px; right: 6px; height: 30px; padding: 0 9px; border: 0; border-radius: 7px; color: var(--blue); background: #edf3ff; font: inherit; font-size: 11px; font-weight: 700; cursor: pointer; }
        .integradores-formulario input:focus { outline: 0; border-color: var(--blue); box-shadow: 0 0 0 3px rgba(40, 96, 220, .1); }
        .integradores-formulario small { color: var(--muted); font-size: 11px; font-weight: 400; }
        .integradores-formulario code { color: var(--navy); }
        .integradores-flash { margin: 18px 0 0; padding: 11px 12px; border: 1px solid; border-radius: 9px; font-size: 12px; }
        .integradores-flash--sucesso { border-color: #b9e4d6; color: #087858; background: #ecfaf5; }
        .integradores-flash--erro { border-color: #f3c5cc; color: #b4233d; background: #fff1f3; }
        .integradores-eyebrow { margin: 0; color: #1554d1; font-size: 10px; font-weight: 800; letter-spacing: 1px; text-transform: uppercase; }
        .integradores-intro { max-width: 680px; padding: 8px 0 28px; border-bottom: 1px solid var(--line); }
        .integradores-intro h2 { margin-top: 5px; font-size: 27px; }
        .integradores-grid { display: grid; grid-template-columns: minmax(0, 480px); gap: 18px; padding-top: 26px; }
        .integration-card { position: relative; display: grid; min-height: 220px; padding: 27px; overflow: hidden; border: 3px solid #2f75ff; border-radius: 18px; color: var(--navy); background: linear-gradient(135deg, #ffffff 0%, #e6f0ff 100%); box-shadow: 0 0 0 1px rgba(136, 183, 255, .85), 0 0 22px rgba(47, 117, 255, .34), 0 16px 34px rgba(21, 84, 209, .18), inset 0 0 22px rgba(116, 174, 255, .12); text-decoration: none; transition: transform .18s ease, box-shadow .18s ease; }
        .integration-card::before { position: absolute; top: -90px; right: -55px; width: 235px; height: 235px; border-radius: 50%; background: rgba(40, 96, 220, .17); content: ''; filter: blur(8px); }
        .integration-card:hover { transform: translateY(-3px); box-shadow: 0 0 0 1px rgba(172, 207, 255, 1), 0 0 30px rgba(47, 117, 255, .48), 0 22px 42px rgba(21, 84, 209, .28), inset 0 0 26px rgba(116, 174, 255, .17); }
        .integration-card__logo { position: relative; width: min(450px, 100%); height: 126px; object-fit: contain; object-position: left center; }
        .integration-card__title, .integration-card__arrow { position: relative; }
        .integration-card__title { display: flex; align-items: center; align-self: end; gap: 30px; margin-top: 24px; }
        .integration-card__type { padding: 5px 9px; border: 1px solid #c8d8fa; border-radius: 999px; color: #1554d1; background: #edf3ff; font-size: 10px; font-weight: 800; letter-spacing: .6px; text-transform: uppercase; }
        .integration-card__title strong { font-size: 28px; letter-spacing: -.4px; }
        .integration-card__arrow { position: absolute; right: 27px; bottom: 24px; font-size: 25px; font-weight: 700; }
        @media (max-width: 760px) { .integradores-shell { grid-template-columns: 1fr; } .integradores-sidebar { position: static; width: 100%; height: auto; } .integradores-content { width: min(100% - 24px, 1060px); padding-top: 24px; } .integradores-grid { grid-template-columns: 1fr; } }
    </style>
    <script>
        function integradoresAbrirPedidoNoSistema(evento, pedidoId) {
            evento.preventDefault();

            var janelaSistema = window.open('/erp', '_blank');
            if (!janelaSistema) {
                window.alert('O navegador bloqueou a abertura da nova aba. Permita pop-ups para acessar o pedido no sistema.');
                return false;
            }

            var tentativas = 0;
            var desktopProntoEm = 0;
            var aguardarSistema = window.setInterval(function () {
                tentativas++;
                if (janelaSistema.closed || tentativas > 80) {
                    window.clearInterval(aguardarSistema);
                    return;
                }

                try {
                    var sistemaCarregado = janelaSistema.document.readyState === 'complete'
                        && janelaSistema.document.querySelector('#desktop')
                        && typeof janelaSistema.jQuery === 'function'
                        && janelaSistema.jQuery.active === 0;

                    if (!sistemaCarregado || typeof janelaSistema.abrirJanela !== 'function' || typeof janelaSistema.divIdRootAberto !== 'function' || typeof janelaSistema.unique !== 'function') {
                        desktopProntoEm = 0;
                        return;
                    }

                    if (!desktopProntoEm) {
                        desktopProntoEm = Date.now();
                        return;
                    }

                    if (Date.now() - desktopProntoEm < 700) {
                        return;
                    }

                    janelaSistema.abrirJanela(false, janelaSistema.divIdRootAberto(), '', janelaSistema.unique(), '', 'Editar Pedido', '/ven/ven002/content/ven002content02/', '&acaoId=' + encodeURIComponent(pedidoId) + '&tabela=T005', [990, 605]);
                    janelaSistema.focus();
                    window.clearInterval(aguardarSistema);
                } catch (erro) {
                    // A nova aba ainda está carregando; tenta novamente na próxima iteração.
                }
            }, 100);

            return false;
        }
    </script>
</head>
<body>
    <div class="integradores-shell">
        <aside class="integradores-sidebar">
            <a class="integradores-brand" href="/erp" title="Voltar ao sistema"><img src="/hardness3/static/img/logo_hardness.png" alt="Hardness"></a>
            <?php if ($empresaAtualNome !== ''): ?>
                <div class="integradores-empresa-atual">
                    <span class="integradores-empresa-atual__icone material-symbols-outlined" aria-hidden="true">apartment</span>
                    <span class="integradores-empresa-atual__texto"><small>Empresa atual</small><strong title="<?php echo integradores_h($empresaAtualNome); ?>"><?php echo integradores_h($empresaAtualNome); ?></strong></span>
                </div>
            <?php endif; ?>
            <?php if ($temAcessoPainel && $integracaoAtual === 'iset'): ?>
                <details class="integradores-selector">
                    <summary>ISET</summary>
                    <nav class="integradores-disponiveis" aria-label="Trocar integração">
                        <p class="integradores-disponiveis__titulo">Integrações disponíveis</p>
                        <a class="integradores-disponiveis__item" href="<?php echo integradores_h(integradores_base_path('iset/')); ?>">
                            <span class="integradores-menu-icon material-symbols-outlined" aria-hidden="true">hub</span>
                            <span class="integradores-disponiveis__texto"><strong>ISET</strong><small>Acessar integração</small></span>
                            <span class="integradores-disponiveis__seta material-symbols-outlined" aria-hidden="true">arrow_forward</span>
                        </a>
                    </nav>
                </details>
                <nav class="integradores-nav" aria-label="Menu da ISET">
                    <?php foreach ($items as $key => $item): ?>
                        <a class="<?php echo $activePage === $key ? 'active' : ''; ?>" href="<?php echo integradores_h(integradores_base_path($item[1])); ?>">
                            <span class="integradores-menu-icon" aria-hidden="true">
                                <span class="material-symbols-outlined"><?php echo $item[2]; ?></span>
                            </span>
                            <?php echo integradores_h($item[0]); ?>
                        </a>
                    <?php endforeach; ?>
                </nav>
            <?php elseif ($temAcessoPainel): ?>
                <nav class="integradores-disponiveis" aria-label="Integrações disponíveis">
                    <p class="integradores-disponiveis__titulo">Integrações disponíveis</p>
                    <a class="integradores-disponiveis__item" href="<?php echo integradores_h(integradores_base_path('iset/')); ?>">
                        <span class="integradores-menu-icon material-symbols-outlined" aria-hidden="true">hub</span>
                        <span class="integradores-disponiveis__texto"><strong>ISET</strong><small>Acessar integração</small></span>
                        <span class="integradores-disponiveis__seta material-symbols-outlined" aria-hidden="true">arrow_forward</span>
                    </a>
                </nav>
            <?php endif; ?>
        </aside>
        <main class="integradores-content">
    <?php
}

function integradores_footer()
{
    ?>
        </main>
    </div>
</body>
</html>
    <?php
}
