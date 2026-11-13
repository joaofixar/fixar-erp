<?php
namespace hardness;
/**
* Ações
*/
switch ($g['r'][3]) {
	
	/**
	* Tab Leitura
	* Chamado em: etc006ContentLeitura
	*/
	case 'etc006TabRetornosPendentesAgenda':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/sistema/outros/grid/etc006GridRetornosPendenteAgenda/', 'Retornos pendentes', '')
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
}
?>