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
					
			// novo formulário
			$form = new Formulario('form_fin007_form5');

			// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
			$formArray = array(
				'campos' => array(
					'geral' => array(
						'Data_Inicio' => array('name' => 'Data_Inicio', 'label'=>'Data Inicial', 'type' => 'data', 'reject'=>true),	
						'Data_Fim' => array('name' => 'Data_Fim', 'label'=>'Data Final', 'type' => 'data', 'reject'=>true),	
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
								array('title' => 'Sim', 'value' => 'S'),
								array('title' => 'Não', 'value' => 'N'), 
							)
						),
						'Titulos_Cobranca' => array('name' => 'Titulos_Cobranca', 'label' => 'Incluir títulos cobrança', 'type' => 'select', 'style'=>'width:82px;', 'select' => 
							array(
								array('title' => '...', 'value' => ''),
								array('title' => 'Sim', 'value' => 'S'),
								array('title' => 'Não', 'value' => 'N'), 
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
					$Data_Inicio = $form->campoValorEnviado('Data_Inicio');
					$Data_Fim = $form->campoValorEnviado('Data_Fim');
					$Empresa = $form->campoValorEnviado('Empresa');
					$Saldo_Bancario = $form->campoValorEnviado('Saldo_Bancario');
					$Titulos_Cobranca = $form->campoValorEnviado('Titulos_Cobranca');
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							nova_janela('/hardness3/hardness/relatorios/fluxo_caixa.php?Data_Inicio={$Data_Inicio}&Data_Fim={$Data_Fim}&Empresa={$Empresa}&Saldo_Bancario={$Saldo_Bancario}&Titulos_Cobranca={$Titulos_Cobranca}',0,0,800,600,'no','no','no','no','yes','yes',0);
							$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
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
			$g['smarty']->display('form.tpl');
			
			/**
			* Botões
			*/
			// Definição dos botões
			$botoesEsq = '';
			$botoesDir = '';
			$botoesDir .= '<button type="submit" id="fin007FormFluxoCaixaBtn">Enviar</button>';
			$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
			$g['smarty']->assign('botoesId', $g['divId']);
			$g['smarty']->assign('botoesEsq', $botoesEsq);
			$g['smarty']->assign('botoesDir', $botoesDir);
			$g['smarty']->display('buttons-floatBottom.tpl');			
		break;

		case 'fin009formReferenciasComerciais':
			global $g;
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;
		
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
		$form_tabela = 'D024';
		$form_campo_pk = 'D024_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'fin009formReferenciasComerciais_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/fin/fin009/form/fin009formReferenciasComerciais/';
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D024_Id_Cliente' => array('name' => 'D024_Id_Cliente', 'label'=>'Cliente', 'type' => 'pesquisa','length' => array(50,60),
						'pesquisa' => array('D024','D024_Id','D024_Nome_Empresa, D024_Id',""),
  					),
				)
			)
		);
        $form = new Formulario('fin009formReferenciasComerciais');
		$form->definirFormularioArray($formArray);

		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#D024_Id_Cliente_Pesquisa').bind('keypress', function(e) {
					var D024_Campo_Valor = $('#D024_Id_Cliente').val();
					if (e.keyCode == 13 && $('#D024_Id_Cliente_Pesquisa').val() != '') {
						nova_janela('/hardness3/hardness/clientes/referencias_comerciais.php?D024_Id=' + D024_Campo_Valor + '&Abre_Janela=Nao&Voltar=N',0,0,800,600,'no','no','no','no','yes','yes',0);
						return false;
					} else if(e.keyCode == 13 && $('#D024_Id_Cliente_Pesquisa').val() == ''){
						return false;
					}
				}); 											
			} </script></div>
EOT;
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
				$D024_Id = $form->campoValorEnviado('D024_Id_Cliente');								
				echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							var D024_Id = '{$D024_Id}';							
							nova_janela('/hardness3/hardness/clientes/referencias_comerciais.php?D024_Id=' + D024_Id + '&Abre_Janela=Nao&Voltar=N',0,0,800,600,'no','no','no','no','yes','yes',0);
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
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('form.tpl');

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="fin009formReferenciasComerciais_btn">Enviar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
		break;
	
	}
?>
