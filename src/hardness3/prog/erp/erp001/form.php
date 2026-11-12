<?php
namespace hardness;
require_once('prog/erp/erp001/form_func-js.php');
// require_once("bibliotecas/classes/erp001.php");
// $erp001 = new erp001();
/**
 * Andrey
 * 27/04/2012
 */
 
/**Classe
* 
* require_once("bibliotecas/classes/fis009.php");
* $fis009 = new fis009();
*/


/**
 * Ações
 */
switch ($g['r'][3]) {
	
	case 'erp001FormSetCliente':
		$content1 = (isset($r_content1)) ? $r_content1 : '';
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
		$form_tabela = '';
		$form_campo_pk = '';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = '';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/erp/erp001/form/erp001FormSetCliente/';
		
		$pesquisaGridExtra = <<<EOT
			left join D020 on D020_Id=D024_D020_Id 
			left join D018 on D018_Id=D024_D018_Id 
			WHERE D024_Flag_Ativo='S'
			order by D024_Nome_Empresa
EOT;
			$pesquisaGrid = array(
				'D024',																											// tabela
				'D024_Id',																										// chave primaria
				'D024_Id,D024_Nome_Empresa,D024_Nome_Fantasia,D020_Nome_Cidade,D018_UF,D024_Cnpj,D024_DDD_Telefone_1,D024_Telefone_1',	// campos
				$pesquisaGridExtra,																								// extras sql
				'Pesquisa de Cliente',																							// título da janela
				'Código,Cliente,Fantasia,Cidade,UF,CNPJ,DDD,Telefone',															// descrição dos campos
				'erp001ProcuraCliente',																							// callback js
				$g['divId'],																									// divId
				array($content1),																									// parametro extra
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
		/* A linha abaixo era usada para dar nome ao campo e executar o JS comentado a baixo,
		após uma mudança no framework não é mais possível utilizar $uniqid para o
		nome de campos de um form. Por tanto essa parte foi desabilitada e o campo
		foi trocado para nomes fixos -> 'Cliente' => array('name' => 'Cliente'... */
		//$uniqid = uniqid();
		// novo formulário
		$form = new Formulario('erp001FormSetClienteForm');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'Cliente' => array('name' => 'Cliente' , 'label' => 'Cliente', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGrid, 'length' => array(60,120), 'reject' => true),
				)
			)
		);
		$limite = 0;
		$form->definirFormularioArray($formArray);
		
		/* Código atualmente redundante, framework já dá focus no 
		primeiro campo do formulário automaticamente */
