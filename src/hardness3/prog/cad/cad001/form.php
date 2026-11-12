<?php
namespace hardness;
require_once('prog/cad/cad001/form_func-js.php');
//Classes
require_once("bibliotecas/classes/CAD001.php");
$CAD001 = new CAD001();

/**
* Ações
*/
switch ($g['r'][3]) {
	case 'cad001FormCadastro':
		global $g;
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$inclusaoOrcamento = (isset($_REQUEST['inclusaoOrcamento']) && $_REQUEST['inclusaoOrcamento'] == 'S') ? true : false;

		/**
		* PARTE 2: Definição do formulário
		*/
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = true;
		if ($formMultiTab) {
			$formId = (isset($r_formcad001FormCadastro)) ? $r_formcad001FormCadastro['formId'] : $r_formId;
		} else {
			$formId = uniqid();
		}

		// novo formulário
		$form = new Formulario('formcad001FormCadastro');

		$WHERE_D020 = "WHERE D020_D018_Id = '0'";
		if ($form->campoValorEnviado('D024_D018_Id')) {
			$WHERE_D020 = "WHERE D020_D018_Id = '{$form->campoValorEnviado('D024_D018_Id')}'";
		} else if(!empty($acaoId)) {
			$D024 = mysql_fetch_array(mysql_query("SELECT * FROM D024 WHERE D024_Id = '{$acaoId}'"));
			if($D024['D024_D018_Id'] > 0){
				$WHERE_D020 = "WHERE D020_D018_Id = '{$D024['D024_D018_Id']}'";
			}
		}

		$campoPreCadastro = ((isset($g['c029Ids'][183]) && !isset($g['c029Ids'][103])) || (isset($g['c029Ids'][231]))) ? true : false;
		$extraCampoPreCadastro = (isset($g['c029Ids'][183]) && !isset($g['c029Ids'][103])) ? "onfocus='this.initialSelect = this.selectedIndex;' onchange='this.selectedIndex = this.initialSelect;'" : "";

		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'D024';
		$form_campo_pk = 'D024_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad001FormCadastro_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad001/form/cad001FormCadastro/';

		// Editor
		$form_editor = array(
			'cadastro' => '{"D024_Data_Ultima_Analise":{"top":398,"left":660.171875,"width":88,"hidden":0},"D024_Nome_Empresa":{"top":7,"left":53.1875,"width":440,"hidden":0},"D024_Flag_Pessoa_Fisica_Juridica":{"top":7,"left":637.84375,"width":144,"hidden":0},"D024_Cod_Pais_Celular":{"top":30,"left":5,"width":38,"hidden":1},"D024_Nome_Fantasia":{"top":30,"left":40.34375,"width":440,"hidden":0},"D024_Flag_Contribuinte":{"top":30,"left":591.375,"width":144,"hidden":0},"buttonSearchIE":{"top":28,"left":832,"width":null,"hidden":0},"D024_Cod_Pais_Telefone_2":{"top":53,"left":5,"width":38,"hidden":1},"D024_Cod_Pais_Telefone_1":{"top":53,"left":16,"width":38,"hidden":1},"D024_Inscricao_Estadual":{"top":53,"left":73.359375,"width":168,"hidden":0},"D024_Rg":{"top":53,"left":665.375,"width":136,"hidden":0},"D024_Cod_Pais_Fax":{"top":76,"left":5,"width":38,"hidden":1},"D024_Cnpj":{"top":76,"left":53.8125,"width":168,"hidden":0},"buttonSearchCnpj":{"top":74,"left":272,"width":null,"hidden":0},"D024_Cpf":{"top":76,"left":652.53125,"width":136,"hidden":0},"D024_Apelido":{"top":76,"left":917.609375,"width":124,"hidden":1},"D024_Cep":{"top":99,"left":67.25,"width":72,"hidden":0},"buttonSearchCep1":{"top":97,"left":176,"width":null,"hidden":0},"D024_Codigo_Regime_Tributario":{"top":99,"left":653.34375,"width":144,"hidden":0},"D024_Endereco":{"top":122,"left":42.78125,"width":280,"hidden":0},"D024_Numero":{"top":122,"left":642.75,"width":136,"hidden":0},"D024_Cnpj_Matriz":{"top":122,"left":909.921875,"width":128,"hidden":1},"D024_Complemento":{"top":145,"left":21.390625,"width":280,"hidden":0},"D024_Bairro":{"top":145,"left":581.1875,"width":200,"hidden":0},"D024_D018_Id":{"top":168,"left":67.875,"width":79,"hidden":0},"D024_D020_Id":{"top":168,"left":575.671875,"width":208,"hidden":0},"incluirCidade":{"top":166,"left":848,"width":null,"hidden":0},"D024_Flag_Revenda_Consumidor":{"top":168,"left":1172.921875,"width":112,"hidden":1},"D024_D030_Id":{"top":191,"left":60.515625,"width":287,"hidden":0},"D024_Nome_Contato":{"top":191,"left":579.34375,"width":200,"hidden":0},"D024_C004_Id":{"top":191,"left":1183.4375,"width":86,"hidden":1},"D024_DDD_Telefone_1":{"top":214,"left":31.96875,"width":40,"hidden":0},"D024_Telefone_1":{"top":214,"left":144,"width":120,"hidden":0},"D024_DDD_Celular":{"top":214,"left":647.015625,"width":40,"hidden":0},"D024_Celular":{"top":214,"left":736,"width":88,"hidden":0},"D024_D039_Id":{"top":214,"left":919.078125,"width":96,"hidden":1},"D024_DDD_Telefone_2":{"top":237,"left":39.109375,"width":40,"hidden":0},"D024_Telefone_2":{"top":237,"left":143.9375,"width":120,"hidden":0},"D024_DDD_Fax":{"top":237,"left":663.53125,"width":40,"hidden":0},"D024_Fax":{"top":237,"left":736,"width":88,"hidden":0},"D024_E_Mail":{"top":259.96875,"left":58.703125,"width":280,"hidden":0},"D024_Web_Site":{"top":259.96875,"left":578.328125,"width":200,"hidden":0},"D024_E_Mail_Nota":{"top":282.96875,"left":15.90625,"width":280,"hidden":0},"D024_Flag_Cliente":{"top":282.96875,"left":688.296875,"width":96,"hidden":0},"D024_E_Mail_Boleto":{"top":305.953125,"left":-0.625,"width":280,"hidden":0},"D024_Flag_Fornecedor":{"top":305.953125,"left":666.28125,"width":96,"hidden":0},"D024_Observacoes":{"top":328.96875,"left":30.5625,"height":60,"width":440,"hidden":0},"D024_Data_Fundacao":{"top":328.953125,"left":619.8125,"width":88,"hidden":0},"D024_Flag_Pre_Cadastro":{"top":351.953125,"left":644.875,"width":96,"hidden":0},"D024_Data_Cadastro":{"top":374.953125,"left":654.046875,"width":88,"hidden":0},"D024_D046_Id":{"top":397.96875,"left":59.296875,"width":287,"hidden":0}}',
			'cobranca' => '{"D024_Cobranca_Rua":{"top":7,"left":91.99998474121094,"width":232,"hidden":0},"D024_Cobranca_Complemento":{"top":30,"left":73.99998474121094,"width":232,"hidden":0},"D024_Cobranca_Bairro":{"top":53,"left":108.99998474121094,"width":232,"hidden":0},"buttonSearchCep2":{"top":74,"left":223.99998474121094,"width":null,"hidden":0},"D024_Cobranca_Cep":{"top":76,"left":118.99998474121094,"width":70,"hidden":0},"D024_DDD_Cobranca_Telefone":{"top":99,"left":96.99998474121094,"width":35,"hidden":0},"D024_Cobranca_Telefone":{"top":99,"left":191.99998474121094,"width":95,"hidden":0},"D024_DDD_Cobranca_Fax":{"top":122,"left":119.99998474121094,"width":35,"hidden":0},"D024_Cobranca_Fax":{"top":122,"left":191.99998474121094,"width":95,"hidden":0},"D024_Cobranca_Email":{"top":145,"left":113.99998474121094,"width":232,"hidden":0},"D024_D018_Cobranca":{"top":168,"left":124.99998474121094,"width":80,"hidden":0},"D024_D020_Cobranca":{"top":191,"left":104.99998474121094,"width":232,"hidden":0},"D024_D030_Cobranca":{"top":214,"left":117.99998474121094,"width":232,"hidden":0},"D024_D062_Id":{"top":260,"left":50.99998474121094,"width":232,"hidden":0},"D024_Condicoes_Pagamento":{"top":283,"left":16,"width":232,"hidden":0},"D024_Praca_Pagamento":{"top":374.9999694824219,"left":631.9999847412109,"width":120,"hidden":0}}',
			'entrega' => '{"buttonSearchCep3":{"top":74,"left":175.99998474121094,"width":null,"hidden":0},"D024_Entrega_Rua":{"top":7,"left":44,"width":232,"hidden":0},"D024_Entrega_Complemento":{"top":30,"left":26,"width":232,"hidden":0},"D024_Entrega_Bairro":{"top":53,"left":61,"width":232,"hidden":0},"D024_Entrega_Cep":{"top":76,"left":71,"width":70,"hidden":0},"D024_DDD_Entrega_Fax":{"top":99,"left":72,"width":30,"hidden":0},"D024_Entrega_Fax":{"top":99,"left":143.99998474121094,"width":75,"hidden":0},"D024_Entrega_Email":{"top":122,"left":66,"width":232,"hidden":0},"D024_D018_Entrega":{"top":145,"left":76.99998474121094,"width":43,"hidden":0},"D024_D020_Entrega":{"top":168,"left":57,"width":232,"hidden":0},"D024_D030_Entrega":{"top":191,"left":70,"width":232,"hidden":0}}',
			'fiscal' => '{"tituloVendas":{"top":-1000,"left":145,"width":null,"hidden":1},"tituloCompras":{"top":-1000,"left":223,"width":null,"hidden":1},"D024_Numero_Suframa":{"top":7,"left":54.125,"width":184,"hidden":0},"D024_Empresa_Emissora":{"top":7,"left":588.421875,"width":0,"hidden":1},"D024_Inscricao_Municipal":{"top":30,"left":44.34375,"width":184,"hidden":0},"D024_Observacao_Isento_ICMS":{"top":30,"left":505,"height":59,"width":342,"hidden":1},"D024_Codigo_Beneficio_Fiscal":{"top":53,"left":60.234375,"width":184,"hidden":0},"D024_TTD":{"top":76,"left":116.5,"width":185,"hidden":0},"D024_Despacho_Concessorio":{"top":99,"left":24.15625,"width":184,"hidden":0},"D024A_Exportacao_UF_Embarque":{"top":122,"left":43,"width":50,"hidden":0},"D024A_Exportacao_Local_Embarque":{"top":145,"left":27,"width":275,"hidden":0},"D024_Flag_ICMS_Substituicao_Tributaria_Substituto":{"top":99,"left":648,"width":64,"hidden":1},"D024_Flag_Isento_ICMS":{"top":122,"left":570,"width":64,"hidden":1},"D024_CNAE":{"top":145,"left":667.3125,"width":146,"hidden":1},"D024_D006_Id":{"top":168,"left":69,"width":368,"hidden":1},"D024_Percentual_Carga_Media":{"top":168,"left":622.0625,"width":86,"hidden":1},"D024_Flag_Suframa":{"top":191,"left":11,"width":368,"hidden":1},"D024_Percentual_Reducao_ICMS":{"top":259.96875,"left":64,"width":75,"hidden":1},"D024_Aliquota_ICMS":{"top":282.96875,"left":70,"width":75,"hidden":1},"D024_Flag_Isento_ICMS_Venda":{"top":305.96875,"left":24,"width":64,"hidden":1},"D024_D059_Id":{"top":306,"left":239,"width":400,"hidden":1},"D024_Flag_Isento_PIS_COFINS":{"top":328.96875,"left":22,"width":64,"hidden":1},"D024_Situacao_Tributaria_PIS_COFINS":{"top":329,"left":239,"width":400,"hidden":1},"D024_Flag_Isento_IPI":{"top":351.96875,"left":71,"width":64,"hidden":1},"D024_D088_Id":{"top":352,"left":239,"width":400,"hidden":1},"D024_Observacao_Isento_IPI":{"top":374.96875,"left":70,"height":45,"width":392,"hidden":1}}',
			'comercial' => '{"D024_Flag_Destino_Produto":{"top":283,"left":506,"width":296,"hidden":0},"D024_Flag_Lista_Preco":{"top":7,"left":73,"width":112,"hidden":0},"D024_C007_Vendedor_Interno":{"top":30,"left":44,"width":110,"hidden":0},"D024_Percentual_Comissao_Interno":{"top":30,"left":269,"width":104,"hidden":0},"D024_Instrucao_Pagamento":{"top":30,"left":464,"height":62,"width":296,"hidden":0},"D024_C007_Vendedor_Externo":{"top":53,"left":51,"width":110,"hidden":0},"D024_Percentual_Comissao_Externo":{"top":53,"left":269,"width":104,"hidden":0},"D024_D021_Id":{"top":76,"left":65,"width":110,"hidden":0},"D024_Percentual_Comissao":{"top":76,"left":269,"width":104,"hidden":0},"D024_Data_Ativo":{"top":99,"left":87,"width":104,"hidden":0},"D024_IPV_1":{"top":99,"left":279,"width":104,"hidden":0},"D024_Observacao_Financeiro":{"top":99,"left":472,"height":62,"width":296,"hidden":0},"D024_Data_Inativo":{"top":122,"left":79,"width":104,"hidden":0},"D024_Flag_Comercial":{"top":145,"left":40,"width":110,"hidden":0},"D024_Flag_Permite_ACP":{"top":168,"left":41,"width":110,"hidden":0},"D024_Observacao_Comercial":{"top":168,"left":473,"height":59,"width":296,"hidden":0},"D024_Flag_Frete":{"top":191,"left":112,"width":304,"hidden":0},"D024_D022_Id":{"top":214,"left":61,"width":303,"hidden":0},"D024_Id_Matriz":{"top":237,"left":106,"width":281,"hidden":0},"D024_Dias_Envio_Email_Orc_Apos_Emissao":{"top":237,"left":455,"width":72,"hidden":0},"D024_Dias_Envio_Email_Orc_Antes_Vencer":{"top":237,"left":685,"width":72,"hidden":0},"D024_Dia_Visita":{"top":260,"left":92,"width":296,"hidden":0},"D024_Flag_Transporta_Diferentes_UF":{"top":283,"left":13,"width":110,"hidden":0},"D024_Horario_Entrega":{"top":306,"left":988,"width":100,"hidden":1},"D024_Flag_Tempo":{"top":283,"left":971.359375,"width":110,"hidden":1},"D024_Tipo_Pagamento":{"top":306,"left":649.546875,"width":110,"hidden":1},"D024_Dias_Carencia_Pagamento":{"top":329,"left":638.34375,"width":100,"hidden":1},"D024_Dia_Semana_Pagamento":{"top":352,"left":644.9375,"width":110,"hidden":1},"D024_Dia_Mes_Pagamento":{"top":375,"left":649.34375,"width":100,"hidden":1},"D024_Flag_Cotacao_Compra":{"top":398,"left":585.1875,"width":110,"hidden":1}}',
			'financeiro' => '{"D024A_D031_Id":{"top":76,"left":568.234375,"width":175,"hidden":0},"D024_Flag_Liberado":{"top":7,"left":68.890625,"width":64,"hidden":0},"D024_Pgto_Primeiro_Vcto_Tipo":{"top":7,"left":612.28125,"width":112,"hidden":0},"D024_Prazo_Parcela":{"top":7,"left":768,"width":56,"hidden":0},"D024_Flag_Ativo":{"top":30,"left":57.84375,"width":64,"hidden":0},"D024_Pgto_Dias_Vcto_Tipo":{"top":30,"left":564.640625,"width":112,"hidden":0},"D024_Dias_Vencimento":{"top":30,"left":768,"width":56,"hidden":0},"D024_Flag_Restricao":{"top":30,"left":1019.9375,"width":64,"hidden":1},"D024_Valor_Limite":{"top":53,"left":39.5,"width":150,"hidden":0},"D024_D027_Id":{"top":53,"left":607.6875,"width":175,"hidden":0},"D024_Flag_Protestar":{"top":76,"left":104.1875,"width":64,"hidden":0},"D024_Quantidade_Parcelas":{"top":99,"left":705.53125,"width":56,"hidden":0},"D024_Flag_Permite_Agrupamentos":{"top":99,"left":29.5625,"width":64,"hidden":0},"D024_Cobrar_Frete_Primeira_Parcela":{"top":122,"left":607.953125,"width":64,"hidden":0},"D024_Flag_Empresas_Grupo":{"top":122,"left":65.109375,"width":64,"hidden":0},"D024_Cobrar_IPI_Primeira_Parcela":{"top":145,"left":617.71875,"width":64,"hidden":0},"D024_Observacao_Administracao":{"top":145,"left":42.5625,"height":62,"width":312,"hidden":0},"D024_Cobrar_ST_Primeira_Parcela":{"top":168,"left":617.765625,"width":64,"hidden":0},"D024_Percentual_Desconto_Tabela":{"top":306,"left":24.15625,"width":146,"hidden":1},"D024_Valor_Limite_Manual":{"top":329,"left":-12.90625,"width":176,"hidden":1}}',
		);
		
		$C004=mysql_query("select * from C004 where C004_Id='{$g['empresaAtual']}'");
		$mC004=mysql_fetch_array($C004);
		$CNPJ_Empresa_Corrente = substr($mC004['C004_Cnpj'], 0, -8);


		$disabledCadastros = false;
		$disabledFiscal = false;
		$disabledComercial = false;
		$disabledFinanceiro = false;

		//Desabilitar perfil Cadastros
		if(isset($g['c029Ids'][231])){
			$disabledCadastros = true;
		}

		//Desabilitar perfil Fiscal
		if(isset($g['c029Ids'][234])){
			$disabledFiscal = true;
		}

		//Desabilitar perfil Comercial
		if(isset($g['c029Ids'][235])){
			$disabledComercial = true;
		}

		//Desabilitar perfil Financeiro
		if(isset($g['c029Ids'][236])){
			$disabledFinanceiro = true;
		}

		//Cadastro de cidade
		if(isset($g['c029Ids'][45])){
			$cadastrarCidade = 'button';
		} else {
			$cadastrarCidade = 'hidden';
		}
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'cadastro' => array(
					'D024_Nome_Empresa' => array('name' => 'D024_Nome_Empresa', 'extra' => "onchange=\"campoNomeFantasia(this, '{$g['divId']}');\"", 'label' => 'Nome', 'readonly' => $disabledCadastros,  'type' => 'text', 'length' => array(60,255)),
					'D024_Flag_Pessoa_Fisica_Juridica' => array('name' => 'D024_Flag_Pessoa_Fisica_Juridica', 'disabled' => $disabledCadastros, 'label' => 'Pessoa', 'type' => 'select', 'select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Jurídica', 'value' => 'J'),
							array('title' => 'Física', 'value' => 'F'), 
							array('title' => 'Internacional', 'value' => 'I'), 
						)
					),
					'D024_Nome_Fantasia' => 			array('name' => 'D024_Nome_Fantasia', 'label' => 'Fantasia', 'readonly' => $disabledCadastros, 'type' => 'text', 'length' => array(50,255)),
					'D024_Flag_Revenda_Consumidor' => 	array('name' => 'D024_Flag_Revenda_Consumidor', 'label' => 'Tipo', 'disabled' => $disabledCadastros, 'type' => 'select', 'restricao' => false, 'select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),   
							array('title' => 'Revenda', 'value' => '1'),   
							array('title' => 'Consumidor Atacado', 'value' => '2'),   
							array('title' => 'Consumidor Varejo', 'value' => '3')   
						)
					),   
					'D024_Flag_Contribuinte' => 	array('name' => 'D024_Flag_Contribuinte', 'label' => 'Tipo Contribuinte', 'disabled' => $disabledCadastros, 'type' => 'select', 'select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => '0 - Não contribuinte', 'value' => '0' ),   
							array('title' => '1 - Consumidor Final', 'value' => '1'),   
							array('title' => '2 - Revenda', 'value' => '2'),   
							array('title' => '3 - Produtor rural', 'value' => '3'),
							array('title' => '4 - Simples', 'value' => '4'),   
							array('title' => '5 - Industria', 'value' => '5'),   
							array('title' => '8 - Industria Triangular', 'value' => '8')   
						)
					),
					'D024_Inscricao_Estadual' => 		array('name' => 'D024_Inscricao_Estadual',	'label' => 'Insc. Estadual', 'readonly' => $disabledCadastros, 'type' => 'text', 'length' => array(20,255)),
					'D024_Rg' => 	array('name' => 'D024_Rg', 'label' => 'RG', 'type' => 'text', 'readonly' => $disabledCadastros, 'length' => array(14,30)),
					'D024_Cnpj' => 	array('name' => 'D024_Cnpj', 'label' => 'CNPJ', 'type' => 'cnpj', 'readonly' => $disabledCadastros, 'length' => array(18,18)),
					'D024_Cnpj_Matriz' => 	array('name' => 'D024_Cnpj_Matriz', 'label' => 'CNPJ Matriz', 'type' => 'cnpj', 'readonly' => $disabledCadastros, 'length' => array(20,20)),
					'buttonSearchCnpj' => 	array('name' => 'buttonSearchCnpj',	'label' => '', 'type' => 'button', 'readonly' => $disabledCadastros, 'extra' => "style='padding: 0; heigth: 20px; width: 50px;' onclick='pesquisaCNPJClientes(\"{$g['divId']}\", \"{$g['divIdRoot']}\",\"D024_Cnpj\")'", 'align'=>'left', 'value'=>'Buscar', 'reject'=>true, 'reload' => false),
					'buttonSearchIE' => 	array('name' => 'buttonSearchCnpj',	'label' => '', 'type' => 'button', 'readonly' => $disabledCadastros, 'extra' => "style='padding: 0; heigth: 20px; width: 50px;' onclick='pesquisaIEClientes(\"{$g['divId']}\", \"{$g['divIdRoot']}\",\"D024_Cnpj\")'", 'align'=>'left', 'value'=>'Buscar IE', 'reject'=> true, 'reload' => false),
					'D024_Json_Sintegra' => array('name' => 'D024_Json_Sintegra', 'label'=> 'Sintegra',  'uppercase' => false, 'type' => 'hidden','readonly' => $disabledCadastros),
					'D024_Data_Json_Sintegra' => array('name' => 'D024_Data_Json_Sintegra', 'label'=> 'Data atualizada Sintegra','type'=>'hidden','readonly' => $disabledCadastros),

					'D024_Cpf' => 	array('name' => 'D024_Cpf', 'label' => 'CPF', 'type' => 'cpf', 'readonly' => $disabledCadastros, 'length' => array(11,14)),
					'D024_Cep' => 	array('name' => 'D024_Cep',	'label' => 'CEP', 'type' => 'cep', 'readonly' => $disabledCadastros, 'length' => array(10,15)),
					'buttonSearchCep1' => 	array('name' => 'buttonSearchCep1',	'label' => '', 'type' => 'button', 'readonly' => $disabledCadastros, 'extra' => "style='padding: 0; heigth: 20px; width: 50px;' onclick='pesquisaCep(false,\"{$g['divId']}\", \"{$g['divIdRoot']}\",\"D024_Cep\", {\"EnderecoDb\": \"D024_Endereco\", \"Cep\": \"D024_Cep\", \"BairroDb\": \"D024_Bairro\", \"UfDb\": \"D024_D018_Id\",\"CidadeDb\": \"D024_D020_Id\", \"PaisDb\": \"D024_D030_Id\"})'", 'align'=>'left', 'value'=>'Buscar', 'reject'=>true, 'reload' => false),
					'D024_Endereco' => 	array('name' => 'D024_Endereco', 'label' => 'Endereço',	'type' => 'text', 'readonly' => $disabledCadastros, 'length' => array(50,255)),
					'D024_Numero' => array('name' => 'D024_Numero', 'label' => 'Número', 	'type' => 'text', 'readonly' => $disabledCadastros, 'length' => array(10,50)),
					'D024_Complemento' => array('name' => 'D024_Complemento', 'label' => 'Complemento', 'type' => 'text', 'readonly' => $disabledCadastros, 'length' => array(20,200)),//'align'=>'right', 'style'=>'float:right; margin-top: 0px', 
					'D024_D018_Id' => array('name' => 'D024_D018_Id', 'label' => 'UF',     'type' => 'select','align'=>'left', 'style'=>'float:left', 'disabled' => $disabledCadastros, 'extra' => "onchange=\"alteraEstado(this, '{$g['divId']}')\"", 'select' => 
						gGeraSelect('D018', 'D018_Id', 'D018_UF','', true)
					),
					'D024_D020_Id' => array('name' => 'D024_D020_Id', 'label' => 'Cidade', 'disabled' => $disabledCadastros, 'type' => 'select', 'select' => 
						gGeraSelect('D020', 'D020_Id', 'D020_Nome_Cidade', $WHERE_D020, true)
					),
					'incluirCidade' => array('name' => 'incluirCidade', 'type' => $cadastrarCidade , 'reload' => false, 'value' => 'Incluir', 'reject' => true),
					'D024_D030_Id' => array('name' => 'D024_D030_Id', 'label' => 'País', 'type' => 'select', 'disabled' => $disabledCadastros, 'align'=>'left', 'style'=>'float:left', 'select' => 
						gGeraSelect('D030', 'D030_Id', 'D030_Nome_Pais,D030_Codigo_Area', 'order by D030_Nome_Pais', true)
					),
					'D024_Bairro' => array('name' => 'D024_Bairro', 'label' => 'Bairro', 'type' => 'text', 'readonly' => $disabledCadastros, 'length' => array(25,30)),
					'D024_Cod_Pais_Telefone_1' => array('name' => 'D024_Cod_Pais_Telefone_1',	'label' => '', 'readonly' => $disabledCadastros, 'type' => 'text', 'length' => array(2,2)),
					'D024_DDD_Telefone_1' => array('name' => 'D024_DDD_Telefone_1',	'label' => 'Telefone 1', 'readonly' => $disabledCadastros, 'type' => 'text', 'length' => array(1,4)),
					'D024_Telefone_1' => array('name' => 'D024_Telefone_1',	'label' => '', 'type' => 'text', 'readonly' => $disabledCadastros, 'extra' => '', 'length' => array(14,14)),
					'D024_Cod_Pais_Celular' => array('name' => 'D024_Cod_Pais_Celular', 'label' => '', 'readonly' => $disabledCadastros, 'type' => 'text', 'length' => array(2,2)),
					'D024_DDD_Celular' => array('name' => 'D024_DDD_Celular', 'label' => 'Celular', 'readonly' => $disabledCadastros, 'type' => 'text', 'length' => array(2,4)),
					'D024_Celular' => array('name' => 'D024_Celular',	'label' => '', 'type' => 'text', 'readonly' => $disabledCadastros, 'length' => array(14,14)),
					'D024_Cod_Pais_Telefone_2' => array('name' => 'D024_Cod_Pais_Telefone_2',	'label' => '', 'readonly' => $disabledCadastros, 'type' => 'text', 'length' => array(2,2)),
					'D024_DDD_Telefone_2' => array('name' => 'D024_DDD_Telefone_2',	'label' => 'Telefone 2', 'readonly' => $disabledCadastros, 'type' => 'text', 'length' => array(1,4)),
					'D024_Telefone_2' => array('name' => 'D024_Telefone_2', 'label' => '', 'type' => 'text', 'readonly' => $disabledCadastros, 'length' => array(14,14)),
					'D024_Cod_Pais_Fax' => array('name' => 'D024_Cod_Pais_Fax', 'label' => '', 'type' => 'text', 'readonly' => $disabledCadastros, 'length' => array(2,2)),
					'D024_DDD_Fax' => array('name' => 'D024_DDD_Fax', 'label' => 'Fax', 'type' => 'text', 'readonly' => $disabledCadastros, 'length' => array(2,4)),
					'D024_Fax' => array('name' => 'D024_Fax', 'label' => '', 'type' => 'text', 'readonly' => $disabledCadastros, 'length' => array(14,14)),
					'D024_Nome_Contato' => 	array('name' => 'D024_Nome_Contato','label' => 'Contato', 'type' => 'text', 'readonly' => $disabledCadastros, 'align'=>'left', 'style'=>'float:left', 'length' => array(14,40)),
					'D024_E_Mail' => array('name' => 'D024_E_Mail', 'label' => 'E-mail', 'type' => 'text', 'readonly' => $disabledCadastros, 'length' => array(255,255)),
					'D024_E_Mail_Nota' => array('name' => 'D024_E_Mail_Nota', 'label' => 'E-mail para NF', 'readonly' => $disabledCadastros, 'type' => 'text', 'length' => array(255,255)),
					'D024_E_Mail_Boleto' => array('name' => 'D024_E_Mail_Boleto', 'label' => 'E-mail para Boleto', 'type' => 'text', 'readonly' => $disabledCadastros, 'length' => array(255,255)),
					'D024_Web_Site' => array('name' => 'D024_Web_Site', 'label' => 'Website', 'type' => 'text', 'readonly' => $disabledCadastros, 'length' => array(30,40)),
					'D024_Inscricao_Estadual' => array('name' => 'D024_Inscricao_Estadual', 'label' => 'I.E.', 'type' => 'text', 'readonly' => $disabledCadastros, 'length' => array(30,30)),
					'D024_Flag_Pre_Cadastro' => array('name' => 'D024_Flag_Pre_Cadastro', 	'label' => 'Pré-Cadastrado', 'type' => 'select', 'disabled' => $campoPreCadastro, 'extra' => $extraCampoPreCadastro, 'select' => 
						array(
							array('title' => '...', 'value' => 'S'),
							array('title' => 'Sim', 'value' => 'S', 'selected' => true), 
							array('title' => 'Não', 'value' => 'N') 
						)
					),
					'D024_Flag_Cliente' => array('name' => 'D024_Flag_Cliente', 'label' => 'Cliente', 'disabled' => $disabledCadastros, 'type' => 'select', 'select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'D024_Data_Fundacao' => array('name' => 'D024_Data_Fundacao', 'label' => 'Fundação/Nascimento', 'readonly' => $disabledCadastros, 'type' => 'data', 'length' => array(15,30)),
					'D024_Flag_Fornecedor' => array('name' => 'D024_Flag_Fornecedor', 'label' => 'Fornecedor', 'disabled' => $disabledCadastros, 'type' => 'select', 'select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'D024_D046_Id' => array('name' => 'D024_D046_Id', 'label' => 'Grupo', 'disabled' => $disabledCadastros, 'type' => 'select', 'select' => 
						gGeraSelect('D046', 'D046_Id', 'D046_Nome','', true)
					),
					'D024_D039_Id' =>  array('name' => 'D024_D039_Id', 'label' => 'Sub-Grupo', 'disabled' => $disabledCadastros, 'type' => 'select', 'select' => 
						gGeraSelect('D039', 'D039_Id', 'D039_Descricao','', true)
					),
					'D024_Codigo_Regime_Tributario' => array('name' => 'D024_Codigo_Regime_Tributario', 'disabled' => $disabledCadastros, 'label'=>'CRT', 'type' => 'select', 
						'select' => array(
							array('title' => '...', 'value' => "", 'selected' => true),
							array('title' => '1 - Simples Nacional', 'value' => "1"),
							array('title' => '2 - Simples Nacional é excesso de sublimite de receita bruta', 'value' => "2"),
							array('title' => '3 - Regime Normal', 'value' => "3")
						)
					),
					'D024_Observacoes' => array('name' => 'D024_Observacoes', 'label' => 'Observação', 'readonly' => $disabledCadastros, 'type' => 'textarea', 'length' => array(4,57)),
					'D024_Data_Cadastro' => array('name' => 'D024_Data_Cadastro', 'label' => 'Data cadastro', 'readonly' => $disabledCadastros, 'value' => date("d/m/Y"), 'type' => 'data', 'length' => array(15,14)),
					'D024_Apelido' => array('name' => 'D024_Apelido', 'label' => 'Apelido', 'readonly' => $disabledCadastros, 'type' => 'text'),
					'D024_C004_Id' => array('name' => 'D024_C004_Id', 'label' => 'Empresa', 'disabled' => $disabledCadastros, 'type' => 'select','align'=>'left', 'style'=>'float:left', 'select' => 
						gGeraSelect('C004', 'C004_Id', 'C004_Nome_Abreviado','where C004_flag_Ativo="S"', true)
					),
					'D024_Data_Ultima_Analise' => array('name' => 'D024_Data_Ultima_Analise', 'label' => 'Última Análise', 'value' => date("d/m/Y"), 'type' => 'data', 'length' => array(15,14)),
					'formId' => array('name' => 'formId', 'label' => '', 'type' => 'hidden', 'value' => $formId, 'reject' => true),
				),
				'cobranca' => array(
					'D024_Cobranca_Cep' => array('name' => 'D024_Cobranca_Cep', 'label'=>'CEP', 'type' => 'cep'),
					'buttonSearchCep2' =>     array('name' => 'buttonSearchCep2',    'label' => '', 'type' => 'button', 'extra' => "style='padding: 0; heigth: 20px; width: 50px;' onclick='pesquisaCep(false,\"{$g['divId']}\", \"{$g['divIdRoot']}\",\"D024_Cobranca_Cep\", {\"EnderecoDb\": \"D024_Cobranca_Rua\", \"Cep\": \"D024_Cobranca_Cep\", \"BairroDb\": \"D024_Cobranca_Bairro\", \"UfDb\": \"D024_D018_Cobranca\",\"CidadeDb\": \"D024_D020_Cobranca\", \"PaisDb\": \"D024_D030_Cobranca\"})'", 'align'=>'left', 'value'=>'Buscar', 'reject'=>true),
					'D024_Cobranca_Rua' => array('name' => 'D024_Cobranca_Rua', 'label'=>'Endereço', 'type' => 'text','length' => array(40,40)),
					'D024_Cobranca_Complemento' => array('name' => 'D024_Cobranca_Complemento', 'label'=>'Complemento', 'type' => 'text','length' => array(15,25)),
					'D024_Cobranca_Bairro' => array('name' => 'D024_Cobranca_Bairro', 'label'=>'Bairro', 'type' => 'text','length' => array(15,25)),
					'D024_DDD_Cobranca_Telefone' => array('name' => 'D024_DDD_Cobranca_Telefone', 'label' => 'Telefone', 'type' => 'text','length' => array(2,2)),
					'D024_Cobranca_Telefone' => array('name' => 'D024_Cobranca_Telefone', 'label' => '', 'type' => 'text','length' => array(14,14), 'align'=>'right'),
					'D024_DDD_Cobranca_Fax' => array('name' => 'D024_DDD_Cobranca_Fax', 'label' => 'Fax', 'type' => 'text','length' => array(2,2)),
					'D024_Cobranca_Fax' => array('name' => 'D024_Cobranca_Fax', 'label' => '', 'type' => 'text','length' => array(14,14), 'align'=>'right'),
					'D024_Cobranca_Email' => array('name' => 'D024_Cobranca_Email', 'label' => 'Email', 'type' => 'text','length' => array(40,40)),
					'D024_D018_Cobranca' => array('name' => 'D024_D018_Cobranca', 'label' => 'UF', 'type' => 'select','extra' => "onchange=\"alteraEstadoCobranca(this, '{$g['divId']}')\"", 'select' => 	
						gGeraSelect('D018', 'D018_Id', 'D018_UF', '', true)
					),
					'D024_D020_Cobranca' => array('name' => 'D024_D020_Cobranca', 'label' => 'Cidade','extra' => 'style="width:232px;"', 'type' => 'select', 'select' => 
						gGeraSelect('D020', 'D020_Id', 'D020_Nome_Cidade','', true)
					),
					'D024_D030_Cobranca' => array('name' => 'D024_D030_Cobranca', 'label' => 'País', 'type' => 'select', 'extra' => 'style="width:232px;"', 'select' => 
						gGeraSelect('D030', 'D030_Id', 'D030_Nome_Pais,D030_Codigo_Area', '', false, 30)  
					),
					'D024_Praca_Pagamento' => array('name' => 'D024_Praca_Pagamento', 'label' => 'CNPJ/CPF (NÃO ALTERAR)', 'type' => 'text','length' => array(40,40)),
					'D024_D062_Id' => array('name' => 'D024_D062_Id', 'label' => 'Banco Pagamento', 'extra' => 'style="width:232px;"','type' => 'select','select' =>
						gGeraSelect('D062', 'D062_Id', 'D062_Nome_Banco','', true)
					),
					'D024_Condicoes_Pagamento' => array('name' => 'D024_Condicoes_Pagamento', 'label' => 'Condições de Pagamento', 'type' => 'text','length' => array(40,40)),
				),
				'entrega' => array(
					'D024_Entrega_Cep' => array('name' => 'D024_Entrega_Cep', 'label'=>'CEP', 'type' => 'cep'),
					'buttonSearchCep3' =>     array('name' => 'buttonSearchCep3',    'label' => '', 'type' => 'button', 'extra' => "style='padding: 0; heigth: 20px; width: 50px;' onclick='pesquisaCep(false,\"{$g['divId']}\", \"{$g['divIdRoot']}\",\"D024_Entrega_Cep\", {\"EnderecoDb\": \"D024_Entrega_Rua\", \"Cep\": \"D024_Entrega_Cep\", \"BairroDb\": \"D024_Entrega_Bairro\", \"UfDb\": \"D024_D018_Entrega\",\"CidadeDb\": \"D024_D020_Entrega\", \"PaisDb\": \"D024_D030_Entrega\"})'", 'align'=>'left', 'value'=>'Buscar', 'reject'=>true),
					'D024_Entrega_Rua' => array('name' => 'D024_Entrega_Rua', 'label'=>'Endereço', 'type' => 'text','length' => array(40,40)),
					'D024_Entrega_Complemento' => array('name' => 'D024_Entrega_Complemento', 'label'=>'Complemento', 'type' => 'text','length' => array(15,25)),
					'D024_Entrega_Bairro' => array('name' => 'D024_Entrega_Bairro', 'label'=>'Bairro', 'type' => 'text','length' => array(15,25)),
					'D024_Numero_Entrega' => array('name' => 'D024_Entrega_Complemento', 'label'=>'Numero', 'type' => 'text','length' => array(15,25)),
					'D024_DDD_Entrega_Fax' => array('name' => 'D024_DDD_Entrega_Fax', 'label' => 'Fax', 'type' => 'text','length' => array(1,2)),
					'D024_Entrega_Fax' => array('name' => 'D024_Entrega_Fax', 'label' => '', 'type' => 'text','length' => array(10,9), 'align'=>'right'),
					'D024_Entrega_Email' => array('name' => 'D024_Entrega_Email', 'label' => 'Email', 'type' => 'text','length' => array(40,40)),
					'D024_D018_Entrega' => array('name' => 'D024_D018_Entrega', 'label' => 'UF', 'type' => 'select','extra' => "onchange=\"alteraEstadoEntrega(this, '{$g['divId']}')\"", 'select' => 	
						gGeraSelect('D018', 'D018_Id', 'D018_UF','', true)
					),
					'D024_D020_Entrega' => array('name' => 'D024_D020_Entrega', 'label' => 'Cidade', 'type' => 'select','extra' => 'style="width:232px;"', 'select' => 
						gGeraSelect('D020', 'D020_Id', 'D020_Nome_Cidade','', true)
					),
					'D024_D030_Entrega' => array('name' => 'D024_D030_Entrega', 'label' => 'País', 'type' => 'select','extra' => 'style="width:232px;"', 'select' => 
						gGeraSelect('D030', 'D030_Id', 'D030_Nome_Pais,D030_Codigo_Area', '', false, 30)  
					),
				),
				'fiscal' => array(
					'tituloCompras' => array('name' => 'tituloCompras', 'type' => 'titulo', 'readonly' => $disabledFiscal, 'label' => 'Compras', 'reject' => true),
					'D024_Flag_ICMS_Substituicao_Tributaria_Substituto' => array('name' => 'D024_Flag_ICMS_Substituicao_Tributaria_Substituto', 'disabled' => $disabledFiscal, 'label'=>'ICMS S.T.', 'type' => 'select','select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N'),
						)),
					'D024_Flag_Isento_ICMS' => array('name' => 'D024_Flag_Isento_ICMS', 'label'=>'Isento de ICMS (Compra) ', 'disabled' => $disabledFiscal, 'type' => 'select','select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Não', 'value' => '2'),
							array('title' => 'Sim', 'value' => '1'),
						)),
					'D024_Observacao_Isento_ICMS' => array('name' => 'D024_Observacao_Isento_ICMS', 'label'=>'Obs. ISENTO de ICMS', 'readonly' => $disabledFiscal, 'type' => 'textarea','length' => array(3,60)),
					'tituloVendas' => array('name' => 'tituloVendas', 'type' => 'titulo', 'readonly' => $disabledFiscal, 'label' => 'Vendas', 'reject' => true),
					'D024_D006_Id' => array('name' => 'D024_D006_Id', 'label'=>'CFOP Padrão', 'disabled' => $disabledFiscal, 'type' => 'select','select' => 
						gGeraSelect('D006', 'D006_Id', 'D006_Codigo_CFOP,D006_Descricao', '', true)
					), 
					'D024_Flag_Suframa' => array('name' => 'D024_Flag_Suframa', 'label'=>'Excessão para o Suframa', 'disabled' => $disabledFiscal, 'type' => 'select','select' => 
						array(
							array('title' => 'Não Definida', 'value' => '', 'selected' => true),
							array('title' => 'Sistemas fará validações', 'value' => 'S'),
							array('title' => 'Não tem suframa', 'value' => 'N'),
						)),
					'D024_Numero_Suframa' => array('name' => 'D024_Numero_Suframa', 'label' => 'Número Suframa', 'readonly' => $disabledFiscal, 'type' => 'text','length' => array(30,40)),
					'D024_Inscricao_Municipal' => array('name' => 'D024_Inscricao_Municipal', 'label' => 'Inscrição Municipal', 'readonly' => $disabledFiscal, 'type' => 'text','length' => array(30,40)),
					'D024_Percentual_Reducao_ICMS' => array('name' => 'D024_Percentual_Reducao_ICMS', 'label' => 'Redução ICMS', 'readonly' => $disabledFiscal, 'type' => 'moeda','length' => array(10,10)),
					'D024_Aliquota_ICMS' => array('name' => 'D024_Aliquota_ICMS', 'label' => 'Aliquota ICMS', 'type' => 'moeda', 'readonly' => $disabledFiscal,'length' => array(10,10)),
					'D024_Flag_Isento_ICMS_Venda' => array('name' => 'D024_Flag_Isento_ICMS_Venda', 'label' => 'Isento de ICMS venda?', 'disabled' => $disabledFiscal, 'type' => 'select', 'select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Não', 'value' => 'N'),
							array('title' => 'Sim', 'value' => 'S'),
						)
					),
					'D024_D059_Id' => array('name' => 'D024_D059_Id', 'label' => 'Situação Tributária', 'disabled' => $disabledFiscal, 'type' => 'select', 'select' => 
						gGeraSelect('D059', 'D059_Id', 'D059_Codigo_ST,D059_Descricao_Origem', '', true), 'align' => 'right', 'readonly' => $disabledFiscal, 'extra' => 'style="width: 400px; float:right;"'
					),
					'D024_Flag_Isento_PIS_COFINS' => array('name' => 'D024_Flag_Isento_PIS_COFINS', 'label' => 'Isento de PIS/COFINS?', 'disabled' => $disabledFiscal, 'type' => 'select', 'select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Não', 'value' => 'N'),
							array('title' => 'Sim', 'value' => 'S'),
						)
					),
					'D024_Situacao_Tributaria_PIS_COFINS' => array('name' => 'D024_Situacao_Tributaria_PIS_COFINS', 'disabled' => $disabledFiscal, 'label' => 'Situação Tributária', 'type' => 'select', 'select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => '01 - Operação Tributável (base de cálculo = valor da operação alíquota normal (cumulativo/não cumulativo))', 'value' => '01'),
							array('title' => '02 - Operação Tributável (base de cálculo = valor da operação (alíquota diferenciada))', 'value' => '02'),
							array('title' => '03 - Operação Tributável (base de cálculo = quantidade vendida x alíquota por unidade de produto)', 'value' => '03'),
							array('title' => '04 - Operação Tributável (tributário monofísica (alíquota zero))', 'value' => '04'),
							array('title' => '06 - Operação Tributável (alíquota zero)', 'value' => '06'),
							array('title' => '07 - Operação Isenta da Contribuição', 'value' => '07'),
							array('title' => '08 - Operação Sem Incidência da Contribuição', 'value' => '08'),
							array('title' => '09 - Operação com Suspensão da Contribuição', 'value' => '09'),
							array('title' => '99 - Outras Operações', 'value' => '99'),
						),'align' => 'right', 'extra' => 'style="width: 400px; float:right;"'
					),
					'D024_Flag_Isento_IPI' => array('name' => 'D024_Flag_Isento_IPI', 'label' => 'Isento de IPI?', 'disabled' => $disabledFiscal, 'type' => 'select', 'select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Não', 'value' => '2'),
							array('title' => 'Sim', 'value' => '1'),
						)
					),
					'D024_D088_Id' => array('name' => 'D024_D088_Id', 'label' => 'Situação Tributária', 'disabled' => $disabledFiscal, 'type' => 'select', 'select' =>
						gGeraSelect('D088', 'D088_Id', 'D088_Codigo_ST,D088_Descricao', '', true), 'align' => 'right', 'extra' => 'style="width: 400px; float:right;"'
					),
					'D024_Observacao_Isento_IPI' => array('name' => 'D024_Observacao_Isento_IPI', 'label' => 'Obs. ISENTO', 'readonly' => $disabledFiscal, 'type' => 'textarea', 'length' => array(2,70)),
					'D024_Empresa_Emissora' => array('name' => 'D024_Empresa_Emissora','label'=>'Cliente para emissão de NFe', 'disabled' => $disabledFiscal, 'type' => 'pesquisa', 'length' => array(50,30),
						'pesquisa' => array('D024','D024_Id','D024_Id,D024_Nome_Fantasia,D020_Nome_Cidade,D018_UF,D024_Cpf,D024_Cnpj', 'readonly' => $disabledFiscal, 'left join D020 on D020_Id=D024_D020_Id left join D018 on D018_Id=D024_D018_Id WHERE D024_Flag_Cliente="S" and D024_Flag_Ativo="S" order by D024_Nome_Empresa','(%1) %2 [%3 - %4]'),
					),
					'D024_Percentual_Carga_Media' => array('name' => 'D024_Percentual_Carga_Media', 'label' => '% Carga Média', 'readonly' => $disabledFiscal, 'type' => 'moeda','length' => array(10,10)),
					'D024_CNAE' => array('name' => 'D024_CNAE',	'label' => 'CNAE', 'type' => 'text', 'readonly' => $disabledFiscal, 'length' => array(20,30)),
					'D024_Codigo_Beneficio_Fiscal' => array('name' => 'D024_Codigo_Beneficio_Fiscal', 'label' => 'Benefício Fiscal', 'readonly' => $disabledFiscal, 'type' => 'text','length' => array(30,40)),
					'D024_TTD' => array('name' => 'D024_TTD', 'label' => 'TTD', 'readonly' => $disabledFiscal, 'type' => 'text','length' => array(30,40)),
					'D024_Despacho_Concessorio' => array('name' => 'D024_Despacho_Concessorio', 'label' => 'Despacho Concessório', 'readonly' => $disabledFiscal, 'type' => 'text','length' => array(30,40)),
					'D024A_Exportacao_UF_Embarque' => array('name' => 'D024A_Exportacao_UF_Embarque', 'label' => 'UF de embarque', 'disabled' => $disabledFiscal, 'type' => 'select', 'extra' => 'style="width:50px;"', 'select' =>
						gGeraSelect('D018', 'D018_UF', 'D018_UF', '', true)
					),
					'D024A_Exportacao_Local_Embarque' => array('name' => 'D024A_Exportacao_Local_Embarque', 'label' => 'Local desembarque', 'readonly' => $disabledFiscal, 'type' => 'text','length' => array(50,60)),
				),
				'comercial' => array(
					'D024_Flag_Lista_Preco' => 	array('name' => 'D024_Flag_Lista_Preco', 'disabled' => $disabledComercial, 'label' => 'Lista Preço', 'type' => 'select', 'select' =>
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Preço 1', 'value' => '1'),
							array('title' => 'Preço 2', 'value' => '2'),
							array('title' => 'Preço 3', 'value' => '3'),
							array('title' => 'Preço 4', 'value' => '4'),
							array('title' => 'Preço 5', 'value' => '5'),
						)
					),
					'D024_C007_Vendedor_Interno' => array('name' => 'D024_C007_Vendedor_Interno', 'disabled' => $disabledComercial, 'label' => 'Vendedor Principal', 'type' => 'select', 'extra' => 'style="width:110px;"', 'select' =>
						gGeraSelect("C007", "C007_Id", "C007_Primeiro_Nome", "where (C007_Flag_Vendedor='V' or C007_Flag_Vendedor='S' or C007_Flag_Vendedor='D') and C007_Ativo='S' order by C007_Primeiro_Nome", true)
					),
					'D024_C007_Vendedor_Externo' => array('name' => 'D024_C007_Vendedor_Externo', 'disabled' => $disabledComercial, 'label' => 'Vendedor Externo', 'type' => 'select', 'extra' => 'style="width:110px;"', 'select' =>
						gGeraSelect("C007", "C007_Id", "C007_Primeiro_Nome", "where (C007_Flag_Vendedor='V' or C007_Flag_Vendedor='S' or C007_Flag_Vendedor='D') and C007_Ativo='S' order by C007_Primeiro_Nome", true)
					),
					'D024_D021_Id' => array('name' => 'D024_D021_Id', 'label' => 'Representante', 'disabled' => $disabledComercial, 'type' => 'select', 'extra' => 'style="width:110px;"', 'select' =>
						gGeraSelect("D021", "D021_Id", "D021_Nome_Empresa", "where D021_Data_Irregular='0000-00-00' or  D021_Data_Irregular is null order by D021_Nome_Empresa", true)
					),
					'D024_Percentual_Comissao' => array('name' => 'D024_Percentual_Comissao', 'readonly' => $disabledComercial, 'label'=>'% Comissão', 'type' => 'moeda', 'value' => '0,00','length' => array(15,30)),
					'D024_Percentual_Comissao_Externo' => array('name' => 'D024_Percentual_Comissao_Externo', 'readonly' => $disabledComercial, 'label'=>'% Comissão', 'type' => 'moeda', 'value' => '0,00','length' => array(15,30)),
					'D024_Percentual_Comissao_Interno' => array('name' => 'D024_Percentual_Comissao_Interno', 'readonly' => $disabledComercial, 'label'=>'% Comissão', 'type' => 'moeda', 'value' => '0,00','length' => array(15,30)),
					'D024_Flag_Cotacao_Compra' => array('name' => 'D024_Flag_Cotacao_Compra', 'label'=>'Considerar para Cotação de Compra', 'disabled' => $disabledComercial, 'type' => 'select', 'extra' => 'style="width:110px;"', 'select' =>
					array (
						array('title' => '...', 'value' => '', 'selected' => true),
						array('title' => 'Não', 'value' => 'N'),
						array('title' => 'Sim', 'value' => 'S'),
					)),
					'D024_Flag_Comercial' => array('name' => 'D024_Flag_Comercial', 'label'=>'Situação Comercial', 'disabled' => $disabledComercial, 'type' => 'select', 'extra' => 'style="width:110px;"', 'select' =>
					array (
						array('title' => '...', 'value' => '', 'selected' => true),
						array('title' => 'Ativo', 'value' => 'A'),
						array('title' => 'Inativo', 'value' => 'I'),
					)),
					'D024_Data_Ativo' => array('name' => 'D024_Data_Ativo', 'label'=>'Data Ativo', 'readonly' => $disabledComercial, 'type' => 'data','align'=>'right', 'style'=>'float:right', 'length' => array(15,14)),
					'D024_Flag_Permite_ACP' => array('name' => 'D024_Flag_Permite_ACP', 'label'=>'Permite Emitir ACP', 'disabled' => $disabledComercial, 'type' => 'select', 'extra' => 'style="width:110px;"', 'select' =>
					array (
						array('title' => '...', 'value' => '', 'selected' => true),
						array('title' => 'Não', 'value' => 'N'),
						array('title' => 'Sim', 'value' => 'S'),
					)),
					'D024_Dia_Visita' => array('name' => 'D024_Dia_Visita', 'label'=>'Dia visita', 'readonly' => $disabledComercial, 'type' => 'text','length' => array(3,50)),
					'D024_Data_Inativo' => array('name' => 'D024_Data_Inativo', 'label'=>'Data Inativo', 'readonly' => $disabledComercial, 'type' => 'data','align'=>'right', 'style'=>'float:right', 'length' => array(15,14)),
					'D024_Flag_Tempo' => array('name' => 'D024_Flag_Tempo', 'label'=>'Estado Negociação', 'disabled' => $disabledComercial, 'type' => 'select', 'extra' => 'style="width:110px;"', 'select' =>
					array (
						array('title' => '...', 'value' => '', 'selected' => true),
						array('title' => 'Bom', 'value' => 'BM'),
						array('title' => 'Regular', 'value' => 'RE'),
						array('title' => 'Ruim', 'value' => 'RM'),
					)),
					'D024_Horario_Entrega' => array('name' => 'D024_Horario_Entrega', 'label'=>'Horário Entrega', 'readonly' => $disabledComercial, 'type' => 'tempo','length' => array(15,30)),
					'D024_Tipo_Pagamento' => array('name' => 'D024_Tipo_Pagamento', 'label'=>'Frequência Pagamento', 'disabled' => $disabledComercial, 'type' => 'select', 'extra' => 'style="width:110px;"', 'select' =>
					array (
						array('title' => '...', 'value' => '', 'selected' => true),
						array('title' => 'Diário', 'value' => 'D'),
						array('title' => 'Semanal', 'value' => 'S'),
						array('title' => 'Quinzenal', 'value' => 'Q'),
						array('title' => 'Mensal', 'value' => 'M'),
					)),
					'D024_Dias_Carencia_Pagamento' => array('name' => 'D024_Dias_Carencia_Pagamento', 'readonly' => $disabledComercial, 'label'=>'Dias carência pagamento', 'type' => 'text','length' => array(15,30)),
					'D024_Dia_Semana_Pagamento' => array('name' => 'D024_Dia_Semana_Pagamento', 'disabled' => $disabledComercial, 'label'=>'Dia Semana Pagamento', 'type' => 'select', 'extra' => 'style="width:110px;"', 'select' =>
					array (
						array('title' => '...', 'value' => '', 'selected' => true),
						array('title' => 'Domingo', 'value' => '1'),
						array('title' => 'Segunda', 'value' => '2'),
						array('title' => 'Terça', 'value' => '3'),
						array('title' => 'Quarta', 'value' => '4'),
						array('title' => 'Quinta', 'value' => '5'),
						array('title' => 'Sexta', 'value' => '6'),
						array('title' => 'Sábado', 'value' => '7'),
					)),
					'D024_Dia_Mes_Pagamento' => array('name' => 'D024_Dia_Mes_Pagamento', 'readonly' => $disabledComercial, 'label'=>'Dia do mês pagamento', 'type' => 'data','length' => array(15,30)),
					'D024_Observacao_Comercial' => array('name' => 'D024_Observacao_Comercial', 'readonly' => $disabledComercial, 'label'=>'Observação Comercial', 'type' => 'textarea','length' => array(3,50)),
					'D024_D022_Id' => array('name' => 'D024_D062_Id', 'label' => 'Transportadora', 'disabled' => $disabledComercial, 'type' => 'select', 'select' =>
						gGeraSelect('D022', 'D022_Id', 'D022_Nome_Empresa','', true)
					),
					'D024_IPV_1' => array('name' => 'D024_IPV_1', 'label'=>'IPV Venda', 'type' => 'moeda', 'readonly' => $disabledComercial, 'casasDecimais' => '4', 'value' => '0,0000', 'length' => array(15,30)),
					'D024_Id_Matriz' => array('name' => 'D024_Id_Matriz', 'label'=> "Matriz", 'readonly' => $disabledComercial, 'type' => 'pesquisa', 'pesquisa' => array('D024','D024_Id','D024_Nome_Fantasia,D020_Nome_Cidade,D018_UF,D024_Cpf,D024_Cnpj,D024_DDD_Telefone_1,D024_Telefone_1,D024_Nome_Fantasia,D024_Id','left join D020 on D020_Id=D024_D020_Id left join D018 on D018_Id=D024_D018_Id WHERE D024_Flag_Ativo="S" ORDER BY D024_Nome_Fantasia','%1 - %9 - (%4%5) -  [%2/%3]'), 'length' => array(80,30)),
					'D024_Flag_Transporta_Diferentes_UF' => array('name' => 'D024_Flag_Transporta_Diferentes_UF', 'label'=>'Faturar em UF de entrega <br/>diferente do faturamento', 'disabled' => $disabledComercial, 'type' => 'select', 'extra' => 'style="width:110px;"', 'select' =>
					array (
						array('title' => 'Não', 'value' => 'N'),
						array('title' => 'Sim', 'value' => 'S'),
					)),
					'D024_Instrucao_Pagamento' => array('name' => 'D024_Instrucao_Pagamento', 'label'=>'Instrução de Pagamento', 'readonly' => $disabledComercial, 'type' => 'textarea'),
					'D024_Observacao_Financeiro' => array('name' => 'D024_Observacao_Financeiro', 'label'=>'Observação Financeiro', 'readonly' => $disabledComercial, 'type' => 'textarea','length' => array(3,60)),
					'D024_Flag_Frete' => array('name' => 'D024_Flag_Frete', 'label' => 'Frete', 'type' => 'select', 'select'=>
					array(
						array('title' => '...', 'value' => '', 'selected' => true),
						array('title' => '0 - Contratação por conta do Remetente', 'value' => '0'),
						array('title' => '1 - Contratação por conta do Destinatário', 'value' => '1'),
						array('title' => '2 - Contratação por conta de Terceiros', 'value' => '2'),
						array('title' => '3 - Próprio por conta do Remetente', 'value' => '3'),
						array('title' => '4 - Próprio por conta do Destinatário', 'value' => '4'),							
						array('title' => '9 - Sem frete', 'value' => '9', 'selected' => (isset($r_consumidor) && $r_consumidor) ? true : false),
					)),
					'D024_Dias_Envio_Email_Orc_Apos_Emissao' => array('name' => 'D024_Dias_Envio_Email_Orc_Apos_Emissao', 'label'=>'Dias envio email Orc.</br>Automático após emissão', 'readonly' => $disabledComercial, 'type' => 'text','length' => array(3,50)),
					'D024_Dias_Envio_Email_Orc_Antes_Vencer' => array('name' => 'D024_Dias_Envio_Email_Orc_Antes_Vencer', 'label'=>'Dias envio email Orc.</br>Automático antes vencer', 'readonly' => $disabledComercial, 'type' => 'text','length' => array(3,50)),
					'D024_Flag_Destino_Produto' => array('name' => 'D024_Flag_Destino_Produto', 'readonly' => $disabledComercial, 'label'=>'Destino Produto',  'type' => 'select', 'select' =>
						array(
							array('title' => '...', 'value' => ""),
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
				),
				'financeiro' => array(
					'D024_Flag_Restricao' => array('name' => 'D024_Flag_Restricao', 'label'=>'Cliente possui restrições?', 'disabled' => $disabledFinanceiro, 'type' => 'select','select' => 
						array (
							array('title' => 'Não', 'value' => 'N'),
							array('title' => 'Sim', 'value' => 'S', 'selected' => true),
						)),
					'D024_Flag_Liberado' => array('name' => 'D024_Flag_Liberado', 'label'=>'Crédito Liberado?', 'disabled' => $disabledFinanceiro, 'type' => 'select','select' => 
						array (
							array('title' => 'Não', 'value' => 'N', 'selected' => true),
							array('title' => 'Sim', 'value' => 'S'),
						)),
					'D024_Flag_Ativo' => array('name' => 'D024_Flag_Ativo', 'label'=>'Cliente está ativo?', 'disabled' => $disabledFinanceiro, 'type' => 'select','select' => 
						array (
							array('title' => 'Sim', 'value' => 'S', 'selected' => true),
							array('title' => 'Não', 'value' => 'N'),
						)),
					'D024_Valor_Limite' => array('name' => 'D024_Valor_Limite', 'label'=>'Valor limite/Fat.mínimo', 'readonly' => $disabledFinanceiro, 'type' => 'moeda','length' => array(25,25)),
					'D024_Valor_Limite_Manual' => array('name' => 'D024_Valor_Limite_Manual', 'label'=>'Valor limite/Fat.mínimo Manual', 'readonly' => $disabledFinanceiro, 'type' => 'moeda','length' => array(25,25)),
					'D024_Flag_Protestar' => array('name' => 'D024_Flag_Protestar', 'label'=>'Protestar?', 'disabled' => $disabledFinanceiro, 'type' => 'select','select' => 
						array (
							array('title' => 'Sim', 'value' => 'S', 'selected' => true),
							array('title' => 'Não', 'value' => 'N'),
						)),
					'D024_Flag_Permite_Agrupamentos' => array('name' => 'D024_Flag_Permite_Agrupamentos', 'disabled' => $disabledFinanceiro, 'label'=>'Agrupamento automático', 'type' => 'select','select' => 
						array(
							array('title' => 'Sim', 'value' => '1', 'selected' => true),
							array('title' => 'Não', 'value' => '0'),
						)
					),
					'D024_Pgto_Primeiro_Vcto_Tipo' => array('name' => 'D024_Pgto_Primeiro_Vcto_Tipo', 'disabled' => $disabledFinanceiro, 'label' => '1º Venc', 'type' => 'select', 'select' =>
						array(
							array('title' => '...', 'value' => ''),
							array('title' => 'DIAS', 'value' => 'DIAS'),
							array('title' => 'DATA', 'value' => 'DATA')
						)
					),
					'D024_Cobrar_Frete_Primeira_Parcela' => array('name' => 'D024_Cobrar_Frete_Primeira_Parcela', 'disabled' => $disabledFinanceiro, 'label' => 'Cobrar frete na primeira parcela', 'type' => 'select', 'select' =>
						array(
							array('title' => '...', 'value' => ''),
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'D024_Cobrar_IPI_Primeira_Parcela' => array('name' => 'D024_Cobrar_IPI_Primeira_Parcela', 'disabled' => $disabledFinanceiro, 'label' => 'Cobrar IPI na primeira parcela', 'type' => 'select', 'select' =>
						array(
							array('title' => '...', 'value' => ''),							
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'D024_Prazo_Parcela' => array('name' => 'D024_Prazo_Parcela', 'label' => '', 'readonly' => $disabledFinanceiro, 'type' => 'text'),
					'D024_Pgto_Dias_Vcto_Tipo' => array('name' => 'D024_Pgto_Dias_Vcto_Tipo', 'disabled' => $disabledFinanceiro, 'label' => 'Dia Fixo/Intervalo', 'type' => 'select', 'select' =>
						array(
							array('title' => '...', 'value' => ''),							
							array('title' => 'INTERVALO', 'value' => 'INTERVALO'),
							array('title' => 'DIA FIXO', 'value' => 'DIA FIXO')
						)
					),
					'D024_Dias_Vencimento' => array('name' => 'D024_Dias_Vencimento', 'label' => '', 'readonly' => $disabledFinanceiro, 'type' => 'text'),
					'D024_Quantidade_Parcelas' => array('name' => 'D024_Quantidade_Parcelas', 'label' => 'N° Parcelas', 'readonly' => $disabledFinanceiro, 'type' => 'text'),
					'D024_D027_Id' => array('name' => 'D024_D027_Id','label'=>'Portador', 'disabled' => $disabledFinanceiro, 'type' => 'select', 'select' =>
						gGeraSelect('D027', 'D027_Id', 'D027_Portador','LEFT JOIN D007 ON D007_Id = D027_D007_Id LEFT JOIN C004 ON C004_Id = D007_C004_Id WHERE D027_Flag_Ativo = "S" AND D027_Flag_Exibe_CRM = "S" AND (C004_Cnpj LIKE "%'.$CNPJ_Empresa_Corrente.'%" OR ifnull(D027_D007_Id,0) = 0) order by D027_Portador', true)
					),
					'D024A_D031_Id' => array('name' => 'D024A_D031_Id','label'=>'Portador a pagar', 'disabled' => $disabledFinanceiro, 'type' => 'select', 'select' =>
						gGeraSelect('D031', 'D031_Id', 'D031_Portador','order by D031_Portador', true)
					),
					'D024_Flag_Empresas_Grupo' => array('name' => 'D024_Flag_Empresas_Grupo', 'disabled' => $disabledFinanceiro, 'label'=>'Empresa do grupo', 'type' => 'select','select' => 
						array(
							array('title' => 'Sim', 'value' => 'S', 'selected' => true),
							array('title' => 'Não', 'value' => 'N'),
						)
					),
					'D024_Percentual_Desconto_Tabela' => array('name' => 'D024_Percentual_Desconto_Tabela', 'readonly' => $disabledFinanceiro, 'label' => '% Desconto Financeiro', 'type' => 'text'),
					'D024_Cobrar_ST_Primeira_Parcela' => array('name' => 'D024_Cobrar_ST_Primeira_Parcela', 'readonly' => $disabledFinanceiro, 'label' => 'Cobrar ST na primeira parcela', 'type' => 'select', 'select' => 
						array(
							array('title' => '...', 'value' => ''),
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'D024_Observacao_Administracao' => array('name' => 'D024_Observacao_Administracao', 'label'=>'Observação Financeiro', 'readonly' => $disabledFinanceiro, 'type' => 'textarea','length' => array(3,60)),
				)
			)
		);
		$form->definirFormularioArray($formArray);

		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				var enterPressed = false;
				$("#D024_Cep").bind('keypress keydown keyup', function(event) {
					if (event.which == 13 && enterPressed == false) {
						enterPressed = true;
						$('#buttonSearchCep1').trigger('click');
						event.preventDefault();
						setTimeout(function(){ enterPressed = false; }, 2500);
      					return false;
					}
				});
				$("#D024_Cnpj").bind('keypress keydown keyup', function(event) {
					if (event.which == 13 && enterPressed == false) {
						enterPressed = true;

						$('#buttonSearchCnpj').trigger('click');
						event.preventDefault();
						setTimeout(function(){ enterPressed = false; }, 2500);
      					return false;
					}
				});
				$('#incluirCidade').unbind('click');
				$('#incluirCidade').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Incluir Cidade', '/fis/fis005/content/fis005contentCadastroCidade/','', [400,300]);
					return false;
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
					// Insert ou Update?
					if (!empty($acaoId)) {
						// Update
						$form->campoSetarValor('D024_Cep', str_replace('-', '', $form->campoValorEnviado('D024_Cep')), true);
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
							// O FW atualiza a linha do grid automaticamente, somente recarregar se não for grid
							if ($('#{$g['divIdParent']}').find('table.grid').length === 0) {
							    divRefresh('{$g['divIdParent']}', true);
							}
						} </script></div>
EOT;
						}
				} else {
					// Insert
					$form->campoSetarValor('D024_Cep', str_replace('-', '', $form->campoValorEnviado('D024_Cep')), true);
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						// Imprime uma mensagem e fecha a janela
						$acaoId = $g['mysqlLastId'];
						$D024 = mysql_query("SELECT D024A_D024_Id
											   FROM D024A 
											  WHERE D024A_Id = '{$acaoId}'");
						$mD024 = mysql_fetch_array($D024);
						$acaoId = $mD024['D024A_D024_Id'];

						if($g['C031']['separarClientePorEmpresa'] == 'S'){
							mysqli_query("UPDATE D024 SET D024_C004_Id = '{$g['empresaAtual']}' WHERE D024_Id = '{$acaoId}'");
						}

						if($inclusaoOrcamento == 'S'){
							$D024_Nome_Empresa = mysql_real_escape_string(strtoupper($form->campoValorEnviado('D024_Nome_Empresa')));

							echo  <<<EOT
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									$('#{$g['divIdParent']} .itemForm-T003_D024_Id').val('$acaoId').data('definirContato', '0').trigger('change');
									$('#{$g['divIdParent']} .itemForm-D085_D024_Id').val('$acaoId').data('definirContato', '0').trigger('change');
									$('#{$g['divIdParent']} .itemForm-T003_D024_Id_Pesquisa').val('$D024_Nome_Empresa');
									$('#{$g['divIdParent']} .itemForm-D085_D024_Id_Pesquisa').val('$D024_Nome_Empresa');
									fecharJanela('{$g['divId']}');
								} </script></div>
EOT;
						} else {
							//die($AbrirEdicaoClienteAposInsercao);
							$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
							echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										if('{$g['C031']['AbrirEdicaoClienteAposInsercao']}' == 'S' || '{$g['C031']['incluirContatoAposInsercao']}' == 'S'){
											if('{$g['C031']['incluirContatoAposInsercao']}' == 'S'){
												var \$buttons = {
													"Sim": function() {
														abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}', unique(), '', 'Editar Cadastro - ID: ' + '{$acaoId}', '/cad/cad001/content/cad001contentCadastro/', '&acaoId=' + encodeURIComponent('{$acaoId}') + '&tab=contatos', '[800,700]');
														$(this).dialog("destroy").remove();
													},
													"Não": function() { 
														if('{$g['C031']['AbrirEdicaoClienteAposInsercao']}' == 'S'){
															abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}', unique(), '', 'Editar Cadastro - ID: ' + '{$acaoId}', '/cad/cad001/content/cad001contentCadastro/', '&acaoId=' + encodeURIComponent('{$acaoId}'), '[800,700]');
														} 
														$(this).dialog("destroy").remove(); 													
													}
												};											
												dialogConfirm('Cadastro incluído com sucesso', 'Deseja incluir um contato? ', \$buttons);
											} else if('{$g['C031']['AbrirEdicaoClienteAposInsercao']}' == 'S'){
												abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}', unique(), '', 'Editar Cadastro - ID: ' + '{$acaoId}', '/cad/cad001/content/cad001contentCadastro/', '&acaoId=' + encodeURIComponent('{$acaoId}'), '[800,700]');
											}
										} else {
											{$alert}
										}
										
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

	case 'cad001FormVeiculos':
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
		$form_tabela = 'D116';
		$form_campo_pk = 'D116_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad045FormCadastroVeiculosBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad001/form/cad001FormVeiculos/';
		
		// form editor
		$form_editor = array(
			'geral' => '{"D116_Placa":{"top":7,"left":41,"width":75,"hidden":0},"D116_Modelo":{"top":30,"left":40,"width":175,"hidden":0},"D116_D133_Id":{"top":53,"left":40,"width":42,"hidden":0},"D116_Cor":{"top":76,"left":57,"width":175,"hidden":0},"D116_D134_Id":{"top":99,"left":57,"width":42,"hidden":0},"D116_D126_Id":{"top":122,"left":54,"width":110,"hidden":0},"D116_Observacao":{"top":145,"left":14,"width":192,"hidden":0}}',
		);
		
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('formVeiculos_cad001');
		$formArray = array(
			'campos' => array(
				'geral' => array(
				'D116_Placa' => array('name' => 'D116_Placa', 'label'=>'Placa', 'type' => 'text','length' => array(10,7)),
				'D116_Modelo' => array('name' => 'D116_Modelo', 'label'=>'Modelo', 'type' => 'text','length' => array(30,30)),
				'D116_D133_Id' => array('name' => 'D116_D133_Id', 'label'=>'Modelo', 'type' => 'select',
					'select' => gGeraSelect("D133", "D133_Id", "D133_Modelo", "order by D133_Modelo", true)
				),
				'D116_Cor' => array('name' => 'D116_Cor', 'label'=>'Cor', 'type' => 'text','length' => array(30,30)),
				'D116_D134_Id' => array('name' => 'D116_D134_Id', 'label'=>'Cor', 'type' => 'select',
					'select' => gGeraSelect("D134", "D134_Id", "D134_Cor", "order by D134_Cor", true)
				),
				'D116_D126_Id' => array('name' => 'D116_D126_Id', 'label' => 'Tipo', 'type' => 'select','select' => 
					gGeraSelect("D126", "D126_Id", "D126_Descricao", "order by D126_Descricao", true)
				),
				'D116_Observacao' => array('name' => 'D116_Observacao', 'label'=>'Observação', 'type' => 'textarea','length' => array(3,30)),
				
				//'uploadArquivoDocumento' => array('name' => 'uploadArquivoDocumento', 'reject'=>true, 'label' => 'Upload Documento', 'type' => 'file', 'length' => array(10,80)),
				//'D116_Nome_Documento' => array('name' => 'D116_Nome_Documento', 'label' => 'Upload Documento', 'type' => 'hidden', 'uppercase'=>false, 'length' => array(10,80)),
				
				'D116_D024_Id' => array('name' => 'D116_D024_Id','label' => 'D116_D024_Id', 'value'=>@$r_D024_Id, 'type' => 'hidden','length' => array(10,7)),
				),
			) // 'campos'
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
					// Insert ou Update?
					if (!empty($acaoId)) {
						
						$arquivo = $form->campoValorEnviado('uploadArquivoDocumento');
						$form->campoSetarValor('D116_Nome_Documento', $arquivo['name'], true);
						// Update
						if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
						// Imprime uma mensagem e fecha a janela
						$r_naoRefreshParent = (isset($r_naoRefreshParent)) ? $r_naoRefreshParent : '';
						echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							// Fecha a janela, recarrega o grid e abre a alteração
							$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
								setTimeout(function() {
									//abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}', unique(), '', 'Editar Veículo - ID: ' + '{$r_acaoId}', '/cad/cad001/content/cad001ContentVeiculosIncluir/', '&tab=geral&acaoId=' + encodeURIComponent('{$acaoIdInserido}') +
									{$alert}
								}, 150);
							});
							$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
							if('{$r_naoRefreshParent}' == '') {
								// O FW atualiza a linha do grid automaticamente, somente recarregar se não for grid
								if ($('#{$g['divIdParent']}').find('table.grid').length === 0) {
									divRefresh('{$g['divIdParent']}', true);
								}
							}
						} </script></div>
EOT;
						}
						if ($arquivo['error'] == UPLOAD_ERR_OK) {
							// Gravar o arquivo no disco
							$pathinfo = pathinfo($arquivo['name']);
							$pathinfo['extension'] = empty($pathinfo['extension']) ? '.tmp' : $pathinfo['extension'];
							$dir = "{$g['pathDados']}veiculos/";
							//$dir = "{$g['pathDados']}di";
							is_dir($dir) or mkdir($dir, 0777, true);
							$file = $dir . '/' . $r_acaoId . '.' . $pathinfo['extension'];
							move_uploaded_file($arquivo['tmp_name'], $file);
						}
				} else {
					// Insert
					$arquivo = $form->campoValorEnviado('uploadArquivoDocumento');
					$form->campoSetarValor('D116_Nome_Documento', $arquivo['name'], true);
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						if ($arquivo['error'] == UPLOAD_ERR_OK) {
							// Gravar o arquivo no disco
							$pathinfo = pathinfo($arquivo['name']);
							$pathinfo['extension'] = empty($pathinfo['extension']) ? '.tmp' : $pathinfo['extension'];
							$dir = "{$g['pathDados']}veiculos/";
							//$dir = "{$g['pathDados']}di";
							is_dir($dir) or mkdir($dir, 0777, true);
							$file = $dir . '/' . $g['mysqlLastId'] . '.' . $pathinfo['extension'];
							move_uploaded_file($arquivo['tmp_name'], $file);
						}	
						$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
						$acaoIdInserido = $g['mysqlLastId'];
						$D024_Id = $form->campoValorEnviado('D116_D024_Id');
						// Imprime uma mensagem e fecha a janela
						$r_naoRefreshParent = (isset($r_naoRefreshParent)) ? $r_naoRefreshParent : '';
						echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							// Fecha a janela, recarrega o grid e abre a alteração
							$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
								setTimeout(function() {
									//abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}', unique(), '', 'Editar Veículo - ID: ' + '{$r_acaoId}', '/cad/cad001/content/cad001ContentVeiculosIncluir/', '&tab=geral&acaoId=' + encodeURIComponent('{$acaoIdInserido}') +
									//																																										  '&D024_Id=' + encodeURIComponent('{$D024_Id}'), [600,450]);
									{$alert}
								}, 150);
							});
							$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
							if('{$r_naoRefreshParent}' == '') {
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
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="cad045FormCadastroVeiculosBtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
		break;

	case 'cad001FormContatos':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;

		if($r_T139_Id > 0){
			$row = mysql_fetch_assoc(mysql_query("SELECT T139_De FROM T139 WHERE T139_Id = '{$r_T139_Id}'"));
			$email = $row['T139_De'];
			if (strpos($email, '<') !== false) {
				$email = explode('<', $email);
				$nome = $email[0];
				$email = explode('>', $email[1]);
				$email = $email[0];
			}
			$email = mysql_real_escape_string($email);
			$nome = mysql_real_escape_string(str_replace('"', '', $nome));
			$D013_Nome_Contato = trim($nome);
			$D013_E_Mail = trim($email);
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
		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'D013';
		$form_campo_pk = 'D013_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = array('cad001FormContatosBtnAdd', 'cad001FormContatosBtnCln', 'cad001FormContatosBtnNew');
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad001/form/cad001FormContatos/';
		$form_editor = array(
			'geral' => '{
							"D013_Flag_Ativo":{"top":122,"left":105,"width":59,"hidden":0}, 
							"D013A_Flag_Envio_Certificado":{"top":145,"left":45,"width":59,"hidden":0},
							"D013_Nome_Contato":{"top":7,"left":22,"width":312,"hidden":0},
							"D013_E_Mail":{"top":30,"left":28,"width":312,"hidden":0},
							"D013_Tipo_Contato":{"top":53,"left":37,"width":312,"hidden":0},
							"D013_Data_Nascimento":{"top":76,"left":0,"width":120,"hidden":0},
							"D013_DDD_Telefone_1":{"top":76,"left":208,"width":24,"hidden":0},
							"D013_Telefone_1":{"top":76,"left":287.984375,"width":88,"hidden":0},
							"D013_DDD_Telefone_2":{"top":99,"left":7,"width":24,"hidden":0},
							"D013_Telefone_2":{"top":99,"left":95.984375,"width":88,"hidden":0},
							"D013_DDD_Celular":{"top":99,"left":216,"width":24,"hidden":0},
							"D013_Celular":{"top":99,"left":287.984375,"width":88,"hidden":0},
							"cad001FormContatosBtnCln":{"top":120,"left":223.984375,"width":null,"hidden":0},
							"cad001FormContatosBtnAdd":{"top":120,"left":287.984375,"width":null,"hidden":0}
						}',
		);
		
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('formContatos_cad001');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D013_Nome_Contato' => array('name' => 'D013_Nome_Contato', 'label'=>'Nome', 'type' => 'text','length' => array(50,60), 'value' => @$D013_Nome_Contato),
					'D013_E_Mail' => array('name' => 'D013_E_Mail', 'label'=>'E-Mail', 'type' => 'text','length' => array(50,90), 'value' => @$D013_E_Mail),
					'D013_Tipo_Contato' => array('name' => 'D013_Tipo_Contato', 'label'=>'Tipo', 'type' => 'text','length' => array(50,90)),
					'D013_Data_Nascimento' => array('name' => 'D013_Data_Nascimento', 'label'=>'Nascimento', 'type' => 'data','length' => array(20,30)),
					'D013_DDD_Telefone_1' => array('name' => 'D013_DDD_Telefone_1', 'label'=>'Telefone', 'type' => 'text','length' => array(2,2)),
					'D013_Telefone_1' => array('name' => 'D013_Telefone', 'label'=>'', 'type' => 'text','length' => array(20,30), 'align'=>'right'),
					'D013_DDD_Telefone_2' => array('name' => 'D013_DDD_Telefone_2', 'label'=>'Telefone 2', 'type' => 'text','length' => array(2,2)),
                    'D013_Telefone_2' => array('name' => 'D013_Telefone_2', 'label'=>'', 'type' => 'text','length' => array(20,30)),
                    'D013_DDD_Celular' => array('name' => 'D013_DDD_Celular', 'label'=>'Celular', 'type' => 'text','length' => array(2,2), 'align'=>'right'),
                    'D013_Celular' => array('name' => 'D013_Celular', 'label'=>'', 'type' => 'text','length' => array(20,30), 'align'=>'right'),
					'D013_D024_Id' => array('name' => 'D013_D024_Id', 'value' => @$r_D024_Id, 'type' => 'hidden', 'label' => 'D013_D024_Id', 'length' => array(30,30)),
                    'D013_Flag_Ativo' => array('name' => 'D013_Flag_Ativo', 'label' => 'Ativo', 'type' => 'select','select' => 
                    	array(
                    		array('title' => 'SIM', 'value' => 'S'),
                    		array('title' => 'NÃO', 'value' => 'N')
                    	)
                    ),
					'D013A_Flag_Envio_Certificado' => array('name' => 'D013A_Flag_Envio_Certificado', 'label' => 'Envio Certificado', 'type' => 'select', 'select' =>
						array(
							array('title' => 'NÃO', 'value' => 'N'),
							array('title' => 'SIM', 'value' => 'S')
						)
					),
                    'D013_Flag_Ecommerce' => array('name' => 'D013_Flag_Ecommerce', 'label' => 'Acessa e-commerce?', 'type' => 'select','select' => 
                    	array(
                    		array('title' => 'SIM', 'value' => 'S'),
                    		array('title' => 'NÃO', 'value' => 'N')
                    	)
                    ),
					'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true),
					'D013_C007_Id_Cliente' => array('name' => 'D013_C007_Id_Cliente', 'type' => 'text', 'label' => 'C007 ID Cliente', 'length' => array(30,30)),
				),
			) // 'campos'
		);

		// Botão Limpar / Novo Registro
		if (!empty($acaoId)) {
			$formArray['campos']['geral']['cad001FormContatosBtnNew'] = array('name' => 'cad001FormContatosBtnNew', 'type'=>'submit', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		}
		$formArray['campos']['geral']['cad001FormContatosBtnCln'] = array('name' => 'cad001FormContatosBtnCln', 'type'=>'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['geral']['cad001FormContatosBtnAdd'] = array('name' => 'cad001FormContatosBtnAdd', 'type'=>'button', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
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
					// Insert ou Update?
					if (!empty($acaoId)) {
						// Update
						log('abaixo gerar update');
						if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
							// Imprime uma mensagem e fecha a janela
							echo  <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							divRefresh('{$form->campoValorEnviado('divReload')}', true);
							divSetarAcaoId('{$g['divId']}', 0);
							divRefresh('{$g['divId']}', true);
						} </script></div>
EOT;
						}
				} else {
					// Insert
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						// Imprime uma mensagem e fecha a janela
						echo  <<<EOT
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

	case 'cad001FormAlterarContato':
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
		$form_tabela = 'D013';
		$form_campo_pk = 'D013_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = array('cad001FormContatosBtnAdd', 'cad001FormContatosBtnCln', 'cad001FormContatosBtnNew');
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad001/form/cad001FormAlterarContato/';
		$form_editor = array(
			'geral' => '{
							"D013_Flag_Ativo":{"top":122,"left":34,"width":59,"hidden":0}, 
							"D013A_Flag_Envio_Certificado":{"top":120,"left":80,"width":65,"hidden":0}, 
							"D013_Nome_Contato":{"top":7,"left":22,"width":312,"hidden":0},
							"D013_E_Mail":{"top":30,"left":28,"width":312,"hidden":0},
							"D013_Tipo_Contato":{"top":53,"left":37,"width":312,"hidden":0},
							"D013_Data_Nascimento":{"top":76,"left":0,"width":120,"hidden":0},
							"D013_DDD_Telefone_1":{"top":76,"left":208,"width":24,"hidden":0},
							"D013_Telefone_1":{"top":76,"left":287.984375,"width":88,"hidden":0},
							"D013_DDD_Telefone_2":{"top":99,"left":7,"width":24,"hidden":0},
							"D013_Telefone_2":{"top":99,"left":95.984375,"width":88,"hidden":0},
							"D013_DDD_Celular":{"top":99,"left":216,"width":24,"hidden":0},
							"D013_Celular":{"top":99,"left":287.984375,"width":88,"hidden":0},
							"cad001FormContatosBtnCln":{"top":120,"left":223.984375,"width":null,"hidden":0},
							"cad001FormContatosBtnAdd":{"top":120,"left":287.984375,"width":null,"hidden":0}
						}',
			);	
		
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('formContatos_cad001');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D013_Nome_Contato' => array('name' => 'D013_Nome_Contato', 'label'=>'Nome', 'type' => 'text','length' => array(50,60), 'value' => @$D013_Nome_Contato),
					'D013_E_Mail' => array('name' => 'D013_E_Mail', 'label'=>'E-Mail', 'type' => 'text','length' => array(50,90), 'value' => @$D013_E_Mail),
					'D013_Tipo_Contato' => array('name' => 'D013_Tipo_Contato', 'label'=>'Tipo', 'type' => 'text','length' => array(50,90)),
					'D013_Data_Nascimento' => array('name' => 'D013_Data_Nascimento', 'label'=>'Nascimento', 'type' => 'data','length' => array(20,30)),
					'D013_DDD_Telefone_1' => array('name' => 'D013_DDD_Telefone_1', 'label'=>'Telefone', 'type' => 'text','length' => array(2,2)),
					'D013_Telefone_1' => array('name' => 'D013_Telefone', 'label'=>'', 'type' => 'text','length' => array(20,30), 'align'=>'right'),
					'D013_DDD_Telefone_2' => array('name' => 'D013_DDD_Telefone_2', 'label'=>'Telefone 2', 'type' => 'text','length' => array(2,2)),
                    'D013_Telefone_2' => array('name' => 'D013_Telefone_2', 'label'=>'', 'type' => 'text','length' => array(20,30)),
                    'D013_DDD_Celular' => array('name' => 'D013_DDD_Celular', 'label'=>'Celular', 'type' => 'text','length' => array(2,2), 'align'=>'right'),
                    'D013_Celular' => array('name' => 'D013_Celular', 'label'=>'', 'type' => 'text','length' => array(20,30), 'align'=>'right'),
					'D013_D024_Id' => array('name' => 'D013_D024_Id', 'value' => @$r_D024_Id, 'type' => 'hidden', 'label' => 'D013_D024_Id', 'length' => array(30,30)),
                    'D013_Flag_Ativo' => array('name' => 'D013_Flag_Ativo', 'label' => 'Ativo', 'type' => 'select','select' => 
                    	array(
                    		array('title' => 'SIM', 'value' => 'S'),
                    		array('title' => 'NÃO', 'value' => 'N')
                    	)
                    ),
					'D013A_Flag_Envio_Certificado' => array('name' => 'D013A_Flag_Envio_Certificado', 'label' => 'Envio Certificado', 'type' => 'select', 'select' =>
						array(
							array('title' => 'NÃO', 'value' => 'N'),
							array('title' => 'SIM', 'value' => 'S')
						)
					),
                    'D013_Flag_Ecommerce' => array('name' => 'D013_Flag_Ecommerce', 'label' => 'Acessa e-commerce?', 'type' => 'select','select' => 
                    	array(
                    		array('title' => 'SIM', 'value' => 'S'),
                    		array('title' => 'NÃO', 'value' => 'N')
                    	)
                    ),
					'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true),
					'D013_C007_Id_Cliente' => array('name' => 'D013_C007_Id_Cliente', 'type' => 'text', 'label' => 'C007 ID Cliente', 'length' => array(30,30)),
				),
			) // 'campos'
		);

		// Botão Limpar / Novo Registro
		if (!empty($acaoId)) {
			$formArray['campos']['geral']['cad001FormContatosBtnNew'] = array('name' => 'cad001FormContatosBtnNew', 'type'=>'submit', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		}
		$formArray['campos']['geral']['cad001FormContatosBtnCln'] = array('name' => 'cad001FormContatosBtnCln', 'type'=>'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['geral']['cad001FormContatosBtnAdd'] = array('name' => 'cad001FormContatosBtnAdd', 'type'=>'button', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
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
					// Insert ou Update?
					if (!empty($acaoId)) {
						// Update
						if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
							// Imprime uma mensagem e fecha a janela
							echo  <<<EOT
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									fecharJanela('{$g['divId']}'); 
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

		
	case 'cad001FormAgenda':
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
		$form_tabela = 'D085';
		$form_campo_pk = 'D085_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad001FormAgenda_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad001/form/cad001FormAgenda/';
		
		$form_editor = array(
			'geral' => '{"D085_D024_Id":{"top":7,"left":56.640625,"width":489,"hidden":0},"D085_C007_Id":{"top":30,"left":51.96875,"width":239,"hidden":0},"D085_Data_Retorno":{"top":53,"left":8.453125,"width":87,"hidden":1},"D085_Data":{"top":53,"left":59.3125,"width":72,"hidden":0},"D085_Hora":{"top":53,"left":176,"width":40,"hidden":0},"D085_Retorno":{"top":76,"left":2.75,"height":140,"width":77,"hidden":1},"D085_Data_Previsao_Retorno":{"top":76,"left":47.078125,"width":72,"hidden":0},"D085_Hora_Previsao_Retorno":{"top":76,"left":176,"width":40,"hidden":0},"D085_Hora_Retorno":{"top":99,"left":0,"width":99,"hidden":1},"D085_Mensagem":{"top":99,"left":28.125,"height":200,"width":504,"hidden":0},"D085_D021_Id":{"top":145,"left":2.71875,"width":0,"hidden":1}}',
			'associacoes' => '{"D085_T003_Id":{"top":7,"left":36.46875,"width":507,"hidden":0}}'
		);

		// novo formulário
		$form = new Formulario('cad001FormAgenda');

		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D085_D024_Id' => array(
				        'name' => 'T003_D024_Id',
				        'label'=> "Cliente",
				        'type' => 'pesquisa',
				        'pesquisa' => array('D024','D024_Id','D024_Nome_Empresa,D020_Nome_Cidade,D018_UF,D024_Cpf,D024_Cnpj,D024_DDD_Telefone_1,D024_Telefone_1,D024_Nome_Fantasia,D024_Id','left join D020 on D020_Id=D024_D020_Id left join D018 on D018_Id=D024_D018_Id WHERE D024_Flag_Cliente="S" and D024_Flag_Ativo="S" order by D024_Nome_Empresa','%1 - (%4%5) -  [%2/%3]'), 
				        'length' => array(80,30),
				        'disabled' => 'disabled'
					),
					'D085_C007_Id_Inclusao' => array('name' => 'D085_C007_Id_Inclusao','label'=>'', 'type' => 'hidden','value' => $g['usuarioAtual']),
					'D085_C007_Id' => array('name' => 'D085_C007_Id', 'label'=>'Usuário','type' => 'select', 'disabled'=>'disabled','select' => 
						gGeraSelect('C007','C007_Id','C007_Nome',"",true, $g['usuarioAtual']),
					),
					'D085_Data' => array('name' => 'D085_Data','label'=>'Data', 'type' => 'data', 'value' => date("d/m/Y"), 'length' => array(10,10), 'readonly' => true),
					'D085_Data_Previsao_Retorno' => array('name' => 'D085_Data_Previsao_Retorno', 'label' => 'Retornar', 'type' => 'data', 'length' => array(10,10)),
					'D085_Hora' => array('name' => 'D085_Hora','label' => '', 'type' => 'tempo', 'value' => date('H:i'), 'length' => array(10,10), 'readonly' => true),
					'D085_Hora_Previsao_Retorno' => array('name' => 'D085_Hora_Previsao_Retorno','label' => '', 'type' => 'tempo', 'length' => array(10,10)),
					'D085_Mensagem' => array('name' => 'D085_Mensagem','label'=>'Mensagem', 'type' => 'textarea','length' => array(3,60)),
					'D085_Flag_Manual' => array('name' => 'D085_Flag_Manual', 'value' => 'S', 'type' => 'hidden'),
					'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true),
				),
				'associacoes' => array(
					'D085_T003_Id' => array(
				        'name' => 'D085_T003_Id',
				        'label'=> "Orçamento",
				        'type' => 'pesquisa',
				        'pesquisa' => array('T003','T003_Id',"T003_Id,T003_Data_Emissao,T003_Valor_Total",'WHERE T003_D024_Id = '.$r_D024_Id.' ORDER BY T003_Id','%1 - %2 - %3'),
				        'length' => array(80,30)
					),
				)
			)
		);

		$form->definirFormularioArray($formArray);

		if(!empty($r_D024_Id)){
			$sqlD024 = mysql_query("SELECT CONCAT(D024_Nome_Empresa, ' - (', D024_Cpf, D024_Cnpj, ') - [', D020_Nome_Cidade,'/',D018_UF,']') as Cliente FROM D024 LEFT JOIN  D020 ON D020_Id=D024_D020_Id LEFT JOIN  D018 ON D018_Id=D024_D018_Id WHERE D024_Id = '{$r_D024_Id}'");
			$D024 = mysql_fetch_array($sqlD024);
			$D024['Cliente'] = mysql_real_escape_string($D024['Cliente']);
			echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					setTimeout(function(){
						$('#D085_D024_Id').val('{$r_D024_Id}');
						$('#D085_D024_Id_Pesquisa_disabled').val('{$D024['Cliente']}');
						$('#D085_D024_Id_Pesquisa').val('{$D024['Cliente']}');
					}, 100);
				} </script></div>
