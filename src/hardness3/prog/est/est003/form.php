<?php
namespace hardness;
require_once('prog/est/est003/form_func-js.php');

/**
 * Weiberlan
 * 12/11/2011
 */

/**
 * Ações
 */
switch ($g['r'][3]) {
	
	case 'est003FormIncluirDE':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		$sql= <<<EOT
			SELECT * FROM T006 
			left join D009 on D009_Id=T006_D009_Id
            left join D049 on D049_Id=D009_D049_Id
            left join D082 on D082_Id=D049_D082_Id
            left join D001 on D049_D001_Id=D001_Id
            left join T005 on T006_T005_Id=T005_Id
            left join D024 on D024_Id=T005_D024_Id
            left join D006 on D006_Id=T006_D006_Id
            left join D037 on D037_Id=D001_D037_Id
            left join T001 on T001_T006_Id=T006_Id 
            WHERE T006_Id={$r_T006_Id}
EOT;
		$resultado = mysql_query($sql);
		$row = mysql_fetch_array($resultado);

		$sql = <<<EOT
SELECT 
	T066_Id, D004_Local
FROM T066 left join D009 on T066_D009_Id=D009_Id left join D048 on D048_Id=T066_D048_Id left join D004 on D004_Id=T066_D004_Id WHERE ((1=1)) AND D009_D049_Id='10349' and D009_C004_Id=1 LIMIT 0,100
EOT;
				
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = isset($r_acaoId) ? $r_acaoId : null;
		
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
		$form_tabela = 'T001';
		$form_campo_pk = 'T001_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est003FormIncluirDE_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est003/form/est003FormIncluirDE/';
		$form_editor = array(
			'geral' => '{"Nome_Cliente":{"top":7,"left":74,"width":392,"hidden":0},"Produto":{"top":30,"left":69,"width":392,"hidden":0},"T001_Data_Lancamento":{"top":53,"left":15,"width":72,"hidden":0},"T001_Descricao_Operacao":{"top":76,"left":50,"width":392,"hidden":0},"T001_Quantidade":{"top":99,"left":44,"width":72,"hidden":0},"T001_Flag_Operacao":{"top":122,"left":51,"width":112,"hidden":0},"T001_T066_Id":{"top":145,"left":57,"width":207,"hidden":0}}'
		);
				
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('est003FormIncluirDE');
		$formArray = array(
			'campos' => array(
				'geral' => array(
				//CÓDIGO CRIADO PARA DEMONSTRAÇÃO, NÃO FUNCIONA
					//'T001_D024_Id' => array('name' => 'T001_D024_Id', 'label'=>'Fornecedor', 'type' => 'pesquisa','length' => array(40,60),
				    //	'pesquisa' => array('D024','D024_Id','D024_Nome_Empresa,D024_Id',""),
					//),
					'T001_D024_Id_Cliente' => array('name' => 'T001_D024_Id_Cliente', 'label'=>'Cliente', 'value' => $row['D024_Id'], 'readonly' => true, 'type' => 'hidden', 'length' => array(40,60)),
					'Nome_Cliente' => array('name' => 'Nome_Cliente', 'label'=>'Cliente', 'value' => $row['D024_Nome_Empresa'], 'readonly' => true, 'reject' => true, 'type' => 'text', 'length' => array(40,60)),
					'Produto' => array('name' => 'Produto', 'label'=>'Produto', 'value' => $row['D001_Descricao_Produto'], 'readonly' => true, 'reject' => true, 'type' => 'text','length' => array(40,60)),
					'T001_Data_Lancamento' => array('name' => 'T001_Data_Lancamento', 'label'=>'Data Lançamento','value' => date('d/m/Y'), 'type' => 'data','length' => array(10,10)),
					'T001_Descricao_Operacao' => array('name' => 'T001_Descricao_Operacao', 'label'=>'Descrição','value' => "Divergencia Ped.{$row['T005_Id']}", 'type' => 'text','length' => array(40,60)),
					//'T001_Valor_Custo_Unitario' => array('name' => 'T001_Valor_Custo_Unitario', 'label'=>'Custo', 'type' => 'moeda','length' => array(10,10)),
					//'T001_Valor_Custo_Medio_Unitario' => array('name' => 'T001_Valor_Custo_Medio_Unitario', 'label'=>'Medio', 'type' => 'moeda','length' => array(10,10)),
					//'T001_Valor_Preco_Unitario' => array('name' => 'T001_Valor_Preco_Unitario', 'label'=>'Preço', 'type' => 'moeda','length' => array(10,10)),
					'T001_Quantidade' => array('name' => 'T001_Quantidade', 'label'=>'Quantidade', 'type' => 'moeda','length' => array(10,10)),
					'T001_Flag_Operacao' => array('name' => 'T001_Flag_Operacao', 'label'=>'Operação', 'type' => 'select',
						'select' => array(
							array('title' => '...', 'value' => '','selected' => true),
							array('title' => 'ENTRADA', 'value' => 'E'),
							array('title' => 'SAÍDA', 'value' => 'S'),
							array('title' => 'BALANÇO', 'value' => 'B'),
							//array('title' => 'CANCELADO', 'value' => 'C'),
						)
					),
					'T001_Flag_MMF' => array('name' => 'T001_Flag_MMF', 'label'=>'MMF', 'type' => 'hidden', 'value' => 'N'),
					'T001_Flag_Atualiza_Custo' => array('name' => 'T001_Flag_Atualiza_Custo', 'label'=>'Atualiza custo', 'type' => 'hidden', 'value' => 'N'),
					'T001_D009_Id' => array('name' => 'T001_D009_Id', 'label'=>'IdProduto','value' => $row['D009_Id'], 'type' => 'hidden'),
					'T001_T006_Id' => array('name' => 'T001_T006_Id', 'label'=>'IdItemPedido','value' => $r_T006_Id, 'type' => 'hidden'),
					'T001_T066_Id' => array('name' => 'T001_T066_Id', 'label' => 'Locação', 'type' => 'select','select' =>
						gGeraSelect('T066', 'T066_Id', 'D004_Local', "left join D004 ON D004_Id=T066_D004_Id WHERE T066_D009_Id='{$row['D009_Id']}' ORDER BY D004_Local ASC", true)
					),
                ),
				
            ) // 'campos'
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
							// Imprime uma mensagem e fecha a janela
							require_once("bibliotecas/classes/CAD002.php");
							$CAD002 = new CAD002();
							$CAD002->D001_reprocessa_historico(0,$form->campoValorEnviado('T001_D009_Id'));
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
		echo  $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');		
		
		/**
		* Botões estão no content
		*/
		
	break;

	case 'est003FormImportarCSV':

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
		$form_tabela = 'T066';
		$form_campo_pk = 'T066';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est003FormImportarCSV_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est003/form/est003FormImportarCSV/';
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('est003FormImportarCSV');
		$formArray = array(
			'campos' => array(
				'geral' => array(
				)
			)
		);
		$formArray['campos']['geral']['uploadArquivoCsvEst003'] 		= array('name' => 'uploadArquivoCsvEst0003', 'label' => 'Selecione o CSV', 'type' => 'file', 'length' => array(24, 24), 'reject' => true);
		$formArray['campos']['geral']['est003FormImportarCSV_btn'] 		= array('name' => 'est003FormImportarCSV_btn', 'type'=>'submit', 'reload' => false, 'value' => 'Enviar', 'align' => 'right', 'style' => 'float:right;', 'extra' => 'style="margin-right:10px;margin-top:0px;padding-left:10px;padding-right:10px;padding-top:0px;height:20px;"', 'reject'=>true);
		$formArray['campos']['geral']['Est003Arquivo_Csv'] 				= array('name' => 'Est003_Arquivo_Csv', 'type' => 'hidden');
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
				$arquivo = $form->campoValorEnviado('uploadArquivoCsvEst003');
				require_once('bibliotecas/classes/EST003.php');
				$EST003 = new EST003();
				$retorno = $EST003->importarCSVBalanco($arquivo);
				
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
		
	
	case 'est003FormProdutosDefeito':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/	
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = isset($r_acaoId) ? $r_acaoId : null;
		
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
		$form_tabela = 'T206';
		$form_campo_pk = 'T206_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est003FormProdutosDefeito_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est003/form/est003FormProdutosDefeito/';

		//form editor
		$form_editor = array('geral' => '{"Procura_Produto":{"top":6.9393310546875,"left":35.76898193359375,"width":330.039216,"hidden":0},"T206_Quantidade":{"top":29.963226318359375,"left":18.45892333984375,"width":121.03921600000001,"hidden":0},"T206_T066_Id":{"top":29.963226318359375,"left":224.67828369140625,"width":159,"hidden":0},"T206_Data":{"top":52.95648193359375,"left":51.82293701171875,"width":120.03921600000001,"hidden":0},"T206_D169_Id":{"top":75.98037719726562,"left":52.92584228515625,"width":127,"hidden":0},"T206_Descricao_Defeito":{"top":98.98898315429688,"left":32.15380859375,"height":131.039216,"width":344.039216,"hidden":0},"T206_C007_Id":{"top":236.9945068359375,"left":37.1016845703125,"width":351,"hidden":0},"est003FormProdutosDefeito_btn":{"top":280.9742431640625,"left":367.9993896484375,"width":null,"hidden":0}}');
		
		$pesquisaGridExtra = <<<EOT
		left join D049 on D049_Id=D009_D049_Id 
		left join D001 on D001_Id=D049_D001_Id 
		left join D082 on D082_Id=D049_D082_Id 
		WHERE D009_C004_Id = {$g['empresaAtual']}
		AND D049_Flag_Ativo != 'N'
		ORDER BY D001_Codigo_Produto
EOT;
		$pesquisaGrid = array(
			'D009',																														// tabela
			'D009_Id',																													// chave primaria
			'D009_Id,D001_Codigo_Produto,D001_Descricao_Produto,D082_Marca,D009_Quantidade_Estoque,D009_Quantidade_Estoque_Liquido',	// campos
			$pesquisaGridExtra, 																										// extras sql
			'Pesquisa de Produtos',																										// título da janela
			'D009_Id,Código,Produto,Marca,Estoque,Estoque Liquido',																		// descrição dos campos
			'pesquisaProdutoDefeito',																									// callback js
			$g['divId'],																												// divId
			0,																															// parametro extra
			array(
				array('colunaExibir' => false),
				array(),
				array('style' => 'font-weight:bold'),
				array(),			
                array(),
                array(),
			)																															// parametros para cada campo do grid
		);		

		if($acaoId > 0){
			$T206 = mysql_query("SELECT D001_Codigo_Produto,
										D001_Descricao_Produto,
										D009_Id,
										T206_T066_Id	
								   FROM T206
							  LEFT JOIN D009 ON D009_Id = T206_D009_Id
							  LEFT JOIN D049 ON D049_Id = D009_D049_Id
							  LEFT JOIN D001 ON D001_Id = D049_D001_Id 
							      WHERE T206_Id = '{$acaoId}'");
			$mT206 = mysql_fetch_array($T206);

			$item    	  = $mT206['D001_Codigo_Produto'] . ' - ' . $mT206['D001_Descricao_Produto'];
			$D009_Id 	  = $mT206['D009_Id'];
			$T066_Id      = $mT206['T206_T066_Id'];
			$arrayLocacao = gGeraSelect('T066', 'T066_Id', 'D004_Local', 'LEFT JOIN D004 ON D004_Id = T066_D004_Id WHERE T066_D009_Id =' .$D009_Id . ' order by D004_Local', false, $T066_Id);
		}
		
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('est003FormProdutosDefeito');
		$formArray = array(
			'campos' => array(
				'geral' => array(
				//CÓDIGO CRIADO PARA DEMONSTRAÇÃO, NÃO FUNCIONA
					'Procura_Produto' => array('name' => 'Procura_Produto' , 'label' => 'Produto', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGrid, 'length' => array(45,90), 'reject' => true, 'value' => $item),
                	'T206_D009_Id' => array('name' => 'T206_D009_Id', 'type' => 'hidden'),
                	'T206_D169_Id' => array('name' => 'T206_D169_Id', 'label' => 'Tipo', 'type' => 'select', 'select' =>
                        gGeraSelect('D169', 'D169_Id', 'D169_Id,D169_Descricao_Tipo_Produto', 'order by D169_Id', true)
                    ),
					'T206_Quantidade' => array('name' => 'T206_Quantidade', 'type' => 'text', 'label' => 'Quantidade'),
					'T206_Data' => array('name' => 'T206_Data', 'type' => 'data', 'label' => 'Data'),					
					'T206_Descricao_Defeito' => array('name' => 'T206_Descricao_Defeito', 'type' => 'textarea','label' => 'Defeito', 'length' => array(4,50)),	
					'T206_C007_Id' => array('name' => 'T206_C007_Id', 'label' => 'Usuário', 'readonly' => true ,'type' => 'select', 'select' =>
						gGeraSelect('C007', 'C007_Id', 'C007_Nome', 'order by C007_Nome', false, $g['usuarioAtual']),
					),	
					'T206_T066_Id' => array('name' => 'T206_T066_Id' , 'label' => 'Locação', 'type' => 'select', 'select' =>
						$arrayLocacao,
					),
				),				
            ) // 'campos'
		);
				
		$formArray['campos']['geral']['est003FormProdutosDefeito_btn'] = array('name' => 'est003FormProdutosDefeito_btn', 'type'=>'submit', 'reload' => false, 'value' => !empty($acaoId) ? 'Alterar' : 'Inserir', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);		
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
							// Imprime uma mensagem e fecha a janela
							$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
							$D009_Id = $form -> campoValorEnviado('T206_D009_Id');
							require_once("bibliotecas/classes/CAD002.php");
							$CAD002 = new CAD002();
							$CAD002->D001_reprocessa_historico(false, $D009_Id, 30, true);
							
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
		echo  $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');		
		
		/**
		* Botões estão no content
		*/
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				//pesquisaProdutoDefeito('');
			} </script></div>
EOT;
	break;

	case 'est003FormGerarPedido':
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
		$idCliente='99999999999';
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
		$enviar_btnId = 'est003FormGerarPedidoBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est003/form/est003FormGerarPedido/';
		
		$form_editor = array(
			'geral'=>'{"T005_D024_Id":{"top":7,"left":87.640625,"width":265,"hidden":0},"T005_C007_Id_Vendedor_Interno":{"top":30,"left":74.78125,"width":288,"hidden":0},"T005_D013_Id":{"top":53,"left":76.015625,"width":239,"hidden":0},"T005_Data_Emissao":{"top":76,"left":79.703125,"width":280,"hidden":0},"T005_Data_Entrega":{"top":99,"left":76.015625,"width":280,"hidden":0},"T005_D036_Id":{"top":99,"left":980.890625,"width":128,"hidden":1},"T005_Flag_Destino_Produto":{"top":122,"left":77.25,"width":288,"hidden":0},"T005_Flag_Lista_Preco":{"top":122,"left":1426.859375,"width":240,"hidden":1},"T005_Flag_ACP":{"top":145,"left":41.578125,"width":288,"hidden":0},"T005_Flag_Frete":{"top":168,"left":96.203125,"width":288,"hidden":0},"T005_D022_Id":{"top":191,"left":39.75,"width":267,"hidden":0},"T005_T208_Id":{"top":306,"left":1459.640625,"width":0,"hidden":1}}'
		);

		$vendedorInternoRead = true;
		$vendedorInternoExtra = "onfocus='this.initialSelect = this.selectedIndex;' onchange='this.selectedIndex = this.initialSelect;'";
		if($acaoId > 0){		
			$sqlC007 = mysql_query("SELECT C007_Flag_Ausente FROM T005 LEFT JOIN D024 ON D024_Id=T005_D024_Id LEFT JOIN C007 ON C007_Id=D024_C007_Vendedor_Interno WHERE T005_Id = '{$acaoId}'");
			$rowC007 = mysql_fetch_array($sqlC007);
			if(($rowC007['C007_Flag_Ausente'] == 'S' && $g['C031']['alterarVendedorAusente'] == 'S') || ($g['nivelAlcada']==4) || $g['C031']['alterarVendedor'] == 'S'){
				$vendedorInternoRead = false;
				$vendedorInternoExtra = "";
			}
		}


		// novo formulário
		$form = new Formulario('est003FormGerarPedido');

		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T005_Id' => array('name' => 'T005_Id', 'type' => 'hidden'),
					'T005_D024_Id' => array('name' => 'T005_D024_Id','label'=>'Cliente', 'type' => 'pesquisa','extra' => "onchange=\"carregaCFOP_Contatos(this, '{$g['divId']}', '', '{$g['C031']['alterarVendedorAusente']}', '{$g['C031']['alterarVendedor']}');\"", 'pesquisa' => array('D024','D024_Id','D024_Id,D024_Nome_Empresa,D024_Nome_Fantasia,D020_Nome_Cidade,D018_UF,D024_Cpf,D024_Cnpj','left join D020 on D020_Id=D024_D020_Id left join D018 on D018_Id=D024_D018_Id WHERE D024_Flag_Cliente="S" and D024_Flag_Ativo="S" order by D024_Nome_Empresa','(%1) %2 - %3 [%4 - %5]'), 'length' => array(50,30)),
					'T005_D013_Id' => array('name' => 'T005_D013_Id', 'label' => 'Contato', 'type' => 'select',
						'select' => gGeraSelect('D013','D013_Id','D013_Nome_Contato',"where D013_D024_Id=$idCliente AND D013_Flag_Ativo = 'S'",true),
						'selectRefresh' => array('D013','D013_Id','D013_Nome_Contato,D013_DDD_Telefone_1,D013_Telefone_1',"where D013_D024_Id=$idCliente AND D013_Flag_Ativo = 'S'",true),
					),
					'T005_C007_Id_Vendedor_Interno' => array('name' => 'T005_C007_Id_Vendedor_Interno', 'label'=>'Vendedor','type' => 'select', 'extra' => $vendedorInternoExtra, 'readonly' => $vendedorInternoRead, 'select' => 
							gGeraSelect('C007','C007_Id','IF(C007_Ativo != "N"[[virgula]] C007_Primeiro_Nome[[virgula]] CONCAT(C007_Primeiro_Nome[[virgula]]" (INATIVO)")) as C007_Primeiro_Nome','WHERE C007_Flag_Vendedor!="" AND (C007_Ativo != "N" OR C007_Id = "'.$idVendedorInterno.'") ORDER BY C007_Primeiro_Nome',true),
					),	
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
					'T005_Data_Emissao' => array('name' => 'T005_Data_Emissao','label'=>'Emissão','type' => 'data', 'length' => array(20,10), 'value'=>date("d/m/Y")),
					'T005_Data_Entrega' => array('name' => 'T005_Data_Entrega', 'label'=>'Entrega','type' => 'data'),
					'T005_Flag_Lista_Preco' => 	array('name' => 'T005_Flag_Lista_Preco', 'label' => 'Lista Preço', 'type' => 'select', 'select' => 
						array(
							array('title' => 'Preço 1', 'value' => '1','selected' => true),   
							array('title' => 'Preço 2', 'value' => '2'),   
							array('title' => 'Preço 3', 'value' => '3'),
							array('title' => 'Preço 4', 'value' => '4'),
							array('title' => 'Preço 5', 'value' => '5'),
						)
					),
					'T005_Flag_Frete' => array('name' => 'T005_Flag_Frete','label'=>'Frete', 'type' => 'select',
						'select' => array(
							array('title' => '0-EMITENTE', 'value' => '0'),
							array('title' => '1-DESTINATÁRIO', 'value' => '1'),
							array('title' => '2-TERCEIROS', 'value' => '2'),
							array('title' => '9-SEM FRETE', 'value' => '9'),
						),
					),
					'T005_D022_Id' => array('name' => 'T005_D022_Id', 'label' => 'Transportadora', 'type' => 'pesquisa', 'pesquisa' => array('D022','D022_Id','D022_Nome_Empresa,D020_Nome_Cidade,D018_UF','left join D020 on D020_Id=D022_D020_Id left join D018 on D018_Id=D022_D018_Id'),'extra' => "onchange=\"carregaTipoTransp(this, '{$g['divId']}')\"", 'length' => array(50,90)),
					'T005_T208_Id' => array('name' => 'T005_T208_Id', 'label' => 'Rota', 'type' => 'pesquisa', 'pesquisa' => array('T208','T208_Id','T208_Rota')),
					'T005_D036_Id' => array('name' => 'T005_D036_Id', 'label' => 'Tipo', 'type' => 'select', 'select' =>
							gGeraSelect('D036','D036_Id','D036_Tipo_Transporte','',true),
					),
					'T005_C004_Id' => array('name' => 'T005_C004_Id', 'label'=>'Transporte','type' => 'hidden', 'value' => $g['empresaAtual']),
					'T005_Flag_Destino_Produto' => array('name' => 'T005_Flag_Destino_Produto','label'=>'Destino', 'type' => 'select', 
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
					if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {						
						echo gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!');			
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								divRefresh('{$r_divIdMain}', true);
							} </script></div>
EOT;
					}
				} else {
					// Insert
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						$T005_Id = $g['mysqlLastId'];
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								divRefresh('{$g['divIdParent']}');
								$.getJSON('/est/est003/form_func-ajax/gerarPedidoProdutosDefeitos/?ajax=true&T005_Id=' + encodeURIComponent("$T005_Id") +       '&callback=?', function(request) {
								if (request.code) {
										abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' ,unique(), '', 'Editar Pedido - ID: ' + {$T005_Id}, '/ven/ven002/content/ven002content02/', '&acaoId=' + encodeURIComponent('{$T005_Id}') + '&tabela=T005', [1000,615]);
								} else {
									dialogConfirm('Erro', request.data);
								}
							});
								
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
		$botoesDir .= '<button type="submit" id="est003FormGerarPedidoBtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
    break;

}
?>