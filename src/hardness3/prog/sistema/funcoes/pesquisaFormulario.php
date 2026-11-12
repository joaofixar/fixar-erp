<?php
namespace hardness;
global $g;
$params = $_REQUEST['sql'];
$labels = $_REQUEST['labels'];
$sqlExtra = ($_REQUEST['sqlExtra'] != 'undefined') ? trim($_REQUEST['sqlExtra'], '[]') : '';
$listaParams = trim($params, '[]');
$listaLabels = trim($labels, '[]');
$params = explode(',',trim($params, '[]'));
$divIdAberto = $_REQUEST['divIdAberto'];
$arrayConfig = array();
foreach($params as $campo){
	if (strpos($campo, ' as ') !== false) { 
		$arrayConfig[] = array('callback' => 'gCorrigeData');
	} else {
		$arrayConfig[] = array();
	}
}

/**
 * PARTE 1: Variavéis envidas para o formulário
 */
// Variável indica qual tab do form será exibida
$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
// Utilizada em UPDATE, é o ID do registro que está sendo editado
$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;

/**
 * PARTE 2: Definição do formulário
 */
// Este mesmo form será exibido em multiplas tabs ou não?
$formMultiTab = false;
if ($formMultiTab) {
	$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
} else {
	$formId = uniqid();
}
// A qual tabela este form está relacionada, e a chave primária da tabela
$form_tabela = substr($params[0], 0, 4);
$form_campo_pk = substr($params[0], 0, 4).'_Id';
// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
$enviar_btnId = '';
// Form que será executado ao clicar no botão
$enviar_acaoHref = '';		
// novo formulário
$form = new Formulario('formPesquisaFormulario');

$pesquisaGrid = array(
	$form_tabela,																									// tabela
	$form_campo_pk,																									// chave primaria
	$listaParams,																									// campos
	$sqlExtra, 																										// extras sql
	'Pesquisa',																										// título da janela
	$listaLabels,																									// descrição dos campos
	'pesquisaFormulario',																							// callback js
	$g['divId'],																									// divId
	array($divIdAberto, $g['divIdRoot']),																			// parametro extra
	$arrayConfig,																									// parametros para cada campo do grid	
);

// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
$formArray = array(
	'campos' => array(
		'geral' => array(
			'campoPesquisa' => array('name' => 'campoPesquisa', 'label'=>'', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGrid, 'reject' => true, 'length' => array(53,53)),
		)
	)
);
$form->definirFormularioArray($formArray);
/**
 * PARTE 3: Processamento do formulário (se foi enviado)
 * 		   E carregamento dos valores (se não foi enviado e possui acaoId)
 */

if($formCodigo = $form->processarFormulario($form_tabela)) {
	// Form com erros
	if($formCodigo == 2) {
	}
}


/**
* PARTE 4: Gerar o formulário
*/
$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

/**
 * PARTE 5: Exibir o formulário
 */
$g['smarty']->assign('formularioID', $form->formularioID());
$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
echo $gerado['pagina'];
if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
$g['smarty']->display('form.tpl');
?>
