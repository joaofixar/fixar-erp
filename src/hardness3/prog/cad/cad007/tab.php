<?php
namespace hardness;
/**
* Ações
*/
switch ($g['r'][3]) {
	
	/**
	* Grid Principal
	* Chamado em: cad007content01
	*/
	case 'cad007tab01':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
	
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad007/grid/cad007grid01/', 'Certificados'),			
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	/**
	* Form Principal - Cadastro certificado
	* Chamado em: cad007content02
	*/
	case 'cad007tab02':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		// Definição das Tabs
		$tabs = array();
		if(!empty($r_acaoId)){
			$tabsList[] = array(uniqid(), '/cad/cad007/content/cad007ContentProdutos/','Produtos', "&acaoId=" . urlencode($_REQUEST['acaoId']));						
		}
		$tabsList[] = array(uniqid(), '/cad/cad007/form/cad007form01/', 'Cadastro de Certificado', "&tab=cadastro_certificado&acaoId=" . urlencode($_REQUEST['acaoId']));	
		
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
}
?>