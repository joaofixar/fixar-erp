<?php
namespace hardness;
/**
* Ações
*/
switch ($g['r'][3]) {
	
	/**
	* Grid Principal
	* Chamado em: fin001content01
	*/
	case 'fin001tab01':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/fin/fin001/grid/fin001grid01/', 'Títulos a Receber'),
			array(uniqid(), '/fin/fin001/content/fin001contentBoletosOutrosPortadores/', 'Boleto Bancário/Outros'),
			array(uniqid(), '/fin/fin001/grid/fin001gridReparcelamentos/', 'Reparcelamentos/Agrupamentos'),
			array(uniqid(), '/fin/fin001/grid/fin001GridAcrescimosDescontos/', 'Acréscimos/Descontos'),
			array(uniqid(), '/fin/fin001/grid/fin001GridCobrancas/', 'Cobranças','',false,true),
			array(uniqid(), '/fin/fin001/grid/fin001GridRemessas/', 'Remessas','',false,true),			
			array(uniqid(), '/fin/fin001/content/fin001ContentRetornoRemessas/', 'Retornos'),
			array(uniqid(), '/fin/fin001/grid/fin001gridListaCheques/', 'Cheques'),
			array(uniqid(), '/fin/fin001/content/fin001ImportarCsv/', 'Importar CSV'),
			//array(uniqid(), '/fin/fin001/grid/fin001grid09/', 'Total Conta'),
			//array(uniqid(), '/fin/fin001/grid/fin001grid10/', 'Total Diário'),
			//array(uniqid(), '/fin/fin001/grid/fin001grid11/', 'Total Portador'),
			//array(uniqid(), '/fin/fin001/grid/fin001grid12/', 'Total Cliente'),
			//array(uniqid(), '/fin/fin001/grid/fin001grid13/', 'Total Vendedor'),			
		);
