<?php
namespace hardness;
//Classes
require_once('prog/est/est020/form_func-js.php');
require_once("bibliotecas/classes/ReformaTributaria.php");
require_once("bibliotecas/classes/EST020.php");
require_once("bibliotecas/classes/VEN001.php");
$EST020 = new EST020();
$VEN001 = new VEN001();
$reformaTributaria = new ReformaTributaria();

switch ($g['r'][3]) {		
	case 'est020FormPrincipal':
		global $g;
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
		$formMultiTab = true;
		if ($formMultiTab) {
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;			
		} else {
			$formId = uniqid();
		}
		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = 'T084';
		$form_campo_pk = 'T084_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est020FormPrincipalBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est020/form/est020FormPrincipal/';
		$form_editor = array(
			'cabecalho'    => '{"T084_Flag_Modalidade_Transporte":{"top":7,"left":123.84375,"width":127,"hidden":0},"T084_Data_Emissao":{"top":7,"left":726.890625,"width":77,"hidden":0},"T084_Hora_Emissao":{"top":7,"left":902.0625,"width":104,"hidden":0},"T084_Flag_Tipo_Servico":{"top":30,"left":93.953125,"width":271,"hidden":0},"T084_Flag_Indicador_IE":{"top":30,"left":609.171875,"width":287,"hidden":0},"T084_Tipo_Conhecimento":{"top":53,"left":104.203125,"width":271,"hidden":0},"T084_D006_Id":{"top":76,"left":118.78125,"width":271,"hidden":0},"T084_D018_Id_Origem":{"top":99,"left":103.015625,"width":127,"hidden":0},"T084_D020_Id_Origem":{"top":122,"left":82.171875,"width":271,"hidden":0},"T084_D018_Id_Destino":{"top":145,"left":101.296875,"width":128,"hidden":0},"T084_D020_Id_Destino":{"top":168,"left":80.453125,"width":271,"hidden":0},"T084_D024_Remetente_Id":{"top":191,"left":101.5625,"width":361,"hidden":0},"T084_D024_Destinatario_Id":{"top":214,"left":95.265625,"width":363,"hidden":0},"T084A_D024_Recebedor_Id":{"top":237,"left":102.171875,"width":383,"hidden":0},"T084_D024_Tomador_Id":{"top":260,"left":110.453125,"width":362,"hidden":0},"T084_Flag_Retira":{"top":283,"left":126.203125,"width":79,"hidden":0},"T084_Informacoes_Adicionais_Retira":{"top":306,"left":7.515625,"height":100,"width":407,"hidden":0}}',						
		);

		// busca o ID do cliente pra carregar os contatos, caso seja edição
		$idCliente = $r_idCliente ? $r_idCliente : '0';

		if ($acaoId>0){
			$sql = "SELECT T084_D024_Destinatario_Id FROM T084 WHERE T084_Id = '{$acaoId}'";
			$result = mysql_query($sql);
			$aD024 = mysql_fetch_assoc($result);		
			$idCliente=$aD024['T084_D024_Destinatario_Id'];
		}

		// endereço de entrega
		$arrayEndereco = gGeraSelect('D148','D148_Id','D148_Nome','WHERE D148_D024_Id='.$idCliente.' ORDER BY D148_Id',true);
		$arrayEndereco[0]['title'] = 'PADRÃO';

		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'cabecalho' => array(										
					'T084_Data_Emissao' => array('name' => 'T084_Data_Emissao', 'label' => 'Data Emissão', 'type' => 'data', 'value'=>date("d/m/Y")),				
					'T084_Hora_Emissao' => array('name' => 'T084_Hora_Emissao', 'label' => 'Hora Emissão', 'type' => 'tempo', 'value'=>date("H:i")),				
					'T084_Flag_Modalidade_Transporte' => array('name' => 'T084_Flag_Modalidade_Transporte', 'label' => 'Modal', 'type' => 'select', 'select' =>
							array(
								array('title' => 'Rodoviário', 'value' => '01'),							
							)
					),						
					'T084_Tipo_Conhecimento' => array('name' => 'T084_Tipo_Conhecimento', 'label' => 'Finalidade', 'type' => 'select', 'select' =>
							array(
								array('title' => 'Normal', 'value' => '0'),
								array('title' => 'Complemento de Valores', 'value' => '1'),
								// array('title' => 'Anulação', 'value' => '2'),
							)
					),
					'T084_Flag_Tipo_Servico' => array('name' => 'T084_Flag_Tipo_Servico', 'label' => 'Tipo Serviço', 'type' => 'select', 'select' =>
							array(
								array('title' => 'Normal', 'value' => '0'),
								// array('title' => 'Subcontratação', 'value' => '1'),
								// array('title' => 'Redespacho', 'value' => '2'),
								// array('title' => 'Redespacho Intermediário', 'value' => '3'),
								// array('title' => 'Serviço Vinculado a Multimodal', 'value' => '4'),
							)
					),					
					'T084_Flag_Retira' => array('name' => 'T084_Flag_Retira', 'label' => 'Retira', 'type' => 'select', 'select' =>
							array(
								array('title' => 'Sim', 'value' => 'S'),
								array('title' => 'Não', 'value' => 'N')
							)
					),
					'T084_Informacoes_Adicionais_Retira' => array('name' => 'T084_Informacoes_Adicionais_Retira', 'label' => 'Informações Adicionais Retira', 'type' => 'textarea'),					
					'T084_D018_Id_Origem' => array('name' => 'T084_D018_Id_Origem', 'label' => 'UF Origem', 'type' => 'select', 'extra' => "onchange = \"alteraEstado(this, '{$g['divId']}','T084_D018_Id_Origem', 'T084_D020_Id_Origem')\"", 'select' =>
						gGeraSelect('D018', 'D018_Id', 'D018_UF', 'WHERE D018_Codigo_IBGE > 0 ORDER BY D018_UF')
					),
					'T084_D020_Id_Origem' => array('name' => 'T084_D020_Id_Origem', 'label' => 'Cidade Origem', 'type' => 'select', 'select' =>
						gGeraSelect('D020', 'D020_Id', 'D020_Nome_Cidade')
					),
					'T084_D018_Id_Destino' => array('name' => 'T084_D018_Id_Destino', 'label' => 'UF Destino', 'type' => 'select', 'extra' => "onchange = \"alteraEstado(this, '{$g['divId']}','T084_D018_Id_Destino', 'T084_D020_Id_Destino')\"", 'select' =>
						gGeraSelect('D018', 'D018_Id', 'D018_UF', 'WHERE D018_Codigo_IBGE > 0 ORDER BY D018_UF')
					),
					'T084_D020_Id_Destino' => array('name' => 'T084_D020_Id_Destino', 'label' => 'Cidade Destino', 'type' => 'select', 'select' =>
						gGeraSelect('D020', 'D020_Id', 'D020_Nome_Cidade')
					),
					'T084_D024_Tomador_Id' => array('name' => 'T084_D024_Tomador_Id', 'label' => 'Tomador', 'type' => 'pesquisa', 
						'pesquisa' => array('D024','D024_Id','D024_Codigo,D024_Nome_Empresa')
					),
					'T084_D024_Remetente_Id' => array('name' => 'T084_D024_Remetente_Id', 'label' => 'Remetente', 'type' => 'pesquisa', 
						'pesquisa' => array('D024','D024_Id','D024_Codigo,D024_Nome_Empresa')
					),
					'T084_D024_Destinatario_Id' => array('name' => 'T084_D024_Destinatario_Id', 'label' => 'Destinatário', 'type' => 'pesquisa',
						'pesquisa' => array('D024','D024_Id','D024_Codigo,D024_Nome_Empresa'), 'extra' => "onchange=\"carregaRecebedor(this, '{$g['divId']}')\"",
					),
					'T084A_D024_Recebedor_Id' => array('name' => 'T084A_D024_Recebedor_Id', 'label' => 'Recebedor', 'type' => 'select',
					'selectRefresh' => array('D148','D148_Id','D148_Nome','WHERE D148_D024_Id='.$idCliente.' ORDER BY D148_Id',true),
						'select' => $arrayEndereco,
					),
					'T084_Flag_Indicador_IE' => array('name' => 'T084_Flag_Indicador_IE', 'label' => 'Indicador Inscrição Estatual Tomador:', 'type' => 'select', 'select' =>
							array(
								array('title' => 'Contribuinte ICMS', 'value' => '1'),
								array('title' => 'Contribuinte isento de inscrição', 'value' => '2'),
								array('title' => 'Não Contribuinte', 'value' => '9'),								
							)
					),
					'T084_D006_Id' => array('name' => 'T084_D006_Id', 'label' => 'C.F.O.P.', 'type' => 'select', 'select' =>
						gGeraSelect('D006', 'D006_Id', 'D006_Codigo_CFOP,D006_Descricao', 'WHERE D006_Flag_Inativo != "S" ORDER BY D006_Codigo_CFOP,D006_Descricao', true)						
					),					
					'T084_C004_Id' => array('name' => 'T084_C004_Id', 'label' => 'T084_Cnpj_Transportadora', 'value'=>$g['empresaAtual'], 'type' => 'hidden'),
					'T084_C007_Id' => array('name' => 'T084_C007_Id', 'label' => 'T084_Cnpj_Transportadora', 'value'=>$g['usuarioAtual'], 'type' => 'hidden'),
					'T084_Flag_Entrada_Saida' => array('name' => 'T084_Flag_Entrada_Saida', 'label' => 'T084_Flag_Entrada_Saida', 'value'=> 'S', 'type' => 'hidden'),//Nesse formulário sempre será uma saida
					'T084_Flag_Travar' => array('name' => 'T084_Flag_Travar', 'label' => 'T084_Flag_Travar', 'type' => 'hidden'),//Nesse formulário sempre será uma saida
				),									  
			)
		);
        $form = new Formulario('form-est020FormPrincipal');
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
				if (!empty($acaoId)) {
					// Update
					if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $r_acaoId, $form)) {
						// Fecha a janela, recarrega o parent (grid) e emite uma mensagem de alerta

						$retorno = $EST020->incluirCTe($form, $acaoId);

						if($retorno !== true) {
							echo gGeraAlertMsg('Operação ERRO', $retorno);
						} else {
							echo gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!');
						}

						echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                                // O FW atualiza a linha do grid automaticamente, somente recarregar se não for grid
								if ($('#{$g['divIdParent']}').find('table.grid').length === 0) {
									divRefresh('{$g['divIdParent']}', true);
								}
                                divRefresh($('.contentCabecalhoLayout').parent().attr('id'));
                            } </script></div>
