<?php
namespace hardness;

switch ($g['r'][3]) {
	case 'cad023TabConfiguracaoProdutos':
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad023/grid/cad023GridTipos/', 'Departamentos', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
			array(uniqid(), '/cad/cad027/grid/cad027GridGrupos/', 'Setores', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
			array(uniqid(), '/cad/cad026/grid/cad026GridLinhas/', 'Grupos', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
			array(uniqid(), '/cad/cad028/grid/cad028GridSubGrupos/', 'Sub-Grupos', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
			array(uniqid(), '/cad/cad029/grid/cad029GridAplicacoes/', 'Aplicações', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
			array(uniqid(), '/cad/cad024/grid/cad024GridUnidades/', 'Unidades', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
			array(uniqid(), '/cad/cad019/content/cad019ContentLocacoes/', 'Locações', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
			array(uniqid(), '/cad/cad025/grid/cad025GridMarcas/', 'Marcas', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
			array(uniqid(), '/cad/cad023/content/cad023ContentPrincipal2rows/','Importar CSV','&tab=geral&acaoId='.urlencode($r_acaoId)),
			array(uniqid(), '/cad/cad023/grid/cad023GridOutrosEstoques/','Outros Estoques','&tab=geral&acaoId='.urlencode($r_acaoId)),
			array(uniqid(), '/cad/cad023/grid/cad023GridEmbalagens/','Embalagens','&tab=geral&acaoId='.urlencode($r_acaoId)),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'cad023TabsFormCSV':
		$tabs = array();
		$tabsList = array();
		$tabsList[] = array(uniqid(), '/cad/cad023/content/cad023ImportarCSVDepartamentos/', 'Importar Departamentos');
		$tabsList[] = array(uniqid(), '/cad/cad023/content/cad023ImportarCSVSetores/', 'Importar Setores', "&acaoId=" . urlencode($r_acaoId));
		$tabsList[] = array(uniqid(), '/cad/cad023/content/cad023ImportarCSVGrupos/', 'Importar Grupos', "&acaoId=" . urlencode($r_acaoId));
		$tabsList[] = array(uniqid(), '/cad/cad023/content/cad023ImportarCSVSubGrupo/', 'Importar Sub-Grupos', "&acaoId=" . urlencode($r_acaoId));
		$tabsList[] = array(uniqid(), '/cad/cad023/content/cad023ImportarCSVMarca/', 'Importar Marcas', "&acaoId=" . urlencode($r_acaoId));
		$tabsList[] = array(uniqid(), '/cad/cad023/content/cad023ImportarCSVLocacao/', 'Importar Locações', "&acaoId=" . urlencode($r_acaoId));

		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'cad023TabTipos':
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad023/grid/cad023GridTipos/', 'Departamentos', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
			//array(uniqid(), '/abc/abc001/grid/abc001GridOutroNome/', 'Tab 1', '&acaoId=' . urlencode($r_acaoId)),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'cad023TabCadastroTipos':
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad023/form/cad023CadastroTipos/', 'Departamento', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
			//array(uniqid(), '/abc/abc001/grid/abc001GridOutroNome/', 'Tab 1', '&acaoId=' . urlencode($r_acaoId)),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'cad023TabTiposAgregados':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad023/grid/cad023GridTiposAgregados/', 'Tipos - Agregados', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'cad023TabTiposServicos':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad023/grid/cad023GridTiposServicos/', 'Tipos - Serviços', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'cad023TabCadastroOutrosEstoques':
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad023/form/cad023FormCadastroOutrosEstoques/', 'Outros Estoques', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'cad023TabCadastroEmbalagens':
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad023/form/cad023FormCadastroEmbalagens/', 'Embalagens', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
}

?>
