<?php
namespace hardness;

	/**
	* Ações
	*/
	switch ($g['r'][3]) {

		/**
		* Chamado em: est004Content01
		*/

		case 'est005TabPrincipal':
			global $g;
			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
				array(uniqid(), '/est/est005/grid/est005GridConhecimentos/', 'Conhecimentos', "&acaoId=" . urlencode($r_acaoId)),
				array(uniqid(), '/est/est005/form/est005FormImportarXML/', 'Importar XML', "&tab=geral&acaoId=" . urlencode($r_acaoId)),			
  			);

			  if(isset($g['c029Ids'][257])){
				$tabsList[] = array(uniqid(), '/est/est020/content/est020ContentPrincipal/', 'Transmissão', "&tab=geral&acaoId=" . urlencode($r_acaoId));
			}

			// Processa Tabs
			echo gProcessaTabs($tabsList, $tabs);

			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('tab.tpl');
			break;

		case 'est005TabCadastroConhecimento':

			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
				array(uniqid(), '/est/est005/form/est005FormCadastroConhecimento/', 'Conhecimento', "&tab=geral&acaoId=" . urlencode($_REQUEST['acaoId'])),

			);

			// Processa Tabs
			echo gProcessaTabs($tabsList, $tabs);

			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('tab.tpl');
		break;
		
		case 'est005TabEditarConhecimento':

			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
				array(uniqid(), '/est/est005/form/est005FormCadastroConhecimento/', 'Conhecimento', "&tab=geral&acaoId=" . urlencode($_REQUEST['acaoId'])),
				array(uniqid(), '/est/est005/content/est005FormCadastroNotasRelacionadas/', 'Notas Relacionadas', "&tab=geral&acaoId=" . urlencode($_REQUEST['acaoId'])),
				array(uniqid(), '/est/est005/grid/est005gridCadastroContasPagar/', 'Contas a Pagar', "&tab=geral&acaoId=" . urlencode($_REQUEST['acaoId'])),
			);

			// Processa Tabs
			echo gProcessaTabs($tabsList, $tabs);

			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('tab.tpl');
		break;
	}

?>
