<?php
 namespace hardness; if (!($g['r'][3] === 'alterarPortalRepresentante')) { goto uWi5G; } require 'portal-representante/editor-form.php'; return; uWi5G: if (!($g['r'][3] === 'alterarPainel')) { goto bk44U; } require 'painel-personalizado/editor-form.php'; return; bk44U: $pathDados = $g['pathDados']; if ($g['modoDesenvolvedor']) { goto GDXTl; } if (!$g['modoDesenvolvedorApp']) { goto mkQLl; } Personalizacoes::$rootDir = Apps::getDirEditor(); $pathDados = Apps::getDirEditor(); mkQLl: goto njFT3; GDXTl: Personalizacoes::$rootDir = 'ide/'; $pathDados = 'ide/'; njFT3: echo <<<EOT
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
$tab = isset($_REQUEST['tab']) ? $_REQUEST['tab'] : false; $tipo = $g['r'][3]; $formNaoRecarregar = true; $formMultiTab = false; if ($formMultiTab) { goto kbX0t; } $formId = uniqid(); goto a3nnQ; kbX0t: $formId = isset($r_formcad001FormCadastro) ? $r_formcad001FormCadastro['formId'] : $r_formId; a3nnQ: $form_tabela = 'D000'; $form_campo_pk = 'D000_Id'; $enviar_btnId = $tipo . $r_empresa . 'Btn'; $enviar_acaoHref = '/sistema/personalizacaoConf/form/' . $tipo . '/'; $dir = "{$pathDados}personalizacaoConf/"; is_dir($dir) or mkdir($dir, 0777, true); if ($tipo == 'alterarDashboard') { goto FwfIJ; } if ($tipo == 'cssVars') { goto IeVMN; } if ($tipo == 'alterarCSS') { goto zacyd; } if ($tipo == 'alterarFlag') { goto QPfIn; } $r_acaoId = trim(urldecode($r_acaoId), '()'); $arquivo = $dir . $r_empresa . $r_acaoId . '-' . $tipo . '.php'; goto GnGN6; QPfIn: $arquivo = $g['pathDadosUsuario'] . strtolower($r_acaoId); GnGN6: goto Nk8Jw; zacyd: $arquivo = $dir . $r_empresa . $r_acaoId . '-' . $tipo . '.css'; Nk8Jw: goto f5DE1; IeVMN: $arquivo = $dir . $r_empresa . $r_acaoId . '-' . $tipo . '.ini'; f5DE1: goto HmOp_; FwfIJ: if (!empty($r_empresa)) { goto XW6sn; } echo gGeraAlertMsg('Empresa não informada', 'Selecione a empresa antes de personalizar o dashboard.'); return; XW6sn: $arquivo = $dir . (int) $r_empresa . 'DASHBOARD PERSONALIZAVEL-alterarDashboard.json'; $arquivosDashboard = array_values(array_unique(array($arquivo, $dir . (int) $r_empresa . 'DASHBOARD+PERSONALIZAVEL-alterarDashboard.json', $dir . (int) $r_empresa . 'DASHBOARD-PERSONALIZAVEL-alterarDashboard.json'))); $arquivosDashboardExistentes = array_values(array_filter($arquivosDashboard, 'file_exists')); usort($arquivosDashboardExistentes, function ($arquivoA, $arquivoB) { return (int) @filemtime($arquivoB) - (int) @filemtime($arquivoA); }); $arquivoLeitura = !empty($arquivosDashboardExistentes) ? $arquivosDashboardExistentes[0] : $arquivo; foreach ($arquivosDashboardExistentes as $arquivoDashboardExistente) { goto VGC8r; nPXAJ: b7Gkv: goto vXzBp; T0qI9: qFeeY: goto nPXAJ; VGC8r: $configuracaoDashboardExistente = json_decode(file_get_contents($arquivoDashboardExistente), true); goto Lsjsi; Lsjsi: if (!(is_array($configuracaoDashboardExistente) && !empty($configuracaoDashboardExistente['habilitado']))) { goto qFeeY; } goto m4btL; wMzHD: goto zKwYJ; goto T0qI9; m4btL: $arquivoLeitura = $arquivoDashboardExistente; goto wMzHD; vXzBp: } zKwYJ: HmOp_: if (!isset($r_remover)) { goto dw0Al; } if ($tipo == 'alterarDashboard') { goto AAgZT; } unlink($arquivo); goto DHaTQ; AAgZT: foreach ($arquivosDashboard as $arquivoDashboard) { goto ZOj9j; ZOj9j: if (!file_exists($arquivoDashboard)) { goto PdN8M; } goto kY0oB; T2HlV: PdN8M: goto FFJ9b; FFJ9b: xL7Vy: goto y6GH7; kY0oB: unlink($arquivoDashboard); goto T2HlV; y6GH7: } QjCQp: $arquivosDashboardExistentes = array(); DHaTQ: echo gGeraAlertMsg('Removido', 'Configuração removida!'); dw0Al: if (file_exists(isset($arquivoLeitura) ? $arquivoLeitura : $arquivo)) { goto G9Sy2; } if ($tipo == 'alterarDashboard' && file_exists('configuracoes/dashboard-personalizavel.json')) { goto gb_Bi; } if ($tipo == 'menu') { goto sUZZw; } if ($tipo == 'colunaEditarRestricoes') { goto yCy6r; } $Code = getCodeDefault($tipo); goto bgya1; yCy6r: $Code = <<<'EOT'
<?php
// Adicionar novos campos
//$g['restricaoCamposEditar'] = array_merge($g['restricaoCamposEditar'], array('NOME_CAMPO', 'NOME_CAMPO_02'));

