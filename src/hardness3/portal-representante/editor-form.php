<?php
namespace hardness;

if (realpath(isset($_SERVER['SCRIPT_FILENAME']) ? $_SERVER['SCRIPT_FILENAME'] : '') === __FILE__ || empty($g['usuarioAtual'])) { http_response_code(404); exit; }
require_once __DIR__ . '/config.php';
if (!isset($r_empresa) || ($r_empresa !== 'geral' && !ctype_digit((string) $r_empresa))) {
    echo gGeraAlertMsg('Empresa não informada', 'Selecione uma empresa para personalizar o painel.');
    return;
}
$painelEmpresaId = $r_empresa === 'geral' ? 0 : (int) $r_empresa;
$arquivo = portalRepresentanteArquivoConfiguracao($painelEmpresaId);
$tipo = 'alterarPortalRepresentante';
$tab = 'geral';
$r_acaoId = 'PORTAL DO REPRESENTANTE';
$formId = uniqid();
$form_tabela = 'D000';
$form_campo_pk = 'D000_Id';
$formNaoRecarregar = true;
$formMultiTab = false;
$prefixo = $tipo . $r_empresa;
$enviar_btnId = $prefixo . 'Btn';
$enviar_acaoHref = '/sistema/personalizacaoConf/form/alterarPortalRepresentante/';
if (empty($_SESSION['portalEditorCsrf'])) {
    // O framework fecha a sessão antes de incluir os formulários AJAX.
    // Persistir a validação para que a próxima requisição de Alterar a encontre.
    $sessaoEditorFechada = session_status() !== PHP_SESSION_ACTIVE;
    if ($sessaoEditorFechada) session_start();
    if (empty($_SESSION['portalEditorCsrf'])) $_SESSION['portalEditorCsrf'] = bin2hex(random_bytes(32));
    if ($sessaoEditorFechada) session_write_close();
}
$Code = is_readable($arquivo) ? file_get_contents($arquivo) : '';
$original = file_get_contents(portalRepresentanteArquivoPadrao());
$form = new Formulario($tipo . 'Form');
$formArray = array('campos' => array('geral' => array(
    $prefixo . 'Code' => array('name' => $prefixo . 'Code', 'label' => '', 'type' => 'textarea', 'uppercase' => false, 'value' => $Code, 'code' => 'json', 'reject' => true),
    $prefixo . 'Btn' => array('name' => $prefixo . 'Btn', 'type' => 'button', 'reload' => false, 'value' => 'Alterar (CTRL+S)', 'reject' => true),
    $prefixo . 'BtnCopiar' => array('name' => $prefixo . 'BtnCopiar', 'type' => 'button', 'reload' => false, 'value' => 'Copiar', 'reject' => true, 'align' => 'right'),
    $prefixo . 'BtnOriginal' => array('name' => $prefixo . 'BtnOriginal', 'type' => 'button', 'reload' => false, 'value' => 'Visualizar Original', 'reject' => true, 'align' => 'right'),
)));
if (is_file($arquivo)) $formArray['campos']['geral'][$prefixo . 'BtnRemover'] = array('name' => $prefixo . 'BtnRemover', 'type' => 'button', 'reload' => false, 'value' => 'Remover', 'reject' => true, 'align' => 'right');
// No template nativo, cada campo sem align abre uma linha. Os campos ocultos
// ficam na linha das ações para não consumir a altura reservada ao editor.
$formArray['campos']['geral'][$prefixo . 'Csrf'] = array('name' => $prefixo . 'Csrf', 'type' => 'hidden', 'uppercase' => false, 'value' => $_SESSION['portalEditorCsrf'], 'align' => 'right', 'reject' => true);
$formArray['campos']['geral'][$prefixo . 'Remover'] = array('name' => $prefixo . 'Remover', 'type' => 'hidden', 'uppercase' => false, 'value' => '0', 'align' => 'right', 'reject' => true);
$form->definirFormularioArray($formArray);
$formCodigo = $form->processarFormulario($form_tabela);
if ($formCodigo == 1) {
    echo gGeraAlertMsgErroForm($form);
} elseif ($formCodigo == 2) {
    $Code = $form->campoValorEnviado($prefixo . 'Code');
    try {
        $csrf = $form->campoValorEnviado($prefixo . 'Csrf');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !is_string($csrf) || !hash_equals($_SESSION['portalEditorCsrf'], $csrf)) throw new RepresentantePortalErro('O formulário expirou. Reabra esta aba.');
        if ($form->campoValorEnviado($prefixo . 'Remover') === '1') {
            if (is_file($arquivo) && !unlink($arquivo)) throw new RepresentantePortalErro('Não foi possível remover a personalização.');
            echo gGeraAlertMsg('Removido', 'A empresa volta a usar a configuração geral ou o padrão Hardness3.');
        } else {
            portalRepresentanteSalvarConfiguracao($painelEmpresaId, $Code);
            echo gGeraAlertMsg('Operação OK', 'Portal salvo para esta empresa. Atualize a página do representante para aplicar consultas e layout.');
        }
    } catch (RepresentantePortalErro $erro) { echo gGeraAlertMsg('Portal não alterado', htmlspecialchars($erro->getMessage(), ENT_QUOTES, 'UTF-8')); }
}
$painelDiv = json_encode($g['divId']);
$painelPrefixo = json_encode($prefixo);
// O editor nativo mede o textarea ao iniciar. Dimensionar antes de renderizar
// os scripts dos campos, como no formulário de personalização do dashboard.
echo <<<HTML
<div class="divHidden"><script type="text/javascript">
if (typeof jQuery !== 'undefined') {
    (function () {
        var container = jQuery(document.getElementById({$painelDiv}));
        var textarea = jQuery(document.getElementById({$painelPrefixo} + 'Code'));
        if (!container.length || !textarea.length) return;
        var limite = window.innerHeight;
        container.parents('.ui-tabs, .ui-dialog-content, .content').each(function () {
            var bounds = this.getBoundingClientRect();
            if (bounds.height > 0 && bounds.bottom > textarea[0].getBoundingClientRect().top) {
                limite = Math.min(limite, bounds.bottom);
            }
        });
        var botoes = jQuery(document.getElementById({$painelPrefixo} + 'Btn')).closest('tr');
        var altura = limite - textarea[0].getBoundingClientRect().top - Math.max(30, botoes.outerHeight(true) || 0) - 12;
        textarea.width(Math.max(100, container.width() - 30)).height(Math.max(80, altura));
    })();
}
</script></div>
HTML;
$gerado = gGeraCamposForm($form, $tab, $r_acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);
$g['smarty']->assign('formularioID', $form->formularioID());
$g['smarty']->assign('table', $gerado['table']);
$g['smarty']->assign('form_tabela', $form_tabela);
$g['smarty']->assign('formId', $formId);
echo $gerado['pagina'];
$g['smarty']->display('form.tpl');
$originalHtml = htmlspecialchars($original, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
echo '<div id="' . htmlspecialchars($prefixo . 'OriginalCode', ENT_QUOTES, 'UTF-8') . '" style="display: none;"><textarea readonly aria-label="JSON original do Portal do Representante" spellcheck="false" style="width: 800px; height: 400px; text-transform: none;">' . $originalHtml . '</textarea></div>';
echo <<<HTML
<div class="divHidden"><script type="text/javascript">
(function () {
    var divId = {$painelDiv};
    var prefixo = {$painelPrefixo};
    var botao = document.getElementById(prefixo + 'BtnRemover');
    var textarea = jQuery(document.getElementById(prefixo + 'Code'));
    // Em abas carregadas ocultas, o formulário nativo aguarda esse evento.
    if (!document.getElementById(prefixo + 'CodeCode')) textarea.triggerHandler('gerarEditor');
    // O framework carrega jQuery 1.6.2: usar os eventos compatíveis com ele.
    jQuery(document.getElementById(prefixo + 'BtnCopiar')).unbind('click.painel').bind('click.painel', function () {
        copyToClipboard(textarea.val());
    });
    jQuery(document.getElementById(prefixo + 'BtnOriginal')).unbind('click.painel').bind('click.painel', function () {
        dialogConfirm('Original', '', false, false, false, jQuery(document.getElementById(prefixo + 'OriginalCode')));
    });
    if (botao) jQuery(botao).unbind('click.painel').bind('click.painel', function () {
        dialogConfirm('Remover?', 'Remover esta personalização e voltar à configuração herdada?', {
            'Remover': function () {
                jQuery(document.getElementById(prefixo + 'Remover')).val('1');
                jQuery(document.getElementById(prefixo + 'Btn')).trigger('click');
                jQuery(this).dialog('destroy').remove();
            },
            'Fechar': function () { jQuery(this).dialog('destroy').remove(); }
        });
    });
})();
</script></div>
HTML;
