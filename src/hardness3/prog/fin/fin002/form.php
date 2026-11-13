<?php
namespace hardness;
//Classes
require_once("bibliotecas/classes/FIN002.php");
$FIN002 = new FIN002();
require_once("bibliotecas/classes/FIN009.php");
$FIN009 = new FIN009();
require_once("prog/fin/fin002/form_func-js.php");
/**
 * Ações
 */
switch ($g['r'][3]) {
	/**
	 * Form Principal
	 * Chamado em: fin002tab02
	 */
	case 'fin002form01':
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
		$form_tabela = 'T015';
		$form_campo_pk = 'T015_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'content_form1_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/fin/fin002/form/fin002form01/';

		//form editor
		$form_editor = array('cadastro_titulo' => '{"T015_C004_Id":{"top":329,"left":47.4375,"width":463,"hidden":0},"T015_D024_Id":{"top":7,"left":34.953125,"width":442,"hidden":0},"formFornecedor":{"top":5,"left":576,"width":null,"hidden":0},"NumeroParcelas":{"top":30,"left":4,"width":98,"hidden":1},"T015_D032_Id":{"top":30,"left":53.421875,"width":143,"hidden":0},"formGrupos":{"top":28,"left":256,"width":null,"hidden":0},"T015_D014_Id":{"top":30,"left":326.0625,"width":193,"hidden":0},"formContas":{"top":28,"left":576,"width":null,"hidden":0},"T015_Codigo_Barras":{"top":53,"left":6.65625,"width":456,"hidden":0},"T015_Data_Emissao":{"top":76,"left":15.484375,"width":136,"hidden":0},"T015_Data_Vencimento":{"top":76,"left":316.53125,"width":136,"hidden":0},"T015_Numero_Documento":{"top":99,"left":18.4375,"width":136,"hidden":0},"T015_Numero_Duplicata":{"top":99,"left":338.75,"width":136,"hidden":0},"Pesquisa_Conta":{"top":99,"left":728,"width":0,"hidden":1},"T015_D031_Id":{"top":122,"left":47.6875,"width":143,"hidden":0},"T015_D073_Id":{"top":122,"left":339.21875,"width":143,"hidden":0},"T015_Valor_Duplicata":{"top":145,"left":27.359375,"width":136,"hidden":0},"T015_Valor_Juros":{"top":145,"left":352.0625,"width":136,"hidden":0},"T015_Valor_Desconto":{"top":168,"left":15.90625,"width":136,"hidden":0},"T015_Valor_Pago":{"top":168,"left":354.453125,"width":136,"hidden":0},"T015_Valor_Total":{"top":191,"left":38.140625,"width":136,"hidden":0},"T015_T075_Id":{"top":191,"left":400,"width":143,"hidden":1},"T015_Observacao":{"top":213.984375,"left":32.296875,"height":83,"width":456,"hidden":0},"T015_Valor_Original":{"top":305.96875,"left":26.25,"width":135,"hidden":0},"T015_Data_Vencimento_Original":{"top":306,"left":329.265625,"width":120,"hidden":0}}');

		// novo formulário
		$form = new Formulario('form_fin002_form1');

		$condicaoC004='';
		if ($acaoId<=0){
			$condicaoC004 = " where C004_Id='{$g['empresaAtual']}' ";
		}

		$clientePorEmpresa = '';
		if($g['C031']['separarClientePorEmpresa'] == 'S'){
			$clientePorEmpresa = "AND (IFNULL(D024_C004_Id,0) = '{$g['empresaAtual']}' OR IFNULL(D024_C004_Id,0) = 0)";
		}

		// Campo: Procura_Produto
		$pesquisaGridExtra = <<<EOT
			LEFT JOIN D024 ON T015_D024_Id=D024_Id
			LEFT JOIN D031 ON T015_D031_Id=D031_Id
			LEFT JOIN D014 ON T015_D014_Id=D014_Id
			LEFT JOIN D032 ON T015_D032_Id=D032_Id
			group by T015_Nome_Cliente
			order by T015_Data_Vencimento DESC
EOT;
		$pesquisaGrid = array(
			'T015',																										// tabela
			'T015_Id',																									// chave primaria
			'T015_Nome_Cliente||D024_Nome_Empresa||D032_Descricao||D014_SubConta||D031_Portador||T015_Data_Vencimento',	// campos
			$pesquisaGridExtra,																							// extras sql
			'Busca de Títulos a Pagar',																					// título da janela
			'Descrição,Fornecedor,Grupo,Conta,Portador,Vencimento,',													// descrição dos campos
			'campoPesquisaTitulosPagar',																				// callback js
			$g['divId'],																								// divId
			'',
			array(																										// parametros para cada campo do grid
				array('style' => 'font-weight:bold'),
				array(),
				array(),
				array(),
				array(),
				array('callback' => 'gCorrigeData'),
			),
			'T015_Id||D032_Descricao||D014_SubConta||D031_Portador||T015_Data_Vencimento||D024_Nome_Empresa'										 // Parâmetros que são ignorados na cláusula where do pesquisaGrid
		);

		if (empty($acaoId)){
			$disabled = false;
		}else{
			$disabled = true;
		}
		
		
		$D032_Id = $r_D032_Id;
		
		if(empty($D032_Id)) {
			$T015 = mysql_fetch_array(mysql_query("SELECT * FROM T015 WHERE T015_Id = '{$acaoId}'"));
			$D032_Id = (!empty($T015['T015_D032_Id'])) ? $T015['T015_D032_Id'] : 0;
		}
	
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'cadastro_titulo' => array(
					'Pesquisa_Conta' => array('name' => 'Pesquisa_Conta' , 'label' => 'Descrição', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGrid, 'length' => array(20,50), 'reject' => true),
					'T015_Nome_Cliente' => array('name' => 'T015_Nome_Cliente', 'type' => 'hidden'),
					'T015_D024_Id' => array('name' => 'Consulta_T015_D024_Id', 'label'=>'Fornecedor', 'type' => 'pesquisa', 'pesquisa' => array('D024','D024_Id','D024_Id,D024_Nome_Empresa,D020_Nome_Cidade,D018_UF,D024_Cpf,D024_Cnpj,D024_DDD_Telefone_1,D024_Telefone_1','left join D020 on D020_Id=D024_D020_Id left join D018 on D018_Id=D024_D018_Id WHERE D024_Flag_Ativo="S" '. $clientePorEmpresa .' order by D024_Nome_Empresa','(%1) %2 [%3/%4] - %5%6 Tel:(%7)%8'), 'length' => array(85,30)),
					//'T015_D014_Id' => array('name' => 'Consulta_T015_D014_Id','label'=>'Conta', 'type' => 'pesquisa','pesquisa'=>array('D014','D014_Id','D014_SubConta,D014_Grupo','where D014_Flag_Plano="M"'), 'length' => array(65,30)),
					
					'T015_D032_Id' => array('name' => 'T015_D032_Id', 'label' => 'Grupo', 'type' => 'select',
						'extra' => "onchange=\"divSetarAcaoId('{$g['divId']}', \$('#T015_D032_Id').val(), 'D032_Id'); alteraConta(this, '{$g['divId']}')\"", 
						'select' => gGeraSelect('D032', 'D032_Id', 'D032_Descricao','WHERE D032_Flag_Ativo="S" ORDER BY D032_Descricao', true)
					),
					
					'T015_D014_Id' => array('name' => 'T015_D014_Id', 'label' => 'Conta', 	'type' => 'select', 'align'=>'right', 'style'=>'float:right',					
						'selectRefresh' => array('D014', 'D014_Id', 'IF(D154_Id>0[[virgula]]CONCAT(D154_Descricao[[virgula]]" - "[[virgula]]D014_SubConta)[[virgula]]D014_SubConta) as D014_SubConta',"LEFT JOIN D154 ON D154_Id=D014_D154_Id WHERE D014_D032_Id = '{$D032_Id}' AND D014_Flag_Ativo='S' ORDER BY D014_SubConta", true),
						'select' => gGeraSelect('D014', 'D014_Id', 'IF(D154_Id>0[[virgula]]CONCAT(D154_Descricao[[virgula]]" - "[[virgula]]D014_SubConta)[[virgula]]D014_SubConta) as D014_SubConta',"LEFT JOIN D154 ON D154_Id=D014_D154_Id WHERE D014_D032_Id = '{$D032_Id}' AND D014_Flag_Ativo='S' ORDER BY D014_SubConta", true)
					),
					
					'T015_D073_Id' => array('name' => 'T015_D073_Id', 'label' => 'Centro Custo', 'type' => 'select','select' => 
						gGeraSelect('D073','D073_Id','D073_Nome', 'WHERE D073_Flag_Ativo = "S"', true),
					),
					'T015_Data_Emissao' => array('name' => 'T015_Data_Emissao', 'label'=>'Data Emissão','type' => 'data', 'length' => array(20,10)),
					'T015_Data_Vencimento' => array('name' => 'T015_Data_Vencimento','label'=>'Data Vencimento ', 'extra' => "onchange=\"retornaSaldoConta('{$g['divId']}')\"", 'align'=>'right','style'=>'float:right','type' => 'data', 'length' => array(20,10)),
					'T015_Numero_Documento' => array('name' => 'T015_Numero_Documento', 'label'=>'Nº Documento','type' => 'text', 'length' => array(30,30)),
					'T015_Numero_Duplicata' => array('name' => 'T015_Numero_Duplicata', 'label'=>'Nº Duplicata ','align'=>'right','style'=>'float:right','type' => 'text', 'length' => array(30,30)),		
					'T015_D031_Id' => array('name' => 'T015_D031_Id', 'label' => 'Portador', 'type' => 'select', 'select' => 
						gGeraSelect('D031','D031_Id','D031_Portador', '', true),
					),
					'T015_Valor_Duplicata' => array('name' => 'T015_Valor_Duplicata', 'label'=>'Valor Título', 'type' => 'moeda', 'length' => array(20,10)),
					'T015_Valor_Juros' => array('name' => 'T015_Valor_Juros', 'label'=>'Valor Juros ','align'=>'right','style'=>'float:right', 'disabled'=> $disabled, 'type' => 'moeda', 'length' => array(20,10)),
					'T015_Valor_Desconto' => array('name' => 'T015_Valor_Desconto', 'label'=>'Valor Desconto', 'type' => 'moeda', 'disabled'=> $disabled, 'length' => array(20,10)),
					'T015_Valor_Pago' => array('name' => 'T015_Valor_Pago', 'label'=>'Valor Pago ','align'=>'right', 'disabled'=>true, 'style'=>'float:right', 'type' => 'moeda', 'length' => array(20,10)),
					'T015_Valor_Total' => array('name' => 'T015_Valor_Total', 'label'=>'Valor Total', 'type' => 'moeda', 'disabled'=>true, 'style'=>'font-weight:bold'),
					'T015_Observacao' => array('name' => 'T015_Observacao', 'label'=>'Observação', 'type' => 'textarea', 'length' => array(4,81)),
					'T015_Valor_Original' => array('name' => 'T015_Valor_Original','label'=>'Valor original', 'readonly' => true,'type' => 'moeda', 'length' => array(15,10)),
					'T015_Data_Vencimento_Original' => array('name' => 'T015_Data_Vencimento_Original', 'label'=>'Vencimento original', 'align'=>'right', 'style'=>'float:right','type' => 'data', 'readonly' => true, 'length' => array(15,10)),
					'T015_Data_Pagamento' => array('name' => 'T015_Data_Pagamento','type' => 'hidden'),
					'Saldo_Conta' => array('name' => 'Saldo_Conta', 'label'=>'Saldo Conta','type' => 'hidden', 'length' => array(15,10), 'reject'=>true),
					'T015_Codigo_Barras' => array('name' => 'T015_Codigo_Barras', 'label'=>'Código de Barras','type' => 'text', 'length' => array(20,100)),
					'T015_T075_Id' => array('name' => 'T015_D075_Id', 'label' => 'DI', 'type' => 'select','select' => 
						gGeraSelect('T075','T075_Id','T075_Numero_DI', '', false, $r_T075_Id),'align'=>'right','style'=>'float:right',
					),
					'formFornecedor' => array('name' => 'formFornecedor', 'type'=>'button', 'reload' => false, 'value' => 'Incluir', 'align' => 'right', 'reject' => true),
					'formGrupos' => array('name' => 'formGrupos', 'type'=>'button', 'reload' => false, 'value' => 'Incluir', 'align' => 'right', 'reject' => true,),
					'formContas' => array('name' => 'formContas', 'type'=>'button', 'reload' => false, 'value' => 'Incluir', 'align' => 'right', 'reject' => true,),
				)
			)
		);
		if($g['C031']['contasPagarUnificado'] == 'S'){
			$formArray['campos']['cadastro_titulo']['T015_C004_Id'] = array('name' => 'T015_C004_Id', 'label' => 'Empresa', 'type' => 'select', 'select' => gGeraSelect('C004', 'C004_Id', 'C004_Nome_Abreviado,C004_Nome_Empresa', "where C004_Flag_Ativo='S' ORDER BY C004_Id", true , $g['empresaAtual']));
		} else {
			$formArray['campos']['cadastro_titulo']['T015_C004_Id'] = array('name' => 'T015_C004_Id', 'type' => 'hidden', 'value' => $g['empresaAtual']);
		}

		/* So exibe o campo para o Numero de Parcelas, se for um Incluir */
		if( empty($r_acaoId) )
			$formArray['campos']['cadastro_titulo']['NumeroParcelas'] = array('name' => 'NumeroParcelas', 'label'=>'Numero de Parcelas','reject' => true,'type' => 'moeda', 'casasDecimais' => 0);
		/*
		Exibe a opcao de parcela, apenas na 1 parcela, ou na inclusao
			
		$sql = mysql_query("SELECT * FROM T015 WHERE T015_Id = '{$r_acaoId}' AND T015_Numero_Parcela = '1'");
		if( mysql_num_rows($sql) > 0 || empty($r_acaoId) ){
			$formArray['campos']['cadastro_titulo']['NumeroParcelas'] = array('name' => 'NumeroParcelas', 'label'=>'Numero de Parcelas','reject' => true,'type' => 'text');
		} else{
			$formArray['campos']['cadastro_titulo']['NumeroParcelas'] = array('name' => 'NumeroParcelas', 'label'=>'Numero de Parcelas','reject' => true,'type' => 'text', 'disabled' => true);
		}*/
		$form->definirFormularioArray($formArray);
		if(empty($acaoId)){
			echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$('#T015_Codigo_Barras').addClass('input-editar-campo');
					$('#T015_Codigo_Barras').keyup(function(){
						calculaVencimento($('#T015_Codigo_Barras').val());
						return false;
					});
					$('#T015_Codigo_Barras').keypress(function(event) {
						if (event.keyCode == '13') {
							$('#T015_Numero_Documento').focus();
						}
					});
				} </script></div>
