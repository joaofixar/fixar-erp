<?php
namespace hardness;
require_once("bibliotecas/classes/VEN001.php");
$VEN001 = new VEN001();
require_once("bibliotecas/classes/CAD002.php");
$CAD002 = new CAD002();
require_once('prog/ven/ven001/form_func-js.php');

/**
* Ações
*/
switch ($g['r'][3]) {
	/**
	* Form Principal
	* Chamado em: ven001tab02
	*/
	case 'ven001FormOrcamento':
		global $g;
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$T139_Id = (isset($r_formFin002FormOrcamento)) ? $r_formFin002FormOrcamento['T139_Id'] : @$r_T139_Id;
		$D085_Id = (isset($r_D085_Id)) ? $r_D085_Id : false;
		$r_D024_Id_CRM = (isset($r_D024_Id_CRM)) ? $r_D024_Id_CRM : false;
		if(!empty($r_D024_Id_CRM)){
			$sqlD024CRM = <<<EOT
			SELECT * FROM D024
			LEFT JOIN D018 on D018_Id=D024_D018_Id
			LEFT JOIN D020 on D020_Id=D024_D020_Id 
			LEFT JOIN D022 ON D022_Id=D024_D022_Id
			WHERE D024_Id='{$r_D024_Id_CRM}'
EOT;
			$resultD024CRM = mysql_query($sqlD024CRM);
			$linhaD024CRM = mysql_fetch_array($resultD024CRM);
			$linhaD024CRM['D024_Nome_Empresa'] = mysql_real_escape_string($linhaD024CRM['D024_Nome_Empresa']);
			$linhaD024CRM['D020_Nome_Cidade'] = mysql_real_escape_string($linhaD024CRM['D020_Nome_Cidade']);
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} #T003_D024_Id_Pesquisa').val('[{$r_D024_Id_CRM}] ' + '{$linhaD024CRM['D024_Nome_Empresa']}' + ' [{$linhaD024CRM['D020_Nome_Cidade']}/{$linhaD024CRM['D018_UF']}] - TEL ({$linhaD024CRM['D024_DDD_Telefone_1']}) {$linhaD024CRM['D024_Telefone_1']}');
				$('#{$g['divId']} #T003_D024_Id').val('{$r_D024_Id_CRM}');
				$('#{$g['divId']} #T003_D022_Id_Pesquisa').val('{$linhaD024CRM['D022_Nome_Empresa']} - {$linhaD024CRM['D020_Nome_Cidade']} - {$linhaD024CRM['D018_UF']}');
				$('#{$g['divId']} #T003_D022_Id').val('{$linhaD024CRM['D022_Id']}');
				setTimeout(function(){
					$('#{$g['divId']} #T003_D024_Id_Pesquisa').trigger('change');
				}, 350);
			} </script></div>
EOT;
		}
		
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
		//Carregar CFOP pela tela de E-mail.__________________________
        if (isset($r_T139_Id) && !empty($r_T139_Id)){
			$sql = "SELECT T139_D024_Id, T139_D013_Id FROM T139 WHERE T139_Id = '{$r_T139_Id}'";
			$result = mysql_query($sql);
			$aD024 = mysql_fetch_assoc($result);
			if (!empty($aD024['T139_D024_Id'])) {
		        echo <<<EOT
		        <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					setTimeout(function() {
					    $("#{$g['divId']}").showLoading();
						$.getJSON('/ven/ven001/form_func-ajax/carregaCFOP_Contatos/?ajax=true&T003_D024_Id=' + encodeURIComponent('{$aD024['T139_D024_Id']}') + '&D013_Contato_Pre_Selecionado=' + encodeURIComponent('{$aD024['T139_D013_Id']}') + '&callback=?', function(request) {
						    $("#{$g['divId']}").hideLoading();
							if (request.code) {
								$('#T003_D006_Id').html(request.data['T003_D006_Id']);
								$('#T003_D116_Id').html(request.data['T003_D116_Id']);
								$('#T003_D116_Id_refreshBtn').html(request.data['T003_D116_Id_refreshBtn']);
								$('#T003_D013_Id').html(request.data['T003_D013_Id']);
								$('#T003_D013_Id_refreshBtn').html(request.data['T003_D013_Id_refreshBtn']);
								//reloadUI("{$g['divId']}");
							} else {
								var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
    							dialogConfirm("Erro!", request.data, buttons)
							}
						});
					}, 100);
		        } </script></div>
EOT;
			}
		}
		//Carregar CFOP pela tela de Agenda.__________________________
        if (isset($r_D085_Id) && !empty($r_D085_Id)){
			$sql = "SELECT D085_D024_Id, D085_D013_Id FROM D085 WHERE D085_Id = '{$r_D085_Id}'";
			$result = mysql_query($sql);
			$aD024 = mysql_fetch_assoc($result);
			if (!empty($aD024['D085_D024_Id'])) {
		        echo <<<EOT
		        <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					setTimeout(function() {
					    $("#{$g['divId']}").showLoading();
						$.getJSON('/ven/ven001/form_func-ajax/carregaCFOP_Contatos/?ajax=true&T003_D024_Id=' + encodeURIComponent('{$aD024['D085_D024_Id']}') + '&D013_Contato_Pre_Selecionado=' + encodeURIComponent('{$aD024['D085_D013_Id']}') + '&callback=?', function(request) {
						    $("#{$g['divId']}").hideLoading();
							if (request.code) {
								$('#T003_D006_Id').html(request.data['T003_D006_Id']);
								$('#T003_D116_Id').html(request.data['T003_D116_Id']);
								$('#T003_D116_Id_refreshBtn').html(request.data['T003_D116_Id_refreshBtn']);
								$('#T003_D013_Id').html(request.data['T003_D013_Id']);
								$('#T003_D013_Id_refreshBtn').html(request.data['T003_D013_Id_refreshBtn']);
								//reloadUI("{$g['divId']}");
							} else {
								var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
    							dialogConfirm("Erro!", request.data, buttons)
							}
						});
					}, 100);
		        } </script></div>
EOT;
			}
		}

		//_________________________________________
		// busca o ID do cliente pra carregar os contatos, caso seja edição
		$idCliente = $r_idCliente ? $r_idCliente : '99999999999';
		$listaPreco = array(false, false, false, false);
		
		if ($acaoId>0){
			$sql = "SELECT T003_D024_Id, T003_C007_Id_Vendedor_Interno, T003_C007_Id_Vendedor_Externo, C007_C007_Id, C007_C007_Id_2 FROM T003 LEFT JOIN C007 ON C007_Id=T003_C007_Id_Vendedor_Interno WHERE T003_Id = '{$acaoId}'";
			$result = mysql_query($sql);
			$aD024 = mysql_fetch_assoc($result);		
			$idCliente=$aD024['T003_D024_Id'];	
			$idVendedorInterno=$aD024['T003_C007_Id_Vendedor_Interno'];
			$idVendedorExterno=$aD024['T003_C007_Id_Vendedor_Externo'];
		} else if (isset($r_formFin002FormOrcamento)) {
			$idCliente = $r_formFin002FormOrcamento['T003_D024_Id'];
		} else if (isset($r_consumidor) && $r_consumidor) {
			// Caso seja "consumidor", preenche os dados para inclusão
			$res = mysql_query("SELECT D024_Id, D024_Flag_Lista_Preco FROM D024 WHERE D024_Id = '{$g['C020']['C020_Cupom_Fiscal_Cliente']}'");
			if (mysql_num_rows($res)) {
				$row = mysql_fetch_assoc($res);
				$idCliente = $row['D024_Id'];
				$idListaPreco = $row['D024_Flag_Lista_Preco'];
				$listaPreco[$idListaPreco] = true;
			} else {
				$res = mysql_query("SELECT D024_Id, D024_Flag_Lista_Preco FROM D024 WHERE D024_Id = '{$g['C031']['codigoClienteCupomFiscal']}'");
				if (mysql_num_rows($res)) {
					$row = mysql_fetch_assoc($res);
					$idCliente = $row['D024_Id'];
					$idListaPreco = $row['D024_Flag_Lista_Preco'];
					$listaPreco[$idListaPreco] = true;
				}
			}
		}

		$clientePorEmpresa = '';
		if($g['C031']['separarClientePorEmpresa'] == 'S'){
			$clientePorEmpresa = "AND (IFNULL(D024_C004_Id,0) = '{$g['empresaAtual']}' OR IFNULL(D024_C004_Id,0) = 0)";
		}

		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'T003';
		$form_campo_pk = 'T003_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'content_form1Orcamento_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/ven/ven001/form/ven001FormOrcamento/';
		// editor
		if($g['C031']['modeloExpedicao'] == '1'){
			$form_editor = array(
				'orcamentos' => '{"T003A_D148_Id_Redespacho":{"top":490.0000457763672,"left":45.52083492279053,"width":462,"hidden":0},"T003_D024_Id":{"top":6.9965362548828125,"left":113.31597805023193,"width":373.77778,"hidden":0},"btnOrcamentoIncluirCliente":{"top":5,"left":565.9896306991577,"width":null,"hidden":0},"T003_D013_Id":{"top":30,"left":108.31597805023193,"width":395,"hidden":0},"ven001FormContatos":{"top":27.986114501953125,"left":565.9896306991577,"width":null,"hidden":0},"T003_Cliente_Area":{"top":52.986114501953125,"left":131.9791784286499,"width":455.77778,"hidden":0},"T003_Flag_Destino_Produto":{"top":75.98959350585938,"left":109.8090353012085,"width":464,"hidden":0},"T003_Prazo_Entrega":{"top":98.99305725097656,"left":93.83681392669678,"width":87.77778,"hidden":0},"T003_Ordem_Compra":{"top":98.99305725097656,"left":289.70487117767334,"width":247.77778,"hidden":0},"T003_Data_Emissao":{"top":121.99653625488281,"left":105.00000667572021,"width":87.77778,"hidden":0},"T003_C007_Id_Vendedor_Interno":{"top":121.99653625488281,"left":308.24652767181396,"width":255,"hidden":0},"T003_Flag_Lista_Preco":{"top":145.00001525878906,"left":92.17014217376709,"width":96,"hidden":0},"T003_C007_Id_Vendedor_Externo":{"top":145.00001525878906,"left":325.39932918548584,"width":255,"hidden":0},"T003_Flag_Acp":{"top":167.9861297607422,"left":66.37152767181396,"width":96,"hidden":0},"T003_D021_Id":{"top":167.9861297607422,"left":291.73612117767334,"width":255,"hidden":0},"T003_Observacao":{"top":190.98960876464844,"left":61.180556297302246,"height":59.77778000000001,"width":455.77778,"hidden":0},"T003_Observacao_Comercial":{"top":260.00001525878906,"left":45.399306297302246,"height":59.77778000000001,"width":455.77778,"hidden":0},"T003_Flag_Frete":{"top":328.9930877685547,"left":129.7916784286499,"width":464,"hidden":0},"T003_D036_Id":{"top":351.9965362548828,"left":132.93402767181396,"width":463,"hidden":0},"T003_D022_Id":{"top":375.0000457763672,"left":79.25347805023193,"width":441.77778,"hidden":0},"T003_Valor_Frete":{"top":397.9861297607422,"left":389.6528081893921,"width":167.77778,"hidden":0},"T003_Valor_Despesas_Acessorias":{"top":420.98960876464844,"left":50.62500286102295,"width":167.77778,"hidden":0},"T003_D148_Id_Entrega":{"top":443.9930877685547,"left":68.54167079925537,"width":463,"hidden":0},"ven001FormEnderecos":{"top":441.99656677246094,"left":640.0000066757202,"width":null,"hidden":0},"T003_D148_Id_Cobranca":{"top":466.99656677246094,"left":59.16667079925537,"width":463,"hidden":0},"T003_T134_Id":{"top":512.9861297607422,"left":18.628474235534668,"width":464,"hidden":0},"content_form1Orcamento_btn":{"top":510.98960876464844,"left":640.0000066757202,"width":null,"hidden":0}}'
			);
		} else if($g['C031']['modeloExpedicao'] == '2'){
			$form_editor = array(
				'orcamentos' => '{"T003_D024_Id":{"top":7,"left":113.328125,"width":374,"hidden":0},"btnOrcamentoIncluirCliente":{"top":5,"left":566,"width":null,"hidden":0},"T003_Flag_Expedicao":{"top":191,"left":104.015625,"width":97,"hidden":0},"T003_D013_Id":{"top":30,"left":108.296875,"width":395,"hidden":0},"ven001FormContatos":{"top":28,"left":566,"width":null,"hidden":0},"T003_Cliente_Area":{"top":53,"left":132,"width":456,"hidden":0},"T003_D006_Id":{"top":53,"left":680,"width":351,"hidden":1},"T003_Flag_Destino_Produto":{"top":76,"left":109.828125,"width":464,"hidden":0},"T003_Percentual_Comissao":{"top":76,"left":709,"width":228,"hidden":1},"T003_Prazo_Entrega":{"top":99,"left":93.828125,"width":88,"hidden":0},"T003_Ordem_Compra":{"top":99,"left":289.6875,"width":248,"hidden":0},"T003_Data_Emissao":{"top":122,"left":104.984375,"width":88,"hidden":0},"T003_C007_Id_Vendedor_Interno":{"top":122,"left":308.25,"width":255,"hidden":0},"T003_Flag_Lista_Preco":{"top":145,"left":92.15625,"width":96,"hidden":0},"T003_C007_Id_Vendedor_Externo":{"top":145,"left":325.40625,"width":255,"hidden":0},"T003_Validade_Proposta":{"top":145,"left":739,"width":225,"hidden":1},"T003_Flag_Acp":{"top":168,"left":66.34375,"width":96,"hidden":0},"T003_D021_Id":{"top":168,"left":291.75,"width":255,"hidden":0},"T003_Frete":{"top":168,"left":738,"width":225,"hidden":1},"T003_Observacao":{"top":214,"left":61.171875,"height":60,"width":456,"hidden":0},"T003_Forma_Pagamento":{"top":191,"left":740,"width":232,"hidden":1},"T003_Flag_Revenda_Consumidor":{"top":214,"left":838,"width":172,"hidden":1},"T003_Observacao_Comercial":{"top":283,"left":45.375,"height":60,"width":456,"hidden":0},"T003_D116_Id":{"top":305.984375,"left":855,"width":42,"hidden":1},"T003_Flag_Frete":{"top":352,"left":129.796875,"width":224,"hidden":0},"T003_D036_Id":{"top":352,"left":420.921875,"width":175,"hidden":0},"T003_D022_Id":{"top":375,"left":80.234375,"width":442,"hidden":0},"T003_Valor_Frete_Calculado":{"top":398,"left":50.1875,"width":168,"hidden":0},"T003_Valor_Frete":{"top":398,"left":389.671875,"width":168,"hidden":0},"T003_Valor_Despesas_Acessorias":{"top":421,"left":50.609375,"width":168,"hidden":0},"T003_D148_Id_Entrega":{"top":444,"left":68.53125,"width":463,"hidden":0},"ven001FormEnderecos":{"top":442,"left":640,"width":null,"hidden":0},"T003_D148_Id_Cobranca":{"top":467,"left":59.140625,"width":463,"hidden":0},"T003_T134_Id":{"top":490,"left":18.578125,"width":464,"hidden":0},"content_form1Orcamento_btn":{"top":488,"left":640,"width":null,"hidden":0},"T003_C004_Id_Estoque":{"top":490,"left":930.78125,"width":201,"hidden":1}}'
			);
		}

		// novo formulário
		$form = new Formulario('formFin002FormOrcamento');

		$pesquisaGrid = array(
			'D024',																											// tabela
			'D024_Id',																										// chave primaria
			'D024_Id,D024_Nome_Empresa,D024_Nome_Fantasia,D020_Nome_Cidade,D018_UF,D024_Cpf,D024_Cnpj,D024_DDD_Telefone_1,D024_Telefone_1',	// campos
			'left join D020 on D020_Id=D024_D020_Id left join D018 on D018_Id=D024_D018_Id WHERE D024_Flag_Cliente="S" and D024_Flag_Ativo="S" order by D024_Nome_Empresa', // extras sql
			'Pesquisa de Clientes',																							// título da janela
			'Código,N. Empresa,N. Fantasia,Cidade,UF,CPF,CNPJ,DDD,Telefone',												// descrição dos campos
			'campoProcurarCliente',																							// callback js
			$g['divId'],																									// divId
			0,																												// parametro extra
			array()																											// parametros para cada campo do grid
		);

	// endereço de entrega
		$arrayEndereco = gGeraSelect('D148','D148_Id','D148_Nome','WHERE D148_D024_Id='.$idCliente.' ORDER BY D148_Id',true);
		$arrayEndereco[0]['title'] = 'PADRÃO';

		if(empty($idCliente)){
			$idCliente = 0;
		}

		// Vendedor INTERNO
		if($g['nivelAlcada']==4){
			$vendedorInternoRead = false;
			$vendedorInternoExtra = "";
		} else if ($g['nivelAlcada']==2 && ($aD024['C007_C007_Id']==$g['usuarioAtual'] || $aD024['C007_C007_Id_2']==$g['usuarioAtual'])) {
			$vendedorInternoRead = false;
			$vendedorInternoExtra = "";
		} else {
			$vendedorInternoRead = true;
			$vendedorInternoExtra = "onfocus='this.initialSelect = this.selectedIndex;' onchange='this.selectedIndex = this.initialSelect;'";
			echo "<script>document.getElementById('T003_C007_Id_Vendedor_Interno_refreshBtn').remove();</script>";
		}
		if($acaoId > 0){
			$sqlC007 = mysql_query("SELECT C007_Flag_Ausente FROM T003 LEFT JOIN D024 ON D024_Id=T003_D024_Id LEFT JOIN C007 ON C007_Id=D024_C007_Vendedor_Interno WHERE T003_Id = '{$acaoId}'");
			$rowC007 = mysql_fetch_array($sqlC007);
			if(($rowC007['C007_Flag_Ausente'] == 'S' && $g['C031']['alterarVendedorAusente'] == 'S') || ($g['nivelAlcada']==4) || $g['C031']['alterarVendedor'] == 'S'){
				$vendedorInternoRead = false;
				$vendedorInternoExtra = "";
			}
		}

		// Vendedor EXTERNO
		if($g['nivelAlcada']==4){
			$vendedorExternoRead = false;
			$vendedorExternoExtra = "";
		}	 else {
			$vendedorExternoRead = true;
			$vendedorExternoExtra = "onfocus='this.initialSelect = this.selectedIndex;' onchange='this.selectedIndex = this.initialSelect;'";
			echo "<script>document.getElementById('T003_C007_Id_Vendedor_Externo_refreshBtn').remove();</script>";
		}
		if($acaoId > 0){
			$sqlC007 = mysql_query("SELECT C007_Flag_Ausente FROM T003 LEFT JOIN D024 ON D024_Id=T003_D024_Id LEFT JOIN C007 ON C007_Id=D024_C007_Vendedor_Externo WHERE T003_Id = '{$acaoId}'");
			$rowC007 = mysql_fetch_array($sqlC007);
			if(($rowC007['C007_Flag_Ausente'] == 'S' && $g['C031']['alterarVendedorAusente'] == 'S') || ($g['nivelAlcada']==4) || $g['C031']['alterarVendedor'] == 'S'){
				$vendedorExternoRead = false;
				$vendedorExternoExtra = "";
			}
		}

		$faixaCalculo = !empty($g['C031']['padraoFaixaCalculoIcmsCredito']) ? $g['C031']['padraoFaixaCalculoIcmsCredito'] : '';
		//Buscas destinos, foi centralizado nesta classe e vai valer para todos os locais
		$destinoProdutos = $CAD002->retornaDestinoProdutos($r_consumidor,'V');

		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'orcamentos' => array(
					'T003_D024_Id' => array('name' => 'T003_D024_Id', 'label'=> "Cliente", 'type' => 'pesquisa', 'extra' => "onchange=\"divSetarAcaoId('{$g['divId']}', \$('#T003_D024_Id').val(), 'idCliente'); carregaCFOPContatos(this, '{$g['divId']}', '', '{$g['C031']['alterarVendedorAusente']}', '{$g['C031']['alterarVendedor']}'); verificaFlagContribuinte(this, '{$g['divId']}','{$g['divIdRoot']}','{$g['divIdParent']}'); verificaListaPreco(this, '{$g['divId']}');\"", 'pesquisa' => array('D024','D024_Id','D024_Nome_Empresa,D020_Nome_Cidade,D018_UF,D024_Cpf,D024_Cnpj,D024_DDD_Telefone_1,D024_Telefone_1,D024_Nome_Fantasia,D024_Id','left join D020 on D020_Id=D024_D020_Id left join D018 on D018_Id=D024_D018_Id WHERE D024_Flag_Cliente="S" and D024_Flag_Ativo="S" '.$clientePorEmpresa.' order by D024_Nome_Empresa','%1 - %9 - (%4%5) -  [%2/%3]'), 'length' => array(80,30)),
					//'T003_D024_Id' => array('name' => 'T003_D024_Id' , 'label' => 'Cliente', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGrid, 'length' => array(64,120)),
					'T003_D013_Id' => array('name' => 'T003_D013_Id', 'label' => 'Contato', 'type' => 'select', 'selectRefresh' => array('D013','D013_Id','D013_Nome_Contato,D013_DDD_Telefone_1,D013_Telefone_1,D013_E_Mail',"where D013_D024_Id=$idCliente AND D013_Flag_Ativo = 'S' ORDER BY D013_Nome_Contato",(isset($r_consumidor) && $r_consumidor) ? false : true),
						'select' => gGeraSelect('D013','D013_Id','D013_Nome_Contato,D013_DDD_Telefone_1,D013_Telefone_1,D013_E_Mail',"where D013_D024_Id=$idCliente AND D013_Flag_Ativo = 'S' ORDER BY D013_Nome_Contato",(isset($r_consumidor) && $r_consumidor) ? false : true)
					),

					'ven001FormContatos' => array('name' => 'ven001FormContatos', 'type'=>'submit', 'reload' => false, 'value' => 'Incluir', 'align' => 'right', 'reject' => true),
					'T003_D148_Id_Entrega' => array('name' => 'T003_D148_Id_Entrega', 'label' => 'Endereço Entrega', 'type' => 'select', 'selectRefresh' => array('D148','D148_Id','D148_Nome','WHERE D148_D024_Id='.$idCliente.' ORDER BY D148_Id',true),
						'select' => $arrayEndereco
					),
					'ven001FormEnderecos' => array('name' => 'ven001FormEnderecos', 'type'=>'submit', 'reload' => false, 'value' => 'Incluir', 'align' => 'right', 'reject' => true),
					'T003_D148_Id_Cobranca' => array('name' => 'T003_D148_Id_Cobranca', 'label' => 'Endereço Cobrança', 'type' => 'select', 'selectRefresh' => array('D148','D148_Id','D148_Nome','WHERE D148_D024_Id='.$idCliente.' ORDER BY D148_Id',true),
						'select' => $arrayEndereco
					),
					'T003A_D148_Id_Redespacho' => array('name' => 'T003A_D148_Id_Redespacho', 'label' => 'Endereço Redespacho', 'type' => 'select', 'selectRefresh' => array('D148','D148_Id','D148_Nome','WHERE D148_D024_Id='.$idCliente.' ORDER BY D148_Id',true),
						'select' => $arrayEndereco
					),
					'T003_T134_Id' => array('name' => 'T003_T134_Id','label'=>'Faixa para cálculo do Crédito<br /> de ICMS Simples Nacional','uppercase'=>true,'type' => 'select', 'select' =>
						gGeraSelect('T134', 'T134_Id','CONCAT(T134_Tipo[[virgula]]": DE "[[virgula]]convert(format(round(T134_Faixa_Inicial),2,"de_DE")using utf8)[[virgula]]" A "[[virgula]]convert(format(round(T134_Faixa_Final),2,"de_DE")using utf8)[[virgula]]" = ALIQ: "[[virgula]]T134_ICMS[[virgula]]"% REDUÇÃO: "[[virgula]]if(T134_Reducao_Base_ICMS,T134_Reducao_Base_ICMS,0)[[virgula]]"%") as faixaCalculo','',true,$faixaCalculo)
					),
					'T003_D116_Id' => array('name' => 'T003_D116_Id', 'label'=>'Veículo', 'type' => 'select',
						'select'=> gGeraSelect('D116', 'D116_Id', 'D116_Placa,D133_Modelo,D134_Cor', "LEFT JOIN D133 on D133_Id=D116_D133_Id LEFT JOIN D134 on D134_Id=D116_D134_Id WHERE D116_D024_Id=$idCliente", true),
						'selectRefresh' => array('D116', 'D116_Id', 'D116_Placa,D133_Modelo,D134_Cor', "LEFT JOIN D133 on D133_Id=D116_D133_Id LEFT JOIN D134 on D134_Id=D116_D134_Id WHERE D116_D024_Id=$idCliente", true)
					),
					'ven001FormVeiculos' => array('name' => 'ven001FormVeiculos', 'type'=>'submit', 'reload' => false, 'value' => 'Incluir', 'align' => 'right', 'reject' => true),
					'T003_D006_Id' => array('name' => 'T003_D006_Id', 'label'=>'CFOP','type' => 'select', 'restricao' => false, 'select' => 
						gGeraSelect('D006','D006_Id','D006_Codigo_CFOP,D006_Descricao',' WHERE IFNULL(D006_Flag_Inativo,"N")!="S" AND D006_Flag_Departamento="COMERCIAL"  ORDER BY D006_Codigo_CFOP',true, (isset($r_consumidor) && $r_consumidor) ? $g['C020']['C020_Cupom_Fiscal_CFOP'] : ''),
					),
					'T003_Flag_Revenda_Consumidor' => array('name' => 'T003_Flag_Revenda_Consumidor', 'label'=>'Tipo','type' => 'select',
						'select' => 
							array(
								array('title' => '...', 'value' => 0, 'selected' => true),
								array('title' => 'Revenda', 'value' => 1),
								array('title' => 'Consumidor atacado', 'value' => 2),
								array('title' => 'Consumidor varejo', 'value' => 3),
							)
					),
					'T003_Data_Emissao' => array('name' => 'T003_Data_Emissao', 'label'=>'Emissão','type' => 'data','value'=>date("d/m/Y"), 'length' => array(12,10)),
					'T003_C007_Id_Vendedor_Interno' => array('name' => 'T003_C007_Id_Vendedor_Interno', 'readonly' => $vendedorInternoRead, 'label'=>'Vendedor','type' => 'select', 'extra' => $vendedorInternoExtra, 'select' => 
						gGeraSelect('C007','C007_Id','IF(C007_Ativo != "N"[[virgula]] C007_Primeiro_Nome[[virgula]] CONCAT(C007_Primeiro_Nome[[virgula]]" (INATIVO)")) as C007_Primeiro_Nome','WHERE C007_Flag_Vendedor!="" AND (C007_Ativo != "N" OR C007_Id = "'.$idVendedorInterno.'") ORDER BY C007_Primeiro_Nome',true,$g['usuarioAtual']),
					),
					'T003_C007_Id_Vendedor_Externo' => array('name' => 'T003_C007_Id_Vendedor_Externo', 'readonly' => $vendedorExternoRead, 'label'=>'Externo','type' => 'select', 'extra' => $vendedorExternoExtra, 'select' => 
						gGeraSelect('C007','C007_Id','IF(C007_Ativo != "N"[[virgula]] C007_Primeiro_Nome[[virgula]] CONCAT(C007_Primeiro_Nome[[virgula]]" (INATIVO)")) as C007_Primeiro_Nome','WHERE C007_Flag_Vendedor!="" AND (C007_Ativo != "N" OR C007_Id = "'.$idVendedorExterno.'") ORDER BY C007_Primeiro_Nome',true),
					),
					'T003_D021_Id' => array('name' => 'T003_D021_Id', 'label'=>'Representante', 'type' => 'select', 'select' => 
						gGeraSelect('D021','D021_Id','D021_Nome_Empresa',"WHERE D021_Ativo = 'S'",true),
					),
					//'T003_Percentual_Comissao' => array('name' => 'T003_Percentual_Comissao', 'label'=>'Comissão %','value'=>'0,00','type' => 'moeda', 'length' => array(20,10)),
					'T003_Flag_Acp' => array('name' => 'T003_Flag_Acp', 'label'=>'Tipo Documento','type' => 'select', 'select' => 
						array(
							array('title' => '...', 'value' => 0),
							array('title' => 'NF', 'value' => 1),
							array('title' => 'Romaneio', 'value' => 2),
							array('title' => 'ACP', 'value' => 3),
							array('title' => 'Cupom Fiscal', 'value' => 4, 'selected' => (isset($r_consumidor) && $r_consumidor && $g['C031']['documentoPadraoPDV']=='1' ) ? true : false),
							array('title' => 'NF D1', 'value' => 5),
							array('title' => 'NFe', 'value' => 6, 'selected' => (isset($r_consumidor) && $r_consumidor) ? false : true),
							array('title' => 'NFSe', 'value' => 7),
							array('title' => 'NFCe', 'value' => 8, 'selected' => (isset($r_consumidor) && $r_consumidor && $g['C031']['documentoPadraoPDV']=='0') ? true : false),
						)
					),
					'T003_Flag_Lista_Preco' => 	array('name' => 'T003_Flag_Lista_Preco', 'label' => 'Lista Preço', 'type' => 'select', 'select' => 
						array(
							array('title' => 'Preço 1', 'value' => '1', 'selected' => $listaPreco[1]),
							array('title' => 'Preço 2', 'value' => '2', 'selected' => $listaPreco[2]),
							array('title' => 'Preço 3', 'value' => '3', 'selected' => $listaPreco[3]),
							array('title' => 'Preço 4', 'value' => '4', 'selected' => $listaPreco[4]),
							array('title' => 'Preço 5', 'value' => '5', 'selected' => $listaPreco[5]),
						)
					),
					'T003_Valor_Despesas_Acessorias' => array('name' => 'T003_Valor_Despesas_Acessorias', 'label'=>'Despesas acessórias', 'type' => 'moeda', 'value'=> '', 'casasDecimais' => '2', 'length' => array(12,10)),
					'T003_Forma_Pagamento' => array('name' => 'T003_Forma_Pagamento', 'label'=>'Forma de Pagamento', 'type' => 'text', 'length' => array(100,100)),
					'T003_Cliente_Area' => array('name' => 'T003_Cliente_Area', 'label'=>'Area', 'type' => 'text'),
					'T003_Frete' => array('name' => 'T003_Frete', 'label'=>'Frete','type' => 'texto', 'length' => array(100,100)),
					'T003_Valor_Frete' => array('name' => 'T003_Valor_Frete', 'label'=>'Valor Frete','type' => 'moeda', 'length' => array(100,100)),
					'T003_Prazo_Entrega' => array('name' => 'T003_Prazo_Entrega', 'label'=>'Prazo de entrega', 'type' => 'text', 'length' => array(100,100)),
					'T003_Validade_Proposta' => array('name' => 'T003_Validade_Proposta', 'label'=>'Validade proposta', 'type' => 'text', 'length' => array(100,100)),
					'T003_Observacao' => array('name' => 'T003_Observacao', 'label'=>'Observação orçamento', 'type' => 'textarea', 'length' => array(4,50)),
					'T003_C004_Id' => array('name' => 'T003_C004_Id', 'type' => 'hidden', 'value'=> "{$g['empresaAtual']}"),
					'T003_T139_Id' => array('name' => 'T003_T139_Id', 'label' => 'ID Email', 'type' => 'hidden', 'value'=> @$T139_Id),
					'T003_D085_Id' => array('name' => 'T003_D085_Id', 'label' => 'ID Email', 'type' => 'hidden', 'value'=> @$D085_Id),
					'T003_Percentual_Comissao' => array('name' => 'T003_Percentual_Comissao', 'label'=>'Percentual Comissão', 'type' => 'text', 'length' => array(40,50)),
					'T003_Nome_Cliente' => array('name' => 'T003_Nome_Cliente', 'label' => 'T003_Nome_Cliente', 'type' => 'hidden', 'length' => array(40,50)),
					'T003_Ordem_Compra' => array('name' => 'T003_Ordem_Compra', 'label' => 'Ordem compra', 'restricao' => false),
					'T003_D022_Id' => array('name' => 'T003_D022_Id', 'label' => 'Transportadora', 'type' => 'pesquisa', 'pesquisa' => array('D022', 'D022_Id', 'D022_Nome_Empresa,D020_Nome_Cidade,D018_UF', "LEFT JOIN D020 ON D020_Id = D022_D020_Id LEFT JOIN D018 ON D018_Id = D022_D018_Id LEFT JOIN D022A ON D022_Id = D022A_D022_Id WHERE IFNULL(D022A_Flag_Ativo, 'S') != 'N'"), 'extra' => "onchange=\"carregaTipoTransp(this, '{$g['divId']}')\"", 'length' => array(50,90)),
					'T003_D036_Id' => array('name' => 'T003_D036_Id', 'label' => 'Tipo', 'type' => 'select', 'select' =>
						gGeraSelect('D036','D036_Id','D036_Tipo_Transporte','',true),
					),
					'T003_Flag_Frete' => array('name' => 'T003_Flag_Frete','label'=>'Frete', 'type' => 'select',
						'select' => array(
							array('title' => '...', 'value' => ''),
							array('title' => '0 - Contratação por conta do Remetente', 'value' => '0'),
							array('title' => '1 - Contratação por conta do Destinatário', 'value' => '1'),
							array('title' => '2 - Contratação por conta de Terceiros', 'value' => '2'),
							array('title' => '3 - Próprio por conta do Remetente', 'value' => '3'),
							array('title' => '4 - Próprio por conta do Destinatário', 'value' => '4'),							
							array('title' => '9 - Sem frete', 'value' => '9', 'selected' => (isset($r_consumidor) && $r_consumidor) ? true : false),
						),
					),
					'T003_Flag_Destino_Produto' => array('name' => 'T003_Flag_Destino_Produto','label'=>'Destino', 'type' => 'select', 
						'select' => $destinoProdutos
					),
					'T003_Observacao_Comercial' => array('name' => 'T003_Observacao_Comercial', 'label'=>'Observação comercial', 'type' => 'textarea', 'length' => array(4,50)),
					'T003_C007_Id' => array('name' => 'T003_C007_Id', 'label' => 'Usuario', 'type' => 'hidden', 'value' => $g['usuarioAtual']),
					'btnOrcamentoIncluirCliente' => array('name' => 'btnOrcamentoIncluirCliente', 'type'=>'button', 'reload' => false, 'value' => 'Incluir', 'reject' => true),
					'T003_Percentual_Comissao_Representante' => array('name' => 'T003_Percentual_Comissao_Representante', 'label'=>'Percentual Comissão', 'type' => 'hidden', 'length' => array(40,50)),
					'T003_Percentual_Comissao_Externo' => array('name' => 'T003_Percentual_Comissao_Externo', 'label'=>'Percentual Comissão', 'type' => 'hidden', 'length' => array(40,50)),
					'T003_Percentual_Comissao_Usuario' => array('name' => 'T003_Percentual_Comissao_Usuario', 'label'=>'Percentual Comissão', 'type' => 'hidden', 'length' => array(40,50)),
					'T003A_Hora_Inclusao' => array('name' => 'T003A_Hora_Inclusao', 'label'=>'Hora inclusão','type' => 'hidden' , 'value' => date("H:i:s")),
					'T003A_Flag_Ratear_Frete_Custo' => array('name' => 'T003A_Flag_Ratear_Frete_Custo', 'label'=>'T003A_Flag_Ratear_Frete_Custo','type' => 'hidden' , 'value' => $g['C031']['ratearFreteVendaNoCusto']),
					'T003A_Flag_Conversao_Unidade' => array('name' => 'T003A_Flag_Conversao_Unidade', 'label'=>'T003A_Flag_Conversao_Unidade','type' => 'hidden' , 'value' => $g['C031']['utilizaUnidadeConversaoVenda']),
					'T003A_Data_Entrega' => array('name' => 'T003A_Data_Entrega', 'label'=>'Data Entrega','type' => 'data'),
					'atualizaDivIdMain' => array('name' => 'atualizaDivIdMain', 'label' => 'atualizaDivIdMain', 'type' => 'hidden', 'reject' => true, 'value' => 'N'),
				)
			)
		);
		// Botão Limpar / Enviar
		if(!empty($r_acaoId)){
			$formArray['campos']['orcamentos']['content_form1Orcamento_btn'] = array('name' => 'content_form1Orcamento_btn', 'type'=>'button', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		}
		if($g['C031']['modeloExpedicao'] == '1'){
			$formArray['campos']['orcamentos']['T003_Flag_Expedicao'] = array('name' => 'T003_Flag_Expedicao', 'type' => 'hidden', 'value' => (isset($r_consumidor) && $r_consumidor) ? 2 : 1);
		} else if($g['C031']['modeloExpedicao'] == '2'){
			$formArray['campos']['orcamentos']['T003_Flag_Expedicao'] = array('name' => 'T003_Flag_Expedicao', 'label' => 'Expedição', 'type' => 'select', 'select' => 
				array(
					array('title' => '...', 'value' => ''),
					array('title' => 'Comercial', 'value' => '1'),
					array('title' => 'Balcão', 'value' => '2'),
				)
			);
		}

		if($g['C031']['venderEstoqueMatriz'] == 'S'){
			$formArray['campos']['orcamentos']['T003_C004_Id_Estoque'] = array('name' => 'T003_C004_Id_Estoque', 'label' => 'Estoque', 'type' => 'select', 'select' => gGeraSelect('C004','C004_Id','C004_Nome_Abreviado, C004_Nome_Empresa',"where C004_Id='1' or C004_Id='{$g['empresaAtual']}'",true,$g['empresaAtual']));
		}
		$form->definirFormularioArray($formArray);

		echo <<<EOT
		    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		    	$('#ven001FormContatos').unbind('click');
				$('#ven001FormContatos').unbind('click').bind('click', function(data) {
					var id = $('#{$g['divId']} #T003_D024_Id').val();
					if (id>0){
						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Cliente/Fornecedor - ID: ' + id, '/cad/cad001/content/cad001contentCadastro/', '&acaoId=' + encodeURIComponent(id) + '&tabela=D024' + '&tab=contatos', [950,553]);											
					}else{
						dialogConfirm('Erro', 'Selecione o Cliente!');
					}
					return false;
				});
				$('#ven001FormEnderecos').unbind('click');
				$('#ven001FormEnderecos').unbind('click').bind('click', function(data) {
					var id = $('#{$g['divId']} #T003_D024_Id').val();
					if (id>0){
						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Cliente/Fornecedor - ID: ' + id, '/cad/cad001/content/cad001contentCadastro/', '&acaoId=' + encodeURIComponent(id) + '&tabela=D024' + '&tab=enderecos', [950,553]);											
					}else{
						dialogConfirm('Erro', 'Selecione o Cliente!');
					}
					return false;
				});
				$('#ven001FormVeiculos').unbind('click');
				$('#ven001FormVeiculos').unbind('click').bind('click', function(data) {
					var id = $('#{$g['divId']} #T003_D024_Id').val();
					if (id>0){
						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Incluir Veículo', '/cad/cad001/content/cad001ContentVeiculosIncluir/', '&naoRefreshParent=true&D024_Id=' + encodeURIComponent(id) + '&tabela=D024' + '&tab=veiculos', [300,350]);
					}else{
						dialogConfirm('Erro', 'Selecione o Cliente!');
					}
					return false;
				});
				$('#btnOrcamentoIncluirCliente').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Incluir Novo Cliente', '/cad/cad001/content/cad001OrcamentoIncluirCliente/', '', [590,420]);
				});

				function carregaTipoTransp(elem, divId) {
					setTimeout(function() {
						$.getJSON('/ven/ven001/form_func-ajax/carregaTipoTransp/?ajax=true&D022_Id=' + encodeURIComponent($('#T003_D022_Id').val()) + '&callback=?', function(request) {
							if (request.code) {
								$('#T003_D036_Id').val(request.data['T003_D036_Id']);
							} else {
								var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
								dialogConfirm("Erro!", request.data, \$buttons)
							}
						});
					}, 100);
				}

	            $('#{$g['divId']} #T003_Flag_Acp').change(function() {
	                if($(this).val() == 8){
	                	$('#{$g['divId']} #T003_D022_Id').val('');
	                	$('#{$g['divId']} #T003_Flag_Frete').val('9');
	                }
	            });
	            $('#{$g['divId']} #T003_Flag_Acp').trigger('change');
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
				// Insert ou Update?
				if (!empty($acaoId)) {
					// Update
					$FlagACP = $form->campoValorEnviado('T003_Flag_Acp');

					$T003_D022_Id = $form->campoValorEnviado('T003_D022_Id');
					// Atualização do campo "T003_Nome_Transportadora"
					if (!empty($T003_D022_Id)) {
						$D022_transp = mysql_query("SELECT D022_Nome_Empresa FROM D022 WHERE D022_Id = '{$T003_D022_Id}'");
	 					$rD022_transp = mysql_fetch_array($D022_transp);
	 					if(mysql_num_rows($D022_transp)){
							mysqli_query("UPDATE T003 SET T003_Nome_Transportadora = '{$rD022_transp['D022_Nome_Empresa']}' WHERE T003_Id = '$acaoId'");
	 					}
					}

					$T003 = mysql_query("SELECT T003_Flag_Lista_Preco,
												T003_Valor_Frete,
												T003A_Flag_Ratear_Frete_Custo,
												T003_Flag_Frete,
												T003_Flag_Destino_Produto 
										   FROM T003 
									  LEFT JOIN T003A ON T003_Id = T003A_T003_Id
										  WHERE T003_Id = '{$acaoId}'");

 					$rT003 = mysql_fetch_array($T003);
					if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
						if($g['C031']['modeloExpedicao'] == '1'){
							if($FlagACP == '4'){
								$T003_Flag_Expedicao = 2;
							} else {
								$T003_Flag_Expedicao = 1;
							}
   							mysql_query("update T003 left join D024 on D024_Id=T003_D024_Id set T003_Nome_Cliente = D024_Nome_Empresa, T003_D018_Id=D024_D018_Id, T003_Flag_Expedicao = '{$T003_Flag_Expedicao}' where T003_Id='$acaoId'");
						} else {
							mysql_query("update T003 left join D024 on D024_Id=T003_D024_Id set T003_Nome_Cliente = D024_Nome_Empresa, T003_D018_Id=D024_D018_Id where T003_Id='$acaoId'");
						}

						//Caso no cabeçalho seja colocado algum tipo de frete diferente de Emitente O ipv desse orçamento especifico recebe 0 
						$frete     			  = $form->campoValorEnviado('T003_Flag_Frete');
						$valorFrete 		  = gCorrigeNumeroInverte($form->campoValorEnviado('T003_Valor_Frete'));
						$atualizouPrecoTabela = false;
						if($frete!='0'){
							$T250 = mysql_query("Select T250_Id,T250_Aliquota,T250_Flag_Altera_Preco from T250 where T250_T003_Id='$acaoId' and T250_Tipo='1' and T250_Flag_Campo_Calculado='S'");
							$mT250=mysql_fetch_array($T250);
							$T250_Id=$mT250['T250_Id'];

							if($mT250['T250_Aliquota']>0){
								if($mT250['T250_Flag_Altera_Preco']=='S'){
									$msg = "<br> Foi deduzido ".$mT250['T250_Aliquota']."% no preço tabela desse orçamento.";
								}else{
									$msg = "<br> Foi deduzido ".$mT250['T250_Aliquota']."% do demonstrativo IPV.";
								}
								$atualizouPrecoTabela = true;
								mysql_query("Update T250 set T250_Aliquota=0 where T250_Id='{$T250_Id}'");
								$VEN001->atualizarPrecoTabela($acaoId);
							}

							if((($valorFrete != $rT003['T003_Valor_Frete']) || ($frete != $rT003['T003_Flag_Frete'])) && $rT003['T003A_Flag_Ratear_Frete_Custo'] == 'S' && !$atualizouPrecoTabela){
								$VEN001->atualizarPrecoTabela($acaoId);
							} 

						}else{
							//caso seja emitente o T250_Aliquota desse IPV desse orçamento recebe o Valor original do ipv(tabela T249_ALiquota)
							$T250 = mysql_query("Select T250_Id,T250_Aliquota,T250_Flag_Altera_Preco from T250 where T250_T003_Id='$acaoId' and T250_Tipo='1' and T250_Flag_Campo_Calculado='S'");
							$mT250=mysql_fetch_array($T250);
							$T250_Id=$mT250['T250_Id'];

							$T249 = mysql_query("select T249_Id,T249_Aliquota from T249 where T249_Tipo='1' and T249_Flag_Campo_Calculado='S' and T249_C004_Id='{$g['empresaAtual']}'");
							$mT249 = mysql_fetch_array($T249);

							if($mT250['T250_Aliquota']<=0 and $mT249['T249_Aliquota']>0){
								if($mT250['T250_Flag_Altera_Preco']=='S'){
									$msg = "<br> Foi acrescido ".$mT249['T249_Aliquota']."% no preço tabela desse orçamento.";
								}else{
									$msg = "<br> Foi acrescido ".$mT249['T249_Aliquota']."% ao demonstrativo IPV.";
								}
								$atualizouPrecoTabela = true;
								mysql_query("Update T250 set T250_Aliquota='{$mT249['T249_Aliquota']}' where T250_Id='{$T250_Id}'");
								$VEN001->atualizarPrecoTabela($acaoId);
							}

							if((($valorFrete != $rT003['T003_Valor_Frete']) || ($frete != $rT003['T003_Flag_Frete'])) && $rT003['T003A_Flag_Ratear_Frete_Custo'] == 'S' && !$atualizouPrecoTabela){
								$VEN001->atualizarPrecoTabela($acaoId);
							} 

						}
						//Atualiza as aliquotas dos produtos, caso o orçamento seja gerado pelo coletor
				        $sqlTotalizador = mysql_query("SELECT T004_Id FROM T004 WHERE T004_T003_Id='{$acaoId}'");

						// Personalizar de acordo com necessidade do cliente
						$VEN001->acoesFinaisAtualizarOrcamento($acaoId);

				        echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								T003_Id = {$acaoId};						
						 		idProgressBar = dialogAlert('Atualizando Alíquotas',"<span id='progressBar'>Processando 0%</span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' />");
			            		ven001AtualizarAliquotasLoop(idProgressBar,T003_Id,1);

	  					 		function ven001AtualizarAliquotasLoop(idProgressBar, T003_Id, inicio){
					                $.getJSON('/ven/ven001/form_func-ajax/atualizarAliquota/?ajax=true&T003_Id=' + T003_Id + '&extra=' + encodeURIComponent("{$extra_}") + '&inicio=' + inicio + '&callback=?', function(request) {
					                    if (request.concluido == 0) {
					                    	$('#progressBar').html('<b>Processado '+ request.percentual + '%</b>' + '<br/><br/>Produtos atualizados: <b>' + request.ok + '</b><br/>Produtos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
					                    	ven001AtualizarAliquotasLoop(idProgressBar,T003_Id,0);
					                    }else{
					                    	$('#dc-' + idProgressBar).dialog("destroy").remove();
					                    	dialogConfirm('Dados e Alíquotas atualizadas','<b>Processado '+ request.percentual + '%</b>' + '<br/><br/>Produtos atualizados: <b>' + request.ok + '</b><br/>Produtos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
					                    	if (request.code) {
												divRefresh('{$g['divId']}', true);
					                        } else {
					                            dialogConfirm('Erro!', request.data);
					                        }
					                    } 
					                });	                    
						      	}
							} </script></div>
EOT;
						// Imprime uma mensagem e fecha a janela
						//echo gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!'.$msg);

						//Verifica se a lista de preço foi alterada
						$lista = $form->campoValorEnviado('T003_Flag_Lista_Preco');
				        if($lista != $rT003['T003_Flag_Lista_Preco']){
				        	//Altera a Lista de Precos e o valor Original dos produtos
				        	$VEN001->alteraLista($acaoId, $lista);
				        	echo gGeraAlertMsg('Atenção', 'A lista de preço foi alterada, os valores de tabela dos produtos foram modificados.');
				        }
						mysql_query("call T003_Gravar_Totalizacao_4('$acaoId')");
						
						// Definido no form como N, se o cliente quiser atualizar pode personalizar e alterar o value para S
						$atualizaDivIdMain = $form->campoValorEnviado('atualizaDivIdMain');	
				        echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								if('{$atualizaDivIdMain}' == 'S'){	
									divRefresh('{$r_divIdMain}', true);
								}
								divRefresh($('.contentCabecalhoLayout').parent().attr('id'));
							} </script></div>
EOT;
					}
				} else {
					// Insert
					// primeiro verifica se foi setado o tipo do cliente
					$FlagACP = $form->campoValorEnviado('T003_Flag_Acp');
					$T003_T139_Id = $form->campoValorEnviado('T003_T139_Id');
					$T003_D085_Id = $form->campoValorEnviado('T003_D085_Id');
					$T003_D022_Id = $form->campoValorEnviado('T003_D022_Id');

					$result = mysql_query("SELECT D024_Id, 
												  D024_Flag_Contribuinte,
												  D024_Pgto_Primeiro_Vcto_Tipo,
												  D024_Cobrar_Frete_Primeira_Parcela,
												  D024_Cobrar_IPI_Primeira_Parcela,
												  D024_Prazo_Parcela,
												  D024_Pgto_Dias_Vcto_Tipo,
												  D024_Dias_Vencimento,
												  D024_Quantidade_Parcelas,
												  D024_D027_Id,
												  D024_Cobrar_ST_Primeira_Parcela
												   from D024 WHERE D024_Id='{$form->campoValorEnviado('T003_D024_Id')}'");
					$D024 = mysql_fetch_assoc($result);

					if (!empty($T003_T139_Id)) {
						$sqlT139 = mysql_query("SELECT *.C007 FROM T139 LEFT JOIN C007 ON C007_Id=T139_C007_Id WHERE T139_Id = '{$T003_T139_Id}'");
						$rowT139 = mysql_fetch_array($sqlT139);
						require_once('sistema/comunicacaoEmail.php');
						$mail = new comunicacaoEmail($rowT139['C007_Email_POP_Servidor'], $rowT139['C007_Email_SMTP_Usuario'], $rowT139['C007_Email_SMTP_Senha'], false, $T003_T139_Id, $rowT139['C007_Email_POP_Porta']);
						$mail->associarEmails($T003_T139_Id);
					}

					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						// Imprime uma mensagem e fecha a janela
						$acaoId  = $g['mysqlLastId'];
						$T003A   = mysql_query("select T003A_T003_Id from T003A where T003A_Id='{$acaoId}'");
						$mT003A  = mysql_fetch_array($T003A);
						$T003_Id = $mT003A['T003A_T003_Id'];
						$acaoId  = $T003_Id;
						
						if($g['C031']['habilitaEnsaioOrcamento'] == 'S'){
							$VEN001->incluirEnsaioOrcamento($acaoId);
						}
						
						if($g['C031']['modeloExpedicao'] == '1'){
							if($FlagACP == '4'){
								$T003_Flag_Expedicao = 2;
							} else {
								$T003_Flag_Expedicao = 1;
							}
   							mysql_query("update T003 left join D024 on D024_Id=T003_D024_Id set T003_Nome_Cliente = D024_Nome_Empresa, T003_D018_Id=D024_D018_Id, T003_Flag_Expedicao = '{$T003_Flag_Expedicao}' where T003_Id='$acaoId'");
						} else {
							mysql_query("update T003 left join D024 on D024_Id=T003_D024_Id set T003_Nome_Cliente = D024_Nome_Empresa, T003_D018_Id=D024_D018_Id where T003_Id='$acaoId'");
						}
						// Associa esse orçamento a um e-mail (caso ele foi gerado apartir de um)
						if (!empty($T003_T139_Id)) {
							$sql = "UPDATE T139 SET T139_T003_Id = '{$acaoId}' WHERE T139_Id = '{$T003_T139_Id}';";
							mysql_query($sql);
							$sql = "UPDATE T139 SET T139_D024_Id = '{$form->campoValorEnviado('T003_D024_Id')}' WHERE T139_Id = '{$T003_T139_Id}';";
							mysql_query($sql);
						}
						// Associa esse orçamento a agenda que originou
						if (!empty($T003_D085_Id)) {
							$sql = "UPDATE D085 SET D085_T003_Id = '{$acaoId}' WHERE D085_Id = '{$T003_D085_Id}';";
							mysql_query($sql);
						}
						// Casas decimais do preço
						if (isset($r_consumidor) && $r_consumidor) {
							mysql_query("update T003 set T003_Casas_Decimais_Preco = 2 where T003_Id='$acaoId'");
						} else {
							mysql_query("update T003 set T003_Casas_Decimais_Preco = '{$g['C004']['C004_Casas_Decimais_Preco']}' where T003_Id='$acaoId'");
						}

						// Atualização do campo "T003_Nome_Transportadora"
						if (!empty($T003_D022_Id)) {
							$D022_transp = mysql_query("SELECT D022_Nome_Empresa FROM D022 WHERE D022_Id = '{$T003_D022_Id}'");
		 					$rD022_transp = mysql_fetch_array($D022_transp);
		 					if(mysql_num_rows($D022_transp)){
								mysqli_query("UPDATE T003 SET T003_Nome_Transportadora = '{$rD022_transp['D022_Nome_Empresa']}' WHERE T003_Id = '$acaoId'");
		 					}
						}

						//VALIDAÇÕES PAGAMENTO
						$update = "";

						//Primeiro vencimento tipo (DIAS/DATA)
						if(!empty($D024['D024_Pgto_Primeiro_Vcto_Tipo'])){
							$primeiroVencTipo = $D024['D024_Pgto_Primeiro_Vcto_Tipo'];
						}else{
							$primeiroVencTipo = $g['C031']['primeiroVencimentoTipo'];
						}

						if(!empty($primeiroVencTipo)){
                            $update.=" T003_Pgto_Primeiro_Vcto_Tipo='{$primeiroVencTipo}' ";
                        }

                        //Primeiro vencimento número
                        if($D024['D024_Prazo_Parcela']>0){
							$primeiroVencNum = $D024['D024_Prazo_Parcela'];
						}else{
							$primeiroVencNum = $g['C031']['primeiroVencimentoNum'];
						}

						if(!empty($primeiroVencNum)){
                            if (!empty($update)){
                                $update .= ',';
                            }
                            $update.=" T003_Pgto_Primeiro_Vcto_Valor='{$primeiroVencNum}'"; 
                        }

                        //Dias venc tipo (DIA FIXO/INTERVALO)
                        if(!empty($D024['D024_Pgto_Dias_Vcto_Tipo'])){
							$tipoIntervalodias = $D024['D024_Pgto_Dias_Vcto_Tipo'];
						}else{
							$tipoIntervalodias = $g['C031']['tipoIntervalodias'];
						}

						if(!empty($tipoIntervalodias)){
                            if (!empty($update)){
                                $update .= ',';
                            }
                            $update.=" T003_Pgto_Dias_Vcto_Tipo='{$tipoIntervalodias}'"; 
                        }

                        //Dias venc num (DIA FIXO/INTERVALO)
                        if($D024['D024_Dias_Vencimento']>0){
							$IntervalorDias = $D024['D024_Dias_Vencimento'];
						}else{
							$IntervalorDias = $g['C031']['IntervalorDias'];
						}

						if(!empty($IntervalorDias)){
                            if (!empty($update)){
                                $update .= ',';
                            }
                            $update.=" T003_Pgto_Dias_Vcto_Valor='{$IntervalorDias}'"; 
                        }

                        //Portador
                        if(!empty($D024['D024_D027_Id'])){
							$D027portadorEmpresa = $D024['D024_D027_Id'];
						}else{
							$D027portadorEmpresa = $g['C031']['D027portadorEmpresa'];
						}

						if(!empty($D027portadorEmpresa)){
                            if (!empty($update)){
                                $update .= ',';
                            }
                            $update.=" T003_D027_Id='{$D027portadorEmpresa}'"; 
                        }

                        //Frete primeira parcela
                        if(!empty($D024['D024_Cobrar_Frete_Primeira_Parcela'])){
							$usarFretePrimeiraParcela = $D024['D024_Cobrar_Frete_Primeira_Parcela'];
						}else{
							$usarFretePrimeiraParcela = $g['C031']['usarFretePrimeiraParcela'];
						}

						if(!empty($usarFretePrimeiraParcela)){
                            if (!empty($update)){
                                $update .= ',';
                            }
                            $update.=" T003_Flag_Frete_Primeira_Parcela='{$usarFretePrimeiraParcela}'"; 
                        }

                        //ST primeira parcela
                        if(!empty($D024['D024_Cobrar_ST_Primeira_Parcela'])){
							$usarSTPrimeiraParcela = $D024['D024_Cobrar_ST_Primeira_Parcela'];
						}else{
							$usarSTPrimeiraParcela = $g['C031']['usarSTPrimeiraParcela'];
						}

						if(!empty($usarSTPrimeiraParcela)){
                            if (!empty($update)){
                                $update .= ',';
                            }
                            $update.=" T003_Flag_ST_Primeira_Parcela='{$usarSTPrimeiraParcela}'"; 
                        }

                        //IPI primeira parcela
                        if(!empty($D024['D024_Cobrar_IPI_Primeira_Parcela'])){
							$usarIPIPrimeiraParcela = $D024['D024_Cobrar_IPI_Primeira_Parcela'];
						}else{
							$usarIPIPrimeiraParcela = $g['C031']['usarIPIPrimeiraParcela'];
						}

						if(!empty($usarIPIPrimeiraParcela)){
                            if (!empty($update)){
                                $update .= ',';
                            }
                            $update.=" T003_Flag_IPI_Primeira_Parcela='{$usarIPIPrimeiraParcela}'"; 
                        }

                        //Parcelas padrão (vem selecionado no formulario de pagamento)
                        if($D024['D024_Quantidade_Parcelas']>0){
							$numeroParcelasPadrao = $D024['D024_Quantidade_Parcelas'];
						}else{
							$numeroParcelasPadrao = $g['C031']['numeroParcelasPadrao'];
						}

						if(!empty($numeroParcelasPadrao)){
                            if (!empty($update)){
                                $update .= ',';
                            }
                            $update.=" T003_Pgto_Parcelas='{$numeroParcelasPadrao}'"; 
                        }


						if(!empty($update)){
                            mysql_query("UPDATE T003 SET {$update} WHERE T003_Id='{$acaoId}'"); 
                            $sucesso++;
                        }

						/**
						 * Só entrará nesse if caso a inclusão do orçamento esteja sendo feita pela tab de "catálogos" no CRM
						 */
						if($r_tabCatalogoCRM == true){
							$sqlT198 = "SELECT * FROM T198 WHERE T198_C007_Id = '{$g['usuarioAtual']}'";
							$resultT198 = mysql_query($sqlT198);
							$erros = '';
							while($linhaT198 = mysql_fetch_array($resultT198)){
								$retorno = $VEN001->incluirProduto(false, $linhaT198['T198_D009_Id'], $acaoId);
								if ($retorno !== true) {
									$erros .= "{$linhaT198['T198_D009_Id']}<br/>";
								}
							}
							if(empty($erros)){
								mysql_query("DELETE FROM T198 WHERE T198_C007_Id = '{$g['usuarioAtual']}'");
								//$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
								echo <<<EOT
									<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
										// Fecha a janela, recarrega o grid e abre a alteração
										$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
											setTimeout(function() {
												abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}', unique(), '', 'Editar Orçamento - ID: ' + '{$acaoId}', '/ven/ven001/content/ven001ContentEditarOrcamento/', '&acaoId=' + encodeURIComponent('{$acaoId}'), [1000,610]);
												{$alert}
											}, 150);
			  							});
										$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
										divRefresh('{$g['divIdParent']}', true);
									} </script></div>
