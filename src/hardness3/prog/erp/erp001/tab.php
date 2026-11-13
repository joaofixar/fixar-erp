<?php
namespace hardness;
	/**
	* Ações
	*/
	switch ($g['r'][3]) {

		case 'erp001TabPrincipal':
			$D024_Id=(isset($r_D024_Id)) ? $r_D024_Id : false;
			$sqlHistorico = "SELECT * FROM T194 WHERE T194_C007_Id = '{$g['usuarioAtual']}' AND T194_Status = 'A' LIMIT 4;";
			$historico = mysql_query($sqlHistorico);
			$tabs = array();
			$tabsList = array();
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			$tabsList[] = array(uniqid(), '/erp/erp001/content/erp001ContentPrincipal2rows/', 'ERP', "&tab=geral&content1=" . urlencode($r_content1) . '&D024_Id=' . urlencode($D024_Id) . '&divJanela=' . urlencode($r_divId));
			
			while($row = mysql_fetch_array($historico)){
				$sqlEmpresa = "SELECT * FROM D024 WHERE D024_Id = '{$row['T194_D024_Id']}' LIMIT 1;";
				$empresa = mysql_query($sqlEmpresa);
				if(mysql_num_rows($empresa) > 0){
					$campoNome = $g['C031']['CRMNomeTab'];
					$nomeEmpresa = mysql_fetch_array($empresa);
					$nomeEmpresa = substr($nomeEmpresa[$campoNome], 0, 12);
					$D024_Id = $row['T194_D024_Id'];
					$tabsList[] = array(uniqid(), '/erp/erp001/content/erp001ContentPrincipalerp/', $nomeEmpresa, "&tab=geral&content1=" . urlencode($r_content1) . '&D024_Id=' . $D024_Id);
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
		
		case 'erp001TabGeral':

			// Definição das Tabs
			$tabs = array();
			$tabsList = array();
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
 			if(isset($g['c029Ids'][127])){
				$tabsList[] = array(uniqid(), '/erp/erp005/content/erp005ContentPrePedido/', 'Pré Pedido', "&acaoId=" . urlencode($r_acaoId));
			}
			if(isset($g['c029Ids'][130])){
				$tabsList[] = array(uniqid(), '/erp/erp001/grid/erp001GridPrincipalOrcamentos/', 'Orçamentos', "&tab=geral&acaoId=" . urlencode($r_acaoId));
			}
			if(isset($g['c029Ids'][131])){
				$tabsList[] = array(uniqid(), '/erp/erp001/grid/erp001GridPrincipalPedidos/', 'Pedidos', "&tab=geral&acaoId=" . urlencode($r_acaoId));
			}
			if(isset($g['c029Ids'][128])){
				$tabsList[] = array(uniqid(), '/erp/erp001/grid/erp001GridCatalogo/', 'Catálogo', "&tab=geral&acaoId=" . urlencode($r_acaoId));
			}
			if(isset($g['c029Ids'][132])){
				$tabsList[] = array(uniqid(), '/erp/erp001/grid/erp001GridPrincipalNotasFiscais/', 'Notas Fiscais', "&tab=geral&acaoId=" . urlencode($r_acaoId));
			}
			if(isset($g['c029Ids'][147])){
				$tabsList[] = array(uniqid(), '/erp/erp001/grid/erp001gridPrincipalProdutos/', 'Produtos NF', "&tab=geral&acaoId=" . urlencode($r_acaoId));
			}
			if(isset($g['c029Ids'][121])){
				$tabsList[] = array(uniqid(), '/erp/erp001/grid/erp001GridPrincipalClientesAtivos/', 'Clientes Ativos', "&tab=geral&acaoId=" . urlencode($r_acaoId));
			}
			if(isset($g['c029Ids'][121])){
				$tabsList[] = array(uniqid(), '/erp/erp001/grid/erp001GridPrincipalClientesInativos/', 'Clientes Inativos', "&tab=geral&acaoId=" . urlencode($r_acaoId));
			}
			if(isset($g['c029Ids'][120])){
				$tabsList[] = array(uniqid(), '/erp/erp001/grid/erp001GridResumo/', 'Resumo', "&tab=geral&acaoId=" . urlencode($r_acaoId));
			}
			if(isset($g['c029Ids'][122])){	
				$tabsList[] = array(uniqid(), '/erp/erp001/grid/erp001GridABCClientes/', 'ABC Clientes', "&tab=geral&acaoId=" . urlencode($r_acaoId));
			}
			if(isset($g['c029Ids'][123])){
				$tabsList[] = array(uniqid(), '/erp/erp001/grid/erp001GridABCProdutos/', 'ABC Produtos', "&tab=geral&acaoId=" . urlencode($r_acaoId));
			}
			if(isset($g['c029Ids'][124])){
				$tabsList[] = array(uniqid(), '/erp/erp001/grid/erp001GridFiscal/', 'Fiscal', "&tab=geral&acaoId=" . urlencode($r_acaoId));
			}
			if(isset($g['c029Ids'][125])){
				$tabsList[] = array(uniqid(), '/erp/erp001/grid/erp001GridResumoGerencial/', 'Resumo CFOP', "&tab=geral&acaoId=" . urlencode($r_acaoId));
			}
			if(isset($g['c029Ids'][126])){
				$tabsList[] = array(uniqid(), '/erp/erp001/grid/erp001GridResumoFinanceiro/', 'Resumo Financeiro', "&tab=geral&acaoId=" . urlencode($r_acaoId));
			}
			if(isset($g['c029Ids'][143])){
				//array(uniqid(), '/erp/erp001/grid/erp001GridAgenda/', 'Agenda', "&tab=geral&content1=" . urlencode($r_content1) . '&acaoId=' . urlencode($r_D024_Id)), 
				$tabsList[] = array(uniqid(), '/erp/erp001/grid/erp001GridAgendaGeral/', 'Agenda', "&tab=geral");    
			}
			if(isset($g['c029Ids'][133])){	
				$tabsList[] = array(uniqid(), '/erp/erp001/grid/erp001GridEmail/', 'Email', "&tab=geral&acaoId=" . urlencode($r_acaoId));
			}
			if(isset($g['c029Ids'][148])){	
				$tabsList[] = array(uniqid(), '/etc/etc014/grid/etc014GridPivot/', 'Relatórios', "&bi=conf&acaoId=" . urlencode($r_acaoId) . '&divReload=' . urlencode(null));
			}
			// Processa Tabs
			echo gProcessaTabs($tabsList, $tabs);

			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('tab.tpl');
		break;
		
		case 'erp001TabPrincipalCliente':
			// Definição das Tabs
			$tabs = array();
			$tabsList = array();
			if(isset($g['c029Ids'][128])){
				$tabsList[] = array(uniqid(), '/erp/erp001/grid/erp001GridCatalogoCliente/', 'Catálogo', "&tab=geral&acaoId=" . urlencode($r_acaoId) . '&D024_Id=' . urlencode($r_D024_Id));
			}
			if(isset($g['c029Ids'][127])){
				$tabsList[] = array(uniqid(), '/erp/erp005/content/erp005ContentProduto/', 'Pré Pedido', "&tab=geral&content1=" . urlencode($r_content1) . '&D024_Id=' . urlencode($r_D024_Id));    			
			}
			if(isset($g['c029Ids'][130])){
				$tabsList[] = array(uniqid(), '/erp/erp002/grid/erp002GridPrincipalOrcamentosClienteAbertos/', 'Orçamentos', "&tab=geral&content1=" . urlencode($r_content1) . '&D024_Id=' . urlencode($r_D024_Id));
			}
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			if(isset($g['c029Ids'][131])){
				$tabsList[] = array(uniqid(), '/erp/erp002/grid/erp002GridPrincipalPedidosClienteAbertos/', 'Pedidos', "&tab=geral&content1=" . urlencode($r_content1) . '&D024_Id=' . urlencode($r_D024_Id));
			}
			if(isset($g['c029Ids'][132])){
				$tabsList[] = array(uniqid(), '/erp/erp002/grid/erp002GridNotasEmitidasCliente/', 'NF Emitidas', "&tab=geral&content1=" . urlencode($r_content1) . '&D024_Id=' . urlencode($r_D024_Id));
			}
			if(isset($g['c029Ids'][146])){
				$tabsList[] = array(uniqid(), '/erp/erp002/grid/erp002gridProdutosCliente/', 'Produtos', "&tab=geral&content1=" . urlencode($r_content1) . '&D024_Id=' . urlencode($r_D024_Id));
			}
			if(isset($g['c029Ids'][133])){
				$tabsList[] = array(uniqid(), '/erp/erp002/grid/erp002GridEmail/', 'E-mail', "&tab=geral&content1=" . urlencode($r_content1) . '&D024_Id=' . urlencode($r_D024_Id));
			}
			if(isset($g['c029Ids'][143])){
				//array(uniqid(), '/erp/erp001/grid/erp001GridAgenda/', 'Agenda', "&tab=geral&content1=" . urlencode($r_content1) . '&acaoId=' . urlencode($r_D024_Id)), 
				$tabsList[] = array(uniqid(), '/erp/erp002/grid/erp002GridAgenda/', 'Agenda', "&tab=geral&content1=" . urlencode($r_content1) . '&D024_Id=' . urlencode($r_D024_Id));    
			}
			if(isset($g['c029Ids'][144])){
				//array(uniqid(), '/erp/erp001/grid/erp001GridContatos/', 'Contatos', "&tab=geral&content1=" . urlencode($r_content1) . '&acaoId=' . urlencode($r_D024_Id)),  
				$tabsList[] = array(uniqid(), '/erp/erp002/grid/erp002GridContatos/', 'Contatos', "&tab=geral&content1=" . urlencode($r_content1) . '&D024_Id=' . urlencode($r_D024_Id));  
			}
				//array(uniqid(), '/erp/erp002/grid/erp002GridRequisicao/', 'Requisição', "&tab=geral&content1=" . urlencode($r_content1) . '&D024_Id=' . urlencode($r_D024_Id)),  
			if(isset($g['c029Ids'][134])){	
				$tabsList[] = array(uniqid(), '/erp/erp002/grid/erp002GridCotacao/', 'Cotação', "&tab=geral&content1=" . urlencode($r_content1) . '&D024_Id=' . urlencode($r_D024_Id));  
			}
			if(isset($g['c029Ids'][135])){
				$tabsList[] = array(uniqid(), '/erp/erp002/grid/erp002GridOrdem/', 'O.C.', "&tab=geral&content1=" . urlencode($r_content1) . '&D024_Id=' . urlencode($r_D024_Id));  
			}
			if(isset($g['c029Ids'][136])){
				$tabsList[] = array(uniqid(), '/erp/erp002/grid/erp002GridNFRecebida/', 'NF Recebidas', "&tab=geral&content1=" . urlencode($r_content1) . '&D024_Id=' . urlencode($r_D024_Id));
			}
			if(isset($g['c029Ids'][137])){
				$tabsList[] = array(uniqid(), '/erp/erp002/grid/erp002GridConhecimentos/', 'Conhecimentos', "&tab=geral&content1=" . urlencode($r_content1) . '&D024_Id=' . urlencode($r_D024_Id));  
			}
			if(isset($g['c029Ids'][138])){
				$tabsList[] = array(uniqid(), '/erp/erp003/grid/erp003GridReceber/', 'Receber', "&tab=geral&content1=" . urlencode($r_content1) . '&D024_Id=' . urlencode($r_D024_Id));  				
			}
			if(isset($g['c029Ids'][139])){
				$tabsList[] = array(uniqid(), '/erp/erp003/grid/erp003GridPagar/', 'Pagar', "&tab=geral&content1=" . urlencode($r_content1) . '&D024_Id=' . urlencode($r_D024_Id));  
			}
			if(isset($g['c029Ids'][140])){
				$tabsList[] = array(uniqid(), '/erp/erp003/grid/erp003GridBoletos/', 'Boletos', "&tab=geral&content1=" . urlencode($r_content1) . '&D024_Id=' . urlencode($r_D024_Id));  
			}
			if(isset($g['c029Ids'][120])){
				$tabsList[] = array(uniqid(), '/erp/erp004/grid/erp004GridResumo/', 'Resumo', "&tab=geral&content1=" . urlencode($r_content1) . '&D024_Id=' . urlencode($r_D024_Id));    			
			}
			// Processa Tabs
			echo gProcessaTabs($tabsList, $tabs);

			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('tab.tpl');
		break;
		
		case 'erp001TabSaldoGeralPedidos':

            // Definição das Tabs
            $tabs = array();
            $tabsList = array(
            // Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
                array(uniqid(), '/erp/erp001/grid/erp001GridSaldoGeralPedidos/', 'Saldo Geral', '&tab=geral&D024_Id=' . urlencode($r_D024_Id)),
            );

            // Processa Tabs
            echo gProcessaTabs($tabsList, $tabs);

            // Carrega Template
            $g['smarty']->assign('tabs', $tabs);
            $g['smarty']->display('tab.tpl');
        break;
        
        case 'erp001TabPedidosProduto':

            // Definição das Tabs
            $tabs = array();
            $tabsList = array(
            // Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
                array(uniqid(), '/cad/cad002/grid/pedidos/', 'Pedidos', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
            );

            // Processa Tabs
            echo gProcessaTabs($tabsList, $tabs);

            // Carrega Template
            $g['smarty']->assign('tabs', $tabs);
            $g['smarty']->display('tab.tpl');
        break;
        
        case 'erp001TabDiMP':

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

        case 'erp001TabDiLP':

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
        
		
		case 'erp001TabEditarAgenda':

			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
				array(uniqid(), '/erp/erp001/form/erp001FormEditarAgenda/', 'Agenda', '&tab=geral&acaoId=' . urlencode($r_acaoId) . '&D024_Id=' . urlencode($r_D024_Id)),
			);

			// Processa Tabs
			echo gProcessaTabs($tabsList, $tabs);

			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('tab.tpl');
		break;
		
		case 'erp001TabEditarContato':

			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
				array(uniqid(), '/erp/erp001/form/erp001FormEditarContato/', 'Contato', '&D024_Id=' . urlencode($r_D024_Id) . '&tab=geral&acaoId=' . urlencode($r_acaoId)),
			);

			// Processa Tabs
			echo gProcessaTabs($tabsList, $tabs);

			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('tab.tpl');
		break;
	}

?>