// 		echo <<<EOT
// 			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {	
// 				$('#' + '{$uniqid}').focus();
// 			}</script></div>
// EOT;
		
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
		$botoesDir = '';
		//$botoesDir .= '<button type="submit" id="cad050FormLocacaoLocal_btn">Enviar</button>';
		// $botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;
	
	case 'erp001FormEditarAgenda':
			/**
			* PARTE 1: Variavéis envidas para o formulário
			*/
			// Variável indica qual tab do form será exibida
			$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
			// Utilizada em UPDATE, é o ID do registro que está sendo editado
			$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
			
			$D024_Id = (isset($r_D024_Id)) ? $r_D024_Id : false;

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
			$form_tabela = 'D085';
			$form_campo_pk = 'D085_Id';
			// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
			$enviar_btnId = 'cad001FormAgenda_btn';
			// Form que será executado ao clicar no botão
			$enviar_acaoHref = '/erp/erp001/form/erp001FormEditarAgenda/';

			// novo formulário
			$form = new Formulario('erp001FormAgenda');

			// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
			if(!empty($D024_Id)){
				$formArray = array(
					'campos' => array(
						'geral' => array(
							'D085_D024_Id' => array('name' => 'D085_D024_Id','label'=>'', 'type' => 'hidden', 'value' => @$r_D024_Id),
							'D085_C007_Id' => array('name' => 'D085_C007_Id','label'=>'', 'type' => 'hidden','value' => $g['usuarioAtual']),
							'D085_Data' => array('name' => 'D085_Data','label'=>'Data', 'type' => 'data', 'value' => date("d/m/Y"), 'length' => array(10,10)),
							'D085_Data_Retorno' => array('name' => 'D085_Data_Retorno','label'=>'Data Retorno', 'type' => 'data', 'length' => array(10,10)),
							'D085_Mensagem' => array('name' => 'D085_Mensagem','label'=>'Mensagem', 'type' => 'textarea','length' => array(3,60)),
							// 'cad001FormAgenda_btn' => array('name' => 'cad001FormAgenda_btn', 'type'=>'submit','reload'=>false,'value'=>'Incluir','align' => 'right', 'reject'=>true),
						)
					)
				);
			} else {
				$formArray = array(
					'campos' => array(
						'geral' => array(
							'D085_D024_Id' => array('name' => 'D085_D024_Id','label'=>'Cliente', 'type' => 'pesquisa',
								'pesquisa' => array('D024','D024_Id','D024_Id,D024_Nome_Empresa,D020_Nome_Cidade,D018_UF,D024_Cpf,D024_Cnpj','left join D020 on D020_Id=D024_D020_Id left join D018 on D018_Id=D024_D018_Id WHERE D024_Flag_Cliente="S" and D024_Flag_Ativo="S" order by D024_Nome_Empresa','(%1) %2 [%3 - %4]'), 'length' => array(50,30)),
							'D085_C007_Id' => array('name' => 'D085_C007_Id','label'=>'', 'type' => 'hidden','value' => $g['usuarioAtual']),
							'D085_Data' => array('name' => 'D085_Data','label'=>'Data', 'type' => 'data', 'value' => date("d/m/Y"), 'length' => array(10,10)),
							'D085_Data_Retorno' => array('name' => 'D085_Data_Retorno','label'=>'Data Retorno', 'type' => 'data', 'length' => array(10,10)),
							'D085_Mensagem' => array('name' => 'D085_Mensagem','label'=>'Mensagem', 'type' => 'textarea','length' => array(3,60)),
							// 'cad001FormAgenda_btn' => array('name' => 'cad001FormAgenda_btn', 'type'=>'submit','reload'=>false,'value'=>'Incluir','align' => 'right', 'reject'=>true),
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
						// Insert ou Update?
						if (!empty($acaoId)) {
							// Update
							if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
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
								} </script></div>
EOT;
							}
					} else {
						// Insert
						if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
							// Imprime uma mensagem e fecha a janela
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
			$g['smarty']->display('form.tpl');

			/**
			* Botões
			*/
			// Definição dos botões
			$botoesMid = '';
			$botoesEsq = '';
			$botoesDir = '';
			$botoesDir .= '<button type="submit" id="cad001FormAgenda_btn">Enviar</button>';
			$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
			$g['smarty']->assign('botoesId', $g['divId']);
			$g['smarty']->assign('botoesEsq', $botoesEsq);
			$g['smarty']->assign('botoesMid', $botoesMid);
			$g['smarty']->assign('botoesDir', $botoesDir);
			$g['smarty']->display('buttons-floatBottom.tpl');

		break;
		
		case 'erp001FormEditarContato':
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
			$enviar_btnId = 'erp001FormContatosBtnNew';
			// Form que será executado ao clicar no botão
			$enviar_acaoHref = '/erp/erp001/form/erp001FormEditarContato/';
			// Array de definição do formulário
			// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
			$form = new Formulario('formContatos_erp001');
			$formArray = array(
				'campos' => array(
					'geral' => array(
						'D013_Nome_Contato' => array('name' => 'D013_Nome_Contato', 'label'=>'Nome', 'type' => 'text','length' => array(50,60)),
						'D013_E_Mail' => array('name' => 'D013_E_Mail', 'label'=>'E-Mail', 'type' => 'text','length' => array(50,90)),
						'D013_Tipo_Contato' => array('name' => 'D013_Tipo_Contato', 'label'=>'Tipo', 'type' => 'text','length' => array(50,90)),
						'D013_Data_Nascimento' => array('name' => 'D013_Data_Nascimento', 'label'=>'Nascimento', 'type' => 'data','length' => array(20,30)),
						'D013_DDD_Telefone_1' => array('name' => 'D013_DDD_Telefone_1', 'label'=>'Telefone', 'type' => 'text','length' => array(2,2)),
						'D013_Telefone_1' => array('name' => 'D013_Telefone', 'label'=>'', 'type' => 'text','length' => array(20,30), 'align'=>'right'),
						'D013_D024_Id' => array('name' => 'D013_D024_Id', 'value' => @$r_D024_Id, 'type' => 'hidden', 'label' => 'D013_D024_Id', 'length' => array(30,30)),
						//'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true),
					),
				) 
			);

			//Botão Limpar / Novo Registro
			/*if (!empty($acaoId)) {
				$formArray['campos']['geral']['cad001FormContatosBtnNew'] = array('name' => 'cad001FormContatosBtnNew', 'type'=>'submit', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
			}
			$formArray['campos']['geral']['cad001FormContatosBtnCln'] = array('name' => 'cad001FormContatosBtnCln', 'type'=>'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
			//$formArray['campos']['geral']['cad001FormContatosBtnAdd'] = array('name' => 'cad001FormContatosBtnAdd', 'type'=>'button', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
			*/
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

			$botoesMid = '';
			$botoesEsq = '';
			$botoesDir = '';
			$botoesDir .= '<button type="submit" id="erp001FormContatosBtnNew">Enviar</button>';
			$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
			$g['smarty']->assign('botoesId', $g['divId']);
			$g['smarty']->assign('botoesEsq', $botoesEsq);
			$g['smarty']->assign('botoesMid', $botoesMid);
			$g['smarty']->assign('botoesDir', $botoesDir);
			$g['smarty']->display('buttons-floatBottom.tpl');
			// Ações dos botões
			// A ação do botão Enviar é definido dentro do formulário
			break;
}
?>