EOT;
		}

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
							echo gGeraAlertMsg('Sucesso', 'Agenda alterada com sucesso.');
							echo <<<EOT
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									divRefresh('{$g['divIdParent']}', true);
									$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								} </script></div>
EOT;
						}
				} else {
					// Insert
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						// Imprime uma mensagem e fecha a janela
						echo gGeraAlertMsg('Sucesso', 'Agenda cadastrada com sucesso.');
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								divRefresh('{$g['divIdParent']}', true);
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
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
		echo  $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');
	break;
		
	case 'cad001FormDocumentos':
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
		$form_tabela = 'D087';
		$form_campo_pk = 'D087_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = array('cad001FormDocumentosBtnAdd', 'cad001FormDocumentosBtnCln', 'cad001FormDocumentosBtnNew');
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad001/form/cad001FormDocumentos/';
		
		//Form Editor
		$form_editor = array(
			'geral' => '{"__D087_D024_Id":{"top":53,"left":970.0000610351562,"width":431,"hidden":1},"D087_Data_Validade":{"top":53,"left":9,"width":65,"hidden":0},"uploadArquivo":{"top":7,"left":12,"width":344,"hidden":0},"D087_Descricao":{"top":76,"left":57,"height":62,"width":344,"hidden":0},"cad001FormDocumentosBtnAdd":{"top":120,"left":496,"width":null,"hidden":0}}'
		);
		
		// Array de definição do formulário
		$r_D024_Id = isset($r_cad001FormDocumentos) ? $r_cad001FormDocumentos['D087_D024_Id'] : $r_D024_Id;
		
		$tmp = gGeraSelect('D024', 'D024_Id', 'D024_Nome_Fantasia,D024_Nome_Empresa', "WHERE D024_Id = $r_D024_Id");
		foreach($tmp as &$value) { $value['selected'] = true; }
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('cad001FormDocumentos');
		$formArray = array(
			'campos' => array(
				'geral' => array(
				)
			)
		);
		$formArray['campos']['geral']['__D087_D024_Id'] 		= array('name' => '__D087_D024_Id', 'label'=>'Cliente', 'type' => 'select', 'extra'=>'style="width:382px;"', 'disabled' => true, 'reject'=> true, 'select' => 
			$tmp
		);
		$formArray['campos']['geral']['D087_D024_Id'] 			= array('name' => 'D087_D024_Id', 'type' => 'hidden', 'value' => $r_D024_Id);
		$formArray['campos']['geral']['D087_C007_Id'] 			= array('name' => 'D087_C007_Id', 'type' => 'hidden', 'value' => $g['usuarioAtual']);
		$formArray['campos']['geral']['D087_Data_Hora_UpLoad'] 	= array('name' => 'D087_Data_Hora_UpLoad', 'type' => 'hidden', 'value' => date('Y-m-d H:i:s'));
		$formArray['campos']['geral']['D087_Data_Validade'] 	= array('name' => 'D087_Data_Validade', 'label'=>'Validade do Arquivo', 'type' => 'data', 'length' => array(8, 8));
		$formArray['campos']['geral']['D087_Descricao'] 		= array('name' => 'D087_Descricao', 'label'=>'Descrição', 'type' => 'textarea','length' => array(3,60));
		$formArray['campos']['geral']['uploadArquivo'] 			= array('name' => 'uploadArquivo', 'label' => 'Arquivo documento', 'type' => 'file', 'length' => array(24, 24), 'reject' => true, 'uppercase' => false);
		$formArray['campos']['geral']['D087_Nome_Arquivo'] 		= array('name' => 'D087_Nome_Arquivo', 'type' => 'hidden', 'uppercase' => false);
		if (!empty($r_acaoId)) {
			 $formArray['campos']['geral']['cad001FormDocumentosBtnNew'] = array('name' => 'cad001FormDocumentosBtnNew', 'type' => 'button', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		}
		//$formArray['campos']['geral']['cad001FormDocumentosBtnCln'] = array('name' => 'cad001FormDocumentosBtnCln', 'type' => 'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['geral']['cad001FormDocumentosBtnAdd'] = array('name' => 'cad001FormDocumentosBtnAdd', 'type' => 'submit', 'reload' => false, 'value' => empty($r_acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
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
						$form->campoSetarValor('D087_Nome_Arquivo', $arquivo['name'], true);
						if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
							// Gravar o arquivo no disco
							$pathinfo = pathinfo($arquivo['name']);
							$pathinfo['extension'] = empty($pathinfo['extension']) ? '.tmp' : $pathinfo['extension'];
							$dir = "{$g['pathDados']}documentos/D024_".str_repeat('0',5-strlen($form->campoValorEnviado('D087_D024_Id'))).$form->campoValorEnviado('D087_D024_Id');
							//$dir = "{$g['pathDados']}di";
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

	case 'cad001FormDocumentosVeiculos':
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
		$form_tabela = 'D127';
		$form_campo_pk = 'D127_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = array('cad001FormDocumentoVeiculosBtn', '', 'cad001FormDocumentoVeiculosBtnNew');
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad001/form/cad001FormDocumentosVeiculos/';
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('formVeiculos_cad001');
		$formArray = array(
			'campos' => array(
				'geral' => array(
				'D127_Descricao' => array('name' => 'D127_Descricao', 'label' => 'Descrição', 'type' => 'text', 'length' => array(40,60)),
				'uploadArquivoDocumento' => array('name' => 'uploadArquivoDocumento', 'reject'=>true, 'label' => 'Upload Documento', 'type' => 'file', 'length' => array(10,80)),
				'D127_Nome_Arquivo' => array('name' => 'D127_Nome_Arquivo', 'label' => 'Upload Documento', 'type' => 'hidden', 'uppercase'=>false, 'length' => array(10,80)),
				'D127_D116_Id' => array('name' => 'D127_D116_Id','label' => 'D127_D116_Id', 'value'=>@$r_D116_Id, 'type' => 'hidden','length' => array(10,7)),
				),
			) // 'campos'
		);
		$formArray['campos']['geral']['divReload'] 	= array('name' => 'divReload', 'label'=>'', 'type' => 'hidden','value' => @$r_divReload, 'reject' => true);
		if (!empty($acaoId)) {
			$formArray['campos']['geral']['cad001FormDocumentoVeiculosBtnNew'] = array('name' => 'cad001FormDocumentoVeiculosBtnNew', 'type'=>'submit', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		}
		$formArray['campos']['geral']['cad001FormDocumentoVeiculosBtn'] = array('name' => 'cad001FormDocumentoVeiculosBtn', 'type' => 'submit', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		
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
					// Insert ou Update?
					if (!empty($acaoId)) {
						
						$arquivo = $form->campoValorEnviado('uploadArquivoDocumento');
						if(!empty($arquivo['name'])){
							$form->campoSetarValor('D127_Nome_Arquivo', $arquivo['name'], true);
						}
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
						if ($arquivo['error'] == UPLOAD_ERR_OK) {
							// Gravar o arquivo no disco
							$pathinfo = pathinfo($arquivo['name']);
							$pathinfo['extension'] = empty($pathinfo['extension']) ? '.tmp' : $pathinfo['extension'];
							$dir = "{$g['pathDados']}veiculos/";
							//$dir = "{$g['pathDados']}di";
							is_dir($dir) or mkdir($dir, 0777, true);
							$file = $dir . '/' . $r_acaoId . '.' . $pathinfo['extension'];
							move_uploaded_file($arquivo['tmp_name'], $file);
						}
				} else {
					// Insert
					$arquivo = $form->campoValorEnviado('uploadArquivoDocumento');
					$form->campoSetarValor('D127_Nome_Arquivo', $arquivo['name'], true);
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						if ($arquivo['error'] == UPLOAD_ERR_OK) {
							// Gravar o arquivo no disco
							$pathinfo = pathinfo($arquivo['name']);
							$pathinfo['extension'] = empty($pathinfo['extension']) ? '.tmp' : $pathinfo['extension'];
							$dir = "{$g['pathDados']}veiculos/";
							//$dir = "{$g['pathDados']}di";
							is_dir($dir) or mkdir($dir, 0777, true);
							$file = $dir . '/' . $g['mysqlLastId'] . '.' . $pathinfo['extension'];
							move_uploaded_file($arquivo['tmp_name'], $file);
						}	
						//$alert = gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
						$acaoIdInserido = $g['mysqlLastId'];
						$D024_Id = $form->campoValorEnviado('D127_D116_Id');
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
		echo  $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		echo  $g['smarty']->fetch('form.tpl');
		
		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		//$botoesDir .= '<button type="submit" id="cad045FormDocumentoVeiculosBtn">Enviar</button>';
		//$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'cad001FormReferencias':
		global $g;
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;
		$D024_Id = (isset($r_cad001FormReferencias)) ? $r_cad001FormReferencias['D061_D024_Id'] : $r_D024_Id;
		$sql = "SELECT D061_Id FROM D061 WHERE D061_D024_Id='{$D024_Id}'";
		$result = mysql_fetch_assoc(mysql_query($sql));
		
		$r_acaoId = $result['D061_Id'];
		
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
		$form_tabela = 'D061';
		$form_campo_pk = 'D061_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = array('cad001FormReferenciasBtnAdd', 'cad001FormReferenciasBtnCln', 'cad001FormReferenciasBtnNew');
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad001/form/cad001FormReferencias/';
		
		$form_editor = array(
			'geral' => '{"tituloPessoais":{"top":4,"left":332,"width":null,"hidden":1},"D061_DDD_Telefone_1":{"top":30,"left":193,"width":35,"hidden":0},"D061_Telefone_1":{"top":30,"left":288,"width":75,"hidden":0},"D061_Referencia_Tipo_1":{"top":30,"left":374,"width":125,"hidden":0},"D061_Observacao_1":{"top":30,"left":542.0000610351562,"width":225,"hidden":0},"D061_Nome_Contato_1":{"top":30,"left":15,"width":125,"hidden":0},"tituloComerciais":{"top":96,"left":331,"width":null,"hidden":1},"D061_DDD_Telefone_2":{"top":53,"left":193,"width":35,"hidden":0},"D061_Telefone_2":{"top":53,"left":288,"width":75,"hidden":0},"D061_Referencia_Tipo_2":{"top":53,"left":374,"width":125,"hidden":0},"D061_Observacao_2":{"top":53,"left":542.0000610351562,"width":225,"hidden":0},"D061_Nome_Contato_2":{"top":53,"left":15,"width":125,"hidden":0},"tituloBancarias":{"top":188,"left":335,"width":null,"hidden":1},"D061_DDD_Telefone_3":{"top":76,"left":193,"width":35,"hidden":0},"D061_Telefone_3":{"top":76,"left":288,"width":75,"hidden":0},"D061_Referencia_Tipo_3":{"top":76,"left":374,"width":125,"hidden":0},"D061_Observacao_3":{"top":76,"left":542.0000610351562,"width":225,"hidden":0},"D061_Nome_Contato_3":{"top":76,"left":15,"width":125,"hidden":0},"cad001FormReferenciasBtnCln":{"top":280.9999694824219,"left":784.0000610351562,"width":null,"hidden":0},"cad001FormReferenciasBtnAdd":{"top":280.9999694824219,"left":720.0000610351562,"width":null,"hidden":0},"D061_DDD_Telefone_Comercial_1":{"top":122,"left":193,"width":35,"hidden":0},"D061_Telefone_Comercial_1":{"top":122,"left":288,"width":75,"hidden":0},"D061_Referencia_Comercial_Tipo_1":{"top":122,"left":374,"width":125,"hidden":0},"D061_Observacao_Comercial_1":{"top":122,"left":542.0000610351562,"width":225,"hidden":0},"D061_Referencia_Comercial_1":{"top":122,"left":15,"width":125,"hidden":0},"D061_DDD_Telefone_Comercial_2":{"top":145,"left":193,"width":35,"hidden":0},"D061_Telefone_Comercial_2":{"top":145,"left":288,"width":75,"hidden":0},"D061_Referencia_Comercial_Tipo_2":{"top":145,"left":374,"width":125,"hidden":0},"D061_Observacao_Comercial_2":{"top":145,"left":542.0000610351562,"width":225,"hidden":0},"D061_Referencia_Comercial_2":{"top":145,"left":15,"width":125,"hidden":0},"D061_DDD_Telefone_Comercial_3":{"top":168,"left":193,"width":35,"hidden":0},"D061_Telefone_Comercial_3":{"top":168,"left":288,"width":75,"hidden":0},"D061_Referencia_Comercial_Tipo_3":{"top":168,"left":374,"width":125,"hidden":0},"D061_Observacao_Comercial_3":{"top":168,"left":542.0000610351562,"width":225,"hidden":0},"D061_Referencia_Comercial_3":{"top":168,"left":15,"width":125,"hidden":0},"D061_DDD_Telefone_Banco_1":{"top":214,"left":193,"width":35,"hidden":0},"D061_Telefone_Banco_1":{"top":214,"left":288,"width":75,"hidden":0},"D061_Referencia_Bancaria_Tipo_1":{"top":214,"left":374,"width":125,"hidden":0},"D061_Observacao_Banco_1":{"top":214,"left":542.0000610351562,"width":225,"hidden":0},"D061_Referencia_Bancaria_1":{"top":214,"left":15,"width":125,"hidden":0},"D061_DDD_Telefone_Banco_2":{"top":236.99996948242188,"left":193,"width":35,"hidden":0},"D061_Telefone_Banco_2":{"top":236.99996948242188,"left":288,"width":75,"hidden":0},"D061_Referencia_Bancaria_Tipo_2":{"top":236.99996948242188,"left":374,"width":125,"hidden":0},"D061_Observacao_Banco_2":{"top":236.99996948242188,"left":542.0000610351562,"width":225,"hidden":0},"D061_Referencia_Bancaria_2":{"top":236.99996948242188,"left":15,"width":125,"hidden":0},"D061_DDD_Telefone_Banco_3":{"top":259.9999694824219,"left":193,"width":35,"hidden":0},"D061_Telefone_Banco_3":{"top":259.9999694824219,"left":288,"width":75,"hidden":0},"D061_Referencia_Bancaria_Tipo_3":{"top":259.9999694824219,"left":374,"width":125,"hidden":0},"D061_Observacao_Banco_3":{"top":259.9999694824219,"left":542.0000610351562,"width":225,"hidden":0},"D061_Referencia_Bancaria_3":{"top":259.9999694824219,"left":15,"width":125,"hidden":0}}'        
		);
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					'D061_D024_Id' => array('name' => 'D061_D024_Id', 'label' => 'Nome', 'value'=>$D024_Id, 'type' => 'hidden', 'length' => array(60,80)),
					//Pessoais
					'tituloPessoais' => array('name' => 'tituloPessoais', 'label' => 'Pessoais', 'type' => 'titulo','reject'=>true, 'length' => array(60,80)),
					'D061_Nome_Contato_1' => array('name' => 'D061_Nome_Contato_1', 'label' => 'Nome', 'type' => 'text', 'length' => array(20,80)),
					'D061_DDD_Telefone_1' => array('name' => 'D061_DDD_Telefone_1', 'label' => 'Telefone', 'type' => 'text', 'length' => array(2,2), 'align'=>'right'),
					'D061_Telefone_1' => array('name' => 'D061_Telefone_1', 'label' => '', 'type' => 'text', 'length' => array(10,10), 'align'=>'right'),
					'D061_Referencia_Tipo_1' => array('name' => 'D061_Referencia_Tipo_2', 'label' => 'Tipo', 'type' => 'text', 'length' => array(20,80), 'align'=>'right'),
					'D061_Observacao_1' => array('name' => 'D061_Observacao_1', 'label' => 'Observação', 'type' => 'text', 'length' => array(40,80), 'align'=>'right'),
					
					'D061_Nome_Contato_2' => array('name' => 'D061_Nome_Contato_2', 'label' => 'Nome', 'type' => 'text', 'length' => array(20,80)),
					'D061_DDD_Telefone_2' => array('name' => 'D061_DDD_Telefone_2', 'label' => 'Telefone', 'type' => 'text', 'length' => array(2,2), 'align'=>'right'),
					'D061_Telefone_2' => array('name' => 'D061_Telefone_2', 'label' => '', 'type' => 'text', 'length' => array(10,10), 'align'=>'right'),
					'D061_Referencia_Tipo_2' => array('name' => 'D061_Referencia_Tipo_2', 'label' => 'Tipo', 'type' => 'text', 'length' => array(20,80), 'align'=>'right'),
					'D061_Observacao_2' => array('name' => 'D061_Observacao_2', 'label' => 'Observação', 'type' => 'text', 'length' => array(40,80), 'align'=>'right'),
					
					'D061_Nome_Contato_3' => array('name' => 'D061_Nome_Contato_3', 'label' => 'Nome', 'type' => 'text', 'length' => array(20,80)),
					'D061_DDD_Telefone_3' => array('name' => 'D061_DDD_Telefone_3', 'label' => 'Telefone', 'type' => 'text', 'length' => array(2,2), 'align'=>'right'),
					'D061_Telefone_3' => array('name' => 'D061_Telefone_3', 'label' => '', 'type' => 'text', 'length' => array(10,10), 'align'=>'right'),
					'D061_Referencia_Tipo_3' => array('name' => 'D061_Referencia_Tipo_2', 'label' => 'Tipo', 'type' => 'text', 'length' => array(20,80), 'align'=>'right'),
					'D061_Observacao_3' => array('name' => 'D061_Observacao_3', 'label' => 'Observação', 'type' => 'text', 'length' => array(40,80), 'align'=>'right'),
					
					//Comerciais
					'tituloComerciais' => array('name' => 'tituloComerciais', 'label' => 'Comerciais', 'type' => 'titulo','reject'=>true, 'length' => array(60,80)),
					'D061_Referencia_Comercial_1' => array('name' => 'D061_Referencia_Comercial_1', 'label' => 'Nome', 'type' => 'text', 'length' => array(20,80)),
					'D061_DDD_Telefone_Comercial_1' => array('name' => 'D061_DDD_Telefone_Comercial_1', 'label' => 'Telefone', 'type' => 'text', 'length' => array(2,2), 'align'=>'right'),
					'D061_Telefone_Comercial_1' => array('name' => 'D061_Telefone_Comercial_1', 'label' => '', 'type' => 'text', 'length' => array(10,10), 'align'=>'right'),
					'D061_Referencia_Comercial_Tipo_1' => array('name' => 'D061_Referencia_Comercial_Tipo_1', 'label' => 'Tipo', 'type' => 'text', 'length' => array(20,80), 'align'=>'right'),
					'D061_Observacao_Comercial_1' => array('name' => 'D061_Observacao_Comercial_1', 'label' => 'Observação', 'type' => 'text', 'length' => array(40,80), 'align'=>'right'),
					
					'D061_Referencia_Comercial_2' => array('name' => 'D061_Referencia_Comercial_2', 'label' => 'Nome', 'type' => 'text', 'length' => array(20,80)),
					'D061_DDD_Telefone_Comercial_2' => array('name' => 'D061_DDD_Telefone_Comercial_2', 'label' => 'Telefone', 'type' => 'text', 'length' => array(2,2), 'align'=>'right'),
					'D061_Telefone_Comercial_2' => array('name' => 'D061_Telefone_Comercial_2', 'label' => '', 'type' => 'text', 'length' => array(10,10), 'align'=>'right'),
					'D061_Referencia_Comercial_Tipo_2' => array('name' => 'D061_Referencia_Comercial_Tipo_2', 'label' => 'Tipo', 'type' => 'text', 'length' => array(20,80), 'align'=>'right'),
					'D061_Observacao_Comercial_2' => array('name' => 'D061_Observacao_Comercial_2', 'label' => 'Observação', 'type' => 'text', 'length' => array(40,80), 'align'=>'right'),
					
					'D061_Referencia_Comercial_3' => array('name' => 'D061_Referencia_Comercial_3', 'label' => 'Nome', 'type' => 'text', 'length' => array(20,80)),
					'D061_DDD_Telefone_Comercial_3' => array('name' => 'D061_DDD_Telefone_Comercial_3', 'label' => 'Telefone', 'type' => 'text', 'length' => array(2,2), 'align'=>'right'),
					'D061_Telefone_Comercial_3' => array('name' => 'D061_Telefone_Comercial_3', 'label' => '', 'type' => 'text', 'length' => array(10,10), 'align'=>'right'),
					'D061_Referencia_Comercial_Tipo_3' => array('name' => 'D061_Referencia_Comercial_Tipo_3', 'label' => 'Tipo', 'type' => 'text', 'length' => array(20,80), 'align'=>'right'),
					'D061_Observacao_Comercial_3' => array('name' => 'D061_Observacao_Comercial_3', 'label' => 'Observação', 'type' => 'text', 'length' => array(40,80), 'align'=>'right'),
					
					//Bancárias
					'tituloBancarias' => array('name' => 'tituloBancarias', 'label' => 'Bancárias', 'type' => 'titulo','reject'=>true, 'length' => array(60,80)),
					'D061_Referencia_Bancaria_1' => array('name' => 'D061_Referencia_Bancaria_1', 'label' => 'Nome', 'type' => 'text', 'length' => array(20,80)),
					'D061_DDD_Telefone_Banco_1' => array('name' => 'D061_DDD_Telefone_Banco_1', 'label' => 'Telefone', 'type' => 'text', 'length' => array(2,2), 'align'=>'right'),
					'D061_Telefone_Banco_1' => array('name' => 'D061_Telefone_Banco_1', 'label' => '', 'type' => 'text', 'length' => array(10,10), 'align'=>'right'),
					'D061_Referencia_Bancaria_Tipo_1' => array('name' => 'D061_Referencia_Bancaria_Tipo_1', 'label' => 'Tipo', 'type' => 'text', 'length' => array(20,80), 'align'=>'right'),
					'D061_Observacao_Banco_1' => array('name' => 'D061_Observacao_Banco_1', 'label' => 'Observação', 'type' => 'text', 'length' => array(40,80), 'align'=>'right'),
					
					'D061_Referencia_Bancaria_2' => array('name' => 'D061_Referencia_Bancaria_2', 'label' => 'Nome', 'type' => 'text', 'length' => array(20,80)),
					'D061_DDD_Telefone_Banco_2' => array('name' => 'D061_DDD_Telefone_Banco_2', 'label' => 'Telefone', 'type' => 'text', 'length' => array(2,2), 'align'=>'right'),
					'D061_Telefone_Banco_2' => array('name' => 'D061_Telefone_Banco_2', 'label' => '', 'type' => 'text', 'length' => array(10,10), 'align'=>'right'),
					'D061_Referencia_Bancaria_Tipo_2' => array('name' => 'D061_Referencia_Bancaria_Tipo_2', 'label' => 'Tipo', 'type' => 'text', 'length' => array(20,80), 'align'=>'right'),
					'D061_Observacao_Banco_2' => array('name' => 'D061_Observacao_Banco_2', 'label' => 'Observação', 'type' => 'text', 'length' => array(40,80), 'align'=>'right'),
					
					'D061_Referencia_Bancaria_3' => array('name' => 'D061_Referencia_Bancaria_3', 'label' => 'Nome', 'type' => 'text', 'length' => array(20,80)),
					'D061_DDD_Telefone_Banco_3' => array('name' => 'D061_DDD_Telefone_Banco_3', 'label' => 'Telefone', 'type' => 'text', 'length' => array(2,2), 'align'=>'right'),
					'D061_Telefone_Banco_3' => array('name' => 'D061_Telefone_Banco_3', 'label' => '', 'type' => 'text', 'length' => array(10,10), 'align'=>'right'),
					'D061_Referencia_Bancaria_Tipo_3' => array('name' => 'D061_Referencia_Bancaria_Tipo_3', 'label' => 'Tipo', 'type' => 'text', 'length' => array(20,80), 'align'=>'right'),
					'D061_Observacao_Banco_3' => array('name' => 'D061_Observacao_Banco_3', 'label' => 'Observação', 'type' => 'text', 'length' => array(40,80), 'align'=>'right'),
					
					'D061_C004_Id' => array('name' => 'D061_C004_Id', 'label' => 'Empresa Atual', 'type' => 'hidden', 'value'=>$g['empresaAtual'], 'length' => array(40,80)),
				)
			)
		);
		$form = new Formulario('cad001FormReferencias');
		// Botão Limpar / Novo Registro
		if (!empty($acaoId)) {
			$formArray['campos']['geral']['cad001FormReferenciasBtnNew'] = array('name' => 'cad001FormReferenciasBtnNew', 'type'=>'submit', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		}
		$formArray['campos']['geral']['cad001FormReferenciasBtnCln'] = array('name' => 'cad001FormReferenciasBtnCln', 'type'=>'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['geral']['cad001FormReferenciasBtnAdd'] = array('name' => 'cad001FormReferenciasBtnAdd', 'type'=>'button', 'reload' => false, 'value' => empty($r_acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
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
						echo gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!');
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
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

	case 'cad001FormPreCadastrarCliente':
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
			$formId = (isset($r_formcad001FormCadastro)) ? $r_formcad001FormCadastro['formId'] : $r_formId;
		} else {
			$formId = uniqid();
		}

		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'D024';
		$form_campo_pk = 'D024_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad001FormPreCadastrarCliente_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad001/form/cad001FormPreCadastrarCliente/';
		// Form Editor
		$form_editor = array(
			'cadastro' => '{"tituloContato":{"top":-20,"left":323,"width":null,"hidden":1},"D024_D021_Id":{"top":7,"left":5,"width":42,"hidden":1},"D024_Flag_Revenda_Consumidor":{"top":7,"left":5,"width":172,"hidden":1},"D024_Nome_Empresa":{"top":7,"left":40,"width":275,"hidden":0},"D024_Flag_Pessoa_Fisica_Juridica":{"top":7,"left":463,"width":112,"hidden":0},"D024_Nome_Fantasia":{"top":30,"left":26,"width":275,"hidden":0},"D024_Flag_Contribuinte":{"top":30,"left":463,"width":128,"hidden":0},"D024_E_Mail":{"top":53,"left":46,"width":225,"hidden":0},"D024_Cpf":{"top":53,"left":503,"width":88,"hidden":0},"D024_Inscricao_Estadual":{"top":76,"left":60,"width":175,"hidden":0},"D024_Rg":{"top":76,"left":507,"width":88,"hidden":0},"D024_Cnpj":{"top":99,"left":42,"width":125,"hidden":0},"D024_D039_Id":{"top":99,"left":533,"width":34,"hidden":1},"D024_D046_Id":{"top":122,"left":44,"width":34,"hidden":1},"D024_Flag_Cliente":{"top":122,"left":538,"width":48,"hidden":0},"buttonSearchCep":{"top":143,"left":160,"width":null,"hidden":0},"D024_Cep":{"top":145,"left":55,"width":75,"hidden":0},"D024_Flag_Fornecedor":{"top":145,"left":514,"width":48,"hidden":0},"D024_Endereco":{"top":168,"left":28,"width":275,"hidden":0},"D024_Numero":{"top":168,"left":485,"width":88,"hidden":0},"D024_D018_Id":{"top":191,"left":61,"width":41,"hidden":0},"D024_D020_Id":{"top":191,"left":377,"width":208,"hidden":0},"D024_D030_Id":{"top":214,"left":54,"width":314,"hidden":0},"D024_Bairro":{"top":214,"left":493,"width":88,"hidden":0},"D024_DDD_Fax":{"top":237,"left":56,"width":35,"hidden":0},"D024_Fax":{"top":237,"left":128,"width":95,"hidden":0},"D024_DDD_Telefone_1":{"top":260,"left":24,"width":30,"hidden":0},"D024_Telefone_1":{"top":260,"left":128,"width":95,"hidden":0},"D024_DDD_Celular":{"top":260,"left":441,"width":35,"hidden":0},"D024_Celular":{"top":260,"left":528,"width":88,"hidden":0},"D024_DDD_Telefone_2":{"top":283,"left":24,"width":30,"hidden":0},"D024_Telefone_2":{"top":283,"left":128,"width":95,"hidden":0},"D024_Nome_Contato":{"top":283,"left":485,"width":88,"hidden":0},"D024_Web_Site":{"top":306,"left":35,"width":175,"hidden":0},"D024_Data_Fundacao":{"top":306,"left":415,"width":88,"hidden":0},"D024_Observacoes":{"top":329,"left":14,"height":73,"width":327,"hidden":0},"D024_Flag_Pre_Cadastro":{"top":397.9999694824219,"left":506,"width":56,"hidden":0},"D024_Flag_Lista_Preco":{"top":420.9999694824219,"left":13,"width":68,"hidden":0},"D024_Data_Cadastro":{"top":420.9999694824219,"left":390,"width":100,"hidden":0},"D013_Nome_Contato":{"top":466.9999694824219,"left":40,"width":260,"hidden":1},"D013_E_Mail":{"top":466.9999694824219,"left":382,"width":232,"hidden":1},"D013_Tipo_Contato":{"top":489.9999694824219,"left":54,"width":260,"hidden":1},"D013_Data_Nascimento":{"top":489.9999694824219,"left":386,"width":124,"hidden":1},"D013_DDD_Telefone_1":{"top":512.9999694824219,"left":32,"width":34,"hidden":1},"D013_Telefone_1":{"top":512.9999694824219,"left":128,"width":124,"hidden":1}}'
		);

		// novo formulário
		$form = new Formulario('formcad001FormCadastro');

		// Definição do Formulário
		$formArray = array(
		'campos' => array(
			'cadastro' => array(
				'D024_Nome_Empresa' => array('name' => 'D024_Nome_Empresa','extra' => "onchange=\"campoNomeFantasia(this, '{$g['divId']}');\"", 'label' => 'Nome', 'type' => 'text', 'length' => array(50,255)),
				'D024_Flag_Pessoa_Fisica_Juridica' => array('name' => 'D024_Flag_Pessoa_Fisica_Juridica', 'label' => 'Pessoa', 'type' => 'select', 'align'=>'right', 'style'=>'float:right', 'select' => 
					array(
						array('title' => '...', 'value' => '', 'selected' => true),
						array('title' => 'Jurídica', 'value' => 'J'),
						array('title' => 'Física', 'value' => 'F'), 
						array('title' => 'Internacional', 'value' => 'I'), 
					)
				),
				'D024_Nome_Fantasia' => 			array('name' => 'D024_Nome_Fantasia', 'label' => 'Fantasia',	'type' => 'text', 'length' => array(50,255)),
				'D024_Flag_Revenda_Consumidor' => 	array('name' => 'D024_Flag_Revenda_Consumidor', 'label' => 'Tipo', 'restricao' => false, 'type' => 'select', 'align'=>'right', 'style'=>'float:right', 'select' => 
					array(
						array('title' => '...', 'value' => '', 'selected' => true),   
						array('title' => 'Revenda', 'value' => '1'),   
						array('title' => 'Consumidor Atacado', 'value' => '2'),   
						array('title' => 'Consumidor Varejo', 'value' => '3')   
					)
				),   
				'D024_Flag_Contribuinte' => 	array('name' => 'D024_Flag_Contribuinte', 'label' => 'Tipo', 'type' => 'select', 'align'=>'right', 'style'=>'float:right', 'select' => 
					array(
						array('title' => '...', 'value' => '', 'selected' => true),
						array('title' => 'Não contribuinte', 'value' => '0' ),   
						array('title' => 'Consumidor Final', 'value' => '1'),   
						array('title' => 'Revenda', 'value' => '2'),   
						array('title' => 'Produtor rural', 'value' => '3'),
						array('title' => 'Simples', 'value' => '4'),   
						array('title' => 'Industria', 'value' => '5')   
					)
				),
				'D024_E_Mail' => array('name' => 'D024_E_Mail', 'label' => 'E-mail', 'type' => 'text', 'length' => array(40,40), 'restricao' => false),
				'D024_Flag_Fornecedor' => array('name' => 'D024_Flag_Fornecedor', 'label' => 'Fornecedor','align'=>'right', 'style'=>'float:right', 'type' => 'select', 'restricao' => false, 'select' => 
					array(
						array('title' => '...', 'value' => '', 'selected' => true),
						array('title' => 'Sim', 'value' => 'S'),
						array('title' => 'Não', 'value' => 'N')
					)
				),
				'D024_Flag_Pre_Cadastro' => array('name' => 'D024_Flag_Pre_Cadastro', 'disabled' => true,'label' => 'Pré-Cadastro', 'type' => 'select', 'restricao' => false, 'select' => 
					array(
						array('title' => '...', 'value' => ''),
						array('title' => 'Sim', 'value' => 'S', 'selected' => true), 
						array('title' => 'Não', 'value' => 'N') 
					)
				),
				'D024_Flag_Cliente' => array('name' => 'D024_Flag_Cliente', 'disabled' => true,'label' => 'Cliente', 'type' => 'select', 'restricao' => false, 'select' => 
					array(
						array('title' => '...', 'value' => ''),
						array('title' => 'Sim', 'value' => 'S','selected' => true),
						array('title' => 'Não', 'value' => 'N')
					)
				),
				'D024_D046_Id' => array('name' => 'D024_D046_Id', 'label' => 'Grupo', 'type' => 'select', 'restricao' => false, 'select' => 
					gGeraSelect('D046', 'D046_Id', 'D046_Nome','', true)
				),
				'D024_D039_Id' =>  array('name' => 'D024_D039_Id', 'label' => 'Sub-Grupo','align'=>'right', 'style'=>'float:right', 'type' => 'select', 'restricao' => false, 'select' => 
					gGeraSelect('D039', 'D039_Id', 'D039_Descricao','', true)
				),
				'D024_Inscricao_Estadual' => 		array('name' => 'D024_Inscricao_Estadual',	'label' => 'Insc. Estadual', 'type' => 'text', 'length' => array(20,255), 'restricao' => false),
				'D024_Rg' => 	array('name' => 'D024_Rg', 'label' => 'RG', 'type' => 'text', 'length' => array(14,30), 'align' => 'right', 'style' => 'float:right', 'restricao' => false),
				'D024_Cnpj' => 	array('name' => 'D024_Cnpj', 'label' => 'CNPJ', 'type' => 'cnpj', 'length' => array(20,20)),
				'D024_Cpf' => 	array('name' => 'D024_Cpf', 'label' => 'CPF', 'type' => 'cpf', 'align' => 'right', 'style' => 'float:right', 'restricao' => false, 'length' => array(14,30)),
				'D024_Cep' =>     array('name' => 'D024_Cep',    'label' => 'CEP', 'type' => 'cep', 'length' => array(10,15), 'restricao' => false),
				'buttonSearchCNPJ' =>     array('name' => 'buttonSearchCNPJ',    'label' => '', 'type' => 'button', 'extra' => "style='padding: 0; heigth: 20px; width: 50px;' onclick='pesquisaCNPJ(\"{$g['divId']}\", \"{$g['divIdRoot']}\",\"D024_Cep\", {\"EnderecoDb\": \"D024_Endereco\", \"Cep\": \"D024_Cep\", \"BairroDb\": \"D024_Bairro\", \"UfDb\": \"D024_D018_Id\",\"CidadeDb\": \"D024_D020_Id\", \"PaisDb\": \"D024_D030_Id\"})'", 'align'=>'left', 'value'=>'Buscar', 'reject'=>true),
				

				'buttonSearchCep' =>     array('name' => 'buttonSearchCep',    'label' => '', 'type' => 'button', 'extra' => "style='padding: 0; heigth: 20px; width: 50px;' onclick='pesquisaCep(false,\"{$g['divId']}\", \"{$g['divIdRoot']}\",\"D024_Cep\", {\"EnderecoDb\": \"D024_Endereco\", \"Cep\": \"D024_Cep\", \"BairroDb\": \"D024_Bairro\", \"UfDb\": \"D024_D018_Id\",\"CidadeDb\": \"D024_D020_Id\", \"PaisDb\": \"D024_D030_Id\"})'", 'align'=>'left', 'value'=>'Buscar', 'reject'=>true),
				'D024_Endereco' => 	array('name' => 'D024_Endereco', 'label' => 'Endereço',	'type' => 'text', 'length' => array(50,255), 'restricao' => false),
				'D024_Numero' => array('name' => 'D024_Numero', 'label' => 'Número', 	'type' => 'text', 'align'=>'right', 'style'=>'float:right', 'restricao' => false, 'length' => array(14,14)),
				'D024_D018_Id' => array('name' => 'D024_D018_Id', 'label' => 'UF', 	'type' => 'select','extra' => "onchange=\"alteraEstado(this, '{$g['divId']}')\"", 'restricao' => false, 'select' => 
					gGeraSelect('D018', 'D018_Id', 'D018_UF','', true)
				),
				'D024_D020_Id' => array('name' => 'D024_D020_Id', 'label' => 'Cidade', 	'type' => 'select', 'align'=>'right', 'style'=>'float:right', 'restricao' => false, 'select' => 
					gGeraSelect('D020', 'D020_Id', 'D020_Nome_Cidade','', true)
				),
				'D024_D030_Id' => array('name' => 'D024_D030_Id', 'label' => 'País', 'type' => 'select', 'value' => 38, 'restricao' => false, 'select' => 
					gGeraSelect('D030', 'D030_Id', 'D030_Nome_Pais,D030_Codigo_Area', '', false, 30)
				),
				'D024_Bairro' => array('name' => 'D024_Bairro', 'label' => 'Bairro', 'type' => 'text', 'align'=>'right', 'style'=>'float:right', 'restricao' => false, 'length' => array(14,14)),
				'D024_DDD_Fax' => array('name' => 'D024_DDD_Fax', 'label' => 'Fax', 'type' => 'text','align'=>'right', 'length' => array(2,2), 'restricao' => false),
				'D024_Fax' => array('name' => 'D024_Fax', 'label' => '', 'type' => 'text','align'=>'right', 'style'=>'float:right', 'length' => array(14,14), 'restricao' => false),
				'D024_DDD_Telefone_1' => array('name' => 'D024_DDD_Telefone_1',	'label' => 'Telefone 1', 'type' => 'text', 'length' => array(1,4), 'restricao' => false),
				'D024_Telefone_1' => array('name' => 'D024_Telefone_1',	'label' => '', 'type' => 'text','align'=>'right', 'extra' => '', 'length' => array(14,9), 'restricao' => false),
				'D024_DDD_Celular' => array('name' => 'D024_DDD_Celular', 'label' => 'Celular', 'type' => 'text', 'align'=>'right', 'length' => array(2,2), 'restricao' => false),
				'D024_Celular' => array('name' => 'D024_Celular',	'label' => '', 'type' => 'text','align'=>'right','style'=>'float:right', 'length' => array(14,14), 'restricao' => false),
				'D024_DDD_Telefone_2' => array('name' => 'D024_DDD_Telefone_2',	'label' => 'Telefone 2', 'type' => 'text', 'length' => array(1,2), 'restricao' => false),
				'D024_Telefone_2' => array('name' => 'D024_Telefone_2', 'label' => '', 'type' => 'text', 'align' => 'right', 'length' => array(14,9), 'restricao' => false),
				'D024_Nome_Contato' => 	array('name' => 'D024_Nome_Contato','label' => 'Contato', 'type' => 'text','align'=>'right', 'style'=>'float:right', 'length' => array(14,14), 'restricao' => false),
				'D024_Web_Site' => array('name' => 'D024_Web_Site', 'label' => 'Website', 'type' => 'text', 'length' => array(30,40), 'restricao' => false),
				'D024_Inscricao_Estadual' => array('name' => 'D024_Inscricao_Estadual', 'label' => 'I.E.', 'type' => 'text', 'length' => array(30,30), 'restricao' => false),
				'D024_Data_Fundacao' => array('name' => 'D024_Data_Fundacao','align'=>'right', 'style'=>'float:right', 'label' => 'Fundação/Nascimento', 'type' => 'data', 'length' => array(15,30), 'restricao' => false),
				'D024_Observacoes' => array('name' => 'D024_Observacoes', 'label' => 'Observação', 	'type' => 'textarea', 'length' => array(4,57), 'restricao' => false),
				'D024_Flag_Liberado' => array('name' => 'D024_Flag_Liberado', 'type' => 'hidden','value' => 'N'),
				'D024_Data_Cadastro' => array('name' => 'D024_Data_Cadastro', 'label' => 'Data cadastro','value' => date("d/m/Y"), 'align'=>'right', 'type' => 'data', 'style'=>'float:right; margin-left:60px', 'length' => array(15,14), 'restricao' => false),
				'D024_D021_Id' => array('name' => 'D024_D021_Id', 'label' => 'Representante', 'type' => 'select', 'select' => 
					gGeraSelect("D021", "D021_Id", "D021_Nome_Empresa", "where D021_Data_Irregular='0000-00-00' or  D021_Data_Irregular is null order by D021_Nome_Empresa", true)
				),
				'D024_Flag_Lista_Preco' => 	array('name' => 'D024_Flag_Lista_Preco', 'label' => 'Lista Preço', 'type' => 'select', 'select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Preço 1', 'value' => '1'),   
							array('title' => 'Preço 2', 'value' => '2'),   
							array('title' => 'Preço 3', 'value' => '3'),   
						)
					),
					'tituloContato' => array('name' => 'tituloContato', 'label' => 'Contato','type' => 'titulo', 'reject' => true),
					'D013_Nome_Contato' => array('name' => 'D013_Nome_Contato', 'label'=>'Nome', 'restricao' => false, 'type' => 'text','length' => array(50,60),'reject' => true),
					'D013_E_Mail' => array('name' => 'D013_E_Mail', 'label'=>'E-Mail', 'type' => 'text','length' => array(50,90),'reject' => true),
					'D013_Tipo_Contato' => array('name' => 'D013_Tipo_Contato', 'label'=>'Tipo', 'type' => 'text','length' => array(50,90),'reject' => true),
					'D013_Data_Nascimento' => array('name' => 'D013_Data_Nascimento', 'label'=>'Nascimento', 'type' => 'data','length' => array(20,30),'reject' => true),
					'D013_DDD_Telefone_1' => array('name' => 'D013_DDD_Telefone_1', 'label'=>'Telefone', 'type' => 'text','length' => array(2,2),'reject' => true),
					'D013_Telefone_1' => array('name' => 'D013_Telefone_1', 'label'=>'', 'type' => 'text','length' => array(20,30), 'align'=>'right','reject' => true),
					'formId' => array('name' => 'formId', 'label' => '', 'type' => 'hidden', 'value' => $formId, 'reject' => true, 'restricao' => false),
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
						// Imprime uma mensagem e fecha a janela
						$acaoId = $g['mysqlLastId'];
						$D013_Nome_Contato = $form->campoValorEnviado('D013_Nome_Contato');
						if($D013_Nome_Contato !== false){
							$D013_E_Mail = $form->campoValorEnviado('D013_E_Mail');
							$D013_Tipo_Contato = $form->campoValorEnviado('D013_Tipo_Contato');
							$D013_Data_Nascimento = $form->campoValorEnviado('D013_Data_Nascimento');
							$D013_DDD_Telefone_1 = $form->campoValorEnviado('D013_DDD_Telefone_1');
							$D013_Telefone_1 = $form->campoValorEnviado('D013_Telefone_1');
							mysql_query("INSERT INTO D013(D013_Nome_Contato,D013_E_Mail,D013_Tipo_Contato,D013_Data_Nascimento,D013_DDD_Telefone_1,D013_Telefone_1,D013_D024_Id)
										VALUES ('{$D013_Nome_Contato}','{$D013_E_Mail}','{$D013_Tipo_Contato}','{$D013_Data_Nascimento}','{$D013_DDD_Telefone_1}','{$D013_Telefone_1}','{$acaoId}')
									");
						}
						$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
						echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
								setTimeout(function() {
									//abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}', unique(), '', 'Editar Cadastro - ID: ' + '{$acaoId}', '/cad/cad001/content/cad001contentCadastro/', '&acaoId=' + encodeURIComponent('{$acaoId}'), '[1000,700]');
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
	
	case 'cad001FormEtiquetasClientes':
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
	$form_tabela = '';
	$form_campo_pk = '';
	// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
	$enviar_btnId = 'cad001FormEtiquetasClientes_Btn';
	// Form que será executado ao clicar no botão
	$enviar_acaoHref = '/cad/cad001/form/cad001FormEtiquetasClientes/';
	
	// Definição do Formulário
	$formArray = array(
		'campos' => array(
			'geral' => array(
				// Campo normal (do banco)
				//'D024_Id_Inicial' => array('name' => 'D024_Id_Inicial', 'label' => 'Id Cliente Inicial', 'type' => 'text', 'length' => array(20,30)),
				//'D024_Id_Final' => array('name' => 'D024_Id_Final', 'label' => 'Id Cliente Final', 'type' => 'text', 'length' => array(20,30)),
				'D024_Classes_Ativos' => array('name' => 'D024_Classes_Ativos', 'label' => 'Classes Ativos<br><i>(T para todos)</i>', 'type' => 'text', 'length' => array(20,30)),
				'D024_Classes_Inativos' => array('name' => 'D024_Classes_Inativos', 'label' => 'Classes Inativos<br><i>(T para todos)</i>', 'type' => 'text', 'length' => array(20,30)),
				'Quantidade' => array('name' => 'Quantidade', 'label' => 'Quantidade<br><i>(intervalo)</i>', 'type' => 'text', 'length' => array(20,30)),
			)
		)
	);
	$form = new Formulario('cad001FormEtiquetasClientes');
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
			$retorno = $CAD001->imprimirEtiquetas($form);
			if($retorno <=0){
				// Fecha a janela, recarrega o parent (grid), emite uma mensagem de alerta e abre o registro para alteração (o que acabou de ser incluido)
				/*$alert = gGeraAlertMsg('ERRO!', $retorno, false);
				echo <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						// Fecha a janela, recarrega o grid e abre a alteração
						{$alert}
					} </script></div>
EOT;*/
				echo $retorno;
			}else{
				// Fecha a janela, recarrega o parent (grid), emite uma mensagem de alerta e abre o registro para alteração (o que acabou de ser incluido)
				echo gGeraAlertMsg("Operação OK", "Etiquetas enviadas para impressora!<br><br>$retorno clientes buscados");

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
	$botoesDir .= '<button type="submit" id="cad001FormEtiquetasClientes_Btn">Enviar</button>';
	$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
	$g['smarty']->assign('botoesId', $g['divId']);
	$g['smarty']->assign('botoesEsq', $botoesEsq);
	$g['smarty']->assign('botoesMid', $botoesMid);
	$g['smarty']->assign('botoesDir', $botoesDir);
	$g['smarty']->display('buttons-floatBottom.tpl');
break;

case 'cad001FormClienteCRMVendedor':
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
			$formId = (isset($r_formcad001FormCadastro)) ? $r_formcad001FormCadastro['formId'] : $r_formId;
		} else {
			$formId = uniqid();
		}

		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'D024';
		$form_campo_pk = 'D024_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad001FormCadastro_btn_CRM_Vendedor';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad001/form/cad001FormClienteCRMVendedor/';
		// Editor
		$form_editor = array(
			'cadastro' => '{"D024_Flag_Revenda_Consumidor":{"top":145,"left":1023.0000610351562,"width":172,"hidden":1},"D024_Nome_Empresa":{"top":7,"left":88,"width":275,"hidden":0},"D024_Flag_Pessoa_Fisica_Juridica":{"top":7,"left":527,"width":128,"hidden":0},"D024_C004_Id":{"top":168,"left":1008.0000610351562,"width":214,"hidden":1},"D024_Nome_Fantasia":{"top":30,"left":74,"width":275,"hidden":0},"D024_Flag_Contribuinte":{"top":30,"left":527,"width":144,"hidden":0},"D024_Inscricao_Estadual":{"top":53,"left":108,"width":175,"hidden":0},"D024_Rg":{"top":53,"left":571.0000610351562,"width":104,"hidden":0},"D024_Cnpj":{"top":76,"left":90,"width":125,"hidden":0},"D024_Cpf":{"top":76,"left":560.0000610351562,"width":104,"hidden":0},"buttonSearchCep1":{"top":97,"left":224,"width":null,"hidden":0},"D024_Cep":{"top":99,"left":103,"width":75,"hidden":0},"D024_Endereco":{"top":122,"left":76,"width":275,"hidden":0},"D024_Numero":{"top":122,"left":574.0000610351562,"width":72,"hidden":0},"D024_Complemento":{"top":145,"left":58,"width":125,"hidden":0},"D024_Bairro":{"top":145,"left":502,"width":152,"hidden":0},"D024_D018_Id":{"top":168,"left":102,"width":63,"hidden":0},"D024_D020_Id":{"top":168,"left":450,"width":208,"hidden":0},"D024_D030_Id":{"top":190.99996948242188,"left":95,"width":321,"hidden":0},"D024_Apelido":{"top":190.99996948242188,"left":582.0000610351562,"width":124,"hidden":1},"D024_DDD_Telefone_1":{"top":213.99996948242188,"left":65,"width":30,"hidden":0},"D024_Telefone_1":{"top":213.99996948242188,"left":176,"width":95,"hidden":0},"D024_DDD_Celular":{"top":213.99996948242188,"left":505,"width":24,"hidden":0},"D024_Celular":{"top":213.99996948242188,"left":592.0000610351562,"width":104,"hidden":0},"D024_DDD_Telefone_2":{"top":236.99996948242188,"left":72,"width":30,"hidden":0},"D024_Telefone_2":{"top":236.99996948242188,"left":176,"width":95,"hidden":0},"D024_DDD_Fax":{"top":236.99996948242188,"left":520,"width":24,"hidden":0},"D024_Fax":{"top":236.99996948242188,"left":592.0000610351562,"width":104,"hidden":0},"D024_Nome_Contato":{"top":259.9999694824219,"left":85,"width":95,"hidden":0},"D024_Web_Site":{"top":259.9999694824219,"left":483,"width":168,"hidden":0},"D024_E_Mail":{"top":282.9999694824219,"left":94,"width":225,"hidden":0},"D024_Flag_Cliente":{"top":282.9999694824219,"left":563.0000610351562,"width":96,"hidden":0},"D024_Flag_Pre_Cadastro":{"top":305.9999694824219,"left":39,"width":64,"hidden":0},"D024_Flag_Fornecedor":{"top":305.9999694824219,"left":539,"width":96,"hidden":0},"D024_Data_Fundacao":{"top":328.9999694824219,"left":15,"width":100,"hidden":0},"D024_D046_Id":{"top":328.9999694824219,"left":492,"width":176,"hidden":0},"D024_Observacoes":{"top":351.9999694824219,"left":62,"height":77,"width":327,"hidden":0},"D024_D039_Id":{"top":351.9999694824219,"left":549.0000610351562,"width":96,"hidden":0},"D024_Data_Cadastro":{"top":374.9999694824219,"left":559.0000610351562,"width":56,"hidden":0}}',
			'cobranca' => '{"buttonSearchCep2":{"top":5,"left":224,"width":null},"D024_Cobranca_Rua":{"top":30,"left":92,"width":232},"D024_Cobranca_Complemento":{"top":53,"left":74,"width":100},"D024_Cobranca_Bairro":{"top":76,"left":109,"width":100},"D024_Cobranca_Cep":{"top":7,"left":119,"width":70},"D024_DDD_Cobranca_Telefone":{"top":99,"left":97,"width":35},"D024_Cobranca_Telefone":{"top":99,"left":192,"width":95},"D024_DDD_Cobranca_Fax":{"top":122,"left":120,"width":35},"D024_Cobranca_Fax":{"top":122,"left":192,"width":95},"D024_Cobranca_Email":{"top":145,"left":114,"width":232},"D024_D018_Cobranca":{"top":168,"left":125,"width":80},"D024_D020_Cobranca":{"top":191,"left":105,"width":232},"D024_D030_Cobranca":{"top":214,"left":118,"width":232},"D024_Praca_Pagamento":{"top":237,"left":39,"width":232},"D024_D062_Id":{"top":260,"left":51,"width":232},"D024_Condicoes_Pagamento":{"top":283,"left":16,"width":232}}',
			'entrega' => '{"buttonSearchCep3":{"top":5,"left":176,"width":null},"D024_Entrega_Rua":{"top":30,"left":44,"width":232},"D024_Entrega_Complemento":{"top":53,"left":26,"width":100},"D024_Entrega_Bairro":{"top":76,"left":61,"width":100},"D024_Entrega_Cep":{"top":7,"left":71,"width":70},"D024_DDD_Entrega_Fax":{"top":99,"left":72,"width":30},"D024_Entrega_Fax":{"top":99,"left":144,"width":75},"D024_Entrega_Email":{"top":122,"left":66,"width":232},"D024_D018_Entrega":{"top":145,"left":77,"width":43},"D024_D020_Entrega":{"top":168,"left":57,"width":232},"D024_D030_Entrega":{"top":191,"left":70,"width":232}}',
			'fiscal' => '{"tituloCompras":{"top":4,"left":144,"width":null,"hidden":1},"D024_Flag_ICMS_Substituicao_Tributaria_Substituto":{"top":30,"left":93,"width":48,"hidden":0},"D024_Flag_Isento_ICMS":{"top":53.00001525878906,"left":16,"width":48,"hidden":0},"tituloVendas":{"top":73.00001525878906,"left":144,"width":null,"hidden":1},"D024_Empresa_Emissora":{"top":99.00001525878906,"left":2,"width":231,"hidden":1},"D024_Observacao_Isento_ICMS":{"top":122.00001525878906,"left":34,"height":45,"width":342,"hidden":0},"D024_D006_Id":{"top":191.00001525878906,"left":74,"width":244,"hidden":0},"D024_Flag_Suframa":{"top":214.00001525878906,"left":12,"width":183,"hidden":0},"D024_Inscricao_Municipal":{"top":237.00001525878906,"left":47,"width":175,"hidden":0},"D024_Numero_Suframa":{"top":237.00001525878906,"left":425.00006103515625,"width":184,"hidden":0},"D024_Percentual_Reducao_ICMS":{"top":260.00001525878906,"left":68,"width":75,"hidden":0},"D024_Aliquota_ICMS":{"top":283.00001525878906,"left":72,"width":75,"hidden":0},"D024_Flag_Isento_ICMS_Venda":{"top":305.98338317871094,"left":28,"width":48,"hidden":0},"D024_D059_Id":{"top":305.98338317871094,"left":208,"width":400,"hidden":0},"D024_Flag_Isento_PIS_COFINS":{"top":328.98338317871094,"left":30,"width":48,"hidden":0},"D024_Situacao_Tributaria_PIS_COFINS":{"top":328.98338317871094,"left":208,"width":400,"hidden":0},"D024_Flag_Isento_IPI":{"top":351.98338317871094,"left":75,"width":48,"hidden":0},"D024_D088_Id":{"top":351.98338317871094,"left":208,"width":400,"hidden":0},"D024_Observacao_Isento_IPI":{"top":374.98338317871094,"left":76,"height":30,"width":392,"hidden":0}}',
			'comercial' => '{"D024_Percentual_Desconto_Tabela":{"top":7,"left":93,"width":100},"D024_Flag_Lista_Preco":{"top":30,"left":125,"width":70},"D024_C007_Vendedor_Interno":{"top":53,"left":103,"width":110},"D024_C007_Vendedor_Externo":{"top":76,"left":99,"width":110},"D024_D021_Id":{"top":99,"left":115,"width":110},"D024_Percentual_Comissao":{"top":122,"left":126,"width":100},"D024_Flag_Cotacao_Compra":{"top":145,"left":10,"width":110},"D024_Flag_Comercial":{"top":168,"left":94,"width":110},"D024_Data_Ativo":{"top":168,"left":328,"width":100},"D024_Flag_Permite_ACP":{"top":191,"left":99,"width":110},"D024_Data_Inativo":{"top":191,"left":322,"width":100},"D024_Flag_Tempo":{"top":214,"left":93,"width":110},"D024_Horario_Entrega":{"top":237,"left":111,"width":100},"D024_Tipo_Pagamento":{"top":260,"left":76,"width":110},"D024_Dias_Carencia_Pagamento":{"top":283,"left":64,"width":100},"D024_Dia_Semana_Pagamento":{"top":306,"left":73,"width":110},"D024_Dia_Mes_Pagamento":{"top":329,"left":77,"width":100},"D024_D022_Id":{"top":351.9999694824219,"left":111,"width":232},"D024_Observacao_Comercial":{"top":374.9999694824219,"left":76,"width":292}}',
			'financeiro' => '{"D024_D027_Id":{"top":260.00001525878906,"left":97,"width":165,"hidden":1},"D024_Flag_Restricao":{"top":7,"left":13,"width":64,"hidden":0},"D024_Flag_Liberado":{"top":30,"left":52.98333740234375,"width":64,"hidden":0},"D024_Flag_Ativo":{"top":53.00001525878906,"left":42.98333740234375,"width":64,"hidden":0},"D024_Observacao_Financeiro":{"top":76.00001525878906,"left":25,"height":42,"width":342,"hidden":0},"D024_Valor_Limite":{"top":145.00001525878906,"left":82.98333740234375,"width":150,"hidden":0},"D024_Flag_Protestar":{"top":168.00001525878906,"left":88,"width":64,"hidden":0},"D024_Prazo_Parcela":{"top":191.00001525878906,"left":96,"width":56,"hidden":1},"D024_Dias_Vencimento":{"top":214.00001525878906,"left":85,"width":56,"hidden":1},"D024_Quantidade_Parcelas":{"top":237.00001525878906,"left":82,"width":116,"hidden":1}}'
		);
		
		// novo formulário
		$form = new Formulario('formcad001FormCadastro_CRM_Vendedor');

		// Definição do Formulário
		$formArray = array(
		'campos' => array(
			'cadastro' => array(
				'D024_Nome_Empresa' => array('name' => 'D024_Nome_Empresa','extra' => "onchange=\"campoNomeFantasia(this, '{$g['divId']}');\"", 'label' => 'Nome', 'type' => 'text', 'length' => array(50,255)),
				'D024_Flag_Pessoa_Fisica_Juridica' => array('name' => 'D024_Flag_Pessoa_Fisica_Juridica', 'label' => 'Pessoa', 'type' => 'select', 'align'=>'right', 'style'=>'float:right', 'select' => 
					array(
						array('title' => '...', 'value' => '', 'selected' => true),
						array('title' => 'Jurídica', 'value' => 'J'),
						array('title' => 'Física', 'value' => 'F'), 
						array('title' => 'Internacional', 'value' => 'I'), 
					)
				),
				'D024_Nome_Fantasia' => 			array('name' => 'D024_Nome_Fantasia', 'label' => 'Fantasia',	'type' => 'text', 'length' => array(50,255)),
				'D024_Flag_Revenda_Consumidor' => 	array('name' => 'D024_Flag_Revenda_Consumidor', 'label' => 'Tipo', 	'type' => 'select', 'align'=>'right', 'style'=>'float:right', 'select' => 
					array(
						array('title' => '...', 'value' => '', 'selected' => true),   
						array('title' => 'Revenda', 'value' => '1'),   
						array('title' => 'Consumidor Atacado', 'value' => '2'),   
						array('title' => 'Consumidor Varejo', 'value' => '3')   
					)
				),   
				'D024_Flag_Contribuinte' => 	array('name' => 'D024_Flag_Contribuinte', 'label' => 'Tipo', 'type' => 'select', 'align'=>'right', 'style'=>'float:right', 'select' => 
					array(
						array('title' => '...', 'value' => '', 'selected' => true),
						array('title' => 'Não contribuinte', 'value' => '0' ),   
						array('title' => 'Consumidor Final', 'value' => '1'),   
						array('title' => 'Revenda', 'value' => '2'),   
						array('title' => 'Produtor rural', 'value' => '3'),
						array('title' => 'Simples', 'value' => '4'),   
						array('title' => 'Industria', 'value' => '5')   
					)
				),
				'D024_Inscricao_Estadual' => 		array('name' => 'D024_Inscricao_Estadual',	'label' => 'Insc. Estadual', 'type' => 'text', 'length' => array(20,255)),
				'D024_Rg' => 	array('name' => 'D024_Rg', 'label' => 'RG', 'type' => 'text', 'length' => array(14,30), 'align' => 'right', 'style' => 'float:right'),
				'D024_Cnpj' => 	array('name' => 'D024_Cnpj', 'label' => 'CNPJ', 'type' => 'cnpj', 'length' => array(20,20)),
				'D024_Cpf' => 	array('name' => 'D024_Cpf', 'label' => 'CPF', 'type' => 'cpf', 'align' => 'right', 'style' => 'float:right', 'length' => array(14,30)),
				'D024_Cep' => 	array('name' => 'D024_Cep',	'label' => 'CEP', 'type' => 'cep', 'length' => array(10,15)),
				'buttonSearchCep1' => 	array('name' => 'buttonSearchCep1',	'label' => '', 'type' => 'button', 'extra' => "style='padding: 0; heigth: 20px; width: 50px;' onclick='pesquisaCep(false,\"{$g['divId']}\", \"{$g['divIdRoot']}\",\"D024_Cep\", {\"EnderecoDb\": \"D024_Endereco\", \"Cep\": \"D024_Cep\", \"BairroDb\": \"D024_Bairro\", \"UfDb\": \"D024_D018_Id\",\"CidadeDb\": \"D024_D020_Id\", \"PaisDb\": \"D024_D030_Id\"})'", 'align'=>'left', 'value'=>'Buscar', 'reject'=>true),
				'D024_Endereco' => 	array('name' => 'D024_Endereco', 'label' => 'Endereço',	'type' => 'text', 'length' => array(50,255)),
				'D024_Numero' => array('name' => 'D024_Numero', 'label' => 'Número', 	'type' => 'text', 'align'=>'right', 'style'=>'float:right; margin-top: 0px', 'length' => array(10,50)),
				'D024_Complemento' => array('name' => 'D024_Complemento', 'label' => 'Complemento',     'type' => 'text', 'length' => array(20,200)),//'align'=>'right', 'style'=>'float:right; margin-top: 0px', 
				'D024_D018_Id' => array('name' => 'D024_D018_Id', 'label' => 'UF',     'type' => 'select','align'=>'left', 'style'=>'float:left', 'extra' => "onchange=\"alteraEstado(this, '{$g['divId']}')\"", 'select' => 
					gGeraSelect('D018', 'D018_Id', 'D018_UF','', true)
				),
				'D024_D020_Id' => array('name' => 'D024_D020_Id', 'label' => 'Cidade', 	'type' => 'select', 'align'=>'right', 'style'=>'float:right', 'select' => 
					gGeraSelect('D020', 'D020_Id', 'D020_Nome_Cidade','', true)
				),
				'D024_D030_Id' => array('name' => 'D024_D030_Id', 'label' => 'País', 'type' => 'select', 'align'=>'left', 'style'=>'float:left',  'value' => 38, 'select' => 
					gGeraSelect('D030', 'D030_Id', 'D030_Nome_Pais,D030_Codigo_Area', '', false)
				),
				'D024_Bairro' => array('name' => 'D024_Bairro', 'label' => 'Bairro', 'type' => 'text', 'length' => array(25,30)),
				'D024_DDD_Telefone_1' => array('name' => 'D024_DDD_Telefone_1',	'label' => 'Telefone 1', 'type' => 'text', 'length' => array(1,4)),
				'D024_Telefone_1' => array('name' => 'D024_Telefone_1',	'label' => '', 'type' => 'text','align'=>'right', 'extra' => '', 'length' => array(14,9)),
				'D024_DDD_Celular' => array('name' => 'D024_DDD_Celular', 'label' => 'Celular', 'type' => 'text', 'align'=>'right', 'length' => array(2,2)),
				'D024_Celular' => array('name' => 'D024_Celular',	'label' => '', 'type' => 'text','align'=>'right','style'=>'float:right', 'length' => array(14,14)),
				'D024_DDD_Telefone_2' => array('name' => 'D024_DDD_Telefone_2',	'label' => 'Telefone 2', 'type' => 'text', 'length' => array(1,2)),
				'D024_Telefone_2' => array('name' => 'D024_Telefone_2', 'label' => '', 'type' => 'text', 'align' => 'right', 'length' => array(14,9)),
				'D024_DDD_Fax' => array('name' => 'D024_DDD_Fax', 'label' => 'Fax', 'type' => 'text','align'=>'right', 'length' => array(2,2)),
				'D024_Fax' => array('name' => 'D024_Fax', 'label' => '', 'type' => 'text','align'=>'right', 'length' => array(14,14)),
				'D024_Nome_Contato' => 	array('name' => 'D024_Nome_Contato','label' => 'Contato', 'type' => 'text','align'=>'left', 'style'=>'float:left', 'length' => array(14,40)),
				'D024_E_Mail' => array('name' => 'D024_E_Mail', 'label' => 'E-mail', 'type' => 'text', 'length' => array(40,60)),
				'D024_Web_Site' => array('name' => 'D024_Web_Site', 'label' => 'Website', 'type' => 'text','align'=>'right', 'style'=>'float:right', 'length' => array(30,40)),
				'D024_Inscricao_Estadual' => array('name' => 'D024_Inscricao_Estadual', 'label' => 'I.E.', 'type' => 'text', 'length' => array(30,30)),
				'D024_Flag_Pre_Cadastro' => array('name' => 'D024_Flag_Pre_Cadastro', 	'label' => 'Pré-Cadastrado', 'type' => 'select', 'select' => 
					array(
						array('title' => '...', 'value' => 'S'),
						array('title' => 'Sim', 'value' => 'S', 'selected' => true), 
						array('title' => 'Não', 'value' => 'N') 
					)
				),
				'D024_Flag_Cliente' => array('name' => 'D024_Flag_Cliente', 'label' => 'Cliente','align'=>'right', 'style'=>'float:right', 'type' => 'select', 'select' => 
					array(
						array('title' => '...', 'value' => '', 'selected' => true),
						array('title' => 'Sim', 'value' => 'S'),
						array('title' => 'Não', 'value' => 'N')
					)
				),
				'D024_Data_Fundacao' => array('name' => 'D024_Data_Fundacao', 'label' => 'Fundação/Nascimento', 'type' => 'data', 'length' => array(15,30)),
				'D024_Flag_Fornecedor' => array('name' => 'D024_Flag_Fornecedor', 'label' => 'Fornecedor','align'=>'right', 'style'=>'float:right', 'type' => 'select', 'select' => 
					array(
						array('title' => '...', 'value' => '', 'selected' => true),
						array('title' => 'Sim', 'value' => 'S'),
						array('title' => 'Não', 'value' => 'N')
					)
				),
				'D024_D046_Id' => array('name' => 'D024_D046_Id', 'label' => 'Grupo', 'type' => 'select', 'select' => 
					gGeraSelect('D046', 'D046_Id', 'D046_Nome','', true)
				),
				'D024_D039_Id' =>  array('name' => 'D024_D039_Id', 'label' => 'Sub-Grupo','align'=>'right', 'style'=>'float:right', 'type' => 'select', 'select' => 
					gGeraSelect('D039', 'D039_Id', 'D039_Descricao','', true)
				),
				'D024_Observacoes' => array('name' => 'D024_Observacoes', 'label' => 'Observação', 	'type' => 'textarea', 'length' => array(4,57)),
				'D024_Data_Cadastro' => array('name' => 'D024_Data_Cadastro', 'label' => 'Data cadastro','value' => date("d/m/Y"), 'align'=>'right', 'type' => 'data', 'length' => array(15,14)),
				'D024_Apelido' => array('name' => 'D024_Apelido', 'label' => 'Apelido', 'type' => 'text'),
				'D024_C004_Id' => array('name' => 'D024_C004_Id', 'label' => 'Empresa', 'type' => 'select','align'=>'left', 'style'=>'float:left', 'select' => 
					gGeraSelect('C004', 'C004_Id', 'C004_Nome_Abreviado','', true)
				),
				'formId' => array('name' => 'formId', 'label' => '', 'type' => 'hidden', 'value' => $formId, 'reject' => true),
			),
		'cobranca' => array(
				'D024_Cobranca_Cep' => array('name' => 'D024_Cobranca_Cep', 'label'=>'CEP', 'type' => 'cep'),
				'buttonSearchCep2' =>     array('name' => 'buttonSearchCep2',    'label' => '', 'type' => 'button', 'extra' => "style='padding: 0; heigth: 20px; width: 50px;' onclick='pesquisaCep(false,\"{$g['divId']}\", \"{$g['divIdRoot']}\",\"D024_Cobranca_Cep\", {\"EnderecoDb\": \"D024_Cobranca_Rua\", \"Cep\": \"D024_Cobranca_Cep\", \"BairroDb\": \"D024_Cobranca_Bairro\", \"UfDb\": \"D024_D018_Cobranca\",\"CidadeDb\": \"D024_D020_Cobranca\", \"PaisDb\": \"D024_D030_Cobranca\"})'", 'align'=>'left', 'value'=>'Buscar', 'reject'=>true),
				'D024_Cobranca_Rua' => array('name' => 'D024_Cobranca_Rua', 'label'=>'Endereço', 'type' => 'text','length' => array(40,40)),
				'D024_Cobranca_Complemento' => array('name' => 'D024_Cobranca_Complemento', 'label'=>'Complemento', 'type' => 'text','length' => array(15,25)),
				'D024_Cobranca_Bairro' => array('name' => 'D024_Cobranca_Bairro', 'label'=>'Bairro', 'type' => 'text','length' => array(15,25)),
				'D024_DDD_Cobranca_Telefone' => array('name' => 'D024_DDD_Cobranca_Telefone', 'label' => 'Telefone', 'type' => 'text','length' => array(2,2)),
				'D024_Cobranca_Telefone' => array('name' => 'D024_Cobranca_Telefone', 'label' => '', 'type' => 'text','length' => array(14,14), 'align'=>'right'),
				'D024_DDD_Cobranca_Fax' => array('name' => 'D024_DDD_Cobranca_Fax', 'label' => 'Fax', 'type' => 'text','length' => array(2,2)),
				'D024_Cobranca_Fax' => array('name' => 'D024_Cobranca_Fax', 'label' => '', 'type' => 'text','length' => array(14,14), 'align'=>'right'),
				'D024_Cobranca_Email' => array('name' => 'D024_Cobranca_Email', 'label' => 'Email', 'type' => 'text','length' => array(40,40)),
				'D024_D018_Cobranca' => array('name' => 'D024_D018_Cobranca', 'label' => 'UF', 'type' => 'select','extra' => "onchange=\"alteraEstadoCobranca(this, '{$g['divId']}')\"", 'select' => 	
					gGeraSelect('D018', 'D018_Id', 'D018_UF', '', true)
				),
				'D024_D020_Cobranca' => array('name' => 'D024_D020_Cobranca', 'label' => 'Cidade','extra' => 'style="width:232px;"', 'type' => 'select', 'select' => 
					gGeraSelect('D020', 'D020_Id', 'D020_Nome_Cidade','', true)
				),
				'D024_D030_Cobranca' => array('name' => 'D024_D030_Cobranca', 'label' => 'País', 'type' => 'select', 'extra' => 'style="width:232px;"', 'select' => 
					gGeraSelect('D030', 'D030_Id', 'D030_Nome_Pais,D030_Codigo_Area', '', false, 30)  
				),
				'D024_Praca_Pagamento' => array('name' => 'D024_Praca_Pagamento', 'label' => 'Praça de 	Pagamento', 'type' => 'text','length' => array(40,40)),
				'D024_D062_Id' => array('name' => 'D024_D062_Id', 'label' => 'Banco Pagamento', 'extra' => 'style="width:232px;"','type' => 'select','select' =>
					gGeraSelect('D062', 'D062_Id', 'D062_Nome_Banco','', true)
				),
				'D024_Condicoes_Pagamento' => array('name' => 'D024_Condicoes_Pagamento', 'label' => 'Condições de Pagamento', 'type' => 'text','length' => array(40,40)),
			),
		'entrega' => array(
				'D024_Entrega_Cep' => array('name' => 'D024_Entrega_Cep', 'label'=>'CEP', 'type' => 'cep'),
				'buttonSearchCep3' =>     array('name' => 'buttonSearchCep3',    'label' => '', 'type' => 'button', 'extra' => "style='padding: 0; heigth: 20px; width: 50px;' onclick='pesquisaCep(false,\"{$g['divId']}\", \"{$g['divIdRoot']}\",\"D024_Entrega_Cep\", {\"EnderecoDb\": \"D024_Entrega_Rua\", \"Cep\": \"D024_Entrega_Cep\", \"BairroDb\": \"D024_Entrega_Bairro\", \"UfDb\": \"D024_D018_Entrega\",\"CidadeDb\": \"D024_D020_Entrega\", \"PaisDb\": \"D024_D030_Entrega\"})'", 'align'=>'left', 'value'=>'Buscar', 'reject'=>true),
				'D024_Entrega_Rua' => array('name' => 'D024_Entrega_Rua', 'label'=>'Endereço', 'type' => 'text','length' => array(40,40)),
				'D024_Entrega_Complemento' => array('name' => 'D024_Entrega_Complemento', 'label'=>'Complemento', 'type' => 'text','length' => array(15,25)),
				'D024_Entrega_Bairro' => array('name' => 'D024_Entrega_Bairro', 'label'=>'Bairro', 'type' => 'text','length' => array(15,25)),
				'D024_Numero_Entrega' => array('name' => 'D024_Entrega_Complemento', 'label'=>'Numero', 'type' => 'text','length' => array(15,25)),
				'D024_DDD_Entrega_Fax' => array('name' => 'D024_DDD_Entrega_Fax', 'label' => 'Fax', 'type' => 'text','length' => array(1,2)),
				'D024_Entrega_Fax' => array('name' => 'D024_Entrega_Fax', 'label' => '', 'type' => 'text','length' => array(10,9), 'align'=>'right'),
				'D024_Entrega_Email' => array('name' => 'D024_Entrega_Email', 'label' => 'Email', 'type' => 'text','length' => array(40,40)),
				'D024_D018_Entrega' => array('name' => 'D024_D018_Entrega', 'label' => 'UF', 'type' => 'select','extra' => "onchange=\"alteraEstadoEntrega(this, '{$g['divId']}')\"", 'select' => 	
					gGeraSelect('D018', 'D018_Id', 'D018_UF','', true)
				),
				'D024_D020_Entrega' => array('name' => 'D024_D020_Entrega', 'label' => 'Cidade', 'type' => 'select','extra' => 'style="width:232px;"', 'select' => 
					gGeraSelect('D020', 'D020_Id', 'D020_Nome_Cidade','', true)
				),
				'D024_D030_Entrega' => array('name' => 'D024_D030_Entrega', 'label' => 'País', 'type' => 'select','extra' => 'style="width:232px;"', 'select' => 
					gGeraSelect('D030', 'D030_Id', 'D030_Nome_Pais,D030_Codigo_Area', '', false, 30)  
				),
			),
		'fiscal' => array(
				'tituloCompras' => array('name' => 'tituloCompras', 'type' => 'titulo', 'label' => 'Compras', 'reject' => true),
				'D024_Flag_ICMS_Substituicao_Tributaria_Substituto' => array('name' => 'D024_Flag_ICMS_Substituicao_Tributaria_Substituto', 'label'=>'ICMS S.T.', 'type' => 'select','select' => 
					array(
						array('title' => '...', 'value' => '', 'selected' => true),
						array('title' => 'Sim', 'value' => 'S'),
						array('title' => 'Não', 'value' => 'N'),
					)),
				'D024_Flag_Isento_ICMS' => array('name' => 'D024_Flag_Isento_ICMS', 'label'=>'Isento de ICMS (Compra) ', 'type' => 'select','select' => 
					array(
						array('title' => '...', 'value' => '', 'selected' => true),
						array('title' => 'Não', 'value' => '2'),
						array('title' => 'Sim', 'value' => '1'),
					)),
				'D024_Observacao_Isento_ICMS' => array('name' => 'D024_Observacao_Isento_ICMS', 'label'=>'Obs. ISENTO de ICMS', 'type' => 'textarea','length' => array(3,60)),
				'tituloVendas' => array('name' => 'tituloVendas', 'type' => 'titulo', 'label' => 'Vendas', 'reject' => true),
				'D024_D006_Id' => array('name' => 'D024_D006_Id', 'label'=>'CFOP Padrão', 'type' => 'select','select' => 
					gGeraSelect('D006', 'D006_Id', 'D006_Codigo_CFOP,D006_Descricao', '', true)
				), 
				'D024_Flag_Suframa' => array('name' => 'D024_Flag_Suframa', 'label'=>'Excessão para o Suframa', 'type' => 'select','select' => 
					array(
						array('title' => 'Não Definida', 'value' => '', 'selected' => true),
						array('title' => 'Sistemas fará validações', 'value' => 'S'),
						array('title' => 'Não tem suframa', 'value' => 'N'),
					)),
				'D024_Numero_Suframa' => array('name' => 'D024_Numero_Suframa', 'label' => 'Número Suframa', 'type' => 'text','length' => array(30,40)),
				'D024_Inscricao_Municipal' => array('name' => 'D024_Inscricao_Municipal', 'label' => 'Inscrição Municipal', 'type' => 'text','length' => array(30,40)),
				'D024_Percentual_Reducao_ICMS' => array('name' => 'D024_Percentual_Reducao_ICMS', 'label' => 'Redução ICMS', 'type' => 'moeda','length' => array(10,10)),
				'D024_Aliquota_ICMS' => array('name' => 'D024_Aliquota_ICMS', 'label' => 'Aliquota ICMS', 'type' => 'moeda','length' => array(10,10)),
				'D024_Flag_Isento_ICMS_Venda' => array('name' => 'D024_Flag_Isento_ICMS_Venda', 'label' => 'Isento de ICMS venda?', 'type' => 'select', 'select' => 
					array(
						array('title' => '...', 'value' => '', 'selected' => true),
						array('title' => 'Não', 'value' => 'N'),
						array('title' => 'Sim', 'value' => 'S'),
					)
				),
				'D024_D059_Id' => array('name' => 'D024_D059_Id', 'label' => 'Situação Tributária', 'type' => 'select', 'select' => 
					gGeraSelect('D059', 'D059_Id', 'D059_Codigo_ST,D059_Descricao_Origem', '', true),'align' => 'right', 'extra' => 'style="width: 400px; float:right;"'
				),
				'D024_Flag_Isento_PIS_COFINS' => array('name' => 'D024_Flag_Isento_PIS_COFINS', 'label' => 'Isento de PIS/COFINS?', 'type' => 'select', 'select' => 
					array(
						array('title' => '...', 'value' => '', 'selected' => true),
						array('title' => 'Não', 'value' => 'N'),
						array('title' => 'Sim', 'value' => 'S'),
					)
				),
				'D024_Situacao_Tributaria_PIS_COFINS' => array('name' => 'D024_Situacao_Tributaria_PIS_COFINS', 'label' => 'Situação Tributária', 'type' => 'select', 'select' => 
					array(
						array('title' => '...', 'value' => '', 'selected' => true),
						array('title' => '01 - Operação Tributável (base de cálculo = valor da operação alíquota normal (cumulativo/não cumulativo))', 'value' => '01'),
						array('title' => '02 - Operação Tributável (base de cálculo = valor da operação (alíquota diferenciada))', 'value' => '02'),
						array('title' => '03 - Operação Tributável (base de cálculo = quantidade vendida x alíquota por unidade de produto)', 'value' => '03'),
						array('title' => '04 - Operação Tributável (tributário monofísica (alíquota zero))', 'value' => '04'),
						array('title' => '06 - Operação Tributável (alíquota zero)', 'value' => '06'),
						array('title' => '07 - Operação Isenta da Contribuição', 'value' => '07'),
						array('title' => '08 - Operação Sem Incidência da Contribuição', 'value' => '08'),
						array('title' => '09 - Operação com Suspensão da Contribuição', 'value' => '09'),
						array('title' => '99 - Outras Operações', 'value' => '99'),
					),'align' => 'right', 'extra' => 'style="width: 400px; float:right;"'
				),
				'D024_Flag_Isento_IPI' => array('name' => 'D024_Flag_Isento_IPI', 'label' => 'Isento de IPI?', 'type' => 'select', 'select' => 
					array(
						array('title' => '...', 'value' => '', 'selected' => true),
						array('title' => 'Não', 'value' => '2'),
						array('title' => 'Sim', 'value' => '1'),
					)
				),
				'D024_D088_Id' => array('name' => 'D024_D088_Id', 'label' => 'Situação Tributária', 'type' => 'select', 'select' => 
					gGeraSelect('D088', 'D088_Id', 'D088_Codigo_ST,D088_Descricao', '', true),'align' => 'right', 'extra' => 'style="width: 400px; float:right;"'
				),			
				'D024_Observacao_Isento_IPI' => array('name' => 'D024_Observacao_Isento_IPI', 'label' => 'Obs. ISENTO', 'type' => 'textarea', 'length' => array(2,70)),
				'D024_Empresa_Emissora' => array('name' => 'D024_Empresa_Emissora','label'=>'Cliente para emissão de NFe', 'type' => 'pesquisa', 'length' => array(50,30),
					'pesquisa' => array('D024','D024_Id','D024_Id,D024_Nome_Fantasia,D020_Nome_Cidade,D018_UF,D024_Cpf,D024_Cnpj','left join D020 on D020_Id=D024_D020_Id left join D018 on D018_Id=D024_D018_Id WHERE D024_Flag_Cliente="S" and D024_Flag_Ativo="S" order by D024_Nome_Empresa','(%1) %2 [%3 - %4]'),
				),
			),
		'comercial' => array(
					'D024_Flag_Lista_Preco' => 	array('name' => 'D024_Flag_Lista_Preco', 'label' => 'Lista Preço', 'type' => 'select', 'select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Preço 1', 'value' => '1'),   
							array('title' => 'Preço 2', 'value' => '2'),   
							array('title' => 'Preço 3', 'value' => '3'),   
						)
					),
					'D024_C007_Vendedor_Interno' => array('name' => 'D024_C007_Vendedor_Interno', 'label' => 'Vendedor Interno', 'type' => 'select', 'extra' => 'style="width:110px;"', 'select' => 
						gGeraSelect("C007", "C007_Id", "C007_Primeiro_Nome", "where (C007_Flag_Vendedor='V' or C007_Flag_Vendedor='S' or C007_Flag_Vendedor='D') and C007_Ativo='S' order by C007_Primeiro_Nome", true)
					),
					'D024_C007_Vendedor_Externo' => array('name' => 'D024_C007_Vendedor_Externo', 'label' => 'Vendedor Externo', 'type' => 'select', 'extra' => 'style="width:110px;"', 'select' => 
						gGeraSelect("C007", "C007_Id", "C007_Primeiro_Nome", "where (C007_Flag_Vendedor='V' or C007_Flag_Vendedor='S' or C007_Flag_Vendedor='D') and C007_Ativo='S' order by C007_Primeiro_Nome", true)
					),
					'D024_D021_Id' => array('name' => 'D024_D021_Id', 'label' => 'Representante', 'type' => 'select', 'extra' => 'style="width:110px;"', 'select' => 
						gGeraSelect("D021", "D021_Id", "D021_Nome_Empresa", "where D021_Data_Irregular='0000-00-00' or  D021_Data_Irregular is null order by D021_Nome_Empresa", true)
					),
					'D024_Percentual_Comissao' => array('name' => 'D024_Percentual_Comissao', 'label'=>'% Comissão', 'type' => 'moeda', 'value' => '0,00','length' => array(15,30)),
					'D024_Flag_Cotacao_Compra' => array('name' => 'D024_Flag_Cotacao_Compra', 'label'=>'Considerar para Cotação de Compra', 'type' => 'select', 'extra' => 'style="width:110px;"', 'select' => 
					array (
						array('title' => '...', 'value' => '', 'selected' => true),
						array('title' => 'Não', 'value' => 'N'),
						array('title' => 'Sim', 'value' => 'S'),
					)),
					'D024_Flag_Comercial' => array('name' => 'D024_Flag_Comercial', 'label'=>'Situação Comercial', 'type' => 'select', 'extra' => 'style="width:110px;"', 'select' => 
					array (
						array('title' => '...', 'value' => '', 'selected' => true),
						array('title' => 'Ativo', 'value' => 'A'),
						array('title' => 'Inativo', 'value' => 'I'),
					)),
					'D024_Data_Ativo' => array('name' => 'D024_Data_Ativo', 'label'=>'Data Ativo', 'type' => 'data','align'=>'right', 'style'=>'float:right', 'length' => array(15,14)),
					'D024_Flag_Permite_ACP' => array('name' => 'D024_Flag_Permite_ACP', 'label'=>'Permite Emitir ACP', 'type' => 'select', 'extra' => 'style="width:110px;"', 'select' => 
					array (
						array('title' => '...', 'value' => '', 'selected' => true),
						array('title' => 'Não', 'value' => 'N'),
						array('title' => 'Sim', 'value' => 'S'),
					)),
					'D024_Data_Inativo' => array('name' => 'D024_Data_Inativo', 'label'=>'Data Inativo', 'type' => 'data','align'=>'right', 'style'=>'float:right', 'length' => array(15,14)),
					'D024_Flag_Tempo' => array('name' => 'D024_Flag_Tempo', 'label'=>'Estado Negociação', 'type' => 'select', 'extra' => 'style="width:110px;"', 'select' => 
					array (
						array('title' => '...', 'value' => '', 'selected' => true),
						array('title' => 'Bom', 'value' => 'BM'),
						array('title' => 'Regular', 'value' => 'RE'),
						array('title' => 'Ruim', 'value' => 'RM'),
					)),
					'D024_Horario_Entrega' => array('name' => 'D024_Horario_Entrega', 'label'=>'Horário Entrega', 'type' => 'tempo','length' => array(15,30)),
					'D024_Tipo_Pagamento' => array('name' => 'D024_Tipo_Pagamento', 'label'=>'Frequência Pagamento', 'type' => 'select', 'extra' => 'style="width:110px;"', 'select' => 
					array (
						array('title' => '...', 'value' => '', 'selected' => true),
						array('title' => 'Diário', 'value' => 'D'),
						array('title' => 'Semanal', 'value' => 'S'),
						array('title' => 'Quinzenal', 'value' => 'Q'),
						array('title' => 'Mensal', 'value' => 'M'),
					)),
					'D024_Dias_Carencia_Pagamento' => array('name' => 'D024_Dias_Carencia_Pagamento', 'label'=>'Dias carência pagamento', 'type' => 'text','length' => array(15,30)),
		
					'D024_Dia_Semana_Pagamento' => array('name' => 'D024_Dia_Semana_Pagamento', 'label'=>'Dia Semana Pagamento', 'type' => 'select', 'extra' => 'style="width:110px;"', 'select' => 
					array (
						array('title' => '...', 'value' => '', 'selected' => true),
						array('title' => 'Domingo', 'value' => '1'),
						array('title' => 'Segunda', 'value' => '2'),
						array('title' => 'Terça', 'value' => '3'),
						array('title' => 'Quarta', 'value' => '4'),
						array('title' => 'Quinta', 'value' => '5'),
						array('title' => 'Sexta', 'value' => '6'),
						array('title' => 'Sábado', 'value' => '7'),
					)),
					'D024_Dia_Mes_Pagamento' => array('name' => 'D024_Dia_Mes_Pagamento', 'label'=>'Dia do mês pagamento', 'type' => 'data','length' => array(15,30)),
					'D024_Observacao_Comercial' => array('name' => 'D024_Observacao_Comercial', 'label'=>'Observação Comercial', 'type' => 'textarea','length' => array(3,50)),
					'D024_D022_Id' => array('name' => 'D024_D062_Id', 'label' => 'Transportadora', 'type' => 'select','extra' => 'style="width:232px;"','select' =>
						gGeraSelect('D022', 'D022_Id', 'D022_Nome_Empresa','', true)
					),
				),
			'financeiro' => array(
				'D024_Flag_Restricao' => array('name' => 'D024_Flag_Restricao', 'label'=>'Cliente possui restrições?', 'type' => 'select','select' => 
					array (
						array('title' => 'Não', 'value' => 'N', 'selected' => true),
						array('title' => 'Sim', 'value' => 'S'),
					)),
				'D024_Flag_Liberado' => array('name' => 'D024_Flag_Liberado', 'label'=>'Crédito Liberado?', 'type' => 'select','select' => 
					array (
						array('title' => 'Não', 'value' => 'N', 'selected' => true),
						array('title' => 'Sim', 'value' => 'S'),
					)),
				'D024_Observacao_Financeiro' => array('name' => 'D024_Observacao_Financeiro', 'label'=>'Observação Financeiro', 'type' => 'textarea','length' => array(3,60)),
				'D024_Flag_Ativo' => array('name' => 'D024_Flag_Ativo', 'label'=>'Cliente está ativo?', 'type' => 'select','select' => 
					array (
						array('title' => 'Sim', 'value' => 'S', 'selected' => true),
						array('title' => 'Não', 'value' => 'N'),
					)),
				'D024_Valor_Limite' => array('name' => 'D024_Valor_Limite', 'label'=>'Valor Limite', 'type' => 'moeda','length' => array(25,25)),
				'D024_Flag_Protestar' => array('name' => 'D024_Flag_Protestar', 'label'=>'Protestar?', 'type' => 'select','select' => 
					array (
						array('title' => 'Sim', 'value' => 'S', 'selected' => true),
						array('title' => 'Não', 'value' => 'N'),
					)),
				'D024_Prazo_Parcela' => array('name' => 'D024_Prazo_Parcela', 'label' => '1° Venc.', 'type' => 'text'),
				'D024_Dias_Vencimento' => array('name' => 'D024_Dias_Vencimento', 'label' => 'Dias Venc.', 'type' => 'text'),
				'D024_Quantidade_Parcelas' => array('name' => 'D024_Quantidade_Parcelas', 'label' => 'N° Parcelas', 'type' => 'text'),
				'D024_D027_Id' => array('name' => 'D024_D027_Id','label'=>'Portador', 'extra'=>'style="width:165px;"', 'type' => 'select', 'select' =>
					gGeraSelect('D027', 'D027_Id', 'D027_Portador','ORDER BY D027_Portador')
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
						$form->campoSetarValor('D024_Cep', str_replace('-', '', $form->campoValorEnviado('D024_Cep')), true);
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
							// O FW atualiza a linha do grid automaticamente, somente recarregar se não for grid
                            if ($('#{$g['divIdParent']}').find('table.grid').length === 0) {
                                divRefresh('{$g['divIdParent']}', true);
                            }
						} </script></div>
EOT;
						}
				} else {
					// Insert
					$form->campoSetarValor('D024_Cep', str_replace('-', '', $form->campoValorEnviado('D024_Cep')), true);
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						// Imprime uma mensagem e fecha a janela
						$acaoId = $g['mysqlLastId'];
						//die($AbrirEdicaoClienteAposInsercao);
						$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
						echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
								setTimeout(function() {
									if({$g['C031']['AbrirEdicaoClienteAposInsercao']} == 'S'){
										abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}', unique(), '', 'Editar Cadastro - ID: ' + '{$acaoId}', '/cad/cad001/content/cad001contentCadastro/', '&acaoId=' + encodeURIComponent('{$acaoId}'), '[800,700]');
									}
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

	case 'cad001FormJuntar':
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
		$form_tabela = 'D024';
		$form_campo_pk = 'D024_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad001FormBtnJuntar';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad001/form/cad001FormJuntar/';
		
		// form editor
		$form_editor = array(
			'geral' => '{"D024_Id_Antigo":{"top":7,"left":8,"width":248,"hidden":0},"D024_Id_Novo":{"top":30,"left":14,"width":248,"hidden":0},"TextoAviso":{"top":100,"left":12,"width":null,"hidden":0}}',
		);
		
		// Array de definição do formulário
		$pesquisaGridAntigo = array(
			'D024',																											// tabela
			'D024_Id',																										// chave primaria
			'D024_Id,D024_Nome_Empresa,D024_Nome_Fantasia,D020_Nome_Cidade,D018_UF,D024_Cnpj,D024_DDD_Telefone_1,D024_Telefone_1',	// campos
			'left join D020 on D020_Id=D024_D020_Id left join D018 on D018_Id=D024_D018_Id order by D024_Nome_Empresa', 	// extras sql
			'Pesquisa de Cliente',																							// título da janela
			'Código,Cliente,Fantasia,Cidade,UF,CNPJ,DDD,Telefone',															// descrição dos campos
			'cad001JuntarProcuraClientes',																					// callback js
			$g['divId'],																									// divId
			'Antigo',																										// parametro extra
			array(																											// parametros para cada campo do grid
				array('colunaAlinhar' => 'center'),
				array('style' => 'font-weight:bold'),
				array('colunaExibir'=>false),
				array(),
				array(),
				array(),
				array(),
				array()
			)
		);
		$pesquisaGridNovo = array(
			'D024',																											// tabela
			'D024_Id',																										// chave primaria
			'D024_Id,D024_Nome_Empresa,D024_Nome_Fantasia,D020_Nome_Cidade,D018_UF,D024_Cnpj,D024_DDD_Telefone_1,D024_Telefone_1',	// campos
			'left join D020 on D020_Id=D024_D020_Id left join D018 on D018_Id=D024_D018_Id order by D024_Nome_Empresa', 	// extras sql
			'Pesquisa de Cliente',																							// título da janela
			'Código,Cliente,Fantasia,Cidade,UF,CNPJ,DDD,Telefone',															// descrição dos campos
			'cad001JuntarProcuraClientes',																					// callback js
			$g['divId'],																									// divId
			'Novo',																											// parametro extra
			array(																											// parametros para cada campo do grid
				array('colunaAlinhar' => 'center'),
				array('style' => 'font-weight:bold'),
				array('colunaExibir'=>false),
				array(),
				array(),
				array(),
				array(),
				array()
			)
		);
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('formJuntarCad001Form');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D024_Id_Antigo' => array('name' => 'D024_Id_Antigo', 'label' => 'Cliente Antigo', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGridAntigo, 'reject' => true),
					'D024_Id_Novo' => array('name' => 'D024_Id_Novo', 'label' => 'Cliente Novo', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGridNovo, 'reject' => true),
					'TextoAviso' => array('name' => 'TextoAviso', 'label'=>'', 'type' => 'comentario', 'value' => '* Este processo irá juntar o cliente <b>Antigo</b> com o <b>Novo</b>.', 'reject' => true),
				),
			) // 'campos'
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
				mysql_query("set autocommit=0");
				mysql_query("start transaction");
				$retorno = gJuntarCodigos('D024', $form->campoValorEnviado('D024_Id_Antigo'), $form->campoValorEnviado('D024_Id_Novo'), false, array('T241','D024A'));
				if ($retorno === true) {
					mysql_query("COMMIT");
					mysql_query("set autocommit=1");
					$alert .= gGeraAlertMsg('OK', 'Os clientes foram juntados com sucesso!', false);
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
				} else {
					mysql_query("ROLLBACK");
					mysql_query("set autocommit=1");
					if (strpos($retorno, 'BLOQUEADO') !== false) {
						$retorno = explode('-', $retorno);
						$retorno = "O Cliente Antigo possui registros em '{$retorno[1]}' e não pode ser juntado.";
					}
					echo gGeraAlertMsg('Ocorreu um Erro', $retorno);
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
		echo  $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		echo  $g['smarty']->fetch('formEditor.tpl');
		
		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="cad001FormBtnJuntar">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'cad001OrcamentoIncluirCliente':
		global $g;
		//Caso o cadastro seja a partir de um Email spam, preenchemos algumas informações
		if($r_T139_Id > 0){
			$row = mysql_fetch_assoc(mysql_query("SELECT T139_De FROM T139 WHERE T139_Id = '{$r_T139_Id}'"));
			$email = $row['T139_De'];
			if (strpos($email, '<') !== false) {
				$email = explode('<', $email);
				$nome = $email[0];
				$email = explode('>', $email[1]);
				$email = $email[0];
			}
			$email = mysql_real_escape_string($email);
			$nome = mysql_real_escape_string(str_replace('"', '', $nome));
			$D024_Nome_Contato = trim($nome);
			$D024_E_Mail = trim($email);
		}
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
		$form_tabela = 'D024';
		$form_campo_pk = 'D024_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = array('cad001OrcamentoIncluirClienteBtnAdd', 'cad001OrcamentoIncluirClienteBtnCln', 'cad001OrcamentoIncluirClienteBtnNew');
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad001/form/cad001OrcamentoIncluirCliente/';
		$form_editor = array(
			'geral' => '{"D024_Nome_Empresa":{"top":6.969970703125,"left":53.553955078125,"width":408.039216,"hidden":0},"D024_Nome_Contato":{"top":29.978546142578125,"left":51.76165771484375,"width":408.039216,"hidden":0},"D024_E_Mail":{"top":52.971832275390625,"left":18.8419189453125,"width":408.039216,"hidden":0},"D024_DDD_Telefone_1":{"top":75.98040771484375,"left":-0.52081298828125,"width":38.039215999999996,"hidden":0},"D024_Telefone_1":{"top":75.98040771484375,"left":143.9951171875,"width":146.039216,"hidden":0},"D024_D018_Id":{"top":98.97366333007812,"left":69.9295654296875,"width":51,"hidden":0},"D024_D020_Id":{"top":98.97366333007812,"left":184.558837890625,"width":287,"hidden":0},"D024_D030_Id":{"top":121.98223876953125,"left":69.347412109375,"width":415,"hidden":0},"D024_Flag_Pessoa_Fisica_Juridica":{"top":144.97549438476562,"left":46.8443603515625,"width":175,"hidden":0},"D024_Cnpj":{"top":144.99081420898438,"left":328.2169189453125,"width":136.039216,"hidden":0},"D024_Cpf":{"top":0,"left":0,"width":136,"hidden":0},"buttonSearchCnpj":{"top":142.9993896484375,"left":511.99444580078125,"width":null,"hidden":0},"D024_C007_Vendedor_Interno":{"top":167.9993896484375,"left":6.44915771484375,"width":175,"hidden":0},"D024_Flag_Contribuinte":{"top":167.9993896484375,"left":333.6856689453125,"width":143,"hidden":0},"D024_C007_Vendedor_Externo":{"top":190.99264526367188,"left":3.79901123046875,"width":110,"hidden":0},"D024_Observacoes":{"top":213.97061157226562,"left":26.79229736328125,"height":62.039216,"width":408.039216,"hidden":0},"cad001OrcamentoIncluirClienteBtnAdd":{"top":303.9981384277344,"left":383.97674560546875,"width":null,"hidden":0},"cad001OrcamentoIncluirClienteBtnCln":{"top":303.9981384277344,"left":447.97796630859375,"width":null,"hidden":0}}',
		);

		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('form_cad001OrcamentoIncluirCliente');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D024_Nome_Empresa' => array('name' => 'D024_Nome_Empresa', 'label'=>'Nome', 'type' => 'text'),
					'D024_Nome_Contato' => array('name' => 'D024_Nome_Contato', 'label'=>'Contato', 'type' => 'text', 'value' => @$D024_Nome_Contato),
					'D024_Cnpj' => 	array('name' => 'D024_Cnpj', 'label' => 'CNPJ', 'type' => 'cnpj', 'length' => array(20,20)),
					'D024_Cpf' => 	array('name' => 'D024_Cpf', 'label' => 'CPF', 'type' => 'cpf', 'align' => 'right', 'style' => 'float:right', 'length' => array(14,30)),
					'buttonSearchCnpj' => 	array('name' => 'buttonSearchCnpj', 'label' => '', 'type' => 'button', 'extra' => "style='padding: 0; heigth: 20px; width: 50px;' onclick='pesquisaCNPJClientes(\"{$g['divId']}\", \"{$g['divIdRoot']}\",\"D024_Cnpj\")'", 'align'=>'left', 'value'=>'Buscar', 'reject'=>true, 'reload' => false),
					'D024_E_Mail' => array('name' => 'D024_E_Mail', 'label'=>'Contato E-Mail', 'type' => 'text', 'value' => @$D024_E_Mail),
					'D024_DDD_Telefone_1' => array('name' => 'D024_DDD_Telefone_1', 'label'=>'Contato Telefone', 'type' => 'text','length' => array(2,2)),
					'D024_Telefone_1' => array('name' => 'D024_Telefone_1', 'label'=>'', 'type' => 'text'),
					'D024_D018_Id' => array('name' => 'D024_D018_Id', 'label' => 'UF',     'type' => 'select','align'=>'left', 'style'=>'float:left', 'extra' => "onchange=\"alteraEstado(this, '{$g['divId']}')\"", 'select' => 
						gGeraSelect('D018', 'D018_Id', 'D018_UF','', true)
					),
					'D024_D020_Id' => array('name' => 'D024_D020_Id', 'label' => 'Cidade', 	'type' => 'select', 'align'=>'right', 'style'=>'float:right', 'restricao' => false, 'select' => 
						gGeraSelect('D020', 'D020_Id', 'D020_Nome_Cidade','', true)
					),
					'D024_D030_Id' => array('name' => 'D024_D030_Id', 'label' => 'País', 'type' => 'select', 'align'=>'left', 'style'=>'float:left', 'restricao' => false,  'value' => 30, 'select' => 
						gGeraSelect('D030', 'D030_Id', 'D030_Nome_Pais,D030_Codigo_Area', 'order by D030_Nome_Pais', false)
					),
					'D024_Flag_Pessoa_Fisica_Juridica' => array('name' => 'D024_Flag_Pessoa_Fisica_Juridica', 'label' => 'Pessoa', 'type' => 'select', 'align'=>'right', 'style'=>'float:right', 'select' => 
						array(
							array('title' => '...', 'value' => ''),
							array('title' => 'Jurídica', 'value' => 'J', 'selected' => true),
							array('title' => 'Física', 'value' => 'F'), 
							array('title' => 'Internacional', 'value' => 'I'),
						)
					),
					'D024_Flag_Contribuinte' => 	array('name' => 'D024_Flag_Contribuinte', 'label' => 'Tipo', 'type' => 'select', 'align'=>'right', 'style'=>'float:right', 'select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Não contribuinte', 'value' => '0' ),
							array('title' => 'Consumidor Final', 'value' => '1'),
							array('title' => 'Revenda', 'value' => '2'),
							array('title' => 'Produtor rural', 'value' => '3'),
							array('title' => 'Simples', 'value' => '4'),
							array('title' => 'Industria', 'value' => '5')
						)
					),
					// valores padrão
					'D024_Flag_Lista_Preco' => 	array('name' => 'D024_Flag_Lista_Preco', 'label' => '', 'type' => 'hidden', 'value' => 1),
					'D024_Flag_Cliente' => array('name' => 'D024_Flag_Cliente', 'label' => '', 'type' => 'hidden', 'value' => 'S'),
					'D024_Flag_Fornecedor' => array('name' => 'D024_Flag_Fornecedor', 'label' => '', 'type' => 'hidden', 'value' => 'N'),
					'D024_Flag_Ativo' => array('name' => 'D024_Flag_Ativo', 'label' => '', 'type' => 'hidden', 'value' => 'S'),
					'D024_Flag_Pre_Cadastro' => array('name' => 'D024_Flag_Pre_Cadastro', 'label' => '', 'type' => 'hidden', 'value' => 'S'),
					'D024_Data_Cadastro' => array('name' => 'D024_Data_Cadastro', 'label' => '', 'type' => 'hidden', 'value' => date('Y-m-d')),
					'D024_C007_Vendedor_Interno' => array('name' => 'D024_C007_Vendedor_Interno', 'label' => 'Vendedor Interno', 'type' => 'select', 'select' => 
						gGeraSelect("C007", "C007_Id", "C007_Primeiro_Nome", "where (C007_Flag_Vendedor='V' or C007_Flag_Vendedor='S' or C007_Flag_Vendedor='D') and C007_Ativo='S' order by C007_Primeiro_Nome", true, $g['usuarioAtual'])
					),
					'D024_C007_Vendedor_Externo' => array('name' => 'D024_C007_Vendedor_Externo', 'label' => 'Vendedor Externo', 'type' => 'select', 'extra' => 'style="width:110px;"', 'select' =>
						gGeraSelect("C007", "C007_Id", "C007_Primeiro_Nome", "where (C007_Flag_Vendedor='V' or C007_Flag_Vendedor='S' or C007_Flag_Vendedor='D') and C007_Ativo='S' order by C007_Primeiro_Nome", true)
					),
					'D024_Flag_Comercial' => 	array('name' => 'D024_Flag_Comercial', 'label' => '', 'type' => 'hidden', 'value' => 'A'),
					'D024_Flag_Liberado' => 	array('name' => 'D024_Flag_Liberado', 'label' => '', 'type' => 'hidden', 'value' => 'N'),
					'D024_Observacoes' => 	array('name' => 'D024_Observacoes', 'label' => 'Observações', 'type' => 'textarea', 'value' => ''),
				),
			) // 'campos'
		);

		// Botão Limpar / Novo Registro
		if (!empty($acaoId)) {
			$formArray['campos']['geral']['cad001OrcamentoIncluirClienteBtnNew'] = array('name' => 'cad001OrcamentoIncluirClienteBtnNew', 'type'=>'submit', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		}
		$formArray['campos']['geral']['cad001OrcamentoIncluirClienteBtnCln'] = array('name' => 'cad001OrcamentoIncluirClienteBtnCln', 'type'=>'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['geral']['cad001OrcamentoIncluirClienteBtnAdd'] = array('name' => 'cad001OrcamentoIncluirClienteBtnAdd', 'type'=>'button', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$form->definirFormularioArray($formArray);
		
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} #D024_Flag_Pessoa_Fisica_Juridica').change(function() {
					if($(this).val() == 'F') {
						$('#{$g['divId']} #D024_Cnpj').closest('.formEditor').hide();
						$('#{$g['divId']} #buttonSearchCnpj').closest('.formEditor').hide();
						$('#{$g['divId']} #D024_Cpf').closest('.formEditor').show();
					} else if($(this).val() == 'J') {
						$('#{$g['divId']} #D024_Cpf').closest('.formEditor').hide();
						$('#{$g['divId']} #D024_Cnpj').closest('.formEditor').show();
						$('#{$g['divId']} #buttonSearchCnpj').closest('.formEditor').show();
					} else {
						$('#{$g['divId']} #D024_Cnpj').closest('.formEditor').hide();
						$('#{$g['divId']} #buttonSearchCnpj').closest('.formEditor').hide();
						$('#{$g['divId']} #D024_Cpf').closest('.formEditor').hide();
					}
				});
				$('#{$g['divId']} #D024_Flag_Pessoa_Fisica_Juridica').trigger('change');
			} </script></div>
