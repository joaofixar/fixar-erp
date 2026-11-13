<?php
namespace hardness;
	// Classes
	require_once("bibliotecas/classes/FIN007.php");
	$FIN007 = new FIN007();
	
	/**
	* @author AndreySliwinski
	* @since 21/011/2011
	*/
	
	switch ($g['r'][3]) {
	
		case 'fin008FormFluxoCaixa':
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
			$form_tabela = '';
			$form_campo_pk = '';
			// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
			$enviar_btnId = 'fin007FormFluxoCaixaBtn';
			// Form que será executado ao clicar no botão
			$enviar_acaoHref = '/fin/fin008/form/fin008FormFluxoCaixa/';

			// editor
			$form_editor = array(
				'geral' => '{"DataInicio":{"top":7,"left":55,"width":78,"hidden":0},"DataFim":{"top":30,"left":59,"width":78,"hidden":0},"Empresa":{"top":53,"left":64,"width":82,"hidden":0},"Saldo_Bancario":{"top":76,"left":4,"width":82,"hidden":0},"Titulos_Cobranca":{"top":99,"left":-2,"width":82,"hidden":0}}'
			);
					
			// novo formulário
			$form = new Formulario('form_fin007_form5');

			// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
			$formArray = array(
				'campos' => array(
					'geral' => array(
						'DataInicio' => array('name' => 'Data_Inicio', 'label'=>'Data Inicial', 'type' => 'data', 'reject'=>true),	
						'DataFim' => array('name' => 'Data_Fim', 'label'=>'Data Final', 'type' => 'data', 'reject'=>true),	
						'Empresa' => array('name' => 'Empresa', 'label' => 'Empresa', 'type' => 'select', 'style'=>'width:82px;', 'select' => 
							array(
								array('title' => '...', 'value' => ''),
								array('title' => 'Atual', 'value' => '1'),
								array('title' => 'Todas', 'value' => '2'), 
							)
						),
						'Saldo_Bancario' => array('name' => 'Saldo_Bancario', 'label' => 'Incluir saldo bancário', 'type' => 'select', 'style'=>'width:82px;', 'select' => 
							array(
								array('title' => '...', 'value' => ''),
								array('title' => 'Sim', 'value' => 'S','selected'=>'selected'),
								array('title' => 'Não', 'value' => 'N'), 
							)
						),
						'Titulos_Cobranca' => array('name' => 'Titulos_Cobranca', 'label' => 'Incluir títulos cobrança', 'type' => 'select', 'style'=>'width:82px;', 'select' => 
							array(
								array('title' => '...', 'value' => ''),
								array('title' => 'Sim', 'value' => 'S'),
								array('title' => 'Não', 'value' => 'N', 'selected'=>'selected'), 
							)
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
					$Data_Inicio = $form->campoValorEnviado('DataInicio');
					$Data_Fim = $form->campoValorEnviado('DataFim');
					$Empresa = $form->campoValorEnviado('Empresa');
					$Saldo_Bancario = $form->campoValorEnviado('Saldo_Bancario');
					$Titulos_Cobranca = $form->campoValorEnviado('Titulos_Cobranca');
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							nova_janela('/hardness3/hardness/relatorios/fluxo_caixa.php?Data_Inicio={$Data_Inicio}&Data_Fim={$Data_Fim}&Empresa={$Empresa}&Saldo_Bancario={$Saldo_Bancario}&Titulos_Cobranca={$Titulos_Cobranca}',0,0,950,750,'no','no','no','no','yes','yes', 'Fluxo de Caixa');
							//$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
						} </script></div>
EOT;
				}
			}
			
			/**
			* PARTE 4: Gerar o formulário
			*/
			//$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk, "Confirma a não liberação?");
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
			
			/**
			* Botões
			*/
			// Definição dos botões
			$botoesEsq = '';
			$botoesDir = '';
			$botoesDir .= '<button type="submit" id="fin007FormFluxoCaixaBtn">Imprimir</button>';
			$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
			$g['smarty']->assign('botoesId', $g['divId']);
			$g['smarty']->assign('botoesEsq', $botoesEsq);
			$g['smarty']->assign('botoesDir', $botoesDir);
			$g['smarty']->display('buttons-floatBottom.tpl');
			
			
		break;
	
		case 'fin008FormBalancete':
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
			$form_tabela = '';
			$form_campo_pk = '';
			// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
			$enviar_btnId = 'fin008FormBalanceteBtn';
			// Form que será executado ao clicar no botão
			$enviar_acaoHref = '/fin/fin008/form/fin008FormBalancete/';

			// editor
			$form_editor = array(
				'geral' => '{"DataInicio":{"top":7,"left":5,"width":72,"hidden":0},"DataFim":{"top":30,"left":9,"width":72,"hidden":0},"Empresa":{"top":53,"left":14,"width":240,"hidden":0},"Tipo":{"top":76,"left":37,"width":240,"hidden":0},"Contas":{"top":99,"left":23,"width":240,"hidden":0}}'
			);
					
			// novo formulário
			$form = new Formulario('fin008FormBalancete');

			// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
			$formArray = array(
				'campos' => array(
					'geral' => array(
						'DataInicio' => array('name' => 'Data_Inicio', 'label'=>'Data Inicial', 'type' => 'data', 'reject'=>true),	
						'DataFim' => array('name' => 'Data_Fim', 'label'=>'Data Final', 'type' => 'data', 'reject'=>true),							
						'Empresa' => array('name' => 'Empresa', 'label' => 'Empresa', 'type' => 'select', 'select' => 
							array(
								array('title' => 'Atual', 'value' => '1'),
								array('title' => 'Todas', 'value' => '2'), 
							)
						),
						'Tipo' => array('name' => 'Tipo', 'label' => 'Tipo', 'type' => 'select', 'select' => 
							array(
								array('title' => 'Realizado', 'value' => '1'),
								array('title' => 'A Realizar', 'value' => '2'), 
							)
						),
						'Contas' => array('name' => 'Contas', 'label' => 'Contas', 'type' => 'select', 'select' => 
							array(
								array('title' => 'Todas', 'value' => '2'), 
								array('title' => 'Apenas com Movimentação', 'value' => '1'),
							)
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
					$Data_Inicial = $form->campoValorEnviado('DataInicio');
					$Data_Final = $form->campoValorEnviado('DataFim');
					$Empresa = $form->campoValorEnviado('Empresa');
					$Tipo = $form->campoValorEnviado('Tipo');
					$Contas = $form->campoValorEnviado('Contas');
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							nova_janela('/rel/relatorios/balancete/rel/?ajax=true&Data_Inicial={$Data_Inicial}&Data_Final={$Data_Final}&Empresa={$Empresa}&Tipo={$Tipo}&Contas={$Contas}',0,0,850,600,'no','no','no','no','yes','yes', 'Balancete');
							//nova_janela('/rel/relatorios/balancete1/rel/?ajax=true&Data_Inicial={$Data_Inicial}&Data_Final={$Data_Final}&Empresa={$Empresa}&Tipo={$Tipo}&Contas={$Contas}',0,0,850,600,'no','no','no','no','yes','yes',0);
							//nova_janela('../../hardness/empresas/E003/relatorios/D014_balancete.php3?Data_Inicial={$Data_Inicial}&Data_Final={$Data_Final}&Empresa={$Empresa}&Tipo={$Tipo}&Contas={$Contas}',0,0,900,750,'no','no','no','no','yes','yes',0);
							//$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
						} </script></div>
EOT;
				}
			}
			
			/**
			* PARTE 4: Gerar o formulário
			*/
			//$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk, "Confirma a não liberação?");
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
			
			/**
			* Botões
			*/
			// Definição dos botões
			$botoesEsq = '';
			$botoesDir = '';
			$botoesDir .= '<button type="submit" id="fin008FormBalanceteBtn">Imprimir</button>';
			$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
			$g['smarty']->assign('botoesId', $g['divId']);
			$g['smarty']->assign('botoesEsq', $botoesEsq);
			$g['smarty']->assign('botoesDir', $botoesDir);
			$g['smarty']->display('buttons-floatBottom.tpl');
		
		break;

		case 'fin008FormComissoes':
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
			$form_tabela = '';
			$form_campo_pk = '';
			// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
			$enviar_btnId = 'fin008FormComissoesBtn';
			// Form que será executado ao clicar no botão
			$enviar_acaoHref = '/fin/fin008/form/fin008FormComissoes/';

			// editor
			$form_editor = array(
				'geral' => '{"DataInicio":{"top":4,"left":20.296875,"width":78,"hidden":0},"T007_Flag_ACP":{"top":76,"left":30.296875,"width":132,"hidden":0},"DataFim":{"top":26,"left":25.640625,"width":78,"hidden":0},"Representante":{"top":53,"left":3.75,"width":220,"hidden":0}}'
			);
					
			// novo formulário
			$form = new Formulario('fin008FormComissoes');

			// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
			$formArray = array(
				'campos' => array(
					'geral' => array(
						'DataInicio' => array('name' => 'Data_Inicio', 'label'=>'Data Inicial', 'type' => 'data', 'reject'=>true),	
						'DataFim' => array('name' => 'Data_Fim', 'label'=>'Data Final', 'type' => 'data', 'reject'=>true),							
						'Representante' => array('name' => 'Representante', 'label' => 'Representante', 'type' => 'select', 'style'=>'width:220px;', 
							'select' => gGeraSelect('D021', 'D021_Id', 'D021_Nome_Empresa', 'GROUP BY D021_Id ORDER BY D021_Nome_Empresa', true)
						),												
						'T007_Flag_ACP' => array('name' => 'T007_Flag_ACP', 'label'=>'Modelo','type' => 'select', 'extra'=>'style="width:132px;"','select' => 
							array(
								array('title' => 'Todos', 'value' => '0', 'selected' => true),
								//array('title' => 'NF', 'value' => 1),
								//array('title' => 'Romaneio', 'value' => 2),
								array('title' => 'ACP', 'value' => 3),
								//array('title' => 'Cupom Fiscal', 'value' => 4),
								//array('title' => 'NF D1', 'value' => 5),
								array('title' => 'NFe', 'value' => 6),
								//array('title' => 'NFSe', 'value' => 7),							
							)
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
					$Data_Inicial = $form->campoValorEnviado('DataInicio');
					$Data_Final = $form->campoValorEnviado('DataFim');
					$Representante = $form->campoValorEnviado('Representante');
					$T007_Flag_ACP = $form->campoValorEnviado('T007_Flag_ACP');
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							nova_janela('/rel/relatorios/relatorio_comissao_representante/rel/?ajax=true&Data_Inicial={$Data_Inicial}&Data_Final={$Data_Final}&Representante={$Representante}&T007_Flag_ACP={$T007_Flag_ACP}',0,0,900,750,'no','no','no','no','yes','yes',0);
							//$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
						} </script></div>
EOT;
				}
			}
			
			/**
			* PARTE 4: Gerar o formulário
			*/
			//$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk, "Confirma a não liberação?");
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
			
			/**
			* Botões
			*/
			// Definição dos botões
			$botoesEsq = '';
			$botoesDir = '';
			$botoesDir .= '<button type="submit" id="fin008FormComissoesBtn">Imprimir</button>';
			$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
			$g['smarty']->assign('botoesId', $g['divId']);
			$g['smarty']->assign('botoesEsq', $botoesEsq);
			$g['smarty']->assign('botoesDir', $botoesDir);
			$g['smarty']->display('buttons-floatBottom.tpl');
		
		break;

		case 'fin008FormMapaVendas':
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
			$form_tabela = '';
			$form_campo_pk = '';
			// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
			$enviar_btnId = 'fin008FormMapaVendasBtn';
			// Form que será executado ao clicar no botão
			$enviar_acaoHref = '/fin/fin008/form/fin008FormMapaVendas/';

			// editor
			$form_editor = array(
				'geral' => '{"DataInicio":{"top":4,"left":4,"width":78},"DataFim":{"top":26,"left":8,"width":78},"Modelo":{"top":48,"left":21,"width":82},"Expedicao":{"top":70,"left":5,"width":82},"T007_C007_Id_Vendedor_Interno":{"top":92,"left":8,"width":212},"Empresa":{"top":114,"left":13,"width":82}}'
			);
					
			// novo formulário
			$form = new Formulario('fin008FormMapaVendas');

			// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
			$formArray = array(
				'campos' => array(
					'geral' => array(
						'DataInicio' => array('name' => 'Data_Inicio', 'label'=>'Data Inicial', 'type' => 'data', 'reject'=>true),	
						'DataFim' => array('name' => 'Data_Fim', 'label'=>'Data Final', 'type' => 'data', 'reject'=>true),							
						'Modelo' => array('name' => 'Modelo', 'label' => 'Modelo', 'type' => 'select', 'style'=>'width:90px;', 'select' => 
							array(
								array('title' => 'Diario', 'value' => '1', 'selected'=>'selected'),
								array('title' => 'Mensal', 'value' => '2'), 
							)
						),
						'Expedicao' => array('name' => 'Expedicao', 'label' => 'Expedição', 'type' => 'select', 'style'=>'width:90px;', 'select' => 
							array(
								array('title' => 'Todos', 'value' => '0', 'selected'=>'selected'),
								array('title' => 'Comercial', 'value' => '1'),
								array('title' => 'Balcão', 'value' => '2'), 
							)
						),
						'T007_C007_Id_Vendedor_Interno' => array('name' => 'T007_C007_Id_Vendedor_Interno', 'label' => 'Vendedor', 'type' => 'select', 'style'=>'width:220px;', 
							'select' => gGeraSelect('C007', 'C007_Id', 'C007_Nome', 'WHERE C007_Ativo!="N"', true)
						),
						'Empresa' => array('name' => 'Empresa', 'label' => 'Empresa', 'type' => 'select', 'style'=>'width:90px;', 'select' => 
							array(
								array('title' => 'Atual', 'value' => $g['empresaAtual']),
								array('title' => 'Todas', 'value' => '0'), 
							)
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
					$Data_Inicial = $form->campoValorEnviado('DataInicio');
					$Data_Final = $form->campoValorEnviado('DataFim');
					$Modelo = $form->campoValorEnviado('Modelo');
					$Expedicao = $form->campoValorEnviado('Expedicao');
					$Empresa = $form->campoValorEnviado('Empresa');
					$T007_C007_Id_Vendedor_Interno = $form->campoValorEnviado('T007_C007_Id_Vendedor_Interno');
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							nova_janela('/rel/relatorios/mapaVenda/rel/?ajax=true&Data_Inicial={$Data_Inicial}&Data_Final={$Data_Final}&Modelo={$Modelo}&Expedicao={$Expedicao}&Empresa={$Empresa}&T007_C007_Id_Vendedor_Interno={$T007_C007_Id_Vendedor_Interno}',0,0,900,750,'no','no','no','no','yes','yes', 'Mapa de Vendas');
							//$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
						} </script></div>
EOT;
				}
			}
			
			/**
			* PARTE 4: Gerar o formulário
			*/
			//$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk, "Confirma a não liberação?");
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
			
			/**
			* Botões
			*/
			// Definição dos botões
			$botoesEsq = '';
			$botoesDir = '';
			$botoesDir .= '<button type="submit" id="fin008FormMapaVendasBtn">Imprimir</button>';
			$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
			$g['smarty']->assign('botoesId', $g['divId']);
			$g['smarty']->assign('botoesEsq', $botoesEsq);
			$g['smarty']->assign('botoesDir', $botoesDir);
			$g['smarty']->display('buttons-floatBottom.tpl');
		
		break;

		case 'fin008FormDRE':
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
			$form_tabela = '';
			$form_campo_pk = '';
			// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
			$enviar_btnId = 'fin008FormDREBtn';
			// Form que será executado ao clicar no botão
			$enviar_acaoHref = '/fin/fin008/form/fin008FormDRE/';

			// editor
			$form_editor = array(
				'geral' => '{"DataInicio":{"top":7,"left":21,"width":88,"hidden":0},"DataFim":{"top":30,"left":25,"width":88,"hidden":0},"Empresa":{"top":53,"left":30,"width":232,"hidden":0},"Cliente":{"top":76,"left":41,"width":232,"hidden":0},"CFOP":{"top":99,"left":43,"width":146,"hidden":1}}'
			);
					
			// novo formulário
			$form = new Formulario('fin008FormDRE');

			// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
			$formArray = array(
				'campos' => array(
					'geral' => array(
						'DataInicio' => array('name' => 'Data_Inicio', 'label'=>'Data Inicial', 'type' => 'data', 'reject'=>true),	
						'DataFim' => array('name' => 'Data_Fim', 'label'=>'Data Final', 'type' => 'data', 'reject'=>true),							
						'Empresa' => array('name' => 'Empresa', 'label' => 'Empresa', 'type' => 'texto'),
						'Cliente' => array('name' => 'Cliente', 'label' => 'Cliente', 'type' => 'texto'),
						'CFOP' => array('name' => 'CFOP', 'label' => 'CFOP', 'type' => 'texto'),
						'empresasGrupo' => 	array('name' => 'empresasGrupo', 'label' => 'Vendas grupo',	'type' => 'select', 'restricao' => false, 'select' => 
							array(
								array('title' => 'Somar vendas para empresas do grupo', 'value' => 'S','selected' => true),   
								array('title' => 'Não somar venas para empresas do grupo', 'value' => 'N'),   
							)
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
					$Data_Inicial = $form->campoValorEnviado('DataInicio');
					$Data_Final = $form->campoValorEnviado('DataFim');
					$Empresa = $form->campoValorEnviado('Empresa');
					$Cliente = $form->campoValorEnviado('Cliente');
					$CFOP = $form->campoValorEnviado('CFOP');
					$empresasGrupo = $form->campoValorEnviado('empresasGrupo');
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							nova_janela('/rel/relatorios/dre/rel/?ajax=true&Data_Inicial={$Data_Inicial}&Data_Final={$Data_Final}&Empresa={$Empresa}&Cliente={$Cliente}&CFOP={$CFOP}&empresasGrupo={$empresasGrupo}',0,0,900,600,'no','no','no','no','yes','yes', 'DRE');
							//$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
						} </script></div>
EOT;
				}
			}
			
			/**
			* PARTE 4: Gerar o formulário
			*/
			//$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk, "Confirma a não liberação?");
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
			
			/**
			* Botões
			*/
			// Definição dos botões
			$botoesEsq = '';
			$botoesDir = '';
			$botoesDir .= '<button type="submit" id="fin008FormDREBtn">Imprimir</button>';
			$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
			$g['smarty']->assign('botoesId', $g['divId']);
			$g['smarty']->assign('botoesEsq', $botoesEsq);
			$g['smarty']->assign('botoesDir', $botoesDir);
			$g['smarty']->display('buttons-floatBottom.tpl');
		
		break;

	}
?>
