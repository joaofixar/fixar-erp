<?php
namespace hardness;
/**
* Ações
*/
switch ($g['r'][3]) {
	
	/**
	* Grid Principal
	* Chamado em: fin007content01
	*/
	case 'fin007tab01':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
	
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/fin/fin007/grid/fin007grid01/', 'Análise de Crédito'),
			array(uniqid(), '/fin/fin007/grid/fin007grid02/', 'Clientes'),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'fin007tab02':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
	
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/fin/fin007/grid/fin007grid03/', 'Análise de Crédito (Nota)'),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;


  	case 'fin007TabValorLimite':

    	/**
		* Cabeçalho (Versão Acima da TAB)
		*/
		$cabecalhoId = uniqid();
		echo '<div id="'.$cabecalhoId.'" class="contentCabecalho" style="height: 38px;"></div>';
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($cabecalhoId, '/fin/fin007/outro/fin007cabecalhoValorLimite/', '&acaoId=' . urlencode($r_acaoId), true),
		);
		echo gProcessaAcoes($actions);

        $tabs = array();
        $tabsList = array(
        // Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
            array(uniqid(), '/fin/fin007/grid/DemonstrativoValorLimite/', 'Valor Limite', '&tab=geral&acaoId=' . urlencode($r_acaoId), false, true),
        );

        // Processa Tabs
        echo gProcessaTabs($tabsList, $tabs);

        // Carrega Template
        $g['smarty']->assign('tabs', $tabs);
        $g['smarty']->display('tab.tpl');

        if (isset($r_abrirLocacao)) {
	        echo <<<EOT
	        <div class="divHidden"><script> if (typeof jQuery != 'undefined') {
	        	setTimeout(function() {
	        		$('#{$g['divId']} .tabs:visible:last').tabs("select", 1);
	        	}, 50);
	        } </script></div>
EOT;
		}
	break;

	case 'fin007TabSaldoLimite':

    	/**
		* Cabeçalho (Versão Acima da TAB)
		*/
		$cabecalhoId = uniqid();
		echo '<div id="'.$cabecalhoId.'" class="contentCabecalho" style="height: 38px;"></div>';
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($cabecalhoId, '/fin/fin007/outro/fin007cabecalhoSaldoLimite/', '&acaoId=' . urlencode($r_acaoId), true),
		);
		echo gProcessaAcoes($actions);

        $tabs = array();
        $tabsList = array(
        // Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
            array(uniqid(), '/fin/fin007/grid/DemonstrativoSaldoLimite/', 'Saldo Limite', '&tab=geral&acaoId=' . urlencode($r_acaoId), false, true),
        );

        // Processa Tabs
        echo gProcessaTabs($tabsList, $tabs);

        // Carrega Template
        $g['smarty']->assign('tabs', $tabs);
        $g['smarty']->display('tab.tpl');

        if (isset($r_abrirLocacao)) {
	        echo <<<EOT
	        <div class="divHidden"><script> if (typeof jQuery != 'undefined') {
	        	setTimeout(function() {
	        		$('#{$g['divId']} .tabs:visible:last').tabs("select", 1);
	        	}, 50);
	        } </script></div>
EOT;
		}
	break;
}