EOT;
		}
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#Pesquisa_Conta_Pesquisa').val('{$T015['T015_Nome_Cliente']}');
				$('#Pesquisa_Conta_Pesquisa').keyup(function(){
					$('#T015_Nome_Cliente').val($('#Pesquisa_Conta_Pesquisa').val());
				});
				$('#T015_D014_Id_refreshBtn').bind('click', function(){
					alteraConta($('#T015_D032_Id'), '{$g['divId']}');
				});
			} </script></div>
EOT;
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {				
				$('#formFornecedor').unbind('click').bind('click', function(data) {				
					id = unique();
					abrirJanela(data, '{$g['divIdRoot']}', id, id, '', 'Clientes/Fornecedores', '/cad/cad001/content/cad001contentCadastro/','', [960,583]);
				});
		
				$('#formGrupos').unbind('click').bind('click', function(data) {
					id = unique();
					abrirJanela(data, '{$g['divIdRoot']}', id, id, '', 'Incluir grupo plano de conta', '/cad/cad034/content/cad034ContentGrupoPlanoContasIncluir/', '&tab=geral', [400,180]);
				});

				$('#formContas').unbind('click').bind('click', function(data) {

					var D032_Id = $('#{$g['divId']} #T015_D032_Id').val();					
					id = unique();
					abrirJanela(data, '{$g['divIdRoot']}', id, id, '', 'Incluir conta', '/cad/cad034/content/cad034ContentPlanoContasIncluir/', '&tab=geral&D032_Id=' + encodeURIComponent(D032_Id), [650,520]);										
				});
		
								
			} </script></div>
EOT;

		//Script para inclusão do título projetado
		if($r_incluirTituloProjetado == true && $r_D014_Id > 0 && $r_D032_Id > 0) {
			$dataAtual = date("d/m/Y");
			$D024 = mysql_query("SELECT * FROM D024 LEFT JOIN D020 ON D020_Id=D024_D020_Id LEFT JOIN D018 ON D018_Id=D024_D018_Id WHERE D024_Id='{$r_D024_Id}'");
			if(mysql_num_rows($D024) > 0){
				$D024 = mysql_fetch_array($D024);
				$stringD024 = "({$D024['D024_Id']}) {$D024['D024_Nome_Empresa']} [{$D024['D020_Nome_Cidade']}/{$D024['D018_UF']}] - Tel:({$D024['D024_DDD_Telefone_1']}){$D024['D024_Telefone_1']}";

				echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$('#T015_D024_Id_Pesquisa').val('{$stringD024}');
					$('#T015_D024_Id').val('{$r_D024_Id}');
				} </script></div>
EOT;
			}
			$r_documento = urldecode($r_documento);
			$r_vencimento = urldecode($r_vencimento);
			$r_valor = gCorrigeNumero($r_valor);
			log('Venc '.$r_vencimento);
			echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$('#T015_Data_Emissao').val('{$dataAtual}');
					$('#T015_Data_Vencimento').val('{$r_vencimento}');
					$('#T015_Data_Vencimento_Original').val('{$r_vencimento}');
					$('#T015_Valor_Duplicata').val('{$r_valor}'); 
					$('#T015_Numero_Documento').val('{$r_documento}');
					$('#T015_Numero_Duplicata').val('{$r_documento}');
					$('#T015_D032_Id').val('{$r_D032_Id}');
					$('#T015_D031_Id').val('{$r_D031_Id}');
					$('#T015_D073_Id').val('{$r_D073_Id}');
					alteraConta($('#T015_D032_Id'), '{$g['divId']}');
					setTimeout(function(){
						$('#T015_D014_Id').val('{$r_D014_Id}');
						retornaSaldoConta('{$g['divId']}');
					}, 500);
				} </script></div>
