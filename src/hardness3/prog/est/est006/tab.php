<?php
namespace hardness;
	/**
	* Weiberlan
	* 29/11/2011
	*/

	/**
	* Ações
	*/
	switch ($g['r'][3]) {

		/**
		* Chamado em: est006Content01
		*/

		case 'est006TabPrincipal':

			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/est/est006/grid/est006GridOrdensCompra/', 'Ordens de compra', "&acaoId=" . urlencode($r_acaoId)),
  			);

			// Processa Tabs
			echo gProcessaTabs($tabsList, $tabs);

			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('tab.tpl');
		break;

		case 'est006TabIncluirOrdemCompra':
			
			$D024_Id = (isset($r_D024_Id)) ? $r_D024_Id : false;
			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
				array(uniqid(), '/est/est006/form/est006FormIncluirOrdemCompra/', 'Cadastro', "&tab=geral&acaoId=" . urlencode($_REQUEST['acaoId']) . 
																	                          '&gerarCotacaoRequisicaoTabCotacao=' . urlencode($r_gerarCotacaoRequisicaoTabCotacao) . 
																				   			  '&sql=' . urlencode($r_sql) .
																				   			  '&GerarOCEstoque=' . urlencode($r_GerarOCEstoque) .
																				   			  '&D024_Id=' . urlencode($D024_Id))
			);

			// Processa Tabs
			echo gProcessaTabs($tabsList, $tabs);

			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('tab.tpl');
		break;
		
		case 'est006TabEditarOrdemCompra':
			
			$cabecalhoId = uniqid();
			echo '<div id="'.$cabecalhoId.'" class="contentCabecalho" style="height: 38px;"></div>';
			$actions = array(
				// identificador, url, paramentros get (opcional)
				array($cabecalhoId, '/est/est006/outro/est006CabecalhoOrdemCompra/', '&T011_Id=' . urlencode($r_acaoId), true),
			);
			echo gProcessaAcoes($actions);

			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
				array(uniqid(), '/est/est006/content/est006ContentEditarOrdemCompra/', 'Produtos', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
				array(uniqid(), '/est/est006/form/est006FormAprovacao/', 'Aprovação', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
				array(uniqid(), '/est/est006/content/est006ContentEmail/', 'E-mail', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
				array(uniqid(), '/est/est006/form/est006FormTotalOrdemCompra/', 'Totais', "&tab=geral&acaoId=" . urlencode($r_acaoId),false,true),
				array(uniqid(), '/est/est006/form/est006FormIncluirOrdemCompra/', 'Cabeçalho', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
				array(uniqid(), '/est/est006/content/est006ContentPagamentoCotacao/', 'Pagamento', "&tab=geral&T011_Id=" . urlencode($r_acaoId)),
				array(uniqid(), '/est/est006/grid/est006GridContatos/', 'Contatos', "&tab=geral&T011_Id=" . urlencode($r_acaoId)),
			);

			// Processa Tabs
			echo gProcessaTabs($tabsList, $tabs);

			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('tab.tpl');
			
			/**
			 * Botões
			 */
			$botoesMid = '';
			$botoesEsq = '';
			$botoesDir = '';
			$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Fechar</button>';
			
			$g['smarty']->assign('botoesId', $g['divId']);
			$g['smarty']->assign('botoesEsq', $botoesEsq);
			$g['smarty']->assign('botoesMid', $botoesMid);
			$g['smarty']->assign('botoesDir', $botoesDir);
			$g['smarty']->display('buttons-floatBottom.tpl');
		break;

		case 'est006TabAgregados':
			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/est/est006/content/est006AgregadosOrdemCompra/', 'Agregados', "&acaoId=" . urlencode($r_acaoId)),
  			);
			// Processa Tabs
			echo gProcessaTabs($tabsList, $tabs);
			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('tab.tpl');
		break;
	}

?>
