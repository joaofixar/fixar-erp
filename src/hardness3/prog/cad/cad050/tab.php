<?php
namespace hardness;
	/**
	* Ações
	*/
	switch ($g['r'][3]) {

		/**
		* Chamado em: cad049Content01
		*/

		case 'cad050TabCadastroLocacaoLocal':

			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad050/form/cad050FormLocacaoLocal/', 'Local Locação', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
			);

			// Processa Tabs
			echo gProcessaTabs($tabsList, $tabs);

			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('tab.tpl');
			break;


		// case 'cad049TabCadastroCor':
// 
			// // Definição das Tabs
			// $tabs = array();
			// $tabsList = array(
			// // Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			// array(uniqid(), '/cad/cad049/form/cad049FormCadastroCor/', 'Cor Veículo', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
			// );
// 
			// // Processa Tabs
			// echo gProcessaTabs($tabsList, $tabs);
// 
			// // Carrega Template
			// $g['smarty']->assign('tabs', $tabs);
			// $g['smarty']->display('tab.tpl');
			// break;		
	}

?>