/* 		if(isset($g['c029Ids'][38])){
			$tabsBoleto = array(array(uniqid(), '/fin/fin005/grid/fin005GridPrincipal/', 'Boleto Bancário'));
			array_splice($tabsList, 1, 0, $tabsBoleto);
		}	 */	
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'fin001tabBoletosOutrosPortadores':
		// Definição das Tabs
		$tabs     = array();
		$tabsList = array();

		$tabs = array();
		$tabsList = array();

		if(isset($g['c029Ids'][38])){
			$tabsList[] = array(uniqid(), '/fin/fin005/grid/fin005GridPrincipal/', 'Boleto Bancário');
		}	

		$tabsList[] = array(uniqid(), '/fin/fin001/grid/fin001GridOutrosPortadores/', 'Outros Portadores');
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'fin001TabCobrancas':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
	
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/fin/fin001/grid/fin001GridCobrancas/', 'Cobranças','',false,true),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	/**
	* Form Principal + Acrescimos e Descontos, Recebimentos e Agrupamentos (se update)
	* Chamado em: fin001content02
	*/
	case 'fin001tab02':
		/**
		* Cabeçalho (Versão Acima da TAB)
		*/
		if (isset($r_acaoId) && !empty($r_acaoId)) {
			$cabecalhoId = uniqid();
			echo '<div id="'.$cabecalhoId.'" class="contentCabecalho" style="height: 38px;"></div>';
			$actions = array(
				// identificador, url, paramentros get (opcional)
				array($cabecalhoId, '/fin/fin001/outro/fin001CabecalhoCliente/', '&acaoId=' . urlencode($r_acaoId), true),
			);
			echo gProcessaAcoes($actions);
		}

		// Form (caso seja o mesmo form em multiplas tabs)
		$formId = uniqid();
		$g['smarty']->assign('formId', $formId);
		
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/fin/fin001/form/fin001form01/', 'Cadastro', "&tab=cadastro_titulo&acaoId=" . urlencode($_REQUEST['acaoId']) . '&formId=' . urlencode($formId)),						
		);
		
		// Definição das Tabs
		// só cria as demais tabs caso seja update
		if ($_REQUEST['acaoId']) {
			$tab2Id = uniqid();
			array_push($tabsList, array($tab2Id, '/fin/fin001/content/fin001content07/', 'Acréscimo/Desconto', "&acaoId=" . urlencode($_REQUEST['acaoId']), true));
			$tab3Id = uniqid();
			//array_push($tabsList, array($tab3Id, '/fin/fin001/grid/fin001grid03/', 'Recebimentos', "os&acaoId=" . urlencode($_REQUEST['acaoId']), true));
			$tab4Id = uniqid();
			array_push($tabsList, array($tab4Id, '/fin/fin001/grid/fin001gridReparcelamentosTitulo/', 'Reparcelamentos', "&acaoId=" . urlencode($_REQUEST['acaoId']), true));
			$tab5Id = uniqid();
			//array_push($tabsList, array($tab5Id, '/fin/fin001/form/fin001form01/', 'Cheque', "&tab=cheque&acaoId=" . urlencode($_REQUEST['acaoId']) . '&formId=' . urlencode($formId), false));
			$tab6Id = uniqid();
			array_push($tabsList, array($tab6Id, '/fin/fin001/grid/fin001GridCaixaBanco/', 'Banco', "&acaoId=" . urlencode($_REQUEST['acaoId']), true));
			$tab7Id = uniqid();
			array_push($tabsList, array($tab7Id, '/fin/fin001/grid/fin001GridBoletosTitulo/', 'Boletos', "&tab=remessa&acaoId=" . urlencode($_REQUEST['acaoId']) . '&formId=' . urlencode($formId), false));
			$tab8Id = uniqid();
			array_push($tabsList, array($tab8Id, '/fin/fin001/form/fin001form01/', 'Outros', "&tab=remessa&acaoId=" . urlencode($_REQUEST['acaoId']) . '&formId=' . urlencode($formId), false));
			$tab9Id = uniqid();
			array_push($tabsList, array($tab9Id, '/fin/fin001/grid/fin001GridRetorno/', 'Retorno', "&acaoId=" . urlencode($_REQUEST['acaoId']), true));
			$tab10Id = uniqid();
			array_push($tabsList, array($tab10Id, '/fin/fin001/content/fin001ContentDocumentos/', 'Documentos', "&T002_Id=" . urlencode($_REQUEST['acaoId']), true));
			$tab11Id = uniqid();
			array_push($tabsList, array($tab11Id, '/fin/fin001/grid/fin001GridAgendaCobranca/', 'Agenda', "&acaoId=" . urlencode($_REQUEST['acaoId']), true));
			$tab12Id = uniqid();
			array_push($tabsList, array($tab12Id, '/fin/fin001/grid/fin001GridCheque/', 'Cheques', "&acaoId=" . urlencode($_REQUEST['acaoId']), true));
		}
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs, true);
		
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	/**
	* Remessa
	* Chamado em: fin001content03
	*/
	case 'fin001tab03':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
	
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/fin/fin001/form/fin001form06/', 'Remessa', "&tab=remessa"),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	/**
	* Agrupar
	* Chamado em: fin001content04
	*/
	case 'fin001tab04':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
	
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			//array(uniqid(), '/fin/fin001/form/fin001form07/', 'Agrupar', "&tab=agrupar&extra=".urlencode($r_extra)),
			array(uniqid(), '/fin/fin001/content/fin001contentAgrupar/', 'Agrupar de Títulos', "&extra=".urlencode($r_extra)),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	/**
	* Baixar
	* Chamado em: fin001content05
	*/
	case 'fin001tab05':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
	
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			//array(uniqid(), '/fin/fin001/form/fin001form08/', 'Baixar', "&tab=baixar&extra=".urlencode($r_extra)),
			array(uniqid(), '/fin/fin001/content/fin001content052rows/', 'Títulos a baixar', "&tab=baixar&extra=".urlencode($r_extra)),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;	
	
	/**
	* Reparcelar
	* Chamado em: fin001content06
	*/
	case 'fin001tab06':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
	
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/fin/fin001/form/fin001form09/', 'Reparcelar', "&tab=reparcelar&extra=".urlencode($r_extra)),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;		
	
	
	case 'fin001tabReparcelamento':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
	
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/fin/fin001/form/fin001formReparcelamento/', 'Detalhes', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
			array(uniqid(), '/fin/fin001/grid/fin001gridTitulosGeradores/', 'Títulos Geradores', "&acaoId=" . urlencode($r_acaoId)),
			array(uniqid(), '/fin/fin001/grid/fin001gridTitulosGerados/', 'Títulos Gerados',  "&acaoId=" . urlencode($r_acaoId)),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;		
	
	case 'fin001tabReparcelamentoJanela':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
	
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/fin/fin001/grid/fin001grid06/', 'Títulos', "&tab=geral&acaoId=" . urlencode($r_acaoId) . '&extra=' . urlencode($r_extra)),
			//array(uniqid(), '/fin/fin001/content/fin001ContentReparcelamento/', 'Reparcelamento', '&acaoId=' . urlencode($r_acaoId) . '&extra=' . urlencode($r_extra) . '&simular=' . urlencode($r_simular), true, true),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs, true);
		
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
		
	break;

	case 'fin001tabBaixaTitulos':

		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/fin/fin001/content/fin001content052rows/', 'Titulos a baixar', "&tab=geral&acaoId=" . urlencode($r_acaoId) .'&extra=' . urlencode($r_extra)),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs, true);
		
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'fin001TabRelatorioRetorno':
		/**
		* Cabeçalho (Versão Acima da TAB)
		*/
		$cabecalhoId = uniqid();
		echo '<div id="'.$cabecalhoId.'" class="contentCabecalho" style="height: 38px;"></div>';
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($cabecalhoId, '/fin/fin001/outro/fin001CabecalhoRetorno/', '&T037_Id=' . urlencode($r_T037_Id), true),
		);
		echo gProcessaAcoes($actions);

		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/fin/fin001/grid/fin001GridRelatorioRetorno/', 'Relatório', "&tab=geral&T037_Id=" . urlencode($r_T037_Id)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs, true);

		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'fin001TabAlterarVencimento':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		$extra = isset($r_extra) ? $r_extra : false;
	
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/fin/fin001/form/fin001FormVencimento/', 'Vencimento','&tab=geral&extra='. urlencode($extra),false,true),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'fin001TabSerasaRelato':
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/fin/fin001/form/fin001FormSerasaRelatoNormal/', 'Arquivo de Remessa Normal', "&tab=geral"),
			array(uniqid(), '/fin/fin001/form/fin001FormSerasaRelatoConciliacao/', 'Arquivo de Conciliação', "&tab=geral"),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs, true);
		
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'fin001TabAgendaCobranca':
		/**
		* Cabeçalho (Versão Acima da TAB)
		*/
		if (isset($r_acaoId) && !empty($r_acaoId)) {
			$cabecalhoId = uniqid();
			echo '<div id="'.$cabecalhoId.'" class="contentCabecalho" style="height: 38px;"></div>';
			$actions = array(
				// identificador, url, paramentros get (opcional)
				array($cabecalhoId, '/fin/fin001/outro/fin001CabecalhoCliente/', '&acaoId=' . urlencode($r_acaoId), true),
			);
			echo gProcessaAcoes($actions);
		}
		// Form (caso seja o mesmo form em multiplas tabs)
		$formId = uniqid();
		$g['smarty']->assign('formId', $formId);
		
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/fin/fin001/grid/fin001GridAgendaCobranca/', 'Contatos', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
		);
				
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs, true);
		
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'fin001TabCreditoTrocaCentral':
		if (!isset($g['c029Ids'][35]) && !gVerificaRoot()) {
			echo '<div class="msgError">Usuário sem acesso à opção Contas a Receber.</div>';
			break;
		}

		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/fin/fin001/grid/fin001GridCreditoTrocaCentral/', 'Créditos Troca'),
		);

		echo gProcessaTabs($tabsList, $tabs, true);
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'fin001TabCreditoTrocaCliente':
		if (!isset($g['c029Ids'][35]) && !gVerificaRoot()) {
			echo '<div class="msgError">Usuário sem acesso à opção Contas a Receber.</div>';
			break;
		}

		$tabs = array();
		$tabsList = array(
			array(
				uniqid(),
				'/fin/fin001/grid/fin001GridCreditoTroca/',
				'Crédito Troca',
				'&D024_Id=' . urlencode($r_D024_Id)
			),
		);

		echo gProcessaTabs($tabsList, $tabs, true);
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'fin001TabAbaterSaldoCreditoTroca':
		if (!isset($g['c029Ids'][35]) && !gVerificaRoot()) {
			echo '<div class="msgError">Usuário sem acesso à opção Contas a Receber.</div>';
			break;
		}
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/fin/fin001/grid/fin001GridAbaterSaldoCreditoTroca/', 'Títulos em aberto', '&D024_Id=' . urlencode($r_D024_Id) . '&divOrigem=' . urlencode($r_divOrigem)),
		);
		echo gProcessaTabs($tabsList, $tabs, true);
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'fin001TabCreditosClientes':
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/fin/fin001/grid/fin001GridCreditoAntecipado/', 'Crédito Antecipado', "&tab=geral"),
			array(uniqid(), '/fin/fin001/grid/fin001GridCreditoTroca/', 'Crédito Troca', "&tab=geral"),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs, true);
		
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'fin001TabGridPagamento':
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/fin/fin001/grid/fin001GridPagamento/', 'Reparcelamento', '&acaoId=' . urlencode($r_acaoId) .  "&divReload=" . urlencode($content1) . '&extra=' . urlencode($r_extra) . '&simular=' . urlencode(@$r_simular)),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs, true);
		
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'fin001TabTitulosVencidos':
		$cabecalhoId = uniqid();
		echo '<div id="'.$cabecalhoId.'" class="contentCabecalho" style="height: 38px;"></div>';
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($cabecalhoId, '/cad/cad001/outro/cad001CabecalhoCliente/', '&acaoId=' . urlencode($r_D024_Id), true),
		);
		echo gProcessaAcoes($actions);

		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/fin/fin001/grid/fin001GridTitulosVencidos/', 'Títulos', "&tab=geral&D024_Id=" . urlencode($r_D024_Id)),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs, true);
		
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'fin001TabIncluirCheque':
		$formId = uniqid();
		$g['smarty']->assign('formId', $formId);
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/fin/fin001/form/fin001FormIncluirCheque/', 'Cheques', '&tab=geral&acaoId=' . urlencode($r_acaoId) . '&T002_Id=' . urlencode($r_T002_Id) . '&formId=' . urlencode($formId)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'fin001TabConfiguracoesFinanceiro':
		$formId = uniqid();
		$g['smarty']->assign('formId', $formId);
		// Definição das Tabs
		$tabs = array();
		$tabsList = array();
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
		if(isset($g['c029Ids'][62])){
			$tabsList[] = array(uniqid(), '/cad/cad022/grid/cad022GridPortadoresReceber/', 'Portadores a Receber', "&acaoId=" . urlencode($r_acaoId));
		}
		if(isset($g['c029Ids'][60])){
			$tabsList[] = array(uniqid(), '/cad/cad021/grid/cad021GridPortadoresPagar/', 'Portadores a Pagar', "&acaoId=" . urlencode($r_acaoId));
		}
		if(isset($g['c029Ids'][168])){
			$tabsList[] = array(uniqid(), '/cad/cad014/grid/cad014GridCadastroBanco/', 'Banco', "&acaoId=" . urlencode($r_acaoId));
		}
		if(isset($g['c029Ids'][168])){
			//$tabsList[] = array(uniqid(), '/cad/cad056/grid/cad056GridBancos/', 'Bancos', "&tab=geral&acaoId=" . urlencode($r_acaoId));
		}
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'fin001TabTitulosParaEnvio':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/fin/fin001/grid/fin001GridTitulosParaEnvio/', 'Títulos', '&extra=' . urlencode($r_extra)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'fin001TabDividir':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
		    array(uniqid(), '/fin/fin001/grid/fin001GridDividir/', 'Títulos a dividir', "&tab=agrupar&extra=".urlencode($r_extra)),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
    break;

	case 'fin001TabDivisao':
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
		    array(uniqid(), '/fin/fin001/grid/fin001GridDivisao/', 'Títulos a gerar', "&tab=agrupar&extra=".urlencode($r_extra)),
		);
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
    break;

}
