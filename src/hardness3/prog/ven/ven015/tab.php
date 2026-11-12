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

	case 'ven015TabNotaFiscalPDV':
		usleep(100000);
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
            array(uniqid(), '/ven/ven015/content/ven015ContentProdutosPDV/', 'Produtos', "&acaoId=" . urlencode($r_acaoId) . "&T007_Id=" . urlencode($r_acaoId) .'&formId=' . urlencode($formId), true),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'ven015TabFormCPF':
		// Definição das Tabs
		$tabs = array();
/* 		$tabsList = array(
			array(uniqid(), '/ven/ven015/form/ven015FormCPF/', 'CPF', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
			array(uniqid(), '/ven/ven015/form/ven015FormCNPJ/', 'CNPJ', '&tab=geral&acaoId=' . urlencode($r_acaoId)),

		); */
		$tabsList[] = array(uniqid(), '/ven/ven015/form/ven015FormCPF/', 'CPF', '&tab=geral&acaoId=' . urlencode($r_acaoId));
		if($g['C031']['permiteGerarNFCPessoaJuridica'] == 'S'){
			$tabsList[] = array(uniqid(), '/ven/ven015/form/ven015FormCNPJ/', 'CNPJ', '&tab=geral&acaoId=' . urlencode($r_acaoId));
		}

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'ven015TabFormVendedor':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/ven/ven015/form/ven015FormVendedor/', 'Diversos', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'ven015TabFormDAV':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/ven/ven015/form/ven015FormDAV/', 'DAV', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'ven015TabAplicarDesconto':
   	
		// Definição das Tabs
		$tabs = array();
		$tabsList = array();
		$tabsList[] = array(uniqid(), '/ven/ven001/form/ven001FormAplicarDesconto/', 'Percentual', '&tab=geral&idTabelaCabecalho=' . urlencode($r_idTabelaCabecalho) . '&tabelaCabecalho=' . urlencode($r_tabelaCabecalho) . '&tabelaProduto=' . urlencode($r_tabelaProduto) . '&PDV=true');
		$tabsList[] = array(uniqid(), '/ven/ven001/form/ven001FormAplicarDescontoValorTotal/', 'Desconto no valor total', '&tab=geral&acaoId=' . urlencode($r_idTabelaCabecalho) . '&tabelaCabecalho=' . urlencode($r_tabelaCabecalho) . '&PDV=true');
		if($g['C031']['habilitarPercentualDescontoValorTotal'] == 'S'){
			$tabsList[] = array(uniqid(), '/ven/ven001/form/ven001FormAplicarPercentualDescontoValorTotal/', 'Percentual no valor total', '&tab=geral&acaoId=' . urlencode($r_idTabelaCabecalho) . '&tabelaCabecalho=' . urlencode($r_tabelaCabecalho). '&PDV=true');
		}
	
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'ven015TabPrincipalPagamentosPDV':
   	
		// Definição das Tabs
		$tabs = array();
		$tabsList = array();
		$tabsList[] = array(uniqid(), '/ven/ven015/content/ven015contentPagamentosPDV/', 'Pagamento', '&tab=geral&acaoId=' . urlencode("{$r_acaoId}") . '&T007_Id=' . urlencode("{$r_acaoId}") . '&tabela=' . urlencode("{$r_from}"));
	
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'ven015TabDiversos':
   	
		// Definição das Tabs
		$tabs = array();
		$tabsList = array();
		$tabsList[] = array(uniqid(), '/ven/ven015/grid/ven015GridDiversos/', 'Vendas', '&tab=geral&acaoId=' . urlencode("{$r_acaoId}") . '&tabela=' . urlencode("{$r_from}"));
	
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;


}