EOT;
							}else{
								$alert .= gGeraAlertMsg('Erro!', "Erro ao incluir os seguintes produtos:<br/><br/>{$erros}", false);
								echo <<<EOT
									<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
										{$alert}
									} </script></div>
EOT;
							}
						} else {
							//Abrir tela de leitura da agenda
							if (!empty($T003_D085_Id)) {
								$eventoAposInsert = <<<EOT
									var dataJanelaAnexo = $('#{$g['divIdParent']}').data('janelaVars');
									$('#{$g['divIdParent']}').closest('.ui-dialog-content').dialog('close');
									abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Leitura de Agenda', '/etc/etc017/content/etc017ContentOrcamentoAgenda/', dataJanelaAnexo + '&acaoId=' + encodeURIComponent('{$T003_D085_Id}') + '&T003_Id=' + encodeURIComponent('{$acaoId}'), [900,600]);
EOT;
							//Abrir tela de leitura do e-mail ou do anexo do e-mail
							} else if (!empty($T003_T139_Id)) {
								$eventoAposInsert = <<<EOT
									var dataJanelaAnexo = $('#{$g['divIdParent']}').data('janelaVars');
									$('#{$g['divIdParent']}').closest('.ui-dialog-content').dialog('close');
									setTimeout(function(){
										abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Leitura de Anexo', '/etc/etc006/content/etc006ContentLeituraAnexo/', dataJanelaAnexo + '&tela=orcamento&T003_Id=' + encodeURIComponent('{$acaoId}'), [900,600]);
									}, 50);
EOT;
							//Abrir orçamento normal
							} else {
								$eventoAposInsert = "abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}', unique(), '', 'Editar Orçamento - ID: ' + '{$acaoId}', '/ven/ven001/content/ven001ContentEditarOrcamento/', '&acaoId=' + encodeURIComponent('{$acaoId}'), [1000,610]);";
							}
							echo <<<EOT
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									// Fecha a janela, recarrega o grid e abre a alteração
									$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
										setTimeout(function() {
											{$eventoAposInsert}
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
			} else {
				// Neste caso, se for um insert e vier de uma agenda carrega alguns dados
				if (!empty($r_D085_Id)) {
					$r_D085_Id = mysql_real_escape_string($r_D085_Id);
					$sql = "SELECT D085_D024_Id FROM D085 WHERE D085_Id = '{$r_D085_Id}';";
					$resultado = mysql_query($sql);
					$row = mysql_fetch_assoc($resultado);
					$empresa = '';
					if (!empty($row['D085_D024_Id'])) {
						$sql = "SELECT D024_Id, D024_Nome_Empresa FROM D024 WHERE D024_Id = '{$row['D085_D024_Id']}';";
						$resultado = mysql_query($sql);
						$row = mysql_fetch_assoc($resultado);
						$empresa .= <<<EOT
							$('#{$g['divId']} #T003_D024_Id').val('{$row['D024_Id']}');
							$('#{$g['divId']} #T003_D024_Id_Pesquisa').val('{$row['D024_Nome_Empresa']}');
EOT;
					}
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							{$empresa}
						} </script></div>
EOT;
				// Neste caso, se for um insert e vier de um e-mail carrega alguns dados
				} else if (!empty($T139_Id)) {
					$T139_Id = mysql_real_escape_string($T139_Id);
					$sql = "SELECT T139_D024_Id FROM T139 WHERE T139_Id = '{$T139_Id}';";
					$resultado = mysql_query($sql);
					$row = mysql_fetch_assoc($resultado);
					$empresa = '';
					if (!empty($row['T139_D024_Id'])) {
						$sql = "SELECT D024_Id, D024_Nome_Empresa FROM D024 WHERE D024_Id = '{$row['T139_D024_Id']}';";
						$resultado = mysql_query($sql);
						$row = mysql_fetch_assoc($resultado);
						$empresa .= <<<EOT
							$('#{$g['divId']} #T003_D024_Id').val('{$row['D024_Id']}');
							$('#{$g['divId']} #T003_D024_Id_Pesquisa').val('{$row['D024_Nome_Empresa']}');
EOT;
					}
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							{$empresa}
						} </script></div>
EOT;
				//Caso o orçamento seja para consumidor, alguns dados ja vem preenchidos
				} else if (isset($r_consumidor) && $r_consumidor) {
					// Campo Cliente
					$sql = "SELECT D024_Id, D024_Nome_Empresa FROM D024 WHERE D024_Id = '{$idCliente}';";
					$resultado = mysql_query($sql);
					$row = mysql_fetch_assoc($resultado);
					$js .= <<<EOT
						$('#{$g['divId']} #T003_D024_Id').val('{$row['D024_Id']}');
						$('#{$g['divId']} #T003_D024_Id_Pesquisa').val('{$row['D024_Nome_Empresa']}');
EOT;
					// Campo Transportadora
					$sql = "SELECT D022_Nome_Empresa FROM D022 WHERE D022_Id = '{$g['C020']['C020_Cupom_Fiscal_Transportadora']}';";
					$resultado = mysql_query($sql);
					$row = mysql_fetch_assoc($resultado);
					$js .= <<<EOT
						$('#{$g['divId']} #T003_D022_Id').val('{$g['C020']['C020_Cupom_Fiscal_Transportadora']}');
						$('#{$g['divId']} #T003_D022_Id_Pesquisa').val('{$row['D022_Nome_Empresa']}');
EOT;
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							{$js}
						} </script></div>
EOT;
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
		if(empty($r_acaoId)){
			//$botoesEsq .= '<button id="etc006PreCadastrarCliente">Pré-Cadastrar Cliente</button>';
			$botoesDir .= '<button type="submit" id="content_form1Orcamento_btn">Incluir</button>';
			$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		}
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
		
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
			$('#etc006PreCadastrarCliente').unbind('click').bind('click', function(data) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Pré-Cadastro de Cliente', '/cad/cad001/content/cad001ContentPreCadastrarCliente/', '&acaoId=' + encodeURIComponent('{$r_acaoId}') + '&tabela=T139', [800,550]);
			});
		} </script></div>
EOT;

	break;
	
	/**
	* Form Principal
	* Chamado em: ven001tab02
	*/
	case 'ven001FormProdutos':
		$g['smarty']->assign('navSql', "T003_C004_Id = '{$g['empresaAtual']}'");
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']}').data('sqlPesquisa', '[T003_Id, T003_Data_Emissao as Data, D024_Nome_Empresa, D024_Nome_Fantasia]');
				$('#{$g['divId']}').data('labelPesquisa', '[Pedido, Emissão, Razão Social, Nome Fantasia]');
				$('#{$g['divId']}').data('sqlExtra', '[LEFT JOIN D024 ON T003_D024_Id = D024_Id]');
			} </script></div>
