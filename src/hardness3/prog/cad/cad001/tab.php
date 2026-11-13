<?php
namespace hardness;
/**
* Ações
*/
switch ($g['r'][3]) {
	 case 'cad001tabPrincipal':
		 	
		// Form (caso seja o mesmo form em multiplas tabs)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);

		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad001/grid/cad001gridPrincipal/', 'Clientes/Fornecedores'),
			array(uniqid(), '/cad/cad001/grid/cad001gridTrabalho/', 'Trabalho'),
			array(uniqid(), '/cad/cad001/grid/cad001gridClientesInativos/', 'Inativos'),
			array(uniqid(), '/cad/cad001/grid/cad001gridClientesContatos/', 'Contatos'),
			array(uniqid(), '/cad/cad001/content/cad001ImportarCSV/', 'Importar CSV'),
		);

		// Processa Tabs
		$g['conteudo']['pagina'] = gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'cad001tabCadastro':
		/**
		* Cabeçalho (Versão Acima da TAB)
		*/
		if (isset($r_acaoId) && !empty($r_acaoId)) {
			$cabecalhoId = uniqid();
			echo '<div id="'.$cabecalhoId.'" class="contentCabecalho" style="height: 38px;"></div>';
			$actions = array(
				// identificador, url, paramentros get (opcional)
				array($cabecalhoId, '/cad/cad001/outro/cad001CabecalhoCliente/', '&acaoId=' . urlencode($r_acaoId), true),
			);
			echo gProcessaAcoes($actions);
		}
		
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado) // '&formId=' . urlencode($formId)
		$formId = uniqid();
		$g['smarty']->assign('formId', $formId);
		$idTabContatos = uniqid();
		$idTabEnderecos = uniqid();
		$tabs = array();
		$tabsList = array();
	    $tabsList[] = array(uniqid(), '/cad/cad001/form/cad001FormCadastro/', 'Cadastro', "&tab=cadastro&acaoId=" . urlencode($_REQUEST['acaoId']) . '&formId=' . urlencode($formId));
		if (isset($g['c029Ids'][103])) {
			$tabsList[] = array(uniqid(), '/cad/cad001/form/cad001FormCadastro/', 'Comercial', "&tab=comercial&acaoId=" . urlencode($_REQUEST['acaoId']) . '&formId=' . urlencode($formId));
			$tabsList[] = array(uniqid(), '/cad/cad001/form/cad001FormCadastro/', 'Financeiro',  "&tab=financeiro&acaoId=" . urlencode($_REQUEST['acaoId']) . '&formId=' . urlencode($formId));
			$tabsList[] = array(uniqid(), '/cad/cad001/form/cad001FormCadastro/', 'Fiscal', "&tab=fiscal&acaoId=" . urlencode($_REQUEST['acaoId']) . '&formId=' . urlencode($formId));
		}
		if(!empty($r_acaoId)){
			$tabsList[] = array($idTabContatos , '/cad/cad001/content/cad001contentContatos/', 'Contatos', "&tab=geral&D024_Id=" . urlencode($_REQUEST['acaoId']), true);
			$tabsList[] = array($idTabEnderecos, '/cad/cad001/grid/cad001GridEnderecos/', 'Endereços', "&acaoId=" . urlencode($_REQUEST['acaoId']));
			if (isset($g['c029Ids'][103])) {
				//$tabsList[] = array(uniqid(), '/cad/cad001/content/cad001ContentReferencias/', 'Referências', "&tab=geral&D024_Id=" . urlencode($_REQUEST['acaoId']), true);
				//$tabsList[] = array(uniqid(), '/cad/cad001/form/cad001FormCadastro/', 'Cobrança', "&tab=cobranca&acaoId=" . urlencode($_REQUEST['acaoId']) . '&formId=' . urlencode($formId));
				//$tabsList[] = array(uniqid(), '/cad/cad001/form/cad001FormCadastro/', 'Entrega', "&tab=entrega&acaoId=" . urlencode($_REQUEST['acaoId']) . '&formId=' . urlencode($formId));
				//$tabsList[] = array(uniqid(), '/cad/cad001/form/cad001FormCadastro/', 'Fiscal', "&tab=fiscal&acaoId=" . urlencode($_REQUEST['acaoId']) . '&formId=' . urlencode($formId));
				//$tabsList[] = array(uniqid(), '/cad/cad001/form/cad001FormCadastro/', 'Comercial', "&tab=comercial&acaoId=" . urlencode($_REQUEST['acaoId']) . '&formId=' . urlencode($formId));
				//$tabsList[] = array(uniqid(), '/cad/cad001/form/cad001FormCadastro/', 'Financeiro',  "&tab=financeiro&acaoId=" . urlencode($_REQUEST['acaoId']) . '&formId=' . urlencode($formId));
				//$tabsList[] = array(uniqid(), '/cad/cad001/content/cad001contentVeiculos/', $g['conf']->altText('Veículos'), "&tab=geral&D024_Id=" . urlencode($_REQUEST['acaoId']), true);
				//$tabsList[] = array(uniqid(), '/cad/cad001/content/cad001contentEmail/', 'E-Mail', "&tab=geral&acaoId=" . urlencode($_REQUEST['acaoId']), true);
				$tabsList[] = array(uniqid(), '/cad/cad001/content/cad001ContentDocumentos/', 'Documentos', "&tab=geral&acaoId=" . urlencode($_REQUEST['acaoId']). "&D024_Id=" . urlencode($_REQUEST['acaoId']), true)	;
			}
			$tabsList[] = array(uniqid(), '/cad/cad001/grid/cad001GridAgenda/', 'Agenda', "&acaoId=" . urlencode($_REQUEST['acaoId']). "&D024_Id=" . urlencode($_REQUEST['acaoId']), true);
			$tabsList[] = array(uniqid(), '/fin/fin001/content/fin001ContentCreditosClientes/', 'Créditos', "&D024_Id=" . urlencode($_REQUEST['acaoId']), true);
			$tabsList[] = array(uniqid(), '/cad/cad001/outro/cad001MostraSintegra/', 'Sintegra',  "&acaoId=" . urlencode($_REQUEST['acaoId']));
		}
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs, true);



		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
		
		// Quando vier do botão (Incluir Veículos, diretamente no combo)
		if (!empty($r_acaoId) && isset($r_tab) && $r_tab == 'veiculos') {
			echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					setTimeout(function() {
						$('#{$g['divId']} .tabs').tabs('select', 9);
					}, 100);
				} </script></div>
