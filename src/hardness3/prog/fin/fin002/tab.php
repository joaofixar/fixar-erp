<?php
namespace hardness;
/**
* Ações
*/
switch ($g['r'][3]) {
	
	/**
	* Grid Principal
	* Chamado em: fin002content01
	*/
	case 'fin002tab01':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
	
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/fin/fin002/grid/fin002grid01/', 'Título a Pagar'),
            array(uniqid(), '/fin/fin002/grid/fin002GridReparcelamentos/', 'Reparcelamentos/Agrupamentos'),
            array(uniqid(), '/fin/fin002/content/fin002ImportarCSV/', 'Importar CSV'),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	/**
	* Form Principal + Acrescimos e Descontos, Recebimentos e Agrupamentos (se update)
	* Chamado em: fin001content02
	*/
	case 'fin002tab02':
		/**
		* Cabeçalho (Versão Acima da TAB)
		*/
		if (isset($r_acaoId) && !empty($r_acaoId)) {
			$cabecalhoId = uniqid();
			echo '<div id="'.$cabecalhoId.'" class="contentCabecalho" style="height: 38px;"></div>';
			$actions = array(
				// identificador, url, paramentros get (opcional)
				array($cabecalhoId, '/fin/fin002/outro/fin002CabecalhoFornecedor/', '&acaoId=' . urlencode($r_acaoId), true),
			);
			echo gProcessaAcoes($actions);
		}
	
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/fin/fin002/form/fin002form01/', 'Cadastro de Título', "&tab=cadastro_titulo&acaoId=" . urlencode($_REQUEST['acaoId'])),						
		);
		
		// só cria a segunda tab caso seja update
		if ($_REQUEST['acaoId']) {
			$tab2Id = uniqid();
			array_push($tabsList, array($tab2Id, '/fin/fin002/content/fin002ContentAcrescimosDescontos/', 'Acréscimos/Descontos', "&acaoId=" . urlencode($_REQUEST['acaoId'])));
			$tab3Id = uniqid();
			array_push($tabsList, array($tab3Id, '/fin/fin002/grid/fin002gridReparcelamentosTitulo/', 'Reparcelamentos', "&acaoId=" . urlencode($_REQUEST['acaoId']), true));
			//array_push($tabsList, array($tab3Id, '/fin/fin002/grid/fin002gridAgrupamentos/', 'Agrupamentos', "&acaoId=" . urlencode($_REQUEST['acaoId']), true));
			$tab4Id = uniqid();
			array_push($tabsList, array($tab4Id, '/fin/fin002/grid/fin002grid03/', 'Pagamentos', "&tab=recebimentos&acaoId=" . urlencode($_REQUEST['acaoId'])));
			$tab5Id = uniqid();
			array_push($tabsList, array($tab5Id, '/fin/fin002/grid/fin002GridCaixaBanco/', 'Caixa/Banco', "&acaoId=" . urlencode($_REQUEST['acaoId'])));
			$tab6Id = uniqid();
			array_push($tabsList, array($tab6Id, '/fin/fin002/content/fin002ContentDocumentos/', 'Documentos', "&T015_Id=" . urlencode($_REQUEST['acaoId'])));
		}
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
		case 'fin002tab03':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
	
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/fin/fin002/content/contentBaixa2rows/', 'Titulos a baixar', "&tab=geral&acaoId=" . urlencode($_REQUEST['acaoId']) . "&extra=".urlencode($r_extra)),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	
    break;	
    
    /**
    * Agrupar
    * Chamado em: fin001content04
    */
    case 'fin002TabAgrupar':
        // Form (caso seja o mesmo form em multiplas tabs)
        //$formId = uniqid();
        //$g['smarty']->assign('formId', $formId);
    
        // Definição das Tabs
        $tabs = array();
        $tabsList = array(
            array(uniqid(), '/fin/fin002/content/fin002contentAgrupar/', 'Agrupar de Títulos', "&extra=".urlencode($r_extra)),
        );
        
        // Processa Tabs
        echo gProcessaTabs($tabsList, $tabs);
        
        // Carrega template
        $g['smarty']->assign('tabs', $tabs);
        $g['smarty']->display('tab.tpl');
    break;
    
    case 'fin002tabReparcelamento':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
	
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/fin/fin002/form/fin002formReparcelamento/', 'Detalhes', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
			array(uniqid(), '/fin/fin002/grid/fin002gridTitulosGeradores/', 'Títulos Geradores', "&acaoId=" . urlencode($r_acaoId)),
			array(uniqid(), '/fin/fin002/grid/fin002gridTitulosGerados/', 'Títulos Gerados',  "&acaoId=" . urlencode($r_acaoId)),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'fin002TabProjecoes':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
		    array(uniqid(), '/fin/fin002/grid/fin002GridProjecoes/', 'Projeções', "&tab=agrupar&extra=".urlencode($r_extra)),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
    break;

    case 'fin002TabDividir':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
		    array(uniqid(), '/fin/fin002/grid/fin002GridDividir/', 'Títulos a dividir', "&tab=agrupar&extra=".urlencode($r_extra)),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
    break;

    case 'fin002TabDivisao':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
		    array(uniqid(), '/fin/fin002/grid/fin002GridDivisao/', 'Títulos a gerar', "&tab=agrupar&extra=".urlencode($r_extra)),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
    break;

	case 'fin002tabReparcelamentoJanela':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
	
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/fin/fin002/grid/fin002GridReparcelar/', 'Títulos', "&tab=geral&acaoId=" . urlencode($r_acaoId) . '&extra=' . urlencode($r_extra)),
			//array(uniqid(), '/fin/fin001/content/fin001ContentReparcelamento/', 'Reparcelamento', '&acaoId=' . urlencode($r_acaoId) . '&extra=' . urlencode($r_extra) . '&simular=' . urlencode($r_simular), true, true),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs, true);
		
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
		
	break;

	case 'fin002TabGridPagamento':
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/fin/fin002/grid/fin002GriReparcelamento/', 'Reparcelamento', '&acaoId=' . urlencode($r_acaoId) .  "&divReload=" . urlencode($content1) . '&extra=' . urlencode($r_extra) . '&simular=' . urlencode(@$r_simular)),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs, true);
		
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
    
}
