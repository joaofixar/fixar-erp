<?php
namespace hardness;
/**
* Ações
*/
switch ($g['r'][3]) {

	/**
	* Chamado em: est002Content01
	*/

	case 'est002TabPrincipal':
		echo $r_acaoId;

		// Definição das Tabs
		$tabs = array();
		$tabsList = array();
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
		if($g['C031']['modeloExpedicao'] == '1') {
			if ($g['C031']['expedicaoDeMercadoria'] == 'normal') {
				$tabsList[] = array(uniqid(), '/est/est002/grid/est002GridImpressao', 'Impressão', "&acaoId=" . urlencode($_REQUEST['acaoId']),false,true);
				if($g['C031']['desabilitarSeparacao']=='N'){
					$tabsList[] = array(uniqid(), '/est/est002/grid/est002GridSeparacao/', 'Separação', "&acaoId=" . urlencode($_REQUEST['acaoId']),false,true);
				}
			} else {
				$tabsList[] = array(uniqid(), '/est/est002/grid/est002GridImpressao', 'Aguardando Separação', "&acaoId=" . urlencode($_REQUEST['acaoId']),false,true);
				$tabsList[] = array(uniqid(), '/est/est002/grid/est002GridSeparacao/', 'Em Separação', "&acaoId=" . urlencode($_REQUEST['acaoId']),false,true);
			}
			if($g['C031']['desabilitarConferencia']=='N'){
				$tabsList[] = array(uniqid(), '/est/est002/grid/est002GridConferencia/', 'Conferência', "&acaoId=" . urlencode($_REQUEST['acaoId']),false,true);
			}
		} else if($g['C031']['modeloExpedicao'] == '2') {
			$tabsList[] = array(uniqid(), '/est/est002/content/est002ContentSeparacaoComercial/', 'Separação Comercial', "&acaoId=" . urlencode($_REQUEST['acaoId']),false,true);
			$tabsList[] = array(uniqid(), '/est/est002/content/est002ContentSeparacaoBalcao/', 'Separação Balcão', "&acaoId=" . urlencode($_REQUEST['acaoId']),false,true);
			$tabsList[] = array(uniqid(), '/est/est002/grid/est002GridConferenciaComercial/', 'Conferência Comercial', "&acaoId=" . urlencode($_REQUEST['acaoId']),false,true);
			$tabsList[] = array(uniqid(), '/est/est002/grid/est002GridConferenciaBalcao/', 'Conferência Balcão', "&acaoId=" . urlencode($_REQUEST['acaoId']),false,true);
		}
//$tabsList[] = array(uniqid(), '/ven/ven012/grid/ven012GridFilaDeEnviar/', 'Enviar NFe para Sefaz', "&acaoId=" . urlencode($_REQUEST['acaoId']),'',true);
		//Abas de emissão da nota fiscal
		$tabsList[] = array(uniqid(), '/ven/ven012/grid/ven012GridPrincipal/', 'Gerar Notas Fiscais', "&acaoId=" . urlencode($_REQUEST['acaoId']),'',true);
		$tabsList[] = array(uniqid(), '/ven/ven012/content/ven012ContentFilaDeEnvioNovo/', 'Enviar NFe para Sefaz', "&acaoId=" . urlencode($_REQUEST['acaoId']),'',true);

		//array(uniqid(), '/est/est002/grid/est002GridEmissaoNF/', 'Emissão NF', "&acaoId=" . urlencode($_REQUEST['acaoId']),false,true),
		$tabsList[] = array(uniqid(), '/est/est002/grid/est002GridAguardandoEntrega/', 'Aguardando Coleta', "&acaoId=" . urlencode($_REQUEST['acaoId']),false,true);
		if(isset($g['c029Ids'][242])){
			$tabsList[] = array(uniqid(), '/fis/fis012/content/fis012ContentPrincipal/', 'MDF-e');
		}
		$tabsList[] = array(uniqid(), '/est/est002/grid/est002GridColetados/', 'Coletados', "&acaoId=" . urlencode($_REQUEST['acaoId']),false,true);
		//$tabsList[] = array(uniqid(), '/est/est002/grid/est002GridEntregaInterna/', 'Entrega Interna', "&acaoId=" . urlencode($_REQUEST['acaoId']),false,true);
		//array(uniqid(), '/est/est002/content/est002ContentEstoque/', 'Estoque', "&acaoId=" . urlencode($_REQUEST['acaoId']),false,true),
		//array(uniqid(), '/est/est002/grid/est002GridAguardandoLiberacao/', 'Aguardando Liberação', "&acaoId=" . urlencode($_REQUEST['acaoId']),false,true),
		//array(uniqid(), '/est/est003/grid/est003GridNaoLiberado/', 'Não Liberado', "&acaoId=" . urlencode($_REQUEST['acaoId']),false,true),
		//$tabsList[] = array(uniqid(), '/est/est003/grid/est003GridAguardandoProdCompra', 'Aguardando Prod/Compra', "&acaoId=" . urlencode($_REQUEST['acaoId']),false,true);
		$tabsList[] = array(uniqid(), '/est/est003/grid/est003GridDivergencias/', 'Divergências', "&acaoId=" . urlencode($_REQUEST['acaoId']),false,true);
		//$tabsList[] = array(uniqid(), '/est/est002/grid/est002GridEmprestimo/', 'Empréstimo', "&acaoId=" . urlencode($_REQUEST['acaoId']),false,true);
		//$tabsList[] = array(uniqid(), '/est/est003/grid/est003GridListaBox/', 'Lista Box', "&acaoId=" . urlencode($_REQUEST['acaoId']),false,true);
		//$tabsList[] = array(uniqid(), '/est/est003/content/est003Content/', 'Estoque', "&acaoId=" . urlencode($_REQUEST['acaoId']),false,true);
		$tabsList[] = array(uniqid(), '/est/est002/grid/est002GridGeral/', 'Geral', "&acaoId=" . urlencode($_REQUEST['acaoId']),false,true);
		//$tabsList[] = array(uniqid(), '/est/est002/grid/est002GridControleProduto/', 'Controle do Produto', "&acaoId=" . urlencode($_REQUEST['acaoId']),false,true);
		//$tabsList[] = array(uniqid(), '/est/est002/grid/est002GridSeparacaoProduto/', 'Produtos em Separação', "&acaoId=" . urlencode($_REQUEST['acaoId']),false,true);
		$tabsList[] = array(uniqid(), '/est/est002/grid/est002GridPedidosRetornados/', 'Pedidos Retornados', "&acaoId=" . urlencode($_REQUEST['acaoId']),false,true);
		if(isset($g['c029Ids'][38])){
			$tabsList[] = array(uniqid(), '/fin/fin005/grid/fin005GridPrincipal/', 'Boleto Bancário');
		}
		$tabsList[] = array(uniqid(), '/est/est002/grid/est002GridPortalPostal/', 'Correios');

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'est002TabProdutosEmSeparacao':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/est/est002/grid/est002GridProdutosEmSeparacao/', 'Produtos Em Separação', "&acaoId=" . urlencode($_REQUEST['acaoId'])),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'est002TabPedido':

		// Definição das Tabs
		$tabs = array();
		$tabsList = array();
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
		$tabsList[] = array(uniqid(), '/est/est002/grid/est002GridProdutosPedido/', 'Produtos', "&acaoId=" . urlencode($_REQUEST['acaoId']));
		if(isset($g['c029Ids'][230])){
			$tabsList[] = array(uniqid(), '/est/est002/form/est002FormRomaneio/', 'Romaneio', "&tab=geral&acaoId=" . urlencode($_REQUEST['acaoId']));
		}		

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'est002TabEtiquetas':

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
		array(uniqid(), '/est/est002/grid/est002GridEtiquetas/', 'Etiquetas', "&acaoId=" . urlencode($_REQUEST['acaoId'])),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'est002TabEntregaInterna':

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
		array(uniqid(), '/est/est002/grid/est002GridProdutosEntregaInterna/', 'Produtos a entregar', "&acaoId=" . urlencode($_REQUEST['acaoId'])),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'est002TabSelecionarFuncionario':

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
		array(uniqid(), '/est/est002/form/est002FormSelecionarFuncionario/', 'Devolução', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;		
	
	case 'est002TabHistoricoEntregaInterna':

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
		array(uniqid(), '/est/est002/grid/est002GridHistoricoEntregaInterna/', 'Entregas do pedido', "&acaoId=" . urlencode($_REQUEST['acaoId'])),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;			
	
	case 'est002TabEstoque':

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad020/grid/cad020lista/', 'Produtos', "&acaoId=" . urlencode($_REQUEST['acaoId'])),
			array(uniqid(), '/cad/cad019/grid/cad019GridLocacao/', 'Locações', "&acaoId=" . urlencode($_REQUEST['acaoId'])),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'est002TabConfirmarEmprestimo':

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/est/est002/form/est002FormConfirmarEmprestimo/', 'Devolução', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'est002TabCadastrarEmprestimo':

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/est/est002/form/est002FormCadastrarEmprestimo/', 'Inclusão', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'est002TabSeparacaoPedido':

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/est/est002/content/est002SeparacaoPedido2Rows/', 'Separação', '&tab=geral&acaoId=' . urlencode($r_acaoId) . '&T005_Id=' . urlencode($r_T005_Id)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'est002TabFaturamentoPedido':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/est/est002/content/est002ContentFaturamentoPedido/', 'Conferência', '&tab=geral&acaoId=' . urlencode($r_acaoId) . '&T005_Id=' . urlencode($r_T005_Id)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'est002SeparacaoPorLocacao':
		/**
		* Cabeçalho (Versão Acima da TAB)
		*/
		$cabecalhoId = uniqid();
		echo '<div id="'.$cabecalhoId.'" class="contentCabecalho" style="height: 38px;"></div>';
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($cabecalhoId, '/est/est002/outro/est002SeparacaoPorLocacaoCabecalho/', '&acaoId=' . urlencode($r_acaoId), true),
		);
		echo gProcessaAcoes($actions);
	
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/est/est002/content/est002SeparacaoPorLocacao2rows/', 'Separação', "&acaoId=" . urlencode($_REQUEST['acaoId'])),			
		);
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'est002TabSeparacaoBalcao':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array();
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
		$tabsList[] = array(uniqid(), '/est/est002/grid/est002GridSeparacaoBalcao/', 'Pedidos', "&acaoId=" . urlencode($_REQUEST['acaoId']),false,true);
		$tabsList[] = array(uniqid(), '/est/est002/grid/est002GridSeparacaoBalcaoProduto/', 'Produtos', "&acaoId=" . urlencode($_REQUEST['acaoId']),false,true);
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'est002TabSeparacaoComercial':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array();
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
		$tabsList[] = array(uniqid(), '/est/est002/grid/est002GridSeparacaoComercial/', 'Pedidos', "&acaoId=" . urlencode($_REQUEST['acaoId']),false,true);
		$tabsList[] = array(uniqid(), '/est/est002/grid/est002GridSeparacaoComercialProduto/', 'Produtos', "&acaoId=" . urlencode($_REQUEST['acaoId']),false,true);
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'est002TabCadastroPacote':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array();
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
		$tabsList[] = array(uniqid(), '/est/est002/form/est002FormCadastroPacote/', 'Dados', "&tab=geral&acaoId=" . urlencode($r_acaoId), false, true);
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'est002VolumesConferencia':
		/**
		* Cabeçalho (Versão Acima da TAB)
		*/
		$cabecalhoId = uniqid();
		echo '<div id="'.$cabecalhoId.'" class="contentCabecalho" style="height: 38px;"></div>';
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($cabecalhoId, '/est/est002/outro/est002VolumesConferenciaCabecalho/', '&acaoId=' . urlencode($r_acaoId), true),
		);
		echo gProcessaAcoes($actions);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/est/est002/content/est002VolumesConferenciaContent/', 'Conferência', "&acaoId=" . urlencode($_REQUEST['acaoId']) . '&divJanela=' . urlencode($_REQUEST['divJanela'])),
			array(uniqid(), '/est/est002/content/est002VolumesConsultaContent/',    'Volumes',     "&acaoId=" . urlencode($_REQUEST['acaoId']) . '&divJanela=' . urlencode($_REQUEST['divJanela'])),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'est002VolumesConferenciaFinalizarTab':
		// Definição das Tabs
		$tabs     = array();
		$tabsList = array();
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
		$tabsList[] = array(uniqid(), '/est/est002/form/est002VolumesConferenciaFinalizar/', 'Dados', "&tab=geral&acaoId=" . urlencode($r_acaoId) . '&divGrid=' . urlencode($_REQUEST['divGrid']), false, true);
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
}
