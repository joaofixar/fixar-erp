<?php
namespace hardness;
  /**
 * Weiberlan
 * 07/12/2011
 */
switch ($g['r'][3]) {
	
	case 'cad040TabCertificadoConformidade':
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad040/grid/cad040CertificadosConformidade/', 'Certificado Conformidade', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
			//array(uniqid(), '/abc/abc001/grid/abc001GridOutroNome/', 'Tab 1', '&acaoId=' . urlencode($r_acaoId)),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'cad040TabCadastroCertificadoConformidade':
		$D106_Id = (isset($r_D106_Id)) ? $r_D106_Id : false;
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		
		// Definição das Tabs
		$tabs = array();
		if(empty($r_acaoId)){
		$tabsList = array(
			array(uniqid(), '/cad/cad040/form/cad040FormCertificadoConformidade/', 'Certificado de Conformidade', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
		);
		}else{
		$tabsList = array(
			array(uniqid(), '/cad/cad040/form/cad040FormCertificadoConformidade/', 'Certificado de Conformidade', '&tab=geral&acaoId=' . urlencode($r_acaoId). '&D106_Id='. urlencode($D106_Id)),
			array(uniqid(), '/cad/cad040/grid/cad040GridChecklist/', 'CheckList', '&tab=geral&acaoId=' . urlencode($r_acaoId). '&D106_Id='. urlencode($D106_Id)),
			array(uniqid(), '/cad/cad040/content/cad040ContentAcessorios/', 'Acessórios', '&acaoId=' . urlencode($r_acaoId))
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