<?php
namespace hardness;
/**
* Ações
*/
switch ($g['r'][3]) {

	case 'crm001TabPrincipal':
		$D024_Id=(isset($r_D024_Id)) ? $r_D024_Id : false;
		$novocliente = (isset($r_novocliente)) ? $r_novocliente : '';
		$sqlHistorico = "SELECT * FROM T194 WHERE T194_C007_Id = '{$g['usuarioAtual']}' AND T194_Status = 'A' LIMIT 7;";
		$historico = mysql_query($sqlHistorico);
		$tabs = array();
		$tabsList = array();
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
		$tabsList[] = array(uniqid(), '/crm/crm001/content/crm001ContentPrincipal2rows/', 'CRM', "&tab=geral&content1=" . urlencode($r_content1) . '&D024_Id=' . urlencode($D024_Id) . '&divJanela=' . urlencode($r_divId) . '&novocliente=' . $novocliente);

		while($row = mysql_fetch_array($historico)){
			$sqlEmpresa = "SELECT * FROM D024 WHERE D024_Id = '{$row['T194_D024_Id']}' LIMIT 1;";
			$empresa = mysql_query($sqlEmpresa);
			if(mysql_num_rows($empresa) > 0){
				$campoNome = $g['C031']['CRMNomeTab'];
				$nomeEmpresa = mysql_fetch_array($empresa);
				$nomeEmpresa = substr($nomeEmpresa[$campoNome], 0, 12);
				$D024_Id = $row['T194_D024_Id'];
				$tabsList[] = array(uniqid(), '/crm/crm001/content/crm001ContentPrincipalCRM/', $nomeEmpresa, "&tab=geral&content1=" . urlencode($r_content1) . '&D024_Id=' . $D024_Id);
			}
		}

		$tabsList[] = array(uniqid(), '', '+', "");
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->assign('g', $g);
		$g['smarty']->display('tabCRM.tpl');
	break;

	case 'crm001TabGeral':
		$novocliente = (isset($r_novocliente)) ? $r_novocliente : '';

		// Definição das Tabs
		$tabs = array();
		$tabsList = array();
		if(isset($g['c029Ids'][207])){
			$tabsList[] = array(uniqid(), '/etc/etc017/content/etc017ContentTarefas/', 'Atividades', "&acaoId=" . urlencode($r_acaoId));
		}
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
		if(isset($g['c029Ids'][127])){
			//$tabsList[] = array(uniqid(), '/crm/crm005/content/crm005ContentPrePedido/', 'Pré Pedido', "&acaoId=" . urlencode($r_acaoId));
		}
		if(isset($g['c029Ids'][130])){
			//$tabsList[] = array(uniqid(), '/crm/crm001/grid/crm001GridPrincipalOrcamentos/', 'Orçamentos', "&tab=geral&acaoId=" . urlencode($r_acaoId));
			$tabsList[] = array(uniqid(), '/crm/crm001/content/crm001ContentOrcamentos/', 'Orçamentos', "&acaoId=" . urlencode($r_acaoId));
		}
		if(isset($g['c029Ids'][131])){
			//$tabsList[] = array(uniqid(), '/crm/crm001/grid/crm001GridPrincipalPedidos/', 'Pedidos', "&tab=geral&acaoId=" . urlencode($r_acaoId));
			$tabsList[] = array(uniqid(), '/crm/crm001/content/crm001ContentPedidos/', 'Pedidos', "&acaoId=" . urlencode($r_acaoId));
		}
		if(isset($g['c029Ids'][128])){
			$tabsList[] = array(uniqid(), '/crm/crm001/grid/crm001GridCatalogo/', 'Catálogo', "&tab=geral&acaoId=" . urlencode($r_acaoId));
		}
		//} else if(isset($g['c029Ids'][151])){
		//	$tabsList[] = array(uniqid(), '/ven/ven014/grid/ven014GridPrincipal/', 'Catálogo', "&tab=geral&acaoId=" . urlencode($r_acaoId));
		//}
		if(isset($g['c029Ids'][132])){
			//$tabsList[] = array(uniqid(), '/crm/crm001/grid/crm001GridPrincipalNotasFiscais/', 'Notas Fiscais', "&tab=geral&acaoId=" . urlencode($r_acaoId));
			$tabsList[] = array(uniqid(), '/crm/crm001/content/crm001ContentPrincipalNotasFiscais/', 'Notas Fiscais', "&tab=geral&acaoId=" . urlencode($r_acaoId));
		} else if(isset($g['c029Ids'][200])){
			$tabsList[] = array(uniqid(), '/ven/ven003/content/ven003ContentPrincipal/', 'Notas Fiscais', "&acaoId=" . urlencode($r_acaoId));
		}
		//if(isset($g['c029Ids'][147])){
		//	$tabsList[] = array(uniqid(), '/crm/crm001/grid/crm001gridPrincipalProdutos/', 'Produtos vendidos', "&tab=geral&acaoId=" . urlencode($r_acaoId));
		//}
		if(isset($g['c029Ids'][121])){
			$tabsList[] = array(uniqid(), '/crm/crm001/content/crm001ContentPrincipalClientes/', 'Clientes', "&tab=geral&acaoId=" . urlencode($r_acaoId));
		}
		/*if(isset($g['c029Ids'][120])){
			$tabsList[] = array(uniqid(), '/crm/crm001/grid/crm001GridResumo/', 'Resumo', "&tab=geral&acaoId=" . urlencode($r_acaoId));
		}*/
		if(isset($g['c029Ids'][122])){
			//$tabsList[] = array(uniqid(), '/crm/crm001/grid/crm001GridABCClientes/', 'ABC Clientes', "&tab=geral&acaoId=" . urlencode($r_acaoId));
		}
		if(isset($g['c029Ids'][123])){
			//$tabsList[] = array(uniqid(), '/crm/crm001/grid/crm001GridABCProdutos/', 'ABC Produtos', "&tab=geral&acaoId=" . urlencode($r_acaoId));
		}
		if(isset($g['c029Ids'][124])){
			//$tabsList[] = array(uniqid(), '/crm/crm001/grid/crm001GridFiscal/', 'Fiscal', "&tab=geral&acaoId=" . urlencode($r_acaoId));
		}
		if(isset($g['c029Ids'][125])){
			//$tabsList[] = array(uniqid(), '/crm/crm001/grid/crm001GridResumoGerencial/', 'Resumo CFOP', "&tab=geral&acaoId=" . urlencode($r_acaoId));
		}
		if(isset($g['c029Ids'][126])){
			//$tabsList[] = array(uniqid(), '/crm/crm001/grid/crm001GridResumoFinanceiro/', 'Resumo Financeiro', "&tab=geral&acaoId=" . urlencode($r_acaoId));
		}
		if(isset($g['c029Ids'][143]) && !isset($g['c029Ids'][207])){
			$tabsList[] = array(uniqid(), '/crm/crm001/grid/crm001GridAgendaGeral/', 'Agenda', "&tab=geral&acaoId=" . urlencode($r_acaoId));
		}
		if(isset($g['c029Ids'][13]) && !isset($g['c029Ids'][207])){
			$tabsList[] = array(uniqid(), '/crm/crm001/content/crm001ContentEmail/', 'Email', "&tab=geral&acaoId=" . urlencode($r_acaoId));
		}
		if(isset($g['c029Ids'][148])){
			//$tabsList[] = array(uniqid(), '/etc/etc014/content/etc014ContentPivot/', 'Relatórios', "&acaoId=" . urlencode($r_acaoId) . '&divReload=' . urlencode(null));
		}
		if($g['C031']['modeloExpedicao'] == '2'){
			$tabsList[] = array(uniqid(), '/crm/crm001/grid/crm001GridFaturarComercial/', 'Faturar comercial', "&tab=geral&acaoId=" . urlencode($r_acaoId));
			$tabsList[] = array(uniqid(), '/crm/crm001/grid/crm001GridFaturarBalcao/', 'Faturar balcão', "&tab=geral&acaoId=" . urlencode($r_acaoId));			
		}
		if(isset($g['c029Ids'][138])){
			$tabsList[] = array(uniqid(), '/crm/crm003/grid/crm003GridReceber/', 'Receber', "&tab=geral&acaoId=" . urlencode($r_acaoId));
		}

		$tabsList[] = array(uniqid(), '/crm/crm001/content/crm001Resumos/', 'Resumo', "&acaoId=" . urlencode($r_acaoId));
		//Para exibir as abas de ordem de produção e ordem de compra, verificamos se o usuário tem acesso a pedidos
		if(isset($g['c029Ids'][131])){
			$tabsList[] = array(uniqid(), '/crm/crm001/content/crm001ContentPrincipalOrdens/', 'Compras/Produção', "&tab=geral&acaoId=" . urlencode($r_acaoId));
		}

		//Para exibir os preços de venda por produto de cada cliente
		if(isset($g['c029Ids'][130]) || isset($g['c029Ids'][131]) || isset($g['c029Ids'][200])){
			$tabsList[] = array(uniqid(), '/crm/crm001/grid/crm001GridTabelaPrecosClientes/', 'Tabela Preço', "&tab=geral&acaoId=" . urlencode($r_acaoId));
		}

		//Para exibir os preços de venda por produto de cada cliente
		if (isset($g['c029Ids'][250]) || isset($g['c029Ids'][251])) {
			$tabsList[] = array(uniqid(), '/crm/crm001/content/crm001ContentPrincipalB2B/', 'B2B', "&tab=geral&acaoId=" . urlencode($r_acaoId));
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
		
	case 'crm001TabPrincipalCliente':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array();
		if(isset($g['c029Ids'][207])){
			$tabsList[] = array(uniqid(), '/etc/etc017/content/etc017ContentTarefasCliente/', 'Tarefas', "&acaoId=" . urlencode($r_acaoId) . '&D024_Id=' . urlencode($r_D024_Id));
		}
		if(isset($g['c029Ids'][127])){
			//$tabsList[] = array(uniqid(), '/crm/crm005/content/crm005ContentProduto/', 'Pré Pedido', "&tab=geral&content1=" . urlencode($r_content1) . '&D024_Id=' . urlencode($r_D024_Id));    			
		}
		if(isset($g['c029Ids'][130])){
			$tabsList[] = array(uniqid(), '/crm/crm002/grid/crm002GridPrincipalOrcamentosCliente/', 'Orçamentos', "&tab=geral&content1=" . urlencode($r_content1) . '&D024_Id=' . urlencode($r_D024_Id));
		}
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
		if(isset($g['c029Ids'][131])){
			$tabsList[] = array(uniqid(), '/crm/crm002/grid/crm002GridPrincipalPedidosCliente/', 'Pedidos', "&tab=geral&content1=" . urlencode($r_content1) . '&D024_Id=' . urlencode($r_D024_Id));
		}
		if(isset($g['c029Ids'][128])){
			//$tabsList[] = array(uniqid(), '/crm/crm001/grid/crm001GridCatalogoCliente/', 'Catálogo', "&tab=geral&acaoId=" . urlencode($r_acaoId) . '&D024_Id=' . urlencode($r_D024_Id));
		}
		if(isset($g['c029Ids'][132])){
			$tabsList[] = array(uniqid(), '/crm/crm002/grid/crm002GridNotasCliente/', 'Notas Fiscais', "&tab=geral&content1=" . urlencode($r_content1) . '&D024_Id=' . urlencode($r_D024_Id));
		}
//			if(isset($g['c029Ids'][146])){
				$tabsList[] = array(uniqid(), '/crm/crm002/grid/crm002gridProdutosCliente/', 'Produtos Vendidos', "&tab=geral&content1=" . urlencode($r_content1) . '&D024_Id=' . urlencode($r_D024_Id));
//			}
		if(isset($g['c029Ids'][143])){
			//array(uniqid(), '/crm/crm001/grid/crm001GridAgenda/', 'Agenda', "&tab=geral&content1=" . urlencode($r_content1) . '&acaoId=' . urlencode($r_D024_Id)), 
			$tabsList[] = array(uniqid(), '/crm/crm002/grid/crm002GridAgenda/', 'Agenda', "&tab=geral&content1=" . urlencode($r_content1) . '&D024_Id=' . urlencode($r_D024_Id));    
		}
		if(isset($g['c029Ids'][133])){
			$tabsList[] = array(uniqid(), '/crm/crm001/content/crm001ContentEmailCliente/', 'E-mail', "&tab=geral&content1=" . urlencode($r_content1) . '&D024_Id=' . urlencode($r_D024_Id));
		}
		if(isset($g['c029Ids'][144])){
			//array(uniqid(), '/crm/crm001/grid/crm001GridContatos/', 'Contatos', "&tab=geral&content1=" . urlencode($r_content1) . '&acaoId=' . urlencode($r_D024_Id)),  
			//$tabsList[] = array(uniqid(), '/crm/crm002/grid/crm002GridContatos/', 'Contatos', "&tab=geral&content1=" . urlencode($r_content1) . '&D024_Id=' . urlencode($r_D024_Id));  
		}
			//array(uniqid(), '/crm/crm002/grid/crm002GridRequisicao/', 'Requisição', "&tab=geral&content1=" . urlencode($r_content1) . '&D024_Id=' . urlencode($r_D024_Id)),  
		if(isset($g['c029Ids'][134])){	
			//$tabsList[] = array(uniqid(), '/crm/crm002/grid/crm002GridCotacao/', 'Cotação', "&tab=geral&content1=" . urlencode($r_content1) . '&D024_Id=' . urlencode($r_D024_Id));  
		}
		if(isset($g['c029Ids'][135])){
			//$tabsList[] = array(uniqid(), '/crm/crm002/grid/crm002GridOrdem/', 'O.C.', "&tab=geral&content1=" . urlencode($r_content1) . '&D024_Id=' . urlencode($r_D024_Id));  
		}
		if(isset($g['c029Ids'][136])){
			//$tabsList[] = array(uniqid(), '/crm/crm002/grid/crm002GridNFRecebida/', 'NF Recebidas', "&tab=geral&content1=" . urlencode($r_content1) . '&D024_Id=' . urlencode($r_D024_Id));
		}
		if(isset($g['c029Ids'][137])){
			//$tabsList[] = array(uniqid(), '/crm/crm002/grid/crm002GridConhecimentos/', 'Conhecimentos', "&tab=geral&content1=" . urlencode($r_content1) . '&D024_Id=' . urlencode($r_D024_Id));  
		}
		if(isset($g['c029Ids'][138])){
			//$tabsList[] = array(uniqid(), '/crm/crm003/grid/crm003GridReceberCliente/', 'Receber', "&tab=geral&content1=" . urlencode($r_content1) . '&D024_Id=' . urlencode($r_D024_Id));  				
			$tabsList[] = array(uniqid(), '/rel/relatorios/extratoCliente/relatorio/', 'Extrato', "&tab=geral&ocultarDadosCRM=true&content1=" . urlencode($r_content1) . '&D024_Id=' . urlencode($r_D024_Id));  				
		}
		if(isset($g['c029Ids'][139])){
			//$tabsList[] = array(uniqid(), '/crm/crm003/grid/crm003GridPagar/', 'Pagar', "&tab=geral&content1=" . urlencode($r_content1) . '&D024_Id=' . urlencode($r_D024_Id));  
		}
		if(isset($g['c029Ids'][140])){
			//$tabsList[] = array(uniqid(), '/crm/crm003/grid/crm003GridBoletos/', 'Boletos', "&tab=geral&content1=" . urlencode($r_content1) . '&D024_Id=' . urlencode($r_D024_Id));  
		}
		if(isset($g['c029Ids'][120])){
			//$tabsList[] = array(uniqid(), '/crm/crm004/grid/crm004GridResumo/', 'Resumo', "&tab=geral&content1=" . urlencode($r_content1) . '&D024_Id=' . urlencode($r_D024_Id));    			
		}
		$tabsList[] = array(uniqid(), '/crm/crm001/outro/crm001ResumoCliente/', 'Resumo', "&tab=geral&content1=" . urlencode($r_content1) . '&D024_Id=' . urlencode($r_D024_Id));

		//Para exibir os preços de venda por produto de cada cliente
		if(isset($g['c029Ids'][130]) || isset($g['c029Ids'][131]) || isset($g['c029Ids'][200])){
			$tabsList[] = array(uniqid(), '/crm/crm002/grid/crm002GridTabelaPrecosCliente/', 'Tabela de preços', "&tab=geral&D024_Id=" . urlencode($r_D024_Id));
		}
		if(isset($g['c029Ids'][219])){
			$tabsList[] = array(uniqid(), '/fin/fin007/grid/DemonstrativoSaldoLimite/', 'CNPJ Matriz', '&tab=geral&acaoId=' . urlencode($r_D024_Id), false, true);
		}
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'crm001TabSaldoGeralPedidos':

        // Definição das Tabs
        $tabs = array();
        $tabsList = array(
        // Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
            array(uniqid(), '/crm/crm001/grid/crm001GridSaldoGeralPedidos/', 'Saldo Geral', '&tab=geral&D024_Id=' . urlencode($r_D024_Id)),
        );

        // Processa Tabs
        echo gProcessaTabs($tabsList, $tabs);

        // Carrega Template
        $g['smarty']->assign('tabs', $tabs);
        $g['smarty']->display('tab.tpl');
    break;

    case 'crm001TabPedidos':

        // Definição das Tabs
        $tabs = array();
        $tabsList = array(
        // Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
            array(uniqid(), '/crm/crm001/grid/crm001GridPrincipalPedidos/', 'Pedidos' ),
            array(uniqid(), '/crm/crm001/grid/crm001GridPrincipalPedidosProdutos/', 'Produtos' ),
			array(uniqid(), '/crm/crm001/grid/crm001GridPrincipalPedidosCompraInterna/', 'Compra interna' ),
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
    
    case 'crm001TabPedidosProduto':

    	/**
		* Cabeçalho (Versão Acima da TAB)
		*/
		$cabecalhoId = uniqid();
		echo '<div id="'.$cabecalhoId.'" class="contentCabecalho" style="height: 38px;"></div>';
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($cabecalhoId, '/crm/crm001/outro/cabecalhoPedido/', '&acaoId=' . urlencode($r_acaoId), true),
		);
		echo gProcessaAcoes($actions);

		$sqlD001 = mysql_query("SELECT D049_D001_Id, D009_C004_Id FROM D049 LEFT JOIN D009 ON D049_Id=D009_D049_Id WHERE D009_Id ='{$r_acaoId}'");
		$resD001 = mysql_fetch_array($sqlD001);
        // Definição das Tabs
        $tabs = array();
        $tabsList = array(
        // Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
            array(uniqid(), '/cad/cad002/grid/pedidos/', 'Pedidos', '&tab=geral&acaoId=' . urlencode($r_acaoId), false, true),
            array(uniqid(), '/cad/cad002/grid/locacaoVisualizar/', 'Locações', '&acaoId=' . urlencode($r_acaoId)),
            array(uniqid(), '/cad/cad002/grid/cad002GridEstoqueExterno/', 'Estoque Fora', '&D001_Id=' . urlencode($resD001['D049_D001_Id']) . '&C004_Id=' . $resD001['D009_C004_Id']),
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
    
    case 'crm001TabDiMP':

        // Definição das Tabs
        $tabs = array();
        $tabsList = array(
        // Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
            array(uniqid(), '/cad/cad002/grid/diMP/', 'Ordens de Compra', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
        );

        // Processa Tabs
        echo gProcessaTabs($tabsList, $tabs);

        // Carrega Template
        $g['smarty']->assign('tabs', $tabs);
        $g['smarty']->display('tab.tpl');
    break;    

    case 'crm001TabDiLP':

        // Definição das Tabs
        $tabs = array();
        $tabsList = array(
        // Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
            array(uniqid(), '/cad/cad002/grid/diLP/', 'Ordens de Compra', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
        );

        // Processa Tabs
        echo gProcessaTabs($tabsList, $tabs);

        // Carrega Template
        $g['smarty']->assign('tabs', $tabs);
        $g['smarty']->display('tab.tpl');
    break;         
    
	
	case 'crm001TabEditarAgenda':
		if(isset($r_acaoId) && $r_acaoId > 0 || isset($r_D024_Id) && $r_D024_Id > 0){
			/**
			* Cabeçalho (Versão Acima da TAB)
			*/
			$cabecalhoId = uniqid();
			echo '<div id="'.$cabecalhoId.'" class="contentCabecalho" style="height: 33px;"></div>';
			$actions = array(
				// identificador, url, paramentros get (opcional)
				array($cabecalhoId, '/cad/cad001/outro/cad001CabecalhoAgenda/', '&acaoId=' . urlencode($r_acaoId), true),
			);
			echo gProcessaAcoes($actions);
		}

		$formId = uniqid();
		$g['smarty']->assign('formId', $formId);
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/crm/crm001/form/crm001FormEditarAgenda/', 'Agenda', '&tab=geral&acaoId=' . urlencode($r_acaoId) . '&D024_Id=' . urlencode($r_D024_Id) . '&formId=' . urlencode($formId)),
			//array(uniqid(), '/crm/crm001/form/crm001FormEditarAgenda/', 'Associações', '&tab=associacoes&acaoId=' . urlencode($r_acaoId) . '&D024_Id=' . urlencode($r_D024_Id) . '&formId=' . urlencode($formId)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'crm001TabEditarAgendaOrcamento':
		/**
		* Cabeçalho (Versão Acima da TAB)
		*/
		$cabecalhoId = uniqid();
		echo '<div id="'.$cabecalhoId.'" class="contentCabecalho" style="height: 38px;"></div>';
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($cabecalhoId, '/cad/cad001/outro/cad001CabecalhoAgenda/', '&acaoId=' . urlencode($r_acaoId), true),
		);
		echo gProcessaAcoes($actions);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/crm/crm001/form/crm001FormEditarAgendaOrcamento/', 'Agenda', '&tab=geral&acaoId=' . urlencode($r_acaoId) . '&D024_Id=' . urlencode($r_D024_Id)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'crm001TabEditarContato':

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/crm/crm001/form/crm001FormEditarContato/', 'Contato', '&D024_Id=' . urlencode($r_D024_Id) . '&tab=geral&acaoId=' . urlencode($r_acaoId)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'crm001TabDuplicarPedido':

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/crm/crm001/form/crm001FormSelecionarDiasDuplicarPedido/', 'Duplicar Pedido', '&tab=geral&acaoId=' . urlencode($r_acaoId) . '&extra=' . urlencode($r_extra)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'crm001TabIncluirAgendaGeral':

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/crm/crm001/form/crm001FormIncluirAgendaGeral/', 'Incluir Agenda', '&tab=geral&extra=' . urlencode($r_extra)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'crm001TabGridAgenda':

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/crm/crm001/grid/crm001GridAgendaGeral/', 'Agenda'),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'crm001TabAnaliseCompras':

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/crm/crm001/grid/crm001GridAnaliseCompras/', 'Análise de Compras'),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'crm001TabOrcamentoRetornoPendente':
	    // Definição das Tabs
		$tabs = array();
		$tabsList = array(
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/crm/crm001/grid/crm001GridOrcamentoRetornoPendente/', 'Agenda Pendente'),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'crm001Resumos':

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/crm/crm001/outro/crm001Resumo/', 'R1', "&acaoId=" . urlencode($r_acaoId)),
		);
		if(isset($g['c029Ids'][200]) || $g['nivelAlcada'] == '4'){
			$tabsList[] = array(uniqid(), '/crm/crm001/grid/crm001GridResumo/', 'R2', "&tab=geral&acaoId=" . urlencode($r_acaoId));
		}

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');		
	break;

	case 'crm001TabOrcamentos':
        // Definição das Tabs
        $tabs = array();
        $tabsList = array(
        // Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
            array(uniqid(), '/crm/crm001/grid/crm001GridPrincipalOrcamentos/', 'Orçamentos'),
            array(uniqid(), '/crm/crm001/grid/crm001GridPrincipalOrcamentosProdutos/', 'Produtos'),
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
	
	case 'crm001TabPrincipalB2B':
        // Definição das Tabs
        $tabs = array();
		$tabsList = array();
		
		if(isset($g['c029Ids'][251])){
			$tabsList[] = array(uniqid(), '/crm/crm001/grid/crm001GridNotificacoes/', 'Notificações Celular');
		}
        // Processa Tabs
        echo gProcessaTabs($tabsList, $tabs);

        // Carrega Template
        $g['smarty']->assign('tabs', $tabs);
        $g['smarty']->display('tab.tpl');
    break;

    case 'crm001TabConfiguracoesComerciais':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array();
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
		if(isset($g['c029Ids'][71])){
			$tabsList[] = array(uniqid(), '/cad/cad046/grid/cad046GridMotivosCancelamento/', 'Motivos Cancelamento', '&tab=geral&acaoId=' . urlencode($r_acaoId));
		}
		if(isset($g['c029Ids'][92])){
			$tabsList[] = array(uniqid(), '/cad/cad047/grid/cad047GridTransportesTipos/', 'Tipos de Transporte', '&tab=geral&acaoId=' . urlencode($r_acaoId));
		}
		if(isset($g['c029Ids'][194])){
			$tabsList[] = array(uniqid(), '/cad/cad059/grid/cad059GridMoedas/', 'Moedas', "");
		}
		if(isset($g['c029Ids'][197])){
			$tabsList[] = array(uniqid(), '/cad/cad060/grid/cad060GridMontagemIPV/', 'Variavéis IPV', "");
		}
		if(isset($g['c029Ids'][204])){
			$tabsList[] = array(uniqid(), '/cad/cad060/grid/cad060GridTipoAgenda/', 'Tipo agenda', "");
		}
		if(isset($g['c029Ids'][212])){
			$tabsList[] = array(uniqid(), '/cad/cad061/grid/cad061gridPrincipalPedidos/', 'Pedidos Periodicos', '');
		}
		if(isset($g['c029Ids'][216])){
			$tabsList[] = array(uniqid(), '/cad/cad062/grid/cad062GridRMA/', 'Status RMA', '&tab=geral&acaoId=' . urlencode($r_acaoId));
		}
		if(isset($g['c029Ids'][216])){
			$tabsList[] = array(uniqid(), '/cad/cad062/grid/cad062GridRMATipo/', 'Tipo RMA', '&tab=geral&acaoId=' . urlencode($r_acaoId));
		}

		if(isset($g['c029Ids'][71])){
			$tabsList[] = array(uniqid(), '/est/est018/grid/est018GridStatusOC/', 'Status Ordem Compra', '&tab=geral&acaoId=' . urlencode($r_acaoId));
		}

		if (isset($g['c029Ids'][249])) {
			$tabsList[] = array(uniqid(), '/cad/cad030/content/cad030ContentUsuariosB2B/', 'Usuários B2B', "&acaoId=" . urlencode($r_acaoId), false, true);
		}

		$tabsList[] = array(uniqid(), '/crm/crm001/grid/crm001GridCanaisProspecaoCliente/', 'Canais Prospecção Cliente', '');

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'crm001TabPrincipalNotasFiscais':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/crm/crm001/grid/crm001GridPrincipalNotasFiscais/', 'Notas Fiscais', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
			array(uniqid(), '/crm/crm001/grid/crm001gridPrincipalProdutos/', 'Produtos', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
		);
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'crm001TabCanalProspecaoCliente':
		$formId = uniqid();
		$g['smarty']->assign('formId', $formId);
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/crm/crm001/form/crm001FormCanalProspecaoCliente/', 'Canal de Prospecção', '&tab=geral&acaoId=' . urlencode($r_acaoId))
		);
		echo gProcessaTabs($tabsList, $tabs);
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'crm001TabPrincipalClientes':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/crm/crm001/grid/crm001GridPrincipalClientesAtivos/', 'Clientes Ativos', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
			array(uniqid(), '/crm/crm001/grid/crm001GridPrincipalClientesInativos/', 'Clientes Inativos', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
		);
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'crm001TabPrincipalOrdens':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/est/est009/grid/est009GridComprasEstoqueModelo2View/', 'Compras estoque M2', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
			array(uniqid(), '/crm/crm001/grid/crm001GridPrincipalOrdemCompra/', 'Ordem Compra', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
			array(uniqid(), '/crm/crm001/grid/crm001GridPrincipalOrdemProducao/', 'Ordem Produção', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
		);
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'crm001TabCancelarPedidos':
		/**
		* Este código PHP será executado ao acessar: /crm-crm001-tab-crm001TabCancelarPedidos/
		* Ele irá substituir o original (que fica dentro do .PHP, no switch/case)
		*
		* Caso seja uma AJAX, é necessário acrescentar este código:
		* No inicio: $resposta = array('code' => true, 'data' => array());
		* No final: echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
		*/

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/crm/crm001/grid/crm001GridCancelarPedidos/', 'Cancelar Pedidos', '&tab=geral&extra=' . urlencode($r_extra)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'crm001TabIncluirProdutosTabelaPreco':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/crm/crm001/grid/crm001GridIncluirProdutosTabelaPreco/', 'Produtos', '&tab=geral&extra=' . urlencode($r_extra)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'crm001TabTabelaPrecos':
		/**
		* Cabeçalho (Versão Acima da TAB)
		*/
		$cabecalhoId = uniqid();
		echo '<div id="'.$cabecalhoId.'" class="contentCabecalho" style="height: 38px;"></div>';
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($cabecalhoId, '/crm/crm001/outro/crm001CabecalhoTabelaPrecoCliente/', '&acaoId=' . urlencode($r_acaoId), true),
		);
		echo gProcessaAcoes($actions);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/crm/crm001/grid/crm001GridTabelaPrecos/', 'Produtos', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
			array(uniqid(), '/crm/crm001/content/crm001ContentCSVTabelaPrecos/', 'Importar CSV', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
		
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'crm001TabOrcamentoPerdido':
		// Definição das Tabs
		$cabecalhoId = uniqid();
		echo '<div id="'.$cabecalhoId.'" class="contentCabecalho" style="height: 38px;"></div>';
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($cabecalhoId, '/crm/crm001/outro/crm001CabecalhoOrcamentoCancelado/', '&acaoId=' . urlencode($r_acaoId), true),
		);
		echo gProcessaAcoes($actions);

		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/crm/crm001/grid/crm001GridOrcamentoPerdido/', 'Orçamentos', '&acaoId=' . urlencode($r_acaoId)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
}
