<?php
namespace hardness;
require_once("bibliotecas/classes/EST015.php");
$EST015 = new EST015();

require_once("bibliotecas/classes/CAD002.php");
$CAD002 = new CAD002();

require_once('prog/est/est015/form_func-js.php');

/**
* Ações
*/
switch ($g['r'][3]) {
	/**
	* Form Principal
	* Chamado em: est015tab02
	*/
	case 'est015FormOrcamento':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		
		$T139_Id = (isset($r_formest015FormOrcamento)) ? $r_formest015FormOrcamento['T139_Id'] : @$r_T139_Id;
		$r_D024_Id_CRM = (isset($r_D024_Id_CRM)) ? $r_D024_Id_CRM : false;
		$r_GerarCotacaoEstoque = (isset($r_GerarCotacaoEstoque)) ? $r_GerarCotacaoEstoque : false;
		$T003_Id = isset($r_T003_Id) ? $r_T003_Id : false;
		
		if(!empty($r_D024_Id_CRM)){
			$sqlD024CRM = <<<EOT
			SELECT * FROM D024
			LEFT JOIN D018 on D018_Id=D024_D018_Id
			LEFT JOIN D020 on D020_Id=D024_D020_Id WHERE
			D024_Id='{$r_D024_Id_CRM}'
EOT;
			$resultD024CRM = mysql_query($sqlD024CRM);
			$linhaD024CRM = mysql_fetch_array($resultD024CRM);
			echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} #T218_D024_Id_Pesquisa').val('[{$r_D024_Id_CRM}] ' + '{$linhaD024CRM['D024_Nome_Empresa']}' + ' [{$linhaD024CRM['D020_Nome_Cidade']}/{$linhaD024CRM['D018_UF']}] - TEL ({$linhaD024CRM['D024_DDD_Telefone_1']}) {$linhaD024CRM['D024_Telefone_1']}');
				$('#{$g['divId']} #T218_D024_Id').val('{$r_D024_Id_CRM}');
				setTimeout(function(){
					$('#{$g['divId']} #T218_D024_Id_Pesquisa').trigger('change');
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
			$sql = "SELECT T139_D024_Id FROM T139 WHERE T139_Id = '{$r_T139_Id}'";
			$result = mysql_query($sql);
			$aD024 = mysql_fetch_assoc($result);
			if (!empty($aD024['T139_D024_Id'])) {
		        echo <<<EOT
		        <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					setTimeout(function() {
					    $("#{$g['divId']}").showLoading();
						$.getJSON('/est/est015/form_func-ajax/carregaCFOP_Contatos/?ajax=true&T218_D024_Id=' + encodeURIComponent('{$aD024['T139_D024_Id']}') + '&callback=?', function(request) {
						    $("#{$g['divId']}").hideLoading();
							if (request.code) {
								$('#T218_D006_Id').html(request.data['T218_D006_Id']);
								$('#T218_D116_Id').html(request.data['T218_D116_Id']);
								$('#T218_D116_Id_refreshBtn').html(request.data['T218_D116_Id_refreshBtn']);
								$('#T218_D013_Id').html(request.data['T218_D013_Id']);
								$('#T218_D013_Id_refreshBtn').html(request.data['T218_D013_Id_refreshBtn']);
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
		$idCliente = $r_idCliente ? $r_idCliente :'99999999999';
		if ($acaoId>0){
			$sql = "SELECT T218_D024_Id FROM T218 WHERE T218_Id = '{$acaoId}'";
			$result = mysql_query($sql);
			$aD024 = mysql_fetch_assoc($result);		
			$idCliente=$aD024['T218_D024_Id'];	
		} 
		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'T218';
		$form_campo_pk = 'T218_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'content_form1Orcamento_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est015/form/est015FormOrcamento/';
		// editor
		$form_editor = array(
			'orcamentos' => '{"T218_Flag_Destino_Produto":{"top":76,"left":84.578125,"width":352,"hidden":0},"T218_D024_Id":{"top":7,"left":58.296875,"width":329,"hidden":0},"btnCotacaoIncluirFornecedor":{"top":5,"left":496,"width":null,"hidden":0},"button":{"top":166,"left":528,"width":null,"hidden":1},"T218_Data_Emissao":{"top":30,"left":72.359375,"width":72,"hidden":0},"T218_Flag_Acp":{"top":30,"left":290.25,"width":96,"hidden":0},"T218_D013_Id":{"top":53,"left":76.015625,"width":351,"hidden":0},"est015FormContatos":{"top":51,"left":496,"width":null,"hidden":0},"T218_D006_Id":{"top":53,"left":760,"width":350,"hidden":1},"T218_C007_Id_Vendedor_Interno":{"top":99,"left":59.515625,"width":351,"hidden":0},"T218_Flag_Revenda_Consumidor":{"top":76,"left":1014,"width":172,"hidden":1},"T218_Prazo_Entrega":{"top":122,"left":37.484375,"width":344,"hidden":0},"T218_Percentual_Comissao":{"top":99,"left":933,"width":229,"hidden":1},"T218_Forma_Pagamento":{"top":145,"left":15.484375,"width":344,"hidden":0},"T218_Flag_Expedicao":{"top":122,"left":984,"width":103,"hidden":1},"T218_Observacao":{"top":168,"left":62.578125,"height":83,"width":344,"hidden":0},"T218_Frete":{"top":145,"left":1009,"width":225,"hidden":1},"T218_Validade_Proposta":{"top":168,"left":947,"width":225,"hidden":1},"TransportadoraPadrao":{"top":214,"left":858,"width":320,"hidden":1},"T218_Flag_Frete":{"top":260,"left":96.203125,"width":352,"hidden":0},"T218_Flag_Lista_Preco":{"top":237,"left":989,"width":76,"hidden":1},"T218_D022_Id":{"top":283,"left":48.078125,"width":330,"hidden":0},"T218_Valor_Frete":{"top":306,"left":68.28125,"width":136,"hidden":0},"T218_D036_Id":{"top":306,"left":324.890625,"width":127,"hidden":0},"T218_Data_Finalizado_Fornecedor":{"top":329,"left":22.21875,"width":136,"hidden":0}}'
		);

		// novo formulário
		$form = new Formulario('formest015FormOrcamento');

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
		
		if($g['C031']['habilitaEmail'] == 'S'){
		
			$exibirtext = 'text';
			$exibirMoeda = 'moeda';
			

		}else{
			$exibirMoeda = 'hidden';
			$exibirtext = 'hidden';
		}

		//Buscas destinos, foi centralizado nesta classe e vai valer para todos os locais
		$destinoProdutos = $CAD002->retornaDestinoProdutos(false, 'C');

		
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'orcamentos' => array(
					'T218_D024_Id' => array('name' => 'T218_D024_Id','label'=>'Fornecedor', 'type' => 'pesquisa','extra' => "onchange=\"divSetarAcaoId('{$g['divId']}', \$('#T218_D024_Id').val(), 'idCliente'); carregaCFOPContatosCotacao(this, '{$g['divId']}'); verificaFlagContribuinteCotacao(this, '{$g['divId']}','{$g['divIdRoot']}','{$g['divIdParent']}'); verificaListaPreco(this, '{$g['divId']}');\"", 'pesquisa' => array('D024','D024_Id','D024_Id,D024_Nome_Empresa,D020_Nome_Cidade,D018_UF,D024_Cpf,D024_Cnpj,D024_DDD_Telefone_1,D024_Telefone_1,D024_Nome_Fantasia','left join D020 on D020_Id=D024_D020_Id left join D018 on D018_Id=D024_D018_Id WHERE D024_Flag_Fornecedor="S" and D024_Flag_Ativo="S" order by D024_Nome_Empresa','(%1) %2 [%3/%4] - %5 Tel:(%7)%8'), 'length' => array(80,30)),
					//'T218_D024_Id' => array('name' => 'T218_D024_Id' , 'label' => 'Cliente', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGrid, 'length' => array(64,120)),
					'T218_D013_Id' => array('name' => 'T218_D013_Id', 'label' => 'Contato', 'type' => 'select', 'selectRefresh' => array('D013','D013_Id','D013_Nome_Contato,D013_DDD_Telefone_1,D013_Telefone_1,D013_E_Mail',"where D013_D024_Id='$idCliente' AND D013_Flag_Ativo = 'S'",true),
						'select' => gGeraSelect('D013','D013_Id','D013_Nome_Contato,D013_DDD_Telefone_1,D013_Telefone_1,D013_E_Mail',"where D013_D024_Id='$idCliente' AND D013_Flag_Ativo = 'S'",true)
					),
					
					'est015FormContatos' => array('name' => 'est015FormContatos', 'type'=>'submit', 'reload' => false, 'value' => 'Incluir', 'align' => 'right', 'reject' => true),
					/*'T218_D116_Id' => array('name' => 'T218_D116_Id', 'label'=>'Veículo', 'type' => 'select',
						'select'=> gGeraSelect('D116', 'D116_Id', 'D116_Placa,D133_Modelo,D134_Cor', "LEFT JOIN D133 on D133_Id=D116_D133_Id LEFT JOIN D134 on D134_Id=D116_D134_Id WHERE D116_D024_Id=$idCliente", true),
						'selectRefresh' => array('D116', 'D116_Id', 'D116_Placa,D133_Modelo,D134_Cor', "LEFT JOIN D133 on D133_Id=D116_D133_Id LEFT JOIN D134 on D134_Id=D116_D134_Id WHERE D116_D024_Id=$idCliente", true)
					),
					'est015FormVeiculos' => array('name' => 'est015FormVeiculos', 'type'=>'submit', 'reload' => false, 'value' => 'Incluir', 'align' => 'right', 'reject' => true),	*/
					'T218_D006_Id' => array('name' => 'T218_D006_Id', 'label'=>'CFOP','type' => 'select', 'restricao' => false, 'select' => 
						gGeraSelect('D006','D006_Id','D006_Codigo_CFOP,D006_Descricao',' WHERE D006_Flag_Inativo!="S" AND D006_Flag_Departamento="COMERCIAL"  ORDER BY D006_Codigo_CFOP',true),
					),
					'T218_Flag_Revenda_Consumidor' => array('name' => 'T218_Flag_Revenda_Consumidor', 'label'=>'Tipo','type' => 'select',
						'select' => 
							array(
								array('title' => '...', 'value' => 0, 'selected' => true),
								array('title' => 'Revenda', 'value' => 1),
								array('title' => 'Consumidor atacado', 'value' => 2),
								array('title' => 'Consumidor varejo', 'value' => 3),
							)
					),
					'T218_Data_Emissao' => array('name' => 'T218_Data_Emissao', 'label'=>'Emissão','type' => 'data','value'=>date("d/m/Y"), 'length' => array(12,10)),											
					'T218_C007_Id_Vendedor_Interno' => array('name' => 'T218_C007_Id_Vendedor_Interno', 'label'=>'Comprador','type' => 'select', 'select' => 
						//gGeraSelect('C007','C007_Id','C007_Login','WHERE C007_Id='.$g['usuarioAtual']),
						gGeraSelect('C007','C007_Id','C007_Login','WHERE C007_Flag_Vendedor!=""',true,$g['usuarioAtual']),
					),
					/*'T218_D021_Id' => array('name' => 'T218_D021_Id', 'label'=>'Representante','type' => 'select', 'select' => 
						gGeraSelect('D021','D021_Id','D021_Nome_Empresa','',true),
					),*/
					//'T218_Percentual_Comissao' => array('name' => 'T218_Percentual_Comissao', 'label'=>'Comissão %','value'=>'0,00','type' => 'moeda', 'length' => array(20,10)),
					'T218_Flag_Expedicao' => array('name' => 'T218_Flag_Expedicao', 'label'=>'Expedição','type' => 'select','select' => 
						array(
							array('title' => '...', 'value' => 0),
							array('title' => 'Comercial', 'value' => 1),
							array('title' => 'Balcão', 'value' => 2),
						)
					),
					'T218_Flag_Acp' => array('name' => 'T218_Flag_Acp', 'label'=>'Tipo Documento','type' => 'select', 'select' => 
						array(
							array('title' => '...', 'value' => 0),
							array('title' => 'NF', 'value' => 1),
							array('title' => 'Romaneio', 'value' => 2),
							array('title' => 'ACP', 'value' => 3),
							array('title' => 'Cupom Fiscal', 'value' => 4),
							array('title' => 'NF D1', 'value' => 5),
							array('title' => 'NFe', 'value' => 6, 'selected' => true),
							array('title' => 'NFSe', 'value' => 7),
						)
					),
					'T218_Flag_Lista_Preco' => 	array('name' => 'T218_Flag_Lista_Preco', 'label' => 'Lista Preço', 'type' => 'select', 'select' => 
						array(
							array('title' => 'Preço 1', 'value' => '1'),   
							array('title' => 'Preço 2', 'value' => '2'),   
							array('title' => 'Preço 3', 'value' => '3'),   
						)
					),
					'T218_Nome_Transportadora' => array('name' => 'T218_Nome_Transportadora', 'label'=>'Transportadora da Cotação', 'type' => $exibirtext, 'length' => array(40,50)),
					'T218_Forma_Pagamento' => array('name' => 'T218_Forma_Pagamento', 'label'=>'Forma de Pagamento', 'type' => 'text', 'length' => array(40,50)),
					'T218_Frete' => array('name' => 'T218_Frete', 'label'=>'Frete','type' => 'texto', 'length' => array(40,50)),
					'T218_Valor_Frete' => array('name' => 'T218_Valor_Frete', 'label'=>'Valor Frete','type' => 'moeda', 'length' => array(40,50)),
					'T218_Prazo_Entrega' => array('name' => 'T218_Prazo_Entrega', 'label'=>'Prazo de entrega', 'type' => 'text', 'length' => array(40,50)),
					'T218_Validade_Proposta' => array('name' => 'T218_Validade_Proposta', 'label'=>'Validade proposta', 'type' => 'text', 'length' => array(40,50)),
					//'T218_Prazos_Pagamentos' => array('name' => 'T218_Prazos_Entregas', 'label'=>'Forma de Pagamento','type' => 'text', 'length' => array(53,40)),
					'T218_Flag_Frete' => array('name' => 'T218_Flag_Frete','label'=>'Frete', 'type' => 'select',
						'select' => array(
							array('title' => '0-EMITENTE', 'value' => '0'),
							array('title' => '1-DESTINATÁRIO', 'value' => '1'),
							array('title' => '2-TERCEIROS', 'value' => '2'),
							array('title' => '9-SEM FRETE', 'value' => '9'),
						),
					),
					'T218_D022_Id' => array('name' => 'T218_D022_Id', 'label' => 'Transportadora', 'type' => 'pesquisa', 'pesquisa' => array('D022','D022_Id','D022_Nome_Empresa,D020_Nome_Cidade,D018_UF','left join D020 on D020_Id=D022_D020_Id left join D018 on D018_Id=D022_D018_Id'),'extra' => "onchange=\"carregaTipoTransp(this, '{$g['divId']}')\"", 'length' => array(50,90)),
					'T218_D036_Id' => array('name' => 'T218_D036_Id', 'label' => 'Tipo', 'type' => 'select', 'select' =>
							gGeraSelect('D036','D036_Id','D036_Tipo_Transporte','',true),
					),
					'TransportadoraPadrao' => array('name' => 'TransportadoraPadrao', 'label' => 'Transportadora Padrão', 'type' => 'select','reject' =>true ,'disabled' => true,
						'select' => gGeraSelect('D022','D022_Id','D022_Nome_Empresa,D020_Nome_Cidade,D018_UF',"left join D020 on D020_Id=D022_D020_Id left join D018 on D018_Id=D022_D018_Id WHERE D022_Id='{$idTransportadora}' OR D022_Id = '{$idTransportadoraCidade}'",false), 
					),
					'button' => array('name' => 'button', 'type' => 'button','value' =>'Usar Padrão','reload' => false,'extra' => "onclick='copiaTransportadora({$idTipoTransp})'",'reject'=>true),
					'nada' => array('name' => 'nada', 'label' => 'Transportadora', 'type' => 'hidden', 'reject'=>true, 'length' => array(30,30)),
					'T218_Observacao' => array('name' => 'T218_Observacao', 'label'=>'Observação', 'type' => 'textarea', 'length' => array(4,50)),
					'T218_C004_Id' => array('name' => 'T218_C004_Id', 'type' => 'hidden', 'value'=> "{$g['empresaAtual']}"),
					'T139_Id' => array('name' => 'T139_Id', 'reject' => true, 'label' => 'ID Email', 'type' => 'hidden', 'value'=> $T139_Id),
					'T218_Percentual_Comissao' => array('name' => 'T218_Percentual_Comissao', 'label'=>'Percentual Comissão', 'type' => 'text', 'length' => array(40,50)),
					'T218_Nome_Cliente' => array('name' => 'T218_Nome_Cliente', 'label' => 'T218_Nome_Cliente', 'type' => 'hidden', 'length' => array(40,50)),
					'T218_Data_Finalizado_Fornecedor' => array('name' => 'T218_Data_Finalizado_Fornecedor', 'label'=>'Fornecedor finalizou','type' => 'data', 'length' => array(12,10)),
					'GerarCotacaoEstoque' => array('name' => 'GerarCotacaoEstoque','label'=>'GerarCotacaoEstoque', 'type' => 'hidden', 'value' => @$r_GerarCotacaoEstoque,'reject' => true),
					'extra' => array('name' => 'extra','label'=>'extra', 'type' => 'hidden', 'value' => @$r_extra,'reject' => true),
					'GerarCotacaoEmergencia' => array('name' => 'GerarCotacaoEmergencia','label'=>'GerarCotacaoEmergencia', 'type' => 'hidden', 'value' => @$r_GerarCotacaoEmergencia,'reject' => true),
					'GerarCotacaoPedidoEmergencia' => array('name' => 'GerarCotacaoPedidoEmergencia','label'=>'GerarCotacaoPedidoEmergencia', 'type' => 'hidden', 'value' => @$r_GerarCotacaoPedidoEmergencia,'reject' => true),
					'btnCotacaoIncluirFornecedor' => array('name' => 'btnCotacaoIncluirFornecedor', 'type'=>'button', 'reload' => false, 'value' => 'Incluir', 'reject' => true),
					'atualizarCodigoFornecedor' => array('name' => 'atualizarCodigoFornecedor', 'label'=>'atualizarCodigoFornecedor', 'type' => 'hidden', 'value' => @$r_atualizarCodigoFornecedor, 'reject' => true),
					'T218_Flag_Destino_Produto' => array('name' => 'T218_Flag_Destino_Produto','label'=>'Destino', 'type' => 'select', 
						'select' => $destinoProdutos
					),
					'T218A_Hora_Emissao' => array('name' => 'T218A_Hora_Emissao', 'label'=>'Hora emissão','type' => 'hidden' , 'value' => date("H:i:s")),
				)
			)
		);
		// Botão Limpar / Enviar
		if(!empty($r_acaoId)){
			$formArray['campos']['orcamentos']['content_form1Orcamento_btn'] = array('name' => 'content_form1Orcamento_btn', 'type'=>'button', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		}
		$form->definirFormularioArray($formArray);

		echo <<<EOT
		    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		    	$('#est015FormContatos').unbind('click');
				$('#est015FormContatos').unbind('click').bind('click', function(data) {
					var id = $('#{$g['divId']} #T218_D024_Id').val();
					if (id>0){
						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Cliente/Fornecedor - ID: ' + id, '/cad/cad001/content/cad001contentCadastro/', '&acaoId=' + encodeURIComponent(id) + '&tabela=D024' + '&tab=contatos', [950,553]);											
					}else{
						dialogConfirm('Erro', 'Selecione o Cliente!');
					}
					return false;
				});
				$('#est015FormVeiculos').unbind('click');
				$('#est015FormVeiculos').unbind('click').bind('click', function(data) {
					var id = $('#{$g['divId']} #T218_D024_Id').val();
					if (id>0){
						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Incluir Veículo', '/cad/cad001/content/cad001ContentVeiculosIncluir/', '&naoRefreshParent=true&D024_Id=' + encodeURIComponent(id) + '&tabela=D024' + '&tab=veiculos', [300,350]);
					}else{
						dialogConfirm('Erro', 'Selecione o Cliente!');
					}
					return false;
				});
				$('#btnCotacaoIncluirFornecedor').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Incluir Novo Fornecedor', '/cad/cad001/content/cad001CotacaoIncluirFornecedor/', '&fornecedor=true', [570,290]);
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
					
					$T218 = mysql_query("SELECT T218_Flag_Lista_Preco FROM T218 WHERE T218_Id = '{$acaoId}'");
 					$rT218 = mysql_fetch_array($T218);
					if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {												
   						mysql_query("update T218 left join D024 on D024_Id=T218_D024_Id set T218_Nome_Cliente = D024_Nome_Empresa, T218_D018_Id=D024_D018_Id where T218_Id='$acaoId'");
						// Imprime uma mensagem e fecha a janela
						echo gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!');
						
						//Verifica se a lista de preço foi alterada
						$lista = $form->campoValorEnviado('T218_Flag_Lista_Preco');
				        if($lista != $rT218['T218_Flag_Lista_Preco']){	
				        	//Altera a Lista de Precos e o valor Original dos produtos
				        	//$EST015->alteraLista($acaoId, $lista);				        
				        	//echo gGeraAlertMsg('Atenção', 'A lista de preço foi alterada, os valores de tabela dos produtos serão modificados.');	
				        }						
						//Atualiza as aliquotas dos produtos, caso o orçamento seja gerado pelo coletor
						//$EST015->atualizarAliquotas($acaoId);
						if($form->campoValorEnviado('atualizarCodigoFornecedor') == true){
							$retorno = $EST015->atualizarCodigoFornecedorCotacao($acaoId);
						}

				        $sqlTotalizador = mysql_query("SELECT T219_Id FROM T219 WHERE T219_T218_Id='{$acaoId}'");
				        while($rowTotalizador = mysql_fetch_array($sqlTotalizador)){
				            mysql_query("call T219_Gravar_Totalizacao('{$rowTotalizador['T219_Id']}')");
				        }
				        mysql_query("call T218_Gravar_Totalizacao_3('$acaoId')");
				        
				        if($r_abrirCotacao == true){
				        	echo <<<EOT
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									// Fecha a janela, recarrega o grid e abre a alteração
									setTimeout(function() {
										abrirJanela(false, '{$g['divIdRoot']}', '{$r_divIdMain}', unique(), '', 'Editar Cotação de Compra', '/est/est015/content/est015ContentEditarOrcamento/', '&acaoId=' + encodeURIComponent('{$acaoId}'), [1000,610]);
									}, 150);
		  							$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
									//divRefresh('{$r_divIdMain}', true);
								} </script></div>
EOT;
						} else {
				        	echo <<<EOT
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									//divRefresh('{$r_divIdMain}', true);
									divRefresh($('.contentCabecalhoLayout').parent().attr('id'));
								} </script></div>
EOT;
						}
					}
				} else {
					// Insert
					// primeiro verifica se foi setado o tipo do cliente
					$result = mysql_query("SELECT D024_Id,
												  D024_Flag_Contribuinte,
												  D024_Pgto_Primeiro_Vcto_Tipo,
												  D024_Prazo_Parcela,
												  D024_Pgto_Dias_Vcto_Tipo,
												  D024_Dias_Vencimento,
												  D024A_D031_Id,
												  D024_Cobrar_Frete_Primeira_Parcela,
												  D024_Cobrar_ST_Primeira_Parcela,
												  D024_Cobrar_IPI_Primeira_Parcela,
												  D024_Quantidade_Parcelas 
											 FROM D024 
										LEFT JOIN D024A ON D024_Id = D024A_D024_Id
											WHERE D024_Id='{$form->campoValorEnviado('T218_D024_Id')}'");

					$D024 = mysql_fetch_assoc($result);	

					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						// Imprime uma mensagem e fecha a janela
						$acaoId = $g['mysqlLastId'];
						//pega o T218_Id da tabela estendida
						$T218A  = mysql_query("SELECT T218A_T218_Id FROM T218A WHERE T218A_Id = '{$acaoId}'");
						$mT218A = mysql_fetch_array($T218A);
						$acaoId = $mT218A['T218A_T218_Id'];

						$sqlT218 	  = mysql_query("SELECT T218_Grupo FROM T218 ORDER BY T218_Grupo DESC LIMIT 1");
						$resT218 	  = mysql_fetch_array($sqlT218);
						$grupo 		  = $resT218['T218_Grupo'] + 1;

						$calcularST   = ($g['C031']['comprasCalcularST'] == 'S') ? 'S' : 'N';
			            $calcularIPI  = ($g['C031']['comprasCalcularIPI'] == 'S') ? 'S' : 'N';
			            $calcularICMS = ($g['C031']['comprasCalcularICMS'] == 'S') ? 'S' : 'N';


						$update = "UPDATE T218";
						$update .= " LEFT JOIN D024 ON D024_Id=T218_D024_Id";
						$update .= " SET";
						if(!empty($D024['D024_Pgto_Primeiro_Vcto_Tipo'])){
							$update.=" T218_Pgto_Primeiro_Vcto_Tipo='{$D024['D024_Pgto_Primeiro_Vcto_Tipo']}',";
						}
						if(!empty($D024['D024_Prazo_Parcela'])){
							$update.=" T218_Pgto_Primeiro_Vcto_Valor='{$D024['D024_Prazo_Parcela']}', ";
						}
						if(!empty($D024['D024_Pgto_Dias_Vcto_Tipo'])){
							$update.=" T218_Pgto_Dias_Vcto_Tipo='{$D024['D024_Pgto_Dias_Vcto_Tipo']}', ";
						}
						if(!empty($D024['D024_Dias_Vencimento'])){
							$update.=" T218_Pgto_Dias_Vcto_Valor='{$D024['D024_Dias_Vencimento']}', ";
						}
						if(!empty($D024['D024A_D031_Id'])){
							$update.=" T218_Pgto_Portador='{$D024['D024A_D031_Id']}', ";
						}
						if(!empty($D024['D024_Quantidade_Parcelas'])){
							$update.=" T218_Pgto_Parcelas='{$D024['D024_Quantidade_Parcelas']}', ";
						}
						$update .= " T218_Nome_Cliente		 = D024_Nome_Empresa,";
						$update .= " T218_D018_Id      		 = D024_D018_Id,";
						$update .= " T218_Grupo              = '{$grupo}',";
						$update .= " T218_Flag_Calcular_ST   = '{$calcularST}',";
						$update .= " T218_Flag_Calcular_IPI  = '{$calcularIPI}',";
						$update .= " T218_Flag_Calcular_ICMS = '{$calcularICMS}' ";
						$update .= " WHERE T218_Id='{$acaoId}' ";
						mysql_query($update);
					
						
/* 						mysql_query("UPDATE T218 
						          LEFT JOIN D024 ON D024_Id=T218_D024_Id 
								        SET T218_Nome_Cliente       = D024_Nome_Empresa, 
										    T218_D018_Id            = D024_D018_Id, 
											T218_Grupo              = '{$grupo}', 
											T218_Flag_Calcular_ST   = '{$calcularST}', 
											T218_Flag_Calcular_IPI  = '{$calcularIPI}', 
											T218_Flag_Calcular_ICMS = '{$calcularICMS}' 
									  WHERE T218_Id='{$acaoId}'"); */
						
						// Associa esse orçamento a um e-mail (caso ele foi gerado apartir de um)
						if (!empty($T139_Id)) {
							$sql = "UPDATE T139 SET T139_T218_Id = '{$acaoId}' WHERE T139_Id = '{$T139_Id}';";
							mysql_query($sql);
							$sql = "UPDATE T139 SET T139_D024_Id = '{$form->campoValorEnviado('T218_D024_Id')}' WHERE T139_Id = '{$T139_Id}';";
							mysql_query($sql);
						}
						if($form->campoValorEnviado('GerarCotacaoEstoque') == true){
							$retorno = $EST015->insereItemEstoque($acaoId, urldecode(urldecode($r_extra)));
							if($retorno === true){
								echo <<<EOT
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									T218_Id={$acaoId};
									idProgressBar = dialogAlert('Processando CFOP e alíquotas',"<div style='border:none;width:auto;height:200px;overflow:auto'><span id='progressBar'><b><br>0% Processando CFOP</b></span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' /></div>");

									$.getJSON('/est/est015/form_func-ajax/atualizarCFOP/?ajax=true&T218_Id=' + T218_Id + '&callback=?', function(request) {
										if (request.code) {
											$('#progressBar').html('<br><b><font color="green">100% CFOP processada &#10004;</font></b></br>' + '<b>0% Processando alíquotas</b></br>');
											est015AtualizarAliquotasCotacaoLoop(idProgressBar,T218_Id,1);

											function est015AtualizarAliquotasCotacaoLoop(idProgressBar, T218_Id, inicio){
								                $.getJSON('/est/est015/form_func-ajax/atualizarAliquota/?ajax=true&T218_Id=' + T218_Id + '&extra=' + encodeURIComponent("{$extra_}") + '&inicio=' + inicio +'&C004_Id='+ encodeURIComponent("{$g['empresaAtual']}")+'&callback=?', function(request) {
								                    if (request.concluido == 0) {
								                    	$('#progressBar').html('<br><b><font color="green">100% CFOP processada &#10004;</font></b></br><b>' + request.percentual + '<b>% Processando alíquotas</b>' + '<br/><br/>Produtos atualizados: <b>' + request.ok + '</b><br/>Produtos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
								                    	est015AtualizarAliquotasCotacaoLoop(idProgressBar,T218_Id,0);
								                    }else{
								                    	if (request.code) {
								                    		$.getJSON('/est/est015/form_func-ajax/est015TotalizarInserirItemEstoque/?ajax=true&T218_Id=' + T218_Id + '&extra=' + encodeURIComponent("{$extra_}") + '&callback=?', function(request) {
								                    			if(request.code){
								                    				divRefresh('{$g['divId']}');
								                    				$('#dc-' + idProgressBar).dialog("destroy").remove();
								                    				dialogConfirm('CFOP e alíquotas processadas','<br><b><font color="green">100% CFOP processada &#10004;</font></b></br><b><font color="green"><b>100% Alíquotas processadas &#10004;</b><br><font color="green"><b>100% Produtos totalizados &#10004;</b></font>' );
								                    				$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
																		setTimeout(function() {
																			abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}', unique(), '', 'Editar Cotação de Compra - ID: ' + '{$acaoId}', '/est/est015/content/est015ContentEditarOrcamento/', '&acaoId=' + encodeURIComponent('{$acaoId}') + '&tabela=T218', [1000,610]);
																		}, 150);
										  							});
										  							divRefresh('{$g['divIdParent']}');
																	$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');	
								                    			}else{
								                    				divRefresh('{$g['divId']}');
								                    				$('#dc-' + idProgressBar).dialog("destroy").remove();
								                    				var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
																	dialogConfirm('Erro ao totalizar itens', '<div style=\'border:none;width:auto;height:200px;overflow:auto\'><b>Os produtos foram incluídos na cotação!</b>'+'<br><br>No entanto houve um erro ao totalizar itens<b><font color="red"><br>Favor realizar o processo de atualizar alíquotas manualmente</font></b>'+'<br><br>Erro(s) encontrado(s):<br>'+request.data +'</div>', \$buttons);	
																	$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
																		setTimeout(function() {
																			abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}', unique(), '', 'Editar Cotação de Compra - ID: ' + '{$acaoId}', '/est/est015/content/est015ContentEditarOrcamento/', '&acaoId=' + encodeURIComponent('{$acaoId}') + '&tabela=T218', [1000,610]);
																		}, 150);
										  							});
										  							divRefresh('{$g['divIdParent']}');
																	$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								                    			}
								                    		});	  
						                    			}else{
						                    				divRefresh('{$g['divId']}');
						                    				$('#dc-' + idProgressBar).dialog("destroy").remove();
															var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
															dialogConfirm('Erro ao processar Alíquotas', '<div style=\'border:none;width:auto;height:200px;overflow:auto\'><b>Os produtos foram incluídos na cotação!</b>'+'<br><br>No entanto houve um erro ao atualizar alíquotas<b><font color="red"><br>Favor realizar o processo de atualizar alíquotas manualmente</font></b>'+'<br><br>Erro(s) encontrado(s):<br>'+request.erros +'<br>' +request.data +'</div>', \$buttons);
															$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
																setTimeout(function() {
																	abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}', unique(), '', 'Editar Cotação de Compra - ID: ' + '{$acaoId}', '/est/est015/content/est015ContentEditarOrcamento/', '&acaoId=' + encodeURIComponent('{$acaoId}') + '&tabela=T218', [1000,610]);
																}, 150);
								  							});
								  							divRefresh('{$g['divIdParent']}');
															$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');	
						                    			}		
								                    } 
								                });	                    
									      	}
										} else {
											divRefresh('{$g['divId']}');
											$('#dc-' + idProgressBar).dialog("destroy").remove();
											var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
											dialogConfirm('Erro ao processar CFOP', '<div style=\'border:none;width:auto;height:200px;overflow:auto\'><b>Os produtos foram incluídos na cotação!</b>'+'<br><br>No entanto houve um erro ao atualizar CFOP<b><font color="red"><br>Favor realizar o processo de atualizar CFOP e alíquotas manualmente</font></b>'+'<br><br>Erro(s) encontrado(s):<br>'+request.data+'</div>', \$buttons);
											$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
												setTimeout(function() {
													abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}', unique(), '', 'Editar Cotação de Compra - ID: ' + '{$acaoId}', '/est/est015/content/est015ContentEditarOrcamento/', '&acaoId=' + encodeURIComponent('{$acaoId}') + '&tabela=T218', [1000,610]);
												}, 150);
				  							});
				  							divRefresh('{$g['divIdParent']}');
											$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');	
										}
									});
								} </script></div>
