<?php
namespace hardness;
/**
* Ações
*/
switch ($g['r'][3]) {

	/**
	* Chamado em: cad032Content01
	*/

	case 'cad032TabPrincipal':

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
		array(uniqid(), '/cad/cad032/grid/cad032GridPerfis/', 'Perfis', "&acaoId=" . urlencode($r_acaoId)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
		break;


	case 'cad032TabCadastroPerfil':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array();
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
		$tabsList[] = array(uniqid(), '/cad/cad032/form/cad032FormCadastroPerfil/', 'Perfil', "&tab=geral&acaoId=" . urlencode($r_acaoId));
		if (!empty($r_acaoId)) {
			$tabsList[] = array(uniqid(), '/cad/cad032/grid/cad032GridAcessos/', 'Acessos: Menu', "&C029_Flag_Tipo=M&acaoId=" . urlencode($r_acaoId));
			$tabsList[] = array(uniqid(), '/cad/cad032/grid/cad032GridAcessos/', 'Acessos: Ações', "&C029_Flag_Tipo=A&acaoId=" . urlencode($r_acaoId));
		}
		if (!empty($r_acaoId)) {
			//$tabsList[] = array(uniqid(), '/cad/cad032/grid/cad032GridResumosRelatorios/', 'Resumos/Relatórios', "&tab=geral&acaoId=" . urlencode($r_acaoId));
		}

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'cad032TabCopiaPerfil':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array();
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
		$tabsList[] = array(uniqid(), '/cad/cad032/outro/cad032OutroCopiaPerfil/', 'Perfils', "&tab=geral&acaoId=" . urlencode($r_acaoId));

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'cad032TabPerfisItem':

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
		array(uniqid(), '/cad/cad032/grid/cad032GridPerfisItem/', 'Perfis', "&acaoId=" . urlencode($r_acaoId)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
}
