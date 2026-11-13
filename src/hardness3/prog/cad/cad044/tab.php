<?php
namespace hardness;

switch ($g['r'][3]) {
	/**
	* @author Weiberlan
	* @since 30/11/11
	*/
	case 'cad044TabTextosDeDocumentos':
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad044/grid/cad044GridTextosDeDocumentosGeral/', 'Geral', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
			array(uniqid(), '/cad/cad044/grid/cad044GridTextosDeDocumentosReciboLocacao/', 'Recibo de Locação', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
			array(uniqid(), '/cad/cad044/grid/cad044GridTextosDeDocumentosCertificadoInspecao/', 'Certificado de Inspeção', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
			array(uniqid(), '/cad/cad044/grid/cad044GridTextosDeDocumentosCertificadoConformidade/', 'Certificado de Conformidade', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
			//array(uniqid(), '/abc/abc001/grid/abc001GridOutroNome/', 'Tab 1', '&acaoId=' . urlencode($r_acaoId)),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'cad044TabCadastroTextosDeDocumentos':
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad044/form/cad044CadastroTextosDeDocumentos/', 'Textos De Documentos', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
			//array(uniqid(), '/abc/abc001/grid/abc001GridOutroNome/', 'Tab 1', '&acaoId=' . urlencode($r_acaoId)),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
}

?>