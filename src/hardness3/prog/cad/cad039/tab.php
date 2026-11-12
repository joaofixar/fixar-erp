<?php
namespace hardness;

switch ($g['r'][3]) {
	 /**
 * Weiberlan
 * 07/12/2011
 */
	case 'cad039TabCertificadosDeInspecao':
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad039/grid/cad039GridCertificadosDeInspecao/', 'Certificados de Inspeção', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
			//array(uniqid(), '/abc/abc001/grid/abc001GridOutroNome/', 'Tab 1', '&acaoId=' . urlencode($r_acaoId)),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'cad039TabCertificadosDeInspecaoIncluir':
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		
		 // Definição das Tabs
		 $D124_D106_Id = (isset($r_D124_D106_Id)) ? $r_D124_D106_Id : false;
		 if(empty($r_acaoId)){
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad039/form/cad039FormCertificadosDeInspecao/', 'Cadastro de Certificado', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
			//array(uniqid(), '/abc/abc001/grid/abc001GridOutroNome/', 'Tab 1', '&acaoId=' . urlencode($r_acaoId)),
		);
		}else{		
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad039/form/cad039FormCertificadosDeInspecao/', 'Cadastro de Certificado', '&tab=geral&acaoId=' . urlencode($r_acaoId) . '&D124_D106_Id='. urlencode($D124_D106_Id)),
			array(uniqid(), '/cad/cad039/grid/cad039GridEspecificacoes/', 'Especificações', '&tab=geral&D124_Id=' . urlencode($r_D124_Id) . '&D124_D106_Id=' . urlencode($D124_D106_Id) .  '&acaoId=' . urlencode($r_acaoId)),
			array(uniqid(), '/cad/cad039/content/cad039ContentAcessorios/', 'Acessórios', '&acaoId=' . urlencode($r_acaoId)),
			);
		}
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
}

?>