// Remover um campo
//unset($g['restricaoCamposEditar'][array_search('NOME_CAMPO', $g['restricaoCamposEditar'])]);

// Substituir todos os registros
//$g['restricaoCamposEditar'] = array('NOME_CAMPO', 'NOME_CAMPO_02');
EOT;
bgya1: goto AWW6J; sUZZw: $Code = file_get_contents('bibliotecas/menu.php'); AWW6J: goto N6yQx; gb_Bi: $Code = file_get_contents('configuracoes/dashboard-personalizavel.json'); N6yQx: goto wImO9; G9Sy2: $Code = file_get_contents(isset($arquivoLeitura) ? $arquivoLeitura : $arquivo); wImO9: $form = new Formulario($tipo . 'Form'); $tabela = $r_acaoId == 'GLOBAL' || $r_acaoId == 'CAMPOS_LIVRE' ? '_GLOBAL' : ''; $tabela = $r_acaoId == 'GLOBAL' && $tipo == 'altText' ? '_alteracoesTextuais' : $tabela; $tabela = $r_acaoId == 'GLOBAL' && $tipo == 'cssVars' ? '_cssVars' : $tabela; $tabela = empty($tabela) ? $r_acaoId : $tabela; $codeLanguage = 'php'; if ($tipo == 'alterarDashboard') { goto hWFYB; } if ($tipo == 'cssVars') { goto Djdhv; } if ($tipo == 'alterarMetodos') { goto oqE1h; } if ($tipo == 'menu') { goto CRZjd; } if ($tipo == 'menuBuiler') { goto dOMSt; } if ($tipo == 'colunaEditarRestricoes') { goto bL6ft; } if ($tipo == 'alterarSQL') { goto TTwp9; } if ($tipo == 'alterarCSS') { goto jjoP5; } if ($tipo == 'alterarFlag') { goto gzwnX; } if ($tipo == 'menu') { goto kM1Qz; } if (!($tipo == 'colunaEditarRestricoes')) { goto c3wmq; } $original = "configuracoes/_colunaEditarRestricoes.php"; c3wmq: goto OyfxT; kM1Qz: $original = "bibliotecas/menu.php"; OyfxT: if ($tipo == 'alterarPrograma') { goto lK4Yt; } $original = "configuracoes/{$tabela}.php"; goto wVlmL; lK4Yt: $prog = explode('-', $tabela); if ($prog[0] == 'per') { goto L8TMD; } if (!(count($prog) > 4)) { goto M3klE; } $prog = array($prog[0], $prog[1], $prog[2] . '-' . $prog[3], $prog[4]); M3klE: $original = "prog/{$prog[0]}/{$prog[1]}/{$prog[2]}.php"; goto Bj4qm; L8TMD: $original = "{$pathDados}/builder/prog/{$prog[1]}/{$prog[2]}.php"; Bj4qm: wVlmL: goto yEt97; gzwnX: $codeLanguage = 'json'; $original = ''; yEt97: goto ZVNWk; jjoP5: $codeLanguage = 'css'; $original = strtolower("static/css/{$tabela}"); ZVNWk: goto MkdWA; TTwp9: $codeLanguage = 'sql'; $original = "scripts/sql/{$tabela}"; MkdWA: goto KoX0b; bL6ft: $original = 'configuracoes/_colunaEditarRestricoes.php'; KoX0b: goto j2KW8; dOMSt: $original = 'bibliotecas/menu.php'; j2KW8: goto DmzHn; CRZjd: $original = 'bibliotecas/menu.php'; DmzHn: goto RxPae; oqE1h: $original = "bibliotecas/classes/{$tabela}"; $original = str_replace('PHP', 'php', $original); RxPae: goto SGC7X; Djdhv: $codeLanguage = 'css'; $original = "configuracoes/{$tabela}.ini"; SGC7X: goto cYCwG; hWFYB: $codeLanguage = 'json'; $original = 'configuracoes/dashboard-personalizavel.json'; cYCwG: if (file_exists($original)) { goto Ib86X; } $original = false; if (!($tipo == 'alterarCallback')) { goto RTzSU; } $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator('bibliotecas/funcoesCallback/')); foreach ($iterator as $path) { goto XodkL; qalC4: $original = $matches[0][0]; goto YAE56; ylP89: $original2 = $src; goto PcXRK; YAE56: qnQMp: goto Uksqh; yLNEu: if (!(isset($matches[1]) && !empty($matches[1]))) { goto qnQMp; } goto qalC4; XodkL: $src = file_get_contents($path); goto ylP89; PcXRK: preg_match_all('/function ' . $r_acaoId . ' ?\\((.*)(function|$)/sUu', $src, $matches); goto yLNEu; Uksqh: DnIL7: goto PrYwV; PrYwV: } IaAty: $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator('prog/')); foreach ($iterator as $path) { goto a50ut; GMMNL: preg_match_all('/function ' . $r_acaoId . ' ?\\((.*)(function|$)/sUu', $src, $matches); goto C3Hc0; xsRNq: $original2 = $src; goto GMMNL; oRWXa: nJaQS: goto DBLU7; Rhmij: ahDow: goto baaj7; a50ut: if (!(strpos($path, '-callback.php') !== false)) { goto oYNXm; } goto czY4X; baaj7: oYNXm: goto oRWXa; czY4X: $src = file_get_contents($path); goto xsRNq; C3Hc0: if (!(isset($matches[1]) && !empty($matches[1]))) { goto ahDow; } goto e5nx3; e5nx3: $original = $matches[0][0]; goto Rhmij; DBLU7: } JSQbe: RTzSU: goto U5uB8; Ib86X: $original = file_get_contents($original); if (!($tipo == 'alterarPrograma')) { goto pLMDj; } $original2 = $original; preg_match("/case '{$prog[3]}':(.*)break;/msUu", $original, $matches); if (!isset($matches[1])) { goto EjDl3; } $original = $matches[1]; EjDl3: pLMDj: U5uB8: $original2 = !isset($original2) ? '' : $original2; $formArray = array('campos' => array('geral' => array($tipo . $r_empresa . 'Code' => array('name' => $tipo . $r_empresa . 'Code', 'label' => '', 'type' => 'textarea', 'uppercase' => false, 'value' => $Code, 'code' => $codeLanguage)))); $formArray['campos']['geral'][$tipo . $r_empresa . 'Btn'] = array('name' => $tipo . $r_empresa . 'Btn', 'type' => 'button', 'reload' => false, 'value' => 'Alterar (CTRL+S)', 'reject' => true); $formArray['campos']['geral'][$tipo . $r_empresa . 'BtnCopiar'] = array('name' => $tipo . $r_empresa . 'BtnCopiar', 'type' => 'button', 'reload' => false, 'value' => 'Copiar', 'align' => 'right', 'reject' => true); $formArray['campos']['geral'][$tipo . $r_empresa . 'BtnOriginal'] = array('name' => $tipo . $r_empresa . 'Btn', 'type' => 'button', 'reload' => false, 'value' => 'Visualizar Original', 'reject' => true, 'align' => 'right', 'style' => !$original ? 'opacity: 0.5;' : ''); $formArray['campos']['geral'][$tipo . $r_empresa . 'BtnOriginal2'] = array('name' => $tipo . $r_empresa . 'Btn2', 'type' => 'button', 'reload' => false, 'value' => 'Visualizar Original (arquivo inteiro)', 'reject' => true, 'align' => 'right', 'style' => !$original2 ? 'opacity: 0.5;' : ''); if (!($tipo == 'cssVars')) { goto qySkq; } $formArray['campos']['geral']['addTemaBtn'] = array('name' => 'addTemaBtn', 'type' => 'button', 'reload' => false, 'value' => 'Adicionar Tema', 'reject' => true, 'align' => 'right'); qySkq: if (!($tipo == 'alterarDashboard' ? !empty($arquivosDashboardExistentes) : file_exists($arquivo))) { goto UC3bg; } $formArray['campos']['geral'][$tipo . $r_empresa . 'BtnRemover'] = array('name' => $tipo . $r_empresa . 'BtnRemover', 'type' => 'button', 'reload' => false, 'value' => 'Remover', 'reject' => true, 'align' => 'right'); UC3bg: $form->definirFormularioArray($formArray); if (!($formCodigo = $form->processarFormulario($form_tabela))) { goto La3bL; } if ($formCodigo == 1) { goto z0rTR; } if (!($formCodigo == 2)) { goto Pnxcu; } $Code = $form->campoValorEnviado($tipo . $r_empresa . 'Code'); if (!($tipo == 'alterarCallback')) { goto Da982; } $Code = preg_replace('/function ' . $r_acaoId . ' ?\\(/sUu', 'function _' . $r_acaoId . ' (', trim($Code)); Da982: if (!($tipo == 'alterarDashboard')) { goto DJxkf; } $dashboardConfiguracao = json_decode($Code, true); if (json_last_error() !== JSON_ERROR_NONE) { goto RMRy6; } require_once 'dashboard-personalizado/config.php'; $dashboardErros = array(); if (dashboardPersonalizadoValidarConfiguracao($dashboardConfiguracao, $dashboardErros, false)) { goto QT3qz; } echo gGeraAlertMsg('Dashboard inválido', implode(' ', $dashboardErros)); $Code = false; QT3qz: goto eKjum; RMRy6: echo gGeraAlertMsg('JSON inválido', 'Corrija a configuração antes de salvar: ' . json_last_error_msg()); $Code = false; eKjum: DJxkf: if (!($Code === false)) { goto AYD32; } goto dashboardPersonalizadoNaoSalvar; AYD32: file_put_contents($arquivo, $Code . "\n"); echo gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!'); dashboardPersonalizadoNaoSalvar: Pnxcu: goto Gcuj6; z0rTR: echo gGeraAlertMsgErroForm($form); Gcuj6: La3bL: $gerado = gGeraCamposForm($form, $tab, $r_acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk); $g['smarty']->assign('formularioID', $form->formularioID()); $g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : ''); echo $gerado['pagina']; if ($formMultiTab) { goto Fh60I; } $g["smarty"]->assign("formId", $formId); Fh60I: $g['smarty']->display('form.tpl'); if (!$original) { goto YQ_y_; } $original = htmlspecialchars($original, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); echo <<<EOT
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
YQ_y_: if (!$original2) { goto hXlVr; } $original2 = htmlspecialchars($original2, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); echo <<<EOT
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
hXlVr: echo <<<EOT
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
function getCodeDefault($tipo) { goto xvW7j; xvW7j: global $r_acaoId; goto selsH; selsH: switch ($tipo) { case 'ini': return <<<DEFAULT
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
goto kKFBz; case 'cssVars': return <<<DEFAULT
// Substituição de variáveis no CSS (veja arquivo original)
DEFAULT;
goto kKFBz; case 'restricaoIncluirUpdate': return <<<DEFAULT
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
goto kKFBz; case 'restricaoExcluir': return <<<DEFAULT
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
goto kKFBz; case 'restricaoTravar': return <<<DEFAULT
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
goto kKFBz; case 'camposNaoUsar': return <<<DEFAULT
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
goto kKFBz; case 'tabsNaoUsar': return <<<'DEFAULT'
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
goto kKFBz; case 'camposValorPadrao': return <<<DEFAULT
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
goto kKFBz; case 'camposLabelForm': return <<<DEFAULT
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
goto kKFBz; case 'camposTipo': return <<<DEFAULT
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
goto kKFBz; case 'triggers': return <<<DEFAULT
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
goto kKFBz; case 'altText': return <<<DEFAULT
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
goto kKFBz; case 'alterarMetodos': goto pK2ra; uyXmk: goto kKFBz; goto r3_Yn; e3eVi: $classe = $classe[0]; goto W0XhF; pK2ra: $classe = explode('.', $r_acaoId); goto e3eVi; W0XhF: return <<<DEFAULT
<?php
namespace hardness;
/**
* Alterar (sobre-escrever) Métodos de uma Classe
*/

class {$classe} extends {$classe}_ {
\t// defina os métodos para sobreescrever
}
DEFAULT;
goto uyXmk; r3_Yn: case 'alterarPrograma': goto FqTsD; KkmRR: goto kKFBz; goto kAUVn; hvZPd: return <<<DEFAULT
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
goto KkmRR; kqevT: $programa = "/{$programa}/"; goto hvZPd; FqTsD: $programa = trim($r_acaoId, '/'); goto kqevT; kAUVn: case 'alterarCallback': $programa = trim($r_acaoId, '()'); return <<<DEFAULT
<?php
namespace hardness;
/**
* Este código PHP irá substituir o callback: {$programa}()
*/

DEFAULT;
case 'alterarSQL': return <<<DEFAULT
DEFAULT;
goto kKFBz; case 'alterarCSS': return <<<DEFAULT
DEFAULT;
case 'alterarFlag': return <<<DEFAULT
DEFAULT;
goto kKFBz; case 'auditoriaCampos': return <<<DEFAULT
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
goto kKFBz; case 'menuBuilder': goto ulmnm; ulmnm: $programa = trim($r_acaoId, '()'); goto V_plN; V_plN: return <<<'DEFAULT'
<?php
namespace hardness;
/**
* Alterações do menu, os itens aqui são incluídos automaticamente pelo Fast Crud
*/
// Exemplo:
//$menu['Vendas']['Orçamentos'] = "abrirJanela(event, divIdRootAberto(), '', menuId('per001'), 'Grid: Orçamentos', 'Orçamentos', '/per/per001/content/contentOrcamentoGrid/', '', 'auto', false, 'center');";

DEFAULT;
goto XrRp5; XrRp5: goto kKFBz; goto JqUvr; JqUvr: } goto sGfct; Wh7Mc: kKFBz: goto W0Bmw; sGfct: t1UGE: goto Wh7Mc; W0Bmw: }