EOT;
		//Script para inclusão da despesa
		} else if($r_incluirDespesa == true) {
			$D024 = mysql_query("SELECT * FROM D024 LEFT JOIN D020 ON D020_Id=D024_D020_Id LEFT JOIN D018 ON D018_Id=D024_D018_Id WHERE D024_Id='{$r_D024_Id}'");
			if(mysql_num_rows($D024) > 0){
				$D024 = mysql_fetch_array($D024);
				$stringD024 = "({$D024['D024_Id']}) {$D024['D024_Nome_Empresa']} [{$D024['D020_Nome_Cidade']}/{$D024['D018_UF']}] - Tel:({$D024['D024_DDD_Telefone_1']}){$D024['D024_Telefone_1']}";

				echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$('#T015_D024_Id_Pesquisa').val('{$stringD024}');
					$('#T015_D024_Id').val('{$r_D024_Id}');
				} </script></div>
EOT;
			}
			$r_vencimento = gCorrigeData($r_vencimento);
			$r_valor = gCorrigeNumero($r_valor);
			$r_observacao = $r_observacao;
			log('Venc '.$r_vencimento);
			echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$('#T015_Data_Emissao').val('{$r_vencimento}');
					$('#T015_Data_Vencimento').val('{$r_vencimento}');
					$('#T015_Valor_Duplicata').val('{$r_valor}'); 
					$('#T015_Observacao').val('{$r_observacao}');
					setTimeout(function(){
						$('#T015_D014_Id').val('{$r_D014_Id}');
						retornaSaldoConta('{$g['divId']}');
					}, 500);
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

					$validaDataEmissao = true;
					$validaFornecedorNumeroValorDuplicata = true;
					$validacao = '';

					//Não inclui títulos onde a Data de Vencimento é anterior a Emissão
					if($g['C031']['aceitarVencimentoAnteriorEmissaoPagar'] == 'N'){
						$T015_Data_Vencimento = gCorrigeDataInverte($form->campoValorEnviado('T015_Data_Vencimento'));
						$T015_Data_Emissao = gCorrigeDataInverte($form->campoValorEnviado('T015_Data_Emissao'));
						$sqlEmissao = mysql_query("SELECT DATEDIFF('{$T015_Data_Emissao}','{$T015_Data_Vencimento}')");
						$diferencaEmissao = mysql_fetch_array($sqlEmissao);	
						if($diferencaEmissao[0] > 0){
							$validaDataEmissao = false;
							$validacao .= 'A Data de Vencimento não pode ser anterior a Data de Emissão! <br />';
						}
					}

					//Não atualiza títulos onde o fornecedor, número e valor da duplicata sejam iguais a algum existente
					if($g['C031']['aceitarTitulosPagarNumeroValorDuplicataIguais'] == 'N'){
						$fornecedor = $form->campoValorEnviado('T015_D024_Id');
						$numeroDuplicata = $form->campoValorEnviado('T015_Numero_Duplicata');
						$valorDuplicata = $form->campoValorEnviado('T015_Valor_Duplicata');
						$T015_C004_Id = $form->campoValorEnviado('T015_C004_Id');
						$sqlValidacaoFNVDuplicata = mysql_query("SELECT T015_D024_Id, T015_Numero_Duplicata, T015_Valor_Duplicata 
														  FROM T015 
														 WHERE T015_D024_Id = '{$fornecedor}' 
														   AND T015_Numero_Duplicata = '{$numeroDuplicata}'
														   AND T015_Valor_Duplicata = '{$valorDuplicata}'
														   AND T015_C004_Id = '{$T015_C004_Id}' 
														   AND (T015_T015_Id_Agrupado <= 0 OR T015_T015_Id_Agrupado IS NULL) 
														   AND T015_Flag_Cancelada != 'S'
														   AND T015_Id <> '{$acaoId}'"); //deve ser diferente a ele mesmo
						if(mysql_num_rows($sqlValidacaoFNVDuplicata) > 0){
							$validaFornecedorNumeroValorDuplicata = false;
							$validacao .= 'Número e valor de duplicata já existem para esse fornecedor, faça a alteração de um desses campos para continuar! <br />';
						}
					}

					if (!empty($validacao)){
						echo gGeraAlertMsg('Operação Inválida',$validacao);
					}

					
					if($validaDataEmissao && $validaFornecedorNumeroValorDuplicata){

						// Faz o select na conta e testa se a o campo para verificar o saldo da conta está igual a SIM
						$D014_Id = $form->campoValorEnviado('T015_D014_Id');
						$D014=mysql_query("SELECT D014_Flag_Verificar_Saldo from D014 WHERE D014_Id='{$D014_Id}'");
						$mD014 = mysql_fetch_array($D014);

						// Define se é para verificar o saldo da conta selecionada na inclusão de um título a pagar
						if($g['C031']['verificarSaldoConta'] == 'S' && $mD014['D014_Flag_Verificar_Saldo'] == 'S'){
							$Saldo_Conta 		  = gCorrigeNumeroInverte($form->campoValorEnviado('Saldo_Conta'));
							$T015_Valor_Duplicata = gCorrigeNumeroInverte($form->campoValorEnviado('T015_Valor_Duplicata'));

							$T015_Data_Vencimento  = $form->campoValorEnviado('T015_Data_Vencimento');
							$T015_Data_Vencimento  = explode("/", $T015_Data_Vencimento);
							$mes = $T015_Data_Vencimento[1];

							$ValorAnterior = mysql_query("SELECT T015_Valor_Duplicata from T015 WHERE T015_Id='{$acaoId}'");
							$mValorAnterior = mysql_fetch_array($ValorAnterior);

							$saldoAtual = ($Saldo_Conta + $mValorAnterior['T015_Valor_Duplicata']);

							//die("Saldo conta: " . $Saldo_Conta . " + Valor anterior: " . $mValorAnterior['T015_Valor_Duplicata'] . "= Saldo atual: ".$saldoAtual . "<br />Saldo Atual: " . $saldoAtual . "< Valor Duplicata: " . $T015_Valor_Duplicata);

							if($saldoAtual < $T015_Valor_Duplicata){
								$valorNecessario = gCorrigeNumero($T015_Valor_Duplicata - $saldoAtual);
								$saldoAtual = gCorrigeNumero($saldoAtual);
								echo gGeraAlertMsg('Erro', 'O valor da duplicata é maior que o saldo da conta! <br />Saldo Conta: R$ <b>'.$saldoAtual . "</b><br />Valor Necessário: <b>" . $valorNecessario."</b>");
							}else{
								if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
								
									// Chamada de método que atualiza a projeção orçamentária da conta selecionada paa atualizar o saldo da conta
									$retorno = $FIN009->FIN009RecalculaProjecao($form->campoValorEnviado('T015_D014_Id'),null,null, $mes);

									$valorNumeroParcelas = $form->campoValorEnviado('NumeroParcelas');
									if( !empty($valorNumeroParcelas) && $valorNumeroParcelas !=0 )
									{
										$retorno=$FIN002->duplicarRegistrosUpdate($acaoId, $valorNumeroParcelas);
									}
										
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
							}
						}else{
							if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
							
								$valorNumeroParcelas = $form->campoValorEnviado('NumeroParcelas');
								if( !empty($valorNumeroParcelas) && $valorNumeroParcelas !=0 )
								{
									$retorno=$FIN002->duplicarRegistrosUpdate($acaoId, $valorNumeroParcelas);
								}
									
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
						}
					}//$validaData
				} else {
					// Insert

					$validaData = true;
					$validaDataEmissao = true;
					$validaFornecedorNumeroValorDuplicata = true;
					$validacao='';

					//Não inclui títulos onde a Data de Vencimento é anterior à Emissão
					if($g['C031']['aceitarVencimentoAnteriorEmissaoPagar'] == 'N'){
					$T015_Data_Vencimento = gCorrigeDataInverte($form->campoValorEnviado('T015_Data_Vencimento'));
					$T015_Data_Emissao = gCorrigeDataInverte($form->campoValorEnviado('T015_Data_Emissao'));
					$sqlEmissao = mysql_query("SELECT DATEDIFF('{$T015_Data_Emissao}','{$T015_Data_Vencimento}')");
					$diferencaEmissao = mysql_fetch_array($sqlEmissao);	
						if($diferencaEmissao[0] > 0){
							$validaDataEmissao = false;
							$validacao = 'A Data de Vencimento não pode ser anterior a Data de Emissão! <br />';
						}
					}

					//Define se é para rejeitar inclusão de titulos a pagar atrasados
					if($g['C031']['aceitarTitulosPagarAtrasados'] == 'N'){
						$T015_Data_Vencimento = gCorrigeDataInverte($form->campoValorEnviado('T015_Data_Vencimento'));
						$sql = mysql_query("SELECT DATEDIFF(CURDATE(),'{$T015_Data_Vencimento}')");
						$diferenca = mysql_fetch_array($sql);					
						log("Resultado= ". $diferenca[0]);
						if($diferenca[0] > 0){
							$validaData = false;
							$validacao ='A Data de Vencimento do Título está atrasada!';
						} else {
							//a Data de Vencimento não está atrasada
							$validaData = true;
						}

					}

					//Não inclui títulos onde o fornecedor, número e valor da duplicata sejam iguais a algum existente
					if($g['C031']['aceitarTitulosPagarNumeroValorDuplicataIguais'] == 'N'){
						$fornecedor = $form->campoValorEnviado('T015_D024_Id');
						$numeroDuplicata = $form->campoValorEnviado('T015_Numero_Duplicata');
						$valorDuplicata = $form->campoValorEnviado('T015_Valor_Duplicata');
						$T015_C004_Id = $form->campoValorEnviado('T015_C004_Id');
						$sqlValidacaoFNVDuplicata = mysql_query("SELECT T015_D024_Id, T015_Numero_Duplicata, T015_Valor_Duplicata 
														  FROM T015 
														 WHERE T015_D024_Id = '{$fornecedor}' 
														   AND T015_Numero_Duplicata = '{$numeroDuplicata}'
														   AND T015_C004_Id = '{$T015_C004_Id}' 
														   AND (T015_T015_Id_Agrupado <= 0 OR T015_T015_Id_Agrupado IS NULL) 
														   AND T015_Flag_Cancelada != 'S'
														   AND T015_Valor_Duplicata = '{$valorDuplicata}'");
						log("SQL= SELECT T015_D024_Id, T015_Numero_Duplicata, T015_Valor_Duplicata 
														  FROM T015 
														 WHERE T015_D024_Id = '{$fornecedor}' 
														    AND T015_Numero_Duplicata = '{$numeroDuplicata}'
														    AND T015_C004_Id = '{$T015_C004_Id}' 
														    AND (T015_T015_Id_Agrupado <= 0 OR T015_T015_Id_Agrupado IS NULL) 
														   	AND T015_Flag_Cancelada != 'S'
														    AND T015_Valor_Duplicata = '{$valorDuplicata}'");
						if(mysql_num_rows($sqlValidacaoFNVDuplicata) > 0){
							$validaFornecedorNumeroValorDuplicata = false;
							$validacao .= 'Número e valor de duplicata já existem para esse fornecedor, faça a alteração de um desses campos para continuar! <br />';
						}
					}

					if (!empty($validacao)){
						echo gGeraAlertMsg('Operação Inválida',$validacao);
					}

					//Se validaData for true o processo continua normalmente
					if($validaData && $validaDataEmissao && $validaFornecedorNumeroValorDuplicata){

					// Faz o select na conta e testa se a o campo para verificar o saldo da conta está igual a SIM
					$D014_Id = $form->campoValorEnviado('T015_D014_Id');
					$D014=mysql_query("SELECT D014_Flag_Verificar_Saldo from D014 WHERE D014_Id='{$D014_Id}'");
					$mD014 = mysql_fetch_array($D014);

						// Define se é para verificar o saldo da conta selecionada na inclusão de um título a pagar
						if($g['C031']['verificarSaldoConta'] == 'S' && $mD014['D014_Flag_Verificar_Saldo'] == 'S'){
							$Saldo_Conta = gCorrigeNumeroInverte($form->campoValorEnviado('Saldo_Conta'));
							$Valor_Duplicata = gCorrigeNumeroInverte($form->campoValorEnviado('T015_Valor_Duplicata'));
							$T015_Data_Vencimento  = $form->campoValorEnviado('T015_Data_Vencimento');
							$T015_Data_Vencimento  = explode("/", $T015_Data_Vencimento);
							$mes = $T015_Data_Vencimento[1];
							if($Valor_Duplicata > $Saldo_Conta){
								$ValorNecessario = gCorrigeNumero($Saldo_Conta - $Valor_Duplicata);
								$Saldo_Conta = gCorrigeNumero($Saldo_Conta);
								echo gGeraAlertMsg('Erro', 'O valor da duplicata é maior que o saldo da conta! <br />Saldo Conta: R$ <b>'.$Saldo_Conta . "</b><br />Valor Necessário: <b>" . $ValorNecessario."</b>");
							}else{
								if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {

									// Chamada de método que atualiza a projeção orçamentária da conta selecionada paa atualizar o saldo da conta
									$retorno = $FIN009->FIN009RecalculaProjecao($form->campoValorEnviado('T015_D014_Id'),null,null, $mes);
									
									$T015_Id = $g['mysqlLastId'];
			                        $valorOriginal = gCorrigeNumeroInverte($form->campoValorEnviado('T015_Valor_Duplicata'));
			                        $vencOriginal = gCorrigeDataInverte($form->campoValorEnviado('T015_Data_Vencimento'));
			                        mysql_query("UPDATE T015 SET T015_Valor_Original='{$valorOriginal}',T015_Data_Vencimento_Original='{$vencOriginal}' WHERE T015_Id='{$T015_Id}'");
			                        
			                        $valorNumeroParcelas = $form->campoValorEnviado('NumeroParcelas');
									if( !empty($valorNumeroParcelas) && $valorNumeroParcelas !=0 )
									{
										$retorno=$FIN002->duplicarRegistros($T015_Id, $valorNumeroParcelas);
									}
									
									// Insere os Acrésciomos e Descontos
									$jurus = gCorrigeNumeroInverte($form->campoValorEnviado('T015_Valor_Juros'));
									$desconto = gCorrigeNumeroInverte($form->campoValorEnviado('T015_Valor_Desconto'));
									if($jurus > 0){
										mysql_query("INSERT INTO T033 ( T033_T015_Id,
																	T033_C004_Id,
																	T033_Data_Lancamento,
																	T033_Historico,
																	T033_Valor,
																	T033_Flag_Deb_Cred
																	) VALUES (
																	'$T015_Id',
																	'{$g['empresaAtual']}',
																	current_date(),
																	'JUROS',
																	'$jurus',
																	'C'
																	) 
												    ");
										$cErro = mysql_error();
								        if (!empty($cErro)) {
								            mysql_query("ROLLBACK");
								            return "Erro: $cErro";
								        }
									}
									if($desconto > 0){
										mysql_query("INSERT INTO T033 ( T033_T015_Id,
																	T033_C004_Id,
																	T033_Data_Lancamento,
																	T033_Historico,
																	T033_Valor,
																	T033_Flag_Deb_Cred
																	) VALUES (
																	'$T015_Id',
																	'{$g['empresaAtual']}',
																	current_date(),
																	'DESCONTO',
																	'$desconto',
																	'D'
																	) 
												    ");
										$cErro = mysql_error();
								        if (!empty($cErro)) {
								            mysql_query("ROLLBACK");
								            return "Erro: $cErro";
								        }
									}

									// Imprime uma mensagem e fecha a janela
									$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
									echo <<<EOT
										<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
											$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
												setTimeout(function() {
												{$alert}
												abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Título a Pagar', '/fin/fin002/content/fin002content02/', '', [600,500]);
												}, 150);
	  										});
											$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
											divRefresh('{$g['divIdParent']}', true);
										} </script></div>
EOT;
								}
								// NÃO EXISTEM ESSAS FUNÇÕES PARA A TABELA T015. VER QUAIS OS NOMES DAS FUNÇÕES
								if ($T015_Id) {
									mysql_query("call T015_Gravar_Totalizacao('$T015_Id')");
		    						mysql_query("call T015_Baixar_Titulo('$T015_Id','".date('Y-m-d')."')");
								}
							}
						}else{
							if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
									$T015_Id = $g['mysqlLastId'];
			                        $valorOriginal = gCorrigeNumeroInverte($form->campoValorEnviado('T015_Valor_Duplicata'));
			                        $vencOriginal = gCorrigeDataInverte($form->campoValorEnviado('T015_Data_Vencimento'));
			                        mysql_query("UPDATE T015 SET T015_Valor_Original='{$valorOriginal}',T015_Data_Vencimento_Original='{$vencOriginal}' WHERE T015_Id='{$T015_Id}'");
			                        
			                        $valorNumeroParcelas = $form->campoValorEnviado('NumeroParcelas');
									if( !empty($valorNumeroParcelas) && $valorNumeroParcelas !=0 )
									{
										$retorno=$FIN002->duplicarRegistros($T015_Id, $valorNumeroParcelas);
									}
									
									// Insere os Acrésciomos e Descontos
									$jurus = gCorrigeNumeroInverte($form->campoValorEnviado('T015_Valor_Juros'));
									$desconto = gCorrigeNumeroInverte($form->campoValorEnviado('T015_Valor_Desconto'));
									if($jurus > 0){
										mysql_query("INSERT INTO T033 ( T033_T015_Id,
																	T033_C004_Id,
																	T033_Data_Lancamento,
																	T033_Historico,
																	T033_Valor,
																	T033_Flag_Deb_Cred
																	) VALUES (
																	'$T015_Id',
																	'{$g['empresaAtual']}',
																	current_date(),
																	'JUROS',
																	'$jurus',
																	'C'
																	) 
												    ");
										$cErro = mysql_error();
								        if (!empty($cErro)) {
								            mysql_query("ROLLBACK");
								            return "Erro: $cErro";
								        }
									}
									if($desconto > 0){
										mysql_query("INSERT INTO T033 ( T033_T015_Id,
																	T033_C004_Id,
																	T033_Data_Lancamento,
																	T033_Historico,
																	T033_Valor,
																	T033_Flag_Deb_Cred
																	) VALUES (
																	'$T015_Id',
																	'{$g['empresaAtual']}',
																	current_date(),
																	'DESCONTO',
																	'$desconto',
																	'D'
																	) 
												    ");
										$cErro = mysql_error();
								        if (!empty($cErro)) {
								            mysql_query("ROLLBACK");
								            return "Erro: $cErro";
								        }
									}
								
									// Imprime uma mensagem e fecha a janela
									$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
									echo <<<EOT
										<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
											$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
												setTimeout(function() {
												{$alert}
												abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Título a Pagar', '/fin/fin002/content/fin002content02/', '', [600,500]);
												}, 150);
	  										});
											$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
											divRefresh('{$g['divIdParent']}', true);
										} </script></div>