EOT;
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
					// Insert ou Update?
					if (!empty($acaoId)) {
						// Update
						if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
							// Imprime uma mensagem e fecha a janela
							echo  <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							divRefresh('{$form->campoValorEnviado('divReload')}', true);
							divRefresh('{$g['divId']}', true);
						} </script></div>
EOT;
						}
				} else {
					// Insert
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						$D013_D024_Id = $g['mysqlLastId'];
						$D013_Nome_Contato = mysql_real_escape_string(strtoupper($form->campoValorEnviado('D024_Nome_Contato')));
						$D024_Nome_Empresa = mysql_real_escape_string(strtoupper($form->campoValorEnviado('D024_Nome_Empresa')));
						$D013_E_Mail = mysql_real_escape_string(strtoupper($form->campoValorEnviado('D024_E_Mail')));
						$D013_DDD_Telefone_1 = mysql_real_escape_string(strtoupper($form->campoValorEnviado('D024_DDD_Telefone_1')));
						$D013_Telefone_1 = mysql_real_escape_string(strtoupper($form->campoValorEnviado('D024_Telefone_1')));
						mysql_query("INSERT INTO D013 (D013_D024_Id, D013_Nome_Contato, D013_E_Mail, D013_DDD_Telefone_1, D013_Telefone_1) VALUES ('{$D013_D024_Id}', '{$D013_Nome_Contato}', '{$D013_E_Mail}', '{$D013_DDD_Telefone_1}', '{$D013_Telefone_1}')");
						$D013_Id = $g['mysqlLastId'];
						// Imprime uma mensagem e fecha a janela
						echo  <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$('#{$g['divIdParent']} .itemForm-T003_D024_Id').val('$D013_D024_Id');
								$('#{$g['divIdParent']} .itemForm-D085_D024_Id').val('$D013_D024_Id');
								// enviarFormAutomatico é utilizado dentro do carregaCFOPContato.js para eviar o form depois de carregar os dados
								// .data('enviarFormAutomatico', 1)
								$('#{$g['divIdParent']} .itemForm-T003_D024_Id_Pesquisa').val('$D024_Nome_Empresa').data('definirContato', '{$D013_Id}').trigger('change');
								$('#{$g['divIdParent']} .itemForm-D085_D024_Id_Pesquisa').val('$D024_Nome_Empresa').data('definirContato', '{$D013_Id}').trigger('change');
								fecharJanela('{$g['divId']}');
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
	break;

	case 'cad001PedidoIncluirCliente':
		global $g;
		//Caso o cadastro seja a partir de um Email spam, preenchemos algumas informações
		if($r_T139_Id > 0){
			$row = mysql_fetch_assoc(mysql_query("SELECT T139_De FROM T139 WHERE T139_Id = '{$r_T139_Id}'"));
			$email = $row['T139_De'];
			if (strpos($email, '<') !== false) {
				$email = explode('<', $email);
				$nome = $email[0];
				$email = explode('>', $email[1]);
				$email = $email[0];
			}
			$email = mysql_real_escape_string($email);
			$nome = mysql_real_escape_string(str_replace('"', '', $nome));
			$D024_Nome_Contato = trim($nome);
			$D024_E_Mail = trim($email);
		}
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
		$form_tabela = 'D024';
		$form_campo_pk = 'D024_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = array('cad001PedidoIncluirClienteBtnAdd', 'cad001PedidoIncluirClienteBtnCln', 'cad001PedidoIncluirClienteBtnNew');
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad001/form/cad001PedidoIncluirCliente/';
		$form_editor = array(
			'geral' => '{"D024_Nome_Empresa":{"top":6.92401123046875,"left":53.553955078125,"width":408.039216,"hidden":0},"D024_Nome_Contato":{"top":29.9326171875,"left":51.76165771484375,"width":408.039216,"hidden":0},"D024_E_Mail":{"top":52.925872802734375,"left":18.8419189453125,"width":408.039216,"hidden":0},"D024_DDD_Telefone_1":{"top":75.9344482421875,"left":-0.52081298828125,"width":38.039215999999996,"hidden":0},"D024_Telefone_1":{"top":75.9344482421875,"left":143.933837890625,"width":146.039216,"hidden":0},"D024_D018_Id":{"top":98.91238403320312,"left":69.9295654296875,"width":51,"hidden":0},"D024_D020_Id":{"top":98.91238403320312,"left":184.558837890625,"width":287,"hidden":0},"D024_D030_Id":{"top":121.92095947265625,"left":69.347412109375,"width":415,"hidden":0},"D024_Flag_Pessoa_Fisica_Juridica":{"top":144.94485473632812,"left":46.8443603515625,"width":144,"hidden":0},"D024_Cpf":{"top":0,"left":0,"width":120.039,"hidden":0},"D024_Cnpj":{"top":144.97549438476562,"left":344.2095947265625,"width":120.03921600000001,"hidden":0},"buttonSearchCnpj":{"top":142.9993896484375,"left":511.99444580078125,"width":null,"hidden":0},"D024_C007_Vendedor_Interno":{"top":167.9993896484375,"left":6.44915771484375,"width":143,"hidden":0},"D024_Flag_Contribuinte":{"top":167.9993896484375,"left":349.6783447265625,"width":127,"hidden":0},"D024_Observacoes":{"top":190.93136596679688,"left":26.79229736328125,"height":62.039216,"width":408.039216,"hidden":0},"cad001PedidoIncluirClienteBtnAdd":{"top":280.9283142089844,"left":383.97674560546875,"width":null,"hidden":0},"cad001PedidoIncluirClienteBtnCln":{"top":280.9283142089844,"left":447.97796630859375,"width":null,"hidden":0}}',
		);
	
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('form_cad001OrcamentoIncluirCliente');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D024_Nome_Empresa' => array('name' => 'D024_Nome_Empresa', 'label'=>'Nome', 'type' => 'text'),
					'D024_Nome_Contato' => array('name' => 'D024_Nome_Contato', 'label'=>'Contato', 'type' => 'text', 'value' => @$D024_Nome_Contato),
					'D024_Cnpj' => 	array('name' => 'D024_Cnpj', 'label' => 'CNPJ', 'type' => 'cnpj', 'length' => array(20,20)),
					'D024_Cpf' => 	array('name' => 'D024_Cpf', 'label' => 'CPF', 'type' => 'cpf', 'align' => 'right', 'style' => 'float:right', 'length' => array(14,30)),
					'buttonSearchCnpj' => 	array('name' => 'buttonSearchCnpj', 'label' => '', 'type' => 'button', 'extra' => "style='padding: 0; heigth: 20px; width: 50px;' onclick='pesquisaCNPJClientes(\"{$g['divId']}\", \"{$g['divIdRoot']}\",\"D024_Cnpj\")'", 'align'=>'left', 'value'=>'Buscar', 'reject'=>true, 'reload' => false),
					'D024_E_Mail' => array('name' => 'D024_E_Mail', 'label'=>'Contato E-Mail', 'type' => 'text', 'value' => @$D024_E_Mail),
					'D024_DDD_Telefone_1' => array('name' => 'D024_DDD_Telefone_1', 'label'=>'Contato Telefone', 'type' => 'text','length' => array(2,2)),
					'D024_Telefone_1' => array('name' => 'D024_Telefone_1', 'label'=>'', 'type' => 'text'),
					'D024_D018_Id' => array('name' => 'D024_D018_Id', 'label' => 'UF',     'type' => 'select','align'=>'left', 'style'=>'float:left', 'extra' => "onchange=\"alteraEstado(this, '{$g['divId']}')\"", 'select' => 
						gGeraSelect('D018', 'D018_Id', 'D018_UF','', true)
					),
					'D024_D020_Id' => array('name' => 'D024_D020_Id', 'label' => 'Cidade', 	'type' => 'select', 'align'=>'right', 'style'=>'float:right', 'restricao' => false, 'select' => 
						gGeraSelect('D020', 'D020_Id', 'D020_Nome_Cidade','', true)
					),
					'D024_D030_Id' => array('name' => 'D024_D030_Id', 'label' => 'País', 'type' => 'select', 'align'=>'left', 'style'=>'float:left', 'restricao' => false,  'value' => 30, 'select' => 
						gGeraSelect('D030', 'D030_Id', 'D030_Nome_Pais,D030_Codigo_Area', 'order by D030_Nome_Pais', false)
					),
					'D024_Flag_Pessoa_Fisica_Juridica' => array('name' => 'D024_Flag_Pessoa_Fisica_Juridica', 'label' => 'Pessoa', 'type' => 'select', 'align'=>'right', 'style'=>'float:right', 'select' => 
						array(
							array('title' => '...', 'value' => ''),
							array('title' => 'Jurídica', 'value' => 'J', 'selected' => true),
							array('title' => 'Física', 'value' => 'F'), 
							array('title' => 'Internacional', 'value' => 'I'),
						)
					),
					'D024_Flag_Contribuinte' => 	array('name' => 'D024_Flag_Contribuinte', 'label' => 'Tipo', 'type' => 'select', 'align'=>'right', 'style'=>'float:right', 'select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Não contribuinte', 'value' => '0' ),
							array('title' => 'Consumidor Final', 'value' => '1'),
							array('title' => 'Revenda', 'value' => '2'),
							array('title' => 'Produtor rural', 'value' => '3'),
							array('title' => 'Simples', 'value' => '4'),
							array('title' => 'Industria', 'value' => '5')
						)
					),
					// valores padrão
					'D024_Flag_Lista_Preco' => 	array('name' => 'D024_Flag_Lista_Preco', 'label' => '', 'type' => 'hidden', 'value' => 1),
					'D024_Flag_Cliente' => array('name' => 'D024_Flag_Cliente', 'label' => '', 'type' => 'hidden', 'value' => 'S'),
					'D024_Flag_Fornecedor' => array('name' => 'D024_Flag_Fornecedor', 'label' => '', 'type' => 'hidden', 'value' => 'N'),
					'D024_Flag_Ativo' => array('name' => 'D024_Flag_Ativo', 'label' => '', 'type' => 'hidden', 'value' => 'S'),
					'D024_Flag_Pre_Cadastro' => array('name' => 'D024_Flag_Pre_Cadastro', 'label' => '', 'type' => 'hidden', 'value' => 'S'),
					'D024_Data_Cadastro' => array('name' => 'D024_Data_Cadastro', 'label' => '', 'type' => 'hidden', 'value' => date('Y-m-d')),
					'D024_C007_Vendedor_Interno' => array('name' => 'D024_C007_Vendedor_Interno', 'label' => 'Vendedor Interno', 'type' => 'select', 'select' => 
						gGeraSelect("C007", "C007_Id", "C007_Primeiro_Nome", "where (C007_Flag_Vendedor='V' or C007_Flag_Vendedor='S' or C007_Flag_Vendedor='D') and C007_Ativo='S' order by C007_Primeiro_Nome", true, $g['usuarioAtual'])
					),
					'D024_Flag_Comercial' => 	array('name' => 'D024_Flag_Comercial', 'label' => '', 'type' => 'hidden', 'value' => 'A'),
					'D024_Observacoes' => 	array('name' => 'D024_Observacoes', 'label' => 'Observações', 'type' => 'textarea', 'value' => ''),
				),
			) // 'campos'
		);
	
		// Botão Limpar / Novo Registro
		if (!empty($acaoId)) {
			$formArray['campos']['geral']['cad001PedidoIncluirClienteBtnNew'] = array('name' => 'cad001PedidoIncluirClienteBtnNew', 'type'=>'submit', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		}
		$formArray['campos']['geral']['cad001PedidoIncluirClienteBtnCln'] = array('name' => 'cad001PedidoIncluirClienteBtnCln', 'type'=>'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['geral']['cad001PedidoIncluirClienteBtnAdd'] = array('name' => 'cad001PedidoIncluirClienteBtnAdd', 'type'=>'button', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$form->definirFormularioArray($formArray);
		
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} #D024_Flag_Pessoa_Fisica_Juridica').change(function() {
					if($(this).val() == 'F') {
						$('#{$g['divId']} #D024_Cnpj').closest('.formEditor').hide();
						$('#{$g['divId']} #buttonSearchCnpj').closest('.formEditor').hide();
						$('#{$g['divId']} #D024_Cpf').closest('.formEditor').show();
					} else if($(this).val() == 'J') {
						$('#{$g['divId']} #D024_Cpf').closest('.formEditor').hide();
						$('#{$g['divId']} #D024_Cnpj').closest('.formEditor').show();
						$('#{$g['divId']} #buttonSearchCnpj').closest('.formEditor').show();
					} else {
						$('#{$g['divId']} #D024_Cnpj').closest('.formEditor').hide();
						$('#{$g['divId']} #buttonSearchCnpj').closest('.formEditor').hide();
						$('#{$g['divId']} #D024_Cpf').closest('.formEditor').hide();
					}
				});
				$('#{$g['divId']} #D024_Flag_Pessoa_Fisica_Juridica').trigger('change');
			} </script></div>
	EOT;
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
					// Insert ou Update?
					if (!empty($acaoId)) {
						// Update
						if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
							// Imprime uma mensagem e fecha a janela
							echo  <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							divRefresh('{$form->campoValorEnviado('divReload')}', true);
							divRefresh('{$g['divId']}', true);
						} </script></div>
	EOT;
						}
				} else {
					// Insert
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						$D013_D024_Id = $g['mysqlLastId'];
						$D013_Nome_Contato = mysql_real_escape_string(strtoupper($form->campoValorEnviado('D024_Nome_Contato')));
						$D024_Nome_Empresa = mysql_real_escape_string(strtoupper($form->campoValorEnviado('D024_Nome_Empresa')));
						$D013_E_Mail = mysql_real_escape_string(strtoupper($form->campoValorEnviado('D024_E_Mail')));
						$D013_DDD_Telefone_1 = mysql_real_escape_string(strtoupper($form->campoValorEnviado('D024_DDD_Telefone_1')));
						$D013_Telefone_1 = mysql_real_escape_string(strtoupper($form->campoValorEnviado('D024_Telefone_1')));
						mysql_query("INSERT INTO D013 (D013_D024_Id, D013_Nome_Contato, D013_E_Mail, D013_DDD_Telefone_1, D013_Telefone_1) VALUES ('{$D013_D024_Id}', '{$D013_Nome_Contato}', '{$D013_E_Mail}', '{$D013_DDD_Telefone_1}', '{$D013_Telefone_1}')");
						$D013_Id = $g['mysqlLastId'];
						// Imprime uma mensagem e fecha a janela
						echo  <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$('#{$g['divIdParent']} .itemForm-T005_D024_Id').val('$D013_D024_Id');
								$('#{$g['divIdParent']} .itemForm-D085_D024_Id').val('$D013_D024_Id');
								// enviarFormAutomatico é utilizado dentro do carregaCFOPContato.js para eviar o form depois de carregar os dados
								// .data('enviarFormAutomatico', 1)
								$('#{$g['divIdParent']} .itemForm-T005_D024_Id_Pesquisa').val('$D024_Nome_Empresa').data('definirContato', '{$D013_Id}').trigger('change');
								$('#{$g['divIdParent']} .itemForm-D085_D024_Id_Pesquisa').val('$D024_Nome_Empresa').data('definirContato', '{$D013_Id}').trigger('change');
								fecharJanela('{$g['divId']}');
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
	break;

	case 'cad001FormCadastroEndereco':
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
		$form_tabela = 'D148';
		$form_campo_pk = 'D148_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad001FormCadastroEndereco_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad001/form/cad001FormCadastroEndereco/';
		$form_editor = array(
			'geral' => '{"D148_Nome":{"top":7,"left":27.125,"width":376,"hidden":0},"D148_Flag_Pessoa_Fisica_Juridica":{"top":30,"left":85.1875,"width":144,"hidden":0},"D148_Cpf":{"top":30,"left":355.875,"width":120,"hidden":0},"D148_Inscricao_Estadual":{"top":53,"left":52.78125,"width":136,"hidden":0},"D148_Cnpj":{"top":53,"left":349.15625,"width":120,"hidden":0},"D148_Cep":{"top":76,"left":99.265625,"width":136,"hidden":0},"buttonSearchCep2":{"top":74,"left":288,"width":null,"hidden":0},"D148_Rua":{"top":99,"left":67.453125,"width":264,"hidden":0},"D148_Numero":{"top":99,"left":418.75,"width":40,"hidden":0},"D148_Complemento":{"top":122,"left":53.40625,"width":136,"hidden":0},"D148_Bairro":{"top":122,"left":300.53125,"width":168,"hidden":0},"D148_D018_Id":{"top":145,"left":99.875,"width":51,"hidden":0},"D148_D020_Id":{"top":145,"left":223.6875,"width":239,"hidden":0},"D148_D030_Id":{"top":168,"left":92.53125,"width":383,"hidden":0},"D148_DDD_Telefone":{"top":191,"left":80.296875,"width":30,"hidden":0},"D148_Telefone":{"top":191,"left":176,"width":90,"hidden":0},"D148_DDD_Fax":{"top":214,"left":103.546875,"width":30,"hidden":0},"D148_Fax":{"top":214,"left":176,"width":90,"hidden":0},"D148_Email":{"top":237,"left":94.375,"width":376,"hidden":0},"D148_Flag_Tipo":{"top":260,"left":12.40625,"width":144,"hidden":0}}',
		);

		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('form_cad001FormCadastroEndereco');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D148_Nome' => array('name' => 'D148_Nome', 'label'=>'Nome / Descrição', 'type' => 'text'),
					'D148_Cep' => array('name' => 'D148_Cep', 'label'=>'CEP', 'type' => 'cep'),
					'buttonSearchCep2' =>     array('name' => 'buttonSearchCep2', 'label' => '', 'type' => 'button', 'extra' => "style='padding: 0; heigth: 20px; width: 50px;' onclick='pesquisaCep(false,\"{$g['divId']}\", \"{$g['divIdRoot']}\",\"D148_Cep\", {\"EnderecoDb\": \"D148_Rua\", \"Cep\": \"D148_Cep\", \"BairroDb\": \"D148_Bairro\", \"UfDb\": \"D148_D018_Id\",\"CidadeDb\": \"D148_D020_Id\", \"PaisDb\": \"D148_D030_Id\"})'", 'align'=>'left', 'value'=>'Buscar', 'reject'=>true),
					// Primeiro parâmetro do array(1,2), não interfere no valor inserido e sim o valor de número 2					
					'D148_Rua' => array('name' => 'D148_Rua', 'label'=>'Endereço', 'type' => 'text','length' => array(80,80)),
					'D148_Numero' => array('name' => 'D148_Numero', 'label'=>'Número', 'type' => 'text','length' => array(40,40)),
					'D148_Complemento' => array('name' => 'D148_Complemento', 'label'=>'Complemento', 'type' => 'text','length' => array(50,50)),
					'D148_Bairro' => array('name' => 'D148_Bairro', 'label'=>'Bairro', 'type' => 'text','length' => array(50,50)),
					'D148_DDD_Telefone' => array('name' => 'D148_DDD_Telefone', 'label' => 'Telefone', 'type' => 'text','length' => array(2,2)),
					'D148_Telefone' => array('name' => 'D148_Telefone', 'label' => '', 'type' => 'text','length' => array(14,14), 'align'=>'right'),
					'D148_DDD_Fax' => array('name' => 'D148_DDD_Fax', 'label' => 'Fax', 'type' => 'text','length' => array(2,2)),
					'D148_Fax' => array('name' => 'D148_Fax', 'label' => '', 'type' => 'text','length' => array(14,14), 'align'=>'right'),
					'D148_Email' => array('name' => 'D148_Email', 'label' => 'Email', 'type' => 'text','length' => array(100,100)),
					'D148_D018_Id' => array('name' => 'D148_D018_Id', 'label' => 'UF', 'type' => 'select','extra' => "onchange=\"alteraEstadoCadastroEndereco(this, '{$g['divId']}')\"", 'select' => 	
						gGeraSelect('D018', 'D018_Id', 'D018_UF', '', true)
					),
					'D148_D020_Id' => array('name' => 'D148_D020_Id', 'label' => 'Cidade', 'type' => 'select', 'select' => 
						gGeraSelect('D020', 'D020_Id', 'D020_Nome_Cidade','', true)
					),
					'D148_D030_Id' => array('name' => 'D148_D030_Id', 'label' => 'País', 'type' => 'select', 'select' => 
						gGeraSelect('D030', 'D030_Id', 'D030_Nome_Pais,D030_Codigo_Area', '', true, 30)  
					),
					'D148_Flag_Tipo' => array('name' => 'D148_Flag_Tipo', 'label' => 'Endereço padrão para', 'type' => 'select', 'align'=>'right', 'style'=>'float:right', 'select' => 
						array(
							array('title' => '-', 'value' => '', 'selected' => true),
							array('title' => 'ENTREGA', 'value' => 'E' ),
							array('title' => 'COBRANÇA', 'value' => 'C' ),
							array('title' => 'REDESPACHO', 'value' => 'R' ),
						)
					),
					'D148_D024_Id' => array('name' => 'D148_D024_Id', 'label'=>'', 'type' => 'hidden', 'value' => $r_D024_Id),
					'D148_Cnpj' => array('name' => 'D148_Cnpj', 'label' => 'CNPJ', 'type' => 'cnpj', 'length' => array(18,18)),
					'D148_Cpf' => array('name' => 'D148_Cpf', 'label' => 'CPF', 'type' => 'cpf', 'align' => 'right', 'style' => 'float:right', 'length' => array(14,20)),
					'D148_Inscricao_Estadual' => array('name' => 'D148_Inscricao_Estadual', 'label' => 'Insc. Estadual', 'type' => 'text', 'length' => array(20,20)),
					'D148_Flag_Pessoa_Fisica_Juridica' => array('name' => 'D148_Flag_Pessoa_Fisica_Juridica', 'label' => 'Pessoa', 'type' => 'select', 'align'=>'right', 'style'=>'float:right', 'select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'FÍSICA', 'value' => 'F' ),
							array('title' => 'JURÍDICA', 'value' => 'J' ),
						)
					),
				),
			) // 'campos'
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
						$alert = gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
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

	case 'cad001CotacaoIncluirFornecedor':
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
		$form_tabela = 'D024';
		$form_campo_pk = 'D024_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = array('cad001CotacaoIncluirFornecedorBtnAdd', 'cad001CotacaoIncluirFornecedorBtnCln', 'cad001CotacaoIncluirFornecedorBtnNew');
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad001/form/cad001CotacaoIncluirFornecedor/';
		$form_editor = array(
			'geral' => '{"D024_Cnpj":{"top":145,"left":330,"width":136,"hidden":0},"D024_Nome_Empresa":{"top":7,"left":56,"width":408,"hidden":0},"D024_Cpf":{"top":145,"left":368,"width":104,"hidden":0},"D013_Nome_Contato":{"top":30,"left":46,"width":408,"hidden":0},"D013_E_Mail":{"top":53,"left":22,"width":408,"hidden":0},"D013_DDD_Telefone_1":{"top":76,"left":9,"width":40,"hidden":0},"D013_Telefone_1":{"top":76,"left":144,"width":120,"hidden":0},"D024_D018_Id":{"top":98.984375,"left":70,"width":51,"hidden":0},"D024_D020_Id":{"top":98.984375,"left":185,"width":287,"hidden":0},"D024_D030_Id":{"top":121.984375,"left":69,"width":415,"hidden":0},"D024_Flag_Pessoa_Fisica_Juridica":{"top":144.984375,"left":47,"width":144,"hidden":0},"D024_Flag_Contribuinte":{"top":168,"left":319,"width":160,"hidden":0},"D024_C007_Vendedor_Interno":{"top":168,"left":6,"width":207,"hidden":0},"cad001CotacaoIncluirFornecedorBtnAdd":{"top":189,"left":208,"width":null,"hidden":0},"cad001CotacaoIncluirFornecedorBtnCln":{"top":189,"left":304,"width":null,"hidden":0}}',
		);

		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('form_cad001CotacaoIncluirFornecedor');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D024_Nome_Empresa' => array('name' => 'D024_Nome_Empresa', 'label'=>'Nome', 'type' => 'text'),
					'D013_Nome_Contato' => array('name' => 'D013_Nome_Contato', 'label'=>'Contato', 'type' => 'text', 'reject' => true),
					'D024_Cnpj' => 	array('name' => 'D024_Cnpj', 'label' => 'CNPJ', 'type' => 'cnpj', 'length' => array(20,20)),
					'D024_Cpf' => 	array('name' => 'D024_Cpf', 'label' => 'CPF', 'type' => 'cpf', 'align' => 'right', 'style' => 'float:right', 'length' => array(14,30)),
					'D013_E_Mail' => array('name' => 'D013_E_Mail', 'label'=>'Contato E-Mail', 'type' => 'text', 'reject' => true),
					'D013_DDD_Telefone_1' => array('name' => 'D013_DDD_Telefone_1', 'label'=>'Contato Telefone', 'type' => 'text','length' => array(2,2), 'reject' => true),
					'D013_Telefone_1' => array('name' => 'D013_Telefone', 'label'=>'', 'type' => 'text', 'reject' => true),
					'D024_D018_Id' => array('name' => 'D024_D018_Id', 'label' => 'UF',     'type' => 'select','align'=>'left', 'style'=>'float:left', 'extra' => "onchange=\"alteraEstado(this, '{$g['divId']}')\"", 'select' => 
						gGeraSelect('D018', 'D018_Id', 'D018_UF','', true)
					),
					'D024_D020_Id' => array('name' => 'D024_D020_Id', 'label' => 'Cidade', 	'type' => 'select', 'align'=>'right', 'style'=>'float:right', 'restricao' => false, 'select' => 
						gGeraSelect('D020', 'D020_Id', 'D020_Nome_Cidade','', true)
					),
					'D024_D030_Id' => array('name' => 'D024_D030_Id', 'label' => 'País', 'type' => 'select', 'align'=>'left', 'style'=>'float:left', 'restricao' => false,  'value' => 38, 'select' => 
						gGeraSelect('D030', 'D030_Id', 'D030_Nome_Pais,D030_Codigo_Area', 'order by D030_Nome_Pais', false)
					),
					'D024_Flag_Pessoa_Fisica_Juridica' => array('name' => 'D024_Flag_Pessoa_Fisica_Juridica', 'label' => 'Pessoa', 'type' => 'select', 'align'=>'right', 'style'=>'float:right', 'select' => 
						array(
							array('title' => '...', 'value' => ''),
							array('title' => 'Jurídica', 'value' => 'J', 'selected' => true),
							array('title' => 'Física', 'value' => 'F'), 
							array('title' => 'Internacional', 'value' => 'I'),
						)
					),
					'D024_Flag_Contribuinte' => 	array('name' => 'D024_Flag_Contribuinte', 'label' => 'Tipo', 'type' => 'select', 'align'=>'right', 'style'=>'float:right', 'select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Não contribuinte', 'value' => '0' ),
							array('title' => 'Consumidor Final', 'value' => '1'),
							array('title' => 'Revenda', 'value' => '2'),
							array('title' => 'Produtor rural', 'value' => '3'),
							array('title' => 'Simples', 'value' => '4'),
							array('title' => 'Industria', 'value' => '5')
						)
					),
					// valores padrão
					'D024_Flag_Lista_Preco' => 	array('name' => 'D024_Flag_Lista_Preco', 'label' => '', 'type' => 'hidden', 'value' => 1),
					'D024_Flag_Cliente' => array('name' => 'D024_Flag_Cliente', 'label' => '', 'type' => 'hidden', 'value' => 'N'),
					'D024_Flag_Fornecedor' => array('name' => 'D024_Flag_Fornecedor', 'label' => '', 'type' => 'hidden', 'value' => 'S'),
					'D024_Flag_Ativo' => array('name' => 'D024_Flag_Ativo', 'label' => '', 'type' => 'hidden', 'value' => 'S'),
					'D024_Flag_Pre_Cadastro' => array('name' => 'D024_Flag_Pre_Cadastro', 'label' => '', 'type' => 'hidden', 'value' => 'S'),
					'D024_Data_Cadastro' => array('name' => 'D024_Data_Cadastro', 'label' => '', 'type' => 'hidden', 'value' => date('Y-m-d')),
					'D024_C007_Vendedor_Interno' => array('name' => 'D024_C007_Vendedor_Interno', 'label' => 'Vendedor Interno', 'type' => 'select', 'select' => 
						gGeraSelect("C007", "C007_Id", "C007_Primeiro_Nome", "where (C007_Flag_Vendedor='V' or C007_Flag_Vendedor='S' or C007_Flag_Vendedor='D') and C007_Ativo='S' order by C007_Primeiro_Nome", true, $g['usuarioAtual'])
					),
				),
			) // 'campos'
		);

		// Botão Limpar / Novo Registro
		if (!empty($acaoId)) {
			$formArray['campos']['geral']['cad001CotacaoIncluirFornecedorBtnNew'] = array('name' => 'cad001CotacaoIncluirFornecedorBtnNew', 'type'=>'submit', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		}
		$formArray['campos']['geral']['cad001CotacaoIncluirFornecedorBtnCln'] = array('name' => 'cad001CotacaoIncluirFornecedorBtnCln', 'type'=>'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['geral']['cad001CotacaoIncluirFornecedorBtnAdd'] = array('name' => 'cad001CotacaoIncluirFornecedorBtnAdd', 'type'=>'button', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$form->definirFormularioArray($formArray);

		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} #D024_Flag_Pessoa_Fisica_Juridica').change(function() {
					if($(this).val() == 'F') {
						$('#{$g['divId']} #D024_Cnpj').closest('.formEditor').hide();
						$('#{$g['divId']} #D024_Cpf').closest('.formEditor').show();
					} else if($(this).val() == 'J') {
						$('#{$g['divId']} #D024_Cpf').closest('.formEditor').hide();
						$('#{$g['divId']} #D024_Cnpj').closest('.formEditor').show();
					} else {
						$('#{$g['divId']} #D024_Cnpj').closest('.formEditor').hide();
						$('#{$g['divId']} #D024_Cpf').closest('.formEditor').hide();
					}
				});
				$('#{$g['divId']} #D024_Flag_Pessoa_Fisica_Juridica').trigger('change');
			} </script></div>
