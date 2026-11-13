<?php
namespace hardness;
/**
* Ações
*/
switch ($g['r'][3]) {
	
	/**
	* Grid Principal
	* Chamado em: ven010content01
	*/
	case 'ven010tab01':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
	
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/ven/ven010/grid/ven010grid01/', 'Contratos de Locação', "&acaoId=" . urlencode($r_acaoId)),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	/**
	* Form Principal - Contratos de Locação
	* Chamado em: ven010content02
	*/
	case 'ven010tab02':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		if (!isset($r_readOnly))
			$r_readOnly=false;

		if (!isset($r_mostraGerar))
			$r_mostraGerar=false;		
			
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/ven/ven010/form/ven010form01/', 'Contratos de Locação', "&tab=contratos_locacao&acaoId=" . urlencode($r_acaoId)."&readOnly=".$r_readOnly."&mostraGerar=".$r_mostraGerar),
			array(uniqid(), '/ven/ven010/grid/ven010grid02/', 'Logística de Contrato', "&tab=contratos_locacao&acaoId=" . urlencode($r_acaoId)."&readOnly=".$r_readOnly."&mostraGerar=".$r_mostraGerar),						
			array(uniqid(), '/ven/ven010/grid/ven010grid03/', 'Contas a receber', "&tab=contas_receber&acaoId=" . urlencode($r_acaoId). "&T141_Id=". urlencode($r_acaoId) ."&readOnly=".$r_readOnly."&mostraGerar=".$r_mostraGerar),
			array(uniqid(), '/ven/ven010/content/ven010ContentBens/', 'Bens', "&acaoId=" . urlencode($r_acaoId)."&readOnly=".$r_readOnly),						
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'ven010tab04':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		if (!isset($r_readOnly))
			$r_readOnly=false;		
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/ven/ven010/form/ven010form01/', 'Contratos de Locação', "&tab=contratos_locacao&acaoId=" . urlencode($r_acaoId)),					
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
}
?>