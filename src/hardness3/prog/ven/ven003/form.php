<?php
namespace hardness;
set_time_limit(0);
require_once('prog/ven/ven003/form_func-js.php');
require_once("bibliotecas/classes/ReformaTributaria.php");
require_once("bibliotecas/classes/VEN001.php");
require_once("bibliotecas/classes/VEN003.php");
require_once("bibliotecas/classes/CAD002.php");
require_once('hardness/class/nfe4_eventos.class.php');
require_once('prog/ven/ven003/grid_func-callback.php');
$VEN001 = new VEN001();
$VEN003 = new VEN003();
$CAD002 = new CAD002();
$reformaTributaria = new ReformaTributaria();

/**
 * Ações
 */
switch ($g['r'][3]) {

	/*
	 * Formulário base da geração de NF de Débito/Crédito a partir de NF-e
	 * autorizada. Esta rota existe exclusivamente para que a tela utilize o
	 * ciclo padrão content -> form do framework, inclusive personalização por
	 * cliente, sem persistir dados por este formulário.
	 */
	case 'ven003FormNFDebitoCreditoAutorizada':
		$tab = 'principal';
		$formId = 'formVen003NFDebitoCreditoAutorizada_' . uniqid();
		$form_tabela = 'D000';
		$form_campo_pk = 'D000_Id';
		$form_editor = array(
			$tab => '{"tipoOperacaoNFDCOrigem":{"top":7,"left":15,"width":230,"hidden":0},"cfopNFDCOrigem":{"top":7,"left":330,"width":650,"hidden":0},"tipoFiscalNFDCOrigem":{"top":38,"left":15,"width":350,"hidden":0},"percentualNFDCOrigem":{"top":76,"left":15,"width":85,"hidden":0},"preencherPercentualNFDCOrigem":{"top":73,"left":285,"width":155,"hidden":0}}'
		);
		$form = new Formulario('formVen003NFDebitoCreditoAutorizada');
		$form->definirFormularioArray(array(
			'campos' => array(
				$tab => array(
					'tipoOperacaoNFDCOrigem' => array(
						'name' => 'tipoOperacaoNFDCOrigem',
						'label' => 'Operação',
						'type' => 'select',
						'select' => array(
							array('title' => 'Selecione...', 'value' => '', 'selected' => true),
							array('title' => 'NF de Crédito', 'value' => 'credito'),
							array('title' => 'NF de Débito', 'value' => 'debito')
						)
					),
					'cfopNFDCOrigem' => array(
						'name' => 'cfopNFDCOrigem',
						'label' => 'CFOP',
						'type' => 'select',
						'select' => gGeraSelect('D006', 'D006_Id', 'D006_Codigo_CFOP,D006_Descricao', 'WHERE IFNULL(D006_Flag_Inativo,"N")!="S" ORDER BY D006_Codigo_CFOP', true, 0)
					),
					'tipoFiscalNFDCOrigem' => array(
						'name' => 'tipoFiscalNFDCOrigem',
						'label' => 'Tipo fiscal',
						'type' => 'select',
						'select' => array(array('title' => 'Selecione primeiro a operação...', 'value' => '', 'selected' => true)),
						'extra' => 'disabled="disabled"'
					),
					'percentualNFDCOrigem' => array(
						'name' => 'percentualNFDCOrigem',
						'label' => 'Percentual da quantidade (%)',
						'type' => 'moeda',
						'casasDecimais' => 2,
						'value' => '100,00',
						'length' => array(8, 8)
					),
					'preencherPercentualNFDCOrigem' => array(
						'name' => 'preencherPercentualNFDCOrigem',
						'label' => '',
						'type' => 'button',
						'value' => 'Preencher quantidades',
						'reload' => false,
						'reject' => true
					)
				)
			)
		));
		$gerado = gGeraCamposForm($form, $tab, false, '', '', $formId, $form_campo_pk, false, false, false, false);
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']);
		$g['smarty']->assign('form_tabela', $form_tabela);
		$g['smarty']->assign('formId', $formId);
		echo $gerado['pagina'];
		echo $g['smarty']->fetch('formEditor.tpl');
	break;
	
	case 'ven003FormPrincipal':
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
			//$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
			$formId = (isset($r_formven003formPrincipal)) ? $r_formven003formPrincipal['formId'] : $r_formId;
		} else {
			$formId = uniqid();
		}
        
		
		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'T007';
		$form_campo_pk = 'T007_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'ven003FormPrincipal_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/ven/ven003/form/ven003FormPrincipal/';

		$valorAcpInicial = '';
		if (!empty($acaoId)) {
			$sqlFlagAcp = mysql_query("SELECT T007_Flag_ACP FROM T007 WHERE T007_Id='{$acaoId}'");
			$mFlagAcp = mysql_fetch_array($sqlFlagAcp);
			$valorAcpInicial = $mFlagAcp['T007_Flag_ACP'];
		}

		//form editor
		$form_editor = array(
			'principal'    => '{"clienteProposta":{"top":0,"left":0,"width":0,"hidden":1},"T007_D024_Id":{"top":6.95465087890625,"left":120.772057056427,"width":375.039216,"hidden":0},"T007_Cliente_CNPJ_CPF":{"top":6.985298156738281,"left":575.5208740234375,"width":119.03921600000001,"hidden":0},"contatoProposta":{"top":0,"left":0,"width":0,"hidden":1},"T007_Flag_Destino_Produto":{"top":29.963241577148438,"left":76.47058916091919,"width":400,"hidden":0},"T007_Data_Emissao":{"top":52.94117736816406,"left":86.94853258132935,"width":136.039216,"hidden":0},"T007_Hora_Emissao":{"top":52.97181701660156,"left":390.11947679519653,"width":88.03921600000001,"hidden":0},"T007_Data_Saida":{"top":75.98039245605469,"left":101.7463231086731,"width":136.039216,"hidden":0},"T007_Hora_Saida":{"top":75.99571228027344,"left":404.9172673225403,"width":88.03921600000001,"hidden":0},"T007_Flag_Entrada_Saida":{"top":98.95834350585938,"left":107.53676652908325,"width":144,"hidden":0},"T007_Flag_Destino_Operacao":{"top":121.95159912109375,"left":68.79595232009888,"width":145,"hidden":0},"T007_Flag_Finalidade_NFe":{"top":144.96017456054688,"left":104.24325609207153,"width":144,"hidden":0},"T007A_Tipo_NF_Debito":{"top":144.99080657958984,"left":354.56494188308716,"width":144,"hidden":0},"T007_Flag_ACP":{"top":167.95343017578125,"left":110.29412126541138,"width":144,"hidden":0},"T007A_Tipo_NF_Credito":{"top":144.99080657958984,"left":350.96508836746216,"width":144,"hidden":0},"T007_Numero_Nota_Fiscal":{"top":167.9840545654297,"left":402.69609117507935,"width":87.03921600000001,"hidden":0},"T007_Flag_Indicador_Presenca":{"top":213.95526123046875,"left":52.864582538604736,"width":400,"hidden":0},"T007_Flag_Intermediador_Transacao":{"top":236.97915649414062,"left":32.00061082839966,"width":400,"hidden":0},"T007_D148_Id_Entrega":{"top":259.98773193359375,"left":68.53553819656372,"width":399,"hidden":0},"T007_D148_Id_Cobranca":{"top":282.98101806640625,"left":59.17585802078247,"width":399,"hidden":0},"T007A_D148_Id_Redespacho":{"top":305.9742736816406,"left":45.52695894241333,"width":399,"hidden":0},"T007_T134_Id":{"top":328.98284912109375,"left":18.612131595611572,"width":400,"hidden":0}}',
			'transporte'   => '{"T007_D022_Id":{"top":7,"left":47.000030517578125,"width":215,"hidden":0},"T007_Transportadora_Placa_Veiculo":{"top":30,"left":59.000030517578125,"width":85,"hidden":0},"T007_Estado_Veiculo":{"top":53,"left":80.00003051757812,"width":50,"hidden":0},"T007_Valor_Frete":{"top":76,"left":71.00003051757812,"width":85,"hidden":0},"T007_Flag_Frete":{"top":99,"left":97.00003051757812,"width":900,"hidden":0},"T007_Valor_Despesas_Acessorias":{"top":122,"left":16,"width":85,"hidden":0},"T007_D036_Id":{"top":145,"left":69.00003051757812,"width":200,"hidden":0},"T007_Valor_Seguro":{"top":168,"left":87.00003051757812,"width":85,"hidden":0},"T007_Quantidade_Volumes":{"top":191,"left":81.00003051757812,"width":85,"hidden":0},"T007_Numero_Caixas":{"top":213.98336791992188,"left":50.000030517578125,"width":85,"hidden":0},"T007_Especie":{"top":236.98336791992188,"left":84.00003051757812,"width":85,"hidden":0},"T007_Marca":{"top":259.9833679199219,"left":92.00003051757812,"width":85,"hidden":0},"T007_Peso_Liquido":{"top":282.9833679199219,"left":65.00003051757812,"width":85,"hidden":0},"T007_Peso_Bruto":{"top":305.9833679199219,"left":70.00003051757812,"width":85,"hidden":0},"T007_Flag_Totalizar_Peso":{"top":328.9999694824219,"left":55.000030517578125,"width":64,"hidden":0}}',
			'adicionais'   => '{"T007_Observacao_Nota_Fiscal_2":{"top":7,"left":18.999984741210938,"height":83,"width":392,"hidden":0},"T007_Observacao_Nota_Fiscal_1":{"top":99,"left":48.99998474121094,"height":87,"width":392,"hidden":0},"avisoObservacoesNF":{"top":250,"left":10,"width":null,"hidden":1}}',
			'exportacao'   => '{"T007_Exportacao_UF_Embarque":{"top":7,"left":43,"width":50,"hidden":0},"T007_Exportacao_Local_Embarque":{"top":30,"left":27,"width":275,"hidden":0}}', 
			'referenciada' => '',
			'mais' 		   => '{"T007_Casas_Decimais_Quantidade":{"top":7,"left":34.75,"width":50,"hidden":0},"T007_Flag_Trib_Transparencia":{"top":7,"left":225.96875,"width":64,"hidden":0},"T007_Casas_Decimais_Preco":{"top":30,"left":61.671875,"width":50,"hidden":0},"T007_C007_Id_Vendedor_Interno":{"top":53,"left":122.78125,"width":319,"hidden":0},"T007_D021_Id":{"top":76,"left":97.71875,"width":319,"hidden":0},"T007_Valor_Total_Comissao":{"top":99,"left":33.515625,"width":72,"hidden":0},"T007_Percentual_Comissao":{"top":99,"left":272.09375,"width":56,"hidden":0},"T007_Percentual_Comissao_Manual":{"top":99,"left":376.96875,"width":56,"hidden":0},"T007_Valor_Total_Comissao_Externo":{"top":122,"left":29.859375,"width":72,"hidden":0},"T007_Percentual_Comissao_Externo":{"top":122,"left":272.09375,"width":56,"hidden":0},"T007_Valor_Comissao_Representante":{"top":145,"left":45.765625,"width":72,"hidden":0},"T007_Percentual_Comissao_Representante":{"top":145,"left":272.09375,"width":56,"hidden":0},"T007_Valor_Total_Comissao_Usuario":{"top":168,"left":80.015625,"width":72,"hidden":0},"T007_Percentual_Comissao_Usuario":{"top":168,"left":272.09375,"width":56,"hidden":0}}'
		);

		// novo formulário
		$form = new Formulario('formven003formPrincipal');
		
		$faixaCalculo = !empty($g['C031']['padraoFaixaCalculoIcmsCredito']) ? $g['C031']['padraoFaixaCalculoIcmsCredito'] : '';

		if($g['C031']['exibirNoDanfeValorTribTransp']=='S'){
			$selected = " 'selected' => true";
		}else{
			$selected="";
		}

		// busca o ID do cliente pra carregar os contatos, caso seja edição
		$idCliente='99999999999';
		if ($acaoId>0){
			$sql = "SELECT T007_D024_Id FROM T007 LEFT JOIN D024 ON D024_Id=T007_D024_Id WHERE T007_Id = '{$acaoId}'";
			$result = mysql_query($sql);
			$aD024 = mysql_fetch_assoc($result);		
			$idCliente=$aD024['T007_D024_Id'];

			$T007 = mysql_query("SELECT T007_Numero_Nota_Fiscal from T007 WHERE T007_Id='{$acaoId}'");
			$mT007 = mysql_fetch_array($T007);

			if(!empty($mT007['T007_Numero_Nota_Fiscal'])){
				$disabled=true;
			}else{
				$disabled=false;
			}
		}
		$clientePorEmpresa = '';
		if($g['C031']['separarClientePorEmpresa'] == 'S'){
			$clientePorEmpresa = "AND (IFNULL(D024_C004_Id,0) = '{$g['empresaAtual']}' OR IFNULL(D024_C004_Id,0) = 0)";
		}

		// endereço de entrega
		$arrayEndereco = gGeraSelect('D148','D148_Id','D148_Nome','WHERE D148_D024_Id='.$idCliente.' ORDER BY D148_Id',true);
		$arrayEndereco[0]['title'] = 'PADRÃO';
		//Buscas destinos, foi centralizado nesta classe e vai valer para todos os locais
		$destinoProdutos = $CAD002->retornaDestinoProdutos($r_consumidor, 'V');

		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'principal' => array(
		            //'T007_D024_Id' => array('name' => 'Consulta_T007_D024_Id','label'=>'Cliente', 'type' => 'pesquisa','extra' => "onchange=\"carregaCFOP(this, '{$g['divId']}');\"", 'pesquisa' => array('D024','D024_Id','D024_Id,D024_Nome_Empresa,D020_Nome_Cidade,D018_UF'         ,'left join D020 on D020_Id=D024_D020_Id left join D018 on D018_Id=D024_D018_Id where D024_Flag_Ativo="S" order by D024_Nome_Empresa'), 'length' => array(64,30)),
					'T007_D024_Id' => array('name' => 'Consulta_T007_D024_Id','label'=>'Cliente', 'type' => 'pesquisa', 'extra' => "onchange=\"carregaDestinoProduto(this, '{$g['divId']}');\"", 'pesquisa' => array('D024','D024_Id','D024_Nome_Empresa,D020_Nome_Cidade,D018_UF,D024_Cpf,D024_Cnpj,D024_DDD_Telefone_1,D024_Telefone_1,D024_Nome_Fantasia,D024_Id','left join D020 on D020_Id=D024_D020_Id left join D018 on D018_Id=D024_D018_Id WHERE D024_Flag_Cliente="S" and D024_Flag_Ativo="S" '. $clientePorEmpresa .' order by D024_Nome_Empresa','%1 - %9 - (%4%5) -  [%2/%3]'), 'length' => array(80,30)),			                                                                                
					'T007_D006_Id' => array('name' => 'T007_D006_Id', 'label'=>'CFOP','type' => 'hidden', 'select' => 
						gGeraSelect('D006', 'D006_Id', 'D006_Codigo_CFOP,D006_Descricao','WHERE IFNULL(D006_Flag_Inativo,"N")!="S" ORDER BY D006_Codigo_CFOP',true),
					),
					'T007_D148_Id_Entrega' => array('name' => 'T007_D148_Id_Entrega', 'label' => 'Endereço Entrega', 'type' => 'select', 'selectRefresh' => array('D148','D148_Id','D148_Nome','WHERE D148_D024_Id='.$idCliente.' ORDER BY D148_Id',true),
						'select' => $arrayEndereco
					),
					'T007_D148_Id_Cobranca' => array('name' => 'T007_D148_Id_Cobranca', 'label' => 'Endereço Cobrança', 'type' => 'select', 'selectRefresh' => array('D148','D148_Id','D148_Nome','WHERE D148_D024_Id='.$idCliente.' ORDER BY D148_Id',true),
						'select' => $arrayEndereco
					),
					'T007A_D148_Id_Redespacho' => array('name' => 'T007A_D148_Id_Redespacho', 'label' => 'Endereço Redespacho', 'type' => 'select', 'selectRefresh' => array('D148','D148_Id','D148_Nome','WHERE D148_D024_Id='.$idCliente.' ORDER BY D148_Id',true),
						'select' => $arrayEndereco
					),
					'T007_T134_Id' => array('name' => 'T007_T134_Id','label'=>'Faixa para cálculo do Crédito<br /> de ICMS Simples Nacional','uppercase'=>true,'type' => 'select', 'select' =>
						gGeraSelect('T134', 'T134_Id','CONCAT(T134_Tipo[[virgula]]": DE "[[virgula]]convert(format(round(T134_Faixa_Inicial),2,"de_DE")using utf8)[[virgula]]" A "[[virgula]]convert(format(round(T134_Faixa_Final),2,"de_DE")using utf8)[[virgula]]" = ALIQ: "[[virgula]]T134_ICMS[[virgula]]"% REDUÇÃO: "[[virgula]]if(T134_Reducao_Base_ICMS,T134_Reducao_Base_ICMS,0)[[virgula]]"%") as faixaCalculo','',true,$faixaCalculo)
					),
					'T007_Data_Emissao' => array('name' => 'T007_Data_Emissao', 'label'=>'Data Emissão','type' => 'data','value'=>date("d/m/Y"), 'length' => array(12,10)),
					'T007_Hora_Emissao' => array('name' => 'T007_Hora_Emissao', 'label'=>'Hora Emissão','type' => 'tempo', 'value'=>date("H:i"), 'length' => array(12,10)),
					'T007_Data_Saida' => array('name' => 'T007_Data_Saida', 'label'=>'Data Saida','type' => 'data', 'value'=>date("d/m/Y"), 'length' => array(12,10)),
					'T007_Hora_Saida' => array('name' => 'T007_Hora_Saida', 'label'=>'Hora Saida','type' => 'tempo', 'value'=>date("H:i"), 'length' => array(12,10)),
					'T007_Flag_Entrada_Saida' => array('name' => 'T007_Flag_Entrada_Saida', 'label'=>'Operação','type' => 'select', 'select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Entrada', 'value' => 'E'),
							array('title' => 'Saida', 'value' => 'S' )
						)
					),							
					'T007_Flag_Destino_Operacao' => array('name' => 'T007_Flag_Destino_Operacao', 'label'=>'Destino operação','type' => 'select', 'select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => '1-Interna', 'value' => '1'),
							array('title' => '2-Inter-estadual', 'value' => '2' ),
							array('title' => '3-Com exterior', 'value' => '3' )
						)
					),							
					'T007_Flag_Finalidade_NFe' => array('name' => 'T007_Flag_Finalidade_NFe', 'label'=>'Finalidade','type' => 'select', 'select' => 
						array(
							array('title' => '...', 'value' => 0, 'selected' => true),
							array('title' => 'Normal', 'value' => 1),
							array('title' => 'Complementar', 'value' => 2),
							array('title' => 'Ajuste', 'value' => 3),
							array('title' => 'Devolução de mercadoria', 'value' => 4),
							array('title' => 'Nota de crédito', 'value' => 5),
							array('title' => 'Nota de débito', 'value' => 6),
							)
					),
					'T007A_Tipo_NF_Credito' => array('name' => 'T007A_Tipo_NF_Credito', 'label'=>'Tipo Crédito','type' => 'select', 'select' => 
						array(
							array('title' => '...', 'value' => ''),
							array('title' => '01 - Multa e juros', 'value' => '01'),
							array('title' => '02 - Crédito presumido ZFM', 'value' => '02'),
							array('title' => '03 - Retorno por recusa total/não localização', 'value' => '03'),
							array('title' => '04 - Redução de valores', 'value' => '04'),
							array('title' => '05 - Transferência de crédito na sucessão', 'value' => '05'),
							array('title' => '06 - Retorno por recusa parcial', 'value' => '06')
						)
					),
					'T007A_Tipo_NF_Debito' => array('name' => 'T007A_Tipo_NF_Debito', 'label'=>'Tipo Débito','type' => 'select', 'select' => 
						array(
							array('title' => '...', 'value' => ''),
							array('title' => '01 - Transferência de créditos p/ cooperativas', 'value' => '01'),
							array('title' => '02 - Anulação de crédito por saídas imunes/isentas', 'value' => '02'),
							array('title' => '03 - Débitos de NF não processadas na apuração', 'value' => '03'),
							array('title' => '04 - Multa e juros', 'value' => '04'),
							array('title' => '05 - Transferência de crédito de sucessão', 'value' => '05'),
							array('title' => '06 - Pagamento antecipado', 'value' => '06'),
							array('title' => '07 - Perda em estoque', 'value' => '07'),
							array('title' => '08 - Desenquadramento do Simples Nacional', 'value' => '08')
						)
					),							
					'T007_Flag_ACP' => array('name' => 'T007_Flag_ACP', 'label'=>'Modelo','type' => 'select', 'disabled'=>$disabled, 'select' => 
						array(
							array('title' => '...', 'value' => 0, 'selected' => true),
							//array('title' => 'NF', 'value' => 1),
							//array('title' => 'Romaneio', 'value' => 2),
							array('title' => 'ACP', 'value' => 3),
							array('title' => 'Cupom Fiscal', 'value' => 4),
							//array('title' => 'NF D1', 'value' => 5),
							array('title' => 'NF-e', 'value' => 6),
							array('title' => 'NFS-e', 'value' => 7),							
							array('title' => 'NFC-e', 'value' => 8),
						)
					),
					'T007_Flag_Indicador_Presenca' => array('name' => 'T007_Flag_Indicador_Presenca', 'label'=>'Indicador Presença','type' => 'select', 'select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => '0 - Não se aplica (complementar ou de ajuste)', 'value' => '0'),
							array('title' => '1 - Operação presencial', 'value' => '1'),
							array('title' => '2 - Operação não presencial, pela Internet', 'value' => '2'),
							array('title' => '3 - Operação não presencial, Teleatendimento', 'value' => '3'),
							array('title' => '4 - NFC-e em operação com entrega a domicílio', 'value' => '4'),
							array('title' => '5 - Operação presencial, fora do estabelecimento', 'value' => '5'),
							array('title' => '9 - Operação não presencial, outros', 'value' => '9'),							
						)
					),
					'T007_Flag_Intermediador_Transacao' => array('name' => 'T007_Flag_Intermediador_Transacao', 'label'=>'Intermediador Transação','type' => 'select', 'select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => '0 - Operação sem intermediador', 'value' => '0'),
							array('title' => '1 - Operação em site ou plataforma de terceiros', 'value' => '1'),							
						)
					),
                    'T007_Numero_Nota_Fiscal' => array('name' => 'T007_Numero_Nota_Fiscal', 'label'=>'Numero NF','type' => 'text', 'length' => array(12,10)),
					'T007_T075_Id' => array('name' => 'T007_T075_Id','label'=>'DI', 'type' => 'hidden',
						'select' => gGeraSelect('T075','T075_Id','T075_Numero_DI','', true)
					),
					'formId' => array('name' => 'formId', 'label' => '', 'type' => 'hidden', 'value' => $formId, 'reject' => true),
					'T007_Versao_NFe' => array('name' => 'T007_Versao_NFe', 'label' => 'T007_Versao_NFe', 'type' => 'hidden', 'value' => $g['C004']['C004_Versao_NFe'], 'reject' => true),
					'T007_Flag_Destino_Produto' => array('name' => 'T007_Flag_Destino_Produto', 'label' => 'Destino produto', 'type' => 'select',
                        'select' => $destinoProdutos
                    ),
                    'T007_Cliente_CNPJ_CPF' => array('name' => 'T007_Cliente_CNPJ_CPF', 'label'=>'CPF/CNPJ','type' => 'text', 'length' => array(18,18)),
					'atualizaDivIdMain' => array('name' => 'atualizaDivIdMain', 'label' => 'atualizaDivIdMain', 'type' => 'hidden', 'reject' => true, 'value' => 'N'),
				),
				'transporte' => array(
					'T007_D022_Id' => array('name' => 'T007_D022_Id','label'=>'Transportadora', 'type' => 'pesquisa', 'pesquisa' => array('D022','D022_Id','D022_Nome_Empresa,D020_Nome_Cidade,D018_UF',"left join D020 on D020_Id=D022_D020_Id left join D018 on D018_Id=D022_D018_Id left join D022A on D022_Id=D022A_D022_Id WHERE IFNULL(D022A_Flag_Ativo, 'S') != 'N'"), 'length' => array(50,30)),
					'T007_Transportadora_Placa_Veiculo' => array('name' => 'T007_Transportadora_Placa_Veiculo', 'label'=>'Placa veículo','type' => 'text', 'value'=> '', 'length' => array(12,10)),
					'T007_Estado_Veiculo' => array('name' => 'T007_Estado_Veiculo', 'label'=>'UF Placa','type' => 'select', 'extra'=>'style="width:50px;"','select' => 
						gGeraSelect('D018','D018_UF','D018_UF','',true)
					),											
					'T007_Valor_Frete' => array('name' => 'T007_Valor_Frete', 'label'=>'Valor frete', 'type' => 'moeda', 'casasDecimais' => 2, 'value'=> '', 'length' => array(12,10)),
					'T007_Flag_Frete' => array('name' => 'T007_Flag_Frete', 'label'=>'Frete', 'type' => 'select', 'extra'=>'style="width:250px;"','select' => 
						array(
							array('title' => '0 - Contratação por conta do Remetente', 'value' => 0),
							array('title' => '1 - Contratação por conta do Destinatário', 'value' => 1),
							array('title' => '2 - Contratação por conta de Terceiros', 'value' => 2),
							array('title' => '3 - Próprio por conta do Remetente', 'value' => 3),
							array('title' => '4 - Próprio por conta do Destinatário', 'value' => 4),							
							array('title' => '9 - Sem frete', 'value' => 9),							
						)
					),							
					'T007_Valor_Despesas_Acessorias' => array('name' => 'T007_Valor_Despesas_Acessorias', 'label'=>'Despesas acessórias', 'type' => 'moeda', 'value'=> '', 'casasDecimais' => '2', 'length' => array(12,10)),
					'T007_D036_Id' => array('name' => 'T007_D036_Id', 'label'=>'Transporte', 'type' => 'select', 'extra'=>'style="width:200px;"','select' => 
						gGeraSelect('D036','D036_Id','D036_Tipo_Transporte','',true),
					),											
					'T007_Valor_Seguro' => array('name' => 'T007_Valor_Seguro', 'label'=>'Seguro', 'type' => 'moeda', 'value'=> '', 'casasDecimais' => '2', 'length' => array(12,10)),
					'T007_Quantidade_Volumes' => array('name' => 'T007_Quantidade_Volumes', 'label'=>'Volumes', 'type' => 'text', 'value'=> '', 'length' => array(12,10)),
					'T007_Numero_Caixas' => array('name' => 'T007_Numero_Caixas', 'label'=>'Número caixas', 'type' => 'text', 'value'=> '', 'length' => array(12,10)),
					'T007_Especie' => array('name' => 'T007_Especie', 'label'=>'Espécie', 'type' => 'text', 'value'=> '', 'length' => array(12,10)),
					'T007_Valor_Despesas_Acessorias' => array('name' => 'T007_Valor_Despesas_Acessorias', 'label'=>'Despesas acessórias', 'type' => 'moeda', 'value'=> '', 'casasDecimais' => '2', 'length' => array(12,10)), 
					'T007_Marca' => array('name' => 'T007_Marca', 'label'=>'Marca', 'type' => 'text', 'value'=> '', 'length' => array(12,20)),
					'T007_Peso_Liquido' => array('name' => 'T007_Peso_Liquido', 'label'=>'Peso liquido', 'type' => 'moeda', 'casasDecimais' => '3', 'value'=> '', 'length' => array(12,10)),
					'T007_Peso_Bruto' => array('name' => 'T007_Peso_Bruto', 'label'=>'Peso bruto', 'type' => 'moeda', 'casasDecimais' => '3', 'value'=> '', 'length' => array(12,10)),
					'T007_Flag_Totalizar_Peso' => array('name' => 'T007_Flag_Totalizar_Peso', 'label'=>'Totalizar Peso','type' => 'select','select' => 
						array(
							array('title' => 'Não', 'value' => 'N'),
							array('title' => 'Sim', 'value' => 'S'),
						)
					),
				),
				'adicionais' => array(
					'T007_Observacao_Nota_Fiscal_2' => array('name' => 'T007_Observacao_Nota_Fiscal_2','label'=>'Observação destinatário', 'type' => 'textarea', 'length' => array(5,70)),
					'T007_Observacao_Nota_Fiscal_1' => array('name' => 'T007_Observacao_Nota_Fiscal_1','label'=>'Observação fiscal', 'type' => 'textarea', 'length' => array(5,70),'readonly' => false),
					'avisoObservacoesNF' => array('name' => 'avisoObservacoesNF', 'label'=>'As duas observações serão agrupadas e constarão no campo de Informações Complementares no xml da nota','type' => 'titulo', 'reject' => true),
				),
				'exportacao' => array(
					'T007_Exportacao_UF_Embarque' => array('name' => 'T007_Exportacao_UF_Embarque', 'label'=>'UF de embarque', 'type' => 'select', 'extra'=>'style="width:50px;"','select' => 
						gGeraSelect('D018','D018_UF','D018_UF','',true)),
					'T007_Exportacao_Local_Embarque' => array('name' => 'T007_Exportacao_Local_Embarque', 'label'=>'Local desembarque', 'type' => 'text', 'value'=> '', 'length' => array(50,10)),
				),
				'referenciada' => array(
				),
				'mais' => array(
					'T007_Casas_Decimais_Quantidade' => array('name' => 'T007_Casas_Decimais_Quantidade', 'label'=>'Casas decimais quantidade', 'type' => 'select','select' => 
						array(
							array('title' => '0', 'value' => 0),
						    array('title' => '1', 'value' => 1),
							array('title' => '2', 'value' => 2, 'selected' => true),							
							array('title' => '3', 'value' => 3)
						)
					),
					'T007_Casas_Decimais_Preco' => array('name' => 'T007_Casas_Decimais_Preco', 'label'=>'Casas decimais preço', 'type' => 'select','select' => 
						array(
							array('title' => '0', 'value' => 0, 'selected'=>$g['c004']['C004_Casas_Decimais_Preco']==0 ? true : false),
						    array('title' => '1', 'value' => 1, 'selected'=>$g['c004']['C004_Casas_Decimais_Preco']==1 ? true : false),
							array('title' => '2', 'value' => 2, 'selected'=>$g['c004']['C004_Casas_Decimais_Preco']==2 ? true : false),
							array('title' => '3', 'value' => 3, 'selected'=>$g['c004']['C004_Casas_Decimais_Preco']==3 ? true : false),
							array('title' => '4', 'value' => 4, 'selected'=>$g['c004']['C004_Casas_Decimais_Preco']==4 ? true : false),
							array('title' => '5', 'value' => 5, 'selected'=>$g['c004']['C004_Casas_Decimais_Preco']==5 ? true : false),
							array('title' => '6', 'value' => 6, 'selected'=>$g['c004']['C004_Casas_Decimais_Preco']==6 ? true : false),
							array('title' => '7', 'value' => 7, 'selected'=>$g['c004']['C004_Casas_Decimais_Preco']==7 ? true : false),
							array('title' => '8', 'value' => 8, 'selected'=>$g['c004']['C004_Casas_Decimais_Preco']==8 ? true : false),
							array('title' => '9', 'value' => 9, 'selected'=>$g['c004']['C004_Casas_Decimais_Preco']==9 ? true : false),
							array('title' => '10', 'value' => 10, 'selected'=>$g['c004']['C004_Casas_Decimais_Preco']==10 ? true : false),
						)
					),
					'T007_Flag_Trib_Transparencia' => array('name' => 'T007_Flag_Trib_Transparencia', 'label'=>'Exibir no DANFE total tributos lei transp?', 'type' => 'select','select' => 
						array(
							array('title' => 'Sim', 'value' => 'S', 'selected' => $g['C031']['exibirNoDanfeValorTribTransp']=='S' ? true : false),
						    array('title' => 'Não', 'value' => 'N', 'selected' => $g['C031']['exibirNoDanfeValorTribTransp']=='N' ? true : false),
						)
					),
					'T007_C007_Id_Vendedor_Interno' => array('name' => 'T007_C007_Id_Vendedor_Interno', 'label' => 'Vendedor', 'type' => 'select', 
						'select' => gGeraSelect('C007','C007_Id','IF(C007_Ativo != "N"[[virgula]] C007_Primeiro_Nome[[virgula]] CONCAT(C007_Primeiro_Nome[[virgula]]" (INATIVO)")) as C007_Primeiro_Nome','ORDER BY C007_Primeiro_Nome', true, $g['usuarioAtual']),						
					),					
				)
			)
		);
		$formArray['campos']['mais']['T007_Percentual_Comissao'] = array('name' => 'T007_Percentual_Comissao', 'label' => '%', 'type' => ($g['nivelAlcada'] < 4) ? 'hidden' : 'moeda', 'align' => 'right', 'length' => array(10,10), 'readonly' => true);
		$formArray['campos']['mais']['T007_Percentual_Comissao_Manual'] = array('name' => 'T007_Percentual_Comissao_Manual', 'label' => '% manual', 'type' => 'moeda', 'align' => 'right', 'length' => array(10,10));
    	$formArray['campos']['mais']['T007_Valor_Total_Comissao'] = array('name' => 'T007_Valor_Total_Comissao', 'label' => 'Comissão Vendedor Interno', 'type' => ($g['nivelAlcada'] < 4) ? 'hidden' : 'moeda','align' => 'right', 'length' => array(10,10), 'readonly' => true);
    	$formArray['campos']['mais']['T007_Percentual_Comissao_Representante'] = array('name' => 'T007_Percentual_Comissao_Representante', 'label' => '%', 'type' => ($g['nivelAlcada'] < 4) ? 'hidden' : 'moeda','align' => 'right', 'length' => array(10,10));
    	$formArray['campos']['mais']['T007_Valor_Comissao_Representante'] = array('name' => 'T007_Valor_Comissao_Representante', 'label' => 'Comissão Representante', 'type' => ($g['nivelAlcada'] < 4) ? 'hidden' : 'moeda','align' => 'right', 'length' => array(10,10), 'readonly' => true);
    	$formArray['campos']['mais']['T007_Percentual_Comissao_Externo'] = array('name' => 'T007_Percentual_Comissao_Externo', 'label' => '%', 'type' => ($g['nivelAlcada'] < 4) ? 'hidden' : 'moeda','align' => 'right', 'length' => array(10,10));
    	$formArray['campos']['mais']['T007_Valor_Total_Comissao_Externo'] = array('name' => 'T007_Valor_Total_Comissao_Externo', 'label' => 'Comissão Vendedor Externo', 'type' => ($g['nivelAlcada'] < 4) ? 'hidden' : 'moeda','align' => 'right', 'length' => array(10,10), 'readonly' => true);
    	$formArray['campos']['mais']['T007_Percentual_Comissao_Usuario'] = array('name' => 'T007_Percentual_Comissao_Usuario', 'label' => '%', 'type' => ($g['nivelAlcada'] < 4) ? 'hidden' : 'moeda','align' => 'right', 'length' => array(10,10));
    	$formArray['campos']['mais']['T007_Valor_Total_Comissao_Usuario'] = array('name' => 'T007_Valor_Total_Comissao_Usuario', 'label' => 'Comissão Usuário', 'type' => ($g['nivelAlcada'] < 4) ? 'hidden' : 'moeda','align' => 'right', 'length' => array(10,10), 'readonly' => true);
    	$formArray['campos']['mais']['T007_D021_Id'] = array('name' => 'T007_D021_Id', 'label' => 'Representante', 'extra' => "onchange=\"atualizarComissaoRepresentante(this, '{$g['divId']}');\"", 'type' => ($g['nivelAlcada'] < 4) ? 'hidden' : 'select', 'select' => gGeraSelect('D021', 'D021_Id', 'D021_Nome_Empresa,D021_Nome_Fantasia', "WHERE D021_Ativo = 'S'",'ORDER BY D021_Nome_Empresa', true));


		$form->definirFormularioArray($formArray);
		//if ($r_tab == 'principal') {
	        echo <<<EOT
	            <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
	            	function atualizarCamposCreditoDebitoRtc() {
	            		var finalidade = $('#{$g['divId']} #T007_Flag_Finalidade_NFe').val();
	            		var campoCredito = $('#{$g['divId']} #T007A_Tipo_NF_Credito').closest('.formEditor');
	            		var campoDebito = $('#{$g['divId']} #T007A_Tipo_NF_Debito').closest('.formEditor');

	            		campoCredito.hide();
	            		campoDebito.hide();

	            		if (finalidade == '5') {
	            			campoCredito.show();
	            		} else if (finalidade == '6') {
	            			campoDebito.show();
	            		}
	            	}

	            	function atualizarCamposAcp(valor) {
	            		if (typeof valor == 'undefined') {
	            			valor = $('#{$g['divId']} #T007_Flag_ACP').val();
	            		}
	            		if (typeof valor == 'undefined') {
	            			valor = $('#{$r_formId} #T007_Flag_ACP').val();
	            		}
	            		if (typeof valor == 'undefined') {
	            			valor = $('#T007_Flag_ACP').val();
	            		}
	            		var campoNumero = $('#{$g['divId']} #T007_Numero_Nota_Fiscal').closest('.formEditor');
	            		var campoCpfCnpj = $('#{$g['divId']} #T007_Cliente_CNPJ_CPF').closest('.formEditor');

	            		campoNumero.hide();
	            		campoCpfCnpj.hide();

	            		if ((valor == '7') || (valor == '5')) {
	            			campoNumero.show();
	            			setTimeout(function() {
	            				$('#{$g['divId']} #T007_Numero_Nota_Fiscal').focus();
	            			}, 100);
	            		}

	            		if (valor == '8') {
	            			campoCpfCnpj.show();
	            		}
	            	}

	                $('#{$g['divId']}').undelegate('#T007_Flag_ACP', 'change').delegate('#T007_Flag_ACP', 'change', function() {
	                	atualizarCamposAcp($(this).val());
	                    if($(this).val() != 8){
		                	$('#{$r_tabTransporte} #T007_D022_Id_Pesquisa').prop('readonly', false).css('background-color', '#ffffff');
	                    }
	                });
	                $('#{$g['divId']} #T007_Flag_Finalidade_NFe').change(function() {
	                	atualizarCamposCreditoDebitoRtc();
	                });
	                $('#{$g['divId']} #T007_Numero_Nota_Fiscal').closest('.formEditor').hide();
	                $('#{$g['divId']} #T007_Cliente_CNPJ_CPF').closest('.formEditor').hide();
	                atualizarCamposAcp('{$valorAcpInicial}');
	                atualizarCamposCreditoDebitoRtc();
	                setTimeout(function() {
	                	atualizarCamposAcp($('#{$g['divId']} #T007_Flag_ACP').val() || '{$valorAcpInicial}');
	                	atualizarCamposCreditoDebitoRtc();
	                }, 200);
	            } </script></div>
EOT;
		//}

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
                $FlagACP = $form->campoValorEnviado('T007_Flag_ACP');
                $NumeroNota = $form->campoValorEnviado('T007_Numero_Nota_Fiscal');
                if(($FlagACP == 7 || $FlagACP == 5) && (empty($NumeroNota) || $NumeroNota=='')){
                    echo gGeraAlertMsg('ERRO', 'É necessário preencher o campo "Numero NF".');
                }else{
			        // Insert ou Update?
			        if (!empty($acaoId)) {
				        // Update
				        if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
				        	if($g['C031']['modeloExpedicao'] == 2){
								if($FlagACP == '4'){
									$T007_Flag_Expedicao = 2;
								} else {
									$T007_Flag_Expedicao = 1;
								}
								mysql_query("UPDATE T007 SET T007_Flag_Expedicao = '{$T007_Flag_Expedicao}' WHERE T007_Id = '{$acaoId}'");
							}

					        $retorno = $VEN003->incluirNota($form,$acaoId);

							//Atualiza as aliquotas dos produtos, caso o orçamento seja gerado pelo coletor
							echo <<<EOT
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									idDialodAliquotas = dialogAlert('Atualizando Alíquotas',"<span id='progressBar'>Atualizar aliquotas</span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' />");
									$.getJSON('/ven/ven003/form_func-ajax/atualizarAliquota/?ajax=true&T007_Id=' + encodeURIComponent("{$acaoId}") + '&callback=?', function(request) {
										$('#dc-' + idDialodAliquotas).dialog("destroy").remove();
										if (request.code) {
										} else {
											var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
						    				dialogConfirm('Erro', request.data, \$buttons);
											return false;
										}
									});
								} </script></div>
EOT;

					        if ($retorno !== true) {
						        echo gGeraAlertMsg('Operação ERRO', $retorno);
					        } else {
						        echo gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!');
					        }

							// Definido no form como N, se o cliente quiser atualizar pode personalizar e alterar o value para S
							$atualizaDivIdMain = $form->campoValorEnviado('atualizaDivIdMain');	

					        echo <<<EOT
                            <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                                // O FW atualiza a linha do grid automaticamente, somente recarregar se não for grid
								if ($('#{$g['divIdParent']}').find('table.grid').length === 0) {
									divRefresh('{$g['divIdParent']}', true);
								}								
								if('{$atualizaDivIdMain}' == 'S'){	
									divRefresh('{$r_divRefresh}', true);
								}
                                divRefresh($('.contentCabecalhoLayout').parent().attr('id'));
                            } </script></div>
EOT;
				        }
			        } else {
				        $retorno = $VEN003->incluirNota($form,$acaoId);
				        $acaoId = $g['mysqlLastId'];
				        if ($retorno !== true) {
					        echo gGeraAlertMsg('Operação ERRO', $retorno);
				        } else {
					        echo gGeraAlertMsg('Operação OK', 'Registro Incuido com Sucesso!');
				        }
				        echo <<<EOT
                            <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                            $("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
						        setTimeout(function() {
							        abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}', unique(), '', 'Editar Nota Fiscal - ID: ' + '{$acaoId}', '/ven/ven003/content/ven003contentIncluirNotaFiscal/', '&tab=geral&acaoId=' + encodeURIComponent('{$acaoId}'), [900,550]);
							        {$alert}
						        }, 150);
						    });
					        $("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
                                divRefresh('{$g['divIdParent']}', true);
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
	

	case 'ven003FormTotais':

		$Empresa_Atual = $g['empresaAtual'];
		$T007_Id = $r_acaoId;
        mysql_query("call T007_Gravar_Totalizacao_4({$T007_Id})");
		$T008=mysql_query("select sum(T008_Valor_ICMS_Credito) T008_Valor_ICMS_Credito, sum(T008_Valor_Lucro) as Lucro,
                          T008_Aliquota_ICMS_Credito
                     	  from T008
                    	  where T008_T007_Id='$T007_Id'
                    	  group by T008_T007_Id");
		echo mysql_error();
		$mT008=mysql_fetch_array($T008);
		
		
		$T007=mysql_query("SELECT 
							T007_Valor_Total_Produtos_Sem_Desconto,
							T007_Valor_Total_Servicos,
							T007_Valor_ICMS_Substituicao_Retencao,
							T007_Valor_Total_IPI,
							T007_Valor_Despesas_Acessorias,
							T007_Valor_Frete,
							T007_Valor_Desconto,
							T007_Valor_Seguro,
							T007_Valor_Total,
							T007_Valor_Total_ICMS,
							T007_Valor_Total_PIS,
							T007_Valor_Total_COFINS,
							T007A_Valor_Total_PIS_Retido,
							T007A_Valor_Total_COFINS_Retido,
							T007_Peso_Liquido,
							T007_Valor_Total_IPI_Devolucao
						  FROM T007
					 LEFT JOIN T007A ON T007A_T007_Id=T007_Id
                    	  WHERE T007_Id='$T007_Id'");
		echo mysql_error();
		$mT007=mysql_fetch_array($T007);

 		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;
		
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
		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		
		
		$form_tabela = 'T007';
		$form_campo_pk = 'T007_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = '';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '';

		// form editor
		$form_editor = array('geral' => '{"T007_Valor_Total_Produtos_Sem_Desconto":{"top":7,"left":61.859375,"width":232,"hidden":0},"T007_Valor_Total_Servicos":{"top":30,"left":63.703125,"width":232,"hidden":0},"T007_Valor_ICMS_Substituicao_Retencao":{"top":53,"left":62.484375,"width":232,"hidden":0},"T007_Valor_Total_IPI":{"top":76,"left":92.4375,"width":232,"hidden":0},"T007_Valor_Despesas_Acessorias":{"top":99,"left":23.34375,"width":232,"hidden":0},"T007_Valor_Frete":{"top":122,"left":80.203125,"width":232,"hidden":0},"T007_Valor_Desconto":{"top":145,"left":60.640625,"width":232,"hidden":0},"T007_Valor_Seguro":{"top":168,"left":70.40625,"width":232,"hidden":0},"T007_Valor_Total":{"top":191,"left":82.640625,"width":232,"hidden":0},"T007_Valor_Total_ICMS":{"top":213.984375,"left":78.375,"width":232,"hidden":0},"T008_Valor_ICMS_Credito":{"top":236.984375,"left":11.9375,"width":232,"hidden":0},"T007_Valor_Total_PIS":{"top":259.984375,"left":88.15625,"width":232,"hidden":0},"T007_Valor_Total_COFINS":{"top":282.984375,"left":64.328125,"width":232,"hidden":0},"T007_Peso_Liquido":{"top":305.984375,"left":61.859375,"width":232,"hidden":0},"Lucro":{"top":329,"left":61.25,"width":232,"hidden":0},"Percentual_Lucro":{"top":352,"left":65.53125,"width":232,"hidden":0},"T007_Valor_Total_IPI_Devolucao":{"top":375,"left":37.40625,"width":232,"hidden":0},"T007A_Valor_Total_PIS_Retido":{"top":398,"left":49,"width":232,"hidden":0},"T007A_Valor_Total_COFINS_Retido":{"top":421,"left":34,"width":232,"hidden":0}}');
		
		$lucro = gCorrigeNumero(($mT008['Lucro']/$mT007['T007_Valor_Total_Produtos'])*100);
        $mT008['Lucro'] = gCorrigeNumero($mT008['Lucro']);
        $mT007['T007_Valor_Total_Produtos_Sem_Desconto']=number_format($mT007['T007_Valor_Total_Produtos_Sem_Desconto'],2,',','.');
        $mT007['T007_Valor_Total_Servicos']=number_format($mT007['T007_Valor_Total_Servicos'],2,',','.');
		$mT007['T007_Valor_ICMS_Substituicao_Retencao']=number_format($mT007['T007_Valor_ICMS_Substituicao_Retencao'],2,',','.');
		$mT007['T007_Valor_Total_IPI']=number_format($mT007['T007_Valor_Total_IPI'],2,',','.');
		$mT007['T007_Valor_Despesas_Acessorias']=number_format($mT007['T007_Valor_Despesas_Acessorias'],2,',','.');
		$mT007['T007_Valor_Frete']=number_format($mT007['T007_Valor_Frete'],2,',','.');
		$mT007['T007_Valor_Desconto']=number_format($mT007['T007_Valor_Desconto'],2,',','.');
		$mT007['T007_Valor_Seguro']=number_format($mT007['T007_Valor_Seguro'],2,',','.');
		$mT007['T007_Valor_Total']=number_format($mT007['T007_Valor_Total'],2,',','.');
		$mT007['T007_Valor_Total_ICMS']=number_format($mT007['T007_Valor_Total_ICMS'],2,',','.');
        $mT008['T008_Valor_ICMS_Credito']=number_format($mT008['T008_Valor_ICMS_Credito'],2,',','.');
		$mT007['T007_Valor_Total_PIS']=number_format($mT007['T007_Valor_Total_PIS'],2,',','.');
		$mT007['T007_Valor_Total_COFINS']=number_format($mT007['T007_Valor_Total_COFINS'],2,',','.');
		$mT007['T007A_Valor_Total_PIS_Retido']=number_format($mT007['T007A_Valor_Total_PIS_Retido'],2,',','.');
		$mT007['T007A_Valor_Total_COFINS_Retido']=number_format($mT007['T007A_Valor_Total_COFINS_Retido'],2,',','.');
		$mT007['T007_Peso_Liquido']=number_format($mT007['T007_Peso_Liquido'],2,',','.');
		$mT007['T007_Valor_Total_IPI_Devolucao']=number_format($mT007['T007_Valor_Total_IPI_Devolucao'],2,',','.');


		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
     				'T007_Valor_Total_Produtos_Sem_Desconto' => array('name' => 'T007_Valor_Total_Produtos_Sem_Desconto', 'value' => $mT007['T007_Valor_Total_Produtos_Sem_Desconto'], 'label' => 'Produtos', 'type' => 'moeda', 'casasDecimais' => '2', 'length' => array(40, 80), 'disabled' => 'true'),
                    'T007_Valor_Total_Servicos' => array('name' => 'T007_Valor_Total_Servicos', 'value' => $mT007['T007_Valor_Total_Servicos'], 'label' => 'Serviços', 'type' => 'moeda', 'casasDecimais' => '2', 'length' => array(40, 80), 'disabled' => 'true'),
					'T007_Valor_ICMS_Substituicao_Retencao' => array('name' => 'T007_Valor_ICMS_Substituicao_Retencao', 'value' => $mT007['T007_Valor_ICMS_Substituicao_Retencao'], 'label' => 'ICMS ST', 'type' => 'moeda', 'casasDecimais' => '2', 'length' => array(40,80), 'disabled' => 'true'),
					'T007_Valor_Total_IPI' => array('name' => 'T007_Valor_Total_IPI', 'value' => $mT007['T007_Valor_Total_IPI'], 'label' => 'IPI', 'type' => 'moeda', 'length' => array(40,80), 'casasDecimais' => '2', 'disabled' => 'true'),
					'T007_Valor_Despesas_Acessorias' => array('name' => 'T007_Valor_Despesas_Acessorias', 'value' => $mT007['T007_Valor_Despesas_Acessorias'], 'label' => 'Desp.Acessórias', 'type' => 'moeda', 'casasDecimais' => '2', 'length' => array(40,80), 'disabled' => 'true'),
					'T007_Valor_Frete' => array('name' => 'T007_Valor_Frete', 'value' => $mT007['T007_Valor_Frete'], 'label' => 'Frete', 'type' => 'moeda', 'length' => array(40,80), 'casasDecimais' => '2', 'disabled' => 'true'),
					'T007_Valor_Desconto' => array('name' => 'T007_Valor_Desconto', 'value' => $mT007['T007_Valor_Desconto'], 'label' => 'Desc.R$.', 'type' => 'moeda', 'length' => array(40,80), 'casasDecimais' => '2', 'disabled' => 'true'),
					'T007_Valor_Seguro' => array('name' => 'T007_Valor_Seguro', 'value' => $mT007['T007_Valor_Seguro'], 'label' => 'Seguro', 'type' => 'moeda', 'length' => array(40,80), 'casasDecimais' => '2', 'disabled' => 'true'),
					'T007_Valor_Total' => array('name' => 'T007_Valor_Total', 'value' => $mT007['T007_Valor_Total'], 'label' => 'Total', 'type' => 'moeda', 'length' => array(40,80), 'casasDecimais' => '2', 'disabled' => 'true'),
					'T007_Valor_Total_ICMS' => array('name' => 'T007_Valor_Total_ICMS', 'value' => $mT007['T007_Valor_Total_ICMS'], 'label' => 'ICMS', 'type' => 'moeda', 'length' => array(40,80), 'casasDecimais' => '2', 'disabled' => 'true'),
                    'T008_Valor_ICMS_Credito' => array('name' => 'T008_Valor_ICMS_Credito', 'value' => $mT008['T008_Valor_ICMS_Credito'], 'label' => 'Valor ICMS Crédito', 'type' => 'moeda', 'length' => array(40,80), 'casasDecimais' => '2', 'disabled' => 'true','reject' => true),
					'T007_Valor_Total_PIS' => array('name' => 'T007_Valor_Total_PIS', 'value' => $mT007['T007_Valor_Total_PIS'], 'label' => 'PIS', 'type' => 'moeda', 'length' => array(40,80), 'casasDecimais' => '2', 'disabled' => 'true'),
					'T007_Valor_Total_COFINS' => array('name' => 'T007_Valor_Total_COFINS', 'value' => $mT007['T007_Valor_Total_COFINS'], 'label' => 'COFINS', 'type' => 'moeda', 'length' => array(40,80), 'casasDecimais' => '2', 'disabled' => 'true'),
					'T007A_Valor_Total_PIS_Retido' => array('name' => 'T007A_Valor_Total_PIS_Retido', 'value' => $mT007['T007A_Valor_Total_PIS_Retido'], 'label' => 'Total Valor PIS Ret.', 'type' => 'moeda', 'length' => array(40,80), 'casasDecimais' => '2', 'disabled' => 'true'),
					'T007A_Valor_Total_COFINS_Retido' => array('name' => 'T007A_Valor_Total_COFINS_Retido', 'value' => $mT007['T007A_Valor_Total_COFINS_Retido'], 'label' => 'Total Valor COFINS Ret.', 'type' => 'moeda', 'length' => array(40,80), 'casasDecimais' => '2', 'disabled' => 'true'),
					'T007_Peso_Liquido' => array('name' => 'T007_Peso_Liquido', 'value' => $mT007['T007_Peso_Liquido'], 'label' => 'Peso KG', 'type' => 'moeda', 'length' => array(40,80), 'casasDecimais' => '2', 'disabled' => 'true'),
					'T007_Valor_Total_IPI_Devolucao' => array('name' => 'T007_Valor_Total_IPI_Devolucao', 'value' => $mT007['T007_Valor_Total_IPI_Devolucao'], 'label' => 'IPI Devolução', 'type' => 'moeda', 'length' => array(40,80), 'casasDecimais' => '2', 'disabled' => 'true'),
				)
			)
		);
		if($g['C007']['C007_Flag_Vendedor'] == 'D'){
			$formArray['campos']['geral']['Lucro'] = array('name' => 'Lucro', 'value' => $mT008['Lucro'], 'label' => 'R$ Lucro', 'type' => 'moeda', 'length' => array(40,80), 'casasDecimais' => '2', 'disabled' => 'true');
			$formArray['campos']['geral']['Percentual_Lucro'] = array('name' => 'Percentual_Lucro', 'value' => $lucro, 'label' => '% Lucro', 'type' => 'moeda', 'length' => array(40,80), 'casasDecimais' => '2', 'disabled' => 'true');
		}
        $form = new Formulario('form-ven001FormTotais');
		$form->definirFormularioArray($formArray);

		/**
		 * PARTE 3: Processamento do formulário (se foi enviado)
		 * E carregamento dos valores (se não foi enviado parametro acaoId)
		 */
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
			// Form sem erros
			} 
		} 

		/**
		 * PARTE 4: Gerar o formulário
		 */
		$gerado = gGeraCamposForm($form, $tab, $r_acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

		/**
		 * PARTE 5: Exibir o formulário
		 */
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');
	break;
	
	case 'ven003FormTecnico':
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;

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

		$form_tabela = 'T007';
		$form_campo_pk = 'T007_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)

		$enviar_btnId = array('ven003FormBtnEnviar', 'ven003FormBtnLimpar');
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/ven/ven003/form/ven003FormTecnico/';

		if($g['usuarioAtual'] == '63' or $g['nivelAlcada']>=4){
			$campoDisabled = false;
			$type= 'button';
			$comboDisabled = "";
		} else {
			$campoDisabled = true;
			$type= 'hidden';
			$comboDisabled = "onfocus='this.initialSelect = this.selectedIndex;' onchange='this.selectedIndex = this.initialSelect;'";
		}
		//form editor
		$form_editor = array('geral' => '{"T007_Chave_Acesso_Nfe":{"top":7,"left":12.21875,"width":280,"hidden":0},"T007_TX_Resposta_Codigo":{"top":7,"left":419.078125,"width":264,"hidden":0},"ven003FormBtnVars":{"top":28,"left":656,"width":null,"hidden":0},"T007_Numero_Recibo_Nfe":{"top":30,"left":13.453125,"width":280,"hidden":0},"Validador":{"top":28,"left":416,"width":null,"hidden":0},"ven003FormBtnXML":{"top":28,"left":528,"width":null,"hidden":0},"T007_Numero_Nota_Fiscal":{"top":53,"left":24.46875,"width":280,"hidden":0},"T007_Serie":{"top":76,"left":56.859375,"width":280,"hidden":0},"T007_Flag_Almoxarifado":{"top":99,"left":-5.5,"width":280,"hidden":0},"T007_Flag_Impresso":{"top":122,"left":12.859375,"width":280,"hidden":0},"T007_Versao_NFe":{"top":145,"left":55.640625,"width":58,"hidden":0},"ven003FormDadosNota":{"top":143,"left":176,"width":null,"hidden":0},"ven003FormBtnLimpar":{"top":143,"left":256,"width":null,"hidden":0},"ven003FormBtnEnviar":{"top":143,"left":320,"width":null,"hidden":0}}');

		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
     				'T007_Chave_Acesso_Nfe' 	=> array('name' => 'T007_Chave_Acesso_Nfe', 'label' => 'Chave Acesso', 'type' => 'text', 'length' => array(50, 80)),
					'T007_Numero_Recibo_Nfe' 	=> array('name' => 'T007_Numero_Recibo_Nfe', 'label' => 'Número Recibo', 'type' => 'text', 'length' => array(50,80)),
					'T007_Numero_Nota_Fiscal'	=> array('name' => 'T007_Numero_Nota_Fiscal', 'label' => 'Número Nota', 'type' => 'text', 'readonly' => $campoDisabled),
     				'T007_Serie' 				=> array('name' => 'T007_Serie', 'label' => 'Serie', 'type' => 'text', 'readonly' => $campoDisabled),
					'T007_Flag_Almoxarifado' 	=> array('name' => 'T007_Flag_Almoxarifado','label' => 'Flag Almoxarifado', 'type' => 'text','readonly' => $campoDisabled),
					'T007_Flag_Impresso' 		=> array('name' => 'T007_Flag_Impresso', 'label' => 'Flag Impresso', 'type' => 'text', 'readonly' => $campoDisabled),
					'T007_TX_Resposta_Codigo' 	=> array('name' => 'T007_TX_Resposta_Codigo', 'label' => 'Flag Transmissão', 'readonly' => $campoDisabled, 'type' => 'select',
						'select' => array(
							array('value' => '0', 'title' => '...', 'selected' => true),
							array('value' => '1', 'title' => '001 - Gerando XML'),
							array('value' => '2', 'title' => '002 - Assinando XML'),
							array('value' => '3', 'title' => '003 - Transmitindo NF-e para a SEFAZ'),
							array('value' => '4', 'title' => '004 - XML Gerado! Assinando'),
							array('value' => '5', 'title' => '005 - XML Assinado! Transmitindo'),
							array('value' => '6', 'title' => '006 - Aguardando retorno da Sefaz'),
							array('value' => '7', 'title' => '007 - Danfe Gerado!'),
							array('value' => '8', 'title' => '008 - NF-e Enviada! Aguardando confirmacao'),
							array('value' => '9', 'title' => '009 - NF-e Enviada e Autorizada!'),
							array('value' => '10', 'title' => '010 - Refazendo NF'),
							array('value' => '11', 'title' => '011 - Refazendo NF para ser enviada via SVC '),
							array('value' => '12', 'title' => '012 - Iniciando Processo'),
							array('value' => '13', 'title' => '013 - Enviado e-mail'),
							array('value' => '14', 'title' => '014 - Enviado e-mail boleto'),
							array('value' => '15', 'title' => '015 - NFC-e emitida em contingência'),
							array('value' => '100', 'title' => '100 - ERRO Interno: SELECT na T007 nao retornou resultado!'),
							array('value' => '101', 'title' => '101 -  NF-e VERSAO 1.10 nao e mais suportado.'),
							array('value' => '102', 'title' => '102 - Versao nao foi definida na nota fiscal (T007_Versao_NFe), verifique!'),
							array('value' => '103', 'title' => '103 - NFe esta definida como treinamento e o ambiente nao e homologacao, verifique!'),
							array('value' => '104', 'title' => '104 - Tipo Certificado A3 nao e mais suportado.'),
							array('value' => '105', 'title' => '105 - O Tipo do documento nao e NF-e. Verifique!'),
							array('value' => '106', 'title' => '106 - NOTA FISCAL ESTA CANCELADA NAO PODE SER TRANSMITIDA PARA O SEFAZ!'),
							array('value' => '107', 'title' => '107 - Senha do certificado invalida.'),
							array('value' => '108', 'title' => '108 - Senha do certificado em branco.'),
							array('value' => '109', 'title' => '109 - Erro Gerando XML!'),
							array('value' => '110', 'title' => '110 - ERRO Interno: Tentando gravar a chave de acesso na tabela T007!'),
							array('value' => '111', 'title' => '111 - ERRO Interno: Tentando gravar na T127!'),
							array('value' => '112', 'title' => '112 - ERRO Interno: Falha ao copiar arrquivo!'),
							array('value' => '113', 'title' => '113 - Nota Fiscal nao foi assinada, impossível transmitir!'),
							array('value' => '114', 'title' => '114 - Ocorreu um erro ao gravar o retorno do Sefaz. Por favor entre em contato com o suporte e informe o numero do recibo.'),
							array('value' => '115', 'title' => '115 - A NF-e foi enviada, porem, ocorreu um erro ao gravar o retorno do Sefaz na nota fiscal. Por favor entre em contato com o suporte e informe o numero do recibo.'),
							array('value' => '116', 'title' => '116 - A NF-e foi enviada, porem, ao consultar o protocolo, ocorreu um erro.'),
							array('value' => '117', 'title' => '117 - ERRO Interno: Tentando limpar chave de acesso.'),
							array('value' => '118', 'title' => '118 - ESSA NOTA JA POSSUI PROTOCOLO, NAO PODE SER ENVIANDO PELA SVC.'),
							array('value' => '119', 'title' => '119 - Ja existe um numero de protocolo para essa nota, processo nao pode ser refeito.'),
							array('value' => '120', 'title' => '120 - ERRO Interno: Salvando dados na T007.'),
							array('value' => '121', 'title' => '121 - Certificado Digital não encontrado.'),
							array('value' => '122', 'title' => '122 - Erro de Mysql.'),
							array('value' => '200', 'title' => '200 - Atencao! Erro nao encontrado registro de erro na global.T136 - verificar e incluir este erro.'),
							array('value' => '201', 'title' => '201 - Nao foi possivel limpar a chave de acesso, pois esta nota ja tem protocolo.'),
							array('value' => '202', 'title' => '202 - Erro Sefaz'),
						)
					),
					'T007_Versao_NFe' 			=> array('name' => 'T007_Versao_NFe','label' => 'Versão', 'type' => 'select',
						'select' => array(
							array('title' => '3.10', 'value' => '3.10', 'selected' => true),
							array('title' => '4.00', 'value' => '4.00'),
						)
					),
					'Validador' => array('name' => 'Validador', 'type' => 'button', 'reload' => false, 'value' => 'Validação XML', 'reject' => true, 'extra' => "onclick=\"visuXML('{$r_acaoId}');\"")
				)
			)
		);
        $form = new Formulario('ven003FormTecnico');

        // Botões dentro do Formulário

		$formArray['campos']['geral']['ven003FormBtnEnviar'] = array('name' => 'ven003FormBtnEnviar', 'type' => 'submit', 'reload' => false, 'value' => 'Enviar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['geral']['ven003FormBtnLimpar'] = array('name' => 'ven003FormBtnLimpar', 'type' => 'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right;'.($g['usuarioAtual']!=63 ? 'opacity: 0.8;':''), 'reject' => true, 'disabled' => $campoDisabled);
		$formArray['campos']['geral']['ven003FormDadosNota'] = array('name' => 'ven003FormDadosNota', 'type' => 'button', 'reload' => false, 'value' => 'Dados', 'align' => 'right', 'style' => 'float:right;'.($g['usuarioAtual']!=63 ? 'opacity: 0.8;':''), 'reject' => true, 'disabled' => $campoDisabled);

		$sqlT007 = mysql_query("SELECT T007_Numero_Protocolo_Nfe FROM T007 WHERE T007_Id = '{$r_acaoId}'");
		$rowT007 = mysql_fetch_assoc($sqlT007);

		// Só será mostrado o botão download XML caso a nota não tenha protocolo.
		if($rowT007['T007_Numero_Protocolo_Nfe'] <= 0){
			$campoDisabled = false;
			$comboDisabled = "";
		}else{
			$campoDisabled = true;
			$type = 'hidden';
			$comboDisabled = "onfocus='this.initialSelect = this.selectedIndex;' onchange='this.selectedIndex = this.initialSelect;'";
		}
		$formArray['campos']['geral']['ven003FormBtnXML']    = array('name' => 'ven003FormBtnXML', 'type' => $type, 'reload' => false, 'value' => 'Download XML', 'align' => 'right', 'style' => 'float:right;'.($g['usuarioAtual']!=63 ? 'opacity: 0.8;':''), 'extra' => "onclick=\"downloadXMLNaoAutorizado('{$r_acaoId}');\"", 'reject' => true, 'disabled' => $campoDisabled);
		$formArray['campos']['geral']['ven003FormBtnVars']   = array('name' => 'ven003ContentVarsLayoutDanfe', 'type' => $type, 'reload' => false, 'value' => 'Vars Layout DANFE', 'reject' => true);
		$formArray['campos']['geral']['ven003FormBtnExcluirNfeTravada']   = array('name' => 'ven003FormBtnExcluirNfeTravada', 'type' => $type, 'reload' => false, 'value' => 'Exluir NFe travada', 'reject' => true);
		$formArray['campos']['geral']['ven003FormBtnFinalizarEnvioEmail']   = array('name' => 'ven003FormBtnFinalizarEnvioEmail', 'type' => $type, 'reload' => false, 'value' => 'Finalizar envio e-mail', 'reject' => true);

		$form->definirFormularioArray($formArray);
		
		/**
		 * PARTE 3: Processamento do formulário (se foi enviado)
		 * E carregamento dos valores (se não foi enviado parametro acaoId)
		 */
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
			// Form sem erros
			} else if ($formCodigo == 2) {
				// Insert ou Update?
				if (!empty($r_acaoId)) {
					// Update
					if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $r_acaoId, $form, false, empty($rowT007['T007_Numero_Protocolo_Nfe']) ? false : true)) {
						// Fecha a janela, recarrega o parent (grid) e emite uma mensagem de alerta
						$alert = gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										{$alert}
									}, 150);
  								});
								// O FW atualiza a linha do grid automaticamente, somente recarregar se não for grid
								if ($('#{$g['divIdParent']}').find('table.grid').length === 0) {
									divRefresh('{$g['divIdParent']}', true);
								}
							} </script></div>
EOT;
					}
				} else {
					// Insert
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						// Fecha a janela, recarrega o parent (grid), emite uma mensagem de alerta e abre o registro para alteração (o que acabou de ser incluido)
						$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								// Fecha a janela, recarrega o grid e abre a alteração
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										{$alert}
									}, 150);
	  							});
								divRefresh('{$g["divIdParent"]}', true);
							} </script></div>
EOT;
					}
				}
			}
		} else {
			// Carrega os valores caso seja um update
			if (!empty($r_acaoId)) {
				$processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $r_acaoId);
				// Mostra erros de formulário uteis ao desenvolvedor
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
		$gerado = gGeraCamposForm($form, $tab, $r_acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk, false, false, false, empty($rowT007['T007_Numero_Protocolo_Nfe']) ? false : true);

		/**
		 * PARTE 5: Exibir o formulário
		 */
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');

		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#ven003FormDadosNota').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Dados Nota', '/ven/ven003/content/ven003ContentEditarDadosNota/', '&tab=geral&acaoId=' + encodeURIComponent({$r_acaoId}), [500,500]);
				});
				$('#ven003FormBtnVars').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Variáveis Danfe', '/ven/ven003/content/ven003ContentVarsLayoutDanfe/', '&tab=geral&acaoId=' + encodeURIComponent({$r_acaoId}), [500,500]);
				});
				$('#ven003FormBtnExcluirNfeTravada').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Excluir NFe travada', '/ven/ven003/outro/excluirNfeComErro/', '&tab=geral&acaoId=' + encodeURIComponent({$r_acaoId}), [250,250]);
				});
				$('#ven003FormBtnFinalizarEnvioEmail').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Finalizar envio e-mail', '/ven/ven003/outro/finalizarEnvioEmail/', '&tab=geral&acaoId=' + encodeURIComponent({$r_acaoId}), [500,500]);
				});
			} </script></div>
EOT;
	
	break;
	
	case 'ven003FormProdutos':
		 
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
		$form_tabela = 'T008';
		$form_campo_pk = 'T008_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = array('ven003FormProdutosBtnAdd', 'ven003FormProdutosBtnCln', 'ven003FormProdutosBtnNew');
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/ven/ven003/form/ven003FormProdutos/';
			$filtro = '';
            if($g['C031']['SeparaProdutoPorEmpresa']=='S'){
                  $filtro = "AND IF(D001_C004_Id > 0, D001_C004_Id=".$g['empresaAtual'].", (1=1))";
            }else{
                  $filtro = '';
            }
		$cSql_T008=mysql_query("select *
                              from T008
                        left join T007 on T007_Id=T008_T007_Id
                        left join D009 on D009_Id=T008_D009_Id
                        left join D049 on D049_Id=D009_D049_Id 
                        left join D024 on D024_Id=D049_D024_Id
                        left join D082 on D082_Id=D049_D082_Id
                        left join D005 on D005_Id=T008_D005_Id
                            where T008_Id='$r_acaoId'
							{$filtro}
                            ");
    	echo mysql_error();
    	$mT008=mysql_fetch_array($cSql_T008);
		// Campo: Procura_Produto
		$pesquisaGridExtra = <<<EOT
			left join D049 on D049_D001_Id=D001_Id
			left join D009 on D009_D049_Id=D049_Id
			left join D082 on D082_Id=D049_D082_Id
			left join D037 on D037_Id=D001_D037_Id
			where (D009_C004_Id = '{$g['empresaAtual']}')
			AND NOT EXISTS (
				SELECT 1
				FROM D009A D009A_Visibilidade
				WHERE D009A_Visibilidade.D009A_D009_Id=D009_Id
				  AND D009A_Visibilidade.D009A_Flag_Pertence_Empresa='N'
			)
			{$filtro}
			group by D009_Id
			order by D001_Codigo_Produto
EOT;
		$pesquisaGrid = array(
			'D001',																								// tabela
			'D009_Id',																							// chave primaria
			'D001_Codigo_Produto,D001_Descricao_Produto,D082_Marca,D009_Quantidade_Estoque_Liquido as Pedido',	// campos
			$pesquisaGridExtra,																					// extras sql
			'Pesquisa de Produto',																				// título da janela
			'Código,Descrição,Marca,Estoque',																	// descrição dos campos
			'campoProcuraProduto',																				// callback js
			$g['divId'],																						// divId
			(isset($r_ven003FormProdutosForm) ? $r_ven003FormProdutosForm['T008_T007_Id'] : $r_T007_Id),		// parametro extra
			array(																								// parametros para cada campo do grid
				array('colunaAlinhar' => 'center'),
				array(),
				array(),
				array('callback' => 'qtePedidos', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal;width:1%', 'callbackParameter' => array('abrirLocacao' => true, 'valorMenor' => 0)),
			)
		);
		// novo formulário
		$form = new Formulario('ven003FormProdutosForm');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'produto' => array(
					'T008_Codigo_Produto' => array('name' => 'T008_Codigo_Produto' , 'label' => 'Produto', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGrid, 'length' => array(20,30), 'reject' => true),
					'T008_Codigo_Barras' => array('name' => 'T008_Codigo_Barras', 'label' => 'Código de barras (GTIN)', 'type' => 'text', 'length' => array(20,30), 'align' => 'right', 'style' => 'float:right;'),
					'Marca' => array('name' => 'Marca', 'label' => 'Marca', 'type' => 'text', 'length' => array(20,255), 'length' => array(75,90), 'reject' => true),
					'T008_Descricao_Produto' => array('name' => 'T008_Descricao_Produto', 'label' => 'Descrição', 'type' => 'textarea', 'length' => array(2,72)),
					'T008_Quantidade' => array('name' => 'T008_Quantidade', 'label' => 'Quantidade', 'type' => 'moeda', 'casasDecimais' => '3', 'length' => array(20, 30)),
					'T008_D037_Id' => array('name' => 'T008_D037_Id', 'label' => 'Unidade', 'type' => 'select', 'align' => 'right', 'style' => 'float:right;', 'select' => 
						gGeraSelect('D037', 'D037_Id', 'D037_Descricao_Unidade', '', true)
					),
					'T008_Valor_Custo_Unitario' => array('name' => 'T008_Valor_Custo_Unitario', 'label' => 'Custo Unitário', 'type' => 'moeda', 'length' => array(20,15)),
					'T008_Valor_Preco_Sem_Desconto_Unitario' => array('name' => 'T008_Valor_Preco_Sem_Desconto_Unitario', 'label' => 'Preço Unitário', 'type' => 'moeda', 'length' => array(20, 30)),
					'T008_Peso_Unitario' => array('name' => 'T008_Peso_Unitario', 'label' => 'Peso Unitário Kg', 'type' => 'moeda', 'casasDecimais' => '3', 'length' => array(10,10)),
					//'T008_Valor_Custo_Unitario' => array('name' => 'T008_Valor_Custo_Unitario', 'label' => 'Custo Unitário', 'type' => 'moeda', 'align' => 'right', 'style' => 'float:right;', 'length' => array(20, 30)),
					'T008_Numero_Pedido_Compra' => array('name' => 'T008_Numero_Pedido_Compra', 'label' => 'Número OC', 'type' => 'text', 'length' => array(20, 120)),
					'T008_Item_Pedido_Compra' => array('name' => 'T008_Item_Pedido_Compra', 'label' => 'Item OC', 'type' => 'text', 'length' => array(20, 120), 'align' => 'right', 'style' => 'float:right;'),
                    'T008_D006_Id' => array('name' => 'T008_D006_Id', 'label' => 'CFOP', 'type' => 'select', 'select' => 
						gGeraSelect('D006', 'D006_Id', 'D006_Codigo_CFOP,D006_Descricao','WHERE IFNULL(D006_Flag_Inativo,"N")!="S" ORDER BY D006_Codigo_CFOP DESC')
					),
                    'CFOP_Classificacao_Fiscal' => array('name' => 'D005_Classificacao_Fiscal', 'value'=>$mT008['D005_Classificacao_Fiscal'], 'label' => 'NCM', 'type' => 'text', 'length' => array(15,10), 'reject'=>true, 'readonly'=>true),
					'CFOP_IPI' => array('name' => 'T008_Aliquota_IPI', 'label' => 'IPI %', 'type' => 'text','value' => '0,00', 'length' => array(15,10), 'align' => 'right', 'readonly'=>true,  'reject'=>true),
					'CFOP_ICMS' => array('name' => 'T008_Aliquota_ICMS', 'label' => 'ICMS %', 'type' => 'text','value' => '0,00', 'length' => array(15,10), 'align' => 'right', 'readonly'=>true, 'reject'=>true),
					// Campos Hidden
					'T008_T007_Id' => array('name' => 'T008_T007_Id', 'label' => '', 'type' => 'hidden', 'value' => @$r_T007_Id),
					'T008_C004_Id' => array('name' => 'T008_C004_Id', 'label' => '', 'type' => 'hidden', 'value' => $g['empresaAtual']),
					'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true),
					'T008_D009_Id' => array('name' => 'T008_D009_Id', 'label' => 'T008_D009_Id', 'type' => 'hidden'),
				),
			)
		);
		// Botão Limpar / Novo Registro
		if (!empty($acaoId)) {
			 $formArray['campos']['produto']['ven003FormProdutosBtnNew'] = array('name' => 'ven003FormProdutosBtnNew', 'type'=>'submit', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		}
		$formArray['campos']['produto']['ven003FormProdutosBtnAdd'] = array('name' => 'ven003FormProdutosBtnAdd', 'type'=>'button', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['produto']['ven003FormProdutosBtnCln'] = array('name' => 'ven003FormProdutosBtnCln', 'type'=>'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$form->definirFormularioArray($formArray);

		/**
		 * PARTE 3: Processamento do formulário (se foi efnviado)
		 * 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		 */

		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
				// Form sem erros
			} else if ($formCodigo == 2) {
				$retorno = $VEN003->incluirProduto($form,$acaoId);
				if ($retorno !== true) {
					echo gGeraAlertMsg('Erro', $retorno);	
				} else {
					echo gGeraAlertMsg('Operação OK', "Com sucesso");	
					echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								divRefresh('{$form->campoValorEnviado('divReload')}', true);
								divRefresh('{$g['divId']}', true);
							} </script></div>
EOT;
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
		$g['smarty']->display('form.tpl');

	break;
	
	
	
	/**
	* @author AndreySliwnski
	* @since 21/11/2011
	*/
	case 'ven003FormProdutoEditar':
        global $g;
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$T007_Id = (isset($_REQUEST['T007_Id'])) ? $_REQUEST['T007_Id'] : false;
		$D059_Id = isset($r_D059_Id) ? $r_D059_Id : false;

		if (isset($r_ven003FormProdutoEditar) && $r_ven003FormProdutoEditar['T008_D059_Id']) {
			$D059_Id = $r_ven003FormProdutoEditar['T008_D059_Id'];
		}

		/**
		 * PARTE 2: Definição do formulário
		 */
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = true;
		$formId = (isset($r_ven003FormProdutoEditar)) ? $r_ven003FormProdutoEditar['formId'] : $r_formId;

		$cSql_T008=mysql_query("select *
                              from T008
                        left join T007 on T007_Id=T008_T007_Id
                        left join D009 on D009_Id=T008_D009_Id
                        left join D049 on D049_Id=D009_D049_Id 
                        left join D024 on D024_Id=D049_D024_Id
                        left join D082 on D082_Id=D049_D082_Id
                        left join D005 on D005_Id=T008_D005_Id
                            where T008_Id='$r_acaoId'");
    	echo mysql_error();
    	$mT008=mysql_fetch_array($cSql_T008);

    	/**
		* --- Consultas para formar o select do campo CFOP ---
    	*/
    	$cClausula  = " select * ";
		$cClausula .= " from T007 ";
		$cClausula .= " left join D024 on D024_Id=T007_D024_Id ";
		$cClausula .= " left join D020 on D020_Id=D024_D020_Id ";
		$cClausula .= " left join D018 on D018_Id=D020_D018_Id ";
		$cClausula .= " left join T007A on T007_Id=T007A_T007_Id ";
		$cClausula .= " where T007_Id='{$T007_Id}'";
		$cSql_T007 = mysql_query($cClausula);
		//echo mysql_error();
		$mT007 = mysql_fetch_array($cSql_T007);
		$cC004=mysql_query("select * from C004 left join D020 on D020_Id=C004_D020_Id left join D018 on D018_Id=D020_D018_Id where C004_Id='{$mT007['T007_C004_Id']}'");
		$mC004=mysql_fetch_array($cC004);

        $clausulaCFOP = " where IFNULL(D006_Flag_Inativo,'N')!='S' ";
        if ($g['c020']['C020_Mostrar_Todas_CFOP_Nota']!='S')
        {
	    	if ($mT007['D024_Flag_Pessoa_Fisica_Juridica']=='I')
	        {
	        	$clausulaCFOP .= " and D006_Flag_Estado='I' ";
	        }
	        elseif ($mC004['D018_UF']==$mT007['D018_UF'])
	        {
	        	$clausulaCFOP .= " and D006_Flag_Estado='D' ";
	        }
	        elseif ($mC004['D018_UF']!=$mT007['D018_UF'])
	        {
	        	$clausulaCFOP .= " and D006_Flag_Estado='F' ";
	        }
        }
        $clausulaCFOP .= " order by D006_Codigo_CFOP ";

		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = 'T008';
		$form_campo_pk = 'T008_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = array('ven003FormProdutoEditar_btn');
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/ven/ven003/form/ven003FormProdutoEditar/';

		if($mT007['T007A_Flag_Conversao_Unidade'] != 'S'){
			$posicaoItem = '{"T008A_Quantidade_Comercial":{"top":99,"left":322.4375,"width":137,"hidden":1},"Pesquisa_Grid":{"top":7,"left":83.75,"width":378,"hidden":0},"T008A_Preco_Unitario_Comercial":{"top":122,"left":309.34375,"width":137,"hidden":1},"T008_Codigo_Barras":{"top":30,"left":66.140625,"width":88,"hidden":0},"T008_Codigo_Barras_Tributavel":{"top":30,"left":269.046875,"width":88,"hidden":0},"barraHorizontal":{"top":50,"left":16,"width":null,"hidden":1},"D082_Marca":{"top":53,"left":91.65625,"width":392,"hidden":0},"T008_Descricao_Produto":{"top":76,"left":66.265625,"width":392,"hidden":0},"T008_Quantidade":{"top":99,"left":58.984375,"width":120,"hidden":0},"T008_Peso_Bruto":{"top":168,"left":348.359375,"width":72,"hidden":1},"T008_Valor_Custo_Unitario":{"top":122,"left":53.109375,"width":120,"hidden":0},"T008_Valor_Preco_Sem_Desconto_Unitario":{"top":145,"left":45.890625,"width":120,"hidden":0},"T008_Numero_Recopi":{"top":191,"left":346.9375,"width":104,"hidden":1},"T008_D037_Id":{"top":168,"left":75.046875,"width":69,"hidden":0},"T008_Peso_Unitario":{"top":191,"left":2.65625,"width":72,"hidden":0},"T008_Numero_Pedido_Compra":{"top":214,"left":6.515625,"width":72,"hidden":0},"T008_Item_Pedido_Compra":{"top":237,"left":24.421875,"width":72,"hidden":0},"T008_Percentual_GN_GLP":{"top":237,"left":402.734375,"width":86,"hidden":1},"T008_Flag_Destino_Produto":{"top":260,"left":85.28125,"width":415,"hidden":0},"T008_D006_Id":{"top":283,"left":87.40625,"width":415,"hidden":0},"T008_Informacoes_Adicionais":{"top":306,"left":38.578125,"height":62,"width":408,"hidden":0}}';
		} else {
			$posicaoItem = '{"T008A_Quantidade_Comercial":{"top":99,"left":66.4375,"width":136,"hidden":0},"Pesquisa_Grid":{"top":7,"left":83.75,"width":378,"hidden":0},"T008A_Preco_Unitario_Comercial":{"top":145,"left":53.34375,"width":120,"hidden":0},"T008_Codigo_Barras":{"top":30,"left":66.140625,"width":88,"hidden":0},"T008_Codigo_Barras_Tributavel":{"top":30,"left":269.046875,"width":88,"hidden":0},"barraHorizontal":{"top":50,"left":16,"width":null,"hidden":1},"D082_Marca":{"top":53,"left":91.65625,"width":392,"hidden":0},"T008_Descricao_Produto":{"top":76,"left":66.265625,"width":392,"hidden":0},"T008_Quantidade":{"top":99,"left":314.984375,"width":120,"hidden":1},"T008_Peso_Bruto":{"top":168,"left":348.359375,"width":72,"hidden":1},"T008_Valor_Custo_Unitario":{"top":122,"left":53.109375,"width":120,"hidden":0},"T008_Valor_Preco_Sem_Desconto_Unitario":{"top":145,"left":301.890625,"width":120,"hidden":1},"T008_Numero_Recopi":{"top":191,"left":346.9375,"width":104,"hidden":1},"T008_D037_Id":{"top":168,"left":75.046875,"width":69,"hidden":0},"T008_Peso_Unitario":{"top":191,"left":2.65625,"width":72,"hidden":0},"T008_Numero_Pedido_Compra":{"top":214,"left":6.515625,"width":72,"hidden":0},"T008_Item_Pedido_Compra":{"top":237,"left":24.421875,"width":72,"hidden":0},"T008_Percentual_GN_GLP":{"top":237,"left":402.734375,"width":86,"hidden":1},"T008_Flag_Destino_Produto":{"top":260,"left":85.28125,"width":415,"hidden":0},"T008_D006_Id":{"top":283,"left":87.40625,"width":415,"hidden":0},"T008_Informacoes_Adicionais":{"top":306,"left":38.578125,"height":62,"width":408,"hidden":0}}';
		}

		//form editor
		$form_editor = array(
			'item' 		   => $posicaoItem,
			'ipi'  		   => '{"T008_Indicador_Escala":{"top":30,"left":28,"width":130,"hidden":0},"T008_D005_Id":{"top":7,"left":93,"width":448,"hidden":0},"T008_Aliquota_IPI":{"top":53,"left":57,"width":72,"hidden":0},"T008_Situacao_Tributaria_IPI":{"top":76,"left":12,"width":367,"hidden":0},"T008_Codigo_CEST":{"top":99,"left":61,"width":72,"hidden":0},"T008_Codigo_Enq_Legal_IPI":{"top":122,"left":9,"width":40,"hidden":0}}',
			'icms' 		   => '{"T008_Flag_ST":{"top":7,"left":140.21875,"width":96,"hidden":0},"T008_Percentual_FCP":{"top":7,"left":523.625,"width":40,"hidden":0},"T008_Codigo_Beneficio_Fiscal":{"top":30,"left":135.671875,"width":88,"hidden":0},"T008A_Codigo_Beneficio_Reducao_Base_ICMS":{"top":30,"left":378.703125,"width":72,"hidden":0},"Codigo_Origem_Mercadoria":{"top":53,"left":141.65625,"width":351,"hidden":0},"T008_D059_Id":{"top":76,"left":150.96875,"width":351,"hidden":0},"T008_Percentual_Partilha_ICMS_Origem":{"top":99,"left":134.578125,"width":40,"hidden":0},"T008_ST_ICMS_Interno":{"top":99,"left":480.21875,"width":40,"hidden":0},"T008_Percentual_Partilha_ICMS_Destino":{"top":122,"left":132.859375,"width":40,"hidden":0},"T008_ICMS_Inter_Estadual":{"top":122,"left":447.390625,"width":40,"hidden":0},"T008_Aliquota_ICMS":{"top":145,"left":213.515625,"width":40,"hidden":0},"T008_Percentual_Reducao_ICMS":{"top":145,"left":471.890625,"width":40,"hidden":0},"T008_Modalidade_ICMS":{"top":168,"left":113.265625,"width":351,"hidden":0},"T008_Aliquota_ICMS_Credito":{"top":191,"left":59.703125,"width":40,"hidden":0},"T008_Percentual_Reducao_ICMS_ST":{"top":191,"left":409.359375,"width":40,"hidden":0},"T008_Valor_ICMS_Credito":{"top":214,"left":23.1875,"width":40,"hidden":0},"T008_ST_VA":{"top":214,"left":531.203125,"width":40,"hidden":0},"T008_Modalidade_ICMS_Substituicao":{"top":237,"left":113.265625,"width":351,"hidden":0},"T008_UF_ICMS_Substituicao_Devido":{"top":260,"left":91.703125,"width":351,"hidden":0},"T008_Percentual_Base_Operacao_Propria":{"top":283,"left":124.984375,"width":104,"hidden":0},"T008_Preco_Pauta_ST":{"top":283,"left":446.921875,"width":88,"hidden":1},"T008_Valor_Base_ICMS_Substituicao_Destino":{"top":306,"left":138.875,"width":104,"hidden":0},"T008_Valor_ICMS_Substituicao_Destino":{"top":329,"left":95.421875,"width":104,"hidden":0}}',
			'pisCofins'    => '{"tituloPIS":{"top":4,"left":285,"width":null,"hidden":1},"T008_Aliquota_PIS":{"top":30,"left":222,"width":78,"hidden":0},"T008_Flag_Isento_PIS":{"top":53,"left":221,"width":85,"hidden":0},"T008_Situacao_Tributaria_PIS":{"top":76,"left":135,"width":250,"hidden":0},"tituloCOFINS":{"top":119,"left":262,"width":null,"hidden":1},"T008_Aliquota_COFINS":{"top":145,"left":200,"width":78,"hidden":0},"T008_Flag_Isento_COFINS":{"top":168,"left":221,"width":85,"hidden":0},"T008_Situacao_Tributaria_COFINS":{"top":191,"left":113,"width":250,"hidden":0}}',
			'importacao'   => '{"T008_Valor_Total_Base_Imposto_Importacao":{"top":7,"left":33,"width":103,"hidden":0},"T008_Valor_Total_Imposto_Importacao":{"top":7,"left":333,"width":103,"hidden":0},"T008_Valor_Total_Despesas_Aduaneira":{"top":30,"left":62,"width":103,"hidden":0},"T008_Valor_Total_IOF":{"top":30,"left":384,"width":103,"hidden":0},"T008_Numero_Documento_Importacao":{"top":53,"left":30,"width":103,"hidden":0},"T008_Data_Registro_Documento_Importacao":{"top":53,"left":318,"width":103,"hidden":0},"T008_Local_Desembaraco":{"top":76,"left":88,"width":376,"hidden":0},"T008_Data_Desembaraco":{"top":99,"left":91,"width":103,"hidden":0},"T008_UF_Desembaraco":{"top":99,"left":419,"width":63,"hidden":0},"T008_Codigo_Exportador":{"top":122,"left":95,"width":376,"hidden":0},"T008_Numero_Adicao":{"top":145,"left":111,"width":104,"hidden":0},"T008_Numero_Sequencia_Adicao":{"top":145,"left":327,"width":104,"hidden":0},"T008_Codigo_Fabricante":{"top":168,"left":23,"width":376,"hidden":0},"T008_Valor_Desconto_Item_Adicao":{"top":191,"left":75,"width":104,"hidden":0},"T008_Numero_Drawback_Importacao":{"top":214,"left":136,"width":104,"hidden":0},"T008_Valor_Total_AFRMM":{"top":237,"left":118,"width":104,"hidden":0},"T008_Flag_Transporte_DI":{"top":260,"left":86,"width":208,"hidden":0},"T008_Flag_Tipo_Intermedio_DI":{"top":283,"left":82,"width":208,"hidden":0},"T008_CNPJ_Importador":{"top":306,"left":101,"width":200,"hidden":0},"T008_UF_Importador":{"top":329,"left":114,"width":56,"hidden":0}}',
			'exportacao'   => '{"T008_Numero_Drawback_Exportacao":{"top":7,"left":88,"width":120,"hidden":0},"T008_Numero_Registro_Exportacao":{"top":30,"left":8,"width":120,"hidden":0},"T008_Chave_Acesso_Exportacao":{"top":76,"left":26,"width":360,"hidden":0},"T008_Quantidade_Exportada":{"top":53,"left":30,"width":120,"hidden":0}}',
			'calculos'     => '{"T008A_Valor_IBS":{"top":0,"left":0,"width":0,"hidden":1},"T008_Flag_Calculo_Manual":{"top":6.970001220703125,"left":14.905029296875,"width":58,"hidden":0},"T008_Valor_Base_ICMS_Impresso":{"top":29.993896484375,"left":121.17034912109375,"width":88.03921600000001,"hidden":0},"T008_Valor_ICMS_Substituicao_Retido":{"top":29.993896484375,"left":358.51715087890625,"width":89.03921600000001,"hidden":0},"T008A_Valor_Base_CBS_IBS":{"top":259.98773193359375,"left":378.00244140625,"width":88.03921600000001,"hidden":0},"T008_Valor_ICMS":{"top":52.987152099609375,"left":148.20770263671875,"width":88.03921600000001,"hidden":0},"T008_Valor_Base_ICMS_Substituicao_Retido":{"top":52.987152099609375,"left":326.5931396484375,"width":89.03921600000001,"hidden":0},"T008_Valor_Base_IPI":{"top":75.9957275390625,"left":134.5128173828125,"width":89.03921600000001,"hidden":0},"T008_Valor_Partilha_ICMS_Destino":{"top":75.9957275390625,"left":324.7242431640625,"width":88.03921600000001,"hidden":0},"T008_Valor_IPI":{"top":98.95834350585938,"left":161.5655517578125,"width":88.03921600000001,"hidden":0},"T008_Valor_Partilha_ICMS_Origem":{"top":98.95834350585938,"left":326.43994140625,"width":88.03921600000001,"hidden":0},"T008_Valor_Base_ICMS_Substituicao":{"top":121.99755859375,"left":57.4141845703125,"width":88.03921600000001,"hidden":0},"T008_Percentual_Partilha_ICMS":{"top":121.99755859375,"left":352.7879638671875,"width":88.03921600000001,"hidden":0},"T008_Valor_ICMS_Substituicao_Retencao":{"top":144.97549438476562,"left":84.46685791015625,"width":88.03921600000001,"hidden":0},"T008_Valor_Base_Partilha_ICMS":{"top":144.97549438476562,"left":352.61944580078125,"width":88.03921600000001,"hidden":0},"T008_Valor_Base_PIS":{"top":167.9993896484375,"left":130.9742431640625,"width":88.03921600000001,"hidden":0},"T008_Valor_Base_FCP":{"top":167.9993896484375,"left":399.26470947265625,"width":88.03921600000001,"hidden":0},"T008_Valor_PIS":{"top":190.99267578125,"left":158.02691650390625,"width":88.03921600000001,"hidden":0},"T008_Valor_FCP":{"top":190.99267578125,"left":426.3173828125,"width":88.03921600000001,"hidden":0},"T008_Valor_Base_COFINS":{"top":213.97061157226562,"left":109.2830810546875,"width":88.03921600000001,"hidden":0},"T008_Valor_Base_FCP_ST":{"top":213.97061157226562,"left":383.45587158203125,"width":88.03921600000001,"hidden":0},"T008_Valor_COFINS":{"top":236.97918701171875,"left":136.33575439453125,"width":88.03921600000001,"hidden":0},"T008_Valor_FCP_ST":{"top":236.97918701171875,"left":410.508544921875,"width":88.03921600000001,"hidden":0},"T008_Valor_Total_Despesas_Acessorias":{"top":259.98773193359375,"left":97.42645263671875,"width":88.03921600000001,"hidden":0},"T008A_Valor_CBS":{"top":282.996337890625,"left":425.94970703125,"width":88.03921600000001,"hidden":0},"T008_Valor_Total_Frete":{"top":282.996337890625,"left":149.6629638671875,"width":88.03921600000001,"hidden":0},"T008A_Valor_IBS_UF":{"top":305.9896240234375,"left":414.18499755859375,"width":88.03921600000001,"hidden":0},"T008_Valor_Total_Seguro":{"top":305.9896240234375,"left":140.01220703125,"width":88.03921600000001,"hidden":0},"T008A_Valor_IBS_Municipal":{"top":328.9981689453125,"left":402.77264404296875,"width":88.03921600000001,"hidden":0},"T008_Valor_IPI_Devolucao":{"top":328.9981689453125,"left":107.628662109375,"width":88.03921600000001,"hidden":0},"T008A_Valor_Diferimento_CBS":{"top":351.991455078125,"left":399.034912109375,"width":88.03921600000001,"hidden":0},"T008_Valor_ICMS_Oculto":{"top":351.991455078125,"left":52.52752685546875,"width":88.03921600000001,"hidden":0},"T008A_Valor_Diferimento_IBS_UF":{"top":375,"left":387.27020263671875,"width":88.03921600000001,"hidden":0},"T008_Valor_Base_ICMS_Oculto":{"top":375,"left":25.474853515625,"width":88.03921600000001,"hidden":0},"T008A_Valor_Diferimento_IBS_Municipal":{"top":397.9932861328125,"left":375.842529296875,"width":88.03921600000001,"hidden":0}}',
			'combustiveis' => '{"T008A_Codigo_Produto_ANP":{"top":7,"left":118.359375,"width":136,"hidden":0},"T008A_Descricao_Produto_ANP":{"top":30,"left":104.234375,"width":392,"hidden":0},"T008A_Percentual_GLP":{"top":53,"left":76.859375,"width":136,"hidden":0},"T008A_Percentual_GNN":{"top":76,"left":70.859375,"width":136,"hidden":0},"T008A_Percentual_GNI":{"top":99,"left":67.828125,"width":137,"hidden":0},"T008A_Valor_Partida":{"top":122,"left":141.125,"width":136,"hidden":0},"T008A_Codigo_Autorizacao_Codif":{"top":145,"left":27.25,"width":136,"hidden":0},"T008A_Quantidade_Temperatura_Ambiente":{"top":168,"left":-1.046875,"width":136,"hidden":0}}',
			'medicamentos' => '{"T008A_Codigo_Produto_ANVISA":{"top":7,"left":68.234375,"width":88,"hidden":0},"T008A_Valor_Preco_Maximo_Consumidor":{"top":30,"left":6.734375,"width":88,"hidden":0},"T008A_Numero_Lote":{"top":53,"left":101.421875,"width":88,"hidden":0},"T008A_Data_Fabricacao_Lote":{"top":76,"left":34.5625,"width":88,"hidden":0},"T008A_Data_Validade_Lote":{"top":99,"left":46.484375,"width":89,"hidden":0},"T008A_Codigo_Agregacao":{"top":145,"left":48.125,"width":88,"hidden":0},"T008A_Quantidade_Produto_Lote":{"top":122,"left":61.359375,"width":88,"hidden":0},"T008A_Motivo_Isencao_ANVISA":{"top":168,"left":63.640625,"height":108,"width":408,"hidden":0}}',
			'outros'       => '{"T008A_Ficha_Conteudo_Importacao":{"top":7,"left":112.34375,"width":296,"hidden":0},"T008A_Codigo_Beneficio_Credito_Presumido":{"top":53,"left":11.734375,"width":72,"hidden":0},"T008A_Percentual_Beneficio_Credito_Presumido":{"top":30,"left":29.125,"width":72,"hidden":0}}',

			/* [REFORMA TRIBUTÁRIA] - Gabriel Cegantini 19/09/2025
			==============================================================*/
			'reforma'	   => '{"T008A_Situacao_Tributaria_CBS_IBS":{"top":6.985321044921875,"left":106.41845703125,"width":543,"hidden":0},"T008A_Classificacao_Tributaria_CBS_IBS":{"top":29.993896484375,"left":36.67279052734375,"width":543,"hidden":0},"T008A_Aliquota_CBS":{"top":52.987152099609375,"left":139.292236328125,"width":104.03921600000001,"hidden":0},"T008A_Percentual_Reducao_CBS":{"top":52.987152099609375,"left":338.86334228515625,"width":104.03921600000001,"hidden":0},"T008A_Percentual_Diferimento_CBS":{"top":52.987152099609375,"left":551.9607543945312,"width":104.03921600000001,"hidden":0},"T008A_Aliquota_IBS_UF":{"top":75.9957275390625,"left":127.52752685546875,"width":104.03921600000001,"hidden":0},"T008A_Percentual_Reducao_IBS_UF":{"top":75.9957275390625,"left":327.0986328125,"width":103.03921600000001,"hidden":0},"T008A_Percentual_Diferimento_IBS_UF":{"top":75.9957275390625,"left":540.1961059570312,"width":104.03921600000001,"hidden":0},"T008A_Aliquota_IBS_Municipal":{"top":98.98898315429688,"left":119.0103759765625,"width":104.03921600000001,"hidden":0},"T008A_Percentual_Reducao_IBS_Municipal":{"top":98.98898315429688,"left":315.70159912109375,"width":104.03921600000001,"hidden":0},"T008A_Percentual_Diferimento_IBS_Municipal":{"top":98.98898315429688,"left":528.7836303710938,"width":104.03921600000001,"hidden":0},"T008A_Situacao_Tributaria_CBS_IBS_Regular":{"top":144.99081420898438,"left":82.03125,"width":543,"hidden":0},"T008A_Classificacao_Tributaria_CBS_IBS_Regular":{"top":167.9993896484375,"left":12.2855224609375,"width":543,"hidden":0},"T008A_Aliquota_CBS_Regular":{"top":190.99267578125,"left":114.905029296875,"width":104.03921600000001,"hidden":0},"T008A_Percentual_Reducao_CBS_Regular":{"top":190.99267578125,"left":314.46075439453125,"width":104.03921600000001,"hidden":0},"T008A_Aliquota_IBS_UF_Regular":{"top":213.98593139648438,"left":103.14031982421875,"width":104.03921600000001,"hidden":0},"T008A_Percentual_Reducao_IBS_UF_Regular":{"top":213.98593139648438,"left":302.696044921875,"width":104.03921600000001,"hidden":0},"T008A_Aliquota_IBS_Municipal_Regular":{"top":236.9945068359375,"left":94.60784912109375,"width":104.03921600000001,"hidden":0},"T008A_Percentual_Reducao_IBS_Municipal_Regular":{"top":236.9945068359375,"left":291.29901123046875,"width":104.03921600000001,"hidden":0}}',
			'referenciada' => '{"T008A_Chave_Acesso_DFe_Referenciado":{"top":6.99755859375,"left":16.593137741088867,"width":360.431372,"hidden":0},"T008A_Item_DFe_Referenciado":{"top":30,"left":24.730392456054688,"width":360.431372,"hidden":0}}',
		);

		$editorCalculos = json_decode($form_editor['calculos'], true);
		$editorCalculos['T008A_Valor_IBS_Transferido'] = array('top' => 421, 'left' => 348, 'width' => 88, 'hidden' => 0);
		$editorCalculos['T008A_Valor_CBS_Transferido'] = array('top' => 444, 'left' => 348, 'width' => 88, 'hidden' => 0);
		$editorCalculos['T008A_Competencia_Ajuste'] = array('top' => 467, 'left' => 348, 'width' => 88, 'hidden' => 0);
		$editorCalculos['T008A_Valor_IBS_Ajuste_Competencia'] = array('top' => 490, 'left' => 348, 'width' => 88, 'hidden' => 0);
		$editorCalculos['T008A_Valor_CBS_Ajuste_Competencia'] = array('top' => 513, 'left' => 348, 'width' => 88, 'hidden' => 0);
		$editorCalculos['T008A_Valor_IBS_Estornado'] = array('top' => 536, 'left' => 348, 'width' => 88, 'hidden' => 0);
		$editorCalculos['T008A_Valor_CBS_Estornado'] = array('top' => 559, 'left' => 348, 'width' => 88, 'hidden' => 0);
		$form_editor['calculos'] = json_encode($editorCalculos);

		if ($g['c004']['C004_Codigo_Regime_Tributario']=='1' && $g['c020']['C020_Versao_NFe']!='1.10'){
			//$form_editor['icms'] = '{"T008_Percentual_Partilha_ICMS_Destino":{"top":76,"left":25,"width":56,"hidden":0},"T008_Flag_ST":{"top":7,"left":78,"width":85,"hidden":0},"T008_Percentual_Partilha_ICMS_Origem":{"top":99,"left":26,"width":56,"hidden":0},"Codigo_Origem_Mercadoria":{"top":30,"left":77,"width":304,"hidden":0},"T008_Percentual_FCP":{"top":122,"left":109,"width":56,"hidden":0},"T008_D059_Id":{"top":53,"left":88,"width":304,"hidden":0}}';
		}
		$form = new Formulario('ven003FormProdutoEditar');
		$filtro = '';
            if($g['C031']['SeparaProdutoPorEmpresa']=='S'){
                  $filtro = "AND IF(D001_C004_Id > 0, D001_C004_Id=".$g['empresaAtual'].", (1=1))";
            }else{
                  $filtro = '';
            }
		// Campo: Procura_Produto
		$pesquisaGridExtra = <<<EOT
			left join D049 on D049_D001_Id=D001_Id
			left join D009 on D009_D049_Id=D049_Id
			left join D082 on D082_Id=D049_D082_Id
			left join D037 on D037_Id=D001_D037_Id
			LEFT JOIN D083 on D083_D001_Id=D001_Id AND D083_D024_Id='{$mT007['T007_D024_Id']}'
			where (D009_C004_Id = '{$g['empresaAtual']}'
			AND D001_Flag_Ativo='S')
			AND NOT EXISTS (
				SELECT 1
				FROM D009A D009A_Visibilidade
				WHERE D009A_Visibilidade.D009A_D009_Id=D009_Id
				  AND D009A_Visibilidade.D009A_Flag_Pertence_Empresa='N'
			)
			{$filtro}
			group by D009_Id
			order by D001_Codigo_Produto
EOT;
		$pesquisaGrid = array(
			'D001',																								// tabela
			'D009_Id',																							// chave primaria
			'D001_Codigo_Produto,D083_Codigo_Produto_Fornecedor,D001_Descricao_Produto,D001_Descricao_Ingles,D082_Marca,D009_Quantidade_Estoque_Liquido as Pedido',			// campos
			$pesquisaGridExtra,																					// extras sql
			'Pesquisa de Produto',																				// título da janela
			'Cód.,Cód.Cli,Descrição,Descrição Inglês,Marca,Estoque',																	// descrição dos campos
			'campoProcuraProduto',																				// callback js
			$g['divId'],																						// divId
			(isset($r_ven003FormProdutoEditar) ? $r_ven003FormProdutoEditar['T008_T007_Id'] : $r_T007_Id),		// parametro extra
			array(																								// parametros para cada campo do grid
				array('colunaAlinhar' => 'center'),
				array('colunaAlinhar' => 'center'),
				array(),
				array(),
				array(),
				array('callback' => 'qtePedidos', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal;width:1%', 'callbackParameter' => array('abrirLocacao' => true, 'valorMenor' => 0)),
			)
		);

		$descricaoCFOP  = ($g['C031']['descricaoCFOPExibida'] == 'P') ? 'D006_Descricao' : 'IF(D006_Descricao_Comercial !="",D006_Descricao_Comercial,D006_Descricao) as D006_Descricao';


		$T008=mysql_query("select *
		                    from T008
	                   left join D009 on D009_Id=T008_D009_Id
	                   left join D049 on D049_Id=D009_D049_Id
	                   left join D082 on D082_Id=D049_D082_Id
		                   where T008_Id='$acaoId'");
		$mT008=mysql_fetch_array($T008);
		/*if ($acaoId)
			$tipoCFOP='select';
		else
			$tipoCFOP='hidden';*/
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'item' => array(
					//'CampoTeste' => array('name' => 'CampoTeste', 'label' => 'CampoTeste', 'type' => 'textarea', 'length' => array(3,60), 'reject'=>true),
					//'T008_Codigo_Produto' => array('name' => 'T008_Codigo_Produto', 'label' => 'Produto', 'disabled' => true, 'length' => array(20,30)),
					'Pesquisa_Grid' => array('name' => 'Pesquisa_Grid' , 'label' => 'Produto', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGrid, 'length' => array(20,30), 'reject' => true, 'value' => $mT008['T008_Codigo_Produto'] ),
					'T008_Codigo_Barras' => array('name' => 'T008_Codigo_Barras', 'label' => 'EAN (GTIN)', 'type' => 'text', 'length' => array(20,30), 'align' => 'right', 'style' => 'float:right;'),
					'D082_Marca' => array('name' => 'D082_Marca', 'value'=>$mT008['D082_Marca'], 'label' => 'Marca', 'type' => 'text', 'length' => array(75,90), 'readonly'=>true, 'reject'=>true),
					'T008_Descricao_Produto' => array('name' => 'T008_Descricao_Produto', 'label' => 'Descrição', 'type' => 'text', 'length' => array(75,90)),
					'T008_Quantidade' => array('name' => 'T008_Quantidade', 'label' => 'Quantidade', 'type' => 'moeda', 'length' => array(20,10)),
					'T008A_Quantidade_Comercial' => array('name' => 'T008A_Quantidade_Comercial', 'label' => 'Quantidade', 'type' => 'moeda', 'length' => array(20,10)),
					'T008_Valor_Custo_Unitario' => array('name' => 'T008_Valor_Custo_Unitario', 'label' => 'Custo Unitário', 'type' => 'moeda', 'length' => array(20,15)),
					'T008_Valor_Preco_Sem_Desconto_Unitario' => array('name' => 'T008_Valor_Preco_Sem_Desconto_Unitario', 'label' => 'Preço Unitário', 'type' => 'moeda', 'length' => array(20,10)),
					'T008A_Preco_Unitario_Comercial' => array('name' => 'T008A_Preco_Unitario_Comercial', 'label' => 'Preço Unitário', 'type' => 'moeda', 'length' => array(20,10)),
					'T008_D037_Id' => array('name' => 'T008_D037_Id', 'label' => 'Unidade', 'type' => 'select', 'select' => 
						gGeraSelect('D037', 'D037_Id', 'D037_Descricao_Unidade', '', true)
					),
					'T008_Peso_Unitario' => array('name' => 'T008_Peso_Unitario', 'label' => 'Peso Unitário Líquido Kg', 'type' => 'moeda', 'casasDecimais' => '3', 'length' => array(10,10)),
					'T008_Peso_Bruto' => array('name' => 'T008_Peso_Bruto', 'label' => 'Peso Unitário Bruto Kg', 'type' => 'moeda', 'casasDecimais' => '3', 'length' => array(10,10)),
					'T008_Numero_Pedido_Compra' => array('name' => 'T008_Numero_Pedido_Compra', 'label' => 'Número Pedido compra', 'type' => 'text', 'length' => array(13,60)),
					'T008_Item_Pedido_Compra' => array('name' => 'T008_Item_Pedido_Compra', 'label' => 'Ítem pedido compra', 'type' => 'text', 'length' => array(10,10)),
					'barraHorizontal' => array('name' => 'barraHorizontal', 'type' => 'titulo', 'label' => '', 'reject' => true),
					'T008_D006_Id' => array('name' => 'T008_D006_Id', 'label' => 'CFOP', 'type' => 'select', 'select' => 
						gGeraSelect('D006', 'D006_Id', "LPAD(D006_Id[[VIRGULA]]4[[VIRGULA]]0), D006_Codigo_CFOP, {$descricaoCFOP}",'WHERE IFNULL(D006_Flag_Inativo,"N")!="S" ORDER BY D006_Codigo_CFOP',true,$mT007['T007_D006_Id'], '%1 - %2 - %3'),
					),
					//'CFOP_Classificacao_Fiscal' => array('name' => 'D005_Classificacao_Fiscal', 'value'=>$mT008['D005_Classificacao_Fiscal'], 'label' => 'NCM', 'type' => 'text', 'length' => array(10,10), 'reject'=>true, 'readonly'=>true),
					//'CFOP_IPI' => array('name' => 'CFOP_IPI', 'label' => 'IPI %', 'type' => 'text','value' => '0,00', 'length' => array(10,10), 'align' => 'right', 'readonly'=>true,  'reject'=>true),
					//'CFOP_ICMS' => array('name' => 'CFOP_ICMS', 'label' => 'ICMS %', 'type' => 'text','value' => '0,00', 'length' => array(10,10), 'align' => 'right', 'readonly'=>true, 'reject'=>true),
					//campos hidden
					'T008_Codigo_Produto' => array('name' => 'T008_Codigo_Produto' , 'label' => 'ProdutoHidden', 'type' => 'hidden'),
					'T008_Informacoes_Adicionais' => array('name' => 'T008_Informacoes_Adicionais', 'label' => 'Dados adicionais', 'type' => 'textarea', 'length' => array(15,10), 'align' => 'right'),
					'T008_T007_Id' => array('name' => 'T008_T007_Id', 'label' => 'Id Nota', 'value' => @$r_T007_Id, 'type' => 'hidden'),
					'T008_C004_Id' => array('name' => 'T008_C004_Id', 'label' => 'Id Empresa', 'value' => $g['empresaAtual'], 'type' => 'hidden'),
					'T008_D009_Id' => array('name' => 'T008_D009_Id', 'label' => 'T008_D009_Id', 'type' => 'hidden'),
					'formId' => array('name' => 'formId', 'label' => '', 'type' => 'hidden', 'value' => $formId, 'reject' => true),
					'T008_Percentual_GN_GLP' => array('name' => 'T008_Percentual_GN_GLP', 'label' => 'Perc. Mistura GLP', 'type' => 'moeda', 'casasDecimais' => '4', 'length' => array(10,10)),
					'T008_Numero_Recopi' => array('name' => 'T008_Numero_Recopi', 'label' => 'Número RECOPI', 'type' => 'text', 'length' => array(13,10)),
					'T008_Flag_Destino_Produto' => array('name' => 'T008_Flag_Destino_Produto','label'=>'Destino', 'type' => 'select', 
						'select' => array(
							array('title' => '...', 'value' => "", 'selected' => true),
							array('title' => '1 - INDUSTRIALIZAÇÃO', 'value' => "1"),
							array('title' => '2 - REVENDA', 'value' => "2"),
                            array('title' => '3 - CONSUMO', 'value' => "3"),
                            array('title' => '4 - ESPECIAL', 'value' => "4"),
                            array('title' => '5 - BONIFICAÇÃO', 'value' => "5"),
							array('title' => '6 - TROCA', 'value' => "6"),
							array('title' => '7 - DEMONSTRAÇÃO', 'value' => "7"),
							array('title' => '8 - CONSIGNAÇÃO', 'value' => "8"),
							array('title' => '9 - VENDA FUTURA', 'value' => "9"),
							array('title' => '10 - VENDA CONTA E ORDEM', 'value' => "10"),
							array('title' => '11 - VENDA PRÓ-EMPREGO', 'value' => "11"),
							array('title' => '12 - REMESSA PARA CONSERTO', 'value' => "12"),
							array('title' => '13 - REMESSA PARA INDUSTRIALIZAÇÃO', 'value' => "13"),
							array('title' => '14 - OUTRAS SAÍDAS', 'value' => "14"),
							array('title' => '15 - AMOSTRA', 'value' => "15"),
							array('title' => '16 - DOAÇÃO', 'value' => "16"),
							array('title' => '17 - VENDA DE ATIVO IMOBILIZADO', 'value' => "17"),
							array('title' => '18 - TRANSFERÊNCIA ENTRE EMPRESAS', 'value' => "18"),
							array('title' => '19 - REMESSA TRIANGULAR', 'value' => "19"),
							array('title' => '20 - REMESSA VENDA FUTURA', 'value' => "20"),
						)
					),
					'T008_Codigo_Barras_Tributavel' => array('name' => 'T008_Codigo_Barras_Tributavel', 'label' => 'EAN Tributável (GTIN Tributável)', 'type' => 'text', 'length' => array(20,30), 'align' => 'right', 'style' => 'float:right;'),
				),

				//------------ ABA IPI ---------------
				'ipi' => array(
					'T008_D005_Id' => array('name' => 'T008_D005_Id', 'label' => 'NCM', 'type' => 'select', 'select' => 
						gGeraSelect('D149', 'D005_Id', 'D005_Classificacao_Fiscal, D005_Flag_Aplicacao, D149_Aliquota_IPI', "LEFT JOIN D005 ON D149_D005_Id=D005_Id AND D149_C004_Id='{$g['empresaAtual']}' WHERE D005_Flag_Ativo='S' ORDER BY D005_Classificacao_Fiscal, D005_Observacao", true)
					),
					'T008_Aliquota_IPI' => array('name' => 'T008_Aliquota_IPI', 'label' => 'Alíquota IPI %', 'type' => 'moeda', 'length' => array(10,15)),
					'T008_Situacao_Tributaria_IPI' => array('name' => 'T008_Situacao_Tributaria_IPI', 'label' => 'Situação Tributária IPI', 'type' => 'select', 'select' => 
						gGeraSelect('D088', 'D088_Codigo_ST', 'D088_Codigo_ST,D088_Descricao', 'order by D088_Codigo_ST', true)
					),
					'T008_Codigo_CEST' => array('name' => 'T008_Codigo_CEST', 'label' => 'Código CEST', 'type' => 'text', 'length' => array(13,10)),
					'T008_Codigo_Enq_Legal_IPI' => array('name' => 'T008_Codigo_Enq_Legal_IPI', 'label' => 'Cód. Enquadramento IPI', 'type' => 'text', 'length' => array(3,3)),
					'T008_Indicador_Escala' => array('name' => 'T008_Indicador_Escala', 'label'=>'Indicador de Escala', 'type' => 'select', 'select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Relevante', 'value' => 'S'),
							array('title' => 'Não Relevante', 'value' => 'N'),
						)
					),
				),

				//------------ ABA PIS/COFINS ---------------
				'pisCofins' => array(
					'tituloPIS' => array('name' => 'tituloVendas', 'type' => 'titulo', 'label' => 'PIS', 'reject' => true),
					'T008_Aliquota_PIS' => array('name' => 'T008_Aliquota_PIS', 'label' => 'PIS %', 'type' => 'moeda', 'length' => array(10,15)),
					'T008_Flag_Isento_PIS' => array('name' => 'T008_Flag_Isento_PIS', 'label'=>'Isento', 'type' => 'select', 'extra' => 'style="width:85px;"', 'select' =>
						array(
							array('title' => 'Não', 'value'=>'N', 'selected'=>true),
							array('title' => 'Sim', 'value'=>'S'),
						)
					),
					'T008_Situacao_Tributaria_PIS' => array('name' => 'T008_Situacao_Tributaria_PIS', 'label'=>'Situação Tributária PIS', 'type' => 'select', 'extra' => 'style="width:250px;"', 'select' =>
						array(
							array('title' => '...', 'value'=>'', 'selected'=>true),
							array('value'=>'01','title'=>'01 - Operação Tributável com Alíquota Básica'),
							array('value'=>'02','title'=>'02 - Operação Tributável com Alíquota Diferenciada'),
							array('value'=>'03','title'=>'03 - Operação Tributável com Alíquota por Unidade de Medida de Produto'),
							array('value'=>'04','title'=>'04 - Operação Tributável Monofásica - Revenda a Alíquota Zero'),
							array('value'=>'05','title'=>'05 - Operação Tributável por Substituição Tributária'),
							array('value'=>'06','title'=>'06 - Operação Tributável a Alíquota Zero'),
							array('value'=>'07','title'=>'07 - Operação Isenta da Contribuição'),
							array('value'=>'08','title'=>'08 - Operação sem Incidência da Contribuição'),
							array('value'=>'09','title'=>'09 - Operação com Suspensão da Contribuição'),
							array('value'=>'49','title'=>'49 - Outras Operações de Saída'),
							array('value'=>'50','title'=>'50 - Operação com Direito a Crédito - Vinculada Exclusivamente a Receita Tributada no Mercado Interno'),
							array('value'=>'51','title'=>'51 - Operação com Direito a Crédito – Vinculada Exclusivamente a Receita Não Tributada no Mercado Interno'),
							array('value'=>'52','title'=>'52 - Operação com Direito a Crédito - Vinculada Exclusivamente a Receita de Exportação'),
							array('value'=>'53','title'=>'53 - Operação com Direito a Crédito - Vinculada a Receitas Tributadas e Não-Tributadas no Mercado Interno'),
							array('value'=>'54','title'=>'54 - Operação com Direito a Crédito - Vinculada a Receitas Tributadas no Mercado Interno e de Exportação'),
							array('value'=>'55','title'=>'55 - Operação com Direito a Crédito - Vinculada a Receitas Não-Tributadas no Mercado Interno e de Exportação'),
							array('value'=>'56','title'=>'56 - Operação com Direito a Crédito - Vinculada a Receitas Tributadas e Não-Tributadas no Mercado Interno, e de Exportação'),
							array('value'=>'60','title'=>'60 - Crédito Presumido - Operação de Aquisição Vinculada Exclusivamente a Receita Tributada no Mercado Interno'),
							array('value'=>'61','title'=>'61 - Crédito Presumido - Operação de Aquisição Vinculada Exclusivamente a Receita Não-Tributada no Mercado Interno'),
							array('value'=>'62','title'=>'62 - Crédito Presumido - Operação de Aquisição Vinculada Exclusivamente a Receita de Exportação'),
							array('value'=>'63','title'=>'63 - Crédito Presumido - Operação de Aquisição Vinculada a Receitas Tributadas e Não-Tributadas no Mercado Interno'),
							array('value'=>'64','title'=>'64 - Crédito Presumido - Operação de Aquisição Vinculada a Receitas Tributadas no Mercado Interno e de Exportação'),
							array('value'=>'65','title'=>'65 - Crédito Presumido - Operação de Aquisição Vinculada a Receitas Não-Tributadas no Mercado Interno e de Exportação'),
							array('value'=>'66','title'=>'66 - Crédito Presumido - Operação de Aquisição Vinculada a Receitas Tributadas e Não-Tributadas no Mercado Interno, e de Exportação'),
							array('value'=>'67','title'=>'67 - Crédito Presumido - Outras Operações'),
							array('value'=>'70','title'=>'70 - Operação de Aquisição sem Direito a Crédito'),
							array('value'=>'71','title'=>'71 - Operação de Aquisição com Isenção'),
							array('value'=>'72','title'=>'72 - Operação de Aquisição com Suspensão'),
							array('value'=>'73','title'=>'73 - Operação de Aquisição a Alíquota Zero'),
							array('value'=>'74','title'=>'74 - Operação de Aquisição sem Incidência da Contribuição'),
							array('value'=>'75','title'=>'75 - Operação de Aquisição por Substituição Tributária'),
							array('value'=>'98','title'=>'98 - Outras Operações de Entrada'),
							array('value'=>'99','title'=>'99 - Outras Operações'),
						)
					),
					'tituloCOFINS' => array('name' => 'tituloVendas', 'type' => 'titulo', 'label' => 'COFINS', 'reject' => true),
					'T008_Aliquota_COFINS' => array('name' => 'T008_Aliquota_COFINS', 'label' => 'COFINS %', 'type' => 'moeda', 'length' => array(10,15)),
					'T008_Flag_Isento_COFINS' => array('name' => 'T008_Flag_Isento_COFINS', 'label'=>'Isento', 'type' => 'select', 'extra' => 'style="width:85px;"', 'select' =>
						array(
							array('title' => 'Não', 'value'=>'N', 'selected'=>true),
							array('title' => 'Sim', 'value'=>'S'),
						)
					),
					'T008_Situacao_Tributaria_COFINS' => array('name' => 'T008_Situacao_Tributaria_COFINS', 'label'=>'Situação Tributária COFINS', 'type' => 'select', 'extra' => 'style="width:250px;"', 'select' =>
						array(
							array('title' => '...', 'value'=>'', 'selected'=>true),
							array('value'=>'01','title'=>'01 - Operação Tributável com Alíquota Básica'),
							array('value'=>'02','title'=>'02 - Operação Tributável com Alíquota Diferenciada'),
							array('value'=>'03','title'=>'03 - Operação Tributável com Alíquota por Unidade de Medida de Produto'),
							array('value'=>'04','title'=>'04 - Operação Tributável Monofásica - Revenda a Alíquota Zero'),
							array('value'=>'05','title'=>'05 - Operação Tributável por Substituição Tributária'),
							array('value'=>'06','title'=>'06 - Operação Tributável a Alíquota Zero'),
							array('value'=>'07','title'=>'07 - Operação Isenta da Contribuição'),
							array('value'=>'08','title'=>'08 - Operação sem Incidência da Contribuição'),
							array('value'=>'09','title'=>'09 - Operação com Suspensão da Contribuição'),
							array('value'=>'49','title'=>'49 - Outras Operações de Saída'),
							array('value'=>'50','title'=>'50 - Operação com Direito a Crédito - Vinculada Exclusivamente a Receita Tributada no Mercado Interno'),
							array('value'=>'51','title'=>'51 - Operação com Direito a Crédito – Vinculada Exclusivamente a Receita Não Tributada no Mercado Interno'),
							array('value'=>'52','title'=>'52 - Operação com Direito a Crédito - Vinculada Exclusivamente a Receita de Exportação'),
							array('value'=>'53','title'=>'53 - Operação com Direito a Crédito - Vinculada a Receitas Tributadas e Não-Tributadas no Mercado Interno'),
							array('value'=>'54','title'=>'54 - Operação com Direito a Crédito - Vinculada a Receitas Tributadas no Mercado Interno e de Exportação'),
							array('value'=>'55','title'=>'55 - Operação com Direito a Crédito - Vinculada a Receitas Não-Tributadas no Mercado Interno e de Exportação'),
							array('value'=>'56','title'=>'56 - Operação com Direito a Crédito - Vinculada a Receitas Tributadas e Não-Tributadas no Mercado Interno, e de Exportação'),
							array('value'=>'60','title'=>'60 - Crédito Presumido - Operação de Aquisição Vinculada Exclusivamente a Receita Tributada no Mercado Interno'),
							array('value'=>'61','title'=>'61 - Crédito Presumido - Operação de Aquisição Vinculada Exclusivamente a Receita Não-Tributada no Mercado Interno'),
							array('value'=>'62','title'=>'62 - Crédito Presumido - Operação de Aquisição Vinculada Exclusivamente a Receita de Exportação'),
							array('value'=>'63','title'=>'63 - Crédito Presumido - Operação de Aquisição Vinculada a Receitas Tributadas e Não-Tributadas no Mercado Interno'),
							array('value'=>'64','title'=>'64 - Crédito Presumido - Operação de Aquisição Vinculada a Receitas Tributadas no Mercado Interno e de Exportação'),
							array('value'=>'65','title'=>'65 - Crédito Presumido - Operação de Aquisição Vinculada a Receitas Não-Tributadas no Mercado Interno e de Exportação'),
							array('value'=>'66','title'=>'66 - Crédito Presumido - Operação de Aquisição Vinculada a Receitas Tributadas e Não-Tributadas no Mercado Interno, e de Exportação'),
							array('value'=>'67','title'=>'67 - Crédito Presumido - Outras Operações'),
							array('value'=>'70','title'=>'70 - Operação de Aquisição sem Direito a Crédito'),
							array('value'=>'71','title'=>'71 - Operação de Aquisição com Isenção'),
							array('value'=>'72','title'=>'72 - Operação de Aquisição com Suspensão'),
							array('value'=>'73','title'=>'73 - Operação de Aquisição a Alíquota Zero'),
							array('value'=>'74','title'=>'74 - Operação de Aquisição sem Incidência da Contribuição'),
							array('value'=>'75','title'=>'75 - Operação de Aquisição por Substituição Tributária'),
							array('value'=>'98','title'=>'98 - Outras Operações de Entrada'),
							array('value'=>'99','title'=>'99 - Outras Operações'),
						)
					)
				),

				// --------------- ABA IMPORTAÇÃO ----------------
				'importacao' => array(
					'T008_Valor_Total_Base_Imposto_Importacao' => array('name' => 'T008_Valor_Total_Base_Imposto_Importacao', 'label' => 'Valor Base Imposto Importação', 'type' => 'moeda', 'length' => array(15,15)),
					'T008_Valor_Total_Imposto_Importacao' => array('name' => 'T008_Valor_Total_Imposto_Importacao', 'label' => 'Valor Imposto Importação', 'type' => 'moeda', 'length' => array(15,15), 'align' => 'right', 'style' => 'float:right;'),
					'T008_Valor_Total_Despesas_Aduaneira' => array('name' => 'T008_Valor_Total_Despesas_Aduaneira', 'label' => 'Valor Despesa Aduaneira', 'type' => 'moeda', 'length' => array(15,15)),
					'T008_Valor_Total_IOF' => array('name' => 'T008_Valor_Total_IOF', 'label' => 'Valor Total IOF', 'type' => 'moeda', 'length' => array(15,15), 'align' => 'right', 'style' => 'float:right;'),
					'T008_Numero_Documento_Importacao' => array('name' => 'T008_Numero_Documento_Importacao', 'label' => 'Número Documento Importação', 'type' => 'text', 'length' => array(15,15)),
					'T008_Data_Registro_Documento_Importacao' => array('name' => 'T008_Data_Registro_Documento_Importacao', 'label' => 'Data Documento Importação', 'type' => 'data', 'length' => array(15,15)),
					'T008_Local_Desembaraco' => array('name' => 'T008_Local_Desembaraco', 'label' => 'Local Desembaraço', 'type' => 'text', 'length' => array(70,90)),
					'T008_Data_Desembaraco' => array('name' => 'T008_Data_Desembaraco', 'label' => 'Data Desembaraço', 'type' => 'data', 'length' => array(15,15)),
					'T008_UF_Desembaraco' => array('name' => 'T008_UF_Desembaraco', 'label' => 'UF Desembaraço', 'type' => 'select', 'align' => 'right', 'style' => 'float:right;', 'select' => 
						gGeraSelect('D018', 'D018_UF', 'D018_UF', 'order by D018_UF,D018_Estado', true)
					),
					'T008_Codigo_Exportador' => array('name' => 'T008_Codigo_Exportador', 'label' => 'Código Exportador', 'type' => 'text', 'length' => array(70,90)),
					'T008_Numero_Adicao' => array('name' => 'T008_Numero_Adicao', 'label' => 'Número Adição', 'type' => 'text', 'length' => array(10,20)),
					'T008_Numero_Sequencia_Adicao' => array('name' => 'T008_Numero_Sequencia_Adicao', 'label' => 'Número Sequencial Adição', 'type' => 'text', 'length' => array(10,20), 'align' => 'right', 'style' => 'float:right;'),
					'T008_Codigo_Fabricante' => array('name' => 'T008_Codigo_Fabricante', 'label' => 'Código do Fabricante Estrangeiro', 'type' => 'text', 'length' => array(70,90)),
					'T008_Valor_Desconto_Item_Adicao' => array('name' => 'T008_Valor_Desconto_Item_Adicao', 'label' => 'Valor Desconto Adição', 'type' => 'moeda', 'length' => array(15,20)),

					'T008_Flag_Transporte_DI' => array('name' => 'T008_Flag_Transporte_DI', 'label'=>'Forma de transporte', 'type' => 'select', 'select' =>
						array(
							array('title' => '...', 'value'=>'', 'selected'=>true),
							array('title' => 'Marítima', 'value' => '1'),
							array('title' => 'Fluvial', 'value' => '2'),
							array('title' => 'Lacustre', 'value' => '3'),
							array('title' => 'Aérea', 'value' => '4'),
							array('title' => 'Postal', 'value' => '5'),
							array('title' => 'Ferroviária', 'value' => '6'),
							array('title' => 'Rodoviária', 'value' => '7'),
							array('title' => 'Conduto / Rede Transmissão', 'value' => '8'),
							array('title' => 'Meios Próprios', 'value' => '9'),
							array('title' => 'Entrada / Saída ficta', 'value' => '10'),
							array('title' => 'Courier', 'value' => '11'),
							array('title' => 'Handcarry', 'value' => '12'),
						)
					),
					'T008_Valor_Total_AFRMM' => array('name' => 'T008_Valor_Total_AFRMM', 'label' => 'Valor AFRMM', 'type' => 'moeda'),
					'T008_Flag_Tipo_Intermedio_DI' => array('name' => 'T008_Flag_Tipo_Intermedio_DI', 'label' => 'Forma de importação', 'type' => 'select', 'select' =>
						array(
							array('title' => '...', 'value'=>'', 'selected'=>true),
							array('title' => 'Importação por conta própria', 'value' => '1'),
							array('title' => 'Importação por conta e ordem', 'value' => '2'),
							array('title' => 'Importação por encomenda', 'value' => '3'),
						)
					),
					'T008_CNPJ_Importador' => array('name' => 'T008_CNPJ_Importador', 'label' => 'CNPJ Importador', 'type' => 'cnpj'),
					'T008_UF_Importador' => array('name' => 'T008_UF_Importador', 'label' => 'UF Importador', 'type' => 'select', 
						'select' => gGeraSelect('D018', 'D018_UF', 'D018_UF', 'order by D018_UF,D018_Estado', true)
					),
					'T008_Numero_Drawback_Importacao' => array('name' => 'T008_Numero_Drawback_Importacao', 'label' => 'Drawback', 'type' => 'text'),
				),
				// --------------- ABA EXPORTAÇÃO -----------------
				'exportacao' => array(
					'T008_Numero_Drawback_Exportacao' => array('name' => 'T008_Numero_Drawback_Exportacao', 'label' => 'Drawback', 'type' => 'texto'),
					'T008_Numero_Registro_Exportacao' => array('name' => 'T008_Numero_Registro_Exportacao', 'label' => 'N° Registro de Exportação', 'type' => 'texto'),
					'T008_Chave_Acesso_Exportacao' => array('name' => 'T008_Chave_Acesso_Exportacao', 'label' => 'Chave acesso NFe <br>recebida p/ exportação', 'type' => 'texto'),
					'T008_Quantidade_Exportada' => array('name' => 'T008_Quantidade_Exportada', 'label' => 'Quantidade exportada', 'type' => 'moeda'),
				),
				// --------------- ABA CALCULOS ----------------
				'calculos' => array(
					'T008_Flag_Calculo_Manual' => array('name' => 'T008_Flag_Calculo_Manual', 'label' => 'FORNECER VALORES MANUALMENTE', 'type' => 'select',
						'select' => array(
							array('title' => '...', 'value' => '','selected' => true),
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N'),
						)
					),
					//'XT008_Aliquota_ICMS' => array('name' => 'XT008_Aliquota_ICMS', 'label' => 'Alíquota ICMS', 'type' => 'moeda','reject' => true, 'length' => array(15,15)),
					'T008_Valor_Base_ICMS_Impresso' => array('name' => 'T008_Valor_Base_ICMS_Impresso', 'label' => 'Valor Base ICMS', 'type' => 'moeda', 'length' => array(15,15)),
					'T008_Valor_ICMS' => array('name' => 'T008_Valor_ICMS', 'label' => 'Valor ICMS', 'type' => 'moeda', 'length' => array(15,15), 'align' => 'right', 'style' => 'float:right;'),
					'T008_Valor_Base_IPI' => array('name' => 'T008_Valor_Base_IPI', 'label' => 'Valor Base IPI', 'type' => 'moeda', 'length' => array(15,15)),
					'T008_Valor_IPI' => array('name' => 'T008_Valor_IPI', 'label' => 'Valor IPI', 'type' => 'moeda', 'length' => array(15,15), 'align' => 'right', 'style' => 'float:right;'),
					'T008_Valor_Base_ICMS_Substituicao' => array('name' => 'T008_Valor_Base_ICMS_Substituicao', 'label' => 'Valor Base ICMS Substituição', 'type' => 'moeda', 'length' => array(15,15)),
					'T008_Valor_ICMS_Substituicao_Retencao' => array('name' => 'T008_Valor_ICMS_Substituicao_Retencao', 'label' => 'Valor ICMS Substituição', 'type' => 'moeda', 'length' => array(15,15), 'align' => 'right', 'style' => 'float:right;'),
					'T008_Valor_Base_PIS' => array('name' => 'T008_Valor_Base_PIS', 'label' => 'Valor Base PIS', 'type' => 'moeda', 'length' => array(15,15)),
					'T008A_Valor_Base_PIS_Retido' => array('name' => 'T008A_Valor_Base_PIS_Retido', 'label' => 'Valor Base PIS Retido', 'type' => 'moeda', 'length' => array(15,15)),
					'T008A_Valor_PIS_Retido' => array('name' => 'T008A_Valor_PIS_Retido', 'label' => 'Valor PIS Retido', 'type' => 'moeda', 'length' => array(15,15)),
					'T008_Valor_PIS' => array('name' => 'T008_Valor_PIS', 'label' => 'Valor PIS', 'type' => 'moeda', 'length' => array(15,15), 'align' => 'right', 'style' => 'float:right;'),
					'T008_Valor_Base_COFINS' => array('name' => 'T008_Valor_Base_COFINS', 'label' => 'Valor Base COFINS', 'type' => 'moeda', 'length' => array(15,15)),
					'T008A_Valor_Base_COFINS_Retido' => array('name' => 'T008A_Valor_Base_COFINS_Retido', 'label' => 'Valor Base COFINS Retido', 'type' => 'moeda', 'length' => array(15,15)),
					'T008A_Valor_COFINS_Retido' => array('name' => 'T008A_Valor_COFINS_Retido', 'label' => 'Valor COFINS Retido', 'type' => 'moeda', 'length' => array(15,15)),
					'T008_Valor_COFINS' => array('name' => 'T008_Valor_COFINS', 'label' => 'Valor COFINS', 'type' => 'moeda', 'length' => array(15,15), 'align' => 'right', 'style' => 'float:right;'),
					'T008_Valor_Total_Despesas_Acessorias' => array('name' => 'T008_Valor_Total_Despesas_Acessorias', 'label' => 'Despesas Acessórias', 'type' => 'moeda', 'length' => array(15,15), 'align' => 'right', 'style' => 'float:right;'),
					'T008_Valor_Total_Frete' => array('name' => 'T008_Valor_Total_Frete', 'label' => 'Valor Frete', 'type' => 'moeda', 'length' => array(15,15), 'align' => 'right', 'style' => 'float:right;'),
					'T008_Valor_Total_Seguro' => array('name' => 'T008_Valor_Total_Seguro', 'label' => 'Valor Seguro', 'type' => 'moeda', 'length' => array(15,15), 'align' => 'right', 'style' => 'float:right;'),
					'T008_Valor_IPI_Devolucao' => array('name' => 'T008_Valor_IPI_Devolucao', 'label' => 'Valor IPI Devolução', 'type' => 'moeda', 'length' => array(15,15), 'align' => 'right', 'style' => 'float:right;'),
					'T008_Valor_Base_ICMS_Oculto' => array('name' => 'T008_Valor_Base_ICMS_Oculto', 'label' => 'Valor Base ICMS cheio (diferimento)', 'type' => 'moeda', 'length' => array(15,15), 'align' => 'right', 'style' => 'float:right;'),
					'T008_Valor_ICMS_Oculto' => array('name' => 'T008_Valor_ICMS_Oculto', 'label' => 'Valor ICMS cheio (diferimento)', 'type' => 'moeda', 'length' => array(15,15), 'align' => 'right', 'style' => 'float:right;'),
					'T008_Valor_ICMS_Substituicao_Retido' => array('name' => 'T008_Valor_ICMS_Substituicao_Retido', 'label' => 'Valor do ICMS ST retido', 'type' => 'moeda', 'length' => array(10,15), 'align' => 'right', 'style' => 'float:right;'),
					'T008_Valor_Base_ICMS_Substituicao_Retido' => array('name' => 'T008_Valor_Base_ICMS_Substituicao_Retido', 'label' => 'Valor do BC do ICMS ST retido', 'type' => 'moeda', 'length' => array(10,15), 'align' => 'right', 'style' => 'float:right;'),	
					'T008_Percentual_Partilha_ICMS' => array('name' => 'T008_Percentual_Partilha_ICMS', 'label' => 'Percentual Partilha ICMS', 'type' => 'moeda', 'length' => array(10,15), 'align' => 'right', 'style' => 'float:right;'),
					'T008_Valor_Partilha_ICMS_Destino' => array('name' => 'T008_Valor_Partilha_ICMS_Destino', 'label' => 'Valor da Partilha ICMS Destino', 'type' => 'moeda', 'length' => array(10,15), 'align' => 'right', 'style' => 'float:right;'),
					'T008_Valor_Partilha_ICMS_Origem' => array('name' => 'T008_Valor_Partilha_ICMS_Origem', 'label' => 'Valor da Partilha ICMS Origem', 'type' => 'moeda', 'length' => array(10,15), 'align' => 'right', 'style' => 'float:right;'),
					'T008_Valor_Base_Partilha_ICMS' => array('name' => 'T008_Valor_Base_Partilha_ICMS', 'label' => 'Valor Base Partilha ICMS', 'type' => 'moeda', 'length' => array(10,15), 'align' => 'right', 'style' => 'float:right;'),
					'T008_Valor_Base_FCP' => array('name' => 'T008_Valor_Base_FCP', 'label' => 'Valor Base FCP', 'type' => 'moeda', 'length' => array(10,15), 'align' => 'right', 'style' => 'float:right;'),
					'T008_Valor_FCP' => array('name' => 'T008_Valor_FCP', 'label' => 'Valor FCP', 'type' => 'moeda', 'length' => array(10,15), 'align' => 'right', 'style' => 'float:right;'),
					'T008_Valor_Base_FCP_ST' => array('name' => 'T008_Valor_Base_FCP_ST', 'label' => 'Valor Base FCP ST', 'type' => 'moeda', 'length' => array(10,15), 'align' => 'right', 'style' => 'float:right;'),
					'T008_Valor_FCP_ST' => array('name' => 'T008_Valor_FCP_ST', 'label' => 'Valor FCP ST', 'type' => 'moeda', 'length' => array(10,15), 'align' => 'right', 'style' => 'float:right;'),
					/* [REFORMA TRIBUTÁRIA] Gabriel Cegantini 04/12/2025 
					====================================================================*/
					'T008A_Valor_CBS' => array('name' => 'T008A_Valor_CBS', 'label' => 'Valor CBS', 'type' => 'moeda', 'length' => array(10,15), 'align' => 'right', 'style' => 'float:right;'),
					'T008A_Valor_IBS_UF' => array('name' => 'T008A_Valor_IBS_UF', 'label' => 'Valor IBS UF', 'type' => 'moeda', 'length' => array(10,15), 'align' => 'right', 'style' => 'float:right;'),
					'T008A_Valor_IBS_Municipal' => array('name' => 'T008A_Valor_IBS_Municipal', 'label' => 'Valor IBS Mun.', 'type' => 'moeda', 'length' => array(10,15), 'align' => 'right', 'style' => 'float:right;'),
					'T008A_Valor_Diferimento_CBS' => array('name' => 'T008A_Valor_Diferimento_CBS', 'label' => 'Valor CBS (Dif.)', 'type' => 'moeda', 'length' => array(10,15), 'align' => 'right', 'style' => 'float:right;'),
					'T008A_Valor_Diferimento_IBS_UF' => array('name' => 'T008A_Valor_Diferimento_IBS_UF', 'label' => 'Valor IBS UF (Dif.)', 'type' => 'moeda', 'length' => array(10,15), 'align' => 'right', 'style' => 'float:right;'),
					'T008A_Valor_Diferimento_IBS_Municipal' => array('name' => 'T008A_Valor_Diferimento_IBS_Municipal', 'label' => 'Valor IBS Mun. (Dif.)', 'type' => 'moeda', 'length' => array(10,15), 'align' => 'right', 'style' => 'float:right;'),
					'T008A_Valor_Base_CBS_IBS' => array('name' => 'T008A_Valor_Base_CBS_IBS', 'label' => 'Valor Base CBS/IBS', 'type' => 'moeda', 'length' => array(10,15), 'align' => 'right', 'style' => 'float:right;'),
					'T008A_Valor_IBS' => array('name' => 'T008A_Valor_IBS', 'label' => 'Valor IBS', 'type' => 'moeda', 'length' => array(10,15), 'align' => 'right', 'style' => 'float:right;'),
					'T008A_Valor_IBS_Transferido' => array('name' => 'T008A_Valor_IBS_Transferido', 'label' => 'Valor IBS transferido', 'type' => 'moeda', 'length' => array(10,15), 'align' => 'right', 'style' => 'float:right;'),
					'T008A_Valor_CBS_Transferido' => array('name' => 'T008A_Valor_CBS_Transferido', 'label' => 'Valor CBS transferido', 'type' => 'moeda', 'length' => array(10,15), 'align' => 'right', 'style' => 'float:right;'),
					'T008A_Competencia_Ajuste' => array('name' => 'T008A_Competencia_Ajuste', 'label' => 'Competência ajuste (MM/AAAA)', 'type' => 'text', 'length' => array(7,7), 'align' => 'right', 'style' => 'float:right;', 'extra' => 'placeholder="MM/AAAA"'),
					'T008A_Valor_IBS_Ajuste_Competencia' => array('name' => 'T008A_Valor_IBS_Ajuste_Competencia', 'label' => 'Valor IBS ajuste competência', 'type' => 'moeda', 'length' => array(10,15), 'align' => 'right', 'style' => 'float:right;'),
					'T008A_Valor_CBS_Ajuste_Competencia' => array('name' => 'T008A_Valor_CBS_Ajuste_Competencia', 'label' => 'Valor CBS ajuste competência', 'type' => 'moeda', 'length' => array(10,15), 'align' => 'right', 'style' => 'float:right;'),
					'T008A_Valor_IBS_Estornado' => array('name' => 'T008A_Valor_IBS_Estornado', 'label' => 'Valor IBS estornado', 'type' => 'moeda', 'length' => array(10,15), 'align' => 'right', 'style' => 'float:right;'),
					'T008A_Valor_CBS_Estornado' => array('name' => 'T008A_Valor_CBS_Estornado', 'label' => 'Valor CBS estornado', 'type' => 'moeda', 'length' => array(10,15), 'align' => 'right', 'style' => 'float:right;'),
					/*====================================================================*/
				),
				// --------------- ABA COMBUSTIVEIS ----------------
				'combustiveis' => array(
					'T008A_Codigo_Produto_ANP' => array('name' => 'T008A_Codigo_Produto_ANP', 'label' => 'Codigo produto ANP', 'type' => 'text', 'length' => array(15,20), 'align' => 'right', 'style' => 'float:right;'),
					'T008A_Descricao_Produto_ANP' => array('name' => 'T008A_Descricao_Produto_ANP', 'label' => 'Descrição produto ANP', 'type' => 'text'),
					'T008A_Percentual_GLP' => array('name' => 'T008A_Percentual_GLP', 'label' => '% GLP (derivado do petróleo)', 'type' => 'moeda','length' => array(10,15), 'align' => 'right', 'style' => 'float:right;','casasDecimais' => 4),
					'T008A_Percentual_GNN' => array('name' => 'T008A_Percentual_GNN', 'label' => '% GNN (Gás Natural Nacional)', 'type' => 'moeda','length' => array(10,15), 'align' => 'right', 'style' => 'float:right;','casasDecimais' => 4),
					'T008A_Percentual_GNI' => array('name' => 'T008A_Percentual_GNI', 'label' => '% GNI (Gás Natural Importado)', 'type' => 'moeda','length' => array(10,15), 'align' => 'right', 'style' => 'float:right;','casasDecimais' => 4),
					'T008A_Valor_Partida' => array('name' => 'T008A_Valor_Partida', 'label' => 'Valor de partida', 'type' => 'moeda','length' => array(10,15), 'align' => 'right', 'style' => 'float:right;'),
					'T008A_Codigo_Autorizacao_Codif' => array('name' => 'T008A_Codigo_Autorizacao_Codif', 'label' => 'Código de autorização (registro CODIF)', 'type' => 'text'),
					'T008A_Quantidade_Temperatura_Ambiente' => array('name' => 'T008A_Quantidade_Temperatura_Ambiente', 'label' => 'Quantidade faturada à temperatura ambiente', 'type' => 'moeda','length' => array(10,15), 'align' => 'right', 'style' => 'float:right;','casasDecimais' => 4)
				),
				// --------------- ABA MEDICAMENTOS ----------------
				'medicamentos' => array(
					'T008A_Numero_Lote' => array('name' => 'T008A_Numero_Lote', 'label' => 'Nº Lote', 'type' => 'text', 'length' => array(15,20), 'align' => 'right', 'style' => 'float:right;'),
					'T008A_Quantidade_Produto_Lote' => array('name' => 'T008A_Quantidade_Produto_Lote', 'label' => 'Quantidade lote', 'type' => 'moeda','length' => array(10,15), 'align' => 'right', 'style' => 'float:right;','casasDecimais' => 3),
					'T008A_Data_Fabricacao_Lote' => array('name' => 'T008A_Data_Fabricacao_Lote', 'label' => 'Data Fabricação Lote', 'type' => 'data'),
					'T008A_Data_Validade_Lote' => array('name' => 'T008A_Data_Validade_Lote', 'label' => 'Data Validade Lote', 'type' => 'data'),
					'T008A_Codigo_Agregacao' => array('name' => 'T008A_Codigo_Agregacao', 'label' => 'Código Agragação', 'type' => 'text', 'length' => array(1,20)),
					'T008A_Codigo_Produto_ANVISA' => array('name' => 'T008A_Codigo_Produto_ANVISA', 'label' => 'Código Anvisa', 'type' => 'text','length' => array(1,20)),
					'T008A_Motivo_Isencao_ANVISA' => array('name' => 'T008A_Motivo_Isencao_ANVISA', 'label' => 'Motivo Isenção', 'type' => 'textarea'),
					'T008A_Valor_Preco_Maximo_Consumidor' => array('name' => 'T008A_Valor_Preco_Maximo_Consumidor', 'label' => 'Preço máximo consumidor','type' => 'moeda','length' => array(10,15), 'align' => 'right', 'style' => 'float:right;','casasDecimais' => 2),
				),
				// --------------- ABA OUTRAS INFORMAÇÕES ----------------
				'outros' => array(
					'T008A_Ficha_Conteudo_Importacao' => array('name' => 'T008A_Ficha_Conteudo_Importacao', 'label' => 'Número FCI', 'type' => 'text'),
					/* Campos para NT 2019.001 - Criação e Atualização de Regras de Validação
					Versão 1.64 – Setembro de 2024, por solicitação da Smartness
					Gabriel Cegantini - 02/10/2025
					================================================================================ */
					'T008A_Codigo_Beneficio_Credito_Presumido' => array('name' => 'T008A_Codigo_Beneficio_Credito_Presumido', 'label'=>'Cód. Benefício Fiscal Cred. Pres.', 'type' => 'texto','length' => array(10,10), 'uppercase' => false),
					'T008A_Percentual_Beneficio_Credito_Presumido' => array('name' => 'T008A_Percentual_Beneficio_Credito_Presumido', 'label'=>'% Benefício Fiscal Cred. Pres', 'type' => 'moeda', 'casasDecimais' => '2', 'length' => array(5,5)),
					/* ================================================================================ */
				),
			)
		);

		// Campos definidos na aba ICMS
		$formArray['campos']['icms']['T008_Flag_ST'] = array('name' => 'T008_Flag_ST', 'label'=>'Substituição Tributária', 'type' => 'select', 'select' =>
			array(
				array('title' => 'Sim', 'value'=>'S'),
				array('title' => 'Não', 'value'=>'N', 'selected'=>true)
			)
		);
		$formArray['campos']['icms']['Codigo_Origem_Mercadoria'] = array('name' => 'Codigo_Origem_Mercadoria', 'type'=>'select', 'label'=>'Origem da Mercadoria', 'reject' => true, 'select' =>
			array(
				array('title' => '...', 'value'=>''),
				array('title' => '0 - Nacional - exceto as indicadas nos códigos 3 a 5', 'value'=>'0'),
				array('title' => '1 - Estrangeira - Importação direta, exceto a indicada no código 6', 'value' =>'1'),
				array('title' => '2 - Estrangeira - Adquirida no mercado interno, exceto a indicada no código 7', 'value'=>'2'),
				array('title' => '3 - Nacional - mercadoria ou bem com Conteúdo de Importação superior a 40%', 'value' =>'3'),
				array('title' => '4 - Nacional - cuja produção tenha sido feita em conformidade com os processos produtivos básicos de que tratam as legislações citadas nos Ajustes', 'value'=>'4'),
				array('title' => '5 - Nacional - mercadoria ou bem com Conteúdo de Importação inferior ou igual a 40%', 'value' =>'5'),
				array('title' => '6 - Estrangeira - Importação direta, sem similar nacional, constante em lista da CAMEX', 'value'=>'6'),
				array('title' => '7 - Estrangeira - Adquirida no mercado interno, sem similar nacional, constante em lista da CAMEX', 'value' =>'7'),
				array('title' => '8 - Nacional - Mercadoria ou bem com Conteúdo de Importação superior a 70% (setenta por cento)', 'value' =>'8')
			)
		);

		$formArray['campos']['icms']['T008_Codigo_Beneficio_Fiscal']  = array('name' => 'T008_Codigo_Beneficio_Fiscal', 'label' => 'Código Benefício Fiscal','type' => 'text');

		if ($g['c004']['C004_Codigo_Regime_Tributario']=='1' && $g['c020']['C020_Versao_NFe']!='1.10'){
            $D059_Simples_Nacional='S';
        }else{
        	$D059_Simples_Nacional='N';
        }

       	// $simples_ = gGeraSelect('D059', 'D059_Id', 'D059_Codigo_ST,D059_Descricao_Origem', ' WHERE D059_Simples_Nacional="S" order by D059_Codigo_ST', true);
       	// $simples[] = $simples_[0];
       	// $simples[] = array('title' => 'optgroup', 'value' => 'SIMPLES');
       	// $simples_ = array_slice($simples_, 1, count($simples_)-1, true);
       	// $simples = array_merge($simples, $simples_);
       	// $simples[] = array('title' => 'optgroup', 'value' => 'NORMAL');
       	// $normal = gGeraSelect('D059', 'D059_Id', 'D059_Codigo_ST,D059_Descricao_Origem', ' WHERE D059_Simples_Nacional="N" order by D059_Codigo_ST', true);
       	// unset($normal[0]);
		// $arrayCST = array_merge($simples, $normal);

		// Campos gerais ICMS
/* 		$C004 =  mysql_fetch_assoc(mysql_query("SELECT C004_Flag_Empresa_Simples FROM C004 WHERE C004_iD='{$g['empresaAtual']}'"));
		if($C004['C004_Flag_Empresa_Simples'] == 1){
			$simples[] = array('title' => 'optgroup', 'value' => 'SIMPLES NACIONAL');
			$simples_ = gGeraSelect('D059', 'D059_Id', 'D059_Codigo_ST,D059_Descricao_Origem', ' WHERE D059_Simples_Nacional="S" order by D059_Codigo_ST', true);
			$simples[] = $simples_[0];
			$simples_ = array_slice($simples_, 1, count($simples_)-1, true);
			$arrayCST = array_merge($simples, $simples_);
		}else{
			$normal[] = array('title' => 'optgroup', 'value' => 'REGIME NORMAL');
			$normal_ = gGeraSelect('D059', 'D059_Id', 'D059_Codigo_ST,D059_Descricao_Origem', ' WHERE D059_Simples_Nacional="N" order by D059_Codigo_ST', true);
			$normal[] = $normal_[0];
			$normal_ = array_slice($normal_, 1, count($normal_)-1, true);
		 	$arrayCST = array_merge($normal, $normal_);
		} */

		$arrayCST = array();
		// CST SIMPLES
		$simples[]    = array('title' => 'optgroup', 'value' => "SIMPLES NACIONAL");
		$simples_     = gGeraSelect('D059', 'D059_Id', 'D059_Codigo_ST,D059_Descricao_Origem', ' WHERE D059_Simples_Nacional="S" order by D059_Codigo_ST', false);
		$simples[]    = $simples_[0];
		$simples_     = array_slice($simples_, 1, count($simples_)-1, true);
		$arraySimples = array_merge($simples, $simples_);
	
		// CST REGIME NORMAL
		$normal[]    = array('title' => 'optgroup', 'value' => 'REGIME NORMAL');
		$normal_     = gGeraSelect('D059', 'D059_Id', 'D059_Codigo_ST,D059_Descricao_Origem', ' WHERE D059_Simples_Nacional="N" order by D059_Codigo_ST', false);
		$normal[]    = $normal_[0];
		$normal_     = array_slice($normal_, 1, count($normal_)-1, true);
		$arrayNormal = array_merge($normal, $normal_);

		$pulaLinha[] = array('title' => '','value' => '');
		$C004 =  mysql_fetch_assoc(mysql_query("SELECT C004_Flag_Empresa_Simples FROM C004 WHERE C004_iD='{$g['empresaAtual']}'"));
		if($C004['C004_Flag_Empresa_Simples'] == 1){
			// Se for do simples, posiciona as CST do simples no inicio
			$arrayCST = array_merge($arraySimples,$pulaLinha,$arrayNormal);
		}else{
			// Se for regime normal, posiciona as CST do regime normal no inicio
			$arrayCST = array_merge($arrayNormal,$pulaLinha,$arraySimples);
		}

		$formArray['campos']['icms']['T008_D059_Id'] = array('name' => 'T008_D059_Id', 'label' => 'Situação Tributária', 'type' => 'select', 
			'select' => $arrayCST
	 	);
	 	$formArray['campos']['icms']['T008_Percentual_Partilha_ICMS_Destino'] = array('name' => 'T008_Percentual_Partilha_ICMS_Destino', 'label' => '% Partilha ICMS Destino', 'type' => 'moeda', 'length' => array(10,15));
		$formArray['campos']['icms']['T008_Percentual_Partilha_ICMS_Origem'] = array('name' => 'T008_Percentual_Partilha_ICMS_Origem', 'label' => '% Partilha ICMS Origem', 'type' => 'moeda', 'length' => array(10,15));
		$formArray['campos']['icms']['T008_ICMS_Inter_Estadual'] = array('name' => 'T008_ICMS_Inter_Estadual', 'label' => '% ICMS Inter Estadual', 'type' => 'moeda', 'length' => array(15, 20));
		$formArray['campos']['icms']['T008_ST_ICMS_Interno']  = array('name' => 'T008_ST_ICMS_Interno', 'label' => '% ICMS Interno', 'type' => 'moeda', 'length' => array(15, 20));
		$formArray['campos']['icms']['T008_Modalidade_ICMS_Substituicao'] = array('name' => 'T008_Modalidade_ICMS_Substituicao', 'label' => 'Modalidade de determinação <br/>da BC do ICMS ST', 'type' => 'select', 'select' => 
			array(
				array('title' => '...', 'value' => ''),
				array('title' => 'Preço tabelado ou máximo sugerido', 'value' => '0'),
				array('title' => 'Lista Negativa (valor)', 'value' => '1'),
				array('title' => 'Lista Positiva (valor)', 'value' => '2'),
				array('title' => 'Lista Neutra (valor)', 'value' => '3'),
				array('title' => 'Margem Valor Agregado (%)', 'value' => '4'),
				array('title' => 'Pauta (valor)', 'value' => '5'),
				array('title' => 'Valor da Operação', 'value' => '6'),
			)
		);
		$formArray['campos']['icms']['T008_ST_VA'] = array('name' => 'T008_ST_VA', 'label' => 'MVA', 'type' => 'moeda', 'length' => array(10,15));
		$formArray['campos']['icms']['T008_Modalidade_ICMS'] = array('name' => 'T008_Modalidade_ICMS', 'label' => 'Modalidade de determinação <br/>da BC do ICMS', 'type' => 'select', 'select' => 
			array(
				array('title' => '...', 'value' => '','selected' => true),
				array('title' => 'Margem Valor Agregado (%)', 'value' => '0'),
				array('title' => 'Pauta (Valor)', 'value' => '1'),
				array('title' => 'Preço Tabelado Máx. (valor)', 'value' => '2'),
				array('title' => 'Valor da Operação', 'value' => '3'),
			)
		);
		$formArray['campos']['icms']['T008_Percentual_Reducao_ICMS'] = array('name' => 'T008_Percentual_Reducao_ICMS', 'label' => '% Redução ICMS', 'type' => 'moeda', 'length' => array(10,15));
		$formArray['campos']['icms']['T008_Aliquota_ICMS'] = array('name' => 'T008_Aliquota_ICMS', 'label' => '% ICMS', 'type' => 'moeda', 'length' => array(10,15));
		$formArray['campos']['icms']['T008_Percentual_Base_Operacao_Propria'] = array('name' => 'T008_Percentual_Base_Operacao_Propria', 'label' => '% da BC operação própria', 'type' => 'moeda', 'length' => array(10,15));
		$formArray['campos']['icms']['T008_Modalidade_ICMS_Substituicao'] = array('name' => 'T008_Modalidade_ICMS_Substituicao', 'label' => 'Modalidade de determinação <br/>da BC do ICMS ST', 'type' => 'select', 'select' => 
			array(
				array('title' => '...', 'value' => ''),
				array('title' => 'Preço tabelado ou máximo sugerido', 'value' => '0'),
				array('title' => 'Lista Negativa (valor)', 'value' => '1'),
				array('title' => 'Lista Positiva (valor)', 'value' => '2'),
				array('title' => 'Lista Neutra (valor)', 'value' => '3'),
				array('title' => 'Margem Valor Agregado (%)', 'value' => '4'),
				array('title' => 'Pauta (valor)', 'value' => '5'),
				array('title' => 'Valor da Operação', 'value' => '6'),
			)
		);
		$formArray['campos']['icms']['T008_Percentual_Reducao_ICMS_ST'] = array('name' => 'T008_Percentual_Reducao_ICMS_ST', 'label' => '% Redução de BC do ICMS ST', 'type' => 'moeda', 'length' => array(10,15));
		$formArray['campos']['icms']['T008_ST_VA'] = array('name' => 'T008_ST_VA', 'label' => 'MVA', 'type' => 'moeda', 'length' => array(10,15));
		$formArray['campos']['icms']['T008_UF_ICMS_Substituicao_Devido'] = array('name' => 'T008_UF_ICMS_Substituicao_Devido', 'label' => 'UF para qual é devido o ICMS ST', 'type' => 'select', 'select' => 
			gGeraSelect('D018', 'D018_UF', 'D018_UF,D018_Estado', 'order by D018_Id', true)
		);
		$formArray['campos']['icms']['T008_Percentual_FCP'] = array('name' => 'T008_Percentual_FCP', 'label' => '% FCP', 'type' => 'moeda', 'length' => array(10,15), 'extra' => 'title="Fundo de Combate a Pobreza"', 'align' => 'right', 'style' => 'float:right;');
		$formArray['campos']['icms']['T008_Valor_Base_ICMS_Substituicao_Destino'] = array('name' => 'T008_Valor_Base_ICMS_Substituicao_Destino', 'label' => 'Valor da BC do ICMS ST <br/>da UF destino', 'type' => 'moeda', 'length' => array(10,15));
	 	$formArray['campos']['icms']['T008_Valor_ICMS_Substituicao_Destino'] = array('name' => 'T008_Valor_ICMS_Substituicao_Destino', 'label' => 'Valor do ICMS ST da UF destino', 'type' => 'moeda', 'length' => array(10,15));
		$formArray['campos']['icms']['T008_Aliquota_ICMS_Credito']  = array('name' => 'T008_Aliquota_ICMS_Credito', 'label' => 'Alíquota aplicável de cálculo do crédito', 'type' => 'moeda', 'length' => array(15, 20));
		$formArray['campos']['icms']['T008_Valor_ICMS_Credito']  = array('name' => 'T008_Valor_ICMS_Credito', 'label' => 'Valor crédito do ICMS que pode ser aproveitado <br/>nos termos do art. 23 da LC 123', 'type' => 'moeda', 'length' => array(15, 20));
		$formArray['campos']['icms']['T008_Preco_Pauta_ST'] = array('name' => 'T008_Preco_Pauta_ST', 'label' => 'Preço Pauta', 'type' => 'moeda', 'length' => array(20,10));
		/* Campos para NT 2019.001 - Criação e Atualização de Regras de Validação
		Versão 1.64 – Setembro de 2024, por solicitação da Smartness
		Gabriel Cegantini - 02/10/2025
		================================================================================ */
		$formArray['campos']['icms']['T008A_Codigo_Beneficio_Reducao_Base_ICMS'] = array('name' => 'T008A_Codigo_Beneficio_Reducao_Base_ICM', 'label'=>'Cód. Benefício Fiscal Red. BC', 'type' => 'texto','length' => array(10,10), 'uppercase' => false);
		/* ================================================================================ */

		// Desabilita a edição do formulário
		$formArray['campos']['icms']['Tab_Icms_Produto']  = array('name' => 'Tab_Icms_Produto', 'label' => '', 'type' => 'hidden', 'reject' => true);
		 
		/* [REFORMA TRIBUTÁRIA] - Gabriel Cegantini 19/09/2025
		==============================================================*/
		// Callback pra pegar a classificação tributária da CST da reforma
		$callbackReformaAoAlterarCst = function ($campo) {
			return <<<EOT
				jsRecarregarCampo('{$campo}', array('cst' => \$array['campoAtual']));
			EOT;
		};
		// Callback pra trocar o select
		$callbackReformaPreencherSelectClassificacaoTributaria = function ($campo) {
			return <<<EOT
				require_once("bibliotecas/classes/ReformaTributaria.php");
				\$reformaTributaria = new ReformaTributaria();
				// Se o usuário trocar a CST vamos receber ela na hora do form
				if (isset(\$array['cst'])) {
					\$cst = \$array['cst'];
				// Se é a primeira vez que o usuário abriu o form de alteração precisamos descobrir qual é ela
				} else {
					\$qObj = mysql_query("SELECT {$campo} 
										FROM T008
								   LEFT JOIN T008A ON T008A_T008_Id = T008_Id
										WHERE T008_Id = '{\$array['acaoId']}'");
					\$mObj = mysql_fetch_assoc(\$qObj);
					\$cst = \$mObj['{$campo}'];
				}
				return \$reformaTributaria->getCclassTribFormSelect(\$cst);
			EOT;
		};
		// Ao escolher a classificação tributária no item da NF, aplica de imediato
		// as alíquotas e percentuais definidos na tabela oficial da Reforma Tributária.
		$callbackReformaAoAlterarClassificacao = function ($camposAliquotas) {
			$atualizacoes = '';
			foreach ($camposAliquotas as $campo => $chaveAliquota) {
				$atualizacoes .= "jsAlterarCampoValor('{$campo}', number_format((float) \$aliquotas['{$chaveAliquota}'], 2, ',', ''));\n";
			}
			return <<<EOT
				require_once("bibliotecas/classes/ReformaTributaria.php");
				\$reformaTributaria = new ReformaTributaria();
				\$aliquotas = \$reformaTributaria->getAliquotas(\$array['campoAtual'] ?? '');
				{$atualizacoes}
EOT;
		};
		$camposAliquotasReforma = array(
			'T008A_Aliquota_CBS' => 'aliquotaCbs',
			'T008A_Percentual_Reducao_CBS' => 'percentualReducaoCbs',
			'T008A_Percentual_Diferimento_CBS' => 'percentualDiferimentoCbs',
			'T008A_Aliquota_IBS_UF' => 'aliquotaIbsUf',
			'T008A_Percentual_Reducao_IBS_UF' => 'percentualReducaoIbsUf',
			'T008A_Percentual_Diferimento_IBS_UF' => 'percentualDiferimentoIbsUf',
			'T008A_Aliquota_IBS_Municipal' => 'aliquotaIbsMunicipal',
			'T008A_Percentual_Reducao_IBS_Municipal' => 'percentualReducaoIbsMunicipal',
			'T008A_Percentual_Diferimento_IBS_Municipal' => 'percentualDiferimentoIbsMunicipal'
		);
		$camposAliquotasReformaRegular = array();
		foreach ($camposAliquotasReforma as $campo => $chaveAliquota) {
			if (strpos($campo, 'Percentual_Diferimento') !== false) {
				continue;
			}
			$camposAliquotasReformaRegular[$campo . '_Regular'] = $chaveAliquota;
		}
		// Campos da aba reforma tributária
		$formArray['campos']['reforma']= array(
			'T008A_Situacao_Tributaria_CBS_IBS' 			=> array('name' => 'T008A_Situacao_Tributaria_CBS_IBS', 'label' => 'CST CBS/IBS', 'type' => 'select', 'select' => $reformaTributaria->getCstsFormSelect(), 'callbackAoAlterar' => $callbackReformaAoAlterarCst('T008A_Classificacao_Tributaria_CBS_IBS')),
			'T008A_Classificacao_Tributaria_CBS_IBS' 		=> array('name' => 'T008A_Classificacao_Tributaria_CBS_IBS', 'label' => 'Classificação Trib. CBS/IBS', 'type' => 'select', 'select' => array(array('title' => '...', 'select' => '')), 'callbackPreencherSelect' => $callbackReformaPreencherSelectClassificacaoTributaria('T008A_Situacao_Tributaria_CBS_IBS'), 'callbackAoAlterar' => $callbackReformaAoAlterarClassificacao($camposAliquotasReforma)),
			'T008A_Aliquota_CBS' 							=> array('name' => 'T008A_Aliquota_CBS', 'label' => '% CBS', 'type' => 'moeda'),
			'T008A_Aliquota_IBS_Municipal' 					=> array('name' => 'T008A_Aliquota_IBS_Municipal', 'label' => '% IBS Mun', 'type' => 'moeda'),
			'T008A_Aliquota_IBS_UF' 						=> array('name' => 'T008A_Aliquota_IBS_UF', 'label' => '% IBS UF', 'type' => 'moeda'),
			'T008A_Percentual_Reducao_CBS' 					=> array('name' => 'T008A_Percentual_Reducao_CBS', 'label' => '% Red. CBS', 'type' => 'moeda'),
			'T008A_Percentual_Reducao_IBS_UF' 				=> array('name' => 'T008A_Percentual_Reducao_IBS_UF', 'label' => '% Red. IBS UF', 'type' => 'moeda'),
			'T008A_Percentual_Reducao_IBS_Municipal' 		=> array('name' => 'T008A_Percentual_Reducao_IBS_Municipal', 'label' => '% Red. IBS Mun.', 'type' => 'moeda'),
			'T008A_Percentual_Diferimento_CBS' 				=> array('name' => 'T008A_Percentual_Diferimento_CBS', 'label' => '% Dif. CBS', 'type' => 'moeda'),
			'T008A_Percentual_Diferimento_IBS_UF' 			=> array('name' => 'T008A_Percentual_Diferimento_IBS_UF', 'label' => '% Dif. IBS UF', 'type' => 'moeda', 'casasDecimais' => 4),
			'T008A_Percentual_Diferimento_IBS_Municipal' 	=> array('name' => 'T008A_Percentual_Diferimento_IBS_Municipal', 'label' => '% Dif. IBS Mun.', 'type' => 'moeda'),

			// Trib regular
			'T008A_Situacao_Tributaria_CBS_IBS_Regular' 			=> array('name' => 'T008A_Situacao_Tributaria_CBS_IBS_Regular', 'label' => 'CST CBS/IBS Reg.', 'type' => 'select', 'select' => $reformaTributaria->getCstsFormSelect(), 'callbackAoAlterar' => $callbackReformaAoAlterarCst('T008A_Classificacao_Tributaria_CBS_IBS_Regular')),
			'T008A_Classificacao_Tributaria_CBS_IBS_Regular' 		=> array('name' => 'T008A_Classificacao_Tributaria_CBS_IBS_Regular', 'label' => 'Classificação Trib. CBS/IBS Reg.', 'type' => 'select', 'select' => array(array('title' => '...', 'select' => '')), 'callbackPreencherSelect' => $callbackReformaPreencherSelectClassificacaoTributaria('T008A_Situacao_Tributaria_CBS_IBS_Regular'), 'callbackAoAlterar' => $callbackReformaAoAlterarClassificacao($camposAliquotasReformaRegular)),
			'T008A_Aliquota_CBS_Regular' 							=> array('name' => 'T008A_Aliquota_CBS_Regular', 'label' => '% CBS Reg.', 'type' => 'moeda'),
			'T008A_Aliquota_IBS_Municipal_Regular' 					=> array('name' => 'T008A_Aliquota_IBS_Municipal_Regular', 'label' => '% IBS Mun Reg.', 'type' => 'moeda'),
				'T008A_Aliquota_IBS_UF_Regular' 						=> array('name' => 'T008A_Aliquota_IBS_UF_Regular', 'label' => '% IBS UF Reg.', 'type' => 'moeda'),
				'T008A_Percentual_Reducao_CBS_Regular' 					=> array('name' => 'T008A_Percentual_Reducao_CBS_Regular', 'label' => '% Red. CBS Reg.', 'type' => 'moeda'),
				'T008A_Percentual_Reducao_IBS_UF_Regular' 				=> array('name' => 'T008A_Percentual_Reducao_IBS_UF_Regular', 'label' => '% Red. IBS UF Reg.', 'type' => 'moeda'),
					'T008A_Percentual_Reducao_IBS_Municipal_Regular' 		=> array('name' => 'T008A_Percentual_Reducao_IBS_Municipal_Regular', 'label' => '% Red. IBS Mun. Reg.', 'type' => 'moeda'),
				);
			$formArray['campos']['referenciada']= array(
				'T008A_Chave_Acesso_DFe_Referenciado' 					=> array('name' => 'T008A_Chave_Acesso_DFe_Referenciado', 'label' => 'Chave DFe Ref.', 'type' => 'text', 'length' => array(44,44)),
				'T008A_Item_DFe_Referenciado' 							=> array('name' => 'T008A_Item_DFe_Referenciado', 'label' => 'Item DFe Ref.', 'type' => 'text', 'length' => array(5,5)),
			);
		/*==============================================================*/

		$form->definirFormularioArray($formArray);
		
		/**
		 * PARTE 3: Processamento do formulário (se foi enviado)
		 * E carregamento dos valores (se não foi enviado parametro acaoId)
		 */
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
			// Form sem erros
			} else if ($formCodigo == 2) {
				// Insert ou Update? 
				$retorno = $VEN003->incluirProduto($form,$acaoId);
				if ($retorno !== true) {
					echo gGeraAlertMsg('Erro', $retorno);
				} else {
					echo gGeraAlertMsg('Operação OK', "Com sucesso");
					echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										{$alert}
									}, 150);
  								});
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								divRefresh('{$g['divIdParent']}', true);
							} </script></div>
EOT;
				}
			}
		} else {
			// Carrega os valores caso seja um update
			if (!empty($r_acaoId)) {
				$processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $r_acaoId);
				
				$sql = "SELECT T008_Flag_Situacao_Tributaria FROM T008 WHERE T008_Id = '{$acaoId}'";
				$resultado = mysql_query($sql);
				$row = mysql_fetch_assoc($resultado);
				$form->campoSetarValor('Codigo_Origem_Mercadoria', $row['T008_Flag_Situacao_Tributaria'][0]);
				
				// Mostra erros de formulário uteis ao desenvolvedor
				if ($processado !== true) {
					reset($form->formArray['campos']);
					if ($tab == key($form->formArray['campos'])) {
						echo $processado;
					}
				}
			}
			$form->campoSetarValor('T008_D059_Id', $D059_Id);
		}

		/**
		 * PARTE 4: Gerar o formulário
		 */
		$gerado = gGeraCamposForm($form, $tab, $r_acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

		/**
		 * PARTE 5: Exibir o formulário
		 */
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');

	break;
	
		case 'ven003FormInutilizar':
			/**
			* PARTE 1: Variavéis envidas para o formulário
			*/
			// Variável indica qual tab do form será exibida
			$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
			// Utilizada em UPDATE, é o ID do registro que está sendo editado
			$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
			$r_T007_Id = (isset($_REQUEST['T007_Id'])) ? $_REQUEST['T007_Id'] : false;
			
			$sql=mysql_query("SELECT T007_Numero_Nota_Fiscal FROM T007 WHERE T007_Id='{$r_T007_Id}'");
			$row=mysql_fetch_array($sql);

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
			$form_tabela = 'T007';
			$form_campo_pk = 'T007_Id';
			// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
			$enviar_btnId = 'ven003FormInutilizar_btn';
			// Form que será executado ao clicar no botão
			$enviar_acaoHref = '/ven/ven003/form/ven003FormInutilizar/';

			// form editor
			$form_editor = array('geral' =>'{"T007_Numero_Nota_Fiscal_Inicial":{"top":6.98529052734375,"left":17.51225471496582,"width":136.431372,"hidden":0},"T007_Numero_Nota_Fiscal_Final":{"top":30,"left":22.855392456054688,"width":136.431372,"hidden":0},"T007_Serie":{"top":52.977935791015625,"left":90.53921508789062,"width":142,"hidden":0},"T007_Flag_ACP":{"top":75.9681396484375,"left":35.3431396484375,"width":143,"hidden":0},"T007_Justificativa_Inutilizacao":{"top":98.98284912109375,"left":55.72304153442383,"height":58.431371999999996,"width":341.431372,"hidden":0},"ven003FormInutilizar_btn":{"top":165.9803924560547,"left":415.98040771484375,"width":null,"hidden":0}}');


			// novo formulário
			$form = new Formulario('ven003FormInutilizar');

			// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
			$formArray = array(
				'campos' => array(
					'geral' => array(
						'T007_Numero_Nota_Fiscal_Inicial' => array('name' => 'T007_Numero_Nota_Fiscal_Inicial','label'=>'Numeração Inicial NF', 'value' => $row['T007_Numero_Nota_Fiscal'], 'type' => 'text','reject' => true, 'length' => array(20,20)),
						'T007_Numero_Nota_Fiscal_Final' => array('name' => 'T007_Numero_Nota_Fiscal_Final','label'=>'Numeração Final NF', 'value' => $row['T007_Numero_Nota_Fiscal'], 'type' => 'text','reject' => true, 'legth' => array(20,20)),
						'T007_Serie' => array('name' => 'T007_Serie','label'=>'Série', 'type' => 'select','select' => 
							array(
								array('title' => '001 - Normal', 'value' => '1', 'selected' => true),
								array('title' => '002 - Normal', 'value' => '2'),
								array('title' => '003 - Normal', 'value' => '3'),
								array('title' => '100 - Normal', 'value' => '100'),
								array('title' => '900 - SCAN', 'value' => '900'),
							),
						
						),
						'T007_Flag_ACP' => array('name' => 'T007_Flag_ACP','label'=>'Tipo documento', 'type' => 'select','select' => 
							array(
								array('title' => 'NF-e', 'value' => '6', 'selected' => true),
								array('title' => 'NFC-e', 'value' => '8'),
							),
						),
						'T007_Justificativa_Inutilizacao' => array('name' => 'T007_Justificativa_Inutilizacao','label'=>'Justificativa', 'type' => 'textarea','length' => array(3,60)),
						'ven003FormInutilizar_btn' => array('name' => 'ven003FormInutilizar_btn', 'type'=>'submit', 'reload' => false, 'value' => 'Enviar', 'align' => 'right', 'style' => 'float:right;', 'extra' => 'style="margin-right:10px;margin-top:0px;padding-left:10px;padding-right:10px;padding-top:0px;height:20px;"', 'reject'=>true),
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
					$T007_Numero_Nota_Fiscal_Inicial = urldecode($form->campoValorEnviado('T007_Numero_Nota_Fiscal_Inicial'));
					$T007_Numero_Nota_Fiscal_Final   = urldecode($form->campoValorEnviado('T007_Numero_Nota_Fiscal_Final'));
					$T007_Justificativa_Inutilizacao = urldecode($form->campoValorEnviado('T007_Justificativa_Inutilizacao'));
					$T007_Serie 					 = urldecode($form->campoValorEnviado('T007_Serie'));
					$T007_Flag_ACP 					 = urldecode($form->campoValorEnviado('T007_Flag_ACP'));

					$verifica_T007 = mysql_query("SELECT T007_Numero_Nota_Fiscal+1 as T007_Numero_Nota_Fiscal 
												    FROM T007 
												   WHERE T007_C004_Id='{$g['empresaAtual']}' 
												     AND T007_Data_Emissao > DATE_ADD(CURDATE(), INTERVAL -60 DAY)
												     AND T007_Flag_ACP='{$T007_Flag_ACP}' 
												ORDER BY T007_Numero_Nota_Fiscal DESC LIMIT 1;");
					
					if(mysql_num_rows($verifica_T007) <= 0){
						
						$verifica_T007 = mysql_query("SELECT T007_Numero_Nota_Fiscal+1 as T007_Numero_Nota_Fiscal 
														FROM T007 
					   								   WHERE T007_C004_Id='{$g['empresaAtual']}' 
														 AND T007_Flag_ACP='{$T007_Flag_ACP}' 
													ORDER BY T007_Numero_Nota_Fiscal DESC LIMIT 1;");
					}

					$mVerifica_T007 = mysql_fetch_array($verifica_T007);
					if(($T007_Numero_Nota_Fiscal_Inicial > $mVerifica_T007['T007_Numero_Nota_Fiscal']) || ($T007_Numero_Nota_Fiscal_Final > $mVerifica_T007['T007_Numero_Nota_Fiscal'])){
						echo gGeraAlertMsg('Erro', "Não foi possível inutilizar esta numeração, verifique se você inseriu a numeração corretamente");
					} else {

						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Inutilizar Nota Fiscal', '/ven/ven003/outro/ven003OutroInutilizar/', '&T007_Numero_Nota_Fiscal_Inicial={$T007_Numero_Nota_Fiscal_Inicial}&T007_Numero_Nota_Fiscal_Final={$T007_Numero_Nota_Fiscal_Final}&T007_Justificativa_Inutilizacao={$T007_Justificativa_Inutilizacao}&T007_Serie={$T007_Serie}&T007_Flag_ACP={$T007_Flag_ACP}', [680,430]);
								divRefresh('{$g['divId']}', true);
							} </script></div>
EOT;
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
							echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								divRefresh('{$g['divId']}', true);
							} </script></div>
EOT;
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
			echo  $gerado['pagina'];
			if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
			$g['smarty']->display('formEditor.tpl');

		break;
			
		case 'ven003FormImportarXML':
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
		$form_tabela = 'T007';
		$form_campo_pk = 'T007';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'ven003FormImportarXML_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/ven/ven003/form/ven003FormImportarXML/';
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('ven003FormImportarXML');
		$formArray = array(
			'campos' => array(
				'geral' => array(
				)
			)
		);
		$formArray['campos']['geral']['uploadArquivoXML'] 				= array('name' => 'uploadArquivoXML', 'label' => 'Selecione o XML', 'type' => 'file', 'length' => array(24, 24), 'reject' => true);
		$formArray['campos']['geral']['ven003FormImportarXML_btn'] 		= array('name' => 'ven003FormImportarXML_btn', 'type'=>'submit', 'reload' => false, 'value' => 'Enviar', 'align' => 'right', 'style' => 'float:right;', 'extra' => 'style="margin-right:10px;margin-top:0px;padding-left:10px;padding-right:10px;padding-top:0px;height:20px;"', 'reject'=>true);
		$formArray['campos']['geral']['T007_Arquivo_XML'] 				= array('name' => 'T007_Arquivo_XML', 'type' => 'hidden');
		$formArray['campos']['geral']['divReload'] 						= array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true);
		$formArray['campos']['geral']['cadastrarProdutos'] 				= array('name' => 'cadastrarProdutos', 'label'=>'Cadastrar produtos?', 'reject'=>true, 'type' => 'select','select' => 
																				array(
																					array('title' => 'Não', 'value' => 'N'),
																					array('title' => 'Sim', 'value' => 'S'),
																				)
																			);
		$formArray['campos']['geral']['geraContasReceber'] 				= array('name' => 'geraContasReceber', 'label'=>'Gera contas a receber?', 'reject'=>true, 'type' => 'select','select' => 
																				array(
																					array('title' => 'Não', 'value' => 'N'),
																					array('title' => 'Sim', 'value' => 'S'),
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
				$arquivo = $form->campoValorEnviado('uploadArquivoXML');
				$cadastrarProdutos = $form->campoValorEnviado('cadastrarProdutos');
				$geraContasReceber = $form->campoValorEnviado('geraContasReceber');
				$retorno = $VEN003->prepararImportarXML($arquivo,$cadastrarProdutos,$geraContasReceber);
				//print_r($retorno[2]);
//				die();
				if($retorno[0] > 1){
					$msg = <<<EOT
					<textarea style="width:700px; height:400px;" class="itemForm itemForm-arrayColunas formInput ui-state-default ui-corner-all">
Notas normais importadas: {$retorno[0]}\\n
Notas normais com erro: {$retorno[1]}\\n\\n
Notas canceladas: {$retorno[3]}\\n
Notas canceladas com erro: {$retorno[4]}\\n\\n
Erros Nota: \\n\\n{$retorno[2]}\\n\\n
Erros Cancelamento \\n\\n{$retorno[5]}										
EOT;
					if(!empty($retorno[6])){
						$msg .= <<<EOT
\\nCFOPs Cadastradas \\n\\n{$retorno[6]}

\\n** FAVOR VERIFICAR AS CFOPs CADASTRADAS **
EOT;
					}
					$msg .= <<<EOT
						</textarea>	
EOT;
					echo gGeraAlertMsg('Erro', $msg);
				}else{
					// Imprime uma mensagem
					$msg = <<<EOT
					<textarea style="width:700px; height:400px;" class="itemForm itemForm-arrayColunas formInput ui-state-default ui-corner-all">
Notas normais importadas: {$retorno[0]}\\n
Notas normais com erro: {$retorno[1]}\\n\\n
Notas canceladas: {$retorno[3]}\\n
Notas canceladas com erro: {$retorno[4]}\\n\\n
Erros Nota: \\n\\n{$retorno[2]}\\n\\n
Erros Cancelamento \\n\\n{$retorno[5]}
EOT;
					if(!empty($retorno[6])){
						$msg .= <<<EOT
\\nCFOPs Cadastradas \\n\\n{$retorno[6]}

\\n** FAVOR VERIFICAR AS CFOPs CADASTRADAS **
EOT;
					}
					$msg .= <<<EOT
						</textarea>	
EOT;
					echo gGeraAlertMsg('Importar XML', $msg);	
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							divRefresh('{$form->campoValorEnviado('divReload')}', true);
						} </script></div>
EOT;
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
						echo  $processado;
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
		$g['smarty']->display('form.tpl');
		
	break;

	case 'ven003FormImportarXMLAtualizar':
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
		$form_tabela = 'T007';
		$form_campo_pk = 'T007';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'ven003FormImportarXMLAtualizar_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/ven/ven003/form/ven003FormImportarXMLAtualizar/';
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('ven003FormImportarXMLAtualizar');
		$formArray = array(
			'campos' => array(
				'geral' => array(
				)
			)
		);
		$formArray['campos']['geral']['uploadArquivoXML'] 				= array('name' => 'uploadArquivoXML', 'label' => 'Selecione o XML', 'type' => 'file', 'length' => array(24, 24), 'reject' => true);
		$formArray['campos']['geral']['ven003FormImportarXMLAtualizar_btn'] 		= array('name' => 'ven003FormImportarXMLAtualizar_btn', 'type'=>'submit', 'reload' => false, 'value' => 'Enviar', 'align' => 'right', 'style' => 'float:right;', 'extra' => 'style="margin-right:10px;margin-top:0px;padding-left:10px;padding-right:10px;padding-top:0px;height:20px;"', 'reject'=>true);
		$formArray['campos']['geral']['T007_Arquivo_XML'] 				= array('name' => 'T007_Arquivo_XML', 'type' => 'hidden');
		$formArray['campos']['geral']['divReload'] 						= array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true);
		
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
				$arquivo = $form->campoValorEnviado('uploadArquivoXML');
				$retorno = $VEN003->prepararImportarXML($arquivo);
				
				if($retorno[0] > 1){
					$msg = <<<EOT
					<textarea style="width:700px; height:400px;" class="itemForm itemForm-arrayColunas formInput ui-state-default ui-corner-all">
Notas normais importadas: {$retorno[0]}\\n
Notas normais com erro: {$retorno[1]}\\n\\n
Notas canceladas: {$retorno[3]}\\n
Notas canceladas com erro: {$retorno[4]}\\n\\n
Erros Nota: \\n\\n{$retorno[2]}\\n\\n
Erros Cancelamento \\n\\n{$retorno[5]}										
EOT;
					if(!empty($retorno[6])){
						$msg .= <<<EOT
\\nCFOPs Cadastradas \\n\\n{$retorno[6]}

\\n** FAVOR VERIFICAR AS CFOPs CADASTRADAS **
EOT;
					}
					$msg .= <<<EOT
						</textarea>	
EOT;
					echo gGeraAlertMsg('Erro', $msg);
				}else{
					// Imprime uma mensagem
					$msg = <<<EOT
					<textarea style="width:700px; height:400px;" class="itemForm itemForm-arrayColunas formInput ui-state-default ui-corner-all">
Notas normais importadas: {$retorno[0]}\\n
Notas normais com erro: {$retorno[1]}\\n\\n
Notas canceladas: {$retorno[3]}\\n
Notas canceladas com erro: {$retorno[4]}\\n\\n
Erros Nota: \\n\\n{$retorno[2]}\\n\\n
Erros Cancelamento \\n\\n{$retorno[5]}
EOT;
					if(!empty($retorno[6])){
						$msg .= <<<EOT
\\nCFOPs Cadastradas \\n\\n{$retorno[6]}

\\n** FAVOR VERIFICAR AS CFOPs CADASTRADAS **
EOT;
					}
					$msg .= <<<EOT
						</textarea>	
EOT;
					echo gGeraAlertMsg('Importar XML', $msg);	
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							divRefresh('{$form->campoValorEnviado('divReload')}', true);
						} </script></div>
EOT;
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
						echo  $processado;
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
		$g['smarty']->display('form.tpl');
		
	break;

	case 'ven003FormImportarTXT':
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
		$form_tabela = 'T007';
		$form_campo_pk = 'T007';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'ven003FormImportarTXT_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/ven/ven003/form/ven003FormImportarTXT/';
		
		$form_editor = array(
			'geral' => '{"uploadArquivoTXT":{"top":7,"left":25.78125,"width":245,"hidden":0},"ven003FormImportarTXT_btn":{"top":5,"left":384,"width":null,"hidden":0},"cadastrarProdutos":{"top":53,"left":5.609375,"width":60,"hidden":0},"calcularTotalProduto":{"top":76,"left":9.28125,"width":60,"hidden":0}}'
		);

/*     {"uploadArquivoTXT":{"top":7,"left":23,"width":245},"cadastrarProdutos":{"top":30,"left":4,"width":49},"calcularTotalProduto":{"top":50,"left":8,"width":49}}*/

		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('ven003FormImportarTXT');
		$formArray = array(
			'campos' => array(
				'geral' => array(
				)
			)
		);
		$formArray['campos']['geral']['uploadArquivoTXT'] 			= array('name' => 'uploadArquivoTXT', 'label' => 'Selecione o TXT', 'type' => 'file', 'length' => array(24, 24), 'reject' => true);
		$formArray['campos']['geral']['ven003FormImportarTXT_btn'] 		= array('name' => 'ven003FormImportarTXT_btn', 'type'=>'submit', 'reload' => false, 'value' => 'Enviar', 'align' => 'right', 'style' => 'float:right;', 'extra' => 'style="margin-right:10px;margin-top:0px;padding-left:10px;padding-right:10px;padding-top:0px;height:20px;"', 'reject'=>true);
		$formArray['campos']['geral']['divReload'] 						= array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true);
		$formArray['campos']['geral']['cadastrarProdutos'] 				= array('name' => 'cadastrarProdutos', 'label'=>'Cadastrar produtos?', 'reject'=>true, 'type' => 'select','select' => 
																				array(
																					array('title' => 'Não', 'value' => 'N'),
																					array('title' => 'Sim', 'value' => 'S'),
																					)
																				);
		$formArray['campos']['geral']['calcularTotalProduto'] 				= array('name' => 'calcularTotalProduto', 'label'=>'Recalcular valores?', 'reject'=>true, 'type' => 'select','select' => 
																				array(
																					array('title' => 'Não', 'value' => 'N'),
																					array('title' => 'Sim', 'value' => 'S'),
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
				$arquivo = $form->campoValorEnviado('uploadArquivoTXT');
				$cadastrarProdutos = $form->campoValorEnviado('cadastrarProdutos');
				$calcularTotalProduto = $form->campoValorEnviado('calcularTotalProduto');
				$retorno = $VEN003->importarTXT($arquivo, $cadastrarProdutos, $calcularTotalProduto);
				
				log("*****Retorno====".$retorno);
				if($retorno !== true){
					$retorno = htmlentities($retorno);
					echo gGeraAlertMsg('Relatório de Erro', '<textarea style=" background-color: #FFF; width:900px; height:400px; text-transform: none;">' . $retorno . '</textarea>');
				}else{
					// Imprime uma mensagem
					$retorno = htmlentities($retorno);
					echo gGeraAlertMsg('Operação OK', 'Arquivo importado com Sucesso!');	
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							divRefresh('{$form->campoValorEnviado('divReload')}', true);
						} </script></div>
EOT;
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
						echo  $processado;
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

	case 'ven003FormPagamentos':
		global $g;
		$acaoId = (isset($r_acaoId)) ? $r_acaoId : false;
		$T007_Id = (isset($r_T007_Id)) ? $r_T007_Id : false;

  		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;

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
		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = 'T007';
		$form_campo_pk = 'T007_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'ven003FormPagamento_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/ven/ven003/form/ven003FormPagamentos/';

	     //form editor
        $form_editor = array(
            'geral' => '{"T007_Pgto_Primeiro_Vcto_Valor_Data":{"top":30,"left":176,"width":72,"hidden":0},"T007_Pgto_Parcelas":{"top":7,"left":31.078125,"width":176,"hidden":0},"T007_Flag_Frete_Primeira_Parcela":{"top":7,"left":270.28125,"width":57,"hidden":0},"T007_Pgto_Primeiro_Vcto_Tipo":{"top":30,"left":33.171875,"width":96,"hidden":0},"T007_Pgto_Primeiro_Vcto_Valor":{"top":30,"left":176,"width":72,"hidden":0},"T007_Flag_ST_Primeira_Parcela":{"top":30,"left":281.90625,"width":57,"hidden":0},"T007_Observacao_Pagamento":{"top":30,"left":637,"height":31,"width":168,"hidden":1},"T007_Pgto_Dias_Vcto_Tipo":{"top":53,"left":21.296875,"width":97,"hidden":0},"T007_Pgto_Dias_Vcto_Valor":{"top":53,"left":176,"width":72,"hidden":0},"T007_Flag_IPI_Primeira_Parcela":{"top":53,"left":282.515625,"width":57,"hidden":0},"T007_D027_Id":{"top":76,"left":31.6875,"width":175,"hidden":0},"ven003FormPagamento_btn":{"top":74,"left":336,"width":null,"hidden":0},"ven003FormAdicionarParcela_btn":{"top":74,"left":752,"width":null,"hidden":1},"ven003SalvarObservacao_btn":{"top":97,"left":752,"width":null,"hidden":1}}',
        );

		// novo formulário
		$form = new Formulario('formVen003FormPagamentos');

		$dadosTabela = array('tabela' => $form_tabela, 'campos' => 'T007_Valor_Total as valorTotal, T007_Pgto_Parcelas as pgtoParcelas', 'where' => $form_campo_pk . " = '{$T007_Id}'" );
		$select = $VEN001->populaComboPgtoParcelas($dadosTabela);
		$CNPJ_Empresa_Corrente=substr($g['C004']['C004_Cnpj'], 0, -8);

		$T007 = mysql_query("SELECT D024_D027_Id FROM T007 LEFT JOIN D024 ON D024_Id = T007_D024_Id WHERE T007_Id  = {$T007_Id}");
		$mT007 = mysql_fetch_array($T007);		
		$disabledPortador = $g["C031"]["desabilitarEdicaoPortador"] == 'S' && !empty($mT007['D024_D027_Id']) && $g['nivelAlcada'] != 4;

		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T007_Pgto_Parcelas' => array('name' => 'T007_Pgto_Parcelas', 'label' => 'Parcelas', 'type' => 'select', 'select' => $select, 'uppercase' => false),
					'T007_Pgto_Primeiro_Vcto_Tipo' => array('name' => 'T007_Pgto_Primeiro_Vcto_Tipo', 'label'=>'1º Venc.', 'type' => 'select','select' =>
						array(
							array('title' => 'Dias', 'value' => 'DIAS'),
							array('title' => 'Data', 'value' => 'DATA'),
						)
					),
					'T007_Pgto_Primeiro_Vcto_Valor' => array('name' => 'T007_Pgto_Primeiro_Vcto_Valor', 'type' => 'text','label' => '',  'length' => array(3,7), 'align' => 'right'),
					'T007_Pgto_Primeiro_Vcto_Valor_Data' => array('name' => 'T007_Pgto_Primeiro_Vcto_Valor_Data', 'label' => '', 'type' => 'data', 'align' => 'right'),
					'T007_Pgto_Dias_Vcto_Tipo' => array('name' => 'T007_Pgto_Dias_Vcto_Tipo', 'label'=>'Dias Venc.', 'type' => 'select','select' =>
						array(
							array('title' => 'Intervalo', 'value' => 'INTERVALO'),
							array('title' => 'Dia Fixo', 'value' => 'DIA FIXO'),
						)
					),
					'T007_Pgto_Dias_Vcto_Valor' => array('name' => 'T007_Pgto_Dias_Vcto_Valor', 'label' => '', 'type' => 'text', 'length' => array(3,6), 'align' => 'right'),
					'T007_D027_Id' => array('name' => 'D027_Id','label'=>'Portador', 'type' => 'select', 'disabled' => $disabledPortador, 'select' =>
						gGeraSelect('D027', 'D027_Id', 'concat(D027_Portador," ",if(D027_Acrescimo_IPV>0,concat("+",D027_Acrescimo_IPV,"%"),"")) as D027_Portador','LEFT JOIN D007 ON D007_Id = D027_D007_Id LEFT JOIN C004 ON C004_Id = D007_C004_Id WHERE (D007_Flag_Layout="1" and D007_Flag_Ativo="S" AND C004_Cnpj LIKE "%'.$CNPJ_Empresa_Corrente.'%") OR ifnull(D027_D007_Id,0) = 0 order by D027_Portador', true, $mT007['T007_D027_Id'])
					),
					'T007_Flag_Frete_Primeira_Parcela' => array('name' => 'T007_Flag_Frete_Primeira_Parcela', 'label'=>'Frete primeira parcela', 'type' => 'select','select' =>
						array(
							array('title' => '...', 'value' => ''),
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N'),
						)
					),
					'T007_Flag_ST_Primeira_Parcela' => array('name' => 'T007_Flag_ST_Primeira_Parcela', 'label'=>'ST primeira parcela', 'type' => 'select','select' =>
						array(
							array('title' => '...', 'value' => ''),
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N'),
						)
					),
					'T007_Flag_IPI_Primeira_Parcela' => array('name' => 'T007_Flag_IPI_Primeira_Parcela', 'label'=>'IPI primeira parcela', 'type' => 'select','select' =>
						array(
							array('title' => '...', 'value' => ''),
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N'),
						)
					),
					'ven003FormPagamento_btn' => array('name' => 'ven003FormPagamento_btn', 'type'=>'submit','reload'=>false,'value'=>'Gerar Parcelas', 'reject'=>true),
					'ven003FormAdicionarParcela_btn' => array('name' => 'ven003FormAdicionarParcela_btn', 'type'=>'submit', 'reload' => false, 'value' => 'Adicionar Parcela', 'reject' => true),
					'T007_Observacao_Pagamento' => array('name' => 'T007_Observacao_Pagamento', 'value'=>$mT007['T007_Observacao_Pagamento'], 'label' => 'Observação', 'type' => 'textarea', 'length' => array(1,23),'reject' => true),
					'ven003SalvarObservacao_btn' => array('name' => 'ven003SalvarObservacao_btn', 'type'=>'submit', 'reload' => false, 'value' => 'Salvar Observação', 'reject' => true),
					'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true),
					'T007_Id' => array('name' => 'T007_Id', 'label' => '', 'type' => 'hidden', 'value' => @$r_T007_Id, 'reject' => true),
				)
			)
		);

		$form->definirFormularioArray($formArray);

		echo <<<EOT
		    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		    	$('#ven003FormAdicionarParcela_btn').unbind('click');
				$('#ven003FormAdicionarParcela_btn').unbind('click').bind('click', function(data) {
					var id = '{$row['T007_D024_Id']}';
						$.getJSON('/ven/ven003/form_func-ajax/inserirParcela/?ajax=true&T007_Id=' + encodeURIComponent('{$T007_Id}') + '&form_tabela=' + encodeURIComponent('{$form_tabela}') + '&form_campo_pk=' + encodeURIComponent('{$form_campo_pk}') + '&callback=?', function(request) {
							if (request.code) {
								divRefresh("{$r_divReload}");
							} else {
								var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
    							dialogConfirm("Erro!", request.data, buttons)
							}
						});
					return false;
				});
				$('#ven003SalvarObservacao_btn').unbind('click').bind('click', function(data) {
					$.getJSON('/ven/ven003/form_func-ajax/salvarObservacao/?ajax=true=' + '&' + $("#{$formId}").serialize() + '&T007_Id=' + encodeURIComponent('{$T007_Id}') + '&callback=?', function(request) {
						if (request.code) {
							divRefresh('{$g['divId']}');
						} else {
							var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
							dialogConfirm("Erro!", request.data, buttons)
						}
					});
					return false;
				});
				
				$('#{$g['divId']} #T007_Pgto_Primeiro_Vcto_Tipo').change(function() {
					mudaCampoVencimento($(this).val());
				});

				mudaCampoVencimento($('#{$g['divId']} #T007_Pgto_Primeiro_Vcto_Tipo').val())

				function mudaCampoVencimento(parametro){
					if (parametro == 'DIAS') {
						$('#{$g['divId']} #T007_Pgto_Primeiro_Vcto_Valor_Data').val('').hide();
						$('#{$g['divId']} #T007_Pgto_Primeiro_Vcto_Valor').show();
						$('#{$g['divId']} #T007_Pgto_Primeiro_Vcto_Valor').focus();
					} else {
						$('#{$g['divId']} #T007_Pgto_Primeiro_Vcto_Valor').val('').hide();
						$('#{$g['divId']} #T007_Pgto_Primeiro_Vcto_Valor_Data').show();
						$('#{$g['divId']} #T007_Pgto_Primeiro_Vcto_Valor_Data').focus();
					}
				}
				$('#{$g['divId']} #T007_Pgto_Primeiro_Vcto_Tipo').trigger('change');
		    } </script></div>
EOT;

		/**
		 * PARTE 3: Processamento do formulário (se foi efnviado)
		 * 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		 */
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
				// Form sem erros
			} else if ($formCodigo == 2) {
				$D027_Id = $form->campoValorEnviado('T007_D027_Id');

				$D027 = mysql_query("SELECT D027_Acrescimo_IPV FROM D027 WHERE D027_Id = {$D027_Id}");
				$rD027 = mysql_fetch_array($D027);

				if ($rD027['D027_Acrescimo_IPV'] > 0) {
					$T250 = mysql_query("SELECT T250_Id, T250_Aliquota, T250_Flag_Altera_Preco FROM T250 WHERE T250_T007_Id = '$T007_Id' AND T250_Tipo = '5' AND T250_Flag_Campo_Calculado = 'S'");
					$mT250 = mysql_fetch_array($T250);
					$T250_Id = $mT250['T250_Id'];

					if (number_format($mT250['T250_Aliquota'], 2) != number_format($rD027['D027_Acrescimo_IPV'], 2)) {
						mysql_query("UPDATE T250 SET T250_Aliquota = '{$rD027['D027_Acrescimo_IPV']}' WHERE T250_Id = '{$T250_Id}'");
						$erro = mysql_error();
						$VEN001->atualizarPrecoTabela($T007_Id);

						if ($mT250['T250_Flag_Altera_Preco'] == 'S') {
							$msg = "Foi acrescido " . $rD027['D027_Acrescimo_IPV'] . "% no preço tabela desse orçamento.";
						} else {
							$msg = "Foi acrescido " . $rD027['D027_Acrescimo_IPV'] . "% ao demonstrativo IPV.";
						}
					}
				} else {
					$T250 = mysql_query("SELECT T250_Id, T250_Aliquota, T250_Flag_Altera_Preco FROM T250 WHERE T250_T007_Id = '$T007_Id' AND T250_Tipo = '5' AND T250_Flag_Campo_Calculado = 'S'");
					$mT250 = mysql_fetch_array($T250);
					$T250_Id = $mT250['T250_Id'];

					if ($mT250['T250_Aliquota'] > 0) {
						mysql_query("UPDATE T250 SET T250_Aliquota = '0' WHERE T250_Id = '{$T250_Id}'");
						$erro = mysql_error();
						$VEN001->atualizarPrecoTabela($T007_Id);

						if ($mT250['T250_Flag_Altera_Preco'] == 'S') {
							$msg = "Foi deduzido " . $mT250['T250_Aliquota'] . "% no preço tabela desse orçamento, devido a mudança do portador.";
						} else {
							$msg = "Foi deduzido " . $mT250['T250_Aliquota'] . "% ao demonstrativo IPV, devido a mudança do portador.";
						}
					}
				}

				// Update dos campos do T007
				if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $r_acaoId, $form)) {

					//ARRAY UTILIZADO NA FUNÇÃO ATUALIZAR PARCELAS
					$dadosTabela = array('tabela' => $form_tabela, 
										 'tabelaId' => 	$T007_Id,
										 'campos' => 'T007_Pgto_Primeiro_Vcto_Tipo as primeiroVencimentoTipo,
	                                         T007_Pgto_Primeiro_Vcto_Valor as primeiroVencimentoNum,
	                                         T007_Pgto_Primeiro_Vcto_Valor_Data as primeiroVencimentoData,
	                                         T007_Pgto_Dias_Vcto_Tipo as diasVctoTipo,
	                                         T007_Pgto_Dias_Vcto_Valor as valorIntervaloDiaFixo,
	                                         T007_D027_Id as D027_Id,
	                                         T007_Flag_Frete_Primeira_Parcela as campoUsarFreteParcela,
	                                         T007_Flag_ST_Primeira_Parcela as campoUsarSTParcela,
	                                         T007_Flag_IPI_Primeira_Parcela as campoUsarIPIParcela,
	                                         T007_Pgto_Parcelas as Total_Parcelas,
	                                         T007_Valor_Total_IPI as valorTotalIPI,
	                                         T007_Valor_Total as valorTotal,
	                                         T007_Valor_Frete as valorTotalFrete,
	                                         T007_Valor_ICMS_Substituicao_Retencao as valorTotalST', 
                                         'where' => $form_campo_pk."='{$T007_Id}'", 
                                         'tabelaPgto' => 'T118', 
                                         'camposPgto' => 'T118_Prazos_Dias as prazoDias,T118_D027_Id as D027Pagamento,T118_Numero_Parcela as numeroParcela', 
                                         'wherePgto' => "T118_T007_Id='{$T007_Id}'");
   					// Gerar Parcelas	
					$retorno = $VEN001->atualizarParcelas($dadosTabela);
					if ($retorno!==true) {
						echo gGeraAlertMsg('Error', 'Erro ao gerar Pacelas:<br />' . $retorno);
					} else if(!empty($msg)){
						echo gGeraAlertMsg('Alerta', $msg);
					}
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
	                       divRefresh("{$r_divReload}");
						} </script></div>
EOT;
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

		$sqlD024 = mysql_query("SELECT D024_Instrucao_Pagamento, D024_Prazo_Parcela, D024_Dias_Vencimento, D024_Quantidade_Parcelas, D027_Portador FROM D024 LEFT JOIN D027 ON D027_Id=D024_D027_Id LEFT JOIN T007 ON T007_D024_Id = D024_Id WHERE T007_Id = '{$T007_Id}'");
		$D024 = mysql_fetch_array($sqlD024);
		$obs='';
		$obs .=	empty($D024['D024_Prazo_Parcela']) 				? '' : " 1º Venc: ".$D024['D024_Prazo_Parcela']."<br />";
		$obs .= empty($D024['D024_Dias_Vencimento']) 			? '' : " Dia Fixo/Intervalo: ".$D024['D024_Dias_Vencimento']."<br />";
		$obs .= empty($D024['D024_Quantidade_Parcelas']) 		? '' : " Nº Parcelas: ".$D024['D024_Quantidade_Parcelas']."<br />";
		$obs .= empty($D024['D027_Portador']) 					? '' : " Portador: ".$D024['D027_Portador']."<br />";
		echo <<<EOT
			<div style="border: 1px solid silver; margin: 10px 15px 0 450px; padding: 5px; height: 110px; border-radius:5px; color: var(--BGVermelho); overflow: auto;">
				<span style="font-weight: bold; color: #000;">Informação de Pagamento</span><br> {$obs}{$D024['D024_Instrucao_Pagamento']}	
			</div>
EOT;

	break;

	case 'xven003FormPagamentos':
		$T007_Id = (isset($r_T007_Id)) ? $r_T007_Id : false;
		global $g;

  		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;

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
		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário

		$form_tabela = 'T118';
		$form_campo_pk = 'T118_Id';
		$form_parent = 'T007_Id';
        $form_parent_valor = $T007_Id;
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'ven003FormPagamento_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/ven/ven003/form/ven003FormPagamentos/';

		//form editor
		$form_editor = array('geral' => '{"Total_Parcelas":{"top":7,"left":31,"width":176,"hidden":0},"usarFreteParcela":{"top":7,"left":271,"width":59,"hidden":1},"selecionaPrimeiroVencimento":{"top":30,"left":33,"width":96,"hidden":0},"primeiroVencimentoData":{"top":30,"left":192,"width":56,"hidden":0},"primeiroVencimentoNum":{"top":30,"left":192,"width":56,"hidden":0},"usarSTParcela":{"top":30,"left":283,"width":59,"hidden":1},"ven003FormAdicionarParcela_btn":{"top":28,"left":656,"width":null,"hidden":1},"selecionaDias":{"top":53,"left":21,"width":96,"hidden":0},"diaFixo":{"top":53,"left":192,"width":56,"hidden":0},"IntervalorDias":{"top":53,"left":192,"width":56,"hidden":0},"usarIPIParcela":{"top":53,"left":284,"width":59,"hidden":1},"ven003SalvarObservacao_btn":{"top":51,"left":656,"width":null,"hidden":1},"Portador_D027_Id":{"top":76,"left":32,"width":175,"hidden":0},"ven003FormPagamento_btn":{"top":74,"left":304,"width":null,"hidden":0},"T007_Observacao_Pagamento":{"top":99,"left":573,"height":32,"width":184,"hidden":1}}');

		$form = new Formulario('formVen003FormPagamentos');

		$select = array();
		$select[] = array('value' => '0', 'title' => 'A COMBINAR');
		// pega as configurações de juros da empresa atual
		$C004=mysql_query("select * from C004 where C004_Id='{$g['empresaAtual']}'");
		$mC004=mysql_fetch_array($C004);
		$tipoJuros=$mC004['C004_Tipo_Juros'];
		$taxaJuros=$mC004['C004_Taxa_Juros'];
		$parcelasIsentas=$mC004['C004_Parcelas_Isentas_Juros'];

		$T007=mysql_query("SELECT * FROM T007 LEFT JOIN D024 ON D024_Id=T007_D024_Id WHERE T007_Id='{$T007_Id}'");
		$mT007=mysql_fetch_array($T007);

		if($mT007['T007_Pgto_Parcelas'] <= 0 && $mT007['D024_Quantidade_Parcelas'] > 0){
			$mT007['T007_Pgto_Parcelas'] = $mT007['D024_Quantidade_Parcelas'];
		}else if($mT007['T007_Pgto_Parcelas'] <= 0 && $mT007['D024_Quantidade_Parcelas'] <= 0){
			$mT007['T007_Pgto_Parcelas'] = $g['C031']['numeroParcelasPadrao'];
		}

		if($mT007['T007_Pgto_Portador'] <= 0 && $mT007['D024_D027_Id'] > 0){
			$mT007['T007_Pgto_Portador'] = $mT007['D024_D027_Id'];
		}else if($mT007['T007_Pgto_Portador'] <= 0 && $g['C031']['D027portadorEmpresa'] > 0){
			$mT007['T007_Pgto_Portador']=$g['C031']['D027portadorEmpresa'];
		}

		if(empty($mT007['D024_Prazo_Parcela'])){
			$mT007['D024_Prazo_Parcela'] = $g['C031']['primeiroVencimentoNum'];
		}
		if(empty($mT007['D024_Dias_Vencimento'])){
			$mT007['D024_Dias_Vencimento'] = $g['C031']['IntervalorDias'];
		}
		if(empty($mT007['T007_Pgto_Dias_Vcto_Tipo'])){
			$mT007['T007_Pgto_Dias_Vcto_Tipo'] = $g['C031']['tipoIntervalodias'];
		}

		//SELECIONANDO OS SERVIÇOS DESSE ORÇAMENTO PARA CALCULAR O TOTAL
		$sql = "SELECT T203_Valor_Total FROM T203 where T203_T007_Id='{$T007_Id}'";
		$T203_sql = mysql_query($sql);

		$totalServicos=0;
		// LOOPING PARA CALCULAR O TOTAL DOS SERVIÇOS SOMADO COM TOTAL DO PEDIDO
		while($T203 = mysql_fetch_array($T203_sql)){
			$totalServicos+=$T203['T203_Valor_Total'];
		}

		//SOMANDO O TOTAL DO ORÇAMENTO COM O TOTAL DOS SERVIÇOS
		$valorTotalNota = $totalServicos + $mT007['T007_Valor_Total'];

		for ($i=1; $i<=$g['C031']['numeroMaxParcelas']; $i++) {
			$valorParcela=$VEN003->VEN003calcularParcelaCombo($i,$valorTotalNota,$tipoJuros,$taxaJuros,$parcelasIsentas);
			if ($i>$parcelasIsentas){
				$valorTotal=gCorrigeNumero($i*gCorrigeNumeroInverte($valorParcela));
				$valorParcela.="*";
			}else{
				$valorTotal=gCorrigeNumero($valorTotalNota);
			}
			if($i == $mT007['T007_Pgto_Parcelas']){
				$select[] = array('value' => $i, 'title' => str_pad($i,2,"0",STR_PAD_LEFT) . " x" . " - " .$valorParcela." = ".$valorTotal, 'selected'=>true);
			}else{
				$select[] = array('value' => $i, 'title' => str_pad($i,2,"0",STR_PAD_LEFT) . " x" . " - " .$valorParcela." = ".$valorTotal);
			}
			// if($i == $mT005['T005_Pgto_Parcelas']){
			// 	$select[] = array('value' => $i, 'title' => str_pad($i,2,"0",STR_PAD_LEFT) . " x" . " - " .$valorParcela." = ".$valorTotal, 'selected'=>true);
			// } 
		}

		if(($mT007['T007_Pgto_Primeiro_Vcto_Valor'] != "") || (!empty($mT007['T007_Pgto_Primeiro_Vcto_Valor']))){
			$primeiroVctoValor = $mT007['T007_Pgto_Primeiro_Vcto_Valor'];
		}else{
			$primeiroVctoValor = $mT007['D024_Prazo_Parcela'];
		}
		if(($mT007['T007_Pgto_Dias_Vcto_Valor'] != "") || (!empty($mT007['T007_Pgto_Dias_Vcto_Valor']))){
			$diaVctoValor = $mT007['T007_Pgto_Dias_Vcto_Valor'];
		}else{
			$diaVctoValor = $mT007['D024_Dias_Vencimento'];
		}
		if(($mT007['T007_Pgto_Data'] != "") || (!empty($mT007['T007_Pgto_Data']))){
			$pgtoData = $mT007['T007_Pgto_Data'];
		}else{
			$pgtoData = "";
		}

		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'Total_Parcelas' => array('name' => 'Total_Parcelas', 'label' => 'Parcelas', 'type' => 'select', 'select' => $select,'uppercase'=>false, 'reject' => true),
					//'valorParcelaSimulacao' => array('name' => 'valorParcelaSimulacao', 'label' => '', 'type' => 'text','extra'=>'style="background:#E8E8E8"','reject' => true, 'length' => array(3,6), 'align' => 'right', 'readonly'=>'readonly'),
					'selecionaPrimeiroVencimento' => array('name' => 'selecionaPrimeiroVencimento', 'label'=>'1º Venc.', 'reject'=>true, 'type' => 'select','select' => 
					array(
						array('title' => 'Dias', 'value' => 'Dias'),
						array('title' => 'Data', 'value' => 'Data'),
					)),
					'primeiroVencimentoNum' => array('name' => 'primeiroVencimentoNum', 'label' => '', 'value'=> $primeiroVctoValor, 'type' => 'text', 'reject' => true, 'length' => array(3,6), 'align' => 'right'),
					'primeiroVencimentoData' => array('name' => 'primeiroVencimentoData', 'label' => '', 'value'=> $primeiroVctoValor, 'type' => 'data', 'reject' => true, 'align' => 'right'),
					'selecionaDias' => array('name' => 'selecionaDias', 'label'=>'Dias Venc.', 'type' => 'select', 'reject'=>true,'select' => 
						array(
							array('title' => 'Intervalo', 'value' => 'I', 'selected' => ($mT007['T007_Pgto_Dias_Vcto_Tipo'] == "Intervalo") ? true : false),
							array('title' => 'Dia Fixo', 'value' => 'F', 'selected' => ($mT007['T007_Pgto_Dias_Vcto_Tipo'] == "Dia Fixo") ? true : false),
						)
					),
					'IntervalorDias' => array('name' => 'IntervalorDias', 'label' => '', 'type' => 'text', 'value'=>$diaVctoValor, 'reject' => true, 'length' => array(3,6)),
					'diaFixo' => array('name' => 'diaFixo', 'label' => '', 'type' => 'text', 'reject' => true, 'value'=>$diaVctoValor, 'length' => array(3,2), 'align' => 'right'),
					'Portador_D027_Id' => array('name' => 'D027_Id','label'=>'Portador', 'reject'=>true, 'type' => 'select', 'select' =>
						gGeraSelect('D027', 'D027_Id', 'D027_Portador','LEFT JOIN D007 ON D007_Id = D027_D007_Id WHERE (D007_Flag_Layout="1" and D007_Flag_Ativo="S") OR ifnull(D027_D007_Id,0) = 0 order by D027_Portador', false, $mT007['T007_Pgto_Portador'])
					),
					'ven003FormPagamento_btn' => array('name' => 'ven003FormPagamento_btn', 'type'=>'submit','reload'=>false,'value'=>'Gerar Parcelas', 'reject'=>true),
					'ven003FormAdicionarParcela_btn' => array('name' => 'ven003FormAdicionarParcela_btn', 'type'=>'submit', 'reload' => false, 'value' => 'Adicionar Parcela', 'reject' => true),
					'T007_Observacao_Pagamento' => array('name' => 'T007_Observacao_Pagamento', 'value'=>$mT007['T007_Observacao_Pagamento'], 'label' => 'Observação', 'type' => 'textarea', 'length' => array(1,23),'reject' => true),
					'ven003SalvarObservacao_btn' => array('name' => 'ven003SalvarObservacao_btn', 'type'=>'submit', 'reload' => false, 'value' => 'Salvar Observação', 'reject' => true),
					'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true),
					'T007_Id' => array('name' => 'T007_Id', 'label' => '', 'type' => 'hidden', 'value' => @$r_T007_Id, 'reject' => true),
				)
			)
		);

		$formArray['campos']['geral']['usarFreteParcela'] = array('name' => 'usarFreteParcela', 'label'=>'Frete primeira parcela', 'reject'=>true, 'type' => 'select','select' => 
																array(
																	array('title' => '...', 'value' => ''),
																	array('title' => 'Sim', 'value' => 'on'),
																	array('title' => 'Não', 'value' => 'off'),
																)
															);

		//--------------------------------------------------------------------------------------------------------
		// Data: 31/08/2018
		// Programador: OSNI
		// Método: ven003FormPagamentos
		// Chamado: 33004
		// Descricao: Valor que é definido no cadastro do cliente e é repassado automaticamente a tab de Notas Fiscais (ST = 'ON')
		// --------------------------------------------------------------------------------------------------------

		$formArray['campos']['geral']['usarSTParcela'] = array('name' => 'usarSTParcela', 'label'=>'ST primeira parcela', 'reject'=>true, 'type' => 'select','select' => 
																array(
																	array('title' => '...', 'value' => ''),
																	array('title' => 'Sim', 'value' => 'on', 'selected' => ($mT007['D024_Cobrar_ST_Primeira_Parcela'] == 'S') ? true : false),
																	array('title' => 'Não', 'value' => 'off'),
																)
															);
		
		// --------------------------------------------------------------------------------------------------------
		// FIM - OSNI - 33004 - ven003FormPagamentos
		// --------------------------------------------------------------------------------------------------------


		$formArray['campos']['geral']['usarIPIParcela'] = array('name' => 'usarIPIParcela', 'label'=>'IPI primeira parcela', 'reject'=>true, 'type' => 'select','select' => 
																array(
																	array('title' => '...', 'value' => ''),
																	array('title' => 'Sim', 'value' => 'on'),
																	array('title' => 'Não', 'value' => 'off'),
																)
															);
		$form->definirFormularioArray($formArray);

		echo <<<EOT
		    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		    	$('#ven003FormAdicionarParcela_btn').unbind('click');
				$('#ven003FormAdicionarParcela_btn').unbind('click').bind('click', function(data) {
					var id = '{$row['T007_D024_Id']}';
						$.getJSON('/ven/ven003/form_func-ajax/inserirParcela/?ajax=true=' + '&' + $("#{$formId}").serialize() + '&callback=?', function(request) {
							if (request.code) {
								divRefresh("{$r_divReload}");
							} else {
								var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
    							dialogConfirm("Erro!", request.data, buttons)
							}
						});
					return false;
				});
				$('#ven003SalvarObservacao_btn').unbind('click').bind('click', function(data) {
					$.getJSON('/ven/ven003/form_func-ajax/salvarObservacao/?ajax=true=' + '&' + $("#{$formId}").serialize() + '&T007_Id=' + encodeURIComponent('{$T007_Id}') + '&callback=?', function(request) {
						if (request.code) {
							divRefresh('{$g['divId']}');
						} else {
							var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
							dialogConfirm("Erro!", request.data, buttons)
						}
					});
					return false;
				});
				$('#{$g['divId']} #selecionaDias').change(function() {
					if ($(this).val() == 'I') {
						$('#{$g['divId']} #diaFixo').val('').hide();
						$('#{$g['divId']} #IntervalorDias').show();
						$('#{$g['divId']} #IntervalorDias').focus();
					} else {
						$('#{$g['divId']} #IntervalorDias').val('').hide();
						$('#{$g['divId']} #diaFixo').show();
						$('#{$g['divId']} #diaFixo').focus();
					}
				});
				$('#{$g['divId']} #selecionaDias').trigger('change');

				$('#{$g['divId']} #selecionaPrimeiroVencimento').change(function() {
					if ($(this).val() == 'Dias') {
						$('#{$g['divId']} #primeiroVencimentoData').val('').hide();
						$('#{$g['divId']} #primeiroVencimentoNum').show();
						$('#{$g['divId']} #primeiroVencimentoNum').focus();
					} else {
						$('#{$g['divId']} #primeiroVencimentoNum').val('').hide();
						$('#{$g['divId']} #primeiroVencimentoData').show();
						$('#{$g['divId']} #primeiroVencimentoData').focus();
					}
				});
				$('#{$g['divId']} #selecionaPrimeiroVencimento').trigger('change');
				/*
				// calcula e mostra o valor das parcelas (simulação)
				var changeHandler = function() {
					$.getJSON('/ven/ven003/form_func-ajax/calcularParcelaSimulacao/?ajax=true=' + '&' + $("#{$formId}").serialize() + '&callback=?', function(request) {
						if (request.code) {
							//divRefresh("{$r_divReload}");
							$('#{$g['divId']} #valorParcelaSimulacao').val(request.data);
						} else {
							var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
    						dialogConfirm("Erro!", request.data, buttons)
						}
					});
				}
				$('#{$g['divId']} #Total_Parcelas').change(changeHandler).keyup(changeHandler);
				*/
		    } </script></div>
EOT;

		/**
		 * PARTE 3: Processamento do formulário (se foi efnviado)
		 * 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		 */
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
				// Form sem erros
			} else if ($formCodigo == 2) {
				// Gerar Boleto
				$retorno = $VEN003->atualizarParcelas($form);
				if ($retorno === true) {
					$alert = gGeraAlertMsg('Operação OK', 'Parcelas cadastradas com sucesso.', false);
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								if (typeof jQuery != 'undefined') {
                                   //divRefresh("{$g['divId']}", true);
                                   divRefresh('{$form->campoValorEnviado('divReload')}', true);
                                   //divRefresh('{$g['divId']}', true);
                           	}
						} </script></div>
EOT;
				} else {
					echo gGeraAlertMsg('Error', 'Erro ao gerar Pacelas:<br />' . $retorno);
				}
			}
		}  else {
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
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk, false, $form_parent, $form_parent_valor);

		/**
		 * PARTE 5: Exibir o formulário
		 */
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');

		$sqlD024 = mysql_query("SELECT D024_Instrucao_Pagamento, D024_Prazo_Parcela, D024_Dias_Vencimento, D024_Quantidade_Parcelas, D027_Portador FROM D024 LEFT JOIN D027 ON D027_Id=D024_D027_Id LEFT JOIN T007 ON T007_D024_Id = D024_Id WHERE T007_Id = '{$T007_Id}'");
		$D024 = mysql_fetch_array($sqlD024);
		$obs='';
		$obs .=	empty($D024['D024_Prazo_Parcela']) 				? '' : " 1º Venc: ".$D024['D024_Prazo_Parcela']."<br />";
		$obs .= empty($D024['D024_Dias_Vencimento']) 			? '' : " Dia Fixo/Intervalo: ".$D024['D024_Dias_Vencimento']."<br />";
		$obs .= empty($D024['D024_Quantidade_Parcelas']) 		? '' : " Nº Parcelas: ".$D024['D024_Quantidade_Parcelas']."<br />";
		$obs .= empty($D024['D027_Portador']) 					? '' : " Portador: ".$D024['D027_Portador']."<br />";
		echo <<<EOT
			<div style="border: 1px solid silver; margin: 10px 15px 0 450px; padding: 5px; height: 110px; border-radius:5px; color: var(--BGVermelho); overflow: auto;">
				<span style="font-weight: bold; color: #000;">Informação de Pagamento</span><br> {$obs}{$D024['D024_Instrucao_Pagamento']}	
			</div>
EOT;

	break;

	case 'ven003FormCancelarNotaEmitida':
		global $g;

		$T007_Id = isset($r_T007_Id) ? $r_T007_Id : false;

		echo $T007_Id;
		die();
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;
		
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
		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = 'D047';
		$form_campo_pk = 'D047_Id';		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'ven001FormCancelarOrcamentoBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/ven/ven001/form/ven001FormCancelarOrcamento/';
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					//Campo normal (do banco)
					'T003_D047_Id' => array('name' => 'T003_D047_Id', 'label' => '* Motivo', 'type' => 'select', 
						'select' =>  gGeraSelect('D047','D047_Id','D047_Descricao_Motivo','',true),
					),
					'T003_Observacao_Orcamento_Perdido' => array('name' => 'T003_Observacao_Orcamento_Perdido', 'label' => '* Observação', 'type' => 'textarea', 'length' => array(3,50)),
					'T003_Id' => array('name' => 'T003_Id', 'value'=>@$r_T003_Id, 'label' => 'Observação', 'type' => 'hidden', 'length' => array(3,50)),
				)
			)
		);
        $form = new Formulario('form-cad046FormPrincipal');
		$form->definirFormularioArray($formArray);

		/**
		 * PARTE 3: Processamento do formulário (se foi enviado)
		 * E carregamento dos valores (se não foi enviado parametro acaoId)
		 */
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
			// Form sem erros
			} else if ($formCodigo == 2) {
				$retorno = $VEN001->cancelarOrcamento($form);
				if ($retorno !== true) {
					echo gGeraAlertMsg('Erro', $retorno);	
				} else {
					echo gGeraAlertMsg('Operação OK', "Orçamento Cancelado com Sucesso");	
					echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										divRefresh('{$g['divIdParent']}', true);
									}, 150);
  								});
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
							} </script></div>
EOT;
				}
			}
		} else {
			// Carrega os valores caso seja um update
			if (!empty($r_acaoId)) {
				$processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $r_acaoId);
				// Mostra erros de formulário uteis ao desenvolvedor
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
		$gerado = gGeraCamposForm($form, $tab, $r_acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

		/**
		 * PARTE 5: Exibir o formulário
		 */
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('form.tpl');

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="ven001FormCancelarOrcamentoBtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;
	
	case 'ven003FormReferenciada':
		$T007_Id = $r_T007_Id;
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
		$form_tabela = 'T132';
		$form_campo_pk = 'T132_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'ven003FormIncluirNotaReferenciada';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/ven/ven003/form/ven003FormReferenciada/';

		//form editor
		$form_editor = array(
			'nfe' => '{"T132_Chave_Acesso":{"top":7,"left":28,"width":326,"hidden":0}}',
			'nf' => '{"T132_UF_IBGE":{"top":76,"left":308,"width":72,"hidden":0},"T132_Ano_Mes":{"top":30,"left":79,"width":72,"hidden":0},"T132_Cnpj":{"top":76,"left":48,"width":152,"hidden":0},"T132_Modelo":{"top":53,"left":87,"width":72,"hidden":0},"T132_Serie":{"top":7,"left":353,"width":72,"hidden":0},"T132_Numero_Nota_Fiscal":{"top":7,"left":31,"width":152,"hidden":0}}',
			'cupom' => '{"T132_Cupom_Numero_EFC":{"top":7,"left":30,"width":136,"hidden":0},"T132_Cupom_Numero_COO":{"top":35,"left":21,"width":136,"hidden":0},"T132_Cupom_Modelo":{"top":62,"left":87,"width":336,"hidden":0}}'
		);

		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('formReferenciada_ven003');
		$formArray = array(
			'campos' => array(
				'nfe' => array(
					'T132_Chave_Acesso' => array('name' => 'T132_Chave_Acesso', 'label'=>'Chave de Acesso', 'type' => 'text','length' => array(50,60)),
					'T132_T007_Id' => array('name' => 'T132_T007_Id', 'value' => @$r_T007_Id, 'type' => 'hidden', 'label' => 'T132_T007_Id', 'length' => array(30,30)),
				),
				'nf' => array(
					'T132_UF_IBGE' => array('name' => 'T132_UF_IBGE', 'label' => 'Cód. UF IBGE', 'type' => 'text','length' => array(50,60)),
					'T132_Ano_Mes' => array('name' => 'T132_Ano_Mes', 'label'=>'Ano/mês', 'type' => 'text','length' => array(4,4), 'extra' => 'placeholder="AAMM"'),
					'T132_Cnpj' => array('name' => 'T132_Cnpj', 'label' => 'CNPJ Emitente', 'type' => 'text','length' => array(14,14)),
					'T132_Modelo' => array('name' => 'T132_Modelo', 'label' => 'Modelo', 'type' => 'text','length' => array(50,60)),
					'T132_Serie' => array('name' => 'T132_Serie', 'label' => 'Série', 'type' => 'text','length' => array(3,3)),
					'T132_Numero_Nota_Fiscal' => array('name' => 'T132_Numero_Nota_Fiscal', 'label' => 'Número nota fiscal', 'type' => 'text','length' => array(50,60)),
				),
				'cupom' => array(
					'T132_Cupom_Numero_EFC' => array('name' => 'T132_Cupom_Numero_EFC', 'label' => 'Número de ordem<br> seqüencial do ECF', 'type' => 'text','length' => array(50,60)),
					'T132_Cupom_Numero_COO' => array('name' => 'T132_Cupom_Numero_COO', 'label' => 'Contador de Ordem<br> de Operação (COO)', 'type' => 'text','length' => array(50,60)),
					'T132_Cupom_Modelo' => array('name' => 'T132_Cupom_Modelo', 'label' => 'Modelo', 'type' => 'select', 'select' =>
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => '2B - Cupom Fiscal emitido por máquina registradora (não ECF)', 'value' => '2B'),
							array('title' => '2C - Cupom Fiscal PDV', 'value' => '2C'),
							array('title' => '2D - Cupom Fiscal (emitido por ECF)', 'value' => '2D'),
						)
					),
				),
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
				$g['conteudo']['pagina'] = gGeraAlertMsgErroForm($form);
				// Form sem erros
			} else if ($formCodigo == 2) {
				$retorno = $VEN003->incluirNotaReferenciada($form);
				$acaoId = $g['mysqlLastId'];
				if ($retorno !== true) {
					echo gGeraAlertMsg('Operação ERRO', $retorno);
				} else {
					echo gGeraAlertMsg('Operação OK', 'Registro Incuido com Sucesso!');
				}
				echo  <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						divRefresh('{$g['divIdParent']}', true);
						$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
					} </script></div>
EOT;
			}
		} else {
			// Carrega os valores caso seja um update
			if (!empty($acaoId)) {
				$processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $acaoId);
				// mostra erros de formulário uteis ao desenvolvedor
				if ($processado !== true) {
					reset($form->formArray['campos']);
					if ($tab == key($form->formArray['campos'])) {
						echo  $processado;
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
		echo  $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		echo  $g['smarty']->fetch('formEditor.tpl');

		/**
		* Botões
		*/
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo  $g['smarty']->fetch('buttons-floatBottom.tpl');
		// Ações dos botões
		// A ação do botão Enviar é definido dentro do formulário
	break;
			
	case 'ven003EditarDadosNota':
		global $g;
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;
		
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
		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = 'T007';
		
		$form_campo_pk = 'T007_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'ven003EditarDadosNotaBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/ven/ven003/form/ven003EditarDadosNota/';
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					'T007_Codigo_Cliente' => array('name' => 'T007_Codigo_Cliente', 'label' => 'Codigo Cliente', 'type' => 'text', 'length' => array(30,40)),
					'T007_Cliente_Razao' => array('name' => 'T007_Cliente_Razao', 'label' => 'Cliente Razão', 'type' => 'text', 'length' => array(30,40)),
					'T007_Cliente_CNPJ_CPF' => array('name' => 'T007_Cliente_CNPJ_CPF', 'label' => 'CNPJ/CPF', 'type' => 'text', 'length' => array(30,40)),
					'T007_Cliente_Inscricao_Estadual' => array('name' => 'T007_Cliente_Inscricao_Estadual', 'label' => 'I.E', 'type' => 'text', 'length' => array(30,40)),
					'T007_Cliente_Inscricao_Suframa' => array('name' => 'T007_Cliente_Inscricao_Suframa', 'label' => 'Suframa', 'type' => 'text', 'length' => array(30,40)),
					'T007_Cliente_Endereco' => array('name' => 'T007_Cliente_Endereco', 'label' => 'Endereço', 'type' => 'text', 'length' => array(30,40)),
					'T007_Cliente_Endereco_Numero' => array('name' => 'T007_Cliente_Endereco_Numero', 'label' => 'Numero Endereço', 'type' => 'text', 'length' => array(30,40)),
					'T007_Cliente_Complemento' => array('name' => 'T007_Cliente_Complemento', 'label' => 'Complemento', 'type' => 'text', 'length' => array(30,40)),
					'T007_Cliente_Bairro' => array('name' => 'T007_Cliente_Bairro', 'label' => 'Bairro', 'type' => 'text', 'length' => array(30,40)),
					'T007_Cliente_CEP' => array('name' => 'T007_Cliente_CEP', 'label' => 'Cep', 'type' => 'text', 'length' => array(30,40)),
					'T007_Cliente_Cidade' => array('name' => 'T007_Cliente_Cidade', 'label' => 'Cidade', 'type' => 'text', 'length' => array(30,40)),
					'T007_Cliente_Estado' => array('name' => 'T007_Cliente_Estado', 'label' => 'Estado', 'type' => 'text', 'length' => array(30,40)),
					'T007_Cliente_Pais' => array('name' => 'T007_Cliente_Pais', 'label' => 'País', 'type' => 'text', 'length' => array(30,40)),
					'T007_Cliente_Codigo_Cidade_IBGE' => array('name' => 'T007_Cliente_Codigo_Cidade_IBGE', 'label' => 'Codigo IBGE Cidade', 'type' => 'text', 'length' => array(30,40)),
					'T007_Cliente_Codigo_Estado_IBGE' => array('name' => 'T007_Cliente_Codigo_Estado_IBGE', 'label' => 'Codigo IBGE Estado', 'type' => 'text', 'length' => array(30,40)),
					'T007_Cliente_Codigo_Pais' => array('name' => 'T007_Cliente_Codigo_Pais', 'label' => 'Codigo País', 'type' => 'text', 'length' => array(30,40)),
					'T007_Cliente_Telefone' => array('name' => 'T007_Cliente_Telefone', 'label' => 'Telefone', 'type' => 'text', 'length' => array(30,40)),
					'T007_IPV_Cliente' => array('name' => 'T007_IPV_Cliente', 'label' => 'IPV Cliente', 'type' => 'text', 'length' => array(30,40)),
					'T007_Transportadora_CNPJ' => array('name' => 'T007_Transportadora_CNPJ', 'label' => 'CNPJ', 'type' => 'text', 'length' => array(30,40)),
					'T007_Empresa_Usuaria_CRT' => array('name' => 'T007_Empresa_Usuaria_CRT','label'=>'Regime Tributário','extra' =>'style="width:300px"', 'type' => 'select', 
						'select' => array(
							array('title' => '...', 'value' => ""),
							array('title' => '1 - Simples Nacional', 'value' => "1"),
							array('title' => '2 - Simples Nacional é excesso de sublimite de receita bruta', 'value' => "2"),
							array('title' => '3 - Regime Normal', 'value' => "3", 'selected' => true)
						)
					),
					'T007_Flag_Destino_Operacao' => array('name' => 'T007_Flag_Destino_Operacao', 'label' => 'Destino Operação', 'type' => 'text','length' => array(1,1)),
					'T007_Flag_Indicador_Presenca' => array('name' => 'T007_Flag_Indicador_Presenca', 'label' => 'Indicador Presença', 'type' => 'text','length' => array(1,1)),
					//'T007_Id' => array('name' => 'T007_Id', 'label' => 'T007_Id', 'type' => 'text', 'length' => array(30,40)),
				)
			)
		);
		               
        $form = new Formulario('form-ven003FormDadosNota');
		$form->definirFormularioArray($formArray);

		/**
		 * PARTE 3: Processamento do formulário (se foi enviado)
		 * E carregamento dos valores (se não foi enviado parametro acaoId)
		 */
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
			// Form sem erros
			} else if ($formCodigo == 2) {
				// Insert ou Update?
				if (!empty($r_acaoId)) {
					// Update
					if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $r_acaoId, $form)) {
						// Fecha a janela, recarrega o parent (grid) e emite uma mensagem de alerta
						echo gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!', true);
					}
				} else {
					// Insert
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						// Fecha a janela, recarrega o parent (grid), emite uma mensagem de alerta e abre o registro para alteração (o que acabou de ser incluido)
						$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								// Fecha a janela, recarrega o grid e abre a alteração
								//$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								divRefresh('{$g['divIdParent']}', true);
							} </script></div>
EOT;
					}
				}
			}
		} else {
			// Carrega os valores caso seja um update
			if (!empty($r_acaoId)) {
				$processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $r_acaoId);
				// Mostra erros de formulário uteis ao desenvolvedor
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
		$gerado = gGeraCamposForm($form, $tab, $r_acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

		/**
		 * PARTE 5: Exibir o formulário
		 */
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('form.tpl');

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="ven003EditarDadosNotaBtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'ven003VarsLayout':
		global $g;
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;
		
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
		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = 'T007';
		
		$form_campo_pk = 'T007_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'ven003EditarLayout';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/ven/ven003/form/ven003VarsLayout/';

		//form_editor
		$form_editor = array('geral' => '{"T007_Config_Vars_Danfe":{"top":7,"left":16,"height":83,"width":440,"hidden":0}}');
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					'T007_Config_Vars_Danfe' => array('name' => 'T007_Config_Vars_Danfe', 'label' => '', 'type' => 'textarea', 'uppercase' => false),
				)
			)
		);
		               
        $form = new Formulario('form-ven003VarsLayout');
		$form->definirFormularioArray($formArray);

		/**
		 * PARTE 3: Processamento do formulário (se foi enviado)
		 * E carregamento dos valores (se não foi enviado parametro acaoId)
		 */
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
			// Form sem erros
			} else if ($formCodigo == 2) {
				// Insert ou Update?
				if (!empty($r_acaoId)) {
					// Update
					if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $r_acaoId, $form)) {
						// Fecha a janela, recarrega o parent (grid) e emite uma mensagem de alerta
						echo gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!', true);
					}
				} else {
					// Insert
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						// Fecha a janela, recarrega o parent (grid), emite uma mensagem de alerta e abre o registro para alteração (o que acabou de ser incluido)
						$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								// Fecha a janela, recarrega o grid e abre a alteração
								//$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								divRefresh('{$g['divIdParent']}', true);
							} </script></div>
EOT;
					}
				}
			}
		} else {
			// Carrega os valores caso seja um update
			if (!empty($r_acaoId)) {
				$processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $r_acaoId);
				// Mostra erros de formulário uteis ao desenvolvedor
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
		$gerado = gGeraCamposForm($form, $tab, $r_acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

		/**
		 * PARTE 5: Exibir o formulário
		 */
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="ven003EditarLayout">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;
    
    case 'ven003FormServicos':
        /**
         * PARTE 1: Variáveis envidas para o formulário
         */
        // Variável indica qual tab do form será exibida
        $tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
        // Utilizada em UPDATE, é o ID do registro que está sendo editado
        $acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
        $r_T007_Id = isset($r_ven003FormServicosForm) ? $r_ven003FormServicosForm['T203_T007_Id'] : $r_T007_Id;

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
        $form_tabela = 'T203';
        $form_campo_pk = 'T203_Id';
        // ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
        $enviar_btnId = array('ven003FormServicosBtnAdd', 'ven003FormServicosBtnCln', 'ven003FormServicosBtnNew');
        // Form que será executado ao clicar no botão
        $enviar_acaoHref = '/ven/ven003/form/ven003FormServicos/';

        //form editor
        $form_editor = array('geral' => '{"Procura_Servico":{"top":7,"left":53,"width":248,"hidden":0},"T203_Titulo":{"top":30,"left":65,"width":264,"hidden":0},"T203_Descricao":{"top":53,"left":34,"width":264,"hidden":0},"T203_Valor_Unitario":{"top":99,"left":40,"width":100,"hidden":0},"T203_Tempo_Estimado":{"top":122,"left":6,"width":75,"hidden":0},"ven001FormVeiculosBtnNew":{"top":143,"left":384,"width":null,"hidden":0},"T203_D116_Id":{"top":145,"left":48,"width":280,"hidden":0},"ven003FormServicosBtnCln":{"top":166,"left":256,"width":null,"hidden":0},"ven003FormServicosBtnAdd":{"top":166,"left":320,"width":null,"hidden":0}}');

        // Campo: Procura_Servico
        $pesquisaGridExtra = <<<EOT
            left join D049 on D049_D001_Id=D001_Id
            left join D009 on D009_D049_Id=D049_Id
            left join D082 on D082_Id=D049_D082_Id
            left join D037 on D037_Id=D001_D037_Id
            where D009_C004_Id = '{$g['empresaAtual']}'
            order by D001_Codigo_Produto
EOT;
        $pesquisaGrid = array(
            'D112',                                                                                                // tabela
            'D112_Id',                                                                                            // chave primaria
            'D112_Titulo,D112_Descricao',                                                                        // campos
            '',                                                                                                    // extras sql
            'Pesquisa de Serviço',                                                                                // título da janela
            'Título,Descrição',                                                                                    // descrição dos campos
            'campoProcuraServico',                                                                                // callback js
            $g['divId']                                                                                            // divId
        );
        //$sqlVeiculo = mysql_query("select T007_D024_Id,T007_D116_Id from T007 where T007_Id='{$r_T007_Id}'");
        //$resultVeiculo = mysql_fetch_array($sqlVeiculo);
        // novo formulário
        $form = new Formulario('ven003FormServicosForm');
        // Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
        $formArray = array(
            'campos' => array(
                'geral' => array(
                'Procura_Servico' => array('name' => 'Procura_Servico' , 'label' => 'Serviço', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGrid, 'length' => array(50,30), 'reject' => true),
                    'T203_Titulo' => array('name' => 'T203_Titulo','label'=>'Título','type' => 'text', 'length' => array(50,30)),
                    'T203_Descricao' => array('name' => 'T203_Descricao','label'=>'Descrição','type' => 'textarea', 'length' => array(1,46)),
                    //'T203_Quantidade' => array('name' => 'T203_Quantidade','label'=>'Quantidade','type' => 'text', 'length' => array(10,8)),
                    //'T203_Valor_Unitario' => array('name' => 'T203_Valor_Unitario','label'=>'Valor Unitário','type' => 'moeda', 'length' => array(15,10)),
                    'T203_Valor_Unitario' => array('name' => 'T203_Valor_Unitario','label'=>'Valor Total','type' => 'moeda', 'length' => array(15,10)),
                    'T203_Tempo_Estimado' => array('name' => 'T203_Tempo_Estimado','label'=>'Tempo Estimado','type' => 'tempo', 'length' => array(10,10)),
                    'T203_D116_Id' => array('name' => 'T203_D116_Id', 'label'=>'Veículo', 'extra'=>'style="width:280px;"', 'type' => 'select',
                        'select' => gGeraSelect('D116', 'D116_Id','D116_Placa,D133_Modelo,D134_Cor', ' LEFT JOIN D133 on D133_Id=D116_D133_Id LEFT JOIN D134 on D134_Id=D116_D134_Id WHERE D116_D024_Id=(SELECT T007_D024_Id FROM T007 WHERE T007_Id='.@$r_T007_Id.')', true),
                        'selectRefresh' => array('D116', 'D116_Id', 'D116_Placa,D133_Modelo,D134_Cor', ' LEFT JOIN D133 on D133_Id=D116_D133_Id LEFT JOIN D134 on D134_Id=D116_D134_Id WHERE D116_D024_Id=(SELECT T007_D024_Id FROM T007 WHERE T007_Id='.@$r_T007_Id.')',true)
                    ),
                    'ven001FormVeiculosBtnNew' => array('name' => 'ven001FormVeiculosBtnNew', 'type'=>'submit', 'reload' => false, 'value' => 'Veículos', 'align' => 'right', 'style' => 'float:right;', 'reject' => true),
                    //'T203_Hora_Inicio' => array('name' => 'T203_Hora_Inicio','label'=>'Hora Início','type' => 'text', 'length' => array(10,10)),
                    //'T203_Hora_Fim' => array('name' => 'T203_Hora_Fim','label'=>'Hora Fim','type' => 'text', 'length' => array(10,10)),
                    //'T203_Valor_Unitario' => array('name' => 'T203_Valor_Unitario','label'=>'Valor total', 'value'=>'1', 'type' => 'hidden'),
                )
            )
        );
        // Botão Limpar / Novo Registro
        $formArray['campos']['geral']['T203_T007_Id'] = array('name' => 'T203_T007_Id', 'type'=>'hidden', 'value' => @$r_T007_Id);
        if (!empty($acaoId)) {
            $formArray['campos']['geral']['ven003FormServicosBtnNew'] = array('name' => 'ven003FormServicosBtnNew', 'type'=>'submit', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
        }
        $formArray['campos']['geral']['ven003FormServicosBtnCln'] = array('name' => 'ven003FormServicosBtnCln', 'type'=>'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
        $formArray['campos']['geral']['ven003FormServicosBtnAdd'] = array('name' => 'ven003FormServicosBtnAdd', 'type'=>'button', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
        $formArray['campos']['geral']['T203_Quantidade'] = array('name' => 'T203_Quantidade', 'type'=>'hidden', 'value' => 1);
        $formArray['campos']['geral']['T203_Status'] = array('name' => 'T203_Status', 'type'=>'hidden', 'value' => 'P');
        $formArray['campos']['geral']['T203_D110_Id'] = array('name' => 'T203_D110_Id', 'type'=>'hidden');
        $formArray['campos']['geral']['T203_D112_Id'] = array('name' => 'T203_D112_Id', 'type'=>'hidden');
        $formArray['campos']['geral']['T203_Hora_Inicio'] = array('name' => 'T203_Hora_Inicio', 'type'=>'hidden', 'value'=>'0000-00-00 00:00:00');
        $formArray['campos']['geral']['divReload'] = array('name' => 'divReload', 'type'=>'hidden','value' => @$r_divReload, 'reject' => true);
        $form->definirFormularioArray($formArray);

        $sql = "SELECT T007_D024_Id FROM T007 WHERE T007_Id = '{$r_T007_Id}';";
        $resultado = mysql_query($sql);
        $row = mysql_fetch_array($resultado);
        echo <<<EOT
            <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                $('#ven001FormVeiculosBtnNew').unbind('click');
                $('#ven001FormVeiculosBtnNew').unbind('click').bind('click', function(data) {
                    var id = '{$row['T007_D024_Id']}';
                    abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Incluir Veículo', '/cad/cad001/content/cad001ContentVeiculosIncluir/', '&naoRefreshParent=true&D024_Id=' + encodeURIComponent(id) + '&tabela=D024' + '&tab=veiculos', [300,350]);
                    return false;
                });
            } </script></div>
EOT;


        /**
         * PARTE 3: Processamento do formulário (se foi efnviado)
         *            E carregamento dos valores (se não foi enviado e possui acaoId)
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
                        echo <<<EOT
                            <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                                divRefresh('{$form->campoValorEnviado('divReload')}', true);
                                divSetarAcaoId('{$g['divId']}', '');
                                divRefresh('{$g['divId']}', true);
                            } </script></div>
EOT;
                    }
                } else {
                    // Insert
                    if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
                        //$retorno = $VEN003->insereServico($form, $g['mysqlLastId']);
                        //$erro = ($retorno === true) ? '' : "alert('$retorno');";
                        echo <<<EOT
                            <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                                divRefresh('{$form->campoValorEnviado('divReload')}', true);
                                divRefresh('{$g['divId']}', true);
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

        /**
         * Botões
         */
        // Definição dos botões
        $botoesMid = '';
        $botoesEsq = '';
        $botoesDir = '';
        //$botoesDir .= '<button type="submit" id="content_form1_btn">Enviar</button>';
        //$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
        $g['smarty']->assign('botoesId', $g['divId']);
        $g['smarty']->assign('botoesEsq', $botoesEsq);
        $g['smarty']->assign('botoesMid', $botoesMid);
        $g['smarty']->assign('botoesDir', $botoesDir);
        $g['smarty']->display('buttons-floatBottom.tpl');
            
        break;
        
    case 'ven003FormAplicarDesconto':
        	/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;
		
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
		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = 'T007';
		$form_campo_pk = 'T007_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'ven003AplicarDescontoBtnExecutar';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/ven/ven003/form/ven003FormAplicarDesconto/';
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					'T007_Id' => array('name' => 'T007_Id', 'label' => 'T007_Id', 'type' => 'hidden', 'length' => array(40, 80), 'reject' => true, 'value' => $r_T007_Id),
     				'Desconto' => array('name' => 'Desconto', 'label' => 'Aplicar Desconto %', 'type' => 'moeda', 'length' => array(40, 80), 'reject' => true),
				)
			)
		);
        $form = new Formulario('form-ven003FormAplicarDesconto');
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
				// Ações dos botões
				$novoIdTelaForm = uniqid();
				echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
						var \$buttons = {
							"Sim": function() {
								var T007_Id = $('#{$g['divId']} #T007_Id').val();
								var Desconto = $('#{$g['divId']} #Desconto').val();
								$('#{$g['divId']}').showLoading();
								$.getJSON('/ven/ven003/form_func-ajax/ven003aplicarDesconto/?ajax=true&T007_Id=' + encodeURIComponent(T007_Id) + '&Desconto=' + encodeURIComponent(Desconto) + '&callback=?', function(request) {
									if (request.code) {
										var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					    				dialogConfirm('Ok', request.data, \$buttons);
					    				divRefresh('{$g['divIdParent']}');
					    				fecharJanela('{$g['divId']}');
									} else {
										var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					    				dialogConfirm('Erro', request.data, \$buttons);
									}
									$('#{$g['divId']}').hideLoading();
								});
								$(this).dialog("destroy").remove();
							},
							"Não": function() { $(this).dialog("destroy").remove(); } 
						};
						dialogConfirm('Aplicar Desconto', 'Você tem certeza que deseja aplicar o desconto? ', \$buttons);
					} </script></div>
EOT;
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
		$gerado = gGeraCamposForm($form, $tab, $r_acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

		/**
		 * PARTE 5: Exibir o formulário
		 */
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('form.tpl');
    break;

    case 'ven003FormDetalhesCheque':
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
		$formMultiTab = false;
		if ($formMultiTab) {
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}
				
		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'T118';
		$form_campo_pk = 'T118_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'ven003FormDetalhesCheque_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/ven/ven003/form/ven003FormDetalhesCheque/';
				
		// novo formulário
		$form = new Formulario('ven003ContentDetalhesCheque');

		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T118_Numero_Cheque' => array('name' => 'T118_Numero_Cheque', 'label' => 'Número', 'type' => 'text', 'length' => array(20, 120)),
					'T118_Data_Cheque' => array('name' => 'T118_Data_Cheque', 'label' => 'Data', 'type' => 'data', 'length' => array(20, 120)),					
					'T118_Agencia_Cheque' => array('name' => 'T118_Agencia_Cheque', 'label' => 'Agência', 'type' => 'text', 'length' => array(20, 120)),
					'T118_Conta_Corrente_Cheque' => array('name' => 'T118_Conta_Corrente_Cheque', 'label' => 'Conta Corrente', 'type' => 'text', 'length' => array(20, 120)),					
					'T118_Banco_Cheque' => array('name' => 'T118_Banco_Cheque', 'label' => 'Banco', 'type' => 'text', 'length' => array(50, 120)),									
					'T118_Nome_Cheque' => array('name' => 'T118_Nome_Cheque', 'label' => 'Nome', 'type' => 'text', 'length' => array(50, 120)),					
					'T118_Cidade_Cheque' => array('name' => 'T118_Cidade_Cheque', 'label' => 'Cidade', 'type' => 'text', 'length' => array(50, 120)),
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
				if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
					// Imprime uma mensagem e fecha a janela
					$alert = gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!', false);
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
								setTimeout(function() {
									{$alert}
								}, 150);
  							});
							$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
							divRefresh('{$g['divIdParent']}', true);
						} </script></div>
EOT;
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
		//echo '<div style="padding:5px">Estorno de título - ID: '.$acaoId.'</div>';
		echo $gerado['pagina'];				
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('form.tpl');
				
	break;

	case 'ven003FormNotaTransporte':
		global $g;
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		// Utiliza para ver se é devolução ou nota espelho
		$acao = (isset($_REQUEST['acao'])) ? $_REQUEST['acao'] : "";

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
		$enviar_btnId = 'ven003FormNotaTransporte_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/ven/ven003/form/ven003FormNotaTransporte/';
				
		// novo formulário
		$form = new Formulario('ven003FormNotaTransporte');

		$pesquisaGridExtra = <<<EOT
			left join D020 on D020_Id=D024_D020_Id 
			left join D018 on D018_Id=D024_D018_Id 
			WHERE D024_Flag_Ativo='S' AND D024_Flag_Pre_Cadastro != 'S' {$restricaoVendedorCliente}
			order by D024_Nome_Empresa
EOT;

		$pesquisaGrid = array(
			'D024',																											// tabela
			'D024_Id',																										// chave primaria
			'D024_Id,D024_Nome_Empresa,D024_Nome_Fantasia,D020_Nome_Cidade,D018_UF,D024_Cnpj,D024_DDD_Telefone_1,D024_Telefone_1',	// campos
			$pesquisaGridExtra,																								// extras sql
			'Pesquisa de Cliente',																							// título da janela
			'Código,Cliente,Fantasia,Cidade,UF,CNPJ,DDD,Telefone',															// descrição dos campos
			'ven003ProcuraCliente',																							// callback js
			$g['divId'],																									// divId
			'',																												// parametro extra
			array(																											// parametros para cada campo do grid
				array('colunaAlinhar' => 'center'),
				array('style' => 'font-weight:bold'),
				array(),
				array(),
				array(),
				array(),
				array(),
				array()
			)
		);

		$cfop = 0;
		$obs = '';
		$D024_Id = 0;
		$D024_Nome_Empresa = '';

		$form_editor = array(
			'geral' => '{"Observacao":{"top":0,"left":0,"height":70,"width":424,"hidden":1},"C004_Id":{"top":6.9393463134765625,"left":31.433837890625,"width":431,"hidden":0},"Nome_Empresa":{"top":29.947921752929688,"left":15.333984375,"width":410.039216,"hidden":0},"T007_Flag_ACP":{"top":52.94117736816406,"left":30.30029296875,"width":431,"hidden":0},"T007_Flag_Finalidade_NFe":{"top":75.94975280761719,"left":24.2493896484375,"width":432,"hidden":0},"T007_Flag_Indicador_Presenca":{"top":98.94300842285156,"left":0.56683349609375,"width":432,"hidden":0},"T007_Flag_Destino_Produto":{"top":121.95158386230469,"left":37.270263671875,"width":431,"hidden":0},"D006_CFOP":{"top":144.9907989501953,"left":46.875,"width":431,"hidden":0},"utilizarValoresDespesas":{"top":167.99940490722656,"left":257.7666015625,"width":13,"hidden":0},"aplicarRegra":{"top":190.9467010498047,"left":276.68505859375,"width":13,"hidden":0},"atualizarAliquotasReformaTributaria":{"top":213.9859161376953,"left":243.94915771484375,"width":13,"hidden":0},"observacaoCFOP":{"top":233.9460906982422,"left":36.01409912109375,"width":null,"hidden":0},"ven003CadastrarCliente":{"top":257.99632263183594,"left":79.993896484375,"width":null,"hidden":0},"ven003FormNotaTransporte_btn":{"top":257.99632263183594,"left":383.9920654296875,"width":null,"hidden":0}}'
		);

		// Caso seja um emissão de nota a partir de um cupom fiscal
		$sql = "SELECT T007_Flag_ACP, T007_Numero_Nota_Fiscal, D024_Id, D024_Nome_Empresa FROM T007 LEFT JOIN D024 ON D024_Id=T007_D024_Id WHERE T007_Id = '{$r_T007_Id}'";
		$resT007 = mysql_query($sql);
		$rowT007 = mysql_fetch_assoc($resT007);
		if ($rowT007['T007_Flag_ACP'] == 4) {
			$sql = "SELECT D006_Id FROM D006 WHERE D006_Codigo_CFOP = '5.929'";
			$resD006 = mysql_query($sql);
			if (mysql_num_rows($resD006) > 0) {
				$rowD006 = mysql_fetch_assoc($resD006);
				$cfop  = $rowD006['D006_Id'];
			}
			$D024_Id = $rowT007['D024_Id'];
			$D024_Nome_Empresa = $rowT007['D024_Nome_Empresa'];
			$obs = 'Referente Cupom Fiscal Nr. ' . $rowT007['T007_Numero_Nota_Fiscal'];
		}

		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'Nome_Empresa' => array('name' => 'Nome_Empresa', 'label' => 'Destinatário', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGrid, 'reject' => true, 'length' => array(75, 75), 'value' => $D024_Nome_Empresa),
					'D024_Id' => array('name' => 'D024_Id', 'type' => 'hidden', 'reject' => true, 'value' => $D024_Id),
					'D006_CFOP' => array('name' => 'D006_CFOP', 'label' => 'CFOP', 'type' => 'select', 
						'select' => gGeraSelect('D006', 'D006_Id', "D006_Codigo_CFOP, D006_Descricao", "WHERE IFNULL(D006_Flag_Inativo,'N')!='S' ORDER BY D006_Codigo_CFOP", true, $cfop),
					'reject' => true),
					'C004_Id' => array('name' => 'C004_Id', 'label' => 'Empresa', 'type' => 'select', 
						'select' => gGeraSelect('C004', 'C004_Id', "C004_Id, C004_Nome_Empresa", "where C004_Flag_Ativo='S' ORDER BY C004_Id", false, $g['empresaAtual']),
					'reject' => true),
					'T007_Flag_ACP' => array('name' => 'T007_Flag_ACP', 'label' => 'Modelo', 'type' => 'select', 
						'select' => array(
										array('title' => '...', 'value' => 0),
										array('title' => 'NF', 'value' => 1),
										array('title' => 'Romaneio', 'value' => 2),
										array('title' => 'ACP', 'value' => 3),
										array('title' => 'Cupom Fiscal', 'value' => 4),
										array('title' => 'NF D1', 'value' => 5),
										array('title' => 'NF-e', 'value' => 6, 'selected' => true),
										array('title' => 'NFS-e', 'value' => 7),							
										array('title' => 'NFC-e', 'value' => 8),							
									),
					'reject' => true),
						'T007_Flag_Finalidade_NFe' => array('name' => 'T007_Flag_Finalidade_NFe', 'label'=>'Finalidade','type' => 'select', 'select' => 
							array(
								array('title' => 'Normal', 'value' => 1),
								array('title' => 'Complementar', 'value' => 2),
								array('title' => 'Ajuste', 'value' => 3),
								array('title' => 'Devolução de mercadoria', 'value' => 4, 'selected' => true),
								array('title' => 'Nota de crédito', 'value' => 5),
								array('title' => 'Nota de débito', 'value' => 6),
							),
						'reject' => true),
					'T007_Flag_Indicador_Presenca' => array('name' => 'T007_Flag_Indicador_Presenca', 'label'=>'Ind. Presença','type' => 'select', 'select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => '0 - Não se aplica (complementar ou de ajuste)', 'value' => 0),
							array('title' => '1 - Operação presencial', 'value' => 1),
							array('title' => '2 - Operação não presencial, pela Internet', 'value' => 2),
							array('title' => '3 - Operação não presencial, Teleatendimento', 'value' => 3),
							array('title' => '4 - NFC-e em operação com entrega a domicílio', 'value' => 4),
							array('title' => '5 - Operação presencial, fora do estabelecimento', 'value' => 5),
							array('title' => '9 - Operação não presencial, outros', 'value' => 9),							
						)
					),			
					'T007_Flag_Destino_Produto' => array('name' => 'T007_Flag_Destino_Produto','label'=>'Destino', 'type' => 'select', 
						'select' => array(
							array('title' => '...', 'value' => ""),
							array('title' => '1 - INDUSTRIALIZAÇÃO', 'value' => "1"),
							array('title' => '2 - REVENDA', 'value' => "2"),
							array('title' => '3 - CONSUMO', 'value' => "3"),
							array('title' => '4 - ESPECIAL', 'value' => "4"),
							array('title' => '5 - BONIFICAÇÃO', 'value' => "5"),
							array('title' => '6 - TROCA', 'value' => "6"),
							array('title' => '7 - DEMONSTRAÇÃO', 'value' => "7"),
							array('title' => '8 - CONSIGNAÇÃO', 'value' => "8"),
							array('title' => '9  - VENDA FUTURA', 'value' => "9"),
							array('title' => '10 - VENDA CONTA E ORDEM', 'value' => "10"),
							array('title' => '11 - VENDA PRÓ-EMPREGO', 'value' => "11"),
							array('title' => '12 - REMESSA PARA CONSERTO', 'value' => "12"),
							array('title' => '13 - REMESSA PARA INDUSTRIALIZAÇÃO', 'value' => "13"),
							array('title' => '14 - OUTRAS SAÍDAS', 'value' => "14"),
							array('title' => '15 - AMOSTRA', 'value' => "15"),
							array('title' => '16 - DOAÇÃO', 'value' => "16"),
							array('title' => '17 - VENDA DE ATIVO IMOBILIZADO', 'value' => "17"),
							array('title' => '18 - TRANSFERÊNCIA ENTRE EMPRESAS', 'value' => "18"),
							array('title' => '19 - REMESSA TRIANGULAR', 'value' => "19"),
							array('title' => '20 - REMESSA VENDA FUTURA', 'value' => "20"),
						)
					),
					'aplicarRegra' => array('name' => 'aplicarRegra', 'label'=>'Aplicar Regra de CFOP e Atualizar Alíquotas', 'type' => 'checkbox', 'checked'=> false, 'reject' => true),
					'utilizarValoresDespesas' => array('name' => 'utilizarValoresDespesas', 'label'=>'Aplicar Valores do Frete e Despesas Acessórias', 'type' => 'checkbox', 'checked'=> false, 'reject' => true),
					'atualizarAliquotasReformaTributaria' => array('name' => 'atualizarAliquotasReformaTributaria', 'label'=>'Atualizar IBS/CBS com a configuração CFOP/NCM', 'type' => 'checkbox', 'checked'=> false, 'reject' => true, 'extra' => 'title="Irá calcular o IBS/CBS pela configuração do CFOP/NCM. Caso a NF de origem já possua dados da Reforma Tributária, os valores serão recalculados e sobrescritos."'),
					'observacaoCFOP' => array('name' => 'observacaoCFOP', 'label'=>'Deixe a CFOP em branco para usar a CFOP de devolução dos produtos da nota','type' => 'titulo', 'reject' => true),
					'Observacao' => array('name' => 'Observacao', 'label' => 'Observação', 'type' => 'textarea', 'length' => array(5, 80), 'reject' => true, 'value' => $obs),
					'ven003FormNotaTransporte_btn' => array('name' => 'ven003FormNotaTransporte_btn', 'value' => 'Gerar Nota Espelho', 'reject' => true, 'type' => 'button', 'reload' => false)
				)	
			)
		);
		$formArray['campos']['geral']['ven003CadastrarCliente'] = array('name' => 'ven003CadastrarCliente', 'type'=>'button', 'reload' => false, 'value' => 'Cadastrar Destinatário', 'align' => 'left', 'reject' => true);
		$form->definirFormularioArray($formArray);
		
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#ven003CadastrarCliente').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Clientes/Fornecedores', '/cad/cad001/content/cad001contentCadastro/','', [960,583]);
				});
			} </script></div>
EOT;
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
				$Trans_D024_Id				  	     = $form->campoValorEnviado('D024_Id');
				$D006_Id 					  	     = $form->campoValorEnviado('D006_CFOP');
				$C004_Id					  	     = $form->campoValorEnviado('C004_Id');
				$T007_Flag_ACP				  	     = $form->campoValorEnviado('T007_Flag_ACP');
				$T007_Flag_Finalidade_NFe 	  	     = $form->campoValorEnviado('T007_Flag_Finalidade_NFe');
				$Trans_Observacao 			  	     = $form->campoValorEnviado('Observacao');
				$T007_Flag_Indicador_Presenca 	     = $form->campoValorEnviado('T007_Flag_Indicador_Presenca');
				$T007_Flag_Destino_Produto	  	     = $form->campoValorEnviado('T007_Flag_Destino_Produto');
				$aplicarRegraCFOP 			  	     = $form->campoValorEnviado('aplicarRegra') == 'on';
				$utilizarValoresDespesas      	     = $form->campoValorEnviado('utilizarValoresDespesas');
				$atualizarAliquotasReformaTributaria = $form->campoValorEnviado('atualizarAliquotasReformaTributaria') == 'on';		
				
				if (isset($r_listaT007s)) {
					$r_listaT007s = json_decode(urldecode($r_listaT007s));
					$r_T007_Id = $r_listaT007s[0];
				} else {
					$r_listaT007s = false;
				}

				$retorno = $VEN003->gerarNotaTransporte($r_T007_Id, $Trans_D024_Id, $D006_Id, $Trans_Observacao, $T007_Flag_Indicador_Presenca, $r_listaT007s, $C004_Id, $T007_Flag_ACP, $acao, $T007_Flag_Finalidade_NFe, $T007_Flag_Destino_Produto, $aplicarRegraCFOP, $utilizarValoresDespesas, $atualizarAliquotasReformaTributaria);
				if($retorno === true){
					if ($r_listaT007s) {
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								fecharJanela('{$g['divId']}');
								divRefresh('{$g['divIdParent']}', true);
							} </script></div>
EOT;
					} else {
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$('#{$g['divId']}').closest('.ui-dialog-content').dialog('close');
								$('#{$r_divIdAberto}').closest('.ui-dialog-content').dialog('close');					
							} </script></div>
EOT;
					}
				} else {
					echo $retorno;
					$row = mysql_fetch_assoc(mysql_query("SELECT D024_Nome_Fantasia FROM D024 WHERE D024_Id = '{$form->campoValorEnviado('D024_Id')}'"));
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							$('#{$g['divId']} #D024_Id').val('{$form->campoValorEnviado('D024_Id')}');
							$('#{$g['divId']} #Nome_Empresa_Pesquisa').val('{$row['D024_Nome_Fantasia']}');
						} </script></div>
EOT;
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
		//echo '<div style="padding:5px">Estorno de título - ID: '.$acaoId.'</div>';
		echo $gerado['pagina'];				
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');		
	break;

	case 'ven003FormAtualizarCFOP':
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
		$formMultiTab = false;
		if ($formMultiTab) {
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}
		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'T007';
		$form_campo_pk = 'T007_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'ven003FormAtualizarCFOP_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/ven/ven003/form/ven003FormAtualizarCFOP/';
        
        //form editor
        $form_editor = array(
            'geral' => '{"linha3":{"top":7,"left":105,"width":null,"hidden":0},"linha":{"top":50,"left":9,"width":null,"hidden":1},"ven003FormAtualizarCFOP_btn":{"top":74,"left":416,"width":null,"hidden":0},"D006_Id":{"top":76,"left":15,"width":368,"hidden":0},"linha2":{"top":96,"left":10,"width":null,"hidden":1},"ven003FormAtualizarCFOPRegras":{"top":120,"left":16,"width":null,"hidden":0}}',
        );

		$descricaoCFOP  = ($g['C031']['descricaoCFOPExibida'] == 'P') ? 'D006_Descricao' : 'IF(D006_Descricao_Comercial !="",D006_Descricao_Comercial,D006_Descricao) as D006_Descricao';

		
		$form = new Formulario('ven003FormAtualizarCFOP');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D006_Id' => array('name' => 'D006_Id', 'label' => 'CFOP', 'type' => 'select', 'reject' => true,
						'select' => gGeraSelect('D006', 'D006_Id', "LPAD(D006_Id[[VIRGULA]]4[[VIRGULA]]0), D006_Codigo_CFOP, {$descricaoCFOP}", 'WHERE IFNULL(D006_Flag_Inativo,"N")!="S" ORDER BY D006_Codigo_CFOP DESC', true, false, '%1 - %2 - %3')
					),
				),
			)
		);

		$formArray['campos']['geral']['ven003FormAtualizarCFOP_btn'] = array('name' => 'ven003FormAtualizarCFOP_btn', 'type'=>'submit', 'reload' => false, 'value' => 'Atualizar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['geral']['linha'] = array('name' => 'linha', 'type'=>'titulo', 'value' => '', 'label' => 'Atualizar os itens do orçamento com a CFOP:', 'reject' => true);
		$formArray['campos']['geral']['linha2'] = array('name' => 'linha2', 'type'=>'titulo', 'value' => '', 'label' => 'Atualizar as CFOPs de acordo com as regras:', 'reject' => true);
		$formArray['campos']['geral']['linha3'] = array('name' => 'linha3', 'type'=>'comentario', 'value' => 'Atenção: Ao escolher uma das opções, a CFOP e as <br />aliquotas dos itens deste orçamento serão alterados.', 'label' => '', 'reject' => true, 'style' => 'color: #FF0000; font-size: 12px;');
		$formArray['campos']['geral']['ven003FormAtualizarCFOPRegras'] = array('name' => 'ven003FormAtualizarCFOPRegras', 'type'=>'button', 'reload' => false, 'value' => 'Aplicar regras CFOP', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$form->definirFormularioArray($formArray);
		
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
			$('#ven003FormAtualizarCFOPRegras').unbind('click').bind('click', function(data) {
				$('#{$g['divId']}').showLoading();
				$.getJSON('/ven/ven003/form_func-ajax/atualizarCFOP/?ajax=true&T007_Id=' + encodeURIComponent("{$r_T007_Id}") + '&callback=?', function(request) {
					if (request.code) {
						var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
						dialogConfirm('Ok', request.data, \$buttons);
					} else {
						var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
						dialogConfirm('Erro', request.data, \$buttons);
					}
					$('#{$g['divId']}').hideLoading();
					$('#{$g['divId']}').closest('.ui-dialog-content').dialog('close');
					divRefresh('{$g['divIdParent']}', true);
				});
			});
			} </script></div>
EOT;

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
				$D006_Id = $form->campoValorEnviado('D006_Id');
				$retorno = $VEN003->ven003atualizarCFOPManual($r_T007_Id, $D006_Id);
				if ($retorno !== true) {
					echo gGeraAlertMsg('Erro', $retorno);	
				} else {
					echo gGeraAlertMsg('Operação OK', "Atualizado!");	
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
							divRefresh('{$g['divIdParent']}', true);
						} </script></div>
EOT;
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
	
		$botoesDir = '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'ven003FormCancelarNF':
		global $g;
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;

		$T007 = mysql_query("SELECT T007_Flag_SCAN FROM T007 WHERE T007_Id = '{$r_T007_Id}'");
		$mT007 = mysql_fetch_array($T007);

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
		$form_tabela = 'T007';
		$form_campo_pk = 'T007_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'ven003FormCancelarNF_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/ven/ven003/form/ven003FormCancelarNF/';
        
        //form editor
        $form_editor = array(
            'geral' => '',
        );

		$form = new Formulario('ven003FormAtualizarCFOP');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'AmbienteCancelamento' => array('name' => 'AmbienteCancelamento', 'label' => 'Tipo', 'type'=>'select', 'reject' => true, 'select' => 
						array(
							array('title' => 'Normal', 'value' => 'normal','selected'=> (isset($mT007['T007_Flag_SCAN']) && $mT007['T007_Flag_SCAN'] == 'S') ? false : true),
							array('title' => 'SVC - Contingência', 'value' => 'contingencia', 'selected' => (isset($mT007['T007_Flag_SCAN']) && $mT007['T007_Flag_SCAN'] == 'S') ? true : false),
						),
					),
					'T007_Observacao_Cancelamento' => array('name' => 'T007_Observacao_Cancelamento', 'label' => 'Observação do Cancelamento', 'type' => 'textarea', 'length' => array(8, 70), 
						'value' => "Nota fiscal cancelada por: {$g['C007']['C007_Nome']}\nData e hora : ".agora()."\nMotivo do Cancelamento : "),
				),
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
				$retorno = $VEN003->cancelarNotaFiscal($form, $r_T007_Id);
				if ($retorno !== true) {
					echo gGeraAlertMsg('Erro', $retorno);	
				} else {
					echo gGeraAlertMsg('Operação OK', "Nota Cancelada!");	
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
							divRefresh('{$g['divIdParent']}', true);
						} </script></div>
EOT;
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
	
		$botoesDir = '<button type="submit" id="ven003FormCancelarNF_btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'ven003FormDevolucaoNotaRecebida':
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
		$enviar_btnId = 'ven003FormDevolucaoNotaRecebida_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/ven/ven003/form/ven003FormDevolucaoNotaRecebida/';
		//form editor
		$form_editor = array(
			'geral' => '{"Nome_Empresa":{"top":30,"left":15.265625,"width":458,"hidden":0},"T007_Flag_Indicador_Presenca":{"top":53,"left":0.5,"width":480,"hidden":0},"D006_Id":{"top":76,"left":46.84375,"width":479,"hidden":0},"ipiNotaEspelho":{"top":99,"left":367.484375,"width":13,"hidden":0},"observacaoCFOP":{"top":119,"left":71,"width":null,"hidden":1},"ven003CadastrarCliente":{"top":143,"left":80,"width":null,"hidden":0},"ven003FormDevolucaoNotaRecebida_btn":{"top":143,"left":416,"width":null,"hidden":0}}'
		);

				
		// novo formulário
		$form = new Formulario('ven003FormDevolucaoNotaRecebida');

		$pesquisaGridExtra = <<<EOT
			left join D020 on D020_Id=D024_D020_Id 
			left join D018 on D018_Id=D024_D018_Id 
			WHERE D024_Flag_Ativo='S' {$restricaoVendedorCliente}
			order by D024_Nome_Empresa
EOT;

		$pesquisaGrid = array(
			'D024',																											// tabela
			'D024_Id',																										// chave primaria
			'D024_Id,D024_Nome_Empresa,D024_Nome_Fantasia,D020_Nome_Cidade,D018_UF,D024_Cnpj,D024_DDD_Telefone_1,D024_Telefone_1',	// campos
			$pesquisaGridExtra,																								// extras sql
			'Pesquisa de Cliente',																							// título da janela
			'Código,Cliente,Fantasia,Cidade,UF,CNPJ,DDD,Telefone',															// descrição dos campos
			'ven003ProcuraCliente',																							// callback js
			$g['divId'],																									// divId
			'',																												// parametro extra
			array(																											// parametros para cada campo do grid
				array('colunaAlinhar' => 'center'),
				array('style' => 'font-weight:bold'),
				array(),
				array(),
				array(),
				array(),
				array(),
				array()
			)
		);
		$r_listaT013s = json_decode(urldecode($r_listaT013s));
		$T013_Id = $r_listaT013s[0];
		$sqlT013 = mysql_query("SELECT D024_Id, D024_Nome_Empresa FROM T013 LEFT JOIN D024 ON D024_Id=T013_D024_Id WHERE T013_Id = '{$T013_Id}'");
		$rowT013 = mysql_fetch_array($sqlT013);
		$D024_Id = $rowT013['D024_Id'];
		$D024_Nome_Empresa = $rowT013['D024_Nome_Empresa'];
		
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'Nome_Empresa' => array('name' => 'Nome_Empresa', 'label' => 'Destinatário', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGrid, 'reject' => true, 'length' => array(75, 75), 'value' => $D024_Nome_Empresa),
					'D024_Id' => array('name' => 'D024_Id', 'type' => 'hidden', 'reject' => true, 'value' => $D024_Id),
					'D006_Id' => array('name' => 'D006_Id', 'label' => 'CFOP', 'type' => 'select', 
						'select' => gGeraSelect('D006', 'D006_Id', "D006_Codigo_CFOP, D006_Descricao, concat('('[[VIRGULA]]D006_Id[[VIRGULA]]')')", "WHERE IFNULL(D006_Flag_Inativo,'N')!='S' AND D006_Flag_Entrada_Saida='S' ORDER BY D006_Codigo_CFOP", true),
					'reject' => true),
					'T007_Flag_Indicador_Presenca' => array('name' => 'T007_Flag_Indicador_Presenca', 'label'=>'Ind. Presença','type' => 'select', 'select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => '0 - Não se aplica (complementar ou de ajuste)', 'value' => 0),
							array('title' => '1 - Operação presencial', 'value' => 1),
							array('title' => '2 - Operação não presencial, pela Internet', 'value' => 2),
							array('title' => '3 - Operação não presencial, Teleatendimento', 'value' => 3),
							array('title' => '4 - NFC-e em operação com entrega a domicílio', 'value' => 4),
							array('title' => '5 - Operação presencial, fora do estabelecimento', 'value' => 5),
							array('title' => '9 - Operação não presencial, outros', 'value' => 9),							
						)
					),
					'ipiNotaEspelho' => array('name' => 'ipiNotaEspelho', 'label'=>'Aplicar IPI no campo IPI Devolução','type' => 'checkbox', 'checked'=> false, 'reject' => true),
					'observacaoCFOP' => array('name' => 'observacaoCFOP', 'label'=>'Deixe a CFOP em branco para usar a CFOP de devolução dos produtos da nota','type' => 'titulo', 'reject' => true),
					//'Observacao' => array('name' => 'Observacao', 'label' => 'Observação', 'type' => 'textarea', 'length' => array(5, 80), 'reject' => true),
					'ven003FormDevolucaoNotaRecebida_btn' => array('name' => 'ven003FormDevolucaoNotaRecebida_btn', 'value' => 'Gerar nota devolução', 'reject' => true, 'type' => 'button', 'reload' => false, 'style' => 'margin-top: 10px; float: right;'),
					'ven003CadastrarCliente' => array('name' => 'ven003CadastrarCliente', 'value' => 'Cadastrar destinatário', 'reject' => true, 'type' => 'button', 'reload' => false, 'style' => 'margin-top: 10px; float: right;'),
				)
			)
		);
		$form->definirFormularioArray($formArray);
		
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#ven003CadastrarCliente').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Clientes/Fornecedores', '/cad/cad001/content/cad001contentCadastro/','', [960,583]);
				});
			} </script></div>
EOT;
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
				$D024_Id 					  = $form->campoValorEnviado('D024_Id');
				$D006_Id					  = $form->campoValorEnviado('D006_Id');
				$T007_Flag_Indicador_Presenca = $form->campoValorEnviado('T007_Flag_Indicador_Presenca');
				$ipiNotaEspelho				  = $form->campoValorEnviado('ipiNotaEspelho');
				//$Observacao = $form->campoValorEnviado('Observacao');
				$Observacao = '';
				$retorno = $VEN003->gerarDevolucaoNotaRecebida($T013_Id, $r_listaT013s, $D024_Id, $D006_Id, $Observacao, $T007_Flag_Indicador_Presenca, $ipiNotaEspelho);
				if($retorno === true){
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							fecharJanela('{$g['divId']}');
							divRefresh('{$g['divIdParent']}', true);
						} </script></div>
EOT;
				} else {
					echo $retorno;
					$row = mysql_fetch_assoc(mysql_query("SELECT D024_Nome_Fantasia FROM D024 WHERE D024_Id = '{$form->campoValorEnviado('D024_Id')}'"));
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							$('#{$g['divId']} #D024_Id').val('{$form->campoValorEnviado('D024_Id')}');
							$('#{$g['divId']} #Nome_Empresa_Pesquisa').val('{$row['D024_Nome_Fantasia']}');
						} </script></div>
EOT;
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
		//echo '<div style="padding:5px">Estorno de título - ID: '.$acaoId.'</div>';
		echo $gerado['pagina'];				
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');		
	break;

	case 'ven003FormCorrecaoNFCe':
		global $g;
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;
		
		/**
		 * PARTE 2: Definição do formulário
		 */
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = false;
		if ($formMultiTab) {
			$formId = (isset($r_formId)) ? $r_formId : false;
		} else {
			$formId = uniqid();
		}
		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = 'xxxx';
		$form_campo_pk = 'xxxx';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'ven003FormCorrecaoNFCeBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/ven/ven003/form/ven003FormCorrecaoNFCe/';
		// Editor
		$form_editor = array(
			'geral' => '{"numeroNota":{"top":7,"left":50.671875,"width":247,"hidden":0},"serieNFCe":{"top":7,"left":386.03125,"width":40,"hidden":0},"numeroProtocolo":{"top":30,"left":75.046875,"width":328,"hidden":0},"numeroChaveAcesso":{"top":53,"left":39.734375,"width":328,"hidden":0},"dataAutorizacao":{"top":76,"left":38.328125,"width":136,"hidden":0},"horaAutorizacao":{"top":76,"left":277.484375,"width":89,"hidden":0}}'
		);
		
		$form = new Formulario('ven003FormIncluirCCe');
		// Definição do Formulário
		$formArray = array(
			'campos' => array(		
				'geral' => array(
					'numeroNota' => array('name' => 'numeroNota', 'label' => 'Número NFC-e', 'type' => 'text', 'reject' => true),
					'numeroProtocolo' => array('name' => 'numeroProtocolo', 'label' => 'Protocolo', 'type' => 'text','reject' => true),
					'dataAutorizacao' => array('name' => 'dataHoraAutorizacao', 'label' => 'Data Autorizacao', 'type' => 'data','reject' => true),
					'horaAutorizacao' => array('name' => 'horaAutorizacao', 'label' => 'Hora Autorizacao', 'type' => 'tempo','reject' => true),
					'numeroChaveAcesso' => array('name' => 'numeroChaveAcesso', 'label' => 'Chave de acesso', 'type' => 'texte','reject' => true),
					'serieNFCe' => array('name' => 'serieNFCe', 'label' => 'Série', 'type' => 'texte','reject' => true),
				),
			),
		);	
		

		$form->definirFormularioArray($formArray);
		/**
		 * PARTE 3: Processamento do formulário (se foi enviado)
		 * E carregamento dos valores (se não foi enviado parametro acaoId)
		 */
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
				// Form sem erros
			} else if ($formCodigo == 2) {
				$numeroNota        = $form->campoValorEnviado('numeroNota');
				$numeroProtocolo   = $form->campoValorEnviado('numeroProtocolo');
				$dataAutorizacao   = $form->campoValorEnviado('dataAutorizacao');
				$horaAutorizacao   = $form->campoValorEnviado('horaAutorizacao');
				$numeroChaveAcesso = $form->campoValorEnviado('numeroChaveAcesso');
				$serieNFCe         = $form->campoValorEnviado('serieNFCe');

				$retorno = $VEN003->corrigeNFCe($numeroNota,$numeroProtocolo,$dataAutorizacao,$horaAutorizacao,$numeroChaveAcesso,$serieNFCe);

				if($retorno !== true){
					echo gGeraAlertMsg('Erro', $retorno);	
				} else {
					echo gGeraAlertMsg('Ok', 'Operação realizada com sucesso.');	
					echo <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						fecharJanela('{$g['divId']}');
					} </script></div>
EOT;
				}
			}
		} else {
			// Carrega os valores caso seja um update
			if (!empty($r_acaoId)) {
				$processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $r_acaoId);
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
		$gerado = gGeraCamposForm($form, $tab, $r_acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

		/**
		 * PARTE 5: Exibir o formulário
		 */
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');	

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="ven003FormCorrecaoNFCeBtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'ven003FormIncluirCCe':
		global $g;
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;
		
		/**
		 * PARTE 2: Definição do formulário
		 */
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = false;
		if ($formMultiTab) {
			$formId = (isset($r_formId)) ? $r_formId : false;
		} else {
			$formId = uniqid();
		}
		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = 'T199';
		$form_campo_pk = 'T199_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'ven003FormIncluirCCeBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/ven/ven003/form/ven003FormIncluirCCe/';
		// Editor
		$form_editor = array(
			'geral' => '{"T199_Correcao":{"top":7,"left":12,"height":223,"width":360,"hidden":0}}'
		);
		
		$form = new Formulario('ven003FormIncluirCCe');
		// Definição do Formulário
		$formArray = array(
			'campos' => array(		
				'geral' => array(
					'T199_Correcao' => array('name' => 'T199_Correcao', 'label' => 'Texto de <br>Correção', 'type' => 'textarea', 'length' => array(7,75)),
					'T199_T007_Id' => array('name' => 'T199_T007_Id', 'type' => 'hidden', 'value' => @$r_T007_Id),
					'T199_C007_Id' => array('name' => 'T199_C007_Id', 'type' => 'hidden', 'value' => @$g['usuarioAtual']),
					'T199_Sequencial_Evento' => array('name' => 'T199_Sequencial_Evento', 'type' => 'hidden', 'value' => '0'),
				),
			),
		);	
		

		$form->definirFormularioArray($formArray);
		/**
		 * PARTE 3: Processamento do formulário (se foi enviado)
		 * E carregamento dos valores (se não foi enviado parametro acaoId)
		 */
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
				// Form sem erros
			} else if ($formCodigo == 2) {
				// Insert ou Update?
				$T199_Correcao = $form->campoValorEnviado('T199_Correcao');
				$T199_Correcao=adicionarEspape($T199_Correcao);
				$form->campoSetarValor('T199_Correcao', $T199_Correcao, true);
				
				if(strlen($T199_Correcao) < 15) {
				    echo gGeraAlertMsg('Erro', 'Texto de correção deve ter mais de 15 caracteres!');
				} else if(strlen($T199_Correcao) > 1000) {
					echo gGeraAlertMsg('Erro', 'Texto de correção ultrapassou 1000 caracteres!');
				} else {
					if (!empty($r_acaoId)) {
						// Update
						if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $r_acaoId, $form)) {					    	
							// Imprime uma mensagem e fecha a janela
							$alert = gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!', false);
							echo <<<EOT
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
										setTimeout(function() {
											{$alert}
										}, 150);
	  								});
									// O FW atualiza a linha do grid automaticamente, somente recarregar se não for grid
                                    if ($('#{$g['divIdParent']}').find('table.grid').length === 0) {
                                        divRefresh('{$g['divIdParent']}', true);
                                    }
								} </script></div>
EOT;
						}
					} else {
						$T007_Id = $form->campoValorEnviado('T007_Id');
						$sqlT199 = mysql_query("SELECT T199_Sequencial_Evento+1 AS Seq FROM T199 WHERE T199_T007_Id = '{$r_T007_Id}' ORDER BY T199_Sequencial_Evento desc");
						$rowT199 = mysql_fetch_array($sqlT199);
					    
					    if ($rowT199['Seq'] > 20){
					    	echo gGeraAlertMsg('Erro', 'Nota fiscal não pode ter mais de 20 cartas de correções!');
					    } else {
							// Insert
							if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
								// Imprime uma mensagem e fecha a janela
								if ($rowT199['Seq']<=0) $rowT199['Seq']=1;
								mysql_query("UPDATE T199 SET T199_Sequencial_Evento = '{$rowT199['Seq']}', T199_Data_Hora_Inclusao = now() WHERE T199_Id = '{$g['mysqlLastId']}'");
								$alert = gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
								echo <<<EOT
									<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
										$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
											setTimeout(function() {
												{$alert}
											}, 150);
			  							});
										$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
										divRefresh('{$g['divIdParent']}', true);
									} </script></div>
EOT;
							}
						}
					}
				}
			}
		} else {
			// Carrega os valores caso seja um update
			if (!empty($r_acaoId)) {
				$processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $r_acaoId);
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
		$gerado = gGeraCamposForm($form, $tab, $r_acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

		/**
		 * PARTE 5: Exibir o formulário
		 */
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');	
	break;

	case 'ven003FormIncluirEventoNFe':
		$tab = isset($r_tab) ? $r_tab : 'geral';
		$T007_IdFormulario = (int)$r_T007_Id;
		$formId = uniqid();
		$form_tabela = 'T321';
		$form_campo_pk = 'T321_Id';
		$enviar_btnId = 'ven003FormIncluirEventoNFeBtn';
		$enviar_acaoHref = '/ven/ven003/form/ven003FormIncluirEventoNFe/?T007_Id=' . urlencode($T007_IdFormulario);
		// Posição inicial ajustável pelo editor visual do framework.
		$form_editor = array(
			'geral' => '{"T321_Tipo_Evento":{"top":7.996337890625,"left":25.56683349609375,"width":543,"hidden":0}}'
		);

		$form = new Formulario('ven003FormIncluirEventoNFe');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T321_Tipo_Evento' => array('name' => 'T321_Tipo_Evento', 'label' => 'Evento', 'type' => 'select', 'select' => array(
						array('title' => '...', 'value' => '', 'selected' => true),
						array('title' => '112140 - Fornecimento não realizado com pagamento antecipado', 'value' => '112140')
					)),
					'T321_T007_Id' => array('name' => 'T321_T007_Id', 'type' => 'hidden', 'value' => $T007_IdFormulario),
					'T321_C007_Id' => array('name' => 'T321_C007_Id', 'type' => 'hidden', 'value' => (int)$g['usuarioAtual']),
					'T321_Status' => array('name' => 'T321_Status', 'type' => 'hidden', 'value' => 'RASCUNHO'),
					'T321_Ambiente' => array('name' => 'T321_Ambiente', 'type' => 'hidden'),
					'T321_Chave_NFe' => array('name' => 'T321_Chave_NFe', 'type' => 'hidden'),
					'T321_Sequencial_Evento' => array('name' => 'T321_Sequencial_Evento', 'type' => 'hidden'),
					'T321_Data_Hora_Inclusao' => array('name' => 'T321_Data_Hora_Inclusao', 'type' => 'hidden'),
				),
			),
		);
		$form->definirFormularioArray($formArray);

		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
			} else if ($formCodigo == 2) {
				$T007_Id = $T007_IdFormulario;
				$tipoEvento = $form->campoValorEnviado('T321_Tipo_Evento');
				$T007 = mysql_query("SELECT T007_Ambiente_NFe, T007_Chave_Acesso_Nfe, T007_Numero_Protocolo_Nfe FROM T007 WHERE T007_Id = '{$T007_Id}'");
				$T007 = mysql_fetch_array($T007);

				if (empty($tipoEvento)) {
					echo gGeraAlertMsg('Erro', 'Selecione o evento que deseja incluir.');
				} else if (empty($T007['T007_Numero_Protocolo_Nfe']) || empty($T007['T007_Chave_Acesso_Nfe'])) {
					echo gGeraAlertMsg('Erro', 'A NF-e precisa estar autorizada para incluir um evento.');
				} else {
					$tipoEventoSql = adicionarEspape($tipoEvento);
					$sqlSequencial = mysql_query("SELECT IFNULL(MAX(T321_Sequencial_Evento), 0) + 1 AS Sequencial FROM T321 WHERE T321_Chave_NFe = '{$T007['T007_Chave_Acesso_Nfe']}' AND T321_Tipo_Evento = '{$tipoEventoSql}'");
					$sequencial = mysql_fetch_array($sqlSequencial);

					$form->campoSetarValor('T321_T007_Id', $T007_Id, true);
					$form->campoSetarValor('T321_C007_Id', (int)$g['usuarioAtual'], true);
					$form->campoSetarValor('T321_Status', 'RASCUNHO', true);
					$form->campoSetarValor('T321_Ambiente', $T007['T007_Ambiente_NFe'], true);
					$form->campoSetarValor('T321_Chave_NFe', $T007['T007_Chave_Acesso_Nfe'], true);
					$form->campoSetarValor('T321_Sequencial_Evento', $sequencial['Sequencial'], true);
					$form->campoSetarValor('T321_Data_Hora_Inclusao', date('Y-m-d H:i:s'), true);

					if ($g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						$T321_IdIncluido = (int)$g['mysqlLastId'];
						/*
						 * Cada evento pode definir seu próximo passo após a inclusão.
						 * Eventos sem fluxo complementar apenas fecham esta janela.
						 */
						$fluxosAposInclusao = array(
							'112140' => array(
								'titulo' => 'Evento NF-e',
								'url' => '/ven/ven003/content/ven003contentEvento112140/',
								'tamanho' => '[950,600]'
							)
						);
						$abrirProximaJanela = '';
						if (isset($fluxosAposInclusao[$tipoEvento])) {
							$fluxo = $fluxosAposInclusao[$tipoEvento];
							$abrirProximaJanela = "abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}', unique(), '', '{$fluxo['titulo']}', '{$fluxo['url']}', '&T321_Id={$T321_IdIncluido}', {$fluxo['tamanho']});";
						}

						$alert = gGeraAlertMsg('Operação OK', 'Evento incluído como rascunho.', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind('dialogclose', function(data, ui) {
									setTimeout(function() { {$abrirProximaJanela} {$alert} }, 150);
								});
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								divRefresh('{$g['divIdParent']}', true);
							} </script></div>
EOT;
					}
				}
			}
		}

		$gerado = gGeraCamposForm($form, $tab, $r_acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']);
		$g['smarty']->assign('form_tabela', $form_tabela);
		echo $gerado['pagina'];
		$g['smarty']->assign('formId', $formId);
		$g['smarty']->display('formEditor.tpl');
	break;

	case 'ven003FormIncluirItemEvento112140':
		$T321_Id = (int)$r_T321_Id;
		$evento = mysql_fetch_array(mysql_query("SELECT T321_T007_Id, T321_Status, T321_Tipo_Evento FROM T321 WHERE T321_Id = '{$T321_Id}'"));
		$itensNfe = new classeNFeEventos();
		$pesquisaGridExtra = "LEFT JOIN T008A ON T008A_T008_Id = T008_Id WHERE T008_T007_Id = '{$evento['T321_T007_Id']}' AND NOT EXISTS (SELECT 1 FROM T322 WHERE T322_T321_Id = '{$T321_Id}' AND T322_T008_Id_Origem = T008.T008_Id) ORDER BY T008_Codigo_Produto, T008_Id";
		$pesquisaGrid = array(
			'T008', 'T008_Id', 'T008_Id||T008_Codigo_Produto||T008_Descricao_Produto||T008_Quantidade||T008_Unidade||T008A_Valor_IBS||T008A_Valor_CBS',
			$pesquisaGridExtra, 'Pesquisa de produto da NF-e', 'Id,Código,Descrição,Quantidade,Unidade,IBS,CBS',
			'campoProcuraProdutoEvento112140', $g['divId'], array($T321_Id),
			array(array('chavePrimaria' => true, 'colunaExibir' => false), array('style' => 'width:1%'), array(), array('colunaAlinhar' => 'right'), array('style' => 'width:1%'), array('colunaAlinhar' => 'right'), array('colunaAlinhar' => 'right')),
			'T008_Id||T008_Quantidade||T008A_Valor_IBS||T008A_Valor_CBS', null, null, null, null, null,
			'T008_Codigo_Produto,Código,texto||T008_Descricao_Produto,Descrição,texto'
		);
		$tab = 'geral'; $formId = uniqid(); $form_tabela = 'T322'; $form_campo_pk = 'T322_Id';
		$enviar_btnId = 'ven003FormIncluirItemEvento112140Btn';
		$enviar_acaoHref = '/ven/ven003/form/ven003FormIncluirItemEvento112140/?T321_Id=' . urlencode($T321_Id);
		// Posições iniciais ajustáveis pelo editor visual do framework.
		$form_editor = array(
			'geral' => '{"Produto_Origem":{"top":6.954681396484375,"left":67.76959228515625,"width":346.039216,"hidden":0},"Quantidade_Original":{"top":29.993896484375,"left":44.45465087890625,"width":136.039216,"hidden":0},"Quantidade_Saldo":{"top":29.993896484375,"left":277.89520263671875,"width":136.039216,"hidden":0},"T322_Quantidade_Nao_Fornecida":{"top":52.971832275390625,"left":13.32720947265625,"width":135.039216,"hidden":0},"Valor_IBS_Saldo":{"top":75.9957275390625,"left":284.727294921875,"width":135.039216,"hidden":0},"Valor_CBS_Saldo":{"top":98.98898315429688,"left":280.5606689453125,"width":135.039216,"hidden":0},"T322_Valor_IBS":{"top":75.9957275390625,"left":62.11700439453125,"width":135.039216,"hidden":0},"T322_Valor_CBS":{"top":98.98898315429688,"left":57.95037841796875,"width":135.039216,"hidden":0}}'
		);
		$form = new Formulario('ven003FormIncluirItemEvento112140');
		$form->definirFormularioArray(array('campos' => array('geral' => array(
			'Produto_Origem' => array('name' => 'Produto_Origem', 'label' => 'Produto', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGrid, 'length' => array(70, 70), 'reject' => true),
			'T322_NItem' => array('name' => 'T322_NItem', 'type' => 'hidden'),
			'T322_T008_Id_Origem' => array('name' => 'T322_T008_Id_Origem', 'type' => 'hidden'),
			'Quantidade_Original' => array('name' => 'Quantidade_Original', 'label' => 'Qtde original', 'type' => 'moeda', 'casasDecimais' => 4, 'readonly' => true, 'reject' => true),
			'Quantidade_Saldo' => array('name' => 'Quantidade_Saldo', 'label' => 'Qtde saldo', 'type' => 'moeda', 'casasDecimais' => 4, 'readonly' => true, 'reject' => true),
			'T322_Quantidade_Nao_Fornecida' => array('name' => 'T322_Quantidade_Nao_Fornecida', 'label' => 'Qtde não fornecida', 'type' => 'moeda', 'casasDecimais' => 4),
			'T322_Unidade_Nao_Fornecida' => array('name' => 'T322_Unidade_Nao_Fornecida', 'type' => 'hidden'),
			'Valor_IBS_Saldo' => array('name' => 'Valor_IBS_Saldo', 'label' => 'IBS saldo', 'type' => 'moeda', 'casasDecimais' => 2, 'readonly' => true, 'reject' => true),
			'T322_Valor_IBS' => array('name' => 'T322_Valor_IBS', 'label' => 'Valor IBS', 'type' => 'moeda', 'casasDecimais' => 2),
			'Valor_CBS_Saldo' => array('name' => 'Valor_CBS_Saldo', 'label' => 'CBS saldo', 'type' => 'moeda', 'casasDecimais' => 2, 'readonly' => true, 'reject' => true),
			'T322_Valor_CBS' => array('name' => 'T322_Valor_CBS', 'label' => 'Valor CBS', 'type' => 'moeda', 'casasDecimais' => 2),
			'T322_T321_Id' => array('name' => 'T322_T321_Id', 'type' => 'hidden', 'value' => $T321_Id),
		))));
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			if ($formCodigo == 1) { echo gGeraAlertMsgErroForm($form); }
			else if ($formCodigo == 2 && (!$evento || $evento['T321_Tipo_Evento'] != '112140' || $evento['T321_Status'] != 'RASCUNHO')) { echo gGeraAlertMsg('Erro', 'O evento não está disponível para alteração.'); }
			else if ($formCodigo == 2) {
				$form->campoSetarValor('T322_T321_Id', $T321_Id, true);
				$T008_Id = (int)$form->campoValorEnviado('T322_T008_Id_Origem');
				$itemOrigem = mysql_fetch_array(mysql_query("SELECT T008_Unidade FROM T008 WHERE T008_Id = '{$T008_Id}' AND T008_T007_Id = '{$evento['T321_T007_Id']}'"));
				$nItem = $itensNfe->buscarNItemPorItemOrigem112140($evento['T321_T007_Id'], $T008_Id);
				if (empty($nItem) || empty($itemOrigem['T008_Unidade'])) {
					echo gGeraAlertMsg('Erro', 'Não foi possível localizar o item selecionado no XML autorizado ou sua unidade na T008.');
				} else {
					$form->campoSetarValor('T322_NItem', $nItem, true);
					$form->campoSetarValor('T322_T008_Id_Origem', $T008_Id, true);
					$form->campoSetarValor('T322_Unidade_Nao_Fornecida', $itemOrigem['T008_Unidade'], true);
				if ($g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
				echo '<div class="divHidden"><script>$("#'.$g['divId'].'").closest(".ui-dialog-content").dialog("close"); divRefresh("'.$g['divIdParent'].'", true);</script></div>';
				}
				}
			}
		}
		$gerado = gGeraCamposForm($form, $tab, false, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);
		$g['smarty']->assign('formularioID', $form->formularioID()); $g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', $form_tabela); echo $gerado['pagina']; $g['smarty']->assign('formId', $formId); $g['smarty']->display('formEditor.tpl');
		$g['smarty']->assign('botoesDir', '<button type="submit" id="ven003FormIncluirItemEvento112140Btn">Salvar produto</button><button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>');
		$g['smarty']->assign('botoesEsq', ''); $g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'ven003FormProdutosConsumidor':
		$g['smarty']->assign('navSql', "T007_C004_Id = '{$g['empresaAtual']}'");
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']}').data('sqlPesquisa', '[T007_Id, T007_Data_Emissao as Data, D024_Nome_Empresa, D024_Nome_Fantasia]');
				$('#{$g['divId']}').data('labelPesquisa', '[Pedido, Emissão, Razão Social, Nome Fantasia]');
				$('#{$g['divId']}').data('sqlExtra', '[LEFT JOIN D024 ON T007_D024_Id = D024_Id]');
			} </script></div>
EOT;
		gHabilitaNavegacao($g['divId'], 'VEN003', 'ven003FormProdutosConsumidor');		

		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$T007=mysql_query("SELECT T007_D024_Id, T007_Casas_Decimais_Preco,T007_Casas_Decimais_Quantidade, T007_C004_Id_Estoque, T007_Flag_PDV FROM T007 WHERE T007_Id='{$r_T007_Id}'");
		$mT007=mysql_fetch_array($T007);

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
		$form_tabela = 'T008';
		$form_campo_pk = 'T008_Id';
		$form_parent = 'T007_Id';
		$form_parent_valor = $r_T007_Id;
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = array('ven003FormProdutosConsumidorBtnAdd', 'ven003FormProdutosConsumidorBtnCln', 'ven003FormProdutosConsumidorBtnNew');
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/ven/ven003/form/ven003FormProdutosConsumidor/';
        
        //form editor
        $form_editor = array(
            'produto' => '{"Procura_Produto":{"top":7,"left":36,"width":122,"hidden":0},"Marca":{"top":7,"left":267,"width":136,"hidden":0},"precoTabela":{"top":7,"left":466,"width":88,"hidden":0},"T008_Descricao_Produto":{"top":30,"left":17,"width":360,"hidden":0},"PrecoMinimo":{"top":30,"left":464,"width":88,"hidden":0},"T008_Quantidade":{"top":53,"left":11,"width":72,"hidden":0},"T008_D037_Id":{"top":53,"left":160,"width":63,"hidden":0},"T008_Valor_Preco_Sem_Desconto_Unitario":{"top":53,"left":237,"width":72,"hidden":0},"T008_Percentual_Desconto":{"top":53,"left":392,"width":24,"hidden":0},"estoqueLiquido":{"top":53,"left":483,"width":88,"hidden":0},"T008_Observacao":{"top":53,"left":733,"width":360,"hidden":1},"T008_Codigo_Substituto":{"top":76,"left":6,"width":136,"hidden":0},"T008_Numero_Pedido_Compra":{"top":76,"left":257,"width":88,"hidden":0},"T008_Item_Pedido_Compra":{"top":76,"left":400,"width":40,"hidden":0},"estoqueLoja":{"top":76,"left":480,"width":88,"hidden":0},"T008_Informacoes_Adicionais":{"top":99,"left":13,"width":360,"hidden":0},"estoqueTotal":{"top":99,"left":476,"width":89,"hidden":0},"T008_D006_Id":{"top":122,"left":36,"width":367,"hidden":0},"ordemCompra":{"top":122,"left":505,"width":88,"hidden":0},"T008_Aliquota_IPI":{"top":122,"left":752,"width":40,"hidden":1},"T008_Flag_Destino_Produto":{"top":145,"left":37,"width":144,"hidden":0},"T008_Observacao_Entrega":{"top":145,"left":233,"width":136,"hidden":0},"estoqueFora":{"top":145,"left":477,"width":88,"hidden":0},"UltimoPrecoVenda":{"top":168,"left":31,"height":39,"width":359,"hidden":0},"ven001BtnCadastrarProduto":{"top":166,"left":479.96875,"width":null,"hidden":0},"ven003FormProdutosConsumidorBtnCln":{"top":189,"left":479.96875,"width":null,"hidden":0},"ven003FormProdutosConsumidorBtnAdd":{"top":189,"left":560,"width":null,"hidden":0}}',
        );
		// Campo: Procura_Produto
		$T007_Id = isset($r_ven003FormProdutosConsumidorForm) ? $r_ven003FormProdutosConsumidorForm['T008_T007_Id'] : $r_T007_Id;

		$C004_Id_Estoque = $g['empresaAtual'];
		if($g['C031']['venderEstoqueMatriz'] == 'S'){
			$C004_Id_Estoque = (isset($mT007['T007_C004_Id_Estoque']) && $mT007['T007_C004_Id_Estoque'] > 0) ? $mT007['T007_C004_Id_Estoque'] : $g['empresaAtual'];
		}
		
		$filtro = '';
        if($g['C031']['SeparaProdutoPorEmpresa']=='S'){
              $filtro = "AND IF(D001_C004_Id > 0, D001_C004_Id=".$g['empresaAtual'].", (1=1))";
        }else{
              $filtro = '';
        }

        log("filtro: $filtro - Global = ".$g['C031']['SeparaProdutoPorEmpresa']);

		$pesquisaGridExtra = <<<EOT
			left join D049 on D049_D001_Id=D001_Id  
			left join D041 on D041_D049_Id=D049_Id  
			left join D009 on D009_D049_Id=D049_Id  
			left join D082 on D082_Id=D049_D082_Id  
			left join D037 on D037_Id=D001_D037_Id   
			left join D060 on D060_Id=D001_D060_Id
			left join D003 on D003_Id=D001_D003_Id
			left join D002 on D002_Id=D001_D002_Id
			left join C008 on C008_Id=D001_C008_Id
			left join D005 on D005_Id=D001_D005_Id
			left join T066 on T066_D009_Id=D009_Id
			left join D004 on D004_Id=T066_D004_Id
			LEFT JOIN D083 on D083_D001_Id=D001_Id AND D083_D024_Id='{$mT007['T007_D024_Id']}'
			LEFT JOIN D144 ON D144_D049_Id=D049_Id  
			left join C007 on C007_Id=D009_C007_Id_Comprador
			where (D009_C004_Id = '{$C004_Id_Estoque}'  and D001_Flag_Ativo='S' and ifnull(D049_Modelo,'')!='0' and ifnull(D049_Flag_Ativo,'')!='N')  
			AND NOT EXISTS (
				SELECT 1
				FROM D009A D009A_Visibilidade
				INNER JOIN D009 D009_Visibilidade ON D009_Visibilidade.D009_Id=D009A_Visibilidade.D009A_D009_Id
				INNER JOIN D049 D049_Visibilidade ON D049_Visibilidade.D049_Id=D009_Visibilidade.D009_D049_Id
				WHERE D049_Visibilidade.D049_D001_Id=D001_Id
				  AND D009_Visibilidade.D009_C004_Id='{$g['empresaAtual']}'
				  AND D009A_Visibilidade.D009A_Flag_Pertence_Empresa='N'
			)
			{$filtro}
			group by D009_Id  
			order by D001_Codigo_Produto ASC
EOT;
		$pesquisaGrid = array(
			'D001',																											              // tabela
			'D009_Id,D144_Id',																										      // chave primaria
			'D009_Id||C008_Id||D060_Id||D001_Codigo_Produto||D001_Codigo_Barras||D049_Modelo||D083_Codigo_Produto_Fornecedor||D001_Descricao_Produto||D001_Aplicacao||substr(D082_Marca,1,15) as D082_Marca||D037_Unidade||D005_Classificacao_Fiscal||D009_Preco_1||D009_Preco_2||D009_Preco_3||D009_Quantidade_Estoque||D009_Quantidade_Estoque_Liquido as Pedido||(select sum(T066_Quantidade_Estoque) from T066 left join D004 on D004_Id=T066_D004_Id  left join D146 ON D146_Id = D004_D146_Id where T066_D009_Id=D009_Id and UPPER(D146_Descricao)="LOJA") as Estoque||T066_Quantidade_Estoque||D009_Quantidade_Estoque_Fora||D009_Quantidade_Estoque_Similar||"" as retornaPrecoVenda||infoCustoVendaProduto||D009_Quantidade_OC||D004_Local||substr(C007_Primeiro_Nome,1,10) as C007_Primeiro_Nome||D001_Especificacoes||D001_Descricao_Ingles||D049_Id||D001_Flag_Pre_Cadastro||D144_Id||D060_Descricao_Grupo||"' . $T007_Id . '" as T007_Id||D001_Id||D009_Data_Alteracao_Custo||D009_Data_Ultima_Venda||D009_Flag_Custo_Utilizado',	// campos
			$pesquisaGridExtra,																								              // extras sql
			'Pesquisa de Produto',																										  // título da janela
			'Id,Dep,Setor,Código,EAN,Sub-item,Cód.Cli,Descrição,Referência,Marca,UN,NCM,Preço,Preço 2,Preço 3,Físico,Líquido,Loja,Locação,Fora,Similar,Preço,,OC,Locação,Comprador,Observações,Descrição Inglês,D049_Id,D001_Flag_Pre_Cadastro,D144_Id,Setor,T007_Id,D001_Id',	  // descrição dos campos
			'campoProcuraProdutoConsumidor',																					              // callback js
			$g['divId'],																									              // divId
			array($T007_Id, $mT007['T007_D024_Id'], $mT007['T007_Flag_PDV']),															  // parametro extra
			array(																											              // parametros para cada campo do grid
				array('chavePrimaria' => true, 'colunaExibir' => false),
				array('colunaAlinhar' => 'center', 'style' => 'width:1%', 'colunaExibir' => false),
				array('colunaAlinhar' => 'center', 'style' => 'width:1%', 'colunaExibir' => false),
				array('colunaAlinhar' => 'center', 'style' => 'width:1%;font-weight:bold'),
				array('colunaAlinhar' => 'center', 'style' => 'width:1%;font-weight:bold'),
				array('colunaAlinhar' => 'left', 'style' => 'width:1%;font-weight:bold'),
				array('colunaAlinhar' => 'center', 'style' => 'width:1%'),
				array('style' => 'font-weight:bold;white-space:normal;'),
				array('colunaAlinhar' => 'left', 'style' => 'width:1%'),
				array('style' => 'width:1%'),
				array('style' => 'width:1%'),
				array('style' => 'width:1%'),
				array('callback' => 'gCorrigeNumero', 'colunaAlinhar' => 'right', 'style' => 'font-weight:bold', 'colunaExibir' => false),
				array('callback' => 'gCorrigeNumero', 'colunaAlinhar' => 'right', 'style' => 'font-weight:bold', 'colunaExibir' => false),
				array('callback' => 'gCorrigeNumero', 'colunaAlinhar' => 'right', 'style' => 'font-weight:bold', 'colunaExibir' => false),
				array('callback' => 'validaCampoEstoque', 'colunaAlinhar' => 'right', 'style' => 'font-weight:bold', 'colunaExibir' => false),
				array('callback' => 'qtePedidos', 'colunaAlinhar' => 'right', 'style' => 'font-weight:normal;width:1%'),
				array('style' => 'width: 1%;', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => 2),
				array('colunaAlinhar' => 'center', 'colunaCasasDecimais' => 2, 'colunaExibir' => false),
				array('callback' => 'estoqueFora', 'style' => 'width: 1%;', 'colunaAlinhar' => 'right', 'callbackParameter' => array($r_T007_Id)),
				array('callback' => 'abrirJanela', 'callbackParameter' => array('Estoque Similares', '/cad/cad002/content/cad002ContentEstoqueSimilares/', 'D001_Id', '850,400', true), 'style' => 'width: 1%;', 'colunaAlinhar' => 'right'),
				array('colunaTipo' => 'livre', 'callback' => 'retornaPrecoVendaNota', 'colunaAlinhar' => 'right', 'style' => 'font-weight:bold;width:1%'),
				array('callback' => 'infoCustoVendaProduto', 'colunaTipo' => 'livre', 'style' => 'width:1%'),
				array('colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('Ordens de Compra', '/ven/ven001/content/ven001contentProdutoOC/', 'D009_Id', '700,400', true), 'style' => 'font-weight:normal;width:1%'),
				array('style' => 'width:1%'),
				array('style' => 'width:1%'),
				array('colunaExibir' => false),
				array('colunaExibir' => false),
				array('colunaExibir' => false),
				array('colunaExibir' => false),
				array('chavePrimaria' => true, 'colunaExibir' => false),
				array('colunaExibir' => false),
				array('colunaTitulo' => 'T007_Id', 'colunaExibir' => false),
				array('colunaExibir' => false),
				array('colunaExibir' => false),
				array('colunaExibir' => false),
				array('colunaExibir' => false)
			),
			'D009_Id||D049_Id||D009_Preco_1||D009_Preco_2||D009_Preco_3||D009_Quantidade_Estoque||D009_Quantidade_Estoque_Liquido||infoCustoVendaProduto||D144_Id||C008_Id||D060_Id||D037_Unidade||D009_Quantidade_Estoque_Real||T066_Quantidade_Estoque||D009_Quantidade_Estoque_Fora||D009_Quantidade_OC||D144_Id||D001_Id||D009_Flag_Custo_Utilizado', // Campos que são ignorados na cláusula where do pesquisaGrid
			null,
			null,
			null,
			null,
			null,
			'D001_Descricao_Produto,Descricao,texto||D001_Codigo_Produto,Codigo,texto||D009_Quantidade_Estoque_Liquido,Estoque,numero', // Paramentro para montar os filtros
		);
		
		$readOnlyDescricao = ($g['C031']['editarDescricaoProdutoVenda'] == 'S') ? false : true;
		$readOnlyUnidade   = ($g['C031']['editarUnidadeProdutoVenda'] == 'S') ? false : true;
		$extraUnidade      = ($g['C031']['editarUnidadeProdutoVenda'] == 'S') ? "" : "onfocus='this.initialSelect = this.selectedIndex;' onchange='this.selectedIndex = this.initialSelect;'";

		$sqlVeiculo = mysql_query("select T007_D024_Id,T007_D116_Id from T007 where T007_Id='{$r_T007_Id}'");
		$resultVeiculo = mysql_fetch_array($sqlVeiculo);
		
		//Buscas destinos, foi centralizado nesta classe e vai valer para todos os locais
		$destinoProdutos = $CAD002->retornaDestinoProdutos(false, 'V');

		// novo formulário
		$form = new Formulario('ven003FormProdutosConsumidorForm');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'produto' => array(
					'Procura_Produto' => array('name' => 'Procura_Produto' , 'label' => 'Produto', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGrid, 'length' => array(64,120), 'reject' => true, 'extra' => 'placeholder="BUSCAR"'),
					'Marca' => array('name' => 'Marca', 'label' => 'Marca', 'type' => 'text', 'length' => array(20,255), 'reject' => true, 'readonly'=>true),
					'T008_Codigo_Substituto' => array('name' => 'T008_Codigo_Substituto', 'label' => 'Código cliente', 'type' => 'text', 'length' => array(20, 120)),
					'T008_Descricao_Produto' => array('name' => 'T008_Descricao_Produto', 'label' => 'Descrição', 'type' => 'text', 'readonly' => $readOnlyDescricao, 'length' => array(64, 120)),
					'T008_Quantidade' => array('name' => 'T008_Quantidade', 'label' => 'Quantidade', 'type' => 'moeda', 'length' => array(20, 30), 'casasDecimais' => $mT007['T007_Casas_Decimais_Quantidade']),
					'T008_D037_Id' => array('name' => 'T008_D037_Id', 'label' => '', 'readonly' => $readOnlyUnidade, 'type' => 'select', 'extra' => $extraUnidade, 'select' => 
					    gGeraSelect('D037', 'D037_Id', 'D037_Unidade', '', true)
					),
					'T008_Valor_Preco_Sem_Desconto_Unitario' => array('name' => 'T008_Valor_Preco_Sem_Desconto_Unitario', 'label' => 'Preço Unit.', 'type' => 'moeda', 'length' => array(20, 30), 'casasDecimais' => $mT007['T007_Casas_Decimais_Preco']),
					'PrecoMinimo' => array('name' => 'PrecoMinimo', 'type' => 'moeda', 'length' => array(20, 30), 'reject' => true, 'readonly' => true, 'style' => 'text-align: right;', 'label' => 'Prc. Mínimo'),
					'T008_Numero_Pedido_Compra' => array('name' => 'T008_Numero_Pedido_Compra', 'label' => 'OC/Item', 'type' => 'text', 'length' => array(20, 60)),
					'T008_Item_Pedido_Compra' => array('name' => 'T008_Item_Pedido_Compra', 'type' => 'text', 'length' => array(20, 30)),
					'T008_Percentual_Desconto' => array('name' => 'T008_Percentual_Desconto', 'label' => '%D', 'type' => 'moeda', 'length' => array(15, 30), 'reject'=>true),
					'T008_D005_Id' => array('name' => 'T008_D005_Id', 'label' => 'NCM', 'type' => 'hidden', 'readonly' => true, 'restricao' => false, 'extra'=>'style="width:95px;"', 'extra' => "onfocus='this.initialSelect = this.selectedIndex;' onchange='this.selectedIndex = this.initialSelect;'", 'select' => 
						gGeraSelect('D149', 'D005_Id', 'D005_Classificacao_Fiscal, D005_Flag_Aplicacao, D149_Aliquota_IPI', "LEFT JOIN D005 ON D149_D005_Id=D005_Id AND D149_C004_Id='{$g['empresaAtual']}' WHERE D005_Flag_Ativo='S' ORDER BY D005_Classificacao_Fiscal, D005_Observacao", true)
					),
					'T008_Aliquota_IPI' => array('name' => 'T008_Aliquota_IPI', 'label' => 'IPI', 'type' => 'moeda', 'length' => array(5,20)),
					//'T008_Valor_Custo_Unitario' => array('name' => 'T008_Valor_Custo_Unitario', 'label' => 'Custo Unitário', 'type' => 'moeda', 'align' => 'right', 'style' => 'float:right;', 'length' => array(20, 30)),
					//'T008_Numero_Pedido_Compra' => array('name' => 'T008_Numero_Pedido_Compra', 'label' => 'Número OC', 'type' => 'text', 'length' => array(20, 120)),
					//'T008_Item_Pedido_Compra' => array('name' => 'T008_Item_Pedido_Compra', 'label' => 'Item OC', 'type' => 'text', 'length' => array(20, 120), 'align' => 'right', 'style' => 'float:right;'),
					'T008_Observacao' => array('name' => 'T008_Observacao', 'label' => 'Observação', 'type' => 'text', 'length' => array(64, 120)),
					'T008_Informacoes_Adicionais' => array('name' => 'T008_Observacao', 'label' => 'Observação', 'type' => 'text'),
					'T008_Observacao_Entrega' => array('name' => 'T008_Observacao_Entrega', 'label' => 'Obs. Entrega', 'type' => 'text'),
					//Este campo não existe no banco, é apenas para inserir um serviço na T147
					'idVeiculoOrcamento' => array('name' => 'idVeiculoOrcamento', 'label' => 'Veículo', 'type' => 'select','reject' => true, 'extra'=>'style="width:190px;"',
						'select'=> gGeraSelect('D116', 'D116_Id', 'D116_Placa,D133_Modelo,D134_Cor', 'LEFT JOIN D133 on D133_Id=D116_D133_Id LEFT JOIN D134 on D134_Id=D116_D134_Id WHERE D116_D024_Id=(SELECT T007_D024_Id FROM T007 WHERE T007_Id='.@$r_T007_Id.')', true,@$resultVeiculo['T007_D116_Id']),
						'selectRefresh' => array('D116', 'D116_Id', 'D116_Placa,D133_Modelo,D134_Cor', ' LEFT JOIN D133 on D133_Id=D116_D133_Id LEFT JOIN D134 on D134_Id=D116_D134_Id WHERE D116_D024_Id=(SELECT T007_D024_Id FROM T007 WHERE T007_Id='.@$r_T007_Id.')', true,@$resultVeiculo['T007_D116_Id'])
					),
					'ven001FormVeiculosProdutoOrcamentoBtnNew' => array('name' => 'ven001FormVeiculosProdutoOrcamentoBtnNew', 'type'=>'submit', 'reload' => false, 'value' => 'Veículos', 'reject' => true),

					// Campos Hidden
					'T008_T007_Id' => array('name' => 'T008_T007_Id', 'label' => '', 'type' => 'hidden', 'value' => @$r_T007_Id),
					'T008_C004_Id' => array('name' => 'T008_C004_Id', 'label' => '', 'type' => 'hidden', 'value' => $g['empresaAtual']),
					'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true),
					'T008_D009_Id' => array('name' => 'T008_D009_Id', 'label' => 'T008_D009_Id', 'type' => 'hidden'),
					'D001_Id' => array('name' => 'D001_Id', 'label' => 'D001_Id', 'type' => 'hidden', 'reject'=>true),
					'UltimoPrecoVenda' => array('name' => 'UltimoPrecoVenda', 'label' => "<a href=\"#\" onclick=\"abrirJanela(event, divIdRootAberto(), gridPegarDivId(\$(this)), unique(), 'Histórico', '.: Histórico :.', '/ven/ven001/content/ven001ContentHistoricoProduto/', '&acaoId=' + encodeURIComponent(\$('#{$g['divId']} .itemForm-T008_D009_Id').val()), [900,650]); return false;\">Histórico</a>", 'type' => 'textarea', 'reject'=>true, 'readonly'=>true),
					//'NcmProduto' => array('name' => 'NcmProduto', 'label' => 'NcmProduto','reject' => 'true', 'type' => 'hidden'),
					'T008_Codigo_Produto' => array('name' => 'T008_Codigo_Produto', 'label' => '', 'type' => 'hidden'),
                    'T008_D006_Id' => array('name' => 'T008_D006_Id', 'label' => 'CFOP', 'type' => 'select', 'select' => 
						gGeraSelect('D006', 'D006_Id', 'D006_Codigo_CFOP,D006_Descricao','WHERE IFNULL(D006_Flag_Inativo,"N")!="S" ORDER BY D006_Codigo_CFOP DESC','...',false)
					),
					'T008_Flag_Destino_Produto' => array('name' => 'T008_Flag_Destino_Produto','label'=>'Destino', 'type' => 'select', 
						'select' => $destinoProdutos
					),
					'estoqueLiquido' => array('name' => 'estoqueLiquido', 'label' => "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Pedidos', '/crm/crm001/content/crm001ContentPedidosProduto/', '&acaoId=' + encodeURIComponent(\$('#{$g['divId']} .itemForm-T008_D009_Id').val()), [700,400]); return false;\">Est. Liq.</a>", 'type' => 'moeda', 'length' => array(5,20), 'reject' => true, 'readonly'=>true, 'style' => 'float:right; text-align:right;'),
					'estoqueLoja' => array('name' => 'estoqueLoja', 'label' => 'Est. Loja', 'type' => 'moeda', 'length' => array(5,20), 'reject' => true, 'readonly'=>true, 'style' => 'float:right; text-align:right;'),
					'estoqueTotal' => array('name' => 'estoqueTotal', 'label' => 'Est. Total', 'type' => 'moeda', 'length' => array(5,20), 'reject' => true, 'readonly'=>true, 'style' => 'float:right; text-align:right;'),
					'ordemCompra' => array('name' => 'ordemCompra', 'label' => "<a href=\"#\" onclick=\"abrirJanela(event, divIdRootAberto(), gridPegarDivId(\$(this)), unique(), 'Ordem de Compra', '.: Ordem de Compra :.', '/ven/ven001/content/ven001contentProdutoOC/', '&acaoId=' + encodeURIComponent(\$('#{$g['divId']} .itemForm-T008_D009_Id').val()), [700,400]); return false;\">OC</a>", 'type' => 'moeda', 'length' => array(5,20), 'style' => 'float:right; text-align:right;', 'reject' => true, 'readonly'=>true),
					'estoqueFora' => array('name' => 'estoqueFora', 'label' => "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Estoque Fora', '/cad/cad002/content/cad002ContentEstoqueExterno/', '&acaoId=' + encodeURIComponent(\$('#{$g['divId']} .itemForm-D001_Id').val()) + '&T007_Id=' + encodeURIComponent('{$r_T007_Id}'), [700,400]); return false;\">Est. Fora</a>", 'type' => 'moeda', 'length' => array(5,20), 'reject' => true, 'readonly'=>true, 'style' => 'float:right; text-align:right;'),
					'precoTabela' => array('name' => 'precoTabela', 'label' => 'Prc. Tabela', 'type' => 'moeda', 'length' => array(5,20), 'reject' => true, 'readonly'=>true, 'style' => 'float:right; text-align:right;'),
					//'infoCustoVenda' => array('name' => 'infoCustoVenda', 'label' => '', 'type' => 'titulo', 'length' => array(5,20), 'reject' => true, 'value' => ''),
					'enviado' => array('name' => 'enviado', 'label' => '', 'type' => 'hidden', 'length' => array(5,20), 'reject' => true, 'value' => ''),
					'Codigo_Origem_Mercadoria' => array('name' => 'Codigo_Origem_Mercadoria', 'label' => '', 'type' => 'hidden', 'length' => array(5,20), 'reject' => true),
				),
			)
		);
		// Botão Limpar / Novo Registro
		if (!empty($acaoId)) {
			 $formArray['campos']['produto']['ven003FormProdutosConsumidorBtnNew'] = array('name' => 'ven003FormProdutosConsumidorBtnNew', 'type'=>'submit', 'reload' => false, 'value' => 'Novo Registro', 'reject' => true);
		}
		$formArray['campos']['produto']['ven003FormProdutosConsumidorBtnAdd'] = array('name' => 'ven003FormProdutosConsumidorBtnAdd', 'type'=>'button', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'reject' => true);
		$formArray['campos']['produto']['ven003FormProdutosConsumidorBtnCln'] = array('name' => 'ven003FormProdutosConsumidorBtnCln', 'type'=>'button', 'reload' => false, 'value' => 'Limpar', 'reject' => true);
		$formArray['campos']['produto']['ven001BtnCadastrarProduto'] = array('name' => 'ven001BtnCadastrarProduto', 'type'=>'button', 'reload' => false, 'value' => 'Pré-Cadastrar Produto', 'reject' => true);	
		$form->definirFormularioArray($formArray);
		
		echo <<<EOT
		    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		    	$('#ven001BtnCadastrarProduto').unbind('click');
				$('#ven001BtnCadastrarProduto').unbind('click').bind('click', function(data) {
					//var id = $('#{$g['divId']} #T007_D024_Id').val();
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Pré-cadastrar Produto', '/cad/cad002/content/cad002ContentPreCadastro/', '&T007_Id={$T007_Id}&divForm={$g['divId']}', [600,430]);
					return false;
				});
		    } </script></div>
EOT;
	
		/**
		 * PARTE 3: Processamento do formulário (se foi efnviado)
		 * 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		 */

		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			$D009_Id = $form->campoValorEnviado('T008_D009_Id');
			$T008_Quantidade = $form->campoValorEnviado('T008_Quantidade');
			if($D009_Id != '' && $g['C031']['informarEstoqueInsuficiente'] == 'S' && $form->campoValorEnviado('enviado') == '') {
				echo <<<EOT
				    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				    		$.getJSON('/ven/ven001/form_func-ajax/ven001VerificarEstoqueOrcamento/?ajax=true&D009_Id=' + encodeURIComponent("{$D009_Id}") + '&T004_Quantidade=' + encodeURIComponent('{$T008_Quantidade}') + '&callback=?', function(request) {
					    		if(request.code){
					    			$('#{$g['divId']} #enviado').val('true');
					    			$('#ven003FormProdutosConsumidorBtnAdd').trigger('click');
					    		} else {
						    		var buttons = {
										"Continuar": function() {
											$('#{$g['divId']} #enviado').val('true');
											$('#ven003FormProdutosConsumidorBtnAdd').trigger('click');
											$(this).dialog("destroy").remove();
										},
										"Cancelar": function() { $(this).dialog("destroy").remove(); } 
									};
									dialogConfirm('Atenção', request.data, buttons);
								}
							});
				    } </script></div>
EOT;
			} else {
				// Form com erros
				if ($formCodigo == 1) {
					echo gGeraAlertMsgErroForm($form);
					// Form sem erros
				} else if ($formCodigo == 2) {
					$T008_Codigo_Produto = $form->campoValorEnviado('T008_Codigo_Produto');
	                $T008Codigo = mysql_query("SELECT T008_Codigo_Produto FROM T008 WHERE T008_Codigo_Produto='{$T008_Codigo_Produto}' AND T008_T007_Id='{$r_T007_Id}'");
	                if($g['C031']['permitirProdutosDuplicadosVenda'] == 'N' && mysql_num_rows($T008Codigo) > 0){
	                	echo gGeraAlertMsg('Erro', "Já existe o item com o código ".$T008_Codigo_Produto." nesta venda. Por favor, selecione outro produto.");
	                } else {
						$T008_Id = false;
						$retorno = $VEN003->incluirProduto($form);
						if ($retorno !== true) {
							echo gGeraAlertMsg('Erro', $retorno);	
						} else {
							$T008_Id = $g['mysqlLastId'];
							$VEN003->atualizarAliquotasNF($form->campoValorEnviado('T008_T007_Id'));
							echo <<<EOT
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									divRefresh('{$form->campoValorEnviado('divReload')}', true, false, false, '{$T008_Id}', 'T008_Id'); // grid
									divRefresh('{$r_divOutro}', true, false, 2); // totais
									divRefresh('{$g['divId']}');
								} </script></div>
EOT;
							// Em caso de sucesso, limpa o form (a ação padrão é manter os dados) não precisar dar: divRefresh('{$g['divId']}', true);
							$form->limpar();
						}
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
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk, false, $form_parent, $form_parent_valor);

		/**
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');
	break;

		/**
	 * Formulário Configuracoes
	 */
	case 'ven003formConfiguracoes':
		
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
        $T007_Id = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($r_T007A_Id)) ? $r_T007A_Id : false;

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
		$form_tabela       = 'T007A';
		$form_campo_pk     = 'T007A_Id';
        $form_parent       = 'T007_Id';
        $form_parent_valor = $T007_Id;
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId ='ven003formConfiguracoesBtnAdd';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/ven/ven003/form/ven003formConfiguracoes/';

		//form editor
		$form_editor = array('geral' => '{"T007A_Flag_Conversao_Unidade":{"top":7,"left":13.859375,"width":55,"hidden":0},"ven003formConfiguracoesBtnAdd":{"top":51,"left":208,"width":null,"hidden":0}}');

		// novo formulário
		$form = new Formulario('ven003formConfiguracoes');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T007A_T007_Id' => array('name' =>'T007A_T007_Id', 'label'=>'T007A_T007_Id', 'value' => $T007_Id, 'type' => 'hidden','readonly' => true),
					'T007A_Flag_Conversao_Unidade' => array('name' => 'T007A_Flag_Conversao_Unidade', 'label'=>'Conversão de unidade','type' => 'select','select' => 
						array(
							array('title' => 'Não', 'value' => 'N'),
							array('title' => 'Sim', 'value' => 'S'),						
						)
					),
					'ven003formConfiguracoesBtnAdd' => array('name' => 'ven003formConfiguracoesBtnAdd', 'type'=>'submit', 'reload' => false, 'value' => empty($acaoId) ? 'Alterar' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true),
				)
			)
		);
		
		//$formArray['campos']['geral']['ven003formConfiguracoesBtnAdd'] = array('name' => 'ven003formConfiguracoesBtnAdd', 'type'=>'button', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$form->definirFormularioArray($formArray);

		/**
		 * PARTE 3: Processamento do formulário (se foi efnviado)
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
						
						if($form->campoValorEnviado('T007A_Flag_Conversao_Unidade') == 'S'){
						
							// Aqui é uma segurança caso o cliente não utilize unidade conversão e mudar para usar
							// E o pedido já foi criado antes de alterar a opção, repassa os valores para tabela estendida
							mysqli_query("UPDATE T008A 
									   LEFT JOIN T008 ON T008_Id = T008A_T008_Id
											 SET T008A_Quantidade_Comercial     = T008_Quantidade,
												 T008A_Preco_Unitario_Comercial = T008_Valor_Preco_Sem_Desconto_Unitario
										   WHERE T008_T007_Id ='{$T007_Id}' 
											 AND T008A_Quantidade_Comercial <= 0
											 AND T008A_Preco_Unitario_Comercial <= 0");	
											 
						} elseif ($form->campoValorEnviado('T007A_Flag_Conversao_Unidade') == 'N') {
							
							$totalizarVenda = false;
							// Se tiver um item com unidade conversão seta se precisa totalizar a venda com os novos valores
							$T008 = mysql_query("SELECT T008_Id 
												   FROM T008 
											  LEFT JOIN T008A ON T008A_T008_Id = T008_Id
												  WHERE T008_T007_Id = '{$T007_Id}'
													AND T008A_D184_Id > 0");
							if(mysql_num_rows($T008) > 0){
								$totalizarVenda = true;
							}
							
							mysqli_query("UPDATE T008A 
									   LEFT JOIN T008 ON T008_Id = T008A_T008_Id
									   LEFT JOIN D037 ON D037_Id = T008_D037_Id
											 SET T008_Quantidade			    = T008A_Quantidade_Comercial,
											 	 T008_Unidade 				    = D037_Unidade,
												 T008_D037_Id_Tributavel		= D037_Id,
												 T008A_Quantidade_Comercial     = 0,
												 T008A_Preco_Unitario_Comercial = 0,
												 T008A_D184_Id                  = 0
										   WHERE T008_T007_Id ='{$T007_Id}' 
											 AND T008A_Quantidade_Comercial > 0
											 AND T008A_Preco_Unitario_Comercial > 0");	
						
							if($totalizarVenda){
								// Da um UPDATE para chamar a trigger e realizar o gravar totalizacao
								mysql_query("UPDATE T007 SET T007_Flag_Indicador_Presenca = T007_Flag_Indicador_Presenca
													   WHERE T007_Id = '{$T007_Id}'");
							}
						}

						// Imprime uma mensagem e fecha a janela
						echo gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!');
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								divRefresh('{$form->campoValorEnviado('divReload')}', true);
								divRefresh('{$g['divId']}', true);
							} </script></div>
EOT;
					}
				} else {
					// Insert
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {	
						$T007A = $g['mysqlLastId'];	
						echo gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!');
						echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							divRefresh('{$g['divIdParent']}', true);
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
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk, false, $form_parent, $form_parent_valor);


		/**
		 * PARTE 5: Exibir o formulário
		 */
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');			
	break;

	case 'ven003FormCadastroObservacao':
		$T007_Id = isset($r_T007_Id) ? $r_T007_Id : false;
		$acaoId = isset($r_acaoId) ? $r_acaoId : false;
		$tab = isset($r_tab) ? $r_tab : 'geral';
		$formId = uniqid();
		$form_tabela = 'T297';
		$form_campo_pk = 'T297_Id';
		$enviar_btnId = 'ven003FormCadastroObservacaoBtn';
		$enviar_acaoHref = '/ven/ven003/form/ven003FormCadastroObservacao/';
		$formArray = array('campos' => array('geral' => array(
			'T297_D188_Id' => array('name' => 'T297_D188_Id', 'label' => 'Observação', 'type' => 'pesquisa', 'pesquisa' => array('D188', 'D188_Id', 'D188_Codigo_Observacao,D188_Descricao_Observacao')),
			'T297_Descricao_Complementar_Observacao' => array('name' => 'T297_Descricao_Complementar_Observacao', 'label' => 'Descrição Complementar', 'type' => 'textarea'),
			'T297_T007_Id' => array('name' => 'T297_T007_Id', 'value' => $T007_Id, 'type' => 'hidden'),
		)));
		$form = new Formulario('ven003FormCadastroObservacao');
		$form->definirFormularioArray($formArray);
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
			} elseif ($formCodigo == 2) {
				$sql = !empty($acaoId) ? $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form) : $g['sqlAuto']->gerarSQLInsert($form_tabela, $form);
				if ($sql) {
					$alert = gGeraAlertMsg('Operação OK', !empty($acaoId) ? 'Registro Alterado com Sucesso!' : 'Registro Incluído com Sucesso!', false);
					echo <<<EOT
					<div class="divHidden"><script type="text/javascript">if (typeof jQuery != 'undefined') {
						$('#{$g['divId']}').closest('.ui-dialog-content').bind('dialogclose', function() { setTimeout(function() { {$alert} }, 150); });
						$('#{$g['divId']}').closest('.ui-dialog-content').dialog('close');
						divRefresh('{$g['divIdParent']}', true);
					}</script></div>
EOT;
				}
			}
		} elseif (!empty($acaoId)) {
			$processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $acaoId);
			if ($processado !== true) { echo $processado; }
		}
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']);
		$g['smarty']->assign('form_tabela', $form_tabela);
		echo $gerado['pagina'];
		$g['smarty']->assign('formId', $formId);
		$g['smarty']->display('formEditor.tpl');
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', '');
		$g['smarty']->assign('botoesMid', '');
		$g['smarty']->assign('botoesDir', '<button type="submit" id="ven003FormCadastroObservacaoBtn">Enviar</button><button onclick="fecharJanela(\'' . $g['divId'] . '\'); return false;" class="btnFechar">Cancelar</button>');
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'ven003FormCadastroObrigacaoTributaria':
		$T007_Id = isset($r_T007_Id) ? $r_T007_Id : false;
		$T297_Id = isset($r_T297_Id) ? $r_T297_Id : false;
		$acaoId = isset($r_acaoId) ? $r_acaoId : false;
		$tab = isset($r_tab) ? $r_tab : 'geral';
		$formId = uniqid();
		$form_tabela = 'T298';
		$form_campo_pk = 'T298_Id';
		$enviar_btnId = 'ven003FormCadastroObrigacaoTributariaBtn';
		$enviar_acaoHref = '/ven/ven003/form/ven003FormCadastroObrigacaoTributaria/';
		$formArray = array('campos' => array('geral' => array(
			'T298_D189_Id' => array('name' => 'T298_D189_Id', 'label' => 'Ajuste', 'type' => 'pesquisa', 'pesquisa' => array('D189', 'D189_Id', 'D189_Codigo_Ajuste,D189_Descricao_Ajuste')),
			'T298_Descricao_Complementar_Ajuste' => array('name' => 'T298_Descricao_Complementar_Ajuste', 'label' => 'Descrição Complementar Ajuste', 'type' => 'text'),
			'T298_T008_Id' => array('name' => 'T298_T008_Id', 'label' => 'Código Item', 'type' => 'pesquisa', 'pesquisa' => array('T008', 'T008_Id', 'D001_Codigo_Produto,T008_Descricao_Produto', "LEFT JOIN D009 ON D009_Id = T008_D009_Id LEFT JOIN D049 ON D049_Id = D009_D049_Id LEFT JOIN D001 ON D001_Id = D049_D001_Id WHERE T008_T007_Id = '{$T007_Id}'")),
			'T298_Valor_Base_ICMS' => array('name' => 'T298_Valor_Base_ICMS', 'label' => 'Base ICMS', 'type' => 'moeda'),
			'T298_Aliquota_ICMS' => array('name' => 'T298_Aliquota_ICMS', 'label' => 'Alíquota ICMS', 'type' => 'moeda'),
			'T298_Valor_ICMS' => array('name' => 'T298_Valor_ICMS', 'label' => 'Valor ICMS', 'type' => 'moeda'),
			'T298_Outros_Valores' => array('name' => 'T298_Outros_Valores', 'label' => 'Valor Outros', 'type' => 'moeda'),
			'T298_T297_Id' => array('name' => 'T298_T297_Id', 'value' => $T297_Id, 'type' => 'hidden'),
		)));
		$form = new Formulario('ven003FormCadastroObrigacaoTributaria');
		$form->definirFormularioArray($formArray);
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
			} elseif ($formCodigo == 2) {
				$sql = !empty($acaoId) ? $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form) : $g['sqlAuto']->gerarSQLInsert($form_tabela, $form);
				if ($sql) {
					$alert = gGeraAlertMsg('Operação OK', !empty($acaoId) ? 'Registro Alterado com Sucesso!' : 'Registro Incluído com Sucesso!', false);
					echo <<<EOT
					<div class="divHidden"><script type="text/javascript">if (typeof jQuery != 'undefined') {
						$('#{$g['divId']}').closest('.ui-dialog-content').bind('dialogclose', function() { setTimeout(function() { {$alert} }, 150); });
						$('#{$g['divId']}').closest('.ui-dialog-content').dialog('close'); divRefresh('{$g['divIdParent']}', true);
					}</script></div>
EOT;
				}
			}
		} elseif (!empty($acaoId)) {
			$processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $acaoId);
			if ($processado !== true) { echo $processado; }
		}
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']);
		$g['smarty']->assign('form_tabela', $form_tabela);
		echo $gerado['pagina'];
		$g['smarty']->assign('formId', $formId);
		$g['smarty']->display('formEditor.tpl');
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', '');
		$g['smarty']->assign('botoesMid', '');
		$g['smarty']->assign('botoesDir', '<button type="submit" id="ven003FormCadastroObrigacaoTributariaBtn">Enviar</button><button onclick="fecharJanela(\'' . $g['divId'] . '\'); return false;" class="btnFechar">Cancelar</button>');
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'ven003FormCadastroCiap':
		
		$T007_Id = isset($r_T007_Id) ? $r_T007_Id : false;	
		$acaoId = isset($r_T316_Id) ? $r_T316_Id : false;	

		$T007 = mysql_query("SELECT T007_Data_Emissao
							   FROM T007
							  WHERE T007_Id = '{$T007_Id}'");
		$mT007 = mysql_fetch_array($T007);

		$mT007['T007_Data_Emissao'] = date("d/m/Y", strtotime($mT007['T007_Data_Emissao']));

		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;
		
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
		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = 'T316';
		$form_campo_pk = 'T316_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'ven003FormCadastroCiapBtn';
		// Form que será executado ao clicar no botão54
		$enviar_acaoHref = '/ven/ven003/form/ven003FormCadastroCiap/';
		
		$form_editor = array(
			'geral' => '{"T316_T008_Id":{"top":6.95465087890625,"left":80.39215087890625,"width":298.039216,"hidden":0},"T316_Codigo_Produto_CIAP":{"top":6.98529052734375,"left":454.871337890625,"width":88.03921600000001,"hidden":0},"T316_Descricao_Utilizacao_Bem":{"top":29.993865966796875,"left":8.76226806640625,"width":504.039216,"hidden":0},"T316_Data_Inicio_Apropriacao_Credito":{"top":52.98713684082031,"left":26.16424560546875,"width":104.03921600000001,"hidden":0},"T316_Numero_Parcelas_Apropriacao":{"top":75.99571228027344,"left":57.1844482421875,"width":104.03921600000001,"hidden":0},"T316_Flag_Ativo_Baixado":{"top":98.98898315429688,"left":83.19549560546875,"width":111,"hidden":0}}'
		);

		$readonly = ($acaoId > 0) ? true : false ;

		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(					
					'T316_T008_Id' => array('name' => 'T316_T008_Id', 'label' => 'Código', 'readonly' => $readonly , 'type' => 'pesquisa', 
						'pesquisa' => array('T008','T008_Id','D001_Codigo_Produto,T008_Descricao_Produto', "LEFT JOIN D009 ON D009_Id = T008_D009_Id LEFT JOIN D049 ON D049_Id = D009_D049_Id LEFT JOIN D001 ON D001_Id = D049_D001_Id  WHERE T008_T007_Id = '{$T007_Id}' AND D001_Flag_Tipo_Item = '08'")						
					),			
					'T316_Codigo_Produto_CIAP' => array('name' => 'T316_Codigo_Produto_CIAP', 'label' => 'Cód.Escrituração', 'type' => 'text','length' => array(60,60)),		
					'T316_Data_Inicio_Apropriacao_Credito' => array('name' => 'T316_Data_Inicio_Apropriacao_Credito', 'label' => 'Início Apropriação', 'type' => 'data', 'value' => $mT007['T007_Data_Emissao']),
					'T316_Numero_Parcelas_Apropriacao' => array('name' => 'T316_Numero_Parcelas_Apropriacao', 'label' => 'Nº Parcelas', 'type' => 'text'),
					'T316_Descricao_Utilizacao_Bem' => array('name' => 'T316_Descricao_Utilizacao_Bem', 'label' => 'Descr. Utilização Bem', 'type' => 'text','length' => array(60,60)),
					'T316_Flag_Ativo_Baixado' => array('name' => 'T316_Flag_Ativo_Baixado','label'=>'Baixado', 'type' => 'select', 
						'select' => array(
								array('title' => 'Não', 'value' => 'N', 'selected' => true),
								array('title' => 'Sim', 'value' => 'S'),
				
						)  			
					),					
				)
			)
		);
        $form = new Formulario('ven003FormCadastroObservacao');
		$form->definirFormularioArray($formArray);

		/**
		 * PARTE 3: Processamento do formulário (se foi enviado)
		 * E carregamento dos valores (se não foi enviado parametro acaoId)
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
						// Fecha a janela, recarrega o parent (grid) e emite uma mensagem de alerta
						$alert = gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										{$alert}
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
				} else {
					// Insert
					$T316_Codigo_Produto_CIAP = $form->campoValorEnviado('T316_Codigo_Produto_CIAP');

					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						$T316_Id = $g['mysqlLastId'];
						if(empty($T316_Codigo_Produto_CIAP)){
							$T316 = mysql_query("SELECT D001_Codigo_Produto 
										    	   FROM T316
									          LEFT JOIN T008 ON T008_Id = T316_T008_Id
											  LEFT JOIN D009 ON D009_Id = T008_D009_Id
									          LEFT JOIN D049 ON D049_Id = D009_D049_Id
									          LEFT JOIN D001 ON D001_Id = D049_D001_Id
									              WHERE T316_Id = '{$T316_Id}'");
							
							$mT316 = mysql_fetch_array($T316);

							mysqli_query("UPDATE T316 SET T316_Codigo_Produto_CIAP = '{$mT316['D001_Codigo_Produto']}' WHERE T316_Id = '{$T316_Id}'");
						}


						// Fecha a janela, recarrega o parent (grid), emite uma mensagem de alerta e abre o registro para alteração (o que acabou de ser incluido)
						$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								// Fecha a janela, recarrega o grid e abre a alteração
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										
										{$alert}
									}, 150);
	  							});
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								divRefresh('{$g['divIdParent']}', true);
							} </script></div>
EOT;
					}
				}
			}
		} else {			
			// Carrega os valores caso seja um update
			if (!empty($acaoId)) {
				$processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $acaoId);
				// Mostra erros de formulário uteis ao desenvolvedor
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

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="ven003FormCadastroCiapBtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;
}
