<?php
namespace hardness;
/**
* RubiaYwata
* 18/11/2011
*/

/**
* Ações
*/
switch ($g['r'][3]) {

	/**
	* Chamado em: ven003Content01
	*/
	
	case 'ven003TabPrincipal':
		// Definição das Tabs
		$tabs = array();
		$uniqidPrincipal=uniqid();
		$tabsList = array(
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)			
			array($uniqidPrincipal, '/ven/ven003/grid/ven003GridPrincipal/', 'Notas Fiscais', "&tab=geral&acaoId=" . urlencode($_REQUEST['acaoId'])),
			array(uniqid(), '/ven/ven003/grid/ven003GridAnalitico/', 'Produtos', "&tab=geral" ),
			//array(uniqid(), '/ven/ven012/grid/ven012GridFilaDeEnvioNovo/', 'Enviar NFe para Sefaz', "&acaoId=" . urlencode($_REQUEST['acaoId'])),
			array(uniqid(), '/ven/ven012/content/ven012EnviarNFe/', 'Enviar NFe para Sefaz', "&tab=geral&acaoId=" . urlencode($_REQUEST['acaoId']) . "&divReload=" . urlencode($uniqidPrincipal)),
			array(uniqid(), '/ven/ven003/content/ven003ContentInutilizarNumeracao/', 'Inutilizar', "&tab=geral&acaoId=" . urlencode($_REQUEST['acaoId'])),
			array(uniqid(), '/ven/ven003/content/ven003ContentImportarXMLTXT/', 'Importar XML/TXT', "&tab=geral&acaoId=" . urlencode($_REQUEST['acaoId']) . "&divReload=" . urlencode($uniqidPrincipal)),
			//array(uniqid(), '/ven/ven003/form/ven003FormImportarXML/', 'Importar XML', "&tab=geral&acaoId=" . urlencode($_REQUEST['acaoId']) . "&divReload=" . urlencode($uniqidPrincipal)),
			//array(uniqid(), '/ven/ven003/form/ven003FormImportarTXT/', 'Importar TXT', "&tab=geral&acaoId=" . urlencode($_REQUEST['acaoId']) . "&divReload=" . urlencode($uniqidPrincipal)),
            array(uniqid(), '/ven/ven003/grid/ven003GridCCE/', 'CC-e'),
            array(uniqid(), '/ven/ven003/grid/ven003GridRecuperacaoICMSST/', 'ICMS/ST'),
			array(uniqid(), '/ven/ven003/grid/ven003GridRelatorioPuloNumeracao/', 'Pulo Numeração', "&tab=geral&acaoId=" . urlencode($_REQUEST['acaoId']) . "&divReload=" . urlencode($uniqidPrincipal)),
		);
		if(isset($g['c029Ids'][38])){
			$tabsList[] = array(uniqid(), '/fin/fin005/grid/fin005GridPrincipal/', 'Boleto Bancário');
		}
		$tabsList[] = array(uniqid(), '/ven/ven003/grid/ven003GridConhecimentosNF/', 'Conhecimentos');
		if(isset($g['c029Ids'][242])){
			$tabsList[] = array(uniqid(), '/fis/fis012/content/fis012ContentPrincipal/', 'MDF-e');
		}
		$tabsList[] = array(uniqid(), '/ven/ven003/content/ven003ContentListaNFDebitoCredito/', 'NF Débito/Crédito');

		// if($g['usuarioAtual']==63){
		// 	$tabsList[] = array(uniqid(), '/ven/ven003/content/ven003ContentImportarXMLAtualizar/', 'Importar XML - Atualizar', "&tab=geral&acaoId=" . urlencode($_REQUEST['acaoId']) . "&divReload=" . urlencode($uniqidPrincipal));
		// }

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'ven003TabTXTXML':
		// Definição das Tabs
		$tabs = array();
		$uniqidPrincipal=uniqid();
		$tabsList = array(
			array($uniqidPrincipal, '/ven/ven003/form/ven003FormImportarXML/', 'Importar XML', "&tab=geral&acaoId=" . urlencode($_REQUEST['acaoId']) . "&divReload=" . urlencode($uniqidPrincipal)),
			array(uniqid(), '/ven/ven003/form/ven003FormImportarTXT/', 'Importar TXT', "&tab=geral&acaoId=" . urlencode($_REQUEST['acaoId']) . "&divReload=" . urlencode($uniqidPrincipal)),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');

	break;

	case 'ven003TabProdutoEditar':
		$formId = uniqid();
		$g['smarty']->assign('formId', $formId);
		// Definição das Tabs

		$T008=mysql_query("SELECT T008_D059_Id from T008 WHERE T008_Id='{$r_acaoId}'");
		$mT008=mysql_fetch_array($T008);

		$tabs = array();
		$tabsList = array(
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/ven/ven003/form/ven003FormProdutoEditar/', 'Item', "&tab=item&acaoId=" . urlencode($r_acaoId) . "&T007_Id=" . urlencode($r_T007_Id) . '&formId=' . urlencode($formId)),
			array(uniqid(), '/ven/ven003/form/ven003FormProdutoEditar/', 'IPI', "&tab=ipi&acaoId=" . urlencode($r_acaoId) . "&T007_Id=" . urlencode($r_T007_Id) . '&formId=' . urlencode($formId)),
			array(uniqid(), '/ven/ven003/form/ven003FormProdutoEditar/', 'ICMS', "&tab=icms&acaoId=" . urlencode($r_acaoId) . "&T007_Id=" . urlencode($r_T007_Id) . '&formId=' . urlencode($formId) . '&D059_Id=' . $mT008['T008_D059_Id']),
			array(uniqid(), '/ven/ven003/form/ven003FormProdutoEditar/', 'Pis/Cofins', "&tab=pisCofins&acaoId=" . urlencode($r_acaoId) . "&T007_Id=" . urlencode($r_T007_Id) . '&formId=' . urlencode($formId)),
			array(uniqid(), '/ven/ven003/form/ven003FormProdutoEditar/', 'Importação', "&tab=importacao&acaoId=" . urlencode($r_acaoId) . "&T007_Id=" . urlencode($r_T007_Id) . '&formId=' . urlencode($formId)),
			array(uniqid(), '/ven/ven003/form/ven003FormProdutoEditar/', 'Exportação', "&tab=exportacao&acaoId=" . urlencode($r_acaoId) . "&T007_Id=" . urlencode($r_T007_Id) . '&formId=' . urlencode($formId)),
			array(uniqid(), '/ven/ven003/form/ven003FormProdutoEditar/', 'Cálculos', "&tab=calculos&acaoId=" . urlencode($r_acaoId) . "&T007_Id=" . urlencode($r_T007_Id) . '&formId=' . urlencode($formId)),
			array(uniqid(), '/ven/ven003/form/ven003FormProdutoEditar/', 'Combustíveis', "&tab=combustiveis&acaoId=" . urlencode($r_acaoId) . "&T007_Id=" . urlencode($r_T007_Id) . '&formId=' . urlencode($formId)),
			array(uniqid(), '/ven/ven003/form/ven003FormProdutoEditar/', 'Medicamentos', "&tab=medicamentos&acaoId=" . urlencode($r_acaoId) . "&T007_Id=" . urlencode($r_T007_Id) . '&formId=' . urlencode($formId)),
			array(uniqid(), '/ven/ven003/form/ven003FormProdutoEditar/', 'Outras Inf.', "&tab=outros&acaoId=" . urlencode($r_acaoId) . "&T007_Id=" . urlencode($r_T007_Id) . '&formId=' . urlencode($formId)),
			/* [REFORMA TRIBUTÁRIA] - Gabriel Cegantini 19/09/2025
			==============================================================*/
			array(uniqid(), '/ven/ven003/form/ven003FormProdutoEditar/', 'Reforma', "&tab=reforma&acaoId=" . urlencode($r_acaoId) . "&T007_Id=" . urlencode($r_T007_Id) . '&formId=' . urlencode($formId)),
			array(uniqid(), '/ven/ven003/form/ven003FormProdutoEditar/', 'Referenciada', "&tab=referenciada&acaoId=" . urlencode($r_acaoId) . "&T007_Id=" . urlencode($r_T007_Id) . '&formId=' . urlencode($formId)),
			/*==============================================================*/
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs, true);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'ven003TabIncluirNotaFiscal':
		$formId = uniqid();
		$g['smarty']->assign('formId', $formId);
		
		if(empty($r_acaoId)){
			$tabTransporte = uniqid();
			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)			
				array(uniqid(), '/ven/ven003/form/ven003FormPrincipal/', 'Cabeçalho', "&tab=principal&acaoId=" . urlencode($r_acaoId) . '&formId=' . urlencode($formId) . '&tabTransporte=' . urlencode($tabTransporte)),
				array($tabTransporte, '/ven/ven003/form/ven003FormPrincipal/', 'Transporte', "&tab=transporte&acaoId=" . urlencode($r_acaoId) . '&formId=' . urlencode($formId)),
				array(uniqid(), '/ven/ven003/form/ven003FormPrincipal/', 'Adicionais', "&tab=adicionais&acaoId=" . urlencode($r_acaoId) . '&formId=' . urlencode($formId)),
				array(uniqid(), '/ven/ven003/form/ven003FormPrincipal/', 'Exportação', "&tab=exportacao&acaoId=" . urlencode($r_acaoId) . '&formId=' . urlencode($formId)),
				//array(uniqid(), '/ven/ven003/form/ven003GridReferenciada/', 'Referenciada', "&acaoId=" . urlencode($r_acaoId) . '&formId=' . urlencode($formId)),
				array(uniqid(), '/ven/ven003/form/ven003FormPrincipal/', 'Mais', "&tab=mais&acaoId=" . urlencode($r_acaoId) . '&formId=' . urlencode($formId)),
			);
		}else{
			/**
			 * Cabeçalho (Versão Acima da TAB)
			 */
			$T007 = mysql_query("SELECT * FROM T007 LEFT JOIN T007A ON T007_Id = T007A_T007_Id WHERE T007_Id = '{$r_acaoId}'");
			$T007 = mysql_fetch_array($T007);
			$cabecalhoId = uniqid();
			echo '<div id="'.$cabecalhoId.'" class="contentCabecalho" style="height: 33px;"></div>';
			$actions = array(
				// identificador, url, paramentros get (opcional)
				array($cabecalhoId, '/ven/ven003/outro/ven003CabecalhoNotaFiscal/', '&acaoId=' . urlencode($r_acaoId), true),
			);
			echo gProcessaAcoes($actions);
			$tabTransporte = uniqid();
			// Definição das Tabs
			$tabs = array();
			$tabsList = array();
			//if($T007['T007_Flag_ACP'] == '8'){
				$tabsList[] = array(uniqid(), '/ven/ven003/content/ven003ContentProdutosConsumidor/', 'Produtos', "&acaoId=" . urlencode($r_acaoId) . "&T007_Id=" . urlencode($r_acaoId) .'&formId=' . urlencode($formId), true);
			//}
			$tabsList[] = array(uniqid(), '/ven/ven003/form/ven003FormPrincipal/', 'Cabeçalho', "&tab=principal&acaoId=" . urlencode($r_acaoId) . '&formId=' . urlencode($formId) . '&tabTransporte=' . urlencode($tabTransporte));
			//if($T007['T007_Flag_ACP'] != '8'){
			//	$tabsList[] = array(uniqid(), '/ven/ven003/content/ven003ContentProdutosNF/', 'Produtos', "&acaoId=" . urlencode($r_acaoId) . "&T007_Id=" . urlencode($r_acaoId) .'&formId=' . urlencode($formId), true);
			//}
			//$tabsList[] = array(uniqid(), '/ven/ven003/content/ven003ContentServicosNF/', 'Serviços', "&acaoId=" . urlencode($r_acaoId) . "&T007_Id=" . urlencode($r_acaoId) .'&formId=' . urlencode($formId), true);
			$tabsList[] = array(uniqid(), '/ven/ven003/form/ven003FormTotais/', 'Totais', "&tab=geral&acaoId=" . urlencode($r_acaoId) . '&formId=' . urlencode($formId),false,true);
			$tabsList[] = array(uniqid(), '/ven/ven003/grid/ven003GridObservacoes/', 'Observações', "&tab=geral&acaoId=" . urlencode($r_acaoId) . '&T007_Id=' . urlencode($r_acaoId) . '&formId=' . urlencode($formId), true, true);
			$tabsList[] = array($tabTransporte, '/ven/ven003/form/ven003FormPrincipal/', 'Transporte', "&tab=transporte&acaoId=" . urlencode($r_acaoId) . '&formId=' . urlencode($formId));
			$tabsList[] = array(uniqid(), '/ven/ven003/form/ven003FormPrincipal/', 'Adicionais', "&tab=adicionais&acaoId=" . urlencode($r_acaoId) . '&formId=' . urlencode($formId));
			$tabsList[] = array(uniqid(), '/ven/ven003/form/ven003FormPrincipal/', 'Exportação', "&tab=exportacao&acaoId=" . urlencode($r_acaoId) . '&formId=' . urlencode($formId));
			$tabsList[] = array(uniqid(), '/ven/ven003/content/ven003ContentReferenciada/', 'Referenciada', "&T007_Id=" . urlencode($r_acaoId) . '&formId=' . urlencode($formId), true);
			$tabsList[] = array(uniqid(), '/ven/ven003/form/ven003FormPrincipal/', 'Mais', "&tab=mais&acaoId=" . urlencode($r_acaoId) . '&formId=' . urlencode($formId));
			$tabsList[] = array(uniqid(), '/ven/ven003/content/ven003ContentTecnico/', 'Técnico', "&tab=geral&acaoId=" . urlencode($r_acaoId) . '&formId=' . urlencode($formId), true);
			$tabsList[] = array(uniqid(), '/etc/etc017/content/etc017ContentAtividadesT007/', 'Atividades', "&T007_Id=" . urlencode($r_acaoId) . '&formId=' . urlencode($formId), true);
			if(!empty($T007['T007_Numero_Protocolo_Nfe'])) {
				$tabsList[] = array(uniqid(), '/ven/ven003/content/ven003contentPagamentos/', 'Pagamentos', "&acaoId=" . urlencode($r_acaoId) . '&formId=' . urlencode($formId), true, "divRefresh(\'{$g['divId']}\')");
				if($g['C031']['ativarPagamentosPDVNota'] == 'S'){
					$tabsList[] = array(uniqid(), '/ven/ven003/content/ven003contentPagamentosPDV/', 'Caixa', "&acaoId=" . urlencode($r_acaoId) . '&formId=' . urlencode($formId), true, "divRefresh(\'{$g['divId']}\')");
				}
				$tabsList[] = array(uniqid(), '/ven/ven003/grid/ven003GridPagamentosTitulos/', 'a Receber', "&acaoId=" . urlencode($r_acaoId) . '&formId=' . urlencode($formId), true);
				$tabsList[] = array(uniqid(), '/ven/ven003/grid/ven003GridPagamentosBoletos/', 'Boletos', "&acaoId=" . urlencode($r_acaoId) . '&formId=' . urlencode($formId), true);
				$tabsList[] = array(uniqid(), '/ven/ven003/grid/ven003GridPagamentosTitulosPagar/', 'a Pagar', "&acaoId=" . urlencode($r_acaoId) . '&formId=' . urlencode($formId), true);
				$tabsList[] = array(uniqid(), '/ven/ven003/grid/ven003GridCCe/', 'CCe', "&acaoId=" . urlencode($r_acaoId) . '&formId=' . urlencode($formId), true);
			} else {
				$tabsList[] = array(uniqid(), '/ven/ven003/content/ven003contentPagamentos/', 'Pagamentos', "&acaoId=" . urlencode($r_acaoId) . '&formId=' . urlencode($formId), true, "divRefresh(\'{$g['divId']}\')");
				if($g['C031']['ativarPagamentosPDVNota'] == 'S'){
					$tabsList[] = array(uniqid(), '/ven/ven003/content/ven003contentPagamentosPDV/', 'Caixa', "&acaoId=" . urlencode($r_acaoId) . '&formId=' . urlencode($formId), true, "divRefresh(\'{$g['divId']}\')");
				}
				if($g['C031']['utilizaUnidadeConversaoVenda'] == 'S'){
					$tabsList[] = array(uniqid(), '/ven/ven003/form/ven003formConfiguracoes/', 'Configurações', "&tab=geral&acaoId=" . urlencode($r_acaoId) . '&T007A_Id=' . urlencode($T007['T007A_Id']), true);
				}
			}
			$tabsList[] = array(uniqid(), '/ven/ven003/grid/ven003GridEventosNFe/', 'Eventos', "&acaoId=" . urlencode($r_acaoId) . '&T007_Id=' . urlencode($r_acaoId) . '&formId=' . urlencode($formId), true);

		}
		if($g['c029Ids'][275]){
			$D001 = mysql_query("SELECT T008_Id
							   	   FROM T008
							  LEFT JOIN T007 ON T007_Id = T008_T007_Id
						      LEFT JOIN D009 ON D009_Id = T008_D009_Id
						      LEFT JOIN D049 ON D049_Id = D009_D049_Id
						      LEFT JOIN D001 ON D001_Id = D049_D001_Id
							      WHERE T008_T007_Id = '{$r_acaoId}'
								    AND T007_Flag_Entrada_Saida = 'E'
								    AND D001_Flag_Tipo_Item = '08' LIMIT 1;");
			if (mysql_num_rows($D001) > 0) { // Se houver pelo menos 1 registro retornado da query acima retorna a tela para cadastro do CIAP que vai para o SPED FISCAL.
				$tabsList[] = array(uniqid(), '/ven/ven003/grid/ven003GridCiap/', 'CIAP', "&tab=geral&acaoId=" . urlencode($_REQUEST['acaoId']) . "&T007_Id=" . urlencode($_REQUEST['acaoId']) .'&formId=' . urlencode($formId), true,true);
			}
		}

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs, true);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'ven003TabEnviarNotaFiscal':
		$formId = uniqid();
		$g['smarty']->assign('formId', $formId);
		
		if(empty($r_acaoId)){
			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)			
				array(uniqid(), '/ven/ven003/form/ven003FormPrincipal/', 'Cabeçalho', "&tab=principal&acaoId=" . urlencode($r_T007_Id) . '&formId=' . urlencode($formId)),
				array(uniqid(), '/ven/ven003/form/ven003FormPrincipal/', 'Transporte', "&tab=transporte&acaoId=" . urlencode($r_T007_Id) . '&formId=' . urlencode($formId)),
				array(uniqid(), '/ven/ven003/form/ven003FormPrincipal/', 'Adicionais', "&tab=adicionais&acaoId=" . urlencode($r_T007_Id) . '&formId=' . urlencode($formId)),
				array(uniqid(), '/ven/ven003/form/ven003FormPrincipal/', 'Exportação', "&tab=exportacao&acaoId=" . urlencode($r_T007_Id) . '&formId=' . urlencode($formId)),
				array(uniqid(), '/ven/ven003/form/ven003FormPrincipal/', 'Referenciada', "&tab=referenciada&acaoId=" . urlencode($r_T007_Id) . '&formId=' . urlencode($formId)),
				array(uniqid(), '/ven/ven003/form/ven003FormPrincipal/', 'Mais', "&tab=mais&acaoId=" . urlencode($r_T007_Id) . '&formId=' . urlencode($formId)),
			);
		}else{
			/**
			* Cabeçalho (Versão Acima da TAB)
			*/
			$T007 = mysql_query("SELECT * FROM T007 WHERE T007_Id = '{$r_acaoId}'");
			$T007 = mysql_fetch_array($T007);
			$cabecalhoId = uniqid();
			echo '<div id="'.$cabecalhoId.'" class="contentCabecalho" style="height: 40px;"></div>';
			$actions = array(
				// identificador, url, paramentros get (opcional)
				array($cabecalhoId, '/ven/ven003/outro/ven003CabecalhoNotaFiscal/', '&acaoId=' . urlencode($r_acaoId), true),
			);
			echo gProcessaAcoes($actions);
			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
				array(uniqid(), '/ven/ven003/form/ven003FormPrincipal/', 'Cabeçalho', "&tab=principal&acaoId=" . urlencode($r_T007_Id) . '&formId=' . urlencode($formId)),
				array(uniqid(), '/ven/ven003/content/ven003ContentProdutosNF/', 'Produtos', "&acaoId=" . urlencode($r_acaoId) . "&T007_Id=" . urlencode($r_acaoId) .'&formId=' . urlencode($formId), true),
                //array(uniqid(), '/ven/ven003/content/ven003ContentServicosNF/', 'Serviços', "&acaoId=" . urlencode($r_acaoId) . "&T007_Id=" . urlencode($r_acaoId) .'&formId=' . urlencode($formId), true),
				array(uniqid(), '/ven/ven003/form/ven003FormTotais/', 'Totais', "&tab=geral&acaoId=" . urlencode($r_acaoId) . '&formId=' . urlencode($formId),false,true),
				array(uniqid(), '/ven/ven003/grid/ven003GridObservacoes/', 'Observações', "&tab=geral&acaoId=" . urlencode($r_acaoId) . '&T007_Id=' . urlencode($r_acaoId) . '&formId=' . urlencode($formId), true, true),
				array(uniqid(), '/ven/ven003/form/ven003FormPrincipal/', 'Transporte', "&tab=transporte&acaoId=" . urlencode($r_T007_Id) . '&formId=' . urlencode($formId)),
				array(uniqid(), '/ven/ven003/form/ven003FormPrincipal/', 'Adicionais', "&tab=adicionais&acaoId=" . urlencode($r_T007_Id) . '&formId=' . urlencode($formId)),
				array(uniqid(), '/ven/ven003/form/ven003FormPrincipal/', 'Exportação', "&tab=exportacao&acaoId=" . urlencode($r_T007_Id) . '&formId=' . urlencode($formId)),
				array(uniqid(), '/ven/ven003/content/ven003ContentReferenciada/', 'Referenciada', "&T007_Id=" . urlencode($r_T007_Id) . '&formId=' . urlencode($formId), true),
				array(uniqid(), '/ven/ven003/form/ven003FormPrincipal/', 'Mais', "&tab=mais&acaoId=" . urlencode($r_T007_Id) . '&formId=' . urlencode($formId))
			);
			if ($g['usuarioAtual'] == 63 || $g['nivelAlcada'] >= 4) {
				$tabsList[] = array(uniqid(), '/ven/ven003/content/ven003ContentTecnico/', 'Técnico', "&tab=geral&acaoId=" . urlencode($r_acaoId) . '&formId=' . urlencode($formId), true);
			}

			if(!empty($T007['T007_Numero_Protocolo_Nfe']) || $T007['T007_Flag_Analise_Credito'] == 1 || $T007['T007_Flag_Analise_Credito'] == 2) {
				$tabsList[] = array(uniqid(), '/ven/ven003/grid/ven003GridPagamentosTitulos/', 'Titulos', "&acaoId=" . urlencode($r_acaoId) . '&formId=' . urlencode($formId), true);
				$tabsList[] = array(uniqid(), '/ven/ven003/grid/ven003GridPagamentosBoletos/', 'Boletos', "&acaoId=" . urlencode($r_acaoId) . '&formId=' . urlencode($formId), true);
			} else {
				$tabsList[] = array(uniqid(), '/ven/ven003/content/ven003contentPagamentos/', 'Pagamentos', "&acaoId=" . urlencode($r_acaoId) . '&formId=' . urlencode($formId), true, "divRefresh(\'{$g['divId']}\')");
				if($g['C031']['ativarPagamentosPDVNota'] == 'S'){
					$tabsList[] = array(uniqid(), '/ven/ven003/content/ven003contentPagamentosPDV/', 'Caixa', "&acaoId=" . urlencode($r_acaoId) . '&formId=' . urlencode($formId), true, "divRefresh(\'{$g['divId']}\')");
				}
			}
		}

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs, true);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'ven003TabInutilizarNumeracao':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/ven/ven003/form/ven003FormInutilizar/', 'Inutilizar numeração', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
			array(uniqid(), '/ven/ven003/grid/ven003GridInutilizacoesPendentes/', 'Pendências', '&tab=geral'),
			array(uniqid(), '/ven/ven003/grid/ven003GridInutilizadas/', 'Inutilizadas', '&tab=geral'),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'ven003TabImportarXML':
		// Definição das Tabs
		$tabs = array();
		$uniqidPrincipal=uniqid();
		$tabsList = array();
		$tabsList[] = array(uniqid(), '/ven/ven003/form/ven003FormImportarXML/', 'Importar XML', "&tab=geral&acaoId=");

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'ven003TabDevolucao':
		// Definição das Tabs
		$tabs = array();
		$uniqidPrincipal=uniqid();
		$tabsList = array();
		$tabsList[] = array(uniqid(), '/ven/ven003/grid/ven003GridDevolucao/', 'Gerar nota devolução (Origem emitidas)', "&acaoId=" . urlencode($r_acaoId));
		$tabsList[] = array(uniqid(), '/ven/ven003/grid/ven003GridDevolucaoRecebidas/', 'Gerar nota de devolução (Origem recebidas)', "&acaoId=" . urlencode($r_acaoId));

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'ven003TabCCe':
		/**
		* Cabeçalho (Versão Acima da TAB)
		*/
		$cabecalhoId = uniqid();
		echo '<div id="'.$cabecalhoId.'" class="contentCabecalho" style="height: 33px;"></div>';
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($cabecalhoId, '/ven/ven003/outro/ven003CabecalhoNotaFiscal/', '&acaoId=' . urlencode($r_acaoId), true),
		);
		echo gProcessaAcoes($actions);
		// Definição das Tabs
		$tabs = array();
		$uniqidPrincipal=uniqid();
		$tabsList = array();
		$tabsList[] = array(uniqid(), '/ven/ven003/grid/ven003GridCCe/', 'CC-e', "&acaoId=" . urlencode($r_acaoId));

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'ven003TabIncluirCCe':
		// Definição das Tabs
		$tabs = array();
		$uniqidPrincipal=uniqid();
		$tabsList = array();
		$tabsList[] = array(uniqid(), '/ven/ven003/form/ven003FormIncluirCCe/', 'CC-e', "&tab=geral&acaoId=" . urlencode($r_acaoId) . "&T007_Id=" . urlencode($r_T007_Id));

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'ven003TabIncluirEventoNFe':
		$tabs = array();
		$tabsList = array();
		$tabsList[] = array(uniqid(), '/ven/ven003/form/ven003FormIncluirEventoNFe/', 'Evento', "&tab=geral&T007_Id=" . urlencode($r_T007_Id));

		echo gProcessaTabs($tabsList, $tabs);
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'ven003TabEvento112140':
		$tabs = array();
		$tabsList = array();
		$tabsList[] = array(uniqid(), '/ven/ven003/grid/ven003GridEvento112140Itens/', 'Itens não fornecidos', '&T321_Id=' . urlencode($r_T321_Id));
		echo gProcessaTabs($tabsList, $tabs);
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'ven003TabIncluirNotaReferenciada':
		$formId = uniqid();
		$g['smarty']->assign('formId', $formId);
		// Definição das Tabs
		// Definição das Tabs
		$tabs = array();
		$uniqidPrincipal=uniqid();
		$tabsList = array();
		$tabsList[] = array(uniqid(), '/ven/ven003/form/ven003FormReferenciada/', 'Nota Fiscal Eletrônica', "&tab=nfe&acaoId=" . urlencode($r_acaoId) . "&T007_Id=" . urlencode($r_T007_Id) . '&formId=' . urlencode($formId));
		$tabsList[] = array(uniqid(), '/ven/ven003/form/ven003FormReferenciada/', 'Nota Fiscal', "&tab=nf&acaoId=" . urlencode($r_acaoId) . "&T007_Id=" . urlencode($r_T007_Id) . '&formId=' . urlencode($formId));
		$tabsList[] = array(uniqid(), '/ven/ven003/form/ven003FormReferenciada/', 'Cupom Fiscal', "&tab=cupom&acaoId=" . urlencode($r_acaoId) . "&T007_Id=" . urlencode($r_T007_Id) . '&formId=' . urlencode($formId));

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'ven003TabTransmitirNotaFiscal':
		// Definição das Tabs
		$tabs = array();
		$uniqidPrincipal=uniqid();
		$tabsList = array();
		$tabsList[] = array(uniqid(), '/ven/ven003/grid/ven003GridTransmitirNotaFiscal/', 'Transmitir', "&acaoId=" . urlencode($r_acaoId) . "&naoFechaJanelaPDV=" . urlencode($r_naoFechaJanelaPDV));

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'ven003TabLogCCe':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/ven/ven003/grid/ven003GridLogCCe/', 'Log CC-e', "&tab=geral&T199_Id=" . urlencode($r_T199_Id)),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');

	break;

	case 'ven003TabCorrecaoNFCe':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/ven/ven003/form/ven003FormCorrecaoNFCe/', 'Correcao NFC-e', "&tab=geral"),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');

	break;

	case 'ven003TabCadastroCiap':
		// Definição das Tabs
		$tabs = array();
		$tab1 = uniqid();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)			
			array($tab1, '/ven/ven003/form/ven003FormCadastroCiap/', 'CIAP', '&tab=geral&T007_Id=' . urlencode($r_T007_Id)),				
		);									
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'ven003TabCadastroObservacao':
		$acaoId = isset($r_acaoId) ? $r_acaoId : false;
		$T007_Id = isset($r_T007_Id) ? $r_T007_Id : false;
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/ven/ven003/form/ven003FormCadastroObservacao/', 'Observação', '&tab=geral&acaoId=' . urlencode($acaoId) . '&T007_Id=' . urlencode($T007_Id)),
		);
		if (!empty($acaoId)) {
			$tabsList[] = array(uniqid(), '/ven/ven003/grid/ven003GridObrigacoes/', 'Outras Obrigações', '&tab=geral&acaoId=' . urlencode($acaoId) . '&T007_Id=' . urlencode($T007_Id));
		}
		echo gProcessaTabs($tabsList, $tabs);
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'ven003TabCadastroObrigacaoTributaria':
		$acaoId = isset($r_acaoId) ? $r_acaoId : false;
		$T297_Id = isset($r_T297_Id) ? $r_T297_Id : false;
		$T007_Id = isset($r_T007_Id) ? $r_T007_Id : false;
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/ven/ven003/form/ven003FormCadastroObrigacaoTributaria/', 'Obrigação Tributária', '&tab=geral&acaoId=' . urlencode($acaoId) . '&T297_Id=' . urlencode($T297_Id) . '&T007_Id=' . urlencode($T007_Id)),
		);
		echo gProcessaTabs($tabsList, $tabs);
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
}
