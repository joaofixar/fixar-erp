<?php
namespace hardness;

require_once('prog/est/est011/form_func-js.php');
require_once("bibliotecas/classes/EST011.php");
$EST011 = new EST011();

/**
 * Ações
 */
switch ($g['r'][3]) {
	
	case 'est011FormIncluirProduto':
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
		$form_tabela = 'T182';
		$form_campo_pk = 'T182_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est011FormIncluirProduto_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est011/form/est011FormIncluirProduto/';
		$form_editor = array(
			'geral' => '{"T182_D024_Id_Cliente":{"top":7,"left":56.78125,"width":426,"hidden":0},"T182_D013_Id_Cliente":{"top":30,"left":51.765625,"width":447,"hidden":0},"formContatoGarantiaCliente":{"top":28,"left":560,"width":null,"hidden":0},"T182_NF_Venda":{"top":53,"left":35.65625,"width":56,"hidden":0},"button":{"top":51,"left":160,"width":null,"hidden":0},"T182_Pedido":{"top":53,"left":280.546875,"width":56,"hidden":0},"T182_NF_Retorno_Cliente":{"top":76,"left":42.375,"width":56,"hidden":0},"T182_NF_Remessa_Fornecedor":{"top":76,"left":250.78125,"width":56,"hidden":0},"T182_NF_Retorno_Fornecedor":{"top":99,"left":32.828125,"width":56,"hidden":0},"T182_NF_Remessa_Cliente":{"top":99,"left":260.328125,"width":56,"hidden":0},"Procura_Produto_Inclusao":{"top":122,"left":51.75,"width":426,"hidden":0},"Descricao_Produto":{"top":145,"left":41.71875,"width":440,"hidden":0},"Marca_Produto":{"top":168,"left":59.65625,"width":440,"hidden":0},"T182_D082_Id_Marca_Recebida":{"top":76,"left":383.875,"width":169,"hidden":1},"T182_Quantidade":{"top":191,"left":26.984375,"width":136,"hidden":0},"T182_D018_Id":{"top":191,"left":477.390625,"width":47,"hidden":0},"T182_D164_Id":{"top":214,"left":61.671875,"width":143,"hidden":0},"T182_Lote_Fabricacao":{"top":214,"left":268.0625,"width":184,"hidden":0},"T182_D024_Id_Fornecedor":{"top":237,"left":34.953125,"width":426,"hidden":0},"T182_D013_Id_Fornecedor":{"top":260,"left":51.765625,"width":447,"hidden":0},"formContatoGarantiaFornecedor":{"top":258,"left":560,"width":null,"hidden":0},"T182_Preco":{"top":283,"left":62.1875,"width":136,"hidden":0},"T182_Data":{"top":306,"left":16.53125,"width":136,"hidden":0},"T182_Hora":{"top":306,"left":243,"width":136,"hidden":0},"T182_Data_Conclusao":{"top":329,"left":14.578125,"width":136,"hidden":0},"T182_Hora_Conclusao":{"top":329,"left":243,"width":136,"hidden":0},"T182_C007_Id":{"top":352,"left":45.625,"width":143,"hidden":0},"T182_D162_Id":{"top":375,"left":59.171875,"width":447,"hidden":0},"T182_Observacao":{"top":398,"left":32.296875,"height":85,"width":440,"hidden":0},"T182_Acao":{"top":490,"left":65.40625,"height":70,"width":440,"hidden":0}}',
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
			'est011CampoProcuraProduto',																		// callback js
			$g['divId'],																						// divId
			(''),																								// parametro extra
			array(																								// parametros para cada campo do grid
				array('colunaAlinhar' => 'center'),
				array(),
				array(),
				array('callback' => 'gCorrigeNumero', 'colunaAlinhar' => 'right')
			)
		);		
		
		$T182=mysql_query("select * 
		                    from T182 
	                   left join D009 on D009_Id=T182_D009_Id
	                   left join D049 on D049_Id=D009_D049_Id
	                   left join D082 on D082_Id=D049_D082_Id
	                   left join D001 on D001_Id=D049_D001_Id	
		                   where T182_Id='$acaoId'");
		$mT182=mysql_fetch_array($T182);		


		if(empty($r_acaoId)){
			$WHERE_D013_Cliente = ' WHERE D013_Id=0 ';
			$WHERE_D013_Fornecedor = ' WHERE D013_Id=0 ';
		}else{
			$WHERE_D013_Cliente = " WHERE D013_D024_Id='{$mT182['T182_D024_Id_Cliente']}' AND D013_Flag_Ativo = 'S'";
			$WHERE_D013_Fornecedor = " WHERE D013_D024_Id='{$mT182['T182_D024_Id_Fornecedor']}' AND D013_Flag_Ativo = 'S'";
		}

		// novo formulário
		$form = new Formulario('est011FormIncluirProduto');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T182_D024_Id_Cliente' => array(
						'name' => 'T182_D024_Id_Cliente', 
						'label'=> "Cliente", 
						'type' => 'pesquisa', 
						'extra' => "onchange=\"carregaContatoGarantiaCliente(this, '{$g['divId']}');\"", 
						'pesquisa' => array('D024','D024_Id','D024_Nome_Empresa,D020_Nome_Cidade,D018_UF,D024_Cpf,D024_Cnpj,D024_DDD_Telefone_1,D024_Telefone_1,D024_Nome_Fantasia,D024_Id','left join D020 on D020_Id=D024_D020_Id left join D018 on D018_Id=D024_D018_Id WHERE D024_Flag_Cliente="S" and D024_Flag_Ativo="S" order by D024_Nome_Empresa','%1 - %9 - [%2/%3] - (%4%5)'), 'length' => array(80,30)),
					'T182_D013_Id_Cliente' => array(
						'name' => 'T182_D013_Id_Cliente', 
						'label' => 'Contato', 
						'type' => 'select', 
						'selectRefresh' => array('D013','D013_Id','D013_Nome_Contato,D013_DDD_Telefone_1,D013_Telefone_1,D013_E_Mail',"where D013_Flag_Ativo = 'S' ORDER BY D013_Nome_Contato", true),
						'select' => gGeraSelect('D013','D013_Id','D013_Nome_Contato,D013_DDD_Telefone_1,D013_Telefone_1,D013_E_Mail',"$WHERE_D013_Cliente ORDER BY D013_Nome_Contato", true)
					),
					'T182_NF_Venda' => array(
						'name' => 'T182_NF_Venda',
						'label'=>'NF Venda', 
						'type' => 'text', 
						'length' => array(20,30)
					),
					'T182_NF_Remessa_Cliente' => array(
						'name' => 'T182_NF_Remessa_Cliente',
						'label'=>'NF Rem.Cli', 
						'type' => 'text', 
						'length' => array(20,30)
					),
					'T182_NF_Remessa_Fornecedor' => array(
						'name' => 'T182_NF_Remessa_Fornecedor',
						'label'=>'NF Rem.Forn', 
						'type' => 'text', 
						'length' => array(20,30)
					),
					'T182_NF_Retorno_Fornecedor' => array(
						'name' => 'T182_NF_Retorno_Fornecedor',
						'label'=>'NF Ret.Forn', 
						'type' => 'text', 
						'length' => array(20,30)
					),
					'T182_NF_Retorno_Cliente' => array(
						'name' => 'T182_NF_Retorno_Cliente',
						'label'=>'NF Ret.Cli', 
						'type' => 'text', 
						'length' => array(20,30)
					),
					'button' => array('name' => 'button', 'type' => 'button','value' =>'NAO TEM','reload' => false,'extra' => 'onclick="preencheSemNota()"','align' => 'right', 'reject'=>true),
					'formContatoGarantiaCliente' => array(
						'name' => 'FormContatoGarantiaCliente', 
						'type'=>'submit', 
						'reload' => false, 
						'value' => 'Incluir', 
						'align' => 'right', 
						'reject' => true),
					'Procura_Produto_Inclusao' => array(
						'name' => 'Procura_Produto_Troca' , 
						'label' => 'Produto', 
						'type' => 'pesquisaGrid', 
						'pesquisaGrid' => $pesquisaGrid, 
						'length' => array(64,120), 
						'reject' => true, 
						'value' => $mT182['T182_D009_Id']),
					'Descricao_Produto' => array(
						'name' => 'Descricao_Produto', 
						'label' => 'Descrição', 
						'reject'=>true, 
						'readonly'=>true, 
						'type' => 'text', 
						'length' => array(64, 120), 
						'value' => $mT182['D001_Descricao_Produto']),
					'Marca_Produto' => array(
						'name' => 'Marca_Produto', 
						'label' => 'Marca', 
						'type' => 'text', 
						'length' => array(20,255),
						'readonly'=>true, 
						'reject' => true, 
						'value' => $mT182['D082_Marca']),
 					'T182_D082_Id_Marca_Recebida' => array(
						'name' => 'T182_D082_Id_Marca_Recebida', 
						'label'=> "Marca recebida", 
						'type' => 'pesquisa', 
						'pesquisa' => array('D082','D082_Id','D082_Marca','ORDER BY D082_Marca'), 'length' => array(80,30)), 
					'T182_Quantidade' => array(
						'name' => 'T182_Quantidade',
						'label'=>'Quantidade', 
						'type' => 'text', 
						'length' => array(20,30)),
					'T182_D024_Id_Fornecedor' => array(
						'name' => 'T182_D024_Id_Fornecedor', 
						'label'=> "Fornecedor", 
						'type' => 'pesquisa', 
						'extra' => "onchange=\"carregaContatoGarantiaFornecedor(this, '{$g['divId']}');\"", 
						'pesquisa' => array('D024','D024_Id','D024_Nome_Empresa,D020_Nome_Cidade,D018_UF,D024_Cpf,D024_Cnpj,D024_DDD_Telefone_1,D024_Telefone_1,D024_Nome_Fantasia,D024_Id','left join D020 on D020_Id=D024_D020_Id left join D018 on D018_Id=D024_D018_Id WHERE D024_Flag_Cliente="S" and D024_Flag_Ativo="S" order by D024_Nome_Empresa','%1 - %9 - (%4%5) -  [%2/%3]'), 'length' => array(80,30)),
					'T182_D013_Id_Fornecedor' => array(
						'name' => 'T182_D013_Id_Fornecedor', 
						'label' => 'Contato', 
						'type' => 'select', 
						'selectRefresh' => array('D013','D013_Id','D013_Nome_Contato,D013_DDD_Telefone_1,D013_Telefone_1,D013_E_Mail',"where D013_Flag_Ativo = 'S' ORDER BY D013_Nome_Contato", true),
						'select' => gGeraSelect('D013','D013_Id','D013_Nome_Contato,D013_DDD_Telefone_1,D013_Telefone_1,D013_E_Mail',"$WHERE_D013_Fornecedor ORDER BY D013_Nome_Contato", true)
					),
					'formContatoGarantiaFornecedor' => array(
						'name' => 'FormContatoGarantiaFornecedor', 
						'type'=>'submit', 
						'reload' => false, 
						'value' => 'Incluir', 
						'align' => 'right', 
						'reject' => true),
					// 'T182_Tipo' => array('name' => 'T182_Tipo', 
					// 	'label'=>'Tipo',
					// 	'type' => 'select',
					// 	'select' =>	array(
					// 		//array('title' => 'Garantia', 'value' => 'G'),
					// 		//array('title' => 'Conserto', 'value' => 'C'),
					// 		array('title' => 'Avaria', 'value' => 'A'),
					// 		array('title' => 'Troca', 'value' => 'T'),
					// 		array('title' => 'Devolução', 'value' => 'DV'),
					// 		array('title' => 'Desistência', 'value' => 'DT'),
					// 		array('title' => 'Desconto', 'value' => 'DC'),
					// 	)
					// ),
					'T182_Preco' => array(
						'name' => 'T182_Preco',
						'label'=>'Preço', 
						'type' => 'moeda', 
						'length' => array(20,30)),
					'T182_Data' => array(
						'name' => 'T182_Data',
						'label'=>'Data abertura', 
						'type' => 'data', 
						'length' => array(20,30)),
					'T182_Hora' => array(
						'name' => 'T182_Hora',
						'label'=>'Hora', 
						'type' => 'tempo', 
						'length' => array(20,30)),
					'T182_Data_Conclusao' => array(
						'name' => 'T182_Data_Conclusao',
						'label'=>'Data conclusão', 
						'type' => 'data', 
						'length' => array(20,30)),
					'T182_Hora_Conclusao' => array(
						'name' => 'T182_Hora_Conclusao',
						'label'=>'Hora', 
						'type' => 'tempo', 
						'length' => array(20,30)),
					'T182_C007_Id' => array(
						'name' => 'T182_C007_Id', 
						'label'=>'Usuário',
						'disabled'=>'disabled', 
						'type' => 'select', 
						'select'=>	gGeraSelect('C007', 'C007_Id', 'C007_Login', 'order by C007_Login', true, $g['usuarioAtual']),
					),
					'T182_Observacao' => array(
						'name' => 'T182_Observacao',
						'label'=>'Observação', 
						'type' => 'textarea', 
						'length' => array(5,60)),
					'T182_D009_Id' => array(
						'name' => 'T182_D009_Id', 
						'label' => 'Produto', 
						'type' => 'hidden'),
					'T182_D162_Id' => array(
						'name' => 'T182_D162_Id',
						'label' => 'Status',
						'type' => 'select',
						'select' => gGeraSelect('D162', 'D162_Id', 'D162_Status', 'order by D162_Status', true),
					),
					'T182_D164_Id' => array(
						'name' => 'T182_D164_Id',
						'label' => 'Tipo',
						'type' => 'select',
						'select' => gGeraSelect('D164', 'D164_Id', 'D164_Tipo', 'order by D164_Tipo', true),
					),
					'T182_D018_Id' => array(
						'name' => 'T182_D018_Id',
						'label' => 'UF',
						'type' => 'select',
						'select' => gGeraSelect('D018', 'D018_Id', 'D018_UF', 'order by D018_Id', true),
					),
					'T182_Lote_Fabricacao' => array(
						'name' => 'T182_Lote_Fabricacao',
						'label'=>'Lote Fabricação', 
						'type' => 'text', 
						'length' => array(50,50)
					),
					'T182_Acao' => array(
						'name' => 'T182_Acao',
						'label'=>'Ação', 
						'type' => 'textarea', 
						'length' => array(5,60)
					),
					'T182_Pedido' => array(
						'name' => 'T182_Pedido',
						'label'=>'Pedido', 
						'type' => 'text', 
						'length' => array(20,30)
					),
					'T182_T007_Id' => array(
						'name' => 'T182_T007_Id',
						'label'=>'T007_Id', 
						'type' => 'hidden'
					),
					'T182_C004_Id' => array(
						'name' => 'T182_C004_Id',
						'label'=>'T182_C004_Id', 
						'type' => 'hidden',
						'value' => $g['empresaAtual']
					),
				)
			)
		);

		$form->definirFormularioArray($formArray);

		echo <<<EOT
		    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		    	$('#formContatoGarantiaCliente').unbind('click');
				$('#formContatoGarantiaCliente').unbind('click').bind('click', function(data) {
					var id = $('#{$g['divId']} #T182_D024_Id_Cliente').val();
					if (id>0){
						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Cliente/Fornecedor - ID: ' + id, '/cad/cad001/content/cad001contentCadastro/', '&acaoId=' + encodeURIComponent(id) + '&tabela=D024' + '&tab=contatos', [1000,553]);											
					}else{
						dialogConfirm('Erro', 'Selecione o Cliente!');
					}
					return false;
				});
		    	$('#formContatoGarantiaFornecedor').unbind('click');
				$('#formContatoGarantiaFornecedor').unbind('click').bind('click', function(data) {
					var id = $('#{$g['divId']} #T182_D024_Id_Fornecedor').val();
					if (id>0){
						abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Cliente/Fornecedor - ID: ' + id, '/cad/cad001/content/cad001contentCadastro/', '&acaoId=' + encodeURIComponent(id) + '&tabela=D024' + '&tab=contatos', [1000,553]);											
					}else{
						dialogConfirm('Erro', 'Selecione o Cliente!');
					}
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

					$T182_NF_Venda = $form->campoValorEnviado('T182_NF_Venda');
					$T182_D024_Id_Cliente  = $form->campoValorEnviado('T182_D024_Id_Cliente');

					$gravar = true;

					if(!empty($T182_NF_Venda) && !($T182_NF_Venda=="NAO TEM")){
						$D024 =mysql_query("Select D024_Nome_Empresa from  D024  where D024_Id={$T182_D024_Id_Cliente}");
						$D024_Nome_Empresa = mysql_fetch_array($D024);
						$T007 = mysql_query("Select T007_Id,D024_Nome_Empresa from T007 left join D024 on T007_D024_Id=D024_Id where T007_Numero_Nota_Fiscal = {$T182_NF_Venda} and T007_D024_Id={$T182_D024_Id_Cliente}");
						$nT007 = mysql_num_rows($T007);
						
						if($nT007<=0){
							$gravar = false;
							$alert = gGeraAlertMsg('Alerta', 'A nota Nº'.$T182_NF_Venda.' não está vinculada ao cliente '.$D024_Nome_Empresa[0]."!", false);
							echo <<<EOT
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
										{$alert}
								} </script></div>
EOT;
						}

						$mT007=mysql_fetch_assoc($T007);
						$form->campoSetarValor('T182_T007_Id', $mT007['T007_Id'], true);
						log("T007_Id = " . $mT007['T007_Id']);

					}

					// Update
					if($gravar){
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
					}
				} else {
					// Insert

					$T182_NF_Venda = $form->campoValorEnviado('T182_NF_Venda');
					$T182_D024_Id_Cliente  = $form->campoValorEnviado('T182_D024_Id_Cliente');

					$gravar = true;

					if(!empty($T182_NF_Venda) && !($T182_NF_Venda=="NAO TEM")){
						$D024 =mysql_query("Select D024_Nome_Empresa from  D024  where D024_Id={$T182_D024_Id_Cliente}");
						$D024_Nome_Empresa = mysql_fetch_array($D024);
						$T007 = mysql_query("Select T007_Id,D024_Nome_Empresa from T007 left join D024 on T007_D024_Id=D024_Id where T007_Numero_Nota_Fiscal = {$T182_NF_Venda} and T007_D024_Id={$T182_D024_Id_Cliente}");
						$nT007 = mysql_num_rows($T007);
						
						if($nT007<=0){
							$gravar = false;
							$alert = gGeraAlertMsg('Alerta', 'A nota Nº'.$T182_NF_Venda.' não está vinculada ao cliente '.$D024_Nome_Empresa[0]."!", false);
							echo <<<EOT
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
										{$alert}
								} </script></div>
EOT;
						}

						$mT007=mysql_fetch_assoc($T007);
						$form->campoSetarValor('T182_T007_Id', $mT007['T007_Id'], true);

					}					
					
					if($gravar){
						if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
							$acaoId = $g['mysqlLastId'];
						// Imprime uma mensagem e fecha a janela
						//$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}', unique(), '', 'Editar Produto - ID: ' + '{$acaoId}', '/est/est011/content/est011ContentIncluirProduto/', '&acaoId=' + encodeURIComponent('{$acaoId}'), [1000,610]);
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
	
	case 'est011FormGerarRemessa':
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
		
		$form_tabela = 'T183';
		$form_campo_pk = 'T183_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est011FormGerarRemessa_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est011/form/est011FormGerarRemessa/';

		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('est011FormGerarRemessa');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T183_D024_Id' => array('name' => 'T183_D024_Id', 'label'=>'Fornecedor', 'type' => 'pesquisa', 'pesquisa' => array('D024', 'D024_Id', 'D024_Nome_Empresa,D024_Nome_Fantasia', 'where D024_Flag_Fornecedor="S" order by D024_Nome_Empresa'), 'length' => array(40,40)),
                ),				
            ) 
		);
				
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
							$retorno = $EST011->gerarRemessa($g['mysqlLastId']);
							if ($retorno !== true) {
								echo gGeraAlertMsg('Erro', $retorno);
								echo <<<EOT
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								} </script></div>								
EOT;
							}else{							
								// Imprime uma mensagem e fecha a janela
								$alert .= gGeraAlertMsg('Operação OK', 'Remessa Gerada com Sucesso!', false);
								echo <<<EOT
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
										setTimeout(function() {
										{$alert}
										divRefresh('{$g['divIdParent']}', true);
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
		echo  $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('form.tpl');		
		
	break;	
	
	case 'est011FormCabecalhoRemessa':
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
		$form_tabela = 'T183';
		$form_campo_pk = 'T183_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est011FormCabecalhoRemessa_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est011/form/est011FormCabecalhoRemessa/';
		
		// novo formulário
		$form = new Formulario('est011FormIncluirProduto');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T183_Numero' => array('name' => 'T183_Numero','label'=>'Número', 'type' => 'text', 'readonly'=>'readonly', 'length' => array(20,30)),
					'T183_Data' => array('name' => 'T183_Data','label'=>'Data', 'type' => 'data', 'length' => array(20,30)),
					'T183_D024_Id' => array('name' => 'T183_D024_Id', 'label'=>'Fornecedor', 'type' => 'pesquisa', 'pesquisa' => array('D024', 'D024_Id', 'D024_Nome_Empresa,D024_Nome_Fantasia', 'where D024_Flag_Fornecedor="S" order by D024_Nome_Empresa'), 'length' => array(60,40)),
					
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

	case 'est011FormDocumentos':
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
		$form_tabela = 'T271';
		$form_campo_pk = 'T271_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = array('est011FormDocumentosBtnAdd', 'est011FormDocumentosBtnCln', 'est011FormDocumentosBtnNew');
        // Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est011/form/est011FormDocumentos/';
        
        //form editor
        $form_editor = array(
            'geral' => '{"T271_Descricao":{"top":7,"left":72.96875,"height":42,"width":342,"hidden":0},"est011FormDocumentosBtnNew":{"top":51,"left":1040,"width":null,"hidden":1},"uploadArquivo":{"top":76,"left":27.109375,"width":208,"hidden":0},"est011FormDocumentosBtnAdd":{"top":74,"left":416,"width":null,"hidden":0}}',
        );        
        
		// Array de definição do formulário
		$r_T182_Id = isset($r_est011FormDocumentos) ? $r_est011FormDocumentos['T271_T182_Id'] : $r_T182_Id;
		                                                           
		$tmp = gGeraSelect('T182', 'T182_Id', "WHERE T182_Id = '{$r_T182_Id}'");
		foreach($tmp as &$value) { $value['selected'] = true; }
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('est011FormDocumentos');
		$formArray = array(
			'campos' => array(
				'geral' => array(
				)
			)
		);
		//$formArray['campos']['geral']['__T271_T182_Id'] 		= array('name' => '__T271_T182_Id', 'label'=>'Número Pedido', 'type' => 'select', 'extra'=>'style="width:382px;"', 'disabled' => true, 'reject'=> true, 'select' => 
		//	$tmp
		//);
		$formArray['campos']['geral']['T271_Descricao'] 	= array('name' => 'T271_Descricao', 'label'=>'Descrição', 'type' => 'textarea','length' => array(3,60));
		$formArray['campos']['geral']['uploadArquivo'] 			= array('name' => 'uploadArquivo', 'label' => 'Arquivo documento', 'type' => 'file', 'length' => array(24, 24), 'reject' => true, 'uppercase' => false);
		$formArray['campos']['geral']['T271_Nome_Arquivo'] 		= array('name' => 'T271_Nome_Arquivo', 'type' => 'hidden', 'uppercase' => false);
		if (!empty($r_acaoId)) {
			 $formArray['campos']['geral']['est011FormDocumentosBtnNew'] = array('name' => 'est011FormDocumentosBtnNew', 'type' => 'button', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		}
		$formArray['campos']['geral']['T271_T182_Id'] 			= array('name' => 'T271_T182_Id', 'type' => 'hidden', 'value' => $r_T182_Id);
		$formArray['campos']['geral']['T271_C007_Id'] 			= array('name' => 'T271_C007_Id', 'type' => 'hidden', 'value' => $g['usuarioAtual']);
		$formArray['campos']['geral']['T271_Data_Hora_Upload'] 			= array('name' => 'T271_Data_Hora_Upload', 'type' => 'hidden', 'value' => date('Y-m-d H:i:s'));
		//$formArray['campos']['geral']['est011FormDocumentosBtnCln'] = array('name' => 'est011FormDocumentosBtnCln', 'type' => 'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['geral']['est011FormDocumentosBtnAdd'] = array('name' => 'est011FormDocumentosBtnAdd', 'type' => 'submit', 'reload' => false, 'value' => 'Incluir' , 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
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
						$form->campoSetarValor('T271_Nome_Arquivo', $arquivo['name'], true);
						if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
							// Gravar o arquivo no disco
							$pathinfo = pathinfo($arquivo['name']);
							$pathinfo['extension'] = empty($pathinfo['extension']) ? '.tmp' : $pathinfo['extension'];
							//$dir = "{$g['pathDados']}documentos/T182_".str_repeat('0',5-strlen($form->campoValorEnviado('T271_T182_Id'))).$form->campoValorEnviado('T271_T182_Id');
							$dir = "{$g['pathDados']}RMA";
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
}
?>