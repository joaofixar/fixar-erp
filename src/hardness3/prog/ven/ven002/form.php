<?php
namespace hardness;
require_once('prog/ven/ven002/form_func-js.php');

require_once("old/lib/funcoes.php");

//Classes
require_once("bibliotecas/classes/VEN002.php");
$VEN002 = new VEN002();
require_once("bibliotecas/classes/VEN001.php");
$VEN001 = new VEN001();
require_once("bibliotecas/classes/CAD002.php");
$CAD002 = new CAD002();
/**
 * Ações
 */
switch ($g['r'][3]) {
	// Rota própria: personalização do formulário e recarga independentes da grade.
	case 'ven002FormNFDebitoCredito':
			$T005_Id = isset($_REQUEST['T005_Id']) ? (int) $_REQUEST['T005_Id'] : 0;
			$sqlPedido = mysql_query("SELECT T005_Id, T005_D024_Id, T005_D006_Id,
										 D024_Nome_Empresa
								   FROM T005
								   LEFT JOIN D024 ON D024_Id = T005_D024_Id
								  WHERE T005_Id = '{$T005_Id}' AND T005_C004_Id = '{$g['empresaAtual']}'");
			$pedidoNFDebitoCredito = $sqlPedido ? mysql_fetch_assoc($sqlPedido) : array();

			if (empty($pedidoNFDebitoCredito['T005_Id'])) {
				echo gGeraAlertMsg('Erro', 'Pedido não encontrado.');
				break;
			}

			$clientePorEmpresaNFDC = '';
			if ($g['C031']['separarClientePorEmpresa'] == 'S') {
				$clientePorEmpresaNFDC = "AND (IFNULL(D024_C004_Id,0) = '{$g['empresaAtual']}' OR IFNULL(D024_C004_Id,0) = 0)";
			}

			$opcoesCFOPArray = gGeraSelect(
				'D006',
				'D006_Id',
				'D006_Codigo_CFOP,D006_Descricao',
				'WHERE IFNULL(D006_Flag_Inativo,"N") != "S" ORDER BY D006_Codigo_CFOP',
				true,
				$pedidoNFDebitoCredito['T005_D006_Id']
			);

			$tab = 'principal';
			$formId = 'formVen002NFDebitoCredito_' . uniqid();
			$form_tabela = 'D000';
			$form_campo_pk = 'D000_Id';
			// Mantém os dados de cabeçalho em uma sequência de leitura única e
			// aproxima a ação do percentual que ela aplica na grade abaixo.
			$form_editor = array(
				$tab => '{"tipoOperacaoNFDC":{"top":30,"left":39,"width":220,"hidden":0},"tipoFiscalNFDC":{"top":30,"left":343,"width":400,"hidden":0},"clienteNFDC":{"top":53,"left":40,"width":681,"hidden":0},"cfopNFDC":{"top":76,"left":39,"width":703,"hidden":0},"ven002PreencherPercentualNFDC":{"top":97,"left":250,"width":null,"hidden":0},"percentualNFDC":{"top":100,"left":19,"width":80,"hidden":0}}'
			);
			$form = new Formulario('formVen002NFDebitoCredito');
			$form->definirFormularioArray(array(
				'campos' => array(
					$tab => array(
						'tipoOperacaoNFDC' => array(
							'name' => 'tipoOperacaoNFDC',
							'label' => 'Operação *',
							'type' => 'select',
							'select' => array(array('title' => 'NF de Débito', 'value' => 'debito', 'selected' => true)),
							'extra' => 'disabled="disabled"'
						),
						'clienteNFDC' => array(
							'name' => 'clienteNFDC',
							'label' => 'Cliente *',
							'type' => 'pesquisa',
							'reject' => true,
							'pesquisa' => array(
								'D024',
								'D024_Id',
								'D024_Nome_Empresa,D020_Nome_Cidade,D018_UF,D024_Cpf,D024_Cnpj,D024_DDD_Telefone_1,D024_Telefone_1,D024_Nome_Fantasia,D024_Id',
								'LEFT JOIN D020 ON D020_Id=D024_D020_Id LEFT JOIN D018 ON D018_Id=D024_D018_Id WHERE D024_Flag_Cliente="S" ' . $clientePorEmpresaNFDC . ' AND D024_Flag_Ativo="S" ORDER BY D024_Nome_Empresa',
								'%1 - %9 - (%4%5) - [%2/%3]'
							),
							'length' => array(80, 30)
						),
						'cfopNFDC' => array(
							'name' => 'cfopNFDC',
							'label' => 'CFOP *',
							'type' => 'select',
							'select' => $opcoesCFOPArray
						),
						'tipoFiscalNFDC' => array(
							'name' => 'tipoFiscalNFDC',
							'label' => 'Tipo fiscal *',
							'type' => 'select',
							'select' => array(array('title' => '06 - Pagamento antecipado', 'value' => '06', 'selected' => true)),
							'extra' => 'disabled="disabled"'
						),
						'percentualNFDC' => array(
							'name' => 'percentualNFDC',
							'label' => 'Percentual do saldo (%)',
							'type' => 'moeda',
							'casasDecimais' => 2,
							'value' => '100,00',
							'length' => array(8, 8)
						),
						'ven002PreencherPercentualNFDC' => array(
							'name' => 'ven002PreencherPercentualNFDC',
							'label' => '',
							'type' => 'button',
							'value' => 'Preencher quantidades',
							'reload' => false,
							'reject' => true
						)
					)
				)
			));
			$form->campoSetarValor('clienteNFDC', (int) $pedidoNFDebitoCredito['T005_D024_Id']);
			$form->campoSetarValorPesquisa('clienteNFDC', htmlspecialchars($pedidoNFDebitoCredito['D024_Nome_Empresa'], ENT_QUOTES, 'UTF-8'));
			$gerado = gGeraCamposForm($form, $tab, false, '', '', $formId, $form_campo_pk, false, false, false, false);
			$g['smarty']->assign('formularioID', $form->formularioID());
			$g['smarty']->assign('table', $gerado['table']);
			$g['smarty']->assign('form_tabela', $form_tabela);
			$g['smarty']->assign('formId', $formId);
			echo '<style>
/* Rótulos com largura fixa deixam os controles da NF alinhados por coluna. */
#' . $formId . ' .formFormatoEditor {
	height: 126px;
	box-sizing: border-box;
	border: 1px solid #c7cdd2;
	border-radius: 3px;
	background: #f5f6f7;
}
#' . $formId . ' .formEditor {
	display: flex;
	align-items: center;
}
#' . $formId . ' .formEditor > div {
	float: none !important;
}
#' . $formId . ' .formEditor > div:first-child {
	flex: 0 0 105px;
	width: 105px;
	margin-top: 0 !important;
	padding-right: 6px;
	box-sizing: border-box;
	text-align: right;
}
#' . $formId . ' .formEditor-percentualNFDC > div:first-child {
	flex-basis: 125px;
	width: 125px;
}
#' . $formId . ' .formEditor-ven002PreencherPercentualNFDC {
	display: block;
}
#' . $formId . ' .formEditor-ven002PreencherPercentualNFDC > div:first-child {
	display: block;
	width: auto;
	margin: 0 !important;
	padding: 0;
}
</style>';
			echo '<div class="alert alert-info">A NF de Débito 06 será criada em elaboração para revisão no Fiscal. O pedido continuará disponível para o faturamento da venda.</div>';
			echo $gerado['pagina'];
			echo $g['smarty']->fetch('formEditor.tpl');
			echo '<div class="buttonsBottom" id="btns-' . $g['divId'] . '"><button type="button" id="ven002ValidarNFDebitoCredito">Gerar NF</button><button type="button" onclick="$(this).closest(\'.ui-dialog\').find(\'.ui-dialog-content\').dialog(\'close\');">Fechar</button></div>';
	break;

	/**
	* Form Principal
	* Chamado em: ven002tab02
	*/
	case 'ven002form01':

		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$r_T005_Id = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;

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
		
		// busca o ID do cliente pra carregar os contatos, caso seja edição
		$idCliente='99999999999';
		if ($acaoId>0){
			$sql = "SELECT T005_D024_Id,
                               T005A_Flag_Pagamento_Antecipado,
			               D020_D022_Id,
						   D022_D036_Id, 
						   D024_D022_Id, 
						   D024_D020_Id, 
						   T005_C007_Id_Vendedor_Interno, 
						   T005_C007_Id_Vendedor_Externo,
						   C007_C007_Id,
						   C007_C007_Id_2,
						   C007_Flag_Ausente
				 	  FROM T005 
				 LEFT JOIN T005A ON T005A_T005_Id=T005_Id
				 LEFT JOIN D024 ON D024_Id=T005_D024_Id
				 LEFT JOIN D020 ON D020_Id=D024_D020_Id 
				 LEFT JOIN D022 ON D022_Id=D020_D022_Id 
				 LEFT JOIN D036 ON D036_Id=D022_D036_Id 
				 LEFT JOIN C007 ON C007_Id=T005_C007_Id_Vendedor_Interno
				     WHERE T005_Id = '{$acaoId}'";
			$result = mysql_query($sql);
			$aD024 = mysql_fetch_assoc($result);		
			$idTransportadoraCidade=$aD024['D020_D022_Id'];
			$idCliente=$aD024['T005_D024_Id'];
			$idTransportadora=$aD024['D024_D022_Id'];
            $idTipoTransp=$aD024['D022_D036_Id'];
            $idVendedorInterno=$aD024['T005_C007_Id_Vendedor_Interno'];
			$idVendedorExterno=$aD024['T005_C007_Id_Vendedor_Externo'];
		}
		if (isset($r_form_ven002_form1)) {
			$idCliente = $r_form_ven002_form1['T005_D024_Id'];
		}

		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'T005';
		$form_campo_pk = 'T005_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = array('ven002FormCabecalhoBtnAdd', 'ven002FormCabecalhoBtnCln', 'ven002FormCabecalhoBtnNew');
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/ven/ven002/form/ven002form01/';
		
		$form_editor = array(
			'geral'=>'{"T005_C004_Id_Estoque":{"top":0,"left":0,"width":23.882383999999995,"hidden":1},"T005_D116_Id":{"top":0,"left":0,"width":23.882383999999995,"hidden":1},"T005_Peso_Bruto":{"top":0,"left":0,"width":0,"hidden":1},"T005_Peso_Liquido":{"top":0,"left":0,"width":0,"hidden":1},"T005_Especie":{"top":0,"left":0,"width":0,"hidden":1},"T005_Quantidade_Volumes":{"top":0,"left":0,"width":0,"hidden":1},"T005_Codigo_Rastreio":{"top":0,"left":0,"width":0,"hidden":1},"T005_C007_Id_Embalagem":{"top":0,"left":0,"width":23.882383999999995,"hidden":1},"T005_Custo_Frete":{"top":0,"left":0,"width":0,"hidden":1},"T005_T208_Id":{"top":0,"left":0,"width":0,"hidden":1},"T005_Validade_Proposta":{"top":0,"left":0,"width":0,"hidden":1},"T005_Pedido_Ecommerce":{"top":0,"left":0,"width":0,"hidden":1},"T005_Flag_Dividido":{"top":0,"left":0,"width":23.882383999999995,"hidden":1},"T005_Percentual_Comissao_Representante":{"top":0,"left":0,"width":0,"hidden":1},"T005_Percentual_Comissao":{"top":0,"left":0,"width":0,"hidden":1},"ven002FormCabecalhoBtnCln":{"top":0,"left":0,"width":null,"hidden":1},"T005_D024_Id":{"top":6.9393463134765625,"left":120.77206611633301,"width":267.039216,"hidden":0},"T005_D013_Id":{"top":6.9393463134765625,"left":524.3106555938721,"width":239,"hidden":0},"ven002FormContatos":{"top":4.9632415771484375,"left":831.9393177032471,"width":null,"hidden":0},"T005_C007_Id_Vendedor_Interno":{"top":29.963241577148438,"left":107.72059059143066,"width":288,"hidden":0},"T005_Data_Emissao":{"top":29.963241577148438,"left":520.9864978790283,"width":232.039216,"hidden":0},"T005_C007_Id_Vendedor_Externo":{"top":52.95649719238281,"left":117.40196418762207,"width":287,"hidden":0},"T005_D021_Id":{"top":52.95649719238281,"left":499.73958015441895,"width":240,"hidden":0},"T005_Flag_ACP":{"top":75.96507263183594,"left":66.36029243469238,"width":112,"hidden":0},"T005_Flag_Lista_Preco":{"top":75.98039245605469,"left":291.62071418762207,"width":94,"hidden":0},"T005_Flag_Destino_Produto":{"top":75.98039245605469,"left":525.8118762969971,"width":239,"hidden":0},"T005_D148_Id_Entrega":{"top":98.95834350585938,"left":68.5355396270752,"width":287,"hidden":0},"T005_Data_Entrega":{"top":98.95834350585938,"left":525.6280384063721,"width":232.039216,"hidden":0},"T005_D148_Id_Cobranca":{"top":121.95159912109375,"left":59.175859451293945,"width":287,"hidden":0},"T005_Prazo_Entrega":{"top":121.9669189453125,"left":487.5459461212158,"width":232.039216,"hidden":0},"T005A_D148_Id_Redespacho":{"top":144.97549438476562,"left":45.526960372924805,"width":287,"hidden":0},"T005_T134_Id":{"top":167.96875,"left":18.612133026123047,"width":655,"hidden":0},"T005_D006_Id":{"top":213.93994140625,"left":126.86886787414551,"width":655,"hidden":0},"tituloOC":{"top":233.96139526367188,"left":409.98774909973145,"width":null,"hidden":0},"T005_Ordem_Compra":{"top":259.9571228027344,"left":65.54841041564941,"width":280.039216,"hidden":0},"tituloObservacoes":{"top":279.9938659667969,"left":419.96016120910645,"width":null,"hidden":0},"T005_Observacao":{"top":305.9589538574219,"left":120.77206611633301,"height":59.039215999999996,"width":280.039216,"hidden":0},"T005_Observacao_Comercial":{"top":305.9589538574219,"left":478.40074348449707,"height":59.039215999999996,"width":280.039216,"hidden":0},"T005_Observacao_Nota_Fiscal":{"top":374.9847106933594,"left":99.35661506652832,"height":59.039215999999996,"width":280.039216,"hidden":0},"T005_Observacao_1":{"top":374.9847106933594,"left":456.86274909973145,"height":59.039215999999996,"width":280.039216,"hidden":0},"tituloTransporte":{"top":440.9773254394531,"left":417.9840564727783,"width":null,"hidden":0},"T005_Flag_Frete":{"top":466.9577331542969,"left":129.77940559387207,"width":288,"hidden":0},"T005_Valor_Frete":{"top":466.9577331542969,"left":629.6568470001221,"width":120.03921600000001,"hidden":0},"T005_D022_Id":{"top":489.9662780761719,"left":71.99754524230957,"width":267.039216,"hidden":0},"T005_D036_Id":{"top":489.9662780761719,"left":660.9221668243408,"width":127,"hidden":0},"TransportadoraPadrao":{"top":512.9442443847656,"left":41.71262168884277,"width":288,"hidden":0},"button":{"top":510.9681701660156,"left":463.95527839660645,"width":null,"hidden":0},"T005_Valor_Despesas_Acessorias":{"top":512.9442443847656,"left":578.6152210235596,"width":120.03921600000001,"hidden":0},"ven002FormCabecalhoBtnAdd":{"top":510.9681701660156,"left":863.9399280548096,"width":null,"hidden":0},"T005_Flag_Totalizar_Peso":{"top":535.9681701660156,"left":612.8676624298096,"width":127,"hidden":0},"T005A_Flag_Pagamento_Antecipado":{"top":0,"left":0,"width":134.960784,"hidden":1}}'
		);

		// Permissão alterar vendedor interno do pedido
		$vendedorInternoRead = true;
		$vendedorInternoExtra = "onfocus='this.initialSelect = this.selectedIndex;' onchange='this.selectedIndex = this.initialSelect;'";
		if($acaoId > 0){		
			if ($aD024['C007_Flag_Ausente'] == 'S' && $g['C031']['alterarVendedorAusente'] == 'S') {
				$vendedorInternoRead = false;
				$vendedorInternoExtra = "";
			} else if ($g['nivelAlcada']==4) {
				$vendedorInternoRead = false;
				$vendedorInternoExtra = "";
			} else if ($g['nivelAlcada']==2 && ($aD024['C007_C007_Id']==$g['usuarioAtual'] || $aD024['C007_C007_Id_2']==$g['usuarioAtual'])) {
				$vendedorInternoRead = false;
				$vendedorInternoExtra = "";
			} else if ($g['C031']['alterarVendedor']=='S'){
				$vendedorInternoRead = false;
				$vendedorInternoExtra = "";
			}
		}

		// Permissão alterar vendedor externo do pedido
		$vendedorExternoRead = true;
		$vendedorExternoExtra = "onfocus='this.initialSelect = this.selectedIndex;' onchange='this.selectedIndex = this.initialSelect;'";
		if($acaoId > 0){		
			$sqlC007 = mysql_query("SELECT C007_Flag_Ausente, 
										   C007_C007_Id,
										   C007_C007_Id_2
			                          FROM T005 
								 LEFT JOIN C007 ON C007_Id=T005_C007_Id_Vendedor_Externo
								     WHERE T005_Id = '{$acaoId}'");
			$rowC007 = mysql_fetch_array($sqlC007);
			if ($rowC007['C007_Flag_Ausente'] == 'S' && $g['C031']['alterarVendedorAusente'] == 'S') {
				$vendedorExternoRead = false;
				$vendedorExternoExtra = "";
			} else if ($g['nivelAlcada']==4){
				$vendedorExternoRead = false;
				$vendedorExternoExtra = "";
			} else if ($g['nivelAlcada']==2 && ($rowC007['C007_C007_Id']==$g['usuarioAtual'] || $rowC007['C007_C007_Id_2']==$g['usuarioAtual'])) {
				$vendedorExternoRead = false;
				$vendedorExternoExtra = "";
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
		$destinoProdutos = $CAD002->retornaDestinoProdutos(false, 'V');

		// novo formulário
		$form = new Formulario('form_ven002_form1');

		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T005A_Flag_Pagamento_Antecipado' => array('name' => 'T005A_Flag_Pagamento_Antecipado', 'label' => 'Possui NF de pgto antecipado', 'type' => 'select', 'disabled' => true, 'reject' => true, 'reload' => false,
						'select' => array(
							array('title' => 'Não', 'value' => 'N', 'selected' => empty($aD024['T005A_Flag_Pagamento_Antecipado']) || $aD024['T005A_Flag_Pagamento_Antecipado'] !== 'S'),
							array('title' => 'Sim', 'value' => 'S', 'selected' => isset($aD024['T005A_Flag_Pagamento_Antecipado']) && $aD024['T005A_Flag_Pagamento_Antecipado'] === 'S'),
						)
					),
					'T005_D024_Id' => array('name' => 'T005_D024_Id', 'label'=> "Cliente", 'type' => 'pesquisa', 'extra' => "onchange=\"carregaCFOP_Contatos(this, '{$g['divId']}', '', '{$g['C031']['alterarVendedorAusente']}', '{$g['C031']['alterarVendedor']}');\"", 'pesquisa' => array('D024','D024_Id','D024_Nome_Empresa,D020_Nome_Cidade,D018_UF,D024_Cpf,D024_Cnpj,D024_DDD_Telefone_1,D024_Telefone_1,D024_Nome_Fantasia,D024_Id','left join D020 on D020_Id=D024_D020_Id left join D018 on D018_Id=D024_D018_Id WHERE D024_Flag_Cliente="S" '. $clientePorEmpresa .' and D024_Flag_Ativo="S" order by D024_Nome_Empresa','%1 - %9 - (%4%5) -  [%2/%3]'), 'length' => array(80,30)),
					'T005_D013_Id' => array('name' => 'T005_D013_Id', 'label' => 'Contato', 'type' => 'select',
						'select' => gGeraSelect('D013','D013_Id','D013_Nome_Contato,D013_DDD_Telefone_1,D013_Telefone_1,D013_E_Mail',"where D013_D024_Id=$idCliente AND D013_Flag_Ativo = 'S'",true),
						'selectRefresh' => array('D013','D013_Id','D013_Nome_Contato,D013_DDD_Telefone_1,D013_Telefone_1,D013_E_Mail',"where D013_D024_Id=$idCliente AND D013_Flag_Ativo = 'S'",true),
					),
					'ven002FormContatos' => array('name' => 'ven002FormContatos', 'type'=>'button', 'reload' => false, 'value' => 'Incluir', 'reject' => true),
					'T005_D148_Id_Entrega' => array('name' => 'T005_D148_Id_Entrega', 'label' => 'Endereço Entrega', 'type' => 'select', 'selectRefresh' => array('D148','D148_Id','D148_Nome','WHERE D148_D024_Id='.$idCliente.' ORDER BY D148_Id',true),
						'select' => $arrayEndereco
					),
					'T005_D148_Id_Cobranca' => array('name' => 'T005_D148_Id_Cobranca', 'label' => 'Endereço Cobrança', 'type' => 'select', 'selectRefresh' => array('D148','D148_Id','D148_Nome','WHERE D148_D024_Id='.$idCliente.' ORDER BY D148_Id',true),
						'select' => $arrayEndereco
					),
					'T005A_D148_Id_Redespacho' => array('name' => 'T005A_D148_Id_Redespacho', 'label' => 'Endereço Redespacho', 'type' => 'select', 'selectRefresh' => array('D148','D148_Id','D148_Nome','WHERE D148_D024_Id='.$idCliente.' ORDER BY D148_Id',true),
						'select' => $arrayEndereco
					),
					'T005_T134_Id' => array('name' => 'T005_T134_Id','label'=>'Faixa para cálculo do Crédito<br /> de ICMS Simples Nacional','uppercase'=>true,'type' => 'select', 'select' =>
						gGeraSelect('T134', 'T134_Id','CONCAT(T134_Tipo[[virgula]]": DE "[[virgula]]convert(format(round(T134_Faixa_Inicial),2,"de_DE")using utf8)[[virgula]]" A "[[virgula]]convert(format(round(T134_Faixa_Final),2,"de_DE")using utf8)[[virgula]]" = ALIQ: "[[virgula]]T134_ICMS[[virgula]]"% REDUÇÃO: "[[virgula]]if(T134_Reducao_Base_ICMS,T134_Reducao_Base_ICMS,0)[[virgula]]"%") as faixaCalculo','',true,$faixaCalculo)
					),
					'T005_D006_Id' => array('name' => 'T005_D006_Id', 'label'=>'CFOP','type' => 'select', 'select' => 
        					gGeraSelect('D006','D006_Id','D006_Codigo_CFOP,D006_Descricao',' WHERE IFNULL(D006_Flag_Inativo,"N")!="S" AND D006_Flag_Departamento="COMERCIAL"  ORDER BY D006_Codigo_CFOP',true),
					),											
					'T005_Data_Emissao' => array('name' => 'T005_Data_Emissao','label'=>'Emissão','type' => 'data', 'length' => array(20,10)),
					'T005_Data_Entrega' => array('name' => 'T005_Data_Entrega','label'=>'Entrega','type' => 'data', 'length' => array(20,10), 'exibir'=>false),
					'T005_C007_Id_Vendedor_Interno' => array('name' => 'T005_C007_Id_Vendedor_Interno', 'label'=>'Vendedor','type' => 'select', 'extra' => $vendedorInternoExtra, 'readonly' => $vendedorInternoRead, 'select' => 
							gGeraSelect('C007','C007_Id','IF(C007_Ativo != "N"[[virgula]] C007_Primeiro_Nome[[virgula]] CONCAT(C007_Primeiro_Nome[[virgula]]" (INATIVO)")) as C007_Primeiro_Nome','WHERE C007_Flag_Vendedor!="" AND (C007_Ativo != "N" OR C007_Id = "'.$idVendedorInterno.'") ORDER BY C007_Primeiro_Nome',true),
					),
					'T005_C007_Id_Vendedor_Externo' => array('name' => 'T005_C007_Id_Vendedor_Externo', 'label'=>'Externo','type' => 'select', 'extra' => $vendedorExternoExtra, 'readonly' => $vendedorExternoRead, 'select' => 
							gGeraSelect('C007','C007_Id','IF(C007_Ativo != "N"[[virgula]] C007_Primeiro_Nome[[virgula]] CONCAT(C007_Primeiro_Nome[[virgula]]" (INATIVO)")) as C007_Primeiro_Nome','WHERE C007_Flag_Vendedor!="" AND (C007_Ativo != "N" OR C007_Id = "'.$idVendedorExterno.'") ORDER BY C007_Primeiro_Nome',true),
					),
					'T005_D021_Id' => array('name' => 'T005_D021_Id', 'label'=>'Representante','type' => 'select', 'select' => 
							gGeraSelect('D021','D021_Id','D021_Nome_Empresa',"WHERE D021_Ativo = 'S'",true),
					),	
					'T005_Percentual_Comissao' => array('name' => 'T005_Percentual_Comissao', 'label'=>'Comissão %','value'=>'0,00','type' => 'moeda', 'length' => array(20,10)),
					'T005_Percentual_Comissao_Representante' => array('name' => 'T005_Percentual_Comissao_Representante', 'label'=>'Comissão Rep. %','value'=>'0,00','type' => 'moeda', 'length' => array(20,10)),
					'T005_Flag_ACP' => array('name' => 'T005_Flag_ACP', 'label'=>'Tipo Documento','type' => 'select','select' => 
						array(
							array('title' => '...', 'value' => 0),
							array('title' => 'NF', 'value' => 1),
							array('title' => 'Romaneio', 'value' => 2),
							array('title' => 'ACP', 'value' => 3),
							array('title' => 'Cupom Fiscal', 'value' => 4),
							array('title' => 'NF D1', 'value' => 5),
							array('title' => 'NFe', 'value' => 6, 'selected' => true),
							array('title' => 'NFSe', 'value' => 7),							
							array('title' => 'NFCe', 'value' => 8),							
						)
					),
					'T005_Prazo_Entrega' => array('name' => 'T005_Prazo_Entrega', 'label'=>'Prazo de Entrega','type' => 'text', 'length' => array(53,30)),
					'T005_D116_Id' => array('name' => 'T005_D116_Id', 'label' => 'Veículo','type' => 'select',
						'select'=> gGeraSelect('D116', 'D116_Id', 'D116_Placa,D133_Modelo,D134_Cor', "LEFT JOIN D133 on D133_Id=D116_D133_Id LEFT JOIN D134 on D134_Id=D116_D134_Id WHERE D116_D024_Id=$idCliente", true),
						'selectRefresh' => array('D116', 'D116_Id', 'D116_Placa,D133_Modelo,D134_Cor', "LEFT JOIN D133 on D133_Id=D116_D133_Id LEFT JOIN D134 on D134_Id=D116_D134_Id WHERE D116_D024_Id=$idCliente", true)
					),
					'T005_Flag_Lista_Preco' => 	array('name' => 'T005_Flag_Lista_Preco', 'label' => 'Lista Preço', 'type' => 'select', 'select' => 
						array(
							array('title' => 'Preço 1', 'value' => '1'),   
							array('title' => 'Preço 2', 'value' => '2'),   
							array('title' => 'Preço 3', 'value' => '3'),   
							array('title' => 'Preço 4', 'value' => '4'),   
							array('title' => 'Preço 5', 'value' => '5'),
						)
					),
					'ven002FormVeiculos' => array('name' => 'ven002FormVeiculos', 'type'=>'button', 'reload' => false, 'value' => 'Incluir', 'reject' => true),
					'tituloOC' => array('name' => 'tituloOC', 'label'=>'Ordem de Compra','type' => 'titulo', 'reject' => true, 'reload' => false),
					'T005_Ordem_Compra' => array('name' => 'T005_Ordem_Compra', 'label'=>'Ordem de Compra','type' => 'text', 'length' => array(53,30)),
					'tituloObservacoes' => array('name' => 'tituloObservacoes', 'label'=>'Observações','type' => 'titulo', 'reject' => true),
					'T005_Observacao' => array('name' => 'T005_Observacao', 'label'=>'Cliente','type' => 'textarea','extra' => 'title="Esta observação será visualizada pelo cliente (Não será impressa na DANFE)"', 'length' => array(3,50)),
					'T005_Observacao_Comercial' => array('name' => 'T005_Observacao_Comercial', 'label'=>'Comercial<br> Interna','type' => 'textarea','extra' => 'title="Observação de uso interno da área comercial (Não será impressa na DANFE)"', 'length' => array(3,50)),
					'T005_Observacao_Nota_Fiscal' => array('name' => 'T005_Observacao_Nota_Fiscal', 'label'=>'Nota Fiscal','type' => 'textarea','extra' => 'title="Esta observação será impressa na DANFE"', 'length' => array(3,50)),
					'T005_Observacao_1' => array('name' => 'T005_Observacao_1', 'label'=>'Almox.:','type' => 'textarea','extra' => 'title="Observação de uso interno da área de estoque (Não será impressa na DANFE)"', 'length' => array(3,50)),
					'tituloTransporte' => array('name' => 'tituloTransporte', 'label'=>'Transporte','type' => 'titulo', 'reject' => true),
					'T005_Flag_Frete' => array('name' => 'T005_Flag_Frete','label'=>'Frete', 'type' => 'select',
						'select' => array(
							array('title' => '0 - Contratação por conta do Remetente', 'value' => 0),
							array('title' => '1 - Contratação por conta do Destinatário', 'value' => 1),
							array('title' => '2 - Contratação por conta de Terceiros', 'value' => 2),
							array('title' => '3 - Próprio por conta do Remetente', 'value' => 3),
							array('title' => '4 - Próprio por conta do Destinatário', 'value' => 4),							
							array('title' => '9 - Sem frete', 'value' => 9),
						),
					),
					'T005_Valor_Despesas_Acessorias' => array('name' => 'T005_Valor_Despesas_Acessorias', 'label'=>'Despesas acessórias', 'type' => 'moeda', 'value'=> '', 'casasDecimais' => '2', 'length' => array(12,10)),
					'T005_Valor_Frete' => array('name' => 'T005_Valor_Frete', 'label'=>'Valor Frete','value'=>'0,00','type' => 'moeda', 'length' => array(20,20)),
					'T005_D022_Id' => array('name' => 'T005_D022_Id', 'label' => 'Transportadora', 'type' => 'pesquisa', 'pesquisa' => array('D022','D022_Id','D022_Id,D022_Nome_Empresa,D020_Nome_Cidade,D018_UF', "left join D020 on D020_Id=D022_D020_Id left join D018 on D018_Id=D022_D018_Id left join D022A on D022_Id=D022A_D022_Id WHERE IFNULL(D022A_Flag_Ativo, 'S') != 'N'"), 'extra' => "onchange=\"carregaTipoTransp(this, '{$g['divId']}')\"", 'length' => array(50,90)),
					'T005_T208_Id' => array('name' => 'T005_T208_Id', 'label' => 'Rota', 'type' => 'pesquisa', 'pesquisa' => array('T208','T208_Id','T208_Rota')),
					'T005_D036_Id' => array('name' => 'T005_D036_Id', 'label' => 'Tipo', 'type' => 'select', 'select' =>
							gGeraSelect('D036','D036_Id','D036_Tipo_Transporte','',true),
					),
					'TransportadoraPadrao' => array('name' => 'TransportadoraPadrao', 'label' => 'Transportadora Padrão', 'type' => 'select','reject' =>true ,'disabled' => true,
						'select' => gGeraSelect('D022','D022_Id','D022_Nome_Empresa,D020_Nome_Cidade,D018_UF',"left join D020 on D020_Id=D022_D020_Id left join D018 on D018_Id=D022_D018_Id WHERE D022_Id='{$idTransportadora}' OR D022_Id = '{$idTransportadoraCidade}'",false), 
					),
					'button' => array('name' => 'button', 'type' => 'button','value' =>'Usar Padrão','reload' => false,'extra' => "onclick='copiaTransportadora({$idTipoTransp})'",'reject'=>true),
					'nada' => array('name' => 'nada', 'label' => 'Transportadora', 'type' => 'hidden', 'reject'=>true, 'length' => array(30,30)),
					'T005_Flag_Revenda_Consumidor' => array('name' => 'T005_Flag_Revenda_Consumidor', 'label'=>'T005_Flag_Revenda_Consumidor','type' => 'hidden'
						//'select' => 
							//array(
								//array('title' => '...', 'value' => 0, 'selected' => true),
								//array('title' => 'Revenda', 'value' => 1),
								//array('title' => 'Consumidor atacado', 'value' => 2),
								//array('title' => 'Consumidor varejo', 'value' => 3),
							//)
					),
					'T005_Quantidade_Volumes' => array('name' => 'T005_Quantidade_Volumes', 'label' => 'Volumes', 'type' => 'text'),
					'T005_Especie' => array('name' => 'T005_Especie', 'label' => 'Espécie', 'type' => 'text'),
					'T005_Peso_Liquido' => array('name' => 'T005_Peso_Liquido', 'label' => 'Peso Liquido', 'type' => 'text'),
					'T005_Peso_Bruto' => array('name' => 'T005_Peso_Bruto', 'label' => 'Peso Bruto', 'type' => 'text'),
					'T005_Flag_Dividido' => array('name' => 'T005_Flag_Dividido', 'label'=>'Dividido','type' => 'select','select' => 
						array(
							array('title' => 'Não', 'value' => 'N'),
							array('title' => 'Sim', 'value' => 'S'),
						)
					),
					'T005_Pedido_Ecommerce' => array('name' => 'T005_Pedido_Ecommerce', 'label' => 'N° Ecommerce', 'type' => 'text'),
					'T005_Codigo_Rastreio' => array('name' => 'T005_Codigo_Rastreio', 'label' => 'Codigo de Rastreio', 'type' => 'text'),
					'T005_C007_Id_Embalagem' => array('name' => 'T005_C007_Id_Embalagem', 'label' => 'Embalado por', 'type' => 'select', 'select' =>
						gGeraSelect('C007', 'C007_Id', 'C007_Primeiro_Nome', '', true)
					),
					'T005_Custo_Frete' => array('name' => 'T005_Custo_Frete', 'label' => 'Custo Frete', 'type' => 'moeda'),
					'T005_Validade_Proposta' => array('name' => 'T005_Validade_Proposta', 'label' => 'Validade Proposta', 'type' => 'text'),
					'T005_Flag_Totalizar_Peso' => array('name' => 'T005_Flag_Totalizar_Peso', 'label'=>'Totalizar Peso','type' => 'select','select' => 
						array(
							array('title' => 'Não', 'value' => 'N', 'selected' => ($g['C031']['totalizarPesoVenda'] == "N") ? true : false),
							array('title' => 'Sim', 'value' => 'S', 'selected' => ($g['C031']['totalizarPesoVenda'] == "S") ? true : false),
						)
					),
					'T005_Flag_Destino_Produto' => array('name' => 'T005_Flag_Destino_Produto','label'=>'Destino', 'type' => 'select', 
						'select' => $destinoProdutos
					),
					'atualizaDivIdMain' => array('name' => 'atualizaDivIdMain', 'label' => 'atualizaDivIdMain', 'type' => 'hidden', 'reject' => true, 'value' => 'N'),
				)
			)
		);

		if($g['C031']['modeloExpedicao'] == '2'){
			$formArray['campos']['geral']['T005_Flag_Expedicao'] = array('name' => 'T005_Flag_Expedicao', 'label' => 'Expedição', 'type' => 'select', 'select' => 
				array(
					array('title' => '...', 'value' => ''),
					array('title' => 'Comercial', 'value' => '1'),
					array('title' => 'Balcão', 'value' => '2'),
				)
			);
		}
		
		/** Botão Limpar / Novo Registro
		if (!empty($acaoId)) {
			$formArray['campos']['geral']['ven002FormCabecalhoBtnNew'] = array('name' => 'ven002FormCabecalhoBtnNew', 'type'=>'submit', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right; margin-top:20px;', 'reject' => true);
		}
		*/
		$formArray['campos']['geral']['ven002FormCabecalhoBtnCln'] = array('name' => 'ven002FormCabecalhoBtnCln', 'type'=>'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right; margin-top:20px;', 'reject' => true);
		$formArray['campos']['geral']['ven002FormCabecalhoBtnAdd'] = array('name' => 'ven002FormCabecalhoBtnAdd', 'type'=>'button', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right; margin-top:20px;', 'reject' => true);

		if($g['C031']['venderEstoqueMatriz'] == 'S'){
			$formArray['campos']['geral']['T005_C004_Id_Estoque'] = array('name' => 'T005_C004_Id_Estoque', 'label' => 'Estoque', 'type' => 'select','select' => gGeraSelect('C004','C004_Id','C004_Nome_Abreviado,C004_Nome_Empresa',"where C004_Flag_Ativo='S' and (C004_Id='1' or C004_Id='{$g['empresaAtual']}')",true,$g['empresaAtual']));
		}

		$form->definirFormularioArray($formArray);
		
		$sql = "SELECT T005_D024_Id FROM T005 WHERE T005_Id = '{$r_T005_Id}';";
		$resultado = mysql_query($sql);
		$row = mysql_fetch_array($resultado);
		echo <<<EOT
		    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		    	$('#ven002FormContatos').unbind('click');
				$('#ven002FormContatos').unbind('click').bind('click', function(data) {
					var id = '{$row['T005_D024_Id']}';
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Cliente/Fornecedor - ID: ' + id, '/cad/cad001/content/cad001contentCadastro/', '&acaoId=' + encodeURIComponent(id) + '&tabela=D024' + '&tab=contatos', [950,553]);
					return false;
				});
				$('#ven002FormVeiculos').unbind('click');
				$('#ven002FormVeiculos').unbind('click').bind('click', function(data) {
					var id = '{$row['T005_D024_Id']}';
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Incluir Veículo', '/cad/cad001/content/cad001ContentVeiculosIncluir/', '&naoRefreshParent=true&D024_Id=' + encodeURIComponent(id) + '&tabela=D024' + '&tab=veiculos', [300,350]);
					return false;
				});

				$('#{$g['divId']} #T005_Flag_ACP').change(function() {
	                if($(this).val() == 8){
	                	$('#{$g['divId']} #T005_D022_Id_Pesquisa').val('').prop('readonly', true).css('background-color', '#ede7de');
	                	$('#{$g['divId']} #T005_D022_Id').val('');
	                	$('#{$g['divId']} #T005_Flag_Frete').val('9');
	                	$('#{$g['divId']} #button').closest('.formEditor').hide();
	                } else {
	                	$('#{$g['divId']} #T005_D022_Id_Pesquisa').prop('readonly', false).css('background-color', '#ffffff');
	                	$('#{$g['divId']} #button').closest('.formEditor').show();
	                }
	            });
	            $('#{$g['divId']} #T005_Flag_ACP').trigger('change');
		    } </script></div>