EOT;
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
					// Insert ou Update?
					if (!empty($acaoId)) {
						// Update
						if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
							// Imprime uma mensagem e fecha a janela
							echo  <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							divRefresh('{$form->campoValorEnviado('divReload')}', true);
							divRefresh('{$g['divId']}', true);
						} </script></div>
EOT;
						}
				} else {
					// Insert
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						$D013_D024_Id = $g['mysqlLastId'];
						$D024_Nome_Empresa = mysql_real_escape_string(strtoupper($form->campoValorEnviado('D024_Nome_Empresa')));
						$D013_Nome_Contato = mysql_real_escape_string(strtoupper($form->campoValorEnviado('D013_Nome_Contato')));
						$D013_E_Mail = mysql_real_escape_string(strtoupper($form->campoValorEnviado('D013_E_Mail')));
						$D013_DDD_Telefone_1 = mysql_real_escape_string(strtoupper($form->campoValorEnviado('D013_DDD_Telefone_1')));
						$D013_Telefone_1 = mysql_real_escape_string(strtoupper($form->campoValorEnviado('D013_Telefone_1')));
						mysql_query("INSERT INTO D013 (D013_D024_Id, D013_Nome_Contato, D013_E_Mail, D013_DDD_Telefone_1, D013_Telefone_1) VALUES ('{$D013_D024_Id}', '{$D013_Nome_Contato}', '{$D013_E_Mail}', '{$D013_DDD_Telefone_1}', '{$D013_Telefone_1}')");
						$D013_Id = $g['mysqlLastId'];
						// Imprime uma mensagem e fecha a janela
						echo  <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$('#{$g['divIdParent']} .itemForm-T218_D024_Id').val('$D013_D024_Id');
								// enviarFormAutomatico é utilizado dentro do carregaCFOPContato.js para eviar o form depois de carregar os dados
								$('#{$g['divIdParent']} .itemForm-T218_D024_Id_Pesquisa').val('$D024_Nome_Empresa').data('definirContato', '{$D013_Id}').data('enviarFormAutomatico', 1).trigger('change');
								fecharJanela('{$g['divId']}');
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
	break;

	case 'cad001FormImportarCSV':
	
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
		$form_tabela = 'D024';
		$form_campo_pk = 'D024';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad001FormImportarCSV_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad001/form/cad001FormImportarCSV/';
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('cad001FormImportarCSV');
		$formArray = array(
			'campos' => array(
				'geral' => array(
				)
			)
		);
		$formArray['campos']['geral']['uploadArquivoCSVCAD001'] 		= array('name' => 'uploadArquivoCSVCAD001', 'label' => 'Selecione o CSV', 'type' => 'file', 'length' => array(24, 24), 'reject' => true);
		$formArray['campos']['geral']['cad001FormImportarCSV_btn'] 		= array('name' => 'cad001FormImportarCSV_btn', 'type'=>'submit', 'reload' => false, 'value' => 'Enviar', 'align' => 'right', 'style' => 'float:right;', 'extra' => 'style="margin-right:10px;margin-top:0px;padding-left:10px;padding-right:10px;padding-top:0px;height:20px;"', 'reject'=>true);
		$formArray['campos']['geral']['D024_Arquivo_Csv'] 				= array('name' => 'D024_Arquivo_Csv', 'type' => 'hidden');
		$formArray['campos']['geral']['divReload'] 						= array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true);
		
		echo <<<EOT
						<div class=""></div>
EOT;


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
				$arquivo = $form->campoValorEnviado('uploadArquivoCSVCAD001');
				$retorno = $CAD001->importarCSV($arquivo);
				
				if($retorno !== true){
					//$retorno = htmlentities(utf8_decode($retorno));
					$retorno = htmlentities($retorno);
					echo gGeraAlertMsg('Relatório de Importação', '<textarea style=" background-color: #FFF; width:900px; height:400px; text-transform: none;">' . $retorno . '</textarea>');
				}else{
					// Imprime uma mensagem
					echo gGeraAlertMsg('Operação OK', 'CSV Importado com Sucesso!');
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

}
