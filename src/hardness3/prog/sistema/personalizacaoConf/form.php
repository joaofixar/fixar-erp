<?php
 namespace hardness; if (!($g['r'][3] === 'alterarPainel')) { goto r49W6; } require 'painel-personalizado/editor-form.php'; return; r49W6: $pathDados = $g['pathDados']; if ($g['modoDesenvolvedor']) { goto zSFDP; } if (!$g['modoDesenvolvedorApp']) { goto tHCRY; } Personalizacoes::$rootDir = Apps::getDirEditor(); $pathDados = Apps::getDirEditor(); tHCRY: goto VJDsU; zSFDP: Personalizacoes::$rootDir = 'ide/'; $pathDados = 'ide/'; VJDsU: echo <<<EOT
\t<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
\t    \$divId = \$('#{$r_divId}');
        \$textarea = \$divId.find('textarea.itemForm');
        var height = \$divId.closest('.ui-dialog').data('height');
        if (!height) {
            height = \$divId.closest('.ui-tabs').height();
            \$divId.closest('.ui-dialog').data('height', height);
        }
        \$textarea.width(\$divId.width() - 30);
\t\t\$textarea.height(height - 75);
\t} </script></div>
EOT;
$tab = isset($_REQUEST['tab']) ? $_REQUEST['tab'] : false; $tipo = $g['r'][3]; $formNaoRecarregar = true; $formMultiTab = false; if ($formMultiTab) { goto S907j; } $formId = uniqid(); goto MYO36; S907j: $formId = isset($r_formcad001FormCadastro) ? $r_formcad001FormCadastro['formId'] : $r_formId; MYO36: $form_tabela = 'D000'; $form_campo_pk = 'D000_Id'; $enviar_btnId = $tipo . $r_empresa . 'Btn'; $enviar_acaoHref = '/sistema/personalizacaoConf/form/' . $tipo . '/'; $dir = "{$pathDados}personalizacaoConf/"; is_dir($dir) or mkdir($dir, 0777, true); if ($tipo == 'alterarDashboard') { goto RoyVK; } if ($tipo == 'cssVars') { goto nmEOz; } if ($tipo == 'alterarCSS') { goto pzN6g; } if ($tipo == 'alterarFlag') { goto mIAvS; } $r_acaoId = trim(urldecode($r_acaoId), '()'); $arquivo = $dir . $r_empresa . $r_acaoId . '-' . $tipo . '.php'; goto FumSp; mIAvS: $arquivo = $g['pathDadosUsuario'] . strtolower($r_acaoId); FumSp: goto RHnad; pzN6g: $arquivo = $dir . $r_empresa . $r_acaoId . '-' . $tipo . '.css'; RHnad: goto Vjbyk; nmEOz: $arquivo = $dir . $r_empresa . $r_acaoId . '-' . $tipo . '.ini'; Vjbyk: goto K8Trt; RoyVK: if (!empty($r_empresa)) { goto TXJGs; } echo gGeraAlertMsg('Empresa não informada', 'Selecione a empresa antes de personalizar o dashboard.'); return; TXJGs: $arquivo = $dir . (int) $r_empresa . 'DASHBOARD PERSONALIZAVEL-alterarDashboard.json'; $arquivosDashboard = array_values(array_unique(array($arquivo, $dir . (int) $r_empresa . 'DASHBOARD+PERSONALIZAVEL-alterarDashboard.json', $dir . (int) $r_empresa . 'DASHBOARD-PERSONALIZAVEL-alterarDashboard.json'))); $arquivosDashboardExistentes = array_values(array_filter($arquivosDashboard, 'file_exists')); usort($arquivosDashboardExistentes, function ($arquivoA, $arquivoB) { return (int) @filemtime($arquivoB) - (int) @filemtime($arquivoA); }); $arquivoLeitura = !empty($arquivosDashboardExistentes) ? $arquivosDashboardExistentes[0] : $arquivo; foreach ($arquivosDashboardExistentes as $arquivoDashboardExistente) { goto Sln1a; gV12q: hp6KO: goto E3xJR; Sln1a: $configuracaoDashboardExistente = json_decode(file_get_contents($arquivoDashboardExistente), true); goto frxNY; W2tw2: goto i05e6; goto PMJog; PMJog: q3W2G: goto gV12q; hSar5: $arquivoLeitura = $arquivoDashboardExistente; goto W2tw2; frxNY: if (!(is_array($configuracaoDashboardExistente) && !empty($configuracaoDashboardExistente['habilitado']))) { goto q3W2G; } goto hSar5; E3xJR: } i05e6: K8Trt: if (!isset($r_remover)) { goto qUySc; } if ($tipo == 'alterarDashboard') { goto hyorK; } unlink($arquivo); goto RH2Rk; hyorK: foreach ($arquivosDashboard as $arquivoDashboard) { goto hhDtG; M3BsS: dkgcb: goto h7WkF; h7WkF: cDFDW: goto S9grc; hhDtG: if (!file_exists($arquivoDashboard)) { goto dkgcb; } goto oN1gO; oN1gO: unlink($arquivoDashboard); goto M3BsS; S9grc: } RGXND: $arquivosDashboardExistentes = array(); RH2Rk: echo gGeraAlertMsg('Removido', 'Configuração removida!'); qUySc: if (file_exists(isset($arquivoLeitura) ? $arquivoLeitura : $arquivo)) { goto CW7FN; } if ($tipo == 'alterarDashboard' && file_exists('configuracoes/dashboard-personalizavel.json')) { goto HWILz; } if ($tipo == 'menu') { goto uQl4w; } if ($tipo == 'colunaEditarRestricoes') { goto UKMB9; } $Code = getCodeDefault($tipo); goto Ad0tY; UKMB9: $Code = <<<'EOT'
<?php
// Adicionar novos campos
//$g['restricaoCamposEditar'] = array_merge($g['restricaoCamposEditar'], array('NOME_CAMPO', 'NOME_CAMPO_02'));

