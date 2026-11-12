<?php
namespace hardness;
/**
 * Ações
 */
switch ($g['r'][3]) {


	case 'cad068TabNotaFiscal':

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
		    array(uniqid(), '/cad/cad068/grid/cad068GridParticipanteXML/', 'Participantes autorizados no XML'),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');

	break;

	case 'cad068TabIncluirParticipante':

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad068/form/cad068FormIncluirParticipante/', 'Cadastro', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');

	break;

}
?>