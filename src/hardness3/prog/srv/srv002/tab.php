<?php
namespace hardness;
/**
 * Weiberlan
 * 08/11/2011
 */

/**
 * Ações
 */
switch ($g['r'][3]) {

	/**
	 * Grid Principal
	 * Chamado em: srv002content01
	 */
	case 'srv002tab01':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/srv/srv002/grid/srv002grid01/', 'Pendentes', '', false, true),
			array(uniqid(), '/srv/srv002/grid/srv002grid02/', 'Em Execução', '', false,true),
			array(uniqid(), '/srv/srv002/grid/srv002grid03/', 'Concluídos', '', false, true),
			array(uniqid(), '/srv/srv002/grid/srv002gridCancelados/', 'Cancelados', '', false, true),
			array(uniqid(), '/srv/srv002/grid/srv002gridGerenciamento/', 'Cronograma', '', false, true),
			array(uniqid(), '/srv/srv002/grid/srv002gridFuncionarios/', 'Funcionários', '', false, true),
			array(uniqid(), '/srv/srv002/grid/srv002gridVeiculos/', 'Veículos', '', false, true),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	
	break;


		/**
		 * Form Principal - Cadastro pedidos
		 * Chamado em: srv002content02
		 */

	case 'srv002tab02':
		$sql = "SELECT * FROM T147 WHERE T147_Id='$r_acaoId'";
		$result=mysql_query($sql);
		$aT147 = mysql_fetch_assoc($result);
		//if ($r_acaoId) {
			$tabs = array();
			$tabsList = array(
			);
			if(($aT147['T147_Status'] == "P") || ($aT147['T147_Status'] == "E")){
				$tab4Id = uniqid();
				array_push($tabsList, array($tab4Id, '/srv/srv002/form/srv002FormControle/', 'Controle', "&tab=geral&&acaoId=" . urlencode($r_acaoId)));
			}
			$tab5Id = uniqid();
			array_push($tabsList, array($tab5Id, '/srv/srv002/grid/srv002gridProdutos/', 'Produtos', "&acaoId=" . urlencode($r_acaoId)));
			$tab1Id = uniqid();
			array_push($tabsList, array($tab1Id, '/srv/srv002/grid/srv002grid04/', 'Processos', "&acaoId=" . urlencode($r_acaoId)));
			$tab2Id = uniqid();
			array_push($tabsList, array($tab2Id, '/srv/srv002/grid/srv002grid05/', 'Materiais', "&acaoId=" . urlencode($r_acaoId)));
			$tab3Id = uniqid();
			array_push($tabsList, array($tab3Id, '/srv/srv002/grid/srv002gridHistorico/', 'Histórico', "&acaoId=" . urlencode($r_acaoId)));
			// Processa Tabs
			echo gProcessaTabs($tabsList, $tabs);

			// Carrega template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('tab.tpl');
		//}

		break;

	case 'srv002tab03':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/srv/srv002/grid/srv002grid06/', 'Competências', '&status=' . $r_status),
			array(uniqid(), '/srv/srv002/grid/srv002grid07/', 'Materiais', '&status=' . $r_status),

		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
		break;
		
	case 'srv002TabIncluir':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/srv/srv002/form/srv002FormularioInclusao/', 'Incluir', '&tab=geral&status=' . $r_status),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'srv002tabHistVeiculo':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
		);
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
		$sql = "SELECT T147_Status FROM T147 WHERE T147_Id='{$r_acaoId}'";
		$result = mysql_query($sql);
		$linha = mysql_fetch_array($result);
		if($linha['T147_Status']=="C"){
			$tab1Id = uniqid();
			array_push($tabsList, array($tab1Id, '/srv/srv002/grid/srv002grid04/', 'Processos', "&acaoId=" . urlencode($r_acaoId)));
			$tab2Id = uniqid();
			array_push($tabsList, array($tab2Id, '/srv/srv002/grid/srv002grid05/', 'Materiais', "&acaoId=" . urlencode($r_acaoId)));
		}else{
			$tab1Id = uniqid();
			array_push($tabsList, array($tab1Id, '/srv/srv002/form/srv002FormControle/', 'Controle', "&tab=geral&acaoId=" . urlencode($r_acaoId)));
			$tab2Id = uniqid();
			array_push($tabsList, array($tab2Id, '/srv/srv002/grid/srv002grid04/', 'Processos', "&acaoId=" . urlencode($r_acaoId)));
			$tab3Id = uniqid();
			array_push($tabsList, array($tab3Id, '/srv/srv002/grid/srv002grid05/', 'Materiais', "&acaoId=" . urlencode($r_acaoId)));
			$tab4Id = uniqid();
			array_push($tabsList, array($tab4Id, '/srv/srv002/grid/srv002gridHistVeiculo/', 'Histórico', "&acaoId=" . urlencode($r_acaoId)));
		}

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
}
?>