EOT;
							}else{
								$alert .= gGeraAlertMsg('Erro!', "Erro ao incluir produtos na cotação: {$retorno}", false);
								echo <<<EOT
									<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
										{$alert}
									} </script></div>
EOT;
							}
						}else if($form->campoValorEnviado('GerarCotacaoEmergencia') == true){
							log("entra aqui para gerar cotacao emergencia");
							$retorno = $EST015->insereItemCotacaoEmergencia($acaoId, $form->campoValorEnviado('extra'),$T003_Id);
							echo <<<EOT
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									var T005_Id = "{$retorno}";
									T218_Id={$acaoId};
									idProgressBar = dialogAlert('Processando CFOP e alíquotas',"<span id='progressBar'><b><br>0% Processando CFOP</b></span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' />");

									$.getJSON('/est/est015/form_func-ajax/est015atualizarCFOP/?ajax=true&T218_Id=' + T218_Id + '&callback=?', function(request) {
										if (request.code) {
											$('#progressBar').html('<br><b><font color="green">100% CFOP processada &#10004;</font></b></br>' + '<b>0% Processando alíquotas</b></br>');
											est015AtualizarAliquotasCotacaoLoop(idProgressBar,T218_Id,1);

											function est015AtualizarAliquotasCotacaoLoop(idProgressBar, T218_Id, inicio){
								                $.getJSON('/est/est015/form_func-ajax/atualizarAliquota/?ajax=true&T218_Id=' + T218_Id + '&extra=' + encodeURIComponent("{$extra_}") + '&inicio=' + inicio + '&C004_Id='+encodeURIComponent("{$g['empresaAtual']}")+'&callback=?', function(request) {
								                    if (request.concluido == 0) {
								                    	$('#progressBar').html('<br><b><font color="green">100% CFOP processada &#10004;</font></b></br><b>' + request.percentual + '<b>% Processando alíquotas</b>' + '<br/><br/>Produtos atualizados: <b>' + request.ok + '</b><br/>Produtos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
								                    	est015AtualizarAliquotasCotacaoLoop(idProgressBar,T218_Id,0);
								                    }else{
								                    	if (request.code) {
								                    		$.getJSON('/est/est015/form_func-ajax/est015TotalizarInsereItemCotacaoEmergencia/?ajax=true&T218_Id=' + T218_Id +'&T005_Id='+ T005_Id +'&extra=' + encodeURIComponent("{$extra_}") + '&callback=?', function(request) {
								                    			if(request.code){
								                    				$('#dc-' + idProgressBar).dialog("destroy").remove();
								                    				dialogConfirm('CFOP e alíquotas processadas','<br><b><font color="green">100% CFOP processada &#10004;</font></b></br><b><font color="green"><b>100% Alíquotas processadas &#10004;</b><br><font color="green"><b>100% Produtos totalizados &#10004;</b></font>' );
								                    				$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
																		setTimeout(function() {
																			abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}', unique(), '', 'Editar Cotação de Compra - ID: ' + '{$acaoId}', '/est/est015/content/est015ContentEditarOrcamento/', '&acaoId=' + encodeURIComponent('{$acaoId}') + '&tabela=T218', [1000,610]);
																			{$alert}
																		}, 150);
										  							});
										  							divRefresh('{$g['divIdParent']}');
																	$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');	
								                    			}else{
								                    				$('#dc-' + idProgressBar).dialog("destroy").remove();
								                    				var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
																	dialogConfirm('Erro ao totalizar itens', request.data, \$buttons);	
								                    			}
								                    		});	  
						                    			}else{
						                    				$('#dc-' + idProgressBar).dialog("destroy").remove();
															var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
															dialogConfirm('Erro ao atualizar alíquotas', request.data, \$buttons);
						                    			}		
								                    } 
								                });	                    
									      	}
										} else {
											$('#dc-' + idProgressBar).dialog("destroy").remove();
											var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
											dialogConfirm('Erro ao atualizar CFOP', request.data, \$buttons);
										}
									});
								} </script></div>
EOT;
						}else if($form->campoValorEnviado('GerarCotacaoPedidoEmergencia') == true){
							$retorno = $EST015->insereItemCotacaoPedidoEmergencia($acaoId, $form->campoValorEnviado('extra'));	
							echo <<<EOT
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									var T005_Id = "{$retorno}";
									T218_Id={$acaoId};
									idProgressBar = dialogAlert('Processando CFOP e alíquotas',"<span id='progressBar'><b><br>0% Processando CFOP</b></span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' />");

									$.getJSON('/est/est015/form_func-ajax/est015atualizarCFOP/?ajax=true&T218_Id=' + T218_Id + '&callback=?', function(request) {
										if (request.code) {
											$('#progressBar').html('<br><b><font color="green">100% CFOP processada &#10004;</font></b></br>' + '<b>0% Processando alíquotas</b></br>');
											est015AtualizarAliquotasCotacaoLoop(idProgressBar,T218_Id,1);

											function est015AtualizarAliquotasCotacaoLoop(idProgressBar, T218_Id, inicio){
								                $.getJSON('/est/est015/form_func-ajax/atualizarAliquota/?ajax=true&T218_Id=' + T218_Id + '&extra=' + encodeURIComponent("{$extra_}") + '&inicio=' + inicio +'&C004_Id=' + encodeURIComponent("{$g['empresaAtual']}")+'&callback=?', function(request) {
								                    if (request.concluido == 0) {
								                    	$('#progressBar').html('<br><b><font color="green">100% CFOP processada &#10004;</font></b></br><b>' + request.percentual + '<b>% Processando alíquotas</b>' + '<br/><br/>Produtos atualizados: <b>' + request.ok + '</b><br/>Produtos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
								                    	est015AtualizarAliquotasCotacaoLoop(idProgressBar,T218_Id,0);
								                    }else{
								                    	if (request.code) {
								                    		$.getJSON('/est/est015/form_func-ajax/est015TotalizarInsereItemCotacaoEmergencia/?ajax=true&T218_Id=' + T218_Id +'&T005_Id='+ T005_Id +'&extra=' + encodeURIComponent("{$extra_}") + '&callback=?', function(request) {
								                    			if(request.code){
								                    				$('#dc-' + idProgressBar).dialog("destroy").remove();
								                    				dialogConfirm('CFOP e alíquotas processadas','<br><b><font color="green">100% CFOP processada &#10004;</font></b></br><b><font color="green"><b>100% Alíquotas processadas &#10004;</b><br><font color="green"><b>100% Produtos totalizados &#10004;</b></font>' );
								                    				$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
																		setTimeout(function() {
																			abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}', unique(), '', 'Editar Cotação de Compra - ID: ' + '{$acaoId}', '/est/est015/content/est015ContentEditarOrcamento/', '&acaoId=' + encodeURIComponent('{$acaoId}') + '&tabela=T218', [1000,610]);
																			{$alert}
																		}, 150);
										  							});
										  							divRefresh('{$g['divIdParent']}');
																	$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');	
								                    			}else{
								                    				$('#dc-' + idProgressBar).dialog("destroy").remove();
								                    				var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
																	dialogConfirm('Erro ao totalizar itens', request.data, \$buttons);	
								                    			}
								                    		});	  
						                    			}else{
						                    				$('#dc-' + idProgressBar).dialog("destroy").remove();
															var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
															dialogConfirm('Erro ao atualizar alíquotas', request.data, \$buttons);
						                    			}		
								                    } 
								                });	                    
									      	}
										} else {
											$('#dc-' + idProgressBar).dialog("destroy").remove();
											var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
											dialogConfirm('Erro ao atualizar CFOP', request.data, \$buttons);
										}
									});
								} </script></div>
