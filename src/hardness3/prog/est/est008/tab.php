<?php
namespace hardness;
/**
* Weiberlan
* 24/02/2012
*/

	/**
	* Ações
	*/
	switch ($g['r'][3]) {

		/**
		* Chamado em: est008Content01
		*/

		case 'est008TabPrincipal':

			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/est/est008/grid/est008GridCotacaoCompra/', 'Cotação de Compra', "&acaoId=" . urlencode($r_acaoId), false, true),
			array(uniqid(), '/est/est008/grid/est008GridProdutosCotacao/', 'Produtos', "&acaoId=" . urlencode($r_acaoId), false, true),
			);

			// Processa Tabs
			echo gProcessaTabs($tabsList, $tabs);

			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('tab.tpl');
		break;


		case 'est008TabCadastroCotacaoCompra':

			// Definição das Tabs
			$tabs = array();
			if(empty($r_acaoId)){
				$tabsList = array(
					// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
					array(uniqid(), '/est/est008/form/est008FormCadastroCotacaoCompra/', 'Cabeçalho', "&tab=geral&acaoId=" . urlencode($r_acaoId)),
				);
			}else{
				$tabsList = array(
					// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
					array(uniqid(), '/est/est008/content/est008ContentFornecedores/', 'Fornecedores', "&T010_Id=" . urlencode($r_acaoId),false,true),
					array(uniqid(), '/est/est008/content/est008ContentProdutos/', 'Produtos', "&T010_Id=" . urlencode($r_acaoId),false,true),
					array(uniqid(), '/est/est008/grid/est008GridIniciarCotacao/', 'Preços', "&tab=geral&T010_Id=" . urlencode($r_acaoId),false,true),
					array(uniqid(), '/est/est008/form/est008FormCadastroCotacaoCompra/', 'Cabeçalho', "&tab=geral&acaoId=" . urlencode($r_acaoId),false,true),
					array(uniqid(), '/etc/etc006/grid/etc006GridTodos/', 'E-mail', "&tab=geral&T010_Id=" . urlencode($r_acaoId),false,true),
				);
			}

			// Processa Tabs
			echo gProcessaTabs($tabsList, $tabs);

			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('tab.tpl');
		break;
			
		case 'est008TabGerarOC':

			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
			// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/est/est008/grid/est008GridGerarOC/', 'Gerar OC', "&T010_Id=" . urlencode($r_T010_Id)),
			);

			// Processa Tabs
			echo gProcessaTabs($tabsList, $tabs);

			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('tab.tpl');
		break;
		
		case 'est008TabFornecedoresOC':

			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
				// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
				array(uniqid(), '/est/est008/grid/est008GridFornecedoresOC/', 'Fornecedores OC', "&acaoId=" . urlencode($r_acaoId). '&T059_Id=' . urlencode($r_T059_Id))
			);

			// Processa Tabs
			echo gProcessaTabs($tabsList, $tabs);

			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('tab.tpl');
		break;	
		
		case 'est008TabFornecedoresOCForn':

			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
				// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
				array(uniqid(), '/est/est008/grid/est008GridFornecedoresOCForn/', 'Produtos', "&T010_Id=" . urlencode($r_T010_Id). '&T173_Id=' . urlencode($r_T173_Id)),
				array(uniqid(), '/est/est008/grid/est008GridContatos/', 'Contatos', "&T010_Id=" . urlencode($r_T010_Id). '&T173_Id=' . urlencode($r_T173_Id))
			);

			// Processa Tabs
			echo gProcessaTabs($tabsList, $tabs);

			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('tab.tpl');
		break;	
		
		case 'est008tabCadastroCotacaoCompraNew':

			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
				// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
				array(uniqid(), '/est/est008/form/est008FormCadastroCotacaoCompra/', 'Cadastro', "&tab=geral&GerarCotacaoEstoque=" . urlencode($r_GerarCotacaoEstoque) . 
																										   '&GerarCotacaoEmergencia='.urlencode($r_GerarCotacaoEmergencia) .
																										   '&GerarCotacaoEstoqueRequsicao='.urlencode($r_GerarCotacaoEstoqueRequsicao))
			);

			// Processa Tabs
			echo gProcessaTabs($tabsList, $tabs);

			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('tab.tpl');
		break;	
		
		case 'est006tabIncluirOrdemCompra':

			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
				// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
				array(uniqid(), '/est/est006/form/est006FormIncluirOrdemCompra/', 'Cadastro', "&tab=geral&GerarOCEstoque=" . urlencode($r_GerarOCEstoque) . '&GerarOCEmergencia=' . urlencode($r_GerarOCEmergencia))
			);

			// Processa Tabs
			echo gProcessaTabs($tabsList, $tabs);

			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('tab.tpl');
		break;	
		
		case 'est008tabAlterarMarca':
			
			$T059_Id      = (isset($r_T059_Id)) ? $r_T059_Id : false;
			$T010_Id 	  = (isset($r_T010_Id)) ? $r_T010_Id : false;
			$T173_Id 	  = (isset($r_T173_Id)) ? $r_T173_Id : false;
			$T174_D009_Id = (isset($r_T174_D009_Id)) ? $r_T174_D009_Id : false;

			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
				// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
				array(uniqid(), '/est/est008/form/est008formAlterarMarca/', 'Marca', '&tab=geral&T059_Id=' . urlencode($T059_Id) 
																	 . '&T010_Id=' . urlencode($T010_Id) 
																	 . '&acao_Id=' . urlencode($T010_Id) 
																	 . '&T173_Id=' . urlencode($T173_Id)
																	 . '&T174_D009_Id=' . urlencode($T174_D009_Id))
			);

			// Processa Tabs
			echo gProcessaTabs($tabsList, $tabs);

			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('tab.tpl');
		break;
		
		case 'est008tabResumoCotacao':
			
			$T010_Id 	  = (isset($r_T010_Id)) ? $r_T010_Id : false;
			$T173_Id 	  = (isset($r_T173_Id)) ? $r_T173_Id : false;
			$T174_Id 	  = (isset($r_T174_Id)) ? $r_T174_Id : false;
			$D024_Id 	  = (isset($r_D024_Id)) ? $r_D024_Id : false;

			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
				// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
				array(uniqid(), '/est/est008/grid/est008GridResumoCotacao/', 'Pré-Visualizar Cotação', '&tab=geral&T010_Id=' . urlencode($T010_Id) 
																	 . '&T173_Id=' . urlencode($T173_Id)
																	 . '&T174_Id=' . urlencode($T174_Id) 
																	 . '&D024_Id=' . urlencode($D024_Id))
			);

			// Processa Tabs
			echo gProcessaTabs($tabsList, $tabs);

			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('tab.tpl');
		break;
		
		case 'est008TabResumo':
			
			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
				// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
				array(uniqid(), '/est/est008/outros/est008OutrosResumo/', 'Resumo', '&tab=geral&T010_Id=' . urlencode($r_T010_Id) 
																	 . '&D009_Id=' . urlencode($r_D009_Id))
			);

			// Processa Tabs
			echo gProcessaTabs($tabsList, $tabs);

			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('tab.tpl');
		break;
		
		case 'est008TabRetornarProdForn':
			
			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
				// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
				array(uniqid(), '/est/est008/grid/est008GridRetornarProdForn/', 'Retornar produto', '&tab=geral&T010_Id=' . urlencode($r_T010_Id) 
																	 . '&T173_Id=' . urlencode($r_T173_Id))
			);

			// Processa Tabs
			echo gProcessaTabs($tabsList, $tabs);

			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('tab.tpl');
		break;
		
		case 'est008TabIncluirProdForn':
			$div = (isset($r_div)) ? $r_div : false;
			
			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
				// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
				array(uniqid(), '/est/est008/form/est008FormIncluirProdForn/', 'Incluir produto', '&tab=geral&T010_Id=' . urlencode($r_T010_Id) 
																	 . '&T173_Id=' . urlencode($r_T173_Id)
																	 . '&div=' . urlencode($div))
			);

			// Processa Tabs
			echo gProcessaTabs($tabsList, $tabs);

			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('tab.tpl');
		break;
		
		case 'est008TabPreCadProd':
			$T059_Id = (isset($r_T059_Id)) ? $r_T059_Id : false;
			$T010_Id = (isset($r_T010_Id)) ? $r_T010_Id : false;
			
			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
				// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
				array(uniqid(), '/est/est008/form/est008FormPreCadProd/', 'Pré-Cadastro', '&tab=geral&T059_Id=' . urlencode($r_T059_Id) . '&T010_Id=' . urlencode($r_T010_Id) . '&abrirEditar='.urlencode($r_abrirEditar))
																	
			);

			// Processa Tabs
			echo gProcessaTabs($tabsList, $tabs);

			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('tab.tpl');
		break;
		
		case 'est008TabSelecionarEmpresa':
			$T173_Id = (isset($r_T173_Id)) ? $r_T173_Id : false;
			$T010_Id = (isset($r_T010_Id)) ? $r_T010_Id : false;
			
			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
				// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
				array(uniqid(), '/est/est008/form/est008FormSelecionarEmpresa/', 'Empresa', '&tab=geral&T173_Id=' . urlencode($r_T173_Id) . '&T010_Id=' . urlencode($r_T010_Id))
																	
			);

			// Processa Tabs
			echo gProcessaTabs($tabsList, $tabs);

			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('tab.tpl');
		break;
		
		case 'est008TabEditarProdutoFornecedor':
			
			// Definição das Tabs
			$tabs = array();
			$tabsList = array(
				// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
				array(uniqid(), '/est/est008/form/est008FormEditarProdutoFornecedor/', 'Produto', '&tab=geral&acaoId=' . urlencode($r_acaoId) . '&T010_Id=' . urlencode($r_T010_Id) . '&T173_Id=' . urlencode($r_T173_Id))
			);

			// Processa Tabs
			echo gProcessaTabs($tabsList, $tabs);

			// Carrega Template
			$g['smarty']->assign('tabs', $tabs);
			$g['smarty']->display('tab.tpl');
		break;
	}

?>
