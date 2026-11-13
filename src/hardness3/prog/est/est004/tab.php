<?php
namespace hardness;
/**
* Ações
*/
switch ($g['r'][3]) {
	/**
	* Chamado em: est004Content01
	*/
	case 'est004TabPrincipal':
		// Definição das Tabs
		$tabs = array();
		$tab1 = uniqid();
		$tab2 = uniqid();
		$tab3 = uniqid();
		$tab4 = uniqid();
		$tab5 = uniqid();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array($tab1, '/est/est004/grid/est004GridNotasFiscaisRecebidas/', 'Notas Fiscais Recebidas', "&acaoId=" . urlencode($r_acaoId) .  "&divReload=" . urlencode($tab2)),
			array($tab5, '/est/est004/grid/est004GridNotasFiscaisRecebidasProdutos/', 'Produtos', "&acaoId=" . urlencode($r_acaoId) .  "&divReload=" . urlencode($tab4)),
			array($tab2, '/est/est004/form/est004FormImportarXML/', 'Importar XML', "&tab=geral&acaoId=" . urlencode($r_acaoId) .  "&divReload=" . urlencode($tab1)),
			array($tab3, '/est/est004/grid/est004GridNotasFiscaisReceber/', 'Recebimentos internos', "&acaoId=" . urlencode($r_acaoId) .  "&divReload=" . urlencode($tab3)),
            //array(uniqid(), '/etc/etc006/grid/etc006Xml/', 'Recebidas por E-mail'),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'est004TabCadastroNotaFiscal':
         $GerarNFOC = (isset($r_GerarNFOC)) ? $r_GerarNFOC : false;
         $extra = (isset($r_extra)) ? $r_extra : false;
		
		$formId = uniqid();
		$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/est/est004/form/est004FormCadastroNota/', 'Principal', "&tab=principal&acaoId=" . urlencode($_REQUEST['acaoId']). '&formId=' . urlencode($formId)  . '&GerarNFOC=' . urlencode($GerarNFOC) . '&extra=' . urlencode($extra)),
			array(uniqid(), '/est/est004/form/est004FormCadastroNota/', 'ICMS', "&tab=icms&acaoId=" . urlencode($_REQUEST['acaoId']). '&formId=' . urlencode($formId)),
			array(uniqid(), '/est/est004/form/est004FormCadastroNota/', 'IPI', "&tab=ipi&acaoId=" . urlencode($_REQUEST['acaoId']). '&formId=' . urlencode($formId)),
			array(uniqid(), '/est/est004/form/est004FormCadastroNota/', 'Frete', "&tab=frete&acaoId=" . urlencode($_REQUEST['acaoId']). '&formId=' . urlencode($formId)),
			array(uniqid(), '/est/est004/form/est004FormCadastroNota/', 'Serviços', "&tab=servicos&acaoId=" . urlencode($_REQUEST['acaoId']) . '&formId=' . urlencode($formId)),
			array(uniqid(), '/est/est004/form/est004FormCadastroNota/', 'Outros', "&tab=outros&acaoId=" . urlencode($_REQUEST['acaoId']) . '&formId=' . urlencode($formId)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs, true);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'est004TabEditarNotaFiscal':
		/**
		* Cabeçalho (Versão Acima da TAB)
		*/
		$cabecalhoId = uniqid();
		echo '<div id="'.$cabecalhoId.'" class="contentCabecalho" style="height: 38px;"></div>';
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($cabecalhoId, '/est/est004/outro/est004CabecalhoNotaFiscal/', '&acaoId=' . urlencode($r_acaoId), true),
		);
		echo gProcessaAcoes($actions);

		$sqlT013 = mysql_query("SELECT * FROM T013 WHERE T013_Id = '{$r_acaoId}'");
		$T013 = mysql_fetch_array($sqlT013);

		$formId = uniqid();
		$tabFreteId = uniqid();
		$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/est/est004/content/est004ContentProdutosNota/', 'Produtos', "&acaoId=" . urlencode($_REQUEST['acaoId']) . '&formId=' . urlencode($formId), true),
			array(uniqid(), '/est/est004/form/est004FormCadastroNota/', 'Cabeçalho', "&tab=principal&acaoId=" . urlencode($_REQUEST['acaoId']) . '&formId=' . urlencode($formId) . '&tabFreteId=' . urlencode($tabFreteId)),
			array(uniqid(), '/est/est004/form/est004FormCadastroNota/', 'ICMS', "&tab=icms&acaoId=" . urlencode($_REQUEST['acaoId']) . '&formId=' . urlencode($formId)),
			array(uniqid(), '/est/est004/form/est004FormCadastroNota/', 'IPI', "&tab=ipi&acaoId=" . urlencode($_REQUEST['acaoId']) . '&formId=' . urlencode($formId)),
			array($tabFreteId, '/est/est004/form/est004FormCadastroNota/', 'Frete', "&tab=frete&acaoId=" . urlencode($_REQUEST['acaoId']) . '&formId=' . urlencode($formId)),
			array(uniqid(), '/est/est004/form/est004FormCadastroNota/', 'Serviços', "&tab=servicos&acaoId=" . urlencode($_REQUEST['acaoId']) . '&formId=' . urlencode($formId)),
			array(uniqid(), '/est/est004/form/est004FormCadastroNota/', 'Outros', "&tab=outros&acaoId=" . urlencode($_REQUEST['acaoId']) . '&formId=' . urlencode($formId)),
		);
		$tabsList[] = array(uniqid(), '/est/est004/content/est004ContentPagamentoNota/', 'Pagamento', "&acaoId=" . urlencode($_REQUEST['acaoId']) . '&formId=' . urlencode($formId), true);
		$tabsList[] = array(uniqid(), '/est/est004/grid/est004GridTitulosNota/', 'Contas a Pagar', "&acaoId=" . urlencode($_REQUEST['acaoId']) . '&formId=' . urlencode($formId), true);
		$tabsList[] = array(uniqid(), '/est/est004/form/est004FormTotais/', 'Totais', "&tab=geral&acaoId=" . urlencode($_REQUEST['acaoId']) . '&formId=' . urlencode($formId), true,true);
		if($T013['T013_Flag_Travar_Nota'] == 'S'){
			$tabsList[] = array(uniqid(), '/est/est004/grid/est004gridOrdemCompra/', 'Ordens de Compra', "&tab=geral&acaoId=" . urlencode($_REQUEST['acaoId']) . '&formId=' . urlencode($formId), true,true);
		}
		$tabsList[] = array(uniqid(), '/est/est004/grid/est004GridObservacoes/', 'Observações', "&tab=geral&acaoId=" . urlencode($_REQUEST['acaoId']) . '&formId=' . urlencode($formId), true,true);

		if($g['c029Ids'][275]){
			$D001 = mysql_query("SELECT T014_Id
							   	   FROM T014
						      LEFT JOIN D009 ON D009_Id = T014_D009_Id
						      LEFT JOIN D049 ON D049_Id = D009_D049_Id
						      LEFT JOIN D001 ON D001_Id = D049_D001_Id
							      WHERE T014_T013_Id = {$r_acaoId}
								    AND D001_Flag_Tipo_Item = '08' LIMIT 1;");
			if (mysql_num_rows($D001) > 0) { // Se houver pelo menos 1 registro retornado da query acima retorna a tela para cadastro do CIAP que vai para o SPED FISCAL.
				$tabsList[] = array(uniqid(), '/est/est004/grid/est004GridCiap/', 'CIAP', "&tab=geral&acaoId=" . urlencode($_REQUEST['acaoId']) . "&T013_Id=" . urlencode($_REQUEST['acaoId']) .'&formId=' . urlencode($formId), true,true);
			}
		}


		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs, true);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'est004TabAssociarItem':

		// Definição das Tabs
		$tabs = array();
		$tab1 = uniqid();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array($tab1, '/est/est004/form/est004FormAssociarItem/', 'Item', "&tab=geral&T014_Id=" . urlencode($r_T014_Id)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'est004TabAssociarItemProduto':

		// Definição das Tabs
		$tabs = array();
		$tab1 = uniqid();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array($tab1, '/est/est004/form/est004FormAssociarItemProduto/', 'Item', "&tab=geral&T014_Id=" . urlencode($r_T014_Id)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'est004TabImportarOC':

		// Definição das Tabs
		$tabs = array();
		$tab1 = uniqid();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array($tab1, '/est/est004/grid/est004GridImportarOC/', "Itens nota fiscal {$r_T013_Id}", "&tab=geral&T013_Id=" . urlencode($r_T013_Id)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'est004TabImportarOCNovo':

		$r_acaoId = $r_T013_Id;

		$cabecalhoId = uniqid();
		echo '<div id="'.$cabecalhoId.'" class="contentCabecalho" style="height: 49px;"></div>';
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($cabecalhoId, '/est/est004/outro/est004CabecalhoNotaFiscalOC/', '&acaoId=' . urlencode($r_acaoId), true),
		);
		echo gProcessaAcoes($actions);

		// Definição das Tabs
		$tabs = array();
		$tab1 = uniqid();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array($tab1, '/est/est004/grid/est004GridImportarOCNovo/', "Produtos", "&tab=geral&T013_Id=" . urlencode($r_T013_Id)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'est004TabImportarOS':

		// Definição das Tabs
		$tabs = array();
		$tab1 = uniqid();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array($tab1, '/est/est004/grid/est004GridImportarOS/', 'Itens', "&tab=geral&T013_Id=" . urlencode($r_T013_Id)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'est004TabImportarItensNota':

		// Definição das Tabs
		$tabs = array();
		$tab1 = uniqid();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array($tab1, '/est/est004/grid/est004GridImportarItensNota/', 'Itens', "&tab=geral&T013_Id=" . urlencode($r_T013_Id) . '&D024_Id=' . urlencode($r_D024_Id)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'est004TabCancelarNotaEntrada':
		// Definição das Tabs
		$tabs = array();
		$tab1 = uniqid();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array($tab1, '/est/est004/form/est004FormCancelarNotaEntrada/', 'Motivo cancelamento', "&tab=geral&T013_Id=" . urlencode($r_T013_Id)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'est004TabImportarXml':
		// Definição das Tabs
		$tabs = array();
		$uniqidPrincipal=uniqid();
		$tabsList = array();
		$tabsList[] = array(uniqid(), '/est/est004/form/est004FormImportarXML/', 'Importar XML', "&tab=geral");

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'est004TabAssociarItemNovo':
		/**
		* Cabeçalho (Versão Acima da TAB)
		*/
		$cabecalhoId = uniqid();
		echo '<div id="'.$cabecalhoId.'" class="contentCabecalho" style="height: 38px;"></div>';
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($cabecalhoId, '/est/est004/outro/est004CabecalhoAssociarItemNovo/', '&acaoId=' . urlencode($r_acaoId), true),
		);
		echo gProcessaAcoes($actions);

		// Definição das Tabs
		$tabs = array();
		$tab1 = uniqid();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array($tab1, '/est/est004/grid/est004GridAssociarItemNovo/', 'Itens', '&acaoId=' . urlencode($r_acaoId)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'est004TabCadastroObservacao':
		// Definição das Tabs
		$tabs = array();
		$tab1 = uniqid();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array($tab1, '/est/est004/form/est004FormCadastroObservacao/', 'Observação', "&tab=geral&acaoId=" . urlencode($r_acaoId) .  "&T013_Id=" . urlencode($r_T013_Id)),				
		);					
		
		if(!empty($r_acaoId))
			$tabsList[] = array(uniqid(), '/est/est004/grid/est004GridObrigacoes/', 'Outras Obrigações', "&tab=geral&acaoId=" . urlencode($r_acaoId) .  "&T013_Id=" . urlencode($r_T013_Id));
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'est004TabCadastroObrigacaoTributaria':
		// Definição das Tabs
		$tabs = array();
		$tab1 = uniqid();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)			
			array($tab1, '/est/est004/form/est004FormCadastroObrigacaoTributaria/', 'Obrigação Tributária', '&tab=geral&acaoId='  . urlencode($r_acaoId)  . '&T297_Id='. urlencode($r_T297_Id)  . '&T013_Id=' . urlencode($r_T013_Id)),				

		);									
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'est004TabCadastroCiap':
		// Definição das Tabs
		$tabs = array();
		$tab1 = uniqid();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)			
			array($tab1, '/est/est004/form/est004FormCadastroCiap/', 'CIAP', '&tab=geral&acaoId='  . urlencode($r_acaoId) . '&T013_Id=' . urlencode($r_T013_Id)),				

		);									
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
}

