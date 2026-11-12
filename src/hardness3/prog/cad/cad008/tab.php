<?php
namespace hardness;
/**
 * Ações
 */
switch ($g['r'][3]) {

	/**
	 * Grid Principal
	 * Chamado em: cad008content01
	 */
	case 'cad008tab01':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
		array(uniqid(), '/cad/cad008/grid/cad008grid01/', 'Bens'),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
		break;

		/**
		 * Form Principal - Cadastro bem
		 * Chamado em: cad008content02
		 */
	case 'cad008tab02':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		if(empty($r_acaoId)){
			$tabs = array();
			$tabsList = array(
				array(uniqid(), '/cad/cad008/form/cad008form01/', 'Cadastro de Bem', "&tab=Bens&acaoId=" . urlencode($r_acaoId)),
			);
		}else{
			$tabs = array();
			$tabsList = array(
				array(uniqid(), '/cad/cad008/form/cad008form01/', 'Cadastro de Bem', "&tab=Bens&acaoId=" . urlencode($r_acaoId)),
				array(uniqid(), '/cad/cad008/grid/cad008GridEspecificacoes/', 'Especificações', "&tab=geral&acaoId=" . urlencode($r_acaoId))
			);
		}

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
		break;
	case 'cad008tab03':
		if ($_REQUEST['acaoId']) {
		$tabs = array();
		$tabsList = array(
		);
			$tab2Id = uniqid();
			array_push($tabsList, array($tab2Id, '/cad/cad008/grid/cad008grid02/', 'Manutenções de bem', "&tab=manutencao_bem&acaoId=" . urlencode($_REQUEST['acaoId'])));
			$tab3Id = uniqid();
			array_push($tabsList, array($tab3Id, '/cad/cad008/grid/cad008grid04/', 'Logística de bem', "&tab=logistica_bem&acaoId=" . urlencode($_REQUEST['acaoId'])));
			$tab4Id = uniqid();
			array_push($tabsList, array($tab4Id, '/cad/cad008/grid/cad008grid03/', 'Contratos de bem', "&tab=contrato_bem&acaoId=" . urlencode($_REQUEST['acaoId'])));
			// $tab5Id = uniqid();
			// array_push($tabsList, array($tab5Id, '/cad/cad008/grid/cad008gridEspecificacoes/', 'Especificações', "&tab=geral&acaoId=" . urlencode($_REQUEST['acaoId'])));			//$tab5Id = uniqid();
			//array_push($tabsList, array($tab5Id, '/cad/cad008/grid/cad008GridChecklist/', 'Checklist', "&tab=geral&acaoId=" . urlencode($r_acaoId)));
			// $tab6Id = uniqid();
			// array_push($tabsList, array($tab6Id, '/cad/cad008/grid/cad008GridEspecificacoes/', 'Especificações', "&tab=geral&acaoId=" . urlencode($r_acaoId)));		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
		}
}
?>