// Remover um campo
//unset($g['restricaoCamposEditar'][array_search('NOME_CAMPO', $g['restricaoCamposEditar'])]);

// Substituir todos os registros
//$g['restricaoCamposEditar'] = array('NOME_CAMPO', 'NOME_CAMPO_02');
EOT;
Ad0tY: goto q5gCt; uQl4w: $Code = file_get_contents('bibliotecas/menu.php'); q5gCt: goto S7E6q; HWILz: $Code = file_get_contents('configuracoes/dashboard-personalizavel.json'); S7E6q: goto Q1KxC; CW7FN: $Code = file_get_contents(isset($arquivoLeitura) ? $arquivoLeitura : $arquivo); Q1KxC: $form = new Formulario($tipo . 'Form'); $tabela = $r_acaoId == 'GLOBAL' || $r_acaoId == 'CAMPOS_LIVRE' ? '_GLOBAL' : ''; $tabela = $r_acaoId == 'GLOBAL' && $tipo == 'altText' ? '_alteracoesTextuais' : $tabela; $tabela = $r_acaoId == 'GLOBAL' && $tipo == 'cssVars' ? '_cssVars' : $tabela; $tabela = empty($tabela) ? $r_acaoId : $tabela; $codeLanguage = 'php'; if ($tipo == 'alterarDashboard') { goto L7xIw; } if ($tipo == 'cssVars') { goto cg9tZ; } if ($tipo == 'alterarMetodos') { goto ll_aB; } if ($tipo == 'menu') { goto k5ORw; } if ($tipo == 'menuBuiler') { goto zVrJ_; } if ($tipo == 'colunaEditarRestricoes') { goto knPMd; } if ($tipo == 'alterarSQL') { goto jnz3F; } if ($tipo == 'alterarCSS') { goto M08AY; } if ($tipo == 'alterarFlag') { goto MEx80; } if ($tipo == 'menu') { goto RPDGw; } if (!($tipo == 'colunaEditarRestricoes')) { goto py5K3; } $original = "configuracoes/_colunaEditarRestricoes.php"; py5K3: goto SLYjV; RPDGw: $original = "bibliotecas/menu.php"; SLYjV: if ($tipo == 'alterarPrograma') { goto ivDdT; } $original = "configuracoes/{$tabela}.php"; goto bwBYi; ivDdT: $prog = explode('-', $tabela); if ($prog[0] == 'per') { goto W3yXz; } if (!(count($prog) > 4)) { goto dOgGA; } $prog = array($prog[0], $prog[1], $prog[2] . '-' . $prog[3], $prog[4]); dOgGA: $original = "prog/{$prog[0]}/{$prog[1]}/{$prog[2]}.php"; goto IYrt0; W3yXz: $original = "{$pathDados}/builder/prog/{$prog[1]}/{$prog[2]}.php"; IYrt0: bwBYi: goto DwJt_; MEx80: $codeLanguage = 'json'; $original = ''; DwJt_: goto BkKt0; M08AY: $codeLanguage = 'css'; $original = strtolower("static/css/{$tabela}"); BkKt0: goto WFQrt; jnz3F: $codeLanguage = 'sql'; $original = "scripts/sql/{$tabela}"; WFQrt: goto XWC3h; knPMd: $original = 'configuracoes/_colunaEditarRestricoes.php'; XWC3h: goto xgcN3; zVrJ_: $original = 'bibliotecas/menu.php'; xgcN3: goto lQSJD; k5ORw: $original = 'bibliotecas/menu.php'; lQSJD: goto hp1bY; ll_aB: $original = "bibliotecas/classes/{$tabela}"; $original = str_replace('PHP', 'php', $original); hp1bY: goto uJ1rW; cg9tZ: $codeLanguage = 'css'; $original = "configuracoes/{$tabela}.ini"; uJ1rW: goto zzeC8; L7xIw: $codeLanguage = 'json'; $original = 'configuracoes/dashboard-personalizavel.json'; zzeC8: if (file_exists($original)) { goto RCcWu; } $original = false; if (!($tipo == 'alterarCallback')) { goto Q5VKC; } $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator('bibliotecas/funcoesCallback/')); foreach ($iterator as $path) { goto XjSgF; EFSm3: $original2 = $src; goto sJlr3; XjSgF: $src = file_get_contents($path); goto EFSm3; sJlr3: preg_match_all('/function ' . $r_acaoId . ' ?\\((.*)(function|$)/sUu', $src, $matches); goto hSlq8; B0Oq9: $original = $matches[0][0]; goto gjIFF; cyThE: wV2aE: goto nzdi8; hSlq8: if (!(isset($matches[1]) && !empty($matches[1]))) { goto nwBaJ; } goto B0Oq9; gjIFF: nwBaJ: goto cyThE; nzdi8: } tOmKr: $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator('prog/')); foreach ($iterator as $path) { goto feshf; XdbqI: $src = file_get_contents($path); goto jJfwq; czdT8: if (!(isset($matches[1]) && !empty($matches[1]))) { goto NhKqg; } goto bbZ7y; lZRx1: TOyuB: goto mWxRc; jJfwq: $original2 = $src; goto qbV53; bbZ7y: $original = $matches[0][0]; goto Ad8zB; feshf: if (!(strpos($path, '-callback.php') !== false)) { goto Mzjd1; } goto XdbqI; qbV53: preg_match_all('/function ' . $r_acaoId . ' ?\\((.*)(function|$)/sUu', $src, $matches); goto czdT8; Ad8zB: NhKqg: goto hsS5H; hsS5H: Mzjd1: goto lZRx1; mWxRc: } YywBX: Q5VKC: goto gMbrX; RCcWu: $original = file_get_contents($original); if (!($tipo == 'alterarPrograma')) { goto AKE0y; } $original2 = $original; preg_match("/case '{$prog[3]}':(.*)break;/msUu", $original, $matches); if (!isset($matches[1])) { goto azt05; } $original = $matches[1]; azt05: AKE0y: gMbrX: $original2 = !isset($original2) ? '' : $original2; $formArray = array('campos' => array('geral' => array($tipo . $r_empresa . 'Code' => array('name' => $tipo . $r_empresa . 'Code', 'label' => '', 'type' => 'textarea', 'uppercase' => false, 'value' => $Code, 'code' => $codeLanguage)))); $formArray['campos']['geral'][$tipo . $r_empresa . 'Btn'] = array('name' => $tipo . $r_empresa . 'Btn', 'type' => 'button', 'reload' => false, 'value' => 'Alterar (CTRL+S)', 'reject' => true); $formArray['campos']['geral'][$tipo . $r_empresa . 'BtnCopiar'] = array('name' => $tipo . $r_empresa . 'BtnCopiar', 'type' => 'button', 'reload' => false, 'value' => 'Copiar', 'align' => 'right', 'reject' => true); $formArray['campos']['geral'][$tipo . $r_empresa . 'BtnOriginal'] = array('name' => $tipo . $r_empresa . 'Btn', 'type' => 'button', 'reload' => false, 'value' => 'Visualizar Original', 'reject' => true, 'align' => 'right', 'style' => !$original ? 'opacity: 0.5;' : ''); $formArray['campos']['geral'][$tipo . $r_empresa . 'BtnOriginal2'] = array('name' => $tipo . $r_empresa . 'Btn2', 'type' => 'button', 'reload' => false, 'value' => 'Visualizar Original (arquivo inteiro)', 'reject' => true, 'align' => 'right', 'style' => !$original2 ? 'opacity: 0.5;' : ''); if (!($tipo == 'cssVars')) { goto g1EGs; } $formArray['campos']['geral']['addTemaBtn'] = array('name' => 'addTemaBtn', 'type' => 'button', 'reload' => false, 'value' => 'Adicionar Tema', 'reject' => true, 'align' => 'right'); g1EGs: if (!($tipo == 'alterarDashboard' ? !empty($arquivosDashboardExistentes) : file_exists($arquivo))) { goto LiJ6W; } $formArray['campos']['geral'][$tipo . $r_empresa . 'BtnRemover'] = array('name' => $tipo . $r_empresa . 'BtnRemover', 'type' => 'button', 'reload' => false, 'value' => 'Remover', 'reject' => true, 'align' => 'right'); LiJ6W: $form->definirFormularioArray($formArray); if (!($formCodigo = $form->processarFormulario($form_tabela))) { goto KQMw4; } if ($formCodigo == 1) { goto rpFue; } if (!($formCodigo == 2)) { goto d2B89; } $Code = $form->campoValorEnviado($tipo . $r_empresa . 'Code'); if (!($tipo == 'alterarCallback')) { goto W6cFZ; } $Code = preg_replace('/function ' . $r_acaoId . ' ?\\(/sUu', 'function _' . $r_acaoId . ' (', trim($Code)); W6cFZ: if (!($tipo == 'alterarDashboard')) { goto y3ESC; } $dashboardConfiguracao = json_decode($Code, true); if (json_last_error() !== JSON_ERROR_NONE) { goto o2nue; } require_once 'dashboard-personalizado/config.php'; $dashboardErros = array(); if (dashboardPersonalizadoValidarConfiguracao($dashboardConfiguracao, $dashboardErros, false)) { goto nD773; } echo gGeraAlertMsg('Dashboard inválido', implode(' ', $dashboardErros)); $Code = false; nD773: goto TqwwD; o2nue: echo gGeraAlertMsg('JSON inválido', 'Corrija a configuração antes de salvar: ' . json_last_error_msg()); $Code = false; TqwwD: y3ESC: if (!($Code === false)) { goto YnuPb; } goto dashboardPersonalizadoNaoSalvar; YnuPb: file_put_contents($arquivo, $Code . "\n"); echo gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!'); dashboardPersonalizadoNaoSalvar: d2B89: goto Sexna; rpFue: echo gGeraAlertMsgErroForm($form); Sexna: KQMw4: $gerado = gGeraCamposForm($form, $tab, $r_acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk); $g['smarty']->assign('formularioID', $form->formularioID()); $g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : ''); echo $gerado['pagina']; if ($formMultiTab) { goto WFHWH; } $g["smarty"]->assign("formId", $formId); WFHWH: $g['smarty']->display('form.tpl'); if (!$original) { goto Tuo1x; } $original = htmlspecialchars($original, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); echo <<<EOT
<div id="{$tipo}{$r_empresa}OriginalCode" style="display: none;">
<textarea style="width: 800px; height: 400px; text-transform: none;">
{$original}
</textarea>
</div>
EOT;
echo <<<EOT
\t<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
\t\t\$('#{$tipo}{$r_empresa}BtnOriginal').click(function() {
\t\t\tdialogConfirm('Original', '', false, false, false, \$('#{$tipo}{$r_empresa}OriginalCode'));
\t\t});
\t} </script></div>
EOT;
Tuo1x: if (!$original2) { goto gtGXO; } $original2 = htmlspecialchars($original2, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); echo <<<EOT
<div id="{$tipo}{$r_empresa}OriginalCode2" style="display: none;">
<textarea style="width: 800px; height: 400px; text-transform: none;">
{$original2}
</textarea>
</div>
EOT;
echo <<<EOT
\t<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
\t\t\$('#{$tipo}{$r_empresa}BtnOriginal2').click(function() {
\t\t\tdialogConfirm('Original (arquivo inteiro)', '', false, false, false, \$('#{$tipo}{$r_empresa}OriginalCode2'));
\t\t});
\t} </script></div>
EOT;
gtGXO: echo <<<EOT
\t<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
\t\t\$('#{$tipo}{$r_empresa}BtnRemover').click(function() {
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
function getCodeDefault($tipo) { goto Ks_6n; Ks_6n: global $r_acaoId; goto yfUnz; yfUnz: switch ($tipo) { case 'ini': return <<<DEFAULT
<?php
/**
* Variáveis de Configurações do Sistema
* 
* Modifica variáveis utilizadas internamente pelo sistema.
*/

/*
Exemplo:
\$conf['ini']['formatoOrcamentoEmail'] = 'ambos'; // 'html' ou 'pdf' ou 'ambos'
EOT;
*/
DEFAULT;
goto HhDuv; case 'cssVars': return <<<DEFAULT
// Substituição de variáveis no CSS (veja arquivo original)
DEFAULT;
goto HhDuv; case 'restricaoIncluirUpdate': return <<<DEFAULT
<?php
/**
* restricaoIncluirUpdate
* 
* Código PHP para validar um campo de uma tabela na hora de Incluir ou Atualizar.
* Para ver variáveis e funções disponíveis dentro do código, veja:
* \t\thttp://flycker/wiki/index.php?title=Restri%C3%A7%C3%B5es_de_Campos
* Dica: utilize unset() para remover uma restrição definida global.
*/

/*
Exemplo:
\$conf['restricaoIncluirUpdate']['{$r_acaoId}_Campo'] = <<<'EOT'
\t// Código PHP de validação
\t// \$retorno = true; (OK)
\t// \$retorno = 'string'; (ERRO)
EOT;
*/
DEFAULT;
goto HhDuv; case 'restricaoExcluir': return <<<DEFAULT
<?php
/**
* ignorarRelacionamentoExcluir
* 
* Lista de relacionamentos de integridade referencial. 
* O registro somente poderá ser excluído caso um registro desta tabela não for chave primária de algum outro registro existente.
*/

/*
Exemplo:
\$conf['ignorarRelacionamentoExcluir'] = array_merge(\$conf['ignorarRelacionamentoExcluir'], 
\tarray('X000_{$r_acaoId}_Id', 'Y000_{$r_acaoId}_Id')
);
*/
DEFAULT;
goto HhDuv; case 'restricaoTravar': return <<<DEFAULT
<?php
/**
* restricaoTravar
* 
* Código PHP para validar se um registro de uma tabela pode: ser Editada, Incluida ou Removida.
* Para ver variáveis e funções disponíveis dentro do código, veja:
*\t\thttp://flycker/wiki/index.php?title=Restri%C3%A7%C3%B5es_de_Campos
* Dica: utilize unset() para remover uma restrição definida global.
*/

/*
Exemplo:
\$conf['restricaoTravar']['{$r_acaoId}'] = <<<'EOT'
\t// Código PHP de validação
\t// \$retorno = true; (OK)
\t// \$retorno = 'string'; (ERRO)
EOT;
*/
DEFAULT;
goto HhDuv; case 'camposNaoUsar': return <<<DEFAULT
<?php
/**
* camposNaoUsar
* 
* Lista de campos que não serão utilizados (será removido do FORM e do GRID).
*/

/*
Exemplo:
\$conf['camposNaoUsar'] = array_merge(\$conf['camposNaoUsar'], array(
\t'{$r_acaoId}_Campo'
));
*/
DEFAULT;
goto HhDuv; case 'tabsNaoUsar': return <<<'DEFAULT'
<?php
/**
* tabsNaoUsar
* 
* Lista de tabs que não serão utilizadas (não serão mostradas).
*/

/*
Exemplo:
$conf['tabsNaoUsar'] = array_merge($conf['tabsNaoUsar'], array(
	'cad001tabPrincipal' => array('Trabalho', 'Inativos'),
));
*/
DEFAULT;
goto HhDuv; case 'camposValorPadrao': return <<<DEFAULT
<?php
/**
* camposValorPadrao
* 
* Defina valores padrão para campos no formulário de inclusão.
*/

/*
Exemplo:
\$conf['camposValorPadrao']['{$r_acaoId}_Campo'] = date('d/m/Y');
*/
DEFAULT;
goto HhDuv; case 'camposLabelForm': return <<<DEFAULT
<?php
/**
* camposLabelForm
* 
* Modifica o 'label' do formulário de um campo.
*/

/*
Exemplo:
\$conf['camposLabelForm']['{$r_acaoId}_Campo'] = 'Novo Label';
*/
DEFAULT;
goto HhDuv; case 'camposTipo': return <<<DEFAULT
<?php
/**
* camposTipo
* 
* Define qual é o tipo do campo. Com esta informação é feita a formatação dele no FORM, GRID, etc.
*/

/*
Exemplos:
\$conf['camposTipo']['{$r_acaoId}_Campo'] = array('numero', 2);
\$conf['camposTipo']['{$r_acaoId}_Campo'] = array('data');
\$conf['camposTipo']['{$r_acaoId}_Campo'] = array('tempo');
\$conf['camposTipo']['{$r_acaoId}_Campo'] = array('cnpj');
\$conf['camposTipo']['{$r_acaoId}_Campo'] = array('cpf');
*/
DEFAULT;
goto HhDuv; case 'triggers': return <<<DEFAULT
<?php
/**
* triggers
* 
* Execução de código PHP antes/após INSERT/UPDATE/DELETE
* A chave primária sempre estará disponível (exceto em beforeInsert) pela variavél: \$chaveValor 
* IMPORTANTE: coloque um '\\' na função mysql_query caso use ela: mysqli_query() para utilizar a original do mysql
*/

/*
Exemplos:
\$conf['triggers']['{$r_acaoId}']['insertBefore'] = <<<'EOT'
\t// Código PHP
EOT;
\$conf['triggers']['{$r_acaoId}']['insertAfter'] = <<<'EOT'
\t// Código PHP
EOT;
\$conf['triggers']['{$r_acaoId}']['updateBefore'] = <<<'EOT'
\t// Código PHP
EOT;
\$conf['triggers']['{$r_acaoId}']['updateAfter'] = <<<'EOT'
\t// Código PHP
EOT;
\$conf['triggers']['{$r_acaoId}']['deleteBefore'] = <<<'EOT'
\t// Código PHP
EOT;
\$conf['triggers']['{$r_acaoId}']['deleteAfter'] = <<<'EOT'
\t// Código PHP
EOT;
*/
DEFAULT;
goto HhDuv; case 'altText': return <<<DEFAULT
<?php
/**
* Alterações de Textos
* 
* Altera textos utilizados dentro de qualquer parte do site (quando utilizado pela função \$g['conf']->altText
*/

/*
Exemplo:
\$conf['altText'] = array_merge(\$conf['altText'], array(
\t'Veículos' => 'Barcos'
));
*/
DEFAULT;
goto HhDuv; case 'alterarMetodos': goto lMCyn; lMCyn: $classe = explode('.', $r_acaoId); goto YxmaZ; Csj0V: return <<<DEFAULT
<?php
namespace hardness;
/**
* Alterar (sobre-escrever) Métodos de uma Classe
*/

class {$classe} extends {$classe}_ {
\t// defina os métodos para sobreescrever
}
DEFAULT;
goto dmv0q; YxmaZ: $classe = $classe[0]; goto Csj0V; dmv0q: goto HhDuv; goto uqF8w; uqF8w: case 'alterarPrograma': goto C5MhU; C5MhU: $programa = trim($r_acaoId, '/'); goto XN7R0; DwBRK: return <<<DEFAULT
<?php
namespace hardness;
/**
* Este código PHP será executado ao acessar: {$programa}
* Ele irá substituir o original (que fica dentro do .PHP, no switch/case)
*
* Caso seja uma AJAX, é necessário acrescentar este código:
* No inicio: \$resposta = array('code' => true, 'data' => array());
* No final: echo \$_REQUEST['callback'] . "(" . json_encode(\$resposta) . ");";
*/

DEFAULT;
goto KvHnW; KvHnW: goto HhDuv; goto wel97; XN7R0: $programa = "/{$programa}/"; goto DwBRK; wel97: case 'alterarCallback': $programa = trim($r_acaoId, '()'); return <<<DEFAULT
<?php
namespace hardness;
/**
* Este código PHP irá substituir o callback: {$programa}()
*/

DEFAULT;
case 'alterarSQL': return <<<DEFAULT
DEFAULT;
goto HhDuv; case 'alterarCSS': return <<<DEFAULT
DEFAULT;
case 'alterarFlag': return <<<DEFAULT
DEFAULT;
goto HhDuv; case 'auditoriaCampos': return <<<DEFAULT
<?php
/**
* auditoriaCampos
* 
* Lista de campos que serão feitas auditorias individuais (tabela T213)
*/

/*
Exemplo:
\$conf['auditoriaCampos'] = array_merge(\$conf['auditoriaCampos'], array(
\t'{$r_acaoId}_Campo'
));
*/
DEFAULT;
goto HhDuv; case 'menuBuilder': goto WrfO0; LFTK1: goto HhDuv; goto R56bD; WrfO0: $programa = trim($r_acaoId, '()'); goto Lncb9; Lncb9: return <<<'DEFAULT'
<?php
namespace hardness;
/**
* Alterações do menu, os itens aqui são incluídos automaticamente pelo Fast Crud
*/
// Exemplo:
//$menu['Vendas']['Orçamentos'] = "abrirJanela(event, divIdRootAberto(), '', menuId('per001'), 'Grid: Orçamentos', 'Orçamentos', '/per/per001/content/contentOrcamentoGrid/', '', 'auto', false, 'center');";

DEFAULT;
goto LFTK1; R56bD: } goto g0wDP; g0wDP: eaRyJ: goto Aazdc; Aazdc: HhDuv: goto SIpWL; SIpWL: }