EOT;
		}
		
		// Quando vier do botão (Incluir Contatos, diretamente no combo)
		if (!empty($r_acaoId) && isset($r_tab) && $r_tab == 'contatos') {
			echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					setTimeout(function() {
						$('#{$g['divId']} .tabs').tabs('select', '#{$idTabContatos}');
					}, 100);
				} </script></div>
EOT;
		}		

		// Quando vier do botão (Incluir Enderecos, diretamente no combo)
		if (!empty($r_acaoId) && isset($r_tab) && $r_tab == 'enderecos') {
			echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					setTimeout(function() {
						$('#{$g['divId']} .tabs').tabs('select', '#{$idTabEnderecos}');
					}, 100);
				} </script></div>
	EOT;
			}	
		break;

		case 'cad001tabClienteCRMVendedor':
		/**
		* Cabeçalho (Versão Acima da TAB)
		*/
		if (isset($r_acaoId) && !empty($r_acaoId)) {
			$cabecalhoId = uniqid();
			echo '<div id="'.$cabecalhoId.'" class="contentCabecalho" style="height: 38px;"></div>';
			$actions = array(
				// identificador, url, paramentros get (opcional)
				array($cabecalhoId, '/cad/cad001/outro/cad001CabecalhoCliente/', '&acaoId=' . urlencode($r_acaoId), true),
			);
			echo gProcessaAcoes($actions);
		}
		
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado) // '&formId=' . urlencode($formId)
		$formId = uniqid();
		$g['smarty']->assign('formId', $formId);
		
		$tabs = array();

		$tabsList = array(	
			array(uniqid(), '/cad/cad001/form/cad001FormClienteCRMVendedor/', 'Cadastro', "&tab=cadastro&acaoId=" . urlencode($_REQUEST['acaoId']) . '&formId=' . urlencode($formId)),
			array(uniqid(), '/cad/cad001/form/cad001FormClienteCRMVendedor/', 'Cobrança', "&tab=cobranca&acaoId=" . urlencode($_REQUEST['acaoId']) . '&formId=' . urlencode($formId)),
			array(uniqid(), '/cad/cad001/form/cad001FormClienteCRMVendedor/', 'Entrega', "&tab=entrega&acaoId=" . urlencode($_REQUEST['acaoId']) . '&formId=' . urlencode($formId)),
			array(uniqid(), '/cad/cad001/form/cad001FormClienteCRMVendedor/', 'Fiscal', "&tab=fiscal&acaoId=" . urlencode($_REQUEST['acaoId']) . '&formId=' . urlencode($formId)),
			array(uniqid(), '/cad/cad001/form/cad001FormClienteCRMVendedor/', 'Comercial', "&tab=comercial&acaoId=" . urlencode($_REQUEST['acaoId']) . '&formId=' . urlencode($formId)),
			array(uniqid(), '/cad/cad001/form/cad001FormClienteCRMVendedor/', 'Financeiro',  "&tab=financeiro&acaoId=" . urlencode($_REQUEST['acaoId']) . '&formId=' . urlencode($formId)),
		);
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs, true);

		// Carrega template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
		
		// Quando vier do botão (Incluir Veículos, diretamente no combo)
		if (!empty($r_acaoId) && isset($r_tab) && $r_tab == 'veiculos') {
			echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					setTimeout(function() {
						$('#{$g['divId']} .tabs').tabs('select', 9);
					}, 100);
				} </script></div>
