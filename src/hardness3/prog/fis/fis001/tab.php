<?php
namespace hardness;
/**
* Ações
*/
switch ($g['r'][3]) {
	
	/**
	* Lista os GRIDs em várias abas
	* Chamado em: fis001content01
	*/
	case 'fis001tab01':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
	
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/fis/fis001/grid_cfop/fis001grid01/', 'Vendas'),
			array(uniqid(), '/fis/fis001/grid_cfop/fis001grid02/', 'Compras'),
			array(uniqid(), '/fis/fis001/grid_cfop/fis001grid03/', 'Transferências'),
			array(uniqid(), '/fis/fis001/grid_cfop/fis001grid04/', 'Outras saídas'),
			array(uniqid(), '/fis/fis001/grid_cfop/fis001grid05/', 'Outras entradas'),
			array(uniqid(), '/fis/fis001/grid_cfop/fis001grid06/', 'Devolução compras'),
			array(uniqid(), '/fis/fis001/grid_cfop/fis001grid07/', 'Devolução vendas'),
			array(uniqid(), '/fis/fis001/grid_cfop/fis001GridGeral/', 'Geral'),
			array(uniqid(), '/fis/fis001/grid_cfop/fis001gridInativas/', 'Inativas'),
			array(uniqid(), '/fis/fis001/grid/fis001GridRegras/', 'Regras venda'),
			array(uniqid(), '/fis/fis001/grid/fis001GridRegrasCompras/', 'Regras compra'),
		);
		
		// Processa Tabs
		$g['conteudo']['pagina'] = gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	/**
	* Tab do formulário
	* Chamado em: fis001content02
	*/
	case 'fis001tab02':
		
		// Verificando qual D059 está selecionado
		$sqlD006 = mysql_query("SELECT D006_D059_Id FROM D006 WHERE D006_Id=".$_REQUEST['acaoId']);
		$rowD006 = mysql_fetch_assoc($sqlD006);

		// Form (caso seja o mesmo form em multiplas tabs, senão comente as duas linhas)
		$formId = uniqid();
		$g['smarty']->assign('formId', $formId);
	
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/fis/fis001/form/fis001form01/', 'Geral', "&tab=geral&acaoId=" . urlencode($_REQUEST['acaoId']) . "&formId=" . urlencode($formId)),
			array(uniqid(), '/fis/fis001/form/fis001form01/', 'Estoque', "&tab=estoque&acaoId=" . urlencode($_REQUEST['acaoId']) . "&formId=" . urlencode($formId)),
			array(uniqid(), '/fis/fis001/form/fis001form01/', 'Financeiro', "&tab=financeiro&acaoId=" . urlencode($_REQUEST['acaoId']) . "&formId=" . urlencode($formId)),
			array(uniqid(), '/fis/fis001/form/fis001form01/', 'Retorno', "&tab=retorno&acaoId=" . urlencode($_REQUEST['acaoId']) . "&formId=" . urlencode($formId)),
			array(uniqid(), '/fis/fis001/form/fis001form01/', 'ICMS', "&tab=icms&acaoId=" . urlencode($_REQUEST['acaoId']) . "&formId=" . urlencode($formId) . "&D059_Id=" . urlencode($rowD006['D006_D059_Id'])),
			array(uniqid(), '/fis/fis001/form/fis001form01/', 'IPI', "&tab=ipi&acaoId=" . urlencode($_REQUEST['acaoId']) . "&formId=" . urlencode($formId)),
			//array(uniqid(), '/fis/fis001/form/fis001form01/', 'ST', "&tab=st&acaoId=" . urlencode($_REQUEST['acaoId']) . "&formId=" . urlencode($formId)),
			array(uniqid(), '/fis/fis001/form/fis001form01/', 'PIS', "&tab=pis&acaoId=" . urlencode($_REQUEST['acaoId']) . "&formId=" . urlencode($formId)),
			array(uniqid(), '/fis/fis001/form/fis001form01/', 'Cofins', "&tab=cofins&acaoId=" . urlencode($_REQUEST['acaoId']) . "&formId=" . urlencode($formId)),
			array(uniqid(), '/fis/fis001/form/fis001form01/', 'Outros', "&tab=outros&acaoId=" . urlencode($_REQUEST['acaoId']) . "&formId=" . urlencode($formId)),			
			array(uniqid(), '/fis/fis001/form/fis001form01/', 'Reforma', "&tab=reforma&acaoId=" . urlencode($_REQUEST['acaoId']) . "&formId=" . urlencode($formId)),			
		);
		
		// Processa Tabs
		$g['conteudo']['pagina'] = gProcessaTabs($tabsList, $tabs, true);
		
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');

	break;
	
	/**
	* Tab do formulário (regras cfop)
	* Chamado em: fis001content03
	*/
	case 'fis001tab03':
		// Form (caso seja o mesmo form em multiplas tabs, senão comente as duas linhas)
		$formId = uniqid();
		$g['smarty']->assign('formId', $formId);
	
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/fis/fis001/form/fis001form02/', 'Cadastro', "&tab=geral&acaoId=" . urlencode($_REQUEST['acaoId']) . "&formId=" . urlencode($formId)),
		);
		
		// Processa Tabs
		$g['conteudo']['pagina'] = gProcessaTabs($tabsList, $tabs, true);
		
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');

	break;
	
	/**
	* Tab do formulário (regras cfop)
	* Chamado em: fis001content03
	*/
	case 'fis001tab03Compras':
		// Form (caso seja o mesmo form em multiplas tabs, senão comente as duas linhas)
		$formId = uniqid();
		$g['smarty']->assign('formId', $formId);
	
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/fis/fis001/form/fis001form02Compras/', 'Cadastro', "&tab=geral&acaoId=" . urlencode($_REQUEST['acaoId']) . "&formId=" . urlencode($formId)),
		);
		
		// Processa Tabs
		$g['conteudo']['pagina'] = gProcessaTabs($tabsList, $tabs, true);
		
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');

	break;	
}
?>