EOT;

		/**
			* Na maioria dos casos não será necessário alterar nada daqui para baixo
			* Somente em casos especiais, para alterar pequenos detalhes ou modificações do visual
			*/


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
					$FlagACP = $form->campoValorEnviado('T005_Flag_ACP');

					$T005 = mysql_query("SELECT T005_Flag_Lista_Preco,
												T005_Valor_Frete,
												T005A_Flag_Ratear_Frete_Custo,
												T005_Flag_Frete,
												T005_Flag_Destino_Produto 
										   FROM T005 
									  LEFT JOIN T005A ON T005_Id= T005A_T005_Id
									  LEFT JOIN D022 ON D022_Id=T005_D022_Id 
									      WHERE T005_Id = '{$acaoId}'");

					$rT005 = mysql_fetch_array($T005);
					if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {						
						if($g['C031']['modeloExpedicao'] == '1'){
							if($FlagACP == '4'){
								$T005_Flag_Expedicao = 2;
							} else {
								$T005_Flag_Expedicao = 1;
							}
							mysqli_query("UPDATE T005 SET T005_Flag_Expedicao = '{$T005_Flag_Expedicao}' WHERE T005_Id = '{$acaoId}'");
						}

						//Caso no cabeçalho seja colocado algum tipo de frete diferente de Emitente O ipv desse orçamento especifico recebe 0 
						$frete 				  = $form->campoValorEnviado('T005_Flag_Frete');
						$valorFrete 		  = gCorrigeNumeroInverte($form->campoValorEnviado('T005_Valor_Frete'));
						$atualizouPrecoTabela = false;
						if($frete!='0'){
							log("if frete dentro de pedido");
							$T251 = mysql_query("Select T251_Id,T251_Aliquota,T251_Flag_Altera_Preco from T251 where T251_T005_Id='$acaoId' and T251_Tipo='1' and T251_Flag_Campo_Calculado='S'");
							$mT251=mysql_fetch_array($T251);
							$T251_Id=$mT251['T251_Id'];
							log("T251:".$T251_Id);

							if($mT251['T251_Aliquota']>0){
								if($mT251['T251_Flag_Altera_Preco']=='S'){
									$msg = "<br> Foi deduzido ".$mT251['T251_Aliquota']."% no preço tabela desse orçamento.";
								}else{
									$msg = "<br> Foi deduzido ".$mT251['T251_Aliquota']."% do demonstrativo IPV.";
								}

								$atualizouPrecoTabela = true;
								mysql_query("Update T251 set T251_Aliquota=0 where T251_Id='{$T251_Id}'");
								$VEN002->atualizarPrecoTabela($acaoId);
							}

							if((($valorFrete != $rT005['T005_Valor_Frete']) || ($frete != $rT005['T005_Flag_Frete'])) && $rT005['T005A_Flag_Ratear_Frete_Custo'] == 'S' && !$atualizouPrecoTabela){
								$VEN002->atualizarPrecoTabela($acaoId);								
							} 

						}else{
							//caso seja emitente o T251_Aliquota desse IPV desse orçamento recebe o Valor original do ipv(tabela T249_ALiquota)
							$T251 = mysql_query("Select T251_Id,T251_Aliquota,T251_Flag_Altera_Preco from T251 where T251_T005_Id='$acaoId' and T251_Tipo='1' and T251_Flag_Campo_Calculado='S'");
							$mT251=mysql_fetch_array($T251);
							$T251_Id=$mT251['T251_Id'];

							$T249 = mysql_query("select T249_Id,T249_Aliquota from T249 where T249_Tipo='1' and T249_Flag_Campo_Calculado='S' and T249_C004_Id='{$g['empresaAtual']}'");
							$mT249 = mysql_fetch_array($T249);

							if($mT251['T251_Aliquota']<=0 and $mT249['T249_Aliquota']>0){
								if($mT251['T251_Flag_Altera_Preco']=='S'){
									$msg = "<br> Foi acrescido ".$mT249['T249_Aliquota']."% no preço tabela desse orçamento.";
								}else{
									$msg = "<br> Foi acrescido ".$mT249['T249_Aliquota']."% ao demonstrativo IPV.";
								}
								
								$atualizouPrecoTabela = true;
								mysql_query("Update T251 set T251_Aliquota='{$mT249['T249_Aliquota']}' where T251_Id='{$T251_Id}'");
								$VEN002->atualizarPrecoTabela($acaoId);								
							}

							if((($valorFrete != $rT005['T005_Valor_Frete']) || ($frete != $rT005['T005_Flag_Frete'])) && $rT005['T005A_Flag_Ratear_Frete_Custo'] == 'S' && !$atualizouPrecoTabela){
								$VEN002->atualizarPrecoTabela($acaoId);								
							} 

						}

						// Personalizar de acordo com necessidade do cliente
						$VEN002->acoesFinaisAtualizarPedido($acaoId);

						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								T005_Id = {$acaoId};						
						 		idProgressBar = dialogAlert('Atualizando Alíquotas',"<span id='progressBar'>Processando 0%</span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' />");
			            		ven002AtualizarAliquotasLoop(idProgressBar,T005_Id,1);

	  					 		function ven002AtualizarAliquotasLoop(idProgressBar, T005_Id, inicio){
					                $.getJSON('/ven/ven002/form_func-ajax/atualizarAliquota/?ajax=true&T005_Id=' + T005_Id + '&extra=' + encodeURIComponent("{$extra_}") + '&inicio=' + inicio + '&callback=?', function(request) {
					                    if (request.concluido == 0) {
					                    	$('#progressBar').html('<b>Processado '+ request.percentual + '%</b>' + '<br/><br/>Produtos atualizados: <b>' + request.ok + '</b><br/>Produtos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
					                    	ven002AtualizarAliquotasLoop(idProgressBar,T005_Id,0);
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
						// echo gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!'.$msg);
						$lista = $form->campoValorEnviado('T005_Flag_Lista_Preco');
						//Verifica se a lista de preço foi alterada
						if(($rT005['T005_Flag_Lista_Preco'] != $lista) && !empty($lista)){
							$VEN002->alteraLista($acaoId, $lista);
							echo gGeraAlertMsg('Atenção', 'A lista de preço foi alterada, os valores de tabela dos produtos serão modificados.');	
						}	
						
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
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						// Imprime uma mensagem e fecha a janela
						$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!');
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									{$alert}
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

	/**
	 * Formulário Produtos
	 */
	case 'ven002form02':
		$g['smarty']->assign('navSql', "T005_C004_Id = '{$g['empresaAtual']}'");
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']}').data('sqlPesquisa', '[T005_Id, T005_Data_Emissao as Data, T005_Data_Entrega as Data, D024_Nome_Empresa, D024_Nome_Fantasia]');
				$('#{$g['divId']}').data('labelPesquisa', '[Pedido, Emissão, Entrega, Razão Social, Nome Fantasia]');
				$('#{$g['divId']}').data('sqlExtra', '[LEFT JOIN D024 ON T005_D024_Id = D024_Id]');
			} </script></div>
EOT;
		$T005 = mysql_fetch_array(mysql_query("SELECT ifnull(T005_T005_Id_Origem,0) as T005_T005_Id_Origem, T005_Casas_Decimais_Preco FROM T005 WHERE T005_Id = '{$r_T005_Id}'"));
		if($T005['T005_T005_Id_Origem'] <= 0){ 
			gHabilitaNavegacao($g['divId'], 'VEN002', 'ven002form02');
		}
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$T005_Id = (isset($r_T005_Id)) ? $r_T005_Id : false;

		$T005 = mysql_query("SELECT T005_D024_Id, 
								    T005_C004_Id_Estoque,
								    T005_Casas_Decimais_Preco,
								    T005_Flag_Lista_Preco 
							   FROM T005 
							  WHERE T005_Id='{$r_T005_Id}'");
		
		$mT005 = mysql_fetch_array($T005);

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
		$form_tabela = 'T006';
		$form_campo_pk = 'T006_Id';
        $form_parent = 'T005_Id';
        $form_parent_valor = $T005_Id;
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = array('ven002FormProdutosBtnAdd', 'ven002FormProdutosBtnCln', 'ven002FormProdutosBtnNew');
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/ven/ven002/form/ven002form02/';

        //form editor
		if ($g['C031']['utilizaQuantidadeConversao'] === 'S') {
			$camposForm = ',"UltimoPrecoVenda": {"top": 190.994873046875,"left": 30.21649169921875,"height": 39.020618,"width": 392.020618,"hidden": 0},"ambiente": {"top": 168,"left": 27.60308837890625,"width": 392.020618,"hidden": 0},"T006_Observacao": {"top": 144.98971557617188,"left": 16.283493041992188,"width": 392.020618,"hidden": 0},"T006_Flag_Destino_Produto": {"top": 121.99484252929688,"left": 29.798965454101562,"width": 399,"hidden": 0},"T006_Valor_Preco_Sem_Desconto_Unitario": {"top": 75.98968505859375,"left": 13.608245849609375,"width": 72.02061800000001,"hidden": 0},"T006_Percentual_Desconto": {"top": 75.98968505859375,"left": 178.54638671875,"width": 24.020618,"hidden": 0},"T006A_Quantidade_Conversao": {"top": 52.994842529296875,"left": 268.4226989746094,"width": 120.02061800000001,"hidden": 0},"T006_Observacao_Entrega": {"top": 75.98968505859375,"left": 285.80413818359375,"width": 120.02061800000001,"hidden": 0},"T006_Numero_Pedido_Compra": {"top": 98.98455810546875,"left": 305.273193359375,"width": 72.02061800000001,"hidden": 0}';
		} else {
			$camposForm = ',"UltimoPrecoVenda": {"top": 168,"left": 30.21649169921875,"height": 39.020618,"width": 392.020618,"hidden": 0},"T006_Observacao": {"top": 121.99484252929688,"left": 16.283493041992188,"width": 392.020618,"hidden": 0},"T006_Observacao_Entrega": {"top": 75.98968505859375,"left": 269.814453125,"width": 136.020618,"hidden": 0},"T006_Numero_Pedido_Compra": {"top": 98.98455810546875,"left": 289.28350830078125,"width": 88.02061800000001,"hidden": 0}, "T006_Flag_Destino_Produto": {"top": 75.98968505859375,"left": 29.798965454101562,"width": 143,"hidden": 0},"T006_Valor_Preco_Sem_Desconto_Unitario": {"top": 52.994842529296875,"left": 269.59796142578125,"width": 71.02061800000001,"hidden": 0},"ambiente": {"top": 144.98971557617188,"left": 27.60308837890625,"width": 392.020618,"hidden": 0},"T006_Percentual_Desconto": {"top": 52.994842529296875,"left": 434.53607177734375,"width": 23.020618,"hidden": 0}';
		}

		$form_editor = array(
			'geral' => '{"T006_Data_Entrega": {"top": 0,"left": 0,"width": 0,"hidden": 1},"D001_Peso_Unitario_Bruto": {"top": 0,"left": 0,"width": 137,"hidden": 1},"T006_Hora_Entrega": {"top": 0,"left": 0,"width": 127,"hidden": 1},"T006_T208_Id": {"top": 0,"left": 0,"width": 57.958782,"hidden": 1},"T006_D006_Id": {"top": 0,"left": 0,"width": 382.958382,"hidden": 1},"T006_D022_Id": {"top": 0,"left": 0,"width": 350.958382,"hidden": 1},"T006_Aliquota_IPI": {"top": 0,"left": 0,"width": 40,"hidden": 1},"Procura_Produto": {"top": 6.9897003173828125,"left": 35.73710632324219,"width": 122.02061800000001,"hidden": 0},"Marca": {"top": 6.9897003173828125,"left": 299.64434814453125,"width": 136.020618,"hidden": 0},"precoTabela": {"top": 6.9897003173828125,"left": 500.273193359375,"width": 88.02061800000001,"hidden": 0},"T006_Descricao_Produto": {"top": 29.984542846679688,"left": 18.247421264648438,"width": 392.020618,"hidden": 0},"PrecoMinimo": {"top": 29.984542846679688,"left": 495.66497802734375,"width": 88.02061800000001,"hidden": 0},"T006_Quantidade": {"top": 52.979400634765625,"left": 10.963912963867188,"width": 72.02061800000001,"hidden": 0},"T006_D037_Id": {"top": 52.979400634765625,"left": 159.9742431640625,"width": 63,"hidden": 0},"estoqueLiquido": {"top": 52.979400634765625,"left": 515.6907348632812,"width": 88.02061800000001,"hidden": 0},"estoqueLoja": {"top": 75.9742431640625,"left": 512.6443481445312,"width": 88.02061800000001,"hidden": 0},"T006_Codigo_Substituto": {"top": 98.98455810546875,"left": 4.6855621337890625,"width": 136.020618,"hidden": 0},"T006_Item_Pedido_Compra": {"top": 98.98455810546875,"left": 431.98455810546875,"width": 40.020618,"hidden": 0},"estoqueTotal": {"top": 98.98455810546875,"left": 509.10308837890625,"width": 88.02061800000001,"hidden": 0},"ordemCompra": {"top": 121.97940063476562,"left": 539.8763427734375,"width": 88.02061800000001,"hidden": 0},"estoqueFora": {"top": 144.98971557617188,"left": 511.71649169921875,"width": 88.02061800000001,"hidden": 0},"ven002BtnCadastrarProduto": {"top": 165.98971557617188,"left": 519.9896850585938,"width": null,"hidden": 0},"ven002FormProdutosBtnCln": {"top": 189,"left": 519.9896850585938,"width": null,"hidden": 0},"ven002FormProdutosBtnAdd": {"top": 189,"left": 600,"width": null,"hidden": 0}'. $camposForm .'}',
		);	

		$calculadoraQtdNavegaTab = (!isset($g['C031']['calculadoraQtdNavegaTab']) || $g['C031']['calculadoraQtdNavegaTab'] !== 'N') ? 'true' : 'false';

		$C004_Id_Estoque = $g['empresaAtual'];
		if($g['C031']['venderEstoqueMatriz'] == 'S'){
			$C004_Id_Estoque = (isset($mT005['T005_C004_Id_Estoque']) && $mT005['T005_C004_Id_Estoque'] > 0) ? $mT005['T005_C004_Id_Estoque'] : $g['empresaAtual'];
		}
		//configuração global de separar produto por empresa
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
			array('style' => 'font-weight:bold;white-space:normal;','colunaExibir' => false),
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
			array('callback' => 'estoqueFora', 'style' => 'width: 1%;', 'colunaAlinhar' => 'right', 'callbackParameter' => array(false, $r_T005_Id)),
			array('callback' => 'abrirJanela', 'callbackParameter' => array('Estoque Similares', '/cad/cad002/content/cad002ContentEstoqueSimilares/', 'D001_Id', '850,400', true), 'style' => 'width: 1%;', 'colunaAlinhar' => 'right'),
			array('colunaTipo' => 'livre', 'callback' => 'retornaPrecoVendaPedido', 'colunaAlinhar' => 'right', 'style' => 'font-weight:bold;width:1%','callbackParameter' => array($r_T005_Id, $mT005['T005_Flag_Lista_Preco'])),
			array('style' => 'width: 1%;', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => 3, 'colunaEditar' => true, 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T266_Quantidade', 'colunaEditarInsert' => false, 'colunaExibir' => $incluirLote),
			array('colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('Ordens de Compra', '/ven/ven001/content/ven001contentProdutoOC/', 'D009_Id', '700,400', true), 'style' => 'font-weight:normal;width:1%'),
			array('style' => 'width:1%'),
			array('style' => 'width:1%'),
			array('style' => 'font-weight:bold;white-space:normal;', 'colunaExibir' => true),
			array('colunaExibir' => false),
			array('colunaExibir' => false),
			array('colunaExibir' => false),
			array('chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaExibir' => false),
			array('chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaTitulo' => 'T005_Id', 'colunaExibir' => false),
			array('colunaAlinhar' => 'right', 'callback' => 'abrirJanela', 'callbackParameter' => array('Tabelas Fornecedor', '/cad/cad002/tab/cad002TabTabelaFornVenda/', 'D009_Id', '700,400', true), 'style' => 'font-weight:normal;width:1%', 'colunaExibir'=>false),
		);

		$qtdeConversaoCampo = '';
		$qtdeConversaoDesc  = '';
		if ($g['C031']['utilizaQuantidadeConversao'] === 'S') {
			$qtdeConversaoCampo = '||T266_Quantidade_Conversao';
			$qtdeConversaoDesc  = ',Qtde Conversão';

			array_push($listaCampos, array('style' => 'width: 1%;', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => 3, 'colunaEditar' => true, 'colunaEditarJSCallback' => 'colunaEditarCallbackJS_T266_Quantidade', 'colunaEditarInsert' => true, 'colunaExibir' => $incluirLote));
		}

		$pesquisaGridExtra = <<<EOT
			left join D049 on D049_D001_Id=D001_Id
			left join D041 on D041_D049_Id=D049_Id
			left join D009 on D009_D049_Id=D049_Id
			left join D082 on D082_Id=D049_D082_Id
			left join D037 on D037_Id=D001_D037_Id
			left join D060 on D060_Id=D001_D060_Id
			left join C008 on C008_Id=D001_C008_Id
			left join D003 on D003_Id=D001_D003_Id
			left join D002 on D002_Id=D001_D002_Id
			left join D005 on D005_Id=D001_D005_Id
			left join T066 on T066_D009_Id=D009_Id
			left join D004 on D004_Id=T066_D004_Id
			LEFT JOIN D083 on D083_D001_Id=D001_Id AND D083_D024_Id='{$mT005['T005_D024_Id']}'
			LEFT JOIN D144 ON D144_D049_Id=D049_Id
			left join C007 on C007_Id=D009_C007_Id_Comprador
			left join D001A on D001A_D001_Id=D001_Id
			LEFT JOIN T266 on T266_D009_Id=D009_Id AND T266_D024_Id='{$mT005['T005_D024_Id']}' AND T266_C007_Id='{$g['usuarioAtual']}'
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
			'D009_Id||C008_Id||D060_Id||foto||D001_Codigo_Produto||D049_Modelo||D083_Codigo_Produto_Fornecedor||D001_Descricao_Produto||D001_Descricao_Ingles||D001_Aplicacao||substr(D082_Marca,1,15) as D082_Marca||D037_Unidade||D005_Classificacao_Fiscal||D009_Preco_1||D009_Preco_2||D009_Preco_3||D009_Quantidade_Estoque||D009_Quantidade_Estoque_Liquido as Pedido||(select sum(T066_Quantidade_Estoque) from T066 left join D004 on D004_Id=T066_D004_Id  left join D146 ON D146_Id = D004_D146_Id where T066_D009_Id=D009_Id and UPPER(D146_Descricao)="LOJA") as Estoque||T066_Quantidade_Estoque||D009_Quantidade_Estoque_Fora||D009_Quantidade_Estoque_Similar||"" as retornaPrecoVenda||T266_Quantidade||D009_Quantidade_OC||D004_Local||substr(C007_Primeiro_Nome,1,10) as C007_Primeiro_Nome||D001_Observacao_Compra||D001_Especificacoes||D049_Id||D001_Flag_Pre_Cadastro||D144_Id||D060_Descricao_Grupo||D001_Id||"' . $T005_Id . '" as T005_Id||D009_Quantidade_Estoque_Tabela(D009_Id)' . $qtdeConversaoCampo,	// campos
			$pesquisaGridExtra,																								              // extras sql
			'Pesquisa de Produto',																										  // título da janela
			'Id,Dep,Setor,Foto,Código,Sub-item,Cód.Cli,Descrição,Descrição Inglês,Referência,Marca,UN,NCM,Preço,Preço 2,Preço 3,Físico,Líquido,Loja,Locação,Fora,Similar,Preço,Quantidade,OC,Locação,Comprador,Observação Venda,Específicações,D049_Id,D001_Flag_Pre_Cadastro,D144_Id,Setor,D001_Id,T005_Id,Est.Tab' . $qtdeConversaoDesc,	  // descrição dos campos
			'campoProcuraProduto',																					             		 // callback js
			$g['divId'],																									              // divId
			array($T005_Id, $mT005['T005_D024_Id']),   																					  // parametro extra
			$listaCampos,
			'D009_Id||D049_Id||foto||D009_Preco_1||D009_Preco_2||D009_Preco_3||D009_Quantidade_Estoque||D009_Quantidade_Estoque_Liquido||D144_Id||C008_Id||D060_Id||D009_Quantidade_Estoque_Real||T066_Quantidade_Estoque||D009_Quantidade_Estoque_Fora||D009_Quantidade_OC||D001_Flag_Pre_Cadastro||D144_Id||D001_Id', // Campos que são ignorados na cláusula where do pesquisaGrid
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

        $sqlVeiculo = mysql_query("select T005_D024_Id,T005_D116_Id from T005 where T005_Id='{$r_T005_Id}'");
		$resultVeiculo = mysql_fetch_array($sqlVeiculo);
		//Buscas destinos, foi centralizado nesta classe e vai valer para todos os locais
		$destinoProdutos = $CAD002->retornaDestinoProdutos(false, 'V');

		// novo formulário
		$form = new Formulario('ven002FormProdutosForm');

		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'Procura_Produto' => array('name' => 'Procura_Produto' , 'label' => 'Produto', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGrid, 'length' => array(20,50), 'reject' => true),
					'Marca' => array('name' => 'Marca', 'label' => 'Marca', 'type' => 'text', 'length' => array(20,255), 'reject' => true, 'disabled' => true),
					'T006_Codigo_Substituto' => array('name' => 'T006_Codigo_Substituto', 'label' => 'Código cliente', 'type' => 'text', 'length' => array(20,255), 'align' => 'right', 'style' => 'float:right;'),
					'T006_Descricao_Produto' => array('name' => 'T006_Descricao_Produto', 'label' => 'Descrição', 'type' => 'text', 'length' => array(64, 120), 'readonly' =>  $readOnlyDescricao),
					'T006_Quantidade' => array('name' => 'T006_Quantidade', 'label' => 'Quantidade', 'type' => 'moeda', 'length' => array(20, 30), 'extra' => "onchange=\"gatilhoPrecoPedidoPorQtd(this, \$('#T006_Quantidade').val(),\$('#T006_T005_Id').val(), \$('#T006_D009_Id').val(), \$('#estoqueLiquido').val(), '{$g['divId']}');\""),				
					'T006_D037_Id' => array('name' => 'T006_D037_Id', 'label' => '', 'type' => 'select', 'readonly'=>$readOnlyUnidade, 'type' => 'select', 'align' => 'right', 'style' => 'float:right;', 'extra' => $extraUnidade, 'select' =>
					   	gGeraSelect('D037', 'D037_Id', 'D037_Unidade', '', true)
					),
					'T006_Valor_Preco_Sem_Desconto_Unitario' => array('name' => 'T006_Valor_Preco_Sem_Desconto_Unitario', 'label' => 'Preço Unit.', 'type' => 'moeda', 'length' => array(20, 30), 'casasDecimais' => $mT005['T005_Casas_Decimais_Preco']),
					'PrecoMinimo' => array('name' => 'PrecoMinimo', 'type' => 'moeda', 'length' => array(20, 30), 'reject' => true, 'readonly' => true, 'style' => 'color: var(--BGVermelho); text-align: right;', 'label' => 'Prc. Mínimo'),
					'T006_Numero_Pedido_Compra' => array('name' => 'T006_Numero_Pedido_Compra', 'label' => 'OC/Item', 'type' => 'text', 'length' => array(20, 60)),
					'T006_Item_Pedido_Compra' => array('name' => 'T006_Item_Pedido_Compra', 'type' => 'text', 'length' => array(20, 30)),
					'T006_Percentual_Desconto' => array('name' => 'T006_Percentual_Desconto', 'label' => '%', 'type' => 'moeda', 'length' => array(15, 30), 'reject'=>true),
					'T006_D005_Id' => array('name' => 'T006_D005_Id', 'label' => 'NCM', 'type' => 'hidden', 'readonly' => true, 'align' => 'right', 'extra'=>'style="width:95px;"', 'extra' => "onfocus='this.initialSelect = this.selectedIndex;' onchange='this.selectedIndex = this.initialSelect;'", 'select' =>
					    gGeraSelect('D149', 'D005_Id', 'D005_Classificacao_Fiscal, D005_Flag_Aplicacao, D149_Aliquota_IPI', "LEFT JOIN D005 ON D149_D005_Id=D005_Id AND D149_C004_Id='{$g['empresaAtual']}' WHERE D005_Flag_Ativo='S' ORDER BY D005_Classificacao_Fiscal, D005_Observacao", true)
					),
					'T006_Aliquota_IPI' => array('name' => 'T006_Aliquota_IPI', 'label' => 'IPI', 'align' => 'right', 'type' => 'moeda', 'length' => array(5,20), 'style' => 'float:right;'),
					//'T006_Numero_Pedido_Compra' => array('name' => 'T006_Numero_Pedido_Compra', 'label' => 'Número OC', 'type' => 'moeda', 'length' => array(20, 30), 'align' => 'right', 'style' => 'float:right;'),
					//'T006_Item_Pedido_Compra' => array('name' => 'T006_Item_Pedido_Compra', 'label' => 'Item OC', 'type' => 'moeda', 'length' => array(20, 30)),
					//'T006_IPV' => array('name' => 'T006_IPV', 'label' => 'IPV', 'type' => 'moeda', 'length' => array(20, 30), 'align' => 'right', 'style' => 'float:right;'),
					'T006_Observacao' => array('name' => 'T006_Observacao', 'label' => 'Observação', 'type' => 'text', 'length' => array(64, 120)),
					'T006_Observacao_Entrega' => array('name' => 'T006_Observacao_Entrega', 'label' => 'Entrega dias', 'type' => 'text', 'length' => array(64, 120)),
					'ambiente' => array('name' => "ambiente_{$r_T005_Id}", 'label' => 'Ambiente', 'type' => 'text', 'length' => array(30, 60), 'reject' => true, 'extra' => 'onblur="T006_Quantidade_Focus();"'),
					'T006_Codigo_Local' => array('name' => 'T006_Codigo_Local', 'label' => 'Ambiente', 'type' => 'text', 'type' => 'hidden'),
					'idVeiculoPedido' => array('name' => 'idVeiculoPedido', 'label' => 'Veículo', 'type' => 'select','reject' => true,'extra'=>'style="width:190px;"',
						'select'=> gGeraSelect('D116', 'D116_Id', 'D116_Placa,D133_Modelo,D134_Cor', 'LEFT JOIN D133 on D133_Id=D116_D133_Id LEFT JOIN D134 on D134_Id=D116_D134_Id WHERE D116_D024_Id=(SELECT T005_D024_Id FROM T005 WHERE T005_Id='.@$r_T005_Id.')', true,@$resultVeiculo['T005_D116_Id']),
						'selectRefresh' => array('D116', 'D116_Id', 'D116_Placa,D133_Modelo,D134_Cor', ' LEFT JOIN D133 on D133_Id=D116_D133_Id LEFT JOIN D134 on D134_Id=D116_D134_Id WHERE D116_D024_Id=(SELECT T005_D024_Id FROM T005 WHERE T005_Id='.@$r_T005_Id.')', true,@$resultVeiculo['T005_D116_Id'])
					),
					'ven001FormVeiculosProdutoPedidoBtnNew' => array('name' => 'ven001FormVeiculosProdutoPedidoBtnNew', 'type'=>'submit', 'reload' => false, 'value' => 'Veículos', 'align' => 'right','reject' => true),
            		// Campos Hidden
					'T006_T005_Id' => array('name' => 'T006_T005_Id', 'label' => '', 'type' => 'hidden', 'value' => @$r_T005_Id),
					'T006_C004_Id' => array('name' => 'T006_C004_Id', 'label' => '', 'type' => 'hidden', 'value' => $g['empresaAtual']),
					'T006_Codigo_Produto' => array('name' => 'T006_Codigo_Produto', 'label' => '', 'type' => 'hidden'),
					'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true),
					'T006_D009_Id' => array('name' => 'T006_D009_Id', 'label' => 'T006_D009_Id', 'type' => 'hidden'),
					'UltimoPrecoVenda' => array('name' => 'UltimoPrecoVenda', 'label' => "<a href=\"#\" tabindex=-1  onclick=\"abrirJanela(event, divIdRootAberto(), gridPegarDivId(\$(this)), unique(), 'Histórico', '.: Histórico :.', '/ven/ven001/content/ven001ContentHistoricoProduto/', '&acaoId=' + encodeURIComponent(\$('#{$g['divId']} .itemForm-T006_D009_Id').val()), [900,650]); return false;\">Histórico</a>", 'type' => 'textarea', 'reject'=>true, 'readonly'=>true),
					'D001_Id' => array('name' => 'D001_Id', 'label' => 'D001_Id', 'type' => 'hidden', 'reject'=>true),
					'T006_D022_Id' => array('name' => 'T006_D022_Id', 'label' => 'Transportadora', 'type' => 'select', 'select' => 
		            	gGeraSelect('D022', 'D022_Id', 'D022_Nome_Empresa', '', true)
					),
					'T006_T208_Id' => array('name' => 'T006_T208_Id', 'label' => 'Rota', 'type' => 'select', 'select' => 
		            	gGeraSelect('T208', 'T208_Id', 'T208_Rota', '', true)
					),
					'T006_Hora_Entrega' => array('name' => 'T006_Hora_Entrega', 'label' => 'Hora Entrega', 'type' => 'tempo', 'length' => array(20, 30)),
					//'NcmProduto' => array('name' => 'NcmProduto', 'label' => 'NcmProduto','reject' => 'true', 'type' => 'hidden'),
					'T006_D006_Id' => array('name' => 'T006_D006_Id', 'label' => 'CFOP', 'restricao' => false, 'type' => 'select', 'readonly'=>true, 'extra' => "onfocus='this.initialSelect = this.selectedIndex;' onchange='this.selectedIndex = this.initialSelect;'",
					    'select' => gGeraSelect('D006','D006_Id','D006_Codigo_CFOP,D006_Descricao',' WHERE IFNULL(D006_Flag_Inativo,"N")!="S" AND D006_Flag_Departamento="COMERCIAL" ORDER BY D006_Codigo_CFOP DESC', true),
					),
					'T006_Flag_Destino_Produto' => array('name' => 'T006_Flag_Destino_Produto','label'=>'Destino', 'type' => 'select', 
						'select' => $destinoProdutos
					),
					'estoqueLiquido' => array('name' => 'estoqueLiquido', 'label' => "<a href=\"#\" tabindex=-1 onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Pedidos', '/crm/crm001/content/crm001ContentPedidosProduto/', '&acaoId=' + encodeURIComponent(\$('#{$g['divId']} .itemForm-T006_D009_Id').val()), [700,400]); return false;\">Est. Liq.</a>", 'align' => 'right', 'type' => 'moeda', 'length' => array(5,20), 'style' => 'float:right; text-align:right;', 'reject' => true, 'readonly'=>true),
					'estoqueLoja' => array('name' => 'estoqueLoja', 'label' => 'Est. Loja', 'align' => 'right', 'type' => 'moeda', 'length' => array(5,20), 'style' => 'float:right; text-align:right;', 'reject' => true, 'readonly'=>true),
					'estoqueTotal' => array('name' => 'estoqueTotal', 'label' => 'Est. Total', 'align' => 'right', 'type' => 'moeda', 'length' => array(5,20), 'style' => 'float:right; text-align:right;', 'reject' => true, 'readonly'=>true),
					'ordemCompra' => array('name' => 'ordemCompra', 'label' => "<a href=\"#\" tabindex=-1 onclick=\"abrirJanela(event, divIdRootAberto(), gridPegarDivId(\$(this)), unique(), 'Ordem de Compra', '.: Ordem de Compra :.', '/ven/ven001/content/ven001contentProdutoOC/', '&acaoId=' + encodeURIComponent(\$('#{$g['divId']} .itemForm-T006_D009_Id').val()), [700,400]); return false;\">OC</a>", 'align' => 'right', 'type' => 'moeda', 'length' => array(5,20), 'style' => 'float:right; text-align:right;', 'reject' => true, 'readonly'=>true),
					'estoqueFora' => array('name' => 'estoqueFora', 'label' => "<a href=\"#\" tabindex=-1 onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Estoque Fora', '/cad/cad002/content/cad002ContentEstoqueExterno/', '&acaoId=' + encodeURIComponent(\$('#{$g['divId']} .itemForm-D001_Id').val()) + '&T005_Id=' + encodeURIComponent('{$r_T005_Id}'), [700,400]); return false;\">Est. Fora</a>", 'type' => 'moeda', 'length' => array(5,20), 'reject' => true, 'readonly'=>true, 'style' => 'float:right; text-align:right;'),
					'precoTabela' => array('name' => 'precoTabela', 'label' => 'Prc. Tabela', 'type' => 'moeda', 'length' => array(5,20), 'reject' => true, 'readonly'=>true, 'style' => 'float:right; text-align:right;'),
					'enviado' => array('name' => 'enviado', 'label' => '', 'type' => 'hidden', 'length' => array(5,20), 'reject' => true, 'value' => ''),
					'T006_T208_Id' => array('name' => 'T006_T208_Id', 'label' => 'Rota', 'type' => 'select', 'select' => gGeraSelect('T208', 'T208_Id', 'T208_Rota', '')),
					'T006_D022_Id' => array('name' => 'T006_D022_Id', 'label' => 'Transportador', 'type' => 'select', 'select' => gGeraSelect('D022', 'D022_Id', 'D022_Nome_Empresa', '')),
					'T006_Data_Entrega' => array('name' => 'T006_Data_Entrega', 'label' => 'Data Entrega', 'type' => 'data'),
					'D001_Peso_Unitario_Bruto' => array('name' => 'D001_Peso_Unitario_Bruto', 'label' => 'Peso Bruto', 'type' => 'moeda', 'length' => array(20, 30), 'readonly' => true),
				)
			)
		);
		// Botão Limpar / Novo Registro
		if (!empty($acaoId)) {
			$formArray['campos']['geral']['ven002FormProdutosBtnNew'] = array('name' => 'ven002FormProdutosBtnNew', 'type'=>'submit', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		}
		if(!isset($g['c029Ids'][187])){
			$incluirProduto = true;
			$incluirProdutoStyle = 'color:#ada8a8';
		}

		if ($g['C031']['utilizaQuantidadeConversao'] === 'S') {
			$formArray['campos']['geral']['usaConversao']               = array('name' => 'usaConversao', 'label' => 'usaConversao', 'type' => 'hidden', 'reject' => true);
			$formArray['campos']['geral']['T006A_Quantidade_Conversao'] = array('name' => 'T006A_Quantidade_Conversao', 'label' => 'Qtde Conversão', 'type' => 'moeda', 'length' => array(20, 30), 'readonly '=> true, 'extra' => "onchange=\"calculaQuantidadeConversao(this, '{$g['divId']}');\"");
			$formArray['campos']['geral']['D001_Peso_Unitario_Kg']      = array('name' => 'D001_Peso_Unitario_Kg', 'label' => 'D001_Peso_Unitario_Kg', 'type' => 'hidden', 'reject' => true);
			$formArray['campos']['geral']['T006_Quantidade']['extra']   = "onchange=\"calculaQuantidade(this, '{$g['divId']}');\"";
		}

		$formArray['campos']['geral']['ven002FormProdutosBtnAdd'] = array('name' => 'ven002FormProdutosBtnAdd', 'type'=>'button', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['geral']['ven002FormProdutosBtnCln'] = array('name' => 'ven002FormProdutosBtnCln', 'type'=>'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['geral']['ven002BtnCadastrarProduto'] = array('name' => 'ven002BtnCadastrarProduto', 'type'=>'button', 'reload' => false, 'value' => 'Pré-Cadastrar Produto', 'reject' => true, 'disabled' => "{$incluirProduto}", 'style' => "$incluirProdutoStyle");
		$formArray['campos']['geral']['T006A_Altura'] = array('name' => 'T006A_Altura', 'type' => 'hidden', 'reject' => true, 'value' => '0.00000');
		$formArray['campos']['geral']['T006A_Largura'] = array('name' => 'T006A_Largura', 'type' => 'hidden', 'reject' => true, 'value' => '0.00000');
		$formArray['campos']['geral']['T006A_Comprimento'] = array('name' => 'T006A_Comprimento', 'type' => 'hidden', 'reject' => true, 'value' => '0.00000');
		$formArray['campos']['geral']['T006A_Resultado'] = array('name' => 'T006A_Resultado', 'type' => 'hidden', 'reject' => true, 'value' => '0.00000');
		$form->definirFormularioArray($formArray);

		echo <<<EOT
		    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				var formCalculadoraQuantidadePedido = $('#{$g['divId']}');
				formCalculadoraQuantidadePedido.unbind('calculadoraQuantidade:resultado.ven002Inclusao').bind('calculadoraQuantidade:resultado.ven002Inclusao', function(_, medidas) {
					formCalculadoraQuantidadePedido.find('#T006A_Altura').val(medidas.altura);
					formCalculadoraQuantidadePedido.find('#T006A_Largura').val(medidas.largura);
					formCalculadoraQuantidadePedido.find('#T006A_Comprimento').val(medidas.comprimento);
					formCalculadoraQuantidadePedido.find('#T006A_Resultado').val(medidas.resultado);
					formCalculadoraQuantidadePedido.find('#T006_Quantidade').val(medidas.resultado.replace('.', ',')).trigger('change');
				});
				$.getScript('/hardness3/static/js/calculadoraQuantidade.js').done(function() {
					configurarCalculadoraQuantidade({participarTab: {$calculadoraQtdNavegaTab}});
					inicializarCalculadoraQuantidade({formulario: formCalculadoraQuantidadePedido, formId: '{$g['divId']}', campo: '#T006_D037_Id', antesDe: '#T006_D037_Id_refreshBtn', altura: '#T006A_Altura', largura: '#T006A_Largura', comprimento: '#T006A_Comprimento', resultado: '#T006A_Resultado', divIdRoot: '{$g['divIdRoot']}', divIdParent: '{$g['divId']}'});
				});
				$('#ven002BtnCadastrarProduto').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Pré-cadastrar Produto', '/cad/cad002/content/cad002ContentPreCadastro/', '&T005_Id={$r_T005_Id}&divForm={$g['divId']}', [600,430]);
					return false;
				});
				$('#ven001FormVeiculosProdutoPedidoBtnNew').unbind('click');
				$('#ven001FormVeiculosProdutoPedidoBtnNew').unbind('click').bind('click', function(data) {
					var id = '{$resultVeiculo['T005_D024_Id']}';
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
			$D009_Id = $form->campoValorEnviado('T006_D009_Id');
			$T006_Quantidade = $form->campoValorEnviado('T006_Quantidade');
			if($D009_Id != '' && $g['C031']['informarEstoqueInsuficiente'] == 'S' && $form->campoValorEnviado('enviado') == '') {
				echo <<<EOT
				    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				    		$.getJSON('/ven/ven002/form_func-ajax/ven002VerificarEstoquePedido/?ajax=true&D009_Id=' + encodeURIComponent("{$D009_Id}") + '&T006_Quantidade=' + encodeURIComponent('{$T006_Quantidade}') + '&callback=?', function(request) {
					    		if(request.code){
					    			$('#{$g['divId']} #enviado').val('true');
					    			$('#ven002FormProdutosBtnAdd').trigger('click');
					    		} else {
						    		var buttons = {
										"Continuar": function() {
											$('#{$g['divId']} #enviado').val('true');
											$('#ven002FormProdutosBtnAdd').trigger('click');
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
					$T006_Codigo_Produto = $form->campoValorEnviado('T006_Codigo_Produto');
	                $T006Codigo = mysql_query("SELECT T006_Codigo_Produto FROM T006 WHERE T006_Codigo_Produto='{$T006_Codigo_Produto}' AND T006_T005_Id='{$T005_Id}'");
					if($g['C031']['permitirProdutosDuplicadosVenda'] == 'N' && mysql_num_rows($T006Codigo) > 0){
						echo gGeraAlertMsg('Erro', "Já existe um item com o código ".$T006_Codigo_Produto." neste pedido. Por favor, selecione outro produto.");
					} else {
						$T006_Id = false;
						$T006_Codigo_Local = $form->campoValorEnviado('ambiente');
						$form->campoSetarValor('T006_Codigo_Local', $T006_Codigo_Local, true);
						$retorno = $VEN002->incluirProduto($form);
						if ($retorno !== true) {
							echo gGeraAlertMsg('Erro', $retorno);	
						} else {
							echo <<<EOT
									<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
										var T006_Codigo_Local = '{$T006_Codigo_Local}';
										divRefresh('{$form->campoValorEnviado('divReload')}', true, false, false, '{$T006_Id}', 'T006_Id'); // grid
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
		 * Formulário Serviços
		 */
	case 'ven002form03':
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$r_T005_Id = isset($r_ven002FormServicosForm) ? $r_ven002FormServicosForm['T147_T005_Id'] : $r_T005_Id;

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
		$form_tabela = 'T147';
		$form_campo_pk = 'T147_Id';
        $form_parent = 'T005_Id';
        $form_parent_valor = $r_T005_Id;
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = array('ven002FormServicosBtnAdd', 'ven002FormServicosBtnCln', 'ven002FormServicosBtnNew');
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/ven/ven002/form/ven002form03/';

		//form editor
		$form_editor = array('geral' => '{"Procura_Tipo":{"top":7,"left":53,"width":248,"hidden":0},"T147_Titulo":{"top":30,"left":58,"width":264,"hidden":0},"ven001FormVeiculosBtnNew":{"top":166,"left":384,"width":null,"hidden":0},"ven002FormServicosBtnCln":{"top":189,"left":240,"width":null,"hidden":0},"T147_Descricao":{"top":53,"left":34,"width":264,"hidden":0},"ven002FormServicosBtnAdd":{"top":189,"left":304,"width":null,"hidden":0},"T147_Valor_Unitario":{"top":122,"left":40,"width":72,"hidden":0},"T147_Tempo_Estimado":{"top":145,"left":6,"width":75,"hidden":0},"T147_D116_Id":{"top":168,"left":48,"width":269,"hidden":0}}');

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
			'D112',																								// tabela
			'D112_Id',																							// chave primaria
			'D112_Titulo, D112_Descricao',																		// campos
			'',																									// extras sql
			'Pesquisa de Serviço',																				// título da janela
			'Serviço,Descrição',																					// descrição dos campos
			'campoProcuraServicoPedido',																				// callback js
			$g['divId']																							// divId
		);
        $sqlVeiculo = mysql_query("select T005_D024_Id,T005_D116_Id from T005 where T005_Id='{$r_T005_Id}'");
		$resultVeiculo = mysql_fetch_array($sqlVeiculo);
		// novo formulário
		$form = new Formulario('ven002FormServicosForm');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'Procura_Servico' => array('name' => 'Procura_Servico' , 'label' => 'Serviço', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGrid, 'length' => array(50,30), 'reject' => true),
					'T147_T212_Id' => array('name' => 'T147_T212_Id','label'=>'Título','type' => 'select',
						'select' => gGeraSelect('T212', 'T212_Id', 'T212_Nome', '', true)
					),
					'T147_Titulo' => array('name' => 'T147_Titulo','type' => 'hidden'),
					'T147_D112_Id' => array('name' => 'T147_D112_Id','type' => 'hidden', 'label'=>'T147_D112_Id'),
					'T147_Descricao' => array('name' => 'T147_Descricao','label'=>'Descrição','type' => 'textarea', 'length' => array(2,46)),
					//'T147_Quantidade' => array('name' => 'T147_Quantidade','label'=>'Quantidade','type' => 'text', 'length' => array(10,8)),
					//'T147_Valor_Unitario' => array('name' => 'T147_Valor_Unitario','label'=>'Valor Unitário','type' => 'moeda', 'length' => array(15,10)),
					'T147_Valor_Unitario' => array('name' => 'T147_Valor_Unitario','label'=>'Valor Total','type' => 'moeda', 'length' => array(15,10)),
					'T147_Tempo_Estimado' => array('name' => 'T147_Tempo_Estimado','label'=>'Tempo Estimado','type' => 'text', 'length' => array(10,10)),
					'T147_D110_Id' => array('name' => 'T147_D110_Id','label'=>'Serviço','type' => 'hidden', 'length' => array(10,10)),
					'T147_D116_Id' => array('name' => 'T147_D116_Id', 'label'=>'Veículo', 'extra'=>'style="width:280px;"', 'type' => 'select',
						'select' => gGeraSelect('D116', 'D116_Id','D116_Placa,D133_Modelo,D134_Cor', ' LEFT JOIN D133 on D133_Id=D116_D133_Id LEFT JOIN D134 on D134_Id=D116_D134_Id WHERE D116_D024_Id=(SELECT T005_D024_Id FROM T005 WHERE T005_Id='.@$r_T005_Id.')', true, @$resultVeiculo['T005_D116_Id']),
						'selectRefresh' => array('D116', 'D116_Id', 'D116_Placa,D133_Modelo,D134_Cor', ' LEFT JOIN D133 on D133_Id=D116_D133_Id LEFT JOIN D134 on D134_Id=D116_D134_Id WHERE D116_D024_Id=(SELECT T005_D024_Id FROM T005 WHERE T005_Id='.@$r_T005_Id.')',true, @$resultVeiculo['T005_D116_Id'])
					),
					'ven001FormVeiculosBtnNew' => array('name' => 'ven001FormVeiculosBtnNew', 'type'=>'submit', 'reload' => false, 'value' => 'Veículos', 'align' => 'right', 'style' => 'float:right;', 'reject' => true),
		            //'T147_Hora_Inicio' => array('name' => 'T147_Hora_Inicio','label'=>'Hora Início','type' => 'text', 'length' => array(10,10)),
		            //'T147_Hora_Fim' => array('name' => 'T147_Hora_Fim','label'=>'Hora Fim','type' => 'text', 'length' => array(10,10)),
					'T147_T005_Id' => array('name' => 'T147_T005_Id', 'type' => 'hidden', 'value'=> @$r_T005_Id, 'length' => array(50,30)),
					'T147_Quantidade' => array('name' => 'T147_Quantidade','label'=>'Quantidade', 'type' => 'text'),
					//'T147_Valor_Unitario' => array('name' => 'T147_Valor_Unitario','label'=>'Valor total', 'value'=>'1', 'type' => 'hidden'),
					'T147_Status' => array('name' => 'T147_Status','label'=>'Status', 'value'=>'P', 'type' => 'hidden'),
					'T147_Hora_Inicio' => array('name' => 'T147_Hora_Inicio','label'=>'Hora Início', 'value'=>'0000-00-00 00:00:00', 'type' => 'hidden'),
					'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true),
				)
			)
		);
		// Botão Limpar / Novo Registro
		if (!empty($acaoId)) {
			$formArray['campos']['geral']['ven002FormServicosBtnNew'] = array('name' => 'ven002FormServicosBtnNew', 'type'=>'submit', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		}
		$formArray['campos']['geral']['ven002FormServicosBtnCln'] = array('name' => 'ven002FormServicosBtnCln', 'type'=>'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['geral']['ven002FormServicosBtnAdd'] = array('name' => 'ven002FormServicosBtnAdd', 'type'=>'button', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$form->definirFormularioArray($formArray);

		$sql = "SELECT T005_D024_Id FROM T005 WHERE T005_Id = '{$r_T005_Id}';";
		$resultado = mysql_query($sql);
		$row = mysql_fetch_array($resultado);
		echo <<<EOT
		    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		    	$('#ven001FormVeiculosBtnNew').unbind('click');
				$('#ven001FormVeiculosBtnNew').unbind('click').bind('click', function(data) {
					var id = '{$row['T005_D024_Id']}';
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
						$retorno = $VEN002->insereServico($form, $g['mysqlLastId']);
						$erro = ($retorno === true) ? '' : "alert('$retorno');";
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
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk, false, $form_parent, $form_parent_valor);


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

		/**
		 * Formulário Materiais
		 */
	case 'ven002form04':
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
		$form_tabela = 'T149';
		$form_campo_pk = 'T149_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = array('ven002FormMateriaisBtnAdd', 'ven002FormMateriaisBtnCln', 'ven002FormMateriaisBtnNew');
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/ven/ven002/form/ven002form04/';
		
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
		$form = new Formulario('form_fin002_form4');

		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
			'editar_material' => array(
				'ven002FormMateriaisPesquisa' => array('name' => 'ven001FormMateriaisPesquisa' , 'label' => 'Material', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGrid, 'length' => array(20,30), 'reject' => true),
				'Descricao_Produto' => array('name' => 'Descricao_Produto', 'label' => 'Descrição', 'type' => 'text', 'length' => array(64, 120),'reject' => true),
				'ven002_Marca' => array('name' => 'ven001_Marca', 'label' => 'Marca', 'type' => 'text', 'length' => array(20,255), 'reject' => true),			
		
				'T149_T147_Id' => array('name' => 'T149_T147_Id','label'=>'', 'type' => 'hidden','value' => @$r_T147_Id, 'length' => array(60,20)),
				'T149_D009_Id' => array('name' => 'T149_D009_Id', 'label' => 'T149_D009_Id', 'type' => 'hidden'),
				'T149_Quantidade' => array('name' => 'T149_Quantidade','label'=>'Quantidade', 'type' => 'text', 'length' => array(10,10)),
				'T149_D037_Id' => array('name' => 'T149_D037_Id', 'label'=>'Unidade', 'extra'=>'style="width:182px;"', 'type' => 'select', 'select'=>
						gGeraSelect('D037', 'D037_Id', 'D037_Unidade', 'order by D037_Unidade', true),
				),
				//'content_form4_btn' => array('name' => 'content_form4_btn', 'type'=>'submit','reload'=>false,'value'=>'Incluir','align' => 'right', 'reject'=>true),
				'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true),
		
		)
		)
		);
		
		// Botão Limpar / Novo Registro
		if (!empty($acaoId)) {
			 $formArray['campos']['editar_material']['ven002FormMateriaisBtnNew'] = array('name' => 'ven002FormMateriaisBtnNew', 'type'=>'button', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		}
		$formArray['campos']['editar_material']['ven002FormMateriaisBtnCln'] = array('name' => 'ven002FormMateriaisBtnCln', 'type'=>'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['editar_material']['ven002FormMateriaisBtnAdd'] = array('name' => 'ven002FormMateriaisBtnAdd', 'type'=>'submit', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
	

		$form->definirFormularioArray($formArray);

		/**
			* Na maioria dos casos não será necessário alterar nada daqui para baixo
			* Somente em casos especiais, para alterar pequenos detalhes ou modificações do visual
			*/


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
						// Atualiza o Grid
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
						// Atualiza o Grid
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

		/**
			* Botões
			*/
		// Definição dos botões
		$botoesMid = '';
		$botoesEsq = '';
		$botoesDir = '';
		//$botoesDir .= '<button type="submit" id="content_form4_btn">Enviar</button>';
		//$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');

		break;
			
	case 'ven002form05':
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
		$form_tabela = 'T148';
		$form_campo_pk = 'T148_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = array('ven002FormProcessosBtnAdd', 'ven002FormProcessosBtnCln', 'ven002FormProcessosBtnNew');
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/ven/ven002/form/ven002form05/';

		// novo formulário
		$form = new Formulario('form_fin002_form5');

		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
			'geral' => array(
			'T148_T147_Id' => array('name' => 'T148_T147_Id','label'=>'', 'type' => 'hidden','value' => @$r_T147_Id),
			'T148_Titulo' => array('name' => 'T148_Titulo','label'=>'Título', 'type' => 'text','length' => array(50,30)),
			'T148_Descricao' => array('name' => 'T148_Descricao','label'=>'Descrição', 'type' => 'textarea','length' => array(3,47)),
			//'content_form5_btn' => array('name' => 'content_form5_btn', 'type'=>'submit','reload'=>false,'value'=>'Incluir','align' => 'right', 'reject'=>true),
			'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true),
		
		)
		)
		);
		
			// Botão Limpar / Novo Registro
			if (!empty($acaoId)) {
				 $formArray['campos']['geral']['ven002FormProcessosBtnNew'] = array('name' => 'ven002FormProcessosBtnNew', 'type'=>'button', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
			}
			$formArray['campos']['geral']['ven002FormProcessosBtnCln'] = array('name' => 'ven002FormProcessosBtnCln', 'type'=>'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
			$formArray['campos']['geral']['ven002FormProcessosBtnAdd'] = array('name' => 'ven002FormProcessosBtnAdd', 'type'=>'submit', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		

		$form->definirFormularioArray($formArray);

		/**
			* Na maioria dos casos não será necessário alterar nada daqui para baixo
			* Somente em casos especiais, para alterar pequenos detalhes ou modificações do visual
			*/


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
								divRefresh('{$form->campoValorEnviado('divReload')}', true);
								divSetarAcaoId('{$g['divId']}', '');
								divRefresh('{$g['divId']}', true);
							} </script></div>
EOT;
					}
				} else {
					// Insert
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						// Atualiza o Grid
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

		/**
			* Botões
			*/
		// Definição dos botões
		$botoesMid = '';
		$botoesEsq = '';
		$botoesDir = '';
		//$botoesDir .= '<button type="submit" id="content_form5_btn">Enviar</button>';
		//$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');

		break;
		
		case 'ven002FormProdutosEditar':
			global $g;
			
			/**
			* PARTE 1: Variavéis envidas para o formulário
			*/
			// Variável indica qual tab do form será exibida
			$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
			// Utilizada em UPDATE, é o ID do registro que está sendo editado
			$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;

			// Variável utilizada para configurar os campos do ICMS
			$D059_Id = isset($r_D059_Id) ? $r_D059_Id : false;
			if (isset($r_ven002FormProdutosEditar) && $r_ven002FormProdutosEditar['T008_D059_Id']) {
				$D059_Id = $r_ven002FormProdutosEditar['T006_D059_Id'];
			}

			// Permite ou não alterar a descrição do produto e unidade
			$readOnlyDescricao = ($g['C031']['editarDescricaoProdutoVenda'] == 'S') ? false : true;
			$readOnlyUnidade   = ($g['C031']['editarUnidadeProdutoVenda'] == 'S') ? false : true;
			$extraUnidade      = ($g['C031']['editarUnidadeProdutoVenda'] == 'S') ? "" : "onfocus='this.initialSelect = this.selectedIndex;' onchange='this.selectedIndex = this.initialSelect;'";

			$T005 = mysql_query("SELECT T005_D024_Id, 
										T005_Id,
										T005A_Flag_Conversao_Unidade 
								   FROM T006 
							  LEFT JOIN T005 ON T005_Id=T006_T005_Id 
							  LEFT JOIN T005A ON T005_Id=T005A_T005_Id
							      WHERE T006_Id='{$acaoId}'");

			$mT005 = mysql_fetch_array($T005);

			/**
			* PARTE 2: Definição do formulário
			*/
			// Este mesmo form será exibido em multiplas tabs ou não?
			$formMultiTab = true;
			if ($formMultiTab) {
				$formId = (isset($r_ven002FormProdutosForm)) ? $r_ven002FormProdutosForm['formId'] : $r_formId;
			} else {
				$formId = uniqid();
			}
			// A qual tabela este form está relacionada, e a chave primária da tabela
			$form_tabela = 'T006';
			$form_campo_pk = 'T006_Id';
			// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
			$enviar_btnId = 'ven002FormEditarProdutosBtn';
			// Form que será executado ao clicar no botão
			$enviar_acaoHref = '/ven/ven002/form/ven002FormProdutosEditar/';
			

			//form editor
			$camposForm = '';
			if ($g['C031']['utilizaQuantidadeConversao'] === 'S') {
				$camposForm = ',"T006A_Quantidade_Conversao":{"top":76,"left":332.453125,"width":105,"hidden":0}';
			} 

			if($mT005['T005A_Flag_Conversao_Unidade'] != 'S'){
				$posicaoItem = '{"T006_Valor_Preco_Original":{"top":122,"left":27.078125,"width":104,"hidden":0},"T006A_Quantidade_Comercial":{"top":76,"left":322.4375,"width":104,"hidden":1},"Procura_Produto_Edicao":{"top":7,"left":51.75,"width":408,"hidden":0},"Marca":{"top":29.921875,"left":59.65625,"width":88,"hidden":0},"T006A_Preco_Unitario_Comercial":{"top":99,"left":309.34375,"width":104,"hidden":1},"T006_D037_Id":{"top":30,"left":411.046875,"width":63,"hidden":0},"T006_Descricao_Produto":{"top":52.921875,"left":34.265625,"width":424,"hidden":0},"T006_IPV":{"top":260,"left":73.671875,"width":107,"hidden":1},"T006_Quantidade":{"top":75.921875,"left":26.984375,"width":102,"hidden":0},"T006_Valor_Custo_Unitario":{"top":122,"left":21.109375,"width":102,"hidden":0},"T006_Valor_Preco_Sem_Desconto_Unitario":{"top":99,"left":13.890625,"width":102,"hidden":0},"T006_D006_Id":{"top":145,"left":55.40625,"width":432,"hidden":0},"T006_Observacao":{"top":168,"left":32.296875,"width":424,"hidden":0},"T006_T208_Id":{"top":214,"left":67.984375,"width":75,"hidden":1},"T006_D022_Id":{"top":237,"left":21,"width":212,"hidden":1}'. $camposForm .'}';
			} else {
				$posicaoItem = '{"T006_Valor_Preco_Original":{"top":122,"left":27.078125,"width":104,"hidden":0},"T006A_Quantidade_Comercial":{"top":76,"left":34.4375,"width":104,"hidden":0},"Procura_Produto_Edicao":{"top":7,"left":51.75,"width":408,"hidden":0},"Marca":{"top":29.921875,"left":59.65625,"width":88,"hidden":0},"T006A_Preco_Unitario_Comercial":{"top":99,"left":21.34375,"width":104,"hidden":0},"T006_D037_Id":{"top":30,"left":411.046875,"width":63,"hidden":0},"T006_Descricao_Produto":{"top":52.921875,"left":34.265625,"width":424,"hidden":0},"T006_IPV":{"top":260,"left":73.671875,"width":107,"hidden":1},"T006_Quantidade":{"top":99,"left":346.984375,"width":102,"hidden":1},"T006_Valor_Custo_Unitario":{"top":122,"left":21.109375,"width":102,"hidden":0},"T006_Valor_Preco_Sem_Desconto_Unitario":{"top":76,"left":333.890625,"width":102,"hidden":1},"T006_D006_Id":{"top":145,"left":55.40625,"width":432,"hidden":0},"T006_Observacao":{"top":168,"left":32.296875,"width":424,"hidden":0},"T006_T208_Id":{"top":214,"left":67.984375,"width":75,"hidden":1},"T006_D022_Id":{"top":237,"left":21,"width":212,"hidden":1}'. $camposForm .'}';
			}

			$form_editor = array(
			 	'item' 		 => $posicaoItem,
				'ipi'  		 => '{"T006_D005_Id":{"top":7,"left":93,"width":123,"hidden":0},"T006_Aliquota_IPI":{"top":30,"left":99,"width":102,"hidden":0},"T006_Situacao_Tributaria_IPI":{"top":53,"left":19,"width":315,"hidden":0}}',
			 	'icms' 		 => '{"T006_Flag_ST":{"top":7,"left":77.046875,"width":57,"hidden":0},"T006_D059_Id":{"top":30,"left":94.15625,"width":303,"hidden":0},"T006_ST_ICMS_Interno":{"top":53,"left":108.234375,"width":40,"hidden":0},"T006_ICMS_Inter_Estadual":{"top":53,"left":330.609375,"width":40,"hidden":0},"T006_Valor_ICMS_Substituicao_Destino":{"top":76,"left":26.71875,"width":40,"hidden":0},"T006_Percentual_Reducao_ICMS":{"top":76,"left":98.453125,"width":40,"hidden":0},"T006_Percentual_Reducao_ICMS_ST":{"top":76,"left":289.640625,"width":40,"hidden":0},"T006_Valor_Base_ICMS_Substituicao_Destino":{"top":99,"left":70.546875,"width":40,"hidden":0},"T006_Aliquota_ICMS":{"top":99,"left":145.546875,"width":40,"hidden":0},"T006_ST_VA":{"top":99,"left":418.859375,"width":40,"hidden":0},"T006_Modalidade_ICMS":{"top":122,"left":50.125,"width":304,"hidden":0},"T006_Modalidade_ICMS_Substituicao":{"top":145,"left":50.125,"width":304,"hidden":0},"T006_Percentual_Base_Operacao_Propria":{"top":168,"left":56.25,"width":40,"hidden":0},"T006_ICMS_Inter_Estadual_MVA":{"top":191,"left":16.125,"width":120,"hidden":1},"T006_ST_ICMS_Interno_MVA":{"top":214,"left":52.1875,"width":120,"hidden":1},"T006_Preco_Pauta_ST":{"top":237,"left":125.34375,"width":120,"hidden":1}}',
				'pis-cofins' => '{"T006_Aliquota_PIS":{"top":7,"left":109.9666748046875,"width":102,"hidden":0},"T006_Flag_Isento_PIS":{"top":29.98333740234375,"left":108.9666748046875,"width":58,"hidden":0},"T006_Situacao_Tributaria_PIS":{"top":52.98333740234375,"left":30,"width":288,"hidden":0},"T006_Aliquota_COFINS":{"top":75.98332214355469,"left":87.9666748046875,"width":102,"hidden":0},"T006_Flag_Isento_COFINS":{"top":99.00001525878906,"left":108.9666748046875,"width":58,"hidden":0},"T006_Situacao_Tributaria_COFINS":{"top":122.00001525878906,"left":8,"width":288,"hidden":0}}'
			);

		// Campo: Procura_Produto
		$pesquisaGridExtra = <<<EOT
			left join D049 on D049_D001_Id=D001_Id
			left join D041 on D041_D049_Id=D049_Id
			left join D009 on D009_D049_Id=D049_Id
			left join D082 on D082_Id=D049_D082_Id
			left join D037 on D037_Id=D001_D037_Id 
			LEFT JOIN D083 on D083_D001_Id=D001_Id AND D083_D024_Id='{$mT005['T005_D024_Id']}'
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
			'D009_Id,D144_Id',																									              // chave primaria
			'D001_Codigo_Produto||D083_Codigo_Produto_Fornecedor||D001_Descricao_Produto||D082_Marca||D009_Preco_1||D009_Preco_2||D009_Preco_3||D009_Quantidade_Estoque||D009_Quantidade_Pedido(D009_Id,1) as Pedido||D009_Quantidade_Estoque_Liquido||D009_Id as D009Id||D001_Descricao_Ingles||D009_Id||D049_Id||D144_Id',	// campos
			$pesquisaGridExtra,																								              // extras sql
			'Pesquisa de Produto',																										  // título da janela
			'Cód.,Cód.Cli,Descrição,Marca,Preço 1,Preço 2,Preço 3,Estoque Real,Pedidos,Estoque Líq,Estoque MP,Descrição Inglês,Link,D049_Id',	  // descrição dos campos
			'ven002CampoProcuraProdutoEditar',																					          // callback js
			$g['divId'],																									              // divId
			(isset($r_ven002FormProdutosForm) ? $r_ven002FormProdutosForm['T006_T005_Id'] : $mT005['T005_Id']),					              // parametro extra
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

			$T006=mysql_query("select *
			                    from T006
			                   left join D009 on D009_Id=T006_D009_Id
			                   left join D049 on D049_Id=D009_D049_Id
			                   left join D082 on D082_Id=D049_D082_Id
			                   left join T006A on T006A_T006_Id=T006_Id
			                   where T006_Id='$acaoId'");
			$mT006=mysql_fetch_array($T006);

			$readOnlyEdicao = ($g['C031']['gerarAtividadesManipulacaoItens'] == 'S') ? true : false;
			$descricaoCFOP  = ($g['C031']['descricaoCFOPExibida'] == 'P') ? 'D006_Descricao' : 'IF(D006_Descricao_Comercial !="",D006_Descricao_Comercial,D006_Descricao) as D006_Descricao';


			// novo formulário
			$form = new Formulario('ven002FormProdutosForm');
			// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
			$formArray = array(
			'campos' => array(
			'item' => array(
				'Procura_Produto_Edicao' => array('name' => 'Procura_Produto_Edicao' , 'label' => 'Produto', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGrid,'length' => array(30,30), 'reject' => true, 'value' => "{$mT006['T006_Codigo_Produto']}"),
				'T006_Codigo_Produto' => array('name' => 'T006_Codigo_Produto', 'label' => 'CódigoProd', 'type' => 'hidden', 'length' => array(15, 30)),
				'Marca' => array('name' => 'Marca', 'label' => 'Marca', 'type' => 'text', 'length' => array(64,255), 'reject' => true, 'disabled' => true, 'value' => "$mT006[D082_Marca]"),
				'T006_Descricao_Produto' => array('name' => 'T006_Descricao_Produto', 'label' => 'Descrição', 'type' => 'text', 'readonly' => $readOnlyDescricao, 'length' => array(64, 120)),
				'T006_Quantidade' => array('name' => 'T006_Quantidade', 'label' => 'Quantidade', 'type' => 'moeda', 'length' => array(15, 30), 'readonly' => $readOnlyEdicao),
				'T006A_Quantidade_Comercial' => array('name' => 'T006A_Quantidade_Comercial', 'label' => 'Quantidade', 'type' => 'moeda', 'length' => array(15, 30), 'readonly' => $readOnlyEdicao),
				'T006_D037_Id' => array('name' => 'T006_D037_Id', 'label' => 'Unidade', 'type' => 'select', 'align' => 'right', 'readonly' => $readOnlyUnidade, 'extra' => $extraUnidade, 'style' => 'float:right;', 'select' => 
					gGeraSelect('D037', 'D037_Id', 'D037_Unidade', '', true)
				),
				'T006_Valor_Custo_Unitario' => array('name' => 'T006_Valor_Custo_Unitario', 'label' => 'Custo Unitário', 'readonly' => 'true', 'type' => 'hidden', 'length' => array(15, 30)),
				'T006_Valor_Preco_Sem_Desconto_Unitario' => array('name' => 'T006_Valor_Preco_Sem_Desconto_Unitario', 'label' => 'Preço Unitário', 'type' => 'moeda', 'length' => array(15, 30), 'readonly' => $readOnlyEdicao),
				'T006A_Preco_Unitario_Comercial' => array('name' => 'T006A_Preco_Unitario_Comercial', 'label' => 'Preço Unitário', 'type' => 'moeda', 'length' => array(15, 30), 'readonly' => $readOnlyEdicao),
				'T006_IPV' => array('name' => 'T006_IPV', 'label' => 'IPV', 'type' => 'moeda', 'length' => array(15, 30), 'align' => 'right', 'style' => 'float:right;'),
				'T006_D006_Id' => array('name' => 'T006_D006_Id', 'label' => 'CFOP', 'type' => 'select',
					'select' => gGeraSelect('D006','D006_Id',"LPAD(D006_Id[[VIRGULA]]4[[VIRGULA]]0), D006_Codigo_CFOP, {$descricaoCFOP}",' WHERE IFNULL(D006_Flag_Inativo,"N")!="S" AND D006_Flag_Departamento="COMERCIAL"  ORDER BY D006_Codigo_CFOP', true, false, '%1 - %2 - %3'),
				),
				'T006_Observacao' => array('name' => 'T006_Observacao', 'label' => 'Observação', 'type' => 'text', 'length' => array(65, 120)),
				'T006_T208_Id' => array('name' => 'T006_T208_Id', 'label' => 'Rota', 'type' => 'select', 'select' => gGeraSelect('T208', 'T208_Id', 'T208_Rota', '')),
				'T006_D022_Id' => array('name' => 'T006_D022_Id', 'label' => 'Transportador', 'type' => 'select', 'select' => gGeraSelect('D022', 'D022_Id', 'D022_Nome_Empresa', '')),
				// Campos Hidden
				'T006_C004_Id' => array('name' => 'T006_C004_Id', 'label' => '', 'type' => 'hidden'),
				'T006_D009_Id' => array('name' => 'T006_D009_Id', 'label' => '', 'type' => 'hidden'),
				'T006_D082_Id' => array('name' => 'T006_D082_Id', 'label' => '', 'type' => 'hidden'),
				'T006_T005_Id' => array('name' => 'T006_T005_Id', 'label' => '', 'type' => 'hidden', 'value'=>$mT005['T005_Id']),
				'T006A_Altura' => array('name' => 'T006A_Altura', 'label' => '', 'type' => 'hidden', 'reject' => true, 'value' => $mT006['T006A_Altura']),
				'T006A_Largura' => array('name' => 'T006A_Largura', 'label' => '', 'type' => 'hidden', 'reject' => true, 'value' => $mT006['T006A_Largura']),
				'T006A_Comprimento' => array('name' => 'T006A_Comprimento', 'label' => '', 'type' => 'hidden', 'reject' => true, 'value' => $mT006['T006A_Comprimento']),
				'T006A_Resultado' => array('name' => 'T006A_Resultado', 'label' => '', 'type' => 'hidden', 'reject' => true, 'value' => $mT006['T006A_Resultado']),
				'T006_Valor_Preco_Original' => array('name' => 'T006_Valor_Preco_Original', 'label' => 'Preço Tabela', 'type' => 'moeda', 'readonly' => true),
				'formId' => array('name' => 'formId', 'label' => '', 'type' => 'hidden', 'value' => $formId, 'reject' => true),
				'T006_T208_Id' => array('name' => 'T006_T208_Id', 'label' => 'Rota', 'type' => 'select', 'select' => gGeraSelect('T208', 'T208_Id', 'T208_Rota', '')),
				'T006_D022_Id' => array('name' => 'T006_D022_Id', 'label' => 'Transportador', 'type' => 'select', 'select' => gGeraSelect('D022', 'D022_Id', 'D022_Nome_Empresa', '')),
				'T006_Demonstrativo_Preco_Venda' => array('name' => 'T006_Demonstrativo_Preco_Venda', 'label' => 'Demonstrativo', 'type' => 'hidden', 'uppercase' => false),
			),
			//TAB IPI
			'ipi' => array(
			    'T006_D005_Id' => array('name' => 'T006_D005_Id', 'label' => 'NCM', 'type' => 'select',  'select' => 
			        gGeraSelect('D149', 'D005_Id', 'D005_Classificacao_Fiscal, D005_Flag_Aplicacao, D149_Aliquota_IPI', "LEFT JOIN D005 ON D149_D005_Id=D005_Id AND D149_C004_Id='{$g['empresaAtual']}' WHERE D005_Flag_Ativo='S' ORDER BY D005_Classificacao_Fiscal, D005_Observacao", true)
			    ),
			    'T006_Aliquota_IPI' => array('name' => 'T006_Aliquota_IPI', 'label' => 'IPI %', 'type' => 'moeda', 'length' => array(15, 20)),
			    'T006_Situacao_Tributaria_IPI' => array('name' => 'T006_Situacao_Tributaria_IPI', 'label' => 'Situação Tributária IPI', 'type' => 'select',  'select' => 
			        gGeraSelect('D088', 'D088_Codigo_ST', 'D088_Codigo_ST,D088_Descricao', '', true)
			    ),
			),
			//TAB ICMS
			'icms' => array(
			    'T006_Aliquota_ICMS' => array('name' => 'T006_Aliquota_ICMS', 'label' => '% ICMS', 'type' => 'moeda', 'length' => array(15, 20)),
			    'T006_Percentual_Reducao_ICMS' => array('name' => 'T006_Percentual_Reducao_ICMS', 'label' => '% Redução ICMS', 'type' => 'moeda', 'length' => array(15, 20)),
			    'T006_Flag_ST' => array('name' => 'T006_Flag_ST', 'label' => 'Substituição Tributária', 'type' => 'select', 'select' => 
			        array(
			            array('title' => 'Sim', 'value' => 'S'),
			            array('title' => 'Não', 'value' => 'N', 'selected' => true)
			        )
			    ),
			    'T006_ST_ICMS_Interno' => array('name' => 'T006_ST_ICMS_Interno', 'label' => '% ICMS Interno', 'type' => 'moeda', 'length' => array(15, 20)),
				'T006_ST_ICMS_Interno_MVA' => array('name' => 'T006_ST_ICMS_Interno_MVA', 'label' => 'Aliquota ICMS Interno MVA', 'type' => 'moeda', 'length' => array(15, 20), 'align'=>'right', 'style'=>'float:right'),
			    'T006_ICMS_Inter_Estadual' => array('name' => 'T006_ICMS_Inter_Estadual', 'label' => '% ICMS Inter Estadual', 'type' => 'moeda', 'length' => array(15, 20)),
				'T006_ICMS_Inter_Estadual_MVA' => array('name' => 'T006_ICMS_Inter_Estadual_MVA', 'label' => 'Aliquitoa ICMS Inter Estadual MVA', 'type' => 'moeda', 'length' => array(15, 20), 'align'=>'right', 'style'=>'float:right'),
				'T006_ST_VA' => array('name' => 'T006_ST_VA', 'label' => 'MVA', 'type' => 'moeda', 'length' => array(15, 20)),
				'T006_Modalidade_ICMS' => array('name' => 'T006_Modalidade_ICMS', 'label' => 'Modalidade de determinação <br/>da BC do ICMS', 'type' => 'select', 'select' => 
					array(
						array('title' => '...', 'value' => '','selected' => true),
						array('title' => 'Margem Valor Agregado (%)', 'value' => '0'),
						array('title' => 'Pauta (Valor)', 'value' => '1'),
						array('title' => 'Preço Tabelado Máx. (valor)', 'value' => '2'),
						array('title' => 'Valor da Operação', 'value' => '3')
					)
				),
				'T006_Modalidade_ICMS_Substituicao' => array('name' => 'T006_Modalidade_ICMS_Substituicao', 'label' => 'Modalidade de determinação <br/>da BC ao ICMS ST', 'type' => 'select', 'select' => 
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
				'T006_Percentual_Reducao_ICMS_ST' => array('name' => 'T006_Percentual_Reducao_ICMS_ST', 'label' => '% Redução de BC do ICMS ST', 'type' => 'moeda', 'length' => array(10,15)),
				'T006_Percentual_Base_Operacao_Propria' => array('name' => 'T006_Percentual_Base_Operacao_Propria', 'label' => '% da BC operação própria', 'type' => 'moeda', 'length' => array(10,15)),
				'T006_Valor_ICMS_Substituicao_Destino' => array('name' => 'T006_Valor_ICMS_Substituicao_Destino', 'label' => 'Valor do ICMS ST da UF destino', 'type' => 'moeda'),
				'T006_Valor_Base_ICMS_Substituicao_Destino' => array('name' => 'T006_Valor_Base_ICMS_Substituicao_Destino', 'label' => 'Valor da BC do ICMS ST <br/>da UF destino', 'type' => 'moeda'),
				'T006_Preco_Pauta_ST' => array('name' => 'T006_Preco_Pauta_ST', 'label' => 'Preço Pauta', 'type' => 'moeda', 'length' => array(20,10)),
				// 'T006_UF_ICMS_Substituicao_Devido' => array('name' => 'T006_UF_ICMS_Substituicao_Devido', 'label' => 'UF para qual é devido o ICMS ST', 'type' => 'select', 'extra' => 'style="width:300px;"', 'select' => 
				//  	gGeraSelect('D018', 'D018_UF', 'D018_UF,D018_Estado', 'order by D018_Id', true)
				// ),
			),
			//TAB PIS/COFINS
			'pis-cofins' => array(
			    'T006_Aliquota_PIS' => array('name' => 'T006_Aliquota_PIS', 'label' => 'PIS %', 'type' => 'moeda', 'length' => array(15, 20)),
			    'T006_Flag_Isento_PIS' => array('name' => 'T006_Flag_Isento_PIS', 'label' => 'Isento', 'type' => 'select', 'select' => 
			        array(
			            array('title' => 'Sim', 'value' => 'S'),
			            array('title' => 'Não', 'value' => 'N', 'selected' => true)
			        )
			    ),
			    'T006_Situacao_Tributaria_PIS' => array('name' => 'T006_Situacao_Tributaria_PIS', 'label' => 'Situação Tributária PIS', 'extra'=>'style="width:288px;"', 'type' => 'select', 'select' =>
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

			    'T006_Aliquota_COFINS' => array('name' => 'T006_Aliquota_COFINS', 'label' => 'COFINS %', 'type' => 'moeda', 'length' => array(15, 20)),

			    'T006_Flag_Isento_COFINS' => array('name' => 'T006_Flag_Isento_COFINS', 'label' => 'Isento', 'type' => 'select', 'select' => 
			        array(
			            array('title' => 'Sim', 'value' => 'S'),
			            array('title' => 'Não', 'value' => 'N', 'selected' => true)
			        )
			    ),
			    'T006_Situacao_Tributaria_COFINS' => array('name' => 'T006_Situacao_Tributaria_COFINS', 'label' => 'Situação Tributária COFINS', 'extra'=>'style="width:288px;"', 'type' => 'select', 'select' =>
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
			)
			)
			);

			if ($g['C031']['utilizaQuantidadeConversao'] === 'S') {
				$formArray['campos']['item']['usaConversao']               = array('name' => 'usaConversao', 'label' => 'usaConversao', 'type' => 'hidden', 'reject' => true);
				$formArray['campos']['item']['T006A_Quantidade_Conversao'] = array('name' => 'T006A_Quantidade_Conversao', 'label' => 'Qtde Conversão', 'type' => 'moeda', 'length' => array(20, 30), 'readonly' => $readOnlyEdicao);
				$formArray['campos']['item']['D001_Peso_Unitario_Kg']      = array('name' => 'D001_Peso_Unitario_Kg', 'label' => 'D001_Peso_Unitario_Kg', 'type' => 'hidden', 'reject' => true);
			}

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
			
			$formArray['campos']['icms']['T006_D059_Id'] = array('name' => 'T006_D059_Id', 'label' => 'Situação Tributária', 'type' => 'select', 
				'select' => $arrayCST
			);

			$formArray['campos']['icms']['T006_Flag_ST'] = array('name' => 'T006_Flag_ST', 'label' => 'Substituição Tributária', 'type' => 'select', 'select' => 
				array(
					array('title' => 'Sim', 'value' => 'S'),
					array('title' => 'Não', 'value' => 'N', 'selected' => true)
				)
			);

			// Botão Limpar / Novo Registro
			if (!empty($acaoId)) {
				$formArray['campos']['produto']['ven002FormProdutosBtnNew'] = array('name' => 'ven002FormProdutosBtnNew', 'type'=>'submit', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
			}
			$formArray['campos']['produto']['ven002FormProdutosBtnCln'] = array('name' => 'ven002FormProdutosBtnCln', 'type'=>'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
			$formArray['campos']['produto']['ven002FormProdutosBtnAdd'] = array('name' => 'ven002FormProdutosBtnAdd', 'type'=>'button', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
			
			// Desabilita a edição do formulário
			$formArray['campos']['icms']['Tab_Icms_Produto']  = array('name' => 'Tab_Icms_Produto', 'label' => '', 'type' => 'hidden', 'reject' => true);
			$calculadoraQtdNavegaTab = (!isset($g['C031']['calculadoraQtdNavegaTab']) || $g['C031']['calculadoraQtdNavegaTab'] !== 'N') ? 'true' : 'false';
			$form->definirFormularioArray($formArray);

			$camposMostrarICMS = array();
			$camposMostrarICMS['T006_Aliquota_ICMS'] = array('CST' => array(1, 2, 12, 3, 8, 10, 11, 14, 24));
			$camposMostrarICMS['T006_Modalidade_ICMS'] = array('CST' => array(1, 2, 12, 3, 8, 10, 11, 14, 24));
			$camposMostrarICMS['T006_ST_ICMS_Interno'] = array('CST' => array(1, 2, 3, 4, 12, 13, 3, 5, 6, 7, 8, 10, 11, 14, 15, 16, 17, 18, 19, 20, 21, 22, 24));
			$camposMostrarICMS['T006_Percentual_Partilha_ICMS_Origem'] = array('CST' => array(1, 2, 3, 6, 12, 5, 13, 7, 8, 10, 11, 14, 15, 16, 17, 18, 19, 20, 21, 22, 24));
			$camposMostrarICMS['T006_Percentual_Partilha_ICMS_Destino'] = array('CST' => array(1, 2, 3, 6, 12, 5, 13, 7, 8, 10, 11, 14, 15, 16, 17, 18, 19, 20, 21, 22, 24));
			$camposMostrarICMS['T006_Percentual_Reducao_ICMS'] = array('CST' => array(3, 12, 8, 10, 11, 14, 24));
			$camposMostrarICMS['T006_ICMS_Inter_Estadual'] = array('CST' => array(2, 3, 12, 4, 10, 11, 14, 18, 19, 20, 24));
			$camposMostrarICMS['T006_Modalidade_ICMS_Substituicao'] = array('CST' => array(2, 12, 4, 10, 11, 14, 18, 19, 20, 24));
			$camposMostrarICMS['T006_ST_VA'] = array('CST' => array(2, 12, 4, 10, 11, 14, 18, 19, 20, 24));
			$camposMostrarICMS['T006_Percentual_Reducao_ICMS_ST'] = array('CST' => array(2, 12, 4, 10, 11, 14, 18, 19, 20, 24));
			$camposMostrarICMS['T006_Percentual_Base_Operacao_Propria'] = array('CST' => array(12, 14));
			$camposMostrarICMS['T006_UF_ICMS_Substituicao_Devido'] = array('CST' => array(12, 14));
			$camposMostrarICMS['T006_Valor_Base_ICMS_Substituicao_Destino'] = array('CST' => array(13));
			$camposMostrarICMS['T006_Valor_ICMS_Substituicao_Destino'] = array('CST' => array(13));
			$camposMostrarICMS['T006_Valor_ICMS_Credito'] = array('CST' => array(15, 18, 24));
			$camposMostrarICMS['T006_Aliquota_ICMS_Credito'] = array('CST' => array(15, 18, 24));
			// $camposMostrarICMS['T006_Preco_Pauta_ST'] = array('CST' => array(2, 3, 12, 4, 10, 11, 14, 18, 19, 20, 24));

			$camposMostrarICMS = json_encode($camposMostrarICMS);

			echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					var formCalculadoraQuantidadePedidoEditar = $('#{$g['divId']}');
					formCalculadoraQuantidadePedidoEditar.unbind('calculadoraQuantidade:resultado.ven002Edicao').bind('calculadoraQuantidade:resultado.ven002Edicao', function(_, medidas) {
						formCalculadoraQuantidadePedidoEditar.find('#T006A_Altura').val(medidas.altura);
						formCalculadoraQuantidadePedidoEditar.find('#T006A_Largura').val(medidas.largura);
						formCalculadoraQuantidadePedidoEditar.find('#T006A_Comprimento').val(medidas.comprimento);
						formCalculadoraQuantidadePedidoEditar.find('#T006A_Resultado').val(medidas.resultado);
						formCalculadoraQuantidadePedidoEditar.find('#T006A_Quantidade_Comercial:visible').val(medidas.resultado.replace('.', ',')).trigger('change');
						formCalculadoraQuantidadePedidoEditar.find('#T006_Quantidade').val(medidas.resultado.replace('.', ',')).trigger('change');
					});
					$.getScript('/hardness3/static/js/calculadoraQuantidade.js').done(function() {
						configurarCalculadoraQuantidade({participarTab: {$calculadoraQtdNavegaTab}});
						inicializarCalculadoraQuantidade({formulario: formCalculadoraQuantidadePedidoEditar, formId: '{$g['divId']}', campo: '#T006_Quantidade', altura: '#T006A_Altura', largura: '#T006A_Largura', comprimento: '#T006A_Comprimento', resultado: '#T006A_Resultado', divIdRoot: '{$g['divIdRoot']}', divIdParent: '{$g['divId']}' });
					});
					mostrarOcultarCamposForm({$camposMostrarICMS}, '#{$g['divId']}', '#T006_D059_Id', 'CST');
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

							$T006 = mysql_query("SELECT T006_D009_Id 
							                       FROM T006 
												  WHERE T006_Id = '{$acaoId}' ");
							$mT006 = mysql_fetch_array($T006);	

							if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
                                // $T006_D059_Id = $form->campoValorEnviado('T006_D059_Id');
                                // $sqlD059 = mysql_query("SELECT D059_Codigo_ST FROM D059 WHERE D059_Id='{$T006_D059_Id}'");
                                // $rowSql = mysql_fetch_array($sqlD059);
                                // mysql_query("UPDATE T006 SET T006_Flag_Situacao_Tributaria='0{$rowSql['D059_Codigo_ST']}' WHERE T006_Id='{$acaoId}'");                                
								
								// Se trocar de produto, ele atualiza as aliquotas
								if($form->campoValorEnviado('T006_D009_Id') != $mT006['T006_D009_Id']){
									$VEN002->atualizarAliquotasPedidos($mT005['T005_Id'], false, $acaoId);
									$CAD002->processaEstoqueLiquidoPorLocacao($form->campoValorEnviado('T006_D009_Id'));
								}

								$T006A_Altura = gCorrigeNumeroInverte($form->campoValorEnviado('T006A_Altura'));
								$T006A_Largura = gCorrigeNumeroInverte($form->campoValorEnviado('T006A_Largura'));
								$T006A_Comprimento = gCorrigeNumeroInverte($form->campoValorEnviado('T006A_Comprimento'));
								$T006A_Resultado = gCorrigeNumeroInverte($form->campoValorEnviado('T006A_Resultado'));
								mysql_query("INSERT INTO T006A (T006A_T006_Id, T006A_Altura, T006A_Largura, T006A_Comprimento, T006A_Resultado) VALUES ('{$acaoId}', '{$T006A_Altura}', '{$T006A_Largura}', '{$T006A_Comprimento}', '{$T006A_Resultado}') ON DUPLICATE KEY UPDATE T006A_Altura = VALUES(T006A_Altura), T006A_Largura = VALUES(T006A_Largura), T006A_Comprimento = VALUES(T006A_Comprimento), T006A_Resultado = VALUES(T006A_Resultado)");

								mysqli_query("CALL T006_Gravar_Totalizacao_4('{$acaoId}')");

								$alert = gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!', false);
								// Imprime uma mensagem e fecha a janela
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
					$form->campoSetarValor('T006A_Altura', !empty($mT006['T006A_Altura']) ? $mT006['T006A_Altura'] : '0.00000');
					$form->campoSetarValor('T006A_Largura', !empty($mT006['T006A_Largura']) ? $mT006['T006A_Largura'] : '0.00000');
					$form->campoSetarValor('T006A_Comprimento', !empty($mT006['T006A_Comprimento']) ? $mT006['T006A_Comprimento'] : '0.00000');
					$form->campoSetarValor('T006A_Resultado', !empty($mT006['T006A_Resultado']) ? $mT006['T006A_Resultado'] : '0.00000');
					// mostra erros de formulário uteis ao desenvolvedor
					if ($processado !== true) {
						reset($form->formArray['campos']);
						if ($tab == key($form->formArray['campos'])) {
							echo $processado;
						}
					}
				}
				$form->campoSetarValor('T006_D059_Id', $D059_Id);
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

		case 'xven002FormPagamento':
			global $g;

			//$acaoId = (isset($r_acaoId)) ? $r_acaoId : false; 
			$T005_Id = (isset($r_T005_Id)) ? $r_T005_Id : false;
			  
			//echo $T005_Id;
			//('qqqq');
			/**
			* PARTE 1: Variavéis envidas para o formulário
			*/
			// Variável indica qual tab do form será exibida
			$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
			$r_divReload = isset($r_ven002FormPagamento) ? $r_ven002FormPagamento['divReload'] : $r_divReload;
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
			$form_tabela = 'T090';
			$form_campo_pk = 'T090_Id';
            $form_parent = 'T005_Id';
            $form_parent_valor = $T005_Id;
			// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
			$enviar_btnId = 'ven002FormPagamento_btn';
			// Form que será executado ao clicar no botão
			$enviar_acaoHref = '/ven/ven002/form/ven002FormPagamento/';
            $form_editor = array(
                'geral' => '{"Total_Parcelas":{"top":7,"left":31.078125,"width":176,"hidden":0},"usarFreteParcela":{"top":7,"left":271,"width":59,"hidden":0},"selecionaPrimeiroVencimento":{"top":30,"left":33.171875,"width":105,"hidden":0},"primeiroVencimentoData":{"top":30,"left":187,"width":62,"hidden":0},"primeiroVencimentoNum":{"top":30,"left":187,"width":62,"hidden":0},"usarSTParcela":{"top":30,"left":283,"width":59,"hidden":0},"selecionaDias":{"top":53,"left":21.296875,"width":105,"hidden":0},"diaFixo":{"top":53,"left":187,"width":62,"hidden":0},"IntervalorDias":{"top":53,"left":187,"width":62,"hidden":0},"usarIPIParcela":{"top":53,"left":284,"width":59,"hidden":0},"ven002FormAdicionarParcela_btn":{"top":51,"left":624,"width":null,"hidden":1},"T005_Observacao_Pagamento":{"top":53,"left":781,"height":37,"width":168,"hidden":1},"Portador_D027_Id":{"top":76,"left":24.359375,"width":176,"hidden":0},"ven002SalvarObservacao_btn":{"top":74,"left":624,"width":null,"hidden":1},"ven002FormPagamento_btn":{"top":75,"left":300,"width":null,"hidden":0}}',
            );

			// novo formulário
			$form = new Formulario('ven002FormPagamento');

			$select = array();
			$select[] = array('value' => '0', 'title' => 'A COMBINAR');
			// pega as configurações de juros da empresa atual
			$C004=mysql_query("select * from C004 where C004_Id='{$g['empresaAtual']}'");
			$mC004=mysql_fetch_array($C004);
			$CNPJ_Empresa_Corrente = substr($mC004['C004_Cnpj'], 0, -8);
			$tipoJuros=$mC004['C004_Tipo_Juros'];
			$taxaJuros=$mC004['C004_Taxa_Juros'];
			$parcelasIsentas=$mC004['C004_Parcelas_Isentas_Juros'];

			$T005=mysql_query("SELECT * FROM T005 LEFT JOIN D024 ON D024_Id=T005_D024_Id WHERE T005_Id='{$T005_Id}'");
			$mT005=mysql_fetch_array($T005);

			if($mT005['T005_Pgto_Parcelas'] <= 0 && $mT005['D024_Quantidade_Parcelas'] > 0){
				$mT005['T005_Pgto_Parcelas'] = $mT005['D024_Quantidade_Parcelas'];
			}else if($mT005['T005_Pgto_Parcelas'] <= 0 && $mT005['D024_Quantidade_Parcelas'] <= 0){
				$mT005['T005_Pgto_Parcelas'] = $g['C031']['numeroParcelasPadrao'];
			}

			if($mT005['T005_Pgto_Portador'] <= 0 && $mT005['D024_D027_Id'] > 0){
				$mT005['T005_Pgto_Portador'] = $mT005['D024_D027_Id'];	
			}else if($mT005['T005_Pgto_Portador'] <= 0 && $g['C031']['D027portadorEmpresa'] > 0){
				$mT005['T005_Pgto_Portador']=$g['C031']['D027portadorEmpresa'];
			}

			if(empty($mT005['D024_Prazo_Parcela'])){
				$mT005['D024_Prazo_Parcela'] = $g['C031']['primeiroVencimentoNum'];
			}
			if(empty($mT005['D024_Dias_Vencimento'])){
				$mT005['D024_Dias_Vencimento'] = $g['C031']['IntervalorDias'];
			}
			if(empty($mT005['T005_Pgto_Dias_Vcto_Tipo'])){
				$mT005['T005_Pgto_Dias_Vcto_Tipo'] = $g['C031']['tipoIntervalodias'];
			}

			//SELECIONANDO OS SERVIÇOS DESSE ORÇAMENTO PARA CALCULAR O TOTAL
			$sql = "SELECT T147_Valor_Total FROM T147 where T147_T005_Id='{$T005_Id}'";
			$T147_sql = mysql_query($sql);

			$totalServicos=0;
			// LOOPING PARA CALCULAR O TOTAL DOS SERVIÇOS SOMADO COM TOTAL DO PEDIDO
			while($T147 = mysql_fetch_array($T147_sql)){
				$totalServicos+=$T147['T147_Valor_Total'];
			}

			//SOMANDO O TOTAL DO ORÇAMENTO COM O TOTAL DOS SERVIÇOS
			$valorTotalPedido = $totalServicos + $mT005['T005_Valor_Total'];

			for ($Parcela=1; $Parcela<=$g['C031']['numeroMaxParcelas']; $Parcela++) {
				$valorParcela=$VEN002->calcularParcelaCombo($Parcela,$valorTotalPedido,$tipoJuros,$taxaJuros,$parcelasIsentas);
				if ($Parcela>$parcelasIsentas){
					$valorTotal=gCorrigeNumero($Parcela*gCorrigeNumeroInverte($valorParcela));
					$valorParcela.="*";
				}else{
					$valorTotal=gCorrigeNumero($valorTotalPedido);
				}
				if($Parcela==$mT005['T005_Pgto_Parcelas']){
					$select[] = array('value' => $Parcela, 'title' => str_pad($Parcela,2,"0",STR_PAD_LEFT) . " x" . " - " .$valorParcela." = ".gCorrigeNumero($valorTotalPedido), 'selected'=>true);
				}else{
					$select[] = array('value' => $Parcela, 'title' => str_pad($Parcela,2,"0",STR_PAD_LEFT) . " x" . " - " .$valorParcela." = ".gCorrigeNumero($valorTotalPedido));
				}
			}

			if(($mT005['T005_Pgto_Primeiro_Vcto_Valor'] != "") || (!empty($mT005['T005_Pgto_Primeiro_Vcto_Valor']))){
				$primeiroVctoValor = $mT005['T005_Pgto_Primeiro_Vcto_Valor'];
			}else{
				$primeiroVctoValor = $mT005['D024_Prazo_Parcela'];
			}
			if(!empty($mT005['T005_Pgto_Dias_Vcto_Valor'])){
				$diaVctoValor = $mT005['T005_Pgto_Dias_Vcto_Valor'];
			}else{
				$diaVctoValor = $mT005['D024_Dias_Vencimento'];
			}

			if(($mT005['T005_Pgto_Data'] != "") || (!empty($mT005['T005_Pgto_Data']))){
				$pgtoData = $mT005['T005_Pgto_Data'];
			}else{
				$pgtoData = "";
			}

			// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
			$formArray = array(
				'campos' => array(
					'geral' => array(
						'Total_Parcelas' => array('name' => 'Total_Parcelas', 'label' => 'Parcelas', 'type' => 'select', 'select' => $select,'uppercase'=>false, 'reject' => true),
						//'valorParcelaSimulacao' => array('name' => 'valorParcelaSimulacao', 'label' => '', 'type' => 'text','extra'=>'style="background:#E8E8E8"','reject' => true, 'length' => array(3,6), 'align' => 'right', 'readonly'=>'readonly'),
						'selecionaPrimeiroVencimento' => array('name' => 'selecionaPrimeiroVencimento', 'label'=>'1º Venc.', 'reject'=>true, 'type' => 'select', 'select' => 
						array(
							array('title' => 'Dias', 'value' => 'Dias', 'selected' => ($mT005['T005_Pgto_Primeiro_Vcto_Tipo'] == "DIAS") ? true : false),
							array('title' => 'Data', 'value' => 'Data', 'selected' => ($mT005['T005_Pgto_Primeiro_Vcto_Tipo'] == "DATA") ? true : false),
						)),
						'primeiroVencimentoNum' => array('name' => 'primeiroVencimentoNum', 'label' => '', 'value'=> $primeiroVctoValor, 'type' => 'text', 'reject' => true, 'length' => array(3,6), 'align' => 'right'),
						'primeiroVencimentoData' => array('name' => 'primeiroVencimentoData', 'label' => '', 'value'=> $primeiroVctoValor, 'type' => 'data', 'reject' => true, 'align' => 'right'),
						'selecionaDias' => array('name' => 'selecionaDias', 'label'=>'Dias Venc.', 'type' => 'select', 'reject'=>true, 'select' => 
						array(
							array('title' => 'Intervalo', 'value' => 'I', 'selected' => ($mT005['T005_Pgto_Dias_Vcto_Tipo'] == "INTERVALO") ? true : false),
							array('title' => 'Dia Fixo', 'value' => 'F', 'selected' => ($mT005['T005_Pgto_Dias_Vcto_Tipo'] == "DIA FIXO") ? true : false),
						)),			
						'IntervalorDias' => array('name' => 'IntervalorDias', 'label' => '', 'type' => 'text', 'value'=>$diaVctoValor, 'reject' => true, 'length' => array(3,6), 'align' => 'right'),
						'diaFixo' => array('name' => 'diaFixo', 'label' => '', 'type' => 'text', 'reject' => true, 'value'=>$diaVctoValor, 'length' => array(3,2), 'align' => 'right'),												
						'Portador_D027_Id' => array('name' => 'D027_Id','label'=>'Portadorrrrr', 'reject'=>true, 'type' => 'select', 'select' =>
							gGeraSelect('D027', 'D027_Id', 'concat(D027_Portador," ",if(D027_Acrescimo_IPV>0,concat("+",D027_Acrescimo_IPV,"%"),"")) as D027_Portador','LEFT JOIN D007 ON D007_Id = D027_D007_Id LEFT JOIN C004 ON C004_Id = D007_C004_Id WHERE D027_Flag_Ativo = "S" AND D027_Flag_Exibe_CRM = "S" AND (D007_Flag_Layout="1" and D007_Flag_Ativo="S" AND C004_Cnpj LIKE "%'.$CNPJ_Empresa_Corrente.'%") OR ifnull(D027_D007_Id,0) = 0 order by D027_Portador', true, $mT005['T005_Pgto_Portador'])
						),
						'ven002FormPagamento_btn' => array('name' => 'ven002FormPagamento_btn', 'type'=>'submit','reload'=>false,'value'=>'Gerar Parcelas','align' => 'right', 'reject'=>true),
						'ven002FormAdicionarParcela_btn' => array('name' => 'ven002FormAdicionarParcela_btn', 'type'=>'submit', 'reload' => false, 'value' => 'Adicionar Parcela', 'align' => 'right', 'reject' => true),
						'T005_Observacao_Pagamento' => array('name' => 'T005_Observacao_Pagamento', 'label' => 'Observação', 'type' => 'textarea', 'length' => array(1,23),'reject' => true),
						'ven002SalvarObservacao_btn' => array('name' => 'ven002SalvarObservacao_btn', 'type'=>'submit', 'reload' => false, 'value' => 'Salvar Observação', 'align' => 'right', 'reject' => true),
						'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true),
						'T005_Id' => array('name' => 'T005_Id', 'label' => '', 'type' => 'hidden', 'value' => @$r_T005_Id, 'reject' => true),
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
			// Método: ven002FormPagamento
			// Chamado: 33004
			// Descricao: Valor que é definido no cadastro do cliente e é repassado automaticamente a tab de Pedido (ST = 'ON')
			// --------------------------------------------------------------------------------------------------------

			$formArray['campos']['geral']['usarSTParcela'] = array('name' => 'usarSTParcela', 'label'=>'ST primeira parcela', 'reject'=>true, 'type' => 'select','select' => 
																	array(
																		array('title' => '...', 'value' => ''),
																		array('title' => 'Sim', 'value' => 'on', 'selected' => ($mT005['D024_Cobrar_ST_Primeira_Parcela'] == 'S') ? true : false),
																		array('title' => 'Não', 'value' => 'off'),
																	)
																);

			// --------------------------------------------------------------------------------------------------------
			// FIM - OSNI - 33004 - ven002FormPagamento
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
		    	$('#ven002FormAdicionarParcela_btn').unbind('click');
				$('#ven002FormAdicionarParcela_btn').unbind('click').bind('click', function(data) {
					var id = '{$row['T005_D024_Id']}';
						$.getJSON('/ven/ven002/form_func-ajax/inserirParcela/?ajax=true=' + '&' + $("#{$formId}").serialize() + '&callback=?', function(request) {
							if (request.code) {															
								divRefresh("{$r_divReload}");
							} else {
								var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
    							dialogConfirm("Erro!", request.data, buttons)
							}
						});
					return false;
				});
				$('#ven002SalvarObservacao_btn').unbind('click').bind('click', function(data) {
					$.getJSON('/ven/ven002/form_func-ajax/salvarObservacao/?ajax=true=' + '&' + $("#{$formId}").serialize() + '&T005_Id=' + encodeURIComponent('{$T005_Id}') + '&callback=?', function(request) {
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
					$.getJSON('/ven/ven002/form_func-ajax/calcularParcelaSimulacao/?ajax=true=' + '&' + $("#{$formId}").serialize() + '&callback=?', function(request) {
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

					$D027_Id=$form->campoValorEnviado('Portador_D027_Id');;
					//Ao escolher um portador Que tenha algo uma Taxa de Acrescimo IPV(seja diferente de 0) esse valor da taxa vai ser inserido no T250 do orçamento especifico
					$D027 = mysql_query("SELECT D027_Acrescimo_IPV FROM D027 WHERE D027_Id={$D027_Id}");
					$rD027 = mysql_fetch_array($D027);
					log("Aliquota: ".$rD027['D027_Acrescimo_IPV']);
					if($rD027['D027_Acrescimo_IPV']>0){
						log("O Acrescimo IPV é maior que 0");
						log("T005_Id:".$T005_Id);
						$T251 = mysql_query("Select T251_Id,T251_Aliquota,T251_Flag_Altera_Preco from T251 where T251_T005_Id='$T005_Id' and T251_Tipo='5' and T251_Flag_Campo_Calculado='S'");
						$mT251=mysql_fetch_array($T251);
						$T251_Id=$mT251['T251_Id'];
	
						if(number_format($mT251['T251_Aliquota'],2)!=number_format($rD027['D027_Acrescimo_IPV'],2)){
							mysql_query("Update T251 set T251_Aliquota='{$rD027['D027_Acrescimo_IPV']}' where T251_Id='{$T251_Id}'");
							$erro=mysql_error();
							log("erro: ".$erro);
							$VEN002->atualizarPrecoTabela($T005_Id);
	
							if($mT251['T251_Flag_Altera_Preco']=='S'){
								$msg = "Foi acrescido ".$rD027['D027_Acrescimo_IPV']."% no preço tabela desse pedido.";
							}else{
								$msg = "Foi acrescido ".$rD027['D027_Acrescimo_IPV']."% ao demonstrativo IPV.";
							}
						}
	
					}else{
						$T251 = mysql_query("Select T251_Id,T251_Aliquota,T251_Flag_Altera_Preco from T251 where T251_T005_Id='$T005_Id' and T251_Tipo='5' and T251_Flag_Campo_Calculado='S'");
						$mT251=mysql_fetch_array($T251);
						$T251_Id=$mT251['T251_Id'];
	
						if($mT251['T251_Aliquota']>0){
							mysql_query("Update T251 set T251_Aliquota='0' where T251_Id='{$T251_Id}'");
							$erro=mysql_error();
							log("erro: ".$erro);
							$VEN002->atualizarPrecoTabela($T005_Id);
	
							if($mT251['T251_Flag_Altera_Preco']=='S'){
								$msg = "Foi deduzido ".$mT251['T251_Aliquota']."% no preço tabela desse pedido, devido a mudança do portador.";
							}else{
								$msg = "Foi deduzido ".$mT251['T251_Aliquota']."% ao demonstrativo IPV, devido a mudança do portador.";
							}
						}
					
					}

					// Gerar Boleto			
					$retorno = $VEN002->atualizarParcelas($form);
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
		$gerado = gGeraCamposForm($form, $tab, $r_acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk, false, $form_parent, $form_parent_valor);


		/**
		 * PARTE 5: Exibir o formulário
		 */
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');

		$sqlD024 = mysql_query("SELECT D024_Instrucao_Pagamento, D024_Prazo_Parcela, D024_Dias_Vencimento, D024_Quantidade_Parcelas, D027_Portador FROM D024 LEFT JOIN T005 ON T005_D024_Id = D024_Id LEFT JOIN D027 ON D027_Id=D024_D027_Id WHERE T005_Id = '{$T005_Id}'");
		$D024 = mysql_fetch_array($sqlD024);
		$obs='';
		$obs .=	empty($D024['D024_Prazo_Parcela']) 				? '' : " 1º Venc: ".$D024['D024_Prazo_Parcela']."<br />";
		$obs .= empty($D024['D024_Dias_Vencimento']) 			? '' : " Dia Fixo/Intervalo: ".$D024['D024_Dias_Vencimento']."<br />";
		$obs .= empty($D024['D024_Quantidade_Parcelas']) 		? '' : " Nº Parcelas: ".$D024['D024_Quantidade_Parcelas']."<br />";
		$obs .= empty($D024['D027_Portador']) 					? '' : " Portador: ".$D024['D027_Portador']."<br />";
		//$obs .= empty($obs) 									? '' : $obs."<br />";
		echo <<<EOT
			<div style="border: 1px solid silver; margin: 10px 15px 0 450px; padding: 5px; height: 110px; border-radius:5px; color: var(--BGVermelho); overflow: auto;">
				<span style="font-weight: bold; color: #000;">Informação de Pagamento:<br /> </span> {$obs}{$D024['D024_Instrucao_Pagamento']}	
			</div>	
EOT;

	break;

	case 'ven002FormPagamento':
		global $g;

		$T005_Id = (isset($r_T005_Id)) ? $r_T005_Id : false;
		$acaoId = $T005_Id;
		 
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		$r_divReload = isset($r_ven002FormPagamento) ? $r_ven002FormPagamento['divReload'] : $r_divReload;
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
		$form_tabela = 'T005';
		$form_campo_pk = 'T005_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'ven002FormPagamento_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/ven/ven002/form/ven002FormPagamento/';
        $form_editor = array(
            'geral' => '{"T005_Pgto_Parcelas":{"top":7,"left":31.078125,"width":176,"hidden":0},"T005_Flag_Frete_Primeira_Parcela":{"top":7,"left":270.28125,"width":57,"hidden":0},"T005_Pgto_Primeiro_Vcto_Tipo":{"top":30,"left":33.171875,"width":96,"hidden":0},"T005_Pgto_Primeiro_Vcto_Valor":{"top":30,"left":176,"width":72,"hidden":0},"T005_Pgto_Primeiro_Vcto_Valor_Data":{"top":30,"left":176,"width":72,"hidden":0},"T005_Flag_ST_Primeira_Parcela":{"top":30,"left":281.90625,"width":57,"hidden":0},"T005_Pgto_Dias_Vcto_Tipo":{"top":53,"left":21.296875,"width":97,"hidden":0},"T005_Pgto_Dias_Vcto_Valor":{"top":53,"left":176,"width":72,"hidden":0},"T005_Flag_IPI_Primeira_Parcela":{"top":53,"left":282.515625,"width":57,"hidden":0},"ven002FormAdicionarParcela_btn":{"top":51,"left":624,"width":null,"hidden":1},"T005_Observacao_Pagamento":{"top":53,"left":781,"height":37,"width":168,"hidden":1},"T005_D027_Id":{"top":76,"left":31.6875,"width":175,"hidden":0},"ven002SalvarObservacao_btn":{"top":74,"left":624,"width":null,"hidden":1},"ven002FormPagamento_btn":{"top":74,"left":336,"width":null,"hidden":0}}',
        );

		if ($g['C031']['utilizaTrocoBaixa'] == 'S') {
			array_push($form_editor['geral'], array('T005_Valor_Total' => '{"top":99,"left":40.46875,"width":89,"hidden":0}', 'ValorRecebido' => '{"top":122,"left":9.953125,"width":89,"hidden":0}', 'ValorTroco' => '{"top":122,"left":254.515625,"width":150,"hidden":0}'));
		}

		// novo formulário
		$form = new Formulario('ven002FormPagamento');

		$VEN001->verificaPgtoPorCurvaCliente(false, $T005_Id);

		$dadosTabela = array('tabela' => 'T005', 'campos' => 'T005_Valor_Total as valorTotal,T005_Pgto_Parcelas as pgtoParcelas', 'where' => $form_campo_pk."='{$T005_Id}'");
	    $select = $VEN001->populaComboPgtoParcelas($dadosTabela);
	    $CNPJ_Empresa_Corrente=substr($g['C004']['C004_Cnpj'], 0, -8);

		$T005 = mysql_query("SELECT D024_D027_Id FROM T005 LEFT JOIN D024 ON D024_Id = T005_D024_Id WHERE T005_Id = {$T005_Id}");
		$mT005 = mysql_fetch_array($T005);
		$disabledPortador = $g["C031"]["desabilitarEdicaoPortador"] == 'S' && !empty($mT005['D024_D027_Id']) && $g['nivelAlcada'] != 4;

		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T005_Pgto_Parcelas' => array('name' => 'T005_Pgto_Parcelas', 'label' => 'Parcelas', 'type' => 'select', 'select' => $select,'uppercase'=>false),
					'T005_Pgto_Primeiro_Vcto_Tipo' => array('name' => 'T005_Pgto_Primeiro_Vcto_Tipo', 'label'=>'1º Venc.', 'type' => 'select','select' =>
						array(
							array('title' => 'Dias', 'value' => 'DIAS'),
							array('title' => 'Data', 'value' => 'DATA'),
						)
					),
					'T005_Pgto_Primeiro_Vcto_Valor' => array('name' => 'T005_Pgto_Primeiro_Vcto_Valor', 'type' => 'text','label' => '',  'length' => array(3,7), 'align' => 'right'),
					'T005_Pgto_Primeiro_Vcto_Valor_Data' => array('name' => 'T005_Pgto_Primeiro_Vcto_Valor_Data', 'label' => '', 'type' => 'data', 'align' => 'right'),
					'T005_Pgto_Dias_Vcto_Tipo' => array('name' => 'T005_Pgto_Dias_Vcto_Tipo', 'label'=>'Dias Venc.', 'type' => 'select','select' =>
						array(
							array('title' => 'Intervalo', 'value' => 'INTERVALO'),
							array('title' => 'Dia Fixo', 'value' => 'DIA FIXO'),
						)
					),
					'T005_Pgto_Dias_Vcto_Valor' => array('name' => 'T005_Pgto_Dias_Vcto_Valor', 'label' => '', 'type' => 'text', 'length' => array(3,6), 'align' => 'right'),
					'T005_D027_Id' => array('name' => 'D027_Id','label'=>'Portador', 'type' => 'select', 'disabled' => $disabledPortador, 'select' =>
						gGeraSelect('D027', 'D027_Id', 'concat(D027_Portador," ",if(D027_Acrescimo_IPV>0,concat("+",D027_Acrescimo_IPV,"%"),"")) as D027_Portador','LEFT JOIN D007 ON D007_Id = D027_D007_Id LEFT JOIN C004 ON C004_Id = D007_C004_Id WHERE D027_Flag_Ativo = "S" AND D027_Flag_Exibe_CRM = "S" AND ((D007_Flag_Layout="1" and D007_Flag_Ativo="S" AND C004_Cnpj LIKE "%'.$CNPJ_Empresa_Corrente.'%") OR ifnull(D027_D007_Id,0) = 0) order by D027_Portador', true, $mT005['T005_D027_Id'])
					),
					'T005_Flag_Frete_Primeira_Parcela' => array('name' => 'T005_Flag_Frete_Primeira_Parcela', 'label'=>'Frete primeira parcela', 'type' => 'select','select' =>
						array(
							array('title' => '...', 'value' => ''),
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N'),
						)
					),
					'T005_Flag_ST_Primeira_Parcela' => array('name' => 'T005_Flag_ST_Primeira_Parcela', 'label'=>'ST primeira parcela', 'type' => 'select','select' =>
						array(
							array('title' => '...', 'value' => ''),
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N'),
						)
					),
					'T005_Flag_IPI_Primeira_Parcela' => array('name' => 'T005_Flag_IPI_Primeira_Parcela', 'label'=>'IPI primeira parcela', 'type' => 'select','select' =>
						array(
							array('title' => '...', 'value' => ''),
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N'),
						)
					),
					'ven002FormPagamento_btn' => array('name' => 'ven002FormPagamento_btn', 'type'=>'submit','reload'=>false,'value'=>'Gerar Parcelas','align' => 'right', 'reject'=>true),
					'ven002FormAdicionarParcela_btn' => array('name' => 'ven002FormAdicionarParcela_btn', 'type'=>'submit', 'reload' => false, 'value' => 'Adicionar Parcela', 'align' => 'right', 'reject' => true),
					'T005_Observacao_Pagamento' => array('name' => 'T005_Observacao_Pagamento', 'label' => 'Observação', 'type' => 'textarea', 'length' => array(1,23),'reject' => true),
					'ven002SalvarObservacao_btn' => array('name' => 'ven002SalvarObservacao_btn', 'type'=>'submit', 'reload' => false, 'value' => 'Salvar Observação', 'align' => 'right', 'reject' => true),
					'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true),
					'T005_Id' => array('name' => 'T005_Id', 'label' => '', 'type' => 'hidden', 'value' => @$r_T005_Id, 'reject' => true),
				)
			)
		);

		if ($g['C031']['utilizaTrocoBaixa'] == 'S') {
			$formArray['campos']['geral']['T005_Valor_Total'] = array('name' => 'T005_Valor_Total', 'label' => 'Valor total',      'type' => 'moeda', 'extra' => "readonly onkeydown=\"return event.key != 'Enter';\"", 'style' => 'text-align: right; background-color: #E8E8E8;');
			$formArray['campos']['geral']['ValorRecebido']    = array('name' => 'ValorRecebido',    'label' => 'Recebido cliente', 'type' => 'moeda', 'reject' => true, 'extra' => "autocomplete=\"off\" oninput=\"calcularValorTroco('{$r_divReload}');\" onkeydown=\"return event.key != 'Enter';\"", 'style' => 'text-align: right;');
			$formArray['campos']['geral']['ValorTroco']       = array('name' => 'ValorTroco',       'label' => 'Troco',            'type' => 'moeda', 'reject' => true, 'extra' => "readonly  onkeydown=\"return event.key != 'Enter';\"", 'style' => 'text-align: right; background-color: #E8E8E8; font-size: 14px;');
		}
		
		$form->definirFormularioArray($formArray);
		
		echo <<<EOT
		    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		    	$('#ven002FormAdicionarParcela_btn').unbind('click');
				$('#ven002FormAdicionarParcela_btn').unbind('click').bind('click', function(data) {
					var id = '{$row['T005_D024_Id']}';
						$.getJSON('/ven/ven002/form_func-ajax/inserirParcela/?ajax=true&T005_Id=' + encodeURIComponent('{$T005_Id}') + '&form_tabela=' + encodeURIComponent('{$form_tabela}') + '&form_campo_pk=' + encodeURIComponent('{$form_campo_pk}') + '&callback=?', function(request) {
							if (request.code) {															
								divRefresh("{$r_divReload}");
							} else {
								var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
    							dialogConfirm("Erro!", request.data, buttons)
							}
						});
					return false;
				});
				$('#ven002SalvarObservacao_btn').unbind('click').bind('click', function(data) {
					$.getJSON('/ven/ven002/form_func-ajax/salvarObservacao/?ajax=true=' + '&' + $("#{$formId}").serialize() + '&T005_Id=' + encodeURIComponent('{$T005_Id}') + '&callback=?', function(request) {
						if (request.code) {															
							divRefresh('{$g['divId']}');
						} else {
							var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
							dialogConfirm("Erro!", request.data, buttons)
						}
					});
					return false;
				});
				
				$('#{$g['divId']} #T005_Pgto_Primeiro_Vcto_Tipo').change(function() {
					mudaCampoVencimento($(this).val());
				});

				mudaCampoVencimento($('#{$g['divId']} #T005_Pgto_Primeiro_Vcto_Tipo').val())

				function mudaCampoVencimento(parametro){
					if (parametro == 'DIAS') {
						$('#{$g['divId']} #T005_Pgto_Primeiro_Vcto_Valor_Data').val('').hide();
						$('#{$g['divId']} #T005_Pgto_Primeiro_Vcto_Valor').show();
						$('#{$g['divId']} #T005_Pgto_Primeiro_Vcto_Valor').focus();
					} else {
						$('#{$g['divId']} #T005_Pgto_Primeiro_Vcto_Valor').val('').hide();
						$('#{$g['divId']} #T005_Pgto_Primeiro_Vcto_Valor_Data').show();
						$('#{$g['divId']} #T005_Pgto_Primeiro_Vcto_Valor_Data').focus();
					}
				}
				$('#{$g['divId']} #T005_Pgto_Primeiro_Vcto_Tipo').trigger('change');									
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

					$D027_Id=$form->campoValorEnviado('T005_D027_Id');;
					//Ao escolher um portador Que tenha algo uma Taxa de Acrescimo IPV(seja diferente de 0) esse valor da taxa vai ser inserido no T250 do orçamento especifico
					$D027 = mysql_query("SELECT D027_Acrescimo_IPV FROM D027 WHERE D027_Id={$D027_Id}");
					$rD027 = mysql_fetch_array($D027);
					if($rD027['D027_Acrescimo_IPV']>0){
						$T251 = mysql_query("Select T251_Id,T251_Aliquota,T251_Flag_Altera_Preco from T251 where T251_T005_Id='$T005_Id' and T251_Tipo='5' and T251_Flag_Campo_Calculado='S'");
						$mT251=mysql_fetch_array($T251);
						$T251_Id=$mT251['T251_Id'];
	
						if(number_format($mT251['T251_Aliquota'],2)!=number_format($rD027['D027_Acrescimo_IPV'],2)){
							mysql_query("Update T251 set T251_Aliquota='{$rD027['D027_Acrescimo_IPV']}' where T251_Id='{$T251_Id}'");
							$erro=mysql_error();
							log("erro: ".$erro);
							$VEN002->atualizarPrecoTabela($T005_Id);
	
							if($mT251['T251_Flag_Altera_Preco']=='S'){
								$msg = "Foi acrescido ".$rD027['D027_Acrescimo_IPV']."% no preço tabela desse pedido.";
							}else{
								$msg = "Foi acrescido ".$rD027['D027_Acrescimo_IPV']."% ao demonstrativo IPV.";
							}
						}
	
					}else{
						$T251    = mysql_query("Select T251_Id,T251_Aliquota,T251_Flag_Altera_Preco from T251 where T251_T005_Id='$T005_Id' and T251_Tipo='5' and T251_Flag_Campo_Calculado='S'");
						$mT251   = mysql_fetch_array($T251);
						$T251_Id = $mT251['T251_Id'];
	
						if($mT251['T251_Aliquota']>0){
							mysql_query("Update T251 set T251_Aliquota='0' where T251_Id='{$T251_Id}'");
							$erro=mysql_error();
							$VEN002->atualizarPrecoTabela($T005_Id);
	
							if($mT251['T251_Flag_Altera_Preco']=='S'){
								$msg = "Foi deduzido ".$mT251['T251_Aliquota']."% no preço tabela desse pedido, devido a mudança do portador.";
							}else{
								$msg = "Foi deduzido ".$mT251['T251_Aliquota']."% ao demonstrativo IPV, devido a mudança do portador.";
							}
						}					
					}

					// Update dos campos do T005
					$VEN002->atualizaDadosPagamento($form);

					//ARRAY UTILIZADO NA FUNÇÃO ATUALIZAR PARCELAS
					$dadosTabela = array('tabela' => $form_tabela, 
											'tabelaId' => 	$T005_Id,
											'campos' => 'T005_Pgto_Primeiro_Vcto_Tipo as primeiroVencimentoTipo,
												T005_Pgto_Primeiro_Vcto_Valor as primeiroVencimentoNum,
												T005_Pgto_Primeiro_Vcto_Valor_Data as primeiroVencimentoData,
												T005_Pgto_Dias_Vcto_Tipo as diasVctoTipo,
												T005_Pgto_Dias_Vcto_Valor as valorIntervaloDiaFixo,
												T005_D027_Id as D027_Id,
												T005_Flag_Frete_Primeira_Parcela as campoUsarFreteParcela,
												T005_Flag_ST_Primeira_Parcela as campoUsarSTParcela,
												T005_Flag_IPI_Primeira_Parcela as campoUsarIPIParcela,
												T005_Pgto_Parcelas as Total_Parcelas,
												T005_Valor_Total_IPI as valorTotalIPI,
												T005_Valor_Total as valorTotal,
												T005_Valor_Frete as valorTotalFrete,
												T005_Valor_ICMS_Substituicao_Retencao as valorTotalST', 
											'where' => $form_campo_pk."='{$T005_Id}'", 
											'tabelaPgto' => 'T090', 
											'camposPgto' => 'T090_Prazos_Dias as prazoDias,T090_D027_Id as D027Pagamento,T090_Numero_Parcela as numeroParcela', 
											'wherePgto' => "T090_T005_Id='{$T005_Id}'");
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

		$sqlD024 = mysql_query("SELECT D024_Instrucao_Pagamento, D024_Prazo_Parcela, D024_Dias_Vencimento, D024_Quantidade_Parcelas, D027_Portador FROM D024 LEFT JOIN D027 ON D027_Id=D024_D027_Id LEFT JOIN T005 ON T005_D024_Id = D024_Id WHERE T005_Id = '{$T005_Id}'");
		$D024 = mysql_fetch_array($sqlD024);
		$obs='';
		$obs .=	empty($D024['D024_Prazo_Parcela']) 				? '' : " 1º Venc: ".$D024['D024_Prazo_Parcela']."<br />";
		$obs .= empty($D024['D024_Dias_Vencimento']) 			? '' : " Dia Fixo/Intervalo: ".$D024['D024_Dias_Vencimento']."<br />";
		$obs .= empty($D024['D024_Quantidade_Parcelas']) 		? '' : " Nº Parcelas: ".$D024['D024_Quantidade_Parcelas']."<br />";
		$obs .= empty($D024['D027_Portador']) 					? '' : " Portador: ".$D024['D027_Portador']."<br />";
		echo <<<EOT
			<div style="border: 1px solid silver; margin: 10px 15px 0 450px; padding: 5px; height: 100px; border-radius:5px; color: var(--BGVermelho); overflow: auto;">
				<span style="font-weight: bold; color: #000;">Informação de Pagamento Cliente</span><br> {$obs}{$D024['D024_Instrucao_Pagamento']}	
			</div>	
EOT;
		break;

	case 'ven002FormTotais':
	
	// implementação da rubia, retirada toda essa parte 
	/* 
	$Empresa_Atual = $g['empresaAtual'];
	
	$T005=mysql_query("select T005_Flag_Travar_Pedido,
                          T005_Flag_Devolucao
                     from T005
                    where T005_Id='$T005_Id'");
	echo mysql_error();
	$mT005=mysql_fetch_array($T005);

	if (($mT005['T005_Flag_Devolucao']=='S') or ($mT005['T005_Flag_Travar_Pedido']==1)) {
	//    $Pedido_Travado='S';
	}
	if ($mT005['T005_Flag_Travar_Pedido']!=1) {

	   //-- ATUALIZA O PESO DOS ITENS
	   mysql_query("update T006
	             left join T005 on T005_Id=T006_T005_Id
	             left join D009 on D009_Id=T006_D009_Id
	             left join D049 on D049_Id=D009_D049_Id
	             left join D001 on D001_Id=D049_D001_Id
	                   set T006_Peso_Unitario=D001_Peso_Unitario_Kg
	                 where T005_Id='$T005_Id' 
	                   and T005_C004_Id='$Empresa_Atual'");

	    //-- TOTALIZA O PEDIDO
	    $cSql_T006=mysql_query("select T006_Id from T006 where T006_T005_Id='$T005_Id' order by T006_Id");
	    while ($mT006=mysql_fetch_array($cSql_T006)) {
	          mysql_query("call T006_Gravar_Totalizacao_4('$mT006[T006_Id]')");
	    }
	    mysql_query("call T005_Gravar_Totalizacao_4('$T005_Id')");
	}
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
		
		
		$form_tabela = 'T005';
		$form_campo_pk = 'T005_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = '';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '';
        
        //form editor
        $form_editor = array(
            'geral' => '{"T005_Valor_Total_Produtos_Sem_Desconto":{"top":7,"left":31,"width":227,"hidden":0},"T005_Valor_Desconto":{"top":30,"left":30,"width":227,"hidden":0},"T005_Valor_Total_Servicos":{"top":53.00001525878906,"left":31,"width":227,"hidden":0},"ven002AlcadaPedido":{"top":53.00001525878906,"left":192,"width":177,"hidden":1},"T005_Valor_ICMS_Substituicao_Retencao":{"top":76.00001525878906,"left":56,"width":227,"hidden":0},"ven002AlcadaDesconto":{"top":76.00001525878906,"left":192,"width":177,"hidden":1},"T005_Valor_Total_IPI":{"top":99.00001525878906,"left":64,"width":227,"hidden":0},"ven002AlcadaDescontoProduto":{"top":99.00001525878906,"left":192,"width":177,"hidden":1},"T005_Valor_Despesas_Acessorias":{"top":122.00001525878906,"left":14,"width":227,"hidden":0},"ven002AlcadaPrazoMedio":{"top":122.00001525878906,"left":192,"width":177,"hidden":1},"T005_Valor_Total_Agregados":{"top":145.00001525878906,"left":20,"width":227,"hidden":0},"ven002AlcadaMotivoLiberacao":{"top":145.00001525878906,"left":192,"height":75,"width":147,"hidden":1},"T005_Valor_Troca":{"top":168.00001525878906,"left":40,"width":227,"hidden":0},"T005_Valor_Frete":{"top":191.00001525878906,"left":49,"width":227,"hidden":0},"T005_Valor_Total":{"top":214.00001525878906,"left":52,"width":227,"hidden":0},"T005_Valor_Total_ICMS":{"top":237.00001525878906,"left":50,"width":227,"hidden":0},"T006_Valor_ICMS_Credito":{"top":260.00001525878906,"left":13,"width":227,"hidden":0},"T005_IPV":{"top":283.00001525878906,"left":58,"width":227,"hidden":0},"T005_Peso_Liquido":{"top":306.00001525878906,"left":32,"width":227,"hidden":0},"T005_Valor_Desconto_Manual":{"top":328.98338317871094,"left":9,"width":227,"hidden":1}}'
        );
		
		//$totalServicosConfirmado = $VEN002->calculaTotalServicos($r_acaoId);
		$totalGeralConfirmado = $VEN002->calculaTotalGeral($r_acaoId);
		
        /*
        * ************** IMPORTANTEEEEEE!!!! *****************
        *  Qualquer coisa que for mudado daqui para baixo até o próximo marcador, precisa ser mudado tbm na função $VEN002->motivoPedidoLiberacao, e no documento ven002/form_func-ajax.php -> case 'verificaAlcada';
        *  Se possível, quando der, criar uma única função para os três casos de uso.
        */
		//Calcula Desconto por alçada
		$descontoPedido = $VEN002->calculaPercentualDescontoPedido($r_acaoId);
		$descontoVendedor = gCorrigeNumeroInverte($g['conf']->ini('alcadaDescontoVendedor'));
		$descontoSupervisor = gCorrigeNumeroInverte($g['conf']->ini('alcadaDescontoSupervisor'));

		$alcadaDescontoVendedorPorProduto = $g['conf']->ini('alcadaDescontoVendedorPorProduto');
		$alcadaDescontoSupervisorPorProduto = $g['conf']->ini('alcadaDescontoSupervisorPorProduto');
		$alcadaDescontoSupervisorPorProduto = gCorrigeNumeroInverte($alcadaDescontoSupervisorPorProduto);

		//Calcula prazo médio por alçada
		$prazoMedioAlcadaVendedor = $VEN002->calculaPrazoMedioAlcadaVendedor($r_acaoId);
		$prazoMedioAlcadaSupervisor = $VEN002->calculaPrazoMedioAlcadaSupervisor($r_acaoId);
		$retorno=mysql_query("SELECT AVG(T090_Prazos_Dias) FROM T090 WHERE T090_T005_Id='{$r_acaoId}'");
		$T005=mysql_fetch_array($retorno);				
		$prazoMedio=$T005['AVG(T090_Prazos_Dias)'];
        $retornoSaldoMinimo = $VEN002->verificaFuturoSaldoProduto($r_acaoId,true);

        $T005MaiorDesconto = $VEN002->retornaMaiorDescontoPedido($r_acaoId);

        $usarAlcadaPorProduto = $g['conf']->ini('usarAlcadaPorProduto');

  		//Verificações de alçada
		if(($descontoPedido <= $descontoVendedor) && ($prazoMedio <= $prazoMedioAlcadaVendedor) && ($retornoSaldoMinimo==0)){

			$prazoMedio=gCorrigeNumero($T005['AVG(T090_Prazos_Dias)']);
			$descontoPedidoUsar = gCorrigeNumero($descontoPedido);
			$T005MaiorDescontoUsar = gCorrigeNumero($T005MaiorDesconto);
			$alcadaPedido = "Alçada: Vendedor";
			$alcadaDesconto = "Desconto: {$descontoPedidoUsar}%";
			$alcadaMaiorDesconto = "Maior Desconto: {$T005MaiorDescontoUsar}%";
			$alcadaPrazoMedio = "Prazo Médio: {$prazoMedio} Dias";

		} elseif (($descontoPedido <= $descontoSupervisor) && ($prazoMedio <= $prazoMedioAlcadaSupervisor)){

			$prazoMedio=gCorrigeNumero($T005['AVG(T090_Prazos_Dias)']);
			$descontoPedidoUsar = gCorrigeNumero($descontoPedido);
			$T005MaiorDescontoUsar = gCorrigeNumero($T005MaiorDesconto);
			$alcadaPedido = "Alçada: Supervisor";
			$alcadaDesconto = "Desconto: {$descontoPedidoUsar}%";
			$alcadaMaiorDesconto = "Maior Desconto: {$T005MaiorDescontoUsar}%";
			$alcadaPrazoMedio = "Prazo Médio: {$prazoMedio} Dias";

		} else {

			$prazoMedio=gCorrigeNumero($T005['AVG(T090_Prazos_Dias)']);
			$descontoPedidoUsar = gCorrigeNumero($descontoPedido);
			$T005MaiorDescontoUsar = gCorrigeNumero($T005MaiorDesconto);
			$alcadaPedido = "Alçada: Diretor";
			$alcadaDesconto = "Desconto: {$descontoPedidoUsar}%";
			$alcadaMaiorDesconto = "Maior Desconto: {$T005MaiorDescontoUsar}%";
			$alcadaPrazoMedio = "Prazo Médio: {$prazoMedio} Dias";
		}

		// Verificação apenas para quando a global $usarAlcadaPorProduto for igual a sim
		if($usarAlcadaPorProduto == "S"){

			if($T005MaiorDesconto <= $alcadaDescontoVendedorPorProduto){

				$prazoMedio=gCorrigeNumero($T005['AVG(T090_Prazos_Dias)']);
				$descontoPedidoUsar = gCorrigeNumero($descontoPedido);
				$T005MaiorDescontoUsar = gCorrigeNumero($T005MaiorDesconto);
				$alcadaPedido = "Alçada: Vendedor";
				$alcadaDesconto = "Desconto: {$descontoPedidoUsar}%";
				$alcadaMaiorDesconto = "Maior Desconto: {$T005MaiorDescontoUsar}%";
				$alcadaPrazoMedio = "Prazo Médio: {$prazoMedio} Dias";

			}elseif($T005MaiorDesconto <= $alcadaDescontoSupervisorPorProduto){

				$prazoMedio=gCorrigeNumero($T005['AVG(T090_Prazos_Dias)']);
				$descontoPedidoUsar = gCorrigeNumero($descontoPedido);
				$T005MaiorDescontoUsar = gCorrigeNumero($T005MaiorDesconto);
				$alcadaPedido = "Alçada: Supervisor";
				$alcadaDesconto = "Desconto: {$descontoPedidoUsar}%";
				$alcadaMaiorDesconto = "Maior Desconto: {$T005MaiorDescontoUsar}%";
				$alcadaPrazoMedio = "Prazo Médio: {$prazoMedio} Dias";

			}else{

				$prazoMedio=gCorrigeNumero($T005['AVG(T090_Prazos_Dias)']);
				$descontoPedidoUsar = gCorrigeNumero($descontoPedido);
				$T005MaiorDescontoUsar = gCorrigeNumero($T005MaiorDesconto);
				$alcadaPedido = "Alçada: Diretor";
				$alcadaDesconto = "Desconto: {$descontoPedidoUsar}%";
				$alcadaMaiorDesconto = "Maior Desconto: {$T005MaiorDescontoUsar}%";
				$alcadaPrazoMedio = "Prazo Médio: {$prazoMedio} Dias";

			}
		}
        
        $motivoPedidoLiberacao= '';
        $retornoStatus=mysql_query("SELECT * FROM T005 WHERE T005_Id='{$r_acaoId}'");
        $T005Status=mysql_fetch_array($retornoStatus);
        if($T005Status['T005_Flag_Status'] == '14'){
            $motivoPedidoLiberacao .= "                APROVADO     \n";
        }
        if(!empty($T005Status['T005_Motivo_Pedido_Liberacao'])){
            $motivoPedidoLiberacao .= $T005Status['T005_Motivo_Pedido_Liberacao'];
        }else{
            $motivoPedidoLiberacao .= $VEN002->motivoPedidoLiberacao($r_acaoId);
        }

        $total=$T005Status['T005_Valor_Total_Produtos'];

		$T006=mysql_query("select sum(T006_Valor_ICMS_Credito) T006_Valor_ICMS_Credito,
                          T006_Aliquota_ICMS_Credito
                     	  from T006
                    	  where T006_T005_Id='{$r_acaoId}'
                    	  group by T006_T005_Id");
		echo mysql_error();
		$mT006=mysql_fetch_array($T006);
   
        $mT006['T008_Valor_ICMS_Credito']=number_format($mT006['T006_Valor_ICMS_Credito'],2,',','.');

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
     				'T005_Valor_Total_Produtos_Sem_Desconto' => array('name' => 'T005_Valor_Total_Produtos_Sem_Desconto', 'label' => 'Produtos', 'type' => 'moeda', 'length' => array(40, 80), 'disabled' => 'true', 'extra'=>'style=background:#E8E8E8;'),
     				'T005_Valor_Desconto' => array('name' => 'T005_Valor_Desconto', 'label' => 'Desc.R$.', 'type' => 'moeda', 'length' => array(40,80),'extra' => "onchange=\"gravarDescontoPedido(this, '{$g['divId']}', '{$r_T005_Id}', 'T005_Valor_Desconto');\"",'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'),
     				'T005_Valor_Desconto_Manual' => array('name' => 'T005_Valor_Desconto_Manual', 'label' => 'Desc Manual.', 'type' => 'moeda', 'length' => array(40,80),'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'),
     				'T005_Valor_Total_Servicos' => array('name' => 'T005_Valor_Total_Servicos', 'label' => 'Serviços', 'type' => 'moeda', 'length' => array(40, 80), 'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'),
					'T005_Valor_ICMS_Substituicao_Retencao' => array('name' => 'T005_Valor_ICMS_Substituicao_Retencao', 'label' => 'S.T.', 'type' => 'moeda', 'length' => array(40,40), 'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'),
					'T005_Valor_Total_IPI' => array('name' => 'T005_Valor_Total_IPI', 'label' => 'IPI', 'type' => 'moeda', 'length' => array(40,80), 'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'),
					'T005_Valor_Despesas_Acessorias' => array('name' => 'T005_Valor_Despesas_Acessorias', 'label' => 'Desp.Acess', 'type' => 'moeda', 'length' => array(40,80), 'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'),
					'T005_Valor_Total_Agregados' => array('name' => 'T005_Valor_Total_Agregados', 'label' => 'Agregados', 'type' => 'moeda', 'length' => array(40,80), 'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'),
					'T005_Valor_Troca' => array('name' => 'T005_Valor_Troca', 'label' => 'Trocas', 'type' => 'moeda', 'length' => array(40,80), 'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'),
					'T005_Valor_Frete' => array('name' => 'T005_Valor_Frete', 'label' => 'Frete', 'type' => 'moeda', 'length' => array(40,80), 'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'),
     				'T005_Valor_Total' => array('name' => 'T005_Valor_Total', 'label' => 'Total', 'type' => 'moeda', 'length' => array(40, 80), 'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'),
					'T005_Valor_Total_ICMS' => array('name' => 'T005_Valor_Total_ICMS', 'label' => 'ICMS', 'type' => 'moeda', 'length' => array(40,80), 'disabled' => 'true', 'extra'=>'style=background:#E8E8E8;'),
                    'T006_Valor_ICMS_Credito' => array('name' => 'T006_Valor_ICMS_Credito', 'value' => $mT006['T006_Valor_ICMS_Credito'], 'label' => 'ICMS Crédito', 'type' => 'moeda', 'length' => array(40,80), 'casasDecimais' => '2', 'disabled' => 'true','reject' => true, 'extra'=>'style=background:#E8E8E8;'),
					'T005_IPV' => array('name' => 'T005_IPV', 'label' => 'IPV', 'type' => 'text', 'length' => array(40,80), 'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'),
					'T005_Peso_Liquido' => array('name' => 'T005_Peso_Liquido', 'label' => 'Peso KG', 'type' => 'moeda', 'length' => array(40,80), 'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'),
					//'T005_Valor_Total' => array('name' => 'T005_Valor_Total', 'label' => 'Total','reject' => true, 'value' =>gCorrigeNumero($totalGeralConfirmado), 'type' => 'moeda', 'length' => array(40,80), 'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'),
					'ven002AlcadaPedido' => array('name' => 'ven002AlcadaPedido', 'label' => '','type' => 'text','reject' => true,'value' => $alcadaPedido, 'length' => array(20,40), 'disabled' => 'true',  'extra'=>'style=margin-left:200px;:right;background:yellow;border-color: var(--BGVermelho);','length' => array(30,30)),
					'ven002AlcadaDesconto' => array('name' => 'ven002AlcadaDesconto', 'label' => '','type' => 'text','reject' => true,'value' => $alcadaDesconto, 'length' => array(20,40), 'disabled' => 'true',  'extra'=>'style=margin-left:200px;:right;background:yellow;border-color: var(--BGVermelho);','length' => array(30,30)),

					'ven002AlcadaDescontoProduto' => array('name' => 'ven002AlcadaDescontoProduto', 'label' => '','type' => 'text','reject' => true,'value' => $alcadaMaiorDesconto, 'length' => array(20,40), 'disabled' => 'true',  'extra'=>'style=margin-left:200px;:right;background:yellow;border-color: var(--BGVermelho);','length' => array(30,30)),

					'ven002AlcadaPrazoMedio' => array('name' => 'ven002AlcadaPrazoMedio', 'label' => '', 'type' => 'text','reject' => true,'value' => $alcadaPrazoMedio, 'length' => array(20,40), 'disabled' => 'true',  'extra'=>'style=margin-left:200px;:right;background:yellow;border-color: var(--BGVermelho);','length' => array(30,30)),
                    'ven002AlcadaMotivoLiberacao' => array('name' => 'ven002AlcadaMotivoLiberacao', 'label' => '', 'type' => 'textarea','reject' => true,'value' => $motivoPedidoLiberacao, 'length' => array(5,26), 'disabled' => 'true',  'extra'=>'style=margin-left:200px;:right;background:yellow;border-color: var(--BGVermelho);'),
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
			} 
		} //else {
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
		//}
         
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
	
	case 'ven002FormAplicarIpv':
	
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
		$form_tabela = 'T005';
		$form_campo_pk = 'T005_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = '';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '';
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					'T005_Id' => array('name' => 'T005_Id', 'label' => 'T005_Id', 'type' => 'hidden', 'length' => array(40, 80), 'reject' => true, 'value' => $r_T005_Id),
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
	
	case 'ven002FormImportarItens':
	
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
		$form_tabela = 'T005';
		$form_campo_pk = 'T005_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = '';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '';
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					'T005_Id' => array('name' => 'T005_Id', 'label' => 'T005_Id', 'type' => 'hidden', 'length' => array(40, 80), 'reject' => true, 'value' => $r_T005_Id),
     				'T005_Id_Origem' => array('name' => 'T005_Id_Origem', 'label' => 'Informar Ítens', 'type' => 'text', 'length' => array(40, 80), 'reject' => true),
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
	
	case 'ven002FormFaturarEmergencia':
         global $g;
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
				
		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'T005';
		$form_campo_pk = 'T005_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'ven002FormFaturarEmergencia_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/ven/ven002/form/ven002FormFaturarEmergencia/';
				
		// novo formulário
		$form = new Formulario('ven002FormFaturarEmergencia');

		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T005_Observacao_Fatura_Emergencia' => array('name' => 'T005_Observacao_Fatura_Emergencia', 'label'=>'Observação', 'type' => 'textarea', 'length' => array(3,60)),
					'T005_Liberacao_Credito_Id' => array('name' => 'T005_Liberacao_Credito_Id', 'label'=>'IdOperador', 'type' => 'hidden','value' => $g['usuarioAtual']),
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
				// Estorna o Título
				$retorno = $VEN002->faturarEmergencia($acaoId, $form);
				$retorno = ($retorno === true) ? 'Pedido faturado com emergência!' : $retorno;
				// Mensagem
				$alert = gGeraAlertMsg('Faturamento com emergência', $retorno, false);
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
		
		/**
		* Botões
		*/
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="ven002FormFaturarEmergencia_btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		//$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
				
	break;
	
	case 'ven002FormDocumentos':
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
		$form_tabela = 'T167';
		$form_campo_pk = 'T167_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = array('ven002FormDocumentosBtnAdd', 'ven002FormDocumentosBtnCln', 'ven002FormDocumentosBtnNew');
        // Form que será executado ao clicar no botão
		$enviar_acaoHref = '/ven/ven002/form/ven002FormDocumentos/';

		//form editor
		$form_editor = array(
			'geral' => '{"T167_Descricao":{"top":30,"left":73,"width":342,"hidden":0},"uploadArquivo":{"top":7,"left":28,"width":208,"hidden":0},"ven002FormDocumentosBtnAdd":{"top":74,"left":480,"width":null,"hidden":0},"ven002FormImportarDoc":{"top":74,"left":576.0000610351562,"width":null,"hidden":0}}',
		);

		// Array de definição do formulário
		$r_T005_Id = isset($r_ven002FormDocumentos) ? $r_ven002FormDocumentos['T167_T005_Id'] : $r_T005_Id;
		                                                           
		$tmp = gGeraSelect('T005', 'T005_Id', 'T005_Numero_DI', "WHERE T005_Id = $r_T005_Id");
		foreach($tmp as &$value) { $value['selected'] = true; }
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('ven002FormDocumentos');
		$formArray = array(
			'campos' => array(
				'geral' => array(
				)
			)
		);
		//$formArray['campos']['geral']['__T167_T005_Id'] 		= array('name' => '__T167_T005_Id', 'label'=>'Número Pedido', 'type' => 'select', 'extra'=>'style="width:382px;"', 'disabled' => true, 'reject'=> true, 'select' => 
		//	$tmp
		//);
		$formArray['campos']['geral']['T167_Descricao'] 	= array('name' => 'T167_Descricao', 'label'=>'Descrição', 'type' => 'textarea','length' => array(3,60));
		$formArray['campos']['geral']['uploadArquivo'] 			= array('name' => 'uploadArquivo', 'label' => 'Arquivo documento', 'type' => 'file', 'length' => array(24, 24), 'reject' => true, 'uppercase' => false);
		$formArray['campos']['geral']['T167_Nome_Arquivo'] 		= array('name' => 'T167_Nome_Arquivo', 'type' => 'hidden', 'uppercase' => false);
		if (!empty($r_acaoId)) {
			 $formArray['campos']['geral']['ven002FormDocumentosBtnNew'] = array('name' => 'ven002FormDocumentosBtnNew', 'type' => 'button', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		}
		$formArray['campos']['geral']['T167_T005_Id'] 			= array('name' => 'T167_T005_Id', 'type' => 'hidden', 'value' => $r_T005_Id);
		$formArray['campos']['geral']['T167_C007_Id'] 			= array('name' => 'T167_C007_Id', 'type' => 'hidden', 'value' => $g['usuarioAtual']);
		$formArray['campos']['geral']['T167_Data_Hora_UpLoad'] 			= array('name' => 'T167_Data_Hora_UpLoad', 'type' => 'hidden', 'value' => date('Y-m-d H:i:s'));
		//$formArray['campos']['geral']['ven002FormDocumentosBtnCln'] = array('name' => 'ven002FormDocumentosBtnCln', 'type' => 'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['geral']['ven002FormDocumentosBtnAdd'] = array('name' => 'ven002FormDocumentosBtnAdd', 'type' => 'submit', 'reload' => false, 'value' => empty($r_acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
        $formArray['campos']['geral']['ven002FormImportarDoc'] = array('name' => 'ven002FormImportarDoc', 'type' => 'button', 'reload' => false, 'value' => 'Importar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
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
						$form->campoSetarValor('T167_Nome_Arquivo', $arquivo['name'], true);
                        mysql_query("SET AUTOCOMMIT=0");
                        mysql_query("START TRANSACTION");
						if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
							// Gravar o arquivo no disco
							$pathinfo = pathinfo($arquivo['name']);
							$pathinfo['extension'] = empty($pathinfo['extension']) ? '.tmp' : $pathinfo['extension'];
							//$dir = "{$g['pathDados']}documentos/T005_".str_repeat('0',5-strlen($form->campoValorEnviado('T167_T005_Id'))).$form->campoValorEnviado('T167_T005_Id');
							$dir = "{$g['pathDados']}pedido";
							is_dir($dir) or mkdir($dir, 0777, true);
							$file = $dir . '/' . $g['mysqlLastId'] . '.' . $pathinfo['extension'];
							$retornoMoveFile = move_uploaded_file($arquivo['tmp_name'], $file);
							log("move upload file ".$arquivo['tmp_name'].' -> '.$file);
							if (!$retornoMoveFile){
			                    mysql_query("ROLLBACK");
								echo <<<EOT
									<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
										dialogConfirm('ERRO', 'Não foi possível realizar o upload do arquivo, tente novamente. Caso o problema persista informar o suporte técnico.');
										if (typeof jQuery != 'undefined') {
											divRefresh("{$g['divId']}", true);
											divRefresh('{$form->campoValorEnviado('divReload')}', true);
											divRefresh('{$g['divId']}', true);
										}
									} </script></div>
EOT;
							}
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
		            mysql_query("COMMIT");
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
        echo <<<EOT
            <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                $('#ven002FormImportarDoc').unbind('click').bind('click', function(data) {
                    abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Importar Documento', '/ven/ven002/content/ven002ContentImportarDoc/', '&T005_Id=' + encodeURIComponent('{$r_T005_Id}') + '&divReload=' + encodeURIComponent('{$r_divReload}'), [300,100]);
                });
            } </script></div>
EOT;
	break;
	
	case 'ven002FormDetalhesCheque':
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
		$form_tabela = 'T090';
		$form_campo_pk = 'T090_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'ven002FormDetalhesCheque_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/ven/ven002/form/ven002FormDetalhesCheque/';
				
		// novo formulário
		$form = new Formulario('ven002ContentDetalhesCheque');

		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T090_Numero_Cheque' => array('name' => 'T090_Numero_Cheque', 'label' => 'Número', 'type' => 'text', 'length' => array(20, 120)),
					'T090_Data_Cheque' => array('name' => 'T090_Data_Cheque', 'label' => 'Data', 'type' => 'data', 'length' => array(20, 120)),					
					'T090_Agencia_Cheque' => array('name' => 'T090_Agencia_Cheque', 'label' => 'Agência', 'type' => 'text', 'length' => array(20, 120)),
					'T090_Conta_Corrente_Cheque' => array('name' => 'T090_Conta_Corrente_Cheque', 'label' => 'Conta Corrente', 'type' => 'text', 'length' => array(20, 120)),					
					'T090_Banco_Cheque' => array('name' => 'T090_Banco_Cheque', 'label' => 'Banco', 'type' => 'text', 'length' => array(50, 120)),									
					'T090_Nome_Cheque' => array('name' => 'T090_Nome_Cheque', 'label' => 'Nome', 'type' => 'text', 'length' => array(50, 120)),					
					'T090_Cidade_Cheque' => array('name' => 'T090_Cidade_Cheque', 'label' => 'Cidade', 'type' => 'text', 'length' => array(50, 120)),
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
	
	case 'ven002FormPedidoFaturamento':
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
		$form_tabela = 'T005';
		$form_campo_pk = 'T005_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = array('ven002FormPedidoFaturamentoBtnAdd', 'ven002FormPedidoFaturamentoBtnCln', 'ven002FormPedidoFaturamentoBtnNew');
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/ven/ven002/form/ven002FormPedidoFaturamento/';

		// form editor
		$form_editor = array('geral' => '{"T005_D024_Id_Nfe":{"top":7,"left":18,"width":215,"hidden":0},"T005_D024_Id_Contas":{"top":30,"left":12,"width":215,"hidden":0},"ven002FormPedidoFaturamentoBtnCln":{"top":51,"left":256,"width":null,"hidden":0},"ven002FormPedidoFaturamentoBtnAdd":{"top":51,"left":336,"width":null,"hidden":0}}');

		// novo formulário
		$form = new Formulario('fis009FormCadastroPais');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T005_D024_Id_Nfe' => array('name' => 'T005_D024_Id_Nfe','label'=>'Cliente para emissão de NFe', 'type' => 'pesquisa', 'length' => array(50,30),
						'pesquisa' => array('D024','D024_Id','D024_Id,D024_Nome_Empresa,D020_Nome_Cidade,D018_UF,D024_Cpf,D024_Cnpj','left join D020 on D020_Id=D024_D020_Id left join D018 on D018_Id=D024_D018_Id WHERE D024_Flag_Cliente="S" and D024_Flag_Ativo="S" order by D024_Nome_Empresa','(%1) %2 [%3 - %4]'),
					),
					'T005_D024_Id_Contas' => array('name' => 'T005_D024_Id_Contas','label'=>'Cliente para contas a receber', 'type' => 'pesquisa', 'length' => array(50,30),
						'pesquisa' => array('D024','D024_Id','D024_Id,D024_Nome_Empresa,D020_Nome_Cidade,D018_UF,D024_Cpf,D024_Cnpj','left join D020 on D020_Id=D024_D020_Id left join D018 on D018_Id=D024_D018_Id where D024_Flag_Cliente="S" and D024_Flag_Ativo="S" order by D024_Nome_Empresa','(%1) %2 [%3 - %4]'),
					),
				)
			)
		);
		$formArray['campos']['geral']['ven002FormPedidoFaturamentoBtnCln'] = array('name' => 'ven002FormPedidoFaturamentoBtnCln', 'type'=>'button', 'reload' => false, 'value' => 'Limpar', 'style' => 'float:left;', 'reject' => true);
		$formArray['campos']['geral']['ven002FormPedidoFaturamentoBtnAdd'] = array('name' => 'ven002FormPedidoFaturamentoBtnAdd', 'type'=>'button', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:left;', 'reject' => true);

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
									{$alert}
								divRefresh('{$g['divIdParent']}', true);
							} </script></div>
EOT;
					}
				} else {
					// Insert
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						// Imprime uma mensagem e fecha a janela
						$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									{$alert}
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
	
	case 'ven002formTrocas':

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
        $form_parent = 'T005_Id';
        $form_parent_valor = $r_T005_Id;
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = array('ven002formTrocasBtnAdd', 'ven002formTrocasBtnCln', 'ven002formTrocasBtnNew');
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/ven/ven002/form/ven002formTrocas/';
		
		//form editor
		$form_editor = array( 'trocas' => '{"Procura_Produto_Troca":{"top":7,"left":53,"width":345,"hidden":0},"Descricao_Produto":{"top":30,"left":41,"width":345,"hidden":0},"Marca_Produto":{"top":53,"left":60,"width":125,"hidden":0},"T138_Quantidade":{"top":76,"left":35,"width":125,"hidden":0},"T138_Valor_Preco_Unitario":{"top":99,"left":65,"width":125,"hidden":0},"T138_Operacao":{"top":122,"left":42,"width":132,"hidden":0},"T138_Observacao":{"top":145,"left":30,"width":345,"hidden":0},"ven002formTrocasBtnAdd":{"top":166,"left":384,"width":null,"hidden":0},"ven002formTrocasBtnCln":{"top":166,"left":320,"width":null,"hidden":0},"ven002BtnCadastrarProdutoTroca":{"top":166,"left":176,"width":null,"hidden":0}}');

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
			'ven002CampoProcuraProdutoTroca',																	// callback js
			$g['divId'],																						// divId
			(isset($r_ven002FormProdutosForm) ? $r_ven002FormProdutosForm['T138_T005_Id'] : $r_T005_Id),		// parametro extra
			array(																								// parametros para cada campo do grid
				array('colunaAlinhar' => 'center'),
				array(),
				array(),
				array('callback' => 'gCorrigeNumero', 'colunaAlinhar' => 'right')
			)
		);
		// novo formulário
		$form = new Formulario('ven002formTrocasForm');
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
					'T138_T005_Id' => array('name' => 'T138_T005_Id', 'label' => '', 'type' => 'hidden', 'value' => @$r_T005_Id),
					'T138_C004_Id' => array('name' => 'T138_C004_Id', 'label' => '', 'type' => 'hidden', 'value' => $g['empresaAtual']),
					'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true),
					'T138_D009_Id' => array('name' => 'T138_D009_Id', 'label' => '', 'type' => 'hidden'),

				),
			)
		);
		// Botão Limpar / Novo Registro
		if (!empty($acaoId)) {
			 $formArray['campos']['trocas']['ven002formTrocasBtnNew'] = array('name' => 'ven002formTrocasBtnNew', 'type'=>'submit', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		}
		$formArray['campos']['trocas']['ven002formTrocasBtnAdd'] = array('name' => 'ven002formTrocasBtnAdd', 'type'=>'button', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['trocas']['ven002formTrocasBtnCln'] = array('name' => 'ven002formTrocasBtnCln', 'type'=>'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['trocas']['ven002BtnCadastrarProdutoTroca'] = array('name' => 'ven002BtnCadastrarProdutoTroca', 'type'=>'button', 'reload' => false, 'value' => 'Pré-Cadastrar Produto', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);	
		$form->definirFormularioArray($formArray);
		
		echo <<<EOT
		    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		    	$('#ven002BtnCadastrarProdutoTroca').unbind('click');
				$('#ven002BtnCadastrarProdutoTroca').unbind('click').bind('click', function(data) {
					//var id = $('#{$g['divId']} #T005_D024_Id').val();
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
				$retorno = $VEN002->incluirProdutoTroca($form);
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
 
	case 'ven002FormImportarDoc':
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
        $form_tabela = 'T167';
        $form_campo_pk = 'T167_Id';
        // ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
        $enviar_btnId = 'ven002FormDocumentosImportar';
        // Form que será executado ao clicar no botão
        $enviar_acaoHref = '/ven/ven002/form/ven002FormImportarDoc/';
        // Array de definição do formulário
        $r_T005_Id = isset($r_ven002FormDocumentos) ? $r_ven002FormDocumentos['T167_T005_Id'] : $r_T005_Id;
                                                                   
        $tmp = gGeraSelect('T005', 'T005_Id', 'T005_Numero_DI', "WHERE T005_Id = $r_T005_Id");
        foreach($tmp as &$value) { $value['selected'] = true; }
        // Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
        $form = new Formulario('ven002FormDocumentosImportar');
        $formArray = array(
            'campos' => array(
                'geral' => array(
                )
            )
        );
        $formArray['campos']['geral']['Numero_Pedido']     = array('name' => 'Numero_Pedido', 'label'=>'N° Pedido', 'type' => 'text','reject' => true,'length' => array(20,30));
        $formArray['campos']['geral']['T167_T005_Id']             = array('name' => 'T167_T005_Id', 'type' => 'hidden', 'value' => $r_T005_Id);
        $formArray['campos']['geral']['T167_C007_Id']             = array('name' => 'T167_C007_Id', 'type' => 'hidden', 'value' => $g['usuarioAtual']);
        $formArray['campos']['geral']['T167_Data_Hora_UpLoad']             = array('name' => 'T167_Data_Hora_UpLoad', 'type' => 'hidden', 'value' => date('Y-m-d H:i:s'));
        $formArray['campos']['geral']['divReload']     = array('name' => 'divReload', 'label'=>'', 'type' => 'hidden','value' => @$r_divReload, 'reject' => true);
        
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
                                    if (typeof jQuery != 'undefined') {
                                        divRefresh("{$g['divId']}", true);
                                        divRefresh('{$form->campoValorEnviado('divReload')}', true);
                                        divRefresh('{$g['divId']}', true);
                                    }
                                } </script></div>
EOT;
                    }
                } else {
                    // Importação
                    $retorno = $VEN002->importarDocsPedido($form);
                    if ($retorno === true) {
                        $alert .= gGeraAlertMsg('Operação OK', 'Documentos importados com sucesso!', false);
                        echo <<<EOT
                            <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                                divRefresh('{$form->campoValorEnviado('divReload')}', true);
                                if (typeof jQuery != 'undefined') {
                                    $("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
                                        setTimeout(function() {
                                    {$alert}
                                }, 150);
                                });
                                $("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
                                }
                            } </script></div>
EOT;
                    }else{
                       $alert .= gGeraAlertMsg('ERRO', "{$retorno}", false);
                        echo <<<EOT
                            <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                                if (typeof jQuery != 'undefined') {
                                    {$alert}
                                    divRefresh('{$form->campoValorEnviado('divReload')}', true);
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
        echo $gerado['pagina'];
        if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
        $g['smarty']->display('form.tpl');
        
        
        
        /**
        * Botões
        */
        // Definição dos botões
        $botoesEsq = '';
        $botoesDir = '';
        $botoesDir .= '<button type="submit" id="ven002FormDocumentosImportar">Enviar</button>';
        $botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
        $g['smarty']->assign('botoesId', $g['divId']);
        $g['smarty']->assign('botoesEsq', $botoesEsq);
        $g['smarty']->assign('botoesDir', $botoesDir);
        echo $g['smarty']->fetch('buttons-floatBottom.tpl');
        // Ações dos botões
        // A ação do botão Enviar é definido dentro do formulário
        echo <<<EOT
            <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
            } </script></div>
EOT;
    break;
    
    case 'ven002FormCancelarPedido':

        /**
        * PARTE 1: Variavéis envidas para o formulário
        */
        // Variável indica qual tab do form será exibida
        $tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
        // Utilizada em UPDATE, é o ID do registro que está sendo editado
        $T005_Id = (isset($r_T005_Id)) ? $r_T005_Id : false;
        
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
        $form_tabela = 'T005';
        $form_campo_pk = 'T005_Id';
        // ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
        $enviar_btnId = 'ven002FormCancelarPedido_Btn';
        // Form que será executado ao clicar no botão
        $enviar_acaoHref = '/ven/ven002/form/ven002FormCancelarPedido/';

        //form editor
        $form_editor = array(
            'geral' => '{"T005_D047_Id":{"top":7,"left":43,"width":335,"hidden":0},"T005_Observacao_Pedido_Perdido":{"top":30,"left":14,"height":177,"width":328,"hidden":0}}'
        );

        // Array de definição do formulário
        //$r_T005_Id = isset($r_ven002FormDocumentos) ? $r_ven002FormDocumentos['T167_T005_Id'] : $r_T005_Id;
                                                                   
        ///$tmp = gGeraSelect('D047', 'D047_Id', 'D047_Descricao_Motivo', "order by D047_Descricao_Motivo");
//        foreach($tmp as &$value) { $value['selected'] = true; }
        // Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
        $form = new Formulario('ven002FormCancelarPedidoForm');
        $formArray = array(
            'campos' => array(
                'geral' => array(
                	'T005_D047_Id' => array('name' => 'T005_D047_Id', 'label'=>'Motivo','type' => 'select','select' => 
						gGeraSelect('D047', 'D047_Id', 'D047_Descricao_Motivo',''),
					),
                )
            )
        );
        //$formArray['campos']['geral']['T005_D047_Id']     				= 
        $formArray['campos']['geral']['T005_Observacao_Pedido_Perdido'] = array('name' => 'T005_Observacao_Pedido_Perdido', 'label'=>'Observação', 'type' => 'textarea');
        $formArray['campos']['geral']['T005_Id'] = array('name' => 'T005_Id', 'label'=>'Observação', 'type' => 'hidden', 'value'=>$T005_Id);
        //$formArray['campos']['geral']['divReload']     = array('name' => 'divReload', 'label'=>'', 'type' => 'hidden','value' => @$r_divReload, 'reject' => true);
        
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
                                    if (typeof jQuery != 'undefined') {
                                        divRefresh("{$g['divId']}", true);
                                        divRefresh('{$form->campoValorEnviado('divReload')}', true);
                                        divRefresh('{$g['divId']}', true);
                                    }
                                } </script></div>
EOT;
                    }
                } else {
                    $retorno = $VEN002->ven002VerificaCancelarPedido($form);
                    if ($retorno === true) {
                        $alert .= gGeraAlertMsg('Operação OK', 'Pedido Cancelado com sucesso!', false);
                        echo <<<EOT
                            <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                                divRefresh('{$g['divIdParent']}', true);
                                if (typeof jQuery != 'undefined') {
                                    $("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
                                        setTimeout(function() {
                                    {$alert}
                                }, 150);
                                });
                                $("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
                                }
                            } </script></div>
EOT;
                    }else{
                       $alert = gGeraAlertMsg('ERRO', "{$retorno}", false);
                        echo <<<EOT
                            <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                                if (typeof jQuery != 'undefined') {
                                    {$alert}
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
        $gerado = gGeraCamposForm($form, $tab, $T005_Id, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);
        
        
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
        $botoesDir .= '<button type="submit" id="ven002FormCancelarPedido_Btn">Enviar</button>';
        $botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
        $g['smarty']->assign('botoesId', $g['divId']);
        $g['smarty']->assign('botoesEsq', $botoesEsq);
        $g['smarty']->assign('botoesDir', $botoesDir);
        echo $g['smarty']->fetch('buttons-floatBottom.tpl');
        // Ações dos botões
        // A ação do botão Enviar é definido dentro do formulário
        echo <<<EOT
            <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
            } </script></div>
EOT;

    break;
    
    
    case 'ven002FormAgregados':
            /**
            * PARTE 1: Variavéis envidas para o formulário
            */
            // Variável indica qual tab do form será exibida
            $tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
            // Utilizada em UPDATE, é o ID do registro que está sendo editado
            $T006_Id = (isset($_REQUEST['T006_Id'])) ? $_REQUEST['T006_Id'] : false;
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
            $form_tabela = 'T204';
            $form_campo_pk = 'T204_Id';
            // ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
            $enviar_btnId = array('ven002FormAgregadosBtnAdd', 'ven002FormAgregadosBtnCln', 'ven002FormAgregadosBtnNew');
            // Form que será executado ao clicar no botão
            $enviar_acaoHref = '/ven/ven002/form/ven002FormAgregados/';

            // novo formulário
            $form = new Formulario('ven002FormAgregados');

            // Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
            $formArray = array(
                'campos' => array(
                    'geral' => array(
                    'T204_T006_Id' => array('name' => 'T204_T006_Id', 'type' => 'hidden', 'value' => @$T006_Id, 'length' => array(50,30)),
                    'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true),
                    'T204_Descricao' => array('name' => 'T204_Descricao','label'=>'Descrição','type' => 'text', 'length' => array(50,30)),
                    'T204_Valor_Unitario' => array('name' => 'T204_Valor_Unitario','label'=>'Valor','type' => 'moeda','align' => 'right','length' => array(10,10)),
                    'T204_Observacao' => array('name' => 'T204_Observacao','label'=>'Observação','type' => 'textarea', 'length' => array(2,47)),
                    'T204_T209_Id' => array('name' => 'T204_T209_Id','label'=>'Tipo','type' => 'select', 
                    	'select' => gGeraSelect('T209', 'T209_Id', 'T209_Nome'))
                    ),
                ),
            );
            
            // Botão Limpar / Novo Registro
            if (!empty($acaoId)) {
                 $formArray['campos']['geral']['ven002FormAgregadosBtnNew'] = array('name' => 'ven002FormProcessosBtnNew', 'type'=>'button', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
            }
            //$formArray['campos']['geral']['ven002FormAgregadosBtnCln'] = array('name' => 'ven002FormProcessosBtnCln', 'type'=>'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
            $formArray['campos']['geral']['ven002FormAgregadosBtnAdd'] = array('name' => 'ven002FormProcessosBtnAdd', 'type'=>'submit', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
            $form->definirFormularioArray($formArray);




            /**
            * PARTE 3: Processamento do formulário (se foi enviado)
            *          E carregamento dos valores (se não foi enviado e possui acaoId)
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

    case 'ven002FormIncluirPedido':
    	global $g;
    	/**
			* PARTE 1: Variavéis envidas para o formulário
			*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$r_T005_Id = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;

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
		
		// busca o ID do cliente pra carregar os contatos, caso seja edição
		$idCliente = $r_idCliente ? $r_idCliente : '99999999999';
		if ($acaoId>0){
			$sql = "SELECT T005_D024_Id,D020_D022_Id,D022_D036_Id, D024_D022_Id FROM T005 LEFT JOIN D024 ON D024_Id=T005_D024_Id LEFT JOIN D020 ON D020_Id=D024_D020_Id LEFT JOIN D022 ON D022_Id=D020_D022_Id LEFT JOIN D036 ON D036_Id=D022_D036_Id WHERE T005_Id = '{$acaoId}'";
			$result = mysql_query($sql);
			$aD024 = mysql_fetch_assoc($result);		
			$idCliente=$aD024['T005_D024_Id'];
			$idTransportadora=$aD024['D024_D022_Id'];
            $idTipoTransp=$aD024['D022_D036_Id'];
		} 		
		$idTipoTransp=isset($idTipoTransp) ? $idTipoTransp : false;

		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'T005';
		$form_campo_pk = 'T005_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'ven002FormCabecalhoBtnAdd';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/ven/ven002/form/ven002FormIncluirPedido/';
		
		$form_editor = array(
			'geral'=>'{"T005A_D148_Id_Redespacho":{"top":145,"left":45.5382080078125,"width":287,"hidden":0},"T005_D024_Id":{"top":6.99652099609375,"left":120.78125,"width":266.77778,"hidden":0},"btnPedidoIncluirCliente":{"top":5,"left":447.98614501953125,"width":null,"hidden":0},"T005_D013_Id":{"top":6.99652099609375,"left":524.3056030273438,"width":239,"hidden":0},"ven002FormContatos":{"top":5,"left":831.9966430664062,"width":null,"hidden":0},"T005_C007_Id_Vendedor_Interno":{"top":30,"left":107.7257080078125,"width":288,"hidden":0},"T005_Data_Emissao":{"top":30,"left":520.9896850585938,"width":231.77778,"hidden":0},"T005_C007_Id_Vendedor_Externo":{"top":52.986114501953125,"left":117.4132080078125,"width":287,"hidden":0},"T005_D021_Id":{"top":52.986114501953125,"left":499.73968505859375,"width":240,"hidden":0},"T005_Flag_ACP":{"top":75.98959350585938,"left":66.388916015625,"width":288,"hidden":0},"T005_Flag_Lista_Preco":{"top":75.98959350585938,"left":515.6250610351562,"width":240,"hidden":0},"T005_Data_Entrega":{"top":98.99307250976562,"left":109.63543701171875,"width":279.77778,"hidden":0},"T005_Flag_Destino_Produto":{"top":98.99307250976562,"left":525.8159790039062,"width":240,"hidden":0},"T005_D148_Id_Entrega":{"top":121.99655151367188,"left":68.54168701171875,"width":287,"hidden":0},"T005_D148_Id_Cobranca":{"top":121.99655151367188,"left":475.17364501953125,"width":239,"hidden":0},"T005_T134_Id":{"top":167.98611450195312,"left":18.62847900390625,"width":655,"hidden":0},"tituloOC":{"top":185,"left":400.9722900390625,"width":null,"hidden":0},"T005_Ordem_Compra":{"top":213.99307250976562,"left":65.5382080078125,"width":279.77778,"hidden":0},"tituloObservacoes":{"top":233.99307250976562,"left":415.97222900390625,"width":null,"hidden":0},"T005_Observacao":{"top":260.0000305175781,"left":120.78125,"height":58.77778000000001,"width":279.77778,"hidden":0},"T005_Observacao_Comercial":{"top":260.0000305175781,"left":478.40277099609375,"height":58.77778000000001,"width":279.77778,"hidden":0},"T005_Observacao_Nota_Fiscal":{"top":328.9930725097656,"left":99.375,"height":58.77778000000001,"width":279.77778,"hidden":0},"T005_Observacao_1":{"top":328.9930725097656,"left":456.85772705078125,"height":58.77778000000001,"width":279.77778,"hidden":0},"tituloTransporte":{"top":391.9792175292969,"left":407.96881103515625,"width":null,"hidden":0},"T005_Flag_Frete":{"top":420.9895935058594,"left":129.79168701171875,"width":288,"hidden":0},"T005_Valor_Frete":{"top":420.9895935058594,"left":629.6527709960938,"width":119.77778,"hidden":0},"T005_D022_Id":{"top":443.9930725097656,"left":71.99658203125,"width":266.77778,"hidden":0},"T005_Flag_Totalizar_Peso":{"top":443.9930725097656,"left":676.8750610351562,"width":64,"hidden":0},"T005_D036_Id":{"top":466.9965515136719,"left":132.91668701171875,"width":128,"hidden":0}}'
		);

		// endereço
		$arrayEndereco = gGeraSelect('D148','D148_Id','D148_Nome','WHERE D148_D024_Id='.$idCliente.' ORDER BY D148_Id',true);
		$arrayEndereco[0]['title'] = 'PADRÃO';
	
		$vendedorInternoRead = true;
		$vendedorInternoExtra = "onfocus='this.initialSelect = this.selectedIndex;' onchange='this.selectedIndex = this.initialSelect;'";
		$recarregarVendedorInterno = "<script>document.getElementById('T005_C007_Id_Vendedor_Interno_refreshBtn').remove();</script>";
		if($acaoId > 0){		
			$sqlC007 = mysql_query("SELECT C007_Flag_Ausente FROM T005 LEFT JOIN D024 ON D024_Id=T005_D024_Id LEFT JOIN C007 ON C007_Id=D024_C007_Vendedor_Interno WHERE T005_Id = '{$acaoId}'");
			$rowC007 = mysql_fetch_array($sqlC007);
			if(($rowC007['C007_Flag_Ausente'] == 'S' && $g['C031']['alterarVe ndedorAusente'] == 'S') || ($g['nivelAlcada']==4) || $g['C031']['alterarVendedor'] == 'S'){
				$vendedorInternoRead = false;
				$vendedorInternoExtra = "";
				$recarregarVendedorInterno = "";
			}
		}
		echo $recarregarVendedorInterno;

		$vendedorExternoRead = true;
		$vendedorExternoExtra = "onfocus='this.initialSelect = this.selectedIndex;' onchange='this.selectedIndex = this.initialSelect;'";
		$recarregarVendedorExterno = "<script>document.getElementById('T005_C007_Id_Vendedor_Externo_refreshBtn').remove();</script>";
		if($acaoId > 0){		
			$sqlC007 = mysql_query("SELECT C007_Flag_Ausente FROM T005 LEFT JOIN D024 ON D024_Id=T005_D024_Id LEFT JOIN C007 ON C007_Id=D024_C007_Vendedor_Externo WHERE T005_Id = '{$acaoId}'");
			$rowC007 = mysql_fetch_array($sqlC007);
			if(($rowC007['C007_Flag_Ausente'] == 'S' && $g['C031']['alterarVendedorAusente'] == 'S') || ($g['nivelAlcada']==4) || $g['C031']['alterarVendedor'] == 'S'){
				$vendedorExternoRead = false;
				$vendedorExternoExtra = "";
				$recarregarVendedorExterno = "";
			}
		}
		echo $recarregarVendedorExterno;

		$clientePorEmpresa = '';
		if($g['C031']['separarClientePorEmpresa'] == 'S'){
			$clientePorEmpresa = "AND (IFNULL(D024_C004_Id,0) = '{$g['empresaAtual']}' OR IFNULL(D024_C004_Id,0) = 0)";
		}

		$faixaCalculo    = !empty($g['C031']['padraoFaixaCalculoIcmsCredito']) ? $g['C031']['padraoFaixaCalculoIcmsCredito'] : '';
		//Buscas destinos, foi centralizado nesta classe e vai valer para todos os locais
		$destinoProdutos = $CAD002->retornaDestinoProdutos(false, 'V');
		// novo formulário
		$form = new Formulario('form_ven002_form1');


		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T005_D024_Id' => array('name' => 'T005_D024_Id','label'=>'Cliente', 'type' => 'pesquisa','extra' => "onchange=\"divSetarAcaoId('{$g['divId']}', \$('#T005_D024_Id').val(), 'idCliente');carregaCFOP_Contatos(this, '{$g['divId']}', '', '{$g['C031']['alterarVendedorAusente']}', '{$g['C031']['alterarVendedor']}'); carregaListaPreco(this, '{$g['divId']}');\"", 'pesquisa' => array('D024','D024_Id','D024_Id,D024_Nome_Empresa,D024_Nome_Fantasia,D020_Nome_Cidade,D018_UF,D024_Cpf,D024_Cnpj','left join D020 on D020_Id=D024_D020_Id left join D018 on D018_Id=D024_D018_Id WHERE D024_Flag_Cliente="S" and D024_Flag_Ativo="S" '. $clientePorEmpresa .' order by D024_Nome_Empresa','(%1) %2 (%6%7) [%4 - %5]'), 'length' => array(50,30)),					
					'T005_D013_Id' => array('name' => 'T005_D013_Id', 'label' => 'Contato', 'type' => 'select',
						'select' => gGeraSelect('D013','D013_Id','D013_Nome_Contato',"where D013_D024_Id=$idCliente AND D013_Flag_Ativo = 'S'",true),
						'selectRefresh' => array('D013','D013_Id','D013_Nome_Contato,D013_DDD_Telefone_1,D013_Telefone_1',"where D013_D024_Id=$idCliente AND D013_Flag_Ativo = 'S'",true),
					),
					'ven002FormContatos' => array('name' => 'ven002FormContatos', 'type'=>'button', 'reload' => false, 'value' => 'Incluir', 'reject' => true),
					'T005_D006_Id' => array('name' => 'T005_D006_Id', 'label'=>'CFOP','type' => 'select', 'select' => 
        						gGeraSelect('D006','D006_Id','D006_Codigo_CFOP,D006_Descricao',' WHERE IFNULL(D006_Flag_Inativo,"N")!="S" AND D006_Flag_Departamento="COMERCIAL"  ORDER BY D006_Codigo_CFOP',true),
					),											
					'T005_Data_Emissao' => array('name' => 'T005_Data_Emissao','label'=>'Emissão','type' => 'data', 'length' => array(20,10), 'value'=>date("d/m/Y")),
					'T005_Hora_Emissao' => array('name' => 'T005_Hora_Emissao', 'type' => 'hidden', 'value' => date("H:i:s")),
					'T005_C007_Id_Vendedor_Interno' => array('name' => 'T005_C007_Id_Vendedor_Interno', 'label'=>'Vendedor','type' => 'select', 'extra' => $vendedorInternoExtra, 'readonly' => $vendedorInternoRead, 'select' => 
							gGeraSelect('C007','C007_Id','IF(C007_Ativo != "N"[[virgula]] C007_Primeiro_Nome[[virgula]] CONCAT(C007_Primeiro_Nome[[virgula]]" (INATIVO)")) as C007_Primeiro_Nome','WHERE C007_Flag_Vendedor!="" AND (C007_Ativo != "N" OR C007_Id = "'.$idVendedorInterno.'") ORDER BY C007_Primeiro_Nome',true,$g['usuarioAtual']),
					),
					'T005_C007_Id_Vendedor_Externo' => array('name' => 'T005_C007_Id_Vendedor_Externo', 'label'=>'Externo','type' => 'select', 'extra' => $vendedorExternoExtra, 'readonly' => $vendedorExternoRead, 'select' => 
							gGeraSelect('C007','C007_Id','IF(C007_Ativo != "N"[[virgula]] C007_Primeiro_Nome[[virgula]] CONCAT(C007_Primeiro_Nome[[virgula]]" (INATIVO)")) as C007_Primeiro_Nome','WHERE C007_Flag_Vendedor!="" AND (C007_Ativo != "N" OR C007_Id = "'.$idVendedorExterno.'") ORDER BY C007_Primeiro_Nome',true),
					),
					'T005_D021_Id' => array('name' => 'T005_D021_Id', 'label'=>'Representante','type' => 'select', 'select' => 
								gGeraSelect('D021','D021_Id','D021_Nome_Empresa','',true),
					),
					'T005_D148_Id_Entrega' => array('name' => 'T005_D148_Id_Entrega', 'label' => 'Endereço Entrega', 'type' => 'select', 'selectRefresh' => array('D148','D148_Id','D148_Nome','WHERE D148_D024_Id='.$idCliente.' ORDER BY D148_Id',true),
						'select' => $arrayEndereco
					),
					'T005_D148_Id_Cobranca' => array('name' => 'T005_D148_Id_Cobranca', 'label' => 'Endereço Cobrança', 'type' => 'select', 'selectRefresh' => array('D148','D148_Id','D148_Nome','WHERE D148_D024_Id='.$idCliente.' ORDER BY D148_Id',true),
						'select' => $arrayEndereco
					),	
					'T005A_D148_Id_Redespacho' => array('name' => 'T005A_D148_Id_Redespacho', 'label' => 'Endereço Redespacho', 'type' => 'select', 'selectRefresh' => array('D148','D148_Id','D148_Nome','WHERE D148_D024_Id='.$idCliente.' ORDER BY D148_Id',true),
						'select' => $arrayEndereco
					),
					'T005_Percentual_Comissao' => array('name' => 'T005_Percentual_Comissao', 'label'=>'Comissão %','value'=>'0,00','type' => 'moeda', 'length' => array(20,10)),
					'T005_Percentual_Comissao_Representante' => array('name' => 'T005_Percentual_Comissao_Representante', 'label'=>'Comissão Rep. %','value'=>'0,00','type' => 'moeda', 'length' => array(20,10)),
					'T005_Flag_ACP' => array('name' => 'T005_Flag_ACP', 'label'=>'Tipo Documento','type' => 'select','select' => 
						array(
							array('title' => '...', 'value' => 0),
							array('title' => 'NF', 'value' => 1),
							array('title' => 'Romaneio', 'value' => 2),
							array('title' => 'ACP', 'value' => 3),
							array('title' => 'Cupom Fiscal', 'value' => 4),
							array('title' => 'NF D1', 'value' => 5),
							array('title' => 'NFe', 'value' => 6, 'selected' => true),
							array('title' => 'NFSe', 'value' => 7),							
							array('title' => 'NFCe', 'value' => 8),
						)
					),
					'T005_Prazo_Entrega' => array('name' => 'T005_Prazo_Entrega', 'label'=>'Prazo de Entrega','type' => 'text', 'length' => array(53,30)),
					'T005_Data_Entrega' => array('name' => 'T005_Data_Entrega', 'label'=>'Entrega','type' => 'data'),
					'T005_Flag_Primeira_Liberacao_Credito' => array('name' => 'T005_Flag_Primeira_Liberacao_Credito', 'label'=>'T005_Flag_Primeira_Liberacao_Credito','type' => 'hidden', 'value' => '2'),
					'T005_D116_Id' => array('name' => 'T005_D116_Id', 'label' => 'Veículo','type' => 'select',
						'select'=> gGeraSelect('D116', 'D116_Id', 'D116_Placa,D133_Modelo,D134_Cor', "LEFT JOIN D133 on D133_Id=D116_D133_Id LEFT JOIN D134 on D134_Id=D116_D134_Id WHERE D116_D024_Id=$idCliente", true),
						'selectRefresh' => array('D116', 'D116_Id', 'D116_Placa,D133_Modelo,D134_Cor', "LEFT JOIN D133 on D133_Id=D116_D133_Id LEFT JOIN D134 on D134_Id=D116_D134_Id WHERE D116_D024_Id=$idCliente", true)
					),
					'T005_Flag_Lista_Preco' => 	array('name' => 'T005_Flag_Lista_Preco', 'label' => 'Lista Preço', 'type' => 'select', 'select' => 
						array(
							array('title' => 'Preço 1', 'value' => '1'),   
							array('title' => 'Preço 2', 'value' => '2'),   
							array('title' => 'Preço 3', 'value' => '3'),
							array('title' => 'Preço 4', 'value' => '4'),
							array('title' => 'Preço 5', 'value' => '5'),
						)
					),
					'ven002FormVeiculos' => array('name' => 'ven002FormVeiculos', 'type'=>'button', 'reload' => false, 'value' => 'Incluir', 'reject' => true),
					'tituloOC' => array('name' => 'tituloOC', 'label'=>'Ordem de Compra','type' => 'titulo', 'reject' => true),
					'T005_Ordem_Compra' => array('name' => 'T005_Ordem_Compra', 'label'=>'Ordem de Compra','type' => 'text', 'length' => array(53,30)),
					'tituloObservacoes' => array('name' => 'tituloObservacoes', 'label'=>'Observações','type' => 'titulo', 'reject' => true),
					'T005_Observacao' => array('name' => 'T005_Observacao', 'label'=>'Cliente','type' => 'textarea','extra' => 'title="Esta observação será visualizada pelo cliente (Não será impressa na DANFE)"', 'length' => array(3,50)),
					'T005_Observacao_Comercial' => array('name' => 'T005_Observacao_Comercial', 'label'=>'Comercial<br> Interna','type' => 'textarea','extra' => 'title="Observação de uso interno da área comercial (Não será impressa na DANFE)"', 'length' => array(3,50)),
					'T005_Observacao_Nota_Fiscal' => array('name' => 'T005_Observacao_Nota_Fiscal', 'label'=>'Nota Fiscal','type' => 'textarea','extra' => 'title="Esta observação será impressa na DANFE"', 'length' => array(3,50)),
					'T005_Observacao_1' => array('name' => 'T005_Observacao_1', 'label'=>'Almox.:','type' => 'textarea','extra' => 'title="Observação de uso interno da área de estoque (Não será impressa na DANFE)"', 'length' => array(3,50)),
					'tituloTransporte' => array('name' => 'tituloTransporte', 'label'=>'Transporte','type' => 'titulo', 'reject' => true),
					'T005_Flag_Frete' => array('name' => 'T005_Flag_Frete','label'=>'Frete', 'type' => 'select',
						'select' => array(
							array('title' => '0 - Contratação por conta do Remetente', 'value' => '0'),
							array('title' => '1 - Contratação por conta do Destinatário', 'value' => '1'),
							array('title' => '2 - Contratação por conta de Terceiros', 'value' => '2'),
							array('title' => '3 - Próprio por conta do Remetente', 'value' => '3'),
							array('title' => '4 - Próprio por conta do Destinatário', 'value' => '4'),							
							array('title' => '9 - Sem frete', 'value' => '9'),							
						),
					),
					'T005_Valor_Frete' => array('name' => 'T005_Valor_Frete', 'label'=>'Valor Frete','value'=>'0,00','type' => 'moeda', 'length' => array(20,20)),
					'T005_D022_Id' => array('name' => 'T005_D022_Id', 'label' => 'Transportadora', 'type' => 'pesquisa', 'pesquisa' => array('D022','D022_Id','D022_Nome_Empresa,D020_Nome_Cidade,D018_UF',"left join D020 on D020_Id=D022_D020_Id left join D018 on D018_Id=D022_D018_Id"),'extra' => "onchange=\"carregaTipoTransp(this, '{$g['divId']}')\"", 'length' => array(50,90)),
					'T005_T208_Id' => array('name' => 'T005_T208_Id', 'label' => 'Rota', 'type' => 'pesquisa', 'pesquisa' => array('T208','T208_Id','T208_Rota')),
					'T005_D036_Id' => array('name' => 'T005_D036_Id', 'label' => 'Tipo', 'type' => 'select', 'select' =>
							gGeraSelect('D036','D036_Id','D036_Tipo_Transporte','',true),
					),
					'TransportadoraPadrao' => array('name' => 'TransportadoraPadrao', 'label' => 'Transportadora Padrão', 'type' => 'select','reject' =>true ,'disabled' => true,
						'select' => gGeraSelect('D022','D022_Id','D022_Nome_Empresa,D020_Nome_Cidade,D018_UF',"left join D020 on D020_Id=D022_D020_Id left join D018 on D018_Id=D022_D018_Id",false), 
					),
					'T005_T134_Id' => array('name' => 'T005_T134_Id','label'=>'Faixa para cálculo do Crédito<br /> de ICMS Simples Nacional','uppercase'=>true,'type' => 'select', 'select' =>
						gGeraSelect('T134', 'T134_Id','CONCAT(T134_Tipo[[virgula]]": DE "[[virgula]]convert(format(round(T134_Faixa_Inicial),2,"de_DE")using utf8)[[virgula]]" A "[[virgula]]convert(format(round(T134_Faixa_Final),2,"de_DE")using utf8)[[virgula]]" = ALIQ: "[[virgula]]T134_ICMS[[virgula]]"% REDUÇÃO: "[[virgula]]if(T134_Reducao_Base_ICMS,T134_Reducao_Base_ICMS,0)[[virgula]]"%") as faixaCalculo','',true,$faixaCalculo)
					),
					'button' => array('name' => 'button', 'type' => 'button','value' =>'Usar Padrão','reload' => false,'extra' => "onclick='copiaTransportadora({$idTipoTransp})'",'reject'=>true),
					'nada' => array('name' => 'nada', 'label' => 'Transportadora', 'type' => 'hidden', 'reject'=>true, 'length' => array(30,30)),
					'T005_Flag_Revenda_Consumidor' => array('name' => 'T005_Flag_Revenda_Consumidor', 'label'=>'T005_Flag_Revenda_Consumidor','type' => 'hidden'
						//'select' => 
							//array(
								//array('title' => '...', 'value' => 0, 'selected' => true),
								//array('title' => 'Revenda', 'value' => 1),
								//array('title' => 'Consumidor atacado', 'value' => 2),
								//array('title' => 'Consumidor varejo', 'value' => 3),
							//)
					),
					'T005_Quantidade_Volumes' => array('name' => 'T005_Quantidade_Volumes', 'label'=>'Volumes','type' => 'text'),
					'T005_C004_Id' => array('name' => 'T005_C004_Id', 'label'=>'Transporte','type' => 'hidden', 'value' => $g['empresaAtual']),
					'T005_Flag_Totalizar_Peso' => array('name' => 'T005_Flag_Totalizar_Peso', 'label'=>'Totalizar Peso','type' => 'select','select' => 
						array(
							array('title' => 'Não', 'value' => 'N', 'selected' => ($g['C031']['totalizarPesoVenda'] == "N") ? true : false),
							array('title' => 'Sim', 'value' => 'S', 'selected' => ($g['C031']['totalizarPesoVenda'] == "S") ? true : false),
						)
					),
					'T005_Flag_Destino_Produto' => array('name' => 'T005_Flag_Destino_Produto','label'=>'Destino', 'type' => 'select', 
						'select' => $destinoProdutos
					),
					'T005_Casas_Decimais_Preco' => array('name' => 'T005_Casas_Decimais_Preco', 'type' => 'hidden', 'value' => $g['c004']['C004_Casas_Decimais_Preco']),
					'T005A_Flag_Conversao_Unidade' => array('name' => 'T005A_Flag_Conversao_Unidade', 'label'=>'T005A_Flag_Conversao_Unidade', 'type' => 'hidden' ,'value' => $g['C031']['utilizaUnidadeConversaoVenda']),
					'T005A_Flag_Ratear_Frete_Custo' => array('name' => 'T005A_Flag_Ratear_Frete_Custo', 'label'=>'T005A_Flag_Ratear_Frete_Custo','type' => 'hidden' , 'value' => $g['C031']['ratearFreteVendaNoCusto']),
					'btnPedidoIncluirCliente' => array('name' => 'btnPedidoIncluirCliente', 'type'=>'button', 'reload' => false, 'value' => 'Incluir', 'reject' => true),
				)
			)
		);

		if($g['C031']['modeloExpedicao'] == '2'){
			$formArray['campos']['geral']['T005_Flag_Expedicao'] = array('name' => 'T005_Flag_Expedicao', 'label' => 'Expedição', 'type' => 'select', 'select' => 
				array(
					array('title' => '...', 'value' => ''),
					array('title' => 'Comercial', 'value' => '1'),
					array('title' => 'Balcão', 'value' => '2'),
				)
			);
		}
		
		/** Botão Limpar / Novo Registro
		if (!empty($acaoId)) {
			$formArray['campos']['geral']['ven002FormCabecalhoBtnNew'] = array('name' => 'ven002FormCabecalhoBtnNew', 'type'=>'submit', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right; margin-top:20px;', 'reject' => true);
		}
		*/
		//$formArray['campos']['geral']['ven002FormCabecalhoBtnCln'] = array('name' => 'ven002FormCabecalhoBtnCln', 'type'=>'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right; margin-top:20px;', 'reject' => true);
		//$formArray['campos']['geral']['ven002FormCabecalhoBtnAdd'] = array('name' => 'ven002FormCabecalhoBtnAdd', 'type'=>'button', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right; margin-top:20px;', 'reject' => true);
		$form->definirFormularioArray($formArray);
		
		$sql = "SELECT T005_D024_Id FROM T005 WHERE T005_Id = '{$r_T005_Id}';";
		$resultado = mysql_query($sql);
		$row = mysql_fetch_array($resultado);
		echo <<<EOT
		    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		    	$('#ven002FormContatos').unbind('click');
				$('#ven002FormContatos').unbind('click').bind('click', function(data) {
					var id = $('#{$g['divId']} #T005_D024_Id').val();
					if (id>0){
						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Cliente/Fornecedor - ID: ' + id, '/cad/cad001/content/cad001contentCadastro/', '&acaoId=' + encodeURIComponent(id) + '&tabela=D024' + '&tab=contatos', [950,553]);
					} else {
						dialogConfirm('Erro', 'Selecione o Cliente!');
					}
					return false;
				});
				$('#btnPedidoIncluirCliente').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Incluir Novo Cliente', '/cad/cad001/content/cad001PedidoIncluirCliente/', '', [590,420]);
				});
				$('#ven002FormVeiculos').unbind('click');
				$('#ven002FormVeiculos').unbind('click').bind('click', function(data) {
					var id = '{$row['T005_D024_Id']}';
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Incluir Veículo', '/cad/cad001/content/cad001ContentVeiculosIncluir/', '&naoRefreshParent=true&D024_Id=' + encodeURIComponent(id) + '&tabela=D024' + '&tab=veiculos', [300,350]);
					return false;
				});
		    } </script></div>
EOT;

		/**
		* Na maioria dos casos não será necessário alterar nada daqui para baixo
		* Somente em casos especiais, para alterar pequenos detalhes ou modificações do visual
		*/


		/**
		* PARTE 3: Processamento do formulário (se foi enviado)
		* E carregamento dos valores (se não foi enviado e possui acaoId)
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
					$T005 = mysql_query("SELECT T005_Flag_Lista_Preco, T005_D024_Id, T005_D006_Id FROM T005 WHERE T005_Id = '{$acaoId}'");
 					$rT005 = mysql_fetch_array($T005);
					if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {						
						echo gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!');
						$lista = $form->campoValorEnviado('T005_Flag_Lista_Preco');
						//Verifica se a lista de preço foi alterada
						if($rT005['T005_Flag_Lista_Preco'] != $lista){							
							$VEN002->alteraLista($acaoId, $lista);
							echo gGeraAlertMsg('Atenção', 'A lista de preço foi alterada, os valores de tabela dos produtos serão modificados.');	
						}					
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								divRefresh('{$r_divIdMain}', true);
							} </script></div>
EOT;
					}
				} else {
					// Insert
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {					
						//$T005_Id = $g['mysqlLastId'];

	 					$T005A_Id  = $g['mysqlLastId'];
						$T005A   = mysql_query("select T005A_T005_Id from T005A where T005A_Id='{$T005A_Id}'");
						$mT005A  = mysql_fetch_array($T005A);
						$T005_Id = $mT005A['T005A_T005_Id']; 

						$VEN002->acoesAposInserirPedido($T005_Id);

						$T005 = mysql_query("SELECT T005_Flag_Lista_Preco, T005_D024_Id, T005_D006_Id, T005_D022_Id FROM T005 WHERE T005_Id = '{$T005_Id}'");
 						$rT005 = mysql_fetch_array($T005);
						//Altera a empresa a faturar caso possua no cadastro do cliente
						$D024 = mysql_query("SELECT D024_Empresa_Emissora,
													D024_Nome_Empresa,
													D024_Pgto_Primeiro_Vcto_Tipo,
													D024_Prazo_Parcela,
													D024_Pgto_Dias_Vcto_Tipo,
													D024_Dias_Vencimento,
													D024_D027_Id,
													D024_Cobrar_Frete_Primeira_Parcela,
													D024_Cobrar_ST_Primeira_Parcela,
													D024_Cobrar_IPI_Primeira_Parcela,
													D024_Quantidade_Parcelas
											   FROM D024 
											  WHERE D024_Id = '{$rT005['T005_D024_Id']}'");
						$resD024 = mysql_fetch_array($D024);

						if(isset($resD024['D024_Empresa_Emissora'])) {
							mysqli_query("UPDATE T005 SET T005_D024_Id_Nfe = '{$resD024['D024_Empresa_Emissora']}' WHERE T005_Id = '{$T005_Id}'");
						}

						//VALIDAÇÕES PAGAMENTO
						$update = "";

						//Primeiro vencimento tipo (DIAS/DATA)
						if(!empty($resD024['D024_Pgto_Primeiro_Vcto_Tipo'])){
							$primeiroVencTipo = $resD024['D024_Pgto_Primeiro_Vcto_Tipo'];
						}else{
							$primeiroVencTipo = $g['C031']['primeiroVencimentoTipo'];
						}

						if(!empty($primeiroVencTipo)){
                            $update.=" T005_Pgto_Primeiro_Vcto_Tipo='{$primeiroVencTipo}' ";
                        }

                        //Primeiro vencimento número
                        if($resD024['D024_Prazo_Parcela']>0){
							$primeiroVencNum = $resD024['D024_Prazo_Parcela'];
						}else{
							$primeiroVencNum = $g['C031']['primeiroVencimentoNum'];
						}

						if(!empty($primeiroVencNum)){
                            if (!empty($update)){
                                $update .= ',';
                            }
                            $update.=" T005_Pgto_Primeiro_Vcto_Valor='{$primeiroVencNum}'"; 
                        }

                        //Dias venc tipo (DIA FIXO/INTERVALO)
                        if(!empty($resD024['D024_Pgto_Dias_Vcto_Tipo'])){
							$tipoIntervalodias = $resD024['D024_Pgto_Dias_Vcto_Tipo'];
						}else{
							$tipoIntervalodias = $g['C031']['tipoIntervalodias'];
						}

						if(!empty($tipoIntervalodias)){
                            if (!empty($update)){
                                $update .= ',';
                            }
                            $update.=" T005_Pgto_Dias_Vcto_Tipo='{$tipoIntervalodias}'"; 
                        }

                        //Dias venc num (DIA FIXO/INTERVALO)
                        if($resD024['D024_Dias_Vencimento']>0){
							$IntervalorDias = $resD024['D024_Dias_Vencimento'];
						}else{
							$IntervalorDias = $g['C031']['IntervalorDias'];
						}

						if(!empty($IntervalorDias)){
                            if (!empty($update)){
                                $update .= ',';
                            }
                            $update.=" T005_Pgto_Dias_Vcto_Valor='{$IntervalorDias}'"; 
                        }

                        //Portador
                        if(!empty($resD024['D024_D027_Id'])){
							$D027portadorEmpresa = $resD024['D024_D027_Id'];
						}else{
							$D027portadorEmpresa = $g['C031']['D027portadorEmpresa'];
						}

						if(!empty($D027portadorEmpresa)){
                            if (!empty($update)){
                                $update .= ',';
                            }
                            $update.=" T005_D027_Id='{$D027portadorEmpresa}'"; 
                        }

                        //Frete primeira parcela
                        if(!empty($resD024['D024_Cobrar_Frete_Primeira_Parcela'])){
							$usarFretePrimeiraParcela = $resD024['D024_Cobrar_Frete_Primeira_Parcela'];
						}else{
							$usarFretePrimeiraParcela = $g['C031']['usarFretePrimeiraParcela'];
						}

						if(!empty($usarFretePrimeiraParcela)){
                            if (!empty($update)){
                                $update .= ',';
                            }
                            $update.=" T005_Flag_Frete_Primeira_Parcela='{$usarFretePrimeiraParcela}'"; 
                        }

                        //ST primeira parcela
                        if(!empty($resD024['D024_Cobrar_ST_Primeira_Parcela'])){
							$usarSTPrimeiraParcela = $resD024['D024_Cobrar_ST_Primeira_Parcela'];
						}else{
							$usarSTPrimeiraParcela = $g['C031']['usarSTPrimeiraParcela'];
						}

						if(!empty($usarSTPrimeiraParcela)){
                            if (!empty($update)){
                                $update .= ',';
                            }
                            $update.=" T005_Flag_ST_Primeira_Parcela='{$usarSTPrimeiraParcela}'"; 
                        }

                        //IPI primeira parcela
                        if(!empty($resD024['D024_Cobrar_IPI_Primeira_Parcela'])){
							$usarIPIPrimeiraParcela = $resD024['D024_Cobrar_IPI_Primeira_Parcela'];
						}else{
							$usarIPIPrimeiraParcela = $g['C031']['usarIPIPrimeiraParcela'];
						}

						if(!empty($usarIPIPrimeiraParcela)){
                            if (!empty($update)){
                                $update .= ',';
                            }
                            $update.=" T005_Flag_IPI_Primeira_Parcela='{$usarIPIPrimeiraParcela}'"; 
                        }

                        //Parcelas padrão (vem selecionado no formulario de pagamento)
                        if($resD024['D024_Quantidade_Parcelas']>0){
							$numeroParcelasPadrao = $resD024['D024_Quantidade_Parcelas'];
						}else{
							$numeroParcelasPadrao = $g['C031']['numeroParcelasPadrao'];
						}

						if(!empty($numeroParcelasPadrao)){
                            if (!empty($update)){
                                $update .= ',';
                            }
                            $update.=" T005_Pgto_Parcelas='{$numeroParcelasPadrao}'"; 
                        }


						if(!empty($update)){
                            mysql_query("UPDATE T005 SET {$update} WHERE T005_Id='{$T005_Id}'"); 
                        }

						// Imprime uma mensagem e fecha a janela
						$alert = gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!');
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								divRefresh('{$g['divIdParent']}');
								abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' ,unique(), '', 'Editar Pedido - ID: ' + {$T005_Id}, '/ven/ven002/content/ven002content02/', '&acaoId=' + encodeURIComponent('{$T005_Id}') + '&tabela=T005', [1000,615]);
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
		$botoesDir .= '<button type="submit" id="ven002FormCabecalhoBtnAdd">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
    break;

	case 'ven002FormCotar':
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
		$form_tabela = 'T189';
		$form_campo_pk = 'T189_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'ven002FormCotar_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/ven/ven002/form/ven002FormCotar/';

		//form editor
		$form_editor = array(
			'geral' =>'{"T189_Observacao":{"top":7,"left":5,"height":120,"width":217,"hidden":0},"T189_T006_Id":{"top":122,"left":0,"width":127,"hidden":1},"T189_D009_Id":{"top":145,"left":0,"width":127,"hidden":1}}'
		);
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
     				'T189_Observacao' => array('name' => 'T189_Observacao', 'label' => 'Observação', 'type' => 'textarea', 'length' => array(8, 40)), 
					// Campos Hidden
					'T189_T006_Id' => array('name' => 'T189_T006_Id', 'label' => '', 'type' => 'hidden', 'value' => $r_T006_Id),
					'T189_D009_Id' => array('name' => 'T189_D009_Id', 'label' => '', 'type' => 'hidden', 'value' => $r_D009_Id),
				)
			)
		);
        $form = new Formulario('ven002FormCotar');
		$form->definirFormularioArray($formArray);
		/**
		 * PARTE 3: Processamento do formulário (se foi enviado)
		 * E carregamento dos valores (se não foi enviado e possui acaoId)
		 */

		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
			} else if ($formCodigo == 2) {
				$VEN002->cotarProduto($form);
				echo <<<EOT
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

	case 'ven002FormComprar':
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
		$form_tabela = 'T189';
		$form_campo_pk = 'T189_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'ven002FormComprar_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/ven/ven002/form/ven002FormComprar/';

		//form editor
		$form_editor = array(
			'geral' =>'{"T189_Observacao":{"top":7,"left":5,"height":120,"width":217,"hidden":0},"T189_T006_Id":{"top":122,"left":0,"width":127,"hidden":1},"T189_D009_Id":{"top":145,"left":0,"width":127,"hidden":1}}'
		);
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
     				'T189_Observacao' => array('name' => 'T189_Observacao', 'label' => 'Observação', 'type' => 'textarea', 'length' => array(8, 40)), 
					// Campos Hidden
					'T189_T006_Id' => array('name' => 'T189_T006_Id', 'label' => '', 'type' => 'hidden', 'value' => $r_T006_Id),
					'T189_D009_Id' => array('name' => 'T189_D009_Id', 'label' => '', 'type' => 'hidden', 'value' => $r_D009_Id),
				)
			)
		);
        $form = new Formulario('ven002FormCotar');
		$form->definirFormularioArray($formArray);
		/**
		 * PARTE 3: Processamento do formulário (se foi enviado)
		 * E carregamento dos valores (se não foi enviado e possui acaoId)
		 */

		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
			} else if ($formCodigo == 2) {
				$VEN002->comprarProduto($form);
				echo <<<EOT
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

	case 'ven002FormMudarPedidoEmpresa':
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;
		$duplicarEmpresa = isset($r_duplicarEmpresa) && $r_duplicarEmpresa == 'S';
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
		$form_tabela = 'T005';
		$form_campo_pk = 'T005_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'ven002FormMudarPedidoEmpresa_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/ven/ven002/form/ven002FormMudarPedidoEmpresa/';

		//form editor
		$form_editor = array(
			'geral' => '{"T005_C004_Id":{"top":7,"left":15.4375,"width":399,"hidden":0}}'
		);
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array()
			)
		);

		$formArray['campos']['geral']['T005_C004_Id'] = array(
						'name' => 'T005_C004_Id', 'label' => $duplicarEmpresa ? 'Empresa destino' : 'Empresa', 'type' => 'select', 'select' =>
						gGeraSelect('C004', 'C004_Id', 'C004_Nome_Empresa', "where C004_Id!='{$g['empresaAtual']}' and C004_Flag_Ativo='S'")
					);
		$form = new Formulario('ven002FormMudarPedidoEmpresa');
		$form->definirFormularioArray($formArray);
		/**
		 * PARTE 3: Processamento do formulário (se foi enviado)
		 * E carregamento dos valores (se não foi enviado e possui acaoId)
		 */

		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
			} else if ($formCodigo == 2) {
				$C004_Id_Novo = $form->campoValorEnviado('T005_C004_Id');
				if ($duplicarEmpresa) {
					echo gProcessaEtapas('Duplicar e mudar empresa','VEN002', "VEN002VerificarDuplicarPedidosSelecionados();VEN002DuplicarPedidosParaEmpresa($C004_Id_Novo)", "$r_sqlSelecao", false, "$r_divIdParent");
				} else {
					echo gProcessaEtapas('Mudar empresa','VEN002', "VEN002VerificaMudarEmpresaPedido();VEN002MudarEmpresaPedido($C004_Id_Novo)", "$r_sqlSelecao", false, "$r_divIdParent");
				}
				gFecharJanela("{$g['divId']}");
			} else {
				echo gGeraAlertMsg('Erro',"{$retorno}");
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
		$botoesDir .= '<button type="submit" id="ven002FormMudarPedidoEmpresa_btn">'.($duplicarEmpresa ? 'Duplicar e mudar empresa' : 'Mudar').'</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'ven002FormAtualizarCFOPPedidos':
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
		$form_tabela = 'T005';
		$form_campo_pk = 'T005_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'ven002FormAtualizarCFOPPedidos_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/ven/ven002/form/ven002FormAtualizarCFOPPedidos/';
        
        //form editor
        $form_editor = array(
            'geral' => '{"linha3":{"top":7,"left":14,"width":null,"hidden":0},"linha":{"top":50,"left":6,"width":null,"hidden":1},"D006_Id":{"top":76,"left":11.328125,"width":352,"hidden":0},"ven002FormAtualizarCFOPPedidos_btn":{"top":74,"left":399.984375,"width":null,"hidden":0},"linha2":{"top":119,"left":15,"width":null,"hidden":1},"ven002FormAtualizarCFOPRegras":{"top":143,"left":16,"width":null,"hidden":0}}',
        );
		
		$form = new Formulario('ven002FormAtualizarCFOPPedidos');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D006_Id' => array('name' => 'D006_Id', 'label' => 'CFOP', 'type' => 'select', 'reject' => true,
						'select' => gGeraSelect('D006', 'D006_Id', 'D006_Codigo_CFOP,D006_Descricao', 'WHERE IFNULL(D006_Flag_Inativo,"N")!="S" AND D006_Flag_Departamento="COMERCIAL" ORDER BY D006_Codigo_CFOP DESC', true)
					),
				),
			)
		);

		$formArray['campos']['geral']['ven002FormAtualizarCFOPPedidos_btn'] = array('name' => 'ven002FormAtualizarCFOPPedidos_btn', 'type'=>'submit', 'reload' => false, 'value' => 'Atualizar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['geral']['linha'] = array('name' => 'linha', 'type'=>'titulo', 'value' => '', 'label' => 'Atualizar os itens do orçamento com a CFOP:', 'reject' => true);
		$formArray['campos']['geral']['linha2'] = array('name' => 'linha2', 'type'=>'titulo', 'value' => '', 'label' => 'Atualizar as CFOPs de acordo com as regras:', 'reject' => true);
		$formArray['campos']['geral']['linha3'] = array('name' => 'linha3', 'type'=>'comentario', 'value' => 'Atenção: Ao escolher uma das opções, a CFOP e as <br />aliquotas dos itens deste orçamento serão alterados.', 'label' => '', 'reject' => true, 'style' => 'color: #FF0000; font-size: 12px;');
		$formArray['campos']['geral']['ven002FormAtualizarCFOPRegras'] = array('name' => 'ven002FormAtualizarCFOPRegras', 'type'=>'button', 'reload' => false, 'value' => 'Aplicar regras CFOP', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$form->definirFormularioArray($formArray);
		
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
			$('#ven002FormAtualizarCFOPRegras').unbind('click').bind('click', function(data) {
				$('#{$g['divId']}').showLoading();
				$.getJSON('/ven/ven002/form_func-ajax/atualizarCFOPPedidos/?ajax=true&extra=' + encodeURIComponent('{$r_extra}') + '&callback=?', function(request) {
					if (request.code) {
						idProgressBar = dialogAlert('Atualizando CFOP e alíquotas',"<span id='progressBar'>Processando 0%</span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' />");
						            ven002AtualizarAliquotasPedidosCFOPGeralLoop(idProgressBar,1); 

						function ven002AtualizarAliquotasPedidosCFOPGeralLoop(idProgressBar, inicio){
			                $.getJSON('/ven/ven002/form_func-ajax/atualizarAliquotaPedidos/?ajax=true&T005_Id=' + encodeURIComponent("") + '&extra=' + encodeURIComponent("{$r_extra}") + '&inicio=' + inicio + '&callback=?', function(request) {
			                    if (request.concluido == 0) {
			                    	$('#progressBar').html('<b>Processado '+ request.percentual + '%</b>' + '<br/><br/>Pedidos atualizados: <b>' + request.pedidos + '</b><br/>Produtos atualizados: <b>' + request.ok + '</b><br/>Produtos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
			                    	ven002AtualizarAliquotasPedidosCFOPGeralLoop(idProgressBar,0);
			                    }else{
			                    	if (request.code) {
			                    		$('#dc-' + idProgressBar).dialog("destroy").remove();
			                    		dialogConfirm('CFOP e Alíquotas atualizadas','<b>Processado '+ request.percentual + '%</b>' + '<br/><br/>Pedidos atualizados: <b>' + request.pedidos + '</b><br/>Produtos atualizados: <b>' + request.ok + '</b><br/>Produtos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
										$('#{$g['divId']}').closest('.ui-dialog-content').dialog('close');
			                            divRefresh('{$g['divIdParent']}', true);
			                			$(this).dialog("destroy").remove();     
			                        } else {
										$('#dc-' + idProgressBar).dialog("destroy").remove();
			                        	$('#{$g['divId']}').closest('.ui-dialog-content').dialog('close');
			                        	$(this).dialog("destroy").remove(); 
			                            divRefresh('{$g['divIdParent']}', true);
			                            dialogConfirm('Erro!', request.data);
			                        }
			                    } 
			                });	                  
				        }
					}else{
						$('#{$g['divId']}').hideLoading();
						var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
						dialogConfirm('Erro', request.data, \$buttons);
					} 
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
				$retorno = $VEN002->ven002atualizarCFOPManualPedidos($r_extra, $D006_Id);
				if ($retorno[0] !== true) {
					echo gGeraAlertMsg('Erro ao atualizar CFOPs', $retorno[1]);
					echo<<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						divRefresh('{$g['divId']}', true);
					} </script></div>
EOT;
				} else {
					echo <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						idProgressBar = dialogAlert('Atualizando CFOP e alíquotas',"<span id='progressBar'>Processando 0%</span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' />");
				            ven002AtualizarAliquotasPedidosCFOPManualLoop(idProgressBar,1); 

						function ven002AtualizarAliquotasPedidosCFOPManualLoop(idProgressBar, inicio){
			                $.getJSON('/ven/ven002/form_func-ajax/atualizarAliquotaPedidos/?ajax=true&T005_Id=' + encodeURIComponent("") + '&extra=' + encodeURIComponent("{$r_extra}") + '&inicio=' + inicio + '&callback=?', function(request) {
			                    if (request.concluido == 0) {
			                    	$('#progressBar').html('<b>Processado '+ request.percentual + '%</b>' + '<br/><br/>Pedidos atualizados: <b>' + request.pedidos + '</b><br/>Produtos atualizados: <b>' + request.ok + '</b><br/>Produtos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
			                    	ven002AtualizarAliquotasPedidosCFOPManualLoop(idProgressBar,0);
			                    }else{
			                    	if (request.code) {
			                    		$('#dc-' + idProgressBar).dialog("destroy").remove();
			                    		dialogConfirm('CFOP e Alíquotas atualizadas','<b>Processado '+ request.percentual + '%</b>' + '<br/><br/>Pedidos atualizados: <b>' + request.pedidos + '</b><br/>Produtos atualizados: <b>' + request.ok + '</b><br/>Produtos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
										$('#{$g['divId']}').closest('.ui-dialog-content').dialog('close');
			                            divRefresh('{$g['divIdParent']}', true);
			                			$(this).dialog("destroy").remove();     
			                        } else {
			                        	$('#dc-' + idProgressBar).dialog("destroy").remove();
			                        	$('#{$g['divId']}').closest('.ui-dialog-content').dialog('close');
			                        	$(this).dialog("destroy").remove(); 
			                            divRefresh('{$g['divIdParent']}', true);
			                            dialogConfirm('Erro!', request.data);
			                        }
			                    } 
			                });	                  
				        }
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

	case 'ven002FormAtualizarCFOP':
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
		$form_tabela = 'T005';
		$form_campo_pk = 'T005_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'ven002FormAtualizarCFOP_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/ven/ven002/form/ven002FormAtualizarCFOP/';
        
        //form editor
        $form_editor = array(
            'geral' => '{"linha3":{"top":7,"left":14,"width":null,"hidden":0},"linha":{"top":50,"left":6,"width":null,"hidden":1},"ven002FormAtualizarCFOP_btn":{"top":74,"left":399.99993896484375,"width":null,"hidden":0},"D006_Id":{"top":76,"left":15,"width":352,"hidden":0},"linha2":{"top":119,"left":15,"width":null,"hidden":1},"ven002FormAtualizarCFOPRegras":{"top":143,"left":16,"width":null,"hidden":0}}',
        );

		$descricaoCFOP  = ($g['C031']['descricaoCFOPExibida'] == 'P') ? 'D006_Descricao' : 'IF(D006_Descricao_Comercial !="",D006_Descricao_Comercial,D006_Descricao) as D006_Descricao';

		
		$form = new Formulario('ven002FormAtualizarCFOP');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D006_Id' => array('name' => 'D006_Id', 'label' => 'CFOP', 'type' => 'select', 'reject' => true,
						'select' => gGeraSelect('D006', 'D006_Id', "LPAD(D006_Id[[VIRGULA]]4[[VIRGULA]]0), D006_Codigo_CFOP, {$descricaoCFOP}", 'WHERE IFNULL(D006_Flag_Inativo,"N")!="S" AND D006_Flag_Departamento="COMERCIAL" ORDER BY D006_Codigo_CFOP DESC', true, false, '%1 - %2 - %3')
					),
				),
			)
		);

		$formArray['campos']['geral']['ven002FormAtualizarCFOP_btn'] = array('name' => 'ven002FormAtualizarCFOP_btn', 'type'=>'submit', 'reload' => false, 'value' => 'Atualizar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['geral']['linha'] = array('name' => 'linha', 'type'=>'titulo', 'value' => '', 'label' => 'Atualizar os itens do orçamento com a CFOP:', 'reject' => true);
		$formArray['campos']['geral']['linha2'] = array('name' => 'linha2', 'type'=>'titulo', 'value' => '', 'label' => 'Atualizar as CFOPs de acordo com as regras:', 'reject' => true);
		$formArray['campos']['geral']['linha3'] = array('name' => 'linha3', 'type'=>'comentario', 'value' => 'Atenção: Ao escolher uma das opções, a CFOP e as <br />aliquotas dos itens deste orçamento serão alterados.', 'label' => '', 'reject' => true, 'style' => 'color: #FF0000; font-size: 12px;');
		$formArray['campos']['geral']['ven002FormAtualizarCFOPRegras'] = array('name' => 'ven002FormAtualizarCFOPRegras', 'type'=>'button', 'reload' => false, 'value' => 'Aplicar regras CFOP', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$form->definirFormularioArray($formArray);
		
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
			$('#ven002FormAtualizarCFOPRegras').unbind('click').bind('click', function(data) {
				$('#{$g['divId']}').showLoading();
				$.getJSON('/ven/ven002/form_func-ajax/atualizarCFOP/?ajax=true&T005_Id=' + encodeURIComponent("{$r_T005_Id}") + '&callback=?', function(request) {
					if (request.code) {
						T005_Id = $r_T005_Id;
						idProgressBar = dialogAlert('Atualizando CFOP e alíquotas',"<span id='progressBar'>Processando 0%</span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' />");
						            ven002AtualizarAliquotasPedidosCFOPManualLoop(idProgressBar,T005_Id,1); 

			            function ven002AtualizarAliquotasPedidosCFOPManualLoop(idProgressBar,T005_Id, inicio){
			                $.getJSON('/ven/ven002/form_func-ajax/atualizarAliquota/?ajax=true&T005_Id=' + encodeURIComponent("{$r_T005_Id}") + '&extra=' + encodeURIComponent("{$r_extra}") + '&inicio=' + inicio + '&callback=?', function(request) {
			                    if (request.concluido == 0) {
			                    	$('#progressBar').html('<b>Processado '+ request.percentual + '%</b>' + '</br><br/>Produtos atualizados: <b>' + request.ok + '</b><br/>Produtos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
			                    	ven002AtualizarAliquotasPedidosCFOPManualLoop(idProgressBar,T005_Id,0);
			                    }else{
			                    	$('#dc-' + idProgressBar).dialog("destroy").remove();
			                    	dialogConfirm('CFOP e Alíquotas atualizadas','<b>Processado '+ request.percentual + '%</b>' + '</b><br/>Produtos atualizados: <b>' + request.ok + '</b><br/>Produtos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
			                    	divRefresh('{$g['divIdParent']}', true);
			                    	if (request.code) {
			                			$(this).dialog("destroy").remove();     
			                        } else {
			                        	$(this).dialog("destroy").remove(); 
			                            dialogConfirm('Erro!', request.data);
			                        }
			                    } 
			                });	                  
				        }

					} else {
						$('#{$g['divId']}').hideLoading();
						var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
						dialogConfirm('Erro', request.data, \$buttons);
					}
					$('#{$g['divId']}').hideLoading();
					$('#{$g['divId']}').closest('.ui-dialog-content').dialog('close');

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
				$retorno = $VEN002->ven002atualizarCFOPManual($r_T005_Id, $D006_Id);
				if ($retorno !== true) {
					echo gGeraAlertMsg('Erro', $retorno);
					echo<<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						divRefresh('{$g['divId']}', true);
					} </script></div>
EOT;
				} else {
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							T005_Id = $r_T005_Id;
							idProgressBar = dialogAlert('Atualizando CFOP e alíquotas',"<span id='progressBar'>Processando 0%</span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' />");
						            ven002AtualizarAliquotasPedidosCFOPManualLoop(idProgressBar,T005_Id,1); 

				            function ven002AtualizarAliquotasPedidosCFOPManualLoop(idProgressBar,T005_Id, inicio){
				                $.getJSON('/ven/ven002/form_func-ajax/atualizarAliquota/?ajax=true&T005_Id=' + encodeURIComponent("{$r_T005_Id}") + '&extra=' + encodeURIComponent("{$r_extra}") + '&inicio=' + inicio + '&callback=?', function(request) {
				                    if (request.concluido == 0) {
				                    	$('#progressBar').html('<b>Processado '+ request.percentual + '%</b>' + '</b><br/>Produtos atualizados: <b>' + request.ok + '</b><br/>Produtos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
				                    	ven002AtualizarAliquotasPedidosCFOPManualLoop(idProgressBar,T005_Id,0);
				                    }else{
				                    	$('#dc-' + idProgressBar).dialog("destroy").remove();
				                    	dialogConfirm('CFOP e Alíquotas atualizadas','<b>Processado '+ request.percentual + '%</b>' + '</b><br/>Produtos atualizados: <b>' + request.ok + '</b><br/>Produtos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
				              			divRefresh('{$g['divIdParent']}', true);
				                    	if (request.code) {
				                			$(this).dialog("destroy").remove();     
				                        } else {
				                        	$(this).dialog("destroy").remove(); 
				                            dialogConfirm('Erro!', request.data);
				                        }
				                    } 
				                });	                  
					        }
					        $('#{$g['divId']}').hideLoading();
							$('#{$g['divId']}').closest('.ui-dialog-content').dialog('close');
							
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

	case 'ven002FormComissoes':
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
        $form_tabela = 'T005';
        $form_campo_pk = 'T005_Id';
        $enviar_btnId = 'ven002FormComissoesBtn';
        $enviar_acaoHref = '/ven/ven002/form/ven002FormComissoes/';
        
        //form editor
        $form_editor = array(
            'geral' => '{"T005_Valor_Total_Comissao_Usuario":{"top":7,"left":48.015625,"width":56,"hidden":0},"T005_Percentual_Comissao_Usuario":{"top":7,"left":224.09375,"width":56,"hidden":0},"T005_Valor_Total_Comissao":{"top":30,"left":1.515625,"width":56,"hidden":0},"T005_Percentual_Comissao":{"top":30,"left":224.09375,"width":56,"hidden":0},"T005_Percentual_Comissao_Manual":{"top":30,"left":314,"width":56,"hidden":0},"T005_Valor_Total_Comissao_Externo":{"top":53,"left":-2.140625,"width":56,"hidden":0},"T005_Percentual_Comissao_Externo":{"top":53,"left":224.09375,"width":56,"hidden":0},"T005_Valor_Comissao_Representante":{"top":76,"left":13.765625,"width":56,"hidden":0},"T005_Percentual_Comissao_Representante":{"top":76,"left":224.09375,"width":56,"hidden":0},"ven002FormComissoesBtn":{"top":120,"left":240,"width":null,"hidden":0}}',
        );

        // novo formulário
        $form = new Formulario('ven002FormComissoes');

        // Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
        $formArray = array(
            'campos' => array(
                'geral' => array(
                	'T005_Percentual_Comissao' => array('name' => 'T005_Percentual_Comissao', 'label' => '%', 'type' => 'moeda', 'align' => 'right', 'length' => array(10,10), 'readonly' => true),
                	'T005_Percentual_Comissao_Manual' => array('name' => 'T005_Percentual_Comissao_Manual', 'label' => '% manual', 'type' => 'moeda', 'align' => 'right', 'length' => array(10,10)),
                	'T005_Valor_Total_Comissao' => array('name' => 'T005_Valor_Total_Comissao', 'label' => 'Comissão Vendedor Interno', 'type' => 'moeda','align' => 'right', 'length' => array(10,10), 'readonly' => true),
                	'T005_Percentual_Comissao_Representante' => array('name' => 'T005_Percentual_Comissao_Representante', 'label' => '%', 'type' => 'moeda','align' => 'right', 'length' => array(10,10)),
                	'T005_Valor_Comissao_Representante' => array('name' => 'T005_Valor_Comissao_Representante', 'label' => 'Comissão Representante', 'type' => 'moeda','align' => 'right', 'length' => array(10,10), 'readonly' => true),
                	'T005_Percentual_Comissao_Externo' => array('name' => 'T005_Percentual_Comissao_Externo', 'label' => '%', 'type' => 'moeda','align' => 'right', 'length' => array(10,10)),
                	'T005_Valor_Total_Comissao_Externo' => array('name' => 'T005_Valor_Total_Comissao_Externo', 'label' => 'Comissão Vendedor Externo', 'type' => 'moeda','align' => 'right', 'length' => array(10,10), 'readonly' => true),
                	'T005_Percentual_Comissao_Usuario' => array('name' => 'T005_Percentual_Comissao_Usuario', 'label' => '%', 'type' => 'moeda','align' => 'right', 'length' => array(10,10)),
                	'T005_Valor_Total_Comissao_Usuario' => array('name' => 'T005_Valor_Total_Comissao_Usuario', 'label' => 'Comissão Usuário', 'type' => 'moeda','align' => 'right', 'length' => array(10,10), 'readonly' => true),
        			'ven002FormComissoesBtn' => array('name' => 'ven002FormComissoesBtn', 'type'=>'submit', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true),
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

    case 'ven002FormMoeda':
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
        $form_tabela = 'T005';
        $form_campo_pk = 'T005_Id';
        $enviar_btnId = 'ven002FormMoedaBtn';
        $enviar_acaoHref = '/ven/ven002/form/ven002FormMoeda/';
        
        //form editor
        $form_editor = array(
            'geral' => '{"T005_D044_Id":{"top":7,"left":73,"width":95,"hidden":0},"T005_Cotacao_Moeda":{"top":30,"left":14,"width":88,"hidden":0},"ven002FormMoedaBtn":{"top":74,"left":144,"width":null,"hidden":0}}',
        );

        // novo formulário
        $form = new Formulario('ven002FormMoeda');

        // Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
        $formArray = array(
            'campos' => array(
                'geral' => array(
                	'T005_D044_Id' => array('name' => 'T005_D044_Id', 'label' => 'Moeda', 'type' => 'select', 
                		'select' => gGeraSelect('D044', 'D044_Id', 'D044_Moeda,D044_Simbolo')
                	),
                    'T005_Cotacao_Moeda' => array('name' => 'T005_Cotacao_Moeda', 'label' => 'Cotação da Moeda', 'type' => 'moeda', 'align' => 'right', 'length' => array(10,10), 'casasDecimais' => 5),
                	'ven002FormMoedaBtn' => array('name' => 'ven002FormMoedaBtn', 'type'=>'submit', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true),
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

    /**
    * Dados comerciais para impressao de Commercial Invoice / Packing List.
    */
    case 'ven002FormExportacao':
        $tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
        // A aba recebe o pedido pai em T005_Id. O acaoId pode referenciar o
        // registro auxiliar T005A após o submit, portanto não deve ser usado
        // para gravar a cubagem no pedido.
        $T005_Id = (isset($_REQUEST['T005_Id'])) ? (int) $_REQUEST['T005_Id'] : ((isset($_REQUEST['acaoId'])) ? (int) $_REQUEST['acaoId'] : 0);
        $formMultiTab = false;
        $formId = uniqid();

        // T005 é a tabela principal. Os campos T005A_* são carregados e
        // gravados pelo sqlAuto na tabela auxiliar, como nos demais forms
        // relacionais do sistema.
        $form_tabela = 'T005';
        $form_campo_pk = 'T005_Id';
        $enviar_btnId = 'ven002FormExportacaoBtn';
        $enviar_acaoHref = '/ven/ven002/form/ven002FormExportacao/';

        $form_editor = array(
            'geral' => '{"T005A_Invoice_Payment_Terms":{"top":7,"left":14.015625,"width":300,"hidden":0},"T005A_Invoice_From":{"top":30,"left":65.109375,"width":300,"hidden":0},"T005A_Invoice_To":{"top":53,"left":78.28125,"width":300,"hidden":0},"T005A_Invoice_Number":{"top":76,"left":13.703125,"width":300,"hidden":0},"T005A_Invoice_Incoterms":{"top":99,"left":41,"width":300,"hidden":0},"T005_Total_Cubagem":{"top":122,"left":41,"width":300,"hidden":0},"ven002FormExportacaoBtn":{"top":166,"left":208,"width":null,"hidden":0}}'
        );

        $form = new Formulario('ven002FormExportacao');
        $formArray = array(
            'campos' => array(
                'geral' => array(
                    'T005A_Invoice_Payment_Terms' => array('name' => 'T005A_Invoice_Payment_Terms', 'label' => 'Payment Terms', 'type' => 'text', 'length' => array(60, 255)),
                    'T005A_Invoice_From' => array('name' => 'T005A_Invoice_From', 'label' => 'From', 'type' => 'text', 'length' => array(60, 255)),
                    'T005A_Invoice_To' => array('name' => 'T005A_Invoice_To', 'label' => 'To', 'type' => 'text', 'length' => array(60, 255)),
                    'T005A_Invoice_Number' => array('name' => 'T005A_Invoice_Number', 'label' => 'Invoice Number', 'type' => 'text', 'length' => array(30, 100)),
                    'T005A_Invoice_Incoterms' => array('name' => 'T005A_Invoice_Incoterms', 'label' => 'Incoterms', 'type' => 'text', 'length' => array(30, 100)),
                    'T005_Total_Cubagem' => array('name' => 'T005_Total_Cubagem', 'label' => 'Vol in CM3', 'type' => 'moeda', 'casasDecimais' => 3, 'length' => array(12, 10)),
                    'ven002FormExportacaoBtn' => array('name' => 'ven002FormExportacaoBtn', 'type' => 'submit', 'reload' => false, 'value' => 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true),
                )
            )
        );
        $form->definirFormularioArray($formArray);

        if ($formCodigo = $form->processarFormulario($form_tabela)) {
            if ($formCodigo == 1) {
                echo gGeraAlertMsgErroForm($form);
            } else if ($formCodigo == 2) {
                if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $T005_Id, $form)) {
                    echo gGeraAlertMsg('Operação OK', 'Dados de exportação alterados com sucesso!');
                    echo <<<EOT
                        <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                            divRefresh('{$g['divId']}', true);
                        } </script></div>
EOT;
                }
            }
        } else if (!empty($T005_Id)) {
            $processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $T005_Id);
            if ($processado !== true) {
                reset($form->formArray['campos']);
                if ($tab == key($form->formArray['campos'])) {
                    echo $processado;
                }
            }
        }

        $gerado = gGeraCamposForm($form, $tab, $T005_Id, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);
        $g['smarty']->assign('formularioID', $form->formularioID());
        $g['smarty']->assign('table', $gerado['table']);
        $g['smarty']->assign('form_tabela', $form_tabela);
        echo $gerado['pagina'];
        $g['smarty']->assign('formId', $formId);
        $g['smarty']->display('formEditor.tpl');
    break;

	/**
	 * Formulário Configuracoes
	 */
	case 'ven002formConfiguracoes':
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
        $T005_Id = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($r_T005A_Id)) ? $r_T005A_Id : false;

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
		$form_tabela       = 'T005A';
		$form_campo_pk     = 'T005A_Id';
        $form_parent       = 'T005_Id';
        $form_parent_valor = $T005_Id;
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId ='ven002formConfiguracoesBtnAdd';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/ven/ven002/form/ven002formConfiguracoes/';

		//form editor
		$form_editor = array('geral' => '{"T005A_Flag_Conversao_Unidade":{"top":53,"left":173.859375,"width":63,"hidden":0},"T005A_Flag_Tipo":{"top":7,"left":207.203125,"width":63,"hidden":0},"T005A_Flag_Ratear_Frete_Custo":{"top":30,"left":7.125,"width":63,"hidden":0},"ven002formConfiguracoesBtnAdd":{"top":97,"left":368,"width":null,"hidden":0}}');

		// novo formulário
		$form = new Formulario('ven002formConfiguracoes');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T005A_Flag_Tipo' => array('name' => 'T005A_Flag_Tipo', 'label'=>'Compra interna','type' => 'select','select' => 
						array(
							array('title' => '...', 'value' => '0'),
							array('title' => 'Não', 'value' => '1'),
							array('title' => 'Sim', 'value' => '2'),						
						)
					),
					'T005A_T005_Id' => array('name' =>'T005A_T005_Id', 'label'=>'T005A_T005_Id', 'value' => $T005_Id, 'type' => 'hidden','readonly' => true),
					'T005A_Flag_Ratear_Frete_Custo' => array('name' => 'T005A_Flag_Ratear_Frete_Custo', 'label'=>'Ratear frete no custo quando for por conta do remetente','type' => 'select','select' => 
						array(
							array('title' => '...', 'value' => ''),
							array('title' => 'Não', 'value' => 'N'),
							array('title' => 'Sim', 'value' => 'S'),						
						)
					),
					'ven002formConfiguracoesBtnAdd' => array('name' => 'ven002formConfiguracoesBtnAdd', 'type'=>'submit', 'reload' => false, 'value' => empty($acaoId) ? 'Alterar' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true),
				)
			)
		);
		
		if ($g['C031']['utilizaUnidadeConversaoVenda'] == 'S') {
			$formArray['campos']['geral']['T005A_Flag_Conversao_Unidade'] = array('name' => 'T005A_Flag_Conversao_Unidade', 'label' => 'Conversão de unidade','type' => 'select','select' => 
																					array(
																						array('title' => 'Não', 'value' => 'N'),
																						array('title' => 'Sim', 'value' => 'S'),						
																					)
																				);
		}

		//$formArray['campos']['geral']['ven002formConfiguracoesBtnAdd'] = array('name' => 'ven002formConfiguracoesBtnAdd', 'type'=>'button', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
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

						if($form->campoValorEnviado('T005A_Flag_Conversao_Unidade') == 'S'){
						
							// Aqui é uma segurança caso o cliente não utilize unidade conversão e mudar para usar
							// E o pedido já foi criado antes de alterar a opção, repassa os valores para tabela estendida
							mysqli_query("UPDATE T006A 
									   LEFT JOIN T006 ON T006_Id = T006A_T006_Id
											 SET T006A_Quantidade_Comercial     = T006_Quantidade,
												 T006A_Preco_Unitario_Comercial = T006_Valor_Preco_Sem_Desconto_Unitario
										   WHERE T006_T005_Id ='{$T005_Id}' 
											 AND T006A_Quantidade_Comercial <= 0
											 AND T006A_Preco_Unitario_Comercial <= 0");	
											 
						} elseif ($form->campoValorEnviado('T005A_Flag_Conversao_Unidade') == 'N') {
							
							$totalizarVenda = false;
							// Se tiver um item com unidade conversão seta se precisa totalizar a venda com os novos valores
							$T006 = mysql_query("SELECT T006_Id 
												   FROM T006 
											  LEFT JOIN T006A ON T006A_T006_Id = T006_Id
												  WHERE T006_T005_Id = '{$T005_Id}'
													AND T006A_D184_Id > 0");
							if(mysql_num_rows($T006) > 0){
								$totalizarVenda = true;
							}
							
							mysqli_query("UPDATE T006A 
									   LEFT JOIN T006 ON T006_Id = T006A_T006_Id
											 SET T006_Quantidade = T006A_Quantidade_Comercial,
												 T006A_Quantidade_Comercial     = 0,
												 T006A_Preco_Unitario_Comercial = 0,
												 T006A_D184_Id                  = 0
										   WHERE T006_T005_Id ='{$T005_Id}' 
											 AND T006A_Quantidade_Comercial > 0
											 AND T006A_Preco_Unitario_Comercial > 0");	
						
							if($totalizarVenda){
								// Da um UPDATE para chamar a trigger e realizar o gravar totalizacao
								mysql_query("UPDATE T005 SET T005_Flag_Status = T005_Flag_Status
													   WHERE T005_Id = '{$T005_Id}'");
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
						$T005A = $g['mysqlLastId'];	
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

}
