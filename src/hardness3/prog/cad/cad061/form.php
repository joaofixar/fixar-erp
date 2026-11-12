<?php
namespace hardness;
/**
 * Weiberlan
 * 08/11/2011
 * Formulários dos Pedidos
 */
require_once('prog/cad/cad061/form_func-js.php');

require_once("old/lib/funcoes.php");

//Classes
require_once("bibliotecas/classes/CAD061.php");
$CAD061 = new CAD061();
/**
 * Ações
 */
switch ($g['r'][3]) {
	/**
	* Form Principal
	* Chamado em: ven002tab02
	*/
	case 'cad061form01':


		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$r_D157_Id = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;

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
			$sql = "SELECT D157_D024_Id,D020_D022_Id,D022_D036_Id, D024_D022_Id, D024_D020_Id, D157_C007_Id_Vendedor_Interno, D157_C007_Id_Vendedor_Externo FROM D157 LEFT JOIN D024 ON D024_Id=D157_D024_Id LEFT JOIN D020 ON D020_Id=D024_D020_Id LEFT JOIN D022 ON D022_Id=D020_D022_Id LEFT JOIN D036 ON D036_Id=D022_D036_Id WHERE D157_Id = '{$acaoId}'";
			$result = mysql_query($sql);
			$aD024 = mysql_fetch_assoc($result);		
			$idTransportadoraCidade=$aD024['D020_D022_Id'];
			$idCliente=$aD024['D157_D024_Id'];
			$idTransportadora=$aD024['D024_D022_Id'];
            $idTipoTransp=$aD024['D022_D036_Id'];
            $idVendedorInterno=$aD024['D157_C007_Id_Vendedor_Interno'];
			$idVendedorExterno=$aD024['D157_C007_Id_Vendedor_Externo'];
		}
		//if (isset($r_form_ven002_form1)) {
		//	$idCliente = $r_form_ven002_form1['D157_D024_Id'];
		//}

		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'D157';
		$form_campo_pk = 'D157_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = array('ven002FormCabecalhoBtnAdd', 'ven002FormCabecalhoBtnCln', 'ven002FormCabecalhoBtnNew');
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad061/form/cad061form01/';
		
		$form_editor = array(
			'geral'=>'{"D157_Percentual_Comissao_Representante":{"top":7,"left":5,"width":146,"hidden":1},"D157_Validade_Proposta":{"top":7,"left":5,"width":146,"hidden":1},"D157_D024_Id":{"top":7,"left":89,"width":267,"hidden":0},"D157_D013_Id":{"top":7,"left":525,"width":239,"hidden":0},"ven002FormContatos":{"top":5,"left":832,"width":null,"hidden":0},"D157_D006_Id":{"top":7,"left":987,"width":288,"hidden":1},"D157_C007_Id_Vendedor_Interno":{"top":30,"left":75,"width":288,"hidden":0},"D157_Data_Emissao":{"top":30,"left":528,"width":232,"hidden":0},"D157_C007_Id_Vendedor_Externo":{"top":53,"left":84,"width":287,"hidden":0},"D157_D021_Id":{"top":53,"left":498,"width":240,"hidden":0},"D157_Flag_ACP":{"top":76,"left":42,"width":128,"hidden":0},"D157_Flag_Lista_Preco":{"top":76,"left":259,"width":96,"hidden":0},"D157_Flag_Destino_Produto":{"top":76,"left":526,"width":240,"hidden":0},"D157_Pedido_Ecommerce":{"top":76,"left":1008,"width":128,"hidden":1},"D157_D148_Id_Entrega":{"top":99,"left":34,"width":287,"hidden":0},"D157_Data_Entrega":{"top":99,"left":525,"width":232,"hidden":0},"D157_Percentual_Comissao":{"top":99,"left":1068,"width":104,"hidden":1},"D157_D148_Id_Cobranca":{"top":122,"left":24,"width":287,"hidden":0},"D157_Prazo_Entrega":{"top":122,"left":485,"width":232,"hidden":0},"tituloOC":{"top":144,"left":394.984375,"width":null,"hidden":1},"D157_Custo_Frete":{"top":145,"left":1088,"width":88,"hidden":1},"D157_Ordem_Compra":{"top":168,"left":31,"width":280,"hidden":0},"D157_Codigo_Rastreio":{"top":168,"left":989,"width":128,"hidden":1},"tituloObservacoes":{"top":184,"left":405.984375,"width":null,"hidden":1},"D157_Nome_Transportadora":{"top":191,"left":1010,"width":128,"hidden":1},"D157_Observacao":{"top":214,"left":89,"height":59,"width":280,"hidden":0},"D157_Observacao_Comercial":{"top":214,"left":436,"height":59,"width":280,"hidden":0},"D157_Flag_Dividido":{"top":214,"left":1060,"width":58,"hidden":1},"D157_Quantidade_Volumes":{"top":260,"left":1161,"width":127,"hidden":1},"D157_Observacao_Nota_Fiscal":{"top":283,"left":67,"height":59,"width":280,"hidden":0},"D157_Observacao_1":{"top":283,"left":459,"height":59,"width":280,"hidden":0},"D157_Especie":{"top":283,"left":1139,"width":127,"hidden":1},"D157_Flag_Expedicao":{"top":306,"left":1143,"width":103,"hidden":1},"D157_D036_Id":{"top":421,"left":661,"width":128,"hidden":0},"tituloTransporte":{"top":349.984375,"left":405.984375,"width":null,"hidden":1},"D157_Peso_Bruto":{"top":351.96875,"left":1076,"width":127,"hidden":1},"D157_Flag_Frete":{"top":374.984375,"left":96,"width":288,"hidden":0},"D157_Valor_Frete":{"top":374.984375,"left":628,"width":120,"hidden":0},"D157_C007_Id_Embalagem":{"top":374.96875,"left":1095,"width":104,"hidden":1},"D157_D022_Id":{"top":397.984375,"left":39,"width":267,"hidden":0},"D157_Flag_Totalizar_Peso":{"top":398,"left":676,"width":64,"hidden":0},"TransportadoraPadrao":{"top":421,"left":8,"width":288,"hidden":0},"button":{"top":419,"left":432,"width":null,"hidden":0},"ven002FormCabecalhoBtnCln":{"top":442,"left":656,"width":null,"hidden":1},"ven002FormCabecalhoBtnAdd":{"top":442,"left":736,"width":null,"hidden":0},"D157_Peso_Liquido":{"top":420.96875,"left":1020,"width":127,"hidden":1},"D157_D116_Id":{"top":466.984375,"left":1110,"width":42,"hidden":1}}'
		);

		$vendedorInternoRead = true;
		$vendedorInternoExtra = "onfocus='this.initialSelect = this.selectedIndex;' onchange='this.selectedIndex = this.initialSelect;'";
		if($acaoId > 0){		
			$sqlC007 = mysql_query("SELECT C007_Flag_Ausente FROM D157 LEFT JOIN D024 ON D024_Id=D157_D024_Id LEFT JOIN C007 ON C007_Id=D024_C007_Vendedor_Interno WHERE D157_Id = '{$acaoId}'");
			$rowC007 = mysql_fetch_array($sqlC007);
			if(($rowC007['C007_Flag_Ausente'] == 'S' && $g['C031']['alterarVendedorAusente'] == 'S') || ($g['nivelAlcada']==4) || $g['C031']['alterarVendedor']=='S'){
				$vendedorInternoRead = false;
				$vendedorInternoExtra = "";
			}
		}

		$vendedorExternoRead = true;
		$vendedorExternoExtra = "onfocus='this.initialSelect = this.selectedIndex;' onchange='this.selectedIndex = this.initialSelect;'";
		if($acaoId > 0){		
			$sqlC007 = mysql_query("SELECT C007_Flag_Ausente FROM D157 LEFT JOIN D024 ON D024_Id=D157_D024_Id LEFT JOIN C007 ON C007_Id=D024_C007_Vendedor_Externo WHERE D157_Id = '{$acaoId}'");
			$rowC007 = mysql_fetch_array($sqlC007);
			if(($rowC007['C007_Flag_Ausente'] == 'S' && $g['C031']['alterarVendedorAusente'] == 'S') || ($g['nivelAlcada']==4)){
				$vendedorExternoRead = false;
				$vendedorExternoExtra = "";
			}
		}

		// endereço de entrega
		$arrayEndereco = gGeraSelect('D148','D148_Id','D148_Nome','WHERE D148_D024_Id='.$idCliente.' ORDER BY D148_Id',true);
		$arrayEndereco[0]['title'] = 'PADRÃO';

		// novo formulário
		$form = new Formulario('form_ven002_form1');

		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D157_D024_Id' => array('name' => 'D157_D024_Id', 'label'=> "Cliente", 'type' => 'pesquisa', 'extra' => "onchange=\"carregaCFOP_Contatos(this, '{$g['divId']}', '', '{$g['C031']['alterarVendedorAusente']}', '{$g['C031']['alterarVendedor']}');\"", 'pesquisa' => array('D024','D024_Id','D024_Nome_Empresa,D020_Nome_Cidade,D018_UF,D024_Cpf,D024_Cnpj,D024_DDD_Telefone_1,D024_Telefone_1,D024_Nome_Fantasia,D024_Id','left join D020 on D020_Id=D024_D020_Id left join D018 on D018_Id=D024_D018_Id WHERE D024_Flag_Cliente="S" and D024_Flag_Ativo="S" order by D024_Nome_Empresa','%1 - %9 - (%4%5) -  [%2/%3]'), 'length' => array(80,30)),
					'D157_D013_Id' => array('name' => 'D157_D013_Id', 'label' => 'Contato', 'type' => 'select',
						'select' => gGeraSelect('D013','D013_Id','D013_Nome_Contato,D013_DDD_Telefone_1,D013_Telefone_1,D013_E_Mail',"where D013_D024_Id=$idCliente AND D013_Flag_Ativo = 'S'",true),
						'selectRefresh' => array('D013','D013_Id','D013_Nome_Contato,D013_DDD_Telefone_1,D013_Telefone_1,D013_E_Mail',"where D013_D024_Id=$idCliente AND D013_Flag_Ativo = 'S'",true),
					),
					'ven002FormContatos' => array('name' => 'ven002FormContatos', 'type'=>'button', 'reload' => false, 'value' => 'Incluir', 'reject' => true),
					'D157_D148_Id_Entrega' => array('name' => 'D157_D148_Id_Entrega', 'label' => 'Endereço Entrega', 'type' => 'select', 'selectRefresh' => array('D148','D148_Id','D148_Nome','WHERE D148_D024_Id='.$idCliente.' ORDER BY D148_Id',true),
						'select' => $arrayEndereco
					),
					'D157_D148_Id_Cobranca' => array('name' => 'D157_D148_Id_Cobranca', 'label' => 'Endereço Cobrança', 'type' => 'select', 'selectRefresh' => array('D148','D148_Id','D148_Nome','WHERE D148_D024_Id='.$idCliente.' ORDER BY D148_Id',true),
						'select' => $arrayEndereco
					),
					'D157_D006_Id' => array('name' => 'D157_D006_Id', 'label'=>'CFOP','type' => 'select', 'select' => 
        					gGeraSelect('D006','D006_Id','D006_Codigo_CFOP,D006_Descricao',' WHERE D006_Flag_Inativo!="S" AND D006_Flag_Departamento="COMERCIAL"  ORDER BY D006_Codigo_CFOP',true),
					),											
					'D157_Data_Emissao' => array('name' => 'D157_Data_Emissao','label'=>'Emissão','type' => 'data', 'length' => array(20,10)),
					'D157_Data_Entrega' => array('name' => 'D157_Data_Entrega','label'=>'Entrega','type' => 'data', 'length' => array(20,10), 'exibir'=>false),
					'D157_C007_Id_Vendedor_Interno' => array('name' => 'D157_C007_Id_Vendedor_Interno', 'label'=>'Vendedor','type' => 'select', 'extra' => $vendedorInternoExtra, 'readonly' => $vendedorInternoRead, 'select' => 
							gGeraSelect('C007','C007_Id','IF(C007_Ativo != "N"[[virgula]] C007_Primeiro_Nome[[virgula]] CONCAT(C007_Primeiro_Nome[[virgula]]" (INATIVO)")) as C007_Primeiro_Nome','WHERE C007_Flag_Vendedor!="" AND (C007_Ativo != "N" OR C007_Id = "'.$idVendedorInterno.'") ORDER BY C007_Primeiro_Nome',true),
					),
					'D157_C007_Id_Vendedor_Externo' => array('name' => 'D157_C007_Id_Vendedor_Externo', 'label'=>'Externo','type' => 'select', 'extra' => $vendedorExternoExtra, 'readonly' => $vendedorExternoRead, 'select' => 
							gGeraSelect('C007','C007_Id','IF(C007_Ativo != "N"[[virgula]] C007_Primeiro_Nome[[virgula]] CONCAT(C007_Primeiro_Nome[[virgula]]" (INATIVO)")) as C007_Primeiro_Nome','WHERE C007_Flag_Vendedor!="" AND (C007_Ativo != "N" OR C007_Id = "'.$idVendedorExterno.'") ORDER BY C007_Primeiro_Nome',true),
					),
					'D157_D021_Id' => array('name' => 'D157_D021_Id', 'label'=>'Representante','type' => 'select', 'select' => 
							gGeraSelect('D021','D021_Id','D021_Nome_Empresa','',true),
					),	
					'D157_Percentual_Comissao' => array('name' => 'D157_Percentual_Comissao', 'label'=>'Comissão %','value'=>'0,00','type' => 'moeda', 'length' => array(20,10)),
					'D157_Percentual_Comissao_Representante' => array('name' => 'D157_Percentual_Comissao_Representante', 'label'=>'Comissão Rep. %','value'=>'0,00','type' => 'moeda', 'length' => array(20,10)),
					'D157_Flag_ACP' => array('name' => 'D157_Flag_ACP', 'label'=>'Tipo Documento','type' => 'select','select' => 
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
					'D157_Prazo_Entrega' => array('name' => 'D157_Prazo_Entrega', 'label'=>'Prazo de Entrega','type' => 'text', 'length' => array(53,30)),
					'D157_D116_Id' => array('name' => 'D157_D116_Id', 'label' => 'Veículo','type' => 'select',
						'select'=> gGeraSelect('D116', 'D116_Id', 'D116_Placa,D133_Modelo,D134_Cor', "LEFT JOIN D133 on D133_Id=D116_D133_Id LEFT JOIN D134 on D134_Id=D116_D134_Id WHERE D116_D024_Id=$idCliente", true),
						'selectRefresh' => array('D116', 'D116_Id', 'D116_Placa,D133_Modelo,D134_Cor', "LEFT JOIN D133 on D133_Id=D116_D133_Id LEFT JOIN D134 on D134_Id=D116_D134_Id WHERE D116_D024_Id=$idCliente", true)
					),
					'D157_Flag_Lista_Preco' => 	array('name' => 'D157_Flag_Lista_Preco', 'label' => 'Lista Preço', 'type' => 'select', 'select' => 
						array(
							array('title' => 'Preço 1', 'value' => '1'),   
							array('title' => 'Preço 2', 'value' => '2'),   
							array('title' => 'Preço 3', 'value' => '3'),   
							array('title' => 'Preço 4', 'value' => '4'),   
							array('title' => 'Preço 5', 'value' => '5'),
						)
					),
					'ven002FormVeiculos' => array('name' => 'ven002FormVeiculos', 'type'=>'button', 'reload' => false, 'value' => 'Incluir', 'reject' => true),
					//'tituloOC' => array('name' => 'tituloOC', 'label'=>'Ordem de Compra','type' => 'titulo', 'reject' => true, 'reload' => false),
					'D157_Ordem_Compra' => array('name' => 'D157_Ordem_Compra', 'label'=>'Ordem de Compra','type' => 'text', 'length' => array(53,30)),
					//'tituloObservacoes' => array('name' => 'tituloObservacoes', 'label'=>'Observações','type' => 'titulo', 'reject' => true),
					'D157_Observacao' => array('name' => 'D157_Observacao', 'label'=>'Cliente','type' => 'textarea','extra' => 'title="Esta observação será visualizada pelo cliente (Não será impressa na DANFE)"', 'length' => array(3,50)),
					'D157_Observacao_Comercial' => array('name' => 'D157_Observacao_Comercial', 'label'=>'Comercial Interna','type' => 'textarea','extra' => 'title="Observação de uso interno da área comercial (Não será impressa na DANFE)"', 'length' => array(3,50)),
					'D157_Observacao_Nota_Fiscal' => array('name' => 'D157_Observacao_Nota_Fiscal', 'label'=>'Nota Fiscal','type' => 'textarea','extra' => 'title="Esta observação será impressa na DANFE"', 'length' => array(3,50)),
					'D157_Observacao_1' => array('name' => 'D157_Observacao_1', 'label'=>'Almox.:','type' => 'textarea','extra' => 'title="Observação de uso interno da área de estoque (Não será impressa na DANFE)"', 'length' => array(3,50)),
					//'tituloTransporte' => array('name' => 'tituloTransporte', 'label'=>'Transporte','type' => 'titulo', 'reject' => true),
					'D157_Flag_Frete' => array('name' => 'D157_Flag_Frete','label'=>'Frete', 'type' => 'select',
						'select' => array(
							array('title' => '0-EMITENTE', 'value' => '0'),
							array('title' => '1-DESTINATÁRIO', 'value' => '1'),
							array('title' => '2-TERCEIROS', 'value' => '2'),
							array('title' => '9-SEM FRETE', 'value' => '9'),
						),
					),
					'D157_Valor_Frete' => array('name' => 'D157_Valor_Frete', 'label'=>'Valor Frete','value'=>'0,00','type' => 'moeda', 'length' => array(20,20)),
					'D157_D022_Id' => array('name' => 'D157_D022_Id', 'label' => 'Transportadora', 'type' => 'pesquisa', 'pesquisa' => array('D022','D022_Id','D022_Nome_Empresa,D020_Nome_Cidade,D018_UF','left join D020 on D020_Id=D022_D020_Id left join D018 on D018_Id=D022_D018_Id'),'extra' => "onchange=\"carregaTipoTransp(this, '{$g['divId']}')\"", 'length' => array(50,90)),
					'D157_D036_Id' => array('name' => 'D157_D036_Id', 'label' => 'Tipo', 'type' => 'select', 'select' =>
							gGeraSelect('D036','D036_Id','D036_Tipo_Transporte','',true),
					),
					'TransportadoraPadrao' => array('name' => 'TransportadoraPadrao', 'label' => 'Transportadora Padrão', 'type' => 'select','reject' =>true ,'disabled' => true,
						'select' => gGeraSelect('D022','D022_Id','D022_Nome_Empresa,D020_Nome_Cidade,D018_UF',"left join D020 on D020_Id=D022_D020_Id left join D018 on D018_Id=D022_D018_Id WHERE D022_Id='{$idTransportadora}' OR D022_Id = '{$idTransportadoraCidade}'",false), 
					),
					'button' => array('name' => 'button', 'type' => 'button','value' =>'Usar Padrão','reload' => false,'extra' => "onclick='copiaTransportadora({$idTipoTransp})'",'reject'=>true),
					'nada' => array('name' => 'nada', 'label' => 'Transportadora', 'type' => 'hidden', 'reject'=>true, 'length' => array(30,30)),
					'D157_Flag_Revenda_Consumidor' => array('name' => 'D157_Flag_Revenda_Consumidor', 'label'=>'D157_Flag_Revenda_Consumidor','type' => 'hidden'
						//'select' => 
							//array(
								//array('title' => '...', 'value' => 0, 'selected' => true),
								//array('title' => 'Revenda', 'value' => 1),
								//array('title' => 'Consumidor atacado', 'value' => 2),
								//array('title' => 'Consumidor varejo', 'value' => 3),
							//)
					),
					'D157_Flag_Expedicao' => array('name' => 'D157_Flag_Expedicao', 'label'=>'Expedição','type' => 'select','select' => 
						array(
							array('title' => '...', 'value' => 0),
							array('title' => 'Comercial', 'value' => 1),
							array('title' => 'Balcão', 'value' => 2),
						)
					),
					'D157_Quantidade_Volumes' => array('name' => 'D157_Quantidade_Volumes', 'label' => 'Volumes', 'type' => 'text'),
					'D157_Especie' => array('name' => 'D157_Especie', 'label' => 'Espécie', 'type' => 'text'),
					'D157_Peso_Liquido' => array('name' => 'D157_Peso_Liquido', 'label' => 'Peso Liquido', 'type' => 'text'),
					'D157_Peso_Bruto' => array('name' => 'D157_Peso_Bruto', 'label' => 'Peso Bruto', 'type' => 'text'),
					'D157_Nome_Transportadora' => array('name' => 'D157_Nome_Transportadora', 'label' => 'Nome transportadora', 'type' => 'text'),
					'D157_Flag_Dividido' => array('name' => 'D157_Flag_Dividido', 'label'=>'Dividido','type' => 'select','select' => 
						array(
							array('title' => 'Não', 'value' => 'N'),
							array('title' => 'Sim', 'value' => 'S'),
						)
					),
					'D157_Pedido_Ecommerce' => array('name' => 'D157_Pedido_Ecommerce', 'label' => 'N° Ecommerce', 'type' => 'text'),
					'D157_Codigo_Rastreio' => array('name' => 'D157_Codigo_Rastreio', 'label' => 'Codigo de Rastreio', 'type' => 'text'),
					'D157_C007_Id_Embalagem' => array('name' => 'D157_C007_Id_Embalagem', 'label' => 'Embalado por', 'type' => 'select', 'select' =>
						gGeraSelect('C007', 'C007_Id', 'C007_Primeiro_Nome', '', true)
					),
					'D157_Custo_Frete' => array('name' => 'D157_Custo_Frete', 'label' => 'Custo Frete', 'type' => 'moeda'),
					'D157_Validade_Proposta' => array('name' => 'D157_Validade_Proposta', 'label' => 'Validade Proposta', 'type' => 'text'),
					'D157_Flag_Totalizar_Peso' => array('name' => 'D157_Flag_Totalizar_Peso', 'label'=>'Totalizar Peso','type' => 'select','select' => 
						array(
							array('title' => 'Não', 'value' => 'N'),
							array('title' => 'Sim', 'value' => 'S'),
						)
					),
					'D157_Flag_Destino_Produto' => array('name' => 'D157_Flag_Destino_Produto','label'=>'Destino', 'type' => 'select', 
						'select' => array(
							array('title' => '...', 'value' => "", 'selected' => true),
							array('title' => '1 - INDUSTRIALIZAÇÃO', 'value' => "1"),
							array('title' => '2 - REVENDA', 'value' => "2"),
							array('title' => '3 - CONSUMO', 'value' => "3"),
							array('title' => '4 - ESPECIAL', 'value' => "4"),
							array('title' => '5 - BONIFICAÇÃO', 'value' => "5"),
							array('title' => '6 - TROCA', 'value' => "6"),
						)
					),
					'D157_Tipo_Periodicidade' => array('name' => 'D157_Tipo_Periodicidade','label'=>'Tipo periodicidade', 'type' => 'select', 'extra' => 'title="Tipo de periodicidade. Informando dia mês o sistema irá repetir os pedidos sempre nos dias do mês que foram informados no campo "periodicidade". Informando dia semana, o sistema irá repetir os pedidos conforme os dias da semana. 1 - domingo, 2 - segunda-feira, 3 - terça feira, 4 - quarta-feira, 5 - quinta-feira, 6 - sexta-feira, 7 - sábado."',
						'select' => array(
							array('title' => '...', 'value' => "", 'selected' => true),
							array('title' => 'Dia/mês', 'value' => "1"),
							array('title' => 'Dia/semana', 'value' => "2"),
						)
					),
					'D157_Periodicidade' => array('name' => 'D157_Periodicidade', 'label' => 'Periodicidade', 'type' => 'text', 'extra' => 'title="Informe os dias conforme a periodicade. Para periodicidade mensal informa o dia do mês, para periodicidade semana informe o dia da semana conforme informado no campo "Tipo periodicidade""'),
				)
			)
		);
		/** Botão Limpar / Novo Registro
		if (!empty($acaoId)) {
			$formArray['campos']['geral']['ven002FormCabecalhoBtnNew'] = array('name' => 'ven002FormCabecalhoBtnNew', 'type'=>'submit', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right; margin-top:20px;', 'reject' => true);
		}
		*/
		$formArray['campos']['geral']['ven002FormCabecalhoBtnCln'] = array('name' => 'ven002FormCabecalhoBtnCln', 'type'=>'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right; margin-top:20px;', 'reject' => true);
		$formArray['campos']['geral']['ven002FormCabecalhoBtnAdd'] = array('name' => 'ven002FormCabecalhoBtnAdd', 'type'=>'button', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right; margin-top:20px;', 'reject' => true);

		if($g['C031']['venderEstoqueMatriz'] == 'S'){
			$formArray['campos']['geral']['D157_C004_Id_Estoque'] = array('name' => 'D157_C004_Id_Estoque', 'label' => 'Estoque', 'type' => 'select','select' => gGeraSelect('C004','C004_Id','C004_Nome_Abreviado,C004_Nome_Empresa',"where C004_Flag_Ativo='S' and (C004_Id='1' or C004_Id='{$g['empresaAtual']}')",true,$g['empresaAtual']));
		}

		$form->definirFormularioArray($formArray);
		
		$sql = "SELECT D157_D024_Id FROM D157 WHERE D157_Id = '{$r_D157_Id}';";
		$resultado = mysql_query($sql);
		$row = mysql_fetch_array($resultado);
		echo <<<EOT
		    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		    	$('#ven002FormContatos').unbind('click');
				$('#ven002FormContatos').unbind('click').bind('click', function(data) {
					var id = '{$row['D157_D024_Id']}';
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Cliente/Fornecedor - ID: ' + id, '/cad/cad001/content/cad001contentCadastro/', '&acaoId=' + encodeURIComponent(id) + '&tabela=D024' + '&tab=contatos', [950,553]);
					return false;
				});
				$('#ven002FormVeiculos').unbind('click');
				$('#ven002FormVeiculos').unbind('click').bind('click', function(data) {
					var id = '{$row['D157_D024_Id']}';
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Incluir Veículo', '/cad/cad001/content/cad001ContentVeiculosIncluir/', '&naoRefreshParent=true&D024_Id=' + encodeURIComponent(id) + '&tabela=D024' + '&tab=veiculos', [300,350]);
					return false;
				});

				$('#{$g['divId']} #D157_Flag_ACP').change(function() {
	                if($(this).val() == 8){
	                	$('#{$g['divId']} #D157_D022_Id_Pesquisa').val('').prop('readonly', true).css('background-color', '#ede7de');
	                	$('#{$g['divId']} #D157_D022_Id').val('');
	                	$('#{$g['divId']} #D157_Flag_Frete').val('9');
	                	$('#{$g['divId']} #button').closest('.formEditor').hide();
	                } else {
	                	$('#{$g['divId']} #D157_D022_Id_Pesquisa').prop('readonly', false).css('background-color', '#ffffff');
	                	$('#{$g['divId']} #button').closest('.formEditor').show();
	                }
	            });
	            $('#{$g['divId']} #D157_Flag_ACP').trigger('change');
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
					$FlagACP = $form->campoValorEnviado('D157_Flag_ACP');

					if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {						
						if($g['C031']['modeloExpedicao'] == '1'){
							if($FlagACP == '4'){
								$D157_Flag_Expedicao = 2;
							} else {
								$D157_Flag_Expedicao = 1;
							}
							mysql_query("UPDATE D157 SET D157_Flag_Expedicao = '{$D157_Flag_Expedicao}' WHERE D157_Id = '{$acaoId}'");
						}

						$D157 = mysql_query("SELECT D157_Flag_Lista_Preco, D022_Nome_Empresa FROM D157 LEFT JOIN D022 ON D022_Id=D157_D022_Id WHERE D157_Id = '{$acaoId}'");
 						$rD157 = mysql_fetch_array($D157);
						// Imprime uma mensagem e fecha a janela
						mysql_query("UPDATE D157 SET D157_Nome_Transportadora='{$rD157['D022_Nome_Empresa']}' WHERE D157_Id = '{$acaoId}'");
						echo gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!');
						$lista = $form->campoValorEnviado('D157_Flag_Lista_Preco');
						//Verifica se a lista de preço foi alterada
						if(($rD157['D157_Flag_Lista_Preco'] != $lista) && !empty($lista)){
							$VEN002->alteraLista($acaoId, $lista);
							echo gGeraAlertMsg('Atenção', 'A lista de preço foi alterada, os valores de tabela dos produtos serão modificados.');	
						}					
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								//divRefresh('{$r_divIdMain}', true);
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
	case 'cad061form02':
		$g['smarty']->assign('navSql', "D157_C004_Id = '{$g['empresaAtual']}'");
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']}').data('sqlPesquisa', '[D157_Id, D157_Data_Emissao as Data, D157_Data_Entrega as Data, D024_Nome_Empresa, D024_Nome_Fantasia]');
				$('#{$g['divId']}').data('labelPesquisa', '[Pedido, Emissão, Entrega, Razão Social, Nome Fantasia]');
				$('#{$g['divId']}').data('sqlExtra', '[LEFT JOIN D024 ON D157_D024_Id = D024_Id]');
			} </script></div>
