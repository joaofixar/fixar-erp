<?php
namespace hardness;
	switch ($g['r'][3]) {
		/**
		* @author tiagofischer
		* @since 08/11/2011
		*/
		case 'srv003TabOperacao':
			$cabecalhoId = uniqid();
			echo '<div id="'.$cabecalhoId.'" class="contentCabecalho" style="height: 40px;"></div>';
			$actions = array(
				// identificador, url, paramentros get (opcional)
				array($cabecalhoId, '/srv/srv003/outros/srv003CabecalhoOperacao/', '&acaoId=' . urlencode($r_acaoId), true)
			);
			echo gProcessaAcoes($actions);
			
			// Form (caso seja o mesmo form em multiplas tabs)
			//$formId = uniqid();
			//$g['smarty']->assign('formId', $formId);
		
			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
				// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
				array(uniqid(), '/srv/srv003/grid/srv003GridOperacao/', 'Meus Serviços', '&acaoId=' . urlencode($r_acaoId). '&T147_Id=' . urlencode($r_T147_Id)),
			);
			
			// Processa Tabs
			$g['conteudo']['pagina'] = gProcessaTabs($tabsList, $tabs);
			
			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('big.tab.tpl');
		break;
		
		case 'srv003TabOperacaoPendentes':
			$cabecalhoId = uniqid();
			echo '<div id="'.$cabecalhoId.'" class="contentCabecalho" style="height: 40px;"></div>';
			$actions = array(
				// identificador, url, paramentros get (opcional)
				array($cabecalhoId, '/srv/srv003/outros/srv003CabecalhoOperacao/', '&acaoId=' . urlencode($r_acaoId), true),
			);
			echo gProcessaAcoes($actions);
			
			// Form (caso seja o mesmo form em multiplas tabs)
			//$formId = uniqid();
			//$g['smarty']->assign('formId', $formId);
		
			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
				// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
				array(uniqid(), '/srv/srv003/grid/srv003GridOperacaoPendentes/', 'Fila de Serviços', '&acaoId=' . urlencode($r_acaoId)),
			);
			
			// Processa Tabs
			$g['conteudo']['pagina'] = gProcessaTabs($tabsList, $tabs);
			
			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('big.tab.tpl');
		break;
		
		case 'srv003TabProcessosServicos':
			// Form (caso seja o mesmo form em multiplas tabs)
			//$formId = uniqid();
			//$g['smarty']->assign('formId', $formId);
		
			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
				// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
				array(uniqid(), '/srv/srv003/grid/srv003GridProcessosServicos/', 'Processos', "&tab=geral&T147_Id=" . urlencode($r_T147_Id)),
			);
			
			// Processa Tabs
			$g['conteudo']['pagina'] = gProcessaTabs($tabsList, $tabs);
			
			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('big.tab.tpl');
		break;
		
		case 'srv003TabMateriaisServicos':
			// Form (caso seja o mesmo form em multiplas tabs)
			//$formId = uniqid();
			//$g['smarty']->assign('formId', $formId);
		
			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
				// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
				array(uniqid(), '/srv/srv003/grid/srv003GridMateriaisServicos/', 'Materiais', "&tab=geral&T147_Id=" . urlencode($r_T147_Id)),
			);
			
			// Processa Tabs
			$g['conteudo']['pagina'] = gProcessaTabs($tabsList, $tabs);
			
			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('big.tab.tpl');
		break;
		
		case 'srv003TabLogin':
			$cabecalhoId = uniqid();
			echo '<div id="'.$cabecalhoId.'" class="contentCabecalho" style="height: 40px;"></div>';
			$actions = array(
				// identificador, url, paramentros get (opcional)
				array($cabecalhoId, '/srv/srv003/outros/srv003CabecalhoLogin/', '&acaoId=' . urlencode($r_acaoId), true),
			);
			echo gProcessaAcoes($actions);
			
			// Form (caso seja o mesmo form em multiplas tabs)
			//$formId = uniqid();
			//$g['smarty']->assign('formId', $formId);
		
			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
				// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
				array(uniqid(), '/srv/srv003/outros/srv003OutrosLogin/', 'Senha', '&acaoId=' . urlencode($r_acaoId)),
			);
			
			// Processa Tabs
			$g['conteudo']['pagina'] = gProcessaTabs($tabsList, $tabs);
			
			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('big.tab.tpl');
		break;
		
		case 'srv003TabIniciarServico':
			$cabecalhoId = uniqid();
			echo '<div id="'.$cabecalhoId.'" class="contentCabecalho" style="height: 40px;"></div>';
			$actions = array(
				// identificador, url, paramentros get (opcional)
				array($cabecalhoId, '/srv/srv003/outros/srv003CabecalhoOperacao/', '&acaoId=' . urlencode($r_acaoId), true),
			);
			echo gProcessaAcoes($actions);
			
			// Form (caso seja o mesmo form em multiplas tabs)
			//$formId = uniqid();
			//$g['smarty']->assign('formId', $formId);
		
			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
				// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
				array(uniqid(), '/srv/srv003/outros/srv003OutrosServico/', 'Detalhes do Serviço', '&acaoId=' . urlencode($r_acaoId) . '&T147_Id=' . urlencode($r_T147_Id)),
			);
			
			// Processa Tabs
			$g['conteudo']['pagina'] = gProcessaTabs($tabsList, $tabs);
			
			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('big.tab.tpl');
		break;
		
		case 'srv003TabExecucaoServico':
			$cabecalhoId = uniqid();
			echo '<div id="'.$cabecalhoId.'" class="contentCabecalho" style="height: 40px;"></div>';
			$actions = array(
				// identificador, url, paramentros get (opcional)
				array($cabecalhoId, '/srv/srv003/outros/srv003CabecalhoOperacao/', '&acaoId=' . urlencode($r_acaoId), true),
			);
			echo gProcessaAcoes($actions);
			
			// Form (caso seja o mesmo form em multiplas tabs)
			//$formId = uniqid();
			//$g['smarty']->assign('formId', $formId);
		
			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
				// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
				array(uniqid(), '/srv/srv003/outros/srv003OutrosServicoExecucao/', 'Serviço em Execução', '&acaoId=' . urlencode($r_acaoId) . '&T147_Id=' . urlencode($r_T147_Id)),
			);
			
			// Processa Tabs
			$g['conteudo']['pagina'] = gProcessaTabs($tabsList, $tabs);
			
			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('big.tab.tpl');
		break;
		
		case 'srv003TabOperacaoAprovar':
			$cabecalhoId = uniqid();
			echo '<div id="'.$cabecalhoId.'" class="contentCabecalho" style="height: 40px;"></div>';
			$actions = array(
				// identificador, url, paramentros get (opcional)
				array($cabecalhoId, '/srv/srv003/outros/srv003CabecalhoOperacaoAprovar/', '&acaoId=' . urlencode($r_acaoId), true),
			);
			echo gProcessaAcoes($actions);
			
			// Form (caso seja o mesmo form em multiplas tabs)
			//$formId = uniqid();
			//$g['smarty']->assign('formId', $formId);
		
			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
				// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
				array(uniqid(), '/srv/srv003/grid/srv003GridOperacaoAprovar/', 'Aprovar', '&acaoId=' . urlencode($r_acaoId)),
			);
			
			// Processa Tabs
			$g['conteudo']['pagina'] = gProcessaTabs($tabsList, $tabs);
			
			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('big.tab.tpl');
		break;
		
		case 'srv003TabAprovarServico':
			$cabecalhoId = uniqid();
			echo '<div id="'.$cabecalhoId.'" class="contentCabecalho" style="height: 40px;"></div>';
			$actions = array(
				// identificador, url, paramentros get (opcional)
				array($cabecalhoId, '/srv/srv003/outros/srv003CabecalhoOperacao/', '&acaoId=' . urlencode($r_acaoId), true),
			);
			echo gProcessaAcoes($actions);
			
			// Form (caso seja o mesmo form em multiplas tabs)
			//$formId = uniqid();
			//$g['smarty']->assign('formId', $formId);
		
			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
				// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
				array(uniqid(), '/srv/srv003/outros/srv003OutrosServicoAprovar/', 'Detalhes do Serviço', '&acaoId=' . urlencode($r_acaoId) . '&T147_Id=' . urlencode($r_T147_Id)),
			);
			
			// Processa Tabs
			$g['conteudo']['pagina'] = gProcessaTabs($tabsList, $tabs);
			
			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('big.tab.tpl');
		break;
	}
?>
