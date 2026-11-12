<?php
namespace hardness;

switch ($g['r'][3]) {
	/**
	* @author Weiberlan Garcia
	* @since 02/03/2012
	*/
	case 'est011TabPrincipal':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/est/est011/grid/est011GridPrincipalProdutos/', 'Controle','',false,true),
			array(uniqid(), '/ven/ven003/grid/ven003GridDevolucao/', 'Gerar nota devolução (Origem emitidas)', "&acaoId=" . urlencode($r_acaoId)),
			array(uniqid(), '/ven/ven003/grid/ven003GridDevolucaoRecebidas/', 'Gerar nota de devolução (Origem recebidas)', "&acaoId=" . urlencode($r_acaoId)),
			//array(uniqid(), '/est/est011/grid/est011GridPrincipalRemessas/', 'Remessas','',false,true),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
		
	break;
	
	case 'est011TabIncluirProduto':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/est/est011/form/est011FormIncluirProduto/', 'Produto', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
		);

		if($r_acaoId>0){
			$tabsList[] = array(uniqid(), '/etc/etc017/content/etc017ContentAtividadesT182/', 'Atividades', '&T182_Id=' . urlencode($r_acaoId));
			$tabsList[] = array(uniqid(), '/est/est011/content/est011ContentDocumentos/', 'Documentos', "&tab=geral&T182_Id=" . urlencode($r_acaoId));
		}

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
		
	break;	
	
	case 'est011TabGerarRemessa':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/est/est011/form/est011FormGerarRemessa/', 'Gerar', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
		
	break;		
	
	case 'est011TabEditarRemessa':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/est/est011/form/est011FormCabecalhoRemessa/', 'Cabeçalho', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
			array(uniqid(), '/est/est011/grid/est011gridItensRemessa/', 'Produtos','&acaoId=' . urlencode($r_acaoId)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
		
	break;	
}

?>
