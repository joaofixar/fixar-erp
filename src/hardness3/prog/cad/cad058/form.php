<?php
namespace hardness;
//Classes
require_once("prog/cad/cad058/form_func-js.php");

switch ($g['r'][3]) {
	case 'cad058FormConfGeral':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		
		/**
		* PARTE 2: Definição do formulário
		*/
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = false;
		if ($formMultiTab) {
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}

		$T015 = mysql_fetch_array(mysql_query("SELECT * FROM T015 WHERE T015_Id = '{$acaoId}'"));
		$D032_Id = (!empty($T015['T015_D032_Id'])) ? $T015['T015_D032_Id'] : 0;
		
		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = '0000';
		$form_campo_pk = '0000_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad058FormConfGeral_Btn';
		// Form que será executado ao clicar no 
		$enviar_acaoHref = '/cad/cad058/form/cad058FormConfGeral/';
		// formEditor
		// Form Editor
		$form_editor = array(
			'geral' => '{"campoNomeEmpresa":{"top":6.969970703125,"left":24.73958396911621,"width":128,"hidden":0},"EmailEnviadosAddContatosEmpresa":{"top":6.969970703125,"left":520.7567138671875,"width":95,"hidden":0},"tipoSistema":{"top":29.993865966796875,"left":40.28799057006836,"width":128,"hidden":0},"habilitarEdicaoAgenda":{"top":29.993865966796875,"left":587.2855224609375,"width":95,"hidden":0},"alertasSqlLentos":{"top":52.97181701660156,"left":14.889705657958984,"width":56.039215999999996,"hidden":0},"usarDescontoProdutos":{"top":52.97181701660156,"left":624.3566284179688,"width":95,"hidden":0},"usarSenhaRootPadrao":{"top":75.98039245605469,"left":24.463848114013672,"width":64,"hidden":0},"exibirRegistroAgendaManual":{"top":75.98039245605469,"left":539.8743896484375,"width":95,"hidden":0},"impressaoJava":{"top":98.98896789550781,"left":11.795343399047852,"width":128,"hidden":0},"supervidorPodeVerTodosClientes":{"top":98.98896789550781,"left":542.5092163085938,"width":95,"hidden":0},"usarSuporteInterno":{"top":121.99754333496094,"left":33.639705657958984,"width":64,"hidden":0},"vendedorPodeVerTodosClientes":{"top":121.99754333496094,"left":547.7022094726562,"width":95,"hidden":0},"obrigatorioFiltroTelaTransicao":{"top":144.99081420898438,"left":22.99325942993164,"width":64,"hidden":1},"modeloBuscaPesquisaGridProduto":{"top":144.99081420898438,"left":451.2561340332031,"width":95,"hidden":0},"destaqueTermosPesquisaGrid":{"top":167.9993896484375,"left":364.001220703125,"width":95,"hidden":0},"descricaoCFOPExibida":{"top":190.99264526367188,"left":535.8302612304688,"width":95,"hidden":0}}',
		);
		
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('cad058FormConfGeral');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					//'usarLoginColetor' => array('name' => 'usarLoginColetor', 'label' => 'Habilitar Login Coletor', 'type' => 'select', 'select' => 
					//	array(
					//		array('title' => 'Sim', 'value' => 'S'),
					//		array('title' => 'Não', 'value' => 'N')
					//	)
					//),
					'campoNomeEmpresa' => array('name' => 'campoNomeEmpresa', 'label' => 'Nome Empresa Exibido', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Nome Fantasia', 'value' => 'fantasia'),
							array('title' => 'Razão Social', 'value' => 'razaosocial')
						)
					),
					//'alertaEmailRecebido' => array('name' => 'alertaEmailRecebido', 'label' => 'Receber alerta email', 'type' => 'select', 'select' =>
					//	array(
					//		array('title' => 'Não', 'value' => ''),
					//		array('title' => 'Automático', 'value' => 'auto'),
					//		array('title' => 'Tela cheia', 'value' => 'full')
					//	)
					//),
					'EmailEnviadosAddContatosEmpresa' => array('name' => 'EmailEnviadosAddContatosEmpresa', 'label' => 'Adicionar email CC aos contatos do cliente', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					//'CFOPPadraoOrdemServico' => array('name' => 'CFOPPadraoOrdemServico', 'label' => 'CFOP padrão O.S.', 'type' => 'text'),
					//'numeroServicosFuncionarioExecSimult' => array('name' => 'numeroServicosFuncionarioExecSimult', 'label' => 'Máximo de serviços ativos', 'type' => 'text'),
					'usarDescontoProdutos' => array('name' => 'usarDescontoProdutos', 'label' => 'Desconto por produto', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'habilitarEdicaoAgenda' => array('name' => 'habilitarEdicaoAgenda', 'label' => 'Habilitar alteração na agenda', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'exibirRegistroAgendaManual' => array('name' => 'exibirRegistroAgendaManual', 'label' => 'Exibir registros automáticos da agenda', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'obrigatorioFiltroTelaTransicao' => array('name' => 'obrigatorioFiltroTelaTransicao', 'label' => 'Obrigatório uso do filtro', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'vendedorPodeVerTodosClientes' => array('name' => 'vendedorPodeVerTodosClientes', 'label' => 'Vendedor pode ver todos os clientes?', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'supervidorPodeVerTodosClientes' => array('name' => 'supervidorPodeVerTodosClientes', 'label' => 'Supervisor pode ver todos os clientes?', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'impressaoJava' => array('name' => 'impressaoJava', 'label' => 'Periféricos - Imprimir com', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Applet Java', 'value' => '1'),
							array('title' => 'Java', 'value' => '2')
						)
					),
					'usarSuporteInterno' => array('name' => 'usarSuporteInterno', 'label' => 'Usar suporte interno?', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'modeloBuscaPesquisaGridProduto' => array('name' => 'modeloBuscaPesquisaGridProduto', 'label' => 'Define o tipo de busca dos produtos em vendas/compras', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Geral', 'value' => '1'),
							array('title' => 'Código', 'value' => '2'),
							array('title' => 'Descrição', 'value' => '3')
						)
					),
					'destaqueTermosPesquisaGrid' => array('name' => 'destaqueTermosPesquisaGrid', 'label' => 'Realçar informação pesquisada na busca de produtos em vendas/compras', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N'),
						)
					),
					'descricaoCFOPExibida' => array('name' => 'descricaoCFOPExibida', 'label' => 'Define qual descrição da CFOP irá exibir', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Padrão', 'value' => 'P'),
							array('title' => 'Comercial', 'value' => 'C')

						)
					),
				)
			)
		);
		
		if($g['usuarioAtual'] == '63'){
			$formArray['campos']['geral']['usarSenhaRootPadrao'] = array('name' => 'usarSenhaRootPadrao', 'label' => 'Usar senha root padrão', 'type' => 'select', 'select' =>
				array(
					array('title' => 'Sim', 'value' => 'S'),
					array('title' => 'Não', 'value' => 'N')
				)
			);
			$formArray['campos']['geral']['alertasSqlLentos'] = array('name' => 'alertasSqlLentos', 'label' => 'Tempo mínimo sql lentos', 'type' => 'text');
			$formArray['campos']['geral']['tipoSistema'] = array('name' => 'tipoSistema', 'label' => 'Formato do sistema', 'type' => 'select', 'select' =>
				array(
					array('title' => 'COMPLETO', 'value' => 'C'),
					array('title' => 'POR MÓDULOS', 'value' => 'M')
				)
			);
		}

		$arrayObservacoes = array();
		$arrayDiff = array();
		$sqlC031 = mysql_query("SELECT C031_Campo, C031_Observacao, C031_Valor, C031_Valor_Padrao FROM C031 GROUP BY C031_Campo");
		while($rowC031 = mysql_fetch_array($sqlC031)){
			$arrayObservacoes[$rowC031['C031_Campo']] = $rowC031['C031_Observacao'];
			if($rowC031['C031_Valor'] != $rowC031['C031_Valor_Padrao']){
				$arrayDiff[] = $rowC031['C031_Campo'];
			}
		}
		foreach ($formArray['campos'] as $keyTab => &$camposTab) {
			foreach ($camposTab as $keyCampo => &$campoFormulario) {
				$campoFormulario['extra'] = 'title="'.$arrayObservacoes[$keyCampo].'"';
				if(in_array($keyCampo, $arrayDiff)){
					if(empty($campoFormulario['label'])) { continue; }
					$campoFormulario['label'] = "<font color=\"blue\">".$campoFormulario['label']."</font>";
				}
			}
		}
		unset($camposTab);
		unset($campoFormulario);
		$form->definirFormularioArray($formArray);

		/**
		* PARTE 3: Processamento do formulário (se foi enviado)
		* 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		*/
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
			// Form sem erros
			} else if ($formCodigo == 2) {
				// Update
				require_once('bibliotecas/classes/CAD058.php');
                $CAD058 = new CAD058();
                $retorno = $CAD058->atualizarConfGlobal($form, $g['empresaAtual']);
				if ($retorno == true) {
					echo gGeraAlertMsg('Operação OK', 'Configurações salvas com sucesso!');
				}
			}
		} else {
			// Carrega os valores caso seja um update
			$sql = mysql_query("SELECT * FROM C031 WHERE C031_C004_Id = '{$g['empresaAtual']}'");
			while($row = mysql_fetch_array($sql)){
				$form->campoSetarValor($row['C031_Campo'], $row['C031_Valor']);
			}
		}
		
		/**
		* PARTE 4: Gerar o formulário
		*/
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);
		
		/**
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo  $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		echo $g['smarty']->fetch('formEditor.tpl');
	break;

	case 'cad058FormConfCadastros':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;

		/**
		* PARTE 2: Definição do formulário
		*/
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = true;
		if ($formMultiTab) {
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}

		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = '0000';
		$form_campo_pk = '0000_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad058FormConfCadastros_Btn';
		// Form que será executado ao clicar no
		$enviar_acaoHref = '/cad/cad058/form/cad058FormConfCadastros/';
		// formEditor
		// Form Editor
		$form_editor = array(
			'produtos' => '{"mascaraCodigoProduto":{"top":7,"left":72.71875,"width":248,"hidden":0},"alcadaQuantidadeMinimaEstoque":{"top":7,"left":562.109375,"width":72,"hidden":0},"precoTabelado":{"top":7,"left":941,"width":64,"hidden":1},"mascaraCodigoProdutoPreCadastro":{"top":30,"left":85.140625,"width":248,"hidden":0},"diasMaxProdutoPreCadastro":{"top":30,"left":563.390625,"width":72,"hidden":0},"utilizaEcommerce":{"top":53,"left":92.078125,"width":64,"hidden":0},"prazoFornecedorHomologacao":{"top":53,"left":592.234375,"width":40,"hidden":0},"prazoValidadeCusto01":{"top":76,"left":85.046875,"width":56,"hidden":0},"tipoPreco":{"top":76,"left":631.796875,"width":96,"hidden":0},"prazoValidadeCusto02":{"top":99,"left":85.046875,"width":56,"hidden":0},"mascaraCodigoProdutoSimilares":{"top":99,"left":506.140625,"width":152,"hidden":0},"prazoValidadeCusto03":{"top":122,"left":85.046875,"width":56,"hidden":0},"habilitarMultiplasLocacoes":{"top":122,"left":538.625,"width":64,"hidden":0},"prazoUltimaVenda01":{"top":145,"left":93.125,"width":56,"hidden":0},"gravarCodProdAoCadastrar":{"top":145,"left":531.25,"width":64,"hidden":0},"prazoUltimaVenda02":{"top":168,"left":93.125,"width":56,"hidden":0},"desativarHistoricoPrecos":{"top":168,"left":588.578125,"width":64,"hidden":0},"prazoUltimaVenda03":{"top":191,"left":93.125,"width":56,"hidden":0},"SeparaProdutoPorEmpresa":{"top":191,"left":576.84375,"width":64,"hidden":0},"obrigatorioGrupoPreCadastroProduto":{"top":214,"left":21.5625,"width":64,"hidden":0},"quantidadeDigitosEAN":{"top":214,"left":599.78125,"width":56,"hidden":0},"obrigatorioSetorPreCadastroProduto":{"top":237,"left":24.984375,"width":64,"hidden":0},"curvaABCprodutos":{"top":237,"left":609.359375,"width":56,"hidden":0},"obrigatorioNCMPreCadastroProduto":{"top":260,"left":25.359375,"width":64,"hidden":0},"mostrarBotaoIncluirHistoricoProduto":{"top":260,"left":512.5,"width":63,"hidden":0},"obrigatorioSubGrupoPreCadastroProduto":{"top":283,"left":0.609375,"width":64,"hidden":0},"diasAntecedenciaNotificarValidadeProduto":{"top":283,"left":427.4375,"width":56,"hidden":0},"obrigatorioOrigemPreCadastroProduto":{"top":306,"left":15.65625,"width":64,"hidden":0},"habilitaMascaraCodigo":{"top":306,"left":612.015625,"width":64,"hidden":0},"obrigatorioTipoPreCadastroProduto":{"top":329,"left":31.25,"width":64,"hidden":0},"calculaEstoqueMinimoMaximoProduto":{"top":329,"left":410.921875,"width":63,"hidden":0},"utilizaQuantidadeConversao":{"top":375,"left":499.90625,"width":64,"hidden":1},"calculadoraQtdNavegaTab":{"top":351.9931640625,"left":-0.67010498046875,"width":64,"hidden":0},"diasAntecedenciaNotificarValidadeDocumentoProduto":{"top":398,"left":342.578125,"width":56,"hidden":1},"SMT_habilita_Etiqueta_Produto":{"top":398,"left":342.578125,"width":56,"hidden":1}}',
			'clientesForn' => '{"validarCadastroClienteCnpjCpfDuplidade":{"top":7,"left":580.90625,"width":63,"hidden":0},"AbrirEdicaoClienteAposInsercao":{"top":7,"left":103.03125,"width":64,"hidden":0},"validarCPFCNPJPreCadastro":{"top":30,"left":579.03125,"width":64,"hidden":0},"incluirContatoAposInsercao":{"top":30,"left":87.140625,"width":63,"hidden":0},"permiteTransportarDiferentesUF":{"top":53,"left":478.828125,"width":64,"hidden":0},"diasMaxClientePreCadastro":{"top":53,"left":121.0625,"width":56,"hidden":0},"separarClientePorEmpresa":{"top":76,"left":603.71875,"width":63,"hidden":0},"mesesProcessarClasseCliente":{"top":76,"left":116.859375,"width":56,"hidden":0},"curvaABCclientes":{"top":99,"left":609.359375,"width":72,"hidden":0},"diasConsiderarAtraso":{"top":99,"left":10.09375,"width":55,"hidden":0},"ClasseA":{"top":165,"left":15,"width":null,"hidden":1},"mediaVendaMesClienteClasseA":{"top":191,"left":4.453125,"width":72,"hidden":0},"ticketMedioClienteClasseA":{"top":191,"left":155.390625,"width":72,"hidden":0},"diasAtrasoPgtoClienteClasseA":{"top":191,"left":309.359375,"width":24,"hidden":0},"qtdTitulosAtrasoValidarMaiorAtrasoClasseA":{"top":191,"left":443.515625,"width":24,"hidden":0},"mesesCadastroClienteClasseA":{"top":191,"left":560.109375,"width":24,"hidden":0},"prazoVendaClienteClasseA":{"top":191,"left":704.34375,"width":24,"hidden":0},"percentualAtrasoClienteClasseA":{"top":191,"left":827.46875,"width":24,"hidden":0},"ClasseB":{"top":211,"left":14,"width":null,"hidden":1},"mediaVendaMesClienteClasseB":{"top":237,"left":4.453125,"width":72,"hidden":0},"ticketMedioClienteClasseB":{"top":237,"left":155.390625,"width":72,"hidden":0},"diasAtrasoPgtoClienteClasseB":{"top":237,"left":309.359375,"width":24,"hidden":0},"qtdTitulosAtrasoValidarMaiorAtrasoClasseB":{"top":237,"left":443.515625,"width":24,"hidden":0},"mesesCadastroClienteClasseB":{"top":237,"left":560.109375,"width":24,"hidden":0},"prazoVendaClienteClasseB":{"top":237,"left":704.34375,"width":24,"hidden":0},"percentualAtrasoClienteClasseB":{"top":237,"left":827.46875,"width":24,"hidden":0},"ClasseC":{"top":257,"left":17,"width":null,"hidden":1},"mediaVendaMesClienteClasseC":{"top":283,"left":4.453125,"width":72,"hidden":0},"ticketMedioClienteClasseC":{"top":283,"left":155.390625,"width":72,"hidden":0},"diasAtrasoPgtoClienteClasseC":{"top":283,"left":309.359375,"width":24,"hidden":0},"qtdTitulosAtrasoValidarMaiorAtrasoClasseC":{"top":283,"left":443.515625,"width":24,"hidden":0},"mesesCadastroClienteClasseC":{"top":283,"left":560.109375,"width":24,"hidden":0},"prazoVendaClienteClasseC":{"top":283,"left":704.34375,"width":24,"hidden":0},"percentualAtrasoClienteClasseC":{"top":283,"left":827.46875,"width":24,"hidden":0},"ClasseD":{"top":303,"left":14,"width":null,"hidden":1},"mediaVendaMesClienteClasseD":{"top":329,"left":4.453125,"width":72,"hidden":0},"ticketMedioClienteClasseD":{"top":329,"left":155.390625,"width":72,"hidden":0},"diasAtrasoPgtoClienteClasseD":{"top":329,"left":309.359375,"width":24,"hidden":0},"qtdTitulosAtrasoValidarMaiorAtrasoClasseD":{"top":329,"left":443.515625,"width":24,"hidden":0},"mesesCadastroClienteClasseD":{"top":329,"left":560.109375,"width":24,"hidden":0},"prazoVendaClienteClasseD":{"top":329,"left":704.34375,"width":24,"hidden":0},"percentualAtrasoClienteClasseD":{"top":329,"left":827.46875,"width":24,"hidden":0}}',
			'custos' => '{"tipoCusto":{"top":30,"left":175.546875,"width":191,"hidden":0},"estoqueUtilizadoFormarCusto":{"top":53,"left":83.25,"width":191,"hidden":1},"custoMaisRecenteUtilizado":{"top":53,"left":127.703125,"width":190,"hidden":0},"custoPrincipal":{"top":76,"left":85.828125,"width":191,"hidden":0},"prazoCustoPrincipal":{"top":99,"left":165.046875,"width":88,"hidden":0},"custoSecundario":{"top":122,"left":34.078125,"width":191,"hidden":0},"prazoCustoSecundario":{"top":145,"left":165.046875,"width":88,"hidden":0},"custoZerado":{"top":168,"left":56,"width":191,"hidden":0},"inserirTabelaFornecedorPadrao":{"top":30,"left":525.90625,"width":184,"hidden":0}}'
		);
		/*
		if($g['empresaAtual'] != '1'){
			$form_editor['clientesForn'] = '{"AbrirEdicaoClienteAposInsercao":{"top":7,"left":25,"width":64,"hidden":0},"validarCPFCNPJPreCadastro":{"top":7,"left":560,"width":64,"hidden":0},"diasMaxClientePreCadastro":{"top":30,"left":40,"width":40,"hidden":0},"mesesProcessarClasseCliente":{"top":53,"left":35,"width":40,"hidden":0},"ticketMedioClienteClasseA":{"top":99,"left":10,"width":88,"hidden":1},"diasAtrasoPgtoClienteClasseA":{"top":99,"left":246,"width":24,"hidden":1},"mesesCadastroClienteClasseA":{"top":99,"left":383,"width":24,"hidden":1},"prazoVendaClienteClasseA":{"top":99,"left":530,"width":24,"hidden":1},"percentualAtrasoClienteClasseD":{"top":99,"left":681,"width":24,"hidden":1},"ticketMedioClienteClasseB":{"top":122,"left":10,"width":88,"hidden":1},"diasAtrasoPgtoClienteClasseB":{"top":122,"left":246,"width":24,"hidden":1},"mesesCadastroClienteClasseB":{"top":122,"left":383,"width":24,"hidden":1},"prazoVendaClienteClasseB":{"top":122,"left":530,"width":24,"hidden":1},"percentualAtrasoClienteClasseA":{"top":122,"left":681,"width":24,"hidden":1},"ticketMedioClienteClasseC":{"top":145,"left":9,"width":88,"hidden":1},"diasAtrasoPgtoClienteClasseC":{"top":145,"left":246,"width":24,"hidden":1},"mesesCadastroClienteClasseC":{"top":145,"left":383,"width":24,"hidden":1},"prazoVendaClienteClasseC":{"top":145,"left":530,"width":24,"hidden":1},"percentualAtrasoClienteClasseB":{"top":145,"left":681,"width":24,"hidden":1},"ticketMedioClienteClasseD":{"top":168,"left":9,"width":88,"hidden":1},"diasAtrasoPgtoClienteClasseD":{"top":168,"left":246,"width":24,"hidden":1},"mesesCadastroClienteClasseD":{"top":168,"left":383,"width":24,"hidden":1},"prazoVendaClienteClasseD":{"top":168,"left":530,"width":24,"hidden":1},"percentualAtrasoClienteClasseC":{"top":168,"left":681,"width":24,"hidden":1}}';
		}
		*/
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('cad058FormConfCadastros');
		$formArray = array(
			'campos' => array(
				'produtos' => array(
					'utilizaEcommerce' => array('name' => 'utilizaEcommerce', 'label' => 'Utiliza E-commerce', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'alcadaQuantidadeMinimaEstoque' => array('name' => 'alcadaQuantidadeMinimaEstoque', 'label' => 'Quantidade mínima de estoque', 'type' => 'text'),
					'precoTabelado' => array('name' => 'precoTabelado', 'label' => 'Preço Tabelado', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'SMT_habilita_Etiqueta_Produto' => array('name' => 'SMT_habilita_Etiqueta_Produto', 'label' => 'Esse produto usa etiqueta ?', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					//'multimarcas' => array('name' => 'multimarcas', 'label' => 'Multimarcas', 'type' => 'select', 'select' =>
					//	array(
					//		array('title' => 'Sim', 'value' => 'S'),
					//		array('title' => 'Não', 'value' => 'N'),
					//		array('title' => 'Ambos', 'value' => 'ambas')
					//	)
					//),
					'diasMaxProdutoPreCadastro' => array('name' => 'diasMaxProdutoPreCadastro', 'label' => 'Prazo produto em pré cadastro', 'type' => 'text'),
					'mascaraCodigoProduto' => array('name' => 'mascaraCodigoProduto', 'label' => 'Padrão Código Produto', 'type' => 'text', 'uppercase' => false),
					'mascaraCodigoProdutoPreCadastro' => array('name' => 'mascaraCodigoProdutoPreCadastro', 'label' => 'Padrão Pré-Cadastro', 'type' => 'text', 'uppercase' => false),
					'mascaraCodigoProdutoSimilares' => array('name' => 'mascaraCodigoProdutoSimilares', 'label' => 'Padrão produtos similares', 'type' => 'text', 'uppercase' => false),
					'prazoValidadeCusto01' => array('name' => 'prazoValidadeCusto01', 'label' => 'Prazo validade custo', 'type' => 'text', 'uppercase' => false),
					'prazoValidadeCusto02' => array('name' => 'prazoValidadeCusto02', 'label' => 'Prazo validade custo', 'type' => 'text', 'uppercase' => false),
					'prazoValidadeCusto03' => array('name' => 'prazoValidadeCusto03', 'label' => 'Prazo validade custo', 'type' => 'text', 'uppercase' => false),
					'prazoUltimaVenda01' => array('name' => 'prazoUltimaVenda01', 'label' => 'Prazo última venda', 'type' => 'text', 'uppercase' => false),
					'prazoUltimaVenda02' => array('name' => 'prazoUltimaVenda02', 'label' => 'Prazo última venda', 'type' => 'text', 'uppercase' => false),
					'prazoUltimaVenda03' => array('name' => 'prazoUltimaVenda03', 'label' => 'Prazo última venda', 'type' => 'text', 'uppercase' => false),
					'prazoFornecedorHomologacao' => array('name' => 'prazoFornecedorHomologacao', 'label' => 'Prazo homologação fornecedor', 'type' => 'moeda', 'uppercase' => false, 'casasDecimais' => '0'),
					'tipoPreco' => array('name' => 'tipoPreco', 'label' => 'Tipo de preço', 'type' => 'select', 'select' =>
						array(
							array('title' => 'IPV', 'value' => 'I'),
							array('title' => 'Manual', 'value' => 'M')
						)
					),
					'obrigatorioGrupoPreCadastroProduto' => array('name' => 'obrigatorioGrupoPreCadastroProduto', 'label' => 'Obrigatório grupo no pré cadastro', 'type' => 'select', 'select' =>
						array(
							array('title' => 'SIM', 'value' => 'S'),
							array('title' => 'NÃO', 'value' => 'N')
						)
					),
					'SeparaProdutoPorEmpresa' => array('name' => 'SeparaProdutoPorEmpresa', 'label' => 'Separar produtos por empresa?', 'type' => 'select', 'select' =>
						array(
							array('title' => 'SIM', 'value' => 'S'),
							array('title' => 'NÃO', 'value' => 'N')
						)
					),
					'quantidadeDigitosEAN' => array('name' => 'quantidadeDigitosEAN', 'label' => 'Quantidade de dígitos EAN', 'type' => 'text', 'uppercase' => false),
					'obrigatorioSetorPreCadastroProduto' => array('name' => 'obrigatorioSetorPreCadastroProduto', 'label' => 'Obrigatório setor no pré cadastro', 'type' => 'select', 'select' =>
						array(
							array('title' => 'SIM', 'value' => 'S'),
							array('title' => 'NÃO', 'value' => 'N')
						)
					),
					'obrigatorioNCMPreCadastroProduto' => array('name' => 'obrigatorioNCMPreCadastroProduto', 'label' => 'Obrigatório NCM no pré cadastro', 'type' => 'select', 'select' =>
						array(
							array('title' => 'SIM', 'value' => 'S'),
							array('title' => 'NÃO', 'value' => 'N')
						)
					),
					'obrigatorioSubGrupoPreCadastroProduto' => array('name' => 'obrigatorioSubGrupoPreCadastroProduto', 'label' => 'Obrigatório sub-grupo no pré cadastro', 'type' => 'select', 'select' =>
						array(
							array('title' => 'SIM', 'value' => 'S'),
							array('title' => 'NÃO', 'value' => 'N')
						)
					),
					'obrigatorioOrigemPreCadastroProduto' => array('name' => 'obrigatorioOrigemPreCadastroProduto', 'label' => 'Obrigatório origem no pré cadastro', 'type' => 'select', 'select' =>
						array(
							array('title' => 'SIM', 'value' => 'S'),
							array('title' => 'NÃO', 'value' => 'N')
						)
					),
					'obrigatorioTipoPreCadastroProduto' => array('name' => 'obrigatorioTipoPreCadastroProduto', 'label' => 'Obrigatório tipo no pré cadastro', 'type' => 'select', 'select' =>
						array(
							array('title' => 'SIM', 'value' => 'S'),
							array('title' => 'NÃO', 'value' => 'N')
						)
					),
					'habilitaMascaraCodigo' => array('name' => 'habilitaMascaraCodigo', 'label' => 'Usar máscara de código', 'type' => 'select', 'select' =>
						array(
							array('title' => 'NÃO', 'value' => 'N'),
							array('title' => 'SIM', 'value' => 'S')
						)
					),
					'habilitarMultiplasLocacoes' => array('name' => 'habilitarMultiplasLocacoes', 'label' => 'Habilitar multiplas locações no produto', 'type' => 'select', 'select' =>
						array(
							array('title' => 'SIM', 'value' => 'S'),
							array('title' => 'NÃO', 'value' => 'N')
						)
					),
					'gravarCodProdAoCadastrar' => array('name' => 'gravarCodProdAoCadastrar', 'label' => 'Gravar código ao cadastrar novo produto', 'type' => 'select', 'select' =>
						array(
							array('title' => 'SIM', 'value' => 'S'),
							array('title' => 'NÃO', 'value' => 'N')
						)
					),
					'desativarHistoricoPrecos' => array('name' => 'desativarHistoricoPrecos', 'label' => 'Desativar histórico de preços', 'type' => 'select', 'select' =>
						array(
							array('title' => 'SIM', 'value' => 'S'),
							array('title' => 'NÃO', 'value' => 'N')
						)
					),
					'curvaABCprodutos' => array('name' => 'curvaABCprodutos', 'label' => 'Porcentagens curva ABC', 'type' => 'text'),
					'mostrarBotaoIncluirHistoricoProduto' => array('name' => 'mostrarBotaoIncluirHistoricoProduto', 'label' => 'Mostrar botão incluir no histórico do produto', 'type' => 'select', 'select' =>
						array(
							array('title' => 'SIM', 'value' => 'S'),
							array('title' => 'NÃO', 'value' => 'N')
						)
					),
					'diasAntecedenciaNotificarValidadeProduto' => array('name' => 'diasAntecedenciaNotificarValidadeProduto', 'label' => 'Dias com antencedência para notificar vencimento do produto', 'type' => 'text', 'uppercase' => false),
					'diasAntecedenciaNotificarValidadeDocumentoProduto' => array('name' => 'diasAntecedenciaNotificarValidadeDocumentoProduto', 'label' => 'Dias com antencedência para notificar vencimento dos documentos do produto', 'type' => 'text', 'uppercase' => false),
					'utilizaQuantidadeConversao' => array('name' => 'utilizaQuantidadeConversao', 'label' => 'Utiliza o processo de conversão de quantidade.', 'type' => 'select', 'select' =>
						array(
							array('title' => 'NÃO', 'value' => 'N'),
							array('title' => 'SIM', 'value' => 'S')
						)
					),
					'calculadoraQtdNavegaTab' => array('name' => 'calculadoraQtdNavegaTab', 'label' => 'Calculadora de qtd navegação por Tab', 'type' => 'select', 'select' =>
						array(
							array('title' => 'SIM', 'value' => 'S'),
							array('title' => 'NÃO', 'value' => 'N')
						)
					),
					'calculaEstoqueMinimoMaximoProduto' => array('name' => 'calculaEstoqueMinimoMaximoProduto', 'label' => 'Quantidade do estoque mínimo e máximo calculado pelo sistema', 'type' => 'select', 'select' =>
						array(
							array('title' => 'NÃO', 'value' => 'N'),
							array('title' => 'SIM', 'value' => 'S')
						)
					),
				),
				'custos' => array(
					'inserirTabelaFornecedorPadrao' => array('name' => 'inserirTabelaFornecedorPadrao', 'label' => 'Inserir tabela fornecedor padrão', 'type' => 'text'),
					'tipoCusto' => array('name' => 'tipoCusto', 'label' => 'Definição do custo', 'type' => 'select', 'select' =>
						array(
							array('title' => 'De acordo com o estoque', 'value' => 'E'),
							array('title' => 'Custo mais recente', 'value' => 'D')
						)
					),
					'custoMaisRecenteUtilizado' => array('name' => 'custoMaisRecenteUtilizado', 'label' => 'Custo mais recente utilizado', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Médio', 'value' => 'M'),
							array('title' => 'Última compra', 'value' => 'U'),
						)
					),
					'custoPrincipal' => array('name' => 'custoPrincipal', 'label' => 'Custo utilizado com estoque positivo', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Zerar', 'value' => ''),
							array('title' => 'Médio', 'value' => 'M'),
							array('title' => 'Última compra', 'value' => 'U'),
							array('title' => 'Tabela', 'value' => 'T')
						)
					),
					'prazoCustoPrincipal' => array('name' => 'prazoCustoPrincipal', 'label' => 'Prazo validade custo', 'type' => 'moeda', 'uppercase' => false, 'casasDecimais' => '0'),
					'custoSecundario' => array('name' => 'custoSecundario', 'label' => 'Custo utilizado com estoque menor igual a zero', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Zerar', 'value' => ''),
							array('title' => 'Médio', 'value' => 'M'),
							array('title' => 'Última compra', 'value' => 'U'),
							array('title' => 'Tabela', 'value' => 'T')
						)
					),
					'prazoCustoSecundario' => array('name' => 'prazoCustoSecundario', 'label' => 'Prazo validade custo', 'type' => 'moeda', 'uppercase' => false, 'casasDecimais' => '0'),
					'custoZerado' => array('name' => 'custoZerado', 'label' => 'Custo utilizado quando o custo estiver zero', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Zerar', 'value' => ''),
							array('title' => 'Médio', 'value' => 'M'),
							array('title' => 'Última compra', 'value' => 'U'),
							array('title' => 'Tabela', 'value' => 'T')
						)
					),
					'estoqueUtilizadoFormarCusto' => array('name' => 'estoqueUtilizadoFormarCusto', 'label' => 'Estoque utilizado para formar o custo', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Físico', 'value' => 'F'),
							array('title' => 'Líquido', 'value' => 'L')
						)
					),
				),
				'clientesForn' => array(
					'AbrirEdicaoClienteAposInsercao' => array('name' => 'AbrirEdicaoClienteAposInsercao', 'label' => 'Abrir cadastro cliente após incluir', 'type' => 'select', 'select' => 
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'incluirContatoAposInsercao' => array('name' => 'incluirContatoAposInsercao', 'label' => 'Incluir contato após realizar inclusão', 'type' => 'select', 'select' => 
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'diasMaxClientePreCadastro' => array('name' => 'diasMaxClientePreCadastro', 'label' => 'Prazo cliente em pré cadastro', 'type' => 'text'),
					'validarCPFCNPJPreCadastro' => array('name' => 'validarCPFCNPJPreCadastro', 'label' => 'Validar CPF/CNPJ no pré cadastro', 'type' => 'select', 'select' => 
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'validarCadastroClienteCnpjCpfDuplidade' => array('name' => 'validarCadastroClienteCnpjCpfDuplidade', 'label' => 'Validar CPF/CNPJ em duplicidade', 'type' => 'select', 'select' => 
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'curvaABCclientes' => array('name' => 'curvaABCclientes', 'label' => 'Porcentagens curva ABC', 'type' => 'text'),
					'mesesProcessarClasseCliente' => array('name' => 'mesesProcessarClasseCliente', 'label' => 'Meses processa classe cliente', 'type' => 'text'),
					'diasConsiderarAtraso' => array('name' => 'diasConsiderarAtraso', 'label' => 'Qtd dias considerar título como atraso classe cliente', 'type' => 'moeda', 'casasDecimais' => 0),
					'mediaVendaMesClienteClasseA' => array('name' => 'mediaVendaMesClienteClasseA', 'label' => 'Média mês', 'type' => 'moeda'),
					'mediaVendaMesClienteClasseB' => array('name' => 'mediaVendaMesClienteClasseB', 'label' => 'Média mês', 'type' => 'moeda'),
					'mediaVendaMesClienteClasseC' => array('name' => 'mediaVendaMesClienteClasseC', 'label' => 'Média mês', 'type' => 'moeda'),
					'mediaVendaMesClienteClasseD' => array('name' => 'mediaVendaMesClienteClasseD', 'label' => 'Média mês', 'type' => 'moeda'),
					'ticketMedioClienteClasseA' => array('name' => 'ticketMedioClienteClasseA', 'label' => 'Ticket médio', 'type' => 'moeda'),
					'ticketMedioClienteClasseB' => array('name' => 'ticketMedioClienteClasseB', 'label' => 'Ticket médio', 'type' => 'moeda'),
					'ticketMedioClienteClasseC' => array('name' => 'ticketMedioClienteClasseC', 'label' => 'Ticket médio', 'type' => 'moeda'),
					'ticketMedioClienteClasseD' => array('name' => 'ticketMedioClienteClasseD', 'label' => 'Ticket médio', 'type' => 'moeda'),
					'diasAtrasoPgtoClienteClasseA' => array('name' => 'diasAtrasoPgtoClienteClasseA', 'label' => 'Maior atraso dias', 'type' => 'text'),
					'diasAtrasoPgtoClienteClasseB' => array('name' => 'diasAtrasoPgtoClienteClasseB', 'label' => 'Maior atraso dias', 'type' => 'text'),
					'diasAtrasoPgtoClienteClasseC' => array('name' => 'diasAtrasoPgtoClienteClasseC', 'label' => 'Maior atraso dias', 'type' => 'text'),
					'diasAtrasoPgtoClienteClasseD' => array('name' => 'diasAtrasoPgtoClienteClasseD', 'label' => 'Maior atraso dias', 'type' => 'text'),
					'mesesCadastroClienteClasseA' => array('name' => 'mesesCadastroClienteClasseA', 'label' => 'Meses cadastrado', 'type' => 'text'),
					'mesesCadastroClienteClasseB' => array('name' => 'mesesCadastroClienteClasseB', 'label' => 'Meses cadastrado', 'type' => 'text'),
					'mesesCadastroClienteClasseC' => array('name' => 'mesesCadastroClienteClasseC', 'label' => 'Meses cadastrado', 'type' => 'text'),
					'mesesCadastroClienteClasseD' => array('name' => 'mesesCadastroClienteClasseD', 'label' => 'Meses cadastrado', 'type' => 'text'),
					'prazoVendaClienteClasseA' => array('name' => 'prazoVendaClienteClasseA', 'label' => 'Intervalo <br>vendas dias', 'type' => 'text'),
					'prazoVendaClienteClasseB' => array('name' => 'prazoVendaClienteClasseB', 'label' => 'Intervalo <br>vendas dias', 'type' => 'text'),
					'prazoVendaClienteClasseC' => array('name' => 'prazoVendaClienteClasseC', 'label' => 'Intervalo <br>vendas dias', 'type' => 'text'),
					'prazoVendaClienteClasseD' => array('name' => 'prazoVendaClienteClasseD', 'label' => 'Intervalo <br>vendas dias', 'type' => 'text'),
					'percentualAtrasoClienteClasseA' => array('name' => 'percentualAtrasoClienteClasseA', 'label' => 'Perc. de atrasos', 'type' => 'text'),
					'percentualAtrasoClienteClasseB' => array('name' => 'percentualAtrasoClienteClasseB', 'label' => 'Perc. de atrasos', 'type' => 'text'),
					'percentualAtrasoClienteClasseC' => array('name' => 'percentualAtrasoClienteClasseC', 'label' => 'Perc. de atrasos', 'type' => 'text'),
					'percentualAtrasoClienteClasseD' => array('name' => 'percentualAtrasoClienteClasseD', 'label' => 'Perc. de atrasos', 'type' => 'text'),
					'permiteTransportarDiferentesUF' => array('name' => 'permiteTransportarDiferentesUF', 'label' => 'Faturar em UF de entrega diferente da do faturamento?', 'type' => 'select', 'select' => 
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'qtdTitulosAtrasoValidarMaiorAtrasoClasseA' => array('name' => 'qtdTitulosAtrasoValidarMaiorAtrasoClasseA', 'label' => 'Qtd títulos<br>Maior Atraso', 'type' => 'moeda', 'casasDecimais' => 0),
					'qtdTitulosAtrasoValidarMaiorAtrasoClasseB' => array('name' => 'qtdTitulosAtrasoValidarMaiorAtrasoClasseB', 'label' => 'Qtd títulos<br>Maior Atraso', 'type' => 'moeda', 'casasDecimais' => 0),
					'qtdTitulosAtrasoValidarMaiorAtrasoClasseC' => array('name' => 'qtdTitulosAtrasoValidarMaiorAtrasoClasseC', 'label' => 'Qtd títulos<br>Maior Atraso', 'type' => 'moeda', 'casasDecimais' => 0),
					'qtdTitulosAtrasoValidarMaiorAtrasoClasseD' => array('name' => 'qtdTitulosAtrasoValidarMaiorAtrasoClasseD', 'label' => 'Qtd títulos<br>Maior Atraso', 'type' => 'moeda', 'casasDecimais' => 0),
					'separarClientePorEmpresa' => array('name' => 'separarClientePorEmpresa', 'label' => 'Separar clientes por empresa', 'type' => 'select', 'select' => 
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
				),
			)
		);
		//if($g['empresaAtual'] == '1') {
			$formArray['campos']['clientesForn']['ClasseA'] = array('name' => 'ClasseA', 'label'=>'Classe A','type' => 'titulo', 'reject' => true, 'reload' => false);
			$formArray['campos']['clientesForn']['ClasseB'] = array('name' => 'ClasseB', 'label'=>'Classe B','type' => 'titulo', 'reject' => true, 'reload' => false);
			$formArray['campos']['clientesForn']['ClasseC'] = array('name' => 'ClasseC', 'label'=>'Classe C','type' => 'titulo', 'reject' => true, 'reload' => false);
			$formArray['campos']['clientesForn']['ClasseD'] = array('name' => 'ClasseD', 'label'=>'Classe D','type' => 'titulo', 'reject' => true, 'reload' => false);
		//}
		echo <<<EOT
            <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                $('#{$g['divId']} #tipoCusto').change(function() {
                    if($(this).val() == 'E') {
                        $('#{$g['divId']} #custoPrincipal').closest('.formEditor').show();
                        $('#{$g['divId']} #prazoCustoPrincipal').closest('.formEditor').show();
                        $('#{$g['divId']} #custoSecundario').closest('.formEditor').show();
                        $('#{$g['divId']} #prazoCustoSecundario').closest('.formEditor').show();
                        $('#{$g['divId']} #custoZerado').closest('.formEditor').show();
						//$('#{$g['divId']} #estoqueUtilizadoFormarCusto').closest('.formEditor').show();
                        $('#{$g['divId']} #custoMaisRecenteUtilizado').closest('.formEditor').hide();
                    }else{
                    	$('#{$g['divId']} #custoPrincipal').closest('.formEditor').hide();
                        $('#{$g['divId']} #prazoCustoPrincipal').closest('.formEditor').hide();
                        $('#{$g['divId']} #custoSecundario').closest('.formEditor').hide();
                        $('#{$g['divId']} #prazoCustoSecundario').closest('.formEditor').hide();
                        $('#{$g['divId']} #custoZerado').closest('.formEditor').hide();
						//$('#{$g['divId']} #estoqueUtilizadoFormarCusto').closest('.formEditor').hide();
                        $('#{$g['divId']} #custoMaisRecenteUtilizado').closest('.formEditor').show();
                    }
                });
				$('#{$r_formId} #tipoCusto').trigger('change');
            } </script></div>
EOT;

		$arrayObservacoes = array();
		$arrayDiff = array();
		$sqlC031 = mysql_query("SELECT C031_Campo, C031_Observacao, C031_Valor, C031_Valor_Padrao FROM C031 GROUP BY C031_Campo");
		while($rowC031 = mysql_fetch_array($sqlC031)){
			$arrayObservacoes[$rowC031['C031_Campo']] = $rowC031['C031_Observacao'];
			if($rowC031['C031_Valor'] != $rowC031['C031_Valor_Padrao']){
				$arrayDiff[] = $rowC031['C031_Campo'];
			}
		}

		foreach ($formArray['campos'] as $keyTab => &$camposTab) {
			foreach ($camposTab as $keyCampo => &$campoFormulario) {
				$campoFormulario['extra'] = 'title="'.$arrayObservacoes[$keyCampo].'"';
				if(in_array($keyCampo, $arrayDiff)){
					if(empty($campoFormulario['label'])) { continue; }
					$campoFormulario['label'] = "<font color=\"blue\">".$campoFormulario['label']."</font>";
				}
			}
		}
		unset($camposTab);
		unset($campoFormulario);
		$form->definirFormularioArray($formArray);

		/**
		* PARTE 3: Processamento do formulário (se foi enviado)
		* 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		*/
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
			// Form sem erros
			} else if ($formCodigo == 2) {
				// Update
				require_once('bibliotecas/classes/CAD058.php');
                $CAD058 = new CAD058();
                $retorno = $CAD058->atualizarConfGlobal($form, $g['empresaAtual']);
				if ($retorno == true) {
					echo gGeraAlertMsg('Operação OK', 'Configurações salvas com sucesso!');
				}
			}
		} else {
			// Carrega os valores caso seja um update
			$sql = mysql_query("SELECT * FROM C031 WHERE C031_C004_Id = '{$g['empresaAtual']}'");
			while($row = mysql_fetch_array($sql)){
				if(strpos($row['C031_Campo'], "ticketMedioClienteClasse") !== false || strpos($row['C031_Campo'], "mediaVendaMesClienteClasse") !== false){
					$row['C031_Valor'] = gCorrigeNumero($row['C031_Valor']);
				}
				$form->campoSetarValor($row['C031_Campo'], $row['C031_Valor']);
			}
		}
		
		/**
		* PARTE 4: Gerar o formulário
		*/
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);
		
		/**
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo  $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		echo $g['smarty']->fetch('formEditor.tpl');
	break;

	case 'cad058FormConfVendas':
		global $g;

		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		
		/**
		* PARTE 2: Definição do formulário
		*/

    	// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = true;
		if ($formMultiTab) {
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}
		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = '0000';
		$form_campo_pk = '0000_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad058FormConfVendas_Btn';
		// Form que será executado ao clicar no
		$enviar_acaoHref = '/cad/cad058/form/cad058FormConfVendas/';
		// formEditor
		// Form Editor
		$form_editor = array(
			'geral' => '{"exibirLimitesOrcamentoPedido":{"top":7,"left":105.859375,"width":64,"hidden":0},"carteiraClienteDiasMaxContato":{"top":7,"left":578.875,"width":56,"hidden":0},"CRMNomeTab":{"top":30,"left":117.34375,"width":128,"hidden":0},"carteiraClienteDiasMaxOrcamento":{"top":30,"left":563.375,"width":56,"hidden":0},"precopadraoVendaProduto":{"top":53,"left":87.359375,"width":128,"hidden":0},"carteiraClienteDiasMaxPedido":{"top":53,"left":582.953125,"width":56,"hidden":0},"editarDescricaoProdutoVenda":{"top":76,"left":98.875,"width":64,"hidden":0},"carteiraClienteDiasMaxNotaFiscal":{"top":76,"left":565.5,"width":56,"hidden":0},"editarUnidadeProdutoVenda":{"top":99,"left":107.71875,"width":64,"hidden":0},"alterarVendedor":{"top":99,"left":602.84375,"width":64,"hidden":0},"botaoAplicarDesconto":{"top":122,"left":103.46875,"width":64,"hidden":0},"alterarVendedorAusente":{"top":122,"left":600.578125,"width":64,"hidden":0},"percentualPrecoMinimoVenda":{"top":145,"left":96.203125,"width":56,"hidden":0},"utilizarDescProdutoUltimaVenda":{"top":145,"left":597.796875,"width":64,"hidden":0},"totalizarPesoVenda":{"top":168,"left":102.71875,"width":64,"hidden":0},"permitirProdutosDuplicadosVenda":{"top":168,"left":621.015625,"width":64,"hidden":0},"somarICMSPrecoVenda":{"top":191,"left":65.1875,"width":64,"hidden":0},"zerarIPIProdutoNaoProduzido":{"top":191,"left":644.75,"width":64,"hidden":0},"somarPISPrecoVenda":{"top":214,"left":75.015625,"width":64,"hidden":0},"informarEstoqueInsuficiente":{"top":214,"left":619.21875,"width":64,"hidden":0},"somarCOFINSPrecoVenda":{"top":237,"left":53.3125,"width":64,"hidden":0},"venderEstoqueMatriz":{"top":237,"left":637.375,"width":64,"hidden":0},"somarIRPJPrecoVenda":{"top":260,"left":69.78125,"width":64,"hidden":0},"habilitarDescontoPercentual":{"top":260,"left":600.515625,"width":64,"hidden":0},"somarCSLLPrecoVenda":{"top":283,"left":65.953125,"width":64,"hidden":0},"habilitarDescontoReais":{"top":283,"left":628.328125,"width":64,"hidden":0},"C004PrecoTransferenciaGridProdutos":{"top":306,"left":41.21875,"width":255,"hidden":0},"habilitarPercentualDescontoValorTotal":{"top":306,"left":552.296875,"width":63,"hidden":0},"habilitarDescontoValorTotal":{"top":329,"left":62.59375,"width":64,"hidden":0},"habilitarDefinirValorTotal":{"top":329,"left":535.484375,"width":64,"hidden":0},"utilizarPrecoTabelaCliente":{"top":352,"left":50.625,"width":64,"hidden":0},"habilitarInclusaoSubItensMontagem":{"top":352,"left":532.765625,"width":64,"hidden":0},"habilitarInclusaoProdutosPesquisaGrid":{"top":375,"left":76.609375,"width":64,"hidden":0},"ratearFreteVendaNoCusto":{"top":375,"left":510.4375,"width":64,"hidden":0},"exibirDescontoImpressaoOrcPed":{"top":398,"left":74.34375,"width":64,"hidden":0},"definirDestinoVendaTipoCliente":{"top":398,"left":536.46875,"width":64,"hidden":0},"exibirMarcaImpressaoVenda":{"top":421,"left":88.921875,"width":64,"hidden":0},"atualizarAliquotaUnidadeProduto":{"top":421,"left":555.84375,"width":64,"hidden":0},"exibirFotoImpressaoVenda":{"top":444,"left":100.0625,"width":64,"hidden":0},"mostrarDescontoProdutosAcrescimo":{"top":444,"left":449.203125,"width":64,"hidden":0},"vendasZoomFonteImprimirPDF":{"top":467,"left":23.0625,"width":56,"hidden":0},"obrigatorioNumOCItemOC":{"top":467,"left":468.109375,"width":64,"hidden":0},"margemImprimirPDF":{"top":490,"left":98.4375,"width":56,"hidden":0},"obrigatorioCodSubstitutoFaturamento":{"top":489.984375,"left":547.4375,"width":63,"hidden":0},"padraoFaixaCalculoIcmsCredito":{"top":513,"left":82.578125,"width":607,"hidden":0},"exibirHistoricoFormVendaUltimoOrcamento":{"top":536,"left":477.3125,"width":63,"hidden":0},"utilizaUnidadeConversaoVenda":{"top":559,"left":90.65625,"width":95,"hidden":0},"exibirHistoricoFormVendaUltimoPedido":{"top":559,"left":496.90625,"width":63,"hidden":0},"custoUtilizadoNaVenda":{"top":582,"left":95.90625,"width":95,"hidden":0},"exibirHistoricoFormVendaUltimaNotaFiscal":{"top":582,"left":516.328125,"width":63,"hidden":0},"gerarAtividadesManipulacaoItens":{"top":605,"left":394.1875,"width":63,"hidden":0},"atualizaCFOPAliquotasPrecoAoEntrarVendaPendente":{"top":628,"left":414.109375,"width":63,"hidden":0},"desabilitarEdicaoPortador":{"top":651,"left":497.109375,"width":63,"hidden":0},"habilitaNotificacoesGenerico":{"top":674,"left":474.8125,"width":63,"hidden":0},"duplicarPedidoOrcamentoAtualizaPrecoTabela":{"top":697,"left":367.3125,"width":63,"hidden":0}}',
			'orcamento' => '{"diasMaxOrcamentoPendente":{"top":7,"left":150.51666259765625,"width":56.433334,"hidden":0},"orcamentoCarregarPrimeiroContato":{"top":7,"left":499.54998779296875,"width":111,"hidden":0},"usarAlcada":{"top":30,"left":191.04998779296875,"width":64,"hidden":0},"habilitarAplicarPrecoKgOrcamento":{"top":30,"left":543.1333312988281,"width":111,"hidden":0},"formatoOrcamentoEmail":{"top":53,"left":156.04998779296875,"width":64,"hidden":0},"permitePedidoProdutoPreCadastro":{"top":53,"left":518.5500183105469,"width":111,"hidden":0},"permitirVendaSemEstoque":{"top":76,"left":153.89999389648438,"width":64,"hidden":0},"verificaCreditoLiberadoGerarPedido":{"top":76,"left":497.9666442871094,"width":111,"hidden":0},"exibirMsgCotarOrcamento":{"top":99,"left":140.59999084472656,"width":64,"hidden":0},"obrigatorioGerarParcelaOrcamento":{"top":99,"left":466.0833435058594,"width":111,"hidden":0},"permitirPrecoZeradoOrcamento":{"top":122.00001525878906,"left":109.93333435058594,"width":64,"hidden":0},"verificaAlcadaOrcamentoEmailImpressao":{"top":122.00001525878906,"left":484.8999938964844,"width":111,"hidden":0},"orcamentoAtualizaTabelaPrecoCliente":{"top":145.00001525878906,"left":44.33332824707031,"width":64,"hidden":0},"obrigatorioLocacaoItemComValidade":{"top":145.00001525878906,"left":472.8500061035156,"width":111,"hidden":0},"atualizaPrecoTabelaOrcamento":{"top":168.00001525878906,"left":54.75,"width":64,"hidden":0},"mantemPrecoItemComoTabela":{"top":168.00001525878906,"left":376.25,"width":111,"hidden":0},"habilitaEnsaioOrcamento":{"top":191.00001525878906,"left":127.21665954589844,"width":63,"hidden":0},"bloqueioOrcamentoConfirmado":{"top":191.00001525878906,"left":488.54998779296875,"width":111,"hidden":0},"habilitaRegistrodeAcesso":{"top":214.00001525878906,"left":148.34999084472656,"width":63,"hidden":0},"dataVencimentoParcelasAoGerarPedido":{"top":214.00001525878906,"left":457.4333190917969,"width":112,"hidden":0},"prazoPrecoCotacaoNoOrcamento":{"top":237.00001525878906,"left":179.63333129882812,"width":56.433334,"hidden":1},"mantemMesmasParcelasOrcamentoAoGerarPedido":{"top":260.00001525878906,"left":416.9666442871094,"width":63,"hidden":1},"permiteBuscarCadastroItem":{"top":283.00001525878906,"left":33.46665954589844,"width":50,"hidden":1},"permitirOrcamentoEmailImpSemPagamento":{"top":283.00001525878906,"left":484.5833435058594,"width":59,"hidden":1},"permitePedidoClientePreCadastro":{"top":306.00001525878906,"left":520.3000183105469,"width":111,"hidden":0}}',
			'pedido' => '{"statusInicialPedido":{"top":7,"left":71.890625,"width":176,"hidden":0},"usarOrdemCompra":{"top":7,"left":535.65625,"width":64,"hidden":0},"pedidoGerarNFEmQualquerStatus":{"top":30,"left":38.796875,"width":64,"hidden":0},"permissaoFaturarSemPreco":{"top":30,"left":527.375,"width":64,"hidden":0},"permissaoFaturarEmergencia":{"top":53,"left":75.734375,"width":64,"hidden":0},"usarTransportadoraPedido":{"top":53,"left":549.234375,"width":64,"hidden":0},"diasVencPedido":{"top":76,"left":58.140625,"width":56,"hidden":0},"FaturarPedidoAposAnalisePedido":{"top":76,"left":580.15625,"width":64,"hidden":0},"usarBotaoIncluirPedido":{"top":99,"left":34.59375,"width":64,"hidden":0},"obrigatorioDataEntregaPedido":{"top":99,"left":575.515625,"width":64,"hidden":0},"permitirFaturarSemEstoque":{"top":122,"left":69.859375,"width":64,"hidden":0},"formatoPedidoEmail":{"top":122,"left":623.671875,"width":64,"hidden":0},"usarControleCredito":{"top":145,"left":40.390625,"width":64,"hidden":0},"calcularVolumePedido":{"top":145,"left":601.015625,"width":64,"hidden":0},"prazoInicialPedidoPeriodico":{"top":168,"left":15.609375,"width":56,"hidden":0},"pedidoAtualizaTabelaPrecoCliente":{"top":168,"left":512.640625,"width":64,"hidden":0},"prazoFinalPedidoPeriodico":{"top":191,"left":23.28125,"width":56,"hidden":0},"permiteRecuperarPedidoCancelado":{"top":191,"left":548.734375,"width":64,"hidden":0},"quantidadePadraoVolumePedido":{"top":214,"left":33.484375,"width":56,"hidden":0},"horarioLimiteGeracaoPedido":{"top":214,"left":541.4375,"width":56,"hidden":0},"validarNFEFaturarPedido":{"top":237,"left":19.46875,"width":64,"hidden":0},"atualizaPrecoTabelaPedido":{"top":237,"left":502.90625,"width":64,"hidden":0},"habilitaLiberacaoVendas":{"top":260,"left":538.109375,"width":64,"hidden":0}}',
			'geralNotaFiscal' => '{"permitirFinalizarNotaSemEstoque":{"top":282.996337890625,"left":31.893383979797363,"width":79,"hidden":0},"camposDescricaoProdutoNF":{"top":6.9853057861328125,"left":34.28308963775635,"width":600.0392159999999,"hidden":0},"obrigatorioEnvioDanfeViaEmail":{"top":29.993881225585938,"left":35.64644718170166,"width":79,"hidden":0},"NFeEnviarEmailIncluirContatosCC":{"top":29.993881225585938,"left":528.7990159988403,"width":64,"hidden":0},"exibirValorNFDANFE":{"top":52.98713684082031,"left":46.93627643585205,"width":79,"hidden":0},"exibirObservacaoOCnaNF":{"top":52.98713684082031,"left":487.6684923171997,"width":64,"hidden":0},"imprimeInformacoesAdicionaisNFCe":{"top":75.9957275390625,"left":0.45955848693847656,"width":79,"hidden":0},"exibirObservacaoOCPorItemnaNF":{"top":75.9957275390625,"left":444.3167772293091,"width":63,"hidden":0},"imprimirValorIPInaNFCe":{"top":98.98898315429688,"left":75.2910509109497,"width":79,"hidden":0},"exibirObservacaoPedidonaNF":{"top":98.98898315429688,"left":485.43198108673096,"width":64,"hidden":0},"exibirNoDanfeValorTribTransp":{"top":121.99755859375,"left":48.71323490142822,"width":79,"hidden":0},"exibirObservacaoVendedornaNF":{"top":121.99755859375,"left":528.3548021316528,"width":64,"hidden":0},"NFeEnviarEmailTransportadora":{"top":0,"left":0,"width":71.96078399999999,"hidden":1},"dataSaidaDefault":{"top":144.99081420898438,"left":99.01960468292236,"width":72.03921600000001,"hidden":0},"exibirObservacaoAreaClientenaNF":{"top":144.99081420898438,"left":502.26715564727783,"width":64,"hidden":0},"NFeModificarTituloEmail":{"top":0,"left":0,"width":71.96078399999999,"hidden":1},"NFeEnviarEmailAutomatico":{"top":167.9993896484375,"left":28.860295295715332,"width":79,"hidden":0},"exibirObservacaoContatonaNF":{"top":167.9993896484375,"left":536.3051195144653,"width":64,"hidden":0},"NFeEnviarEmailVendedorInterno":{"top":0,"left":0,"width":71.96078399999999,"hidden":1},"EnviarDanfeBoletoMesmoEmail":{"top":351.991455078125,"left":20.266545295715332,"width":79,"hidden":0},"BoletosEnviarEmailAutomatico":{"top":190.99264526367188,"left":20.266545295715332,"width":79,"hidden":0},"emitirNFCDataHoraAtual":{"top":190.99264526367188,"left":479.76410388946533,"width":64,"hidden":0},"NFeEnviarEmailPessoaProposta":{"top":0,"left":0,"width":71.96078399999999,"hidden":1},"enviarBoletoProtegido":{"top":213.98590087890625,"left":35.63112735748291,"width":79,"hidden":0},"exibirTipoFreteNaNF":{"top":213.98590087890625,"left":514.6905565261841,"width":64,"hidden":0},"validarNFEGerarNotaFiscal":{"top":236.99447631835938,"left":42.24877643585205,"width":79,"hidden":0},"NFeEnviarEmailRepresentante":{"top":0,"left":0,"width":71.96078399999999,"hidden":1},"inserirNumeroNFeIncluir":{"top":259.9877624511719,"left":35.63112735748291,"width":79,"hidden":0},"notaAtualizaTabelaPrecoCliente":{"top":236.99447631835938,"left":510.1256151199341,"width":64,"hidden":0},"totalizarPesoNFeDevolucao":{"top":282.996337890625,"left":18.16789150238037,"width":79,"hidden":0},"exibirBotaoTransmitirNota":{"top":259.9877624511719,"left":564.4607591629028,"width":64,"hidden":0},"imprimirCanhotoDanfe":{"top":305.9895935058594,"left":87.25490283966064,"width":127,"hidden":0},"adicionarPreenchimentoCPFRGCanhoto":{"top":282.996337890625,"left":489.61395740509033,"width":63,"hidden":0},"nomeClienteConsumidorFinal":{"top":328.9981689453125,"left":38.09742832183838,"width":120.03921600000001,"hidden":0},"adicionarNomeFantasiaClienteCanhotoDanfe":{"top":305.9895935058594,"left":481.08148670196533,"width":64,"hidden":0},"imprimirNoDanfeEanNoLugarDoCodigo":{"top":328.9981689453125,"left":488.6335802078247,"width":64,"hidden":0},"habilitarEnvioNFeLote":{"top":0,"left":0,"width":86.96078399999999,"hidden":1},"alertaVencimentoCertificadoDigital1":{"top":351.991455078125,"left":542.2028245925903,"width":56.039215999999996,"hidden":0},"alertaVencimentoCertificadoDigital2":{"top":375,"left":539.4760789871216,"width":56.039215999999996,"hidden":0},"imprimirDuplicatasPortadorTipoECommerce":{"top":397.9932861328125,"left":428.9368944168091,"width":64,"hidden":0},"qtdeFaltaNFTravarCliente":{"top":420.98651123046875,"left":431.83209705352783,"width":56.039215999999996,"hidden":0},"exibirObservacaoClienteNF":{"top":443.9951171875,"left":488.95526599884033,"width":63,"hidden":0},"anexoEmailCertificadoQualidade":{"top":0,"left":0,"width":71.96078399999999,"hidden":1}}',
			'gnreNotaFiscal' => '{"prazoDiasVencimentoGnreST":{"top":30,"left":120.765625,"width":56,"hidden":0},"gerarStContasPagar":{"top":7,"left":9.46875,"width":64,"hidden":0},"tituloGnreNotaFiscal":{"top":51,"left":234,"width":null,"hidden":1},"grupoSTContasPagar":{"top":76,"left":202.296875,"width":400,"hidden":0},"contaSTContasPagar":{"top":99,"left":203.515625,"width":400,"hidden":0},"portadorSTContasPagar":{"top":122,"left":190.671875,"width":400,"hidden":0},"centroCustoSTContasPagar":{"top":145,"left":170.5,"width":400,"hidden":0}}',
			'emailNotaFiscal' => '{"tituloEmailNotaFiscal":{"top":3.984375,"left":85,"width":null,"hidden":1},"NFe_Email":{"top":30,"left":108.33984375,"width":295.5,"hidden":0},"NFe_Email_SMTP_Usuario":{"top":52.98828125,"left":101.07421875,"width":295.5,"hidden":0},"NFe_Email_SMTP_Senha":{"top":75.99609375,"left":108.125,"width":295.5,"hidden":0},"NFe_Email_SMTP_Servidor":{"top":121.9921875,"left":66.015625,"width":295.5,"hidden":0},"NFe_Email_SMTP_Porta":{"top":145,"left":79.66796875,"width":55.5,"hidden":0},"NFe_Email_SMTP_Seguranca":{"top":145,"left":246.50390625,"width":111,"hidden":0},"NFe_Email_SMTP_Autenticacao":{"top":167.98828125,"left":73.88671875,"width":55.5,"hidden":0}}',
			'cupomFiscal' => '{"codigoClienteCupomFiscal":{"top":7,"left":153.78125,"width":489,"hidden":0},"cfopCupomFiscalComST":{"top":30,"left":214.65625,"width":511,"hidden":0},"cfopCupomFiscalSemST":{"top":53,"left":215.1875,"width":511,"hidden":0},"documentoPadraoPDV":{"top":76,"left":165.390625,"width":159,"hidden":0},"precoEtiquetaGondola":{"top":76,"left":505.28125,"width":176,"hidden":0},"atualizaPrecoLojaAutomatico":{"top":122,"left":55.21875,"width":55,"hidden":0},"permiteGerarNFCPessoaJuridica":{"top":122,"left":549.71875,"width":55,"hidden":0},"gerarNumeroNFCeAutomaticoInserirPDV":{"top":145,"left":23.09375,"width":55,"hidden":0},"permiteImportarDavQualquerStatus":{"top":145,"left":543.109375,"width":55,"hidden":0},"removerNFCeDaFilaEnvioAposTransmitir":{"top":168,"left":28.4375,"width":55,"hidden":0},"alterarItemPdvVinculadoDav":{"top":168,"left":531.421875,"width":55,"hidden":0},"PDVTransmitirNFCeAutomaticamente":{"top":191,"left":22.71875,"width":55,"hidden":0},"habilitaUpdateVendedor":{"top":191,"left":481.328125,"width":55,"hidden":0},"ativarPagamentosPDVNota":{"top":214,"left":127.125,"width":55,"hidden":0},"desabilitarExpedicaoPDV":{"top":214,"left":514,"width":55,"hidden":0},"permiteImportarVariasDavPdv":{"top":237,"left":525.453125,"width":55,"hidden":0},"permiteAbrirNFCompletaComClientePadraoPDV":{"top":237,"left":18.84375,"width":55,"hidden":0},"habitaOpcoesDiversasPDV":{"top":260,"left":591.84375,"width":55,"hidden":0},"alterarDocumentoParaNFEAoAbrirCompleta":{"top":260,"left":32.390625,"width":55,"hidden":0},"abrirGavetaManual":{"top":283,"left":575,"width":55,"hidden":0},"prazoVencimentoParcelaPdvPortador":{"top":283,"left":94.59375,"width":55,"hidden":0}}',
			'alcadaVenda' => '{"alcadaVenda_descontoMinimoSupervisorAVista":{"top":237,"left":240,"width":56,"hidden":0},"alcadaVenda_valorInicial01":{"top":7,"left":17,"width":72,"hidden":0},"alcadaVenda_valorFinal01":{"top":7,"left":144,"width":72,"hidden":0},"alcadaVenda_qtdeParcelas01":{"top":7,"left":240,"width":24,"hidden":0},"alcadaVenda_prazoMedio01":{"top":7,"left":335,"width":40,"hidden":0},"alcadaVenda_portador01":{"top":7,"left":465,"width":232,"hidden":0},"alcadaVenda_descontoMaximoSupervisorAVista":{"top":260,"left":234,"width":56,"hidden":0},"alcadaVenda_valorInicial02":{"top":30,"left":17,"width":72,"hidden":0},"alcadaVenda_valorFinal02":{"top":30,"left":144,"width":72,"hidden":0},"alcadaVenda_qtdeParcelas02":{"top":30,"left":240,"width":24,"hidden":0},"alcadaVenda_prazoMedio02":{"top":30,"left":335,"width":40,"hidden":0},"alcadaVenda_portador02":{"top":30,"left":465,"width":232,"hidden":0},"alcadaVenda_valorInicial03":{"top":53,"left":17,"width":72,"hidden":0},"alcadaVenda_valorFinal03":{"top":53,"left":144,"width":72,"hidden":0},"alcadaVenda_qtdeParcelas03":{"top":53,"left":240,"width":24,"hidden":0},"alcadaVenda_prazoMedio03":{"top":53,"left":335,"width":40,"hidden":0},"alcadaVenda_portador03":{"top":53,"left":465,"width":232,"hidden":0},"alcadaVenda_valorInicial04":{"top":76,"left":17,"width":72,"hidden":0},"alcadaVenda_valorFinal04":{"top":76,"left":144,"width":72,"hidden":0},"alcadaVenda_qtdeParcelas04":{"top":76,"left":240,"width":24,"hidden":0},"alcadaVenda_prazoMedio04":{"top":76,"left":335,"width":40,"hidden":0},"alcadaVenda_portador04":{"top":76,"left":465,"width":232,"hidden":0},"alcadaVenda_valorInicial05":{"top":99,"left":17,"width":72,"hidden":0},"alcadaVenda_valorFinal05":{"top":99,"left":144,"width":72,"hidden":0},"alcadaVenda_qtdeParcelas05":{"top":99,"left":240,"width":24,"hidden":0},"alcadaVenda_prazoMedio05":{"top":99,"left":335,"width":40,"hidden":0},"alcadaVenda_portador05":{"top":99,"left":465,"width":232,"hidden":0},"alcadaVenda_valorInicial06":{"top":122,"left":17,"width":72,"hidden":0},"alcadaVenda_valorFinal06":{"top":122,"left":144,"width":72,"hidden":0},"alcadaVenda_qtdeParcelas06":{"top":122,"left":240,"width":24,"hidden":0},"alcadaVenda_prazoMedio06":{"top":122,"left":335,"width":40,"hidden":0},"alcadaVenda_portador06":{"top":122,"left":465,"width":232,"hidden":0},"alcadaVenda_valorInicial07":{"top":145,"left":17,"width":72,"hidden":0},"alcadaVenda_valorFinal07":{"top":145,"left":144,"width":72,"hidden":0},"alcadaVenda_qtdeParcelas07":{"top":145,"left":240,"width":24,"hidden":0},"alcadaVenda_prazoMedio07":{"top":145,"left":335,"width":40,"hidden":0},"alcadaVenda_portador07":{"top":145,"left":465,"width":232,"hidden":0},"alcadaVenda_valorInicial08":{"top":168,"left":17,"width":72,"hidden":0},"alcadaVenda_valorFinal08":{"top":168,"left":144,"width":72,"hidden":0},"alcadaVenda_qtdeParcelas08":{"top":168,"left":240,"width":24,"hidden":0},"alcadaVenda_prazoMedio08":{"top":168,"left":335,"width":40,"hidden":0},"alcadaVenda_portador08":{"top":168,"left":465,"width":232,"hidden":0},"alcadaVenda_valorInicial09":{"top":191,"left":17,"width":72,"hidden":0},"alcadaVenda_valorFinal09":{"top":191,"left":144,"width":72,"hidden":0},"alcadaVenda_qtdeParcelas09":{"top":191,"left":240,"width":24,"hidden":0},"alcadaVenda_prazoMedio09":{"top":191,"left":335,"width":40,"hidden":0},"alcadaVenda_portador09":{"top":191,"left":465,"width":232,"hidden":0},"alcadaVenda_valorInicial10":{"top":214,"left":17,"width":72,"hidden":0},"alcadaVenda_valorFinal10":{"top":214,"left":144,"width":72,"hidden":0},"alcadaVenda_qtdeParcelas10":{"top":214,"left":240,"width":24,"hidden":0},"alcadaVenda_prazoMedio10":{"top":214,"left":335,"width":40,"hidden":0},"alcadaVenda_portador10":{"top":214,"left":465,"width":232,"hidden":0},"alcadaVenda_descontoMinimoSupervisor":{"top":237,"left":19,"width":56,"hidden":0},"alcadaVenda_descontoMaximoSupervisor":{"top":260,"left":13,"width":56,"hidden":0}}',
			'alcadaVendaSupervisor' => '{"alcadaVendaSupervisor_descontoMinimoSupervisorAVista":{"top":237,"left":240,"width":56,"hidden":0},"alcadaVendaSupervisor_valorInicial01":{"top":7,"left":17,"width":72,"hidden":0},"alcadaVendaSupervisor_valorFinal01":{"top":7,"left":144,"width":72,"hidden":0},"alcadaVendaSupervisor_qtdeParcelas01":{"top":7,"left":240,"width":24,"hidden":0},"alcadaVendaSupervisor_prazoMedio01":{"top":7,"left":335,"width":40,"hidden":0},"alcadaVendaSupervisor_portador01":{"top":7,"left":465,"width":232,"hidden":0},"alcadaVendaSupervisor_descontoMaximoSupervisorAVista":{"top":260,"left":234,"width":56,"hidden":0},"alcadaVendaSupervisor_valorInicial02":{"top":30,"left":17,"width":72,"hidden":0},"alcadaVendaSupervisor_valorFinal02":{"top":30,"left":144,"width":72,"hidden":0},"alcadaVendaSupervisor_qtdeParcelas02":{"top":30,"left":240,"width":24,"hidden":0},"alcadaVendaSupervisor_prazoMedio02":{"top":30,"left":335,"width":40,"hidden":0},"alcadaVendaSupervisor_portador02":{"top":30,"left":465,"width":232,"hidden":0},"alcadaVendaSupervisor_valorInicial03":{"top":53,"left":17,"width":72,"hidden":0},"alcadaVendaSupervisor_valorFinal03":{"top":53,"left":144,"width":72,"hidden":0},"alcadaVendaSupervisor_qtdeParcelas03":{"top":53,"left":240,"width":24,"hidden":0},"alcadaVendaSupervisor_prazoMedio03":{"top":53,"left":335,"width":40,"hidden":0},"alcadaVendaSupervisor_portador03":{"top":53,"left":465,"width":232,"hidden":0},"alcadaVendaSupervisor_valorInicial04":{"top":76,"left":17,"width":72,"hidden":0},"alcadaVendaSupervisor_valorFinal04":{"top":76,"left":144,"width":72,"hidden":0},"alcadaVendaSupervisor_qtdeParcelas04":{"top":76,"left":240,"width":24,"hidden":0},"alcadaVendaSupervisor_prazoMedio04":{"top":76,"left":335,"width":40,"hidden":0},"alcadaVendaSupervisor_portador04":{"top":76,"left":465,"width":232,"hidden":0},"alcadaVendaSupervisor_valorInicial05":{"top":99,"left":17,"width":72,"hidden":0},"alcadaVendaSupervisor_valorFinal05":{"top":99,"left":144,"width":72,"hidden":0},"alcadaVendaSupervisor_qtdeParcelas05":{"top":99,"left":240,"width":24,"hidden":0},"alcadaVendaSupervisor_prazoMedio05":{"top":99,"left":335,"width":40,"hidden":0},"alcadaVendaSupervisor_portador05":{"top":99,"left":465,"width":232,"hidden":0},"alcadaVendaSupervisor_valorInicial06":{"top":122,"left":17,"width":72,"hidden":0},"alcadaVendaSupervisor_valorFinal06":{"top":122,"left":144,"width":72,"hidden":0},"alcadaVendaSupervisor_qtdeParcelas06":{"top":122,"left":240,"width":24,"hidden":0},"alcadaVendaSupervisor_prazoMedio06":{"top":122,"left":335,"width":40,"hidden":0},"alcadaVendaSupervisor_portador06":{"top":122,"left":465,"width":232,"hidden":0},"alcadaVendaSupervisor_valorInicial07":{"top":145,"left":17,"width":72,"hidden":0},"alcadaVendaSupervisor_valorFinal07":{"top":145,"left":144,"width":72,"hidden":0},"alcadaVendaSupervisor_qtdeParcelas07":{"top":145,"left":240,"width":24,"hidden":0},"alcadaVendaSupervisor_prazoMedio07":{"top":145,"left":335,"width":40,"hidden":0},"alcadaVendaSupervisor_portador07":{"top":145,"left":465,"width":232,"hidden":0},"alcadaVendaSupervisor_valorInicial08":{"top":168,"left":17,"width":72,"hidden":0},"alcadaVendaSupervisor_valorFinal08":{"top":168,"left":144,"width":72,"hidden":0},"alcadaVendaSupervisor_qtdeParcelas08":{"top":168,"left":240,"width":24,"hidden":0},"alcadaVendaSupervisor_prazoMedio08":{"top":168,"left":335,"width":40,"hidden":0},"alcadaVendaSupervisor_portador08":{"top":168,"left":465,"width":232,"hidden":0},"alcadaVendaSupervisor_valorInicial09":{"top":191,"left":17,"width":72,"hidden":0},"alcadaVendaSupervisor_valorFinal09":{"top":191,"left":144,"width":72,"hidden":0},"alcadaVendaSupervisor_qtdeParcelas09":{"top":191,"left":240,"width":24,"hidden":0},"alcadaVendaSupervisor_prazoMedio09":{"top":191,"left":335,"width":40,"hidden":0},"alcadaVendaSupervisor_portador09":{"top":191,"left":465,"width":232,"hidden":0},"alcadaVendaSupervisor_valorInicial10":{"top":214,"left":17,"width":72,"hidden":0},"alcadaVendaSupervisor_valorFinal10":{"top":214,"left":144,"width":72,"hidden":0},"alcadaVendaSupervisor_qtdeParcelas10":{"top":214,"left":240,"width":24,"hidden":0},"alcadaVendaSupervisor_prazoMedio10":{"top":214,"left":335,"width":40,"hidden":0},"alcadaVendaSupervisor_portador10":{"top":214,"left":465,"width":232,"hidden":0},"alcadaVendaSupervisor_descontoMinimoSupervisor":{"top":237,"left":19,"width":56,"hidden":0},"alcadaVendaSupervisor_descontoMaximoSupervisor":{"top":260,"left":13,"width":56,"hidden":0}}',
			'alcadaVendaDesconto' => '{"alcadaVendaSupervisor_descontoMinimoSupervisor":{"top":7,"left":35,"width":120,"hidden":0},"alcadaVendaSupervisor_descontoMaximoSupervisor":{"top":30,"left":29,"width":120,"hidden":0},"alcadaVendaSupervisor_descontoMinimoSupervisorAVista":{"top":7,"left":384,"width":120,"hidden":0},"alcadaVendaSupervisor_descontoMaximoSupervisorAVista":{"top":30,"left":378,"width":120,"hidden":0}}',
			'devolucao' => '{"inserirNumeroNFeAoGerarNFeDevolucao":{"top":30,"left":57.078125,"width":55,"hidden":0},"atualizarAliquotasDevolucao":{"top":7,"left":21.453125,"width":55,"hidden":0},"habilitaRNC":{"top":53,"left":41.640625,"width":55,"hidden":1}}'
		);

		//Script de geração dos campos da alçada de venda
		$camposAlcadaVenda = array();
		for($i = 1;$i <= 10;$i++){
			$i = str_pad($i, 2, '0', STR_PAD_LEFT);
			$camposAlcadaVenda['alcadaVenda_valorInicial'.$i] = array('name' => 'alcadaVenda_valorInicial'.$i, 'label' => 'Valor', 'type' => 'moeda', 'casasDecimais' => '2');
			$camposAlcadaVenda['alcadaVenda_valorFinal'.$i] = array('name' => 'alcadaVenda_valorFinal'.$i, 'label' => '', 'type' => 'moeda', 'casasDecimais' => '2');
			$camposAlcadaVenda['alcadaVenda_qtdeParcelas'.$i] = array('name' => 'alcadaVenda_qtdeParcelas'.$i, 'label' => 'Parcelas', 'type' => 'text');
			$camposAlcadaVenda['alcadaVenda_prazoMedio'.$i] = array('name' => 'alcadaVenda_prazoMedio'.$i, 'label' => 'Prazo Médio', 'type' => 'text');
			$camposAlcadaVenda['alcadaVenda_portador'.$i] = array('name' => 'alcadaVenda_portador'.$i, 'label' => 'Portador', 'type' => 'text');
		}

		$camposAlcadaVendaSupervisor = array();
		for($i = 1;$i <= 10;$i++){
			$i = str_pad($i, 2, '0', STR_PAD_LEFT);
			$camposAlcadaVendaSupervisor['alcadaVendaSupervisor_valorInicial'.$i] = array('name' => 'alcadaVendaSupervisor_valorInicial'.$i, 'label' => 'Valor', 'type' => 'moeda', 'casasDecimais' => '2');
			$camposAlcadaVendaSupervisor['alcadaVendaSupervisor_valorFinal'.$i] = array('name' => 'alcadaVendaSupervisor_valorFinal'.$i, 'label' => '', 'type' => 'moeda', 'casasDecimais' => '2');
			$camposAlcadaVendaSupervisor['alcadaVendaSupervisor_qtdeParcelas'.$i] = array('name' => 'alcadaVendaSupervisor_qtdeParcelas'.$i, 'label' => 'Parcelas', 'type' => 'text');
			$camposAlcadaVendaSupervisor['alcadaVendaSupervisor_prazoMedio'.$i] = array('name' => 'alcadaVendaSupervisor_prazoMedio'.$i, 'label' => 'Prazo Médio', 'type' => 'text');
			$camposAlcadaVendaSupervisor['alcadaVendaSupervisor_portador'.$i] = array('name' => 'alcadaVendaSupervisor_portador'.$i, 'label' => 'Portador', 'type' => 'text');
		}
		$camposAlcadaVendaDesconto['alcadaVendaSupervisor_descontoMinimoSupervisor'] = array('name' => 'alcadaVendaSupervisor_descontoMinimoSupervisor', 'label' => 'Desconto Máximo Vendedor', 'type' => 'text');
		$camposAlcadaVendaDesconto['alcadaVendaSupervisor_descontoMaximoSupervisor'] = array('name' => 'alcadaVendaSupervisor_descontoMaximoSupervisor', 'label' => 'Desconto Máximo Supervisor', 'type' => 'text');
		$camposAlcadaVendaDesconto['alcadaVendaSupervisor_descontoMinimoSupervisorAVista'] = array('name' => 'alcadaVendaSupervisor_descontoMinimoSupervisorAVista', 'label' => 'Desconto Máximo Vendedor à vista', 'type' => 'text');
		$camposAlcadaVendaDesconto['alcadaVendaSupervisor_descontoMaximoSupervisorAVista'] = array('name' => 'alcadaVendaSupervisor_descontoMaximoSupervisorAVista', 'label' => 'Desconto Máximo Supervisor à vista', 'type' => 'text');

		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('cad058FormConfVendas');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'exibirLimitesOrcamentoPedido' => array('name' => 'exibirLimitesOrcamentoPedido', 'label' => 'Exibir limites do cliente', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'CRMNomeTab' => array('name' => 'CRMNomeTab', 'label' => 'Título das abas CRM', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Nome Fantasia', 'value' => 'D024_Nome_Fantasia'),
							array('title' => 'Razão Social', 'value' => 'D024_Nome_Empresa')
						)
					),
					'precopadraoVendaProduto' => array('name' => 'precopadraoVendaProduto', 'label' => 'Preço de venda do produto', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Preço Líquido', 'value' => 'liquido'),
							array('title' => 'Preço Bruto', 'value' => 'bruto')
						)
					),
					'botaoAplicarDesconto' => array('name' => 'botaoAplicarDesconto', 'label' => 'Botão Aplicar Desconto', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'percentualPrecoMinimoVenda' => array('name' => 'percentualPrecoMinimoVenda', 'label' => 'Percentual preço mínimo', 'type' => 'moeda', 'casasDecimais' => '2'),
					'carteiraClienteDiasMaxOrcamento' => array('name' => 'carteiraClienteDiasMaxOrcamento', 'label' => 'Dias sem orcamento para inativar cliente', 'type' => 'text'),
					'carteiraClienteDiasMaxPedido' => array('name' => 'carteiraClienteDiasMaxPedido', 'label' => 'Dias sem pedido para inativar cliente', 'type' => 'text'),
					'carteiraClienteDiasMaxNotaFiscal' => array('name' => 'carteiraClienteDiasMaxNotaFiscal', 'label' => 'Dias sem nota fiscal para inativar cliente', 'type' => 'text'),
					'carteiraClienteDiasMaxContato' => array('name' => 'carteiraClienteDiasMaxContato', 'label' => 'Dias sem contato para inativar cliente', 'type' => 'text'),
					'exibirMarcaImpressaoVenda' => array('name' => 'exibirMarcaImpressaoVenda', 'label' => 'Exibir marca na impressão', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'exibirFotoImpressaoVenda' => array('name' => 'exibirFotoImpressaoVenda', 'label' => 'Exibir foto na impressão', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'alterarVendedor' => array('name' => 'alterarVendedor', 'label' => 'Permite alterar vendedor sempre', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'alterarVendedorAusente' => array('name' => 'alterarVendedorAusente', 'label' => 'Permite alterar vendedor ausente', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'editarDescricaoProdutoVenda' => array('name' => 'editarDescricaoProdutoVenda', 'label' => 'Editar descrição produto', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'editarUnidadeProdutoVenda' => array('name' => 'editarUnidadeProdutoVenda', 'label' => 'Editar unidade produto', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'utilizarDescProdutoUltimaVenda' => array('name' => 'utilizarDescProdutoUltimaVenda', 'label' => 'Utilizar descrição da ultima venda', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'totalizarPesoVenda' => array('name' => 'totalizarPesoVenda', 'label' => 'Totalizar peso na venda', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'somarICMSPrecoVenda' => array('name' => 'somarICMSPrecoVenda', 'label' => 'Somar ICMS no preço de venda', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'somarPISPrecoVenda' => array('name' => 'somarPISPrecoVenda', 'label' => 'Somar PIS no preço de venda', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'somarCOFINSPrecoVenda' => array('name' => 'somarCOFINSPrecoVenda', 'label' => 'Somar COFINS no preço de venda', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'somarIRPJPrecoVenda' => array('name' => 'somarIRPJPrecoVenda', 'label' => 'Somar IRPJ no preço de venda', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'somarCSLLPrecoVenda' => array('name' => 'somarCSLLPrecoVenda', 'label' => 'Somar CSLL no preço de venda', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'permitirProdutosDuplicadosVenda' => array('name' => 'permitirProdutosDuplicadosVenda', 'label' => 'Permitir produtos duplicados', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'informarEstoqueInsuficiente' => array('name' => 'informarEstoqueInsuficiente', 'label' => 'Informar estoque insuficiente', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'venderEstoqueMatriz' => array('name' => 'venderEstoqueMatriz', 'label' => 'Vender estoque da matriz', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'C004PrecoTransferenciaGridProdutos' => array('name' => 'C004PrecoTransferenciaGridProdutos', 'label' => 'Empresa padrão preço transferência', 'type' => 'select', 
						'select' => gGeraSelect('C004', 'C004_Id', 'C004_Nome_Abreviado, C004_Nome_Empresa', 'where C004_Flag_Ativo="S"', true)
					),
					'habilitarDescontoPercentual' => array('name' => 'habilitarDescontoPercentual', 'label' => 'Habilitar desconto em percentual', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'habilitarDescontoReais' => array('name' => 'habilitarDescontoReais', 'label' => 'Habilitar desconto em reais', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'habilitarDefinirValorTotal' => array('name' => 'habilitarDefinirValorTotal', 'label' => 'Habilitar desconto pela definição do valor total', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'habilitarDescontoValorTotal' => array('name' => 'habilitarDescontoValorTotal', 'label' => 'Habilitar desconto no valor total', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'habilitarPercentualDescontoValorTotal' => array('name' => 'habilitarPercentualDescontoValorTotal', 'label' => 'Habilitar percentual desconto no valor total', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'habilitarInclusaoSubItensMontagem' => array('name' => 'habilitarInclusaoSubItensMontagem', 'label' => 'Habilitar inclusao de sub-itens para montagens', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'utilizarPrecoTabelaCliente' => array('name' => 'utilizarPrecoTabelaCliente', 'label' => 'Utilizar tabela de preços do cliente', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'ratearFreteVendaNoCusto' => array('name' => 'ratearFreteVendaNoCusto', 'label' => 'Ratear frete no custo quando por conta do emitente', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'habilitarInclusaoProdutosPesquisaGrid' => array('name' => 'habilitarInclusaoProdutosPesquisaGrid', 'label' => 'Inclusão de produtos em lote', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'definirDestinoVendaTipoCliente' => array('name' => 'definirDestinoVendaTipoCliente', 'label' => 'Destino venda de acordo com o tipo do cliente', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'atualizarAliquotaUnidadeProduto' => array('name' => 'atualizarAliquotaUnidadeProduto', 'label' => 'Atualizar unidade na atualização aliquotas',  'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'exibirDescontoImpressaoOrcPed' => array('name' => 'exibirDescontoImpressaoOrcPed', 'label' => 'Exibir desconto na impressão', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'mostrarDescontoProdutosAcrescimo' => array('name' => 'mostrarDescontoProdutosAcrescimo', 'label' => 'Exibir desconto produto negativo no totalizador orç/pedido/nota', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'padraoFaixaCalculoIcmsCredito' => array('name' => 'padraoFaixaCalculoIcmsCredito','label'=>'Faixa para cálculo do Crédito<br /> de ICMS Simples Nacional','uppercase'=>true,'type' => 'select', 'select' =>
						gGeraSelect('T134', 'T134_Id','CONCAT(T134_Tipo[[virgula]]": DE "[[virgula]]convert(format(round(T134_Faixa_Inicial),2,"de_DE")using utf8)[[virgula]]" A "[[virgula]]convert(format(round(T134_Faixa_Final),2,"de_DE")using utf8)[[virgula]]" = ALIQ: "[[virgula]]T134_ICMS[[virgula]]"% REDUÇÃO: "[[virgula]]if(T134_Reducao_Base_ICMS,T134_Reducao_Base_ICMS,0)[[virgula]]"%") as faixaCalculo','',true)
					),
					'obrigatorioNumOCItemOC' => array('name' => 'obrigatorioNumOCItemOC', 'label' => 'Obrigatório Num OC e Item OC ao faturar pedido e gerar NFe', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'exibirHistoricoFormVendaUltimoOrcamento' => array('name' => 'exibirHistoricoFormVendaUltimoOrcamento', 'label' => 'Exibir histórico último preço orçamento cliente form venda', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'exibirHistoricoFormVendaUltimoPedido' => array('name' => 'exibirHistoricoFormVendaUltimoPedido', 'label' => 'Exibir histórico último preço pedido cliente form venda', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'exibirHistoricoFormVendaUltimaNotaFiscal' => array('name' => 'exibirHistoricoFormVendaUltimaNotaFiscal', 'label' => 'Exibir histórico último preço NF cliente form venda', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'gerarAtividadesManipulacaoItens' => array('name' => 'gerarAtividadesManipulacaoItens', 'label' => 'Gera um registro de atividades no orçamento/pedido ao manipular os itens.', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'vendasZoomFonteImprimirPDF' => array('name' => 'vendasZoomFonteImprimirPDF', 'label' => 'Zoom fonte impressao PDF (1,2,3,4,5px)', 'type' => 'text', 'uppercase' => false),			
					'margemImprimirPDF' => array('name' => 'margemImprimirPDF', 'label' => 'Margens impressao PDF', 'type' => 'text'),					
					'desabilitarEdicaoPortador' => array('name' => 'desabilitarEdicaoPortador', 'label' => 'Desabilita a edição do portador no processo de venda.', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'utilizaUnidadeConversaoVenda' => array('name' => 'utilizaUnidadeConversaoVenda', 'label' => 'Utiliza Unidade Conversão', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'habilitaNotificacoesGenerico' => array('name' => 'habilitaNotificacoesGenerico', 'label' => 'Habilita notificações para alteração de produtos genéricos.', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Não', 'value' => 'N'),
							array('title' => 'Sim', 'value' => 'S')

						)
					),
					'atualizaCFOPAliquotasPrecoAoEntrarVendaPendente' => array('name' => 'atualizaCFOPAliquotasPrecoAoEntrarVendaPendente', 'label' => 'Atualiza CFOP,Alíquotas e Preço Tabela ao entrar em orç/ped pendente.', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Não', 'value' => 'N'),
							array('title' => 'Sim', 'value' => 'S')

						)
					),
					'custoUtilizadoNaVenda' => array('name' => 'custoUtilizadoNaVenda', 'label' => 'Custo utilizado na venda.', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Padrão', 'value' => 'P'),
							array('title' => 'Fixo', 'value' => 'F')

						)
					),
					'duplicarPedidoOrcamentoAtualizaPrecoTabela' => array('name' => 'duplicarPedidoOrcamentoAtualizaPrecoTabela', 'label' => 'Duplicar pedido/orçamento atualiza o preço com os dados do atuais do cadastro', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')

						)
					),
				),
				'orcamento' => array(
					'diasMaxOrcamentoPendente' => array('name' => 'diasMaxOrcamentoPendente', 'label' => 'Prazo orçamento pendente', 'type' => 'text'),
					'usarAlcada' => array('name' => 'usarAlcada', 'label' => 'Controle de alçada', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'formatoOrcamentoEmail' => array('name' => 'formatoOrcamentoEmail', 'label' => 'Formato email orçamento', 'type' => 'select', 'select' =>
						array(
							array('title' => 'PDF', 'value' => 'pdf'),
							array('title' => 'HTML', 'value' => 'html'),
							array('title' => 'Ambos', 'value' => 'ambos'),
						)
					),
					'orcamentoCarregarPrimeiroContato' => array('name' => 'orcamentoCarregarPrimeiroContato', 'label' => 'Trazer primeiro contato em uma nova venda', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'permitirVendaSemEstoque' => array('name' => 'permitirVendaSemEstoque', 'label' => 'Gerar pedido sem estoque', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'permitePedidoProdutoPreCadastro' => array('name' => 'permitePedidoProdutoPreCadastro', 'label' => 'Gerar pedido com itens em pré cadastro', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'verificaCreditoLiberadoGerarPedido' => array('name' => 'verificaCreditoLiberadoGerarPedido', 'label' => 'Requisitar análise financeira ao gerar pedido', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'habilitarAplicarPrecoKgOrcamento' => array('name' => 'habilitarAplicarPrecoKgOrcamento', 'label' => 'Habilitar botão "Aplica preço p/ Kg"', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'exibirMsgCotarOrcamento' => array('name' => 'exibirMsgCotarOrcamento', 'label' => 'Exibir "Cotar" no preço tabela', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'permitirPrecoZeradoOrcamento' => array('name' => 'permitirPrecoZeradoOrcamento', 'label' => 'Permitir inserir produtos sem preço', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'obrigatorioGerarParcelaOrcamento' => array('name' => 'obrigatorioGerarParcelaOrcamento', 'label' => 'Obrigatório forma de pagamento para gerar pedido', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'permitirOrcamentoEmailImpSemPagamento' => array('name' => 'permitirOrcamentoEmailImpSemPagamento', 'label' => 'Permitir imprimir/e-mail sem forma de pagamento', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'verificaAlcadaOrcamentoEmailImpressao' => array('name' => 'verificaAlcadaOrcamentoEmailImpressao', 'label' => 'Verifica alçada ao imprimir ou enviar por e-mail', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'orcamentoAtualizaTabelaPrecoCliente' => array('name' => 'orcamentoAtualizaTabelaPrecoCliente', 'label' => 'Orçamento atualiza a tabela de preços do cliente', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'atualizaPrecoTabelaOrcamento' => array('name' => 'atualizaPrecoTabelaOrcamento', 'label' => 'Atualizar preço tabela ao reprocessar histórico', 'type' => 'select', 'select' =>
						array(
							array('title' => 'SIM', 'value' => 'S'),
							array('title' => 'NÃO', 'value' => 'N')
						)
					),
					'habilitaEnsaioOrcamento' => array('name' => 'habilitaEnsaioOrcamento', 'label' => 'Adicionar ensaio no orçamento.', 'type' => 'select', 'select' =>
						array(
							array('title' => 'NÃO', 'value' => 'N'),
							array('title' => 'SIM', 'value' => 'S')
						)
					),
					'obrigatorioLocacaoItemComValidade' => array('name' => 'obrigatorioLocacaoItemComValidade', 'label' => 'Obrigatório selecionar locação item com validade', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'mantemPrecoItemComoTabela' => array('name' => 'mantemPrecoItemComoTabela', 'label' => 'Mantem o preço do item digitado como preço de tabela (Preço bruto)', 'type' => 'select', 'select' =>
						array(
							array('title' => 'SIM', 'value' => 'S'),
							array('title' => 'NÃO', 'value' => 'N')
						)
					),

					'mantemMesmasParcelasOrcamentoAoGerarPedido' => array('name' => 'mantemMesmasParcelasOrcamentoAoGerarPedido', 'label' => 'Ao gerar o pedido mantem os dados das parcelas do orçamento', 'type' => 'select', 'select' =>
						array(
							array('title' => 'SIM', 'value' => 'S'),
							array('title' => 'NÃO', 'value' => 'N')
						)
					),
					'dataVencimentoParcelasAoGerarPedido' => array('name' => 'dataVencimentoParcelasAoGerarPedido', 'label' => 'Mantem vencimento das parcelas igual do orçamento <br>ou atualiza com a data atual ao gerar pedido', 'type' => 'select', 'select' =>
						array(
							array('title' => 'ORÇAMENTO', 'value' => '1'),
							array('title' => 'DATA ATUAL', 'value' => '2')
						)
					),
					'permitePedidoClientePreCadastro' => array('name' => 'permitePedidoClientePreCadastro', 'label' => 'Gerar pedido com clientes em pré cadastro', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),		
					'permiteBuscarCadastroItem' => array('name' => 'permiteBuscarCadastroItem', 'label' => 'Habilitar busca de produto no grid ao importar CSV', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),				
					'habilitaRegistrodeAcesso' => array('name' => 'habilitaRegistrodeAcesso', 'label' => 'Habilitar registro de acesso', 'type' => 'select', 'select' =>
						array(
							array('title' => 'SIM', 'value' => 'S'),
							array('title' => 'NÃO', 'value' => 'N')
						)
					),
					'bloqueioOrcamentoConfirmado' => array('name' => 'bloqueioOrcamentoConfirmado', 'label' => 'Bloqueia orçamento confirmado e com pedido', 'type' => 'select', 'select' =>
						array(
							array('title' => 'SIM', 'value' => 'S'),
							array('title' => 'NÃO', 'value' => 'N')
						)
					),					
					'prazoPrecoCotacaoNoOrcamento' => array('name' => 'prazoPrecoCotacaoNoOrcamento', 'label' => 'Prazo Preço Cotação', 'type' => 'text'),					
				),
				'pedido' => array(
					'usarOrdemCompra' => array('name' => 'usarOrdemCompra', 'label' => 'Obrigatório ordem de compra no pedido', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'statusInicialPedido' => array('name' => 'statusInicialPedido', 'label' => 'Status inicial pedido', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Impressão', 'value' => '1'),
							array('title' => 'Separação', 'value' => '2'),
							array('title' => 'Conferência', 'value' => '10'),
							array('title' => 'Aguardando Peças', 'value' => '21'),
							array('title' => 'Emitir NFe', 'value' => '3'),
						)
					),
					'pedidoGerarNFEmQualquerStatus' => array('name' => 'pedidoGerarNFEmQualquerStatus', 'label' => 'Faturar em qualquer status', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'permissaoFaturarEmergencia' => array('name' => 'permissaoFaturarEmergencia', 'label' => 'Faturar emergência', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'diasVencPedido' => array('name' => 'diasVencPedido', 'label' => 'Prazo pedido pendente', 'type' => 'text'),
					'usarBotaoIncluirPedido' => array('name' => 'usarBotaoIncluirPedido', 'label' => 'Habilitar inclusão de pedido', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'permissaoFaturarSemPreco' => array('name' => 'permissaoFaturarSemPreco', 'label' => 'Permitir faturar sem forma de pagamento', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'usarTransportadoraPedido' => array('name' => 'usarTransportadoraPedido', 'label' => 'Obrigatório transportadora no pedido', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'FaturarPedidoAposAnalisePedido' => array('name' => 'FaturarPedidoAposAnalisePedido', 'label' => 'Faturar após análise do pedido', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'formatoPedidoEmail' => array('name' => 'formatoPedidoEmail', 'label' => 'Formato email pedido', 'type' => 'select', 'select' =>
						array(
							array('title' => 'PDF', 'value' => 'pdf'),
							array('title' => 'HTML', 'value' => 'html'),
							array('title' => 'Ambos', 'value' => 'ambos'),
						)
					),
					'permitirFaturarSemEstoque' => array('name' => 'permitirFaturarSemEstoque', 'label' => 'Faturar sem estoque', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'obrigatorioDataEntregaPedido' => array('name' => 'obrigatorioDataEntregaPedido', 'label' => 'Obrigatório data entrega pedido', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'calcularVolumePedido' => array('name' => 'calcularVolumePedido', 'label' => 'Calcular volume no pedido', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'usarControleCredito' => array('name' => 'usarControleCredito', 'label' => 'Habilitar análise de crédito', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'pedidoAtualizaTabelaPrecoCliente' => array('name' => 'pedidoAtualizaTabelaPrecoCliente', 'label' => 'Pedido atualiza a tabela de preços do cliente', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'prazoInicialPedidoPeriodico' => array('name' => 'prazoInicialPedidoPeriodico', 'label' => 'Prazo inicial pedidos periódicos', 'type' => 'moeda', 'casasDecimais' => '0'),
					'prazoFinalPedidoPeriodico' => array('name' => 'prazoFinalPedidoPeriodico', 'label' => 'Prazo final pedidos periódicos', 'type' => 'moeda', 'casasDecimais' => '0'),
					'permiteRecuperarPedidoCancelado' => array('name' => 'permiteRecuperarPedidoCancelado', 'label' => 'Permite recuperar pedido cancelado?', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'horarioLimiteGeracaoPedido' => array('name' => 'horarioLimiteGeracaoPedido', 'label' => 'Horário limite para geração de pedidos', 'type' => 'tempo'),
					'validarNFEFaturarPedido' => array('name' => 'validarNFEFaturarPedido', 'label' => 'Validar NF-e ao faturar pedido?', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'habilitaLiberacaoVendas' => array('name' => 'habilitaLiberacaoVendas', 'label' => 'Habilitar liberação de vendas no pedido', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Não', 'value' => 'N'),
							array('title' => 'Sim', 'value' => 'S')
						)
					),
					'quantidadePadraoVolumePedido' => array('name' => 'quantidadePadraoVolumePedido', 'label' => 'Quantidade padrão volumes', 'type' => 'text'),
					'atualizaPrecoTabelaPedido' => array('name' => 'atualizaPrecoTabelaPedido', 'label' => 'Atualizar preço tabela ao reprocessar histórico', 'type' => 'select', 'select' =>
						array(
							array('title' => 'SIM', 'value' => 'S'),
							array('title' => 'NÃO', 'value' => 'N')
						)
					),
				),
				'geralNotaFiscal' => array(
					'dataSaidaDefault' => array('name' => 'dataSaidaDefault', 'label' => 'Data de saida padrão', 'type' => 'text', 'uppercase' => false),
					'exibirValorNFDANFE' => array('name' => 'exibirValorNFDANFE', 'label' => 'Exibir valor NFe canhoto DANFE', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'obrigatorioEnvioDanfeViaEmail' => array('name' => 'obrigatorioEnvioDanfeViaEmail', 'label' => 'Obrigatório envio DANFE via email', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'exibirNoDanfeValorTribTransp' => array('name' => 'exibirNoDanfeValorTribTransp', 'label' => 'Exibir no danfe valor aprox. trib.', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'NFeEnviarEmailAutomatico' => array('name' => 'NFeEnviarEmailAutomatico', 'label' => 'Enviar danfe por email ao transmitir', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'EnviarDanfeBoletoMesmoEmail' => array('name' => 'EnviarDanfeBoletoMesmoEmail', 'label' => 'Envia Danfe e Boleto no mesmo email', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Não', 'value' => 'N'),
							array('title' => 'Sim', 'value' => 'S')
						)
					),
					'NFeEnviarEmailTransportadora' => array('name' => 'NFeEnviarEmailTransportadora', 'label' => 'Enviar email p/ transportadora ao transmitir', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Não', 'value' => 'N'),
							array('title' => 'Sim', 'value' => 'S')
						)
					),
					'NFeModificarTituloEmail' => array('name' => 'NFeModificarTituloEmail', 'label' => 'Modificar título do email', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Não', 'value' => 'N'),
							array('title' => 'Sim', 'value' => 'S')
						)
					),
					'NFeEnviarEmailVendedorInterno' => array('name' => 'NFeEnviarEmailVendedorInterno', 'label' => 'Enviar email p/ o vendedor ao transmitir', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Não', 'value' => 'N'),
							array('title' => 'Sim', 'value' => 'S')
						)
					),
					'NFeEnviarEmailPessoaProposta' => array('name' => 'NFeEnviarEmailPessoaProposta', 'label' => 'Enviar email p/ pessoa proposta ao transmitir', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Não', 'value' => 'N'),
							array('title' => 'Sim', 'value' => 'S')
						)
					),
					'anexoEmailCertificadoQualidade' => array('name' => 'anexoEmailCertificadoQualidade', 'label' => 'Enviar certificado de qualidade ao transmitir', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Não', 'value' => 'N'),
							array('title' => 'Sim', 'value' => 'S')
						)
					),
					'NFeEnviarEmailRepresentante' => array('name' => 'NFeEnviarEmailRepresentante', 'label' => 'Enviar email p/ representante ao transmitir', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Não', 'value' => 'N'),
							array('title' => 'Sim', 'value' => 'S')
						)
					),
					'BoletosEnviarEmailAutomatico' => array('name' => 'BoletosEnviarEmailAutomatico', 'label' => 'Enviar boletos por email ao transmitir', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'enviarBoletoProtegido' => array('name' => 'enviarBoletoProtegido', 'label' => 'Enviar boleto protegido', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Não', 'value' => 'N'),
							array('title' => 'Sim', 'value' => 'S')
						)
					),
					'NFeEnviarEmailIncluirContatosCC' => array('name' => 'NFeEnviarEmailIncluirContatosCC', 'label' => 'Adicionar contatos como CC ao enviar danfe', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'camposDescricaoProdutoNF' => array('name' => 'camposDescricaoProdutoNF', 'label' => 'Descrição produto XML nota fiscal', 'type' => 'text', 'uppercase' => false),
					'exibirObservacaoPedidonaNF' => array('name' => 'exibirObservacaoPedidonaNF', 'label' => 'Exibir observação do número de pedido na nota fiscal', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'exibirObservacaoVendedornaNF' => array('name' => 'exibirObservacaoVendedornaNF', 'label' => 'Exibir observação do vendedor na nota fiscal', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'exibirObservacaoOCnaNF' => array('name' => 'exibirObservacaoOCnaNF', 'label' => 'Exibir observação da ordem de compra na nota fiscal', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'exibirObservacaoOCPorItemnaNF' => array('name' => 'exibirObservacaoOCPorItemnaNF', 'label' => 'Exibir observação da ordem de compra por item na nota fiscal', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'exibirObservacaoAreaClientenaNF' => array('name' => 'exibirObservacaoAreaClientenaNF', 'label' => 'Exibir observação da área do cliente na nota fiscal', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'exibirObservacaoContatonaNF' => array('name' => 'exibirObservacaoContatonaNF', 'label' => 'Exibir observação do contato na nota fiscal', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'exibirTipoFreteNaNF' => array('name' => 'exibirTipoFreteNaNF', 'label' => 'Exibir tipo do frete na observação da nota fiscal', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'nomeClienteConsumidorFinal' => array('name' => 'nomeClienteConsumidorFinal', 'label' => 'Nome do cliente consumidor final', 'type' => 'text', 'uppercase' => false),
					'emitirNFCDataHoraAtual' => array('name' => 'emitirNFCDataHoraAtual', 'label' => 'Emitir nota fiscal de consumidor com data e hora atual', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'notaAtualizaTabelaPrecoCliente' => array('name' => 'notaAtualizaTabelaPrecoCliente', 'label' => 'Nota fiscal atualiza a tabela de preços do cliente', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'exibirBotaoTransmitirNota' => array('name' => 'exibirBotaoTransmitirNota', 'label' => 'Habilitar botão transmitir NF-e/NFC-e', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'adicionarNomeFantasiaClienteCanhotoDanfe' => array('name' => 'adicionarNomeFantasiaClienteCanhotoDanfe', 'label' => 'Exibir nome fantasia do cliente no canhoto do DANFE?', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'imprimirNoDanfeEanNoLugarDoCodigo' => array('name' => 'imprimirNoDanfeEanNoLugarDoCodigo', 'label' => 'Exibir EAN no lugar do código do produto no DANFE?', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Não', 'value' => 'N'),
							array('title' => 'Sim', 'value' => 'S')
						)
					),
					'validarNFEGerarNotaFiscal' => array('name' => 'validarNFEGerarNotaFiscal', 'label' => 'Validar NF-e ao gerar Nota Fiscal', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Não', 'value' => 'N'),
							array('title' => 'Sim', 'value' => 'S')
						)
					),
					'alertaVencimentoCertificadoDigital1' => array('name' => 'alertaVencimentoCertificadoDigital1', 'label' => 'Alerta Vencimento do Certificado Digital 1', 'type' => 'text', 'uppercase' => false),
					'alertaVencimentoCertificadoDigital2' => array('name' => 'alertaVencimentoCertificadoDigital2', 'label' => 'Alerta Vencimento do Certificado Digital 2 ', 'type' => 'text', 'uppercase' => false),
					'imprimirDuplicatasPortadorTipoECommerce' => array('name' => 'imprimirDuplicatasPortadorTipoECommerce', 'label' => 'Imprimir Duplicatas do Portador do Tipo E-commerce no DANFE?', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'inserirNumeroNFeIncluir' => array('name' => 'inserirNumeroNFeIncluir', 'label' => 'Inserir número NFe ao incluir Nota', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'totalizarPesoNFeDevolucao' => array('name' => 'totalizarPesoNFeDevolucao', 'label' => 'Totalizar peso ao emitir NF devolução', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),					
					'qtdeFaltaNFTravarCliente' => array('name' => 'qtdeFaltaNFTravarCliente', 'label' => 'Qtde numerações NF faltando travar tela "Enviar NFe para Sefaz"', 'type' => 'moeda', 'casasDecimais' => '0', 'uppercase' => false),
					'imprimirCanhotoDanfe' => array('name' => 'imprimirCanhotoDanfe', 'label' => 'Imprimir canhoto Danfe', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Cabeçalho', 'value' => '1'),
							array('title' => 'Rodapé', 'value' => '2')
						)
					),
					'exibirObservacaoClienteNF' => array('name' => 'exibirObservacaoClienteNF', 'label' => 'Exibir a observação do cliente (pedido) na nota fiscal', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'habilitarEnvioNFeLote' => array('name' => 'habilitarEnvioNFeLote', 'label' => 'Habilitar a transmissão em lote da nota', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'imprimeInformacoesAdicionaisNFCe' => array('name' => 'imprimeInformacoesAdicionaisNFCe', 'label' => 'Imprime informações adicionais na NFCe', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Não', 'value' => 'N'),
							array('title' => 'Sim', 'value' => 'S')
						)
					),	
					'imprimirValorIPInaNFCe' => array('name' => 'imprimirValorIPInaNFCe', 'label' => 'Imprime valor IPI na NFCe', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Não', 'value' => 'N'),
							array('title' => 'Sim', 'value' => 'S')
						)
					),	
					'adicionarPreenchimentoCPFRGCanhoto' => array('name' => 'adicionarPreenchimentoCPFRGCanhoto', 'label' => 'Exibir preenchimento CPF/RG do receber no canhoto', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),

					'permitirFinalizarNotaSemEstoque' => array('name' => 'permitirFinalizarNotaSemEstoque', 'label' => 'Permitir finalizar nota sem estoque', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
				),
				'gnreNotaFiscal' => array(
					'gerarStContasPagar' => array('name' => 'gerarStContasPagar', 'label' => 'Incluir ST em contas a pagar ao gerar a nota?', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'tituloGnreNotaFiscal' => array('name' => 'tituloGnreNotaFiscal', 'label'=>'Configurações para incluir ST em contas a pagar ao gerar a nota','type' => 'titulo', 'reject' => true),
					'grupoSTContasPagar' => array('name' => 'grupoSTContasPagar', 'label' => 'Grupo', 'type' => 'select','extra' => "onchange=\"alteraConta(this, '{$g['divId']}')\"", 'select' => 
						gGeraSelect('D032', 'D032_Id', 'D032_Descricao','WHERE D032_Flag_Ativo="S" ORDER BY D032_Descricao', true)
					),
					'contaSTContasPagar' => array('name' => 'contaSTContasPagar', 'label' => 'Conta', 'type' => 'select', 'align'=>'right', 'style'=>'float:right', 'select' => 
						gGeraSelect('D014', 'D014_Id', 'IF(D154_Id>0[[virgula]]CONCAT(D154_Descricao[[virgula]]" - "[[virgula]]D014_SubConta)[[virgula]]D014_SubConta) as D014_SubConta',"LEFT JOIN D154 ON D154_Id=D014_D154_Id WHERE D014_D032_Id = '{$g['C031']['grupoSTContasPagar']}' AND D014_Flag_Ativo='S' ORDER BY D014_SubConta", true)
					),
					'portadorSTContasPagar' => array('name' => 'portadorSTContasPagar', 'label' => 'Portador', 'type' => 'select', 'select' => 
						gGeraSelect('D031','D031_Id','D031_Portador', '', true),
					),
					'centroCustoSTContasPagar' => array('name' => 'centroCustoSTContasPagar', 'label' => 'Centro custo', 'type' => 'select','select' => 
						gGeraSelect('D073','D073_Id','D073_Nome', 'WHERE D073_Flag_Ativo = "S"', true),
					),
					'prazoDiasVencimentoGnreST' => array('name' => 'prazoDiasVencimentoGnreST', 'label' => 'Prazo a se acrescentar', 'type' => 'text'),
				),
				'emailNotaFiscal' => array(
					'tituloEmailNotaFiscal' => array('name' => 'tituloEmailNotaFiscal', 'label'=>'Email NFe - Ao preencher ele usa esta e-mail para enviar Notas','type' => 'titulo', 'reject' => true),
					'NFe_Email' => array('name' => 'NFe_Email','label'=>'E-mail', 'type' => 'text', 'length' => array(50,60), 'uppercase' => false, 'nivel' => 4),
					'NFe_Email_SMTP_Servidor' => array('name' => 'NFe_Email_SMTP_Servidor','label'=>'Servidor SMTP', 'type' => 'text', 'length' => array(50,60), 'uppercase' => false, 'nivel' => 4),
					'NFe_Email_SMTP_Porta' => array('name' => 'NFe_Email_SMTP_Porta','label'=>'Porta SMTP', 'type' => 'text', 'length' => array(50,60), 'nivel' => 4),
					'NFe_Email_SMTP_Autenticacao' => array('name' => 'NFe_Email_SMTP_Autenticacao','label'=>'Autenticação', 'type' => 'text', 'length' => array(50,60), 'uppercase' => false, 'nivel' => 4),
					'NFe_Email_SMTP_Usuario' => array('name' => 'NFe_Email_SMTP_Usuario','label'=>'Usuário', 'type' => 'text', 'length' => array(50,60), 'uppercase' => false, 'nivel' => 4),
					'NFe_Email_SMTP_Senha' => array('name' => 'NFe_Email_SMTP_Senha','label'=>'Senha', 'type' => 'password', 'length' => array(50,60), 'uppercase' => false, 'nivel' => 4),
					'NFe_Email_SMTP_Seguranca' => array('name' => 'NFe_Email_SMTP_Seguranca', 'label' => 'Segurança SMTP', 'type' => 'select',
						'select' => array(
							array('title' => 'Automático', 'value' => '0'),
							array('title' => 'SSL', 'value' => '1'),
							array('title' => 'TLS', 'value' => '2'),
						)
					),
					
				),
				'cupomFiscal' => array(
					'cfopCupomFiscalComST' => array('name' => 'cfopCupomFiscalComST', 'label' => 'CFOP com ST', 'type' => 'select', 
						'select' => gGeraSelect('D006', 'D006_Id', 'D006_Id,D006_Codigo_CFOP,D006_Descricao', "WHERE D006_Flag_Inativo != 'S' AND D006_Flag_Venda_Compra_Outros = 'V' ORDER BY D006_Codigo_CFOP", true)
					),
					'cfopCupomFiscalSemST' => array('name' => 'cfopCupomFiscalSemST', 'label' => 'CFOP sem ST', 'type' => 'select', 
						'select' => gGeraSelect('D006', 'D006_Id', 'D006_Id,D006_Codigo_CFOP,D006_Descricao', "WHERE D006_Flag_Inativo != 'S' AND D006_Flag_Venda_Compra_Outros = 'V' ORDER BY D006_Codigo_CFOP", true)
					),
					'codigoClienteCupomFiscal' => array('name' => 'codigoClienteCupomFiscal', 'label' => 'Cliente utilizado no cupom', 'type' => 'pesquisa',
						'pesquisa' => array('D024', 'D024_Id', 'D024_Id,D024_Nome_Empresa,D024_Cnpj')
					),
					'documentoPadraoPDV' => array('name' => 'documentoPadraoPDV', 'label' => 'Documento padrão PDV', 'type' => 'select', 'select' =>
						array(
							array('title' => 'NFC-e', 'value' => '0'),
							array('title' => 'Cupom Fiscal', 'value' => '1')
						)
					),
					'precoEtiquetaGondola' => array('name' => 'precoEtiquetaGondola', 'label' => 'Preço etiqueta gôndola', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Preço Loja', 'value' => '1'),
							array('title' => 'Preço 1', 'value' => '2')
						)
					),
					'gerarNumeroNFCeAutomaticoInserirPDV' => array('name' => 'gerarNumeroNFCeAutomaticoInserirPDV', 'label' => 'Gerar número NFC-e automático ao inserir nota PDV?', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'ativarPagamentosPDVNota' => array('name' => 'ativarPagamentosPDVNota', 'label' => 'Ativar pagamentos PDV na nota', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'removerNFCeDaFilaEnvioAposTransmitir' => array('name' => 'removerNFCeDaFilaEnvioAposTransmitir', 'label' => 'Remover NFC-e/PDV da fila de envio após transmitir', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'PDVTransmitirNFCeAutomaticamente' => array('name' => 'PDVTransmitirNFCeAutomaticamente', 'label' => 'Transmitir NFC-e automaticamente ao finalizar venda', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'alterarDocumentoParaNFEAoAbrirCompleta' => array('name' => 'alterarDocumentoParaNFEAoAbrirCompleta', 'label' => 'Altera documento para NF-e ao abrir nota completa', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'abrirGavetaManual' => array('name' => 'abrirGavetaManual', 'label' => 'Habilitar botão abrir gaveta no PDV', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'habitaOpcoesDiversasPDV' => array('name' => 'habitaOpcoesDiversasPDV', 'label' => 'Habilitar botão opções diversas', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'permiteImportarDavQualquerStatus' => array('name' => 'permiteImportarDavQualquerStatus', 'label' => 'Permite importar DAV em qualquer status', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'permiteAbrirNFCompletaComClientePadraoPDV' => array('name' => 'permiteAbrirNFCompletaComClientePadraoPDV', 'label' => 'Permite abrir NF completa com cliente padrão do PDV', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'permiteImportarVariasDavPdv' => array('name' => 'permiteImportarVariasDavPdv', 'label' => 'Permite Importar mais de uma DAV por NFCe', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'permiteGerarNFCPessoaJuridica' => array('name' => 'permiteGerarNFCPessoaJuridica', 'label' => 'Permite gerar NFCe para pessoa jurídica', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'habilitaUpdateVendedor' => array('name' => 'habilitaUpdateVendedor', 'label' => 'Manter o vendedor do pedido no PDV ao importar DAV', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Não', 'value' => 'N'),
							array('title' => 'Sim', 'value' => 'S')
						)
					),
					'desabilitarExpedicaoPDV' => array('name' => 'desabilitarExpedicaoPDV', 'label' => 'Desabilita processo expedição ao importar DAV', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Não', 'value' => 'N'),
							array('title' => 'Sim', 'value' => 'S')
						)
					),
					'atualizaPrecoLojaAutomatico' => array('name' => 'atualizaPrecoLojaAutomatico', 'label' => 'Atualiza preço loja ao recalcular preço loja real', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Não', 'value' => 'N'),
							array('title' => 'Sim', 'value' => 'S')
						)
					),
					'prazoVencimentoParcelaPdvPortador' => array('name' => 'prazoVencimentoParcelaPdvPortador', 'label' => 'Prazo parcelas de acordo com portador<br>pelo campo "Dias para baixa"', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Não', 'value' => 'N'),
							array('title' => 'Sim', 'value' => 'S')
						)
					),
					'alterarItemPdvVinculadoDav' => array('name' => 'alterarItemPdvVinculadoDav', 'label' => 'Permite alterar/excluir item vinculado a DAV', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Não', 'value' => 'N'),
							array('title' => 'Sim', 'value' => 'S')
						)
					),
				),
				'alcadaVenda' => $camposAlcadaVenda,
				'alcadaVendaSupervisor' => $camposAlcadaVendaSupervisor,
				'alcadaVendaDesconto' => $camposAlcadaVendaDesconto,
				'devolucao' => array(
					'atualizarAliquotasDevolucao' => array('name' => 'atualizarAliquotasDevolucao', 'label' => 'Atualizar alíquotas ao gerar nota de devolução&nbsp', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'habilitaRNC' => array('name' => 'habilitaRNC', 'label' => 'Habilitar o Registro de Não Conformidade?', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'inserirNumeroNFeAoGerarNFeDevolucao' => array('name' => 'inserirNumeroNFeAoGerarNFeDevolucao', 'label' => 'Inserir número NFe ao incluir devolucao', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
				),
			)
		);		
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#grupoSTContasPagar').bind('click', function(){
					alteraConta($('#grupoSTContasPagar'), '{$g['divId']}');
				});
			} </script></div>
EOT;

		if($g['usuarioAtual'] == '63'){
			$formArray['campos']['geral']['zerarIPIProdutoNaoProduzido'] = array('name' => 'zerarIPIProdutoNaoProduzido', 'label' => 'Utilizar campo Tipo (IPI)', 'type' => 'select', 'select' =>
				array(
					array('title' => 'Sim', 'value' => 'S'),
					array('title' => 'Não', 'value' => 'N')
				)
			);
		}

		$arrayObservacoes = array();
		$arrayDiff = array();
		$sqlC031 = mysql_query("SELECT C031_Campo, C031_Observacao, C031_Valor, C031_Valor_Padrao FROM C031 GROUP BY C031_Campo");
		while($rowC031 = mysql_fetch_array($sqlC031)){
			$arrayObservacoes[$rowC031['C031_Campo']] = $rowC031['C031_Observacao'];
			if($rowC031['C031_Valor'] != $rowC031['C031_Valor_Padrao']){
				$arrayDiff[] = $rowC031['C031_Campo'];
			}
		}
		foreach ($formArray['campos'] as $keyTab => &$camposTab) {
			foreach ($camposTab as $keyCampo => &$campoFormulario) {
				$campoFormulario['extra'] = 'title="'.$arrayObservacoes[$keyCampo].'"';
				if(in_array($keyCampo, $arrayDiff)){
					if(empty($campoFormulario['label'])) { continue; }
					$campoFormulario['label'] = "<font color=\"blue\">".$campoFormulario['label']."</font>";
				}
			}
		}
		unset($camposTab);
		unset($campoFormulario);
		$form->definirFormularioArray($formArray);

		/**
		* PARTE 3: Processamento do formulário (se foi enviado)
		* 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		*/
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
			// Form sem erros
			} else if ($formCodigo == 2) {
				// Update
				require_once('bibliotecas/classes/CAD058.php');
                $CAD058 = new CAD058();
                $retorno = $CAD058->atualizarConfGlobal($form, $g['empresaAtual']);
				if ($retorno == true) {
					echo gGeraAlertMsg('Operação OK', 'Configurações salvas com sucesso!');
				}
			}
		} else {
			// Carrega os valores caso seja um update
			$sql = mysql_query("SELECT * FROM C031 WHERE C031_C004_Id = '{$g['empresaAtual']}'");
			while($row = mysql_fetch_array($sql)){
				if(strpos($row['C031_Campo'], "alcadaVenda_valor") !== false || strpos($row['C031_Campo'], "alcadaVenda_desconto") !== false || strpos($row['C031_Campo'], "alcadaVendaSupervisor_valor") !== false || strpos($row['C031_Campo'], "alcadaVendaSupervisor_desconto") !== false){
					$row['C031_Valor'] = gCorrigeNumero($row['C031_Valor']);	
				}

				if(strpos($row['C031_Campo'], "codigoClienteCupomFiscal") !== false && !empty($row['C031_Valor'])){
					$sqlD024 = mysql_query("SELECT D024_Id, D024_Nome_Empresa, D024_Cnpj FROM D024 WHERE D024_Id = '{$row['C031_Valor']}'");
					$rowD024 = mysql_fetch_array($sqlD024);
					$conteudoD024 = $rowD024['D024_Id'].' - '.$rowD024['D024_Nome_Empresa'].' - '.$rowD024['D024_Cnpj'];
					$form->campoSetarValorPesquisa($row['C031_Campo'], $conteudoD024);
				}

				$form->campoSetarValor($row['C031_Campo'], "{$row['C031_Valor']}");
			}
		}
		
		/**
		* PARTE 4: Gerar o formulário
		*/
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);
		
		/**
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo  $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		echo $g['smarty']->fetch('formEditor.tpl');
	break;

	case 'cad058FormConfEstoque':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		
		/**
		* PARTE 2: Definição do formulário
		*/
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = true;
		if ($formMultiTab) {
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}
		
		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = '0000';
		$form_campo_pk = '0000_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad058FormConfEstoque_Btn';
		// Form que será executado ao clicar no 
		$enviar_acaoHref = '/cad/cad058/form/cad058FormConfEstoque/';
		// formEditor
		// Form Editor
		$form_editor = array(
			'geral' => '{"SRMNomeTab":{"top":7,"left":70.90625,"width":144,"hidden":0},"atualizarCustoVendaComCustoMedio":{"top":7,"left":461.828125,"width":64,"hidden":0},"usarDescontoProdutosCompras":{"top":30,"left":80.328125,"width":64,"hidden":0},"exibirMarcaImpressaoCompra":{"top":30,"left":600.921875,"width":64,"hidden":0},"comprasCalcularST":{"top":53,"left":130.78125,"width":64,"hidden":0},"desabilitarCotacaoEmergencia":{"top":53,"left":561.203125,"width":64,"hidden":0},"comprasCalcularIPI":{"top":76,"left":130.953125,"width":64,"hidden":0},"desabilitarCompraEmergencia":{"top":76,"left":563.5,"width":64,"hidden":0},"comprasCalcularICMS":{"top":99,"left":117.59375,"width":64,"hidden":0},"desabilitarMontagemEmergencia":{"top":99,"left":547.625,"width":64,"hidden":0},"estoqueSugestaoCompra":{"top":122,"left":107.5,"width":80,"hidden":0},"desabilitarServicoEmergencia":{"top":122,"left":565.484375,"width":64,"hidden":0},"exibirFotoImpressaoCompra":{"top":145,"left":68.0625,"width":64,"hidden":0},"desabilitarProducaoEmergencia":{"top":145,"left":569.25,"width":64,"hidden":0},"impressaoEtiquetasPadraoJava":{"top":168,"left":531.671875,"width":64,"hidden":0},"avisarQtaCompradaMaiorNecessario":{"top":191,"left":462.203125,"width":63,"hidden":0},"diasGerarDataVencimento":{"top":237,"left":498.34375,"width":25,"hidden":0},"estatisticaDeCompraMesesComMovimento":{"top":283,"left":387.046875,"width":63,"hidden":1}}',
			'estoque' => '{"retiradaInternaUtilizarLeitorBiometrico":{"top":7,"left":48,"width":64,"hidden":0},"controleProdutoPedido":{"top":30,"left":9,"width":64,"hidden":0}}',
			'expedicao' => '{"formatoEtiquetaCorreio":{"top":122,"left":10.609375,"width":184,"hidden":0},"expedicaoDeMercadoria":{"top":7,"left":87.640625,"width":96,"hidden":0},"desabilitarConferencia":{"top":7,"left":547.875,"width":56,"hidden":0},"modeloColetor":{"top":30,"left":92.53125,"width":96,"hidden":0},"desabilitarSeparacao":{"top":30,"left":553.984375,"width":56,"hidden":0},"conferenciaCegaVendas":{"top":53,"left":97.421875,"width":96,"hidden":0},"modeloExpedicao":{"top":76,"left":113.3125,"width":96,"hidden":0},"ValorDeclaradoCorreios":{"top":99,"left":141.640625,"width":88,"hidden":0},"conferenciaVolumes":{"top":53,"left":528.328125,"width":56,"hidden":0},"embalagemPadraoVolume":{"top":76,"left":480.671875,"width":271,"hidden":0}}',
			'cotacao' => '{"nivelAlcadaAtualizarCustoVenda":{"top":7,"left":5,"width":25,"hidden":0},"nivelAlcadaAtualizarCustoProduto":{"top":30,"left":54,"width":24,"hidden":0},"nivelAlcadaAtualizarCustoFornecedor":{"top":7,"left":557,"width":24,"hidden":0}, "compradorPadraoCotacao": {"top":122,"left":14.1875,"width":210,"hidden":0}, "fornecedorPadraoCotacao": {"top":99,"left":14.484375,"width":185,"hidden":0}}',
			'ordemCompra' => '{"habilitaEmail":{"top":0,"left":0,"width":78.921584,"hidden":1},"habilitaComprasImportacao":{"top":0,"left":0,"width":78.921584,"hidden":1},"tituloEmailOC":{"top":3.9675140380859375,"left":463.9552574157715,"width":null,"hidden":0},"permiteOrdemCompraProdutoPreCadastro":{"top":6.969970703125,"left":17.723651885986328,"width":64,"hidden":0},"obrigatorioOCValorFretePreenchido":{"top":29.978546142578125,"left":77.61948776245117,"width":64,"hidden":0},"OC_Email":{"top":29.978546142578125,"left":492.34069442749023,"width":313.039216,"hidden":0},"obrigatorioOCTransportadoraPreenchido":{"top":52.97181701660156,"left":54.13602828979492,"width":64,"hidden":0},"OC_Email_SMTP_Usuario":{"top":52.97181701660156,"left":485.09496688842773,"width":313.039216,"hidden":0},"habilitarbotaoIncluirOC":{"top":75.99571228027344,"left":139.30759811401367,"width":64,"hidden":0},"OC_Email_SMTP_Senha":{"top":75.99571228027344,"left":492.12621688842773,"width":311.039216,"hidden":0},"toleranciaDiferencaOCNF":{"top":98.95832824707031,"left":79.82536697387695,"width":55.039215999999996,"hidden":0},"PrazoVencerOrdemCompra":{"top":121.98223876953125,"left":161.55024337768555,"width":55.039215999999996,"hidden":0},"OC_Email_SMTP_Servidor":{"top":121.98223876953125,"left":450.045955657959,"width":313.039216,"hidden":0},"controleAlcadasOrdemCompra":{"top":144.99081420898438,"left":169.42402267456055,"width":63,"hidden":0},"OC_Email_SMTP_Porta":{"top":144.99081420898438,"left":463.7101707458496,"width":55.039215999999996,"hidden":0},"OC_Email_SMTP_Seguranca":{"top":144.99081420898438,"left":646.5533103942871,"width":110,"hidden":0},"DiasEnviarEmailLembreteOrdemCompra":{"top":167.9993896484375,"left":87.88296890258789,"width":55.039215999999996,"hidden":0},"OC_Email_SMTP_Autenticacao":{"top":167.98406982421875,"left":457.93505477905273,"width":55.039215999999996,"hidden":0},"DiasAtrasoEnviarEmailOrdemCompra":{"top":190.99264526367188,"left":111.32046890258789,"width":55.039215999999996,"hidden":0},"tituloEmailAlertaOC":{"top":210.998779296875,"left":459.9571189880371,"width":null,"hidden":0},"OC_Alerta_Email":{"top":236.99447631835938,"left":492.34069442749023,"width":313.039216,"hidden":0},"OC_Alerta_Email_SMTP_Usuario":{"top":259.98773193359375,"left":485.09496688842773,"width":313.039216,"hidden":0},"OC_Alerta_Email_SMTP_Senha":{"top":282.9963073730469,"left":492.12621688842773,"width":311.039216,"hidden":0},"OC_Alerta_Email_SMTP_Servidor":{"top":305.9895935058594,"left":450.045955657959,"width":313.039216,"hidden":0},"OC_Alerta_Email_SMTP_Porta":{"top":328.9981689453125,"left":463.7101707458496,"width":55.039215999999996,"hidden":0},"OC_Alerta_Email_SMTP_Seguranca":{"top":328.9981689453125,"left":646.5533103942871,"width":110,"hidden":0},"OC_Alerta_Email_SMTP_Autenticacao":{"top":351.9914245605469,"left":457.93505477905273,"width":55.039215999999996,"hidden":0}}',
			'nfentrada' => '{"associarCfopOcParaNf":{"top":7,"left":30,"width":64,"hidden":0},"conferenciaCegaCompras":{"top":7,"left":606,"width":64,"hidden":0},"verificarImpostosNotaRecebida":{"top":30,"left":89,"width":64,"hidden":0},"obrigatorioOCNotaFiscalCompra":{"top":30,"left":532,"width":64,"hidden":0},"formaRateioFreteNotaRecebida":{"top":53,"left":161,"width":112,"hidden":0},"atualizarOrigemProdutoComNotaRecebida":{"top":53,"left":512,"width":64,"hidden":0},"reprocessarHistoricoTodasEmpresas":{"top":76,"left":103,"width":64,"hidden":0},"atualizarAliquotasAoImportarXML":{"top":99,"left":527,"width":63,"hidden":0},"vincularProdutoNFRecebidaOCQtdDiferentes":{"top":122,"left":91,"width":64,"hidden":0},"cadastrarGtinApartirDeNF":{"top":145,"left":546,"width":63,"hidden":0},"incluirProdutoNotaFiscalEntrada":{"top":168,"left":94,"width":63,"hidden":0},"vincularProdutoNFDevolucaoSemVinculoNFSaida":{"top":168,"left":541,"width":63,"hidden":0},"zeraCustoManualFinalizarEstoqueEntrada":{"top":214,"left":76,"width":63,"hidden":0}}',
			'pcp' => '{"nivelMaximoComposicaoGeracaoOP":{"top":7,"left":7.953125,"width":88,"hidden":0},"locacaoPadraoProducao":{"top":30,"left":168.703125,"width":275,"hidden":0},"locacaoPadraoEstoque":{"top":53,"left":173.28125,"width":275,"hidden":0},"consumoMaterialOpSemEstoque":{"top":76,"left":184.109375,"width":95,"hidden":0},"motivoParadaIntervalo":{"top":122,"left":150.90625,"width":275,"hidden":0},"motivoParadaFimTurno":{"top":145,"left":172.234375,"width":275,"hidden":0},"habilitaRecebimentoMateriais":{"top":99,"left":170.765625,"width":95,"hidden":0},"locacaoPadraoSimulacao":{"top":168,"left":229.234375,"width":275,"hidden":1}}',
			'ordemTransferencia' => '{"validaDadosAoFaturarPedidoTransferencia":{"top":30,"left":19.09375,"width":63,"hidden":0},"gerarPedidoPorItemOT":{"top":7,"left":62.484375,"width":63,"hidden":0},"centralDeComprasGrupo":{"top":53,"left":108.984375,"width":239,"hidden":0}}',
			'conhecimento' => '{"atualizarAliquotasConhecimentoAoImportarXML":{"top":7,"left":81.75,"width":55,"hidden":0},"gerarPagarAutoAoImportarCTe":{"top":30,"left":21.066680908203125,"width":54,"hidden":0}}',
		);

		//
		$SMT012 = mysql_query("SHOW TABLES LIKE 'SMT012'");
		// Smartness colocou um campo do IDE deles, valido se existe na tabela na base para mostrar
		if(mysqli_num_rows($SMT012) > 0) {
			$type = 'select';
		}else {
			$type = 'hidden';
		}

		
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('cad058FormConfEstoque');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'SRMNomeTab' => array('name' => 'SRMNomeTab', 'label' => 'Título das abas no SRM', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Nome Fantasia', 'value' => 'D024_Nome_Fantasia'),
							array('title' => 'Razão Social', 'value' => 'D024_Nome_Empresa')
						)
					),
					'usarDescontoProdutosCompras' => array('name' => 'usarDescontoProdutosCompras', 'label' => 'Desconto por produto', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'atualizarCustoVendaComCustoMedio' => array('name' => 'atualizarCustoVendaComCustoMedio', 'label' => 'Atualizar Custo de Venda com Custo Médio do Produto', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'comprasCalcularST' => array('name' => 'comprasCalcularST', 'label' => 'Calcular ST', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'diasGerarDataVencimento' => array('name' => 'diasGerarDataVencimento', 'label' => 'Dias a se contar para gerar Data de Vencimento', 'type' => 'text','uppercase' => false),

					'comprasCalcularIPI' => array('name' => 'comprasCalcularIPI', 'label' => 'Calcular IPI', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'comprasCalcularICMS' => array('name' => 'comprasCalcularICMS', 'label' => 'Calcular ICMS', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'exibirMarcaImpressaoCompra' => array('name' => 'exibirMarcaImpressaoCompra', 'label' => 'Exibir marca na impressão', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'exibirFotoImpressaoCompra' => array('name' => 'exibirFotoImpressaoCompra', 'label' => 'Exibir foto na impressão', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'desabilitarCotacaoEmergencia' => array('name' => 'desabilitarCotacaoEmergencia', 'label' => 'Desabilitar cotação de emergência', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'desabilitarCompraEmergencia' => array('name' => 'desabilitarCompraEmergencia', 'label' => 'Desabilitar compra de emergência', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'desabilitarMontagemEmergencia' => array('name' => 'desabilitarMontagemEmergencia', 'label' => 'Desabilitar montagem de emergência', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'desabilitarServicoEmergencia' => array('name' => 'desabilitarServicoEmergencia', 'label' => 'Desabilitar serviço de emergência', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'desabilitarProducaoEmergencia' => array('name' => 'desabilitarProducaoEmergencia', 'label' => 'Desabilitar produção emergência', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),

					'impressaoEtiquetasPadraoJava' => array('name' => 'impressaoEtiquetasPadraoJava', 'label' => 'Utilizar impressão de etiquetas via Java?', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'estoqueSugestaoCompra' => array('name' => 'estoqueSugestaoCompra', 'label' => 'Utilizar Estoque:', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Físico', 'value' => 'F', 'selected' => true),
							array('title' => 'Líquido', 'value' => 'L')
						)
					),
					'estatisticaDeCompraMesesComMovimento' => array('name' => 'estatisticaDeCompraMesesComMovimento', 'label' => 'Gera estatística de compra com base nos meses com movimentação?', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'avisarQtaCompradaMaiorNecessario' => array('name' => 'desabilitarProducaoEmergencia', 'label' => 'Avisar se quantidade comprar para a OC for maior que o<br>necessário para o pedido em compras emergência', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
				),
				'estoque' => array(
					'retiradaInternaUtilizarLeitorBiometrico' => array('name' => 'retiradaInternaUtilizarLeitorBiometrico', 'label' => 'Utiliza leitor biométrico', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'controleProdutoPedido' => array('name' => 'controleProdutoPedido', 'label' => 'Coletor - Controle de produtos', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
				),
				'expedicao' => array(
					'expedicaoDeMercadoria' => array('name' => 'expedicaoDeMercadoria', 'label' => 'Expedição de mercadorias', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Padrão', 'value' => 'normal'),
							array('title' => 'Coletor', 'value' => 'coletor')
						)
					),
					'modeloColetor' => array('name' => 'modeloColetor', 'label' => 'Pesquisar produto coletor', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Locação', 'value' => 'locacao'),
							array('title' => 'Produto', 'value' => 'pedidoProduto')
						)
					),
					//'informarBoxNaSeparacao' => array('name' => 'informarBoxNaSeparacao', 'label' => 'Coletor - Obrigatório<br /> box na separação', 'type' => 'select', 'select' =>
					//	array(
					//		array('title' => 'Sim', 'value' => 'S'),
					//		array('title' => 'Não', 'value' => 'N')
					//	)
					//),
					'conferenciaCegaVendas' => array('name' => 'conferenciaCegaVendas', 'label' => 'Utilizar conferência cega', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'modeloExpedicao' => array('name' => 'modeloExpedicao', 'label' => 'Modelo de expedição', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Modelo 1', 'value' => '1'),
							array('title' => 'Modelo 2', 'value' => '2')
						)
					),
					'desabilitarSeparacao' => array('name' => 'desabilitarSeparacao', 'label' => 'Desabiltar Separação?', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Não', 'value' => 'N'),
							array('title' => 'Sim', 'value' => 'S')
						)
					),
					'desabilitarConferencia' => array('name' => 'desabilitarConferencia', 'label' => 'Desabiltar Conferência?', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Não', 'value' => 'N'),
							array('title' => 'Sim', 'value' => 'S')
						)
					),
					'formatoEtiquetaCorreio' => array('name' => 'formatoEtiquetaCorreio', 'label' => 'Formato da impressão da etiqueta correio', 'type' => 'select', 'select' =>
						array(
							array('title' => '...', 'value' => ''),
							array('title' => '4 por folha tamanho A4', 'value' => 'A4'),
							array('title' => '6 por folha tamanho A4', 'value' => 'A4_6'),
							array('title' => 'Envelope padrão DL lado direito', 'value' => 'ENV_DL'),
							array('title' => 'Envelope padrão DL lado esquerdo', 'value' => 'ENV_DL_ESQ'),
							array('title' => 'Envelope padrão C5 lado direito', 'value' => 'ENV_C5'),
							array('title' => 'Envelope padrão C5 lado esquerdo', 'value' => 'ENV_C5_ESQ'),
							array('title' => 'Envelope padrão B4', 'value' => 'ENV_B4'),
							array('title' => 'Etiqueta termica 10x10', 'value' => 'ETQ_10x10'),
							array('title' => 'Etiqueta termica 16x10', 'value' => 'ETQ_16x10')
						)
					),
					'ValorDeclaradoCorreios' => array('name' => 'ValorDeclaradoCorreios', 'label' => 'Valor declarado', 'type' => 'moeda'),
					'conferenciaVolumes' => array('name' => 'conferenciaVolumes', 'label' => 'Utilizar conferência volumes', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'embalagemPadraoVolume' => array('name' => 'embalagemPadraoVolume', 'label' => 'Embalagem padrão para gerar volume', 'type' => 'select', 
						'select' => gGeraSelect('D200', 'D200_Id', 'D200_Descricao', '', true)
					),
				),
				'cotacao' => array(
					'nivelAlcadaAtualizarCustoVenda' => array('name' => 'nivelAlcadaAtualizarCustoVenda', 'label' => 'Alçada atualizar custo produto na venda', 'type' => 'text'),
					'nivelAlcadaAtualizarCustoProduto' => array('name' => 'nivelAlcadaAtualizarCustoProduto', 'label' => 'Alçada atualizar custo produto', 'type' => 'text'),
					'nivelAlcadaAtualizarCustoFornecedor' => array('name' => 'nivelAlcadaAtualizarCustoFornecedor', 'label' => 'Alçada atualizar custo produto fornecedor', 'type' => 'text'),
					'compradorPadraoCotacao'              => array('name' => 'compradorPadraoCotacao', 'label' => 'Comprador padrão para atualização de preço', 'type' => 'select',
						'select' => gGeraSelect('C007', 'C007_Id', 'C007_Nome')
					),
					'fornecedorPadraoCotacao'              => array('name' => 'fornecedorPadraoCotacao', 'label' => 'Fornecedor padrão para atualização de preço', 'type' => 'pesquisa',
						'pesquisa' => array('D024', 'D024_Id', 'D024_Nome_Empresa', "where D024_Flag_Ativo = 'S'")
					)
				),
				'ordemCompra' => array(
					'permiteOrdemCompraProdutoPreCadastro' => array('name' => 'permiteOrdemCompraProdutoPreCadastro', 'label' => 'Gerar ordem de compra com itens em pré cadastro', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'obrigatorioOCValorFretePreenchido' => array('name' => 'obrigatorioOCValorFretePreenchido', 'label' => 'Caso frete 0/1/2 obrigatorio valor frete', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'obrigatorioOCTransportadoraPreenchido' => array('name' => 'obrigatorioOCTransportadoraPreenchido', 'label' => 'Caso frete 0/1/2 obrigatorio transportadora', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'habilitarbotaoIncluirOC' => array('name' => 'habilitarbotaoIncluirOC', 'label' => 'Habilitar botão incluir OC?', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'toleranciaDiferencaOCNF' => array('name' => 'toleranciaDiferencaOCNF', 'label' => 'Tolerância divergência de valor OC/NF', 'type' => 'moeda', 'casasDecimais' => '3'),
					'PrazoVencerOrdemCompra' => array('name' => 'PrazoVencerOrdemCompra', 'label' => 'Prazo para OC vencer', 'type' => 'text'),
					'habilitaEmail' => array('name' => 'habilitaEmail', 'label' => 'Habilita layout de email ?', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Não', 'value' => 'N'),
							array('title' => 'Sim', 'value' => 'S')
						)
					),
					'habilitaComprasImportacao' => array('name' => 'habilitaComprasImportacao', 'label' => 'Habilita ordem de compra com importação?', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Não', 'value' => 'N'),
							array('title' => 'Sim', 'value' => 'S')
						)
					),
					'controleAlcadasOrdemCompra' => array('name' => 'controleAlcadasOrdemCompra', 'label' => 'Controle de alçadas', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'tituloEmailOC' => array('name' => 'tituloEmailOC', 'label'=>'Email Compras - Ao preencher ele usa esta e-mail para enviar OC e Cotação','type' => 'titulo', 'reject' => true),
					'DiasEnviarEmailLembreteOrdemCompra' => array('name' => 'DiasEnviarEmailLembreteOrdemCompra', 'label' => 'Dias para lembrete de entrega da OC', 'type' => 'text'),
					'DiasAtrasoEnviarEmailOrdemCompra' => array('name' => 'DiasAtrasoEnviarEmailOrdemCompra', 'label' => 'Dias de atraso para aviso da OC', 'type' => 'text'),
					'OC_Email' => array('name' => 'OC_Email','label'=>'E-mail', 'type' => 'text', 'length' => array(50,60), 'uppercase' => false, 'nivel' => 4),
					'OC_Email_SMTP_Servidor' => array('name' => 'OC_Email_SMTP_Servidor','label'=>'Servidor SMTP', 'type' => 'text', 'length' => array(50,60), 'uppercase' => false, 'nivel' => 4),
					'OC_Email_SMTP_Porta' => array('name' => 'OC_Email_SMTP_Porta','label'=>'Porta SMTP', 'type' => 'text', 'length' => array(50,60), 'nivel' => 4),
					'OC_Email_SMTP_Autenticacao' => array('name' => 'OC_Email_SMTP_Autenticacao','label'=>'Autenticação', 'type' => 'text', 'length' => array(50,60), 'uppercase' => false, 'nivel' => 4),
					'OC_Email_SMTP_Usuario' => array('name' => 'OC_Email_SMTP_Usuario','label'=>'Usuário', 'type' => 'text', 'length' => array(50,60), 'uppercase' => false, 'nivel' => 4),
					'OC_Email_SMTP_Senha' => array('name' => 'OC_Email_SMTP_Senha','label'=>'Senha', 'type' => 'password', 'length' => array(50,60), 'uppercase' => false, 'nivel' => 4),
					'OC_Email_SMTP_Seguranca' => array('name' => 'OC_Email_SMTP_Seguranca', 'label' => 'Segurança SMTP', 'type' => 'select',
						'select' => array(
							array('title' => 'Automático', 'value' => '0'),
							array('title' => 'SSL', 'value' => '1'),
							array('title' => 'TLS', 'value' => '2'),
						)
					),
					'tituloEmailAlertaOC' => array('name' => 'tituloEmailAlertaOC', 'label' => 'E-mail de avisos automáticos da OC', 'type' => 'titulo', 'reject' => true),
					'OC_Alerta_Email' => array('name' => 'OC_Alerta_Email', 'label' => 'E-mail', 'type' => 'text', 'length' => array(50, 60), 'uppercase' => false, 'nivel' => 4),
					'OC_Alerta_Email_SMTP_Servidor' => array('name' => 'OC_Alerta_Email_SMTP_Servidor', 'label' => 'Servidor SMTP', 'type' => 'text', 'length' => array(50, 60), 'uppercase' => false, 'nivel' => 4),
					'OC_Alerta_Email_SMTP_Porta' => array('name' => 'OC_Alerta_Email_SMTP_Porta', 'label' => 'Porta SMTP', 'type' => 'text', 'length' => array(50, 60), 'uppercase' => false, 'nivel' => 4),
					'OC_Alerta_Email_SMTP_Autenticacao' => array('name' => 'OC_Alerta_Email_SMTP_Autenticacao', 'label' => 'Autenticação', 'type' => 'text', 'length' => array(50, 60), 'uppercase' => false, 'nivel' => 4),
					'OC_Alerta_Email_SMTP_Usuario' => array('name' => 'OC_Alerta_Email_SMTP_Usuario', 'label' => 'Usuário', 'type' => 'text', 'length' => array(50, 60), 'uppercase' => false, 'nivel' => 4),
					'OC_Alerta_Email_SMTP_Senha' => array('name' => 'OC_Alerta_Email_SMTP_Senha', 'label' => 'Senha', 'type' => 'password', 'length' => array(50, 60), 'uppercase' => false, 'nivel' => 4),
					'OC_Alerta_Email_SMTP_Seguranca' => array('name' => 'OC_Alerta_Email_SMTP_Seguranca', 'label' => 'Segurança SMTP', 'type' => 'select',
						'select' => array(
							array('title' => 'Automático', 'value' => '0'),
							array('title' => 'SSL', 'value' => '1'),
							array('title' => 'TLS', 'value' => '2'),
						)
					),
				),
				'nfentrada' => array(
					'associarCfopOcParaNf' => array('name' => 'associarCfopOcParaNf', 'label' => 'Associação do item com OC irá puxar CFOP da OC', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'conferenciaCegaCompras' => array('name' => 'conferenciaCegaCompras', 'label' => 'Utilizar conferência cega', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'verificarImpostosNotaRecebida' => array('name' => 'verificarImpostosNotaRecebida', 'label' => 'Verificar impostos nota fiscal recebida', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'vincularProdutoNFRecebidaOCQtdDiferentes' => array('name' => 'vincularProdutoNFRecebidaOCQtdDiferentes', 'label' => 'Permitir vincular produto da NF entrada<br> com OC tendo quantidades diferentes', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'vincularProdutoNFDevolucaoSemVinculoNFSaida' => array('name' => 'vincularProdutoNFDevolucaoSemVinculoNFSaida', 'label' => 'Permitir vincular produto NF Devolução<br>sem vínculo NF saída', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'obrigatorioOCNotaFiscalCompra' => array('name' => 'obrigatorioOCNotaFiscalCompra', 'label' => 'Obrigatório vincular NF de compra a OC', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'formaRateioFreteNotaRecebida' => array('name' => 'formaRateioFreteNotaRecebida', 'label' => 'Forma de rateio do frete', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Preço', 'value' => '1'),
							array('title' => 'Quantidade', 'value' => '2'),
						)
					),
					'atualizarOrigemProdutoComNotaRecebida' => array('name' => 'atualizarOrigemProdutoComNotaRecebida', 'label' => 'Alterar origem da mercadoria no cadastro do <br>produto igual a da nota fiscal de compra', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'reprocessarHistoricoTodasEmpresas' => array('name' => 'reprocessarHistoricoTodasEmpresas', 'label' => 'Reprocessa histórico será  executado <br> para todas as empresas', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'atualizarAliquotasAoImportarXML' => array('name' => 'atualizarAliquotasAoImportarXML', 'label' => 'Aplica regra de CFOP e atualiza alíquotas&nbsp<br> ao importar o XML da nota de entrada', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'incluirProdutoNotaFiscalEntrada' => array('name' => 'incluirProdutoNotaFiscalEntrada', 'label' => 'Permitir fazer o cadastro do produto,<br>caso nenhum vinculo seja encontrado', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'cadastrarGtinApartirDeNF' => array('name' => 'cadastrarGtinApartirDeNF', 'label' => 'Cadastrar GTIN com nota de entrada', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),	
					'zeraCustoManualFinalizarEstoqueEntrada' => array('name' => 'zeraCustoManualFinalizarEstoqueEntrada', 'label' => 'Zera custo manual no cadastro do produto<br>se CFOP atualiza custo', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),	
				),
				'pcp' => array(
					'nivelMaximoComposicaoGeracaoOP' => array('name' => 'nivelMaximoComposicaoGeracaoOP', 'label' => 'Nivel máximo de aninhamento que uma composição gera OP (0 para desativar)', 'type' => 'text','uppercase' => false),
					'locacaoPadraoProducao' => array('name' => 'locacaoPadraoProducao', 'label'=>'Locação padrão de destino da movimentação ', 'type' => 'select', 
						'select' => gGeraSelect('D004', 'D004_Id', 'D004_Descricao', 'ORDER BY D004_Descricao', true),
					),
					'locacaoPadraoEstoque' => array('name' => 'locacaoPadraoEstoque', 'label'=>'Locação padrão da origem da movimentação', 'type' => 'select', 
						'select' => gGeraSelect('D004', 'D004_Id', 'D004_Descricao', 'ORDER BY D004_Descricao', true),
					),
					'locacaoPadraoSimulacao' => array('name' => 'locacaoPadraoSimulacao', 'label'=>'Locação padrão simulação de OP.', 'type' => 'select', 
						'select' => gGeraSelect('D004', 'D004_Id', 'D004_Descricao', 'ORDER BY D004_Descricao', true),
					),
					'consumoMaterialOpSemEstoque' => array('name' => 'consumoMaterialOpSemEstoque', 'label' => 'Permite baixar a OP sem saldo em estoque', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'habilitaRecebimentoMateriais' => array('name' => 'habilitaRecebimentoMateriais', 'label' => 'Habilita processo de conferência de materiais', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Não', 'value' => 'N'),
							array('title' => 'Sim', 'value' => 'S')
						)
					),
					'motivoParadaIntervalo' => array('name' => 'motivoParadaIntervalo', 'label'=>'Motivo de parada padrão para o intervalo do turno', 'type' => $type, 
						'select' => gGeraSelect('SMT012', 'SMT012_Id', 'SMT012_Descricao', 'ORDER BY SMT012_Descricao', true),
					),
					'motivoParadaFimTurno' => array('name' => 'motivoParadaFimTurno', 'label'=>'Motivo de parada padrão para o final do turno', 'type' => $type, 
						'select' => gGeraSelect('SMT012', 'SMT012_Id', 'SMT012_Descricao', 'ORDER BY SMT012_Descricao', true),
					),
				),
				'ordemTransferencia' => array(
					'gerarPedidoPorItemOT' => array('name' => 'gerarPedidoPorItemOT', 'label' => 'Gerar um pedido para cada item da OT', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'centralDeComprasGrupo' => array('name' => 'centralDeComprasGrupo', 'label'=>'Central de compras do grupo','type' => 'select', 
						'select' => gGeraSelect('C004','C004_Id','C004_Nome_Abreviado',' WHERE C004_Flag_Ativo="S" ORDER BY C004_Nome_Abreviado ',true),
					),	
					'validaDadosAoFaturarPedidoTransferencia' => array('name' => 'validaDadosAoFaturarPedidoTransferencia', 'label' => 'Valida dados ao faturar pedido de transferência', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
				),
				'conhecimento' => array( 
					'atualizarAliquotasConhecimentoAoImportarXML' => array('name' => 'atualizarAliquotasConhecimentoAoImportarXML', 'label' => 'Atualizar alíquotas ao importar o XML do conhecimento de frete', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'gerarPagarAutoAoImportarCTe' => array('name' => 'gerarPagarAutoAoImportarCTe', 'label' => 'Gerar contas a pagar automático ao Importar XML do conhecimento de frete', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
				),
			)
		);

		$arrayObservacoes = array();
		$arrayDiff = array();
		$sqlC031 = mysql_query("SELECT C031_Campo, C031_Observacao, C031_Valor, C031_Valor_Padrao FROM C031 GROUP BY C031_Campo");
		while($rowC031 = mysql_fetch_array($sqlC031)){
			$arrayObservacoes[$rowC031['C031_Campo']] = $rowC031['C031_Observacao'];
			if($rowC031['C031_Valor'] != $rowC031['C031_Valor_Padrao']){
				$arrayDiff[] = $rowC031['C031_Campo'];
			}
		}
		foreach ($formArray['campos'] as $keyTab => &$camposTab) {
			foreach ($camposTab as $keyCampo => &$campoFormulario) {
				$campoFormulario['extra'] = 'title="'.$arrayObservacoes[$keyCampo].'"';
				if(in_array($keyCampo, $arrayDiff)){
					if(empty($campoFormulario['label'])) { continue; }
					$campoFormulario['label'] = "<font color=\"blue\">".$campoFormulario['label']."</font>";
				}
			}
		}
		unset($camposTab);
		unset($campoFormulario);
		$form->definirFormularioArray($formArray);

		/**
		* PARTE 3: Processamento do formulário (se foi enviado)
		* 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		*/
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
			// Form sem erros
			} else if ($formCodigo == 2) {
				// Update
				require_once('bibliotecas/classes/CAD058.php');
                $CAD058 = new CAD058();
                $retorno = $CAD058->atualizarConfGlobal($form, $g['empresaAtual']);
				if ($retorno == true) {
					echo gGeraAlertMsg('Operação OK', 'Configurações salvas com sucesso!');
				}
			}
		} else {
			// Carrega os valores caso seja um update
			$sql = mysql_query("SELECT * FROM C031 WHERE C031_C004_Id = '{$g['empresaAtual']}'");
			while($row = mysql_fetch_array($sql)){
				$form->campoSetarValor($row['C031_Campo'], $row['C031_Valor']);
			}
		}
		
		/**
		* PARTE 4: Gerar o formulário
		*/
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);
		
		/**
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo  $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		echo $g['smarty']->fetch('formEditor.tpl');
	break;

	case 'cad058FormConfFinanceiro':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		
		/**
		* PARTE 2: Definição do formulário
		*/
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = true;
		if ($formMultiTab) {
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}
		
		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = '0000';
		$form_campo_pk = '0000_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad058FormConfFinanceiro_Btn';
		// Form que será executado ao clicar no 
		$enviar_acaoHref = '/cad/cad058/form/cad058FormConfFinanceiro/';
		// formEditor
		// Form Editor
		$form_editor = array(

			'geral' => '{"vencimentoAgrupamentoBoleto":{"top":76,"left":11.484375,"width":224,"hidden":0},"mensagemLiberacaoDeCredito":{"top":7,"left":30.484375,"width":64,"hidden":0},"verificarSaldoConta":{"top":30,"left":44.53125,"width":64,"hidden":0},"D027portadorEmpresa":{"top":53,"left":29.21875,"width":223,"hidden":0}}',
			'receber' => '{"exibirTitulosReceberAgrupados":{"top":7,"left":163.25,"width":64,"hidden":0},"aceitarTitulosReceberAtrasados":{"top":30,"left":178.78125,"width":64,"hidden":0},"aceitarVencimentoAnteriorEmissaoReceber":{"top":53,"left":119.28125,"width":64,"hidden":0},"DiasEnviarBoletoCobranca":{"top":76,"left":73.90625,"width":56,"hidden":0},"DiasMaximoEnviarEmailBoletoCobranca":{"top":99,"left":41.875,"width":56,"hidden":0},"DiasEnviarBoletoPadrao":{"top":122,"left":143.078125,"width":56,"hidden":0},"DiasEnviarBoletoLembrete":{"top":145,"left":134.03125,"width":56,"hidden":0},"DiasEnviarNotificacaoBoletoSemRemessa":{"top":168,"left":7.4375,"width":56,"hidden":0},"EnviarLembreteDiaVencimento":{"top":191,"left":118.4375,"width":64,"hidden":0},"HabilitarDataAtualEstornoReceber":{"top":214,"left":74.796875,"width":63,"hidden":1},"atualizaPortadorPagamentoTitulo":{"top":214,"left":136.9375,"width":63,"hidden":0},"portadorCreditoBaixaAgrupada":{"top":237,"left":150.484375,"width":255,"hidden":0},"caixaPadraoBaixaAgrupada":{"top":260,"left":126.53125,"width":255,"hidden":0},"agrupamentoDesabilitado":{"top":283,"left":42.3125,"width":64,"hidden":1},"baixaTitulos":{"top":306,"left":137.953125,"width":64,"hidden":1},"utilizaTrocoBaixa":{"top":352,"left":148.1875,"width":64,"hidden":0}}',
			'pagamento' => '{"gerarVencTituloReceberFinalSemana":{"top":75.9957275390625,"left":456.0202331542969,"width":63,"hidden":0},"D027portadorEmpresa":{"top":6.98529052734375,"left":62.51531982421875,"width":191,"hidden":0},"usarSTPrimeiraParcela":{"top":6.98529052734375,"left":509.2984313964844,"width":63,"hidden":0},"primeiroVencimentoTipo":{"top":29.993881225585938,"left":164.27694702148438,"width":112,"hidden":0},"primeiroVencimentoNum":{"top":29.993881225585938,"left":319.9908142089844,"width":72.03921600000001,"hidden":0},"usarIPIPrimeiraParcela":{"top":29.993881225585938,"left":524.0349426269531,"width":63,"hidden":0},"tipoIntervalodias":{"top":52.98712158203125,"left":116.65133666992188,"width":112,"hidden":0},"IntervalorDias":{"top":52.98712158203125,"left":319.9908142089844,"width":72.03921600000001,"hidden":0},"usarFretePrimeiraParcela":{"top":52.98712158203125,"left":497.3652038574219,"width":63,"hidden":0},"numeroMaxParcelas":{"top":75.9957275390625,"left":42.616424560546875,"width":184.039216,"hidden":0},"numeroParcelasPadrao":{"top":98.98898315429688,"left":93.78060913085938,"width":184.039216,"hidden":0},"dataPrimeiroVencimentoPadrao":{"top":121.99755859375,"left":50.551483154296875,"width":192,"hidden":0}}',
			'pagar' => '{"contasPagarUnificado":{"top":7,"left":118.09375,"width":64,"hidden":0},"exibirTitulosPagarAgrupados":{"top":30,"left":116.28125,"width":64,"hidden":0},"aceitarTitulosPagarAtrasados":{"top":53,"left":139.515625,"width":64,"hidden":0},"aceitarVencimentoAnteriorEmissaoPagar":{"top":76,"left":69.21875,"width":64,"hidden":0},"aceitarTitulosPagarNumeroValorDuplicataIguais":{"top":99,"left":3.765625,"width":64,"hidden":0}, "HabilitarDataAtualEstornoPagar":{"top":122.00001525878906,"left":26.79998779296875,"width":64,"hidden":1}}',
			'boleto' => '{"tituloEmailBoleto":{"top":3.984375,"left":477,"width":null,"hidden":1},"atualizarNossoNumeroBoleto":{"top":6.984375,"left":149.859375,"width":64,"hidden":0},"mostrarDemonstrativoTItulosAgrupadosBoleto":{"top":30,"left":72.4375,"width":64,"hidden":0},"Boleto_Email":{"top":30,"left":508.359375,"width":312,"hidden":0},"mostrarLogoImpressaoBoleto":{"top":52.984375,"left":23.296875,"width":64,"hidden":0},"Boleto_Email_SMTP_Usuario":{"top":52.96875,"left":501.078125,"width":312,"hidden":0},"Boleto_Email_SMTP_Senha":{"top":75.984375,"left":508.140625,"width":311,"hidden":0},"Boleto_Email_SMTP_Servidor":{"top":121.984375,"left":466.03125,"width":314,"hidden":0},"Boleto_Email_SMTP_Porta":{"top":144.984375,"left":479.6875,"width":71,"hidden":0},"Boleto_Email_SMTP_Seguranca":{"top":145,"left":662.515625,"width":111,"hidden":0},"Boleto_Email_SMTP_Autenticacao":{"top":167.96875,"left":473.90625,"width":71,"hidden":0}}',
			'liberacaoCredito' => '{"mostrarDiasSemNota":{"top":168,"left":24.984375,"width":63,"hidden":0},"portadoresLiberadosAVista":{"top":7,"left":113.8125,"width":232,"hidden":0},"portadoresLiberados":{"top":30,"left":148.515625,"width":232,"hidden":0},"portadoresNaoLiberado":{"top":53,"left":127.453125,"width":232,"hidden":0},"diasUltimaVendaPedirAnaliseCredito":{"top":76,"left":73.796875,"width":56,"hidden":0},"prazoSolicitarAnaliseFaturar":{"top":99,"left":23.609375,"width":56,"hidden":0},"somarLimiteCnpjMatriz":{"top":122,"left":50.40625,"width":64,"hidden":0},"validarNFEAnaliseDeCredito":{"top":145,"left":50.734375,"width":64,"hidden":0}}'

		);
		
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('cad058FormConfFinanceiro');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'mensagemLiberacaoDeCredito' => array('name' => 'mensagemLiberacaoDeCredito', 'label' => 'Notificar liberação de crédito', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'verificarSaldoConta' => array('name' => 'verificarSaldoConta', 'label' => 'Verificar saldo das contas', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'D027portadorEmpresa' => array('name' => 'D027portadorEmpresa', 'label' => 'Portador padrão da empresa', 'type' => 'select', 
						'select' => gGeraSelect('D027', 'D027_Id', 'D027_Portador','LEFT JOIN D007 ON D007_Id = D027_D007_Id LEFT JOIN C004 ON C004_Id = D007_C004_Id WHERE C004_Cnpj LIKE "%'.$CNPJ_Empresa_Corrente.'%" OR ifnull(D027_D007_Id,0) = 0 order by D027_Portador', true, $mT005['T005_Pgto_Portador'])
					),
					'vencimentoAgrupamentoBoleto' => array('name' => 'vencimentoAgrupamentoBoleto', 'label' => 'Vencimento agrupamento boleto', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Não gerar boleto', 'value' => '0', 'selected' => true),
							array('title' => 'Utilizar menor vencimento', 'value' => '1'),
							array('title' => 'Utilizar maior vencimento', 'value' => '2'),
						)
					),
				),
				'pagamento' => array(
					'D027portadorEmpresa' => array('name' => 'D027portadorEmpresa', 'label' => 'Portador padrão da empresa', 'type' => 'select', 
						'select' => gGeraSelect('D027', 'D027_Id', 'D027_Portador','LEFT JOIN D007 ON D007_Id = D027_D007_Id LEFT JOIN C004 ON C004_Id = D007_C004_Id WHERE C004_Cnpj LIKE "%'.$CNPJ_Empresa_Corrente.'%" OR ifnull(D027_D007_Id,0) = 0 order by D027_Portador', true, $mT005['T005_Pgto_Portador'])
					),
					'IntervalorDias' => array('name' => 'IntervalorDias', 'label' => '', 'type' => 'text'),
					'numeroMaxParcelas' => array('name' => 'numeroMaxParcelas', 'label' => 'Nº máximo parcelas da empresa', 'type' => 'text'),
					'primeiroVencimentoNum' => array('name' => 'primeiroVencimentoNum', 'label' => '', 'type' => 'text'),
					'numeroParcelasPadrao' => array('name' => 'numeroParcelasPadrao', 'label' => 'Nº de parcelas padrão', 'type' => 'text'),
					'tipoIntervalodias' => array('name' => 'tipoIntervalodias', 'label' => 'Dia Fixo/Intervalo', 'type' => 'select', 'select' =>
						array(
							array('title' => 'INTERVALO', 'value' => 'INTERVALO'),
							array('title' => 'DIA FIXO', 'value' => 'DIA FIXO')
						)
					),
					'primeiroVencimentoTipo' => array('name' => 'primeiroVencimentoTipo', 'label' => '1º Venc', 'type' => 'select', 'select' =>
						array(
							array('title' => 'DIAS', 'value' => 'DIAS'),
							array('title' => 'DATA', 'value' => 'DATA')
						)
					),
					'usarSTPrimeiraParcela' => array('name' => 'usarSTPrimeiraParcela', 'label' => 'ST na primeira parcela', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'usarIPIPrimeiraParcela' => array('name' => 'usarIPIPrimeiraParcela', 'label' => 'IPI primeira parcela', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'dataPrimeiroVencimentoPadrao' => array('name' => 'dataPrimeiroVencimentoPadrao', 'label' => 'Calcular vencimento a partir da', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Data atual', 'value' => 'A'),
							array('title' => 'Data Emissão', 'value' => 'E')
						)
					),
					'usarFretePrimeiraParcela' => array('name' => 'usarFretePrimeiraParcela', 'label' => 'Frete na primeira parcela', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'gerarVencTituloReceberFinalSemana' => array('name' => 'gerarVencTituloReceberFinalSemana', 'label' => 'Gera vencimento final de semana', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
				),
				'receber' => array(
					'exibirTitulosReceberAgrupados' => array('name' => 'exibirTitulosReceberAgrupados', 'label' => 'Exibir títulos agrupados/reparcelados', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'aceitarTitulosReceberAtrasados' => array('name' => 'aceitarTitulosReceberAtrasados', 'label' => 'Aceitar títulos a receber atrasados', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'aceitarVencimentoAnteriorEmissaoReceber' => array('name' => 'aceitarVencimentoAnteriorEmissaoReceber', 'label' => 'Aceitar data de vencimento anterior à emissão', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'DiasEnviarBoletoPadrao' => array('name' => 'DiasEnviarBoletoPadrao', 'label' => 'Dias para envio boleto padrão automático', 'type' => 'text'),
					'DiasEnviarBoletoLembrete' => array('name' => 'DiasEnviarBoletoLembrete', 'label' => 'Dias para envio boleto lembrete automático', 'type' => 'text'),
					'DiasEnviarBoletoCobranca' => array('name' => 'DiasEnviarBoletoCobranca', 'label' => 'Período em dias para envio boleto cobrança automático', 'type' => 'text'),
					'DiasMaximoEnviarEmailBoletoCobranca' => array('name' => 'DiasMaximoEnviarEmailBoletoCobranca', 'label' => 'Máximo dias de atraso para envio boleto cobrança automático', 'type' => 'text'),
					'DiasEnviarNotificacaoBoletoSemRemessa' => array('name' => 'DiasEnviarNotificacaoBoletoSemRemessa', 'label' => 'Máximo dias até vencimento para notificação de boleto sem remessa', 'type' => 'text'),
					'EnviarLembreteDiaVencimento' => array('name' => 'EnviarLembreteDiaVencimento', 'label' => 'Enviar boleto lembrete na data de vencimento?', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'agrupamentoDesabilitado' => array('name' => 'agrupamentoDesabilitado', 'label' => 'Desabilitar agrupamento de títulos em relação ao saldo? (S/N)', 'type' => 'select', 'select' =>
					array(
							array('title' => 'Não', 'value' => 'N'),
							array('title' => 'Sim', 'value' => 'S')
						)
					),
					'baixaTitulos' => array('name' => 'baixaTitulos', 'label' => 'Baixa de títulos com crédito parcial ? (S/N)', 'type' => 'select', 'select' =>
					array(
							array('title' => 'Não', 'value' => 'N'),
							array('title' => 'Sim', 'value' => 'S')
						)
					),
					'HabilitarDataAtualEstornoReceber' => array('name' => 'HabilitarDataAtualEstornoReceber', 'label' => 'Habilitar a gravação da data atual para o estorno? (S/N)', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'atualizaPortadorPagamentoTitulo' => array('name' => 'atualizaPortadorPagamentoTitulo', 'label' => 'Atualiza o portador do pagamento no título', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'portadorCreditoBaixaAgrupada' => array('name' => 'portadorCreditoBaixaAgrupada', 'label' => 'Portador de crédito de baixa automática', 'type' => 'select', 
					'select' => gGeraSelect('D027', 'D027_Id', 'D027_Portador', '', true)
					),
					'caixaPadraoBaixaAgrupada' => array('name' => 'caixaPadraoBaixaAgrupada', 'label' => 'Caixa para movimentação na baixa agrupada', 'type' => 'select', 
					'select' => gGeraSelect('D007', 'D007_Id', 'D007_Nome_Banco', '', true)
					),
					'utilizaTrocoBaixa' => array('name' => 'utilizaTrocoBaixa', 'label' => 'Utiliza troco na baixa de títulos a receber', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
				),
				'pagar' => array(
					'exibirTitulosPagarAgrupados' => array('name' => 'exibirTitulosPagarAgrupados', 'label' => 'Exibir títulos agrupados/reparcelados', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'contasPagarUnificado' => array('name' => 'contasPagarUnificado', 'label' => 'Controle de contas a pagar unificado', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'aceitarTitulosPagarAtrasados' => array('name' => 'aceitarTitulosPagarAtrasados', 'label' => 'Aceitar títulos a pagar atrasados', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'aceitarVencimentoAnteriorEmissaoPagar' => array('name' => 'aceitarVencimentoAnteriorEmissaoPagar', 'label' => 'Aceitar Data de vencimento anterior à emissão', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'aceitarTitulosPagarNumeroValorDuplicataIguais' => array('name' => 'aceitarTitulosPagarNumeroValorDuplicataIguais', 'label' => 'Aceitar número e valor de duplicata iguais à algum existente', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'HabilitarDataAtualEstornoPagar' => array('name' => 'HabilitarDataAtualEstornoPagar', 'label' => 'Habilitar a gravação da data atual para o estorno? (S/N)', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
				),
				'boleto' => array(
					'atualizarNossoNumeroBoleto' => array('name' => 'atualizarNossoNumeroBoleto', 'label' => 'Atualizar nosso número do boleto', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'mostrarDemonstrativoTItulosAgrupadosBoleto' => array('name' => 'mostrarDemonstrativoTItulosAgrupadosBoleto', 'label' => 'Exibir demonstrativo títulos agrupados no boleto?', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Não', 'value' => 'N'),
							array('title' => 'Sim', 'value' => 'S')
						)
					),
					'mostrarLogoImpressaoBoleto' => array('name' => 'mostrarLogoImpressaoBoleto', 'label' => 'Exibir a logomarca da empresa nas impressões dos boletos', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'tituloEmailBoleto' => array('name' => 'tituloEmailBoleto', 'label'=>'Email Boleto - Ao preencher ele usa esta e-mail para enviar Boletos','type' => 'titulo', 'reject' => true),
					'Boleto_Email' => array('name' => 'Boleto_Email','label'=>'E-mail', 'type' => 'text', 'length' => array(50,60), 'uppercase' => false, 'nivel' => 4),
					'Boleto_Email_SMTP_Servidor' => array('name' => 'Boleto_Email_SMTP_Servidor','label'=>'Servidor SMTP', 'type' => 'text', 'length' => array(50,60), 'uppercase' => false, 'nivel' => 4),
					'Boleto_Email_SMTP_Porta' => array('name' => 'Boleto_Email_SMTP_Porta','label'=>'Porta SMTP', 'type' => 'text', 'length' => array(50,60), 'nivel' => 4),
					'Boleto_Email_SMTP_Autenticacao' => array('name' => 'Boleto_Email_SMTP_Autenticacao','label'=>'Autenticação', 'type' => 'text', 'length' => array(50,60), 'uppercase' => false, 'nivel' => 4),
					'Boleto_Email_SMTP_Usuario' => array('name' => 'Boleto_Email_SMTP_Usuario','label'=>'Usuário', 'type' => 'text', 'length' => array(50,60), 'uppercase' => false, 'nivel' => 4),
					'Boleto_Email_SMTP_Senha' => array('name' => 'Boleto_Email_SMTP_Senha','label'=>'Senha', 'type' => 'password', 'length' => array(50,60), 'uppercase' => false, 'nivel' => 4),
					'Boleto_Email_SMTP_Seguranca' => array('name' => 'Boleto_Email_SMTP_Seguranca', 'label' => 'Segurança SMTP', 'type' => 'select',
						'select' => array(
							array('title' => 'Automático', 'value' => '0'),
							array('title' => 'SSL', 'value' => '1'),
							array('title' => 'TLS', 'value' => '2'),
						)
					),
				),
				'liberacaoCredito' => array(
					'portadoresLiberadosAVista' => array('name' => 'portadoresLiberadosAVista', 'label' => 'Portadores liberados a vista', 'type' => 'text'),
					'portadoresLiberados' => array('name' => 'portadoresLiberados', 'label' => 'Portadores liberados', 'type' => 'text'),
					'portadoresNaoLiberado' => array('name' => 'portadoresNaoLiberado', 'label' => 'Portadores não liberados', 'type' => 'text'),
					'diasUltimaVendaPedirAnaliseCredito' => array('name' => 'diasUltimaVendaPedirAnaliseCredito', 'label' => 'Dias sem nota para bloquear crédito', 'type' => 'text'),
					'prazoSolicitarAnaliseFaturar' => array('name' => 'prazoSolicitarAnaliseFaturar', 'label' => 'Prazo para faturar sem nova análise de crédito', 'type' => 'moeda', 'casasDecimais' => '0'),
					'somarLimiteCnpjMatriz' => array('name' => 'somarLimiteCnpjMatriz', 'label' => 'Somar limite de crédito pela CNPJ Matriz', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'validarNFEAnaliseDeCredito' => array('name' => 'validarNFEAnaliseDeCredito', 'label' => 'Validar NF-e ao realiar Análide de Crédito', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'mostrarDiasSemNota' => array('name' => 'mostrarDiasSemNota', 'label' => 'Mostrar dias sem nota, no orçamento e pedido', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Não', 'value' => 'N'),
							array('title' => 'Sim', 'value' => 'S')
						)
					),
				),
			),
		);

		$arrayObservacoes = array();
		$arrayDiff = array();
		$sqlC031 = mysql_query("SELECT C031_Campo, C031_Observacao, C031_Valor, C031_Valor_Padrao FROM C031 GROUP BY C031_Campo");
		while($rowC031 = mysql_fetch_array($sqlC031)){
			$arrayObservacoes[$rowC031['C031_Campo']] = $rowC031['C031_Observacao'];
			if($rowC031['C031_Valor'] != $rowC031['C031_Valor_Padrao']){
				$arrayDiff[] = $rowC031['C031_Campo'];
			}
		}
		foreach ($formArray['campos'] as $keyTab => &$camposTab) {
			foreach ($camposTab as $keyCampo => &$campoFormulario) {
				$campoFormulario['extra'] = 'title="'.$arrayObservacoes[$keyCampo].'"';
				if(in_array($keyCampo, $arrayDiff)){
					if(empty($campoFormulario['label'])) { continue; }
					$campoFormulario['label'] = "<font color=\"blue\">".$campoFormulario['label']."</font>";
				}
			}
		}
		unset($camposTab);
		unset($campoFormulario);
		$form->definirFormularioArray($formArray);

		/**
		* PARTE 3: Processamento do formulário (se foi enviado)
		* 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		*/
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
			// Form sem erros
			} else if ($formCodigo == 2) {
				if($form->campoValorEnviado('numeroParcelasPadrao')<$form->campoValorEnviado('numeroMaxParcelas')){
					// Update
					require_once('bibliotecas/classes/CAD058.php');
	                $CAD058 = new CAD058();
	                $retorno = $CAD058->atualizarConfGlobal($form, $g['empresaAtual']);
					if ($retorno == true) {
						echo gGeraAlertMsg('Operação OK', 'Configurações salvas com sucesso!');
					}
				}else{
					echo gGeraAlertMsg('Erro', 'Número de parcelas padrão não pode ser maior que o número máximo de parcelas.');
				}
			}
		} else {
			// Carrega os valores caso seja um update
			$sql = mysql_query("SELECT * FROM C031 WHERE C031_C004_Id = '{$g['empresaAtual']}'");
			while($row = mysql_fetch_array($sql)){
				$form->campoSetarValor($row['C031_Campo'], $row['C031_Valor']);
			}
		}
		
		/**
		* PARTE 4: Gerar o formulário
		*/
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);
		
		/**
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo  $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		echo $g['smarty']->fetch('formEditor.tpl');
	break;

	case 'cad058FormConfImportacao':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		
		/**
		* PARTE 2: Definição do formulário
		*/
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = false;
		if ($formMultiTab) {
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}
		
		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = '0000';
		$form_campo_pk = '0000_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad058FormConfImportacao_Btn';
		// Form que será executado ao clicar no 
		$enviar_acaoHref = '/cad/cad058/form/cad058FormConfImportacao/';
		// formEditor
		// Form Editor
		$form_editor = array(
			'geral' => '{"incluirItensPOnaDIFornecedorDiferente":{"top":122,"left":2.5,"width":111,"hidden":0},"DIUsarBotaoEntradaEstoque":{"top":30,"left":200.921875,"width":112,"hidden":0},"diasMedioPrazo":{"top":53,"left":185.84375,"width":104,"hidden":0},"usarMoedaEstrangeira":{"top":76,"left":105.75,"width":112,"hidden":0},"permitirProdutosDuplicadosImportacao":{"top":99,"left":156.109375,"width":112,"hidden":0},"MostrarDocDispesaNaDI":{"top":7,"left":35.515625,"width":112,"hidden":0}}',
		);
		
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('cad058FormConfImportacao');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'DIUsarBotaoEntradaEstoque' => array('name' => 'DIUsarBotaoEntradaEstoque', 'label' => 'DI - Botão entrada estoque', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Habilitar', 'value' => 'S'),
							array('title' => 'Desabilitar', 'value' => 'N')
						)
					),
					'diasMedioPrazo' => array('name' => 'diasMedioPrazo', 'label' => 'Prazo calculo estoque MP/LP', 'type' => 'text'),
					'MostrarDocDispesaNaDI' => array('name' => 'MostrarDocDispesaNaDI', 'label' => 'Mostrar documentos da despesa junto com documentos DI?', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'usarMoedaEstrangeira' => array('name' => 'usarMoedaEstrangeira', 'label' => 'Usar moeda estrangeira em "Contas a Pagar"?', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'permitirProdutosDuplicadosImportacao' => array('name' => 'permitirProdutosDuplicadosImportacao', 'label' => 'Permitir produtos duplicados na DI?', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'incluirItensPOnaDIFornecedorDiferente' => array('name' => 'incluirItensPOnaDIFornecedorDiferente', 'label' => 'Permitir incluir produtos na DI via PO com fornecedores diferentes?', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),				
				),
			)
		);

		$arrayObservacoes = array();
		$arrayDiff = array();
		$sqlC031 = mysql_query("SELECT C031_Campo, C031_Observacao, C031_Valor, C031_Valor_Padrao FROM C031 GROUP BY C031_Campo");
		while($rowC031 = mysql_fetch_array($sqlC031)){
			$arrayObservacoes[$rowC031['C031_Campo']] = $rowC031['C031_Observacao'];
			if($rowC031['C031_Valor'] != $rowC031['C031_Valor_Padrao']){
				$arrayDiff[] = $rowC031['C031_Campo'];
			}
		}
		foreach ($formArray['campos'] as $keyTab => &$camposTab) {
			foreach ($camposTab as $keyCampo => &$campoFormulario) {
				$campoFormulario['extra'] = 'title="'.$arrayObservacoes[$keyCampo].'"';
				if(in_array($keyCampo, $arrayDiff)){
					if(empty($campoFormulario['label'])) { continue; }
					$campoFormulario['label'] = "<font color=\"blue\">".$campoFormulario['label']."</font>";
				}
			}
		}
		unset($camposTab);
		unset($campoFormulario);
		$form->definirFormularioArray($formArray);

		/**
		* PARTE 3: Processamento do formulário (se foi enviado)
		* 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		*/
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
			// Form sem erros
			} else if ($formCodigo == 2) {
				// Update
				require_once('bibliotecas/classes/CAD058.php');
                $CAD058 = new CAD058();
                $retorno = $CAD058->atualizarConfGlobal($form, $g['empresaAtual']);
				if ($retorno == true) {
					echo gGeraAlertMsg('Operação OK', 'Configurações salvas com sucesso!');
				}
			}
		} else {
			// Carrega os valores caso seja um update
			$sql = mysql_query("SELECT * FROM C031 WHERE C031_C004_Id = '{$g['empresaAtual']}'");
			while($row = mysql_fetch_array($sql)){
				$form->campoSetarValor($row['C031_Campo'], $row['C031_Valor']);
			}
		}
		
		/**
		* PARTE 4: Gerar o formulário
		*/
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);
		
		/**
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo  $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		echo $g['smarty']->fetch('formEditor.tpl');
	break;

	case 'cad058FormConfMobile':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;

		/**
		* PARTE 2: Definição do formulário
		*/
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = false;
		if ($formMultiTab) {
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}

		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = '0000';
		$form_campo_pk = '0000_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad058FormConfMobile_Btn';
		// Form que será executado ao clicar no
		$enviar_acaoHref = '/cad/cad058/form/cad058FormConfMobile/';
		// formEditor
		// Form Editor
		$form_editor = array(
			'geral' => '{"definirOrdemDeOrdenacao":{"top":99,"left":30,"width":146,"hidden":0},"habilitarServicos":{"top":7,"left":48,"width":64,"hidden":0},"habilitarDescontoPercentualTablet":{"top":7,"left":571,"width":64,"hidden":0},"habilitarTrocas":{"top":30,"left":56.40625,"width":64,"hidden":0},"habilitarDescontoReaisTablet":{"top":30,"left":598,"width":64,"hidden":0},"travarDescontoSuperiorAlcada":{"top":53,"left":10.40625,"width":64,"hidden":0},"habilitarDefinirValorTotalTablet":{"top":53,"left":509,"width":64,"hidden":0},"listarProdutosComPrecoZero":{"top":76,"left":33,"width":64,"hidden":0},"habilitarDescontoValorTotalTablet":{"top":76,"left":578,"width":64,"hidden":0},"permiteAlterarPrecoVenda":{"top":99,"left":504,"width":64,"hidden":0},"tabletHeaderTexto":{"top":145,"left":879.21875,"width":128,"hidden":1},"tabletLoginLogo":{"top":168,"left":857.21875,"width":128,"hidden":1},"liberarEmailImpressao":{"top":191,"left":865,"width":58,"hidden":1}}',
		);

		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('cad058FormConfMobile');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'tabletHeaderTexto' => array('name' => 'tabletHeaderTexto', 'label' => 'Título Tablet', 'type' => 'text'),
					'tabletLoginLogo' => array('name' => 'tabletLoginLogo', 'label' => 'URL Logo Tablet', 'type' => 'text'),
					'liberarEmailImpressao' => array('name' => 'liberarEmailImpressao', 'label' => 'Liberar impressao sem <br />forma de pagamento (tablet)', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'habilitarServicos' => array('name' => 'habilitarServicos', 'label' => 'Habilitar módulo de Servicos', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'habilitarTrocas' => array('name' => 'habilitarTrocas', 'label' => 'Habilitar módulo de Trocas', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'habilitarDescontoPercentualTablet' => array('name' => 'habilitarDescontoPercentualTablet', 'label' => 'Habilitar desconto em percentual', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'habilitarDescontoReaisTablet' => array('name' => 'habilitarDescontoReaisTablet', 'label' => 'Habilitar desconto em reais', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'habilitarDefinirValorTotalTablet' => array('name' => 'habilitarDefinirValorTotalTablet', 'label' => 'Habilitar desconto pela definição do valor total', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'habilitarDescontoValorTotalTablet' => array('name' => 'habilitarDescontoValorTotalTablet', 'label' => 'Habilitar desconto no valor total', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'travarDescontoSuperiorAlcada' => array('name' => 'travarDescontoSuperiorAlcada', 'label' => 'Travar desconto maior que a alçada', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'listarProdutosComPrecoZero' => array('name' => 'listarProdutosComPrecoZero', 'label' => 'Listar produtos com preço zero', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'permiteAlterarPrecoVenda' => array('name' => 'permiteAlterarPrecoVenda', 'label' => 'Permite alterar o preço de venda dos produtos', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'definirOrdemDeOrdenacao' => array('name' => 'definirOrdemDeOrdenacao', 'label' => 'Definine ordenação de pesquisa', 'type' => 'text'),
				),
			)
		);

		$arrayObservacoes = array();
		$arrayDiff = array();
		$sqlC031 = mysql_query("SELECT C031_Campo, C031_Observacao, C031_Valor, C031_Valor_Padrao FROM C031 GROUP BY C031_Campo");
		while($rowC031 = mysql_fetch_array($sqlC031)){
			$arrayObservacoes[$rowC031['C031_Campo']] = $rowC031['C031_Observacao'];
			if($rowC031['C031_Valor'] != $rowC031['C031_Valor_Padrao']){
				$arrayDiff[] = $rowC031['C031_Campo'];
			}
		}
		foreach ($formArray['campos'] as $keyTab => &$camposTab) {
			foreach ($camposTab as $keyCampo => &$campoFormulario) {
				$campoFormulario['extra'] = 'title="'.$arrayObservacoes[$keyCampo].'"';
				if(in_array($keyCampo, $arrayDiff)){
					if(empty($campoFormulario['label'])) { continue; }
					$campoFormulario['label'] = "<font color=\"blue\">".$campoFormulario['label']."</font>";
				}
			}
		}
		unset($camposTab);
		unset($campoFormulario);
		$form->definirFormularioArray($formArray);

		/**
		* PARTE 3: Processamento do formulário (se foi enviado)
		* 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		*/
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
			// Form sem erros
			} else if ($formCodigo == 2) {
				// Update
				require_once('bibliotecas/classes/CAD058.php');
                $CAD058 = new CAD058();
                $retorno = $CAD058->atualizarConfGlobal($form, $g['empresaAtual']);
				if ($retorno == true) {
					echo gGeraAlertMsg('Operação OK', 'Configurações salvas com sucesso!');
				}
			}
		} else {
			// Carrega os valores caso seja um update
			$sql = mysql_query("SELECT * FROM C031 WHERE C031_C004_Id = '{$g['empresaAtual']}'");
			while($row = mysql_fetch_array($sql)){
				$form->campoSetarValor($row['C031_Campo'], $row['C031_Valor']);
			}
		}

		/**
		* PARTE 4: Gerar o formulário
		*/
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

		/**
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo  $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		echo $g['smarty']->fetch('formEditor.tpl');
	break;

	case 'cad058FormConfCyberlog':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;

		/**
		* PARTE 2: Definição do formulário
		*/
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = false;
		if ($formMultiTab) {
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}

		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = '0000';
		$form_campo_pk = '0000_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad058FormConfCyberlog_Btn';
		// Form que será executado ao clicar no
		$enviar_acaoHref = '/cad/cad058/form/cad058FormConfCyberlog/';

		$form_editor = array(
			'geral' => '{"cyberlogUrl":{"top":30,"left":153.22500324249268,"width":392.4,"hidden":0},"cyberlogAccount":{"top":53,"left":157.49999713897705,"width":152.39999999999998,"hidden":0},"cyberlogModule":{"top":53,"left":389.5000123977661,"width":152.39999999999998,"hidden":0},"cyberlogPassword":{"top":76,"left":156.11250019073486,"width":392.4,"hidden":0},"cyberlogAddress":{"top":99,"left":83.26250171661377,"width":152.39999999999998,"hidden":0},"cyberlogDepositNumber":{"top":99,"left":390.7750062942505,"width":88.4,"hidden":0},"cyberlogKey":{"top":121.96250915527344,"left":103.68750476837158,"width":391.4,"hidden":0},"cyberlogConvertUnit":{"top":144.96250915527344,"left":39.17500019073486,"width":63,"hidden":0},"cyberlogInvoiceMethod":{"top":144.9875030517578,"left":275.03751850128174,"width":191,"hidden":0},"cyberlogLockOrder":{"top":167.9875030517578,"left":41.11250019073486,"width":63,"hidden":0},"cyberlogWarehouseUsers":{"top":167.9875030517578,"left":284.0625123977661,"width":184.39999999999998,"hidden":0},"cyberlogOrderDateAsDeliveryDate":{"top":190.99998474121094,"left":38.437500953674316,"width":63,"hidden":0}}',
		);
		
		$form = new Formulario('cad058FormConfCyberlog');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'cyberlogUrl' => array('name' => 'cyberlogUrl', 'label' => 'Url API', 'type' => 'text', 'uppercase' => false),
					'cyberlogAccount' => array('name' => 'cyberlogAccount', 'label' => 'Conta', 'type' => 'text', 'uppercase' => false),
					'cyberlogPassword' => array('name' => 'cyberlogPassword', 'label' => 'Senha', 'type' => 'text', 'uppercase' => false),
					'cyberlogModule' => array('name' => 'cyberlogModule', 'label' => 'Módulo', 'type' => 'text', 'uppercase' => false),
					'cyberlogDepositNumber' => array('name' => 'cyberlogDepositNumber', 'label' => 'Número do depósito', 'type' => 'text', 'uppercase' => false),
					'cyberlogAddress' => array('name' => 'cyberlogAddress', 'label' => 'Endereço do servidor', 'type' => 'text', 'uppercase' => false),
					'cyberlogKey' => array('name' => 'cyberlogKey', 'label' => 'Chave de acesso', 'type' => 'text', 'uppercase' => false),
					'cyberlogConvertUnit' => array('name' => 'cyberlogConvertUnit', 'label' => 'Converter Un. para peças (PC)', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'cyberlogInvoiceMethod' => array('name' => 'cyberlogInvoiceMethod', 'label' => 'Método de Recebimento', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Somente automático', 'value' => '1'),
							array('title' => 'Somente manual', 'value' => '2'),
							array('title' => 'Ambos', 'value' => '3')
						)
					),
					'cyberlogLockOrder' => array('name' => 'cyberlogLockOrder', 'label' => 'Travar pedido ao iniciar tarefa', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'cyberlogWarehouseUsers' => array('name' => 'cyberlogWarehouseUsers', 'label' => 'Usuários almoxarifado', 'type' => 'text', 'uppercase' => false),
					'cyberlogOrderDateAsDeliveryDate' => array('name' => 'cyberlogOrderDateAsDeliveryDate', 'label' => 'Considerar data de entrega', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
				)
			)
		);

		$arrayObservacoes = array();
		$arrayDiff = array();
		$sqlC031 = mysql_query("SELECT C031_Campo, C031_Observacao, C031_Valor, C031_Valor_Padrao FROM C031 GROUP BY C031_Campo");

		while($rowC031 = mysql_fetch_array($sqlC031)) {
			$arrayObservacoes[$rowC031['C031_Campo']] = $rowC031['C031_Observacao'];

			if($rowC031['C031_Valor'] != $rowC031['C031_Valor_Padrao']) {
				$arrayDiff[] = $rowC031['C031_Campo'];
			}
		}

		foreach ($formArray['campos'] as $keyTab => &$camposTab) {
			foreach ($camposTab as $keyCampo => &$campoFormulario) {
				$campoFormulario['extra'] = 'title="'.$arrayObservacoes[$keyCampo].'"';

				if(in_array($keyCampo, $arrayDiff)) {
					if(empty($campoFormulario['label'])) { continue; }

					$campoFormulario['label'] = "<font color=\"blue\">".$campoFormulario['label']."</font>";
				}
			}
		}

		unset($camposTab);
		unset($campoFormulario);

		$form->definirFormularioArray($formArray);
		
		/**
		* PARTE 3: Processamento do formulário (se foi enviado)
		* 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		*/
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
			// Form sem erros
			} else if ($formCodigo == 2) {
				// Update
				require_once('bibliotecas/classes/CAD058.php');
                $CAD058 = new CAD058();
                $retorno = $CAD058->atualizarConfGlobal($form, $g['empresaAtual']);
				if ($retorno == true) {
					echo gGeraAlertMsg('Operação OK', 'Configurações salvas com sucesso!');
				}
			}
		} else {
			// Carrega os valores caso seja um update
			$sql = mysql_query("SELECT * FROM C031 WHERE C031_C004_Id = '{$g['empresaAtual']}'");

			while($row = mysql_fetch_array($sql)) {
				$form->campoSetarValor($row['C031_Campo'], $row['C031_Valor']);
			}
		}

		/**
		* PARTE 4: Gerar o formulário
		*/
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

		/**
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');

		echo  $gerado['pagina'];

		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		
		echo $g['smarty']->fetch('formEditor.tpl');
	break;

	case 'cad058FormConfDuimp':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;

		/**
		* PARTE 2: Definição do formulário
		*/
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = false;
		if ($formMultiTab) {
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}

		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = '0000';
		$form_campo_pk = '0000_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad058FormConfDuimp_Btn';
		// Form que será executado ao clicar no
		$enviar_acaoHref = '/cad/cad058/form/cad058FormConfDuimp/';

		$form_editor = array(
			'geral' => '{"duimpClientIdProducao":{"top":6.9853057861328125,"left":48.636640548706055,"width":392.039216,"hidden":0},"duimpClientSecretProducao":{"top":29.993881225585938,"left":27.696077346801758,"width":392.039216,"hidden":0},"duimpClientIdHomologacao":{"top":52.98713684082031,"left":27.634801864624023,"width":391.039216,"hidden":0},"duimpClientSecretHomologacao":{"top":75.99571228027344,"left":6.678921699523926,"width":391.039216,"hidden":0},"duimpPerfil":{"top":98.98896789550781,"left":113.41911506652832,"width":399,"hidden":0},"duimpAmbiente":{"top":121.99754333496094,"left":91.62071418762207,"width":399,"hidden":0}}',
		);
		
		$form = new Formulario('cad058FormConfDuimp');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'duimpClientIdProducao' => array('name' => 'duimpClientIdProducao', 'label' => 'Client ID produção', 'type' => 'text', 'uppercase' => false),
					'duimpClientSecretProducao' => array('name' => 'duimpClientSecretProducao', 'label' => 'Client Secret produção', 'type' => 'text', 'uppercase' => false),
					'duimpClientIdHomologacao' => array('name' => 'duimpClientIdHomologacao', 'label' => 'Client ID homologação', 'type' => 'text', 'uppercase' => false),
					'duimpClientSecretHomologacao' => array('name' => 'duimpClientSecretHomologacao', 'label' => 'Client Secret homologação', 'type' => 'text', 'uppercase' => false),
					'duimpPerfil' => array('name' => 'duimpPerfil', 'label' => 'Perfil', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Declarante importador/exportador', 'value' => 'IMPEXP'),
							array('title' => 'Depositário', 'value' => 'DEPOSIT'),
							array('title' => 'Operador portuário', 'value' => 'OPERPORT'),
							array('title' => 'Transportador', 'value' => 'TRANSPORT'),
							array('title' => 'PF representante de TETI', 'value' => 'TRANSPEST'),
							array('title' => 'Agente de carga', 'value' => 'AGECARGA'),
							array('title' => 'Remessa expressa/correio', 'value' => 'AGEREMESS'),
							array('title' => 'Ajudante de despachante', 'value' => 'AJUDESPAC'),
							array('title' => 'Instituição financeira', 'value' => 'INSTFINANC'),
							array('title' => 'Ponto de contato OEA', 'value' => 'CONTATOOEA'),
							array('title' => 'Responsável legal OEA', 'value' => 'RESPLEGAL'),
							array('title' => 'Habilitador', 'value' => 'HABILITAD'),
							array('title' => 'Terceiros (outros intervenientes)', 'value' => 'TERCEIROS')
						)
					),
					'duimpAmbiente' => array('name' => 'duimpAmbiente', 'label' => 'Ambiente', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Produção', 'value' => 'PRODUCAO'),
							array('title' => 'Homologação', 'value' => 'HOMOLOGACAO')
						)
					),
				)
			)
		);

		$arrayObservacoes = array();
		$arrayDiff = array();
		$sqlC031 = mysql_query("SELECT C031_Campo, C031_Observacao, C031_Valor, C031_Valor_Padrao FROM C031 GROUP BY C031_Campo");

		while($rowC031 = mysql_fetch_array($sqlC031)) {
			$arrayObservacoes[$rowC031['C031_Campo']] = $rowC031['C031_Observacao'];

			if($rowC031['C031_Valor'] != $rowC031['C031_Valor_Padrao']) {
				$arrayDiff[] = $rowC031['C031_Campo'];
			}
		}

		foreach ($formArray['campos'] as $keyTab => &$camposTab) {
			foreach ($camposTab as $keyCampo => &$campoFormulario) {
				$campoFormulario['extra'] = 'title="'.$arrayObservacoes[$keyCampo].'"';

				if(in_array($keyCampo, $arrayDiff)) {
					if(empty($campoFormulario['label'])) { continue; }

					$campoFormulario['label'] = "<font color=\"blue\">".$campoFormulario['label']."</font>";
				}
			}
		}

		unset($camposTab);
		unset($campoFormulario);

		$form->definirFormularioArray($formArray);
		
		/**
		* PARTE 3: Processamento do formulário (se foi enviado)
		* 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		*/
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
			// Form sem erros
			} else if ($formCodigo == 2) {
				// Update
				require_once('bibliotecas/classes/CAD058.php');
                $CAD058 = new CAD058();
                $retorno = $CAD058->atualizarConfGlobal($form, $g['empresaAtual']);
				if ($retorno == true) {
					echo gGeraAlertMsg('Operação OK', 'Configurações salvas com sucesso!');
				}
			}
		} else {
			// Carrega os valores caso seja um update
			$sql = mysql_query("SELECT * FROM C031 WHERE C031_C004_Id = '{$g['empresaAtual']}'");

			while($row = mysql_fetch_array($sql)) {
				$form->campoSetarValor($row['C031_Campo'], $row['C031_Valor']);
			}
		}

		/**
		* PARTE 4: Gerar o formulário
		*/
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

		/**
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');

		echo  $gerado['pagina'];

		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		
		echo $g['smarty']->fetch('formEditor.tpl');
	break;

	case 'cad058FormConfECommerce':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;

		/**
		* PARTE 2: Definição do formulário
		*/
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = false;
		if ($formMultiTab) {
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}

		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = '0000';
		$form_campo_pk = '0000_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad058FormConfECommerce_Btn';
		// Form que será executado ao clicar no
		$enviar_acaoHref = '/cad/cad058/form/cad058FormConfECommerce/';
		// formEditor
		// Form Editor
		$form_editor = array(
			'geral' => '{"urlFrameworkNovo":{"top":99,"left":167.34375,"width":168,"hidden":0},"plataformaECommerce":{"top":7,"left":37.5,"width":176,"hidden":0},"plataformaECommerce2":{"top":53,"left":35.703125,"width":176,"hidden":0}}',
		);

		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('cad058FormConfMobile');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'plataformaECommerce' => array('name' => 'plataformaECommerce', 'label' => 'Escolher a primeira plataforma de e-commerce', 'type' => 'select', 'select' =>
						array(
							array('title' => '...', 'value' => ''),
							array('title' => '1 - Plugg-to', 'value' => 'pluggto'),
							array('title' => '2 - Tray', 'value' => 'tray'),
							array('title' => '3 - Magento 1.x Soap', 'value' => 'magentoSoap'),
							array('title' => '4 - Magento 1.x Oauth', 'value' => 'magentoOauth'),
							array('title' => '5 - Magento 2.x', 'value' => 'magento2'),							
							array('title' => '6 - Webstorm', 'value' => 'webstorm'),
							array('title' => '7 - NuvemShop', 'value' => 'nuvemshop'),												
						)
					),
					'plataformaECommerce2' => array('name' => 'plataformaECommerce2', 'label' => 'Escolher a segunda plataforma de e-commerce', 'type' => 'select', 'select' =>
						array(
							array('title' => '...', 'value' => ''),
							array('title' => '1 - Plugg-to', 'value' => 'pluggto'),
							array('title' => '2 - Tray', 'value' => 'tray'),
							array('title' => '3 - Magento 1.x Soap', 'value' => 'magentoSoap'),
							array('title' => '4 - Magento 1.x Oauth', 'value' => 'magentoOauth'),
							array('title' => '5 - Magento 2.x', 'value' => 'magento2'),							
							array('title' => '6 - Webstorm', 'value' => 'webstorm'),
							array('title' => '7 - NuvemShop', 'value' => 'nuvemshop'),	
						)
					),
					'urlFrameworkNovo' => array('name' => 'urlFrameworkNovo', 'label' => 'Url Framework Novo', 'type' => 'text', 'uppercase' => false)
				),
			)
		);

		$arrayObservacoes = array();
		$arrayDiff = array();
		$sqlC031 = mysql_query("SELECT C031_Campo, C031_Observacao, C031_Valor, C031_Valor_Padrao FROM C031 GROUP BY C031_Campo");
		while($rowC031 = mysql_fetch_array($sqlC031)){
			$arrayObservacoes[$rowC031['C031_Campo']] = $rowC031['C031_Observacao'];
			if($rowC031['C031_Valor'] != $rowC031['C031_Valor_Padrao']){
				$arrayDiff[] = $rowC031['C031_Campo'];
			}
		}
		foreach ($formArray['campos'] as $keyTab => &$camposTab) {
			foreach ($camposTab as $keyCampo => &$campoFormulario) {
				$campoFormulario['extra'] = 'title="'.$arrayObservacoes[$keyCampo].'"';
				if(in_array($keyCampo, $arrayDiff)){
					if(empty($campoFormulario['label'])) { continue; }
					$campoFormulario['label'] = "<font color=\"blue\">".$campoFormulario['label']."</font>";
				}
			}
		}
		unset($camposTab);
		unset($campoFormulario);
		$form->definirFormularioArray($formArray);

		/**
		* PARTE 3: Processamento do formulário (se foi enviado)
		* 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		*/
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
			// Form sem erros
			} else if ($formCodigo == 2) {
				// Update
				require_once('bibliotecas/classes/CAD058.php');
                $CAD058 = new CAD058();
                $retorno = $CAD058->atualizarConfGlobal($form, $g['empresaAtual']);
				if ($retorno == true) {
					echo gGeraAlertMsg('Operação OK', 'Configurações salvas com sucesso!');
				}
			}
		} else {
			// Carrega os valores caso seja um update
			$sql = mysql_query("SELECT * FROM C031 WHERE C031_C004_Id = '{$g['empresaAtual']}'");
			while($row = mysql_fetch_array($sql)){
				$form->campoSetarValor($row['C031_Campo'], $row['C031_Valor']);
			}
		}

		/**
		* PARTE 4: Gerar o formulário
		*/
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

		/**
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo  $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		echo $g['smarty']->fetch('formEditor.tpl');
	break;

	case 'cad058FormConfECommerceMagento1Oauth':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;

		/**
		* PARTE 2: Definição do formulário
		*/
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = false;
		if ($formMultiTab) {
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}

		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = '0000';
		$form_campo_pk = '0000_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad058FormConfECommerceMagento1Oauth_Btn';
		// Form que será executado ao clicar no
		$enviar_acaoHref = '/cad/cad058/form/cad058FormConfECommerceMagento1Oauth/';
		// formEditor
		// Form Editor
		$form_editor = array(
			'geral' => '{"tituloURL":{"top":5,"left":158,"width":null,"hidden":1},"MagentoUrlCredencial":{"top":30,"left":49.421875,"width":440,"hidden":0},"MagentoUrlAutorizacao":{"top":53,"left":94.671875,"width":440,"hidden":0},"MagentoUrlApi":{"top":76,"left":136.859375,"width":440,"hidden":0},"MagentoUrlRequisicao":{"top":99,"left":72.65625,"width":440,"hidden":0},"MagentoConsumerKEy":{"top":122,"left":98.953125,"width":216,"hidden":0},"MagentoConsumerSecret":{"top":145,"left":86.109375,"width":216,"hidden":0},"tituloOr":{"top":190,"left":159,"width":null,"hidden":1},"MagentoStatusPedido":{"top":214,"left":56.734375,"width":216,"hidden":0},"IntervaloDeAtualizacao":{"top":237,"left":19.4375,"width":40,"hidden":0},"tituloES":{"top":278,"left":154,"width":null,"hidden":1},"HabilitarAtualizacaoEstoque":{"top":306,"left":22.515625,"width":96,"hidden":0},"IntervaloDeAtualizacaoEstoque":{"top":329,"left":26.765625,"width":40,"hidden":0}}',
		);

		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('cad058FormConfECommerceMagento1Oauth');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'tituloURL' => array('name' => 'tituloURL', 'label'=>'Configurações Url´s','type' => 'titulo', 'reject' => true),
					'MagentoUrlCredencial' => array('name' => 'MagentoUrlCredencial', 'label' => 'Url requisição credencial', 'type' => 'text','uppercase' => false),
					'MagentoUrlAutorizacao' => array('name' => 'MagentoUrlAutorizacao', 'label' => 'Url Autorização', 'type' => 'text','uppercase' => false),
					'MagentoUrlRequisicao' => array('name' => 'MagentoUrlRequisicao', 'label' => 'Url requisição token', 'type' => 'text','uppercase' => false),
					'MagentoUrlApi' => array('name' => 'MagentoUrlApi', 'label' => 'Url Api', 'type' => 'text','uppercase' => false),
					'MagentoConsumerKEy' => array('name' => 'MagentoConsumerKEy', 'label' => 'Consumer key', 'type' => 'text','uppercase' => false),
					'MagentoConsumerSecret' => array('name' => 'MagentoConsumerSecret', 'label' => 'Consumer secret', 'type' => 'text','uppercase' => false),
					'tituloOr' => array('name' => 'tituloOr', 'label'=>'Configurações Pedidos','type' => 'titulo', 'reject' => true),
					'MagentoStatusPedido' => array('name' => 'MagentoStatusPedido', 'label' => 'Status pedido magento', 'type' => 'text','uppercase' => false),	
					'IntervaloDeAtualizacao' => array('name' => 'IntervaloDeAtualizacao', 'label' => 'Intervalo de atualização orders', 'type' => 'text','uppercase' => false),
					'tituloES' => array('name' => 'tituloES', 'label'=>'Configurações Estoque','type' => 'titulo', 'reject' => true),
					'IntervaloDeAtualizacaoEstoque' => array('name' => 'IntervaloDeAtualizacaoEstoque', 'label' => 'Intervalo atualização estoque', 'type' => 'text','uppercase' => false),
					'HabilitarAtualizacaoEstoque' => array('name' => 'HabilitarAtualizacaoEstoque', 'label' => 'Habilitar atualização estoque?', 'type' => 'select', 'select' =>
						array(
							array('title' => '...', 'value' => ''),
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N'),
						)	
					),
				),
			)
		);

		$arrayObservacoes = array();
		$arrayDiff = array();
		$sqlC031 = mysql_query("SELECT C031_Campo, C031_Observacao, C031_Valor, C031_Valor_Padrao FROM C031 GROUP BY C031_Campo");
		while($rowC031 = mysql_fetch_array($sqlC031)){
			$arrayObservacoes[$rowC031['C031_Campo']] = $rowC031['C031_Observacao'];
			if($rowC031['C031_Valor'] != $rowC031['C031_Valor_Padrao']){
				$arrayDiff[] = $rowC031['C031_Campo'];
			}
		}
		foreach ($formArray['campos'] as $keyTab => &$camposTab) {
			foreach ($camposTab as $keyCampo => &$campoFormulario) {
				$campoFormulario['extra'] = 'title="'.$arrayObservacoes[$keyCampo].'"';
				if(in_array($keyCampo, $arrayDiff)){
					if(empty($campoFormulario['label'])) { continue; }
					$campoFormulario['label'] = "<font color=\"blue\">".$campoFormulario['label']."</font>";
				}
			}
		}
		unset($camposTab);
		unset($campoFormulario);
		$form->definirFormularioArray($formArray);

		/**
		* PARTE 3: Processamento do formulário (se foi enviado)
		* 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		*/
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
			// Form sem erros
			} else if ($formCodigo == 2) {
				// Update
				require_once('bibliotecas/classes/CAD058.php');
                $CAD058 = new CAD058();
                $retorno = $CAD058->atualizarConfGlobal($form, $g['empresaAtual']);
				if ($retorno == true) {
					echo gGeraAlertMsg('Operação OK', 'Configurações salvas com sucesso!');
				}
			}
		} else {
			// Carrega os valores caso seja um update
			$sql = mysql_query("SELECT * FROM C031 WHERE C031_C004_Id = '{$g['empresaAtual']}'");
			while($row = mysql_fetch_array($sql)){
				$form->campoSetarValor($row['C031_Campo'], $row['C031_Valor']);
			}
		}

		/**
		* PARTE 4: Gerar o formulário
		*/
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

		/**
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo  $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		echo $g['smarty']->fetch('formEditor.tpl');
	break;

	case 'cad058ContentConfECommerceTray':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;

		/**
		* PARTE 2: Definição do formulário
		*/
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = false;
		if ($formMultiTab) {
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}

		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = '0000';
		$form_campo_pk = '0000_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad058ContentConfECommerceTray_Btn';
		// Form que será executado ao clicar no
		$enviar_acaoHref = '/cad/cad058/form/cad058ContentConfECommerceTray/';
		// formEditor
		// Form Editor
		$form_editor = array(
			'geral' => '{"tituloURL":{"top":4,"left":248,"width":null,"hidden":1},"UrlApiTray":{"top":30,"left":218.34375,"width":376,"hidden":0},"ConsumerKeyTray":{"top":53,"left":181.015625,"width":376,"hidden":0},"ConsumerSecretTray":{"top":76,"left":167.15625,"width":376,"hidden":0},"tituloOr":{"top":119,"left":238,"width":null,"hidden":1},"StatusPedidoTray":{"top":145,"left":162.171875,"width":376,"hidden":0},"StatusPedidoImportadoTray":{"top":168,"left":120.0625,"width":383,"hidden":0},"IdD022PedidoTray":{"top":191,"left":138.390625,"width":383,"hidden":0},"IdD027PedidoTray":{"top":214,"left":170.84375,"width":383,"hidden":0},"IdStatusPedidoTrayAposImportacao":{"top":237,"left":40,"width":376,"hidden":0},"FlagTipoFretePedidoTray":{"top":260,"left":163.84375,"width":383,"hidden":0},"tituloProdutos":{"top":303,"left":242,"width":null,"hidden":1},"FlagBuscarProdPedidoCodBarrasTray":{"top":329,"left":111.90625,"width":111,"hidden":0},"atualizaPrecoTabelaAoImportarPedido":{"top":352,"left":8.046875,"width":111,"hidden":0},"integracaoProdutos":{"top":395,"left":236,"width":null,"hidden":1},"precoEnviadoPlataformaTray":{"top":421,"left":104.796875,"width":111,"hidden":0},"sincronizaGrupoESubGrupoTray":{"top":444,"left":121.21875,"width":111,"hidden":0},"sincronizaImagensProdutosTray":{"top":467,"left":133.0625,"width":111,"hidden":0}}',
		);

		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('cad058ContentConfECommerceTray');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'tituloURL' => array('name' => 'tituloURL', 'label'=>'Configurações Url´s','type' => 'titulo', 'reject' => true),
					'UrlApiTray' => array('name' => 'UrlApiTray', 'label' => 'Url Api', 'type' => 'text','uppercase' => false),
					'ConsumerKeyTray' => array('name' => 'ConsumerKeyTray', 'label' => 'Consumer key', 'type' => 'text','uppercase' => false),
					'ConsumerSecretTray' => array('name' => 'ConsumerSecretTray', 'label' => 'Consumer secret', 'type' => 'text','uppercase' => false),
					'tituloOr' => array('name' => 'tituloOr', 'label'=>'Configurações Pedidos','type' => 'titulo', 'reject' => true),
					'tituloProdutos' => array('name' => 'tituloProdutos', 'label'=>'Configurações Produtos ao Importar Pedido','type' => 'titulo', 'reject' => true),
					'integracaoProdutos' => array('name' => 'integracaoProdutos', 'label'=>'Integração Produtos com a Plataforma','type' => 'titulo', 'reject' => true),
					'StatusPedidoTray' => array('name' => 'StatusPedidoTray', 'label' => 'Status pedido tray', 'type' => 'text','uppercase' => false),
					'StatusPedidoImportadoTray' => array('name' => 'StatusPedidoImportadoTray', 'label' => 'Status do pedido hardness', 'type' => 'select', 'select' =>
						array(
							array('title' => '...', 'value' => ''),
							array('title' => '0 - Pendente', 'value' => '0'),
							array('title' => '1 - Imprimir', 'value' => '1'),
							array('title' => '3 - Gerar nota fiscal', 'value' => '3'),
						)	
					),
					'IdStatusPedidoTrayAposImportacao' => array('name' => 'IdStatusPedidoTrayAposImportacao', 'label' => 'Id Status para pedido tray após importação', 'type' => 'text','uppercase' => false),
					'IdD027PedidoTray' => array('name' => 'IdD027PedidoTray', 'label' => 'Portador padrão', 'type' => 'select', 
						'select' => gGeraSelect('D027', 'D027_Id', 'D027_Portador','', true)
					),
					'IdD022PedidoTray' => array('name' => 'IdD022PedidoTray', 'label' => 'Transportadora padrão', 'type' => 'select', 
						'select' => gGeraSelect('D022', 'D022_Id', 'D022_Nome_Empresa','', true)
					),
					'FlagTipoFretePedidoTray' => array('name' => 'FlagTipoFretePedidoTray','label'=>'Tipo Frete Padrão', 'type' => 'select',
						'select' => array(
							array('title' => '0 - Contratação por conta do Remetente', 'value' => 0),
							array('title' => '1 - Contratação por conta do Destinatário', 'value' => 1),
							array('title' => '2 - Contratação por conta de Terceiros', 'value' => 2),
							array('title' => '3 - Próprio por conta do Remetente', 'value' => 3),
							array('title' => '4 - Próprio por conta do Destinatário', 'value' => 4),							
							array('title' => '9 - Sem frete', 'value' => 9),
						),
					),
					'FlagBuscarProdPedidoCodBarrasTray' => array('name' => 'FlagBuscarProdPedidoCodBarrasTray','label'=>'Procura Prod. Codigo Barras', 'type' => 'select',
						'select' => array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						),
					),
					'precoEnviadoPlataformaTray' => array('name' => 'precoEnviadoPlataformaTray', 'label' => 'Preço Prod. enviado para Tray', 'type' => 'select',
						'select' => array(
							array('title' => 'PREÇO 1', 'value' => '1'),
							array('title' => 'PREÇO LOJA', 'value' => '2')
						),
					),
					'atualizaPrecoTabelaAoImportarPedido' => array('name' => 'atualizaPrecoTabelaAoImportarPedido', 'label' => 'Atualiza Preço Tabela do Item ao Importar Pedido', 'type' => 'select',
						'select' => array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						),
					),
					'sincronizaGrupoESubGrupoTray' => array('name' => 'sincronizaGrupoESubGrupoTray', 'label' => 'Envia grupos e sub-grupos', 'type' => 'select',
						'select' => array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						),
					),
					'sincronizaImagensProdutosTray' => array('name' => 'sincronizaImagensProdutosTray', 'label' => 'Envia imagens produtos', 'type' => 'select',
						'select' => array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						),
					),
				),
			)
		);

		$arrayObservacoes = array();
		$arrayDiff = array();
		$sqlC031 = mysql_query("SELECT C031_Campo, C031_Observacao, C031_Valor, C031_Valor_Padrao FROM C031 GROUP BY C031_Campo");
		while($rowC031 = mysql_fetch_array($sqlC031)){
			$arrayObservacoes[$rowC031['C031_Campo']] = $rowC031['C031_Observacao'];
			if($rowC031['C031_Valor'] != $rowC031['C031_Valor_Padrao']){
				$arrayDiff[] = $rowC031['C031_Campo'];
			}
		}
		foreach ($formArray['campos'] as $keyTab => &$camposTab) {
			foreach ($camposTab as $keyCampo => &$campoFormulario) {
				$campoFormulario['extra'] = 'title="'.$arrayObservacoes[$keyCampo].'"';
				if(in_array($keyCampo, $arrayDiff)){
					if(empty($campoFormulario['label'])) { continue; }
					$campoFormulario['label'] = "<font color=\"blue\">".$campoFormulario['label']."</font>";
				}
			}
		}
		unset($camposTab);
		unset($campoFormulario);
		$form->definirFormularioArray($formArray);

		/**
		* PARTE 3: Processamento do formulário (se foi enviado)
		* 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		*/
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
			// Form sem erros
			} else if ($formCodigo == 2) {
				// Update
				require_once('bibliotecas/classes/CAD058.php');
                $CAD058 = new CAD058();
                $retorno = $CAD058->atualizarConfGlobal($form, $g['empresaAtual']);
				if ($retorno == true) {
					echo gGeraAlertMsg('Operação OK', 'Configurações salvas com sucesso!');
				}
			}
		} else {
			// Carrega os valores caso seja um update
			$sql = mysql_query("SELECT * FROM C031 WHERE C031_C004_Id = '{$g['empresaAtual']}'");
			while($row = mysql_fetch_array($sql)){
				$form->campoSetarValor($row['C031_Campo'], $row['C031_Valor']);
			}
		}

		/**
		* PARTE 4: Gerar o formulário
		*/
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

		/**
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo  $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		echo $g['smarty']->fetch('formEditor.tpl');
	break;

	case 'cad058ContentConfMagazord':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;

		/**
		* PARTE 2: Definição do formulário
		*/
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = false;
		if ($formMultiTab) {
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}

		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = '0000';
		$form_campo_pk = '0000_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad058ContentConfMagazord_Btn';
		// Form que será executado ao clicar no
		$enviar_acaoHref = '/cad/cad058/form/cad058ContentConfMagazord/';
		// formEditor
		// Form Editor
		$form_editor = array(
			'geral' => '{"tituloAPI":{"top":4,"left":248,"width":null,"hidden":1},"magazordAcessoURL":{"top":30,"left":218.34375,"width":376,"hidden":0},"magazordAcessoToken":{"top":53,"left":183.015625,"width":376,"hidden":0},"magazordAcessoKey":{"top":76,"left":194.96875,"width":376,"hidden":0},"tituloPedido":{"top":119,"left":238,"width":null,"hidden":1},"magazordPedidoStatusImportar":{"top":145,"left":35.796875,"width":376,"hidden":0},"magazordPedidoStatusImportado":{"top":168,"left":30.09375,"width":376,"hidden":0},"magazordPedidoStatusFaturado":{"top":191,"left":45,"width":376,"hidden":0},"magazordPedidoStatusERP":{"top":214,"left":93.453125,"width":383,"hidden":0},"magazordPedidoTransportadora":{"top":237,"left":138.390625,"width":383,"hidden":0},"magazordPedidoPortador":{"top":260,"left":170.84375,"width":383,"hidden":0},"magazordPedidoTipoFrete":{"top":283,"left":163.84375,"width":383,"hidden":0},"tituloProduto":{"top":326,"left":240,"width":null,"hidden":1},"magazordTabelaPreco":{"top":352,"left":69.328125,"width":105,"hidden":0},"magazordProdutoPreco":{"top":375,"left":75.03125,"width":111,"hidden":0},"magazordDeposito":{"top":398,"left":103.46875,"width":105,"hidden":0}}',
		);

		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('cad058ContentConfMagazord');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'tituloAPI' => array('name' => 'tituloAPI', 'label'=>'Configurações API','type' => 'titulo', 'reject' => true),
					'magazordAcessoURL' => array('name' => 'magazordAcessoURL', 'label' => 'Url Api', 'type' => 'text','uppercase' => false),
					'magazordAcessoToken' => array('name' => 'magazordAcessoToken', 'label' => 'Token acesso', 'type' => 'text','uppercase' => false),
					'magazordAcessoKey' => array('name' => 'magazordAcessoKey', 'label' => 'Key acesso', 'type' => 'text','uppercase' => false),

					'tituloPedido' => array('name' => 'tituloPedido', 'label'=>'Configurações Pedidos','type' => 'titulo', 'reject' => true),
					'magazordPedidoStatusImportar' => array('name' => 'magazordPedidoStatusImportar', 'label' => 'Status do pedido Magazord a ser importado', 'type' => 'text','uppercase' => false),
					'magazordPedidoStatusImportado' => array('name' => 'magazordPedidoStatusImportado', 'label' => 'Status do pedido Magazord após importação', 'type' => 'text','uppercase' => false),
					'magazordPedidoStatusFaturado' => array('name' => 'magazordPedidoStatusFaturado', 'label' => 'Status do pedido Magazord após faturado', 'type' => 'text','uppercase' => false),
					'magazordPedidoStatusERP' => array('name' => 'magazordPedidoStatusERP', 'label' => 'Status do pedido gerado no ERP', 'type' => 'select', 'select' =>
						array(
							array('title' => '...', 'value' => ''),
							array('title' => '0 - Pendente', 'value' => '0'),
							array('title' => '1 - Imprimir', 'value' => '1'),
							array('title' => '3 - Gerar nota fiscal', 'value' => '3'),
						)	
					),
					'magazordPedidoTransportadora' => array('name' => 'magazordPedidoTransportadora', 'label' => 'Transportadora padrão', 'type' => 'select', 
						'select' => gGeraSelect('D022', 'D022_Id', 'D022_Nome_Empresa','', true)
					),
					'magazordPedidoPortador' => array('name' => 'magazordPedidoPortador', 'label' => 'Portador padrão', 'type' => 'select', 
						'select' => gGeraSelect('D027', 'D027_Id', 'D027_Portador','', true)
					),
					'magazordPedidoTipoFrete' => array('name' => 'magazordPedidoTipoFrete','label'=>'Tipo Frete Padrão', 'type' => 'select',
						'select' => array(
							array('title' => '0 - Contratação por conta do Remetente', 'value' => 0),
							array('title' => '1 - Contratação por conta do Destinatário', 'value' => 1),
							array('title' => '2 - Contratação por conta de Terceiros', 'value' => 2),
							array('title' => '3 - Próprio por conta do Remetente', 'value' => 3),
							array('title' => '4 - Próprio por conta do Destinatário', 'value' => 4),
							array('title' => '9 - Sem frete', 'value' => 9),
						),
					),

					'tituloProduto' => array('name' => 'tituloProduto', 'label'=>'Integração Produtos com a Plataforma','type' => 'titulo', 'reject' => true),
					'magazordTabelaPreco' => array('name' => 'magazordTabelaPreco', 'label' => 'Tabela de preço padrão na Magazord', 'type' => 'text','uppercase' => false),
					'magazordProdutoPreco' => array('name' => 'magazordProdutoPreco', 'label' => 'Preço do produto utilizado na venda', 'type' => 'select',
						'select' => array(
							array('title' => 'PREÇO 1', 'value' => '1'),
							array('title' => 'PREÇO 2', 'value' => '2'),
							array('title' => 'PREÇO 3', 'value' => '3'),
							array('title' => 'PREÇO 4', 'value' => '4'),
							array('title' => 'PREÇO LOJA', 'value' => '5')
						),
					),
					'magazordDeposito' => array('name' => 'magazordDeposito', 'label' => 'Deposito padrão na Magazord', 'type' => 'text','uppercase' => false),
				),
			)
		);

		$arrayObservacoes = array();
		$arrayDiff = array();
		$sqlC031 = mysql_query("SELECT C031_Campo, C031_Observacao, C031_Valor, C031_Valor_Padrao FROM C031 GROUP BY C031_Campo");
		while($rowC031 = mysql_fetch_array($sqlC031)){
			$arrayObservacoes[$rowC031['C031_Campo']] = $rowC031['C031_Observacao'];
			if($rowC031['C031_Valor'] != $rowC031['C031_Valor_Padrao']){
				$arrayDiff[] = $rowC031['C031_Campo'];
			}
		}
		foreach ($formArray['campos'] as $keyTab => &$camposTab) {
			foreach ($camposTab as $keyCampo => &$campoFormulario) {
				$campoFormulario['extra'] = 'title="'.$arrayObservacoes[$keyCampo].'"';
				if(in_array($keyCampo, $arrayDiff)){
					if(empty($campoFormulario['label'])) { continue; }
					$campoFormulario['label'] = "<font color=\"blue\">".$campoFormulario['label']."</font>";
				}
			}
		}
		unset($camposTab);
		unset($campoFormulario);
		$form->definirFormularioArray($formArray);

		/**
		* PARTE 3: Processamento do formulário (se foi enviado)
		* 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		*/
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
			// Form sem erros
			} else if ($formCodigo == 2) {
				// Update
				require_once('bibliotecas/classes/CAD058.php');
                $CAD058 = new CAD058();
                $retorno = $CAD058->atualizarConfGlobal($form, $g['empresaAtual']);
				if ($retorno == true) {
					echo gGeraAlertMsg('Operação OK', 'Configurações salvas com sucesso!');
				}
			}
		} else {
			// Carrega os valores caso seja um update
			$sql = mysql_query("SELECT * FROM C031 WHERE C031_C004_Id = '{$g['empresaAtual']}'");
			while($row = mysql_fetch_array($sql)){
				$form->campoSetarValor($row['C031_Campo'], $row['C031_Valor']);
			}
		}

		/**
		* PARTE 4: Gerar o formulário
		*/
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

		/**
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo  $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		echo $g['smarty']->fetch('formEditor.tpl');
	break;

	case 'cad058FormConfMeli':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;

		/**
		* PARTE 2: Definição do formulário
		*/
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = false;
		if ($formMultiTab) {
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}

		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = '0000';
		$form_campo_pk = '0000_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad058FormConfMeli_Btn';
		// Form que será executado ao clicar no
		$enviar_acaoHref = '/cad/cad058/form/cad058FormConfMeli/';
		// formEditor
		// Form Editor
		$form_editor = array(
			'geral' => '{"tituloURL":{"top":4,"left":248.00000619888306,"width":null,"hidden":1},"clientIdMeli":{"top":145.00001525878906,"left":242.65000009536743,"width":376.4,"hidden":0},"clientSecretMeli":{"top":168.00001525878906,"left":220.66249704360962,"width":375.4,"hidden":0},"sellerIdMeli":{"top":30,"left":243.40000009536743,"width":72.4,"hidden":0},"UrlApiMeli":{"top":53,"left":250.2999939918518,"width":377.4,"hidden":0},"tokenMeli":{"top":76,"left":252.91251230239868,"width":376.4,"hidden":0},"refreshTokenMeli":{"top":99,"left":212.77500009536743,"width":376.4,"hidden":0}}',
		);

		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('cad058FormConfMeli');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'tituloURL' => array('name' => 'tituloURL', 'label'=>'Configurações Mercado Livre','type' => 'titulo', 'reject' => true),
					'UrlApiMeli' => array('name' => 'UrlApiMeli', 'label' => 'Url Api', 'type' => 'text','uppercase' => false),
					'sellerIdMeli' => array('name' => 'sellerIdMeli', 'label' => 'Seller Id', 'type' => 'text','uppercase' => false),
					'tokenMeli' => array('name' => 'tokenMeli', 'label' => 'Token', 'type' => 'text','uppercase' => false),					
					'refreshTokenMeli' => array('name' => 'refreshTokenMeli', 'label' => 'Refresh Token', 'type' => 'text','uppercase' => false),					
					'clientIdMeli' => array('name' => 'clientIdMeli', 'label' => 'Client Id', 'type' => 'text','uppercase' => false),					
					'clientSecretMeli' => array('name' => 'clientSecretMeli', 'label' => 'Client Secret', 'type' => 'text','uppercase' => false),					
					
				),
			)
		);

		$arrayObservacoes = array();
		$arrayDiff = array();
		$sqlC031 = mysql_query("SELECT C031_Campo, C031_Observacao, C031_Valor, C031_Valor_Padrao FROM C031 GROUP BY C031_Campo");
		while($rowC031 = mysql_fetch_array($sqlC031)){
			$arrayObservacoes[$rowC031['C031_Campo']] = $rowC031['C031_Observacao'];
			if($rowC031['C031_Valor'] != $rowC031['C031_Valor_Padrao']){
				$arrayDiff[] = $rowC031['C031_Campo'];
			}
		}
		foreach ($formArray['campos'] as $keyTab => &$camposTab) {
			foreach ($camposTab as $keyCampo => &$campoFormulario) {
				$campoFormulario['extra'] = 'title="'.$arrayObservacoes[$keyCampo].'"';
				if(in_array($keyCampo, $arrayDiff)){
					if(empty($campoFormulario['label'])) { continue; }
					$campoFormulario['label'] = "<font color=\"blue\">".$campoFormulario['label']."</font>";
				}
			}
		}
		unset($camposTab);
		unset($campoFormulario);
		$form->definirFormularioArray($formArray);

		/**
		* PARTE 3: Processamento do formulário (se foi enviado)
		* 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		*/
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
			// Form sem erros
			} else if ($formCodigo == 2) {
				// Update
				require_once('bibliotecas/classes/CAD058.php');
                $CAD058 = new CAD058();
                $retorno = $CAD058->atualizarConfGlobal($form, $g['empresaAtual']);
				if ($retorno == true) {
					echo gGeraAlertMsg('Operação OK', 'Configurações salvas com sucesso!');
				}
			}
		} else {
			// Carrega os valores caso seja um update
			$sql = mysql_query("SELECT * FROM C031 WHERE C031_C004_Id = '{$g['empresaAtual']}'");
			while($row = mysql_fetch_array($sql)){
				$form->campoSetarValor($row['C031_Campo'], $row['C031_Valor']);
			}
		}

		/**
		* PARTE 4: Gerar o formulário
		*/
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

		/**
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo  $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		echo $g['smarty']->fetch('formEditor.tpl');
	break;

	case 'cad058FormConfOpenIA':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;

		/**
		* PARTE 2: Definição do formulário
		*/
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = false;
		if ($formMultiTab) {
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}

		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = '0000';
		$form_campo_pk = '0000_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad058FormConfOpenIA_Btn';
		// Form que será executado ao clicar no
		$enviar_acaoHref = '/cad/cad058/form/cad058FormConfOpenIA/';
		// formEditor
		// Form Editor
		$form_editor = array(
			'geral' => '{"openAIApiKey":{"top":29.993865966796875,"left":19.163604736328125,"width":760.0392159999999,"hidden":0}}',
		);

		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('cad058FormConfOpenIA');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'openAIApiKey' => array('name' => 'openAIApiKey', 'label' => 'Chave da API OpenAI', 'type' => 'password','uppercase' => false),
				),
			)
		);

		$arrayObservacoes = array();
		$arrayDiff = array();
		$sqlC031 = mysql_query("SELECT C031_Campo, C031_Observacao, C031_Valor, C031_Valor_Padrao FROM C031 GROUP BY C031_Campo");
		while($rowC031 = mysql_fetch_array($sqlC031)){
			$arrayObservacoes[$rowC031['C031_Campo']] = $rowC031['C031_Observacao'];
			if($rowC031['C031_Valor'] != $rowC031['C031_Valor_Padrao']){
				$arrayDiff[] = $rowC031['C031_Campo'];
			}
		}
		foreach ($formArray['campos'] as $keyTab => &$camposTab) {
			foreach ($camposTab as $keyCampo => &$campoFormulario) {
				$campoFormulario['extra'] = 'title="'.$arrayObservacoes[$keyCampo].'"';
				if(in_array($keyCampo, $arrayDiff)){
					if(empty($campoFormulario['label'])) { continue; }
					$campoFormulario['label'] = "<font color=\"blue\">".$campoFormulario['label']."</font>";
				}
			}
		}
		unset($camposTab);
		unset($campoFormulario);
		$form->definirFormularioArray($formArray);

		/**
		* PARTE 3: Processamento do formulário (se foi enviado)
		* 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		*/
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
			// Form sem erros
			} else if ($formCodigo == 2) {
				// Update
				require_once('bibliotecas/classes/CAD058.php');
                $CAD058 = new CAD058();
                $retorno = $CAD058->atualizarConfGlobal($form, $g['empresaAtual']);
				if ($retorno == true) {
					echo gGeraAlertMsg('Operação OK', 'Configurações salvas com sucesso!');
				}
			}
		} else {
			// Carrega os valores caso seja um update
			$sql = mysql_query("SELECT * FROM C031 WHERE C031_C004_Id = '{$g['empresaAtual']}'");
			while($row = mysql_fetch_array($sql)){
				$form->campoSetarValor($row['C031_Campo'], $row['C031_Valor']);
			}
		}

		/**
		* PARTE 4: Gerar o formulário
		*/
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

		/**
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo  $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		echo $g['smarty']->fetch('formEditor.tpl');
	break;

	case 'cad058FormConfNuvemShop':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;

		/**
		* PARTE 2: Definição do formulário
		*/
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = false;
		if ($formMultiTab) {
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}

		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = '0000';
		$form_campo_pk = '0000_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad058FormConfNuvemShop_Btn';
		// Form que será executado ao clicar no
		$enviar_acaoHref = '/cad/cad058/form/cad058FormConfNuvemShop/';
		// formEditor
		// Form Editor
		$form_editor = array(
			'geral' => '{"tituloURL":{"top":4,"left":248,"width":null,"hidden":1},"UrlApiNuvemShop":{"top":30,"left":218.34375,"width":376,"hidden":0},"UserAgentNuvemShop":{"top":53,"left":181.015625,"width":50,"hidden":0},"AccessTokenNuvemShop":{"top":76,"left":167.15625,"width":376,"hidden":0}}',
		);

		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('cad058FormConfNuvemShop');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'tituloURL' => array('name' => 'tituloURL', 'label'=>'Configurações Url´s','type' => 'titulo', 'reject' => true),
					'UrlApiNuvemShop' => array('name' => 'UrlApiNuvemShop', 'label' => 'Url Api', 'type' => 'text','uppercase' => false),
					'UserAgentNuvemShop' => array('name' => 'UserAgentNuvemShop', 'label' => 'User Agent', 'type' => 'text','uppercase' => false),
					'AccessTokenNuvemShop' => array('name' => 'AccessTokenNuvemShop', 'label' => 'Access Token', 'type' => 'text','uppercase' => false),					
					'precoEnviadoNuvemshop' => array('name' => 'precoEnviadoNuvemshop', 'label' => 'Preço Enviado', 'type' => 'select',
						'select' => array(
							array('title' => 'PREÇO 1', 'value' => '1'),
							array('title' => 'PREÇO 2', 'value' => '2'),
							array('title' => 'PREÇO 3', 'value' => '3'),
							array('title' => 'PREÇO 4', 'value' => '4'),
							array('title' => 'PREÇO LOJA', 'value' => '5')
						),
					),
				),
			)
		);

		$arrayObservacoes = array();
		$arrayDiff = array();
		$sqlC031 = mysql_query("SELECT C031_Campo, C031_Observacao, C031_Valor, C031_Valor_Padrao FROM C031 GROUP BY C031_Campo");
		while($rowC031 = mysql_fetch_array($sqlC031)){
			$arrayObservacoes[$rowC031['C031_Campo']] = $rowC031['C031_Observacao'];
			if($rowC031['C031_Valor'] != $rowC031['C031_Valor_Padrao']){
				$arrayDiff[] = $rowC031['C031_Campo'];
			}
		}
		foreach ($formArray['campos'] as $keyTab => &$camposTab) {
			foreach ($camposTab as $keyCampo => &$campoFormulario) {
				$campoFormulario['extra'] = 'title="'.$arrayObservacoes[$keyCampo].'"';
				if(in_array($keyCampo, $arrayDiff)){
					if(empty($campoFormulario['label'])) { continue; }
					$campoFormulario['label'] = "<font color=\"blue\">".$campoFormulario['label']."</font>";
				}
			}
		}
		unset($camposTab);
		unset($campoFormulario);
		$form->definirFormularioArray($formArray);

		/**
		* PARTE 3: Processamento do formulário (se foi enviado)
		* 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		*/
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
			// Form sem erros
			} else if ($formCodigo == 2) {
				// Update
				require_once('bibliotecas/classes/CAD058.php');
                $CAD058 = new CAD058();
                $retorno = $CAD058->atualizarConfGlobal($form, $g['empresaAtual']);
				if ($retorno == true) {
					echo gGeraAlertMsg('Operação OK', 'Configurações salvas com sucesso!');
				}
			}
		} else {
			// Carrega os valores caso seja um update
			$sql = mysql_query("SELECT * FROM C031 WHERE C031_C004_Id = '{$g['empresaAtual']}'");
			while($row = mysql_fetch_array($sql)){
				$form->campoSetarValor($row['C031_Campo'], $row['C031_Valor']);
			}
		}

		/**
		* PARTE 4: Gerar o formulário
		*/
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

		/**
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo  $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		echo $g['smarty']->fetch('formEditor.tpl');
	break;

	case 'cad058ContentConfECommercePluggto':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;

		/**
		* PARTE 2: Definição do formulário
		*/
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = false;
		if ($formMultiTab) {
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}

		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = '0000';
		$form_campo_pk = '0000_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad058ContentConfECommercePluggto_Btn';
		// Form que será executado ao clicar no
		$enviar_acaoHref = '/cad/cad058/form/cad058ContentConfECommercePluggto/';
		// formEditor
		// Form Editor
		$form_editor = array(
			'geral' => '{"tituloURL":{"top":3,"left":161,"width":null,"hidden":0},"tituloOr":{"top":164,"left":157,"width":null,"hidden":0},"UrlApiPluggto":{"top":30,"left":122,"width":360,"hidden":0},"ClientSecretPluggto":{"top":53,"left":93,"width":360,"hidden":0},"ClientIdPluggto":{"top":76,"left":117,"width":360,"hidden":0},"UsernamePluggto":{"top":99,"left":105,"width":360,"hidden":0},"PasswordPluggto":{"top":122,"left":104,"width":360,"hidden":0},"StatusPedidoImportadoTray":{"top":191,"left":24,"width":208,"hidden":0}}',
		);

		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('cad058ContentConfECommercePluggto');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'tituloURL' => array('name' => 'tituloURL', 'label'=>'Configurações Url´s','type' => 'titulo', 'reject' => true),
					'UrlApiPluggto' => array('name' => 'UrlApiPluggto', 'label' => 'Url Api', 'type' => 'text','uppercase' => false),
					'ClientSecretPluggto' => array('name' => 'ClientSecretPluggto', 'label' => 'Client Secret', 'type' => 'text','uppercase' => false),
					'ClientIdPluggto' => array('name' => 'ClientIdPluggto', 'label' => 'Client Id', 'type' => 'text','uppercase' => false),
					'UsernamePluggto' => array('name' => 'UsernamePluggto', 'label' => 'Username', 'type' => 'text','uppercase' => false),
					'PasswordPluggto' => array('name' => 'PasswordPluggto', 'label' => 'Password', 'type' => 'text','uppercase' => false),
					'tituloOr' => array('name' => 'tituloOr', 'label'=>'Configurações Pedidos','type' => 'titulo', 'reject' => true),
					'StatusPedidoImportadoTray' => array('name' => 'StatusPedidoImportadoTray', 'label' => 'Status do pedido hardness', 'type' => 'select', 'select' =>
						array(
							array('title' => '...', 'value' => ''),
							array('title' => '0 - Pendente', 'value' => '0'),
							array('title' => '1 - Imprimir', 'value' => '1'),
							array('title' => '3 - Gerar nota fiscal', 'value' => '3'),
						)	
					),				
				),
			)
		);

		$arrayObservacoes = array();
		$arrayDiff = array();
		$sqlC031 = mysql_query("SELECT C031_Campo, C031_Observacao, C031_Valor, C031_Valor_Padrao FROM C031 GROUP BY C031_Campo");
		while($rowC031 = mysql_fetch_array($sqlC031)){
			$arrayObservacoes[$rowC031['C031_Campo']] = $rowC031['C031_Observacao'];
			if($rowC031['C031_Valor'] != $rowC031['C031_Valor_Padrao']){
				$arrayDiff[] = $rowC031['C031_Campo'];
			}
		}
		foreach ($formArray['campos'] as $keyTab => &$camposTab) {
			foreach ($camposTab as $keyCampo => &$campoFormulario) {
				$campoFormulario['extra'] = 'title="'.$arrayObservacoes[$keyCampo].'"';
				if(in_array($keyCampo, $arrayDiff)){
					if(empty($campoFormulario['label'])) { continue; }
					$campoFormulario['label'] = "<font color=\"blue\">".$campoFormulario['label']."</font>";
				}
			}
		}
		unset($camposTab);
		unset($campoFormulario);
		$form->definirFormularioArray($formArray);

		/**
		* PARTE 3: Processamento do formulário (se foi enviado)
		* 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		*/
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
			// Form sem erros
			} else if ($formCodigo == 2) {
				// Update
				require_once('bibliotecas/classes/CAD058.php');
                $CAD058 = new CAD058();
                $retorno = $CAD058->atualizarConfGlobal($form, $g['empresaAtual']);
				if ($retorno == true) {
					echo gGeraAlertMsg('Operação OK', 'Configurações salvas com sucesso!');
				}
			}
		} else {
			// Carrega os valores caso seja um update
			$sql = mysql_query("SELECT * FROM C031 WHERE C031_C004_Id = '{$g['empresaAtual']}'");
			while($row = mysql_fetch_array($sql)){
				$form->campoSetarValor($row['C031_Campo'], $row['C031_Valor']);
			}
		}

		/**
		* PARTE 4: Gerar o formulário
		*/
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

		/**
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo  $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		echo $g['smarty']->fetch('formEditor.tpl');
	break;

	case 'cad058FormConfECommerceMagento1Soap':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;

		/**
		* PARTE 2: Definição do formulário
		*/
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = false;
		if ($formMultiTab) {
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}

		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = '0000';
		$form_campo_pk = '0000_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad058FormConfECommerceMagento1Soap_Btn';
		// Form que será executado ao clicar no
		$enviar_acaoHref = '/cad/cad058/form/cad058FormConfECommerceMagento1Soap/';
		// formEditor
		// Form Editor
		$form_editor = array(
			'geral' => '{"tituloURL":{"top":5,"left":210,"width":null,"hidden":1},"MagentoUrlApi":{"top":30,"left":169.4749755859375,"width":504,"hidden":0},"HabilitarAtualizacaoEstoque":{"top":30,"left":838.5124816894531,"width":96,"hidden":1},"MagentoConsumerKEy":{"top":53,"left":151.13748168945312,"width":216,"hidden":0},"MagentoConsumerSecret":{"top":53,"left":441.5749816894531,"width":216,"hidden":0},"IntervaloDeAtualizacaoEstoque":{"top":53,"left":874.7624816894531,"width":40,"hidden":1},"tituloOr":{"top":75,"left":206,"width":null,"hidden":1},"MagentoStatusPedido":{"top":98.99998474121094,"left":88.75,"width":168,"hidden":0},"StatusPedidoImportado":{"top":98.99998474121094,"left":407.0124816894531,"width":176,"hidden":0},"C007IdEcommerce":{"top":121.99998474121094,"left":117.4749755859375,"width":175,"hidden":0},"tituloMagento":{"top":142.99998474121094,"left":202,"width":null,"hidden":1},"MagentoStatusPedidoEnviado":{"top":167.99998474121094,"left":88.75,"width":296,"hidden":0},"MandarNotificacaoCliente":{"top":167.99998474121094,"left":568.5624694824219,"width":64,"hidden":0},"CampoComentarioIntegrador":{"top":190.99998474121094,"left":92.4375,"height":62,"width":504,"hidden":0},"CampoComentarioCliente":{"top":259.99998474121094,"left":110.1624755859375,"height":62,"width":504,"hidden":0},"MagentoCodigoCarrier":{"top":328.99998474121094,"left":113.2249755859375,"width":264,"hidden":0},"EnviarMagentoColetar":{"top":328.99998474121094,"left":528.1874694824219,"width":64,"hidden":0},"MagentoIntegrador":{"top":352.00001525878906,"left":151.7249755859375,"width":272,"hidden":0},"filtroImportarDadosComHashtag":{"top":375.00001525878906,"left":68,"height":108,"width":504,"hidden":0}}',
		);

		$sql = "SELECT T003_C007_Id_Vendedor_Interno FROM T003 WHERE T003_Id = '{$acaoId}'";
		$idVendedorInterno=$aD024['T003_C007_Id_Vendedor_Interno'];

		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('cad058FormConfECommerceMagento1Soap');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'tituloURL' => array('name' => 'tituloURL', 'label'=>'Configurações Url´s','type' => 'titulo', 'reject' => true),
					'MagentoUrlApi' => array('name' => 'MagentoUrlApi', 'label' => 'Url Api', 'type' => 'text','uppercase' => false),
					'MagentoConsumerKEy' => array('\pname' => 'MagentoConsumerKEy', 'label' => 'Username', 'type' => 'text','uppercase' => false),
					'MagentoConsumerSecret' => array('name' => 'MagentoConsumerSecret', 'label' => 'Password', 'type' => 'text','uppercase' => false),
					'tituloOr' => array('name' => 'tituloOr', 'label'=>'Importação Pedidos','type' => 'titulo', 'reject' => true),
					'MagentoStatusPedido' => array('name' => 'MagentoStatusPedido', 'label' => 'Status pedido magento', 'type' => 'text','uppercase' => false),	
					// 'tituloES' => array('name' => 'tituloES', 'label'=>'Configurações Estoque','type' => 'titulo', 'reject' => true),
					'IntervaloDeAtualizacaoEstoque' => array('name' => 'IntervaloDeAtualizacaoEstoque', 'label' => 'Intervalo atualização estoque', 'type' => 'text','uppercase' => false),
					'HabilitarAtualizacaoEstoque' => array('name' => 'HabilitarAtualizacaoEstoque', 'label' => 'Habilitar atualização estoque?', 'type' => 'select', 'select' =>
						array(
							array('title' => '...', 'value' => ''),
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N'),
						)	
					),
					'StatusPedidoImportado' => array('name' => 'StatusPedidoImportado', 'label' => 'Status do pedido hardness', 'type' => 'select', 'select' =>
						array(
							array('title' => '...', 'value' => ''),
							array('title' => '0 - Pendente', 'value' => '0'),
							array('title' => '1 - Imprimir', 'value' => '1'),
							array('title' => '3 - Gerar nota fiscal', 'value' => '3'),
						)	
					),
					'C007IdEcommerce' => array('name' => 'C007IdEcommerce', 'label'=>'Vendedor Interno','type' => 'select',  'select' => 
						gGeraSelect('C007','C007_Id','C007_Primeiro_Nome','WHERE C007_Ativo != "N" ORDER BY C007_Primeiro_Nome',true,$g['usuarioAtual']),
					),
					'tituloMagento' => array('name' => 'tituloMageto', 'label'=>'Dados do faturamento','type' => 'titulo', 'reject' => true),
					'MagentoStatusPedidoEnviado' => array('name' => 'MagentoStatusPedidoEnviado', 'label' => 'Status pedido magento', 'type' => 'text','uppercase' => false),
					'CampoComentarioIntegrador' => array('name' => 'CampoComentarioIntegrador','uppercase' => false, 'label'=>'Comentário integrador','type' => 'textarea', 'length' => array(60,30)),
					'CampoComentarioCliente' => array('name' => 'CampoComentarioCliente', 'label'=>'Comentário cliente','type' => 'textarea','uppercase' => false, 'length' => array(60,30)),
					'MandarNotificacaoCliente' => array('name' => 'MandarNotificacaoCliente', 'label' => 'Notificar cliente?', 'type' => 'select', 'select' =>
						array(
							array('title' => '...', 'value' => ''),
							array('title' => 'Sim', 'value' => '1'),
							array('title' => 'Não', 'value' => '0'),
						)	
					),
					'MagentoCodigoCarrier' => array('name' => 'MagentoCodigoCarrier', 'label' => 'Forma de Entrega', 'type' => 'text','uppercase' => false),
					// 'AtualizarAoGerarANota' => array('name' => 'AtualizarAoGerarANota', 'label' => 'Enviar automaticamente ao gerar nota?', 'type' => 'select', 'select' =>
					// 	array(
					// 		array('title' => '...', 'value' => ''),
					// 		array('title' => 'Sim', 'value' => 'S'),
					// 		array('title' => 'Não', 'value' => 'N'),
					// 	)	
					// ),
					'MagentoIntegrador' => array('name' => 'MagentoIntegrador', 'label' => 'Integrador', 'type' => 'select', 'select' =>
						array(
							array('title' => '...', 'value' => ''),
							array('title' => 'Anymarket', 'value' => 'A'),
							array('title' => 'Integracommerce', 'value' => 'I'),
						)	
					),
					'EnviarMagentoColetar' => array('name' => 'EnviarMagentoColetar', 'label' => 'Enviar dados ao coletar?', 'type' => 'select', 'select' =>
						array(
							array('title' => '...', 'value' => ''),
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N'),
						)	
					),
					// Desativado para realizar modificação do MagentoSoap(importar pedido para todas as empresas)
					//'filtroImportarPedidoMagento' => array('name' => 'filtroImportarPedidoMagento', 'label'=>'Filtro importar pedido','type' => 'textarea','uppercase' => false, 'length' => array(60,30)),
					'filtroImportarDadosComHashtag' => array('name' => 'filtroImportarDadosComHashtag', 'label'=>'Filtro importar dados com #','type' => 'textarea','uppercase' => false, 'length' => array(60,30)),
				),
			)
		);

		$arrayObservacoes = array();
		$arrayDiff = array();
		$sqlC031 = mysql_query("SELECT C031_Campo, C031_Observacao, C031_Valor, C031_Valor_Padrao FROM C031 GROUP BY C031_Campo");
		while($rowC031 = mysql_fetch_array($sqlC031)){
			$arrayObservacoes[$rowC031['C031_Campo']] = $rowC031['C031_Observacao'];
			if($rowC031['C031_Valor'] != $rowC031['C031_Valor_Padrao']){
				$arrayDiff[] = $rowC031['C031_Campo'];
			}
		}
		foreach ($formArray['campos'] as $keyTab => &$camposTab) {
			foreach ($camposTab as $keyCampo => &$campoFormulario) {
				$campoFormulario['extra'] = 'title="'.$arrayObservacoes[$keyCampo].'"';
				if(in_array($keyCampo, $arrayDiff)){
					if(empty($campoFormulario['label'])) { continue; }
					$campoFormulario['label'] = "<font color=\"blue\">".$campoFormulario['label']."</font>";
				}
			}
		}
		unset($camposTab);
		unset($campoFormulario);
		$form->definirFormularioArray($formArray);

		/**
		* PARTE 3: Processamento do formulário (se foi enviado)
		* 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		*/
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
			// Form sem erros
			} else if ($formCodigo == 2) {
				// Update
				require_once('bibliotecas/classes/CAD058.php');
                $CAD058 = new CAD058();
                $retorno = $CAD058->atualizarConfGlobal($form, $g['empresaAtual']);
				if ($retorno == true) {
					echo gGeraAlertMsg('Operação OK', 'Configurações salvas com sucesso!');
				}
			}
		} else {
			// Carrega os valores caso seja um update
			$sql = mysql_query("SELECT * FROM C031 WHERE C031_C004_Id = '{$g['empresaAtual']}'");
			while($row = mysql_fetch_array($sql)){
				$form->campoSetarValor($row['C031_Campo'], $row['C031_Valor']);
			}
		}

		/**
		* PARTE 4: Gerar o formulário
		*/
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

		/**
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo  $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		echo $g['smarty']->fetch('formEditor.tpl');
	break;

	case 'cad058FormConfTrello':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;

		/**
		* PARTE 2: Definição do formulário
		*/
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = false;
		if ($formMultiTab) {
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}

		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = '0000';
		$form_campo_pk = '0000_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad058FormConfTrello_Btn';
		// Form que será executado ao clicar no
		$enviar_acaoHref = '/cad/cad058/form/cad058FormConfTrello/';
		// formEditor
		// Form Editor
		$form_editor = array(
			'geral' => '{"tituloConf":{"top":9,"left":240,"width":null,"hidden":1},"TrelloNomeTimeAnalise":{"top":30,"left":193.828125,"width":312,"hidden":0},"TrelloUrlApi":{"top":53,"left":186.078125,"width":312,"hidden":0},"TrelloListaCriarCard":{"top":76,"left":136.5625,"width":319,"hidden":0},"TrelloListaCardVoltar":{"top":99,"left":131.046875,"width":319,"hidden":0},"TrelloListaCardMelhoria":{"top":122,"left":91.921875,"width":319,"hidden":1},"C007PadraoRetornoCliente":{"top":145,"left":41.78125,"width":319,"hidden":1},"TrelloNomeTimeDesenvolvimento":{"top":168,"left":108.234375,"width":312,"hidden":1}}',
		);

		$sql = "SELECT T003_C007_Id_Vendedor_Interno FROM T003 WHERE T003_Id = '{$acaoId}'";
		$idVendedorInterno=$aD024['T003_C007_Id_Vendedor_Interno'];

		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('cad058FormConfTrello');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'tituloConf' => array('name' => 'tituloURL', 'label'=>'Configurações','type' => 'titulo', 'reject' => true),
					'TrelloNomeTimeDesenvolvimento' => array('name' => 'TrelloNomeTimeDesenvolvimento', 'label' => 'Time desenvolvimento', 'type' => 'text', 'uppercase' => false),
					'TrelloNomeTimeAnalise' => array('name' => 'TrelloNomeTimeAnalise', 'label' => 'Time', 'type' => 'text', 'uppercase' => false),
					'TrelloUrlApi' => array('name' => 'TrelloUrlApi', 'label' => 'Url api', 'type' => 'text', 'uppercase' => false),
					'C007PadraoRetornoCliente' => array('name' => 'C007PadraoRetornoCliente', 'label'=>'Usuário padrão resposta #CLIENTE','type' => 'select',  'select' => 
						gGeraSelect('C007','C007_Id','C007_Nome','WHERE C007_Ativo = "S"',true,$g['usuarioAtual']),
					),
					'TrelloListaCriarCard' => array('name' => 'TrelloListaCriarCard', 'label'=>'Lista criar cartão','type' => 'select',  'select' => 
						gGeraSelect('D167','D167_Id','D166_Grupo_Suporte, D167_Nome_Lista','LEFT JOIN D166 ON D167_D166_Id=D166_Id ORDER BY D166_Grupo_Suporte',true,$g['usuarioAtual']),
					),
					'TrelloListaCardVoltar' => array('name' => 'TrelloListaCardVoltar', 'label'=>'Lista voltar cartão','type' => 'select',  'select' => 
						gGeraSelect('D167','D167_Id','D166_Grupo_Suporte, D167_Nome_Lista','LEFT JOIN D166 ON D167_D166_Id=D166_Id ORDER BY D166_Grupo_Suporte',true,$g['usuarioAtual']),
					),
					'TrelloListaCardMelhoria' => array('name' => 'TrelloListaCardMelhoria', 'label'=>'Lista redireciona melhoria','type' => 'select',  'select' => 
						gGeraSelect('D167','D167_Id','D166_Grupo_Suporte, D167_Nome_Lista','LEFT JOIN D166 ON D167_D166_Id=D166_Id ORDER BY D166_Grupo_Suporte',true,$g['usuarioAtual']),
					),
				),
			)
		);

		$arrayObservacoes = array();
		$arrayDiff = array();
		$sqlC031 = mysql_query("SELECT C031_Campo, C031_Observacao, C031_Valor, C031_Valor_Padrao FROM C031 GROUP BY C031_Campo");
		while($rowC031 = mysql_fetch_array($sqlC031)){
			$arrayObservacoes[$rowC031['C031_Campo']] = $rowC031['C031_Observacao'];
			if($rowC031['C031_Valor'] != $rowC031['C031_Valor_Padrao']){
				$arrayDiff[] = $rowC031['C031_Campo'];
			}
		}
		foreach ($formArray['campos'] as $keyTab => &$camposTab) {
			foreach ($camposTab as $keyCampo => &$campoFormulario) {
				$campoFormulario['extra'] = 'title="'.$arrayObservacoes[$keyCampo].'"';
				if(in_array($keyCampo, $arrayDiff)){
					if(empty($campoFormulario['label'])) { continue; }
					$campoFormulario['label'] = "<font color=\"blue\">".$campoFormulario['label']."</font>";
				}
			}
		}
		unset($camposTab);
		unset($campoFormulario);
		$form->definirFormularioArray($formArray);

		/**
		* PARTE 3: Processamento do formulário (se foi enviado)
		* 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		*/
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
			// Form sem erros
			} else if ($formCodigo == 2) {
				// Update
				require_once('bibliotecas/classes/CAD058.php');
                $CAD058 = new CAD058();
                $retorno = $CAD058->atualizarConfGlobal($form, $g['empresaAtual']);
				if ($retorno == true) {
					echo gGeraAlertMsg('Operação OK', 'Configurações salvas com sucesso!');
				}
			}
		} else {
			// Carrega os valores caso seja um update
			$sql = mysql_query("SELECT * FROM C031 WHERE C031_C004_Id = '{$g['empresaAtual']}'");
			while($row = mysql_fetch_array($sql)){
				$form->campoSetarValor($row['C031_Campo'], $row['C031_Valor']);
			}
		}

		/**
		* PARTE 4: Gerar o formulário
		*/
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

		/**
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo  $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		echo $g['smarty']->fetch('formEditor.tpl');
	break;

	case 'cad058FormConfB2B':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;

		/**
		* PARTE 2: Definição do formulário
		*/
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = false;
		if ($formMultiTab) {
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}

		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = '0000';
		$form_campo_pk = '0000_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad058FormConfB2B_Btn';
		// Form que será executado ao clicar no
		$enviar_acaoHref = '/cad/cad058/form/cad058FormConfB2B/';
		// formEditor
		// Form Editor
		$form_editor = array(
			'geral' => '{"FaturarPedidoAutomaticamenteB2B":{"top":190.99998474121094,"left":64,"width":64,"hidden":0},"ConfB2B":{"top":8,"left":247,"width":null,"hidden":1},"HabilitarPedidoOrcamento":{"top":30,"left":71,"width":192,"hidden":0},"ImagemPrincipalB2B":{"top":53,"left":157,"width":192,"hidden":0},"HabilitarPreco":{"top":76,"left":167,"width":64,"hidden":0},"QuantidadePadraoCarrinho":{"top":99,"left":83,"width":56,"hidden":0},"PermitirVendedorEditarPrecoB2B":{"top":121.99998474121094,"left":31,"width":64,"hidden":0},"PermitirRepresentanteEditarPrecoB2B":{"top":144.99998474121094,"left":45,"width":64,"hidden":0},"PermitirVendedorExternoEditarPrecoB2B":{"top":167.99998474121094,"left":27,"width":64,"hidden":0},"ConfB2Bgrid":{"top":211.99998474121094,"left":239,"width":null,"hidden":1},"MostrarOrcamentos":{"top":236.99998474121094,"left":121,"width":112,"hidden":0},"MostrarPedidos":{"top":259.99998474121094,"left":140,"width":112,"hidden":0},"MostrarBoletos":{"top":282.99998474121094,"left":143,"width":112,"hidden":0},"MostrarNotasFiscais":{"top":305.99998474121094,"left":116,"width":112,"hidden":0},"destinoProdutoB2B":{"top":328.99998474121094,"left":139,"width":112,"hidden":0}}',
		);
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('cad058FormConfB2B');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'ConfB2B' => array('name' => 'ConfB2B', 'label'=>'Configurações B2B','type' => 'titulo', 'reject' => true),
					'HabilitarPedidoOrcamento' => array('name' => 'HabilitarPedidoOrcamento', 'label' => 'Pedido/Orçamento Ecommerce', 'type' => 'select', 'select' =>
						array(
							array('title' => 'nenhum', 'value' => '0'),							
							array('title' => 'Orçamento', 'value' => '1'),
							array('title' => 'Pedido', 'value' => '2'),
						)	
					),
					'HabilitarPreco' => array('name' => 'HabilitarPreco', 'label' => 'Preço B2B', 'type' => 'select', 'select' =>
						array(
							array('title' => '...', 'value' => ''),
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N'),
						)	
					),
					'ConfB2Bgrid' => array('name' => 'ConfB2Bgrid', 'label'=>'Configurações grid B2B','type' => 'titulo', 'reject' => true),
					'MostrarPedidos' => array('name' => 'MostrarPedidos', 'label' => 'Mostrar pedidos', 'type' => 'select', 'select' =>
						array(
							array('title' => '...', 'value' => ''),
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N'),
						)	
					),
					'PermitirVendedorEditarPrecoB2B' => array('name' => 'PermitirVendedorEditarPrecoB2B', 'label' => 'Permitir vendedor interno editar preço?', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N'),
						)	
					),
					'PermitirVendedorExternoEditarPrecoB2B' => array('name' => 'PermitirVendedorExternoEditarPrecoB2B', 'label' => 'Permitir vendedor externo editar preço?', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N'),
						)	
					),
					'PermitirRepresentanteEditarPrecoB2B' => array('name' => 'PermitirRepresentanteEditarPrecoB2B', 'label' => 'Permitir representante editar preço?', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N'),
						)	
					),
					'FaturarPedidoAutomaticamenteB2B' => array('name' => 'FaturarPedidoAutomaticamenteB2B', 'label' => 'Faturar pedido automaticamente', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N'),
					   )	
				    ),
					'MostrarOrcamentos' => array('name' => 'MostrarOrcamentos', 'label' => 'Mostrar orçamentos', 'type' => 'select', 'select' =>
						array(
							array('title' => '...', 'value' => ''),
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N'),
						)	
					),
					'MostrarBoletos' => array('name' => 'MostrarBoletos', 'label' => 'Mostrar boletos', 'type' => 'select', 'select' =>
						array(
							array('title' => '...', 'value' => ''),
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N'),
						)	
					),
					'MostrarNotasFiscais' => array('name' => 'MostrarNotasFiscais', 'label' => 'Mostrar notas fiscais', 'type' => 'select', 'select' =>
						array(
							array('title' => '...', 'value' => ''),
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N'),
						)	
					),
					'destinoProdutoB2B' => array('name' => 'destinoProdutoB2B', 'label' => 'Destino Produto ', 'type' => 'select', 'select' =>
						array(
							array('title' => '1 - INDUSTRIALIZAÇÃO', 'value' => '1'),
							array('title' => '2 - REVENDA', 'value' => '2'),
							array('title' => '3 - CONSUMO', 'value' => '3'),
							array('title' => '4 - ESPECIAL', 'value' => '4'),
							array('title' => '5 - BONIFICAÇÃO', 'value' => '5'),
							array('title' => '6 - TROCA', 'value' => '6'),
							array('title' => '7 - DEMONSTRAÇÃO', 'value' => '7'),
							array('title' => '8 - CONSIGNAÇÃO', 'value' => '8'),
						)	
					),
					'QuantidadePadraoCarrinho' => array('name' => 'QuantidadePadraoCarrinho', 'label' => 'Quantidade padrão carrinho', 'type' => 'text'),
					'ImagemPrincipalB2B' => array('name' => 'ImagemPrincipalB2B', 'label' => 'Foto produto', 'type' => 'select', 'select' =>
						array(
							array('title' => '...', 'value' => ''),
							array('title' => 'Cadastrado no produto', 'value' => '1'),
							array('title' => 'Cadastrado no grupo', 'value' => '2'),
							array('title' => 'Links dos produtos', 'value' => '3'),
						)	
					),
				)	
			)
		);

		$arrayObservacoes = array();
		$arrayDiff = array();
		$sqlC031 = mysql_query("SELECT C031_Campo, C031_Observacao, C031_Valor, C031_Valor_Padrao FROM C031 GROUP BY C031_Campo");
		while($rowC031 = mysql_fetch_array($sqlC031)){
			$arrayObservacoes[$rowC031['C031_Campo']] = $rowC031['C031_Observacao'];
			if($rowC031['C031_Valor'] != $rowC031['C031_Valor_Padrao']){
				$arrayDiff[] = $rowC031['C031_Campo'];
			}
		}
		foreach ($formArray['campos'] as $keyTab => &$camposTab) {
			foreach ($camposTab as $keyCampo => &$campoFormulario) {
				$campoFormulario['extra'] = 'title="'.$arrayObservacoes[$keyCampo].'"';
				if(in_array($keyCampo, $arrayDiff)){
					if(empty($campoFormulario['label'])) { continue; }
					$campoFormulario['label'] = "<font color=\"blue\">".$campoFormulario['label']."</font>";
				}
			}
		}
		unset($camposTab);
		unset($campoFormulario);
		$form->definirFormularioArray($formArray);

		/**
		* PARTE 3: Processamento do formulário (se foi enviado)
		* 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		*/
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
			// Form sem erros
			} else if ($formCodigo == 2) {
				// Update
				require_once('bibliotecas/classes/CAD058.php');
                $CAD058 = new CAD058();
                $retorno = $CAD058->atualizarConfGlobal($form, $g['empresaAtual']);
				if ($retorno == true) {
					echo gGeraAlertMsg('Operação OK', 'Configurações salvas com sucesso!');
				}
			}
		} else {
			// Carrega os valores caso seja um update
			$sql = mysql_query("SELECT * FROM C031 WHERE C031_C004_Id = '{$g['empresaAtual']}'");
			while($row = mysql_fetch_array($sql)){
				$form->campoSetarValor($row['C031_Campo'], $row['C031_Valor']);
			}
		}

		/**
		* PARTE 4: Gerar o formulário
		*/
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

		/**
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo  $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		echo $g['smarty']->fetch('formEditor.tpl');
	break;

	case 'cad058FormConfTablet':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;

		/**
		* PARTE 2: Definição do formulário
		*/
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = false;
		if ($formMultiTab) {
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}

		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = '0000';
		$form_campo_pk = '0000_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad058FormConfTablet_Btn';
		// Form que será executado ao clicar no
		$enviar_acaoHref = '/cad/cad058/form/cad058FormConfTablet/';
		// formEditor
		// Form Editor
		$form_editor = array(
			'geral' => '{"buscaProdutoInventiarioTablet":{"top":7,"left":15.515625,"width":95,"hidden":0}}',
		);
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('cad058FormConfTablet');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'buscaProdutoInventiarioTablet' => array('name' => 'buscaProdutoInventiarioTablet', 'label' => 'Busca Produto Inventário', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Geral', 'value' => '1'),
							array('title' => 'Código', 'value' => '2'),
						)	
					),
				)	
			)
		);

		$arrayObservacoes = array();
		$arrayDiff = array();
		$sqlC031 = mysql_query("SELECT C031_Campo, C031_Observacao, C031_Valor, C031_Valor_Padrao FROM C031 GROUP BY C031_Campo");
		while($rowC031 = mysql_fetch_array($sqlC031)){
			$arrayObservacoes[$rowC031['C031_Campo']] = $rowC031['C031_Observacao'];
			if($rowC031['C031_Valor'] != $rowC031['C031_Valor_Padrao']){
				$arrayDiff[] = $rowC031['C031_Campo'];
			}
		}
		foreach ($formArray['campos'] as $keyTab => &$camposTab) {
			foreach ($camposTab as $keyCampo => &$campoFormulario) {
				$campoFormulario['extra'] = 'title="'.$arrayObservacoes[$keyCampo].'"';
				if(in_array($keyCampo, $arrayDiff)){
					if(empty($campoFormulario['label'])) { continue; }
					$campoFormulario['label'] = "<font color=\"blue\">".$campoFormulario['label']."</font>";
				}
			}
		}
		unset($camposTab);
		unset($campoFormulario);
		$form->definirFormularioArray($formArray);

		/**
		* PARTE 3: Processamento do formulário (se foi enviado)
		* 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		*/
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
			// Form sem erros
			} else if ($formCodigo == 2) {
				// Update
				require_once('bibliotecas/classes/CAD058.php');
                $CAD058 = new CAD058();
                $retorno = $CAD058->atualizarConfGlobal($form, $g['empresaAtual']);
				if ($retorno == true) {
					echo gGeraAlertMsg('Operação OK', 'Configurações salvas com sucesso!');
				}
			}
		} else {
			// Carrega os valores caso seja um update
			$sql = mysql_query("SELECT * FROM C031 WHERE C031_C004_Id = '{$g['empresaAtual']}'");
			while($row = mysql_fetch_array($sql)){
				$form->campoSetarValor($row['C031_Campo'], $row['C031_Valor']);
			}
		}

		/**
		* PARTE 4: Gerar o formulário
		*/
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

		/**
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo  $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		echo $g['smarty']->fetch('formEditor.tpl');
	break;

	case 'cad058FormConfIntelipost':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;

		/**
		* PARTE 2: Definição do formulário
		*/
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = false;
		if ($formMultiTab) {
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}

		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = '0000';
		$form_campo_pk = '0000_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad058FormConfIntelipost_Btn';
		// Form que será executado ao clicar no
		$enviar_acaoHref = '/cad/cad058/form/cad058FormConfIntelipost/';
		// formEditor
		// Form Editor
		$form_editor = array(
			'geral' => '{"EnviarIntelipostColetar":{"top":191,"left":16.1875,"width":160,"hidden":0},"ConfIntelipost":{"top":4,"left":215,"width":null,"hidden":1},"IntelipostUrlApi":{"top":30,"left":104.859375,"width":456,"hidden":0},"IntelipostApiKey":{"top":53,"left":99.953125,"width":456,"hidden":0},"IntelipostPlataform":{"top":76,"left":84.671875,"width":152,"hidden":0},"IntelipostPlataformVersion":{"top":99,"left":33.296875,"width":152,"hidden":0},"IntelipostPlugin":{"top":122,"left":107.296875,"width":152,"hidden":0},"IntelipostPluginVersion":{"top":145,"left":55.921875,"width":152,"hidden":0},"IntelipostPlataformaEnviar":{"top":168,"left":91.390625,"width":160,"hidden":0}}',
		);
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('cad058FormConfIntelipost');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'ConfIntelipost' => array('name' => 'ConfIntelipost', 'label'=>'Configurações Intelipost','type' => 'titulo', 'reject' => true),
					'IntelipostPlataformaEnviar' => array('name' => 'IntelipostPlataformaEnviar', 'label' => 'Envio por', 'type' => 'select', 'select' =>
						array(
							array('title' => '', 'value' => ''),							
							array('title' => 'Magento', 'value' => '1'),
							array('title' => 'Manual', 'value' => '2'),
						)	
					),
					'IntelipostApiKey' => array('name' => 'IntelipostApiKey', 'label' => 'Api Key', 'type' => 'text', 'uppercase' => false),
					'IntelipostPlataform' => array('name' => 'IntelipostPlataform', 'label' => 'Plataforma', 'type' => 'text', 'uppercase' => false),
					'IntelipostPlataformVersion' => array('name' => 'IntelipostPlataformVersion', 'label' => 'Versão da plataforma', 'type' => 'text', 'uppercase' => false),
					'IntelipostPlugin' => array('name' => 'IntelipostPlugin', 'label' => 'Plugin', 'type' => 'text', 'uppercase' => false),
					'IntelipostPluginVersion' => array('name' => 'IntelipostPluginVersion', 'label' => 'Versão do plugin', 'type' => 'text', 'uppercase' => false),
					'IntelipostUrlApi' => array('name' => 'IntelipostUrlApi', 'label' => 'Url Api', 'type' => 'text', 'uppercase' => false),
					'EnviarIntelipostColetar' => array('name' => 'EnviarIntelipostColetar', 'label' => 'Enviar dados ao coletar?', 'type' => 'select', 'select' =>
						array(
							array('title' => '...', 'value' => ''),							
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N'),
						)	
					),
				)	
			)
		);

		$arrayObservacoes = array();
		$arrayDiff = array();
		$sqlC031 = mysql_query("SELECT C031_Campo, C031_Observacao, C031_Valor, C031_Valor_Padrao FROM C031 GROUP BY C031_Campo");
		while($rowC031 = mysql_fetch_array($sqlC031)){
			$arrayObservacoes[$rowC031['C031_Campo']] = $rowC031['C031_Observacao'];
			if($rowC031['C031_Valor'] != $rowC031['C031_Valor_Padrao']){
				$arrayDiff[] = $rowC031['C031_Campo'];
			}
		}
		foreach ($formArray['campos'] as $keyTab => &$camposTab) {
			foreach ($camposTab as $keyCampo => &$campoFormulario) {
				$campoFormulario['extra'] = 'title="'.$arrayObservacoes[$keyCampo].'"';
				if(in_array($keyCampo, $arrayDiff)){
					if(empty($campoFormulario['label'])) { continue; }
					$campoFormulario['label'] = "<font color=\"blue\">".$campoFormulario['label']."</font>";
				}
			}
		}
		unset($camposTab);
		unset($campoFormulario);
		$form->definirFormularioArray($formArray);

		/**
		* PARTE 3: Processamento do formulário (se foi enviado)
		* 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		*/
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
			// Form sem erros
			} else if ($formCodigo == 2) {
				// Update
				require_once('bibliotecas/classes/CAD058.php');
                $CAD058 = new CAD058();
                $retorno = $CAD058->atualizarConfGlobal($form, $g['empresaAtual']);
				if ($retorno == true) {
					echo gGeraAlertMsg('Operação OK', 'Configurações salvas com sucesso!');
				}
			}
		} else {
			// Carrega os valores caso seja um update
			$sql = mysql_query("SELECT * FROM C031 WHERE C031_C004_Id = '{$g['empresaAtual']}'");
			while($row = mysql_fetch_array($sql)){
				$form->campoSetarValor($row['C031_Campo'], $row['C031_Valor']);
			}
		}

		/**
		* PARTE 4: Gerar o formulário
		*/
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

		/**
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo  $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		echo $g['smarty']->fetch('formEditor.tpl');
	break;

	case 'cad058FormConfInterface':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = $g['usuarioAtual'];

		/**
		* PARTE 2: Definição do formulário
		*/
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = false;
		if ($formMultiTab) {
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}

		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'C007';
		$form_campo_pk = 'C007_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad058FormConfInterface_Btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad058/form/cad058FormConfInterface/';
		$form_editor = array(
			'geral' => '{"C007_Tamanho_Fonte_Sistema":{"top":7,"left":19.21875,"width":96,"hidden":0}}'
		);

		// novo formulário
		$form = new Formulario('cad058FormConfInterface');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'C007_Tamanho_Fonte_Sistema' => array('name' => 'C007_Tamanho_Fonte_Sistema', 'label' => 'Tamanho da fonte', 'type' => 'select', 'select' => 
						array(
							array('title' => 'Pequeno', 'value' => '12'),
							array('title' => 'Médio', 'value' => '13'),
							array('title' => 'Grande', 'value' => '14'),
						)
					),
				)
			)
		);
		$form->definirFormularioArray($formArray);

		/**
		* PARTE 3: Processamento do formulário (se foi enviado)
		* 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		*/
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
				// Form sem erros
			} else if ($formCodigo == 2) {
				// Insert ou Update?
				if (!empty($acaoId)) {
					// Update
					if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
						// Imprime uma mensagem e fecha a janela
						$alert = gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										{$alert}
										location.reload();
									}, 150);
									});
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								// O FW atualiza a linha do grid automaticamente, somente recarregar se não for grid
								if ($('#{$g['divIdParent']}').find('table.grid').length === 0) {
									divRefresh('{$g['divIdParent']}', true);
								}
							} </script></div>
EOT;
					}
				}
			}
		} else {
			// Carrega os valores caso seja um update
			if (!empty($acaoId)) {
				$processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $acaoId);
				// mostra erros de formulário uteis ao desenvolvedor
				if ($processado !== true) {
					reset($form->formArray['campos']);
					if ($tab == key($form->formArray['campos'])) {
						echo $processado;
					}
				}
			}
		}


		/**
		* PARTE 4: Gerar o formulário
		*/
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

		/**
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');
	break;

	case 'cad058ContentConfSPEDForm':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;

		/**
		* PARTE 2: Definição do formulário
		*/
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = false;
		if ($formMultiTab) {
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}

		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = '0000';
		$form_campo_pk = '0000_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad058ContentConfSPEDForm_Btn';
		// Form que será executado ao clicar no
		$enviar_acaoHref = '/cad/cad058/form/cad058ContentConfSPEDForm/';
		// formEditor
		// Form Editor
		$form_editor = array(
			'geral' => '{"ConfSPED":{"top":3.9862518310546875,"left":196.03094482421875,"width":null,"hidden":0},"CodigoLayoutSPED":{"top":29.982818603515625,"left":151.4776611328125,"width":151.80068,"hidden":0},"CasasDecimaisSPED":{"top":52.98968505859375,"left":97.1134033203125,"width":151.80068,"hidden":0},"CasasDecimaisBlocoK":{"top":75.99658203125,"left":86.59796142578125,"width":151.80068,"hidden":0},"PerfilSPEDFiscal":{"top":98.98626708984375,"left":132.1649169921875,"width":159,"hidden":0},"dataNFRecebidaSPED":{"top":121.99313354492188,"left":66.01373291015625,"width":159,"hidden":0},"CodigoReceitaSPED":{"top":145,"left":82.680419921875,"width":151.80068,"hidden":0},"dataNFRecebidaLancarSPED":{"top":167.98971557617188,"left":11.85565185546875,"width":159,"hidden":0}}',
		);
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('cad058ContentConfSPEDForm');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'ConfSPED' => array('name' => 'ConfSPED', 'label'=>'Configurações SPED','type' => 'titulo', 'reject' => true),
					'CodigoLayoutSPED' => array('name' => 'CodigoLayoutSPED', 'label' => 'Código layout', 'type' => 'text', 'uppercase' => false),
					'CasasDecimaisSPED' => array('name' => 'CasasDecimaisSPED', 'label' => 'Nº casas decimais SPED', 'type' => 'text', 'uppercase' => false),
					'CasasDecimaisBlocoK' => array('name' => 'CasasDecimaisBlocoK', 'label' => 'Nº casas decimais Bloco K', 'type' => 'text', 'uppercase' => false),
					'PerfilSPEDFiscal' => array('name' => 'PerfilSPEDFiscal', 'label' => 'Perfil SPED Fiscal', 'type' => 'select', 'select' => 
						array(
							array('title' => 'Perfil A', 'value' => 'A'),
							array('title' => 'Perfil B', 'value' => 'B'),							
						)
					),
					'dataNFRecebidaSPED' => array('name' => 'dataNFRecebidaSPED', 'label' => 'Escritura NF recebida pela data', 'type' => 'select', 'select' => 
						array(
							array('title' => 'Data Entrada', 'value' => '1'),
							array('title' => 'Data Emissão', 'value' => '2'),							
							array('title' => 'Data Estoque', 'value' => '3'),							
						)
					),
					'CodigoReceitaSPED' => array('name' => 'CodigoReceitaSPED', 'label'=>'Código de Receita do SPED.', 'type' => 'text'),
					'dataNFRecebidaLancarSPED' => array('name' => 'dataNFRecebidaLancarSPED', 'label' => 'Enviar data entrada da NF Recebida sendo', 'type' => 'select', 'select' => 
						array(
							array('title' => 'Data Entrada', 'value' => '1'),
							array('title' => 'Data Emissão', 'value' => '2'),							
							array('title' => 'Data Estoque', 'value' => '3'),							
						)
					),
				)	
			)
		);

		$arrayObservacoes = array();
		$arrayDiff = array();
		$sqlC031 = mysql_query("SELECT C031_Campo, C031_Observacao, C031_Valor, C031_Valor_Padrao FROM C031 GROUP BY C031_Campo");
		while($rowC031 = mysql_fetch_array($sqlC031)){
			$arrayObservacoes[$rowC031['C031_Campo']] = $rowC031['C031_Observacao'];
			if($rowC031['C031_Valor'] != $rowC031['C031_Valor_Padrao']){
				$arrayDiff[] = $rowC031['C031_Campo'];
			}
		}
		foreach ($formArray['campos'] as $keyTab => &$camposTab) {
			foreach ($camposTab as $keyCampo => &$campoFormulario) {
				$campoFormulario['extra'] = 'title="'.$arrayObservacoes[$keyCampo].'"';
				if(in_array($keyCampo, $arrayDiff)){
					if(empty($campoFormulario['label'])) { continue; }
					$campoFormulario['label'] = "<font color=\"blue\">".$campoFormulario['label']."</font>";
				}
			}
		}
		unset($camposTab);
		unset($campoFormulario);
		$form->definirFormularioArray($formArray);

		/**
		* PARTE 3: Processamento do formulário (se foi enviado)
		* 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		*/
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
			// Form sem erros
			} else if ($formCodigo == 2) {
				// Update
				require_once('bibliotecas/classes/CAD058.php');
                $CAD058 = new CAD058();
                $retorno = $CAD058->atualizarConfGlobal($form, $g['empresaAtual']);
				if ($retorno == true) {
					echo gGeraAlertMsg('Operação OK', 'Configurações salvas com sucesso!');
				}
			}
		} else {
			// Carrega os valores caso seja um update
			$sql = mysql_query("SELECT * FROM C031 WHERE C031_C004_Id = '{$g['empresaAtual']}'");
			while($row = mysql_fetch_array($sql)){
				$form->campoSetarValor($row['C031_Campo'], $row['C031_Valor']);
			}
		}

		/**
		* PARTE 4: Gerar o formulário
		*/
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

		/**
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo  $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		echo $g['smarty']->fetch('formEditor.tpl');
	break;

	case 'cad058ContentConfJobsForm':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;

		/**
		* PARTE 2: Definição do formulário
		*/
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = false;
		if ($formMultiTab) {
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}

		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = '0000';
		$form_campo_pk = '0000_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad058ContentConfJobsForm_Btn';
		// Form que será executado ao clicar no
		$enviar_acaoHref = '/cad/cad058/form/cad058ContentConfJobsForm/';
		// formEditor
		// Form Editor
		$form_editor = array(
			'geral' => '{"venceOrcamento":{"top":122,"left":100.703125,"width":55,"hidden":0},"faturaPedidoAutomaticoComEstoque":{"top":7,"left":19.953125,"width":55,"hidden":0},"reabastecimentoLojaAutomatico":{"top":30,"left":80.765625,"width":55,"hidden":0},"gerarOrdemTransferenciaInterna":{"top":53,"left":55.140625,"width":55,"hidden":0},"gerarPedidoCompraInterna":{"top":76,"left":47.28125,"width":55,"hidden":0},"enviarEmailOrcamentoAutomatico":{"top":99,"left":14.671875,"width":55,"hidden":0}}',
		);
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('cad058ContentConfJobsForm');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'faturaPedidoAutomaticoComEstoque' => array('name' => 'faturaPedidoAutomaticoComEstoque', 'label' => 'Faturar pedido com estoque 100%', 'type' => 'select', 'select' => 
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N'),
						)
					),		
					'reabastecimentoLojaAutomatico' => array('name' => 'reabastecimentoLojaAutomatico', 'label' => 'Reabastecimento loja', 'type' => 'select', 'select' => 
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N'),
						)
					),	
					'gerarOrdemTransferenciaInterna' => array('name' => 'gerarOrdemTransferenciaInterna', 'label' => 'Gerar Ordem Transferência', 'type' => 'select', 'select' => 
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N'),
						)
					),		
					'gerarPedidoCompraInterna' => array('name' => 'gerarPedidoCompraInterna', 'label' => 'Gerar pedido compra interna', 'type' => 'select', 'select' => 
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N'),
						)
					),	
	 				'enviarEmailOrcamentoAutomatico' => array('name' => 'enviarEmailOrcamentoAutomatico', 'label' => 'Envio automático Email Orçamento', 'type' => 'select', 'select' => 
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N'),
						)
					),	 	
					'venceOrcamento' => array('name' => 'venceOrcamento', 'label' => 'Vence Orçamento', 'type' => 'select', 'select' => 
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N'),
						)
					),	 				
				)	
			)
		);

		$arrayObservacoes = array();
		$arrayDiff = array();
		$sqlC031 = mysql_query("SELECT C031_Campo, C031_Observacao, C031_Valor, C031_Valor_Padrao FROM C031 GROUP BY C031_Campo");
		while($rowC031 = mysql_fetch_array($sqlC031)){
			$arrayObservacoes[$rowC031['C031_Campo']] = $rowC031['C031_Observacao'];
			if($rowC031['C031_Valor'] != $rowC031['C031_Valor_Padrao']){
				$arrayDiff[] = $rowC031['C031_Campo'];
			}
		}
		foreach ($formArray['campos'] as $keyTab => &$camposTab) {
			foreach ($camposTab as $keyCampo => &$campoFormulario) {
				$campoFormulario['extra'] = 'title="'.$arrayObservacoes[$keyCampo].'"';
				if(in_array($keyCampo, $arrayDiff)){
					if(empty($campoFormulario['label'])) { continue; }
					$campoFormulario['label'] = "<font color=\"blue\">".$campoFormulario['label']."</font>";
				}
			}
		}
		unset($camposTab);
		unset($campoFormulario);
		$form->definirFormularioArray($formArray);

		/**
		* PARTE 3: Processamento do formulário (se foi enviado)
		* 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		*/
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
			// Form sem erros
			} else if ($formCodigo == 2) {
				// Update
				require_once('bibliotecas/classes/CAD058.php');
                $CAD058 = new CAD058();
                $retorno = $CAD058->atualizarConfGlobal($form, $g['empresaAtual']);
				if ($retorno == true) {
					echo gGeraAlertMsg('Operação OK', 'Configurações salvas com sucesso!');
				}
			}
		} else {
			// Carrega os valores caso seja um update
			$sql = mysql_query("SELECT * FROM C031 WHERE C031_C004_Id = '{$g['empresaAtual']}'");
			while($row = mysql_fetch_array($sql)){
				$form->campoSetarValor($row['C031_Campo'], $row['C031_Valor']);
			}
		}

		/**
		* PARTE 4: Gerar o formulário
		*/
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

		/**
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo  $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		echo $g['smarty']->fetch('formEditor.tpl');
	break;

	case 'cad058FormConfMDFe':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		
		/**
		* PARTE 2: Definição do formulário
		*/
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = false;
		if ($formMultiTab) {
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}
		
		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = '0000';
		$form_campo_pk = '0000_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad058FormConfMDFe_Btn';
		// Form que será executado ao clicar no 
		$enviar_acaoHref = '/cad/cad058/form/cad058FormConfMDFe/';
		// formEditor
		// Form Editor
		$form_editor = array(
			'geral' => '{"habilitarInfoSeguradoraObs":{"top":7,"left":25.453125,"width":64,"hidden":0},"habilitarInfoNotasObs":{"top":30,"left":49.9375,"width":64,"hidden":0}}',
		);
		
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('cad058FormConfGeral');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'habilitarInfoNotasObs' => array('name' => 'habilitarInfoNotasObs', 'label' => 'Habilitar dados das NFEs na observação' , 'type' => 'select', 'select' =>
						array(
							array('title' => 'SIM', 'value' => 'S'),
							array('title' => 'NÃO', 'value' => 'N')
						)
					),
					'habilitarInfoSeguradoraObs' => array('name' => 'habilitarInfoSeguradoraObs', 'label' => 'Habilitar dados da Seguradora na observação', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
				)
			)
		);
		
		$arrayObservacoes = array();
		$arrayDiff = array();
		$sqlC031 = mysql_query("SELECT C031_Campo, C031_Observacao, C031_Valor, C031_Valor_Padrao FROM C031 GROUP BY C031_Campo");
		while($rowC031 = mysql_fetch_array($sqlC031)){
			$arrayObservacoes[$rowC031['C031_Campo']] = $rowC031['C031_Observacao'];
			if($rowC031['C031_Valor'] != $rowC031['C031_Valor_Padrao']){
				$arrayDiff[] = $rowC031['C031_Campo'];
			}
		}
		foreach ($formArray['campos'] as $keyTab => &$camposTab) {
			foreach ($camposTab as $keyCampo => &$campoFormulario) {
				$campoFormulario['extra'] = 'title="'.$arrayObservacoes[$keyCampo].'"';
				if(in_array($keyCampo, $arrayDiff)){
					if(empty($campoFormulario['label'])) { continue; }
					$campoFormulario['label'] = "<font color=\"blue\">".$campoFormulario['label']."</font>";
				}
			}
		}
		unset($camposTab);
		unset($campoFormulario);
		$form->definirFormularioArray($formArray);

		/**
		* PARTE 3: Processamento do formulário (se foi enviado)
		* 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		*/
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
			// Form sem erros
			} else if ($formCodigo == 2) {
				// Update
				require_once('bibliotecas/classes/CAD058.php');
                $CAD058 = new CAD058();
                $retorno = $CAD058->atualizarConfGlobal($form, $g['empresaAtual']);
				if ($retorno == true) {
					echo gGeraAlertMsg('Operação OK', 'Configurações salvas com sucesso!');
				}
			}
		} else {
			// Carrega os valores caso seja um update
			$sql = mysql_query("SELECT * FROM C031 WHERE C031_C004_Id = '{$g['empresaAtual']}'");
			while($row = mysql_fetch_array($sql)){
				$form->campoSetarValor($row['C031_Campo'], $row['C031_Valor']);
			}
		}
		
		/**
		* PARTE 4: Gerar o formulário
		*/
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);
		
		/**
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo  $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		echo $g['smarty']->fetch('formEditor.tpl');
	break;

	case 'cad058FormConfImportacaoDi':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;

		/**
		* PARTE 2: Definição do formulário
		*/
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = true;
		if ($formMultiTab) {
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}

		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = '0000';
		$form_campo_pk = '0000_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad058FormConfImportacaoDi_Btn';
		// Form que será executado ao clicar no
		$enviar_acaoHref = '/cad/cad058/form/cad058FormConfImportacaoDi/';
		// formEditor
		// Form Editor
		$form_editor = array(
			'geral' => '{"MostrarDocDispesaNaDI":{"top":7,"left":35.515625,"width":79,"hidden":0},"DIUsarBotaoEntradaEstoque":{"top":30,"left":176.265625,"width":79,"hidden":0},"habilitaBotaoAtualizarCadastroProdutoViaDI":{"top":53,"left":97.1875,"width":79,"hidden":0},"diasMedioPrazo":{"top":76,"left":185.84375,"width":72,"hidden":0},"usarMoedaEstrangeira":{"top":99,"left":105.75,"width":79,"hidden":0},"permitirProdutosDuplicadosImportacao":{"top":122,"left":156.109375,"width":79,"hidden":0},"incluirItensPOnaDIFornecedorDiferente":{"top":145,"left":2.5,"width":79,"hidden":0},"modeloCalculosDI":{"top":168,"left":228.71875,"width":79,"hidden":0},"incluirLocacaoAutomaticaItemDiComValidade":{"top":191,"left":56.125,"width":79,"hidden":0}}',
			'contasPagar' => '{"tituloContaPagarXMLDi":{"top":4,"left":43,"width":null,"hidden":1},"fornecedorXmlContasPagar":{"top":30,"left":34.953125,"width":377,"hidden":0},"centroCustosXmlContasPagar":{"top":53,"left":28.09375,"width":399,"hidden":0},"portardorXmlContasPagar":{"top":76,"left":47.6875,"width":399,"hidden":0},"grupoXmlContasPagarDi":{"top":99,"left":60.875,"width":399,"hidden":0},"tituloContaPagarXMLDiPorImposto":{"top":142,"left":42,"width":null,"hidden":1},"geraContaPagarIPIAoImportarXml":{"top":168,"left":35.71875,"width":55,"hidden":0},"contaXmlContasPagarIPI":{"top":168,"left":317.515625,"width":148,"hidden":0},"geraContaPagarIIAoImportarXml":{"top":191,"left":42.65625,"width":55,"hidden":0},"contaXmlContasPagarII":{"top":191,"left":317.515625,"width":148,"hidden":0},"geraContaPagarCofinsAoImportarXml":{"top":214,"left":17.375,"width":55,"hidden":0},"contaXmlContasPagarCofins":{"top":214,"left":317.515625,"width":148,"hidden":0},"geraContaPagarPISAoImportarXml":{"top":237,"left":32.1875,"width":55,"hidden":0},"contaXmlContasPagarPIS":{"top":237,"left":317.515625,"width":148,"hidden":0},"geraContaPagarSiscomexAoImportarXml":{"top":260,"left":0.828125,"width":55,"hidden":0},"contaXmlContasPagarSiscomex":{"top":260,"left":317.515625,"width":148,"hidden":0}}',
			'compras' => '{"desabilitarCotacaoEmergenciaInternacional":{"top":7,"left":31.125,"width":55,"hidden":0},"desabilitarCompraEmergenciaInternacional":{"top":30,"left":27.75,"width":55,"hidden":0},"desabilitarMontagemEmergenciaInternacional":{"top":53,"left":15.453125,"width":55,"hidden":0},"listarItensNacionais":{"top":76,"left":32.609375,"width":55,"hidden":0}}',
		);

		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('cad058FormConfImportacaoDi');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'DIUsarBotaoEntradaEstoque' => array('name' => 'DIUsarBotaoEntradaEstoque', 'label' => 'Habilitar botão entrada estoque', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'diasMedioPrazo' => array('name' => 'diasMedioPrazo', 'label' => 'Prazo calculo estoque MP/LP', 'type' => 'text'),
					'MostrarDocDispesaNaDI' => array('name' => 'MostrarDocDispesaNaDI', 'label' => 'Mostrar documentos da despesa junto com documentos DI?', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'usarMoedaEstrangeira' => array('name' => 'usarMoedaEstrangeira', 'label' => 'Usar moeda estrangeira em "Contas a Pagar"?', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'permitirProdutosDuplicadosImportacao' => array('name' => 'permitirProdutosDuplicadosImportacao', 'label' => 'Permitir produtos duplicados na DI?', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'incluirItensPOnaDIFornecedorDiferente' => array('name' => 'incluirItensPOnaDIFornecedorDiferente', 'label' => 'Permitir incluir produtos na DI via PO com fornecedores diferentes?', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'habilitaBotaoAtualizarCadastroProdutoViaDI' => array('name' => 'habilitaBotaoAtualizarCadastroProdutoViaDI', 'label' => 'Habilitar botão atualizar cadastro produto via DI', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'modeloCalculosDI' => array('name' => 'modeloCalculosDI', 'label' => 'Modelo despesas D.I', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Padrão', 'value' => '1'),
							array('title' => 'Novo', 'value' => '2')
						)
					),	
					'incluirLocacaoAutomaticaItemDiComValidade' => array('name' => 'incluirLocacaoAutomaticaItemDiComValidade', 'label' => 'Na conferência item com controle validade lança locação<br>automatica no produto se validade for diferente', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),			
				),
				'contasPagar' => array(
					'tituloContaPagarXMLDi' => array('name' => 'tituloContaPagarXMLDi', 'label'=>'Configurações gerais para incluir contas a pagar ao importar XML','type' => 'titulo', 'reject' => true),
					'tituloContaPagarXMLDiPorImposto' => array('name' => 'tituloContaPagarXMLDi', 'label'=>'Configurações por imposto para incluir contas a pagar ao importar XML','type' => 'titulo', 'reject' => true),
					'geraContaPagarIPIAoImportarXml' => array('name' => 'geraContaPagarIPIAoImportarXml', 'label' => 'Gera contas a pagar IPI ao importar XML', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'geraContaPagarIIAoImportarXml' => array('name' => 'geraContaPagarIIAoImportarXml', 'label' => 'Gera contas a pagar II ao importar XML', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'geraContaPagarCofinsAoImportarXml' => array('name' => 'geraContaPagarCofinsAoImportarXml', 'label' => 'Gera contas a pagar Cofins ao importar XML', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'geraContaPagarPISAoImportarXml' => array('name' => 'geraContaPagarPISAoImportarXml', 'label' => 'Gera contas a pagar PIS ao importar XML', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'geraContaPagarSiscomexAoImportarXml' => array('name' => 'geraContaPagarSiscomexAoImportarXml', 'label' => 'Gera contas a pagar Siscomex ao importar XML', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'grupoXmlContasPagarDi' => array('name' => 'grupoXmlContasPagarDi', 'label' => 'Grupo', 'type' => 'select','extra' => "onchange=\"alteraConta(this, '{$g['divId']}')\"", 'select' => 
						gGeraSelect('D032', 'D032_Id', 'D032_Descricao','WHERE D032_Flag_Ativo="S" ORDER BY D032_Descricao', true)
					),
					'contaXmlContasPagarIPI' => array('name' => 'contaXmlContasPagarIPI', 'label' => 'Conta', 'type' => 'select', 'align'=>'right', 'style'=>'float:right', 'select' => 
						gGeraSelect('D014', 'D014_Id', 'IF(D154_Id>0[[virgula]]CONCAT(D154_Descricao[[virgula]]" - "[[virgula]]D014_SubConta)[[virgula]]D014_SubConta) as D014_SubConta',"LEFT JOIN D154 ON D154_Id=D014_D154_Id WHERE D014_D032_Id = '{$g['C031']['grupoXmlContasPagarDi']}' AND D014_Flag_Ativo='S' ORDER BY D014_SubConta", true)
					),
					'contaXmlContasPagarII' => array('name' => 'contaXmlContasPagarII', 'label' => 'Conta', 'type' => 'select', 'align'=>'right', 'style'=>'float:right', 'select' => 
						gGeraSelect('D014', 'D014_Id', 'IF(D154_Id>0[[virgula]]CONCAT(D154_Descricao[[virgula]]" - "[[virgula]]D014_SubConta)[[virgula]]D014_SubConta) as D014_SubConta',"LEFT JOIN D154 ON D154_Id=D014_D154_Id WHERE D014_D032_Id = '{$g['C031']['grupoXmlContasPagarDi']}' AND D014_Flag_Ativo='S' ORDER BY D014_SubConta", true)
					),
					'contaXmlContasPagarCofins' => array('name' => 'contaXmlContasPagarCofins', 'label' => 'Conta', 'type' => 'select', 'align'=>'right', 'style'=>'float:right', 'select' => 
						gGeraSelect('D014', 'D014_Id', 'IF(D154_Id>0[[virgula]]CONCAT(D154_Descricao[[virgula]]" - "[[virgula]]D014_SubConta)[[virgula]]D014_SubConta) as D014_SubConta',"LEFT JOIN D154 ON D154_Id=D014_D154_Id WHERE D014_D032_Id = '{$g['C031']['grupoXmlContasPagarDi']}' AND D014_Flag_Ativo='S' ORDER BY D014_SubConta", true)
					),
					'contaXmlContasPagarPIS' => array('name' => 'contaXmlContasPagarPIS', 'label' => 'Conta', 'type' => 'select', 'align'=>'right', 'style'=>'float:right', 'select' => 
						gGeraSelect('D014', 'D014_Id', 'IF(D154_Id>0[[virgula]]CONCAT(D154_Descricao[[virgula]]" - "[[virgula]]D014_SubConta)[[virgula]]D014_SubConta) as D014_SubConta',"LEFT JOIN D154 ON D154_Id=D014_D154_Id WHERE D014_D032_Id = '{$g['C031']['grupoXmlContasPagarDi']}' AND D014_Flag_Ativo='S' ORDER BY D014_SubConta", true)
					),
					'contaXmlContasPagarSiscomex' => array('name' => 'contaXmlContasPagarSiscomex', 'label' => 'Conta', 'type' => 'select', 'align'=>'right', 'style'=>'float:right', 'select' => 
						gGeraSelect('D014', 'D014_Id', 'IF(D154_Id>0[[virgula]]CONCAT(D154_Descricao[[virgula]]" - "[[virgula]]D014_SubConta)[[virgula]]D014_SubConta) as D014_SubConta',"LEFT JOIN D154 ON D154_Id=D014_D154_Id WHERE D014_D032_Id = '{$g['C031']['grupoXmlContasPagarDi']}' AND D014_Flag_Ativo='S' ORDER BY D014_SubConta", true)
					),
					'centroCustosXmlContasPagar' => array('name' => 'centroCustosXmlContasPagar', 'label' => 'Centro custo', 'type' => 'select','select' => 
						gGeraSelect('D073','D073_Id','D073_Nome', 'WHERE D073_Flag_Ativo = "S"', true),
					),
					'portardorXmlContasPagar' => array('name' => 'portardorXmlContasPagar', 'label' => 'Portador', 'type' => 'select', 'select' => 
						gGeraSelect('D031','D031_Id','D031_Portador', '', true),
					),
					'fornecedorXmlContasPagar' => array('name' => 'fornecedorXmlContasPagar', 'label' => 'Fornecedor', 'type' => 'pesquisa',
						'pesquisa' => array('D024', 'D024_Id', 'D024_Id, D024_Nome_Empresa', "where D024_Flag_Ativo = 'S'"),
					),				
				),
				'compras' => array(
					'desabilitarCotacaoEmergenciaInternacional' => array('name' => 'desabilitarCotacaoEmergenciaInternacional', 'label' => 'Desabilitar cotação de emergência internacional', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'desabilitarCompraEmergenciaInternacional' => array('name' => 'desabilitarCompraEmergenciaInternacional', 'label' => 'Desabilitar compras de emergência internacional', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'desabilitarMontagemEmergenciaInternacional' => array('name' => 'desabilitarMontagemEmergenciaInternacional', 'label' => 'Desabilitar montagens de emergência internacional', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'listarItensNacionais' => array('name' => 'listarItensNacionais', 'label' => 'Listar itens nacionais em compras internacional', 'type' => 'select', 'select' =>
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),				
				),
			)
		);

		$arrayObservacoes = array();
		$arrayDiff = array();
		$sqlC031 = mysql_query("SELECT C031_Campo, C031_Observacao, C031_Valor, C031_Valor_Padrao FROM C031 GROUP BY C031_Campo");
		while($rowC031 = mysql_fetch_array($sqlC031)){
			$arrayObservacoes[$rowC031['C031_Campo']] = $rowC031['C031_Observacao'];
			if($rowC031['C031_Valor'] != $rowC031['C031_Valor_Padrao']){
				$arrayDiff[] = $rowC031['C031_Campo'];
			}
		}

		foreach ($formArray['campos'] as $keyTab => &$camposTab) {
			foreach ($camposTab as $keyCampo => &$campoFormulario) {
				$campoFormulario['extra'] = 'title="'.$arrayObservacoes[$keyCampo].'"';
				if(in_array($keyCampo, $arrayDiff)){
					if(empty($campoFormulario['label'])) { continue; }
					$campoFormulario['label'] = "<font color=\"blue\">".$campoFormulario['label']."</font>";
				}
			}
		}
		unset($camposTab);
		unset($campoFormulario);
		$form->definirFormularioArray($formArray);

		/**
		* PARTE 3: Processamento do formulário (se foi enviado)
		* 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		*/
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
			// Form sem erros
			} else if ($formCodigo == 2) {
				// Update
				require_once('bibliotecas/classes/CAD058.php');
                $CAD058 = new CAD058();
                $retorno = $CAD058->atualizarConfGlobal($form, $g['empresaAtual']);
				if ($retorno == true) {
					echo gGeraAlertMsg('Operação OK', 'Configurações salvas com sucesso!');
				}
			}
		} else {
			// Carrega os valores caso seja um update
			$sql = mysql_query("SELECT * FROM C031 WHERE C031_C004_Id = '{$g['empresaAtual']}'");
			while($row = mysql_fetch_array($sql)){

				if(strpos($row['C031_Campo'], "fornecedorXmlContasPagar") !== false && !empty($row['C031_Valor'])){
					$sqlD024 = mysql_query("SELECT D024_Id, D024_Nome_Empresa FROM D024 WHERE D024_Id = '{$row['C031_Valor']}'");
					$rowD024 = mysql_fetch_array($sqlD024);
					$conteudoD024 = $rowD024['D024_Id'].' - '.$rowD024['D024_Nome_Empresa'];
					$form->campoSetarValorPesquisa($row['C031_Campo'], $conteudoD024);
				}

				$form->campoSetarValor($row['C031_Campo'], $row['C031_Valor']);
			}
		}
		
		/**
		* PARTE 4: Gerar o formulário
		*/
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);
		
		/**
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo  $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		echo $g['smarty']->fetch('formEditor.tpl');
	break;
}