EOT;
					}
				} else {
					// Insert
					$retorno = $EST020->incluirCTe($form,$acaoId);
					$acaoId = $g['mysqlLastId'];
					
					if ($retorno !== true) {
						echo gGeraAlertMsg('Operação ERRO', $retorno);
						
						echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {						
							$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								divRefresh('{$g['divIdParent']}', true);
							} </script></div>
EOT;			
					} else {						
						echo gGeraAlertMsg('Operação OK', 'Registro Incuido com Sucesso!');
						echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
							setTimeout(function() {
								abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}', unique(), '', 'Editar Conhecimento - ID: ' + '{$acaoId}', '/est/est020/content/est020ContentIncluirConhecimento/', '&tab=geral&acaoId=' + encodeURIComponent('{$acaoId}'), [900,550]);
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

	case 'est020FormTecnico':
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

		$form_tabela = 'T084';
		$form_campo_pk = 'T084_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)

		$enviar_btnId = array('est020FormBtnEnviar', 'est020FormBtnLimpar');
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est020/form/est020FormTecnico/';
		
		//form editor
		$form_editor = array('geral' => '{"T084_Chave_Acesso_Cte":{"top":7,"left":21.296875,"width":296,"hidden":0},"T084_Numero_Recibo_Cte":{"top":30,"left":15.140625,"width":296,"hidden":0},"T084_Numero_Conhecimento":{"top":53,"left":19.9375,"width":296,"hidden":0},"T084_Serie":{"top":76,"left":66.03125,"width":296,"hidden":0},"est020FormBtnLimpar":{"top":97,"left":256,"width":null,"hidden":0},"est020FormBtnEnviar":{"top":97,"left":336,"width":null,"hidden":0},"T084_TX_Resposta_Codigo_Interno":{"top":7,"left":451.890625,"width":271,"hidden":0}}');

		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(					
					'T084_Chave_Acesso_Cte' => array('name' => 'T084_Chave_Acesso_Cte', 'label' => 'Chave Acesso', 'type' => 'text'),
					'T084_Numero_Recibo_Cte' => array('name' => 'T084_Numero_Recibo_Cte', 'label' => 'Número Recibo', 'type' => 'text'),
					'T084_Numero_Conhecimento' => array('name' => 'T084_Numero_Conhecimento', 'label' => 'Número CTE-e', 'type' => 'text'),
					'T084_Serie' => array('name' => 'T084_Serie', 'label' => 'Serie', 'type' => 'text'),				
					'T084_TX_Resposta_Codigo_Interno' 	=> array('name' => 'T084_TX_Resposta_Codigo_Interno', 'label' => 'Flag Transmissão', 'type' => 'select',
						'select' => array(
							array('value' => '0', 'title' => '...', 'selected' => true),
							array('value' => '1', 'title' => 'Iniciando Processo'),
							array('value' => '2', 'title' => 'Gerando XML'),
							array('value' => '3', 'title' => 'Assinando XML'),
							array('value' => '4', 'title' => 'Transmitindo CT-e para a SEFAZ'),
							array('value' => '5', 'title' => 'XML Gerado! Assinando'),
							array('value' => '6', 'title' => 'XML Assinado! Transmitindo'),
							array('value' => '7', 'title' => 'Aguardando retorno da Sefaz'),	
							array('value' => '8', 'title' => 'CT-e Enviado e Autorizado'),													
							array('value' => '100', 'title' => 'Certificado Digital não encontrado.'),
							array('value' => '101', 'title' => 'Erro na hora de comunicar com o SEFAZ'),
							array('value' => '102', 'title' => 'Erro Gerando XML!'),
							array('value' => '103', 'title' => 'Erro ao preparar o XML'),
							array('value' => '104', 'title' => 'Erro ao transmitir CT-e'),
							array('value' => '105', 'title' => 'Erro Interno: Tentando gravar o protocolo'),								
							array('value' => '202', 'title' => 'Erro SEFAZ'),													
						)
					),
				)
			)
		);
        $form = new Formulario('est020FormTecnico');

        // Botões dentro do Formulário
		$formArray['campos']['geral']['est020FormBtnEnviar'] = array('name' => 'est020FormBtnEnviar', 'type' => 'submit', 'reload' => false, 'value' => 'Enviar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['geral']['est020FormBtnLimpar'] = array('name' => 'est020FormBtnLimpar', 'type' => 'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);

		$form->definirFormularioArray($formArray);		

		$T084 = mysql_query("SELECT T084_Numero_Protocolo_Cte FROM T084 WHERE T084_Id = '{$r_acaoId}'");
		$mT084 = mysql_fetch_assoc($T084);

		$travar = false;
		if($mT084['T084_Numero_Protocolo_Cte'] != '') {
			$travar = true;
		}

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
					if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $r_acaoId, $form,false,false)) {
						// Fecha a janela, recarrega o parent (grid) e emite uma mensagem de alerta
						$alert = gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										{$alert}
									}, 150);
  								});
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
								divRefresh('{$g["divIdParent"]}', true);
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
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk,false,false,false,$travar);		
		/**
		 * PARTE 5: Exibir o formulário
		 */
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');
	break;

	case 'est020FormIncluirNfeCte':
		global $g;
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($r_acaoId)) ? $r_acaoId : false;
		$T084_Id = (isset($r_T084_Id)) ? $r_T084_Id : false;
		$T085_C004_Origem_Id = (isset($r_T085_C004_Origem_Id)) ? $r_T085_C004_Origem_Id : '0';
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
		$form_tabela = 'T085';
		$form_campo_pk = 'T085_Id';	
		$form_parent = 'T084_Id';
        $form_parent_valor = $T084_Id;		
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est020FormIncluirNfeCte_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est020/form/est020FormIncluirNfeCte/';
        		
        //form editor
        $form_editor = array(
            'geral' => '{"Origem":{"top":7,"left":85,"width":207,"hidden":0},"T085_T007_Id":{"top":30,"left":98,"width":185,"hidden":0},"T085_Data_Prevista_Entrega":{"top":53,"left":23,"width":200,"hidden":0},"T085_Chave_Acesso_Nfe":{"top":76,"left":16,"width":328,"hidden":0}}',
        );
		
		$form = new Formulario('est020FormIncluirNfeCte');
		
		$select = gGeraSelect('C004', 'C004_Id', 'C004_Nome_empresa', "WHERE C004_Id != '{$g['empresaAtual']}'",false, $T085_C004_Origem_Id);
		$outros = array('title' => 'Outros', 'value' => '0');		
		$readonly = false;
		if($T085_C004_Origem_Id  == '0') {
			$readonly = true;			
			$outros = array('title' => 'Outros', 'value' => '0','selected' => true);		
		}		
		$select[] = $outros;

		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(	
					'T085_Flag_Tipo_Documento' => array('name' => 'T085_Flag_Tipo_Documento', 'label' => 'Tipo de Documento', 'type' => 'hidden', 'value' => '2'),	//Tipo NF-e
					'Origem' => array('name' => 'Origem', 'label' => 'Origem', 'type' => 'select', 'select' => $select, 'reject' => true),																
					'T085_T007_Id' => array('name' => 'T085_T007_Id', 'label' => 'Nota','type' => 'pesquisa',  'extra' => "onchange=\"carregaDadosNfeCte(this, '{$g['divId']}');\"", 
						'pesquisa' => array('T007','T007_Id','T007_Numero_Nota_Fiscal', "WHERE T007_C004_Id = $T085_C004_Origem_Id"), 'readonly' => $readonly
					),
					'T085_Chave_Acesso_Nfe' => array('name' => 'T085_Chave_Acesso_Nfe', 'label' => 'Chave de Acesso Nfe', 'type' => 'text'),																			
					'T085_Data_Prevista_Entrega' => array('name' => 'T085_Data_Prevista_Entrega', 'label' => 'Previsão de Entrega', 'type' => 'data'),																								
					'T085_C004_Origem_Id' => array('name' => 'T085_C004_Origem_Id', 'type' => 'hidden', 'align' => 'right'),					
					'T085_T084_Id' => array('name' => 'T085_T084_Id', 'type' => 'hidden', 'align' => 'right', 'value' => $T084_Id),					
				),
			)
		);
		
		$form->definirFormularioArray($formArray);		

		echo <<<EOT
		    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		    								
				$('#{$g['divId']} #Origem').change(function() {	
					origem = $('#{$g['divId']} #Origem').val();					
					divSetarAcaoId('{$g['divId']}', origem, 'T085_C004_Origem_Id');				
					divRefresh('{$g['divId']}');					
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
				// Insert ou Update?

				$Origem = $form->campoValorEnviado('Origem');
				$form->campoSetarValor('T085_C004_Origem_Id', $Origem,true);				

				$chaveNFe = $form->campoValorEnviado('T085_Chave_Acesso_Nfe');				
				$chaveNFe = trim($chaveNFe);
				$T085 = mysql_query("SELECT T085_Id FROM T085 WHERE T085_Chave_Acesso_Nfe = '{$chaveNFe}' AND T085_Id != T085_Id");

				if(mysql_num_rows($T085) > 0) {
					$alert = gGeraAlertMsg('Erro', "Esta chave já está vinculada a outro CT-e", false);
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
				} else if (!empty($r_acaoId)) {					
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
								divRefresh('{$g["divIdParent"]}', true);
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
		$gerado = gGeraCamposForm($form, $tab, $r_acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk, false, $form_parent, $form_parent_valor);
		/**
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');
	
		$botoesDir = '<button type="submit" id="est020FormIncluirNfeCte_btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;
	
	case 'est020FormIncluirOutroDocumento':
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
		$form_tabela = 'T085';
		$form_campo_pk = 'T085_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est020FormIncluirOutroDocumento_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est020/form/est020FormIncluirOutroDocumento/';
        
        //form editor
        $form_editor = array(
            'geral' => '{"T085_Valor_Total_Nota":{"top":76,"left":69,"width":248,"hidden":0},"T085_Numero_Nota_Fiscal":{"top":7,"left":55,"width":248,"hidden":0},"T085_Descricao_Documento":{"top":30,"left":30,"width":440,"hidden":0},"T085_Tipo_Documento_Originario":{"top":53,"left":5,"width":255,"hidden":0},"T085_Data_Emissao":{"top":99,"left":85,"width":248,"hidden":0},"T085_Data_Prevista_Entrega":{"top":122,"left":55,"width":248,"hidden":0}}',
        );
		
		$form = new Formulario('est020FormIncluirOutroDocumento');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T085_Flag_Tipo_Documento' => array('name' => 'T085_Flag_Tipo_Documento', 'label' => 'Tipo de Documento', 'type' => 'hidden', 'value' => '3'),	//Tipo Outros
					'T085_Tipo_Documento_Originario' => array('name' => 'T085_Tipo_Documento_Originario', 'label' => 'Tipo de Documento Originário', 'type' => 'select', 'align'=>'right', 'style'=>'float:right', 'select' => 
						array(							
							array('title' => 'Declaração', 'value' => '00'),
							array('title' => 'Dutoviário', 'value' => '10'),
							array('title' => 'CF e SAT', 'value' => '59'),
							array('title' => 'NFCe', 'value' => '65'),
							array('title' => 'Outros', 'value' => '99'),
						)
					),		
					'T085_Descricao_Documento' => array('name' => 'T085_Descricao_Documento', 'label' => 'Descrição do documento', 'type' => 'text'),										
					'T085_Numero_Nota_Fiscal' => array('name' => 'T085_Numero_Nota_Fiscal', 'label' => 'Número documento', 'type' => 'text'),	
					'T085_Valor_Total_Nota' => array('name' => 'T085_Valor_Total_Nota', 'label' => 'Valor documento', 'type' => 'moeda'),										
					'T085_Data_Emissao' => array('name' => 'T085_Data_Emissao', 'label' => 'Data Emissão', 'type' => 'data'),
					'T085_Data_Prevista_Entrega' => array('name' => 'T085_Data_Prevista_Entrega', 'label' => 'Previsão de Entrega', 'type' => 'data'),
					'T085_T084_Id' => array('name' => 'T085_T084_Id', 'type' => 'hidden', 'align' => 'right', 'value' => $r_T084_Id),
				)
			)
		);
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
		$gerado = gGeraCamposForm($form, $tab, $r_acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk, false, $form_parent, $form_parent_valor);

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
		$botoesDir .= '<button type="submit" id="est020FormIncluirOutroDocumento_btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;	

	case 'est020FormCancelarCte':
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
		$form_tabela = 'T084';
		$form_campo_pk = 'T084_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est020FormCancelarCte_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est020/form/est020FormCancelarCte/';
        
        //form editor
        $form_editor = array(
            'geral' => '{"T084_Observacao_Cancelamento":{"top":7,"left":7.625,"height":155,"width":464,"hidden":0}}',
        );
		
		$form = new Formulario('est020FormCancelarCte');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(					
					'T084_Observacao_Cancelamento' => array('name' => 'T084_Observacao_Cancelamento', 'label' => 'Observação do Cancelamento', 'type' => 'textarea', 
						'value' => "Cte cancelado por: {$g['C007']['C007_Nome']}\nData e hora : " . date("d/m/Y H:m:s") . "\nMotivo do Cancelamento : "),
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
				$retorno = $EST020->cancelarCTe($form, $acaoId);
				if ($retorno !== true) {
					echo gGeraAlertMsg('Erro', $retorno);	
				} else {
					echo gGeraAlertMsg('Operação OK', "CT-e Cancelado!");	
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
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk,false,false,false,false);
		/**
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');
	
		$botoesDir = '<button type="submit" id="est020FormCancelarCte_btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;	

	case 'est020FormInfoCarga':
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

		$form_tabela = 'T084';
		$form_campo_pk = 'T084_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est020FormInfoCargaBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est020/form/est020FormInfoCarga/';

		//form editor
		$form_editor = array('geral' => '{"T084_Valor_Total_Carga":{"top":7,"left":102.5,"width":248,"hidden":0},"T084_Produto_Predominante":{"top":30,"left":76.3125,"width":248,"hidden":0},"T084_Produto_Predominante_Outras_Caracteristicas":{"top":53,"left":21.125,"width":248,"hidden":0},"T084_Valor_Averbacao":{"top":76,"left":60.046875,"width":248,"hidden":0},"est020FormInfoCargaBtn":{"top":97,"left":400,"width":null,"hidden":0}}');

		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(					
					'T084_Valor_Total_Carga' => array('name' => 'T084_Valor_Total_Carga', 'label' => 'Valor Total Carga', 'type' => 'moeda'),							
					'T084_Produto_Predominante' => array('name' => 'T084_Produto_Predominante', 'label' => 'Produto Predominante', 'type' => 'text'),							
					'T084_Produto_Predominante_Outras_Caracteristicas' => array('name' => 'T084_Produto_Predominante_Outras_Caracteristicas', 'label' => 'Outras Características do Produto', 'type' => 'text'),							
					'T084_Valor_Averbacao' => array('name' => 'T084_Valor_Averbacao', 'label' => 'Valor da Carga Averbação', 'type' => 'moeda'),							
				)
			)
		);
		$formArray['campos']['geral']['est020FormInfoCargaBtn'] = array('name' => 'est020FormInfoCargaBtn', 'type' => 'button', 'reload' => false, 'value' => 'Salvar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
        $form = new Formulario('est020FormInfoCarga');

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
								divRefresh('{$g["divIdParent"]}', true);
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
		**/
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo  $g['smarty']->fetch('buttons-floatBottom.tpl');
	break;

	case 'est020FormIncluirQuantidadeCarga':
		global $g;
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;		
		$T084_Id = (isset($r_T084_Id)) ? $r_T084_Id : false;// CT-e		
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
		$form_tabela = 'T305';
		$form_campo_pk = 'T305_Id';
		$form_parent = 'T084_Id';
        $form_parent_valor = $T084_Id;		
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est020FormQuantidadeCargaBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est020/form/est020FormIncluirQuantidadeCarga/';
		$form_editor = array(
			'geral' => '{"T305_Codigo_Unidade":{"top":7,"left":29.03125,"width":175,"hidden":0},"T305_Tipo_Medida":{"top":30,"left":30.796875,"width":168,"hidden":0},"T305_Quantidade":{"top":53,"left":50.4375,"width":168,"hidden":0}}'
		);

		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T305_Codigo_Unidade' => array('name' => 'T305_Codigo_Unidade', 'label' => 'Código Unidade', 'type' => 'select', 'align'=>'right', 'style'=>'float:right', 'select' => 
						array(							
							array('title' => 'M3', 'value' => '00'),
							array('title' => 'KG', 'value' => '01'),
							array('title' => 'TON', 'value' => '02'),
							array('title' => 'UNIDADE', 'value' => '03'),
							array('title' => 'LITROS', 'value' => '04'),
							array('title' => 'MMBT', 'value' => '05'),
						)
					),		
					'T305_Tipo_Medida' => array('name' => 'T305_Tipo_Medida', 'label' => 'Tipo da Medida', 'type' => 'text'),
					'T305_Quantidade' => array('name' => 'T305_Quantidade', 'label' => 'Quantidade', 'type' => 'moeda',  'casasDecimais' => 4),
					'T305_T084_Id' => array('name' => 'T305_T084_Id', 'type' => 'hidden', 'align' => 'right', 'value' => $T084_Id),
				)
			)
		);
        $form = new Formulario('est020FormIncluirQuantidadeCarga');
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
		$gerado = gGeraCamposForm($form, $tab, $r_acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk, false, $form_parent, $form_parent_valor);

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
		$botoesDir .= '<button type="submit" id="est020FormQuantidadeCargaBtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');

	break;	

	case 'est020FormValores':
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

		$form_tabela = 'T084';
		$form_campo_pk = 'T084_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est020FormValoresBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est020/form/est020FormValores/';

		//form editor
		$form_editor = array('geral' => '{"T084_Valor_Conhecimento":{"top":6.946044921875,"left":13.934661865234375,"width":72.18181799999999,"hidden":0},"T084_Valor_Base_ICMS":{"top":6.946044921875,"left":197.2301025390625,"width":40.18181800000001,"hidden":0},"T084A_Situacao_Tributaria_CBS_IBS":{"top":6.946044921875,"left":378.3664855957031,"width":192,"hidden":0},"T084A_Aliquota_CBS":{"top":6.960235595703125,"left":683.2670593261719,"width":41.18181800000001,"hidden":0},"T084A_Aliquota_IBS_UF":{"top":6.960235595703125,"left":799.4885559082031,"width":40.18181800000001,"hidden":0},"T084A_Aliquota_IBS_Municipal":{"top":6.960235595703125,"left":932.0454406738281,"width":40.18181800000001,"hidden":0},"T084_Valor_Receber":{"top":30,"left":22.8125,"width":72.18181799999999,"hidden":0},"T084_Aliquota_ICMS":{"top":30,"left":213.52273559570312,"width":40.18181800000001,"hidden":0},"T084A_Classificacao_Tributaria_CBS_IBS":{"top":30,"left":308.5511169433594,"width":190,"hidden":0},"T084A_Percentual_Reducao_CBS":{"top":30,"left":658.8210144042969,"width":40.18181800000001,"hidden":0},"T084A_Percentual_Reducao_IBS_UF":{"top":30,"left":775.0426330566406,"width":40.18181800000001,"hidden":0},"T084A_Percentual_Reducao_IBS_Municipal":{"top":30,"left":907.6135559082031,"width":39.18181800000001,"hidden":0},"T084_Percentual_Reducao_ICMS":{"top":52.98297119140625,"left":189.07672119140625,"width":40.18181800000001,"hidden":0},"T084A_Valor_Base_CBS_IBS":{"top":52.98297119140625,"left":374.0624694824219,"width":184.18181800000002,"hidden":0},"T084A_Valor_CBS":{"top":52.98297119140625,"left":678.3664855957031,"width":40.18181800000001,"hidden":0},"T084A_Valor_IBS_UF":{"top":52.98297119140625,"left":794.5880432128906,"width":40.18181800000001,"hidden":0},"T084A_Valor_IBS_Municipal":{"top":52.98297119140625,"left":927.1448059082031,"width":40.18181800000001,"hidden":0},"T084_Valor_ICMS":{"top":75.98013305664062,"left":208.60797119140625,"width":40.18181800000001,"hidden":0},"T084A_Valor_Total_Reforma":{"top":75.99432373046875,"left":357.5426330566406,"width":184.18181800000002,"hidden":0},"T084A_Percentual_Diferimento_CBS":{"top":75.98013305664062,"left":663.9204406738281,"width":40.18181800000001,"hidden":0},"T084A_Percentual_Diferimento_IBS_UF":{"top":75.98013305664062,"left":780.1419982910156,"width":40.18181800000001,"hidden":0},"T084A_Percentual_Diferimento_IBS_Municipal":{"top":75.98013305664062,"left":912.6988830566406,"width":40.18181800000001,"hidden":0},"est020FormValoresBtn":{"top":120,"left":575.9942932128906,"width":null,"hidden":0},"T084A_Valor_Diferimento_CBS":{"top":98.96307373046875,"left":659.0198669433594,"width":40.18181800000001,"hidden":0},"T084A_Valor_Diferimento_IBS_UF":{"top":98.96307373046875,"left":775.2414855957031,"width":40.18181800000001,"hidden":0},"T084A_Valor_Diferimento_IBS_Municipal":{"top":98.96307373046875,"left":907.7982482910156,"width":40.18181800000001,"hidden":0}}');
		
		$T084 = mysql_query("SELECT T084_Tipo_Conhecimento FROM T084 WHERE T084_Id = '{$acaoId}'");
		$mT084 = mysql_fetch_array($T084);

		$readonly = $mT084['T084_Tipo_Conhecimento'] ==  '1' ? false : true;		

		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(					
					'T084_Valor_Conhecimento' => array('name' => 'T084_Valor_Conhecimento', 'label' => 'Total Prestação R$', 'type' => 'moeda'),							
					'T084_Valor_Receber' => array('name' => 'T084_Valor_Receber', 'label' => 'Valor Receber R$', 'type' => 'moeda'),												
					'T084_Valor_Base_ICMS' => array('name' => 'T084_Valor_Base_ICMS', 'label' => 'Base ICMS', 'type' => 'moeda','readonly' => $readonly),												
					'T084_Aliquota_ICMS' => array('name' => 'T084_Aliquota_ICMS', 'label' => '% ICMS', 'type' => 'moeda','readonly' => $readonly),												
					'T084_Valor_ICMS' => array('name' => 'T084_Valor_ICMS', 'label' => 'ICMS R$', 'type' => 'moeda','readonly' => $readonly),												
					'T084_Percentual_Reducao_ICMS' => array('name' => 'T084_Percentual_Reducao_ICMS', 'label' => '% Red. ICMS', 'type' => 'moeda','readonly' => 'readonly'),
				)
			)
		);

		/* [REFORMA TRIBUTÁRIA CTe] - Gabriel Cegantini 08/12/2025
		================================================================================== */
		// Callback pra pegar a classificação tributária da CST da reforma
		$callbackReformaAoAlterarCst = <<<EOT
			jsRecarregarCampo('T084A_Classificacao_Tributaria_CBS_IBS', array('cst' => \$array['campoAtual']));
		EOT;
		// Callback pra trocar o select
		$callbackReformaPreencherSelectClassificacaoTributaria = <<<EOT
			require_once("bibliotecas/classes/ReformaTributaria.php");
			\$reformaTributaria = new ReformaTributaria();
			// Se o usuário trocar a CST vamos receber ela na hora do form
			if (isset(\$array['cst'])) {
				\$cst = \$array['cst'];
			// Se é a primeira vez que o usuário abriu o form de alteração precisamos descobrir qual é ela
			} else {
				\$T084 = mysql_query("SELECT T084A_Situacao_Tributaria_CBS_IBS 
									FROM T084
								LEFT JOIN T084A ON T084A_T084_Id = T084_Id
									WHERE T084_Id = '{\$array['acaoId']}'");
				\$mT084 = mysql_fetch_assoc(\$T084);
				\$cst = \$mT084['T084A_Situacao_Tributaria_CBS_IBS'];
			}
			return \$reformaTributaria->getCclassTribFormSelect(\$cst);
		EOT;

		$formArray['campos']['geral']['T084A_Situacao_Tributaria_CBS_IBS'] 				= array('name' => 'T084A_Situacao_Tributaria_CBS_IBS', 'label' => 'CST CBS/IBS', 'type' => 'select', 'select' => $reformaTributaria->getCstsFormSelect(), 'callbackAoAlterar' => $callbackReformaAoAlterarCst, 'disabled' => 'S');
		$formArray['campos']['geral']['T084A_Classificacao_Tributaria_CBS_IBS'] 		= array('name' => 'T084A_Classificacao_Tributaria_CBS_IBS', 'label' => 'Classificação Trib. CBS/IBS', 'type' => 'select', 'select' => array(array('title' => '...', 'select' => '')), 'callbackPreencherSelect' => $callbackReformaPreencherSelectClassificacaoTributaria, 'disabled' => 'S');
		$formArray['campos']['geral']['T084A_Valor_Base_CBS_IBS'] 						= array('name' => 'T084A_Valor_Base_CBS_IBS', 'label' => 'Base CBS/IBS', 'type' => 'moeda','disabled' => 'S');
		$formArray['campos']['geral']['T084A_Aliquota_CBS'] 							= array('name' => 'T084A_Aliquota_CBS', 'label' => '% CBS', 'type' => 'moeda','disabled' => 'S');
		$formArray['campos']['geral']['T084A_Percentual_Reducao_CBS'] 					= array('name' => 'T084A_Percentual_Reducao_CBS', 'label' => '% Red. CBS', 'type' => 'moeda','disabled' => 'S');
		$formArray['campos']['geral']['T084A_Percentual_Diferimento_CBS'] 				= array('name' => 'T084A_Percentual_Diferimento_CBS', 'label' => '% Dif. CBS', 'type' => 'moeda','disabled' => 'S');
		$formArray['campos']['geral']['T084A_Valor_Diferimento_CBS'] 					= array('name' => 'T084A_Valor_Diferimento_CBS', 'label' => 'Dif. CBS R$', 'type' => 'moeda','disabled' => 'S');
		$formArray['campos']['geral']['T084A_Valor_CBS'] 								= array('name' => 'T084A_Valor_CBS', 'label' => 'CBS R$', 'type' => 'moeda','disabled' => 'S');
		$formArray['campos']['geral']['T084A_Aliquota_IBS_UF'] 							= array('name' => 'T084A_Aliquota_IBS_UF', 'label' => '% IBS UF', 'type' => 'moeda','disabled' => 'S');
		$formArray['campos']['geral']['T084A_Percentual_Reducao_IBS_UF'] 				= array('name' => 'T084A_Percentual_Reducao_IBS_UF', 'label' => '% Red. IBS UF', 'type' => 'moeda','disabled' => 'S');
		$formArray['campos']['geral']['T084A_Percentual_Diferimento_IBS_UF'] 			= array('name' => 'T084A_Percentual_Diferimento_IBS_UF', 'label' => '% Dif. IBS UF', 'type' => 'moeda','disabled' => 'S');
		$formArray['campos']['geral']['T084A_Valor_Diferimento_IBS_UF'] 				= array('name' => 'T084A_Valor_Diferimento_IBS_UF', 'label' => 'Dif. IBS UF R$', 'type' => 'moeda','disabled' => 'S');
		$formArray['campos']['geral']['T084A_Valor_IBS_UF'] 							= array('name' => 'T084A_Valor_IBS_UF', 'label' => 'IBS UF R$', 'type' => 'moeda','disabled' => 'S');
		$formArray['campos']['geral']['T084A_Aliquota_IBS_Municipal'] 					= array('name' => 'T084A_Aliquota_IBS_Municipal', 'label' => '% IBS Mun.', 'type' => 'moeda','disabled' => 'S');
		$formArray['campos']['geral']['T084A_Percentual_Reducao_IBS_Municipal'] 		= array('name' => 'T084A_Percentual_Reducao_IBS_Municipal', 'label' => '% Red. IBS Mun.', 'type' => 'moeda','disabled' => 'S');
		$formArray['campos']['geral']['T084A_Percentual_Diferimento_IBS_Municipal'] 	= array('name' => 'T084A_Percentual_Diferimento_IBS_Municipal', 'label' => '% Dif. IBS Mun.', 'type' => 'moeda','disabled' => 'S');
		$formArray['campos']['geral']['T084A_Valor_Diferimento_IBS_Municipal'] 			= array('name' => 'T084A_Valor_Diferimento_IBS_Municipal', 'label' => 'Dif. IBS Mun. R$', 'type' => 'moeda','disabled' => 'S');
		$formArray['campos']['geral']['T084A_Valor_IBS_Municipal'] 						= array('name' => 'T084A_Valor_IBS_Municipal', 'label' => 'IBS Mun. R$', 'type' => 'moeda','disabled' => 'S');
		$formArray['campos']['geral']['T084A_Valor_Total_Reforma'] 						= array('name' => 'T084A_Valor_Total_Reforma', 'label' => 'Total Reforma R$', 'type' => 'moeda', 'disabled' => 'S');
		/* =============================================================================== */

		$formArray['campos']['geral']['est020FormValoresBtn'] = array('name' => 'est020FormValoresBtn', 'type' => 'button', 'reload' => false, 'value' => 'Salvar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
        $form = new Formulario('est020FormValores');

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
								divRefresh('{$g["divIdParent"]}', true);
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
		**/
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo  $g['smarty']->fetch('buttons-floatBottom.tpl');
	break;

	case 'est020FormIncluirComponente':
		global $g;
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;		
		$T084_Id = (isset($r_T084_Id)) ? $r_T084_Id : false;// CT-e	
		
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
		$form_tabela = 'T304';
		$form_campo_pk = 'T304_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est020FormComponenteBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est020/form/est020FormIncluirComponente/';
		$form_editor = array(
			'geral' => '{"T304_Nome_Componente":{"top":7,"left":11.390625,"width":200,"hidden":0},"T304_Valor_Componente":{"top":30,"left":15.578125,"width":199,"hidden":0}}'
		);

		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(					
					'T304_Nome_Componente' => array('name' => 'T304_Nome_Componente', 'label' => 'Nome Componente', 'type' => 'text'),
					'T304_Valor_Componente' => array('name' => 'T304_Valor_Componente', 'label' => 'Valor Componente', 'type' => 'moeda'),
					'T304_T084_Id' => array('name' => 'T304_T084_Id', 'type' => 'hidden', 'align' => 'right', 'value' => $T084_Id),
				)
			)
		);
        $form = new Formulario('est020FormIncluirComponente');
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

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="est020FormComponenteBtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');

	break;	

	case 'est020FormAdicionais':
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

		$form_tabela = 'T084';
		$form_campo_pk = 'T084_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est020FormAdicionaisBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est020/form/est020FormAdicionais/';

		//form editor
		$form_editor = array('geral' => '{"T084_Observacoes_Gerais":{"top":7,"left":6.25,"height":108,"width":479,"hidden":0},"est020FormAdicionaisBtn":{"top":120,"left":544,"width":null,"hidden":0}}');

		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(					
					'T084_Observacoes_Gerais' => array('name' => 'T084_Observacoes_Gerais', 'label' => 'Observações Gerais', 'type' => 'textarea'),							
				)
			)
		);
		$formArray['campos']['geral']['est020FormAdicionaisBtn'] = array('name' => 'est020FormAdicionaisBtn', 'type' => 'button', 'reload' => false, 'value' => 'Salvar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
        $form = new Formulario('est020FormAdicionais');

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
								divRefresh('{$g["divIdParent"]}', true);
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
		**/
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo  $g['smarty']->fetch('buttons-floatBottom.tpl');
	break;
	case 'est020FormIncluirObservacaoContribuinte':
		global $g;
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;		
		$T084_Id = (isset($r_T084_Id)) ? $r_T084_Id : false;// CT-e	
		
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
		$form_tabela = 'T306';
		$form_campo_pk = 'T306_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est020FormObservacaoContribuinteBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est020/form/est020FormIncluirObservacaoContribuinte/';
		$form_editor = array(
			'geral' => '{"T306_Identificador":{"top":7,"left":29.21875,"width":392,"hidden":0},"T306_Conteudo":{"top":30,"left":43.140625,"height":132,"width":392,"hidden":0}}'
		);

		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(					
					'T306_Identificador' => array('name' => 'T306_Identificador', 'label' => 'Identificador', 'type' => 'text','uppercase' => false),
					'T306_Conteudo' => array('name' => 'T306_Conteudo', 'label' => 'Conteúdo', 'type' => 'textarea', 'uppercase' => false),
					'T306_T084_Id' => array('name' => 'T306_T084_Id', 'type' => 'hidden', 'align' => 'right', 'value' => $T084_Id),
				)
			)
		);
        $form = new Formulario('est020FormIncluirObservacaoContribuinte');
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

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="est020FormObservacaoContribuinteBtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');

	break;	

	case 'est020FormIncluirObservacaoFisco':
		global $g;
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;		
		$T084_Id = (isset($r_T084_Id)) ? $r_T084_Id : false;// CT-e	
		
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
		$form_tabela = 'T307';
		$form_campo_pk = 'T307_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est020FormObservacaoFiscoBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est020/form/est020FormIncluirObservacaoFisco/';
		$form_editor = array(
			'geral' => '{"T307_Identificador":{"top":7,"left":29.21875,"width":296,"hidden":0},"T307_Conteudo":{"top":30,"left":43.140625,"width":456,"hidden":0}}'
		);

		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(					
					'T307_Identificador' => array('name' => 'T307_Identificador', 'label' => 'Identificador', 'type' => 'text'),
					'T307_Conteudo' => array('name' => 'T307_Conteudo', 'label' => 'Conteúdo', 'type' => 'text'),
					'T307_T084_Id' => array('name' => 'T307_T084_Id', 'type' => 'hidden', 'align' => 'right', 'value' => $T084_Id),
				)
			)
		);
        $form = new Formulario('est020FormIncluirObservacaoFisco');
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

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="est020FormObservacaoFiscoBtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');

	break;	

	case 'est020FormPagamentos':
		global $g;
		$acaoId = (isset($r_acaoId)) ? $r_acaoId : false;
		$T084_Id = (isset($r_T084_Id)) ? $r_T084_Id : false;

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
		$form_tabela = 'T084';
		$form_campo_pk = 'T084_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est020FormPagamentoBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est020/form/est020FormPagamentos/';

	     //form editor
        $form_editor = array(
            'geral' => '{"T084_Pgto_Parcelas":{"top":7,"left":16.203125,"width":191,"hidden":0},"T084_Pgto_Primeiro_Vcto_Tipo":{"top":30,"left":17.390625,"width":111,"hidden":0},"T084_Pgto_Primeiro_Vcto_Valor":{"top":30,"left":176,"width":72,"hidden":0},"T084_Pgto_Dias_Vcto_Tipo":{"top":53,"left":7.03125,"width":111,"hidden":0},"T084_D027_Id":{"top":76,"left":15.6875,"width":191,"hidden":0},"est020FormPagamentoBtn":{"top":74,"left":320,"width":null,"hidden":0},"T084_Pgto_Primeiro_Vcto_Valor_Data":{"top":99,"left":112,"width":72,"hidden":0},"T084_Pgto_Dias_Vcto_Valor":{"top":53,"left":176,"width":72,"hidden":0}}',
        );

		// novo formulário
		$form = new Formulario('est020FormPagamentos');

		$dadosTabela = array('tabela' => $form_tabela, 'campos' => 'T084_Valor_Conhecimento as valorTotal, T084_Pgto_Parcelas as pgtoParcelas', 'where' => $form_campo_pk . " = '{$T084_Id}'" );
		$select = $VEN001->populaComboPgtoParcelas($dadosTabela);
		$CNPJ_Empresa_Corrente=substr($g['C004']['C004_Cnpj'], 0, -8);

		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T084_Pgto_Parcelas' => array('name' => 'T084_Pgto_Parcelas', 'label' => 'Parcelas', 'type' => 'select', 'select' => $select, 'uppercase' => false),
					'T084_Pgto_Primeiro_Vcto_Tipo' => array('name' => 'T084_Pgto_Primeiro_Vcto_Tipo', 'label'=>'1º Venc.', 'type' => 'select','select' =>
						array(
							array('title' => 'Dias', 'value' => 'DIAS'),
							array('title' => 'Data', 'value' => 'DATA'),
						)
					),
					'T084_Pgto_Primeiro_Vcto_Valor' => array('name' => 'T084_Pgto_Primeiro_Vcto_Valor', 'type' => 'text','label' => '', 'align' => 'right'),					
					'T084_Pgto_Dias_Vcto_Tipo' => array('name' => 'T084_Pgto_Dias_Vcto_Tipo', 'label'=>'Dias Venc.', 'type' => 'select','select' =>
						array(
							array('title' => 'Intervalo', 'value' => 'INTERVALO'),
							array('title' => 'Dia Fixo', 'value' => 'DIA FIXO'),
						)
					),
					'T084_Pgto_Dias_Vcto_Valor' => array('name' => 'T084_Pgto_Dias_Vcto_Valor', 'label' => '', 'type' => 'text', 'length' => array(3,6), 'align' => 'right'),
					'T084_D027_Id' => array('name' => 'D027_Id','label'=>'Portador', 'type' => 'select', 'select' =>
						gGeraSelect('D027', 'D027_Id', 'concat(D027_Portador," ",if(D027_Acrescimo_IPV>0,concat("+",D027_Acrescimo_IPV,"%"),"")) as D027_Portador','LEFT JOIN D007 ON D007_Id = D027_D007_Id LEFT JOIN C004 ON C004_Id = D007_C004_Id WHERE (D007_Flag_Layout="1" and D007_Flag_Ativo="S" AND C004_Cnpj LIKE "%'.$CNPJ_Empresa_Corrente.'%") OR ifnull(D027_D007_Id,0) = 0 order by D027_Portador', true, $mT084['T084_D027_Id'])
					),														
					'est020FormPagamentoBtn' => array('name' => 'est020FormPagamentoBtn', 'type'=>'submit','reload'=>false,'value'=>'Gerar Parcelas', 'reject'=>true),										
					'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => $r_divReload, 'reject' => true),
					'T084_Id' => array('name' => 'T084_Id', 'label' => '', 'type' => 'hidden', 'value' => $r_T084_Id, 'reject' => true),
				)
			)
		);

		$form->definirFormularioArray($formArray);

		echo <<<EOT
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
				// Update dos campos do T007
				if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $r_acaoId, $form)) {

					//ARRAY UTILIZADO NA FUNÇÃO ATUALIZAR PARCELAS
					$dadosTabela = array('tabela' => $form_tabela, 
										 'tabelaId' => 	$T084_Id,
										 'campos' => 'T084_Pgto_Primeiro_Vcto_Tipo as primeiroVencimentoTipo,
	                                         T084_Pgto_Primeiro_Vcto_Valor as primeiroVencimentoNum,
	                                         T084_Pgto_Dias_Vcto_Tipo as diasVctoTipo,
	                                         T084_Pgto_Dias_Vcto_Valor as valorIntervaloDiaFixo,
	                                         T084_D027_Id as D027_Id,	                                         	                                         	                                         
	                                         T084_Pgto_Parcelas as Total_Parcelas,
											 T084_Valor_Conhecimento as valorTotal', 
                                         'where' => $form_campo_pk."='{$T084_Id}'", 
                                         'tabelaPgto' => 'T307', 
                                         'camposPgto' => 'T307_Prazos_Dias as prazoDias,T307_D027_Id as D027Pagamento,T307_Numero_Parcela as numeroParcela', 
                                         'wherePgto' => "T307_T084_Id='{$T084_Id}'");
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
		echo <<<EOT
			<div style="border: 1px solid silver; margin: 10px 15px 0 450px; padding: 5px; height: 110px; border-radius:5px; color: var(--BGVermelho); overflow: auto;">
				<span style="font-weight: bold; color: #000;">Informação de Pagamento</span><br>
			</div>
EOT;
	break;

	case 'est020FormIncluirDocumentoAnterior':
		global $g;
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;		
		$T084_Id = (isset($r_T084_Id)) ? $r_T084_Id : false;// CT-e	
		
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
		$form_tabela = 'T308';
		$form_campo_pk = 'T308_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est020FormDocumentoAnteriorBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est020/form/est020FormIncluirDocumentoAnterior/';
		$form_editor = array(
			'geral' => '{"T308_Chave_Acesso":{"top":30,"left":29.21875,"width":306,"hidden":0}}'
		);

		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(					
					'T308_Chave_Acesso' => array('name' => 'T308_Chave_Acesso', 'label' => 'Chave de Acesso', 'type' => 'text'),					
					'T308_T084_Id' => array('name' => 'T308_T084_Id', 'type' => 'hidden', 'align' => 'right', 'value' => $T084_Id),
				)
			)
		);
        $form = new Formulario('est020FormIncluirObservacaoFisco');
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

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="est020FormDocumentoAnteriorBtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');

	break;

	case 'est020FormSelecionarTipoDocumento':
		global $g;	
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		// $tipoDocumento = isset($r_tipoDocumento) ? $r_tipoDocumento : "K";

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

		//form editor
      
        $form_editor = array(
			'geral' => '{"tipoDocumento":{"top":7,"left":8,"width":271,"hidden":0}}'
		);
		
		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = '0000';
		$form_campo_pk = '0000_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est020FormSelecionarTipoDocumento_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est020/form/est020FormSelecionarTipoDocumento/';
		// novo formulário
		$form = new Formulario('est020FormSelecionarTipoDocumento');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'tipoDocumento' => array('name' => 'tipoDocumento','label'=>'Tipo Documento', 'type' => 'select', 'reject' => true, 'select' => array(
							array('title' => '...', 'value' => '', 'selected' => true),
							// array('title' => 'NF', 'value' => '1'),
							array('title' => 'NF-e', 'value' => '2'),
							array('title' => 'Outros', 'value' => '3'),
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

		if($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if($formCodigo == 2) {
				$tipoDocumento	  = $form->campoValorEnviado('tipoDocumento');
				
				//NF-e = 2
				if($tipoDocumento == '2') {
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
								console.log("Chamou o incluir documento NF-e");
								setTimeout(function() {
									abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}', unique(), '', 'Incluir Documento - NF-e', '/est/est020/content/est020ContentIncluirNfeCte/', '&tab=geral' + '&T084_Id=' + encodeURIComponent('{$r_T084_Id}') + '&T085_C004_Origem_Id=' + encodeURIComponent('0'), [630,250]);
								}, 150);
							});
							$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
						} </script></div>
					EOT;
				//Outros = 3
				} elseif($tipoDocumento == '3') {
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
								console.log("Chamou o incluir documento NF-e");
								setTimeout(function() {
									abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}', unique(), '', 'Incluir Documento - Outros', '/est/est020/content/est020ContentIncluirOutroDocumento/', '&tab=geral'  + '&T084_Id=' + encodeURIComponent('{$r_T084_Id}'), [630,250]);
								}, 150);
							});
							$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
						} </script></div>
					EOT;
				} else {
					echo gGeraAlertMsg('Erro', 'Selecione uma das opções NF-e ou Outros');
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


		$botoesDir = '<button type="submit" id="est020FormSelecionarTipoDocumento_btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;
}
?>