EOT;
		$D157 = mysql_fetch_array(mysql_query("SELECT ifnull(D157_D157_Id_Origem,0) as D157_D157_Id_Origem, D157_Casas_Decimais_Preco FROM D157 WHERE D157_Id = '{$r_D157_Id}'"));
		if($D157['D157_D157_Id_Origem'] <= 0){ 
			gHabilitaNavegacao($g['divId'], 'CAD061', 'cad061form02');
		}
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$D157_Id = (isset($r_D157_Id)) ? $r_D157_Id : false;
		
		$D157=mysql_query("SELECT D157_D024_Id, D157_C004_Id_Estoque FROM D157 WHERE D157_Id='{$r_D157_Id}'");
		$mD157=mysql_fetch_array($D157);

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
		$form_tabela = 'D158';
		$form_campo_pk = 'D158_Id';
        $form_parent = 'D157_Id';
        $form_parent_valor = $D157_Id;
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = array('ven002FormProdutosBtnAdd', 'ven002FormProdutosBtnCln', 'ven002FormProdutosBtnNew');
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad061/form/cad061form02/';
        
        //form editor
        $form_editor = array(
            'geral' => '{"Procura_Produto":{"top":7,"left":36,"width":122,"hidden":0},"Marca":{"top":7,"left":267,"width":136,"hidden":0},"precoTabela":{"top":7,"left":466,"width":88,"hidden":0},"ven002BtnCadastrarProduto":{"top":166,"left":480,"width":null,"hidden":0},"D158_Descricao_Produto":{"top":30,"left":17,"width":360,"hidden":0},"PrecoMinimo":{"top":30,"left":464,"width":88,"hidden":0},"D158_Quantidade":{"top":53,"left":11,"width":72,"hidden":0},"D158_D037_Id":{"top":53,"left":160,"width":63,"hidden":0},"D158_Valor_Preco_Sem_Desconto_Unitario":{"top":53,"left":237,"width":72,"hidden":0},"D158_Percentual_Desconto":{"top":53,"left":400,"width":24,"hidden":0},"estoqueLiquido":{"top":53,"left":483,"width":88,"hidden":0},"D158_D006_Id":{"top":53,"left":792,"width":367,"hidden":1},"D158_Flag_Destino_Produto":{"top":76,"left":30,"width":144,"hidden":0},"D158_Observacao_Entrega":{"top":76,"left":237,"width":136,"hidden":0},"estoqueLoja":{"top":76,"left":480,"width":88,"hidden":0},"D158_Codigo_Substituto":{"top":99,"left":6,"width":136,"hidden":0},"D158_Numero_Pedido_Compra":{"top":99,"left":257,"width":88,"hidden":0},"D158_Item_Pedido_Compra":{"top":99,"left":399.953125,"width":40,"hidden":0},"estoqueTotal":{"top":99,"left":476,"width":88,"hidden":0},"D158_D022_Id":{"top":99,"left":751,"width":335,"hidden":1},"D158_Observacao":{"top":122,"left":13,"width":360,"hidden":0},"ordemCompra":{"top":122,"left":505,"width":88,"hidden":0},"D158_Hora_Entrega":{"top":122,"left":763,"width":127,"hidden":1},"UltimoPrecoVenda":{"top":145,"left":31,"height":62,"width":360,"hidden":0},"estoqueFora":{"top":145,"left":477,"width":88,"hidden":0},"D158_T208_Id":{"top":145,"left":804,"width":42,"hidden":1},"D158_Aliquota_IPI":{"top":168,"left":816,"width":40,"hidden":1},"ven002FormProdutosBtnCln":{"top":195,"left":480,"width":null,"hidden":0},"ven002FormProdutosBtnAdd":{"top":195,"left":560,"width":null,"hidden":0}}',
        );

		$C004_Id_Estoque = $g['empresaAtual'];
		if($g['C031']['venderEstoqueMatriz'] == 'S'){
			$C004_Id_Estoque = (isset($mD157['D157_C004_Id_Estoque']) && $mD157['D157_C004_Id_Estoque'] > 0) ? $mD157['D157_C004_Id_Estoque'] : $g['empresaAtual'];
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
			LEFT JOIN D083 on D083_D001_Id=D001_Id AND D083_D024_Id='{$mD157['D157_D024_Id']}'
			LEFT JOIN D144 ON D144_D049_Id=D049_Id  
			left join C007 on C007_Id=D009_C007_Id_Comprador
			where (D009_C004_Id = '{$C004_Id_Estoque}'  and D001_Flag_Ativo='S' and ifnull(D049_Modelo,'')!='0' and ifnull(D049_Flag_Ativo,'')!='N')  
			group by D009_Id  
			order by D001_Codigo_Produto ASC
EOT;
		$pesquisaGrid = array(
			'D001',																											              // tabela
			'D009_Id,D144_Id',																										      // chave primaria
			'D009_Id||C008_Id||D060_Id||D001_Codigo_Produto||D049_Modelo||D083_Codigo_Produto_Fornecedor||D001_Descricao_Produto||D001_Aplicacao||substr(D082_Marca,1,15) as D082_Marca||D037_Unidade||D005_Classificacao_Fiscal||D009_Preco_1||D009_Preco_2||D009_Preco_3||D009_Quantidade_Estoque||D009_Quantidade_Estoque_Liquido as Pedido||(select sum(T066_Quantidade_Estoque) from T066 left join D004 on D004_Id=T066_D004_Id  left join D146 ON D146_Id = D004_D146_Id where T066_D009_Id=D009_Id and UPPER(D146_Descricao)="LOJA") as Estoque||T066_Quantidade_Estoque||D009_Quantidade_Estoque_Fora||D009_Quantidade_Estoque_Similar||"" as retornaPrecoVenda||D009_Quantidade_OC||D004_Local||substr(C007_Primeiro_Nome,1,10) as C007_Primeiro_Nome||D001_Especificacoes||D001_Descricao_Ingles||D049_Id||D001_Flag_Pre_Cadastro||D144_Id||D060_Descricao_Grupo||D001_Id||"' . $D157_Id . '" as D157_Id',	// campos
			$pesquisaGridExtra,																								              // extras sql
			'Pesquisa de Produto',																										  // título da janela
			'Id,Dep,Setor,Código,Sub-item,Cód.Cli,Descrição,Referência,Marca,UN,NCM,Preço,Preço 2,Preço 3,Físico,Líquido,Loja,Locação,Fora,Similar,Preço,OC,Locação,Comprador,Observações,Descrição Inglês,D049_Id,D001_Flag_Pre_Cadastro,D144_Id,Setor,D001_Id,D157_Id',	  // descrição dos campos
			'campoProcuraProduto',																					             		 // callback js
			$g['divId'],																									              // divId
			array($D157_Id, $mD157['D157_D024_Id']),   																					  // parametro extra
			array(																											              // parametros para cada campo do grid
				array('chavePrimaria' => true, 'colunaExibir' => false),
				array('colunaAlinhar' => 'center', 'style' => 'width:1%', 'colunaExibir' => false),
				array('colunaAlinhar' => 'center', 'style' => 'width:1%', 'colunaExibir' => false),
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
				array('callback' => 'estoqueFora', 'style' => 'width: 1%;', 'colunaAlinhar' => 'right', 'callbackParameter' => array(false, $r_D157_Id)),
				array('callback' => 'abrirJanela', 'callbackParameter' => array('Estoque Similares', '/cad/cad002/content/cad002ContentEstoqueSimilares/', 'D001_Id', '850,400', true), 'style' => 'width: 1%;', 'colunaAlinhar' => 'right'),
				array('colunaTipo' => 'livre', 'callback' => 'retornaPrecoVendaPedido', 'colunaAlinhar' => 'right', 'style' => 'font-weight:bold;width:1%'),
				array('colunaAlinhar' => 'center', 'callback' => 'abrirJanela', 'callbackParameter' => array('Ordens de Compra', '/ven/ven001/content/ven001contentProdutoOC/', 'D009_Id', '700,400', true), 'style' => 'font-weight:normal;width:1%'),
				array('style' => 'width:1%'),
				array('style' => 'width:1%'),
				array('colunaExibir' => false),
				array('colunaExibir' => false),
				array('colunaExibir' => false),
				array('colunaExibir' => false),
				array('chavePrimaria' => true, 'colunaExibir' => false),
				array('colunaExibir' => false),
				array('colunaExibir' => false),
				array('colunaTitulo' => 'D157_Id', 'colunaExibir' => false)
			),
			'D009_Id||D049_Id||D009_Preco_1||D009_Preco_2||D009_Preco_3||D009_Quantidade_Estoque||D009_Quantidade_Estoque_Liquido||D144_Id||C008_Id||D060_Id||D009_Quantidade_Estoque_Real||T066_Quantidade_Estoque||D009_Quantidade_Estoque_Fora||D009_Quantidade_OC||D001_Flag_Pre_Cadastro||D144_Id||D001_Id' // Campos que são ignorados na cláusula where do pesquisaGrid
		);

		$readOnlyDescricao = ($g['C031']['editarDescricaoProdutoVenda'] == 'S') ? false : true;
		$readOnlyUnidade   = ($g['C031']['editarUnidadeProdutoVenda'] == 'S') ? false : true;
		$extraUnidade      = ($g['C031']['editarUnidadeProdutoVenda'] == 'S') ? "" : "onfocus='this.initialSelect = this.selectedIndex;' onchange='this.selectedIndex = this.initialSelect;'";

        $sqlVeiculo = mysql_query("select D157_D024_Id,D157_D116_Id from D157 where D157_Id='{$r_D157_Id}'");
		$resultVeiculo = mysql_fetch_array($sqlVeiculo);

		// novo formulário
		$form = new Formulario('ven002FormProdutosForm');

		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'Procura_Produto' => array('name' => 'Procura_Produto' , 'label' => 'Produto', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGrid, 'length' => array(20,50), 'reject' => true),
					'Marca' => array('name' => 'Marca', 'label' => 'Marca', 'type' => 'text', 'length' => array(20,255), 'reject' => true, 'disabled' => true),
					'D158_Codigo_Substituto' => array('name' => 'D158_Codigo_Substituto', 'label' => 'Código cliente', 'type' => 'text', 'length' => array(20,255), 'align' => 'right', 'style' => 'float:right;'),
					'D158_Descricao_Produto' => array('name' => 'D158_Descricao_Produto', 'label' => 'Descrição', 'type' => 'text', 'length' => array(64, 120), 'readonly' =>  $readOnlyDescricao),
					'D158_Quantidade' => array('name' => 'D158_Quantidade', 'label' => 'Quantidade', 'type' => 'moeda', 'length' => array(20, 30)),
					'D158_D037_Id' => array('name' => 'D158_D037_Id', 'label' => '', 'type' => 'select', 'readonly'=>$readOnlyUnidade, 'type' => 'select', 'align' => 'right', 'style' => 'float:right;', 'extra' => $extraUnidade, 'select' =>
					   	gGeraSelect('D037', 'D037_Id', 'D037_Unidade', '', true)
					),
					'D158_Valor_Preco_Sem_Desconto_Unitario' => array('name' => 'D158_Valor_Preco_Sem_Desconto_Unitario', 'label' => 'Preço Unit.', 'type' => 'moeda', 'length' => array(20, 30), 'casasDecimais' => $D157['D157_Casas_Decimais_Preco']),
					'PrecoMinimo' => array('name' => 'PrecoMinimo', 'type' => 'moeda', 'length' => array(20, 30), 'reject' => true, 'readonly' => true, 'style' => 'color: var(--BGVermelho); text-align: right;', 'label' => 'Prc. Mínimo'),
					'D158_Numero_Pedido_Compra' => array('name' => 'D158_Numero_Pedido_Compra', 'label' => 'OC/Item', 'type' => 'text', 'length' => array(20, 30)),
					'D158_Item_Pedido_Compra' => array('name' => 'D158_Item_Pedido_Compra', 'type' => 'text', 'length' => array(20, 30)),
					'D158_Percentual_Desconto' => array('name' => 'D158_Percentual_Desconto', 'label' => '%', 'type' => 'moeda', 'length' => array(15, 30), 'reject'=>true),
					'D158_D005_Id' => array('name' => 'D158_D005_Id', 'label' => 'NCM', 'type' => 'hidden', 'readonly' => true, 'align' => 'right', 'extra'=>'style="width:95px;"', 'extra' => "onfocus='this.initialSelect = this.selectedIndex;' onchange='this.selectedIndex = this.initialSelect;'", 'select' =>
					    gGeraSelect('D149', 'D005_Id', 'D005_Classificacao_Fiscal, D005_Flag_Aplicacao, D149_Aliquota_IPI', "LEFT JOIN D005 ON D149_D005_Id=D005_Id AND D149_C004_Id='{$g['empresaAtual']}' WHERE D005_Flag_Ativo='S' ORDER BY D005_Classificacao_Fiscal, D005_Observacao", true)
					),
					'D158_Aliquota_IPI' => array('name' => 'D158_Aliquota_IPI', 'label' => 'IPI', 'align' => 'right', 'type' => 'moeda', 'length' => array(5,20), 'style' => 'float:right;'),
					//'D158_Numero_Pedido_Compra' => array('name' => 'D158_Numero_Pedido_Compra', 'label' => 'Número OC', 'type' => 'moeda', 'length' => array(20, 30), 'align' => 'right', 'style' => 'float:right;'),
					//'D158_Item_Pedido_Compra' => array('name' => 'D158_Item_Pedido_Compra', 'label' => 'Item OC', 'type' => 'moeda', 'length' => array(20, 30)),
					//'D158_IPV' => array('name' => 'D158_IPV', 'label' => 'IPV', 'type' => 'moeda', 'length' => array(20, 30), 'align' => 'right', 'style' => 'float:right;'),
					'D158_Observacao' => array('name' => 'D158_Observacao', 'label' => 'Observação', 'type' => 'text', 'length' => array(64, 120)),
					'D158_Observacao_Entrega' => array('name' => 'D158_Observacao_Entrega', 'label' => 'Entrega dias', 'type' => 'text', 'length' => array(64, 120)),
					'idVeiculoPedido' => array('name' => 'idVeiculoPedido', 'label' => 'Veículo', 'type' => 'select','reject' => true,'extra'=>'style="width:190px;"',
						'select'=> gGeraSelect('D116', 'D116_Id', 'D116_Placa,D133_Modelo,D134_Cor', 'LEFT JOIN D133 on D133_Id=D116_D133_Id LEFT JOIN D134 on D134_Id=D116_D134_Id WHERE D116_D024_Id=(SELECT D157_D024_Id FROM D157 WHERE D157_Id='.@$r_D157_Id.')', true,@$resultVeiculo['D157_D116_Id']),
						'selectRefresh' => array('D116', 'D116_Id', 'D116_Placa,D133_Modelo,D134_Cor', ' LEFT JOIN D133 on D133_Id=D116_D133_Id LEFT JOIN D134 on D134_Id=D116_D134_Id WHERE D116_D024_Id=(SELECT D157_D024_Id FROM D157 WHERE D157_Id='.@$r_D157_Id.')', true,@$resultVeiculo['D157_D116_Id'])
					),
					'ven001FormVeiculosProdutoPedidoBtnNew' => array('name' => 'ven001FormVeiculosProdutoPedidoBtnNew', 'type'=>'submit', 'reload' => false, 'value' => 'Veículos', 'align' => 'right','reject' => true),
            		// Campos Hidden
					'D158_D157_Id' => array('name' => 'D158_D157_Id', 'label' => '', 'type' => 'hidden', 'value' => @$r_D157_Id),
					'D158_C004_Id' => array('name' => 'D158_C004_Id', 'label' => '', 'type' => 'hidden', 'value' => $g['empresaAtual']),
					'D158_Codigo_Produto' => array('name' => 'D158_Codigo_Produto', 'label' => '', 'type' => 'hidden'),
					'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true),
					'D158_D009_Id' => array('name' => 'D158_D009_Id', 'label' => 'D158_D009_Id', 'type' => 'hidden'),
					'UltimoPrecoVenda' => array('name' => 'UltimoPrecoVenda', 'label' => "<a href=\"#\" onclick=\"abrirJanela(event, divIdRootAberto(), gridPegarDivId(\$(this)), unique(), 'Histórico', '.: Histórico :.', '/ven/ven001/content/ven001ContentHistoricoProduto/', '&acaoId=' + encodeURIComponent(\$('#{$g['divId']} .itemForm-D158_D009_Id').val()), [900,650]); return false;\">Histórico</a>", 'type' => 'textarea', 'reject'=>true, 'readonly'=>true),
					'D001_Id' => array('name' => 'D001_Id', 'label' => 'D001_Id', 'type' => 'hidden', 'reject'=>true),
					'D158_D022_Id' => array('name' => 'D158_D022_Id', 'label' => 'Transportadora', 'type' => 'select', 'select' => 
		            	gGeraSelect('D022', 'D022_Id', 'D022_Nome_Empresa', '', true)
					),
					'D158_T208_Id' => array('name' => 'D158_T208_Id', 'label' => 'Rota', 'type' => 'select', 'select' => 
		            	gGeraSelect('T208', 'T208_Id', 'T208_Rota', '', true)
					),
					'D158_Hora_Entrega' => array('name' => 'D158_Hora_Entrega', 'label' => 'Hora Entrega', 'type' => 'tempo', 'length' => array(20, 30)),
					//'NcmProduto' => array('name' => 'NcmProduto', 'label' => 'NcmProduto','reject' => 'true', 'type' => 'hidden'),
					'D158_D006_Id' => array('name' => 'D158_D006_Id', 'label' => 'CFOP', 'restricao' => false, 'type' => 'select', 'readonly'=>true, 'extra' => "onfocus='this.initialSelect = this.selectedIndex;' onchange='this.selectedIndex = this.initialSelect;'",
					    'select' => gGeraSelect('D006','D006_Id','D006_Codigo_CFOP,D006_Descricao',' WHERE D006_Flag_Inativo!="S" AND D006_Flag_Departamento="COMERCIAL" ORDER BY D006_Codigo_CFOP DESC', true),
					),
					'D158_Flag_Destino_Produto' => array('name' => 'D158_Flag_Destino_Produto','label'=>'Destino', 'type' => 'select', 
						'select' => array(
							array('title' => '...', 'value' => "", 'selected' => true),
							array('title' => '1 - INDUSTRIALIZAÇÃO', 'value' => "1"),
							array('title' => '2 - REVENDA', 'value' => "2"),
                            array('title' => '3 - CONSUMO', 'value' => "3"),
                            array('title' => '4 - ESPECIAL', 'value' => "4"),
                            array('title' => '5 - BONIFICAÇÃO', 'value' => "5"),
							array('title' => '6 - TROCA', 'value' => "6"),
						)
					),
					'estoqueLiquido' => array('name' => 'estoqueLiquido', 'label' => "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Pedidos', '/crm/crm001/content/crm001ContentPedidosProduto/', '&acaoId=' + encodeURIComponent(\$('#{$g['divId']} .itemForm-D158_D009_Id').val()), [700,400]); return false;\">Est. Liq.</a>", 'align' => 'right', 'type' => 'moeda', 'length' => array(5,20), 'style' => 'float:right; text-align:right;', 'reject' => true, 'readonly'=>true),
					'estoqueLoja' => array('name' => 'estoqueLoja', 'label' => 'Est. Loja', 'align' => 'right', 'type' => 'moeda', 'length' => array(5,20), 'style' => 'float:right; text-align:right;', 'reject' => true, 'readonly'=>true),
					'estoqueTotal' => array('name' => 'estoqueTotal', 'label' => 'Est. Total', 'align' => 'right', 'type' => 'moeda', 'length' => array(5,20), 'style' => 'float:right; text-align:right;', 'reject' => true, 'readonly'=>true),
					'ordemCompra' => array('name' => 'ordemCompra', 'label' => "<a href=\"#\" onclick=\"abrirJanela(event, divIdRootAberto(), gridPegarDivId(\$(this)), unique(), 'Ordem de Compra', '.: Ordem de Compra :.', '/ven/ven001/content/ven001contentProdutoOC/', '&acaoId=' + encodeURIComponent(\$('#{$g['divId']} .itemForm-D158_D009_Id').val()), [700,400]); return false;\">OC</a>", 'align' => 'right', 'type' => 'moeda', 'length' => array(5,20), 'style' => 'float:right; text-align:right;', 'reject' => true, 'readonly'=>true),
					'estoqueFora' => array('name' => 'estoqueFora', 'label' => "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Estoque Fora', '/cad/cad002/content/cad002ContentEstoqueExterno/', '&acaoId=' + encodeURIComponent(\$('#{$g['divId']} .itemForm-D001_Id').val()) + '&D157_Id=' + encodeURIComponent('{$r_D157_Id}'), [700,400]); return false;\">Est. Fora</a>", 'type' => 'moeda', 'length' => array(5,20), 'reject' => true, 'readonly'=>true, 'style' => 'float:right; text-align:right;'),
					'precoTabela' => array('name' => 'precoTabela', 'label' => 'Prc. Tabela', 'type' => 'moeda', 'length' => array(5,20), 'reject' => true, 'readonly'=>true, 'style' => 'float:right; text-align:right;'),
					'enviado' => array('name' => 'enviado', 'label' => '', 'type' => 'hidden', 'length' => array(5,20), 'reject' => true, 'value' => ''),
					'D158_T208_Id' => array('name' => 'D158_T208_Id', 'label' => 'Rota', 'type' => 'select', 'select' => gGeraSelect('T208', 'T208_Id', 'T208_Rota', '')),
					'D158_D022_Id' => array('name' => 'D158_D022_Id', 'label' => 'Transportador', 'type' => 'select', 'select' => gGeraSelect('D022', 'D022_Id', 'D022_Nome_Empresa', '')),
				)
			)
		);
		// Botão Limpar / Novo Registro
		if (!empty($acaoId)) {
			$formArray['campos']['geral']['ven002FormProdutosBtnNew'] = array('name' => 'ven002FormProdutosBtnNew', 'type'=>'submit', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		}
		$formArray['campos']['geral']['ven002FormProdutosBtnAdd'] = array('name' => 'ven002FormProdutosBtnAdd', 'type'=>'button', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['geral']['ven002FormProdutosBtnCln'] = array('name' => 'ven002FormProdutosBtnCln', 'type'=>'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		if($g['C031']['permitePedidoProdutoPreCadastro'] == 'S'){
			$formArray['campos']['geral']['ven002BtnCadastrarProduto'] = array('name' => 'ven002BtnCadastrarProduto', 'type'=>'button', 'reload' => false, 'value' => 'Pré-Cadastrar Produto', 'reject' => true);	
		}
		$form->definirFormularioArray($formArray);

		echo <<<EOT
		    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		    	$('#ven002BtnCadastrarProduto').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Pré-cadastrar Produto', '/cad/cad002/content/cad002ContentPreCadastro/', '&D157_Id={$r_D157_Id}&divForm={$g['divId']}', [600,430]);
					return false;
				});
				$('#ven001FormVeiculosProdutoPedidoBtnNew').unbind('click');
				$('#ven001FormVeiculosProdutoPedidoBtnNew').unbind('click').bind('click', function(data) {
					var id = '{$resultVeiculo['D157_D024_Id']}';
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
			$D009_Id = $form->campoValorEnviado('D158_D009_Id');
			$D158_Quantidade = $form->campoValorEnviado('D158_Quantidade');
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
				// Form sem erros
			} else if ($formCodigo == 2) {
				$D158_Codigo_Produto = $form->campoValorEnviado('D158_Codigo_Produto');
                $D158Codigo = mysql_query("SELECT D158_Codigo_Produto FROM D158 WHERE D158_Codigo_Produto='{$D158_Codigo_Produto}' AND D158_D157_Id='{$D157_Id}'");
				if($g['C031']['permitirProdutosDuplicadosVenda'] == 'N' && mysql_num_rows($D158Codigo) > 0){
					echo gGeraAlertMsg('Erro', "Já existe um item com o código ".$D158_Codigo_Produto." neste pedido. Por favor, selecione outro produto.");
				} else {
					$D158_Id = false;
					$retorno = $CAD061->incluirProduto($form);
					if ($retorno !== true) {
						echo gGeraAlertMsg('Erro', $retorno);	
					} else {
						echo <<<EOT
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									//divRefresh('{$form->campoValorEnviado('divReload')}', true);
									//divRefresh('{$r_divOutro}', true);
									//divRefresh('{$g['divId']}', true);
									divRefresh('{$form->campoValorEnviado('divReload')}', true, false, false, '{$D158_Id}', 'D158_Id'); // grid
									divRefresh('{$r_divOutro}', true, false, 2); // totais
									divRefresh('{$g['divId']}');
								} </script></div>
EOT;
						// Em caso de sucesso, limpa o form (a ação padrão é manter os dados) não precisar dar: divRefresh('{$g['divId']}', true);
						$form->limpar();
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
		
		case 'cad061FormProdutosEditar':
			/**
			* PARTE 1: Variavéis envidas para o formulário
			*/
			// Variável indica qual tab do form será exibida
			$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
			// Utilizada em UPDATE, é o ID do registro que está sendo editado
			$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;

			$D157 = mysql_query("SELECT D157_D024_Id, D157_Id from D158 LEFT JOIN D157 on D157_Id=D158_D157_Id where D158_Id='{$acaoId}'");
			$mD157 = mysql_fetch_array($D157);

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
			$form_tabela = 'D158';
			$form_campo_pk = 'D158_Id';
			// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
			$enviar_btnId = 'cad061FormEditarProdutosBtn';
			// Form que será executado ao clicar no botão
			$enviar_acaoHref = '/cad/cad061/form/cad061FormProdutosEditar/';

			//form editor
			$form_editor = array(
				'item' 		 => '{"Procura_Produto_Edicao":{"top":7,"left":53,"width":408,"hidden":0},"Marca":{"top":29.933319091796875,"left":59.933349609375,"width":88,"hidden":0},"D158_D037_Id":{"top":30,"left":428,"width":51,"hidden":0},"D158_Descricao_Produto":{"top":52.933319091796875,"left":34,"width":424,"hidden":0},"D158_Quantidade":{"top":75.93331909179688,"left":28,"width":102,"hidden":0},"D158_IPV":{"top":76,"left":394,"width":102,"hidden":0},"D158_Valor_Custo_Unitario":{"top":99,"left":23,"width":102,"hidden":1},"D158_Valor_Preco_Sem_Desconto_Unitario":{"top":122,"left":16,"width":102,"hidden":0},"D158_D006_Id":{"top":145,"left":56,"width":432,"hidden":0},"D158_Observacao":{"top":168,"left":30,"width":424,"hidden":0},"D158_T208_Id":{"top":191,"left":68,"width":75,"hidden":1},"D158_D022_Id":{"top":214,"left":21,"width":212,"hidden":1}}',
				'ipi'  		 => '{"D158_D005_Id":{"top":7,"left":93,"width":123,"hidden":0},"D158_Aliquota_IPI":{"top":30,"left":99,"width":102,"hidden":0},"D158_Situacao_Tributaria_IPI":{"top":53,"left":19,"width":315,"hidden":0}}',
				'icms' 		 => '{"D158_Aliquota_ICMS":{"top":7,"left":87.9666748046875,"width":102,"hidden":0},"D158_Percentual_Reducao_ICMS":{"top":29.98333740234375,"left":70.9666748046875,"width":102,"hidden":0},"D158_D059_Id":{"top":52.98333740234375,"left":63.9666748046875,"width":288,"hidden":0},"D158_Flag_ST":{"top":75.98332214355469,"left":46.9666748046875,"width":58,"hidden":0},"D158_ST_ICMS_Interno":{"top":99.00001525878906,"left":50,"width":102,"hidden":0},"D158_ST_ICMS_Interno_MVA":{"top":99.00001525878906,"left":313.00006103515625,"width":102,"hidden":0},"D158_ICMS_Inter_Estadual":{"top":122.00001525878906,"left":18,"width":102,"hidden":0},"D158_ICMS_Inter_Estadual_MVA":{"top":122.00001525878906,"left":279.00006103515625,"width":102,"hidden":0},"D158_ST_VA":{"top":145.00001525878906,"left":130,"width":102,"hidden":0}}',
				'pis-cofins' => '{"D158_Aliquota_PIS":{"top":7,"left":109.9666748046875,"width":102,"hidden":0},"D158_Flag_Isento_PIS":{"top":29.98333740234375,"left":108.9666748046875,"width":58,"hidden":0},"D158_Situacao_Tributaria_PIS":{"top":52.98333740234375,"left":30,"width":288,"hidden":0},"D158_Aliquota_COFINS":{"top":75.98332214355469,"left":87.9666748046875,"width":102,"hidden":0},"D158_Flag_Isento_COFINS":{"top":99.00001525878906,"left":108.9666748046875,"width":58,"hidden":0},"D158_Situacao_Tributaria_COFINS":{"top":122.00001525878906,"left":8,"width":288,"hidden":0}}'
			);

		// Campo: Procura_Produto
		$pesquisaGridExtra = <<<EOT
			left join D049 on D049_D001_Id=D001_Id
			left join D041 on D041_D049_Id=D049_Id
			left join D009 on D009_D049_Id=D049_Id
			left join D082 on D082_Id=D049_D082_Id
			left join D037 on D037_Id=D001_D037_Id 
			LEFT JOIN D083 on D083_D001_Id=D001_Id AND D083_D024_Id='{$mD157['D157_D024_Id']}'
			where (D009_C004_Id = '{$g['empresaAtual']}'
			and D001_Flag_Ativo='S')
			group by D009_Id
			order by D001_Codigo_Produto ASC
EOT;
		$pesquisaGrid = array(
			'D001',																											              // tabela
			'D009_Id',																										              // chave primaria
			'D001_Codigo_Produto||D083_Codigo_Produto_Fornecedor||D001_Descricao_Produto||D082_Marca||D009_Preco_1||D009_Preco_2||D009_Preco_3||D009_Quantidade_Estoque||D009_Quantidade_Pedido(D009_Id,1) as Pedido||D009_Quantidade_Estoque_Liquido||D009_Id as D009Id||D001_Descricao_Ingles||D009_Id||D049_Id',	// campos
			$pesquisaGridExtra,																								              // extras sql
			'Pesquisa de Produto',																										  // título da janela
			'Cód.,Cód.Cli,Descrição,Marca,Preço 1,Preço 2,Preço 3,Estoque Real,Pedidos,Estoque Líq,Estoque MP,Descrição Inglês,Link,D049_Id',	  // descrição dos campos
			'cad061CampoProcuraProdutoEditar',																					          // callback js
			$g['divId'],																									              // divId
			(isset($r_ven002FormProdutosForm) ? $r_ven002FormProdutosForm['D158_D157_Id'] : $mD157['D157_Id']),					              // parametro extra
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
			),
			'D009_Id||D049_Id||D009_Preco_1||D009_Preco_2||D009_Preco_3||D009_Quantidade_Estoque||D009_Quantidade_Estoque_Liquido' // Campos que são ignorados na cláusula where do pesquisaGrid
		);

			$D158=mysql_query("select * 
			                    from D158 
		                   left join D009 on D009_Id=D158_D009_Id
		                   left join D049 on D049_Id=D009_D049_Id
		                   left join D082 on D082_Id=D049_D082_Id
			                   where D158_Id='$acaoId'");
			$mD158=mysql_fetch_array($D158);

			// novo formulário
			$form = new Formulario('ven002FormProdutosForm');
			// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
			$formArray = array(
			'campos' => array(
			'item' => array(
				'Procura_Produto_Edicao' => array('name' => 'Procura_Produto_Edicao' , 'label' => 'Produto', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGrid,'length' => array(30,30), 'reject' => true, 'value' => "{$mD158['D158_Codigo_Produto']}"),
				'D158_Codigo_Produto' => array('name' => 'D158_Codigo_Produto', 'label' => 'CódigoProd', 'type' => 'hidden', 'length' => array(15, 30)),
				'Marca' => array('name' => 'Marca', 'label' => 'Marca', 'type' => 'text', 'length' => array(64,255), 'reject' => true, 'disabled' => true, 'value' => "$mD158[D082_Marca]"),
				'D158_Descricao_Produto' => array('name' => 'D158_Descricao_Produto', 'label' => 'Descrição', 'type' => 'text', 'length' => array(64, 120)),
				'D158_Quantidade' => array('name' => 'D158_Quantidade', 'label' => 'Quantidade', 'type' => 'moeda', 'length' => array(15, 30)),
				'D158_D037_Id' => array('name' => 'D158_D037_Id', 'label' => 'Unidade', 'type' => 'select', 'align' => 'right', 'style' => 'float:right;', 'select' => 
					gGeraSelect('D037', 'D037_Id', 'D037_Unidade', '', true)
				),
				'D158_Valor_Custo_Unitario' => array('name' => 'D158_Valor_Custo_Unitario', 'label' => 'Custo Unitário', 'readonly' => 'true', 'disabled' => 'true', 'type' => 'moeda', 'length' => array(15, 30)),
				'D158_Valor_Preco_Sem_Desconto_Unitario' => array('name' => 'D158_Valor_Preco_Sem_Desconto_Unitario', 'label' => 'Preço Unitário', 'type' => 'moeda', 'length' => array(15, 30)),
				'D158_IPV' => array('name' => 'D158_IPV', 'label' => 'IPV', 'type' => 'moeda', 'length' => array(15, 30), 'align' => 'right', 'style' => 'float:right;'),
				'D158_D006_Id' => array('name' => 'D158_D006_Id', 'label' => 'CFOP', 'type' => 'select',
					'select' => gGeraSelect('D006','D006_Id','D006_Codigo_CFOP,D006_Descricao',' WHERE D006_Flag_Inativo!="S" AND D006_Flag_Departamento="COMERCIAL"  ORDER BY D006_Codigo_CFOP', true),
				),
				'D158_Observacao' => array('name' => 'D158_Observacao', 'label' => 'Observação', 'type' => 'text', 'length' => array(65, 120)),
				'D158_T208_Id' => array('name' => 'D158_T208_Id', 'label' => 'Rota', 'type' => 'select', 'select' => gGeraSelect('T208', 'T208_Id', 'T208_Rota', '')),
				'D158_D022_Id' => array('name' => 'D158_D022_Id', 'label' => 'Transportador', 'type' => 'select', 'select' => gGeraSelect('D022', 'D022_Id', 'D022_Nome_Empresa', '')),
				// Campos Hidden
				'D158_C004_Id' => array('name' => 'D158_C004_Id', 'label' => '', 'type' => 'hidden'),
				'D158_D009_Id' => array('name' => 'D158_D009_Id', 'label' => '', 'type' => 'hidden'),
				'D158_D082_Id' => array('name' => 'D158_D082_Id', 'label' => '', 'type' => 'hidden'),
				'D158_D157_Id' => array('name' => 'D158_D157_Id', 'label' => '', 'type' => 'hidden', 'value'=>$mD157['D157_Id']),
				'D158_Valor_Preco_Original' => array('name' => 'D158_Valor_Preco_Original', 'label' => '', 'type' => 'hidden'),
				'formId' => array('name' => 'formId', 'label' => '', 'type' => 'hidden', 'value' => $formId, 'reject' => true),
				'D158_T208_Id' => array('name' => 'D158_T208_Id', 'label' => 'Rota', 'type' => 'select', 'select' => gGeraSelect('T208', 'T208_Id', 'T208_Rota', '')),
				'D158_D022_Id' => array('name' => 'D158_D022_Id', 'label' => 'Transportador', 'type' => 'select', 'select' => gGeraSelect('D022', 'D022_Id', 'D022_Nome_Empresa', '')),
			),
			//TAB IPI
			'ipi' => array(
			    'D158_D005_Id' => array('name' => 'D158_D005_Id', 'label' => 'NCM', 'type' => 'select',  'select' => 
			        gGeraSelect('D149', 'D005_Id', 'D005_Classificacao_Fiscal, D005_Flag_Aplicacao, D149_Aliquota_IPI', "LEFT JOIN D005 ON D149_D005_Id=D005_Id AND D149_C004_Id='{$g['empresaAtual']}' WHERE D005_Flag_Ativo='S' ORDER BY D005_Classificacao_Fiscal, D005_Observacao", true)
			    ),
			    'D158_Aliquota_IPI' => array('name' => 'D158_Aliquota_IPI', 'label' => 'IPI %', 'type' => 'moeda', 'length' => array(15, 20)),
			    'D158_Situacao_Tributaria_IPI' => array('name' => 'D158_Situacao_Tributaria_IPI', 'label' => 'Situação Tributária IPI', 'type' => 'select',  'select' => 
			        gGeraSelect('D088', 'D088_Codigo_ST', 'D088_Codigo_ST,D088_Descricao', '', true)
			    ),
			),
			//TAB ICMS
			'icms' => array(
			    'D158_Aliquota_ICMS' => array('name' => 'D158_Aliquota_ICMS', 'label' => 'Aliquota ICMS', 'type' => 'moeda', 'length' => array(15, 20)),
			    'D158_Percentual_Reducao_ICMS' => array('name' => 'D158_Percentual_Reducao_ICMS', 'label' => '% Reducao base', 'type' => 'moeda', 'length' => array(15, 20)),
			    'D158_D059_Id' => array('name' => 'D158_D059_Id', 'label' => 'Situação Tributária', 'type' => 'select', 'extra'=>'style="width:288px;"',  'select' => 
			        gGeraSelect('D059', 'D059_Id', 'D059_Codigo_ST,D059_Descricao_Origem', '', true)
			    ),
			    'D158_Flag_ST' => array('name' => 'D158_Flag_ST', 'label' => 'Substituição Tributária', 'type' => 'select', 'select' => 
			        array(
			            array('title' => 'Sim', 'value' => 'S'),
			            array('title' => 'Não', 'value' => 'N', 'selected' => true)
			        )
			    ),
			    'D158_ST_ICMS_Interno' => array('name' => 'D158_ST_ICMS_Interno', 'label' => 'Aliquota ICMS Interno', 'type' => 'moeda', 'length' => array(15, 20)),
				'D158_ST_ICMS_Interno_MVA' => array('name' => 'D158_ST_ICMS_Interno_MVA', 'label' => 'Aliquota ICMS Interno MVA', 'type' => 'moeda', 'length' => array(15, 20), 'align'=>'right', 'style'=>'float:right'),
			    'D158_ICMS_Inter_Estadual' => array('name' => 'D158_ICMS_Inter_Estadual', 'label' => 'Aliquota ICMS Inter Estadual', 'type' => 'moeda', 'length' => array(15, 20)),
				'D158_ICMS_Inter_Estadual_MVA' => array('name' => 'D158_ICMS_Inter_Estadual_MVA', 'label' => 'Aliquitoa ICMS Inter Estadual MVA', 'type' => 'moeda', 'length' => array(15, 20), 'align'=>'right', 'style'=>'float:right'),
			    'D158_ST_VA' => array('name' => 'D158_ST_VA', 'label' => 'MVA', 'type' => 'moeda', 'length' => array(15, 20)),
			    
			),
			//TAB PIS/COFINS
			'pis-cofins' => array(
			    'D158_Aliquota_PIS' => array('name' => 'D158_Aliquota_PIS', 'label' => 'PIS %', 'type' => 'moeda', 'length' => array(15, 20)),
			    'D158_Flag_Isento_PIS' => array('name' => 'D158_Flag_Isento_PIS', 'label' => 'Isento', 'type' => 'select', 'select' => 
			        array(
			            array('title' => 'Sim', 'value' => 'S'),
			            array('title' => 'Não', 'value' => 'N', 'selected' => true)
			        )
			    ),
			    'D158_Situacao_Tributaria_PIS' => array('name' => 'D158_Situacao_Tributaria_PIS', 'label' => 'Situação Tributária PIS', 'extra'=>'style="width:288px;"', 'type' => 'select', 'select' =>
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

			    'D158_Aliquota_COFINS' => array('name' => 'D158_Aliquota_COFINS', 'label' => 'COFINS %', 'type' => 'moeda', 'length' => array(15, 20)),

			    'D158_Flag_Isento_COFINS' => array('name' => 'D158_Flag_Isento_COFINS', 'label' => 'Isento', 'type' => 'select', 'select' => 
			        array(
			            array('title' => 'Sim', 'value' => 'S'),
			            array('title' => 'Não', 'value' => 'N', 'selected' => true)
			        )
			    ),
			    'D158_Situacao_Tributaria_COFINS' => array('name' => 'D158_Situacao_Tributaria_COFINS', 'label' => 'Situação Tributária COFINS', 'extra'=>'style="width:288px;"', 'type' => 'select', 'select' =>
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
			// Botão Limpar / Novo Registro
			if (!empty($acaoId)) {
				$formArray['campos']['produto']['ven002FormProdutosBtnNew'] = array('name' => 'ven002FormProdutosBtnNew', 'type'=>'submit', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
			}
			$formArray['campos']['produto']['ven002FormProdutosBtnCln'] = array('name' => 'ven002FormProdutosBtnCln', 'type'=>'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
			$formArray['campos']['produto']['ven002FormProdutosBtnAdd'] = array('name' => 'ven002FormProdutosBtnAdd', 'type'=>'button', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
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
                                // $D158_D059_Id = $form->campoValorEnviado('D158_D059_Id');
                                // $sqlD059 = mysql_query("SELECT D059_Codigo_ST FROM D059 WHERE D059_Id='{$D158_D059_Id}'");
                                // $rowSql = mysql_fetch_array($sqlD059);
                                // mysql_query("UPDATE D158 SET D158_Flag_Situacao_Tributaria='0{$rowSql['D059_Codigo_ST']}' WHERE D158_Id='{$acaoId}'");                                
                                
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

		case 'cad061FormPagamento':
			//$acaoId = (isset($r_acaoId)) ? $r_acaoId : false; 
			$D157_Id = (isset($r_D157_Id)) ? $r_D157_Id : false;
			  
			//echo $D157_Id;
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
			$form_tabela = 'D159';
			$form_campo_pk = 'D159_Id';
            $form_parent = 'D157_Id';
            $form_parent_valor = $D157_Id;
			// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
			$enviar_btnId = 'cad061FormPagamento_btn';
			// Form que será executado ao clicar no botão
			$enviar_acaoHref = '/cad/cad061/form/cad061FormPagamento/';
            $form_editor = array(
                'geral' => '{"usarFreteParcela":{"top":7,"left":271,"width":59,"hidden":1},"Total_Parcelas":{"top":7,"left":31,"width":176,"hidden":0},"usarSTParcela":{"top":30,"left":283,"width":59,"hidden":1},"selecionaPrimeiroVencimento":{"top":30,"left":33,"width":105,"hidden":0},"primeiroVencimentoData":{"top":30,"left":192,"width":56,"hidden":0},"primeiroVencimentoNum":{"top":30,"left":208,"width":40,"hidden":0},"usarIPIParcela":{"top":53,"left":284,"width":59,"hidden":1},"selecionaDias":{"top":53,"left":21,"width":105,"hidden":0},"IntervalorDias":{"top":53,"left":208,"width":39,"hidden":0},"diaFixo":{"top":53,"left":208,"width":40,"hidden":0},"ven002FormPagamento_btn":{"top":75,"left":300,"width":null,"hidden":0},"Portador_D027_Id":{"top":76,"left":32,"width":176,"hidden":0},"ven002FormAdicionarParcela_btn":{"top":51,"left":624,"width":null,"hidden":1},"D157_Observacao_Pagamento":{"top":53,"left":781,"height":37,"width":168,"hidden":1},"ven002SalvarObservacao_btn":{"top":74,"left":624,"width":null,"hidden":1}}',
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
			$D157=mysql_query("select * from D157 where D157_Id='$D157_Id'");
			$mD157=mysql_fetch_array($D157);
			
			//SELECIONANDO OS SERVIÇOS DESSE ORÇAMENTO PARA CALCULAR O TOTAL
			$sql = "SELECT T147_Valor_Total FROM T147 where T147_D157_Id='{$D157_Id}'";
			$T147_sql = mysql_query($sql);
			
			$totalServicos=0;
			// LOOPING PARA CALCULAR O TOTAL DOS SERVIÇOS SOMADO COM TOTAL DO PEDIDO
			while($T147 = mysql_fetch_array($T147_sql)){
				$totalServicos+=$T147['T147_Valor_Total'];
			}
			
			//SOMANDO O TOTAL DO ORÇAMENTO COM O TOTAL DOS SERVIÇOS
			$valorTotalPedido = $totalServicos + $mD157['D157_Valor_Total'];
						
			for ($Parcela=1; $Parcela<=24; $Parcela++) {
				$valorParcela=$CAD061->calcularParcelaCombo($Parcela,$valorTotalPedido,$tipoJuros,$taxaJuros,$parcelasIsentas);				
				if ($Parcela>$parcelasIsentas){
					$valorTotal=gCorrigeNumero($Parcela*gCorrigeNumeroInverte($valorParcela));
					$valorParcela.="*";
				}else{
					$valorTotal=gCorrigeNumero($valorTotalPedido);
				}
				if($Parcela=='1' || $Parcela == $mD157['D157_Pgto_Parcelas']){
					$select[] = array('value' => $Parcela, 'title' => str_pad($Parcela,2,"0",STR_PAD_LEFT) . " x" . " - " .$valorParcela." = ".gCorrigeNumero($valorTotalPedido), 'selected'=>true);
				}else{
					$select[] = array('value' => $Parcela, 'title' => str_pad($Parcela,2,"0",STR_PAD_LEFT) . " x" . " - " .$valorParcela." = ".gCorrigeNumero($valorTotalPedido));
				}
			}
			
			if(($mD157['D157_Pgto_Primeiro_Vcto_Valor'] != "") || (!empty($mD157['D157_Pgto_Primeiro_Vcto_Valor']))){
				$primeiroVctoValor = $mD157['D157_Pgto_Primeiro_Vcto_Valor'];
			}else{
				$primeiroVctoValor = $g['conf']->camposValorPadrao("primeiroVencimentoNum");
			}
			if(($mD157['D157_Pgto_Dias_Vcto_Valor'] != "") || (!empty($mD157['D157_Pgto_Dias_Vcto_Valor']))){
				$diaVctoValor = $mD157['D157_Pgto_Dias_Vcto_Valor'];
			}else{
				$diaVctoValor = $g['conf']->camposValorPadrao("IntervalorDias");
			}
			if(($mD157['D157_Pgto_Data'] != "") || (!empty($mD157['D157_Pgto_Data']))){
				$pgtoData = $mD157['D157_Pgto_Data'];
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
							array('title' => 'Dias', 'value' => 'Dias', 'selected' => ($mD157['D157_Pgto_Primeiro_Vcto_Tipo'] == "Dias") ? true : false),
							array('title' => 'Data', 'value' => 'Data', 'selected' => ($mD157['D157_Pgto_Primeiro_Vcto_Tipo'] == "Data") ? true : false),
						)),
						'primeiroVencimentoNum' => array('name' => 'primeiroVencimentoNum', 'label' => '', 'value'=> $primeiroVctoValor, 'type' => 'text', 'reject' => true, 'length' => array(3,6), 'align' => 'right'),
						'primeiroVencimentoData' => array('name' => 'primeiroVencimentoData', 'label' => '', 'value'=> $primeiroVctoValor, 'type' => 'data', 'reject' => true, 'align' => 'right'),
						'selecionaDias' => array('name' => 'selecionaDias', 'label'=>'Dias Venc.', 'type' => 'select', 'reject'=>true, 'select' => 
						array(
							array('title' => 'Intervalo', 'value' => 'I', 'selected' => ($mD157['D157_Pgto_Dias_Vcto_Tipo'] == "Intervalo") ? true : false),
							array('title' => 'Dia Fixo', 'value' => 'F', 'selected' => ($mD157['D157_Pgto_Dias_Vcto_Tipo'] == "Dia Fixo") ? true : false),
						)),			
						'IntervalorDias' => array('name' => 'IntervalorDias', 'label' => '', 'type' => 'text', 'value'=>$diaVctoValor, 'reject' => true, 'length' => array(3,6), 'align' => 'right'),
						'diaFixo' => array('name' => 'diaFixo', 'label' => '', 'type' => 'text', 'reject' => true, 'value'=>$diaVctoValor, 'length' => array(3,2), 'align' => 'right'),												
						'Portador_D027_Id' => array('name' => 'D027_Id','label'=>'Portador', 'reject'=>true, 'type' => 'select', 'select' =>
							gGeraSelect('D027', 'D027_Id', 'D027_Portador','LEFT JOIN D007 ON D007_Id = D027_D007_Id LEFT JOIN C004 ON C004_Id = D007_C004_Id WHERE C004_Cnpj LIKE "%'.$CNPJ_Empresa_Corrente.'%" OR ifnull(D027_D007_Id,0) = 0 order by D027_Portador', true, $mD157['D157_Pgto_Portador'])
						),
						'cad061FormPagamento_btn' => array('name' => 'cad061FormPagamento_btn', 'type'=>'submit','reload'=>false,'value'=>'Gerar Parcelas','align' => 'right', 'reject'=>true),
						'ven002FormAdicionarParcela_btn' => array('name' => 'ven002FormAdicionarParcela_btn', 'type'=>'submit', 'reload' => false, 'value' => 'Adicionar Parcela', 'align' => 'right', 'reject' => true),
						'D157_Observacao_Pagamento' => array('name' => 'D157_Observacao_Pagamento', 'label' => 'Observação', 'type' => 'textarea', 'length' => array(1,23),'reject' => true),
						'ven002SalvarObservacao_btn' => array('name' => 'ven002SalvarObservacao_btn', 'type'=>'submit', 'reload' => false, 'value' => 'Salvar Observação', 'align' => 'right', 'reject' => true),
						'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true),
						'D157_Id' => array('name' => 'D157_Id', 'label' => '', 'type' => 'hidden', 'value' => @$r_D157_Id, 'reject' => true),
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

			$formArray['campos']['geral']['usarSTParcela'] = array('name' => 'usarSTParcela', 'label'=>'ST primeira parcela', 'reject'=>true, 'type' => 'select','select' => 
																	array(
																		array('title' => '...', 'value' => ''),
																		array('title' => 'Sim', 'value' => 'on'),
																		array('title' => 'Não', 'value' => 'off'),
																	)
																);
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
					var id = '{$row['D157_D024_Id']}';
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
					$.getJSON('/ven/ven002/form_func-ajax/salvarObservacao/?ajax=true=' + '&' + $("#{$formId}").serialize() + '&D157_Id=' + encodeURIComponent('{$D157_Id}') + '&callback=?', function(request) {
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
					// Gerar Boleto			
					$retorno = $CAD061->atualizarParcelas($form);
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
		$gerado = gGeraCamposForm($form, $tab, $r_acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk, false, $form_parent, $form_parent_valor);


		/**
		 * PARTE 5: Exibir o formulário
		 */
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');

		$sqlD024 = mysql_query("SELECT D024_Instrucao_Pagamento FROM D024 LEFT JOIN D157 ON D157_D024_Id = D024_Id WHERE D157_Id = '{$D157_Id}'");
		$D024 = mysql_fetch_array($sqlD024);
		echo <<<EOT
			<div style="border: 1px solid silver; margin: 10px 15px 0 450px; padding: 5px; height: 110px; border-radius:5px; color: red; overflow: auto;">
				<span style="font-weight: bold; color: #000;">Informação de Pagamento</span> {$D024['D024_Instrucao_Pagamento']}	
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
						if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
							// Gravar o arquivo no disco
							$pathinfo = pathinfo($arquivo['name']);
							$pathinfo['extension'] = empty($pathinfo['extension']) ? '.tmp' : $pathinfo['extension'];
							//$dir = "{$g['pathDados']}documentos/T005_".str_repeat('0',5-strlen($form->campoValorEnviado('T167_T005_Id'))).$form->campoValorEnviado('T167_T005_Id');
							$dir = "{$g['pathDados']}pedido";
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
	
	
    case 'ven002FormAplicarDescontoPercentual':
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
		$enviar_btnId = 'ven002AplicarDescontoBtnExecutar';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/ven/ven002/form/ven002FormAplicarDescontoPercentual/';

		//form editor
		$form_editor = array('geral' =>'{"Desconto":{"top":4,"left":18,"width":175},"CampoAplicarDesconto":{"top":26,"left":4,"width":177}}');
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					//'T005_Id' => array('name' => 'T005_Id', 'label' => 'T005_Id', 'type' => 'hidden', 'length' => array(40, 80), 'reject' => true, 'value' => $r_T005_Id),
     				'Desconto' => array('name' => 'Desconto', 'label' => '% Desconto', 'type' => 'moeda', 'length' => array(30, 80), 'reject' => true, 'casasDecimais' => 5),
     				'CampoAplicarDesconto' => array('name' => 'CampoAplicarDesconto', 'label' => 'Desconto base', 'type'=>'select', 'reject' => true, 'select' => 
						array(
							array('title' => 'Preço tabela', 'value' => 'tabela','selected'=>true),
							array('title' => 'Preço pedido', 'value' => 'pedido'),
						),
					),
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
							var CampoAplicarDesconto = $('#{$g['divId']} #CampoAplicarDesconto').val();
							var Desconto = $('#{$g['divId']} #Desconto').val();
							$('#{$g['divId']}').showLoading();
							$.getJSON('/ven/ven002/form_func-ajax/ven002aplicarDesconto/?ajax=true&T005_Id=' + encodeURIComponent('{$r_T005_Id}') + '&CampoAplicarDesconto=' + encodeURIComponent(CampoAplicarDesconto) + '&Desconto=' + encodeURIComponent(Desconto) + '&callback=?', function(request) {
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
		$botoesDir .= '<button type="submit" id="ven002AplicarDescontoBtnExecutar">Executar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
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

    case 'cad061FormIncluirPedido':
    	global $g;
    	/**
			* PARTE 1: Variavéis envidas para o formulário
			*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$r_D157_Id = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;

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
			$sql = "SELECT D157_D024_Id,D020_D022_Id,D022_D036_Id, D024_D022_Id FROM D157 LEFT JOIN D024 ON D024_Id=D157_D024_Id LEFT JOIN D020 ON D020_Id=D024_D020_Id LEFT JOIN D022 ON D022_Id=D020_D022_Id LEFT JOIN D036 ON D036_Id=D022_D036_Id WHERE D157_Id = '{$acaoId}'";
			$result = mysql_query($sql);
			$aD024 = mysql_fetch_assoc($result);		
			$idCliente=$aD024['D157_D024_Id'];
			$idTransportadora=$aD024['D024_D022_Id'];
            $idTipoTransp=$aD024['D022_D036_Id'];
		} 		
		$idTipoTransp=isset($idTipoTransp) ? $idTipoTransp : false;

		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'D157';
		$form_campo_pk = 'D157_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'ven002FormCabecalhoBtnAdd';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad061/form/cad061FormIncluirPedido/';
		
		$form_editor = array(
			'geral'=>'{"D157_D024_Id":{"top":7,"left":89,"width":267,"hidden":0},"D157_D013_Id":{"top":7,"left":532,"width":239,"hidden":0},"ven002FormContatos":{"top":5,"left":832,"width":null,"hidden":0},"D157_D006_Id":{"top":7,"left":987,"width":288,"hidden":1},"D157_C007_Id_Vendedor_Interno":{"top":30,"left":75,"width":288,"hidden":0},"D157_Data_Emissao":{"top":30,"left":528,"width":232,"hidden":0},"D157_C007_Id_Vendedor_Externo":{"top":53,"left":84,"width":287,"hidden":0},"D157_D021_Id":{"top":53,"left":498,"width":240,"hidden":0},"D157_Flag_ACP":{"top":76,"left":42,"width":288,"hidden":0},"D157_Flag_Lista_Preco":{"top":76,"left":515,"width":240,"hidden":0},"D157_Data_Entrega":{"top":99,"left":77,"width":280,"hidden":0},"D157_Flag_Destino_Produto":{"top":99,"left":526,"width":240,"hidden":0},"D157_Percentual_Comissao":{"top":99,"left":1068,"width":104,"hidden":1},"D157_D148_Id_Entrega":{"top":122,"left":34,"width":287,"hidden":0},"D157_D148_Id_Cobranca":{"top":122,"left":472,"width":239,"hidden":0},"D157_Pedido_Ecommerce":{"top":122,"left":992,"width":128,"hidden":1},"tituloOC":{"top":144,"left":394.984375,"width":null,"hidden":1},"D157_Custo_Frete":{"top":145,"left":1088,"width":88,"hidden":1},"D157_Ordem_Compra":{"top":168,"left":31,"width":280,"hidden":0},"D157_Codigo_Rastreio":{"top":168,"left":989,"width":128,"hidden":1},"tituloObservacoes":{"top":184,"left":405.984375,"width":null,"hidden":1},"D157_Nome_Transportadora":{"top":191,"left":1010,"width":128,"hidden":1},"D157_Observacao":{"top":214,"left":89,"height":59,"width":280,"hidden":0},"D157_Observacao_Comercial":{"top":214,"left":436,"height":59,"width":280,"hidden":0},"D157_Flag_Dividido":{"top":214,"left":1060,"width":58,"hidden":1},"D157_Prazo_Entrega":{"top":237,"left":965,"width":280,"hidden":1},"D157_Quantidade_Volumes":{"top":260,"left":1161,"width":127,"hidden":1},"D157_Observacao_Nota_Fiscal":{"top":283,"left":67,"height":59,"width":280,"hidden":0},"D157_Observacao_1":{"top":283,"left":459,"height":59,"width":280,"hidden":0},"D157_Especie":{"top":283,"left":1139,"width":127,"hidden":1},"D157_Flag_Expedicao":{"top":306,"left":1143,"width":103,"hidden":1},"D157_D036_Id":{"top":328.96875,"left":1125,"width":128,"hidden":1},"tituloTransporte":{"top":349.984375,"left":405.984375,"width":null,"hidden":1},"D157_Peso_Bruto":{"top":351.96875,"left":1076,"width":127,"hidden":1},"D157_Flag_Frete":{"top":374.984375,"left":96,"width":288,"hidden":0},"D157_Valor_Frete":{"top":374.984375,"left":628,"width":120,"hidden":0},"D157_C007_Id_Embalagem":{"top":374.96875,"left":1095,"width":104,"hidden":1},"D157_D022_Id":{"top":397.984375,"left":39,"width":267,"hidden":0},"D157_Flag_Totalizar_Peso":{"top":398,"left":676,"width":64,"hidden":0},"ven002FormCabecalhoBtnCln":{"top":419,"left":656,"width":null,"hidden":1},"ven002FormCabecalhoBtnAdd":{"top":419,"left":752,"width":null,"hidden":0},"D157_Peso_Liquido":{"top":420.96875,"left":1020,"width":127,"hidden":1},"TransportadoraPadrao":{"top":443.984375,"left":8,"width":288,"hidden":1},"button":{"top":441.984375,"left":431.984375,"width":null,"hidden":1},"D157_D116_Id":{"top":466.984375,"left":1110,"width":42,"hidden":1}}'
		);

	
		$vendedorInternoRead = true;
		$vendedorInternoExtra = "onfocus='this.initialSelect = this.selectedIndex;' onchange='this.selectedIndex = this.initialSelect;'";
		if($acaoId > 0){		
			$sqlC007 = mysql_query("SELECT C007_Flag_Ausente FROM D157 LEFT JOIN D024 ON D024_Id=D157_D024_Id LEFT JOIN C007 ON C007_Id=D024_C007_Vendedor_Interno WHERE D157_Id = '{$acaoId}'");
			$rowC007 = mysql_fetch_array($sqlC007);
			if(($rowC007['C007_Flag_Ausente'] == 'S' && $g['C031']['alterarVendedorAusente'] == 'S') || ($g['nivelAlcada']==4) || $g['C031']['alterarVendedor'] == 'S'){
				$vendedorInternoRead = false;
				$vendedorInternoExtra = "";
			}
		}

		$vendedorExternoRead = true;
		$vendedorExternoExtra = "onfocus='this.initialSelect = this.selectedIndex;' onchange='this.selectedIndex = this.initialSelect;'";
		if($acaoId > 0){		
			$sqlC007 = mysql_query("SELECT C007_Flag_Ausente FROM D157 LEFT JOIN D024 ON D024_Id=D157_D024_Id LEFT JOIN C007 ON C007_Id=D024_C007_Vendedor_Externo WHERE D157_Id = '{$acaoId}'");
			$rowC007 = mysql_fetch_array($sqlC007);
			if(($rowC007['C007_Flag_Ausente'] == 'S' && $g['C031']['alterarVendedorAusente'] == 'S') || ($g['nivelAlcada']==4) || $g['C031']['alterarVendedor'] == 'S'){
				$vendedorExternoRead = false;
				$vendedorExternoExtra = "";
			}
		}

		// novo formulário
		$form = new Formulario('form_ven002_form1');

		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D157_D024_Id' => array('name' => 'D157_D024_Id','label'=>'Cliente', 'type' => 'pesquisa','extra' => "onchange=\"carregaCFOP_Contatos(this, '{$g['divId']}', '', '{$g['C031']['alterarVendedorAusente']}', '{$g['C031']['alterarVendedor']}'); carregaListaPreco(this, '{$g['divId']}');\"", 'pesquisa' => array('D024','D024_Id','D024_Id,D024_Nome_Empresa,D024_Nome_Fantasia,D020_Nome_Cidade,D018_UF,D024_Cpf,D024_Cnpj','left join D020 on D020_Id=D024_D020_Id left join D018 on D018_Id=D024_D018_Id WHERE D024_Flag_Cliente="S" and D024_Flag_Ativo="S" order by D024_Nome_Empresa','(%1) %2 - %3 [%4 - %5]'), 'length' => array(50,30)),
					'D157_D013_Id' => array('name' => 'D157_D013_Id', 'label' => 'Contato', 'type' => 'select',
						'select' => gGeraSelect('D013','D013_Id','D013_Nome_Contato',"where D013_D024_Id=$idCliente AND D013_Flag_Ativo = 'S'",true),
						'selectRefresh' => array('D013','D013_Id','D013_Nome_Contato,D013_DDD_Telefone_1,D013_Telefone_1',"where D013_D024_Id=$idCliente AND D013_Flag_Ativo = 'S'",true),
					),
					'ven002FormContatos' => array('name' => 'ven002FormContatos', 'type'=>'button', 'reload' => false, 'value' => 'Incluir', 'reject' => true),
					'D157_D006_Id' => array('name' => 'D157_D006_Id', 'label'=>'CFOP','type' => 'select', 'select' => 
        						gGeraSelect('D006','D006_Id','D006_Codigo_CFOP,D006_Descricao',' WHERE D006_Flag_Inativo!="S" AND D006_Flag_Departamento="COMERCIAL"  ORDER BY D006_Codigo_CFOP',true),
					),											
					'D157_Data_Emissao' => array('name' => 'D157_Data_Emissao','label'=>'Emissão','type' => 'data', 'length' => array(20,10), 'value'=>date("d/m/Y")),
					'D157_C007_Id_Vendedor_Interno' => array('name' => 'D157_C007_Id_Vendedor_Interno', 'label'=>'Vendedor','type' => 'select', 'extra' => $vendedorInternoExtra, 'readonly' => $vendedorInternoRead, 'select' => 
							gGeraSelect('C007','C007_Id','IF(C007_Ativo != "N"[[virgula]] C007_Primeiro_Nome[[virgula]] CONCAT(C007_Primeiro_Nome[[virgula]]" (INATIVO)")) as C007_Primeiro_Nome','WHERE C007_Flag_Vendedor!="" AND (C007_Ativo != "N" OR C007_Id = "'.$idVendedorInterno.'") ORDER BY C007_Primeiro_Nome',true),
					),
					'D157_C007_Id_Vendedor_Externo' => array('name' => 'D157_C007_Id_Vendedor_Externo', 'label'=>'Externo','type' => 'select', 'extra' => $vendedorExternoExtra, 'readonly' => $vendedorExternoRead, 'select' => 
							gGeraSelect('C007','C007_Id','IF(C007_Ativo != "N"[[virgula]] C007_Primeiro_Nome[[virgula]] CONCAT(C007_Primeiro_Nome[[virgula]]" (INATIVO)")) as C007_Primeiro_Nome','WHERE C007_Flag_Vendedor!="" AND (C007_Ativo != "N" OR C007_Id = "'.$idVendedorExterno.'") ORDER BY C007_Primeiro_Nome',true),
					),
					'D157_D021_Id' => array('name' => 'D157_D021_Id', 'label'=>'Representante','type' => 'select', 'select' => 
								gGeraSelect('D021','D021_Id','D021_Nome_Empresa','',true),
					),
					'D157_D148_Id_Entrega' => array('name' => 'D157_D148_Id_Entrega', 'label' => 'Endereço Entrega', 'type' => 'select', 'selectRefresh' => array('D148','D148_Id','D148_Nome','WHERE D148_D024_Id='.$idCliente.' ORDER BY D148_Id',true),
						'select' => $arrayEndereco
					),
					'D157_D148_Id_Cobranca' => array('name' => 'D157_D148_Id_Cobranca', 'label' => 'Endereço Cobrança', 'type' => 'select', 'selectRefresh' => array('D148','D148_Id','D148_Nome','WHERE D148_D024_Id='.$idCliente.' ORDER BY D148_Id',true),
						'select' => $arrayEndereco
					),	
					'D157_Percentual_Comissao' => array('name' => 'D157_Percentual_Comissao', 'label'=>'Comissão %','value'=>'0,00','type' => 'moeda', 'length' => array(20,10)),
					'D157_Percentual_Comissao_Representante' => array('name' => 'D157_Percentual_Comissao_Representante', 'label'=>'Comissão Rep. %','value'=>'0,00','type' => 'moeda', 'length' => array(20,10)),
					'D157_Flag_ACP' => array('name' => 'D157_Flag_ACP', 'label'=>'Tipo Documento','type' => 'select','select' => 
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
					'D157_Prazo_Entrega' => array('name' => 'D157_Prazo_Entrega', 'label'=>'Prazo de Entrega','type' => 'text', 'length' => array(53,30)),
					'D157_Data_Entrega' => array('name' => 'D157_Data_Entrega', 'label'=>'Entrega','type' => 'data'),
					'D157_Flag_Primeira_Liberacao_Credito' => array('name' => 'D157_Flag_Primeira_Liberacao_Credito', 'label'=>'D157_Flag_Primeira_Liberacao_Credito','type' => 'hidden', 'value' => '2'),
					'D157_D116_Id' => array('name' => 'D157_D116_Id', 'label' => 'Veículo','type' => 'select',
						'select'=> gGeraSelect('D116', 'D116_Id', 'D116_Placa,D133_Modelo,D134_Cor', "LEFT JOIN D133 on D133_Id=D116_D133_Id LEFT JOIN D134 on D134_Id=D116_D134_Id WHERE D116_D024_Id=$idCliente", true),
						'selectRefresh' => array('D116', 'D116_Id', 'D116_Placa,D133_Modelo,D134_Cor', "LEFT JOIN D133 on D133_Id=D116_D133_Id LEFT JOIN D134 on D134_Id=D116_D134_Id WHERE D116_D024_Id=$idCliente", true)
					),
					'D157_Flag_Lista_Preco' => 	array('name' => 'D157_Flag_Lista_Preco', 'label' => 'Lista Preço', 'type' => 'select', 'select' => 
						array(
							array('title' => 'Preço 1', 'value' => '1'),   
							array('title' => 'Preço 2', 'value' => '2'),   
							array('title' => 'Preço 3', 'value' => '3'),
							array('title' => 'Preço 4', 'value' => '4'),
							array('title' => 'Preço 5', 'value' => '5'),
						)
					),
					'ven002FormVeiculos' => array('name' => 'ven002FormVeiculos', 'type'=>'button', 'reload' => false, 'value' => 'Incluir', 'reject' => true),
					//'tituloOC' => array('name' => 'tituloOC', 'label'=>'Ordem de Compra','type' => 'titulo', 'reject' => true),
					'D157_Ordem_Compra' => array('name' => 'D157_Ordem_Compra', 'label'=>'Ordem de Compra','type' => 'text', 'length' => array(53,30)),
					//'tituloObservacoes' => array('name' => 'tituloObservacoes', 'label'=>'Observações','type' => 'titulo', 'reject' => true),
					'D157_Observacao' => array('name' => 'D157_Observacao', 'label'=>'Cliente','type' => 'textarea','extra' => 'title="Esta observação será visualizada pelo cliente (Não será impressa na DANFE)"', 'length' => array(3,50)),
					'D157_Observacao_Comercial' => array('name' => 'D157_Observacao_Comercial', 'label'=>'Comercial Interna','type' => 'textarea','extra' => 'title="Observação de uso interno da área comercial (Não será impressa na DANFE)"', 'length' => array(3,50)),
					'D157_Observacao_Nota_Fiscal' => array('name' => 'D157_Observacao_Nota_Fiscal', 'label'=>'Nota Fiscal','type' => 'textarea','extra' => 'title="Esta observação será impressa na DANFE"', 'length' => array(3,50)),
					'D157_Observacao_1' => array('name' => 'D157_Observacao_1', 'label'=>'Almox.:','type' => 'textarea','extra' => 'title="Observação de uso interno da área de estoque (Não será impressa na DANFE)"', 'length' => array(3,50)),
					//'tituloTransporte' => array('name' => 'tituloTransporte', 'label'=>'Transporte','type' => 'titulo', 'reject' => true),
					'D157_Flag_Frete' => array('name' => 'D157_Flag_Frete','label'=>'Frete', 'type' => 'select',
						'select' => array(
							array('title' => '0-EMITENTE', 'value' => '0'),
							array('title' => '1-DESTINATÁRIO', 'value' => '1'),
							array('title' => '2-TERCEIROS', 'value' => '2'),
							array('title' => '9-SEM FRETE', 'value' => '9'),
						),
					),
					'D157_Valor_Frete' => array('name' => 'D157_Valor_Frete', 'label'=>'Valor Frete','value'=>'0,00','type' => 'moeda', 'length' => array(20,20)),
					'D157_D022_Id' => array('name' => 'D157_D022_Id', 'label' => 'Transportadora', 'type' => 'pesquisa', 'pesquisa' => array('D022','D022_Id','D022_Nome_Empresa,D020_Nome_Cidade,D018_UF','left join D020 on D020_Id=D022_D020_Id left join D018 on D018_Id=D022_D018_Id'),'extra' => "onchange=\"carregaTipoTransp(this, '{$g['divId']}')\"", 'length' => array(50,90)),
					'D157_D036_Id' => array('name' => 'D157_D036_Id', 'label' => 'Tipo', 'type' => 'select', 'select' =>
							gGeraSelect('D036','D036_Id','D036_Tipo_Transporte','',true),
					),
					'TransportadoraPadrao' => array('name' => 'TransportadoraPadrao', 'label' => 'Transportadora Padrão', 'type' => 'select','reject' =>true ,'disabled' => true,
						'select' => gGeraSelect('D022','D022_Id','D022_Nome_Empresa,D020_Nome_Cidade,D018_UF',"left join D020 on D020_Id=D022_D020_Id left join D018 on D018_Id=D022_D018_Id",false), 
					),
					'button' => array('name' => 'button', 'type' => 'button','value' =>'Usar Padrão','reload' => false,'extra' => "onclick='copiaTransportadora({$idTipoTransp})'",'reject'=>true),
					'nada' => array('name' => 'nada', 'label' => 'Transportadora', 'type' => 'hidden', 'reject'=>true, 'length' => array(30,30)),
					'D157_Flag_Revenda_Consumidor' => array('name' => 'D157_Flag_Revenda_Consumidor', 'label'=>'D157_Flag_Revenda_Consumidor','type' => 'hidden'
						//'select' => 
							//array(
								//array('title' => '...', 'value' => 0, 'selected' => true),
								//array('title' => 'Revenda', 'value' => 1),
								//array('title' => 'Consumidor atacado', 'value' => 2),
								//array('title' => 'Consumidor varejo', 'value' => 3),
							//)
					),
					'D157_Quantidade_Volumes' => array('name' => 'D157_Quantidade_Volumes', 'label'=>'Volumes','type' => 'text'),
					'D157_C004_Id' => array('name' => 'D157_C004_Id', 'label'=>'Transporte','type' => 'hidden', 'value' => $g['empresaAtual']),
					'D157_Flag_Totalizar_Peso' => array('name' => 'D157_Flag_Totalizar_Peso', 'label'=>'Totalizar Peso','type' => 'select','select' => 
						array(
							array('title' => 'Não', 'value' => 'N'),
							array('title' => 'Sim', 'value' => 'S'),
						)
					),
					'D157_Flag_Destino_Produto' => array('name' => 'D157_Flag_Destino_Produto','label'=>'Destino', 'type' => 'select', 
						'select' => array(
							array('title' => '...', 'value' => "", 'selected' => true),
							array('title' => '1 - INDUSTRIALIZAÇÃO', 'value' => "1"),
							array('title' => '2 - REVENDA', 'value' => "2"),
							array('title' => '3 - CONSUMO', 'value' => "3"),
							array('title' => '4 - ESPECIAL', 'value' => "4"),
							array('title' => '5 - BONIFICAÇÃO', 'value' => "5"),
							array('title' => '6 - TROCA', 'value' => "6"),
						)
					),
					
					'D157_Tipo_Periodicidade' => array('name' => 'D157_Tipo_Periodicidade','label'=>'Tipo periodicidade', 'type' => 'select', 
						'select' => array(
							array('title' => '...', 'value' => "", 'selected' => true),
							array('title' => 'Dia/mês', 'value' => "1"),
							array('title' => 'Dia/semana', 'value' => "2"),
						)
					),
					'D157_Periodicidade' => array('name' => 'D157_Periodicidade', 'label' => 'Periodicidade', 'type' => 'text'),
				)
			)
		);
		/** Botão Limpar / Novo Registro
		if (!empty($acaoId)) {
			$formArray['campos']['geral']['ven002FormCabecalhoBtnNew'] = array('name' => 'ven002FormCabecalhoBtnNew', 'type'=>'submit', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right; margin-top:20px;', 'reject' => true);
		}
		*/
		//$formArray['campos']['geral']['ven002FormCabecalhoBtnCln'] = array('name' => 'ven002FormCabecalhoBtnCln', 'type'=>'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right; margin-top:20px;', 'reject' => true);
		//$formArray['campos']['geral']['ven002FormCabecalhoBtnAdd'] = array('name' => 'ven002FormCabecalhoBtnAdd', 'type'=>'button', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right; margin-top:20px;', 'reject' => true);
		$form->definirFormularioArray($formArray);
		
		$sql = "SELECT D157_D024_Id FROM D157 WHERE D157_Id = '{$r_D157_Id}';";
		$resultado = mysql_query($sql);
		$row = mysql_fetch_array($resultado);
		echo <<<EOT
		    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		    	$('#ven002FormContatos').unbind('click');
				$('#ven002FormContatos').unbind('click').bind('click', function(data) {
					var id = '{$row['D157_D024_Id']}';
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Cliente/Fornecedor - ID: ' + id, '/cad/cad001/content/cad001contentCadastro/', '&acaoId=' + encodeURIComponent(id) + '&tabela=D024' + '&tab=contatos', [950,553]);
					return false;
				});
				$('#ven002FormVeiculos').unbind('click');
				$('#ven002FormVeiculos').unbind('click').bind('click', function(data) {
					var id = '{$row['D157_D024_Id']}';
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
					$D157 = mysql_query("SELECT D157_Flag_Lista_Preco, D157_D024_Id, D157_D006_Id FROM D157 WHERE D157_Id = '{$acaoId}'");
 					$rD157 = mysql_fetch_array($D157);
					if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {						
						echo gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!');
						$lista = $form->campoValorEnviado('D157_Flag_Lista_Preco');
						//Verifica se a lista de preço foi alterada
						if($rD157['D157_Flag_Lista_Preco'] != $lista){							
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
						$D157_Id = $g['mysqlLastId'];
						$D157 = mysql_query("SELECT D157_Flag_Lista_Preco, D157_D024_Id, D157_D006_Id FROM D157 WHERE D157_Id = '{$D157_Id}'");
 						$rD157 = mysql_fetch_array($D157);
						//Altera a empresa a faturar caso possua no cadastro do cliente
						$D024 = mysql_query("SELECT D024_Empresa_Emissora FROM D024 WHERE D024_Id = '{$rD157['D157_D024_Id']}'");
						$resD024 = mysql_fetch_array($D024); 
						if(isset($resD024['D024_Empresa_Emissora'])) {
							mysql_query("UPDATE D157 SET D157_D024_Id_Nfe = '{$resD024['D024_Empresa_Emissora']}' WHERE D157_Id = '{$D157_Id}'");
						}
						// Imprime uma mensagem e fecha a janela
						$alert = gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!');
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								divRefresh('{$g['divIdParent']}');
								abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' ,unique(), '', 'Editar Pedido - ID: ' + {$D157_Id}, '/cad/cad061/content/cad061content02/', '&acaoId=' + encodeURIComponent('{$D157_Id}') + '&tabela=D157', [1000,615]);
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

    case 'ven002FormAplicarDescontoReais':
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
		$enviar_btnId = 'ven002AplicarDescontoReaisBtnExecutar';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/ven/ven002/form/ven002FormAplicarDescontoReais/';

		$T005 = mysql_query("SELECT T005_Valor_Total,T005_Valor_Desconto_Manual FROM T005 WHERE T005_Id='{$r_T005_Id}'");
		$mT005 = mysql_fetch_array($T005);

		// $valorTotalAntigo = $mT005['T005_Valor_Total'] + $mT005['T005_Valor_Desconto_Manual'];
		// $descontoPercentual = gCorrigeNumero((($valorTotalAntigo - $mT005['T005_Valor_Total']) / $valorTotalAntigo) * 100);   
		// $descontoPercentual = ($descontoPercentual == "0,00" ? "" : $descontoPercentual);

		//form editor
		$form_editor = array('geral' =>'{"DescontoReais":{"top":7,"left":5,"width":178,"hidden":0}}');
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					//'T005_Id' => array('name' => 'T005_Id', 'label' => 'T005_Id', 'type' => 'hidden', 'length' => array(40, 80), 'reject' => true, 'value' => $r_T005_Id),
     				'DescontoReais' => array('name' => 'DescontoReais', 'label' => 'Valor (R$)', 'value'=>'', 'type' => 'moeda', 'length' => array(30, 80), 'reject' => true, 'casasDecimais' => 5),
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
								var CampoAplicarDesconto = $('#{$g['divId']} #CampoAplicarDesconto').val();
								var DescontoReais = $('#{$g['divId']} #DescontoReais').val();
								$('#{$g['divId']}').showLoading();
								$.getJSON('/ven/ven002/form_func-ajax/ven002aplicarDescontoReais/?ajax=true&T005_Id=' + encodeURIComponent('{$r_T005_Id}') + '&DescontoReais=' + encodeURIComponent(DescontoReais) + '&callback=?', function(request) {
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
		$botoesDir .= '<button type="submit" id="ven002AplicarDescontoReaisBtnExecutar">Executar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'ven002FormDefinirValorTotal':
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
		$enviar_btnId = 'ven002FormDefinirValorTotalBtnExecutar';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/ven/ven002/form/ven002FormDefinirValorTotal/';

		$T005 = mysql_query("SELECT T005_Valor_Total FROM T005 WHERE T005_Id='{$r_T005_Id}'");
		$mT005 = mysql_fetch_array($T005);

		//form editor
		$form_editor = array('geral' =>'{"DescontoValorTotal":{"top":7,"left":5,"width":178,"hidden":0}}');
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
     				'DescontoValorTotal' => array('name' => 'DescontoValorTotal', 'label' => 'Valor Total (R$)', 'value'=>gCorrigeNumero($mT005['T005_Valor_Total']), 'type' => 'moeda', 'length' => array(30, 80), 'reject' => true, 'casasDecimais' => 5),
				)
			)
		);
        $form = new Formulario('form-ven002FormDefinirValorTotal');
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
								var DescontoValorTotal = $('#{$g['divId']} #DescontoValorTotal').val();
								$('#{$g['divId']}').showLoading();
								$.getJSON('/ven/ven002/form_func-ajax/ven002aplicarDescontoTotal/?ajax=true&T005_Id=' + encodeURIComponent('{$r_T005_Id}') + '&DescontoValorTotal=' + encodeURIComponent(DescontoValorTotal) + '&callback=?', function(request) {
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
		$botoesDir .= '<button type="submit" id="ven002FormDefinirValorTotalBtnExecutar">Executar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'ven002FormAplicarDescontoValorTotal':
  		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;
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
		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = 'T005';
		$form_campo_pk = 'T005_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'ven002FormAplicarDescontoValorTotalBtnExecutar';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/ven/ven002/form/ven002FormAplicarDescontoValorTotal/';

		$T005 = mysql_query("SELECT T005_Valor_Total FROM T005 WHERE T005_Id='{$r_T005_Id}'");
		$mT005 = mysql_fetch_array($T005);

		//form editor
		$form_editor = array('geral' =>'{"T005_Valor_Desconto_Manual":{"top":7,"left":5,"width":178,"hidden":0}}');
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
     				'T005_Valor_Desconto_Manual' => array('name' => 'T005_Valor_Desconto_Manual', 'label' => 'Desconto (R$)', 'type' => 'moeda', 'length' => array(30, 80), 'casasDecimais' => 2),
				)
			)
		);
        $form = new Formulario('form-ven002FormAplicarDescontoValorTotal');
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
                if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
                    // Imprime uma mensagem e fecha a janela
                    echo gGeraAlertMsg('Operação OK', 'Desconto aplicado com sucesso!');
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
		$botoesDir .= '<button type="submit" id="ven002FormAplicarDescontoValorTotalBtnExecutar">Executar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
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
			
		);
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					'T005_C004_Id' => array('name' => 'T005_C004_Id', 'label' => 'Empresa', 'type' => 'select', 'select' => 
						gGeraSelect('C004', 'C004_Id', 'C004_Nome_Empresa', 'where C004_Flag_Ativo="S"')
					), 
				)
			)
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
				$retorno = $VEN002->mudarEmpresaPedidosSelecionados($C004_Id_Novo);
				if($retorno === true){
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
							divRefresh('{$g['divIdParent']}', true);
							$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
						} </script></div>
EOT;
				} else {
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
							var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
    						dialogConfirm("Erro!", '{$retorno}', buttons)
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
		$botoesDir .= '<button type="submit" id="ven002FormMudarPedidoEmpresa_btn">Mudar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
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
		
		$form = new Formulario('ven002FormAtualizarCFOP');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D006_Id' => array('name' => 'D006_Id', 'label' => 'CFOP', 'type' => 'select', 'reject' => true,
						'select' => gGeraSelect('D006', 'D006_Id', 'D006_Codigo_CFOP,D006_Descricao', 'WHERE D006_Flag_Inativo!="S" AND D006_Flag_Departamento="COMERCIAL" ORDER BY D006_Codigo_CFOP DESC', true)
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
				$retorno = $VEN002->ven002atualizarCFOPManual($r_T005_Id, $D006_Id);
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
            'geral' => '{"T005_Valor_Total_Comissao_Usuario":{"top":7,"left":48,"width":56,"hidden":0},"T005_Percentual_Comissao_Usuario":{"top":7,"left":224,"width":56,"hidden":0},"T005_Valor_Total_Comissao":{"top":30,"left":2,"width":56,"hidden":0},"T005_Percentual_Comissao":{"top":30,"left":224,"width":56,"hidden":0},"T005_Percentual_Comissao_Manual":{"top":30,"left":314,"width":56,"hidden":1},"T005_Valor_Total_Comissao_Externo":{"top":53,"left":-2,"width":56,"hidden":0},"T005_Percentual_Comissao_Externo":{"top":53,"left":224,"width":56,"hidden":0},"T005_Valor_Comissao_Representante":{"top":76,"left":14,"width":56,"hidden":0},"T005_Percentual_Comissao_Representante":{"top":76,"left":224,"width":56,"hidden":0},"ven002FormComissoesBtn":{"top":120,"left":240,"width":null,"hidden":0}}',
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
                	'T005_Cotacao_Moeda' => array('name' => 'T005_Cotacao_Moeda', 'label' => 'Cotação da Moeda', 'type' => 'moeda', 'align' => 'right', 'length' => array(10,10)),
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

    case 'cad061FormProcessarPedidos':
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
        $enviar_btnId = 'cad061FormProcessarPedidosBtn';
        $enviar_acaoHref = '/cad/cad061/form/cad061FormProcessarPedidos/';

        //form editor
        $form_editor = array(
            'geral' => '{"prazoInicial":{"top":7,"left":16,"width":24,"hidden":0},"dataInicial":{"top":7,"left":128,"width":72,"hidden":0},"diaInicial":{"top":7,"left":224,"width":72,"hidden":0},"prazoFinal":{"top":30,"left":23,"width":24,"hidden":0},"dataFinal":{"top":30,"left":128,"width":72,"hidden":0},"diaFinal":{"top":30,"left":224,"width":72,"hidden":0},"cad061FormProcessarPedidosBtn":{"top":60,"left":128,"width":null,"hidden":0}}',
        );

        // novo formulário
        $form = new Formulario('cad061FormProcessarPedidos');

        // Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
        $formArray = array(
            'campos' => array(
                'geral' => array(
                	'prazoInicial' => array('name' => 'prazoInicial', 'label' => 'Prazo inicial', 'type' => 'moeda', 'align' => 'right', 'value' => $g['C031']['prazoInicialPedidoPeriodico'], 'casasDecimais' => '0', 'reject' => true),
                	'prazoFinal' => array('name' => 'prazoFinal', 'label' => 'Prazo final', 'type' => 'moeda', 'align' => 'right', 'value' => $g['C031']['prazoFinalPedidoPeriodico'], 'casasDecimais' => '0', 'reject' => true),
                	'dataInicial' => array('name' => 'dataInicial', 'label' => '', 'type' => 'data', 'align' => 'right', 'reject' => true, 'readonly' => true),
                	'dataFinal' => array('name' => 'dataFinal', 'label' => '', 'type' => 'data', 'align' => 'right', 'reject' => true, 'readonly' => true),
                	'diaInicial' => array('name' => 'diaInicial', 'label' => '', 'type' => 'texto', 'align' => 'right', 'reject' => true, 'readonly' => true),
                	'diaFinal' => array('name' => 'diaFinal', 'label' => '', 'type' => 'texto', 'align' => 'right', 'reject' => true, 'readonly' => true),
                	'cad061FormProcessarPedidosBtn' => array('name' => 'cad061FormProcessarPedidosBtn', 'type'=>'submit', 'reload' => false, 'value' => 'Processar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true),
                )
            )
        );

        $form->definirFormularioArray($formArray);

        echo <<<EOT
	        <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
	        	var dias = [ "DOMINGO", "SEGUNDA", "TERÇA", "QUARTA", "QUINTA", "SEXTA", "SÁBADO" ];
                $('#{$g['divId']} #prazoInicial').keyup(function() {
                	var data = new Date();
                	if($('#{$g['divId']} #prazoInicial').val() != ''){
	                	data.setDate(data.getDate() + parseInt($('#{$g['divId']} #prazoInicial').val()));
	                	var mes = ("0" + (data.getMonth() + 1)).slice(-2);
						$('#{$g['divId']} #dataInicial').val(("0" + data.getDate()).slice(-2) + '/' + mes + '/' + data.getFullYear());
						var dia = parseInt(data.getDay());
						$('#{$g['divId']} #diaInicial').val(dias[dia]);
					}
                });
                $('#{$g['divId']} #prazoInicial').trigger('keyup');
                $('#{$g['divId']} #dataInicial').removeClass('campo_input_data');

                $('#{$g['divId']} #prazoFinal').keyup(function() {
                	var data = new Date();
                	if($('#{$g['divId']} #prazoFinal').val() != ''){
	                	data.setDate(data.getDate() + parseInt($('#{$g['divId']} #prazoFinal').val()));
	                	var mes = ("0" + (data.getMonth() + 1)).slice(-2);
						$('#{$g['divId']} #dataFinal').val(("0" + data.getDate()).slice(-2) + '/' + mes + '/' + data.getFullYear());
						var dia = parseInt(data.getDay());
						$('#{$g['divId']} #diaFinal').val(dias[dia]);
					}
                });
                $('#{$g['divId']} #prazoFinal').trigger('keyup');
                $('#{$g['divId']} #dataFinal').removeClass('campo_input_data');
            } </script></div>
EOT;
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
                require_once("bibliotecas/classes/CAD061.php");
				$CAD061 = new CAD061();
				$retorno = $CAD061->processarPedidosPeriodicos($form->campoValorEnviado('prazoInicial'), $form->campoValorEnviado('prazoFinal'));
                if($retorno === true){
	                echo gGeraAlertMsg('Operação OK', 'Pedidos gerados com sucesso!');
    	        } else {
    	        	echo gGeraAlertMsg('Erro', $retorno);
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
