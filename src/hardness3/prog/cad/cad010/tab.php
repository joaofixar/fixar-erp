<?php
namespace hardness;
/**
 * Ações
 */
switch ($g['r'][3]) {

	/**
	 * Grid Principal
	 * Chamado em: cad010content01
	 */
	case 'cad010tab01':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
		array(uniqid(), '/cad/cad010/grid/cad010grid01/', 'Funcionários'),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
		break;

		/**
		 * Form Principal - Cadastro de funcionários
		 * Chamado em: cad010content02
		 */
	case 'cad010tab02':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
		array(uniqid(), '/cad/cad010/form/cad010form01/', 'Cadastro de Funcionário', '&typeForm=' . urlencode($r_typeForm) . '&tab=funcionarios&acaoId=' . urlencode($_REQUEST['acaoId'])),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
		break;

	case 'cad010tab03':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
		array(uniqid(), '/cad/cad010/form/cad010form01/', 'Cadastro de Funcionário', "&tab=funcionarios&acaoId=" . urlencode($_REQUEST['acaoId'])),
		//array(uniqid(), '/cad/cad010/content/cad010ContentCompetencias/', 'Competências', "&tab=competencias&acaoId=" . urlencode($_REQUEST['acaoId'])),
		array(uniqid(), '/cad/cad010/content/cad010ContentDocumentos/', 'Documentos', "&acaoId=" . urlencode($r_acaoId) . "&D109_Id=" . urlencode($r_acaoId)),
		//array(uniqid(), '/cad/cad010/grid/cad010GridHistorico/', 'Histórico', "&acaoId=" . urlencode($r_acaoId) . "&D109_Id=" . urlencode($r_acaoId)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
		break;
		
		case 'cad010TabCadastroDigital':			
			// Form (caso seja o mesmo form em multiplas tabs)
			//$formId = uniqid();
			//$g['smarty']->assign('formId', $formId);
		
			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
				// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
				array(uniqid(), '/cad/cad010/outros/cad010OutrosCadastrarDigital/', 'Senha', '&acaoId=' . urlencode($r_acaoId)),
			);
			
			// Processa Tabs
			$g['conteudo']['pagina'] = gProcessaTabs($tabsList, $tabs);
			
			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('big.tab.tpl');
		break;
}
?>