EOT;
		}
		
		// Quando vier do botão (Incluir Contatos, diretamente no combo)
		if (!empty($r_acaoId) && isset($r_tab) && $r_tab == 'contatos') {
			echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					setTimeout(function() {
						$('#{$g['divId']} .tabs').tabs('select', 2);
					}, 100);
				} </script></div>
EOT;
		}		
		
		break;
		
		case 'cad001TabAgenda':
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

			$formId = uniqid();
			$g['smarty']->assign('formId', $formId);
			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
				array(uniqid(), '/cad/cad001/form/cad001FormAgenda/', 'Agenda', "&tab=geral&acaoId=" . urlencode($r_acaoId) . '&formId=' . urlencode($formId)),
				array(uniqid(), '/cad/cad001/form/cad001FormAgenda/', 'Associações', "&tab=associacoes&acaoId=" . urlencode($r_acaoId) . '&formId=' . urlencode($formId)),
			);

			// Processa Tabs
			echo gProcessaTabs($tabsList, $tabs);

			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('tab.tpl');
		break;
			
		case 'cad001TabDocumentos':
			// Form (caso seja o mesmo form em multiplas tabs)
			//$formId = uniqid();
			//$g['smarty']->assign('formId', $formId);

			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad001/content/cad001ContentDocumentos/', 'Documentos', '&D024_Id=' . urlencode($r_D024_Id)),
			);

			// Processa Tabs
			echo gProcessaTabs($tabsList, $tabs);

			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('tab.tpl');
		break;
		
		case 'cad045TabCadastroVeiculos':
		// Form (caso seja o mesmo form em multiplas tabs - raramente utilizado)
		//$formId = uniqid();
		//$g['smarty']->assign('formId', $formId);
		
		// Definição das Tabs
		$tabs = array();
		if(empty($r_acaoId)){
			$tabsList = array(
				// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
				array(uniqid(), '/cad/cad001/form/cad001FormVeiculos/', 'Cabeçalho', '&tab=geral&acaoId=' .  '&naoRefreshParent=' . urlencode($r_naoRefreshParent). urlencode($r_acaoId) . '&D024_Id=' . urlencode($r_D024_Id)),
			);
		}else{
			$tabsList = array(
				// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
				array(uniqid(), '/cad/cad001/form/cad001FormVeiculos/', 'Cabeçalho', '&tab=geral&acaoId=' . urlencode($r_acaoId) . '&D024_Id=' . urlencode($r_D024_Id)),
				array(uniqid(), '/cad/cad001/grid/cad001GridVeiculosEspecificacoes/', 'Especificações', '&acaoId=' . urlencode($r_acaoId)),
				array(uniqid(), '/cad/cad001/content/cad001ContentDocumentosVeiculos/', 'Documentos', '&acaoId=' . urlencode($r_acaoId)),
			);
		}
		
		
		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);
		
		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
	
	case 'cad001TabPreCadastrarCliente':
		 	
         $formId = uniqid();
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
		array(uniqid(), '/cad/cad001/form/cad001FormPreCadastrarCliente/', 'Pré-Cadastro', '&tab=cadastro&formId=' . urlencode($formId) . '&acaoId='. urlencode($r_acaoId)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'cad001TabAlterarContato':
		 	
		$formId = uniqid();
	   // Definição das Tabs
	   $tabs = array();
	   $tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/cad/cad001/form/cad001FormAlterarContato/', 'Contato', '&tab=geral&formId=' . urlencode($formId) . '&acaoId='. urlencode($r_acaoId)),
	   );

	   // Processa Tabs
	   echo gProcessaTabs($tabsList, $tabs);

	   // Carrega Template
	   $g['smarty']->assign('tabs', $tabs);
	   $g['smarty']->display('tab.tpl');
   break;

	case 'cad001TabEtiquetasClientes': 	
		$formId = uniqid();
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
		array(uniqid(), '/cad/cad001/form/cad001FormEtiquetasClientes/', 'Clientes', '&tab=geral&formId=' . urlencode($formId)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'cad001TabJuntar':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
		array(uniqid(), '/cad/cad001/form/cad001FormJuntar/', 'Juntar Clientes/Fornecedores', '&tab=geral'),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'cad001OrcamentoIncluirCliente':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
		array(uniqid(), '/cad/cad001/form/cad001OrcamentoIncluirCliente/', 'Dados', '&tab=geral'),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'cad001PedidoIncluirCliente':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
		array(uniqid(), '/cad/cad001/form/cad001PedidoIncluirCliente/', 'Dados', '&tab=geral'),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'cad001tabCadastroEndereco':// Definição das Tabs
		$tabs = array();
		$tabsList = array(
			array(uniqid(), '/cad/cad001/form/cad001FormCadastroEndereco/', 'Endereço', '&tab=geral&acaoId=' . urlencode($r_acaoId)),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

	case 'cad001CotacaoIncluirFornecedor':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
		array(uniqid(), '/cad/cad001/form/cad001CotacaoIncluirFornecedor/', 'Dados', '&tab=geral'),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;
}
