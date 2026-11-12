<?php
namespace hardness;
	/**
	* Weiberlan
	* 08/11/2011
	*/

	/**
	* Ações
	*/
	switch ($g['r'][3]) {

		/**
		* Grid Principal
		* Chamado em: ven002content01
		*/
		case 'ven002tab01':
			// Form (caso seja o mesmo form em multiplas tabs)
			//$formId = uniqid();
			//$g['smarty']->assign('formId', $formId);

			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/ven/ven002/grid/ven002grid01/', 'Pedidos'),
            array(uniqid(), '/ven/ven002/grid/ven002GridProdutosPedido/', 'Produtos'),
            array(uniqid(), '/ven/ven002/grid/ven002GridServicosPedido/', 'Serviços'),
            array(uniqid(), '/ven/ven002/grid/ven002GridProdutosTrocaPedido/', 'Troca'),
			);

			// Processa Tabs
			echo gProcessaTabs($tabsList, $tabs);

			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('tab.tpl');
			break;


			/**
			* Form Principal - Cadastro pedidos
			* Chamado em: ven002content02
			*/

		case 'ven002tab02':
			/**
			* Cabeçalho (Versão Acima da TAB)
			*/
			$cabecalhoId = uniqid();
			echo '<div id="'.$cabecalhoId.'" class="contentCabecalho" style="height: 38px;"></div>';
			$actions = array(
				// identificador, url, paramentros get (opcional)
				array($cabecalhoId, '/ven/ven002/outro/ven002CabecalhoOrcamento/', '&acaoId=' . urlencode($r_acaoId). '&D024_Id=' . urlencode($D024_Id), true),
			);
			echo gProcessaAcoes($actions);
			
			if ($_REQUEST['acaoId']) {
				$tabs = array();
				$tabsList = array(
				);
				$tab2Id = uniqid();
				array_push($tabsList, array($tab2Id, '/ven/ven002/content/ven002content04/', 'Produtos', "&acaoId=" . urlencode($_REQUEST['acaoId']) . "&T005_Id=" . urlencode($r_acaoId)));
				$tab1Id = uniqid();
				array_push($tabsList, array($tab1Id, '/ven/ven002/form/ven002form01/', 'Cabeçalho', "&tab=geral&acaoId=" . urlencode($_REQUEST['acaoId']) . '&divIdMain=' . urlencode($r_divIdMain)));
				//$tab4Id = uniqid();
				//array_push($tabsList, array($tab4Id, '/ven/ven002/form/ven002FormTotais/', 'Totais', "&tab=geral&acaoId=" . urlencode($_REQUEST['acaoId']) . "&T005_Id=" . urlencode($_REQUEST['acaoId']),false,true));
				$tab5Id = uniqid();
				array_push($tabsList, array($tab5Id, '/ven/ven002/content/ven002ContentPagamento/', 'Pagamento', "&acaoId=" . urlencode($_REQUEST['acaoId']) . "&T005_Id=" . urlencode($_REQUEST['acaoId']),false,true));
				$tab6Id = uniqid();
				array_push($tabsList, array($tab6Id, '/ven/ven002/content/ven002ContentDocumentos/', 'Documentos', "&acaoId=" . urlencode($_REQUEST['acaoId']) . "&T005_Id=" . urlencode($_REQUEST['acaoId'])));
				$tab8Id = uniqid();
				array_push($tabsList, array($tab8Id, '/ven/ven002/content/ven002ContentEmail/', 'E-mail', "&acaoId=" . urlencode($_REQUEST['acaoId']) . "&T005_Id=" . urlencode($_REQUEST['acaoId'])));
				$tab7Id = uniqid();
				array_push($tabsList, array($tab7Id, '/ven/ven002/grid/ven002GridHistorico/', 'Histórico', "&acaoId=" . urlencode($_REQUEST['acaoId']) . "&T005_Id=" . urlencode($_REQUEST['acaoId']),false,true));
				//$tab9Id = uniqid();
				//array_push($tabsList, array($tab9Id, '/ven/ven002/form/ven002FormPedidoFaturamento/', 'Faturamento', "&tab=geral&acaoId=" . urlencode($_REQUEST['acaoId']) . "&T005_Id=" . urlencode($_REQUEST['acaoId'])));
				//$tab9Id = uniqid();
				//array_push($tabsList, array($tab9Id, '/ven/ven002/content/ven002contentTrocas/', 'Trocas', "&acaoId=" . urlencode($_REQUEST['acaoId']) . "&T005_Id=" . urlencode($r_acaoId)));
				//$tab3Id = uniqid();
				//array_push($tabsList, array($tab3Id, '/ven/ven002/content/ven002content05/', 'Serviços', "&acaoId=" . urlencode($_REQUEST['acaoId']) . "&T005_Id=" . urlencode($_REQUEST['acaoId']),false,true));
				//$tab8Id = uniqid();
				//array_push($tabsList, array($tab8Id, '/ven/ven002/grid/ven002GridEntregaServicos/', 'Entrega', "&tab=geral&acaoId=" . urlencode($_REQUEST['acaoId']) . "&T005_Id=" . urlencode($_REQUEST['acaoId']),false,true));
				$tab10Id = uniqid();
				array_push($tabsList, array($tab10Id, '/ven/ven002/form/ven002FormMoeda/', 'Moeda', "&tab=geral&acaoId=" . urlencode($_REQUEST['acaoId']) . "&T005_Id=" . urlencode($_REQUEST['acaoId']),false,true));
				$tab11Id = uniqid();
				array_push($tabsList, array($tab11Id, '/etc/etc017/content/etc017ContentAtividadesT005/', 'Atividades', "&tab=geral&T005_Id=" . urlencode($_REQUEST['acaoId']),false,true));
				
				if($g['nivelAlcada'] == 4){
					$tab3Id = uniqid();
					array_push($tabsList, array($tab3Id, '/ven/ven002/form/ven002FormComissoes/', 'Comissões', "&tab=geral&acaoId=" . urlencode($_REQUEST['acaoId']),false,true));
				}
				$tab12Id = uniqid();
				array_push($tabsList, array($tab12Id, '/ven/ven002/form/ven002formConfiguracoes/', 'Configurações', "&tab=geral&acaoId=" . urlencode($_REQUEST['acaoId']),false,true));
				// Processa Tabs
				echo gProcessaTabs($tabsList, $tabs);

				// Carrega template
				$g['smarty']->assign('tabs', $tabs);
				$g['smarty']->display('tab.tpl');
			}
		break;

		case 'ven002tab03':
			// Form (caso seja o mesmo form em multiplas tabs)
			//$formId = uniqid();
			//$g['smarty']->assign('formId', $formId);

			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/ven/ven002/content/ven002content07/', 'Processos', '&T147_Id=' . urlencode($r_T147_Id)),
			array(uniqid(), '/ven/ven002/content/ven002content06/', 'Materiais', '&T147_Id=' . urlencode($r_T147_Id)),

			);

			// Processa Tabs
			echo gProcessaTabs($tabsList, $tabs);

			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('tab.tpl');
			break;

		case 'ven002TabEditarProduto':
			$sqlT006 = mysql_query("SELECT IFNULL(D049_Flag_Tipo,'CP') AS D049_Flag_Tipo, T006_D059_Id FROM T006 LEFT JOIN D009 ON D009_Id=T006_D009_Id LEFT JOIN D049 ON D049_Id=D009_D049_Id WHERE T006_Id = '{$r_acaoId}'");
			$rowT006 = mysql_fetch_array($sqlT006);
			// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
			$formId = uniqid();
			$g['smarty']->assign('formId', $formId);
			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
				array(uniqid(), '/ven/ven002/form/ven002FormProdutosEditar/', 'Produto', '&tab=item&acaoId=' . urlencode($r_acaoId) . '&formId=' . urlencode($formId)),
				array(uniqid(), '/ven/ven002/form/ven002FormProdutosEditar/', 'IPI', '&tab=ipi&acaoId=' . urlencode($r_acaoId) . '&formId=' . urlencode($formId)),
				array(uniqid(), '/ven/ven002/form/ven002FormProdutosEditar/', 'Icms', '&tab=icms&acaoId=' . urlencode($r_acaoId) . '&formId=' . urlencode($formId) . '&D059_Id=' . $rowT006['T006_D059_Id']),
				array(uniqid(), '/ven/ven002/form/ven002FormProdutosEditar/', 'Pis/Cofins', '&tab=pis-cofins&acaoId=' . urlencode($r_acaoId) . '&formId=' . urlencode($formId)),
	            array(uniqid(), '/ven/ven002/content/ven002ContentAgregados/', 'Agregados', '&tab=geral&acaoId=' . urlencode($r_acaoId) . '&formId=' . urlencode($formId). '&T005_Id=' . urlencode($r_T005_Id)),
			);
			if($rowT006['D049_Flag_Tipo'] == 'PD'){
				$tabsList[] = array(uniqid(), '/ven/ven002/content/ven002ContentEngenhariaProduto/', 'Engenharia', '&tab=geral&acaoId=' . urlencode($r_acaoId) . '&formId=' . urlencode($formId));
			}

			// Processa Tabs
			echo gProcessaTabs($tabsList, $tabs, true);

			// Carrega template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('tab.tpl');
			break;

		case 'ven002TabPagamento':
			// Form (caso seja o mesmo form em multiplas tabs)
			//$formId = uniqid();
			//$g['smarty']->assign('formId', $formId);

			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/ven/ven002/content/ven002ContentPagamento/', 'Pagamento', '&T005_Id=' . urlencode($r_T005_Id)),
			);

			// Processa Tabs
			echo gProcessaTabs($tabsList, $tabs);

			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('tab.tpl');
		break;

	case 'ven002TabIncluirPedido':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/ven/ven002/form/ven002FormIncluirPedido/', 'Pedido', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'ven002TabCompras':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/ven/ven002/grid/ven002GridCompras/', 'Compras', '&acaoId=' . urlencode($r_acaoId)),
		);
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'ven002TabCotar':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/ven/ven002/form/ven002FormCotar/', 'Cotar Produto', '&tab=geral&T005_Id=' . urlencode($r_T005_Id) . '&T006_Id=' . urlencode($r_T006_Id) . '&D009_Id=' . urlencode($r_D009_Id)),
		);
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'ven002TabComprar':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/ven/ven002/form/ven002FormComprar/', 'Comprar Produto', '&tab=geral&T005_Id=' . urlencode($r_T005_Id) . '&T006_Id=' . urlencode($r_T006_Id) . '&D009_Id=' . urlencode($r_D009_Id)),
		);
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'ven002TabMontagemIPV':
		// Definição das Tabs
		$tabs = array();
		
		if ($r_total){
			$tabsList = array(
				// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
				array(uniqid(), '/ven/ven002/grid/ven002GridMontagemIPVTotal/', 'IPV', "&tab=geral&total=" . urlencode($r_total)),
			);
		} else {
			$tabsList = array(
				// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
				array(uniqid(), '/ven/ven002/grid/ven002GridMontagemIPV/', 'IPV', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
			);
		}


		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'ven002TabEngenhariaProduto':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/ven/ven002/grid/ven002GridComposicaoProdutoProduzido/', 'Composicao', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
			array(uniqid(), '/ven/ven002/grid/ven002GridProcessosProdutoProduzido/', 'Processos', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
}