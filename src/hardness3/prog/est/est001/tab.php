<?php
namespace hardness;

switch ($g['r'][3]) {
	/**
	* @author AndreySliwinski
	* @since ...
	*/
	case 'est001TabDivergenciasEstoque':
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		
		// Definição das Tabs
		
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/est/est001/grid/est001GridDivergenciasEstoque/', 'Divergências de Estoque', '&tab=geral&acaoId=' . urlencode($r_acaoId)),			
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'est001TabDivergenciasEstoqueIncluir':
		if($r_acaoId){
			/**
		* Cabeçalho (Versão Acima da TAB)
		*/
		$cabecalhoId = uniqid();
		echo '<div id="'.$cabecalhoId.'" class="contentCabecalho" style="height: 38px;"></div>';
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($cabecalhoId, '/est/est001/outro/est001CabecalhoDE/', '&acaoId=' . urlencode($r_acaoId), true),
		);
		echo gProcessaAcoes($actions);
		}
	
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		
		// Caso especial: 
		//		Case o campo 'FLAG...' for ... ir abrir somente o grid
		// 		Senão abre um content-2rows (form + grid)
		$sql = "SELECT T029_Flag_Ok FROM T029 WHERE T029_Id= '" . mysql_real_escape_string($r_acaoId). "';";
		$resultado = mysql_query($sql);
		$row = mysql_fetch_array($resultado);
		if ($row['T029_Flag_Ok'] == 'N') {
			$tab2 = array(uniqid(), '/est/est001/content/est001ContentDivergenciasEstoqueIncluirProduto/', 'Produtos', '&acaoId=' . urlencode($r_acaoId). '&T029_Id=' . urlencode($r_T029_Id));			
		} else {
			$tab2 = array(uniqid(), '/est/est001/grid/est001GridDivergenciasEstoqueProdutos/', 'Produtos', '&tab=geral&acaoId=' . urlencode($r_acaoId). '&T029_Id=' . urlencode($r_T029_Id));
		}
	
		if(!empty($r_acaoId)){
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			$tab2,
			array(uniqid(), '/est/est001/form/est001FormDivergenciasEstoque/', 'Cabeçalho', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
			array(uniqid(), '/est/est001/form/est001FormDivergenciasEstoqueImportar/', 'Importar Arq. Coletora', '&tab=geral&T029_Id=' . urlencode($r_acaoId)),
		);
		}else{
			$tabs = array();
			$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/est/est001/form/est001FormDivergenciasEstoque/', 'Divergências Estoque', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
			//array(uniqid(), '/abc/abc001/grid/abc001GridOutroNome/', 'Tab 1', '&acaoId=' . urlencode($r_acaoId)),
		);
		}
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
}

?>