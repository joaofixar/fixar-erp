<?php
namespace hardness;
switch ($g['r'][3]) {

	/**
	 * Grid Principal
	 * Chamado em: ven001ContentPrincipal
	 */
	case 'cad013TabAgendaContatos':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad013/grid/cad013GridAgendaContatos/', 'Agenda de Contatos'),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
		
	break;
	
	case 'cad013TabEditarContatos':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array();
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
		$tabsList[] = array(uniqid(), '/cad/cad013/form/cad013FormEditarContatos/', 'Cadastro', "&tab=geral&acaoId=" . urlencode($r_acaoId));
		if (!empty($r_acaoId)) {
			$tabsList[] = array(uniqid(), '/cad/cad013/content/cad013ContentEmail/', 'E-Mail', "&tab=geral&acaoId=" . urlencode($r_acaoId));
		}

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
		
	break;
}
?>