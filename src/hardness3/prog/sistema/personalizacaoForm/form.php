<?php
 namespace hardness; if ($g['modoDesenvolvedor']) { goto VMjzY; } if (!$g['modoDesenvolvedorApp']) { goto k7lPa; } Personalizacoes::$rootDir = Apps::getDirEditor(); k7lPa: goto z8loP; VMjzY: Personalizacoes::$rootDir = 'ide/'; z8loP: switch ($g['r'][3]) { case 'personalizacao': goto H_zGI; nu5UO: FUuNE: goto R77hx; xA3tY: $opcoes = gGeraSelect('C021', 'C021_Id', 'C021_Id,C021_Nome_Perfil', 'ORDER BY C021_Nome_Perfil ASC', false, $r_alterandoPara); goto u0MaM; Hk103: echo <<<EOT
\t<div style="margin: 4px 8px;">
\t\tAlterando para: 
\t\t<select onchange="divSetarAcaoId('{$r_divIdRefresh}', \$(this).val(), 'alterandoPara'); divRefresh('{$r_divIdRefresh}', true);">
\t\t\t{$local}
\t\t</select>&nbsp;
\t</div>
EOT;
goto EcArA; AM7j3: $codigo = trim($form->campoValorEnviado('CodigoForm' . $formMd5)); goto qHp6f; puT9I: $formId = uniqid(); goto koopJ; owy9S: if ($formCodigo == 1) { goto vQNLu; } goto VACCM; fiFM8: if ($formArrayOriginal) { goto Oxenh; } goto spv2l; uYiS8: if ($formMultiTab) { goto zzZoy; } goto puT9I; koopJ: goto FUuNE; goto V0V4o; bvGUf: ofuRW: goto Nc3kZ; fDtVC: $botoesEsq = <<<EOT
            <button onclick="dialogConfirm('Personalização do Form Original', '<textarea style=\\'width: 500px; height: 400px;\\'>' + \$('#originalForm{$formId}').html() + '</textarea>');">Mostrar Original</button>
EOT;
goto YQx6m; T2kmJ: Sb8Ih: goto hzDx0; ijDkV: $g['smarty']->display('formEditor.tpl'); goto P5Xjg; BVOaJ: foreach ($formOriginal['campos'] as $tabId => $tab) { goto oZTMv; x9lyJ: EcSDY: goto B4eEt; B4eEt: LYWYY: goto dMlJK; oZTMv: foreach ($tab as $key => $value) { goto wS38q; RJYF6: unset($formOriginal['campos'][$tabId][$key]['selectRefresh']); goto yM3sK; yM3sK: unset($formOriginal['campos'][$tabId][$key]['nivel']); goto YIy8J; wS38q: unset($formOriginal['campos'][$tabId][$key]['select']); goto Eqj8e; YIy8J: unset($formOriginal['campos'][$tabId][$key]['callbackPreencherValor']); goto uTN95; PHV2y: unset($formOriginal['campos'][$tabId][$key]['callbackAoSair']); goto DtVoh; cFJX5: vGftj: goto FD6dZ; HRpYl: unset($formOriginal['campos'][$tabId][$key]['callbackRestricao']); goto lRoSU; DtVoh: unset($formOriginal['campos'][$tabId][$key]['callbackAoAlterar']); goto HRpYl; Eqj8e: unset($formOriginal['campos'][$tabId][$key]['pesquisaGrid']); goto RJYF6; uTN95: unset($formOriginal['campos'][$tabId][$key]['callbackPreencherSelect']); goto PHV2y; lRoSU: unset($formOriginal['campos'][$tabId][$key]['callbackJS']); goto cFJX5; FD6dZ: } goto x9lyJ; dMlJK: } goto bvGUf; mbv4j: goto Uekyv; goto fQIbE; fQIbE: OYi_j: goto i7s6s; PMBdT: $formId = isset($r_formcad001FormCadastro) ? $r_formcad001FormCadastro['formId'] : $r_formId; goto nu5UO; buGFk: ZsjEV: goto I893g; uJ7As: $enviar_acaoHref = '/sistema/personalizacaoForm/form/personalizacao/'; goto Jt28q; C1Kq2: $formMultiTab = false; goto uYiS8; d4YaP: TBvMA: goto EmUec; DeLmU: $r_alterandoPara = -1; goto iTvpy; qHp6f: Personalizacoes::salvarPersonalizacaoForm($formMd5, $codigo, $pre); goto b4O9P; YQx6m: $botoesDir = <<<EOT
            <button id="personalizarFormBtn" type="submit" onclick="\$('#{$formId} input[id=ColunaValor_Testar]').val('false');">Enviar</button>
EOT;
goto gQAid; Tco87: AHfcj: goto ijDkV; Nc3kZ: $formOriginalTexto = Personalizacoes::converterForm($formOriginal); goto yOBQJ; u0MaM: array_unshift($opcoes, array('title' => 'optgroup', 'value' => 'Perfis')); goto Y6dCT; F1Yyu: zPoTI: goto Oy8AG; JDgmd: $g['smarty']->assign('botoesMid', $botoesMid); goto o2KC3; EcArA: $formArray = array('campos' => array('geral' => array('CodigoForm' . $formMd5 => array('name' => 'Codigo', 'label' => '', 'type' => 'textarea', 'uppercase' => false, 'code' => 'plaintext', 'codeModoSimples' => false, 'value' => $codigo)))); goto qYrm1; rJbnc: $r_alterandoPara = empty($r_alterandoPara) ? -1 : $r_alterandoPara; goto pKIrU; qYrm1: $form->definirFormularioArray($formArray); goto a6TlM; YgPGk: echo $gerado['pagina']; goto rQlik; MhhzQ: $botoesMid = ''; goto fDtVC; O5ebO: if ($r_alterandoPara == -2) { goto Sb8Ih; } goto VE_wb; rKMvh: echo '<div style="float: right; width: 200px;">' . $docTabs . '</div>'; goto qP2u9; X6E1a: $form_campo_pk = 'D000_Id'; goto vVj5d; Od_r3: $g["smarty"]->assign("formId", $formId); goto Tco87; o2KC3: $g['smarty']->assign('botoesDir', $botoesDir); goto kmCGF; Of2U1: array_unshift($opcoes, array('title' => "Usuário: {$g['C007']['C007_Login']}", 'value' => -2, 'selected' => $r_alterandoPara == -2)); goto a5GzF; i7s6s: Uekyv: goto bVhY7; Oy8AG: $local = gProcessaSelect($opcoes); goto Hk103; V0V4o: zzZoy: goto PMBdT; kmCGF: $g['smarty']->display('buttons-floatBottom.tpl'); goto ZMmvI; Jt28q: $form_editor = array('geral' => '{"CodigoForm' . $formMd5 . '":{"top":7,"left":5,"height":361,"width":675,"hidden":0}}'); goto M85mZ; dUe56: if ($g['modoDesenvolvedor'] || $g['modoDesenvolvedorApp']) { goto TBvMA; } goto xA3tY; iTvpy: XINcl: goto fNK2o; Kubjc: echo gGeraAlertMsgErroForm($form); goto Qq5Te; H_zGI: $formMd5 = md5($r_acaoId); goto rJbnc; yOBQJ: $docTabs = <<<'EOT'
<span style="font-size: 1.2em; font-weight: bold;">Obrigatórios:</span>
<span style="font-weight: bold; border-bottom: 1px solid black;">field</span> 			        : campo (id)
<span style="font-weight: bold; border-bottom: 1px solid black;">tab</span> 			        : tab do campo
<span style="font-weight: bold; border-bottom: 1px solid black;">name</span> 			        : usado no html 'name'
<span style="font-weight: bold; border-bottom: 1px solid black;">type</span> 			        : text, password, radio, checkbox, hidden, textarea, select, file, pesquisa, data, moeda, pesquisaGrid, submit, button, tempo, titulo, cnpj, cpf, comentario, enviarForm

<span style="font-size: 1.2em; font-weight: bold;">Todos:</span>
<span style="font-weight: bold; border-bottom: 1px solid black;">label</span> 			        : label do campo
<span style="font-weight: bold; border-bottom: 1px solid black;">reject</span> 			        : S/vazio
<span style="font-weight: bold; border-bottom: 1px solid black;">abrirJanela</span> 			: url para abrir uma janela
<span style="font-weight: bold; border-bottom: 1px solid black;">abrirJanelaTitulo</span> 	    : título da janela
<span style="font-weight: bold; border-bottom: 1px solid black;">abrirJanelaTamanho</span> 	    : tamanho da janela: auto ou [largura, altura]
<span style="font-weight: bold; border-bottom: 1px solid black;">abrirJanelaParametros</span>   : a janela já recebe todos os parâmetros recebidos originalmente no form + o "campoAtual", utilize este para enviar extras - exemplo: "&xyz=123&campo=[X001_Nome_Campo_Form]"
<span style="font-weight: bold; border-bottom: 1px solid black;">pesquisa</span> 	        : array(tabela, campoId, campoTxt, sqlExtra, formato)
<span style="font-weight: bold; border-bottom: 1px solid black;">pesquisaGrid</span> 	    : array(tabela, campoId, campoTxt, sqlExtra, tituloJanela, descricaoCampoTxt, callbackJs, divId, parametroExtra, parametrosGrid, camposIgnorados, tamanho)
EOT;
goto TH1x2; M85mZ: $codigo = ''; goto uxhGe; Y6dCT: array_unshift($opcoes, array('title' => 'Geral: todos os usuários', 'value' => -1, 'selected' => $r_alterandoPara == -1)); goto Of2U1; I893g: $form = new Formulario('personalizarFormForm'); goto dUe56; I_GzF: if (!file_exists($arquivo)) { goto ZsjEV; } goto odGwK; B0a66: $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : ''); goto YgPGk; XV0Mg: goto uWmdt; goto wcxjl; TH1x2: $docTabs = str_replace("\n", '<br />', $docTabs); goto rKMvh; gQAid: $botoesDir .= '<button onclick="fecharJanela(\'' . $g['divId'] . '\'); return false;" class="btnFechar">Cancelar</button>'; goto gxSQL; odGwK: $codigo = file_get_contents($arquivo); goto buGFk; ZMmvI: die; goto yF_5C; R77hx: $form_tabela = 'D000'; goto X6E1a; hzDx0: $pre = "u{$g['C007']['C007_Id']}-"; goto Flnjo; wcxjl: vQNLu: goto Kubjc; azGNV: $g['smarty']->assign('botoesEsq', $botoesEsq); goto JDgmd; KGii_: $arquivo = $dir . $pre . $formMd5; goto I_GzF; FypyZ: if (!($g['modoDesenvolvedor'] || $g['modoDesenvolvedorApp'])) { goto XINcl; } goto DeLmU; uxhGe: $dir = Personalizacoes::getDirPersonalizacoesForm(); goto KGii_; qP2u9: echo <<<EOT
<pre id="originalForm{$formId}" style="display: none;">
{$formOriginalTexto}
</pre>
EOT;
goto MhhzQ; VE_wb: if ($r_alterandoPara == -1) { goto OYi_j; } goto gETEY; Flnjo: Ier8g: goto FypyZ; EmUec: $opcoes = array(array('title' => 'Geral: todos os usuários', 'value' => -1, 'selected' => $r_alterandoPara == -1)); goto F1Yyu; yUT25: $formOriginal = unserialize($formArrayOriginal); goto Iqn7O; j2Z02: $gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk); goto Dje3C; vVj5d: $enviar_btnId = 'personalizarFormBtn'; goto uJ7As; P5Xjg: $formArrayOriginal = apc_fetch("form-php-{$formMd5}-pegarForm"); goto fiFM8; rQlik: if ($formMultiTab) { goto AHfcj; } goto Od_r3; Dje3C: $g['smarty']->assign('formularioID', $form->formularioID()); goto QOW6D; DcZEq: Oxenh: goto yUT25; bVhY7: goto Ier8g; goto T2kmJ; HR1vt: goto H0UXw; goto DcZEq; a5GzF: goto zPoTI; goto d4YaP; a6TlM: if (!($formCodigo = $form->processarFormulario($form_tabela))) { goto MYiVl; } goto owy9S; DDdYK: WyCq4: goto XV0Mg; gxSQL: $g['smarty']->assign('botoesId', $g['divId']); goto azGNV; spv2l: $formOriginal = Personalizacoes::getForm($formMd5); goto HR1vt; QOW6D: $g['smarty']->assign('table', $gerado['table']); goto B0a66; gETEY: $pre = "{$r_alterandoPara}-"; goto mbv4j; yF_5C: goto E9HBB; goto ERH28; fNK2o: $tab = isset($_REQUEST['tab']) ? $_REQUEST['tab'] : false; goto ODsqK; Qq5Te: uWmdt: goto DdMuN; pKIrU: $pre = ''; goto O5ebO; VACCM: if (!($formCodigo == 2)) { goto WyCq4; } goto AM7j3; b4O9P: echo gGeraAlertMsg('Operação OK', 'Form personalizado com sucesso!'); goto DDdYK; Iqn7O: H0UXw: goto BVOaJ; ODsqK: $acaoId = isset($_REQUEST['acaoId']) ? $_REQUEST['acaoId'] : false; goto C1Kq2; DdMuN: MYiVl: goto j2Z02; ERH28: } QTnaK: E9HBB: $tab = isset($_REQUEST['tab']) ? $_REQUEST['tab'] : false; $tipo = $g['r'][3]; $formNaoRecarregar = true; $formMultiTab = false; if ($formMultiTab) { goto kLZH8; } $formId = uniqid(); goto mFSo1; kLZH8: $formId = isset($r_formcad001FormCadastro) ? $r_formcad001FormCadastro['formId'] : $r_formId; mFSo1: $form_tabela = 'D000'; $form_campo_pk = 'D000_Id'; $enviar_btnId = $tipo . 'Btn'; $enviar_acaoHref = '/sistema/personalizacaoForm/form/' . $tipo . '/'; $dir = Personalizacoes::getDirPersonalizacoesForm(); is_dir($dir) or mkdir($dir, 0777, true); $tabela = str_replace('/', '-', trim($r_href, '/')); if (empty($r_campo)) { goto ZH9uB; } $arquivo = $dir . $tabela . '-' . $r_campo . '-' . $tipo; goto Ax_Nx; ZH9uB: $arquivo = $dir . $tabela . '-' . $tipo; Ax_Nx: if (!isset($r_remover)) { goto BF8bt; } unlink($arquivo); echo gGeraAlertMsg('Removido', 'Configuração removida!'); BF8bt: if (file_exists($arquivo)) { goto JqEAe; } $Code = getCodeDefault($tipo); goto hqEco; JqEAe: $Code = file_get_contents($arquivo); hqEco: $form = new Formulario($tipo . 'Form'); $prog = explode('-', $tabela); if (!(count($prog) > 4)) { goto QbDF8; } $prog = array($prog[0], $prog[1], $prog[2] . '-' . $prog[3], $prog[4]); QbDF8: if ($tipo == 'codigoFonte' || $tipo == 'novoCampo') { goto hyN8N; } $original = ''; goto qMC8s; hyN8N: if ($prog[0] == 'per') { goto SE53w; } $original = "prog/{$prog[0]}/{$prog[1]}/{$prog[2]}.php"; goto hhkrs; SE53w: $original = "{$g['pathDados']}/builder/prog/{$prog[1]}/{$prog[2]}.php"; hhkrs: qMC8s: if (file_exists($original)) { goto D3OLp; } $original = false; goto hc9hZ; D3OLp: $original = file_get_contents($original); $original2 = $original; preg_match("/case '{$prog[3]}':(.*)break;/msUu", $original, $matches); if (!isset($matches[1])) { goto MPRNU; } $original = $matches[1]; MPRNU: hc9hZ: $original2 = !isset($original2) ? '' : $original2; if (!in_array($tipo, array('callbackPreencherValor', 'callbackPreencherSelect', 'callbackAoSair', 'callbackAoAlterar', 'callbackRestricao', 'callbackJS'))) { goto uOOMC; } $formIdOriginal = md5(trim($r_href, '/') . '/'); $formPHP = unserialize(apc_fetch("form-php-{$formIdOriginal}-pegarForm")); $formPrimeiraTab = array_shift(array_values($formPHP['campos'])); $callback = $formPrimeiraTab[$r_campo][$tipo]; if (file_exists("bibliotecas/funcoesCallback/{$callback}.php")) { goto laDC8; } $original = $callback; goto FOZ2O; laDC8: $original = file_get_contents("bibliotecas/funcoesCallback/{$callback}.php"); FOZ2O: uOOMC: $formArray = array('campos' => array('geral' => array($tipo . 'Code' => array('name' => $tipo . 'Code', 'label' => '', 'type' => 'textarea', 'uppercase' => false, 'code' => $tipo == 'callbackJS' ? 'javascript' : 'php', 'value' => $Code)))); $formArray['campos']['geral'][$tipo . 'Btn'] = array('name' => $tipo . 'Btn', 'type' => 'button', 'reload' => false, 'value' => 'Alterar', 'reject' => true); $formArray['campos']['geral'][$tipo . 'BtnOriginal'] = array('name' => $tipo . 'Btn', 'type' => 'button', 'reload' => false, 'value' => 'Visualizar Original', 'reject' => true, 'align' => 'right', 'style' => !$original ? 'opacity: 0.5;' : ''); $formArray['campos']['geral'][$tipo . 'BtnOriginal2'] = array('name' => $tipo . 'Btn2', 'type' => 'button', 'reload' => false, 'value' => 'Visualizar Original (arquivo inteiro)', 'reject' => true, 'align' => 'right', 'style' => !$original2 ? 'opacity: 0.5;' : ''); if (!file_exists($arquivo)) { goto pwh7e; } $formArray['campos']['geral'][$tipo . 'BtnRemover'] = array('name' => $tipo . 'BtnRemover', 'type' => 'button', 'reload' => false, 'value' => 'Remover', 'reject' => true, 'align' => 'right'); pwh7e: if (!($tipo == 'cssVars')) { goto hWi1k; } $formArray['campos']['geral']['addTemaBtn'] = array('name' => 'addTemaBtn', 'type' => 'button', 'reload' => false, 'value' => 'Adicionar Tema', 'reject' => true, 'align' => 'right'); hWi1k: $form->definirFormularioArray($formArray); if (!($formCodigo = $form->processarFormulario($form_tabela))) { goto el8Eu; } if ($formCodigo == 1) { goto gKuGX; } if (!($formCodigo == 2)) { goto A6ZmN; } file_put_contents($arquivo, trim($form->campoValorEnviado($tipo . 'Code')) . "\n"); $Code = $form->campoValorEnviado($tipo . 'Code'); echo gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!'); A6ZmN: goto CGZEC; gKuGX: echo gGeraAlertMsgErroForm($form); CGZEC: el8Eu: $gerado = gGeraCamposForm($form, $tab, $r_acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk); $g['smarty']->assign('formularioID', $form->formularioID()); $g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : ''); echo $gerado['pagina']; if ($formMultiTab) { goto uNHi9; } $g["smarty"]->assign("formId", $formId); uNHi9: $g['smarty']->display('form.tpl'); echo <<<EOT
<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
\t\$('#{$tipo}Btn').closest('.idForm').css({
\t    height: '10%',
\t});
    const \$code = \$('#{$tipo}Code');
    \$code.closest('table').css({
        width: '99%',
\t    height: '99%',
\t});
    \$code.closest('form').css({
        width: '99%',
\t    height: '99%',
\t});
    \$code.css({
        width: '100%',
\t    height: '100%',
\t});
    setTimeout(function() {
        \$code.trigger('gerarEditor');
    }, 50);
} </script></div>
EOT;
if (!$original) { goto v5NVO; } $original = htmlspecialchars($original, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); echo <<<EOT
<div id="{$tipo}OriginalCode" style="display: none;">
<textarea style="width: 800px; height: 400px; text-transform: none;">
{$original}
</textarea>
</div>
EOT;
echo <<<EOT
\t<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
\t\t\$('#{$tipo}BtnOriginal').click(function() {
\t\t\tdialogConfirm('Original', '', false, false, false, \$('#{$tipo}OriginalCode'));
\t\t});
\t} </script></div>
EOT;
v5NVO: if (!$original2) { goto rtNHj; } $original2 = htmlspecialchars($original2, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); echo <<<EOT
<div id="{$tipo}OriginalCode2" style="display: none;">
<textarea style="width: 800px; height: 400px; text-transform: none;">
{$original2}
</textarea>
</div>
EOT;
echo <<<EOT
\t<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
\t\t\$('#{$tipo}BtnOriginal2').click(function() {
\t\t\tdialogConfirm('Original (arquivo inteiro)', '', false, false, false, \$('#{$tipo}OriginalCode2'));
\t\t});
\t} </script></div>
EOT;
rtNHj: echo <<<EOT
\t<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
\t\t\$('#{$tipo}BtnRemover').click(function() {
\t\t\tvar buttons = {
\t\t\t\t"Remover": function() {
\t\t\t\t\tdivSetarAcaoId('{$g['divId']}', 'true', 'remover');
\t\t\t\t\tdivRefresh('{$g['divId']}');
\t\t\t\t\t\$(this).dialog("destroy").remove();
\t\t\t\t},
\t\t\t\t"Fechar": function() { \$(this).dialog("destroy").remove(); }
\t\t\t};
\t\t\tdialogConfirm('Remover?', 'Deseja remover este arquivo de personalização?', buttons);
\t\t});
\t} </script></div>
EOT;
function getCodeDefault($tipo) { goto hHRVN; VckDy: switch ($tipo) { case 'restricaoIncluirUpdate': return <<<DEFAULT
<?php
/**
* restricaoIncluirUpdate
* 
* Código PHP para validar um campo de uma tabela na hora de Incluir ou Atualizar.
* Para ver variáveis e funções disponíveis dentro do código, veja:
* \t\thttp://flycker/wiki/index.php?title=Restri%C3%A7%C3%B5es_de_Campos
* Dica: utilize unset() para remover uma restrição definida global.
*/

// Criar/modificar restrição:
/*
\$conf['restricaoIncluirUpdate']['{$r_campo}'] = <<<'EOT'
\t// Código PHP de validação
\t// \$retorno = true; (OK)
\t// \$retorno = 'string'; (ERRO)
EOT;
*/

// Remover restrição:
//unset(\$conf['restricaoIncluirUpdate']['{$r_campo}']);
DEFAULT;
goto gXXYo; case 'camposValorPadrao': return <<<DEFAULT
<?php
/**
* camposValorPadrao
* 
* Defina valores padrão para campos no formulário de inclusão.
*/

// Criar/modificar valor padrão: 
//\$conf['camposValorPadrao']['{$r_campo}'] = date('d/m/Y');

// Remover valor padrão:
//unset(\$conf['camposValorPadrao']['{$r_campo}']);
DEFAULT;
goto gXXYo; case 'camposLabelForm': return <<<DEFAULT
<?php
/**
* camposLabelForm
* 
* Modifica o 'label' do formulário de um campo.
*/

// Mudar o texto:
//\$conf['camposLabelForm']['{$r_campo}'] = 'Novo Label';
DEFAULT;
goto gXXYo; case 'camposTipo': return <<<DEFAULT
<?php
/**
* camposTipo
* 
* Define qual é o tipo do campo. Com esta informação é feita a formatação dele no FORM, GRID, etc.
*/

// Mudar o tipo do campo:
//\$conf['camposTipo']['{$r_campo}'] = array('numero', 2);
DEFAULT;
goto gXXYo; case 'codigoFonte': return <<<DEFAULT
<?php
/**
* Código Fonte
* 
* Copie e cole o "Array()" do codigo fonte para a variável "\$codigo" (e modifique o que precisar). Exemplo:
*
* \t\$codigo = array('name' => 'Consulta_T117_D007_Id', 'label' => 'Banco', 'type' => 'select','extra' => "onchange=\\"preencheCampos(this, '{\$g['divId']}')\\"", 'select' => 
*\t\tgGeraSelect('D007', 'D007_Id', 'D007_Nome_Banco','left join D062 on D062_Id=D007_D062_Id where D062_Id is not null and D007_C004_Id=' . \$g['empresaAtual'] . ' order by D007_Id', true)
*\t);
*/
DEFAULT;
case 'novoCampo': return <<<DEFAULT
<?php
namespace hardness;
/**
* Adicione novos campos no formulário utilizando a mesma sintaxe do PHP.
*/

/*
\$formArray = array(
\t'campos' => array(
\t\t'geral' => array(
\t\t\t...
\t\t)
\t)
);
*/
DEFAULT;
goto gXXYo; case 'callbackPreencherValor': return <<<'DEFAULT'
<?php
/**
 * Código executado para preencher o valor (value) do campo 
 * Variáveis:
 *      $g, $array['todosCampos'], $array['{parametro}']
 */
// Exemplo
//return "Valor do campo";
DEFAULT;
goto gXXYo; case 'callbackPreencherSelect': return <<<'DEFAULT'
<?php
/**
 * Código executado para preencher a opção "select" de campos "type [select]" 
 * Variáveis:
 *      $array['campoAtual'], $array['todosCampos'], $g, $array['{parametro}']
 */
//return gGeraSelect($tabela, $campoId, $campoTxt, $sqlExtra, [$first], [$default], [$formato]);
//return array(array('title' => 'Título', 'value' => 'valor'), ...);
DEFAULT;
goto gXXYo; case 'callbackAoSair': return <<<'DEFAULT'
<?php
/**
 * Código executado ao sair de um campo ou ao clicar em um botão
 * Variáveis:
 *      $array['campoAtual'], $array['todosCampos'], $g, $array['{parametro}'], $array['acaoId']
 * Funções:
 *      jsRecarregarCampo('campo', array('var' => 'valor'))
 *      jsAlterarCampoValor($campo, $valor)
 *      jsExecutarCodigo($codigo)
 *      jsAbrirJanela($url, $titulo, $parametros, $tamanho = '[900,600]')
 */
DEFAULT;
goto gXXYo; case 'callbackAoAlterar': return <<<'DEFAULT'
<?php
/**
 * Código executado ao alterar o valor de um campo
 * Variáveis:
 *      $array['campoAtual'], $array['todosCampos'], $g, $array['{parametro}'], $array['acaoId']
 * Funções:
 *      jsRecarregarCampo('campo', array('var' => 'valor'))
 *      jsAlterarCampoValor($campo, $valor)
 *      jsExecutarCodigo($codigo)
 *      jsAbrirJanela($url, $titulo, $parametros, $tamanho = '[900,600]')
 */
DEFAULT;
goto gXXYo; case 'callbackRestricao': return <<<'DEFAULT'
<?php
/**
 * Código executado ao enviar um formulário (inserir/alterar)
 * Variáveis:
 *      $array['campoAtual'], $array['todosCampos'], $array['acaoId'], $g, $array['{parametro}']
 * Funções:
 *      jsRecarregarCampo('campo', array('var' => 'valor'))
 *      jsAlterarCampoValor($campo, $valor)
 *      jsExecutarCodigo($codigo)
 *      jsAbrirJanela($url, $titulo, $parametros, $tamanho = '[900,600]')
 */
// Retornar true ou mensagem de erro 
// Exemplo campo obrigatório
//return empty($array['campoAtual']) ? 'Campo é obrigatório' : true;
DEFAULT;
goto gXXYo; case 'callbackJS': return <<<'DEFAULT'
/**
 * Código Javascript executado ao selecionar um registro do grid (via pesquisaGrid)
 * Variáveis:
 *      array['divId'], array[PK]
 */
// Exemplo: preencher um campo do formulário  
//$(`#${array['divId']} .itemForm-X000_Id`).val(array['X000_Id']);
DEFAULT;
goto gXXYo; } goto M1n0D; Pk4v2: gXXYo: goto KAz9c; hHRVN: global $r_campo; goto VckDy; M1n0D: pr8tI: goto Pk4v2; KAz9c: }