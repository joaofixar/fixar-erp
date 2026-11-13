<?php
namespace hardness;
/**
* Ações
*/
switch ($g['r'][3]) {
	case 'srm001TabPrincipal':
		$D024_Id=(isset($r_D024_Id)) ? $r_D024_Id : false;
		$novocliente = (isset($r_novocliente)) ? $r_novocliente : '';
		$sqlHistorico = "SELECT * FROM T240 WHERE T240_C007_Id = '{$g['usuarioAtual']}' AND T240_Status = 'A' ORDER BY T240_Ordem LIMIT 7;";
		$historico = mysql_query($sqlHistorico);
		$tabs = array();
		$tabsList = array();
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
		$tabsList[] = array(uniqid(), '/srm/srm001/content/srm001ContentPrincipal2rows/', 'SRM', "&tab=geral&content1=" . urlencode($r_content1) . '&D024_Id=' . urlencode($D024_Id) . '&divJanela=' . urlencode($r_divId) . '&novocliente=' . $novocliente, false, false, true);
		
		while($row = mysql_fetch_array($historico)){
			$sqlEmpresa = "SELECT * FROM D024 WHERE D024_Id = '{$row['T240_D024_Id']}' LIMIT 1;";
			$empresa = mysql_query($sqlEmpresa);
			if(mysql_num_rows($empresa) > 0){
				$campoNome = $g['C031']['SRMNomeTab'];
				$nomeEmpresa = mysql_fetch_array($empresa);
				$nomeEmpresa = substr($nomeEmpresa[$campoNome], 0, 12);
				$D024_Id = $row['T240_D024_Id'];
				$tabsList[] = array(uniqid(), '/srm/srm001/content/srm001ContentPrincipalSRM/', $nomeEmpresa, "&tab=geral&content1=" . urlencode($r_content1) . '&D024_Id=' . $D024_Id, false, false, true);
			}
		}
		
		$tabsList[] = array(uniqid(), '', '+', "");
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->assign('g', $g);
		$g['smarty']->display('tabSRM.tpl');
	break;
	
	case 'srm001TabGeral':
		$novocliente = (isset($r_novocliente)) ? $r_novocliente : '';

		// Definição das Tabs
		$tabs = array();
		$tabsList = array();
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
		if(isset($g['c029Ids'][170])){
			$tabsList[] = array(uniqid(), '/srm/srm001/content/srm001ContentCotacoes/', 'Cotação Compra', "&acaoId=" . urlencode($r_acaoId));
		}
		if(isset($g['c029Ids'][171])){
			$tabsList[] = array(uniqid(), '/srm/srm001/content/srm001ContentOrdensCompra/', 'Ordem Compra', "&acaoId=" . urlencode($r_acaoId));
		}
		if(isset($g['c029Ids'][21])){
			$tabsList[] = array(uniqid(), '/srm/srm001/content/srm001ContentNotasRecebidas/', 'Notas Fiscais Recebidas', "&acaoId=" . urlencode($r_acaoId));
		}
		if(isset($g['c029Ids'][22])){
			$tabsList[] = array(uniqid(), '/srm/srm001/content/srm001ContentConhecimentos/', 'Conhecimentos', "&acaoId=" . urlencode($r_acaoId));
		}
		if(isset($g['c029Ids'][94]) || isset($g['c029Ids'][95])){
			$tabsList[] = array(uniqid(), '/srm/srm001/content/srm001ContentComprasEstoque/', 'Compras Estoque/Emergência', "&acaoId=" . urlencode($r_acaoId));
		}
		if(isset($g['c029Ids'][94])){
			$tabsList[] = array(uniqid(), '/srm/srm001/grid/srm001GridFornecedores/', 'Fornecedores', "&acaoId=" . urlencode($r_acaoId));
		}
		if(isset($g['c029Ids'][13])){
			$tabsList[] = array(uniqid(), '/crm/crm001/content/crm001ContentEmail/', 'Email', "&tab=geral&acaoId=" . urlencode($r_acaoId));
		}
		if(isset($g['c029Ids'][139])){
			$tabsList[] = array(uniqid(), '/srm/srm001/grid/srm001GridContasPagar/', 'Pagar', "&tab=geral&acaoId=" . urlencode($r_acaoId));
		}
	
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');

		if (!empty($novocliente)) {
			echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					//console.log('{$novocliente}');
					var x = $('#{$novocliente} > .tabs > .ui-tabs-nav > li').size()-2;
					setTimeout(
					function(){
						$('#{$novocliente} > .tabs').tabs('select', x);
					},50);
				}</script></div>
EOT;
		}
	break;
		
	case 'srm001TabPrincipalCliente':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array();
		if(isset($g['c029Ids'][170])){
			$tabsList[] = array(uniqid(), '/srm/srm001/content/srm001ContentCotacoesFornecedor/', 'Cotação Compra', "&acaoId=" . urlencode($r_acaoId) . '&D024_Id=' . urlencode($r_D024_Id));
		}
		if(isset($g['c029Ids'][171])){
			$tabsList[] = array(uniqid(), '/srm/srm001/content/srm001ContentOrdensComprasFornecedor/', 'Ordem Compra', "&acaoId=" . urlencode($r_acaoId) . '&D024_Id=' . urlencode($r_D024_Id));
		}
		if(isset($g['c029Ids'][21])){
			$tabsList[] = array(uniqid(), '/srm/srm001/content/srm001ContentNotasRecebidasFornecedor/', 'Notas Fiscais Recebidas', "&acaoId=" . urlencode($r_acaoId) . '&D024_Id=' . urlencode($r_D024_Id));
		}
		if(isset($g['c029Ids'][22])){
			$tabsList[] = array(uniqid(), '/srm/srm001/grid/srm001GridConhecimentosFornecedor/', 'Conhecimentos', "&acaoId=" . urlencode($r_acaoId) . '&D024_Id=' . urlencode($r_D024_Id));
		}
		if(isset($g['c029Ids'][139])){
			$tabsList[] = array(uniqid(), '/srm/srm002/grid/srm002GridContasPagarFornecedor/', 'Pagar', "&tab=geral&acaoId=" . urlencode($r_acaoId) . '&D024_Id=' . urlencode($r_D024_Id));
		}
		if(isset($g['c029Ids'][133])){
			$tabsList[] = array(uniqid(), '/srm/srm001/content/srm001ContentEmailFornecedor/', 'E-mail', "&tab=geral&content1=" . urlencode($r_content1) . '&D024_Id=' . urlencode($r_D024_Id));
		}

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'srm001TabComprasEstoque':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array();		
			
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
		if (isset($g['c029Ids'][94])) {
			$tabsList[] = array(uniqid(), '/srm/srm001/content/srm001ContentComprasEstoqueModelos/', 'Compras Estoque', '&acaoId=' . urlencode($r_acaoId), false, false);			
		}
		if (isset($g['c029Ids'][95])) {
			if($g['C031']['desabilitarCotacaoEmergencia'] == 'N'){
				$tabsList[] = array(uniqid(), '/est/est009/grid/est009GridCotacaoEmergencia/', 'Cotação Emergência', '&acaoId=' . urlencode($r_acaoId), false, false);
			}
			if($g['C031']['desabilitarCompraEmergencia'] == 'N'){
				$tabsList[] = array(uniqid(), '/srm/srm001/content/srm001ContentComprasEmergencia/', 'Compras Emergência', '&acaoId=' . urlencode($r_acaoId), false, false);
			}
		}
		if($g['C031']['desabilitarMontagemEmergencia'] == 'N'){
			$tabsList[] = array(uniqid(), '/est/est009/grid/est009GridMontagensEmergencia/', 'Montagens Emergência', "&acaoId=" . urlencode($r_acaoId), false, false);
		}
		if($g['C031']['desabilitarServicoEmergencia'] == 'N'){
			$tabsList[] = array(uniqid(), '/est/est009/grid/est009GridServicosEmergencia/', 'Serviços Emergência', "&acaoId=" . urlencode($r_acaoId), false, false);
		}
		if($g['C031']['desabilitarProducaoEmergencia'] == 'N'){
			$tabsList[] = array(uniqid(), '/est/est017/grid/est017GridProducaoEmergencia/', 'Produção Emergência', "&acaoId=" . urlencode($r_acaoId), false, false);
		}
		// if (isset($g['c029Ids'][95])) {
		// 	if($g['C031']['desabilitarCotacaoEmergencia'] == 'N'){
		// 		$tabsList[] = array(uniqid(), '/est/est019/grid/est019GridCotacaoEmergencia/', 'Cotação Emergência Internacional', '&acaoId=' . urlencode($r_acaoId), false, false);
		// 	}
		// 	if($g['C031']['desabilitarCompraEmergencia'] == 'N'){
		// 		$tabsList[] = array(uniqid(), '/est/est019/content/est019ContentComprasEmergencia/', 'Compras Emergência Internacional', '&acaoId=' . urlencode($r_acaoId), false, false);
		// 	}
		// }
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'srm001TabCotacoes':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/est/est015/grid/est015GridPrincipalOrcamentos/', 'Cotações'),
            array(uniqid(), '/est/est015/grid/est015GridPrincipalProdutos/', 'Produtos'),
			//array(uniqid(), '/est/est015/grid/est015GridPrincipalProdutosVsFornecedor/', 'Prod. x Forn.'),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'srm001TabOrdensCompras':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/est/est016/grid/est016grid01/', 'Ordens'),
        	array(uniqid(), '/est/est016/grid/est016GridProdutosPedido/', 'Produtos'),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'srm001TabNotasRecebidas':
		// Definição das Tabs
		$tabs = array();
		$tab1 = uniqid();
		$tab2 = uniqid();
		$tab3 = uniqid();
		$tab4 = uniqid();
		$tab5 = uniqid();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array($tab1, '/est/est004/grid/est004GridNotasFiscaisRecebidas/', 'Notas Fiscais', "&acaoId=" . urlencode($r_acaoId) .  "&divReload=" . urlencode($tab2)),
			array($tab5, '/est/est004/grid/est004GridNotasFiscaisRecebidasProdutos/', 'Produtos', "&acaoId=" . urlencode($r_acaoId) .  "&divReload=" . urlencode($tab4)),
			array($tab2, '/est/est004/form/est004FormImportarXML/', 'Importar XML', "&tab=geral&acaoId=" . urlencode($r_acaoId) .  "&divReload=" . urlencode($tab1)),
			array($tab3, '/est/est004/grid/est004GridNotasFiscaisReceber/', 'Recebimentos internos', "&acaoId=" . urlencode($r_acaoId) .  "&divReload=" . urlencode($tab3)),
	        array(uniqid(), '/etc/etc006/grid/etc006Xml/', 'Recebidas por E-mail'),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'srm001TabCotacoesFornecedor':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/srm/srm001/grid/srm001GridCotacoesFornecedor/', 'Cotações'),
            array(uniqid(), '/srm/srm001/grid/srm001GridProdutosCotacaoFornecedor/', 'Produtos'),
			//array(uniqid(), '/srm/srm001/grid/srm001GridPrincipalProdutosVsFornecedor/', 'Prod. x Forn.'),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'srm001TabOrdensComprasFornecedor':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/srm/srm001/grid/srm001GridOrdensCompraFornecedor/', 'Ordens'),
        	array(uniqid(), '/srm/srm001/grid/srm001GridProdutosOrdensComprasFornecedor/', 'Produtos'),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'srm001TabNotasRecebidasFornecedor':
		// Definição das Tabs
		$tabs = array();
		$tab1 = uniqid();
		$tab2 = uniqid();
		$tab3 = uniqid();
		$tab4 = uniqid();
		$tab5 = uniqid();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array($tab1, '/srm/srm001/grid/srm001GridNotasRecebidasFornecedor/', 'Notas Recebidas', "&acaoId=" . urlencode($r_acaoId) .  "&divReload=" . urlencode($tab2)),
			array($tab5, '/srm/srm001/grid/srm001GridNotasFiscaisRecebidasProdutosFornecedor/', 'Produtos', "&acaoId=" . urlencode($r_acaoId) .  "&divReload=" . urlencode($tab4)),
			array($tab2, '/srm/srm001/form/srm001FormImportarXML/', 'Importar XML', "&tab=geral&acaoId=" . urlencode($r_acaoId) .  "&divReload=" . urlencode($tab1)),
			array($tab3, '/srm/srm001/grid/srm001GridNotasFiscaisReceber/', 'Recebimentos internos', "&acaoId=" . urlencode($r_acaoId) .  "&divReload=" . urlencode($tab3)),
	        array(uniqid(), '/etc/etc006/grid/etc006XmlFornecedor/', 'Recebidas por E-mail'),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'srm001TabComprasEmergencia':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/est/est009/grid/est009GridComprasEmergencia/', 'Cotar'),
        	array(uniqid(), '/est/est009/grid/est009GridComprasEmergenciaCotando/', 'Cotando'),
        	array(uniqid(), '/est/est009/grid/est009GridComprasEmergenciaComprar/', 'Comprar'),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'srm001TabComprasEstoqueModelos':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/est/est009/grid/est009GridComprasEstoque/', 'M1', '&tab=geral&acaoId=' . urlencode($r_acaoId), false, false),
        	array(uniqid(), '/est/est009/grid/est009GridComprasEstoqueModelo2/', 'M2', '&tab=geral&acaoId=' . urlencode($r_acaoId), false, false),
        	array(uniqid(), '/est/est009/grid/est009GridComprasEstoqueModelo3/', 'M3', '&tab=geral&acaoId=' . urlencode($r_acaoId), false, false),
			array(uniqid(), '/est/est009/grid/est009GridComprasEstoqueEmpresas/', 'M4', '&tab=geral&acaoId=' . urlencode($r_acaoId), false, false),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
}
