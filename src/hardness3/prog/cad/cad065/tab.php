<?php 

namespace hardness;

switch ($g['r'][3]) {
	/**
	* @author Ricardo Norio Miyata
	*/
	case 'cad065TabLacres':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad065/grid/cad065GridLacres/', 'Lacres'),
			array(uniqid(), '/cad/cad065/grid/cad065GridLacresInativos/', 'Inativos'),
		);

		// Processa Tabs
		$g['conteudo']['pagina'] = gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;	

	case 'cad065TabCadastroLacres':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad065/form/cad065FormLacres/', 'Lacres', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
		);

		// Processa Tabs
		$g['conteudo']['pagina'] = gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
}

?>