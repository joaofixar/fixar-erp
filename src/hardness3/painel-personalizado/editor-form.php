<?php
namespace hardness;

require_once __DIR__ . '/config.php';
if (!isset($r_empresa) || ($r_empresa !== 'geral' && !ctype_digit((string) $r_empresa))) {
    echo gGeraAlertMsg('Empresa não informada', 'Selecione uma empresa para personalizar o painel.');
    return;
}
$painelEmpresaId = $r_empresa === 'geral' ? 0 : (int) $r_empresa;
$arquivo = painelPersonalizadoArquivoEditor($painelEmpresaId);
$tipo = 'alterarPainel';
$tab = 'geral';
$r_acaoId = 'PAINEL PERSONALIZAVEL';
$formId = uniqid();
$form_tabela = 'D000';
$form_campo_pk = 'D000_Id';
$formNaoRecarregar = true;
$formMultiTab = false;
$prefixo = $tipo . $r_empresa;
$enviar_btnId = $prefixo . 'Btn';
$enviar_acaoHref = '/sistema/personalizacaoConf/form/alterarPainel/';
if (isset($r_remover)) {
    if (!is_file($arquivo) || @unlink($arquivo)) echo gGeraAlertMsg('Removido', 'Personalização do painel removida.');
    else echo gGeraAlertMsg('Erro', 'Não foi possível remover a personalização.');
}
$Code = is_readable($arquivo) ? file_get_contents($arquivo) : '';
$form = new Formulario($tipo . 'Form');
$formArray = array('campos' => array('geral' => array(
    $prefixo . 'Code' => array('name' => $prefixo . 'Code', 'label' => '', 'type' => 'textarea', 'uppercase' => false, 'value' => $Code, 'code' => 'json'),
    $prefixo . 'Btn' => array('name' => $prefixo . 'Btn', 'type' => 'button', 'reload' => false, 'value' => 'Alterar (CTRL+S)', 'reject' => true),
    $prefixo . 'BtnCopiar' => array('name' => $prefixo . 'BtnCopiar', 'type' => 'button', 'reload' => false, 'value' => 'Copiar', 'reject' => true, 'align' => 'right'),
)));
if (is_file($arquivo)) $formArray['campos']['geral'][$prefixo . 'BtnRemover'] = array('name' => $prefixo . 'BtnRemover', 'type' => 'button', 'reload' => false, 'value' => 'Remover', 'reject' => true, 'align' => 'right');
$form->definirFormularioArray($formArray);
$formCodigo = $form->processarFormulario($form_tabela);
if ($formCodigo == 1) {
    echo gGeraAlertMsgErroForm($form);
} elseif ($formCodigo == 2) {
    $Code = $form->campoValorEnviado($prefixo . 'Code');
    $config = json_decode($Code, true);
    $erros = array();
    if (json_last_error() !== JSON_ERROR_NONE || !painelPersonalizadoValidarConfiguracao($config, $erros)) {
        if (!$erros) $erros[] = 'JSON inválido: ' . json_last_error_msg();
        echo gGeraAlertMsg('Painel inválido', htmlspecialchars(implode(' ', $erros), ENT_QUOTES, 'UTF-8'));
    } else {
        $diretorio = dirname($arquivo);
        if ((!is_dir($diretorio) && !@mkdir($diretorio, 0777, true)) || @file_put_contents($arquivo, $Code . "\n", LOCK_EX) === false) {
            echo gGeraAlertMsg('Erro', 'Não foi possível salvar a personalização do painel.');
        } else {
            echo gGeraAlertMsg('Operação OK', 'Painel salvo. Reabra a aba Início para atualizar o atalho Painel TV.');
        }
    }
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
        var altura = container.closest('.ui-dialog').data('height') || container.closest('.ui-tabs').height() || container.height();
        container.find('textarea.itemForm').width(container.width() - 30).height(Math.max(180, altura - 75));
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
echo <<<HTML
<div class="divHidden"><script type="text/javascript">
(function () {
    var divId = {$painelDiv};
    var prefixo = {$painelPrefixo};
    var botao = document.getElementById(prefixo + 'BtnRemover');
    var textarea = jQuery(document.getElementById(prefixo + 'Code'));
    // Em abas carregadas ocultas, o formulário nativo aguarda esse evento.
    if (!document.getElementById(prefixo + 'CodeCode')) textarea.triggerHandler('gerarEditor');
    jQuery(document.getElementById(prefixo + 'BtnCopiar')).off('click.painel').on('click.painel', function () {
        copyToClipboard(textarea.val());
    });
    if (botao) jQuery(botao).off('click').on('click', function () {
        dialogConfirm('Remover?', 'Deseja remover a personalização deste painel?', {
            'Remover': function () {
                divSetarAcaoId(divId, 'true', 'remover');
                divRefresh(divId);
                jQuery(this).dialog('destroy').remove();
            },
            'Fechar': function () { jQuery(this).dialog('destroy').remove(); }
        });
    });
})();
</script></div>
HTML;