EOT;
		gHabilitaNavegacao($g['divId'], 'VEN001', 'ven001FormProdutos');		

		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$T003=mysql_query("SELECT T003_D024_Id, T003_Casas_Decimais_Preco, T003_C004_Id_Estoque, T003_Flag_Lista_Preco FROM T003 WHERE T003_Id='{$r_T003_Id}'");
		$mT003=mysql_fetch_array($T003);

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
		$form_tabela = 'T004';
		$form_campo_pk = 'T004_Id';
		$form_parent = 'T003_Id';
		$form_parent_valor = $r_T003_Id;
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = array('ven001FormProdutosBtnAdd', 'ven001FormProdutosBtnCln', 'ven001FormProdutosBtnNew');
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/ven/ven001/form/ven001FormProdutos/';
        
        //form editor
		if ($g['C031']['utilizaQuantidadeConversao'] === 'S') {
			$camposForm = ',"T004_Valor_Preco_Sem_Desconto_Unitario":{"top":75.98583984375,"left":13.58892822265625,"width":71.52578,"hidden":0},"T004_Percentual_Desconto":{"top":75.98583984375,"left":171.30154418945312,"width":23.52578,"hidden":0},"T004_Flag_Destino_Produto":{"top":144.99356079101562,"left":29.768035888671875,"width":399,"hidden":0},"ambiente":{"top":0,"left":0,"width":360,"hidden":1},"D001_Peso_Unitario_Bruto":{"top":0,"left":0,"width":137,"hidden":1},"T004A_Quantidade_Conversao":{"top":52.983245849609375,"left":268.4149475097656,"width":119.52578,"hidden":0},"T004_Numero_Pedido_Compra":{"top":98.9884033203125,"left":305.2577209472656,"width":71.52578,"hidden":0},"T004_Observacao_Entrega":{"top":75.98583984375,"left":285.7925109863281,"width":119.52578,"hidden":0}';
		} else {
			$camposForm = ',"T004_Numero_Pedido_Compra":{"top":98.98837280273438,"left":289.2525329589844,"width":87.52578,"hidden":0},"T004_Valor_Preco_Sem_Desconto_Unitario":{"top":53,"left":269.5940856933594,"width":71.52578,"hidden":0},"T004_Percentual_Desconto":{"top":53,"left":427.3066711425781,"width":23.52578,"hidden":0},"T004_Flag_Destino_Produto":{"top":76,"left":29.76803588867188,"width":144,"hidden":0},"ambiente":{"top":145,"left":27.583770751953125,"width":391.52578,"hidden":0},"D001_Peso_Unitario_Bruto":{"top":0,"left":0,"width":137,"hidden":1}';
		}

        $form_editor = array(
            'produto' => '{"T004A_Data_Entrega":{"top":0,"left":0,"width":71,"hidden":1},"Procura_Produto":{"top":6.997406005859375,"left":35.721649169921875,"width":121.52578,"hidden":0},"Marca":{"top":6.997406005859375,"left":292.1713562011719,"width":135.52578,"hidden":0},"precoTabela":{"top":6.997406005859375,"left":500.2577209472656,"width":87.52578,"hidden":0},"T004_Descricao_Produto":{"top":30,"left":18.22808837890625,"width":391.52578,"hidden":0},"PrecoMinimo":{"top":30,"left":495.6571960449219,"width":87.52578,"hidden":0},"T004_Quantidade":{"top":52.983245849609375,"left":10.940704345703125,"width":71.52578,"hidden":0},"T004_D037_Id":{"top":52.983245849609375,"left":159.99356079101562,"width":63,"hidden":0},"estoqueLiquido":{"top":52.983245849609375,"left":515.6829528808594,"width":87.52578,"hidden":0},"infoCustoVenda":{"top":55.998687744140625,"left":449.9999694824219,"width":null,"hidden":0},"T004_Observacao_Entrega":{"top":75.98580932617188,"left":269.7873840332031,"width":135.52578,"hidden":0},"estoqueLoja":{"top":75.98580932617188,"left":512.6288146972656,"width":87.52578,"hidden":0},"T004_Codigo_Substituto":{"top":98.98837280273438,"left":4.65850830078125,"width":135.52578,"hidden":0},"T004_Item_Pedido_Compra":{"top":98.98837280273438,"left":431.9845275878906,"width":39.52578,"hidden":0},"estoqueTotal":{"top":98.98837280273438,"left":509.0914611816406,"width":88.52578,"hidden":0},"D001_Qte_Embalagem":{"top":0,"left":0,"width":136.5,"hidden":1},"T004_Observacao":{"top":121.990966796875,"left":16.2757568359375,"width":391.52578,"hidden":0},"ordemCompra":{"top":121.990966796875,"left":539.8646545410156,"width":87.52578,"hidden":0},"T004_Aliquota_IPI":{"top":0,"left":0,"width":40,"hidden":1},"D001_Peso_Unitario_Bruto":{"top":0,"left":0,"width":137,"hidden":1},"estoqueFora":{"top":144.9935302734375,"left":511.7010192871094,"width":87.52578,"hidden":0},"T004A_T066_Id":{"top":167.99612426757812,"left":32.648193359375,"width":399,"hidden":0},"ven001BtnPreCadastrarProduto":{"top":170,"left":520,"width":null,"hidden":0},"ven001BtnCadastrarProduto":{"top":0,"left":0,"width":null,"hidden":1},"T004_D006_Id":{"top":0,"left":0,"width":375.47422,"hidden":1},"UltimoPrecoVenda":{"top":190.99868774414062,"left":30.19329833984375,"height":38.52578,"width":391.52578,"hidden":0},"ven001FormProdutosBtnCln":{"top":195,"left":520,"width":null,"hidden":0},"ven001FormProdutosBtnAdd":{"top":195,"left":600,"width":null,"hidden":0}'. $camposForm .'}',
        );
		// Campo: Procura_Produto
		$T003_Id = isset($r_ven001FormProdutosForm) ? $r_ven001FormProdutosForm['T004_T003_Id'] : $r_T003_Id;

		$C004_Id_Estoque = $g['empresaAtual'];
		if($g['C031']['venderEstoqueMatriz'] == 'S'){
			$C004_Id_Estoque = (isset($mT003['T003_C004_Id_Estoque']) && $mT003['T003_C004_Id_Estoque'] > 0) ? $mT003['T003_C004_Id_Estoque'] : $g['empresaAtual'];
		}

		$filtro = '';
		if($g['C031']['SeparaProdutoPorEmpresa']=='S'){
				$filtro = "AND IF(D001_C004_Id > 0, D001_C004_Id=".$g['empresaAtual'].", (1=1))";
		}else{
				$filtro = '';
		}

        $incluirLote = false;
        if($g['C031']['habilitarInclusaoProdutosPesquisaGrid']=='S'){
            $incluirLote = true;
        }

		$listaCampos = array(																											              // parametros para cada campo do grid
			array('chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaAlinhar' => 'center', 'style' => 'width:1%', 'colunaExibir' => false),
			array('colunaAlinhar' => 'center', 'style' => 'width:1%', 'colunaExibir' => false),
			array('colunaTipo' => 'livre', 'callback' => 'mostrarImagemProdutoPequena', 'colunaExibir' => false, 'style' => 'width:0px'),				
			array('colunaAlinhar' => 'center', 'style' => 'width:1%;font-weight:bold'),
			array('colunaAlinhar' => 'left', 'style' => 'width:1%;font-weight:bold'),
			array('colunaAlinhar' => 'center', 'style' => 'width:1%'),
			array('style' => 'font-weight:bold;white-space:normal;'),
			array('style' => 'font-weight:bold;white-space:normal;', 'colunaExibir' => false),
			array('colunaAlinhar' => 'left', 'style' => 'width:1%'),
			array('style' => 'width:1%'),
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
			array('callback' => 'estoqueFora', 'style' => 'width: 1%;', 'colunaAlinhar' => 'right', 'callbackParameter' => array($r_T003_Id)),
			array('callback' => 'abrirJanela', 'callbackParameter' => array('Estoque Similares', '/cad/cad002/content/cad002ContentEstoqueSimilares/', 'D001_Id', '850,400', true), 'style' => 'width: 1%;', 'colunaAlinhar' => 'right'),
			array('colunaTipo' => 'livre', 'callback' => 'retornaPrecoVenda', 'colunaAlinhar' => 'right', 'style' => 'font-weight:bold;width:1%', 'callbackParameter' => array($r_T003_Id, $mT003['T003_Flag_Lista_Preco'])),
			array('style' => 'width: 1%;', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => 3, 'colunaEditar' => true, 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T198_Quantidade', 'colunaEditarInsert' => false, 'colunaExibir' => $incluirLote),
			array('callback' => 'infoCustoVendaProduto', 'colunaTipo' => 'livre', 'style' => 'width:1%'),
			array('colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('Ordens de Compra', '/ven/ven001/content/ven001contentProdutoOC/', 'D009_Id', '700,400', true), 'style' => 'font-weight:normal;width:1%'),
			array('style' => 'width:1%'),
			array('style' => 'width:1%'),
			array('style' => 'font-weight:bold;white-space:normal;', 'colunaExibir' => true),
			array('colunaExibir' => false),
			array('colunaExibir' => false),
			array('colunaExibir' => false),
			array('colunaExibir' => false),
			array('colunaExibir' => false),
			array('colunaExibir' => false),
			array('chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaExibir' => false),
			array('colunaExibir' => false),
			array('colunaExibir' => false),
			array('colunaAlinhar' => 'right', 'callback' => 'abrirJanela', 'callbackParameter' => array('Tabelas Fornecedor', '/cad/cad002/tab/cad002TabTabelaFornVenda/', 'D009_Id', '700,400', true), 'style' => 'font-weight:normal;width:1%', 'colunaExibir'=>false)
		);

		$qtdeConversaoDesc  = '';
		$qtdeConversaoCampo = '';
		if ($g['C031']['utilizaQuantidadeConversao'] === 'S') {
			$qtdeConversaoDesc  = ',Qtde Conversão';
			$qtdeConversaoCampo = '||T198_Quantidade_Conversao';

			array_push($listaCampos, array('style' => 'width: 1%;', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => 3, 'colunaEditar' => true, 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T198_Quantidade', 'colunaEditarInsert' => true, 'colunaExibir' => $incluirLote));
		}

		$pesquisaGridExtra = <<<EOT
			left join D049 on D049_D001_Id=D001_Id  
			left join D041 on D041_D049_Id=D049_Id  
			left join D009 on D009_D049_Id=D049_Id  
			left join D082 on D082_Id=D049_D082_Id  
			left join D037 on D037_Id=D001_D037_Id   
			left join D060 on D060_Id=D001_D060_Id
			left join D003 on D003_Id=D001_D003_Id
			LEFT JOIN C004 ON C004_Id=D001_C004_Id
			left join D002 on D002_Id=D001_D002_Id
			left join C008 on C008_Id=D001_C008_Id
			left join D005 on D005_Id=D001_D005_Id
			left join T066 on T066_D009_Id=D009_Id
			left join D004 on D004_Id=T066_D004_Id
			LEFT JOIN D083 on D083_D001_Id=D001_Id AND D083_D024_Id='{$mT003['T003_D024_Id']}'
			LEFT JOIN D144 ON D144_D049_Id=D049_Id  
			left join C007 on C007_Id=D009_C007_Id_Comprador
			LEFT JOIN D001A ON D001_Id=D001A_D001_Id
			LEFT JOIN T198 on T198_D009_Id=D009_Id AND T198_D024_Id='{$mT003['T003_D024_Id']}' AND T198_C007_Id='{$g['usuarioAtual']}'
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
			'D009_Id,D049_Flag_Tipo,D144_Id',																										      // chave primaria
			'D009_Id||C008_Id||D060_Id||foto||D001_Codigo_Produto||D049_Modelo||D083_Codigo_Produto_Fornecedor||D001_Descricao_Produto||D001_Descricao_Ingles||D001_Aplicacao||substr(D082_Marca,1,15) as D082_Marca||D037_Unidade||D049_Flag_Tipo||D005_Classificacao_Fiscal||D009_Preco_1||D009_Preco_2||D009_Preco_3||D009_Quantidade_Estoque||D009_Quantidade_Estoque_Liquido as Pedido||(select sum(T066_Quantidade_Estoque) from T066 left join D004 on D004_Id=T066_D004_Id  left join D146 ON D146_Id = D004_D146_Id where T066_D009_Id=D009_Id and UPPER(D146_Descricao)="LOJA") as Estoque||T066_Quantidade_Estoque||D009_Quantidade_Estoque_Fora||D009_Quantidade_Estoque_Similar||"" as retornaPrecoVenda||T198_Quantidade||infoCustoVendaProduto||D009_Quantidade_OC||D004_Local||substr(C007_Primeiro_Nome,1,10) as C007_Primeiro_Nome||D001_Observacao_Compra||D001_Especificacoes||D049_Id||D001_Flag_Pre_Cadastro||D144_Id||D060_Descricao_Grupo||"' . $T003_Id . '" as T003_Id||D001_Id||D009_Data_Alteracao_Custo||D009_Data_Ultima_Venda||D009_Flag_Custo_Utilizado||D009_Quantidade_Estoque_Tabela(D009_Id)'.$qtdeConversaoCampo,	// campos
			$pesquisaGridExtra,																								              // extras sql
			'Pesquisa de Produto',																										  // título da janela
			'Id,Dep,Setor,Foto,Código,Sub-item,Cód.Cli,Descrição,Descrição Inglês,Referência,Marca,UN,Tipo,NCM,Preço,Preço 2,Preço 3,Físico,Líquido,Loja,Locação,Fora,Similar,Preço,Quantidade,,OC,Locação,Comprador,Observações Venda,Especificações,D049_Id,D001_Flag_Pre_Cadastro,D144_Id,Setor,T003_Id,D001_Id,D009_Data_Alteracao_Custo,D009_Data_Ultima_Venda,D009_Flag_Custo_Utilizado,Est.Tab'.$qtdeConversaoDesc,	  // descrição dos campos
			'ven001CampoProcuraProduto',																					              // callback js
			$g['divId'],																									              // divId
			array($T003_Id, $mT003['T003_D024_Id'], $g['C031']['habilitarInclusaoSubItensMontagem']),	  // parametro extra
			$listaCampos,
			'D009_Id||D049_Id||foto||D009_Preco_1||D009_Preco_2||D009_Preco_3||D009_Quantidade_Estoque||D009_Quantidade_Estoque_Liquido||infoCustoVendaProduto||D144_Id||C008_Id||D060_Id||D037_Unidade||D009_Quantidade_Estoque_Real||T066_Quantidade_Estoque||D009_Quantidade_Estoque_Fora||D009_Quantidade_OC||D144_Id||D001_Id||D009_Flag_Custo_Utilizado||T198_Id', // Campos que são ignorados na cláusula where do pesquisaGrid
			null,
			null,
			null,
			null,
			null,
			'D001_Descricao_Produto,Descricao,texto||D001_Codigo_Produto,Codigo,texto||D009_Quantidade_Estoque_Liquido,Estoque,numero', // Paramentro para montar os filtros
		);
		
		//Buscas destinos, foi centralizado nesta classe e vai valer para todos os locais
		$destinoProdutos   = $CAD002->retornaDestinoProdutos(false, 'V');
		$readOnlyDescricao = ($g['C031']['editarDescricaoProdutoVenda'] == 'S') ? false : true;
		$readOnlyUnidade   = ($g['C031']['editarUnidadeProdutoVenda'] == 'S') ? false : true;
		$extraUnidade      = ($g['C031']['editarUnidadeProdutoVenda'] == 'S') ? "" : "onfocus='this.initialSelect = this.selectedIndex;' onchange='this.selectedIndex = this.initialSelect;'";

		$sqlVeiculo = mysql_query("select T003_D024_Id,T003_D116_Id from T003 where T003_Id='{$r_T003_Id}'");
		$resultVeiculo = mysql_fetch_array($sqlVeiculo);
		// novo formulário
		$form = new Formulario('ven001FormProdutosForm');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)

		$formArray = array(
			'campos' => array(
				'produto' => array(
					// Campos Hidden
					'T004_T003_Id' => array('name' => 'T004_T003_Id', 'label' => '', 'type' => 'hidden', 'value' => @$r_T003_Id),
					'T004_C004_Id' => array('name' => 'T004_C004_Id', 'label' => '', 'type' => 'hidden', 'value' => $g['empresaAtual']),
					'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true),
					'T004_D009_Id' => array('name' => 'T004_D009_Id', 'label' => 'T004_D009_Id', 'type' => 'hidden'),
					'D001_Id' => array('name' => 'D001_Id', 'label' => 'D001_Id', 'type' => 'hidden', 'reject'=>true),
					'T004A_Altura' => array('name' => 'T004A_Altura', 'type' => 'hidden', 'reject' => true, 'value' => '0.00000'),
					'T004A_Largura' => array('name' => 'T004A_Largura', 'type' => 'hidden', 'reject' => true, 'value' => '0.00000'),
					'T004A_Comprimento' => array('name' => 'T004A_Comprimento', 'type' => 'hidden', 'reject' => true, 'value' => '0.00000'),
					'T004A_Resultado' => array('name' => 'T004A_Resultado', 'type' => 'hidden', 'reject' => true, 'value' => '0.00000'),
					// Campos normais
					'Procura_Produto' => array('name' => 'Procura_Produto' , 'label' => 'Produto', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGrid, 'length' => array(64,120), 'reject' => true, 'extra' => 'placeholder="BUSCAR"'),
					'Marca' => array('name' => 'Marca', 'label' => 'Marca', 'type' => 'text', 'length' => array(20,255), 'reject' => true, 'readonly'=>true),
					'T004_Codigo_Substituto' => array('name' => 'T004_Codigo_Substituto', 'label' => 'Código cliente', 'type' => 'text', 'length' => array(20, 120)),
					'T004_Descricao_Produto' => array('name' => 'T004_Descricao_Produto', 'label' => 'Descrição', 'type' => 'text', 'readonly' => $readOnlyDescricao, 'length' => array(64, 120)),
					'T004_Quantidade' => array('name' => 'T004_Quantidade', 'label' => 'Quantidade', 'type' => 'moeda', 'length' => array(20, 30), 'extra' => "onchange=\"gatilhoPrecoOrcamentoPorQtd(this, \$('#T004_Quantidade').val(),\$('#T004_T003_Id').val(), \$('#T004_D009_Id').val(), \$('#estoqueLiquido').val(), '{$g['divId']}');\""),
					'T004_D037_Id' => array('name' => 'T004_D037_Id', 'label' => '', 'readonly' => $readOnlyUnidade, 'extra' => $extraUnidade, 'type' => 'select', 'select' => 
					    gGeraSelect('D037', 'D037_Id', 'D037_Unidade', '', true)
					),
					'T004_Valor_Preco_Sem_Desconto_Unitario' => array('name' => 'T004_Valor_Preco_Sem_Desconto_Unitario', 'label' => 'Preço Unit.', 'type' => 'moeda', 'length' => array(20, 30), 'casasDecimais' => $mT003['T003_Casas_Decimais_Preco']),
					'PrecoMinimo' => array('name' => 'PrecoMinimo', 'type' => 'moeda', 'length' => array(20, 30), 'reject' => true, 'readonly' => true, 'style' => 'text-align: right;', 'label' => 'Prc. Mínimo'),
					'T004_Numero_Pedido_Compra' => array('name' => 'T004_Numero_Pedido_Compra', 'label' => 'OC/Item', 'type' => 'text', 'length' => array(20, 60)),
					'T004_Item_Pedido_Compra' => array('name' => 'T004_Item_Pedido_Compra', 'type' => 'text', 'length' => array(20, 30)),
					'T004_Percentual_Desconto' => array('name' => 'T004_Percentual_Desconto', 'label' => '%D', 'type' => 'moeda', 'length' => array(15, 30), 'reject'=>true),
					'T004_D005_Id' => array('name' => 'T004_D005_Id', 'label' => 'NCM', 'type' => 'hidden', 'readonly' => true, 'restricao' => false, 'extra'=>'style="width:95px;"', 'extra' => "onfocus='this.initialSelect = this.selectedIndex;' onchange='this.selectedIndex = this.initialSelect;'", 'select' => 
						gGeraSelect('D149', 'D005_Id', 'D005_Classificacao_Fiscal, D005_Flag_Aplicacao, D149_Aliquota_IPI', "LEFT JOIN D005 ON D149_D005_Id=D005_Id AND D149_C004_Id='{$g['empresaAtual']}' WHERE D005_Flag_Ativo='S' ORDER BY D005_Classificacao_Fiscal, D005_Observacao", true)
					),
					'T004_Aliquota_IPI' => array('name' => 'T004_Aliquota_IPI', 'label' => 'IPI', 'type' => 'moeda', 'length' => array(5,20)),
					//'T004_Valor_Custo_Unitario' => array('name' => 'T004_Valor_Custo_Unitario', 'label' => 'Custo Unitário', 'type' => 'moeda', 'align' => 'right', 'style' => 'float:right;', 'length' => array(20, 30)),
					//'T004_Numero_Pedido_Compra' => array('name' => 'T004_Numero_Pedido_Compra', 'label' => 'Número OC', 'type' => 'text', 'length' => array(20, 120)),
					//'T004_Item_Pedido_Compra' => array('name' => 'T004_Item_Pedido_Compra', 'label' => 'Item OC', 'type' => 'text', 'length' => array(20, 120), 'align' => 'right', 'style' => 'float:right;'),
					'T004_Observacao' => array('name' => 'T004_Observacao', 'label' => 'Observação', 'type' => 'text', 'length' => array(64, 120)),
					'T004_Observacao_Entrega' => array('name' => 'T004_Observacao_Entrega', 'label' => 'Obs. Entrega', 'type' => 'text'),
					'ambiente' => array('name' => "ambiente_{$r_T003_Id}", 'label' => 'Ambiente', 'type' => 'text', 'length' => array(30, 60), 'reject' => true, 'extra' => 'onblur="T004_Quantidade_Focus();"'),
					'T004_Codigo_Local' => array('name' => 'T004_Codigo_Local', 'label' => 'Ambiente', 'type' => 'text', 'type' => 'hidden', 'value' => 'teste'),
					//Este campo não existe no banco, é apenas para inserir um serviço na T147
					'idVeiculoOrcamento' => array('name' => 'idVeiculoOrcamento', 'label' => 'Veículo', 'type' => 'select','reject' => true, 'extra'=>'style="width:190px;"',
						'select'=> gGeraSelect('D116', 'D116_Id', 'D116_Placa,D133_Modelo,D134_Cor', 'LEFT JOIN D133 on D133_Id=D116_D133_Id LEFT JOIN D134 on D134_Id=D116_D134_Id WHERE D116_D024_Id=(SELECT T003_D024_Id FROM T003 WHERE T003_Id='.@$r_T003_Id.')', true,@$resultVeiculo['T003_D116_Id']),
						'selectRefresh' => array('D116', 'D116_Id', 'D116_Placa,D133_Modelo,D134_Cor', ' LEFT JOIN D133 on D133_Id=D116_D133_Id LEFT JOIN D134 on D134_Id=D116_D134_Id WHERE D116_D024_Id=(SELECT T003_D024_Id FROM T003 WHERE T003_Id='.@$r_T003_Id.')', true,@$resultVeiculo['T003_D116_Id'])
					),
					'ven001FormVeiculosProdutoOrcamentoBtnNew' => array('name' => 'ven001FormVeiculosProdutoOrcamentoBtnNew', 'type'=>'submit', 'reload' => false, 'value' => 'Veículos', 'reject' => true),

					'UltimoPrecoVenda' => array('name' => 'UltimoPrecoVenda', 'label' => "<a href=\"#\" tabindex=\"-1\" onclick=\"abrirJanela(event, divIdRootAberto(), gridPegarDivId(\$(this)), unique(), 'Histórico', '.: Histórico :.', '/ven/ven001/content/ven001ContentHistoricoProduto/', '&acaoId=' + encodeURIComponent(\$('#{$g['divId']} .itemForm-T004_D009_Id').val()), [900,650]); return false;\">Histórico</a>", 'type' => 'textarea', 'reject'=>true, 'readonly'=>true, 'uppercase' => false),
					//'NcmProduto' => array('name' => 'NcmProduto', 'label' => 'NcmProduto','reject' => 'true', 'type' => 'hidden'),
					'T004_Codigo_Produto' => array('name' => 'T004_Codigo_Produto', 'label' => '', 'type' => 'hidden'),
					'T004_D006_Id' => array('name' => 'T004_D006_Id', 'label' => 'CFOP', 'type' => 'select', 'readonly'=>true, 'extra' => "onfocus='this.initialSelect = this.selectedIndex;' onchange='this.selectedIndex = this.initialSelect;'",
					    'select' => gGeraSelect('D006','D006_Id','D006_Codigo_CFOP,D006_Descricao',' WHERE IFNULL(D006_Flag_Inativo,"N")!="S" AND D006_Flag_Departamento="COMERCIAL" ORDER BY D006_Codigo_CFOP DESC', true),
					),
					'T004_Flag_Destino_Produto' => array('name' => 'T004_Flag_Destino_Produto','label'=>'Destino', 'type' => 'select', 
						'select' => $destinoProdutos
					),
					'estoqueLiquido' => array('name' => 'estoqueLiquido', 'label' => "<a href=\"#\" tabindex=\"-1\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Pedidos', '/crm/crm001/content/crm001ContentPedidosProduto/', '&acaoId=' + encodeURIComponent(\$('#{$g['divId']} .itemForm-T004_D009_Id').val()), [700,400]); return false;\">Est. Liq.</a>", 'type' => 'moeda', 'length' => array(5,20), 'reject' => true, 'readonly'=>true, 'style' => 'float:right; text-align:right;'),
					'estoqueLoja' => array('name' => 'estoqueLoja', 'label' => 'Est. Loja', 'type' => 'moeda', 'length' => array(5,20), 'reject' => true, 'readonly'=>true, 'style' => 'float:right; text-align:right;'),
					'estoqueTotal' => array('name' => 'estoqueTotal', 'label' => 'Est. Total', 'type' => 'moeda', 'length' => array(5,20), 'reject' => true, 'readonly'=>true, 'style' => 'float:right; text-align:right;'),
					'ordemCompra' => array('name' => 'ordemCompra', 'label' => "<a href=\"#\" tabindex=\"-1\" onclick=\"abrirJanela(event, divIdRootAberto(), gridPegarDivId(\$(this)), unique(), 'Ordem de Compra', '.: Ordem de Compra :.', '/ven/ven001/content/ven001contentProdutoOC/', '&acaoId=' + encodeURIComponent(\$('#{$g['divId']} .itemForm-T004_D009_Id').val()), [700,400]); return false;\">OC</a>", 'type' => 'moeda', 'length' => array(5,20), 'style' => 'float:right; text-align:right;', 'reject' => true, 'readonly'=>true),
					'estoqueFora' => array('name' => 'estoqueFora', 'label' => "<a href=\"#\" tabindex=\"-1\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Estoque Fora', '/cad/cad002/content/cad002ContentEstoqueExterno/', '&acaoId=' + encodeURIComponent(\$('#{$g['divId']} .itemForm-D001_Id').val()) + '&T003_Id=' + encodeURIComponent('{$r_T003_Id}'), [700,400]); return false;\">Est. Fora</a>", 'type' => 'moeda', 'length' => array(5,20), 'reject' => true, 'readonly'=>true, 'style' => 'float:right; text-align:right;'),
					'precoTabela' => array('name' => 'precoTabela', 'label' => 'Prc. Tabela', 'type' => 'moeda', 'length' => array(5,20), 'reject' => true, 'readonly'=>true, 'style' => 'float:right; text-align:right;'),
					'infoCustoVenda' => array('name' => 'infoCustoVenda', 'label' => '', 'type' => 'titulo', 'length' => array(5,20), 'reject' => true, 'value' => ''),
					'enviado' => array('name' => 'enviado', 'label' => '', 'type' => 'hidden', 'length' => array(5,20), 'reject' => true, 'value' => ''),
					'locacao' => array('name' => 'locacao', 'label' => '', 'type' => 'hidden', 'reject' => true, 'value' => ''),
					'T004A_T066_Id' => array('name' => 'T004A_T066_Id', 'label' => 'Locação', 'type' => 'select', 'align' => 'left', 'select' => 
						gGeraSelect('D004', 'D004_Id', 'D004_Local', 'where false', true)
					),
					'T004A_Data_Entrega' => array('name' => 'T004A_Data_Entrega', 'label' => 'Data Entrega', 'type' => 'data'),
					'D001_Peso_Unitario_Bruto' => array('name' => 'D001_Peso_Unitario_Bruto', 'label' => 'Peso Bruto', 'type' => 'moeda', 'length' => array(20, 30), 'readonly' => true),
					'D001_Qte_Embalagem' => array('name' => 'D001_Qte_Embalagem', 'label' => 'Qtde. Embalagem', 'type' => 'moeda', 'length' => array(20, 30), 'readonly' => true),
				),
			)
		);
		// Botão Limpar / Novo Registro
		if (!empty($acaoId)) {
			 $formArray['campos']['produto']['ven001FormProdutosBtnNew'] = array('name' => 'ven001FormProdutosBtnNew', 'type'=>'submit', 'reload' => false, 'value' => 'Novo Registro', 'reject' => true);
		}

		if(!isset($g['c029Ids'][187])){
			$incluirProduto = true;
			$incluirProdutoStyle = 'color:#ada8a8';
		}

		if ($g['C031']['utilizaQuantidadeConversao'] === 'S') {
			$formArray['campos']['produto']['usaConversao']               = array('name' => 'usaConversao', 'label' => 'usaConversao', 'type' => 'hidden', 'reject' => true);
			$formArray['campos']['produto']['T004A_Quantidade_Conversao'] = array('name' => 'T004A_Quantidade_Conversao', 'label' => 'Qtde Conversão', 'type' => 'moeda', 'length' => array(20, 30), 'readonly '=> true, 'extra' => "onchange=\"calculaQuantidadeConversao(this, '{$g['divId']}');\"");
			$formArray['campos']['produto']['D001_Peso_Unitario_Kg']      = array('name' => 'D001_Peso_Unitario_Kg', 'label' => 'D001_Peso_Unitario_Kg', 'type' => 'hidden', 'reject' => true);
			$formArray['campos']['produto']['T004_Quantidade']['extra']   = "onchange=\"calculaQuantidade(this, '{$g['divId']}');\"";
		}

		$formArray['campos']['produto']['ven001FormProdutosBtnAdd'] = array('name' => 'ven001FormProdutosBtnAdd', 'type'=>'button', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'reject' => true, 'disabled');
		$formArray['campos']['produto']['ven001FormProdutosBtnCln'] = array('name' => 'ven001FormProdutosBtnCln', 'type'=>'button', 'reload' => false, 'value' => 'Limpar', 'reject' => true, 'extra' => "tabindex=-1");
		$formArray['campos']['produto']['ven001BtnPreCadastrarProduto'] = array('name' => 'ven001BtnPreCadastrarProduto', 'type'=>'button', 'reload' => false, 'value' => 'Pré-Cadastrar Produto', 'reject' => true, 'extra' => 'tabindex=-1', 'disabled' => "{$incluirProduto}", 'style' => "$incluirProdutoStyle");	
		$formArray['campos']['produto']['ven001BtnCadastrarProduto'] = array('name' => 'ven001BtnCadastrarProduto', 'type'=>'button', 'reload' => false, 'value' => 'Cadastrar Produto', 'reject' => true, 'exibir' => false, 'extra' => 'tabindex=-1');
		// C031 = N: a calculadora não participa da navegação por Tab em nenhuma tela.
		$calculadoraQtdNavegaTab = (!isset($g['C031']['calculadoraQtdNavegaTab']) || $g['C031']['calculadoraQtdNavegaTab'] !== 'N') ? 'true' : 'false';
		$form->definirFormularioArray($formArray);
		
		echo <<<EOT
		    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				var formCalculadoraQuantidadeProduto = $('#{$g['divId']}');
				$(document).unbind('calculadoraQuantidade:resultado.ven001Produto').bind('calculadoraQuantidade:resultado.ven001Produto', function(_, medidas, formOrigemId) {
					if (formOrigemId !== '{$g['divId']}') { return; }
					formCalculadoraQuantidadeProduto.find('#T004A_Altura').val(medidas.altura);
					formCalculadoraQuantidadeProduto.find('#T004A_Largura').val(medidas.largura);
					formCalculadoraQuantidadeProduto.find('#T004A_Comprimento').val(medidas.comprimento);
					formCalculadoraQuantidadeProduto.find('#T004A_Resultado').val(medidas.resultado);
					formCalculadoraQuantidadeProduto.find('#T004A_Quantidade_Comercial').val(medidas.resultado.replace('.', ',')).trigger('change');
					formCalculadoraQuantidadeProduto.find('#T004_Quantidade').val(medidas.resultado.replace('.', ',')).trigger('change');
				});
				$.getScript('/hardness3/static/js/calculadoraQuantidade.js').done(function() {
					configurarCalculadoraQuantidade({participarTab: {$calculadoraQtdNavegaTab}});
					inicializarCalculadoraQuantidade({formulario: formCalculadoraQuantidadeProduto, formId: '{$g['divId']}', campo: '#T004_D037_Id', antesDe: '#T004_D037_Id_refreshBtn', altura: '#T004A_Altura', largura: '#T004A_Largura', comprimento: '#T004A_Comprimento', resultado: '#T004A_Resultado', divIdRoot: '{$g['divIdRoot']}', divIdParent: '{$g['divId']}'});
				});
				$('#ven001BtnPreCadastrarProduto').unbind('click');
				$('#ven001BtnPreCadastrarProduto').unbind('click').bind('click', function(data) {
					//var id = $('#{$g['divId']} #T003_D024_Id').val();
					if('{$r_telaEmail}' != ''){
						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Leitura de Anexo', '/etc/etc006/content/etc006ContentLeituraAnexo/', '&tab=geral&acaoId=' + encodeURIComponent("{$r_T139_Id}") + '&file=' + encodeURIComponent('{$r_file}') + '&T003_Id={$T003_Id}&C007_Id=' + encodeURIComponent("{$r_C007_Id}") + '&tela=preCadastroProduto&divForm={$g['divId']}', [900,600]);
					} else {
						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Pré-cadastrar Produto', '/cad/cad002/content/cad002ContentPreCadastro/', '&T003_Id={$T003_Id}&divForm={$g['divId']}', [600,430]);
					}
					return false;
				});
		    	$('#ven001BtnCadastrarProduto').unbind('click');
				$('#ven001BtnCadastrarProduto').unbind('click').bind('click', function(data) {
					//var id = $('#{$g['divId']} #T003_D024_Id').val();
					if('{$r_telaEmail}' != ''){
						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Leitura de Anexo', '/etc/etc006/content/etc006ContentLeituraAnexo/', '&tab=geral&acaoId=' + encodeURIComponent("{$r_T139_Id}") + '&file=' + encodeURIComponent('{$r_file}') + '&T003_Id={$T003_Id}&C007_Id=' + encodeURIComponent("{$r_C007_Id}") + '&tela=cadastroProduto', [1000,600]);
					} else {
						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastrar Produto', '/cad/cad002/content/form2/', '&T003_Id={$T003_Id}&divForm={$g['divId']}', [1000,635]);
					}
					return false;
				});
				$('#ven001FormVeiculosProdutoOrcamentoBtnNew').unbind('click');
				$('#ven001FormVeiculosProdutoOrcamentoBtnNew').unbind('click').bind('click', function(data) {
					var id = '{$resultVeiculo['T003_D024_Id']}';
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Incluir Veículo', '/cad/cad001/content/cad001ContentVeiculosIncluir/', '&naoRefreshParent=true&D024_Id=' + encodeURIComponent(id) + '&tabela=D024' + '&tab=veiculos', [300,350]);
					return false;
				});
		    } </script></div>
EOT;
	
		/**
		 * PARTE 3: Processamento do formulário (se foi efnviado)
		 * 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		 */

		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			$D009_Id = $form->campoValorEnviado('T004_D009_Id');
			$T004_Quantidade = $form->campoValorEnviado('T004_Quantidade');
			
			if($D009_Id != '' && ($g['C031']['informarEstoqueInsuficiente'] == 'S' || $g['C031']['obrigatorioLocacaoItemComValidade'] == 'S') && $form->campoValorEnviado('enviado') == '') {
				$T066_Id = $form->campoValorEnviado('T004A_T066_Id');
				echo <<<EOT
				    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						$.getJSON('/ven/ven001/form_func-ajax/ven001VerificarLocacaoOrcamento/?ajax=true&D009_Id=' + encodeURIComponent("{$D009_Id}") + '&T066_Id=' + encodeURIComponent('{$T066_Id}') + '&callback=?', function(request) {
							if(request.code){
								$.getJSON('/ven/ven001/form_func-ajax/ven001VerificarEstoqueOrcamento/?ajax=true&D009_Id=' + encodeURIComponent("{$D009_Id}") + '&T004_Quantidade=' + encodeURIComponent('{$T004_Quantidade}') + '&T066_Id=' + encodeURIComponent('{$T066_Id}') + '&callback=?', function(request) {
									if(request.code){
										$('#{$g['divId']} #enviado').val('true');
										$('#{$g['divId']} #locacao').val({$T066_Id});
										$('#ven001FormProdutosBtnAdd').trigger('click');
									} else {
										var buttons = {
											"Continuar": function() {
												$('#{$g['divId']} #enviado').val('true');
												$('#{$g['divId']} #locacao').val({$T066_Id});
												$('#ven001FormProdutosBtnAdd').trigger('click');
												$(this).dialog("destroy").remove();
											},
											"Cancelar": function() { $(this).dialog("destroy").remove(); } 
										};
										dialogConfirm('Atenção', request.data, buttons);
										campoProcuraLocacao("{$D009_Id}", "{$g['divId']}");
									}
								});
							} else {
								var buttons = {
									"Fechar": function() { $(this).dialog("destroy").remove(); } 
								};
								dialogConfirm('Atenção', request.data, buttons);
								campoProcuraLocacao("{$D009_Id}", "{$g['divId']}");
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
					$T004_Codigo_Produto = $form->campoValorEnviado('T004_Codigo_Produto');
	                $T004Codigo = mysql_query("SELECT T004_Codigo_Produto FROM T004 WHERE T004_Codigo_Produto='{$T004_Codigo_Produto}' AND T004_T003_Id='{$r_T003_Id}'");
	                if($g['C031']['permitirProdutosDuplicadosVenda'] == 'N' && mysql_num_rows($T004Codigo) > 0){
	                	echo gGeraAlertMsg('Erro', "Já existe o item com o código ".$T004_Codigo_Produto." neste orçamento. Por favor, selecione outro produto.");
	                } else {
						$T004_Id = false;
						$T004_Codigo_Local = $form->campoValorEnviado('ambiente');
						$form->campoSetarValor('T004_Codigo_Local', $T004_Codigo_Local, true);

						// Pega a locação
						$T004A_T066_Id = $form->campoValorEnviado('locacao');
						if($T004A_T066_Id > 0){
							$form->campoSetarValor('T004A_T066_Id', $T004A_T066_Id, true);
						}

						$retorno = $VEN001->incluirProduto($form);
						if ($retorno !== true) {
							echo gGeraAlertMsg('Erro', $retorno);
						} else {
							//echo gGeraAlertMsg('Operação OK', "Com sucesso");
							echo <<<EOT
									<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
										var T004_Codigo_Local = '{$T004_Codigo_Local}';
										divRefresh('{$form->campoValorEnviado('divReload')}', true, false, false, '{$T004_Id}', 'T004_Id'); // grid
										divRefresh('{$r_divOutro}', true, false, 2); // totais
										divRefresh('{$g['divId']}', true, false, 2);
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

	case 'ven001FormServicos':
		
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$r_T003_Id = isset($r_ven001FormServicosForm) ? $r_ven001FormServicosForm['T145_T003_Id'] : $r_T003_Id;
		
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
		$form_tabela = 'T145';
		$form_campo_pk = 'T145_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = array('ven001FormServicosBtnAdd', 'ven001FormServicosBtnCln', 'ven001FormServicosBtnNew');
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/ven/ven001/form/ven001FormServicos/';
        
        //form editor
        $form_editor = array(
            'servicos' => '{"Procura_Servico":{"top":7,"left":53,"width":264,"hidden":0},"T145_Titulo":{"top":30,"left":58,"width":280,"hidden":0},"T145_Descricao":{"top":53,"left":34,"width":280,"hidden":0},"T145_Valor_Unitario":{"top":122,"left":33,"width":100,"hidden":0},"T145_Tempo_Estimado":{"top":145,"left":6,"width":50,"hidden":0},"ven001FormVeiculosBtnNew":{"top":166,"left":400,"width":null,"hidden":0},"T145_D116_Id":{"top":168,"left":48,"width":280,"hidden":0},"ven001FormServicosBtnCln":{"top":189,"left":224,"width":null,"hidden":0},"ven001FormServicosBtnAdd":{"top":189,"left":320,"width":null,"hidden":0}}',
        );
		
		// Campo: Procura_Servico
		$pesquisaGridExtra = <<<EOT
EOT;
		$pesquisaGrid = array(
			'D112',																								// tabela
			'D112_Id',																							// chave primaria
			'D112_Id,D112_Titulo,D112_Descricao',																// campos
			$pesquisaGridExtra,																					// extras sql
			'Pesquisa de Serviço',																				// título da janela
			'Código,Título,Descrição',																			// descrição dos campos
			'campoProcuraServico',																				// callback js
			$g['divId']																						// divId
		);
        $sqlVeiculo = mysql_query("select T003_D024_Id,T003_D116_Id from T003 where T003_Id='{$r_T003_Id}'");
		$resultVeiculo = mysql_fetch_array($sqlVeiculo);
		// novo formulário
		$form = new Formulario('ven001FormServicosForm');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'servicos' => array(
				'Procura_Servico' => array('name' => 'Procura_Servico' , 'label' => 'Serviço', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGrid, 'length' => array(50,30), 'reject' => true),
					'T145_Titulo' => array('name' => 'T145_Titulo','label'=>'Título','type' => 'text', 'length' => array(50,30)),
					'T145_Descricao' => array('name' => 'T145_Descricao','label'=>'Descrição','type' => 'textarea', 'length' => array(3,46)),
					'T145_Valor_Unitario' => array('name' => 'T145_Valor_Unitario','label'=>'Valor Total','type' => 'moeda', 'length' => array(15,10)),
					'T145_Tempo_Estimado' => array('name' => 'T145_Tempo_Estimado','label'=>'Tempo Estimado','type' => 'tempo', 'length' => array(5,6)),
					'T145_D110_Id' => array('name' => 'T145_D110_Id', 'label'=>'Competência', 'type' => 'pesquisa', 'readonly'=>true, 'extra'=>'style="width:270px;"', 'length' => array(30,30), 'pesquisa'=>array('D110', 'D110_Id', 'D110_Titulo'),'type'=>'hidden'),
					//'T145_Hora_Inicio' => array('name' => 'T145_Hora_Inicio','label'=>'Hora Início','type' => 'text', 'length' => array(10,10)),
					//'T145_Hora_Fim' => array('name' => 'T145_Hora_Fim','label'=>'Hora Fim','type' => 'text', 'length' => array(10,10)),
					'T145_D116_Id' => array('name' => 'T145_D116_Id', 'label'=>'Veículo', 'extra'=>'style="width:280px;"', 'type' => 'select',
						'select'=> gGeraSelect('D116', 'D116_Id', 'D116_Placa,D133_Modelo,D134_Cor', ' LEFT JOIN D133 on D133_Id=D116_D133_Id LEFT JOIN D134 on D134_Id=D116_D134_Id WHERE D116_D024_Id=(SELECT T003_D024_Id FROM T003 WHERE T003_Id='.@$r_T003_Id.')', true, @$resultVeiculo['T003_D116_Id']),
						'selectRefresh' => array('D116', 'D116_Id', 'D116_Placa,D133_Modelo,D134_Cor', ' LEFT JOIN D133 on D133_Id=D116_D133_Id LEFT JOIN D134 on D134_Id=D116_D134_Id WHERE D116_D024_Id=(SELECT T003_D024_Id FROM T003 WHERE T003_Id='.@$r_T003_Id.')', true, @$resultVeiculo['T003_D116_Id'])
					),
					'ven001FormVeiculosBtnNew' => array('name' => 'ven001FormVeiculosBtnNew', 'type'=>'submit', 'reload' => false, 'value' => 'Veículos', 'align' => 'right', 'style' => 'float:right;', 'reject' => true),
					'T145_Quantidade' => array('name' => 'T145_Quantidade','label'=>'Quantidade','type' => 'hidden', 'length' => array(10,8), 'value'=>'1'),
					//'T145_Valor_Unitario' => array('name' => 'T145_Valor_Unitario', 'label' => 'Valor Unitário', 'type' => 'hidden', 'length' => array(15,10)),
					'T145_Quantidade_Confirmada' => array('name' => 'T145_Quantidade_Confirmada','label'=>'Quantidade','type' => 'hidden', 'length' => array(10,8), 'value'=>'1'),
					'T145_T003_Id' => array('name' => 'T145_T003_Id', 'type' => 'hidden', 'value' => @$r_T003_Id, 'length' => array(50,30)),
					'T145_D112_Id' => array('name' => 'T145_D112_Id', 'type' => 'hidden', 'label'=>'Serviço', 'value'=>'', 'length' => array(50,30)),
					'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true),
				)
			)
		);
		// Botão Limpar / Novo Registro
		if (!empty($acaoId)) {
			 $formArray['campos']['servicos']['ven001FormServicosBtnNew'] = array('name' => 'ven001FormServicosBtnNew', 'type'=>'submit', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		}
		$formArray['campos']['servicos']['ven001FormServicosBtnCln'] = array('name' => 'ven001FormServicosBtnCln', 'type'=>'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['servicos']['ven001FormServicosBtnAdd'] = array('name' => 'ven001FormServicosBtnAdd', 'type'=>'button', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$form->definirFormularioArray($formArray);

		$sql = "SELECT T003_D024_Id FROM T003 WHERE T003_Id = '{$r_T003_Id}';";
		$resultado = mysql_query($sql);
		$row = mysql_fetch_array($resultado);
		echo <<<EOT
		    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		    	$('#ven001FormVeiculosBtnNew').unbind('click');
				$('#ven001FormVeiculosBtnNew').unbind('click').bind('click', function(data) {
					var id = '{$row['T003_D024_Id']}';
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Incluir Veículo', '/cad/cad001/content/cad001ContentVeiculosIncluir/', '&naoRefreshParent=true&D024_Id=' + encodeURIComponent(id) + '&tabela=D024' + '&tab=veiculos', [300,350]);
					return false;
				});
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
						$retorno = $VEN001->insereServico($form, $g['mysqlLastId']);
						$erro = ($retorno === true) ? '' : "alert('$retorno');";
						$alert = gGeraAlertMsg('Operação OK', 'Registro Incluído com Sucesso!<br /><br /><b>ATENÇÃO!</b><br />Antes de gerar o pedido confirme a quantidade so serviço (Qtd Confirmada)', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								{$erro}
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

	break;
	
	case 'ven001FormTrocas':
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
		$form_tabela = 'T138';
		$form_campo_pk = 'T138_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = array('ven001FormTrocasBtnAdd', 'ven001FormTrocasBtnCln', 'ven001FormTrocasBtnNew');
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/ven/ven001/form/ven001FormTrocas/';
        
        //form editor
        $form_editor = array(
            'trocas' => '{"Procura_Produto_Troca":{"top":7,"left":37,"width":328,"hidden":0},"Descricao_Produto":{"top":30,"left":25,"width":345,"hidden":0},"ven001FormTrocasBtnAdd":{"top":143,"left":368,"width":null,"hidden":0},"Marca_Produto":{"top":53,"left":44,"width":125,"hidden":0},"ven001FormTrocasBtnCln":{"top":143,"left":288,"width":null,"hidden":0},"T138_Quantidade":{"top":76,"left":19,"width":125,"hidden":0},"ven001BtnCadastrarProdutoTroca":{"top":143,"left":144,"width":null,"hidden":0},"T138_Operacao":{"top":99,"left":250,"width":124,"hidden":0},"T138_Valor_Preco_Unitario":{"top":99,"left":49,"width":125,"hidden":0},"T138_Observacao":{"top":122,"left":14,"width":345,"hidden":0}}',
        );
		
		// Campo: Procura_Produto
		$pesquisaGridExtra = <<<EOT
			left join D049 on D049_D001_Id=D001_Id
			left join D009 on D009_D049_Id=D049_Id
			left join D082 on D082_Id=D049_D082_Id
			left join D037 on D037_Id=D001_D037_Id
			where (D009_C004_Id = '{$g['empresaAtual']}'
			and D001_Flag_Ativo!='N')
			AND NOT EXISTS (
				SELECT 1
				FROM D009A D009A_Visibilidade
				WHERE D009A_Visibilidade.D009A_D009_Id=D009_Id
				  AND D009A_Visibilidade.D009A_Flag_Pertence_Empresa='N'
			)
			group by D009_Id
			order by D001_Codigo_Produto
EOT;
		$pesquisaGrid = array(
			'D001',																								// tabela
			'D009_Id',																							// chave primaria
			'D001_Codigo_Produto,D001_Descricao_Produto,D082_Marca,D009_Quantidade_Estoque_Liquido',			// campos
			$pesquisaGridExtra,																					// extras sql
			'Pesquisa de Produto',																				// título da janela
			'Código,Descrição,Marca,Estoque',																	// descrição dos campos
			'ven001CampoProcuraProdutoTroca',																	// callback js
			$g['divId'],																						// divId
			(isset($r_ven001FormProdutosForm) ? $r_ven001FormProdutosForm['T138_T003_Id'] : $r_T003_Id),		// parametro extra
			array(																								// parametros para cada campo do grid
				array('colunaAlinhar' => 'center'),
				array(),
				array(),
				array('callback' => 'gCorrigeNumero', 'colunaAlinhar' => 'right')
			)
		);
		// novo formulário
		$form = new Formulario('ven001FormTrocasForm');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'trocas' => array(
					'Procura_Produto_Troca' => array('name' => 'Procura_Produto_Troca' , 'label' => 'Produto', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGrid, 'length' => array(64,120), 'reject' => true),
					'Descricao_Produto' => array('name' => 'Descricao_Produto', 'label' => 'Descrição', 'reject'=>true, 'readonly'=>true, 'type' => 'text', 'length' => array(64, 120)),
					'Marca_Produto' => array('name' => 'Marca_Produto', 'label' => 'Marca', 'type' => 'text', 'length' => array(20,255),'readonly'=>true, 'reject' => true),									
					'T138_Quantidade' => array('name' => 'T138_Quantidade', 'label' => 'Quantidade', 'type' => 'moeda', 'length' => array(20, 30)),
					'T138_Valor_Preco_Unitario' => array('name' => 'T138_Valor_Preco_Unitario', 'label' => 'Valor', 'type' => 'moeda', 'length' => array(20, 30)),
					'T138_Operacao' => array('name' => 'T138_Operacao', 'label' => 'Operação', 'type' => 'select', 'align' => 'right', 'style' => 'float:right;', 'select' => 
						array(
							array('title' => 'Parte Pagamento', 'value' => 'P', 'selected' => true),
							array('title' => 'Troca', 'value' => 'T'),
						)
					),					
					'T138_Observacao' => array('name' => 'T138_Observacao', 'label' => 'Observação', 'type' => 'text', 'length' => array(64, 120)), 
					// Campos Hidden
					'T138_T003_Id' => array('name' => 'T138_T003_Id', 'label' => '', 'type' => 'hidden', 'value' => @$r_T003_Id),
					'T138_C004_Id' => array('name' => 'T138_C004_Id', 'label' => '', 'type' => 'hidden', 'value' => $g['empresaAtual']),
					'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true),
					'T138_D009_Id' => array('name' => 'T138_D009_Id', 'label' => '', 'type' => 'hidden'),

				),
			)
		);
		// Botão Limpar / Novo Registro
		if (!empty($acaoId)) {
			 $formArray['campos']['trocas']['ven001FormTrocasBtnNew'] = array('name' => 'ven001FormTrocasBtnNew', 'type'=>'submit', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		}
		$formArray['campos']['trocas']['ven001FormTrocasBtnAdd'] = array('name' => 'ven001FormTrocasBtnAdd', 'type'=>'button', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['trocas']['ven001FormTrocasBtnCln'] = array('name' => 'ven001FormTrocasBtnCln', 'type'=>'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['trocas']['ven001BtnCadastrarProdutoTroca'] = array('name' => 'ven001BtnCadastrarProdutoTroca', 'type'=>'button', 'reload' => false, 'value' => 'Pré-Cadastrar Produto', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);	
		$form->definirFormularioArray($formArray);
		
		echo <<<EOT
		    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		    	$('#ven001BtnCadastrarProdutoTroca').unbind('click');
				$('#ven001BtnCadastrarProdutoTroca').unbind('click').bind('click', function(data) {
					//var id = $('#{$g['divId']} #T003_D024_Id').val();
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Pré-cadastrar Produto', '/cad/cad002/content/cad002ContentPreCadastro/', '', [600,300]);											
					return false;
				});
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
				$retorno = $VEN001->incluirProdutoTroca($form);
				if ($retorno !== true) {
					echo gGeraAlertMsg('Erro', $retorno);	
				} else {
					//echo gGeraAlertMsg('Operação OK', "Com sucesso");	
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
		$g['smarty']->display('formEditor.tpl');

	break;
	
	case 'ven001FormProcessos':
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
			$form_tabela = 'T146';
			$form_campo_pk = 'T146_Id';
			// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
			$enviar_btnId = array('ven001FormProcessosBtnAdd', 'ven001FormProcessosBtnCln', 'ven001FormProcessosBtnNew');
			// Form que será executado ao clicar no botão
			$enviar_acaoHref = '/ven/ven001/form/ven001FormProcessos/';

			// novo formulário
			$form = new Formulario('ven001FormProcessosForm');

			// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
			$formArray = array(
				'campos' => array(
					'geral' => array(
					//'T148_T147_Id' => array('name' => 'T148_T147_Id','label'=>'', 'type' => 'hidden','value' => $acaoId, 'length' => array(60,20)),
					//'T146_D110_Id' => array('name' => 'T146_D110_Id','label'=>'Competência', 'type' => 'pesquisa', 'pesquisa' => array('D110', 'D110_Id', 'D110_Titulo') ,'length' => array(50,10)),
					'T146_Titulo' => array('name' => 'T145_Titulo','label'=>'Título','type' => 'text', 'length' => array(50,30)),
					'T146_Descricao' => array('name' => 'T146_Descricao','label'=>'Descrição','type' => 'textarea', 'length' => array(3,47)),
					'T146_T145_Id' => array('name' => 'T146_T145_Id', 'type' => 'hidden', 'value' => @$r_T145_Id, 'length' => array(50,30)),
					'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true),
					)
				)
			);
			
			// Botão Limpar / Novo Registro
			if (!empty($acaoId)) {
				 $formArray['campos']['geral']['ven001FormProcessosBtnNew'] = array('name' => 'ven001FormProcessosBtnNew', 'type'=>'button', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
			}
			$formArray['campos']['geral']['ven001FormProcessosBtnCln'] = array('name' => 'ven001FormProcessosBtnCln', 'type'=>'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
			$formArray['campos']['geral']['ven001FormProcessosBtnAdd'] = array('name' => 'ven001FormProcessosBtnAdd', 'type'=>'submit', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);

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
							// Imprime uma mensagem e fecha a janela
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
			$g['smarty']->display('form.tpl');

	break;
	
	case 'ven001FormMateriais':
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
			$form_tabela = 'T152';
			$form_campo_pk = 'T152_Id';
			// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
			$enviar_btnId = array('ven001FormMateriaisBtnAdd', 'ven001FormMateriaisBtnCln', 'ven001FormMateriaisBtnNew');
			// Form que será executado ao clicar no botão
			$enviar_acaoHref = '/ven/ven001/form/ven001FormMateriais/';

			// Campo: Procura_Produto
			$pesquisaGridExtra = <<<EOT
				left join D049 on D049_D001_Id=D001_Id
				left join D009 on D009_D049_Id=D049_Id
				left join D082 on D082_Id=D049_D082_Id
				left join D037 on D037_Id=D001_D037_Id
				where (D009_C004_Id = '{$g['empresaAtual']}'
				  and D001_Flag_Pre_Cadastro!='S')
				AND NOT EXISTS (
					SELECT 1
					FROM D009A D009A_Visibilidade
					WHERE D009A_Visibilidade.D009A_D009_Id=D009_Id
					  AND D009A_Visibilidade.D009A_Flag_Pertence_Empresa='N'
				)
				group by D009_Id
				order by D001_Codigo_Produto
EOT;
			$pesquisaGrid = array(
				'D001',																								// tabela
				'D009_Id',																							// chave primaria
				'D001_Codigo_Produto,D001_Descricao_Produto,D082_Marca,D009_Quantidade_Estoque_Liquido',			// campos
				$pesquisaGridExtra,																					// extras sql
				'Pesquisa de Material',																				// título da janela
				'Código,Descrição,Marca,Estoque',																	// descrição dos campos
				'campoProcuraProdutoMaterial',																				// callback js
				$g['divId'],																						// divId
				'',//(isset($r_ven001FormProdutosForm) ? $r_ven001FormProdutosForm['T004_T003_Id'] : $r_T003_Id),		// parametro extra
				array(																								// parametros para cada campo do grid
					array('colunaAlinhar' => 'center'),
					array(),
					array(),
					array('callback' => 'gCorrigeNumero', 'colunaAlinhar' => 'right')
				)
			);
			
			// novo formulário
			$form = new Formulario('ven001FormMateriaisForm');
			
			// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
			$formArray = array(
				'campos' => array(
					'geral' => array(
						'ven001FormMateriaisPesquisa' => array('name' => 'ven001FormMateriaisPesquisa' , 'label' => 'Material', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGrid, 'length' => array(20,30), 'reject' => true),
						'Descricao_Produto' => array('name' => 'Descricao_Produto', 'label' => 'Descrição', 'type' => 'text', 'length' => array(64, 120),'reject' => true),
						'ven001_Marca' => array('name' => 'ven001_Marca', 'label' => 'Marca', 'type' => 'text', 'length' => array(20,255), 'reject' => true),			
						//'T148_T147_Id' => array('name' => 'T148_T147_Id','label'=>'', 'type' => 'hidden','value' => $acaoId, 'length' => array(60,20)),
						'T152_D009_Id' => array('name' => 'T152_D009_Id', 'label' => 'T152_D009_Id', 'type' => 'hidden'),
						'T152_Quantidade' => array('name' => 'T152_Quantidade','label'=>'Quantidade','type' => 'text', 'length' => array(20,10)),
						'T152_D037_Id' => array('name' => 'T152_D037_Id', 'label'=>'Unidade', 'extra'=>'style="width:182px;"', 'type' => 'select', 'select'=>
								gGeraSelect('D037', 'D037_Id', 'D037_Unidade', 'order by D037_Unidade', true),
						),
						'T152_T145_Id' => array('name' => 'T152_T145_Id', 'type' => 'hidden', 'value' => @$r_T145_Id, 'length' => array(50,30)),
						'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true),
					)
				)
			);
			
			// Botão Limpar / Novo Registro
			if (!empty($acaoId)) {
				 $formArray['campos']['geral']['ven001FormMateriaisBtnNew'] = array('name' => 'ven001FormMateriaisBtnNew', 'type'=>'button', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
			}
			$formArray['campos']['geral']['ven001FormMateriaisBtnCln'] = array('name' => 'ven001FormMateriaisBtnCln', 'type'=>'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
			$formArray['campos']['geral']['ven001FormMateriaisBtnAdd'] = array('name' => 'ven001FormMateriaisBtnAdd', 'type'=>'submit', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);

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
							// Imprime uma mensagem e fecha a janela
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
			$g['smarty']->display('form.tpl');

	break;
	
	
	case 'ven001FormTotalOrcamento':
	/* @author RubiaYwata
	* @since 16/11/2011
	*/
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
		$form_tabela = 'T003';
		$form_campo_pk = 'T003_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = '';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '';
        
        //form editor
        
        $form_editor = array(
            'geral' => '{"tituloTotal":{"top":4,"left":36,"width":null,"hidden":0},"T003_Valor_Total_Produtos_Sem_Desconto":{"top":30,"left":31,"width":125,"hidden":0},"T003_Valor_Desconto":{"top":53,"left":12,"width":125,"hidden":0},"T003_Valor_Total_Produtos":{"top":76,"left":31,"width":125,"hidden":0},"T003_Valor_ST":{"top":99,"left":56,"width":125,"hidden":0},"T003_Valor_Total_IPI":{"top":122,"left":64,"width":125,"hidden":0},"T003_Valor_Total_Servicos":{"top":145,"left":31,"width":125,"hidden":0},"T003_Valor_Frete":{"top":168,"left":49,"width":125,"hidden":0},"T003_Valor_Despesas_Acessorias":{"top":191,"left":20,"width":125,"hidden":0},"T003_Valor_Total_Agregados":{"top":214,"left":20,"width":125,"hidden":0},"T003_Valor_Troca":{"top":237,"left":40,"width":125,"hidden":0},"T003_Valor_Total":{"top":260,"left":52,"width":125,"hidden":0},"T003_Valor_Total_ICMS":{"top":283,"left":50,"width":125,"hidden":0},"T003_Peso_Total":{"top":306,"left":25,"width":125,"hidden":0},"ven001AlcadaOrcamento":{"top":99,"left":96,"width":175,"hidden":0},"ven001AlcadaDesconto":{"top":122,"left":96,"width":175,"hidden":0},"ven001AlcadaPrazoMedio":{"top":145,"left":96,"width":175,"hidden":0},"ven001AlcadaMotivoLiberacao":{"top":168,"left":96,"width":172,"hidden":0}}',
        );
		
		mysql_query("update T004
          left join T003 on T003_Id=T004_T003_Id
          left join D009 on D009_Id=T004_D009_Id
          left join D049 on D049_Id=D009_D049_Id
          left join D001 on D001_Id=D049_D001_Id
          left join T006 on T004_Id=T006_T004_Id
                set T004_Peso_Unitario=D001_Peso_Unitario_Kg 
              where T003_Id='$r_acaoId'
                and T006_T004_Id is Null");

		$cSql_T004=mysql_query("select T004_Id from T004 where T004_T003_Id='$r_acaoId'");
		while ($mT004=mysql_fetch_array($cSql_T004)) {
		      mysql_query("call T004_Gravar_Totalizacao_4('$mT004[T004_Id]')");
		}
		mysql_query("call T003_Gravar_Totalizacao_4($r_acaoId);");
		
		//$totalServicos = $VEN001->calculaTotalServicos($r_acaoId);
		$totalGeral = $VEN001->calculaTotalGeral($r_acaoId);

		
        /*
        * ************** IMPORTANTEEEEEE!!!! *****************
        *  Qualquer coisa que for mudado daqui para baixo até o próximo marcador, precisa ser mudado tbm na função $VEN001->verificaAlcada, e no documento ven001/form_func-ajax.php -> case 'verificaAlcada';
        *  Se possível, quando der, criar uma única função para os três casos de uso.
        */
		//Calcula Desconto por alçada
		$descontoOrcamento = $VEN001->calculaPercentualDescontoOrcamento($r_acaoId);
		$descontoOrcamento = number_format($descontoOrcamento, 2, ".", " ");
		$descontoVendedor = gCorrigeNumeroInverte($g['conf']->ini('alcadaDescontoVendedor'));
		$descontoSupervisor = gCorrigeNumeroInverte($g['conf']->ini('alcadaDescontoSupervisor'));

		$alcadaDescontoVendedorPorProduto = $g['conf']->ini('alcadaDescontoVendedorPorProduto');
		$alcadaDescontoSupervisorPorProduto = $g['conf']->ini('alcadaDescontoSupervisorPorProduto');
		$alcadaDescontoSupervisorPorProduto = gCorrigeNumeroInverte($alcadaDescontoSupervisorPorProduto);
		$alcadaDescontoVendedorPorProduto = gCorrigeNumeroInverte($alcadaDescontoVendedorPorProduto);


		//Calcula prazo médio por alçada
		$prazoMedioAlcadaVendedor = $VEN001->calculaPrazoMedioAlcadaVendedor($r_acaoId);
		$prazoMedioAlcadaSupervisor = $VEN001->calculaPrazoMedioAlcadaSupervisor($r_acaoId);
		$retorno=mysql_query("SELECT AVG(T089_Prazos_Dias) FROM T089 WHERE T089_T003_Id='{$r_acaoId}'");
		$T003=mysql_fetch_array($retorno);				
		$prazoMedio=$T003['AVG(T089_Prazos_Dias)'];
		$prazoMedio = (empty($prazoMedio)) ? "0" : $prazoMedio;
        $retornoSaldoMinimo = $VEN001->verificaFuturoSaldoProduto($r_acaoId,true);

        $T003MaiorDesconto = $VEN001->retornaMaiorDescontoOrcamento($r_acaoId);
        $T003MaiorDesconto = number_format($T003MaiorDesconto, 2, ".", " ");
        //die("Maior desconto: ".$T003MaiorDesconto);

        $usarAlcadaPorProduto = $g['conf']->ini('usarAlcadaPorProduto');

        //die($usarAlcadaPorProduto);
        //Verificações de alçada
        // if($T003MaiorDesconto <= $alcadaDescontoSupervisorPorProduto){
        // 	echo "SIM";
        // }else{
        // 	echo "Não";
        // }
        // die(		   $T003MaiorDesconto . " <= " . $alcadaDescontoSupervisorPorProduto. 
        // 	"<br />" . $descontoOrcamento . " <=" . $descontoSupervisor.
        // 	"<br />" . $prazoMedio . "<=" . $prazoMedioAlcadaSupervisor.
        // 	"<br />" . $usarAlcadaPorProduto . " == S".
        // 	"<br />" . $retornoSaldoMinimo . "== 0");
		if(($descontoOrcamento <= $descontoVendedor) && ($prazoMedio <= $prazoMedioAlcadaVendedor) && ($retornoSaldoMinimo==0)){

			$prazoMedio=gCorrigeNumero($T003['AVG(T089_Prazos_Dias)']);
			$descontoOrcamentoUsar = gCorrigeNumero($descontoOrcamento);
			$T003MaiorDescontoUsar = gCorrigeNumero($T003MaiorDesconto);
			$alcadaOrcamento = "Alçada: Vendedor";
			$alcadaDesconto = "Desconto: {$descontoOrcamentoUsar}%";
			$alcadaMaiorDesconto = "Maior Desconto: {$T003MaiorDescontoUsar}%";
			$alcadaPrazoMedio = "Prazo Médio: {$prazoMedio} Dias";

		} else if (($descontoOrcamento <= $descontoSupervisor) && ($prazoMedio <= $prazoMedioAlcadaSupervisor)){

            $prazoMedio=gCorrigeNumero($T003['AVG(T089_Prazos_Dias)']);
			$descontoOrcamentoUsar = gCorrigeNumero($descontoOrcamento);
			$T003MaiorDescontoUsar = gCorrigeNumero($T003MaiorDesconto);
			$alcadaOrcamento = "Alçada: Supervisor";
			$alcadaDesconto = "Desconto: {$descontoOrcamentoUsar}%";
			$alcadaMaiorDesconto = "Maior Desconto: {$T003MaiorDescontoUsar}%";
			$alcadaPrazoMedio = "Prazo Médio: {$prazoMedio} Dias";

		}else{

			$prazoMedio=gCorrigeNumero($T003['AVG(T089_Prazos_Dias)']);
			$descontoOrcamentoUsar = gCorrigeNumero($descontoOrcamento);
			$T003MaiorDescontoUsar = gCorrigeNumero($T003MaiorDesconto);
			$alcadaOrcamento = "Alçada: Diretor";
			$alcadaDesconto = "Desconto: {$descontoOrcamentoUsar}%";
			$alcadaMaiorDesconto = "Maior Desconto: {$T003MaiorDescontoUsar}%";
			$alcadaPrazoMedio = "Prazo Médio: {$prazoMedio} Dias";
		}

		// Verificação apenas para quando a global $usarAlcadaPorProduto for igual a sim
		if($usarAlcadaPorProduto == "S"){

			if($T003MaiorDesconto <= $alcadaDescontoVendedorPorProduto){

				$prazoMedio=gCorrigeNumero($T003['AVG(T089_Prazos_Dias)']);
				$descontoOrcamentoUsar = gCorrigeNumero($descontoOrcamento);
				$T003MaiorDescontoUsar = gCorrigeNumero($T003MaiorDesconto);
				$alcadaOrcamento = "Alçada: Vendedor";
				$alcadaDesconto = "Desconto: {$descontoOrcamentoUsar}%";
				$alcadaMaiorDesconto = "Maior Desconto: {$T003MaiorDescontoUsar}%";
				$alcadaPrazoMedio = "Prazo Médio: {$prazoMedio} Dias";

			}else if($T003MaiorDesconto <= $alcadaDescontoSupervisorPorProduto){

				$prazoMedio=gCorrigeNumero($T003['AVG(T089_Prazos_Dias)']);
				$descontoOrcamentoUsar = gCorrigeNumero($descontoOrcamento);
				$T003MaiorDescontoUsar = gCorrigeNumero($T003MaiorDesconto);
				$alcadaOrcamento = "Alçada: Supervisor";
				$alcadaDesconto = "Desconto: {$descontoOrcamentoUsar}%";
				$alcadaMaiorDesconto = "Maior Desconto: {$T003MaiorDescontoUsar}%";
				$alcadaPrazoMedio = "Prazo Médio: {$prazoMedio} Dias";

			}else{

				$prazoMedio=gCorrigeNumero($T003['AVG(T089_Prazos_Dias)']);
				$descontoOrcamentoUsar = gCorrigeNumero($descontoOrcamento);
				$T003MaiorDescontoUsar = gCorrigeNumero($T003MaiorDesconto);
				$alcadaOrcamento = "Alçada: Diretor";
				$alcadaDesconto = "Desconto: {$descontoOrcamentoUsar}%";
				$alcadaMaiorDesconto = "Maior Desconto: {$T003MaiorDescontoUsar}%";
				$alcadaPrazoMedio = "Prazo Médio: {$prazoMedio} Dias";
			}
		}
        
        $motivoPedidoLiberacao= '';
        $retornoStatus=mysql_query("SELECT * FROM T003 WHERE T003_Id='{$r_acaoId}'");
        $T003Status=mysql_fetch_array($retornoStatus);
        
        if(!empty($T003Status['T003_Motivo_Pedido_Liberacao'])){
            $motivoPedidoLiberacao .= $T003Status['T003_Motivo_Pedido_Liberacao'];
        }else{
            $motivoPedidoLiberacao .= $VEN001->motivoPedidoLiberacao($r_acaoId);
        }

        $totalConfirmado=$T003Status['T003_Valor_Total_Produtos_Confirmado'];
        $totalOrcamento=$T003Status['T003_Valor_Total_Produtos'];        
        $total='';
        if ($totalConfirmado<=0){
            $total=$totalOrcamento;
        }else{
            $total=$totalConfirmado;
        }

        if ($T003Status['T003_Liberou_Alcada_C007_Id']>0 && $T003Status['T003_Liberou_Alcada_Data']>0 && ( $T003Status['T003_Liberou_Alcada_Desconto']>=0 || $T003Status['T003_Liberou_Alcada_Prazo_Medio']>=0)){
	        if ($T003Status['T003_Liberou_Alcada_Valor_Total'] == $total || $T003Status['T003_Liberou_Alcada_Prazo_Medio'] == $prazoMedio){
	    		//$descontoOrcamento = gCorrigeNumero($descontoOrcamento);
	    		$T003MaiorDesconto = gCorrigeNumero($T003MaiorDesconto);
				$alcadaOrcamento = "Alçada: aprovado";
				$alcadaDesconto = "Desconto: {$descontoOrcamento}%";
				$alcadaMaiorDesconto = "Maior Desconto: {$T003MaiorDesconto}%";
				$alcadaPrazoMedio = "Prazo Médio: {$prazoMedio} Dias";  	
	        }
	    }
        /*
        * 
        *Aqui termina verificação de alçadaa-------------------- 
        * 
        */
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					'tituloTotal' => array('name' => 'tituloTotal', 'type' => 'titulo', 'label' => 'Total Orçamento', 'reject' => true,  'extra'=>'style=background:#E8E8E8;'),
					'T003_Valor_Total_Produtos_Sem_Desconto' => array('name' => 'T003_Valor_Total_Produtos_Sem_Desconto', 'label' => 'Produtos', 'type' => 'moeda', 'length' => array(20,40), 'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'),
					'T003_Valor_Desconto' => array('name' => 'T003_Valor_Desconto', 'label' => 'Desconto R$', 'type' => 'moeda', 'colunaCasasDecimais'=>'2', 'length' => array(20,40),'extra' => "onchange=\"gravarDesconto(this, '$r_divReload', '{$r_T003_Id}', 'T003_Valor_Desconto');\"",'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'),
					'T003_Valor_Total_Produtos' => array('name' => 'T003_Valor_Total_Produtos', 'label' => 'Produtos', 'type' => 'moeda', 'length' => array(20,40), 'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'),
					'T003_Valor_ST' => array('name' => 'T003_Valor_ST', 'label' => 'S.T.', 'type' => 'moeda', 'length' => array(20,40), 'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'),
					'T003_Valor_Total_IPI' => array('name' => 'T003_Valor_Total_IPI', 'label' => 'IPI', 'type' => 'moeda', 'length' => array(20,40), 'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'), 
					'T003_Valor_Total_Servicos' => array('name' => 'T003_Valor_Total_Servicos', 'label' => 'Serviços', 'type' => 'moeda', 'length' => array(20,40), 'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'),
					'T003_Valor_Frete' => array('name' => 'T003_Valor_Frete', 'label' => 'Frete', 'type' => 'moeda', 'length' => array(20,40), 'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'),
					'T003_Valor_Despesas_Acessorias' => array('name' => 'T003_Valor_Despesas_Acessorias', 'label' => 'Dp. Acess.', 'type' => 'moeda', 'length' => array(20,40), 'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'),
					'T003_Valor_Total_Agregados' => array('name' => 'T003_Valor_Total_Agregados', 'label' => 'Agregados', 'type' => 'moeda', 'length' => array(20,40), 'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'),
					'T003_Valor_Troca' => array('name' => 'T003_Valor_Troca', 'label' => 'Trocas', 'type' => 'moeda', 'length' => array(20,40), 'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'),
					'T003_Valor_Total' => array('name' => 'T003_Valor_Total', 'label' => 'Total', 'type' => 'moeda', 'length' => array(20,40), 'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'),
					'T003_Valor_Total_ICMS' => array('name' => 'T003_Valor_Total_ICMS', 'label' => 'ICMS', 'type' => 'moeda', 'length' => array(20, 40), 'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'),
					'T003_Peso_Total' => array('name' => 'T003_Peso_Total', 'label' => 'Peso Total', 'type' => 'text', 'length' => array(20,40), 'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'),
					'ven001AlcadaOrcamento' => array('name' => 'ven001AlcadaOrcamento', 'label' => '','type' => 'text','reject' => true,'value' => $alcadaOrcamento, 'length' => array(20,40), 'disabled' => 'true',  'extra'=>'style=margin-left:200px;:right;background:yellow;border-color: var(--BGVermelho);','length' => array(30,30)),
					'ven001AlcadaDesconto' => array('name' => 'ven001AlcadaDesconto', 'label' => '','type' => 'text','reject' => true,'value' => $alcadaDesconto, 'length' => array(20,40), 'disabled' => 'true',  'extra'=>'style=margin-left:200px;:right;background:yellow;border-color: var(--BGVermelho);','length' => array(30,30)),
					'ven001AlcadaDescontoProduto' => array('name' => 'ven001AlcadaDescontoProduto', 'label' => '','type' => 'text','reject' => true,'value' => $alcadaMaiorDesconto, 'length' => array(20,40), 'disabled' => 'true',  'extra'=>'style=margin-left:200px;:right;background:yellow;border-color: var(--BGVermelho);','length' => array(30,30)),
					'ven001AlcadaPrazoMedio' => array('name' => 'ven001AlcadaPrazoMedio', 'label' => '', 'type' => 'text','reject' => true,'value' => $alcadaPrazoMedio, 'length' => array(20,40), 'disabled' => 'true',  'extra'=>'style=margin-left:200px;:right;background:yellow;border-color: var(--BGVermelho);','length' => array(30,30)),
                    'ven001AlcadaMotivoLiberacao' => array('name' => 'ven001AlcadaMotivoLiberacao', 'label' => '', 'type' => 'textarea','reject' => true,'value' => $motivoPedidoLiberacao, 'length' => array(5,26), 'disabled' => 'true',  'extra'=>'style=margin-left:200px;:right;background:yellow;border-color: var(--BGVermelho);'),
					//'T003_IPV' => array('name' => 'T003_IPV', 'label' => 'IPV', 'type' => 'moeda', 'length' => array(20,40), 'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'),   
				)
			)
		);
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
			} else if ($formCodigo == 2) {
				// Insert ou Update?
				if (!empty($r_acaoId)) {
					// Update
					if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $r_acaoId, $form)) {
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
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
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

	break;
	
		case 'ven001FormTotalConfirmado':

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
		$form_tabela = 'T003';
		$form_campo_pk = 'T003_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = '';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '';
        
        //form editor
        $form_editor = array(
            'geral2' => '{"tituloTotal":{"top":4,"left":9,"width":null,"hidden":0},"T003_Valor_Total_Produtos_Sem_Desconto_Confirmado":{"top":30,"left":31,"width":125,"hidden":0},"T003_Valor_Desconto_Confirmado":{"top":53,"left":12,"width":125,"hidden":0},"T003_Valor_Total_Produtos_Confirmado":{"top":76,"left":31,"width":125,"hidden":0},"T003_Valor_ST_Confirmado":{"top":99,"left":56,"width":125,"hidden":0},"T003_Valor_Total_IPI_Confirmado":{"top":122,"left":64,"width":125,"hidden":0},"T003_Valor_Total_Servicos":{"top":145,"left":31,"width":125,"hidden":0},"T003_Valor_Frete":{"top":168,"left":49,"width":125,"hidden":0},"T003_Valor_Despesas_Acessorias":{"top":191,"left":20,"width":125,"hidden":0},"T003_Valor_Troca":{"top":214,"left":40,"width":125,"hidden":0},"T003_Valor_Total_Confirmado":{"top":237,"left":52,"width":125,"hidden":0},"T003_Valor_Total_ICMS_Confirmado":{"top":260,"left":50,"width":125,"hidden":0},"T003_Peso_Total_Confirmado":{"top":283,"left":25,"width":125,"hidden":0}}',
        );
        
		//$totalServicosConfirmado = $VEN001->calculaTotalServicosConfirmado($r_acaoId);
		$totalGeralConfirmado = $VEN001->calculaTotalGeralConfirmado($r_acaoId);
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral2' => array(
					// Campo normal (do banco)
					'tituloTotal' => array('name' => 'tituloTotal', 'type' => 'titulo', 'label' => 'Total Confirmado', 'reject' => true),
					'T003_Valor_Total_Produtos_Sem_Desconto_Confirmado' => array('name' => 'T003_Valor_Total_Produtos_Sem_Desconto_Confirmado', 'label' => 'Produtos', 'type' => 'moeda', 'length' => array(20,40), 'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'),
					'T003_Valor_Desconto_Confirmado' => array('name' => 'T003_Valor_Desconto_Confirmado', 'label' => 'Desconto R$', 'type' => 'moeda', 'length' => array(20,40),'extra' => "onchange=\"gravarDesconto(this, '{$r_divReload}', '{$r_T003_Id}', 'T003_Valor_Desconto_Confirmado');\"",'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'),
					'T003_Valor_Total_Produtos_Confirmado' => array('name' => 'T003_Valor_Total_Produtos_Confirmado', 'label' => 'Produtos', 'type' => 'moeda', 'length' => array(20,40), 'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'),
					'T003_Valor_ST_Confirmado' => array('name' => 'T003_Valor_ST_Confirmado', 'label' => 'S.T.', 'type' => 'moeda', 'length' => array(20,40), 'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'),
					'T003_Valor_Total_IPI_Confirmado' => array('name' => 'T003_Valor_Total_IPI_Confirmado', 'label' => 'IPI', 'type' => 'moeda', 'length' => array(20,40), 'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'),
					'T003_Valor_Total_Servicos' => array('name' => 'T003_Valor_Total_Servicos', 'label' => 'Serviços', 'type' => 'moeda', 'length' => array(20,40), 'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'),
					'T003_Valor_Frete' => array('name' => 'T003_Valor_Frete', 'label' => 'Frete', 'type' => 'moeda', 'length' => array(20,40), 'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'),
					'T003_Valor_Despesas_Acessorias' => array('name' => 'T003_Valor_Despesas_Acessorias', 'label' => 'Dp. Acess.', 'type' => 'moeda', 'length' => array(20,40), 'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'),
					'T003_Valor_Troca' => array('name' => 'T003_Valor_Troca', 'label' => 'Trocas', 'type' => 'moeda', 'length' => array(20,40), 'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'),
					'T003_Valor_Total_Confirmado' => array('name' => 'T003_Valor_Total_Confirmado', 'label' => 'Total','reject' => true,'value' => gCorrigeNumero($totalGeralConfirmado), 'type' => 'moeda', 'length' => array(20,40), 'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'),
     				'T003_Valor_Total_ICMS_Confirmado' => array('name' => 'T003_Valor_Total_ICMS_Confirmado', 'label' => 'ICMS', 'type' => 'moeda', 'length' => array(20, 40), 'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'),
					'T003_Peso_Total_Confirmado' => array('name' => 'T003_Peso_Total_Confirmado', 'label' => 'Peso Total', 'type' => 'text', 'length' => array(20,40), 'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'),
				)
			)
		);
        $form = new Formulario('form-ven001FormTotais2');
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
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
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
		
	break;
	
	case 'ven001FormProdutosEditar':
		global $g;
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida

		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$T003_Id = (isset($r_T003_Id)) ? $r_T003_Id : false;
		$D059_Id = (isset($_REQUEST['D059_Id'])) ? $_REQUEST['D059_Id'] : false;
		//$D059_Id = isset($r_D059_Id) ? $r_D059_Id : false;
	
		if (isset($r_ven001FormProdutosFormEditar) && $r_ven001FormProdutosFormEditar['T004_D059_Id']) {
			$D059_Id = $r_ven001FormProdutosFormEditar['T004_D059_Id'];
		}

		// Permite ou não alterar a descrição do produto e unidade
		$readOnlyDescricao = ($g['C031']['editarDescricaoProdutoVenda'] == 'S') ? false : true;
		$readOnlyUnidade   = ($g['C031']['editarUnidadeProdutoVenda'] == 'S') ? false : true;
		$extraUnidade      = ($g['C031']['editarUnidadeProdutoVenda'] == 'S') ? "" : "onfocus='this.initialSelect = this.selectedIndex;' onchange='this.selectedIndex = this.initialSelect;'";

		/**
		 * PARTE 2: Definição do formulário
		 */
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = true;
		if ($formMultiTab) {
			$formId = (isset($r_ven001FormProdutosForm)) ? $r_ven001FormProdutosForm['formId'] : $r_formId;
		} else {
			$formId = uniqid();
		}
		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'T004';
		$form_campo_pk = 'T004_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'ven001FormEditarProdutosBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/ven/ven001/form/ven001FormProdutosEditar/';
        
        //form editor
		$camposForm = '';
		if ($g['C031']['utilizaQuantidadeConversao'] === 'S') {
			$camposForm = ',"T004A_Quantidade_Conversao":{"top":76,"left":236.453125,"width":120,"hidden":0}';
		} 

		$sql = "SELECT T003A_Flag_Conversao_Unidade,
					   T003_D024_Id 
				  FROM T003 
		     LEFT JOIN T003A ON T003_Id = T003A_T003_Id
				 WHERE T003_Id = '{$r_T003_Id}'";
		$resT003 = mysql_query($sql);
		$rowT003 = mysql_fetch_assoc($resT003);

		if($rowT003['T003A_Flag_Conversao_Unidade'] != 'S'){
			//$posicaoItem = '{"T004A_Quantidade_Comercial":{"top":122,"left":514.4375,"width":120,"hidden":1},"ven001FormProdutosEditarProcuraProdutoEditar":{"top":7,"left":35.75,"width":344,"hidden":0},"Marca":{"top":30,"left":36.1875,"width":136,"hidden":0},"T004_Codigo_Substituto":{"top":30,"left":244.71875,"width":120,"hidden":0},"T004_Valor_Custo_Unitario":{"top":30,"left":629.109375,"width":120,"hidden":1},"T004_Valor_Preco_Original":{"top":30,"left":869.9375,"width":127,"hidden":1},"T004_Descricao_Produto":{"top":53,"left":18.265625,"width":360,"hidden":0},"T004_Quantidade":{"top":76,"left":10.984375,"width":125,"hidden":0},"T004_D037_Id":{"top":76,"left":267.046875,"width":127,"hidden":0},"T004A_Preco_Unitario_Comercial":{"top":99,"left":501.34375,"width":121,"hidden":1},"T004_Numero_Pedido_Compra":{"top":122,"left":17.890625,"width":125,"hidden":0},"T004_Item_Pedido_Compra":{"top":122,"left":275.109375,"width":120,"hidden":0},"T004_Observacao":{"top":145,"left":16.296875,"width":360,"hidden":0},"T004_D006_Id":{"top":168,"left":39.40625,"width":368,"hidden":0},"T004_Valor_Preco_Unitario":{"top":99,"left":5.34375,"width":125,"hidden":0}'. $camposForm .'}';
			$posicaoItem = '{"T004_Demonstrativo_Preco_Venda":{"top":191,"left":3.03125,"width":360,"hidden":0},"ven001FormProdutosEditarProcuraProdutoEditar":{"top":7,"left":35.75,"width":344,"hidden":0},"Marca":{"top":30,"left":36.1875,"width":136,"hidden":0},"T004_Codigo_Substituto":{"top":30,"left":244.71875,"width":120,"hidden":0},"T004_Valor_Preco_Original":{"top":99,"left":251.078125,"width":120,"hidden":0},"T004_Descricao_Produto":{"top":53,"left":18.265625,"width":360,"hidden":0},"T004_Quantidade":{"top":76,"left":10.984375,"width":125,"hidden":0},"T004_D037_Id":{"top":76,"left":267.046875,"width":127,"hidden":0},"T004_Valor_Preco_Unitario":{"top":99,"left":5.34375,"width":125,"hidden":0},"T004A_Preco_Unitario_Comercial":{"top":99,"left":501.34375,"width":121,"hidden":1},"T004_Numero_Pedido_Compra":{"top":122,"left":17.890625,"width":125,"hidden":0},"T004_Item_Pedido_Compra":{"top":122,"left":275.109375,"width":120,"hidden":0},"T004A_Quantidade_Comercial":{"top":122,"left":514.4375,"width":120,"hidden":1},"T004_Observacao":{"top":145,"left":16.296875,"width":360,"hidden":0},"T004_D006_Id":{"top":168,"left":39.40625,"width":368,"hidden":0}'.$camposForm.'}';
		} else {
			//$posicaoItem = '{"T004A_Quantidade_Comercial":{"top":76,"left":18.4375,"width":120,"hidden":0},"ven001FormProdutosEditarProcuraProdutoEditar":{"top":7,"left":35.75,"width":344,"hidden":0},"Marca":{"top":30,"left":36.1875,"width":136,"hidden":0},"T004_Codigo_Substituto":{"top":30,"left":244.71875,"width":120,"hidden":0},"T004_Valor_Custo_Unitario":{"top":30,"left":629.109375,"width":120,"hidden":1},"T004_Valor_Preco_Original":{"top":30,"left":869.9375,"width":127,"hidden":1},"T004_Descricao_Produto":{"top":53,"left":18.265625,"width":360,"hidden":0},"T004_Quantidade":{"top":99,"left":506.984375,"width":125,"hidden":1},"T004_D037_Id":{"top":76,"left":267.046875,"width":127,"hidden":0},"T004A_Preco_Unitario_Comercial":{"top":99,"left":5.34375,"width":121,"hidden":0},"T004_Numero_Pedido_Compra":{"top":122,"left":17.890625,"width":125,"hidden":0},"T004_Item_Pedido_Compra":{"top":122,"left":275.109375,"width":120,"hidden":0},"T004_Observacao":{"top":145,"left":16.296875,"width":360,"hidden":0},"T004_D006_Id":{"top":168,"left":39.40625,"width":368,"hidden":0},"T004_Valor_Preco_Unitario":{"top":122,"left":501.34375,"width":125,"hidden":1}'. $camposForm .'}';
			$posicaoItem = '{"ven001FormProdutosEditarProcuraProdutoEditar":{"top":7,"left":35.75,"width":344,"hidden":0},"Marca":{"top":30,"left":36.1875,"width":136,"hidden":0},"T004_Codigo_Substituto":{"top":30,"left":244.71875,"width":120,"hidden":0},"T004_Valor_Preco_Original":{"top":99,"left":251.078125,"width":120,"hidden":0},"T004_Descricao_Produto":{"top":53,"left":18.265625,"width":360,"hidden":0},"T004A_Quantidade_Comercial":{"top":76,"left":18.4375,"width":120,"hidden":0},"T004_D037_Id":{"top":76,"left":267.046875,"width":127,"hidden":0},"T004A_Preco_Unitario_Comercial":{"top":99,"left":5.34375,"width":121,"hidden":0},"T004_Quantidade":{"top":99,"left":506.984375,"width":125,"hidden":1},"T004_Numero_Pedido_Compra":{"top":122,"left":17.890625,"width":125,"hidden":0},"T004_Item_Pedido_Compra":{"top":122,"left":275.109375,"width":120,"hidden":0},"T004_Valor_Preco_Unitario":{"top":122,"left":501.34375,"width":125,"hidden":1},"T004_Observacao":{"top":145,"left":16.296875,"width":360,"hidden":0},"T004_D006_Id":{"top":168,"left":39.40625,"width":368,"hidden":0},"T004_Demonstrativo_Preco_Venda":{"top":191,"left":3.03125,"width":360,"hidden":0}'.$camposForm.'}';
		}

        $form_editor = array(
            'item' => $posicaoItem,
            'ipi' => '{"T004_D005_Id":{"top":7,"left":93,"width":272,"hidden":0},"T004_Aliquota_IPI":{"top":30,"left":99,"width":104,"hidden":0},"T004_Situacao_Tributaria_IPI":{"top":53,"left":19,"width":270,"hidden":0}}',
            'icms' => '{"T004_Preco_Pauta_ST":{"top":168,"left":334.5625,"width":88,"hidden":1},"T004_Flag_ST":{"top":7,"left":77.046875,"width":57,"hidden":0},"T004_D059_Id":{"top":30,"left":94.15625,"width":303,"hidden":0},"T004_ST_ICMS_Interno":{"top":53,"left":108.234375,"width":40,"hidden":0},"T004_ICMS_Inter_Estadual":{"top":53,"left":330.609375,"width":40,"hidden":0},"T004_Valor_ICMS_Substituicao_Destino":{"top":76,"left":26.71875,"width":40,"hidden":1},"T004_Aliquota_ICMS":{"top":76,"left":145.546875,"width":40,"hidden":0},"T004_Percentual_Reducao_ICMS_ST":{"top":76,"left":289.640625,"width":40,"hidden":0},"T004_Valor_Base_ICMS_Substituicao_Destino":{"top":99,"left":70.546875,"width":40,"hidden":1},"T004_Percentual_Reducao_ICMS":{"top":99,"left":98.453125,"width":40,"hidden":0},"T004_ST_VA":{"top":99,"left":418.859375,"width":40,"hidden":0},"T004_Modalidade_ICMS":{"top":122,"left":50.125,"width":304,"hidden":0},"T004_Modalidade_ICMS_Substituicao":{"top":145,"left":50.125,"width":304,"hidden":0},"T004_Percentual_Base_Operacao_Propria":{"top":168,"left":56.25,"width":40,"hidden":0},"T004_ICMS_Inter_Estadual_MVA":{"top":191,"left":16.125,"width":116,"hidden":1},"T004_ST_ICMS_Interno_MVA":{"top":214,"left":52.1875,"width":116,"hidden":1}}',
            'pis-cofins' => '{"T004_Aliquota_PIS":{"top":7,"left":110,"width":100,"hidden":0},"T004_Flag_Isento_PIS":{"top":30,"left":109,"width":56,"hidden":0},"T004_Situacao_Tributaria_PIS":{"top":53,"left":30,"width":288,"hidden":0},"T004_Aliquota_COFINS":{"top":76,"left":88,"width":100,"hidden":0},"T004_Flag_Isento_COFINS":{"top":99,"left":109,"width":56,"hidden":0},"T004_Situacao_Tributaria_COFINS":{"top":122,"left":8,"width":288,"hidden":0}}'
		);
		
		if(!$D059_Id){
			$form_editor['icms'] = '{"T004_Aliquota_ICMS":{"top":7,"left":88,"width":100,"hidden":0},"T004_Percentual_Reducao_ICMS":{"top":30,"left":98,"width":100,"hidden":0},"T004_D059_Id":{"top":53,"left":64,"width":288,"hidden":0},"T004_Flag_ST":{"top":76,"left":47,"width":56,"hidden":0},"T004_ST_ICMS_Interno":{"top":99,"left":52,"width":100,"hidden":0},"T004_ST_ICMS_Interno_MVA":{"top":99,"left":314,"width":100,"hidden":0},"T004_ICMS_Inter_Estadual":{"top":122,"left":18,"width":100,"hidden":0},"T004_ICMS_Inter_Estadual_MVA":{"top":122,"left":280,"width":100,"hidden":0},"T004_ST_VA":{"top":145,"left":131,"width":100,"hidden":0}}';
		}
		
		
		// Campo: Procura_Produto
		$pesquisaGridExtra = <<<EOT
			left join D049 on D049_D001_Id=D001_Id
			left join D041 on D041_D049_Id=D049_Id
			left join D009 on D009_D049_Id=D049_Id
			left join D082 on D082_Id=D049_D082_Id
			left join D037 on D037_Id=D001_D037_Id 
			LEFT JOIN D083 on D083_D001_Id=D001_Id AND D083_D024_Id='{$rowT003['T003_D024_Id']}'
			LEFT JOIN D144 ON D144_D049_Id=D049_Id
			where (D009_C004_Id = '{$g['empresaAtual']}'
			and D001_Flag_Ativo='S')
			AND NOT EXISTS (
				SELECT 1
				FROM D009A D009A_Visibilidade
				WHERE D009A_Visibilidade.D009A_D009_Id=D009_Id
				  AND D009A_Visibilidade.D009A_Flag_Pertence_Empresa='N'
			)
			group by D009_Id
			order by D001_Codigo_Produto ASC
EOT;
		$pesquisaGrid = array(
			'D001',																											              // tabela
			'D009_Id,D144_Id',																								              // chave primaria
			'D001_Codigo_Produto||D083_Codigo_Produto_Fornecedor||D001_Descricao_Produto||D082_Marca||D009_Preco_1||D009_Preco_2||D009_Preco_3||D009_Quantidade_Estoque||D009_Quantidade_Pedido(D009_Id,1) as Pedido||D009_Quantidade_Estoque_Liquido||D009_Id as D009Id||D001_Descricao_Ingles||D009_Id||D049_Id||D144_Id',	// campos
			$pesquisaGridExtra,																								              // extras sql
			'Pesquisa de Produto',																										  // título da janela
			'Cód.,Cód.Cli,Descrição,Marca,Preço 1,Preço 2,Preço 3,Estoque Real,Pedidos,Estoque Líq,Estoque MP,Descrição Inglês,Link,D049_Id',	  // descrição dos campos
			'ven001CampoProcuraProdutoEditar',																					          // callback js
			$g['divId'],																									              // divId
			$r_T003_Id,					              																					  // parametro extra
			array(																											              // parametros para cada campo do grid
				array('colunaAlinhar' => 'center'),
				array('colunaAlinhar' => 'center'),
				array('style' => 'font-weight:bold'),
				array(),
				array('callback' => 'gCorrigeNumero', 'colunaAlinhar' => 'right','style' => 'font-weight:bold'),
				array('callback' => 'gCorrigeNumero', 'colunaAlinhar' => 'right','style' => 'font-weight:bold'),
				array('callback' => 'gCorrigeNumero', 'colunaAlinhar' => 'right','style' => 'font-weight:bold'),
				array('callback' => 'gCorrigeNumero', 'colunaAlinhar' => 'right','style' => 'font-weight:bold'),
				array('callback' => 'qtePedidos', 'colunaAlinhar' => 'right','style' => 'font-weight:normal', 'colunaExibir' => false),
				array('callback' => 'gCorrigeNumero', 'colunaAlinhar' => 'right','style' => 'font-weight:bold'),
				array('callback' => 'validaEstoqueMP', 'colunaAlinhar' => 'right','style' => 'font-weight:bold'),				
                array(),
				array('callback' => 'campoLinkProdutoPesquisaGrid'),
				array('colunaExibir' => false),
				array('colunaExibir' => false),
			),
			'D009_Id||D049_Id||D009_Preco_1||D009_Preco_2||D009_Preco_3||D009_Quantidade_Estoque||D009_Quantidade_Estoque_Liquido||D144_Id' // Campos que são ignorados na cláusula where do pesquisaGrid
		);
		
		$T004=mysql_query("select *
		                    from T004
		               left join T004A on T004A_T004_Id=T004_Id
	                   left join D009 on D009_Id=T004_D009_Id
	                   left join D049 on D049_Id=D009_D049_Id
	                   left join D082 on D082_Id=D049_D082_Id
		                   where T004_Id='$acaoId'");
		$mT004=mysql_fetch_array($T004);

		$readOnlyEdicao = ($g['C031']['gerarAtividadesManipulacaoItens'] == 'S') ? true : false;
		$descricaoCFOP  = ($g['C031']['descricaoCFOPExibida'] == 'P') ? 'D006_Descricao' : 'IF(D006_Descricao_Comercial !="",D006_Descricao_Comercial,D006_Descricao) as D006_Descricao';
		
		// novo formulário
		$form = new Formulario('ven001FormProdutosFormEditar');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)

		$formArray = array(
			'campos' => array(
				'item' => array(
					'ven001FormProdutosEditarProcuraProdutoEditar' => array('name' => 'ven001FormProdutosEditarProcuraProdutoEditar', 'label' => 'Produto','type' => 'pesquisaGrid', 'reject' => true, 'pesquisaGrid' => $pesquisaGrid, 'readonly' => $readOnlyEdicao, 'length' => array(20,30), 'value' => $mT004['T004_Codigo_Produto']),
					'Marca' => array('name' => 'Marca', 'label' => 'Marca', 'type' => 'text', 'length' => array(20,255), 'reject' => true, 'disabled'=>'disabled', 'value' => "$mT004[D082_Marca]"),
					'T004_Codigo_Substituto' => array('name' => 'T004_Codigo_Substituto', 'label' => 'Código cliente', 'type' => 'text', 'length' => array(20, 120), 'align' => 'right', 'style' => 'float:right;'),
					'T004_Descricao_Produto' => array('name' => 'T004_Descricao_Produto', 'label' => 'Descrição', 'type' => 'text', 'readonly' => $readOnlyDescricao, 'length' => array(64, 120)),
					'T004_Quantidade' => array('name' => 'T004_Quantidade', 'label' => 'Quantidade', 'type' => 'moeda', 'casasDecimais' => '3', 'length' => array(20, 30), 'readonly' => $readOnlyEdicao),
					'T004A_Quantidade_Comercial' => array('name' => 'T004A_Quantidade_Comercial', 'label' => 'Quantidade', 'type' => 'moeda', 'casasDecimais' => '3', 'length' => array(20, 30), 'readonly' => $readOnlyEdicao),				
					'T004_D037_Id' => array('name' => 'T004_D037_Id', 'label' => 'Unidade', 'type' => 'select', 'align' => 'right', 'readonly' => $readOnlyDescricao, 'extra' => $extraUnidade, 'style' => 'float:right;', 'select' => 
						gGeraSelect('D037', 'D037_Id', 'D037_Unidade', '', true)
					),
					'T004_Valor_Preco_Unitario' => array('name' => 'T004_Valor_Preco_Unitario', 'label' => 'Preço Unitário', 'type' => 'moeda', 'length' => array(20, 30), 'readonly' => $readOnlyEdicao	),
					'T004A_Preco_Unitario_Comercial' => array('name' => 'T004A_Preco_Unitario_Comercial', 'label' => 'Preço Unitário', 'type' => 'moeda', 'length' => array(20, 30), 'readonly' => $readOnlyEdicao),
					'T004_Valor_Custo_Unitario' => array('name' => 'T004_Valor_Custo_Unitario', 'label' => 'Custo Unitário', 'type' => 'hidden', 'align' => 'right', 'readonly' => 'true', 'disabled' => 'true', 'style' => 'float:right;', 'length' => array(20, 30)),
					'T004_Valor_Preco_Original' => array('name' => 'T004_Valor_Preco_Original', 'label' => 'Preço Tabela', 'type' => 'moeda', 'align' => 'right', 'style' => 'float:right;', 'length' => array(20, 30), 'readonly' => true),
					'T004_Numero_Pedido_Compra' => array('name' => 'T004_Numero_Pedido_Compra', 'label' => 'Número OC', 'type' => 'text', 'length' => array(20, 120)),
					'T004_Item_Pedido_Compra' => array('name' => 'T004_Item_Pedido_Compra', 'label' => 'Item OC', 'type' => 'text', 'length' => array(20, 120), 'align' => 'right', 'style' => 'float:right;'),
					'T004_Observacao' => array('name' => 'T004_Observacao', 'label' => 'Observação', 'type' => 'text', 'length' => array(65, 120)),
					'T004_D006_Id' => array('name' => 'T004_D006_Id', 'label' => 'CFOP', 'type' => 'select',
						'select' => gGeraSelect('D006', 'D006_Id', "LPAD(D006_Id[[VIRGULA]]4[[VIRGULA]]0), D006_Codigo_CFOP, {$descricaoCFOP}", 'WHERE IFNULL(D006_Flag_Inativo,"N")!="S" AND D006_Flag_Departamento="COMERCIAL" ORDER BY D006_Codigo_CFOP', true, false, '%1 - %2 - %3'),
					),
					
					// Campos Hidden
					'T004_T003_Id' => array('name' => 'T004_T003_Id', 'label' => 'T004_T003_Id', 'type' => 'hidden' ),
					'T004_D082_Id' => array('name' => 'T004_D082_Id', 'label' => 'T004_D082_Id', 'type' => 'hidden' ),
					'T004_Codigo_Produto' => array('name' => 'T004_Codigo_Produto', 'label' => 'Código Produto', 'type' => 'hidden'),
					'T004_C004_Id' => array('name' => 'T004_C004_Id', 'label' => 'C004_Id', 'type' => 'hidden', 'value' => $g['empresaAtual']),
					'T004_D009_Id' => array('name' => 'T004_D009_Id', 'label' => 'D009_Id', 'type' => 'hidden'),
					'T004_Id' => array('name' => 'T004_Id', 'label' => '', 'type' => 'hidden','value' => $acaoId),
					'T004A_Altura' => array('name' => 'T004A_Altura', 'type' => 'hidden', 'reject' => true, 'value' => $mT004['T004A_Altura']),
					'T004A_Largura' => array('name' => 'T004A_Largura', 'type' => 'hidden', 'reject' => true, 'value' => $mT004['T004A_Largura']),
					'T004A_Comprimento' => array('name' => 'T004A_Comprimento', 'type' => 'hidden', 'reject' => true, 'value' => $mT004['T004A_Comprimento']),
					'T004A_Resultado' => array('name' => 'T004A_Resultado', 'type' => 'hidden', 'reject' => true, 'value' => $mT004['T004A_Resultado']),
					'formId' => array('name' => 'formId', 'label' => '', 'type' => 'hidden', 'value' => $formId, 'reject' => true),
					'T004_Demonstrativo_Preco_Venda' => array('name' => 'T004_Demonstrativo_Preco_Venda', 'label' => 'Demonstrativo', 'type' => 'hidden', 'uppercase' => false),					

				),
				//TAB IPI
				'ipi' => array(
					'T004_D005_Id' => array('name' => 'T004_D005_Id', 'label' => 'NCM', 'type' => 'select',  'select' => 
						gGeraSelect('D149', 'D005_Id', 'D005_Classificacao_Fiscal, D005_Flag_Aplicacao, D149_Aliquota_IPI', "LEFT JOIN D005 ON D149_D005_Id=D005_Id AND D149_C004_Id='{$g['empresaAtual']}' WHERE D005_Flag_Ativo='S' ORDER BY D005_Classificacao_Fiscal, D005_Observacao", true)
					),
					'T004_Aliquota_IPI' => array('name' => 'T004_Aliquota_IPI', 'label' => 'IPI %', 'type' => 'moeda', 'length' => array(15, 20)),
					'T004_Situacao_Tributaria_IPI' => array('name' => 'T004_Situacao_Tributaria_IPI', 'label' => 'Situação Tributária IPI', 'type' => 'select',  'select' => 
						gGeraSelect('D088', 'D088_Codigo_ST', 'D088_Codigo_ST,D088_Descricao', '', true)
					),
				),
				//TAB ICMS
				'icms' => array(
					'T004_Aliquota_ICMS' => array('name' => 'T004_Aliquota_ICMS', 'label' => '% ICMS', 'type' => 'moeda', 'length' => array(15, 20)),
					'T004_Percentual_Reducao_ICMS' => array('name' => 'T004_Percentual_Reducao_ICMS', 'label' => '% Redução ICMS', 'type' => 'moeda', 'length' => array(15, 20)),
					'T004_D059_Id' => array('name' => 'T004_D059_Id', 'label' => 'Situação Tributária', 'type' => 'select', 'extra'=>'style="width:288px;"',  'select' => 
					gGeraSelect('D059', 'D059_Id', 'D059_Codigo_ST,D059_Descricao_Origem', '', true)
					),
					'T004_Flag_ST' => array('name' => 'T004_Flag_ST', 'label' => 'Substituição Tributária', 'type' => 'select', 'select' => 
					array(
					array('title' => 'Sim', 'value' => 'S'),
					array('title' => 'Não', 'value' => 'N', 'selected' => true)
					)
					),
					'T004_ST_ICMS_Interno' => array('name' => 'T004_ST_ICMS_Interno', 'label' => '% ICMS Interno', 'type' => 'moeda', 'length' => array(15, 20)),
					'T004_ST_ICMS_Interno_MVA' => array('name' => 'T004_ST_ICMS_Interno_MVA', 'label' => 'Aliquota ICMS Interno MVA', 'type' => 'moeda', 'length' => array(15, 20), 'align'=>'right', 'style'=>'float:right'),
					'T004_ICMS_Inter_Estadual' => array('name' => 'T004_ICMS_Inter_Estadual', 'label' => '% ICMS Inter Estadual', 'type' => 'moeda', 'length' => array(15, 20)),
					'T004_ICMS_Inter_Estadual_MVA' => array('name' => 'T004_ICMS_Inter_Estadual_MVA', 'label' => 'Aliquitoa ICMS Inter Estadual MVA', 'type' => 'moeda', 'length' => array(15, 20), 'align'=>'right', 'style'=>'float:right'),
					'T004_ST_VA' => array('name' => 'T004_ST_VA', 'label' => 'MVA', 'type' => 'moeda', 'length' => array(15, 20)),
					'T004_Modalidade_ICMS_Substituicao' => array('name' => 'T004_Modalidade_ICMS_Substituicao', 'label' => 'Modalidade de determinação <br/>da BC ao ICMS ST', 'type' => 'select', 'select' => 
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
					),
					'T004_Modalidade_ICMS' => array('name' => 'T004_Modalidade_ICMS', 'label' => 'Modalidade de determinação <br/>da BC do ICMS', 'type' => 'select', 'select' => 
						array(
							array('title' => '...', 'value' => '','selected' => true),
							array('title' => 'Margem Valor Agregado (%)', 'value' => '0'),
							array('title' => 'Pauta (Valor)', 'value' => '1'),
							array('title' => 'Preço Tabelado Máx. (valor)', 'value' => '2'),
							array('title' => 'Valor da Operação', 'value' => '3')
						)
					),
					'T004_Percentual_Reducao_ICMS_ST' => array('name' => 'T004_Percentual_Reducao_ICMS_ST', 'label' => '% Redução de BC do ICMS ST', 'type' => 'moeda', 'length' => array(10,15)),
					'T004_Valor_ICMS_Substituicao_Destino' => array('name' => 'T004_Valor_ICMS_Substituicao_Destino', 'label' => 'Valor do ICMS ST da UF destino', 'type' => 'moeda'),
					'T004_Valor_Base_ICMS_Substituicao_Destino' => array('name' => 'T004_Valor_Base_ICMS_Substituicao_Destino', 'label' => 'Valor da BC do ICMS ST <br/>da UF destino', 'type' => 'moeda'),
					'T004_Percentual_Base_Operacao_Propria' => array('name' => 'T004_Percentual_Base_Operacao_Propria', 'label' => '% da BC operação própria', 'type' => 'moeda', 'length' => array(10,15)),
					'T004_Preco_Pauta_ST' => array('name' => 'T004_Preco_Pauta_ST', 'label' => 'Preço pauta', 'type' => 'moeda', 'length' => array(10,15)),
				),
				//TAB PIS/COFINS
				'pis-cofins' => array(
					'T004_Aliquota_PIS' => array('name' => 'T004_Aliquota_PIS', 'label' => 'PIS %', 'type' => 'moeda', 'length' => array(15, 20)),
					'T004_Flag_Isento_PIS' => array('name' => 'T004_Flag_Isento_PIS', 'label' => 'Isento', 'type' => 'select', 'select' => 
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N', 'selected' => true)
						)
					),
					'T004_Situacao_Tributaria_PIS' => array('name' => 'T004_Situacao_Tributaria_PIS', 'label' => 'Situação Tributária PIS', 'extra'=>'style="width:288px;"', 'type' => 'select', 'select' =>
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
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
					
					'T004_Aliquota_COFINS' => array('name' => 'T004_Aliquota_COFINS', 'label' => 'COFINS %', 'type' => 'moeda', 'length' => array(15, 20)),
					
					'T004_Flag_Isento_COFINS' => array('name' => 'T004_Flag_Isento_COFINS', 'label' => 'Isento', 'type' => 'select', 'select' => 
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N', 'selected' => true)
						)
					),
					'T004_Situacao_Tributaria_COFINS' => array('name' => 'T004_Situacao_Tributaria_COFINS', 'label' => 'Situação Tributária COFINS', 'extra'=>'style="width:288px;"', 'type' => 'select', 'select' =>
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('value'=>'01', 'title'=>	'01 - Operação Tributável com Alíquota Básica'),
							array('value'=>'02', 'title'=>	'02 - Operação Tributável com Alíquota Diferenciada'),
							array('value'=>'03', 'title'=>	'03 - Operação Tributável com Alíquota por Unidade de Medida de Produto'),
							array('value'=>'04', 'title'=>	'04 - Operação Tributável Monofásica - Revenda a Alíquota Zero'),
							array('value'=>'05', 'title'=>	'05 - Operação Tributável por Substituição Tributária'),
							array('value'=>'06', 'title'=>	'06 - Operação Tributável a Alíquota Zero'),
							array('value'=>'07', 'title'=>	'07 - Operação Isenta da Contribuição'),
							array('value'=>'08', 'title'=>	'08 - Operação sem Incidência da Contribuição'),
							array('value'=>'09', 'title'=>	'09 - Operação com Suspensão da Contribuição'),
							array('value'=>'49', 'title'=>	'49 - Outras Operações de Saída'),
							array('value'=>'50', 'title'=>	'50 - Operação com Direito a Crédito - Vinculada Exclusivamente a Receita Tributada no Mercado Interno'),
							array('value'=>'51', 'title'=>	'51 - Operação com Direito a Crédito - Vinculada Exclusivamente a Receita Não-Tributada no Mercado Interno'),
							array('value'=>'52', 'title'=>	'52 - Operação com Direito a Crédito - Vinculada Exclusivamente a Receita de Exportação'),
							array('value'=>'53', 'title'=>	'53 - Operação com Direito a Crédito - Vinculada a Receitas Tributadas e Não-Tributadas no Mercado Interno'),
							array('value'=>'54', 'title'=>	'54 - Operação com Direito a Crédito - Vinculada a Receitas Tributadas no Mercado Interno e de Exportação'),
							array('value'=>'55', 'title'=>	'55 - Operação com Direito a Crédito - Vinculada a Receitas Não Tributadas no Mercado Interno e de Exportação'),
							array('value'=>'56', 'title'=>	'56 - Operação com Direito a Crédito - Vinculada a Receitas Tributadas e Não-Tributadas no Mercado Interno e de Exportação'),
							array('value'=>'60', 'title'=>	'60 - Crédito Presumido - Operação de Aquisição Vinculada Exclusivamente a Receita Tributada no Mercado Interno'),
							array('value'=>'61', 'title'=>	'61 - Crédito Presumido - Operação de Aquisição Vinculada Exclusivamente a Receita Não-Tributada no Mercado Interno'),
							array('value'=>'62', 'title'=>	'62 - Crédito Presumido - Operação de Aquisição Vinculada Exclusivamente a Receita de Exportação'),
							array('value'=>'63', 'title'=>	'63 - Crédito Presumido - Operação de Aquisição Vinculada a Receitas Tributadas e Não-Tributadas no Mercado Interno'),
							array('value'=>'64', 'title'=>	'64 - Crédito Presumido - Operação de Aquisição Vinculada a Receitas Tributadas no Mercado Interno e de Exportação'),
							array('value'=>'65', 'title'=>	'65 - Crédito Presumido - Operação de Aquisição Vinculada a Receitas Não-Tributadas no Mercado Interno e de Exportação'),
							array('value'=>'66', 'title'=>	'66 - Crédito Presumido - Operação de Aquisição Vinculada a Receitas Tributadas e Não-Tributadas no Mercado Interno e de Exportação'),
							array('value'=>'67', 'title'=>	'67 - Crédito Presumido - Outras Operações'),
							array('value'=>'70', 'title'=>	'70 - Operação de Aquisição sem Direito a Crédito'),
							array('value'=>'71', 'title'=>	'71 - Operação de Aquisição com Isenção'),
							array('value'=>'72', 'title'=>	'72 - Operação de Aquisição com Suspensão'),
							array('value'=>'73', 'title'=>	'73 - Operação de Aquisição a Alíquota Zero'),
							array('value'=>'74', 'title'=>	'74 - Operação de Aquisição sem Incidência da Contribuição'),
							array('value'=>'75', 'title'=>	'75 - Operação de Aquisição por Substituição Tributária'),
							array('value'=>'98', 'title'=>	'98 - Outras Operações de Entrada'),
							array('value'=>'99', 'title'=>	'99 - Outras Operações'),
						)
					),
				)
			)
		);

		if ($g['C031']['utilizaQuantidadeConversao'] === 'S') {
			$formArray['campos']['item']['usaConversao']               = array('name' => 'usaConversao', 'label' => 'usaConversao', 'type' => 'hidden', 'reject' => true);
			$formArray['campos']['item']['T004A_Quantidade_Conversao'] = array('name' => 'T004A_Quantidade_Conversao', 'label' => 'Qtde Conversão', 'type' => 'moeda', 'length' => array(20, 30), 'readonly' => $readOnlyEdicao);
			$formArray['campos']['item']['D001_Peso_Unitario_Kg']      = array('name' => 'D001_Peso_Unitario_Kg', 'label' => 'D001_Peso_Unitario_Kg', 'type' => 'hidden', 'reject' => true);
		}

		// Botão Limpar / Novo Registro
		if (!empty($acaoId)) {
			 $formArray['campos']['produto']['ven001FormProdutosBtnNew'] = array('name' => 'ven001FormProdutosBtnNew', 'type'=>'submit', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		}
		$formArray['campos']['produto']['ven001FormProdutosBtnCln'] = array('name' => 'ven001FormProdutosBtnCln', 'type'=>'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['produto']['ven001FormProdutosBtnAdd'] = array('name' => 'ven001FormProdutosBtnAdd', 'type'=>'button', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		
		//Campos ICMS
		$C004 =  mysql_fetch_assoc(mysql_query("SELECT C004_Flag_Empresa_Simples FROM C004 WHERE C004_iD='{$g['empresaAtual']}'"));
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
		}
		
		$formArray['campos']['icms']['T004_D059_Id'] =  array('name' => 'T004_D059_Id', 'label' => 'Situação Tributária', 'type' => 'select',  
			'select' => $arrayCST			
		);

		$formArray['campos']['icms']['T004_Flag_ST'] =  array('name' => 'T004_Flag_ST', 'label' => 'Substituição Tributária', 'type' => 'select', 'select' => 
			array(
				array('title' => 'Sim', 'value' => 'S'),
				array('title' => 'Não', 'value' => 'N', 'selected' => true)
			)
		);
		
		// Desabilita a edição do formulário
		$formArray['campos']['icms']['Tab_Icms_Produto']  = array('name' => 'Tab_Icms_Produto', 'label' => '', 'type' => 'hidden', 'reject' => true);
		
		$form->definirFormularioArray($formArray);

		$camposMostrarICMS = array();
		$camposMostrarICMS['T004_Aliquota_ICMS'] = array('CST' => array(1, 2, 12, 3, 8, 10, 11, 14, 24));
		$camposMostrarICMS['T004_Modalidade_ICMS'] = array('CST' => array(1, 2, 12, 3, 8, 10, 11, 14, 24));
		$camposMostrarICMS['T004_ST_ICMS_Interno'] = array('CST' => array(1, 2, 3, 4, 12, 13, 3, 5, 6, 7, 8, 10, 11, 14, 15, 16, 17, 18, 19, 20, 21, 22, 24));
		$camposMostrarICMS['T004_Percentual_Partilha_ICMS_Origem'] = array('CST' => array(1, 2, 3, 6, 12, 5, 13, 7, 8, 10, 11, 14, 15, 16, 17, 18, 19, 20, 21, 22, 24));
		$camposMostrarICMS['T004_Percentual_Partilha_ICMS_Destino'] = array('CST' => array(1, 2, 3, 6, 12, 5, 13, 7, 8, 10, 11, 14, 15, 16, 17, 18, 19, 20, 21, 22, 24));
		$camposMostrarICMS['T004_Percentual_Reducao_ICMS'] = array('CST' => array(3, 12, 8, 10, 11, 14, 24));
		$camposMostrarICMS['T004_ICMS_Inter_Estadual'] = array('CST' => array(2, 3, 12, 4, 10, 11, 14, 18, 19, 20, 24));
		$camposMostrarICMS['T004_Modalidade_ICMS_Substituicao'] = array('CST' => array(2, 12, 4, 10, 11, 14, 18, 19, 20, 24));
		$camposMostrarICMS['T004_ST_VA'] = array('CST' => array(2, 12, 4, 10, 11, 14, 18, 19, 20, 24));
		$camposMostrarICMS['T004_Percentual_Reducao_ICMS_ST'] = array('CST' => array(2, 12, 4, 10, 11, 14, 18, 19, 20, 24));
		$camposMostrarICMS['T004_Percentual_Base_Operacao_Propria'] = array('CST' => array(12, 14));
		$camposMostrarICMS['T004_UF_ICMS_Substituicao_Devido'] = array('CST' => array(12, 14));
		$camposMostrarICMS['T004_Valor_Base_ICMS_Substituicao_Destino'] = array('CST' => array(13));
		$camposMostrarICMS['T004_Valor_ICMS_Substituicao_Destino'] = array('CST' => array(13));
		$camposMostrarICMS['T004_Valor_ICMS_Credito'] = array('CST' => array(15, 18, 24));
		$camposMostrarICMS['T004_Aliquota_ICMS_Credito'] = array('CST' => array(15, 18, 24));
		// $camposMostrarICMS['T004_Preco_Pauta_ST'] = array('CST' => array(2, 12, 4, 10, 11, 14, 18, 19, 20, 24));

		$camposMostrarICMS = json_encode($camposMostrarICMS);
		$calculadoraQtdNavegaTab = (!isset($g['C031']['calculadoraQtdNavegaTab']) || $g['C031']['calculadoraQtdNavegaTab'] !== 'N') ? 'true' : 'false';

		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				var formCalculadoraQuantidadeEditar = $('#{$g['divId']}');
				$(document).unbind('calculadoraQuantidade:resultado.ven001Editar').bind('calculadoraQuantidade:resultado.ven001Editar', function(_, medidas, formOrigemId) {
					if (formOrigemId !== '{$g['divId']}') { return; }
					formCalculadoraQuantidadeEditar.find('#T004A_Altura').val(medidas.altura);
					formCalculadoraQuantidadeEditar.find('#T004A_Largura').val(medidas.largura);
					formCalculadoraQuantidadeEditar.find('#T004A_Comprimento').val(medidas.comprimento);
					formCalculadoraQuantidadeEditar.find('#T004A_Resultado').val(medidas.resultado);
					formCalculadoraQuantidadeEditar.find('#T004A_Quantidade_Comercial').val(medidas.resultado.replace('.', ',')).trigger('change');
					formCalculadoraQuantidadeEditar.find('#T004_Quantidade').val(medidas.resultado.replace('.', ',')).trigger('change');
				});
				$.getScript('/hardness3/static/js/calculadoraQuantidade.js').done(function() {
					configurarCalculadoraQuantidade({participarTab: {$calculadoraQtdNavegaTab}});
					inicializarCalculadoraQuantidade({formulario: formCalculadoraQuantidadeEditar, formId: '{$g['divId']}', campo: formCalculadoraQuantidadeEditar.find('#T004A_Quantidade_Comercial:visible').length ? '#T004A_Quantidade_Comercial' : '#T004_Quantidade', altura: '#T004A_Altura', largura: '#T004A_Largura', comprimento: '#T004A_Comprimento', resultado: '#T004A_Resultado', divIdRoot: '{$g['divIdRoot']}', divIdParent: '{$g['divId']}'});
				});
				mostrarOcultarCamposForm({$camposMostrarICMS}, '#{$g['divId']}', '#T004_D059_Id', 'CST');
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
				// Insert ou Update?
				if (!empty($acaoId)) {
					// Update
					$T004                  = mysql_query("SELECT T004_Valor_Preco_Unitario, T004_T003_Id, T004_D009_Id FROM T004 WHERE T004_Id = '{$acaoId}'");
					$mT004                 = mysql_fetch_array($T004);
					$valorPrecoAntigo 	   = $mT004['T004_Valor_Preco_Unitario'];
					$novoValorPreco        = $form->campoValorEnviado('T004_Valor_Preco_Unitario');
					$novoValorPrecoTratado = gCorrigeNumeroInverte($form->campoValorEnviado('T004_Valor_Preco_Unitario'));

					if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
						//die($sql);
						//Calcula valor do desconto
						if($valorPrecoAntigo != $novoValorPrecoTratado && $rowT003['T003A_Flag_Conversao_Unidade'] != 'S'){
							$VEN001->ven001AplicarPercentualDesconto($acaoId, 'T004_Valor_Preco_Unitario', $valorPrecoAntigo, $novoValorPreco);
						}

						// Se trocar de produto, ele atualiza as aliquotas
						if($form->campoValorEnviado('T004_D009_Id') != $mT004['T004_D009_Id']){
							$VEN001->atualizarAliquotas($mT004['T004_T003_Id'], null, $acaoId);
						}
						
						mysqli_query("CALL T004_Gravar_Totalizacao_4('{$acaoId}')");

						$alert = gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!', false);
						// Imprime uma mensagem e fecha a janela
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								/*$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										{$alert}
									}, 150);
  								});*/
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								// O FW atualiza a linha do grid automaticamente, somente recarregar se não for grid
								if ($('#{$g['divIdParent']}').find('table.grid').length === 0) {
									divRefresh('{$g['divIdParent']}', true);
								}
								divRefresh($('#{$g['divIdParent']}').closest('.ui-tabs-panel').find('.content-2columns:eq(1)').attr('id'), true, false, 2); // totais
							} </script></div>
EOT;
					}
				} else {
					// Insert
					// Nunca será insert neste FORM
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
			$form->campoSetarValor('T004_D059_Id', $D059_Id);
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
	/* @author RubiaYwata
	* @since 16/11/2011
	*/
	case 'ven001FormTotalOrcamento':
	
	
		echo '<label style="font-weight: bold;">Total Orçamento</label>';
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
		$form_tabela = 'T003';
		$form_campo_pk = 'T003_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = '';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '';
		
		$cSql_T004=mysql_query("select T004_Id from T004 where T004_T003_Id='$r_acaoId'");
		while ($mT004=mysql_fetch_array($cSql_T004)) {
		      mysql_query("call T004_Gravar_Totalizacao_4('$mT004[T004_Id]')");
		}
		mysql_query("call T003_Gravar_Totalizacao_4($r_acaoId);");
		
		
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					'IPV_Minimo' => array('name' => 'IPV_Minimo', 'label' => 'Importar Orçamentos', 'type' => 'moeda', 'length' => array(40, 80), 'disabled' => 'true'),
					'T003_Id' => array('name' => 'T003_Id', 'label' => 'S.T.', 'type' => 'hidden'),
					
					'T003_Id_Origem' => array('name' => 'T003_Id_Origem', 'label' => 'Importar Orçamentos', 'type' => 'moeda', 'length' => array(40, 80), 'disabled' => 'true'),
				)
			)
		);
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
			} else if ($formCodigo == 2) {
				// Insert ou Update?
				if (!empty($r_acaoId)) {
					// Update
					if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $r_acaoId, $form)) {
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
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
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

	break;
	
	case 'ven001FormTotalConfirmado':
	
		echo '<label style="font-weight: bold;">Total Confirmado</label></ br></ br>';    

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
		
		
		$form_tabela = 'T003';
		$form_campo_pk = 'T003_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = '';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '';
		//form editor
        $form_editor = array(
            'geral2' => '{"tituloTotal":{"top":4,"left":9,"width":null,"hidden":1},"T003_Valor_Total_Produtos_Sem_Desconto_Confirmado":{"top":30,"left":31,"width":125,"hidden":0},"T003_Valor_Desconto_Confirmado":{"top":53,"left":12,"width":125,"hidden":0},"T003_Valor_Total_Produtos_Confirmado":{"top":76,"left":31,"width":125,"hidden":0},"T003_Valor_ST_Confirmado":{"top":99,"left":56,"width":125,"hidden":0},"T003_Valor_Total_IPI_Confirmado":{"top":122,"left":64,"width":125,"hidden":0},"T003_Valor_Total_Servicos":{"top":145,"left":31,"width":125,"hidden":0},"T003_Valor_Frete":{"top":168,"left":49,"width":125,"hidden":0},"T003_Valor_Despesas_Acessorias":{"top":191,"left":20,"width":125,"hidden":0},"T003_Valor_Troca":{"top":214,"left":40,"width":125,"hidden":0},"T003_Valor_Total_Confirmado":{"top":237,"left":52,"width":125,"hidden":0},"T003_Valor_Total_ICMS_Confirmado":{"top":259.9999694824219,"left":50,"width":125,"hidden":0},"T003_Peso_Total_Confirmado":{"top":282.9999694824219,"left":25,"width":125,"hidden":0}}',
        );
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral2' => array(
					// Campo normal (do banco)
     				'T003_Valor_Total_ICMS_Confirmado' => array('name' => 'T003_Valor_Total_ICMS_Confirmado', 'label' => 'ICMS', 'type' => 'moeda', 'length' => array(40, 80), 'disabled' => 'true'),
					'T003_Valor_ST_Confirmado' => array('name' => 'T003_Valor_ST_Confirmado', 'label' => 'S.T.', 'type' => 'moeda', 'length' => array(40,80), 'disabled' => 'true'),
					'T003_Valor_Total_IPI_Confirmado' => array('name' => 'T003_Valor_Total_IPI_Confirmado', 'label' => 'IPI', 'type' => 'moeda', 'length' => array(40,80), 'disabled' => 'true'),
					'T003_Valor_Total_Produtos_Sem_Desconto_Confirmado' => array('name' => 'T003_Valor_Total_Produtos_Sem_Desconto_Confirmado', 'label' => 'Produtos', 'type' => 'moeda', 'length' => array(40,80), 'disabled' => 'true'),
					'T003_Valor_Frete' => array('name' => 'T003_Valor_Frete', 'label' => 'Frete', 'type' => 'moeda', 'length' => array(40,80), 'disabled' => 'true'),
					'T003_Valor_Despesas_Acessorias' => array('name' => 'T003_Valor_Despesas_Acessorias', 'label' => 'Dp. Acess.', 'type' => 'moeda', 'length' => array(40,80), 'disabled' => 'true'),
					'T003_Valor_Desconto_Confirmado' => array('name' => 'T003_Valor_Desconto_Confirmado', 'label' => 'Desconto R$', 'type' => 'moeda', 'length' => array(40,80)),
					'T003_IPV_Confirmado' => array('name' => 'T003_IPV_Confirmado', 'label' => 'IPV', 'type' => 'moeda', 'length' => array(40,80), 'disabled' => 'true'),
					'T003_Peso_Total_Confirmado' => array('name' => 'T003_Peso_Total_Confirmado', 'label' => 'Peso Total', 'type' => 'text', 'length' => array(40,80), 'disabled' => 'true'),
					'T003_Valor_Total_Confirmado' => array('name' => 'T003_Valor_Total_Confirmado', 'label' => 'Total', 'type' => 'moeda', 'length' => array(40,80), 'disabled' => 'true'),
				)
			)
		);
        $form = new Formulario('form-ven001FormTotais2');
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
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
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
		

	break;
	
	case 'ven001FormAplicarIpv':
	
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
		$form_tabela = 'T003';
		$form_campo_pk = 'T003_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = '';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '';
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					'T003_Id' => array('name' => 'T003_Id', 'label' => 'T003_Id', 'type' => 'hidden', 'length' => array(40, 80), 'reject' => true, 'value' => $r_T003_Id),
     				'IPV' => array('name' => 'IPV', 'label' => 'Aplicar IPV', 'type' => 'moeda', 'length' => array(40, 80), 'reject' => true),
				)
			)
		);
        $form = new Formulario('form-ven001FormTotais2');
		$form->definirFormularioArray($formArray);
         
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
	
	case 'ven001FormAplicarDesconto':
  		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;
		$tabelaCabecalho = (isset($r_tabelaCabecalho)) ? $r_tabelaCabecalho : false;
		$idTabelaCabecalho = (isset($r_idTabelaCabecalho)) ? $r_idTabelaCabecalho : false;

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
		$form_tabela = $tabelaCabecalho;
		$form_campo_pk = $tabelaCabecalho . '_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'ven001AplicarDescontoBtnExecutar';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/ven/ven001/form/ven001FormAplicarDesconto/';

		//form editor
		$form_editor = array('geral' =>'{"desconto":{"top":7,"left":30.578125,"width":136,"hidden":0},"campoAplicarDesconto":{"top":30,"left":15.28125,"width":144,"hidden":0}}');
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
     				'desconto' => array('name' => 'desconto', 'label' => 'Desconto (%)', 'type' => 'moeda', 'length' => array(30, 80), 'reject' => true, 'casasDecimais' => 10),
     				'campoAplicarDesconto' => array('name' => 'campoAplicarDesconto', 'label' => 'Desconto Base', 'type' => 'select', 'reject' => true, 'select' => 
						array(
							array('title' => 'Preço de tabela', 'value' => 'tabela', 'selected'=>true),
							array('title' => 'Preço do pedido', 'value' => 'pedido')
						)
					)
				)
			)
		);
        $form = new Formulario('form-ven001FormAplicarDesconto');
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
				$lote = 10;

				echo <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
						var \$buttons = {
							"Sim": function() {
								var campoAplicarDesconto = $('#{$g['divId']} #campoAplicarDesconto').val();
								var desconto = $('#{$g['divId']} #desconto').val();
								$('#{$g['divId']}').showLoading();
								$.getJSON('/ven/ven001/form_func-ajax/ven001aplicarDesconto/?ajax=true&tabelaCabecalho=' + encodeURIComponent('{$tabelaCabecalho}') + '&idTabelaCabecalho=' + encodeURIComponent('{$idTabelaCabecalho}')  + '&tabelaProduto=' + encodeURIComponent('{$r_tabelaProduto}') + '&desconto=' + encodeURIComponent(desconto) + '&campoAplicardesconto=' + encodeURIComponent(campoAplicarDesconto) + '&PDV=' + encodeURIComponent('{$r_PDV}') + '&lote={$lote}&callback=?', function(request) {
									if (request.code && request.data == 'jsonEtapas') {
										var p = typeof request.json === 'string' ? JSON.parse(request.json) : request.json;
										var idProgressBar = dialogAlert('Aplicar Desconto', "<span class='progressBarDesconto'>Processando 0%</span>");
										function finalizarDesconto(sucesso, mensagem) {
											$('#dc-' + idProgressBar).dialog('destroy').remove();
											$('#{$g['divId']}').hideLoading();
											if (sucesso) {
												divRefresh('{$g['divIdParent']}');
												fecharJanela('{$g['divId']}');
											}
											dialogConfirm(sucesso ? 'Ok' : 'Erro', mensagem);
										}
										function processarDesconto(inicio) {
											$.getJSON('/sistema/outros/grid_func-ajax/ajaxEtapas/?ajax=true&inicio=' + inicio + '&classe=' + encodeURIComponent(p.classe) + '&metodo=' + encodeURIComponent(p.metodo) + '&sql=' + encodeURIComponent(p.sql) + '&lote=' + encodeURIComponent(p.lote) + '&callback=?', function(resposta) {
												if (!resposta.code) {
													finalizarDesconto(false, resposta.erros || resposta.data || 'Erro ao aplicar desconto.');
													return;
												}
												if (resposta.concluido == 1) {
													finalizarDesconto(true, 'Desconto aplicado com sucesso!');
													return;
												}
												$('#dc-' + idProgressBar).find('.progressBarDesconto').text('Processando ' + resposta.percentual + '% (' + resposta.ok + '/' + resposta.totalRegistros + ' itens)');
												processarDesconto(0);
											}).fail(function(xhr) {
												finalizarDesconto(false, 'Erro HTTP ' + xhr.status + ' ao aplicar desconto. Confira o orçamento antes de tentar novamente.');
											});
										}
										processarDesconto(1);
										return;
									}

									if (request.code) {
										var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					    				dialogConfirm('Ok', request.data, \$buttons);
					    				divRefresh('{$g['divIdParent']}');
					    				fecharJanela('{$g['divId']}');
									} else {
										var \$buttons = { 
											"Fechar": function() { 
													$(this).dialog("destroy").remove(); 
													setTimeout(function() { 
														$('#desconto').focus();
														$('#desconto').select();
													}, 150); 
											}, 
											"Pedir autorização": function() { 
													$(this).dialog("destroy").remove(); 
													abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Autorização', '/sistema/funcoes/autoriza/content/', '&tabela=T007&tabelaId={$idTabelaCabecalho}&descricao=Desconto&valor=' + encodeURIComponent(desconto) + '&divForm={$g['divId']}', [400,300]);
											} 
										};
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
		$botoesMid = '';
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="ven001AplicarDescontoBtnExecutar">Executar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;
	
	case 'ven001FormAplicarDescontoReais':
  		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;
		$tabelaCabecalho = (isset($r_tabelaCabecalho)) ? $r_tabelaCabecalho : false;
		$idTabelaCabecalho = (isset($r_idTabelaCabecalho)) ? $r_idTabelaCabecalho : false;

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
		$form_tabela = $tabelaCabecalho;
		$form_campo_pk = $tabelaCabecalho . '_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'ven001AplicarDescontoReaisBtnExecutar';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/ven/ven001/form/ven001FormAplicarDescontoReais/';

		//form editor
		$form_editor = array('geral' =>'{"descontoReais":{"top":7,"left":5,"width":178,"hidden":0}}');
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
     				'descontoReais' => array('name' => 'descontoReais', 'label' => 'Valor (R$)', 'type' => 'moeda', 'length' => array(30, 80), 'reject' => true, 'casasDecimais' => 2)
				)
			)
		);
        $form = new Formulario('form-ven002FormAplicarDesconto');
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
								var descontoReais = $('#{$g['divId']} #descontoReais').val();
								$('#{$g['divId']}').showLoading();
								$.getJSON('/ven/ven001/form_func-ajax/ven001aplicarDescontoReais/?ajax=true&tabelaCabecalho=' + encodeURIComponent('{$tabelaCabecalho}') + '&idTabelaCabecalho=' + encodeURIComponent('{$idTabelaCabecalho}')  + '&tabelaProduto=' + encodeURIComponent('{$r_tabelaProduto}') + '&descontoReais=' + encodeURIComponent(descontoReais) + '&callback=?', function(request) {
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
		$g['smarty']->display('formEditor.tpl');

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = '';
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="ven001AplicarDescontoReaisBtnExecutar">Executar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'ven001FormPagamentos':
		global $g;
		$acaoId = (isset($r_acaoId)) ? $r_acaoId : false;
		$T003_Id = (isset($r_T003_Id)) ? $r_T003_Id : false;

		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado

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
		$form_tabela = 'T003';
		$form_campo_pk = 'T003_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'ven001FormPagamento_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/ven/ven001/form/ven001FormPagamentos/';

        //form editor
        $form_editor = array(
            'geral' => '{"T003_Pgto_Primeiro_Vcto_Valor_Data":{"top":30,"left":176,"width":72,"hidden":0},"T003_Pgto_Parcelas":{"top":7,"left":31.078125,"width":176,"hidden":0},"T003_Flag_Frete_Primeira_Parcela":{"top":7,"left":270.28125,"width":57,"hidden":0},"T003_Pgto_Primeiro_Vcto_Tipo":{"top":30,"left":33.171875,"width":96,"hidden":0},"T003_Pgto_Primeiro_Vcto_Valor":{"top":30,"left":176,"width":72,"hidden":0},"T003_Flag_ST_Primeira_Parcela":{"top":30,"left":281.90625,"width":57,"hidden":0},"T003_Observacao_Pagamento":{"top":30,"left":637,"height":31,"width":168,"hidden":1},"T003_Pgto_Dias_Vcto_Tipo":{"top":53,"left":21.296875,"width":97,"hidden":0},"T003_Pgto_Dias_Vcto_Valor":{"top":53,"left":176,"width":72,"hidden":0},"T003_Flag_IPI_Primeira_Parcela":{"top":53,"left":282.515625,"width":57,"hidden":0},"T003_D027_Id":{"top":76,"left":31.6875,"width":175,"hidden":0},"ven001FormPagamento_btn":{"top":74,"left":336,"width":null,"hidden":0},"ven001FormAdicionarParcela_btn":{"top":74,"left":752,"width":null,"hidden":1},"ven001SalvarObservacao_btn":{"top":97,"left":752,"width":null,"hidden":1}}',
        );

		// novo formulário
		$form = new Formulario('ven001FormPagamento');

		$VEN001->verificaPgtoPorCurvaCliente($T003_Id, false);

		$dadosTabela = array('tabela' => $form_tabela, 'campos' => 'T003_Valor_Total as valorTotal,T003_Pgto_Parcelas as pgtoParcelas', 'where' => $form_campo_pk."='{$T003_Id}'" );
		$select = $VEN001->populaComboPgtoParcelas($dadosTabela);
		$CNPJ_Empresa_Corrente=substr($g['C004']['C004_Cnpj'], 0, -8);
		
		$T003 = mysql_query("SELECT D024_D027_Id FROM T003 LEFT JOIN D024 ON D024_Id = T003_D024_Id WHERE T003_Id = {$T003_Id}");
		$mT003 = mysql_fetch_array($T003);
		$disabledPortador = $g["C031"]["desabilitarEdicaoPortador"] == 'S' && !empty($mT003['D024_D027_Id']) && $g['nivelAlcada'] != 4;

		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T003_Pgto_Parcelas' => array('name' => 'T003_Pgto_Parcelas', 'label' => 'Parcelas', 'type' => 'select', 'select' => $select,'uppercase'=>false),
					'T003_Pgto_Primeiro_Vcto_Tipo' => array('name' => 'T003_Pgto_Primeiro_Vcto_Tipo', 'label'=>'1º Venc.', 'type' => 'select','select' =>
						array(
							array('title' => 'Dias', 'value' => 'DIAS'),
							array('title' => 'Data', 'value' => 'DATA'),
						)
					),
					'T003_Pgto_Primeiro_Vcto_Valor' => array('name' => 'T003_Pgto_Primeiro_Vcto_Valor', 'type' => 'text','label' => '',  'length' => array(3,7), 'align' => 'right'),
					'T003_Pgto_Primeiro_Vcto_Valor_Data' => array('name' => 'T003_Pgto_Primeiro_Vcto_Valor_Data', 'label' => '', 'type' => 'data', 'align' => 'right'),
					'T003_Pgto_Dias_Vcto_Tipo' => array('name' => 'T003_Pgto_Dias_Vcto_Tipo', 'label'=>'Dias Venc.', 'type' => 'select','select' =>
						array(
							array('title' => 'Intervalo', 'value' => 'INTERVALO'),
							array('title' => 'Dia Fixo', 'value' => 'DIA FIXO'),
						)
					),
					'T003_Pgto_Dias_Vcto_Valor' => array('name' => 'T003_Pgto_Dias_Vcto_Valor', 'label' => '', 'type' => 'text', 'length' => array(3,6), 'align' => 'right'),
					'T003_D027_Id' => array('name' => 'D027_Id','label'=>'Portador', 'type' => 'select', 'disabled' => $disabledPortador, 'select' =>
						gGeraSelect('D027', 'D027_Id', 'concat(D027_Portador," ",if(D027_Acrescimo_IPV>0,concat("+",D027_Acrescimo_IPV,"%"),"")) as D027_Portador','LEFT JOIN D007 ON D007_Id = D027_D007_Id LEFT JOIN C004 ON C004_Id = D007_C004_Id WHERE D027_Flag_Ativo = "S" AND D027_Flag_Exibe_CRM = "S" AND ((D007_Flag_Layout="1" and D007_Flag_Ativo="S" AND C004_Cnpj LIKE "%'.$CNPJ_Empresa_Corrente.'%") OR ifnull(D027_D007_Id,0) = 0) order by D027_Portador', true, $mT003['T003_D027_Id'])
					),
					'T003_Flag_Frete_Primeira_Parcela' => array('name' => 'T003_Flag_Frete_Primeira_Parcela', 'label'=>'Frete primeira parcela', 'type' => 'select','select' =>
						array(
							array('title' => '...', 'value' => ''),
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N'),
						)
					),
					'T003_Flag_ST_Primeira_Parcela' => array('name' => 'T003_Flag_ST_Primeira_Parcela', 'label'=>'ST primeira parcela', 'type' => 'select','select' =>
						array(
							array('title' => '...', 'value' => ''),
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N'),
						)
					),
					'T003_Flag_IPI_Primeira_Parcela' => array('name' => 'T003_Flag_IPI_Primeira_Parcela', 'label'=>'IPI primeira parcela', 'type' => 'select','select' =>
						array(
							array('title' => '...', 'value' => ''),
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N'),
						)
					),
					'ven001FormPagamento_btn' => array('name' => 'ven001FormPagamento_btn', 'type'=>'submit','reload'=>false,'value'=>'Gerar Parcelas','align' => 'right', 'reject'=>true),
					'ven001FormAdicionarParcela_btn' => array('name' => 'ven001FormAdicionarParcela_btn', 'type'=>'submit', 'reload' => false, 'value' => 'Adicionar Parcela', 'align' => 'right', 'reject' => true),
					'T003_Observacao_Pagamento' => array('name' => 'T003_Observacao_Pagamento', 'label' => 'Observação', 'type' => 'textarea', 'length' => array(1,23)),
					'ven001SalvarObservacao_btn' => array('name' => 'ven001SalvarObservacao_btn', 'type'=>'submit', 'reload' => false, 'value' => 'Salvar Observação', 'align' => 'right', 'reject' => true),
					'T003_Id' => array('name' => 'T003_Id', 'label' => '', 'type' => 'hidden', 'value' => @$r_T003_Id, 'reject' => true),
				)
			)
		);

		$form->definirFormularioArray($formArray);
		
		echo <<<EOT
		    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		    	$('#ven001FormAdicionarParcela_btn').unbind('click');
				$('#ven001FormAdicionarParcela_btn').unbind('click').bind('click', function(data) {
					var id = '{$row['T003_D024_Id']}';
						$.getJSON('/ven/ven001/form_func-ajax/inserirParcela/?ajax=true&T003_Id=' + encodeURIComponent('{$T003_Id}') + '&form_tabela=' + encodeURIComponent('{$form_tabela}') + '&form_campo_pk=' + encodeURIComponent('{$form_campo_pk}') + '&callback=?', function(request) {
							if (request.code) {
								divRefresh("{$r_divReload}");
							} else {
								var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
    							dialogConfirm("Erro!", request.data, buttons)
							}
						});
					return false;
				});
				$('#ven001SalvarObservacao_btn').unbind('click').bind('click', function(data) {
					$.getJSON('/ven/ven001/form_func-ajax/salvarObservacao/?ajax=true=' + '&' + $("#{$formId}").serialize() + '&T003_Id=' + encodeURIComponent('{$T003_Id}') + '&callback=?', function(request) {
						if (request.code) {
							divRefresh('{$g['divId']}');
						} else {
							var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
							dialogConfirm("Erro!", request.data, buttons)
						}
					});
					return false;
				});
				
				$('#{$g['divId']} #T003_Pgto_Primeiro_Vcto_Tipo').change(function() {
					mudaCampoVencimento($(this).val());
				});

				mudaCampoVencimento($('#{$g['divId']} #T003_Pgto_Primeiro_Vcto_Tipo').val())

				function mudaCampoVencimento(parametro){
					if (parametro == 'DIAS') {
						$('#{$g['divId']} #T003_Pgto_Primeiro_Vcto_Valor_Data').val('').hide();
						$('#{$g['divId']} #T003_Pgto_Primeiro_Vcto_Valor').show();
						$('#{$g['divId']} #T003_Pgto_Primeiro_Vcto_Valor').focus();
					} else {
						$('#{$g['divId']} #T003_Pgto_Primeiro_Vcto_Valor').val('').hide();
						$('#{$g['divId']} #T003_Pgto_Primeiro_Vcto_Valor_Data').show();
						$('#{$g['divId']} #T003_Pgto_Primeiro_Vcto_Valor_Data').focus();
					}
				}
				$('#{$g['divId']} #T003_Pgto_Primeiro_Vcto_Tipo').trigger('change');							
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

				$D027_Id=$form->campoValorEnviado('T003_D027_Id');

				//Ao escolher um portador Que tenha algo uma Taxa de Acrescimo IPV(seja diferente de 0) esse valor da taxa vai ser inserido no T250 do orçamento especifico
				$D027 = mysql_query("SELECT D027_Acrescimo_IPV FROM D027 WHERE D027_Id={$D027_Id}");
				$rD027 = mysql_fetch_array($D027);
				log("Aliquota: ".$rD027['D027_Acrescimo_IPV']);
				if($rD027['D027_Acrescimo_IPV']>0){
					log("O Acrescimo IPV é maior que 0");
					log("T003_Id:".$T003_Id);
					$T250 = mysql_query("Select T250_Id,T250_Aliquota,T250_Flag_Altera_Preco from T250 where T250_T003_Id='$T003_Id' and T250_Tipo='5' and T250_Flag_Campo_Calculado='S'");
					$mT250=mysql_fetch_array($T250);
					$T250_Id=$mT250['T250_Id'];

					if(number_format($mT250['T250_Aliquota'],2)!=number_format($rD027['D027_Acrescimo_IPV'],2)){
						mysql_query("Update T250 set T250_Aliquota='{$rD027['D027_Acrescimo_IPV']}' where T250_Id='{$T250_Id}'");
						$erro=mysql_error();
						log("erro: ".$erro);
						$VEN001->atualizarPrecoTabela($T003_Id);

						if($mT250['T250_Flag_Altera_Preco']=='S'){
							$msg = "Foi acrescido ".$rD027['D027_Acrescimo_IPV']."% no preço tabela desse orçamento.";
						}else{
							$msg = "Foi acrescido ".$rD027['D027_Acrescimo_IPV']."% ao demonstrativo IPV.";
						}
						}

					}else{
					$T250 = mysql_query("Select T250_Id,T250_Aliquota,T250_Flag_Altera_Preco from T250 where T250_T003_Id='$T003_Id' and T250_Tipo='5' and T250_Flag_Campo_Calculado='S'");
					$mT250=mysql_fetch_array($T250);
					$T250_Id=$mT250['T250_Id'];

					if($mT250['T250_Aliquota']>0){
						mysql_query("Update T250 set T250_Aliquota='0' where T250_Id='{$T250_Id}'");
						$erro=mysql_error();
						log("erro: ".$erro);
						$VEN001->atualizarPrecoTabela($T003_Id);

						if($mT250['T250_Flag_Altera_Preco']=='S'){
							$msg = "Foi deduzido ".$mT250['T250_Aliquota']."% no preço tabela desse orçamento, devido a mudança do portador.";
						}else{
							$msg = "Foi deduzido ".$mT250['T250_Aliquota']."% ao demonstrativo IPV, devido a mudança do portador.";
						}
					}
				}

				// Update dos campos do T003
				if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $r_acaoId, $form)) {

					//ARRAY UTILIZADO NA FUNÇÃO ATUALIZAR PARCELAS
					$dadosTabela = array('tabela' => $form_tabela, 
										 'tabelaId' => 	$T003_Id,
										 'campos' => 'T003_Pgto_Primeiro_Vcto_Tipo as primeiroVencimentoTipo,
	                                         T003_Pgto_Primeiro_Vcto_Valor as primeiroVencimentoNum,
	                                         T003_Pgto_Primeiro_Vcto_Valor_Data as primeiroVencimentoData,
	                                         T003_Pgto_Dias_Vcto_Tipo as diasVctoTipo,
	                                         T003_Pgto_Dias_Vcto_Valor as valorIntervaloDiaFixo,
	                                         T003_D027_Id as D027_Id,
	                                         T003_Flag_Frete_Primeira_Parcela as campoUsarFreteParcela,
	                                         T003_Flag_ST_Primeira_Parcela as campoUsarSTParcela,
	                                         T003_Flag_IPI_Primeira_Parcela as campoUsarIPIParcela,
	                                         T003_Pgto_Parcelas as Total_Parcelas,
	                                         T003_Valor_Total_IPI as valorTotalIPI,
	                                         T003_Valor_Total as valorTotal,
	                                         T003_Valor_Frete as valorTotalFrete,
	                                         T003_Valor_ST as valorTotalST',
                                         'where' => $form_campo_pk."='{$T003_Id}'", 
                                         'tabelaPgto' => 'T089', 
                                         'camposPgto' => 'T089_Prazos_Dias as prazoDias,T089_D027_Id as D027Pagamento,T089_Numero_Parcela as numeroParcela', 
                                         'wherePgto' => "T089_T003_Id='{$T003_Id}'");
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
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

		/**
		 * PARTE 5: Exibir o formulário
		 */
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');

		$sqlD024 = mysql_query("SELECT D024_Instrucao_Pagamento, D024_Prazo_Parcela, D024_Dias_Vencimento, D024_Quantidade_Parcelas, D027_Portador FROM D024 LEFT JOIN D027 ON D027_Id=D024_D027_Id LEFT JOIN T003 ON T003_D024_Id = D024_Id WHERE T003_Id = '{$T003_Id}'");
		$D024 = mysql_fetch_array($sqlD024);
		$obs='';
		$obs .=	empty($D024['D024_Prazo_Parcela']) 				? '' : " 1º Venc: ".$D024['D024_Prazo_Parcela']."<br />";
		$obs .= empty($D024['D024_Dias_Vencimento']) 			? '' : " Dia Fixo/Intervalo: ".$D024['D024_Dias_Vencimento']."<br />";
		$obs .= empty($D024['D024_Quantidade_Parcelas']) 		? '' : " Nº Parcelas: ".$D024['D024_Quantidade_Parcelas']."<br />";
		$obs .= empty($D024['D027_Portador']) 					? '' : " Portador: ".$D024['D027_Portador']."<br />";
		echo <<<EOT
			<div style="border: 1px solid silver; margin: 7px 15px 0 485px; padding: 5px; height: 100px; border-radius:5px; color: var(--BGVermelho); overflow: auto;">
				<span style="font-weight: bold; color: #000;">Informação de Pagamento Cliente</span><br> {$obs}{$D024['D024_Instrucao_Pagamento']}	
			</div>	
EOT;

	break;

	case 'xven001FormPagamentos':
		global $g;
		$acaoId = (isset($r_acaoId)) ? $r_acaoId : false;
		$T003_Id = (isset($r_T003_Id)) ? $r_T003_Id : false;

		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado

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
		$form_tabela = 'T003';
		$form_campo_pk = 'T003_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'ven001FormPagamento_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/ven/ven001/form/ven001FormPagamentos/';

        //form editor
        $form_editor = array(
            'geral' => '{"Total_Parcelas":{"top":7,"left":31.078125,"width":176,"hidden":0},"usarFreteParcela":{"top":7,"left":271,"width":59,"hidden":0},"selecionaPrimeiroVencimento":{"top":30,"left":33.171875,"width":96,"hidden":0},"primeiroVencimentoData":{"top":30,"left":176,"width":72,"hidden":0},"primeiroVencimentoNum":{"top":30,"left":176,"width":72,"hidden":0},"usarSTParcela":{"top":30,"left":283,"width":59,"hidden":0},"T003_Observacao_Pagamento":{"top":30,"left":637,"height":31,"width":168,"hidden":1},"selecionaDias":{"top":53,"left":21.296875,"width":96,"hidden":0},"IntervalorDias":{"top":53,"left":176,"width":72,"hidden":0},"diaFixo":{"top":53,"left":176,"width":72,"hidden":0},"usarIPIParcela":{"top":53,"left":284,"width":59,"hidden":0},"Portador_D027_Id":{"top":76,"left":24.359375,"width":176,"hidden":0},"ven001FormAdicionarParcela_btn":{"top":74,"left":752,"width":null,"hidden":1},"ven001FormPagamento_btn":{"top":75,"left":300,"width":null,"hidden":0},"ven001SalvarObservacao_btn":{"top":97,"left":752,"width":null,"hidden":1}}',
        );

		// novo formulário
		$form = new Formulario('ven001FormPagamento');

		$select = array();
		$select[] = array('value' => '0', 'title' => 'A COMBINAR');
		// pega as configurações de juros da empresa atual
		$C004=mysql_query("select * from C004 where C004_Id='{$g['empresaAtual']}'");
		$mC004=mysql_fetch_array($C004);
		$CNPJ_Empresa_Corrente = substr($mC004['C004_Cnpj'], 0, -8);

		$tipoJuros=$mC004['C004_Tipo_Juros'];
		$taxaJuros=$mC004['C004_Taxa_Juros'];
		$parcelasIsentas=$mC004['C004_Parcelas_Isentas_Juros'];
		$T003  = mysql_query("SELECT * FROM T003 LEFT JOIN D027 ON D027_Id=T003_Pgto_Portador LEFT JOIN D024 ON D024_Id=T003_D024_Id WHERE T003_Id='{$T003_Id}'");
		$mT003 = mysql_fetch_array($T003);

		//SELECIONANDO OS SERVIÇOS DESSE ORÇAMENTO PARA CALCULAR O TOTAL
		$sql = "SELECT * FROM T145 where T145_T003_Id='{$T003_Id}'";
		$T145_sql = mysql_query($sql);

		// LOOPING PARA CALCULAR O TOTAL DE SERVIÇOS CONFIRMADOS PARA ESSE ORÇAMENTO
		$totalServicos=0;
		while($T145 = mysql_fetch_array($T145_sql)){
			$totalServicos+=$T145['T145_Quantidade_Confirmada'] * $T145['T145_Valor_Unitario'];
		}

		if($mT003['T003_Pgto_Parcelas'] <= 0 && $mT003['D024_Quantidade_Parcelas'] > 0){
			$mT003['T003_Pgto_Parcelas'] = $mT003['D024_Quantidade_Parcelas'];
		}else if($mT003['T003_Pgto_Parcelas'] <= 0 && $mT003['D024_Quantidade_Parcelas'] <= 0){
			$mT003['T003_Pgto_Parcelas'] = $g['C031']['numeroParcelasPadrao'];
		}

		if($mT003['T003_Pgto_Portador'] <= 0 && $mT003['D024_D027_Id'] > 0){
			$mT003['T003_Pgto_Portador'] = $mT003['D024_D027_Id'];
		}else if($mT003['T003_Pgto_Portador'] <= 0 && $g['C031']['D027portadorEmpresa'] > 0){
			$mT003['T003_Pgto_Portador']=$g['C031']['D027portadorEmpresa'];
		}

		//SOMANDO O TOTAL DO ORÇAMENTO COM O TOTAL DOS SERVIÇOS
		$valorTotalOrcamento = $totalServicos + $mT003['T003_Valor_Total'];
		for ($i=1; $i<=$g['C031']['numeroMaxParcelas']; $i++) {
			$valorParcela = $VEN001->calcularParcelaCombo($i,$valorTotalOrcamento,$tipoJuros,$taxaJuros,$parcelasIsentas);
			if ($i>$parcelasIsentas){
				$valorTotal = gCorrigeNumero($i*gCorrigeNumeroInverte($valorParcela));
				$valorParcela.="*";
			}else{
				$valorTotal = gCorrigeNumero($valorTotalOrcamento);
			}
			if($i == $mT003['T003_Pgto_Parcelas']){
				$select[] = array('value' => $i, 'title' => str_pad($i,2,"0",STR_PAD_LEFT) . " x" . " - " .$valorParcela." = ".gCorrigeNumero($valorTotalOrcamento), 'selected'=>true);
			}else{
				$select[] = array('value' => $i, 'title' => str_pad($i,2,"0",STR_PAD_LEFT) . " x" . " - " .$valorParcela." = ".gCorrigeNumero($valorTotalOrcamento));
			}
		}

		if(empty($mT003['D024_Prazo_Parcela'])){
			$mT003['D024_Prazo_Parcela'] = $g['C031']['primeiroVencimentoNum'];
		}
		if(empty($mT003['D024_Dias_Vencimento'])){
			$mT003['D024_Dias_Vencimento'] = $g['C031']['IntervalorDias'];
		}

		//Define valor do campo 1° Venc. Dias e 1° Venc. Data
		if(($mT003['T003_Pgto_Primeiro_Vcto_Valor'] != "") || (!empty($mT003['T003_Pgto_Primeiro_Vcto_Valor']))){
			$primeiroVctoValor = $mT003['T003_Pgto_Primeiro_Vcto_Valor'];
		}else{
			$primeiroVctoValor = $mT003['D024_Prazo_Parcela'];
		}
		if(($mT003['T003_Pgto_Dias_Vcto_Valor'] != "") || (!empty($mT003['T003_Pgto_Dias_Vcto_Valor']))){
			$diaVctoValor = $mT003['T003_Pgto_Dias_Vcto_Valor'];
		}else{
			$diaVctoValor = $mT003['D024_Dias_Vencimento'];
		}

		if(($mT003['T003_Pgto_Data'] != "") || (!empty($mT003['T003_Pgto_Data']))){
			$pgtoData = $mT003['T003_Pgto_Data'];
		}else{
			$pgtoData = "";
		}

		if(empty($mT003['T003_Pgto_Dias_Vcto_Tipo'])){
			$mT003['T003_Pgto_Dias_Vcto_Tipo'] = $g['C031']['tipoIntervalodias'];
		}

		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'Total_Parcelas' => array('name' => 'Total_Parcelas', 'label' => 'Parcelas', 'type' => 'select', 'select' => $select,'uppercase'=>false, 'reject' => true),
					//'valorParcelaSimulacao' => array('name' => 'valorParcelaSimulacao', 'label' => '', 'type' => 'text','extra'=>'style="background:#E8E8E8"','reject' => true, 'length' => array(3,6), 'align' => 'right', 'readonly'=>'readonly'),
					'selecionaPrimeiroVencimento' => array('name' => 'selecionaPrimeiroVencimento', 'label'=>'1º Venc.', 'reject'=>true, 'type' => 'select','select' =>
					array(
						array('title' => 'Dias', 'value' => 'Dias', 'selected' => ($mT003['T003_Pgto_Primeiro_Vcto_Tipo'] == "DIAS") ? true : false),
						array('title' => 'Data', 'value' => 'Data', 'selected' => ($mT003['T003_Pgto_Primeiro_Vcto_Tipo'] == "DATA") ? true : false),
					)),
					'primeiroVencimentoNum' => array('name' => 'primeiroVencimentoNum', 'label' => '', 'value'=> $primeiroVctoValor, 'type' => 'text', 'reject' => true, 'length' => array(3,7), 'align' => 'right'),
					'primeiroVencimentoData' => array('name' => 'primeiroVencimentoData', 'label' => '', 'value'=> $primeiroVctoValor,  'type' => 'data', 'reject' => true, 'align' => 'right'),
					'selecionaDias' => array('name' => 'selecionaDias', 'label'=>'Dias Venc.', 'type' => 'select', 'reject'=>true,'select' =>
					array(
						array('title' => 'Intervalo', 'value' => 'I', 'selected' => ($mT003['T003_Pgto_Dias_Vcto_Tipo'] == "INTERVALO") ? true : false),
						array('title' => 'Dia Fixo', 'value' => 'F', 'selected' => ($mT003['T003_Pgto_Dias_Vcto_Tipo'] == "DIA FIXO") ? true : false),
					)),
					'IntervalorDias' => array('name' => 'IntervalorDias', 'label' => '', 'value'=>$diaVctoValor, 'type' => 'text', 'reject' => true, 'length' => array(3,6), 'align' => 'right'),
					'diaFixo' => array('name' => 'diaFixo', 'label' => '', 'type' => 'text', 'value'=>$diaVctoValor, 'reject' => true, 'length' => array(3,6), 'align' => 'right'),
					'Portador_D027_Id' => array('name' => 'D027_Id','label'=>'Portador', 'reject'=>true, 'type' => 'select', 'select' =>
						gGeraSelect('D027', 'D027_Id', 'concat(D027_Portador," ",if(D027_Acrescimo_IPV>0,concat("+",D027_Acrescimo_IPV,"%"),"")) as D027_Portador','LEFT JOIN D007 ON D007_Id = D027_D007_Id LEFT JOIN C004 ON C004_Id = D007_C004_Id WHERE (D007_Flag_Layout="1" and D007_Flag_Ativo="S" AND C004_Cnpj LIKE "%'.$CNPJ_Empresa_Corrente.'%") OR ifnull(D027_D007_Id,0) = 0 order by D027_Portador', true, $mT003['T003_Pgto_Portador'])
					),
					'ven001FormPagamento_btn' => array('name' => 'ven001FormPagamento_btn', 'type'=>'submit','reload'=>false,'value'=>'Gerar Parcelas','align' => 'right', 'reject'=>true),
					'ven001FormAdicionarParcela_btn' => array('name' => 'ven001FormAdicionarParcela_btn', 'type'=>'submit', 'reload' => false, 'value' => 'Adicionar Parcela', 'align' => 'right', 'reject' => true),
					'T003_Observacao_Pagamento' => array('name' => 'T003_Observacao_Pagamento', 'label' => 'Observação', 'type' => 'textarea', 'length' => array(1,23)),
					'ven001SalvarObservacao_btn' => array('name' => 'ven001SalvarObservacao_btn', 'type'=>'submit', 'reload' => false, 'value' => 'Salvar Observação', 'align' => 'right', 'reject' => true),
					'T003_Id' => array('name' => 'T003_Id', 'label' => '', 'type' => 'hidden', 'value' => @$r_T003_Id, 'reject' => true),
				)
			)
		);

		$formArray['campos']['geral']['usarFreteParcela'] = array('name' => 'usarFreteParcela', 'label'=>'Frete primeira parcela', 'reject'=>true, 'type' => 'select','select' => 
																array(
																	array('title' => '...', 'value' => 'off'),
																	array('title' => 'Sim', 'value' => 'on'),
																	array('title' => 'Não', 'value' => 'off'),
																)
															);
		//--------------------------------------------------------------------------------------------------------
		// Data: 31/08/2018
		// Programador: OSNI
		// Método: ven001FormPagamentos
		// Chamado: 33004
		// Descricao: Valor que é definido no cadastro do cliente e é repassado automaticamente a tab de Orçamento (ST = 'ON')
		// --------------------------------------------------------------------------------------------------------

		$formArray['campos']['geral']['usarSTParcela'] = array('name' => 'usarSTParcela', 'label'=>'ST primeira parcela', 'reject'=>true, 'type' => 'select','select' => 
																array(
																	array('title' => '...', 'value' => 'off'),
																	array('title' => 'Sim', 'value' => 'on', 'selected' => ($mT003['D024_Cobrar_ST_Primeira_Parcela'] == 'S') ? true : false),
																	array('title' => 'Não', 'value' => 'off'),
																)
															);

		// --------------------------------------------------------------------------------------------------------
		// FIM - OSNI - 33004 - ven001FormPagamentos
		// --------------------------------------------------------------------------------------------------------
		
		$formArray['campos']['geral']['usarIPIParcela'] = array('name' => 'usarIPIParcela', 'label'=>'IPI primeira parcela', 'reject'=>true, 'type' => 'select','select' => 
																array(
																	array('title' => '...', 'value' => 'off'),
																	array('title' => 'Sim', 'value' => 'on'),
																	array('title' => 'Não', 'value' => 'off'),
																)
															);
 
		$form->definirFormularioArray($formArray);
		
		echo <<<EOT
		    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		    	$('#ven001FormAdicionarParcela_btn').unbind('click');
				$('#ven001FormAdicionarParcela_btn').unbind('click').bind('click', function(data) {
					var id = '{$row['T003_D024_Id']}';
						$.getJSON('/ven/ven001/form_func-ajax/inserirParcela/?ajax=true&' + $("#{$formId}").serialize() + '&callback=?', function(request) {
							if (request.code) {
								divRefresh("{$r_divReload}");
							} else {
								var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
    							dialogConfirm("Erro!", request.data, buttons)
							}
						});
					return false;
				});
				$('#ven001SalvarObservacao_btn').unbind('click').bind('click', function(data) {
					$.getJSON('/ven/ven001/form_func-ajax/salvarObservacao/?ajax=true=' + '&' + $("#{$formId}").serialize() + '&T003_Id=' + encodeURIComponent('{$T003_Id}') + '&callback=?', function(request) {
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
					$.getJSON('/ven/ven001/form_func-ajax/calcularParcelaSimulacao/?ajax=true=' + '&' + $("#{$formId}").serialize() + '&callback=?', function(request) {
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

				$D027_Id=$form->campoValorEnviado('Portador_D027_Id');

				//Ao escolher um portador Que tenha algo uma Taxa de Acrescimo IPV(seja diferente de 0) esse valor da taxa vai ser inserido no T250 do orçamento especifico
				$D027 = mysql_query("SELECT D027_Acrescimo_IPV FROM D027 WHERE D027_Id={$D027_Id}");
				$rD027 = mysql_fetch_array($D027);
				log("Aliquota: ".$rD027['D027_Acrescimo_IPV']);
				if($rD027['D027_Acrescimo_IPV']>0){
					log("O Acrescimo IPV é maior que 0");
					log("T003_Id:".$T003_Id);
					$T250 = mysql_query("Select T250_Id,T250_Aliquota,T250_Flag_Altera_Preco from T250 where T250_T003_Id='$T003_Id' and T250_Tipo='5' and T250_Flag_Campo_Calculado='S'");
					$mT250=mysql_fetch_array($T250);
					$T250_Id=$mT250['T250_Id'];

					if(number_format($mT250['T250_Aliquota'],2)!=number_format($rD027['D027_Acrescimo_IPV'],2)){
						mysql_query("Update T250 set T250_Aliquota='{$rD027['D027_Acrescimo_IPV']}' where T250_Id='{$T250_Id}'");
						$erro=mysql_error();
						log("erro: ".$erro);
						$VEN001->atualizarPrecoTabela($T003_Id);

						if($mT250['T250_Flag_Altera_Preco']=='S'){
							$msg = "Foi acrescido ".$rD027['D027_Acrescimo_IPV']."% no preço tabela desse orçamento.";
						}else{
							$msg = "Foi acrescido ".$rD027['D027_Acrescimo_IPV']."% ao demonstrativo IPV.";
						}
					}

				}else{
					$T250 = mysql_query("Select T250_Id,T250_Aliquota,T250_Flag_Altera_Preco from T250 where T250_T003_Id='$T003_Id' and T250_Tipo='5' and T250_Flag_Campo_Calculado='S'");
					$mT250=mysql_fetch_array($T250);
					$T250_Id=$mT250['T250_Id'];

					if($mT250['T250_Aliquota']>0){
						mysql_query("Update T250 set T250_Aliquota='0' where T250_Id='{$T250_Id}'");
						$erro=mysql_error();
						log("erro: ".$erro);
						$VEN001->atualizarPrecoTabela($T003_Id);

						if($mT250['T250_Flag_Altera_Preco']=='S'){
							$msg = "Foi deduzido ".$mT250['T250_Aliquota']."% no preço tabela desse orçamento, devido a mudança do portador.";
						}else{
							$msg = "Foi deduzido ".$mT250['T250_Aliquota']."% ao demonstrativo IPV, devido a mudança do portador.";
						}
					}
				
				}

				// Gerar Boleto			
				$retorno = $VEN001->VEN001atualizarParcelas($form);
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
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);


		/**
		 * PARTE 5: Exibir o formulário
		 */
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');

		$sqlD024 = mysql_query("SELECT D024_Instrucao_Pagamento, D024_Prazo_Parcela, D024_Dias_Vencimento, D024_Quantidade_Parcelas, D027_Portador FROM D024 LEFT JOIN D027 ON D027_Id=D024_D027_Id LEFT JOIN T003 ON T003_D024_Id = D024_Id WHERE T003_Id = '{$T003_Id}'");
		$D024 = mysql_fetch_array($sqlD024);
		$obs='';
		$obs .=	empty($D024['D024_Prazo_Parcela']) 				? '' : " 1º Venc: ".$D024['D024_Prazo_Parcela']."<br />";
		$obs .= empty($D024['D024_Dias_Vencimento']) 			? '' : " Dia Fixo/Intervalo: ".$D024['D024_Dias_Vencimento']."<br />";
		$obs .= empty($D024['D024_Quantidade_Parcelas']) 		? '' : " Nº Parcelas: ".$D024['D024_Quantidade_Parcelas']."<br />";
		$obs .= empty($D024['D027_Portador']) 					? '' : " Portador: ".$D024['D027_Portador']."<br />";
		echo <<<EOT
			<div style="border: 1px solid silver; margin: 10px 15px 0 450px; padding: 5px; height: 100px; border-radius:5px; color: var(--BGVermelho); overflow: auto;">
				<span style="font-weight: bold; color: #000;">Informação de Pagamento</span><br> {$obs}{$D024['D024_Instrucao_Pagamento']}	
			</div>	
EOT;

	break;
	
	case 'ven001FormCancelarOrcamento':
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
		$form_tabela = 'D047';
		$form_campo_pk = 'D047_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'ven001FormCancelarOrcamentoBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/ven/ven001/form/ven001FormCancelarOrcamento/';
		// editor
		$form_editor = array(
			'geral' => '{"T003_D047_Id":{"top":7,"left":35,"width":287,"hidden":0},"T003_Observacao_Orcamento_Perdido":{"top":30,"left":6,"height":200,"width":280,"hidden":0}}'
		);
		
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
										//removerLinhaGrid('{$g['divIdParent']}', 'T003_Id', '{$form->campoValorEnviado('T003_Id')}');
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
		$g['smarty']->display('formEditor.tpl');

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
	
	case 'ven001FormDocumentos':
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
		$form_tabela = 'T188';
		$form_campo_pk = 'T188_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = array('ven001FormDocumentosBtnAdd', 'ven001FormDocumentosBtnCln', 'ven001FormDocumentosBtnNew');
        // Form que será executado ao clicar no botão
		$enviar_acaoHref = '/ven/ven001/form/ven001FormDocumentos/';
        
        //form editor
        $form_editor = array(
            'geral' => '{"ven001FormDocumentosBtnAdd":{"top":74,"left":416,"width":null,"hidden":0},"T188_Descricao":{"top":7,"left":73,"width":342,"hidden":0},"uploadArquivo":{"top":76,"left":28,"width":208,"hidden":0}}',
        );        
        
		// Array de definição do formulário
		$r_T003_Id = isset($r_ven001FormDocumentos) ? $r_ven001FormDocumentos['T188_T003_Id'] : $r_T003_Id;
		                                                           
		$tmp = gGeraSelect('T003', 'T003_Id', 'T003_Numero_DI', "WHERE T003_Id = '{$r_T003_Id}'");
		foreach($tmp as &$value) { $value['selected'] = true; }
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('ven001FormDocumentos');
		$formArray = array(
			'campos' => array(
				'geral' => array(
				)
			)
		);
		//$formArray['campos']['geral']['__T188_T003_Id'] 		= array('name' => '__T188_T003_Id', 'label'=>'Número Pedido', 'type' => 'select', 'extra'=>'style="width:382px;"', 'disabled' => true, 'reject'=> true, 'select' => 
		//	$tmp
		//);
		$formArray['campos']['geral']['T188_Descricao'] 	= array('name' => 'T188_Descricao', 'label'=>'Descrição', 'type' => 'textarea','length' => array(3,60));
		$formArray['campos']['geral']['uploadArquivo'] 			= array('name' => 'uploadArquivo', 'label' => 'Arquivo documento', 'type' => 'file', 'length' => array(24, 24), 'reject' => true, 'uppercase' => false);
		$formArray['campos']['geral']['T188_Nome_Arquivo'] 		= array('name' => 'T188_Nome_Arquivo', 'type' => 'hidden', 'uppercase' => false);
		if (!empty($r_acaoId)) {
			 $formArray['campos']['geral']['ven001FormDocumentosBtnNew'] = array('name' => 'ven001FormDocumentosBtnNew', 'type' => 'button', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		}
		$formArray['campos']['geral']['T188_T003_Id'] 			= array('name' => 'T188_T003_Id', 'type' => 'hidden', 'value' => $r_T003_Id);
		$formArray['campos']['geral']['T188_C007_Id'] 			= array('name' => 'T188_C007_Id', 'type' => 'hidden', 'value' => $g['usuarioAtual']);
		$formArray['campos']['geral']['T188_Data_Hora_Upload'] 			= array('name' => 'T188_Data_Hora_Upload', 'type' => 'hidden', 'value' => date('Y-m-d H:i:s'));
		//$formArray['campos']['geral']['ven001FormDocumentosBtnCln'] = array('name' => 'ven001FormDocumentosBtnCln', 'type' => 'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['geral']['ven001FormDocumentosBtnAdd'] = array('name' => 'ven001FormDocumentosBtnAdd', 'type' => 'submit', 'reload' => false, 'value' => empty($r_acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['geral']['divReload'] 	= array('name' => 'divReload', 'label'=>'', 'type' => 'hidden','value' => @$r_divReload, 'reject' => true);
		
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
						echo <<<EOT
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									if (typeof jQuery != 'undefined') {
										divRefresh("{$g['divId']}", true);
										divRefresh('{$form->campoValorEnviado('divReload')}', true);
										divRefresh('{$g['divId']}', true);
									}
								} </script></div>
EOT;
					}
				} else {
					// Insert
					// Antes de criar o registro no banco, vamos verificar se o arquivo foi realmente enviado
					$arquivo = $form->campoValorEnviado('uploadArquivo');
					if ($arquivo['error'] == UPLOAD_ERR_OK) {
						// Neste caso, guardamos o nome do arquivo no banco
						$form->campoSetarValor('T188_Nome_Arquivo', $arquivo['name'], true);
						if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
							// Gravar o arquivo no disco
							$pathinfo = pathinfo($arquivo['name']);
							$pathinfo['extension'] = empty($pathinfo['extension']) ? '.tmp' : $pathinfo['extension'];
							//$dir = "{$g['pathDados']}documentos/T003_".str_repeat('0',5-strlen($form->campoValorEnviado('T188_T003_Id'))).$form->campoValorEnviado('T188_T003_Id');
							$dir = "{$g['pathDados']}orcamento";
							is_dir($dir) or mkdir($dir, 0777, true);
							$file = $dir . '/' . $g['mysqlLastId'] . '.' . $pathinfo['extension'];
							move_uploaded_file($arquivo['tmp_name'], $file);
							echo <<<EOT
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									if (typeof jQuery != 'undefined') {
										divRefresh("{$g['divId']}", true);
										divRefresh('{$form->campoValorEnviado('divReload')}', true);
										divRefresh('{$g['divId']}', true);
									}
								} </script></div>
EOT;
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
		
		
		
		/**
		* Botões
		*/
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		// $botoesDir .= '<button type="submit" id="content_form1_btn">Enviar</button>';
		// $botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo $g['smarty']->fetch('buttons-floatBottom.tpl');
		// Ações dos botões
		// A ação do botão Enviar é definido dentro do formulário
	break;
    
    case 'ven001FormAgregados':
            /**
            * PARTE 1: Variavéis envidas para o formulário
            */
            // Variável indica qual tab do form será exibida
            $tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
            // Utilizada em UPDATE, é o ID do registro que está sendo editado
            $T004_Id = (isset($_REQUEST['T004_Id'])) ? $_REQUEST['T004_Id'] : false;
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
            $form_tabela = 'T202';
            $form_campo_pk = 'T202_Id';
            // ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
            $enviar_btnId = array('ven001FormAgregadosBtnAdd', 'ven001FormAgregadosBtnCln', 'ven001FormAgregadosBtnNew');
            // Form que será executado ao clicar no botão
            $enviar_acaoHref = '/ven/ven001/form/ven001FormAgregados/';
            
            //form editor
            $form_editor = array(
                'geral' => '{"ven001FormAgregadosBtnAdd":{"top":51,"left":400.00006103515625,"width":null,"hidden":0},"T202_Valor_Unitario":{"top":7,"left":369.00006103515625,"width":75,"hidden":0},"T202_Descricao":{"top":7,"left":25,"width":275,"hidden":0},"T202_Observacao":{"top":30,"left":14,"width":277,"hidden":0}}',
            );

            // novo formulário
            $form = new Formulario('ven001FormAgregados');

            // Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
            $formArray = array(
                'campos' => array(
                    'geral' => array(
                    'T202_T004_Id' => array('name' => 'T202_T004_Id', 'type' => 'hidden', 'value' => @$T004_Id, 'length' => array(50,30)),
                    'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true),
                    'T202_Descricao' => array('name' => 'T202_Descricao','label'=>'Descrição','type' => 'text', 'length' => array(50,30)),
                    'T202_Valor_Unitario' => array('name' => 'T202_Valor_Unitario','label'=>'Valor','type' => 'moeda','align' => 'right','length' => array(10,10)),
                    'T202_Observacao' => array('name' => 'T202_Observacao','label'=>'Observação','type' => 'textarea', 'length' => array(2,47)),
                    )
                )
            );
            
            // Botão Limpar / Novo Registro
            if (!empty($acaoId)) {
                 $formArray['campos']['geral']['ven001FormAgregadosBtnNew'] = array('name' => 'ven001FormProcessosBtnNew', 'type'=>'button', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
            }
            //$formArray['campos']['geral']['ven001FormAgregadosBtnCln'] = array('name' => 'ven001FormProcessosBtnCln', 'type'=>'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
            $formArray['campos']['geral']['ven001FormAgregadosBtnAdd'] = array('name' => 'ven001FormProcessosBtnAdd', 'type'=>'submit', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
            $form->definirFormularioArray($formArray);




            /**
            * PARTE 3: Processamento do formulário (se foi enviado)
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
                            // Imprime uma mensagem e fecha a janela
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
    break;

    case 'ven001FormSelecionarEmpresa':
            /**
         * PARTE 1: Variavéis envidas para o formulário
         */
        // Variável indica qual tab do form será exibida
        $tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
        // Utilizada em UPDATE, é o ID do registro que está sendo editado
        $acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
	
		$mudarFilial = (isset($r_mudarFilial)) ? $r_mudarFilial : false;
		//$gerarNF = (isset($r_gerarNF)) ? $r_gerarNF : false;
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
        $form_tabela = '';
        $form_campo_pk = '';
        // ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
        $enviar_btnId = 'ven001SelecionarEmpresa_btn';
        // Form que será executado ao clicar no botão
        $enviar_acaoHref = '/ven/ven001/form/ven001FormSelecionarEmpresa/';

        // novo formulário
        $form = new Formulario('ven001FormSelecionarEmpresa');
        // Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
        $formArray = array(
            'campos' => array(
                'geral' => array(
                    'C004_Id_Novo' => array('name' => 'C004_Id_Novo','label'=>'Empresa', 'type' => 'select','extra' => 'style="width:300px;"' ,
                        'select' => gGeraSelect('C004','C004_Id','C004_Nome_Empresa',"where C004_Flag_Ativo='S'", false, $g['empresaAtual'])
                    ),
                    'T003_Id_Antigo' => array('name' => 'T003_Id_Antigo', 'label' => 'T003_Id_Antigo', 'type' => 'hidden','value' => @$r_T003_Id),
                )
            )
        );

        $form->definirFormularioArray($formArray);

        /**
         * PARTE 3: Processamento do formulário (se foi enviado)
         *            E carregamento dos valores (se não foi enviado e possui acaoId)
         */
if ($formCodigo = $form->processarFormulario($form_tabela)) {
            // Form com erros
            if ($formCodigo == 1) {
                echo gGeraAlertMsgErroForm($form);
                // Form sem erros
            } else if ($formCodigo == 2) {
                $C004_Id_Novo = $form->campoValorEnviado('C004_Id_Novo');
                $T003_Id_Antigo = $form->campoValorEnviado('T003_Id_Antigo');
		if($mudarFilial == true){
			$retorno = $VEN001->VEN001MudarFilial($T003_Id_Antigo,$C004_Id_Novo);
		} else {
			$retorno = $VEN001->VEN001DividirOrcamento($T003_Id_Antigo,$C004_Id_Novo);
		}
                
            if ($retorno===true) {
                // Imprime uma mensagem e fecha a janela
                if($mudarFilial == true){
                    $alert .= gGeraAlertMsg('Operação OK', 'Filial alterada com sucesso!', false);
			    	$link = "";
		    	} else if($gerarNF == true){
		    		$alert .= gGeraAlertMsg('Operação OK', 'Nota Gerada com sucesso!', false);
					$link = "abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,unique(), '', 'Editar Nota Fiscal - ID: ' + '{$T007_Id}', '/ven/ven003/content/ven003contentIncluirNotaFiscal/', '&acaoId=' + encodeURIComponent('{$T007_Id}') + '&tabela={$from}', [900,550]);";
		    	} else {
		    		$alert .= gGeraAlertMsg('Operação OK', 'Orçamento dividido com sucesso!', false);
					$link = "abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Dividir Orçamento', '/ven/ven001/content/ven001ContentEditarOrcamento/', '&acaoId=' + encodeURIComponent('{$novoT003_Id}'), [1000,610]);";
		    	}
                    echo <<<EOT
                        <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                            $("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
                                setTimeout(function() {
                                {$alert}
                                {$link}
								divRefresh('{$r_divRefresh}');
                                }, 150);
                              });
                            $("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
                            divRefresh('{$g['divIdParent']}', true);
                        } </script></div>
EOT;
                }else{
                   $alert .= gGeraAlertMsg('Erro',"{$retorno}", false);
                   echo <<<EOT
                        <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                            {$alert}
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

    case 'ven001FormImportarOrcamento':
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
		
		
		$form_tabela = 'T003';
		$form_campo_pk = 'T003_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'ven001ImportarOrcamentoBtnExecutar';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/ven/ven001/form/ven001FormImportarOrcamento/';
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
     				'T003_Id' => array('name' => 'T003_Id', 'label' => 'T003_Id', 'type' => 'hidden', 'length' => array(40, 80), 'reject' => true, 'value' => $r_T003_Id),
     				'T003_Id_Origem' => array('name' => 'T003_Id_Origem', 'label' => 'Orçamento', 'type' => 'text', 'length' => array(40, 80), 'reject' => true),
				)
			)
		);
        $form = new Formulario('form-ven001FormTotais2');
		$form->definirFormularioArray($formArray);
		

			if ($formCodigo = $form->processarFormulario($form_tabela)) {
				// Form com erros
				if ($formCodigo == 1) {
					echo gGeraAlertMsgErroForm($form);
					// Form sem erros
				} else if ($formCodigo == 2) {
					// Importar itens do orçamento em etapas para melhorar a perfomance
					if ($g['C031']['importarItensOrcamentoEmEtapas'] == 'S') {
						$T003_Id = $form->campoValorEnviado('T003_Id');
						$T003_Id_Origem = $form->campoValorEnviado('T003_Id_Origem');

						$sql = "SELECT
									T004_Id,
									{$T003_Id} AS T003_Id,
									{$T003_Id_Origem} AS T003_Id_Origem,
									IF(T004_Id = (
											SELECT Ult.T004_Id FROM T004 AS Ult WHERE Ult.T004_T003_Id = '{$T003_Id_Origem}' ORDER BY Ult.T004_Item DESC, Ult.T004_Id DESC LIMIT 1
										), 1, 0
									) AS Ultimo_T004
								FROM T004
								WHERE T004_T003_Id = '{$T003_Id_Origem}'";
						$sql = gComprimeSql($sql);
							
						echo <<<JS
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								processaEtapasAsync("Importar Itens Orçamento", "VEN001", "importarItensOrcamentoEmEtapas()", "{$sql}", false, "{$g['divId']}" || $('#{$g['divIdParent']}').attr('id') || '')
									.then(function () {
										console.log('then executando depois do processaEtapasAsync');
									})
									.catch(function (err) {
										console.log('erro etapas:', err);
									})
									.finally(function () {
										console.log('finally etapas');
										divRefresh($('#{$g['divIdParent']}').parent().parent().find('.content-2rows-last').attr('id'));
										divRefresh($('#{$g['divIdParent']}').parent().parent().find('.content-2rows-width').attr('id'));
										fecharJanela('{$g['divId']}');
										$('#{$g['divId']}').hideLoading();
									});
							} </script></div>
						JS;
						die();
					} else {
						$retorno = $VEN001->importarItensOrcamento($form);
					}
					if ($retorno === true) {
						$alert = gGeraAlertMsg('Operação OK', 'Itens importados com sucesso.', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								if (typeof jQuery != 'undefined') {
									{$alert}
                                   divRefresh("{$g['divIdParent']}", true);
								   $("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
                               	}
							} </script></div>
EOT;
					} else {
						echo gGeraAlertMsg('Erro!', 'Erro ao importar itens:<br />' . $retorno);
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

	case 'ven001FormDefinirValorTotal':
	/**
	 * PARTE 1: Variavéis envidas para o formulário
	 */
	// Variável indica qual tab do form será exibida
	$idTabelaCabecalho = (isset($r_idTabelaCabecalho)) ? $r_idTabelaCabecalho : false;
	$tabelaCabecalho = (isset($r_tabelaCabecalho)) ? $r_tabelaCabecalho : false;
	$tabelaProduto = (isset($r_tabelaProduto)) ? $r_tabelaProduto : false;
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
	$form_tabela = $tabelaCabecalho;
	$form_campo_pk = $tabelaCabecalho.'_Id';
	// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
	$enviar_btnId = 'ven001AplicarDescontoTotalBtnExecutar';
	// Form que será executado ao clicar no botão
	$enviar_acaoHref = '/ven/ven001/form/ven001FormDefinirValorTotal/';

	//form editor
	$form_editor = array('geral' =>'{"descontoValorTotal":{"top":7,"left":14.28125,"width":200,"hidden":0}}');
	
	// Definição do Formulário
	$formArray = array(
		'campos' => array(
			'geral' => array(
				// Campo normal (do banco)
				'descontoValorTotal' => array('name' => 'descontoValorTotal', 'label' => 'Valor Total (R$)', 'type' => 'moeda', 'length' => array(30, 80), 'reject' => true, 'casasDecimais' => 10)
			)
		)
	);
	$form = new Formulario('form-ven001FormDefinirValorTotal');
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
							var descontoValorTotal = $('#{$g['divId']} #descontoValorTotal').val();
							$('#{$g['divId']}').showLoading();
							$.getJSON('/ven/ven001/form_func-ajax/ven001aplicarDescontoTotal/?ajax=true&tabelaCabecalho=' + encodeURIComponent('{$tabelaCabecalho}') + '&idTabelaCabecalho=' + encodeURIComponent('{$idTabelaCabecalho}') + '&tabelaProduto=' + encodeURIComponent('{$r_tabelaProduto}') + '&descontoValorTotal=' + encodeURIComponent(descontoValorTotal) + '&callback=?', function(request) {
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
		$g['smarty']->display('formEditor.tpl');

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = '';
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="ven001AplicarDescontoTotalBtnExecutar">Executar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'ven001FormAplicarDescontoValorTotal':
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;
		$acaoId = (isset($r_acaoId)) ? $r_acaoId : false;
		$tabelaCabecalho = (isset($r_tabelaCabecalho)) ? $r_tabelaCabecalho : false;
		$PDV = (isset($r_PDV)) ? $r_PDV : false;

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
		$form_tabela = $tabelaCabecalho;
		$form_campo_pk = $tabelaCabecalho . '_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'ven001AplicarDescontoTotalBtnExecutar';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/ven/ven001/form/ven001FormAplicarDescontoValorTotal/';

		if ( $tabelaCabecalho == "T003" ) {
			$form_editor = array('geral' => '{"T003_Valor_Desconto_Manual":{"top":7,"left":18.96875,"width":200,"hidden":0}}'); //form editor
			$formArray['campos']['geral']['T003_Valor_Desconto_Manual'] = array('name' => 'T003_Valor_Desconto_Manual', 'label' => 'Desconto (R$)', 'type' => 'moeda', 'length' => array(30, 80), 'casasDecimais' => 2);
		} elseif ( $tabelaCabecalho == "T005" ) {
			$form_editor = array('geral' => '{"T005_Valor_Desconto_Manual":{"top":7,"left":18.96875,"width":200,"hidden":0}}'); //form editor
			$formArray['campos']['geral']['T005_Valor_Desconto_Manual'] = array('name' => 'T005_Valor_Desconto_Manual', 'label' => 'Desconto (R$)', 'type' => 'moeda', 'length' => array(30, 80), 'casasDecimais' => 2);
		} else { // T007
			$form_editor = array('geral' => '{"T007_Valor_Desconto":{"top":7,"left":18.96875,"width":200,"hidden":0}}'); //form editor
			$formArray['campos']['geral']['T007_Valor_Desconto'] = array('name' => 'T007_Valor_Desconto', 'label' => 'Desconto (R$)', 'type' => 'moeda', 'length' => array(30, 80), 'casasDecimais' => 2);
		}

		$form = new Formulario('form-ven001FormAplicarDescontoValorTotal');
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
				// Caso seja chamada da tela PDV
				if ($PDV){
                   // Validar alçada desconto

				   echo <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
						var \$buttons = {
							"Sim": function() {
								var valorDesconto = $('#{$g['divId']} #T007_Valor_Desconto').val();
								$('#{$g['divId']}').showLoading();
								$.getJSON('/ven/ven015/form_func-ajax/ven015aplicarDescontoPDV/?ajax=true&T007_Id=' + encodeURIComponent('{$acaoId}') + '&valorDesconto=' + encodeURIComponent(valorDesconto) + '&callback=?', function(request) {
									if (request.code) {
										var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
										dialogConfirm('Ok', request.data, \$buttons);
										divRefresh('{$g['divIdParent']}');
										fecharJanela('{$g['divId']}');
									} else {
										var \$buttons = { 
											"Fechar": function() { 
													$(this).dialog("destroy").remove(); 
													setTimeout(function() { 
														$('#valorDesconto').focus();
														$('#valorDesconto').select();
													}, 150); 
											}, 
											"Pedir autorização": function() { 
													$(this).dialog("destroy").remove(); 
													abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Autorização', '/sistema/funcoes/autoriza/content/', '&tabela=T007&tabelaId={$acaoId}&descricao=Desconto&valor=' + encodeURIComponent(request.percentualDesconto) + '&noValorTotal=true&divForm={$g['divId']}', [400,300]);
											} 
										};
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

				} else if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
					// Imprime uma mensagem e fecha a janela
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
							divRefresh('{$g['divIdParent']}');
						} </script></div>
EOT;
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
		$botoesMid = '';
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="ven001AplicarDescontoTotalBtnExecutar">Executar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;
	
	case 'ven001FormAplicarPercentualDescontoValorTotal':
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;
		$idTabelaCabecalho = (isset($r_idTabelaCabecalho)) ? $r_idTabelaCabecalho : false;
		$tabelaCabecalho = (isset($r_tabelaCabecalho)) ? $r_tabelaCabecalho : false;
		$PDV = (isset($r_PDV)) ? $r_PDV : false;

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
		$form_tabela = $tabelaCabecalho;
		$form_campo_pk = $tabelaCabecalho . '_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'ven001FormAplicarPercentualDescontoValorTotal';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/ven/ven001/form/ven001FormAplicarPercentualDescontoValorTotal/';

		$formArray = array(
			'campos' => array(
				'geral' => array(
     				'percentualDesconto' => array('name' => 'percentualDesconto', 'label' => 'Desconto (%)', 'type' => 'moeda', 'length' => array(30, 80), 'reject' => true, 'casasDecimais' => 2),
				)
			)
		);

		$form = new Formulario('form-ven001FormAplicarDescontoValorTotal');
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
				echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
					var \$buttons = {
						"Sim": function() {
							var percentualDesconto = $('#{$g['divId']} #percentualDesconto').val();
							$('#{$g['divId']}').showLoading();
							$.getJSON('/ven/ven001/form_func-ajax/ven001aplicarPercentualDescontoValorTotal/?ajax=true&tabelaCabecalho=' + encodeURIComponent('{$tabelaCabecalho}') + '&idTabelaCabecalho=' + encodeURIComponent('{$idTabelaCabecalho}')  + '&tabelaProduto=' + encodeURIComponent('{$r_tabelaProduto}') + '&percentualDesconto=' + encodeURIComponent(percentualDesconto) + '&PDV=' + encodeURIComponent('{$r_PDV}') + '&callback=?', function(request) {
								if (request.code) {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
									dialogConfirm('Ok', request.data, \$buttons);
									divRefresh('{$g['divIdParent']}');
									fecharJanela('{$g['divId']}');
								} else {
									var \$buttons = { 
										"Fechar": function() { 
												$(this).dialog("destroy").remove(); 
												setTimeout(function() { 
													$('#desconto').focus();
													$('#desconto').select();
												}, 150); 
										}, 
										"Pedir autorização": function() { 
												$(this).dialog("destroy").remove(); 
												abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Autorização', '/sistema/funcoes/autoriza/content/', '&tabela=T007&tabelaId={$idTabelaCabecalho}&descricao=Desconto&valor=' + encodeURIComponent(desconto) + '&divForm={$g['divId']}', [400,300]);
										} 
									};
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
		$botoesMid = '';
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="ven001FormAplicarPercentualDescontoValorTotal">Executar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'ven001FormCotar':
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
		$form_tabela = 'T189';
		$form_campo_pk = 'T189_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'ven001FormCotar_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/ven/ven001/form/ven001FormCotar/';
        
        //form editor
        $form_editor = array(
            'geral' => '{"T189_Observacao":{"top":7,"left":14,"height":131,"width":241,"hidden":0},"T189_T004_Id":{"top":122,"left":0,"width":127,"hidden":1},"T189_D009_Id":{"top":99,"left":0,"width":127,"hidden":1}}',
        );
		
		$form = new Formulario('ven001FormCotar');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T189_Observacao' => array('name' => 'T189_Observacao', 'label' => 'Observação', 'type' => 'textarea', 'length' => array(8, 40)), 
					// Campos Hidden
					'T189_T004_Id' => array('name' => 'T189_T004_Id', 'label' => '', 'type' => 'hidden', 'value' => $r_T004_Id),
					'T189_D009_Id' => array('name' => 'T189_D009_Id', 'label' => '', 'type' => 'hidden', 'value' => $r_D009_Id),
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
				$retorno = $VEN001->cotarProduto($form);
				if ($retorno !== true) {
					echo gGeraAlertMsg('Erro', $retorno);	
				} else {
					//echo gGeraAlertMsg('Operação OK', "Com sucesso");	
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

	break;

	case 'ven001FormAtualizarCFOP':
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
		$form_tabela = 'T003';
		$form_campo_pk = 'T003_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'ven001FormAtualizarCFOP_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/ven/ven001/form/ven001FormAtualizarCFOP/';
        
        //form editor
        $form_editor = array(
            'geral' => '{"linha3":{"top":7,"left":14,"width":null,"hidden":0},"linha":{"top":50,"left":19,"width":null,"hidden":1},"D006_Id":{"top":76,"left":11.328125,"width":335,"hidden":0},"ven001FormAtualizarCFOP_btn":{"top":74,"left":400,"width":null,"hidden":0},"linha2":{"top":119,"left":17,"width":null,"hidden":1},"ven001FormAtualizarCFOPRegras":{"top":143,"left":16,"width":null,"hidden":0}}',
        );

		$descricaoCFOP  = ($g['C031']['descricaoCFOPExibida'] == 'P') ? 'D006_Descricao' : 'IF(D006_Descricao_Comercial !="",D006_Descricao_Comercial,D006_Descricao)';
		
		$form = new Formulario('ven001FormAtualizarCFOP');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D006_Id' => array('name' => 'D006_Id', 'label' => 'CFOP', 'type' => 'select', 'reject' => true,
						'select' => gGeraSelect('D006', 'D006_Id', "LPAD(D006_Id[[VIRGULA]]4[[VIRGULA]]0),D006_Codigo_CFOP,{$descricaoCFOP}", 'WHERE IFNULL(D006_Flag_Inativo,"N")!="S" AND D006_Flag_Departamento="COMERCIAL" ORDER BY D006_Codigo_CFOP DESC', true, false, '%1 - %2 - %3')
					),
				),
			)
		);

		$formArray['campos']['geral']['ven001FormAtualizarCFOP_btn'] = array('name' => 'ven001FormAtualizarCFOP_btn', 'type'=>'submit', 'reload' => false, 'value' => 'Atualizar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['geral']['linha'] = array('name' => 'linha', 'type'=>'titulo', 'value' => '', 'label' => 'Atualizar manual:', 'reject' => true);
		$formArray['campos']['geral']['linha2'] = array('name' => 'linha2', 'type'=>'titulo', 'value' => '', 'label' => 'Atualizar de acordo com as regras de CFOP:', 'reject' => true);
		$formArray['campos']['geral']['linha3'] = array('name' => 'linha3', 'type'=>'comentario', 'value' => 'Atenção: Ao escolher uma das opções, a CFOP e as <br />aliquotas dos itens deste orçamento serão alterados.', 'label' => '', 'reject' => true, 'style' => 'color: #FF0000; font-size: 12px;');
		$formArray['campos']['geral']['ven001FormAtualizarCFOPRegras'] = array('name' => 'ven001FormAtualizarCFOPRegras', 'type'=>'button', 'reload' => false, 'value' => 'Aplicar regras de CFOP', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$form->definirFormularioArray($formArray);
		
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
			$('#ven001FormAtualizarCFOPRegras').bind('click', function(data) {
				$('#{$g['divId']}').showLoading();
				$.getJSON('/ven/ven001/form_func-ajax/atualizarCFOP/?ajax=true&T003_Id=' + encodeURIComponent("{$r_T003_Id}") + '&callback=?', function(request) {
					if (request.code) {
						T003_Id = {$r_T003_Id};	
						idProgressBar = dialogAlert('Atualizando Alíquotas',"<span id='progressBar'>Processando 0%</span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' />");
	            		ven001AtualizarAliquotaCFOPGeralLoop(idProgressBar,T003_Id,1);
						
						function ven001AtualizarAliquotaCFOPGeralLoop(idProgressBar, T003_Id, inicio){
			                $.getJSON('/ven/ven001/form_func-ajax/atualizarAliquota/?ajax=true&T003_Id=' + T003_Id + '&extra=' + encodeURIComponent("{$extra_}") + '&inicio=' + inicio + '&callback=?', function(request) {
			                    if (request.concluido == 0) {
			                    	$('#progressBar').html('<b>Processado '+ request.percentual + '%</b>' + '<br/><br/>Produtos atualizados: <b>' + request.ok + '</b><br/>Produtos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
			                    	ven001AtualizarAliquotaCFOPGeralLoop(idProgressBar,T003_Id,0);
			                    }else{
			                    	if (request.code) {
			                    		$('#dc-' + idProgressBar).dialog("destroy").remove();
			                    		dialogConfirm('Alíquotas atualizadas','<b>Processado '+ request.percentual + '%</b>' + '<br/><br/>Produtos atualizados: <b>' + request.ok + '</b><br/>Produtos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
										$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
										divRefresh('{$g['divIdParent']}', true);
			                        } else {
			                        	$('#dc-' + idProgressBar).dialog("destroy").remove();
			                            dialogConfirm('Erro!', request.data);
			                        }
			                    } 
			                });	                    
				      	}
					} else {
						var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
						dialogConfirm('Erro', request.data, \$buttons);
					}
					$('#{$g['divId']}').hideLoading();
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
				$retorno = $VEN001->ven001atualizarCFOPManual($r_T003_Id, $D006_Id);
				if ($retorno !== true) {
					echo<<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						divRefresh('{$g['divId']}', true);
					} </script></div>
EOT;
					echo gGeraAlertMsg('Erro', $retorno);	
				} else {
					echo <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						T003_Id = {$r_T003_Id};	
						idProgressBar = dialogAlert('Atualizando Alíquotas',"<span id='progressBar'>Processando 0%</span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' />");
	            		ven001AtualizarAliquotaCFOPLoop(idProgressBar,T003_Id,1);

	            		function ven001AtualizarAliquotaCFOPLoop(idProgressBar, T003_Id, inicio){
			                $.getJSON('/ven/ven001/form_func-ajax/atualizarAliquota/?ajax=true&T003_Id=' + T003_Id + '&extra=' + encodeURIComponent("{$extra_}") + '&inicio=' + inicio + '&callback=?', function(request) {
			                    if (request.concluido == 0) {
			                    	$('#progressBar').html('<b>Processado '+ request.percentual + '%</b>' + '<br/><br/>Produtos atualizados: <b>' + request.ok + '</b><br/>Produtos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
			                    	ven001AtualizarAliquotaCFOPLoop(idProgressBar,T003_Id,0);
			                    }else{
			                    	if (request.code) {
			                    		$('#dc-' + idProgressBar).dialog("destroy").remove();
			                    		dialogConfirm('Alíquotas atualizadas','<b>Processado '+ request.percentual + '%</b>' + '<br/><br/>Produtos atualizados: <b>' + request.ok + '</b><br/>Produtos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
										$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
										divRefresh('{$g['divIdParent']}', true);
			                        } else {
			                        	$('#dc-' + idProgressBar).dialog("destroy").remove();
			                            dialogConfirm('Erro!', request.data);
			                        }
			                    } 
			                });	                    
				      	}
						
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
	
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'ven001FormComissoes':
        /**
        * PARTE 1: Variavéis envidas para o formulário
        */
        // Variável indica qual tab do form será exibida
        $tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
        // Utilizada em UPDATE, é o ID do registro que está sendo editado
        $T004_Id = (isset($_REQUEST['T004_Id'])) ? $_REQUEST['T004_Id'] : false;
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
        $form_tabela = 'T003';
        $form_campo_pk = 'T003_Id';
        $enviar_btnId = 'ven001FormComissoesBtn';
        $enviar_acaoHref = '/ven/ven001/form/ven001FormComissoes/';
        
        //form editor
        $form_editor = array(
            'geral' => '{"T003_Valor_Total_Comissao_Usuario":{"top":7,"left":48.015625,"width":56,"hidden":0},"T003_Percentual_Comissao_Usuario":{"top":7,"left":224.09375,"width":56,"hidden":0},"T003_Valor_Total_Comissao":{"top":30,"left":1.515625,"width":56,"hidden":0},"T003_Percentual_Comissao":{"top":30,"left":224.09375,"width":56,"hidden":0},"T003_Percentual_Comissao_Manual":{"top":30,"left":314,"width":56,"hidden":0},"T003_Valor_Total_Comissao_Externo":{"top":53,"left":-2.140625,"width":56,"hidden":0},"T003_Percentual_Comissao_Externo":{"top":53,"left":224.09375,"width":56,"hidden":0},"T003_Valor_Comissao_Representante":{"top":76,"left":13.765625,"width":56,"hidden":0},"T003_Percentual_Comissao_Representante":{"top":76,"left":224.09375,"width":56,"hidden":0},"ven001FormComissoesBtn":{"top":120,"left":240,"width":null,"hidden":0}}',
        );

        // novo formulário
        $form = new Formulario('ven001FormComissoes');

        // Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
        $formArray = array(
            'campos' => array(
                'geral' => array(
                	'T003_Percentual_Comissao' => array('name' => 'T003_Percentual_Comissao', 'label' => '%', 'type' => 'moeda', 'align' => 'right', 'length' => array(10,10), 'readonly' => true),
                	'T003_Percentual_Comissao_Manual' => array('name' => 'T003_Percentual_Comissao_Manual', 'label' => '% manual', 'type' => 'moeda', 'align' => 'right', 'length' => array(10,10)),
                	'T003_Valor_Total_Comissao' => array('name' => 'T003_Valor_Total_Comissao', 'label' => 'Comissão Vendedor Interno', 'type' => 'moeda','align' => 'right', 'length' => array(10,10), 'readonly' => true),
                	'T003_Percentual_Comissao_Representante' => array('name' => 'T003_Percentual_Comissao_Representante', 'label' => '%', 'type' => 'moeda','align' => 'right', 'length' => array(10,10)),
                	'T003_Valor_Comissao_Representante' => array('name' => 'T003_Valor_Comissao_Representante', 'label' => 'Comissão Representante', 'type' => 'moeda','align' => 'right', 'length' => array(10,10), 'readonly' => true),
                	'T003_Percentual_Comissao_Externo' => array('name' => 'T003_Percentual_Comissao_Externo', 'label' => '%', 'type' => 'moeda','align' => 'right', 'length' => array(10,10)),
                	'T003_Valor_Total_Comissao_Externo' => array('name' => 'T003_Valor_Total_Comissao_Externo', 'label' => 'Comissão Vendedor Externo', 'type' => 'moeda','align' => 'right', 'length' => array(10,10), 'readonly' => true),
                	'T003_Percentual_Comissao_Usuario' => array('name' => 'T003_Percentual_Comissao_Usuario', 'label' => '%', 'type' => 'moeda','align' => 'right', 'length' => array(10,10)),
                	'T003_Valor_Total_Comissao_Usuario' => array('name' => 'T003_Valor_Total_Comissao_Usuario', 'label' => 'Comissão Usuário', 'type' => 'moeda','align' => 'right', 'length' => array(10,10), 'readonly' => true),
        			'ven001FormComissoesBtn' => array('name' => 'ven001FormComissoesBtn', 'type'=>'submit', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true),
                )
            )
        );
        
        $form->definirFormularioArray($formArray);
        /**
        * PARTE 3: Processamento do formulário (se foi enviado)
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
                        echo gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!');
                        echo <<<EOT
	                        <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
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
    break;

    case 'ven001FormMoeda':
        /**
        * PARTE 1: Variavéis envidas para o formulário
        */
        // Variável indica qual tab do form será exibida
        $tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
        // Utilizada em UPDATE, é o ID do registro que está sendo editado
        $T004_Id = (isset($_REQUEST['T004_Id'])) ? $_REQUEST['T004_Id'] : false;
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
        $form_tabela = 'T003';
        $form_campo_pk = 'T003_Id';
        $enviar_btnId = 'ven001FormMoedaBtn';
        $enviar_acaoHref = '/ven/ven001/form/ven001FormMoeda/';
        
        //form editor
        $form_editor = array(
            'geral' => '{"T003_D044_Id":{"top":7,"left":73,"width":95,"hidden":0},"T003_Cotacao_Moeda":{"top":30,"left":14,"width":88,"hidden":0},"ven001FormMoedaBtn":{"top":74,"left":144,"width":null,"hidden":0}}',
        );

        // novo formulário
        $form = new Formulario('ven001FormMoeda');

        // Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
        $formArray = array(
            'campos' => array(
                'geral' => array(
                	'T003_D044_Id' => array('name' => 'T003_D044_Id', 'label' => 'Moeda', 'type' => 'select', 
                		'select' => gGeraSelect('D044', 'D044_Id', 'D044_Moeda,D044_Simbolo')
                	),
                    'T003_Cotacao_Moeda' => array('name' => 'T003_Cotacao_Moeda', 'label' => 'Cotação da Moeda', 'type' => 'moeda', 'align' => 'right', 'length' => array(10,10), 'casasDecimais' => 5),
                	'ven001FormMoedaBtn' => array('name' => 'ven001FormMoedaBtn', 'type'=>'submit', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true),
                )
            )
        );
        
        $form->definirFormularioArray($formArray);
        /**
        * PARTE 3: Processamento do formulário (se foi enviado)
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
                        echo gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!');
                        echo <<<EOT
	                        <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
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
    break;

    case 'ven001FormAplicarPrecoPorKg':
    	global $g;
    	echo "* Irá aplicar o preço por KG para os produtos que possuem peso unitário e preço por Kg cadastrados.";
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
		$form_tabela = 'TXXX';
		$form_campo_pk = 'TXXX_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'ven001FormAplicarPrecoPorKg_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/ven/ven001/form/ven001FormAplicarPrecoPorKg/';
        
        //form editor
        $form_editor = array(
            //
        );
		
		$form = new Formulario('ven001FormAplicarPrecoPorKg');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'PrecoPorKG' => array('name' => 'PrecoPorKG', 'label' => 'Preço por KG', 'type' => 'moeda', 'casasDecimais' => 4, 'reject' => true)
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
				$Preco = $form->campoValorEnviado('PrecoPorKG');
				$Preco = gCorrigeNumeroInverte($Preco);
				$sql = <<<EOT
					SELECT
						T004_Id,
						T004_T003_Id,
						T004_Valor_Preco_Unitario,
						D001_Peso_Unitario_Kg,
						D001_Descricao_Produto,
						T004_Codigo_Produto,
						D049_Valor_Preco_KG
					FROM T004
					LEFT JOIN D009 ON T004_D009_Id=D009_Id
					LEFT JOIN D049 ON D049_Id=D009_D049_Id
					LEFT JOIN D041 ON D041_D049_Id=D049_Id
					LEFT JOIN D001 ON D001_Id=D049_D001_Id
					left join D002 on D002_Id=D001_D002_Id
					WHERE T004_T003_Id = '{$r_T003_Id}'
					AND D049_Valor_Preco_KG > 0
					AND D001_Peso_Unitario_Kg > 0
					GROUP BY T004_Id
EOT;
				$alert = '';
				$count = array(0, 0);
				$res = mysql_query($sql);
				while ($row = mysql_fetch_assoc($res)) {
					//if (preg_match('#^perfil#i', $row['D001_Descricao_Produto'])) {
						if ($row['D001_Peso_Unitario_Kg'] > 0) {
							$count[0]++;
							$preco = $row['D001_Peso_Unitario_Kg'] * $Preco;
							//Foi utilizado o método pois o anterior não calculava o desconto para o preço unitário se manter correto
							$VEN001->ven001AplicarPercentualDesconto($row['T004_Id'], 'T004_Valor_Preco_Unitario', '', gCorrigeNumero($preco, 4));
							//$sql = "UPDATE T004 SET T004_Valor_Preco_Unitario = '{$preco}' WHERE T004_Id = '{$row['T004_Id']}'";
							//$alert .= addslashes($sql) . "\\n";
							//mysql_query($sql);
							//mysql_query("CALL T004_Gravar_Totalizacao_4('{$row['T004_Id']}')");
							mysql_query("CALL T003_Gravar_Totalizacao_4('{$row['T004_T003_Id']}')");
						} else {
							$count[1]++;
							if (!$alert) {
								$alert = "Alguns dos produtos não possuem peso definido:\\n";
							}
							$alert .= "{$row['T004_Codigo_Produto']}\\n";
						}
						//}
				}
				$alert = "Itens Corrigidos: {$count[0]}\\n\\n" . $alert;
				// Itens com Erro: {$count[1]}\\n
				echo <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
	    				dialogConfirm('Operação Ok', '{$alert}', \$buttons);
						$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
						divRefresh('{$g['divIdParent']}', true);
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

    case 'ven001FormIncluirItemMontado':
    	/**
		* Cabeçalho (Versão Acima da TAB)
		*/
		$cabecalhoId = uniqid();
		echo '<div id="'.$cabecalhoId.'" class="contentCabecalho" style="height: 38px;"></div>';
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($cabecalhoId, '/ven/ven001/outro/ven001CabecalhoItemMontado/', '&D009_Id=' . urlencode($r_D009_Id), true),
		);
		echo gProcessaAcoes($actions);

    	/**
        * PARTE 1: Variavéis envidas para o formulário
        */
        // Variável indica qual tab do form será exibida
        $tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
        // Utilizada em UPDATE, é o ID do registro que está sendo editado
        $T004_Id = (isset($_REQUEST['T004_Id'])) ? $_REQUEST['T004_Id'] : false;
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
        $form_tabela = 'T000';
        $form_campo_pk = 'T000_Id';
        $enviar_btnId = 'ven001FormIncluirItemMontadoBtn';
        $enviar_acaoHref = '/ven/ven001/form/ven001FormIncluirItemMontado/';

        //form editor
        $form_editor = array(
            'geral' => '{"T004_Quantidade":{"top":7,"left":15,"width":86,"hidden":0},"ven001FormIncluirItemMontadoBtn":{"top":5,"left":180,"width":null,"hidden":0}}',
        );

        // novo formulário
        $form = new Formulario('ven001FormIncluirItemMontado');

        // Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
        $formArray = array(
            'campos' => array(
                'geral' => array(
                	'T004_Quantidade' => array('name' => 'T004_Quantidade', 'label' => 'Quantidade', 'type' => 'moeda', 'align' => 'right', 'length' => array(10,10), 'reject' => true),
                	'T003_Id' => array('name' => 'T003_Id', 'type' => 'hidden', 'value' => $r_T003_Id, 'reject' => true),
                	'D009_Id' => array('name' => 'D009_Id', 'type' => 'hidden', 'value' => $r_D009_Id, 'reject' => true),
                	'divForm' => array('name' => 'divForm', 'type' => 'hidden', 'value' => $r_divForm, 'reject' => true),
                	'checkEnviado' => array('name' => 'checkEnviado', 'type' => 'hidden', 'value' => '', 'reject' => true),
                	'ven001FormIncluirItemMontadoBtn' => array('name' => 'ven001FormIncluirItemMontadoBtn', 'type'=>'submit', 'reload' => false, 'value' => 'Incluir', 'align' => 'right', 'style' => 'float:right;', 'reject' => true),
                )
            )
        );

        $form->definirFormularioArray($formArray);
        /**
        * PARTE 3: Processamento do formulário (se foi enviado)
        *            E carregamento dos valores (se não foi enviado e possui acaoId)
        */
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			if ($formCodigo == 1) {
                echo gGeraAlertMsgErroForm($form);
                // Form sem erros
            } else if($form->campoValorEnviado('checkEnviado') == '') {
				echo <<<EOT
				    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						var buttons = {
							"Continuar": function() {
								$('#{$g['divId']} #checkEnviado').val('true');
								$('#ven001FormIncluirItemMontadoBtn').trigger('click');
								$(this).dialog("destroy").remove();
							},
							"Cancelar": function() { $(this).dialog("destroy").remove(); }
						};
						dialogConfirm('Atenção', 'Deseja incluir os sub-itens do produto montado?', buttons);
				    } </script></div>
EOT;
			} else {
	            // Form com erros
	            if ($formCodigo == 2) {
	            	$retorno = $VEN001->incluirSubItensOrcamento($form->campoValorEnviado('T003_Id'), $form->campoValorEnviado('D009_Id'), gCorrigeNumeroInverte($form->campoValorEnviado('T004_Quantidade')));
	            	if($retorno === true){
	            		echo gGeraAlertMsg('Operação OK', 'Sub-itens incluídos com sucesso!');
	                    echo <<<EOT
	                        <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
	                            fecharJanela('{$g['divId']}');
	                            var divReload = $('#{$form->campoValorEnviado('divForm')}').data('vars').match(/divReload=([a-z0-9]+)/);
	                            var divOutro = $('#{$form->campoValorEnviado('divForm')}').data('vars').match(/divOutro=([a-z0-9]+)/);
	                            divRefresh(divReload[1]); // grid
								divRefresh(divOutro[1], true, false, 2); // totais
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

	case 'ven001FormConfiguracoes':
        /**
        * PARTE 1: Variavéis envidas para o formulário
        */
        // Variável indica qual tab do form será exibida
        $tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
        // Utilizada em UPDATE, é o ID do registro que está sendo editado
		$T003_Id = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($r_T003A_Id)) ? $r_T003A_Id : false;

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
        $form_tabela       = 'T003A';
        $form_campo_pk     = 'T003A_Id';
        $form_parent       = 'T003_Id';
        $form_parent_valor = $T003_Id;
        $enviar_btnId 	 = 'ven001FormConfiguracoesBtn';
        $enviar_acaoHref = '/ven/ven001/form/ven001FormConfiguracoes/';
        
        //form editor
        $form_editor = array(
            'geral' => '{"T003A_Flag_Ratear_Frete_Custo":{"top":7,"left":39.125,"width":63,"hidden":0},"T003A_Flag_Conversao_Unidade":{"top":30,"left":205.859375,"width":63,"hidden":0},"ven001FormConfiguracoesBtn":{"top":74,"left":368,"width":null,"hidden":0}}',
        );

        // novo formulário
        $form = new Formulario('ven001FormConfiguracoes');

        // Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
        $formArray = array(
            'campos' => array(
                'geral' => array(
					'T003A_Flag_Ratear_Frete_Custo' => array('name' => 'T003A_Flag_Ratear_Frete_Custo', 'label'=>'Ratear frete no custo quando for por conta do remetente','type' => 'select','select' => 
						array(
							array('title' => '...', 'value' => ''),
							array('title' => 'Não', 'value' => 'N'),
							array('title' => 'Sim', 'value' => 'S'),						
						)
					),
					'T003A_T003_Id' => array('name' =>'T003A_T003_Id', 'label'=>'T003A_T003_Id', 'value' => $T003_Id, 'type' => 'hidden','readonly' => true),
                	'ven001FormConfiguracoesBtn' => array('name' => 'ven001FormConfiguracoesBtn', 'type'=>'submit', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true),
                )
            )
        );

		if ($g['C031']['utilizaUnidadeConversaoVenda'] == 'S') {
			$formArray['campos']['geral']['T003A_Flag_Conversao_Unidade'] = array('name' => 'T003A_Flag_Conversao_Unidade', 'label' => 'Conversão de unidade','type' => 'select','select' => 
																					array(
																						array('title' => 'Não', 'value' => 'N'),
																						array('title' => 'Sim', 'value' => 'S'),						
																					)
																				);
		}
        
        $form->definirFormularioArray($formArray);
        /**
        * PARTE 3: Processamento do formulário (se foi enviado)
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
						if($form->campoValorEnviado('T003A_Flag_Conversao_Unidade') == 'S'){
							// Aqui é uma segurança caso o cliente não utilize unidade conversão e mudar para usar
							// E o orçamento já foi criado antes de alterar a opção, repassa os valores para tabela estendida
							mysqli_query("UPDATE T004A 
							 		   LEFT JOIN T004 ON T004_Id = T004A_T004_Id
											 SET T004A_Quantidade_Comercial     = T004_Quantidade,
												 T004A_Preco_Unitario_Comercial = T004_Valor_Preco_Unitario
									   	   WHERE T004_T003_Id ='{$T003_Id}' 
										 	 AND T004A_Quantidade_Comercial <= 0
										 	 AND T004A_Preco_Unitario_Comercial <= 0");	
											 
						} elseif ($form->campoValorEnviado('T003A_Flag_Conversao_Unidade') == 'N') {
							$totalizarVenda = false;
							// Se tiver um item com unidade conversão setada precisa totalizar a venda com os novos valores
							$T004 = mysql_query("SELECT T004_Id 
							                       FROM T004 
											  LEFT JOIN T004A ON T004A_T004_Id = T004_Id
												  WHERE T004_T003_Id = '{$T003_Id}'
												    AND T004A_D184_Id > 0");
							if(mysql_num_rows($T004) > 0){
								$totalizarVenda = true;
							}
											
							mysqli_query("UPDATE T004A 
									   LEFT JOIN T004 ON T004_Id = T004A_T004_Id
								 		     SET T004_Quantidade = T004A_Quantidade_Comercial,
											 	 T004A_Quantidade_Comercial     = 0,
									 			 T004A_Preco_Unitario_Comercial = 0,
											     T004A_D184_Id                  = 0
								  		   WHERE T004_T003_Id ='{$T003_Id}' 
								  			 AND T004A_Quantidade_Comercial > 0
								  			 AND T004A_Preco_Unitario_Comercial > 0");	

							if($totalizarVenda){
								// Da um UPDATE para chamar a trigger e realizar o gravar totalizacao
								mysql_query("UPDATE T003 SET T003_Flag_Status_Orcamento = T003_Flag_Status_Orcamento
								                       WHERE T003_Id = '{$T003_Id}'");
							}
						}

                        // Imprime uma mensagem e fecha a janela
                        echo gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!');
                        echo <<<EOT
	                        <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
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

	case 'ven001FormAssociarItemProduto':
		global $g;
		require_once("prog/ven/ven001/form_func-js.php");
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$T004_Id = (isset($r_T004_Id)) ? $r_T004_Id : false;

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
		$form_tabela = '';
		$form_campo_pk = '';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'ven001FormAssociarItemProduto_Btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/ven/ven001/form/ven001FormAssociarItemProduto/';

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
			where (D009_C004_Id = '{$g['empresaAtual']}' AND D001_Flag_Ativo != 'N')
			AND NOT EXISTS (
				SELECT 1
				FROM D009A D009A_Visibilidade
				WHERE D009A_Visibilidade.D009A_D009_Id=D009_Id
				  AND D009A_Visibilidade.D009A_Flag_Pertence_Empresa='N'
			)
			{$filtro}
			order by D001_Codigo_Produto
EOT;
		$pesquisaGrid = array(
			'D001',																								// tabela
			'D009_Id',																							// chave primaria
			'D001_Codigo_Produto,D001_Descricao_Produto,D082_Marca,D037_Unidade,D009_Quantidade_Estoque',// campos
			$pesquisaGridExtra,																					// extras sql
			'Pesquisa de Produto',																				// título da janela
			'Código,Descrição,Marca,Un,Estoque',																// descrição dos campos
			'campoProcuraProdutoAssociarItemOrc',																		// callback js
			$g['divId'],																						// divId
			''																									// parametro extra
		);
		
		// novo formulário
		$form = new Formulario('ven001FormAssociarItemProduto');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'Procura_Produto_Associar' => array('name' => 'Procura_Produto_Associar' , 'label' => 'Produto', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGrid, 'length' => array(55,50)),
					// Campos Hidden
					'D001_Codigo_Produto' => array('name' => 'D001_Codigo_Produto', 'label' => 'D001_Codigo_Produto', 'type' => 'hidden', 'restricao' => false),
					'D009_Id' => array('name' => 'D009_Id', 'label' => 'D009_Id', 'type' => 'hidden'),
					'T004_Id' => array('name' => 'T004_Id', 'label' => 'T004_Id','value' => $T004_Id, 'type' => 'hidden'),
				),
			)
		);
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
					// Gerar Boleto
					//$form->campoValorEnviado(T117_D024_Id)
					$T004_Id = $form->campoValorEnviado('T004_Id');
					$retorno = $VEN001->associarProduto($form);
					if ($retorno === true) {
						$alert = gGeraAlertMsg('Operação OK', 'Produto associado com sucesso!.', false);
						echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							fecharJanela("{$g['divId']}");
							//divRefresh('{$g['divIdParent']}', true);
							// Ação: abrir nova janela do browser 
							// Alerta: função do sistema antigo, chama uma arquivo diretamente do sistema antigo
							//nova_janela('/hardness3/hardness/almoxarifado/imprimir_pedido_almoxarifado_2.php?T005_Id='+encodeURIComponent('{$form->campoValorEnviado('T005_Id')}'),0,0,750,500,'yes','no','no','no','yes','no','Impressao_Pedido');
						} </script></div>
EOT;
					} else {
						echo gGeraAlertMsg('Error', 'Erro:<br />' . $retorno);
					}
				}
			}


		/**
		 * PARTE 4: Gerar o formulário
		 */
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);
        /**
		* Botões
		*/
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		//$botoesEsq .= '<button type="submit" id="est004PreCadastrarProdutoBtn">Pré-Cadastrar produto</button>';
		//$botoesEsq .= '<button type="submit" id="est004VincularOC">Ordem de Compra</button>';
		$botoesDir .= '<button type="submit" id="ven001FormAssociarItemProduto_Btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		//$botoesDir .= '<button onclick="">Novo</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo $g['smarty']->fetch('buttons-floatBottom.tpl');
		// Ações dos botões
		
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
			$('#est004PreCadastrarProdutoBtn').unbind('click').bind('click', function(data) {
				//var id = $('#{$g['divId']} #T003_D024_Id').val();
				abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Pré-Cadastrar Produto', '/est/est008/content/est008ContentPreCadastrarProduto/','&T059_Id=' + encodeURIComponent('{$array['todosCampos']['T059_Id']}') + '&T010_Id=' + encodeURIComponent('{$array['todosCampos']['T010_Id']}'), [550,400]);
				return false;
			});

			$('#est004VincularOC').unbind('click').bind('click', function(data) {
				fecharJanela("{$g['divId']}");
				abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Selecione o Item aqui', '/est/est004/content/est004ContentAssociarItemNovo/', '&acaoId=' + encodeURIComponent('{$T014_Id}'), [950,500]);
			});
		} </script></div>
EOT;

		/**
		 * PARTE 5: Exibir o formulário
		 */
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('form.tpl');
	break;
}