EOT;
						}else{
							/**
							 * Só entrará nesse if caso a inclusão do orçamento esteja sendo feita pela tab de "catálogos" no CRM
							 */
							if($r_tabCatalogoCRM == true){
								$sqlT198 = "SELECT * FROM T198";
								$resultT198 = mysql_query($sqlT198);
								$erros = '';
								while($linhaT198 = mysql_fetch_array($resultT198)){
									$retorno = $EST015->incluirProduto(false, $linhaT198['T198_D009_Id'], $acaoId);
									if ($retorno !== true) {
										$erros .= "{$linhaT198['T198_D009_Id']}<br/>";
									}
								}
								if(empty($erros)){
									mysql_query("DELETE FROM T198");
									//$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
									echo <<<EOT
										<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
											alert("entra aqui");
											// Fecha a janela, recarrega o grid e abre a alteração
											$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
												setTimeout(function() {
													abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}', unique(), '', 'Editar Cotação de Compra - ID: ' + '{$acaoId}', '/est/est015/content/est015ContentEditarOrcamento/', '&acaoId=' + encodeURIComponent('{$acaoId}') + '&tabela=T218', [1000,610]);
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
								//$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
								echo <<<EOT
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									// Fecha a janela, recarrega o grid e abre a alteração
									$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
										setTimeout(function() {
											abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}', unique(), '', 'Editar Cotação de Compra - ID: ' + '{$acaoId}', '/est/est015/content/est015ContentEditarOrcamento/', '&acaoId=' + encodeURIComponent('{$acaoId}') + '&tabela=T218', [1000,610]);
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
				// Neste caso, se for um insert e vier de um e-mail, carrega alguns dados
				if (!empty($T139_Id)) {
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
							$('#{$g['divId']} #T218_D024_Id').val('{$row['D024_Id']}');
							$('#{$g['divId']} #T218_D024_Id_Pesquisa').val('{$row['D024_Nome_Empresa']}');
EOT;
					}
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							{$empresa}
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
			//$botoesEsq .= '<button id="etc006PreCadastrarCliente">Pré-Cadastrar Fornecedor</button>';
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
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Pré-Cadastro de Fornecedor', '/cad/cad001/content/cad001ContentPreCadastrarCliente/', '&acaoId=' + encodeURIComponent('{$r_acaoId}') + '&tabela=T139', [800,550]);
			});
		} </script></div>
EOT;

	break;
	
	/**
	* Form Principal
	* Chamado em: est015tab02
	*/
	case 'est015FormProdutos':
		$g['smarty']->assign('navSql', "T218_C004_Id = '{$g['empresaAtual']}'");
		gHabilitaNavegacao($g['divId'], 'EST015', 'est015FormProdutos');	
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;

		$T218  = mysql_query("SELECT T218_D024_Id FROM T218 WHERE T218_Id='{$r_T218_Id}'");
		$mT218 = mysql_fetch_array($T218);

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
		$form_tabela = 'T219';
		$form_campo_pk = 'T219_Id';
		$form_parent = 'T218_Id';
		$form_parent_valor = $r_T218_Id;
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = array('est015FormProdutosBtnAdd', 'est015FormProdutosBtnCln', 'est015FormProdutosBtnNew');
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est015/form/est015FormProdutos/';

		$D024_Id = $mT218['T218_D024_Id'];
        
        //form editor
        $form_editor = array(
            'produto' => '{"T219_Flag_Destino_Produto":{"top":99,"left":276.578125,"width":128,"hidden":0},"Procura_Produto":{"top":7,"left":35.359375,"width":122,"hidden":0},"Marca":{"top":7,"left":283.3125,"width":120,"hidden":0},"T219_D005_Id":{"top":7,"left":473.5,"width":95,"hidden":0},"T219_Percentual_Desconto":{"top":7,"left":991,"width":104,"hidden":1},"T219_Descricao_Produto":{"top":30,"left":17.640625,"width":360,"hidden":0},"estoqueLiquido":{"top":30,"left":466.125,"width":88,"hidden":0},"T219_Quantidade":{"top":53,"left":9.65625,"width":72,"hidden":0},"T219_D037_Id":{"top":53,"left":160,"width":63,"hidden":0},"T219_Valor_Preco_Unitario":{"top":53,"left":244.1875,"width":120,"hidden":0},"estoqueLoja":{"top":53,"left":463.078125,"width":88,"hidden":0},"T219_Flag_Origem_Produto":{"top":76,"left":30.484375,"width":144,"hidden":0},"T219_Observacao_Entrega":{"top":76,"left":252.125,"width":120,"hidden":0},"estoqueTotal":{"top":76,"left":460.640625,"width":88,"hidden":0},"T219_Codigo_Substituto":{"top":99,"left":13.953125,"width":136,"hidden":0},"ordemCompra":{"top":99,"left":489.375,"width":88,"hidden":0},"T219_Observacao":{"top":122,"left":14.578125,"width":360,"hidden":0},"estoqueFora":{"top":122,"left":461.25,"width":88,"hidden":0},"T219_D006_Id":{"top":122,"left":712,"width":367,"hidden":1},"UltimoPrecoCompra":{"top":145,"left":31.078125,"height":62,"width":360,"hidden":0},"T219_Aliquota_ICMS":{"top":145,"left":777,"width":40,"hidden":1},"T219_Aliquota_IPI":{"top":145,"left":912,"width":40,"hidden":1},"T219_Flag_ST":{"top":145,"left":1159,"width":24,"hidden":1},"est015BtnCadastrarProduto":{"top":158,"left":480,"width":null,"hidden":0},"T219_ST_VA":{"top":168,"left":1043,"width":40,"hidden":1},"est015FormProdutosBtnCln":{"top":189,"left":480,"width":null,"hidden":0},"est015FormProdutosBtnAdd":{"top":189,"left":560,"width":null,"hidden":0},"T219_ICMS_Inter_Estadual":{"top":191,"left":769,"width":40,"hidden":1},"T219_ST_ICMS_Interno":{"top":191,"left":885,"width":40,"hidden":1}}'
        );

		$mT218['est015FormProdutosForm'] = $r_est015FormProdutosForm['T219_T218_Id'];
		$pesquisaGrid = $EST015->pesquisaGridItemCotacao($r_T218_Id,$mT218);

		$sqlVeiculo = mysql_query("select T218_D024_Id,T218_D116_Id from T218 where T218_Id='{$r_T218_Id}'");
		$resultVeiculo = mysql_fetch_array($sqlVeiculo);

		//Buscas destinos, foi centralizado nesta classe e vai valer para todos os locais
		$destinoProdutos = $CAD002->retornaDestinoProdutos(false, 'C');

		// novo formulário
		$form = new Formulario('est015FormProdutosForm');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'produto' => array(
					'Procura_Produto' => array('name' => 'Procura_Produto' , 'label' => 'Produto', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGrid, 'length' => array(64,120), 'reject' => true, 'placeholder' => 'BUSCAR'),
					'Marca' => array('name' => 'Marca', 'label' => 'Marca', 'type' => 'text', 'length' => array(20,255), 'reject' => true, 'disabled' => true, 'restricao' => false),
					'T219_Codigo_Substituto' => array('name' => 'T219_Codigo_Substituto', 'label' => 'Código forn.', 'type' => 'text', 'length' => array(20, 120), 'align' => 'right', 'style' => 'float:right;'),
					'T219_Codigo_Produto' => array('name' => 'T219_Codigo_Produto', 'label' => 'Código produto', 'type' => 'hidden', 'length' => array(20, 120)),
					'T219_Descricao_Produto' => array('name' => 'T219_Descricao_Produto', 'label' => 'Descrição', 'type' => 'text', 'length' => array(64, 120)),
					'T219_Quantidade' => array('name' => 'T219_Quantidade', 'label' => 'Quantidade', 'type' => 'moeda', 'length' => array(20, 30)),
					'T219_D037_Id' => array('name' => 'T219_D037_Id', 'label' => '', 'type' => 'select', 'align' => 'right', 'style' => 'float:right;', 'select' => 
						gGeraSelect('D037', 'D037_Id', 'D037_Unidade', '', true)
					),
					//'T219_Valor_Preco_Sem_Desconto_Unitario' => array('name' => 'T219_Valor_Preco_Sem_Desconto_Unitario', 'label' => 'Preço Unitário', 'type' => 'hidden', 'length' => array(20, 30)),
					'T219_Valor_Preco_Unitario' => array('name' => 'T219_Valor_Preco_Unitario', 'label' => 'Preço Unitário', 'type' => 'moeda', 'length' => array(20, 30)),
					'T219_Percentual_Desconto' => array('name' => 'T219_Percentual_Desconto', 'label' => '% Desconto', 'type' => 'moeda', 'length' => array(15, 30), 'reject'=>true),
					'T219_D005_Id' => array('name' => 'T219_D005_Id', 'label' => 'NCM', 'type' => 'select', 'align' => 'right', 'readonly' => true, 'extra' => "onfocus='this.initialSelect = this.selectedIndex;' onchange='this.selectedIndex = this.initialSelect;'", 'select' => 
                        gGeraSelect('D005', 'D005_Id', 'D005_Classificacao_Fiscal,D005_Flag_Aplicacao,SUBSTRING(D005_Observacao[[virgula]]1[[virgula]]20)', 'where D005_Flag_Ativo="S" order by D005_Classificacao_Fiscal', true)
                    ),
					'T219_Aliquota_IPI' => array('name' => 'T219_Aliquota_IPI', 'label' => 'IPI', 'align' => 'right', 'readonly'=>true, 'type' => 'moeda', 'length' => array(5,20)),
					'T219_Aliquota_ICMS' => array('name' => 'T219_Aliquota_ICMS', 'label' => 'ICM', 'align' => 'right', 'readonly'=>true, 'type' => 'moeda', 'length' => array(5,20)),
					'T219_Flag_ST' => array('name' => 'T219_Flag_ST', 'label' => 'ST?', 'type' => 'text', 'readonly'=>true, 'length' => array(5,20)),
					'T219_ST_VA' => array('name' => 'T219_ST_VA', 'label' => 'MVA', 'align' => 'right', 'readonly'=>true, 'type' => 'moeda', 'length' => array(5,20)),
					'T219_ST_ICMS_Interno' => array('name' => 'T219_ST_ICMS_Interno', 'label' => 'ICM Interno', 'readonly'=>true, 'align' => 'right', 'type' => 'moeda', 'length' => array(5,20)),
					'T219_ICMS_Inter_Estadual' => array('name' => 'T219_ICMS_Inter_Estadual', 'label' => 'ICM Inter', 'readonly'=>true, 'align' => 'right', 'type' => 'moeda', 'length' => array(5,20)),
					//'T219_Valor_Custo_Unitario' => array('name' => 'T219_Valor_Custo_Unitario', 'label' => 'Custo Unitário', 'type' => 'moeda', 'align' => 'right', 'style' => 'float:right;', 'length' => array(20, 30)),
					//'T219_Numero_Pedido_Compra' => array('name' => 'T219_Numero_Pedido_Compra', 'label' => 'Número OC', 'type' => 'text', 'length' => array(20, 120)),
					//'T219_Item_Pedido_Compra' => array('name' => 'T219_Item_Pedido_Compra', 'label' => 'Item OC', 'type' => 'text', 'length' => array(20, 120), 'align' => 'right', 'style' => 'float:right;'),
					'T219_Observacao' => array('name' => 'T219_Observacao', 'label' => 'Observação', 'type' => 'text', 'length' => array(64, 120)),
					'T219_Flag_Origem_Produto' => array('name' => 'T219_Flag_Origem_Produto', 'label' => 'Origem', 'type' => 'select', 'select' => 
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
					),
					'T219_Observacao_Entrega' => array('name' => 'T219_Observacao_Entrega', 'label' => 'Entrega dias', 'type' => 'text'),
					//Este campo não existe no banco, é apenas para inserir um serviço na T230
					'idVeiculoOrcamento' => array('name' => 'idVeiculoOrcamento', 'label' => 'Veículo', 'type' => 'select','reject' => true, 'extra'=>'style="width:190px;"',
						'select'=> gGeraSelect('D116', 'D116_Id', 'D116_Placa,D133_Modelo,D134_Cor', 'LEFT JOIN D133 on D133_Id=D116_D133_Id LEFT JOIN D134 on D134_Id=D116_D134_Id WHERE D116_D024_Id=(SELECT T218_D024_Id FROM T218 WHERE T218_Id='.@$r_T218_Id.')', true,@$resultVeiculo['T218_D116_Id']),
						'selectRefresh' => array('D116', 'D116_Id', 'D116_Placa,D133_Modelo,D134_Cor', ' LEFT JOIN D133 on D133_Id=D116_D133_Id LEFT JOIN D134 on D134_Id=D116_D134_Id WHERE D116_D024_Id=(SELECT T218_D024_Id FROM T218 WHERE T218_Id='.@$r_T218_Id.')', true,@$resultVeiculo['T218_D116_Id'])
					),

					// Campos Hidden
					'T219_T218_Id' => array('name' => 'T219_T218_Id', 'label' => '', 'type' => 'hidden', 'value' => @$r_T218_Id),
					'T219_C004_Id' => array('name' => 'T219_C004_Id', 'label' => '', 'type' => 'hidden', 'value' => $g['empresaAtual']),
					'T219_D009_Id' => array('name' => 'T219_D009_Id', 'label' => 'T219_D009_Id', 'type' => 'hidden'),
					'D001_Id' => array('name' => 'D001_Id', 'label' => 'D001_Id', 'type' => 'hidden', 'reject'=>true),
					'UltimoPrecoCompra' => array('name' => 'UltimoPrecoCompra', 'label' => "<a href=\"#\" onclick=\"abrirJanela(event, divIdRootAberto(), gridPegarDivId(\$(this)), unique(), 'Produto', '.: Produto :.', '/cad/cad002/content/form2/', '&acaoId=' + encodeURIComponent(\$('#{$g['divId']} .itemForm-D001_Id').val()), [1000,600]); return false;\">Histórico</a>", 'type' => 'textarea', 'reject'=>true, 'readonly'=>true),
					//'NcmProduto' => array('name' => 'NcmProduto', 'label' => 'NcmProduto','reject' => 'true', 'type' => 'hidden'),
					'T219_D006_Id' => array('name' => 'T219_D006_Id', 'label' => 'CFOP', 'restricao' => false, 'readonly'=>true, 'type' => 'select',
						'select' => gGeraSelect('D006','D006_Id','D006_Codigo_CFOP,D006_Descricao',' WHERE D006_Flag_Inativo!="S" AND D006_Flag_Departamento="COMERCIAL" ORDER BY D006_Codigo_CFOP DESC', true),
					),
					'estoqueLiquido' => array('name' => 'estoqueLiquido', 'label' => "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Pedidos', '/crm/crm001/content/crm001ContentPedidosProduto/', '&acaoId=' + encodeURIComponent(\$('#{$g['divId']} .itemForm-T219_D009_Id').val()), [700,400]); return false;\">Est. Liq.</a>", 'type' => 'moeda', 'length' => array(5,20), 'reject' => true, 'readonly'=>true, 'style' => 'float:right; text-align:right;'),
					'estoqueLoja' => array('name' => 'estoqueLoja', 'label' => 'Est. Loja', 'type' => 'moeda', 'length' => array(5,20), 'reject' => true, 'readonly'=>true, 'style' => 'float:right; text-align:right;'),
					'estoqueTotal' => array('name' => 'estoqueTotal', 'label' => 'Est. Total', 'type' => 'moeda', 'length' => array(5,20), 'reject' => true, 'readonly'=>true, 'style' => 'float:right; text-align:right;'),
					'ordemCompra' => array('name' => 'ordemCompra', 'label' => "<a href=\"#\" onclick=\"abrirJanela(event, divIdRootAberto(), gridPegarDivId(\$(this)), unique(), 'Ordem de Compra', '.: Ordem de Compra :.', '/ven/ven001/content/ven001contentProdutoOC/', '&acaoId=' + encodeURIComponent(\$('#{$g['divId']} .itemForm-T219_D009_Id').val()), [700,400]); return false;\">OC</a>", 'type' => 'moeda', 'length' => array(5,20), 'style' => 'float:right; text-align:right;', 'reject' => true, 'readonly'=>true),
					'estoqueFora' => array('name' => 'estoqueFora', 'label' => "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Estoque Fora', '/cad/cad002/content/cad002ContentEstoqueExterno/', '&acaoId=' + encodeURIComponent(\$('#{$g['divId']} .itemForm-D001_Id').val()), [700,400]); return false;\">Est. Fora</a>", 'type' => 'moeda', 'length' => array(5,20), 'reject' => true, 'readonly'=>true, 'style' => 'float:right; text-align:right;'),
					'T219_Flag_Destino_Produto' => array('name' => 'T219_Flag_Destino_Produto','label'=>'Destino', 'type' => 'select', 
						'select' => $destinoProdutos
					),
					'T219_Item' => array('name' => 'T219_Item', 'label' => 'Item', 'type' => 'hidden'),
				),
			)
		);
		// Botão Limpar / Novo Registro
		if (!empty($acaoId)) {
			$formArray['campos']['produto']['est015FormProdutosBtnNew'] = array('name' => 'est015FormProdutosBtnNew', 'type'=>'submit', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		}
		$formArray['campos']['produto']['est015FormProdutosBtnAdd'] = array('name' => 'est015FormProdutosBtnAdd', 'type'=>'button', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['produto']['est015FormProdutosBtnCln'] = array('name' => 'est015FormProdutosBtnCln', 'type'=>'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['produto']['est015BtnCadastrarProduto'] = array('name' => 'est015BtnCadastrarProduto', 'type'=>'button', 'reload' => false, 'value' => 'Pré-Cadastrar Produto', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);	
		$form->definirFormularioArray($formArray);
		
		echo <<<EOT
		    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		    	$('#est015BtnCadastrarProduto').unbind('click');
				$('#est015BtnCadastrarProduto').unbind('click').bind('click', function(data) {
					//var id = $('#{$g['divId']} #T218_D024_Id').val();
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Pré-cadastrar Produto', '/cad/cad002/content/cad002ContentPreCadastro/', '&T218_Id={$r_T218_Id}&divForm={$g['divId']}', [600,430]);											
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
				$T219_Id = false;
				$form->campoSetarValor('T219_Item', 9999, true);
				if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
					//echo gGeraAlertMsg('Operação OK', "Com sucesso");	
					$T219_Id = $g['mysqlLastId'];
					$retorno = true;
					$D009_Id = $form->campoValorEnviado('T219_D009_Id');
					$D006_Id = $form->campoValorEnviado('T219_D006_Id');
					$T219_Flag_Origem_Produto = $form->campoValorEnviado('T219_Flag_Origem_Produto');
					$T219_Flag_Destino_Produto = $form->campoValorEnviado('T219_Flag_Destino_Produto');

					//Aplicar Regra
					if(empty($D006_Id)) {						
						$D006_Id = $EST015->retornaCFOP($D024_Id, $D009_Id, $g['empresaAtual'], $T219_Flag_Origem_Produto, false,false, $T219_Flag_Destino_Produto);            
						$demonstrativoCFOP = $EST015->retornaCFOP($D024_Id, $D009_Id, $g['empresaAtual'], $T219_Flag_Origem_Produto, true, false, $T219_Flag_Destino_Produto);
						$demonstrativoCFOP = mysql_real_escape_string($demonstrativoCFOP);
					
						mysql_query("UPDATE T219 
										LEFT JOIN D009 ON D009_Id=T219_D009_Id
										LEFT JOIN D049 ON D049_Id=D009_D049_Id
										LEFT JOIN D001 ON D001_Id=D049_D001_Id
										SET T219_D006_Id = '{$D006_Id}', T219_D006_Id_Info = '{$demonstrativoCFOP}', T219_Flag_Pre_Cadastro=D001_Flag_Pre_Cadastro 
									WHERE T219_Id = '{$T219_Id}'");
						$erro = mysql_error();
					
						if(!empty($erro)){
							$retorno = "Erro ao atualizar CFOP: " . $erro;
						}
					}

					$T219 = mysql_query("SELECT 
	                        T218_Flag_Calcular_IPI,
	                        T218_Flag_Calcular_ICMS,
	                        T218_Flag_Calcular_ST,
	                        T219_Id,
	                        T219_Item,
	                        T219_Flag_Origem_Produto,
	                        D009_Id,
	                        D001_Codigo_Produto,
	                        D001_D037_Id,
	                        D001_Peso_Unitario_Kg
	                        FROM T218
	                   LEFT JOIN D006 ON D006_Id=T218_D006_Id
	                   LEFT JOIN T219 ON T218_Id=T219_T218_Id
	                   LEFT JOIN T225 ON T219_Id=T225_T219_Id
	                   LEFT JOIN D009 ON D009_Id=T219_D009_Id
	                   LEFT JOIN D049 ON D049_Id=D009_D049_Id
	                   LEFT JOIN D001 ON D001_Id=D049_D001_Id
	                       WHERE T219_Id ='{$T219_Id}'");

					$mT219 = mysql_fetch_array($T219);

					$retorno = $EST015->atualizarAliquotas($r_T218_Id, $g['empresaAtual'], false, $mT219);

					mysql_query("call T218_Gravar_Totalizacao_3('{$r_T218_Id}');");

					if($retorno !== true) {
						echo gGeraAlertMsg('Erro', $retorno);	
					}						
					
					echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								divRefresh('{$r_divReload}', true, false, false, '{$T219_Id}', 'T219_Id'); // grid
								divRefresh('{$r_divOutro}', true, false, 2); // totais
								divRefresh('{$g['divId']}');
							} </script></div>
EOT;
					// Em caso de sucesso, limpa o form (a ação padrão é manter os dados) não precisar dar: divRefresh('{$g['divId']}', true);
					$form->limpar();
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
		
	case 'est015FormServicos':
		
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$r_T218_Id = isset($r_est015FormServicosForm) ? $r_est015FormServicosForm['T223_T218_Id'] : $r_T218_Id;
		
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
		$form_tabela = 'T223';
		$form_campo_pk = 'T223_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = array('est015FormServicosBtnAdd', 'est015FormServicosBtnCln', 'est015FormServicosBtnNew');
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est015/form/est015FormServicos/';
        
        //form editor
        $form_editor = array(
            'servicos' => '{"Procura_Servico":{"top":7,"left":53,"width":264,"hidden":0},"T223_Titulo":{"top":30,"left":58,"width":280,"hidden":0},"T223_Descricao":{"top":53,"left":34,"width":280,"hidden":0},"T223_Valor_Unitario":{"top":122,"left":33,"width":100,"hidden":0},"T223_Tempo_Estimado":{"top":145,"left":6,"width":50,"hidden":0},"est015FormVeiculosBtnNew":{"top":166,"left":400,"width":null,"hidden":0},"T223_D116_Id":{"top":168,"left":48,"width":280,"hidden":0},"est015FormServicosBtnCln":{"top":189,"left":224,"width":null,"hidden":0},"est015FormServicosBtnAdd":{"top":189,"left":320,"width":null,"hidden":0}}',
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
        $sqlVeiculo = mysql_query("select T218_D024_Id,T218_D116_Id from T218 where T218_Id='{$r_T218_Id}'");
		$resultVeiculo = mysql_fetch_array($sqlVeiculo);
		// novo formulário
		$form = new Formulario('est015FormServicosForm');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'servicos' => array(
				'Procura_Servico' => array('name' => 'Procura_Servico' , 'label' => 'Serviço', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGrid, 'length' => array(50,30), 'reject' => true),
					'T223_Titulo' => array('name' => 'T223_Titulo','label'=>'Título','type' => 'text', 'length' => array(50,30)),
					'T223_Descricao' => array('name' => 'T223_Descricao','label'=>'Descrição','type' => 'textarea', 'length' => array(3,46)),
					'T223_Valor_Unitario' => array('name' => 'T223_Valor_Unitario','label'=>'Valor Total','type' => 'moeda', 'length' => array(15,10)),
					'T223_Tempo_Estimado' => array('name' => 'T223_Tempo_Estimado','label'=>'Tempo Estimado','type' => 'tempo', 'length' => array(5,6)),
					'T223_D110_Id' => array('name' => 'T223_D110_Id', 'label'=>'Competência', 'type' => 'pesquisa', 'readonly'=>true, 'extra'=>'style="width:270px;"', 'length' => array(30,30), 'pesquisa'=>array('D110', 'D110_Id', 'D110_Titulo'),'type'=>'hidden'),
					//'T223_Hora_Inicio' => array('name' => 'T223_Hora_Inicio','label'=>'Hora Início','type' => 'text', 'length' => array(10,10)),
					//'T223_Hora_Fim' => array('name' => 'T223_Hora_Fim','label'=>'Hora Fim','type' => 'text', 'length' => array(10,10)),
					'T223_D116_Id' => array('name' => 'T223_D116_Id', 'label'=>'Veículo', 'extra'=>'style="width:280px;"', 'type' => 'select',
						'select'=> gGeraSelect('D116', 'D116_Id', 'D116_Placa,D133_Modelo,D134_Cor', ' LEFT JOIN D133 on D133_Id=D116_D133_Id LEFT JOIN D134 on D134_Id=D116_D134_Id WHERE D116_D024_Id=(SELECT T218_D024_Id FROM T218 WHERE T218_Id='.@$r_T218_Id.')', true, @$resultVeiculo['T218_D116_Id']),
						'selectRefresh' => array('D116', 'D116_Id', 'D116_Placa,D133_Modelo,D134_Cor', ' LEFT JOIN D133 on D133_Id=D116_D133_Id LEFT JOIN D134 on D134_Id=D116_D134_Id WHERE D116_D024_Id=(SELECT T218_D024_Id FROM T218 WHERE T218_Id='.@$r_T218_Id.')', true, @$resultVeiculo['T218_D116_Id'])
					),
					'est015FormVeiculosBtnNew' => array('name' => 'est015FormVeiculosBtnNew', 'type'=>'submit', 'reload' => false, 'value' => 'Veículos', 'align' => 'right', 'style' => 'float:right;', 'reject' => true),
					'T223_Quantidade' => array('name' => 'T223_Quantidade','label'=>'Quantidade','type' => 'hidden', 'length' => array(10,8), 'value'=>'1'),
					//'T223_Valor_Unitario' => array('name' => 'T223_Valor_Unitario', 'label' => 'Valor Unitário', 'type' => 'hidden', 'length' => array(15,10)),
					'T223_Quantidade_Confirmada' => array('name' => 'T223_Quantidade_Confirmada','label'=>'Quantidade','type' => 'hidden', 'length' => array(10,8), 'value'=>'1'),
					'T223_T218_Id' => array('name' => 'T223_T218_Id', 'type' => 'hidden', 'value' => @$r_T218_Id, 'length' => array(50,30)),
					'T223_D112_Id' => array('name' => 'T223_D112_Id', 'type' => 'hidden', 'label'=>'Serviço', 'value'=>'', 'length' => array(50,30)),
					'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true),
				)
			)
		);
		// Botão Limpar / Novo Registro
		if (!empty($acaoId)) {
			 $formArray['campos']['servicos']['est015FormServicosBtnNew'] = array('name' => 'est015FormServicosBtnNew', 'type'=>'submit', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		}
		$formArray['campos']['servicos']['est015FormServicosBtnCln'] = array('name' => 'est015FormServicosBtnCln', 'type'=>'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['servicos']['est015FormServicosBtnAdd'] = array('name' => 'est015FormServicosBtnAdd', 'type'=>'button', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$form->definirFormularioArray($formArray);

		$sql = "SELECT T218_D024_Id FROM T218 WHERE T218_Id = '{$r_T218_Id}';";
		$resultado = mysql_query($sql);
		$row = mysql_fetch_array($resultado);
		echo <<<EOT
		    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		    	$('#est015FormVeiculosBtnNew').unbind('click');
				$('#est015FormVeiculosBtnNew').unbind('click').bind('click', function(data) {
					var id = '{$row['T218_D024_Id']}';
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
						$retorno = $EST015->insereServico($form, $g['mysqlLastId']);
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
	
	case 'est015FormTrocas':
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
		$form_tabela = 'T222';
		$form_campo_pk = 'T222_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = array('est015FormTrocasBtnAdd', 'est015FormTrocasBtnCln', 'est015FormTrocasBtnNew');
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est015/form/est015FormTrocas/';
        
        //form editor
        $form_editor = array(
            'trocas' => '{"Procura_Produto_Troca":{"top":7,"left":37,"width":328,"hidden":0},"Descricao_Produto":{"top":30,"left":25,"width":345,"hidden":0},"est015FormTrocasBtnAdd":{"top":143,"left":368,"width":null,"hidden":0},"Marca_Produto":{"top":53,"left":44,"width":125,"hidden":0},"est015FormTrocasBtnCln":{"top":143,"left":288,"width":null,"hidden":0},"T222_Quantidade":{"top":76,"left":19,"width":125,"hidden":0},"est015BtnCadastrarProdutoTroca":{"top":143,"left":144,"width":null,"hidden":0},"T222_Operacao":{"top":99,"left":250,"width":124,"hidden":0},"T222_Valor_Preco_Unitario":{"top":99,"left":49,"width":125,"hidden":0},"T222_Observacao":{"top":122,"left":14,"width":345,"hidden":0}}',
        );
		
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
			'est015CampoProcuraProdutoTroca',																	// callback js
			$g['divId'],																						// divId
			(isset($r_est015FormProdutosForm) ? $r_est015FormProdutosForm['T222_T218_Id'] : $r_T218_Id),		// parametro extra
			array(																								// parametros para cada campo do grid
				array('colunaAlinhar' => 'center'),
				array(),
				array(),
				array('callback' => 'gCorrigeNumero', 'colunaAlinhar' => 'right')
			)
		);
		// novo formulário
		$form = new Formulario('est015FormTrocasForm');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'trocas' => array(
					'Procura_Produto_Troca' => array('name' => 'Procura_Produto_Troca' , 'label' => 'Produto', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGrid, 'length' => array(64,120), 'reject' => true),
					'Descricao_Produto' => array('name' => 'Descricao_Produto', 'label' => 'Descrição', 'reject'=>true, 'readonly'=>true, 'type' => 'text', 'length' => array(64, 120)),
					'Marca_Produto' => array('name' => 'Marca_Produto', 'label' => 'Marca', 'type' => 'text', 'length' => array(20,255),'readonly'=>true, 'reject' => true),									
					'T222_Quantidade' => array('name' => 'T222_Quantidade', 'label' => 'Quantidade', 'type' => 'moeda', 'length' => array(20, 30)),
					'T222_Valor_Preco_Unitario' => array('name' => 'T222_Valor_Preco_Unitario', 'label' => 'Valor', 'type' => 'moeda', 'length' => array(20, 30)),
					'T222_Operacao' => array('name' => 'T222_Operacao', 'label' => 'Operação', 'type' => 'select', 'align' => 'right', 'style' => 'float:right;', 'select' => 
						array(
							array('title' => 'Parte Pagamento', 'value' => 'P', 'selected' => true),
							array('title' => 'Troca', 'value' => 'T'),
						)
					),					
					'T222_Observacao' => array('name' => 'T222_Observacao', 'label' => 'Observação', 'type' => 'text', 'length' => array(64, 120)), 
					// Campos Hidden
					'T222_T218_Id' => array('name' => 'T222_T218_Id', 'label' => '', 'type' => 'hidden', 'value' => @$r_T218_Id),
					'T222_C004_Id' => array('name' => 'T222_C004_Id', 'label' => '', 'type' => 'hidden', 'value' => $g['empresaAtual']),
					'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true),
					'T222_D009_Id' => array('name' => 'T222_D009_Id', 'label' => '', 'type' => 'hidden'),

				),
			)
		);
		// Botão Limpar / Novo Registro
		if (!empty($acaoId)) {
			 $formArray['campos']['trocas']['est015FormTrocasBtnNew'] = array('name' => 'est015FormTrocasBtnNew', 'type'=>'submit', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		}
		$formArray['campos']['trocas']['est015FormTrocasBtnAdd'] = array('name' => 'est015FormTrocasBtnAdd', 'type'=>'button', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['trocas']['est015FormTrocasBtnCln'] = array('name' => 'est015FormTrocasBtnCln', 'type'=>'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['trocas']['est015BtnCadastrarProdutoTroca'] = array('name' => 'est015BtnCadastrarProdutoTroca', 'type'=>'button', 'reload' => false, 'value' => 'Pré-Cadastrar Produto', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);	
		$form->definirFormularioArray($formArray);
		
		echo <<<EOT
		    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		    	$('#est015BtnCadastrarProdutoTroca').unbind('click');
				$('#est015BtnCadastrarProdutoTroca').unbind('click').bind('click', function(data) {
					//var id = $('#{$g['divId']} #T218_D024_Id').val();
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
				$retorno = $EST015->incluirProdutoTroca($form);
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
	
	case 'est015FormProcessos':
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
			$enviar_btnId = array('est015FormProcessosBtnAdd', 'est015FormProcessosBtnCln', 'est015FormProcessosBtnNew');
			// Form que será executado ao clicar no botão
			$enviar_acaoHref = '/est/est015/form/est015FormProcessos/';

			// novo formulário
			$form = new Formulario('est015FormProcessosForm');

			// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
			$formArray = array(
				'campos' => array(
					'geral' => array(
					//'T148_T230_Id' => array('name' => 'T148_T230_Id','label'=>'', 'type' => 'hidden','value' => $acaoId, 'length' => array(60,20)),
					//'T146_D110_Id' => array('name' => 'T146_D110_Id','label'=>'Competência', 'type' => 'pesquisa', 'pesquisa' => array('D110', 'D110_Id', 'D110_Titulo') ,'length' => array(50,10)),
					'T146_Titulo' => array('name' => 'T223_Titulo','label'=>'Título','type' => 'text', 'length' => array(50,30)),
					'T146_Descricao' => array('name' => 'T146_Descricao','label'=>'Descrição','type' => 'textarea', 'length' => array(3,47)),
					'T146_T223_Id' => array('name' => 'T146_T223_Id', 'type' => 'hidden', 'value' => @$r_T223_Id, 'length' => array(50,30)),
					'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true),
					)
				)
			);
			
			// Botão Limpar / Novo Registro
			if (!empty($acaoId)) {
				 $formArray['campos']['geral']['est015FormProcessosBtnNew'] = array('name' => 'est015FormProcessosBtnNew', 'type'=>'button', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
			}
			$formArray['campos']['geral']['est015FormProcessosBtnCln'] = array('name' => 'est015FormProcessosBtnCln', 'type'=>'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
			$formArray['campos']['geral']['est015FormProcessosBtnAdd'] = array('name' => 'est015FormProcessosBtnAdd', 'type'=>'submit', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);

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
	
	case 'est015FormMateriais':
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
			$enviar_btnId = array('est015FormMateriaisBtnAdd', 'est015FormMateriaisBtnCln', 'est015FormMateriaisBtnNew');
			// Form que será executado ao clicar no botão
			$enviar_acaoHref = '/est/est015/form/est015FormMateriais/';

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
				'',//(isset($r_est015FormProdutosForm) ? $r_est015FormProdutosForm['T219_T218_Id'] : $r_T218_Id),		// parametro extra
				array(																								// parametros para cada campo do grid
					array('colunaAlinhar' => 'center'),
					array(),
					array(),
					array('callback' => 'gCorrigeNumero', 'colunaAlinhar' => 'right')
				)
			);
			
			// novo formulário
			$form = new Formulario('est015FormMateriaisForm');
			
			// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
			$formArray = array(
				'campos' => array(
					'geral' => array(
						'est015FormMateriaisPesquisa' => array('name' => 'est015FormMateriaisPesquisa' , 'label' => 'Material', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGrid, 'length' => array(20,30), 'reject' => true),
						'Descricao_Produto' => array('name' => 'Descricao_Produto', 'label' => 'Descrição', 'type' => 'text', 'length' => array(64, 120),'reject' => true),
						'est015_Marca' => array('name' => 'est015_Marca', 'label' => 'Marca', 'type' => 'text', 'length' => array(20,255), 'reject' => true),			
						//'T148_T230_Id' => array('name' => 'T148_T230_Id','label'=>'', 'type' => 'hidden','value' => $acaoId, 'length' => array(60,20)),
						'T152_D009_Id' => array('name' => 'T152_D009_Id', 'label' => 'T152_D009_Id', 'type' => 'hidden'),
						'T152_Quantidade' => array('name' => 'T152_Quantidade','label'=>'Quantidade','type' => 'text', 'length' => array(20,10)),
						'T152_D037_Id' => array('name' => 'T152_D037_Id', 'label'=>'Unidade', 'extra'=>'style="width:182px;"', 'type' => 'select', 'select'=>
								gGeraSelect('D037', 'D037_Id', 'D037_Unidade', 'order by D037_Unidade', true),
						),
						'T152_T223_Id' => array('name' => 'T152_T223_Id', 'type' => 'hidden', 'value' => @$r_T223_Id, 'length' => array(50,30)),
						'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true),
					)
				)
			);
			
			// Botão Limpar / Novo Registro
			if (!empty($acaoId)) {
				 $formArray['campos']['geral']['est015FormMateriaisBtnNew'] = array('name' => 'est015FormMateriaisBtnNew', 'type'=>'button', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
			}
			$formArray['campos']['geral']['est015FormMateriaisBtnCln'] = array('name' => 'est015FormMateriaisBtnCln', 'type'=>'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
			$formArray['campos']['geral']['est015FormMateriaisBtnAdd'] = array('name' => 'est015FormMateriaisBtnAdd', 'type'=>'submit', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);

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
	
	
	case 'est015FormTotalOrcamento':
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
		$form_tabela = 'T218';
		$form_campo_pk = 'T218_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = '';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '';
        
        //form editor
		$form_editor = array(
			'geral' => '{"tituloTotal":{"top":4,"left":36,"width":null,"hidden":0},"T218_Valor_Total_Produtos_Sem_Desconto":{"top":30,"left":31,"width":125,"hidden":0},"T218_Valor_Desconto":{"top":53,"left":12,"width":125,"hidden":0},"T218_Valor_Total_Produtos":{"top":76,"left":31,"width":125,"hidden":0},"T218_Valor_ST":{"top":99,"left":56,"width":125,"hidden":0},"T218_Valor_Total_IPI":{"top":122,"left":64,"width":125,"hidden":0},"T218_Valor_Total_Servicos":{"top":145,"left":31,"width":125,"hidden":0},"T218_Valor_Frete":{"top":168,"left":49,"width":125,"hidden":0},"T218_Valor_Despesas_Acessorias":{"top":191,"left":20,"width":125,"hidden":0},"T218_Valor_Total_Agregados":{"top":214,"left":20,"width":125,"hidden":0},"T218_Valor_Troca":{"top":237,"left":40,"width":125,"hidden":0},"T218_Valor_Total":{"top":260,"left":52,"width":125,"hidden":0},"T218_Valor_Total_ICMS":{"top":283,"left":50,"width":125,"hidden":0},"T218_Peso_Total":{"top":306,"left":25,"width":125,"hidden":0},"est015AlcadaOrcamento":{"top":99,"left":96,"width":175,"hidden":0},"est015AlcadaDesconto":{"top":122,"left":96,"width":175,"hidden":0},"est015AlcadaPrazoMedio":{"top":145,"left":96,"width":175,"hidden":0},"est015AlcadaMotivoLiberacao":{"top":168,"left":96,"width":172,"hidden":0}}',
		);

		mysql_query("update T219
          left join T218 on T218_Id=T219_T218_Id
          left join D009 on D009_Id=T219_D009_Id
          left join D049 on D049_Id=D009_D049_Id
          left join D001 on D001_Id=D049_D001_Id
          left join T225 on T219_Id=T225_T219_Id
                set T219_Peso_Unitario=D001_Peso_Unitario_Kg 
              where T218_Id='$r_acaoId'
                and T225_T219_Id is Null");

		$cSql_T219=mysql_query("select T219_Id from T219 where T219_T218_Id='$r_acaoId'");
		while ($mT219=mysql_fetch_array($cSql_T219)) {
		      mysql_query("call T219_Gravar_Totalizacao('$mT219[T219_Id]')");
		}
		mysql_query("call T218_Gravar_Totalizacao_3($r_acaoId);");
		
		//$totalServicos = $EST015->calculaTotalServicos($r_acaoId);
		$totalGeral = $EST015->calculaTotalGeral($r_acaoId);

		
        /*
        * ************** IMPORTANTEEEEEE!!!! *****************
        *  Qualquer coisa que for mudado daqui para baixo até o próximo marcador, precisa ser mudado tbm na função $EST015->verificaAlcada, e no documento est015/form_func-ajax.php -> case 'verificaAlcada';
        *  Se possível, quando der, criar uma única função para os três casos de uso.
        */
		//Calcula Desconto por alçada
		$descontoOrcamento = $EST015->calculaPercentualDescontoOrcamento($r_acaoId);
		$descontoOrcamento = number_format($descontoOrcamento, 2, ".", " ");
		$descontoVendedor = gCorrigeNumeroInverte($g['conf']->ini('alcadaDescontoVendedor'));
		$descontoSupervisor = gCorrigeNumeroInverte($g['conf']->ini('alcadaDescontoSupervisor'));

		$alcadaDescontoVendedorPorProduto = $g['conf']->ini('alcadaDescontoVendedorPorProduto');
		$alcadaDescontoSupervisorPorProduto = $g['conf']->ini('alcadaDescontoSupervisorPorProduto');
		$alcadaDescontoSupervisorPorProduto = gCorrigeNumeroInverte($alcadaDescontoSupervisorPorProduto);
		$alcadaDescontoVendedorPorProduto = gCorrigeNumeroInverte($alcadaDescontoVendedorPorProduto);


		//Calcula prazo médio por alçada
		$prazoMedioAlcadaVendedor = $EST015->calculaPrazoMedioAlcadaVendedor($r_acaoId);
		$prazoMedioAlcadaSupervisor = $EST015->calculaPrazoMedioAlcadaSupervisor($r_acaoId);
		$retorno=mysql_query("SELECT AVG(T220_Prazos_Dias) FROM T220 WHERE T220_T218_Id='{$r_acaoId}'");
		$T218=mysql_fetch_array($retorno);				
		$prazoMedio=$T218['AVG(T220_Prazos_Dias)'];
		$prazoMedio = (empty($prazoMedio)) ? "0" : $prazoMedio;
        $retornoSaldoMinimo = $EST015->verificaFuturoSaldoProduto($r_acaoId,true);

        $T218MaiorDesconto = $EST015->retornaMaiorDescontoOrcamento($r_acaoId);
        $T218MaiorDesconto = number_format($T218MaiorDesconto, 2, ".", " ");
        //die("Maior desconto: ".$T218MaiorDesconto);

        $usarAlcadaPorProduto = $g['conf']->ini('usarAlcadaPorProduto');

        //die($usarAlcadaPorProduto);
        //Verificações de alçada
        // if($T218MaiorDesconto <= $alcadaDescontoSupervisorPorProduto){
        // 	echo "SIM";
        // }else{
        // 	echo "Não";
        // }
        // die(		   $T218MaiorDesconto . " <= " . $alcadaDescontoSupervisorPorProduto. 
        // 	"<br />" . $descontoOrcamento . " <=" . $descontoSupervisor.
        // 	"<br />" . $prazoMedio . "<=" . $prazoMedioAlcadaSupervisor.
        // 	"<br />" . $usarAlcadaPorProduto . " == S".
        // 	"<br />" . $retornoSaldoMinimo . "== 0");
		if(($descontoOrcamento <= $descontoVendedor) && ($prazoMedio <= $prazoMedioAlcadaVendedor) && ($retornoSaldoMinimo==0)){

			$prazoMedio=gCorrigeNumero($T218['AVG(T220_Prazos_Dias)']);
			$descontoOrcamentoUsar = gCorrigeNumero($descontoOrcamento);
			$T218MaiorDescontoUsar = gCorrigeNumero($T218MaiorDesconto);
			$alcadaOrcamento = "Alçada: Vendedor";
			$alcadaDesconto = "Desconto: {$descontoOrcamentoUsar}%";
			$alcadaMaiorDesconto = "Maior Desconto: {$T218MaiorDescontoUsar}%";
			$alcadaPrazoMedio = "Prazo Médio: {$prazoMedio} Dias";

		} else if (($descontoOrcamento <= $descontoSupervisor) && ($prazoMedio <= $prazoMedioAlcadaSupervisor)){

            $prazoMedio=gCorrigeNumero($T218['AVG(T220_Prazos_Dias)']);
			$descontoOrcamentoUsar = gCorrigeNumero($descontoOrcamento);
			$T218MaiorDescontoUsar = gCorrigeNumero($T218MaiorDesconto);
			$alcadaOrcamento = "Alçada: Supervisor";
			$alcadaDesconto = "Desconto: {$descontoOrcamentoUsar}%";
			$alcadaMaiorDesconto = "Maior Desconto: {$T218MaiorDescontoUsar}%";
			$alcadaPrazoMedio = "Prazo Médio: {$prazoMedio} Dias";

		}else{

			$prazoMedio=gCorrigeNumero($T218['AVG(T220_Prazos_Dias)']);
			$descontoOrcamentoUsar = gCorrigeNumero($descontoOrcamento);
			$T218MaiorDescontoUsar = gCorrigeNumero($T218MaiorDesconto);
			$alcadaOrcamento = "Alçada: Diretor";
			$alcadaDesconto = "Desconto: {$descontoOrcamentoUsar}%";
			$alcadaMaiorDesconto = "Maior Desconto: {$T218MaiorDescontoUsar}%";
			$alcadaPrazoMedio = "Prazo Médio: {$prazoMedio} Dias";
		}

		// Verificação apenas para quando a global $usarAlcadaPorProduto for igual a sim
		if($usarAlcadaPorProduto == "S"){

			if($T218MaiorDesconto <= $alcadaDescontoVendedorPorProduto){

				$prazoMedio=gCorrigeNumero($T218['AVG(T220_Prazos_Dias)']);
				$descontoOrcamentoUsar = gCorrigeNumero($descontoOrcamento);
				$T218MaiorDescontoUsar = gCorrigeNumero($T218MaiorDesconto);
				$alcadaOrcamento = "Alçada: Vendedor";
				$alcadaDesconto = "Desconto: {$descontoOrcamentoUsar}%";
				$alcadaMaiorDesconto = "Maior Desconto: {$T218MaiorDescontoUsar}%";
				$alcadaPrazoMedio = "Prazo Médio: {$prazoMedio} Dias";

			}else if($T218MaiorDesconto <= $alcadaDescontoSupervisorPorProduto){

				$prazoMedio=gCorrigeNumero($T218['AVG(T220_Prazos_Dias)']);
				$descontoOrcamentoUsar = gCorrigeNumero($descontoOrcamento);
				$T218MaiorDescontoUsar = gCorrigeNumero($T218MaiorDesconto);
				$alcadaOrcamento = "Alçada: Supervisor";
				$alcadaDesconto = "Desconto: {$descontoOrcamentoUsar}%";
				$alcadaMaiorDesconto = "Maior Desconto: {$T218MaiorDescontoUsar}%";
				$alcadaPrazoMedio = "Prazo Médio: {$prazoMedio} Dias";

			}else{

				$prazoMedio=gCorrigeNumero($T218['AVG(T220_Prazos_Dias)']);
				$descontoOrcamentoUsar = gCorrigeNumero($descontoOrcamento);
				$T218MaiorDescontoUsar = gCorrigeNumero($T218MaiorDesconto);
				$alcadaOrcamento = "Alçada: Diretor";
				$alcadaDesconto = "Desconto: {$descontoOrcamentoUsar}%";
				$alcadaMaiorDesconto = "Maior Desconto: {$T218MaiorDescontoUsar}%";
				$alcadaPrazoMedio = "Prazo Médio: {$prazoMedio} Dias";
			}
		}
        
        $motivoPedidoLiberacao= '';
        $retornoStatus=mysql_query("SELECT * FROM T218 WHERE T218_Id='{$r_acaoId}'");
        $T218Status=mysql_fetch_array($retornoStatus);
        
        if(!empty($T218Status['T218_Motivo_Pedido_Liberacao'])){
            $motivoPedidoLiberacao .= $T218Status['T218_Motivo_Pedido_Liberacao'];
        }else{
            $motivoPedidoLiberacao .= $EST015->motivoPedidoLiberacao($r_acaoId);
        }

        $totalConfirmado=$T218Status['T218_Valor_Total_Produtos_Confirmado'];
        $totalOrcamento=$T218Status['T218_Valor_Total_Produtos'];        
        $total='';
        if ($totalConfirmado<=0){
            $total=$totalOrcamento;
        }else{
            $total=$totalConfirmado;
        }

        if ($T218Status['T218_Liberou_Alcada_C007_Id']>0 && $T218Status['T218_Liberou_Alcada_Data']>0 && ( $T218Status['T218_Liberou_Alcada_Desconto']>=0 || $T218Status['T218_Liberou_Alcada_Prazo_Medio']>=0)){
	        if ($T218Status['T218_Liberou_Alcada_Valor_Total'] == $total || $T218Status['T218_Liberou_Alcada_Prazo_Medio'] == $prazoMedio){
	    		//$descontoOrcamento = gCorrigeNumero($descontoOrcamento);
	    		$T218MaiorDesconto = gCorrigeNumero($T218MaiorDesconto);
				$alcadaOrcamento = "Alçada: aprovado";
				$alcadaDesconto = "Desconto: {$descontoOrcamento}%";
				$alcadaMaiorDesconto = "Maior Desconto: {$T218MaiorDesconto}%";
				$alcadaPrazoMedio = "Prazo Médio: {$prazoMedio} Dias";  	
	        }
	    }
        /*
        * 
        *Aqui termina verificação de alçadaa-------------------- 
        * 
        */
		
		if($g['C031']['habilitaEmail'] == 'S'){
			$exibirText = 'text';
			$exibirMoeda = 'moeda';
			array_push($form_editor['geral'], array('T218A_Desconto_Cotacao' => "{'top':326,'left':5,'width':125,'hidden':0}", 'T218A_Flag_Desconto_Tela' => "{'top':348,'left':20,'width':125,'hidden':0}"));

		}else{
			$exibirText = 'hidden';
			$exibirMoeda = 'hidden';
		}


		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					'tituloTotal' => array('name' => 'tituloTotal', 'type' => 'titulo', 'label' => 'Total Orçamento', 'reject' => true,  'extra'=>'style=background:#E8E8E8;'),
					'T218_Valor_Total_Produtos_Sem_Desconto' => array('name' => 'T218_Valor_Total_Produtos_Sem_Desconto', 'label' => 'Produtos', 'type' => 'moeda', 'length' => array(20,40), 'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'),
					'T218_Valor_Desconto' => array('name' => 'T218_Valor_Desconto', 'label' => 'Desconto R$', 'type' => 'moeda', 'colunaCasasDecimais'=>'2', 'length' => array(20,40),'extra' => "onchange=\"gravarDesconto(this, '$r_divReload', '{$r_T218_Id}', 'T218_Valor_Desconto');\"",'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'),
					'T218_Valor_Total_Produtos' => array('name' => 'T218_Valor_Total_Produtos', 'label' => 'Produtos', 'type' => 'moeda', 'length' => array(20,40), 'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'),
					'T218_Valor_ST' => array('name' => 'T218_Valor_ST', 'label' => 'S.T.', 'type' => 'moeda', 'length' => array(20,40), 'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'),
					'T218_Valor_Total_IPI' => array('name' => 'T218_Valor_Total_IPI', 'label' => 'IPI', 'type' => 'moeda', 'length' => array(20,40), 'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'), 
					'T218_Valor_Total_Servicos' => array('name' => 'T218_Valor_Total_Servicos', 'label' => 'Serviços', 'type' => 'moeda', 'length' => array(20,40), 'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'),
					'T218_Valor_Frete' => array('name' => 'T218_Valor_Frete', 'label' => 'Frete', 'type' => 'moeda', 'length' => array(20,40), 'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'),
					'T218_Valor_Despesas_Acessorias' => array('name' => 'T218_Valor_Despesas_Acessorias', 'label' => 'Dp. Acess.', 'type' => 'moeda', 'length' => array(20,40), 'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'),
					'T218_Valor_Total_Agregados' => array('name' => 'T218_Valor_Total_Agregados', 'label' => 'Agregados', 'type' => 'moeda', 'length' => array(20,40), 'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'),
					'T218_Valor_Troca' => array('name' => 'T218_Valor_Troca', 'label' => 'Trocas', 'type' => 'moeda', 'length' => array(20,40), 'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'),
					'T218_Valor_Total' => array('name' => 'T218_Valor_Total', 'label' => 'Total', 'type' => 'moeda', 'length' => array(20,40), 'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'),
					'T218_Valor_Total_ICMS' => array('name' => 'T218_Valor_Total_ICMS', 'label' => 'ICMS', 'type' => 'moeda', 'length' => array(20, 40), 'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'),
					'T218_Peso_Total' => array('name' => 'T218_Peso_Total', 'label' => 'Peso Total', 'type' => 'text', 'length' => array(20,40), 'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'),
					'est015AlcadaOrcamento' => array('name' => 'est015AlcadaOrcamento', 'label' => '','type' => 'text','reject' => true,'value' => $alcadaOrcamento, 'length' => array(20,40), 'disabled' => 'true',  'extra'=>'style=margin-left:200px;:right;background:yellow;border-color: var(--BGVermelho);','length' => array(30,30)),
					'est015AlcadaDesconto' => array('name' => 'est015AlcadaDesconto', 'label' => '','type' => 'text','reject' => true,'value' => $alcadaDesconto, 'length' => array(20,40), 'disabled' => 'true',  'extra'=>'style=margin-left:200px;:right;background:yellow;border-color: var(--BGVermelho);','length' => array(30,30)),
					'est015AlcadaDescontoProduto' => array('name' => 'est015AlcadaDescontoProduto', 'label' => '','type' => 'text','reject' => true,'value' => $alcadaMaiorDesconto, 'length' => array(20,40), 'disabled' => 'true',  'extra'=>'style=margin-left:200px;:right;background:yellow;border-color: var(--BGVermelho);','length' => array(30,30)),
					'est015AlcadaPrazoMedio' => array('name' => 'est015AlcadaPrazoMedio', 'label' => '', 'type' => 'text','reject' => true,'value' => $alcadaPrazoMedio, 'length' => array(20,40), 'disabled' => 'true',  'extra'=>'style=margin-left:200px;:right;background:yellow;border-color: var(--BGVermelho);','length' => array(30,30)),
                    'est015AlcadaMotivoLiberacao' => array('name' => 'est015AlcadaMotivoLiberacao', 'label' => '', 'type' => 'textarea','reject' => true,'value' => $motivoPedidoLiberacao, 'length' => array(5,26), 'disabled' => 'true',  'extra'=>'style=margin-left:200px;:right;background:yellow;border-color: var(--BGVermelho);'),
					//'T218_IPV' => array('name' => 'T218_IPV', 'label' => 'IPV', 'type' => 'moeda', 'length' => array(20,40), 'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'),   
					'T218A_Desconto_Cotacao' => array('name' => 'T218A_Desconto_Cotacao', 'label'=>'Desconto da Cotação', 'disabled' => 'true', 'type' => $exibirMoeda, 'length' => array(20,40)),
					'T218A_Flag_Desconto_Tela' => array('name' => 'T218A_Flag_Desconto_Tela', 'label'=>'Tipo de Desconto', 'disabled' => 'true', 'type' => $exibirText, 'length' => array(20,40)),

				)
			)
		);
        $form = new Formulario('form-est015FormTotais');
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
	
		case 'est015FormTotalConfirmado':

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
		$form_tabela = 'T218';
		$form_campo_pk = 'T218_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = '';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '';
        
        //form editor
        $form_editor = array(
            'geral2' => '{"tituloTotal":{"top":4,"left":9,"width":null,"hidden":0},"T218_Valor_Total_Produtos_Sem_Desconto_Confirmado":{"top":30,"left":31,"width":125,"hidden":0},"T218_Valor_Desconto_Confirmado":{"top":53,"left":12,"width":125,"hidden":0},"T218_Valor_Total_Produtos_Confirmado":{"top":76,"left":31,"width":125,"hidden":0},"T218_Valor_ST_Confirmado":{"top":99,"left":56,"width":125,"hidden":0},"T218_Valor_Total_IPI_Confirmado":{"top":122,"left":64,"width":125,"hidden":0},"T218_Valor_Total_Servicos":{"top":145,"left":31,"width":125,"hidden":0},"T218_Valor_Frete":{"top":168,"left":49,"width":125,"hidden":0},"T218_Valor_Despesas_Acessorias":{"top":191,"left":20,"width":125,"hidden":0},"T218_Valor_Troca":{"top":214,"left":40,"width":125,"hidden":0},"T218_Valor_Total_Confirmado":{"top":237,"left":52,"width":125,"hidden":0},"T218_Valor_Total_ICMS_Confirmado":{"top":260,"left":50,"width":125,"hidden":0},"T218_Peso_Total_Confirmado":{"top":283,"left":25,"width":125,"hidden":0}}',
        );
        
		//$totalServicosConfirmado = $EST015->calculaTotalServicosConfirmado($r_acaoId);
		$totalGeralConfirmado = $EST015->calculaTotalGeralConfirmado($r_acaoId);
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral2' => array(
					// Campo normal (do banco)
					'tituloTotal' => array('name' => 'tituloTotal', 'type' => 'titulo', 'label' => 'Total Confirmado', 'reject' => true),
					'T218_Valor_Total_Produtos_Sem_Desconto_Confirmado' => array('name' => 'T218_Valor_Total_Produtos_Sem_Desconto_Confirmado', 'label' => 'Produtos', 'type' => 'moeda', 'length' => array(20,40), 'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'),
					'T218_Valor_Desconto_Confirmado' => array('name' => 'T218_Valor_Desconto_Confirmado', 'label' => 'Desconto R$', 'type' => 'moeda', 'length' => array(20,40),'extra' => "onchange=\"gravarDesconto(this, '{$r_divReload}', '{$r_T218_Id}', 'T218_Valor_Desconto_Confirmado');\"",'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'),
					'T218_Valor_Total_Produtos_Confirmado' => array('name' => 'T218_Valor_Total_Produtos_Confirmado', 'label' => 'Produtos', 'type' => 'moeda', 'length' => array(20,40), 'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'),
					'T218_Valor_ST_Confirmado' => array('name' => 'T218_Valor_ST_Confirmado', 'label' => 'S.T.', 'type' => 'moeda', 'length' => array(20,40), 'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'),
					'T218_Valor_Total_IPI_Confirmado' => array('name' => 'T218_Valor_Total_IPI_Confirmado', 'label' => 'IPI', 'type' => 'moeda', 'length' => array(20,40), 'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'),
					'T218_Valor_Total_Servicos' => array('name' => 'T218_Valor_Total_Servicos', 'label' => 'Serviços', 'type' => 'moeda', 'length' => array(20,40), 'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'),
					'T218_Valor_Frete' => array('name' => 'T218_Valor_Frete', 'label' => 'Frete', 'type' => 'moeda', 'length' => array(20,40), 'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'),
					'T218_Valor_Despesas_Acessorias' => array('name' => 'T218_Valor_Despesas_Acessorias', 'label' => 'Dp. Acess.', 'type' => 'moeda', 'length' => array(20,40), 'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'),
					'T218_Valor_Troca' => array('name' => 'T218_Valor_Troca', 'label' => 'Trocas', 'type' => 'moeda', 'length' => array(20,40), 'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'),
					'T218_Valor_Total_Confirmado' => array('name' => 'T218_Valor_Total_Confirmado', 'label' => 'Total','reject' => true,'value' => gCorrigeNumero($totalGeralConfirmado), 'type' => 'moeda', 'length' => array(20,40), 'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'),
     				'T218_Valor_Total_ICMS_Confirmado' => array('name' => 'T218_Valor_Total_ICMS_Confirmado', 'label' => 'ICMS', 'type' => 'moeda', 'length' => array(20, 40), 'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'),
					'T218_Peso_Total_Confirmado' => array('name' => 'T218_Peso_Total_Confirmado', 'label' => 'Peso Total', 'type' => 'text', 'length' => array(20,40), 'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'),
				)
			)
		);
        $form = new Formulario('form-est015FormTotais2');
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
	
	case 'est015FormProdutosEditar':
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$T218_Id = (isset($r_T218_Id)) ? $r_T218_Id : false;

		/**
		 * PARTE 2: Definição do formulário
		 */
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = true;
		if ($formMultiTab) {
			$formId = (isset($r_est015FormProdutosForm)) ? $r_est015FormProdutosForm['formId'] : $r_formId;
		} else {
			$formId = uniqid();
		}
		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'T219';
		$form_campo_pk = 'T219_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est015FormEditarProdutosBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est015/form/est015FormProdutosEditar/';
        
        //form editor
        $form_editor = array(
            'item' => '{"est015FormProdutosEditarProcuraProdutoEditar":{"top":7,"left":68,"width":344,"hidden":0},"Marca":{"top":30,"left":68,"width":136,"hidden":0},"T219_Codigo_Substituto":{"top":30,"left":278,"width":120,"hidden":0},"T219_Valor_Preco_Original":{"top":30,"left":679,"width":128,"hidden":1},"T219_Descricao_Produto":{"top":53,"left":49,"width":360,"hidden":0},"T219_Quantidade":{"top":76,"left":43,"width":125,"hidden":0},"T219_D037_Id":{"top":76,"left":299,"width":127,"hidden":0},"T219_Valor_Preco_Unitario":{"top":99,"left":37,"width":125,"hidden":0},"T219_Valor_Custo_Unitario":{"top":99,"left":277,"width":120,"hidden":0},"T219_Numero_Pedido_Compra":{"top":122,"left":47,"width":125,"hidden":0},"T219_Item_Pedido_Compra":{"top":122,"left":305,"width":120,"hidden":0},"T219_Observacao":{"top":145,"left":45,"width":360,"hidden":0},"T219_D006_Id":{"top":168,"left":75,"width":368,"hidden":0},"T219_D005_Id":{"top":191,"left":74,"width":143,"hidden":0},"T219_Flag_Origem_Produto":{"top":214,"left":9,"width":368,"hidden":0}}',
            'ipi' => '{"T219_D005_Id":{"top":7,"left":93,"width":272,"hidden":0},"T219_Aliquota_IPI":{"top":30,"left":99,"width":104,"hidden":0},"T219_Situacao_Tributaria_IPI":{"top":53,"left":19,"width":270,"hidden":0}}',
            'icms' => '{"T219_Aliquota_ICMS":{"top":7,"left":88,"width":100,"hidden":0},"T219_Percentual_Reducao_ICMS":{"top":30,"left":98,"width":100,"hidden":0},"T219_D059_Id":{"top":53,"left":64,"width":288,"hidden":0},"T219_Flag_ST":{"top":76,"left":47,"width":56,"hidden":0},"T219_ST_ICMS_Interno":{"top":99,"left":52,"width":100,"hidden":0},"T219_ST_ICMS_Interno_MVA":{"top":99,"left":314,"width":100,"hidden":1},"T219_ICMS_Inter_Estadual":{"top":122,"left":18,"width":100,"hidden":0},"T219_ICMS_Inter_Estadual_MVA":{"top":122,"left":280,"width":100,"hidden":1},"T219_ST_VA":{"top":145,"left":131,"width":100,"hidden":0}}',
            'pis-cofins' => '{"T219_Aliquota_PIS":{"top":7,"left":110,"width":100,"hidden":0},"T219_Flag_Isento_PIS":{"top":30,"left":109,"width":56,"hidden":0},"T219_Situacao_Tributaria_PIS":{"top":53,"left":30,"width":288,"hidden":0},"T219_Aliquota_COFINS":{"top":76,"left":88,"width":100,"hidden":0},"T219_Flag_Isento_COFINS":{"top":99,"left":109,"width":56,"hidden":0},"T219_Situacao_Tributaria_COFINS":{"top":122,"left":8,"width":288,"hidden":0}}'
        );
		
		// Campo: Procura_Produto
		$pesquisaGridExtra = <<<EOT
			left join D049 on D049_D001_Id=D001_Id
			left join D041 on D041_D049_Id=D049_Id
			left join D009 on D009_D049_Id=D049_Id
			left join D082 on D082_Id=D049_D082_Id
			left join D037 on D037_Id=D001_D037_Id 
			LEFT JOIN D083 on D083_D001_Id=D001_Id AND D083_D024_Id='{$mT218['T218_D024_Id']}'
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
			'est015CampoProcuraProdutoEditar',																					          // callback js
			$g['divId'],																									              // divId
			(isset($r_est015FormProdutosForm) ? $r_est015FormProdutosForm['T219_T218_Id'] : $r_T218_Id),					              // parametro extra
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
		
		$T219=mysql_query("select * 
		                    from T219 
	                   left join D009 on D009_Id=T219_D009_Id
	                   left join D049 on D049_Id=D009_D049_Id
	                   left join D082 on D082_Id=D049_D082_Id
		                   where T219_Id='$acaoId'");
		$mT219=mysql_fetch_array($T219);

		$descricaoCFOP  = ($g['C031']['descricaoCFOPExibida'] == 'P') ? 'D006_Descricao' : 'IF(D006_Descricao_Comercial !="", D006_Descricao_Comercial, D006_Descricao) as D006_Descricao';

		// novo formulário
		$form = new Formulario('est015FormProdutosFormEditar');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'item' => array(
					'est015FormProdutosEditarProcuraProdutoEditar' => array('name' => 'est015FormProdutosEditarProcuraProdutoEditar', 'label' => 'Produto','type' => 'pesquisaGrid', 'reject' => true, 'pesquisaGrid' => $pesquisaGrid, 'length' => array(20,30), 'value' => $mT219['T219_Codigo_Produto']),
					'Marca' => array('name' => 'Marca', 'label' => 'Marca', 'type' => 'text', 'length' => array(20,255), 'reject' => true, 'disabled'=>'disabled', 'value' => "$mT219[D082_Marca]"),
					'T219_Codigo_Substituto' => array('name' => 'T219_Codigo_Substituto', 'label' => 'Código cliente', 'type' => 'text', 'length' => array(20, 120), 'align' => 'right', 'style' => 'float:right;'),
					'T219_Descricao_Produto' => array('name' => 'T219_Descricao_Produto', 'label' => 'Descrição', 'type' => 'text', 'length' => array(64, 120)),
					'T219_Quantidade' => array('name' => 'T219_Quantidade', 'label' => 'Quantidade', 'type' => 'moeda', 'casasDecimais' => '3', 'length' => array(20, 30)),
					'T219_D037_Id' => array('name' => 'T219_D037_Id', 'label' => 'Unidade', 'type' => 'select', 'align' => 'right', 'style' => 'float:right;', 'select' => 
						gGeraSelect('D037', 'D037_Id', 'D037_Unidade', '', true)
					),
					'T219_Valor_Preco_Unitario' => array('name' => 'T219_Valor_Preco_Unitario', 'label' => 'Preço Unitário', 'type' => 'moeda', 'length' => array(20, 30), 'restricao' => false),
					'T219_Valor_Custo_Unitario' => array('name' => 'T219_Valor_Custo_Unitario', 'label' => 'Custo Unitário', 'type' => 'moeda', 'align' => 'right', 'style' => 'float:right;', 'length' => array(20, 30)),
					'T219_Valor_Preco_Original' => array('name' => 'T219_Valor_Preco_Original', 'label' => 'Preço Original', 'type' => 'moeda', 'align' => 'right', 'style' => 'float:right;', 'length' => array(20, 30)),
					'T219_Numero_Pedido_Compra' => array('name' => 'T219_Numero_Pedido_Compra', 'label' => 'Número OC', 'type' => 'text', 'length' => array(20, 120)),
					'T219_Item_Pedido_Compra' => array('name' => 'T219_Item_Pedido_Compra', 'label' => 'Item OC', 'type' => 'text', 'length' => array(20, 120), 'align' => 'right', 'style' => 'float:right;'),
					'T219_Observacao' => array('name' => 'T219_Observacao', 'label' => 'Observação', 'type' => 'text', 'length' => array(65, 120)),
					'T219_D006_Id' => array('name' => 'T219_D006_Id', 'restricao' => false, 'label' => 'CFOP', 'type' => 'select',
						'select' => gGeraSelect('D006', 'D006_Id', "LPAD(D006_Id[[VIRGULA]]4[[VIRGULA]]0), D006_Codigo_CFOP, {$descricaoCFOP}", 'WHERE D006_Flag_Inativo!="S" AND D006_Flag_Departamento="COMERCIAL" ORDER BY D006_Codigo_CFOP', true, false, '%1 - %2 - %3'),
					),
					'T219_D005_Id' => array('name' => 'T219_D005_Id', 'label' => 'NCM', 'type' => 'select',  'select' =>
						gGeraSelect('D005', 'D005_Id', 'D005_Classificacao_Fiscal, D149_Aliquota_IPI, D005_Flag_Aplicacao', "LEFT JOIN D149 ON D149_D005_Id=D005_Id AND D149_C004_Id='{$g['empresaAtual']}' WHERE D005_Flag_Ativo='S'", true)
					),
					'T219_Flag_Origem_Produto' => array('name' => 'T219_Flag_Origem_Produto', 'label' => 'Origem mecadoria', 'type'=>'select', 'select' => 
						array(
							array('title' => '...', 'value'=>''),
							array('title' => '0 - Nacional - exceto as indicadas nos códigos 3 a 5', 'value'=>'0'),
							array('title' => '1 - Estrangeira - Importação direta, exceto a indicada no código 6', 'value' =>'1'),
							array('title' => '2 - Estrangeira - Adquirida no mercado interno, exceto a indicada no código 7', 'value'=>'2'),
							array('title' => '3 - Nacional - mercadoria ou bem com Conteúdo de Importação superior a 40%', 'value' =>'3'),
							array('title' => '4 - Nacional - cuja produção tenha sido feita em conformidade com os processos produtivos básicos de que tratam as legislações citadas nos Ajustes', 'value'=>'4'),
							array('title' => '5 - Nacional - mercadoria ou bem com Conteúdo de Importação inferior ou igual a 40%', 'value' =>'5'),
							array('title' => '6 - Estrangeira - Importação direta, sem similar nacional, constante em lista da CAMEX', 'value'=>'6'),
							array('title' => '7 - Estrangeira - Adquirida no mercado interno, sem similar nacional, constante em lista da CAMEX', 'value' =>'7')
						)
					),

					// Campos Hidden
					'T219_T218_Id' => array('name' => 'T219_T218_Id', 'label' => 'T219_T218_Id', 'type' => 'hidden' ),
					'T219_D082_Id' => array('name' => 'T219_D082_Id', 'label' => 'T219_D082_Id', 'type' => 'hidden' ),
					'T219_Codigo_Produto' => array('name' => 'T219_Codigo_Produto', 'label' => 'Código Produto', 'type' => 'hidden'),
					'T219_C004_Id' => array('name' => 'T219_C004_Id', 'label' => 'C004_Id', 'type' => 'hidden', 'value' => $g['empresaAtual']),
					'T219_D009_Id' => array('name' => 'T219_D009_Id', 'label' => 'D009_Id', 'type' => 'hidden'),
					'T219_Id' => array('name' => 'T219_Id', 'label' => '', 'type' => 'hidden','value' => $acaoId),
					'formId' => array('name' => 'formId', 'label' => '', 'type' => 'hidden', 'value' => $formId, 'reject' => true),
				),
				//TAB IPI
				'ipi' => array(
					'T219_Aliquota_IPI' => array('name' => 'T219_Aliquota_IPI', 'label' => 'IPI %', 'type' => 'moeda', 'length' => array(15, 20)),
					'T219_Situacao_Tributaria_IPI' => array('name' => 'T219_Situacao_Tributaria_IPI', 'label' => 'Situação Tributária IPI', 'type' => 'select',  'select' => 
						gGeraSelect('D088', 'D088_Codigo_ST', 'D088_Codigo_ST,D088_Descricao', '', true)
					),
				),
				//TAB ICMS
				'icms' => array(
					'T219_Aliquota_ICMS' => array('name' => 'T219_Aliquota_ICMS', 'label' => 'Aliquota ICMS', 'type' => 'moeda', 'length' => array(15, 20)),
					'T219_Percentual_Reducao_ICMS' => array('name' => 'T219_Percentual_Reducao_ICMS', 'label' => '% Redução', 'type' => 'moeda', 'length' => array(15, 20)),
					'T219_Percentual_Reducao_ICMS_ST' => array('name' => 'T219_Percentual_Reducao_ICMS_ST', 'label' => '% Redução ST', 'type' => 'moeda', 'length' => array(15, 20)),
					'T219_D059_Id' => array('name' => 'T219_D059_Id', 'label' => 'Situação Tributária', 'type' => 'select', 'extra'=>'style="width:288px;"',  'select' => 
					gGeraSelect('D059', 'D059_Id', 'D059_Codigo_ST,D059_Descricao_Origem', '', true)
					),
					'T219_Flag_ST' => array('name' => 'T219_Flag_ST', 'label' => 'Substituição Tributária', 'type' => 'select', 'select' => 
					array(
					array('title' => 'Sim', 'value' => 'S'),
					array('title' => 'Não', 'value' => 'N', 'selected' => true)
					)
					),
					'T219_ST_ICMS_Interno' => array('name' => 'T219_ST_ICMS_Interno', 'label' => 'Aliquota ICMS Interno', 'type' => 'moeda', 'length' => array(15, 20)),
					'T219_ST_ICMS_Interno_MVA' => array('name' => 'T219_ST_ICMS_Interno_MVA', 'label' => 'Aliquota ICMS Interno MVA', 'type' => 'moeda', 'length' => array(15, 20), 'align'=>'right', 'style'=>'float:right'),
					'T219_ICMS_Inter_Estadual' => array('name' => 'T219_ICMS_Inter_Estadual', 'label' => 'Aliquota ICMS Inter Estadual', 'type' => 'moeda', 'length' => array(15, 20)),
					'T219_ICMS_Inter_Estadual_MVA' => array('name' => 'T219_ICMS_Inter_Estadual_MVA', 'label' => 'Aliquota ICMS Inter Estadual MVA', 'type' => 'moeda', 'length' => array(15, 20), 'align'=>'right', 'style'=>'float:right'),
					'T219_ST_VA' => array('name' => 'T219_ST_VA', 'label' => 'MVA', 'type' => 'moeda', 'length' => array(15, 20)),
				),
				//TAB PIS/COFINS
				'pis-cofins' => array(
					'T219_Aliquota_PIS' => array('name' => 'T219_Aliquota_PIS', 'label' => 'PIS %', 'type' => 'moeda', 'length' => array(15, 20)),
					'T219_Flag_Isento_PIS' => array('name' => 'T219_Flag_Isento_PIS', 'label' => 'Isento', 'type' => 'select', 'select' => 
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N', 'selected' => true)
						)
					),
					'T219_Situacao_Tributaria_PIS' => array('name' => 'T219_Situacao_Tributaria_PIS', 'label' => 'Situação Tributária PIS', 'extra'=>'style="width:288px;"', 'type' => 'select', 'select' =>
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Operação Tributável (base de cálculo = valor da operação alíquota normal (cumulativo/não cumulativo))', 'value' => '01'),
							array('title' => 'Operação Tributável (base de cálculo = valor a operação(alíquota diferenciada))', 'value' => '02'),
							array('title' => 'Operação Tributável (base de cálculo = quantidade Vendida x alíquota por unidade de produto)', 'value' => '03'),
							array('title' => 'Operação Tributável (tributação monofísica (alíquota zero))', 'value' => '04'),
							array('title' => 'Operação Tributável (alíquota zero)', 'value' => '06'),
							array('title' => 'Operação Isenta da Contribuição', 'value' => '07'),
							array('title' => 'Operação Sem Incidência da Contribuição', 'value' => '08'),
							array('title' => 'Operação com Suspensão da Contribuição', 'value' => '09'),
							array('title' => 'Outras Operações', 'value' => '99'),
						)
					),
					
					'T219_Aliquota_COFINS' => array('name' => 'T219_Aliquota_COFINS', 'label' => 'COFINS %', 'type' => 'moeda', 'length' => array(15, 20)),
					
					'T219_Flag_Isento_COFINS' => array('name' => 'T219_Flag_Isento_COFINS', 'label' => 'Isento', 'type' => 'select', 'select' => 
						array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N', 'selected' => true)
						)
					),
					'T219_Situacao_Tributaria_COFINS' => array('name' => 'T219_Situacao_Tributaria_COFINS', 'label' => 'Situação Tributária COFINS', 'extra'=>'style="width:288px;"', 'type' => 'select', 'select' =>
						array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Operação Tributável (base de cálculo = valor da operação alíquota normal (cumulativo/não cumulativo))', 'value' => '01'),
							array('title' => 'Operação Tributável (base de cálculo = valor a operação(alíquota diferenciada))', 'value' => '02'),
							array('title' => 'Operação Tributável (base de cálculo = quantidade Vendida x alíquota por unidade de produto)', 'value' => '03'),
							array('title' => 'Operação Tributável (tributação monofísica (alíquota zero))', 'value' => '04'),
							array('title' => 'Operação Tributável (alíquota zero)', 'value' => '06'),
							array('title' => 'Operação Isenta da Contribuição', 'value' => '07'),
							array('title' => 'Operação Sem Incidência da Contribuição', 'value' => '08'),
							array('title' => 'Operação com Suspensão da Contribuição', 'value' => '09'),
							array('title' => 'Outras Operações', 'value' => '99'),
						)
					),
				)
			)
		);
		// Botão Limpar / Novo Registro
		if (!empty($acaoId)) {
			 $formArray['campos']['produto']['est015FormProdutosBtnNew'] = array('name' => 'est015FormProdutosBtnNew', 'type'=>'submit', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		}
		$formArray['campos']['produto']['est015FormProdutosBtnCln'] = array('name' => 'est015FormProdutosBtnCln', 'type'=>'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['produto']['est015FormProdutosBtnAdd'] = array('name' => 'est015FormProdutosBtnAdd', 'type'=>'button', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
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
						//die($sql);
						//Calcula valor do desconto

						mysql_query("CALL T219_Gravar_Totalizacao('{$acaoId}')");
		                      
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
	case 'est015FormTotalOrcamento':
	
	
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
		$form_tabela = 'T218';
		$form_campo_pk = 'T218_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = '';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '';
		
		$cSql_T219=mysql_query("select T219_Id from T219 where T219_T218_Id='$r_acaoId'");
		while ($mT219=mysql_fetch_array($cSql_T219)) {
		      mysql_query("call T219_Gravar_Totalizacao('$mT219[T219_Id]')");
		}
		mysql_query("call T218_Gravar_Totalizacao($r_acaoId);");
		
		
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					'IPV_Minimo' => array('name' => 'IPV_Minimo', 'label' => 'Importar Orçamentos', 'type' => 'moeda', 'length' => array(40, 80), 'disabled' => 'true'),
					'T218_Id' => array('name' => 'T218_Id', 'label' => 'S.T.', 'type' => 'hidden'),
					
					'T218_Id_Origem' => array('name' => 'T218_Id_Origem', 'label' => 'Importar Orçamentos', 'type' => 'moeda', 'length' => array(40, 80), 'disabled' => 'true'),
				)
			)
		);
        $form = new Formulario('form-est015FormTotais');
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
	
	case 'est015FormTotalConfirmado':
	
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
		
		
		$form_tabela = 'T218';
		$form_campo_pk = 'T218_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = '';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '';
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral2' => array(
					// Campo normal (do banco)
     				'T218_Valor_Total_ICMS_Confirmado' => array('name' => 'T218_Valor_Total_ICMS_Confirmado', 'label' => 'ICMS', 'type' => 'moeda', 'length' => array(40, 80), 'disabled' => 'true'),
					'T218_Valor_ST_Confirmado' => array('name' => 'T218_Valor_ST_Confirmado', 'label' => 'S.T.', 'type' => 'moeda', 'length' => array(40,80), 'disabled' => 'true'),
					'T218_Valor_Total_IPI_Confirmado' => array('name' => 'T218_Valor_Total_IPI_Confirmado', 'label' => 'IPI', 'type' => 'moeda', 'length' => array(40,80), 'disabled' => 'true'),
					'T218_Valor_Total_Produtos_Sem_Desconto_Confirmado' => array('name' => 'T218_Valor_Total_Produtos_Sem_Desconto_Confirmado', 'label' => 'Produtos', 'type' => 'moeda', 'length' => array(40,80), 'disabled' => 'true'),
					'T218_Valor_Frete' => array('name' => 'T218_Valor_Frete', 'label' => 'Frete', 'type' => 'moeda', 'length' => array(40,80), 'disabled' => 'true'),
					'T218_Valor_Despesas_Acessorias' => array('name' => 'T218_Valor_Despesas_Acessorias', 'label' => 'Dp. Acess.', 'type' => 'moeda', 'length' => array(40,80), 'disabled' => 'true'),
					'T218_Valor_Desconto_Confirmado' => array('name' => 'T218_Valor_Desconto_Confirmado', 'label' => 'Desconto R$', 'type' => 'moeda', 'length' => array(40,80)),
					'T218_IPV_Confirmado' => array('name' => 'T218_IPV_Confirmado', 'label' => 'IPV', 'type' => 'moeda', 'length' => array(40,80), 'disabled' => 'true'),
					'T218_Peso_Total_Confirmado' => array('name' => 'T218_Peso_Total_Confirmado', 'label' => 'Peso Total', 'type' => 'text', 'length' => array(40,80), 'disabled' => 'true'),
					'T218_Valor_Total_Confirmado' => array('name' => 'T218_Valor_Total_Confirmado', 'label' => 'Total', 'type' => 'moeda', 'length' => array(40,80), 'disabled' => 'true'),
				)
			)
		);
        $form = new Formulario('form-est015FormTotais2');
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
	
	case 'est015FormAplicarIpv':
	
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
		$form_tabela = 'T218';
		$form_campo_pk = 'T218_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = '';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '';
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					'T218_Id' => array('name' => 'T218_Id', 'label' => 'T218_Id', 'type' => 'hidden', 'length' => array(40, 80), 'reject' => true, 'value' => $r_T218_Id),
     				'IPV' => array('name' => 'IPV', 'label' => 'Aplicar IPV', 'type' => 'moeda', 'length' => array(40, 80), 'reject' => true),
				)
			)
		);
        $form = new Formulario('form-est015FormTotais2');
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
	
	case 'est015FormAplicarDesconto':
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
		$form_tabela = 'T218';
		$form_campo_pk = 'T218_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est015AplicarDescontoBtnExecutar';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est015/form/est015FormAplicarDesconto/';

		//form editor
		$form_editor = array('geral' =>'{"Desconto":{"top":4,"left":18,"width":175},"CampoAplicarDesconto":{"top":26,"left":4,"width":177}}');
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					//'T218_Id' => array('name' => 'T218_Id', 'label' => 'T218_Id', 'type' => 'hidden', 'length' => array(40, 80), 'reject' => true, 'value' => $r_T218_Id),
     				'Desconto' => array('name' => 'Desconto', 'label' => '% Desconto', 'type' => 'moeda', 'length' => array(30, 80), 'reject' => true),
     				'CampoAplicarDesconto' => array('name' => 'CampoAplicarDesconto', 'label' => 'Desconto base', 'type'=>'select', 'reject' => true, 'select' => 
						array(
							array('title' => 'Preço tabela', 'value' => 'tabela','selected'=>true),
							array('title' => 'Preço pedido', 'value' => 'pedido'),
						),
					),
				)
			)
		);
        $form = new Formulario('form-est015FormAplicarDesconto');
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
								$.getJSON('/est/est015/form_func-ajax/est015aplicarDesconto/?ajax=true&T218_Id=' + encodeURIComponent('{$r_T218_Id}') + '&CampoAplicarDesconto=' + encodeURIComponent(CampoAplicarDesconto) + '&Desconto=' + encodeURIComponent(Desconto) + '&callback=?', function(request) {
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
		$botoesDir .= '<button type="submit" id="est015AplicarDescontoBtnExecutar">Executar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;
	
	
	case 'est015FormAplicarDescontoReais':
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
		$form_tabela = 'T218';
		$form_campo_pk = 'T218_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est015AplicarDescontoReaisBtnExecutar';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est015/form/est015FormAplicarDescontoReais/';

		$T218 = mysql_query("SELECT T218_Valor_Total,T218_Valor_Desconto_Manual FROM T218 WHERE T218_Id='{$r_T218_Id}'");
		$mT218 = mysql_fetch_array($T218);

		// $valorTotalAntigo = $mT218['T218_Valor_Total'] + $mT218['T218_Valor_Desconto_Manual'];
		// $descontoPercentual = gCorrigeNumero((($valorTotalAntigo - $mT218['T218_Valor_Total']) / $valorTotalAntigo) * 100);   
		// $descontoPercentual = ($descontoPercentual == "0,00" ? "" : $descontoPercentual);

		//form editor
		$form_editor = array('geral' =>'{"DescontoReais":{"top":7,"left":5,"width":178,"hidden":0}}');
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					//'T218_Id' => array('name' => 'T218_Id', 'label' => 'T218_Id', 'type' => 'hidden', 'length' => array(40, 80), 'reject' => true, 'value' => $r_T218_Id),
     				'DescontoReais' => array('name' => 'DescontoReais', 'label' => 'Valor (R$)', 'value'=>$mT218['T218_Valor_Desconto_Manual'], 'type' => 'moeda', 'length' => array(30, 80), 'reject' => true),
				)
			)
		);
        $form = new Formulario('form-est002FormAplicarDesconto');
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
								$.getJSON('/est/est015/form_func-ajax/est015aplicarDescontoReais/?ajax=true&T218_Id=' + encodeURIComponent('{$r_T218_Id}') + '&DescontoReais=' + encodeURIComponent(DescontoReais) + '&callback=?', function(request) {
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
		$botoesDir .= '<button type="submit" id="est015AplicarDescontoReaisBtnExecutar">Executar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'est015FormPagamentos':
			$acaoId = (isset($r_acaoId)) ? $r_acaoId : false; 
			$T218_Id = (isset($r_T218_Id)) ? $r_T218_Id : false;
			  
			//echo $T218_Id;
			//('qqqq');
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
			$form_tabela = 'T218';
			$form_campo_pk = 'T218_Id';
			// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
			$enviar_btnId = 'est015FormPagamento_btn';
			// Form que será executado ao clicar no botão
			$enviar_acaoHref = '/est/est015/form/est015FormPagamentos/';
            
            //form editor
            $form_editor = array(
                'geral' => '{"Total_Parcelas":{"top":7,"left":32.203125,"width":175,"hidden":0},"selecionaPrimeiroVencimento":{"top":30,"left":33.390625,"width":105,"hidden":0},"primeiroVencimentoNum":{"top":30,"left":208,"width":40,"hidden":0},"primeiroVencimentoData":{"top":30,"left":208,"width":94,"hidden":0},"selecionaDias":{"top":53,"left":23.03125,"width":105,"hidden":0},"IntervalorDias":{"top":53,"left":208,"width":40,"hidden":0},"diaFixo":{"top":53,"left":208,"width":49,"hidden":0},"Portador_D031_Id":{"top":76,"left":31.6875,"width":175,"hidden":0},"est015FormPagamento_btn":{"top":74,"left":272,"width":null,"hidden":0},"est015FormAdicionarParcela_btn":{"top":74,"left":400,"width":null,"hidden":0},"T218_Observacao_Pagamento":{"top":99,"left":16.296875,"height":14,"width":168,"hidden":0},"est015SalvarObservacao_btn":{"top":97,"left":272,"width":null,"hidden":0}}',
            );

			// novo formulário
			$form = new Formulario('est015FormPagamento');			
			
			$select = array();
			$select[] = array('value' => '0', 'title' => 'A COMBINAR');
			// pega as configurações de juros da empresa atual
			$C004=mysql_query("select * from C004 where C004_Id='{$g['empresaAtual']}'");
			$mC004=mysql_fetch_array($C004);
			$tipoJuros=$mC004['C004_Tipo_Juros'];
			$taxaJuros=$mC004['C004_Taxa_Juros'];
			$parcelasIsentas=$mC004['C004_Parcelas_Isentas_Juros'];						
			$T218=mysql_query("SELECT * 
							     FROM T218 
							LEFT JOIN D031 on D031_Id=T218_Pgto_Portador where T218_Id='$T218_Id'");
			$mT218 = mysql_fetch_array($T218);	
			
			//SELECIONANDO OS SERVIÇOS DESSE ORÇAMENTO PARA CALCULAR O TOTAL
			$sql = "SELECT * FROM T223 where T223_T218_Id='{$T218_Id}'";
			$T223_sql = mysql_query($sql);
			
			// LOOPING PARA CALCULAR O TOTAL DE SERVIÇOS CONFIRMADOS PARA ESSE ORÇAMENTO
			$totalServicos=0;
			while($T223 = mysql_fetch_array($T223_sql)){
				$totalServicos+=$T223['T223_Quantidade_Confirmada'] * $T223['T223_Valor_Unitario'];
			}
			
			//SOMANDO O TOTAL DO ORÇAMENTO COM O TOTAL DOS SERVIÇOS
			$valorTotalOrcamento = $totalServicos + $mT218['T218_Valor_Total'];
			for ($i=1; $i<=24; $i++) {
				$valorParcela=$EST015->calcularParcelaCombo($i,$valorTotalOrcamento,$tipoJuros,$taxaJuros,$parcelasIsentas);				
				if ($i>$parcelasIsentas){
					$valorTotal=gCorrigeNumero($i*gCorrigeNumeroInverte($valorParcela));
					$valorParcela.="*";
				}else{
					$valorTotal=gCorrigeNumero($valorTotalOrcamento);
				}
				if($i=='1' or $i == $mT218['T218_Pgto_Parcelas']){
					$select[] = array('value' => $i, 'title' => str_pad($i,2,"0",STR_PAD_LEFT) . " x" . " - " .$valorParcela." = ".$valorTotal, 'selected'=>true);
				}else{
					$select[] = array('value' => $i, 'title' => str_pad($i,2,"0",STR_PAD_LEFT) . " x" . " - " .$valorParcela." = ".$valorTotal);
				}
			}
			
			if(($mT218['T218_Pgto_Primeiro_Vcto_Valor'] != "") || (!empty($mT218['T218_Pgto_Primeiro_Vcto_Valor']))){
				$primeiroVctoValor = $mT218['T218_Pgto_Primeiro_Vcto_Valor'];
			}else{
				$primeiroVctoValor = $g['conf']->camposValorPadrao("primeiroVencimentoNum");
			}

			if(($mT218['T218_Pgto_Dias_Vcto_Valor'] != "") || (!empty($mT218['T218_Pgto_Dias_Vcto_Valor']))){
				$diaVctoValor = $mT218['T218_Pgto_Dias_Vcto_Valor'];
			}else{
				$diaVctoValor = $g['conf']->camposValorPadrao("IntervalorDias");
			}

			if(($mT218['T218_Pgto_Data'] != "") || (!empty($mT218['T218_Pgto_Data']))){
				$pgtoData = $mT218['T218_Pgto_Data'];
			}else{
				$pgtoData = "";
			}

			if($mT218['T218_Pgto_Portador'] <= 0 && $mT218['D024A_D031_Id'] > 0){
				$mT218['T218_Pgto_Portador'] = $mT218['D024A_D031_Id'];	
			}

			// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
			$formArray = array(
				'campos' => array(
					'geral' => array(
						'Total_Parcelas' => array('name' => 'Total_Parcelas', 'label' => 'Parcelas', 'type' => 'select', 'select' => $select,'uppercase'=>false, 'reject' => true),
						//'valorParcelaSimulacao' => array('name' => 'valorParcelaSimulacao', 'label' => '', 'type' => 'text','extra'=>'style="background:#E8E8E8"','reject' => true, 'length' => array(3,6), 'align' => 'right', 'readonly'=>'readonly'),
						'selecionaPrimeiroVencimento' => array('name' => 'selecionaPrimeiroVencimento', 'label'=>'1º Venc.', 'reject'=>true, 'type' => 'select','select' => 
						array(
							array('title' => 'Dias', 'value' => 'Dias', 'selected' => ($mT218['T218_Pgto_Primeiro_Vcto_Tipo'] == "Dias") ? true : false),
							array('title' => 'Data', 'value' => 'Data', 'selected' => ($mT218['T218_Pgto_Primeiro_Vcto_Tipo'] == "Data") ? true : false),
						)),
						'primeiroVencimentoNum' => array('name' => 'primeiroVencimentoNum', 'label' => '', 'value'=> $primeiroVctoValor, 'type' => 'text', 'reject' => true, 'length' => array(3,6), 'align' => 'right'),
						'primeiroVencimentoData' => array('name' => 'primeiroVencimentoData', 'label' => '', 'value'=> $primeiroVctoValor,  'type' => 'data', 'reject' => true, 'align' => 'right'),
						'selecionaDias' => array('name' => 'selecionaDias', 'label'=>'Dias Venc.', 'type' => 'select', 'reject'=>true,'select' => 
						array(
							array('title' => 'Intervalo', 'value' => 'I', 'selected' => ($mT218['T218_Pgto_Dias_Vcto_Tipo'] == "Intervalo") ? true : false),
							array('title' => 'Dia Fixo', 'value' => 'F', 'selected' => ($mT218['T218_Pgto_Dias_Vcto_Tipo'] == "Dia Fixo") ? true : false),
						)),			
						'IntervalorDias' => array('name' => 'IntervalorDias', 'label' => '', 'value'=>$diaVctoValor, 'type' => 'text', 'reject' => true, 'length' => array(3,6), 'align' => 'right'),
						'diaFixo' => array('name' => 'diaFixo', 'label' => '', 'type' => 'text', 'value'=>$diaVctoValor, 'reject' => true, 'length' => array(3,2), 'align' => 'right'),												
						'Portador_D031_Id' => array('name' => 'D031_Id','label'=>'Portador', 'reject'=>true, 'type' => 'select', 'select' =>
							gGeraSelect('D031', 'D031_Id', 'D031_Portador',' order by D031_Portador', false, $mT218['T218_Pgto_Portador'])
						),
						'est015FormPagamento_btn' => array('name' => 'est015FormPagamento_btn', 'type'=>'submit','reload'=>false,'value'=>'Gerar Parcelas','align' => 'right', 'reject'=>true),
						'est015FormAdicionarParcela_btn' => array('name' => 'est015FormAdicionarParcela_btn', 'type'=>'submit', 'reload' => false, 'value' => 'Adicionar Parcela', 'align' => 'right', 'reject' => true),
						'T218_Observacao_Pagamento' => array('name' => 'T218_Observacao_Pagamento', 'label' => 'Observação', 'type' => 'textarea', 'length' => array(1,23)),
						'est015SalvarObservacao_btn' => array('name' => 'est015SalvarObservacao_btn', 'type'=>'submit', 'reload' => false, 'value' => 'Salvar Observação', 'align' => 'right', 'reject' => true),
						'T218_Id' => array('name' => 'T218_Id', 'label' => '', 'type' => 'hidden', 'value' => @$r_T218_Id, 'reject' => true),
					)
				)
			);
			
		if($g['C031']['usarFretePrimeiraParcela'] == 'S'){
			$formArray['campos']['geral']['usarFreteParcela'] = array('name' => 'usarFreteParcela', 'label'=>'Frete primeira parcela', 'reject'=>true, 'type' => 'select','select' => 
																	array(
																		array('title' => '...', 'value' => 'off'),
																		array('title' => 'Sim', 'value' => 'on'),
																		array('title' => 'Não', 'value' => 'off'),
																	)
																);

			$formArray['campos']['geral']['usarSTParcela'] = array('name' => 'usarSTParcela', 'label'=>'ST primeira parcela', 'reject'=>true, 'type' => 'select','select' => 
																	array(
																		array('title' => '...', 'value' => 'off'),
																		array('title' => 'Sim', 'value' => 'on'),
																		array('title' => 'Não', 'value' => 'off'),
																	)
																);
			$formArray['campos']['geral']['usarIPIParcela'] = array('name' => 'usarIPIParcela', 'label'=>'IPI primeira parcela', 'reject'=>true, 'type' => 'select','select' => 
																	array(
																		array('title' => '...', 'value' => 'off'),
																		array('title' => 'Sim', 'value' => 'on'),
																		array('title' => 'Não', 'value' => 'off'),
																	)
																);
		}
 
		$form->definirFormularioArray($formArray);
		
		echo <<<EOT
		    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		    	$('#est015FormAdicionarParcela_btn').unbind('click');
				$('#est015FormAdicionarParcela_btn').unbind('click').bind('click', function(data) {
					var id = '{$row['T218_D024_Id']}';
						$.getJSON('/est/est015/form_func-ajax/inserirParcela/?ajax=true&' + $("#{$formId}").serialize() + '&callback=?', function(request) {
							if (request.code) {
								divRefresh("{$r_divReload}");
							} else {
								var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
    							dialogConfirm("Erro!", request.data, buttons)
							}
						});
					return false;
				});
				$('#est015SalvarObservacao_btn').unbind('click').bind('click', function(data) {
					$.getJSON('/est/est015/form_func-ajax/salvarObservacao/?ajax=true=' + '&' + $("#{$formId}").serialize() + '&T218_Id=' + encodeURIComponent('{$T218_Id}') + '&callback=?', function(request) {
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
					$.getJSON('/est/est015/form_func-ajax/calcularParcelaSimulacao/?ajax=true=' + '&' + $("#{$formId}").serialize() + '&callback=?', function(request) {
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
					$retorno = $EST015->est015atualizarParcelas($form);
					if ($retorno === true) {
						$alert = gGeraAlertMsg('Operação OK', 'Parcelas cadastradas com sucesso.', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									if (typeof jQuery != 'undefined') {
                                       //divRefresh("{$g['divId']}", true);
                                       divRefresh("{$r_divReload}");
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
	
	
	case 'est015FormCancelarOrcamento':
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
		$enviar_btnId = 'est015FormCancelarOrcamentoBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est015/form/est015FormCancelarOrcamento/';
		
		$form_editor = array(
			'geral' => '{"T218_Observacao_Orcamento_Perdido":{"top":7,"left":5,"height":200,"width":281,"hidden":0}}'
		);
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					//Campo normal (do banco)
					/*
					'T218_D047_Id' => array('name' => 'T218_D047_Id', 'label' => '* Motivo', 'type' => 'select', 
						'select' =>  gGeraSelect('D047','D047_Id','D047_Descricao_Motivo','',true),
					),
					*/
					'T218_Observacao_Orcamento_Perdido' => array('name' => 'T218_Observacao_Orcamento_Perdido', 'label' => '* Observação', 'type' => 'textarea', 'length' => array(3,50)),
					'T218_Id' => array('name' => 'T218_Id', 'value'=>@$r_T218_Id, 'label' => 'Observação', 'type' => 'hidden', 'length' => array(3,50)),
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
				$retorno = $EST015->cancelarOrcamento($form);
				if ($retorno !== true) {
					echo gGeraAlertMsg('Erro', $retorno);	
				} else {
					echo gGeraAlertMsg('Operação OK', "Orçamento Cancelado com Sucesso");	
					echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										// O FW atualiza a linha do grid automaticamente, somente recarregar se não for grid
                                        if ($('#{$g['divIdParent']}').find('table.grid').length === 0) {
                                            divRefresh('{$g['divIdParent']}', true);
                                        }
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
		$botoesDir .= '<button type="submit" id="est015FormCancelarOrcamentoBtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;
	
	case 'est015FormDocumentos':
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
		$form_tabela = 'T221';
		$form_campo_pk = 'T221_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = array('est015FormDocumentosBtnAdd', 'est015FormDocumentosBtnCln', 'est015FormDocumentosBtnNew');
        // Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est015/form/est015FormDocumentos/';
        
        //form editor
        $form_editor = array(
            'geral' => '{"est015FormDocumentosBtnAdd":{"top":74,"left":416,"width":null,"hidden":0},"T221_Descricao":{"top":7,"left":73,"width":342,"hidden":0},"uploadArquivo":{"top":76,"left":28,"width":208,"hidden":0}}',
        );        
        
		// Array de definição do formulário
		$r_T218_Id = isset($r_est015FormDocumentos) ? $r_est015FormDocumentos['T221_T218_Id'] : $r_T218_Id;
		                                                           
		$tmp = gGeraSelect('T218', 'T218_Id', 'T218_Numero_DI', "WHERE T218_Id = '{$r_T218_Id}'");
		foreach($tmp as &$value) { $value['selected'] = true; }
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('est015FormDocumentos');
		$formArray = array(
			'campos' => array(
				'geral' => array(
				)
			)
		);
		//$formArray['campos']['geral']['__T221_T218_Id'] 		= array('name' => '__T221_T218_Id', 'label'=>'Número Pedido', 'type' => 'select', 'extra'=>'style="width:382px;"', 'disabled' => true, 'reject'=> true, 'select' => 
		//	$tmp
		//);
		$formArray['campos']['geral']['T221_Descricao'] 	= array('name' => 'T221_Descricao', 'label'=>'Descrição', 'type' => 'textarea','length' => array(3,60));
		$formArray['campos']['geral']['uploadArquivo'] 			= array('name' => 'uploadArquivo', 'label' => 'Arquivo documento', 'type' => 'file', 'length' => array(24, 24), 'reject' => true, 'uppercase' => false);
		$formArray['campos']['geral']['T221_Nome_Arquivo'] 		= array('name' => 'T221_Nome_Arquivo', 'type' => 'hidden', 'uppercase' => false);
		if (!empty($r_acaoId)) {
			 $formArray['campos']['geral']['est015FormDocumentosBtnNew'] = array('name' => 'est015FormDocumentosBtnNew', 'type' => 'button', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		}
		$formArray['campos']['geral']['T221_T218_Id'] 			= array('name' => 'T221_T218_Id', 'type' => 'hidden', 'value' => $r_T218_Id);
		$formArray['campos']['geral']['T221_C007_Id'] 			= array('name' => 'T221_C007_Id', 'type' => 'hidden', 'value' => $g['usuarioAtual']);
		$formArray['campos']['geral']['T221_Data_Hora_Upload'] 			= array('name' => 'T221_Data_Hora_Upload', 'type' => 'hidden', 'value' => date('Y-m-d H:i:s'));
		//$formArray['campos']['geral']['est015FormDocumentosBtnCln'] = array('name' => 'est015FormDocumentosBtnCln', 'type' => 'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['geral']['est015FormDocumentosBtnAdd'] = array('name' => 'est015FormDocumentosBtnAdd', 'type' => 'submit', 'reload' => false, 'value' => empty($r_acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
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
						$form->campoSetarValor('T221_Nome_Arquivo', $arquivo['name'], true);
						if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
							// Gravar o arquivo no disco
							$pathinfo = pathinfo($arquivo['name']);
							$pathinfo['extension'] = empty($pathinfo['extension']) ? '.tmp' : $pathinfo['extension'];
							//$dir = "{$g['pathDados']}documentos/T218_".str_repeat('0',5-strlen($form->campoValorEnviado('T221_T218_Id'))).$form->campoValorEnviado('T221_T218_Id');
							$dir = "{$g['pathDados']}cotacaoNovo";
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
    
    case 'est015FormAgregados':
            /**
            * PARTE 1: Variavéis envidas para o formulário
            */
            // Variável indica qual tab do form será exibida
            $tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
            // Utilizada em UPDATE, é o ID do registro que está sendo editado
            $T219_Id = (isset($_REQUEST['T219_Id'])) ? $_REQUEST['T219_Id'] : false;
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
            $enviar_btnId = array('est015FormAgregadosBtnAdd', 'est015FormAgregadosBtnCln', 'est015FormAgregadosBtnNew');
            // Form que será executado ao clicar no botão
            $enviar_acaoHref = '/est/est015/form/est015FormAgregados/';
            
            //form editor
            $form_editor = array(
                'geral' => '{"est015FormAgregadosBtnAdd":{"top":51,"left":400.00006103515625,"width":null,"hidden":0},"T202_Valor_Unitario":{"top":7,"left":369.00006103515625,"width":75,"hidden":0},"T202_Descricao":{"top":7,"left":25,"width":275,"hidden":0},"T202_Observacao":{"top":30,"left":14,"width":277,"hidden":0}}',
            );

            // novo formulário
            $form = new Formulario('est015FormAgregados');

            // Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
            $formArray = array(
                'campos' => array(
                    'geral' => array(
                    'T202_T219_Id' => array('name' => 'T202_T219_Id', 'type' => 'hidden', 'value' => @$T219_Id, 'length' => array(50,30)),
                    'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true),
                    'T202_Descricao' => array('name' => 'T202_Descricao','label'=>'Descrição','type' => 'text', 'length' => array(50,30)),
                    'T202_Valor_Unitario' => array('name' => 'T202_Valor_Unitario','label'=>'Valor','type' => 'moeda','align' => 'right','length' => array(10,10)),
                    'T202_Observacao' => array('name' => 'T202_Observacao','label'=>'Observação','type' => 'textarea', 'length' => array(2,47)),
                    )
                )
            );
            
            // Botão Limpar / Novo Registro
            if (!empty($acaoId)) {
                 $formArray['campos']['geral']['est015FormAgregadosBtnNew'] = array('name' => 'est015FormProcessosBtnNew', 'type'=>'button', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
            }
            //$formArray['campos']['geral']['est015FormAgregadosBtnCln'] = array('name' => 'est015FormProcessosBtnCln', 'type'=>'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
            $formArray['campos']['geral']['est015FormAgregadosBtnAdd'] = array('name' => 'est015FormProcessosBtnAdd', 'type'=>'submit', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
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

    case 'est015FormSelecionarEmpresa':
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
        $enviar_btnId = 'est015SelecionarEmpresa_btn';
        // Form que será executado ao clicar no botão
        $enviar_acaoHref = '/est/est015/form/est015FormSelecionarEmpresa/';

        // novo formulário
        $form = new Formulario('est015FormSelecionarEmpresa');
        // Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
        $formArray = array(
            'campos' => array(
                'geral' => array(
                    'C004_Id_Novo' => array('name' => 'C004_Id_Novo','label'=>'Empresa', 'type' => 'select','extra' => 'style="width:300px;"' ,
                        'select' => gGeraSelect('C004','C004_Id','C004_Nome_Empresa',"where C004_Flag_Ativo='S'", false, $g['empresaAtual'])
                    ),
                    'T218_Id_Antigo' => array('name' => 'T218_Id_Antigo', 'label' => 'T218_Id_Antigo', 'type' => 'hidden','value' => @$r_T218_Id),
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
                $T218_Id_Antigo = $form->campoValorEnviado('T218_Id_Antigo');
				if($mudarFilial == true){
					$retorno = $EST015->est015MudarFilial($T218_Id_Antigo,$C004_Id_Novo);
				} else {
					$retorno = $EST015->est015DividirOrcamento($T218_Id_Antigo,$C004_Id_Novo);
				}
                
	            if ($retorno===true) {
	                // Imprime uma mensagem e fecha a janela
	                if($mudarFilial == true){
	                    $alert .= gGeraAlertMsg('Operação OK', 'Filial alterada com sucesso!', false);
				    	$link = "";
			    	} else if($gerarNF == true){
			    		$alert .= gGeraAlertMsg('Operação OK', 'Nota Gerada com sucesso!', false);
						$link = "abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,unique(), '', 'Editar Nota Fiscal - ID: ' + '{$T007_Id}', '/est/esT218/content/esT218contentIncluirNotaFiscal/', '&acaoId=' + encodeURIComponent('{$T007_Id}') + '&tabela={$from}', [900,550]);";
			    	} else {
			    		$alert .= gGeraAlertMsg('Operação OK', 'Orçamento dividido com sucesso!', false);
						$link = "abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Dividir Orçamento', '/est/est015/content/est015ContentEditarOrcamento/', '&acaoId=' + encodeURIComponent('{$novoT218_Id}'), [1000,610]);";
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

    case 'est015FormImportarOrcamento':
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
		
		
		$form_tabela = 'T218';
		$form_campo_pk = 'T218_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est015ImportarOrcamentoBtnExecutar';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est015/form/est015FormImportarOrcamento/';
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
     				'T218_Id' => array('name' => 'T218_Id', 'label' => 'T218_Id', 'type' => 'hidden', 'length' => array(40, 80), 'reject' => true, 'value' => $r_T218_Id),
     				'T218_Id_Origem' => array('name' => 'T218_Id_Origem', 'label' => 'Ordem de compra', 'type' => 'text', 'length' => array(35, 80), 'reject' => true),
				)
			)
		);
        $form = new Formulario('form-est015FormTotais2');
		$form->definirFormularioArray($formArray);
		

			if ($formCodigo = $form->processarFormulario($form_tabela)) {
				// Form com erros
				if ($formCodigo == 1) {
					echo gGeraAlertMsgErroForm($form);
					// Form sem erros
				} else if ($formCodigo == 2) {
					// Gerar Boleto
					//$form->campoValorEnviado(T117_D024_Id);
					$T218_Id = $form->campoValorEnviado(T218_Id);
					$retorno = $EST015->importarItensOrcamento($form);
					if ($retorno === true) {
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								if (typeof jQuery != 'undefined') {
									idProgressBar = dialogAlert('Processando CFOP e Alíquotas',"<span id='progressBar'>Processando 0%</span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' />");
									est015AtualizarAliquotas(idProgressBar,1); 

									function est015AtualizarAliquotas(idProgressBar, inicio){
						                $.getJSON('/est/est015/form_func-ajax/atualizarAliquota/?ajax=true&T218_Id=' + encodeURIComponent("{$T218_Id}") + '&extra=' + encodeURIComponent("{$r_extra}") + '&inicio=' + inicio + '&C004_Id='+'encodeURIComponent("{$g['empresaAtual']}")'+'&callback=?', function(request) {
						                    if (request.concluido == 0) {
						                    	$('#progressBar').html('<b>Processado '+ request.percentual + '%</b>' +'</b><br/>Produtos atualizados: <b>' + request.ok + '</b><br/>Produtos com erro: <b> ' + request.erro + '</b><br /><br/><b><font color="red">' + request.erros + '</font></b>');
						                    	est015AtualizarAliquotas(idProgressBar,0);
						                    }else{
						                    	if (request.code) {
						                    		$('#dc-' + idProgressBar).dialog("destroy").remove();
						                    		dialogConfirm('CFOP e Alíquotas processadas','<b>Processado '+ request.percentual + '%</b>' + '<br/><br/>Pedidos atualizados: <b>' + request.pedidos + '</b><br/>Produtos atualizados: <b>' + request.ok + '</b><br/>Produtos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
													$('#{$g['divId']}').closest('.ui-dialog-content').dialog('close');
						                            divRefresh('{$g['divIdParent']}', true);
						                			$(this).dialog("destroy").remove();     
						                        } else {
													$('#dc-' + idProgressBar).dialog("destroy").remove();
						                        	$('#{$g['divId']}').closest('.ui-dialog-content').dialog('close');
						                        	$(this).dialog("destroy").remove(); 
						                            divRefresh('{$g['divIdParent']}', true);
						                            dialogConfirm('Erro ao atualizar alíquotas', request.data);
						                        }
						                    } 
						                });	
						            }       
                               	}
							} </script></div>
EOT;
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

	case 'est015FormAplicarDescontoTotal':
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
		$form_tabela = 'T218';
		$form_campo_pk = 'T218_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est015AplicarDescontoTotalBtnExecutar';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est015/form/est015FormAplicarDescontoTotal/';

		$T218 = mysql_query("SELECT T218_Valor_Total FROM T218 WHERE T218_Id='{$r_T218_Id}'");
		$mT218 = mysql_fetch_array($T218);

		//form editor
		$form_editor = array('geral' =>'{"DescontoValorTotal":{"top":7,"left":5,"width":178,"hidden":0}}');
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
     				'DescontoValorTotal' => array('name' => 'DescontoValorTotal', 'label' => 'Valor Total (R$)', 'value'=>gCorrigeNumero($mT218['T218_Valor_Total']), 'type' => 'moeda', 'length' => array(30, 80), 'reject' => true),
				)
			)
		);
        $form = new Formulario('form-est015FormAplicarDescontoTotal');
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
								$.getJSON('/est/est015/form_func-ajax/est015aplicarDescontoTotal/?ajax=true&T218_Id=' + encodeURIComponent('{$r_T218_Id}') + '&DescontoValorTotal=' + encodeURIComponent(DescontoValorTotal) + '&callback=?', function(request) {
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
		$botoesDir .= '<button type="submit" id="est015AplicarDescontoTotalBtnExecutar">Executar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'est015FormCotar':
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
		$enviar_btnId = 'est015FormCotar_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est015/form/est015FormCotar/';
        
        //form editor
        $form_editor = array(
            'geral' => '{"T189_Observacao":{"top":7,"left":14,"height":131,"width":241,"hidden":0},"T189_T219_Id":{"top":122,"left":0,"width":127,"hidden":1},"T189_D009_Id":{"top":99,"left":0,"width":127,"hidden":1}}',
        );
		
		$form = new Formulario('est015FormCotar');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T189_Observacao' => array('name' => 'T189_Observacao', 'label' => 'Observação', 'type' => 'textarea', 'length' => array(8, 40)), 
					// Campos Hidden
					'T189_T219_Id' => array('name' => 'T189_T219_Id', 'label' => '', 'type' => 'hidden', 'value' => $r_T219_Id),
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
				$retorno = $EST015->cotarProduto($form);
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

	case 'est015FormAtualizarCFOP':
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
		$form_tabela = 'T218';
		$form_campo_pk = 'T218_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est015FormAtualizarCFOP_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est015/form/est015FormAtualizarCFOP/';
        
        //form editor
        $form_editor = array(
            'geral' => '{"linha3":{"top":3,"left":104.984375,"width":null,"hidden":0},"linha":{"top":30,"left":18.984375,"width":null,"hidden":0},"D006_Id":{"top":53,"left":11,"width":335,"hidden":0},"est015FormAtualizarCFOP_btn":{"top":51,"left":400,"width":null,"hidden":0},"linha2":{"top":88,"left":15,"width":null,"hidden":0},"est015FormAtualizarCFOPRegras":{"top":120,"left":16,"width":null,"hidden":0}}',
        );

		$descricaoCFOP  = ($g['C031']['descricaoCFOPExibida'] == 'P') ? 'D006_Descricao' : 'IF(D006_Descricao_Comercial !="",D006_Descricao_Comercial,D006_Descricao) as D006_Descricao';
		
		$form = new Formulario('est015FormAtualizarCFOP');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D006_Id' => array('name' => 'D006_Id', 'label' => 'CFOP', 'type' => 'select', 'reject' => true,
						'select' => gGeraSelect('D006', 'D006_Id', "LPAD(D006_Id[[VIRGULA]]4[[VIRGULA]]0), D006_Codigo_CFOP,{$descricaoCFOP}", 'ORDER BY D006_Codigo_CFOP DESC', true, false, '%1 - %2 - %3')
					),
				),
			)
		);

		$formArray['campos']['geral']['est015FormAtualizarCFOP_btn'] = array('name' => 'est015FormAtualizarCFOP_btn', 'type'=>'submit', 'reload' => false, 'value' => 'Atualizar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['geral']['linha'] = array('name' => 'linha', 'type'=>'titulo', 'value' => '', 'label' => 'Atualizar manual:', 'reject' => true);
		$formArray['campos']['geral']['linha2'] = array('name' => 'linha2', 'type'=>'titulo', 'value' => '', 'label' => 'Atualizar de acordo com as regras de CFOP:', 'reject' => true);
		$formArray['campos']['geral']['linha3'] = array('name' => 'linha3', 'type'=>'comentario', 'value' => 'Atenção: Ao escolher uma das opções, a CFOP e as <br />aliquotas dos itens deste orçamento serão alterados.', 'label' => '', 'reject' => true, 'style' => 'color: #FF0000; font-size: 12px;');
		$formArray['campos']['geral']['est015FormAtualizarCFOPRegras'] = array('name' => 'est015FormAtualizarCFOPRegras', 'type'=>'button', 'reload' => false, 'value' => 'Aplicar regras de CFOP', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$form->definirFormularioArray($formArray);
		
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
			$('#est015FormAtualizarCFOPRegras').unbind('click').bind('click', function(data) {
				idProgressBar = dialogAlert('Processando CFOP e alíquotas',"<span id='progressBar'><b><br>0% Processando CFOP</b></span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' />");
				$.getJSON('/est/est015/form_func-ajax/atualizarCFOP/?ajax=true&T218_Id=' + encodeURIComponent("{$r_T218_Id}") + '&callback=?', function(request) {
					if (request.code) {
						T218_Id = {$r_T218_Id};
						$('#progressBar').html('<br><b><font color="green">100% CFOP processada &#10004;</font></b></br>' + '<b>0% Processando alíquotas</b></br>');
						est015AtualizarAliquotasCFOPGeralLoop(idProgressBar,T218_Id,1); 

						function est015AtualizarAliquotasCFOPGeralLoop(idProgressBar, T218_Id, inicio){
			                $.getJSON('/est/est015/form_func-ajax/atualizarAliquota/?ajax=true&T218_Id=' + T218_Id + '&extra=' + encodeURIComponent("{$extra_}") + '&inicio=' + inicio + '&callback=?', function(request) {
			                    if (request.concluido == 0) {
			                    	$('#progressBar').html('<br><b><font color="green">100% CFOP processada &#10004;</font></b></br><b>' + request.percentual + '<b>% Processando alíquotas</b>' + '<br/><br/>Produtos atualizados: <b>' + request.ok + '</b><br/>Produtos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
			                    	est015AtualizarAliquotasCFOPGeralLoop(idProgressBar,T218_Id,0);
			                    }else{
			                    	if (request.code) {
			                    		$('#dc-' + idProgressBar).dialog("destroy").remove();
			                    		if(request.erro == 0){
											dialogConfirm('CFOP e alíquotas processadas','<br><b><font color="green">100% CFOP processada &#10004;</font></b></br><b><font color="green">' + request.percentual + '<b>%</b><b> Alíquotas processadas &#10004;' + '</font><br/><br/>Produtos atualizados: <b>' + request.ok + '</b><br/>Produtos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');	
			                    		}else{
			                    			dialogConfirm('CFOP e alíquotas processadas','<br><b><font color="green">100% CFOP processada &#10004;</font></b></br><b><font color="red">' + request.percentual + '<b>%</b><b> Alíquotas processadas &#10006;' + '</font><br/><br/>Produtos atualizados: <b>' + request.ok + '</b><br/>Produtos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');	
			                    		} 
			                    		divRefresh('{$g['divIdParent']}', true);  
			                        } else {
			                        	$('#dc-' + idProgressBar).dialog("destroy").remove();
			                            dialogConfirm('Erro!', request.data);
			                            divRefresh('{$g['divIdParent']}', true);
			                        }
			                    } 
			                });	                    
				      	}
					} else {
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
				//$retorno = $EST015->est015atualizarCFOPManual($r_T218_Id, $D006_Id);
				echo <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						T218_Id = {$r_T218_Id};	
						idProgressBar = dialogAlert('Processando CFOP e alíquotas',"<span id='progressBar'><b><br>0% Processando CFOP</b></span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' />");

						$.getJSON('/est/est015/form_func-ajax/atualizarCFOPManual/?ajax=true&T218_Id=' + T218_Id + '&D006_Id=' + {$D006_Id}  + '&callback=?', function(request) {
							if(request.code){
								$('#progressBar').html('<br><b><font color="green">100% CFOP processada &#10004;</font></b></br>' + '<b>0% Processando alíquotas</b></br>');
								est015AtualizarAliquotaCFOPLoop(idProgressBar,T218_Id, 1); 
							}else{
								$('#dc-' + idProgressBar).dialog("destroy").remove();
								$(this).dialog("destroy").remove(); 
			                    dialogConfirm('Erro!', request.data);
							}
						});

	            		function est015AtualizarAliquotaCFOPLoop(idProgressBar, T218_Id, inicio){
			                $.getJSON('/est/est015/form_func-ajax/atualizarAliquota/?ajax=true&T218_Id=' + T218_Id + '&extra=' + encodeURIComponent("{$extra_}") + '&inicio=' + inicio + '&callback=?', function(request) {
			                    if (request.concluido == 0) {
			                    	$('#progressBar').html('<br><b><font color="green">100% CFOP processada &#10004;</font></b></br><b>' + request.percentual + '<b>% Processando alíquotas</b>' + '<br/><br/>Produtos atualizados: <b>' + request.ok + '</b><br/>Produtos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
			                    	est015AtualizarAliquotaCFOPLoop(idProgressBar,T218_Id,0);
			                    }else{
			                    	if(request.code){
			                   			$('#dc-' + idProgressBar).dialog("destroy").remove();
			                    		if(request.erro == 0){
											dialogConfirm('CFOP e alíquotas processadas','<br><b><font color="green">100% CFOP atualizada &#10004;</font></b></br><b><font color="green">' + request.percentual + '<b>%</b><b> Alíquotas processadas &#10004;' + '</font><br/><br/>Produtos atualizados: <b>' + request.ok + '</b><br/>Produtos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');	
			                    		}else{
			                    			dialogConfirm('CFOP e alíquotas processadas','<br><b><font color="green">100% CFOP atualizada &#10004;</font></b></br><b><font color="red">' + request.percentual + '<b>%</b><b> Alíquotas processadas &#10006;' + '</font><br/><br/>Produtos atualizados: <b>' + request.ok + '</b><br/>Produtos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');	
			                    		} 
			                    		divRefresh('{$g['divIdParent']}', true);  
			                        } else {
			                        	$('#dc-' + idProgressBar).dialog("destroy").remove();
			                        	$(this).dialog("destroy").remove(); 
			                            dialogConfirm('Erro!', request.data);
			                            divRefresh('{$g['divIdParent']}', true); 
			                        }
			                    } 
			                });	                    
				      	}
						$('#{$g['divId']}').hideLoading();
						$('#{$g['divId']}').closest('.ui-dialog-content').dialog('close');
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
	
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;	

	case 'est015FormNaoLiberar':
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
		$form_tabela = 'T218';
		$form_campo_pk = 'T218_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est015FormNaoLiberarBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est015/form/est015FormNaoLiberar/';
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T218_Liberou_Alcada_Observacao_Nao_Liberado' => array('name' => 'T218_Liberou_Alcada_Observacao_Nao_Liberado', 'label' => '* Motivo', 'type' => 'textarea', 'length' => array(3,50)),
					'T218_Id' => array('name' => 'T218_Id', 'value'=>@$r_T218_Id, 'label' => 'Observação', 'type' => 'hidden', 'length' => array(3,50)),
				)
			)
		);
        $form = new Formulario('est015FormNaoLiberar');
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
				$retorno = $EST015->est015NaoLiberarOrcamento($form);
				if ($retorno !== true) {
					echo gGeraAlertMsg('Erro', $retorno);	
				} else {
					echo gGeraAlertMsg('Operação OK', "Cotação de compra cancelada com Sucesso");	
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
		$gerado = gGeraCamposForm($form, $tab, $r_acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk,"Você deseja recusar esta ordem de compra?");

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
		$botoesDir .= '<button type="submit" id="est015FormNaoLiberarBtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'est015FormConfiguracoes':
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

		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = 'T218';
		$form_campo_pk = 'T218_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est015FormConfiguracoesBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est015/form/est015FormConfiguracoes/';
		
		$form_editor = array('geral' => '{"T218_Flag_Calcular_ST":{"top":7,"left":32.421875,"width":64,"hidden":0},"est015FormConfiguracoesBtn":{"top":97,"left":160,"width":null,"hidden":0},"T218_Flag_Calcular_IPI":{"top":30,"left":33.03125,"width":64,"hidden":0},"T218_Flag_Calcular_ICMS":{"top":53,"left":18.96875,"width":64,"hidden":0}}');

		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T218_Flag_Calcular_ST' => array('name' => 'T218_Flag_Calcular_ST', 'label' => 'Calcular ST','type' => 'select','select' => 
						array(
							array('title' => 'SIM', 'value' => 'S'),
							array('title' => 'NÃO', 'value' => 'N'),
						)
					),
					'T218_Flag_Calcular_IPI' => array('name' => 'T218_Flag_Calcular_IPI', 'label' => 'Calcular IPI','type' => 'select','select' => 
						array(
							array('title' => 'SIM', 'value' => 'S'),
							array('title' => 'NÃO', 'value' => 'N'),
						)
					),
					'T218_Flag_Calcular_ICMS' => array('name' => 'T218_Flag_Calcular_ICMS', 'label' => 'Calcular ICMS','type' => 'select','select' => 
						array(
							array('title' => 'SIM', 'value' => 'S'),
							array('title' => 'NÃO', 'value' => 'N'),
						)
					),
				)
			)
		);
		// Botão Limpar / Enviar
		if(!empty($r_acaoId)){
			$formArray['campos']['geral']['est015FormConfiguracoesBtn'] = array('name' => 'est015FormConfiguracoesBtn', 'type'=>'button', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		}
		$form = new Formulario('est015FormConfiguracoes');
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
   						echo gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!');
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								//divRefresh('{$r_divIdMain}', true);
								divRefresh($('.contentCabecalhoLayout').parent().attr('id'));
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
}
