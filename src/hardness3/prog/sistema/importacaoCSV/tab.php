<?php
namespace hardness;
/**
* Ações
*/
switch ($g['r'][3]) {

	 case 'sistemaPImpCSVTabelas':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array();
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
		$tabsList[] = array(uniqid(), '/sistema/importacaoCSV/grid/sistemaPImpCSVTabelas/', 'Tabelas', '&acaoId=' . urlencode($r_acaoId) . '&divReload=' . urlencode($r_divReload));

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	 break;
	 
	 case 'sistemaPImpCSVRecuperar':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array();
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
		$tabsList[] = array(uniqid(), '/sistema/importacaoCSV/grid/sistemaPImpCSVRecuperar/', 'Configurações Salvas', '&acaoId=' . urlencode($r_acaoId));

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	 break;
	 
	 case 'sistemaPImpCSVAcoes':
	 	// retira o * do nome da tabela
	 	$r_acaoId = trim($r_acaoId, ' *');
	 	
		// Definição das Tabs
		$tabs = array();
		$tabsList = array();
		$tabsList[] = array(uniqid(), '/sistema/importacaoCSV/grid/sistemaPImpCSVAcoes/', 'Importação CSV', '&tab=geral&acaoId=' . urlencode($r_acaoId) . '&divReload=' . urlencode($r_divReload));

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
		
		/*if ($r_acaoId == 'GLOBAL') {
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} li:eq(2)').css('margin-left', '20px');
			} </script></div>
EOT;
		}*/
	 break;

}
