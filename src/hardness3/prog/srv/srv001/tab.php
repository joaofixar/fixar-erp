<?php
namespace hardness;
	switch ($g['r'][3]) {
	
		case 'srv001tab001':
			// Form (caso seja o mesmo form em multiplas tabs)
			//$formId = uniqid();
			//$g['smarty']->assign('formId', $formId);
		
			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
				// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
				array(uniqid(), '/srv/srv001/grid/srv001grid001/', 'Cadastro de Serviços'),
			);
			
			// Processa Tabs
			$g['conteudo']['pagina'] = gProcessaTabs($tabsList, $tabs);
			
			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('tab.tpl');
			
		break;
		
		case 'tabForm1':
			// Form (caso seja o mesmo form em multiplas tabs)
			// $formId = uniqid();
			// $g['smarty']->assign('formId', $formId);
		
			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
				array(uniqid(), '/srv/srv001/form/form1/', 'Serviço', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
				array(uniqid(), '/srv/srv001/content/srv001contentProcessos/', 'Processos', "&tab=geral&D112_Id=" . urlencode($r_acaoId)),
				array(uniqid(), '/srv/srv001/content/srv001ContentMateriais/', 'Materiais', "&tab=geral&D112_Id=" . urlencode($r_acaoId)),
			);
			
			// Processa Tabs
			$g['conteudo']['pagina'] = gProcessaTabs($tabsList, $tabs);
			
			// Carrega template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('tab.tpl');

		break;
		
		case 'tabForm2':
			// Form (caso seja o mesmo form em multiplas tabs)
			// $formId = uniqid();
			// $g['smarty']->assign('formId', $formId);
		
			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
				array(uniqid(), '/srv/srv001/form/form1/', 'Serviço', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
			);
			
			// Processa Tabs
			$g['conteudo']['pagina'] = gProcessaTabs($tabsList, $tabs);
			
			// Carrega template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('tab.tpl');

		break;
		
		
	}
?>