EOT;
							}
								// NÃO EXISTEM ESSAS FUNÇÕES PARA A TABELA T015. VER QUAIS OS NOMES DAS FUNÇÕES
							if ($T015_Id) {
								mysql_query("call T015_Gravar_Totalizacao('$T015_Id')");
		    					mysql_query("call T015_Baixar_Titulo('$T015_Id','".date('Y-m-d')."')");
							}
						}
					}//$validaData
				}//insert
			}//else if ($formCodigo == 2)
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
		
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$("#{$g['divId']} .itemForm-T015_Data_Vencimento").change();
			} </script></div>
EOT;

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="content_form1_btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');

		break;

		/**
		 * Form Principal - Acrescimos e Descontos
		 * Chamado em: fin002tab02
		 */
	case 'fin002form02':
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
		$form_tabela = 'T033';
		$form_campo_pk = 'T033_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'content_form2_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/fin/fin002/form/fin002form02/';

		//form editor
		$form_editor = array('acrescimos_descontos' => '{"content_form2_btn":{"top":5,"left":448,"width":null,"hidden":0},"T033_Historico":{"top":7,"left":9,"width":125,"hidden":0},"T033_Valor":{"top":7,"left":199,"width":60,"hidden":0},"T033_Flag_Deb_Cred":{"top":7,"left":316,"width":60,"hidden":0}}' , );

		// novo formulário
		$form = new Formulario('form_fin002_form2');

		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'acrescimos_descontos' => array(
					'T033_Historico' => array('name' => 'T033_Historico', 'label'=>'Histórico','reload'=>false, 'type' => 'text', 'length' => array(20,10)),
					'T033_Valor' => array('name' => 'T033_Valor', 'label'=>'Valor ','align' => 'right','reload'=>false, 'type' => 'moeda','style'=>'margin-left:5px','length' => array(7,10)),					
					'T033_Flag_Deb_Cred' => array('name' => 'T033_Flag_Deb_Cred','label'=>'Tipo ', 'align' => 'right', 'type' => 'select', 'extra'=>'style="width:60px;margin-left:5px;"','select' => 
						array(
							array('title' => '...', 'value' => ''),
							array('title' => 'Dbt', 'value' => 'D'),
							array('title' => 'Crd', 'value' => 'C'),
						)
					),
					'content_form2_btn' => array('name' => 'content_form2_btn', 'type'=>'submit','reload'=>false,'value'=>'Incluir','align' => 'right', 'style'=> 'float:right;','extra'=>'style="margin-left:5px;padding-left:5px;padding-right:5px;padding-top:0px;height:20px;"', 'reject'=>true),
					'T033_Data_Lancamento' => array('name' => 'T020_Data_Lancamento','type' => 'hidden','value'=>date("Y-m-d")),
					'T033_T015_Id' => array('name' => 'T033_T015_Id','type' => 'hidden','value'=>$_REQUEST['acaoId']),		
					'T033_C004_Id' => array('name' => 'T033_C004_Id','type' => 'hidden','value'=>$g['empresaAtual']),
			
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
				// Insert
				if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
					// Imprime uma mensagem e fecha a janela
					//$alert = gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
					$T033_Id = $g['mysqlLastId'];
					if ($T033_Id) {
						// FUNCAO PERSONALIZADO NO APP FINANCEIRO LANCAMENTO ACRESCIMOS/DESCONTOS EM BANCOS E CAIXAS
						require_once('bibliotecas/classes/FIN001.php');
						$FIN002 = new FIN002;
						$FIN002->vinculaContaGrupoT033($T033_Id, $form);
						mysql_query("call T015_Gravar_Totalizacao('{$_REQUEST['acaoId']}')");
					}
					echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								divRefresh('{$g['divId']}', true);
								divRefresh('{$r_divReload}', true);
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
		//echo '<div style="padding-top:5px;padding-bottom:5px">Inclusão de Acrescimos e Descontos</div>';
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	/**
	 * Comentário do Estorno
	 * Chamado em: grid_func-js - fin002ValidaCampoBxEstorno()
	 */
	case 'fin002form04':

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
		$enviar_btnId = 'content_form4_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/fin/fin002/form/fin002form04/';

		// novo formulário
		$form = new Formulario('form_fin002_form4');

		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		if($g['C031']['HabilitarDataAtualEstornoPagar'] == 'S'){
			$formArray = array(
				'campos' => array(
					'estorno' => array(
						'T019_Obs_Estorno' => array('name' => 'T019_Obs_Estorno', 'label'=>'Observação', 'type' => 'textarea', 'length' => array(8,63)),				
						'acaoId' => array('name' => 'acaoId','type' => 'hidden','reject'=>true, 'value'=>$acaoId),
					)	
				)
			);
        }else{
			/***************************** CARLOS CARVALHO **********************************************/
			$sql = "select 
						D007_Nome_Banco
					from 
						T015 
					left join D007 on T015_D007_Id = D007_Id 
					where 
						T015_Id = '{$acaoId}'";
			$conjuntoDados1 = mysql_query($sql);
			$Var = mysql_fetch_assoc($conjuntoDados1);
			$nomeBanco = $Var['D007_Nome_Banco'];
			$formArray = array(
				'campos' => array(
					'estorno' => array(
						'T019_Obs_Estorno'       => array('name' => 'T019_Obs_Estorno', 'label'=>'Observação', 'type' => 'textarea', 'length' => array(8,63)),
						'D007_Nome_Banco'   => array('name' => 'D007_Nome_Banco', 'label'=> 'Banco', 'type' => 'text', 'length' => array(63,160), 'reject'=>true, 'value' => $nomeBanco, 'disabled' => true, 'readonly' => true),
						'T019_Data_Lancamento'   => array('name' => 'T019_Data_Lancamento', 'label'=>'Data', 'type' => 'data', 'length' => array(8,63)),
						'acaoId' => array('name' => 'acaoId','type' => 'hidden','reject'=>true, 'value'=>$acaoId),
					)	
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
				// Estorna o Título
				if($g['C031']['HabilitarDataAtualEstornoPagar'] == 'S'){
					$retorno = $FIN002->estornar($form->campoValorEnviado('acaoId'), $form->campoValorEnviado('T019_Obs_Estorno'));
				}else{
					$retorno = $FIN002->estornar($form->campoValorEnviado('acaoId'), $form->campoValorEnviado('T019_Obs_Estorno'), gCorrigeDataInverte($form->campoValorEnviado('T019_Data_Lancamento')));
				}
				$retorno = ($retorno === true) ? 'Titulo estornado!' : $retorno;
				// Mensagem
				$alert = gGeraAlertMsg('Operação de Estorno', $retorno, false);
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
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk, "Confirma o estorno?");


		/**
		 * PARTE 5: Exibir o formulário
		 */
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		//echo  '<div style="padding:5px">Estorno de título - ID: '.$acaoId.'</div>';
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('form.tpl');

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="content_form4_btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
					
		break;

		/**
		 * Baixar
		 * Chamado em: fin001tab05
		 */
	case 'fin002form05':
		global $g;
		$habilitarBaixaLoteAjax = (isset($g['C031']['habilitarBaixaLoteAjaxPagar']) && $g['C031']['habilitarBaixaLoteAjaxPagar'] == 'S');
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$extra = isset($r_fin002form05) ? $r_fin002form05['extra'] : $r_extra;

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
		$enviar_btnId = 'content_baixar_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/fin/fin002/form/fin002form05/';

		// novo formulário
		$form = new Formulario('form_fin002_baixar');

		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'baixar' => array(
					'D007_Id' => array('name' => 'D007_Id', 'label' => 'Banco', 'type' => 'select','select' => 
						gGeraSelect('D007','D007_Id','D007_Nome_Banco', "WHERE (1=1) AND IF('{$g['C031']['contasPagarUnificado']}' != 'S', D007_C004_Id = '{$g['empresaAtual']}' AND D007_Flag_Ativo='S',  D007_Flag_Ativo='S')"),
					),
					'dataBaixa' => array('name' => 'dataBaixa','label'=>'Data baixa','type'=>'data','restricao'=>'globalData', 'length' => array(15,10)),
					'extra' => array('name' => 'extra', 'type' => 'hidden', 'value' => @$r_extra, 'reject' => true),
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
					if ($habilitarBaixaLoteAjax) {
						$dadosBaixaLote = array(
							'D007_Id'   => $form->campoValorEnviado('D007_Id'),
							'dataBaixa' => $form->campoValorEnviado('dataBaixa'),
							'extra'     => $form->campoValorEnviado('extra'),
						);
						$dadosBaixaLoteJson = json_encode($dadosBaixaLote);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								var fin002BaixaLoteDados = {$dadosBaixaLoteJson};
								var fin002BaixaLoteLimit = 10;
								var fin002BaixaProgressId = dialogAlert(
									'Processando baixa',
									"<div style='overflow:hidden;'>" +
										"<div id='fin002BaixaProgress' style='line-height:16px;'>Preparando lote...</div>" +
										"<div style='margin-top:6px; text-align:center;'>" +
											"<img src='/hardness3/static/img/loading.gif' style='width:24px; height:24px;' />" +
										"</div>" +
									"</div>"
								);
								$('#dc-' + fin002BaixaProgressId).parent().css('width', '280px');

								function fin002BaixaAtualizarStatus(data) {
									$('#fin002BaixaProgress').html(
										'<div><b>Processando baixa em lotes</b></div>' +
										'<div>Processados: <b>' + data.processado + '</b> de <b>' + data.total + '</b></div>' +
										'<div>Restantes: <b>' + data.restante + '</b></div>'
									);
								}

								function fin002BaixaFinalizar(data) {
									$('#{$g['divId']}').hideLoading();
									$('#dc-' + fin002BaixaProgressId).dialog("destroy").remove();
									dialogConfirm('Operação de Baixa', 'Baixa realizada com sucesso!<br /><br />Processados: <b>' + data.processado + '</b> de <b>' + data.total + '</b>');
									divRefresh('{$g['divIdParent']}', true);
									$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								}

								function fin002BaixaErro(msg) {
									$('#{$g['divId']}').hideLoading();
									$('#dc-' + fin002BaixaProgressId).dialog("destroy").remove();
									dialogConfirm('Operação de Baixa', msg);
								}

								function fin002ExecutarProximoLote(snapshotIds, T045_Id, processadoAtual) {
									var idsLote = snapshotIds.slice(processadoAtual, processadoAtual + fin002BaixaLoteLimit);
									if (idsLote.length <= 0) {
										fin002BaixaFinalizar({ processado: processadoAtual, total: snapshotIds.length, restante: 0 });
										return;
									}

									$.ajax({
										url: '/fin/fin002/form_func-ajax/fin002BaixaLoteProcessar/?ajax=true',
										type: 'POST',
										dataType: 'json',
										data: {
											D007_Id: fin002BaixaLoteDados.D007_Id,
											dataBaixa: fin002BaixaLoteDados.dataBaixa,
											extra: fin002BaixaLoteDados.extra,
											T045_Id: T045_Id,
											processadoAtual: processadoAtual,
											'snapshotIds[]': snapshotIds,
											'idsLote[]': idsLote
										},
										success: function(request) {
											if (request.code) {
												fin002BaixaAtualizarStatus(request.data);
												if (request.data.done) {
													fin002BaixaFinalizar(request.data);
													return;
												}
												fin002ExecutarProximoLote(snapshotIds, T045_Id, request.data.processado);
											} else {
												fin002BaixaErro(request.data);
											}
										},
										error: function() {
											fin002BaixaErro('Falha de comunicacao ao processar o lote da baixa.');
										}
									});
								}

								$('#{$g['divId']}').showLoading();
								$.ajax({
									url: '/fin/fin002/form_func-ajax/fin002BaixaLotePreparar/?ajax=true',
									type: 'POST',
									dataType: 'json',
									data: fin002BaixaLoteDados,
									success: function(request) {
										if (request.code) {
											fin002BaixaAtualizarStatus(request.data);
											fin002ExecutarProximoLote(request.data.ids, request.data.T045_Id, 0);
										} else {
											fin002BaixaErro(request.data);
										}
									},
									error: function() {
										fin002BaixaErro('Falha de comunicacao ao iniciar a baixa em lotes.');
									}
								});
							} </script></div>
EOT;
					} else {
						// baixa o Título (fluxo legado)
						$retorno = $FIN002->baixar($form->campoValorEnviado('D007_Id'),gCorrigeDataInverte($form->campoValorEnviado('dataBaixa')),$form->campoValorEnviado('extra'));
						if ($retorno === true){
							$alert = gGeraAlertMsg('Operação de Baixa', 'Baixa realizada com sucesso!', false);
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
						}else{
							echo gGeraAlertMsg('Operação de Baixa', $retorno);
						}
					}
				}
			}
		/**
		 * PARTE 4: Gerar o formulário
		 */
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk,'Tem certeza? Os dados informados acima estao corretos?');

		// acrescenta o div com o grid
		$gridId = uniqid();
		//echo '<div id="'.$gridId.'"></div>';
		echo '<br /><hr />';
		echo '<p align="center" style="font-size:14px;font-weight:bold;"><b>BAIXAR</b></p>';

		$actions = array(
		// identificador, url, paramentros get (opcional)
		array($gridId, '/fin/fin002/grid/fin002grid04/','&extra='.urlencode($r_extra)),
		);
		/**
		 * PARTE 5: Exibir o formulário
		 */
		$g['smarty']->assign('formularioID', $form->formularioID());
		// alinhando a table do form
		$g['smarty']->assign('tableAlign','align="center"');
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		//echo  '<div style="padding-top:5px;padding-bottom:5px">Baixar</div>';
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('form.tpl');
		//echo gProcessaAcoes($actions);

		

	break;
        
        /**
        * Agrupar
        */
        case 'fin002FormAgrupar':
        	/*            
            // acrescenta o div com o grid
            $gridId = uniqid();        
            echo '<div id="'.$gridId.'"></div>';        
            
            $actions = array(
                // identificador, url, paramentros get (opcional)
                array($gridId, '/fin/fin002/grid/fin002GridAgrupar/','&extra='.urlencode($r_extra)),
            );
            echo gProcessaAcoes($actions);
            
            // espaços depois do grid
            echo '<br /><br />';
            */
            
            /**
            * PARTE 1: Variavéis envidas para o formulário
            */        
            // Variável indica qual tab do form será exibida
            $tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
            // Utilizada em UPDATE, é o ID do registro que está sendo editado
            $acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;    
            $extra = isset($r_fin002FormAgrupar) ? $r_fin002FormAgrupar['extra'] : $r_extra;    

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
            $form_tabela = 'T015';
            $form_campo_pk = 'T015_Id';
            // ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
            $enviar_btnId = 'content_agrupar_btn';
            // Form que será executado ao clicar no botão
            $enviar_acaoHref = '/fin/fin002/form/fin002FormAgrupar/';

            // Editor
			$form_editor = array(
				'agrupar' => '{"T015_D031_Id":{"top":30,"left":113,"width":159,"hidden":0},"agruparNovoVencimento":{"top":30,"left":348,"width":72,"hidden":0},"aviso":{"top":53,"left":62,"width":null,"hidden":0}}',
			);
                    
            // novo formulário
            $form = new Formulario('form_fin002_agrupar');        
            
            $formArray = array(
                'campos' => array(
                    'agrupar' => array(
                        'T015_D031_Id' => array('name' => 'T015_D031_Id', 'label' => 'Portador', 'type' => 'select','select' => 
                            gGeraSelect('D031','D031_Id','D031_Portador'),
                        ),
                        'agruparNovoVencimento' => array('name' => 'agruparNovoVencimento','label'=>'Novo Vencimento ', 'align'=>'right','type' => 'data', 'length' => array(15,10)),
                        'extra' => array('name' => 'extra', 'type' => 'hidden', 'value' => @$r_extra, 'reject' => true),
                        'aviso' => array('name' => 'aviso', 'type' => 'comentario', 'value' => "<div class='msgAviso'>Atenção!!! Esse processo só poderá ser feito uma vez, tenha certeza de informar corretamente os dados acima.</div>", 'reject' => true),
                    )    
                )
            );    

            $form->definirFormularioArray($formArray);
            
            /**
            * Na maioria dos casos não será necessário alterar nada daqui para baixo
            * Somente em casos especiais, para alterar pequenos detalhes ou modificações do visual
            */
            
            
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
                    $retorno = $FIN002->agrupar($form->campoValorEnviado('T015_D031_Id'), $form->campoValorEnviado('agruparNovoVencimento'),$form->campoValorEnviado('extra'));
                    if ($retorno === true) {
                        $alert = gGeraAlertMsg('Agrupar', 'Agrupado!', false);
                    } else {
                        $alert = gGeraAlertMsg('Agrupar', $retorno, false);
                    }
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
            $g['smarty']->assign('tableAlign','align="center"');
            $g['smarty']->assign('table',$gerado['table']);
            
            echo $gerado['pagina'];
            if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
            $g['smarty']->display('formEditor.tpl');        
            
            //echo '<div class="msgAviso">Atenção!!! Esse processo só poderá ser feito uma vez, tenha certeza de informar corretamente os dados acima.</div>';
            
        break;
	
	case 'fin002formReparcelamento':
		global $g;
		
		$T196_Id = (isset($r_acaoId)) ? $r_acaoId : false;
		
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
		$form_tabela = 'T196';
		$form_campo_pk = 'T196_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'etc010FormUsuariosClientesBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/etc/etc010/form/etc010FormUsuariosClientes/';
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					'T196_D024_Id' => array('name' => 'T196_D024_Id', 'label' => 'Cliente/Fornecedor', 'extra'=>'style="width:350px;"', 'disabled'=>true, 'type' => 'select', 
						'select' =>  gGeraSelect('D024', 'D024_Id','D024_Nome_Empresa',"" ),
					),
					'T196_C007_Id' => array('name' => 'T196_C007_Id', 'label' => 'Usuário', 'extra'=>'style="width:350px;"', 'disabled'=>true, 'type' => 'select', 
						'select' =>  gGeraSelect('C007', 'C007_Id','C007_Primeiro_Nome', ''),
					),
					'T196_Tipo' => array('name' => 'T196_Tipo', 'label' => 'Tipo', 'type' => 'select', 'extra'=>'style="width:350px;"', 'disabled'=>true, 'select' => 
						array(
							array('title' => '...', 'value' => ''),
							array('title' => 'Reparcelamento', 'value' => 'R'),
							array('title' => 'Agrupamento', 'value' => 'A'), 
						)
					),
					'T196_Data_Emissao' => array('name' => 'T196_Data_Emissao', 'label' => 'Data','disabled'=>true, 'type' => 'data', 'length' => array(10,15)),
					'T196_Valor_Total' => array('name' => 'T196_Valor_Total', 'label' => 'Valor Total','disabled'=>true, 'type' => 'moeda', 'length' => array(10,30)),
					//'T196_Titulos_Geradores' => array('name' => 'T196_Titulos_Geradores', 'label' => 'Títulos Geradores','disabled'=>true, 'type' => 'textarea', 'length' => array(4,60)),
					//'T196_Titulos_Gerados' => array('name' => 'T196_Titulos_Gerados', 'label' => 'Títulos Gerados','disabled'=>true, 'type' => 'textarea', 'length' => array(4,60)),
				)
			)
		);
        $form = new Formulario('form-fin001FormPrincipalReparcelamento');
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
						//$alert = gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
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
    
    case 'fin002FormReparcelameto':
        $acaoId = (isset($r_acaoId)) ? $r_acaoId : false; 
        $T005_Id = (isset($r_T005_Id)) ? $r_T005_Id : false;            
        
        //echo $T005_Id;
        //('qqqq');
        /**
        * PARTE 1: Variavéis envidas para o formulário
        */
        // Variável indica qual tab do form será exibida
        $tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
        $r_divReload = isset($r_ven002FormPagamento) ? $r_ven002FormPagamento['divReload'] : $r_divReload;  
        $extra = isset($r_fin002FormPagamento) ? $r_fin002FormPagamento['extra'] : $r_extra;
        
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
        $form_tabela = '';
        $form_campo_pk = '';
        // ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
        $enviar_btnId = 'fin002FormReparcelamento_btn';
        // Form que será executado ao clicar no botão
        $enviar_acaoHref = '/fin/fin002/form/fin002FormReparcelameto/';

        //form editor
		$form_editor = array(
			'geral' => '{"Total_Parcelas":{"top":7,"left":64.21875,"width":164,"hidden":0},"selecionaPrimeiroVencimento":{"top":7,"left":298.203125,"width":105,"hidden":0},"primeiroVencimentoData":{"top":7,"left":496,"width":72,"hidden":0},"primeiroVencimentoNum":{"top":7,"left":496,"width":72,"hidden":0},"Portador_D031_Id":{"top":30,"left":63.703125,"width":165,"hidden":0},"selecionaDias":{"top":30,"left":296.484375,"width":105,"hidden":0},"IntervalorDias":{"top":30,"left":496,"width":72,"hidden":0},"diaFixo":{"top":30,"left":496,"width":72,"hidden":0},"Ndcto":{"top":53,"left":8.015625,"width":152,"hidden":0},"Emissao":{"top":53,"left":310.953125,"width":88,"hidden":0},"Nduplicata":{"top":76,"left":18.515625,"width":152,"hidden":0},"fin002FormReparcelamento_btn":{"top":74,"left":496,"width":null,"hidden":0},"fin002FormAdicionarParcela_btn":{"top":74,"left":608,"width":null,"hidden":0}}', 
		);
        
        $queryT015 = <<<EOT
	        SELECT 
	        T015_Numero_Duplicata, 
	        T015_Data_Emissao, 
	        D024_Nome_Empresa,
	        D024_Id, 
	        T015_Data_Vencimento, 
	        T015_Valor_Duplicata, 
	        T015_Valor_Saldo 
	        FROM T015 
	        left join D024 on D024_Id=T015_D024_Id 
	        left join D031 on D031_Id=T015_D031_Id 
	        left join D014 on D014_Id=T015_D014_Id
			left join D032 on D032_Id=D014_D032_Id 
	        left join D046 on D046_Id=D024_D046_Id 
	        left join D073 on D073_Id=T015_D073_Id
	        left join T007 on T007_Id=T015_T007_Id 
	        LEFT JOIN C004 ON C004_Id=T015_C004_Id 
	        left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno 
	        WHERE                      
