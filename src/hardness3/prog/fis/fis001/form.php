<?php
namespace hardness;

require_once("bibliotecas/classes/FIS001.php");
$FIS001 = new FIS001();

require_once("bibliotecas/classes/CAD002.php");
$CAD002 = new CAD002();

require_once("bibliotecas/classes/ReformaTributaria.php");
$reformaTributaria = new ReformaTributaria();

/**
* Formulário
* /fis/fis001/form/
*/

/**
* Ações
*/
switch ($g['r'][3]) {

	/**
	* Formulário CFOP
	* Chamado em: fis001tab02
	*/
	case 'fis001form01':
		global $g;
		// O campo "Consulta CFOP" utiliza jQuery UI autocomplete e tem um comportamento diferente do padrão para o type = 'pesquisa'
		// Por isso redefinimos aqui o callback do autocomplete (http://jqueryui.com/demos/autocomplete/)
		$pesquisaSelect = <<<EOT
			select: function(event, ui) {
				$('#D006_Codigo_CFOP').val(ui.item.value);
				$(this).val('');
				return false;
			}
EOT;
		$pesquisaChange = <<<EOT
			change: function(event, ui) {
				//
			}
EOT;
	
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		// D059, utilizado para mostrar os campos referentes a cada  modalidade do ICMS
		$D059_Id = (isset($_REQUEST['D059_Id'])) ? $_REQUEST['D059_Id'] : false;
		
		/**
		* PARTE 2: Definição do formulário
		*/
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = true;
		if ($formMultiTab) {
			$formId = (isset($r_formfis001form1)) ? $r_formfis001form1['formId'] : $r_formId;
		} else {
			$formId = uniqid();
		}

		if(isset($r_formfis001form1) && $r_formfis001form1['D006_D059_Id']){
			$D059_Id = $r_formfis001form1['D006_D059_Id'];
		}
		
		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'D006';
		$form_campo_pk = 'D006_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'content_form1_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/fis/fis001/form/fis001form01/';
		// Editor
		
		$form_editor = array(
			'geral'      => '{"D006_Codigo_CFOP":{"top":7,"left":81.921875,"width":58,"hidden":0},"D006_Codigo_CFOP_Contra_Partida":{"top":7,"left":277.515625,"width":152,"hidden":0},"D006_Descricao":{"top":30,"left":98.265625,"width":376,"hidden":0},"D006_Flag_Entrada_Saida":{"top":53,"left":77.65625,"width":176,"hidden":0},"D006_Flag_Estado":{"top":76,"left":113.15625,"width":176,"hidden":0},"D006_Flag_Devolucao":{"top":99,"left":95.90625,"width":176,"hidden":0},"D006_Percentual_Trib_Transparencia":{"top":99,"left":787.28125,"width":58,"hidden":1},"D006_Flag_Venda_Compra_Outros":{"top":122,"left":125.671875,"width":176,"hidden":0},"D006_Flag_Departamento":{"top":145,"left":77.078125,"width":176,"hidden":0},"D006_Observacao":{"top":168,"left":96.296875,"height":83,"width":376,"hidden":0},"D006_Observacao_Produto":{"top":260,"left":54.71875,"height":62,"width":376,"hidden":0},"D006_Flag_Inativo":{"top":329,"left":80.421875,"width":160,"hidden":0},"D006_D006_Id":{"top":352,"left":73.90625,"width":383,"hidden":0},"D006_D188_Id":{"top":375,"left":64.75,"width":383,"hidden":0},"D006_C004_Id":{"top":398,"left":111.4375,"width":383,"hidden":0}}',
			'estoque'    => '{"D006_Flag_Estoque":{"top":7,"left":74.171875,"width":179,"hidden":0},"D006_Flag_Unidade_Tributavel":{"top":76,"left":-1.03125,"width":57,"hidden":0},"D006_Flag_MMF":{"top":30,"left":2.65625,"width":112,"hidden":0},"D006_Flag_Atualiza_Custo":{"top":53,"left":46.0625,"width":112,"hidden":0}}',
			'financeiro' => '{"D006_Flag_Gera_Contas":{"top":7,"left":23,"width":208,"hidden":0},"D006_D014_Id":{"top":30,"left":56,"width":185,"hidden":0},"D006_D073_Id":{"top":53,"left":0,"width":185,"hidden":0}}',
			'retorno'    => '{"D006_Flag_Retorno":{"top":7,"left":29,"width":112,"hidden":0},"D006_Numero_Dias_Retorno":{"top":30,"left":22,"width":53,"hidden":0}}',
			//'icms'       => '{"D006_Flag_Calcula_FCP":{"top":30,"left":402.359375,"width":128,"hidden":0},"D006_Flag_Isento_ICMS":{"top":7,"left":134.015625,"width":128,"hidden":0},"D006_Flag_Deb_Crd_ICMS":{"top":7,"left":394.40625,"width":128,"hidden":0},"D006_Flag_Somar_IPI_Base_ICMS":{"top":30,"left":103.4375,"width":128,"hidden":0},"D006_Valor_Codigo_Beneficio_Fiscal":{"top":53,"left":86.328125,"width":120,"hidden":0},"D006_Codigo_Beneficio_Fiscal":{"top":53,"left":352.828125,"width":128,"hidden":1},"D006_D059_Id":{"top":76,"left":102.828125,"width":399,"hidden":0},"D006_Motivo_Desoneracao_ICMS":{"top":99,"left":102.828125,"width":570,"hidden":1}}',
			'icms'       => '{"D006_ST_ICMS_Inter_Estadual_MVA":{"top":0,"left":0,"width":56,"hidden":1},"D006_ST_ICMS_Interno_MVA":{"top":0,"left":0,"width":56,"hidden":1},"D006_Codigo_Beneficio_Fiscal":{"top":0,"left":0,"width":143.921784,"hidden":1},"D006_Flag_Isento_ICMS":{"top":6.9853057861328125,"left":151.82293701171875,"width":128,"hidden":0},"D006_Flag_Deb_Crd_ICMS":{"top":6.9853057861328125,"left":413.89398193359375,"width":128,"hidden":0},"D006_Flag_Somar_IPI_Base_ICMS":{"top":29.993881225585938,"left":122.2120361328125,"width":128,"hidden":0},"D006_Flag_Calcula_FCP":{"top":29.993881225585938,"left":422.51837158203125,"width":128,"hidden":0},"D006_Flag_Somar_DIFAL_Custo":{"top":52.987152099609375,"left":103.6611328125,"width":128,"hidden":0},"D006_Flag_Calcula_FCP_ST":{"top":52.987152099609375,"left":406.7095947265625,"width":128,"hidden":0},"D006_Valor_Codigo_Beneficio_Fiscal":{"top":75.9957275390625,"left":103.78369140625,"width":120.03921600000001,"hidden":0},"D006_Flag_Calcula_FCP_Retido":{"top":75.9957275390625,"left":388.44976806640625,"width":128,"hidden":0},"D006_Flag_Somar_ICMS_Diferido":{"top":98.98898315429688,"left":76.33270263671875,"width":127,"hidden":0},"D006_D059_Id":{"top":121.99755859375,"left":119.056396484375,"width":399,"hidden":0},"D006_Aliquota_ICMS":{"top":144.99081420898438,"left":181.556396484375,"width":87.03921600000001,"hidden":0},"D006_Percentual_FCP":{"top":144.99081420898438,"left":507.65936279296875,"width":72.03921600000001,"hidden":0},"D006_Percentual_Partilha_ICMS_Origem":{"top":167.9993896484375,"left":102.6807861328125,"width":87.03921600000001,"hidden":0},"D006_Flag_Substituicao_Tributaria":{"top":167.9993896484375,"left":428.3394775390625,"width":79,"hidden":0},"D006_Percentual_Partilha_ICMS_Destino":{"top":190.99264526367188,"left":100.965087890625,"width":88.03921600000001,"hidden":0},"D006_Flag_Deb_Crd_ST":{"top":190.99264526367188,"left":482.53680419921875,"width":79,"hidden":0},"D006_Flag_Somar_Fator_ICMS_Dif_Base":{"top":213.9705810546875,"left":45.2818603515625,"width":95,"hidden":0},"D006_Flag_Somar_Despesas_Base_ST":{"top":213.9705810546875,"left":368.9644775390625,"width":79,"hidden":0},"D006_Flag_Endereco_Aliquota_Icms_Interno_Difal":{"top":236.97918701171875,"left":15.333984375,"width":96,"hidden":0},"D006_Flag_ST_VA":{"top":236.97918701171875,"left":477.57354736328125,"width":79,"hidden":0},"D006_ST_ICMS_Interno":{"top":259.9877624511719,"left":144.2861328125,"width":89.03921600000001,"hidden":0},"D006_Flag_Ajustar_MVA":{"top":259.9877624511719,"left":477.68072509765625,"width":79,"hidden":0},"D006_Somar_FCP_ICMS_Interno":{"top":282.996337890625,"left":20.3431396484375,"width":96,"hidden":0},"D006_ST_MVA":{"top":282.996337890625,"left":515.2420043945312,"width":72.03921600000001,"hidden":0},"D006_Percentual_Reducao_ICMS":{"top":305.9895935058594,"left":135.9681396484375,"width":88.03921600000001,"hidden":0},"D006_Reducao_MVA":{"top":305.9895935058594,"left":458.88482666015625,"width":72.03921600000001,"hidden":0},"D006_Flag_Reduz_Base_DIFAL":{"top":328.9828796386719,"left":47.487762451171875,"width":95,"hidden":0}, "D006_Percentual_Reducao_Base_DIFAL":{"top":328.9828796386719,"left":383.2475891113281,"width":72.03921600000001,"hidden":0}, "D006_Codigo_Beneficio_Reducao_Base_ICMS":{"top":328.9828796386719,"left":394.85296630859375,"width":71.03921600000001,"hidden":0},"D006_ST_ICMS_Inter_Estadual":{"top":351.9914245605469,"left":111.48895263671875,"width":89.03921600000001,"hidden":0},"D006_Flag_Deduz_FCP_ST_Valor_ST":{"top":351.9914245605469,"left":405.48406982421875,"width":79,"hidden":0},"D006_Flag_Destaca_Reducao_ICMS_XML":{"top":375.0000305175781,"left":50.9957275390625,"width":95,"hidden":0},"D006_ST_ICMS_Interno_Reducao":{"top":375.0000305175781,"left":408.241455078125,"width":72.03921600000001,"hidden":0},"D006_Modalidade_ICMS_Substituicao":{"top":397.9932556152344,"left":24.4945068359375,"width":400,"hidden":0},"D006_UF_ICMS_Substituicao_Devido":{"top":420.9865417480469,"left":59.84991455078125,"width":399,"hidden":0},"D006_Modalidade_ICMS":{"top":443.9797668457031,"left":40.3033447265625,"width":400,"hidden":0},"D006_Deduz_ICMS_Desonerado_Valor_Total":{"top":466.9883728027344,"left":41.77392578125,"width":63,"hidden":0},"D006_Percentual_Base_Operacao_Propria":{"top":466.9883728027344,"left":429.10540771484375,"width":56.039215999999996,"hidden":0},"D006_Motivo_Desoneracao_ICMS":{"top":489.9815979003906,"left":118.87255859375,"width":399,"hidden":0}}',
			'ipi'        => '{"D006_Flag_Isento_IPI":{"top":7,"left":85.171875,"width":112,"hidden":0},"D006_Flag_Somar_Despesas_Base_IPI":{"top":30,"left":7.078125,"width":112,"hidden":0},"D006_Flag_Credita_IPI":{"top":53,"left":75.25,"width":112,"hidden":0},"D006_D088_Id":{"top":76,"left":12.140625,"width":319,"hidden":0},"D006_D088_Id_Sem_Aliquota":{"top":99,"left":12.671875,"width":317,"hidden":0},"D006_Codigo_Enq_Legal_IPI":{"top":122,"left":20.09375,"width":56,"hidden":0},"D006_Reducao_Aliquota_IPI":{"top":145,"left":27.078125,"width":56,"hidden":0}}',
			//'st'         => '{"D006_Modalidade_ICMS_Substituicao":{"top":168,"left":63.0625,"width":280,"hidden":0},"D006_Flag_Substituicao_Tributaria":{"top":7,"left":145.640625,"width":112,"hidden":0},"D006_ST_ICMS_Inter_Estadual":{"top":7,"left":673,"width":53,"hidden":1},"D006_Flag_ST_VA":{"top":30,"left":133,"width":112,"hidden":0},"D006_ST_ICMS_Interno":{"top":30,"left":705,"width":53,"hidden":1},"D006_Flag_Ajustar_MVA":{"top":53,"left":141.5625,"width":58,"hidden":0},"D006_ST_ICMS_Interno_Reducao":{"top":53,"left":635,"width":53,"hidden":1},"D006_Flag_Deb_Crd_ST":{"top":76,"left":143.1875,"width":59,"hidden":0},"D006_ST_ICMS_Inter_Estadual_MVA":{"top":76,"left":647,"width":53,"hidden":1},"D006_Flag_Somar_Despesas_Base_ST":{"top":99,"left":61.25,"width":112,"hidden":0},"D006_ST_ICMS_Interno_MVA":{"top":99,"left":679,"width":53,"hidden":1},"D006_Somar_FCP_ICMS_Interno":{"top":122,"left":-2.328125,"width":112,"hidden":0},"D006_ST_MVA":{"top":145,"left":178.859375,"width":53,"hidden":0},"D006_Reducao_MVA":{"top":145,"left":691,"width":53,"hidden":1}}',
			'pis'        => '{"D006_Flag_Isento_PIS":{"top":7,"left":113.59375,"width":112,"hidden":0},"D006_Flag_Deb_Crd_PIS":{"top":30,"left":111.125,"width":112,"hidden":0},"D006_Flag_Forma_Calculo_PIS":{"top":53,"left":100.859375,"width":112,"hidden":0},"D006_Flag_Deduz_ICMS_Base_PIS":{"top":76,"left":64.28125,"width":112,"hidden":0},"D006_Flag_Deduz_FCP_Base_PIS":{"top":99,"left":70.390625,"width":111,"hidden":0},"D006_Flag_Deduz_Partilha_ICMS_Destino_Base_PIS":{"top":122,"left":7.421875,"width":111,"hidden":0},"D006_Flag_Somar_Despesas_Base_PIS":{"top":145,"left":46.4375,"width":111,"hidden":0},"D006_Flag_Envia_PIS_XML":{"top":168,"left":119.703125,"width":111,"hidden":0},"D006_Situacao_Tributaria_PIS":{"top":191,"left":70.96875,"width":342,"hidden":0},"D006_Aliquota_PIS":{"top":214,"left":143.34375,"width":104,"hidden":0},"D006_Percentual_Reducao_PIS":{"top":237,"left":97.71875,"width":104,"hidden":0},"D006_Flag_Somar_IPI_Base_Retido":{"top":260,"left":50.65625,"width":111,"hidden":0},"D006_Flag_Somar_ST_Base_Retido":{"top":283,"left":50.6875,"width":111,"hidden":0},"D006_Flag_Somar_FCP_Base_Retido":{"top":306,"left":43.390625,"width":111,"hidden":0}}',
			'cofins'     => '{"D006_Flag_Deduz_Partilha_ICMS_Destino_Base_COFINS":{"top":122,"left":7.421875,"width":111,"hidden":0},"D006_Flag_Isento_COFINS":{"top":7,"left":91.875,"width":112,"hidden":0},"D006_Flag_Deb_Crd_COFINS":{"top":30,"left":89.40625,"width":112,"hidden":0},"D006_Flag_Forma_Calculo_COFINS":{"top":53,"left":100.859375,"width":112,"hidden":0},"D006_Flag_Deduz_ICMS_Base_COFINS":{"top":76,"left":64.28125,"width":112,"hidden":0},"D006_Flag_Deduz_FCP_Base_COFINS":{"top":99,"left":70.390625,"width":111,"hidden":0},"D006_Flag_Somar_Despesas_Base_COFINS":{"top":145,"left":46.4375,"width":110,"hidden":0},"D006_Flag_Envia_COFINS_XML":{"top":168,"left":97.984375,"width":111,"hidden":0},"D006_Situacao_Tributaria_COFINS":{"top":191,"left":70.96875,"width":342,"hidden":0},"D006_Aliquota_COFINS":{"top":214,"left":121.625,"width":104,"hidden":0},"D006_Percentual_Reducao_COFINS":{"top":237,"left":76,"width":104,"hidden":0},"D006_Flag_Somar_IPI_Base_COFINS_Retido":{"top":260,"left":50.65625,"width":111,"hidden":0},"D006_Flag_Somar_ST_Base_COFINS_Retido":{"top":283,"left":50.6875,"width":111,"hidden":0},"D006_Flag_Somar_FCP_Base_COFINS_Retido":{"top":306,"left":43.390625,"width":111,"hidden":0}}',
			'outros'     => '{"D006_Flag_Isento_IRPJ":{"top":6.9699859619140625,"left":227.86456298828125,"width":96,"hidden":0},"D006_Percentual_Desconto_CFOP":{"top":6.9699859619140625,"left":529.2586059570312,"width":72.03921600000001,"hidden":0},"D006_Flag_Isento_CSLL":{"top":29.978546142578125,"left":224.034912109375,"width":96,"hidden":0},"D006_Flag_Destaca_Desconto_NF":{"top":29.978546142578125,"left":480.65252685546875,"width":79,"hidden":0},"D006_Flag_Somar_Despesas_Base_CSLL":{"top":52.971832275390625,"left":148.131103515625,"width":95,"hidden":0},"D006_Flag_Escritura_Sped_Contribuicoes":{"top":52.971832275390625,"left":443.1065673828125,"width":79,"hidden":0},"D006_Flag_Somar_Despesas_Base_IRPJ":{"top":75.98040771484375,"left":151.96075439453125,"width":95,"hidden":0},"D006_Flag_ICMS_Deson_DANFE":{"top":75.9957275390625,"left":442.539794921875,"width":79,"hidden":0},"D006_Flag_Envia_CSLL_XML":{"top":98.97366333007812,"left":187.86761474609375,"width":96,"hidden":0},"D006_Codigo_Beneficio_Credito_Presumido":{"top":98.98898315429688,"left":427.8798828125,"width":72.03921600000001,"hidden":0},"D006_Flag_Envia_IRPJ_XML":{"top":121.99755859375,"left":191.697265625,"width":96,"hidden":0},"D006_Percentual_Beneficio_Credito_Presumido":{"top":121.99755859375,"left":445.251220703125,"width":72.03921600000001,"hidden":0},"D006_Flag_Tipo_Desconto":{"top":144.99081420898438,"left":44.14825439453125,"width":383,"hidden":0},"D006_Flag_Tipo_Desconto_PIS_COFINS":{"top":167.9993896484375,"left":11.289794921875,"width":383,"hidden":0},"D006_Flag_Tipo_Desconto_IPI":{"top":190.99264526367188,"left":57.506103515625,"width":383,"hidden":0},"D006_Indicador_Natureza_Frete":{"top":213.98590087890625,"left":195.5115966796875,"width":383,"hidden":0},"D006_Base_Calculo_Credito":{"top":236.99447631835938,"left":66.881103515625,"width":383,"hidden":0},"D006_Impressao_Dados_Produtos_DANFE":{"top":259.9877624511719,"left":42.003662109375,"height":70.03921600000001,"width":376.039216,"hidden":0},"D006_Flag_Somar_FCP_Base_CSLL":{"top":351.9914245605469,"left":158.18011474609375,"width":96,"hidden":0},"D006_Flag_Somar_FCP_Base_IRPJ":{"top":351.9914245605469,"left":450.01531982421875,"width":96,"hidden":0},"D006_Flag_Somar_ST_Base_CSLL":{"top":375.0000305175781,"left":165.4718017578125,"width":96,"hidden":0},"D006_Flag_Somar_IPI_Base_IRPJ":{"top":375.0000305175781,"left":457.260986328125,"width":96,"hidden":0},"D006_Flag_Somar_IPI_Base_CSLL":{"top":397.9932556152344,"left":165.42584228515625,"width":96,"hidden":0},"D006_Flag_Somar_ST_Base_IRPJ":{"top":397.9932556152344,"left":457.30694580078125,"width":96,"hidden":0}}',
			/* [REFORMA TRIBUTÁRIA] - Gabriel Cegantini 27/11/2025
			================================================================ */
			'reforma'	   => '{"D006_Situacao_Tributaria_CBS_IBS":{"top":6.9853057861328125,"left":170.419677734375,"width":543,"hidden":0},"D006_Classificacao_Tributaria_CBS_IBS":{"top":29.993865966796875,"left":100.67401123046875,"width":543,"hidden":0},"D006_Aliquota_CBS_Visual":{"top":52.987152099609375,"left":203.29345703125,"width":104.03921600000001,"hidden":0},"D006_Percentual_Reducao_CBS_Visual":{"top":52.987152099609375,"left":402.86456298828125,"width":104.03921600000001,"hidden":0},"D006_Percentual_Diferimento_CBS_Visual":{"top":52.987152099609375,"left":615.9619750976562,"width":104.03921600000001,"hidden":0},"D006_Aliquota_IBS_UF_Visual":{"top":75.9957275390625,"left":191.52874755859375,"width":104.03921600000001,"hidden":0},"D006_Percentual_Reducao_IBS_UF_Visual":{"top":75.9957275390625,"left":391.099853515625,"width":104.03921600000001,"hidden":0},"D006_Percentual_Diferimento_IBS_UF_Visual":{"top":75.9957275390625,"left":604.1973266601562,"width":104.03921600000001,"hidden":0},"D006_Aliquota_IBS_Municipal_Visual":{"top":98.98898315429688,"left":180.11639404296875,"width":104.03921600000001,"hidden":0},"D006_Percentual_Reducao_IBS_Municipal_Visual":{"top":98.98898315429688,"left":379.6875,"width":104.03921600000001,"hidden":0},"D006_Percentual_Diferimento_IBS_Municipal_Visual":{"top":98.98898315429688,"left":592.7848510742188,"width":104.03921600000001,"hidden":0},"D006_Situacao_Tributaria_CBS_IBS_Regular":{"top":144.99081420898438,"left":146.032470703125,"width":543,"hidden":0},"D006_Classificacao_Tributaria_CBS_IBS_Regular":{"top":167.9993896484375,"left":76.2867431640625,"width":543,"hidden":0},"D006_Aliquota_CBS_Regular_Visual":{"top":190.99264526367188,"left":178.90625,"width":104.03921600000001,"hidden":0},"D006_Percentual_Reducao_CBS_Regular_Visual":{"top":190.99264526367188,"left":378.46197509765625,"width":104.03921600000001,"hidden":0},"D006_Percentual_Diferimento_CBS_Regular_Visual":{"top":190.99264526367188,"left":591.5747680664062,"width":104.03921600000001,"hidden":0},"D006_Aliquota_IBS_UF_Regular_Visual":{"top":213.98590087890625,"left":167.14154052734375,"width":104.03921600000001,"hidden":0},"D006_Percentual_Reducao_IBS_UF_Regular_Visual":{"top":213.98590087890625,"left":366.697265625,"width":104.03921600000001,"hidden":0},"D006_Percentual_Diferimento_IBS_UF_Regular_Visual":{"top":213.98590087890625,"left":579.8099975585938,"width":104.03921600000001,"hidden":0},"D006_Aliquota_IBS_Municipal_Regular_Visual":{"top":236.99447631835938,"left":155.71380615234375,"width":104.03921600000001,"hidden":0},"D006_Percentual_Reducao_IBS_Municipal_Regular_Visual":{"top":236.99447631835938,"left":355.284912109375,"width":104.03921600000001,"hidden":0},"D006_Percentual_Diferimento_IBS_Municipal_Regular_Visual":{"top":236.99447631835938,"left":568.3823852539062,"width":104.03921600000001,"hidden":0},"D006_Flag_Nao_Somar_IBS_CBS_Valor_Total_Reforma":{"top":282.996337890625,"left":54.96319580078125,"width":63,"hidden":0},"D006_Flag_Deduz_PIS_Base_IBS_CBS":{"top":305.9895935058594,"left":136.99444580078125,"width":63,"hidden":0},"D006_Flag_Deduz_COFINS_Base_IBS_CBS":{"top":328.9981384277344,"left":115.30328369140625,"width":63,"hidden":0},"D006_Flag_Deduz_ICMS_Base_IBS_CBS":{"top":351.9914245605469,"left":89.5679931640625,"width":63,"hidden":0},"D006_Flag_Deduz_DIFAL_Base_IBS_CBS":{"top":375.0000305175781,"left":9.54345703125,"width":63,"hidden":0},"D006_Flag_Deduz_FCP_Base_IBS_CBS":{"top":397.9932556152344,"left":95.664794921875,"width":63,"hidden":0},"D006_Flag_Deduz_FCP_Dest_Base_IBS_CBS":{"top":420.9865417480469,"left":54.4117431640625,"width":63,"hidden":0}}',
		);
       	
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('formfis001form1');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					//'D006_Codigo_CFOP_' => array('name' => 'D006_Codigo_CFOP_', 'label' => 'Consulta CFOP', 'type' => 'pesquisa', 'pesquisa' => array('D091', 'D091_Id', 'D091_Id,D091_Descricao'), 'pesquisaSelect' => $pesquisaSelect, 'pesquisaChange' => $pesquisaChange, 'length' => array(30,30), 'reject' => true),
					'D006_Codigo_CFOP' => array('name' => 'D006_Codigo_CFOP', 'label' => 'Código CFOP','type' => 'text', 'length' => array(6,6)),
					'D006_Codigo_CFOP_Contra_Partida' => array('name' => 'D006_Codigo_CFOP_Contra_Partida', 'label' => 'CFOP Contra Partida','type' => 'text', 'length' => array(255,255)),
		        	'D006_Descricao' => array('name' => 'D006_Descricao', 'label'=>'Descrição', 'type' => 'text','length' => array(50,50)),
					'D006_Flag_Entrada_Saida' => array('name' => 'D006_Flag_Entrada_Saida', 'label'=>'Saida/Entrada', 'type' => 'select','select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Saida', 'value' => 'S'),
							array('title' => 'Entrada', 'value' => 'E'),
						)
					),
					'D006_Flag_Estado' => array('name' => 'D006_Flag_Estado', 'label'=>'Estado', 'type' => 'select', 'select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Dentro do Estado', 'value' => 'D'),
							array('title' => 'Fora do Estado', 'value' => 'F'),
							array('title' => 'Internacional', 'value' => 'I'),
						)
					),					
					'D006_Flag_Devolucao' => array('name' => 'D006_Flag_Devolucao', 'label'=>'Devolução', 'type' => 'select','select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Não', 'value' => 'N'),
							array('title' => 'Sim', 'value' => 'S' ),
						)
					),
					'D006_Flag_Venda_Compra_Outros' => array('name' => 'D006_Flag_Venda_Compra_Outros', 'label'=>'Tipo', 'type' => 'select','select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Venda', 'value' => 'V'),
							array('title' => 'Compra', 'value' => 'C' ),
							array('title' => 'Transferencia', 'value' => 'T' ),
							array('title' => 'Outras entradas', 'value' => 'OE' ),
							array('title' => 'Outras saídas', 'value' => 'OS' ),
							array('title' => 'Devolução Vendas', 'value' => 'DV' ),
							array('title' => 'Devolução Compras', 'value' => 'DC' ),
							array('title' => 'Transporte', 'value' => 'TR' ),
						)
					),					
					'D006_Flag_Departamento' => array('name' => 'D006_Flag_Departamento', 'label'=>'Departamento', 'type' => 'select','select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Comercial', 'value' => 'COMERCIAL'),
							array('title' => 'Administração', 'value' => 'ADMINISTRACAO' ),
						)
					),					
					'D006_Observacao' => array('name' => 'D006_Observacao', 'label'=>'Observação', 'type' => 'textarea','length' => array(5,65)),
					'D006_Observacao_Produto' => array('name' => 'D006_Observacao_Produto', 'label'=>'Observação Produto', 'type' => 'textarea','length' => array(5,65)),
					'D006_Flag_Inativo' => array('name' => 'D006_Flag_Inativo', 'label'=>'Inativar CFOP', 'type' => 'select','select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Não', 'value' => 'N'),
							array('title' => 'Sim', 'value' => 'S' ),
						)
					),
					'D006_Percentual_Trib_Transparencia' => array('name' => 'D006_Percentual_Trib_Transparencia', 'label' => 'Percentual tributos (Lei tarnsp)','type' => 'moeda', 'length' => array(6,6)),
					'D006_D006_Id' => array('name' => 'D006_D006_Id', 'label'=>'CFOP devolução', 'type' => 'select', 'select' => 
						gGeraSelect('D006', 'D006_Id', 'D006_Codigo_CFOP, D006_Descricao',"where D006_Flag_Inativo!='S' order by D006_Codigo_CFOP",true),
					),
					'D006_D188_Id' => array('name' => 'D006_D188_Id', 'label'=>'Observação Fiscal', 'type' => 'select', 'select' => 
						gGeraSelect('D188', 'D188_Id', 'D188_Descricao_Observacao','order by D188_Descricao_Observacao',true),
					),
					'formId' => array('name' => 'formId', 'label' => '', 'type' => 'hidden', 'value' => $formId, 'reject' => true),
					'D006_C004_Id' => array('name' => 'D006_C004_Id', 'label'=>'Empresa', 'type' => 'select', 'select' => 
						gGeraSelect('C004', 'C004_Id', 'C004_Nome_Abreviado','WHERE C004_Flag_Ativo = "S" order by C004_Nome_Abreviado',true),
					),
                ),

				'estoque' => array(
					'D006_Flag_Estoque' => array('name' => 'D006_Flag_Estoque', 'label'=>'Estoque', 'type' => 'select','select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Não altera estoque', 'value' => 'N'),
							array('title' => 'Credita', 'value' => 'C'),
							array('title' => 'Debita', 'value' => 'D'),
						),
					),
					'D006_Flag_MMF' => array('name' => 'D006_Flag_MMF', 'label'=>'Min, Max e Frequencia', 'type' => 'select','select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N'),
						),
					),
					'D006_Flag_Atualiza_Custo' => array('name' => 'D006_Flag_Atualiza_Custo', 'label'=>'Atualiza custo', 'type' => 'select','select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N'),
						),
					),
					'D006_Flag_Atualiza_Custo' => array('name' => 'D006_Flag_Atualiza_Custo', 'label'=>'Atualiza custo', 'type' => 'select','select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N'),
						),
					),
					'D006_Flag_Unidade_Tributavel' => array('name' => 'D006_Flag_Unidade_Tributavel', 'label'=>'Possui unidade tributável', 'type' => 'select','select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N'),
						)
					),
				),
				
				'financeiro' => array(
					'D006_Flag_Gera_Contas' => array('name' => 'D006_Flag_Gera_Contas', 'label'=>'Gera contas', 'type' => 'select','select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Não gera contas', 'value' => 'N'),
							array('title' => 'Receber', 'value' => 'R'),
							array('title' => 'Pagar', 'value' => 'P'),
						),
					),
					'D006_D014_Id' => array('name' => 'D006_D014_Id', 'label'=>'Conta', 'type' => 'pesquisa', 'pesquisa' => array('D014','D014_Id','D014_SubConta,D154_Descricao', 'LEFT JOIN D154 ON D154_Id=D014_D154_Id WHERE D014_Flag_Ativo = "S"')),
					'D006_D073_Id' => array('name' => 'D006_D073_Id', 'label'=>'Centro de Custos', 'type' => 'pesquisa', 'pesquisa' => array('D073','D073_Id','D073_Nome', 'WHERE D073_Flag_Ativo = "S"') ),
                ),
				
				'retorno' => array(
					'D006_Flag_Retorno' => array('name' => 'D006_Flag_Retorno', 'label'=>'Possui retorno', 'type' => 'select','select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N'),
						),
					),
					'D006_Numero_Dias_Retorno' => array('name' => 'D006_Numero_Dias_Retorno', 'label'=>'Dias para retorno', 'type' => 'text', 'length' => array(5,5)), 
                ),				
                
				'icms' => array(
					'D006_Flag_Isento_ICMS' => array('name' => 'D006_Flag_Isento_ICMS', 'label'=>'Isento ICMS', 'type' => 'select','select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N'),
							array('title' => 'Desonerado', 'value' => 'D'),
						),
					),
					'D006_Flag_Somar_IPI_Base_ICMS' => array('name' => 'D006_Flag_Somar_IPI_Base_ICMS', 'label'=>'Somar IPI na base', 'type' => 'select','select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N'),
						),
					),
					'D006_Flag_Somar_ICMS_Diferido' => array('name' => 'D006_Flag_Somar_ICMS_Diferido', 'label'=>'Somar ICMS diferido na base', 'type' => 'select','select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N'),
						),
					),
					'D006_Flag_Deb_Crd_ICMS' => array('name' => 'D006_Flag_Deb_Crd_ICMS', 'label'=>'Credita ICMS?', 'type' => 'select','select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N'),
						),
					),
					'D006_Valor_Codigo_Beneficio_Fiscal' => array('name' => 'D006_Valor_Codigo_Beneficio_Fiscal', 'label'=>'Codigo Benefício Fiscal', 'type' => 'texto','length' => array(10,10)),
					/* Campos para NT 2019.001 - Criação e Atualização de Regras de Validação
					Versão 1.64 – Setembro de 2024, por solicitação da Smartness
					Gabriel Cegantini - 02/10/2025
					================================================================================ */
					'D006_Codigo_Beneficio_Reducao_Base_ICMS' => array('name' => 'D006_Codigo_Beneficio_Reducao_Base_ICMS', 'label'=>'Cód. Benefício Fiscal Red. BC', 'type' => 'texto','length' => array(10,10), 'uppercase' => false),
					/* ================================================================================ */
					'D006_Percentual_Reducao_ICMS' => array('name' => 'D006_Percentual_Reducao_ICMS', 'label'=>'% Redução ICMS', 'type' => 'moeda', 'casasDecimais' => '2', 'length' => array(5,5)),
					'D006_D059_Id' => array('name' => 'D006_D059_Id', 'label'=>'Situação tributária', 'type' => 'select','select' => 
						$arrayCST
					),
					'D006_Aliquota_ICMS' => array('name' => 'D006_Aliquota_ICMS', 'label'=>'% ICMS', 'type' => 'moeda', 'casasDecimais' => '2', 'length' => array(5,5)),
					'D006_Percentual_Partilha_ICMS_Destino' => array('name' => 'D006_Percentual_Partilha_ICMS_Destino', 'label'=>'% Partilha ICMS Destino', 'type' => 'moeda', 'casasDecimais' => '2', 'length' => array(5,5)),
					'D006_Percentual_Partilha_ICMS_Origem' => array('name' => 'D006_Percentual_Partilha_ICMS_Origem', 'label'=>'% Partilha ICMS Origem', 'type' => 'moeda', 'casasDecimais' => '2', 'length' => array(5,5)),
					'D006_Flag_Somar_Fator_ICMS_Dif_Base' => array('name' => 'D006_Flag_Somar_Fator_ICMS_Dif_Base', 'label'=>'Somar fator partilha ICMS na base?', 'type' => 'select','select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N'),
						),
					),
					'D006_Flag_Calcula_FCP' => array('name' => 'D006_Flag_Calcula_FCP', 'label'=>'Calcular FCP?', 'type' => 'select','select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N'),
						),
					),
					'D006_Flag_Calcula_FCP_ST' => array('name' => 'D006_Flag_Calcula_FCP_ST', 'label'=>'Calcular FCP ST?', 'type' => 'select','select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N'),
						),
				    ),
					'D006_Flag_Calcula_FCP_Retido' => array('name' => 'D006_Flag_Calcula_FCP_Retido', 'label'=>'Calcular FCP Retido?', 'type' => 'select','select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N'),
						),
					),
					'D006_UF_ICMS_Substituicao_Devido' => array('name' => 'D006_UF_ICMS_Substituicao_Devido', 'label'=>'UF para qual é devido o ICMS ST', 'type' => 'select','select' => 
						gGeraSelect('D018', 'D018_UF', 'D018_UF,D018_Estado', 'order by D018_Id', true)
					),
					'D006_Percentual_Base_Operacao_Propria' => array('name' => 'D006_Percentual_Base_Operacao_Propria', 'label'=>'% da BC operação própria', 'type' => 'moeda', 'casasDecimais' => '2', 'length' => array(5,5)),
					'D006_Codigo_Beneficio_Fiscal' => array('name' => 'D006_Codigo_Beneficio_Fiscal', 'label' => 'Benefício Fiscal Cliente?','type' => 'select', 'select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N'),
						),
					),
					'D006_Flag_Somar_DIFAL_Custo' => array('name' => 'D006_Flag_Somar_DIFAL_Custo', 'label'=>'Somar DIFAL no Custo?', 'type' => 'select','select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N'),
						),
					),
					'D006_Flag_Substituicao_Tributaria' => array('name' => 'D006_Flag_Substituicao_Tributaria', 'label'=>'Substituição Tributária', 'type' => 'select','select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N'),
						),
					),
					'D006_Flag_ST_VA' => array('name' => 'D006_Flag_ST_VA', 'label'=>'Agrega MVA', 'type' => 'select','select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N'),
						),
					),
					'D006_Flag_Ajustar_MVA' => array('name' => 'D006_Flag_Ajustar_MVA', 'label'=>'Ajustar MVA', 'type' => 'select','select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N'),
						),
					),
					'D006_Flag_Deb_Crd_ST' => array('name' => 'D006_Flag_Deb_Crd_ST', 'label'=>'Credita ST?', 'type' => 'select','select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N'),
						),
					),
					'D006_ST_ICMS_Inter_Estadual' => array('name' => 'D006_ST_ICMS_Inter_Estadual', 'label'=>'% ICMS Inter Estadual', 'type' => 'moeda', 'casasDecimais' => '2', 'length' => array(5,5)),
					'D006_ST_ICMS_Interno' => array('name' => 'D006_ST_ICMS_Interno', 'label'=>'% ICMS Interno', 'type' => 'moeda', 'casasDecimais' => '2', 'length' => array(5,5)),
					'D006_ST_ICMS_Interno_Reducao' => array('name' => 'D006_ST_ICMS_Interno_Reducao', 'label'=>'% Redução BC do ICMS ST', 'type' => 'moeda', 'casasDecimais' => '2', 'length' => array(5,5)),
					'D006_ST_ICMS_Inter_Estadual_MVA' => array('name' => 'D006_ST_ICMS_Inter_Estadual_MVA', 'label'=>'% ICMS Inter Estadual MVA', 'type' => 'moeda', 'casasDecimais' => '2', 'length' => array(5,5)),
					'D006_ST_ICMS_Interno_MVA' => array('name' => 'D006_ST_ICMS_Interno_MVA', 'label'=>'% ICMS Interno MVA', 'type' => 'moeda', 'casasDecimais' => '2', 'length' => array(5,5)),
					'D006_ST_MVA' => array('name' => 'D006_ST_MVA', 'label'=>'MVA', 'type' => 'moeda', 'casasDecimais' => '2', 'length' => array(5,5)),
					'D006_Reducao_MVA' => array('name' => 'D006_Reducao_MVA', 'label'=>'% Redução MVA', 'type' => 'moeda', 'casasDecimais' => '2', 'length' => array(5,5)),
					'D006_Flag_Somar_Despesas_Base_ST' => array('name' => 'D006_Flag_Somar_Despesas_Base_ST', 'label'=>'Somar despesas na base ICMS/ST', 'type' => 'select','select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N'),
						),
					),
					'D006_Somar_FCP_ICMS_Interno' => array('name' => 'D006_Somar_FCP_ICMS_Interno', 'label'=>'Somar FCP na alíquota de ICMS Interno?', 'type' => 'select','select' => 
						array(
							array('title' => '...', 'value' => ''),
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N'),
						),
					),
					'D006_Modalidade_ICMS_Substituicao' => array('name' => 'D006_Modalidade_ICMS_Substituicao', 'label'=>'Modalidade de deter. da BC do ICMS ST', 'type' => 'select','select' => 
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
					'D006_Modalidade_ICMS' => array('name' => 'D006_Modalidade_ICMS', 'label'=>'Modalidade de deter. da BC do ICMS', 'type' => 'select','select' => 
						array(
							array('title' => '...', 'value' => '','selected' => true),
							array('title' => 'Margem Valor Agregado (%)', 'value' => '0'),
							array('title' => 'Pauta (Valor)', 'value' => '1'),
							array('title' => 'Preço Tabelado Máx. (valor)', 'value' => '2'),
							array('title' => 'Valor da Operação', 'value' => '3')
						)
					),
					'D006_Deduz_ICMS_Desonerado_Valor_Total' => array('name' => 'D006_Deduz_ICMS_Desonerado_Valor_Total', 'label'=>'Deduz ICMS desonerado valor total?', 'type' => 'select','select' => 
						array(
							array('title' => 'Sim', 'value' => 'S','selected' => true),
							array('title' => 'Não', 'value' => 'N')
						)
					),
					'D006_Percentual_FCP' => array('name' => 'D006_Percentual_FCP', 'label'=>'% FCP', 'type' => 'moeda', 'casasDecimais' => '2', 'length' => array(5,5)),
					'D006_Percentual_Reducao_Base_DIFAL' => array('name' => 'D006_Percentual_Reducao_Base_DIFAL', 'label'=>'% Red. exclusiva da BC do DIFAL', 'type' => 'moeda', 'casasDecimais' => '2', 'length' => array(5,5)),
					'D006_Flag_Reduz_Base_DIFAL' => array('name' => 'D006_Flag_Reduz_Base_DIFAL', 'label'=>'Usar red. do ICMS na BC do DIFAL?', 'type' => 'select','select' => 
						array(
							array('title' => 'Sim', 'value' => 'S','selected' => true),
							array('title' => 'Não', 'value' => 'N')
						)
					),		
					'D006_Flag_Deduz_FCP_ST_Valor_ST' => array('name' => 'D006_Flag_Deduz_FCP_ST_Valor_ST', 'label'=>'Reduz FCP ST do Valor ST?', 'type' => 'select','select' => 
						array(
							array('title' => 'Sim', 'value' => 'S','selected' => true),
							array('title' => 'Não', 'value' => 'N')
						)
					),		
					'D006_Flag_Endereco_Aliquota_Icms_Interno_Difal' => array('name' => 'D006_Flag_Endereco_Aliquota_Icms_Interno_Difal', 'label'=>'Usar % ICMS interno Difal da UF endereço', 'type' => 'select','select' => 
						array(
							array('title' => 'Padrão', 'value' => 'P','selected' => true),
							array('title' => 'Entrega', 'value' => 'E')
						)
					),		
					'D006_Flag_Destaca_Reducao_ICMS_XML' => array('name' => 'D006_Flag_Destaca_Reducao_ICMS_XML', 'label'=>'Destaca % redução ICMS no XML?', 'type' => 'select','select' => 
						array(
							array('title' => 'Sim', 'value' => 'S','selected' => true),
							array('title' => 'Não', 'value' => 'N')
						)
					),	
				),
                
				'ipi' => array(
					'D006_Flag_Isento_IPI' => array('name' => 'D006_Flag_Isento_IPI', 'label'=>'Isento IPI', 'type' => 'select','select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N'),
						),
					),
					'D006_Flag_Somar_Despesas_Base_IPI' => array('name' => 'D006_Flag_Somar_Despesas_Base_IPI', 'label'=>'Somar Despesas na base', 'type' => 'select','select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N'),
						),
					),
					'D006_Flag_Credita_IPI' => array('name' => 'D006_Flag_Credita_IPI', 'label'=>'Credita IPI?', 'type' => 'select','select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N'),
						),
					),
					'D006_D088_Id' => array('name' => 'D006_D088_Id', 'label'=>'Sit.tributária com aliquota', 'type' => 'select', 'select' =>
						gGeraSelect('D088', 'D088_Id', 'D088_Codigo_ST,D088_Descricao',"order by D088_Codigo_ST,D088_Descricao", true),
					),
					'D006_D088_Id_Sem_Aliquota' => array('name' => 'D006_D088_Id_Sem_Aliquota', 'label'=>'Sit.tributária sem aliquota', 'type' => 'select', 'select' =>
						gGeraSelect('D088', 'D088_Id', 'D088_Codigo_ST,D088_Descricao',"order by D088_Codigo_ST,D088_Descricao", true),
					),
					'D006_Codigo_Enq_Legal_IPI' => array('name' => 'D006_Codigo_Enq_Legal_IPI', 'label' => 'Cód. Enquadramento IPI', 'type' => 'texto', 'length' => array(3,3)),
					'D006_Reducao_Aliquota_IPI' => array('name' => 'D006_Reducao_Aliquota_IPI', 'label'=>'% Redução aliquota IPI', 'type' => 'moeda', 'casasDecimais' => '2', 'length' => array(5,5)),
                ),	
                			
				// 'st' => array(
					
                // ),	
                			
				'pis' => array(
					'D006_Flag_Isento_PIS' => array('name' => 'D006_Flag_Isento_PIS', 'label'=>'Isento PIS', 'type' => 'select','select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N'),
						),
					),
					'D006_Flag_Forma_Calculo_PIS' => array('name' => 'D006_Flag_Forma_Calculo_PIS', 'label'=>'Base cálculo', 'type' => 'select','select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Venda', 'value' => 'V'),
							array('title' => 'Lucro', 'value' => 'L'),
						),
					),
					'D006_Situacao_Tributaria_PIS' => array('name' => 'D006_Situacao_Tributaria_PIS', 'label'=>'Situação Tributária', 'type' => 'select','select' => 
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
						),
					),
					'D006_Flag_Deb_Crd_PIS' => array('name' => 'D006_Flag_Deb_Crd_PIS', 'label'=>'Credita PIS?', 'type' => 'select','select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N'),
						),
					),
					'D006_Flag_Deduz_ICMS_Base_PIS' => array('name' => 'D006_Flag_Deduz_ICMS_Base_PIS', 'label'=>'Deduz ICMS da base?', 'type' => 'select','select' => 
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N','selected' => true),
						),
					),	
					'D006_Flag_Deduz_FCP_Base_PIS' => array('name' => 'D006_Flag_Deduz_FCP_Base_PIS', 'label'=>'Deduz FCP da base?', 'type' => 'select','select' => 
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N','selected' => true),
						),
					),	
					'D006_Flag_Deduz_Partilha_ICMS_Destino_Base_PIS' => array('name' => 'D006_Flag_Deduz_Partilha_ICMS_Destino_Base_PIS', 'label'=>'Deduz Prtlh. ICMS Dest. da base?', 'type' => 'select','select' => 
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N','selected' => true),
						),
					),
					'D006_Flag_Somar_Despesas_Base_PIS' => array('name' => 'D006_Flag_Somar_Despesas_Base_PIS', 'label'=>'Somar Despesas na base', 'type' => 'select','select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N'),
						),
					),	
					'D006_Flag_Envia_PIS_XML' => array('name' => 'D006_Flag_Envia_PIS_XML', 'label'=>'Retém PIS', 'type' => 'select','select' =>
						array(
							array('title' => 'Não', 'value' => 'N', 'selected' => true),
							array('title' => 'Sim', 'value' => 'S'),
						),
					),
					'D006_Flag_Somar_IPI_Base_Retido' => array('name' => 'D006_Flag_Somar_IPI_Base_Retido', 'label'=>'Somar IPI na base retido', 'type' => 'select','select' => array(array('title' => 'Não', 'value' => 'N', 'selected' => true), array('title' => 'Sim', 'value' => 'S'))),
					'D006_Flag_Somar_ST_Base_Retido' => array('name' => 'D006_Flag_Somar_ST_Base_Retido', 'label'=>'Somar ST na base retido', 'type' => 'select','select' => array(array('title' => 'Não', 'value' => 'N', 'selected' => true), array('title' => 'Sim', 'value' => 'S'))),
					'D006_Flag_Somar_FCP_Base_Retido' => array('name' => 'D006_Flag_Somar_FCP_Base_Retido', 'label'=>'Somar FCP na base retido', 'type' => 'select','select' => array(array('title' => 'Não', 'value' => 'N', 'selected' => true), array('title' => 'Sim', 'value' => 'S'))),
					'D006_Percentual_Reducao_PIS' => array('name' => 'D006_Percentual_Reducao_PIS ', 'label'=>'% Redução PIS', 'type' => 'moeda', 'casasDecimais' => '2', 'length' => array(5,5)),
					'D006_Aliquota_PIS' => array('name' => 'D006_Aliquota_PIS ', 'label'=>'% PIS', 'type' => 'moeda', 'casasDecimais' => '2', 'length' => array(5,5)),
				),	
                			
				'cofins' => array(
					'D006_Flag_Isento_COFINS' => array('name' => 'D006_Flag_Isento_COFINS', 'label'=>'Isento COFINS', 'type' => 'select','select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N'),
						),
					),
					'D006_Flag_Forma_Calculo_COFINS' => array('name' => 'D006_Flag_Forma_Calculo_COFINS', 'label'=>'Base cálculo', 'type' => 'select','select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Venda', 'value' => 'V'),
							array('title' => 'Lucro', 'value' => 'L'),
						),
					),
					'D006_Situacao_Tributaria_COFINS' => array('name' => 'D006_Situacao_Tributaria_COFINS', 'label'=>'Situação Tributária', 'type' => 'select','select' => 
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
						),
					),
					'D006_Flag_Deb_Crd_COFINS' => array('name' => 'D006_Flag_Deb_Crd_COFINS', 'label'=>'Credita COFINS?', 'type' => 'select','select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N'),
						),
					),
					'D006_Flag_Deduz_ICMS_Base_COFINS' => array('name' => 'D006_Flag_Deduz_ICMS_Base_COFINS', 'label'=>'Deduz ICMS da base?', 'type' => 'select','select' => 
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N','selected' => true),
						),
					),	
					'D006_Flag_Deduz_FCP_Base_COFINS' => array('name' => 'D006_Flag_Deduz_FCP_Base_COFINS', 'label'=>'Deduz FCP da base?', 'type' => 'select','select' => 
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N','selected' => true),
						),
					),
					'D006_Flag_Deduz_Partilha_ICMS_Destino_Base_COFINS' => array('name' => 'D006_Flag_Deduz_Partilha_ICMS_Destino_Base_COFINS', 'label'=>'Deduz Prtlh. ICMS Dest. da base?', 'type' => 'select','select' => 
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N','selected' => true),
						),
					),
					'D006_Flag_Somar_Despesas_Base_COFINS' => array('name' => 'D006_Flag_Somar_Despesas_Base_COFINS', 'label'=>'Somar Despesas na base', 'type' => 'select','select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N'),
						),
					),
					'D006_Flag_Envia_COFINS_XML' => array('name' => 'D006_Flag_Envia_COFINS_XML', 'label'=>'Retém COFINS', 'type' => 'select','select' =>
						array(
							array('title' => 'Não', 'value' => 'N', 'selected' => true),
							array('title' => 'Sim', 'value' => 'S'),
						),
					),
					'D006_Flag_Somar_IPI_Base_COFINS_Retido' => array('name' => 'D006_Flag_Somar_IPI_Base_COFINS_Retido', 'label'=>'Somar IPI na base retido', 'type' => 'select','select' => array(array('title' => 'Não', 'value' => 'N', 'selected' => true), array('title' => 'Sim', 'value' => 'S'))),
					'D006_Flag_Somar_ST_Base_COFINS_Retido' => array('name' => 'D006_Flag_Somar_ST_Base_COFINS_Retido', 'label'=>'Somar ST na base retido', 'type' => 'select','select' => array(array('title' => 'Não', 'value' => 'N', 'selected' => true), array('title' => 'Sim', 'value' => 'S'))),
					'D006_Flag_Somar_FCP_Base_COFINS_Retido' => array('name' => 'D006_Flag_Somar_FCP_Base_COFINS_Retido', 'label'=>'Somar FCP na base retido', 'type' => 'select','select' => array(array('title' => 'Não', 'value' => 'N', 'selected' => true), array('title' => 'Sim', 'value' => 'S'))),
					'D006_Percentual_Reducao_COFINS' => array('name' => 'D006_Percentual_Reducao_COFINS ', 'label'=>'% Redução COFINS', 'type' => 'moeda', 'casasDecimais' => '2', 'length' => array(5,5)),					
					'D006_Aliquota_COFINS' => array('name' => 'D006_Aliquota_COFINS ', 'label'=>'% COFINS', 'type' => 'moeda', 'casasDecimais' => '2', 'length' => array(5,5)),
                ),
                'outros' => array(
					'D006_Flag_Isento_IRPJ' => array('name' => 'D006_Flag_Isento_IRPJ', 'label'=>'Isento IRPJ', 'type' => 'select','select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N'),
						),
					),
					'D006_Flag_Isento_CSLL' => array('name' => 'D006_Flag_Isento_CSLL', 'label'=>'Isento CSLL', 'type' => 'select','select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N'),
						),
					),
					'D006_Base_Calculo_Credito' => array('name' => 'D006_Base_Calculo_Credito', 'label'=>'Cód Base de Cálculo do Crédito PIS/COFINS', 'type' => 'select','select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => '01 - Aquisição de bens para revenda', 'value' => '01'), 
							array('title' => '02 - Aquisição de bens utilizados como insumo', 'value' => '02'), 
							array('title' => '03 - Aquisição de serviços utilizados como insumo', 'value' => '03'), 
							array('title' => '04 - Energia elétrica e térmica, inclusive sob a forma de vapor', 'value' => '04'), 
							array('title' => '05 - Aluguéis de prédios', 'value' => '05'), 
							array('title' => '06 - Aluguéis de máquinas e equipamentos', 'value' => '06'), 
							array('title' => '07 - Armazenagem de mercadoria e frete na operação de venda', 'value' => '07'), 
							array('title' => '08 - Contraprestações de arrendamento mercantil', 'value' => '08'), 
							array('title' => '09 - Máquinas, equipamentos e outros bens incorporados ao ativo imobilizado (crédito sobre encargos de depreciação).', 'value' => '09'), 
							array('title' => '10 - Máquinas, equipamentos e outros bens incorporados ao ativo imobilizado (crédito com base no valor de aquisição).', 'value' => '10'), 
							array('title' => '11 - Amortização e Depreciação de edificações e benfeitorias em imóveis', 'value' => '11'), 
							array('title' => '12 - Devolução de Vendas Sujeitas à Incidência Não-Cumulativa', 'value' => '12'), 
							array('title' => '13 - Outras Operações com Direito a Crédito (inclusive os créditos presumidos sobre receitas)', 'value' => '13'), 
							array('title' => '14 - Atividade de Transporte de Cargas – Subcontratação', 'value' => '14'), 
							array('title' => '15 - Atividade Imobiliária – Custo Incorrido de Unidade Imobiliária', 'value' => '15'), 
							array('title' => '16 - Atividade Imobiliária – Custo Orçado de unidade não concluída', 'value' => '16'), 
							array('title' => '17 - Atividade de Prestação de Serviços de Limpeza, Conservação e Manutenção – vale-transporte, vale-refeição ou vale-alimentação, fardamento ou uniforme.', 'value' => '17'), 
							array('title' => '18 - Estoque de abertura de bens', 'value' => '18'), 			
						),
					),
					'D006_Indicador_Natureza_Frete' => array('name' => 'D006_Indicador_Natureza_Frete', 'label'=>'Natureza do Frete', 'type' => 'select','select' => 
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => '0 - Operações de vendas, com ônus suportado pelo estabelecimento vendedor', 'value' => '0'),
							array('title' => '1 - Operações de vendas, com ônus suportado pelo adquirente', 'value' => '1'),
							array('title' => '2 - Operações de compras (bens para revenda, matériasprima e outros produtos, geradores de crédito)', 'value' => '2'),
							array('title' => '3 - Operações de compras (bens para revenda, matériasprima e outros produtos, não geradores de crédito)', 'value' => '3'),
							array('title' => '4 - Transferência de produtos acabados entre estabelecimentos da pessoa jurídica', 'value' => '4'),
							array('title' => '5 - Transferência de produtos em elaboração entre estabelecimentos da pessoa jurídic', 'value' => '5'),
							array('title' => '9 - Outras', 'value' => '9'),
						),
					),	
					'D006_Impressao_Dados_Produtos_DANFE' => array('name' => 'D006_Impressao_Dados_Produtos_DANFE', 'label'=>'Define os dados do produto impressos na DANFE', 'type' => 'textarea','length' => array(5,65),
																   'extra' => "title='" . "Por padrão imprime os dados:\nCODIGO, DESCRICAO, NCM, CEST, CST, CFOP, UNIDADE, QUANTIDADE, VALOR_UNITARIO, VALOR_IPI, VALOR_ST, V.UNIT+IPI+ST, VALOR_TOTAL, BASE_ICMS, VALOR_ICMS, %_ICMS, %_IPI \n\nCaso não deseje a impressão de algum dado incluir o nome e separar por virgula\nEX: VALOR_IPI,%_IPI" . "'"),
					'D006_Flag_Tipo_Desconto' => array('name' => 'D006_Flag_Tipo_Desconto', 'label'=>'Tipo Desconto no ICMS (Aplicado no Valor Total)', 'type' => 'select','select' => 
						array(
							array('title' => 'Condicional (Não abate na base do ICMS)', 'value' => 'C'),
							array('title' => 'Incondicional (Abate na base do ICMS)', 'value' => 'I'),
						),	
					),	
					'D006_Flag_Tipo_Desconto_PIS_COFINS' => array('name' => 'D006_Flag_Tipo_Desconto_PIS_COFINS', 'label'=>'Tipo Desconto no PIS/COFINS (Aplicado no Valor Total)', 'type' => 'select','select' => 
						array(
							array('title' => 'Condicional (Não abate na base do PIS/COFINS)', 'value' => 'C'),
							array('title' => 'Incondicional (Abate na base do PIS/COFINS)', 'value' => 'I'),
						),
					),	
					'D006_Flag_Tipo_Desconto_IPI' => array('name' => 'D006_Flag_Tipo_Desconto_IPI', 'label'=>'Tipo Desconto no IPI (Aplicado no Valor Total)', 'type' => 'select','select' => 
						array(
							array('title' => 'Condicional (Não abate na base do IPI)', 'value' => 'C'),
							array('title' => 'Incondicional (Abate na base do IPI)', 'value' => 'I'),
						),
					),	
					'D006_Flag_Destaca_Desconto_NF' => array('name' => 'D006_Flag_Destaca_Desconto_NF', 'label'=>'Destaca Desconto NF', 'type' => 'select','select' => 
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N','selected' => true),
						),
					),	
					'D006_Percentual_Desconto_CFOP' => array('name' => 'D006_Percentual_Desconto_CFOP', 'label'=>'% Desconto', 'type' => 'moeda', 'casasDecimais' => '2', 'length' => array(5,5)),																   						  
					'D006_Flag_Somar_Despesas_Base_IRPJ' => array('name' => 'D006_Flag_Somar_Despesas_Base_IRPJ', 'label' => 'Soma Despesas Base IRPJ', 'type' => 'select','select' => 
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N','selected' => true),
						),
					),	
					'D006_Flag_Somar_Despesas_Base_CSLL' => array('name' => 'D006_Flag_Somar_Despesas_Base_CSLL', 'label' => 'Soma Despesas Base CSLL', 'type' => 'select','select' => 
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N','selected' => true),
						),
					),	
					'D006_Flag_Somar_FCP_Base_CSLL' => array('name' => 'D006_Flag_Somar_FCP_Base_CSLL', 'label' => 'Somar FCP na base CSLL', 'type' => 'select', 'select' => array(array('title' => 'Não', 'value' => 'N', 'selected' => true), array('title' => 'Sim', 'value' => 'S'))),
					'D006_Flag_Somar_IPI_Base_CSLL' => array('name' => 'D006_Flag_Somar_IPI_Base_CSLL', 'label' => 'Somar IPI na base CSLL', 'type' => 'select', 'select' => array(array('title' => 'Não', 'value' => 'N', 'selected' => true), array('title' => 'Sim', 'value' => 'S'))),
					'D006_Flag_Somar_ST_Base_CSLL' => array('name' => 'D006_Flag_Somar_ST_Base_CSLL', 'label' => 'Somar ST na base CSLL', 'type' => 'select', 'select' => array(array('title' => 'Não', 'value' => 'N', 'selected' => true), array('title' => 'Sim', 'value' => 'S'))),
					'D006_Flag_Somar_FCP_Base_IRPJ' => array('name' => 'D006_Flag_Somar_FCP_Base_IRPJ', 'label' => 'Somar FCP na base IRPJ', 'type' => 'select', 'select' => array(array('title' => 'Não', 'value' => 'N', 'selected' => true), array('title' => 'Sim', 'value' => 'S'))),
					'D006_Flag_Somar_IPI_Base_IRPJ' => array('name' => 'D006_Flag_Somar_IPI_Base_IRPJ', 'label' => 'Somar IPI na base IRPJ', 'type' => 'select', 'select' => array(array('title' => 'Não', 'value' => 'N', 'selected' => true), array('title' => 'Sim', 'value' => 'S'))),
					'D006_Flag_Somar_ST_Base_IRPJ' => array('name' => 'D006_Flag_Somar_ST_Base_IRPJ', 'label' => 'Somar ST na base IRPJ', 'type' => 'select', 'select' => array(array('title' => 'Não', 'value' => 'N', 'selected' => true), array('title' => 'Sim', 'value' => 'S'))),
					'D006_Flag_Envia_IRPJ_XML' => array('name' => 'D006_Flag_Envia_IRPJ_XML', 'label' => 'Envia IRPJ no XML', 'type' => 'select', 'value' => 'N', 'select' =>
						array(
							array('title' => 'Não', 'value' => 'N','selected' => true),
							array('title' => 'Sim', 'value' => 'S'),
						),
					),
					'D006_Flag_Envia_CSLL_XML' => array('name' => 'D006_Flag_Envia_CSLL_XML', 'label' => 'Envia CSLL no XML', 'type' => 'select', 'value' => 'N', 'select' =>
						array(
							array('title' => 'Não', 'value' => 'N','selected' => true),
							array('title' => 'Sim', 'value' => 'S'),
						),
					),
					'D006_Flag_Escritura_Sped_Contribuicoes' => array('name' => 'D006_Flag_Escritura_Sped_Contribuicoes', 'label' => 'Escritura SPED Contribuições', 'type' => 'select','select' => 
						array(
							array('title' => 'Sim', 'value' => 'S','selected' => true),
							array('title' => 'Não', 'value' => 'N'),
						),
					),
					'D006_Flag_ICMS_Deson_DANFE' => array('name' => 'D006_Flag_ICMS_Deson_DANFE', 'label'=>'Imprimir ICMS Deson. DANFE', 'type' => 'select','select' => 
						array(
							array('title' => 'NÃO', 'value' => 'N', 'selected' => true),
							array('title' => 'SIM', 'value' => 'S'),
						),	
					),
					/* Campos para NT 2019.001 - Criação e Atualização de Regras de Validação
					Versão 1.64 – Setembro de 2024, por solicitação da Smartness
					Gabriel Cegantini - 02/10/2025
					================================================================================ */
					'D006_Codigo_Beneficio_Credito_Presumido' 		=> array('name' => 'D006_Codigo_Beneficio_Credito_Presumido', 'label'=>'Cód. Benefício Fiscal Cred. Pres.', 'type' => 'texto','length' => array(10,10), 'uppercase' => false),
					'D006_Percentual_Beneficio_Credito_Presumido' 	=> array('name' => 'D006_Percentual_Beneficio_Credito_Presumido', 'label'=>'% Benefício Fiscal Cred. Pres', 'type' => 'moeda', 'casasDecimais' => '2', 'length' => array(5,5)),
					/* ================================================================================ */
				),	
            ) // 'campos'
		);

		/* [REFORMA TRIBUTÁRIA] - Gabriel Cegantini 24/11/2025
		==============================================================*/
		// Callback pra pegar a classificação tributária da CST da reforma
		$callbackReformaAoAlterarCst = function ($campo) {
			return <<<EOT
				jsRecarregarCampo('{$campo}', array('cst' => \$array['campoAtual']));
			EOT;
		};
		// Callback pra trocar o select
		$callbackReformaPreencherSelectClassificacaoTributaria = function ($campo) {
			$tabela = explode('_', $campo)[0];
			return <<<EOT
				require_once("bibliotecas/classes/ReformaTributaria.php");
				\$reformaTributaria = new ReformaTributaria();
				// Se o usuário trocar a CST vamos receber ela na hora do form
				if (isset(\$array['cst'])) {
					\$cst = \$array['cst'];
				// Se é a primeira vez que o usuário abriu o form de alteração precisamos descobrir qual é ela
				} else {
					\$qObj = mysql_query("SELECT {$campo} 
										FROM {$tabela}
										WHERE {$tabela}_Id = '{\$array['acaoId']}'");
					\$mObj = mysql_fetch_assoc(\$qObj);
					\$cst = \$mObj['{$campo}'];
				}
				return \$reformaTributaria->getCclassTribFormSelect(\$cst);
			EOT;
		};
		// As alíquotas são derivadas da tabela oficial da Reforma Tributária.
		// Elas são exibidas na CFOP somente para conferência; não são gravadas na D006.
		$callbackReformaPreencherAliquota = function ($campoClassificacao, $chaveAliquota) {
			return <<<EOT
				require_once("bibliotecas/classes/ReformaTributaria.php");
				\$reformaTributaria = new ReformaTributaria();
				\$cClassTrib = \$array['todosCampos']['{$campoClassificacao}'] ?? '';
				if (empty(\$cClassTrib) && !empty(\$array['acaoId'])) {
					\$resultado = mysql_query("SELECT {$campoClassificacao} FROM D006 WHERE D006_Id = '" . (int) \$array['acaoId'] . "'");
					\$registro = mysql_fetch_assoc(\$resultado);
					\$cClassTrib = \$registro['{$campoClassificacao}'] ?? '';
				}
				\$aliquotas = \$reformaTributaria->getAliquotas(\$cClassTrib);
				return number_format((float) \$aliquotas['{$chaveAliquota}'], 2, ',', '');
EOT;
		};
		$callbackReformaAoAlterarClassificacao = function ($camposVisualizacao) {
			$atualizacoes = '';
			foreach ($camposVisualizacao as $campo => $chaveAliquota) {
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
			'D006_Aliquota_CBS_Visual' => 'aliquotaCbs',
			'D006_Percentual_Reducao_CBS_Visual' => 'percentualReducaoCbs',
			'D006_Percentual_Diferimento_CBS_Visual' => 'percentualDiferimentoCbs',
			'D006_Aliquota_IBS_UF_Visual' => 'aliquotaIbsUf',
			'D006_Percentual_Reducao_IBS_UF_Visual' => 'percentualReducaoIbsUf',
			'D006_Percentual_Diferimento_IBS_UF_Visual' => 'percentualDiferimentoIbsUf',
			'D006_Aliquota_IBS_Municipal_Visual' => 'aliquotaIbsMunicipal',
			'D006_Percentual_Reducao_IBS_Municipal_Visual' => 'percentualReducaoIbsMunicipal',
			'D006_Percentual_Diferimento_IBS_Municipal_Visual' => 'percentualDiferimentoIbsMunicipal'
		);
		$camposAliquotasReformaRegular = array();
		foreach ($camposAliquotasReforma as $campo => $chaveAliquota) {
			$camposAliquotasReformaRegular[str_replace('_Visual', '_Regular_Visual', $campo)] = $chaveAliquota;
		}
		$formArray['campos']['reforma'] = [
			'D006_Situacao_Tributaria_CBS_IBS' => array('name' => 'D006_Situacao_Tributaria_CBS_IBS', 'label' => 'CST CBS/IBS', 'type' => 'select', 'select' => $reformaTributaria->getCstsFormSelect(), 'callbackAoAlterar' => $callbackReformaAoAlterarCst('D006_Classificacao_Tributaria_CBS_IBS')),
			'D006_Classificacao_Tributaria_CBS_IBS' => array('name' => 'D006_Classificacao_Tributaria_CBS_IBS', 'label' => 'Classificação Trib. CBS/IBS', 'type' => 'select', 'select' => array(array('title' => '...', 'select' => '')), 'callbackPreencherSelect' => $callbackReformaPreencherSelectClassificacaoTributaria('D006_Situacao_Tributaria_CBS_IBS'), 'callbackAoAlterar' => $callbackReformaAoAlterarClassificacao($camposAliquotasReforma)),
			'D006_Aliquota_CBS_Visual' => array('name' => 'D006_Aliquota_CBS_Visual', 'label' => '% CBS', 'type' => 'moeda', 'casasDecimais' => 2, 'readonly' => true, 'reject' => true, 'callbackPreencherValor' => $callbackReformaPreencherAliquota('D006_Classificacao_Tributaria_CBS_IBS', 'aliquotaCbs')),
			'D006_Percentual_Reducao_CBS_Visual' => array('name' => 'D006_Percentual_Reducao_CBS_Visual', 'label' => '% Red. CBS', 'type' => 'moeda', 'casasDecimais' => 2, 'readonly' => true, 'reject' => true, 'callbackPreencherValor' => $callbackReformaPreencherAliquota('D006_Classificacao_Tributaria_CBS_IBS', 'percentualReducaoCbs')),
			'D006_Percentual_Diferimento_CBS_Visual' => array('name' => 'D006_Percentual_Diferimento_CBS_Visual', 'label' => '% Dif. CBS', 'type' => 'moeda', 'casasDecimais' => 2, 'readonly' => true, 'reject' => true, 'callbackPreencherValor' => $callbackReformaPreencherAliquota('D006_Classificacao_Tributaria_CBS_IBS', 'percentualDiferimentoCbs')),
			'D006_Aliquota_IBS_UF_Visual' => array('name' => 'D006_Aliquota_IBS_UF_Visual', 'label' => '% IBS UF', 'type' => 'moeda', 'casasDecimais' => 2, 'readonly' => true, 'reject' => true, 'callbackPreencherValor' => $callbackReformaPreencherAliquota('D006_Classificacao_Tributaria_CBS_IBS', 'aliquotaIbsUf')),
			'D006_Percentual_Reducao_IBS_UF_Visual' => array('name' => 'D006_Percentual_Reducao_IBS_UF_Visual', 'label' => '% Red. IBS UF', 'type' => 'moeda', 'casasDecimais' => 2, 'readonly' => true, 'reject' => true, 'callbackPreencherValor' => $callbackReformaPreencherAliquota('D006_Classificacao_Tributaria_CBS_IBS', 'percentualReducaoIbsUf')),
			'D006_Percentual_Diferimento_IBS_UF_Visual' => array('name' => 'D006_Percentual_Diferimento_IBS_UF_Visual', 'label' => '% Dif. IBS UF', 'type' => 'moeda', 'casasDecimais' => 2, 'readonly' => true, 'reject' => true, 'callbackPreencherValor' => $callbackReformaPreencherAliquota('D006_Classificacao_Tributaria_CBS_IBS', 'percentualDiferimentoIbsUf')),
			'D006_Aliquota_IBS_Municipal_Visual' => array('name' => 'D006_Aliquota_IBS_Municipal_Visual', 'label' => '% IBS Mun.', 'type' => 'moeda', 'casasDecimais' => 2, 'readonly' => true, 'reject' => true, 'callbackPreencherValor' => $callbackReformaPreencherAliquota('D006_Classificacao_Tributaria_CBS_IBS', 'aliquotaIbsMunicipal')),
			'D006_Percentual_Reducao_IBS_Municipal_Visual' => array('name' => 'D006_Percentual_Reducao_IBS_Municipal_Visual', 'label' => '% Red. IBS Mun.', 'type' => 'moeda', 'casasDecimais' => 2, 'readonly' => true, 'reject' => true, 'callbackPreencherValor' => $callbackReformaPreencherAliquota('D006_Classificacao_Tributaria_CBS_IBS', 'percentualReducaoIbsMunicipal')),
			'D006_Percentual_Diferimento_IBS_Municipal_Visual' => array('name' => 'D006_Percentual_Diferimento_IBS_Municipal_Visual', 'label' => '% Dif. IBS Mun.', 'type' => 'moeda', 'casasDecimais' => 2, 'readonly' => true, 'reject' => true, 'callbackPreencherValor' => $callbackReformaPreencherAliquota('D006_Classificacao_Tributaria_CBS_IBS', 'percentualDiferimentoIbsMunicipal')),
			'D006_Situacao_Tributaria_CBS_IBS_Regular' => array('name' => 'D006_Situacao_Tributaria_CBS_IBS_Regular', 'label' => 'CST CBS/IBS Reg.', 'type' => 'select', 'select' => $reformaTributaria->getCstsFormSelect(), 'callbackAoAlterar' => $callbackReformaAoAlterarCst('D006_Classificacao_Tributaria_CBS_IBS_Regular')),
			'D006_Classificacao_Tributaria_CBS_IBS_Regular' => array('name' => 'D006_Classificacao_Tributaria_CBS_IBS_Regular', 'label' => 'Classificação Trib. CBS/IBS Reg.', 'type' => 'select', 'select' => array(array('title' => '...', 'select' => '')), 'callbackPreencherSelect' => $callbackReformaPreencherSelectClassificacaoTributaria('D006_Situacao_Tributaria_CBS_IBS_Regular'), 'callbackAoAlterar' => $callbackReformaAoAlterarClassificacao($camposAliquotasReformaRegular)),
			'D006_Aliquota_CBS_Regular_Visual' => array('name' => 'D006_Aliquota_CBS_Regular_Visual', 'label' => '% CBS Reg.', 'type' => 'moeda', 'casasDecimais' => 2, 'readonly' => true, 'reject' => true, 'callbackPreencherValor' => $callbackReformaPreencherAliquota('D006_Classificacao_Tributaria_CBS_IBS_Regular', 'aliquotaCbs')),
			'D006_Percentual_Reducao_CBS_Regular_Visual' => array('name' => 'D006_Percentual_Reducao_CBS_Regular_Visual', 'label' => '% Red. CBS Reg.', 'type' => 'moeda', 'casasDecimais' => 2, 'readonly' => true, 'reject' => true, 'callbackPreencherValor' => $callbackReformaPreencherAliquota('D006_Classificacao_Tributaria_CBS_IBS_Regular', 'percentualReducaoCbs')),
			'D006_Percentual_Diferimento_CBS_Regular_Visual' => array('name' => 'D006_Percentual_Diferimento_CBS_Regular_Visual', 'label' => '% Dif. CBS Reg.', 'type' => 'moeda', 'casasDecimais' => 2, 'readonly' => true, 'reject' => true, 'callbackPreencherValor' => $callbackReformaPreencherAliquota('D006_Classificacao_Tributaria_CBS_IBS_Regular', 'percentualDiferimentoCbs')),
			'D006_Aliquota_IBS_UF_Regular_Visual' => array('name' => 'D006_Aliquota_IBS_UF_Regular_Visual', 'label' => '% IBS UF Reg.', 'type' => 'moeda', 'casasDecimais' => 2, 'readonly' => true, 'reject' => true, 'callbackPreencherValor' => $callbackReformaPreencherAliquota('D006_Classificacao_Tributaria_CBS_IBS_Regular', 'aliquotaIbsUf')),
			'D006_Percentual_Reducao_IBS_UF_Regular_Visual' => array('name' => 'D006_Percentual_Reducao_IBS_UF_Regular_Visual', 'label' => '% Red. IBS UF Reg.', 'type' => 'moeda', 'casasDecimais' => 2, 'readonly' => true, 'reject' => true, 'callbackPreencherValor' => $callbackReformaPreencherAliquota('D006_Classificacao_Tributaria_CBS_IBS_Regular', 'percentualReducaoIbsUf')),
			'D006_Percentual_Diferimento_IBS_UF_Regular_Visual' => array('name' => 'D006_Percentual_Diferimento_IBS_UF_Regular_Visual', 'label' => '% Dif. IBS UF Reg.', 'type' => 'moeda', 'casasDecimais' => 2, 'readonly' => true, 'reject' => true, 'callbackPreencherValor' => $callbackReformaPreencherAliquota('D006_Classificacao_Tributaria_CBS_IBS_Regular', 'percentualDiferimentoIbsUf')),
			'D006_Aliquota_IBS_Municipal_Regular_Visual' => array('name' => 'D006_Aliquota_IBS_Municipal_Regular_Visual', 'label' => '% IBS Mun. Reg.', 'type' => 'moeda', 'casasDecimais' => 2, 'readonly' => true, 'reject' => true, 'callbackPreencherValor' => $callbackReformaPreencherAliquota('D006_Classificacao_Tributaria_CBS_IBS_Regular', 'aliquotaIbsMunicipal')),
			'D006_Percentual_Reducao_IBS_Municipal_Regular_Visual' => array('name' => 'D006_Percentual_Reducao_IBS_Municipal_Regular_Visual', 'label' => '% Red. IBS Mun. Reg.', 'type' => 'moeda', 'casasDecimais' => 2, 'readonly' => true, 'reject' => true, 'callbackPreencherValor' => $callbackReformaPreencherAliquota('D006_Classificacao_Tributaria_CBS_IBS_Regular', 'percentualReducaoIbsMunicipal')),
			'D006_Percentual_Diferimento_IBS_Municipal_Regular_Visual' => array('name' => 'D006_Percentual_Diferimento_IBS_Municipal_Regular_Visual', 'label' => '% Dif. IBS Mun. Reg.', 'type' => 'moeda', 'casasDecimais' => 2, 'readonly' => true, 'reject' => true, 'callbackPreencherValor' => $callbackReformaPreencherAliquota('D006_Classificacao_Tributaria_CBS_IBS_Regular', 'percentualDiferimentoIbsMunicipal')),
			'D006_Flag_Nao_Somar_IBS_CBS_Valor_Total_Reforma' => array('name' => 'D006_Flag_Nao_Somar_IBS_CBS_Valor_Total_Reforma', 'label' => 'Não somar IBS/CBS no total reforma', 'type' => 'select', 'select' =>
				array(
					array('title' => 'Sim', 'value' => 'S'),
					array('title' => 'Não', 'value' => 'N'),
				)
			),
			'D006_Flag_Deduz_PIS_Base_IBS_CBS' => array('name' => 'D006_Flag_Deduz_PIS_Base_IBS_CBS', 'label' => 'Deduzir PIS da base', 'type' => 'select', 'select' =>
				array(
					array('title' => 'Sim', 'value' => 'S', 'selected' => true),
					array('title' => 'Não', 'value' => 'N'),
				)
			),
			'D006_Flag_Deduz_COFINS_Base_IBS_CBS' => array('name' => 'D006_Flag_Deduz_COFINS_Base_IBS_CBS', 'label' => 'Deduzir COFINS da base', 'type' => 'select', 'select' =>
				array(
					array('title' => 'Sim', 'value' => 'S', 'selected' => true),
					array('title' => 'Não', 'value' => 'N'),
				)
			),
			'D006_Flag_Deduz_ICMS_Base_IBS_CBS' => array('name' => 'D006_Flag_Deduz_ICMS_Base_IBS_CBS', 'label' => 'Deduzir ICMS próprio da base', 'type' => 'select', 'select' =>
				array(
					array('title' => 'Sim', 'value' => 'S', 'selected' => true),
					array('title' => 'Não', 'value' => 'N'),
				)
			),
			'D006_Flag_Deduz_DIFAL_Base_IBS_CBS' => array('name' => 'D006_Flag_Deduz_DIFAL_Base_IBS_CBS', 'label' => 'Deduzir partilha ICMS destino (DIFAL) da base', 'type' => 'select', 'select' =>
				array(
					array('title' => 'Sim', 'value' => 'S', 'selected' => true),
					array('title' => 'Não', 'value' => 'N'),
				)
			),
			'D006_Flag_Deduz_FCP_Base_IBS_CBS' => array('name' => 'D006_Flag_Deduz_FCP_Base_IBS_CBS', 'label' => 'Deduzir FCP próprio da base', 'type' => 'select', 'select' =>
				array(
					array('title' => 'Sim', 'value' => 'S', 'selected' => true),
					array('title' => 'Não', 'value' => 'N'),
				)
			),
			'D006_Flag_Deduz_FCP_Dest_Base_IBS_CBS' => array('name' => 'D006_Flag_Deduz_FCP_Dest_Base_IBS_CBS', 'label' => 'Deduzir partilha FCP destino da base', 'type' => 'select', 'select' =>
				array(
					array('title' => 'Sim', 'value' => 'S', 'selected' => true),
					array('title' => 'Não', 'value' => 'N'),
				)
			),
		];
		/* ============================================================== */

		// Campos gerais ICMS
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

		// UF 
		$ufs_     = gGeraSelect('D018', 'D018_UF', 'D018_UF,D018_Estado', 'order by D018_Id', true);
		$ufs      = $ufs_;
		$ufs[]    = array('title' => 'USAR UF DA VENDA', 'value' => 'VD');
		$arrayUFs = $ufs;

		$pulaLinha[] = array('title' => '','value' => '');
		$C004 =  mysql_fetch_assoc(mysql_query("SELECT C004_Flag_Empresa_Simples FROM C004 WHERE C004_iD='{$g['empresaAtual']}'"));
		if($C004['C004_Flag_Empresa_Simples'] == 1){
			// Se for do simples, posiciona as CST do simples no inicio
			$arrayCST    = array_merge($arraySimples,$pulaLinha,$arrayNormal);
		}else{
			// Se for regime normal, posiciona as CST do regime normal no inicio
			$arrayCST    = array_merge($arrayNormal,$pulaLinha,$arraySimples);
		}

		$formArray['campos']['icms']['D006_D059_Id'] = array('name' => 'D006_D059_Id', 'label' => 'Situação Tributária', 'type' => 'select',  
			'select' => $arrayCST			
		);	

		$formArray['campos']['icms']['D006_UF_ICMS_Substituicao_Devido'] = array('name' => 'D006_UF_ICMS_Substituicao_Devido', 'label' => 'UF para qual é devido o ICMS ST', 'type' => 'select',  
			'select' => $arrayUFs			
		);	

        $restricao = ($g['C004']['C004_Codigo_Regime_Tributario'] == '1' ? 'S' : 'N');
		if($restricao == 'N'){
			$formArray['campos']['icms']['D006_Motivo_Desoneracao_ICMS'] = array('name' => 'D006_Motivo_Desoneracao_ICMS', 'label' => 'Motivo desoneração', 'type' => 'select', 'select' =>
				array(
					array('value' => '', 'title' => '...'),
					array('value' => '1', 'title' => '1 - Táxi'),
					array('value' => '3', 'title' => '3 - Uso na agropecuária'),
					array('value' => '4', 'title' => '4 - Frotista/Locadora'),
					array('value' => '5', 'title' => '5 - Diplomático/Consular'),
					array('value' => '6', 'title' => '6 - Utilitários e Motocicletas da Amazônia Ocidental e Áreas de Livre Comércio'),
					array('value' => '7', 'title' => '7 - SUFRAMA'),
					array('value' => '8', 'title' => '8 - Venda a Órgão Público'),
					array('value' => '9', 'title' => '9 - Outros'),
					array('value' => '10', 'title' => '10 - Deficiente Condutor'),
					array('value' => '11', 'title' => '11 - Deficiente Não Condutor'),
					array('value' => '12', 'title' => '12 - Órgão de fomento e desenvolvimento agropecuário'),
				)
			);
		}

		if($g['C004']['C004_Codigo_Regime_Tributario'] != 3){
			// Simples nacional
			$formArray['campos']['icms']['D006_Flag_Isento_ICMS_Credito'] = array('name' => 'D006_Flag_Isento_ICMS_Credito', 'label' => 'Isento ICMS Crédito (Simples Nacional)', 'type' => 'select', 'select' =>
				array(
					array('title' => 'Sim', 'value' => 'S'),
					array('title' => 'Não', 'value' => 'N','selected' => true)
				)
			);
		}

		$form->definirFormularioArray($formArray);

	 	$camposMostrarICMS = array();
		$camposMostrarICMS['D006_Aliquota_ICMS'] = array('CST' => array(1, 2, 12, 3, 8, 10, 11, 14, 24));
		$camposMostrarICMS['D006_Percentual_FCP'] = array('CST' => array(1, 2, 12, 3, 8, 10, 11, 14, 24));
		$camposMostrarICMS['D006_ST_ICMS_Interno'] = array('CST' => array(1, 2, 3, 4, 12, 13, 3, 5, 6, 7, 8, 10, 11, 14, 15, 16, 17, 18, 19, 20, 21, 22, 24));
		$camposMostrarICMS['D006_Somar_FCP_ICMS_Interno'] = array('CST' => array(1, 2, 3, 4, 12, 5, 6, 7, 8, 10, 11, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 24));
		$camposMostrarICMS['D006_Percentual_Partilha_ICMS_Origem'] = array('CST' => array(1, 2, 3, 6, 12, 5, 13, 7, 8, 10, 11, 14, 15, 16, 17, 18, 19, 20, 21, 22, 24));
		$camposMostrarICMS['D006_Percentual_Partilha_ICMS_Destino'] = array('CST' => array(1, 2, 3, 6, 12, 5, 13, 7, 8, 10, 11, 14, 15, 16, 17, 18, 19, 20, 21, 22, 24));
		$camposMostrarICMS['D006_Flag_Endereco_Aliquota_Icms_Interno_Difal'] = array('CST' => array(1, 2, 3, 6, 12, 5, 13, 7, 8, 10, 11, 14, 15, 16, 17, 18, 19, 20, 21, 22, 24));
		$camposMostrarICMS['D006_Flag_Somar_Fator_ICMS_Dif_Base'] = array('CST' => array(1, 2, 3, 12, 13, 5, 6, 7, 8, 10, 11, 14, 15, 16, 17, 18, 19, 20, 21, 22, 24));
		$camposMostrarICMS['D006_Flag_Deb_Crd_ST'] = array('CST' => array(2, 12, 4, 13, 8, 9, 10, 11, 14, 18, 19, 20, 23, 24));
		$camposMostrarICMS['D006_Flag_Substituicao_Tributaria'] = array('CST' => array(2, 12, 4, 13, 8, 9, 10, 11, 14, 18, 19, 20, 23, 24));
		$camposMostrarICMS['D006_Flag_Deduz_FCP_ST_Valor_ST'] = array('CST' => array(2, 12, 4, 13, 8, 9, 10, 11, 14, 18, 19, 20, 23, 24));
		$camposMostrarICMS['D006_Flag_Somar_Despesas_Base_ST'] = array('CST' => array(1, 2, 12, 3, 4, 13, 8, 9, 10, 11, 14, 18, 19, 20, 23, 24));
		$camposMostrarICMS['D006_Flag_ST_VA'] = array('CST' => array(2, 12, 4, 13, 8, 9, 10, 11, 14, 18, 19, 20, 23, 24));
		$camposMostrarICMS['D006_Flag_Ajustar_MVA'] = array('CST' => array(2, 12, 4, 13, 8, 9, 10, 11, 14, 18, 19, 20, 23, 24));
		$camposMostrarICMS['D006_Reducao_MVA'] = array('CST' => array(2, 12, 4, 13, 8, 9, 10, 11, 14, 18, 19, 20, 23, 24));
		$camposMostrarICMS['D006_Percentual_Reducao_ICMS'] = array('CST' => array(2, 3, 12, 4, 8, 9, 10, 13, 11, 14, 18, 19, 20, 23, 24));
		$camposMostrarICMS['D006_ST_ICMS_Inter_Estadual'] = array('CST' => array(2, 3, 12, 4, 10, 11, 14, 18, 19, 20, 24));
		$camposMostrarICMS['D006_Flag_Reduz_Base_DIFAL'] = array('CST' => array(2, 3, 12, 4, 8, 9, 10, 13, 11, 14, 18, 19, 20, 23, 24));
		$camposMostrarICMS['D006_Percentual_Reducao_Base_DIFAL'] = array('CST' => array(2, 3, 12, 4, 8, 9, 10, 13, 11, 14, 18, 19, 20, 23, 24));
		$camposMostrarICMS['D006_Modalidade_ICMS_Substituicao'] = array('CST' => array(2, 12, 4, 10, 11, 14, 18, 19, 20, 24));
		$camposMostrarICMS['D006_ST_MVA'] = array('CST' => array(2, 12, 4, 13, 8, 9, 10, 11, 14, 18, 19, 20, 23, 24));											
		$camposMostrarICMS['D006_ST_ICMS_Interno_Reducao'] = array('CST' => array(2, 12, 4, 10, 11, 14, 18, 19, 20, 24));
		$camposMostrarICMS['D006_Percentual_Base_Operacao_Propria'] = array('CST' => array(12, 14));
		$camposMostrarICMS['D006_UF_ICMS_Substituicao_Devido'] = array('CST' => array(12, 14));
		$camposMostrarICMS['D006_Modalidade_ICMS'] = array('CST' => array(1, 2, 12, 3, 8, 10, 11, 14, 24));
		$camposMostrarICMS['D006_Motivo_Desoneracao_ICMS'] = array('CST' => array(3, 4, 5, 6, 7, 10, 11, 13, 14));
		$camposMostrarICMS['D006_Flag_Destaca_Reducao_ICMS_XML'] = array('CST' => array(8)); 

		/* Campos para NT 2019.001 - Criação e Atualização de Regras de Validação
		Versão 1.64 – Setembro de 2024, por solicitação da Smartness
		Gabriel Cegantini - 02/10/2025
		================================================================================ */
		$camposMostrarICMS['D006_Codigo_Beneficio_Reducao_Base_ICMS'] = array('CST' => array(8));
		/* ================================================================================ */

		$camposMostrarICMS = json_encode($camposMostrarICMS);
		
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {	
				
				mostrarOcultarCamposForm({$camposMostrarICMS}, '#{$g['divId']}', '#D006_D059_Id', 'CST');				
				
				$('#{$g['divId']} #D006_Flag_Gera_Contas').change(function() {
					if($(this).val() == 'P') {
						$('#{$g['divId']} #D006_D014_Id_Pesquisa').closest('.formEditor').show();
						$('#{$g['divId']} #D006_D073_Id_Pesquisa').closest('.formEditor').show();
					} else if($(this).val() == 'R') {
						$('#{$g['divId']} #D006_D014_Id_Pesquisa').closest('.formEditor').show();
						$('#{$g['divId']} #D006_D073_Id_Pesquisa').closest('.formEditor').hide();
					} else {
						$('#{$g['divId']} #D006_D014_Id_Pesquisa').closest('.formEditor').hide();
						$('#{$g['divId']} #D006_D073_Id_Pesquisa').closest('.formEditor').hide();
					}
				});
				$('#{$g['divId']} #D006_Flag_Gera_Contas').trigger('change');
			} </script></div>
EOT;
		
		/**
		* PARTE 3: Processamento do formulário (se foi enviado)
		* 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		*/
		// Se for update, passa essa informação para a classe formulário
		if (!empty($acaoId)) {
			$form->acaoId = $acaoId;
		}else{
			$form->campoSetarValor('D006_D059_Id', $D059_Id);
		}

		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				$g['conteudo']['pagina'] = gGeraAlertMsgErroForm($form);
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
			if (!empty($acaoId)) {
				$processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $acaoId);
				// mostra erros de formulário uteis ao desenvolvedor
				if ($processado !== true) {
					reset($form->formArray['campos']);
					if ($tab == key($form->formArray['campos'])) {
						echo  $processado;
					}
				}
				/**
				* Aqui todos o valor dos campos do formulário foram carregados (item 'value') se for update
				* Você pode utilizar ou modificar eles aqui (no sistema antigo é o 'if $operacao == 1')
				*/
				$form->campoSetarValor('D006_D059_Id', $D059_Id);
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
		if (!$formMultiTab) { $g['smarty']->assign('formId', $formId); }
		echo  $g['smarty']->fetch('formEditor.tpl');
	break;
	
	case 'fis001form02':
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($r_acaoId)) ? $r_acaoId : false;

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

		//Buscas destinos, foi centralizado nesta classe e vai valer para todos os locais
		$destinoProdutos   = $CAD002->retornaDestinoProdutos(false, 'V');

		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'D137';
		$form_campo_pk = 'D137_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'fis001FormRegra_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/fis/fis001/form/fis001form02/';

		// novo formulário
		$form = new Formulario('fis001FormRegra');

		//form editor
        $form_editor = array(
            'geral' => '{"D137_Origem_Estoque":{"top":0,"left":0,"width":23.882383999999995,"hidden":1},"D137_Flag_Origem_Pedido":{"top":0,"left":0,"width":119.88278399999999,"hidden":1},"D137_Flag_Preferencial_Sobre_D024_D006_Id":{"top":0,"left":0,"width":119.88278399999999,"hidden":1},"D137_Flag_Revenda_Consumidor":{"top":0,"left":0,"width":178,"hidden":1},"D137_D006_Id":{"top":6.9546661376953125,"left":231.41851806640625,"width":335,"hidden":0},"D137_C004_Id":{"top":29.978561401367188,"left":177.23651123046875,"width":335,"hidden":0},"D137_C004_Codigo_Regime_Tributario":{"top":52.98713684082031,"left":200.5667724609375,"width":335,"hidden":0},"D137_Empresas":{"top":75.9957275390625,"left":34.7579345703125,"width":328.039216,"hidden":0},"D137_D024_Codigo_Regime_Tributario":{"top":98.95834350585938,"left":140.64031982421875,"width":335,"hidden":0},"D137_Empresas_Destinatario":{"top":121.9669189453125,"left":4.58026123046875,"width":328.039216,"hidden":0},"D137_D024_Flag_Contribuinte":{"top":144.99081420898438,"left":138.80206298828125,"width":335,"hidden":0},"D137_Pessoa_Fisica_Juridica":{"top":167.96875,"left":124.1881103515625,"width":100,"hidden":0},"D137_Dentro_Fora_Estado":{"top":190.99264526367188,"left":191.83514404296875,"width":95,"hidden":0},"D137_Flag_Valida_Regra_Endereco_Entrega":{"top":190.99264526367188,"left":388.3118896484375,"width":95,"hidden":0},"D137_Estados_Especificos":{"top":213.98590087890625,"left":99.509765625,"width":328.039216,"hidden":0},"D137_Produzido_Adquirido":{"top":236.99447631835938,"left":203.3701171875,"width":335,"hidden":0},"D137_Origem_Mercadoria":{"top":259.9877624511719,"left":92.06494140625,"width":328.039216,"hidden":0},"D137_Flag_Destino_Produto":{"top":282.996337890625,"left":187.72979736328125,"width":335,"hidden":0},"D137_D060_Ids":{"top":305.9895935058594,"left":92.01898193359375,"width":328.039216,"hidden":0},"D137_D003_Ids":{"top":328.9981689453125,"left":88.14337158203125,"width":328.039216,"hidden":0},"D137_Peso_Minimo":{"top":351.991455078125,"left":65.42584228515625,"width":88.03921600000001,"hidden":0},"D137_Peso_Maximo":{"top":374.9693603515625,"left":62.791015625,"width":88.03921600000001,"hidden":0},"D137_UF_Tem_ST":{"top":397.9932861328125,"left":210.6004638671875,"width":96,"hidden":0},"D137_D005_Id":{"top":420.95587158203125,"left":242.003662109375,"width":335,"hidden":0},"D137_NCM":{"top":443.9951171875,"left":146.73712158203125,"height":62.039216,"width":328.039216,"hidden":0},"D137_NCM_Tem_ST":{"top":512.9901733398438,"left":199.21875,"width":96,"hidden":0},"D137_Flag_UF_Convenio_NCM_ST":{"top":535.998779296875,"left":140.74755859375,"width":96,"hidden":0},"D137_Flag_Inativar":{"top":558.9920654296875,"left":170.373779296875,"width":96,"hidden":0},"D137_ST_Retido":{"top":581.9852905273438,"left":219.66912841796875,"width":96,"hidden":0},"D137_Flag_Tipo_IPI":{"top":604.993896484375,"left":221.70648193359375,"width":96,"hidden":0}}',
        );
		
		
		$formArray = array(
			'campos' => array(
				'geral' => array(					
					'D137_D006_Id' => array('name' => 'D137_D006_Id', 'label'=>'CFOP','type' => 'select','select' =>
						gGeraSelect('D006', 'D006_Id', 'D006_Codigo_CFOP,D006_Descricao', 'WHERE D006_Flag_Inativo!="S" and D006_Flag_Entrada_Saida="S" order by D006_Codigo_CFOP', true)
					),
					'D137_Empresas' => array('name' => 'D137_Empresas','label'=>'IDs Empresas Emitentes <span style="font-size:10px;font-style:italic">(separar por vírgula)</span> (10)', 'type' => 'text', 'length' => array(30,40)),
					'D137_Empresas_Destinatario' => array('name' => 'D137_Empresas_Destinatario','label'=>'Códigos Destinatários (Cliente)<span style="font-size:10px;font-style:italic">(separar por vírgula)</span> (10)', 'type' => 'text', 'length' => array(30,500)),
					'D137_C004_Id' => array('name' => 'D137_C004_Id', 'label'=>'Empresa Emitente', 'type' => 'select','select' => 
						gGeraSelect('C004', 'C004_Id', 'C004_Nome_Abreviado', 'order by C004_Id', true)
					), 			
					'D137_Flag_Revenda_Consumidor' => array('name' => 'D137_Flag_Revenda_Consumidor','label'=>'Revenda/Consumidor<span style="font-size:10px;font-style:italic">(separar por vírgula)</span>', 'type' => 'text', 'length' => array(30,40)),
					'D137_Origem_Mercadoria' => array('name' => 'D137_Origem_Mercadoria','label'=>'Origens Produtos<span style="font-size:10px;font-style:italic"> (separar por vírgula)</span>', 'type' => 'text', 'length' => array(30,40)),
					'D137_C004_Codigo_Regime_Tributario' => array('name' => 'D137_C004_Codigo_Regime_Tributario','label'=>'CRT Emitente', 'type' => 'select', 
						'select' => array(
							array('title' => '...', 'value' => "", 'selected' => true),
							array('title' => '1 - Simples Nacional', 'value' => "1"),
							array('title' => '2 - Simples Nacional é excesso de sublimite de receita bruta', 'value' => "2"),
							array('title' => '3 - Regime Normal', 'value' => "3")
						)
					),
					'D137_D024_Flag_Contribuinte' => array('name' => 'D137_D024_Flag_Contribuinte', 'label' => 'Tipo Destinatário (Cliente)', 'type' => 'select', 'select' => 
						array(
							array('title' => '...', 'value' => "", 'selected' => true),
							array('title' => '0 - Não contribuinte', 'value' => '9'),   
							array('title' => '1 - Consumidor Final', 'value' => '1'),   
							array('title' => '2 - Revenda', 'value' => '2'),
							array('title' => '3 - Produtor rural', 'value' => '3'),
							array('title' => '4 - Simples', 'value' => '4'),
							array('title' => '5 - Industria', 'value' => '5'),
							array('title' => '8 - Industria Triangular', 'value' => '8')   
						)
					),
					'D137_Produzido_Adquirido' => array('name' => 'D137_Produzido_Adquirido','label'=>'Tipo Produto', 'type' => 'select', 
						'select' => array(
							array('title' => '...', 'value' => "", 'selected' => true),
							array('title' => 'PRODUZIDO', 'value' => "PD"),
							array('title' => 'COMPRADO', 'value' => "CP"),
							array('title' => 'MONTADO', 'value' => "MT"),
							array('title' => 'MODIFICADO', 'value' => "MD"),
						)
					),						
					'D137_Estados_Especificos' => array('name' => 'D137_Estados_Especificos','label'=>'UFs Específicas<span style="font-size:10px;font-style:italic"> (separar por vírgula)</span>', 'type' => 'text', 'length' => array(100,100)),
					'D137_Dentro_Fora_Estado' => array('name' => 'D137_Dentro_Fora_Estado','label'=>'Dentro/Fora UF', 'type' => 'select', 
						'select' => array(
							array('title' => '...', 'value' => "", 'selected' => true),
							array('title' => 'DENTRO', 'value' => "D"),
							array('title' => 'FORA', 'value' => "F"),
						)
					),
					'D137_Flag_Valida_Regra_Endereco_Entrega' => array('name' => 'D137_Flag_Valida_Regra_Endereco_Entrega_Entrega','label'=>'Valida pelo End Entrega.', 'type' => 'select', 
						'select' => array(
							array('title' => '...', 'value' => "", 'selected' => true),
							array('title' => 'SIM', 'value' => "S"),
						)
					),																																										
					'D137_UF_Tem_ST' => array('name' => 'D137_UF_Tem_ST','label'=>'UF tem ST?', 'type' => 'select', 
						'select' => array(
							array('title' => '...', 'value' => "", 'selected' => true),
							array('title' => 'SIM', 'value' => "S"),
							array('title' => 'NÃO', 'value' => "N"),
						)
					),		
					'D137_D005_Id' => array('name' => 'D137_D005_Id', 'label'=>'NCM','type' => 'select','select' => 
						gGeraSelect('D005', 'D005_Id', 'D005_Classificacao_Fiscal,D005_Observacao', '', true)
					), 			
					'D137_NCM_Tem_ST' => array('name' => 'D137_NCM_Tem_ST','label'=>'NCM tem ST?', 'type' => 'select', 
						'select' => array(
							array('title' => '...', 'value' => "", 'selected' => true),
							array('title' => 'SIM', 'value' => "S"),
							array('title' => 'NÃO', 'value' => "N"),
						)
					),		
					'D137_Flag_UF_Convenio_NCM_ST' => array('name' => 'D137_Flag_UF_Convenio_NCM_ST','label'=>'Usar NCM - UF ST Venda?', 'type' => 'select', 
						'select' => array(
							array('title' => '...', 'value' => "", 'selected' => true),
							array('title' => 'SIM', 'value' => "S"),
							array('title' => 'NÃO', 'value' => "N"),
						)
					),		
					'D137_Pessoa_Fisica_Juridica' => array('name' => 'D137_Pessoa_Fisica_Juridica','label'=>'Pessoa Destinatário (Cliente)', 'type' => 'select', 
						'select' => array(
							array('title' => '...', 'value' => "", 'selected' => true),
							array('title' => 'FÍSICA', 'value' => "PF"),
							array('title' => 'JURÍDICA', 'value' => "PJ"),
                            array('title' => 'INTERNACIONAL', 'value' => "PI"),
						)
					),	
					'D137_Flag_Cabecalho_Item' => array('name' => 'D137_Flag_Cabecalho_Item','label'=>'Tipo Regra', 'type' => 'hidden', 'value' => 'I'),
					'D137_Flag_Inativar' => array('name' => 'D137_Flag_Inativar','label'=>'Inativar esta Regra?', 'type' => 'select', 
						'select' => array(
							array('title' => 'NÃO', 'value' => "N", 'selected' => true),
							array('title' => 'SIM', 'value' => "S"),
						)
					),																						
					'D137_Flag_Preferencial_Sobre_D024_D006_Id' => array('name' => 'D137_Flag_Preferencial_Sobre_D024_D006_Id','label'=>'Preferencial sobre CFOP Cliente', 'type' => 'select', 
						'select' => array(
							array('title' => 'NÃO', 'value' => "N", 'selected' => true),
							array('title' => 'SIM', 'value' => "S"),
						)
					),																						
					'D137_Peso_Minimo' => array('name' => 'D137_Peso_Minimo', 'label'=>'Peso Unitário Líquido Mínimo Kg Produto', 'type' => 'moeda', 'casasDecimais' => '3', 'length' => array(5,5)),
					'D137_Peso_Maximo' => array('name' => 'D137_Peso_Maximo', 'label'=>'Peso Unitário Líquido Máximo Kg Produto', 'type' => 'moeda', 'casasDecimais' => '3', 'length' => array(5,5)),
					'D137_NCM' => array('name' => 'D137_NCM', 'label'=>'NCMs<span style="font-size:10px;font-style:italic"> (separar por vírgula)</span>', 'type' => 'textarea','length' => array(2,35)),
					'D137_Flag_Destino_Produto' => array('name' => 'D137_Flag_Destino_Produto','label'=>'Destino Produto', 'type' => 'select', 
						'select' => $destinoProdutos
					),
					'D137_Flag_Origem_Pedido' => array('name' => 'D137_Flag_Origem_Pedido', 'label'=>'Origem pedido (5)', 'type' => 'select', 
						'select' => array(
							array('title' => '...', 'value' => "", 'selected' => true),
							array('title' => 'EXTERNO', 'value' => "E"),
							array('title' => 'INTERNO', 'value' => "I"),
						)
					),
					'D137_D024_Codigo_Regime_Tributario' => array('name' => 'D137_D024_Codigo_Regime_Tributario','label'=>'CRT Destinatário (Cliente)', 'type' => 'select', 
						'select' => array(
							array('title' => '...', 'value' => "", 'selected' => true),
							array('title' => '1 - Simples Nacional', 'value' => "1"),
							array('title' => '2 - Simples Nacional é excesso de sublimite de receita bruta', 'value' => "2"),
							array('title' => '3 - Regime Normal', 'value' => "3")
						)
					),
					'D137_ST_Retido' => array('name' => 'D137_ST_Retido', 'label'=>'ST Retido', 'type' => 'select', 
						'select' => array(
							array('title' => '...', 'value' => "", 'selected' => true),
							array('title' => 'NÃO', 'value' => "N"),
							array('title' => 'SIM', 'value' => "S")
						)
					),
					'D137_Flag_Tipo_IPI' => array('name' => 'D137_Flag_Tipo_IPI', 'label'=>'Tipo (IPI)', 'type' => 'select', 
						'select' => array(
							array('title' => '...', 'value' => "", 'selected' => true),
							array('title' => 'PRODUZIDO', 'value' => "1"),
							array('title' => 'ADQUIRIDO', 'value' => "2")
						)
					),
					'D137_D060_Ids' => array('name' => 'D137_D060_Ids','label'=>'Ids Setor Produto<span style="font-size:10px;font-style:italic"> (separar por vírgula)</span>', 'type' => 'text', 'length' => array(100,100)),
					'D137_D003_Ids' => array('name' => 'D137_D003_Ids','label'=>'Ids Grupo Produto<span style="font-size:10px;font-style:italic"> (separar por vírgula)</span>', 'type' => 'text', 'length' => array(100,100)),
				)
			)
		);

		if($g['C031']['venderEstoqueMatriz'] == 'S'){
			$formArray['campos']['geral']['D137_Origem_Estoque'] = array('name' => 'D137_Origem_Estoque','label'=>'Origem Estoque', 'type' => 'select', 
				'select' => array(
					array('title' => '...', 'value' => "", 'selected' => true),
					array('title' => 'DENTRO DA EMPRESA', 'value' => "D"),
					array('title' => 'FORA DA EMPRESA', 'value' => "F"),
				)
			);
		}

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
					$verificacao=$FIS001->verificaCampoEstados($form);
					if ($verificacao===true){
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
					}else{
						echo gGeraAlertMsg('Erro', 'Campo "Estados Específicos" preenchido de forma incorreta<br />Preencha com as siglas dos estados separados por vírgulas, ex: PR,SC,RS');
					}
				} else {
					// Insert
					$verificacao=$FIS001->verificaCampoEstados($form);
					if ($verificacao===true){
						if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
							// Imprime uma mensagem e fecha a janela
							$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
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
					}else{
						echo gGeraAlertMsg('Erro', 'Campo "Estados Específicos" preenchido de forma incorreta<br />Preencha com as siglas dos estados separados por vírgulas, ex: PR,SC,RS');
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
	
	case 'fis001form02Compras':
			/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($r_acaoId)) ? $r_acaoId : false;

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

		//Buscas destinos, foi centralizado nesta classe e vai valer para todos os locais
		$destinoProdutos   = $CAD002->retornaDestinoProdutos(false, 'C');

		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'D145';
		$form_campo_pk = 'D145_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'fis001FormRegra_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/fis/fis001/form/fis001form02Compras/';

		// novo formulário
		$form = new Formulario('fis001FormRegra');

		//form editor
        $form_editor = array(
			'geral' => '{"D145_D006_Id":{"top":6.92401123046875,"left":206.8780517578125,"width":200,"hidden":0},"D145_Flag_Revenda_Consumidor":{"top":6.9393310546875,"left":516.9730224609375,"width":178.039216,"hidden":1},"D145_C004_Codigo_Regime_Tributario":{"top":29.932586669921875,"left":148.1617431640625,"width":200,"hidden":0},"D145_Empresas":{"top":29.947906494140625,"left":503.9981689453125,"width":178.039216,"hidden":1},"D145_D024_Flag_Contribuinte":{"top":52.95648193359375,"left":150.3216552734375,"width":200,"hidden":0},"D145_Produzido_Adquirido":{"top":75.98037719726562,"left":171.36944580078125,"width":303,"hidden":0},"D145_C004_Id":{"top":75.98037719726562,"left":687.9595947265625,"width":96,"hidden":1},"D145_Estados_Especificos":{"top":98.94302368164062,"left":13.35784912109375,"width":232.039216,"hidden":0},"D145_Flag_UF_Convenio_NCM_ST":{"top":98.94302368164062,"left":605.958984375,"width":100,"hidden":1},"D145_Dentro_Fora_Estado":{"top":121.9669189453125,"left":139.07781982421875,"width":100,"hidden":0},"D145_UF_Tem_ST":{"top":144.96017456054688,"left":178.599853515625,"width":100,"hidden":0},"D145_D005_Id":{"top":167.96875,"left":210.0030517578125,"width":200,"hidden":0},"D145_NCM_Tem_ST":{"top":190.96200561523438,"left":167.2181396484375,"width":100,"hidden":0},"D145_Pessoa_Fisica_Juridica":{"top":213.92462158203125,"left":198.31494140625,"width":100,"hidden":0},"D145_Peso_Minimo":{"top":213.92462158203125,"left":766.9730224609375,"width":53.039215999999996,"hidden":1},"D145_Flag_Inativar":{"top":236.97915649414062,"left":198.3455810546875,"width":100,"hidden":0},"D145_Flag_Preferencial_Sobre_D024_D006_Id":{"top":259.972412109375,"left":78.8143310546875,"width":100,"hidden":0},"D145_Peso_Maximo":{"top":259.95709228515625,"left":826.9608154296875,"width":53.039215999999996,"hidden":1},"D145_Origem_Mercadoria":{"top":282.9809875488281,"left":53.5079345703125,"width":232.039216,"hidden":0},"D145_Empresas_Destinatario":{"top":305.9436340332031,"left":141.513427734375,"width":232.039216,"hidden":0},"D145_Empresas_Destinatario_Ignorar":{"top":328.9828186035156,"left":96.323486328125,"width":232.039216,"hidden":0},"D145_Codigo_Regime_Tributario":{"top":351.9761047363281,"left":61.01409912109375,"width":232.039216,"hidden":0},"D145_NCM":{"top":374.9693298339844,"left":122.8553466796875,"height":58.039215999999996,"width":232.039216,"hidden":0},"D145_Flag_Destino_Produto":{"top":443.9644470214844,"left":155.7291259765625,"width":239,"hidden":0},"D145_D060_Ids":{"top":466.9270935058594,"left":60.01837158203125,"width":232.039216,"hidden":0},"D145_D003_Ids":{"top":489.9815979003906,"left":56.14276123046875,"width":232.039216,"hidden":0}}',
        );
		
		
		$formArray = array(
			'campos' => array(
				'geral' => array(					
					'D145_D006_Id' => array('name' => 'D145_D006_Id', 'label'=>'CFOP', 'extra'=>'style="width:200px"','type' => 'select','select' => 
						gGeraSelect('D006', 'D006_Id', 'D006_Codigo_CFOP,D006_Descricao', 'WHERE D006_Flag_Inativo="N" AND D006_Flag_Entrada_Saida="E" order by D006_Codigo_CFOP', true)
					), 			
					'D145_Empresas' => array('name' => 'D145_Empresas','label'=>'ID das empresas origem<span style="font-size:10px;font-style:italic">(separar por vírgula)</span>', 'type' => 'text', 'length' => array(30,40)),
					'D145_Empresas_Destinatario' => array('name' => 'D145_Empresas_Destinatario','label'=>'Código Fornecedor', 'type' => 'text', 'length' => array(30,40)),
					'D145_Empresas_Destinatario_Ignorar' => array('name' => 'D145_Empresas_Destinatario_Ignorar','label'=>'Código Fornecedor não usar', 'type' => 'text', 'length' => array(30,40)),
					'D145_C004_Id' => array('name' => 'D145_C004_Id', 'label'=>'Empresa', 'type' => 'select','select' => 
						gGeraSelect('C004', 'C004_Id', 'C004_Nome_Abreviado', 'order by C004_Id', true)
					),
					'D145_Flag_Revenda_Consumidor' => array('name' => 'D145_Flag_Revenda_Consumidor','label'=>'Revenda/Consumidor<span style="font-size:10px;font-style:italic">(separar por vírgula)</span>', 'type' => 'text', 'length' => array(30,40)),
					'D145_Origem_Mercadoria' => array('name' => 'D145_Origem_Mercadoria','label'=>'Origem Mercadoria<span style="font-size:10px;font-style:italic">(separar por vírgula)</span>', 'type' => 'text', 'length' => array(30,40)),
					'D145_C004_Codigo_Regime_Tributario' => array('name' => 'D145_C004_Codigo_Regime_Tributario','label'=>'Regime Tributário','extra' =>'style="width:200px"', 'type' => 'select', 
						'select' => array(
							array('title' => '...', 'value' => "", 'selected' => true),
							array('title' => '1 - Simples Nacional', 'value' => "1"),
							array('title' => '2 - Simples Nacional é excesso de sublimite de receita bruta', 'value' => "2"),
							array('title' => '3 - Regime Normal', 'value' => "3")
						)
					),			
					'D145_D024_Flag_Contribuinte' => array('name' => 'D145_D024_Flag_Contribuinte', 'label' => 'Tipo Contribuinte','extra' =>'style="width:200px"', 'type' => 'select', 'select' => 
						array(
							array('title' => '...', 'value' => "", 'selected' => true),
							array('title' => 'Não contribuinte', 'value' => '9'),   
							array('title' => 'Consumidor Final', 'value' => '1'),   
							array('title' => 'Revenda', 'value' => '2'),
							array('title' => 'Produtor rural', 'value' => '3'),
							array('title' => 'Simples', 'value' => '4'),
							array('title' => 'Industria', 'value' => '5'),
							array('title' => 'Industria Triangular', 'value' => '8')   
						)
					),
					'D145_Produzido_Adquirido' => array('name' => 'D145_Produzido_Adquirido','label'=>'Tipo Produto','extra' =>'style="width:200px"', 'type' => 'select', 
						'select' => array(
							array('title' => '...', 'value' => "", 'selected' => true),
							array('title' => 'PRODUZIDO/IMPORTADO', 'value' => "P"),
							array('title' => 'AQUISIÇÃO NACIONAL', 'value' => "A"),
						)
					),						
					'D145_Estados_Especificos' => array('name' => 'D145_Estados_Especificos','label'=>'UF Específicos Destinatário<span style="font-size:10px;font-style:italic">(separar por vírgula)</span>', 'type' => 'text', 'length' => array(100,100)),
					'D145_Dentro_Fora_Estado' => array('name' => 'D145_Dentro_Fora_Estado','label'=>'Dentro/Fora Estado','extra' =>'style="width:100px"', 'type' => 'select', 
						'select' => array(
							array('title' => '...', 'value' => "", 'selected' => true),
							array('title' => 'DENTRO', 'value' => "D"),
							array('title' => 'FORA', 'value' => "F"),
						)
					),																																								
					'D145_UF_Tem_ST' => array('name' => 'D145_UF_Tem_ST','label'=>'UF tem ST?','extra' =>'style="width:100px"', 'type' => 'select', 
						'select' => array(
							array('title' => '...', 'value' => "", 'selected' => true),
							array('title' => 'SIM', 'value' => "S"),
							array('title' => 'NÃO', 'value' => "N"),
						)
					),		
					'D145_D005_Id' => array('name' => 'D145_D005_Id', 'label'=>'NCM', 'extra'=>'style="width:200px"','type' => 'select','select' =>
                            gGeraSelect('D005', 'D005_Id', 'D005_Classificacao_Fiscal,D005_Flag_Aplicacao,D005_Observacao', 'order by D005_Classificacao_Fiscal', true)
                    ), 			
					'D145_NCM_Tem_ST' => array('name' => 'D145_NCM_Tem_ST','label'=>'NCM tem ST?','extra' =>'style="width:100px"', 'type' => 'select', 
						'select' => array(
							array('title' => '...', 'value' => "", 'selected' => true),
							array('title' => 'SIM', 'value' => "S"),
							array('title' => 'NÃO', 'value' => "N"),
						)
					),		
					'D145_Flag_UF_Convenio_NCM_ST' => array('name' => 'D145_Flag_UF_Convenio_NCM_ST','label'=>'Convênio NCM/UF ST?','extra' =>'style="width:100px"', 'type' => 'select', 
						'select' => array(
							array('title' => '...', 'value' => "", 'selected' => true),
							array('title' => 'SIM', 'value' => "S"),
							array('title' => 'NÃO', 'value' => "N"),
						)
					),		
					'D145_Pessoa_Fisica_Juridica' => array('name' => 'D145_Pessoa_Fisica_Juridica','label'=>'Pessoa','extra' =>'style="width:100px"', 'type' => 'select', 
						'select' => array(
							array('title' => '...', 'value' => "", 'selected' => true),
							array('title' => 'FÍSICA', 'value' => "PF"),
							array('title' => 'JURÍDICA', 'value' => "PJ"),
                            array('title' => 'INTERNACIONAL', 'value' => "PI"),
						)
					),	
					'D145_Flag_Cabecalho_Item' => array('name' => 'D145_Flag_Cabecalho_Item','label'=>'Tipo Regra','extra' =>'style="width:100px"', 'type' => 'hidden', 'value' => 'I'),						
					'D145_Flag_Inativar' => array('name' => 'D145_Flag_Inativar','label'=>'Inativar','extra' =>'style="width:100px"', 'type' => 'select', 
						'select' => array(
							array('title' => 'NÃO', 'value' => "N", 'selected' => true),
							array('title' => 'SIM', 'value' => "S"),
						)
					),																						
					'D145_Flag_Preferencial_Sobre_D024_D006_Id' => array('name' => 'D145_Flag_Preferencial_Sobre_D024_D006_Id','label'=>'Preferencial sobre CFOP Cliente','extra' =>'style="width:100px"', 'type' => 'select', 
						'select' => array(
							array('title' => 'NÃO', 'value' => "N", 'selected' => true),
							array('title' => 'SIM', 'value' => "S"),
						)
					),																						
					'D145_Peso_Minimo' => array('name' => 'D145_Peso_Minimo', 'label'=>'Peso mínimo Kg', 'type' => 'moeda', 'casasDecimais' => '3', 'length' => array(5,5)),
					'D145_Peso_Maximo' => array('name' => 'D145_Peso_Maximo', 'label'=>'Peso máximo Kg', 'type' => 'moeda', 'casasDecimais' => '3', 'length' => array(5,5)),
					'D145_NCM' => array('name' => 'D145_NCM', 'label'=>'NCM<span style="font-size:10px;font-style:italic">(separar por vírgula)</span>', 'type' => 'textarea','length' => array(2,35)),
					'D145_Codigo_Regime_Tributario' => array('name' => 'D145_Codigo_Regime_Tributario','label'=>'Regime Tributário<span style="font-size:10px;font-style:italic">(separar por vírgula)</span>', 'type' => 'text', 'length' => array(30,40)),
					'D145_Flag_Destino_Produto' => array('name' => 'D145_Flag_Destino_Produto','label'=>'Destino Produto', 'type' => 'select', 
						'select' => $destinoProdutos
					),
					'D145_D060_Ids' => array('name' => 'D145_D060_Ids','label'=>'Ids Setor Produto<span style="font-size:10px;font-style:italic"> (separar por vírgula)</span>', 'type' => 'text', 'length' => array(100,100)),
					'D145_D003_Ids' => array('name' => 'D145_D003_Ids','label'=>'Ids Grupo Produto<span style="font-size:10px;font-style:italic"> (separar por vírgula)</span>', 'type' => 'text', 'length' => array(100,100)),
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
					$verificacao=$FIS001->verificaCampoEstados($form);
					if ($verificacao===true){
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
					}else{
						echo gGeraAlertMsg('Erro', 'Campo "Estados Específicos" preenchido de forma incorreta<br />Preencha com as siglas dos estados separados por vírgulas, ex: PR,SC,RS');
					}
				} else {
					// Insert
					$verificacao=$FIS001->verificaCampoEstados($form);
					if ($verificacao===true){
						if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
							// Imprime uma mensagem e fecha a janela
							$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
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
					}else{
						echo gGeraAlertMsg('Erro', 'Campo "Estados Específicos" preenchido de forma incorreta<br />Preencha com as siglas dos estados separados por vírgulas, ex: PR,SC,RS');
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
	
}

?>