EOT;
	    //Ao clicar em "gerar parcelas" o form atualiza e o extra retorna com os caracteres '+' e '=' codificados em ASCII, é preciso fazer um replace para que funcione
	    $extra = str_replace("%3D", "=", $extra);
	    $extra = str_replace("%2B", "+", $extra);
        $queryT015 .= gPegarParteWhere(gInsertExtraWhere(base64_decode($extra), "(T015_T015_Id_Agrupado<=0 or T015_T015_Id_Agrupado is null) AND T015_Flag_Reparcelar='S' AND T015_Data_Pagamento='0000-00-00' AND T015_C004_Id='{$g['empresaAtual']}'"));
	    $queryT015 .= "ORDER BY T015_Data_Vencimento";

        $T015 = mysql_query($queryT015);

        $valorSomado = 0;

        while($aT015 = mysql_fetch_array($T015)){
            $D024_Id = $aT015['D024_Id'];
            $valorSomado += $aT015['T015_Valor_Saldo'];
        }
        $erro=mysql_error();
        if (!empty($erro)){
            log("$erro");
        }

        // die(); 
        
        // novo formulário                        
        
        $form = new Formulario('fin002FormReparcelamento');            
        
        $select = array();
        $select[] = array('value' => '0', 'title' => 'A COMBINAR');
        for ($i=1; $i<=$g['C031']['numeroMaxParcelas']; $i++) {
            //$valorParcela=$FIN002->fin002calcularParcelaCombo($i,$valorSomado);
            $valorParcela=gCorrigeNumero($valorSomado / $i);
            //echo $valorParcela;
            if($i=='1'){
                $select[] = array('value' => $i, 'title' => str_pad($i,2,"0",STR_PAD_LEFT) . ($i == 1 ? " Parcela&nbsp;&nbsp;" : " Parcelas") . " - " .$valorParcela, 'selected'=>true);
            }else{
                $select[] = array('value' => $i, 'title' => str_pad($i,2,"0",STR_PAD_LEFT) . ($i == 1 ? " Parcela&nbsp;&nbsp;" : " Parcelas") . " - " .$valorParcela);
            }
        }
        // Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
        $formArray = array(
            'campos' => array(
                'geral' => array(
                    'Total_Parcelas' => array('name' => 'Total_Parcelas', 'label' => 'Parcelas', 'type' => 'select', 'select' => $select, 'extra' => 'style="width:164px;"', 'reject' => true),
                    //'valorParcelaSimulacao' => array('name' => 'valorParcelaSimulacao', 'label' => '', 'type' => 'text','extra'=>'style="background:#E8E8E8"','reject' => true, 'length' => array(3,6), 'align' => 'right', 'readonly'=>'readonly'),
                    'selecionaPrimeiroVencimento' => array('name' => 'selecionaPrimeiroVencimento', 'label'=>'Primeira Parcela', 'reject'=>true, 'type' => 'select', 'extra' => 'style="width:105px;"','select' => 
                    array(
                        array('title' => 'Dias', 'value' => 'Dias', 'selected' => true),
                        array('title' => 'Data', 'value' => 'Data'),
                    )),
                    'primeiroVencimentoNum' => array('name' => 'primeiroVencimentoNum', 'label' => '', 'type' => 'text', 'reject' => true, 'length' => array(3,6), 'align' => 'right'),
                    'primeiroVencimentoData' => array('name' => 'primeiroVencimentoData', 'label' => '', 'type' => 'data', 'reject' => true, 'align' => 'right'),
                    'selecionaDias' => array('name' => 'selecionaDias', 'label'=>'Demais Parcelas', 'type' => 'select', 'reject'=>true, 'extra' => 'style="width:105px;"','select' => 
                    array(
                        array('title' => 'Intervalo', 'value' => 'I', 'selected' => true),
                        array('title' => 'Dia Fixo', 'value' => 'F'),
                    )),            
                    'IntervalorDias' => array('name' => 'IntervalorDias', 'label' => '', 'type' => 'text', 'reject' => true, 'length' => array(3,6), 'align' => 'right'),
                    'diaFixo' => array('name' => 'diaFixo', 'label' => '', 'type' => 'text', 'reject' => true, 'length' => array(3,2), 'align' => 'right'),                                                
                    'Portador_D031_Id' => array('name' => 'D031_Id','label'=>'Portador', 'extra'=>'style="width:165px;"', 'reject'=>true, 'type' => 'select', 'select' =>
                        gGeraSelect('D031', 'D031_Id', 'D031_Portador','order by D031_Portador')
                    ),
                    'Ndcto' => array('name' => 'Ndcto', 'label' => 'Número Documento', 'type' => 'text', 'reject' => true, 'length' => array(27,100)),
                    'Nduplicata' => array('name' => 'Nduplicata', 'label' => 'Número Duplicata', 'type' => 'text', 'reject' => true, 'length' => array(27,100)),
                    'Emissao' => array('name' => 'Emissao', 'label' => 'Data Emissão', 'type' => 'data', 'value'=>date("d/m/Y"), 'reject' => true, 'length' => array(10,100)),
                    'fin002FormReparcelamento_btn' => array('name' => 'fin002FormReparcelamento_btn', 'type'=>'submit','reload'=>false,'value'=>'Gerar Parcelas','align' => 'right', 'reject'=>true),
                    'fin002FormAdicionarParcela_btn' => array('name' => 'fin002FormAdicionarParcela_btn', 'type'=>'submit', 'reload' => false, 'value' => 'Adicionar Parcela', 'align' => 'right', 'style' => 'float:right;', 'reject' => true),
                    // 'T005_Observacao_Pagamento' => array('name' => 'T005_Observacao_Pagamento', 'label' => 'Observação', 'type' => 'textarea', 'length' => array(1,23)),
                    // 'ven002SalvarObservacao_btn' => array('name' => 'ven002SalvarObservacao_btn', 'type'=>'submit', 'reload' => false, 'value' => 'Salvar Observação', 'align' => 'right', 'style' => 'float:right; margin-right:120px; margin-top:15px;', 'reject' => true),                        'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true),
                    'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true),
                    'D024_Id' => array('name' => 'D024_Id', 'label' => '', 'type' => 'hidden', 'value' => @$D024_Id, 'reject' => true),
                    'valorSomado' => array('name' => 'valorSomado', 'label' => '', 'type' => 'hidden', 'value' => @$valorSomado, 'reject' => true),
                    'extra' => array('name' => 'extra', 'type' => 'hidden', 'value' => @$r_extra, 'reject' => true),
                )
            )
        );
 
        $form->definirFormularioArray($formArray);
        
        echo <<<EOT
            <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                $('#fin002FormAdicionarParcela_btn').unbind('click');
                $('#fin002FormAdicionarParcela_btn').unbind('click').bind('click', function(data) {
                    var id = '{$row['T005_D024_Id']}';
                        $.getJSON('/fin/fin002/form_func-ajax/inserirParcela/?ajax=true=' + '&' + $("#{$formId}").serialize() + '&callback=?', function(request) {
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
                    $.getJSON('/fin/fin002/form_func-ajax/fin002calcularParcelaSimulacao/?ajax=true=' + '&' + $("#{$formId}").serialize() + '&callback=?', function(request) {
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
         *            E carregamento dos valores (se não foi enviado e possui acaoId)
         */
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
            // Form com erros
            if ($formCodigo == 1) {
                echo gGeraAlertMsgErroForm($form);
                // Form sem erros
            } else if ($formCodigo == 2) {
                // Gerar Boleto            
                $retorno = $FIN002->fin002atualizarParcelas($form);
                if ($retorno === true) {
                    $alert = gGeraAlertMsg('Operação OK', 'Parcelas cadastradas com sucesso.', false);
                    echo <<<EOT
                        <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                            if (typeof jQuery != 'undefined') {
                               	//divRefresh("{$g['divId']}", true);
                            	//$alert;	
                               	divRefresh('{$form->campoValorEnviado('divReload')}');
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

    case 'fin002FormDocumentos':
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
		$form_tabela = 'T243';
		$form_campo_pk = 'T243_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'fin002FormDocumentosBtnAdd';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/fin/fin002/form/fin002FormDocumentos/';
		
		//Form Editor
		$form_editor = array(
			'geral' => '{"uploadArquivo":{"top":4,"left":12,"width":243,"hidden":0},"fin002FormDocumentosBtnAdd":{"top":5,"left":416,"width":null,"hidden":0}}'
		);
		
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T243_T015_Id' => array('name' => 'T243_T015_Id', 'type' => 'hidden', 'value' => $r_T015_Id),
					'T243_Data_Hora_Upload' => array('name' => 'T243_Data_Hora_Upload', 'type' => 'hidden', 'value' => date("Y-m-d H:i:s")),
					'divReload' => array('name' => 'divReload', 'label'=>'', 'type' => 'hidden','value' => @$r_divReload, 'reject' => true),
					'uploadArquivo' => array('name' => 'uploadArquivo', 'label' => 'Arquivo documento', 'type' => 'file', 'length' => array(24, 24), 'reject' => true, 'uppercase' => false),
					'fin002FormDocumentosBtnAdd' => array('name' => 'fin002FormDocumentosBtnAdd', 'type' => 'submit', 'reload' => false, 'value' => 'Incluir', 'align' => 'right', 'style' => 'float:right;', 'reject' => true),
					'T243_Nome_Arquivo' => array('name' => 'T243_Nome_Arquivo', 'type' => 'hidden', 'uppercase' => false),
				)
			)
		);
		
		$form = new Formulario('fin002FormDocumentos');
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
						$form->campoSetarValor('T243_Nome_Arquivo', $arquivo['name'], true);
						if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
							// Gravar o arquivo no disco
							$pathinfo = pathinfo($arquivo['name']);
							$pathinfo['extension'] = empty($pathinfo['extension']) ? '.tmp' : $pathinfo['extension'];
							$dir = "{$g['pathDados']}documentos/contas_pagar";
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
    break;

    case 'fin002FormDivisao':
        $acaoId = (isset($r_acaoId)) ? $r_acaoId : false; 
        $T005_Id = (isset($r_T005_Id)) ? $r_T005_Id : false;            
        
        //echo $T005_Id;
        //('qqqq');
        /**
        * PARTE 1: Variavéis envidas para o formulário
        */
        // Variável indica qual tab do form será exibida
        $tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
        $r_divReload = isset($r_ven002FormPagamento) ? $r_ven002FormPagamento['divReload'] : $r_divReload;    
        $extra = isset($r_fin002FormPagamento) ? $r_fin002FormPagamento['extra'] : $r_extra;
        
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
        $form_tabela = '';
        $form_campo_pk = '';
        // ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
        $enviar_btnId = 'fin002FormDivisao_btn';
        // Form que será executado ao clicar no botão
        $enviar_acaoHref = '/fin/fin002/form/fin002FormDivisao/';

        //form editor
		$form_editor = array(
			'geral' => '{"Total_Parcelas":{"top":7,"left":31,"width":208,"hidden":0},"fin002FormDivisao_btn":{"top":5,"left":320,"width":null,"hidden":0},"Emissao":{"top":7,"left":454,"width":72,"hidden":1}}', 
		);
        
        $queryT015 = <<<EOT
	        SELECT 
	        T015_Numero_Duplicata, 
	        T015_Numero_Documento, 
	        T015_Data_Emissao, 
	        D024_Nome_Empresa,
	        D024_Id, 
	        T015_Data_Vencimento, 
	        T015_Valor_Duplicata, 
	        T015_Valor_Saldo, 
	        T015_D031_Id, 
	        T015_D073_Id
	        FROM T015 
	        LEFT JOIN D024 ON D024_Id=T015_D024_Id
			LEFT JOIN D073 ON D073_Id=T015_D073_Id
			LEFT JOIN D031 ON D031_Id=T015_D031_Id
			LEFT JOIN D014 ON D014_Id=T015_D014_Id
			LEFT JOIN D032 ON D032_Id=D014_D032_Id 
			LEFT JOIN D154 ON D154_Id=D014_D154_Id
			LEFT JOIN T007 ON T007_Id=T015_T007_Id
			LEFT JOIN C004 ON C004_Id=T015_C004_Id
			LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno
	        WHERE                      
EOT;
	    //Ao clicar em "gerar parcelas" o form atualiza e o extra retorna com os caracteres '+' e '=' codificados em ASCII, é preciso fazer um replace para que funcione   	
        $extra = str_replace("%3D", "=", $extra);
        $extra = str_replace("%2B", "+", $extra);
        $queryT015 .= gPegarParteWhere(gInsertExtraWhere(base64_decode($extra), "(T015_T015_Id_Agrupado<=0 or T015_T015_Id_Agrupado is null) AND T015_Flag_Reparcelar='S' AND T015_Data_Pagamento='0000-00-00'"));
        $queryT015 .= "ORDER BY T015_Data_Vencimento DESC";
        $T015 = mysql_query($queryT015);

        
        $valorSomado = 0;
        while($mT015 = mysql_fetch_array($T015)){
            $valorSomado = ($valorSomado + $mT015['T015_Valor_Saldo']);
            $registroT015 = $mT015;
        }

        $form = new Formulario('fin002FormDivisao');            
        
        $select = array();
        $select[] = array('value' => '0', 'title' => 'A COMBINAR');
        for ($i=1; $i<=60; $i++) {
            $valorParcela = gCorrigeNumero($valorSomado/$i);
            if($i=='1'){
                $select[] = array('value' => $i, 'title' => str_pad($i,2,"0",STR_PAD_LEFT) . " PARCELA&nbsp;&nbsp; - " .$valorParcela, 'selected'=>true);
            }else{
                $select[] = array('value' => $i, 'title' => str_pad($i,2,"0",STR_PAD_LEFT) . " PARCELAS - " .$valorParcela);
            }
        }
        // Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
        $formArray = array(
            'campos' => array(
                'geral' => array(
                    'Total_Parcelas' => array('name' => 'Total_Parcelas', 'label' => 'Parcelas', 'type' => 'select', 'select' => $select, 'reject' => true),
                    'fin002FormDivisao_btn' => array('name' => 'fin002FormDivisao_btn', 'type'=>'submit','reload'=>false,'value'=>'Gerar Parcelas','align' => 'right', 'reject'=>true),
                    'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true),
                    'valorSomado' => array('name' => 'valorSomado', 'label' => '', 'type' => 'hidden', 'value' => @$valorSomado, 'reject' => true),
                    'extra' => array('name' => 'extra', 'type' => 'hidden', 'value' => $r_extra, 'reject' => true),
                    'registroT015' => array('name' => 'registroT015', 'type' => 'hidden', 'value' => serialize($registroT015), 'reject' => true),
                )
            )
        );
 
        $form->definirFormularioArray($formArray);     
             
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
                // Gerar Boleto            
                $retorno = $FIN002->gerarParcelasDivisao($form);
                if ($retorno === true) {
                    $alert = gGeraAlertMsg('Operação OK', 'Parcelas geradas com sucesso.', false);
                    echo <<<EOT
                        <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                                if (typeof jQuery != 'undefined') {
                                   //divRefresh("{$g['divId']}", true);
                                   divRefresh('{$form->campoValorEnviado('divReload')}');
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

    case 'fin002FormImportarCSV':
	
		/**
		* PARTE 1: Variáveis envidas para o formulário
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
		$form_tabela = 'T015';
		$form_campo_pk = 'T015';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'fin002FormImportarCSV_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/fin/fin002/form/fin002FormImportarCSV/';
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponíveis (automacaoFormulario.php)
		$form = new Formulario('fin002FormImportarCSV');
		$formArray = array(
			'campos' => array(
				'geral' => array(
				)
			)
		);
		$formArray['campos']['geral']['uploadArquivoCSVFIN002'] 		= array('name' => 'uploadArquivoCSVFIN002', 'label' => 'Selecione o CSV', 'type' => 'file', 'length' => array(24, 24), 'reject' => true);
		$formArray['campos']['geral']['fin002FormImportarCSV_btn'] 		= array('name' => 'fin002FormImportarCSV_btn', 'type'=>'submit', 'reload' => false, 'value' => 'Enviar', 'align' => 'right', 'style' => 'float:right;', 'extra' => 'style="margin-right:10px;margin-top:0px;padding-left:10px;padding-right:10px;padding-top:0px;height:20px;"', 'reject'=>true);
		$formArray['campos']['geral']['T015_Arquivo_Csv'] 				= array('name' => 'T015_Arquivo_Csv', 'type' => 'hidden');
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
				$arquivo = $form->campoValorEnviado('uploadArquivoCSVFIN002');
				$retorno = $FIN002->importarCSV($arquivo);
				
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
